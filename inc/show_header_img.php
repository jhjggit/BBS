<?php
    header("Content-type: image/png");
    $img_url = "..".$_GET['img_url'];

    $img = imagecreatefrompng($img_url);

    imagepng($img);

    imagedestroy($img);