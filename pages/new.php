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

$var['page'] = 'new';
?>

<div class="news-block">
		<div class="news-header">
			<h2 class="news-name" style="float:left;">
				Новый релиз
			</h2>
		
		<button data-release-new type="button" class="btn btn-default" style="float: right; height: 30px; padding: 0px 12px; ">Сохранить</button>
		<label class="btn btn-default" style="float: right; height: 30px; padding: 4px 12px; margin-right: 7px;">Загрузить <input id="uploadPosterAdmin" type="file" name="test" style="display: none;"></label>
			
			<div class="clear"></div>	
		</div>
		<div class="clear"></div>
		<div class="detail_torrent_info">
			<input id="nName" class="form-control" type="text" placeholder="Название: О моём перерождении в слизь" required="">
			<input id="nEname" class="form-control" style="margin-top: 7px;" type="text" placeholder="Англиское название: Tensei shitara Slime Datta Ken" required="">
			<input id="nYear" class="form-control" style="margin-top: 7px;" type="text" placeholder="Год выхода: 2018" required="">
			<input id="nType" class="form-control" style="margin-top: 7px;" type="text" placeholder="Тип: ТВ 24 эпизода" required="">
			<input id="nGenre" class="form-control" style="margin-top: 7px;" type="text" placeholder="Жанры: фэнтези, приключения " required="">
			<input id="nVoice" class="form-control" style="margin-top: 7px;" type="text" placeholder="Озвучка: Silv, Hekomi, Malevich, December" required="">
			<input id="nOther" class="form-control" style="margin-top: 7px;" type="text" placeholder="Работа над релизом: Darkknight" required="">
			<input id="nAnnounce" class="form-control" style="margin-top: 7px;" type="text" placeholder="Анонс: Серия выходит в понедельник" required="">
			<select id="nStatus" class="form-control" style="margin-top: 7px;">
					<option value="" disabled selected>Состояние релиза</option>
					<option value="1">В работе</option>
					<option value="2">Завершен</option>
					<option value="2">Скрыт</option>
			</select>
			<input id="nMoon" class="form-control" style="margin-top: 7px;" type="text" placeholder="moonwalk: https://streamguard.cc/serial/ecd3786bcde7f9b28b4f6..." required="">
			<textarea id="nDescription" class="form-control" style="margin-top: 7px; resize: none;" rows="4" placeholder="Описание: Одинокий тридцатисемилетний Сато́ру Мика́мию ..."></textarea>
		</div>
		
	<div class="detail_torrent_side">
		<div class="detail_pic_corner">
			<img id="adminPoster" class="detail_torrent_pic" border="0" src="/upload/release/default.jpg" width="350" height="495" alt="">
		</div>
	</div>	
		<div class="clear"></div>
		<div class="news_footer"></div>
</div>

<div class="news-block">
	<div class="news-header">
		<h2 class="news-name">
			Релизы
		</h2>
		<div class="clear"></div>	
	</div>
	<div class="clear"></div>
	<div>
		<style> td,th { text-align: center; vertical-align: middle; } </style>
		<table id="tableRelease" class="table table-striped table-bordered" style="width:100%">
		<thead>
			<tr>
				<th>ID</th>
				<th>Название</th>
				<th>Статус</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		</tbody>
		</table>
	</div>
	<div class="clear"></div>
	<div class="news_footer"></div>
</div>

<!-- Put this script tag to the <head> of your page -->
<script type="text/javascript" src="https://vk.com/js/api/openapi.js?160"></script>

<script type="text/javascript">
  VK.init({apiId: 6798605, color1: 'FFFFFF', color2: '2B587A', color3: '5B7FA6', onlyWidgets: true});
</script>

<!-- Put this div tag to the place, where the Comments block will be -->
<div id="vk_comments" style="margin-top: 15px;"></div>
<script type="text/javascript">
VK.Widgets.Comments("vk_comments", {limit: 5, attach: "*"});
</script>

<?require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
