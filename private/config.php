<?php
$conf['start'] = microtime(true);

$conf['memcache']	= ['/tmp/memcached.socket', 0];
$conf['redis'] = '/var/run/redis/redis-server.sock';

$conf['cache'] = 'redis';

$conf['mysql_host'] = 'secret';
$conf['mysql_user'] = 'secret';
$conf['mysql_pass'] = 'secret';
$conf['mysql_base'] = 'secret';

$conf['email'] = 'email';
$conf['email_from'] = 'Sender';

// v3
$conf['recaptcha_secret'] = 'secret';
$conf['recaptcha_public'] = 'secret';

// v2
$conf['recaptcha2_secret'] = 'secret';
$conf['recaptcha2_public'] = 'secret';

$conf['hash_len'] = 64;
$conf['hash_algo'] = 'sha256';

$conf['torrent_secret'] = 'secret';
$conf['torrent_announce'] = 'link';

$conf['sphinx_host'] = 'ip';
$conf['sphinx_port'] = 'port';

$conf['stat_url'] = 'WS Link';
$conf['stat_secret'] = 'secret';

$conf['nginx_domain'] = 'link';
$conf['nginx_download_cache_server'] = 'link';
$conf['nginx_secret'] = 'secret';

$conf['vk_id'] = 'id';
$conf['vk_secert'] = 'secret';

$conf['youtube_secret'] = 'secret';
$conf['youtube_chanel'] = 'channel';
$conf['youtube_playlist'] = 'playlist';
$conf['youtube_playlist_main'] = 'playlist';
$conf['youtube_playlist_lupin'] = 'playlist';
$conf['youtube_playlist_sharon'] = 'playlist';
$conf['youtube_playlist_silv'] = 'playlist';
$conf['youtube_playlist_dejz'] = 'playlist';

$conf['push_all'] = 'secret';
$conf['push_sanasol'] = 'secret';
$conf['push_albot'] = 'secret';

$conf['telegram'] = 'secret';

$conf['player_login'] = 'login';
$conf['player_passwd'] = 'secret';

$conf['cdn'] = true;

$conf['fcm_token'] = "fcm_token";

$conf['api_v2'] = 'api v2 host';
