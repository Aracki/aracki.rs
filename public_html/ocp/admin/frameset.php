<?php
	require_once("../include/session.php");
?>
<?php session_checkAdministrator(); ?>
<script>
		var IE = (navigator.userAgent.indexOf("Firefox") == -1) ? true : false;
		
		var frameBorder = (IE) ? "no" : "yes";
		var frameMargin = (IE) ? ' TOPMARGIN="0" LEFTMARGIN="0" MARGINHEIGHT="0" MARGINWIDTH="0" ' : '';
		var frameSpacing1 = (IE) ? ' framespacing="0" ' : '';
		var frameSpacing2 = (IE) ? ' framespacing="5" ' : '';

		var framesetStr = '<frameset rows="30,*" frameborder="no" '+frameMargin+' '+frameSpacing1+'>';
		framesetStr += '<frame src="/ocp/admin/title.php?<?php echo utils_randomQS();?>" name="titleFrame" scrolling="NO" noresize>';
		framesetStr += '<frameset cols="<?php echo getSVar("ocpUserWidth");?>,*" id="leftFrameset" name="leftFrameset" border="5" BORDERCOLOR="#7B7B7B" frameborder="yes" '+frameSpacing2+'>';
		framesetStr += '<frame src="/ocp/admin/left.php?<?php echo utils_randomQS();?>" FRAMEBORDER="'+frameBorder+'" BORDER="5" name="leftFrame" id="leftFrame" scrolling="NO">';
		framesetStr += '<frame src="/ocp/admin/right_frameset.php?<?php echo utils_randomQS();?>" FRAMEBORDER="'+frameBorder+'" BORDER="5" name="rightFrame" id="rightFrame">';
		framesetStr += '</frameset>';
		framesetStr += '</frameset><noframes></noframes>';
	</script>
</head>
<script>
	document.writeln(framesetStr);
</script>
</html>