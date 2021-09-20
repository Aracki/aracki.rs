<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../include/izvestaji.php");
	require_once("../../include/users.php");

	session_checkAdministrator() ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/ocp/css/opsti.css">
<link rel="stylesheet" href="/ocp/css/opcije.css">
</head>
<body class="ocp_body"><?php  
	$action = utils_requestStr(getPVar("Action"));

	if (utils_valid($action)){//submitovana su prava
		$reportId = utils_requestInt(getPVar("Id"));

		$groupCount = utils_requestInt(getPVar("Group_Count"));
		$reportRights = array();
		for ($i=0; $i < $groupCount; $i++){
			$groupId = utils_requestInt(getPVar("UGrp_Id_".$i));
			$group = users_getUserGroup($groupId);
			if ($group["Super"] == 1)
				$reportRights[$groupId] = 1;
			else 
				$reportRights[$groupId] = utils_requestInt(getPVar("UGrp_Rights".$groupId));
		}
//utils_dump($reportRights, 1);
		izv_saveReportRights($reportId, $reportRights);
		?><script>
			parent.subMenuFrame.reconstruct();
			parent.document.getElementById("resizableFrameset").setAttribute("rows", "100%,*");
		</script><?php  
	} else { //pre submita
		$reportId = utils_requestInt(getGVar("reportId"));	//izvestaj

		drawEditForm($reportId);
		
	}

	function drawEditForm($reportId){
		$report = izv_get($reportId);
		$groups = users_getAllUserGroups("UGrp_Name", "asc");

?><div id="ocp_main_table">
<form name="formObject" id="formObject" method="post" action="rights_edit.php?<?php echo utils_randomQS()?>">
	<input type="hidden" name="Id" value="<?php echo $reportId?>">
	<input type="hidden" name="Action" value="Izmeni">
	<input type="hidden" name="Group_Count" value="<?php echo count($groups)?>">
	<table class="ocp_naslov_table">
		<tr>
			<td class="ocp_naslov_td">
				<?php echo ocpLabels("Edit report rights")?>:&nbsp;<?php echo $report["Ime"]?>
			</td>
		</tr>
	</table>
	<table class="ocp_opcije_table" border="0" cellpadding="0" cellspacing="0">
		<tr>
			 <td width="22%" valign="top"  class="ocp_opcije_td" style="padding:6px;">
				<span class="ocp_opcije_tekst1"><?php echo ocpLabels("Report visible for")?>:</span>
			 </td>
			<td  class="ocp_opcije_td">
				<?php   for ($i=0; $i<count($groups); $i++){	
					$checked = "";
					if ($groups[$i]["Super"] == "1"){
						$checked = "checked";
					} else {
						$checked = izv_isVisible($reportId, $groups[$i]["Id"]) ? "checked" : "";
					}
				?>
				<input type="hidden" name="UGrp_Id_<?php echo $i?>" value="<?php echo $groups[$i]["Id"]?>">
				<input type="checkbox" name="UGrp_Rights<?php echo $groups[$i]["Id"]?>" value="1" <?php echo $checked?>>&nbsp;<span class="ocp_opcije_tekst1"><?php echo $groups[$i]["Name"]?></span><br/>
<?php  				}	?> 
			</td>
		</tr>
	</table>
	<table width="100%">
		<tr>
			<td height="40" align="center" class="ocp_text"><input type="submit" name="submitSave" class="ocp_dugme" value="<?php echo ocpLabels("Save")?>">&nbsp;<input type="button" name="submitCancel" class="ocp_dugme" onClick="parent.menuFrame.showSubmenuClose(true, true);" value="<?php echo ocpLabels("Cancel")?>"></td>
		</tr>
	</table>
</form>
</div><?php  
	$userHeight = utils_valid(getSVar("ocpUserHeight")) ? getSVar("ocpUserHeight") : "25%";
?>
<script language="javascript">
	window.onload = function(){
		parent.document.getElementById("resizableFrameset").setAttribute("rows", "<?php echo $userHeight?>,*");
	}
</script><?php  
	}	
?>
</body>
</html>