<?php
	require_once("../include/connect.php");
	require_once("../include/session.php");
	require_once("../include/xml.php");
	require_once("../include/xml_tools.php");
	require_once("../siteManager/lib/root.php");
	require_once("../siteManager/lib/verzija.php");
	require_once("../siteManager/lib/template.php");
	require_once("../siteManager/lib/meni.php");
	require_once("../siteManager/lib/sekcija.php");
	require_once("../siteManager/lib/stranica.php");
	require_once("../config/triggers_sm.php");

	$Akcija = utils_requestStr(getGVar("Akcija"));
	$srcId = utils_requestInt(getGVar("srcId"));
	$type = utils_requestStr(getGVar("type"));

	if ($Akcija == "copy") { // posle submita
		$destId = utils_requestInt(getGVar("destId"));
		$destType = utils_requestStr(getGVar("destType"));
		
		$jsAction = "";
		$idKopije = 0;

		if ($type != "stranica"){
			set_time_limit(2400);	//40 min
			
			$noCopy = false;
			$destVersion = array();
			$xml = xml_createObject();

			$rootNode = xml_createElement($xml, "mapiranje");
			$sekcNode = xml_createElement($xml, "sekcije");
			$straNode = xml_createElement($xml, "stranice");
			$blokNode = xml_createElement($xml, "blokovi");

			if ($type == "verzija"){// kopira se verzija
				if ($srcId != $destId) {
					$destVersion = verzija_get($destId);
					this_copyTree($srcId, "S", $destId, 0); //kopiranje drveta
				} else {
					$noCopy = true;
				}
			} else {//kopira se sekcija
				if ($destType == "Verzija"){//u verziju
					$destVersion = verzija_get($destId);
				} else {//u sekciju
					if ($srcId != $destId) {
						$destSection = sekcija_get($destId);
						$destVersion = verzija_get($destSection["Sekc_Verz_Id"]);
					} else {
						$noCopy = true;
					}
				}
				if (!$noCopy)
					this_copyTree($srcId, "S1", $destId, 0); //kopiranje drveta
			}
			
			if (!$noCopy){
				xml_appendChild($rootNode, $sekcNode);
				xml_appendChild($rootNode, $straNode);
				xml_appendChild($rootNode, $blokNode);

				$SekcMap = this_loadMapArray($sekcNode);
				$StraMap = this_loadMapArray($straNode);
				$BlokMap = this_loadMapArray($blokNode);
				
				if ($type == "verzija"){
					//kopiranje pocetne sekcija i dodatnog menija
					$srcVerzija = verzija_get($srcId);

					$oldPocSekc = $srcVerzija["Verz_Sekc_Id"];
					if (utils_valid($oldPocSekc) && ($oldPocSekc != 0) && utils_valid($SekcMap[$oldPocSekc])){
						con_update("update Verzija set Verz_Sekc_Id=".$SekcMap[$oldPocSekc]." where Verz_Id=".$destId);
					}
					$meni = con_getResults("select * from DodatniMeni where Meni_Verz_Id=".$srcId." and Meni_Valid=1");
					for ($i=0; $i<count($meni); $i++){
						$next = $meni[$i];

						$strSQL = "insert into DodatniMeni (Meni_Verz_Id, Meni_Stra_Id, Meni_Verz_To_Id, Meni_Naziv, Meni_RedPrikaza) values (" . $destId;
						if (utils_valid($next["Meni_Stra_Id"]) && ($next["Meni_Stra_Id"] != 0) && utils_valid($StraMap[$next["Meni_Stra_Id"]])){
							$strSQL .= ", ". $StraMap[$next["Meni_Stra_Id"]] . ", NULL";
						} else {
							$strSQL .= ", NULL, ".$next["Meni_Verz_To_Id"];
						}
						$strSQL .= ", '".utils_escapeSingleQuote($next["Meni_Naziv"])."', ".$next["Meni_RedPrikaza"].")";
						con_update($strSQL);
					}
					this_copyLinks($srcId, "S", 0); //kopiranje linkova
				} else {
					this_copyLinks($srcId, "S1", 0); //kopiranje linkova
				}

				if ($type == "verzija"){
					$jsAction = "parent.parent.leftFrame.openMenuInLowerFrame('Verzija', '".$destId."', 'Root', '1', '4', null, null, null);";
				} else if ($type == "sekcija" && $destType == "Verzija"){
					$jsAction = "parent.parent.leftFrame.openMenuInLowerFrame('Sekcija', '".$idKopije."', 'Verzija', '".$destId."', '4', null, null, null);";
				} else {
					$jsAction = "parent.parent.leftFrame.openMenuInLowerFrame('Sekcija', '".$idKopije."', 'Sekcija', '".$destId."', '4', null, null, null);";
				}
			}
		} else {
			//update patha vec uradjen
			$idKopije = stranica_copy($srcId, $destId);
			$jsAction = "parent.parent.leftFrame.openMenuInLowerFrame('Stranica', '".$idKopije."', 'Sekcija', '".$destId."', '4', null, null, null);";
		}
?>
		<html>
		<head>
			<title> OCP </title>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<script language="javascript">
				function CloseMe(){
					<?php echo $jsAction; ?>
					<?php if ($noCopy){	?>
						alert("<?php echo ocpLabels("You cannot copy object into itself.")?>");
					<?php }	?>
					parent.parent.leftFrame.refreshTree();
				}
			</script>
		</head>
		<body onload="CloseMe();"></body>
		</html>
<?php
	} else { // pre submita
?><html>
	<head>
		<title> OCP </title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="/ocp/css/opsti.css">
		<link rel="stylesheet" href="/ocp/css/opcije.css">
		<script language="javascript">
			function openMenuInLowerFrame(type, id){
				window.open("/ocp/siteManager/copy_tree.php?<?php echo utils_randomQS();?>&type=<?php echo $type?>&srcId=<?php echo $srcId?>&destId="+id+"&destType="+type+"&Akcija=copy", "_self");
			}
			parent.ocpFixed = 226;
		</script>
		<script src="/ocp/jscript/flash_scroll.js"></script>
		<script language="javascript" type="text/javascript" src="/ocp/jscript/swfobject.js"></script>
	</head><?php

		$hint = "";
		$swfArgs = "treeSource=/ocp/siteManager/copy_data.php&treeFilter=" . $type;
		$swfArgs .= "&dragDisallowed=1&menuDisallowed=1";
		$flashHeight = "100%";
		if ($type == "verzija"){
			$hint = "Choose version where you want to copy version";
			$swfArgs .= "&versionClickDisallowed=0&sectionClickDisallowed=1&pageClickDisallowed=1";	
			$flashHeight="226";
			?><body class="ocp_body"><?php
		} else if ($type == "sekcija"){
			$hint = "Choose version/section where you want to copy section";
			$swfArgs .= "&versionClickDisallowed=0&sectionClickDisallowed=0&pageClickDisallowed=1";
			$flashHeight="226";
			?><body class="ocp_body"><?php
		} else if ($type == "stranica"){
			$hint = "Choose section where you want to copy page";
			$swfArgs .= "&versionClickDisallowed=1&sectionClickDisallowed=0&pageClickDisallowed=1";
			?><body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" marginwidth="0" marginheight="0" style="overflow-y:hidden;" onLoad="document.body.style.overflowY='hidden' "><?php
		}
?>		<div id="ocp_blok_menu_1"> 
			<table class="ocp_blokovi_table"> 
				<tr><td class="ocp_blokovi_td"><img src="/ocp/img/opsti/razno/hint.gif" style="vertical-align: bottom; margin-left:3px;"> <?php echo ocpLabels($hint); ?></td></tr>
			</table>
		</div>
		<table border="0" cellspacing="0" cellpadding="0" width="100%" height="90%">
		<tr>
			<td width="100%">
				<div id="copy_tree" style="height:100%"></div>
				<script type="text/javascript">
					var so = new SWFObject("/ocp/flash/tree.swf?<?php echo $swfArgs ?>", "treeFlash", "100%", "<?php echo $flashHeight?>", "6", "#ffffff", "", "onmousewheel=\"scrollFlash(this)\" onMouseOver=\"enableScroll(this)\" onfocus=\"enableScroll(this)\" onblur=\"disableScroll(this)\"");
				   so.write("copy_tree");
				</script></td>
			</tr>
		</table>
	</body>
</html><?php
	}

	function this_copyTree($Id, $T, $NewId, $CurrLevel){
		global $xml, $sekcNode, $straNode, $blokNode, $destId, $destType, $idKopije, $destVersion;

		switch($T){
			case "S1":
				$record = sekcija_get($Id);
				
				$sekcija = $record;
				$sekcija["Sekc_Id"] = "";

				$sekcija = smobj_beforeInsert("Sekcija", $sekcija);

				$strSQL = "insert into Sekcija(Sekc_Naziv, Sekc_Verz_Id, Sekc_ParentId, Sekc_HtmlKeywords, Sekc_HtmlDescription, Sekc_RedPrikaza, Sekc_LastModify, Sekc_ExtraParams) values ('" . utils_escapeSingleQuote($sekcija["Sekc_Naziv"]) . "', " . $destVersion["Verz_Id"] . ", ";
				if ($destType == "Verzija") $strSQL .= "NULL";
				else $strSQL .= $destId;
				if (isset($sekcija["Sekc_HtmlKeywords"]) && utils_valid($sekcija["Sekc_HtmlKeywords"])) $strSQL .= ", '".utils_escapeSingleQuote($sekcija["Sekc_HtmlKeywords"])."'";
				else $strSQL .= ", NULL";
				if (isset($sekcija["Sekc_HtmlDescription"]) && utils_valid($sekcija["Sekc_HtmlDescription"])) $strSQL .= ", '".utils_escapeSingleQuote($sekcija["Sekc_HtmlDescription"])."'";
				else $strSQL .= ", NULL";
				$strSQL .= ", " . $sekcija["Sekc_RedPrikaza"] . ", '".$sekcija["Sekc_LastModify"]."'";
				if (isset($sekcija["Sekc_ExtraParams"]) && utils_valid($sekcija["Sekc_ExtraParams"])) $strSQL .= ", '".$sekcija["Sekc_ExtraParams"]."')";
				else $strSQL .= ", NULL)";
//utils_dump($strSQL);
//die();
				$sekcija["Sekc_Id"] = con_insert($strSQL);

				smobj_afterInsert("Sekcija", $sekcija);
					
				$idKopije = $sekcija["Sekc_Id"];

				$newNode = xml_createElement($xml, "sekcije_".$Id);
				$newNode = xml_setContent($xml, $newNode, $sekcija["Sekc_Id"]);
				xml_appendChild($sekcNode, $newNode);

				this_copyTree($Id, "Str", $sekcija["Sekc_Id"], $CurrLevel+1);
				this_copyTree($Id, "S", $sekcija["Sekc_Id"], $CurrLevel+1);
				break;
			
			case "S":
				$records = ($CurrLevel == 0) ? verzija_getAllSekcija($Id) : sekcija_getAllPodsekcija($Id);

				for ($i=0; $i<count($records); $i++){
					$record = $records[$i];

//utils_dump("Sekcija ".$record["Sekc_Naziv"]);
					$sekcija = $record;
					$sekcija["Sekc_Id"] = "";
	
					$sekcija = smobj_beforeInsert("Sekcija", $sekcija);

					$strSQL = "insert into Sekcija(Sekc_Naziv, Sekc_Verz_Id, Sekc_ParentId, Sekc_HtmlKeywords, Sekc_HtmlDescription, Sekc_RedPrikaza, Sekc_LastModify, Sekc_ExtraParams) values ('" . utils_escapeSingleQuote($sekcija["Sekc_Naziv"]) . "', " . $destVersion["Verz_Id"];

					if ($CurrLevel == 0) 
						$strSQL .= ", NULL"; 
					else 
						$strSQL .= ", " . $NewId;
					if (isset($sekcija["Sekc_HtmlKeywords"]) && utils_valid($sekcija["Sekc_HtmlKeywords"])) $strSQL .= ", '".utils_escapeSingleQuote($sekcija["Sekc_HtmlKeywords"])."'";
					else $strSQL .= ", NULL";
					if (isset($sekcija["Sekc_HtmlDescription"]) && utils_valid($sekcija["Sekc_HtmlDescription"])) $strSQL .= ", '".utils_escapeSingleQuote($sekcija["Sekc_HtmlDescription"])."'";
					else $strSQL .= ", NULL";
					$strSQL .= ", " . $sekcija["Sekc_RedPrikaza"] . ", '".$sekcija["Sekc_LastModify"]."'";
					if (isset($sekcija["Sekc_ExtraParams"]) && utils_valid($sekcija["Sekc_ExtraParams"])) $strSQL .= ", '".$sekcija["Sekc_ExtraParams"]."')";
					else $strSQL .= ", NULL)";
//utils_dump($strSQL);
					$sekcija["Sekc_Id"] = con_insert($strSQL);

					smobj_afterInsert("Sekcija", $sekcija);
					
					if ($NewId == $destId) $idKopije = $sekcija["Sekc_Id"];

					$newNode = xml_createElement($xml, "sekcije_".$record["Sekc_Id"]);
					$newNode = xml_setContent($xml, $newNode, $sekcija["Sekc_Id"]);
					xml_appendChild($sekcNode, $newNode);

					this_copyTree($record["Sekc_Id"], "Str", $sekcija["Sekc_Id"], $CurrLevel+1);
					this_copyTree($record["Sekc_Id"], "S", $sekcija["Sekc_Id"], $CurrLevel+1);
				}
				break;

			case "Str": 
				$records = sekcija_getAllStranica($Id);
						
				for ($i=0; $i<count($records); $i++){
					$record = $records[$i];
					
//utils_dump("Stranica ".$record["Stra_Naziv"));
					
					$stranica = $record;
					$stranica["Stra_Id"] = "";
					$stranica = smobj_beforeInsert("Stranica", $stranica);

					$strSQL = "insert into Stranica(Stra_Naziv, Stra_Temp_Id, Stra_Sekc_Id, Stra_Prikaz, Stra_PublishDate, Stra_ExpiryDate, Stra_RedPrikaza, Stra_HtmlTitle, Stra_HtmlKeywords, Stra_HtmlDescription, Stra_User_Id, Stra_LastModify, Stra_ExtraParams)";
							
					$prikaz = ($stranica["Stra_Prikaz"] == 1) ? "1" : "0";
					$publish = (utils_valid($stranica["Stra_PublishDate"])) ? "'".datetime_format4Database($stranica["Stra_PublishDate"])."'" : "NULL";
					$expiry = (utils_valid($stranica["Stra_ExpiryDate"])) ? "'".datetime_format4Database($stranica["Stra_ExpiryDate"])."'" : "NULL";
					$htmlTitle = (utils_valid($stranica["Stra_HtmlTitle"])) ? "'".utils_escapeSingleQuote($stranica["Stra_HtmlTitle"])."'" : "NULL";
					$htmlKeys = (utils_valid($stranica["Stra_HtmlKeywords"])) ? "'".utils_escapeSingleQuote($stranica["Stra_HtmlKeywords"])."'" : "NULL";
					$htmlDesc = (utils_valid($stranica["Stra_HtmlDescription"])) ? "'".utils_escapeSingleQuote($stranica["Stra_HtmlDescription"])."'" : "NULL";
					$extra = (utils_valid($stranica["Stra_ExtraParams"])) ? "'".$stranica["Stra_ExtraParams"]."'" : "NULL";

					$strSQL .= " values ('".utils_escapeSingleQuote($stranica["Stra_Naziv"])."', ".$stranica["Stra_Temp_Id"].", ".$NewId.", ".$prikaz.", ".$publish.", ".$expiry.", ".$stranica["Stra_RedPrikaza"].", ".$htmlTitle.", ".$htmlKeys.", ".$htmlDesc.", ".$stranica["Stra_User_Id"].", '".$stranica["Stra_LastModify"]."', ".$extra.")";

//utils_dump($strSQL);
					$stranica["Stra_Id"] = con_insert($strSQL);

					smobj_afterInsert("Stranica", $stranica);

					$newNode = xml_createElement($xml, "stranica_".$record["Stra_Id"]);
					$newNode = xml_setContent($xml, $newNode, $stranica["Stra_Id"]);
					xml_appendChild($straNode, $newNode);

					stranica_updatePath($stranica["Stra_Id"]);

					this_copyTree($record["Stra_Id"], "B", $stranica["Stra_Id"], $CurrLevel);
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
						$meta = "'".$meta." (" . $destId . ")'";
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
					$newBlokId = con_insert($strSQL);

					$newNode = xml_createElement($xml, "blok_".$record["Blok_Id"]);
					$newNode = xml_setContent($xml, $newNode, $newBlokId);
					xml_appendChild($blokNode, $newNode);

					$strSQL = "insert into Stranica_Blok(StBl_Stra_Id, StBl_Blok_Id, StBl_RedPrikaza)";
					$strSQL .= " values (".$NewId.", ".$newBlokId.", ".$record["StBl_RedPrikaza"].")";
					con_insert($strSQL);
				}
				break;
			default: break;
		}
	}

	//Izmena internih linkova, pocetnih stranica i kreiranje analogija
	function this_copyLinks($Id, $T, $CurrLevel){
		global $SekcMap, $StraMap, $BlokMap, $type, $srcId, $destId, $destType;

		switch($T){
			case "S1":
				$record = sekcija_get($Id);
//izmene pocetnih stranica sekcija
				$oldPocStra = $record["Sekc_Stra_Id"];
				if (utils_valid($oldPocStra) && ($oldPocStra != 0) && utils_valid($StraMap[$oldPocStra])){
//utils_dump("update Sekcija set Sekc_Stra_Id=".$StraMap[$oldPocStra]." where Sekc_Id=".$SekcMap[$record["Sekc_Id"]]);
					con_update("update Sekcija set Sekc_Stra_Id=".$StraMap[$oldPocStra]." where Sekc_Id=".$SekcMap[$Id]);
				}
					
				this_copyLinks($Id, "Str", $CurrLevel+1);
				this_copyLinks($Id, "S", $CurrLevel+1);
				break;
			case "S":
				$records = ($CurrLevel==0) ? verzija_getAllSekcija($Id) : sekcija_getAllPodsekcija($Id);
				for ($i=0; $i<count($records); $i++){
					$record = $records[$i];

//izmene pocetnih stranica sekcija
					$oldPocStra = $record["Sekc_Stra_Id"];
					if (utils_valid($oldPocStra) && ($oldPocStra != 0) && utils_valid($StraMap[$oldPocStra])){
//utils_dump("update Sekcija set Sekc_Stra_Id=".$StraMap[$oldPocStra]." where Sekc_Id=".$SekcMap[$record["Sekc_Id"]]);
						con_update("update Sekcija set Sekc_Stra_Id=".$StraMap[$oldPocStra]." where Sekc_Id=".$SekcMap[$record["Sekc_Id"]]);
					}
					
					this_copyLinks($record["Sekc_Id"], "Str", $CurrLevel+1);
					this_copyLinks($record["Sekc_Id"], "S", $CurrLevel+1);
				}
				break;
			case "Str": 
				$records = sekcija_getAllStranica($Id);
				for ($i=0; $i<count($records); $i++){
					if ($destType == "Verzija"){
						//analogije
						$temp = array();
						$temp["Stra_Id"] = $records[$i]["Stra_Id"];
						$temp["StSt_Stra_Id"] = $StraMap[$records[$i]["Stra_Id"]];
						$temp["StSt_Id"] = "";
						stranica_newAnalogie($temp);
					}

					this_copyLinks($records[$i]["Stra_Id"], "B", $CurrLevel);
				}
				break;
			case "B":	
				if (!is_null($StraMap[$Id])){
					$Id = $StraMap[$Id];
					if (utils_isRewrite())
						convert_blockLinks(REWRITE_REWRITE_COPY, array("StraMap"=>$StraMap, "Id"=>$Id));
					else 
						convert_blockLinks(OLD_OLD_COPY, array("StraMap"=>$StraMap, "Id"=>$Id));
				}
				break;
			default: break;
		}
	}

	function this_loadMapArray($node){
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