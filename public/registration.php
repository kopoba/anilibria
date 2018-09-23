<?php
/*
	На сайте пользователь заполняет форму регистрации.
	- Login
	- Email
	- coinhive (Proof of Work Captcha)
	
	Отправляет POST запрос. Ловим данные. Выполняем проверки:
	- coinhive proof
	- empty $_POST['login'] & $_POST['email']
	- FILTER_VALIDATE_EMAIL $_POST['email']
	- Уже зарегистрирован?
	Если ошибка - отвечаем json {err:error, mes:причина}.
	
	Генерируем пароль (8 символов) => hash. Добавляем запись в базу.
	|     login       |       email      |    password      |   create   |
	| $_POST['login'] |  $_POST['email'] |  password_hash() |   time()   |
	
	Отправляем пароль на почту $_POST['email']
	
	Links
	https://coinhive.com/documentation/captcha
	http://php.net/manual/ru/function.password-hash.php
*/

function _message($mes, $err = 'ok'){
	$arr = ['err' => $err, 'mes' => $mes];
	echo json_encode($arr);
	die();
}

if(empty($_POST['login']) || empty($_POST['email'])){
	_message('Empty post value', 'error');
}

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	_message('Wrong email', 'error');
} 
