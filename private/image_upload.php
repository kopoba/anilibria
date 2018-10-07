<?php
//if upload form is submitted
function cropandupload() {
    global $user, $error;
        //get the file information
        $fileName = basename($_FILES["image"]["name"]);
        $fileTmp = $_FILES["image"]["tmp_name"];
        $fileType = $_FILES["image"]["type"];
        $fileSize = $_FILES["image"]["size"];
        $fileExt = substr($fileName, strrpos($fileName, ".") + 1);
        $error = "";

        //specify image upload directory
        $largeImageLoc = $_SERVER['DOCUMENT_ROOT'] . "/upload/temp/" . $fileName;
        $thumbImageLoc = $_SERVER['DOCUMENT_ROOT'] . "/upload/avatars/" . $user["id"] . "." . $fileExt;

        //check file extension
        if ((!empty($_FILES["image"])) && ($_FILES["image"]["error"] == 0)) {
            if ($fileExt != "jpg" && $fileExt != "jpeg" && $fileExt != "png") {
                $error = "Sorry, only JPG, JPEG & PNG files are allowed.";
            }
        } else {
            $error = "Select a JPG, JPEG & PNG image to upload";
        }

        //if everything is ok, try to upload file
        if (strlen($error) == "" && !empty($fileName)) {
            if (move_uploaded_file($fileTmp, $largeImageLoc)) {
                //file permission
                chmod($largeImageLoc, 0777);

                //get dimensions of the original image
                list($current_width, $current_height) = getimagesize($largeImageLoc);

                //get image coords
                $x1 = $_POST['x1'];
                $y1 = $_POST['y1'];
                $x2 = $_POST['x2'];
                $y2 = $_POST['y2'];
                $w = $_POST['w'];
                $h = $_POST['h'];

                //define the final size of the cropped image
                $width_new = 200;
                $height_new = 200;

                //crop and resize image
                $newImage = imagecreatetruecolor($width_new, $height_new);

                switch ($fileType) {
                    case "image/gif":
                        $source = imagecreatefromgif($largeImageLoc);
                        break;
                    case "image/pjpeg":
                    case "image/jpeg":
                    case "image/jpg":
                        $source = imagecreatefromjpeg($largeImageLoc);
                        break;
                    case "image/png":
                    case "image/x-png":
                        $source = imagecreatefrompng($largeImageLoc);
                        break;
                }

                imagecopyresampled($newImage, $source, 0, 0, $x1, $y1, $width_new, $height_new, $w, $h);

                switch ($fileType) {
                    case "image/gif":
                        imagegif($newImage, $thumbImageLoc);
                        break;
                    case "image/pjpeg":
                    case "image/jpeg":
                    case "image/jpg":
                        imagejpeg($newImage, $thumbImageLoc, 90);
                        break;
                    case "image/png":
                    case "image/x-png":
                        imagepng($newImage, $thumbImageLoc);
                        break;
                }
                imagedestroy($newImage);

                //remove large image
                //unlink($imageUploadLoc);

                //display cropped image
                $srcPath = "https://" . $_SERVER['SERVER_NAME'] . "/upload/avatars/" . $user["id"] . "." . $fileExt;
                echo 'CROP IMAGE:<br/><img src="' . $srcPath . '"/>';
            } else {
                $error = "Sorry, there was an error uploading your file.";
            }
        } else {
            //display error
            echo $error;
        }
}
?>
