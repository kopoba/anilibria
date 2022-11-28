<?php

require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');

$cache->set('cacheLottery', (int)$_GET['percentage'], 0);
