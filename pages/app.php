<?php
require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

$var['title'] = 'Мобильное приложение';
$var['page'] = 'app';

$version = $var['app_version'];
$updateSrc = file_get_contents($_SERVER['DOCUMENT_ROOT']."/private/app_updates/version_$version.txt");
$updateJson = json_decode($updateSrc, true);
$versionName = $updateJson['update']['version_name'];
$appLink = $updateJson['update']['links'][0]['url'];

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
    border-radius: 4px;
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
	border-radius: 4px;
}

.andriodLogo {
	width: 200px;
	height: 200px;
	position: absolute;
	background: url(/img/android.png);
	background-repeat: no-repeat;
	background-size: cover;
}
</style>




<div class="news-block">
	<div class="news-body">

<div class="day">Приложение на ПК</div>
		<p style="text-align: left;">
             - Приложение для Windows 10. <a href="https://anilibria.github.io/anilibria-win/">Скачать</a>.<br/>
             - Приложение для MacOS, Windows, Linux. <a href="https://github.com/anilibria/anilibria-winmaclinux/blob/master/README.md" target="_blank">Скачать</a>.
		</p>
	
	<img src="/img/004app.jpg" width="840" height="515" style="border-radius: 4px;">
		<div class="day">Приложение на Android</div>
		<p style="text-align: left;">
			 - <a href="<?php echo $appLink; ?>">Скачайте .apk файл</a>, найдите его в папке <u>downloads</u> и запустите.<br>
			 - Минимальная версия Android 4.4 KitKat<br> 
			 - Вы увидите окно с надписью <u>Установка заблокирована</u>, не пугайтесь, нажмите кнопку <u>настройки</u>.<br>
			 - Найдите пункт <u>Неизвестные источники</u> и выставьте параметр <u>разрешить</u>.<br>
			 - Выберите галочку <u>Разрешить только эту установку</u> и нажмите <u>ок</u>.<br>
			 - Нажмите установить, когда появится надпись <u>приложение установлено</u>, нажмите <u>открыть</u>.<br/>
			 - Исходный код доступен на <a href="https://github.com/anilibria/anilibria-app" target="_blank">github</a>.
		</p>
		<p style="text-align: center;">
			<img src="/upload/app/app06.jpg" width="230" style="float:left;padding:1px;border:1px solid #d3d3d3;background-color:##fff;border-radius: 4px;">
			<img src="/upload/app/app07.jpg" width="230" style="padding:1px;border:1px solid #d3d3d3;background-color:##fff;border-radius: 4px;">
			<img src="/upload/app/app05.jpg" width="230" style="float:right;padding:1px;border:1px solid #d3d3d3;background-color:##fff;border-radius: 4px;">
		</p>
	</div>
	
	
	
	<div class="clear"></div>
	<div style="margin-top:10px;"></div>
</div>

<div id="vk_comments" style="margin-top: 10px;"></div>

<?php require($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
