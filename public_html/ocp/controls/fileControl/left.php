<?php 
	require_once("../../include/session.php");

	$root = utils_requestStr(getGVar("root"));	
	$field = utils_requestStr(getGVar("field"));
	$sirina = utils_requestStr(getGVar("sirina"));
	$visina = utils_requestStr(getGVar("visina"));
	$max = utils_requestInt(getGVar("max"));
	$basicFolder = utils_requestStr(getGVar("basicFolder"));

	$parameters = "root=" . $root . "&basicFolder=" . $basicFolder . "&field=" . $field . "&sirina=" . $sirina . "&visina=" . $visina . "&max=" . $max;
?><HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/ocp/css/opsti.css">
<link rel="stylesheet" href="/ocp/css/opcije.css">
<script language="javascript" type="text/javascript" src="/ocp/jscript/swfobject.js"></script>
<SCRIPT>
	function openMenuInLowerFrame(type, id, typeR, idR, right, dubina, maxdubina, brojDeceR){
		window.open("/ocp/controls/fileControl/menu_frame.php?<?php echo utils_randomQS();?>&<?php echo $parameters?>&putanja=" + id, "menuFrame");
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