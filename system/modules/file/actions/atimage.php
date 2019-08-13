<?php

function atimage_GET(Web $w)
{
    list($id) = $w->pathMatch();

    $attachment = $w->File->getAttachment($id);
    if (empty($attachment) || !$attachment->exists() || !$attachment->isImage()) {
        header("HTTP/1.1 404 Not Found");
        return;
    }

    header("Content-Type: image/jpeg");

    if ($attachment->hasCachedImage()) {
        echo file_get_contents($attachment->getImageCachePath());
        return;
    }

    require_once 'phpthumb/ThumbLib.inc.php';

    $full_file_path = $attachment->getFilePath() . "/" . $attachment->filename;
    $image_info = getimagesize($full_file_path);
    $width = $image_info[0];
    $height = $image_info[1];

    $original_image = null;
    $final_image = null;

    switch ($image_info["mime"]) {
        case image_type_to_mime_type(IMAGETYPE_JPEG):
            $original_image = imagecreatefromjpeg($full_file_path);
            break;
        case image_type_to_mime_type(IMAGETYPE_PNG):
            $original_image = imagecreatefrompng($full_file_path);
            break;
        case image_type_to_mime_type(IMAGETYPE_BMP):
            $original_image = imagecreatefrombmp($full_file_path);
            break;
        default:
            $w->Log->setLogger("FILE")->error("Unable to convert image with mime type " . $image_info["mime"] . " to JPEG");
            return;
    }

    $max_width = Config::get("file.cached_image_max_width", 1920);

    if ($width > $max_width) {
        $reduction_ratio = $width / $max_width;
        $new_width = $max_width;
        $new_height = $height / $reduction_ratio;

        $final_image = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($final_image, $original_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    }

    if (!is_dir(dirname($attachment->getImageCachePath()))) {
        mkdir(dirname($attachment->getImageCachePath()), 0755, true);
    }

    imagejpeg(empty($final_image) ? $original_image : $final_image, $attachment->getImageCachePath(), Config::get("file.cached_image_default_quality", -1));
    echo file_get_contents($attachment->getImageCachePath());
}
