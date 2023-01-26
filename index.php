<?php
require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

$var['description'] = 'Смотреть аниме онлайн в любимой озучке.';
$var['page'] = 'main';

require($_SERVER['DOCUMENT_ROOT'].'/private/header.php');
?>

<div style="margin-top: 10px;">
	<style>
		.youtubeTable {
			width: 880px;
		}	
		.youtubeTable tr td:nth-child(1){
			text-align:left;
		}
		.youtubeTable tr td:nth-child(2) {
			text-align:right;
		}
		.youtubeTable tr:not(:first-child) td {
			padding-top: 10px;
		}
		.youtubeTable img {
            opacity:0.8;
            width: 435px;
            object-fit: cover;
            border-radius: 4px;
        }
		.youtubeTable img:hover { opacity:1; }



	</style>
	<table class="youtubeTable">
		<tbody>
			<?php echo youtubeShow(); ?>
		</tbody>
	</table>
</div>
<?php require($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');
