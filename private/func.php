<?php

function createPasswd($passwd = ''){
	if(empty($passwd)){
		$passwd = genRandStr(8);
	}
	return [$passwd, password_hash($passwd, PASSWORD_DEFAULT)];
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

function _message($key, $err = 'ok'){
	$text = [
		'success' => 'Успех',
		'empty' => 'Пустое значение, заполните все поля',
		'wrong' => 'Неправильное значение',
		'authorized' => 'Уже авторизован',
		'registered' => 'Уже зарегистрирован',
		'long' => 'Слишком длинное значение',
		'wrongLogin' => 'Неправильный логин',
		'wrongEmail' => 'Неправильный email',
		'wrongUserAgent' => 'Неправильный user agent',
		'invalidUser' => 'Неправильный пользователь',
		'wrong2FA' => 'Неправильный код 2FA',
		'wrongPasswd' => 'Неправильный пароль',
		'noUser' => 'Нет такого пользователя',
		'wrongHash' => 'Неправильный hash',
		'wrongLink' => 'Неправильная ссылка',
		'reCaptcha3' => 'reCaptcha проверка не пройдена: низкий score',
		'coinhive' => 'Coinhive проверка не пройдена',
		'checkEmail' => 'Проверьте почту',
		'unauthorized' => 'Неавторизованный пользователь',
		'2FA' => '2FA уже активирована',
		'2FAdisabled' => '2FA выключена',
		'2FAenabled' => '2FA включена',
		'access' => 'Доступ запрещен',
		'same' => 'Одинаковые данные',
		'used' => 'Уже занято',
		'noUploadFile' => 'Неудачная загрузка',
		'uploadError' => 'Неудачная загрузка',
		'wrongType' => 'Неправильный формат файла',
		'maxSize' => 'Слишком большой файл',
	];
	
	die(json_encode(['err' => $err, 'mes' => $text[$key], 'key' => $key]));
}

function _message2($mes){
	die(json_encode(['err' => 'ok', 'mes' => $mes]));
}

function half_string($s){
	return substr($s, round(strlen($s)/2));
}

function session_hash($login, $passwd, $rand = '', $time = ''){
	global $conf, $var;
	if(empty($rand)){
		$rand = genRandStr(8);
	}
	if(empty($time)){
		$time = $var['time']+86400;
	}
	return [$rand.hash($conf['hash_algo'], $rand.$var['ip'].$var['user_agent'].$time.$login.sha1(half_string($passwd))), $time];
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
			$query->bindParam(':hash', $_SESSION["sess"]);
			$query->execute();
		}
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
		session_unset();
		session_destroy();
		header("Location: https://".$_SERVER['SERVER_NAME']);	
	}
}

function login(){
	global $db, $var, $user;
	if($user){
		_message('authorized', 'error');
	}
	if(empty($_POST['login']) || empty($_POST['passwd'])){
		_message('empty', 'error');
	}
	if(strlen($_POST['login']) > 20){
		_message('long', 'error');
	}
	if(preg_match('/[^0-9A-Za-z]/', $_POST['login'])){
		_message('wrongLogin', 'error');
	}
	if(strlen($var['user_agent']) > 256){
		_message('wrongUserAgent', 'error');
	}
	$query = $db->prepare("SELECT * FROM `users` WHERE `login` = :login");
	$query->bindValue(':login', $_POST['login']);
	$query->execute();
	if($query->rowCount() == 0){
		_message('Invalid user', 'error');
	}
	$row = $query->fetch();
	if(!empty($row['2fa'])){
		if(empty($_POST['fa2code'])){
			_message('empty', 'error');
		}
		if(oathHotp($row['2fa'], floor(microtime(true) / 30)) != $_POST['fa2code']){
			_message('wrong2FA', 'error');
		}
	}
	if(!password_verify($_POST['passwd'], $row['passwd'])){
		_message('wrongPasswd', 'error');
	}
	if(password_needs_rehash($row['passwd'], PASSWORD_DEFAULT)){
		$passwd = createPasswd($_POST['passwd']);
		$query = $db->prepare("UPDATE `users` SET `passwd` = :passwd WHERE `id` = :id");
		$query->bindParam(':passwd', $passwd[1]);
		$query->bindParam(':id', $row['id']);
		$query->execute();
		$row['passwd'] = $passwd[1];
	}
	$hash = session_hash($row['login'], $row['passwd']);
	$query = $db->prepare("INSERT INTO `session` (`uid`, `hash`, `time`, `ip`, `info`) VALUES (:uid, :hash, :time, INET6_ATON(:ip), :info)");
	$query->bindParam(':uid', $row['id']);
	$query->bindParam(':hash', $hash[0]);
	$query->bindParam(':time', $hash[1]);
	$query->bindParam(':ip', $var['ip']);
	$query->bindParam(':info', $var['user_agent']);
	$query->execute();
	$query = $db->prepare("SELECT `id` FROM `session` WHERE `uid` = :uid ORDER BY `time`");
	$query->bindParam(':uid', $row['id']);
	$query->execute();
	if($query->rowCount() > 10){
		$row = $query->fetch();
		$query = $db->prepare("DELETE FROM `session` WHERE `id` = :id");
		$query->bindParam(':id', $row['id']);
		$query->execute();
	}
	$_SESSION['sess'] = $hash[0];
	$query = $db->prepare("UPDATE `users` SET `last_activity` = :time WHERE `id` = :id");
	$query->bindParam(':time', $var['time']);
	$query->bindParam(':id', $row['id']);
	$query->execute();
	$query = $db->prepare("INSERT INTO `log_ip` (`uid`, `ip`, `time`, `info`) VALUES (:uid, INET6_ATON(:ip), :time, :info)");
	$query->bindParam(':uid', $row['id']);
	$query->bindParam(':ip', $var['ip']);
	$query->bindParam(':time', $var['time']);
	$query->bindParam(':info', $var['user_agent']);
	$query->execute();
	_message('success');
}

