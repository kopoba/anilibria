<?php

require '/var/www/html/private/vendor/AltoRouter/AltoRouter.php';

require '/var/www/html/private/config.php';
require '/var/www/html/private/init/mysql.php';
require '/var/www/html/private/init/var.php';

$router = new AltoRouter();
$router->setBasePath('/api2');

header('Content-Type: application/json; charset=utf-8');


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
    $query = $db->prepare('SELECT `year` FROM `releases` WHERE `year` > 0 GROUP BY `year`');
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
        WHERE `is_ongoing` = 1 AND `publish_day` = :day
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
    // TODO: INSERT from DB
});

// delUserFavorite
$router->map('GET', '/delUserFavorite/[:userId]/[:releaseId]', function ($userId, $releaseId) {
    // TODO: DELETE from DB
});


// buildSearchCache
$router->map('GET', '/buildSearchCache', function () {
    global $db;
    $query = $db->prepare('SELECT `id`, `name`, `name_english` as ename, `name_alternative` as aname, `description` FROM `releases` WHERE `is_hidden` = 0');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
});


$match = $router->match();
$result = null;


if(is_array($match) && is_callable($match['target'])) {

    echo json_encode(call_user_func_array($match['target'], $match['params']) ?? []);

} else {
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}


