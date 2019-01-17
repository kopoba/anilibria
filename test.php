<pre>
<?php
function sendApi($url, $data){
	$options = [
		'http' => [
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data)
		]
	];
	$context  = stream_context_create($options);
	return file_get_contents($url, false, $context);
	
}

var_dump(sendApi(
	'https://dev.anilibria.tv/public/catalog.php', 
	[
		'page' => '1', // num page
		'genre' => 'комендия,магия',
		'xpage' => 'catalog',
		'sort' => '2', // 1 new, 2 popular
		'json' => ''
	]
));

var_dump(sendApi(
	'https://dev.anilibria.tv/public/search.php', 
	[
		'search' => 'наруто',
		'json' => ''
	]
));

var_dump(sendApi(
	'https://dev.anilibria.tv/public/api/index.php', 
	[
		'query' => 'torrent',
		'id' => '1202, 473',
	]
));

var_dump(sendApi(
	'https://dev.anilibria.tv/public/api/index.php', 
	[
		'query' => 'info',
		'id' => '1202, 473',
		'filter' => 'name,torrent', // show only
	]
));

var_dump(sendApi(
	'https://dev.anilibria.tv/public/api/index.php', 
	[
		'query' => 'info',
		'id' => '1202, 473',
		'filter' => 'description,torrent',
		'rm' => '' // remove filter
	]
));

// favorites by user id

// auth

// add favorites

// last youtube