function password_link(){
	global $conf, $db, $var;
	if(empty($_GET['id']) || empty($_GET['time']) || empty($_GET['hash'])){
		_message('Empty get value', 'error');
	}
	if(!ctype_digit($_GET['id']) || !ctype_digit($_GET['time'])){
		_message('Wrong id or time', 'error');	
	}
	$query = $db->prepare("SELECT * FROM `users` WHERE `id` = :id");
	$query->bindParam(':id', $_GET['id']);
	$query->execute();
	if($query->rowCount() == 0){
		_message('No such user', 'error');
	}
	$row = $query->fetch();
	$hash = hash($conf['hash_algo'], $var['ip'].$_GET['id'].$_GET['time'].sha1(half_string($row['passwd'])));
	if($_GET['hash'] != $hash){
		_message('Wrong hash', 'error');
	}
	if($var['time'] > $_GET['time']){
		_message('Invalid link', 'error');
	}
	$passwd = createPasswd();
	$query = $db->prepare("UPDATE `users` SET `passwd` = :passwd WHERE `id` = :id");
	$query->bindValue(':id', $row['id']);
	$query->bindParam(':passwd', $passwd[1]);
	$query->execute();
	_mail($row['mail'], "Новый пароль", "Ваш пароль: $passwd[0]");
	_message('Success');
}

function testRecaptcha(){
	$v = 3;
	if(!empty($_POST['recaptcha']) && $_POST['recaptcha'] == 2){
		$v = $_POST['recaptcha'];
	}
	$result = recaptcha($v);
	if(!$result['success']){
		_message('reCaptcha test failed', 'error');
	}
	if($v == 3 && $result['score'] < 0.5){
		_message('reCaptcha test failed: score too low', 'error');
	}
}

function testCoinhive(){
	if(!coinhive_proof()){
		_message('Coinhive captcha error', 'error');
	}
}

function password_recovery(){
	global $conf, $db, $var;
	testRecaptcha();
	if(empty($_POST['mail'])){
		_message('empty', 'error');
	}
	if(strlen($_POST['mail']) > 254){
		_message('long', 'error');
	}
	if(!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)){
		_message('wrongEmail', 'error');
	}
	$query = $db->prepare("SELECT * FROM `users` WHERE `mail` = :mail");
	$query->bindParam(':mail', $_POST['mail']);
	$query->execute();
	if($query->rowCount() == 0){
		_message('noUser', 'error');
	}
	$row = $query->fetch();
	$time = $var['time']+43200;
	$hash = hash($conf['hash_algo'], $var['ip'].$row['id'].$time.sha1(half_string($row['passwd'])));
	$link = "https://" . $_SERVER['SERVER_NAME'] . "/public/password_link.php?id={$row['id']}&time={$time}&hash={$hash}";
	_mail($row['mail'], "Восстановление пароля", "Запрос отправили с IP {$var['ip']}<br/>Чтобы восстановить пароль <a href='$link'>перейдите по ссылке</a>.");
	_message('checkEmail');
}

function registration(){
	global $db, $user;
	if($user){
		_message('registered', 'error');
	}
	testRecaptcha();
	if(empty($_POST['login']) || empty($_POST['mail'])){
		_message('empty', 'error');
	}
	if(strlen($_POST['login']) > 20 || strlen($_POST['mail']) > 254){
		_message('long', 'error');
	}
	if(preg_match('/[^0-9A-Za-z]/', $_POST['login'])){
		_message('wrongLogin', 'error');
	}
	if(!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)){
		_message('wrongEmail', 'error');
	}
	$_POST['mail'] = mb_strtolower($_POST['mail']);
	$query = $db->prepare("SELECT * FROM `users` WHERE `login` = :login OR `mail`= :mail");
	$query->bindValue(':login', $_POST['login']);
	$query->bindParam(':mail', $_POST['mail']);
	$query->execute();
	if($query->rowCount() > 0){
		_message('registered', 'error');
	}
	$passwd = createPasswd();
	$query = $db->prepare("INSERT INTO `users` (`login`, `mail`, `passwd`, `register_date`) VALUES (:login, :mail, :passwd, unix_timestamp(now()))");
	$query->bindValue(':login', $_POST['login']);
	$query->bindParam(':mail', $_POST['mail']);
	$query->bindParam(':passwd', $passwd[1]);
	$query->execute();
	_mail($_POST['mail'], "Регистрация", "Вы успешно зарегистрировались на сайте!<br/>Ваш пароль: $passwd[0]");
	_message('success');
}

function auth(){
	global $conf, $db, $var, $user;
	if(!empty($_SESSION['sess'])){
		$query = $db->prepare("SELECT * FROM `session` WHERE `hash` = :hash AND `time` > unix_timestamp(now())");
		$query->bindParam(':hash', $_SESSION['sess']);
		$query->execute();
		if($query->rowCount() != 1){
			_exit();
		}
		$session = $query->fetch();
		$query = $db->prepare("SELECT * FROM `users` WHERE `id` = :id");
		$query->bindParam(':id', $session['uid']);
		$query->execute();
		if($query->rowCount() != 1){
			_exit();
		}
		$row = $query->fetch();
		if($_SESSION['sess'] != session_hash($row['login'], $row['passwd'], substr($session['hash'], 0, 8), $session['time'])[0]){
			_exit();
		}
		if($var['time'] > $session['time']){			
			$hash = session_hash($row['login'], $row['passwd']);
			$query = $db->prepare('UPDATE `session` set `hash` = :hash, `time` = :time WHERE `id` = :id');
			$query->bindParam(':hash', $hash[0]);
			$query->bindParam(':time', $hash[1]);
			$query->bindParam(':id', $session['id']);
			$query->execute();
			$_SESSION['sess'] = $hash[0];
		}
		$user = [	'id' => $row['id'], 
					'login' => $row['login'], 
					'nickname' => $row['nickname'],
					'avatar' => $row['avatar'],
					'passwd' => $row['passwd'], 
					'mail' => $row['mail'], 
					'2fa' => $row['2fa'],
					'access' => $row['access'],
					'register_date' => $row['register_date'],
					'last_activity' => $row['last_activity'],
					'dir' => substr(md5($row['id']), 0, 2),
				];
		if(!empty($row['user_values'])){			
			$user['user_values'] = json_decode($row['user_values'], true);
		}
	}
}

