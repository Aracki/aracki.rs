<?php
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/connect.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/session.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/polja.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/selectradio.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/objekti.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/tipoviobjekata.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/xml.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/xml_tools.php");
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/ocp/css/opsti.css">
<link rel="stylesheet" href="/ocp/css/opcije.css">
</head>
<body class="ocp_body">
<?php 
	$recordCount = 0; //globalna promenljiva u pozvanoj f-ji se puni
	$nizObjekata = array();
	$typeId = utils_requestInt(getPVar("typeId"));
	$type = tipobj_getName($typeId);
	$brojac = utils_requestInt(getPVar("ocp_brojac"));
	$broj = utils_requestInt(getPVar("ocp_broj"));
	$sortingName = utils_requestStr(getPVar("sortName"));
	$direction = utils_requestStr(getPVar("direction"));
	$objId = utils_requestInt(getPVar("objId"));
	
	//default vrednosti
	$ocpDefaultValues = utils_requestStr(getPVar("ocpDefaultValues"));
	$ocpDefaultObject = utils_parseOcpDefaultValues($ocpDefaultValues);

	$inForm = "";
	$filterText = ": ";

	$fields = (utils_valid($ocpDefaultValues)) ? 
				polja_getFields($typeId, null, xml_getAllFields($type)) : polja_getFields($typeId, null, xml_getIdenFields($type));
	$idenBits = xml_getIdenCheckbox($type);

	for ($i=0; $i<count($fields); $i++){
		$record = $fields[$i];

		switch ($record["TipTabela"]){
			case "Dates" :
				$param_od = datetime_getFormDate($record["ImePolja"]."_od");
				if (!utils_valid($param_od) && utils_valid($ocpDefaultValues) && isset($ocpDefaultObject[$record["ImePolja"]."_od"])) $param_od = $ocpDefaultObject[$record["ImePolja"]."_od"];
				if (utils_valid($param_od)){
					$record["Value"] = $param_od;
					$inForm .= '<INPUT TYPE="HIDDEN" NAME="'.$record["ImePolja"].'_od" VALUE="'.$param_od.'">';
					$filterText .= ocpLabels($record["Labela"]).": ".ocpLabels("from")." \"".$param_od."\", ";
				} else $record["Value"] = "";
			
				$param_do = datetime_getFormDate($record["ImePolja"]."_do");
				if (!utils_valid($param_do) && utils_valid($ocpDefaultValues) && isset($ocpDefaultObject[$record["ImePolja"]."_do"])) $param_do = $ocpDefaultObject[$record["ImePolja"]."_do"];
				if (utils_valid($param_do)){
					$record["Value1"] = $param_do;
					$inForm .= '<INPUT TYPE="HIDDEN" NAME="'.$record["ImePolja"].'_do" VALUE="'.$param_do.'">';
					if (!utils_valid($param_od)) $filterText .= $record["Labela"].": ";
					$filterText .= ocpLabels("to")." \"".$param_do."\", ";
				} else $record["Value1"] = "";
				$fields[$i] = $record;
				break;
			case "Bits" :
					$includeCheck = utils_requestStr(getPVar($record["ImePolja"]."_include"));
					$param = "";
					if (!is_null($idenBits[$record["ImePolja"]]) && ($includeCheck == "1")){
						$param = utils_requestStr(getPVar($record["ImePolja"]));
					} else {
						if (utils_valid($ocpDefaultValues) && ($ocpDefaultObject[$record["ImePolja"]."_include"] == "1")){ 
							$param = $ocpDefaultObject[$record["ImePolja"]];
							$includeCheck == "1";
						}
					}

					if ($includeCheck == "1"){
						if (utils_valid($param) && ($param != "0")){
							$record["Value"] = "1";
							$inForm .= '<INPUT TYPE="HIDDEN" NAME="'.$record["ImePolja"].'" VALUE="1"><INPUT TYPE="HIDDEN" NAME="'.$record["ImePolja"].'_include" VALUE="'.$includeCheck.'">';
							$filterText .= ocpLabels($record["Labela"]).": \"".ocpLabels("Yes")."\", ";
						} else {
							$record["Value"] = "0";
							$inForm .= '<INPUT TYPE="HIDDEN" NAME="'.$record["ImePolja"].'" VALUE="0"><INPUT TYPE="HIDDEN" NAME="'.$record["ImePolja"].'_include" VALUE="'.$includeCheck.'">';
							$filterText .= ocpLabels($record["Labela"]).": \"".ocpLabels("No")."\", ";
						}		
					}
					$fields[$i] = $record;
					break;
			default :
				$param = "";
				if ($record["TipTabela"] == "Ints" || $record["TipTabela"] == "Objects" || $record["TipTabela"] == "Uploads"){
					$param = intval(getPVar($record["ImePolja"]));
					if (isset($ocpDefaultValues) && isset($ocpDefaultObject[$record["ImePolja"]]) && (!utils_valid($param) || ($param==0)) && utils_valid($ocpDefaultValues))
						$param = intval($ocpDefaultObject[$record["ImePolja"]]);
				} else {
					$param = utils_requestStr(getPVar($record["ImePolja"]));
					if (isset($ocpDefaultObject[$record["ImePolja"]]) && !utils_valid($param) && utils_valid($ocpDefaultValues)) $param = utils_requestStr($ocpDefaultObject[$record["ImePolja"]]);
				}

				if (utils_valid($param)){
					$record["Value"] = $param;
					$inForm .= '<INPUT TYPE="HIDDEN" NAME="'.$record["ImePolja"].'" VALUE="'.$param.'">';
					if (($record["TipTabela"] == "Objects") && ($param != 0)){
						$object = obj_get($record["PodtipIme"], $param);
						$filterText .= ocpLabels($record["Labela"]).": \"".xml_generateRecordIdenString($record["PodtipIme"], $object)."\", ";
					} else if (($record["TipTabela"] == "Objects") && ($param == 0)) {
						;
					}else 
						$filterText .= ocpLabels($record["Labela"]).": \"".$param."\", ";
				}
				$fields[$i] = $record;
				break;
		}
	}

	if (!utils_valid($sortingName)){
		$sortingName = "Id";
		$direction = "desc";
		if (polja_hasOrderColumn($type)){
			$sortingName = "OcpOrderColumn";
			$direction = "desc";
		}
	} else if (!utils_valid($direction))
		$direction = "asc";
		
	$nizObjekata = obj_getList($typeId, $fields, $sortingName, $direction, $broj, $brojac); 
