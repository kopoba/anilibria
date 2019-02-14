<!DOCTYPE html>
<html prefix="og: http://ogp.me/ns#">
	<head>
		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-44944415-2"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());
		  gtag('config', 'UA-44944415-2');
		</script>
		
		<?php echo $var['og']; ?>
		<title><?php echo $var['title']; ?></title>
		<meta charset="UTF-8">
		<meta name="description" content="<?php echo $var['description']; ?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo fileTime('/css/bootstrap.min.css');?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo fileTime('/css/main.css');?>" />
	</head>
	<body>
		<div class="header">
			<img src="/img/28.png">
			<div id="headercontent">
				<img id="logopic" src="/img/logo_new.png" alt="AniLibria логотип" style="width: 213px;">
			</div>
		</div>
		<div class="main">
			<div class="content">
				<div class="contentmenu">
					<ul class="main-navigation">
						<li><a id="activelink0" href="/">ГЛАВНАЯ</a></li>
						<li><a id="activelink1" href="/pages/catalog.php">РЕЛИЗЫ</a></li>
						<li><a id="activelink2" href="/pages/schedule.php">РАСПИСАНИЕ</a></li>
						<li><a id="activelink3" href="/pages/app.php">ПРИЛОЖЕНИЕ</a></li>
						<li><a id="activelink4" href="/pages/team.php">КОМАНДА</a></li>
						<li><a id="activelink5" href="/pages/links.php">ССЫЛКИ</a></li>
						<li><a id="activelink6" href="/pages/donate.php">ПОДДЕРЖАТЬ ПРОЕКТ</a></li>
					</ul>
				</div>
