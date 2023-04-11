<?php
function thumb_GET(Web &$w)
{
    $filename = str_replace("..", "", FILE_ROOT . $w->getPath());

    if (is_file($filename)) {
        $width = Request::int("w", FileService::$_thumb_width);
        $height = Request::int("h", FileService::$_thumb_height);

        require_once 'phpthumb/ThumbLib.inc.php';
        $thumb = PhpThumbFactory::create($filename);
        $thumb->adaptiveResize($width, $height);

        header("Content-Type: image/png");
        $thumb->show();
        exit;
    }
}
