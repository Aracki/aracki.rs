<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../include/language.php");
?>

<?php session_checkAdministrator(); ?>

<HTML>
<HEAD>
	<TITLE> Ocp </TITLE>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link href="/ocp/css/opsti.css" rel="stylesheet" type="text/css">
	<link href="/ocp/css/dugmici.css" rel="stylesheet" type="text/css">
	<link href="/ocp/css/opcije.css" rel="stylesheet" type="text/css">
	<script src="/ocp/validate/validate_double_quotes.js"></script>
</HEAD>
<body class="ocp_body" onload="parent.detailFrame.location.href = '/ocp/html/blank.html';parent.menuFrame.populateQuerySubmenu();">
<?php
	$niz = lang_getJezici("Jezik", "asc");
?><div id="ocp_main_table">
	<form name="formObject" id="formObject" method="post" action="translation_list.php?<?php echo utils_randomQS();?>" onSubmit="validate_double_quotes(document.formObject); return true;">
		<input type="hidden" name="sortName" VALUE="Labela">
		<input type="hidden" name="direction" VALUE="asc">
		<table class="ocp_blokovi_table">
			<tr>
				<td class="ocp_blokovi_td" style="padding:4px 0px 4px 6px;"><?php echo ocpLabels("Search translation");?>:</td>
			</tr>
		</table>
		<table class="ocp_opcije_table">
			<tr>
				<td class="ocp_opcije_td" style="width: 22%;"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Language");?></span></td>
				<td class="ocp_opcije_td"><select class="ocp_forma" style="width:100%" name="language">
					<?php
	for ($i=0; $i<count($niz); $i++)
		echo('<option value="'.$niz[$i]["Id"].'">'.$niz[$i]["Jezik"].'</option>');					
					?></select></td>
			</tr>
			<tr>
				<td class="ocp_opcije_td" style="width: 22%;"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Label's first letters");?></span></td>
				<td class="ocp_opcije_td">
					<input type="text" class="ocp_forma" style="width: 100%;" name="firstLetters" value="">
				</td>
			</tr>
			<tr>
				<td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Not translated");?></span></td>
				<td class="ocp_opcije_td">
					<span class="ocp_opcije_tekst1"><?php echo ocpLabels("No");?></span><input type="checkbox" name="translation" value="no" checked>
				</td>
			</tr>
			<tr>
				<td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Automatic input");?></span></td>
				<td class="ocp_opcije_td">
					<span class="ocp_opcije_tekst1"><?php echo ocpLabels("Yes");?></span><input type="checkbox" name="automatic" value="1" checked>
				</td>
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