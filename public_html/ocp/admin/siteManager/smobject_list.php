<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");

?><HTML>
<HEAD>
<TITLE> Ocp </TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/ocp/css/opcije.css">
<script language="javascript" type="text/javascript" src="/ocp/jscript/swfobject.js"></script>
</HEAD><?php  
//	$StraId = utils_requestInt(getGVar("Id"));
	$type = utils_requestStr(getGVar("type"));
	$field = utils_requestStr(getGVar("field"));

	$swfArgs = "treeSource=/ocp/siteManager/smlist_tree_data.php&treeFilter=".$type;
	$swfArgs .= "&dragDisallowed=1&menuDisallowed=1";
	if ($type == "verzija"){
		$swfArgs .= "&versionClickDisallowed=0&sectionClickDisallowed=1&pageClickDisallowed=1";	
	} else if ($type == "sekcija"){
		$swfArgs .= "&versionClickDisallowed=1&sectionClickDisallowed=0&pageClickDisallowed=1";
	} else {
		$swfArgs .= "&versionClickDisallowed=1&sectionClickDisallowed=1&pageClickDisallowed=0";
	}
	
?><BODY leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="document.body.style.overflowX = 'hidden'; self.focus();">
<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
	<tr>
		<td width="100%" valign="top">
			<div id="intlink" style="height:100%"></div>
			<script type="text/javascript">
			   var so = new SWFObject("/ocp/flash/tree.swf?<?php echo $swfArgs?>", "treeFlash", "100%", "100%", "6", "#ffffff");
			   so.write("intlink");
			</script>
		</td>
	</tr>
</table>
<script>
	var field = "<?php echo $field?>";
	function openMenuInLowerFrame(type, id){
		var inputField = field.substring(field.indexOf('.')+1);
		inputField = inputField.split(".");

		if (field != "undefined"){
			var x = eval("top.opener.document.forms['"+inputField[0]+"'].elements['"+inputField[1]+"']");
			if (x) {
				x.value = id;
				eval("opener.update"+inputField[1]+"_label(id)");
				window.close();
			}
		}
	}
</script>
</Body>
</html>