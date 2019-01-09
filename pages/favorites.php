<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/minify.php');

$var['page'] = 'catalog';

require_once($_SERVER['DOCUMENT_ROOT'].'/private/header.php');
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
</style>


<div style="margin-top: 15px;">
	<table class="simpleCatalog" style="width: 100%;">
		<tbody>
		</tbody>	
	</table>
</div>
	
<div id="xpagination" style="display: table; text-align: center; margin: 15px auto 0; padding: none;"></div>

</div>

<?require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
