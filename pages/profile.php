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

require_once($_SERVER['DOCUMENT_ROOT'].'/private/header.php');
show_profile();
?>
<?php
if(isset($errorMsg)) {
    echo "<div id=\"error\" style=\"display: block; text-align: center;\">".$errorMsg."</div>";
} else {
    ?>
    <p>
        <b>ID:</b><span>&nbsp;<?php echo $userResult["ID"] ?></span><br/>
        <b>Login:</b><span>&nbsp;<?php echo $userResult["LOGIN"] ?></span><br/>
        <b>Email:</b><span>&nbsp;<?php echo $userResult["EMAIL"] ?></span><br/>
        <b>Access level:</b><span>&nbsp;<?php echo $userResult["ACCESS"] ?></span>
    </p>
    <?php
}
?>

<?require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
