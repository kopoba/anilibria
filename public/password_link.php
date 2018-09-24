<?php
/*
	Получаем $_GET запрос:
	id
	time
	hash
	
	Узнаем ip с которого получили запрос.
	
	Выполняем проверки:
	- empty $_GET['id'], $_GET['time'], $_GET['hash']
	- is_numeric $_GET['id'] & $_GET['time']
	- Запрос отправил неавторизованный пользователь. (?)
	- Есть ли такой пользователь?
	- hash == hash('sha512', ip.id.time.half_string(password_hash))
	- time > time()
	Если ошибка - отвечаем json {err:error, mes:причина}.
	
	Генерируем пароль (8 символов) => hash. Обновляем запись в базе.
	|     id       |     ...     |    password      |
	| $_POST['id'] |     ...     |  password_hash() |
	
	Отправляем пароль на почту пользователя.
	
	Links
	http://php.net/manual/ru/function.password-hash.php
*/

require_once($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');

if(empty($_GET['id']) || empty($_GET['time']) || empty($_GET['hash'])){
	_message('Empty get value', 'error');
}

if(!is_numeric($_GET['id']) || !is_numeric($_GET['time'])){
	_message('Wrong id or time', 'error');	
}

$query = $db->prepare("SELECT * FROM `users` WHERE `id` = :id");
$query->bindParam(':id', $_GET['id'], PDO::PARAM_STR);
$query->execute();
if($query->rowCount() == 0){
	_message('No such user', 'error');
}
$row = $query->fetch();

$ip = $_SERVER['REMOTE_ADDR'];
$hash = hash('sha512', $ip.$_GET['id'].$_GET['time'].half_string($row['passwd']));
if($_GET['hash'] != $hash){
	_message('Wrong hash', 'error');
}

if(time() > $_GET['time']){
	_message('Invalid link', 'error');
}

$passwd = genRandStr(8);
$hash = rehash($passwd);

$query = $db->prepare("UPDATE `users` SET `passwd` = :passwd WHERE `id` = :id");
$query->bindValue(':id', $row['id'], PDO::PARAM_STR);
$query->bindParam(':passwd', $hash, PDO::PARAM_STR);
$query->execute();

_mail($row['mail'], "Новый пароль", "Ваш пароль: $passwd");
_message('Success');
