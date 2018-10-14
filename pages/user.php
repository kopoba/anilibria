<?php
/*
    Запрашиваем поля пользователя show_profile();
    if(!isset($_GET["id"])) => Проверяем, если в ссылке не указан ?id=userid,
    то, загружаем данные залогиненого пользователя.

    Если пользователь не найден => выводим ошибку, скрываем пустые поля профиля

    Пользователь найден => Записываем нужные нам данные в массив $userInfo и выводим на странице

    В дальнейшем:
    1. Изменение данных 90% / 100%
    2. Привязка 2FA
    3. Оформление страницы
    4. Настройки (приватность)
    5. Привязка аккаунтов Патреон/ВК

*/

require_once($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');


if(!empty($_GET['id']) && ctype_digit($_GET['id'])){
	$profile = show_profile($_GET['id']);
}else{
	$profile = ['err' => true, 'mes' => 'К сожалению, такого пользователя не существует.'];
}

require_once($_SERVER['DOCUMENT_ROOT'].'/private/header.php');

if($profile['err']) {
	echo "<div id=\"error\" style=\"display: block; text-align: center;\">{$profile['mes']}</div>";
}else{
	echo "<p>";
	echo "<b>ID:</b><span>&nbsp;{$profile['mes']['id']}</span><br/>";
	echo "<b>Nickname:</b><span>&nbsp{$profile['mes']['nickname']}</span><br/>";
	echo "<b>Доступ:</b><span>&nbsp;{$var['group'][$profile['mes']['access']]}</span><br/>";
	if(!empty($profile['mes']['user_values']) && is_array($profile['mes']['user_values'])){
		foreach($profile['mes']['user_values'] as $v => $k){
			echo "<b>{$var['user_values'][$v]}</b><span>&nbsp;$k</span><br/>";
		}
	}
	echo "<b>Пол:</b><span>&nbsp;{$var['sex'][$profile['mes']['sex']]}</span><br/>";
	echo "<b>Дата регистрации:</b><span>&nbsp;{$profile['mes']['register_date']}</span><br/>";
	echo "</p>";

	echo "<img class=\"rounded\" id=\"avatar\" src=\"".getUserAvatar($_GET['id'])."\" alt=\"avatar\">";
}

require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');
