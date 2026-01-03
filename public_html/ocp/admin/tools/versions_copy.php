<?php 
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../siteManager/lib/root.php");
	require_once("../../siteManager/lib/verzija.php");
	require_once("../../siteManager/lib/template.php");
	require_once("../../siteManager/lib/meni.php");
	require_once("../../siteManager/lib/sekcija.php");
	require_once("../../siteManager/lib/stranica.php");
	require_once("../../include/xml.php");
	require_once("../../include/xml_tools.php");
?>

<?php session_checkAdministrator(); ?>
<HTML>
<HEAD>
<TITLE> OCP </TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/ocp/css/opsti.css">
<link rel="stylesheet" href="/ocp/css/opcije.css">
</HEAD>
<BODY class="ocp_body"><div id="ocp_main_table"><?php
	$action = utils_requestStr(getPVar("Action"));

	if (utils_valid($action) && ($action == "Copy")){
		set_time_limit(2400);	//40 min

		$srcId = utils_requestInt(getPVar("srcId"));
		$destId = utils_requestInt(getPVar("destId"));
		$destVersion = verzija_get($destId);

		$xml = xml_createObject();

		$rootNode = xml_createElement($xml, "mapiranje");
		$sekcNode = xml_createElement($xml, "sekcije");
		$straNode = xml_createElement($xml, "stranice");
		$blokNode = xml_createElement($xml, "blokovi");

		DrawTree($srcId, "S", $destId, 0); //kopiranje drveta

		xml_appendChild($rootNode, $sekcNode);
		xml_appendChild($rootNode, $straNode);
		xml_appendChild($rootNode, $blokNode);

		$SekcMap = loadMapArray($sekcNode);
		$StraMap = loadMapArray($straNode);
		$BlokMap = loadMapArray($blokNode);
		
		ChangeLinks($srcId, "S", 0); //kopiranje linkova

		require_once("../../include/design/message.php"); ?>
		<?php echo message_info(ocpLabels("Version copy has been successfully finished") . ".")?><?php
	}
	
	$roots = root_getAll(); $root = $roots[0];
	$versions = root_getAllVerzija($root["Root_Id"]);

	drawCopyForm($versions);


	function drawCopyForm($versions){
?><form name="formObject" id="formObject" method="post" onSubmit="return validate();" action="versions_copy.php?<?php echo utils_randomQS()?>" style="display:inline;">
	<input type="hidden" name="Action" value="Copy">
	<table class="ocp_naslov_table">
		<tr>
			<td class="ocp_naslov_td"><?php echo ocpLabels("Version copy")?></td>
		</tr>
	</table>
	<table class="ocp_opcije_table" border="0" cellpadding="0" cellspacing="0">
		<tr style="position:relative; top:0px">
			<td class="ocp_opcije_td ocp_opcije_tekst1" style="width: 22%;"><?php echo ocpLabels("Source version")?></td>
			<td class="ocp_opcije_td ocp_opcije_tekst1"><select name="srcId" class="ocp_forma" style="width:100%">
				<option value="">--<?php echo ocpLabels("choose version")?>--</option><?php
		for ($i=0; $i<count($versions); $i++){
		?><option value="<?php echo $versions[$i]["Verz_Id"]?>"><?php echo $versions[$i]["Verz_Naziv"]?></option><?php
		}
			?></select></td>
		</tr>
		<tr style="position:relative; top:0px">
			<td class="ocp_opcije_td ocp_opcije_tekst1" style="width: 22%;"><?php echo ocpLabels("Destination version")?></td>
			<td class="ocp_opcije_td ocp_opcije_tekst1"><select name="destId" class="ocp_forma" style="width:100%">
				<option value="">--<?php echo ocpLabels("choose version")?>--</option><?php
		for ($i=0; $i<count($versions); $i++){
		?><option value="<?php echo $versions[$i]["Verz_Id"]?>"><?php echo $versions[$i]["Verz_Naziv"]?></option><?php
		}
			?></select></td>
		</tr>
	</table>
	<table width="100%">
		<tr>
			<td height="40" align="center" class="ocp_text"><input type="submit" name="submitSave" class="ocp_dugme" value="<?php echo ocpLabels("Copy")?>">&nbsp;<input type="button" name="submitCancel" class="ocp_dugme" onClick="document.formObject.reset();" value="<?php echo ocpLabels("Cancel")?>"></td>
		</tr>
	</table>
</form>
<script language="javascript">
	function validate(){
		if ((document.formObject.srcId.value == "") && (document.formObject.destId.value == "")){
			alert("<?php echo ocpLabels("You have to choose all parameters")?>.");
			return false;
		}

		if ((document.formObject.srcId.value == document.formObject.destId.value)){
			alert("<?php echo ocpLabels("Source and destination versions must be different")?>.");
			return false;
		}
		return true;
	}
</script><?php
	}
	?></div>
