<?php
	require_once("../include/connect.php");
	require_once("../include/session.php");
	require_once("../siteManager/lib/verzija.php");
	require_once("../siteManager/lib/sekcija.php");
	require_once("../siteManager/lib/stranica.php");
	require_once("../siteManager/lib/extraparams.php");
	require_once("../include/objekti.php");
	require_once("../include/polja.php");
	require_once("../include/xml.php");
	require_once("../include/tipoviobjekata.php");
	require_once("../include/design/table.php");
	require_once("../include/xml_tools.php");
	require_once("../config/triggers_sm.php");

?><html>
<head>
	<title> OCP </title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="/ocp/css/opsti.css">
	<link rel="stylesheet" href="/ocp/css/opcije.css">
	<script language="javascript" src="/ocp/jscript/load.js"></script>
	<script language="javascript" src="/ocp/jscript/helpcalendar.js"></script>
	<script SRC="/ocp/jscript/pallete.js" type="text/javascript"></script>
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

	if (utils_valid($Akcija)) { // posle submita
		switch ($Akcija) {
			case "Sacuvaj":
				$temp = array();
				$temp["Verz_Id"] = utils_requestInt(getPVar("Verz_Id"));
				$temp["Verz_Naziv"] = utils_requestStr(getPVar("Verz_Naziv"));
				$temp["Verz_HtmlTitle"] = utils_requestStr(getPVar("Verz_HtmlTitle"));
				$temp["Verz_HtmlKeywords"] = utils_requestStr(getPVar("Verz_HtmlKeywords"));
				$temp["Verz_HtmlDescription"] = utils_requestStr(getPVar("Verz_HtmlDescription"));
				$temp["Verz_Sekc_Id"] = utils_requestInt(getPVar("Verz_Sekc_Id"));
				$temp["Verz_LastModify"] = utils_requestStr(getPVar("Verz_LastModify"));
				$extraParams = extra_setXml("verzija");
				if (utils_valid($extraParams)) $temp["Verz_ExtraParams"] = $extraParams;
				else $temp["Verz_ExtraParams"] = "";
				verzija_edit($temp);

				$filename = $_SERVER['DOCUMENT_ROOT']."/code/labele.xml";
				if (file_exists($filename) && is_writable($filename)){
					$labels = verzija_readLabels($temp["Verz_Id"]);

					$filter_text = utils_requestStr(getPVar("filter_text"));
					foreach ($labels as $key=>$value){
						if (utils_valid($filter_text)){
							if (is_integer(strpos(utils_toLower($key), utils_toLower($filter_text))) || 
								is_integer(strpos(utils_toLower($value), utils_toLower($filter_text)))){
								$labels[$key] = utils_requestStr(getPVar($key), true, true);
							}
						} else {
							$labels[$key] = utils_requestStr(getPVar($key), true, true);
						}
						
					}
					$new_label_counter = utils_requestInt(getPVar("new_label_counter"));
					for ($i=0; $i<$new_label_counter; $i++){
						$key = utils_requestStr(getPVar("new_key_".$i));
						$value = utils_requestStr(getPVar("new_value_".$i), 0, 1);
						if (utils_valid($key))
							$labels[$key] = $value;
					}
					verzija_saveLabels($temp["Verz_Id"], $labels);
				}

				$title = utils_requestStr(getPVar("Verz_Naziv"), 0, 1);
				$title = utils_escape($title);
				?><script>
					top.leftFrame.refreshTree(); parent.menuFrame.defaultPage();
					parent.menuFrame.changeObjName('<?php echo $title; ?>', true);
				</script><?php
				break;

			case "Obrisi":
				verzija_delete(utils_requestInt(getPVar("Verz_Id")));
?>
				<script>
					top.leftFrame.refreshTree(); window.open("/ocp/siteManager/menu_frame.php?<?php echo utils_randomQS();?>", "menuFrame");
					window.open("/ocp/html/blank.html", "detailFrame");
				</script>
<?php
				break;

			case "Dodaj":
				$temp = array();
				$temp["Sekc_Verz_Id"] = utils_requestInt(getPVar("Sekc_Verz_Id"));
				$temp["Sekc_Naziv"] = utils_requestStr(getPVar("Sekc_Naziv"));
				$temp["Sekc_HtmlKeywords"] = utils_requestStr(getPVar("Sekc_HtmlKeywords"));
				$temp["Sekc_HtmlDescription"] = utils_requestStr(getPVar("Sekc_HtmlDescription"));
				$temp["Sekc_LinkName"] = utils_requestStr(getPVar("Sekc_LinkName"));
				$extraParams = extra_setXml("sekcija");
				if (utils_valid($extraParams)) $temp["Sekc_ExtraParams"] = $extraParams;
				sekcija_new($temp);
?>
				<script>
					top.leftFrame.refreshTree(); window.open("/ocp/siteManager/menu_frame.php?<?php echo utils_randomQS();?>", "menuFrame");
					window.open("/ocp/html/blank.html", "detailFrame");
				</script>
<?php
				break;
		}
	} else { // pre submita
		jsValidateOcpLabels();

		$Akcija = utils_requestStr(getGVar("Akcija"));
		$VerzId = utils_requestInt(getGVar("Verz_Id"));

		switch($Akcija) {
			case "Uredi": DrawEdit(verzija_get($VerzId)); break;
			case "DodajSekciju": DrawNewSection(verzija_get($VerzId)); break;
			case "Obrisi": DrawDelete(verzija_get($VerzId)); break;
			default: break;
		}
	}
	
	function DrawEdit($verzija){
		global $validate;

		$verzija = smobj_preForm("Verzija", $verzija);
?>
		<form action="verzijaedit.php?<?php echo utils_randomQS();?>" method="post" name="formObject" id="formObject" onSubmit="return validate();">
			<input type="hidden" value="Sacuvaj" name="Akcija">
			<input type="hidden" value="<?php echo $verzija["Verz_Id"];?>" name="Verz_Id">
			<input type="hidden" value="<?php echo $verzija["Verz_LastModify"];?>" name="Verz_LastModify">
<?php
			table_group(ocpLabels("general options"), false);
			table_option(ocpLabels("MENU"), "ikona_menu", 
				array (
					table_option_text(ocpLabels("Menu title"), "Verz_Naziv", $verzija["Verz_Naziv"], true),
					table_option_select(ocpLabels("Startup section"), "Verz_Sekc_Id", verzija_getAllSekcija($verzija["Verz_Id"]), "Sekc_Id", "Sekc_Naziv", $verzija["Verz_Sekc_Id"], false, "--".ocpLabels("Choose")."--")
				)
			);

			if (utils_valid($verzija["Verz_ExtraParams"])) extra_transformString($verzija["Verz_ExtraParams"]);
			else extra_transform(extra_getXml("verzija"));

			table_group(ocpLabels("additional options"), true);
?>
			<div id="ocpAdvancedDiv" style="visibility: hidden; display: none;">
<?php
				table_option(ocpLabels("SEARCH ENGINES DATA"), "ikona_strana", 
					array (
						table_option_text(ocpLabels("Version title"), "Verz_HtmlTitle", $verzija["Verz_HtmlTitle"]), 
						table_option_textarea(ocpLabels("Version keywords"), "Verz_HtmlKeywords", $verzija["Verz_HtmlKeywords"]),
						table_option_textarea(ocpLabels("Version description"), "Verz_HtmlDescription", $verzija["Verz_HtmlDescription"])
					)
				);
				$filename = $_SERVER['DOCUMENT_ROOT']."/code/labele.xml";
				if (file_exists($filename) && is_writable($filename)){
					?><div id="verz_labels"><?php require_once("labeleedit.php");?></div><?php
				}
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

				var value = is_necessary("formObject.Verz_Naziv", null, "<?php echo ocpLabels("Menu title");?>") <?php echo $validate;?>;

				if (value) {
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

	function DrawDelete($verzija) {
?>
		<form action="verzijaedit.php?<?php echo utils_randomQS();?>" method="post" name="formObject" id="formObject">
			<input type="hidden" name="Akcija" value="Obrisi">
			<input type="hidden" name="Verz_Id" value="<?php echo $verzija["Verz_Id"];?>">
		</form>

		<script>
			var x = confirm("<?php echo ocpLabels("Are you sure you want to delete version"); ?>?");
			if (x) document.formObject.submit();
			else parent.menuFrame.defaultPage();
		</script>
<?php
	}

	function DrawNewSection($verzija) {
		global $validate;

		$sekcija = array("Sekc_Naziv"=>"", "Sekc_ExtraParams"=>"", "Sekc_LinkName"=>"", "Sekc_HtmlKeywords"=>"", "Sekc_HtmlDescription"=>"");
		$sekcija = smobj_preForm("Sekcija", $sekcija);
?>
		<form action="verzijaedit.php?<?php echo utils_randomQS();?>" method="post" name="formObject" id="formObject" onSubmit="return validate();">
			<input type="hidden" name="Akcija" value="Dodaj">
			<input type="hidden" value="<?php echo $verzija["Verz_Id"];?>" name="Sekc_Verz_Id">
<?php
			table_group(ocpLabels("general options"), false);
			table_option(ocpLabels("SECTION DATA"), "ikona_sekcija", 
				array (
					table_option_text(ocpLabels("Title"), "Sekc_Naziv", $sekcija["Sekc_Naziv"], true)
				), 2
			);

			if (utils_valid($sekcija["Sekc_ExtraParams"])) {
				extra_transformString($sekcija["Sekc_ExtraParams"]);
			} else {
				extra_transform(extra_getXml("sekcija"));
			}

			table_group(ocpLabels("additional options"), true);
?>
			<div id="ocpAdvancedDiv" style="visibility: hidden; display: none;">
<?php
				table_option(ocpLabels("SEARCH ENGINES DATA"), "ikona_strana", 
					array (
						table_option_text(ocpLabels("Url name"), "Sekc_LinkName", $sekcija["Sekc_LinkName"]),
						table_option_textarea(ocpLabels("Section keywords"), "Sekc_HtmlKeywords", $sekcija["Sekc_HtmlKeywords"]),
						table_option_textarea(ocpLabels("Section description"), "Sekc_HtmlDescription", $sekcija["Sekc_HtmlDescription"])
						
					)
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
			function validate() {
				if (simpleEditorExists) { 
					checkHtmlEditors(simpleEditorArr); 
				}

				if (value) {
					validate_double_quotes_field(document.formObject.Sekc_Naziv);
					validate_double_quotes_field(document.formObject.Sekc_HtmlKeywords);
					validate_double_quotes_field(document.formObject.Sekc_HtmlDescription);
				}

				var value = is_necessary("formObject.Sekc_Naziv", null, "<?php echo ocpLabels("Title");?>") <?php echo $validate;?>;

				return value;
			}
		</script>
<?php
	}
?>
</div>

</body>
</html>