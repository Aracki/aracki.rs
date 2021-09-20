<?php
	require_once("../include/connect.php");
	require_once("../include/session.php");
	require_once("../include/objekti.php");
	require_once("../include/polja.php");
	require_once("../include/xml.php");
	require_once("../siteManager/lib/root.php");
	require_once("../siteManager/lib/verzija.php");
	require_once("../siteManager/lib/sekcija.php");
	require_once("../siteManager/lib/stranica.php");
	require_once("../siteManager/lib/template.php");
	require_once("../siteManager/lib/extraparams.php");
	require_once("../include/tipoviobjekata.php");
	require_once("../include/design/table.php");
	require_once("../include/design/message.php");
	require_once("../include/xml_tools.php");
	require_once("../config/triggers_sm.php");

	global $straniceDepth;

	$straniceDepth = array(); // globalni niz koji se puni rekurzivno
?>

<html>
<head>
	<title> OCP </title>
	
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<link rel="stylesheet" href="/ocp/css/opsti.css">
	<link rel="stylesheet" href="/ocp/css/opcije.css">
</head>

	<script language="javascript" src="/ocp/jscript/load.js"></script>
	<script language="javascript" src="/ocp/jscript/helpcalendar.js"></script>
	<script SRC="/ocp/jscript/pallete.js" type="text/javascript"></script>
	<script>
		var simpleEditorExists = false;
	</script>

<body class="ocp_body">
	<div class="ocp_plavi"><img src="/ocp/img/blank.gif" width="1" height="4" border="0"></div>
	<div id="ocp_main_table">
