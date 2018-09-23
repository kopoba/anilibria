<?php
/*
	На сайте пользователь заполняет форму.
	- Login
	- Email
	- coinhive (Proof of Work Captcha)
	
	Отправляет POST запрос. Ловим данные. Выполняем проверки:
	- coinhive proof
	- empty $_POST['login'] & $_POST['mail']
	- strlen login max 20, email max 254
	- FILTER_VALIDATE_EMAIL $_POST['mail']
	- Запрос отправил неавторизованный пользователь.
	- Уже зарегистрирован $_POST['login'] или $_POST['mail'] ?
	Если ошибка - отвечаем json {err:error, mes:причина}.
	
	Генерируем пароль (8 символов) => hash. Добавляем запись в базу.
	|     login       |       mail      |    password      |   create   |
	| $_POST['login'] |  $_POST['mail'] |  password_hash() |   time()   |
	
	Отправляем пароль на почту $_POST['mail']
	
	Links
	https://coinhive.com/documentation/captcha
	http://php.net/manual/ru/function.password-hash.php
*/

require_once($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');

function _message($mes, $err = 'ok'){
	$arr = ['err' => $err, 'mes' => $mes];
	echo json_encode($arr);
	die();
}

function genRandStr($length = 10) {
	$str = ''; $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~!@#$%^&*()_+-=';
	for ($i = 0; $i < $length; $i++) {
		$str .= $chars[random_int(0 ,strlen($chars)-1)];
	}
    return $str;
}

function rehash($passwd, $hash = 0, $a = 'no_hash'){
	if(empty($hash) || password_needs_rehash($hash, PASSWORD_DEFAULT))
		$a = password_hash($passwd, PASSWORD_DEFAULT);
	return $a;
}

function _mail($email, $subject, $message){
	global $conf;
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=utf-8\r\n";
	$headers .= "Content-Transfer-Encoding: base64\r\n";
	$subject  = "=?utf-8?B?".base64_encode($subject)."?=";
	$headers .= "From: {$conf['email_from']} <{$conf['email']}>\r\n";
	mail($email, $subject, rtrim(chunk_split(base64_encode($message))), $headers);
}

if(empty($_POST['login']) || empty($_POST['mail'])){
	_message('Empty post value', 'error');
}

if(strlen($_POST['login']) > 20 || strlen($_POST['mail']) > 254){
	_message('Too long login or email', 'error');
}

if(!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)){
	_message('Wrong email', 'error');
} 

$query = $db->prepare("SELECT * FROM `users` WHERE `login` = :login OR `mail`= :mail");
$query->bindValue(':login', $_POST['login'], PDO::PARAM_STR);
$query->bindParam(':mail', $_POST['mail'], PDO::PARAM_STR);
$query->execute();
if($query->rowCount() > 0){
	_message('Already registered', 'error');
}

$passwd = genRandStr(8);
$hash = rehash($passwd);

$query = $db->prepare("INSERT INTO `users` (`login`, `mail`, `passwd`) VALUES (:login, :mail, :passwd)");
$query->bindValue(':login', $_POST['login'], PDO::PARAM_STR);
$query->bindParam(':mail', $_POST['mail'], PDO::PARAM_STR);
$query->bindParam(':passwd', $hash, PDO::PARAM_STR);
$query->execute();

_mail($_POST['mail'], "Регистрация", "Вы успешно зарегистрировались на сайте!<br/>Ваш пароль: $passwd");
_message('Success registration');
