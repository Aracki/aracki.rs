<?php
require_once("../../include/connect.php");
require_once("../../include/session.php");
require_once("../../include/users.php");
require_once("../../siteManager/lib/root.php");
require_once("../../siteManager/lib/verzija.php");
require_once("../../siteManager/lib/sekcija.php");
require_once("../../siteManager/lib/stranica.php");
?>

<?php session_checkAdministrator(); ?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/ocp/css/opsti.css">
<link rel="stylesheet" href="/ocp/css/opcije.css">
<script language="javascript" type="text/javascript" src="/ocp/jscript/swfobject.js"></script>
</head>
<body class="ocp_body"><?php
	$action = utils_requestStr(getPVar("Action"));

	if (utils_valid($action)){//submitovana su prava
		$groupId = utils_requestInt(getPVar("UGrp_Id"));

		if ($groupId == "-1"){//insert grupe
			$groupName = utils_requestStr(getPVar("UGrp_Name"));
			$exist = users_userGroupExists($groupName);
			if ($exist){
	?><SCRIPT>alert("<?php echo ocpLabels("User group with given name already exist");?>.");</SCRIPT><?php			
			} else {
				$typeCount =utils_requestInt(getPVar("Type_Count"));
				$typeRights = array();
				for ($f=0; $f < intval($typeCount); $f++){
					$typeId = utils_requestInt(getPVar("Type_".$f));
					$typeRights[$typeId] = utils_requestStr(getPVar("Type_Rights".$typeId));
				}
				$groupId = users_createUserGroup($groupName, $typeRights);
			}
		} else {//update
			$group = users_getUserGroup($groupId);
			$oldGroupName = $group["Name"];
			$groupName = utils_requestStr(getPVar("UGrp_Name"));
			$update = false;
			if ($oldGroupName != $groupName){
				$exist = users_userGroupExists($groupName);
				if ($exist){
?><SCRIPT>alert("<?php echo ocpLabels("User group with given name already exist.");?>");</SCRIPT><?php			
				} else $update = 1;
			} else $update = 1;
			
			if ($update){
				$typeCount = utils_requestInt(getPVar("Type_Count"));
				$typeRights = array();
				for ($f=0; $f < intval($typeCount); $f++){
					$typeId = utils_requestInt(getPVar("Type_".$f));
					$typeRights[$typeId] = utils_requestStr(getPVar("Type_Rights".$typeId));
				}
				users_updateUserGroup($groupId, $oldGroupName, $groupName, $typeRights);
			}
		}

		$hierarchy = utils_requestInt(getPVar("Hierarchy"));
		$napomene = "";
		
		users_updateSMPrava("Root", 1, $groupId, utils_requestInt(getPVar("Hierarchy")));
		$test = preg_replace("(#@#|\n)", "", trim($napomene));
//utils_log("'".$test."'", "log.txt");
//utils_log("'".$napomene."'", "log.txt");
		if ($test != ""){?>
		<script>
			var napomene = "<?php echo $napomene;?>";
			alert(napomene.replace(/#@#/g, "\n"));
		</script><?php		
		}
		?><script>
			parent.subMenuFrame.reconstruct();
			parent.document.getElementById("resizableFrameset").setAttribute("rows", "100%,*");
		</script><?php
	} else { //pre submita
		$action = utils_requestStr(getGVar("action"));	
		$groupId = utils_requestInt(getGVar("groupId"));	//grupa

		switch ($action){
			case "delete" : 
				users_deleteUserGroup($groupId);
				?><script>parent.subMenuFrame.reconstruct();</script><?php
				break;
			case "iu" :
				drawEditForm($groupId);
				break;
		}
	}

	function drawEditForm($groupId){
		$group = users_getUserGroup($groupId);
		$group["Super"] = (!isset($group["Super"]) ||  $group["Super"] != 1) ? 0 : 1;

		$Verz_Prava = verzija_getRights($groupId);

		$Verz_gPravo = "0";
		if (isset($Verz_Prava[""]) && !is_null($Verz_Prava[""]))	$Verz_gPravo = $Verz_Prava[""];
		if ($group["Super"] == "1") $Verz_gPravo = "4";

		$Sekc_Prava = sekcija_getRights($groupId);
		$Sekc_gPravo = "0";
		if (isset($Sekc_Prava[""]) && !is_null($Sekc_Prava[""]))	$Sekc_gPravo = $Sekc_Prava[""];
		if ($group["Super"] == "1") $Sekc_gPravo = "4";

		$Stra_Prava = stranica_getRights($groupId);
		$Stra_gPravo = "0";
		if (isset($Stra_Prava[""]) && !is_null($Stra_Prava[""]))	$Stra_gPravo = $Stra_Prava[""];
		if ($group["Super"] == "1") $Stra_gPravo = "4";

?><div id="ocp_main_table">
<form name="formObject" id="formObject" method="post" onSubmit="return hierarchy();" action="groups_edit.php?<?php echo utils_randomQS();?>">
	<input type="hidden" name="Hierarchy" value="0">
	<input type="hidden" name="UGrp_Id" value="<?php echo $groupId;?>">
	<input type="hidden" name="Action" value="Izmeni">
	<table class="ocp_naslov_table">
		<tr>
			<td class="ocp_naslov_td"><?php
		if ($groupId != "-1"){
		?><?php echo ocpLabels("Edit user group");?>:&nbsp;<?php echo $group["Name"];?><?php
		}else {
		?><?php echo ocpLabels("New user group");?><?php
		}
			?></td>
		</tr>
	</table>
	<table class="ocp_opcije_table" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top"><table class="ocp_opcije_table">
			<tr>
				<td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Name");?>: </span></td>
				<td class="ocp_opcije_td"><?php
		if ($group["Super"] == "1"){
				?><input name="UGrp_Name" type="hidden" value="<?php echo $group["Name"];?>"><span class="ocp_opcije_tekst1"><?php echo $group["Name"];?></span><?php
		} else {
				?><input name="UGrp_Name" type="text" class="ocp_forma" style="width: 100%;" value="<?php echo (isset($group["Name"]) ? $group["Name"] : "");?>"><?php
		}				?></td>
			</tr>
			<tr>
				<td colspan="2" class="ocp_opcije_td_header ocp_opcije_tekst2"><?php echo ocpLabels("Site Manager");?>:</td>
			</tr>
			<tr>
				<td colspan="2" style="padding-left: 5px;"><a href="javascript:styleOn('opsta', 'tab_opsta');" class="polja sel" id="tab_opsta"><?php echo ocpLabels("General rights");?></a> <a href="javascript:styleOn('posebna', 'tab_posebna');" class="polja" id="tab_posebna"><?php echo ocpLabels("Particular rights");?></a></td>
			</tr>
			<tr>
				<td colspan="2" class="ocp_opcije_td"><table class="ocp_opcije_table" id="opsta" style="width:100%; display: block;"><?php
		this_dumpHeader("Site Manager");		
		this_dumpGeneral("Verz_General", $Verz_gPravo);
		this_dumpGeneral("Sekc_General", $Sekc_gPravo);
		this_dumpGeneral("Stra_General", $Stra_gPravo);?></table><?php	

		this_dumpRights($group["Super"], $Verz_Prava, $Sekc_Prava, $Stra_Prava, null, "R");	
		?><table class="ocp_opcije_table" id="posebna" style="width:100%; display: none;">
			<tr id="trHeader" style="position:relative; top:0px">
				<td colspan="6" valign="top"  class="ocp_opcije_td" style="padding:6px;"><?php
		$swfArgs = "treeSource=/ocp/admin/users/tree_data.php&treeFilter=".$groupId;
		$swfArgs .= "&menuDisallowed=1&dragDisallowed=1";
		$swfArgs .= "&versionClickDisallowed=1&sectionClickDisallowed=0&pageClickDisallowed=1&rightsAllowed=1";
	?><div id="groups_edit"></div>
	<script type="text/javascript">
	   var so = new SWFObject("/ocp/flash/tree.swf?<?php echo $swfArgs?>", "security_tree", "579", "205", "6", "#ffffff");
	   so.write("groups_edit");
	</script></td>
          </tr>
      </table></td>
	</tr>
	<tr>
		<td colspan="2" class="ocp_opcije_td_header ocp_opcije_tekst2"><?php echo ocpLabels("Object Manager");?>:</td>
	</tr>
	<tr>
		<td colspan="2" class="ocp_opcije_td"><table class="ocp_opcije_table" style="width:100%; display: block;"><?php
		this_dumpHeader("Object Manager");
		this_dumpTypeRights($group["Super"], users_getGroupTypeRights($groupId));
		?></table>
		</td>
	</tr>
	</table><?php
	if ($group["Super"] != "1"){
	?><table width="100%">
		<tr>
			<td height="40" align="center" class="ocp_text"><input type="submit" name="submitSave" class="ocp_dugme" value="<?php echo ocpLabels("Save");?>">&nbsp;<input type="button" name="submitCancel" class="ocp_dugme" onClick="parent.menuFrame.showSubmenuClose(true, true);" value="<?php echo ocpLabels("Cancel");?>"></td>
		</tr>
	</table><?php
	}	
?></form>
</div><?php
	$userHeight = utils_valid(getSVar("ocpUserHeight")) ? getSVar("ocpUserHeight") : "25%";
?>
<script src="/ocp/validate/validate_double_quotes.js"></script>
<script language="javascript">
	window.onload = function(){
		parent.document.getElementById("resizableFrameset").setAttribute("rows", "<?php echo $userHeight;?>,*");
	}

	function styleOn(objId,tabId) {

		document.getElementById('opsta').style['display'] = 'none';
		document.getElementById('posebna').style['display'] = 'none';

		document.getElementById('tab_opsta').className = 'polja';
		document.getElementById('tab_posebna').className = 'polja';

		document.getElementById(objId).style['display'] = 'block';
		document.getElementById(tabId).className += ' sel';
	}

	//flash poziva ovu f=ju
	function setRight (right, id, type){
		if (right == "null") right = "undefined";
		switch (type){
			case "verzija":
				eval("document.formObject.Verz_Rights"+id+".value="+right);
				break;
			case "sekcija":
				eval("document.formObject.Sekc_Rights"+id+".value="+right);
				break;
			case "stranica":
				eval("document.formObject.Stra_Rights"+id+".value="+right);
				break;
		}
	}

	function validate(){
		if (document.formObject.UGrp_Name.value == ""){ //da li ime UserGroup-e prazno
			alert("<?php echo ocpLabels("Name");?>"+" "+"<?php echo ocpLabels("must have value");?>.");
			return false;
		} else validate_double_quotes_field(document.formObject.UGrp_Name);

		return true;
	}

	function hierarchy(){
		if (validate()){
			var forma = document.formObject;
		//Rights
			for (var i=0; i<forma.elements.length; i++){
				var nextElement = forma.elements[i];

				if (nextElement.name.indexOf("Rights") == 5){
					var prefix = nextElement.name.substring(0, 4);
					if (prefix == "Sekc"){
						var right = nextElement.value;
						var oldRight = eval("forma.Old"+nextElement.name+".value");
						if (right != oldRight){
							x = confirm("<?php echo ocpLabels("Do you want children nodes to inherit rights");?>?");
							if (x) forma.Hierarchy.value = "1";
							break;
						
						}
					}
				}
			}
		} else {
			return false;
		}
		return true;
	}
</script><?php
	}

	function this_dumpHeader($headerType){
		$headerTitle = ($headerType=="Site Manager") ? "General rights" : "Ocp type rights";
		?><tr>
		  <td width="22%" valign="top"  class="ocp_opcije_td" style="padding:6px;"><span class="ocp_opcije_tekst1"><strong><?php echo ocpLabels($headerTitle);?></strong></span></td>
		  <td align="center" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><img src="images/nema.gif" width="15" height="16"> <?php echo ocpLabels("No rights");?></span></td>
		  <td align="center" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><img src="images/select.gif" width="12" height="14"> <?php echo ocpLabels("Select");?></span></td>
		  <td align="center" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><img src="images/update.gif" width="13" height="13"> <?php echo ocpLabels("Update");?></span></td>
		  <td align="center" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><img src="images/insert.gif" width="13" height="16"> <?php echo ocpLabels("Insert");?></span></td>
		  <td align="center" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><img src="images/delete.gif" width="12" height="13"><?php echo ocpLabels("Delete");?></span></td>
		</tr><?php
	}
	
	//	dumpGeneral ispisuje opsta prava nad objektima Site Managera
	function this_dumpGeneral($naziv, $gPravo){
		$genTitle = ""; 
		$genImg = "";
		switch ($naziv){
			case "Verz_General": $genTitle = ocpLabels("Version"); $genImg= "verzija"; break;
			case "Sekc_General": $genTitle = ocpLabels("Section"); $genImg= "sekcija"; break;
			case "Stra_General": $genTitle = ocpLabels("Page"); $genImg= "stranica"; break;
		}
?><tr>
	<td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"> <img src="images/<?php echo $genImg;?>_ikonica.gif" width="17" height="18"> <?php echo $genTitle;?></span></td>
	<td align="center" valign="top" class="ocp_opcije_td"><input name="<?php echo $naziv;?>" type="radio" value="0" <?php if($gPravo=="0") echo("checked")?>></td>
	<td align="center" valign="top" class="ocp_opcije_td"><input name="<?php echo $naziv;?>" type="radio" value="1" <?php if($gPravo=="1") echo("checked")?>></td>
	<td align="center" valign="top" class="ocp_opcije_td"><input name="<?php echo $naziv;?>" type="radio" value="2" <?php if($gPravo=="2") echo("checked")?>></td>
	<td align="center" valign="top" class="ocp_opcije_td"><?php if ($naziv != "Stra_General"){?><input name="<?php echo $naziv;?>" type="radio" value="3" <?php if($gPravo=="3") echo("checked")?>><?php } ?></td>
	<td align="center" valign="top" class="ocp_opcije_td"><input name="<?php echo $naziv;?>" type="radio" value="4" <?php if($gPravo=="4") echo("checked")?>></td>
</tr><input type="hidden" name="Old<?php echo $naziv;?>" value="<?php echo $gPravo;?>">
<?php	
	}

	function this_dumpTypeRights($superG, $type_rights){
		for ($i=0; $i<count($type_rights); $i++){
			$type = $type_rights[$i];
			$type_right = $type["Rights"];

			if ($superG == "1") $type_right = "4";
			if (!utils_valid($type_right)) $type_right = "0";
			$labela = (isset($type["Labela"]) && utils_valid($type["Labela"])) ? ocpLabels($type["Labela"]) : $type["Ime"];
?><tr>
     <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $labela;?></span></td>
     <td align="center" valign="top" class="ocp_opcije_td"><input name="Type_Rights<?php echo $type["Id"];?>" type="radio" value="0" <?php if ($type_right == "0") echo("checked");?>></td>
     <td align="center" valign="top" class="ocp_opcije_td"><input name="Type_Rights<?php echo $type["Id"];?>" type="radio" value="1" <?php if ($type_right == "1") echo("checked");?>></td>
     <td align="center" valign="top" class="ocp_opcije_td"><input name="Type_Rights<?php echo $type["Id"];?>" type="radio" value="2" <?php if ($type_right == "2") echo("checked");?>></td>
     <td align="center" valign="top" class="ocp_opcije_td"><input name="Type_Rights<?php echo $type["Id"];?>" type="radio" value="3" <?php if ($type_right == "3") echo("checked");?>></td>
     <td align="center" valign="top" class="ocp_opcije_td"><input name="Type_Rights<?php echo $type["Id"];?>" type="radio" value="4" <?php if ($type_right == "4") echo("checked");?>></td>
   </tr><input name="Type_<?php echo $i;?>" type="hidden" value="<?php echo $type["Id"];?>"><?php
		}
	?><input name="Type_Count" type="hidden" value="<?php echo count($type_rights);?>"><?php
	}

	//	this_dumpRights rekurzija za ispis prava Site Managera
	function this_dumpRights($superG, $Verz_Prava, $Sekc_Prava, $Stra_Prava, $Id, $T){
		switch($T){
			case "R":	$records = root_getAll();
						for ($i=0; $i<count($records); $i++){
							$record = $records[$i]; 
							this_dumpRights($superG, $Verz_Prava, $Sekc_Prava, $Stra_Prava, $record["Root_Id"], "V");
						}
						break;
			case "V":	$records = root_getAllVerzija($Id);
						for ($i=0; $i<count($records); $i++){
							$record = $records[$i];
							$t = ($superG == "1") ? "4" : (isset($Verz_Prava[$record["Verz_Id"]]) ? $Verz_Prava[$record["Verz_Id"]] : "");
?>	<input type="hidden" name="Verz_Rights<?php echo $record["Verz_Id"];?>" value="<?php echo $t;?>">
	<input type="hidden" name="OldVerz_Rights<?php echo $record["Verz_Id"];?>" value="<?php echo $t;?>">
<?php
							this_dumpRights($superG, $Verz_Prava, $Sekc_Prava, $Stra_Prava, $record["Verz_Id"], "S");
						}
						break;
			case "S":	$records = verzija_getAllSekcija($Id);
						for ($i=0; $i<count($records); $i++){
							$record = $records[$i];
							$t = ($superG == "1" ) ? "4" : (isset($Sekc_Prava[$record["Sekc_Id"]]) ? $Sekc_Prava[$record["Sekc_Id"]] : "");
?>	<input type="hidden" name="Sekc_Rights<?php echo $record["Sekc_Id"];?>" value="<?php echo $t;?>">
	<input type="hidden" name="OldSekc_Rights<?php echo $record["Sekc_Id"];?>" value="<?php echo $t;?>">
<?php							this_dumpRights($superG, $Verz_Prava, $Sekc_Prava, $Stra_Prava, $record["Sekc_Id"], "Str");
							this_dumpRights($superG, $Verz_Prava, $Sekc_Prava, $Stra_Prava, $record["Sekc_Id"], "P");
						}
						break;

			case "P":	$records = sekcija_getAllPodsekcija($Id);
						for ($i=0; $i<count($records); $i++){
							$record = $records[$i];
							$t = ($superG == "1") ? "4" : (isset($Sekc_Prava[$record["Sekc_Id"]]) ? $Sekc_Prava[$record["Sekc_Id"]] : "");
?>	<input type="hidden" name="Sekc_Rights<?php echo $record["Sekc_Id"];?>" value="<?php echo $t;?>">
	<input type="hidden" name="OldSekc_Rights<?php echo $record["Sekc_Id"];?>" value="<?php echo $t;?>"> <?php
							this_dumpRights($superG, $Verz_Prava, $Sekc_Prava, $Stra_Prava, $record["Sekc_Id"], "Str");
							this_dumpRights($superG, $Verz_Prava, $Sekc_Prava, $Stra_Prava, $record["Sekc_Id"], "P");
						}
						break;
			
			case "Str": $records = sekcija_getAllStranica($Id);
						for ($i=0; $i<count($records); $i++){
							$record = $records[$i];
							$t = ($superG == "1") ? "4" : (isset($Stra_Prava[$record["Stra_Id"]]) ? $Stra_Prava[$record["Stra_Id"]] : "");
?>	<input type="hidden" name="Stra_Rights<?php echo $record["Stra_Id"];?>" value="<?php echo $t;?>">
	<input type="hidden" name="OldStra_Rights<?php echo $record["Stra_Id"];?>" value="<?php echo $t;?>"><?php
						}
						break;

			default: break;
		}
	}
?></body>
</html>