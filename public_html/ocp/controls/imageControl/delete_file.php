<?php

require_once("../../include/connect.php");
require_once("../../include/session.php");
require_once("../../include/file.php");
require_once("../../include/polja.php");
require_once("../../include/objekti.php");
require_once("../../include/tipoviobjekata.php");
require_once("../../include/selectradio.php");
require_once("../../include/xml.php");
require_once("../../include/xml_tools.php");
require_once("../../include/design/table.php");
require_once("settings.php");


$webRoot = realpath("../../.."); //root web site-a

$fileName = utils_requestStr(getGVar("fileName")); 
$fileName = str_replace("//", "/", $fileName);
$action = utils_requestStr(getPVar("Akcija")); 
$message = "";

if ($action == "Obrisi") {
    $message = file_delete(	utils_requestStr(getPVar("fileName")),
            utils_requestInt(getPVar("KillLink")),
            utils_requestStr(getPVar("NewLink")));
    foreach($thumbnailSizes as $size) {
        @unlink($webRoot . getThumbPath(utils_requestStr(getPVar("fileName")), $size));
    }
} 
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="stylesheet" href="/ocp/css/opsti.css">
        <link rel="stylesheet" href="/ocp/css/opcije.css">
    </head>
    <body class="ocp_body">
        <?php
        if ($message != "") { ?>
        <script>
            parent.listFrame.location.reload(true);
            window.open("/ocp/html/blank.html", "previewFrame");
        </script><?php
            require_once("../../include/design/message.php");
            echo( message_info($message));
        } else {
            ?><script>
                    window.onload = function(){
                        parent.document.getElementById("resizableFrameset").setAttribute("rows", "*,250");
                    }
        </script><?php

            $objects = file_getAllLinked($fileName);

            ?><form action="delete_file.php?<?php echo utils_randomQS();?>" method="post" name="formObject" id="formObject" onSubmit="return validate();">
            <input type="hidden" value="Obrisi" name="Akcija">
            <input type="hidden" name="fileName" value="<?php echo $fileName?>">
            <div id="ocp_blok_menu_1">
                <table class="ocp_blokovi_table">
                    <tr>
                        <td class="ocp_blokovi_td">
                            <img src="/ocp/img/opsti/razno/hint.gif" style="vertical-align: bottom; margin-left: 3px;">
                                <?php					if (count($objects) > 0) {
                                    echo (ocpLabels("List of objects which contain link to this file"));
                                } else {
                                    echo (ocpLabels("There are no objects which contain link to this file"));
                                }	?>
                        </td></tr>
                </table>
            </div>
                <?php			if (count($objects) > 0) {	?>
            <div id="ocp_blok_menu_2">
                <table class="ocp_opcije_table">
                            <?php				for ($i = 0; $i < count($objects); $i++) {
                                $next = $objects[$i];
                                $tekst = $next["Label"];

                                if (utils_strlen($tekst) > 100) {
                                    $tekst = utils_substr($tekst, 0, 100)." ...";
                                } else if ($tekst == "null") {
                                    $tekst = "";
                                }
                                ?>					<tr>
                        <td class="ocp_opcije_td" style="width: 30px; text-align: right;"><span class="ocp_opcije_tekst1">#<?php echo $next["Id"]; ?></span></td>
                        <td class="ocp_opcije_td" style="width: 40px; text-align: center;"><span class="ocp_opcije_tekst1"><?php echo $next["TypeLabel"]; ?></span></td>
                        <td class="ocp_opcije_td"><span class="ocp_opcije_tekst2"><?php echo $tekst; ?></span></td></tr>
                                <?php
                            }
                            ?>
                </table>
            </div>

            <div id="ocp_blok_menu_1">
                <table class="ocp_blokovi_table">
                    <tr><td class="ocp_blokovi_td"><?php echo ocpLabels("FILE"); ?></td></tr>
                </table>
            </div>
            <table class="ocp_opcije_table">
                        <?php echo table_option_checkbox(ocpLabels("Remove link"), "KillLink", "", array("1", ocpLabels("Yes")), false, 2); ?>
                        <?php echo table_option_intLink(ocpLabels("Replace link"), "NewLink", "", false, $stranica["Stra_Id"], "/upload", 2); ?>
            </table>
                    <?php
                } else {
                    ?>
                    <?php require_once("../../include/design/message.php"); ?>
                    <?php echo message_info(ocpLabels("ARE YOU SURE YOU WANT TO DELETE FILE")."?"); ?>
                    <?php
                }

                table_option_submit(ocpLabels("Confirm"), ocpLabels("Cancel"));
                ?>
        </form>

        <script src="/ocp/validate/validate_double_quotes.js"></script>
        <script>
            function validate(){
                var value = true;
                var forma = document.formObject;

                if (forma.KillLink != null){
                    if (!forma.KillLink.checked && (forma.NewLink.value == '')){
                        alert("<?php echo ocpLabels("You must either remove either replace link"); ?>.");
                        value = false;
                    }
                } else { // nema linkova na ovaj file
                    return true;
                }

                if (value){
                    validate_double_quotes(document.formObject);
                }

                return value;
            }
        </script><?php
        }
        ?></body>
</html>