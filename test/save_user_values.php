<pre>
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

var_dump($user);
?>
</pre>
<hr/>
<form action="/public/save_user_values.php" method="post">
	<input type="text" name="sex">
	<input type="text" name="vk">
	<input type="text" name="telegram">
	<input type="text" name="steam">
	<input type="text" name="age">
	<input type="text" name="country">
	<input type="text" name="city">
	<input type="submit" value="update user values" name="submit">
</form>
