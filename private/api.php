<?php

$APP_ID_ANDROID_TV = "ru.radiationx.anilibria.app.tv";

function safeApiList() // DONE
{
    wrapApiResponse(function () {
        return apiList();
    });
}

function unsafeApiList() // DONE
{
    $response = apiList();
    header('Content-Type: application/json');
    die(json_encode($response, JSON_UNESCAPED_UNICODE/* | JSON_PRETTY_PRINT*/));
}

function wrapApiResponse($func) // DONE
{
    $response = (new ApiResponse())->proceed($func);
    header('Content-Type: application/json');
    die(json_encode($response, JSON_UNESCAPED_UNICODE/* | JSON_PRETTY_PRINT*/));
}

function exitAuth() // DONE
{
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

function apiList()
{
    //only for testing
    //updateApiCache();

    global $cache,
           $var,
           $count,
           $info,
           $torrent,
           $APP_ID_ANDROID_TV;
    $result = [];

    // Для этих методов основная апишка не обязательна
    if (isset($_POST['query'])) {
        switch ($_POST['query']) {
            case 'app_update':
                $appIdHeader = getallheaders()['App-Id'] ?? "";
                $version = $var['app_version'];
                $path = "/private/app_updates/version_$version.txt";
                if ($appIdHeader == $APP_ID_ANDROID_TV) {
                    $version = $var['app_tv_version'];
                    $path = "/private/app_tv_updates/version_$version.txt";
                }
                $src = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $path);
                return json_decode($src, true);
                break;

            case 'config':
                $src = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/private/app_updates/config.txt");
                return json_decode($src, true);
                break;

            case 'donation_details':
                $src = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/private/app_updates/donation_info.txt");
                return json_decode($src, true);
                break;

            case 'empty':
                return [];
                break;
        }
    }

    /* Checking cache */
    function fetchNormalCache() // DONE
    {
        global $cache, $count, $info, $torrent;
        $count = $cache->get('apiInfo');
        $info = [];
        $torrent = json_decode($cache->get('apiTorrent'), true);
        for ($i = 0; $i < $count; $i++) {
            $tmp = json_decode($cache->get("apiInfo$i"), true);
            if (is_array($tmp)) {
                foreach ($tmp as $k => $v) {
                    $info["$k"] = $v;
                }
            }
        }
    }

    function fetchInfiniteCache() // DONE
    {
        global $cache, $count, $info, $torrent;
        $count = $cache->get('infiniteApiInfo');
        $info = [];
        $torrent = json_decode($cache->get('infiniteApiTorrent'), true);
        for ($i = 0; $i < $count; $i++) {
            $tmp = json_decode($cache->get("infiniteApiInfo$i"), true);
            if (is_array($tmp)) {
                foreach ($tmp as $k => $v) {
                    $info["$k"] = $v;
                }
            }
        }
    }

    function checkApiRelaxing() // DONE
    {
        global $info, $torrent;
        return $info === null || $torrent === null || $info === false || $torrent === false;
    }


    ////////////////////// HOTFIX
    if (isset($_POST['query']) && in_array($_POST['query'], ['random_release']) === false) {
        fetchNormalCache(); // DONE
        if (checkApiRelaxing()) { // DONE
            fetchInfiniteCache();
            if (checkApiRelaxing()) {
                throw new ApiException('API is not ready', 400);
            }
        }
    }


    /* Main api methods */
    function apiGetTeams()
    {
        $rawTeams = getTeams();
        $teams = [];
        foreach ($rawTeams as $rawTeam) {
            $users = [];
            foreach ($rawTeam['users'] ?? [] as $rawUser) {
                $user = [
                    'nickname' => $rawUser['nickname'],
                    'roles' => $rawUser['roles'] ?? [],
                    'is_intern' => $rawUser['is_intern'] === true,
                    'is_vacation' => $rawUser['is_vacation'] === true
                ];
                $users[] = $user;
            }

            $team = [
                'title' => $rawTeam['title'],
                'description' => $rawTeam['description'],
                'users' => $users
            ];
            $teams[] = $team;
        }

        $headerRoles = [
            ['title' => 'Войсеры', 'color' => '#339966'],
            ['title' => 'Технари', 'color' => '#800000'],
            ['title' => 'Переводчики', 'color' => '#ebd800'],
            ['title' => 'Оформители', 'color' => '#ff6600'],
            ['title' => 'Релизёры', 'color' => '#b523c5'],
            ['title' => 'Сидеры', 'color' => '#000080'],
            ['title' => 'Дизайнеры', 'color' => '#33cccc']
        ];

        return [
            'header_roles' => $headerRoles,
            'teams' => $teams
        ];
    }

    function apiGetTorrentsMap($torrents, $idsString) // DONE
    {
        global $db;

        $result = [];
        $ids = array_unique(explode(',', $idsString));

        if (!empty($ids)) {

            $query = $db->prepare("SELECT * from torrents WHERE `releases_id` IN (" . implode(', ', array_fill(0, count($ids), '?')) . ")");
            $query->execute(array_values($ids));

            $torrents = $query->fetchAll();
            foreach ($torrents as $torrent) {
                $result[$torrent['releases_id']][] = _transformTorrentData($torrent);
            }

            /*foreach ($ids as $id) {
                if (array_key_exists($id, $torrents)) {
                    $result["$id"] = $torrents["$id"];
                }
            }*/
        }
        return $result;
    }

    function apiGetTorrentsList($torrents, $id) // DONE
    {
        if (array_key_exists($id, $torrents)) {
            return $torrents["$id"];
        }
        return [];
    }

    function apiGetReleaseById($info, $torrent, $rid) // DONE
    {
        $result = null;

        if (array_key_exists($rid, $info)) {
            $releases = array($info[$rid]);

            $proceededReleases = proceedReleases($releases, $torrent);
            if (!empty($proceededReleases)) {
                $result = $proceededReleases[0];
            }
        }
        if ($result) {
            return $result;
        }
        throw new ApiException("Release by id=$rid not found", 404);
    }

    function apiGetReleaseByCode($info, $torrent, $rcode) // DONE
    {
        $result = null;
        foreach ($info as $key => $val) {
            if ($val['code'] == $rcode) {
                $releases = array($val);
                $proceededReleases = proceedReleases($releases, $torrent);
                if (!empty($proceededReleases)) {
                    $result = $proceededReleases[0];
                }
                break;
            }
        }
        if ($result) {
            return $result;
        }
        throw new ApiException("Release by code=$rcode not found", 404);
    }

    function apiGetReleasesByIdsString($info, $torrent, $rid) // DONE
    {
        $list = [];
        if (!empty($rid)) {
            $list = array_unique(explode(',', $rid));
        }
        return apiGetReleasesByIdsArray($info, $torrent, $list);
    }

    function apiGetCatalog($info, $torrent, $items) // DONE
    {
        if (!isset($items)) {
            $items = [];
        }
        $ids = array_map(function ($item) {
            return $item['id'];
        }, $items);
        $pagination = createPagination(count($info));
        $items = apiGetReleasesByIdsArray($info, $torrent, $ids);
        return [
            'items' => $items,
            'pagination' => proceedPagination($pagination)
        ];
    }

    function apiSearchReleases($info, $torrent, $items) // DONE
    {
        if (!isset($items)) {
            $items = [];
        }
        $ids = array_map(function ($item) {
            return $item['id'];
        }, $items);
        return apiGetReleasesByIdsArray($info, $torrent, $ids);
    }

    function apiGetReleasesByIdsArray($info, $torrent, $ids) // DONE
    {
        $releases = [];
        foreach ($ids as $id) {
            if (!array_key_exists($id, $info)) {
                continue;
            }
            $releases[] = $info["$id"];
        }
        return proceedReleases($releases, $torrent);
    }

    function apiGetReleases($info, $torrent) // DONE
    {
        $pagination = createPagination(count($info));
        $startIndex = $pagination['startIndex'];
        $perPage = $pagination['perPage'];
        $releases = array_slice($info, $startIndex, $perPage);
        $items = proceedReleases($releases, $torrent);
        return [
            'items' => $items,
            'pagination' => proceedPagination($pagination)
        ];
    }

    function proceedPagination($pagination) // DONE
    {
        unset($pagination['startIndex'], $pagination['endIndex']);
        $pagination['page'] = $pagination['page'] + 1;
        $pagination['allPages'] = $pagination['allPages'] + 1;
        return $pagination;
    }

    function preparePagination() // DONE
    {
        $startIndex = 0;
        $endIndex = 0;
        $page = 0;
        $perPage = 10;
        if (!empty($_POST['page'])) {
            $page = intval($_POST['page']);
            if ($page <= 1) {
                $page = 0;
            } else {
                $page = $page - 1;
            }
        }

        if (!empty($_POST['perPage'])) {
            $perPage = intval($_POST['perPage']);
            if ($perPage <= 0) {
                $perPage = 1;
            }
        }

        $startIndex = $perPage * $page;
        $endIndex = $startIndex + $perPage - 1;

        return [
            'page' => $page,
            'perPage' => $perPage,
            'startIndex' => $startIndex,
            'endIndex' => $endIndex
        ];
    }

    function createPagination($allItemsCount) // DONE
    {
        $prepared = preparePagination();
        return [
            'page' => $prepared['page'],
            'perPage' => $prepared['perPage'],
            'startIndex' => $prepared['startIndex'],
            'endIndex' => $prepared['endIndex'],
            'allPages' => intval($allItemsCount / $prepared['perPage']),
            'allItems' => $allItemsCount
        ];
    }

    function proceedReleases($releases, $torrent) // DONE
    {
        $result = [];
        $filter = [
            'code',
            'names',
            'series',
            'poster', /*'rating',*/
            'last',
            'moon',
            'status',
            'type',
            'genres',
            'voices',
            'year',
            'day',
            'description',
            'announce', /*'blockedInfo',*/
            'playlist',
            'externalPlaylist',
            'torrents',
            'favorite'
        ];

        foreach ($releases as $key => $val) {
            $unsettedFileds = [];
            $names = $val['names'];
            if (isset($_POST['filter'])) {
                $filterList = array_unique(explode(',', $_POST['filter']));

                foreach ($filter as $v) {
                    if (!isset($_POST['rm'])) {
                        if (!in_array($v, $filterList)) {
                            $unsettedFileds[] = "$v";
                            unset($val["$v"]);
                        }
                    } else {
                        if (in_array($v, $filterList)) {
                            $unsettedFileds[] = "$v";
                            unset($val["$v"]);
                        }
                    }
                }
            }


            if (!empty($val['blockedInfo'])) {
                $val['blockedInfo']['blocked'] = isBlock($val['blockedInfo']['blocked']);
            }

            if (!empty($val['blockedInfo'])) {
                $val['blockedInfo']['blocked'] = false;
            }

            /*if (!empty($val['playlist']['online'])) {
                $host = anilibria_getHost($val['playlist']['online']);
                foreach ($val['playlist'] as $k => $v) {
                    if (empty($val['playlist']["$k"]['sd']) || empty($val['playlist']["$k"]['hd'])) {
                        continue;
                    }
                    $val['playlist']["$k"]['sd'] = str_replace('{host}', $host, $val['playlist']["$k"]['sd']);
                    $val['playlist']["$k"]['hd'] = str_replace('{host}', $host, $val['playlist']["$k"]['hd']);
                    if (isset($val['playlist']["$k"]['fullhd'])) {
                        $val['playlist']["$k"]['fullhd'] = str_replace('{host}', $host,
                            $val['playlist']["$k"]['fullhd']);
                    }
                    if (!empty($val['playlist']["$k"]['file'])) {
                        $epNumber = $val['playlist']["$k"]['id'];
                        $epName = $names[1];
                        $val['playlist']["$k"]['srcSd'] = mp4_link($val['playlist']["$k"]['file'] . '-sd.mp4') . "?download=$epName-$epNumber-sd.mp4";
                        $val['playlist']["$k"]['srcHd'] = mp4_link($val['playlist']["$k"]['file'] . '.mp4') . "?download=$epName-$epNumber-hd.mp4";

                        unset($val['playlist']["$k"]['file']);


                        // Временно убираем ссылки на скачивание
                        unset($val['playlist']["$k"]['srcSd']);
                        unset($val['playlist']["$k"]['srcHd']);

                        // Временная заглушка о причинах отключения скачивания
                        $val['playlist']["$k"]['srcHd'] = "https://vk.com/anilibria?w=wall-37468416_493445";
                    }
                }
            }*/


            if (!in_array('torrents', $unsettedFileds)) {
                $val['torrents'] = apiGetTorrentsList($torrent, $val['id']);
            }

            if (!in_array('favorite', $unsettedFileds)) {
                $val['favorite'] = apiGetFavoriteField($val);
            }

            unset($val['rating']);
            unset($val['playlist']['online']);

            $result[] = $val;
        }
        return $result;
    }

    function apiGetFavoriteField($release) // DONE
    {
        global $user, $db;

        //$count = countRatingRelease($release['id']);

        $query = $db->prepare('SELECT `rating_by_favorites` from `releases` WHERE id = :id');
        $query->bindValue(':id', $release['id']);
        $query->execute();
        $result = $query->fetch();

        return [
            'rating' => intval($result['rating_by_favorites'] ?? 0),
            'added' => isFavorite($user['id'], $release['id'])
        ];
    }

    function apiFavorites($info, $torrent) // DONE
    {
        global $db, $user;
        $favIds = [];
        if ($user) {
            $query = $db->prepare('SELECT `releases_id` AS `rid` FROM `users_favorites` WHERE `users_id` = :uid ORDER BY id ASC');
            $query->bindParam(':uid', $user['id']);
            $query->execute();
            while ($row = $query->fetch()) {
                $favIds[] = $row['rid'];
            }
        } else {
            exitAuth();
            throw new ApiException("No user", 401);
        }
        $favReleases = [];
        foreach ($favIds as $favId) {
            if (!array_key_exists("$favId", $info)) {
                continue;
            }
            $favReleases["$favId"] = $info["$favId"];
        }
        $_POST['perPage'] = '9999';
        $result = apiGetReleases($favReleases, $torrent);
        $result['items'] = array_reverse($result["items"]);
        return $result;
    }

    function apiGetUser() // DONE
    {
        global $db, $conf, $user;

        if (!$user) {
            exitAuth();
            throw new ApiException("No user", 401);
        }

        $tmpAvatar = empty($user['avatar'])
            ? '/upload/avatars/noavatar.jpg'
            : sprintf('%s/%s/%s/%s', $conf['users_avatars_host'], floor($user['id'] / 100), $user['id'], $user['avatar']);


        $result = [
            "id" => intval($user['id']),
            "login" => $user['login'],
            "avatar" => $tmpAvatar
        ];

        $appStoreHeader = getallheaders()['Store-Published'] ?? null;
        if ($appStoreHeader == "Apple") {
            //$result["playerEnabled"] = $user['login'] != "example";
            //270620 - это example юзер для модераторов appstore
            //$result["playerEnabled"] = intval($user['id']) != 270620;
            $result["playerEnabled"] = false;
        }
        return $result;
    }

    function releaseFavoriteAction($info, $torrent) // DONE
    {
        global $db, $user;
        if (!$user) {
            exitAuth();
            throw new ApiException("No user", 401);
        }
        if (empty($_POST['id'])) {
            throw new ApiException("No release id", 400);
        }
        if (empty($_POST['action'])) {
            throw new ApiException("No action", 400);
        }
        if (!array_key_exists($_POST['id'], $info)) {
            throw new ApiException("Release not found", 404);
        }

        $isFavorite = isFavorite($user['id'], $_POST['id']);

        switch ($_POST['action']) {
            case 'add':
                if ($isFavorite) {
                    throw new ApiException("Already added", 400);
                }
                $query = $db->prepare('INSERT INTO `users_favorites` (`users_id`, `releases_id`, `created_at`, `updated_at`) VALUES (:uid, :rid, NOW(), NOW())');
                $query->bindParam(':uid', $user['id']);
                $query->bindParam(':rid', $_POST['id']);
                $query->execute();
                break;

            case 'delete':
                if (!$isFavorite) {
                    throw new ApiException("Already deleted", 400);
                }
                $query = $db->prepare('DELETE FROM `users_favorites` WHERE `users_id` = :uid AND `releases_id` = :rid');
                $query->bindParam(':uid', $user['id']);
                $query->bindParam(':rid', $_POST['id']);
                $query->execute();
                break;
        }

        return apiGetReleaseById($info, $torrent, $_POST['id']);
    }

    function getRawFeed() // DONE
    {
        global $db;
        $result = [];

        $pagination = preparePagination();
        $startIndex = intval($pagination['startIndex']);
        $perPage = intval($pagination['perPage']);


        $sql = "
            SELECT `type`, `id`, `timestamp` FROM (
              SELECT 'release' as `type`, `id` as id, UNIX_TIMESTAMP(`fresh_at`) as `timestamp` FROM `releases` WHERE `is_hidden` = 0 and `deleted_at` IS NULL
              UNION
              SELECT 'youtube' as `type`, `id` as id, UNIX_TIMESTAMP(`created_at`) as `timestamp` FROM `video_contents` WHERE `deleted_at` IS NULL                                   
            )
            AS feed
            ORDER BY `timestamp` DESC 
            LIMIT :start_index, :per_page
        ";

        //$releaseQueryStr = "SELECT 'release' as type, `id` as id, UNIX_TIMESTAMP(`fresh_at`) as timestamp FROM `releases`";
        //$youtubeQueryStr = "SELECT 'youtube' as type, `id` as id, UNIX_TIMESTAMP(`created_at`) as timestamp FROM `youtube`";
        /*$feedQueryStr = "SELECT `type`, `id`, `timestamp` FROM
                (
                    $releaseQueryStr WHERE 1 AND `status` != 3 UNION $youtubeQueryStr WHERE 1) AS feed";
        $queryStr = "$feedQueryStr ORDER BY timestamp DESC LIMIT :start_index, :per_page";*/

        $query = $db->prepare($sql);
        $query->bindParam(":start_index", $startIndex, \PDO::PARAM_INT);
        $query->bindParam(":per_page", $perPage, \PDO::PARAM_INT);
        $query->execute();

        while ($row = $query->fetch()) {
            $result[] = [
                'id' => intval($row['id']),
                'type' => $row['type'],
                'timestamp' => intval($row['timestamp'])
            ];
        }

        return $result;
    }

    function apiGetFeed($info, $torrent) // DONE
    {
        global $db;
        $result = [];
        $rawFeed = getRawFeed();

        foreach ($rawFeed as $feedItem) {
            switch ($feedItem['type']) {
                case 'release':
                    try {
                        $result[] = ['release' => apiGetReleaseById($info, $torrent, $feedItem['id'])];
                    } catch (ApiException $ignore) {

                    }
                    break;

                case 'youtube':
                    $query = $db->prepare('SELECT *, UNIX_TIMESTAMP(`created_at`) as `created_at` FROM `video_contents` WHERE `id` = :id');
                    $query->bindParam(':id', $feedItem['id']);
                    $query->execute();
                    if ($row = $query->fetch()) {
                        $result[] = ['youtube' => createYoutubeFromRow($row)];
                    }
                    break;
            }
        }

        return $result;
    }

    function createYoutubeFromRow($row) // DONE
    {
        global $conf;
        return [
            'id' => intval($row['id']),
            'title' => html_entity_decode(html_entity_decode(trim($row['title']))),
            'image' => !empty($row['preview'])
                ? ImageThumbnail::make(sprintf('%s/%s/%s', $conf['youtube_poster_host'], $row['id'], $row['preview']))->getThumbnail(400)
                : null,
            'vid' => $row['video_id'],
            'views' => intval($row['views']),
            'comments' => intval($row['comments']),
            'timestamp' => intval($row['created_at'])
        ];
    }

    function apiGetYoutube() // DONE
    {
        global $db;
        $countQuery = $db->query('SELECT COUNT(*) FROM `video_contents` WHERE `deleted_at` IS NULL');
        $count = intval($countQuery->fetch()[0]);

        $pagination = createPagination($count);
        $startIndex = (int)$pagination['startIndex'];
        $perPage = (int)$pagination['perPage'];

        $result = [];
        $query = $db->prepare("SELECT *, UNIX_TIMESTAMP(created_at) as created_at FROM `video_contents` WHERE `deleted_at` IS NULL ORDER BY `created_at` DESC LIMIT :start_index, :per_page");
        $query->bindParam(":start_index", $startIndex, \PDO::PARAM_INT);
        $query->bindParam(":per_page", $perPage, \PDO::PARAM_INT);
        $query->execute();
        while ($row = $query->fetch()) {
            $result[] = createYoutubeFromRow($row);
        }

        return [
            'items' => $result,
            'pagination' => proceedPagination($pagination)
        ];
    }

    function apiGetGenres() // DONE
    {
        global $db;
        $result = [];
        $query = $db->query('SELECT `name` from `genres` ORDER BY `name`');
        while ($row = $query->fetch()) {
            $result[] = $row['name'];
        }
        //sort($result);
        return $result;
    }

    function apiGetYears() // DONE
    {
        //global $sphinx, $cache;
        global $db;

        $years = [];
        $query = $db->prepare('SELECT `year` FROM `releases` WHERE `is_hidden` = 0 AND deleted_at IS NULL GROUP BY `year` ORDER BY `year` DESC');
        $query->execute();
        foreach ($query->fetchAll() as $item) $years[] = $item['year'];

        return $years;
        /*$result = json_decode($cache->get('apiYears'), true);;
        if ($result === null || $result === false) {
            $result = [];
            $arr = array_reverse(range(1990, date('Y', time())));
            foreach ($arr as $search) {
                $query = $sphinx->prepare("SELECT `id` FROM anilibria WHERE MATCH(:search) LIMIT 1");
                $query->bindValue(':search', "@(year) ($search)");
                $query->execute();
                if ($query->rowCount() > 0) {
                    $result[] = strval($search);
                }
            }
            $cache->set('apiYears', json_encode($result), 300);
        }
        return $result;*/
    }

    function apiGetSocialAuth() // DONE
    {
        $result = [];

        $result[] = [
            'key' => 'vk',
            'title' => 'ВКонтакте',
            'socialUrl' => 'https://oauth.vk.com/authorize?client_id=5315207&redirect_uri=https://www.anilibria.tv/public/vk.php',
            'resultPattern' => 'https?:\/\/.+?\/public\/vk\.php([?&]code)',
            'errorUrlPattern' => 'https?:\/\/.+?\/pages\/vk\.php'
        ];
        return $result;
    }

    function apiGetSchedule($info, $torrent) // DONE
    {
        global $db, $var;
        $result = [];
        foreach ($var['day'] as $key => $val) {
            $query = $db->prepare('SELECT `id` FROM `releases` WHERE `publish_day` = :day AND `is_ongoing` = 1 AND `is_hidden` = 0 AND `deleted_at` IS NULL');
            $query->bindParam(':day', $key);
            $query->execute();
            $dayReleases = [];
            while ($row = $query->fetch()) {
                try {
                    $dayReleases[] = apiGetReleaseById($info, $torrent, $row['id']);
                } catch (ApiException $ignore) {

                }
            }
            $result[] = [
                'day' => $key,
                'items' => $dayReleases
            ];
        }
        return $result;
    }

    function apiGetRandomRelease() // DONE
    {
        $randomCode = randomRelease();
        return [
            'code' => $randomCode
        ];
    }

    function apiGetLinkMenu() // DONE
    {
        return [
            LinkMenuItem::absoluteLink("Группа VK", "https://vk.com/anilibria", LinkMenuItem::$IC_VK),
            LinkMenuItem::absoluteLink("Канал YouTube", "https://youtube.com/channel/UCuF8ghQWaa7K-28llm-K3Zg",
                LinkMenuItem::$IC_YOUTUBE),
            LinkMenuItem::absoluteLink("Patreon", "https://patreon.com/anilibria", LinkMenuItem::$IC_PATREON),
            LinkMenuItem::absoluteLink("Канал Telegram", "https://t.me/anilibria_tv", LinkMenuItem::$IC_TELEGRAM),
            LinkMenuItem::absoluteLink("Чат Discord", "https://discord.gg/M6yCGeGN9B", LinkMenuItem::$IC_DISCORD),
            LinkMenuItem::absoluteLink("Сайт AniLibria", "https://www.anilibria.tv/", LinkMenuItem::$IC_ANILIBRIA),
            LinkMenuItem::absoluteLink("Наши приложения", "https://anilibria.app/", LinkMenuItem::$IC_ANILIBRIA),
        ];
    }

    function apiGetReservedTestResponse() // DONE
    {
        //return getallheaders()['Store-Published'] ?? NULL;
        //return getallheaders();
        global $var;
        return [
            $var['origin_url'],
            getallheaders()['X-Proxy-Origin'] ?? null,
            $_SERVER['HTTP_HOST']
        ];
    }

    function apiGetOtp($data) // DONE
    {
        global $var;
        $remainingTime = $data['expired_at'] - $var['time'];
        return [
            "code" => $data['code'],
            "expiredAt" => $data['expired_at'],
            "description" => "Откройте на компьютере или в мобильном приложении свой профиль и введите код.\nМобильное приложение должно быть обновлено до актуальной версии.",
            "remainingTime" => $remainingTime,
            "rawData" => $data
        ];
    }

    function proceedBridge($funcSrc, $funcDst) // DONE
    {
        register_shutdown_function(function () use ($funcSrc, $funcDst) {
            // Получаем то, что было выведено во время работы $funcSrc
            $message = ob_get_contents();
            ob_end_clean();
            // Оборачиваем результат в баозовый ответ
            wrapApiResponse(function () use ($message, $funcDst) {
                $messageJson = json_decode($message, true);
                if (!empty($messageJson['err']) && $messageJson['err'] !== 'ok') {
                    throw new ApiException($messageJson['mes'] ?: $messageJson['key'], 400, $messageJson['key']);
                }
                // Выполняем функцию, которая обрабатывает данные, которые вывела $funcSrc
                return $funcDst($messageJson);
            });
        });
        ob_start();
        // Выполняем функцию, которая только выводит данные (функции из func.php)
        $_POST['json'] = '';
        $funcSrc();
        ob_end_clean();
    }

    function checkIsStringOrInteger($value, $key) // DONE
    {
        $type = gettype($value);
        if ($type != "string" && $type != "integer") {
            throw new ApiException("Invalid type for $key", 400);
        }
    }

    function checkIsString($value, $key) // DONE
    {
        $type = gettype($value);
        if ($type != "string") {
            throw new ApiException("Invalid type for $key", 400);
        }
    }

    if (isset($_GET['query'])) {
        switch ($_GET['query']) {
            case 'api_empty':
                return [];
                break;
        }
    }

    if (isset($_POST['query'])) {
        switch ($_POST['query']) {
            case 'teams':
                return apiGetTeams();
                break;

            case 'torrent':  // DONE
                if (!empty($_POST['id'])) {
                    checkIsStringOrInteger($_POST['id'], 'id');
                    return apiGetTorrentsMap($torrent, $_POST['id']);
                } else {
                    return $torrent;
                }
                break;

            case 'info': // DONE
                checkIsStringOrInteger($_POST['id'], 'id');
                return apiGetReleasesByIdsString($info, $torrent, $_POST['id']);
                break;

            case 'release': // DONE
                if (!empty($_POST['id'])) {
                    checkIsStringOrInteger($_POST['id'], 'id');

                    return apiGetReleaseById($info, $torrent, $_POST['id']);

                } elseif (!empty($_POST['code'])) {
                    checkIsString($_POST['code'], 'code');
                    return apiGetReleaseByCode($info, $torrent, $_POST['code']);
                } else {
                    throw new ApiException("No id or code for release", 400);
                }
                break;

            case 'random_release': // DONE
                return apiGetRandomRelease();
                break;

            case 'list': // DONE
                return apiGetReleases($info, $torrent);
                break;

            case 'schedule': // DONE
                return apiGetSchedule($info, $torrent);
                break;

            case 'feed': // DONE
                return apiGetFeed($info, $torrent);
                break;

            case 'genres': // DONE
                return apiGetGenres();
                break;

            case 'years': // DONE
                return apiGetYears();
                break;

            case 'favorites': // DONE
                if (!empty($_POST['id']) || !empty($_POST['action'])) {
                    checkIsStringOrInteger($_POST['id'], 'id');
                    checkIsString($_POST['action'], 'action');
                    return releaseFavoriteAction($info, $torrent);
                } else {
                    return apiFavorites($info, $torrent);
                }
                break;

            case 'youtube': // DONE
                return apiGetYoutube();
                break;

            case 'user': // DONE
                return apiGetUser();
                break;

            case 'catalog': // DONE
                return proceedBridge(
                    function () {
                        showCatalog();
                    },
                    function ($bridgeData) use ($info, $torrent) {
                        return apiGetCatalog($info, $torrent, $bridgeData['table']);
                    }
                );
                break;

            case 'search': // DONE
                return proceedBridge(
                    function () {
                        xSearch();
                    },
                    function ($bridgeData) use ($info, $torrent) {
                        return apiSearchReleases($info, $torrent, $bridgeData['mes']);
                    }
                );
                break;

            case 'vkcomments': // DONE
                return [
                    'baseUrl' => 'https://www.anilibria.tv/',
                    'script' => '<div id="vk_comments"></div><script type="text/javascript" src="https://vk.com/js/api/openapi.js?160" async onload="VK.init({apiId: 5315207, onlyWidgets: true}); VK.Widgets.Comments(\'vk_comments\', {limit: 8, attach: false});" ></script>'
                ];
                break;

            case 'social_auth': // DONE
                return apiGetSocialAuth();

            case 'link_menu': // DONE
                return apiGetLinkMenu();

            case 'reserved_test': // DONE
                return apiGetReservedTestResponse();

            case 'auth_get_otp':  // DONE
                return proceedBridge(
                    function () {
                        getOtpCode();
                    },
                    function ($bridgeData) {
                        return apiGetOtp($bridgeData['mes']);
                    }
                );

            case 'auth_accept_otp': // DONE
                return proceedBridge(
                    function () {
                        acceptOtpCode();
                    },
                    function ($bridgeData) {
                        return $bridgeData;
                    }
                );

            case 'auth_login_otp':  // DONE
                return proceedBridge(
                    function () {
                        loginByOtpCode();
                    },
                    function ($bridgeData) {
                        return $bridgeData;
                    }
                );
                break;
        }
    }
    //Вместо default case
    throw new ApiException("Unknown query", 400);
}

