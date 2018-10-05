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
    <h1>Восстановление пароля</h1>
    <input id="email" type="email" placeholder="E-mail" />
    <div id="hidden_captcha">
        <script src="https://authedmine.com/lib/captcha.min.js" async></script>
        <script src="https://www.google.com/recaptcha/api.js?render=<?php echo $conf['recaptcha_public']; ?>"></script>
        <div class="coinhive-captcha" style="display: none" data-hashes="1024" data-key="<?php echo $conf['coinhive_public']; ?>">
            <em>Loading Captcha...<br>
                If it doesn't load, please disable Adblock!</em>
        </div>
    </div>
    <div id="error" style="display: none"></div>
    <input type="submit" data-submit-passwdrecovery value="Отправить" />
    <a class="a_button" href="login.php">Авторизация</a>
</div>
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
