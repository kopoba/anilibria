<pre>
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/sphinx.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

// id, name, ename, genre, voice, season, year
// @name Убийца Гоблинов
// @genre романтика
// @genre экшен, романтика (и)
// @genre экшен|романтика (или)

$query = $sphinx->query("SELECT * FROM anilibria WHERE MATCH('@genre экшен|романтика')");
$result = $query->fetchAll();
var_dump($result);


/*

array(3) {
  [0]=>
  array(2) {
    ["id"]=>
    string(1) "1"
    [0]=>
    string(1) "1"
  }
  [1]=>
  array(2) {
    ["id"]=>
    string(1) "2"
    [0]=>
    string(1) "2"
  }
  [2]=>
  array(2) {
    ["id"]=>
    string(1) "3"
    [0]=>
    string(1) "3"
  }
}

*/
