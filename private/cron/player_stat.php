#!/usr/bin/php
<?php

require('/var/www/anilibria/root/private/config.php');
require('/var/www/anilibria/root/private/init/memcache.php');

function sendApi($url, $data, $cookie = ''){
	$options = [
		'http' => [
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n".
			"Cookie: awuegfusvfkjasdf=$cookie\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data)
		]
	];
	$context  = stream_context_create($options);
	return [file_get_contents($url, false, $context), $http_response_header];
}

function authPlayerJS(){
	global $cache, $conf;
	$result = $cache->get('playerAuth');
	if($result === false){
		$result = sendApi(
			'https://playerjs.com/php/apps/playerjs/sign.php', 
			[
				'l' => $conf['player_login'],
				'p' => $conf['player_passwd'],
			]
		);
		preg_match("/awuegfusvfkjasdf=(.*?);/s", $result['1']['6'], $phpsessid);
		$result = $phpsessid['1'];
		$cache->set('playerAuth', $result, 300);
	}
	return $result;
}

function simpleSend($url, $data){
	global $cookie;
	return sendApi($url, $data, $cookie)['0'];
}

function getStatAds(){
	global $cache;
	$ads = [ 
		'player.mix.js' => 1288,
		'player.zet.js' => 1279,
		'player.reyden.js' => 1446
	];
		
	$data = [];
	foreach($ads as $key => $val){
		$stat = simpleSend(
			'https://playerjs.com/php/apps/playerjs/vast_impressions.php', 
			[
				'x' => $val,
				'y' => 0,
				'h' => '',
				'p' => 0
			]
		);
		$tmp =  explode('::', explode('/', $stat)['0']);
		$data["$key"] = round($tmp['0']*100/($tmp['1']+$tmp['0']));
	}
	$result = []; $all = array_sum($data);
	foreach($data as $key => $val){
		$result["$key"] = round($val*100/$all);
	}
	$cache->set('playerStatAds', json_encode($result), 1800);
	//var_dump($result);
}

$cookie = authPlayerJS();
getStatAds();
