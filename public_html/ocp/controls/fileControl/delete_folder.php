<?php

	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	
	$webRoot = realpath("../../.."); //root web site-a

	$folderName = utils_requestStr(getGVar("folderName")); 
	$message = "";
	if (utils_valid($folderName)){
		rmdir($webRoot ."/". $folderName);
		$message = ocpLabels("Folder is deleted");
	}
	
?>
<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/ocp/css/opsti.css">
<link rel="stylesheet" href="/ocp/css/opcije.css">
</HEAD>
<BODY class="ocp_body">
	<?php 
	if ($message != "") { ?>
		<script>
			window.open("/ocp/html/blank.html", "menuFrame");
			window.open("/ocp/html/blank.html", "listFrame");
			window.open("/ocp/html/blank.html", "previewFrame");
			top.top.frames.leftFrameset.frames.treeFrame.refreshTree();
		</script><?php 
		require_once("../../include/design/message.php");
		echo( message_info($message));
	} 
?></BODY>
</HTML>