<?php
	$Akcija = utils_requestStr(getPVar("Akcija"));
	$validate = "";	// extra parametri mogu da imaju validate pozive

	if (utils_valid($Akcija)){ // posle submita
		switch ($Akcija){
			case "Sacuvaj":
				$temp = array();
				$temp["Sekc_Id"] = utils_requestInt(getPVar("Sekc_Id"));
				$temp["Sekc_Naziv"] = utils_requestStr(getPVar("Sekc_Naziv"));
				$temp["Sekc_HtmlKeywords"] = utils_requestStr(getPVar("Sekc_HtmlKeywords"));
				$temp["Sekc_HtmlDescription"] = utils_requestStr(getPVar("Sekc_HtmlDescription"));
				$temp["Sekc_LinkName"] = utils_requestStr(getPVar("Sekc_LinkName"));
				$temp["Sekc_Stra_Id"] = utils_requestInt(getPVar("Sekc_Stra_Id"));
				$temp["Sekc_LastModify"] = utils_requestStr(getPVar("Sekc_LastModify"));
			
				$extraParams = extra_setXml("sekcija");
				if (!is_null($extraParams)) $temp["Sekc_ExtraParams"] = $extraParams;
				sekcija_edit($temp);

				$title = utils_requestStr(getPVar("Sekc_Naziv"), 0, 1);
				$title = utils_escape($title);
?>
				<script>
					top.leftFrame.refreshTree(); parent.menuFrame.defaultPage();
					parent.menuFrame.changeObjName('<?php echo $title; ?>', true);
				</script>
<?php
				break;

			case "Obrisi":
				sekcija_delete(utils_requestInt(getPVar("Sekc_Id")), true);
?>
				<script>
					top.leftFrame.refreshTree(); window.open("/ocp/siteManager/menu_frame.php?<?php echo utils_randomQS();?>", "menuFrame");
					window.open("/ocp/html/blank.html", "detailFrame");
				</script>
<?php
				break;

			case "DodajPodsekciju":
				$temp = array();
				$temp["Sekc_ParentId"] = utils_requestInt(getPVar("Sekc_ParentId"));
				$temp["Sekc_Verz_Id"] = utils_requestInt(getPVar("Sekc_Verz_Id"));
				$temp["Sekc_Naziv"] = utils_requestStr(getPVar("Sekc_Naziv"));
				$temp["Sekc_LinkName"] = utils_requestStr(getPVar("Sekc_LinkName"));
				$temp["Sekc_HtmlKeywords"] = utils_requestStr(getPVar("Sekc_HtmlKeywords"));
				$temp["Sekc_HtmlDescription"] = utils_requestStr(getPVar("Sekc_HtmlDescription"));

				$extraParams = extra_setXml("sekcija");
				if (!is_null($extraParams)) $temp["Sekc_ExtraParams"] = $extraParams;
				sekcija_newPodsekcija($temp);
?>
				<script>top.leftFrame.refreshTree();	
					window.open("/ocp/siteManager/menu_frame.php?<?php echo utils_randomQS();?>", "menuFrame");
					window.open("/ocp/html/blank.html", "detailFrame");
				</script>
<?php
				break;
			
			case "DodajStranicu":
				$temp = array();
				$temp["Stra_Temp_Id"] = utils_requestInt(getPVar("Stra_Temp_Id"));
				$temp["Stra_Naziv"] = utils_requestStr(getPVar("Stra_Naziv"));
				$temp["Stra_Prikaz"] = utils_requestStr(getPVar("Stra_Prikaz"));
				$temp["Stra_PublishDate"] = datetime_getFormDate("Stra_PublishDate");
				$temp["Stra_ExpiryDate"] = datetime_getFormDate("Stra_ExpiryDate");
				$temp["Stra_HtmlTitle"] = utils_requestStr(getPVar("Stra_HtmlTitle"));
				$temp["Stra_HtmlKeywords"] = utils_requestStr(getPVar("Stra_HtmlKeywords"));
				$temp["Stra_HtmlDescription"] = utils_requestStr(getPVar("Stra_HtmlDescription"));
				$temp["Stra_LinkName"] = utils_requestStr(getPVar("Stra_LinkName"));
				$temp["Stra_Sekc_Id"] = utils_requestInt(getPVar("Stra_Sekc_Id"));
				$temp["Stra_User_Id"] = getSVar("ocpUserId");

				$extraParams = extra_setXml("stranica");
				if (!is_null($extraParams)) $temp["Stra_ExtraParams"] = $extraParams;

				stranica_new($temp, false);	
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

		$SekcId = utils_requestInt(getGVar("Sekc_Id"));
		$Akcija = utils_requestStr(getGVar("Akcija"));

		switch($Akcija){
			case "Uredi":
				DrawEdit(sekcija_get($SekcId));
				break;

			case "DodajPodsekciju":
				DrawNewSubSection(sekcija_get($SekcId));
				break;

			case "DodajStranicu":
				DrawNewPage(sekcija_get($SekcId));
				break;

			case "Obrisi":
				DrawDelete(sekcija_get($SekcId));
				break;
		}
	}

	// Akcija Uredi sekciju
	function DrawEdit($sekcija){
		global $validate;

		$sekcija = smobj_preForm("Sekcija", $sekcija);
?>
		<form action="sekcijaedit.php?<?php echo utils_randomQS();?>" method="post" name="formObject" id="formObject" onSubmit="return validate();">
			<input type="hidden" value="Sacuvaj" name="Akcija">
			<input type="hidden" value="<?php echo $sekcija["Sekc_Id"]; ?>" name="Sekc_Id">
			<input type="hidden" value="<?php echo $sekcija["Sekc_LastModify"]; ?>" name="Sekc_LastModify">
<?php
			table_group(ocpLabels("general options"), false);
			table_option(ocpLabels("SECTION DATA"), "ikona_sekcija", 
				array (
					table_option_text(ocpLabels("Title"), "Sekc_Naziv", $sekcija["Sekc_Naziv"], true)
				)
			);
			table_option(ocpLabels("MENU"), "ikona_menu", 
				array (
					table_option_select(ocpLabels("Startup page"), "Sekc_Stra_Id", sekcija_getAllStranica($sekcija["Sekc_Id"]), "Stra_Id", "Stra_Naziv", $sekcija["Sekc_Stra_Id"], false, "--".ocpLabels("Choose")."--"))
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
			function validate(){
				if (simpleEditorExists) { 
					checkHtmlEditors(simpleEditorArr) 
				}

				var value = is_necessary("formObject.Sekc_Naziv", null, "<?php echo ocpLabels("Title");?>")<?php echo $validate; ?>;

				if (value){
					validate_double_quotes_field(document.formObject.Sekc_Naziv);
					validate_double_quotes_field(document.formObject.Sekc_HtmlKeywords);
					validate_double_quotes_field(document.formObject.Sekc_HtmlDescription);
					validate_double_quotes_field(document.formObject.Sekc_LinkName);
				}

				return value;
			}
		</script>
<?php
	}

	// Akcija Dodaj novu stranicu
	function DrawNewPage($sekcija){
		global $validate;

		$stranica = array("Stra_HtmlTitle"=>"", "Stra_Temp_Id"=>"", "Stra_Naziv"=>"", "Stra_Prikaz"=>"false", "Stra_ExtraParams"=>"", "Stra_LinkName"=>"", "Stra_HtmlKeywords"=>"", "Stra_HtmlDescription"=>"", "Stra_PublishDate"=>"", "Stra_ExpiryDate"=>"");
		$stranica = smobj_preForm("Stranica", $stranica);
?>
		<form action="sekcijaedit.php?<?php echo utils_randomQS();?>" method="post" name="formObject" id="formObject" onSubmit="return validate();">
			<input type="hidden" value="<?php echo $sekcija["Sekc_Id"]; ?>" name="Stra_Sekc_Id">
			<input type="hidden" name="Akcija" value="DodajStranicu">
<?php
			table_group(ocpLabels("general options"), false);
			$templates = template_getAll();

			for ($k = 0; $k < count($templates); $k++) {
				$templates[$k]["Temp_Naziv"] = ocpLabels($templates[$k]["Temp_Naziv"]);
			}

			table_option(ocpLabels("TEMPLATE"), "ikona_template", 
				array (
					table_option_text(ocpLabels("Page title"), "Stra_HtmlTitle", $stranica["Stra_HtmlTitle"]),
					table_option_select(ocpLabels("Choose template"), "Stra_Temp_Id", $templates, "Temp_Id", "Temp_Naziv", $stranica["Stra_Temp_Id"], true)
				)
			);
			table_option(ocpLabels("MENU"), "ikona_menu", 
				array (
					table_option_text(ocpLabels("Menu title"), "Stra_Naziv", $stranica["Stra_Naziv"], true), 
					table_option_radio(ocpLabels("Page visible in menu?"), "Stra_Prikaz", $stranica["Stra_Prikaz"], array("1", ocpLabels("Yes"), "0",  ocpLabels("No")))
				)
			);

			if (utils_valid($stranica["Stra_ExtraParams"])) { 
				extra_transformString($stranica["Stra_ExtraParams"]);
			} else {
				extra_transform(extra_getXml("stranica"));
			}

			table_group(ocpLabels("additional options"), true);
?>
			<div id="ocpAdvancedDiv" style="visibility: hidden; display: none;">
<?php
				table_option(ocpLabels("SEARCH ENGINES DATA"), "ikona_strana", 
					array (
						table_option_text(ocpLabels("Url name"), "Stra_LinkName", $stranica["Stra_LinkName"]), 
						table_option_textarea(ocpLabels("Page keywords"), "Stra_HtmlKeywords", $stranica["Stra_HtmlKeywords"]),
						table_option_textarea(ocpLabels("Page description"), "Stra_HtmlDescription", $stranica["Stra_HtmlDescription"])
					)
				);
				table_option(ocpLabels("PERIOD OF VISIBILITY"), "ikona_kalendar", 
					array (
						table_option_textDate(ocpLabels("From"), "Stra_PublishDate", $stranica["Stra_PublishDate"]), 
						table_option_textDate(ocpLabels("To"), "Stra_ExpiryDate", $stranica["Stra_ExpiryDate"])
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
		<script src="/ocp/validate/user/validate_dates.js"></script>
		<script>
			function validate(){
				if (simpleEditorExists) { 
					checkHtmlEditors(simpleEditorArr) 
				}

				var value= is_necessary("formObject.Stra_Temp_Id", null, "<?php echo ocpLabels("Choose template"); ?>") && 
							is_necessary("formObject.Stra_Naziv", null, "<?php echo ocpLabels("Menu title"); ?>") &&
							validate_dates("formObject.Stra_PublishDate") && validate_dates("formObject.Stra_ExpiryDate") <?php echo $validate; ?>;

				if (value) {
					validate_double_quotes_field(document.formObject.Stra_HtmlTitle);
					validate_double_quotes_field(document.formObject.Stra_Naziv);
					validate_double_quotes_field(document.formObject.Stra_HtmlKeywords);
					validate_double_quotes_field(document.formObject.Stra_HtmlDescription);
					validate_double_quotes_field(document.formObject.Stra_LinkName);
				}

				return value;
			}
		</script>
<?php
	}

	// Akcija Dodaj novu podsekciju
	function DrawNewSubSection($sekcija){
		global $validate;

		$podsekcija = array("Sekc_Naziv"=>"", "Sekc_ExtraParams"=>"", "Sekc_LinkName"=>"", "Sekc_HtmlKeywords"=>"", "Sekc_HtmlDescription"=>"");
		$podsekcija = smobj_preForm("Sekcija", $podsekcija);
?>
		<form action="sekcijaedit.php?<?php echo utils_randomQS();?>" method="post" name="formObject" id="formObject" onSubmit="return validate();">
			<input type="hidden" name="Akcija" value="DodajPodsekciju">
			<input type="hidden" value="<?php echo $sekcija["Sekc_Verz_Id"]; ?>" name="Sekc_Verz_Id">
			<input type="hidden" value="<?php echo $sekcija["Sekc_Id"]; ?>" name="Sekc_ParentId">
<?php
			table_group(ocpLabels("general options"), false);
			table_option(ocpLabels("SUBSECTION DATA"), "ikona_sekcija", 
				array (
					table_option_text(ocpLabels("Title"), "Sekc_Naziv", $podsekcija["Sekc_Naziv"], true)
				)
			);
			
			if (utils_valid($podsekcija["Sekc_ExtraParams"])) {
				extra_transformString($podsekcija["Sekc_ExtraParams"]);
			} else {
				extra_transform(extra_getXml("sekcija"));
			}
			
			table_group(ocpLabels("additional options"), true);
?>
			<div id="ocpAdvancedDiv" style="visibility: hidden; display: none;">
<?php
				table_option(ocpLabels("SEARCH ENGINES DATA"), "ikona_strana", 
					array (
						table_option_text(ocpLabels("Url name"), "Sekc_LinkName", $podsekcija["Sekc_LinkName"]), 
						table_option_textarea(ocpLabels("Section keywords"), "Sekc_HtmlKeywords", $podsekcija["Sekc_HtmlKeywords"]),
						table_option_textarea(ocpLabels("Section description"), "Sekc_HtmlDescription", $podsekcija["Sekc_HtmlDescription"])
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
			function validate(){
				if (simpleEditorExists) { 
					checkHtmlEditors(simpleEditorArr); 
				}

				var value = is_necessary("formObject.Sekc_Naziv", null, "<?php echo ocpLabels("Title"); ?>") <?php echo $validate; ?>;

				if (value) {
					validate_double_quotes_field(document.formObject.Sekc_Naziv);
					validate_double_quotes_field(document.formObject.Sekc_HtmlKeywords);
					validate_double_quotes_field(document.formObject.Sekc_LinkName);
				}

				return value;
			}
		</script>
<?php
	}

	// Akcija Obrisi
	function DrawDelete($sekcija) {
		global $straniceDepth;
		
		sekcija_getAllDepthStranice($sekcija["Sekc_Id"]);	// f-ja koja se punila globalno
?>
		<script>var linksArray = new Array();</script>
		<form action="sekcijaedit.php?<?php echo utils_randomQS();?>" method="post" name="formObject" id="formObject" onSubmit="return validate();">
			<input type="hidden" value="Obrisi" name="Akcija">
			<input type="hidden" name="Sekc_Id" value="<?php echo $sekcija["Sekc_Id"]; ?>">
<?php
			table_info(array(ocpLabels("List of pages which contains links to pages from this section"), ""));
			if (count($straniceDepth) > 0){
				$none = true;
				for ($j = 0; $j < count($straniceDepth); $j++) {
					$stranicaDepthTitle = utils_toUpper($straniceDepth[$j]["Sekc_Naziv"])."&gt;".strtoupper($straniceDepth[$j]["Stra_Naziv"]);
					$stranice = stranica_getAllLinked($straniceDepth[$j]["Stra_Id"]);
?>
					<script>linksArray[linksArray.length] = '<?php echo $straniceDepth[$j]["Stra_Id"]; ?>';</script>
<?php
					if (count($stranice) > 0) {
						if ($none) {
							// table_info(array(ocpLabels("List of pages which contains links to pages from this section"), ""));
							$none = false;
						}

						$options_arr = array();

						for ($i = 0; $i < count($stranice); $i++) {
							$next = $stranice[$i];
							$tekst = $next["Stra_Naziv"];
							if (utils_strlen($tekst) > 100) {
								$tekst = utils_substr($tekst, 0, 100)." ...";
							} else if ($tekst == "null") {
								$tekst = "";
							}

							$options_arr[$i] = table_option_label(array("#".$next["Stra_Id"], "<img src='/ocp/img/opsti/opcije/ikone/".stranica_getIconName($next).".gif'>"), NULL, $next["Sekc_Naziv"]." &gt; ".$tekst);
						}

							$options_arr[] = table_option_checkbox(ocpLabels("Remove link"), "KillLink".$straniceDepth[$j]["Stra_Id"], "", array("1", ocpLabels("Yes")), false, 2);

							$options_arr[] = table_option_intLink(ocpLabels("Replace link"), "NewLink".$straniceDepth[$j]["Stra_Id"], "", false, $next["Stra_Id"], "/upload", 2);

						table_option(ocpLabels("PAGE")." ".$stranicaDepthTitle, "ikona_menu", $options_arr, 3);
					}
				}
	}
			if (isset($none)){
?>
				<?php echo message_info(ocpLabels("ARE YOU SURE YOU WANT TO DELETE SECTION")."?"); ?>
<?php
			} else {
?>
				<?php echo message_info(ocpLabels("THERE ARE NO PAGES IN THIS SECTION")); ?>
<?php
			}

			table_option_submit(ocpLabels("Confirm"), ocpLabels("Cancel"));
?>
		</form>

		<script src="/ocp/validate/validate_double_quotes.js"></script>
		<script>
			function validate(){
				if (simpleEditorExists) { 
					checkHtmlEditors(simpleEditorArr);
				}

				var value = true;
				var forma = document.formObject;

				for (var i = 0; i < linksArray.length; i++){
					eval("var killLink = forma.KillLink"+linksArray[i]);
					if (killLink != null){
						eval("var newLink = forma.NewLink"+linksArray[i]);
						if (!killLink.checked && (newLink.value == '')){
							alert("<?php echo ocpLabels("You must either remove either replace link"); ?>."); 
							value = false;
							break;
						}
					}
				}

				if (value){
					validate_double_quotes(document.formObject);
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