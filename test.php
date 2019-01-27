<style>
    body{
        margin: 0;
    }
    .code{
        font-family: monospace;
        background: hsla(0, 0%, 0%, 0.05);
        padding: 1em
    }
    pre{
        margin: 0;
    }
</style>


<?php
require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
    
function sendApi($url, $data, $cookie = ''){
	$options = [
		'http' => [
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n".
			"Cookie: PHPSESSID=$cookie\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data)
		]
	];
	$context  = stream_context_create($options);
	return [file_get_contents($url, false, $context), $http_response_header];
}



function auth(){
	global $cache;
	$result = $cache->get('testAuth');
	if($result === false){
		$result = sendApi(
			'https://test.anilibria.tv/public/login.php', 
			[
				'mail' => 'poiuty@lepus.su',
				'passwd' => '~VBHHOPx',
			]
		);
        echo "auth: ".json_encode($result);
		preg_match("/PHPSESSID=(.*?);/s", $result['1']['5'], $phpsessid);
		$result = $phpsessid['1'];
		$cache->set('testAuth', $result, 300);
	}
	return $result;
}

$cookie = auth();

function simpleSend($url, $data){
    global $cookie;
    return sendApi($url, $data, $cookie)['0'];
}

function testPrint($url, $data){
    echo '<div class="code">'
        .'<b>'.$url.' : '.json_encode($data).'</b><br>'
        .'<pre>'
        .htmlentities(simpleSend($url, $data))
        .'</pre>'
        .'</div><br><br>'.PHP_EOL;
}

testPrint(
	'https://test.anilibria.tv/public/api/index.php', 
	[
        'query' => 'user'
    ]
);

/*testPrint(
	'https://test.anilibria.tv/public/catalog.php', 
	[
		'page' => '1', // num page
		'genre' => 'комендия,магия',
		'year' => '2017',
		'xpage' => 'catalog',
		'sort' => '2', // 1 new, 2 popular
		'json' => ''
	]
);

testPrint(
	'https://test.anilibria.tv/public/search.php', 
	[
		'search' => 'наруто',
        'key' => 'genre',
		'json' => ''
	]
);

testPrint(
	'https://test.anilibria.tv/public/api/index.php', 
	[
		'query' => 'torrent',
		'id' => '1202, 473',
	]
);

testPrint(
	'https://test.anilibria.tv/public/api/index.php', 
	[
		'query' => 'release',
		//'id' => '120211111',
		//'filter' => 'name,torrent', // show only
	]
);

testPrint(
	'https://test.anilibria.tv/public/api/index.php', 
	[
		'query' => 'release',
		'id' => '120211111',
		//'filter' => 'name,torrent', // show only
	]
);

testPrint(
	'https://test.anilibria.tv/public/api/index.php', 
	[
		'query' => 'release',
		'id' => '1202',
		//'filter' => 'name,torrent', // show only
	]
);

testPrint(
	'https://test.anilibria.tv/public/api/index.php', 
	[
		'query' => 'release',
		'code' => 'sakurako-san-no-ashimoto-ni-wa-shitai-ga-umatteiru',
		//'filter' => 'name,torrent', // show only
	]
);

testPrint(
	'https://test.anilibria.tv/public/api/index.php', 
	[
		'query' => 'info',
		'id' => '1202, 473',
		//'filter' => 'name,torrent', // show only
	]
);

testPrint(
	'https://test.anilibria.tv/public/api/index.php', 
	[
		'query' => 'info',
		'id' => '1202, 473',
		'filter' => 'description,torrent',
		'rm' => '' // remove filter
	]
);*/

testPrint(
	'https://test.anilibria.tv/public/api/index.php', 
	[
        'query' => 'favorites',
        'filter' => 'torrents',
        'rm' => ''
    ]
);

/*testPrint(
	'https://test.anilibria.tv/public/api/index.php', 
	[
        'query' => 'favorites',
        'action' => 'add',
        'id' => '7471'
    ]
);

testPrint(
	'https://test.anilibria.tv/public/api/index.php', 
	[
        'query' => 'favorites',
        'action' => 'delete',
        'id' => '7471'
    ]
);*/

// add and remove favorite (first send add, second remove)
/*testPrint(
	'https://test.anilibria.tv/public/favorites.php', 
	[
		'rid' => '1202' 
	]
);*/

testPrint(
	'https://test.anilibria.tv/public/api/index.php', 
	[
        'query' => 'youtube',
        'page' => '1',
        'perPage' => '3'
    ]
);

testPrint(
	'https://test.anilibria.tv/public/api/index.php', 
	[
        'query' => 'youtube',
        'page' => '2',
        'perPage' => '3'
    ]
);

testPrint(
	'https://test.anilibria.tv/public/api/index.php', 
	[
		'query' => 'list',
        'page' => '1',
        'perPage' => '3'
	]
);
