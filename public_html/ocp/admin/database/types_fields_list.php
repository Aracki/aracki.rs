<?php
require_once("../../include/connect.php");
require_once("../../include/session.php");
require_once("../../include/tipoviobjekata.php");
require_once("../../include/polja.php");
?>

<?php session_checkAdministrator(); ?>

<HTML>
<HEAD>
<TITLE> OCP </TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/ocp/css/opsti.css">
<link rel="stylesheet" href="/ocp/css/opcije.css">
<script src="/ocp/validate/validate_column_name.js"></script>
</HEAD>
<BODY class="ocp_body" onLoad="resizeMe();"><?php
	$typeId = utils_requestInt(getGVar("typeId"));
	$oldNoFields = utils_requestInt(getGVar("oldNoFields"));
	$subTypes = tipobj_getAll();
	$noFields = 0;

?><table class="ocp_opcije_table" style="width:100%">
<form action="types_fields_list.php?<?php echo utils_randomQS();?>" method="post" id="fieldForm" name="fieldForm">
<?php	drawHeader();

	if (utils_valid($typeId) && ($typeId != 0)){//prvi ulaz
//utils_dump("Prvi ulaz ".$typeId);
		drawField(intval($oldNoFields), $subTypes);
	} else {//svaki sledeci
		$typeId = utils_requestInt(getPVar("typeId"));
		$oldNoFields = utils_requestInt(getPVar("oldNoFields"));
		$noFields = utils_requestInt(getPVar("noFields"));
//utils_dump("Drugi ulaz ".$typeId." ".$oldNoFields." ".$noFields);
		for ($i=0; $i <= intval($noFields); $i++) 
			drawField($i + intval($oldNoFields), $subTypes);
	}	
?>	<input type="hidden" name="typeId" value="<?php echo $typeId;?>">
	<input type="hidden" name="oldNoFields" value="<?php echo $oldNoFields;?>">
	<input type="hidden" name="noFields" value="<?php echo (intval($noFields)+1);?>"></form>
