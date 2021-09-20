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
?>
<html>
<head>
	<link rel="stylesheet" href="/ocp/css/opsti.css">
	<link rel="stylesheet" href="/ocp/css/opcije.css">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script>
		function setParentButtonsVisiblitity(mode){
			var parentForm = parent.document.forms["formObject"];
			for (var i=0; i<parentForm.elements.length; i++){
				if (parentForm.elements[i].type == "submit" || 
					parentForm.elements[i].type == "reset" || 
					parentForm.elements[i].type == "image" || 
					parentForm.elements[i].type == "button" ){
					parentForm.elements[i].style.visibility = mode; 
				}
			}
		}
		var simpleEditorExists = false;
	</script>
	<?php require_once("../../../controls/auto_complete/require.php");?>
</head>
<body class="ocp_body" onLoad="resizeMe();">
<script>
	setParentButtonsVisiblitity('visible');
</script>
<?php

//QueryString
	$superType = utils_requestStr(getGVar("SuperType"));	//tip na kom je podforma
	$superTypeId = utils_requestInt(getGVar("SuperTypeId"));	//id objekta na kom je podforma
	$type = utils_requestStr(getGVar("Type"));	//podtip cija je podforma
	$typeField = utils_requestStr(getGVar("TypeField"));	//podtip cija je podforma
	$typeId = utils_requestInt(getGVar("Id"));	//id objekta koji je u podformi
	$editable = utils_requestStr(getGVar("Editable"));	//da li objekat moze da se update-uje

	$broj = 10;
	$brojac = utils_requestInt(getGVar("ocp_brojac"));

	$sortName = utils_requestStr(getGVar("sortName"));
	$direction = utils_requestStr(getGVar("direction"));

	if (!utils_valid($superType)) {	//POST
		$vrednosti = array(); 

		$superType = utils_requestStr(getPVar("SuperType"));	
		$superTypeId = utils_requestInt(getPVar("SuperTypeId"));
		$type = utils_requestStr(getPVar("ocpType"));
		$typeField = utils_requestStr(getPVar("TypeField"));
		$typeId = utils_requestInt(getPVar("Id"));
		$editable = utils_requestStr(getPVar("Editable"));
		$brojac = utils_requestInt(getPVar("ocp_brojac"));
		$sortName = utils_requestStr(getPVar("sortName"));
		$direction = utils_requestStr(getPVar("direction"));
		$editGroup = utils_requestStr(getPVar("EditGroup"));

//utils_dump("superType ".$superType);
//utils_dump("superTypeId ".$superTypeId);
//utils_dump("type ".$type);
//utils_dump("typeField ".$typeField);
//utils_dump("typeId ".$typeId);
//utils_dump("editable ".$editable);
//utils_dump("brojac ".$brojac);
//utils_dump("sortName ".$sortName);
//utils_dump("direction ".$direction);

		if (utils_valid($typeId) && ($typeId != 0)) {
			$data = obj_get($type, $typeId);
			$polja = (array_keys($data));
			if (count($polja) > 0){
				$editGroupPolja = xml_getEditGroupFields($type, $editGroup);
//utils_dump($editGroupPolja, 1);
				for ($i=0;$i<count($polja);$i++){
					if (!in_array( $polja[$i], $editGroupPolja)){
						$vrednosti[$polja[$i]] = $data[$polja[$i]];
					} else{
						if ($polja[$i] == $typeField) $vrednosti[$polja[$i]] = $superTypeId;
						else{
							if (polja_getFieldType($type, $polja[$i]) == "Dates")
								$vrednosti[$polja[$i]] = datetime_getFormDate($polja[$i]);
							else 
								$vrednosti[$polja[$i]] = utils_requestStr(getPVar($polja[$i]), true);
						}
					}
				}
				obj_update($type, $vrednosti, $data);
			}
		} else {
			$nizPolja = polja_getFields(tipobj_getId($type));
			for ($i=0; $i < count($nizPolja); $i++){
				$polja = $nizPolja[$i];
				if ($polja["ImePolja"] == $typeField) $vrednosti[$polja["ImePolja"]] = $superTypeId;
				else{
					if ($polja["TipTabela"] == "Dates")
						$vrednosti[$polja["ImePolja"]] = datetime_getFormDate($polja["ImePolja"]);
					else
						$vrednosti[$polja["ImePolja"]] = utils_requestStr(getPVar($polja["ImePolja"]), true);
				}
			}
			
			obj_insert($type, $vrednosti);
		}
	} 

