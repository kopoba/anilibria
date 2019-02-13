<?php 

$data = [
	"id" => 0,
	"code" => "",
	"url" => "",
	"title" => "Приложение устраело, необходимо обновить",
	"userId" => 1337,
	"userNick" => "RadiationX",
	"coverImage" => "/upload/app/old_api_a.jpg",
	"coverImageWidth" => 850,
	"coverImageHeight" => 420,
	"content" => "Нажмите на уведомление об обновлении и установите новую версию приложения. Сайт обновлён, это приложение не совместимо с новой версией сайта. <a href='https://www.anilibria.tv/upload/app/AniLibria_v2.3.0.apk'>или нажми на эту ссылку, чтобы скачать файл</a>",
	"countViews" => -1,
	"countComments" => 1,
	"date" => "14.02.2019"
];



die(json_encode($data));