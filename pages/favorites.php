<?php
require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

$var['title'] = 'Избранное';
$var['page'] = 'catalog';

require($_SERVER['DOCUMENT_ROOT'].'/private/header.php');
?>

<style>
.simpleFilter {
	background-color: #3e3e3e;
	padding: 25px 25px;
	margin-top: 15px;
	height: 217px;
}

.simpleCatalog tr:not(:first-child) td {
	padding-top: 30px;
}

.simpleCatalog tr:not(:first-child) td .anime_info_wrapper {
	margin-top: 30px;
	height: calc(100% - 30px);
}

.simpleCatalog tr td:nth-child(1){
	text-align:left;
	width: 306px;
}

.simpleCatalog tr td:nth-child(2) {
    text-align:left;
}

.simpleCatalog tr td:nth-child(3) {
    text-align:right;
}

.simpleCatalog tr td:nth-child(3) .anime_info_wrapper {
    margin-left: 17px;
}

.simpleCatalog td {
	position: relative;
}

.anime_info_wrapper {
	display: none;
	position: absolute;
	top: 0;
	left: 0;
	width: 270px;
	height: 100%;
	background-color: black;
	opacity: 0.8;
	color: white;
	text-align: center;
}

.simpleCatalog td:hover .anime_info_wrapper {
	display: table-cell;
}

.anime_info_wrapper {
	padding: 10px;
	overflow: hidden;
}

.anime_info_wrapper span {
	display: block;
}

.anime_info_wrapper .anime_name {
	font-size: 13pt;
	line-height: 13pt;
	margin-bottom: 5px;
	font-weight: bold;
}
.anime_info_wrapper .anime_number {
	font-size: 12pt;
	margin-bottom: 120px;
}
.anime_info_wrapper .anime_description {
	font-size: 11pt;
}
</style>


<div style="margin-top: 15px;">
	<table class="simpleCatalog" style="width: 100%;">
		<tbody>
		</tbody>	
	</table>
</div>
	
<div id="xpagination" style="display: table; text-align: center; margin: 15px auto 0; padding: none;"></div>

</div>

<?php require($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