//ako je Akcija stigla
	$Akcija = utils_requestStr(getGVar("Akcija"));

	if (!utils_valid($Akcija) || ($Akcija == "Delete")){//lista objekata koji su u podformi
		if ($Akcija == "Delete")
			obj_deleteSubType($type, $typeId, $typeField);
		$where = " and " . $typeField . "=" . $superTypeId;
		if (!utils_valid($sortName)) {
			$sortName = "Id"; 
			$direction = "desc";
			if (polja_hasOrderColumn($type)){
				$sortName = "OcpOrderColumn";
				$direction = "asc";
			}
		}
		$nizObjekata =  obj_getAll($type, $sortName, $direction, $where);
		$brojac = intval($brojac);
		$limit = min(($brojac+1)*$broj, count($nizObjekata));
?><div id="ocp_blok_menu_1">
	<table class="ocp_blokovi_table" >
		<tr>
			<td class="ocp_blokovi_td" style="padding-left: 6px;"><?php 
		if (count($nizObjekata) > $broj){	?>
				<span style="color: #C42E00;"><?php echo ocpLabels("Page");?>: </span><?php
				echo(subforms_getNavigationChain("javascript:newOffset(", count($nizObjekata), $broj, $brojac));
				?><br><?php
		}
		$TR = $_SESSION["ocpTR"];

		$strNav = (count($nizObjekata) > 0) ? 
			"(".($broj*$brojac + 1)."-".$limit."/".count($nizObjekata).")" : "(0-0/0)";
			?><?php echo (ocpLabels("Found objects list").": ".$strNav);?>
			</td>
			<td class="ocp_blokovi_td" style="text-align: right;"><?php
		if ($editable && (intval($TR[tipobj_getId($type)]) > 2)){	?><table cellpadding="0" cellspacing="0" style="float: right; margin-right: 3px;">
					<tr>
						<td style="cursor:pointer" onclick="goForm(-1, 'iu');"><img src="/ocp/img/opsti/kontrole/dugme_novi_obj.gif" width="21" height="21" title="<?php echo ocpLabels("New object");?>"></td>
						<td class="ocp_opcije_dugme" style="cursor:pointer" onclick="goForm(-1, 'iu');"><?php echo ocpLabels("New object");?></td>
						<td style="cursor:pointer" onclick="goForm(-1, 'iu');"><img src="/ocp/img/opsti/kontrole/dugme_desni.gif" width="6" height="21"></td>
					</tr>
				</table><?php
		}
	?></td>
		</tr>
	</table>
</div><?php
		if (count($nizObjekata) > 0){
			$nizObjekataOffset = array();
			for ($i=($brojac*$broj); $i<$limit; $i++) {
				$nizObjekataOffset[] = $nizObjekata[$i]; }

			$xmlDoc = xml_createObject();
			xml_generateList($type, tipobj_getId($type), $nizObjekataOffset, "subforms.php", null);
			xml_setAttribute(xml_documentElement($xmlDoc), "editable", $editable);
			xml_setAttribute(xml_documentElement($xmlDoc), "startIndex", $brojac*$broj);
			subforms_appendListLabels(xml_documentElement($xmlDoc), $sortName, $direction);

			echo(xml_transform($xmlDoc, "subformIdenXsl"));
		}
	} else if (($Akcija == "iu") || ($Akcija == "copy")){ //edit objekta
		//globalne funkcije neophodne
		$validateFunc = array(); //save all the validate functions
		$validateNamesFunc = array(); //save all diffrent functions that should be imported
		$subFormAppend = array(); //moram ovu globalnu promenljivu da napunim
		$subFormAppend["SuperType"] = $superType;
		$subFormAppend["SuperTypeId"] = $superTypeId;
		$subFormAppend["TypeField"] = $typeField;
		$subFormAppend["Editable"] = $editable;
		$subFormAppend["ocp_brojac"] = $brojac;
		$subFormAppend["sortName"] = $sortName;
		$subFormAppend["direction"] = $direction;

		$objId = utils_requestInt(getGVar("Id"));
		$editGroup = utils_requestStr(getGVar("editGroup"));

		$data = array();
		$validObj = true;
		if ($objId != -1){
			$validObj = obj_isValid($type, $objId);
			if ($validObj){
				if ($Akcija == "copy"){
					$data = obj_copy($type, $objId);
					if (polja_hasOrderColumn($type)) 
						$data["OcpOrderColumn"] = obj_getMaxOrder($type);
				}else 
					$data = obj_get($type, $objId);
			}
		} else if (polja_hasOrderColumn($type)){
			$data["OcpOrderColumn"] = obj_getMaxOrder($type);
		}

		$data = obj_preForm($type, $data, $objId);

		if ($validObj){
			if ($editable){
				$xmlDoc = xml_createObject();
				xml_generateForm($type, $data, "subforms.php", $editGroup);
				subforms_appendFormLabels(xml_getFirstElementByTagName($xmlDoc, "fields"), $objId);

				echo(xml_transform($xmlDoc, "subformXsl"));	
				
				utils_getValidation($validateNamesFunc, $validateFunc);
			} else {
				$xmlDoc = xml_createObject();
				xml_generateForm($type, $data, "subforms.php");
				subforms_appendDisplayLabels(xml_getFirstElementByTagName($xmlDoc, "fields"));

				echo(xml_transform($xmlDoc, "subformDisplayXsl"));
			}
?><script>
			setParentButtonsVisiblitity('hidden');
</script><?php
		} else {
?><script>alert("<?php echo ocpLabels('Editing object does not exist anymore')?>.");</script><?php
		}
	}
