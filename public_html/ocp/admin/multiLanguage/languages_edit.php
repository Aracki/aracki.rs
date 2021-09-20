<?php
require_once("../../include/connect.php");
require_once("../../include/session.php");
require_once("../../include/language.php");
require_once("../../include/users.php");
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
		$langId = utils_requestInt(getPVar("Id"));
		if ($langId == "-1"){//insert
			$langId = lang_newJezik(utils_requestStr(getPVar("Jezik")));
		} else {//update
			$language = array();
			$language["Id"] = utils_requestInt(getPVar("Id"));
			$language["Jezik"] = utils_requestStr(getPVar("Jezik"));
			lang_editJezik($language);
		}

		if (utils_requestInt(getPVar("OcpJezik")) == "1"){
			//ako je izabran ovaj jezik, iskljucen je MS
			$oldOcpLang = getSVar("ocpLanguage");
			if ($langId != $oldOcpLang){//i ranije je bio isljucen MS, ali sa drugim jezikom
				con_update("update Ocp set OcpJezik = ".$langId." where Id=1");
				setSVar("ocpLanguage", $langId);
				users_saveSettings(getSVar("ocpUserGroup"), getSVar("ocpUserId"), 
									getSVar("ocpUserWidth"), getSVar("ocpUserHeight"), $langId);
				setSVar("ocpLabels", con_getResultsDict("select Labela, Vrednost from OcpPrevod, OcpLabela where IdLabele = OcpLabela.Id and IdJezika=".$langId));
?><script>
			if (parent.opener != null) parent.opener.location.href = "/ocp/frameset.php?<?php echo utils_randomQS();?>";
			top.parent.location.href = "/ocp/admin/frameset.php?<?php echo utils_randomQS();?>"
</script><?php
			}
		} else {
			$oldOcpLang = getSVar("ocpLanguage");
			if (is_numeric($oldOcpLang) && ($oldOcpLang == $langId)){//ako se vracamo na MS
				con_update("update Ocp set OcpJezik = NULL where Id=1");
				setSVar("ocpLanguage", NULL);
			}
		}
		
?><SCRIPT>
		parent.subMenuFrame.reconstruct();
		parent.document.getElementById("resizableFrameset").setAttribute("rows", "100%,*");
	</SCRIPT><?php
	} else {
		$action = utils_requestStr(getGVar("action"));
		$langId = utils_requestInt(getGVar("langId"));

		switch ($action){
			case "delete" : 
				lang_deleteJezik($langId);
				?><script>parent.subMenuFrame.reconstruct();</script><?php
				break;
			case "iu" :
				drawEditForm($langId);
				break;
		}
	}

	function drawEditForm($langId){
		$language = lang_getJezik($langId);
?><div id="ocp_main_table">
<form name="formObject" id="formObject" method="post" onSubmit="return validate();" action="languages_edit.php?<?php echo utils_randomQS();?>">
	<input type="hidden" name="Action" value="Sacuvaj">
	<input type="hidden" name="Id" value="<?php echo $langId;?>">
	<table class="ocp_naslov_table">
		<tr>
			<td class="ocp_naslov_td"><?php if ($langId != "-1") {?><?php echo ocpLabels("Edit language");?>: <?php echo $language["Jezik"];?><?php } else {?><?php echo ocpLabels("New language");?><?php } ?></td>
		</tr>
	</table>
	<table class="ocp_opcije_table" border="0" cellpadding="0" cellspacing="0">
		<tr style="position:relative; top:0px">
			<td class="ocp_opcije_td ocp_opcije_tekst1" style="width: 22%;"><?php echo ocpLabels("Title");?></td><?php
		$langTitle = ($langId != "-1") ? $language["Jezik"] : "";
			?><td class="ocp_opcije_td ocp_opcije_tekst1"><input type="text" class="ocp_forma" name="Jezik" style="width: 100%;" value="<?php echo $langTitle;?>"/></td>
		</tr>
		<tr style="position:relative; top:0px">
			<td class="ocp_opcije_td ocp_opcije_tekst1" style="width: 22%;"><?php echo ocpLabels("Switch Ocp to this language");?></td>
			<td class="ocp_opcije_td ocp_opcije_tekst1">
			<input type="checkbox" name="OcpJezik" value="1" <?php if (getSVar("ocpLanguage") == $langId) echo("checked");?>/></td>
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
		if (document.formObject.Jezik.value == ""){
			alert("<?php echo ocpLabels("Language");?>"+" "+"<?php echo ocpLabels("must have value");?>.");
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
