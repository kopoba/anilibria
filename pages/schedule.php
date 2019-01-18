<?php
require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

$var['title'] = 'Новое аниме, онгоинги';

$var['page'] = 'schedule';

require($_SERVER['DOCUMENT_ROOT'].'/private/header.php');
?>

<style>
.day {
    background: #4a4a4a;
    text-align: center;
    margin: 10px 0 10px 0;
    height: 30px;
    font-size: 20px;
    line-height: 30px;
    border-radius: 7px;
    color: white;
}

.test {
	border-collapse: separate;
    border-spacing: 8px;
}

.goodcell .info[title] {
    background: #4a4a4a;
}

.goodcell img {
    border: 3px solid #4a4a4a;
}

.goodcell img:hover {
    border: 3px solid #e04e4e;
}
</style>

<div class="news-block">
	<div>			
		<?php echo showSchedule(); ?>
	</div>
	<div class="clear"></div>
	<div style="margin-top:10px;"></div>
</div>

<?php require($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
