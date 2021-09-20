<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../include/tipoviobjekata.php");
?>

<?php session_checkAdministrator(); ?>

<HTML>
<HEAD>
	<TITLE> Ocp </TITLE>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link href="/ocp/css/opsti.css" rel="stylesheet" type="text/css">
	<link href="/ocp/css/dugmici.css" rel="stylesheet" type="text/css">
	<link href="/ocp/css/opcije.css" rel="stylesheet" type="text/css">
</HEAD>
<body class="ocp_body" onload="parent.detailFrame.location.href = '/ocp/html/blank.html';parent.menuFrame.populateQuerySubmenu();">
<?php
	$niz = tipobj_getAll("Labela", "asc");
?><div id="ocp_main_table">
	<form name="formObject" id="formObject" method="post" action="objects_list.php?<?php echo utils_randomQS();?>">
		<input type="hidden" name="sortName" VALUE="Id">
		<input type="hidden" name="direction" VALUE="asc">
		<input type="hidden" name="ocp_brojac" value="0">
		<input type="hidden" name="ocp_broj" value="50">
		<table class="ocp_blokovi_table">
			<tr>
				<td class="ocp_blokovi_td" style="padding:4px 0px 4px 6px;"><?php echo ocpLabels("Search deleted objects");?>:</td>
			</tr>
		</table>
		<table class="ocp_opcije_table">
			<tr>
				<td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Type");?></span></td>
				<td class="ocp_opcije_td"><select class="ocp_forma" style="width:100%" name="typeId">
					<?php
	if (count($niz) > 0)
		for ($i=0; $i<count($niz); $i++) {
			echo('<option value="'.$niz[$i]["Id"].'">'.ocpLabels($niz[$i]["Labela"]).'</option>'); }					
					?></select></td>
			</tr>
		</table>
		<table width="100%">
			<tr>
				<td height="40" align="center" class="ocp_text"><input type="submit" name="submit2" class="ocp_dugme" value="<?php echo ocpLabels("Query");?>">&nbsp;<input type="button" name="button" class="ocp_dugme" onclick="document.formObject.reset();" value="<?php echo ocpLabels("Cancel");?>"></td>
			</tr>
		</table>
	</form>
</div>
</BODY>
</HTML>
