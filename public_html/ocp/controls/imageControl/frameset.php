<?php 
	require_once("../../include/session.php");

	$root = utils_requestStr(getGVar("root"));

	if ($root != "/"){
		//ako mi je putanja /upload/images/ pretvaram je u /upload/images
		if (strrpos($root, "/") == (strlen($root)-1)) $root = substr($root, 0, strlen($root)-1);
	} else $root = "/upload";


	$type = utils_requestStr(getGVar("objType"));
        $field = utils_requestStr(getGVar("field"));
	$sirina = utils_requestStr(getGVar("width"));
	$visina = utils_requestStr(getGVar("height"));
	$max = utils_requestInt(getGVar("max"));

	$basicFolder = utils_requestStr(getGVar("basicFolder"));
	$fileName = substr($basicFolder, strrpos($basicFolder, "/"));
	$basicFolder = substr($basicFolder, 0, strrpos($basicFolder, "/")+1);
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
		framesetStr += '<frame src="title_frame.php?type=<?php echo $type;?>&<?php echo utils_randomQS()?>" name="titleFrame" scrolling="NO" noresize>';
		framesetStr += '<frameset rows="*" cols="25%,75%" id="downFrameset" name="downFrameset" border="5" BORDERCOLOR="#7B7B7B" frameborder="yes" '+frameSpacing2+'>';
		framesetStr += '<frame src="frameset_left.php?<?php echo utils_randomQS()?>&root=<?php echo $root?>&field=<?php echo $field?>&basicFolder=<?php echo $basicFolder?>&objType=<?php echo $type?>&fileName=<?php echo $fileName?>&sirina=<?php echo $sirina?>&visina=<?php echo $visina?>&max=<?php echo $max?>" id="leftFrameset" name="leftFrameset" FRAMEBORDER="'+frameBorder+'" BORDER="5" scrolling="NO">';
		framesetStr += '<frame src="frameset_right.php?<?php echo utils_randomQS()?>&basicFolder=<?php echo $basicFolder?>&field=<?php echo $field?>&sirina=<?php echo $sirina?>&objType=<?php echo $type?>&visina=<?php echo $visina?>&max=<?php echo $max?>&root=<?php echo $root?>" id="rightFrameset" name="rightFrameset" FRAMEBORDER="'+frameBorder+'" BORDER="5">';
		framesetStr += '</frameset>';
		framesetStr += '</frameset><noframes></noframes>';
	</script>
</head>
<script>
	document.writeln(framesetStr);
</script>
</html>
