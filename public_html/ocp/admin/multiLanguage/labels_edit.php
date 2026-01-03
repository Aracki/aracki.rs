<?php
require_once("../../include/connect.php");
require_once("../../include/session.php");
require_once("../../include/language.php");
?>

<?php session_checkAdministrator(); ?>

<HTML>
<HEAD>
<TITLE> OCP </TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/ocp/css/opsti.css">
<link rel="stylesheet" href="/ocp/css/opcije.css">
</HEAD>
<BODY class="ocp_body"><?php
	$action = utils_requestStr(getPVar("Action"));

	if (utils_valid($action)){
		$labId = utils_requestInt(getPVar("Id"));
		if ($labId == "-1"){//insert
			$retVal = lang_newLabela(utils_requestStr(getPVar("Labela")));
			if (!$retVal){
			?><script>alert("<?php echo ocpLabels('Label exists already in database');?>.");</script><?php
			}
		}else{ //update
			lang_editLabela($labId, utils_requestStr(getPVar("Labela")));
		}
?><SCRIPT>
		parent.subMenuFrame.reconstruct();
		parent.document.getElementById("resizableFrameset").setAttribute("rows", "100%,*");
	</SCRIPT><?php
	} else {
		$action = utils_requestStr(getGVar("action"));
		$labId = utils_requestInt(getGVar("labId"));

		switch ($action){
			case "delete" : 
				lang_deleteLabela($labId);
				?><script>parent.subMenuFrame.reconstruct();</script><?php
				break;
			case "iu" :
				drawEditForm($labId);
				break;
		}
	}

	function drawEditForm($labId){
		$label = lang_getLabela($labId);
?><div id="ocp_main_table">
<form name="formObject" id="formObject" method="post" onSubmit="return validate();" action="labels_edit.php?<?php echo utils_randomQS();?>">
	<input type="hidden" name="Action" value="Sacuvaj">
	<input type="hidden" name="Id" value="<?php echo $labId;?>">
	<table class="ocp_naslov_table">
		<tr>
			<td class="ocp_naslov_td"><?php if ($labId != "-1") {?><?php echo ocpLabels("Edit label");?>: <?php echo $label["Labela"];?><?php } else {?><?php echo ocpLabels("New label");?><?php } ?></td>
		</tr>
	</table>
	<table class="ocp_opcije_table" border="0" cellpadding="0" cellspacing="0">
		<tr style="position:relative; top:0px">
			<td class="ocp_opcije_td ocp_opcije_tekst1" style="width: 22%;"><?php echo ocpLabels("Label");?></td><?php
		$labTitle = ($labId != "-1") ? $label["Labela"] : "";
			?><td class="ocp_opcije_td ocp_opcije_tekst1"><input type="text" class="ocp_forma" name="Labela" style="width: 100%;" value="<?php echo $labTitle;?>"/></td>
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
?>
<script src="/ocp/validate/validate_double_quotes.js"></script>
<script language="javascript">
	window.onload = function(){
		parent.document.getElementById("resizableFrameset").setAttribute("rows", "<?php echo $userHeight;?>,*");
	}
	function validate(){
		if (document.formObject.Labela.value == ""){
			alert("<?php echo ocpLabels("Label");?>"+" "+"<?php echo ocpLabels("must have value");?>.");
			return false;
		}
		validate_double_quotes(document.formObject);
		return true;
	}
</script><?php
	}
	?>
</Body>
</html>
