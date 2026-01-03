<?php
	$site_root = $_SERVER['DOCUMENT_ROOT'];

	require_once($site_root."/ocp/include/utils.php");
	require_once($site_root."/ocp/include/date.php");
	require_once($site_root."/ocp/include/string.php");
	require_once($site_root."/ocp/include/log.php");
	require_once($site_root."/ocp/include/prevod.php");

/*Vraca url template
====================*/	
	function blokFn_getUrlTemplate($stranica){
		return $stranica["Temp_Url"]."?Id=".$stranica["Stra_Id"]."&editor=1";
	}

/*Cisti sve html tagove iz ulaznog stringa
====================================================*/	
	function blokFn_Html2Txt($strInput) {
//ako tekst pocinje < imamo problem - strpos ne razlikuje 0 i false		
		$strInput = " " . rawurldecode($strInput);
		
		$posClosedTag = 0;
		$posOpenTag = 0;
		$strOutput = "";
		$i = 0;
		$ok = true;
		
		while ($ok) {
			$posClosedTag = strpos($strInput, ">", $i);
			$posOpenTag = strpos($strInput, "<", $posClosedTag);
			if (is_integer($posClosedTag) && is_integer($posOpenTag))
				$ok = true; 
			else 
				$ok=false;
			if ($ok) {
				$strOutput .= substr($strInput, $posClosedTag+1, $posOpenTag-$posClosedTag-1);
				$i = $posClosedTag+1;
			}
		}
		if ((!$ok)&&($i==0)) 
			return $strInput;
		else 
			return $strOutput;
	}

