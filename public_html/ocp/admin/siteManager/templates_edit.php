<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../siteManager/lib/template.php");
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
		$tempId = utils_requestInt(getPVar("Temp_Id"));
		$temp = array();
		$insertedLabels = false;
		if ($tempId == "-1"){//insert
			$temp["Temp_Naziv"] = utils_requestStr(getPVar("Temp_Naziv"));
			$temp["Temp_Url"] = utils_requestStr(getPVar("Temp_Url"));
			template_new($temp);

			$insertedLabels = lang_newLabela($temp["Temp_Naziv"]) || $insertedLabels;
		} else {//update
			$temp["Temp_Id"] = utils_requestInt(getPVar("Temp_Id"));
			$temp["Temp_Naziv"] = utils_requestStr(getPVar("Temp_Naziv"));
			$temp["Temp_Url"] = utils_requestStr(getPVar("Temp_Url"));
			template_edit($temp);

			$insertedLabels = lang_newLabela($temp["Temp_Naziv"]) || $insertedLabels;
		}
		
?><SCRIPT>
		parent.subMenuFrame.reconstruct();
		<?php	if ($insertedLabels){	?>
		alert("<?php echo ocpLabels("New labels have been added to Multilanguage support. Please translate them.");?>");
		<?php	}?>
		parent.document.getElementById("resizableFrameset").setAttribute("rows", "100%,*");
	</SCRIPT><?php
	} else {
		$action = utils_requestStr(getGVar("action"));
		$tempId = utils_requestInt(getGVar("tempId"));

		switch ($action){
			case "delete" : 
				template_delete($tempId);
				?><script>parent.subMenuFrame.reconstruct();</script><?php
				break;
			case "iu" :
				drawEditForm($tempId);
				break;
		}
	}

	function drawEditForm($tempId){
		$template = template_get($tempId);
?><div id="ocp_main_table">
<form name="formObject" id="formObject" method="post" onSubmit="return validate();" action="templates_edit.php?<?php echo utils_randomQS();?>">
	<input type="hidden" name="Action" value="Sacuvaj">
	<input type="hidden" name="Temp_Id" value="<?php echo $tempId;?>">
	<table class="ocp_naslov_table">
		<tr>
			<td class="ocp_naslov_td"><?php if ($tempId != "-1") {?><?php echo ocpLabels("Edit template");?>: <?php echo ocpLabels($template["Temp_Naziv"]);?><?php } else {?><?php echo ocpLabels("New template");?><?php } ?></td>
		</tr>
	</table>
	<table class="ocp_opcije_table" border="0" cellpadding="0" cellspacing="0">
		<tr style="position:relative; top:0px">
			<td class="ocp_opcije_td ocp_opcije_tekst1" style="width: 22%;"><?php echo ocpLabels("Title");?></td><?php
		$tempTitle = ($tempId != "-1") ? $template["Temp_Naziv"] : "";
			?><td class="ocp_opcije_td ocp_opcije_tekst1"><input type="text" class="ocp_forma" name="Temp_Naziv" style="width: 100%;" value="<?php echo $tempTitle;?>"/></td>
		</tr>
		<tr style="position:relative; top:0px">
			<td class="ocp_opcije_td ocp_opcije_tekst1" style="width: 22%;">Url</td>
			<td class="ocp_opcije_td ocp_opcije_tekst1">
			<table class="ocp_uni_table">
				<tr>
					<td class="ocp_dugmici_td_levi"><?php
		$tempUrl = ($tempId != "-1") ? $template["Temp_Url"] : "";
			?><input type="text" class="ocp_forma" name="Temp_Url" style="width: 100%;" value="<?php echo $tempUrl;?>"/></td>
					<td class="ocp_dugmici_td_desni_3"><a href="javascript:x = window.open('/ocp/controls/fileControl/frameset.php?<?php echo utils_randomQS();?>&root=/templates&field=formObject.Temp_Url','imgKontrola','top=100, left=50, width=760, height=560, scrollbars=yes, resizable=yes, status=yes'); x.focus();"><img src="/ocp/img/opsti/kontrole/kontrola_browse.gif" class="ocp_kontrola" title="<?php echo ocpLabels("Browse server");?>"/></a>
					<a href="javascript: void(0);" onClick="var urlCont=formObject.Temp_Url;window.open(urlCont.value, '', 'width=500, height=400, resizable, scrollbars');"><img src="/ocp/img/opsti/kontrole/kontrola_preview.gif" class="ocp_kontrola" title="<?php echo ocpLabels("Selected link preview");?>"/></a></td>
				</tr>
			</table></td>
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
		if (document.formObject.Temp_Naziv.value == ""){
			alert("<?php echo ocpLabels("Title");?>"+" "+"<?php echo ocpLabels("must have value");?>.");
			return false;
		}

		if (document.formObject.Temp_Url.value == ""){
			alert("Url <?php echo ocpLabels("must have value");?>.");
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
