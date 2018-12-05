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

<div class="news-block">		
	<div class="profile-info-left-side">
		<div class="profile-left-block-wrapper">
			<span class="profile-nickname"><b>VKuser323907417</b></span>			
			<div class="profile-avatar-wrapper">
				<a href="#" data-modal-show title="Изменить"><img src="/upload/avatars/2.jpg" id="profile-avatar" alt="" width="150" height="150"></a>
			</div>
			<div class="user-status">
				Зритель<br/>
				Сообщений: 550
			</div>
		</div>
	</div>
	
	<div class="profile-line"></div>
	
	<div class="profile-info-right-side">
		<div class="profile-right-block-content">
			<h3 class="profile-content-title">Личные данные</h3>
				<p class="data-label">Имя: <span class="user-data">Мәке</span></p>
				<p class="data-label">Пол: <span class="user-data">Мужской</span></p>
				<p class="data-label">Возраст: <span class="user-data">100</span></p>
				<p class="data-label">Был в сети: <span class="user-data">30.11.2019</span></p>
				<p class="data-label">Регистрация: <span class="user-data">20.11.2018</span></p>
				<br/>
				<h3 class="profile-content-title">Статистика</h3>
				<p class="data-label">Рейтинг: <span class="user-data">10</span></p>
				<p class="data-label">Скачал: <span class="user-data">100 TB</span></p>
				<p class="data-label">Раздал: <span class="user-data">200 TB</span></p>
		</div>
	</div>
	<div class="profile-info-right-side">
		<div class="profile-right-block-content">
			<h3 class="profile-content-title">Контактная информация</h3>
				<p class="data-label">Телефон: <span class="user-data">Не указано</span></p>
				<p class="data-label">Веб-сайт: <span class="user-data"><a href="http://vk.com/id323907417" target="_blank">http://vk.com/id323907417</a></span></p>
				<p class="data-label">Skype: <span class="user-data">Не указано</span></p>
				<p class="data-label">ВКонтакте: <span class="user-data">Не указано</span></p>
				<p class="data-label">Facebook: <span class="user-data">Не указано</span></p>
				<p class="data-label">Instagram: <span class="user-data">Не указано</span></p>
				<p class="data-label">YouTube: <span class="user-data">Не указано</span></p>
				<p class="data-label">Twitch: <span class="user-data">Не указано</span></p>
				<p class="data-label">Twitter: <span class="user-data">Не указано</span></p>
				<p class="data-label">Telegram: <span class="user-data">Не указано</span></p>
		</div>
	</div>
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

<div class="news-block">
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
				<input class="btn btn btn-success btn-block" style="margin-top: 10px;" type="submit" data-2fa-generate value="СГЕНЕРИРОВАТЬ КЛЮЧ">
			</div>
			<input class="form-control" id="2fapasswd" style="margin-top: 10px;" type="password" placeholder="Пароль" required="">
			<input class="form-control" id="2facheck" style="margin-top: 10px;" type="text" placeholder="Код" required="">
			<input class="btn btn btn-success btn-block" style="margin-top: 10px;" type="submit" id="send2fa" data-2fa-start value="<?php echo $tmpMes; ?>">
		</div>
		<div class="clear"></div>
		<div class="news_footer"></div>
</div>

<div class="modal fade" id="avatarModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="avatarInfo">Загрузка аватара</h4>
			</div>
			<div class="modal-body" style="max-height: 500px; max-width:580px; overflow: hidden;">
				<center><img id="avatarPreview" src="/upload/avatars/noavatar.png" ></center>
				<input type="hidden" id="x1" name="x1" />
				<input type="hidden" id="y1" name="y1" />
				<input type="hidden" id="w" name="w" />
				<input type="hidden" id="h" name="h" />
			</div>
			<div class="modal-footer">
				<label class="btn btn-default">Загрузить <input id="uploadAvatar" type="file" name="test" style="display: none;"></label>
				<button data-upload-avatar type="button" class="btn btn-default">Отправить</button>
			</div>
		</div>
	</div>
</div>

<?require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
