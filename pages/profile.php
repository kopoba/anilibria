<?php
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


    Заливка аватарки:
    Славливаем POST['upload'] по нажатию кнопки и запускаем функцию cropandupload
    из файла image_upload.

    Нужно пофиксить баг -> при выборе аватарки в профиле, появляется предпросмотр + crop
    Кропу выставляются координаты $(image).imgAreaSelect

*/

require_once($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/private/header.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/private/image_upload.php');
$profile = show_profile();
if(isset($_POST['upload'])) {
    cropandupload();
}

if($profile['err']){
    echo "<div id=\"error\" style=\"display: block; text-align: center;\">{$profile['mes']}</div>";
}else{

    echo "<p>
			<b>ID:</b><span>&nbsp;{$profile['mes']['id']}</span><br/>
			<b>Login:</b><span>&nbsp;{$profile['mes']['login']}</span><br/>
			<b>Email:</b><span>&nbsp;{$profile['mes']['mail']}</span><br/>
			<b>Access level:</b><span>&nbsp;{$profile['mes']['access']}</span>
		</p>";
}

?>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
