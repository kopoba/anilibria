<?php

$items = [];
$items[] = [
	"torrentUpdate" => 0,
	"id" => "0",
	"code" => "obnovis",
	"title" => "App need update / Приложение устраело, необходимо обновить",
	"torrent_link" => "",
	"link" => "",
	"image" => "/upload/iblock/062/ueno-san-wa-bukiyou-nedotyepa-ueno.png",
	"episode" => "Это важно",
	"description" => "Нажмите на уведомление об обновлении и установите новую версию приложения. Сайт обновлён, это приложение не совместимо с новой версией сайта.",
	"season" => [
		"Зима 2019"
	],
	"voices" => [
		"RadiationX"
	],
	"genres" => [
		"постапокалипсис",
		"ужасы",
		"триллер"
	],
	"types" => [
		"Я не знаю что тут еще написать"
	]
];



$pagination = [
	"total" => 1,
	"page" => 1,
	"total_pages" => 1
];

$data = [
	"items" => $items,
	"navigation" => $pagination
];

die(json_encode($data));