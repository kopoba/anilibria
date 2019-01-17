<pre>
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
				'mail' => 'mail',
				'passwd' => 'passwd',
			]
		);
		preg_match("/PHPSESSID=(.*?);/s", $result['1']['5'], $phpsessid);
		$result = $phpsessid['1'];
		$cache->set('testAuth', $result, 300);
	}
	return $result;
}

$cookie = auth();

var_dump(sendApi(
	'https://dev.anilibria.tv/public/catalog.php', 
	[
		'page' => '1', // num page
		'genre' => 'комендия,магия',
		'xpage' => 'catalog',
		'sort' => '2', // 1 new, 2 popular
		'json' => ''
	],
	$cookie
)['0']);

var_dump(sendApi(
	'https://dev.anilibria.tv/public/search.php', 
	[
		'search' => 'наруто',
		'json' => ''
	], 
	$cookie
)['0']);

var_dump(sendApi(
	'https://dev.anilibria.tv/public/api/index.php', 
	[
		'query' => 'torrent',
		'id' => '1202, 473',
	],
	$cookie
)['0']);

var_dump(sendApi(
	'https://dev.anilibria.tv/public/api/index.php', 
	[
		'query' => 'info',
		'id' => '1202, 473',
		'filter' => 'name,torrent', // show only
	],
	$cookie
)['0']);

var_dump(sendApi(
	'https://dev.anilibria.tv/public/api/index.php', 
	[
		'query' => 'info',
		'id' => '1202, 473',
		'filter' => 'description,torrent',
		'rm' => '' // remove filter
	],
	$cookie
)['0']);

var_dump(sendApi(
	'https://dev.anilibria.tv/public/api/favorite.php', 
	[],
	$cookie
)['0']);

// add and remove favorite (first send add, second remove)
var_dump(sendApi(
	'https://dev.anilibria.tv/public/favorites.php', 
	[
		'rid' => '8055' 
	],
	$cookie
)['0']);

var_dump(sendApi(
	'https://dev.anilibria.tv/public/api/youtube.php', 
	[],
	$cookie
)['0']);
