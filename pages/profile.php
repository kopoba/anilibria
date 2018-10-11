<?php
/*
    Запрашиваем поля пользователя show_profile();
    if(!isset($_GET["id"])) => Проверяем, если в ссылке не указан ?id=userid,
    то, загружаем данные залогиненого пользователя.

    Если пользователь не найден => выводим ошибку, скрываем пустые поля профиля

    Пользователь найден => Записываем нужные нам данные в массив $userInfo и выводим на странице

    В дальнейшем:
    1. Изменение данных 50% / 100%
    2. Привязка 2FA
    3. Оформление страницы
    4. Настройки (приватность)
    5. Привязка аккаунтов Патреон/ВК

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
if(isset($_POST['saveData'])) {
    if(!empty($_POST['mail'])) change_mail();
    if(!empty($_POST['passwd'])) change_passwd();
    saveUser($profile['mes']['id']);
}
$profileError = json_decode($profile['err']);
echo $profileError."<<<<";
if($profileError){
    echo "<div id=\"error\" style=\"display: block; text-align: center;\">{$profile['mes']}</div>";
}else{
	$userAvatar = getUserAvatar($profile['mes']['id']);
    $userData = json_decode($profile['mes']['user_data']);
    switch ($profile['mes']['sex']) {
        case 0:
            $sex = "Не указано";
            break;
        case 1:
            $sex = "Мужчина";
            break;
        case 2:
            $sex = "Женщина";
            break;
    }
    echo "<p>
			<b>ID:</b><span>&nbsp;{$profile['mes']['id']}</span><br/>
			<b>Логин:</b><span>&nbsp;{$profile['mes']['login']}</span><br/>
			<b>Ник:</b><span>&nbsp;".getUserNick($profile['mes']['id'])."</span><br/>
			<b>Email:</b><span>&nbsp;{$profile['mes']['mail']}</span><br/>
			<b>Доступ:</b><span>&nbsp;". getGroupName($profile['mes']['access'])."</span><br/>
			<b>Вконтакте:</b><span>&nbsp;$userData->vk</span><br/>
			<b>Телеграм:</b><span>&nbsp;$userData->telegram</span><br/>
			<b>SteamID:</b><span>&nbsp;$userData->steamid</span><br/>
			<b>Возраст:</b><span>&nbsp;$userData->age</span><br/>
			<b>Страна:</b><span>&nbsp;$userData->country</span><br/>
			<b>Город:</b><span>&nbsp;$userData->city</span><br/>
			<b>Пол:</b><span>&nbsp;$sex</span><br/>
			<b>Дата регистрации:</b><span>&nbsp;{$profile['mes']['register_date']}</span><br/>
		</p>";
}

if(!empty($_GET['id'])) {
    if($_GET['id'] == $profile['mes']['id'] || adminLevel() == 5) {?>
        <p><br/><b>Редактирование провиля:</b></p>
        <form method="post" enctype="multipart/form-data">
            <div class="input_wrapper">
                <input id="email" class="styled_input" type="email" name="mail" value="<?php echo $profile['mes']['mail']?>" />
                <label for="email" class="floating_label">Email</label>
            </div>
            <div class="input_wrapper">
                <input id="nickname" class="styled_input" type="text" name="nickname" value="<?php echo $profile['mes']['nickname']?>" />
                <label for="nickname" class="floating_label">Ник</label>
            </div>
            <div class="input_wrapper">
                <input id="password_old" class="styled_input" name="passwd" type="password" />
                <label for="password_old" class="floating_label">Старый пароль</label>
            </div>
            <div class="input_wrapper">
                <input id="password_new" class="styled_input" type="password" />
                <label for="password_new" class="floating_label">Новый пароль</label>
            </div>
            <div class="input_wrapper">
                <input id="password_new2" class="styled_input" type="password" />
                <label for="password_new2" class="floating_label">Повторите пароль</label>
            </div>
            <!-- user json fields -->
            <div class="input_wrapper">
                <input id="vkontakte" class="styled_input" type="url" name="vkontakte" value="<?php echo $userData->vk?>" />
                <label for="vkontakte" class="floating_label">Вконтакте</label>
            </div>
            <div class="input_wrapper">
                <input id="steamid" class="styled_input" type="url" name="steamid" value="<?php echo $userData->steamid?>" />
                <label for="steamid" class="floating_label">SteamID</label>
            </div>
            <div class="input_wrapper">
                <input id="telegram" class="styled_input" type="url" name="telegram" value="<?php echo $userData->telegram?>" />
                <label for="telegram" class="floating_label">Телеграм</label>
            </div>
            <div class="input_wrapper">
                <input id="age" class="styled_input" type="number" name="age" value="<?php echo $userData->age?>" />
                <label for="age" class="floating_label">Возраст</label>
            </div>
            <div class="input_wrapper">
                <input id="country" class="styled_input" type="text" name="country" value="<?php echo $userData->country?>" />
                <label for="country" class="floating_label">Страна</label>
            </div>
            <div class="input_wrapper">
                <input id="city" class="styled_input" type="text" name="city" value="<?php echo $userData->city?>" />
                <label for="city" class="floating_label">Город</label>
            </div>
            <label for="sex">Пол:</label>
            <select id="sex" name="sex">
                <option value="0" <?php if ($profile['mes']['sex'] == 0) echo 'selected="selected"' ?>>Не указано</option>
                <option value="1" <?php if ($profile['mes']['sex'] == 1) echo 'selected="selected"' ?>>Мужской</option>
                <option value="2" <?php if ($profile['mes']['sex'] == 2) echo 'selected="selected"' ?>>Женский</option>
            </select>
            <!-- user json fields end -->
            <input type="submit" name="saveData" value="Изменить" />
        </form>
    <?php }
}

if(adminLevel() == 5) {

    echo "Debug info:<br/>";
    echo "<pre>";
    echo print_r($_POST);
    echo "</pre>";
    echo "JSON Decoded Data:<br/>";
    foreach ($userData as $value) {
        echo "$value<br/>";
    }
}
?>
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
                viewMode: 1,
                scalable: false,
                zoomable: false,
                imageSmoothingEnabled: false,
                imageSmoothingQuality: 'high',
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
                }, "image/jpeg", 0.95);
            }
        });
    });
</script>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
