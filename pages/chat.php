<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/minify.php');

//var_dump($_SESSION);

//die();

require_once($_SERVER['DOCUMENT_ROOT'].'/private/header.php');

$online = $cache->get('online');
$kun = $cache->get('kun');
$chan = $cache->get('chan');

?>

<style>
	
	.chat-page {

	}
	
	#chat {
		color: #7C7C7C;
		height: 358px;
		overflow: auto;
		word-wrap: break-word;
	}
	
	.textarea {
		display: block;
		width: 100%;
		min-height: 75px;
		padding: 6px 12px;
		font-size: 14px;
		line-height: 1.42857143;
		color: #555;
		background-color: #FFF;
		background-image: none;
		border: 1px solid #ddd;
		-webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
		-o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
		transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
		margin: 10px 0;
		resize: vertical;  
		outline: none;
		-webkit-border-radius: 10px;
		-moz-border-radius: 10px;
		border-radius: 10px;
	}
	
	.loading:after {
		overflow: hidden;
		display: inline-block;
		vertical-align: bottom;
		animation: ellipsis 2s infinite;
		content: "\2026"; /* ascii code for the ellipsis character */
	}
	@keyframes ellipsis {
		from {
			width: 2px;
		}
		to {
			width: 15px;
		}
	}
</style>

<div class="news-block">
	<div class="news-header">
		<h2 class="news-name" style="float:left;">
			Аниме чат
		</h2>
		<h2 class="news-name" id="status" style="float:left; font-size: 15px; margin-left: 200px;"></h2>
		<h2 class="news-name" style="float:right; font-size: 15px;">
			Онлайн: <span id="online"><?php echo $online; ?></span> | Кунов: <span id="online_kun"><?php echo $kun; ?></span> | Тянок: <span id="online_chan"><?php echo $chan; ?></span>
		</h2>
		<div class="clear"></div>
	</div>
	<div>
		<hr style="margin-top: 0px;"/>
		<?php if(!empty($_SESSION["sex"]) || !empty($_SESSION["want"])){ ?>
		<div class="chat-page">
			<div id="chat"></div>
			<div class="send">
				<textarea class="textarea" name="text" id="clean" onkeypress="return runScript(event)"></textarea>
				<center>
					<!--Во время общения-->
					<input type="submit" class="btn btn-default" id="send" value="Отправить">
					<input type="submit" class="btn btn-default" id="end" value="Отключиться">
					<!--До общения-->
					<input type="submit" class="btn btn-default" id="search" value="Поиск">
					<input type="submit" class="btn btn-default" id="ban" value="Игнорировать">
					<input type="submit" class="btn btn-default" id="exit" value="Выход">
					<!--Во время поиска-->
					<input type="submit" class="btn btn-default" id="stop" value="Остановить поиск">
				</center>
			</div>
		</div>	
		<?php }else{ ?>
		<center>Привет, незнакомец! Ты попал в наш уютный и анонимный чат без регистрации.<br/>
			В нашем аниме чате, ты можешь поговорить с незнакомцем на любые темы.
			Добро пожаловать :3
			<hr/>
			<form action="/public/chat.php" method="post">
			Хочу поговорить с &nbsp; <input type="checkbox" name="m" value="1" checked="" style="position:relative;top:2px;">&nbsp; кунами и&nbsp; 
			<input type="checkbox" name="w" value="2" checked="" style="position:relative;top:2px;">&nbsp; тянками, скрыть мой&nbsp; <input type="checkbox" name="an" value="1" style="position:relative;top:2px;">&nbsp;пол.<br/>
				<input type="hidden" name="do" value="register">
				<select class="form-control" style="width: 450px; margin-top: 7px;" name="sex">
					<option value="1">Я кун</option>
					<option value="2">Я тян</option>
				</select>
				<input class="btn btn btn-default btn-block" style="width: 450px; margin-top: 7px;" type="submit" value="Вход">	
			<form>
		</center>
		<?php } ?>
	</div>
	<div class="clear"></div>
	<div class="news_footer"></div>
</div>
<?require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