/*Pomera blok StBl_Id1 iznad bloka StBl_Id2
===========================================*/	
	function blokFn_changeOrder($Stra_Id, $StBl_Id1, $StBl_Id2, $Akcija){
//utils_dump($Stra_Id." ".$StBl_Id1." ".$StBl_Id2." ".$Akcija);
		$Dir = "Down";
		$StBl_Id2 = ($StBl_Id2 == "0") ? "last" : $StBl_Id2;

		if ($Akcija == "Kopiraj"){ 
			//prvo kopiram blok i smestim ga na kraj, a onda radim move novokreiranog bloka
			$strSQL = "select Blok_Id, Blok_Share, TipB_Naziv, StBl_Stra_Id, Blok_XmlPodaci, Blok_Tekst from Stranica_Blok, Blok, TipBloka where Blok_Id=StBl_Blok_Id and TipB_Id = Blok_TipB_Id and StBl_Id=".$StBl_Id1;
			$blok = con_getResult($strSQL);
			$Blok_Id = $blok["Blok_Id"];

			if ($blok["Blok_Share"] != "1"){
				$strSQL = "select Blok_TipB_Id, Blok_XmlPodaci, Blok_Share, Blok_MetaNaziv, Blok_Tekst";
				$strSQL .= ", Blok_PublishDate, Blok_ExpiryDate";
				$strSQL .= " from Blok where Blok_Id=".$Blok_Id;
				$orgBlok = con_getResult($strSQL);

				$orgBlok["Blok_Tekst"] = utils_escapeSingleQuote($orgBlok["Blok_Tekst"]);
				$orgBlok["Blok_MetaNaziv"] = utils_escapeSingleQuote($orgBlok["Blok_MetaNaziv"]);
				
				$strSQL = "insert into Blok (Blok_TipB_Id, Blok_XmlPodaci, Blok_Share, Blok_MetaNaziv, Blok_Tekst, Blok_PublishDate, Blok_ExpiryDate) values ";
				$strSQL .= " (".$orgBlok["Blok_TipB_Id"].", '".$orgBlok["Blok_XmlPodaci"].
				"', ".$orgBlok["Blok_Share"].", '".$orgBlok["Blok_MetaNaziv"]."', '".$orgBlok["Blok_Tekst"]."'";
				if (!utils_valid($orgBlok["Blok_PublishDate"])) $strSQL .= ", NULL";
				else $strSQL .= ", '".datetime_format4Database($orgBlok["Blok_PublishDate"])."'";
				if (!utils_valid($orgBlok["Blok_ExpiryDate"])) $strSQL .= ", NULL";
				else $strSQL .= ", '".datetime_format4Database($orgBlok["Blok_ExpiryDate"])."'";
				$strSQL .= ")";
				con_update($strSQL);

				$Blok_Id = con_getValue("select max(Blok_Id) as max from Blok");
			}
			$redPrikaza = intval(con_getValue("select max(StBl_RedPrikaza) from Stranica_Blok where StBl_Valid=1 and StBl_Stra_Id=".$Stra_Id)) + 1;
			$strSQL = "insert into Stranica_Blok(StBl_Stra_Id, StBl_Blok_Id, StBl_RedPrikaza) ";
			$strSQL .= " values (".$Stra_Id.", ".$Blok_Id.", ".$redPrikaza.")";
			con_update($strSQL);

			if ($StBl_Id2 == "last") {
				return;
			} else 
				$StBl_Id1 = con_getValue("select max(StBl_Id) from Stranica_Blok"); 
		}

		$StBl_RedPrikaza = con_getValue("select StBl_RedPrikaza from Stranica_Blok where StBl_Id=".$StBl_Id1);
		$StBl_RedPrikaza_Move = ($StBl_Id2 != "last") ? con_getValue("select StBl_RedPrikaza from Stranica_Blok where StBl_Id=".$StBl_Id2) : "last";

//utils_dump($StBl_Id1." ima red prikaza ".$StBl_RedPrikaza);
//utils_dump($StBl_Id2." ima red prikaza ".$StBl_RedPrikaza_Move);

		$strSQL = "select StBl_Id from Stranica_Blok where StBl_RedPrikaza=".$StBl_RedPrikaza." and StBl_Stra_Id=".$Stra_Id;
		$StBl_Id = con_getValue($strSQL);

		$strSQL = " select StBl_Id from Stranica_Blok where StBl_Stra_Id=".$Stra_Id;// ." and StBl_Valid=1";
		if ($StBl_RedPrikaza_Move != "last"){
			if (intval($StBl_RedPrikaza) < intval($StBl_RedPrikaza_Move)){
				$strSQL .=" and StBl_RedPrikaza > ".$StBl_RedPrikaza." and StBl_RedPrikaza < ".$StBl_RedPrikaza_Move;
				$StBl_RedPrikaza_Move = intval($StBl_RedPrikaza_Move) - 1;
			} else {
				$Dir = "Up";
				$strSQL .=" and StBl_RedPrikaza < ".$StBl_RedPrikaza." and StBl_RedPrikaza >= ".$StBl_RedPrikaza_Move;
			}
		} else {
			$StBl_RedPrikaza_Move = 
				intval(con_getValue("select max(StBl_RedPrikaza) from Stranica_Blok where StBl_Valid=1 and StBl_Stra_Id=".$Stra_Id)) + 1;

			$strSQL .=" and StBl_RedPrikaza > ".$StBl_RedPrikaza;
		}
//utils_dump($strSQL);
//utils_dump("StBl_RedPrikaza_Move " . $StBl_RedPrikaza_Move);


		$results = con_getResults($strSQL);
		for ($i=0;$i<count($results);$i++){
			if ($Dir == "Down"){
				con_update("update Stranica_Blok set StBl_RedPrikaza=StBl_RedPrikaza-1 where StBl_Id=".$results[$i]["StBl_Id"]);
//utils_dump("update Stranica_Blok set StBl_RedPrikaza=StBl_RedPrikaza-1 where StBl_Id=".$results[$i]["StBl_Id"]);
			} else{
				con_update("update Stranica_Blok set StBl_RedPrikaza=StBl_RedPrikaza+1 where StBl_Id=".$results[$i]["StBl_Id"]);
//utils_dump("update Stranica_Blok set StBl_RedPrikaza=StBl_RedPrikaza+1 where StBl_Id=".$results[$i]["StBl_Id"]);
			}
		}	
		con_update("update Stranica_Blok set StBl_RedPrikaza=".$StBl_RedPrikaza_Move." where StBl_Id=".$StBl_Id);
//utils_dump("update Stranica_Blok set StBl_RedPrikaza=".$StBl_RedPrikaza_Move." where StBl_Id=".$StBl_Id);
		
		//korekcija ukoliko je neki red prikaza uvecan nerezonski
		$results = con_getResults("select * from Stranica_Blok where StBl_Stra_Id=".$Stra_Id." order by StBl_RedPrikaza asc");
		for ($i=0; $i<count($results); $i++)
			con_update("update Stranica_Blok set StBl_RedPrikaza=".$i." where StBl_Id=".$results[$i]["StBl_Id"]);

	}

