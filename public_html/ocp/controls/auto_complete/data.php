<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../include/objekti.php");
	require_once("../../include/xml.php");
	require_once("../../include/xml_tools.php");
	require_once("../../include/polja.php");
	require_once("../../include/upload.php");
	require_once("../../include/selectradio.php");
	require_once("../../include/tipoviobjekata.php");

	global $xmlDelimiter;

	$txt = utils_requestStr(getGVar("mask"));
	$fieldValue = utils_requestInt(getGVar("fieldValue"));
	$type = utils_requestStr(getGVar("type"));	//complex tip
	$restrict = utils_requestStr(getGVar("restrict"), 0, 1);	//restrikcija

	if (!utils_valid($restrict)) $restrict = "";

	$sortName = xml_getSortName($type);

	$restrict .= " and $sortName like '$txt%'";

	$typeId = tipobj_getId($type);			//id tipa
	$obj = obj_getList($typeId, polja_getFields($typeId), $sortName, "asc", 100, 0, $restrict);
	$objLabels = xml_generateRecordsIdenString($type, $obj) . $xmlDelimiter;


	header('Content-Type: application/xml; charset=UTF-8'); 

	?><complete><?php

	if (count($obj) > 0){
		for ($i=0; $i<count($obj); $i++){
			$objLabel = utils_substr($objLabels, 0, utils_strpos($objLabels, $xmlDelimiter));
			$objLabels = utils_substr($objLabels, utils_strpos($objLabels, $xmlDelimiter) + strlen($xmlDelimiter));
			?><option value="<?php echo $obj[$i]["Id"];?>"><?php echo utils_htmlEncode($objLabel);?></option><?php
		}		
				
	} else {
		?><option value=""></option><?php
	}

	?></complete><?php
?>