<?php
require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

$var['description'] = 'Смотрите аниме онлайн в любимой озучке.';

require($_SERVER['DOCUMENT_ROOT'].'/private/header.php');

echo youtubeShow();

require($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');