?><FORM ACTION="objects.php?<?php echo utils_randomQS();?>" METHOD="POST" NAME="reconstructForm" ID="reconstructForm">
	<?php echo $inForm;?>
	<INPUT TYPE="HIDDEN" NAME="typeId" VALUE="<?php echo $typeId;?>">
	<INPUT TYPE="HIDDEN" NAME="ocp_brojac" VALUE="<?php echo $brojac;?>">
	<INPUT TYPE="HIDDEN" NAME="ocp_broj" VALUE="<?php echo $broj;?>">
	<INPUT TYPE="HIDDEN" NAME="sortName" VALUE="<?php echo $sortingName;?>">
	<INPUT TYPE="HIDDEN" NAME="direction" VALUE="<?php echo $direction;?>">
	<INPUT TYPE="HIDDEN" NAME="objId" VALUE="<?php echo $objId;?>">
	<INPUT TYPE="HIDDEN" NAME="ocpDefaultValues" VALUE="<?php echo $ocpDefaultValues?>">
</FORM>
<SCRIPT>
	var pressed = false;
	window.onload = function(){
		parent.menuFrame.populateNavigationSubmenu(<?php echo $broj;?>, <?php echo $brojac;?>, <?php echo $recordCount;?>);
<?php	if (utils_valid($objId) && $objId != 0) { ?>
		parent.detailFrame.location.href = "form.php?<?php echo utils_randomQS();?>&objId=<?php echo $objId;?>&action=iu&typeId=<?php echo $typeId;?>";
		document.reconstructForm.objId.value = "";
<?php	} else { ?>
		parent.detailFrame.location.href = "../html/blank.html";
<?php	} ?>
	}
	function reconstruct(){ 
		document.reconstructForm.submit();
	}
	function sort(sortName, direction){
		document.reconstructForm.sortName.value = sortName;
		document.reconstructForm.direction.value = direction;
		document.reconstructForm.submit();
	}
	function newOffset(offset){
		document.reconstructForm.ocp_brojac.value = offset;
		document.reconstructForm.submit();
	}
	function goForm(objId, action){
window.open("form.php?<?php echo utils_randomQS();?>&objId="+objId+"&action="+action+"&typeId=<?php echo $typeId;?>&objOrgId=<?php echo $objId;?>&ocpDefaultValues=<?php echo $ocpDefaultValues;?>", "detailFrame");
	}
	function goDelete(objId){
		window.open("delete.php?<?php echo utils_randomQS();?>&typeId=<?php echo $typeId;?>&objId="+objId, "detailFrame");
	}
	function goOcpOrder(objId, direction){
		document.reconstructForm.sortName.value = "OcpOrderColumn";
		document.reconstructForm.direction.value = "desc";
		window.open("order.php?<?php echo utils_randomQS();?>&typeId=<?php echo $typeId;?>&objId="+objId+"&ocpDefaultValues=<?php echo $ocpDefaultValues;?>&direction="+direction, "detailFrame");
	}
