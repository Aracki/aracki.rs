<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once("thumbnailer.class.php");







function shouldCreateThumbnail() {
    global $thumbnailSizes;
    global $webRoot, $destinationPath, $message;
    if (preg_match("#upload/images/news/#", $destinationPath) || preg_match("#upload/images/#", $destinationPath)) {
        return true;
    } else {
        return false;
    }
}
function getThumbPath($file, $size) {
    $dir = dirname($file);
    $newName = preg_replace("#upload/#", "upload/thumbs/", $dir) . "/" . substr(basename($file), 0, strlen(basename($file))-4) . "_" . $size . "." . trim(strrchr($file, "."), ".");
    return $newName;
}
function regenerateThumbs($file, $force = false) {
    global $thumbnailSizes;
    $file  = preg_replace("#//#", "/", $file);
    $destinationDir = preg_replace("#upload/#", "upload/thumbs/", dirname($file));
    if (!file_exists($destinationDir)) {
        mkdir($destinationDir, 0775, true);
    }
    foreach($thumbnailSizes as $size) {
        if (!file_exists(getThumbPath($file, $size)) || $force) {
            thumbnailer::create_thumbnail($file, $destinationDir, $size);
        }
    }
}
?>