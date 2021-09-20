<?php
require_once("../../include/session.php");
?>
<html>
<head>
<title>OCP</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="/ocp/css/opsti.css" rel="stylesheet" type="text/css">
<link href="/ocp/css/opcije.css" rel="stylesheet" type="text/css">
<script src="/ocp/jscript/flash_scroll.js"></script>
<script language="javascript" type="text/javascript" src="/ocp/jscript/swfobject.js"></script>
<?php
	$straId = utils_requestStr(getGVar("straId"));
?><script>
	function openMenuInLowerFrame(type, id, typeR, idR, right, dubina, maxdubina, brojDeceR, jsName){
		opener.document.formObject.stra<?php echo $straId;?>path.value = id;
		this.close();
	}
</script>
</head>
<body class="ocp_body"><?php
	$swfArgs = "treeSource=/ocp/siteManager/copy_data.php";
	$swfArgs .= "&dragDisallowed=1&menuDisallowed=1";
	$swfArgs .= "&versionClickDisallowed=1&sectionClickDisallowed=0&pageClickDisallowed=1";
	?><div id="ocp_blok_menu_1"> 
	<table class="ocp_blokovi_table"> 
		<tr> 
			<td class="ocp_blokovi_td"><img src="/ocp/img/opsti/razno/hint.gif" style="vertical-align: bottom; margin-left:3px;"> <?php echo ocpLabels("Choose section where you want to move page");?></td> 
		</tr> 
	</table> 
</div><table border="0" cellspacing="0" cellpadding="0" width="100%" height="90%">
	<tr>
		<td width="100%">
			<div id="recycle_popup" style="height:100%"></div>
			<script type="text/javascript">
			   var so = new SWFObject("/ocp/flash/tree.swf?<?php echo $swfArgs ?>", "treeFlash", "100%", "100%", "6", "#ffffff", "", "onmousewheel=\"scrollFlash(this)\" onMouseOver=\"enableScroll(this)\" onfocus=\"enableScroll(this)\" onblur=\"disableScroll(this)\"");
			   so.write("recycle_popup");
			</script></td>
	</tr>
</table></body>
</html>
