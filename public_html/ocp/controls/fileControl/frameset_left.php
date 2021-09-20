<?php 
	require_once("../../include/session.php");

	$root = utils_requestStr(getGVar("root"));	
	$field = utils_requestStr(getGVar("field"));
	$sirina = utils_requestStr(getGVar("sirina"));
	$visina = utils_requestStr(getGVar("visina"));
	$max = utils_requestInt(getGVar("max"));
	$basicFolder = utils_requestStr(getGVar("basicFolder"));
	$fileName = utils_requestStr(getGVar("fileName"));
	$filePath = $basicFolder.$fileName;

	if ($root != "/"){
		//ako mi je putanja /upload/images/ pretvaram je u /upload/images
		if (strrpos($root, "/") == (strlen($root)-1)) $root = substr($root, 0, strlen($root)-1);
	} else $root = "/upload";

	if (utils_valid($filePath)) {
		$frameSrc = "preview.php?".utils_randomQS()."&listType=list&root=" . $root . "&field=" . $field . "&sirina=" . $sirina . "&visina=" . $visina . "&max=" . $max . "&putanja=" . $basicFolder . "&fileName=" . $filePath;
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
		var frameSpacing2 = (IE) ? ' framespacing="5" ' : '';

		var framesetStr = '<frameset rows="*,*" id="framesetLeftInner" name="framesetLeftInner" border="5" BORDERCOLOR="#7B7B7B" frameborder="yes" '+frameSpacing2+'>';
		framesetStr += '<frame src="left.php?<?php echo utils_randomQS()?>&root=<?php echo $root?>&field=<?php echo $field?>&sirina=<?php echo $sirina?>&visina=<?php echo $visina?>&max=<?php echo $max?>&basicFolder=<?php echo $basicFolder?>" id="treeFrame" name="treeFrame" FRAMEBORDER="'+frameBorder+'" BORDER="5" scrolling="NO">';
		framesetStr += '<frame src="<?php echo $frameSrc?>" id="previewFrame" name="previewFrame" FRAMEBORDER="'+frameBorder+'"  scrolling="auto" BORDER="5">';
		framesetStr += '</frameset><noframes></noframes>';
	</script>
</head>
<script>
	document.writeln(framesetStr);
</script>
</html>
