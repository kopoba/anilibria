<?php
require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

$var['title'] = 'Авторизация, правила';
$var['page'] = 'login';

require($_SERVER['DOCUMENT_ROOT'].'/private/header.php');

if(!$user){
?>
<div class="news-block">
		<div class="news-header">
			<h2 class="news-name" style="float:left;">
				Авторизация
			</h2>
			<h2 class="news-name" id="loginMes" style="float:left; padding-left: 10px;"></h2>
			<div class="clear"></div>	
		</div>
		<div class="clear"></div>
		<div>
			<input class="form-control" id="newMail" placeholder="Email или логин" type="text" required>
			<input class="form-control" id="newPasswd" style="margin-top: 10px;"  type="password" placeholder="Пароль" required>
			<input class="form-control" id="fa2code" style="margin-top: 10px;" type="text" placeholder="Оставьте поле пустым, если вы не настроили двухфакторную аутентификацию">
			<div style="margin-top: 10px;">
				<input class="btn btn btn-success" style="width: 418px;" type="submit" name="login" data-submit-login value="ВХОД ЧЕРЕЗ САЙТ">	
				<a href="<?php echo vkAuthLink(); ?>" role="button" class="btn btn btn-success" style="width: 417px;">ВХОД ЧЕРЕЗ VK.COM</a>
			</div>
		</div>
		<div class="clear"></div>
		<div style="margin-top:10px;"></div>
</div>

<div class="news-block">
		<div class="news-header">
			<h2 class="news-name" style="float:left;">
				Регистрация
			</h2>
			<h2 class="news-name" id="regMes" style="float:left; padding-left: 10px;"></h2>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
		<div>
			<input class="form-control" id="regLogin" placeholder="Логин" type="email" required>
			<input class="form-control" id="regEmail" style="margin-top: 10px;" placeholder="E-mail" type="email" required>
			<input class="form-control" id="regPasswd" style="margin-top: 10px;" placeholder="Пароль" type="password" required>
			<div id="RecaptchaField1" style="margin-top: 10px; display: none;"></div>
			<input class="btn btn btn-success btn-block" style="margin-top: 10px;" type="submit" data-submit-register value="ОТПРАВИТЬ" />
		</div>
		<div class="clear"></div>
		<div style="margin-top:10px;"></div>
</div>

<div class="news-block">
		<div class="news-header" >
			<h2 class="news-name" style="float:left;">
				Восстановить пароль
			</h2>
			<h2 class="news-name" id="lostMes" style="float:left; padding-left: 10px;"></h2>
			<div class="clear"></div>
		</div>
		<div>
			<input class="form-control" id="lostEmail" placeholder="E-mail" type="email" required>
			<div id="RecaptchaField2" style="margin-top: 10px; display: none;"></div>
			<input class="btn btn btn-success btn-block" style="margin-top: 10px;" type="submit" data-submit-passwdrecovery value="ОТПРАВИТЬ" />
		</div>
		<div class="clear"></div>
		<div style="margin-top:10px;"></div>
</div>
<?php } ?>	

<div class="news-block" id="rules">
		<div class="news-header">
			<h2 class="news-name">
				Правила
			</h2>
		</div>
		<div class="clear"></div>
		<div>
			<p>1. <i>Общее общение.</i> Все посты, оставляемые пользователями, должны быть конструктивными и нести смысловую нагрузку, ну или хотя бы быть весьма позитивными, а также не нарушать правил, описанных в данной теме.</p>
			<p>2. <i>Ники. Аватары. Подписи</i>. Запрещено использование нецензурных слов, а также слов, оскорбляющих других пользователей. Запрещена порнография. Ники состоящие только из чисел, содержащие разнообразные символы, только повторяющиеся символы, написанные одновременно на нескольких языках и тому подобное — недопустимы. Слишком большие/яркие подписи также недопустимы. Размещение огромных (как по размеру, так и по весу) картинок — запрещено.<br></p>
			<p>2.1. Запрещено использование ников, идентичных с именитыми фандаберами/членами команды АниЛибрии, если вы ими, конечно, не являетесь.</p>
			<p>3. <i>Флуд. Флейм. Оффтоп. Уродливые сообщения.</i> Сообщения, не имеющие смысла и не относящиеся к теме, жестко караются. Чаще всего флуд — это посты из одного-двух слов, не относящиеся к теме, смайлики и т.п. Так-же удаляются "уродливые сообщения", например такие, в которых цитирование занимает большой объём страницы (больше 10 строчек). </p>
			<p>4. <i>Мат</i> (сюда же входят нецензурные выражения), в том числе и завуалированный. Недопустим в любом виде. Исключения могут быть сделаны только для материалов, в которых невозможно сохранить смысл без мата и ТОЛЬКО с разрешения супермодератора или администрации. При этом обязательно предупреждение, что в материалах содержится мат.</p>
			<p>5. <i>Обсуждение действий модераторов или администрации.</i> Публичные жалобы запрещены. Можно обратиться к администрации с жалобой, в которой подробно расписать, что вас не устраивает (пишите в ЛС любому из администраторов). Если вам не нравится администрация, то можете просто перестать посещать данный ресурс.</p>
			<p>6. <i>Оскорбления.</i> Запрещены любые оскорбления личности.</p>
			<p>7. <i>Реклама.</i> Запрещена любая рекламная и коммерческая деятельность на портале без согласования с администрацией.</p>
			<p>7.1. Запрещаются ссылки на любые ресурсы по озвучке аниме-сериалов и дорам (искл. AniLibria.tv).</p>
		</div>
		<div class="clear"></div>
		<div style="margin-top:10px;"></div>
</div>
	
<?php require($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
