<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/minify.php');

//updateYoutube();
//var_dump($user);

updateYoutubeStat();

require_once($_SERVER['DOCUMENT_ROOT'].'/private/header.php');

echo youtubeShow();
?>

<div class="clear"></div>
	<div class="loadmore">
	<a href="#">ЗАГРУЗИТЬ ЕЩЕ</a>
</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
