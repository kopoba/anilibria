<?php
require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

$var['title'] = 'Ссылки';

$var['page'] = 'links';

require($_SERVER['DOCUMENT_ROOT'].'/private/header.php');
?>

<div class="news-block">
	<div class="news-body">			
		<a href="https://discordapp.com/invite/anilibria"><img src="/img/link1.png"></a>
		<a href="https://www.youtube.com/channel/UCuF8ghQWaa7K-28llm-K3Zg"><img src="/img/link2.png" style="margin-top: 10px;"></a>
		<a href="https://t.me/anilibria_tv"><img src="/img/link3.png" style="margin-top: 10px;"></a>
		<a href="https://vk.com/anilibria"><img src="/img/link4.png" style="margin-top: 10px;"></a>
	</div>
	<div class="clear"></div>
	<div style="margin-top:10px;"></div>
</div>

<div id="vk_comments" style="margin-top: 15px;"></div>

<?php require($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
