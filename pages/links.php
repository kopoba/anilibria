<?php
require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

$var['title'] = 'Ссылки';

$var['page'] = 'app';

require($_SERVER['DOCUMENT_ROOT'].'/private/header.php');
?>

<div class="news-block">
	<div>			
		<a href="https://discordapp.com/invite/anilibria"><img src="/img/discord.png" style="margin-top: 10px;"></a>
		<a href="https://www.youtube.com/channel/UCuF8ghQWaa7K-28llm-K3Zg"><img src="/img/youtube.png" style="margin-top: 10px;"></a>
		<a href="https://t.me/anilibria_tv"><img src="/img/telegram.png" style="margin-top: 10px;"></a>
		<a href="https://vk.com/anilibria"><img src="/img/vk.png" style="margin-top: 10px;"></a>
	</div>
	<div class="clear"></div>
	<div class="news_footer"></div>
</div>

<!-- Put this script tag to the <head> of your page -->
<script type="text/javascript" src="https://vk.com/js/api/openapi.js?160"></script>

<script type="text/javascript">
  VK.init({apiId: 6820072, onlyWidgets: true});
</script>

<!-- Put this div tag to the place, where the Comments block will be -->
<div id="vk_comments" style="margin-top: 15px;"></div>
<script type="text/javascript">
VK.Widgets.Comments("vk_comments", {limit: 5, attach: false});
</script>

<?require($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