function base32_map($i, $do = 'encode'){
	$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
	if( $do == 'encode'){
		return $chars[$i];
	}else{
		return array_search($i, str_split($chars));
	}
}

function base32_bits($v){
	$value = ord($v);
	return vsprintf(str_repeat('%08b', count($value)), $value);
}

function base32_encode($data){
	$result = ''; $s = 0;
	$j = [4 => 1, 3 => 3, 2 => 4, 1 => 6];
	$arr = explode('|', substr(chunk_split($data, 5, '|'), 0, -1));
	foreach($arr as $val){
		$s++;
		$arr2 = str_split($val);
		$x = ['00000000', '00000000', '00000000', '00000000', '00000000'];
		foreach($arr2 as $key => $val2){
			$x[$key] = base32_bits($val2);	
		}
		$arr3 = explode('|', substr(chunk_split(implode('', $x), 5, '|'), 0, -1));
		foreach($arr3 as $key => $val3){	
			$result .= base32_map(bindec($val3));
		}
		if($s == count($arr) && isset($j[strlen($val)])){
			$result = str_pad(substr($result, 0, -$j[strlen($val)]), 8*$s, '=', STR_PAD_RIGHT);
		}
	}
	return $result;
}

function base32_decode($data){ // thx Sanasol
	$x = '';
	$arr = str_split($data);
	foreach($arr as $val){
		$x .= str_pad(decbin(base32_map($val, 'decode')), 5, '0', STR_PAD_LEFT);
	}
	$chunks = str_split($x, 8);
	$string = array_map(function($chr){
		return chr(bindec($chr));
	}, $chunks);
	return implode("", $string);
}

function generate_secret(){
	return base32_encode(genRandStr());
}

function oathTruncate($hash){
	$offset = ord($hash[19]) & 0xf;
	$temp = unpack('N', substr($hash, $offset, 4));
	return substr($temp[1] & 0x7fffffff, -6);
}

function oathHotp($secret, $time){
	$secret = base32_decode($secret);
	$time = pack('N*', 0, $time);
	$hash = hash_hmac('sha1', $time, $secret, true);
	return str_pad(oathTruncate($hash), 6, '0', STR_PAD_LEFT);
}

function getQRCodeGoogleUrl($name, $secret){
	$urlencoded = urlencode('otpauth://totp/'.$name.'?secret='.$secret.'');
	return 'https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl='.$urlencoded.'';
}

function auth2FA(){
	global $db, $user;
	if(!$user){
		_message('unauthorized', 'error');
	}
	if(empty($_POST['do'])){
		_message('empty', 'error');
	}
	switch($_POST['do']){
		default: return 'empty'; break;
		case 'gen':
			if(!empty($user['2fa'])){
				_message('2FA', 'error');				
			}
			$base32_key = generate_secret();
			_message2("<img src=".getQRCodeGoogleUrl($user['login']."@anilibria.tv", $base32_key)."><br>Secret key: $base32_key<br/>Сохраните секретный ключ в надежном месте.<input type=\"hidden\" id=\"2fa\" value=\"$base32_key\">");
		break;
		case 'save':
			if(empty($_POST['passwd']) || empty($_POST['code'])){
				_message('empty', 'error');
			}
			if(empty($user['2fa'])){
				if(empty($_POST['2fa'])){
					_message('empty', 'error');
				}
				$check = $_POST['2fa'];
			}else{
				$check = $user['2fa'];
			}
			if(strlen($check) != 16 || !ctype_alnum($check) || ctype_lower($check)){
				_message('wrong2FA', 'error');
			}
			if(oathHotp($check, floor(microtime(true) / 30)) != $_POST['code']){
				_message('wrong2FA', 'error');
			}
			if(!password_verify($_POST['passwd'], $user['passwd'])){
				_message('wrongPasswd', 'error');
			}
			if(!empty($user['2fa'])){
				$query = $db->prepare("UPDATE `users` SET `2fa` = :code WHERE `id` = :uid");
				$query->bindValue(':code', null, PDO::PARAM_INT);
				$query->bindParam(':uid', $user['id']);
				$query->execute();
				_message('2FAdisabled');
			}else{
				$query = $db->prepare("UPDATE `users` SET `2fa` = :code WHERE `id` = :uid");
				$query->bindParam(':code', $_POST['2fa']);
				$query->bindParam(':uid', $user['id']);
				$query->execute();
				_message('2FAenabled');
			}
		break;
	}
}

function recaptcha($v = 3){
	global $conf, $var;
	if(empty($_POST['g-recaptcha-response'])){
		_message('Empty post recaptcha', 'error');
	}
	$secret = 'recaptcha_secret';
	if($v != 3){
		$secret = 'recaptcha2_secret';
	}
	$data = ['secret' => $conf[$secret], 'response' => $_POST['g-recaptcha-response'], 'remoteip' => $var['ip']];
	$verify = curl_init();
	curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
	curl_setopt($verify, CURLOPT_POST, true);
	curl_setopt($verify, CURLOPT_POSTFIELDS, $data);
	curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
	$result = json_decode(curl_exec($verify), true);
	curl_close($verify);
	return $result;
}

function xSpiderBot($name){
	$arr = ['Google' => '/\.googlebot\.com$/i', 'Yandex' => '/\.spider\.yandex\.com$/i'];
	if(strpos($_SERVER['HTTP_USER_AGENT'], $name) !== false){
		return preg_match($arr["$name"], gethostbyaddr($_SERVER['REMOTE_ADDR']));
	}
	return false;
}

