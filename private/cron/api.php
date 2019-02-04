#!/usr/bin/php
<?php
require('/var/www/anilibria.tv/root/private/config.php');
require('/var/www/anilibria.tv/root/private/init/mysql.php');
require('/var/www/anilibria.tv/root/private/init/memcache.php');
require('/var/www/anilibria.tv/root/private/func.php');
require('/var/www/anilibria.tv/root/private/auth.php');
require('/var/www/anilibria.tv/root/private/api.php');

updateApiCache();
