<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../include/tipoviobjekata.php");
	require_once("../../include/polja.php");
	require_once("../../include/objekti.php");
	require_once("../../include/users.php");
	require_once("../../include/xml.php");
	require_once("../../include/xml_tools.php");
?>

<?php session_checkAdministrator(); ?>

<html>
<head>
<link href="/ocp/css/opsti.css" rel="stylesheet" type="text/css">
<link href="/ocp/css/dugmici.css" rel="stylesheet" type="text/css">
<link href="/ocp/css/opcije.css" rel="stylesheet" type="text/css">
<script src="/ocp/jscript/helpcalendar.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body  class="ocp_body">
<div id="ocp_main_table"><?php
	$action = utils_requestStr(getGVar("action"));
	if (utils_valid($action)){
		if ($action == "view"){
			drawQueryForm();
		} else if ($action == "delete"){
			drawDeleteForm();
		}
	}

	function drawQueryForm(){
		$userGroups = users_getAllUserGroups("UGrp_Name", "asc");
		$types = tipobj_getAllTypes();
?><form name="formObject" id="formObject" method="post" action="activities_list.php?<?php echo utils_randomQS();?>"  onSubmit="return validate();">
	<input type="hidden" name="Action" value="view"> 
	<input type="hidden" name="ocp_brojac" value="0">
	<input type="hidden" name="ocp_broj" value="50">

	<table class="ocp_blokovi_table">
	  <tr>
		<td class="ocp_blokovi_td" style="padding-right:0px;padding-left:6px;padding-bottom:4px;padding-top:4px"><?php echo ocpLabels("Search activities log");?>:</td>
	  </tr>
	</table>
	<table class="ocp_opcije_table">
		<tr>
		    <td class="ocp_opcije_td" style="WIDTH:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("User group");?></span></td>
			<td class="ocp_opcije_td"><select name="groupId" style="width: 100%;" class="ocp_forma" onChange="changeUsers(this.value);">
				<option value="">--<?php echo ocpLabels("choose user group");?>--</option><?php
			for ($i=0; $i<count($userGroups); $i++){
				?><option value="<?php echo $userGroups[$i]["Id"];?>"><?php echo $userGroups[$i]["Name"];?></option><?php
			}?></select></td>
    </tr>
	<tr>
		<td class="ocp_opcije_td" style="WIDTH:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("User");?></span></td>
		<td class="ocp_opcije_td"><select name="userName" style="width: 100%;" class="ocp_forma">
			<option value="">--<?php echo ocpLabels("choose user");?>--</option>
          </select></td>
    </tr>
	<tr>
		<td class="ocp_opcije_td" style="WIDTH:22%"><span 
		class="ocp_opcije_tekst1"><?php echo ocpLabels("Action");?></span></td>
		<td class="ocp_opcije_td"><select name="actionLog" style="width:100%;" class="ocp_forma">
			<option value="">--<?php echo ocpLabels("choose action");?>--</option>
			<option value="Update"><?php echo ocpLabels("Update");?></option>
			<option value="Insert"><?php echo ocpLabels("Insert");?></option>
			<option value="Delete"><?php echo ocpLabels("Delete");?></option>
         </select></td>
    </tr>
	<tr>
		<td class="ocp_opcije_td" style="WIDTH:22%"><span 
		  class="ocp_opcije_tekst1"><?php echo ocpLabels("Type");?></span></td>
		<td class="ocp_opcije_td"><select name="type" style="width: 100%;" class="ocp_forma">
				<option value="">--<?php echo ocpLabels("choose type");?>--</option>
				<option value="Root"><?php echo ocpLabels("Root");?></option>
				<option value="Version"><?php echo ocpLabels("Version");?></option>
				<option value="Section"><?php echo ocpLabels("Section");?></option>
				<option value="Page"><?php echo ocpLabels("Page");?></option>
				<option value="Block"><?php echo ocpLabels("Block");?></option><?php			
			for ($i=0; $i<count($types); $i++){
				?><option value="<?php echo $types[$i]["Id"];?>"><?php echo ocpLabels($types[$i]["Labela"]);?></option><?php
			}
			?></select></td>
    </tr>
	<tr>
		<td class="ocp_opcije_td" style="WIDTH: 22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Date");?></span></td>
		<td class="ocp_opcije_td"><table cellSpacing="0" cellPadding="0">
          <tr>
            <td vAlign=top noWrap><span class="ocp_opcije_tekst2"><?php echo ocpLabels("From");?></span><IMG 
            src="/ocp/img/blank.gif" width=3><INPUT class="ocp_forma" style="WIDTH:28px" name="dateFrom_dd"> /
              <INPUT class="ocp_forma" style="WIDTH: 28px" name="dateFrom_mm"> /
              <INPUT class="ocp_forma" style="WIDTH: 40px" name="dateFrom_yyyy">
              <A onclick="openDateFlash(event,'formObject.dateFrom'); return false;"          href="javascript:void(0);"><IMG src="/ocp/img/opsti/kontrole/kontrola_kalendar.gif" width="20" height="21" class="ocp_kontrola" title="<?php echo ocpLabels("Calendar");?>"></A></td>
            <td vAlign=top noWrap style="padding-left: 10px;"><span class="ocp_opcije_tekst2"><?php echo ocpLabels("To");?></span><IMG 
            src="/ocp/img/blank.gif" width=3><INPUT class="ocp_forma" style="WIDTH:28px" name="dateTo_dd"> /
              <INPUT class="ocp_forma" style="WIDTH: 28px" name="dateTo_mm"> /
              <INPUT class="ocp_forma" style="WIDTH: 40px" name="dateTo_yyyy">
              <A onclick="openDateFlash(event,'formObject.dateTo'); return false;"          href="javascript:void(0);"><IMG src="/ocp/img/opsti/kontrole/kontrola_kalendar.gif" width="20" height="21" class="ocp_kontrola" title="<?php echo ocpLabels("Calendar");?>"></A></td>
          </tr>
    </table></td>
    </tr>
  </tbody></table>
	<table width="100%">
		<tr>
			<td height="40" align="center" class="ocp_text"><input type="submit" name="submitSave" class="ocp_dugme" value="<?php echo ocpLabels("Search");?>">&nbsp;<input type="button" name="submitCancel" class="ocp_dugme" onClick="document.formObject.reset()" value="<?php echo ocpLabels("Cancel");?>"></td>
		</tr>
	</table>