</table>
<SCRIPT>
	var labReservedKeywords = ": <?php echo ocpLabels('is a reserved keyword');?>.";
	var labNotNumber = ": <?php echo ocpLabels('can not be number');?>.";
	var labFirstChar = ": <?php echo ocpLabels('first character must be letter or _');?>.";
	var labForrbidenChar = ": <?php echo ocpLabels('has forrbiden character');?>.";
	function submitFieldForm(){
		if (validate()){
			document.fieldForm.submit();
		}
	}

	function validateFieldForm(types){
		var insert = (<?php echo $typeId;?> == -1) ? true : false;

		var noFields = document.fieldForm.noFields.value;
		var oldNoFields = document.fieldForm.oldNoFields.value;
		var delimiter = "#!@#";

		var imePolja = "";
		var tip = "";
		var podtipId = "";
		var nullValue = "";
		var defaultValue = "";
		var redPrikaza = "";
		var retValue = true;
		var usedFieldNames = new Array();
	<?php
		
		if ($typeId != -1){
			$oldFields = polja_getFields($typeId);
			for ($i=0; $i<count($oldFields); $i++){
				echo("usedFieldNames[".$i."] = '".$oldFields[$i]["ImePolja"]."';\n");
			}
		}

		?>for (var i=oldNoFields; i<=oldNoFields+noFields; i++){
			var testPolje = eval("document.fieldForm.ImePolja"+i);
			if (testPolje == null) break;

			var _imePolja = eval("document.fieldForm.ImePolja"+i+".value");
			var _tip = eval("document.fieldForm.Tip"+i+".value");
			var _podtipId = eval("document.fieldForm.PodtipId"+i+".value");
			var checked = eval("document.fieldForm.Null"+i+".checked");
			var _nullValue = (checked == true) ? "Null" : "" ;
			var _defaultValue = eval("document.fieldForm.Default"+i+".value");
			var _redPrikaza = eval("document.fieldForm.RedPrikaza"+i+".value");

			if ((_imePolja != "") && (_tip != "") && (_redPrikaza != "")){//polje mozda moze da se snimi
				if (!validate_column_name(_imePolja, labNotNumber, labFirstChar, labForrbidenChar, labReservedKeywords) ){
					retValue = false; 
					break;
				}

				for (var k=0; k<usedFieldNames.length; k++){
					if (usedFieldNames[k] == _imePolja){
						alert("<?php echo ocpLabels("Field names must be different");?>.");
						retValue = false;
						break;
					}
				}
				usedFieldNames[usedFieldNames.length] = _imePolja;

				if ((_tip == "Objects") && (_podtipId == "")){
					alert(_imePolja + " <?php echo ocpLabels("must have Foreign key defined");?>.");
					retValue = false; 
					break;
				}
				
				if (!insert && (_nullValue != "Null") && (_defaultValue == "")){
					alert("<?php echo ocpLabels("Added fields must have Default value of allow Nulls");?>.");
					retValue = false; 
					break;
				}
				//polje moze da snimi
				imePolja += _imePolja + delimiter;
				tip += _tip + delimiter;
				podtipId += _podtipId + delimiter;
				nullValue += _nullValue + delimiter;
				defaultValue += _defaultValue + delimiter;
				redPrikaza += _redPrikaza + delimiter;
				usedFieldNames[usedFieldNames.length] = _imePolja;
			}
		}

		if (retValue && (imePolja != "")){//imamo sta da posaljemo parentu
			parent.document.forms["formObject"].elements["novoImePolja"].value = imePolja.substring(0, imePolja.length-delimiter.length);
			parent.document.forms["formObject"].elements["novoTip"].value = tip.substring(0, tip.length-delimiter.length);
			parent.document.forms["formObject"].elements["novoPodtipId"].value = podtipId.substring(0, podtipId.length-delimiter.length);
			parent.document.forms["formObject"].elements["novoNull"].value = nullValue.substring(0, nullValue.length-delimiter.length);
			parent.document.forms["formObject"].elements["novoDefault"].value = defaultValue.substring(0, defaultValue.length-delimiter.length);
			parent.document.forms["formObject"].elements["novoRedPrikaza"].value = redPrikaza.substring(0, redPrikaza.length-delimiter.length);
			//alert(imePolja+" "+tip+" "+podtipId+" "+nullValue+" "+defaultValue+" "+redPrikaza);
			//alert(defaultValue);
		} else {
			if (retValue && (usedFieldNames.length == 0) && types == "objects"){
				alert("<?php echo ocpLabels("Object type must have at least one field");?>.");
				retValue = false;
			}
		}

		return retValue;
	}

	function addOrderField(){
		if (!validate("OcpOrderColumn")) return;
		var noFields = parseInt(document.fieldForm.noFields.value);
		var oldNoFields = parseInt(document.fieldForm.oldNoFields.value);

		for (var i=oldNoFields; i<(oldNoFields+noFields); i++){
			var _imePolja = eval("document.fieldForm.ImePolja"+i+".value");
			var _tip = eval("document.fieldForm.Tip"+i+".value");
			var _podtipId = eval("document.fieldForm.PodtipId"+i+".value");
			var checked = eval("document.fieldForm.Null"+i+".checked");
			var _nullValue = (checked == true) ? "Null" : "" ;
			var _defaultValue = eval("document.fieldForm.Default"+i+".value");
			var _redPrikaza = eval("document.fieldForm.RedPrikaza"+i+".value");

			if ((_imePolja == "") && (_tip == "")){//polje mozda moze da se snimi
				eval("document.fieldForm.ImePolja"+i+".value = 'OcpOrderColumn'");
				eval("document.fieldForm.Tip"+i+".value = 'Ints'");
				eval("document.fieldForm.PodtipId"+i+".value = ''");
				eval("document.fieldForm.Null"+i+".checked");
				eval("document.fieldForm.Default"+i+".value = '0'");
				break;
			}
		}
	}

	function validate(newColumn){
		var insert = (<?php echo $typeId;?> == -1) ? true : false;

		var noFields = document.fieldForm.noFields.value;
		var oldNoFields = document.fieldForm.oldNoFields.value;
		var retValue = true;

		var usedFieldNames = new Array();
	<?php
		
		if ($typeId != -1){
			$oldFields = polja_getFields($typeId);
			for ($i=0; $i<count($oldFields); $i++){
				echo("usedFieldNames[".$i."] = '".$oldFields[$i]["ImePolja"]."';\n");
			}
		}

		?>
		if (newColumn != null){
			for (var k=0; k<usedFieldNames.length; k++){
				if (usedFieldNames[k] == newColumn){
					alert("<?php echo ocpLabels("Field names must be different");?>.");
					return false;
				}
			}
			usedFieldNames[usedFieldNames.length] = newColumn;
		}

		for (var i=oldNoFields; i<=oldNoFields+noFields; i++){
			var testPolje = eval("document.fieldForm.ImePolja"+i);
			if (testPolje == null) break;

			var _imePolja = eval("document.fieldForm.ImePolja"+i+".value");
			var _tip = eval("document.fieldForm.Tip"+i+".value");
			var _redPrikaza = eval("document.fieldForm.RedPrikaza"+i+".value");

			if ((_imePolja != "") && (_tip != "") && (_redPrikaza != "")){//polje mozda moze da se snimi
				for (var k=0; k<usedFieldNames.length; k++){
					if (usedFieldNames[k] == _imePolja){
						alert("<?php echo ocpLabels("Field names must be different");?>.");
						retValue = false;
						break;
					}
				}
				usedFieldNames[usedFieldNames.length] = _imePolja;
			}
		}

		return retValue;
	}
	
	function resizeMe() {
		var sh;
		if (document.body && document.body.scrollHeight) sh = document.body.scrollHeight;
		else sh = document.height;
		var o = getMe();
		if (o) o.height = sh;
	}

	function getMe() {
		a = parent.document.getElementsByTagName('iframe');
		for (var i=0;i < a.length; i++) {
			id = a[i].id;
			frm = parent.frames[id];
			if ((frm.document != null) && (frm.document.body != null)) {
				var inrHtml = frm.document.body.innerHTML;
				if (inrHtml == document.body.innerHTML) return a[i];
			}
		}
		return null;
	}
