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
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');

password_link();
