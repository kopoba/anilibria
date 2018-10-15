<?php
$var['time'] = time();
$var['ip'] = $_SERVER['REMOTE_ADDR'];
$var['user_agent'] = $_SERVER['HTTP_USER_AGENT'];


$var['sex'] = [
	'Не указано',
	'Мужчина',
	'Женщина'
];

$var['group'] = [
	'Заблокирован',
	'Пользователь',
	'Спонсор',
	'Либриец',
	'Редактор',
	'Админ'
];

$var['user_values'] = [
	'id' => 'ID',
	'login' => 'Логин',
	'mail' => 'Email',
	'nickname' => 'Nickname', 
	'access' => 'Доступ', 
	'register_date' => 'Дата регистрации', 
	'last_activity' => 'Последняя активность',
	'sex' => 'Пол', 
	'vk' => 'Вконтакте', 
	'telegram' => 'Телеграм', 
	'steam' => 'SteamID', 
	'age' => 'Возраст', 
	'country' => 'Страна', 
	'city' => 'Город'
];
