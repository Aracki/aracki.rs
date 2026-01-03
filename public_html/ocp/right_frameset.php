<?php
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/session.php");
?>
<html>
<head><Title>OCP</Title>
<script>
	var ocpfirstLoad = true;
	var ocpSized = false;
	var ocpFixed = null; //fixna velicina
</script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script src="/ocp/jscript/frameset.js"></script>
<script src="/ocp/jscript/sticky.js"></script>
</head>
<?php
	$module = utils_requestStr(getGVar("module"));
	$href = utils_requestStr(getGVar("href"));

	if ($module == "siteManager"){
?><frameset rows="30,52,0,*" name="rightFrameset" id="rightFrameset" FRAMESPACING="0" frameborder="0" border="0">
 	<frame src="/ocp/title_frame.php?<?php echo utils_randomQS();?>&module=siteManager" name="titleFrame" id="titleFrame" scrolling="NO" noresize>
	<frame src="<?php if (!utils_valid($href)) { echo "/ocp/siteManager/menu_frame.php?".utils_randomQS(); } else { echo $href; } ?>" name="menuFrame" scrolling="NO" noresize>
	<frame src="/ocp/html/blank.html" name="subMenuFrame" id="subMenuFrame" style="border-bottom:#7B7B7B solid 2px;" scrolling="auto" onload="
		if (!ocpfirstLoad){ 
			if (!ocpSized){
				adjustIFrameSize(window, frames['subMenuFrame'].window, 0, ocpFixed);
				ocpFixed = null;
				frames['subMenuFrame'].window.focus();
				menuFrame.showSubmenuClose(true, 'subMenuFrame');
			} else {
				ocpSized = false;
			}
		} else { ocpfirstLoad = false;}" noresize>
	<frame src="/ocp/home.php?<?php echo utils_randomQS();?>&type=siteManager" name="detailFrame" class="ocp_default" noresize>
</frameset><noframes></noframes><?php
		} else if ($module == "objectManager") {
?><frameset rows="30,52,*" name="rightFrameset" id="rightFrameset" FRAMESPACING="0" frameborder="0" border="0" TOPMARGIN="0" LEFTMARGIN="0" MARGINHEIGHT="0" MARGINWIDTH="0">
 	<frame src="/ocp/title_frame.php?<?php echo utils_randomQS();?>&module=objectManager" name="titleFrame" scrolling="NO" frameborder="no" noresize>
	<frame src="<?php if (!utils_valid($href)) { echo "/ocp/objectManager/menu_frame.php?".utils_randomQS(); } else { echo $href; } ?>" name="menuFrame" scrolling="NO" frameborder="no" noresize>
	<frameset rows="*,0" name="resizableFrameset" id="resizableFrameset" FRAMESPACING="5" frameborder="yes" border="1" TOPMARGIN="0" LEFTMARGIN="0" MARGINHEIGHT="0" MARGINWIDTH="0">
		<frame src="/ocp/html/blank.html" name="subMenuFrame" id="subMenuFrame" FRAMEBORDER="no" scrolling="auto">
		<frame src="/ocp/home.php?<?php echo utils_randomQS(); ?>&type=objectManager" name="detailFrame" FRAMEBORDER="no"  BORDER="0" BORDERCOLOR="#7B7B7B">
	</frameset>
</frameset>
<?php
	} else if ($module == "reports") {
?><frameset rows="30,52,*" name="rightFrameset" id="rightFrameset" FRAMESPACING="0" frameborder="0" border="0" TOPMARGIN="0" LEFTMARGIN="0" MARGINHEIGHT="0" MARGINWIDTH="0">
 	<frame src="/ocp/title_frame.php?<?php echo utils_randomQS();?>&module=reports" name="titleFrame" scrolling="NO" frameborder="no" noresize>
	<frame src="<?php if (!utils_valid($href)) echo ("/ocp/reports/menu_frame.php?".utils_randomQS()); else echo($href);?>" name="menuFrame" scrolling="NO" frameborder="no" noresize>
	<frameset rows="*,0" name="resizableFrameset" id="resizableFrameset" FRAMESPACING="5" frameborder="yes" border="1" TOPMARGIN="0" LEFTMARGIN="0" MARGINHEIGHT="0" MARGINWIDTH="0">
		<frame src="/ocp/html/blank.html" name="subMenuFrame" id="subMenuFrame" FRAMEBORDER="no" scrolling="YES">
		<frame src="/ocp/home.php?<?php echo utils_randomQS();?>&type=reports" name="detailFrame" FRAMEBORDER="no"  BORDER="0" BORDERCOLOR="#7B7B7B">
	</frameset>
</frameset><?php

	}
?>
</html>