<?php
require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/minify.php');

$var['page'] = 'app';

require($_SERVER['DOCUMENT_ROOT'].'/private/header.php');
?>


<style>
.day {
    background:rgba(0,0,0, 0.7);
    text-align: center;
    font-size:20px;
    margin: 10px 0 10px 0;
    height: 30px;
    line-height	: 30px;
    border-radius: 2px;
    color: white;
}

a#join-team-link, a#join-team-link:visited {
	font-size:13pt;
	font-family: 'PT Sans', sans-serif;
	font-weight:400;
	display:inline-block;
	background-color:#f04646;
	padding:10px;
	color:#FFF;
	text-decoration:none;
}
</style>

<div class="news-block">
	<div>			
		<img alt="apppic1.jpg" src="/img/apppic1.jpg" title="apppic1.jpg" width="840" height="103">
		<img src="/img/logo.png" width="200" height="200" align="left">
		<div class="day">AniLibria.TV (версия 2.1.7)</div>
		<p style="text-align: right;">
			<i>
				На данный момент (версия 2.0) функционал приложения: просмотр онлайн с возможностью выбрать SD и HD качество (для большинства экранов телефонов будет достаточно качества SD), скачивание торрент.файлов, поиск по жанрам, избранное, просмотр новостей и блогов, просмотр комментариев<br>
				-2.0.1: Исправлен баг с проблемами авторизации через ВК
			</i>
			<br>
			<br>
			<br>
			Новая версия: <a id="join-team-link" href="/upload/app/AniLibria_v2.1.7.apk">Скачать .apk файл</a>
			<br>
		</p>
		<div class="day">Инструкция по установке</div>
		<p style="text-align: center;">
			 1) Скачайте .apk файл, найдите его в папке "downloads" и запустите<br>
			 2) Вы увидите окно с надписью "Установка заблокирована", не пугайтесь, нажмите кнопку "настройки"<br>
			 3) Найдите пункт "Неизвестные источники" и выставьте параметр "разрешить"<br>
			 4) Выберите галочку "Разрешить только эту установку" и нажмите "ок"<br>
			 5) Нажмите установить, когда появится надпись "приложение установлено", нажмите "открыть".
		</p>
		<div class="day">Скриншоты</div>
		<p style="text-align: center;">
			<img alt="001app.jpg" src="/img/001app.jpg" title="001app.jpg" width="230" height="410">&nbsp; <img alt="002app.jpg" src="/img/002app.jpg" title="002app.jpg" width="230" height="410">&nbsp; <img alt="003app.jpg" src="/img/003app.jpg" title="003app.jpg" width="230" height="410">
		</p>
		<hr/>
		Внимание! Приложение работает на Android от версии 4.4. На более старых версиях приложение работать не будет! Свои отзывы о приложении оставляйте в комментариях. Спасибо, что выбираете нас!
	</div>
	<div class="clear"></div>
	<div class="news_footer"></div>
</div>

<!-- Put this script tag to the <head> of your page -->
<script type="text/javascript" src="https://vk.com/js/api/openapi.js?160"></script>

<script type="text/javascript">
  VK.init({apiId: 6820072, color1: 'FFFFFF', color2: '2B587A', color3: '5B7FA6', onlyWidgets: true});
</script>

<!-- Put this div tag to the place, where the Comments block will be -->
<div id="vk_comments" style="margin-top: 15px;"></div>
<script type="text/javascript">
VK.Widgets.Comments("vk_comments", {limit: 5, attach: false});
</script>

<?require($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
