<?php
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/connect.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/session.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/tipoviobjekata.php");

	$objId = utils_requestInt(getGVar("objId"));
	$typeId = utils_requestInt(getGVar("typeId"));
	$type = utils_requestStr(getGVar("type"));

	if (!utils_valid($type)){
		$type = tipobj_getName($typeId);
	} 


	$ocpDefaultValues = utils_requestStr(getGVar("ocpDefaultValues"));
	$defaultValues = "";
	if (utils_valid($ocpDefaultValues)){
//name1:value1|name2:value2|....|namen:valuen
		$defaultValues = " AND a1." . $ocpDefaultValues;
// AND a1.name1:value1|name2:value2|....|namen:valuen
		$defaultValues = ereg_replace("[|]", "' AND a1.", $defaultValues);
// AND a1.name1:value1' AND a1.name2:value2' AND a1.namen:valuen
		$defaultValues = ereg_replace("[:]", "='", $defaultValues);
// AND a1.name1='value1' AND a1.name2='value2' AND a1.namen='valuen
		$defaultValues .= "'";
		$defaultValues .= str_replace("a1.", "a2.", $defaultValues);
	}

	$direction = utils_requestStr(getGVar("direction"));

	$sql = "SELECT a1.Id, a1.OcpOrderColumn, a2.OcpOrderColumn as OcpOrderColumn2 ";
	$sql .= "FROM $type a1, $type a2 ";
	$sql .= "WHERE a2.Id=".$objId." AND a1.Id <> ".$objId." AND a1.Valid = 1 AND a2.Valid = 1 ";
	$sql .= $defaultValues;

	if ($direction == "asc"){
		$sql .= " AND a1.OcpOrderColumn < a2.OcpOrderColumn ORDER BY a1.OcpOrderColumn DESC LIMIT 0, 1";
	} else {
		$sql .= " AND a1.OcpOrderColumn > a2.OcpOrderColumn ORDER BY a1.OcpOrderColumn ASC LIMIT 0, 1";
	}	
//utils_dump($sql);
	$record = con_getResult($sql);
	if (count($record) > 0){
		$sql = "UPDATE $type SET OcpOrderColumn = ".$record["OcpOrderColumn"]." WHERE Id = ".$objId;
//utils_dump($sql);
		con_update($sql);
		$sql = "UPDATE $type SET OcpOrderColumn = ".$record["OcpOrderColumn2"]." WHERE Id = ".$record["Id"];
//utils_dump($sql);
		con_update($sql);
	}
	
		$redirectUrl = utils_requestStr(getGVar("redirectUrl"));
	if (utils_valid($redirectUrl)){
?><script>
	this.location.href="<?php echo $redirectUrl?>";
</SCRIPT><?php		
	}	else{
?><script>
	parent.subMenuFrame.reconstruct();
</SCRIPT><?php
	}
?>