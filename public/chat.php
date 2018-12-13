<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');

$site = $_SERVER['HTTP_HOST'];

if(!empty($_GET['exitlink'])){
	unset($_SESSION['sex']);
	unset($_SESSION['want']);
	header("Location: https://$site/pages/chat.php");
	die;
}

header_remove("Pragma");
header_remove("Expires");
header_remove("Cache-Control");

$time = time();
$sid = session_id();
$online = $cache->get('online');

if($online === FALSE){
	$all = 0; $kun = 0; $chan = 0;
	$query = $db->prepare("SELECT * FROM `chat_sessions` WHERE `ping` > UNIX_TIMESTAMP()-90");
	$query->execute();
	while($query->fetch()){
		$all++;
		if($row['sex'] == 1){
			$kun++;
		}
		if($row['sex'] == 2){
			$chan++;
		}
	}
	$online = json_encode([$all, $kun, $chan]);
	$cache->set('online', $online, 30);
}

switch(@$_POST['do']){
	default: die;
	case 'typing': $cache->set($sid.'typing', $time, 60); break;

	case 'ping':
		$query = $db->prepare("UPDATE `chat_sessions` SET `ping` = :time WHERE `sess` = :sess");
		$query->bindParam(':time', $time);
		$query->bindParam(':sess', $sid);
		$query->execute();
		$cache->set("$sid.ping", $time, 60);
		echo $online;
	break;

	case 'close':
		$query = $db->prepare("UPDATE `chat_talks` SET `status` = '1', `end` = :time WHERE (`one` = :sess OR `two` =:sess) AND `status` = '0'");
		$query->bindParam(':sess', $sid);
		$query->bindParam(':time', $time);
		$query->execute();
		setcookie('status', 2, 0, "/");
	break;
	
	case 'stop':
		// Завершаем беседу
		$query = $db->prepare("UPDATE `chat_talks` SET `status` = '1' WHERE (`one` = :sess OR `two` =:sess) AND `status` = '0'");
		$query->bindParam(':sess', $sid);
		$query->execute();

		// Ставим флаг -> собеседник занят
		$query = $db->prepare("UPDATE `chat_sessions` SET `status` = '1' WHERE `sess` = :sess");
		$query->bindParam(':sess', $sid);
		$query->execute();
	break;
	
	case 'ban':
		$query = $db->prepare("SELECT * FROM `chat_talks` WHERE (`one` = :sess OR `two` = :sess) AND `status` = '1' AND `idt` = :idt");
		$query->bindParam(':sess', $sid);
		$query->bindParam(':idt', $_SESSION["idt"]);
		$query->execute();
		if($query->rowCount() != 1) die; // Не нашли ;(
		$row = $query->fetch();

		if($row['one'] != $sid) $j = $row['one'];
		if($row['two'] != $sid) $j = $row['two'];

		$cache->set('ban'.$sid.$j, '1', 3600);
	break;

	case 'add':
		if(strlen(trim(strip_tags($_POST['mes']))) == 0 || mb_strlen($_POST['mes'], 'UTF-8') > 1000) die;

		$query = $db->prepare("SELECT * FROM `chat_talks` WHERE (`one` = :sess OR `two` = :sess) AND `status` != '1' AND `idt` = :idt");
		$query->bindParam(':sess', $sid);
		$query->bindParam(':idt', $_SESSION["idt"]);
		$query->execute();
		if($query->rowCount() != 1) die('ошибка');
		$row = $query->fetch();
		if(round(microtime(true) * 1000) > $cache->get($sid.'message') || empty($cache->get($sid.'message'))){
			$cache->set($sid.'message', round(microtime(true) * 1000)+500, 600);
			$_POST['mes'] = strip_tags(trim($_POST['mes']));
			$query = $db->prepare("INSERT INTO `chat_messages` (`idt`, `session`, `message`, `time`) VALUES ( :idt, :session, :message, :time) ");
			$query->bindParam(':idt', $_SESSION['idt']);
			$query->bindParam(':session', $sid);
			$query->bindParam(':message', $_POST['mes']);
			$query->bindParam(':time', $time);
			$query->execute();
			echo $_POST['mes'];

		} else echo 'SPAM'; // rewrite
	break;

	case 'get':
		$query = $db->prepare("SELECT * FROM `chat_talks` WHERE (`one` = :sess OR `two` = :sess) AND `status` != '1' AND `idt` = :idt");
		$query->bindParam(':sess', $sid);
		$query->bindParam(':idt', $_SESSION["idt"]);
		$query->execute();
		if($query->rowCount() != 1){	// Собеседник отключился (rewrite)
			
			die; 
		} 
		$row = $query->fetch();

		if($row['one'] != $sid) $j = $row['one'];
		if($row['two'] != $sid) $j = $row['two'];

		$query = $db->prepare("SELECT * FROM `chat_messages` WHERE `idt` = :idt AND `send` != '1' AND `session` != :session");
		$query->bindParam(':idt', $_SESSION["idt"]);
		$query->bindParam(':session', $sid);
		$query->execute();

		if($query->rowCount() > 0){
			$row = $query->fetch();
			echo $row['message'];
			$query = $db->prepare("UPDATE `chat_messages` SET `send` = '1' WHERE `id` = :id"); // как только отдали сообщение - ставим флаг
			$query->bindParam(':id', $row['id']);											  // чтобы повторно не выводить его
			$query->execute();																  // скорее всего это лишнее действие, но пока оставлю так

			$query = $db->prepare("DELETE FROM `chat_messages` WHERE `send` = '1' AND `id` = :id");  // мы не читаем переписку пользователей
			$query->bindParam(':id', $row['id']);													// сразу как доставили сообщение - удаляем его
			$query->execute();
		}
	break;

	case 'register': 
		$sex    = (int)$_POST['sex'];
		$search = $_POST['m'] + $_POST['w'];
		if($search > 3 || $search < 0){
			 $search = 3;
		}
		if(intval($_POST['an']) == 1){
			$cache->set('an.'.$sid, 1, 72000);
		}else{
			$cache->delete('an.'.$sid);
		}
		$query = $db->prepare("SELECT * FROM `chat_sessions` WHERE `sess` = :sess");
		$query->bindParam(':sess', $sid);
		$query->execute();
		if($query->rowCount() == 0){
			$query = $db->prepare("INSERT INTO `chat_sessions` (`sess`, `ip`, `sex`, `search`, `enter`) VALUE (:sess, :ip, :sex, :search, :enter)");
			$query->bindParam(':sess', $sid);
			$query->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
			$query->bindParam(':sex', $sex);
			$query->bindParam(':search', $search);
			$query->bindParam(':enter', $time);
			$query->execute();
		}
		if($query->rowCount() == 1){
			$query = $db->prepare("UPDATE `chat_sessions` SET `sex` = :sex, `search` = :search, `enter` = :enter WHERE `sess`=:sess");
			$query->bindParam(':sess', $sid);
			$query->bindParam(':sex', $sex);
			$query->bindParam(':search', $search);
			$query->bindParam(':enter', $time);
			$query->execute();
		}
		$_SESSION["sex"]  = $sex;
		$_SESSION["want"] = $search;
		header("Location: https://$site/pages/chat.php");
	break;

	case 'search':
		$query = $db->prepare("SELECT * FROM `chat_talks` WHERE (`one` = :sess OR `two` = :sess) AND `status` != '1'");
		$query->bindParam(':sess', $sid);
		$query->execute();
		if($query->rowCount() == 0){
			$query_update = $db->prepare("UPDATE `chat_sessions` SET `status` = '0' WHERE `sess` = :sess");
			$query_update->bindParam(':sess', $sid);
			$query_update->execute();
			
			$timeout = $time-30;
			$sql = "SELECT * FROM `chat_sessions` WHERE `status` = '0' AND `sess` != :sess AND `ping` > :time"; // default want 3
			if(!empty($_SESSION["want"] == 2)){
				$sql .= " AND `sex` = :want AND `search` = :sex";
			}
			$query_select = $db->prepare($sql);
			$query_select->bindParam(':sess', $sid);
			if(!empty($_SESSION["want"] == 2)){
				$query_select->bindParam(':want', $_SESSION['want']);
			}
			$query_select->bindParam(':sex', $_SESSION['sex']);
			$query_select->bindParam(':time', $timeout);
			$query_select->execute();
			while($row = $query_select->fetch()){
				// Проверка на бан.
				if($cache->get('ban'.$sid.$row['sess']) == '1' || $cache->get('ban'.$row['sess'].$sid) == '1'){
					continue;
				}
				// Возможно уже нашел собеседника?
				$query_check = $db->prepare("SELECT * FROM `chat_sessions` WHERE `sess` = :sess AND `status` = '0'");
				$query_check->bindParam(':sess', $row['sess']);
				$query_check->execute();
				if($query_check->rowCount() != 1){
					continue;
				}
				$room = genRandStr();
				$query_insert = $db->prepare("INSERT INTO `chat_talks` (`one`, `two`, `room`, `start`) VALUES (:one, :two, :room, :time) ");
				$query_insert->bindParam(':one', $sid);
				$query_insert->bindParam(':two', $row['sess']);
				$query_insert->bindParam(':room', $room);
				$query_insert->bindParam(':time', $time);
				$query_insert->execute();

				$query_update = $db->prepare("UPDATE `chat_sessions` SET `status` = '1' WHERE `sess` = :one");
				$query_update->bindParam(':one', $sid);
				$query_update->execute();

				$query_update = $db->prepare("UPDATE `chat_sessions` SET `status` = '1' WHERE `sess` = :two");
				$query_update->bindParam(':two', $row['sess']);
				$query_update->execute();
				
				break; // Как нашли, заканчиваем цикл
			}
			die;
		}
		$row = $query->fetch();
		$_SESSION['idt'] = $row['idt'];
		//if($_COOKIE['status'] == 1){ rewrite
		//	die("Вы можете продолжить беседу<br/>");
		//}
		if($row['one'] != $sid) $j = $row['one'];
		if($row['two'] != $sid) $j = $row['two'];

		if($cache->get('an.'.$j) == 1){ // Не показываем пол собеседника
			die("Собеседник найден! Общайтесь!<br/>");
		}	
		$query = $db->prepare("SELECT * FROM `chat_sessions` WHERE `sess` = :sess");
		$query->bindParam(':sess', $j);
		$query->execute();
		$row = $query->fetch();
			
		if($row['sex'] == 1){
			$sex = 'Найден кун!';
		}else{
			$sex = 'Найдена тян!';
		}
		die ("$sex Общайтесь!<br/>");
	break;
}
