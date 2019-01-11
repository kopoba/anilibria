$(document).on('click', '[data-modal-show]', function(e) {
	$('#avatarModal').modal('show');
});

$('#uploadAvatar').change(function(){
	readURLProfile(this);	
});

function readURLProfile(input) {
	if ($('#avatarPreview').data('Jcrop')) {
		$('#avatarPreview').data('Jcrop').destroy();
		$('#avatarPreview').removeAttr("style");
		$('#x1').val('');
		$('#y1').val('');
		$('#w').val('');
		$('#h').val('');
	}
	if(input.files&&input.files[0]) {
		var reader = new FileReader();
		reader.onload=function(e) {
			$('#avatarPreview').attr('src',e.target.result);
			$('#avatarPreview').Jcrop({onChange: updateInfo, onSelect: updateInfo});
		}
		reader.readAsDataURL(input.files[0]);
	}
}

function updateInfo(e) {
	$('#x1').val(e.x);
	$('#y1').val(e.y);
	$('#w').val(e.w);
	$('#h').val(e.h);
};


$(document).on("click", "[data-upload-avatar]", function(e) {
	$(this).blur();
	e.preventDefault();
	
	x1 = $('input[id=x1]').val();
	y1 = $('input[id=y1]').val();
	w = $('input[id=w]').val();
	h = $('input[id=h]').val();

	file_data = $('#uploadAvatar').prop('files')[0];
	form_data = new FormData();
	form_data.append('x1', x1);
	form_data.append('y1', y1);
	form_data.append('w', w);  
	form_data.append('h', h);  
    form_data.append('avatar', file_data);
    
	$.ajax({
		type: 'POST',
		cache: false,
		processData: false,
		contentType: false,
		data: form_data,
		url: "//"+document.domain+"/public/avatar.php",
		success: function(json) {
			data = JSON.parse(json);			
			if(data.err != 'ok'){
				$("#avatarInfo").html('Загрузка аватара (<font color=red>'+data.mes+'</font>)');
				return;
			}
			$('#profile-avatar').attr('src', data.mes);
			$('#avatarModal').modal('hide');
		}
	});
});
