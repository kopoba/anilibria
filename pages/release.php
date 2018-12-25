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
$var['release'] = ['id' => 508, 'name' => 'super test'];

/*	TODO
	Page generated in 0.3303 seconds. Peak memory usage: 1.06 MB
	file_get_contents use 97.98% time in func getReleaseVideo and wsInfoShow
	we need cache data from remote servers 
	https://img.poiuty.com/img/ae/293fcc66d6b2dcf71f4bf578f74d27ae.png
*/
?>

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
			
			<button data-online-table class="presence_online" title="Столько либрийцев смотрят это аниме прямо сейчас"></button>
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
				<td class="torrentcol1">Серия 1-11 [HDTVRip 1080p]</td>
				<td class="torrentcol2"><img style="margin-bottom: 3px;" src="/img/other/1.png" alt="dl"> 8.46GB <img style="margin-bottom: 3px;" src="/img/other/2.png" alt="dl"> 100 <img style="margin-bottom: 3px;" src="/img/other/3.png" alt="dl"> 424 <img style="margin-bottom: 3px;" src="/img/other/4.png" alt="dl"> 8307</td>
				<td class="torrentcol3">Добавлен 16.12.2018</td>
				<td class="torrentcol4"><img style="margin-bottom: 3px;" src="/img/other/5.png" alt="dl"> <a class="torrent-download-link" href="/upload/torrents/5969.torrent">Cкачать</a>
				</td>
			</tr>
			<tr>
				<td class="torrentcol1">Серия 1-11 [HDTVRip 1080p]</td>
				<td class="torrentcol2"><img style="margin-bottom: 3px;" src="/img/other/1.png" alt="dl"> 8.46GB <img style="margin-bottom: 3px;" src="/img/other/2.png" alt="dl"> 100 <img style="margin-bottom: 3px;" src="/img/other/3.png" alt="dl"> 424 <img style="margin-bottom: 3px;" src="/img/other/4.png" alt="dl"> 8307</td>
				<td class="torrentcol3">Добавлен 16.12.2018</td>
				<td class="torrentcol4"><img style="margin-bottom: 3px;" src="/img/other/5.png" alt="dl"> <a class="torrent-download-link" href="/upload/torrents/5969.torrent">Cкачать</a>
				</td>
			</tr>
		</table>			
	</div>
	<div class="clear"></div>
	<div class="news_footer"></div>
</div>

<div class="modal fade" id="statModal" tabindex="-1" role="dialog" aria-hidden="true" >
	<div class="modal-dialog" style="width: 480px;">
		<div class="modal-content">
			<div class="modal-header">
				<center><h4 class="modal-title">ТОП-20 ПРЯМО СЕЙЧАС</h4></center>
			</div>
			<div  class="modal-body">
				<div class="tableStat">
					<table class="table table-borderless table-condensed table-hover">
						<tr>
							<th>ТАЙТЛ</th>
							<th class="tableCenter">ОНЛАЙН</th>
						</tr>
						<?php echo wsInfoShow(); ?>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<?require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
