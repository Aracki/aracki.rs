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
	<script language="javascript" src="/ocp/jscript/helpcalendar.js"></script>
	<script src="/ocp/jscript/pallete.js" type="text/javascript"></script>
</head>
<body class="ocp_body">
<script>
	var simpleEditorExists = false;
</script>
<?php
	$Akcija = utils_requestStr(getPVar("Akcija"));
	$validate = "";	// extra parametri mogu da imaju validate pozive

	if (utils_valid($Akcija)) { // posle submita
		switch ($Akcija) {
			case "Sacuvaj":
				$pageName = "";

				$temp = array();
				$temp["Stra_Id"] = utils_requestInt(getPVar("Stra_Id"));
				$temp["Stra_Temp_Id"] = utils_requestInt(getPVar("Stra_Temp_Id"));
				$temp["Stra_Naziv"] = utils_requestStr(getPVar("Stra_Naziv"));
				$temp["Stra_Prikaz"] = utils_requestStr(getPVar("Stra_Prikaz"));
				$temp["Stra_PublishDate"] = datetime_getFormDate("Stra_PublishDate");
				$temp["Stra_ExpiryDate"] = datetime_getFormDate("Stra_ExpiryDate");
				$temp["Stra_HtmlTitle"] = utils_requestStr(getPVar("Stra_HtmlTitle"));
				$temp["Stra_HtmlKeywords"] = utils_requestStr(getPVar("Stra_HtmlKeywords"));
				$temp["Stra_HtmlDescription"] = utils_requestStr(getPVar("Stra_HtmlDescription"));
				$temp["Stra_LinkName"] = utils_requestStr(getPVar("Stra_LinkName"));
				$temp["Stra_LastModify"] = utils_requestStr(getPVar("Stra_LastModify"));
				
				$extraParams = extra_setXml("stranica");
				if (utils_valid($extraParams)) $temp["Stra_ExtraParams"] = $extraParams;

				stranica_edit($temp);
				
				$pageName = $temp["Stra_Naziv"];

				$sekcija = sekcija_get(utils_requestInt(getPVar("Stra_Sekc_Id")));
				$verzije = root_getAllVerzija(1);

				if (count($verzije) > 0){
					for ($j = 0; $j < count($verzije); $j++){
						$verzija = $verzije[$j];
						if ($verzija["Verz_Id"] != $sekcija["Sekc_Verz_Id"]){
							unset($temp);
							$temp = array();
							$temp["Stra_Id"] = utils_requestInt(getPVar("Stra_Id"));
							$temp["StSt_Stra_Id"] = utils_requestInt(getPVar("StSt_Stra_Id".$j));
							$temp["StSt_Id"] = utils_requestInt(getPVar("StSt_Id".$j));
							stranica_newAnalogie($temp);
						}
					}
				}

				$title = utils_requestStr(getPVar("Stra_Naziv"), 0, 1);
				$title = utils_escape($title);
?>
		<script>
			top.leftFrame.refreshTree(); parent.menuFrame.defaultPage();
			parent.menuFrame.changeObjName('<?php echo $title; ?>', true);
			parent.menuFrame.changeObjLink('<?php echo utils_getStraLink($temp["Stra_Id"])?>');
		</script>
<?php
			break; 

		case "Obrisi":
			stranica_delete(utils_requestInt(getPVar("Stra_Id")), false, utils_requestInt(getPVar("KillLink")), utils_requestStr(getPVar("NewLink")));
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

		$StraId = utils_requestInt(getGVar("Stra_Id"));
		$Akcija = utils_requestStr(getGVar("Akcija"));	

		switch($Akcija) { 
			case "Uredi": DrawEdit(stranica_get($StraId)); break;
			case "Obrisi": DrawDelete(stranica_get($StraId)); break;
			default: break;
		}
	}

	function DrawEdit($stranica){
		global $validate;

		$stranica = smobj_preForm("Stranica", $stranica);

		table_info(array(ocpLabels("Page url").":",  utils_getStraLink($stranica["Stra_Id"]), ocpLabels("Page created by user").":", stranica_getOwnerName($stranica["Stra_User_Id"])));
?>
		<div id="ocp_main_table">
			<form action="stranicaedit.php?<?php echo utils_randomQS();?>" method="post" name="formObject" id="formObject" onSubmit="return validate();">
				<input type="hidden" value="Sacuvaj" name="Akcija">
				<input type="hidden" value="<?php echo $stranica["Stra_Id"]; ?>" name="Stra_Id">
				<input type="hidden" value="<?php echo $stranica["Stra_Sekc_Id"]; ?>" name="Stra_Sekc_Id">
				<input type="hidden" value="<?php echo $stranica["Stra_LastModify"]; ?>" name="Stra_LastModify">
<?php
				table_group(ocpLabels("general options"), false);
				$templates = template_getAll();

				for ($k = 0; $k < count($templates); $k++) {
					$templates[$k]["Temp_Naziv"] = ocpLabels($templates[$k]["Temp_Naziv"]);
				}

				table_option(ocpLabels("TEMPLATE"), "ikona_template", 
				array (
					table_option_text(ocpLabels("Page title"), "Stra_HtmlTitle", $stranica["Stra_HtmlTitle"]), 
					table_option_select(ocpLabels("Choose template"), "Stra_Temp_Id", $templates, "Temp_Id", "Temp_Naziv", $stranica["Stra_Temp_Id"], true))
				);
				table_option(ocpLabels("MENU"), "ikona_menu", 
					array (
						table_option_text(ocpLabels("Menu title"), "Stra_Naziv", $stranica["Stra_Naziv"], true), 
						table_option_radio(ocpLabels("Page visible in menu?"), "Stra_Prikaz", $stranica["Stra_Prikaz"], array ("1", ocpLabels("Yes"), "0", ocpLabels("No")))
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
					DrawAnalogies($stranica);
?>
				</div>
<?php
				table_option_submit(ocpLabels("Save"), ocpLabels("Cancel"));
?>
			</form>
		</div>

		<script src="/ocp/validate/validate_double_quotes.js"></script>
		<script src="/ocp/validate/user/is_necessary.js"></script>
		<script src="/ocp/validate/user/validate_datetimes.js"></script>
		<script>
			function validate(){
				if (simpleEditorExists) { 
					checkHtmlEditors(simpleEditorArr); 
				}

				var value =	is_necessary("formObject.Stra_Temp_Id", null, "<?php echo ocpLabels("Choose template"); ?>") && 
							is_necessary("formObject.Stra_Naziv", null, "<?php echo ocpLabels("Menu title"); ?>") && 
							validate_datetimes("formObject.Stra_PublishDate") && validate_datetimes("formObject.Stra_ExpiryDate")  
							<?php echo $validate; ?>;

				if (value) {
					validate_double_quotes_field(document.formObject.Stra_HtmlTitle);
					validate_double_quotes_field(document.formObject.Stra_Naziv);
					validate_double_quotes_field(document.formObject.Stra_HtmlKeywords);
					validate_double_quotes_field(document.formObject.Stra_LinkName);
				}

				return value;
			}
		</script>
<?php
	}

	function DrawAnalogies($stranica) {
		$sekcija = sekcija_get($stranica["Stra_Sekc_Id"]);
		$verzije = root_getAllVerzija(1);

		if (count($verzije) > 1) {
			$analogne = stranica_getAllAnalogies($stranica["Stra_Id"]);
		
?>			<table class="ocp_opcije_table"> 
		    <tr> 
				<td rowspan="<?php echo count($verzije); ?>" class="ocp_opcije_td_ikona">
					<img src="/ocp/img/opsti/opcije/ikone/ikona_analogija.gif" title="<?php echo ocpLabels("PAGE IN OTHER VERSIONS"); ?>">
				</td> 
				<td colspan="2" class="ocp_opcije_td_naslov"><?php echo ocpLabels("PAGE IN OTHER VERSIONS"); ?></td></tr>
<?php
				for ($i = 0; $i < count($verzije); $i++) {
					$verzija = $verzije[$i];
					if ($verzija["Verz_Id"] == $sekcija["Sekc_Verz_Id"]) continue; 
					$oldStId = "";
?>			<tr> 
				<td class="ocp_opcije_td" style="width: 22%;"><span class="ocp_opcije_tekst1"><?php echo $verzija["Verz_Naziv"]; ?></span></td> 
				<td class="ocp_opcije_td">
					<select name="StSt_Stra_Id<?php echo $i; ?>" class="ocp_forma" style="width: 100%;">
						<option value="" selected>--<?php echo ocpLabels("Choose analogie"); ?>--</option>
<?php
						$stranice = verzija_getAllStranica($verzija["Verz_Id"]);
						for ($j = 0; $j < count($stranice); $j++){
							$page = $stranice[$j];
							$selected = "";
		
							for ($k = 0; $k < count($analogne); $k++){
								if (($page["Stra_Id"] == $analogne[$k]["StSt_Stra_Id1"]) || 
									($page["Stra_Id"] == $analogne[$k]["StSt_Stra_Id2"])) {
									$oldStId = "<input type='hidden' name='StSt_Id".$i."' value='". $analogne[$k]["StSt_Id"]."'>";
									$selected = "selected";
									break;
								}
							}
							$pageNaziv = " &nbsp;&nbsp;&nbsp;" . $page["Stra_Id"] . " - ".$page["Stra_Naziv"];
							if (utils_strlen($pageNaziv) > 50)
								$pageNaziv = utils_substr($pageNaziv, 0, 50) . " ...";

							if ($sekcNaziv != $page["Sekc_Naziv"]) { 
								$sekcNaziv = $page["Sekc_Naziv"];
?>
								<option value="">&nbsp;</option>
								<option value="">&nbsp;<?php echo $sekcNaziv;?></option>
<?php
							}
?>
					
					<option value="<?php echo $page["Stra_Id"]; ?>" <?php echo $selected; ?>><?php echo $pageNaziv; ?></option><?php
				}	?>				
					</select>
					<?php echo $oldStId; ?>
				</td></tr>
<?php
  			}
?>			
			</table>
<?php
		}
	}

	function DrawDelete($stranica) {
		$stranice = stranica_getAllLinked($stranica["Stra_Id"]);
?>
		<form action="stranicaedit.php?<?php echo utils_randomQS();?>" method="post" name="formObject" id="formObject" onSubmit="return validate();">
			<input type="hidden" value="Obrisi" name="Akcija">
			<input type="hidden" name="Stra_Id" value="<?php echo $stranica["Stra_Id"]; ?>">
			<div id="ocp_blok_menu_1">
				<table class="ocp_blokovi_table">
				<tr> 
					<td class="ocp_blokovi_td">
						<img src="/ocp/img/opsti/razno/hint.gif" style="vertical-align: bottom; margin-left: 3px;">
<?php
						if (count($stranice) > 0) {
							echo (ocpLabels("List of pages which contains links to this page"));
						} else {
							echo (ocpLabels("Other pages do not contain links to this page"));
						}
?>
					</td></tr>
				</table>
			</div>
<?php
			if (count($stranice) > 0) {
?>
				<div id="ocp_blok_menu_2">
					<table class="ocp_opcije_table">
<?php
					for ($i = 0; $i < count($stranice); $i++) {
						$next = $stranice[$i];
						$tekst = $next["Stra_Naziv"];

						if (utils_strlen($tekst) > 100) {
							$tekst = utils_substr($tekst, 0, 100)." ...";
						} else if ($tekst == "null") {
							$tekst = "";
						}

						$objIkona = stranica_getIconName($next);
?>
					<tr>
						<td class="ocp_opcije_td" style="width: 30px; text-align: right;"><span class="ocp_opcije_tekst1">#<?php echo $next["Stra_Id"]; ?></span></td> 
						<td class="ocp_opcije_td" style="width: 40px; text-align: center;"><img src="/ocp/img/opsti/opcije/ikone/<?php echo $objIkona; ?>.gif"></td>
						<td class="ocp_opcije_td"><span class="ocp_opcije_tekst2"><?php echo ($next["Sekc_Naziv"]." &gt; ".$tekst); ?></span></td></tr>
<?php
					}
?>
					</table>
				</div>

				<div id="ocp_blok_menu_1">
					<table class="ocp_blokovi_table"> 
					<tr><td class="ocp_blokovi_td"><?php echo ocpLabels("LINK"); ?></td></tr>
					</table>
				</div>
				<table class="ocp_opcije_table">
				<?php echo table_option_checkbox(ocpLabels("Remove link"), "KillLink", "", array("1", ocpLabels("Yes")), false, 2); ?>
				<?php echo table_option_intLink(ocpLabels("Replace link"), "NewLink", "", false, $stranica["Stra_Id"], "/upload", 2); ?>
				</table>
<?php
			} else {
?>
				<?php require_once("../include/design/message.php"); ?>
				<?php echo message_info(ocpLabels("ARE YOU SURE YOU WANT TO DELETE PAGE")."?"); ?>
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

				if (forma.KillLink != null){
					if (!forma.KillLink.checked && (forma.NewLink.value == '')){
						alert("<?php echo ocpLabels("You must either remove either replace link"); ?>."); 
						value = false;
					}
				} else { // nema linkova na ovu stranicu
					return true;
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
</body>
</html>