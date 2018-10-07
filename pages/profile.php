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

require_once($_SERVER['DOCUMENT_ROOT'].'/private/image_upload.php');
$profile = show_profile();
if(isset($_POST['upload'])) {
    cropandupload();
}

if($profile['err']){
    echo "<div id=\"error\" style=\"display: block; text-align: center;\">{$profile['mes']}</div>";
}else{

    echo "<p>
			<b>ID:</b><span>&nbsp;{$profile['mes']['id']}</span><br/>
			<b>Login:</b><span>&nbsp;{$profile['mes']['login']}</span><br/>
			<b>Email:</b><span>&nbsp;{$profile['mes']['mail']}</span><br/>
			<b>Access level:</b><span>&nbsp;{$profile['mes']['access']}</span>
		</p>";
}

?>
    <script>
        $(document).ready(function(){
            //prepare instant image preview
            var image = $("img#filePreview");
            $("#fileInput").change(function(){
                image.fadeOut();
                //prepare HTML5 FileReader
                var oFReader = new FileReader();
                oFReader.readAsDataURL(document.getElementById("fileInput").files[0]);
                oFReader.onload = function (oFREvent) {
                    image.attr('src', oFREvent.target.result).fadeIn();
                };
                //Фикс для координат
                var originalHeight = image.naturalHeight;
                var originalWidth = image.naturalWidth;
                //Загрузка плагина imgAreaSelect
                $(image).imgAreaSelect({
                    aspectRatio: '1:1',
                    parent: "#avatar_preview",
                    imageHeight: originalHeight,
                    imageWidth: originalWidth,
                    maxWidth: "200",
                    maxHeight: "200",
                    minWidth: "200",
                    minHeight: "200",
                    x1: 0, y1: 0, x2: 200, y2: 200,
                    handles: true,
                    show: true,
                    onInit: getCoordinates,
                    onSelectChange: getCoordinates
                });
            });
        });
        function checkCoords(){
            if(parseInt($('input#w').val())) return true;
            alert('Please select a crop region then press submit.');
            return false;
        }
        function getCoordinates(img, selection) {
            if (!selection.width || !selection.height){
                return;
            }
            var porcX = img.naturalWidth / img.width;
            var porcY = img.naturalHeight / img.height;
            $('input#x1').val(Math.round(selection.x1 * porcX));
            $('input#y1').val(Math.round(selection.y1 * porcY));
            $('input#x2').val(Math.round(selection.x2 * porcX));
            $('input#y2').val(Math.round(selection.y2 * porcY));
            $('input#w').val(Math.round(selection.width * porcX));
            $('input#h').val(Math.round(selection.height * porcY));
        }
    </script>
    <form action="" enctype="multipart/form-data" method="post" onsubmit="checkCoords();">
        <p>Image: <input name="image" id="fileInput" type="file" /></p>
        <input type="hidden" id="x1" name="x1" value="" />
        <input type="hidden" id="y1" name="y1" value="" />
        <input type="hidden" id="x2" name="x2" value="" />
        <input type="hidden" id="y2" name="y2" value="" />
        <input type="hidden" id="w" name="w" value="" />
        <input type="hidden" id="h" name="h" value="" />
        <div id="avatar_preview"><img id="filePreview" style="display:none;"/></div>
        <input name="upload" type="submit" value="Upload" />
    </form>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
