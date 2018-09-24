<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
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
			$(document).on("click", "[data-register-open]", function(e) {
				$(this).blur();
				e.preventDefault();
				mail = $('input[id=regEmail]').val();
				coinhive = $('input[name=coinhive-captcha-token]').val();
				$.post("//"+document.domain+"/public/password_recovery.php", { 'mail': mail, 'coinhive-captcha-token': coinhive }, function(json){
					console.log(json);
				}); 
			});
		</script>
	</head>
	<body>
		<div class="center">
			<input type="text" class="form-control" id="regEmail" placeholder="Login">
			<hr/>
			<div class="coinhive-captcha" data-hashes="1024" data-key="CdATg3DejTD3LWWmOMHh4KHUOK2lwESZ">
				<em>Loading Captcha...<br>
				If it doesn't load, please disable Adblock!</em>
			</div>
			<hr/>
			<button type="submit" data-register-open class="btn btn-success">Submit</button>
		</div>
	</body>
</html>
