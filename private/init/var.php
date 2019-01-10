<?php
$var['page'] = '';
$var['release'] = '';
$var['time'] = time();
$var['ip'] = $_SERVER['REMOTE_ADDR'];
$var['user_agent'] = htmlspecialchars($_SERVER['HTTP_USER_AGENT'], ENT_QUOTES, 'UTF-8');
$var['default_user_values'] = '{"name":"","age":"","sex":"","vk":"","telegram":"","steam":"","phone":"","skype":"","facebook":"","instagram":"","youtube":"","twitch":"","twitter":""}';

$var['sex'] = [
	'Не указано',
	'Мужской',
	'Женский'
];

$var['status'] = [
	'1' => 'В работе',
	'2' => 'Завершен',
	'3' => 'Скрыт'
];

$var['group'] = [
	'Пользователь',
	'Редактор',
	'Админ'
];

$var['day'] = [ 
	'1' => 'Понедельник',
	'2' => 'Вторник',
	'3' => 'Среда',
	'4' => 'Четверг',
	'5' => 'Пятница',
	'6' => 'Субота',
	'7' => 'Воскресение',
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

$var['error'] = [
	'success' => 'Успех',
	'empty' => 'Пустое значение, заполните все поля',
	'wrong' => 'Неправильное значение',
	'authorized' => 'Уже авторизован',
	'registered' => 'Уже зарегистрирован',
	'long' => 'Слишком длинное значение',
	'wrongLogin' => 'Неправильный логин',
	'wrongEmail' => 'Неправильный email',
	'wrongUserAgent' => 'Неправильный user agent',
	'invalidUser' => 'Неправильный пользователь',
	'wrong2FA' => 'Неправильный код 2FA',
	'wrongPasswd' => 'Неправильный пароль',
	'noUser' => 'Нет такого пользователя',
	'wrongHash' => 'Неправильный hash',
	'wrongLink' => 'Неправильная ссылка',
	'reCaptcha3' => 'reCaptcha проверка не пройдена: низкий score',
	'coinhive' => 'Coinhive проверка не пройдена',
	'checkEmail' => 'Проверьте почту',
	'unauthorized' => 'Неавторизованный пользователь',
	'2FA' => '2FA уже активирована',
	'2FAdisabled' => '2FA выключена',
	'2FAenabled' => '2FA включена',
	'access' => 'Доступ запрещен',
	'same' => 'Одинаковые данные',
	'used' => 'Уже занято',
	'noUploadFile' => 'Неудачная загрузка',
	'uploadError' => 'Неудачная загрузка',
	'wrongType' => 'Неправильный формат файла',
	'maxSize' => 'Слишком большой файл',
	'maxarg' => 'Слишком много аргументов',
	'wrongData' => 'Неправильные данные',
	'wrongRelease' => 'Неправильный релиз',
	'exitTorrent' => 'Торрент уже добавлен',
];
