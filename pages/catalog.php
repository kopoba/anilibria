<?php
require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/minify.php');

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



<div class="simpleFilter">
	<div style="margin-bottom: 25px;">
	  <select id="catalogGenre" class="form-control chosen" data-placeholder="Выбрать жанры ..." name="tags[]" multiple style="">
		<?php echo getGenreList(); ?>
	  </select>
	</div>

<div style="margin-bottom: 25px;">
	<select id="catalogYear" class="form-control chosen" data-placeholder="Выбрать год ..." name="tags[]" multiple style="">
		<option value="2018">2018</option>
		<option value="2017">2017</option>
		<option value="2016">2016</option>
		<option value="2015">2015</option>
		<option value="2014">2014</option>
		<option value="2013">2013</option>
	  </select>
	</div>
	
	<div style="float: left; margin-top: 0px;" >
		<input id="switcher" type="checkbox" data-toggle="toggle" data-on="Новое" data-off="Популярное" data-onstyle="default" data-offstyle="default">
	</div>
	

  <input data-catalog-update class="btn btn btn-default btn-block" style="float: left; margin-top: 0px; margin-left: 10px; width: 100px;" type="submit" value="Показать">
</div>


<div style="margin-top: 15px;">
	<table class="simpleCatalog" style="width: 100%;">
	<tbody>

	</tbody>	
	</table>
</div>
	
<div id="xpagination" style="display: table; text-align: center; margin: 15px auto 0; padding: none;"></div>

</div>

<?require($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