?>
<FORM ACTION="subforms.php?<?php echo utils_randomQS();?>" METHOD="GET" NAME="reconstructForm" ID="reconstructForm">
	<INPUT TYPE="HIDDEN" NAME="SuperType" VALUE="<?php echo $superType;?>">
	<INPUT TYPE="HIDDEN" NAME="SuperTypeId" VALUE="<?php echo $superTypeId;?>">
	<INPUT TYPE="HIDDEN" NAME="Type" VALUE="<?php echo $type;?>">
	<INPUT TYPE="HIDDEN" NAME="TypeField" VALUE="<?php echo $typeField?>">
	<INPUT TYPE="HIDDEN" NAME="Editable" VALUE="<?php echo $editable;?>">
	<INPUT TYPE="HIDDEN" NAME="ocp_brojac" VALUE="<?php echo $brojac;?>">
	<INPUT TYPE="HIDDEN" NAME="sortName" VALUE="<?php echo $sortName;?>">
	<INPUT TYPE="HIDDEN" NAME="direction" VALUE="<?php echo $direction;?>">
</FORM>
<script>
	var pressed = false;
	function reconstruct(){
		document.reconstructForm.submit();
	}
	function sort(sortName, direction){
		document.reconstructForm.sortName.value = sortName;
		document.reconstructForm.direction.value = direction;
		document.reconstructForm.ocp_brojac.value = 0;
		document.reconstructForm.submit();
	}
	function newOffset(offset){
		document.reconstructForm.ocp_brojac.value = offset;
		document.reconstructForm.submit();
	}
	var preparedLink = "/ocp/objectManager/style/subform/subforms.php?<?php echo utils_randomQS();?>&SuperTypeId=<?php echo $superTypeId;?>&Type=<?php echo $type;?>&TypeField=<?php echo $typeField?>&SuperType=<?php echo $superType;?>&Editable=<?php echo $editable;?>&ocp_brojac=<?php echo $brojac;?>&sortName=<?php echo $sortName;?>&direction=<?php echo $direction;?>";
	function goDelete(id){
		if (confirm("<?php echo ocpLabels('Are you sure you want to delete object');?>?")) 
			this.location.href= preparedLink + "&Id="+id+"&Akcija=Delete";
	}
	function goForm(id, action){
		this.location.href= preparedLink + "&Id="+id+"&Akcija="+action;	
	}
	function openEditGroup(editGroupName){
		window.open(preparedLink + "&Akcija=iu&Id="+formObject.Id.value+"&editGroup="+editGroupName, "_self");
	}
	function goOcpOrder(objId, direction){
		this.location.href= "../../order.php?<?php echo utils_randomQS()?>&type=<?php echo $type?>&objId="+objId+"&ocpDefaultValues=<?php echo $typeField?>:<?php echo $superTypeId?>&direction="+direction+"&redirectUrl="+escape(preparedLink.substring(0, preparedLink.indexOf("&sortName"))+"&sortName=OcpOrderColumn&direction=asc");
	}
	// da bih odozdo znao da li je subforma u pitanju
	var subformPage = true;
	function resizeMe() {
		var getFFVersion=navigator.userAgent.substring(navigator.userAgent.indexOf("Firefox")).split("/")[1];
		var FFextraHeight=getFFVersion>=0.1 ?  16 : 0 //extra height in px to add to iframe in FireFox 1.0+ browsers

		var sh;
		if (document.body && document.body.scrollHeight) sh = document.body.scrollHeight;
		else 
			if (document.height) sh = document.height;
		
		var o = getMe();
		if (o.contentDocument && o.contentDocument.body.offsetHeight) //ns6 syntax
			o.height = o.contentDocument.body.offsetHeight + FFextraHeight; 
		else if (o.Document && o.Document.body.scrollHeight) //ie5+ syntax
			o.height = o.Document.body.scrollHeight;

		if (parent.subformPage) parent.resizeMe();
			
	}

	function getMe() {
		a = parent.document.getElementsByTagName("iframe");
		for (var i=0;i < a.length; i++) {
			id = a[i].id;
			frm = parent.document.getElementById(id);
			if (frm != null){
				if (id == "subForm_<?php echo $type?>_<?php echo $typeField?>"){
					return a[i];
				}
			}
		}
		return null;
	}
