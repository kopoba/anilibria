<?php
if(session_status() == PHP_SESSION_NONE){
	session_set_cookie_params(0, '/', $_SERVER['SERVER_NAME'], true, true);
	$ok = @session_start();
	if(!$ok){
		session_id(uniqid());
		session_start(); 
	}
}