function secret_cookie(){
	global $conf, $var;
	$rand = genRandStr(8);
	$hash = hash($conf['hash_algo'], $var['ip'].$rand.$conf['sign_secret']);
	setcookie("ani_test", $hash.$_SERVER['REMOTE_ADDR'].$rand, $var['time'] + 86400, '/');
}

function simple_http_filter(){
	global $conf, $var;
	$flag = false;
	if(!empty($_COOKIE['ani_test'])){
		$string = $_COOKIE['ani_test'];
		$hash = substr($string, 0, $conf['hash_len']);
		$rand = substr($string, $conf['hash_len']+strlen($var['ip']));
		$test = hash($conf['hash_algo'], $var['ip'].$rand.$conf['sign_secret']);
		if($hash == $test){ 
			$flag = true;
		}
	}
	$list = ['RU', 'UA', 'BY', 'LV', 'EE', 'LT', 'TM', 'KG', 'KZ', 'MD', 'UZ', 'AZ', 'AM', 'GE'];
	if(!in_array(geoip_country_code_by_name($_SERVER['REMOTE_ADDR']), $list) && !$flag){
		if(xSpiderBot('Google') == false || xSpiderBot('Yandex') == false){			
			$tmpFilter = str_replace("{coinhive}", $conf['coinhive_public'], getTemplate('filter'));
			$tmpFilter = str_replace("{recaptcha}", $conf['recaptcha_public'], $tmpFilter);
			echo $tmpFilter;
			die;
		}
	}
}

function torrentHashExist($hash){
	global $db;
	$query = $db->prepare("SELECT * FROM xbt_files WHERE `info_hash` = :hash");
	$query->bindParam(':hash', $hash);
	$query->execute();
	if($query->rowCount() == 0){
		return false;
	}
	return true;
}

function torrentExist($id){
	global $db;
	$query = $db->prepare("SELECT * FROM `xbt_files` WHERE `fid`= :id");
	$query->bindParam(':id', $id);
	$query->execute();
	return $query->fetch();
}

function torrentAdd($hash, $rid, $json, $completed = 0){
	global $db;
	$query = $db->prepare("INSERT INTO `xbt_files` (`info_hash`, `mtime`, `ctime`, `flags`, `completed`, `rid`, `info`) VALUES( :hash , UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, :completed, :rid, :info)");
	$query->bindParam(':hash', $hash);
	$query->bindParam(':rid', $rid);
	$query->bindParam(':completed', $completed);
	$query->bindParam(':info', $json);
	$query->execute();
	return $db->lastInsertId();	
}

// https://github.com/shakahl/xbt/wiki/XBT-Tracker-(XBTT)
// flags - This field is used to communicate with the tracker. Usable values: 0 - No changes. 1 - Torrent should be deleted. 2 - Torrent was updated.
// flag 1 work		https://github.com/OlafvdSpek/xbt/blob/master/Tracker/server.cpp#L183-L187
// source code		https://img.poiuty.com/img/6e/f01f40eaa783018fe12e5649315b716e.png
// flag 2 not work	https://img.poiuty.com/img/7c/a5479067a6e3a272d66bb92c0416797c.png
// Also I dont find it in source.
function torrentDelete($id){
	global $db;
	$query = $db->prepare("UPDATE `xbt_files` SET `flags` = 1 WHERE `fid` = :id");
	$query->bindParam(':id', $id);
	$query->execute();
	$file = $_SERVER['DOCUMENT_ROOT'].'/upload/torrents/'.$id.'.torrent';
	if(file_exists($file)) {
		unlink($file);
	}
}

function torrent(){
	global $conf, $db, $user, $var;
	if(!$user){
		_message('Unauthorized user', 'error');
	}
	if($user['access'] < 4){
		_message('Access denied', 'error');
	}
	if(empty($_POST['do'])){
		_message('Empty GET', 'error');
	}
	switch($_POST['do']){
		case 'delete':
			if(empty($_POST['edit_torrent']) || !ctype_digit($_POST['edit_torrent'])){
				_message('No edit_torrent', 'error');
			}
			torrentDelete($_POST['edit_torrent']);
			_message('Finish, we delete torrent');
		break;
		default:
			if(empty($_POST['rid']) || empty($_POST['quality']) || empty($_POST['episode'])){
				_message('Set release id, name, quality and episode', 'error');
			}
			if(!ctype_digit($_POST['rid'])){
				_message('Release ID allow numeric', 'error');
			}
			if(strlen($_POST['quality']) > 200 || strlen($_POST['episode']) > 200){
				_message('Max strlen 200', 'error');
			}
			if(empty($_FILES['torrent'])){
				_message('No upload file', 'error');
			}
			if($_FILES['torrent']['error'] != 0){
				_message('Upload error', 'error');
			}
			if($_FILES['torrent']['type'] != 'application/x-bittorrent'){
				_message('You can upload only torrents', 'error');	
			}
			$torrent = new Torrent($_FILES['torrent']['tmp_name']);
			if(empty($torrent->hash_info())){
				_message('Wrong torrent file', 'error');
			}
			$pack_hash = pack('H*', $torrent->hash_info());
			if(torrentHashExist($pack_hash)){
				_message('Torrent hash already exist', 'error');
			}
			$json = json_encode([$_POST['quality'], $_POST['episode'], $torrent->size()]);
			if(empty($_POST['edit_torrent'])){
				$name = torrentAdd($pack_hash, $_POST['rid'], $json);
			}else{
				if(!ctype_digit($_POST['edit_torrent'])){
					_message('edit_torrent allow only numeric', 'error');
				}
				$old = torrentExist($_POST['edit_torrent']);
				if(!is_array($old)){
					_message('No old torrent', 'error');
				}
				$name = torrentAdd($pack_hash, $_POST['rid'], $json, $old['completed']);
				torrentDelete($_POST['edit_torrent']);
			}
			$torrent->announce(false);
			$torrent->announce($conf['torrent_announce']);
			$torrent->save($_SERVER['DOCUMENT_ROOT'].'/upload/torrents/'.$name.'.torrent');
			_message('Success');
		break;
	}
}

