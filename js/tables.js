$(document).ready(function() {
	$('#tebleSess').DataTable({"order": [[ 0, "desc" ]]});
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
    $('#tebleSess').DataTable().cell(':eq('+$(this).data("session-td")+')', 4).data('Closed');
});
