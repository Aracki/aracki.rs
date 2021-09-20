<?php
	require_once("../include/connect.php");
	require_once("../include/session.php");
	require_once("../siteManager/lib/root.php");
	require_once("../siteManager/lib/verzija.php");
	require_once("../siteManager/lib/extraparams.php");
	require_once("../include/design/table.php");
	require_once("../include/tipoviobjekata.php");
	require_once("../include/xml.php");
	require_once("../include/xml_tools.php");
	require_once("../config/triggers_sm.php");
?>

<html>
<head>
	<title> OCP </title>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<link rel="stylesheet" href="/ocp/css/opsti.css">
	<link rel="stylesheet" href="/ocp/css/opcije.css">

	<script language="javascript" src="/ocp/jscript/load.js"></script>
	<script>
		var simpleEditorExists = false;
	</script>
</head>
<body class="ocp_body">
	<div class="ocp_plavi"><img src="/ocp/img/blank.gif" width="1" height="4" border="0"></div>
	<div id="ocp_main_table">
<?php
	$Akcija = utils_requestStr(getPVar("Akcija"));
	$validate = "";	// extra parametri mogu da imaju validate pozive

	if (utils_valid($Akcija)){
		switch ($Akcija){
			case "Sacuvaj":
				$temp = array();
				$temp["Root_Id"] = utils_requestInt(intval(getPVar("Root_Id")));
				$temp["Root_Naziv"] = utils_requestStr(getPVar("Root_Naziv"));
				$temp["Root_Verz_Id"] = utils_requestInt(intval(getPVar("Root_Verz_Id")));
				$temp["Root_LokalnaAdresa"] = utils_requestStr(getPVar("Root_LokalnaAdresa"));
				$temp["Root_HtmlTitle"] = utils_requestStr(getPVar("Root_HtmlTitle"));
				$temp["Root_HtmlKeywords"] = utils_requestStr(getPVar("Root_HtmlKeywords"));
				$temp["Root_HtmlDescription"] = utils_requestStr(getPVar("Root_HtmlDescription"));
				$temp["Root_MaxDubina"] = utils_requestInt(intval(getPVar("Root_MaxDubina")));
				$temp["Root_Rewrite"] = utils_requestInt(getPVar("Root_Rewrite"));
				$temp["Root_LastModify"] = utils_requestStr(getPVar("Root_LastModify"));

				root_edit($temp);

				$_SESSION["ocpLocalAddress"] = con_getValue("select Root_LokalnaAdresa from Root where Root_Valid = 1");

				$title = utils_requestStr(getPVar("Root_Naziv"), 0, 1);
				$title = utils_escape($title);
?>
			<script>
				top.leftFrame.refreshTree(); top.leftFrame.changeSiteName('<?php echo $title; ?>');
				parent.menuFrame.defaultPage(); parent.menuFrame.changeObjName('<?php echo $title; ?>', true);
			</script>
<?php
				break;

			case "Dodaj":
				$temp = array();
				$temp["Verz_Root_Id"] = utils_requestInt(intval(getPVar("Verz_Root_Id")));
				$temp["Verz_Naziv"] = utils_requestStr(getPVar("Verz_Naziv"));
				$temp["Verz_HtmlTitle"] = utils_requestStr(getPVar("Verz_HtmlTitle"));
				$temp["Verz_HtmlKeywords"] = utils_requestStr(getPVar("Verz_HtmlKeywords"));
				$temp["Verz_HtmlDescription"] = utils_requestStr(getPVar("Verz_HtmlDescription"));
				$extraParams = extra_setXml("verzija");
				if (!is_null($extraParams)) $temp["Verz_ExtraParams"] = $extraParams;
				verzija_new($temp);
?>
				<script>
					top.leftFrame.refreshTree(); window.open("/ocp/siteManager/menu_frame.php?<?php echo utils_randomQS();?>", "menuFrame");
					window.open("/ocp/html/blank.html", "detailFrame");
				</script>
<?php
				break;
		}
	} else {
		jsValidateOcpLabels();

		$Akcija = utils_requestStr(getGVar("Akcija"));
		$RootId = utils_requestInt(intval(getGVar("Root_Id")));

		switch($Akcija){
			case "Uredi": DrawEdit(root_get($RootId)); break;
			case "DodajVerziju": DrawNewVersion(root_get($RootId)); break;
		}
	}

	function DrawEdit($root){
		$root = smobj_preForm("Root", $root);
?>
		<form action="rootedit.php?<?php echo utils_randomQS();?>" method="post" name="formObject" id="formObject" onSubmit="return validate();">
			<input type="hidden" name="Akcija" value="Sacuvaj">
			<input type="hidden" value="<?php echo $root["Root_Id"]; ?>" name="Root_Id">
			<input type="hidden" value="<?php echo $root["Root_LastModify"]; ?>" name="Root_LastModify">
<?php	
			table_group(ocpLabels("general options"), false);
			table_option(ocpLabels("SITE DATA"), "ikona_root", 
				array (
					table_option_text(ocpLabels("Title"), "Root_Naziv", $root["Root_Naziv"], true, 1),
					table_option_text(ocpLabels("Site title"), "Root_HtmlTitle", $root["Root_HtmlTitle"], false, 1),
					table_option_text(ocpLabels("Local address (ie server:8011)"), "Root_LokalnaAdresa", $root["Root_LokalnaAdresa"], false, 1)
					
				), 2
			);
			table_option(ocpLabels("MENU"), "ikona_menu", 
				array (
					table_option_text(ocpLabels("Maximum depth"), "Root_MaxDubina", $root["Root_MaxDubina"], true, 1), 
					table_option_select(ocpLabels("Startup version"), "Root_Verz_Id", root_getAllVerzija($root["Root_Id"]), "Verz_Id", "Verz_Naziv", $root["Root_Verz_Id"], false, "--".ocpLabels("Choose")."--")
				), 2
			);
			table_group(ocpLabels("additional options"), true);
?>
			<div id="ocpAdvancedDiv" style="visibility: hidden; display: none;">
<?php
				table_option(ocpLabels("SEARCH ENGINES DATA"), "ikona_strana", 
					array (
						table_option_radio(ocpLabels("New links mode") . "?", "Root_Rewrite", $root["Root_Rewrite"], array ("1", ocpLabels("Yes"), "0", ocpLabels("No"))),
						table_option_textarea(ocpLabels("Site keywords"), "Root_HtmlKeywords", $root["Root_HtmlKeywords"], false, 1),
						table_option_textarea(ocpLabels("Site description"), "Root_HtmlDescription", $root["Root_HtmlDescription"], false, 1)
					), 2
				);
?>
			</div>
<?php
			table_option_submit(ocpLabels("Save"), ocpLabels("Cancel"));
?>
		</form>

		<script src="/ocp/validate/validate_double_quotes.js"></script>
		<script src="/ocp/validate/user/is_necessary.js"></script>
		<script src="/ocp/validate/user/validate_ints.js"></script>
		<script>
			function validate(){
				if (simpleEditorExists) { 
					checkHtmlEditors(simpleEditorArr); 
				}

				var value = is_necessary("formObject.Root_Naziv", null, "<?php echo ocpLabels("Title"); ?>") &&
							is_necessary("formObject.Root_MaxDubina", null, "<?php echo ocpLabels("Maximum depth"); ?>") && 
							validate_ints("formObject.Root_MaxDubina");

				if (value) validate_double_quotes(document.formObject);

				return value;
			}
		</script>
<?php
	}

	function DrawNewVersion($root){
		global $validate;
		
		$verzija = array("Verz_Naziv"=>"", "Verz_ExtraParams"=>"", "Verz_HtmlTitle"=>"", "Verz_HtmlKeywords"=>"", "Verz_HtmlDescription"=>"");
		$verzija = smobj_preForm("Verzija", $verzija);
?> 
		<form action="rootedit.php?<?php echo utils_randomQS();?>" method="post" name="formObject" id="formObject" onSubmit="return validate();">
			<input type="hidden" name="Akcija" value="Dodaj">
			<input type="hidden" value="<?php echo $root["Root_Id"]; ?>" name="Verz_Root_Id" class="ocp_forma"><?php
			table_group(ocpLabels("general options"), false);
			table_option(ocpLabels("MENU"), "ikona_verzija", 
				array (
					table_option_text(ocpLabels("Menu title"), "Verz_Naziv", $verzija["Verz_Naziv"], true, 1)
				), 2
			);

			if (utils_valid($verzija["Verz_ExtraParams"])) extra_transformString($verzija["Verz_ExtraParams"]);
			else extra_transform(extra_getXml("verzija"));

			table_group(ocpLabels("additional options"), true);
?> 
			<div id="ocpAdvancedDiv" style="visibility: hidden; display: none;">
<?php
				table_option(ocpLabels("SEARCH ENGINES DATA"), "ikona_strana", 
					array( 
						table_option_text(ocpLabels("Version title"), "Verz_HtmlTitle", $verzija["Verz_HtmlTitle"], false, 1), 
						table_option_textarea(ocpLabels("Version keywords"), "Verz_HtmlKeywords", $verzija["Verz_HtmlKeywords"]),
						table_option_textarea(ocpLabels("Version description"), "Verz_HtmlDescription", $verzija["Verz_HtmlDescription"])
					), 2
				);
?>
			</div>
<?php

			table_option_submit(ocpLabels("Save"), ocpLabels("Cancel"));
?>
		</form>

		<script src="/ocp/validate/validate_double_quotes.js"></script>
		<script src="/ocp/validate/user/is_necessary.js"></script>
		<script>
			function validate(){
				if (simpleEditorExists) { 
					checkHtmlEditors(simpleEditorArr); 
				}

				value = is_necessary("formObject.Verz_Naziv", null, "<?php echo ocpLabels("Version title");?>") <?php echo $validate;?>;

				if (value){
					validate_double_quotes_field(document.formObject.Verz_Naziv);
					validate_double_quotes_field(document.formObject.Verz_HtmlTitle);
					validate_double_quotes_field(document.formObject.Verz_HtmlKeywords);
					validate_double_quotes_field(document.formObject.Verz_HtmlDescription);
				}

				return value;
			}
		</script>
<?php
	}
?>
</div>

</body>
</html>