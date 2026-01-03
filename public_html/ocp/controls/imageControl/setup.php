<?php
require_once("../../include/connect.php");
require_once("../../include/session.php");
require_once("settings.php");
?><html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="stylesheet" href="/ocp/css/opsti.css">
        <link rel="stylesheet" href="/ocp/css/opcije.css">
        <script type="text/javascript" src="js/jquery.min.js"></script>
        <script type="text/javascript" src="js/json2.js"></script>
    </head>
    <body class="ocp_body">
        <?php
        $currentSetup = thumbnailer::getThumbnailSetup();
        $json = new Services_JSON();
        $settingsPath = thumbnailer::getSettingsFilePath();
        if(!file_exists($settingsPath)){
            echo "file" . thumbnailer::getSettingsFilePath() . " doesn't exist";
            exit;
        } else if (is_dir($settingsPath)){
            echo "file" . thumbnailer::getSettingsFilePath() . " is a directory";
            exit;
        } else if (!is_writable($settingsPath)) {
            echo "file" . thumbnailer::getSettingsFilePath() . " is not writable";
            exit;
        }
        ?>
        <form name="thumbnail_settings" action="#" method="post">
            <input type="hidden" name="type" value="<?php echo getRVar("type"); ?>" />
            <ul id="type_sample" style="display:none">
                <li data-path="">
                    <h4>/upload</h4>
                    <div><label><?php echo OcpLabels("Path"); ?></label>
                        <input data-path="/upload" type="text" name="path" value="/upload" />
                    </div>
                    <a href="#" class="addSize"><?php echo OcpLabels("Add size"); ?></a>
                    <ol>
                        <li class="size" data-path="/upload">
                            <ul>
                                <li>
                                    <label><?php echo OcpLabels("Width"); ?></label>
                                    <input data-path="/upload" type="text" name="width" value="0" />
                                </li>
                                <li>
                                    <label><?php echo OcpLabels("Height"); ?></label>
                                    <input data-path="/upload" type="text" name="height" value="0" />
                                </li>
                            </ul>
                            <a href="#" class="removeSize"><?php echo OcpLabels("Remove size"); ?></a>
                        </li>
                    </ol>
                    <a href="#" class="removeType"><?php echo OcpLabels("Remove type"); ?></a>
                </li>
            </ul>
            <ul id="types">
                <?php
                foreach ($currentSetup as $typeSetup) {
                ?>
                    <li data-path="<?php echo $typeSetup->name; ?>">
                        <h4><?php echo $typeSetup->name ?></h4>
                        <div>
                            <label><?php echo OcpLabels("Path"); ?></label>
                            <input data-path="/upload" type="text" name="path" value="<?php echo $typeSetup->name; ?>" />
                        </div>
                        <a href="#" class="addSize"><?php echo OcpLabels("Add size"); ?></a>
                        <ol>
                        <?php
                        foreach ($typeSetup->sizes as $size) {
                        ?>
                            <li class="size" data-path="<?php echo $typeSetup->name; ?>">
                                <ul>
                                    <li>
                                        <label><?php echo OcpLabels("Width"); ?></label>
                                        <input data-path="<?php echo $typeSetup->name; ?>" type="text" name="width" value="<?php echo $size->width; ?>" />
                                    </li>
                                    <li>
                                        <label><?php echo OcpLabels("Height"); ?></label>
                                        <input data-path="<?php echo $typeSetup->name; ?>" type="text" name="height" value="<?php echo $size->height; ?>" />
                                    </li>
                                </ul>
                                <a href="#" class="removeSize"><?php echo OcpLabels("Remove size"); ?></a>
                            </li>
                        <?php
                        }
                        ?>
                    </ol>
                    <a href="#" class="removeType"><?php echo OcpLabels("Remove type"); ?></a>
                </li>
                <?php
                    }
                ?>
                </ul>
                <a href="#" class="addType"><?php echo OcpLabels("Add path"); ?></a>
                <input id="save" type="submit" value="save" />
            </form>
            <script type="text/javascript">
                var sizeClone = $('.size:eq(0)').clone();
                sizeClone.find(':input').val('');
                $('.addSize').live('click', function(e){
                    e.preventDefault();
                    var sizeClone = $('#type_sample .size:eq(0)').clone(true);
                    var newSizeElem = $(sizeClone).clone();
                    var editingElem = $(newSizeElem).appendTo($(this).nextAll('ol:eq(0)'));
                });
                $('.addType').live('click', function(e){
                    e.preventDefault();
                    var sizeClone = $('#type_sample li:eq(0)').clone(true);
                    var newSizeElem = $(sizeClone).clone();
                    $(newSizeElem).addClass("editing");
                    var editingElem = $(newSizeElem).appendTo('#types');
                });
                $('.removeSize').live('click', function(e){
                    e.preventDefault();
                    if (confirm('<?php echo OcpLabels("Are you sure"); ?>')){
                        $(this).parents("li:eq(0)").remove();
                    }
                });
                $('.removeType').live('click', function(e){
                    e.preventDefault();
                    if ($(this).parents("li:eq(0)").attr('data-path') != 'default'){
                        if (confirm('<?php echo OcpLabels("Are you sure"); ?>')){
                        $(this).parents("li:eq(0)").remove();
                    }
                }
            });
            $('input').live('keyup', function(e){
                var field = $(this);
                if ($(field).attr('name') == 'path'){
                    $(field).parents('li:eq(0)').find('h4:eq(0)').html($(field).val());
                } else if ($(field).attr('name') == 'height' || $(field).attr('name') == 'width'){
                    if ($(field).val().match(/[^\d]/)){
                        var value = $(field).val();
                        while(value.match(/[^\d]/)){
                            value = value.replace(/[^\d]+/, '');
                        }
                        $(field).val(value);
                    }
                }
            });
            function serialize(){
                var root = $('#types');
                var serialized = [];
                var typeNodes = $(root).find('> li');
                $(typeNodes).each(function(i, typeNode){
                    var typeSetting = {};
                    if ($(typeNode).find('input[name=path]').val().match(/\/$/)){
                        $(typeNode).find('input[name=path]').val($(typeNode).find('input[name=path]').val().replace(/\/$/, ''));
                    }
                    typeSetting.name = $(typeNode).find('input[name=path]').val();
                    var sizes = $(typeNode).find('.size');
                    typeSetting.sizes = [];
                    $(sizes).each(function(i, sizeNode){
                        var size = {};
                        size.width = $(sizeNode).find('input[name=width]').val();
                        size.height = $(sizeNode).find('input[name=height]').val();
                        size.name = size.width + "x" + size.height;
                        typeSetting.sizes.push(size);
                    });
                    serialized.push(typeSetting);
                });
                return serialized;
            }
            $('form:eq(0)').submit(function(e){
                e.preventDefault();
                var serialized = serialize();
                var serializedJson = JSON.stringify(serialized);
                $.ajax({
                    url: '/ocp/controls/imageControl/saveSetup.php',
                    type: 'post',
                    data: 'json=' + serializedJson,
                    success : function(response){
                        if (response == 'true'){
                            alert('<?php echo ocpLabels("Thumbnail settings saved");?>');
                        } else {
                            alert('<?php echo ocpLabels("Thumbnail settings could not be saved");?>');
                        }
                    }
                })
            });

        </script>
    </body>
</html>