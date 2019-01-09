<pre>
<?php
require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/sphinx.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

// id, name, ename, genre, voice, season, year
// @name Убийца Гоблинов
// @genre романтика
// @genre экшен, романтика (и)
// @genre экшен|романтика (или)


//$stmt->bindValue(':search', "@name ($search)");
//$stmt->bindValue(':min', (int)$min, PDO::PARAM_INT);
//$stmt->bindValue(':max', (int)$max, PDO::PARAM_INT);

$search = 'goblin"';


// https://github.com/yiisoft/yii2/issues/3668
// https://github.com/yiisoft/yii2/commit/603127712bb5ec90ddc4c461257dab4a92c7178f
$search = str_replace(
	['\\', '/', '"', '(', ')', '|', '-', '!', '@', '~', '&', '^', '$', '=', '>', '<', "\x00", "\n", "\r", "\x1a"],
	['\\\\', '\\/', '\\"', '\\(', '\\)', '\\|', '\\-', '\\!', '\\@', '\\~', '\\&', '\\^', '\\$', '\\=', '\\>', '\\<', "\\x00", "\\n", "\\r", "\\x1a"],
	$search
);

//$query = $sphinx->query("SELECT * FROM anilibria WHERE MATCH('@genre экшен|романтика')");
$query = $sphinx->prepare("SELECT * FROM anilibria WHERE MATCH(:search)");
$query->bindValue(':search', "@(name,ename) ($search)");
$query->execute();
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
