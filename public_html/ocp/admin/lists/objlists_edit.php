<?php
require_once("../../include/connect.php");
require_once("../../include/session.php");
require_once("../../include/selectradio.php");
require_once("../../include/polja.php");
?>

<?php session_checkAdministrator(); ?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/ocp/css/opsti.css">
<link rel="stylesheet" href="/ocp/css/opcije.css">
</head>
<body class="ocp_body"><?php
	$action = utils_requestStr(getPVar("Action"));

	if (utils_valid($action)){//submitovana je lista 
		$fieldId = utils_requestInt(getPVar("fieldId"));
		$listId = utils_requestStr(getPVar("listId"));
		con_update("update Polja set ImeListe = '".$listId."' where Id=".$fieldId);
	?><script>
		parent.subMenuFrame.reconstruct();
		parent.document.getElementById("resizableFrameset").setAttribute("rows", "100%,*");
	</script><?php
	} else {
		$fieldId = utils_requestInt(getGVar("fieldId"));
		$field = polja_getField($fieldId);
		$lista = array();
		if ($field["TipTabela"] == "Selects")
			$lista = selrad_getAllUsedListNames("SelectLista", "Ime", "asc");
		else 
			$lista = selrad_getAllUsedListNames("RadioLista", "Ime", "asc");
?><div id="ocp_main_table">
	<form name="formObject" id="formObject" method="post" onSubmit="return validate();" action="objlists_edit.php?<?php echo utils_randomQS();?>">
		<input type="hidden" name="Action" value="Izmeni">
		<input type="hidden" name="fieldId" value="<?php echo $fieldId;?>">
		<table class="ocp_naslov_table">
			<tr>
				<td class="ocp_naslov_td"><?php echo ocpLabels("Edit list");?></td>
			</tr>
		</table>
		<table class="ocp_opcije_table" border="0" cellpadding="0" cellspacing="0">
			<tr style="position:relative; top:0px">
			    <td class="ocp_opcije_td ocp_opcije_tekst1" style="width: 22%;"><?php echo (ocpLabels("Field"));?>&nbsp;<?php echo $field["ImePolja"];?>: </td>
				<td class="ocp_opcije_td ocp_opcije_tekst1"><select name="listId" style="width: 100%;" class="ocp_forma">
				<option value="">--<?php echo ocpLabels("choose list");?>--</option><?php
				for ($i=0; $i<count($lista); $i++){
				?><option value="<?php echo $lista[$i];?>" <?php if ($field["ImeListe"] == $lista[$i]) echo("selected");?>><?php echo ocpLabels($lista[$i]);?></option><?php
				}
				?></select></td>
			</tr>
		</table>
		<table width="100%">
			<tr>
				<td height="40" align="center" class="ocp_text"><input type="submit" name="submitSave" class="ocp_dugme" value="<?php echo ocpLabels("Save");?>">&nbsp;<input type="button" name="submitCancel" class="ocp_dugme" onClick="parent.menuFrame.showSubmenuClose(true, true);" value="<?php echo ocpLabels("Cancel");?>"></td>
			</tr>
		</table>
	</form>
</div><?php
	$userHeight = utils_valid(getSVar("ocpUserHeight")) ? getSVar("ocpUserHeight") : "25%";
?><script language="javascript">
	window.onload = function(){
		parent.document.getElementById("resizableFrameset").setAttribute("rows", "<?php echo $userHeight;?>,*");
	}
	function validate(){
		if (document.formObject.listId.value == ""){
			alert("<?php echo ocpLabels("You have to choose field\'s list of value");?>.");
			return false;
		}
		return true;
	}
</script><?php
	}
?></body>
</html>