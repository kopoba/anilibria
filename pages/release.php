<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/minify.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/header.php');

$var['page'] = 'release';

?>

<style>
	.title-rus {
		color: #d83541;
		text-align: center;
		font-size: 23px;
		font-weight: 600;
	}
	
	.title-divider {
		display: block;
		height: 2px;
		background: #d3d3d3;
		margin: 15px 150px;
	}
	
	.title-original {
		color: #d83541;
		font-size: 18px;
		text-align: center;
		font-weight: 600;
	}
	
	.detail_torrent_side {
		float: right;
	}
	
	.detail_pic_corner {
		background-color: #d3d3d3;
		padding: 4px;
	}
	
	.detail_torrent_info {
		float: left;
		width: 460px;
		height: 500px;
		overflow: hidden;
	}
	
	#nextserial{
		position: absolute;
		font-family:'BebasBold', sans-serif;
		color:#fff;
		text-align:center;
		font-size:15px;
		background:rgba(0,0,0,.5);
		width: 358px;
		padding-top:5px;
		padding-bottom:5px;
	}
	
	.download-torrent {
		margin-top: 10px;
		background-color: #e0e0e0;
		height: 100%;
		width: 100%;
		float: left;
		text-align:center;
	}
	
	.xtest1{
		width: 240px;
	}

	.xtest2{
		width: 260px;
	}
	
	.xtest3{
		width: 200px;
	}
	
	.xtest4{
		width: 140px;
	}
	
