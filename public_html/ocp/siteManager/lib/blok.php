<?php
require_once("../ocp/include/connect.php");
require_once("../ocp/include/selectradio.php");
require_once("../ocp/include/tipoviobjekata.php");
require_once("../ocp/include/objekti.php");
require_once("../ocp/include/polja.php");
require_once("../ocp/include/xml.php");
require_once("../ocp/include/language.php");
require_once("../ocp/include/xml_tools.php");
require_once("stranica.php");
require_once("template.php");
require_once("tipoviblokova.php");
require_once("blokfunctions.php");

$StraId = utils_requestInt(getGVar("Id"));
$editMode = utils_requestStr(getGVar("editor"));
//kada se salje post kroz navigate.php	
if (!utils_valid($StraId) || ($StraId == 0)) $StraId = utils_requestInt(getPVar("Id"));

$EditBlokId = "";
$StBl_Id = "";

if (($editMode == "1") && (is_null($_SESSION["ocpUserGroup"]))) $editMode = "0";

//nizovi potrebni za validaciju
$validateFunc = array();
$validateFuncCall = array();

if (!utils_valid($StraId) || ($StraId == 0)) { //da radimo insert / update bloka
    $StraId = utils_requestInt(getPVar("Stra_Id"));
    $stranica = stranica_get($StraId);
    $editMode = "1";
    $newBlokId = blokFn_save($StraId);
    ?><script>window.onload = function(){ this.location.href += "#<?php echo $newBlokId;?>"; }</script><?php
} else {//ili ne
    $stranica = stranica_get($StraId);
}

if (is_null($editMode)) $editMode = "0";

if ($editMode == "1") {	//sta nam je sve potrebno da ako smo u edit modu
    jsBlockOcpLabels();
    ?><link rel="stylesheet" href="/ocp/css/blok_traka.css">
<link rel="stylesheet" href="/ocp/css/opsti.css">
<link rel="stylesheet" href="/ocp/css/opcije.css">
<script src="/ocp/jscript/move.js"></script>
<script src="/ocp/jscript/load.js"></script>
<script>
    var thisPageUrl = "<?php echo blokFn_getUrlTemplate($stranica);?>";
    function openPopup(id, type){
        x = window.open( "/ocp/siteManager/framesetSmall.php?<?php echo utils_randomQS();?>&Id="+id+"&Type="+type,"frameSmall", "height=400, width=630, resizable=yes");
        parent.popupWin = x;
    }
    function changeOrderBlok(StBl_Id1, StBl_Id2, Akcija){
        window.open(thisPageUrl+"&StBl_Id1="+StBl_Id1+"&StBl_Id2="+StBl_Id2+"&Akcija="+Akcija, "detailFrame");
    }
    function deleteBlok(Blok_Id, StBl_Id){
        x = confirm("<?php echo ocpLabels("Are you sure you want to delete block");?>?");
        if (x){
            window.open(thisPageUrl+"&Blok_Id="+Blok_Id+"&StBl_Id="+StBl_Id+"&Akcija=Obrisi", "detailFrame");
        }
    }
    simpleEditorExists = false;
    var simpleEditorArr = new Array();
</script>
    <?php	switch (getGVar("Akcija")) {
        case "Pomeri": //ako je stigao zahtev za pomeranje bloka
        case "Kopiraj": //ako je stigao zahtev za kopiranje bloka
            blokFn_changeOrder($StraId, utils_requestInt(getGVar("StBl_Id1")), utils_requestInt(getGVar("StBl_Id2")), utils_requestStr(getGVar("Akcija")));
            break;
        case "Obrisi": //ako je stigao zahtev za brisanje bloka
            blokFn_delete($StraId, utils_requestInt(getGVar("Blok_Id")), utils_requestInt(getGVar("StBl_Id")));
            break;
        case "Share": //dodavanje sharovanog bloka
            $BlokId = utils_requestInt(getGVar("Blok_Id"));
            $SharedBlokId = blokFn_saveShare($BlokId, $stranica["Stra_Id"]);
            ?><script>window.onload = function(){ this.location.href += "#<?php echo $SharedBlokId;?>"; }</script><?php
            break;
        case "Uredi": //ako je stigao zahtev za editovanje nekog bloka
            $EditBlokId = utils_requestInt(getGVar("Blok_Id"));
            $StBl_Id = utils_requestInt(getGVar("StBl_Id"));
            break;
    }
}

