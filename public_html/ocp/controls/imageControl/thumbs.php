<?php
ob_start();
require_once("../../include/connect.php");
require_once("../../include/session.php");
require_once("settings.php");
header("Cache-Control: no-store, no-cache, must-revalidate"); //http 1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 
$webRoot = realpath(dirname(__FILE__) . "/../../.."); //root web site-a
$type = getRVar("objType");

$thumbnailSizes = thumbnailer::getThumbnailSizes(getRVar("file"));
$thumbnailNames = array_keys($thumbnailSizes);

if (getRVar("reset")) {
    $size = getRVar("size");
    $file = $webRoot . getRVar("file");
    $destinationDir = preg_replace("#upload/#", "upload/thumbs/", dirname($file));
    echo $file;
    if (!file_exists($destinationDir)) {
        mkdir($destinationDir, 0775, true);
    }
    thumbnailer::create_thumbnail($file, $destinationDir, $size);
} else if (getRVar("resetAll")) {
    $file = $webRoot . getRVar("file");
    $type = getRVar("objType");
    thumbnailer::regenerateThumbs($file, $type, true);
}
$file = getRVar("file");
?><html>
    <head>
    </head>
    <body>
        <table>
            <tr>
                <?php
                foreach($thumbnailSizes as $name => $size) {
                    ?>
                <td align="center" valign="bottom">
                    <img src="/upload/thumbs/getthumb.php?file=<?php echo getThumbPath($file, $size);?>&rnd=<?php echo time();?>" alt="" /><br />
                </td>
                    <?php
                }
                ?>
            </tr>
            <tr>
                <?php
                foreach($thumbnailSizes as $name => $size) {
                    ?>
                <td align="center" valign="bottom" style="background:#eee">
                        <?php echo $size; ?><br />
                    <span style="color:#666; font:10px/12px Arial"><?php echo $name; ?></span>
                </td>
                    <?php
                }
                ?>
            </tr>
            <tr>
                <?php
                foreach($thumbnailSizes as $name => $size) {
                    ?>
                <td align="center" valign="bottom" style="background:#eee">
                    <input type="button" id="crop" name="crop" value="<?php echo ocpLabels("Crop")?>" class="ocp_forma" align="center" onClick="openCropWindow('<?php echo $size;?>');"/>
                    <p /><input rel="<?php echo $file;?>" type="button" name="reset_<?php echo $size;?>" id="reset" value="<?php echo ocpLabels("Reset");?>" onClick="resetThumb(this)" />

                </td>
                    <?php
                }
                ?>
            </tr>
            <tr>
                <td colspan="<?php echo count($thumbnailSizes);?>" align="center">
                    <p />
                    <p />
                    <input type="button" rel="<?php echo $file;?>" name="regenerate" id="regenerate" value="<?php echo ocpLabels("RegenerateAll");?>" onClick="resetAll(this)" />
                </td>
            </tr>
        </table>
        <script type="text/javascript">
            function openCropWindow(size){
                document.location = 'crop.php?random=' + Math.random() + '&objType=<?php echo $type;?>&size=' + size + '&file=<?php echo urlencode($file); ?>';
            }
            function resetThumb(element){
                var size = element.name.replace(/reset_/, '');
                var file = element.getAttribute('rel');
                document.location = 'thumbs.php?reset=1&file=' + file + '&size=' + size + '&objType=<?php echo $type;?>';
            }
            function resetAll(element){
                var file = element.getAttribute('rel');
                document.location = 'thumbs.php?resetAll=1&file=' + file + '&objType=<?php echo $type; ?>';
            }
        </script>
    </body>
</html>
<?php
ob_end_flush();
?>