<?php 

$items = [];
$items[] = [
	"id" => 0,
	"forumId" => 0,
	"topicId" => 0,
	"postDate" => "Жень, не забудь дату",
	"postMessage" => "Тут нет ничего интересного. Зато вот тут есть кое-что: <a href='https://github.com/anilibria/'>https://github.com/anilibria/</a>. <br><br>Жаль только, что разработчик приложения так и не сделал обработку нажатия на ссылки. Придётся вбивать вручную :) <br><br>А еще скорее всего вы не залогинены, но при этом есть возможность ответить на комментарий. Да? :)",
	"authorId" => 1337,
	"authorName" => "RadiationX",
	"avatar" => "/upload/avatars/noavatar.jpg",
	"userGroup" => 1,
	"userGroupName" => "Один из них"
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