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

<form action="/public/release.php" method="post" enctype="multipart/form-data">
	<input type="file" name="poster" accept=".jpeg,.jpg"><br/>
    <input type="text" name="name" placeholder="name"><br/>
    <input type="text" name="ename" placeholder="ename"><br/>
    <input type="text" name="genre" placeholder="genre"><br/>
    <input type="text" name="voice" placeholder="voice"><br/>
    <input type="text" name="translator" placeholder="translator"><br/>
    <input type="text" name="timing" placeholder="timing"><br/>
    <input type="text" name="design" placeholder="design"><br/>
    <input type="text" name="year" placeholder="year"><br/>
    <input type="text" name="season" placeholder="season"><br/>
    <input type="text" name="type" placeholder="type"><br/>
    <textarea rows="6" cols="50" name="description" placeholder="description"></textarea><br/>
    <input type="submit" value="add release" name="submit">
</form>
