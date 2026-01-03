<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../include/tipoviobjekata.php");
	require_once("../../include/polja.php");
	require_once("../../include/language.php");
	require_once("../../include/xml.php");
	require_once("../../include/xml_tools.php");
	require_once("../../include/design/button.php");
?>

<?php session_checkAdministrator(); ?>

<HTML>
<HEAD>
	<TITLE> OCP </TITLE>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="/ocp/css/opsti.css">
	<link rel="stylesheet" href="/ocp/css/opcije.css">
	<script src="/ocp/validate/validate_table_name.js"></script>
	<script src="/ocp/validate/validate_column_name.js"></script>
</HEAD>
<BODY class="ocp_body"><?php
	$action = utils_requestStr(getPVar("Action"));
	
	if (utils_valid($action)){
		if ($action == "Sacuvaj"){
			$typeId = utils_requestInt(getPVar("Id"));
			$success = true;
			$insertedLabels = false;
			$delimiter = "#!@#";
			if ($typeId == -1){ // insert tipa objekta
				$result = array(); 

				$result["ImeTipa"] = utils_requestStr(getPVar("ImeTipa"));
				$result["Labela"] = utils_requestStr(getPVar("Labela"));
				$result["Grupa"] = utils_requestStr(getPVar("Grupa"));
				$result["Podforma"] = utils_requestInt(getPVar("Podforma"));
				
				$imePoljaArr = explode($delimiter, utils_requestStr(getPVar("novoImePolja")));
				
				$tipArr = explode($delimiter, utils_requestStr(getPVar("novoTip")));
				$podtipIdArr = explode($delimiter, utils_requestStr(getPVar("novoPodtipId")));
				$nullValueArr = explode($delimiter, utils_requestStr(getPVar("novoNull")));
				$defaultValueArr = explode($delimiter, utils_requestStr(getPVar("novoDefault")));
				$redPrikazaArr = explode($delimiter, utils_requestStr(getPVar("novoRedPrikaza")));
				$columns = array();

				$fieldCnt = 0;
				for ($i=0; $i < count($imePoljaArr); $i++){
					if (!utils_valid($imePoljaArr[$i])) continue;

					$result["ImePolja".$fieldCnt] = $imePoljaArr[$i];
					$result["Tip".$fieldCnt] = $tipArr[$i];
					$result["PodtipId".$fieldCnt] = $podtipIdArr[$i];
					$result["Null".$fieldCnt] = $nullValueArr[$i];
					$result["Default".$fieldCnt] = $defaultValueArr[$i];
					$result["RedPrikaza".$i] = $redPrikazaArr[$i];
					$fieldCnt++;
				}
				$result["n"] = $fieldCnt;


				$newId = tipobj_newTip($result);
				$insertedLabels = lang_newLabela($result["Labela"]);
				$insertedLabels = lang_newLabela($result["Grupa"]) || $insertedLabels;

				if ($newId > 0) {
					if ($fieldCnt > 0){//tip ima polja -> pravimo xml-formu
						$cfgdoc = xml_load($_SERVER["DOCUMENT_ROOT"] . "/ocp/admin/database/datafields_forms.xml");

						$result = array(); 
						$result["typeName"] = utils_requestStr(getPVar("ImeTipa"));
						$result["typeId"] = $newId;
						$result["cnt"] = count($imePoljaArr);

						for ($i=0; $i < count($imePoljaArr); $i++){
							if (!utils_valid($imePoljaArr[$i])) continue;

							$result["label".$i] = ($imePoljaArr[$i] == "OcpOrderColumn") ? "Order" : $imePoljaArr[$i];
							
							$inputType = "textBox";
							$validateArr = array();
							$typeNode = xml_getFirstElementByTagName($cfgdoc, strtolower($tipArr[$i]));
							if (!is_null($typeNode)) {
								$inputType = xml_getContent($typeNode);
								if (utils_valid(xml_getAttribute($typeNode, "validate"))) {
									$validateArr = explode(",", xml_getAttribute($typeNode, "validate"));
								}
							}
							if ($nullValueArr[$i] != "Null") $validateArr[] = "is_necessary";
							//utils_log($imePoljaArr[$i]." ".count($validateArr), "forms.log");
							$result["fieldtype".$i] = $inputType;
							$result["fieldName".$i] = $result["label".$i];
							$result["iden".$i] = (($nullValueArr[$i] == "Null") ? "0" : "1");
							$result["root".$i] = "";
							$result["width".$i] = "";
							$result["height".$i] = "";
							$result["max".$i] = "";
							$result["import".$i] = "";
							$result["where".$i] = "";
							$result["editGroup".$i] = "";
							$result["validate".$i] = count($validateArr);
							for ($k = 1; $k <= count($validateArr); $k++) {	
								$result["_validate".$i.$k] = $validateArr[$k-1];}
						}
						$result["IdenId"] = "1";
						xml_createNode($result);
					}
				} else {
					echo "<script>alert('"+ocpLabels("Object type already exists!")+"');</script>"; die();
				}
			} else { // edit tipa objekta
				$result = array(); 
				$result["Id"] = $typeId;
				$imeTipa = utils_requestStr(getPVar("ImeTipa"));
				$result["ImeTipa"] = $imeTipa;
				$result["Labela"] = utils_requestStr(getPVar("Labela"));
				$result["Grupa"] = utils_requestStr(getPVar("Grupa"));
				$result["Podforma"] = utils_requestInt(getPVar("Podforma"));

				$n = utils_requestInt(getPVar("n")); // stara polja
				$result["n"] = $n;
				for ($i = 0; $i < $n; $i++) {
					$result["Obrisi".$i] = utils_requestStr(getPVar("Obrisi".$i));
					$result["PoljeId".$i] = utils_requestInt(getPVar("PoljeId".$i));
					$result["RedPrikaza".$i] = utils_requestInt(getPVar("RedPrikaza".$i));
				}
				tipobj_editTip($result);
				$insertedLabels = lang_newLabela($result["Labela"]);
				$insertedLabels = lang_newLabela($result["Grupa"]) || $insertedLabels;

				$imePoljaArr = explode($delimiter, utils_requestStr(getPVar("novoImePolja")));
				if (utils_valid($imePoljaArr[0])){
					$tipArr = explode($delimiter, utils_requestStr(getPVar("novoTip")));
					$podtipIdArr = explode($delimiter, utils_requestStr(getPVar("novoPodtipId")));
					$nullValueArr = explode($delimiter, utils_requestStr(getPVar("novoNull")));
					$defaultValueArr = explode($delimiter, utils_requestStr(getPVar("novoDefault")));
					$redPrikazaArr = explode($delimiter, utils_requestStr(getPVar("novoRedPrikaza")));
					
					for ($i=0; $i < count($imePoljaArr); $i++){
						$result = array(); 

						$result["Id"] = $typeId;
						$result["ImeTipa"] = utils_requestStr(getPVar("ImeTipa"));
						$result["ImePolja"] = $imePoljaArr[$i];
						$result["Tip"] = $tipArr[$i];
						$result["PodtipId"] = $podtipIdArr[$i];
						$result["Null"] = $nullValueArr[$i];
						$result["Default"] = $defaultValueArr[$i];
						$result["RedPrikaza"] = $redPrikazaArr[$i];

						$success = tipobj_addField($result) && $success;
					}
				}
			}
	?><SCRIPT>
		parent.subMenuFrame.reconstruct();
		<?php if ($insertedLabels) { ?>
		alert("<?php echo ocpLabels("New labels have been added to Multilanguage support. Please translate them."); ?>");
		<?php	}	
			if (!$success){ ?>
		alert("<?php echo ocpLabels("Added fields must have Default value of allow Nulls");?>.");
		<?php	}?>
		parent.document.getElementById("resizableFrameset").setAttribute("rows", "100%,*");
	</SCRIPT><?php
		}
	} else {

		$action = utils_requestStr(getGVar("action"));
		$typeId = utils_requestInt(getGVar("typeId"));
		
		switch ($action){
			case "deleteField" : 
				$fieldId = utils_requestInt(getGVar("fieldId"));
				$fieldName = polja_getFieldName($fieldId);
				xml_removeFieldNode(tipobj_getName($typeId), $fieldName);
				tipobj_deleteField($typeId, $fieldId);
				$action = "iu";
				drawEditForm(intval($typeId));
				break;
			case "deleteType" : 
				xml_removeNode(tipobj_getName($typeId));
				tipobj_delete($typeId);
				?><script>parent.subMenuFrame.reconstruct();</script><?php
				break;
			case "iu" :
				drawEditForm(intval($typeId));
				break;
		}
	}

	function drawEditForm($typeId){
		$type = tipobj_getAllAboutType($typeId);
		$subTypes = tipobj_getAll("Labela", "ASC");
		$userHeight = utils_valid(getSVar("ocpUserHeight")) ? getSVar("ocpUserHeight") : "25%";
?><script language="javascript">
	window.onload = function(){
		parent.document.getElementById("resizableFrameset").setAttribute("rows", "<?php echo $userHeight; ?>,*");
	}
</script><div id="ocp_main_table">
<form name="formObject" id="formObject" method="post" onSubmit="return validate();" action="types_edit.php?<?php echo utils_randomQS();?>">
	<input type="hidden" name="Id" value="<?php echo $typeId; ?>">
	<input type="hidden" name="Action" value="Sacuvaj">
<table class="ocp_naslov_table">
	<tr>
		<td class="ocp_naslov_td">
		<?php
			if ($typeId != -1) {
				echo ocpLabels("Edit object type");
			} else {
				echo ocpLabels("New object type");
			}?>
		</td>
	</tr>
</table>
<table class="ocp_opcije_table" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td valign="top"><table class="ocp_opcije_table">
    <tr><?php 
			if ($typeId != -1) { // nije insert, svejedno
		?><td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Name");?>:</span></td>
		<td class="ocp_opcije_td"><span class="ocp_opcije_tekst2"><input type="hidden" name="ImeTipa"  value="<?php echo $type["Ime"];?>"><?php echo $type["Ime"];?></span></td><?php
			} else { // insert objekta
		?><td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Name");?>:</span></td>
		<td class="ocp_opcije_td"><input type="text" class="ocp_forma" name="ImeTipa" style="width: 100%;" value=""></td><?php		
			}
		?>
	</tr>
	<tr>
		<td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Label");?>:</span></td>
		<td class="ocp_opcije_td"><input type="text" class="ocp_forma" name="Labela" style="width:100%;" value="<?php echo (isset($type["Labela"]))? $type["Labela"] : ""; ?>"></td>
	</tr>
    <tr>
        <td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Menu group");?>: </span></td>
        <td class="ocp_opcije_td"><input type="text" class="ocp_forma" name="Grupa" style="width:100%;" value="<?php echo (isset($type["Grupa"]))? $type["Grupa"] : ""; ?>"></td>
    </tr>
	<tr>
		<td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Browse disabled")?>: </span></td>
		<td class="ocp_opcije_td"><input type="checkbox" NAME="Podforma" VALUE="1" <?php if (isset($type["SamoPodforma"]) && $type["SamoPodforma"] == "1") echo("checked");?>></td>
	</tr>
	<tr>
		<td colspan="2" class="ocp_opcije_td_header ocp_opcije_tekst4"><?php echo ocpLabels("List of fields");?> :</td>
	</tr>
	<tr>
		<td colspan="2" style="padding:0; background-color: #dce5ea; border-top: 1px solid #b3b1b1;">
			<div id="ocp_blok_menu_1">
				<table class="ocp_blokovi_table" >
					<tr>
						<td class="ocp_blokovi_td" style="padding-left: 6px;"></td>
						<td class="ocp_blokovi_td" style="text-align: right;"><?php 

							button_html(ocpLabels("Add order field"), "addOrderField();");
							button_html(ocpLabels("Add field"), "addField();");
						?></td>
					</tr>
				</table>
			</div><?php
		$polja = array();
		if ($typeId != -1) $polja = polja_getFields($typeId, NULL, NULL);
		
		if (count($polja) > 0){
	?><input type="hidden" name="n" value="<?php echo count($polja);?>">
	<table class="ocp_opcije_table" id="listTable" name="listTable" style="width:100%">
        <tr id="trHeader" style="position:relative; top:0px">
          <td width="17%" valign="top" class="ocp_opcije_td_header ocp_opcije_tekst3"><?php echo utils_toUpper(ocpLabels("Field name"));?></td>
          <td width="17%" valign="top" class="ocp_opcije_td_header ocp_opcije_tekst3"><?php echo utils_toUpper(ocpLabels("Field type"));?></td>
          <td width="20%" valign="top" class="ocp_opcije_td_header ocp_opcije_tekst3"><?php echo utils_toUpper(ocpLabels("Foreign key"));?></td>
          <td width="38%" valign="top" class="ocp_opcije_td_header ocp_opcije_tekst3"><?php echo utils_toUpper(ocpLabels("Order"));?></td>
          <td width="8%" valign="top" class="ocp_opcije_td_header ocp_opcije_tekst3"><?php echo utils_toUpper(ocpLabels("Delete"));?></td>
        </tr><?php
			for ($i=0; $i < count($polja); $i++){
				$polje = $polja[$i];?>
		<tr>
          <td class="ocp_opcije_td" style="border-top:0px;">
			<input type="hidden" name="PoljeId<?php echo $i;?>" value="<?php echo $polje["Id"];?>">
			<span class="ocp_opcije_tekst1"><?php echo $polje["ImePolja"];?></span></td>
          <td class="ocp_opcije_td" style="border-top:0px;">
			<input type="hidden" name="Tip<?php echo $i;?>" value="<?php echo $polje["TipTabela"];?>">
			<span class="ocp_opcije_tekst1"><?php echo $polje["TipTabela"];?> </span></td>
          <td class="ocp_opcije_td" style="border-top:0px;">
			<span class="ocp_opcije_tekst1"><?php if ($polje["TipTabela"] == "Objects") { echo $polje["PodtipIme"]; } else { echo " - "; }?> </span></td>
          <td class="ocp_opcije_td" style="border-top:0px;">
			<input type="text" class="ocp_forma" name="RedPrikaza<?php echo $i;?>" value="<?php echo $i;?>" style="width: 100%;"></td>
          <td class="ocp_opcije_td_forma" align="center" style="border-top:0px;">
			<img src="/ocp/img/opsti/kontrole/kontrola_obrisi_objekat.gif" border="0" width="20" height="21" onClick="deleteField('<?php echo $polje["Id"]?>');" title="<?php echo ocpLabels("Delete object")?>" style="cursor:pointer;">
			 </td>
        </tr><?php
			}
?>		</table>
    <br>
<?php	}	?>
    
	<iframe src="/ocp/admin/database/types_fields_list.php?<?php echo utils_randomQS();?>&typeId=<?php echo $typeId;?>&oldNoFields=<?php echo count($polja);?>" width="100%" height="20" frameborder="0" id="fieldsIframe" name="fieldsIframe" scrolling="no"></iframe>
	<input type="hidden" name="novoImePolja" value="">
	<input type="hidden" name="novoTip" value="">
	<input type="hidden" name="novoPodtipId" value="">
	<input type="hidden" name="novoNull" value="">
	<input type="hidden" name="novoDefault" value="">
	<input type="hidden" name="novoRedPrikaza" value="">
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
<script src="/ocp/validate/validate_double_quotes.js"></script>
<SCRIPT>
	var labReservedKeywords = ": <?php echo ocpLabels('is a reserved keyword'); ?>.";
	var labNotNumber = ": <?php echo ocpLabels('can not be number'); ?>.";
	var labFirstChar = ": <?php echo ocpLabels('first character must be letter or _'); ?>.";
	var labForrbidenChar = ": <?php echo ocpLabels('has forrbiden character'); ?>.";
	function validate(){
		var imeTipa = document.formObject.ImeTipa.value;
		if (imeTipa==""){
			alert("<?php echo ocpLabels("Name");?>"+" "+"<?php echo ocpLabels("must have value");?>.");
			return false;
		} else if (!validate_column_name(imeTipa, labNotNumber, 
										labFirstChar, labForrbidenChar, labReservedKeywords)) {
			return false;
		}

		if (!validate_table_name(imeTipa, labNotNumber, 
										labFirstChar, labForrbidenChar, labReservedKeywords)) {
			return false;
		}

		var labela = document.formObject.Labela.value;
		if (labela == "" || labela.indexOf("'")!=-1){
			alert("<?php echo ocpLabels("Type label can not be empty or have single quote");?>.");
			return false;
		} else {
			validate_double_quotes_field(document.formObject.Labela);
		}
		var grupa = document.formObject.Grupa.value;
		if (grupa == "" || grupa.indexOf("'")!=-1){
			alert("<?php echo ocpLabels("Type group can not be empty or have single quote");?>.");
			return false;
		} else {
			validate_double_quotes_field(document.formObject.Grupa);
		}
		
		return fieldsIframe.validateFieldForm();
	}

	function addField(){
		fieldsIframe.submitFieldForm();
	}

	function addOrderField(){
		fieldsIframe.addOrderField();
	}

	function deleteField(fieldId){
		if (confirm("<?php echo ocpLabels('Are you sure you want to delete field');?>?"))
			window.open( "types_edit.php?<?php echo utils_randomQS();?>&typeId=<?php echo $typeId;?>&fieldId="+fieldId+"&action=deleteField","_self");
	}
</SCRIPT>
</div>
<?php
	} ?>
</BODY>
</HTML>