<?php
	require_once("../../../include/session.php");

	$strHref = "";
	$tempArray =$_GET["StBl_Id"];
	for ($i=0; $i<count($tempArray); $i++)
		$strHref .= "&StBl_Id[]=".$tempArray[$i]; 

	$swfArgs = "treeSource=/ocp/siteManager/tree_data.php&treeFilter=undefined";
	$swfArgs .= "&dragDisallowed=1&menuDisallowed=1";
	$swfArgs .= "&versionClickDisallowed=1&sectionClickDisallowed=1&pageClickDisallowed=0";
?><HTML>
<HEAD>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="/ocp/css/opcije.css">
	<script src="/ocp/jscript/flash_scroll.js"></script>
	<script language="javascript" type="text/javascript" src="/ocp/jscript/swfobject.js"></script>
	<script>
		function openMenuInLowerFrame(type, id){
			if (type == "Stranica")
				window.open("/ocp/siteManager/lib/move/move.php?<?php echo utils_randomQS();?>&Stra_Id="+id+"<?php echo $strHref;?>", "move2");
		}	
	</script>
</HEAD>
<BODY leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="document.body.style.overflowX = 'hidden' "><div id="ocp_blok_menu_1"><table class="ocp_blokovi_table"> 
			<tr><td class="ocp_blokovi_td"><img src="/ocp/img/opsti/razno/hint.gif" style="vertical-align: bottom; margin-left:3px;"> <b>2/3</b> <?php echo ocpLabels("Choose page where you want move/copy");?></td></tr> 
		</table> 
	</div><table border="0" cellspacing="0" cellpadding="0" width="100%" height="90%">
	<tr>
		<td width="100%" valign="top">
			<div id="tree" style="height:100%"></div>
			<script type="text/javascript">
			   var so = new SWFObject("/ocp/flash/tree.swf?<?php echo $swfArgs?>", "treeFlash", "100%", "100%", "6", "#ffffff", "", "onmousewheel=\"scrollFlash(this)\" onMouseOver=\"enableScroll(this)\" onfocus=\"enableScroll(this)\" onblur=\"disableScroll(this)\"");
			   so.write("tree");
			</script></td>
	</tr>
</table></BODY>
</HTML>
