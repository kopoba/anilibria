#!/usr/bin/php
<?php
$_SERVER['DOCUMENT_ROOT'] = '/var/www/anilibria/root';
$_SERVER['REMOTE_ADDR'] = '37.1.217.18';
require('/var/www/anilibria/root/private/config.php');
require('/var/www/anilibria/root/private/init/mysql.php');
require('/var/www/anilibria/root/private/init/memcache.php');
require('/var/www/anilibria/root/private/init/var.php');
require('/var/www/anilibria/root/private/func.php');
require('/var/www/anilibria/root/private/auth.php');
require('/var/www/anilibria/root/private/api.php');

updateApiCache();