$blokovi = stranica_getAllBlok($StraId, null, $editMode);
for ($i=0;$i<count($blokovi);$i++) {
//utils_dump("prikaz bloka - start <br>");
    blok_display($stranica, $blokovi[$i]);
//utils_dump("prikaz bloka - end <br>");
}
if (($editMode == "1") && !utils_valid($StBl_Id)) { ?>
<table class="ocp_blok_traka_table" id="blocks__last_position" style="visibility:hidden;cursor:pointer">
    <tr>
        <td width="100%" class="ocp_blok_traka_td"  onmousedown="ocpMoveNodeEnd(this);"
            onmouseover="ocpMoveNodeHigh(this);" onmouseout="ocpMoveNodeReset(this);" id="blocks__last"><span class="ocp_blok_traka_tekst_pom">&nbsp;<?php echo ocpLabels("CLICK HERE TO MOVE BLOCK ABOVE THIS BLOCK");?></span></td>
    </tr>
</table><?php	
}	

if (($editMode == "1") && ((getGVar("Akcija")) == "Novi"))
    blok_displayNew($stranica, utils_requestInt(getGVar("TipB_Id")));

//sta nam je jos ostalo neophodno za edit
if (($editMode == "1") && (($EditBlokId != "") || (getGVar("Akcija") == "Novi"))) {
    ?><script>
    function checkMetaNaziv(){
        var x = document.formObject;
        if (x.Blok_MetaNaziv != null){
            if (x.Blok_MetaNaziv.value != ""){
    <?php
    $lists = blokFn_getAllMetaNaziv($EditBlokId);
    for ($j=0; $j<count($lists); $j++) {
        if ($j==0) echo("				old = new Array(");
        if ($j != (count($lists)-1)) echo('"'.$lists[$j].'",');
        else echo('"'.$lists[$j].'");');
    }

    if (count($lists) == 0)	echo("				old = new Array();");
    echo("\n");	?>
                                        for (var i=0; i<old.length; i++){
                                            if (x.Blok_MetaNaziv.value == old[i].replace(/&quot;/g, "\"")){
                                                alert("<?php echo ocpLabels("Shared block already exist under name");?> "+x.Blok_MetaNaziv.value);
                                                return false;
                                            }
                                        }
                                    }
                                }
                                return true;
                            }

                            function changeForm(){
                                var x = document.forms[0];
                                for (var i=0; i< x.elements.length; i++){
                                    var y = x.elements[i];
                                    if (y.type != "hidden") y.value = unescape(y.value);
                                }
                            }

                            window.onload = function() { changeForm(); }
</SCRIPT>
    <?php
    utils_getValidation($validateFunc, $validateFuncCall, 1);
}

/*	FUNCTION SECTION */

