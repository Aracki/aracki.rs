<?php
	$requirePath = $_SERVER['DOCUMENT_ROOT'];

	require_once($requirePath . "/ocp/include/date.php");
	require_once($requirePath . "/ocp/include/string.php");
	require_once($requirePath . "/ocp/include/xml_tools.php");
	require_once($requirePath . "/ocp/siteManager/lib/root.php");
	require_once($requirePath . "/ocp/siteManager/lib/verzija.php");
	require_once($requirePath . "/ocp/siteManager/lib/sekcija.php");
	

	/*Fja koja vraca template strane
	================================*/
	function lib_getTemplateUrl($id) {
		$templateUrl = con_getValue("SELECT Temp_Url FROM Template, Stranica".lib_getStranicaSql($id)." and Temp_Id=Stra_Temp_Id");
		if (!utils_valid($templateUrl)) $templateUrl = "";
		return $templateUrl;
	}
	
	/*Fja koja vraca dodatni meni
	=============================*/
	function lib_getAdditionalMenu($straid) {
		$cn=new dbase(); $cn->open();
		$strSQL = "select Meni_Stra_Id, Meni_Verz_To_Id, Meni_Naziv from DodatniMeni, Verzija, Sekcija, Stranica ";
		$strSQL .= " where Meni_Verz_Id = Verz_Id and Sekc_Verz_Id = Verz_Id and Stra_Sekc_Id = Sekc_Id ";
		$strSQL .= " and Meni_Valid=1 and Verz_Valid=1 and Sekc_Valid=1 and Stra_Valid=1 and Stra_Id = ".$straid;
		$strSQL .= " order by Meni_Verz_To_Id desc, Meni_RedPrikaza  ";
		$meni = array();
		$result = $cn->query($strSQL);
		while($rst=mysql_fetch_assoc($result)) {
			if (utils_valid($rst["Meni_Stra_Id"]) || utils_valid($rst["Meni_Verz_To_Id"])){
				$meni[] = array("Meni_Stra_Id"=>$rst["Meni_Stra_Id"], 
								"Meni_Verz_To_Id"=>$rst["Meni_Verz_To_Id"], 
								"Naziv"=>$rst["Meni_Naziv"]);
			}
		}
		$cn->close();
	
		return $meni;
	}

	/*Fja koja trazi analogiju u verziji koja je poslata
	====================================================*/
	function lib_getAnalogiePageInVersion($straId, $verzId){
		$sql = "SELECT Stra_Id FROM Stranica, Sekcija, Stranica_Stranica";
		$sql .= lib_getStranicaSql(NULL) . " and Sekc_Id = Stra_Sekc_Id and Sekc_Verz_Id = ".$verzId." and StSt_Valid=1";
		$sql .= " and ((StSt_Stra_Id2=".$straId." and Stra_Id=StSt_Stra_Id1) or (StSt_Stra_Id1=".$straId." and Stra_Id=StSt_Stra_Id2))";
		return con_getValue($sql);
	}

	/*Poziva se kada nije setovan id stranice
	====================================================*/	
	function lib_getHomePage(){
		$strId = "";
		$verzId = con_getValue("select Root_Verz_Id from Root where Root_Valid=1");
		if (!utils_valid($verzId)) $verzId = con_getValue("SELECT Verz_Id FROM Verzija WHERE Verz_Valid = 1");
		if (utils_valid($verzId)) $strId = lib_getHomePageVerzija($verzId, true);
		if (!utils_valid($strId)) $strId = con_getValue("SELECT Stra_Id FROM Stranica ".lib_getStranicaSql(null)." limit 0, 1");
		if (!utils_valid($strId)) $strId = null;
		return $strId;
	}

	/*Fja koja za datu stranicu, vraca sekciju i verziju kojoj pripada
	==================================================================*/
	function lib_getStrInfo($straId){
		$strSQL = "SELECT Stra_Naziv, Stra_HtmlTitle, Stra_HtmlKeywords, Stra_Sekc_Id, Sekc_Naziv, Sekc_Verz_Id, Sekc_ParentId";
		$strSQL .= ", Sekc_HtmlKeywords, Verz_HtmlKeywords, Root_HtmlKeywords, Verz_HtmlTitle, Root_HtmlTitle"; 
		$strSQL .= ", Stra_HtmlDescription, Sekc_HtmlDescription, Verz_HtmlDescription, Root_HtmlDescription";
		$strSQL .= " FROM Stranica, Sekcija, Verzija, Root";
		$strSQL .= " WHERE Stra_Id=".$straId." and Sekc_Id = Stra_Sekc_Id and Sekc_Verz_Id=Verz_Id and Verz_Root_Id=Root_Id";

		$sekcVerz = con_getResult($strSQL);
		$sekcVerz["Verz_Id"] =  $sekcVerz["Sekc_Verz_Id"];
		$parentId = $sekcVerz["Sekc_ParentId"];
		$sekcVerz["Stra_Sekc_Naziv"] = $sekcVerz["Sekc_Naziv"];
		$parentNaziv = $sekcVerz["Sekc_Naziv"];
		$newSekcId = $sekcVerz["Stra_Sekc_Id"];
		$dubina = 1;

		//izvlacim sekciju prvog nivoa za sekciju
		while (!is_null($parentId)){
			$record = con_getResult("select Sekc_Id, Sekc_ParentId, Sekc_Naziv from Sekcija where Sekc_Id=".$parentId);
			$parentId = $record["Sekc_ParentId"];
			$newSekcId = $record["Sekc_Id"];
			$parentNaziv = $record["Sekc_Naziv"];
			$dubina++;
		}
		$sekcVerz["Sekc_Id"] = $newSekcId;
		$sekcVerz["Sekc_Naziv"] = $parentNaziv;
		$sekcVerz["Dubina"] = $dubina;
		$sekcVerz["Verz_Home_Page"] = lib_getHomePageVerzija($sekcVerz["Verz_Id"], true);

		return $sekcVerz;
	}

	/* Fja koja za datu stranicu vraca ceo path u Site Manageru
	============================================================*/
	function lib_getStranicaPath($straId, $homeTitle){
		$meni = array();

		$strSQL = "SELECT Stra_Naziv, Sekc_Naziv, Sekc_Id, Sekc_ParentId, Sekc_ExtraParams FROM Sekcija, Stranica";
		$strSQL .= " WHERE Stra_Id=".$straId." and Sekc_Id = Stra_Sekc_Id";
		$rst = con_getResult($strSQL);

		$parentId = $rst["Sekc_ParentId"];
		$newSekcId = $rst["Sekc_Id"];
		$newSekcNaziv = $rst["Sekc_Naziv"];
		$extraXml = $rst["Sekc_ExtraParams"];
		if ($newSekcNaziv != $rst["Stra_Naziv"]) 
			$meni[] =  array("Stra_Naziv"=>$rst["Stra_Naziv"], "Stra_Url"=>utils_getStraLink($straId));

		//ako je externi link
		preg_match_all("/>(([^\/<]+)<)\/extern_link>/", $extraXml, $info);
		if (isset($info[0][0]) && utils_valid($info[0][0])){
			$link = substr($info[0][0], 1, strpos($info[0][0], "</extern_link>")-1);
			$meni[] =  array("Stra_Naziv"=>$rst["Sekc_Naziv"], "Stra_Url"=>rawurldecode($link));
		} else {
			$newSekcHomepage = lib_getHomePageSekcija($newSekcId, false);
			if (utils_valid($newSekcHomepage) && ($newSekcHomepage != 0)){
				$meni[] =  array("Stra_Naziv"=>$rst["Sekc_Naziv"], "Stra_Url"=>utils_getStraLink($newSekcHomepage));
			} else {
				$meni[] =  array("Stra_Naziv"=>$rst["Sekc_Naziv"], "Stra_Url"=>"");
			}
		}

		while (!is_null($parentId)){ //izvlacim sekciju prvog nivoa za sekciju
			$rst = con_getResult("select Sekc_Id, Sekc_ParentId, Sekc_Naziv, Sekc_ExtraParams from Sekcija where Sekc_Id=".$parentId);
			$parentId = $rst["Sekc_ParentId"];
			$newSekcNaziv = $rst["Sekc_Naziv"];
			$extraXml = $rst["Sekc_ExtraParams"];
			
			preg_match_all("/>(([^\/<]+)<)\/extern_link>/", $extraXml, $info);
			if (isset($info[0][0]) && utils_valid($info[0][0])){
				$link = substr($info[0][0], 1, strpos($info[0][0], "</extern_link>")-1);
				$meni[] =  array("Stra_Naziv"=>$rst["Sekc_Naziv"], "Stra_Url"=>rawurldecode($link));
			} else {
				$newSekcHomepage = lib_getHomePageSekcija($rst["Sekc_Id"], 0);
				if (utils_valid($newSekcHomepage) && ($newSekcHomepage != 0)){
					$meni[] =  array("Stra_Naziv"=>$rst["Sekc_Naziv"], "Stra_Url"=>utils_getStraLink($newSekcHomepage));
				} else {
					$meni[] =  array("Stra_Naziv"=>$rst["Sekc_Naziv"], "Stra_Url"=>"");
				}
			}
		}
		if ($newSekcNaziv != $homeTitle)
			$meni[] = array("Stra_Naziv"=>$homeTitle, "Stra_Url"=>utils_getStraLink(menu_getVerzPocetna()));

		return $meni;
	}

	/* Fja koja vraca pocetnu stranu verzije 
	Ako je home pretraga onda se trazi stranica koja ne mora
	da bude vidljiva u meniju
	=======================================================*/
	function lib_getHomePageVerzija($Verz_Id, $home = false){
		$result = con_getResult("select Verz_Sekc_Id, Sekc_Stra_Id from Verzija, Sekcija where Verz_Id=".$Verz_Id." and Sekc_Id=Verz_Sekc_Id");
		$pocSekcId = (isset($result["Verz_Sekc_Id"]))? $result["Verz_Sekc_Id"] : 0; // pocetna sekcija verzije
		$pocStraId = (isset($result["Sekc_Stra_Id"]))? $result["Sekc_Stra_Id"] : 0; // pocetna stranica pocetne sekcije

		if (!utils_valid($pocSekcId)){ //poc sekcija ne postoji 
			$strSQL = "select Stra_Id from Sekcija, Stranica ".lib_getStranicaSql(null);
			$strSQL .= " and Sekc_Verz_Id=".$Verz_Id." and Sekc_ParentId is null and Sekc_Valid=1 and Stra_Sekc_Id=Sekc_Id";
			$strSQL .= " order by Sekc_RedPrikaza, Stra_RedPrikaza limit 0, 1";
			$strId = con_getValue($strSQL);
		} else {//pocetna sekcija postoji
			if (!utils_valid($pocStraId) || ($pocStraId == 0)){ //ne postoji njena pocetna stranica
				$temp = lib_getHomePageSekcija($pocSekcId, $home);	
				if (!utils_valid($temp) || ($temp == 0)){ //spasavaj se ko moze taktika
					$strSQL = "select Stra_Id from Sekcija, Stranica".lib_getStranicaSql(null);
					$strSQL .= " and Sekc_Verz_Id=".$Verz_Id." and Sekc_ParentId is null and Sekc_Valid=1 and Stra_Id = Sekc_Stra_Id";
					$strSQL .= " order by Sekc_RedPrikaza, Stra_RedPrikaza limit 0, 1";
					$strId = con_getValue($strSQL);
				} else {
					$strId = $temp;
				}
			} else { //provera da li je korektna pocetna stranica
				$strId = con_getValue("select Stra_Id from Stranica".lib_getStranicaSql($pocStraId). " limit 0, 1");
			}
		}
		return $strId;
	}

	/*Fja koja vraca pocetnu stranu sekcije
	=======================================*/
	function lib_getHomePageSekcija($Sekc_Id, $home = false){
		$strId = lib_pocStranicaSekcije($Sekc_Id, $home);
		if (!utils_valid($strId)) $strId = lib_searchPodsekcije($Sekc_Id, $home);
		return $strId;
	}

	/*Pomocna fja za prethodnu
	=======================================*/
	function lib_searchPodsekcije($Sekc_Id, $home = false){
		$strId = null;
		$strSQL = "select Sekc_Id, Sekc_Stra_Id from Sekcija where Sekc_ParentId=".$Sekc_Id." and Sekc_Valid=1 order by Sekc_RedPrikaza";
		$cn=new dbase(); $cn->open();
		$cn->query($strSQL);
		while($record=mysql_fetch_assoc($cn->result)){
			if (!utils_valid($record["Sekc_Id"])) break;
			$strId = lib_pocStranicaSekcije($record["Sekc_Id"], $home);
			if (utils_valid($strId)) break;
			else {
				$strId = lib_searchPodsekcije($record["Sekc_Id"], $home);
				if (utils_valid($strId)) break;
			}
		}
		return $strId;
	}

	/*Pomocna fja za prethodnu
	=======================================*/
	function lib_pocStranicaSekcije($Sekc_Id, $home = false){
		$strSQL = "select Sekc_Stra_Id from Sekcija, Stranica ".lib_getStranicaSql(null); 
		$strSQL .= " and Sekc_Id=".$Sekc_Id." and Stra_Id=Sekc_Stra_Id";
		$strId = con_getValue($strSQL); // pocetna stranica sekcije
		if (!utils_valid($strId)){
			$strSQL = "select Stra_Id from Stranica ".lib_getStranicaSql(null)." and Stra_Sekc_Id=".$Sekc_Id;
			if ((is_null($home)) || !$home) $strSQL .=" and Stra_Prikaz=1";
			$strSQL .= " order by Stra_RedPrikaza limit 0, 1";
			$strId = con_getValue($strSQL); 
		}
		return $strId;
	}

