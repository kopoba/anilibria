<?php
$var['time'] = time();
$var['ip'] = $_SERVER['REMOTE_ADDR'];
$var['user_agent'] = htmlspecialchars($_SERVER['HTTP_USER_AGENT']);
$var['default_user_values'] = '{"name":"","age":"","sex":"","vk":"","telegram":"","steam":"","phone":"","skype":"","facebook":"","instagram":"","youtube":"","twitch":"","twitter":""}';

$var['sex'] = [
	'Не указано',
	'Мужской',
	'Женский'
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
	'nickname' => 'Nickname', 
	'access' => 'Доступ', 
	'register_date' => 'Дата регистрации', 
	'last_activity' => 'Последняя активность',
	
	'name' => 'Имя',
	'age' => 'Возраст',
	'sex' => 'Пол', 
	'vk' => 'Вконтакте', 
	'telegram' => 'Телеграм',
	'steam' => 'SteamID',
	 
	'phone' => 'Телефон',
	'skype' => 'Skype',
	'facebook' => 'Facebook',
	'instagram' => 'Instagram',
	'youtube' => 'Youtube',
	'twitch' => 'Twitch',
	'twitter' => 'Twitter',
	'telegram' => 'Telegram'
];

