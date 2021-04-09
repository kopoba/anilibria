<?php
$conf['start'] = microtime(true);

$conf['memcache']	= ['/tmp/memcached.socket', 0];
$conf['redis'] = '/var/run/redis/redis-server.sock';

$conf['cache'] = 'redis';

$conf['mysql_host'] = 'secret';
$conf['mysql_user'] = 'secret';
$conf['mysql_pass'] = 'secret';
$conf['mysql_base'] = 'secret';

$conf['email'] = 'secret';
$conf['email_from'] = 'secret';

// v3
$conf['recaptcha_secret'] = 'secret';
$conf['recaptcha_public'] = 'secret';

// v2
$conf['recaptcha2_secret'] = 'secret';
$conf['recaptcha2_public'] = 'secret';

$conf['hash_len'] = 64;
$conf['hash_algo'] = 'sha256';

$conf['torrent_secret'] = 'secret';
$conf['torrent_announce'] = 'secret';

$conf['sphinx_host'] = '127.0.0.1';
$conf['sphinx_port'] = '9306';

$conf['stat_url'] = 'wss://socket.anilibria.tv/ws/';
$conf['stat_secret'] = 'secret';

$conf['nginx_domain'] = 'secret';
$conf['nginx_download_cache_server'] = 'secret';
$conf['nginx_secret'] = 'secret';

$conf['vk_id'] = 'secret';
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

$conf['player_login'] = 'secret';
$conf['player_passwd'] = 'secret';

$conf['cdn'] = true;

$conf['fcm_token'] = "secret";

$conf['api_v2'] = 'secret';
