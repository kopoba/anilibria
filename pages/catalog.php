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

.simpleCatalog tr td:last-child {
    text-align:right;
}

.simpleCatalog tr td:nth-child(2) {
    text-align:center;
}

.simpleCatalog tr td:first-child{
	text-align:left;
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
  
  <input data-catalog-update class="btn btn btn-default btn-block" style="width: 100px;" type="submit" value="Показать">
</div>


<div style="margin-top: 15px;">
	<table class="simpleCatalog" style="width: 100%;">
	<tbody>

	</tbody>	
	</table>
</div>
	
<div id="xpagination" style="display: table; text-align: center; margin: 15px auto 0; padding: none;"></div>

</div>

<?require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