function downloadTorrent(){
	global $db, $user, $conf;
	if(!$user){
		_message('Unauthorized user', 'error');
	}
	if(empty($_GET['id'])){
		_message('Empty $_GET', 'error');
	}
	if(!ctype_digit($_GET['id'])){
		_message('Wrong id', 'error');
	}
	$query = $db->prepare("SELECT * FROM `xbt_files` WHERE `fid` = :id");
	$query->bindParam(':id', $_GET['id']);
	$query->execute();
	if($query->rowCount() == 0){
		_message('Wrong id', 'error');
	}
	$info_hash = $query->fetch()['info_hash'];	

	$query = $db->prepare("SELECT * FROM `xbt_users` WHERE `torrent_pass_version` = :id");
	$query->bindParam(':id', $user['id']);
	$query->execute();
	if($query->rowCount() == 0){
		$query = $db->prepare("INSERT INTO `xbt_users` (`torrent_pass_version`) VALUES (:id)");
		$query->bindParam(':id', $user['id']);
		$query->execute();
		$uid = $db->lastInsertId();
	}else{
		$uid = $query->fetch()['uid'];
	}
	$key = sprintf('%08x%s', $uid, substr(sha1("{$conf['torrent_secret']} {$user['id']} $uid $info_hash"), 0, 24));
	$torrent = new Torrent($_SERVER['DOCUMENT_ROOT']."/upload/torrents/{$_GET['id']}.torrent");
	$torrent->announce(false);
	$torrent->announce(str_replace('/announce', "/$key/announce", $conf['torrent_announce']));
	$torrent->send();
}

function upload_avatar() {
	global $db, $user;
	if(!$user){
		_message('unauthorized', 'error');
	}
	
	if(empty($_FILES['avatar'])){
		_message('noUploadFile', 'error');
	}
	
	if($_FILES['avatar']['error'] != 0){
		_message('uploadError', 'error');
	}
	
	if(!in_array(exif_imagetype($_FILES['avatar']['tmp_name']), [IMAGETYPE_PNG, IMAGETYPE_JPEG])){
		_message('onlyPngJpg', 'error');	
	}
	if($_FILES['avatar']['size'] > 150000){
		_message('maxSize', 'error');
	}
	
	$img = new Imagick($_FILES['avatar']['tmp_name']);
	$img->setImageFormat('jpg');
	
	$limit = 0; $crop = true;
	foreach($_POST as $k => $v){
		$limit++;
		if(!in_array($k, ['w', 'h', 'x1', 'y1']))
			$crop = false;	
		
		if(empty($v) && $v != 0)
			$crop = false;

		if(!ctype_digit($v))
			$crop = false;

		if($limit > 4)
			$crop = false;

		if($crop == false)
			break;	
	}
	
	if($crop) $img->cropImage($_POST['w'], $_POST['h'], $_POST['x1'], $_POST['y1']);
	$img->resizeImage(160,160,Imagick::FILTER_LANCZOS, 1, false);
	$img->setImageCompression(Imagick::COMPRESSION_JPEG);
	$img->setImageCompressionQuality(90);
	$img->stripImage();
	
	$name = hash('crc32', $img);
	$tmp = $dir = '/upload/avatars/'.$user['dir'];
	$dir = $_SERVER['DOCUMENT_ROOT'].$dir;
	$file = "$dir/$name.jpg";
	if(!file_exists($dir)) {
		mkdir($dir, 0755, true);
	}
	file_put_contents($file, $img);
	if(!empty($user['avatar']) && $user['avatar'] != $name){
		$old = "$dir/{$user['avatar']}.jpg";
		if(file_exists($old)){
			unlink($old);
		}
	}
	
	$query = $db->prepare("UPDATE `users` SET `avatar` = :avatar WHERE `id` = :id");
	$query->bindParam(':avatar', $name);
	$query->bindParam(':id', $user['id']);
	$query->execute();

	_message2("$tmp/$name.jpg");
}

function getUserAvatar($id = ''){
	global $user;
	if(empty($id) && !empty($user['id'])){
		$id = $user['id'];
	}
	if(empty($id) || !ctype_digit($id)){
		return ['err' => true, 'mes' => 'Wrong ID'];
	}
	$img = "https://".$_SERVER['SERVER_NAME']."/upload/avatars/noavatar.png";
	$dir = substr(md5($id), 0, 2);
	$path = "/upload/avatars/$dir/$id.jpg";
	if(file_exists($_SERVER['DOCUMENT_ROOT'].$path)){
		$img = "https://".$_SERVER['SERVER_NAME'].$path;
	}
	return $img;
}

function userInfo($id){
	global $db, $user, $var; $result = [];
	if(empty($id) || !ctype_digit($id)){
		return ['err' => true, 'mes' => 'Wrong ID'];
	}
	if(!empty($user['id']) && $user['id'] == $id){
		$result = [
			'id' => $user['id'],
			'mail' => $user['mail'],
			'login' => $user['login'],
			'nickname' => $user['nickname'] ?? $user['login'],
			'access' => $user['access'],
			'register_date' => $user['register_date'],
			'last_activity' => $user['last_activity'],
			'user_values' => @$user['user_values']
		];
	}
	if(empty($result)){
		$query = $db->prepare("SELECT * FROM `users` WHERE `id` = :id");
		$query->bindValue(':id', $id);
		$query->execute();
		if($query->rowCount() == 0){
			return ['err' => true, 'mes' => 'К сожалению, такого пользователя не существует.'];
		}
		$row = $query->fetch();
		$result = [
			'id' => $row['id'],
			'nickname' => $row['nickname'] ?? $row['login'],
			'access' => $row['access'],
			'register_date' => $row['register_date'],
			'last_activity' => $row['last_activity'],
			'user_values' => $row['user_values']
		];
		if(!empty($result['user_values'])){
			$result['user_values'] = json_decode($result['user_values'], true);
		}
	}
	return ['err' => false, 'mes' => $result];
}

