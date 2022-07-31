<?php
require($_SERVER['DOCUMENT_ROOT'] . '/private/config.php');
require($_SERVER['DOCUMENT_ROOT'] . '/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'] . '/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'] . '/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'] . '/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'] . '/private/func.php');
require($_SERVER['DOCUMENT_ROOT'] . '/private/auth.php');

$var['title'] = 'Команда проекта';
$var['page'] = 'app';

require($_SERVER['DOCUMENT_ROOT'] . '/private/header.php');
?>

    <style>
        .day {
            background: #4a4a4a;
            text-align: center;
            margin: 10px 0 10px 0;
            height: 30px;
            font-size: 13pt;
            line-height: 30px;
            border-radius: 3px;
            color: white;
        }

        .teamleft {
            float: left;
            margin-left: 6px;
        }

        .teamright {
            float: right;
            margin-right: 6px;
        }

    </style>

    <div class="news-block">
        <div class="news-body">

            <p style="text-align: center;">
                <span style="color: #339966;"><span style="font-size: 16pt;">Войсеры, </span></span> <span
                        style="color: #800000;"><span style="font-size: 16pt;">Технари, </span></span><span
                        style="color: #ebd800;"><span style="font-size: 16pt;">Переводчики, </span></span> <span
                        style="color: #ff6600;"><span style="font-size: 16pt;">Оформители, </span></span> <span
                        style="color: #b523c5;"><span style="font-size: 16pt;">Релизёры, </span></span> <span
                        style="color: #000080;"><span style="font-size: 16pt;">Сидеры, </span></span> <span
                        style="color: #33cccc;"><span style="font-size: 16pt;">Дизайнеры </span></span>
            </p>
            <p style="text-align: center; font-size: 13pt; margin-top: 15px; margin-bottom: 15px;">

            </p>

            <div class="day">
                <div class="teamleft">Мы ищем таланты</div>
                <div class="teamright"><a style="color: #FFF;" href="https://t.me/joinlibria_bot">ПОДАТЬ ЗАЯВКУ</a>
                </div>
            </div>

            <ul>
                <li><span style="color: #00a650;">Lupin</span> - глава команды, войсер</li>
                <li>Kirja - главный администратор сайта</li>
            </ul>
            <div class="day">
                <div class="teamleft">&#8544; лига</div>
                <div class="teamright">СОВЕТ</div>
            </div>
            <ul>
                <li><span style="color: #00a650;">Silv</span> - войсер</li>
                <li><span style="color: #00a650;">Itashi</span> - войсер</li>
                <li><span style="color: #00a650;">Sharon</span> - руководитель войсеров, войсер</li>
                <li><span style="color: #339966;">Amikiri</span> - войсер, руководитель переводчиков</li>
            </ul>
            <div class="day">
                <div class="teamleft">&#8545; лига</div>
                <div class="teamright">ВЫСШАЯ</div>
            </div>
            <ul>
                <li><span style="color: #800000;">Caxaro4ek </span> - руководитель команды технарей</li>
                <li><span style="color: #b523c5;">Rossik666</span> - руководитель команд: релизёров, сидеров; оформитель</li>
                <li><span style="color: #f7941d;">ElViS</span> - руководитель команды оформителей, релизёр</li>
                <li><span style="color: #339966;">OkanaTsoy</span> - руководитель академии войсеров, войсер</li>

                <li><span style="color: #339966;">Kroxxa</span> - войсер</li>
                <li><span style="color: #339966;">Renie</span> - войсер</li>
                <li><span style="color: #339966;">MyAska</span> - войсер</li>
                <li><span style="color: #339966;">Gomer</span> - войсер</li>

                <li><span style="color: #800000;">Alkhorus</span> - технарь</li>
                <li><span style="color: #800000;">Quin</span> - технарь, дизайнер</li>
                <li><span style="color: #800000;">im4x</span> - технарь, куратор академии таймеров</li>
            </ul>
            <div class="day">
                <div class="teamleft">&#8546; лига</div>
                <div class="teamright">МАСТЕРА</div>
            </div>
            <ul>
				<li><span style="color: #339966;">Crowley</span> - войсер</li>
                <li><span style="color: #339966;">NeoNoir</span> - войсер</li>
				<li><span style="color: #339966;">Anzen</span> - войсер, дизайнер</li>
                <li><span style="color: #339966;">HectoR</span> - войсер</li>
                <li><span style="color: #339966;">Zozya</span> - войсер</li>
                <li><span style="color: #339966;">Hekomi</span> - войсер</li>


                <li><span style="color: #800000;">WhiteCat</span> - технарь</li>
                <li><span style="color: #800000;">Hidan</span> - технарь</li>
                <li><span style="color: #800000;">Pomidorchik</span> - технарь</li>
				<li><span style="color: #800000;">Den Sato</span> - технарь, дизайнер</li>
				<li><span style="color: #800000;">Kuper</span> - технарь</li>
                <li><span style="color: #800000;">StuffyHarbor</span> - технарь</li>


                <li><span style="color: #ebd800;">Teriliva</span> - переводчик, редактор</li>
                <li><span style="color: #ebd800;">Flames</span> - переводчик</li>
                <li><span style="color: #ebd800;">Timur_kun</span> - переводчик, редактор</li>


                <li><span style="color: #f7941d;">Evrey or zizika</span> - оформитель</li>


                <li><span style="color: #33cccc;">Kell</span> - дизайнер</li>
                <li><span style="color: #33cccc;">Yukki</span> - дизайнер</li>


                <li><span style="color: #b523c5;">GeeKaZ0iD</span> - зам. руководителя релизёров, сидер</li>
                <li><span style="color: #000080;">basegame</span> - сидер</li>
                <li><span style="color: #000080;">OdinokijKot</span> - сидер</li>
                <li><span style="color: #000080;">Tuxoid</span> - сидер, релизёр</li>
            </ul>
            <div class="day">
                <div class="teamleft">&#8547; лига</div>
                <div class="teamright">ОСНОВА</div>
            </div>
            <ul>
				<li><span style="color: #339966;">Cleo-chan</span> - войсер</li>
				<li><span style="color: #339966;">WhiteCroW</span> - войсер</li>
				<li><span style="color: #339966;">SlivciS</span> - войсер, дизайнер</li>
				<li><span style="color: #339966;">Nuts</span> - войсер, дизайнер</li>
                <li><span style="color: #339966;">Hoopoe</span> - войсер</li>
				

				<li><span style="color: #800000;">Ninja-san</span> - технарь</li>
                <li><span style="color: #800000;">ito</span> - технарь</li>
                <li><span style="color: #800000;">N47</span> - технарь</li>
                <li><span style="color: #800000;">Mango</span> - технарь, оформитель</li>
                <li><span style="color: #800000;">Violin</span> - технарь</li>
				<li><span style="color: #800000;">dzoom</span> - технарь</li>
				<li><span style="color: #800000;">Dr.One</span> - технарь</li>
                <li><span style="color: #800000;">Chewy</span> - технарь</li>
				<li><span style="color: #800000;">Oishi Inu</span> - технарь</li>


                <li><span style="color: #ebd800;">Initrd</span> - переводчик</li>
                <li><span style="color: #ebd800;">DeerGfonis</span> - переводчик</li>
                <li><span style="color: #ebd800;">SwiXit</span> - переводчик, редактор</li>
                <li><span style="color: #ebd800;">Artairo</span> - переводчик</li>
                <li><span style="color: #ebd800;">rokettu</span> - переводчик</li>
                <li><span style="color: #ebd800;">Leyla</span> - переводчик</li>
                <li><span style="color: #ebd800;">AKi99</span> - переводчик</li>
				<li><span style="color: #ebd800;">Vurdalak121</span> - переводчик</li>
                <li><span style="color: #ebd800;">chibikTLT</span> - переводчик</li>
                <li><span style="color: #ebd800;">Shaman</span> - переводчик стажер</li>
                <li><span style="color: #ebd800;">Mr Fantasm</span> - переводчик стажер</li>


                <li><span style="color: #f7941d;">JoyMaloy</span> - оформитель</li>
                <li><span style="color: #f7941d;">Neri</span> - оформитель</li>
                <li><span style="color: #f7941d;">Diabl</span> - оформитель</li>
                <li><span style="color: #f7941d;">NekoNis</span> - оформитель</li>
                <li><span style="color: #f7941d;">Lblzhnik</span> - оформитель</li>
                <li><span style="color: #f7941d;">YoLoFox</span> - оформитель</li>
                <li><span style="color: #f7941d;">nev32mind</span> - оформитель</li>


                <li><span style="color: #33cccc;">LanKett</span> - дизайнер</li>
                <li><span style="color: #33cccc;">Denin</span> - дизайнер стажер</li>


                <li><span style="color: #000080;">iDDQD79</span> - сидер</li>
                <li><span style="color: #000080;">btc8190</span> - сидер</li>
                <li><span style="color: #000080;">xJesus</span> - сидер</li>
                <li><span style="color: #000080;">Falciloid</span> - сидер</li>
                <li><span style="color: #000080;">XaviER</span> - сидер, релизёр</li>
                <li><span style="color: #000080;">Aurenmaru</span> - сидер</li>
                <li><span style="color: #000080;">BakaTeshik</span> - сидер</li>

                <li><span style="color: #b523c5;">T1MOX4</span> - релизёр</li>


            </ul>
            <div class="day">
                <div class="teamleft">&#8548; лига</div>
                <div class="teamright">ЗАПАС</div>
            </div>
            <ul>
                <li><span style="color: #339966;">Arato</span> - войсер (в отпуске)</li>
				<li><span style="color: #339966;">Ados</span> - войсер (в отпуске)</li>
                <li><span style="color: #339966;">JazzJack</span> - войсер (в отпуске)</li>
                <li><span style="color: #339966;">Derenn</span> - войсер (в отпуске)</li>

                <li><span style="color: #ebd800;">Iron_me</span> - переводчик (в отпуске)</li>
                <li><span style="color: #ebd800;">Ph0enix</span> - переводчик, сидер (в отпуске)</li>
                <li><span style="color: #ebd800;">Sinaka</span> - переводчик (в отпуске)</li>
                <li><span style="color: #ebd800;">Nasty Lupus</span> - переводчик (в отпуске)</li>
                <li><span style="color: #ebd800;">Yumiya</span> - переводчик (в отпуске)</li>
                <li><span style="color: #ebd800;">Fenix Main</span> - переводчик (в отпуске)</li>

                <li><span style="color: #f7941d;">Waspil</span> - оформитель (в отпуске)</li>
                <li><span style="color: #f7941d;">Akeno102</span> - оформитель (в отпуске)</li>

                <li><span style="color: #800000;">Blaze</span> - технарь (в отпуске)</li>
                <li><span style="color: #800000;">H3O</span> - технарь (в отпуске)</li>
                <li><span style="color: #800000;">Ghost</span> - технарь (в отпуске)</li>
				<li><span style="color: #800000;">MaxVold</span> - технарь (в отпуске)</li>
				<li><span style="color: #800000;">Luchano</span> - технарь (в отпуске)</li>
                <li><span style="color: #800000;">ZencorZ</span> - технарь (в отпуске)</li>
                <li><span style="color: #800000;">Shiro</span> - технарь (в отпуске)</li>

                <li><span style="color: #000080;">Rumaruka</span> - сидер (в отпуске)</li>
                <li><span style="color: #000080;">Vinipux322</span> - сидер (в отпуске)</li>
                <li><span style="color: #000080;">Ztracer</span> - сидер (в отпуске)</li>

				<li><span style="color: #33cccc;">Spiny</span> - дизайнер (в отпуске)</li>
                <li><span style="color: #33cccc;">Sebastian Wilde</span> - дизайнер (в отпуске)</li>

                <li>Hant - PR-команда (в отпуске)</li>

            </ul>
            <div class="day">
                <div class="teamleft">&#8549; лига</div>
                <div class="teamright">АКАДЕМИЯ</div>
            </div>
            <ul>
                <li><span style="color: #339966;">MefistoO</span> - войсер стажёр</li>
                <li><span style="color: #339966;">Frederica Izzard</span> - войсер стажёр</li>
                <li><span style="color: #339966;">Night shift</span> - войсер стажёр</li>
                <li><span style="color: #339966;">Jaily</span> - войсер стажёр</li>
                <li><span style="color: #339966;">Vodyanoy</span> - войсер стажёр</li>
                <li><span style="color: #339966;">Chaika</span> - войсер стажёр</li>
                <li><span style="color: #339966;">Buzya</span> - войсер стажёр</li>
                <li><span style="color: #339966;">Fellandaris</span> - войсер стажёр</li>
                <li><span style="color: #339966;">Ditaro</span> - войсер стажёр</li>
                <li><span style="color: #339966;">Lobanow</span> - войсер стажёр</li>
                <li><span style="color: #339966;">NastyhaGrizli</span> - войсер стажёр</li>


                <li><span style="color: #800000;">Plastinka</span> - таймер стажёр</li>
                <li><span style="color: #800000;">Ceaser</span> - таймер стажёр</li>
                <li><span style="color: #800000;">Qubitik</span> - таймер стажёр</li>
                <li><span style="color: #800000;">emmett</span> - таймер стажёр</li>

            </ul>

            <div class="day">
                <div class="teamleft">
                    Не поддаётся классификации лиговой системы
                </div>
            </div>
            <ul>
                <li>RadiationX - автор приложения, администратор сайта</li>
                <li>Siren Licorisa - администратор discord-сервера</li>
                <li>Инквизитор - администратор discord-сервера</li>
                <li>SineD - Работа с группой ВК</li>
                <li>Maximka - Организатор мероприятий проекта</li>
                <li>Kemsune - смм-щик</li>
                <li>FranxxFun - смм-щик</li>
            </ul>
        </div>
        <div class="clear"></div>
        <div style="margin-top:10px;"></div>
    </div>


<?php require($_SERVER['DOCUMENT_ROOT'] . '/private/footer.php'); ?>