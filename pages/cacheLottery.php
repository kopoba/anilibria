<?php

require($_SERVER['DOCUMENT_ROOT'] . '/private/config.php');
require($_SERVER['DOCUMENT_ROOT'] . '/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'] . '/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'] . '/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'] . '/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'] . '/private/func.php');
require($_SERVER['DOCUMENT_ROOT'] . '/private/auth.php');


echo '<pre>';
echo sprintf('Previous value: %s', $cache->get('cacheLottery'));
echo PHP_EOL;

if (in_array($user['id'], ['2', '368751', '249035'])) {
    $cache->set('cacheLottery', (int)$_GET['percentage'], 0);
}

echo sprintf('Current value: %s', $cache->get('cacheLottery'));
