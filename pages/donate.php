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
?>

<div class="news-block">
	<div class="news-body">
		<center><i>Мы тратим деньги на премии команде.
		Благодаря этому наша озвучка выходит быстрее.</i></center>

		<center style="margin-top:12px; margin-bottom:12px;"><a href="https://www.patreon.com/anilibria">https://www.patreon.com/anilibria</a> - ежемесячный платёж через Патреон! Самый лучший способ поддержать проект.</center>
		
		<table class="table table-bordered">
			<tbody>
				<tr>
					<td>QIWI</td>
					<td>+7xxxxxx</td>
				</tr>
				<tr>
					<td>Яндекс деньги</td>
					<td>41001990134497</td>
				</tr>
				<tr>
					<td>Webmoney</td>
					<td>R211016581718, Z720752385996</td>
				</tr>

				<tr>
					<td>Bitcoin</td>
					<td>3CarFNZickTNb1nx2Bgk6VammB8CYCBSJd</td>
				</tr>
			</tbody>
		</table>
	 <iframe src="https://money.yandex.ru/quickpay/shop-widget?writer=seller&amp;targets=%D0%94%D0%BE%D0%B1%D1%80%D0%BE%D0%B2%D0%BE%D0%BB%D1%8C%D0%BD%D0%BE%D0%B5%20%D0%BF%D0%BE%D0%B6%D0%B5%D1%80%D1%82%D0%B2%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D0%B5&amp;targets-hint=&amp;default-sum=100&amp;button-text=14&amp;payment-type-choice=on&amp;mobile-payment-type-choice=on&amp;hint=&amp;successURL=&amp;quickpay=shop&amp;account=41001990134497" width="450" height="220" frameborder="0" allowtransparency="true" scrolling="no"></iframe>
	</div>
	<div class="clear"></div>
	<div style="margin-top:10px;"></div>
</div>

<div id="vk_comments" style="margin-top: 15px;"></div>

<?php require($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
