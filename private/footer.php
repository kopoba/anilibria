			</div>
			<div class="side">
				<div class="asidehead" >
					<div style="width: 260px; padding-top: 11px; margin: 0 auto;">

						<div class="inner-addon right-addon">
						<i class="glyphicon glyphicon-search"></i>
						<input id="smallSearchInput" class="form-control" type="search" style="width: 100%; height: 30px;" placeholder="Найти аниме по названию" autocomplete="off">
					</div>

				</div>
			</div>

			<style>
				#smallSearchTable td:hover {
					background-color: #460c0c;
				}
			</style>

			<div id="smallSearch" class="smallSearch">
				<table id="smallSearchTable">
					<tbody>
					</tbody>
				</table>
				</div>

				<div class="clear"></div>
				<?php echo showPosters(); ?>
				<?php if(checkADS()): ?>
				<div id="adfox_162064825798024098"></div>
				<script>
					window.yaContextCb.push(()=>{
						Ya.adfoxCode.create({
						ownerId: 332443,
						containerId: 'adfox_162064825798024098',
						params: {
							p1: 'cpapg',
							p2: 'gxmx'
						}
						})
					})
				</script>
				<script>
				setInterval(function(){            
				window.Ya.adfoxCode.reload('adfox_162064825798024098')
				}, 30000);
				</script>
				<?php endif; ?>
			</div>

		</div>

		<div class="clear"></div>
		<div class="footer">
			<div class="footer_left">
				<div class="footer_left2">
					<a href="http://www.animespirit.ru/" target="_blank" rel="nofollow"><img src="/img/button/1.jpg" alt=""></a>
					<a href="https://github.com/anilibria" target="_blank" rel="nofollow"><img src="/img/button/github.png" alt=""></a>
					<a href="https://alice2k.work/" target="_blank" rel="nofollow"><img src="/img/button/alice2k.png" alt=""></a>
				</div>
			</div>
			<div class="footer_center">
			</div>
			<div class="footer_right">
				<ul>
					<li><a href="/pages/login.php#rules">Правила</a></li>
					<li><a href="tg://resolve?domain=Libria911Bot">Вопрос</a></li>
					<li><a href="/pages/cp.php">Личный кабинет</a></li>
					<?php
						$tmpURL = "<li><a href=\"/pages/login.php\">Регистрация</a></li><li><a href=\"/pages/login.php\">Вход</a></li>";
						if($user) $tmpURL = "<li><a href=\"/pages/favorites.php\">Избранное</a></li><li><a href=\"/public/logout.php\">Выход</a></li>";
						echo $tmpURL;
						unset($tmpURL);
					?>
				</ul>
				<p>
				Весь материал на сайте представлен исключительно для домашнего ознакомительного просмотра.<br/> В случаях нарушения авторских прав - обращайтесь на почту anilibria@protonmail.com
				</p>
			</div>
		</div>

		<div class="modal fade" id="authPlsModal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" style="width: 500px;">
				<div class="modal-content">
					<div class="modal-body">
						<pre style="margin-top: 7px;"><center>Пожалуйста, <a href="https://www.anilibria.tv/pages/login.php">авторизуйтесь</a> на сайте.</center></pre>
					</div>
				</div>
			</div>
		</div>

		<script src="<?php echo '/js/jquery.min.js';?>"></script>
		<script src="<?php echo '/js/bootstrap.min.js';?>"></script>
		<script src="<?php echo '/js/main.js?hash=' . md5_file('/var/www/html/js/main.js');?>"></script>
		<?php echo footerJS(); ?>
		<script>console.log("<?php echo pageStat(); ?>");</script>
		<!--
			Copyright (C) 2018-2019 The AniLibria developers.
			Please contribute if you find AniLibria useful.
			The source code is available from https://github.com/anilibria/
		-->
		<?php if(checkADS()):?>
            <script type='text/javascript' data-cfasync='false'>
                (function(){
                    var ud;
                    try { ud = localStorage.getItem('e795110e33_uid'); } catch (e) { }

                    var script = document.createElement('script');
                    script.type = 'text/javascript';
                    script.charset = 'utf-8';
                    script.async = 'true';
                    script.src = 'https://clicktroute.com//bens/vinos.js?23489&u=' + ud + '&a=' + Math.random();
                    document.body.appendChild(script);
                })();
            </script>
		<?php endif;?>
	</body>
</html>