/*Move blokova na novu stranicu
=========================================================*/
	function blokFn_move($Stra_Id, $arrId, $StBl_IdMove, $Dir){
		for ($i=0;$i<count($arrId);$i++) {	//prvo treba prebaciti blokove na stranicu Stra_Id
			con_update("update Stranica_Blok set StBl_Stra_Id=".$Stra_Id." where StBl_Id=".$arrId[$i]); }
		blokFn_changeOrders($Stra_Id, $arrId, $StBl_IdMove, $Dir);
	}

/*Copy blokova na novu stranicu
=========================================================*/
	function blokFn_copy($Stra_Id, $arrId, $StBl_IdMove, $Dir){
		$strSQL = null;
		$counter = 0;

//prvo treba sve blokove kopirati na novu stranicu
		for ($i=0;$i<count($arrId);$i++){
			$strSQL = "select Blok_Id, Blok_Share, TipB_Naziv, StBl_Stra_Id, Blok_XmlPodaci, Blok_Tekst  from Stranica_Blok, Blok, TipBloka where Blok_Id=StBl_Blok_Id and TipB_Id = Blok_TipB_Id and StBl_Id=".$arrId[$i];
			$blokovi = con_getResults($strSQL);
			for ($j=0;$j<count($blokovi);$j++){
				$Blok_Id = $blokovi[$j]["Blok_Id"];

				if (($blokovi[$j]["Blok_Share"] != "1")){
					$strSQL = "select Blok_TipB_Id, Blok_XmlPodaci, Blok_Share, Blok_MetaNaziv, Blok_Tekst";
					$strSQL .= ", Blok_PublishDate, Blok_ExpiryDate";
					$strSQL .= " from Blok where Blok_Id=".$Blok_Id;
					$orgBlok = con_getResult($strSQL);

					$orgBlok["Blok_Tekst"] = utils_escapeSingleQuote($orgBlok["Blok_Tekst"]);
					$orgBlok["Blok_MetaNaziv"] = utils_escapeSingleQuote($orgBlok["Blok_MetaNaziv"]);
					
					$strSQL = "insert into Blok (Blok_TipB_Id, Blok_XmlPodaci, Blok_Share, Blok_MetaNaziv, Blok_Tekst, Blok_PublishDate, Blok_ExpiryDate) values ";
					$strSQL .= " (".$orgBlok["Blok_TipB_Id"].", '".$orgBlok["Blok_XmlPodaci"].
					"', ".$orgBlok["Blok_Share"].", '".$orgBlok["Blok_MetaNaziv"]."', '".$orgBlok["Blok_Tekst"]."'";
					if (!utils_valid($orgBlok["Blok_PublishDate"])) $strSQL .= ", NULL";
					else $strSQL .= ", '".datetime_format4Database($orgBlok["Blok_PublishDate"])."'";
					if (!utils_valid($orgBlok["Blok_ExpiryDate"])) $strSQL .= ", NULL";
					else $strSQL .= ", '".datetime_format4Database($orgBlok["Blok_ExpiryDate"])."'";
					$strSQL .= ")";
					con_update($strSQL);
					$Blok_Id = con_getValue("select max(Blok_Id) from Blok");
				}

				$strSQL = "insert into Stranica_Blok(StBl_Stra_Id, StBl_Blok_Id, StBl_RedPrikaza) ";
				$strSQL .= " values (".$Stra_Id.", ".$Blok_Id.", ".$counter.")";
				con_update($strSQL);
				if (utils_valid($StBl_IdMove)) $counter++;
				//zamenjujemo id kopiranog bloka na novoj stranici sa onim koji smo dobili
				$arrId[$i] = con_getValue("select max(StBl_Id) from Stranica_Blok"); 
			}
		}
		blokFn_changeOrders($Stra_Id, $arrId, $StBl_IdMove, $Dir);
	}

