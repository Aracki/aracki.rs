<?php
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/session.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/connect.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/izvestaji.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/siteManager/lib/root.php");
?>
<html>
<head>
<title>OCP levi frame</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="/ocp/css/levi.css" rel="stylesheet" type="text/css">
<script src="/ocp/jscript/flash_scroll.js"></script>
<script language="javascript" type="text/javascript" src="/ocp/jscript/swfobject.js"></script>
<script language="JavaScript" type="text/JavaScript">
	
	var currTdCase = "treeTd";

	var IE = (navigator.userAgent.indexOf("Firefox") == -1) ? true : false;

	function openMenuInLowerFrame(type, id, typeR, idR, right, dubina, maxdubina, brojDeceR, jsName){
//		alert(type+" "+id+" "+typeR+" "+idR);
		
		if (typeR == null) typeR = "";
		if (dubina == null) dubina = "";
		if (maxdubina == null) maxdubina = "";

		jsName = (jsName == null) ? "site" : jsName;

		if (jsName == "obj"){
			if (currTdCase != "objTd")
				alternateModules("objTd", "/ocp/objectManager/menu_frame.php?<?php echo utils_randomQS();?>&" + id);
			else
				window.open("/ocp/objectManager/menu_frame.php?<?php echo utils_randomQS();?>&" + id, "menuFrame");
		} else if (jsName == "site"){
			if (currTdCase != "treeTd") {
				alternateModules("treeTd", "/ocp/siteManager/menu_frame.php?<?php echo utils_randomQS();?>&ocpType=" + type + "&ocpId=" + id + "&ocpTypeR=" + typeR + "&ocpIdR=" + idR + "&ocpPravo=" + right + "&ocpDubina=" + dubina + "&ocpMaxDubina=" + maxdubina);
			} else {
				window.open("/ocp/siteManager/menu_frame.php?<?php echo utils_randomQS();?>&ocpType=" + type + "&ocpId=" + id + "&ocpTypeR=" + typeR + "&ocpIdR=" + idR + "&ocpPravo=" + right + "&ocpDubina=" + dubina + "&ocpMaxDubina=" + maxdubina, "menuFrame");
			}
		} else if (jsName == "reports"){
			if (currTdCase != "reportTd")
				alternateModules("reportTd", "/ocp/reports/menu_frame.php?<?php echo utils_randomQS();?>&" + id);
			else
				window.open("/ocp/reports/menu_frame.php?<?php echo utils_randomQS();?>&" + id, "menuFrame");
		}
	}

	function openMenuInLowerFrame1(jsName, link){
		jsName = (jsName == null) ? "site" : jsName;

		if (jsName == "obj"){
			if (currTdCase != "objTd") 
				alternateModules("objTd", link);
			 else 
				window.open(link, "menuFrame");
		} else if (jsName == "site") {
			if (currTdCase != "treeTd") 
				alternateModules("treeTd", link);
			else 
				window.open(link, "menuFrame");
		} else if (jsName == "reports") {
			if (currTdCase != "reportTd") 
				alternateModules("reportTd", link);
			else 
				window.open(link, "menuFrame");
		} 
	}

	function refreshTree(){
		// replace document.getElementById("treeFlash"). -> window.document.treeFlash.
		// in EMBED tag replace id -> name
		window.document.treeFlash.TGotoLabel('/', 'restart');
		window.document.treeFlash.Play();
	}

	function changeSiteName(rootName){
		document.getElementById("siteName").innerHTML = rootName;
	}
	
	function openOption(urlTitle){
		switch (urlTitle){
			case "upload": openPopupWindow("/ocp/fileUpload/browser.php?<?php echo utils_randomQS();?>", "fileUpload", 600, 400); break;
			case "help": openPopupWindow("/ocp/help/frameset.php?<?php echo utils_randomQS();?>", "help", 600, 400); break;
		}
	}

	function openPopupWindow(url, name, width, height) { 
		w = 800; h = 400;
		if (width != null){ w = width;}
		if (height != null){ h = height;}
		var x = window.open(url, name, "top=10, left=50, width=" + w + ", height=" + h + ", scrollbars=yes, resizable=yes, status=yes");
		x.focus();
	}

	function alternateModules(tdCase, link){
		switch (tdCase){
			case "objTd":	
				if (document.getElementById('objTd').style.height != '100%'){
					with(document.getElementById('objTd').style){
						display = (IE) ? 'block' : 'table-row';
						visibility = 'visible';
						height = '100%';
					}
					document.getElementById('objButton').className = "ocp_veliko_dugme_izabrano";
					var href = "/ocp/right_frameset.php?<?php echo utils_randomQS();?>&module=objectManager";
					if (link) href += "&href=" + escape(link);
					parent.rightFrame.location.href = href;
					
					if (document.getElementById('treeTd')){
						with(document.getElementById('treeTd').style){
							display = 'none';
							height = '0%';
							visibility = 'hidden';
						}
						document.getElementById('queryTd').style.display = "none";
						document.getElementById('treeButton').className = "ocp_veliko_dugme";
					}
					
					if (document.getElementById('reportTd')){
						with(document.getElementById('reportTd').style){
							display = 'none';
							height = '0%';
							visibility = 'hidden';
						}
						document.getElementById('reportButton').className = "ocp_veliko_dugme";
					}

					currTdCase = "objTd";
					if (link) window.open(link, "menuFrame");
				}
				break;
			case "treeTd":	
				if (document.getElementById('treeTd').style.height != '100%'){
					if (document.getElementById('objTd')){
						with(document.getElementById('objTd').style){
							display = 'none';
							visibility = 'hidden';
							height = '0%';
						}
						document.getElementById('objButton').className = "ocp_veliko_dugme";
					}

					with(document.getElementById('treeTd').style){
						display = (IE) ? 'block' : 'table-row';
						visibility = 'visible';
						height = '100%';
					}
					document.getElementById('treeButton').className = "ocp_veliko_dugme_izabrano";
					document.getElementById('queryTd').style.display = (IE) ? 'block' : 'table-row';
					var href = "/ocp/right_frameset.php?<?php echo utils_randomQS();?>&module=siteManager";
					if (link) href += "&href=" + escape(link);
					parent.rightFrame.location.href = href;
					currTdCase = "treeTd";
					
					if (document.getElementById('reportTd')){
						with(document.getElementById('reportTd').style){
							display = 'none';
							height = '0%';
							visibility = 'hidden';
						}
						document.getElementById('reportButton').className = "ocp_veliko_dugme";
					}
				}
				break;
			case "reportTd":	
				if (document.getElementById('reportTd').style.height != '100%'){
					if (document.getElementById('objTd')){
						with(document.getElementById('objTd').style){
							display = 'none';
							visibility = 'hidden';
							height = '0%';
						}
						document.getElementById('objButton').className = "ocp_veliko_dugme";
					}
					
					if (document.getElementById('treeTd')){
						with(document.getElementById('treeTd').style){
							display = 'none';
							height = '0%';
							visibility = 'hidden';
						}
						document.getElementById('queryTd').style.display = "none";
						document.getElementById('treeButton').className = "ocp_veliko_dugme";
					}

					with(document.getElementById('reportTd').style){
						display = (IE) ? 'block' : 'table-row';
						visibility = 'visible';
						height = '100%';
					}
					document.getElementById('reportButton').className = "ocp_veliko_dugme_izabrano";
					var href = "/ocp/right_frameset.php?<?php echo utils_randomQS();?>&module=reports";
					if (link) href += "&href=" + escape(link);
					parent.rightFrame.location.href = href;

					currTdCase = "reportTd";
					if (link) window.open(link, "menuFrame");
				}
				break;
		}
	}

	function removeFilter(mode){
		var open = "";
		if (mode != null)
			open = "&Open=" + (mode == "obj" ? "objectManager" : "reports");
		this.location.href = "/ocp/ocpmenu_frame.php?<?php echo utils_randomQS();?>" + open;	
	}

	function openHomePage(){
		if (currTdCase == "treeTd"){
			parent.rightFrame.location.href = "/ocp/right_frameset.php?<?php echo utils_randomQS();?>&module=siteManager";
		} else if (currTdCase == "objTd"){
			parent.rightFrame.location.href = "/ocp/right_frameset.php?<?php echo utils_randomQS();?>&module=objectManager";
		} else if (currTdCase == "reportTd"){
			parent.rightFrame.location.href = "/ocp/right_frameset.php?<?php echo utils_randomQS();?>&module=reports";
		}
	}
