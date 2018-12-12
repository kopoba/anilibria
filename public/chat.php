<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');

$site = $_SERVER['HTTP_HOST'];

if(!isset($_SESSION)){
	session_start();
}

if(!empty($_GET['exitlink'])){
	unset($_SESSION["sex"]);
	unset($_SESSION["want"]);
	header("Location: https://$site/pages/chat.php");
	die;
}

header_remove("Pragma");
header_remove("Expires");
header_remove("Cache-Control");

$time = time();
$time_online = $time-90;
$online = $cache->get('online');
$kun = $cache->get('kun');
$chan = $cache->get('chan');

$sid = session_id();

if($online === FALSE || $kun === FALSE || $chan === FALSE){
	$r3 = mt_rand(30,90);
	$query_select = $db->prepare("SELECT * FROM `chat_sessions` WHERE `ping` > :time");
	$query_select->bindParam(':time', $time_online);
	$query_select->execute();
	$online = $query_select->rowCount();
	$query_select = $db->prepare("SELECT * FROM `chat_sessions` WHERE `ping` > :time AND `sex` = '1'");
	$query_select->bindParam(':time', $time_online);
	$query_select->execute();
	$kun = $query_select->rowCount();
	$query_select = $db->prepare("SELECT * FROM `chat_sessions` WHERE `ping` > :time AND `sex` = '2'");
	$query_select->bindParam(':time', $time_online);
	$query_select->execute();
	$chan = $query_select->rowCount();
	$cache->set('online', $online, $r3);
	$cache->set('kun', $kun, $r3);
	$cache->set('chan', $chan, $r3);
}

$online = json_encode([$online, $kun, $chan]);

if(@$_COOKIE['online'] != $online){
	setcookie('online', $online, 0, "/");
}

unset($time_online);
unset($query_select);

