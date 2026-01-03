<?php
	require_once("../include/session.php");
	session_checkAdministrator(); ?>
<html>
<head><Title>OCP</Title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script src="/ocp/jscript/frameset.js"></script>
</head>
<frameset rows="52,*" name="rightFrameset" id="rightFrameset" FRAMESPACING="0" frameborder="0" border="0" TOPMARGIN="0" LEFTMARGIN="0" MARGINHEIGHT="0" MARGINWIDTH="0">
	<frame src="/ocp/admin/menu_frame.php?<?php echo utils_randomQS();?>" name="menuFrame" scrolling="NO" frameborder="no" noresize>
	<frameset rows="*,0" name="resizableFrameset" id="resizableFrameset" FRAMESPACING="5" frameborder="yes" border="1" TOPMARGIN="0" LEFTMARGIN="0" MARGINHEIGHT="0" MARGINWIDTH="0">
		<frame src="/ocp/html/blank.html" name="subMenuFrame" id="subMenuFrame" FRAMEBORDER="no" scrolling="auto"   onload="
		if (frames['subMenuFrame'].location.href.indexOf('/ocp/admin/siteManager/blockTypes_list.php') != -1){ 
			adjustIFrameSizeAdmin(window, frames['subMenuFrame'].window, 0, null);
			frames['subMenuFrame'].window.focus();
		} else {
			resizableFrameset.setAttribute('rows', '*,0');
		}" >
		<frame src="/ocp/html/blank.html" name="detailFrame" FRAMEBORDER="no"  BORDER="0" BORDERCOLOR="#7B7B7B">
	</frameset>
</frameset>
</html>