function userInfoShow($id){
	global $var;
	if(!empty($id) && ctype_digit($id)){
		$profile = userInfo($id);
	}else{
		$profile = ['err' => true, 'mes' => 'К сожалению, такого пользователя не существует.'];
	}
	if($profile['err']) {	
		return str_replace('{error}', $profile['mes'],  getTemplate('error'));
	}else{
		$a = $b = '';
		foreach($profile['mes'] as $key => $val){
			if($key == 'user_values'){
				if(empty($val)){
					continue;
				}
				foreach($val as $k => $v){
					if($k == 'sex'){
						$v = $var['sex'][$v];
					}
					if($k == 'age'){
						$v = floor(($var['time'] - $v) / 31556926);
					}
					$a .= "<b>{$var['user_values'][$k]}</b><span>&nbsp;$v</span><br/>";
				}
				continue;
			}
			if($key == 'register_date' || $key == 'last_activity'){
				$val = date('Y-m-d', $val);
			}
			if($key == 'access'){
				$val = $var['group'][$val];
			}
			$a .= "<b>{$var['user_values'][$key]}:</b><span>&nbsp;$val</span><br/>";
		}
		$b = "<img class=\"rounded\" id=\"avatar\" src=\"".getUserAvatar($id)."\" alt=\"avatar\">";
		$a = str_replace('{userinfo}', $a,  getTemplate('user_info'));
		$b = str_replace('{avatar}', $b,  getTemplate('user_avatar'));
		return $a.$b;
	}
}

function getTemplate($template){
	$file = $_SERVER['DOCUMENT_ROOT']."/private/template/$template.html";
	if(!file_exists($file)){
		return ['err' => true, 'mes' => 'Template not exists'];
	}
	return file_get_contents($file);
}

// {"sex": "", "vk":"", "telegram": "", "steam": "", "age": "", "country": "", "city": ""}
// sex	int 0, 1, 2
// age	strtotime
function saveUserValues(){
	global $db, $user, $var; $arr = [];
	if(!$user){
		_message('Unauthorized user', 'error');
	}
    if(empty($_POST)){
		_message('Empty post', 'error');	
	}
	if(count($_POST) > 10){		
		_message('Too much args1', 'error');
	}
    foreach($_POST as $key => $val){		
		if(empty($val) || !array_key_exists($key, $var['user_values'])){
			continue;
		}
		if(!preg_match('/^[А-Яа-яA-Za-z0-9_.-]+$/u', $val)){
			_message('Wrong chars', 'error');
		}
		if(mb_strlen($val) > 30){
			_message('Max len 30', 'error');
		}
		$arr[$key] = htmlspecialchars($val);
	}
	if(!empty($arr['sex']) && (!ctype_digit($arr['sex']) || ($arr['sex'] < 0 || $arr['sex'] > 2))){
		_message('Wrong sex', 'error');
	}
    if(!empty($arr['age'])){
		$time = strtotime($arr['age']);
		if(!$time || $time > $var['time'] || date('Y', $time) < date('Y', $var['time'])-80){
			_message('Wrong time', 'error');
		}
		$arr['age'] = $time;
	}
    $json = json_encode($arr);
    if(strlen($json) > 1024){
		_message('Max len 1024', 'error');
	}
	$query = $db->prepare("UPDATE `users` SET `user_values` = :user_values WHERE `id` = :id");
	$query->bindParam(':user_values', $json);
	$query->bindParam(':id', $user['id']);
	$query->execute();
	_message('Data saved');
}


function cryptAES($text, $key, $do = 'encrypt'){
	$key = hash('sha256', $key, true);
	$iv_size = openssl_cipher_iv_length($cipher = 'AES-256-CBC');
	$iv = random_bytes($iv_size);
	if($do == 'encrypt'){
		$ciphertext_raw = openssl_encrypt($text, $cipher, $key, OPENSSL_RAW_DATA, $iv);
		$hmac = hash_hmac('sha512', $ciphertext_raw, $key, true);		
		$ciphertext = base64_encode($iv.$hmac.$ciphertext_raw);
		return $ciphertext;
	}else{
		$c = base64_decode($text);
		$iv_dec = substr($c, 0, $iv_size);
		$hmac = substr($c, $iv_size, $sha2len=64);
		$ciphertext_raw = substr($c, $iv_size+$sha2len);
		$original = openssl_decrypt($ciphertext_raw, $cipher, $key, OPENSSL_RAW_DATA, $iv_dec);
		$calcmac = hash_hmac('sha512', $ciphertext_raw, $key, true);
		if(hash_equals($hmac, $calcmac)){
			return $original;
		}
	}
}

function change_mail(){
	global $db, $user, $var, $conf;
	if(!$user){
		_message('unauthorized', 'error');
	}
	if(empty($_POST['mail']) || empty($_POST['passwd'])){
		_message('empty', 'error');	
	}
	if(!password_verify($_POST['passwd'], $user['passwd'])){
		_message('wrongPasswd', 'error');
	}
	if(!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)){
		_message('wrongEmail', 'error');
	}
	if($_POST['mail'] == $user['mail']){
		_message('same', 'error');
	}
    $_POST['mail'] = mb_strtolower($_POST['mail']);
    $query = $db->prepare("SELECT `id` FROM `users` WHERE `mail` = :mail");
    $query->bindParam(':mail', $_POST['mail']);
	$query->execute();
	if($query->rowCount() > 0){
		_message('used', 'error');
	}
    $time = $var['time'] + 43200;
    $hash = hash($conf['hash_algo'], $var['ip'] . $user['id'] . $user['mail'] . $_POST['mail'] . $time . sha1(half_string($user['passwd'])));
    $link = "https://" . $_SERVER['SERVER_NAME'] . "/public/mail_link.php?time=$time&mail=" . urlencode($_POST['mail']) . "&hash=$hash";
    _mail($user['mail'], "Изменение email", "Запрос отправили с IP {$var['ip']}<br/>Если вы хотите изменить email на {$_POST['mail']} - <a href='$link'>перейдите по ссылке</a>.");
    _message('checkEmail');
}

