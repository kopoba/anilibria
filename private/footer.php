			</div>
			<div class="side">
				<div class="asidehead" >
					<div style="width: 91%; padding-top: 11px; margin: 0 auto;">
					<input class="form-control" type="search" style="width: 100%; height: 30px;" placeholder="Найти аниме по названию" autocomplete="off">
					</div>
				</div>
				
				<div class="clear"></div>
				
				<div class="torrent-block">
					<div class="torrent_block">
						<a href="/release/tokyo-ghoul-re.html"><img class="lasttorpic" src="/upload/poster/1.jpg" alt="" width="245" height="352"></a>
						<p class="lasttortext">Tokyo Ghoul:re / Токийский Гуль: Перерождение</p>
					</div>
				</div>
				
				<div class="torrent-block">
					<div class="torrent_block">
						<a href="/release/tokyo-ghoul-re.html"><img class="lasttorpic" src="/upload/poster/2.jpg" alt="" width="245" height="352"></a>
						<p class="lasttortext">Tokyo Ghoul:re / Токийский Гуль: Перерождение</p>
					</div>
				</div>
				
				<div class="torrent-block">
					<div class="torrent_block">
						<a href="/release/tokyo-ghoul-re.html"><img class="lasttorpic" src="/upload/poster/3.jpg" alt="" width="245" height="352"></a>
						<p class="lasttortext">Tensei shitara Slime Datta Ken /  О моём перерождении в слизь</p>
					</div>
				</div>
				
				<div class="torrent-block">
					<div class="torrent_block">
						<a href="/release/tokyo-ghoul-re.html"><img class="lasttorpic" src="/upload/poster/4.jpg" alt="" width="245" height="352"></a>
						<p class="lasttortext">ChaoS;HEAd / Вершина хаоса</p>
					</div>
				</div>
				<?php 
					$tmpURL = 1;
					if(rand(1,10) > 5) $tmpURL = 2;
					echo "<img src=\"/img/pushall$tmpURL.jpg\" alt=\"\">";
					unset($tmpURL);
				?>
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
				<a  href="/pages/chat.php"><span class="chat-link"></span></a>
				</div>
			</div>
			<div class="footer_right">
				<ul>
					<li><a href="/pages/login.php#rules">Правила</a></li>
					<li><a href="">Реклама</a></li>
					<li><a href="/pages/cp.php">Личный кабинет</a></li>
					<?php 
						$tmpURL = "<li><a href=\"/pages/login.php\">Регистрация</a></li><li><a href=\"/pages/login.php\">Вход</a></li>";
						if($user) $tmpURL = "<li><a href=\"\">Избранное</a></li><li><a href=\"/public/logout.php\">Выход</a></li>";
						echo $tmpURL;
						unset($tmpURL);
					?>
				</ul>
				<p>
				Весь материал на сайте представлен исключительно для домашнего ознакомительного просмотра.<br/> В случаях нарушения авторских прав - обращайтесь на почту lupin@anilibria.tv 
				</p>
			</div>
		</div>
		<script src="/js/jquery.min.js"></script>
		<script src="/js/jquery.bxslider.min.js"></script>
		<script src="/js/bootstrap.min.js"></script>
		<script src="/js/main.js"></script>
		<script>console.log("<?php echo pageStat(); ?>");</script>
		<?php 
			if($_SERVER['REQUEST_URI'] == '/pages/login.php' && !$user){
				echo "<script src=\"https://www.google.com/recaptcha/api.js?render={$conf['recaptcha_public']}\"></script>";
			}
			if($_SERVER['REQUEST_URI'] == '/pages/cp.php' && $user){
				echo "
					<script src=\"/js/jquery.Jcrop.min.js\"></script>
					<link rel=\"stylesheet\" href=\"/css/jquery.Jcrop.min.css\">
					<script src=\"/js/uploadAvatar.js\"></script>
					<link rel=\"stylesheet\" type=\"text/css\" href=\"/css/dataTables.bootstrap.min.css\" />
					<script src=\"/js/jquery.dataTables.min.js\"></script>
					<script src=\"/js/dataTables.bootstrap.min.js\"></script>
					<script src=\"/js/tables.js\"></script>					
				";
			}
			if($_SERVER['REQUEST_URI'] == '/pages/chat.php'){					
				if(!empty($_SESSION["sex"]) || !empty($_SESSION["want"])){
					echo "<script src=\"/js/chat.js\"></script>";
				}
			}
		?>
	</body>
</html>
