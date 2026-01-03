<?php
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/session.php");
?>

<html>
<head>
<title>OCP</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="/ocp/css/home.css" rel="stylesheet" type="text/css">
<link href="/ocp/css/opsti.css" rel="stylesheet" type="text/css">
<link href="/ocp/css/opcije.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="/ocp/jscript/swfobject.js"></script>
<script src="/ocp/jscript/flash_scroll.js"></script>
<script>
	var rightsArr = new Array();
	function openMenuInLowerFrame(type, id, typeR, idR, right, dubina, maxdubina, brojDeceR, jsName){
		if (typeR == null) typeR = "";
		if (dubina == null) dubina = "";
		if (maxdubina == null) maxdubina = "";
		var close = false;

		var ocpTreeFrame = opener.top.top.frames.leftFrame;

		if ((jsName != null) && (jsName == "obj")){
			if (rightsArr[id.substring(6)] < 3){
				alert("<?php echo ocpLabels("You don\'t have sufficient privileges for this operation");?>");
			} else {
				ocpTreeFrame.openMenuInLowerFrame1("obj", "/ocp/objectManager/menu_frame.php?<?php echo utils_randomQS();?>&" + id + "&objId=-1");
				close = true;
			}
		} else {
			if (type == "Sekcija"){
				if (rightsArr[id] < 3){
					alert("<?php echo ocpLabels("You don\'t have sufficient privileges for this operation");?>");
				} else {
					ocpTreeFrame.openMenuInLowerFrame1("", "/ocp/siteManager/menu_frame.php?<?php echo utils_randomQS();?>&ocpType=" + type + "&ocpId=" + id + "&ocpTypeR=" + typeR + "&ocpIdR=" + idR + "&ocpPravo=" + right + "&ocpDubina=" + dubina + "&ocpMaxDubina=" + maxdubina + "&action=newPage");
					close = true;
				}
			} else {
				if (rightsArr[id] < 3){
					alert("<?php echo ocpLabels("You don\'t have sufficient privileges for this operation");?>");
				} else {
					ocpTreeFrame.openMenuInLowerFrame1("", "/ocp/siteManager/menu_frame.php?<?php echo utils_randomQS();?>&ocpType=" + type + "&ocpId=" + id + "&ocpTypeR=" + typeR + "&ocpIdR=" + idR + "&ocpPravo=" + right + "&ocpDubina=" + dubina + "&ocpMaxDubina=" + maxdubina + "&action=newBlock");
					close = true;
				}
			}
		}
		if (close)
			this.close();
	}
</script>
</head>
<body class="ocp_body"><?php
	$type = utils_requestStr(getGVar("type"));

	switch ($type){
		case "block":
			$PR = getSvar('ocpPR');
			$pageKeys = array_keys($PR);
			?><script><?php
			foreach ($pageKeys as $i){
				if (utils_valid($i)){
			?>rightsArr["<?php echo $i;?>"] = <?php echo $PR[$i];?>;<?php
				}
			} 
			?></script><?php

			$swfArgs = "treeSource=/ocp/siteManager/tree_data.php";
			$swfArgs .= "&dragDisallowed=1&menuDisallowed=1";
			$swfArgs .= "&versionClickDisallowed=1&sectionClickDisallowed=1&pageClickDisallowed=0";
	?><div id="ocp_blok_menu_1"> 
	<table class="ocp_blokovi_table"> 
		<tr> 
			<td class="ocp_blokovi_td"><img src="/ocp/img/opsti/razno/hint.gif" style="vertical-align: bottom; margin-left:3px;"> <?php echo ocpLabels("Choose page where you want to add block");?></td> 
		</tr> 
	</table> 
</div><table border="0" cellspacing="0" cellpadding="0" width="100%" height="90%">
	<tr>
		<td width="100%" height="100%">
			<div id="flashcontent" style="height:100%"></div>
			<script type="text/javascript">
			   var so = new SWFObject("/ocp/flash/tree.swf?<?php echo $swfArgs?>", "treeFlash", "100%", "100%", "6", "#ffffff", "", "onmousewheel=\"scrollFlash(this)\" onMouseOver=\"enableScroll(this)\" onfocus=\"enableScroll(this)\" onblur=\"disableScroll(this)\"");
			   so.write("flashcontent");
			</script><?php
			break;
		case "page":
			$SR = getSvar('ocpSR');
			$sectionKeys = array_keys($SR);
			?><script><?php
			foreach ($sectionKeys as $i){
				if (utils_valid($i)){
			?>rightsArr["<?php echo $i;?>"] = <?php echo $SR[$i];?>;<?php
				}
			} 
			?></script><?php

			$swfArgs = "treeSource=/ocp/siteManager/copy_data.php";
			$swfArgs .= "&dragDisallowed=1&menuDisallowed=1";
			$swfArgs .= "&versionClickDisallowed=1&sectionClickDisallowed=0&pageClickDisallowed=1";
	?><div id="ocp_blok_menu_1"> 
	<table class="ocp_blokovi_table"> 
		<tr> 
			<td class="ocp_blokovi_td"><img src="/ocp/img/opsti/razno/hint.gif" style="vertical-align: bottom; margin-left:3px;"> <?php echo ocpLabels("Choose section where you want to add page");?></td> 
		</tr> 
	</table> 
</div><table border="0" cellspacing="0" cellpadding="0" width="100%" height="90%">
	<tr>
		<td width="100%" height="100%">
			<div id="flashcontent" style="height:100%"></div>
			<script type="text/javascript">
			   var so = new SWFObject("/ocp/flash/tree.swf?<?php echo $swfArgs?>", "treeFlash", "100%", "100%", "6", "#ffffff", "", "onmousewheel=\"scrollFlash(this)\" onMouseOver=\"enableScroll(this)\" onfocus=\"enableScroll(this)\" onblur=\"disableScroll(this)\"");
			   so.write("flashcontent");
			</script><?php
			break;
		case "object":
			$TR = getSvar('ocpTR');
			$typeKeys = array_keys($TR);
			?><script><?php
			foreach ($typeKeys as $i){
				if (utils_valid($i)){
			?>rightsArr["<?php echo $i;?>"] = <?php echo $TR[$i];?>;<?php
				}
			} 
			?></script><?php

			$swfArgs = "treeSource=/ocp/objectManager/tree_data.php";
			$swfArgs .= "&dragDisallowed=1&menuDisallowed=1&rootClickDisallowed=1&versionClickDisallowed=1";
			$swfArgs .= "&sectionClickDisallowed=0&pageClickDisallowed=1&jsName=obj";
	?><div id="ocp_blok_menu_1"> 
	<table class="ocp_blokovi_table"> 
		<tr> 
			<td class="ocp_blokovi_td"><img src="/ocp/img/opsti/razno/hint.gif" style="vertical-align: bottom; margin-left:3px;"> <?php echo ocpLabels("Choose object type");?></td> 
		</tr> 
	</table> 
</div><table border="0" cellspacing="0" cellpadding="0" width="100%" height="90%">
	<tr>
		<td width="100%" height="100%">
			<div id="flashcontent" style="height:100%"></div>
				<script type="text/javascript">
			   var so = new SWFObject("/ocp/flash/tree.swf?<?php echo $swfArgs?>", "objFlash", "100%", "100%", "6", "#ffffff", "", "onmousewheel=\"scrollFlash(this)\" onMouseOver=\"enableScroll(this)\" onfocus=\"enableScroll(this)\" onblur=\"disableScroll(this)\"");
			   so.write("flashcontent");
			</script><?php
			break;
	}
		?></td>
	</tr>
</table></body>
</html>
