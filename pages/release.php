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
$var['release'] = ['id' => 508, 'rid' => 7, 'name' => 'super test'];

$showTorrentEditTable = showEditTorrentTable();

/*	TODO
	Page generated in 0.3303 seconds. Peak memory usage: 1.06 MB
	file_get_contents use 97.98% time in func getReleaseVideo and wsInfoShow
	we need cache data from remote servers 
	https://img.poiuty.com/img/ae/293fcc66d6b2dcf71f4bf578f74d27ae.png
*/
?>

<div class="light-off"></div>

<?php echo showRelease(); ?>

<!-- Put this script tag to the <head> of your page -->
<script type="text/javascript" src="https://vk.com/js/api/openapi.js?160"></script>

<script type="text/javascript">
  VK.init({apiId: 6798605, color1: 'FFFFFF', color2: '2B587A', color3: '5B7FA6', onlyWidgets: true});
</script>

<!-- Put this div tag to the place, where the Comments block will be -->
<div id="vk_comments" style="margin-top: 15px;"></div>
<script type="text/javascript">
VK.Widgets.Comments("vk_comments", {limit: 5, attach: false});
</script>

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

<div class="modal fade" id="editTorrent" tabindex="-1" role="dialog" aria-hidden="true" >
	<div class="modal-dialog" style="width: 830px;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="changeAnnounceMes">Редактирование торрентов</h4>
			</div>
			
			<div class="modal-body">
				<table style="margin-bottom: 10px;">
					<tbody>
						<tr>
							<td><input class="form-control" style="width: 265px;" type="text" id="torrentFile"  placeholder="File" readonly></td>
							<td><input class="form-control" style="margin-left: 5px; width: 130px;" id="announce" type="text" placeholder="ID"></td>
							<td><input class="form-control" style="margin-left: 5px; width: 258px;" id="announce" type="text" placeholder="1-8"></td>
							<td><input class="form-control" style="margin-left: 5px; width: 130px;" id="announce" type="text" placeholder="HDTVRip 720p"></td>
						</tr>
					</tbody>
				</table>
				<hr/>
				<table id="editTorrentTable" style="margin-bottom: 10px;">
					<tbody>
						<?php echo $showTorrentEditTable['0']; ?>
					</tbody>
				</table>
				
				<script>
					var editTorrentTable = JSON.parse('<?php echo $showTorrentEditTable['1']; ?>');
				</script>
			</div>
			
			<div class="clear"></div>
			<div class="modal-footer">
				<label class="btn btn-default">Загрузить <input id="uploadTorrent" type="file" name="test" style="display: none;"></label>
				<button type="button" data-send-torrent class="btn btn-default">Сохранить</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="changeAnnounce" tabindex="-1" role="dialog" aria-hidden="true" >
	<div class="modal-dialog" style="width: 480px;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="changeAnnounceMes">Изменить дату выхода серии</h4>
			</div>
			<div  class="modal-body">
				<input class="form-control" id="announce" type="text" placeholder="Серия выходит в понедельник" >
			</div>
			<div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn btn-default">Закрыть</button>
				<button type="button" data-send-announce class="btn btn-default">Сохранить</button>
			</div>
		</div>
	</div>
</div>

<?require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
