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
	- Есть ли такой пользователь?
	Если ошибка - отвечаем json {err:error, mes:причина}.
	
	Узнаем ip с которого получили запрос.
	
	Генерируем ссылку /public/password_recovery.php?id=...&time=...&hash=...
	user_id
	time()+43200 время действия ссылки 12 часов
	hash('sha512', ip.user_id.time()+43200.password_hash) подпись нельзя подделать без password_hash
	
	Отправляем ссылку + ip на почту.
*/

