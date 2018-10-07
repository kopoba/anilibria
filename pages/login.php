<?require_once($_SERVER['DOCUMENT_ROOT'].'/private/header.php');?>
<div id="user_log_reg_rec">
    <h1>Авторизация пользователя</h1>
    <div class="input_wrapper">
        <input id="login" class="styled_input" type="text" spellcheck="false" required />
        <label for="login" class="floating_label">Логин</label>
    </div>
    <div class="input_wrapper">
        <input id="passwd" class="styled_input" type="password" required />
        <label for="passwd" class="floating_label">Пароль</label>
    </div>
    <div class="input_wrapper">
        <input id="fa2code" class="styled_input" type="text" spellcheck="false" />
        <label for="fa2code" class="floating_label">2FA Code</label>
    </div>
    <div id="error" style="display: none"></div>
    <input type="submit" data-submit-login value="Вход" />
    <a class="a_button" href="password_recovery.php">Забыли пароль?</a>
    <a class="a_button" href="register.php">Регистрация</a>
    <hr/>
    <h2>Авторизация через внешние сервисы</h2>
    <a class="oauth_links" href="#"><img src="../images/patreon_auth.png" alt="Patreon Auth"/> </a>
    <a class="oauth_links" href="#"><img src="../images/vk_auth.png" alt="VK Auth"/> </a>
</div>
<?require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
