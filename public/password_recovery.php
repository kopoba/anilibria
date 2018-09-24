<?php
/*
	На сайте пользователь заполняет форму.
	- Email
	- coinhive (Proof of Work Captcha)
	
	Отправляет POST запрос. Ловим данные. Выполняем проверки:
	- coinhive proof
	- empty $_POST['mail']
	- strlen email max 254
	- FILTER_VALIDATE_EMAIL $_POST['mail']
	- Запрос отправил неавторизованный пользователь. (?)
	- Есть ли такой пользователь?
	Если ошибка - отвечаем json {err:error, mes:причина}.
	
	Узнаем ip с которого получили запрос.
	
	Генерируем ссылку /public/password_recovery.php?id=...&time=...&hash=...
	user_id
	time()+43200 время действия ссылки 12 часов
	hash('sha512', ip.user_id.time()+43200.half_string(password_hash))
	
	Подпись нельзя подделать без password_hash.
	Если пароль поменяли - ссылка недействительна (новый password_hash).
	
	Отправляем ссылку + ip на почту.
*/

require_once($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');

if(!coinhive_proof()){
	_message('Coinhive captcha error', 'error');
}

if(empty($_POST['mail'])){
	_message('Empty post value', 'error');
}

if(strlen($_POST['mail']) > 254){
	_message('Too long login or email', 'error');
}

if(!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)){
	_message('Wrong email', 'error');
}

$query = $db->prepare("SELECT * FROM `users` WHERE `mail` = :mail");
$query->bindParam(':mail', $_POST['mail'], PDO::PARAM_STR);
$query->execute();
if($query->rowCount() == 0){
	_message('No such user', 'error');
}
$row = $query->fetch();

$ip = $_SERVER['REMOTE_ADDR'];
$time = time()+43200;
$hash = hash('sha512', $ip.$row['id'].$time.half_string($row['passwd']));
$link = "http://test.poiuty.com/public/password_link.php?id={$row['id']}&time={$time}&hash={$hash}";

_mail($row['mail'], "Восстановление пароля", "Запрос отправили с IP $ip<br/>Чтобы восстановить пароль <a href='$link'>перейдите по ссылке</a>.");
_message('Success');