table { border-collapse: collapse; }
tr { border: none; height: 30px; }
td { border-right: solid 2px #d3d3d3; }
td:last-child { border-right: none; }


.xplayer {
	height: 530px;
	display: none;
}

.release-title {
    color: #d83541;
    text-align: center;
	font-size: 16px;
	margin-top: 0px;
}

.tab-switcher {
	margin-top: 10px;
    padding: 10px 10px;
    
    background-image: linear-gradient(#282829, #282829, #000);
    color: #fff;
    text-transform: uppercase;
    position: relative;
    width: 100%;
    height: 48px;
}

.tab-switcher button.active {
    background: #4d4d4d;
}

.tab-switcher button {
    color: #fff;
    background: transparent;
    border: none;
    font-size: 13px;
    padding: 5px 10px;
    text-transform: uppercase;
    float: left;
    border-radius: 3px;
    
}

.tab-switcher button:focus {
	outline:0;
}

.tab-switcher button.presence_online {
    background: #2d9c64;
    float: right;
}

.light-off {
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.8);
    position: fixed;
    left: 0;
    top: 0;
    z-index: 998;
    display: none;
}

.z-fix {
    position: relative;
    z-index: 999;
}

button.xdark {
	background: transparent;
}

</style>

<div class="light-off"></div>

<div class="news-block">
	<div class="news-header"></div>
	
	

	<div class="detail_torrent_info">
		<h1 class="release-title">
		Этот глупый свин не понимает мечту девочки-зайки<br/>
		Seishun Buta Yarou wa Bunny Girl Senpai no Yume wo Minai
		
		<!--Цирк марионеток / Karakuri Circus -->
		</h1>
		<hr class="poloska-detail">
		<b>Жанры:</b> <a href="/tracker/relizy2.php?genre=экшен">экшен</a> <a href="/tracker/relizy2.php?genre=приключения">приключения</a> <a href="/tracker/relizy2.php?genre=фэнтези">фэнтези</a> <a href="/tracker/relizy2.php?genre=магия">магия</a>
		<br>
		<b>Озвучка:</b> <a href="/tracker/relizy2.php?voice=Dejz">Dejz</a> <a href="/tracker/relizy2.php?voice=Itashi">Itashi</a> <a href="/tracker/relizy2.php?voice=WhiteCrow">WhiteCrow</a> <a href="/tracker/relizy2.php?voice=OkanaTsoy">OkanaTsoy</a>
		<br>
		<b>Аниме-сезон:</b> <a href="/tracker/relizy2.php?date=Осень 2018">Осень 2018</a><br>
		<b>Тип:</b> ТВ (21 эп.), 25 мин.<br>
		<b>Перевод:</b> Nutsy<br>
		<b>Тайминг:</b> Malinero<br>
		<b>Состояние релиза:</b> В работе<br>
		<hr class="poloska-detail">
		<p class="detail-description">
			История повествует о Сэ́те — молодом парне, стремящимся стать великим магом. И группе ведьм, желающих найти Радиáнт — легендарное место, откуда монстры «Немези́ды» спускаются на землю. К тому же, помимо опасностей, ждущих в пути, за героями начала охоту Инквизиция. 
			И снова нас возвращают в этот прекрасный и огромный мир шиноби. Война против призраков далёкого прошлого уже как несколько лет закончилась. Жизнь вернулась в привычное русло и будучи "отсталым" от современности мир, как-то сделал резкий скачок вперед. Появилась развитая инфраструктура, технологии, о которых в "Ураганных хрониках" можно было только мечтать. Но погоня за современностью не затмила традиций. Воля огня, которая всегда была так близка жителям деревни скрытой в листве пылала пуще прежнего. Схема "шиноби-задания-деньги" всё так же актуальна и по сей день. Все эти генины, чунины и джоунины составляли несокрушимую веками иерархию во главе которой стоял лидер именуемый себя Кагэ.
		</p>
	</div>
	

	<div class="detail_torrent_side">
		<div id="nextserial" style="display: block;">Серия выходит в понедельник</div>
		<div class="detail_pic_corner">
			<img class="detail_torrent_pic" border="0" src="/upload/release/1.jpg" width="350" height="500" alt="Radiant / Радиант" title="Radiant / Радиант">
		</div>
		
		<a href="https://www.anilibria.tv/video/top10autumn/"><img width="355" src="https://www.anilibria.tv/bitrix/templates/AniLibria%20KD%20Design/images/images/dopolnitelno%20top10gg.jpg" height="26" width="361"></a>

	</div>

	<div class="clear"></div>

	<div class="tab-switcher">
		<div class="tab-content">
			<button class="active" data-tab="moonPlayer">Внешний плеер</button>
			<button data-tab="anilibriaPlayer" class="">Наш плеер</button>
			<button data-light class="xdark z-fix">Свет</button>
			
			<button class="presence_online" title="Столько либрийцев смотрят это аниме прямо сейчас">Смотрят: 158</button>
			<div class="block_fix"></div>
		</div>
	</div>
	
	<div class="xplayer z-fix" id="moonPlayer">
		<iframe src="https://streamguard.cc/serial/8c04068d73cfada409d06effae051ca3/iframe?nocontrols_translations=1&amp;nocontrols_translations=1&amp;season=1" width="840" height="530" frameborder="0" allowfullscreen=""></iframe>
	</div>
	


	<div class="xplayer z-fix" id="anilibriaPlayer"></div>

	
	<div class="download-torrent">
		<table>
			<tr>
				<td class="xtest1">Серия 1-11 [HDTVRip 1080p]</td>
				<td class="xtest2"><img style="margin-bottom: 3px;" src="/img/other/1.png" alt="dl"> 8.46GB <img style="margin-bottom: 3px;" src="/img/other/2.png" alt="dl"> 100 <img style="margin-bottom: 3px;" src="/img/other/3.png" alt="dl"> 424 <img style="margin-bottom: 3px;" src="/img/other/4.png" alt="dl"> 8307</td>
				<td class="xtest3">Добавлен 16.12.2018</td>
				<td class="xtest4"><img style="margin-bottom: 3px;" src="/img/other/5.png" alt="dl"> <a class="torrent-download-link" href="/upload/torrents/5969.torrent">Cкачать</a>
				</td>
			</tr>
			<tr>
				<td class="xtest1">Серия 1-11 [HDTVRip 1080p]</td>
				<td class="xtest2"><img style="margin-bottom: 3px;" src="/img/other/1.png" alt="dl"> 8.46GB <img style="margin-bottom: 3px;" src="/img/other/2.png" alt="dl"> 100 <img style="margin-bottom: 3px;" src="/img/other/3.png" alt="dl"> 424 <img style="margin-bottom: 3px;" src="/img/other/4.png" alt="dl"> 8307</td>
				<td class="xtest3">Добавлен 16.12.2018</td>
				<td class="xtest4"><img style="margin-bottom: 3px;" src="/img/other/5.png" alt="dl"> <a class="torrent-download-link" href="/upload/torrents/5969.torrent">Cкачать</a>
				</td>
			</tr>
		</table>			
	</div>
	
	<!-- вам понравится -->
	<!-- порядок просмотра -->

	<div class="clear"></div>
	<div class="news_footer"></div>
</div>

<?require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