</script><?php

	/*Popunjava osnovne labele
	==========================*/
	function subforms_appendListLabels($node, $sortName, $direction){
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
		xml_setAttribute($node, "labDisplayObject", ocpLabels("Display object"));
	}

	function subforms_appendFormLabels($node, $objId){
		if ($objId == -1)
			xml_setAttribute($node, "labHeader", ocpLabels("New object"));
		else 
			xml_setAttribute($node, "labHeader", ocpLabels("Edit object"));
		xml_setAttribute($node, "labSave", ocpLabels("Save"));
		xml_setAttribute($node, "labCancel", ocpLabels("Cancel"));
		xml_setAttribute($node, "labGroups", ocpLabels("GROUPS"));
		xml_setAttribute($node, "labGeneral", ocpLabels("General"));

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

	function subforms_appendDisplayLabels($node){
		xml_setAttribute($node, "labHeader", ocpLabels("Display object"));
		xml_setAttribute($node, "labConfirm", ocpLabels("Confirm"));

		xml_setAttribute($node, "labYes", ocpLabels("Yes"));
		xml_setAttribute($node, "labNo", ocpLabels("No"));
	}

	function subforms_getNavigationChain($baseLink, $rowCount, $numbOnPage, $current){
		$navigationChain = "";
		$numbOfPages = ceil($rowCount/$numbOnPage) - 1;
		$needCorrect = false;
		$startOffset = 0;
		$endOffset = $numbOfPages;

		// how many offsets on left and right side of selected
		$sideVisible = 10;
		
		//integer values of given variables
		$rowCount = intval($rowCount);
		$numbOnPage = intval($numbOnPage);
		$current = intval($current);
		
		//findout startOffset and endOffset
		if (($current - $sideVisible) > 0) 
			$startOffset = $current - $sideVisible;
		else {
			$needCorrect = true;
		}
			
		if ((intval($current) + $sideVisible) <= $endOffset){ 
			$endOffset = intval($current) + $sideVisible;
			if ($needCorrect){
				$endOffset += ($sideVisible - $current);
				if ($endOffset > $numbOfPages)
					$endOffset = $numbOfPages;
			}
		} else{
			$startOffset -= ($current+$sideVisible) - $endOffset;
			if ($startOffset < 0) $startOffset = 0;
		}
		
		//now build navigation chain
		for ($i=$startOffset; $i <= $endOffset; $i++){
			if ($i != $current){
				$navigationChain .="<a href='".$baseLink.$i.");' class='ocp_link'>".($i+1)."</a>&nbsp;";
			} else {
				$navigationChain .= "&nbsp;".($i + 1)."&nbsp;";
			}
		}

		$ffw = "&gt;&gt;"; 
		$rrw = "&lt;&lt;"; 
		
		if (strpos($navigationChain, "a href") == false){ 
			$navigationChain = "";
		} else {
			$left = "";
			$right = "";
			if ($current < $numbOfPages){
				if ($endOffset < $numbOfPages){
					$right .= "&nbsp;<a href='".$baseLink.($endOffset+1).")' class='ocp_link'>".$ffw."</a>";
				}
			}
			if ($current > 0){
				if ($startOffset > 0){
					$left = "<a href='".$baseLink.($startOffset-1).")' class='ocp_link'>".$rrw."</a>&nbsp;".$left;
				}
			}
			
			if ($startOffset > 0){
				$navigationChain = "<a href='".$baseLink."0)' class='ocp_link'>1</a>&nbsp;...&nbsp;".$navigationChain;
			}
		
			if ($endOffset < $numbOfPages){
				$navigationChain .= "&nbsp;...&nbsp;<a href='".$baseLink."$numbOfPages)' class='ocp_link'>$numbOfPages</a>";
			}
			
			$navigationChain = $left.$navigationChain.$right;
		}
		
		return $navigationChain;
}
?></body>
</html>