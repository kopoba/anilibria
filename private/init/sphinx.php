<?php
try {
	$sphinx = new PDO("mysql:host={$conf['sphinx_host']};port={$conf['sphinx_port']};", '', '');
	$sphinx->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	$sphinx->exec("set names utf8");
}
catch(PDOException $e) {
	file_put_contents($_SERVER['DOCUMENT_ROOT'].'/private/logs/PDOErrors.txt', $e->getMessage().PHP_EOL, FILE_APPEND);
	die('MySQL Sphinx ERROR');
}