function blok_display($stranica, $blok) {
    global $EditBlokId, $StraId, $editMode, $StBl_Id, $validateFuncCall, $validateFunc;

    $xmlDoc = xml_loadXML($blok["Blok_XmlPodaci"]);

    //bookmark
    ?><a name="<?php echo $blok["StBl_Id"];?>"></a><?php
    if (($editMode == "1") && ($StBl_Id != $blok["StBl_Id"])) {
        //editor , ali ovaj se ne edituje

        $urlEdita = blokFn_getUrlTemplate($stranica)."&random=".time()."&Blok_Id=".$blok["Blok_Id"]."&Akcija=Uredi&StBl_Id=".$blok["StBl_Id"]."#" .$blok["StBl_Id"];
        if (!utils_valid($StBl_Id) || ($StBl_Id == 0)) {
            ?><script>ocpMoveList[ocpMoveList.length] = "blocks__<?php echo $blok["StBl_Id"];?>";</script><?php
        }
        ?>
<table class="ocp_blok_traka_table" id="blocks__<?php echo $blok["StBl_Id"];?>" style="cursor:auto" <?php if (!utils_valid($StBl_Id)) {?>onmousedown="ocpMoveNodeEnd(this);" onmouseover="ocpMoveNodeHigh(this);" onmouseout="ocpMoveNodeReset(this);"<?php } ?>>
    <tr>
        <td width="62%" class="ocp_blok_traka_td">
            <div id="blocks__<?php echo $blok["StBl_Id"];?>_div" style="display:block;">
                <table class="drzac_dugmica">
                    <tr>
                        <td onclick="javascript:window.open('<?php echo $urlEdita;?>', '_self')" style="cursor:pointer;" class="ocp_blok_traka_td"><img src="/ocp/img/opsti/blokovi/blok_traka/edituj_blok.gif" class="ocp_blok_traka_ikona" title="<?php echo ocpLabels("Edit block");?>"></td>
                        <td class="ocp_blok_traka_td_tekst" onclick="javascript:window.open('<?php echo $urlEdita;?>', '_self')" style="cursor:pointer;"><?php echo ocpLabels("Edit block");?></td>
                        <td class="ocp_blok_traka_td"><img src="/ocp/img/opsti/blokovi/blok_traka/crtka.gif" width="2" height="28" class="ocp_gornji2_crtka"></td>
                        <td <?php if (!utils_valid($StBl_Id)) { ?>onclick="ocpMoveNodeStart('blocks', '<?php echo($blok["StBl_Id"]);?>', 'blocks__<?php echo($blok["StBl_Id"]);?>', 'move');" style="cursor:pointer;"<?php }?>><img src="/ocp/img/opsti/blokovi/blok_traka/cut_blok.gif" class="ocp_blok_traka_ikona" title="<?php echo ocpLabels("Move block");?>"></td>
                        <td class="ocp_blok_traka_td_tekst" <?php if (!utils_valid($StBl_Id)) {?>onclick="ocpMoveNodeStart('blocks', '<?php echo ($blok["StBl_Id"]);?>', 'blocks__<?php echo($blok["StBl_Id"]);?>', 'move');" style="cursor:pointer;"<?php } ?>><?php echo ocpLabels("Move block");?></td>
                        <td class="ocp_blok_traka_td"><img src="/ocp/img/opsti/blokovi/blok_traka/crtka.gif" width="2" height="28" class="ocp_gornji2_crtka"></td>
                        <td <?php if (!utils_valid($StBl_Id)) {?>onclick="ocpMoveNodeStart('blocks', '<?php echo($blok["StBl_Id"]);?>', 'blocks__<?php echo ($blok["StBl_Id"]);?>', 'copy');" style="cursor:pointer;"<?php }?> class="ocp_blok_traka_td"><img src="/ocp/img/opsti/blokovi/blok_traka/copy_blok.gif" class="ocp_blok_traka_ikona" title="<?php echo ocpLabels("Copy block");?>"></td>
                        <td class="ocp_blok_traka_td"><img src="/ocp/img/opsti/blokovi/blok_traka/crtka.gif" width="2" height="28" class="ocp_gornji2_crtka"></td>
                        <td onclick="deleteBlok('<?php echo $blok["Blok_Id"];?>', '<?php echo $blok["StBl_Id"];?>');" style="cursor:pointer;" class="ocp_blok_traka_td"><img src="/ocp/img/opsti/blokovi/blok_traka/obrisi_blok.gif" class="ocp_blok_traka_ikona" title="<?php echo ocpLabels("Delete block");?>">
                        </td>
                    </tr>
                </table></div>
            <div id="blocks__<?php echo($blok["StBl_Id"]);?>_div1" class="ocp_blok_traka_tekst_pom" style="display:none;cursor:pointer">&nbsp;<?php echo ocpLabels("CLICK HERE TO MOVE BLOCK ABOVE THIS BLOCK");?></div>
        </td>
        <td id="blocks__<?php echo $blok["StBl_Id"];?>_div2" class="ocp_blok_traka_td_info_desni">
            <table class="drzac_dugmica_desni">
                <tr><?php
                            $vidljivostImg = "";
                            $vidljivostText="";
                            $blok = date_setPublishExpiry($blok, "Blok_PublishDate", "Blok_ExpiryDate"); //datum
                            if (isset($blok["Valid"]) && ($blok["Valid"] == "red")) {
                                $vidljivostImg = "blok_crveni_kalendar";
                                $vidljivostText = ocpLabels("Block has expired");
                            } else if (isset($blok["Valid"]) && ($blok["Valid"] == "green")) {
                                $vidljivostImg = "blok_zeleni_kalendar";
                                $vidljivostText = ocpLabels("Block is not published yet");
                            }
        if (utils_valid($vidljivostImg)) {	?>
                    <td><table class="dugme">
                            <tr>
                                <td class="ocp_blok_traka_td"><img src="/ocp/img/opsti/blokovi/blok_traka/<?php echo $vidljivostImg;?>.gif" class="ocp_blok_traka_ikona" title="<?php echo $vidljivostText;?>"></td>
                            </tr>
                        </table></td>
                    <td class="ocp_blok_traka_td"><img src="/ocp/img/opsti/blokovi/blok_traka/crtka.gif" width="2" height="28" class="ocp_gornji2_crtka"></td>
                                <?php	}
        if ($blok["Blok_Share"] == "1") {	?>
                    <td><table class="dugme">
                            <tr>
                                <td class="ocp_blok_traka_td"><img src="/ocp/img/opsti/blokovi/ikone/mali/deljeni.gif" class="ocp_blok_traka_ikona" title="<?php echo (utils_substr($blok["Blok_MetaNaziv"], 0, 25));?>"></td>
                                <td class="ocp_blok_traka_td_desno_tekst"><span class="ocp_blok_traka_bold"><?php echo (utils_substr($blok["Blok_MetaNaziv"], 0, 25));?></span></td>
                            </tr>
                        </table></td>
                    <td class="ocp_blok_traka_td"><img src="/ocp/img/opsti/blokovi/blok_traka/crtka.gif" width="2" height="28" class="ocp_gornji2_crtka"></td>
            <?php	}	?>
                    <td><table class="dugme">
                            <tr>
                                <td class="ocp_blok_traka_td"><img src="<?php echo $blok["TipB_SlikaUrl"];?>" width="20" height="17" title="<?php echo ocpLabels($blok["TipB_Naziv"]);?>"></td>
                            </tr>
                        </table></td>
                    <td class="ocp_blok_traka_td"><img src="/ocp/img/opsti/blokovi/blok_traka/crtka.gif" width="2" height="28" class="ocp_gornji2_crtka"></td>
                    <td class="ocp_blok_traka_td_desno_tekst">#<?php echo ($blok["StBl_Id"]);?> </td>
                </tr>
            </table>
        </td>
    </tr>
</table><?php

        if ($blok["TipB_Dinamic"] == "0") { //ne edituje se i statican je blok
//utils_dump("prikaz bloka - inner ne edituje se i statican - start <br>");
            $root = xml_documentElement($xmlDoc);
            xml_setAttribute($root, "id", $blok["StBl_Id"]);

            $childs = xml_childNodes($root);
            for ($j=0; $j < count($childs); $j++) {
                $child = xml_item($childs, $j);
                if (xml_nodeName($child) == "import") {
                    $impNodes = xml_childNodes($child);
                    for ($k=0;$k<count($impNodes);$k++) {
                        $impNode = $impNodes[$k];
                        $text = xml_getContent($impNode);
                        $text = xml_convertChar($text, "%22", "%26quot%3B", false);
                        $impNode = xml_setContent($xmlDoc, $impNode, $text);
                    }
                } else {
                    if (xml_nodeName($child) != "tekst") {
                        $text = xml_getContent($child);
                        $text = xml_convertChar($text, "%22", "%26quot%3B", false);
                        $child = xml_setContent($xmlDoc, $child, $text);
                    }
                }
            }
//utils_dump("prikaz bloka - inner ne edituje se i statican - transformacija <br>");
            print rawurldecode(xml_transform($xmlDoc, $_SERVER['DOCUMENT_ROOT'] . $blok["TipB_XslUrl"], 1));
//utils_dump("prikaz bloka - inner ne edituje se i statican - end <br>");

        } else { //ne edituje se i dinamican je blok
            $root = xml_documentElement($xmlDoc);
            $childs = xml_childNodes($root);
            $url = "";
            $urlParams = array();

            for ($j=0; $j < count($childs); $j++) {
                $child = xml_item($childs, $j);
                if (xml_nodeName($child) == "url") $url .= xml_getContent($child);
                else if ((xml_nodeName($child) == "param") && (xml_getContent($child) != ""))
                    $urlParams[xml_getAttribute($child, "name")] = xml_getContent($child);
            }
            $urlParams["Blok_Id"] = $blok["Blok_Id"];
            $urlParams["StBl_Id"] = $blok["StBl_Id"];
            $urlParams["Stra_Id"] = (isset($blok["Stra_Id"]))? $blok["Stra_Id"] : "";
            $_SESSION["urlParams"] = $urlParams;
            require($_SERVER['DOCUMENT_ROOT'].rawurldecode($url));
        }
        ?><br><?php
    } else {
        if (($editMode == "1")&& ($StBl_Id == $blok["StBl_Id"])) {//editor i ovaj se edituje
//uzima se sveza definicija blokova u koju se prepisuju sve vrednosti iz blok xml-a
            $tipXmlDoc = xml_loadXML($blok["TipB_Xml"]);

            $root = xml_documentElement($tipXmlDoc);

            xml_setAttribute($root, "random", date_getMiliseconds());
            xml_setAttribute($root, "edit", "1");
            xml_setAttribute($root, "action", $stranica["Temp_Url"]);
            xml_setAttribute($root, "Stra_Id", $StraId);
            xml_setAttribute($root, "Blok_Id", $EditBlokId);
            xml_setAttribute($root, "TipB_Id", $blok["Blok_TipB_Id"]);
            xml_setAttribute($root, "TipB_Naziv", ocpLabels($blok["TipB_Naziv"]));
            xml_setAttribute($root, "TipB_SlikaUrl", $blok["TipB_SlikaUrl"]);
            xml_setAttribute($root, "Blok_Share",  $blok["Blok_Share"]);
            xml_setAttribute($root, "Blok_MetaNaziv",  ereg_replace("&quot;", "\"", $blok["Blok_MetaNaziv"]));
            xml_setAttribute($root, "Blok_LastModify",  $blok["Blok_LastModify"]);
            xml_setAttribute($root, "Blok_PublishDate",  $blok["Blok_PublishDate"]);
            xml_setAttribute($root, "Blok_ExpiryDate",  $blok["Blok_ExpiryDate"]);
            xml_setAttribute($root, "dinamic",  $blok["TipB_Dinamic"]);
            xml_setAttribute($root, "StBl_Id",  $blok["StBl_Id"]);
            xml_setAttribute($root, "id", $blok["StBl_Id"]);
            blok_appendLabels($root);

            $ocpDefaultValues = "";

            $childs = xml_childNodes($root);
            $oldChilds = xml_childNodes(xml_documentElement($xmlDoc));
            $insertedLabel = false;
            for ($i=0;$i<count($childs);$i++) {
                $child = $childs[$i];

                if (xml_nodeName($child) == "import") {
                    xml_setAttribute($child, "label", ocpLabels(xml_getAttribute($child, "label")));
                    $importName = xml_getAttribute($child, "name");

                    $importOldNodes = xml_getElementsByTagName($xmlDoc, "import"); //prepis starih vrednosti
                    $importOldNode = null;
                    for ($k = 0; $k < $importOldNodes->length; $k++) {
                        if (xml_getAttribute($importOldNodes->item($k), "name") == $importName) {//bingo!!!
                            $importOldNode = $importOldNodes->item($k);
                            break;
                        }
                    }

                    $impNodes = xml_childNodes($child);
                    for ($k=0;$k<count($impNodes);$k++) {
                        $importChildNode = $impNodes[$k];

                        if (!is_null($importOldNode)) {//prepis starih vrednosti
                            $importOldNodes = xml_childNodes($importOldNode);
                            for ($l=0;$l<count($importOldNodes);$l++) {
                                if (xml_nodeName($importOldNodes[$l]) == xml_nodeName($importChildNode)) {
                                    $importChildNode = xml_setContent($tipXmlDoc, $importChildNode, xml_getContent($importOldNodes[$l]));
                                    break;
                                }
                            }
                        }

                        $insertedLabel = blok_prepareChild($tipXmlDoc, $importChildNode, $importName) || $insertedLabel;
                    }
                } else {
                    for ($k=0; $k < count($oldChilds); $k++) {//prepis starih vrednosti
                        if (xml_nodeName($child) == xml_nodeName($oldChilds[$k])) {
                            if ($blok["TipB_Dinamic"] == "0") {
                                $child = xml_setContent($tipXmlDoc, $child, xml_getContent($oldChilds[$k]));
                                break;
                            } else {
                                if (xml_nodeName($child) == "url" ||
                                        (xml_getAttribute($child, "name") == xml_getAttribute($oldChilds[$k], "name"))) {
                                    $child = xml_setContent($tipXmlDoc, $child, xml_getContent($oldChilds[$k]));
                                    //ocp default values
                                    if (xml_nodeName($child) != "url")
                                        $ocpDefaultValues .= xml_getAttribute($child, "name") . ":" . xml_getContent($oldChilds[$k]) . "|";
                                    break;
                                }
                            }
                        }
                    }
                    $insertedLabel = blok_prepareChild($tipXmlDoc, $child, NULL) || $insertedLabel;
                }
            }
            if ($ocpDefaultValues != "") $ocpDefaultValues = substr($ocpDefaultValues, 0, strlen($ocpDefaultValues) - 1);
            xml_setAttribute($root, "ocpDefaultValues", $ocpDefaultValues);

            if (xml_getAttribute($root, "is_necessary")) {
                $validate = xml_getAttribute($root, "is_necessary");
                $validateFuncCall[] = $validate."()";
                $validateFunc[] = $validate;
            }

            print rawurldecode(xml_transform($tipXmlDoc, "../ocp/siteManager/style/blok.xsl", 1));

            if ($insertedLabel) {
                ?><script>alert("<?php echo ocpLabels("New labels have been added to Multilanguage support. Please translate them.");?>");</script><?php
            }
            ?><br><br><?php
        } else {	//nije editor
            if ($blok["TipB_Dinamic"] == "0") { //nije editor i ovaj je statican
                $root = xml_documentElement($xmlDoc);
                xml_setAttribute($root, "id", $blok["StBl_Id"]);

                $childs = xml_childNodes($root);
                for ($j=0;$j<count($childs);$j++) {
                    $child = xml_item($childs, $j);
                    if (xml_nodeName($child) == "import") {
                        $impNodes = xml_childNodes($child);
                        for ($k=0;$k<count($impNodes);$k++) {
                            $impNode = $impNodes[$k];
                            $impNode = xml_setContent($xmlDoc, $impNode, rawurldecode(xml_getContent($impNode)));
                        }
                    } else {
                        $child = xml_setContent($xmlDoc, $child, rawurldecode(xml_getContent($child)));
                    }
                }
                print rawurldecode(xml_transform($xmlDoc, $_SERVER['DOCUMENT_ROOT']. $blok["TipB_XslUrl"], 1));

            } else { //nije editor i ovaj je dinamican
                $url = "";
                $urlParams = array();

                $root = xml_documentElement($xmlDoc);
                $childs = xml_childNodes($root);
                for ($j=0;$j<count($childs);$j++) {
                    $child = xml_item($childs, $j);
                    if (xml_nodeName($child) == "url") $url .= xml_getContent($child);
                    if ((xml_nodeName($child) == "param") && (xml_getContent($child) != ""))
                        $urlParams[xml_getAttribute($child, "name")] = xml_getContent($child);
                }

                $urlParams["Blok_Id"] = $blok["Blok_Id"];
                $urlParams["StBl_Id"] = $blok["StBl_Id"];
                $urlParams["Stra_Id"] = (isset($blok["Stra_Id"]))? $blok["Stra_Id"] : 0;
                $_SESSION["urlParams"] = $urlParams;
                require($_SERVER['DOCUMENT_ROOT'].rawurldecode($url));
            }
        }
    }
}

