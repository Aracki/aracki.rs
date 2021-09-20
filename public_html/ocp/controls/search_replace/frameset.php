<?php
	require_once("../../include/session.php");
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

		var framesetStr = '<frameset rows="30,*" id="srFrameset" name="srFrameset" frameborder="no" '+frameMargin+' '+frameSpacing1+'>';
		framesetStr += '<frame src="title_frame.php?<?php echo utils_randomQS() ?>" name="titleFrame" scrolling="NO" noresize>';
		framesetStr += '<frameset rows="100%,*" id="downFrameset" name="downFrameset" border="5" BORDERCOLOR="#7B7B7B" frameborder="yes" '+frameSpacing2+'>';
		framesetStr += '<frame src="/ocp/controls/search_replace/query.php?<?php echo utils_randomQS() ?>" id="findFrame" name="findFrame" FRAMEBORDER="'+frameBorder+'" BORDER="5">';
		framesetStr += '<frame src="/ocp/html/blank.html" id="replaceFrame" name="replaceFrame" FRAMEBORDER="'+frameBorder+'" BORDER="5">';
		framesetStr += '</frameset>';
		framesetStr += '</frameset><noframes></noframes>';
	</script>
</head>
<script>
	document.writeln(framesetStr);
</script>
</html>
