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

	$data[] = ['img' => 'dityapogodi', 'left' => '0px', 'top' => '0px', 'url' => 'https://vk.cc/auVjXB'];
	$data[] = ['img' => 'sao', 'left' => '420px', 'top' => '110px', 'url' => '/sao'];
	$data[] = ['img' => 'dityapogodi', 'left' => '0px', 'top' => '0px', 'url' => 'https://vk.cc/auVjXB'];
	$data[] = ['img' => 'pf', 'left' => '425px', 'top' => '125px', 'url' => '/pf'];
	$data[] = ['img' => 'dityapogodi', 'left' => '0px', 'top' => '0px', 'url' => 'https://vk.cc/auVjXB'];
	$data[] = ['img' => 'naruto', 'left' => '650px', 'top' => '150px', 'url' => '/naruto'];
	//$data[] = ['img' => 'bdo', 'left' => '0px', 'top' => '135px', 'url' => '/bdo'];
	//$data[] = ['img' => 'bns', 'left' => '0px', 'top' => '135px', 'url' => '/bns'];

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
		
		//$key = 2;
		$ads = $data["$key"];
		
		$img = urlCDN('/img/other/a/'.$ads['img'].'.jpg');
		$height = '60px';
		$width = '235px';
		if($key == 0 || $key == 2 || $key == 4) {
			$img = urlCDN('/img/other/a/'.$ads['img'].'.png');
			$height = '215px';
			$width = '1175px';
		}
		$left = $ads['left'];
		$top = $ads['top'];
		$result['url'] = $ads['url'];
		$result['ads'] = true;
		
	}
	/*Удалить когда голосование закончится*/
	/*$img = urlCDN('/img/season/spring2020.png');
	$result['url'] = '/season/2020spring.html';
	$result['ads'] = true;*/
	/*------------------------------------*/
	
	$css = getTemplate('header');
	$css = str_replace('{img}', $img, $css);
	$css = str_replace('{left}', $left, $css);
	$css = str_replace('{top}', $top, $css);
	$css = str_replace('{height}', $height, $css);
	$css = str_replace('{width}', $width, $css);
	
	$result['css'] = $css;
	return $result;
}

$xcss = headerAds();
?>

<!DOCTYPE html>
<html prefix="og: http://ogp.me/ns#">
	<head>
		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-137180052-1"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());
		  gtag('config', 'UA-159821219-2');
		</script>
		<!-- Yandex.Metrika counter -->
		<script type="text/javascript" >
		   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
		   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
		   (window, document, "script", "https://cdn.jsdelivr.net/npm/yandex-metrica-watch/tag.js", "ym");

		   ym(23688205, "init", {
				clickmap:true,
				trackLinks:true,
				accurateTrackBounce:true
		   });
		</script>
		<noscript><div><img src="https://mc.yandex.ru/watch/23688205" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
		<!-- /Yandex.Metrika counter -->
		<?php echo $var['og']; ?>
		<title><?php echo $var['title']; ?></title>
		<meta charset="UTF-8">
		<meta name="description" content="<?php echo strip_tags($var['description']); ?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo urlCDN(fileTime('/css/bootstrap.min.css'));?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo urlCDN(fileTime('/css/main.css'));?>" />
		<style><?php echo $xcss['css']; ?></style>
	</head>
	<body>
		<input type="hidden" id="csrf_token" value='<?php echo $csrf_token; ?>'>
		<!-- AdguardTeam and AdBlock (dimisa) , please block any ads you want. -->
		<div id="headercontent"></div>
		<div class="link">
		<?php
			if($xcss['ads']){
				echo '<a href="'.$xcss['url'].'" data-toggle="tooltip" data-placement="right" title="Вы можете отключить рекламу в личном кабинете"></a>';
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
						<li><a id="activelink4" href="/pages/app.php">ПРИЛОЖЕНИЕ</a></li>
						<li><a id="activelink5" href="/pages/team.php">КОМАНДА</a></li>
						<li><a id="activelink6" href="/pages/donate.php">ПОДДЕРЖАТЬ ПРОЕКТ</a></li>
					</ul>
				</div>
				
				<!-- <a href="http://animepik.org" target="_blank" ><img src="/img/animepik1.jpg" width="880px" height="107px" style="margin-top: 10px;" alt="animepik.org" /></a> -->





<div class="alert alert-warning" role="alert" style="margin-top: 10px; margin-bottom: 0px; font-size: 12.2pt; color: #000000;">
	Недоступное на нашем сайте можно найти на <a href="http://animepik.org" target="_blank">АнимеПик</a> и <a href="https://dark-libria.it/" target="_blank">Тёмной Либрии</a>. Спасибо, что вы с нами!
</div>
