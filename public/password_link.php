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
	- Есть ли такой пользователь?
	- hash == hash('sha512', ip.id.time.password_hash)
	Если ошибка - отвечаем json {err:error, mes:причина}.
	
	Генерируем пароль (8 символов) => hash. Обновляем запись в базе.
	|     id       |     ...     |    password      |
	| $_POST['id'] |     ...     |  password_hash() |
	
	Отправляем пароль на почту пользователя.
	
	Links
	http://php.net/manual/ru/function.password-hash.php
*/