</SCRIPT><?php

	$xmlDoc = xml_createObject();

	xml_generateList($type, $typeId, $nizObjekata, "objects.php");
	xml_setAttribute(xml_documentElement($xmlDoc), "startIndex", intval($broj)*intval($brojac));
	objects_appendLabels(xml_documentElement($xmlDoc), $sortingName, $direction, substr($filterText, 0, strlen($filterText) - 2), intval($brojac), intval($broj), $recordCount);
	
	echo(xml_transform($xmlDoc, "idenXsl"));

	/*Popunjava osnovne labele
	==========================*/
	function objects_appendLabels($node, $sortName, $direction, $filterText, $brojac, $broj, $recordCount){
		$strNav = ($recordCount > 0) ? 
			"(".($broj*$brojac + 1)."-".min(($brojac+1)*$broj, $recordCount)."/".$recordCount.")" : "(0-0/0)";
		xml_setAttribute($node, "labHeader", ocpLabels("Found objects list").": ".$strNav);
		if (utils_valid($filterText)){
			xml_setAttribute($node, "labFilter", ocpLabels("Filter"));
			xml_setAttribute($node, "filterText", ereg_replace("/&quot;/", "\"", $filterText));
		}
		xml_setAttribute($node, "labCreate", ocpLabels("CREATE"));

		xml_setAttribute($node, "labSortAscending", ocpLabels("Sort ascending"));
		xml_setAttribute($node, "labSortDescending", ocpLabels("Sort descending"));
		xml_setAttribute($node, "labMoveUp", ocpLabels("Move up"));
		xml_setAttribute($node, "labMoveDown", ocpLabels("Move down"));
		xml_setAttribute($node, "labTools", ocpLabels("TOOLS"));
		xml_setAttribute($node, "labNo", ocpLabels("NO"));
		xml_setAttribute($node, "sortName", $sortName);
		xml_setAttribute($node, "direction", $direction);

		xml_setAttribute($node, "labEditObject", ocpLabels("Edit object"));
		xml_setAttribute($node, "labDeleteObject", ocpLabels("Delete object"));
		xml_setAttribute($node, "labCopyObject", ocpLabels("Copy object"));
	}
?>
</body>
</html>