<?php
/*
	Если есть $_SESSION['sess']
	Проверяем есть ли активная сессия в таблице session.
	Если нет => _exit() /private/func.php
	Если да  => создаем массив $user
	
*/

$user = false;
auth();
