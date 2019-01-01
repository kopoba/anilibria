$(document).ready(function() {
	$('#tableSess').DataTable({"order": [[ 0, "desc" ]]});
});

$(document).on("click", "[data-history-show-header]", function(e) {
	$(this).blur();
	e.preventDefault();
	$('#headerModal').modal('show');
	$("#showHeader").text(atob($(this).data("history-show-header")));
});

$(document).on("click", "[data-session-id]", function(e) {
	$(this).blur();
	e.preventDefault();
	$.post("//"+document.domain+"/public/close_sess.php", {'id': $(this).data("session-id") });
	$(this).hide();
    $('#tableSess').DataTable().cell(':eq('+$(this).data("session-td")+')', 4).data('Closed');
});

//$(document).ready(function() {
//	$('#tableRelease').DataTable({"order": [[ 0, "desc" ]]});
//});

$(document).on('click', '[data-admin-release-delete]', function(e){
	$(this).blur();
	e.preventDefault();
	if(window.confirm('Действительно хотите удалить релиз?')){
		$.post("//"+document.domain+"/public/release_delete.php", {'id': $(this).data("admin-release-delete")});
		$('#tableRelease').DataTable().row( $(this).parents('tr') ).remove().draw();
	}
});


$(document).ready(function() {
	$('#tableRelease').dataTable({
		"processing": true,
		"serverSide": true,
		"ajax": { "url":"/public/release_table.php", "type":"POST" },
		"order": [[ 0, "desc" ]]
	});
});

//setInterval( function () { $('#supportList').DataTable().ajax.reload( null, false ); }, 30000 );
