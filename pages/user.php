<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/header.php');

function user_page(){
	global $var;
	if(!empty($_GET['id']) && ctype_digit($_GET['id'])){
		$profile = show_profile($_GET['id']);
	}else{
		$profile = ['err' => true, 'mes' => 'К сожалению, такого пользователя не существует.'];
	}
	if($profile['err']) {	
		return str_replace('__ERROR__', $profile['mes'],  getTemplate('error'));
	}else{
		$a = "<b>ID:</b><span>&nbsp;{$profile['mes']['id']}</span><br/>";
		$a .= "<b>Nickname:</b><span>&nbsp{$profile['mes']['nickname']}</span><br/>";
		$a .= "<b>Доступ:</b><span>&nbsp;{$var['group'][$profile['mes']['access']]}</span><br/>";
		if(!empty($profile['mes']['user_values']) && is_array($profile['mes']['user_values'])){
			foreach($profile['mes']['user_values'] as $v => $k){
				$a .= "<b>{$var['user_values'][$v]}</b><span>&nbsp;$k</span><br/>";
			}
		}
		$a .= "<b>Пол:</b><span>&nbsp;{$var['sex'][$profile['mes']['sex']]}</span><br/>";
		$a .= "<b>Дата регистрации:</b><span>&nbsp;{$profile['mes']['register_date']}</span><br/>";
		$b = "<img class=\"rounded\" id=\"avatar\" src=\"".getUserAvatar($_GET['id'])."\" alt=\"avatar\">";
		$a = str_replace('__USERINFO__', $a,  getTemplate('user_info'));
		$b = str_replace('__AVATAR__', $b,  getTemplate('user_avatar'));
		return $a.$b;
	}
}

echo user_page();

require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');
