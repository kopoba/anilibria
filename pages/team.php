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

$teams = getTeams();
?>

    <div class="news-block team-block">
        <div class="news-body">

            <p style="text-align: center;">

                <span style="color: #339966;"><span style="font-size: 16pt;">Войсеры, </span></span>
                <span style="color: #800000;"><span style="font-size: 16pt;">Технари, </span></span>
                <span style="color: #ebd800;"><span style="font-size: 16pt;">Переводчики, </span></span>
                <span style="color: #ff6600;"><span style="font-size: 16pt;">Оформители, </span></span>
                <span style="color: #b523c5;"><span style="font-size: 16pt;">Релизёры, </span></span>
                <span style="color: #000080;"><span style="font-size: 16pt;">Сидеры, </span></span>
                <span style="color: #33cccc;"><span style="font-size: 16pt;">Дизайнеры </span></span>

                <!-- Join Anilibria Team -->
                <span style="display: inline-block;">
                    <span></span>
                    <span style="font-size: 16pt;">
                        <a style="color: red;" href="https://t.me/joinlibria_bot">Подать заявку</a>
                    </span>
                </span>
            </p>
            <p style="text-align: center; font-size: 13pt; margin-top: 15px; margin-bottom: 15px;"></p>

            <?php
            foreach ($teams as $team) {

                // Team
                echo '<div class="day">';
                echo sprintf('<div class="teamleft">%s</div>', $team['title']);
                echo sprintf('<div class="teamright">%s</div>', $team['description']);
                echo '</div>';

                // Users
                echo '<ul>';
                foreach ($team['users'] ?? [] as $user) {

                    $roles = $user['roles'] ?? [];
                    $colors = array_filter(array_column($roles, 'color'));
                    $color = array_shift($colors);
                    $isIntern = $user['is_intern'] === true;
                    $userRoles = mb_strtolower(implode(', ', array_column($roles, 'title')));
                    $isVacation = $user['is_vacation'] === true;

                    // Check if user IS NOT on vacation
                    if ($isVacation === false) {
                        echo sprintf('<li><div class="teamuser"><span style="color: %s;">%s</span>&nbsp;— %s%s</div></li>', $color ?? 'black', $user['nickname'], $userRoles, $isIntern ? '<span class="intern">стажер</span>' : '');
                    }
                }

                echo '</ul>';
            }
            ?>

            <!-- Vacation Team -->
            <div class="day">
                <div class="teamleft">В отпуске</div>
            </div>

            <?php
            echo '<ul>';
            foreach ($teams as $team) {
                foreach ($team['users'] ?? [] as $user) {

                    $roles = $user['roles'] ?? [];
                    $colors = array_column($roles, 'color');
                    $color = array_shift($colors);
                    $isIntern = $user['is_intern'] === true;
                    $userRoles = mb_strtolower(implode(', ', array_column($roles, 'title')));
                    $isVacation = $user['is_vacation'] === true;

                    // Check if user IS on vacation
                    if ($isVacation === true) {
                        echo sprintf('<li style="color:#a8a8a8;"><div class="teamuser"><span>%s</span>&nbsp;— %s%s</div></li>', $user['nickname'], $userRoles, $isIntern ? '<span class="intern">стажер</span>' : '');
                    }
                }
            }
            echo '</ul>';
            ?>

        </div>
        <div class="clear"></div>
        <div style="margin-top:10px;"></div>
    </div>


<?php require($_SERVER['DOCUMENT_ROOT'] . '/private/footer.php'); ?>