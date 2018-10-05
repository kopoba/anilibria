<?require_once($_SERVER['DOCUMENT_ROOT'].'/private/header.php');?>
<div id="user_log_reg_rec">
    <h1>Регистрация нового пользователя</h1>
    <input type="text" placeholder="Логин" />
    <input type="email" placeholder="E-mail" />
    <div id="hidden_captcha">
        <script src="https://authedmine.com/lib/captcha.min.js" async></script>
        <div class="coinhive-captcha" data-hashes="1024" data-key="CdATg3DejTD3LWWmOMHh4KHUOK2lwESZ">
            <em>Loading Captcha...<br>
                If it doesn't load, please disable Adblock!</em>
        </div>
    </div>
    <input type="submit" value="Регистрация" />
    <a class="a_button" href="login.php">Уже есть аккаунт?</a>
    <hr/>
    <h2>Авторизация через внешние сервисы</h2>
    <a class="oauth_links" href="#"><img src="../images/patreon_auth.png" alt="Patreon Auth"/> </a>
    <a class="oauth_links" href="#"><img src="../images/vk_auth.png" alt="VK Auth"/> </a>
</div>
<?require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>