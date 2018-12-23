<?php
$conf['start'] = microtime(true);

$conf['memcache']	= ['/tmp/memcached.socket', 0];

$conf['mysql_host'] = 'localhost';
$conf['mysql_user'] = 'anilibria';
$conf['mysql_pass'] = 'anilibria';
$conf['mysql_base'] = 'anilibria';

$conf['email'] = 'poiuty@poiuty.com';
$conf['email_from'] = 'Test anilibria';

$conf['coinhive_secret'] = 'secret';
$conf['coinhive_public'] = 'public';

// v3
$conf['recaptcha_secret'] = 'secret';
$conf['recaptcha_public'] = 'public';

// v2
$conf['recaptcha2_secret'] = 'secret';
$conf['recaptcha2_public'] = 'public';

$conf['hash_len'] = 64;
$conf['hash_algo'] = 'sha256';
$conf['sign_secret'] = 'secret';

$conf['torrent_secret'] = 'secret';
$conf['torrent_announce'] = 'http://tt.anilibria.tv:2710/announce';

$conf['sphinx_host'] = '127.0.0.1';
$conf['sphinx_port'] = '9306';

$conf['minify'] = true;

$conf['stat_url'] = 'https://ws.poiuty.com/ws/';
$conf['stat_secret'] = 'secret';
