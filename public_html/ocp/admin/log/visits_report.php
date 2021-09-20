<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../siteManager/lib/stranica.php");
	require_once("../../include/visitreports.php");
	require_once("../../include/xml_tools.php");
?>

<?php session_checkAdministrator(); ?>

<?php
	$mesec = utils_requestInt(getPVar("mesec"));
	$godina = utils_requestInt(getPVar("godina"));
	
	if (!utils_valid($mesec) && !utils_valid($godina)){
?><script>location = "/ocp/admin/log/visits_query.php?<?php echo utils_randomQS();?>"</script><?php 
	} ?>
<HTML>
<HEAD>
	<link href="/ocp/css/opsti.css" rel="stylesheet" type="text/css">
	<link href="/ocp/css/dugmici.css" rel="stylesheet" type="text/css">
	<link href="/ocp/css/opcije.css" rel="stylesheet" type="text/css">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</HEAD>
<BODY class="ocp_body"  onload="parent.detailFrame.location.href = '/ocp/html/blank.html';parent.menuFrame.eraseQuerySubmenu();">
<div id="ocp_main_table">
	<div id="ocp_blok_menu_1">
		<table class="ocp_blokovi_table">
			<tr>
				<td class="ocp_blokovi_td" style="PADDING-RIGHT: 0px; PADDING-LEFT: 6px; PADDING-BOTTOM: 4px; PADDING-TOP: 4px"><?php echo date_getMonth(intval($mesec)-1); ?>, <?php echo $godina; ?></TD>
			</tr>
		</table>
	</div>
	<div id="stickyHeaderDiv" style="overflow:auto;">
	<?php
		visitrep_generate($mesec, $godina);
	?>
	</div>
</div>
</BODY>
</HTML>