function blok_displayNew($stranica, $TipB_Id) {
    global $StraId, $validateFuncCall, $validateFunc;

    $tip = tipblok_get($TipB_Id);
    $xmlDoc = xml_loadXML($tip["TipB_Xml"]);

    $root = xml_documentElement($xmlDoc);
    xml_setAttribute($root, "random", date_getMiliseconds());
    xml_setAttribute($root, "edit", "1");
    xml_setAttribute($root, "action", $stranica["Temp_Url"]);
    xml_setAttribute($root, "Stra_Id", $StraId);
    xml_setAttribute($root, "Blok_Id", "");
    xml_setAttribute($root, "TipB_Id", $tip["TipB_Id"]);
    xml_setAttribute($root, "dinamic",  $tip["TipB_Dinamic"]);
    xml_setAttribute($root, "TipB_Naziv",  ocpLabels($tip["TipB_Naziv"]));
    xml_setAttribute($root, "TipB_SlikaUrl", $tip["TipB_SlikaUrl"]);
    blok_appendLabels($root);

    $childs = xml_childNodes($root);
    $insertedLabel = false;

    for ($j=0;$j<count($childs);$j++) {
        $child = $childs[$j];
        if (xml_nodeName($child) == "import") {
            xml_setAttribute($child, "label", ocpLabels(xml_getAttribute($child, "label")));
            $impNodes = xml_childNodes($child);
            for ($k=0; $k < count($impNodes); $k++) {
                $impNode = xml_item($impNodes, $k);
                $insertedLabel = blok_prepareChild($xmlDoc, $impNode, xml_getAttribute($child, "name")) || $insertedLabel;
            }
        } else {
            $insertedLabel = blok_prepareChild($xmlDoc, $child, null) || $insertedLabel;
        }
    }

    if (xml_getAttribute($root, "is_necessary")) {
        $validate = xml_getAttribute($root, "is_necessary");
        $validateFuncCall[] = $validate."()";
        $validateFunc[] = $validate;
    }

    if ($insertedLabel) {
        ?><script>alert("<?php echo ocpLabels("New labels have been added to Multilanguage support. Please translate them.");?>");</script><?php
    }
    ?><br><img src="/ocp/img/black.gif" width="100%" height="1"><br>
<a name="new"></a><table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td class="ocp_tekst1">
    <?php print rawurldecode(xml_transform($xmlDoc,  "../ocp/siteManager/style/blok.xsl", 1)); ?>
        </td>
    </tr>
</table>
<img src="/ocp/img/black.gif" width="100%" height="1"><br>
    <?php
}

