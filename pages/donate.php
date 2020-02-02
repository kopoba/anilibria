<?php
require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

$var['title'] = 'Поддержать проект';
$var['page'] = 'donate';

require($_SERVER['DOCUMENT_ROOT'].'/private/header.php');

//<div class="xplayer" id="anilibriaPlayer" style="display: block; margin-bottom: 15px; border-top-left-radius: 4px; border-top-right-radius: 4px;"></div>
?>

<div class="news-block">
	<div class="news-body">

	<div id="yandexMoney" style="float:left;"></div>
	<table class="table table-bordered" style="width: 450px; float:right; margin-top:0px; margin-left:30px; margin-bottom: 10px;">
			<tbody>
				<tr>
					<td>QIWI</td>
					<td><a href="https://my.qiwi.com/Aleksandr-PeUlISbRs_" target="_blank">https://my.qiwi.com/Aleksandr-PeUlISbRs_</a></td>
				</tr>
				<tr>
					<td>Яндекс деньги</td>
					<td>41001990134497</td>
				</tr>
				<tr>
					<td>Webmoney</td>
					<td>Больше не поддерживается</td>
				</tr>

				<tr>
					<td>PayPal</td>
					<td><a href="https://www.paypal.me/anilibria" target="_blank">https://www.paypal.me/anilibria</a></td>
				</tr>
			</tbody>
		</table>
		
		<div style="width: 450px; float:right;">
			
			<center style="margin-top:0px;"><a href="https://www.patreon.com/anilibria" target="_blank">https://www.patreon.com/anilibria</a> - ежемесячное добровольное пожертвование!</center>
		
		</div>
		
	</div>
	<div class="clear"></div>
	<div style="margin-top:10px;"></div>
</div>

<div id="vk_comments" style="margin-top: 10px;"></div>

<?php require($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
