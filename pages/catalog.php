<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/minify.php');

$var['page'] = 'catalog';

require_once($_SERVER['DOCUMENT_ROOT'].'/private/header.php');
?>

<style>
.simpleFilter {
	background-color: #3e3e3e;
	padding: 25px 25px;
	margin-top: 15px;
	height: 217px;
}

.simpleCatalog tr:not(:first-child) td {
	padding-top: 30px;
}

</style>


<div class="simpleFilter">
	<div style="margin-bottom: 25px;">
	  <select class="form-control chosen" data-placeholder="Выбрать жанры ..." name="tags[]" multiple style="">
		<?php echo getGenreList(); ?>
	  </select>
	</div>

<div style="margin-bottom: 25px;">
  <select class="form-control chosen" data-placeholder="Выбрать год ..." name="tags[]" multiple style="">
    <option value="Engineering">2018</option>
    <option value="Carpentry">2017</option>
    <option value="Plumbing">2016</option>
    <option value="Electical">2015</option>
    <option value="Mechanical">2014</option>
    <option value="HVAC">2013</option>
  </select>
	</div>
  
  <input class="btn btn btn-default btn-block" style="width: 100px;" type="submit" value="Показать">
</div>


<div style="margin-top: 15px;">
	<table class="simpleCatalog" style="width: 100%;">
	<tbody>
		<tr>
			<td align="left"><img class="torrent_pic" border="0" src="https://www.anilibria.tv/upload/iblock/30b/jojo-no-kimyou-na-bouken-ougon-no-kaze-neveroyatnoe-priklyuchenie-dzhodzho-zolotoy-veter.png" width="270" height="393" alt="" title="">	</td>
			<td align="center" ><img class="torrent_pic" border="0" src="https://www.anilibria.tv/upload/iblock/80f/momokuri-momokuri.jpg" width="270" height="393" alt="" title=""></td>
			<td align="right"><img class="torrent_pic" border="0" src="https://www.anilibria.tv/upload/iblock/955/irozuku-sekai-no-ashita-kara-iz-zavtrashnego-dnya-raznotsvetnogo-mira.png" width="270" height="393" alt="" title=""></td>
		</tr>
		<tr>
			<td align="left"><img class="torrent_pic" border="0" src="https://www.anilibria.tv/upload/iblock/30b/jojo-no-kimyou-na-bouken-ougon-no-kaze-neveroyatnoe-priklyuchenie-dzhodzho-zolotoy-veter.png" width="270" height="393" alt="" title="">	</td>
			<td align="center" ><img class="torrent_pic" border="0" src="https://www.anilibria.tv/upload/iblock/80f/momokuri-momokuri.jpg" width="270" height="393" alt="" title=""></td>
			<td align="right"><img class="torrent_pic" border="0" src="https://www.anilibria.tv/upload/iblock/955/irozuku-sekai-no-ashita-kara-iz-zavtrashnego-dnya-raznotsvetnogo-mira.png" width="270" height="393" alt="" title=""></td>
		</tr>	
		<tr>
			<td align="left"><img class="torrent_pic" border="0" src="https://www.anilibria.tv/upload/iblock/30b/jojo-no-kimyou-na-bouken-ougon-no-kaze-neveroyatnoe-priklyuchenie-dzhodzho-zolotoy-veter.png" width="270" height="393" alt="" title="">	</td>
			<td align="center" ><img class="torrent_pic" border="0" src="https://www.anilibria.tv/upload/iblock/80f/momokuri-momokuri.jpg" width="270" height="393" alt="" title=""></td>
			<td align="right"><img class="torrent_pic" border="0" src="https://www.anilibria.tv/upload/iblock/955/irozuku-sekai-no-ashita-kara-iz-zavtrashnego-dnya-raznotsvetnogo-mira.png" width="270" height="393" alt="" title=""></td>
		</tr>	
		<tr>
			<td align="left"><img class="torrent_pic" border="0" src="https://www.anilibria.tv/upload/iblock/30b/jojo-no-kimyou-na-bouken-ougon-no-kaze-neveroyatnoe-priklyuchenie-dzhodzho-zolotoy-veter.png" width="270" height="393" alt="" title="">	</td>
			<td align="center" ><img class="torrent_pic" border="0" src="https://www.anilibria.tv/upload/iblock/80f/momokuri-momokuri.jpg" width="270" height="393" alt="" title=""></td>
			<td align="right"><img class="torrent_pic" border="0" src="https://www.anilibria.tv/upload/iblock/955/irozuku-sekai-no-ashita-kara-iz-zavtrashnego-dnya-raznotsvetnogo-mira.png" width="270" height="393" alt="" title=""></td>
		</tr>
	</tbody>	
	</table>
</div>
	

	
<div class="text-center" style="margin-top: 20px;">
	<ul class="pagination">
		<li class="disabled"><span>«</span></li>
		<li class="active"><span>1</span></li>
		<li><a href="/catalog?page=2">2</a></li>
		<li><a href="/catalog?page=3">3</a></li>
		<li><a href="/catalog?page=4">4</a></li>
		<li><a href="/catalog?page=5">5</a></li>
		<li><a href="/catalog?page=6">6</a></li>
		<li><a href="/catalog?page=7">7</a></li>
		<li><a href="/catalog?page=8">8</a></li>
		<li class="disabled"><span>...</span></li>
		<li><a href="/catalog?page=123">123</a></li>
		<li><a href="/catalog?page=124">124</a></li>
		<li><a href="/catalog?page=2" rel="next">»</a></li>
	</ul>
	</div>

</div>

<?require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
