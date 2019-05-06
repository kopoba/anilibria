<?php
require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title></title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://www.anilibria.tv/css/bootstrap.min.css">
		<script src="https://www.anilibria.tv/js/jquery.min.js"></script>
		<script src="https://www.anilibria.tv/js/bootstrap.min.js"></script>
		<script src="https://www.anilibria.tv/js/player.js" type="text/javascript"></script>
		<script>
			function anilibriaIframe(){
				$('#iframeModal').modal('show');
				console.log('sdsdfdsf');
			}
		</script>
		<style>
			/* exo-2-regular - latin_cyrillic */
			/* https://google-webfonts-helper.herokuapp.com/fonts/exo-2?subsets=cyrillic,latin */
			@font-face {
			  font-family: 'Exo 2';
			  font-style: normal;
			  font-weight: 400;
			  src: url('https://www.anilibria.tv/fonts/exo-2-v5-latin_cyrillic-regular.eot'); /* IE9 Compat Modes */
			  src: local('Exo 2'), local('Exo2-Regular'),
				   url('https://www.anilibria.tv/fonts/exo-2-v5-latin_cyrillic-regular.eot?#iefix') format('embedded-opentype'), /* IE6-IE8 */
				   url('https://www.anilibria.tv/fonts/exo-2-v5-latin_cyrillic-regular.woff2') format('woff2'), /* Super Modern Browsers */
				   url('https://www.anilibria.tv/fonts/exo-2-v5-latin_cyrillic-regular.woff') format('woff'), /* Modern Browsers */
				   url('https://www.anilibria.tv/fonts/exo-2-v5-latin_cyrillic-regular.ttf') format('truetype'), /* Safari, Android, iOS */
				   url('https://www.anilibria.tv/fonts/exo-2-v5-latin_cyrillic-regular.svg#Exo2') format('svg'); /* Legacy iOS */
			}
			html,body{
				margin:0;padding:0;width:100%;height:100%;
			}
		</style>
	</head>
	<body>
		<div id="anilibriaPlayer" style="width:100%;height:100%;"></div>
		<?php
			$vid = 0;
			$tmp = iframePlayer(); 
			if(!empty($tmp['id'])){
				echo $tmp['result'];
				$vid = $tmp['id'];
			}
		?>	
		<div class="modal fade" id="iframeModal" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Код плеера</h4>
					</div>
				<div class="modal-body">
					<pre>https://www.anilibria.tv/public/iframe.php?id=<?php echo $vid; ?></pre>
					<pre>&lt;iframe src="https://www.anilibria.tv/public/iframe.php?id=<?php echo $vid; ?>" type="text/html" width=840 height=515 frameborder="0" allowfullscreen>&lt;/iframe&gt;</pre>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
