<?php
if(!empty($_SESSION['csrf'])){
	$csrf_token = json_encode(csrf_token());
}else{
	$csrf_token = '';
}

function headerAds(){
	global $cache, $var; $result = []; $left = 0;
	$img = urlCDN('/img/29.png');
	$result['ads'] = false;

	$data[] = ['img' => 'fox_legends.jpg', 'left' => '795px', 'top' => '77px', 'height' => '75px', 'width' => '270px', 'url' => '/fox'];
	$data[] = ['img' => 'hero.jpg', 'left' => '26px', 'top' => '150px', 'height' => '62px', 'width' => '200px', 'url' => '/hero'];
	$data[] = ['img' => 'DG.png', 'left' => '720px', 'top' => '70px', 'height' => '70px', 'width' => '260px', 'url' => '/dg'];
	$data[] = ['img' => 'dc.png', 'left' => '887px', 'top' => '70px', 'height' => '75px', 'width' => '225px', 'url' => '/dc'];

	if(checkADS()){
		$cHour = date('G', $var['time']);
		$key = $cache->get('adsCurrent');
		$adsHour = $cache->get('adsCurrentHour');
		if($key === false || $adsHour === false || $adsHour != $cHour){
			if($key === false){
				$key = 0;
			}else{
				$key++;
			}
			if($key > count($data)-1){
				$key = 0;
			}
			//$key = random_int(0, count($data)-1);
			$cache->set('adsCurrent', $key, 3600);
			$cache->set('adsCurrentHour', $cHour, 3600);
		}

		//$key = 1;
		$ads = $data["$key"];

		//$img = '/img/other/a/'.$ads['img'];
		$img = urlCDN('/img/other/a/'.$ads['img']);
		/*if($key == 0 || $key == 3) {
			$img = urlCDN('/img/season/'.$ads['img']);
		}*/
		$left = $ads['left'];
		$top = $ads['top'];
		$height = $ads['height'];
		$width = $ads['width'];
		$result['url'] = $ads['url'];
		$result['ads'] = true;

	} /*else {
		//Удалить когда голосование закончится
		$img = urlCDN('/img/season/autumn2020.png');
		$left = '0';
		$top = '0';
		$height = '215px';
		$width = '1175px';
		$result['url'] = '/season/2020autumn.html';
		$result['ads'] = true;
		//------------------------------------
	}*/

	$css = getTemplate('header');
	$css = str_replace('{img}', $img, $css);
	if(isset($left)) { $css = str_replace('{left}', $left, $css); }
	if(isset($top)) { $css = str_replace('{top}', $top, $css); }
	if(isset($height)) { $css = str_replace('{height}', $height, $css); }
	if(isset($width)) { $css = str_replace('{width}', $width, $css); }

	$result['css'] = $css;
	return $result;
}

$xcss = headerAds();
?>

