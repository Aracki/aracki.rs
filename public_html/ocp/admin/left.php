<?php
	require_once("../include/session.php");
?>

<?php session_checkAdministrator(); ?>

<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="/ocp/css/levi.css" rel="stylesheet" type="text/css">
<script src="/ocp/jscript/flash_scroll.js"></script>
<script language="javascript" type="text/javascript" src="/ocp/jscript/swfobject.js"></script>
<script language="JavaScript" type="text/JavaScript">
	function openMenuInLowerFrame(type, id, typeR, idR, right, dubina, maxdubina, brojDeceR, jsName){
		if (type == "Sekcija")
			window.open("/ocp/admin/menu_frame.php?<?php echo utils_randomQS();?>&ocpId=" + id, "menuFrame");
	}

	function refreshTree(){
		document.getElementById("treeFlash").TGotoLabel('/', 'restart');
		document.getElementById("treeFlash").Play();
	}
</script>
	<style type="text/css">
	<!--
	body {
		margin-left: 0px;
		margin-top: 0px;
		margin-right: 0px;
		margin-bottom: 0px;
	}
	-->
	</style>
</head>
<body><?php
	$swfArgs = "treeSource=/ocp/admin/left_data.php";
	$swfArgs .= "&menuDisallowed=1&versionClickDisallowed=1&sectionClickDisallowed=0&pageClickDisallowed=0&jsName=admin";
?><div id="admin_left" style="height:100%;"></div>
	<script type="text/javascript">
	   var so = new SWFObject("/ocp/flash/tree.swf?<?php echo $swfArgs; ?>", "treeFlash", "100%", "100%", "6", "#ffffff", "", "onmousewheel=\"scrollFlash(this)\" onMouseOver=\"enableScroll(this)\" onfocus=\"enableScroll(this)\" onblur=\"disableScroll(this)\"");
	   so.write("admin_left");
	</script></body>
</html>