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
			'https://dev.anilibria.tv/public/login.php', 
			[
				'mail' => 'poiuty@lepus.su',
				'passwd' => '~VBHHOPx',
			]
		);
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
    echo '<div style="font-family:monospace;">'
        .'<b>'.$url.'</b><br>'
        .simpleSend($url, $data)
        .'</div><br><br>'.PHP_EOL;
}

testPrint(
	'https://dev.anilibria.tv/public/catalog.php', 
	[
		'page' => '1', // num page
		'genre' => 'комендия,магия',
		'xpage' => 'catalog',
		'sort' => '2', // 1 new, 2 popular
		'json' => ''
	]
);

testPrint(
	'https://dev.anilibria.tv/public/search.php', 
	[
		'search' => 'наруто',
		'json' => ''
	]
);

testPrint(
	'https://dev.anilibria.tv/public/api/index.php', 
	[
		'query' => 'torrent',
		'id' => '1202, 473',
	]
);

testPrint(
	'https://dev.anilibria.tv/public/api/index.php', 
	[
		'query' => 'info',
		'id' => '1202, 473',
		'filter' => 'name,torrent', // show only
	]
);

testPrint(
	'https://dev.anilibria.tv/public/api/index.php', 
	[
		'query' => 'info',
		'id' => '1202, 473',
		'filter' => 'description,torrent',
		'rm' => '' // remove filter
	]
);

testPrint(
	'https://dev.anilibria.tv/public/api/favorite.php', 
	[]
);

// add and remove favorite (first send add, second remove)
testPrint(
	'https://dev.anilibria.tv/public/favorites.php', 
	[
		'rid' => '8055' 
	]
);

testPrint(
	'https://dev.anilibria.tv/public/api/youtube.php', 
	[]
);
