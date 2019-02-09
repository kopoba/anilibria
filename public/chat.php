<?php
require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');

$site = $_SERVER['HTTP_HOST'];

$time = time();
$sid = session_id();
$online = $cache->get('online');

if($online === false){
	$all = 0; $kun = 0; $chan = 0;
	$query = $db->prepare('SELECT * FROM `chat_sessions` WHERE `ping` > UNIX_TIMESTAMP()-90');
	$query->execute();
	while($row = $query->fetch()){
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

if(empty($_POST['do'])){
	die('no action');
}

switch($_POST['do']){
	default: die('wrong action'); break;
	case 'typing': $cache->set($sid.'typing', $time, 60); break;

	case 'ping':
		$query = $db->prepare('UPDATE `chat_sessions` SET `ping` = :time WHERE `sess` = :sess');
		$query->bindParam(':time', $time);
		$query->bindParam(':sess', $sid);
		$query->execute();
		$cache->set($sid.'ping', $time, 60);
		echo $online;
		if(rand(1, 1000) == 100){
			$timeout = $time-86400;
			$query = $db->prepare('DELETE FROM `chat_sessions` WHERE `ping` < :time');
			$query->bindParam(':time', $timeout);
			$query->execute();
			$query = $db->prepare('DELETE FROM `chat_talks` WHERE `start` < :time');
			$query->bindParam(':time', $timeout);
			$query->execute();
			$query = $db->prepare('DELETE FROM `chat_messages` WHERE `time` < :time');
			$query->bindParam(':time', $timeout);
			$query->execute();
		}
	break;

	case 'close':
		$query = $db->prepare('UPDATE `chat_talks` SET `status` = \'1\', `end` = :time WHERE `idt` = :idt');
		$query->bindParam(':time', $time);
		$query->bindParam(':idt', $_SESSION['idt']);
		$query->execute();
		if(!empty($_SESSION['idt'])){
			$_SESSION['idt_last'] = $_SESSION['idt'];
		}
		unset($_SESSION['idt']);
	break;
	
	case 'exit':
		$query = $db->prepare('DELETE FROM `chat_sessions` WHERE `sess` = :sess');
		$query->bindParam(':sess', $sid);
		$query->execute();
		unset($_SESSION['sex']);
		unset($_SESSION['want']);
		unset($_SESSION['idt']);
	break;
	
	case 'stop':		
		$query = $db->prepare('UPDATE `chat_sessions` SET `status` = \'1\' WHERE `sess` = :sess'); // Ставим флаг -> собеседник занят
		$query->bindParam(':sess', $sid);
		$query->execute();
		if(!empty($_SESSION['idt'])){
			$query = $db->prepare('UPDATE `chat_talks` SET `status` = \'1\' WHERE `idt` = :idt'); // Завершаем беседу
			$query->bindParam(':idt', $_SESSION['idt']);
			$query->execute();
			unset($_SESSION['idt']);
		}
	break;
	
	case 'ban':
		if(empty($_SESSION['idt_last'])){
			die;
		}
		$query = $db->prepare('SELECT * FROM `chat_talks` WHERE `idt` = :idt');
		$query->bindParam(':idt', $_SESSION['idt_last']);
		$query->execute();
		if($query->rowCount() == 1){	
			$row = $query->fetch();
			if($row['one'] != $sid) $j = $row['one'];
			if($row['two'] != $sid) $j = $row['two'];
			$cache->set('ban'.$sid.$j, '1', 3600);
		}	
	break;

	case 'add':
		if(empty($_POST['mes'])){
			die;
		}
		$_POST['mes'] = trim(strip_tags($_POST['mes']));
		if(strlen($_POST['mes']) == 0 || mb_strlen($_POST['mes'], 'UTF-8') > 1000){
			die;
		}
		$query = $db->prepare('SELECT * FROM `chat_talks` WHERE `idt` = :idt AND `status` = 0');
		$query->bindParam(':idt', $_SESSION["idt"]);
		$query->execute();
		if($query->rowCount() != 1){	
			 die($_SESSION['idt']);
		}
		$row = $query->fetch();
		if($cache->get($sid.'message') > round(microtime(true) * 1000)){
			die(json_encode(['status' => 'spam', 'mes' => 'вы слишком быстро отправляете сообщения.']));
		}
		$cache->set($sid.'message', round(microtime(true) * 1000)+500, 600);
		$query = $db->prepare('INSERT INTO `chat_messages` (`idt`, `session`, `message`, `time`) VALUES (:idt, :session, :message, :time) ');
		$query->bindParam(':idt', $_SESSION['idt']);
		$query->bindParam(':session', $sid);
		$query->bindParam(':message', $_POST['mes']);
		$query->bindParam(':time', $time);
		$query->execute();
		echo json_encode(['status' => 'ok', 'mes' => $_POST['mes']]);
	break;

	case 'get':
		if(empty($_SESSION['idt'])){
			die;
		}
		$arr = []; $msg = '';
		$query = $db->prepare('SELECT * FROM `chat_talks` WHERE `idt` = :idt AND `status` = 0');
		$query->bindParam(':idt', $_SESSION['idt']);
		$query->execute();
		if($query->rowCount() != 1){ // Собеседник отключился
			$arr['status'] = 'end';
			if(!empty($_SESSION['idt'])){
				$_SESSION['idt_last'] = $_SESSION['idt'];
			}
			unset($_SESSION['idt']);
			die(json_encode($arr)); 
		} 
		$row = $query->fetch();
		if($row['one'] != $sid) $j = $row['one'];
		if($row['two'] != $sid) $j = $row['two'];
		$arr['status'] = 'online';
		if(time() > $cache->get($j.'ping')+30){
			$arr['status'] = 'offline';
		}
		if($cache->get($j.'typing') > time()-2){
			$arr['status'] = 'typing';
		}
		$query = $db->prepare('SELECT * FROM `chat_messages` WHERE `idt` = :idt AND `send` != \'1\' AND `session` != :session');
		$query->bindParam(':idt', $_SESSION["idt"]);
		$query->bindParam(':session', $sid);
		$query->execute();
		if($query->rowCount() > 0){
			$row = $query->fetch();
			$query = $db->prepare("DELETE FROM `chat_messages` WHERE `id` = :id");	// мы не читаем переписку пользователей
			$query->bindParam(':id', $row['id']);									// сразу как доставили сообщение - удаляем
			$query->execute();
			$msg = $row['message'];
		}
		echo json_encode(['status' => $arr['status'], 'mes' => $msg]);
	break;

	case 'register':
		if(empty($_POST['sex'])){
			die;
		}
		$sex = $_POST['sex'];
		$search = @$_POST['m']+ @$_POST['w'];
		if($search < 1 || $search > 3){
			$search = 3;
		}		
		if($sex != 1 && $sex != 2){
			$sex = 1;
		}
		if(!empty($_POST['an'])){
			$cache->set('an.'.$sid, 1, 72000);
		}else{
			$cache->delete('an.'.$sid);
		}
		$query = $db->prepare('SELECT * FROM `chat_sessions` WHERE `sess` = :sess');
		$query->bindParam(':sess', $sid);
		$query->execute();
		if($query->rowCount() == 0){
			$query = $db->prepare('INSERT INTO `chat_sessions` (`sess`, `ip`, `sex`, `search`, `enter`) VALUE (:sess, :ip, :sex, :search, :enter)');
			$query->bindParam(':sess', $sid);
			$query->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
			$query->bindParam(':sex', $sex);
			$query->bindParam(':search', $search);
			$query->bindParam(':enter', $time);
			$query->execute();
		}
		if($query->rowCount() == 1){
			$query = $db->prepare('UPDATE `chat_sessions` SET `sex` = :sex, `search` = :search, `enter` = :enter WHERE `sess`=:sess');
			$query->bindParam(':sess', $sid);
			$query->bindParam(':sex', $sex);
			$query->bindParam(':search', $search);
			$query->bindParam(':enter', $time);
			$query->execute();
		}
		$_SESSION['sex']  = $sex;
		$_SESSION['want'] = $search;
		header("Location: https://$site/pages/chat.php");
	break;

	case 'search':
		if(empty($_SESSION['want']) || empty($_SESSION['sex'])){
			die;
		}
		$xflag = false;
		$query = $db->prepare('SELECT * FROM `chat_talks` WHERE `one` = :sess AND `status` = 0');
		$query->bindParam(':sess', $sid);
		$query->execute();
		if($query->rowCount() == 1){
			$xflag = true;
		}else{
			$query = $db->prepare('SELECT * FROM `chat_talks` WHERE `two` = :sess AND `status` = 0');
			$query->bindParam(':sess', $sid);
			$query->execute();
			if($query->rowCount() == 1){
				$xflag = true;
			}
		}
		if($xflag){
			$row = $query->fetch();
			$_SESSION['idt'] = $row['idt'];
			if($cache->get('hello'.$sid.$_SESSION['idt']) == 1){
				die(json_encode(['status' => 'find', 'mes' => "Вы можете продолжить беседу<br/>"]));
			}
			if($row['one'] != $sid) $j = $row['one'];
			if($row['two'] != $sid) $j = $row['two'];
			$cache->set('hello'.$sid.$_SESSION['idt'], 1, 86400);
			if($cache->get('an.'.$j) == 1){ // Не показываем пол собеседника
				die(json_encode(['status' => 'find', 'mes' => "Собеседник найден! Общайтесь!<br/>"]));
			}
			$query = $db->prepare('SELECT * FROM `chat_sessions` WHERE `sess` = :sess');
			$query->bindParam(':sess', $j);
			$query->execute();
			$row = $query->fetch();
			if($row['sex'] == 1){
				$sex = 'Найден кун';
			}else{
				$sex = 'Найдена тян!';
			}
			die(json_encode(['status' => 'find', 'mes' => "$sex. Общайтесь!<br/>"]));
		}
		
		$query = $db->prepare('UPDATE `chat_sessions` SET `status` = \'0\' WHERE `sess` = :sess');
		$query->bindParam(':sess', $sid);
		$query->execute();
		$timeout = $time-30;
		$sql = 'SELECT * FROM `chat_sessions` WHERE `status` = \'0\' AND `sess` != :sess AND `ping` > :time'; // default want 3
		if($_SESSION['want'] != 3){
			$sql .= ' AND `sex` = :want AND `search` = :sex';
		}
		$query = $db->prepare($sql);
		$query->bindParam(':sess', $sid);
		if($_SESSION['want'] != 3){
			$query->bindParam(':want', $_SESSION['want']);
			$query->bindParam(':sex', $_SESSION['sex']);
		}
		$query->bindParam(':time', $timeout);
		$query->execute();
		while($row = $query->fetch()){
			if($cache->get('ban'.$sid.$row['sess']) == '1' || $cache->get('ban'.$row['sess'].$sid) == '1'){ // Проверка на бан.
				continue;
			}
			$tmp = $db->prepare('SELECT * FROM `chat_sessions` WHERE `sess` = :sess AND `status` = \'0\''); 
			$tmp->bindParam(':sess', $row['sess']);
			$tmp->execute();
			if($tmp->rowCount() != 1){ // Возможно уже нашел собеседника?
				continue;
			}
			$tmp = $db->prepare('INSERT INTO `chat_talks` (`one`, `two`, `start`) VALUES (:one, :two, :time)');
			$tmp->bindParam(':one', $sid);
			$tmp->bindParam(':two', $row['sess']);
			$tmp->bindParam(':time', $time);
			$tmp->execute();
			
			$tmp = $db->prepare('UPDATE `chat_sessions` SET `status` = \'1\' WHERE `sess` = :one');
			$tmp->bindParam(':one', $sid);
			$tmp->execute();

			$tmp = $db->prepare('UPDATE `chat_sessions` SET `status` = \'1\' WHERE `sess` = :two');
			$tmp->bindParam(':two', $row['sess']);
			$tmp->execute();
			break; // Как нашли, завершаем цикл
		}
		die(json_encode(['status' => 'search']));		
	break;
}