<!DOCTYPE html>
<html prefix="og: http://ogp.me/ns#">
	<head>
		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id="></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());
		  gtag('config', '<?php global $conf; echo $conf['google_analytics_id']; ?>');
		</script>
		<!-- Yandex.Metrika counter -->
		<script type="text/javascript" >
		   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
		   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
		   (window, document, "script", "https://cdn.jsdelivr.net/npm/yandex-metrica-watch/tag.js", "ym");

		   ym(<?php global $conf;  echo $conf['yandex_metrika_id']; ?>, "init", {
				clickmap:true,
				trackLinks:true,
				accurateTrackBounce:true
		   });
		</script>

		<script async src="https://yandex.ru/ads/system/header-bidding.js"></script>
		<script type="text/javascript" src="https://ads.digitalcaramel.com/js/anilibria.tv.js"></script>
		<script>window.yaContextCb = window.yaContextCb || []</script>
		<script src="https://yandex.ru/ads/system/context.js" async></script>

		<noscript><div><img src="https://" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
		<!-- /Yandex.Metrika counter -->
		<?php echo $var['og']; ?>
		<title><?php echo $var['title']; ?></title>
		<meta charset="UTF-8">
		<meta name="description" content="<?php echo strip_tags($var['description']); ?>" />


        <!-- Recaptcha -->
        <meta name="recaptcha2_site_key" content="<?php echo $conf['recaptcha2_public']; ?>" />
        <meta name="recaptcha3_site_key" content="<?php echo $conf['recaptcha_public']; ?>" />

		<link rel="stylesheet" type="text/css" href="<?php echo urlCDN(fileTime('/css/bootstrap.min.css'));?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo '/css/main.css?hash=' . md5_file('/var/www/html/css/main.css');?>" />
		<style><?php echo $xcss['css']; ?></style>
		<link rel="apple-touch-icon" sizes="180x180" href="/img/favicons/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/img/favicons/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/img/favicons/favicon-16x16.png">
		<link rel="manifest" href="/img/favicons/site.webmanifest">
		<link rel="mask-icon" href="/img/favicons/safari-pinned-tab.svg" color="#c40809">
		<link rel="shortcut icon" href="/favicon.ico">
		<link rel="icon" href="/favicon.svg" type="image/svg+xml" sizes="any">
		<meta name="msapplication-TileColor" content="#c40809">
		<meta name="msapplication-config" content="/img/favicons/browserconfig.xml">
		<meta name="theme-color" content="#c40809">
	</head>
	<body>
		<input type="hidden" id="csrf_token" value='<?php echo $csrf_token; ?>'>
		<!-- AdguardTeam and AdBlock (dimisa) , please block any ads you want. -->
		<div id="headercontent"></div>
		<div class="link">
		<?php
			if($xcss['ads']){
				echo '<a href="'.$xcss['url'].'" target="_blank" data-toggle="tooltip" data-placement="right" title="Вы можете отключить рекламу в личном кабинете"></a>';
			}
		?>
		</div>
		<div class="header">
			<?php
				if($xcss['ads']){
					echo "<div class='headercontent'></div>";
				}
			?>
		</div>
		<div class="main">
			<div class="content">
				<div class="contentmenu">
					<ul class="main-navigation">
						<li><a id="activelink0" href="/">ГЛАВНАЯ</a></li>
						<li><a id="activelink1" href="/pages/catalog.php">РЕЛИЗЫ</a></li>
						<li><a id="activelink2" href="/pages/schedule.php">РАСПИСАНИЕ</a></li>
						<li><a data-random-release id="activelink3" href="/public/random.php">СЛУЧАЙНОЕ</a></li>
						<li><a id="activelink4" href="https://anilibria.app" target="_blank">ПРИЛОЖЕНИЕ</a></li>
						<li><a id="activelink5" href="/pages/team.php">КОМАНДА</a></li>
						<li><a id="activelink6" href="/pages/donate.php">ПОДДЕРЖАТЬ ПРОЕКТ</a></li>
					</ul>
				</div>


<!--<div class="alert alert-warning" role="alert" style="margin-top: 10px; margin-bottom: 0px; font-size: 12.2pt; color: #000000;">
	Внимание! На сайте идёт <a href="https://www.anilibria.tv/season/2220winter.html" target="_blank">голосование</a> за неонгоинги на озвучку! ТОП-5 мы возьмём на озвучку!</br> А ещё, <a href="https://www.anilibria.tv/season/2021winter.html" target="_blank">голосование</a> за самые ожидаемые аниме зимы 2021!
</div>-->

<a href="https://www.anilibria.tv/pages/donate.php" target="_blank">
	<img src="/img/support_al.png" style="width: 880px; height: 100px; margin-top: 10px;" alt="Support Anilibria" />
</a>

<!--<a href="/season/2023winter.html" target="_blank">
    <img src="/img/season/winter2023.jpg" style="width: 880px; height: 100px; margin-top: 10px;" alt="Vote anime season winter 2023 AniLibria" />
</a>-->