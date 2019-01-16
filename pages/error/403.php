<?php
require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

$var['page'] = '403';
header('HTTP/1.0 403 Forbidden');

require($_SERVER['DOCUMENT_ROOT'].'/private/header.php');
?>

<div class="news-block">
	<div>
		<center>			
		<img src="/img/403.png">
		</center>
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
VK.Widgets.Comments("vk_comments", {limit: 5, pageUrl: "/pages/error/403.php", attach: false});
</script>

<?require($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
