<?php
require_once("../include/connect.php");
require_once("../include/session.php");
require_once("../include/objekti.php");
require_once("../include/xml.php");
require_once("../include/xml_tools.php");
require_once("../include/polja.php");
require_once("../include/upload.php");
require_once("../include/selectradio.php");
require_once("../include/tipoviobjekata.php");
?>

<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/ocp/css/opsti.css">
<link rel="stylesheet" href="/ocp/css/opcije.css">
</HEAD>
<BODY style="margin:0px 0px 0px 0px;"><?php
	$brojObjekata = 20;

	$type = utils_requestStr(getGVar("Type"));	//complex tip
	$fieldName = utils_requestStr(getGVar("FieldName"));	//ime polja
	$fieldValue = utils_requestInt(getGVar("FieldValue"));	//vrednost polja
	$offset = getGVar("offset"); //offset
	if (!utils_valid($offset)) $offset = 0;
	else $offset = intval($offset) - 1;
	$restrict = utils_requestStr(getGVar("restrict"), 0, 1);
	//utils_dump("type:".$type." fn:".$fieldName." fv:".$fieldValue." off:".$offset." ".$restrict);

	if (utils_valid($type)){
		$obj = array();
		$objLabels = "";
		if ($type == "Upload"){
			$obj = upload_getUploadLabels($fieldValue, $restrict);
			for ($i=0; $i<count($obj); $i++){
				$objLabels .= $obj[$i]["Label"];
				if ($i != (strlen($obj)-1))
					$objLabels .= "|@$";
			}
		} else{
			$sortName = xml_getSortName($type);

			$typeId = tipobj_getId($type);			//id tipa
			$obj = obj_getList($typeId, polja_getFields($typeId), $sortName, "asc", $brojObjekata, $offset, $restrict);
			$objLabels = xml_generateRecordsIdenString($type, $obj) . "|@$";
		}
?><select name="Complex" class="ocp_forma" style="width:100%" onChange="changeParent(this.options[this.selectedIndex].value)">
	<option value=""></option><?php	
		for ($i=0; $i<count($obj); $i++){
			$objLabel = utils_substr($objLabels, 0, utils_strpos($objLabels, "|@$"));
			$objLabels = utils_substr($objLabels, utils_strpos($objLabels, "|@$")+3);
		
			$selected = "";
			if ($obj[$i]["Id"] == $fieldValue) $selected = "selected";	?>
				<option value="<?php echo $obj[$i]["Id"];?>" <?php echo $selected;?>><?php echo utils_htmlEncode($objLabel);?></option><?php
		}	?></select><?php
	}
?>
<script src="/ocp/validate/validate_double_quotes.js"></script>
<script>
	function changeParent(value){
		eval('parent.document.formObject.<?php echo $fieldName;?>.value="'+validate_double_quotes_value(value)+'"');
	}
</script>
</BODY>
</HTML>