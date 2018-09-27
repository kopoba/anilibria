<?php
/*
	Получаем $_POST
	- login
	- passwd
	
	Узнаем ip с которого получили запрос.
	
	Выполняем проверки:
	- Уже авторизован?
	- empty $_POST['login'] & $_POST['passwd']
	- strlen login max 20
	- login only 0-9A-Za-z
	- Есть ли такой пользователь?
	- strlen user agent max 256 
	- password_verify($_POST['passwd'])
	Если ошибка - отвечаем json {err:error, mes:причина}.
	
	Если password_hash устарел - обновим его.
	
	$_SESSION['sess']	- hash('sha512', ip.agent.time()+86400.$login.half_string(password_hash));
	
	session table
	|  id  |  uid  |  hash  |  time  |  ip  |  info  |
	
	id		auto increment
	uid		user id from table users
	hash	$_SESSION['sess']
	time	valid until time()+86400
	ip		$_SERVER['REMOTE_ADDR']
	info	$_SERVER['HTTP_USER_AGENT']
	
	
	limit 10 sessions
	
	Links
	http://php.net/manual/ru/function.password-verify.php

*/

require_once($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

if($user){
	_message('Already authorized', 'error');
}

if(empty($_POST['login']) || empty($_POST['passwd'])){
	_message('Empty post value', 'error');
}

if(strlen($_POST['login']) > 20){
	_message('Too long login or email', 'error');
}

if(preg_match('/[^0-9A-Za-z]/', $_POST['login'])){
	_message('Wrong login', 'error');
}

$time = time()+86400;
$ip = $_SERVER['REMOTE_ADDR'];
$agent = $_SERVER['HTTP_USER_AGENT'];

if(strlen($agent) > 256){
	_message('Wrong user agen', 'error');
}

$query = $db->prepare("SELECT * FROM `users` WHERE `login` = :login");
$query->bindValue(':login', $_POST['login'], PDO::PARAM_STR);
$query->execute();
if($query->rowCount() == 0){
	_message('Invalid user', 'error');
}
$row = $query->fetch();

if(!password_verify($_POST['passwd'], $row['passwd'])){
	_message('Wrong password', 'error');
}

$hash = rehash($_POST['passwd'], $row['passwd']);
if($hash != 'no_hash'){
	$query = $db->prepare("UPDATE `users` SET `passwd` = :passwd WHERE `id` = :id");
	$query->bindParam(':passwd', $hash, PDO::PARAM_STR);
	$query->bindParam(':id', $row['id'], PDO::PARAM_STR);
	$query->execute();
	$row['passwd'] = $hash;
}

$_SESSION['sess'] = hash('sha512', $ip.$agent.$time.$row['login'].half_string($row['passwd']));

$query = $db->prepare("INSERT INTO `session` (`uid`, `hash`, `time`, `ip`, `info`) VALUES (:uid, :hash, :time, :ip, :info)");
$query->bindParam(':uid', $row['id'], PDO::PARAM_STR);
$query->bindParam(':hash', $_SESSION['sess'], PDO::PARAM_STR);
$query->bindParam(':time', $time, PDO::PARAM_STR);
$query->bindParam(':ip', $ip, PDO::PARAM_STR);
$query->bindParam(':info', $agent, PDO::PARAM_STR);
$query->execute();

$query = $db->prepare("SELECT `id` FROM `session` WHERE `uid` = :uid");
$query->bindParam(':uid', $row['id'], PDO::PARAM_STR);
$query->execute();
if($query->rowCount() > 10){
	$row = $query->fetch();
	$query = $db->prepare("DELETE FROM `session` WHERE `id` = :id");
	$query->bindParam(':id', $row['id'], PDO::PARAM_STR);
	$query->execute();
}

_message('Success');
