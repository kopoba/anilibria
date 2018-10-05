<?require_once($_SERVER['DOCUMENT_ROOT'].'/private/header.php');?>
<div id="user_log_reg_rec">
    <h1>Авторизация пользователя</h1>
    <input id="login" type="text" placeholder="Логин" />
    <input id="passwd" type="password" placeholder="Пароль" />
    <input id="fa2code" type="text" placeholder="FA2code" />
    <div id="error" style="display: none">ddfgdfg</div>
    <input type="submit" data-submit-login value="Вход" />
    <a class="a_button" href="password_recovery.php">Забыли пароль?</a>
    <a class="a_button" href="register.php">Регистрация</a>
    <hr/>
    <h2>Авторизация через внешние сервисы</h2>
    <a class="oauth_links" href="#"><img src="../images/patreon_auth.png" alt="Patreon Auth"/> </a>
    <a class="oauth_links" href="#"><img src="../images/vk_auth.png" alt="VK Auth"/> </a>
</div>
<?require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
