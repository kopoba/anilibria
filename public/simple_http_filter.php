<?php

/*

	...
	
*/

require_once($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');

if(empty($_GET['do'])){
	_message('Empty POST value', 'error');
}

$arr = ['status' => 'no'];
if($_GET['do'] == 'coinhive'){
	if(coinhive_proof()){
		$arr['status'] = 'yes';
		secret_cookie();
	}
}else{
	$result = recaptchav3();
	if($result['success']){
		$arr['status'] = 'yes';
		$arr['score'] = $result['score'];
		if($result['score'] > 0.5){
			secret_cookie();
		}
	}
}

_message($arr);
