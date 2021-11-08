#!/usr/bin/php
<?php

$_SERVER['DOCUMENT_ROOT'] = '/var/www/html';

require('/var/www/html/private/config.php');
require('/var/www/html/private/init/mysql.php');
require('/var/www/html/private/init/memcache.php');
require('/var/www/html/private/init/var.php');
require('/var/www/html/private/func.php');
require('/var/www/html/private/auth.php');
require('/var/www/html/private/api.php');

updateApiCache();
