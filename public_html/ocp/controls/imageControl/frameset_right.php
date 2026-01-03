<?php 
	require_once("../../include/session.php");

	$type = utils_requestStr(getGVar("objType"));
	$root = utils_requestStr(getGVar("root"));	
	$field = utils_requestStr(getGVar("field"));
	$sirina = utils_requestStr(getGVar("sirina"));
	$visina = utils_requestStr(getGVar("visina"));
	$max = utils_requestInt(getGVar("max"));
	$basicFolder = utils_requestStr(getGVar("basicFolder"));

	if ($root != "/"){
		//ako mi je putanja /upload/images/ pretvaram je u /upload/images
		if (strrpos($root, "/") == (strlen($root)-1)) $root = substr($root, 0, strlen($root)-1);
	} else $root = "/upload";

	if (utils_valid($basicFolder)) {
		$frameSrc = "menu_frame.php?".utils_randomQS()."&listType=list&root=" . $root . "&field=" . $field . "&sirina=" . $sirina . "&visina=" . $visina . "&max=" . $max . "&putanja=" . $basicFolder . "&objType=" . $type;
	} else {
		$frameSrc = "menu_frame.php?".utils_randomQS()."&listType=list&root=" . $root . "&field=" . $field . "&sirina=" . $sirina . "&visina=" . $visina . "&max=" . $max . "&objType=" . $type . "&putanja=" . $root . "/&objType=" . $type;
	}
?><html>
<head>
<title>OCP</title>
<script src="/ocp/jscript/sticky.js"></script>
<script>
	var ocpfirstLoad = true;
	var ocpSized = false;
	var ocpFixed = null; //fixna velicina
</script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script>
		var IE = (navigator.userAgent.indexOf("Firefox") == -1) ? true : false;
		
		var frameBorder = (IE) ? "no" : "yes";
		var frameMargin = (IE) ? ' TOPMARGIN="0" LEFTMARGIN="0" MARGINHEIGHT="0" MARGINWIDTH="0" ' : '';
		var frameSpacing1 = (IE) ? ' framespacing="0" ' : '';
		var frameSpacing2 = (IE) ? ' framespacing="5" ' : '';

		var framesetStr = '<frameset rows="52,*" id="rightFrameset" name="rightFrameset" frameborder="no" '+frameMargin+' '+frameSpacing1+'>';
		framesetStr += '<frame src="<?php echo $frameSrc?>" name="menuFrame" scrolling="NO" noresize>';
		framesetStr += '<frameset rows="800,*" id="resizableFrameset" name="resizableFrameset" border="5" BORDERCOLOR="#7B7B7B" frameborder="yes" '+frameSpacing2+'>';
		framesetStr += '<frame src="/ocp/html/blank.html" id="listFrame" name="listFrame" FRAMEBORDER="'+frameBorder+'" BORDER="5" scrolling="auto">';
		framesetStr += '<frame src="/ocp/html/blank.html" id="uploadFrame" name="uploadFrame" FRAMEBORDER="'+frameBorder+'" BORDER="5">';
		framesetStr += '</frameset>';
		framesetStr += '</frameset><noframes></noframes>';
	</script>
</head>
<script>
	document.writeln(framesetStr);
</script>
</html>