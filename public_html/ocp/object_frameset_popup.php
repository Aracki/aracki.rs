<?php
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/connect.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/session.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/tipoviobjekata.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/design/message.php");
?>
<html>
<head>
	<title>OCP</title>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	
	<link rel="stylesheet" href="/ocp/css/opsti.css">
	<link rel="stylesheet" href="/ocp/css/opcije.css">
</head>
<?php
	$objType = utils_requestStr(getGVar("objType"));
	$ocpDefaultValues = utils_requestStr(getGVar("ocpDefaultValues"));

	$objTypeId = tipobj_getId($objType);
	$TR = getSvar('ocpTR');

	if (utils_valid($objType) && ($TR[$objTypeId] != "0")) {
?>
		<frameset rows="52,*" name="rightFrameset" id="rightFrameset" FRAMESPACING="0" frameborder="0" border="0" TOPMARGIN="0" LEFTMARGIN="0" MARGINHEIGHT="0" MARGINWIDTH="0">
			<frame src="/ocp/objectManager/menu_frame.php?<?php echo utils_randomQS();?>&ocpId=<?php echo $objTypeId?>&ocpDefaultValues=<?php echo $ocpDefaultValues?>" name="menuFrame" scrolling="NO" frameborder="no" noresize>
			<frameset rows="*,0" name="resizableFrameset" id="resizableFrameset" FRAMESPACING="5" frameborder="yes" border="1" TOPMARGIN="0" LEFTMARGIN="0" MARGINHEIGHT="0" MARGINWIDTH="0">
				<frame src="/ocp/html/blank.html" name="subMenuFrame" id="subMenuFrame" FRAMEBORDER="no" scrolling="YES">
				<frame src="/ocp/html/blank.html" name="detailFrame" FRAMEBORDER="no"  BORDER="0" BORDERCOLOR="#7B7B7B">
			</frameset>
		</frameset>
		<noframes></noframes>
<?php	
	} else {	
?>
		<body class="ocp_body">
		<?php if ($TR[$objTypeId] != "0"){ 
			message_info(ocpLabels("You don't have sufficient privileges for this operation"));
		}?>
		</body>
		</html>
<?php
	}	?>