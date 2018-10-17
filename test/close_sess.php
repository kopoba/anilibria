<form action="/public/close_sess.php" method="post">
    <input type="text" name="id">
    <input type="submit" value="close session" name="submit">
</form>
<hr/>
<pre>
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

var_dump(show_sess());

?>
</pre>
