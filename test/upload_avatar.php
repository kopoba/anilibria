<pre>
<?php
require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

var_dump($user);
?>
</pre>
<hr/>
<form action="/public/upload_avatar.php" method="post" enctype="multipart/form-data">
    <input type="file" name="avatar" accept=".jpg">
    <input type="submit" value="upload avatar" name="submit">
</form>
