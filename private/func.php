<?php

function createPasswd($passwd = '') // DONE
{
    if (empty($passwd)) {
        $passwd = genRandStr(8, 1);
    }
    return [$passwd, password_hash($passwd, PASSWORD_ARGON2ID, ['memory_cost' => 1 << 14, 'time_cost' => 3, 'threads' => 2])];
}

function genRandStr($length = 10, $mode = 2) // DONE
{
    $str = '';
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if ($mode == 2) {
        $chars .= '~!@#$%^&*()_+-=';
    }
    for ($i = 0; $i < $length; $i++) {
        $str .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $str;
}

function _mail($email, $subject, $message) // DONE
{
    try {

        global $conf;

        require '/var/www/html/private/vendor/PHPMailer/src/PHPMailer.php';
        require '/var/www/html/private/vendor/PHPMailer/src/SMTP.php';

        $mail = new \PHPMailer\PHPMailer\PHPMailer(false);

        //$mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host = $conf['email_host'];                   //Set the SMTP server to send through
        $mail->SMTPAuth = true;                                   //Enable SMTP authentication
        $mail->Username = $conf['email_login'];                     //SMTP username
        $mail->Password = $conf['email_password'];                               //SMTP password
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port = 465;
        $mail->XMailer = 'AniLibria.TV';
        $mail->CharSet = 'UTF-8'; // the same as 'utf-8'//TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom($conf['email_login'], $conf['email_sender']);
        $mail->addAddress($email);

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();

    } catch (Throwable $exception) {

        // Ignore
    }
}

function _message($key, $err = 'ok', array $payload = [], int $statusCode = 200) // DONE
{
    global $var;
    $response = array_merge(['err' => $err, 'mes' => $var['error'][$key], 'key' => $key], $payload);
    http_response_code($statusCode);
    die(json_encode($response));
}

function _message2($mes) // DONE
{
    die(json_encode(['err' => 'ok', 'mes' => $mes]));
}

function half_string_hash($s) // DONE
{
    global $conf;
    return hash($conf['hash_algo'], substr($s, round(strlen($s) / 2)));
}

function session_hash($login, $passwd, $access, $rand = '') // DONE
{
    global $conf, $var;
    if (empty($rand)) {
        $rand = genRandStr(8);
    }
    return [$rand . hash($conf['hash_algo'], $rand . $var['user_agent'] . $login . sha1(half_string_hash($passwd))), $var['time'] + 60 * 60 * 24 * 30];
}

function _exit() // DONE
{
    global $db, $var, $user;

    $cookieSession = $_COOKIE['PHPSESSID'] ?? null;

    if (empty($cookieSession) === false) {
        $query = $db->prepare('DELETE FROM `users_sessions` WHERE `id` = :session');
        $query->bindParam(':session', $cookieSession);
        $query->execute();
    }

    header("Set-Cookie: PHPSESSID=deleted; expires=Tue, 06-May-2000 20:40:00 GMT; path=/;", false);
    header("Set-Cookie: PHPSESSID=deleted; expires=Tue, 06-May-2000 20:40:00 GMT; path=/; domain=.anilibria.tv", false);
    header("Set-Cookie: PHPSESSID=deleted; expires=Tue, 06-May-2000 20:40:00 GMT; path=/; domain=.www.anilibria.tv", false);

}

function enableCSRF($force = false) // DONE
{
    if (!empty($_POST['csrf']) || $force) {
        $_SESSION['csrf'] = 1;
    }
}

function disableCSRF() // DONE
{
    if (!empty($_SESSION['csrf'])) {
        unset($_SESSION['csrf']);
    }
}

function csrf_token() // DONE
{
    global $var;
    $htime = $var['time'] + 60 * 60 * 24;
    return ['hash' => createSecret($htime), 'time' => $htime];
}

function createSecret($params) // DONE
{
    global $conf;

    /* if (empty($_SESSION['secret'])) {
         return false;
     }*/

    return hash($conf['hash_algo'], $params);
}

function checkSecret($hash, $params) // DONE
{
    global $conf;
    if (empty($_SESSION['secret']) || $hash != hash($conf['hash_algo'], $_SESSION['secret'] . $params)) {
        return false;
    }
    return true;
}

function checkCSRF() // DONE
{
    global $var;
    if (!empty($_SESSION['csrf'])) {
        if (empty($_POST['csrf_token'])) {
            _message('wrong', 'error');
        }
        $arr = json_decode($_POST['csrf_token'], true);
        if (!is_array($arr) || empty($arr['time']) || empty($arr['hash'])) {
            _message('empty', 'error');
        }
        if ($var['time'] > $arr['time'] || !checkSecret($arr['hash'], $arr['time'])) {
            _message('wrong', 'error');
        }
    }
}

function vkAuth() // DONE
{
    global $conf;
    $result = false;

    if (!empty($_GET ['code'])) {

        $data = [
            'client_id' => $conf['vk_id'],
            'client_secret' => $conf['vk_secret'],
            'redirect_uri' => 'https://www.anilibria.tv/public/vk.php', //sprintf('%s/public/vk.php', _getHostname()),  //'https://www.anilibria.tv/public/vk.php',
            'code' => $_GET["code"]
        ];

        $string = http_build_query($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, "https://oauth.vk.com/access_token?" . urldecode($string));
        $tmp = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if (!empty($tmp['user_id'])) {
            $result = $tmp['user_id'];
        }
    }

    return $result;
}

function vkAuthLink() // DONE
{
    global $conf;
    $vkParams = [
        'client_id' => $conf['vk_id'],
        'redirect_uri' => 'https://www.anilibria.tv/public/vk.php', //sprintf('%s/public/vk.php', _getHostname()),  //'https://www.anilibria.tv/public/vk.php',
    ];
    return 'https://oauth.vk.com/authorize?' . urldecode(http_build_query($vkParams));
}

function getUserVK($id) // DONE
{
    global $db;
    $query = $db->prepare('SELECT `id`, `login`, `password` AS `passwd`, NULL AS `2fa`, 1 AS `access` FROM `users` WHERE `vk_id` = :id');
    $query->bindParam(':id', $id);
    $query->execute();
    if ($query->rowCount() == 0) {
        return false;
    }
    return $query->fetch();
}

function oAuthLogin() // DONE
{
    global $db, $var, $user, $conf;

    if ($user) {

        //_message('authorized', 'error');
        die(header('Location: /'));
    }

    $id = vkAuth();

    if (!$id) {
        _message2('vk auth error', 'error');
    }

    $row = getUserVK($id);

    if (!$row) {

        $htime = $var['time'] + 60 * 60;
        $hash = createSecret($id . $htime);

        if (!$hash) {
            _message2('wrong', 'error');
        }

        die(header("Location: " . sprintf('%s/pages/vk.php?id=%s&time=%s&hash=%s', _getHostname(), $id, $htime, $hash)));
    }

    /*if (!empty($row['2fa'])) {
        _message2('please disable 2fa', 'error');
    }*/

    enableCSRF(true);
    startSession($row);

    if (strpos($var['user_agent'], 'mobileApp') === false) {
        header("Location: /");
    }
}

/* OTP Auth start */
function generateOtpCode($length) // DONE
{
    $symbols = "0123456789";
    $result = "";

    for ($i = 1; $i <= $length; $i++) {
        $result .= substr($symbols, (rand() % (strlen($symbols))), 1);
    }

    return $result;
}

function deleteExpiredOtpCodes() // DONE
{
    global $db, $var, $user, $conf;

    $otpDeleteQuery = $db->prepare('DELETE FROM `otp` WHERE `expired_at` < NOW()');
    $otpDeleteQuery->execute();
}

function getOtpCode() // DONE
{
    global $db, $var, $user, $conf;
    $argDeviceId = $_POST['deviceId'];

    if ($user) {
        _message('authorized', 'error');
    }
    if (empty($argDeviceId)) {
        _message('empty device id', 'error');
    }

    deleteExpiredOtpCodes();

    $otpQuery = $db->prepare('SELECT `code`, `expired_at` FROM `otp` WHERE `device_id` = :deviceId AND `expired_at` > NOW()');
    $otpQuery->bindValue(':deviceId', $argDeviceId);
    $otpQuery->execute();
    if ($otpQuery->rowCount() == 0) {
        $code = generateOtpCode(6);
        $expiredAt = date('Y-m-d H:i:s', $var['time'] + 120);

        $insertOtpQuery = $db->prepare('INSERT INTO `otp` (`code`, `expired_at`, `device_id`, `created_at`, `updated_at`) VALUES (:code, :expired_at, :device_id, NOW(), NOW())');
        $insertOtpQuery->bindParam(':code', $code);
        $insertOtpQuery->bindParam(':expired_at', $expiredAt);
        $insertOtpQuery->bindParam(':device_id', $argDeviceId);
        $insertOtpQuery->execute();
        $otpId = $db->lastInsertId();

        $otpQuery = $db->prepare('SELECT `code`, `expired_at` FROM `otp` WHERE `device_id` = :deviceId AND `expired_at` > :time');
        $otpQuery->bindValue(':deviceId', $argDeviceId);
        $otpQuery->bindValue(':time', $var['time']);
        $otpQuery->execute();
    }

    $otpRow = $otpQuery->fetch();
    $result = [
        "code" => $otpRow['code'],
        "expired_at" => (string)strtotime($otpRow['expired_at'])
    ];
    _message2($result);
}

function acceptOtpCode() // DONE
{
    global $db, $var, $user, $conf;
    $argCode = $_POST['code'];

    if (!$user) {
        _message('unauthorized', 'error');
    }
    if (empty($argCode)) {
        _message('empty', 'error');
    }

    deleteExpiredOtpCodes();

    $otpQuery = $db->prepare('SELECT `id`, `users_id` as `uid`, `expired_at` FROM `otp` WHERE `code` = :code AND `expired_at` > NOW()');
    $otpQuery->bindValue(':code', $argCode);
    //$otpQuery->bindValue(':time', $var['time']);
    $otpQuery->execute();
    if ($otpQuery->rowCount() == 0) {
        _message('otpNotFound', 'error');
    }

    $otpRow = $otpQuery->fetch();

    if ($otpRow['uid']) {
        _message('otpAccepted', 'error');
    }

    $updateOtpQuery = $db->prepare('UPDATE `otp` SET `users_id` = :uid WHERE `id` = :id');
    $updateOtpQuery->bindValue(':id', $otpRow['id']);
    $updateOtpQuery->bindParam(':uid', $user['id']);
    $updateOtpQuery->execute();

    _message('success');
}

function loginByOtpCode() // DONE
{
    global $db, $var, $user, $conf;
    $argCode = $_POST['code'];
    $argDeviceId = $_POST['deviceId'];

    if ($user) {
        _message('authorized', 'error');
    }
    if (empty($argCode)) {
        _message('empty', 'error');
    }
    if (empty($argDeviceId)) {
        _message('empty', 'error');
    }

    deleteExpiredOtpCodes();

    $otpQuery = $db->prepare('SELECT `id`, `users_id` as `uid` FROM `otp` WHERE `code` = :code AND `device_id` = :deviceId AND `expired_at` > NOW()');
    $otpQuery->bindValue(':code', $argCode);
    $otpQuery->bindValue(':deviceId', $argDeviceId);
    //$otpQuery->bindValue(':time', $var['time']);
    $otpQuery->execute();
    if ($otpQuery->rowCount() == 0) {
        _message('otpNotFound', 'error');
    }

    $otpRow = $otpQuery->fetch();
    if (!$otpRow['uid']) {
        _message('otpNotAccepted', 'error');
    }

    $userQuery = $db->prepare('SELECT `id`, `login`, `password` AS `passwd`, 1 AS `access` FROM `users` WHERE `id` = :uid');
    $userQuery->bindValue(':uid', $otpRow['uid']);
    $userQuery->execute();
    if ($userQuery->rowCount() == 0) {
        _message('invalidUser', 'error');
    }

    $userRow = $userQuery->fetch();

    $otpDeleteQuery = $db->prepare('DELETE FROM `otp` WHERE `id` = :id');
    $otpDeleteQuery->bindParam(':id', $otpRow['id']);
    $otpDeleteQuery->execute();

    startSession($userRow);
    _message('success');
}

/* OTP Auth end */

function login() // DONE
{
    global $db, $var, $user;

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');

    if ($user) {
        _message('authorized', 'error');
    }
    if (empty($_POST['mail']) || empty($_POST['passwd'])) {
        _message('empty', 'error');
    }
    if (strlen($var['user_agent']) > 256) {
        _message('wrongUserAgent', 'error');
    }
    $_POST['mail'] = mb_strtolower($_POST['mail']);
    $query = $db->prepare('SELECT `id`, `login`, `password` AS `passwd`, NULL AS `2fa`, 1 AS `access` FROM `users` WHERE `email` = :mail');
    $query->bindValue(':mail', $_POST['mail']);
    $query->execute();
    if ($query->rowCount() == 0) {

        $query = $db->prepare('SELECT `id`, `login`, `password` AS `passwd`, NULL AS `2fa`, 1 AS `access` FROM `users` WHERE `login` = :login');
        $query->bindValue(':login', $_POST['mail']);
        $query->execute();
        if ($query->rowCount() == 0) {
            _message('invalidUser', 'error');
        }
    }
    $row = $query->fetch();


    /*if(!empty($row['2fa'])){
        if(empty($_POST['fa2code'])){
            _message('empty', 'error');
        }
        if(oathHotp($row['2fa'], floor(microtime(true) / 30)) != $_POST['fa2code']){
            _message('wrong2FA', 'error');
        }
    }*/

    if (!password_verify($_POST['passwd'], $row['passwd'])) {
        _message('wrongPasswd', 'error');
    }

    if (password_needs_rehash($row['passwd'], PASSWORD_ARGON2ID, ['memory_cost' => 1 << 14, 'time_cost' => 3, 'threads' => 2])) {
        $passwd = createPasswd($_POST['passwd']);
        $query = $db->prepare('UPDATE `users` SET `password` = :passwd WHERE `id` = :id');
        $query->bindParam(':passwd', $passwd[1]);
        $query->bindParam(':id', $row['id']);
        $query->execute();
        $row['passwd'] = $passwd[1];
    }

    enableCSRF();

    $hash = startSession($row);

    _message('success', 'ok', ['sessionId' => $hash]);
}

/*function startSession($row){
	global $db, $var;
	$hash = session_hash($row['login'], $row['passwd'], $row['access']);
	$query = $db->prepare('INSERT INTO `session` (`uid`, `hash`, `time`, `ip`, `info`) VALUES (:uid, :hash, :time, INET6_ATON(:ip), :info)');
	$query->bindParam(':uid', $row['id']);
	$query->bindParam(':hash', $hash[0]);
	$query->bindParam(':time', $hash[1]);
	$query->bindParam(':ip', $var['ip']);
	$query->bindParam(':info', $var['user_agent']);
	$query->execute();
	$sid = $db->lastInsertId();
	$_SESSION['sess'] = $hash[0];
	$query = $db->prepare('UPDATE `users` SET `last_activity` = :time WHERE `id` = :id');
	$query->bindParam(':time', $var['time']);
	$query->bindParam(':id', $row['id']);
	$query->execute();
	$query = $db->prepare('INSERT INTO `log_ip` (`uid`, `sid`, `ip`, `time`, `info`) VALUES (:uid, :sid, INET6_ATON(:ip), :time, :info)');
	$query->bindParam(':uid', $row['id']);
	$query->bindParam(':sid', $sid);
	$query->bindParam(':ip', $var['ip']);
	$query->bindParam(':time', $var['time']);
	$query->bindParam(':info', $var['user_agent']);
	$query->execute();
}*/

function startSession($row) // DONE
{
    global $db, $var;

    $hash = genRandStr(32, 1); // session_hash($row['login'], $row['passwd'], $row['access'])[0];
    $datetime = date('Y-m-d H:i:s');

    $query = $db->prepare('INSERT INTO `users_sessions` (users_id, id, created_at, updated_at, ip_address, user_agent) VALUES (:uid, :hash, :time, :time, :ip, :info)');
    $query->bindParam(':uid', $row['id']);
    $query->bindParam(':hash', $hash);
    $query->bindParam(':time', $datetime);
    $query->bindParam(':ip', $var['ip']);
    $query->bindParam(':info', $var['user_agent']);
    //$query->bindParam(':phpsessid', session_id());

    $query->execute();

    //$sid = $db->lastInsertId();
    //$_SESSION['sess'] = $hash;

    /*
    $query = $db->prepare('UPDATE users SET last_activity = :time WHERE id = :id');
    $query->bindParam(':time', $var['time']);
    $query->bindParam(':id', $row['id']);
    $query->execute();*/

    /*
    $query = $db->prepare('INSERT INTO log_ip (uid, sid, ip, time, info) VALUES (:uid, :sid, INET6_ATON(:ip), :time, :info)');
    $query->bindParam(':uid', $row['id']);
    $query->bindParam(':sid', $sid);
    $query->bindParam(':ip', $var['ip']);
    $query->bindParam(':time', $var['time']);
    $query->bindParam(':info', $var['user_agent']);
    $query->execute();*/

    header("Set-Cookie: PHPSESSID=" . $hash . "; expires=" . gmdate("D, d M Y H:i:s", time() + 60 * 60 * 24 * 30 * 2) . "; path=/;", false);
    header("Set-Cookie: PHPSESSID=" . $hash . "; expires=" . gmdate("D, d M Y H:i:s", time() + 60 * 60 * 24 * 30 * 2) . "; path=/; domain=.anilibria.tv;", false);

    return $hash;
}

function moveErrPage($page = 403) // DONE
{
    die(header("Location: /pages/error/$page.php"));
}

function password_link() // DONE
{
    global $conf, $db, $var;

    if (empty($_GET['id']) || empty($_GET['time']) || empty($_GET['hash'])) {
        moveErrPage();
    }

    if (!ctype_digit($_GET['id']) || !ctype_digit($_GET['time'])) {
        moveErrPage();
    }

    $query = $db->prepare('SELECT `id`, `email` AS `mail`, `password` AS `passwd` FROM `users` WHERE `id` = :id');
    $query->bindParam(':id', $_GET['id']);
    $query->execute();
    if ($query->rowCount() == 0) {
        moveErrPage();
    }
    $row = $query->fetch();
    $hash = hash($conf['hash_algo'], $_GET['id'] . $_GET['time'] . sha1(half_string_hash($row['passwd'])));
    if ($_GET['hash'] != $hash) {
        moveErrPage();
    }
    if ($var['time'] > $_GET['time']) {
        moveErrPage();
    }
    $passwd = createPasswd();
    $query = $db->prepare('UPDATE `users` SET `password` = :passwd WHERE `id` = :id');
    $query->bindValue(':id', $row['id']);
    $query->bindParam(':passwd', $passwd['1']);
    $query->execute();
    _mail($row['mail'], "Новый пароль", "Ваш пароль: {$passwd['0']}");
    die(header('Location: /'));
}

function testRecaptcha() // DONE
{
    $v = 3;
    if (!empty($_POST['recaptcha']) && $_POST['recaptcha'] == 2) {
        $v = $_POST['recaptcha'];
    }
    $result = recaptcha($v);

    if (!$result['success']) {
        _message('reCaptchaFail', 'error');
    }


    if ($v == 3 && $result['score'] < 0.5) {
        _message('reCaptcha3', 'error');
    }
}

function password_recovery() // DONE
{
    global $conf, $db, $var;
    testRecaptcha();
    if (empty($_POST['mail'])) {
        _message('empty', 'error');
    }
    if (strlen($_POST['mail']) > 254) {
        _message('long', 'error');
    }
    if (!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) {
        _message('wrongEmail', 'error');
    }
    $query = $db->prepare('SELECT `id`, `email` AS `mail`, `password` AS `passwd` FROM `users` WHERE `email` = :mail');
    $query->bindParam(':mail', $_POST['mail']);
    $query->execute();
    if ($query->rowCount() == 0) {
        _message('noUser', 'error');
    }
    $row = $query->fetch();
    $time = $var['time'] + 43200;
    $hash = hash($conf['hash_algo'], $row['id'] . $time . sha1(half_string_hash($row['passwd'])));
    $link = "http://" . $_SERVER['HTTP_HOST'] . "/public/link/password.php?id={$row['id']}&time={$time}&hash={$hash}";
    _mail($row['mail'], "Восстановление пароля", "Запрос отправили с IP {$var['ip']}<br/>Чтобы восстановить пароль <a href='$link'>перейдите по ссылке</a>.<br/>Далее вам придет письмо с паролем на почту.");
    _message('checkEmail');
}

function registration() // DONE
{
    global $db, $user, $var;
    if ($user) {
        _message('registered', 'error');
    }
    if (!empty($_POST['vk'])) {
        $vk = json_decode($_POST['vk'], true);
        if (!is_array($vk) || empty($vk['id']) || empty($vk['time']) || empty($vk['hash'])) {
            _message('wrong', 'error');
        }
        if ($var['time'] > $vk['time'] || !checkSecret($vk['hash'], $vk['id'] . $vk['time'])) {
            _message('wrong', 'error');
        }
        if (getUserVK($vk['id'])) {
            _message('wrong', 'error');
        }
    }
    testRecaptcha();
    if (empty($_POST['login']) || empty($_POST['mail']) || empty($_POST['passwd'])) {
        _message('empty', 'error');
    }
    if (strlen($_POST['login']) > 20) _message('longLogin', 'error');
    if (strlen($_POST['mail']) > 254) _message('longEmail', 'error');

    if (preg_match('/[^0-9A-Za-z]/', $_POST['login'])) {
        _message('wrongLogin', 'error');
    }
    if (!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) {
        _message('wrongEmail', 'error');
    }
    $_POST['mail'] = mb_strtolower($_POST['mail']);
    $query = $db->prepare('SELECT `id` FROM `users` WHERE `login` = :login');
    $query->bindValue(':login', $_POST['login']);
    $query->execute();
    if ($query->rowCount() > 0) {
        _message('registeredLogin', 'error');
    }
    $query = $db->prepare('SELECT `id` FROM `users` WHERE `email`= :mail');
    $query->bindParam(':mail', $_POST['mail']);
    $query->execute();
    if ($query->rowCount() > 0) {
        _message('registeredEmail', 'error');
    }
    $passwd = createPasswd($_POST['passwd']);
    $query = $db->prepare('INSERT INTO `users` (`login`, `email`, `password`, `created_at`, `updated_at`, `torrents_passkey`) VALUES (:login, :mail, :passwd, NOW(), NOW(), LPAD(LEFT(REPLACE(REPLACE(REPLACE(TO_BASE64(UNHEX(MD5(:login))), "/", ""), "+", ""), "=", ""), 16), 16, 0))');
    $query->bindValue(':login', $_POST['login']);
    $query->bindParam(':mail', $_POST['mail']);
    $query->bindParam(':passwd', $passwd['1']);
    // $query->bindParam(':time', $var['time']);
    $query->execute();
    if (!empty($_POST['vk'])) {
        $id = $db->lastInsertId();
        $query = $db->prepare('UPDATE `users` SET `vk_id` = :vk WHERE `id` = :id');
        $query->bindParam(':vk', $vk['id']);
        $query->bindParam(':id', $id);
        $query->execute();
        $row = getUserVK($vk['id']);
        if ($row) {
            startSession($row);
        }
    }
    _mail($_POST['mail'], "Регистрация", "Вы успешно зарегистрировались на сайте https://www.anilibria.tv");
    _message('success');
}

function auth() // DONE
{
    global $conf, $db, $var, $user;
    /*if(random_int(1, 1000) == 1){
        $tmp = time();
        $query = $db->prepare('DELETE FROM `users_sessions` WHERE `time` < :time');
        $query->bindParam(':time', $tmp);
        $query->execute();
    }*/

    $cookieSession = $_COOKIE['PHPSESSID'] ?? null;


    if (empty($cookieSession) === false) {

        $query = $db->prepare('SELECT `id`, `users_id` AS `uid`, `id` AS `hash` FROM `users_sessions` WHERE `id` = :hash');
        $query->bindParam(':hash', $cookieSession);
        //$query->bindParam(':time', $var['time']);
        $query->execute();

        if ($query->rowCount() != 1) {
            _exit();
            return;
        }

        $session = $query->fetch();
        $query = $db->prepare('
            SELECT 
                u.`id`, 
                u.`login`, 
                u.`vk_id` AS `vk`, 
                u.`avatar`,
                u.`password` AS `passwd`,
                u.`email` AS `mail`,
                NULL AS `2fa`, 
                1 AS `access`, 
                UNIX_TIMESTAMP(u.`created_at`) AS `register_date`, 
                NULL AS `last_activity`,
                NULL AS `user_values`, 
                u.`show_ads` AS `ads`,
                IF(COUNT(ur.`roles_id`) > 0, 1, IF(u.`is_admin` = 1, 1, 0)) AS `has_roles`
    
            FROM `users` as u
            left join `users_roles` AS ur ON ur.users_id = u.id
            WHERE u.`id` = :id
            GROUP BY u.id
        ');
        $query->bindParam(':id', $session['uid']);

        $query->execute();

        if ($query->rowCount() != 1) {
            _exit();
            return;
        }

        $row = $query->fetch();
        /*if ($_SESSION['sess'] != session_hash($row['login'], $row['passwd'], $row['access'], substr($session['hash'], 0, 8))['0']) {
            _exit();
            return;
        }*/

        // if (random_int(1, 10) == 1) {
        $tmp = $var['time'] + 60 * 60 * 24 * 30;
        $query = $db->prepare('UPDATE `users_sessions` set `updated_at` = NOW() WHERE `id` = :id');
        // $query->bindParam(':time', $tmp);
        $query->bindParam(':id', $session['id']);
        $query->execute();
        // }


        $user = [
            'id' => $row['id'],
            'login' => $row['login'],
            'vk' => $row['vk'],
            'avatar' => $row['avatar'],
            'passwd' => $row['passwd'],
            'mail' => $row['mail'],
            '2fa' => $row['2fa'],
            'access' => $row['access'],
            'register_date' => $row['register_date'],
            'last_activity' => $row['last_activity'],
            'dir' => substr(md5($row['id']), 0, 2),
            'ads' => $row['ads'],
            'downloaded' => 0,
            'uploaded' => 0,
            'has_roles' => (int)$row['has_roles'] === 1
        ];
        $user['user_values'] = [];
        if (!empty($row['user_values'])) {
            $user['user_values'] = json_decode($row['user_values'], true);
        }
        $query = $db->prepare('SELECT `torrents_downloaded` AS `downloaded`, `torrents_uploaded` AS `uploaded` FROM `users` WHERE `id` = :id');
        $query->bindParam(':id', $user['id']);
        $query->execute();
        if ($query->rowCount() == 1) {
            $row = $query->fetch();
            $user['uploaded'] = sprintf('%s%s', $row['uploaded'] >= 0 ? '' : '-', formatBytes(abs($row['uploaded'])));
            $user['downloaded'] = sprintf('%s%s', $row['downloaded'] >= 0 ? '' : '-', formatBytes(abs($row['downloaded'])));
        }
    }
}

function seedersRating() // DONE
{
    global $db;
    $result = '';
    $i = 1;
    $query = $db->query('SELECT `torrents_downloaded` AS `downloaded`, `torrents_uploaded` AS `uploaded`, `id` AS `torrent_pass_version` FROM `users` ORDER BY `torrents_uploaded` DESC LIMIT 50');
    while ($row = $query->fetch()) {
        $select = $db->prepare('SELECT `login` FROM `users` WHERE `id` = :id');
        $select->bindParam(':id', $row['torrent_pass_version']);
        $select->execute();
        if ($select->rowCount() != 1) {
            continue;
        }
        $login = $select->fetch()['login'];
        $download = formatBytes($row['downloaded']);
        $upload = formatBytes($row['uploaded']);
        $result .= "<tr><td>$i</td><td>$login</td><td>$upload</td><td>$download</td></tr>";
        $i++;
    }
    return $result;
}

function base32_map($i, $do = 'encode') // DONE
{
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    if ($do == 'encode') {
        return $chars[$i];
    } else {
        return array_search($i, str_split($chars));
    }
}

function base32_bits($v) // DONE
{
    $value = ord($v);
    return vsprintf(str_repeat('%08b', 1), $value);
}

function base32_encode($data) // DONE
{
    $result = '';
    $s = 0;
    $j = [4 => 1, 3 => 3, 2 => 4, 1 => 6];
    $arr = explode('|', substr(chunk_split($data, 5, '|'), 0, -1));
    foreach ($arr as $val) {
        $s++;
        $arr2 = str_split($val);
        $x = ['00000000', '00000000', '00000000', '00000000', '00000000'];
        foreach ($arr2 as $key => $val2) {
            $x[$key] = base32_bits($val2);
        }
        $arr3 = explode('|', substr(chunk_split(implode('', $x), 5, '|'), 0, -1));
        foreach ($arr3 as $key => $val3) {
            $result .= base32_map(bindec($val3));
        }
        if ($s == count($arr) && isset($j[strlen($val)])) {
            $result = str_pad(substr($result, 0, -$j[strlen($val)]), 8 * $s, '=', STR_PAD_RIGHT);
        }
    }
    return $result;
}

function base32_decode($data) // DONE
{ // thx Sanasol
    $x = '';
    $arr = str_split($data);
    foreach ($arr as $val) {
        $x .= str_pad(decbin(base32_map($val, 'decode')), 5, '0', STR_PAD_LEFT);
    }
    $chunks = str_split($x, 8);
    $string = array_map(function ($chr) {
        return chr(bindec($chr));
    }, $chunks);
    return implode("", $string);
}

function generate_secret() // DONE
{
    return base32_encode(genRandStr());
}

function oathTruncate($hash) // DONE
{
    $offset = ord($hash[19]) & 0xf;
    $temp = unpack('N', substr($hash, $offset, 4));
    return substr($temp[1] & 0x7fffffff, -6);
}

function oathHotp($secret, $time) // DONE
{
    $secret = base32_decode($secret);
    $time = pack('N*', 0, $time);
    $hash = hash_hmac('sha1', $time, $secret, true);
    return str_pad(oathTruncate($hash), 6, '0', STR_PAD_LEFT);
}

function getQRCodeGoogleUrl($name, $secret) // DONE
{
    $urlencoded = urlencode('otpauth://totp/' . $name . '?secret=' . $secret . '');
    return 'https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=' . $urlencoded . '';
}


/*function auth2FA()
{
    global $db, $user;
    if (!$user) {
        _message('unauthorized', 'error');
    }
    if (empty($_POST['do'])) {
        _message('empty', 'error');
    }
    switch ($_POST['do']) {
        default:
            return 'empty';
            break;
        case 'gen':
            if (!empty($user['2fa'])) {
                _message('2FA', 'error');
            }
            $base32_key = generate_secret();
            _message2("<img src=" . getQRCodeGoogleUrl($user['login'] . "@anilibria.tv", $base32_key) . "><br>Secret key: $base32_key<br/>Сохраните секретный ключ в надежном месте.<input type=\"hidden\" id=\"2fa\" value=\"$base32_key\">");
            break;
        case 'save':
            if (empty($_POST['passwd']) || empty($_POST['code'])) {
                _message('empty', 'error');
            }
            if (empty($user['2fa'])) {
                if (empty($_POST['2fa'])) {
                    _message('empty', 'error');
                }
                $check = $_POST['2fa'];
            } else {
                $check = $user['2fa'];
            }
            if (strlen($check) != 16 || !ctype_alnum($check) || ctype_lower($check)) {
                _message('wrong2FA', 'error');
            }
            if (oathHotp($check, floor(microtime(true) / 30)) != $_POST['code']) {
                _message('wrong2FA', 'error');
            }
            if (!password_verify($_POST['passwd'], $user['passwd'])) {
                _message('wrongPasswd', 'error');
            }
            if (!empty($user['2fa'])) {
//				$query = $db->prepare('UPDATE `users` SET `2fa` = :code WHERE `id` = :uid');
//				$query->bindValue(':code', null, PDO::PARAM_INT);
//				$query->bindParam(':uid', $user['id']);
//				$query->execute();
                _message('2FAdisabled');
            } else {
//				$query = $db->prepare('UPDATE `users` SET `2fa` = :code WHERE `id` = :uid');
//				$query->bindParam(':code', $_POST['2fa']);
//				$query->bindParam(':uid', $user['id']);
//				$query->execute();
                _message('2FAenabled');
            }
            break;
    }
}*/


function recaptcha($v = 3) // DONE
{
    global $conf, $var;
    if (empty($_POST['g-recaptcha-response'])) {
        _message('reCaptchaFail', 'error');
    }
    $secret = $v == 3 ? $conf['recaptcha_secret'] : $conf['recaptcha2_secret'];

    $data = ['secret' => $secret, 'response' => $_POST['g-recaptcha-response'], 'remoteip' => $var['ip']];
    $verify = curl_init();
    curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($verify, CURLOPT_POST, true);
    curl_setopt($verify, CURLOPT_POSTFIELDS, $data);
    curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
    $result = json_decode(curl_exec($verify), true);
    curl_close($verify);
    return $result;
}

/*function torrentHashExist($hash)
{
    global $db;
    $query = $db->prepare('SELECT `id` AS `fid` FROM torrents WHERE `hash` = :hash');
    $query->bindParam(':hash', $hash);
    $query->execute();
    if ($query->rowCount() == 0) {
        return false;
    }
    return true;
}*/

/*function torrentExist($id)
{
    global $db;
    $query = $db->prepare('SELECT `id` AS `fid`, `completed`, JSON_ARRAY(CONCAT_WS(\' \', `type`, `quality`, IF(`is_hevc` = 1, \'HEVC\', null)), `description`, `size`) AS `info` FROM `torrents` WHERE `id`= :id');
    $query->bindParam(':id', $id);
    $query->execute();
    if ($query->rowCount() == 0) {
        return false;
    }
    return $query->fetch();
}*/

/*function torrentAdd($hash, $rid, $json, $completed = 0)
{
    global $db, $var;
    $query = $db->prepare('INSERT INTO `xbt_files` (`info_hash`, `mtime`, `ctime`, `flags`, `completed`, `rid`, `info`) VALUES( :hash , :time, :time, 0, :completed, :rid, :info)');
    $query->bindParam(':hash', $hash);
    $query->bindParam(':time', $var['time']);
    $query->bindParam(':rid', $rid);
    $query->bindParam(':completed', $completed);
    $query->bindParam(':info', $json);
    $query->execute();
    return $db->lastInsertId();
}*/

/*function torrentDelete($id)
{
// https://github.com/shakahl/xbt/wiki/XBT-Tracker-(XBTT)
// flags - This field is used to communicate with the tracker. Usable values: 0 - No changes. 1 - Torrent should be deleted. 2 - Torrent was updated.
// flag 1 work		https://github.com/OlafvdSpek/xbt/blob/master/Tracker/server.cpp#L183-L187
// source code		https://img.poiuty.com/img/6e/f01f40eaa783018fe12e5649315b716e.png
// flag 2 not work	https://img.poiuty.com/img/7c/a5479067a6e3a272d66bb92c0416797c.png
// Also I dont find it in source.

    global $db;
    $query = $db->prepare('UPDATE `xbt_files` SET `flags` = 1 WHERE `fid` = :id');
    $query->bindParam(':id', $id);
    $query->execute();
    deleteFile($_SERVER['DOCUMENT_ROOT'].'/upload/torrents/'.$id.'.torrent');
}*/

function isJson($string) // DONE
{
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

/*function torrent()
{
    global $conf, $db, $user, $var;
    function checkTD($key, $val){
        if(empty($val)){
            return false;
        }
        switch($key){
            case 'rid':		if(!ctype_digit($val))	return false;	break;
            case 'fid':		if(!ctype_digit($val))	return false;	break;
            case 'ctime':	if(!strtotime($val))	return false;	break;
            case 'quality': if(strlen($val) > 20)	return false;	break;
            case 'series':	if(strlen($val) > 10)	return false;	break;
        }
        return true;
    }
    if(empty($_POST['data']) || !isJson($_POST['data'])){
        _message('empty');
    }
    if(!$user || $user['access'] < 2){
        _message('access', 'error');
    }
    $data = json_decode($_POST['data'], true);
    foreach($data as $key => $val){
        if(!checkTD('rid', $val['rid']) || !checkTD('quality', $val['quality']) || !checkTD('series', $val['series'])){
            continue;
        }
        if(checkTD('ctime', $val['ctime'])){
            $ctime = strtotime($val['ctime']);
        }else{
            $ctime = $var['time'];
        }
        $ctime = htmlspecialchars($ctime, ENT_QUOTES, 'UTF-8');
        $val['quality'] = htmlspecialchars($val['quality'], ENT_QUOTES, 'UTF-8');
        $val['series'] = htmlspecialchars($val['series'], ENT_QUOTES, 'UTF-8');
        switch($val['do']){
            case 'change':
                if(!checkTD('fid', $val['fid'])){
                    continue 2;
                }
                $old = torrentExist($val['fid']);
                if(!$old){
                    continue 2;
                }
                $tmp = json_decode($old['info'], true);
                $tmp = json_encode([$val['quality'], $val['series'], $tmp['2']]);
                $query = $db->prepare('UPDATE `xbt_files` SET `ctime` = :ctime, `info` = :info WHERE `fid` = :fid');
                $query->bindParam(':ctime', $ctime);
                $query->bindParam(':info', $tmp);
                $query->bindParam(':fid', $val['fid']);
                $query->execute();
                if(!empty($val['delete'])){
                    torrentDelete($val['fid']);
                }
            break;
            case 'add':
                if(empty($_FILES['torrent'])){
                    _message('noUploadFile', 'error');
                }
                if($_FILES['torrent']['error'] != 0){
                    _message('uploadError', 'error');
                }
                if($_FILES['torrent']['type'] != 'application/x-bittorrent'){
                    _message('wrongData', 'error');
                }
                $torrent = new Torrent($_FILES['torrent']['tmp_name']);
                if(empty($torrent->hash_info())){
                    _message('wrongData', 'error');
                }
                $pack_hash = pack('H*', $torrent->hash_info());
                if(torrentHashExist($pack_hash)){
                    _message('exitTorrent', 'error');
                }
                $old = false;
                $size = $torrent->size();
                $json = json_encode([$val['quality'], $val['series'], $size]);
                if(!empty($val['fid'])){
                    $old = torrentExist($val['fid']);
                }
                if($old){
                    torrentDelete($val['fid']);
                    $name = torrentAdd($pack_hash, $val['rid'], $json, $old['completed']);
                }else{
                    $name = torrentAdd($pack_hash, $val['rid'], $json);
                }
                $torrent->announce(false);
                $torrent->announce($conf['torrent_announce']);
                $torrent->save($_SERVER['DOCUMENT_ROOT'].'/upload/torrents/'.$name.'.torrent');
                if(!empty($val['rid'])) {
                    $query = $db->prepare("UPDATE `xrelease` SET `last_change` = UNIX_TIMESTAMP() WHERE `id` = :id");
                    $query->bindParam(':id', $val['rid']);
                    $query->execute();
                    APIv2_UpdateTitle($val['rid']);
                }
                die(json_encode(['err' => 'ok', 'mes' => $var['error']['success'], 'id' => $name, 'size' => formatBytes($size), 'date' => date('d.m.Y', $var['time'])]));
            break;
        }
    }
    if(!empty($val['rid'])) {
        $query = $db->prepare("UPDATE `xrelease` SET `last_change` = UNIX_TIMESTAMP() WHERE `id` = :id");
        $query->bindParam(':id', $val['rid']);
        $query->execute();
        APIv2_UpdateTitle($val['rid']);
    }
    _message('success');
}*/

/*
function downloadTorrent(){
	global $db, $user, $conf;

	if(empty($_GET['id'])){
		_message('empty', 'error');
	}
	if(!ctype_digit($_GET['id'])){
		_message('wrong', 'error');
	}

	if($user){
		$query = $db->prepare('SELECT `info_hash` FROM `xbt_files` WHERE `fid` = :id');
		$query->bindParam(':id', $_GET['id']);
		$query->execute();
		if($query->rowCount() == 0){
			_message('wrong', 'error');
		}
		$info_hash = $query->fetch()['info_hash'];

		$query = $db->prepare('SELECT `uid` FROM `xbt_users` WHERE `torrent_pass_version` = :id');
		$query->bindParam(':id', $user['id']);
		$query->execute();
		if($query->rowCount() == 0){
			$query = $db->prepare('INSERT INTO `xbt_users` (`torrent_pass_version`) VALUES (:id)');
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
	} else {
		$torrent = new Torrent($_SERVER['DOCUMENT_ROOT']."/upload/torrents/{$_GET['id']}.torrent");
		$torrent->send();
	}
}
*/

function downloadTorrent() // DONE
{
    global $db, $user, $conf;

    if (empty($_GET['id'])) {
        _message('empty', 'error', [], 404);
    }

    // Get torrent
    $query = $db->prepare('
        SELECT 
            t.`file`,
            t.`type`,
            r.`name_english`,
            t.`quality`,
            t.`description`,
            t.`is_hevc`
        FROM `torrents` as t
        INNER JOIN `releases` AS r ON r.`id` = t.`release_id` AND r.`is_hidden` = 0 AND r.`deleted_at` IS NULL
        WHERE t.`id` = :id and t.`deleted_at` IS NULL
        GROUP by t.id
    ');

    $query->bindParam(':id', $_GET['id']);
    $query->execute();

    if ($query->rowCount() == 0) _message('wrong', 'error', [], 404);

    $item = $query->fetch();
    $file = $item['file'] ?? null;
    $torrentName = sprintf('%s - AniLibria.TV [%s %s]%s[%s].torrent',
        $item['name_english'],
        $item['type'],
        $item['quality'],
        $item['is_hevc'] === 1 ? "[HEVC]" : '',
        $item['description']
    );

    $torrent = new Torrent(base64_decode($file));
    $torrent->announce(false);

    if (!empty($_GET['session']) || !empty($_GET['psid'])) {

        $properSessionId = $_GET['session'] ?? $_GET['psid'] ?? -1;

        $query = $db->prepare('SELECT `users_id` AS `id` FROM `users_sessions` WHERE `id` = :hash');
        $query->bindParam(':hash', $properSessionId);
        $query->execute();
        if ($query->rowCount() == 0) _message('wrong', 'error', [], 404);

        $userId = $query->fetch();
        $userId = $userId['id'] ?? null;
    }

    if ($user || $userId) {

        $properUserId = $userId ?? $user['id'] ?? -1;

        // Get torrent passkey
        $query = $db->prepare('SELECT `id` AS `uid`, `torrents_passkey` FROM `users` WHERE `id` = :id');
        $query->bindParam(':id', $properUserId);
        $query->execute();
        $user = $query->fetch();
        $torrentsPasskey = $user['torrents_passkey'] ?? null;
    }

    // Create main and personal trackers
    // Check if user is authorized -> set its passkey to torrent tracker
    $trackerMain = sprintf('%s/announce', $conf['torrent_announce']);
    $trackerPersonal = isset($torrentsPasskey) ? sprintf('%s/announce?pk=%s', $conf['torrent_announce'], $torrentsPasskey) : null;

    $torrent->announce([$trackerPersonal ?? $trackerMain]);
    $torrent->send($torrentName);

}

function upload_avatar() // DONE
{
    global $db, $user, $conf;

    if (!$user) {
        _message('unauthorized', 'error');
    }

    if (empty($_FILES['avatar'])) {
        _message('noUploadFile', 'error');
    }

    if ($_FILES['avatar']['error'] != 0) {
        _message('uploadError', 'error');
    }

    if (!in_array(exif_imagetype($_FILES['avatar']['tmp_name']), [IMAGETYPE_PNG, IMAGETYPE_JPEG])) {
        _message('wrongType', 'error');
    }
    if ($_FILES['avatar']['size'] > 1048576) { // limit 1MB
        _message('maxSize', 'error');
    }

    $img = new Imagick($_FILES['avatar']['tmp_name']);
    $img->setImageFormat('jpg');

    $crop = true;
    $arr = ['w', 'h', 'x1', 'y1', 'width', 'height'];
    foreach ($arr as $v) {
        if (empty($_POST["$v"]) && $_POST["$v"] != 0)
            $crop = false;

        if (!ctype_digit($_POST["$v"]))
            $crop = false;

        if ($crop == false)
            break;
    }
    $img->resizeImage($_POST['width'], $_POST['height'], Imagick::FILTER_LANCZOS, 1, false);
    if ($crop) {
        $img->cropImage($_POST['w'], $_POST['h'], $_POST['x1'], $_POST['y1']);
    }
    $img->resizeImage(160, 160, Imagick::FILTER_LANCZOS, 1, false);
    $img->setImageCompression(Imagick::COMPRESSION_JPEG);
    $img->setImageCompressionQuality(85);
    $img->stripImage();

    /*$name = hash('crc32', $img);
    $tmp = $dir = '/upload/avatars/' . $user['dir'];

    $dir = $_SERVER['DOCUMENT_ROOT'] . $dir;
    $file = "$dir/$name.jpg";*/

    $dir = sprintf('/var/www/%s/%s/%s', ltrim($conf['users_avatars_host'], '/'), floor($user['id'] / 100), $user['id']);
    $name = genRandStr(10, 1);
    $filename = $name . '.jpg';

    $filepath = sprintf('%s/%s', $dir, $filename);

    if (!file_exists($filepath)) mkdir($dir, 0755, true);

    file_put_contents($filepath, $img);

    // if (!empty($user['avatar']) && $user['avatar'] != $filename) deleteFile($filepath);

    $query = $db->prepare('UPDATE `users` SET `avatar` = :avatar WHERE `id` = :id');
    $query->bindParam(':avatar', $filename);
    $query->bindParam(':id', $user['id']);
    $query->execute();

    _message2(sprintf('%s/%s/%s/%s', $conf['users_avatars_host'], floor($user['id'] / 100), $user['id'], $filename));
}

function getTemplate($template) // DONE
{
    $file = $_SERVER['DOCUMENT_ROOT'] . "/private/template/$template.html";
    if (!file_exists($file)) {
        return ['err' => true, 'mes' => 'Template not exists'];
    }
    return file_get_contents($file);
}


/*function saveUserValues()
{
    global $db, $user, $var;
    $arr = [];
    if (!$user) {
        _message('authorized', 'error');
    }
    if (empty($_POST)) {
        _message('empty', 'error');
    }
    if (count($_POST) > 20) {
        _message('maxarg', 'error');
    }
    if (!empty($_POST['reset'])) {
        $query = $db->prepare('UPDATE `users` SET `user_values` = :user_values WHERE `id` = :id');
        $query->bindParam(':user_values', $var['default_user_values']);
        $query->bindParam(':id', $user['id']);
        $query->execute();
        _message2('Data saved');
    }
    foreach ($_POST as $key => $val) {
        if (empty($val) || !array_key_exists($key, $var['user_values'])) {
            continue;
        }
        if (mb_strlen($val) > 30) {
            _message('long', 'error');
        }
        $arr[$key] = htmlspecialchars(trim($val), ENT_QUOTES, 'UTF-8');
    }
    if (!empty($arr['sex']) && (!ctype_digit($arr['sex']) || ($arr['sex'] < 0 || $arr['sex'] > 2))) {
        _message('wrongData', 'error');
    }
    if (!empty($arr['age']) && ctype_digit($arr['age']) && $arr['age'] < date('Y', $var['time'])) {
        $year = date('Y', $var['time']) - $arr['age'];
        $time = strtotime("01-01-$year");
        if (!$time) {
            _message('wrongData', 'error');
        }
        $arr['age'] = $time;
    } else {
        unset($arr['age']);
    }
    foreach (json_decode($var['default_user_values'], true) as $k => $v) {
        if (empty($arr[$k]) && empty($user['user_values']["$k"])) {
            $user['user_values']["$k"] = '';
        }
        if (!empty($arr[$k])) {
            $user['user_values'][$k] = $arr[$k];
        }
    }
    $json = json_encode($user['user_values']);
    if (strlen($json) > 1024) {
        _message('long', 'error');
    }
    $query = $db->prepare('UPDATE `users` SET `user_values` = :user_values WHERE `id` = :id');
    $query->bindParam(':user_values', $json);
    $query->bindParam(':id', $user['id']);
    $query->execute();
    _message2('Data saved');
}*/

/*function cryptAES($text, $key, $do = 'encrypt')
{
    $key = hash('sha256', $key, true);
    $iv_size = openssl_cipher_iv_length($cipher = 'AES-256-CBC');
    $iv = random_bytes($iv_size);
    if ($do == 'encrypt') {
        $ciphertext_raw = openssl_encrypt($text, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha512', $ciphertext_raw, $key, true);
        $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);
        return $ciphertext;
    } else {
        $c = base64_decode($text);
        $iv_dec = substr($c, 0, $iv_size);
        $hmac = substr($c, $iv_size, $sha2len = 64);
        $ciphertext_raw = substr($c, $iv_size + $sha2len);
        $original = openssl_decrypt($ciphertext_raw, $cipher, $key, OPENSSL_RAW_DATA, $iv_dec);
        $calcmac = hash_hmac('sha512', $ciphertext_raw, $key, true);
        if (hash_equals($hmac, $calcmac)) {
            return $original;
        }
    }
}*/

function change_vk() // DONE
{
    global $db, $user, $var, $conf;
    if (!$user) {
        _message('unauthorized', 'error');
    }
    if (!empty($_POST['vk']) && !ctype_digit($_POST['vk'])) {
        _message('wrong', 'error');
    }
    if ($_POST['vk'] == $user['vk']) {
        _message('same', 'error');
    }
    if (getUserVK($_POST['vk'])) {
        _message('used', 'error');
    }
    $query = $db->prepare('UPDATE `users` SET `vk_id` = :vk WHERE `id` = :id');
    if (empty($_POST['vk'])) {
        $query->bindValue(':vk', null, PDO::PARAM_INT);
    } else {
        $query->bindParam(':vk', $_POST['vk']);
    }

    $query->bindParam(':id', $user['id']);
    $query->execute();

    _message('success');
}

function change_mail() // DONE
{
    global $db, $user, $var, $conf;
    if (!$user) {
        _message('unauthorized', 'error');
    }
    if (empty($_POST['mail']) || empty($_POST['passwd'])) {
        _message('empty', 'error');
    }
    if (!password_verify($_POST['passwd'], $user['passwd'])) {
        _message('wrongPasswd', 'error');
    }
    if (!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) {
        _message('wrongEmail', 'error');
    }
    if ($_POST['mail'] == $user['mail']) {
        _message('same', 'error');
    }
    $_POST['mail'] = mb_strtolower($_POST['mail']);
    $query = $db->prepare('SELECT `id` FROM `users` WHERE `email` = :mail');
    $query->bindParam(':mail', $_POST['mail']);
    $query->execute();
    if ($query->rowCount() > 0) {
        _message('used', 'error');
    }
    $time = $var['time'] + 43200;
    $hash = hash($conf['hash_algo'], $var['ip'] . $user['id'] . $user['mail'] . $_POST['mail'] . $time . sha1(half_string_hash($user['passwd'])));
    $link = "http://" . $_SERVER['HTTP_HOST'] . "/public/link/mail.php?time=$time&mail=" . urlencode($_POST['mail']) . "&hash=$hash";
    _mail($user['mail'], "Изменение email", "Запрос отправили с IP {$var['ip']}<br/>Если вы хотите изменить email на {$_POST['mail']} - <a href='$link'>перейдите по ссылке</a>.");
    _message('checkEmail');
}

function mail_link() // DONE
{
    global $db, $user, $var, $conf;
    if (!$user) {
        moveErrPage();
    }
    if (empty($_GET['time']) || empty($_GET['mail']) || empty($_GET['hash'])) {
        moveErrPage();
    }
    if ($var['time'] > $_GET['time']) {
        moveErrPage();
    }
    $_GET['mail'] = urldecode($_GET['mail']);
    if (!filter_var($_GET['mail'], FILTER_VALIDATE_EMAIL)) {
        moveErrPage();
    }
    $hash = hash($conf['hash_algo'], $var['ip'] . $user['id'] . $user['mail'] . $_GET['mail'] . $_GET['time'] . sha1(half_string_hash($user['passwd'])));
    if ($hash != $_GET['hash']) {
        moveErrPage();
    }
    $query = $db->prepare('SELECT `id` FROM `users` WHERE `email` = :mail');
    $query->bindParam(':mail', $_GET['mail']);
    $query->execute();
    if ($query->rowCount() > 0) {
        moveErrPage();
    }
    $query = $db->prepare('UPDATE `users` SET `email` = :mail WHERE `id` = :id');
    $query->bindParam(':mail', $_GET['mail']);
    $query->bindParam(':id', $user['id']);
    $query->execute();
    die(header('Location: /'));
}

function change_passwd() // DONE
{
    global $db, $user, $var;

    if (!$user) {
        _message('unauthorized', 'error');
    }

    if (empty($_POST['oldPasswd']) || empty($_POST['newPasswd']) || empty($_POST['repPasswd'])) {
        _message('empty', 'error');
    }

    if (strlen($_POST['newPasswd']) < 7) {
        _message('short', 'error');
    }

    if ($_POST['oldPasswd'] == $_POST['newPasswd']) {
        _message('samePasswd', 'error');
    }

    if ($_POST['newPasswd'] != $_POST['repPasswd']) {
        _message('wrongNewPasswd', 'error');
    }

    if (!password_verify($_POST['oldPasswd'], $user['passwd'])) {
        _message('wrongPasswd', 'error');
    }

    $passwd = createPasswd($_POST['newPasswd']);

    $query = $db->prepare('UPDATE `users` SET `password` = :passwd WHERE `id` = :id');
    $query->bindParam(':passwd', $passwd['1']);
    $query->bindParam(':id', $user['id']);
    $query->execute();

    _mail($user['mail'], "Изменение пароля", "Здравствуйте, {$user['login']}!<br/><br/>Пароль от вашего аккаунта на сайте https://www.anilibria.tv был изменен.<br/><br/>Запрос отправили с IP {$var['ip']}");
    _message('success');
}

function pageStat() // DONE
{
    global $conf;
    return "Page generated in " . round((microtime(true) - $conf['start']), 4) . " seconds. Peak memory usage: " . round(memory_get_peak_usage() / 1048576, 2) . " MB";
}

function close_sess() // DONE
{
    global $db, $user, $conf;

    if (!$user) _message('unauthorized', 'error');
    if (empty($_POST['id'])) _message('wrong', 'error');

    $query = $db->prepare('DELETE FROM `users_sessions` WHERE `id` = :hash AND `users_id` = :uid');
    $query->bindParam(':hash', $_POST['id']);
    $query->bindParam(':uid', $user['id']);
    $query->execute();

    _message2('Success');
}

function formatBytes($size, $precision = 2) // DONE
{
    if (empty($size)) {
        return 0;
    }
    $base = log($size, 1024);
    $suffixes = ['', 'KB', 'MB', 'GB', 'TB', 'PB'];
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}

function parse_code_bb($text) // DONE
{
    $text = str_replace('<br>', '[br]', $text);
    $find = [
        '~\<b\>(.*?)\</b\>~s',
        '~\<i\>(.*?)\</i\>~s',
        '~\<u\>(.*?)\</u\>~s',
        '~\<s\>(.*?)\</s\>~s',
        '~\<a href=\"((?:http|https?)://.*?)\" target=\"\_blank\"\>(.*?)\</a\>~s',
    ];
    $replace = [
        '[b]$1[/b]',
        '[i]$1[/i]',
        '[u]$1[/u]',
        '[s]$1[/s]',
        '[url=$1]$2[/url]',
    ];
    return preg_replace($find, $replace, $text);
}

function isBlock($str) // DONE
{
    global $var;
    if (strpos($str, $var['country']) !== false) {
        return true;
    }
    return false;
}

function adsUrl() // DONE
{
    $arr['cli'] = ['id' => 'vast6979'];
    $arr['rey'] = ['id' => 'vast2585'];
    $arr['re2'] = ['id' => 'vast6088'];
    $arr['wed'] = ['id' => 'vast8417'];
    function prepareAdsUrl($arr)
    {
        foreach ($arr as $key => $val) {
            $result[] = $val['id'];
        }
        return implode(",", $result);
    }

    if (!checkADS()) {
        return prepareAdsUrl($arr);
    }
}

function release404() // DONE
{
    global $var;
    $var['title'] = '404';
    header('HTTP/1.0 404 Not Found');
    return str_replace('{error}', '<center><img src="/img/404.png"></center>', getTemplate('error'));
}

function lowerMove() // DONE
{
    if (preg_match('/[[:upper:]]/', $_SERVER['REQUEST_URI'])) {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . mb_strtolower($_SERVER['REQUEST_URI']));
        die;
    }
}

function showRelease() // DONE
{
    global $db, $user, $var, $conf;
    $status = ['0' => 'В работе', '1' => 'Завершен'];
    if (empty($_GET['code'])) {
        return release404();
    }

    $release = _getReleaseByColumn('alias', $_GET['code']);

    if ($release === null) {
        return release404();
    }

    $franchises = _getReleaseFranchises($release['id']);

    lowerMove();

    $var['release']['block'] = false;

    if (!$user || $user['access'] == 1) {
        $var['release']['block'] = isBlock($release['block']);
    }

    //if ((!$user || $user['access'] == 1) && $release['status'] == 3) {
    //    return release404();
    //}


    $var['title'] = "{$release['name']} / {$release['ename']}";
    $var['release']['id'] = $release['id'];
    $var['release']['name'] = $release['ename'];
    $var['release']['runame'] = $release['name'];

    $shortDesc = mb_substr($release['description'], 0, 250) . '...';

    $var['og'] .= "<meta property='og:title' content='{$release['name']} / {$release['ename']}' />";
    $var['og'] .= "<meta property='og:description' content='$shortDesc' />";
    $var['og'] .= "<meta property='og:url' content='/release/{$release['code']}.html' />";
    $var['description'] = $shortDesc;

    if (mb_strlen($release['name'] . $release['ename']) > 60) {
        $name = "{$release['name']}<br/>{$release['ename']}";
    } else {
        $name = "{$release['name']} / {$release['ename']}";
    }

    $moon = '';
    if (!empty($release['moonplayer'])) {
        $moon = str_replace('{moon}', $release['moonplayer'], getTemplate('moon'));
    }

    if (!$var['release']['block'] && $release['bakanim'] == 0 || $user && $user['access'] >= 2) {
        $page = str_replace('{name}', $release['name'], getTemplate('release'));
    } elseif (!$var['release']['block'] && $release['bakanim'] == 1) {
        $page = str_replace('{name}', $release['name'], getTemplate('bakanim'));
    } else {
        return getTemplate('block');
    }

    $page = str_replace('{ename}', $release['ename'], $page);
    $page = str_replace('{aname}', $release['aname'], $page);
    $page = str_replace('{franchises}', _getReleasesFranchisesAsHtml($franchises ?? [], $release['id']), $page);
    $page = str_replace('{fullname}', $name, $page);
    $page = str_replace('{alt}', "{$release['name']} / {$release['ename']}", $page);
    $page = str_replace('{block}', $release['block'], $page);
    $page = str_replace('{bakanim}', $release['bakanim'], $page);
    $page = str_replace('{hasMoonPlayer}', !empty($release['moonplayer']) ? '' : 'display: none', $page);
    $page = str_replace('{hasEpisodesVisibility}', $release['has_episodes'] ? '' : 'display: none', $page);
    $page = str_replace('{hasRutubeEpisodesVisibility}', $release['has_rutube_episodes'] ? '' : 'display: none', $page);

    $xtmp = explode(',', $release['genre']);
    $str = '';
    foreach ($xtmp as $key => $val) {
        $val = trim($val);
        $str .= "\"$val\",";
    }
    $str = rtrim($str, ',');

    $release['other'] = explode(',', "{$release['translator']},{$release['editing']},{$release['decor']}");
    foreach ($release['other'] as $v) {
        $other[] = trim($v);
    }
    if (!empty($other)) {
        $other = array_filter($other, 'strlen');
        $release['other'] = implode(', ', $other);
    } else {
        $release['other'] = '';
    }

    $release['edityear'] = $release['year'];

    if (!empty($release['year']) && !empty($release['season'])) {

        $xtmp = implode(' ', [$release['year'], $release['season']]);

        if (in_array($release['season'], $var['season'])) {

            $tmpLink = $release['year'] . array_search($release['season'], $var['season']);
            $xtmp = "<a href='/season/$tmpLink.html' class=\"release-season\">$xtmp</a>";
        }
        $release['year'] = $xtmp;
    }

    $page = str_replace('{chosen-genre}', $str, $page);
    $page = str_replace('{genre}', $release['genre'], $page);
    $page = str_replace('{chosen}', getGenreList(), $page);
    $page = str_replace('{releaseid}', $release['id'], $page);
    $page = str_replace('{voice}', $release['voice'], $page);
    $page = str_replace('{year}', $release['year'], $page);
    $page = str_replace('{edityear}', $release['edityear'], $page);
    $page = str_replace('{type}', $release['type'], $page);
    if ($release['other']) {
        $page = str_replace('{other}', "<b><a href=\"#\" data-show-other class=\"release-subseditors\">Работа над субтитрами</a>:</b> " . $release['other'] . "<br>", $page);
    } elseif (!$release['other'] && $user['access'] > 1) {
        $page = str_replace('{other}', "<b>ID Релиза:</b> " . $release['id'] . "<br>", $page);
    } else {
        $page = str_replace('{other}', "", $page);
    }
    $page = str_replace('{translator}', $release['translator'], $page);
    $page = str_replace('{editing}', $release['editing'], $page);
    $page = str_replace('{decor}', $release['decor'], $page);
    if ($release['timing']) {
        $page = str_replace('{timing}', $release['timing'], $page);
        $page = str_replace('{timingTitle}', "<b>Тайминг:</b> ", $page);
    } else {
        $page = str_replace('{timing}', "", $page);
        $page = str_replace('{timingTitle}', "", $page);
    }

    $page = str_replace('{description}', $release['description'], $page);

    //$poster = $_SERVER['DOCUMENT_ROOT'].'/upload/release/350x500/'.$release['id'].'.jpg';

    $tmpImg = $release['poster'];

    $page = str_replace('{img}', $tmpImg, $page);
    $var['og'] .= "<meta property='og:image' content='$tmpImg' />";

    $page = str_replace('{tg_bot_follow}', getTelegramActionLink('web', 'add', $release['id']), $page);

    if ($release['status'] == '2') {
        $page = str_replace('{style}', 'style="display: none;"', $page);
        $page = str_replace('{announce}', 'Релиз завершен', $page);
    } elseif (!empty($release['announce'])) {
        $page = str_replace('{announce}', $release['announce'], $page);
    } else {
        $a = $var['announce']['1'];
        if (array_key_exists($release['day'], $var['announce'])) {
            $a = $var['announce'][$release['day']];
        }
        $page = str_replace('{announce}', $a, $page);
        unset($a);
    }
    $page = str_replace('{style}', '', $page);

    $page = str_replace('{xdescription}', parse_code_bb($release['description']), $page);

    if ($user && $user['access'] > 1 && $release['bakanim'] == 1) {
        //{TeamBlockMsg}
        $TeamBlockMsg = "<div style='width:100%; height: auto; margin-bottom: 10px; padding: 15px; color:#fff; background-color:#383838; text-align: center;'><span>РЕЛИЗ ЗАБЛОКИРОВАН WAKANIM SAS</span></div>";
        $page = str_replace('{TeamBlockMsg}', $TeamBlockMsg, $page);
    } else {
        $page = str_replace('{TeamBlockMsg}', "", $page);
    }

    $button = '';

    if (!$user) {
        $page = str_replace('{userAccess}', '0', $page);
    }
    if ($user) {
        $button .= '<button data-release-favorites {favorites}>Избранное</button>';
        if (isFavorite($user['id'], $release['id'])) {
            $button = str_replace('{favorites}', 'class="favorites"', $button);
        }
        if ($user['access'] > 1) {
            $button .= '<button data-torrent-edit class="">Торрент</button>';
            $page = str_replace('{userAccess}', '1', $page);
        }
        if ($user['access'] > 2) {
            $button .= '<button data-xrelease-edit class="">Редактировать</button>';
            $button .= '<a href="/pages/new.php"><button class="">Создать</button></a>';
        }
    }
    if (!$user || $user['access'] == 1) {
        $button .= '<button data-release-error>Сообщить об ошибке</button>';
    }
    $page = str_replace('{button}', $button, $page);
    $page = str_replace('{id}', $release['id'], $page);
    $page = str_replace('{moon}', $moon, $page);
    $page = str_replace('{xmoon}', $release['moonplayer'], $page);
    $page = str_replace('{favorites}', '', $page);

    $query = $db->prepare('
        SELECT 
           t.`id` AS `fid`,
           JSON_ARRAY(CONCAT_WS(\' \', t.`type`, t.`quality`, IF(t.`is_hevc` = 1, \'HEVC\', null)),t.`description`, t.`size`) AS `info`,
           UNIX_TIMESTAMP(t.`updated_at`) AS `ctime`,
           t.`seeders`,
           t.`leechers`, 
           t.`completed_times` as `completed` 
        FROM `torrents` as t
        INNER JOIN `releases` AS r ON r.`id` = t.`release_id` AND (r.`is_hidden` = 0 OR :userHasRoles) AND r.`deleted_at` IS NULL
        WHERE t.`release_id` = :id AND t.`deleted_at` IS NULL
        GROUP BY t.id
        ORDER BY t.`sort_order` ASC, t.`created_at` ASC
    ');

    $userHasRoles = $user && $user['has_roles'] === true ? 1 : 0;

    $query->bindParam(':id', $release['id']);
    $query->bindParam(':userHasRoles', $userHasRoles);

    $query->execute();
    if ($query->rowCount() == 0) {
        $page = str_replace('{torrent}', '', $page);
    } else {
        $torrent = '';
        while ($row = $query->fetch()) {
            $torrent .= getTemplate('torrent');
            $tmp = json_decode($row['info'], true);
            $torrent = str_replace('{ctime}', date('d.m.Y H:i', $row['ctime']), $torrent);
            $torrent = str_replace('{utctime}', date('c', $row['ctime']), $torrent);
            $torrent = str_replace('{seeders}', $row['seeders'], $torrent);
            $torrent = str_replace('{leechers}', $row['leechers'], $torrent);
            $torrent = str_replace('{completed}', $row['completed'], $torrent);
            $torrent = str_replace('{id}', $row['fid'], $torrent);

            $link = "/public/torrent/download.php?id={$row['fid']}";
            $torrent = str_replace('{link}', $link, $torrent);
            $torrent = str_replace('{rtype}', $tmp['0'], $torrent);
            $torrent = str_replace('{rnum}', $tmp['1'], $torrent);
            $torrent = str_replace('{rsize}', formatBytes($tmp['2']), $torrent);
        }
        $page = str_replace('{torrent}', $torrent, $page);
    }

    // Rutube
    if ($release['has_rutube_episodes'] == 1) {

        $episodes = [];
        $rutubeEpisodes = _getReleaseEpisodesFromRutube($release['id'] ?? null);

        foreach ($rutubeEpisodes as $episode) $episodes[] = ['id' => $episode['id'], 'ordinal' => $episode['ordinal'], 'rutube_id' => $episode['rutube_id']];

        $page = str_replace('{rutubeEpisodes}', json_encode($episodes), $page);

    }

    $page = str_replace('{rutubeEpisodes}', null, $page);

    return $page;
}

/*function uploadPoster($id)
{
    if (empty($_FILES['poster'])) {
        return;
    }
    if ($_FILES['poster']['error'] != 0) {
        return;
    }
    if (!in_array(exif_imagetype($_FILES['poster']['tmp_name']), [IMAGETYPE_PNG, IMAGETYPE_JPEG])) {
        return;
    }
    if ($_FILES['poster']['size'] > 2000000) {
        return;
    }
    $img = new Imagick($_FILES['poster']['tmp_name']);
    $img->setImageFormat('jpg');
    $img->resizeImage(350, 500, Imagick::FILTER_LANCZOS, 1, false);
    $img->setImageCompression(Imagick::COMPRESSION_JPEG);
    $img->setImageCompressionQuality(90);
    $img->stripImage();
    $file = $_SERVER['DOCUMENT_ROOT'] . '/upload/release/350x500/' . $id . '.jpg';
    deleteFile($file);
    file_put_contents($file, $img);
    $img->resizeImage(270, 390, Imagick::FILTER_LANCZOS, 1, false);
    $file = $_SERVER['DOCUMENT_ROOT'] . '/upload/release/270x390/' . $id . '.jpg';
    deleteFile($file);
    file_put_contents($file, $img);
    $img->resizeImage(240, 350, Imagick::FILTER_LANCZOS, 1, false);
    $file = $_SERVER['DOCUMENT_ROOT'] . '/upload/release/240x350/' . $id . '.jpg';
    deleteFile($file);
    file_put_contents($file, $img);
    $img->resizeImage(200, 280, Imagick::FILTER_LANCZOS, 1, false);
    $file = $_SERVER['DOCUMENT_ROOT'] . '/upload/release/200x280/' . $id . '.jpg';
    deleteFile($file);
    file_put_contents($file, $img);
}*/

/*function parse_bb_code($text)
{
     $text = str_replace('[br]', '<br>', $text);
     $find = [
         '~\[b\](.*?)\[/b\]~s',
         '~\[i\](.*?)\[/i\]~s',
         '~\[u\](.*?)\[/u\]~s',
         '~\[s\](.*?)\[/s\]~s',
         '~\[url\]((?:http|https?)://.*?)\[/url\]~s',
         '~\[url=((?:http|https?)://.*?)\](.*?)\[/url\]~s',
     ];
     $replace = [
         '<b>$1</b>',
         '<i>$1</i>',
         '<u>$1</u>',
         '<s>$1</s>',
         '<a href="$1" target="_blank">$1</a>',
         '<a href="$1" target="_blank">$2</a>',
     ];
     return preg_replace($find, $replace, $text);
}*/

/*function xrelease()
{
    global $db, $user, $var;
    $data = []; $sql = ['col' => [], 'val' => [], 'update' => []];
    if(!$user || $user['access'] < 2){
        _message('access', 'error');
    }
    if(empty($_POST['data'])){
        _message('empty', 'error');
    }
    $arr = ['name', 'ename', 'aname', 'year', 'season', 'type', 'genre', 'voice', 'translator', 'editing', 'decor', 'timing', 'announce', 'status', 'moonplayer', 'description', 'day', 'block', 'bakanim'];
    $post = json_decode($_POST['data'], true);
    foreach($arr as $key){
        if(array_key_exists($key, $post)){
            if(!isset($post["$key"])){
                continue;
            }
            $data[$key] = htmlspecialchars($post["$key"], ENT_QUOTES, 'UTF-8');
            if($key == 'description'){
                $data[$key] = parse_bb_code($data[$key]);
            }
            if($key == 'block'){
                $data[$key] = strtoupper($data[$key]);
            }
            if(mb_strlen($data[$key]) > 10000){
                _message('long', 'error');
            }
            $sql['col'][] = "`$key`";
            $sql['val'][] = ":$key";
            $sql['update'][] = "`$key` = :$key";
        }
    }
    if(!empty($data['status']) && array_key_exists($data['status'], $var['status'])){
        $data['search_status'] = $var['status'][$data['status']];
    }else{
        $data['search_status'] = $var['status']['3'];
    }
    $sql['col'][] = '`search_status`';
    $sql['val'][] = ':search_status';
    $sql['update'][] = '`search_status` = :search_status';
    if(!empty($sql['col'])){
        $id = '';
        $sqlCol = implode(',', $sql['col']);
        $sqlVal = implode(',', $sql['val']);
        if(!empty($post['update'])){
            $id = intval($post['update']);
        }
        if(!empty($id)){
            $query = $db->prepare('SELECT `id` FROM `releases` WHERE `id` = :id');
            $query->bindParam(':id', $id);
            $query->execute();
            if($query->rowCount() != 1){
                _message('wrongRelease');
            }
            uploadPoster($id);
            $sqlUpdate = implode(',', $sql['update']);
            $query = $db->prepare("UPDATE `releases` SET $sqlUpdate, `last_change` = UNIX_TIMESTAMP() WHERE `id` = :id");
            $query->bindParam(':id', $id);
        }else{
            $query = $db->prepare("INSERT INTO `xrelease` ($sqlCol) VALUES ($sqlVal)");
        }
        foreach($data as $k => &$v){ // https://stackoverflow.com/questions/12144557/php-pdo-bindparam-was-falling-in-a-foreach
            $query->bindParam(':'.$k, $v);
        }
        $query->execute();
        if(empty($id)){
            $id = $db->lastInsertId();
            uploadPoster($id);
        }
        if(empty($data['ename'])){
            $data['ename'] = '';
        }
        APIv2_UpdateTitle($id);
        die(json_encode(['err' => 'ok', 'url' => urlCode($id, $data['ename']),  'mes' => 'success']));
    }
}*/

function auth_history() // DONE
{

    global $db, $user, $var;
    $data = [];
    $query = $db->prepare('SELECT `created_at` as `time`, `ip_address` as `ip`, `user_agent` as `info`, `id` as  `sid` FROM `users_sessions` WHERE `users_id` = :uid ORDER BY `created_at` DESC LIMIT 100');
    $query->bindParam(':uid', $user['id']);
    $query->execute();

    while ($row = $query->fetch()) {
        $status = false;
        /*$tmp = $db->prepare('SELECT `id` FROM `session` WHERE `id` = :id AND `time` > :time');
        $tmp->bindParam(':id', $row['sid']);
        $tmp->bindParam(':time', $var['time']);
        $tmp->execute();
        if($tmp->rowCount() == 1){
            $status = true;
        }*/
        $data[strtotime($row['time'])] = [$row['ip'], base64_encode($row['info']), $status, $row['sid']];
    }

    return array_reverse($data, true);
}

function footerJS() // DONE
{
    global $var, $user, $conf;
    $result = '';
    $tmplJS = '<script src="{url}"></script>';
    $tmplCSS = '<link rel="stylesheet" type="text/css" href="{url}" />';
    $vk = '<script type="text/javascript" src="https://vk.com/js/api/openapi.js?160" async onload="VK.init({apiId: 5315207, onlyWidgets: true}); setTimeout(function(){ VK.Widgets.Comments(\'vk_comments\', {limit: 8, {page} attach: false});}, 75);" ></script>';
    switch ($var['page']) {
        default:
            break;
        case 'vk':
        case 'login':
            if (!$user) {
                $result = str_replace('{url}', 'https://www.google.com/recaptcha/api.js?render=' . $conf['recaptcha_public'], $tmplJS);
            }
            $result .= str_replace('{page}', '', $vk);
            break;
        case 'cp':
            $result .= str_replace('{url}', fileHash('/js/jquery.Jcrop.min.js'), $tmplJS);
            $result .= str_replace('{url}', fileHash('/css/jquery.Jcrop.min.css'), $tmplCSS);
            $result .= str_replace('{url}', fileHash('/js/uploadAvatar.js'), $tmplJS);
            $result .= str_replace('{url}', fileHash('/css/dataTables.bootstrap.min.css'), $tmplCSS);
            $result .= str_replace('{url}', fileHash('/js/jquery.dataTables.min.js'), $tmplJS);
            $result .= str_replace('{url}', fileHash('/js/dataTables.bootstrap.min.js'), $tmplJS);
            $result .= str_replace('{url}', fileHash('/js/tables.js'), $tmplJS);
            break;
        case 'new':
            $result .= str_replace('{url}', fileHash('/css/dataTables.bootstrap.min.css'), $tmplCSS);
            $result .= str_replace('{url}', fileHash('/js/jquery.dataTables.min.js'), $tmplJS);
            $result .= str_replace('{url}', fileHash('/js/dataTables.bootstrap.min.js'), $tmplJS);
            $result .= str_replace('{url}', fileHash('/js/tables.js'), $tmplJS);
            $result .= str_replace('{page}', '', $vk);
        case 'catalog':
            $result .= str_replace('{url}', fileHash('/css/chosen.min.css'), $tmplCSS);
            $result .= str_replace('{url}', fileHash('/css/simplePagination.css'), $tmplCSS);
            $result .= str_replace('{url}', fileHash('/css/bootstrap-toggle.min.css'), $tmplCSS);
            $result .= str_replace('{url}', fileHash('/css/chosen-bootstrap-theme.css'), $tmplCSS);
            $result .= str_replace('{url}', fileHash('/js/chosen.jquery.min.js'), $tmplJS);
            $result .= str_replace('{url}', fileHash('/js/jquery.simplePagination.js'), $tmplJS);
            $result .= str_replace('{url}', fileHash('/js/bootstrap-toggle.min.js'), $tmplJS);
            $result .= '<script>$(".chosen").chosen();</script>';
            $result .= str_replace('{url}', fileHash('/js/catalog.js'), $tmplJS);
            break;
        case 'alphabet':
            $result .= str_replace('{url}', fileHash('/js/jquery.lazy.min.js'), $tmplJS);
            $result .= '<script>$(function(){$(".lazy").lazy();});</script>';
            break;
        case 'release':
            if ($user && $user['access'] >= 2) {
                $result .= str_replace('{url}', fileHash('/css/chosen.min.css'), $tmplCSS);
                $result .= str_replace('{url}', fileHash('/css/chosen-bootstrap-theme.css'), $tmplCSS);
                $result .= str_replace('{url}', fileHash('/js/chosen.jquery.min.js'), $tmplJS);
                $result .= '<script>$(".chosen").chosen();</script>';
                $result .= '<style>.chosen-container { min-width:100%; }</style>';
            }
            $tmp = getReleaseVideo($var['release']['id']);
            if (!empty($tmp) && !$var['release']['block']) {
                $tmpPlayer = str_replace('{playerjs}', urlCDN(fileHash('/js/player.js')), getTemplate('playerjs'));
                $tmpPlayer = str_replace('{deny}', adsUrl(), $tmpPlayer);
                $result .= str_replace('{playlist}', $tmp, $tmpPlayer);
            }
            unset($tmp);
            $xname = '';
            if (!empty($var['release']['runame'])) {
                $xname = $var['release']['runame'];
            } elseif (!empty($var['release']['name'])) {
                $xname = $var['release']['name'];
            }
            if (!empty($xname)) {
                $result .= wsInfo($xname);
            }
            if (!empty($var['release']['id'])) {
                $result .= str_replace('{page}', '', $vk);
            } else {
                $result .= str_replace('{page}', 'pageUrl: \'/pages/error/404.php\',', $vk);
            }
            break;
        case 'app':
        case 'request':
        case 'links':
        case 'new-season':
        case 'seeders':
            $result .= str_replace('{page}', '', $vk);
            break;
        case 'donate':
            $result .= str_replace('{page}', '', $vk);
            $result .= '
				<script type="text/javascript">
					
					setTimeout(function(){
					f = document.createElement("iframe");
					f.frameBorder = 0;
					f.src = "https://yoomoney.ru/quickpay/shop-widget?writer=seller&targets=%D0%94%D0%BE%D0%B1%D1%80%D0%BE%D0%B2%D0%BE%D0%BB%D1%8C%D0%BD%D0%BE%D0%B5%20%D0%BF%D0%BE%D0%B6%D0%B5%D1%80%D1%82%D0%B2%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D0%B5&default-sum=200&button-text=14&payment-type-choice=on&mobile-payment-type-choice=on&successURL=https%3A%2F%2Fwww.anilibria.tv%2Fpages%2Fdonate.php&quickpay=shop&account=4100115839344905&"; 
					f.width = 360; 
					f.height = 220;
					$("#yandexMoney").append(f);
				  }, 75);
				</script>
			';
            //$result .= '<script src="'.fileTime('/js/player.js').'" type="text/javascript"></script>';
            //$result .= '<script>var player = new Playerjs({ id:"anilibriaPlayer", "title":"&nbsp;", "file":"'.fileTime('/upload/donate/1.mp4').'", poster:"'.fileTime('/upload/donate/1.jpg').'", preroll_deny:"vast2427,vast2585"});</script>';
            break;
        case '404':
        case '403':
            $result .= str_replace('{page}', "pageUrl: '/pages/error/{$var['page']}.php',", $vk);
            break;
    }
    return $result;
}

function wsInfo($name) // DONE
{
    global $conf;
    if (!empty($name)) {
        $url = base64_encode(mb_strtolower(htmlspecialchars(explode('?', $_SERVER['REQUEST_URI'], 2)[0], ENT_QUOTES, 'UTF-8')));
        $hash = hash('sha256', $name . $url . $conf['stat_secret']);
        $json = json_encode(['Hash' => $hash, 'Name' => $name, 'Url' => $url]);
        $result = str_replace('{ws}', $conf['stat_url'], getTemplate('stat'));
        $result = str_replace('{json}', $json, $result);
        return $result;
    }
}

/*function getRemoteCache()
{
    global $db, $conf;
    $query = $db->query('SELECT `id` FROM `releases`');
    while ($row = $query->fetch()) {
        getRemote($conf['nginx_domain'] . '/?id=' . $row['id'] . '&v2=1', 'video' . $row['id'], true);
    }
}*/

/*function curlTor($url)
{
     $ch = curl_init($url);
     curl_setopt($ch, CURLOPT_HEADER, 0);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
     curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1:9050");
     curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
     curl_setopt($ch, CURLOPT_TIMEOUT, 10);
     $result = curl_exec($ch);
     curl_close($ch);
     return $result;
}*/

/*function getRemote($url, $key, $update = false)
{
    global $cache;
    $ctx = stream_context_create(['http' => ['timeout' => 5]]);
    $data = $cache->get('anilibria' . $key);
    if (empty($data) || $update) {
        if (!$data = file_get_contents($url, false, $ctx)) {
            return false;
        }
        if (!isJson($data)) {
            return false;
        }
        $cache->set('anilibria' . $key, $data, 300);
    }
    return $data;
}*/

function wsInfoShow() // DONE
{
    $result = '';
    $arr = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/upload/stats.json'), true);
    $all = $arr['sum'];
    unset($arr['sum']);
    if ($arr) {
        foreach ($arr as $key => $val) {
            $result .= "<tr><td style=\"display:inline-block; width:390px;overflow:hidden;white-space:nowrap; text-overflow: ellipsis;\"><a href=\"https://www.anilibria.tv{$val['1']}\">{$val['0']}</a></td><td class=\"tableCenter\">{$val['2']}</a></td></tr>";
        }
        $result .= "<tr style=\"border-top: 3px solid #ddd; border-bottom: 3px solid #ddd;\"><td style=\"display:inline-block; width:390px;overflow:hidden;white-space:nowrap; text-overflow: ellipsis;\">Всего зрителей</td><td class=\"tableCenter\">$all</a></td></tr>";
    }
    return $result;
}

/*function mp4_link($value)
{
    global $conf, $var;
    $time = $var['time'] + 60 * 60 * 2;
    $key = str_replace("=", "", strtr(base64_encode(md5("{$time}/videos/{$value}" . " {$conf['nginx_secret']}", true)), "+/", "-_"));
    $url = htmlspecialchars("{$conf['nginx_download_cache_server']}/get/$key/$time/$value", ENT_QUOTES, 'UTF-8');
    return $url;
}*/

/*function anilibria_getHost($hosts)
{
    $host = [];
    if (empty($hosts)) {
        return false;
    }
    foreach ($hosts as $key => $val) {
        $host = array_merge($host, array_fill(0, $val, $key));
    }
    if (count($host) == 0) {
        return false;
    }
    if (count($host) == 1) {
        return $host[0] . ".libria.fun";
    }
    shuffle($host);
    return $host[random_int(0, count($host) - 1)] . ".libria.fun";
}*/

function getReleaseVideo($id) // DONE
{
    global $conf, $var, $db, $user, $cache;

    // Episodes
    $query = $db->prepare('
        SELECT re.* 
            from `releases_episodes` as re 
            inner join `releases` as r on re.releases_id = r.id
            where re.releases_id = :id and re.`is_visible` = 1 AND re.`deleted_at` IS NULL 
              AND (r.`is_hidden` = 0 OR :userHasRoles) AND r.`deleted_at` IS NULL AND
              (re.`hls_480` IS NOT NULL OR re.`hls_720` IS NOT NULL OR re.`hls_1080` IS NOT NULL)
            ORDER BY re.`sort_order` ASC
        ');

    $userHasRoles = $user && $user['has_roles'] === true ? 1 : 0;

    $query->bindValue(':id', $id);
    $query->bindParam(':userHasRoles', $userHasRoles);

    $query->execute();
    $episodes = $query->fetchAll();

    $server = ['url' => 'https://cache.libria.fun/videos/media'];
    $playlist = [];

    foreach ($episodes as $episode) {

        $qualities = [];

        $hls480 = !empty($episode['hls_480']) ? sprintf('480/%s', $episode['hls_480']) : null;
        $hls720 = !empty($episode['hls_720']) ? sprintf('720/%s', $episode['hls_720']) : null;
        $hls1080 = !empty($episode['hls_1080']) ? sprintf('1080/%s', $episode['hls_1080']) : null;

        // Check if fhd quality is disabled
        // Swap 1080 with 720 hash
        if ($hls720 && $hls1080 && isset($cache) && $cache->get('fhdQualityIsDisabled')) $hls1080 = $hls720;

        if ($hls480) $qualities[] = sprintf('[480p]%s/ts/%s/%s/%s', $server['url'], $episode['releases_id'], $episode['ordinal'], $hls480);
        if ($hls720) $qualities[] = sprintf('[720p]%s/ts/%s/%s/%s', $server['url'], $episode['releases_id'], $episode['ordinal'], $hls720);
        if ($hls1080) $qualities[] = sprintf('[1080p]%s/ts/%s/%s/%s', $server['url'], $episode['releases_id'], $episode['ordinal'], $hls1080);


        $endingSkip = null; // future
        $openingSkip = array_filter([$episode['opening_starts_at'] ?? null, $episode['opening_ends_at'] ?? null]);

        // Get skips
        $skips = array_filter(
            [
                empty($openingSkip) === false ? implode('-', $openingSkip) : null, // opening
                empty($endingSkip) === false ? implode('-', $endingSkip) : null, // ending
            ]
        );

        $playlist[] = [
            'id' => "s{$episode['ordinal']}",
            'skip' => empty($skips) === false ? implode($skips, ',') : null,
            'file' => implode(',', $qualities),
            'title' => sprintf('Серия %s', $episode['ordinal']),
            'poster' => !empty($episode['preview'])
                ? ImageThumbnail::make(implode(DIRECTORY_SEPARATOR, [$conf['release_episode_poster_host'], $episode['releases_id'], $episode['ordinal'], $episode['preview']]))->getThumbnail(720, null, 80)
                : null,
            'download' => null,
        ];
    }

    return json_encode($playlist);
}


function getTeams(): array
{

    global $db;

    $query = $db->query('
        SELECT 
            `tu`.`id`,
            IF(`tu`.`nickname` IS NOT NULL, `tu`.`nickname`, `u`.`nickname`) AS `nickname`,
            `tu`.`sort_order` AS `user_sort_order`,
            `tu`.`is_intern` AS `is_intern`,
            `tu`.`is_vacation` AS `is_vacation`,
            `t`.`id` AS `team_id`,
            `t`.`title` AS `team_title`,
            `t`.`description` AS `team_description`,
            `t`.`sort_order` AS `team_sort_order`,
            GROUP_CONCAT(`tr`.`title` ORDER BY `tr`.`sort_order` ASC SEPARATOR "," ) AS `roles`,
            GROUP_CONCAT(`tr`.`color` ORDER BY `tr`.`sort_order` ASC SEPARATOR ",") AS `colors`
             
        FROM `teams_users` AS `tu`
        LEFT JOIN `users` AS `u` ON `u`.`id` = `tu`.`user_id`
        INNER JOIN `teams` AS `t` ON `t`.`id` = `tu`.`team_id` AND `t`.`deleted_at` IS NULL
        LEFT JOIN `teams_users_roles` AS `tur` ON `tur`.`team_user_id` = `tu`.`id`
        LEFT JOIN `teams_roles` AS `tr` ON `tr`.id = `tur`.`team_role_id` 

        WHERE `tu`.`deleted_at` IS NULL
        GROUP BY `tu`.`id`

    ');

    $teams = [];
    $users = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as $user) {

        // Team
        $teams[$user['team_id']]['title'] = $user['team_title'] ?? null;
        $teams[$user['team_id']]['users'] = $teams[$user['team_id']]['users'] ?? [];
        $teams[$user['team_id']]['sort_order'] = $user['team_sort_order'] ?? 0;
        $teams[$user['team_id']]['description'] = $user['team_description'] ?? null;

        // Index
        $index = count($teams[$user['team_id']]['users']);

        // Roles + Colors
        $roles = explode(',', $user['roles'] ?? '');
        $colors = explode(',', $user['colors'] ?? '');
        $userRoles = [];

        foreach ($roles as $i => $value) {
            $userRoles[] = [
                'title' => $value,
                'color' => $colors[$i] ? trim($colors[$i]) : null,
            ];
        }

        // User
        $teams[$user['team_id']]['users'][$index] = [
            'id' => $user['id'] ?? null,
            'roles' => $userRoles,
            'nickname' => $user['nickname'] ?? null,
            'is_intern' => $user['is_intern'] == 1,
            'sort_order' => $user['user_sort_order'] ?? 0,
            'is_vacation' => $user['is_vacation'] == 1,
        ];

    }


    // Sort users inside each team
    // Sort ASC
    foreach ($teams as $key => $team) {
        usort($teams[$key]['users'], function ($user1, $user2) {
            return $user1['sort_order'] <=> $user2['sort_order'];
        });
    }

    // Sort teams
    // Sort ASC
    usort($teams, function ($team1, $team2) {
        return $team1['sort_order'] <=> $team2['sort_order'];
    });

    return array_values($teams);
}


/*function youtubeVideoExists($id)
{
    global $db;
    $x = get_headers("http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=$id&format=json")[0];
    if($x == 'HTTP/1.0 404 Not Found' || $x == 'HTTP/1.0 401 Unauthorized'){
        $query = $db->prepare('DELETE FROM `video_contents` WHERE `vid` = :vid');
        $query->bindParam(':vid', $id);
        $query->execute();
        deleteFile($_SERVER['DOCUMENT_ROOT'].'/upload/youtube/'.hash('crc32', $id).'.jpg');
        return false;
    }
    return true;
}*/

/*function updateYoutubeStat()
{
    global $db;
    $query = $db->query('SELECT `id`, `vid` FROM `video_contents`');
    $query->execute();
    while($row = $query->fetch()){
        $stat = youtubeStat($row['vid']);
        if(!$stat){
            continue;
        }
        youtubeGetImage($row['vid']);
        $tmp = $db->prepare('UPDATE `youtube` SET `view` = :view, `comment` = :comment WHERE `id` = :id');
        $tmp->bindParam(':view', $stat['0']);
        $tmp->bindParam(':comment', $stat['1']);
        $tmp->bindParam(':id', $row['id']);
        $tmp->execute();
    }
}*/

/*function youtubeStat($id)
{
    global $db, $conf;
    if(youtubeVideoExists($id)){
        $json = file_get_contents("https://www.googleapis.com/youtube/v3/videos?part=statistics&id=$id&key={$conf['youtube_secret']}");
        if(!empty($json)){
            $arr = json_decode($json, true);
            $comment = 0;
            if(!empty($arr['items']['0']['statistics']['viewCount'])) $view = $arr['items']['0']['statistics']['viewCount'];
            if(!empty($arr['items']['0']['statistics']['commentCount'])) $comment = $arr['items']['0']['statistics']['commentCount'];
        }
        return [$view, $comment];
    }
    return false;
}*/

/*function youtubeGetImage($id)
{
    global $db;
    $remote = "https://img.youtube.com/vi/$id/maxresdefault.jpg";
    $x = get_headers($remote)[0];
    if($x == 'HTTP/1.0 404 Not Found' || $x == 'HTTP/1.0 401 Unauthorized'){
        return;
    }
    $hash = md5(file_get_contents($remote));
    $file = $_SERVER['DOCUMENT_ROOT'].'/upload/youtube/'.hash('crc32', $id).'.jpg';
    $query = $db->prepare('SELECT `hash` FROM `youtube` WHERE `vid` = :vid');
    $query->bindParam(':vid', $id);
    $query->execute();
    $row = $query->fetch();
    if($hash != $row['hash'] || !file_exists($file)){
        $data = fopen($remote, 'rb');
        $img = new Imagick();
        $img->readImageFile($data);
        $img->resizeImage(435,245,Imagick::FILTER_LANCZOS, 1, true);
        $img->setImageCompression(Imagick::COMPRESSION_JPEG);
        $img->setImageCompressionQuality(85);
        $img->stripImage();
        file_put_contents($file, $img);
        $query = $db->prepare('UPDATE `youtube` SET `hash` = :hash WHERE `vid` = :vid');
        $query->bindParam(':hash', $hash);
        $query->bindParam(':vid', $id);
        $query->execute();
    }
}*/

/*function updateYoutube()
{
    function saveYoutube($arr, $type){
        global $db;
        $arr['items'] = array_reverse($arr['items']);
        foreach($arr['items'] as $val){
            if($type == 1){
                if(empty($val['id']['videoId'])){
                    continue;
                }
                $id = $val['id']['videoId'];
            }
            if($type == 2){
                if(empty($val['snippet']['resourceId']['videoId'])){
                    continue;
                }
                $id = $val['snippet']['resourceId']['videoId'];
            }
            if($type == 3){
                if(empty($val['snippet']['resourceId']['videoId'])){
                    continue;
                }
                $id = $val['snippet']['resourceId']['videoId'];
            }

            $query = $db->prepare('SELECT `id` FROM `youtube` WHERE `vid` = :vid');
            $query->bindParam(':vid', $id);
            $query->execute();
            if($query->rowCount() == 1){
                continue;
            }
            $val['snippet']['title'] = htmlspecialchars($val['snippet']['title'], ENT_QUOTES, 'UTF-8');
            $time = strtotime($val['snippet']['publishedAt']);
            $query = $db->prepare('INSERT INTO `youtube` (`title`, `vid`, `time`, `type`) VALUES (:title, :vid, :time, :type)');
            $query->bindParam(':title', $val['snippet']['title']);
            $query->bindParam(':vid', $id);
            $query->bindParam(':time', $time);
            $query->bindParam(':type', $type);
            $query->execute();
            youtubeGetImage($id);
        }
    }
    global $conf;

    $arr = json_decode(file_get_contents("https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=25&playlistId={$conf['youtube_playlist']}&key={$conf['youtube_secret']}"), true);
    saveYoutube($arr, 2); // Anime announce playlist

    //$arr = json_decode(file_get_contents("https://www.googleapis.com/youtube/v3/search?order=date&part=snippet&channelId={$conf['youtube_chanel']}&maxResults=50&key={$conf['youtube_secret']}"), true);
    //saveYoutube($arr, 1); // video

    $arr = json_decode(file_get_contents("https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=25&playlistId={$conf['youtube_playlist_main']}&key={$conf['youtube_secret']}"), true);
    saveYoutube($arr, 3); // AniLibria Main playlist

    $arr = json_decode(file_get_contents("https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=15&playlistId={$conf['youtube_playlist_lupin']}&key={$conf['youtube_secret']}"), true);
    saveYoutube($arr, 3); // Lupin playlist

    $arr = json_decode(file_get_contents("https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=15&playlistId={$conf['youtube_playlist_sharon']}&key={$conf['youtube_secret']}"), true);
    saveYoutube($arr, 3); // Sharon playlist

    $arr = json_decode(file_get_contents("https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=15&playlistId={$conf['youtube_playlist_silv']}&key={$conf['youtube_secret']}"), true);
    saveYoutube($arr, 3); // Silv playlist

    $arr = json_decode(file_get_contents("https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=15&playlistId={$conf['youtube_playlist_dejz']}&key={$conf['youtube_secret']}"), true);
    saveYoutube($arr, 3); // Dejz playlist
}*/

function youtubeShow() // DONE
{
    global $conf;
    global $db;
    $i = 0;
    $arr = [];
    $arr1 = [];
    $arr2 = [];
    $data = [];
    $result = '';
    $tmpl = '<td><a href="{url}" target="_blank"><img src="{img}" alt="{alt}" height="245"></a></td>';
    $query = $db->query('
        SELECT 
            `vc`.`id`, 
            `vc`.`video_id` AS `vid`, 
            `vc`.`title`, 
            `vc`.`preview` 
        FROM `video_contents` as `vc`
        INNER JOIN `video_origins` as `vo` on `vo`.`id` = `vc`.`video_origin_id` 
        WHERE `vo`.`is_announce` = 0 AND `vc`.`deleted_at` IS NULL
        ORDER BY `vc`.`created_at` DESC  LIMIT 6');

    $query->execute();
    while ($row = $query->fetch()) {
        $arr1[] = ['id' => $row['id'], 'vid' => $row['vid'], 'title' => $row['title'], 'preview' => $row['preview']];
    }

    $query = $db->query('
         SELECT 
            `vc`.`id`, 
            `vc`.`video_id` AS `vid`, 
            `vc`.`title`, 
            `vc`.`preview` 
        FROM `video_contents` as `vc`
        INNER JOIN `video_origins` as `vo` on `vo`.`id` = `vc`.`video_origin_id` 
        WHERE `vo`.`is_announce` = 1 AND `vc`.`deleted_at` IS NULL
        ORDER BY `vc`.`created_at` DESC  LIMIT 6');

    $query->execute();
    while ($row = $query->fetch()) {
        $arr2[] = ['id' => $row['id'], 'vid' => $row['vid'], 'title' => $row['title'], 'preview' => $row['preview']];
    }
    $arr1 = array_slice($arr1, 0, count($arr2));
    foreach ($arr1 as $k => $v) {
        $data[] = $arr2["$k"];
        $data[] = $v;
        if (count($data) == 12) {
            break;
        }
    }
    $i = 0;
    foreach ($data as $v) {

        //$youtube = str_replace('{img}', urlCDN(fileTime('/upload/youtube/'.hash('crc32', $v['vid']).'.jpg')), $youtube);

        $videoPreview = !empty($v['preview']) ? sprintf('%s/%s/%s', $conf['youtube_poster_host'], $v['id'], $v['preview']) : null;
        $videoThumbnail = $videoPreview ? ImageThumbnail::make($videoPreview)->getThumbnail(400) : null;

        $youtube = str_replace('{url}', "https://www.youtube.com/watch?v={$v['vid']}", $tmpl);
        $youtube = str_replace('{img}', $videoThumbnail, $youtube);


        $youtube = str_replace('{alt}', $v['title'], $youtube);
        $arr["$i"][] = $youtube;
        if (count($arr[$i]) == 2) {
            $i++;
        }
        unset($youtube);
    }
    if (!empty($arr)) {
        foreach ($arr as $key => $val) {
            $tmp = '<tr>';
            foreach ($val as $k => $v) {
                $tmp .= $v;
            }
            $tmp .= '</tr>';
            $result .= $tmp;
        }
    }
    return $result;
}

/*function updateReleaseAnnounce()
{
    global $db, $user, $var;
    if(!$user || $user['access'] < 2){
        _message('access', 'error');
    }
    if(empty($_POST['id'])){
        _message('empty', 'error');
    }
    if(mb_strlen($_POST['announce']) > 200){
        _message('long', 'error');
    }
    if(!ctype_digit($_POST['id'])){
        _message('wrong', 'error');
    }
    $query = $db->prepare('SELECT `id` FROM `xrelease` WHERE `id` = :id');
    $query->bindParam(':id', $_POST['id']);
    $query->execute();
    if($query->rowCount() == 0){
        _message('wrongRelease', 'error');
    }
    $_POST['announce'] = htmlspecialchars($_POST['announce'], ENT_QUOTES, 'UTF-8');
    $query = $db->prepare('UPDATE `xrelease` SET `announce` = :announce, `last_change` = UNIX_TIMESTAMP() WHERE `id` = :id');
    $query->bindParam(':announce', $_POST['announce']);
    $query->bindParam(':id', $_POST['id']);
    $query->execute();
    APIv2_UpdateTitle($_POST['id']);
    _message('success');
}*/

/*function showEditTorrentTable()
{
    global $db, $var;
    $result = '';
    $arr = [];
    $query = $db->prepare('SELECT `fid`, `rid`, `ctime`, `info` FROM `xbt_files` WHERE `rid` = :rid');
    $query->bindParam(':rid', $var['release']['id']);
    $query->execute();
    while($row = $query->fetch()){
        $date = date('d.m.Y H:i', $row['ctime']);
        $info = json_decode($row['info'], true);
        $tmp = getTemplate('edit_torrent');
        $tmp = str_replace('{id}', $row['fid'], $tmp);
        $tmp = str_replace('{quality}', $info['0'], $tmp);
        $tmp = str_replace('{series}', $info['1'], $tmp);
        $tmp = str_replace('{date}', $date, $tmp);
        $result .= $tmp;

        $arr[] = ['do' => 'change', 'fid' => $row['fid'], 'rid' => $row['rid'], 'series' => $info['1'], 'quality' => $info['0'], 'ctime' => $date, 'delete' => ''];
    }
    return $result;
}*/

function deleteFile($f) // DONE
{
    if (file_exists($f)) {
        unlink($f);
    }
}

/*function removeRelease()
{
    global $db, $user;
    if(!$user || $user['access'] < 2){
        _message('access', 'error');
    }
    if(empty($_POST['id'])){
        _message('empty', 'error');
    }
    $query = $db->prepare('SELECT `id` FROM `xrelease` WHERE `id` = :id');
    $query->bindParam(':id', $_POST['id']);
    $query->execute();
    if($query->rowCount() == 0){
        _message('wrongRelease', 'error');
    }
    if($user['access'] == 4){
        $query = $db->prepare('DELETE FROM `xrelease` WHERE `id` = :id');
        $query->bindParam(':id', $_POST['id']);
        $query->execute();
        $query = $db->prepare('SELECT `fid` FROM `xbt_files` WHERE `rid` = :id');
        $query->bindParam(':id', $_POST['id']);
        $query->execute();
        if($query->rowCount() > 0){
            while($row = $query->fetch()){
                torrentDelete($row['fid']);
            }
        }
        $query = $db->prepare('DELETE FROM `favorites` WHERE `rid` = :rid');
        $query->bindParam(':rid', $_POST['id']);
        $query->execute();
        deleteFile($_SERVER['DOCUMENT_ROOT'].'/upload/release/200x280/'.$_POST['id'].'.jpg');
        deleteFile($_SERVER['DOCUMENT_ROOT'].'/upload/release/240x350/'.$_POST['id'].'.jpg');
        deleteFile($_SERVER['DOCUMENT_ROOT'].'/upload/release/270x390/'.$_POST['id'].'.jpg');
        deleteFile($_SERVER['DOCUMENT_ROOT'].'/upload/release/350x500/'.$_POST['id'].'.jpg');
    }else{
        $query = $db->prepare('UPDATE `xrelease` SET `status` = \'3\' WHERE `id` = :id');
        $query->bindParam(':id', $_POST['id']);
        $query->execute();
    }
    _message('success');
}*/

/*function releaseTable()
{
    global $db, $user, $var;
    $result = '';
    if(!$user || $user['access'] < 2){
        _message('access', 'error');
    }
    $data = []; $order = 'DESC'; $column = 'id'; $search = '';
    $arr = ['draw' => 1, 'start' => 0, 'length' => 10];
    foreach($arr as $key => $val){
        if(array_key_exists($key, $_POST)){
            $_POST["$key"] = intval($_POST["$key"]);
            if(!empty($_POST["$key"])){
                $arr[$key] = $_POST["$key"];
            }
        }
    }
    if(!empty($_POST['order']['0']['dir'])){
        if($_POST['order']['0']['dir'] == 'asc'){
            $order = 'ASC';
        }
    }
    if(!empty($_POST['order']['0']['column'])){
        if($_POST['order']['0']['column'] == 1){
            $column = 'name';
        }
        if($_POST['order']['0']['column'] == 2){
            $column = 'status';
        }
    }
    if($arr['length'] > 100){
        $arr['length'] = 100;
    }
    if(!empty($_POST['search']['value'])){
        $search = $_POST['search']['value'];
    }
    if(empty($search)){
        $query = $db->query("SELECT count(*) OVER (), c.* FROM `xrelease` c ORDER BY `{$column}` $order LIMIT {$arr['start']}, {$arr['length']}");
    }else{
        $search = "*$search*";
        $query = $db->prepare("SELECT count(*) OVER (), c.* FROM `xrelease` c WHERE MATCH(`name`, `ename`, `search_status`) AGAINST (:search IN BOOLEAN MODE) ORDER BY `{$column}` {$order} LIMIT {$arr['start']}, {$arr['length']}");
        $query->bindParam(':search', $search);
    }
    $query->execute();
    $total = 0;
    while($row = $query->fetch()){
        if(empty($total)){
            $total = $row['count(*) OVER ()'];
        }
        $tmp['id'] = "<a href='/release/".releaseCodeByID($row['id']).".html' style='color: #383838;'>{$row['id']}</a>";
        $tmp['name'] = $row['name'];
        $tmp['status'] = $var['status'][$row['status']];
        $tmp['last'] = "<a data-admin-release-delete='{$row['id']}' href='#' style='color: #383838;'><span class='glyphicon glyphicon-remove'></span></a>";
        $data[] = array_values($tmp);
    }
    return ['draw' => $row['draw'], 'start' => $row['start'], 'length' => $row['length'], 'recordsTotal' => $total, 'recordsFiltered' => $total, 'data' => $data];
}*/


function fileHash($file)
{ // DONE

    if (!file_exists($file)) {
        $file = $_SERVER['DOCUMENT_ROOT'] . $file;
        if (!file_exists($file)) {
            return false;
        }
    }

    $content = file_get_contents($file);
    $hashOfContent = md5($content);

    return str_replace($_SERVER['DOCUMENT_ROOT'], '', $file) . '?' . $hashOfContent;

}

function fileTime($file) // DONE
{
    global $cache, $var;
    if (!file_exists($file)) {
        $file = $_SERVER['DOCUMENT_ROOT'] . $file;
        if (!file_exists($file)) {
            return false;
        }
    }
    $hash = crc32($file);
    $time = $cache->get("file{$hash}");
    if ($time === false) {
        $time = filemtime($file);
        $cache->set("file{$hash}", $time, 600);
    }
    return str_replace($_SERVER['DOCUMENT_ROOT'], '', $file) . '?' . $time;
}

/*function sphinxPrepare($x)
{
      $x = explode(',', $x);
      $x = array_filter($x);
      $x = implode(',', $x);
      return preg_replace('/[^\w, ]+/u', '', $x);
}*/

function xSearch() // DONE
{
    global $db, $user;

    $search = trim($_POST['search'] ?? '');

    if (empty($search)) die();

    $query = $db->prepare("
        SELECT * from `releases` 
        WHERE 
            (
                name LIKE CONCAT('%', :search, '%') or
                name_english LIKE CONCAT('%', :search, '%') or
                name_alternative LIKE CONCAT('%', :search, '%')
            ) 
            AND `deleted_at` IS NULL AND (`is_hidden` = 0 OR :userHasRoles)
        LIMIT 12
    ");

    $userHasRoles = $user && $user['has_roles'] === true ? 1 : 0;

    $query->bindValue('search', $search);
    $query->bindParam('userHasRoles', $userHasRoles);

    $query->execute();

    $json = [];
    $result = '';
    $releases = $query->fetchAll();

    if (empty($releases)) {
        $result .= "<tr><td><span style='display: block; width: 247px; margin-left: 13px; padding-top: 7px; padding-bottom: 7px; cursor: not-allowed;'>Ничего не найдено...</span></td></tr>";
    }else {
        foreach ($releases as $release) {
            $code = $release['alias'];
            $json[] = ['id' => $release['id'], 'name' => base64_encode($release['name']), 'ename' => $release['name_english'], 'code' => $code];
            $result .= "<tr><td><a href='/release/$code.html'><span style='display: block; width: 247px; margin-left: 13px; padding-top: 7px; padding-bottom: 7px;'>{$release['name']}</span></a></td></tr>";
        }
    }

    if (isset($_POST['json'])) $result = $json;

    _message2($result);

    /*global $sphinx, $db;
    $result = '';
    $limit = '';
    $data = [];
    $arr = ['search', 'key'];
    $keys = ['name,ename,aname', 'genre', 'year'];
    foreach ($arr as $key) {
        if (!empty($_POST["$key"])) {
            $data["$key"] = trim($_POST["$key"]);
        }
    }
    if (empty($data['search'])) {
        die;
    }
    if (empty($data['key']) || !in_array($data['key'], $keys)) {
        $data['key'] = $keys['0'];
    }
    if (!$data['search'] = sphinxPrepare($data['search'])) {
        die;
    }

    $query = $sphinx->prepare("SELECT `id` FROM anilibria WHERE MATCH(:search) AND `status` != 3 ORDER BY `rating` DESC LIMIT 12");
    $query->bindValue(':search', "@({$data['key']}) ({$data['search']})");
    $query->execute();
    $tmp = $query->fetchAll();
    $json = [];


    foreach ($tmp as $k => $v) {
        $query = $db->prepare('SELECT `id`, `name`, `name_english` AS `ename` FROM `releases` WHERE `id` = :id');
        $query->bindParam(':id', $v['id']);
        $query->execute();
        if ($query->rowCount() != 1) {
            continue;
        }
        $row = $query->fetch();
        $code = releaseCodeByID($row['id']);

        $json[] = ['id' => $row['id'], 'name' => base64_encode($row['name']), 'ename' => $row['ename'], 'code' => $code];

        $result .= "<tr><td><a href='/release/$code.html'><span style='display: block; width: 247px; margin-left: 13px; padding-top: 7px; padding-bottom: 7px;'>{$row['name']}</span></a></td></tr>";
    }
    if (isset($_POST['json'])) {
        $result = $json;
    }
    _message2($result);*/
}

function showPosters() // DONE
{
    global $db, $var, $conf;
    $result = '';

    switch ($var['page']) {
        case 'main':
            $limit = 4;
            break;
        default:
            $limit = 5;
            break;
    }

    $query = $db->query('
        SELECT 
           r.`id`, 
           r.`name`, 
           r.`name_english` AS `ename`, 
           r.`alias` AS `code`, 
           r.`description`, 
           IF(r.`poster` IS NOT NULL, CONCAT("' . $conf['release_poster_host'] . '/", r.`id`, "/", r.`poster`), NULL) as `poster`, 
           r.`is_wakanim` AS `bakanim`,
           EXISTS(SELECT * FROM `torrents` WHERE `release_id` = r.`id` AND `deleted_at` IS NULL) AS `has_torrents`
            
        FROM `releases` as r
        WHERE r.`is_hidden` = 0 and r.`deleted_at` IS NULL
        GROUP BY r.`id`
        ORDER BY r.`fresh_at` DESC 
        LIMIT ' . $limit
    );


    while ($row = $query->fetch()) {

        $img = !empty($row['poster'])
            ? ImageThumbnail::make($row['poster'])->getThumbnail(240, 350)
            : '/upload/release/240x350/default.jpg';

        $tmp = getTemplate('torrent-block');
        $tmp = str_replace('{id}', $row['code'], $tmp);
        $tmp = str_replace('{img}', $img, $tmp);
        $tmp = str_replace('{alt}', "{$row['name']} / {$row['ename']}", $tmp);
        $tmp = str_replace('{runame}', "{$row['name']}", $tmp);
        $tmp = str_replace('{description}', releaseDescriptionByID($row['id'], 179), $tmp);
        $tmp = str_replace('{series}', releaseSeriesByID($row['id']), $tmp);
        //$tmp = str_replace('{torlink}', getTorrentDownloadLink($row['id']), $tmp);

        if ($row['has_torrents'] == 1 && $row['bakanim'] == 0) {
            $link = getTorrentDownloadLink($row['id']);
            if ($link) {
                $tmp = str_replace('{torlink}', "<a href='" . $link . "' class='last_tor_button'>СКАЧАТЬ</a>", $tmp);
            }
        }

        $tmp = str_replace('{torlink}', "", $tmp);
        $result .= $tmp;
    }

    return $result;
}

function getGenreList() // DONE
{
    global $db;
    $arr = [];
    $result = '';
    $total = 0;
    $tmpl = '<option value="{name}">{name}</option>';
    $query = $db->query('SELECT `name` from `genres` ORDER BY `name` ASC');
    while ($row = $query->fetch()) {
        $arr[] = $row['name'];
    }
    //sort($arr);
    foreach ($arr as $k => $v) {
        $result .= str_replace('{name}', $v, $tmpl);
    }
    return $result;
}

/*function releaseNameByID($id)
{
    global $db;
    $query = $db->prepare('SELECT `name`, `name_english` AS `ename` FROM `releases` WHERE `id` = :id');
    $query->bindParam(':id', $id);
    $query->execute();
    $result = $query->fetch();
    return [$result['name'], $result['ename']];
}*/

function releaseCodeByID($id) // DONE
{
    global $db, $user;


    $userHasRoles = $user && $user['has_roles'] === true ? 1 : 0;

    $query = $db->prepare('SELECT `alias` AS `code` FROM `releases` WHERE `id` = :id AND (`is_hidden` = 0 OR :userHasRoles) AND `deleted_at` IS NULL');

    $query->bindParam(':id', $id);
    $query->bindParam(':userHasRoles', $userHasRoles);

    $query->execute();

    return $query->fetch()['code'];
}

function releaseSeriesByID($id) // DONE
{

    global $db, $user;

    $query = $db->prepare('SELECT `description` FROM `torrents` WHERE `release_id` = :id and `deleted_at` IS NULL ORDER BY `updated_at` DESC');
    $query->bindParam(':id', $id);
    $query->execute();
    $row = $query->fetch();
    $seriesFromTorrents = $row['description'] ?? null;

    $query = $db->prepare('
        SELECT re.`ordinal` 
        FROM `releases_episodes` as re
        INNER JOIN `releases` as r on r.`id` = re.`releases_id`
        WHERE 
            r.`id` = :id AND (r.`is_hidden` = 0 OR :userHasRoles) AND r.`deleted_at` IS NULL AND  
            re.`is_visible` = 1 and re.`deleted_at` IS NULL AND 
            (re.`hls_480` IS NOT NULL OR re.`hls_720` IS NOT NULL OR re.`hls_1080` IS NOT NULL OR re.`rutube_id` IS NOT NULL) 
        ORDER BY re.`ordinal` ASC
    ');

    $userHasRoles = $user && $user['has_roles'] === true ? 1 : 0;

    $query->bindParam(':id', $id);
    $query->bindParam(':userHasRoles', $userHasRoles);

    $query->execute();
    $seriesOrdinals = $query->fetchAll(PDO::FETCH_ASSOC);

    $seriesOrdinals = array_map(function (array $episode) {
        return (float)$episode['ordinal'] ?? 0;
    }, $seriesOrdinals);


    $seriesFromEpisodes = count($seriesOrdinals) > 0 ? (min($seriesOrdinals) . " — " . max($seriesOrdinals)) : null;

    return $seriesFromTorrents ?? $seriesFromEpisodes ?? '';

}

function releaseDescriptionByID($id, $SymCount) // DONE
{
    global $db, $user;

    $query = $db->prepare('SELECT `description` FROM `releases` WHERE `id` = :id AND (`is_hidden` = 0 OR :userHasRoles) AND `deleted_at` IS NULL');

    $userHasRoles = $user && $user['has_roles'] === true ? 1 : 0;

    $query->bindParam(':id', $id);
    $query->bindParam(':userHasRoles', $userHasRoles);

    $query->execute();
    $row = $query->fetch();
    $shortdescription = mb_strimwidth($row['description'], 0, $SymCount, "...");
    $cutdescription = explode("\n", $shortdescription);
    return strip_tags($cutdescription[0]);
}

function getTorrentDownloadLink($id) // DONE
{
    global $db, $user;
    $query = $db->prepare('SELECT `id` AS `fid` FROM `torrents` WHERE `release_id` = :id and `deleted_at` IS NULL ORDER BY `updated_at` DESC LIMIT 1');
    $query->bindParam(':id', $id);
    $query->execute();
    $row = $query->fetch();

    return !empty($row['fid']) ? "/public/torrent/download.php?id={$row['fid']}" : "";

    /*if ($user) {
        $link = "/public/torrent/download.php?id={$row['fid']}";
    } else {
        $link = "/upload/torrents/{$row['fid']}.torrent";
    }
    return $link;*/
}

function showCatalog() // DONE
{
    global $db, $user;
    $i = 0;
    $arr = [];
    $result = '';
    $page = 0;

    if (!isset($_POST['search']) || !is_string($_POST['search'])) $_POST['search'] = '';


    function checkFinish() // DONE
    {
        $result = '`is_hidden` = 0 AND r.`deleted_at` IS NULL';

        if (isset($_POST['finish']) && $_POST['finish'] == 2) {
            $result .= ' AND `is_completed` = 1';
        }

        return $result;
    }


    function aSearch($db, $page, $sort) // DONE
    {

        $query = $db->query('SELECT count(r.id) as total FROM `releases` as r WHERE ' . checkFinish());
        $total = $query->fetch()['total'];

        $query = $db->query("SELECT r.`id` FROM `releases` as r WHERE " . checkFinish() . " ORDER BY `{$sort}` DESC LIMIT {$page}, 12");
        $data = $query->fetchAll(PDO::FETCH_ASSOC);
        return ['data' => $data, 'total' => $total];
    }

    function bSearch($db, $page, $sort) // DONE
    {

        global $var;

        if (empty($_POST['search'])) {
            return false;
        }

        $search = json_decode($_POST['search'] ?? null, true);

        $years = isset($search['year']) && empty($search['year']) === false ? explode(',', $search['year']) : [];
        $genres = isset($search['genre']) && empty($search['genre']) === false ? explode(',', $search['genre']) : [];
        $seasons = isset($search['season']) && empty($search['season']) === false ? explode(',', $search['season']) : [];

        $sql = 'SELECT 
            r.id
            FROM `releases` as r
            left join `releases_genres` as rg on rg.`releases_id` = r.`id`
            left join `genres` as g on g.`id` = rg.`genres_id`

            WHERE {years} {genres} {seasons} ' . checkFinish() . "
            GROUP BY r.id
            ORDER BY `{$sort}` DESC
        ";
        $arguments = [];

        // Years
        if (count($years) > 0) {
            $sql = str_replace('{years}', " r.year IN (" . str_repeat('?, ', count($years) - 1) . '?' . ") AND ", $sql);
            $arguments = array_merge($arguments, $years);
        }

        // Genres
        if (count($genres) > 0) {
            $sql = str_replace('{genres}', " g.name IN (" . str_repeat('?, ', count($genres) - 1) . '?' . ") AND ", $sql);
            $arguments = array_merge($arguments, $genres);
        }

        // Seasons
        if (count($seasons) > 0) {

            foreach ($seasons as $key => $season) {
                $seasons[$key] = array_search($season, $var['season']);
            }

            $sql = str_replace('{seasons}', " r.season IN (" . str_repeat('?, ', count($seasons) - 1) . '?' . ") AND ", $sql);
            $arguments = array_merge($arguments, $seasons);
        }

        $sql = str_replace('{years}', '', $sql);
        $sql = str_replace('{genres}', '', $sql);
        $sql = str_replace('{seasons}', '', $sql);

        $query = $db->prepare($sql);
        $query->execute($arguments);

        $data = $query->fetchAll(PDO::FETCH_ASSOC);
        $total = count($data);

        /*$query = $sphinx->prepare("SELECT `id` FROM anilibria WHERE MATCH(:search) AND ".checkFinish()." ORDER BY `{$sort}` DESC LIMIT {$page}, 12 OPTION max_matches=2012");
        $query->bindValue(':search', "$s");
        $query->execute();
        */

        return ['data' => array_slice($data ?? [], $page, 12), 'total' => $total ?? 0];
    }

    function cSearch($db, $user, $page) // DONE
    {
        $data = [];
        $total = 0;
        $query = $db->prepare('SELECT count(*) as total FROM `users_favorites` WHERE `users_id` = :uid');
        $query->bindParam(':uid', $user['id']);
        $query->execute();
        $total = $query->fetch()['total'];
        $query = $db->prepare("SELECT `releases_id` AS `rid` FROM `users_favorites` WHERE `users_id` = :uid ORDER BY `releases_id` DESC LIMIT {$page}, 12");
        $query->bindParam(':uid', $user['id']);
        $query->execute();

        while ($row = $query->fetch()) {
            $data[]['id'] = $row['rid'];
        }

        return ['data' => $data, 'total' => $total];
    }


    function prepareSearchResult($data) // DONE
    {
        global $conf;

        $arr = [];
        $i = 0;
        $animeDescription = '<div class="anime_info_wrapper"><span class="anime_name">{runame}</span><span class="anime_number">{series}</span><span class="anime_description">{description}</span></div>';
        $tmplTD = '<td><a href="/release/{id}.html">' . $animeDescription . '<img class="torrent_pic" border="0" src="{img}" width="270" height="390" alt="{alt}" ></a></td>';
        foreach ($data as $key => $val) {

            $releaseId = $val['id'];
            $release = _getReleaseById($releaseId);
            $episodes = _getReleaseEpisodes($releaseId);

            $ordinals = [];
            foreach ($episodes as $episode) $ordinals[] = $episode['ordinal'];

            if ($release) {

                $img = !empty($release['poster'])
                    ? ImageThumbnail::make(sprintf('%s/%s/%s', $conf['release_poster_host'], $release['id'], $release['poster']))->getThumbnail(270, 390)
                    : '/upload/release/270x390/default.jpg';

                $item = $tmplTD;
                $item = str_replace('{img}', $img, $item);
                $item = str_replace('{alt}', "{$release['name']} / {$release['name_english']}", $item);
                $item = str_replace('{id}', $release['alias'], $item);

                $item = str_replace('{series}', count($episodes) > 1 ? ("Серия: " . implode(' — ', [min($ordinals), max($ordinals)])) : '', $item);
                $item = str_replace('{runame}', "{$release['name']}", $item);
                $item = str_replace('{description}', releaseDescriptionByID($val['id'], 199), $item);

                $arr[$i][] = $item;

                if (count($arr[$i]) == 3) {
                    $i++;
                }

            }

        }
        return $arr;
    }

    // Get sort column
    $sort = ['1' => 'fresh_at', '2' => 'rating_by_favorites'];
    $sort = !empty($_POST['sort']) && array_key_exists($_POST['sort'], $sort)
        ? $sort[$_POST['sort']]
        : $sort['1'];


    if (!empty($_POST['page'])) {
        $page = abs(intval($_POST['page']));
        if (empty($page) || $page == 1) {
            $page = 0;
        } else {
            $page = ($page - 1) * 12;
        }
        if ($page > 2000) {
            $page = 2000;
        }
    }

    if (empty($_POST['xpage'])) {
        _message('empty', 'error');
    }

    if ($_POST['xpage'] == 'favorites') {

        if (!$user) {
            _message('access', 'error');
        }

        $arr = cSearch($db, $user, $page);

    } else {

        $arr = bSearch($db, $page, $sort);

        if (!$arr) {
            $arr = aSearch($db, $page, $sort);
        }
    }

    if (!isset($_POST['json'])) {

        $arr['data'] = prepareSearchResult($arr['data']);

        foreach ($arr['data'] as $key => $val) {
            $tmp = '<tr>';
            foreach ($val as $k => $v) {
                $tmp .= $v;
            }
            $tmp .= '</tr>';
            $result .= $tmp;
        }

    } else {
        $result = $arr['data'];
    }

    die(json_encode([
        'err' => 'ok',
        'table' => $result,
        'total' => $arr['total'],
        'update' => md5($arr['total'] . $_POST['search'])
    ]));
}

function isFavorite($uid, $rid) // DONE
{
    global $db;
    $query = $db->prepare('SELECT `id` FROM `users_favorites` WHERE `users_id` = :uid AND `releases_id` = :rid');
    $query->bindParam(':uid', $uid);
    $query->bindParam(':rid', $rid);
    $query->execute();
    if ($query->rowCount() == 0) {
        return false;
    }
    return true;
}

function releaseFavorite() // DONE
{
    global $db, $user;
    if (!$user) {
        _message('access', 'error');
    }
    if (empty($_POST['rid'])) {
        _message('empty', 'error');
    }
    $query = $db->prepare('SELECT `id` FROM `releases` WHERE `id` = :id');
    $query->bindParam(':id', $_POST['rid']);
    $query->execute();
    if ($query->rowCount() != 1) {
        _message('empty', 'error');
    }
    if (!isFavorite($user['id'], $_POST['rid'])) {
        $query = $db->prepare('INSERT INTO `users_favorites` (`users_id`, `releases_id`, `created_at`, `updated_at`) VALUES (:uid, :rid, NOW(), NOW())');
        $query->bindParam(':uid', $user['id']);
        $query->bindParam(':rid', $_POST['rid']);
        $query->execute();
    } else {
        $query = $db->prepare('DELETE FROM `users_favorites` WHERE `users_id` = :uid AND `releases_id` = :rid');
        $query->bindParam(':uid', $user['id']);
        $query->bindParam(':rid', $_POST['rid']);
        $query->execute();
    }
    _message('success');
}

/*function releaseUpdateLast()
{
    global $db, $user, $var;
    if(!$user || $user['access'] < 2){
        _message('access', 'error');
    }
    if(empty($_POST['id'])){
        _message('empty', 'error');
    }
    $query = $db->prepare('SELECT `id`, `name`, `ename`, `code` FROM `xrelease` WHERE `id` = :id');
    $query->bindParam(':id', $_POST['id']);
    $query->execute();
    if($query->rowCount() == 0){
        _message('wrongRelease', 'error');
    }
    $row = $query->fetch();
    pushFcm("{$row['name']} / {$row['ename']}", $row['code']);
    pushAll("{$row['ename']} / {$row['name']}", $row['code']);
    $query = $db->prepare('UPDATE `xrelease` SET `last` = :time WHERE `id` = :id');
    $query->bindParam(':time', $var['time']);
    $query->bindParam(':id', $_POST['id']);
    $query->execute();
    APIv2_UpdateTitle($_POST['id']);
    _message('success');
}*/

function showSchedule() // DONE
{
    global $db, $var, $conf;
    $arr = [];
    $result = '';
    $i = 0;
    $tmpl1 = '<div class="day">{day}</div>';
    $descTPL = '<div class="schedule-anime-desc"><span class="schedule-runame">{runame}</span><span class="schedule-series">Серия: {series}</span><span class="schedule-description">{description}</span></div>';
    $tmpl2 = '<td class="goodcell"><a href="/release/{id}.html">' . $descTPL . '<img width="200" height="280" src="{img}" alt="{alt}"}></a></td>';

    foreach ($var['day'] as $key => $val) {

        $query = $db->prepare('
            SELECT 
               `id`, 
               `name`, 
               `name_english` AS `ename`, 
               IF(`poster` IS NOT NULL, CONCAT("' . $conf['release_poster_host'] . '/", `id`, "/", `poster`), NULL) as `poster` 
            FROM `releases` 
            WHERE `publish_day` = :day AND `is_ongoing` = 1 AND `is_hidden` = 0 AND `deleted_at` IS NULL
        ');

        $query->bindParam(':day', $key);
        $query->execute();

        while ($row = $query->fetch()) {

            //$poster = $_SERVER['DOCUMENT_ROOT']."/upload/release/200x280/{$row['id']}.jpg";
            //$poster = sprintf('%s/%s/%s', $conf['release_poster_host'], $row['id'], $row['poster']);

            $img = !empty($row['poster'])
                ? ImageThumbnail::make($row['poster'])->getThumbnail(200, 280)
                : '/upload/release/240x350/default.jpg';

            $arr["$key"][$i][] = [
                str_replace('{alt}', "{$row['name']} / {$row['ename']}",
                    str_replace('{id}', releaseCodeByID($row['id']),
                        str_replace('{img}', $img,
                            str_replace('{runame}', "{$row['name']}",
                                str_replace('{series}', releaseSeriesByID($row['id']),
                                    str_replace('{description}', releaseDescriptionByID($row['id'], 99), $tmpl2)
                                )
                            )
                        )
                    )
                )
            ];

            if (count($arr["$key"][$i]) == 4) {
                $i++;
            }
        }
    }
    foreach ($arr as $key => $val) {
        $result .= str_replace('{day}', mb_strtoupper($var['day']["$key"]), $tmpl1);
        $result .= '<table class="test"><tbody>';
        foreach ($val as $v) {
            $result .= '<tr>';
            foreach ($v as $item) {
                $result .= $item['0'];
            }
            $result .= '</tr>';
        }
        $result .= '</tbody></table>';
    }
    return $result;
}

/*function countRating()
{
    global $db;
    $query = $db->query('SELECT `id` FROM `releases`');
    while ($row = $query->fetch()) {
        countRatingRelease($row['id']);
    }
}*/

/*function countRatingRelease($rid)
{
    global $db;
    $tmp = $db->prepare('SELECT count(`id`) as total FROM `users_favorites` WHERE `releases_id` = :rid');
    $tmp->bindParam(':rid', $rid);
    $tmp->execute();
    $count = $tmp->fetch()['total'];
    if($count > 0){
        $tmp = $db->prepare('UPDATE `releases` SET `rating_by_favorites` = :rating WHERE `id` = :id');
        $tmp->bindParam(':rating', $count);
        $tmp->bindParam(':id', $rid);
        $tmp->execute();
    }
    return $count;
}*/

/*function sendHH()
{
    global $var;
    testRecaptcha();
    $result = '';
    $info = [
        'rPosition' => 'Заявка',
        'rName' => 'Имя',
        'rNickname' => 'Никнейм/ творческий псевдоним',
        'rAge' => 'Возраст',
        'rCity' => 'Город',
        'rEmail' => 'Почта',
        'rTelegram' => 'Телеграм',
        'rAbout' => 'Немного о себе',
        'rWhy' => 'Почему вы выбрали именно наш проект',
        'rWhere' => 'На каких проектах были, причина ухода',
        'techTask' => 'Ссылка на выполненное задание',
        'voiceAge' => 'Сколько лет занимаетесь озвучкой',
        'voiceEquip' => 'Модель микрофона и звуковой карты',
        'voiceExample' => 'Ссылка на пример озвучки',
        'voiceTiming' => 'Умеете ли вы сами таймить и сводить звук',
        'subExp' => 'Опыт работы с субтитрами',
        'subPosition' => 'Какую должность вы хотите занимать? (Переводчик / Оформитель)'
    ];
    $position = ['1' => 'Технарь', '2' => 'Войсер', '3' => 'Саббер', '4' => 'Сидер', '5' => 'Пиарщик'];
    $filter = ['1' => ['techTask'], '2' => ['voiceAge', 'voiceEquip', 'voiceExample', 'voiceTiming'], '3' => ['subExp', 'subPosition']];
    if (empty($_POST['info'])) {
        _message('empty', 'error');
    }
    $arr = json_decode($_POST['info'], true);
    if (empty($arr['rPosition'])) {
        _message('empty', 'error');
    }
    if (array_key_exists($arr['rPosition'], $filter)) {
        foreach ($filter[$arr['rPosition']] as $val) {
            if (empty($arr["$val"])) {
                _message('empty', 'error');
            }
        }
    }
    foreach ($filter as $key => $val) {
        if ($arr['rPosition'] != $key) {
            foreach ($val as $v) {
                unset($arr["$v"]);
            }
        }
    }
    foreach ($arr as $key => $val) {
        if (empty($val)) {
            _message('empty', 'error');
        }
        if (mb_strlen($val) > 600) {
            _message('long', 'error');
        }
        if (!array_key_exists($key, $info)) {
            _message('wrong', 'error');
        }

        if ($key == 'rPosition') {
            $val = $position[$arr['rPosition']];
        }
        $result .= "<b>{$info["$key"]}:</b><br/>";
        $result .= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . "<br/><br/>";
    }
    $title = genRandStr(8, 1);
    _mail('anilibriahh@protonmail.com', "{$position[$arr['rPosition']]} новая заявка [$title]", $result);
    _mail('lupin@anilibria.tv', "{$position[$arr['rPosition']]} новая заявка [$title]", $result);
    _message('success');
}*/

/*function urlCode($id, $ename)
{
    global $db;
    $result = '';
    $code = preg_replace('/[^a-z0-9-]/', '', str_replace(" ", "-", mb_strtolower(preg_replace('/\s+/u', " ", trim($ename)))));
    function updateUrlCode($db, $code, $id){
        $query = $db->prepare('UPDATE `xrelease` SET `code` = :code WHERE `id` = :id');
        $query->bindParam(':code', $code);
        $query->bindParam(':id', $id);
        $query->execute();
        return $code;
    }
    $query = $db->prepare('SELECT `code` FROM `xrelease` WHERE `code` = :code');
    $query->bindParam(':code', $code);
    $query->execute();
    if($query->rowCount() != 0 || empty($code)){
        $code = rand(1000000, 10000000);
    }
    $query = $db->prepare('SELECT `code` FROM `xrelease` WHERE `id` = :id');
    $query->bindParam(':id', $id);
    $query->execute();
    $row = $query->fetch();
    $result = $row['code'];
    if(empty($row['code'])){
        $result = updateUrlCode($db, $code, $id);
    }elseif(!ctype_digit($code) && ctype_digit($row['code'])){
        $result = updateUrlCode($db, $code, $id);
    }
    return "/release/$result.html";
}*/

function catalogYear() // DONE
{
    global $db, $sphinx, $cache, $var;

    $options = "";
    $query = $db->prepare('SELECT `year` FROM `releases` WHERE `is_hidden` = 0 AND `deleted_at` IS NULL GROUP BY `year` ORDER BY `year` DESC');
    $query->execute();
    $years = $query->fetchAll();

    foreach ($years as $year) {
        $options .= "<option value='{$year['year']}'>{$year['year']}</option>";
    }

    return $options;
}

/*function pushFcm($name, $code)
{
     global $conf;
     $link = 'https://www.anilibria.tv/release/' . $code . '.html';
     $title = 'Вышла новая серия!';
     $body = $name;

     $reqBody = [
         'to' => '/topics/all',
         'content_available' => true,
         'priority' => 'high',
         'notification' => [
             'body' => $body,
             'title' => $title,
             'link' => $link,
             'sound' => 'default'
         ],
         'data' => [
             'body' => $body,
             'title' => $title,
             'link' => $link,
             'sound' => 'default'
         ]
     ];

     $curl = curl_init();
     curl_setopt_array($curl, array(
         CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
         CURLOPT_CUSTOMREQUEST => "POST",
         CURLOPT_POSTFIELDS => json_encode($reqBody, JSON_UNESCAPED_UNICODE),
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_HTTPHEADER => array(
             "Authorization: " . $conf["fcm_token"],
             "Content-Type: application/json"
         )
     ));
     $response = curl_exec($curl);
     curl_close($curl);
}*/

/*function pushAll($name, $code)
{
      global $conf;
      $url = 'https://www.anilibria.tv/release/' . $code . '.html';
      function sendPush($api, $data)
      {
          curl_setopt_array($ch = curl_init(), [
              CURLOPT_URL => $api,
              CURLOPT_POSTFIELDS => $data,
              CURLOPT_RETURNTRANSFER => true
          ]);
          curl_exec($ch);
          curl_close($ch);
      }

      sendPush('https://pushall.ru/api.php', [
          'type' => 'broadcast',
          'id' => '943',
          'key' => $conf['push_all'],
          'text' => $name,
          'title' => '[AnilibriaTV] новая серия!',
          'url' => $url
      ]);
      sendPush('http://sanasol-test.ru/librbot/hook.php', [
          'key' => $conf['push_sanasol'],
          'text' => $name,
          'title' => 'Вышла новая серия!',
          'url' => $url
      ]);
      sendPush('https://bot.libria.fun/hook.php', [
          'token' => $conf['push_albot'],
          'text' => $name,
          'title' => 'Вышла новая серия!',
          'url' => $url
      ]);
}*/

/*function telegram_send($chanel, $msg)
{
    global $conf;
    $arr = [
        'chat_id' => $chanel,
        'text' => $msg
    ];
    file_get_contents("https://api.telegram.org/bot{$conf['telegram']}/sendMessage?" . http_build_query($arr));
}*/

/*function helpSeed()
{
    global $db;
    $url = false;
    $query = $db->query('SELECT `releases_id` AS `rid` FROM `torrents` WHERE `seeders` < 10 ORDER BY `seeders` DESC LIMIT 10');
    while ($row = $query->fetch()) {
        $tmp = $db->prepare('SELECT `alias` AS `code` FROM `releases` WHERE `id` = :id AND `is_hidden` = 0 and `deleted_at` IS NULL');
        $tmp->bindParam(':id', $row['rid']);
        $tmp->execute();
        if ($tmp->rowCount() == 1) {
            $url = 'https://www.anilibria.tv/release/' . $tmp->fetch()['code'] . '.html';
            break;
        }
    }
    if ($url) {
        telegram_send('@anilibriahelpseed', $url);
    }
}*/

function showAscReleases() // DONE
{
    global $db, $var, $cache, $conf;
    $arr = [];
    $result = '';
    $links = '';
    $i = 0;
    $chars = [];
    function isRussian($text)
    {
        return preg_match('/[\p{Cyrillic}]/u', $text);
    }

    function sortA($a, $b)
    {
        if (isRussian($a)) {
            if (isRussian($b)) {
                return ($a < $b) ? -1 : 1;
            } else {
                return -1;
            }
        }
        return ($a < $b) ? -1 : 1;
    }

    $descTPL = '<div class="schedule-anime-desc"><span class="schedule-runame">{runame}</span><span class="schedule-series">Серия: {series}</span><span class="schedule-description">{description}</span></div>';
    $tmpl2 = '<td class="goodcell"><a href="/release/{id}.html">' . $descTPL . '<img width="200" height="280" data-src="{img}" alt="{alt}" class="lazy"></a></td>';
    // $result = $cache->get('showAscReleases');
    // if ($result === false) {
    $query = $db->query('
            SELECT 
               `id`,
               `name`, `name_english` AS `ename`, 
               NULL AS `voice`, 
               `alias` AS `code`, 
               IF(`poster` IS NOT NULL, CONCAT("' . $conf['release_poster_host'] . '/", `id`, "/", `poster`), NULL) as `poster`
               
            FROM `releases` 
            WHERE `is_hidden` = 0 and `deleted_at` IS NULL
            ORDER BY `name` ASC
        ');
    $query->bindParam(':day', $key);
    $query->execute();
    while ($row = $query->fetch()) {

        /*$img = empty($row['poster'])
            ? urlCDN('/upload/release/240x350/default.jpg')
            : sprintf('%s/%s/%s', $conf['release_poster_host'], $row['id'], $row['poster']);*/

        $img = !empty($row['poster'])
            ? ImageThumbnail::make($row['poster'])->getThumbnail(240, 350)
            : '/upload/release/240x350/default.jpg';

        //$poster = $_SERVER['DOCUMENT_ROOT'] . "/upload/release/200x280/{$row['id']}.jpg";


        //if (!file_exists($poster)) {
        //     $img = urlCDN('/upload/release/200x280/default.jpg');
        // } else {
        //     $img = urlCDN(fileTime($poster));
        // }

        $key = mb_strtoupper(mb_substr($row['name'], 0, 1, "utf-8"));

        if (!in_array($key, $chars)) {
            $chars[$key] = $key;
        }
        $arr["$key"][$i][] = [
            str_replace('{alt}', "{$row['name']} / {$row['ename']}", str_replace('{id}', releaseCodeByID($row['id']), str_replace('{img}', $img, str_replace('{runame}', "{$row['name']}", str_replace('{series}', releaseSeriesByID($row['id']), str_replace('{description}', releaseDescriptionByID($row['id'], 99), $tmpl2))))))
        ];
        if (count($arr["$key"][$i]) == 4) {
            $i++;
        }
    }
    uksort($chars, "sortA");
    uksort($arr, "sortA");
    foreach ($chars as $val) {
        $links .= "<a href=\"#$val\">$val</a>";
    }
    $result .= "<div id=\"alphabet-characters\" style=\"margin-top: 5px\">$links</div>";
    foreach ($arr as $key => $val) {
        $result .= "<div class=\"day\"><span id=\"$key\">$key</span> <a class=\"alphabet-up\" href=\"#headercontent\" style=\"margin-top: 5px; margin-right: 5px;\"></a></div>";
        foreach ($val as $v) {
            $result .= '<table class="test"><tbody>';
            $result .= '<tr>';
            foreach ($v as $item) {
                $result .= $item['0'];
            }
            $result .= '</tr>';
            $result .= '</tbody></table>';
        }
    }

    //   $cache->set('showAscReleases', $result, 86400);
    // }

    return $result;
}

function changeADS() // DONE
{
    global $db, $user, $var, $conf;

    if (!$user) {
        _message('unauthorized', 'error');
    }

    if ($user['ads'] == 1) {
        $ads = 0;

    } else {
        $ads = 1;
    }

    $query = $db->prepare('UPDATE `users` SET `show_ads` = :ads WHERE `id` = :id');
    $query->bindParam(':ads', $ads);
    $query->bindParam(':id', $user['id']);
    $query->execute();

    _message('success');
}

function checkADS() // DONE
{
    global $user;
    if (!$user || $user['ads'] == 1) {
        return true;
    } else {
        return false;
    }
}

function randomRelease() // DONE
{
    global $db, $cache;
    $arr = [];


    $numberOfChunks = $cache->get('infiniteApiInfo');
    $releasesOfChunk = $cache->get(sprintf('infiniteApiInfo%s', rand(1, $numberOfChunks)));
    $releasesOfChunk = array_values(json_decode($releasesOfChunk, true));

    $randomRelease = array_values($releasesOfChunk)[rand(0, count($releasesOfChunk) - 1)];

    return $randomRelease['code'] ?? 'tate-no-yuusha-no-nariagari';
}

/*function updateGenreRating()
{
    global $db, $sphinx;
    $data = [];
    $query = $db->query('SELECT * FROM `genre`');
    while($row = $query->fetch()){
        $select = $sphinx->prepare('SELECT `id` FROM anilibria WHERE MATCH(:search) AND `status` != 3 LIMIT 1000');
        $select->bindValue(':search', "@(genre) ({$row['name']})");
        $select->execute();
        while($tmp = $select->fetch()){
            $release = $db->prepare('SELECT `rating` FROM `xrelease` WHERE `id` = :id');
            $release->bindParam(':id', $tmp['id']);
            $release->execute();
            $data["{$row['id']}"] += $release->fetch()['rating'];
        }
        $update = $db->prepare('UPDATE `genre` SET `rating` = :rating WHERE `id` = :id');
        $update->bindParam(':rating', $data["{$row['id']}"]);
        $update->bindParam(':id', $row['id']);
        $update->execute();
    }
}*/

function showSitemap() // DONE
{
    global $db;
    $release = '';
    $query = $db->query('SELECT `alias` AS `code` FROM `releases` WHERE `is_hidden` = 0 and `deleted_at` IS NULL');
    while ($row = $query->fetch()) {
        $release .= "\t<url><loc>https://www.anilibria.tv/release/{$row['code']}.html</loc></url>\n";
    }
    $result = str_replace('{release}', rtrim($release), getTemplate('sitemap'));
    file_put_contents('/var/www/html/sitemap.xml', $result);
}

function checkIfVoted($rid) // DONE
{
    global $db, $user;
    $svg = 'heart-regular.svg';
    $img = "<img id='$rid' src='/img/other/{svg}' width='20px' height='20px'>";
    if ($user) {
        $query = $db->prepare('SELECT * FROM `users_favorites` WHERE `releases_id` = :rid AND `users_id` = :uid');
        $query->bindParam(':rid', $rid);
        $query->bindParam(':uid', $user['id']);
        $query->execute();
        if ($query->rowCount() == 1) {
            $svg = 'heart-solid.svg';
        }
    }
    $img = str_replace('{svg}', $svg, $img);
    return "<a href='' data-release-favorites='$rid' class='upcoming_season_like'>БУДУ СМОТРЕТЬ $img</a>";
}

function showNewSeason() // DONE
{

    global $db, $user, $var, $conf;
    $result = '';
    $order = '';
    $video = '';

    if (empty($_GET['year']) || !ctype_digit($_GET['year']) || empty($_GET['season']) || !array_key_exists($_GET['season'], $var['season'])) {
        return release404();
    }

    $season = $_GET['season']; //$var['season'][$_GET['season']];
    $year = $_GET['year'];

    $query = $db->prepare('SELECT 
       `id`, 
       `name`, 
       `name_english` AS `ename`, 
        (
           select GROUP_CONCAT(g.name ORDER BY g.name SEPARATOR ", " ) from genres as g
           inner join releases_genres as rg on rg.genres_id = g.id 
           where rg.releases_id = releases.id
       ) as `genre`,
       `season`,
       `description`, 
       `rating_by_favorites` AS `rating`, 
       `alias` AS `code`,
        IF(`poster` IS NOT NULL, CONCAT("' . $conf['release_poster_host'] . '/", `id`, "/", `poster`), NULL) as `poster`
       
        FROM `releases` 
        WHERE `year` = :year AND `season` = :season AND `is_hidden` = 0 AND `deleted_at` IS NULL 
        ORDER BY `rating_by_favorites` DESC'
    );
    $query->bindParam(':year', $year);
    $query->bindParam(':season', $season);
    $query->execute();
    if ($query->rowCount() == 0) {
        return release404();
    }
    lowerMove();
    while ($row = $query->fetch()) {

        /*$img = empty($row['poster'])
            ? urlCDN('/upload/release/270x390/default.jpg')
            : sprintf('%s/%s/%s', $conf['release_poster_host'], $row['id'], $row['poster']);*/

        $img = !empty($row['poster'])
            ? ImageThumbnail::make($row['poster'])->getThumbnail(270, 390)
            : '/upload/release/270x390/default.jpg';

        $tmp = getTemplate('season-vote');
        $tmp = str_replace('{id}', $row['id'], $tmp);
        $tmp = str_replace('{name}', $row['name'], $tmp);
        $tmp = str_replace('{ename}', $row['ename'], $tmp);
        $tmp = str_replace('{genres}', $row['genre'], $tmp);
        $tmp = str_replace('{season}', $var['season'][$row['season']], $tmp);
        $tmp = str_replace('{description}', $row['description'], $tmp);
        $tmp = str_replace('{votes}', $row['rating'], $tmp);
        $tmp = str_replace('{img}', $img, $tmp);
        $tmp = str_replace('{code}', $row['code'], $tmp);
        $tmp = str_replace('{voteBtn}', checkIfVoted($row['id']), $tmp);
        $result .= $tmp;
    }
    $tmpl = '<div style="border-radius: 4px; overflow: hidden; z-index: 1; width: 832px; height: 468px; margin-top: 10px; margin-left: 4px;"><iframe width="832" height="468" src="https://www.youtube.com/embed/{vid}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div><hr/>';
    $key = $year . $_GET['season'];
    if (array_key_exists($key, $var['youtube'])) {
        $video = str_replace('{vid}', $var['youtube']["$key"], $tmpl);
    }
    $var['title'] = "Аниме сезон $year $season";
    return "<div class='news-block'>$video<div>$result</div><div class='clear'></div><div style='margin-top:10px;'></div></div>";
}

function urlCDN($url) // DONE
{
    return $url;

    /*global $conf;
    if ($conf['cdn']) {
        return 'https://anilibria.tv' . $url;
    }
    return $url;*/

}

function sendReleaseReport() // DONE
{
    global $var;
    if (empty($_POST['mes']) || empty($_POST['url']) || mb_strlen($_POST['mes']) > 250) {
        _message('empty', 'error');
    }
    testRecaptcha();
    $title = genRandStr(8, 1);
    $url = 'https://www.anilibria.tv' . htmlspecialchars($_POST['url'], ENT_QUOTES, 'UTF-8');
    $report = htmlspecialchars($_POST['mes'], ENT_QUOTES, 'UTF-8');
    _mail('anilibria@protonmail.com', "Сообщение об ошибке [$title]", "Запрос отправили с IP {$var['ip']}<br/><br/>$url<br/><br/>{$var['user_agent']}<br/><br/>$report");
    _mail('lupin@anilibria.tv', "Сообщение об ошибке [$title]", "Запрос отправили с IP {$var['ip']}<br/><br/>$url<br/><br/>{$var['user_agent']}<br/><br/>$report");
    _message('success');
}

function iframePlayer() // DONE
{
    if (empty($_GET['id']) || !ctype_digit($_GET['id'])) {
        _message('empty', 'error');
    }
    $playList = getReleaseVideo($_GET['id']);
    if ($playList) {
        $result = str_replace('{playerjs}', urlCDN(fileHash('/js/player.js')), getTemplate('playerjs'));
        $result = str_replace('{deny}', adsUrl(), $result);
        $result = str_replace('{playlist}', $playList, $result);
        return ['id' => $_GET['id'], 'result' => $result];
    }
    return '';
}

function APIv2_UpdateTitle($releaseId) // DONE
{
    global $conf;

    if ($conf['api_v2'] && $releaseId > 0) {
        $context = stream_context_create(["http" => ["method" => "GET", "timeout" => .01]]);
        file_get_contents("{$conf['api_v2']}/webhook/updateTitle?id={$releaseId}", 0, $context);
    }
}

function getTelegramActionLink($platform, $action, $payload) // DONE
{
    return "tg://resolve?domain=anilibria_bot&start=_" . rtrim(strtr(base64_encode("{$platform}|{$action}_{$payload}"), '+/', '-_'), '=');
}

function _getReleaseById($releaseId) // DONE
{
    global $db, $user;

    $query = $db->prepare('SELECT * FROM `releases` WHERE `id` = :id AND (`is_hidden` = 0 OR :userHasRoles) AND `deleted_at` IS NULL');

    $userHasRoles = $user && $user['has_roles'] === true ? 1 : 0;

    $query->bindParam(':id', $releaseId);
    $query->bindParam(':userHasRoles', $userHasRoles);

    $query->execute();

    return $query->fetch();
}

function _getReleaseEpisodes($releaseId) // DONE
{
    global $db;

    $query = $db->prepare('SELECT * from `releases_episodes` where
        `releases_id` = :id and `is_visible` = 1 and `deleted_at` IS NULL AND
        (`hls_480` IS NOT NULL OR `hls_720` IS NOT NULL OR `hls_1080` IS NOT NULL) 
        ORDER BY `sort_order` ASC');
    $query->bindValue(':id', $releaseId);
    $query->execute();
    return $query->fetchAll();
}


function _getReleaseEpisodesFromRutube($releaseId = null): array
{
    if ($releaseId) {

        global $db;

        $query = $db->prepare('SELECT * from `releases_episodes` where `releases_id` = :id and `is_visible` = 1 and `deleted_at` IS NULL AND `rutube_id` IS NOT NULL ORDER BY `sort_order` ASC');
        $query->bindValue(':id', $releaseId);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    return [];
}


function _getHostname(): string // DONE
{
    $host = $_SERVER['HTTP_HOST'];
    $scheme = $_SERVER['REQUEST_SCHEME'];

    return $host && $scheme
        ? sprintf('%s://%s', $scheme, $host)
        : '';
}

function _getLatestUpdatedReleases(int $secondsOffset = 0): array
{
    global $db;
    $query = $db->prepare('SELECT id FROM `releases` WHERE `fresh_at` >= DATE_SUB(NOW(), INTERVAL :seconds SECOND) AND `is_hidden` = 0 AND `deleted_at` IS NULL');
    $query->bindParam(':seconds', $secondsOffset);
    $query->execute();

    return $query->fetchAll(PDO::FETCH_ASSOC);

}


/**
 * Get full releases data
 * Convert to legacy db structure format
 *
 * @param null $releasesId
 * @return array
 */
function _getFullReleasesDataInLegacyStructure($releasesId = null): array
{
    global $db, $conf, $user, $var;

    $sql = '
        SELECT 
            r.`id`, 
            r.`name`, 
            r.`name_english` AS `ename`,
            r.`name_alternative` AS `aname`, 
            r.`year`,
            r.`season`,
            CASE r.`season` WHEN "winter" THEN "зима" WHEN "spring" THEN "весна" WHEN "summer" THEN "лето" WHEN "autumn" THEN "осень" END AS `season`,
            r.`type`,
            r.`episodes_total`,
            r.`episodes_are_unknown`,
            r.`duration`,
            GROUP_CONCAT(DISTINCT TRIM(g.`name`) ORDER BY g.`name` SEPARATOR ", " ) AS `genre`,
            GROUP_CONCAT(DISTINCT IF(rm.`role` = "voicing", TRIM(IF(rm.nickname IS NOT NULL, rm.nickname, rmu.nickname)), NULL) SEPARATOR ", ")  as `voice`,
            GROUP_CONCAT(DISTINCT IF(rm.`role` = "translating", TRIM(IF(rm.nickname IS NOT NULL, rm.nickname, rmu.nickname)), NULL) SEPARATOR ", ")  as `translator`,
            GROUP_CONCAT(DISTINCT IF(rm.`role` = "editing", TRIM(IF(rm.nickname IS NOT NULL, rm.nickname, rmu.nickname)), NULL) SEPARATOR ", ")  as `editing`,
            GROUP_CONCAT(DISTINCT IF(rm.`role` = "decorating", TRIM(IF(rm.nickname IS NOT NULL, rm.nickname, rmu.nickname)), NULL) SEPARATOR ", ")  as `decor`,
            GROUP_CONCAT(DISTINCT IF(rm.`role` = "timing", TRIM(IF(rm.nickname IS NOT NULL, rm.nickname, rmu.nickname)), NULL) SEPARATOR ", ")  as `timing`,
            r.`notification` AS `announce`,
            IF(r.`is_hidden` = 1, 3, IF(r.`is_ongoing` = 1, 1, IF(r.`is_completed` = 1, 2, 0))) AS `status`,
            "Скрыт" as `search_status`,
            TRIM(r.`external_player`) AS `moonplayer`,
            r.`description`,
            UNIX_TIMESTAMP(r.`fresh_at`) AS `last`,
            UNIX_TIMESTAMP(r.`updated_at`) AS `last_change`,
            r.`publish_day` AS `day`,
            r.`rating_by_favorites` as `rating`,
            r.`alias` AS `code`,
            NULL AS `block`,
            r.`is_wakanim` AS `bakanim`,
            IF(r.`poster` IS NOT NULL, CONCAT("' . $conf['release_poster_host'] . '/", r.`id`, "/", r.`poster`), NULL) as `poster`,
            EXISTS(SELECT id FROM `releases_episodes` WHERE  `releases_id` = r.`id` AND `deleted_at` IS NULL AND `is_visible` = 1 AND COALESCE(`hls_480`, `hls_720`, `hls_1080`) IS NOT NULL) as `has_episodes`,
            EXISTS(SELECT id FROM `releases_episodes` WHERE `releases_id` = r.`id` AND `deleted_at` IS NULL AND `is_visible` = 1 AND `rutube_id` IS NOT NULL ) as `has_rutube_episodes`
               
            
        FROM `releases` AS r
       
        LEFT JOIN `releases_genres` AS rg ON rg.`releases_id` = r.`id`
        LEFT JOIN `genres` AS g ON g.`id` = rg.`genres_id`
        
        LEFT JOIN `releases_members` AS rm ON rm.`releases_id` = r.`id` AND rm.`deleted_at` IS NULL
        LEFT JOIN `users` AS rmu ON rmu.`id` = rm.`users_id`
        
        WHERE (r.`is_hidden` = 0 OR :userHasRoles) AND r.`deleted_at` IS NULL :releasesPlaceholders
        
        GROUP BY r.`id`
        ORDER BY r.`fresh_at` DESC
    ';


    // Check if releases are provided
    // Create releases placeholders for all releases in provided array
    $hasReleasesFilter = count($releasesId ?? []) > 0;
    $releasesPlaceholders = $hasReleasesFilter
        ? sprintf('AND r.id IN(%s)', str_repeat('?,', count($releasesId ?? []) - 1) . '?')
        : '';

    // Replace releasesPlaceholders with part of sql query (or empty)
    $sql = str_replace(':userHasRoles', $user && $user['has_roles'] === true ? 1 : 0, $sql);
    $sql = str_replace(':releasesPlaceholders', $releasesPlaceholders, $sql);

    $query = $db->prepare($sql);
    $query->execute($hasReleasesFilter ? $releasesId : null);

    $isSingleRelease = count($releasesId ?? []) === 1;

    // Can fetch all/multiple/single release
    $releases = $isSingleRelease
        ? [$query->fetch(PDO::FETCH_ASSOC)]
        : $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($releases as $index => $release) {

        $type = $release['type'] ?? null;
        $type = $type ? ($var['types'][$type] ?? null) : null;

        $typeHuman = "";
        if ($type) $typeHuman .= sprintf('%s', $type);
        if ($release['episodes_total']) $typeHuman .= sprintf(' (%s%s эп.)', $release['episodes_are_unknown'] ? '>' : '', $release['episodes_total']);
        if ($release['duration']) $typeHuman .= sprintf(', %s мин.', $release['duration']);

        $posterSrc = !empty($release['poster']) ? ImageThumbnail::make($release['poster'])->getThumbnail(350, 500) : null;
        $posterThumbnail = $posterSrc ? ImageThumbnail::make($release['poster'])->getThumbnail(null, 150, 50) : null;

        $releases[$index] = array_merge($release, [
            'id' => (int)$release['id'],
            'day' => (int)$release['day'],
            'year' => (int)$release['year'],
            'type' => empty(trim($typeHuman)) === false ? trim($typeHuman) : null,
            'last' => (int)$release['last'],
            'status' => (int)$release['status'],
            'rating' => (int)$release['rating'],
            'poster' => $posterSrc ?? '/upload/release/350x500/default.jpg',
            'bakanim' => (int)$release['bakanim'],
            'preview' => [
                'src' => $posterSrc ?? null,
                'thumbnail' => $posterThumbnail ?? null,
            ],
            'duration' => (float)$release['duration'],
            'type_value' => $release['type'] ?? null,
            'last_change' => (int)$release['last_change'],
            'has_episodes' => (int)$release['has_episodes'],
            'episodes_total' => $release['episodes_total'] ? (int)$release['episodes_total'] : null,
            'has_rutube_episodes' => (int)$release['has_rutube_episodes'] === 1,
            'episodes_are_unknown' => (int)$release['episodes_are_unknown'] === 1,

        ]);
    }

    return $isSingleRelease ? $releases[0] : $releases;

}


/**
 * Get release by column
 *
 * @param string $column
 * @param null $value
 * @return array|null
 */
function _getReleaseByColumn(string $column, $value = null): ?array
{
    global $db, $user;

    $sql = 'SELECT `id` FROM `releases` WHERE `%s` = :value AND (`is_hidden` = 0 OR :userHasRoles) AND `deleted_at` IS NULL';

    $userHasRoles = $user && $user['has_roles'] === true ? 1 : 0;

    $query = $db->prepare(sprintf($sql, $column));
    $query->bindParam('value', $value);
    $query->bindParam('userHasRoles', $userHasRoles);

    $query->execute();
    $release = $query->fetch(PDO::FETCH_ASSOC);

    // Check if release is found in DB
    $hasRelease = isset($release['id']);

    return $hasRelease ? _getFullReleasesDataInLegacyStructure([$release['id']]) : null;

}

/**
 * Get release franchises
 *
 * @param $releaseId
 * @return array
 */
function _getReleaseFranchises($releaseId): array
{
    if ($releaseId) {

        global $db;

        $sql = 'SELECT
        
            `r`.`id` as `release_id`,
            `f`.`id` AS `franchise_id`,
            `r`.`name` AS `release_name`,
            `f`.`name` AS `franchise_name`,
            `r`.`alias` AS `release_alias`,
            `fr2`.`sort_order` AS `release_ordinal`
    
            FROM `franchises_releases` AS `fr1`
            INNER JOIN `franchises` AS `f` ON `f`.`id` = `fr1`.`franchise_id`
            INNER JOIN `franchises_releases` AS `fr2` ON `fr2`.`franchise_id` = `f`.`id`
            INNER JOIN `releases` AS `r` ON `r`.`id` = `fr2`.`release_id`
            WHERE `fr1`.`release_id` = :releaseId
            ORDER BY `fr2`.`sort_order` ASC
        ';

        $query = $db->prepare($sql);
        $query->bindParam('releaseId', $releaseId);
        $query->execute();

        $response = $query->fetchAll(PDO::FETCH_ASSOC);

        $franchisesReleases = [];

        foreach ($response as $franchiseRelease) {
            $franchisesReleases[$franchiseRelease['franchise_id']][] = [
                'franchise' => [
                    'id' => $franchiseRelease['franchise_id'],
                    'name' => $franchiseRelease['franchise_name']
                ],
                'release' => [
                    'id' => $franchiseRelease['release_id'],
                    'name' => $franchiseRelease['release_name'],
                    'alias' => $franchiseRelease['release_alias'],
                    'ordinal' => $franchiseRelease['release_ordinal'],
                ]
            ];
        }

        $franchises = [];
        foreach ($franchisesReleases as $franchise) {
            foreach ($franchise as $franchiseRelease) {
                $franchise =  $franchiseRelease['franchise'];
                $franchises[$franchise['id']]['franchise'] = $franchise;
                $franchises[$franchise['id']]['releases'] = $franchises[$franchise['id']]['releases'] ?? [];
                $franchises[$franchise['id']]['releases'][] = $franchiseRelease['release'];
            }
        }

        return array_values($franchises);
    }

    return [];
}


/**
 * @param array $franchises
 * @param $releaseId
 * @return string|null
 */
function _getReleasesFranchisesAsHtml(array $franchises, $releaseId): ?string
{
    $html = "";

    foreach ($franchises as $franchise) {

        // Franchise
        $html .= sprintf('<div class="release-franchise-title">Порядок просмотра франшизы "%s":</div>', $franchise['franchise']['name']);

        // Releases
        foreach ($franchise['releases'] ?? [] as $release) {
            if ($releaseId != $release['id']) {
                $html .= sprintf('<div class="release-franchise">#%s <a href="/release/%s.html" target="_self">%s</a></div>', $release['ordinal'], $release['alias'], $release['name']);
            } else {
                $html .= sprintf('<div class="release-franchise active">#%s %s</div>', $release['ordinal'], $release['name']);
            }
        }
    }

    return $html;

}


/**
 * @param array $franchises
 * @param $releaseId
 * @return string|null
 */
function _getReleasesFranchisesAsHtmlLinks(array $franchises, $releaseId): ?string
{
    $html = "";

    foreach ($franchises as $franchise) {
        $html .= sprintf('Порядок просмотра франшизы "%s":', $franchise['franchise']['name']);

        foreach ($franchise['releases'] ?? [] as $release) {
            $html .= "<br>";
            if ($releaseId != $release['id']) {
                $html .= sprintf('#%s <a href="https://anilibria.tv/release/%s.html" target="_blank">%s</a>', $release['ordinal'], $release['alias'], $release['name']);
            } else {
                $html .= sprintf('#%s %s', $release['ordinal'], $release['name']);
            }
        }
    }

    return $html;

}


function getUserAvatarUrl($user_id, $filename)
{
    global $conf;
    return sprintf('%s/%s/%s/%s', $conf['users_avatars_host'], floor($user_id / 100), $user_id, $filename);
}