/*Pomeriti niza blokova (StBl_Id-ovima zadatim) u onom smeru 
koji je zadat u odnosu na StBl_IdMove
=========================================================*/
	function blokFn_changeOrders($Stra_Id, $arrId, $StBl_IdMove, $Dir){
		$redPrikaza = 0;
		if (utils_valid($StBl_IdMove) && ($StBl_IdMove != 0)){
			$redPrikaza = con_getValue("select StBl_RedPrikaza from Stranica_Blok where StBl_Id=".$StBl_IdMove);
			if ($Dir == "Down"){
				$temp = con_getValue("select StBl_RedPrikaza from Stranica_Blok where StBl_Valid=1 and  StBl_Stra_Id=".$Stra_Id." and StBl_RedPrikaza > ".$redPrikaza." order by StBl_RedPrikaza asc limit 0, 1");
				if (utils_valid($temp)) $redPrikaza = $temp;
				else $redPrikaza = intval($redPrikaza) + 1;
			}
		} 
//sada imamo redPrikaza
		$notIn = "";
		if (count($arrId) > 0) {
			$notIn = " and StBl_Id not in (";
				for ($i=0;$i<count($arrId);$i++){
					con_update("update Stranica_Blok set StBl_RedPrikaza = ".(intval($redPrikaza) + $i)." where StBl_Id=".$arrId[$i]);
					$notIn .= $arrId[$i].",";
				}
			$notIn = substr($notIn, 0, strlen($notIn)-1).")";
			for ($i=0;$i<count($arrId);$i++){
				con_update("update Stranica_Blok set StBl_RedPrikaza = ".(intval($redPrikaza) + $i)." where StBl_Id=".$arrId[$i]);
			}
		}
		$notIn = substr($notIn, 0, strlen($notIn)-1).")";

		$strSQL = "update Stranica_Blok set StBl_RedPrikaza = StBl_RedPrikaza + ".count($arrId);
		$strSQL .= " where StBl_Stra_Id=".$Stra_Id." and StBl_RedPrikaza >= ".$redPrikaza.$notIn;
//utils_dump($strSQL);
		con_update($strSQL);
	}

/*Vraca odredjeni blok Blok_Id
====================================================*/	
	function blokFn_get($Blok_Id){
		$strSQL = "select Blok_Id, Blok_TipB_Id, Blok_XmlPodaci, Blok_Share, Blok_MetaNaziv, Blok_LastModify,TipB_Naziv,TipB_Dinamic, Blok_Tekst, Blok_ExpiryDate, Blok_PublishDate from Blok, TipBloka where TipB_Id = Blok_TipB_Id and Blok_Id=".$Blok_Id;
		
		$result = con_getResult($strSQL);
		
		$expiry = $result["Blok_ExpiryDate"];
		if (utils_valid($expiry)) $result["Blok_ExpiryDate"] = datetime_format4Database($expiry, true);
		else $result["Blok_ExpiryDate"] = "";

		$publish =  $result["Blok_PublishDate"];
		if (utils_valid($publish)) $result["Blok_PublishDate"] = datetime_format4Database($publish, true);
		else $result["Blok_PublishDate"] = "";

		return $result;
	}
	
