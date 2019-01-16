<?php
require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

$var['page'] = 'app';

require($_SERVER['DOCUMENT_ROOT'].'/private/header.php');
?>

<div class="news-block">
	<div>			
		<img src="/img/donate.jpg" width="840" height="216">
		<hr/>
		<center><i>Мы тратим деньги на премии команде проекта.
		Благодаря этому наша озвучка выходит быстрее конкуретнов.</i></center>
		<br/>
		<center><a href="https://www.patreon.com/anilibria">https://www.patreon.com/anilibria</a> - ежемесячный платёж через Патреон! Самый лучший способ поддержать проект.</center>
		<br>
		
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
	<div class="news_footer"></div>
</div>

<!-- Put this script tag to the <head> of your page -->
<script type="text/javascript" src="https://vk.com/js/api/openapi.js?160"></script>

<script type="text/javascript">
  VK.init({apiId: 6820072, color1: 'FFFFFF', color2: '2B587A', color3: '5B7FA6', onlyWidgets: true});
</script>

<!-- Put this div tag to the place, where the Comments block will be -->
<div id="vk_comments" style="margin-top: 15px;"></div>
<script type="text/javascript">
VK.Widgets.Comments("vk_comments", {limit: 5, attach: false});
</script>

<?require($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
