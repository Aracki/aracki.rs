<?php
function getThumbnailPath($imagePath, $imageSize) {
    if ($imagePath && $imageSize) {
        $imageSizes = array(
                "small_banner" => "50x50",
                "normal_banner" => "250x90",
                "small" => "50x50",
                "normal" => "250x90",
                "large" => "186x186"
        );
        return thumbnailer::getThumbPath($imagePath, $imageSizes[$imageSize]);
    } else {
        return "";
    }
}

if (!class_exists("Services_JSON")) {
    require_once("json.class.php");
}

/**
 * Thumbnailer for OCP 2
 * @requirement Needs thumbnails.json file in /ocp/config.
 * @requirement Needs /upload/thumbs/getthumb.php file
 * Class handles all operations related to thumb creation, cropping etc.
 */
class thumbnailer {

    /**
     * Creates thumbnail for given image and size in given directory
     * @param string $image absolute path to image file
     * @param string $dir absolute path to thumbnails directory
     * @param string $sizeInstruction size instruction - a string in format \d\d?\d?\d?x\d\d?\d?\d? that gets appended to the end of filename and serves as size identifier
     * @return string
     */
    static function create_thumbnail($image, $dir, $sizeInstruction = "0x0") {
        if ($image && file_exists($image)) {
            if (!defined("THUMBNAIL_IMAGE_QUALITY")) {
                define("THUMBNAIL_IMAGE_QUALITY", 95);
            }
            $max_dims = explode("x", $sizeInstruction);
            $max_width = $max_dims[0];
            $max_height = $max_dims[1];
            $src_x = 0;
            $src_y = 0;
            if ($max_width == "") {
                $max_width = 0;
            }
            if ($max_height == "") {
                $max_height = 0;
            }
            if (!preg_match("/\/$/", $dir)) {
                $dir .= "/";
            }
            $imagedims = getimagesize($image);
            $src_width = $imagedims[0];
            $src_height = $imagedims[1];
            if ($max_height == 0) {
                $width = $max_width;
                $height = intval(($width * $src_height) / $src_width);
            } else {
                $width = $max_width;
                $height = $max_height;
                if (intval(($width * $src_height) / $src_width) < intval($height)) {
                    $_ratio = intval(($width * $src_height) / $src_width) / intval($height);
                    $src_width = $_ratio * $src_width;
                    $src_x = abs($imagedims[0] - $src_width) / 2;
                } else if (intval(($width * $src_height) / $src_width) > intval($height)) {
                    $_ratio = intval(($height * $src_width) / $src_height) / intval($width);
                    $src_height = $_ratio * $src_height;
//$src_y = abs($imagedims[1] - $src_height)/2;
                }
            }
            if (file_exists($dir) && is_dir($dir)) {
                $extension = trim(strrchr($image, "."), ".");
                switch ($extension) {
                    case "jpg" :
                    case "jpeg" :
                    case "jpe" :
                    case "JPG" :
                    case "JPEG" :
                    case "JPE" :
                        $type = "jpeg";
                        $image_res = imagecreatefromjpeg($image);
                        break;
                    case "gif" :
                    case "GIF" :
                        $type = "gif";
                        $image_res = imagecreatefromgif($image);
                        break;
                    case "png" :
                    case "PNG" :
                        $type = "png";
                        $image_res = imagecreatefrompng($image);
                        break;
                    case "wbmp" :
                    case "WBMP" :
                        $type = "jpeg";
                        $image_res = imagecreatefromwbmp($image);
                        break;
                }

                if ($height > $max_height && $max_height != 0) {
                    $thumbnail = imagecreatetruecolor($width, $max_height);
                } else {
                    $thumbnail = imagecreatetruecolor($width, $height);
                }
                imagecopyresampled($thumbnail, $image_res, 0, 0, $src_x, $src_y, $width, $height, $src_width, $src_height);
                switch ($type) {
                    case "jpeg" :
                    default :
                        $img = imagejpeg($thumbnail, $dir . substr(basename($image), 0, strlen(basename($image)) - 4) . "_" . $max_width . "x" . $max_height . "." . $extension, THUMBNAIL_IMAGE_QUALITY);
                        break;
                    case "gif" :
                        $img = imagegif($thumbnail, $dir . substr(basename($image), 0, strlen(basename($image)) - 4) . "_" . $max_width . "x" . $max_height . "." . $extension);
                        break;
                    case "png" :
                        $img = imagepng($thumbnail, $dir . substr(basename($image), 0, strlen(basename($image)) - 4) . "_" . $max_width . "x" . $max_height . "." . $extension);
                        break;
                }
                self::applyWatermark($dir . substr(basename($image), 0, strlen(basename($image)) - 4) . "_" . $max_width . "x" . $max_height . "." . $extension, realpath(dirname(__FILE__) . "/../../../upload/images/") . "/na-dlanu-logo-veci.png");
                return $img;

            }
        } else { // no image present
            return "";
        }
    }

