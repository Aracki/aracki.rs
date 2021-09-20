<?php
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/connect.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/session.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/polja.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/selectradio.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/objekti.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/tipoviobjekata.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/upload.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/xml.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/xml_tools.php");
?>
<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/ocp/css/opsti.css">
<link rel="stylesheet" href="/ocp/css/opcije.css">
<SCRIPT SRC="/ocp/jscript/helpcalendar.js" type="text/javascript"></SCRIPT>
<SCRIPT SRC="/ocp/jscript/select.js" type="text/javascript"></SCRIPT>
<SCRIPT SRC="/ocp/jscript/pallete.js" type="text/javascript"></SCRIPT>
<SCRIPT SRC="/ocp/validate/validate_double_quotes.js" type="text/javascript"></SCRIPT>
<?php require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/controls/auto_complete/require.php");?>
<script>
	simpleEditorExists = false;
</script>
</HEAD>
<BODY class="ocp_body">
<?php
	$typeId = utils_requestInt(getGVar("typeId"));
	$type = tipobj_getName($typeId);
	$objId = utils_requestInt(getGVar("objId"));
	$ocpDefaultValues = utils_requestStr(getGVar("ocpDefaultValues"));
?>
<SCRIPT LANGUAGE="javascript">
	function reload(indikator) { 
		if (indikator == 1) document.forma.submit();
		else parent.menuFrame.populateQuerySubmenu();
		parent.detailFrame.location.href = "/ocp/html/sadrzaj_blank.html";
	}
</SCRIPT>
<?php	
	$caseQuery = utils_requestStr(getGVar("case")); 
	$queryForm = false;

	switch ($caseQuery){
		case "noCount" :	//obavezno se radi pretraga
			$queryForm = true;
			break;
		case "redirect":	//radi se redirekcija
			$queryForm = false;
			break;
		case "count":		//broji se koliko ima objekata i na osnovu toga odlucuje
		default:
			if (tipobj_getTypeCount($type) > 50) $queryForm = true;
			break;
	}

	if (!$queryForm){
		$sortingName = "Id";
		$sortingOrder = "desc";
		if (polja_hasOrderColumn($type)){
			$sortingName = "OcpOrderColumn";
			$sortingOrder = "desc";
		}

?><FORM ACTION="objects.php?<?php echo utils_randomQS();?>" METHOD="POST" NAME="forma" ID="forma">
		<INPUT TYPE="HIDDEN" NAME="typeId" VALUE="<?php echo $typeId;?>">
		<INPUT TYPE="HIDDEN" NAME="ocp_brojac" VALUE="0">
		<INPUT TYPE="HIDDEN" NAME="ocp_broj" VALUE="50">
		<INPUT TYPE="HIDDEN" NAME="sortName" VALUE="<?php echo $sortingName?>">
		<INPUT TYPE="HIDDEN" NAME="direction" VALUE="<?php echo $sortingOrder?>">
		<INPUT TYPE="HIDDEN" NAME="objId" VALUE="<?php echo $objId;?>">
		<INPUT TYPE="HIDDEN" NAME="ocpDefaultValues" VALUE="<?php echo $ocpDefaultValues?>">
	</FORM>
<SCRIPT> reload(1);	</SCRIPT>
<?php	
		
		} else {
			$xmlDoc = xml_createObject();
			$data = utils_parseOcpDefaultValues($ocpDefaultValues);
			xml_generateForm($type, $data, "objects.php");
			$fieldsNode = xml_getFirstElementByTagName($xmlDoc, "fields");
			xml_setAttribute($fieldsNode, "ocpDefaultValues", $ocpDefaultValues);
			query_appendLabels($fieldsNode, $typeId);
		
			echo(xml_transform($xmlDoc, "formXsl"));
?><SCRIPT> reload(0);</SCRIPT><?php
	}

	/*Popunjava osnovne labele
	==========================*/
	function query_appendLabels($node, $typeId){
		xml_setAttribute($node, "labHeader", str_replace("'xxxx'", ocpLabels(tipobj_getLabel($typeId)), ocpLabels("Search objects of type 'xxxx':")));
		xml_setAttribute($node, "labSort", ocpLabels("sort"));
		xml_setAttribute($node, "labDisplay", ocpLabels("Display"));
		xml_setAttribute($node, "labObjectsPerPage", ocpLabels("objects per page"));

		xml_setAttribute($node, "labSearch", ocpLabels("Search"));
		xml_setAttribute($node, "labCancel", ocpLabels("Cancel"));
		xml_setAttribute($node, "labFrom", ocpLabels("From"));
		xml_setAttribute($node, "labTo", ocpLabels("To"));
		xml_setAttribute($node, "labIncludeInSearch", ocpLabels("Include in search"));
		xml_setAttribute($node, "labAdvancedSearch", ocpLabels("Advanced search"));

		xml_setAttribute($node, "labCalendar", ocpLabels("Calendar"));
		xml_setAttribute($node, "labCreateLinkOnPage", ocpLabels("Create link on OCP page"));
		xml_setAttribute($node, "labCreateLinkOnBlock", ocpLabels("Create link on block"));
		xml_setAttribute($node, "labBrowseServer", ocpLabels("Browse server"));
		xml_setAttribute($node, "labSelectedImagePreview", ocpLabels("Selected image preview"));
		xml_setAttribute($node, "labSelectedLinkPreview", ocpLabels("Selected link preview"));
		xml_setAttribute($node, "labRichTextFormat", ocpLabels("Rich text format"));
		xml_setAttribute($node, "labColorPallete", ocpLabels("Color pallete"));
		xml_setAttribute($node, "labUpdateListOfValue", ocpLabels("Update list of values"));
	}
?>
</BODY>
</HTML>
