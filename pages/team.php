<?php
require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

$var['title'] = 'Команда проекта';
$var['page'] = 'app';

require($_SERVER['DOCUMENT_ROOT'].'/private/header.php');
?>

<div class="news-block">
	<div class="news-body">
		<center>
			<img src="/img/team.png">
		</center>
		<center>
		<p style="margin-top: 8px;">
			AniLibria — некоммерческий проект по озвучиванию и адаптации зарубежных сериалов, мультфильмов и аниме, аниме-обзорам, видеоблогам, рецензиям.
			Нашей особенностью является стабильная и качественная работа над тем контентом, который мы предоставляем пользователям.
			Нашей целью является достижение большой популярности ресурса anilibria.tv, чтобы впоследствии начать озвучивать аниме в дубляже по лицензии от японцев и/или выпускать на постоянной основе собственные мультфильмы в стиле, максимально приближенном к аниме.<br>
		</p>
		
		Мы ищем таланты. <a href="/pages/request.php">Подать заявку</a> в команду.
		</center>
		<hr/>
		<p style="text-align: center;">
			<i><span style="color: #339966;"><span style="font-size: 16pt;">Войсеры / </span></span> <span style="color: #800000;"><span style="font-size: 16pt;">Технари / </span></span><span style="color: #ff6600;"><span style="font-size: 16pt;">Сабберы / </span></span> <span style="color: #800080;"><span style="font-size: 16pt;">Резерв / </span></span> <span style="color: #33cccc;"><span style="font-size: 16pt;">Дизайнеры/ </span></span> <span style="color: #f2d20c;"><span style="font-size: 16pt;">PR-команда</span></span></i>
		</p>
		<br/>

		<ul>
			<li><span style="color: #ff0000;"><a href="https://vk.com/lupintv" target="_blank"><span style="color: #00a650;">Lupin</span></a></span> - глава проекта, босс, войсер</li>
		</ul>
		<p style="text-align: center;">
			<u> <b>1 Лига (СОВЕТ)</b></u>
		</p>
		<ul>
			<li><span style="color: #ff0000;"><a href="https://vk.com/silvanka" target="_blank"><span style="color: #00a650;">Silv</span></a></span> - войсер</li>
			<li><span style="color: #ff0000;"><a href="https://vk.com/flegontova1993" target="_blank"><span style="color: #00a650;">Itashi</span></a></span> - руководитель PR-команды, войсер</li>
			<li><span style="color: #339966;">Dejz</span> - руководитель академии войсеров, войсер</li>
			<li><span style="color: #800000;">Blaze</span> - руководитель технарей, технарь</li>
			<li><span style="color: #339966;">Sharon</span> - руководитель команды войсеров</li>
			<li><span style="color: #f7941d;">Timo</span> - руководитель команды сабберов</li>
		</ul>
		<p style="text-align: center;">
			<u><b>2 Лига (ВЫСШАЯ)</b></u>
		</p>
		<ul>
			<li><span style="color: #800000;"><a href="https://vk.com/id26183002" target="_blank">Aizawa</a></span> - руководитель подкоманды технарей (сидеры)</li>
			<li><span style="color: #800000;">Alkhorus </span> - технарь / куратор академии таймеров / помощник рук. технарей</li>
			<li><span style="color: #800000;">Hikariya</span>&nbsp;- технарь / помощник рук. сабберов (оформитель) / дизайнер</li>
			<li><span style="color: #339966;">Aemi</span> - войсер</li>
			<li><span style="color: #339966;">Amikiri</span> - войсер</li>
			<li><span style="color: #339966;">Anzen</span> - руководитель команды дизайнеров, войсер</li>
			<li><span style="color: #f7941d;">Falk</span> - переводчик, руководитель академии сабберов</li>
		</ul>
		<p style="text-align: center;">
			<u><b>3 Лига (МАСТЕРА)</b></u>
		</p>
		<ul>
			<li><span style="color: #339966;">Ados</span> - войсер</li>
			<li><span style="color: #339966;">Cleo-chan</span> - войсер</li>
			<li><span style="color: #339966;">December</span> - войсер</li>
			<li><span style="color: #339966;">WhiteCroW</span> - войсер</li>
			<li><span style="color: #339966;">HectoR</span> - войсер</li>
			<li><span style="color: #339966;">MyAska</span> - войсер</li>
			<li><span style="color: #339966;">Malevich</span>&nbsp;- войсер</li>
			<li><span style="color: #800000;">Ninja-san</span> - технарь</li>
			<li><span style="color: #800000;">Quin</span> - технарь</li>
			<li><span style="color: #800000;">Ghost</span> - технарь</li>
			<li><span style="color: #800000;">Пиратехник</span> - технарь</li>
			<li><span style="color: #800000;">Hidan</span> - технарь</li>
			<li><span style="color: #800000;">DarkKnight</span> - технарь </li>
			<li><span style="color: #800000;">Pomidorchik</span>&nbsp;- технарь</li>
			<li><span style="color: #800000;">WhiteCat</span> - технарь </li>
			<li><span style="color: #800000;">Kosaka</span> - технарь </li>
			<li><span style="color: #800000;">Malinero</span> - технарь</li>   
			<li><span style="color: #800000;">Akkakken</span> - технарь / оформитель</li>
			<li><span style="color: #800000;">Luchano</span> - технарь</li>
			<li><span style="color: #800000;">Basegame</span> - технарь (заливщик)</li>
			<li><span style="color: #f7941d;">N.O.</span> - переводчик</li>
			<li><span style="color: #f7941d;">Keitaro</span> - переводчик / оформитель </li>
			<li><span style="color: #f7941d;">ElViS</span> - оформитель</li>
			<li><span style="color: #f7941d;">Sсarlett</span> - оформитель</li>
			<li><span style="color: #f7941d;">Psycho</span> -  переводчик</li>
			<li><span style="color: #f7941d;">Aiso</span> - переводчик</li>
			<li><span style="color: #f7941d;">Flerion</span> - переводчик / оформитель</li>
			<li><span style="color: #f2d20c;">Hant</span> - PR-команда</li>
			<li><span style="color: #f2d20c;">SineD</span> - PR-команда</li>
			<li><span style="color: #f2d20c;">Maximka</span> - PR-команда</li>
		</ul>
		<p style="text-align: center;">
			<u><b>4 Лига (ОСНОВА)</b></u>
		</p>
		<ul>
			<li><span style="color: #339966;">Kanade Eu</span> - войсер</li>
			<li><span style="color: #339966;">Gomer</span> - войсер</li>
			<li><span style="color: #339966;">Arato</span> - войсер</li>
			<li><span style="color: #339966;">Rexus</span> - войсер</li>
			<li><span style="color: #339966;">Derenn</span>&nbsp;- войсер </li>
			<li><span style="color: #339966;">Narea</span>&nbsp;- войсер</li>
			<li><span style="color: #339966;">OkanaTsoy</span>&nbsp;- войсер</li>
			<li><span style="color: #339966;">Hekomi</span> - войсер</li>
			<li><span style="color: #800000;">Psychosocial76</span> - технарь</li>
			<li><span style="color: #800000;">EncoR</span> - технарь</li>
			<li><span style="color: #800000;">EliteNoob </span> - технарь</li>
			<li><span style="color: #800000;">Karfosobos</span> - технарь</li>
			<li><span style="color: #800000;">Dr.One</span> - технарь</li>
			<li><span style="color: #800000;">Sekai</span> - технарь (заливщик)</li>
			<li><span style="color: #f7941d;">BONN</span> -  оформитель </li>
			<li><span style="color: #f7941d;">Nomi</span> - переводчик</li>
			<li><span style="color: #f7941d;">Gamilton</span> - переводчик</li>
			<li><span style="color: #f7941d;">Suisei</span> - переводчик / оформитель</li>
			<li><span style="color: #f7941d;">DiaZone</span> - оформитель</li> 
			<li><span style="color: #f7941d;">TiimeIrre</span> - переводчик</li>
			<li><span style="color: #f7941d;">Smallow</span> - переводчик</li>  
			<li><span style="color: #f7941d;">RinRin</span> - переводчик</li>  
			<li><span style="color: #f7941d;">Nutsy</span> - переводчик</li>
			<li><span style="color: #f7941d;">Narvion</span> - оформитель</li>
			<li><span style="color: #f7941d;">The Magus</span> - переводчик / оформитель</li>
			<li><span style="color: #33cccc;">Kell</span> - дизайнер</li>
			<li><span style="color: #33cccc;">Muvik</span> - дизайнер</li>
			<li><span style="color: #33cccc;">Toppi</span> - дизайнер</li>
		</ul>
		<p style="text-align: center;">
			<u><b>5 Лига (ЛИГА ЗАПАСА)</b></u>
		</p>
		<ul>
			<li><span style="color: #339966;">Nuts</span> - войсер (служит в армии)</li>
			<li><span style="color: #339966;">Kari</span> - войсер (в отпуске)</li>
			<li><span style="color: #339966;">Myuk</span> - войсер (в отпуске)</li>
			<li><span style="color: #f7941d;">LolWhat</span> - переводчик</li>
			<li><span style="color: #f7941d;">Constantum</span> - переводчик<br>
			</li><li><span style="color: #f7941d;">Vurdalak121</span> - переводчик</li>
			<li><span style="color: #f7941d;">Inari</span> - переводчик</li>
			<li><span style="color: #f7941d;">Skaifai</span> - переводчик</li>
			<li><span style="color: #f7941d;">Valar</span> - переводчик</li>
			<li><span style="color: #800000;">Z3nC0rZ</span> - технарь (служит в армии) </li>
			<br>
		</ul>
		<p style="text-align: center;">
			<u><b>6 Лига (АКАДЕМИЯ)</b></u>
		</p>
		<ul>
			<li><span style="color: #339966;">Ashvoice</span>&nbsp;- войсер стажёр</li>
			<li><span style="color: #339966;">SlivciS</span>&nbsp;- войсер стажёр</li>
			<li><span style="color: #339966;">Kiyoko Koheiri</span>&nbsp;- войсер стажёр</li> 
			<li><span style="color: #800000;">WaFee</span> - таймер стажёр</li>                                      
			<li><span style="color: #f7941d;">Renko</span> - саббер стажёр</li>
			<li><span style="color: #f7941d;">FooBoo</span> - саббер стажёр</li>
			<li><span style="color: #f7941d;">Merky</span> - саббер стажёр</li>
		</ul>
		<p style="text-align: center;">
			<u><b>ВНЕ ИЕРАРХИИ (не поддаётся классификации лиговой системы) </b></u>
		</p>
		<ul>
			<li>Fenix0904 - календарщик</li>
			<li>RadiationX - автор приложения, администратор сайта</li>
			<li>Lisitsa - администратор discord-сервера</li>
			<li>Kirja - вёрстка сайта, администратор сайта</li>
		</ul>
	</div>
	<div class="clear"></div>
	<div style="margin-top:10px;"></div>
</div>


<?php require($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
