<?php 
	require_once("../../include/connect.php");
	require_once("../../include/session.php");

	$root = utils_requestStr(getGVar("root"));	
	$field = utils_requestStr(getGVar("field"));
	$basicFolder = utils_requestStr(getGVar("basicFolder"));

	$parameters = "root=" . $root . "&basicFolder=" . $basicFolder . "&field=" . $field;
?><HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/ocp/css/opsti.css">
<link rel="stylesheet" href="/ocp/css/opcije.css">
<script language="javascript" type="text/javascript" src="/ocp/jscript/swfobject.js"></script>
<SCRIPT>
	var field = "<?php echo $field?>";

	function openMenuInLowerFrame(type, id, typeR, idR, right, dubina, maxdubina, brojDeceR){
//		window.open("/ocp/controls/folderControl/preview.php?<?php echo utils_randomQS();?>&<?php echo $parameters?>&putanja=" + id, "previewFrame");
		var fileName = id;
		fileName = fileName.substring(0, fileName.length-1);
		var inputField = field.substring(field.indexOf('.')+1);
		inputField = inputField.split(".");

		if (top.opener && field != "undefined"){
			//// var x = eval("top.opener.document.forms['"+field.substring(0, field.indexOf('.')) + "'].elements['" + field.substring(field.indexOf('.') + 1)+"']");
			//var x = eval("top.opener.document.forms['"+inputField[1]+"'].elements['"+inputField[1]+"']");???
			var x = eval("top.opener.document.forms[0].elements['"+inputField[1]+"']");
			x.value = fileName;
			top.close();
		} else {
			top.returnValue = fileName;
			top.close();

		}
		return true;
	}
	function refreshTree(){
		window.document.treeFlash.TGotoLabel('/', 'restart');
		window.document.treeFlash.Play();
	}
</SCRIPT>
</HEAD>
<BODY class="ocp_body"><?php
	$swfArgs = "menuDisallowed=1&versionClickDisallowed=0&sectionClickDisallowed=0&pageClickDisallowed=1";
	$swfArgs .= "&treeSource=/ocp/controls/fileControl/left_data.php&treeFilter=".$root.",".$basicFolder;
?><div id="file" style="height:100%"></div>
<script type="text/javascript">
   var so = new SWFObject("/ocp/flash/tree.swf?<?php echo $swfArgs?>", "treeFlash", "100%", "100%", "6", "#ffffff");
   so.write("file");
</script></BODY>
</HTML>