function updateApiCache() // DONE
{
    global $db, $cache, $conf, $user, $var;


    $releases = _getFullReleasesDataInLegacyStructure();

    foreach ($releases as $row) {

        $names = [];
        $firstName = html_entity_decode(trim($row['name']));
        $secondName = html_entity_decode(trim($row['ename']));
        if (!empty($firstName)) {
            $names[] = $firstName;
        }
        if (!empty($secondName)) {
            $names[] = $secondName;
        }

        $poster = $row['poster'];

        $genres = [];
        $genresTmp = array_unique(explode(',', $row['genre']));
        foreach ($genresTmp as $genre) {
            $genres[] = html_entity_decode(trim($genre));
        }

        $voices = [];
        $voicesTmp = array_unique(explode(',', $row['voice']));
        foreach ($voicesTmp as $voice) {
            $voices[] = html_entity_decode(trim($voice));
        }

        $playlist = json_decode(getApiPlaylist($row['id']), true);

        $series = null;
        $episodesIds = [];
        $minId = PHP_INT_MAX;
        $maxId = PHP_INT_MIN;

        foreach ($playlist as $key => $episode) {
            if ($key === 'online') {
                continue;
            }
            $id = intval($episode['id']);
            $episodesIds[] = $id;
        }


        if (!empty($episodesIds)) {
            $minId = min($episodesIds);
            $maxId = max($episodesIds);
        }

        if ($minId == PHP_INT_MAX && $maxId == PHP_INT_MIN) {
            $series = null;
        } elseif ($minId == $maxId) {
            $minId = max($minId, 1);
            $series = "$minId";
        } else {
            $series = "$minId-$maxId";
        }

        $moon = null;
        if (!empty($row['moonplayer'])) {
            $moon = $row['moonplayer'];
        }

        $announce = $row['announce'];
        if (!empty($announce)) {
            $announce = html_entity_decode(trim($announce));
        }
        if (empty($announce)) {
            $announce = null;
        }
        if ($row['status'] == "2") {
            $announce = null;
        }


        $telegramPlaylist = [];
        foreach ($playlist as $key => $episode) {
            if ($key === 'online') {
                continue;
            }
            $releaseId = intval($row['id']);
            $episodeId = intval($episode['id']);
            $telegramPlaylist[] = [
                'id' => $episodeId,
                'title' => $episode['title'],
                'url' => getTelegramActionLink("app", "play", "{$releaseId}_{$episodeId}"),
            ];
        }

        $externalPlaylist = [];
        $externalPlaylist[] = [
            'tag' => 'telegram',
            'title' => 'Telegram',
            'actionText' => 'Смотреть в Telegram',
            'episodes' => $telegramPlaylist
        ];

        $info[$row['id']] = [
            'id' => intval($row['id']),
            'code' => $row['code'],
            'names' => $names,
            'series' => $series,
            'poster' => $poster,
            'rating' => (int)$row['rating'],
            'last' => (string)$row['last'],
            'moon' => $moon,
            'announce' => $announce,
            'status' => html_entity_decode($var['status'][$row['status']] ?? null),
            'statusCode' => (string)$row['status'],
            'type' => html_entity_decode($row['type']),
            'genres' => $genres,
            'voices' => $voices,
            'year' => (string)$row['year'],
            'season' => html_entity_decode($row['season']),
            'day' => (string)$row['day'],
            'description' => $row['description'],
            //Для блокировки релизов
            'blockedInfo' => [
                'blocked' => boolval($row['block']),
                'reason' => null,
                'bakanim' => boolval($row['bakanim'])
            ],
            'playlist' => $playlist,
            'externalPlaylist' => $externalPlaylist
        ];

        $tmp = $db->prepare('
            SELECT 
               t.`id` AS `fid`, 
               UNIX_TIMESTAMP(t.`updated_at`) AS `ctime`, 
               t.`hash` AS `info_hash`, 
               t.`leechers`,
               t.`seeders`, 
               t.`completed_times` as `completed`,
               JSON_ARRAY(CONCAT_WS(\' \', t.`type`, t.`quality`, IF(t.`is_hevc` = 1, \'HEVC\', null)), t.`description`, t.`size`) AS `info`
            FROM `torrents` as t
            WHERE t.`release_id` = :rid AND t.`deleted_at` IS NULL
            GROUP BY t.id
            ORDER BY t.`sort_order` ASC, t.`created_at` ASC
        ');

        $rowId = $row['id'];
        $tmp->bindParam(':rid', $rowId);
        $tmp->execute();

        while ($xrow = $tmp->fetch()) {

            $data = json_decode($xrow['info'], true);
            $link = "/public/torrent/download.php?id={$xrow['fid']}";

            $torrent[$row['id']][] = [
                'id' => (int)$xrow['fid'],
                'hash' => $xrow['info_hash'],
                'leechers' => intval($xrow['leechers']),
                'seeders' => intval($xrow['seeders']),
                'completed' => intval($xrow['completed']),
                'quality' => html_entity_decode(trim($data['0'])),
                'series' => html_entity_decode(trim($data['1'])),
                'size' => intval($data['2']),
                'url' => $link,
                'ctime' => intval($xrow['ctime'])
            ];
        }
    }


    $chunk = array_chunk($info, 100, true);
    saveNormalCache($chunk, $torrent ?? []);
    saveInfiniteCache($chunk, $torrent ?? []);
}

function saveNormalCache($chunk, $torrent) // DONE
{
    global $cache;
    foreach ($chunk as $k => $v) {
        $cache->set("apiInfo$k", json_encode($v), 300);
    }
    $cache->set('apiInfo', count($chunk), 300);
    $cache->set('apiTorrent', json_encode($torrent), 300);
}

function saveInfiniteCache($chunk, $torrent) // DONE
{
    global $cache;
    foreach ($chunk as $k => $v) {
        $cache->set("infiniteApiInfo$k", json_encode($v), 0);
    }
    $cache->set('infiniteApiInfo', count($chunk), 0);
    $cache->set('infiniteApiTorrent', json_encode($torrent), 0);
}

function getApiPlaylist($id) // DONE
{

    global $conf, $var, $db;

    // Episodes
    $query = $db->prepare('
        SELECT re.* 
            from `releases_episodes` as re 
            inner join `releases` as r on re.releases_id = r.id
            where 
                re.releases_id = :id and re.`is_visible` = 1 AND re.`deleted_at` IS NULL AND
                r.`is_hidden` = 0  AND r.`deleted_at` IS NULL AND COALESCE(re.`hls_480`, re.`hls_720`, re.`hls_1080`, re.`rutube_id`) IS NOT NULL  
            ORDER BY re.`sort_order` DESC
        ');

    $query->bindValue(':id', $id);
    $query->execute();
    $episodes = $query->fetchAll();

    // Cache Servers
    $query = $db->prepare('SELECT * from `cache_servers` where deleted_at is NULL ORDER BY `response_seconds` ASC, `outgoing_traffic` ASC LIMIT 3');
    $query->bindValue(':id', $id);
    $query->execute();
    $servers = $query->fetchAll();

    $playlist = [];

    foreach ($episodes as $episode) {

        $server = $servers[array_rand($servers, 1)];

        $endingSkip = []; // future
        $openingSkip = [$episode['opening_starts_at'] !== null ? (float)$episode['opening_starts_at'] : null, $episode['opening_ends_at'] !== null ? (float)$episode['opening_ends_at'] : null];

        $item = [
            'id' => (float)$episode['ordinal'],
            'title' => sprintf('Серия %s', $episode['ordinal']),
            'srcSd' => 'https://vk.com/anilibria?w=wall-37468416_493445',
            'srcHd' => 'https://vk.com/anilibria?w=wall-37468416_493445',
            'skips' => [
                'ending' => $endingSkip,
                'opening' => count(array_filter($openingSkip, 'strlen')) === 2 ? $openingSkip : [],
            ],
            'poster' => !empty($episode['preview_original'])
                ? ImageThumbnail::make(implode(DIRECTORY_SEPARATOR, [$conf['release_episode_poster_host'], $episode['releases_id'], $episode['ordinal'], $episode['preview_original']]))->getThumbnail(720, null, 80)
                : null,
            'ordinal' => (float)$episode['ordinal'],
            'sources' => [
                'is_rutube' => $episode['rutube_id'] !== null,
                'is_anilibria' => !empty(array_filter([$episode['hls_480'], $episode['hls_720'], $episode['hls_1080']])) === true
            ],
            'rutube_id' => $episode['rutube_id'] ?? null,
            'updated_at' => strtotime($episode['updated_at'] ?? null),
        ];

        if (empty($episode['hls_480']) === false) $item['sd'] = sprintf('%s/ts/%s/%s/480/%s', $server['url'], $episode['releases_id'], $episode['ordinal'], $episode['hls_480']);
        if (empty($episode['hls_720']) === false) $item['hd'] = sprintf('%s/ts/%s/%s/720/%s', $server['url'], $episode['releases_id'], $episode['ordinal'], $episode['hls_720']);
        if (empty($episode['hls_1080']) === false) $item['fullhd'] = sprintf('%s/ts/%s/%s/1080/%s', $server['url'], $episode['releases_id'], $episode['ordinal'], $episode['hls_1080']);

        $playlist[] = $item;
    }

    return json_encode($playlist);


    /*global $conf;
    $playlist = [];
    $episodesSrc = getRemote($conf['nginx_domain'] . '/?id=' . $id . '&v2=1', 'video' . $id);
    if ($episodesSrc) {
        $episodesArr = json_decode($episodesSrc, true);
        $host = anilibria_getHost($episodesArr['online']);
        if (!empty($episodesArr) && !empty($episodesArr['updated'])) {
            unset($episodesArr['updated']);
            foreach ($episodesArr as $key => $episodeSrc) {
                if ($key == 'online' || $key == 'new') {
                    continue;
                }
                $download = '';
                if (!empty($episode['file'])) {
                    $download = mp4_link($episodeSrc['file'] . '.mp4');
                }
                if ($host) {
                    $playlistArr = explode(",", $episodeSrc['new2']);
                    $playlistMap = [];
                    foreach ($playlistArr as $playlistItem) {
                        $qPattern = '/\[(\d+p)\]/m';
                        preg_match_all($qPattern, $playlistItem, $matches, PREG_SET_ORDER, 0);
                        $quality = $matches[0][1];
                        $playlistMap[$quality] = preg_replace($qPattern, 'https:', $playlistItem);
                    }
                    $episode = [
                        "id" => $key,
                        "title" => "Серия $key",
                        "file" => $episodeSrc['file']
                    ];
                    if (isset($playlistMap["480p"])) {
                        $episode["sd"] = $playlistMap["480p"];
                    }
                    if (isset($playlistMap["720p"])) {
                        $episode["hd"] = $playlistMap["720p"];
                    }
                    if (isset($playlistMap["1080p"])) {
                        $episode["fullhd"] = $playlistMap["1080p"];
                    }
                }
                if (!empty($episodeSrc['file'])) {
                    $episode['srcSd'] = mp4_link($episodeSrc['file'] . '.mp4');
                }
                $playlist[] = $episode;
            }
        }
        if ($host) {
            $playlist['online'] = $episodesArr['online'];
        }
    }
    return $playlist;*/
}

function _transformTorrentData($torrent) // DONE
{
    return [
        'id' => (int)$torrent['id'],
        'hash' => $torrent['hash'] ?? null,
        'leechers' => (int)($torrent['leechers'] ?? 0),
        'seeders' => (int)($torrent['seeders'] ?? 0),
        'completed' => (int)($torrent['completed'] ?? 0),
        'quality' => $torrent['quality'],
        'series' => $torrent['description'],
        'size' => (int)($torrent['size'] ?? 0),
        'url' => "/public/torrent/download.php?id={$torrent['id']}",
        'ctime' => strtotime($torrent['created_at'])
    ];
}


class ApiResponse
{
    private $status = false;
    private $data = null;
    private $error = null;

    public function proceed($func) // DONE
    {
        try {

            $data = $func();
            $this->success($data);

        } catch (ApiException $e) {

            $this->error(
                $e->getMessage(),
                $e->getCode(),
                $e->getDescription()
            );

        } catch (Throwable $e) {

            $this->error(
                $e->getMessage(),
                $e->getCode()
            );

        } catch (Exception $e) {

            $this->error(
                $e->getMessage(),
                $e->getCode()
            );

        } finally {
            return $this->build();
        }
    }

    public function success($data) // DONE
    {
        $this->status = true;
        $this->error = null;
        $this->data = $data;
        return $this->build();
    }

    public function error($message = "Default API error", $code = 400, $description = null) // DONE
    {
        $this->status = false;
        $this->data = null;
        $this->error = [
            'code' => $code,
            'message' => $message,
            'description' => $description
        ];
        return $this->build();
    }

    private function build() // DONE
    {
        return [
            'status' => $this->status,
            'data' => $this->data,
            'error' => $this->error
        ];
    }
}

class ApiException extends \Exception // DONE
{
    protected $description = null;

    public function __construct($message = "Default API error", $code = 400, $description = null) // DONE
    {
        parent::__construct($message, $code);
        $this->description = $description;
    }

    public function getDescription() // DONE
    {
        return $this->description;
    }
}

class LinkMenuItem implements \JsonSerializable // DONE
{
    private $title = "";
    private $absoluteLink = null;
    private $sitePagePath = null;
    private $icon = null;

    public function __construct(
        $title = "",
        $absoluteLink = null,
        $sitePagePath = null,
        $icon = null
    )
    {
        $this->title = strval($title);
        $this->absoluteLink = strval($absoluteLink);
        $this->sitePagePath = strval($sitePagePath);
        $this->icon = strval($icon);
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public static function absoluteLink(
        $title = "",
        $absoluteLink = null,
        $icon = null
    )
    {
        return new LinkMenuItem($title, $absoluteLink, null, $icon);
    }

    public static function pagePath(
        $title = "",
        $sitePagePath = null,
        $icon = null
    )
    {
        return new LinkMenuItem($title, null, $sitePagePath, $icon);
    }

    public static $IC_VK = "vk";
    public static $IC_YOUTUBE = "yotube";
    public static $IC_PATREON = "patreon";
    public static $IC_TELEGRAM = "telegram";
    public static $IC_DISCORD = "discord";
    public static $IC_ANILIBRIA = "anilibria";
    public static $IC_INFO = "info";
    public static $IC_RULES = "rules";
    public static $IC_PERSON = "person";
    public static $IC_SITE = "site";

}