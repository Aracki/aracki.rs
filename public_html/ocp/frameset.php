<?php
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/session.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/xml_tools.php");

	session_loadConfig();

	$module = getSVar("ocpFirstOpen");

?><html>
<head>
	<Title>OCP</Title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script>
		var IE = (navigator.userAgent.indexOf("Firefox") == -1) ? true : false;
		
		var frameBorder = (IE) ? "no" : "yes";
		var resizeStatusStr = "<?php echo ocpLabels("If you want to save OCP perspective, click on upper menu settings")?>";
		
		var framesetStr = '<frameset cols="<?php echo getSVar("ocpUserWidth");?>,*,0" name="leftFrameset" id="leftFrameset" border="5" BORDERCOLOR="#7B7B7B" frameborder="yes">';
		framesetStr += '<frame src="/ocp/ocpmenu_frame.php?<?php echo utils_randomQS();?>&Open=<?php echo $module?>" name="leftFrame" id="leftFrame" FRAMEBORDER="'+frameBorder+'" BORDER="5" scrolling="NO" onblur="window.status=\'\';" onresize="window.status=resizeStatusStr;">';
		framesetStr += '<frame src="/ocp/right_frameset.php?<?php echo utils_randomQS();?>&module=<?php echo $module?>" name="rightFrame" id="rightFrame" FRAMEBORDER="'+frameBorder+'" BORDER="5" scrolling="NO">';
		framesetStr += '<frame src="http://www.ocp2.com/redirect.asp?<?php echo utils_randomQS();?>" name="helpFrame" id="helpFrame" FRAMEBORDER="'+frameBorder+'" BORDER="5" scrolling="Yes">';
		framesetStr += '</frameset>';
	</script>
</head>
<script>
	document.writeln(framesetStr);
</script>
</html>