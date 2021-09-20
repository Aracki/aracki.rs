<?php 
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../include/izvestaji.php");
	require_once("../../include/language.php");
	
	session_checkAdministrator() ?>
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
		if ($action == "Sacuvaj"){
			$reportId = utils_requestInt(getPVar("Id"));
			
			$report = array(); 
			$report["Ime"] = utils_requestStr(getPVar("Ime"));
			$report["Grupa"] = utils_requestStr(getPVar("Grupa"));
			$report["Upit"] = utils_requestStr(getPVar("Upit"), true);
			$report["ParametarXml"] = utils_requestStr(getPVar("ParametarXml"), true);
			$report["DetaljniIzvestaj"] = utils_requestInt(getPVar("DetaljniIzvestaj"));
			$report["Aktivan"] = utils_requestInt(getPVar("Aktivan"));

			$insertedLabels = false;
			if ($reportId == -1){//insert izvestaja
				izv_insert($report);
			} else {//edit izvestaja
				$report["Id"] = $reportId;
				izv_update($report);
				
			}
			$insertedLabels = lang_newLabela($report["Ime"]);
			$insertedLabels = lang_newLabela($report["Grupa"]) || $insertedLabels;
	?><SCRIPT>
		parent.subMenuFrame.reconstruct();
		<?php  	if ($insertedLabels){	?>
		alert("<?php echo ocpLabels("New labels have been added to Multilanguage support. Please translate them.")?>");
		<?php  	} ?>
		parent.document.getElementById("resizableFrameset").setAttribute("rows", "100%,*");
	</SCRIPT><?php  
		}
	} else {

		$action = utils_requestStr(getGVar("action"));
		$reportId = utils_requestInt(getGVar("reportId"));
		
		switch ($action){
			case "deleteReport" : 
				izv_delete($reportId);
				?><script>parent.subMenuFrame.reconstruct();</script><?php  
				break;
			case "iu" :
				drawEditForm($reportId);
				break;
		}
	}

	function drawEditForm($reportId){
		$report = izv_get($reportId);
		$userHeight = utils_valid(getSVar("ocpUserHeight")) ? getSVar("ocpUserHeight") : "25%";
?><script language="javascript">
	window.onload = function(){
		parent.document.getElementById("resizableFrameset").setAttribute("rows", "<?php echo $userHeight?>,*");
	}
</script><div id="ocp_main_table">
<form name="formObject" id="formObject" method="post" action="reports_edit.php?<?php echo utils_randomQS()?>" onsubmit="return validate();">
	<input type="hidden" name="Id" value="<?php echo $reportId?>">
	<input type="hidden" name="Action" value="Sacuvaj">
<table class="ocp_naslov_table">
	<tr>
		<td class="ocp_naslov_td"><?php   if ($reportId != -1){
				?><?php echo ocpLabels("Edit report")?><?php  
			} else {
				?><?php echo ocpLabels("New report")?><?php  
			}?></td>
	</tr>
</table>
<table class="ocp_opcije_table" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td valign="top"><table class="ocp_opcije_table">
    <tr>
		<td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Name")?>:</span></td>
		<td class="ocp_opcije_td"><input type="text" class="ocp_forma" name="Ime" style="width: 100%;" value="<?php echo (isset($report["Ime"]) ? $report["Ime"] : "")?>"></td>
	</tr>
	<tr>
		<td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Menu group")?>:</span></td>
		<td class="ocp_opcije_td"><input type="text" class="ocp_forma" name="Grupa" style="width:100%;" value="<?php echo (isset($report["Grupa"]) ? $report["Grupa"] : "")?>"></td>
	</tr>
    <tr>
        <td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Query")?>: </span></td>
        <td class="ocp_opcije_td">
			<textarea class="ocp_forma" style="width:100%;height: 80px;" name="Upit"><?php echo (isset($report["Upit"]) ? $report["Upit"] : "")?></textarea>
			<span class="ocp_opcije_tekst1"><?php echo ocpLabels("Use ';' to delimit more than one query")?></span>
		</td>
    </tr>
	<tr>
        <td class="ocp_opcije_td" style="width:22%">
			<?php   $oldXml = (isset($report["ParametarXml"]) && utils_valid($report["ParametarXml"])) ? $report["ParametarXml"] : "<parameters>\n</parameters>";?>
			<span class="ocp_opcije_tekst1"><?php echo ocpLabels("Parameter Xml")?>: </span></td>
        <td class="ocp_opcije_td"><textarea class="ocp_forma" style="width:100%;height: 80px;" name="ParametarXml"><?php echo $oldXml?></textarea></td>
    </tr>
	<tr>
		<td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Detail report")?>:</span></td>
		<td class="ocp_opcije_td">
			<select name="DetaljniIzvestaj" class="ocp_forma" style="width:100%">
				<option value=""></option><?php   
		$reports = izv_getAll("Ime", "asc");	
		$report["DetaljniIzvestaj"] = (isset( $report["DetaljniIzvestaj"])) ?  $report["DetaljniIzvestaj"] : "";
		for ($i=0; $i < count($reports); $i++){
			?><option value="<?php echo $reports[$i]["Id"]?>" <?php echo ($reports[$i]["Id"] == $report["DetaljniIzvestaj"] ? "selected" : "")?>><?php echo $reports[$i]["Ime"]?></option><?php  
		}
		?></select>
			<span class="ocp_opcije_tekst1"><?php echo ocpLabels("If batch queries is entered, detailed report will be linked for the first one")?></span>
		</td>
	</tr>
	<tr>
		<td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Active")?>: </span></td>
		<td class="ocp_opcije_td"><input type="checkbox" NAME="Aktivan" VALUE="1" <?php   if (isset($report["Aktivan"]) && $report["Aktivan"] == "1") echo "checked"?>></td>
	</tr>
</table>
<table width="100%">
<tr>
<td height="40" align="center" class="ocp_text"><input type="submit" name="submitSave" class="ocp_dugme" value="<?php echo ocpLabels("Save")?>">&nbsp;<input type="button" name="submitCancel" class="ocp_dugme" onClick="parent.menuFrame.showSubmenuClose(true, true);" value="<?php echo ocpLabels("Cancel")?>"></td>
</tr>
</table>
</form>
</div>
<SCRIPT>
	function validate(){
		if (document.formObject.Ime.value==""){
			alert("<?php echo ocpLabels("Name")?>"+" "+"<?php echo ocpLabels("must have value")?>.");
			return false;
		}

		if (document.formObject.Grupa.value==""){
			alert("<?php echo ocpLabels("Group")?>"+" "+"<?php echo ocpLabels("must have value")?>.");
			return false;
		}

		if (document.formObject.Upit.value==""){
			alert("<?php echo ocpLabels("Query")?>"+" "+"<?php echo ocpLabels("must have value")?>.");
			return false;
		}

		return true;
	}

</SCRIPT>
</div>
<?php  
	} ?>
</BODY>
</HTML>