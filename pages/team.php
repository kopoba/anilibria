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
			<span style="color: #339966;"><span style="font-size: 16pt;">Войсеры, </span></span> <span style="color: #800000;"><span style="font-size: 16pt;">Технари, </span></span><span style="color: #ebd800;"><span style="font-size: 16pt;">Переводчики, </span></span> <span style="color: #ff6600;"><span style="font-size: 16pt;">Оформители, </span></span> <span style="color: #b523c5;"><span style="font-size: 16pt;">Релизёры, </span></span> <span style="color: #000080;"><span style="font-size: 16pt;">Сидеры, </span></span> <span style="color: #33cccc;"><span style="font-size: 16pt;">Дизайнеры </span></span>
		</p>
		<p style="text-align: center; font-size: 13pt; margin-top: 15px; margin-bottom: 15px;">	
		 
		</p>
		
		<div class="day">
				<div class="teamleft">Мы ищем таланты</div> 
				<div class="teamright"><a style="color: #FFF;" href="https://t.me/joinlibria_bot">ПОДАТЬ ЗАЯВКУ</a></div>
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
		</ul>
			<div class="day">
				<div class="teamleft">&#8545; лига</div> 
				<div class="teamright">ВЫСШАЯ</div>
			</div>
		<ul>
			<li><span style="color: #800000;">Alkhorus </span> - технарь, куратор академии таймеров</li>
			<li><span style="color: #339966;">Amikiri</span> - войсер, руководитель переводчиков</li>
			<li><span style="color: #339966;">Anzen</span> - войсер, дизайнер</li>
			<li><span style="color: #339966;">Cleo-chan</span> - войсер</li>
			<li><span style="color: #339966;">MyAska</span> - войсер</li>
			<li><span style="color: #b523c5;">Rossik666</span> - руководитель команд: релизёров, сидеров; оформитель (четвёртая лига)</li>
			<li><span style="color: #f7941d;">Kiota</span> - руководитель команды оформителей</li>
			<li><span style="color: #33cccc;">Spiny</span> - руководитель команды дизайнеров</li>
		</ul>
		<div class="day">
				<div class="teamleft">&#8546; лига</div> 
				<div class="teamright">МАСТЕРА</div>
		</div>
		<ul>
			<li><span style="color: #339966;">Ados</span> - войсер</li>
			<li><span style="color: #339966;">Hekomi</span> - войсер</li>
            <li><span style="color: #339966;">Nuts</span> - войсер, дизайнер</li>
			<li><span style="color: #339966;">HectoR</span> - войсер</li>
			<li><span style="color: #339966;">OkanaTsoy</span> - войсер</li>
			
			<li><span style="color: #800000;">Quin</span> - технарь</li>
			<li><span style="color: #800000;">Ghost</span> - технарь</li>
			<li><span style="color: #800000;">WhiteCat</span> - технарь</li>
			<li><span style="color: #800000;">Hidan</span> - технарь</li>
			<li><span style="color: #800000;">im4x</span> - технарь</li>
			<li><span style="color: #800000;">MaxVold</span> - технарь</li>

			<li><span style="color: #ebd800;">Teriliva</span> - переводчик, редактор</li>
			<li><span style="color: #f7941d;">ElViS</span> - оформитель, релизёр</li>
			
			<li><span style="color: #33cccc;">Kell</span> - дизайнер</li>

			<li><span style="color: #b523c5;">GeeKaZ0iD</span> - зам. руководителя релизёров, сидер</li>
			<li><span style="color: #000080;">basegame</span> - сидер</li>
            <li><span style="color: #000080;">OdinokijKot</span> - сидер</li>
			<li><span style="color: #000080;">Tuxoid</span> - сидер, релизёр</li>
		</ul>
		<div class="day">
				<div class="teamleft">&#8547; лига</div> 
				<div class="teamright">ОСНОВА</div>
		</div>
		<ul>
			<li><span style="color: #339966;">Gomer</span> - войсер</li>
			<li><span style="color: #339966;">Derenn</span>&nbsp;- войсер </li>
			<li><span style="color: #339966;">SlivciS</span> - войсер, дизайнер</li>
			<li><span style="color: #339966;">WhiteCroW</span> - войсер</li>
			<li><span style="color: #339966;">BStrong</span> - войсер</li>
			<li><span style="color: #339966;">JazzJack</span> - войсер</li>
			<li><span style="color: #339966;">Zozya</span> - войсер</li>
			<li><span style="color: #339966;">Crowley</span> - войсер</li>
			<li><span style="color: #339966;">Hoopoe</span> - войсер</li>
			<li><span style="color: #339966;">Briff</span> - войсер</li>
			<li><span style="color: #339966;">Renie</span> - войсер</li>
			
			<li><span style="color: #800000;">Dr.One</span> - технарь</li>
			<li><span style="color: #800000;">ito</span> - технарь</li>
			<li><span style="color: #800000;">Luchano</span> - технарь</li>
			<li><span style="color: #800000;">Nikanor47</span> - технарь</li>
			<li><span style="color: #800000;">Shiro</span> - технарь</li>
			<li><span style="color: #800000;">Caxaro4ek</span> - технарь</li>
			<li><span style="color: #800000;">Kosaka</span> - технарь, дизайнер</li>
			<li><span style="color: #800000;">Mango</span> - технарь</li>
			<li><span style="color: #800000;">dzoom</span> - технарь</li>
			<li><span style="color: #800000;">Shinyoro</span> - технарь</li>
			<li><span style="color: #800000;">Pomidorchik</span> - технарь</li>
			<li><span style="color: #800000;">Violin</span> - технарь</li>
			<li><span style="color: #800000;">Chewy</span> - технарь</li>
			<li><span style="color: #800000;">Ninja-san</span> - технарь</li>
			<li><span style="color: #800000;">StuffyHarbor</span> - технарь</li>
			
			<li><span style="color: #ebd800;">boundlessend</span> - переводчик, релизёр</li>
			<li><span style="color: #ebd800;">Timur_kun</span> - переводчик</li>
			<li><span style="color: #ebd800;">Flames</span> - переводчик</li>
			<li><span style="color: #ebd800;">Leyla</span> - переводчик</li>
			<li><span style="color: #ebd800;">orochimaru</span> - переводчик</li>
			<li><span style="color: #ebd800;">SmithTheHero</span> - переводчик</li>
			<li><span style="color: #ebd800;">Initrd</span> - переводчик</li>
			<li><span style="color: #ebd800;">Yumiya</span> - переводчик стажёр</li>
			<li><span style="color: #ebd800;">DeerGfonis</span> - переводчик стажёр</li>
			<li><span style="color: #ebd800;">Fushidarana</span> - переводчик стажёр</li>
			<li><span style="color: #ebd800;">chibikTLT</span> - переводчик стажёр</li>
			
			
			<li><span style="color: #f7941d;">Evrey or zizika</span> - оформитель</li>
			<li><span style="color: #f7941d;">Diabl</span> - оформитель</li>
			<li><span style="color: #f7941d;">JoyMaloy</span> - оформитель</li>
			<li><span style="color: #f7941d;">Akeno102</span> - оформитель</li>
			<li><span style="color: #f7941d;">Neri</span> - оформитель</li>
			<li><span style="color: #f7941d;">ITWolf</span> - оформитель</li>
			<li><span style="color: #f7941d;">AFFO</span> - оформитель</li>
			
			<li><span style="color: #33cccc;">Sebastian Wilde</span> - дизайнер</li>
			<li><span style="color: #33cccc;">Yukki</span> - дизайнер</li>
			
			<li><span style="color: #000080;">iDDQD79</span> - сидер</li>
			<li><span style="color: #000080;">Rumaruka</span> - сидер</li>
			<li><span style="color: #000080;">btc8190</span> - сидер</li>
			<li><span style="color: #000080;">zlo</span> - сидер</li>
			<li><span style="color: #000080;">xJesus</span> - сидер</li>
			<li><span style="color: #000080;">Falciloid</span> - сидер</li>
			<li><span style="color: #000080;">Ztracer</span> - сидер</li>
			<li><span style="color: #000080;">Vinipux322</span> - сидер</li>
			
			<li><span style="color: #b523c5;">T1MOX4</span> - релизёр</li>
			
			
		</ul>
		<div class="day">
				<div class="teamleft">&#8548; лига</div> 
				<div class="teamright">ЗАПАС</div>
		</div>
		<ul>
			<li><span style="color: #339966;">Arato</span> - войсер (в отпуске)</li>
			<li><span style="color: #339966;">Kari</span> - войсер (в отпуске)</li>
			
			<li><span style="color: #ebd800;">Iron_me</span> - переводчик (в отпуске)</li>
			<li><span style="color: #ebd800;">Ph0enix</span> - переводчик, сидер (в отпуске)</li>
			<li><span style="color: #ebd800;">Annatar</span> - переводчик (в отпуске)</li>
			<li><span style="color: #ebd800;">AKi99</span> - переводчик стажёр (в отпуске)</li>	
			<li><span style="color: #ebd800;">Sinaka</span> - переводчик (в отпуске)</li>
			<li><span style="color: #ebd800;">Artairo</span> - переводчик стажёр (в отпуске)</li>	
			<li><span style="color: #ebd800;">Vurdalak121</span> - переводчик</li>
			<li><span style="color: #ebd800;">Nasty Lupus</span> - переводчик стажёр</li>
			<li><span style="color: #ebd800;">Flerion</span> - переводчик / оформитель</li>
			
			<li><span style="color: #f7941d;">Waspil</span> - оформитель (в отпуске)</li>
			
			<li><span style="color: #800000;">Psychosocial76</span> - технарь, дизайнер (в отпуске)</li>
			<li><span style="color: #800000;">ZencorZ</span> - технарь (в отпуске)</li>
			<li><span style="color: #800000;">EncoR</span> - технарь (в отпуске)</li>
			<li><span style="color: #800000;">Blaze</span> - технарь (в отпуске)</li>
			<li><span style="color: #800000;">H3O</span> - технарь (в отпуске)</li>
			
			<li><span style="color: #000080;">Aurenmaru</span> - сидер (в отпуске)</li>
			
			<li>Hant</span> - PR-команда (в отпуске)</li>
			
		</ul>
		<div class="day">
				<div class="teamleft">&#8549; лига</div> 
				<div class="teamright">АКАДЕМИЯ</div>
		</div>
		<ul>
			<li><span style="color: #339966;">Ashvoice</span> - войсер стажёр</li>
			<li><span style="color: #339966;">Andryoushka</span> - войсер стажёр</li> 
			<li><span style="color: #339966;">Vulpis</span> - войсер стажёр</li>
			<li><span style="color: #339966;">ELeyna</span> - войсер стажёр</li>
			<li><span style="color: #339966;">Kaize</span> - войсер стажёр</li>
			<li><span style="color: #339966;">MefistoO</span> - войсер стажёр</li>
			<li><span style="color: #339966;">Zaika</span> - войсер стажёр</li>
			<li><span style="color: #339966;">Norma</span> - войсер стажёр</li>
			
			<li><span style="color: #800000;">Deveson</span> - таймер стажёр</li>
			<li><span style="color: #800000;">Robolightning</span> - таймер стажёр</li>
			<li><span style="color: #800000;">Hattori</span> - таймер стажёр</li>
			<li><span style="color: #800000;">DimaKnyaz</span> - таймер стажёр</li>
			<li><span style="color: #800000;">Clownassa</span> - таймер стажёр</li>
			<li><span style="color: #800000;">Ichiro</span> - таймер стажёр</li>
			<li><span style="color: #800000;">Vansinvin</span> - таймер стажёр</li>
			
		</ul>
	
		<div class="day">
			<div class="teamleft">
				Не поддаётся классификации лиговой системы
			</div>
		</div>
		<ul>
			<li>RadiationX - автор приложения, администратор сайта</li>
			<li>Siren Licorisa - администратор discord-сервера</li>
			<li>Инквизитор - администратор discord-сервера</li>
			<li>SineD</span> - Работа с группой ВК</li>
			<li>Maximka</span> - Организатор мероприятий проекта</li>
		</ul>
	</div>
	<div class="clear"></div>
	<div style="margin-top:10px;"></div>
</div>


<?php require($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
