<?require_once($_SERVER['DOCUMENT_ROOT'].'/private/header.php');?>
<div id="user_log_reg_rec">
    <h1>Восстановление пароля</h1>
    <input type="email" placeholder="E-mail" />
    <div id="hidden_captcha">
        <script src="https://authedmine.com/lib/captcha.min.js" async></script>
        <div class="coinhive-captcha" data-hashes="1024" data-key="CdATg3DejTD3LWWmOMHh4KHUOK2lwESZ">
            <em>Loading Captcha...<br>
                If it doesn't load, please disable Adblock!</em>
        </div>
    </div>
    <input type="submit" value="Отправить" />
    <a class="a_button" href="#">Авторизация</a>
</div>
<?require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>