<?php
require_once("../../include/connect.php");
require_once("../../include/session.php");
require_once("../../include/visitreports.php");
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
<div id="ocp_main_table">
	<form name="formObject" id="formObject" method="post" action="visits_report.php?<?php echo utils_randomQS();?>">
		<table class="ocp_blokovi_table">
			<tr>
				<td class="ocp_blokovi_td" style="padding:4px 0px 4px 6px;"><?php echo ocpLabels("Search visits log");?>:</td>
			</tr>
		</table>
		<table class="ocp_opcije_table">
			<tbody>
			  <tr>
				<td class="ocp_opcije_td" style="WIDTH: 22%"><span 
			  class="ocp_opcije_tekst1"><?php echo ocpLabels("Month");?></span></td>
				<td class="ocp_opcije_td"><select name="mesec" style="width: 100%;" class="ocp_forma">
				  <option value="1"><?php echo ocpLabels("January");?></option>
				  <option value="2"><?php echo ocpLabels("February");?></option>
				  <option value="3"><?php echo ocpLabels("March");?></option>
				  <option value="4"><?php echo ocpLabels("April");?></option>
				  <option value="5"><?php echo ocpLabels("May");?></option>
				  <option value="6"><?php echo ocpLabels("June");?></option>
				  <option value="7"><?php echo ocpLabels("July");?></option>
				  <option value="8"><?php echo ocpLabels("August");?></option>
				  <option value="9"><?php echo ocpLabels("September");?></option>
				  <option value="10"><?php echo ocpLabels("October");?></option>
				  <option value="11"><?php echo ocpLabels("November");?></option>
				  <option value="12"><?php echo ocpLabels("December");?></option>
				</select></td>
			  </tr>
			  <tr>
				<td class="ocp_opcije_td" style="WIDTH: 22%"><span 
			  class="ocp_opcije_tekst1"><?php echo ocpLabels("Year");?></span></td>
				<td class="ocp_opcije_td"><select name="godina" style="width: 100%;" class="ocp_forma">
<?php		$today = date;
		for ($i=2004; $i<=$today("Y"); $i++){
			?><option value="<?php echo $i;?>"><?php echo $i;?></option><?php 
		}		   ?></select>
				</td>
			  </tr>
			</tbody>
		  </table>
		<table width="100%">
			<tr>
				<td height="40" align="center" class="ocp_text"><input type="submit" name="submit2" class="ocp_dugme" value="<?php echo ocpLabels("Query");?>">&nbsp;<input type="button" name="button" class="ocp_dugme" onclick="document.formObject.reset();" value="<?php echo ocpLabels("Cancel");?>"></td>
			</tr>
		</table>
	</form>
</div>
<script>
	var today = new Date();
	document.formObject.mesec.selectedIndex = today.getMonth();
	document.formObject.godina.selectedIndex = today.getFullYear() - 2004;
</script>
</BODY>
</HTML>
