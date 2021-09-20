<?php
require_once("../../include/connect.php");
require_once("../../include/session.php");
require_once("../../admin/design/list.php");
require_once("../../include/xml.php");
require_once("../../include/xml_tools.php");
require_once("../../include/selectradio.php");
require_once("../../include/tipoviobjekata.php");
require_once("../../include/objekti.php");
require_once("../../include/polja.php");
?>

<?php session_checkAdministrator(); ?>

<?php	
	$typeId = utils_requestInt(getPVar("typeId"));
	if (!utils_valid($typeId) || ($typeId == 0)) { ?> <script>location="/ocp/admin/recycleBin/objects_query.php?<?php echo utils_randomQS();?>"; </script> <?php }
?><HTML>
<HEAD>
<TITLE> Ocp </TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="/ocp/css/opsti.css" rel="stylesheet" type="text/css">
<link href="/ocp/css/dugmici.css" rel="stylesheet" type="text/css">
<link href="/ocp/css/opcije.css" rel="stylesheet" type="text/css">
</HEAD>
<body class="ocp_body" onload="parent.detailFrame.location.href = '/ocp/html/blank.html';parent.menuFrame.eraseQuerySubmenu();">
<?php
	$recordCount = 0; //globalna promenljiva u pozvanoj f-ji se puni


	$sortName = utils_requestStr(getPVar("sortName"));
	$direction = utils_requestStr(getPVar("direction"));
	$brojac = utils_requestInt(getPVar("ocp_brojac"));
	$broj = utils_requestInt(getPVar("ocp_broj"));
	$restoreId = utils_requestStr(getPVar("restoreId"));
	$deleteId = utils_requestStr(getPVar("deleteId"));
	$restoreAll = utils_requestStr(getPVar("restoreAll"));
	$deleteAll = utils_requestStr(getPVar("deleteAll"));

	$typeObj = tipobj_get($typeId);

	//ako je posle submita
	if (utils_valid($restoreId) && ($restoreId != 0)){
		con_update("update ".$typeObj["Ime"]." set Valid=1 where Id=".$restoreId);
	} else if (utils_valid($deleteId) && ($deleteId != 0)){
		con_update("delete from ".$typeObj["Ime"]." where Id=".$deleteId);
	} else if (utils_valid($restoreAll)){
		con_update("update ".$typeObj["Ime"]." set Valid=1 where Valid=0");
	} else if (utils_valid($deleteAll)){
		con_update("delete from ".$typeObj["Ime"]." where Valid=0");
	}  
	$transType = (!utils_valid($typeObj["Labela"])) ? $typeObj["Ime"] : ocpLabels($typeObj["Labela"]);
	$filterText = ": ".ocpLabels("Type").": \"".$transType."\"";

	$niz = obj_getAllNonValid($typeObj["Ime"], $sortName, $direction, $broj, $brojac);
	
	$strNav = ($recordCount > 0) ? 
			"(".($broj*$brojac + 1) . "-" . min(($brojac+1)*$broj, $recordCount)."/".$recordCount.")" : "(0-0/0)";
	
	list_header($strNav, $filterText, NULL);
	?><table>
	<tr>
		<td><table cellpadding="0" cellspacing="0" >
        <tr>
          <td style="cursor:pointer;" onClick="goRestoreAll();"><img src="/ocp/img/opsti/kontrole/dugme_restore.gif" width="21" height="22" title="<?php echo ocpLabels("Restore all");?>"></td>
          <td height="22" class="ocp_opcije_dugme" style="white-space: nowrap; cursor:pointer;" onClick="goRestoreAll();"><?php echo ocpLabels("Restore all");?></td>
          <td style="cursor:pointer;" onClick="goRestoreAll();"><img src="/ocp/img/opsti/kontrole/dugme_desni.gif" width="6" height="22" title="<?php echo ocpLabels("Restore all");?>"></td>
        </tr>
      </table>
	 </td>
	 <td>
		<table cellpadding="0" cellspacing="0">
          <tr>
            <td style="cursor:pointer;" onClick="goDeleteAll();"><img src="/ocp/img/opsti/kontrole/dugme_obrisi.gif" width="21" height="22" title="<?php echo ocpLabels("Delete all");?>"></td>
            <td height="22" class="ocp_opcije_dugme" style="white-space: nowrap; cursor:pointer;" onClick="goDeleteAll();"><?php echo ocpLabels("Delete all");?></td>
            <td style="cursor:pointer;" onClick="goDeleteAll();"><img src="/ocp/img/opsti/kontrole/dugme_desni.gif" width="6" height="22" title="<?php echo ocpLabels("Delete all");?>"></td>
          </tr>
        </table>
	</td>
	</tr>
</table><?php
	list_tableHeader(array("Id", "Identification"), $sortName, $direction, array("Id", ""));
	for ($i=0; $i<count($niz); $i++){
		$niz[$i]["Identification"] = xml_generateRecordIdenString($typeObj["Ime"], $niz[$i], false);
		list_tableRowDeletedObjects($i, array("Id", "Identification"), $niz[$i]);
	}
	list_tableFooter();

?><FORM ACTION="objects_list.php?<?php echo utils_randomQS();?>" METHOD="POST" NAME="reconstructForm" ID="reconstructForm">
	<INPUT TYPE="HIDDEN" NAME="restoreId" VALUE="">
	<INPUT TYPE="HIDDEN" NAME="deleteId" VALUE="">
	<INPUT TYPE="HIDDEN" NAME="restoreAll" VALUE="">
	<INPUT TYPE="HIDDEN" NAME="deleteAll" VALUE="">
	<INPUT TYPE="HIDDEN" NAME="typeId" VALUE="<?php echo $typeId;?>">
	<INPUT TYPE="HIDDEN" NAME="sortName" VALUE="<?php echo $sortName;?>">
	<INPUT TYPE="HIDDEN" NAME="direction" VALUE="<?php echo $direction;?>">
	<INPUT TYPE="HIDDEN" NAME="ocp_brojac" VALUE="<?php echo $brojac?>">
	<INPUT TYPE="HIDDEN" NAME="ocp_broj" VALUE="<?php echo $broj?>">
</FORM>
<SCRIPT>
	window.onload = function(){
		parent.menuFrame.eraseQuerySubmenu();
		parent.menuFrame.populateNavigationSubmenu(<?php echo $broj?>, <?php echo $brojac?>, <?php echo $recordCount?>);
		parent.detailFrame.location.href = '/ocp/html/blank.html';
	}
	function sort(sortName, direction){
		document.reconstructForm.sortName.value = sortName;
		document.reconstructForm.direction.value = direction;
		document.reconstructForm.submit();
	}
	function goRestore(restoreId){
		document.reconstructForm.restoreId.value = restoreId;
		document.reconstructForm.submit();
	}
	function goDelete(deleteId){
		if (confirm("<?php echo ocpLabels('Are you sure you want to delete object');?>?")){
			document.reconstructForm.deleteId.value = deleteId;
			document.reconstructForm.submit();
		}
	}
	function goRestoreAll(){
		document.reconstructForm.restoreAll.value = "1";
		document.reconstructForm.submit();
	}
	function goDeleteAll(){
		if (confirm("<?php echo ocpLabels('Are you sure you want to delete all objects');?>?")){
			document.reconstructForm.deleteAll.value = "1";
			document.reconstructForm.submit();
		}
	}
	function newOffset(offset){
		document.reconstructForm.ocp_brojac.value = offset;
		document.reconstructForm.submit();
	}
</SCRIPT></BODY>
</HTML>