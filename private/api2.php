<?php

require '/var/www/html/private/vendor/AltoRouter/AltoRouter.php';

require '/var/www/html/private/config.php';
require '/var/www/html/private/init/mysql.php';
require '/var/www/html/private/init/var.php';
require '/var/www/html/private/func.php';

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


// getTitleByTorrentID
$router->map('GET', '/getTitleByTorrentID/[:torrentId]', function ($torrentId) {

    global $db;
    $query = $db->prepare('
        SELECT
           t.`releases_id` as `rid` 
        FROM `torrents` as t
        INNER JOIN `releases` as r ON r.id = t.releases_id 
        WHERE r.`is_hidden` = 0 AND r.`deleted_at` IS NULL AND t.`deleted_at` IS NULL AND t.`id` = :torrentId
    ');
    $query->bindParam('torrentId', $torrentId);
    $query->execute();

    return $query->fetch(PDO::FETCH_ASSOC);

});


// IsReleaseExists
$router->map('GET', '/IsReleaseExists/[:releaseId]', function ($releaseId) {

    $release = _getReleaseByColumn('id', $releaseId);

    return [
        'is_exists' => is_null($release) === false,
        'releases_id' => $releaseId,
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
    return $query->fetchAll(PDO::FETCH_ASSOC);

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
    return $query->fetchAll(PDO::FETCH_ASSOC);

});


// getTorrents
$router->map('GET', '/getTorrents/[:releaseId]', function ($releaseId) {
    global $db;
    $query = $db->prepare('
          SELECT 
          
             t.`id` AS `fid`,
             t.`leechers`, 
             t.`seeders`,
             t.`completed`,
             0 AS `flags`,
             UNIX_TIMESTAMP(t.`created_at`) AS `mtime`,
             UNIX_TIMESTAMP(t.`updated_at`) AS `ctime`,
             JSON_ARRAY(CONCAT_WS(" ", t.`type`, t.`quality`, IF(t.`is_hevc` = 1, "HEVC", null)), t.`description`, t.`size`) as `info`
          
          FROM `torrents` AS t
          INNER JOIN `releases` AS r ON r.`id` = t.`releases_id` AND r.`is_hidden` = 0 AND r.`deleted_at` IS NULL
          WHERE r.`id` = :releaseId
    ');

    $query->bindParam('releaseId', $releaseId);
    $query->execute();

    return $query->fetchAll(PDO::FETCH_ASSOC);

});

// getYouTube
$router->map('GET', '/getYouTube/[i:limit]', function ($limit) {

    global $db;

    $limit = $limit && (int)$limit > 0
        ? (int)$limit
        : 999999999999;

    $query = $db->prepare('
        SELECT 
            `id`, 
           `title`,
           `youtube_id` as `vid`,
           UNIX_TIMESTAMP(`created_at`) as `time`,
           `views` as `view`,
           `comments` as `comment`
        FROM `youtube`
        WHERE `deleted_at` IS NULL
        ORDER BY `created_at` DESC
        LIMIT ' . $limit);

    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);

});

// getGenres
$router->map('GET', '/getGenres', function () {
    global $db;
    $query = $db->prepare('SELECT `id`, `name`, 0 as `rating` FROM `genres` ORDER BY `name`');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
});


// getYears
$router->map('GET', '/getYears', function () {
    global $db;
    $query = $db->prepare('SELECT `year` FROM `releases` WHERE `year` > 0 AND `is_hidden` = 0 AND `deleted_at` IS NULL GROUP BY `year`');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
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
    return $query->fetchAll(PDO::FETCH_ASSOC);
});


// getUserIdBySession
$router->map('GET', '/getUserIdBySession/[:sessionId]', function ($sessionId) {
    global $db;
    $query = $db->prepare('SELECT `users_id` as `uid` FROM `users_sessions` WHERE `id` = :sessionId');
    $query->bindParam('sessionId', $sessionId);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
});


// getUserFavorites
$router->map('GET', '/getUserFavorites/[:userId]', function ($userId) {
    global $db;
    $query = $db->prepare('SELECT `releases_id` as `rid` FROM `users_favorites` WHERE `users_id` = :userId');
    $query->bindParam('userId', $userId);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
});

// addUserFavorite
$router->map('GET', '/addUserFavorite/[:userId]/[:releaseId]', function ($userId, $releaseId) {
    global $db;
    $query = $db->prepare('INSERT INTO `users_favorites`  (`id`, `users_id`, `releases_id`, `created_at`, `updated_at`) VALUES (UUID(), :userId, :releaseId, NOW(), NOW())');
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
    return $query->fetchAll(PDO::FETCH_ASSOC);
});


$match = $router->match();
$response = is_array($match) && is_callable($match['target']) ? call_user_func_array($match['target'], $match['params']) : null;

if (empty($response)) header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
if (empty($response) === false) echo json_encode($response);


