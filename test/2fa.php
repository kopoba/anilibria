<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

if(!$user){
	_message('Unauthorized user', 'error');
}

if(!empty($user['2fa'])){
	$tmpMes = 'Выключить 2FA';
}else{
	$tmpMes = 'Включить 2FA';
}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>AniLibria.TV - Так звучит аниме! Озвучка аниме для домашнего просмотра.</title>
		<meta name="keywords" content="Анилибрия" />
		<meta name="description" content="Анилибрия - смотреть аниме онлайн, торрент-трекер" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="/css/main.css">
		<link rel="stylesheet" type="text/css" href="/css/jquery.bxslider.css">
		<link rel="stylesheet" type="text/css" href="/css/alertify.core.css" />
		<link rel="stylesheet" type="text/css" href="/css/alertify.bootstrap.css" />
		<script src="https://authedmine.com/lib/captcha.min.js" async></script>
		<script src="/js/jquery.min.js"></script>
		<script src="/js/popper.min.js"></script>
		<script src="/js/bootstrap.min.js"></script>
		<script src="/js/jquery.bxslider.js"></script>
		<script src="/js/alertify.js"></script>
		<script src="/js/main.js"></script>
		<style>
			.center { position: absolute; top: 50%; left: 50%; transform: translateX(-50%) translateY(-50%); }
		</style>
		<script>
			$(document).on("click", "[data-2fa-generate]", function(e) {
				var _this = $(this);
				_this.blur();
				e.preventDefault();
				$.post("//"+document.domain+"/public/2fa.php", {do: 'gen'}, function(json){
					data = JSON.parse(json);
					if(data.err == 'ok'){
						_this.hide();
						$("#2fakey").html('<center>'+data.mes+'</center>');
						$("#2fainfo").show();
					}else{
						alertify.error(data.mes);
					}
				});
			});
			$(document).on("click", "[data-2fa-start]", function(e) {
				$(this).blur();
				e.preventDefault();
				
				code = $('input[id=f2acode]').val();
				recode = $('input[id=ref2acode]').val();
				passwd = $('input[id=passwd]').val();
				
				$.post("//"+document.domain+"/public/2fa.php", {do: 'save', code: code, recode: recode, passwd: passwd}, function(json){
					data = JSON.parse(json);
					if(data.err == 'ok'){
						alertify.success('Done');
						if(data.mes == '2FA activated'){
							$("#send2fa").val('Выключить 2FA');
						}else{
							$("#send2fa").val('Включить 2FA');
						}
					}else{
						alertify.error(data.mes);
					}
				});
			});
		</script>
	</head>
	<body>
		<div class="center">
			<div class="content-text">
							<div class="page-title">Двухфакторная аутентификация</div>
							<div class="row">
								<div class="col-lg-14">
									<div class="col-lg-12">
										При использовании этой функции для входа в аккаунт необходимо вводить не только пароль, но и код, сгенерированный приложением. 
										Установите на мобильный телефон Google Authenticator [<a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=ru">android</a>] [<a href="https://itunes.apple.com/ru/app/google-authenticator/id388497605?mt=8">ios</a>].<br/>									
										<hr/>
										<input data-2fa-generate="" class="btn btn-sm btn-danger btn-block" style="margin-top: 2px;" value="Сгенерировать ключ" type="submit">
										<div id="2fakey"></div>
										<div id="2fainfo">
											<hr/>
											<input class="form-control" value="" id="f2acode" style="margin-top: 2px;" required="" placeholder="Secret key" type="text">
											<input class="form-control" value="" id="ref2acode" style="margin-top: 2px;" required="" placeholder="Secret key (repeat)" type="text">
											<input class="form-control" value="" id="passwd" style="margin-top: 2px;" required="" placeholder="Password" type="password">
											<input data-2fa-start="" id="send2fa" class="btn btn-sm btn-danger btn-block" style="margin-top: 2px;" value="<?php echo $tmpMes; ?>" type="submit">
										</div>
									</div>
								</div>
							</div>
		</div>
	</body>
</html>
