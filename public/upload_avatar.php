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

require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

upload_avatar();
