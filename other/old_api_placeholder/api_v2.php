<?php
header('Access-Control-Allow-Origin: *');
switch(@$_REQUEST['action']){
	case 'empty':
		die();
	break;
	case 'app':
	case 'app_v2':
		if(@$_REQUEST['check'] == 'update' )
				require_once('update.php');
			else
				require_once('app.php');
	break;
	case 'comments':
	case 'articles':
	case 'article':
	case 'release':
	case 'favorites':
	case 'main':
	case 'search':
	case 'tags':
		$arr_inc = [
			'comments' => 'comments.php',
			'articles' => 'article_list.php',
			'article' => 'article_detail.php',
			'release' => 'release.php',
			'favorites' => 'main.php',
			'main' => 'main.php',
			'search' => 'main.php',
			'tags' => 'tags.php'
		];
		require_once($arr_inc[$_REQUEST['action']]);
	break;
	default:
		require_once('main.php');
	break;
}
