<?php

require($_SERVER['DOCUMENT_ROOT'] . '/private/config.php');
require($_SERVER['DOCUMENT_ROOT'] . '/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'] . '/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'] . '/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'] . '/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'] . '/private/func.php');
require($_SERVER['DOCUMENT_ROOT'] . '/private/auth.php');


$fhdQualityIsDisabled = function () use ($cache) {
    return $cache->get('fhdQualityIsDisabled') == 1 ? 'Disabled' : 'Enabled';
};

echo '<pre>';
echo sprintf('FHD status: previous value: %s', $fhdQualityIsDisabled());
echo PHP_EOL;

if (in_array($user['id'], ['2', '368751', '249035'])) {

    if (isset($_GET['isDisabled'])) {
        $cache->set('fhdQualityIsDisabled', $_GET['isDisabled'] == 'true' ? 1 : 0, 0);
    }

    echo sprintf('FHD status: current value: %s', $fhdQualityIsDisabled());
}