function blok_prepareChild($xmlDoc, $node, $nodeName = NULL) {
    global $validateFunc, $validateFuncCall;

    $node = xml_setContent($xmlDoc, $node, rawurldecode(xml_getContent($node)));
    $inputT = utils_valid(xml_getAttribute($node, "inputType")) ? xml_getAttribute($node, "inputType") : NULL;

//validacijom
    if (utils_valid(xml_getAttribute($node, "validate"))) {
        $nameVar = "";
        if (!is_null($nodeName)) $nameVar .= $nodeName; //importovani nodovi imaju prefix name importa
        $validate = xml_getAttribute($node, "validate");
        $validateFuncs = split(",", $validate);

        for ($f=0; $f < count($validateFuncs); $f++) {
//smestanje poziva funkcije
            if (xml_nodeName($node) == "param") $nameVar = xml_getAttribute($node, "name");
            else $nameVar .= xml_nodeName($node);
            if (($validateFuncs[$f] == "is_necessary") && (!is_null($inputT))) {
                if (($inputT == "textDate") || ($inputT == "textDatetime")) {
                    $validateFuncCall[] = $validateFuncs[$f]."('formObject.".$nameVar."', '".$inputT."')";
                } else {
                    $validateFuncCall[] =
                            $validateFuncs[$f]."('formObject.".$nameVar."', null,  '".ocpLabels(xml_getAttribute($node, "label"))."')";
                }
            } else {
                $validateFuncCall[] = $validateFuncs[$f]."('formObject.".$nameVar."')";
            }

//import .js ako nije vec prisutan
            if (!in_array($validateFuncs[$f], $validateFunc))
                $validateFunc[] = $validateFuncs[$f];
        }
    }
//input type
    $insertedLabel = false;
    if (!is_null($inputT)) {
        switch ($inputT) {
            case "select":
            case "radio":	$tipListe = ($inputT == "select") ? "Selects" : "Radios";
                $lista = selrad_getListValues(xml_getAttribute($node, "listName"), $tipListe);
                $allValue = "";
                $allLabel = "";
                for ($l=0;$l<count($lista);$l++) {
                    $slog = $lista[$l];
                    $allValue .= $slog["Vrednost"]."|@$";
                    if (!is_numeric($slog["Labela"])) {
                        $allLabel .= ocpLabels($slog["Labela"])."|@$";
                        $insertedLabel = lang_newLabela($slog["Labela"]) || $insertedLabel;
                    } else {
                        $allLabel .= $slog["Labela"]."|@$";
                    }
                }
                xml_setAttribute($node, "allvalues", utils_substr($allValue, 0, utils_strlen($allValue)-3));
                xml_setAttribute($node, "alllabels", utils_substr($allLabel, 0, utils_strlen($allLabel)-3));

                break;
            case "complex" :
                $podtipName = xml_getAttribute($node, "podtip");

                if ($podtipName == "Verzija" || $podtipName == "Sekcija" || $podtipName == "Stranica") {

                    $records = ($podtipName == "Verzija") ?
                            con_getResults("select Verz_Id as Id, Verz_Naziv as Naziv from Verzija where Verz_Valid=1 order by Verz_Id") : (
                            ($podtipName == "Sekcija") ?
                            con_getResults("select Sekc_Id as Id, Sekc_Naziv as Naziv from Sekcija where Sekc_Valid=1 order by Sekc_Verz_Id, Sekc_RedPrikaza") :
                            con_getResults("select Stra_Id as Id, Stra_Naziv as Naziv from Stranica where Stra_Valid=1 order by Stra_Sekc_Id, Stra_RedPrikaza")
                    );
                    $allValues = "";
                    $allLabels = "";
                    for ($h=0;$h<count($records);$h++) {
                        $allValues .= $records[$h]["Id"]."|@$";
                        $allLabels .= $records[$h]["Naziv"]."|@$";
                    }
                    xml_setAttribute($node, "allvalues", utils_substr($allValues, 0, utils_strlen($allValues)-3));
                    xml_setAttribute($node, "alllabels", utils_substr($allLabels, 0, utils_strlen($allLabels)-3));
                } else {
                    $sortName = xml_getSortName($podtipName);
                    $restrict = utils_valid(xml_getAttribute($node, "where")) ? xml_getAttribute($node, "where") : NULL;
                    $records = obj_getAll($podtipName, $sortName, "asc", $restrict);
                    $d = array();
                    $allValues = "";
                    for ($h=0;$h<count($records);$h++) {
                        $d = $records[$h];
                        $allValues .= $d["Id"]."|@$";
                    }
                    $allLabels = xml_generateRecordsIdenString($podtipName, $records);
                    xml_setAttribute($node, "allvalues", utils_substr($allValues, 0, utils_strlen($allValues)-3));
                    xml_setAttribute($node, "alllabels", $allLabels);
                }

                break;
            case "foreignKey": ?><script language="javascript" src="/ocp/jscript/select.js"></script><?php
                $restrict = utils_valid(xml_getAttribute($node, "where")) ? xml_getAttribute($node, "where") : NULL;
                $records = obj_getForeignKeyObjectsSimple(xml_getAttribute($node, "podtip"), NULL, xml_getContent($node), $restrict);
                if (count($records) > 0)
                    $node = xml_complexControl($node, $records, "complex", $xmlDoc, $restrict, true);
                break;
            case "fkAutoComplete":
                require_once("../ocp/controls/auto_complete/require.php");
                $podtip = xml_getAttribute($node, "podtip");
                $value = xml_getContent($node);
                if ($value > 0) {
                    xml_setAttribute($node, "value_label", xml_generateRecordIdenString($podtip, obj_get($podtip, $value), 0));
                }
                break;
            default : break;
        }
    }
//labela
    if (($inputT != "html-editor") && utils_valid(xml_getAttribute($node, "label")))
        xml_setAttribute($node, "label", ocpLabels(xml_getAttribute($node, "label")));

    return $insertedLabel;
}

