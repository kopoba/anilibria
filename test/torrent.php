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
<form action="/public/torrent.php" method="post" enctype="multipart/form-data">
    <input type="file" name="torrent" accept=".torrent">
    <input type="text" name="rid">
    <input type="text" name="quality">
    <input type="text" name="episode">
    <input type="hidden" name="do" value="add">
    <input type="submit" value="add torrent" name="submit">
</form>

<hr/>
<form action="/public/torrent.php?do=update" method="post" enctype="multipart/form-data">
	<input type="file" name="torrent" accept=".torrent">
    <input type="text" name="rid">
    <input type="text" name="quality">
    <input type="text" name="episode">
    <input type="text" name="edit_torrent">
    <input type="hidden" name="do" value="update">
    <input type="submit" value="update torrent" name="submit">
</form>

<hr/>
<form action="/public/torrent.php?do=update" method="post" enctype="multipart/form-data">
    <input type="text" name="edit_torrent">
    <input type="hidden" name="do" value="delete">
    <input type="submit" value="delete torrent" name="submit">
</form>
