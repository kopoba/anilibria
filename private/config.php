<?php
$conf['start'] = microtime(true);

$conf['memcache']	= ['/tmp/memcached.socket', 0];
$conf['redis'] = '/var/run/redis/redis-server.sock';

$conf['cache'] = 'redis';

$conf['mysql_host'] = 'localhost';
$conf['mysql_user'] = 'user';
$conf['mysql_pass'] = 'password';
$conf['mysql_base'] = 'database';

$conf['email'] = 'email';
$conf['email_from'] = 'Author';

// v3
$conf['recaptcha_secret'] = 'secret';
$conf['recaptcha_public'] = 'public';

// v2
$conf['recaptcha2_secret'] = 'secret';
$conf['recaptcha2_public'] = 'public';

$conf['hash_len'] = 64;
$conf['hash_algo'] = 'sha256';

$conf['torrent_secret'] = 'secret';
$conf['torrent_announce'] = 'torrent announce link';

$conf['sphinx_host'] = '127.0.0.1';
$conf['sphinx_port'] = '9306';

$conf['stat_url'] = 'wss://link';
$conf['stat_secret'] = 'secret';

$conf['nginx_domain'] = 'link to video server';
$conf['nginx_download_cache_server'] = 'link to mp4 server';
$conf['nginx_secret'] = 'secret';

$conf['vk_id'] = 'vk id';
$conf['vk_secert'] = 'secret';

$conf['youtube_secret'] = 'secret';
$conf['youtube_chanel'] = 'secret';
$conf['youtube_playlist'] = 'secret';
$conf['youtube_playlist_main'] = 'secret';
$conf['youtube_playlist_lupin'] = 'secret';
$conf['youtube_playlist_sharon'] = 'secret';
$conf['youtube_playlist_silv'] = 'secret';

$conf['push_all'] = 'secret';
$conf['push_sanasol'] = 'secret';
$conf['push_albot'] = 'secret';

$conf['telegram'] = 'secret';

$conf['player_login'] = 'login';
$conf['player_passwd'] = 'password';

$conf['cdn'] = true;

$conf['fcm_token'] = "fcm_token";

$conf['api_v2'] = 'http://localhost:99';
