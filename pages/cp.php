<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/minify.php');

if(!$user){
	_message2('Unauthorized user', 'error');
}

if(!empty($user['2fa'])){
	$tmpMes = 'ВЫКЛЮЧИТЬ 2FA';
}else{
	$tmpMes = 'ВКЛЮЧИТЬ 2FA';
}

require_once($_SERVER['DOCUMENT_ROOT'].'/private/header.php');
?>

<div class="news-block" id="rules">
		<div class="news-header">
			<h2 class="news-name" style="float:left;">
				Двухфакторная аутентификация
			</h2>
			<h2 class="news-name" id="2faMes" style="float:left; padding-left: 10px;"></h2>
			<div class="clear"></div>	
		</div>
		<div class="clear"></div>
		<div>
			Установите на мобильный телефон приложение Google Authenticator [<a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=ru">android</a>] [<a href="https://itunes.apple.com/ru/app/google-authenticator/id388497605?mt=8">ios</a>]<br/>
			<div id="2fagen" style="<?php if(!empty($user['2fa'])){ echo "display: none;"; }?>">
			<div id="2fakey"></div>
				<input class="btn btn btn-success btn-block" style="margin-top: 10px;" type="submit" data-2fa-generate value="Сгенерировать ключ">
			</div>
			<input class="form-control" id="2fapasswd" style="margin-top: 10px;" type="password" placeholder="Пароль" required="">
			<input class="form-control" id="2facheck" style="margin-top: 10px;" type="text" placeholder="Код" required="">
			<input class="btn btn btn-success btn-block" style="margin-top: 10px;" type="submit" id="send2fa" data-2fa-start value="<?php echo $tmpMes; ?>">
		</div>
		<div class="clear"></div>
		<div class="news_footer"></div>
</div>

<div class="news-block">
		<div class="news-header">
			<h2 class="news-name" style="float:left;">
				Изменить почту
			</h2>
			<h2 class="news-name" id="changeEmailMes" style="float:left; padding-left: 10px;"></h2>
			<div class="clear"></div>	
		</div>
		<div class="clear"></div>
		<div>			
			<input class="form-control" id="changeEmail" type="text" placeholder="Новый email">
			<input class="form-control" id="changeEmailPasswd" style="margin-top: 10px;" type="password" placeholder="Пароль">
			<input class="btn btn btn-success btn-block" style="margin-top: 10px;" data-change-email type="submit" value="ОТПРАВИТь">
		</div>
		<div class="clear"></div>
		<div class="news_footer"></div>
</div>

<div class="news-block">
		<div class="news-header">
			<h2 class="news-name" style="float:left;">
				Изменить пароль
			</h2>
			<h2 class="news-name" id="changePasswdMes" style="float:left; padding-left: 10px;"></h2>
			<div class="clear"></div>	
		</div>
		<div class="clear"></div>
		<div>			
			<input class="form-control" id="changePasswd" type="password" placeholder="Старый пароль">
			<input class="btn btn btn-success btn-block" style="margin-top: 10px;" data-change-passwd type="submit" value="ОТПРАВИТь">
		</div>
		<div class="clear"></div>
		<div class="news_footer"></div>
</div>

<?require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