/*Brise blok 
====================================================*/	
	function blokFn_delete($Stra_Id, $Blok_Id, $StBl_Id){
		con_update("update Stranica_Blok set StBl_Valid=0 where StBl_Id=".$StBl_Id);
		$blok = blokFn_get($Blok_Id);
		if ($blok["Blok_Share"] == "0")
			con_update("update Blok set Blok_Valid=0 where Blok_Id=".$Blok_Id);
		log_append("Delete", "Blok", $Blok_Id);
	}

/*Radi insert / update bloka
=============================*/	
	function blokFn_save($StraId){
		$filepath = "";
		$strXmlText = "";

		$TipB_Id = utils_requestInt(getPVar("TipB_Id"));
		$tip = tipblok_get($TipB_Id);

		$xmlDoc = xml_loadXML($tip["TipB_Xml"]);
//utils_dump("blok save - start");
		
		$root = xml_documentElement($xmlDoc);
		$childs = xml_childNodes($root);
		for ($i=0;$i<count($childs);$i++){
			$child = $childs[$i];
			$nodeName = xml_nodeName($child);
//utils_dump("blok save - ".$nodeName);
			$inputT = (!is_null(xml_getAttribute($child, "inputType"))) ? xml_getAttribute($child, "inputType") : NULL;

			if ($tip["TipB_Dinamic"] == "1"){//dinamican blok
				switch ($nodeName){
					case "url" : 
						$nodeValue = utils_requestStr(getPVar($nodeName), true, true);
						if (utils_valid($nodeValue)){
							$child = xml_setContent($xmlDoc, $child, rawurlencode($nodeValue));
							$filepath = xml_getContent($child);
						}
						break;
					case "param": 
						$nodeValue = utils_requestStr(getPVar(xml_getAttribute($child, "name")), true, true);
						if ((!is_null($inputT)) && (($inputT == "textDate") || ($inputT == "textDatetime"))){
							if ($inputT == "textDate") $nodeValue = date_getFormDate(xml_getAttribute($child, "name"));
							else $nodeValue = datetime_getFormDate(xml_getAttribute($child, "name"));
						}
							
						if (utils_valid($nodeValue)) $child = xml_setContent($xmlDoc, $child, rawurlencode($nodeValue));
						break;
				}
			} else { //statican blok
				if ($nodeName == "import"){//importovani
					$impName = xml_getAttribute($child, "name");
					$impNodes = xml_childNodes($child);
					for ($j=0;$j<count($impNodes);$j++){
						$impNode = xml_item($impNodes, $j);
						//imena importovanih vrednosti su name att iz import + ime samog noda
						
						$nodeValue = utils_requestStr(getPVar($impName.xml_nodeName($impNode)), true, true);
						$inputT = (!is_null(xml_getAttribute($impNode, "inputType"))) ? xml_getAttribute($impNode, "inputType") : NULL;
						if ((!is_null($inputT)) && (($inputT == "textDate") || ($inputT == "textDatetime"))){
							if ($inputT == "textDate") $nodeValue = date_getFormDate($impName.xml_nodeName($impNode));
							else $nodeValue = datetime_getFormDate($impName.xml_nodeName($impNode)); 
						} else if (!is_null($inputT) && ($inputT == "html-editor")){
							$nodeValue = utils_strictHtml($nodeValue);
						}

						xml_setContent($xmlDoc, $impNode, rawurlencode($nodeValue));
					}
				} else {//obicni
					$nodeValue = utils_requestStr(getPVar($nodeName), true, true);
					if ((!is_null($inputT)) && (($inputT == "textDate") || ($inputT == "textDatetime"))){
						if ($inputT == "textDate") $nodeValue = date_getFormDate($nodeName);
						else $nodeValue = datetime_getFormDate($nodeName); 
					} else if (!is_null($inputT) && ($inputT == "html-editor")){
						$nodeValue = utils_strictHtml($nodeValue);
					}
					xml_setContent($xmlDoc, $child, rawurlencode($nodeValue));
				}
			}
		}
//utils_dump("blok save - parametri postavljeni");
		$Blok_Id = utils_requestInt(getPVar("Blok_Id"));
		if ($tip["TipB_Dinamic"] == "0") {
			$strXmlText = xml_xml($xmlDoc);
			$strXmlText = utils_requestStr(blokFn_Html2Txt(rawurldecode($strXmlText)));
		}
		$xmlVal = xml_xml($xmlDoc);
		$xmlVal = utils_killBadLinks($xmlVal);
		$xmlDoc = xml_loadXML($xmlVal);

		$retBlokId = $Blok_Id;

		if (utils_valid($Blok_Id) && ($Blok_Id != 0)){
			$strSQL = "update Blok set Blok_XmlPodaci='".xml_xml($xmlDoc)."'";
			$Blok_Share = utils_requestStr(getPVar("Blok_Share"));
			$Blok_MetaNaziv = utils_requestStr(getPVar("Blok_MetaNaziv"));
			$Blok_LastModify = utils_requestStr(getPVar("Blok_LastModify"));
			$Blok_PublishDate = datetime_getFormDate("Blok_PublishDate");
			$Blok_ExpiryDate = datetime_getFormDate("Blok_ExpiryDate");

			if (!utils_valid($Blok_Share)) $Blok_Share = "0";
			$blok = blokFn_get($Blok_Id);
			$brokenShare = false; //ako je blok postao nedeljen
	
			switch ($Blok_Share){
				case "1" :	if ($blok["Blok_Share"] == "0"){
								$strSQL .= ", Blok_Share='1', Blok_MetaNaziv='".$Blok_MetaNaziv."'";
							} else {
								$strSQL .= ", Blok_MetaNaziv='".$Blok_MetaNaziv."'";
							}
							break;
				case "0" :	if ($blok["Blok_Share"] == "1"){
								$newBlokId = blokFn_sharedBlockToSingle($Blok_Id, utils_requestInt(getPVar("StBl_Id")), true);
								$Blok_Id = $newBlokId;
								$brokenShare = true;
							}
							break;
				default : break;
			}

			$strSQL .= ",Blok_Tekst = '".$strXmlText."', Blok_LastModify='".date_getMiliseconds()."'";
			if (!utils_valid($Blok_PublishDate)) $strSQL .= ", Blok_PublishDate=null";
			else $strSQL .= ", Blok_PublishDate='".$Blok_PublishDate."'";
			if (!utils_valid($Blok_ExpiryDate)) $strSQL .= ", Blok_ExpiryDate=null";
			else $strSQL .= ", Blok_ExpiryDate='".$Blok_ExpiryDate."'";
			$strSQL .= " where Blok_Id=".$Blok_Id." and Blok_Valid=1";
			if (utils_valid($blok["Blok_LastModify"]) && ($blok["Blok_LastModify"] != 0) && !$brokenShare){
				$strSQL .= " and Blok_LastModify='".$Blok_LastModify."'";
			}
			$affected = con_update($strSQL);
			if (intval($affected) == 0){	?>
	<script>
		alert("<?php echo ocpLabels("Another user has changed object. Your changes are not saved.");?>");
	</script>
<?php		} else {
				log_append("Update", "Blok", $Blok_Id);
			}
		} else {
			$Blok_PublishDate = datetime_getFormDate("Blok_PublishDate");
			$Blok_ExpiryDate = datetime_getFormDate("Blok_ExpiryDate");

			$strSQL = "insert into Blok(Blok_TipB_Id, Blok_XmlPodaci, Blok_Tekst, Blok_LastModify, Blok_PublishDate, Blok_ExpiryDate)";
			$strSQL .= " values(".$TipB_Id.",'".xml_xml($xmlDoc)."','".$strXmlText."', '".date_getMiliseconds()."'";
			if (!utils_valid($Blok_PublishDate)) $strSQL .= ", null";
			else $strSQL .= ", '".$Blok_PublishDate."'";
			if (!utils_valid($Blok_ExpiryDate)) $strSQL .= ", null";
			else $strSQL .= ", '".$Blok_ExpiryDate."'";
			$strSQL .= ")";
			con_update($strSQL);
		
			$newId = con_getValue("select max(Blok_Id) from Blok");

			$max = 0;
			$temp = con_getValue("select max(StBl_RedPrikaza) from Stranica_Blok where StBl_Stra_Id=".$StraId);
			if (utils_valid($temp)){ 
				$max = intval($temp);
				++$max;
			}
			
			$strSQL = "insert into Stranica_Blok(StBl_Stra_Id,StBl_Blok_Id,StBl_RedPrikaza) values (".$StraId.", ".$newId.", ".$max.")";
			con_update($strSQL);

			log_append("Insert", "Blok", $newId);

			$retBlokId = con_getValue("select max(StBl_Id) from Stranica_Blok");
		}
//utils_dump("blok save - end <br>");
		return $retBlokId;
	}

