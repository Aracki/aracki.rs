<?php
ob_start();
require_once("../../include/connect.php");
require_once("../../include/session.php");
require_once("thumbnailer.class.php");
require_once("settings.php");

$ratio = 0;
$webRoot = realpath("../../.."); //root web site-a
$size = getRVar("size");
$type = getRVar("objType");
$dims = preg_split("/x/", $size);
$file = getRVar("file");
$src = urldecode(getRVar("file"));
$disksrc = $webRoot . "/" . $src;
$srcDims = getimagesize($webRoot . "/" . $file);
if ($dims[1] == 0) {
    $ratio = 0;//(float) $srcDims[0]/$srcDims[1];
} else {
    $ratio = (float)$dims[0] / $dims[1];
}


if (getRVar("doCrop")) {
    $targ_w = $dims[0];
    $targ_h = $dims[1];

    if ($dims[1] == 0) {
        $targ_h = intval(($targ_w / intval($_POST["w"])) * intval($_POST["h"]));
        var_dump($targ_h);
        var_dump($targ_w);
        //exit;
        //$targ_h = intval($srcDims[1] * ((float)$dims[0]/$srcDims[0]));
    }

    $jpeg_quality = 95;

    if (preg_match("/\.(jpg|JPG)$/", $disksrc)) {
        $img_r = imagecreatefromjpeg($disksrc);
    } else if (preg_match("/\.(png|PNG)$/", $disksrc)) {
        $img_r = imagecreatefrompng($src);
    } else if (preg_match("/\.(gif|GIF)$/", $disksrc)) {
        $img_r = imagecreatefromgif($disksrc);
    }
    $dst_r = imagecreatetruecolor( $targ_w, $targ_h );
    imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
            $targ_w,$targ_h,$_POST['w'],$_POST['h']);
    imagejpeg($dst_r, $webRoot . getThumbPath($file, $size) ,$jpeg_quality);

    ?>
<script type="text/javascript">
    document.location='thumbs.php?file=<?php echo $file;?>&objType=<?php echo $type;?>';
</script>
    <?php
} else {

    ?><html>
    <head>
        <script type="text/javascript" src="js/jquery.min.js"></script>
        <script type="text/javascript" src="js/jquery.Jcrop.min.js"></script>
        <link rel="stylesheet" href="css/jcrop/jquery.Jcrop.css" type="text/css" />
    </head>
    <body>
        <table>
            <tr>
                <td>
                    <img src="<?php echo $src;?>" id="cropbox" alt="" />
                    <form action="crop.php" method="post" onsubmit="return checkCoords();">
                        <input type="hidden" id="x" name="x" />
                        <input type="hidden" id="y" name="y" />
                        <input type="hidden" id="w" name="w" />
                        <input type="hidden" id="h" name="h" />
                        <input type="hidden" name="objType" value="<?php echo $type;?>" />
                        <input type="hidden" name="file" value="<?php echo $file;?>" />
                        <input type="hidden" name="size" value="<?php echo $size;?>" />
                        <input type="hidden" name="doCrop" value="1" />
                        <input type="submit" value="<?php echo ocpLabels("CropImage"); ?>" />
                        <input type="button" value="<?php echo ocpLabels("Cancel"); ?>" onclick="document.location='thumbs.php?file=<?php echo $file;?>&objType=<?php echo $type;?>'" />
                    </form>
                </td>
                    <?php if ($dims[1] != 0) {
                        ?><td>
                    <div style="width:<?php echo $dims[0];?>px;height:<?php echo $dims[1];?>px;overflow:hidden;">
                        <img src="<?php echo $file;?>" id="preview" />
                    </div>
                </td>
                        <?php

                    }
                    ?>
            </tr>
        </table>
        <script type="text/javascript">
            var hasHeight = <?php echo $dims[1] == 0 ? "false" : "true";?>;
            var jq = $;
            $(document).ready(function(){
                $ = jq;
                jQuery = jq;
                $('#cropbox').Jcrop({
                    aspectRatio: <?php echo $ratio; ?>,
                    onChange: showPreview,
                    //maxSize: [<?php echo $dims[0];?>, <?php echo $dims[1];?>],  d
                    onSelect: updateCoords

                });
            });
            function updateCoords(c) {
                $('#x').val(c.x);
                $('#y').val(c.y);
                $('#w').val(c.w);
                $('#h').val(c.h);
            };

            function checkCoords() {
                if (parseInt($('#w').val())) return true;
                alert('<?php echo ocpLabels("Please select a crop region then press submit.");?>');
                return false;
            };

            function showPreview(coords){
                if (hasHeight && parseInt(coords.w) > 0){
                    var rx = <?php echo $dims[0];?>/coords.w;
                    var ry = <?php echo $dims[1];?>/coords.h;
                    jQuery('#preview').css({
                        width: Math.round(rx * <?php echo $srcDims[0];?>) + 'px',
                        height: Math.round(ry * <?php echo $srcDims[1];?>) + 'px',
                        marginLeft: '-' + Math.round(rx * coords.x) + 'px',
                        marginTop: '-' + Math.round(ry * coords.y) + 'px'
                    });
                }
            }
        </script>
    </body>
</html>
    <?php
}
ob_end_flush();
?>