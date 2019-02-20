<?php
require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/sphinx.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

$var['title'] = 'Алфавитный указатель';
$var['page'] = 'alphabet';

//if(!$user || $user < 3) { die(); }

require($_SERVER['DOCUMENT_ROOT'].'/private/header.php');
?>
<div class="news-block">
    <p style="text-align:center; font-size: 20pt; line-height: 20pt; font-weight: bold;">АЛФАВИТНЫЙ УКАЗАТЕЛЬ</p>
    <hr/>
    <div id="alphabet-characters">
        <?php echo showAscAlphabet(); ?>
    </div>
    <div id="alphabetic-block">
        <?php echo showAscReleases(); ?>
    </div>
    <div class="clear"></div>
    <div style="margin-top:10px;"></div>
</div>

<?php require($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
