<?php
	require_once(utils_getRelativePath() . "/code/lib.php");

	function menu_getSekcVerz(){
		$sekcVerz = $_SESSION["sekcVerz"];
		return $sekcVerz;
	}

//id verzije na kojoj se nalazimo
	function menu_getVerzId(){
		$sekcVerz = $_SESSION["sekcVerz"];
		return $sekcVerz["Sekc_Verz_Id"];
	}

//id sekcije prvog nivoa
	function menu_getSekcFirstLevelId(){
		$sekcVerz = $_SESSION["sekcVerz"];
		return $sekcVerz["Sekc_Id"];
	}

//naziv sekcije prvog nivoa
	function menu_getSekcFirstLevelNaziv(){
		$sekcVerz = $_SESSION["sekcVerz"];
		return $sekcVerz["Sekc_Naziv"];
	}

//id sekcije na kojoj se nalazimo
	function menu_getSekcId(){
		$sekcVerz = $_SESSION["sekcVerz"];
		return $sekcVerz["Stra_Sekc_Id"];
	}

//naziv sekcije na kojoj se nalazimo
	function menu_getSekcNaziv(){
		$sekcVerz = $_SESSION["sekcVerz"];
		return $sekcVerz["Stra_Sekc_Naziv"];
	}

//pocetna stranica verzije na kojoj se nalazimo
	function menu_getVerzPocetna(){
		$sekcVerz = $_SESSION["sekcVerz"];
		return $sekcVerz["Verz_Home_Page"];
	}

//labele verzije
	function menu_getVerzLabels($VerzId){
		return lib_getLabels($VerzId);
	}

	function menu_getVerzLabel($key){
		$VerzLabele = $_SESSION["VerzLabele"]; 
		if (isset($VerzLabele[$key])){
			if ($VerzLabele[$key] == "") 
				return $key . "!";
			return $VerzLabele[$key];
		}
		
		$filename = $_SERVER['DOCUMENT_ROOT']."/code/labele.xml";
		if (file_exists($filename) && is_writable($filename)){
			$xmlDoc = xml_load($filename);

			$jeziciNodes = xml_getElementsByTagname($xmlDoc, "jezik");
			for ($i = 0; $i < $jeziciNodes->length; $i++){
				$jezikNode = $jeziciNodes->item($i);
				
				if (is_null(xml_getFirstElementByTagName($jezikNode, $key))){
					$newNode = xml_createElement($xmlDoc, $key);
					xml_setAttribute($newNode, "text", "");
					xml_appendChild($jezikNode, $newNode);
				}
			}
			
			$xmlTxt = str_replace("\n", "", xml_xml($xmlDoc));
			$xmlTxt = str_replace("\t", "", $xmlTxt);
			$xmlTxt = str_replace(">", ">\n", $xmlTxt);
			$xmlTxt = preg_replace("/(<(?!jezici|jezik|\?xml|\/))/", "\t\t<", $xmlTxt);
			$xmlTxt = preg_replace("/<jezik/", "\t<jezik", $xmlTxt);
			$xmlTxt = preg_replace("/<\/jezik/", "\t</jezik", $xmlTxt);
			$xmlTxt = preg_replace("/<\/jezici/", "</jezici", $xmlTxt);
			if (strlen($xmlTxt) < 40){
				echo ("labele.xml error happend");
				die();
			}
			$handle = fopen($_SERVER['DOCUMENT_ROOT'] . "/code/labele.xml", 'w');
			fwrite($handle, $xmlTxt);
			fclose($handle);
	
			//snimanje bkp verzije labele xml u polje u bazi
			$xmlTxt = str_replace("\n", "", xml_xml($xmlDoc));
			$xmlTxt = utils_escapeSingleQuote($xmlTxt);
			con_update("update Ocp set LabeleXml='".$xmlTxt."' where Id=1");
			//xml_save($xmlDoc, $_SERVER['DOCUMENT_ROOT']."/code/labele.xml");
		}
		
		$VerzLabele[$key] = $key . "!";
		$_SESSION["VerzLabele"] = $VerzLabele; 

		return $key . "!";
	}

	function menu_getStraNaziv(){
		$sekcVerz = $_SESSION["sekcVerz"];
		if (utils_valid($sekcVerz["Stra_HtmlTitle"]))
			return $sekcVerz["Stra_HtmlTitle"];
		return "";
	}

	function menu_getStraHtmlTitle(){
		$sekcVerz = $_SESSION["sekcVerz"];
		$htmlTitle = "";
		if (utils_valid($sekcVerz["Verz_HtmlTitle"])) $htmlTitle .= " | ".$sekcVerz["Verz_HtmlTitle"];
		if (utils_valid($sekcVerz["Root_HtmlTitle"])) $htmlTitle .= " | ".$sekcVerz["Root_HtmlTitle"];
		$htmlTitle = menu_getStraNaziv().$htmlTitle;
		
		return $htmlTitle;
	}

	function menu_getStraHtmlKeywords(){
		$sekcVerz = $_SESSION["sekcVerz"];
		$htmlKeywords = "";

		if (utils_valid($sekcVerz["Stra_HtmlKeywords"])) $htmlKeywords .= $sekcVerz["Stra_HtmlKeywords"].",";
		if (utils_valid($sekcVerz["Sekc_HtmlKeywords"])) $htmlKeywords .= $sekcVerz["Sekc_HtmlKeywords"].",";
		if (utils_valid($sekcVerz["Verz_HtmlKeywords"])) $htmlKeywords .= $sekcVerz["Verz_HtmlKeywords"].",";
		if (utils_valid($sekcVerz["Root_HtmlKeywords"])) $htmlKeywords .= $sekcVerz["Root_HtmlKeywords"].",";

		if (utils_valid($htmlKeywords)) $htmlKeywords = utils_substr($htmlKeywords, 0, utils_strlen($htmlKeywords)-1);

		return $htmlKeywords;
	}

	function menu_getStraHtmlDescription(){
		$sekcVerz = $_SESSION["sekcVerz"];
		$htmlDescription = "";
		
		if (utils_valid($sekcVerz["Stra_HtmlDescription"])) $htmlDescription .= $sekcVerz["Stra_HtmlDescription"].",";
		if (utils_valid($sekcVerz["Sekc_HtmlDescription"])) $htmlDescription .= $sekcVerz["Sekc_HtmlDescription"].",";
		if (utils_valid($sekcVerz["Verz_HtmlDescription"])) $htmlDescription .= $sekcVerz["Verz_HtmlDescription"].",";
		if (utils_valid($sekcVerz["Root_HtmlDescription"])) $htmlDescription .= $sekcVerz["Root_HtmlDescription"].",";

		if (utils_valid($htmlDescription)) $htmlDescription = utils_substr($htmlDescription, 0, utils_strlen($htmlDescription)-1);

		return $htmlDescription;
	}

	//vraca link stranice
	function menu_getStraLink($id){
		return utils_getStraLink($id);
	}

	//home strana sajta
	function menu_getFirstPage(){
		return lib_getHomePage();
	}
?>