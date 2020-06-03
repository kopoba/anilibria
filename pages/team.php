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

<style>
.day {
    background: #4a4a4a;
    text-align: center;
    margin: 10px 0 10px 0;
    height: 30px;
    font-size: 13pt;
    line-height: 30px;
    border-radius: 3px;
    color: white;
}

.teamleft{
	float:left; margin-left: 6px;
}

.teamright{
	float:right; margin-right: 6px;
}

</style>

<div class="news-block">
	<div class="news-body">

		<p style="text-align: center;">
			<span style="color: #339966;"><span style="font-size: 16pt;">Войсеры / </span></span> <span style="color: #800000;"><span style="font-size: 16pt;">Технари / </span></span><span style="color: #ff6600;"><span style="font-size: 16pt;">Сабберы / </span></span> <span style="color: #000080;"><span style="font-size: 16pt;">Сидеры / </span></span> <span style="color: #33cccc;"><span style="font-size: 16pt;">Дизайнеры </span></span>
		</p>
		<p style="text-align: center; font-size: 13pt; margin-top: 15px; margin-bottom: 15px;">	
		 
		</p>
		
		<div class="day">
				<div class="teamleft">Мы ищем таланты</div> 
				<div class="teamright"><a style="color: #FFF;" href="/pages/request.php">ПОДАТЬ ЗАЯВКУ</a></div>
		</div>

		<ul>
			<li><span style="color: #00a650;">Lupin</span> - глава команды, войсер</li>
            <li>Kirja - главный администратор сайта</li>
		</ul>
			<div class="day">
				<div class="teamleft">&#8544; лига</div> 
				<div class="teamright">СОВЕТ</div>
			</div>
		<ul>
			<li><span style="color: #00a650;">Silv</span> - войсер</li>
			<li><span style="color: #00a650;">Itashi</span> - войсер</li>
			<li><span style="color: #00a650;">Кирито-кун</span> - руководитель войсеров, войсер</li>
			<li><span style="color: #339966;">Dejz</span> - руководитель академии войсеров, войсер</li>
		    <li><span style="color: #f7941d;">Psycho</span> - руководитель команды сабберов</li>
		</ul>
			<div class="day">
				<div class="teamleft">&#8545; лига</div> 
				<div class="teamright">ВЫСШАЯ</div>
			</div>
		<ul>
			<li><span style="color: #000080;">Sekai</span> - руководитель подкоманды сидеров / оформитель</li>
			<li><span style="color: #800000;">Alkhorus </span> - технарь / куратор академии таймеров / помощник рук. технарей</li>
		    <li><span style="color: #800000;">Akkakken</span> - руководитель технарей, технарь / оформитель / войсер стажёр</li>
			<li><span style="color: #800000;">Blaze</span> - технарь</li>
			<li><span style="color: #339966;">Amikiri</span> - войсер</li>
			<li><span style="color: #339966;">Anzen</span> - руководитель команды дизайнеров, войсер</li>
			<li><span style="color: #339966;">Cleo-chan</span> - войсер</li>
			<li><span style="color: #339966;">MyAska</span> - войсер</li>
		</ul>
		<div class="day">
				<div class="teamleft">&#8546; лига</div> 
				<div class="teamright">МАСТЕРА</div>
		</div>
		<ul>
			<li><span style="color: #339966;">Ados</span> - войсер</li>
			<li><span style="color: #339966;">WhiteCroW</span> - войсер</li>
			<li><span style="color: #339966;">Malevich</span> - войсер</li>
			<li><span style="color: #339966;">Hekomi</span> - войсер</li>
            <li><span style="color: #339966;">Nuts</span> - войсер / дизайнер</li>
			<li><span style="color: #339966;">Kari</span> - войсер</li>
			<li><span style="color: #800000;">Quin</span> - технарь</li>
			<li><span style="color: #800000;">Ghost</span> - технарь</li>
			<li><span style="color: #800000;">Пиратехник</span> - технарь</li>
			<li><span style="color: #800000;">Pomidorchik</span> - технарь</li>
			<li><span style="color: #800000;">WhiteCat</span> - технарь </li>
			<li><span style="color: #800000;">Kosaka</span> - технарь </li>
            <li><span style="color: #800000;">Malinero</span> - технарь</li>
			<li><span style="color: #f7941d;">N.O.</span> - переводчик</li>
			<li><span style="color: #f7941d;">ElViS</span> - оформитель</li>
			<li><span style="color: #f7941d;">Sсarlett</span> - оформитель</li>
			<li><span style="color: #f7941d;">Aiso</span> - переводчик</li>
			<li><span style="color: #f7941d;">YourSenpai</span> - переводчик</li>			
			<li><span style="color: #f7941d;">Iron_me</span> - переводчик</li>
            <li><span style="color: #000080;">Rossik666</span> - сидер</li>
			<li><span style="color: #000080;">basegame</span> - сидер</li>
			<li><span style="color: #000080;">GeeKaZ0iD</span> - сидер</li>
            <li><span style="color: #000080;">OdinokijKot</span> - сидер</li>
			<li><span style="color: #000080;">Tuxoid</span> - сидер</li>
		</ul>
		<div class="day">
				<div class="teamleft">&#8547; лига</div> 
				<div class="teamright">ОСНОВА</div>
		</div>
		<ul>
			<li><span style="color: #339966;">Gomer</span> - войсер</li>
			<li><span style="color: #339966;">Arato</span> - войсер</li>
			<li><span style="color: #339966;">Derenn</span>&nbsp;- войсер </li>
			<li><span style="color: #339966;">Narea</span>&nbsp;- войсер / дизайнер</li>
			<li><span style="color: #339966;">Kiyoko Koheiri</span> - войсер</li> 
			<li><span style="color: #339966;">SlivciS</span> - войсер</li>
			<li><span style="color: #339966;">OkanaTsoy</span> - войсер</li>
			<li><span style="color: #800000;">Hidan</span> - технарь</li>
			<li><span style="color: #800000;">DarkKnight</span> - технарь </li>
			<li><span style="color: #800000;">Psychosocial76</span> - технарь</li>
			<li><span style="color: #800000;">Dr.One</span> - технарь</li>
			<li><span style="color: #800000;">ZencorZ</span> - технарь</li>
			<li><span style="color: #800000;">Ninja-san</span> - технарь</li>
			<li><span style="color: #800000;">EliteNoob </span> - технарь</li>
			<li><span style="color: #800000;">Booker</span> - технарь</li>
            <li><span style="color: #f7941d;">The Magus</span> - переводчик / оформитель</li>
			<li><span style="color: #f7941d;">Renko</span> - переводчик</li>
            <li><span style="color: #f7941d;">drowzzzy</span> - переводчик</li>
		    <li><span style="color: #f7941d;">MustDy</span> - переводчик</li>
            <li><span style="color: #f7941d;">BONN</span> -  оформитель </li>
			<li><span style="color: #f7941d;">NaRVioN</span> - оформитель</li>
			<li><span style="color: #f7941d;">DiaZone</span> - оформитель</li> 
			<li><span style="color: #f7941d;">Waspil</span> - оформитель</li>
			<li><span style="color: #f7941d;">FLASHIx</span> - оформитель / сидер</li>
			<li><span style="color: #33cccc;">Kell</span> - дизайнер</li>
			<li><span style="color: #33cccc;">Muvik</span> - дизайнер</li>
			<li><span style="color: #33cccc;">Residensi</span> - дизайнер</li>
			<li><span style="color: #33cccc;">Sebastian Wilde</span> - дизайнер</li>
			<li><span style="color: #000080;">Iddqd79</span> - сидер</li>
			<li><span style="color: #000080;">Rumaruka</span> - сидер</li>
			<li><span style="color: #000080;">btc8190</span> - сидер</li>
			<li><span style="color: #000080;">udtpro</span> - сидер</li>
			<li><span style="color: #000080;">zlo</span> - сидер</li>
		</ul>
		<div class="day">
				<div class="teamleft">&#8548; лига</div> 
				<div class="teamright">ЗАПАС</div>
		</div>
		<ul>
			<li><span style="color: #339966;">HectoR</span> - войсер (в отпуске)</li>
			<li><span style="color: #339966;">Rexus</span> - войсер (в отпуске)</li>
			<li><span style="color: #f7941d;">Constantum</span> - переводчик (в отпуске)<br>
            <li><span style="color: #f7941d;">Suisei</span> - переводчик / оформитель (в отпуске)</li>
            <li><span style="color: #f7941d;">Skaifai</span> - переводчик (в отпуске)</li>
			<li><span style="color: #f7941d;">TiimeIrre</span> - переводчик (в отпуске)</li>
		    <li><span style="color: #f7941d;">Flerion</span> - переводчик / оформитель (в отпуске)</li>
			<li><span style="color: #f7941d;">Inari</span> - переводчик (в отпуске)</li>
            <li><span style="color: #f7941d;">Smallow</span> - переводчик (в отпуске)</li>
            <li><span style="color: #f7941d;">Keitaro</span> - переводчик / оформитель (в отпуске)</li>
            <li><span style="color: #800000;">Luchano</span> - технарь (в отпуске)</li>
			<li><span style="color: #800000;">Hikariya</span> - технарь (в отпуске)</li>
			<li><span style="color: #800000;">EncoR</span> - технарь (в отпуске)</li>
			<li><span style="color: #000080;">Tekyera</span> - сидер (в отпуске)</li>
			<li><span style="color: #000080;">SkyMasteer</span> - сидер (в отпуске)</li>
			<li><span style="color: #000080;">LightKay</span> - сидер (в отпуске)</li>
		</ul>
		<div class="day">
				<div class="teamleft">&#8549; лига</div> 
				<div class="teamright">АКАДЕМИЯ</div>
		</div>
		<ul>
			<li><span style="color: #339966;">Ashvoice</span> - войсер стажёр</li>
            <li><span style="color: #339966;">Andryoushka</span> - войсер стажёр</li> 
            <li><span style="color: #339966;">Zozya</span> - войсер стажёр</li>
            <li><span style="color: #339966;">Vulpis</span> - войсер стажёр</li>
            <li><span style="color: #339966;">ELeyna</span> - войсер стажёр</li>
            <li><span style="color: #339966;">Kaize</span> - войсер стажёр</li>
			<li><span style="color: #339966;">Descipe</span> - войсер стажёр</li>
			<li><span style="color: #339966;">MefistoO</span> - войсер стажёр</li>
			<li><span style="color: #339966;">Crowley</span> - войсер стажёр</li>
			<li><span style="color: #339966;">Jazz Jack</span> - войсер стажёр</li>
			<li><span style="color: #339966;">Mikelo-san</span> - войсер стажёр</li>
			<li><span style="color: #339966;">Briff</span> - войсер стажёр</li>
			<li><span style="color: #339966;">BStrong</span> - войсер стажёр</li>
            <li><span style="color: #800000;">Nikanor47</span> - таймер стажёр</li>
            <li><span style="color: #800000;">ito</span> - таймер стажёр</li>			
            <li><span style="color: #800000;">TeagRit</span> - таймер стажёр</li>			
            <li><span style="color: #800000;">im4x</span> - таймер стажёр</li>
            <li><span style="color: #800000;">StuffyHarbor</span> - таймер стажёр</li>
            <li><span style="color: #800000;">H3O</span> - таймер стажёр</li>			
			<li><span style="color: #f7941d;">Kitsune</span> - саббер стажёр</li>
			<li><span style="color: #f7941d;">Dyunan</span> - саббер стажёр</li>
			<li><span style="color: #f7941d;">Ameno Aoki</span> - саббер стажёр</li>
			<li><span style="color: #f7941d;">Teatral</span> - саббер стажёр</li>
			<li><span style="color: #f7941d;">Medoed</span> - саббер стажёр</li>	
			<li><span style="color: #f7941d;">Selv</span> - саббер стажёр</li>
			<li><span style="color: #f7941d;">Sinaka</span> - саббер стажёр</li>
			<li><span style="color: #f7941d;">Zaqwerd</span> - саббер стажёр</li>
			<li><span style="color: #000080;">Ph0enix</span> - сидер (4 лига), саббер стажёр</li>			
		</ul>
	
		<div class="day">
			<div class="teamleft">
				Не поддаётся классификации лиговой системы
			</div>
		</div>
		<ul>
			<li>RadiationX - автор приложения, администратор сайта</li>
			<li>Lisitsa - администратор discord-сервера</li>
			<li>Инквизитор - администратор discord-сервера</li>
			<li>SineD</span> - Работа с группой ВК</li>
			<li>Maximka</span> - Организатор мероприятий проекта</li>
			<li>Hant</span> - Руководитель PR-команды</li>
		</ul>
	</div>
	<div class="clear"></div>
	<div style="margin-top:10px;"></div>
</div>


<?php require($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
