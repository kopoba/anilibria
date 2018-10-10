<!DOCTYPE html>
<html lang="ru">
<head>
    <title>AniLibria.TV</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/slider.css">
    <link rel="stylesheet" href="../fonts/fonts.css">
    <link rel="stylesheet" href="../css/width.css">
    <link rel="stylesheet" href="../css/cropper.css">
    <script src="../js/jquery.min.js"></script>
    <script src="../js/jquery.bxslider.js"></script>
    <script src="../js/main.js"></script>
    <script src="../js/cropper/cropper.js"></script>
</head>
<body>
<div id="content_wrapper">
    <header>
        <div id="main_header">
            <img src="../images/logo.png" alt="logo"/>
            <nav id="main_menu">
                <div class="subnav">
                    <a href="#" class="main_nav_link active">Главная</a>
                    <div class="subnav-content active">
                        <a href="#" class="active">Новости</a>
                        <a href="#">Календарь осеннего сезона</a>
                        <a href="#">Все релизы</a>
                        <a href="#">О Нас</a>
                        <a href="#">Правила</a>
                    </div>
                </div>
                <div class="subnav">
                    <a href="#" class="main_nav_link">Аниме</a>
                    <div class="subnav-content">
                        <a href="#">Календарь осеннего сезона</a>
                        <a href="#">Календарь летнего сезона</a>
                        <a href="#">Все релизы</a>
                        <a href="#">Моё избранное</a>
                        <a href="#">Топ 10 осени</a>
                    </div>
                </div>
                <div class="subnav">
                    <a href="#" class="main_nav_link">Видео</a>
                    <div class="subnav-content">
                        <a href="#">RAP-Обзоры</a>
                        <a href="#">Fisheye Placebo</a>
                        <a href="#">Анонсы сезонов и топ-10</a>
                    </div>
                </div>
                <div class="subnav">
                    <a href="#" class="main_nav_link">Видеоблоги</a>
                    <div class="subnav-content">
                        <a href="#">Люпин(ЛЛН)</a>
                        <a href="#">Иташи</a>
                        <a href="#">Сильв</a>
                        <a href="#">Чайный домик</a>
                    </div>
                </div>
                <div class="subnav">
                    <a href="#" class="main_nav_link">О Команде</a>
                    <div class="subnav-content">
                        <a href="#">О Нас</a>
                        <a href="#">Список команды</a>
                        <a href="#">Подать заявку</a>
                        <a href="#">Поддержать</a>
                    </div>
                </div>
                <div class="subnav">
                    <a href="#" class="main_nav_link">Форум</a>
                    <div class="subnav-content">
                        <!--<a href="#">Обитель комментариев</a>-->
                        <!--<a href="#">Либрийская таверна</a>-->
                        <!--<a href="#">Разработка сайта</a>-->
                        <!--<a href="#">Приёмный зал</a>-->
                    </div>
                </div>
            </nav>
            <?php
            if($user) {
                ?><a href="#" id="user_avatar"><img src="<?php echo getUserAvatar($user["login"], $user["id"])?>" alt="avatar"/></a><?php
            } else {
                ?><a class="auth_button" href="../pages/login.php">Авторизация</a><?php
            }
            ?>
            <div id="user_dropdown_menu">
                <span><?php echo $user["login"] ?></span>
                <a href="../pages/profile.php">Мой профиль</a>
                <a href="#">Моё избранное</a>
                <a href="/public/logout.php">Выход</a>
            </div>
        </div>
        <div id="hslider">
            <div id="slides_main_wrapper" class="slideshow">
                <figure class="slider_main slide" style="background-image: url('https://www.anilibria.tv/bitrix/templates/AniLibria%20KD%20Design/slider/image_smart/884b3171a8f451fc8780bd0f8cd06e06.png')">
                    <a href='#'>Slider Link</a>
                </figure>
                <figure class="slider_main slide" style="background-image: url('https://www.anilibria.tv/bitrix/templates/AniLibria%20KD%20Design/slider/image_smart/7c7123157d4e84147cd3ab48085640e7.png')">
                    <a href='#'>Slider Link</a>
                </figure>
                <figure class="slider_main slide" style="background-image: url('https://www.anilibria.tv/bitrix/templates/AniLibria%20KD%20Design/slider/image_smart/c61bb097dd93bab4328d5b37a23e4e69.png')">
                    <a href='#'>Slider Link</a>
                </figure>
            </div>
        </div>
    </header>
    <aside id="side_menu">
        <a href="#" class="side_image_links">
            <img src="../images/subscribe_new_series.png" class="side_img_link" alt="new series subscription"/>
        </a>
        <div id="social_links">
            <span>Ссылки</span>
            <a href="#">
                <img src="../images/vk.png" alt="vkontakte" />
            </a>
            <a href="#">
                <img src="../images/yt.png" alt="YouTube" />
            </a>
            <a href="#">
                <img src="../images/tg.png" alt="Telegram" />
            </a>
            <a href="#">
                <img src="../images/ds.png" alt="Discord" />
            </a>
            <a href="#">
                <img src="../images/vedroid.png" alt="Android" />
            </a>
        </div>
        <!-- Скрывать для действующего спонсора patreon -->
        <a href="#" class="side_image_links">
            <img src="../images/patreon_subscribe.png" class="side_img_link" alt="patreon subscription"/>
        </a>
        <a href="#" class="side_image_links">
            <img src="../images/support_us.png" class="side_img_link" alt="support us"/>
        </a>
        <!-- Скрывать для действующего спонсора patreon -->
        <div id="notice">
            <p>Рекламное место<br/>240 x 328 px</p>
            <a href="#">
                <!--<img src="images/support_us.png" class="side_img_link" alt="ad name"/>-->
            </a>
        </div>
        <div id="twitter_block">
            <a class="twitter-timeline" data-lang="ru" data-width="100%" data-height="500px" data-theme="dark" data-link-color="#eb5252" href="https://twitter.com/AniLibria_Tv?ref_src=twsrc%5Etfw">Tweets by AniLibria_Tv</a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
        </div>
    </aside>
    <div id="main_content_top">
        <div id="notice_top">
            <p>Рекламное место<br/>518 x 120 px</p>
            <!-- Заменять картинку на благодарность для действующего спонсора patreon -->
            <a href="#">
                <!--<img src="images/support_us.png" class="side_img_link" alt="ad name"/>-->
            </a>
        </div>
        <div id="search">
            <p>Поиск</p>
            <input type="search" placeholder="Введите текст для поиска" />
        </div>
    </div>
    <main role="main" id="main_content">
