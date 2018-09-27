<?php
/*
	Если есть $_SESSION['sess']
	Проверяем есть ли активная сессия в таблице session.
	Если нет => _exit() /private/func.php
	Если да  => создаем массив $user
	
*/

$user = false;
if(!empty($_SESSION['sess'])){
	$query = $db->prepare("SELECT * FROM `session` WHERE `hash` = :hash AND `time` > unix_timestamp(now())");
	$query->bindParam(':hash', $_SESSION['sess'], PDO::PARAM_STR);
	$query->execute();
	if($query->rowCount() != 1){
		_exit();
	}
	$row = $query->fetch();
	
	$query = $db->prepare("SELECT * FROM `users` WHERE `id` = :id");
	$query->bindParam(':id', $row['uid'], PDO::PARAM_STR);
	$query->execute();
	if($query->rowCount() != 1){
		_exit();
	}
	$row = $query->fetch();
	$user = ['id' => $row['id'], 'login' => $row['login'], 'mail' => $row['mail']];
}
