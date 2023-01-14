<?php

require '/var/www/html/private/vendor/AltoRouter/AltoRouter.php';

require '/var/www/html/private/config.php';
require '/var/www/html/private/init/mysql.php';
require '/var/www/html/private/init/var.php';
require '/var/www/html/private/func.php';
require '/var/www/html/private/api.php';

$router = new AltoRouter();
$router->setBasePath('/api2');

header('Content-Type: application/json; charset=utf-8');

// getReleases
$router->map('GET', '/getReleases', function () {
    return _getFullReleasesDataInLegacyStructure();
});

// getTitleByID
$router->map('GET', '/getTitleByID/[:releaseId]', function ($releaseId) {
    return _getReleaseByColumn('id', $releaseId);
});

// getTitleByCode
$router->map('GET', '/getTitleByCode/[:releaseAlias]', function ($releaseAlias) {
    return _getReleaseByColumn('alias', $releaseAlias);
});


// getTitleEpisodesById
$router->map('GET', '/getTitleEpisodesById/[:releaseId]', function ($releaseId) {

    $response = [
        'series' => [],
        'playlist' => [],
    ];

    $releaseEpisodes = json_decode(getApiPlaylist($releaseId) ?? [], true);

    usort($releaseEpisodes, function ($a, $b) {
        return $a['ordinal'] <=> $b['ordinal'];
    });

    $lastEpisode = $releaseEpisodes && $releaseEpisodes[count($releaseEpisodes) - 1] ? $releaseEpisodes[count($releaseEpisodes) - 1] : null;
    $firstEpisode = $releaseEpisodes && $releaseEpisodes[0] ? $releaseEpisodes[0] : null;

    $response['series']['first'] = $firstEpisode ? $firstEpisode['ordinal'] ?? null : null;
    $response['series']['last'] = $lastEpisode ? $lastEpisode['ordinal'] ?? null : null;
    $response['series']['string'] = $firstEpisode && $lastEpisode ? sprintf('%s-%s', $firstEpisode['ordinal'] ?? 0, $lastEpisode['ordinal'] ?? 0) : null;

    foreach ($releaseEpisodes as $episode) {

        $url480 = parse_url($episode['sd']);
        $url720 = parse_url($episode['hd']);
        $url1080 = parse_url($episode['fullhd']);

        $response['playlist'][] = [
            'id' => $episode['id'],
            'uuid' => $episode['uuid'] ?? null,
            '480' => $episode['sd'] ? $url480['path'] ?? null : null,
            '720' => $episode['hd'] ? $url720['path'] ?? null : null,
            '1080' => $episode['fullhd'] ? $url1080['path'] ?? null : null,
            'name' => $episode['name'] ?? null,
            'poster' => $episode['poster'] ?? null,
            'skips' => $episode['skips'],
            'sources' => $episode['sources'] ?? [],
            'rutube_id' => $episode['rutube_id'] ?? null,
            'created_time' => $episode['updated_at'] ?? null,
        ];
    }

    return $response;

});

