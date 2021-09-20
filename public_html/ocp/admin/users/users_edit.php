<?php
require_once("../../include/connect.php");
require_once("../../include/session.php");
require_once("../../include/users.php");
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
		$userId = utils_requestInt(getPVar("userId"));
		$groupId = utils_requestInt(getPVar("User_UGrp_Id"));
		$username = utils_requestStr(getPVar("User_Name"));
		$password = utils_requestStr(getPVar("User_Password"));
//utils_dump($groupId);
		if ($userId == "-1"){//insert korisnika
//utils_dump("Insert");
			users_insertUser($groupId, $username, $password);
		} else {//update korisnika
			$oldUser = users_getUserById($userId);
//utils_dump("Update");
			$updateAllowed = users_updateUser($groupId, $userId, $username, $password);
			//update-ovati user-ove podatke i u logu, ako mu je promenjena kor. grupa ili username
			if ($updateAllowed && (($oldUser["User_Name"] != $username) || ($oldUser["User_UGrp_Id"] != $groupId))){
				con_update("update Logs set UserName='".$username."', UserGroupId=".$groupId." where UserGroupId=".$oldUser["User_UGrp_Id"]." and UserName='".$oldUser["User_Name"]."'");
			}
		}

		?><script>parent.subMenuFrame.reconstruct();
		parent.document.getElementById("resizableFrameset").setAttribute("rows", "100%,*");
		</script><?php
	} else { //pre submita
		$action = utils_requestStr(getGVar("action"));	
		$userId = utils_requestInt(getGVar("userId"));	//korisnik

		switch ($action){
			case "delete" : 
				users_deleteUser($userId);
				?><script>parent.subMenuFrame.reconstruct();</script><?php
				break;
			case "iu" :
				drawEditForm($userId);
				break;
		}
	}

	function drawEditForm($userId){
		$user = users_getUserById($userId);
?><div id="ocp_main_table">
<form name="formObject" id="formObject" method="post" onSubmit="return validate();" action="users_edit.php?<?php echo utils_randomQS();?>">
	<input type="hidden" name="userId" value="<?php echo $userId;?>">
	<input type="hidden" name="Action" value="Sacuvaj">
	<table class="ocp_naslov_table">
		<tr>
			<td class="ocp_naslov_td"><?php 
		if ($userId == "-1"){
			?><?php echo ocpLabels("New user");?><?php
		} else {
			?><?php echo ocpLabels("Edit user");?>:&nbsp;<?php echo $user["User_Name"];?><?php
		} ?></td>
		</tr>
	</table>
	<table class="ocp_opcije_table" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top"><table class="ocp_opcije_table">
			<tr>
			  <td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Username");?>:</span></td><?php
		  $username = ($userId == "-1") ? "" : $user["User_Name"];
			  ?><td class="ocp_opcije_td"><input name="User_Name" type="text" class="ocp_forma" style="width: 100%;" value="<?php echo $username;?>"></td>
			</tr>
			<tr>
			  <td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Password");?>: </span></td>
			  <td class="ocp_opcije_td"><input name="User_Password" type="password" class="ocp_forma" style="width: 100%;" value=""></td>
			</tr>
			<tr>
			  <td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("User group");?>: </span></td>
			  <td class="ocp_opcije_td"><select name="User_UGrp_Id" style="width: 100%;" class="ocp_forma">
				<option value="">--<?php echo ocpLabels("choose group");?>--</option><?php
			$user["User_UGrp_Id"] = isset($user["User_UGrp_Id"]) ? $user["User_UGrp_Id"]  : 0;

			$nizGrupa = users_getAllUserGroups("Name", "asc");
			for ($i=0; $i<count($nizGrupa); $i++){
			  ?><option value="<?php echo $nizGrupa[$i]["Id"];?>" <?php if ($nizGrupa[$i]["Id"] == $user["User_UGrp_Id"]) echo("selected");?>><?php echo $nizGrupa[$i]["Name"];?></option><?php
			}	
				?></select></td>
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
<script language="javascript">
	window.onload = function(){
		parent.document.getElementById("resizableFrameset").setAttribute("rows", "<?php echo $userHeight;?>,*");
	}

	function validate(){
		if (document.formObject.User_Name.value == "") {
			alert("<?php echo ocpLabels("Username");?>"+ " " + "<?php echo ocpLabels("must have value");?>.");
			return false;
		}
		if (document.formObject.User_Password.value == "") {
			alert("<?php echo ocpLabels("Password");?>"+ " " + "<?php echo ocpLabels("must have value");?>.");
			return false;
		}
		if (document.formObject.User_UGrp_Id.value == ""){
			alert("<?php echo ocpLabels("User group");?>"+ " " + "<?php echo ocpLabels("must have value");?>.");
			return false;
		}
		
		//nedozvoljeni karakteri
		var re = new RegExp( "[!@#$%^&*()+={}|\;':,./<>? \"]", "gi");
		if (re.test(document.formObject.User_Name.value)){
			alert("<?php echo ocpLabels("Username");?>"+ " " + ": <?php echo ocpLabels('has forrbiden character');?>.");
			return false;
		}
		if (re.test(document.formObject.User_Password.value)){
			alert("<?php echo ocpLabels("Password");?>"+ " " + ": <?php echo ocpLabels('has forrbiden character');?>.");
			return false;
		}

		return true;
	}
</script><?php
	}
?></body>
</html>