</form>
<script src="/ocp/validate/user/validate_dates.js"></script>
<SCRIPT>
	window.onload= function (){
		parent.detailFrame.location.href = '/ocp/html/blank.html';
		parent.menuFrame.populateQuerySubmenu();
	}
	users = new Array();
	<?php
		for ($i=0; $i<count($userGroups); $i++){
			$users = users_getAllUsersInGroup($userGroups[$i]["Id"]);
			$buildStr = "";
			for ($j=0; $j<count($users); $j++)
				$buildStr .= $users[$j]["UserName"] . ",";
?>	users['<?php echo $userGroups[$i]["Id"];?>'] = "<?php echo substr($buildStr, 0, strlen($buildStr)-1);?>";<?php
		}

?>	function changeUsers(userGroup){
		var empty = false;
		if (userGroup != ""){
			var usersStr = users[userGroup];
			var userNames = usersStr.split(",");
			if (userNames[0] != ""){//ima usera
				document.formObject.userName.options.length = userNames.length+1;
				document.formObject.userName.options[0].value="";
				document.formObject.userName.options[0].text='--<?php echo ocpLabels("choose user");?>--';
				for (var k=1; k <= userNames.length; k++){
					document.formObject.userName.options[k].value = userNames[k-1];
					document.formObject.userName.options[k].text = userNames[k-1];
				}
			} else empty = true;
		} else empty = true;

		if (empty){
			document.formObject.userName.options.length = 1;
			document.formObject.userName.options[0].value="";
			document.formObject.userName.options[0].text='--<?php echo ocpLabels("choose user");?>--';
		}
	}

	function validate(){
		return validate_dates("formObject.dateFrom") && validate_dates("formObject.dateTo");
	}
