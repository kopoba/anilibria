<?php
$conf['start'] = microtime(true);

$conf['memcache'] = ['/tmp/memcached.socket', 0];
$conf['redis'] = 'anilibria.core.redis';  //'/var/run/redis/redis-server.sock';

$conf['cache'] = 'redis';

$conf['mysql_host'] = getenv('MYSQL_HOST') ?? 'secret';
$conf['mysql_user'] = getenv('MYSQL_USER') ?? 'secret';
$conf['mysql_pass'] = getenv('MYSQL_PASSWORD') ?? 'secret';
$conf['mysql_base'] = getenv('MYSQL_DATABASE') ?? 'secret';

// Email
$conf['email_host'] = getenv('EMAIL_HOST') ?? 'email host';
$conf['email_login'] = getenv('EMAIL_LOGIN') ?? 'email login';
$conf['email_sender'] = getenv('EMAIL_SENDER') ?? 'email login';
$conf['email_password'] = getenv('EMAIL_PASSWORD') ?? 'email password';


// v3
$conf['recaptcha_secret'] = getenv('RECAPTCHA3_SECRET') ?? 'secret';
$conf['recaptcha_public'] = getenv('RECAPTCHA3_PUBLIC') ?? 'secret';

// v2
$conf['recaptcha2_secret'] = getenv('RECAPTCHA2_SECRET') ?? 'secret';
$conf['recaptcha2_public'] = getenv('RECAPTCHA2_PUBLIC') ?? 'secret';

$conf['hash_len'] = 64;
$conf['hash_algo'] = 'sha256';

$conf['torrent_secret'] = 'secret';
$conf['torrent_announce'] = getenv('TORRENT_ANNOUNCE') ?? 'secret';

$conf['stat_url'] = getenv('STAT_URL') ?? 'WS Link';
$conf['stat_secret'] = getenv('STAT_SECRET') ?? 'secret';

$conf['nginx_domain'] = 'link';
$conf['nginx_download_cache_server'] = 'link';
$conf['nginx_secret'] = 'secret';

$conf['vk_id'] = getenv('VK_ID') ?? 'id';
$conf['vk_secret'] = getenv('VK_SECRET') ?? 'secret';

$conf['push_all'] = getenv('PUSH_ALL') ?? 'secret';
$conf['push_sanasol'] = getenv('PUSH_SANASOL') ?? 'secret';
$conf['push_albot'] = getenv('PUSH_ALBOT') ?? 'secret';

$conf['player_login'] = getenv('PLAYER_LOGIN') ?? 'login';
$conf['player_passwd'] = getenv('PLAYER_PASSWORD') ?? 'secret';

$conf['cdn'] = true;

$conf['api_v2'] = getenv('API_V2_HOST') ?? 'api v2 host';

// NEXT
$conf['users_avatars_host'] = getenv('USERS_AVATARS_HOST') ?? '/';
$conf['release_poster_host'] = getenv('RELEASE_POSTER_HOST') ?? '/';
$conf['youtube_poster_host'] = getenv('YOUTUBE_POSTER_HOST') ?? '/';
$conf['release_episode_poster_host'] = getenv('RELEASE_EPISODE_POSTER_HOST') ?? '/';