function mail_link(){
	global $db, $user, $var, $conf;
	if(!$user){
		_message('Unauthorized user', 'error');
	}
	if(empty($_GET['time']) || empty($_GET['mail']) || empty($_GET['hash'])){
		_message('Empty $_GET', 'error');
	}
	if($var['time'] > $_GET['time']){
		_message('Too late $_GET', 'error');	
	}
	$_GET['mail'] = urldecode($_GET['mail']);
	if(!filter_var($_GET['mail'], FILTER_VALIDATE_EMAIL)){
		_message('Wrong email', 'error');
	}
	$hash = hash($conf['hash_algo'], $var['ip'].$user['id'].$user['mail'].$_GET['mail'].$_GET['time'].sha1(half_string($user['passwd'])));
	if($hash != $_GET['hash']){
		_message('Wrong hash', 'error');
	}
	$query = $db->prepare("SELECT `id` FROM `users` WHERE `mail` = :mail");
	$query->bindParam(':mail', $_GET['mail']);
	$query->execute();
	if($query->rowCount() > 0){
		_message('Email already use', 'error');
	}
	$query = $db->prepare("UPDATE `users` SET `mail` = :mail WHERE `id` = :id");
	$query->bindParam(':mail', $_GET['mail']);
	$query->bindParam(':id', $user['id']);
	$query->execute();
	_message('Success');
}

function change_passwd(){
	global $db, $user, $var, $conf;
	if(!$user){
		_message('unauthorized', 'error');
	}
	if(empty($_POST['passwd'])){
		_message('empty', 'error');
	}
	if(!password_verify($_POST['passwd'], $user['passwd'])){
		_message('wrongPasswd', 'error');
	}
	$passwd = createPasswd();
	$query = $db->prepare("UPDATE `users` SET `passwd` = :passwd WHERE `id` = :id");
	$query->bindParam(':passwd', $passwd[1]);
	$query->bindParam(':id', $user['id']);
	$query->execute();
	_mail($user['mail'], "Изменение пароля", "Запрос отправили с IP {$var['ip']}<br/>Ваш новый пароль: {$passwd[0]}");
	_message('checkEmail');
}

function pageStat(){
	global $conf;
	return "Page generated in ".round((microtime(true) - $conf['start']), 4)." seconds. Peak memory usage: ".round(memory_get_peak_usage()/1048576, 2)." MB";
}

function show_sess(){
	global $db, $user, $conf;
	if(!$user){
		_message('Unauthorized user', 'error');
	}
	$query = $db->prepare("SELECT * FROM `session` WHERE `uid` = :id");
	$query->bindParam(':id', $user['id']);
	$query->execute();
	return $query->fetchAll();
}

function close_sess(){
	global $db, $user, $conf;
	if(!$user){
		_message('Unauthorized user', 'error');
	}
	if(empty($_POST['id']) || !ctype_digit($_POST['id'])){
		_message('Wrong sess id', 'error');
	}
	$query = $db->prepare("DELETE FROM `session` WHERE `id` = :id AND `uid` = :uid");
	$query->bindParam(':id', $_POST['id']);
	$query->bindParam(':uid', $user['id']);
	$query->execute();
	if($query->rowCount() != 1){
		_message('Cant close session', 'error');
	}
	_message('Success');
}

function formatBytes($size, $precision = 2){
    $base = log($size, 1024);
    $suffixes = ['', 'KB', 'MB', 'GB', 'TB'];
    return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
}

function showRelease(){
	global $db, $user;
	
	function getList($text){
		if(empty($text)){
			return '-';
		}
		$arr = explode(',', $text);
		if(!is_array($arr)){
			return rtrim("<a href='#'>$text</a>");
		}
		$result = '';
		foreach($arr as $k => $v){
			$result .= "<a href='#'>".trim($v)."</a> ";
		}
		return rtrim($result);
	}
	
	$query = $db->prepare("SELECT * FROM `page` WHERE `id` = :id");
	$query->bindParam(':id', $_GET['id']);
	$query->execute();
	if($query->rowCount() != 1){
		return str_replace('{error}', 'К сожалению, такого релиза не существует.',  getTemplate('error'));
	}
	$row = $query->fetch();
	
	$status = ['0' => 'В работе', '1' => 'Завершен'];
	
	$page = str_replace('{runame}', $row['name'],  getTemplate('torrent'));
	$page = str_replace('{engname}', $row['ename'],  $page);
	$page = str_replace('{img}', $row['id'],  $page);
	$page = str_replace('{genre}', getList($row['genre']),  $page);
	$page = str_replace('{voice}', getList($row['voice']),  $page);
	$page = str_replace('{season}', "<a href='#'>{$row['season']} {$row['year']}</a>",  $page);
	$page = str_replace('{translator}', $row['translator'], $page);
	$page = str_replace('{timing}', $row['timing'], $page);
	$page = str_replace('{design}', $row['design'], $page);
	$page = str_replace('{type}', $row['type'], $page);
	$page = str_replace('{status}', $status[$row['status']], $page);
	$page = str_replace('{description}', $row['description'], $page);
	
	if(!empty($user) && $user['access'] > 2){
		$page = str_replace('{edit}', '', $page);
		$page = str_replace('{hidden}', "<input type='hidden' id='rid' name='rid' value={$row['id']}>", $page);
	}else{
		$page = str_replace('{edit}', 'style="display: none;"', $page);
		$page = str_replace('{hidden}', '', $page);
	}
	
	$torrent = $db->prepare("SELECT * FROM `xbt_files` WHERE `rid` = :id");
	$torrent->bindParam(':id', $row['id']);
	$torrent->execute();
	$showTorrent = '';
	while($data = $torrent->fetch()){
		
		$download = "/upload/torrents/{$data['fid']}.torrent";
		$control = '';
		if(!empty($user)){
			if($user['access'] > 2){
				$control =  " | Удалить | Обновить";
			}
			$download = "/public/torrent_download.php?id={$data['fid']}";
		}
		
		$data['info'] = json_decode($data['info'], true);
		$data['info']['2'] = formatBytes($data['info']['2']);
		$data['ctime'] = date('d.m.Y H:m', $data['ctime']);
		$showTorrent .= "
			<tr>
			<td style='text-align:center;vertical-align:middle'>Серия {$data['info']['1']} [{$data['info']['0']}]</td>
			<td style='text-align:center;vertical-align:middle'>Вес {$data['info']['2']} | Раздают {$data['seeders']} | Качают {$data['leechers']} | Скачало {$data['completed']}</td>
			<td style='text-align:center;vertical-align:middle'>Добавлен {$data['ctime']}</td>
			<td style='text-align:center;vertical-align:middle'><a href='$download'>Скачать</a>$control</td>
			</tr>
		";
	}
	$page = str_replace('{table}', $showTorrent, $page);
	return $page;
}

