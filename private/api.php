<?php

function safeApiList() {
    $response = (new ApiResponse()) -> proceed(function() {
        return apiList();
    });
    die(json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

function unsafeApiList() {
    $response = apiList();
    die(json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

function apiList(){
    //only for testing
    //updateApiCache();
    
	global $cache; $result = [];
	$count = $cache->get('apiInfo');
	$torrent = json_decode($cache->get('apiTorrent'), true);
	for($i=0; $i < $count; $i++){
		$tmp = json_decode($cache->get("apiInfo$i"), true);
		foreach($tmp as $k => $v){
			$info["$k"] = $v; 
		}
	}
    
	if($info === false || $torrent === false){
        throw new ApiException('API is not ready', 400);
	}
    
	if(!isset($_POST['query'])){
        throw new ApiException('No query', 400);
	}
    
	function apiGetTorrentsMap($torrents, $idsString){
		$result = [];
		$ids = array_unique(explode(',', $idsString));
		if(!empty($ids)){
			foreach($ids as $id){
				if(array_key_exists($id, $torrents)){
					$result["$id"] = $torrents["$id"];
				}
			}
		}
		return $result;
	}
    
    function apiGetTorrentsList($torrents, $id){
        if(array_key_exists($id, $torrents)){
            return $torrents["$id"];
        }
		return [];
	}
    
    function apiGetReleaseById($info, $torrent, $rid) {
        if(array_key_exists($rid, $info)){
            $releases = array($info[$rid]);
            return proceedReleases($releases, $torrent)[0];
        }
		throw new ApiException("Release by id=$rid not found", 404);
	}
    
    function apiGetReleaseByCode($info, $torrent, $rcode) {
        foreach($info as $key => $val){
            if($val['code'] == $rcode){
                $releases = array($val);
				return proceedReleases($releases, $torrent)[0];
			}
        }
		throw new ApiException("Release by code=$rcode not found", 404);
	}

	function apiGetReleasesById($info, $torrent, $rid){
        $releases = [];
        $list = '';
		if(!empty($rid)){
			$list = array_unique(explode(',', $rid));
		}
        foreach($info as $key => $val){
            if(!empty($list) && !in_array($val['id'], $list)){
				continue;
			}
            $releases[] = $val;
        }
		return proceedReleases($releases, $torrent);
	}
    
    function apiSearchReleases($info, $torrent){
        return apiGetReleases($info, $torrent);
    }
    
    function apiGetReleases($info, $torrent){
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
    
    function proceedPagination($pagination){
        unset($pagination['startIndex'], $pagination['endIndex']);
        $pagination['page'] = $pagination['page'] + 1;
        $pagination['allPages'] = $pagination['allPages'] + 1;
        return $pagination;
    }
    
    function createPagination($allItemsCount) {
        $startIndex = 0;
        $endIndex = 0;
        $page = 0;
        $perPage = 10;
        if(!empty($_POST['page'])){
            $page = intval($_POST['page']);
            if($page <= 1){
                $page = 0;
            }else{
                $page = $page - 1;
            }
        }
        
        if(!empty($_POST['perPage'])){
            $perPage = intval($_POST['perPage']);
            if($perPage <= 0){
                $perPage = 1;
            }
        }
        
        $startIndex = $perPage * $page;
        $endIndex = $startIndex + $perPage - 1;
        return [
            'page' => $page,
            'perPage' => $perPage,
            'allPages' => intval($allItemsCount / $perPage),
            'allItems' => $allItemsCount,
            'startIndex' => $startIndex,
            'endIndex' => $endIndex
        ];
    }
    
    function proceedReleases($releases, $torrent){
		$result = []; 
		$filter = ['code', 'names', 'series', 'poster', 'posterFull', /*'rating',*/ 'last', 'moon', 'status', 'type', 'genres', 'voices', 'year', 'day', 'description', 'blockedInfo', 'playlist', 'torrents', 'favorite'];

        foreach($releases as $key => $val){
			
            $unsettedFileds = [];
			if(isset($_POST['filter'])){
				$filterList = array_unique(explode(',', $_POST['filter']));
                
				foreach($filter as $v){
					if(!isset($_POST['rm'])){
						if(!in_array($v, $filterList)){
                            $unsettedFileds[] = "$v";
							unset($val["$v"]);
						}
					}else{
						if(in_array($v, $filterList)){
                            $unsettedFileds[] = "$v";
							unset($val["$v"]);
						}
					}
				}
			}
            
            if(!in_array("torrents", $unsettedFileds)) {
                $val['torrents'] = apiGetTorrentsList($torrent, $val['id']);   
            }
            if(!in_array("favorite", $unsettedFileds)) {
                $val['favorite'] = apiGetFavoriteField($val);
            }
            unset($val['rating']);
			$result[] = $val;
		}
		return $result;
    }
    
    function apiGetFavoriteField($release){
        global $user;
        return [
            'rating' => intval($release['rating']),
            'added' => isFavorite($user['id'], $release['id'])
        ]; 
    }
    
    
    function apiFavorites($info, $torrent){
        global $db, $user;
        $favIds = [];
        if($user){
            $query = $db->prepare('SELECT `rid` FROM `favorites` WHERE `uid` = :uid');
            $query->bindParam(':uid', $user['id']);
            $query->execute();
            while($row=$query->fetch()){
                $favIds[] = $row['rid'];
            }
        } else {
            throw new ApiException("No user", 401);
        }
        $favReleases = [];
        foreach($favIds as $favId){
            if(!array_key_exists("$favId", $info)){
				continue;
			}
            $favReleases["$favId"] = $info["$favId"];
        }
        return apiGetReleases($favReleases, $torrent);
    }
    
    function apiGetUser(){
        global $db, $user;
        if(!$user) {
            throw new ApiException(401, "No user");
        }
        return [
            "id" => intval($user['id']),
            "login" => $user['login'],
            "avatar" => '/upload/avatars/'.$user['dir'].'/'.$user['avatar'].'.jpg'
        ];
    }

    
    function releaseFavoriteAction($info, $torrent){
        global $db, $user;
        if(!$user){
            throw new ApiException("No user", 401);
        }
        if(empty($_POST['id'])){
            throw new ApiException("No release id", 400);
        }
        if(empty($_POST['action'])){
            throw new ApiException("No action", 400);
        }
        if(!array_key_exists($_POST['id'], $info)){
            throw new ApiException("Release not found", 404);
        }
        
        $isFavorite = isFavorite($user['id'], $_POST['id']);
        
        switch($_POST['action']){
            case 'add':
                if($isFavorite){
                    throw new ApiException("Already added", 400);
                }
                $query = $db->prepare('INSERT INTO `favorites` (`uid`, `rid`) VALUES (:uid, :rid)');
                $query->bindParam(':uid', $user['id']);
                $query->bindParam(':rid', $_POST['id']);
                $query->execute();
            break;
                
            case 'delete':
                if(!$isFavorite){
                    throw new ApiException("Already deleted", 400);
                }
                $query = $db->prepare('DELETE FROM `favorites` WHERE `uid` = :uid AND `rid` = :rid');
                $query->bindParam(':uid', $user['id']);
                $query->bindParam(':rid', $_POST['id']);
                $query->execute();
            break;
        }
        
        return apiGetReleaseById($info, $torrent, $_POST['id']);
    }
    
    
    function apiGetYoutube(){
        global $db;
        $countQuery = $db->query('SELECT COUNT(*) FROM `youtube`');
        $count = intval($countQuery->fetch()[0]);
        
        $pagination = createPagination($count);
        $startIndex = $pagination['startIndex'];
        $perPage = $pagination['perPage'];
        
        $result = [];
        $query = $db->query("SELECT * FROM `youtube` ORDER BY `time` DESC LIMIT {$startIndex}, {$perPage}");
        while($row=$query->fetch()){
            $result[] = [
                'id' => intval($row['id']),
                'title' => $row['title'],
                'image' => '/upload/youtube/'.hash('crc32', $row['vid']).'.jpg',
                'vid' => $row['vid'],
                'views' => intval($row['view']),
                'comments' => intval($row['comment']),
                'timestamp' => intval($row['time'])
            ];
        }
        
        
        return [
            'items' => $result,
            'pagination' => proceedPagination($pagination)
        ];
    }
    
    function apiGetGenres(){
        global $db; 
        $result = []; 
        $query = $db->query('SELECT `name` from `genre`');
        while($row = $query->fetch()){
            $result[] = $row['name'];
        }
        sort($result);
        return $result;
    }
    
    
	switch($_POST['query']){
		case 'torrent':
			if(!empty($_POST['id'])){
				return apiGetTorrentsMap($torrent, $_POST['id']);
			}else{
				return $torrent;
			}
		break;
            
		case 'info':
			return apiGetReleasesById($info, $torrent, $_POST['id']);
		break;
            
        case 'release':
            if(!empty($_POST['id'])){
                return apiGetReleaseById($info, $torrent, $_POST['id']);
            } elseif(!empty($_POST['code'])) {
                return apiGetReleaseByCode($info, $torrent, $_POST['code']);
            } else {
                throw new ApiException("No id or code for release", 400);
            }
        break;
            
        case 'list':
            return apiGetReleases($info, $torrent);
        break;
            
        case 'search':
            return apiSearchReleases($info, $torrent);
        break;
            
        case 'genres':
            return apiGetGenres();
        break;
            
        case 'favorites':
            if(!empty($_POST['id'])||!empty($_POST['action'])){
                return releaseFavoriteAction($info, $torrent);
            }else{
                return apiFavorites($info, $torrent);
            }
        break;
            
        case 'youtube':
            return apiGetYoutube();
        break;
            
        case 'user':
            return apiGetUser();
        break;
	}
    //Вместо default case
    throw new ApiException("Unknown query", 400);
}

function updateApiCache(){
	global $db, $cache, $user;
	$query = $db->query('SELECT `id`, `name`, `ename`, `rating`, `last`, `moonplayer`, `description`, `day`, `year`, `genre`, `voice`, `type`, `status`, `code` FROM `xrelease` WHERE `status` != 3 ORDER BY `last` DESC');
	while($row=$query->fetch()){
        
        $names = [];
        $firstName = trim($row['name']);
        $secondName = trim($row['ename']);
        if(!empty($firstName)){
            $names[] = $firstName;
        }
        if(!empty($secondName)){
            $names[] = $secondName;
        }
        
        $poster = $_SERVER['DOCUMENT_ROOT'].'/upload/release/270x390/'.$row['id'].'.jpg';
        if(!file_exists($poster)){
            $poster = '/upload/release/270x390/default.jpg';
        }else{
            $poster = fileTime($poster);
        }
        
        $posterFull = $_SERVER['DOCUMENT_ROOT'].'/upload/release/350x500/'.$row['id'].'.jpg';
        if(!file_exists($poster)){
            $posterFull = '/upload/release/350x500/default.jpg';
        }else{
            $posterFull = fileTime($posterFull);
        }
        
        $genres = [];
        $genresTmp = array_unique(explode(',', $row['genre']));
        foreach($genresTmp as $genre){
            $genres[] = trim($genre);
        }
        
        $voices = [];
        $voicesTmp = array_unique(explode(',', $row['voice']));
        foreach($voicesTmp as $voice){
            $voices[] = trim($voice);
        }
        
        $playlist = getApiPlaylist($row['id']);
        
        $series = NULL;
        $minId = PHP_INT_MAX;
        $maxId = PHP_INT_MIN;
        foreach($playlist as $episode) {
            $id = intval($episode['id']);
            if($id > $maxId) {
                $maxId = $id;
            }
            if($id < $minId) {
                $minId = $id;
            }
        }
        
        if ($minId == PHP_INT_MAX && $maxId == PHP_INT_MIN){
            $series = NULL;
        } elseif ($minId == $maxId){
            $minId = max($minId, 1);
            $series = "$minId";
        } else {
            $series = "$minId-$maxId";
        }
        
		$info[$row['id']] = [
			'id' => intval($row['id']),
			'code' => $row['code'],
			'names' => $names, 
            'series' => $series,
            'poster' => $poster,
            'posterFull' => $posterFull,
            'rating' => $row['rating'],
			'last' => $row['last'],
			'moon' => $row['moonplayer'],
			'status' => $row['status'],
			'type' => $row['type'],
			'genres' => $genres,
			'voices' => $voices,
			'year' => $row['year'],
			'day' => $row['day'],
			'description' => $row['description'],
            //Для блокировки релизов
            'blockedInfo' => [
                'blocked' => FALSE,
                'reason' => NULL
            ],
            'playlist' => $playlist
		];
        
		$tmp = $db->prepare('SELECT `fid`, `info_hash`, `leechers`, `seeders`, `completed`, `info` FROM `xbt_files` WHERE `rid` = :rid');
		$tmp->bindParam(':rid', $row['id']);
		$tmp->execute();
		while($xrow=$tmp->fetch()){
			$data = json_decode($xrow['info'], true);
            $link = NULL;
            if($user){
				$link = "/public/torrent/download.php?id={$xrow['fid']}";
			}else{
				$link = "/upload/torrents/{$xrow['fid']}.torrent";
			}
			$torrent[$row['id']][] = [
				'id' => intval($xrow['fid']),
				'hash' => unpack('H*', $xrow['info_hash'])['1'],
				'leechers' => intval($xrow['leechers']),
				'seeders' => intval($xrow['seeders']),
				'completed' => intval($xrow['completed']),
				'quality' => $data['0'],
				'series' => $data['1'],
				'size' => intval($data['2']),
                'url' => $link
			];
		}
	}
	$chunk = array_chunk($info, 100, true);
	foreach($chunk as $k => $v){
		$cache->set("apiInfo$k", json_encode($v), 300);
	}
	$cache->set('apiInfo', count($chunk), 300);
	$cache->set('apiTorrent', json_encode($torrent), 300);
}

function getApiPlaylist($id){
	global $conf;
	$playlist = [];
	$episodesSrc = getRemote($conf['nginx_domain'].'/?id='.$id, 'video'.$id);
	if($episodesSrc){
		$episodesArr = json_decode($episodesSrc, true);
		if(!empty($episodesArr) && !empty($episodesArr['updated'])){
			unset($episodesArr['updated']);
			foreach($episodesArr as $key => $episodeSrc) {
				$download = '';
				if(!empty($episode['file'])){
					$download = mp4_link($episodeSrc['file'].'.mp4');
				}
                $episode = [
                    "id" => $key,
                    "title" => "Серия $key",
                    "sd" => "https:${episodeSrc['sd']}",
                    "hd" => "https:${episodeSrc['hd']}"
                ];
                if(!empty($episode['file'])){
					$episode['srcSd'] = mp4_link($episodeSrc['file'].'.mp4');
				}
                $playlist[] = $episode;
			}
		}
	}
	return $playlist;
}

class ApiResponse {
    private $status = FALSE;
    private $data = NULL;
    private $error = NULL;
    
    public function proceed($func){
        try {
            $data = $func();
            $this->success($data);
        } catch(ApiException $e) {
            $this->error(
                $e->getMessage(),
                $e->getCode(),
                $e->getDescription()
            );
        } catch(Exception $e) {
            $this->error(
                $e->getMessage(),
                $e->getCode()
            );
        } finally {   
            return $this->build();
        }
    }
    
    public function success($data) {
        $this->status = TRUE;
        $this->error = NULL;
        $this->data = $data;
        return $this->build();
    }
    
    public function error($message = "Default API error", $code = 400, $description = NULL) {
        $this->status = FALSE;
        $this->data = NULL;
        $this->error = [
            'code' => $code,
            'message' => $message,
            'description' => $description
        ];
        return $this->build();
    }
    
    private function build(){
        return [
            'status' => $this->status,
            'data' => $this->data,
            'error' => $this->error
        ];
    }
}

class ApiException extends \Exception {
    protected $description = NULL;
    
    public function __construct($message = "Default API error", $code = 400, $description = NULL){
        parent::__construct($message, $code);
        $this->description = $description;
    }
    
    public function getDescription() { return $this->description; }
}