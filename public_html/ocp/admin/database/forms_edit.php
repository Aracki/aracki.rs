<?php
require_once("../../include/connect.php");
require_once("../../include/session.php");
require_once("../../include/xml.php");
require_once("../../include/xml_tools.php");
require_once("../../include/polja.php");
require_once("../../include/selectradio.php");
require_once("../../include/tipoviobjekata.php");
require_once("../../include/language.php");
//require_once("../../dbUpload/tipuploada.php");
?>
<?php session_checkAdministrator(); ?>
<html>
    <head>
        <link rel="stylesheet" href="/ocp/css/opsti.css">
        <link rel="stylesheet" href="/ocp/css/opcije.css">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <script src="/ocp/validate/validate_double_quotes.js"></script>
        <script language="javascript">
            function validate() {
                var fldCount = document.formObject.fieldcnt.value;
                var id = document.formObject.typeId.value;

                for (i=0;i<fldCount;i++) {
                    var type = eval("document.formObject.fieldtype"+i+".type");
                    var typeVal = eval("document.formObject.fieldtype"+i+".value");
                    var fname = eval("document.formObject.fieldName"+i+".value");

                    if (typeVal != "upload"){
                        var root = eval("document.formObject.root"+i+".value");
                        var width = eval("document.formObject.width"+i+".value")
                        if (type == "select-one"){
                            var selIndex = document.formObject.elements['fieldtype'+i].selectedIndex;
                            var y = document.formObject.elements['fieldtype'+i].options[selIndex].value;
                            if (y == ""){
                                alert("<?php echo ocpLabels("Field");?> "+fname+"<?php echo ocpLabels(" doesn\'t have choosen control");?>.");
                                return false;
                            }
                            if ((y == "file" || y == "fileImage" || y == "intLink" || y == "folder") && (root == "" || root == "undefined")){
                                alert("<?php echo ocpLabels("Field");?> "+fname+"<?php echo ocpLabels(" doesn\'t have file path");?>.");
                                return false;
                            }
                        }
                    }

                    var x = eval("document.formObject.label"+i+".value");
                    if (x == "") {
                        alert("<?php echo ocpLabels("Field");?> "+fname+"<?php echo ocpLabels(" doesn\'t have label");?>.");
                        return false;
                    }

                    var editGroup = eval("document.formObject.editGroup"+i+".value");
                    if (editGroup != ""){
                        var validates = document.formObject.elements["validate"+i+"[]"];
                        if (validates.length){
                            var is_necessary = false;
                            for (var t=0; t<validates.length; t++){
                                if (validates[t].selected && (validates[t].value == "is_necessary")){
                                    is_necessary = true;
                                    break;
                                }
                            }
                        } else {
                            if (validates.selected && (validates.value == "is_necessary")) is_necessary = true;
                        }
                        if (is_necessary){
                            alert("<?php echo ocpLabels("Necessary fields can not be in edit groups");?>.");
                            return false;
                        }
                    }
                }

                //bar jedno iden polje
                var hasIden = false;
                for (var i=0; i<document.formObject.elements.length; i++){
                    if ((document.formObject.elements[i].type == "checkbox") && (document.formObject.elements[i].checked == true)){
                        var y = document.formObject.elements[i].name;
                        var x = document.formObject.elements[i].value;
                        if ( (x == "1") && (y.indexOf("iden") == 0)){
                            hasIden = true;
                            break;
                        }
                    }
                }

                if (!hasIden){
                    alert("<?php echo ocpLabels("You have to choose at least one necessary and iden field");?>.");
                    return false;
                }

                var k=0;
                var includeUrl = eval("document.formObject.includeUrl"+k);
                var includeLabel = eval("document.formObject.includeLabel"+k);
                while (	(includeLabel && (includeLabel.value != "")) ||
                    (includeUrl && (includeUrl.value != "")) ){
                    if ((includeLabel.value == "") || (includeUrl.value == "")){
                        alert("<?php echo ocpLabels("Inclueded file must have url and label");?>.");
                        return false;
                    }
                    k++;
                    includeUrl = eval("document.formObject.includeUrl"+k);
                    includeLabel = eval("document.formObject.includeLabel"+k);
                }

                var k=0;
                var actionUrl = eval("document.formObject.actionUrl"+k);
                var actionLabel = eval("document.formObject.actionLabel"+k);
                var actionImage = eval("document.formObject.actionImage"+k);
                while (	actionUrl && actionUrl.value != ""){
                    if ((actionLabel.value == "") && (actionImage.value == "")){
                        alert("<?php echo ocpLabels("Action must have label or image file defined.");?>.");
                        return false;
                    }

                    var checkedPlace = false;
                    for (var i=0; i< document.formObject.elements.length; i++){
                        if (document.formObject.elements[i].name.indexOf("actionPlace"+k+"[]") == 0){
                            checkedPlace = true;
                            break;
                        }
                    }
                    if (!checkedPlace){
                        alert("<?php echo ocpLabels("Action must have at least one execution place.");?>.");
                        return false;
                    }
                    k++;
                    actionUrl = eval("document.formObject.actionUrl"+k);
                    actionLabel = eval("document.formObject.actionLabel"+k);
                    actionImage = eval("document.formObject.actionImage"+k);
                }

                validate_double_quotes(document.formObject);

                document.formObject.submit();
            }

            function showEditable(name, checked){
                var divObj = document.getElementById("subForm"+name);
                var divLabel = document.getElementById("subLabel"+name);

                if (checked){
                    divObj.style.visibility = "visible";
                    divLabel.style.visibility = "visible";
                } else {
                    divObj.style.visibility = "hidden";
                    divLabel.style.visibility = "hidden";
                    divObj.checked = false;
                }
            }
        </script>
    </head>
    <body class="ocp_body"><?php

        $typeId = utils_requestInt(getGVar("typeId"));
        if (utils_valid($typeId) && ($typeId != 0)) {//prvi ulaz u formu

            $typeName = tipobj_getName($typeId);
            $typeFields = polja_getFields($typeId);
            $fileNames = this_getValidationJS();
            $xmlDom = xml_getNode($typeName, 0);

            $userHeight = utils_valid(getSVar("ocpUserHeight")) ? getSVar("ocpUserHeight") : "25%";
            ?><script language="javascript">
                window.onload = function(){
                    parent.document.getElementById("resizableFrameset").setAttribute("rows", "<?php echo $userHeight;?>,*");
                }

                function styleOn(objId,tabId) {<?php
    for ($i=0; $i<count($typeFields); $i++) {
        echo("document.getElementById('".$typeFields[$i]["ImePolja"]."').style['display'] = 'none';");
        echo("document.getElementById('tab_".$typeFields[$i]["ImePolja"]."').className = 'polja';");
        echo("document.getElementById(objId).style['display'] = 'block';");
        echo("document.getElementById(tabId).className += ' sel';");
    }
    ?>}
        </script><div id="ocp_main_table">
            <form name="formObject" id="formObject" method="post" onSubmit="return validate();" action="forms_edit.php?<?php echo utils_randomQS();?>">
                <input type="hidden" name="typeId" value="<?php echo $typeId;?>">
                <input type="hidden" name="typeName" value="<?php echo $typeName;?>">
                <input type="hidden" name="fieldcnt" value="<?php echo count($typeFields);?>"><?php
                    $typeLabel = ocpLabels(tipobj_getLabel($typeId));
                    ?><table class="ocp_naslov_table">
                    <tr><td class="ocp_naslov_td"><?php echo ocpLabels("Edit type form");?>: <?php echo $typeLabel;?></td></tr>
                </table>
                <table class="ocp_opcije_table" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td valign="top"><table class="ocp_opcije_table">
                                <tr>
                                    <td colspan="2" class="ocp_opcije_td_header ocp_opcije_tekst4"><?php echo ocpLabels("Edit fields");?>:</td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-left: 5px;"><?php
                                            for ($i=0; $i<count($typeFields); $i++) {
                                                $field = $typeFields[$i]; //Id, TipId, ImePolja, TipTabela
                                                ?><a href="javascript:styleOn('<?php echo $field["ImePolja"];?>', 'tab_<?php echo $field["ImePolja"];?>');" class="polja<?php if ($i == 0) echo(" sel");?>" id="tab_<?php echo $field["ImePolja"];?>"><?php echo $field["ImePolja"];?></a><?php

                                            }
                                            ?></td>
                                </tr>
                                <tr id="trHeader" style="position:relative; top:0px">
                                    <td colspan="2" valign="top" style="padding:0;"><?php
                                            for ($i=0; $i<count($typeFields); $i++) {
                                                $field = $typeFields[$i]; //Id, TipId, ImePolja, TipTabela
                                                $xmlField = this_getField($xmlDom, $field["ImePolja"]);
//utils_dump($xmlField, 1);
                                                ?><table class="ocp_opcije_table" id="<?php echo $field["ImePolja"];?>" name="listTable" style="width:100%; display: <?php if ($i==0) {
                                                    echo("block");
                                                } else {
                                                    echo("none");
                                                       }?>;">
                                            <tr id="trHeader" style="position:relative; top:0px">
                                                <td colspan="2" valign="top" style="padding:6px;"  class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><strong><?php echo $field["ImePolja"];?> (<?php echo $field["TipTabela"];?>) </strong></span>
                                                    <input type="hidden" name="fieldName<?php echo $i;?>" value="<?php echo $field["ImePolja"];?>"></td>
                                            </tr><?php	//Dodaje se pripadnost posebnoj edit grupi
                                                    $editGroup = "";
                                                    if (!is_null($xmlDom) && !is_null($xmlField) && isset($xmlField["editGroup"]) && !is_null($xmlField["editGroup"]))
                                                        $editGroup = $xmlField["editGroup"];
                                                    ?>		<tr>
                                                <td width="22%" align="right" class="ocp_opcije_td">
                                                    <span class="ocp_opcije_tekst1"><?php echo ocpLabels("Edit group");?> </span></td>
                                                <td valign="top" class="ocp_opcije_td"><input type="text" class="ocp_forma" name="editGroup<?php echo $i;?>" style="width: 100%;" value="<?php echo $editGroup;?>"/></td>
                                            </tr><?php
                                                    $currLabel = $field["ImePolja"];
                                                    if (!is_null($xmlDom) && !is_null($xmlField)) $currLabel = $xmlField["label"];
                                                    ?>		<tr>
                                                <td class="ocp_opcije_td" align="right">
                                                    <span class="ocp_opcije_tekst1"><?php echo ocpLabels("Label");?> </span></td>
                                                <td valign="top" class="ocp_opcije_td"><input type="text" name="label<?php echo $i;?>" value="<?php echo $currLabel;?>" class="ocp_forma"></td>
                                            </tr>
                                            <tr>
                                                <td class="ocp_opcije_td" align="right"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Control");?> </span></td>
                                                <td valign="top" class="ocp_opcije_td"><?php
                                                            if($field["TipTabela"] == "Uploads") {	?>
                                                    <span class="ocp_opcije_tekst2">upload</span><input type="hidden" name="fieldtype<?php echo $i;?>" value="upload">
                                                                <?php			}else {
                                                                $currInputT = "";
                                                                if (!is_null($xmlDom) && !is_null($xmlField)) $currInputT = $xmlField["inputType"];
                                                                ?>					<select name="fieldtype<?php echo $i;?>" class="ocp_forma" style="width: 100%;">
                                                        <option value="">-- <?php echo ocpLabels("choose control");?> --</option>
                                                        <option value="textBox" <?php if ($currInputT == "textBox") { ?>selected<?php } ?>>Text</option>
                                                        <option value="textDate" <?php if ($currInputT == "textDate") { ?>selected<?php } ?>>Date</option>
                                                        <option value="textDatetime" <?php if ($currInputT == "textDatetime") { ?>selected<?php } ?>>Date-time</option>
                                                        <option value="check" <?php if ($currInputT == "check") { ?>selected<?php } ?>>Checkbox</option>
                                                        <option value="textarea" <?php if ($currInputT == "textarea") { ?>selected<?php } ?>>Textarea</option>
                                                        <option value="html-editor" <?php if ($currInputT == "html-editor") { ?>selected<?php } ?>>HTML-editor</option>
                                                        <option value="select" <?php if ($currInputT == "select") { ?>selected<?php } ?>>Select list</option>
                                                        <option value="radio" <?php if ($currInputT == "radio") { ?>selected<?php } ?>>Radio list</option>
                                                        <option value="fileImage" <?php if ($currInputT == "fileImage") { ?>selected<?php } ?>>fileImage</option>
                                                        <option value="file" <?php if ($currInputT == "file") { ?>selected<?php } ?>>File</option>
                                                        <option value="folder" <?php if ($currInputT == "folder") { ?>selected<?php } ?>>Folder</option>
                                                        <option value="intLink" <?php if ($currInputT == "intLink") { ?>selected<?php } ?>>Link</option>
                                                        <option value="labela" <?php if ($currInputT == "labela") { ?>selected<?php } ?>>Labela</option>
                                                        <option value="color" <?php if ($currInputT == "color") { ?>selected<?php } ?>>Color</option>
                                                        <option value="hidden" <?php if ($currInputT == "hidden") { ?>selected<?php } ?>>Hidden</option>
                                                        <option value="complex" <?php if ($currInputT == "complex") { ?>selected<?php } ?>>Foreign key</option>
                                                        <option value="fkMultiple" <?php if ($currInputT == "fkMultiple") { ?>selected<?php } ?>>Foreign key (multiple)</option>
                                                        <option value="fkAutoComplete" <?php if ($currInputT == "fkAutoComplete") { ?>selected<?php } ?>>Foreign key (auto complete)</option>
                                                        <option value="versionList" <?php if ($currInputT == "versionList") { ?>selected<?php } ?>>Version list</option>
                                                        <option value="sectionList" <?php if ($currInputT == "sectionList") { ?>selected<?php } ?>>Section list</option>
                                                        <option value="pageList" <?php if ($currInputT == "pageList") { ?>selected<?php   } ?>>Page list</option>
                                                        <option value="empty" <?php if ($currInputT == "empty") { ?>selected<?php } ?>>Empty</option>
                                                    </select><?php
                                                            }	?></td>
                                            </tr>
                                            <tr>
                                                <td class="ocp_opcije_td" align="right"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Validation");?> </span></td>
                                                <td valign="top" class="ocp_opcije_td">
                                                    <select name="validate<?php echo $i;?>[]" size="4" multiple="multiple" class="ocp_forma" style="width: 100%;">
                                                        <option value="">-- <?php echo ocpLabels("choose validation"); ?> --</option>
                                                                <?php
                                                                $currVal = "";
                                                                if (!is_null($xmlDom) && !is_null($xmlField) && (!is_null($xmlField["validate"]))) {
                                                                    $currVal = $xmlField["validate"];
                                                                }

                                                                for ($j = 0; $j < count($fileNames); $j++) {
                                                                    if ($fileNames[$j] != "validate_column_name") {
                                                                        $selected = "";
                                                                        if (utils_valid($currVal) && isset($fileNames[$j]) && utils_valid($fileNames[$j]) && is_integer(strpos($currVal, $fileNames[$j]))) {
                                                                            $selected = "selected";
                                                                        }
                                                                        ?>
                                                        <option value="<?php echo $fileNames[$j] ;?>" <?php echo $selected ;?>><?php echo $fileNames[$j] ;?></option>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                    </select>
                                                </td>
                                            </tr><?php
                                                    $currIden = "0";
                                                    if (!is_null($xmlDom) && !is_null($xmlField)) $currIden = $xmlField["iden"];
                                                    ?><tr>
                                                <td class="ocp_opcije_td" align="right"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Iden");?></span></td>
                                                <td valign="top" class="ocp_opcije_td"><input type="checkbox" name="iden<?php echo $i;?>" value="1" <?php if ($currIden == "1") echo("checked")?>></td>
                                            </tr>
                                            <tr>
                                                <td class="ocp_opcije_td" align="right"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Additional parameters");?> </span></td>
                                                <td valign="top" class="ocp_opcije_td"><table width="100%">
                                                        <tr>
                                                            <td><?php
                                                                        if ($field["TipTabela"] == "Uploads") {
                                                                            $currExtra = "";
                                                                            if (!is_null($xmlDom) && !is_null($xmlField) && (!is_null($xmlField["type"]))) $currExtra = $xmlField["type"];
                                                                            $keys = array_keys($uploadConsts);
                                                                            if (count($keys) > 0) {	?>
                                                                <select class="ocp_forma" name="root<?php echo $i;?>">
                                                                    <option value="">--<?php echo ocpLabels("choose upload type");?>--</option>
                                                                                    <?php					for ($k=0; $k<count($keys); $k++) {
                                                                                        $selected = ($keys[$k] == $currExtra) ? "selected" : "";
                                                                                        ?>						<option value="<?php echo $keys[$k];?>" <?php echo $selected;?>><?php echo $uploadConsts[$keys[$k]];?></option><?php
                                                                                    }?>
                                                                </select>
                                                                                <?php				}
                                                                        } else {	?><span class="ocp_opcije_tekst1"><?php echo ocpLabels("root folder");?></span><?php
                                                                            $root = "";
                                                                            if (!is_null($xmlDom) && !is_null($xmlField) && isset($xmlField["root"]) && !is_null($xmlField["root"]))
                                                                                $root=$xmlField["root"];
                                                                            ?><input type="text" class="ocp_forma" name="root<?php echo $i;?>" value="<?php echo $root;?>" style="width: 100%;" /></td>
                                                            <td><span class="ocp_opcije_tekst1"><?php echo ocpLabels("image max width");?></span><?php
                                                                            $width="";
                                                                            if (!is_null($xmlDom) && !is_null($xmlField) && isset($xmlField["width"]) && !is_null($xmlField["width"]))
                                                                                $width = $xmlField["width"];
                                                                            ?><input type="text" class="ocp_forma" name="width<?php echo $i;?>" value="<?php if ($width > 0) echo $width;?>" style="width:100%;" /></td>
                                                            <td><span class="ocp_opcije_tekst1"><?php echo ocpLabels("image max height");?></span><?php
                                                                            $height = "";
                                                                            if (!is_null($xmlDom) && !is_null($xmlField) && isset($xmlField["height"]) && !is_null($xmlField["height"]))
                                                                                $height = $xmlField["height"];
                                                                            ?><input type="text" class="ocp_forma" name="height<?php echo $i;?>" value="<?php if ($height > 0) echo $height;?>" style="width: 100%;" /></td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="ocp_opcije_tekst1"><?php echo ocpLabels("reserved for later use");?></span><?php
                                                                            $max = "";
                                                                            if (!is_null($xmlDom) && !is_null($xmlField) && isset($xmlField["max"]) && !is_null($xmlField["max"]))
                                                                                $max = $xmlField["max"];
                                                                            ?><input type="text" class="ocp_forma" name="max<?php echo $i;?>" value="<?php if (utils_valid($max)) echo $max;?>" style="width: 100%;" /></td>
                                                            <td><span class="ocp_opcije_tekst1"><?php echo ocpLabels("import file");?></span><?php
                                                                            $importStr = "";
                                                                            if (!is_null($xmlDom) && !is_null($xmlField) && isset($xmlField["import"]) && !is_null($xmlField["import"]))
                                                                                $importStr = $xmlField["import"];
                                                                            ?><input type="text" class="ocp_forma" name="import<?php echo $i;?>" value="<?php echo $importStr;?>" style="width: 100%;" /></td>
                                                            <td><span class="ocp_opcije_tekst1"><?php echo ocpLabels("sql where filter");?></span><?php
                                                                            $whereStr = "";
                                                                            if (!is_null($xmlDom) && !is_null($xmlField) && isset($xmlField["where"]) && !is_null($xmlField["where"]))
                                                                                $whereStr = $xmlField["where"];
                                                                            ?><input type="text" class="ocp_forma" name="where<?php echo $i;?>" value="<?php echo $whereStr;?>" style="width: 100%;" /><?php
                                                                        }	?></td>
                                                        </tr>
                                                    </table></td>
                                            </tr>
                                        </table><?php
                                            }
                                            ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="ocp_opcije_td_header ocp_opcije_tekst4"><?php echo ocpLabels("Edit other parameters");?>:</td>
                                </tr>
                                    <?php	$checked = "";

                                    if (is_null($xmlDom)) $checked = "checked";
                                    else $checked = this_getIdenId($xmlDom);
                                    ?><tr>
                                    <td width="22%" class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Is Id identification field");?>?</span></td>
                                    <td width="78%" class="ocp_opcije_td"><input type="checkbox" name="IdenId" value="1" <?php echo $checked;?>></td>
                                </tr><?php
                                    $includes = this_getIncludes($xmlDom);//include files
                                    for ($k=0; $k < count($includes); $k++) {
                                        $include = $includes[$k];
                                        ?>	<tr>
                                    <td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Included file label");?></span></td>
                                    <td class="ocp_opcije_td"><input type="text" class="ocp_forma" name="includeLabel<?php echo $k;?>" style="width: 100%;" value="<?php echo $include["label"];?>"></td>
                                </tr>
                                <tr>
                                    <td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Included file url");?> </span></td>
                                    <td class="ocp_opcije_td"><input type="text" class="ocp_forma" name="includeUrl<?php echo $k;?>" style="width: 100%;" value="<?php echo $include["url"];?>"></td>
                                </tr><?php
                                    }
                                    ?><tr>
                                    <td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Included file label");?></span></td>
                                    <td class="ocp_opcije_td"><input type="text" class="ocp_forma" name="includeLabel<?php echo count($includes);?>" style="width: 100%;" value=""></td>
                                </tr>
                                <tr>
                                    <td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Included file url");?> </span></td>
                                    <td class="ocp_opcije_td"><input type="text" class="ocp_forma" name="includeUrl<?php echo count($includes);?>" style="width: 100%;" value=""></td>
                                </tr><?php
                                    $subForms = polja_getAllFks($typeId);
                                    if (count($subForms) > 0) {	?>
                                <tr>
                                    <td colspan="2" class="ocp_opcije_td_header ocp_opcije_tekst4"><?php echo ocpLabels("Subforms");?></td>
                                </tr><?php
                                        global $xmlDelimiter;

                                        $xmlSubforms = this_getSubForms($xmlDom);
                                        for ($j=0; $j<count($subForms); $j++) {
                                            $subForm = $subForms[$j];

                                            $subFormName = $subForm["Ime"] . $xmlDelimiter . $subForm["ImePolja"];

                                            $thisSubtype = ($xmlSubforms && isset($xmlSubforms[$subFormName]));
                                            $checked =  $thisSubtype ? "checked" : "";
                                            $editChecked = ($thisSubtype && isset($xmlSubforms[$subFormName]["Editable"])) ?  "checked" : "";
                                            ?><tr>
                                    <td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo $subForm["Labela"];?>:<?php echo $subForm["ImePolja"];?></span></td>
                                    <td class="ocp_opcije_td" style="width:22%"><table width="100%" border="0">
                                            <tr>
                                                <td width="44%" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Add");?>:
                                                        <input type="checkbox" name="subForm[]" value="<?php echo $subFormName;?>" <?php echo $checked;?> onClick="showEditable('<?php echo $subFormName;?>', this.checked);"></span></td>
                                                <td width="33%" style="width:22%"><span class="ocp_opcije_tekst1">
                                                        <input type="checkbox" name="subForm_editable<?php echo $subFormName;?>" value="1"  style="visibility:<?php if ($checked == "") echo("hidden"); else echo("visible");?>" id="subForm<?php echo $subFormName;?>" <?php echo $editChecked;?>><span class="ocp_mali"  style="visibility:<?php if ($checked == "") echo("hidden"); else echo("visible");?>" id="subLabel<?php echo $subFormName;?>"> <?php echo ocpLabels("Objects can be edited");?></span></td>
                                            </tr>
                                        </table></td>
                                </tr>
                                            <?php		}
                                    }?>
                                <tr>
                                    <td colspan="2" class="ocp_opcije_td_header ocp_opcije_tekst4"><?php echo ocpLabels("Actions");?></td>
                                </tr>
                                    <?php
                                    $actions = this_getActions($xmlDom);//actions
                                    for ($k=0; $k < count($actions); $k++) {
                                        $action = $actions[$k];
                                        ?>	<tr>
                                    <td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Action");?></span></td>
                                    <td class="ocp_opcije_td">
                                        <table width="100%">
                                            <tr>
                                                <td width="33%">
                                                    <span class="ocp_opcije_tekst1"><?php echo ocpLabels("label");?></span>
                                                    <input type="text" class="ocp_forma" name="actionLabel<?php echo $k;?>" style="width: 100%;" value="<?php echo $action["label"];?>">
                                                </td>
                                                <td width="33%">
                                                    <span class="ocp_opcije_tekst1"><?php echo ocpLabels("image");?></span>
                                                    <input type="text" class="ocp_forma" name="actionImage<?php echo $k;?>" style="width: 100%;" value="<?php echo $action["image"];?>">
                                                </td>
                                                <td width="33%">
                                                    <span class="ocp_opcije_tekst1">onclick</span>
                                                    <input type="text" class="ocp_forma" name="actionUrl<?php echo $k;?>" style="width: 100%;" value="<?php echo $action["url"];?>">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">
                                                    <span class="ocp_opcije_tekst1"><?php echo ocpLabels("execute as");?></span>
                                                    <input type="checkbox" name="actionPlace<?php echo $k;?>[]" value="button" <?php if (substr_count($action["place"], "button") != 0) echo "checked"?>/><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Button");?></span>
                                                    <input type="checkbox" name="actionPlace<?php echo $k;?>[]" value="formheader" <?php if (substr_count($action["place"], "formheader") != 0) echo "checked"?>/><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Form header");?></span>
                                                    <input type="checkbox" name="actionPlace<?php echo $k;?>[]" value="objectlist" <?php if (substr_count($action["place"], "objectlist") != 0) echo "checked"?>/><span class="ocp_opcije_tekst1"><?php echo ocpLabels("List of objects");?>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr><?php
                                    }
                                    ?>
                                <tr>
                                    <td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Action");?></span></td>
                                    <td class="ocp_opcije_td">
                                        <table width="100%">
                                            <tr>
                                                <td width="33%">
                                                    <span class="ocp_opcije_tekst1"><?php echo ocpLabels("label");?></span>
                                                    <input type="text" class="ocp_forma" name="actionLabel<?php echo count($actions);?>" style="width: 100%;" value="">
                                                </td>
                                                <td width="33%">
                                                    <span class="ocp_opcije_tekst1"><?php echo ocpLabels("image");?></span>
                                                    <input type="text" class="ocp_forma" name="actionImage<?php echo count($actions);?>" style="width: 100%;" value="">
                                                </td>
                                                <td width="33%">
                                                    <span class="ocp_opcije_tekst1">onclick</span>
                                                    <input type="text" class="ocp_forma" name="actionUrl<?php echo count($actions);?>" style="width: 100%;" value="">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">
                                                    <span class="ocp_opcije_tekst1"><?php echo ocpLabels("execute as");?></span>
                                                    <input type="checkbox" name="actionPlace<?php echo count($actions);?>[]" value="button"/><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Button");?></span>
                                                    <input type="checkbox" name="actionPlace<?php echo count($actions);?>[]" value="formheader"/><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Form header");?></span>
                                                    <input type="checkbox" name="actionPlace<?php echo count($actions);?>[]" value="objectlist"/><span class="ocp_opcije_tekst1"><?php echo ocpLabels("List of objects");?>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>


                            </table>
                        </td>
                    </tr>

                </table>
                <table width="100%">
                    <tr>
                        <td height="40" align="center" class="ocp_text"><input type="submit" name="submitSave" class="ocp_dugme" value="<?php echo ocpLabels("Save");?>">&nbsp;<input type="button" name="submitCancel" class="ocp_dugme" onClick="parent.menuFrame.showSubmenuClose(true, true);" value="<?php echo ocpLabels("Cancel");?>"></td>
                    </tr>
                </table>
            </form>
        </div>
            <?php
        } else if (utils_valid(getPVar("typeId"))) { //nakon submita forme

            $result = array();
            $result["typeName"] = utils_requestStr(getPVar("typeName"));
            $result["typeId"] = utils_requestInt(getPVar("typeId"));
            $result["cnt"] = utils_requestInt(getPVar("fieldcnt"));

            $insertedLabel = false;
//utils_dump(intval($result["cnt"]));
            for ($j=0; $j < intval($result["cnt"]); $j++) {
//utils_dump($j." name ".getPVar("fieldName".$j)." labela ".getPVar("label".$j)." inputType ".getPVar("fieldtype".$j));
                $result["label".$j] = utils_requestStr(getPVar("label".$j));
                $insertedLabel = lang_newLabela($result["label".$j]) || $insertedLabel;
                $result["fieldtype".$j] = utils_requestStr(getPVar("fieldtype".$j));
                $result["fieldName".$j] = utils_requestStr(getPVar("fieldName".$j));
                $result["iden".$j] = utils_requestStr(getPVar("iden".$j));
                $result["root".$j] = utils_requestStr(getPVar("root".$j));
                $result["width".$j] = utils_requestInt(getPVar("width".$j));
                /*utils_dump ("root".$j.": " + getPVar("root".$j). "<br>");
				utils_dump ("width".$j.": " .$result["width".$j]. "<br>");*/
                $result["height".$j] = utils_requestInt(getPVar("height".$j));
                $result["max".$j] = utils_requestStr(getPVar("max".$j));
                $result["import".$j] = utils_requestStr(getPVar("import".$j));
                $result["where".$j] = utils_requestStr(getPVar("where".$j));
                $result["editGroup".$j] = utils_requestStr(getPVar("editGroup".$j));
                $insertedLabel = lang_newLabela($result["editGroup".$j]) || $insertedLabel;

                $tempValidateArray = (isset($_POST["validate".$j]))? getPVar("validate".$j) : NULL;
                $result["validate".$j] = count($tempValidateArray);
                for ($k = 0; $k < count($tempValidateArray); $k++) {
                    $result["_validate".$j.($k+1)] = $tempValidateArray[$k];
                }
            }

            $subFormCount = count(getPVar("subForm"));
            $subFormEditableCount = count(getPVar("subForm_editable"));

            $tempSubformArray = (isset($_POST["subForm"]))? getPVar("subForm") : NULL;
            $tempSubformEditableArray = (isset($_POST["subForm_editable"]))? getPVar("subForm_editable") : NULL;
            $result["subform"] = count($tempSubformArray);
            for ($k=0; $k < count($tempSubformArray); $k++) {
                $result["subform_".($k+1)] = $tempSubformArray[$k];

                $editable = utils_requestStr(getPVar("subForm_editable".$result["subform_".($k+1)]));
                if (utils_valid($editable)) $result["subform_editable_".($k+1)] = $editable;
                else $result["subform_editable_".($k+1)] = "";
//utils_dump($result["subform_".($k+1)] . " " . $result["subform_editable_".($k+1)]);

            }

            $k = 0;
            while( utils_valid(getPVar("includeUrl".$k))) {
                $label = utils_requestStr(getPVar("includeLabel".$k));
                if (!utils_valid($label)) $label = "";
                else $insertedLabel = lang_newLabela($label) || $insertedLabel;
                $url = utils_requestStr(getPVar("includeUrl".$k));
                $result["includeUrl".$k] = $url;
                $result["includeLabel".$k] = $label;
                $k++;
            }
            $result["include"] = $k;

            $k = 0;
            $realCnt = 0;
            while( utils_valid(getPVar("actionUrl".$k))) {
                $label = utils_requestStr(getPVar("actionLabel".$k));
                if (!utils_valid($label)) $label = "";
                else $insertedLabel = lang_newLabela($label) || $insertedLabel;
                $image = utils_requestStr(getPVar("actionImage".$k));
                if (!utils_valid($image)) $image = "";
                $places = getPVar("actionPlace".$k);
                $place = "";
                foreach($places as $next)
                    if (utils_valid($next))
                        $place .= $next . ",";
                if (strlen($place) > 0)
                    $place = substr($place, 0, strlen($place)-1);
                $url = utils_requestStr(getPVar("actionUrl".$k));

                if (($label == "" && $image == "") || ($place == "")) {
                    ;
                } else {
                    $result["actionUrl".$realCnt] = $url;
                    $result["actionLabel".$realCnt] = $label;
                    $result["actionImage".$realCnt] = $image;
                    $result["actionPlace".$realCnt] = $place;
                    $realCnt++;
                }
                $k++;
            }
            $result["action"] = $realCnt;

            $idenId = utils_requestStr(getPVar("IdenId"));
            if ($idenId == "1") $result["IdenId"] = "1";

            xml_createNode($result);
            ?><script>
    <?php if ($insertedLabel) {
        ?>alert("<?php echo ocpLabels("New labels have been added to Multilanguage support. Please translate them.");?>");<?php
    }
    ?>
        parent.subMenuFrame.reconstruct();
        </script>
            <?php

        }

//Fje
        function this_getField($xml, $ime) {
            if (is_null($xml)) return NULL;
            $result = array();
            $fieldList = xml_getElementsByTagName($xml,"field");
            $field = NULL;
            for ($j = 0; $j < $fieldList->length; $j++) {
                $nextField = $fieldList->item($j);
                $childs = xml_childNodes($nextField);
                for ($k=0; $k < count($childs); $k++) {
                    if ((xml_nodeName($childs[$k]) == "name") && (xml_getContent($childs[$k]) == $ime)) {
                        $field = $nextField;
                        break;
                    }
                }
                if (!is_null($field)) break;
            }
            if (is_null($field)) return NULL;

            if (!is_null(xml_getAttribute($field, "iden"))) {
                $result["iden"] = xml_getAttribute($field, "iden");
            } else {
                $result["iden"] = "0";
            }
            if (!is_null(xml_getAttribute($field, "root")))
                $result["root"] = xml_getAttribute($field,"root");
            if (!is_null(xml_getAttribute($field, "width")))
                $result["width"] = xml_getAttribute($field,"width");
            if (!is_null(xml_getAttribute($field, "height")))
                $result["height"] = xml_getAttribute($field,"height");
            if (!is_null(xml_getAttribute($field, "max")))
                $result["max"] = xml_getAttribute($field,"max");
            if (!is_null(xml_getAttribute($field, "import")))
                $result["import"] = xml_getAttribute($field,"import");
            if (!is_null(xml_getAttribute($field, "where")))
                $result["where"] = xml_getAttribute($field,"where");
            if (!is_null(xml_getAttribute($field, "type")))
                $result["type"] = xml_getAttribute($field,"type");
            if (!is_null(xml_getAttribute($field, "editGroup")))
                $result["editGroup"] = xml_getAttribute($field,"editGroup");

            $childs = xml_childNodes($field);
            for ($i=0; $i<count($childs); $i++) {
                $child = $childs[$i];
                $result[xml_nodeName($child)] = xml_getContent($child);
            }
            return $result;
        }

        function this_getSubForms($xml) {
            global $xmlDelimiter;

            $retArray = array();
            if (is_null($xml)) return $retArray;
            $subforms = xml_getElementsByTagName($xml,"subform");
            if ($subforms->length > 0) {
                for ($i=0; $i < $subforms->length; $i++) {
                    $subform = $subforms->item($i);
                    $result = array();
                    $result["SubType"] = xml_getAttribute($subform,"SubType");
                    $result["SubTypeField"] = xml_getAttribute($subform,"SubTypeField");
                    $result["Editable"] = xml_getAttribute($subform,"Editable");

                    $retArray[$result["SubType"] . $xmlDelimiter . $result["SubTypeField"]] = $result;
                }
            }

            return $retArray;
        }

        function this_getIncludes($xml) {
            $retArray = array();
            if (is_null($xml)) return $retArray;
            $includes = xml_getElementsByTagName($xml,"include");
            if ($includes->length > 0) {
                for ($i = 0; $i < $includes->length; $i++) {
                    $result = array();
                    $include = $includes->item($i);
                    $result["url"] = xml_getAttribute($include,"url");
                    $result["label"] = xml_getAttribute($include,"label");
                    $retArray[] = $result;
                }
            }

            return $retArray;
        }

        function this_getActions($xml) {
            $retArray = array();
            if (is_null($xml)) return $retArray;
            $actions = xml_getElementsByTagName($xml,"action");
            if ($actions->length>0) {
                for ($i=0; $i<$actions->length; $i++) {
                    $result = array();
                    $action = $actions->item($i);
                    $result["label"] = xml_getAttribute($action,"label");
                    $result["image"] = xml_getAttribute($action,"image");
                    $result["url"] = xml_getAttribute($action,"url");
                    $result["place"] = xml_getAttribute($action,"place");
                    $retArray[] = $result;
                }
            }

            return $retArray;
        }

        function this_getIdenId($xml) {
            $checked = "";

            if (!is_null($xml)) {
                $xmlField = this_getField($xml, "Id");
                $checked = ($xmlField["iden"] == "0") ? "" : "checked";
            }

            return $checked;
        }

        /*Fja koja vraca validacione funkcije
	=====================================*/
        function this_getValidationJS() {
            $fileNames = array();
            $dir = $_SERVER["DOCUMENT_ROOT"]."/ocp/validate/user";
            $dh = opendir($dir);

            if (is_dir($dir)) {
                if ($dh = opendir($dir)) {
                    while (false !== ($filename = readdir($dh))) {
                        //if ($filename != "." && $filename != "..") {
                        if (preg_match("/\.js$/", $filename)) {
                            $fileNames[] = substr($filename, 0, utils_lastIndexOf($filename, "."));
                        }
                    }
                    closedir($dh);
                }
            }

            sort($fileNames);

            return $fileNames;
        }
        ?>
    </body>
</html>