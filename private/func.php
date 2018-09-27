<?php

function rehash($passwd, $hash = 0, $a = 'no_hash'){
	if(empty($hash) || password_needs_rehash($hash, PASSWORD_DEFAULT))
		$a = password_hash($passwd, PASSWORD_DEFAULT);
	return $a;
}

function genRandStr($length = 10) {
	$str = ''; $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~!@#$%^&*()_+-=';
	for ($i = 0; $i < $length; $i++) {
		$str .= $chars[random_int(0 ,strlen($chars)-1)];
	}
    return $str;
}

function _mail($email, $subject, $message){
	global $conf;
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=utf-8\r\n";
	$headers .= "Content-Transfer-Encoding: base64\r\n";
	$subject  = "=?utf-8?B?".base64_encode($subject)."?=";
	$headers .= "From: {$conf['email_from']} <{$conf['email']}>\r\n";
	mail($email, $subject, rtrim(chunk_split(base64_encode($message))), $headers);
}

function _message($mes, $err = 'ok'){
	$arr = ['err' => $err, 'mes' => $mes];
	echo json_encode($arr);
	die();
}

function half_string($s){
	return substr($s, 0, round(strlen($s)/2));
}

function coinhive_proof(){
	global $conf;
	if(empty($_POST['coinhive-captcha-token'])){
		return false;	
	}
	$post_data = [
		'secret' => $conf['coinhive_secret'],
		'token' => $_POST['coinhive-captcha-token'],
		'hashes' => 1024
	];
	$post_context = stream_context_create([
		'http' => [
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($post_data)
		]
	]);
	$url = 'https://api.coinhive.com/token/verify';
	$response = json_decode(file_get_contents($url, false, $post_context));
	if($response && $response->success) {
		return true;
	}
	return false;
}

function _exit(){
	global $db;
	if(session_status() != PHP_SESSION_NONE){
		if(!empty($_SESSION['sess'])){
			$query = $db->prepare("DELETE FROM `session` WHERE `hash` = :hash");
			$query->bindParam(':hash', $_SESSION["sess"], PDO::PARAM_STR);
			$query->execute();
		}
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
		session_unset();
		session_destroy();
		header("Location: https://".$_SERVER['SERVER_NAME']);	
	}
}

function login($login, $passwd){
	global $db, $user, $var;
	if($user){
		_message('Already authorized', 'error');
	}
	if(empty($login) || empty($passwd)){
		_message('Empty post value', 'error');
	}
	if(strlen($login) > 20){
		_message('Too long login or email', 'error');
	}
	if(preg_match('/[^0-9A-Za-z]/', $login)){
		_message('Wrong login', 'error');
	}
	if(strlen($var['user_agent']) > 256){
		_message('Wrong user agen', 'error');
	}
	$query = $db->prepare("SELECT * FROM `users` WHERE `login` = :login");
	$query->bindValue(':login', $login, PDO::PARAM_STR);
	$query->execute();
	if($query->rowCount() == 0){
		_message('Invalid user', 'error');
	}
	$row = $query->fetch();
	if(!password_verify($passwd, $row['passwd'])){
		_message('Wrong password', 'error');
	}
	$hash = rehash($passwd, $row['passwd']);
	if($hash != 'no_hash'){
		$query = $db->prepare("UPDATE `users` SET `passwd` = :passwd WHERE `id` = :id");
		$query->bindParam(':passwd', $hash, PDO::PARAM_STR);
		$query->bindParam(':id', $row['id'], PDO::PARAM_STR);
		$query->execute();
		$row['passwd'] = $hash;
	}
	$time = $var['time']+86400;
	$_SESSION['sess'] = hash('sha512', $ip.$agent.$time.$row['login'].half_string($row['passwd']));
	$query = $db->prepare("INSERT INTO `session` (`uid`, `hash`, `time`, `ip`, `info`) VALUES (:uid, :hash, :time, :ip, :info)");
	$query->bindParam(':uid', $row['id'], PDO::PARAM_STR);
	$query->bindParam(':hash', $_SESSION['sess'], PDO::PARAM_STR);
	$query->bindParam(':time', $time, PDO::PARAM_STR);
	$query->bindParam(':ip', $ip, PDO::PARAM_STR);
	$query->bindParam(':info', $agent, PDO::PARAM_STR);
	$query->execute();
	$query = $db->prepare("SELECT `id` FROM `session` WHERE `uid` = :uid");
	$query->bindParam(':uid', $row['id'], PDO::PARAM_STR);
	$query->execute();
	if($query->rowCount() > 10){
		$row = $query->fetch();
		$query = $db->prepare("DELETE FROM `session` WHERE `id` = :id");
		$query->bindParam(':id', $row['id'], PDO::PARAM_STR);
		$query->execute();
	}
	_message('Success');
}