function check_poster(){
	if(empty($_FILES['poster'])){
		return ['err' => false, 'mes' =>'No upload file'];
	}
	if($_FILES['poster']['error'] != 0){
		return ['err' => false, 'mes' =>'Upload error'];
	}
	if($_FILES['poster']['type'] != 'image/jpeg'){
		return ['err' => false, 'mes' =>'You can upload only jpeg'];
	}
	if($_FILES['poster']['size'] > 1000000){
		return ['err' => false, 'mes' =>'Max size'];
	}
	return ['err' => true];
}

function add_release(){
	global $db, $user, $var;
	if(!$user){
		_message('Unauthorized user', 'error');
	}
	if($user['access'] < 4){
		_message('Access deny', 'error');
	}
	$data = [];
	$sql = ['col' => '', 'val' => ''];
	$arr = ['name', 'ename', 'genre', 'voice', 'translator', 'timing', 'design', 'year', 'season', 'type', 'description'];
	foreach($arr as $key){
		$_POST[$key] = htmlspecialchars($_POST[$key]);
		$sql['col'] .= "`$key`,";
		$sql['val'] .= ":$key,";
		$data[] = $key;
	}
	$sql['col'] = rtrim($sql['col'], ',');
	$sql['val'] = rtrim($sql['val'], ',');
	$check = check_poster();
	if(!$check['err']){
		_message($check['mes'], 'error');
	}
	if(empty($sql['col'])){
		_message('Empty post', 'error');	
	}
	$query = $db->prepare("INSERT INTO `page` ({$sql['col']}) VALUES ({$sql['val']})");
	foreach($data as $k => $v){
		$query->bindParam(":$v", $_POST[$v]);
	}
	$query->execute();
	$id = $db->lastInsertId();
	move_uploaded_file($_FILES['poster']['tmp_name'], $_SERVER['DOCUMENT_ROOT']."/upload/torrent/$id.jpg");
	_message('Success');
}

function edit_release(){
	global $db, $user, $var;
	if(!$user){
		_message('Unauthorized user', 'error');
	}
	if($user['access'] < 4){
		_message('Access deny', 'error');
	}
	if(empty($_POST['id']) || !ctype_digit($_POST['id'])){
		_message('Wrong release id', 'error');
	}
	$query = $db->prepare("SELECT * FROM `page` WHERE `id` = :id");
	$query->bindParam(':id', $_POST['id']);
	$query->execute();
	if($query->rowCount() != 1){
		_message('Release not exists', 'error');
	}
	$check = check_poster();
	if($check['err']){
		$file = $_SERVER['DOCUMENT_ROOT']."/upload/torrent/{$_POST['id']}.jpg";
		if(file_exists($file)){
			unlink($file);
		}
		move_uploaded_file($_FILES['poster']['tmp_name'], $file);
	}
	$data = []; $sql = '';
	$arr = ['name', 'ename', 'genre', 'voice', 'translator', 'timing', 'design', 'year', 'season', 'type', 'description'];
	foreach($arr as $key){
		if(!empty($_POST[$key])){
			$sql .= "`$key` = :$key,";
			$data[] = $key;
		}
	}
	if(!empty($sql)){
		$sql = rtrim($sql, ',');
		$query = $db->prepare("UPDATE `page` SET $sql WHERE `id` = :id");
		foreach($data as $k => $v){
			$_POST[$v] = htmlspecialchars($_POST[$v]);
			$query->bindParam(":$v", $_POST[$v]);
		}
		$query->bindParam(':id', $_POST['id']);
		$query->execute();
	}
	_message('Success');
}

function set_nickname(){
	global $db, $user;
	if(!$user){
		_message('Unauthorized user', 'error');
	}
	if(!empty($user['nickname'])){
		_message('Already set nickname', 'error');
	}
	if(empty($_POST['nickname'])){
		_message('Empty nickname', 'error');
	}
	if(mb_strlen($_POST['nickname']) > 20){
		_message('Nickname max len 20', 'error');
	}
	$_POST['nickname'] = htmlspecialchars($_POST['nickname']);
	$query = $db->prepare("SELECT `id` FROM `users` WHERE `nickname` = :nickname");
	$query->bindParam(':nickname', $_POST['nickname']);
	$query->execute();
	if($query->rowCount() > 0){
		_message('Nickname already use', 'error');
	}
	$query = $db->prepare("UPDATE `users` SET `nickname` = :nickname WHERE `id` = :id");
	$query->bindParam(':nickname', $_POST['nickname']);
	$query->bindParam(':id', $user['id']);
	$query->execute();
	_message('Success');
}

// block country, user access
// ['RU, JP', 4], ['JP'], ['', 2]
function check_block($arr){
	global $db, $user, $var;
	if(!is_array($arr)){
		return true;
	}
	if(strpos($arr[0], geoip_country_code_by_name($var['ip'])) === false){
		return false;
	}
	if(!empty($arr[1])){
		if(empty($user) || $user['access'] < $arr[1]){
			return false;
		}
	}
	return true;
}
