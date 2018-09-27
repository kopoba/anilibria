<?php
/*
	На сайте пользователь заполняет форму.
	- Login
	- Email
	- coinhive (Proof of Work Captcha)
	
	Отправляет POST запрос. Ловим данные. Выполняем проверки:
	- уже авторизован?
	- coinhive proof
	- empty $_POST['login'] & $_POST['mail']
	- strlen login max 20, email max 254
	- login only 0-9A-Za-z
	- FILTER_VALIDATE_EMAIL $_POST['mail']
	- Запрос отправил неавторизованный пользователь. (?)
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
require_once($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

registration();