</script>
<script src="/ocp/validate/validate_double_quotes.js"></script>
</head><?php
	$Filter = utils_requestStr(getGVar("Filter"));
	$Open = utils_requestStr(getGVar("Open"));

	$root_naziv = root_getProperty(1, "Root_Naziv");
	if (!(strpos($root_naziv,"www.") === false)) {
		$root_naziv = utils_substr($root_naziv, strpos($root_naziv,"www.") + 4);
	}
	$izvestaji = izv_getAll4User();

	$disable = getSVar("ocpDisable");

?><body class="ocp_levi_body" <?php if ($Open == "objectManager") { ?> onload="alternateModules('objTd');" <?php } else if ($Open == "reports"){?>onload="alternateModules('reportTd');"<?php } ?>>
<table class="ocp_levi_table">
<tr>
    <td style="padding: 0px"><table class="ocp_levi_logo_table"><tr><td class="ocp_levi_logo_td"><img src="/ocp/img/levi/logo/logo.gif" style="margin-left:2px;cursor:pointer;" onclick="openHomePage();" title="<?php echo ocpLabels("Home");?>"></td></tr></table></td>
  </tr>
  <tr>
    <td class="ocp_levi_naziv_sajta" id="siteName"><?php echo $root_naziv;?></td>
  </tr><?php
  if (substr_count($disable, "siteManager") == 0){
  ?><tr>
    <td class="ocp_veliko_dugme_izabrano" id="treeButton" style="" onclick="alternateModules('treeTd');"><?php echo ocpLabels("Site Manager");?></td>
  </tr>
  <tr style="height:100%;visibility:visible;display:table-row;" id="treeTd">
    <td class="ocp_levi_td"><?php
		$swfArgs0 = "treeSource=/ocp/siteManager/tree_data.php&treeLib=/ocp/siteManager/treelib.php";
		$swfArgs0 .= "&menuDisallowed=0&treeFilter=".$Filter."&jsName=site";
	?><div id="ocpmenu_frame" style="height:100%;"></div>
		<script type="text/javascript">
		   var so = new SWFObject("/ocp/flash/tree.swf?<?php echo $swfArgs0?>", "treeFlash", "100%", "100%", "6", "#ffffff", "", "onmousewheel=\"scrollFlash(this)\" onMouseOver=\"enableScroll(this)\" onfocus=\"enableScroll(this)\" onblur=\"disableScroll(this)\"");
		   so.write("ocpmenu_frame");
		</script></td>
  </tr>
  <tr style="display:table-row" id="queryTd">
    <td class="ocp_pretraga">
	<table class="ocp_uni_table">
		<form action="/ocp/ocpmenu_frame.php?<?php echo utils_randomQS();?>" name="formOcpQuery" method="get" onSubmit="validate_double_quotes(formOcpQuery);return true;">
      <tr>
		<?php if (utils_valid($Filter)){ ?><td style="width:20px" style="display:block"><a href="javascript:removeFilter();"><img src="/ocp/img/opsti/kontrole/kontrola_ukini.gif" width="20" height="21" class="ocp_kontrola" title="<?php echo ocpLabels("Remove filter");?>"></a></td><?php	} ?>
        <td class="ocp_dugmici_td_levi"><input type="text" class="ocp_pretraga_input<?php if (utils_valid($Filter)) echo("_on")?>" style="width:100%" name="Filter" value="<?php if (utils_valid($Filter)){ echo($Filter); } else { echo(ocpLabels("Query")); } ?>"></td>
        <td style="width:22px"><input type="image" src="/ocp/img/opsti/kontrole/kontrola_pretraga.gif" class="ocp_kontrola" title="<?php echo ocpLabels("Submit query");?>"></a></td>
      </tr>
		</form>
    </table>
	</td>
  </tr><?php
	}
	
	if (substr_count($disable, "objectManager") == 0){	
?> <tr>
    <td class="ocp_veliko_dugme" id="objButton" onclick="<?php if (!utils_valid($Filter)){	?>alternateModules('objTd');<?php } else { ?> removeFilter(true);<?php } ?>"><?php echo ocpLabels("Object Manager");?></td>
  </tr>
 <tr style="height:0%;visibility:hidden;display:none;" id="objTd">
    <td class="ocp_levi_td"><?php
		$swfArgs = "treeSource=/ocp/objectManager/tree_data.php";
		$swfArgs .= "&dragDisallowed=1&menuDisallowed=1";
		$swfArgs .= "&rootClickDisallowed=1&versionClickDisallowed=1";
		$swfArgs .= "&sectionClickDisallowed=0&pageClickDisallowed=1&jsName=obj";
	?><div id="ocpmenu_frame2" style="height:100%;"></div>
		<script type="text/javascript">
		   var so = new SWFObject("/ocp/flash/tree.swf?<?php echo $swfArgs?>", "objFlash", "100%", "100%", "6", "#ffffff", "", "onmousewheel=\"scrollFlash(this)\" onfocus=\"enableScroll(this)\" onblur=\"disableScroll(this)\"");
		   so.write("ocpmenu_frame2");
		</script></td>
  </tr><?php
	}
 
    if (substr_count($disable, "reports") == 0 && count($izvestaji) > 0){
  ?>
  <tr>
    <td class="ocp_veliko_dugme" id="reportButton" onclick="<?php if (!utils_valid($Filter)){	?>alternateModules('reportTd');<?php }else {?>removeFilter('reports');<?php } ?>"><?php echo ocpLabels("Reports") ?></td>
  </tr>
  <tr style="height:0%;visibility:hidden;display:none;" id="reportTd">
    <td class="ocp_levi_td"><?php
		$swfArgs = "treeSource=/ocp/reports/tree_data.php";
		$swfArgs .= "&dragDisallowed=1&menuDisallowed=1";
		$swfArgs .= "&rootClickDisallowed=1&versionClickDisallowed=1";
		$swfArgs .= "&sectionClickDisallowed=0&pageClickDisallowed=1&jsName=reports";
	?><div id="ocpmenu_frame3" style="height:100%;"></div>
		<script type="text/javascript">
		   var so = new SWFObject("/ocp/flash/tree.swf?<?php echo $swfArgs ?>", "reportFlash", "100%", "100%", "6", "#ffffff", "", "onmousewheel=\"scrollFlash(this)\" onfocus=\"enableScroll(this)\" onblur=\"disableScroll(this)\"");
		   so.write("ocpmenu_frame3");
		</script></td>
  </tr><?php
  }

	if (getSVar("ocpUserGroup") == "null") { 
  ?><tr>
    <td class="ocp_veliko_dugme" onclick="openPopupWindow('/ocp/admin/frameset.php?<?php echo utils_randomQS();?>', 'admin', 900, 700);"><?php echo ocpLabels("Administration");?></td>
  </tr><?php
	}
 ?></table>
</body>
</html>
