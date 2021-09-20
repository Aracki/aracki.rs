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
		$langId = utils_requestInt(getPVar("IdJezika"));
		$labId = utils_requestInt(getPVar("IdLabele"));
		$transId = utils_requestInt(getPVar("IdPrevoda"));

		if (!utils_valid($langId) || ($langId == 0)){//insert
			lang_insertPrevod(utils_requestInt(getPVar("language")), $labId, utils_requestStr(getPVar("translation")));
		} else {//update
			if (!utils_valid($transId) || ($transId == 0)){
				lang_insertPrevod($langId, $labId, utils_requestStr(getPVar("translation")));
			} else {
				lang_updatePrevod($langId, $labId, utils_requestStr(getPVar("translation")));
			}
		}
?><SCRIPT>
		parent.subMenuFrame.reconstruct();
		parent.document.getElementById("resizableFrameset").setAttribute("rows", "100%,*");
	</SCRIPT><?php
	} else {
		$langId = utils_requestInt(getGVar("langId"));
		$labId = utils_requestInt(getGVar("labId"));
		$transId = utils_requestInt(getGVar("transId"));

		drawEditForm($langId, $labId, $transId);
	}

	function drawEditForm($langId, $labId, $transId){
		$label = lang_getLabela($labId);
?><div id="ocp_main_table">
<form name="formObject" id="formObject" method="post" onSubmit="return validate();" action="translation_edit.php?<?php echo utils_randomQS();?>">
	<input type="hidden" name="Action" value="Sacuvaj">
	<input type="hidden" name="IdJezika" value="<?php echo $langId;?>">
	<input type="hidden" name="IdLabele" value="<?php echo $labId;?>">
	<input type="hidden" name="IdPrevoda" value="<?php echo $transId;?>">
	<table class="ocp_naslov_table">
		<tr>
			<td class="ocp_naslov_td"><?php echo ocpLabels("Edit translation");?>: <?php echo $label["Labela"];?></td>
		</tr>
	</table>
	<table class="ocp_opcije_table" border="0" cellpadding="0" cellspacing="0">
		<tr style="position:relative; top:0px">
			<td class="ocp_opcije_td ocp_opcije_tekst1" style="width: 22%;"><?php echo ocpLabels("Language");?></td><td class="ocp_opcije_td ocp_opcije_tekst1"><?php
		if (utils_valid($langId) && ($langId != 0)){
			$language = lang_getJezik($langId);
			?><span class="ocp_opcije_tekst1"><?php echo $language["Jezik"];?></span><?php
		} else {
			$niz = lang_getJezici("Jezik", "asc");
			?><select class="ocp_forma" style="width:100%" name="language"><?php
			for ($i=0; $i<count($niz); $i++) {
				echo('<option value="'+$niz[$i]["Id"]+'">'+$niz[$i]["Jezik"]+'</option>');}	
					?></select><?php
		}
			?>
			</td>
		</tr>
		<tr style="position:relative; top:0px">
			<td class="ocp_opcije_td ocp_opcije_tekst1" style="width: 22%;"><?php echo ocpLabels("Label");?></td>
			<td class="ocp_opcije_td ocp_opcije_tekst1"><span class="ocp_opcije_tekst1"><?php echo $label["Labela"];?></span></td>
		</tr>
		<tr style="position:relative; top:0px">
			<td class="ocp_opcije_td ocp_opcije_tekst1" style="width: 22%;"><?php echo ocpLabels("Translation");?></td>
			<td class="ocp_opcije_td ocp_opcije_tekst1"><?php
		$transText = "";
		if (utils_valid($transId) && ($transId != 0)){
			$translation = lang_getPrevodById($transId);
			$transText = $translation["Vrednost"];
		} 
			?><input type="text" class="ocp_forma" style="width:100%;" name="translation" value="<?php echo $transText;?>"></td>
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
		formObject.translation.focus();
	}

	function validate(){
		if (document.formObject.translation.value == ""){
			alert("<?php echo ocpLabels("Translation");?>"+" "+"<?php echo ocpLabels("must have value");?>.");
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