    /**
     * Gets thumbnail sizes for given image.
     * @param string $path image path
     * @return array An array of sizes ("size_name"=>"WxH");
     */
    public static function getThumbnailSizes($path = "") {
        if ($path) {
            $path = dirname($path);
        }
        $thumbnailSizes = array();
        $settings = self::getThumbnailSetup();
        foreach ($settings as $setting) { // prvo trazi exact match
            if (!preg_match("/\*$/", $setting->name)) {
                if ($path) {
                    if ($setting->name == $path) {
                        foreach ($setting->sizes as $size) {
                            $thumbnailSizes[$size->name] = $size->width . "x" . $size->height;
                        }
                    }
                } else {
                    if ($setting->name == "default") {
                        foreach ($setting->sizes as $size) {
                            $thumbnailSizes[$size->name] = $size->width . "x" . $size->height;
                        }
                    }
                }
            }
        }
        if (empty($thumbnailSizes)) { // nije nadjen exact match, probaj sa * (/upload/images/test/* matchuje sve sto je u tom direktroijumu
            $targetDir = dirname($path);
            foreach ($settings as $setting) { // prvo trazi exact match
                if (preg_match("/\*$/", $setting->name)) {
                    $specialDir = dirname(preg_replace("/\*$/", "", $setting->name)); // dobijamo spec dir bez *
                    if (substr($targetDir, 0, strlen($specialDir)) == $specialDir) { // target dir je unutar spec direktorijuma
                        foreach ($setting->sizes as $size) {
                            $thumbnailSizes[$size->name] = $size->width . "x" . $size->height;
                        }
                    }
                }
            }
        }
        if ($path && empty($thumbnailSizes)) { // path postoji, nista nije nadjeno - ucitaj default (ponovo)
            $thumbnailSizes = self::getThumbnailSizes();
        }
        return $thumbnailSizes;
    }

    /**
     * gets thumbnail setup object (decoded JSON)
     * @param <type> $type
     * @return <type>
     */
    public static function getThumbnailSetup() {
        $settingsFileContent = file_get_contents(self::getSettingsFilePath());
        $json = new Services_JSON();
        $settings = $json->decode($settingsFileContent);
        return $settings;
    }

    /**
     * Gets absolute path to website root
     * @return string
     */
    public static function getWebRoot() {
        return realpath(dirname(__FILE__) . "/../../../");
    }

