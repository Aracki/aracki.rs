<?php
	require_once("../include/connect.php");
	require_once("../include/session.php");
	require_once("../siteManager/lib/meni.php");
	require_once("../siteManager/lib/stranica.php");
	require_once("../siteManager/lib/verzija.php");
	require_once("../include/design/table.php");
?>

<html>
<head>
	<title> OCP </title>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<link rel="stylesheet" href="/ocp/css/opsti.css">
	<link rel="stylesheet" href="/ocp/css/opcije.css">
</head>
<body class="ocp_body">
<?php
	$Akcija = utils_requestStr(getPVar("Akcija"));
	
	if (utils_valid($Akcija)) { // posle submita
		switch ($Akcija){
			case "ObrisiStranicu":
				meni_deletePage(utils_requestInt(getPVar("Stra_Id")), utils_requestInt(getPVar("Verz_Id")));
?>				<script>top.leftFrame.refreshTree(); window.open("/ocp/siteManager/menu_frame.php?<?php echo utils_randomQS();?>", "menuFrame");</script><?php
				break;

			case "ObrisiVerziju":
				meni_deleteVersion(utils_requestInt(getPVar("Verz_To_Id")), utils_requestInt(getPVar("Verz_Id")));
?>				<script>top.leftFrame.refreshTree(); window.open("/ocp/siteManager/menu_frame.php?<?php echo utils_randomQS();?>", "menuFrame");</script><?php
				break;

			case "UrediStranicu":
				meni_updatePage(utils_requestInt(getPVar("Verz_Id")), utils_requestInt(getPVar("Stra_Id")), utils_requestStr(getPVar("Meni_Naziv")));
				
				$title = utils_requestStr(getPVar("Meni_Naziv"), 0, 1);
				$title = utils_escape($title);

?>				<script>
					top.leftFrame.refreshTree(); parent.menuFrame.defaultPage();
					parent.menuFrame.changeObjName('<?php echo $title; ?>', true);
				</script><?php
				break;

			case "UrediVerziju":
				meni_updateVersion(utils_requestInt(getPVar("Verz_Id")), utils_requestInt(getPVar("Verz_To_Id")), utils_requestStr(getPVar("Meni_Naziv")));

				$title = utils_requestStr(getPVar("Meni_Naziv"), 0, 1);
				$title = utils_escape($title);
?>
				<script>
					top.leftFrame.refreshTree(); parent.menuFrame.defaultPage();
					parent.menuFrame.changeObjName('<?php echo $title; ?>', true);
				</script>
<?php
				break;
		}	
	} else { //pre submita
		jsValidateOcpLabels();

		$Akcija = utils_requestStr(getGVar("Akcija"));
		$Verz_Id = utils_requestInt(getGVar("Verz_Id"));
		switch ($Akcija){
			case "ObrisiStranicu": DrawDeletePage($Verz_Id, utils_requestInt(getGVar("Stra_Id"))); break;
			case "ObrisiVerziju": DrawDeleteVersion($Verz_Id, utils_requestInt(getGVar("Verz_To_Id"))); break;
			case "UrediStranicu": DrawEditPage($Verz_Id, utils_requestInt(getGVar("Stra_Id"))); break;
			case "UrediVerziju": DrawEditVersion($Verz_Id, utils_requestInt(getGVar("Verz_To_Id"))); break;
		}	
	}

	function DrawDeletePage($Verz_Id, $Stra_Id){
?>
		<form action="menuedit.php?<?php echo utils_randomQS();?>" method="post" name="formObject" id="formObject">
			<input type="hidden" value="ObrisiStranicu" name="Akcija">
			<input type="hidden" name="Verz_Id" value="<?php echo $Verz_Id; ?>">
			<input type="hidden" name="Stra_Id" value="<?php echo $Stra_Id; ?>">
		</form>

		<script>
			x = confirm("<?php echo ocpLabels("Are you sure you want to delete page shortcut"); ?>?");
			if (x) document.formObject.submit();
			else parent.menuFrame.defaultPage();
		</script>
<?php
	}

	function DrawDeleteVersion($Verz_Id, $Verz_To_Id) {
?>
		<form action="menuedit.php?<?php echo utils_randomQS();?>" method="post" name="formObject" id="formObject">
			<input type="hidden" value="ObrisiVerziju" name="Akcija">
			<input type="hidden" name="Verz_Id" value="<?php echo $Verz_Id; ?>">
			<input type="hidden" name="Verz_To_Id" value="<?php echo $Verz_To_Id; ?>">
		</form>
		<script>
			x = confirm("<?php echo ocpLabels("Are you sure you want to delete version shortcut"); ?>?");
			if (x) document.formObject.submit();
			else parent.menuFrame.defaultPage();
		</script>
<?php
	}

	function DrawEditPage($Verz_Id, $Stra_Id){
		$stranica = stranica_get($Stra_Id);
		$menuItem = meni_getMenuPage($Verz_Id, $Stra_Id);
		table_info(array(ocpLabels("Page shortcut").":", $stranica["Stra_Naziv"]));
?>
		<div id="ocp_main_table">
			<form action="menuedit.php?<?php echo utils_randomQS();?>" method="post" name="formObject" id="formObject" onSubmit="return validate();">
				<input type="hidden" value="UrediStranicu" name="Akcija">
				<input type="hidden" name="Verz_Id" value="<?php echo $Verz_Id; ?>">
				<input type="hidden" name="Stra_Id" value="<?php echo $Stra_Id; ?>">
<?php
				table_group(ocpLabels("general options"), false);
				table_option(ocpLabels("SHORTCUT DATA"), "ikona_meni_strana", 
					array (
						table_option_text(ocpLabels("Shortcut title"), "Meni_Naziv", $menuItem["Meni_Naziv"])
					)
				);
				table_option_submit(ocpLabels("Save"), ocpLabels("Cancel"));
?>
			</form>
		</div>

		<script src="/ocp/validate/validate_double_quotes.js"></script>
		<script src="/ocp/validate/user/is_necessary.js"></script>

		<script>
			function validate() {
				var value = is_necessary("formObject.Meni_Naziv", null, "<?php echo ocpLabels("Shortcut title"); ?>");
				if (value) validate_double_quotes(document.formObject);

				return value;
			}
		</script>
<?php
	}

	function DrawEditVersion($Verz_Id, $Verz_To_Id) {
		$verzija = verzija_get($Verz_To_Id);
		$menuItem = meni_getMenuVersion($Verz_Id, $Verz_To_Id);
		table_info(array(ocpLabels("Version shortcut").":", $verzija["Verz_Naziv"]));
?>
		<div id="ocp_main_table">
			<form action="menuedit.php?<?php echo utils_randomQS();?>" method="post" name="formObject" id="formObject" onSubmit="return validate();">
				<input type="hidden" value="UrediVerziju" name="Akcija">
				<input type="hidden" name="Verz_Id" value="<?php echo $Verz_Id; ?>">
				<input type="hidden" name="Verz_To_Id" value="<?php echo $Verz_To_Id; ?>">
<?php
				table_group(ocpLabels("general options"), false);
				table_option(ocpLabels("SHORTCUT DATA"), "ikona_meni_strana", 
					array (
						table_option_text(ocpLabels("Shortcut title"), "Meni_Naziv", $menuItem["Meni_Naziv"])
					)
				);
				table_option_submit(ocpLabels("Save"), ocpLabels("Cancel"));
?>
			</form>
		</div>

		<script src="/ocp/validate/validate_double_quotes.js"></script>
		<script src="/ocp/validate/user/is_necessary.js"></script>

		<script>
			function validate() {
				var value = is_necessary("formObject.Meni_Naziv", null, "<?php echo ocpLabels("Shortcut title");?>");
				if (value) validate_double_quotes(document.formObject);

				return value;
			}
		</script>
<?php	
	}
?>
</body>
</html>