// getTitleByTorrentID
$router->map('GET', '/getTitleByTorrentID/[:torrentId]', function ($torrentId) {

    global $db;
    $query = $db->prepare('
        SELECT
           t.`release_id` as `rid` 
        FROM `torrents` as t
        INNER JOIN `releases` as r ON r.id = t.release_id 
        WHERE r.`is_hidden` = 0 AND r.`deleted_at` IS NULL AND t.`deleted_at` IS NULL AND t.`id` = :torrentId
    ');
    $query->bindParam('torrentId', $torrentId);
    $query->execute();

    $torrent = $query->fetch(PDO::FETCH_ASSOC);

    return $torrent ? ['rid' => (int)$torrent['rid']] : null;

});

// IsReleaseExists
$router->map('GET', '/IsReleaseExists/[:releaseId]', function ($releaseId) {

    $release = _getReleaseByColumn('id', $releaseId);

    return [
        'is_exists' => is_null($release) === false,
        'releases_id' => $releaseId ? (int)$releaseId : null,
    ];
});

// getTitlesByLastUpdate
$router->map('GET', '/getTitlesByLastUpdate/[i:limit]', function ($limit) {

    global $db;

    $query = $db->prepare(
        sprintf("
            SELECT 
                `id`, 
                UNIX_TIMESTAMP(fresh_at) as `last`, 
                IF(`is_hidden` = 1, 3, IF(`is_ongoing` = 1, 1, IF(`is_completed` = 1, 2, 0))) AS `status`
            
            FROM `releases` 
            WHERE `is_hidden` = 0 AND `deleted_at` IS NULL
            ORDER BY `fresh_at` DESC
            LIMIT %s
        ", $limit && (int)$limit > 0 ? (int)$limit : 999999999999)
    );

    $query->execute();
    $releases = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($releases as $index => $release) {
        $releases[$index] = [
            'id' => (int)$release['id'],
            'last' => (int)$release['last'],
            'status' => (int)$release['status']
        ];
    }

    return $releases;

});

// getTitlesByLastChange
$router->map('GET', '/getTitlesByLastChange/[i:limit]', function ($limit) {

    global $db;

    $query = $db->prepare(
        sprintf("
            SELECT 
                `id`, 
                UNIX_TIMESTAMP(updated_at) as `last_change`, 
                IF(`is_hidden` = 1, 3, IF(`is_ongoing` = 1, 1, IF(`is_completed` = 1, 2, 0))) AS `status`
            
            FROM `releases` 
            WHERE `is_hidden` = 0 AND `deleted_at` IS NULL
            ORDER BY `updated_at` DESC
            LIMIT %s
        ", $limit && (int)$limit > 0 ? (int)$limit : 999999999999)
    );

    $query->execute();
    $releases = $query->fetchAll(PDO::FETCH_ASSOC);


    foreach ($releases as $index => $release) {
        $releases[$index] = [
            'id' => (int)$release['id'],
            'status' => (int)$release['status'],
            'last_change' => (int)$release['last_change'],
        ];
    }

    return $releases;

});


// getTorrentsByLastChange
$router->map('GET', '/getTorrentsByLastChange/[i:limit]', function ($limit) {

    global $db;

    $query = $db->prepare(
        sprintf("
            SELECT 
                `id` AS `torrent_id`, 
                `release_id`,
                UNIX_TIMESTAMP(updated_at) as `last_change`,
                `type`, 
                `quality`,
                `is_hevc`,
                `description`, 
                `size`,
                `hash`
            FROM `torrents` 
            WHERE `deleted_at` IS NULL
            ORDER BY `updated_at` DESC
            LIMIT %s
        ", $limit && (int)$limit > 0 ? (int)$limit : 999999999999)
    );

    $query->execute();
    $torrents = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($torrents as $index => $torrent) {
        $torrents[$index] = array_merge($torrent, [
            'torrent_id' => (int)$torrent['torrent_id'],
            'releases_id' => (int)$torrent['release_id'],
            'last_change' => (int)$torrent['last_change'],
            'is_hevc' => (bool)$torrent['is_hevc'],
            'size' => (int)$torrent['size'],
        ]);
    }

    return $torrents;

});


// getTorrents
$router->map('GET', '/getTorrents/[:releaseId]', function ($releaseId) {
    global $db;
    $query = $db->prepare('
          SELECT 
             t.`id` AS `torrent_id`,
             t.`leechers`, 
             t.`seeders`,
             t.`completed_times` as `completed`,
             UNIX_TIMESTAMP(t.`updated_at`) AS `mtime`,
             UNIX_TIMESTAMP(t.`created_at`) AS `ctime`,
             t.`type`,
             t.`quality`,
             t.`is_hevc`,
             t.`description`,
             t.`size`,
             t.`hash`,
             t.`sort_order`
          
          FROM `torrents` AS t
          INNER JOIN `releases` AS r ON r.`id` = t.`release_id` AND r.`is_hidden` = 0 AND r.`deleted_at` IS NULL
          WHERE r.`id` = :releaseId and t.`deleted_at` IS NULL
          GROUP BY t.`id`
          ORDER BY t.`sort_order` ASC, t.`created_at` ASC
    ');

    $query->bindParam('releaseId', $releaseId);
    $query->execute();

    $torrents = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($torrents as $index => $torrent) {
        $torrents[$index] = array_merge($torrent, [
            'torrent_id' => (int)$torrent['torrent_id'],
            'seeders' => (int)$torrent['seeders'],
            'leechers' => (int)$torrent['leechers'],
            'completed' => (int)$torrent['completed'],
            'hash' => $torrent['hash'] ?? null,
            'is_hevc' => (bool)$torrent['is_hevc'],
            'mtime' => (int)$torrent['mtime'],
            'ctime' => (int)$torrent['ctime'],
            'size' => (int)$torrent['size'],
        ]);
    }

    return $torrents;

});

// getYouTube
$router->map('GET', '/getYouTube/[i:limit]', function ($limit) {

    global $db, $conf;

    $limit = $limit && (int)$limit > 0
        ? (int)$limit
        : 999999999999;

    $query = $db->prepare('
        SELECT 
           `id`, 
           `title`,
           `preview`,
           `video_id` as `vid`,
           UNIX_TIMESTAMP(`created_at`) as `time`,
           `views` as `view`,
           `comments` as `comment`
        FROM `video_contents`
        WHERE `deleted_at` IS NULL
        ORDER BY `created_at` DESC
        LIMIT ' . $limit);

    $query->execute();
    $youtube = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($youtube as $index => $video) {

        $videoPreview = !empty($video['preview']) ? sprintf('%s/%s/%s', $conf['youtube_poster_host'], $video['id'], $video['preview']) : null;
        $videoThumbnail = $videoPreview ? ImageThumbnail::make($videoPreview)->getThumbnail(400) : null;

        $youtube[$index] = array_merge($video, [
            'id' => (int)$video['id'],
            'time' => (int)$video['time'],
            'view' => (int)$video['view'],
            'comment' => (int)$video['comment'],
            'preview' => [
                'src' => $videoPreview,
                'thumbnail' => $videoThumbnail,
            ]
        ]);
    }

    return $youtube;

});

// getGenres
$router->map('GET', '/getGenres', function () {
    global $db;
    $query = $db->prepare('SELECT `id`, `name`, 0 as `rating` FROM `genres` ORDER BY `name`');
    $query->execute();
    $genres = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($genres as $index => $genre) {
        $genres[$index] = array_merge($genre, [
            'id' => (int)$genre['id'],
            'rating' => (int)$genre['rating']
        ]);
    }

    return $genres;

});

// getYears
$router->map('GET', '/getYears', function () {
    global $db;
    $query = $db->prepare('SELECT `year` FROM `releases` WHERE `year` > 0 AND `is_hidden` = 0 AND `deleted_at` IS NULL GROUP BY `year`');
    $query->execute();
    $years = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($years as $index => $year) {
        $years[$index] = array_merge($year, [
            'year' => (int)$year['year']
        ]);
    }

    return $years;

});

// getSchedule
$router->map('GET', '/getSchedule/[:day]', function ($day) {
    global $db;
    $query = $db->prepare('
        SELECT 
            id, 
            IF(is_hidden = 1, 3, IF(is_ongoing = 1, 1, IF(is_completed = 1, 2, 0))) AS `status` 
        FROM `releases` 
        WHERE `is_ongoing` = 1 AND `publish_day` = :day AND `is_hidden` = 0 AND `deleted_at` IS NULL
    ');
    $query->bindParam('day', $day);
    $query->execute();
    $releases = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($releases as $index => $release) {
        $releases[$index] = array_merge($release, [
            'id' => (int)$release['id'],
            'status' => (int)$release['status']
        ]);
    }

    return $releases;

});

// getTorrentSeedStats
$router->map('GET', '/getTorrentSeedStats/[i:limit]', function ($limit) {

    $limit = $limit && (int)$limit > 0 ? (int)$limit : 999999999999;

    ini_set('memory_limit', -1);

    global $db;
    $query = $db->prepare(
        sprintf('
            SELECT 
               `torrents_downloaded` as `downloaded`,
               `torrents_uploaded` as `uploaded`,
               `login` 
            FROM `users`
            WHERE `torrents_uploaded` > 0
            ORDER BY `torrents_uploaded` DESC
            %s',
            "LIMIT " . $limit
        )
    );

    $query->execute();
    $torrents = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($torrents as $index => $torrent) {
        $torrents[$index] = array_merge($torrent, [
            'uploaded' => (int)$torrent['uploaded'],
            'downloaded' => (int)$torrent['downloaded'],
        ]);
    }

    return $torrents;

});

// getUserIdBySession
$router->map('GET', '/getUserIdBySession/[:sessionId]', function ($sessionId) {
    global $db;
    $query = $db->prepare('SELECT `users_id` as `uid` FROM `users_sessions` WHERE `id` = :sessionId');
    $query->bindParam('sessionId', $sessionId);
    $query->execute();
    $session = $query->fetch(PDO::FETCH_ASSOC);

    return $session ? ['uid' => (int)$session['uid']] : null;

});

// getUser
$router->map('GET', '/getUser/[:userId]', function ($userId) {
    global $db;
    $query = $db->prepare('
        SELECT 
            `login`,
            `nickname`,
            `email`,
            `avatar`,
            `vk_id`,
            `patreon_id`
        FROM `users` 
        WHERE `id` = :userId
    ');
    $query->bindParam('userId', $userId);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);

    return array_merge($user, [
        'avatar_original' => getUserAvatarUrl($userId, $user['avatar']),
        'avatar_thumbnail' => getUserAvatarUrl($userId, $user['avatar'])
    ]);
});

// getUserFavorites
$router->map('GET', '/getUserFavorites/[:userId]', function ($userId) {
    global $db;
    $query = $db->prepare('SELECT `releases_id` as `rid` FROM `users_favorites` WHERE `users_id` = :userId ORDER BY `id` ASC');
    $query->bindParam('userId', $userId);
    $query->execute();
    $favorites = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($favorites as $index => $favorite) {
        $favorites[$index] = array_merge($favorite, [
            'rid' => (int)$favorite['rid']
        ]);
    }

    return $favorites ?? [];
});

// addUserFavorite
$router->map('GET', '/addUserFavorite/[:userId]/[:releaseId]', function ($userId, $releaseId) {
    global $db;
    $query = $db->prepare('INSERT INTO `users_favorites`  (`users_id`, `releases_id`, `created_at`, `updated_at`) VALUES (:userId, :releaseId, NOW(), NOW())');
    return $query->execute(['userId' => $userId, 'releaseId' => $releaseId]);
});

// delUserFavorite
$router->map('GET', '/delUserFavorite/[:userId]/[:releaseId]', function ($userId, $releaseId) {
    global $db;
    $query = $db->prepare('DELETE FROM `users_favorites` WHERE `users_id` = :userId and `releases_id` = :releaseId');
    return $query->execute(['userId' => $userId, 'releaseId' => $releaseId]);
});

// buildSearchCache
$router->map('GET', '/buildSearchCache', function () {
    global $db;
    $query = $db->prepare('SELECT `id`, `name`, `name_english` as ename, `name_alternative` as aname, `description` FROM `releases` WHERE `is_hidden` = 0 AND `deleted_at` IS NULL');
    $query->execute();
    $releases = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($releases as $index => $release) {
        $releases[$index] = array_merge($release, [
            'id' => (int)$release['id'],
        ]);
    }

    return $releases;
});


// Get cache servers
$router->map('GET', '/getCacheServers', function () {
    return [
        [
            'id' => 1,
            'name' => 'https://cache.libria.fun',
            'url' => 'https://cache.libria.fun/videos/media',
            'host' => 'cache.libria.fun',
            'updated_at' => strtotime('now'),
            'is_in_rotation' => 1,
            'outgoing_traffic' =>  0,
            'response_seconds' => 0,
        ]
    ];

});


$match = $router->match();
$response = is_array($match) && is_callable($match['target']) ? call_user_func_array($match['target'], $match['params']) : null;

if (empty($response) && !is_array($response)) header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
if (empty($response) === false || is_array($response)) echo json_encode($response);