</Body>
</html><?php

	function DrawTree($Id, $T, $NewId, $CurrLevel){
		global $xml, $sekcNode, $straNode, $blokNode, $srcId, $destId, $destVersion;

		switch($T){
			case "S":	
				$records = ($CurrLevel == 0) ? verzija_getAllSekcija($Id) : sekcija_getAllPodsekcija($Id);

				for ($i=0; $i<count($records); $i++){
					$record = $records[$i];

//utils_dump("Sekcija ".$record["Sekc_Naziv"]);

					$strSQL = "insert into Sekcija(Sekc_Naziv, Sekc_Verz_Id, Sekc_ParentId, Sekc_HtmlKeywords, Sekc_RedPrikaza, Sekc_LastModify, Sekc_ExtraParams)";

					if ($CurrLevel == 0) $strSQL .= " values ('".utils_escapeSingleQuote($record["Sekc_Naziv"])."', ".$NewId.", NULL"; 
					else $strSQL .= " values ('".utils_escapeSingleQuote($record["Sekc_Naziv"])."', ".$destId.", ".$NewId;
					if (isset($record["Sekc_HtmlKeywords"]) && utils_valid($record["Sekc_HtmlKeywords"])) $strSQL .= ", '".utils_escapeSingleQuote($record["Sekc_HtmlKeywords"])."'";
					else $strSQL .= ", NULL";
					$strSQL .= ", " . $record["Sekc_RedPrikaza"] . ", '".$record["Sekc_LastModify"]."'";
					if (isset($record["Sekc_ExtraParams"]) && utils_valid($record["Sekc_ExtraParams"])) $strSQL .= ", '".$record["Sekc_ExtraParams"]."')";
					else $strSQL .= ", NULL)";
//utils_dump($strSQL);
					con_update($strSQL);
				
					$newSekcId = con_getValue("select max(Sekc_Id) from Sekcija where Sekc_Valid=1");
							
					$newNode = xml_createElement($xml, "sekcije_".$record["Sekc_Id"]);
					$newNode = xml_setContent($xml, $newNode, $newSekcId);
					xml_appendChild($sekcNode, $newNode);

					DrawTree($record["Sekc_Id"], "Str", $newSekcId, $CurrLevel+1);
					DrawTree($record["Sekc_Id"], "S", $newSekcId, $CurrLevel+1);
				}
				break;

			case "Str": 
				$records = sekcija_getAllStranica($Id);
						
				for ($i=0; $i<count($records); $i++){
					$record = $records[$i];
							
//utils_dump("Stranica ".$record["Stra_Naziv"));

					$strSQL = "insert into Stranica(Stra_Naziv, Stra_Temp_Id, Stra_Sekc_Id, Stra_Prikaz, Stra_PublishDate, Stra_ExpiryDate, Stra_RedPrikaza, Stra_HtmlTitle, Stra_HtmlKeywords, Stra_User_Id, Stra_LastModify, Stra_ExtraParams)";
							
					$prikaz = ($record["Stra_Prikaz"] == 1) ? "1" : "0";
					$publish = (utils_valid($record["Stra_PublishDate"])) ? "'".datetime_format4Database($record["Stra_PublishDate"])."'" : "NULL";
					$expiry = (utils_valid($record["Stra_ExpiryDate"])) ? "'".datetime_format4Database($record["Stra_ExpiryDate"])."'" : "NULL";
					$htmlTitle = (utils_valid($record["Stra_HtmlTitle"])) ? "'".utils_escapeSingleQuote($record["Stra_HtmlTitle"])."'" : "NULL";
					$htmlKeys = (utils_valid($record["Stra_HtmlKeywords"])) ? "'".utils_escapeSingleQuote($record["Stra_HtmlKeywords"])."'" : "NULL";
					$extra = (utils_valid($record["Stra_ExtraParams"])) ? "'".$record["Stra_ExtraParams"]."'" : "NULL";

					$strSQL .= " values ('".utils_escapeSingleQuote($record["Stra_Naziv"])."', ".$record["Stra_Temp_Id"].", ".$NewId.", ".$prikaz.", ".$publish.", ".$expiry.", ".$record["Stra_RedPrikaza"].", ".$htmlTitle.", ".$htmlKeys.", ".$record["Stra_User_Id"].", '".$record["Stra_LastModify"]."', ".$extra.")";

//utils_dump($strSQL);
					con_update($strSQL);
						
					$newStraId = con_getValue("select max(Stra_Id) from Stranica where Stra_Valid=1");

					$newNode = xml_createElement($xml, "stranica_".$record["Stra_Id"]);
					$newNode = xml_setContent($xml, $newNode, $newStraId);
					xml_appendChild($straNode, $newNode);

					DrawTree($record["Stra_Id"], "B", $newStraId, $CurrLevel);
				}
				break;

			case "B":	
				$records = stranica_getAllBlok($Id, NULL, "1");
//utils_dump("Blokova ima ".count($records));

				for ($i=0; $i<count($records); $i++){
					$record = $records[$i];
							
					$meta = utils_escapeSingleQuote($record["Blok_MetaNaziv"]);
					if (!utils_valid($meta)) $meta = "NULL"; 
					else{ 
						$meta = "'".$meta." (" . strtolower($destVersion["Verz_Naziv"]) . ")'";
						$alreadyTranslatedId = con_getValue("select Blok_Id from Blok where Blok_Valid=1 and Blok_Share=1 and  Blok_MetaNaziv=".$meta);

						if (utils_valid($alreadyTranslatedId)){
							$strSQL = "insert into Stranica_Blok(StBl_Stra_Id, StBl_Blok_Id, StBl_RedPrikaza)";
							$strSQL .= " values (".$NewId.", ".$alreadyTranslatedId.", ".$record["StBl_RedPrikaza"].")";
							con_update($strSQL);
							continue;
						}
					}

					$share = ($record["Blok_Share"] == 1) ? "1" : "0";
					$publish = (utils_valid($record["Blok_PublishDate"])) ? "'".$record["Blok_PublishDate"]."'" : "NULL";
					$expiry = (utils_valid($record["Blok_ExpiryDate"])) ? "'".$record["Blok_ExpiryDate"]."'" : "NULL";
					$blok_tekst = utils_escapeSingleQuote($record["Blok_Tekst"]);

					$strSQL = "insert into Blok(Blok_TipB_Id, Blok_XmlPodaci, Blok_Share, Blok_Tekst, Blok_MetaNaziv, Blok_PublishDate, Blok_ExpiryDate, Blok_LastModify)";
					$strSQL .= " values (".$record["Blok_TipB_Id"].", '".$record["Blok_XmlPodaci"]."', ".$share.", '".$blok_tekst."', ".$meta.", ".$publish.", ".$expiry.", '".$record["Blok_LastModify"]."')";
//utils_dump($strSQL);
					con_update($strSQL);
							
					$newBlokId = con_getValue("select max(Blok_Id) from Blok where Blok_Valid=1");

					$newNode = xml_createElement($xml, "blok_".$record["Blok_Id"]);
					$newNode = xml_setContent($xml, $newNode, $newBlokId);
					xml_appendChild($blokNode, $newNode);

					$strSQL = "insert into Stranica_Blok(StBl_Stra_Id, StBl_Blok_Id, StBl_RedPrikaza)";
					$strSQL .= " values (".$NewId.", ".$newBlokId.", ".$record["StBl_RedPrikaza"].")";
					con_update($strSQL);
				}
				break;
			default: break;
		}
	}

	//Izmena /code/navigate.asp?Id, pocetnih stranica i kreiranje analogija
	function ChangeLinks($Id, $T, $CurrLevel){
		global $SekcMap, $StraMap, $BlokMap;

		switch($T){
			case "S":	
				$records = ($CurrLevel==0) ? verzija_getAllSekcija($Id) : sekcija_getAllPodsekcija($Id);
				for ($i=0; $i<count($records); $i++){
					$record = $records[$i];

//izmene pocetnih stranica sekcija
					$oldPocStra = $record["Sekc_Stra_Id"];
					if (utils_valid($oldPocStra) && ($oldPocStra != 0)){
						if (utils_valid($StraMap[$oldPocStra])){
//utils_dump("update Sekcija set Sekc_Stra_Id=".$StraMap[$oldPocStra]." where Sekc_Id=".$SekcMap[$record["Sekc_Id"]]);
							con_update("update Sekcija set Sekc_Stra_Id=".$StraMap[$oldPocStra]." where Sekc_Id=".$SekcMap[$record["Sekc_Id"]]);
						}
					}
					
					ChangeLinks($record["Sekc_Id"], "Str", $CurrLevel+1);
					ChangeLinks($record["Sekc_Id"], "S", $CurrLevel+1);
				}
				break;
			case "Str": 
				$records = sekcija_getAllStranica($Id);
				for ($i=0; $i<count($records); $i++){
					//analogije
					$temp = array();
					$temp["Stra_Id"] = $records[$i]["Stra_Id"];
					$temp["StSt_Stra_Id"] = $StraMap[$records[$i]["Stra_Id"]];
					$temp["StSt_Id"] = "";
					stranica_newAnalogie($temp);

					ChangeLinks($records[$i]["Stra_Id"], "B", $CurrLevel);
				}
				break;
			case "B":	
				if (!is_null($StraMap[$Id])){
					$Id = $StraMap[$Id];
					$records = stranica_getAllBlok($Id, NULL, "1");
//utils_dump("Blokova ima ".count($records));

					for ($i=0; $i<count($records); $i++){
						$record = $records[$i];
						$blokXml = $record["Blok_XmlPodaci"];
						$strLink = rawurlencode("/code/navigate.php?Id=");
						$lastIndex = 0;
//Ispravka linkova u tekstu

						while (is_integer(strpos($blokXml, $strLink, $lastIndex))){
							$lastIndex = strpos($blokXml, $strLink, $lastIndex);
							$tempOffset = $lastIndex + strlen($strLink);
							$leftString = substr($blokXml, 0, $tempOffset);
							$procenat = strpos($blokXml, "%", $tempOffset + 1);
							if (!is_integer($procenat)){
								$procenat = strpos($blokXml, "<", $tempOffset + 1);
							}
							$rightString = substr($blokXml, $procenat);
							$strId = substr($blokXml, $tempOffset, $procenat-$tempOffset);
							if (isset($StraMap[$strId]) && utils_valid($StraMap[$strId])){
								$blokXml = $leftString . $StraMap[$strId] . $rightString;
								$procenat = strlen($leftString) + strlen($StraMap[$strId]) + 1;
							} else {
								$procenat++;
							}
							$lastIndex = $procenat;
						}
						con_update("update Blok set Blok_XmlPodaci='".$blokXml."' where Blok_Id=".$record["Blok_Id"]);
					}
				}
				break;
			default: break;
		}
	}

	function loadMapArray($node){
		$mapArray = array();

		$childNodes = xml_childNodes($node);
		for ($i=0; $i<count($childNodes); $i++){
			$child = $childNodes[$i];
			$name = xml_nodeName($child);
			$sekcId = substr($name, strpos($name, "_")+1);
			$mapArray[$sekcId] = xml_getContent($child);
		}

		return $mapArray;
	}

?>