/* Fja za type="stra, sekc, verz" i Id vraca extra parametre
==============================================================*/
	function lib_getExtraParams($type, $Id){
		$result = array();

		switch ($type) {
			case "stra": $sql = "SELECT Stra_ExtraParams as Extra FROM Stranica WHERE Stra_Id=".$Id; break;
			case "sekc": $sql = "SELECT Sekc_ExtraParams FROM Sekcija WHERE Sekc_Id=".$Id; break;
			case "verz": $sql = "SELECT Verz_ExtraParams FROM Verzija WHERE Verz_Id=".$Id; break;
		}

		$temp = con_getValue($sql);

		if (!is_null($temp) && utils_valid($temp)){
			$xmlDoc = xml_loadXML($temp);

			if ($xmlDoc){
				$documentElement = xml_documentElement($xmlDoc);
				$nodes = xml_childNodes($documentElement); // vraca mi type

				$type = xml_item($nodes, 0); 
				$nodes = xml_childNodes($type);

				for ($i = 0; $i < count($nodes); $i++) {
					$node = xml_item($nodes, $i); // ovde treba da vrati topMenu

					if (xml_getContent($node) != "") {
						$result[xml_nodeName($node)] = rawurldecode(xml_getContent($node));
					}
				}
			}
		}

		return $result;
	}

	/*Fja koja vraca kesirani meni
	==============================*/
	function lib_getXmlMenu(){
		$strXml = con_getValue("select Xml from SiteMenu where LastModifyOcp=LastModifySite and Id=1");
		if (!utils_valid($strXml)){
			$xmlDoc = xml_createObject();
			$xmlRoot = lib_Root2Xml($xmlDoc, null, -1);
			xml_appendChild($xmlDoc, $xmlRoot);
			$strXml = xml_xml($xmlDoc);

			con_update("update SiteMenu set Xml='".utils_escapeSingleQuote($strXml)."', LastModifySite=LastModifyOcp where Id=1");
		} 
		return $strXml;
	}

	/* =========================================================
		Fja koja gradi kesirani meni
	============================================================*/
	function lib_Root2Xml($xmlDoc, $record, $Dubina){
		$records = root_getAll();
		$record = $records[0];

		$rootNod = xml_createElement($xmlDoc, "root");
		xml_setAttribute($rootNod, "id", $record["Root_Id"]);
		xml_setAttribute($rootNod, "naziv", $record["Root_Naziv"]);
		xml_setAttribute($rootNod, "dubina", $Dubina);
							
		$verzije = root_getAllVerzija($record["Root_Id"]);
		foreach ($verzije as $verzija){
			$versionNod = lib_Version2Xml($xmlDoc, $verzija, ($Dubina+1));	
			xml_appendChild($rootNod, $versionNod);
		}
			
		return $rootNod;
	}					
	
	function lib_Version2Xml($xmlDoc, $record, $Dubina){
		$verzNod = xml_createElement($xmlDoc, "verzija_".$record["Verz_Id"]);
		xml_setAttribute($verzNod, "id", $record["Verz_Id"]);
		if (isset($pocetna)) xml_setAttribute($verzNod, "pocetna", $pocetna);
		xml_setAttribute($verzNod, "naziv", $record["Verz_Naziv"]);
		xml_setAttribute($verzNod, "dubina", $Dubina);
							
		$extraXml = lib_getExtraParams("verz", $record["Verz_Id"]);
		$keys = array_keys($extraXml);
		for ($j=0; $j<count($keys); $j++)
			xml_setAttribute($verzNod, $keys[$j], $extraXml[$keys[$j]]);

		if (isset($extraXml["extern_link"]) && utils_valid($extraXml["extern_link"])){
			xml_setAttribute($verzNod, "link", $extraXml["extern_link"]);	
		} else {
			$pocetna = lib_getHomePageVerzija($record["Verz_Id"]);
			xml_setAttribute($verzNod, "link", utils_getStraLink($pocetna));
		}

//echo($record["Verz_Naziv"] . "<br>");
		$sekcije = verzija_getAllSekcija($record["Verz_Id"]);
		foreach ($sekcije as $sekcija){
			$sectionNod = lib_Section2Xml($xmlDoc, $sekcija, ($Dubina+1));
			if (!is_null($sectionNod))
				xml_appendChild($verzNod, $sectionNod);
		}
		return $verzNod;
	}
	
	function lib_Section2Xml($xmlDoc, $record, $Dubina){
		$pocetna = lib_getHomePageSekcija($record["Sekc_Id"]);
//if ($record["Sekc_Id"] == 7){ echo("'".$pocetna."'"); die();}
		if (utils_valid($pocetna)){
			$sekcNod = xml_createElement($xmlDoc, "sekcija_".$record["Sekc_Id"]);
			xml_setAttribute($sekcNod, "id", $record["Sekc_Id"]);
			xml_setAttribute($sekcNod, "pocetna", $pocetna);
			xml_setAttribute($sekcNod, "naziv", $record["Sekc_Naziv"]);
			xml_setAttribute($sekcNod, "dubina", $Dubina);

			$extraXml = lib_getExtraParams("sekc", $record["Sekc_Id"]);
			$keys = array_keys($extraXml);
			for ($j=0; $j<count($keys); $j++) 
				xml_setAttribute($sekcNod, $keys[$j], $extraXml[$keys[$j]]);

			if (isset($extraXml["extern_link"]) && utils_valid($extraXml["extern_link"])){
				xml_setAttribute($sekcNod, "link",  $extraXml["extern_link"]);
			} else {
				xml_setAttribute($sekcNod, "link", utils_getStraLink($pocetna));
			}
//echo($record["Sekc_Naziv"] . "<br>");
			$stranice = sekcija_getAllStranica($record["Sekc_Id"]);
			foreach ($stranice as $stranica){
				$pageNod = lib_Page2Xml($xmlDoc, $stranica, ($Dubina+1));
				if (!is_null($pageNod)) xml_appendChild($sekcNod, $pageNod);
			}
			$podsekcije = sekcija_getAllPodsekcija($record["Sekc_Id"]);
			foreach ($podsekcije as $podsekcija){
				
				$podsekcNod = lib_Section2Xml($xmlDoc, $podsekcija, ($Dubina+1));
				if (!is_null($podsekcNod)) xml_appendChild($sekcNod, $podsekcNod);
			}
		
			return $sekcNod;
		}	
		return null;
	}	
		
	function lib_Page2Xml($xmlDoc, $record, $Dubina){					
		if (($record["Valid"] == "ok") && ($record["Stra_Prikaz"] == 1)){ 
//echo($record["Stra_Naziv"] . "<br>");
			$straNod = xml_createElement($xmlDoc, "stranica_".$record["Stra_Id"]);
			xml_setAttribute($straNod, "id", $record["Stra_Id"]);
			xml_setAttribute($straNod, "pocetna", $record["Stra_Id"]);
			xml_setAttribute($straNod, "naziv", $record["Stra_Naziv"]);
			xml_setAttribute($straNod, "dubina", $Dubina);
								
			$extraXml = lib_getExtraParams("stra", $record["Stra_Id"]);
			$keys = array_keys($extraXml);
			for ($j=0; $j<count($keys); $j++){
				xml_setAttribute($straNod, $keys[$j], $extraXml[$keys[$j]]);
			}
			
			if (isset($extraXml["extern_link"]) && utils_valid($extraXml["extern_link"])){
				xml_setAttribute($straNod, "link", $extraXml["extern_link"]);
			} else {
				xml_setAttribute($straNod, "link", utils_getStraLink($record["Stra_Id"]));
			}

			return $straNod;
		}
		return null;
	}

	/*Pomocna funkcija
	===================*/
	function lib_getStranicaSql($id){
		$strToday = datetime_format4Database();
		$strSQL = " where Stra_Valid=1 ".((!is_null($id)) ? " and  Stra_Id=".$id : "");
		$strSQL .= " and ((Stra_PublishDate <= '".$strToday."' and Stra_ExpiryDate >= '".$strToday."')";
		$strSQL .= " or (Stra_PublishDate <= '".$strToday."' and Stra_ExpiryDate is null)";
		$strSQL .= " or (Stra_PublishDate is null and '".$strToday."' <= Stra_ExpiryDate)";
		$strSQL .= " or (Stra_PublishDate is null and Stra_ExpiryDate is null))";
		return $strSQL;
	}

	/*Labele na sajtu
	========================*/
	function lib_getLabels($VerzId){
		$result = array();

		$xmlDoc = xml_load($_SERVER['DOCUMENT_ROOT']."/code/labele.xml");

		$jeziciNodes = xml_getElementsByTagname($xmlDoc, "jezik");
		$jezikNode = null;
		if ($jeziciNodes->length > 0) {
			for ($i = 0; $i < $jeziciNodes->length; $i++){
				$jezikNode = $jeziciNodes->item($i);
				if (xml_getAttribute($jezikNode, "id") == $VerzId) break;
			}
		}

		if (isset($jezikNode) && !is_null($jezikNode)){
			$result["skracenica"] = xml_getAttribute($jezikNode, "skracenica");
			$jezikChilds = xml_childNodes($jezikNode);
			for ($i=0; $i<count($jezikChilds); $i++){
				$pageNode = $jezikChilds[$i];
				$result[xml_nodeName($pageNode)] = xml_getAttribute($pageNode, "text");
			}
		}

		return $result;
	}

	/*Parent sekcija za zadati id sekcije
	========================*/
	function lib_getParentSekc($sekc_id){
		$res=con_getResult('select * from Sekcija where Sekc_Id = (select Sekc_ParentId from Sekcija where Sekc_Id='.$sekc_id.' and Sekc_Valid=1 limit 1) and Sekc_Valid=1');
		return $res;
	}

	/*Vraca niz ideova sekcija selektovane stranice
	=======================================*/
	function lib_getParentsIdArray($straId){
		$idsArray = array("stranica_".$straId);

		$result = con_getResult("SELECT Sekc_Id, Sekc_ParentId, Sekc_Verz_Id from Stranica inner join Sekcija on Stra_Sekc_Id = Sekc_Id where Stra_Id=".$straId);
		$idsArray[] = "sekcija_" . $result["Sekc_Id"];
		$parentId = $result["Sekc_ParentId"];

		//izvlacim sekciju prvog nivoa za sekciju
		while (!is_null($parentId)){
			$result = con_getResult("select Sekc_Id, Sekc_ParentId, Sekc_Verz_Id from Sekcija where Sekc_Id=".$parentId);
			$parentId = $result["Sekc_ParentId"];
			$idsArray[] = "sekcija_" . $result["Sekc_Id"];
		}
		$idsArray[] = "verzija_" . $result["Sekc_Verz_Id"]; 
		return array_reverse($idsArray);
	}

?>