</SCRIPT>
</div>
<?php
	function drawHeader(){
?>
   <tr id="trHeader" style="position:relative; top:0px">
     <td valign="top" class="ocp_opcije_td_header ocp_opcije_tekst3"><?php echo utils_toUpper(ocpLabels("Field name"));?></td>
     <td valign="top" class="ocp_opcije_td_header ocp_opcije_tekst3"><?php echo utils_toUpper(ocpLabels("Field type"));?></td>
     <td valign="top" class="ocp_opcije_td_header ocp_opcije_tekst3"><?php echo utils_toUpper(ocpLabels("Foreign key"));?></td>
     <td valign="top" class="ocp_opcije_td_header ocp_opcije_tekst3">NULL</td>
     <td valign="top" class="ocp_opcije_td_header ocp_opcije_tekst3">DEFAULT</td>
     <td valign="top" class="ocp_opcije_td_header ocp_opcije_tekst3"><?php echo utils_toUpper(ocpLabels("Order"));?></td>
	</tr><?php	
	}
	
	function drawField($no, $subTypes){
		?><tr>
		<td class="ocp_opcije_td"><?php
		$oldImePolja = utils_requestStr(getPVar("ImePolja".$no));
		if (!utils_valid($oldImePolja)) $oldImePolja = "";
		?><input type="text" class="ocp_forma" name="ImePolja<?php echo $no;?>" style="width: 100%;" value="<?php echo $oldImePolja;?>"></td>
        <td class="ocp_opcije_td" style="border-top:0px;"><span class="ocp_opcije_tekst1">
        <select name="Tip<?php echo $no;?>"  class="ocp_forma" style="width: 100%;">
				<option value="">-- <?php echo ocpLabels("field type");?> --</option><?php
					$oldTip = utils_requestStr(getPVar("Tip".$no));
				?><option value="ShortStrings" <?php if ($oldTip == "ShortStrings") echo("selected");?>>ShortStrings</option>
				<option value="LongStrings" <?php if ($oldTip == "LongStrings") echo("selected");?>>LongStrings</option>
				<option value="Ints" <?php if ($oldTip == "Ints") echo("selected");?>>Ints</option>
				<option value="Dates" <?php if ($oldTip == "Dates") echo("selected");?>>Dates</option>
				<option value="Bits" <?php if ($oldTip == "Bits") echo("selected");?>>Bits</option>
				<option value="Floats" <?php if ($oldTip == "Floats") echo("selected");?>>Floats</option>
				<option value="Texts" <?php if ($oldTip == "Texts") echo("selected");?>>Texts</option>
				<option value="Radios" <?php if ($oldTip == "Radios") echo("selected");?>>Radios</option>
				<option value="Selects" <?php if ($oldTip == "Selects") echo("selected");?>>Selects</option>
				<option value="Objects" <?php if ($oldTip == "Objects") echo("selected");?>>Objects</option>
			</select>
            </span></td>
            <td class="ocp_opcije_td" style="border-top:0px;"><span class="ocp_opcije_tekst1">
              <select name="PodtipId<?php echo $no;?>" style="width: 100%;" class="ocp_forma">
				<option value="">-- <?php echo ocpLabels("foreign key");?> --</option><?php
		for ($j=0; $j<count($subTypes); $j++){
			$subType = $subTypes[$j];
			?>	<option value="<?php echo $subType["Id"];?>" <?php if (utils_requestInt(getPVar("PodtipId".$no)) == $subType["Id"]) echo("selected");?>><?php echo $subType["Ime"];?></option><?php
		}	?>
                </select>
            </span></td>
            <td class="ocp_opcije_td" style="border-top:0px;width:20px;"><input type="checkbox" name="Null<?php echo $no;?>" value="Null" <?php if (utils_requestStr(getPVar("Null".$no)) == "Null") echo("checked")?>></td>
            <td class="ocp_opcije_td"><?php
		$oldDefault = utils_requestStr(getPVar("Default".$no));
		if (!utils_valid($oldDefault)) $oldDefault = "";
		?><input type="text" class="ocp_forma" name="Default<?php echo $no;?>" style="width: 100%;" value="<?php echo $oldDefault;?>"></td><?php
		$oldRedPrikaza = intval(getPVar("RedPrikaza".$no));
		if (!utils_valid($oldRedPrikaza)) $oldRedPrikaza = $no;
			?><td class="ocp_opcije_td"><input type="text" class="ocp_forma" name="RedPrikaza<?php echo $no;?>" style="width: 100%;" value="<?php echo $oldRedPrikaza;?>"></td>
          </tr>
         <?php
	}
?></BODY>
</HTML>