</SCRIPT><?php	
	}

	function drawDeleteForm(){
		$types = tipobj_getAllTypes();
?><form name="formObject" id="formObject" method="post" action="activities_list.php?<?php echo utils_randomQS();?>" onSubmit="return validate();">
	<input type="hidden" name="Action" value="delete"> 
	<table class="ocp_blokovi_table">
	  <tr>
		<td class="ocp_blokovi_td" style="padding-right:0px;padding-left:6px;padding-bottom:4px;padding-top:4px"><?php echo ocpLabels("Delete records in activities log");?>:</td>
	  </tr>
	</table>
	<table class="ocp_opcije_table">
	<tr>
		<td class="ocp_opcije_td" style="WIDTH:22%"><span 
		  class="ocp_opcije_tekst1"><?php echo ocpLabels("Type");?></span></td>
		<td class="ocp_opcije_td"><select name="type" style="width: 100%;" class="ocp_forma">
				<option value="">--<?php echo ocpLabels("choose type");?>--</option>
				<option value="Root"><?php echo ocpLabels("Root");?></option>
				<option value="Verzija"><?php echo ocpLabels("Version");?></option>
				<option value="Sekcija"><?php echo ocpLabels("Section");?></option>
				<option value="Stranica"><?php echo ocpLabels("Page");?></option>
				<option value="Blok"><?php echo ocpLabels("Block");?></option><?php			
			for ($i=0; $i<count($types); $i++){
				?><option value="<?php echo $types[$i]["Ime"];?>"><?php echo ocpLabels($types[$i]["Labela"]);?></option><?php
			}
			?></select></td>
    </tr>
	<tr>
		<td class="ocp_opcije_td" style="WIDTH: 22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Date");?></span></td>
		<td class="ocp_opcije_td"><table cellSpacing="0" cellPadding="0">
          <tr>
            <td vAlign=top noWrap><span class="ocp_opcije_tekst2"><?php echo ocpLabels("From");?></span><IMG 
            src="/ocp/img/blank.gif" width=3><INPUT class="ocp_forma" style="WIDTH:28px" name="dateFrom_dd"> /
              <INPUT class="ocp_forma" style="WIDTH: 28px" name="dateFrom_mm"> /
              <INPUT class="ocp_forma" style="WIDTH: 40px" name="dateFrom_yyyy">
              <A onclick="openDateFlash(event,'formObject.dateFrom'); return false;" href="javascript:void(0);"><IMG src="/ocp/img/opsti/kontrole/kontrola_kalendar.gif" width="20" height="21" class="ocp_kontrola" title="<?php echo ocpLabels("Calendar");?>"></A></td>
            <td vAlign=top noWrap style="padding-left: 10px;"><span class="ocp_opcije_tekst2"><?php echo ocpLabels("To");?></span><IMG 
            src="/ocp/img/blank.gif" width=3><INPUT class="ocp_forma" style="WIDTH:28px" name="dateTo_dd"> /
              <INPUT class="ocp_forma" style="WIDTH: 28px" name="dateTo_mm"> /
              <INPUT class="ocp_forma" style="WIDTH: 40px" name="dateTo_yyyy">
              <A onclick="openDateFlash(event,'formObject.dateTo'); return false;" href="javascript:void(0);"><IMG src="/ocp/img/opsti/kontrole/kontrola_kalendar.gif" width="20" height="21" class="ocp_kontrola" title="<?php echo ocpLabels("Calendar");?>"></A></td>
          </tr>
    </table></td>
    </tr>
  </tbody></table>
	<table width="100%">
		<tr>
			<td height="40" align="center" class="ocp_text"><input type="submit" name="submitSave" class="ocp_dugme" value="<?php echo ocpLabels("Delete");?>">&nbsp;<input type="button" name="submitCancel" class="ocp_dugme" onClick="document.formObject.reset()" value="<?php echo ocpLabels("Cancel");?>"></td>
		</tr>
	</table>
</form>
<script src="/ocp/validate/user/validate_dates.js"></script>
<script>
	window.onload= function (){
		parent.detailFrame.location.href = '/ocp/html/blank.html';
		parent.menuFrame.populateDeleteSubmenu();
	}

	function validate(){
		return validate_dates("formObject.dateFrom") && validate_dates("formObject.dateTo");
	}
</script><?php
	}
?></div>
</body>
</html>