/*Radi insert/update sharovanog bloka
=====================================*/
	function blokFn_saveShare($Blok_Id, $StraId){
		$max = 0;
		$temp = con_getValue("select max(StBl_RedPrikaza) from Stranica_Blok where StBl_Stra_Id=".$StraId);
		if (utils_valid($temp)){ 
			$max = intval($temp);
			++$max;
		}
		con_update("insert into Stranica_Blok(StBl_Stra_Id, StBl_Blok_Id, StBl_RedPrikaza) values (".$StraId.", ".$Blok_Id.", ".$max.")");
		$newId = con_getValue("select max(StBl_Id) from Stranica_Blok");
		log_append("Insert", "Blok", $newId);

		return $newId;
	}

/*Razvezuje ovu konkretnu instancu deljenog bloka, ne sve
=========================================================*/
	function blokFn_sharedBlockToSingle($Blok_Id, $StBl_Id, $TransFree){
		$blok = blokFn_get($Blok_Id);
		$strSQL = "insert into Blok(Blok_TipB_Id, Blok_XmlPodaci, Blok_LastModify, Blok_PublishDate, Blok_ExpiryDate) values(".$blok["Blok_TipB_Id"].",'".$blok["Blok_XmlPodaci"]."', '".date_getMiliseconds()."'";
		if (!utils_valid($blok["Blok_PublishDate"])) $strSQL .= ", null";
		else $strSQL .= ", '".$blok["Blok_PublishDate"]."'";
		if (!utils_valid($blok["Blok_ExpiryDate"])) $strSQL .= ", null";
		else $strSQL .= ", '".$blok["Blok_ExpiryDate"]."'";
		$strSQL .= ")";
		
		con_update($strSQL);
		$newId = con_getValue("select max(Blok_Id) from Blok");
				
		$strSQL = "update Stranica_Blok set StBl_Blok_Id=".$newId." where StBl_Id=".$StBl_Id;
		con_update($strSQL);

		log_append("Insert", "Blok", $newId);

		return $newId;
	}

/*Vraca sve iskoristene meta nazive deljenih blokova (i nevalidnih)
==================================================================*/
	function blokFn_getAllMetaNaziv($Blok_Id){
		if ($Blok_Id == "") return array();

		$strSQL = "select distinct Blok_MetaNaziv from Blok";
		$strSQL .= " where Blok_MetaNaziv is not null and Blok_Id <> ".$Blok_Id;
		return con_getResultsArr($strSQL);
	}
?>