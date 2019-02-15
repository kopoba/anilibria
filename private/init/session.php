<?php
if(session_status() == PHP_SESSION_NONE){
	session_set_cookie_params($var['time']+60*60*24*30, '/', $_SERVER['SERVER_NAME'], true, true);
	$ok = @session_start();
	if(!$ok){
		session_id(uniqid());
		session_start(); 
	}
}
