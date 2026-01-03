<?php
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/session.php");
?>
<html>
<head>
<title>OCP levi frame</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script language="JavaScript" type="text/JavaScript">
	function openPopupWindow(url, name, width, height) { 
		w = 800; h = 400;
		if (width != null){ w = width;}
		if (height != null){ h = height;}
		var x = window.open(url, name, "top=100, left=50, width=" + w + ", height=" + h + ", scrollbars=yes, resizable=yes, status=yes");
		x.focus();
	}

	var userWidth = 0;
	function getUserWidth(){
		var cols = top.top.document.getElementById("leftFrameset").getAttribute("cols");
		if (cols.indexOf(",") > -1){
			var width = cols.substring(0, cols.indexOf(","));
			if (isNaN(width) || parseInt(width) > 300){
				width = 300;
			}
			return width;
		}

		return null;
	}

	function getHelpWidth(){
		var cols = top.top.document.getElementById("leftFrameset").getAttribute("cols");

		if (cols.indexOf(",") > -1)
			return cols.substring(cols.lastIndexOf(",")+1, cols.length);

		return null;
	}

	var userHeight = null;
	function getUserHeight(){
		if (parent.resizableFrameset != null){
			var rows = parent.resizableFrameset.getAttribute("rows");
			if (rows.indexOf(",") > -1)
				return rows.substring(0, rows.indexOf(","));
		}

		return null;
	}
	
	function openLeftFrameset(){
		if (getUserWidth() == "0"){
			var tdHtml = '<a href="javascript:closeLeftFrameset();"><img src="img/gornji_1/kontrole/dugme_zatvori_frame.gif" width="40" height="30" border="0" title="<?php echo strtr(ocpLabels("Close OCP\'s left frame"), "'", "\\'");?>">';

			var td = document.getElementById('openCloseButton');
			td.innerHTML = tdHtml;
			top.top.document.getElementById("leftFrameset").setAttribute('cols', userWidth + ',*,'+getHelpWidth());
		} else {
			closeLeftFrameset();
		}
	}

	function closeLeftFrameset(){
		userWidth = getUserWidth();
		var tdHtml = '<a href="javascript:openLeftFrameset();"><img src="img/gornji_1/kontrole/dugme_otvori_frame.gif" width="40" height="30" border="0" title="<?php echo strtr(ocpLabels("Close OCP\'s left frame"), "'", "\\'");?>">';
		var td = document.getElementById('openCloseButton');
		td.innerHTML = tdHtml;
		top.top.document.getElementById("leftFrameset").setAttribute('cols', '0,*,'+getHelpWidth());
	}

	function openHelpFrameset(){
		helpWidth = getHelpWidth();
		if (helpWidth > 0) {
			top.top.document.getElementById("leftFrameset").setAttribute('cols', getUserWidth() + ',*,0');
			document.getElementById("openHelpButton").innerHTML = '<a href="javascript:openHelpFrameset();"><img src="/ocp/img/gornji_1/kontrole/dugme_otvori_help.gif" style="border:0;" title="<?php echo ocpLabels("Help")?>"></a>';
		} else {
			top.top.document.getElementById("leftFrameset").setAttribute('cols', getUserWidth() + ',*,300');
			document.getElementById("openHelpButton").innerHTML = '<a href="javascript:openHelpFrameset();"><img src="/ocp/img/gornji_1/kontrole/dugme_zatvori_help.gif" style="border:0;" title="<?php echo ocpLabels("Help")?>"></a>';
		}
		
	}
</script>
<link href="/ocp/css/gornji.css" rel="stylesheet" type="text/css">
</head><?php
	$titleImg = "";
	$module = utils_requestStr(getGVar("module"));
	if ($module == "siteManager") 
		$titleImg = ocpLabels("Site Manager");
	else if ($module == "objectManager")  
		$titleImg = ocpLabels("Object Manager");
	else if ($module == "reports")
		$titleImg = ocpLabels("Reports");
?><body class="ocp_gornji_body">
<table class="ocp_gornji_table">
  <tr>
    <td class="ocp_gornji_td_levi" valign="top"><table cellpadding="0" cellspacing="0">
		<tr><td id="openCloseButton" name="openCloseButton"><a href="javascript:closeLeftFrameset();"><img src="img/gornji_1/kontrole/dugme_zatvori_frame.gif" width="40" height="30" border="0" title="<?php echo ocpLabels("Close OCP\'s left frame");?>"></a></td><td align="left" class="ocp_gornji_title"><?php echo $titleImg;?></td></tr></table>
	</td>
    <td class="ocp_gornji_td_desni">
	  <table class="ocp_gornji_table_desni">
        <tr>
          <td class="ocp_gornji_td_desni"><?php echo ocpLabels("username");?>: <span class="ocp_gornji_imeusera"><?php echo getSVar("ocpUsername");?></span> | <a href="javascript:openPopupWindow('/ocp/admin/users/user_settings.php?<?php echo utils_randomQS();?>','passwin', 350, 500);" class="ocp_gornji_td_link"><?php echo ocpLabels("settings");?></a><?php
		  
		  $disable = getSVar("ocpDisable");
		  
		  if (substr_count($disable, "siteManager") == 0 || substr_count($disable, "objectManager") == 0){
		  ?> | <a href="javascript:openPopupWindow('/ocp/controls/search_replace/frameset.php?<?php echo utils_randomQS()?>','searchReplace', 600, 500);" class="ocp_gornji_td_link"><?php echo ocpLabels("search & replace")?></a><?php
		  }
		  ?> | <a href="#" onclick="window.open('/ocp/admin/users/save_settings.php?<?php echo utils_randomQS();?>&width='+getUserWidth(), '_top');return false;" class="ocp_gornji_td_link"><?php echo ocpLabels("log out");?></a></td>
        </tr>
      </table>
	  </td>
	  <td class="ocp_gornji_td_help" id="openHelpButton">
	<a href="javascript:openHelpFrameset();"><img src="/ocp/img/gornji_1/kontrole/dugme_otvori_help.gif" style="border: 0;" title="<?php echo ocpLabels("Help");?>"></a>
	</td>
  </tr>
</table>
</body>
</html>