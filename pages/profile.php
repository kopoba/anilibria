<?php
/*
    Запрашиваем поля пользователя show_profile();
    if(!isset($_GET["id"])) => Проверяем, если в ссылке не указан ?id=userid,
    то, загружаем данные залогиненого пользователя.

    Если пользователь не найден => выводим ошибку, скрываем пустые поля профиля

    Пользователь найден => Записываем нужные нам данные в массив $userInfo и выводим на странице

    В дальнейшем:
    1. Изменение данных
    2. Привязка 2FA
    3. Оформление страницы
    4. Настройки (приватность)
    5. Привязка аккаунтов Патреон/ВК


    Заливка аватарки:
    Славливаем POST['upload'] по нажатию кнопки и запускаем функцию cropandupload
    из файла image_upload.

    Нужно пофиксить баг -> при выборе аватарки в профиле, появляется предпросмотр + crop
    Кропу выставляются координаты $(image).imgAreaSelect

*/

require_once($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/private/header.php');

$profile = show_profile();

if($profile['err']){
    echo "<div id=\"error\" style=\"display: block; text-align: center;\">{$profile['mes']}</div>";
}else{
	$userAvatar = getUserAvatar($profile['mes']['id']);
    echo "<p>
			<b>ID:</b><span>&nbsp;{$profile['mes']['id']}</span><br/>
			<b>Login:</b><span>&nbsp;{$profile['mes']['login']}</span><br/>
			<b>Nickname:</b><span>&nbsp;".getUserNick($profile['mes']['id'])."</span><br/>
			<b>Email:</b><span>&nbsp;{$profile['mes']['mail']}</span><br/>
			<b>Access level:</b><span>&nbsp;". getGroupName($profile['mes']['access'])."</span>
		</p>";
}
?>
<link rel="stylesheet" href="../css/bt_modal/bootstrap.min.css">
<script src="../js/bt_modal/bootstrap.min.js"></script>
<style>
    .label {
        cursor: pointer;
    }
    .progress {
        display: none;
        margin-bottom: 1rem;
    }
    .alert {
        display: none;
    }
    .img-container img {
        max-width: 100%;
    }
	#avatar {
		width: 200px;
		height: 200px;
	}
</style>
<h1>Upload cropped image to server</h1>
<label class="label" data-toggle="tooltip" title="Change your avatar">
    <img class="rounded" id="avatar" src="<?php echo $userAvatar?>" alt="avatar">
    <input type="file" class="sr-only" id="input" name="image" accept="image/*">
</label>
<div class="progress">
    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
</div>
<div class="alert" role="alert"></div>
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Crop the image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="img-container">
                    <img id="image" src="<?php echo $userAvatar?>">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="crop">Crop</button>
            </div>
        </div>
    </div>
</div>
<script>
    window.addEventListener('DOMContentLoaded', function () {
        var avatar = document.getElementById('avatar');
        var image = document.getElementById('image');
        var input = document.getElementById('input');
        var $progress = $('.progress');
        var $progressBar = $('.progress-bar');
        var $alert = $('.alert');
        var $modal = $('#modal');
        var cropper;
        //$('[data-toggle="tooltip"]').tooltip();
        input.addEventListener('change', function (e) {
            var files = e.target.files;
            var done = function (url) {
                input.value = '';
                image.src = url;
                $alert.hide();
                $modal.modal('show');
            };
            var reader;
            var file;
            var url;
            if (files && files.length > 0) {
                file = files[0];
                if (URL) {
                    done(URL.createObjectURL(file));
                } else if (FileReader) {
                    reader = new FileReader();
                    reader.onload = function (e) {
                        done(reader.result);
                    };
                    reader.readAsDataURL(file);
                }
            }
        });
        $modal.on('shown.bs.modal', function () {
            cropper = new Cropper(image, {
                aspectRatio: 1,
                viewMode: 2,
            });
        }).on('hidden.bs.modal', function () {
            cropper.destroy();
            cropper = null;
        });
        document.getElementById('crop').addEventListener('click', function () {
            var initialAvatarURL;
            var canvas;
            $modal.modal('hide');
            if (cropper) {
                canvas = cropper.getCroppedCanvas({
                    width: 200,
                    height: 200,
                });
                initialAvatarURL = avatar.src;
                avatar.src = canvas.toDataURL();
                $progress.show();
                $alert.removeClass('alert-success alert-warning');
                canvas.toBlob(function (blob) {
                    var formData = new FormData();
                    formData.append('avatar', blob);
                    $.ajax('/public/upload_avatar.php', {
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        xhr: function () {
                            var xhr = new XMLHttpRequest();
                            xhr.upload.onprogress = function (e) {
                                var percent = '0';
                                var percentage = '0%';
                                if (e.lengthComputable) {
                                    percent = Math.round((e.loaded / e.total) * 100);
                                    percentage = percent + '%';
                                    $progressBar.width(percentage).attr('aria-valuenow', percent).text(percentage);
                                }
                            };
                            return xhr;
                        },
                        complete: function (response) {
                            $progress.hide();
							var getContact = JSON.parse(response.responseText);
							if(getContact.err == "ok") {
								$alert.show().addClass('alert-success').text('Upload success');
							} else {
								$alert.show().addClass('alert-warning').text('Upload error: ' + getContact.mes);
								avatar.src = initialAvatarURL;
							}
                        },
                    });
                }, "image/jpeg");
            }
        });
    });
</script>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
