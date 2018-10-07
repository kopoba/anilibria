<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/header.php');
?>
<div id="user_log_reg_rec">
    <h1>Регистрация нового пользователя</h1>
    <div class="input_wrapper">
        <input id="login" class="styled_input" type="text" spellcheck="false" />
        <label for="login" class="floating_label">Логин</label>
    </div>
    <div class="input_wrapper">
        <input id="email" class="styled_input" type="email" spellcheck="false" />
        <label for="email" class="floating_label">Email</label>
    </div>
    <div id="hidden_captcha">
        <script src="https://authedmine.com/lib/captcha.min.js" async></script>
        <script src="https://www.google.com/recaptcha/api.js?render=<?php echo $conf['recaptcha_public']; ?>"></script>
        <div class="coinhive-captcha" style="display: none" data-hashes="1024" data-key="<?php echo $conf['coinhive_public']; ?>">
            <em>Loading Captcha...<br>
                If it doesn't load, please disable Adblock!</em>
        </div>
    </div>
    <div id="error" style="display: none"></div>
    <input type="submit" data-submit-register value="Отправить" />
    <a class="a_button" href="login.php">Уже есть аккаунт?</a>
    <hr/>
    <h2>Авторизация через внешние сервисы</h2>
    <a class="oauth_links" href="#"><img src="../images/patreon_auth.png" alt="Patreon Auth"/> </a>
    <a class="oauth_links" href="#"><img src="../images/vk_auth.png" alt="VK Auth"/> </a>
</div>
<?require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
