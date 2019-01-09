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

require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/func.php');

password_recovery();
