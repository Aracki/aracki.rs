<?php 
	require_once("../../include/connect.php");
	require_once("../../include/session.php");

	$root = utils_requestStr(getGVar("root"));

	if ($root != "/"){
		//ako mi je putanja /upload/images/ pretvaram je u /upload/images
		if (strrpos($root, "/") == (strlen($root)-1)) $root = substr($root, 0, strlen($root)-1);
	} else $root = "/upload";


	$field = utils_requestStr(getGVar("field"));
	$basicFolder = utils_requestStr(getGVar("basicFolder"));

	if (utils_valid($basicFolder)) {
		$basicFolder .= "/";
		$frameSrc = "preview.php?".utils_randomQS()."&root=" . $root . "&field=" . $field . "&putanja=" . $basicFolder;
	} else {
		$frameSrc = "/ocp/html/blank.html";
	}
?><html>
<head>
<title>OCP</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script>
		var IE = (navigator.userAgent.indexOf("Firefox") == -1) ? true : false;
		
		var frameBorder = (IE) ? "no" : "yes";
		var frameMargin = (IE) ? ' TOPMARGIN="0" LEFTMARGIN="0" MARGINHEIGHT="0" MARGINWIDTH="0" ' : '';
		var frameSpacing1 = (IE) ? ' framespacing="0" ' : '';
		var frameSpacing2 = (IE) ? ' framespacing="5" ' : '';

		var framesetStr = '<frameset rows="30,*" id="fileFrameset" name="fileFrameset" frameborder="no" '+frameMargin+' '+frameSpacing1+'>';
		framesetStr += '<frame src="title_frame.php?<?php echo utils_randomQS()?>" name="titleFrame" scrolling="NO" noresize>';
		
		framesetStr += '<frameset rows="*" id="framesetLeftInner" name="framesetLeftInner" border="5" BORDERCOLOR="#7B7B7B" frameborder="yes" '+frameSpacing2+'>';
		framesetStr += '<frame src="left.php?<?php echo utils_randomQS()?>&root=<?php echo $root?>&field=<?php echo $field?>&basicFolder=<?php echo $basicFolder?>" id="treeFrame" name="treeFrame" FRAMEBORDER="'+frameBorder+'" BORDER="5" scrolling="NO">';
//		framesetStr += '<frame src="<?php echo $frameSrc?>" id="previewFrame" name="previewFrame" FRAMEBORDER="'+frameBorder+'"  scrolling="auto" BORDER="5">';
		framesetStr += '</frameset><noframes></noframes>';

		framesetStr += '</frameset><noframes></noframes>';
	</script>
</head>
<script>
	document.writeln(framesetStr);
</script>
</html>
