<?php 

$data = [
	"id" => 0,
	"code" => "",
	"url" => "",
	"title" => "Приложение устраело, необходимо обновить",
	"userId" => 1337,
	"userNick" => "RadiationX",
	"coverImage" => "",
	"coverImageWidth" => 20,
	"coverImageHeight" => 10,
	"content" => "Нажмите на уведомление об обновлении и установите новую версию приложения. Сайт обновлён, это приложение не совместимо с новой версией сайта. <a href='#'>или нажми на эту ссылку, чтобы скачать файл</a>",
	"countViews" => -1,
	"countComments" => 1,
	"date" => "14.02.2019"
];



die(json_encode($data));