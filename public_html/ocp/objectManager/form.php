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
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/config/triggers.php");

?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/ocp/css/opsti.css">
<link rel="stylesheet" href="/ocp/css/opcije.css">
<?php require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/controls/auto_complete/require.php");?>
</head>
<body class="ocp_body">
<script>
	simpleEditorExists = false;
</script><?php

	$validateFunc = array(); //save all the validate functions
	$validateNamesFunc = array(); //save all diffrent functions that should be imported
	$Id = getPVar("Id");

	if (!is_null($Id)) { //posle submita
		$Id = intval($Id);
		$vrednosti = array(); 
		$type = utils_requestStr(getPVar("ocpType"));
		if ($Id != "") {
			$editGroup = utils_requestStr(getPVar("EditGroup"));
			$data = obj_get($type, $Id);
			$polja = array_keys($data);

			if (array_key_exists("Id", $data)){
				$editGroupPolja = xml_getEditGroupFields($type, $editGroup);
				for ($i=0; $i < count($polja); $i++){
					if (!in_array( $polja[$i], $editGroupPolja)){
						$vrednosti[$polja[$i]] = $data[$polja[$i]];
					} else{
						if (polja_getFieldType($type, $polja[$i]) == "Dates"){
							$vrednosti[$polja[$i]] = datetime_getFormDate($polja[$i]);
						} else 
							$vrednosti[$polja[$i]] = utils_strictHtml(utils_requestStr(getPVar($polja[$i]), true));
					}
				}
				obj_update($type, $vrednosti, $data);
			} else {
?><script>alert("<?php echo ocpLabels('Editing object does not exist anymore')?>");</script><?php
			}
		} else if ($Id == "") {
			$nizPolja = polja_getFields(tipobj_getId($type));
			for ($i=0; $i < count($nizPolja); $i++){
				$polja = $nizPolja[$i];
				if ($polja["TipTabela"] == "Dates")
					$vrednosti[$polja["ImePolja"]] = datetime_getFormDate($polja["ImePolja"]);
				else
					$vrednosti[$polja["ImePolja"]] = utils_strictHtml(utils_requestStr(getPVar($polja["ImePolja"]), true));
			}
			obj_insert($type, $vrednosti);
		}
?><script>	
	parent.document.getElementById("resizableFrameset").setAttribute("rows", "100%,*");
	parent.subMenuFrame.reconstruct();
</script><?php
	} else { //pre submita
		$action = utils_requestStr(getGVar("action"));
		$objId = utils_requestInt(getGVar("objId"));
		$typeId = utils_requestInt(getGVar("typeId"));
		$type = tipobj_getName($typeId);

		//default vrednosti
		$ocpDefaultValues = utils_requestStr(getGVar("ocpDefaultValues"));
//utils_dump($ocpDefaultValues, 1);		
		$ocpDefaultObject = utils_parseOcpDefaultValues($ocpDefaultValues);
//utils_dump($ocpDefaultObject, 1);

		$editGroup = utils_requestStr(getGVar("editGroup"));

		$validObj = true;

		if (($action == "iu") && ($objId==-1)){
			if (polja_hasOrderColumn($type)){
				if (is_null($ocpDefaultObject)) $ocpDefaultObject = array(); 
				$ocpDefaultObject["OcpOrderColumn"] = obj_getMaxOrder($type);
			}
		} else if (($action == "iu") || ($action == "copy")){
			$validObj = obj_isValid($type, $objId);
			if ($validObj){
				if ($action == "copy"){
					$ocpDefaultObject = obj_copy($type, $objId);
					if (polja_hasOrderColumn($type)) 
						$ocpDefaultObject["OcpOrderColumn"] = obj_getMaxOrder($type);
				} else $ocpDefaultObject = obj_get($type, $objId);
			}
		}

		if ($validObj){
			$ocpDefaultObject = obj_preForm($type, $ocpDefaultObject, $objId);

			$xmlDoc = xml_createObject();

			xml_generateForm($type, $ocpDefaultObject, "form.php", $editGroup);
			form_appendLabels(xml_getFirstElementByTagName($xmlDoc, "fields"), $objId);
			echo(xml_transform($xmlDoc, "formXsl"));

			$userHeight = utils_valid(getSVar("ocpUserHeight")) ? getSVar("ocpUserHeight") : "25%";
?><script language="javascript">
	window.onload = function(){
		parent.document.getElementById("resizableFrameset").setAttribute("rows", "<?php echo $userHeight;?>,*");

		var iframes = document.getElementsByTagName("iframe");
		for (var i=0; i<iframes.length; i++){
			if (iframes[i].getAttribute("src").indexOf("/ocp/controls/simple_editor/editor.") == 0){
				window.frames[i].init();
			}
		}
	}
	function openEditGroup(editGroupName){
		window.open("/ocp/objectManager/form.php?<?php echo utils_randomQS();?>&action=iu&typeId=<?php echo $typeId;?>&objId=<?php echo $objId;?>&editGroup="+editGroupName, "_self");
	}
</script><?php
			utils_getValidation($validateNamesFunc, $validateFunc);
		} else{
?><script>
	alert("<?php echo ocpLabels('Editing object does not exist anymore')?>");
	parent.subMenuFrame.reconstruct();
</script><?php
		}
	}

	/*Popunjava osnovne labele
	==========================*/
	function form_appendLabels($node, $objId){
		if ($objId == -1)
			xml_setAttribute($node, "labHeader", ocpLabels("New object"));
		else 
			xml_setAttribute($node, "labHeader", ocpLabels("Edit object"));
		xml_setAttribute($node, "labGroups", ocpLabels("GROUPS"));
		xml_setAttribute($node, "labGeneral", ocpLabels("General"));
		xml_setAttribute($node, "labSave", ocpLabels("Save"));
		xml_setAttribute($node, "labCancel", ocpLabels("Cancel"));

		xml_setAttribute($node, "labCalendar", ocpLabels("Calendar"));
		xml_setAttribute($node, "labCreateLinkOnPage", ocpLabels("Create link on OCP page"));
		xml_setAttribute($node, "labCreateLinkOnBlock", ocpLabels("Create link on block"));
		xml_setAttribute($node, "labBrowseServer", ocpLabels("Browse server"));
		xml_setAttribute($node, "labSelectedImagePreview", ocpLabels("Selected image preview"));
		xml_setAttribute($node, "labSelectedLinkPreview", ocpLabels("Selected link preview"));
		xml_setAttribute($node, "labRichTextFormat", ocpLabels("Rich text format"));
		xml_setAttribute($node, "labColorPallete", ocpLabels("Color pallete"));
		xml_setAttribute($node, "labUpdateListOfValue", ocpLabels("Update list of values"));
		xml_setAttribute($node, "labSelect", ocpLabels("Choose"));
	}
?></body>
</html>