function blok_appendLabels($node) {
    xml_setAttribute($node, "labEdit", ocpLabels("Edit"));
    xml_setAttribute($node, "labEditType", ocpLabels("Edit object type"));
    xml_setAttribute($node, "labEditAll", ocpLabels("edit all"));
    xml_setAttribute($node, "labBlockEditing", ocpLabels("Editing block"));
    xml_setAttribute($node, "labBlockType", ocpLabels("Block type"));
    xml_setAttribute($node, "labSave", ocpLabels("Save"));
    xml_setAttribute($node, "labCancel", ocpLabels("Cancel"));
    xml_setAttribute($node, "labGeneralOptions", ocpLabels("general options"));
    xml_setAttribute($node, "labAdditionalOptions", ocpLabels("additional options"));
    xml_setAttribute($node, "labOpen", ocpLabels("open"));
    xml_setAttribute($node, "labClose", ocpLabels("close"));
    xml_setAttribute($node, "labText", ocpLabels("TEXT"));
    xml_setAttribute($node, "labFrom", ocpLabels("From"));
    xml_setAttribute($node, "labTo", ocpLabels("To"));
    xml_setAttribute($node, "labPeriodOfVisibility", ocpLabels("PERIOD OF VISIBILITY"));
    xml_setAttribute($node, "labNoParams", ocpLabels("This block doesn\'t have parameters. Click on Save to add it."));
    xml_setAttribute($node, "labShared", ocpLabels("Shared"));
    xml_setAttribute($node, "labYes", ocpLabels("Yes"));
    xml_setAttribute($node, "labNo", ocpLabels("No"));
    xml_setAttribute($node, "labUnderName", ocpLabels("Under name"));
    xml_setAttribute($node, "labSharingWithAnother", ocpLabels("SHARING BLOCK WITH ANOTHER PAGES"));
    xml_setAttribute($node, "labImagePreview", ocpLabels("Image preview"));
    xml_setAttribute($node, "labDimension", ocpLabels("Dimensions"));

    xml_setAttribute($node, "labCalendar", ocpLabels("Calendar"));
    xml_setAttribute($node, "labCreateLinkOnPage", ocpLabels("Create link on OCP page"));
    xml_setAttribute($node, "labCreateLinkOnBlock", ocpLabels("Create link on block"));
    xml_setAttribute($node, "labBrowseServer", ocpLabels("Browse server"));
    xml_setAttribute($node, "labSelectedImagePreview", ocpLabels("Selected image preview"));
    xml_setAttribute($node, "labSelectedLinkPreview", ocpLabels("Selected link preview"));
    xml_setAttribute($node, "labRichTextFormat", ocpLabels("Rich text format"));
    xml_setAttribute($node, "labColorPallete", ocpLabels("Color pallete"));
    xml_setAttribute($node, "labSelect", ocpLabels("Choose"));

}
?>