switch(@$_POST['do']){
	default: die;

	case 'add':
		if(strlen(trim(strip_tags($_POST['mes']))) == 0 || mb_strlen($_POST['mes'], 'UTF-8') > 1000) die;

		$query_select = $db->prepare("SELECT * FROM `chat_talks` WHERE (`one` = :sess OR `two` = :sess) AND `status` != '1' AND `idt` = :idt");
		$query_select->bindParam(':sess', $sid);
		$query_select->bindParam(':idt', $_SESSION["idt"]);
		$query_select->execute();
		if($query_select->rowCount() != 1) die('ошибка');
		$row = $query_select->fetch();

		if(round(microtime(true) * 1000) > $cache->get($sid.'message') || empty($cache->get($sid.'message'))){

			$cache->set($sid.'message', round(microtime(true) * 1000)+500, 600);

			$query_insert = $db->prepare("INSERT INTO `chat_messages` (`idt`, `session`, `message`, `time`) VALUES ( :idt, :session, :message, :time) ");
			$query_insert->bindParam(':idt', $_SESSION["idt"]);
			$query_insert->bindParam(':session', $sid);
			if($_POST['mes'] == 'deanon_video_chat_secret_message'){
				$_POST['mes'] = "Аноним приглашает вас в <a href='https://kamorka-desu.ru/video/?id=$row[room]' target='_blank'> видео чат</a>.";
				$query_insert->bindParam(':message', $_POST['mes']);
				echo "пригласил(а) собеседника в <a href='https://kamorka-desu.ru/video/?id=$row[room]' target='_blank'> видео чат</a>.";
			} else {
				$o = strip_tags(trim($_POST['mes']));
				$query_insert->bindParam(':message', $o);
				echo strip_tags(trim($_POST['mes']));
			}
			$query_insert->bindParam(':time', $time);
			$query_insert->execute();

		} else echo 'SPAM';
	break;

	case 'typing':
		$cache->set($sid.'typing', $time, 60);
	break;

	case 'ban':
		$query_select = $db->prepare("SELECT * FROM `chat_talks` WHERE (`one` = :sess OR `two` = :sess) AND `status` = '1' AND `idt` = :idt");
		$query_select->bindParam(':sess', $sid);
		$query_select->bindParam(':idt', $_SESSION["idt"]);
		$query_select->execute();
		if($query_select->rowCount() != 1) die; // Не нашли ;(
		$row = $query_select->fetch();

		if($row['one'] != $sid) $j = $row['one'];
		if($row['two'] != $sid) $j = $row['two'];

		$cache->set('ban'.$sid.$j, '1', 3600);
	break;

	case 'get':
		$query_select = $db->prepare("SELECT * FROM `chat_talks` WHERE (`one` = :sess OR `two` = :sess) AND `status` != '1' AND `idt` = :idt");
		$query_select->bindParam(':sess', $sid);
		$query_select->bindParam(':idt', $_SESSION["idt"]);
		$query_select->execute();
		if($query_select->rowCount() != 1 && $_COOKIE['status'] != 2){	setcookie('status', 2, 0, "/"); die; } // Собеседник отключился
		$row = $query_select->fetch();

		if($row['one'] != $sid) $j = $row['one'];
		if($row['two'] != $sid) $j = $row['two'];

			if($cache->get($j.'ping') > $time-20 && @$_COOKIE['ping'] != 1)
				setcookie('ping', 1, 0, "/");

			if($cache->get($j.'ping') < $time-20 && @$_COOKIE['ping'] != 0)	
				setcookie('ping', 0, 0, "/");	

			if($cache->get($j.'typing') > $time-2)
				setcookie('typing', 1, 0, "/");
			elseif(@$_COOKIE['typing'] !=0)
				setcookie('typing', 0, 0, "/");

		$query_select = $db->prepare("SELECT * FROM `chat_messages` WHERE `idt` = :idt AND `send` != '1' AND `session` != :session");
		$query_select->bindParam(':idt', $_SESSION["idt"]);
		$query_select->bindParam(':session', $sid);
		$query_select->execute();

		if($query_select->rowCount() > 0){
			$row = $query_select->fetch();
			echo $row['message'];
			$query_update = $db->prepare("UPDATE `chat_messages` SET `send` = '1' WHERE `id` = :id"); // как только отдали сообщение - ставим флаг
			$query_update->bindParam(':id', $row['id']);											  // чтобы повторно не выводить его
			$query_update->execute();																  // скорее всего это лишнее действие, но пока оставлю так

			$query_delete = $db->prepare("DELETE FROM `chat_messages` WHERE `send` = '1' AND `id` = :id");  // мы не читаем переписку пользователей
			$query_delete->bindParam(':id', $row['id']);													// сразу как доставили сообщение - удаляем его
			$query_delete->execute();
		}
	break;

	case 'register': 
		$sex    = (int)$_POST['sex'];
		$search = $_POST['m'] + $_POST['w'];
		if($search > 3 || $search < 0) $search = 3;
		if(intval($_POST['an']) == 1) $cache->set('an.'.$sid, 1, 72000);
			else $cache->delete('an.'.$sid);

		$query_select = $db->prepare("SELECT * FROM `chat_sessions` WHERE `sess` = :sess");
		$query_select->bindParam(':sess', $sid);
		$query_select->execute();
		if($query_select->rowCount() == 0){
		$query_insert = $db->prepare("INSERT INTO `chat_sessions` (`sess`, `ip`, `sex`, `search`, `enter`) VALUE (:sess, :ip, :sex, :search, :enter)");
		$query_insert->bindParam(':sess', $sid);
		$query_insert->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
		$query_insert->bindParam(':sex', $sex);
		$query_insert->bindParam(':search', $search);
		$query_insert->bindParam(':enter', $time);
		$query_insert->execute();
		}

		if($query_select->rowCount() == 1){
		$query_update = $db->prepare("UPDATE `chat_sessions` SET `sex` = :sex, `search` = :search, `enter` = :enter WHERE `sess`=:sess");
		$query_update->bindParam(':sess', $sid);
		$query_update->bindParam(':sex', $sex);
		$query_update->bindParam(':search', $search);
		$query_update->bindParam(':enter', $time);
		$query_update->execute();
		}

		$_SESSION["sex"]  = $sex;
		$_SESSION["want"] = $search;
		header("Location: https://$site/pages/chat.php");
	break;

	case 'ping':
		$query_update = $db->prepare("UPDATE `chat_sessions` SET `ping` = :time WHERE `sess` = :sess");
		$query_update->bindParam(':time', $time);
		$query_update->bindParam(':sess', $sid);
		$query_update->execute();
		$cache->set($sid.'ping', $time, 60);
	break;

	case 'search':
		$query_select = $db->prepare("SELECT * FROM `chat_talks` WHERE (`one` = :sess OR `two` = :sess) AND `status` != '1'");
		$query_select->bindParam(':sess', $sid);
		$query_select->execute();
		if($query_select->rowCount() == 1){
		$row = $query_select->fetch();
		$_SESSION["idt"]   = $row["idt"];
		if(($_COOKIE["status"] != 1) & ($_COOKIE["status"] != 2)){

		if($row['one'] != $sid) $j = $row['one'];
		if($row['two'] != $sid) $j = $row['two'];

		if($cache->get('an.'.$j) != 1){ // Показываем или не показываем пол
		$select_sex = $db->prepare("SELECT * FROM `chat_sessions` WHERE `sess` = :sess");
		$select_sex->bindParam(':sess', $j);
		$select_sex->execute();
		$row_sex = $select_sex->fetch();

		if($row_sex['sex'] == 1) $isex = 'Найден кун!';
		if($row_sex['sex'] == 2) $isex = 'Найдена тян!';

		echo $isex." Общайтесь!<br/>";
		}else
			echo "Собеседник найден! Общайтесь!<br/>";

		setcookie('status', 1, 0, "/");
		}else 
			echo "Вы можете продолжить беседу<br/>";

		} else {
		setcookie('status', 0, 0, "/");
		$query_update = $db->prepare("UPDATE `chat_sessions` SET `status` = '0' WHERE `sess` = :sess");
		$query_update->bindParam(':sess', $sid);
		$query_update->execute();

			// Узнаем в какой мы базе
			$query_base_check = $db->prepare("SELECT * FROM `chat_talks` WHERE (`one` = :sess OR `two` = :sess) AND `status` = '1'");
			$query_base_check->bindParam(':sess', $sid);
			$query_base_check->execute();
				if($query_base_check->rowCount() >= 1){
					$query_base = $db->prepare("SELECT SUM(`start`), SUM(`end`) FROM `chat_talks` WHERE (`one` = :sess OR `two` = :sess) AND `status` = '1' ORDER BY `idt` DESC LIMIT 5");
					$query_base->bindParam(':sess', $sid);
					$query_base->execute();
					$row_base = $query_base->fetch();
					$qwe = ($row_base["SUM(`end`)"] - $row_base["SUM(`start`)"])/5;
					if( $qwe >= 180) $base_one = 'good'; else $base_one = 'bad';
				}

		// default want 3
		$query_text = "SELECT * FROM `chat_sessions` WHERE (`sex` = '1' OR `sex` = '2') AND (`search` = :s OR `search` = '3') AND `status` = '0' AND `sess` != :sess AND `ping` > :time";
		
		if($_SESSION["want"] == 1)
			$query_text = "SELECT * FROM `chat_sessions` WHERE `sex` = '1' AND `search` = :s AND `status` = '0' AND `sess` != :sess AND `ping` > :time";

		if($_SESSION["want"] == 2)
			$query_text = "SELECT * FROM `chat_sessions` WHERE `sex` = '2' AND `search` = :s AND `status` = '0' AND `sess` != :sess AND `ping` > :time";

		$ptime = time()-30;
		$query_select = $db->prepare("$query_text");
		$query_select->bindParam(':sess', $sid);
		$query_select->bindParam(':s', $_SESSION["sex"]);
		$query_select->bindParam(':time', $ptime);
		$query_select->execute();

		$d10 = 0;
		if($query_select->rowCount() > 0){ // если собеседники есть, но они заблокированы, тогда выберем любого
			$query_get_count = $db->prepare("$query_text");
			$query_get_count->bindParam(':sess', $sid);
			$query_get_count->bindParam(':s', $_SESSION["sex"]);
			$query_get_count->bindParam(':time', $ptime);
			$query_get_count->execute();
			while($row = $query_get_count->fetch()){ // если использовать $query_select->fetch(), то это ломает поиск (не знаю почему).
				if($cache->get('ban'.$sid.$row['sess']) == '1' || $cache->get('ban'.$row['sess'].$sid) == '1') $d10++;
			}
			unset($row);
		}

		if(($query_select->rowCount() == 0) || ($d10 >= $query_select->rowCount())){ // если нет собеседников, выберем любого.
				$query_select = $db->prepare("SELECT * FROM `chat_sessions` WHERE (`sex` = '1' OR `sex` = '2') AND `status` = '0' AND `sess` != :sess AND `ping` > :time");
				$query_select->bindParam(':sess', $sid);
				$query_select->bindParam(':time', $ptime);
				$query_select->execute();
		}

		if($query_select->rowCount() > 0){
			$step=0;
			while($row = $query_select->fetch()){
				$step++;
			
				if($cache->get('ban'.$sid.$row['sess']) == '1' || $cache->get('ban'.$row['sess'].$sid) == '1') continue; // Проверка на бан.
				
				if($step > $query_select->rowCount()){ // Если не последний возможный пользователь
				  $query_base_check = $db->prepare("SELECT * FROM `chat_talks` WHERE (`one` = :sess OR `two` = :sess) AND `status` = '1'");
				  $query_base_check->bindParam(':sess', $row['sess']);
				  $query_base_check->execute();
				  if($query_base_check->rowCount() >= 5 && ($base_one == 'good' || $base_one == 'bad') ){
					$row_base_check = $query_base_check->fetch();
					if($row_base_check['one'] != $sid) $j = $row_base_check['one'];
					if($row_base_check['two'] != $sid) $j = $row_base_check['two'];
						
						$query_base = $db->prepare("SELECT SUM(`start`), SUM(`end`) FROM `chat_talks` WHERE (`one` = :sess OR `two` = :sess) AND `status` = '1' ORDER BY `idt` DESC LIMIT 5");
						$query_base->bindParam(':sess', $j);
						$query_base->execute();
						$row_base = $query_base->fetch();
						$qwe = ($row_base["SUM(`end`)"] - $row_base["SUM(`start`)"])/5;
						if( $qwe >= 180) $base_two = 'good'; else $base_two = 'bad';
						if($base_one != $base_two) continue;
					}
				}
				
				sleep(2);

				$query_check = $db->prepare("SELECT * FROM `chat_sessions` WHERE `sess` = :sess AND `status` = '0'");
				$query_check->bindParam(':sess', $row['sess']);
				$query_check->execute();
				if($query_check->rowCount() != 1) continue; // Так как цикл, еще раз проверяем -> возможно уже нашел собеседника.

				$tmpRand = genRandStr();
				$query_insert = $db->prepare("INSERT INTO `chat_talks` (`one`, `two`, `room`, `start`) VALUES (:one, :two, :room, :time) ");
				$query_insert->bindParam(':one', $sid);
				$query_insert->bindParam(':two', $row['sess']);
				$query_insert->bindParam(':room', $tmpRand);
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
		}
	}
	break;

	case 'close':
		$query_update = $db->prepare("UPDATE `chat_talks` SET `status` = '1', `end` = :time WHERE (`one` = :sess OR `two` =:sess) AND `status` = '0'");
		$query_update->bindParam(':sess', $sid);
		$query_update->bindParam(':time', $time);
		$query_update->execute();
		setcookie('status', 2, 0, "/");
	break;

	case 'stop':
		// Завершаем беседу
		$query_update = $db->prepare("UPDATE `chat_talks` SET `status` = '1' WHERE (`one` = :sess OR `two` =:sess) AND `status` = '0'");
		$query_update->bindParam(':sess', $sid);
		$query_update->execute();

		// Ставим флаг -> собеседник занят
		$query_update = $db->prepare("UPDATE `chat_sessions` SET `status` = '1' WHERE `sess` = :sess");
		$query_update->bindParam(':sess', $sid);
		$query_update->execute();
	break;
}
