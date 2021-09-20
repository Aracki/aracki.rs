<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../include/language.php");
	require_once("../../include/users.php");
	require_once("../../include/xml_tools.php");
?>

<?php session_checkAdministrator(); ?>

<HTML>
<HEAD>
<TITLE> OCP </TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/ocp/css/opsti.css">
<link rel="stylesheet" href="/ocp/css/opcije.css">
<script src="/ocp/validate/validate_double_quotes.js"></script>
</HEAD>
<BODY class="ocp_body"><div id="ocp_main_table"><?php
	$action = utils_requestStr(getPVar("Action"));

	if (utils_valid($action)){
		$langId = utils_requestInt(getPVar("langId"));
		$path = utils_requestStr(getPVar("path"));
		$mess = "";
		if ($action == "Export"){ //export
			$mess = lang_exportLanguage($langId, $path);
		} else if ($action == "Import"){//import
			$language = utils_requestStr(getPVar("newLanguage"));
			$append = utils_requestStr(getPVar("appendLabels"));
			$mess = lang_importLanguage($langId, $language, $path, $append);			
		}
			require_once("../../include/design/message.php"); 
			echo message_info(ocpLabels($mess).".");
	}
	
	$languages = lang_getJezici("Jezik", "asc");
	drawExportForm($languages);
	drawImportForm($languages);

	function drawExportForm($languages){
?><form name="formObject" id="formObject" method="post" onSubmit="return validate();" action="languages_export.php?<?php echo utils_randomQS();?>">
	<input type="hidden" name="Action" value="Export">
	<table class="ocp_naslov_table">
		<tr>
			<td class="ocp_naslov_td"><?php echo ocpLabels("Export language");?></td>
		</tr>
	</table>
	<table class="ocp_opcije_table" border="0" cellpadding="0" cellspacing="0">
		<tr style="position:relative; top:0px">
			<td class="ocp_opcije_td ocp_opcije_tekst1" style="width: 22%;"><?php echo ocpLabels("Language");?></td>
			<td class="ocp_opcije_td ocp_opcije_tekst1"><select name="langId" class="ocp_forma" style="width:100%">
				<option value="">--<?php echo ocpLabels("choose language");?>--</option><?php
		for ($i=0; $i<count($languages); $i++){
		?><option value="<?php echo $languages[$i]["Id"];?>"><?php echo $languages[$i]["Jezik"];?></option><?php
		}
			?></select></td>
		</tr>
		<tr style="position:relative; top:0px">
			<td class="ocp_opcije_td ocp_opcije_tekst1" style="width: 22%;"><?php echo ocpLabels("Export file path");?></td>
			<td class="ocp_opcije_td ocp_opcije_tekst1">
			<table class="ocp_uni_table">
				<tr>
					<td class="ocp_dugmici_td_levi"><input type="text" class="ocp_forma" name="path" style="width: 100%;"/></td>
					<td class="ocp_dugmici_td_desni_3"><a href="javascript:x = window.open('/ocp/controls/fileControl/frameset.php?<?php echo utils_randomQS();?>&root=/&field=formObject.path','imgKontrola','top=100, left=50, width=760, height=560, scrollbars=yes, resizable=yes, status=yes'); x.focus();"><img src="/ocp/img/opsti/kontrole/kontrola_browse.gif" class="ocp_kontrola" title="<?php echo ocpLabels("Browse server");?>"/></a>
					<a href="javascript: void(0);" onClick="urlCont=formObject.path;window.open(urlCont.value, '', 'width=500, height=400, resizable, scrollbars');"><img src="/ocp/img/opsti/kontrole/kontrola_preview.gif" class="ocp_kontrola" title="<?php echo ocpLabels("Selected link preview");?>"/></a></td>
				</tr>
			</table></td>
		</tr>
	</table>
	<table width="100%">
		<tr>
			<td height="40" align="center" class="ocp_text"><input type="submit" name="submitSave" class="ocp_dugme" value="<?php echo ocpLabels("Export");?>">&nbsp;<input type="button" name="submitCancel" class="ocp_dugme" onClick="document.formObject.reset();" value="<?php echo ocpLabels("Cancel");?>"></td>
		</tr>
	</table>
</form>
<script language="javascript">
	window.onload = function(){
		window.open('/ocp/html/blank.html','detailFrame');
	}

	function validate(){
		if (document.formObject.langId.value == ""){
			alert("<?php echo ocpLabels("Language");?>"+" "+"<?php echo ocpLabels("must have value");?>.");
			return false;
		}

		if (document.formObject.path.value == ""){
			alert("<?php echo ocpLabels("File path");?>"+" "+"<?php echo ocpLabels("must have value");?>.");
			return false;
		}
		validate_double_quotes(document.formObject);
		return true;
	}
</script><?php
	}

	function drawImportForm($languages){
?><form name="formObject2" id="formObject2" method="post" onSubmit="return validate2();" action="languages_export.php?<?php echo utils_randomQS();?>">
	<input type="hidden" name="Action" value="Import">
	<table class="ocp_naslov_table">
		<tr>
			<td class="ocp_naslov_td"><?php echo ocpLabels("Import language");?></td>
		</tr>
	</table>
	<table class="ocp_opcije_table" border="0" cellpadding="0" cellspacing="0">
		<tr style="position:relative; top:0px">
			<td class="ocp_opcije_td ocp_opcije_tekst1" style="width: 22%;"><?php echo ocpLabels("Language");?></td>
			<td class="ocp_opcije_td ocp_opcije_tekst1"><select name="langId" class="ocp_forma" style="width:100%">
				<option value="">--<?php echo ocpLabels("choose language");?>--</option><?php
		for ($i=0; $i<count($languages); $i++){
		?><option value="<?php echo $languages[$i]["Id"];?>"><?php echo $languages[$i]["Jezik"];?></option><?php
		}
			?></select></td>
		</tr>
		<tr style="position:relative; top:0px">
			<td class="ocp_opcije_td ocp_opcije_tekst1" style="width: 22%;"><?php echo ocpLabels("New language");?></td>
			<td class="ocp_opcije_td ocp_opcije_tekst1"><input type="text" class="ocp_forma" name="newLanguage" style="width: 100%;"/></td>
		</tr>
		<tr style="position:relative; top:0px">
			<td class="ocp_opcije_td ocp_opcije_tekst1" style="width: 22%;"><?php echo ocpLabels("Import file path");?></td>
			<td class="ocp_opcije_td ocp_opcije_tekst1">
			<table class="ocp_uni_table">
				<tr>
					<td class="ocp_dugmici_td_levi"><input type="text" class="ocp_forma" name="path" style="width: 100%;"/></td>
					<td class="ocp_dugmici_td_desni_3"><a href="javascript:x = window.open('/ocp/controls/fileControl/frameset.php?<?php echo utils_randomQS();?>&root=/&field=formObject2.path','imgKontrola','top=100, left=50, width=760, height=560, scrollbars=yes, resizable=yes, status=yes'); x.focus();"><img src="/ocp/img/opsti/kontrole/kontrola_browse.gif" class="ocp_kontrola" title="<?php echo ocpLabels("Browse server");?>"/></a>
					<a href="javascript: void(0);" onClick="urlCont=formObject2.path;window.open(urlCont.value, '', 'width=500, height=400, resizable, scrollbars');"><img src="/ocp/img/opsti/kontrole/kontrola_preview.gif" class="ocp_kontrola" title="<?php echo ocpLabels("Selected link preview");?>"/></a></td>
				</tr>
			</table></td>
		</tr>
		<tr style="position:relative; top:0px">
			<td class="ocp_opcije_td ocp_opcije_tekst1" style="width: 22%;"><?php echo ocpLabels("Append labels");?></td>
			<td class="ocp_opcije_td ocp_opcije_tekst1"><input type="checkbox" value="1" name="appendLabels" checked/></td>
		</tr>
	</table>
	<table width="100%">
		<tr>
			<td height="40" align="center" class="ocp_text"><input type="submit" name="submitSave" class="ocp_dugme" value="<?php echo ocpLabels("Import");?>">&nbsp;<input type="button" name="submitCancel" class="ocp_dugme" onClick="document.formObject2.reset();" value="<?php echo ocpLabels("Cancel");?>"></td>
		</tr>
	</table>
</form>
<script language="javascript">
	function validate2(){
		if ((document.formObject2.langId.value == "") && (document.formObject2.newLanguage.value == "")){
			alert("<?php echo ocpLabels("You have to choose existing language or provide new one");?>.");
			return false;
		}

		if (document.formObject2.path.value == ""){
			alert("<?php echo ocpLabels("File path");?>"+" "+"<?php echo ocpLabels("must have value");?>.");
			return false;
		}
		validate_double_quotes(document.formObject);
		return true;
	}
</script><?php
	}
	?></div>
</Body>
</html>
