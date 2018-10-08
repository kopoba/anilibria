<?
/*
    Запрашиваем поля пользователя show_profile();
    if(!isset($_GET["id"])) => Проверяем, если в ссылке не указан ?id=userid,
    то, загружаем данные залогиненого пользователя.

    Если пользователь не найден => выводим ошибку, скрываем пустые поля профиля

    Пользователь найден => Записываем нужные нам данные в массив $userInfo и выводим на странице

    В дальнейшем:
    1. Изменение данных
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

if(!$user){
	_message('Unauthorized user', 'error');
}

if(!empty($user['2fa'])){
	$tmpMes = 'Выключить 2FA';
}else{
	$tmpMes = 'Включить 2FA';
}

require_once($_SERVER['DOCUMENT_ROOT'].'/private/header.php');
?>

<h3>Двухфакторная аутентификация</h3>
Установите на мобильный телефон Google Authenticator [<a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=ru">android</a>] [<a href="https://itunes.apple.com/ru/app/google-authenticator/id388497605?mt=8">ios</a>].<br/>		
<hr/>

<div id="2fagen" style="<?php if(!empty($user['2fa'])){ echo "display: none;"; }?>">
	<div id="2fakey"></div>
	<div id="user_log_reg_rec">
		<input data-2fa-generate="" value="Сгенерировать ключ" type="submit">
	</div>
	<hr/>
</div>

<div id="user_log_reg_rec">
<div class="input_wrapper">
	<input id="passwd" class="styled_input" type="password" spellcheck="false" required />
	<label for="login" class="floating_label">Пароль</label>
</div>
<div class="input_wrapper">
	<input id="2facheck" class="styled_input" type="text" spellcheck="false" required />
	<label for="login" class="floating_label">Код</label>
</div>
<input type="submit" id="send2fa" data-2fa-start value="<?php echo $tmpMes; ?>" />

<div id="error"></div>
</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
