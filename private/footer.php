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
			</div>
			
		</div>
		
		<div class="clear"></div>
		<div class="footer">
			<div class="footer_left">
				<div class="footer_left2">
					<a href="http://www.animespirit.ru/"><img src="/img/button/1.jpg" alt=""></a>
					<a href="http://allmult.com/"><img src="/img/button/2.gif" alt=""></a>
					<a href="http://www.animag.ru/"><img src="/img/button/3.gif" alt=""></a>
					<a href="http://anidream.net/"><img src="/img/button/4.gif" alt=""></a>
					<a href="http://anires.ru/pages/mainmenu.php"><img src="/img/button/5.gif" alt=""></a>
					<a href="http://anibreak.ru/"><img src="/img/button/6.png" alt=""></a>
				</div>
			</div>
			<div class="footer_center">
				<div class="chat">
				<a href="#"><span class="chat-link"></span></a>
				</div>
			</div>
			<div class="footer_right">
				<ul>
					<li><a href="/pages/login.php#rules">Правила</a></li>
					<li><a href="">Реклама</a></li>
					<li><a href="/pages/cp.php">Личный кабинет</a></li>
					<?php 
						$tmpURL = "<li><a href=\"/pages/login.php\">Регистрация</a></li><li><a href=\"/pages/login.php\">Вход</a></li>";
						if($user) $tmpURL = "<li><a href=\"/pages/favorites.php\">Избранное</a></li><li><a href=\"/public/logout.php\">Выход</a></li>";
						echo $tmpURL;
						unset($tmpURL);
					?>
				</ul>
				<p>
				Весь материал на сайте представлен исключительно для домашнего ознакомительного просмотра.<br/> В случаях нарушения авторских прав - обращайтесь на почту lupin@anilibria.tv 
				</p>
			</div>
		</div>
		<script src="<?php echo fileTime('/js/jquery.min.js');?>"></script>	
		<script src="<?php echo fileTime('/js/bootstrap.min.js');?>"></script>
        <script src="<?php echo fileTime('/js/jquery.lazy.min.js');?>"></script>
        <script>
            $(function() {
                $('.lazy').Lazy();
            });
        </script>
		<script src="<?php echo fileTime('/js/main.js');?>"></script>
		<?php echo footerJS(); ?>
		<script>console.log("<?php echo pageStat(); ?>");</script>
		<!-- Yandex.Metrika counter -->
		<script type="text/javascript" >
			(function (d, w, c) {
				(w[c] = w[c] || []).push(function() {
					try {
						w.yaCounter23688205 = new Ya.Metrika({
							id:23688205,
							clickmap:true,
							trackLinks:true,
							accurateTrackBounce:true
						});
					} catch(e) { }
				});

				var n = d.getElementsByTagName("script")[0],
					s = d.createElement("script"),
					f = function () { n.parentNode.insertBefore(s, n); };
				s.type = "text/javascript";
				s.async = true;
				s.src = "https://cdn.jsdelivr.net/npm/yandex-metrica-watch/watch.js";

				if (w.opera == "[object Opera]") {
					d.addEventListener("DOMContentLoaded", f, false);
				} else { f(); }
			})(document, window, "yandex_metrika_callbacks");
		</script>
		<noscript><div><img src="https://mc.yandex.ru/watch/23688205" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
		<!-- /Yandex.Metrika counter -->
		<!-- 
			Copyright (C) 2018-2019 The AniLibria developers.
			Please contribute if you find AniLibria useful.
			The source code is available from https://github.com/anilibria/
		-->		
	</body>
</html>
