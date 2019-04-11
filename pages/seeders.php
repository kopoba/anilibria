<?php
require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');


$var['title'] = 'Топ сидеры';
$var['page'] = 'seeders';

require($_SERVER['DOCUMENT_ROOT'].'/private/header.php');
?>

<style>
	table th, td{ text-align: center; }
	table th { font-weight: 400 !important; }
	
	/* https://www.schemecolor.com/olympic-medals-color-scheme.php */
	tr:nth-child(2) {
		color: #D6AF36;
		font-weight: bold;
	}
	
	tr:nth-child(3) {
		color: #A7A7AD;
		font-weight: bold;
	}
	
	tr:nth-child(4) {
		color: #A77044;
		font-weight: bold;
	}
	
	.table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th, .table>thead>tr>td, .table>tbody>tr>td, .table>tfoot>tr>td {
		padding: 6px !important;
	}
	
</style>
<div class="news-block">
		
		<div>
			<center>
			<table class="table table-bordered" style="width: 800px; margin-top:25px;">
			<tbody>
				<tr>
					<th></th>
					<th>Пользователь</th> 
					<th>Раздал</th>
					<th>Скачал</th>
				</tr>
				<?php echo seedersRating(); ?>
			</tbody>
		</table>
		</center>
		
		</div>
		<div class="clear"></div>
		<div style="margin-top:10px;"></div>
</div>

<div id="vk_comments" style="margin-top: 10px;"></div>

<?php require($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