    /**
     * ?
     * @todo remove, obsolete
     * @param string $destinationPath
     * @return boolean
     */
    public static function shouldCreateThumbnail($destinationPath) {
        $thumbnailSizes = self::getThumbnailSizes($destinationPath);
        $webRoot = self::getWebRoot();
        if (preg_match("#upload/images/#", $destinationPath)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns thumbnail path for given file
     * @param string $file relative path to image file
     * @param string $size size instruction (WxH)
     * @return string
     */
    public static function getThumbPath($file, $size) {
        $file = urldecode($file);
        $dir = dirname($file);
        $newName = preg_replace("#upload/#", "upload/thumbs/", $dir) . "/" . substr(basename($file), 0, strlen(basename($file)) - 4) . "_" . $size . "." . trim(strrchr($file, "."), ".");
        $destinationDir = self::getWebRoot() . preg_replace("#upload/#", "upload/thumbs/", $dir);
        if (!file_exists($destinationDir)) {
            mkdir($destinationDir, 0775, true);
        }
        if (!file_exists(self::getWebRoot() . $newName) && preg_match("#^/upload#" ,$newName)) {
            self::create_thumbnail(self::getWebRoot() . self::getImagePath($newName), $destinationDir, $size);
        }
        return $newName;
    }

    /**
     * Returns original image path from thumbnail path
     * @param string $thumbnailPath
     * @return string
     */
    public static function getImagePath($thumbnailPath) {
        $newName = preg_replace("#upload/thumbs/#", "upload/", dirname($thumbnailPath)) . "/" . preg_replace("/_\d+x\d+\.(jpg|png|gif|JPG|PNG|GIF)$/", ".\\1", basename($thumbnailPath));
        return $newName;
    }

    /**
     *
     * @param string $file absolute path to image file
     * @param <type> $type absolute path to image file dir (to be removed)
     * @param <type> $force whether to force writing over existing thumbs
     */
    public static function regenerateThumbs($file, $force = false) {
        if (substr($file, 0, strlen(self::getWebRoot())) == self::getWebRoot()) { // absolute_path
            $file = substr($file, strlen(self::getWebRoot()));
        }
        $file = preg_replace("#//#", "/", $file); // get rid of double slashes if present
        $thumbnailSizes = self::getThumbnailSizes($file);
        $destinationDir = self::getWebRoot() . preg_replace("#upload/#", "upload/thumbs/", dirname($file));
        if (!file_exists($destinationDir)) {
            mkdir($destinationDir, 0775, true);
        }
        foreach ($thumbnailSizes as $size) {
            if (!file_exists(getThumbPath($file, $size)) || $force) {
                thumbnailer::create_thumbnail(self::getWebRoot() . $file, $destinationDir, $size);
            }
        }
    }

    /**
     * Returns absolute path to settings json file
     * @return string
     */
    public static function getSettingsFilePath() {
        return self::getWebRoot() . "/ocp/config/thumbnails.json";
    }

    public static function getImageResource($src) {
        $image_res = false;
        if (file_exists($src) && !is_dir($src)) {
            $extension = trim(strrchr($src, "."), ".");
            switch ($extension) {
                case "jpg" :
                case "jpeg" :
                case "jpe" :
                case "JPG" :
                case "JPEG" :
                case "JPE" :
                    $type = "jpeg";
                    $image_res = imagecreatefromjpeg($src);
                    break;
                case "gif" :
                case "GIF" :
                    $type = "gif";
                    $image_res = imagecreatefromgif($src);
                    break;
                case "png" :
                case "PNG" :
                    $type = "png";
                    $image_res = imagecreatefrompng($src);
                    break;
                case "wbmp" :
                case "WBMP" :
                    $type = "jpeg";
                    $image_res = imagecreatefromwbmp($src);
                    break;
            }

        }
        return $image_res;
    }

    public static function applyWatermark($src, $watermark, $position = "br") {
        if (file_exists($src) && !is_dir($src) && file_exists($watermark) && !is_dir($watermark)) {
            $extension = trim(strrchr($src, "."), ".");
            $srcDims = getimagesize($src);
            if ($srcDims[0] > 600) {
                $wmDims = getimagesize($watermark);
                $srcW = $srcDims[0];
                $srcH = $srcDims[1];
                $wmW = $wmDims[0];
                $wmH = $wmDims[1];
                if ($wmW < 0.4*$srcW) {
                    $wmScale = 1;
                } else {
                    $wmScale = 0.4*$srcW / $wmW;
                }
                $srcImg = self::getImageResource($src);
                $wmImg = self::getImageResource($watermark);
                imagesavealpha($wmImg, true);
                imagealphablending($wmImg, false);
                if ($wmScale) {
                    $newW = ceil($wmW * $wmScale);
                    $newH = ceil($wmH * $wmScale);
                    $newWmImage = imagecreatetruecolor($newW, $newH);
                    $transparent = imagecolorallocatealpha($newWmImage, 0, 255, 0, 127);
                    imagecolortransparent($newWmImage, $transparent);
                    imagerectangle($newWmImage, 0, 0, $newW, $newH, $transparent);
                    imagealphablending($newWmImage, false);
                    imagesavealpha($newWmImage, true);
                    imagecopyresampled($newWmImage, $wmImg, 0, 0, 0, 0, $newW, $newH, $wmW, $wmH);
                    imagedestroy($wmImg);
                    $wmImg = $newWmImage;
                    $srcX = $srcW - 10 - $newW;
                    $srcY = $srcH - 3 - $newH;
                } else {
                    $srcX = $srcW - 10 - $wmW;
                    $srcY = $srcH - 3 - $wmH;
                    $newW = $wmW;
                    $newH = $wmH;
                }
                imagecopyresampled($srcImg, $wmImg, $srcX, $srcY, 0, 0, $newW, $newH, $newW, $newH);
                //header("Content-Type:image/jpeg");
                imagejpeg($srcImg, $src, THUMBNAIL_IMAGE_QUALITY);
                imagedestroy($srcImg);
            }
        }
    }
}
?>