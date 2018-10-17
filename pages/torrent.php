<?php
/*
    Запрашиваем поля пользователя show_profile();
    if(!isset($_GET["id"])) => Проверяем, если в ссылке не указан ?id=userid,
    то, загружаем данные залогиненого пользователя.

    Если пользователь не найден => выводим ошибку, скрываем пустые поля профиля

    Пользователь найден => Записываем нужные нам данные в массив $userInfo и выводим на странице

    В дальнейшем:
    1. Изменение данных 90% / 100%
    2. Привязка 2FA
    3. Оформление страницы
    4. Настройки (приватность)
    5. Привязка аккаунтов Патреон/ВК

*/

require_once($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/private/header.php');

?>

<article id="anime_detail">
    <header>
        <h1>Легенда о Гранкресте<br/><span>Grancrest Senki</span></h1>
    </header>
    <div id="anime_detail_body">
        <img id="torrent_poster" src="../upload/torrent/grancrest.png" alt="Название аниме" />
        <p>
            <span><b>Жанры:&nbsp;</b></span>
            <a href="#">магия</a>
            <a href="#">фентези</a>
            <a href="#">сёнен</a>
            <br/>
            <span><b>Озвучка:&nbsp;</b></span>
            <a href="#">Cleo-chan</a>
            <a href="#">Dejz</a>
            <a href="#">Myuk</a>
            <br/>
            <span><b>Аниме сезон:&nbsp;</b></span>
            <a href="#">Зима 2018</a>
            <br/>
            <span><b>Тип:&nbsp;</b>ТВ (24 эп.), 25 мин.</span>
            <br/>
            <span><b>Перевод:&nbsp;</b></span>
            <a href="#">Toxich</a>
            <br/>
            <span><b>Тайминг:&nbsp;</b></span>
            <a href="#">Ghost</a>
            <br/>
            <span><b>Состояние релиза:&nbsp;</b>Завершен</span>
            <br/>
            <span><b>Технические параметры&nbsp;</b><small>(Спойлер)</small></span>
            <br/><br/>
            <b>Описание:&nbsp;</b>Волшебный континент поглотил хаос, порождающий ужасные бедствия. Однако великие Лорды с этого континента обладают древней силой Креста - священными печатями, способными обуздать зло и защитить простой народ. Они могли бы спасти всё живое, но вместо этого решили выяснить, кто достоин стать единственным правителем, которому будут принадлежать все печати. В распрях и вечных битвах они совершенно забыли о своём истинном долге. Но не забыли о нём обычные люди.
            Силука Мелетес, одинокая волшебница, презирает всех стоящих выше за то, что они отреклись от своего предназначения, а странствующий рыцарь Тео Корнэро набирается сил, чтобы в один прекрасный день освободить родной город от тиранического гнёта. Они приносят друг другу нерушимую клятву господина и вассала и работают вместе, дабы преобразить континент, охваченный хаосом и войнами.
            <br/>(c) Cleo-chan
        </p>
        <div style="clear:both; height:0"></div>
        <hr class="red_hr"/>
        <h2>Скачать торрентом</h2>
        <table>
            <tr>
                <td class="torr_name">Серия 1-24 [HDTV-Rip 720p]</td>
                <td class="torr_stats">Вес 9.56GB Раздают 55 Качают 7 Скачало 252</td>
                <td class="torr_addtime">Добавлен: 24.06.2018 13:21</td>
                <td class="torr_download"><a href="#">Скачать</a></td>
            </tr>
            <tr>
                <td class="torr_name">Серия 1-24 [BD-Rip 720p]</td>
                <td class="torr_stats">Вес 15.56GB Раздают 87 Качают 21 Скачало 356</td>
                <td class="torr_addtime">Добавлен: 28.06.2018 11:21</td>
                <td class="torr_download"><a href="#">Скачать</a></td>
            </tr>
        </table>
        <br/>
        <a href="#" class="link_button">Загрузить торрент</a>
    </div>
</article>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
