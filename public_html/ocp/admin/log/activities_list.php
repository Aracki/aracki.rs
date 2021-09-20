<?php
require_once("../../include/connect.php");
require_once("../../include/session.php");
require_once("../../include/tipoviobjekata.php");
require_once("../../include/polja.php");
require_once("../../include/objekti.php");
require_once("../../include/users.php");
require_once("../../include/xml.php");
require_once("../../include/xml_tools.php");
require_once("../../admin/design/list.php");
?>

<?php session_checkAdministrator(); ?>

<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="/ocp/css/opsti.css" rel="stylesheet" type="text/css">
<link href="/ocp/css/dugmici.css" rel="stylesheet" type="text/css">
<link href="/ocp/css/opcije.css" rel="stylesheet" type="text/css">
</HEAD>
<BODY  class="ocp_body"><?php
	$recordCount = 0; //globalna promenljiva u pozvanoj f-ji se puni

	
	$action = utils_requestStr(getPVar("Action"));
	if (!utils_valid($action)) $action = "view";

	if ($action == "view" || $action == "viewReconstruct"){
		drawReport($action);
	} else if ($action == "delete"){
		$type = utils_requestStr(getPVar("type"));
		$dateFrom = datetime_getFormDate("dateFrom");
		$dateTo = datetime_getFormDate("dateTo");

		$numbs = log_delete($type, $dateFrom, $dateTo);
		?><div id="ocp_blok_menu_1">
    <table class="ocp_blokovi_table">
        <tr>
          <td class="ocp_blokovi_td" style="PADDING-RIGHT:0px;PADDING-LEFT:6px;PADDING-BOTTOM:4px;PADDING-TOP: 4px"><?php echo ocpLabels("Delete records in activities log");?>:</td>
        </tr>
    </table>
  </div><?php require_once("../../include/design/message.php"); ?>
			<?php echo message_info($numbs." ".ocpLabels("records has been erased").".");?><?php
	}

	function drawReport($action){
		global $recordCount;

		$groupId = utils_requestInt(getPVar("groupId"));
		$userName = utils_requestStr(getPVar("userName"));
		$actionLog = utils_requestStr(getPVar("actionLog"));
		$type = utils_requestStr(getPVar("type"));
		$dateFrom = ($action == "view") ? datetime_getFormDate("dateFrom") : utils_requestStr(getPVar("dateFrom"));
		$dateTo = ($action == "view") ? datetime_getFormDate("dateTo") : utils_requestStr(getPVar("dateTo"));
		$sortName = utils_requestStr(getPVar("sortName"));
		$direction = utils_requestStr(getPVar("direction"));
		$brojac = utils_requestInt(getPVar("ocp_brojac"));
		$broj = utils_requestInt(getPVar("ocp_broj"));

		if (!utils_valid($broj) || ($broj == 0)) $broj = 50;
		if (!utils_valid($brojac)) $brojac = 0;

		
		if (!utils_valid($sortName)){
			$sortName = "UserGroup";
			$direction = "asc";
		} else if (!utils_valid($direction))
			$direction = "asc";

		$filterText = ": ";
		if (utils_valid($groupId) && ($groupId != 0)){
			$userGroup = users_getUserGroup($groupId);
			$filterText .= ocpLabels("User group").": \"".$userGroup["Name"]."\", ";
		}
		if (utils_valid($userName)) $filterText .= ocpLabels("User").": \"".userName."\", ";
		if (utils_valid($actionLog)) $filterText .= ocpLabels("Action").": \"".ocpLabels($actionLog)."\", ";
		if (utils_valid($type)){
			$typeTrans = "";
			if (!is_numeric($type)){
				$typeTrans = ocpLabels($type);
				switch ($type){
					case "Version": $type="Verzija"; break;
					case "Section": $type="Sekcija"; break;
					case "Page": $type="Stranica"; break;
					case "Block": $type="Blok"; break;
				}
			} else {
				$typeObj = tipobj_get($type);
				if (utils_valid($typeObj["Labela"])) $typeTrans = ocpLabels($typeObj["Labela"]);
				else $typeTrans = $typeObj["Ime"];
				$type = $typeObj["Ime"];
			}
			$filterText .= ocpLabels("Type").": \"".$typeTrans."\", ";
		}
		if (utils_valid($dateFrom)) $filterText .= ocpLabels("From").": \"".dateFrom."\", ";
		if (utils_valid($dateTo)) $filterText .= ocpLabels("To").": \"".dateTo."\", ";

		$niz = log_getAll($groupId, $userName, $actionLog, $type, $dateFrom, $dateTo, $sortName, $direction, $broj, $brojac);

		$strNav = ($recordCount > 0) ? 
			"(".($broj*$brojac + 1) . "-" . min(($brojac+1)*$broj, $recordCount)."/".$recordCount.")" : "(0-0/0)";

		
?><div id="ocp_blok_menu_1">
    <table class="ocp_blokovi_table">
        <tr>
          <td class="ocp_blokovi_td" style="PADDING-RIGHT:0px;PADDING-LEFT:6px;PADDING-BOTTOM:4px;PADDING-TOP: 4px"><?php echo ocpLabels("Activities log report");?>: <?php echo $strNav?></td><?php
			  if ($filterText != ": "){?>
			  <td class="ocp_blokovi_td" style="text-align: right;">
					<span style="color: #C42E00;"><?php echo ocpLabels("Filter");?></span>
					<?php echo substr($filterText, 0, strlen($filterText) - 2);?>
				</td>
			  <?php	}
			?>
        </tr>
    </table>
  </div><?php
		list_tableHeader(array("User group", "User", "Action", "Type", "Id", "Description", "Date"), $sortName, $direction, array("UserGroup", "UserName", "Akcija", "TipObjekta", "IdObjekta", "IdenObjekta", "Datum"), 1);

		for ($i=0; $i<count($niz); $i++){
	?><tr>
        <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $i+1;?>.</span></td>
        <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $niz[$i]["UserGroup"];?></span></td>
        <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $niz[$i]["UserName"];?></span></td>
        <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo ocpLabels($niz[$i]["Akcija"]);?></span></td>
        <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $niz[$i]["TipObjekta"];?></span></td>
        <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $niz[$i]["IdObjekta"];?></td>
        <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $niz[$i]["IdenObjekta"];?></span></td>
        <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo datetime_format4Database($niz[$i]["Datum"]);?></span></td>
     </tr><?php
		}
		list_tableFooter();
  ?><FORM ACTION="activities_list.php?<?php echo utils_randomQS();?>" METHOD="POST" NAME="reconstructForm" ID="reconstructForm">
	<INPUT TYPE="HIDDEN" NAME="Action" VALUE="viewReconstruct">
	<INPUT TYPE="HIDDEN" NAME="groupId" VALUE="<?php echo $groupId;?>">
	<INPUT TYPE="HIDDEN" NAME="userName" VALUE="<?php echo $userName;?>">
	<INPUT TYPE="HIDDEN" NAME="actionLog" VALUE="<?php echo $actionLog;?>">
	<INPUT TYPE="HIDDEN" NAME="type" VALUE="<?php echo $type;?>">
	<INPUT TYPE="HIDDEN" NAME="dateFrom" VALUE="<?php echo $dateFrom;?>">
	<INPUT TYPE="HIDDEN" NAME="dateTo" VALUE="<?php echo $dateTo;?>">
	<INPUT TYPE="HIDDEN" NAME="sortName" VALUE="<?php echo $sortName;?>">
	<INPUT TYPE="HIDDEN" NAME="direction" VALUE="<?php echo $direction;?>">
	<INPUT TYPE="HIDDEN" NAME="ocp_brojac" VALUE="<?php echo $brojac?>">
	<INPUT TYPE="HIDDEN" NAME="ocp_broj" VALUE="<?php echo $broj?>">
</FORM>
<SCRIPT>
	function sort(sortName, direction){
		document.reconstructForm.sortName.value = sortName;
		document.reconstructForm.direction.value = direction;
		document.reconstructForm.submit();
	}

	window.onload= function (){
		parent.menuFrame.eraseQuerySubmenu();
		parent.menuFrame.populateNavigationSubmenu(<?php echo $broj?>, <?php echo $brojac?>, <?php echo $recordCount?>);
		parent.detailFrame.location.href = '/ocp/html/blank.html';
	}
	function newOffset(offset){
		document.reconstructForm.ocp_brojac.value = offset;
		document.reconstructForm.submit();

	}
</script><?php
	}
?>
</BODY>
</HTML>