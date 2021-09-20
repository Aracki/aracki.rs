<?php
/*
pretraga po 
	root - Naziv, Keywords, Naslov
	verzija - Naziv, Keywords, Naslov
	sekcija - Naziv, Keywords
	stranica - Naziv, Naslov, Keywords
	blokovi - svi tekst, textarea, html editor
	extra parametri - svi tekst, textarea, html editor

	Smesta u session varijable, da se pretraga posle preview-a ne bi radila ponovo
==================================================================================*/
	function search_findByParameterSM($parameter, $matchCase, $attributes){
		$foundObjects = array();

		$encParameter = rawurlencode($parameter);

		$regExpParameter = search_prepare4RegExp($parameter);
	
		//roots
		$changed = 0;
		$strSQL = "select * from Root where (";
		if (is_null($attributes) || $attributes["menuTitle"]){ $strSQL .= "Root_Naziv like '%".$parameter."%' or "; $changed = 1;	}
		if (is_null($attributes) || $attributes["htmlTitle"]){	$strSQL .= "Root_HtmlTitle like '%".$parameter."%' or ";	$changed = 1;	}
		if (is_null($attributes) || $attributes["htmlKeywords"]){ $strSQL .= "Root_HtmlKeywords like '%".$parameter."%' or "; $changed = 1; }
		if (is_null($attributes) || $attributes["htmlDescription"]){ $strSQL .= "Root_HtmlDescription like '%".$parameter."%' or "; $changed = 1; }
		if ($changed){
			$strSQL = substr($strSQL, 0, strlen($strSQL)-4) . ") and Root_Valid=1";
			$roots = con_getResults($strSQL);
			for ($i=0; $i<count($roots); $i++){
				$next = array();

				$next["Type"] = "Root";
				$next["Label"] = "Root";
				$next["Id"] = $roots[$i]["Root_Id"];
				$next["Title"] = $roots[$i]["Root_Naziv"];

				$foundIn =	"";
				if (is_null($attributes) || $attributes["menuTitle"]) 
					$foundIn .= search_formatResult($next, $regExpParameter, $matchCase, "Menu title", "Root_Naziv", $roots[$i]["Root_Naziv"]);
				if (is_null($attributes) || $attributes["htmlTitle"])
					$foundIn .= search_formatResult($next, $regExpParameter, $matchCase, "Root title", "Root_HtmlTitle", $roots[$i]["Root_HtmlTitle"]);
				if (is_null($attributes) || $attributes["htmlKeywords"])
					$foundIn .= search_formatResult($next, $regExpParameter, $matchCase, "Root keywords", "Root_HtmlKeywords", $roots[$i]["Root_HtmlKeywords"]);
				if (is_null($attributes) || $attributes["htmlDescription"])
					$foundIn .= search_formatResult($next, $regExpParameter, $matchCase, "Site description", "Root_HtmlDescription", $roots[$i]["Root_HtmlDescription"]);

				if ($foundIn != ""){
					$next["FoundIn"] = $foundIn;
					$foundObjects[] = $next;
				}
			}
		}

		//verzije
		$changed = 0;
		$strSQL = "select * from Verzija where (";
		if (is_null($attributes) || $attributes["menuTitle"]){ $strSQL .= "Verz_Naziv like '%".$parameter."%' or "; $changed = 1;	}
		if (is_null($attributes) || $attributes["htmlTitle"]){	$strSQL .= "Verz_HtmlTitle like '%".$parameter."%' or ";	$changed = 1;	}
		if (is_null($attributes) || $attributes["htmlKeywords"]){ $strSQL .= "Verz_HtmlKeywords like '%".$parameter."%' or "; $changed = 1; }
		if (is_null($attributes) || $attributes["extraParams"]){ $strSQL .= "Verz_ExtraParams like '%".$encParameter."%' or "; $changed = 1; }
		if (is_null($attributes) || $attributes["htmlDescription"]){ $strSQL .= "Verz_HtmlDescription like '%".$parameter."%' or "; $changed = 1; }
		if ($changed){
			$strSQL = substr($strSQL, 0, strlen($strSQL)-4) . ") and Verz_Valid=1";
			$verzije = con_getResults($strSQL);
			for ($i=0; $i<count($verzije); $i++){
				$next = array();

				$next["Type"] = "Verzija";
				$next["Label"] = "Version";
				$next["Id"] = $verzije[$i]["Verz_Id"];
				$next["Title"] = $verzije[$i]["Verz_Naziv"];

				$foundIn = "";
				if (is_null($attributes) || $attributes["menuTitle"])
					$foundIn .= search_formatResult($next, $regExpParameter, $matchCase, "Menu title", "Verz_Naziv", $verzije[$i]["Verz_Naziv"]);
				if (is_null($attributes) || $attributes["htmlTitle"])
					$foundIn .= search_formatResult($next, $regExpParameter, $matchCase, "Version title", "Verz_HtmlTitle", $verzije[$i]["Verz_HtmlTitle"]); 
				if (is_null($attributes) || $attributes["htmlKeywords"])
					$foundIn .= search_formatResult($next, $regExpParameter, $matchCase, "Version keywords", "Verz_HtmlKeywords", $verzije[$i]["Verz_HtmlKeywords"]);
				if (is_null($attributes) || $attributes["extraParams"])
					$foundIn .= search_formatResult($next, $regExpParameter, $matchCase, "Extra parameters", "Verz_ExtraParams", rawurldecode(strip_tags($verzije[$i]["Verz_ExtraParams"])));
				if (is_null($attributes) || $attributes["htmlDescription"])
					$foundIn .= search_formatResult($next, $regExpParameter, $matchCase, "Version description", "Verz_HtmlDescription", $verzije[$i]["Verz_HtmlDescription"]);
				if ($foundIn != ""){
					$next["FoundIn"] = $foundIn;
					$foundObjects[] = $next;
				}
			}
		}

		//sekcije
		$changed = 0;
		$strSQL = "select * from Sekcija where (";
		if (is_null($attributes) || $attributes["menuTitle"]){ $strSQL .= "Sekc_Naziv like '%".$parameter."%' or "; $changed = 1;	}
		if (is_null($attributes) || $attributes["htmlKeywords"]){ $strSQL .= "Sekc_HtmlKeywords like '%".$parameter."%' or "; $changed = 1; }
		if (is_null($attributes) || $attributes["extraParams"]){ $strSQL .= "Sekc_ExtraParams like '%".$encParameter."%' or "; $changed = 1; }
		if (is_null($attributes) || $attributes["htmlDescription"]){ $strSQL .= "Sekc_HtmlDescription like '%".$parameter."%' or "; $changed = 1; }
		if ($changed){
			$strSQL = substr($strSQL, 0, strlen($strSQL)-4) . ") and Sekc_Valid=1";
			$sekcije = con_getResults($strSQL);
			for ($i=0; $i<count($sekcije); $i++){
				$next = array();

				$next["Type"] = "Sekcija";
				$next["Label"] = "Section";
				$next["Id"] = $sekcije[$i]["Sekc_Id"];
				$next["Title"] = $sekcije[$i]["Sekc_Naziv"];

				$foundIn =	"";
				if (is_null($attributes) || $attributes["menuTitle"])
					$foundIn .= search_formatResult($next, $regExpParameter, $matchCase, "Menu title", "Sekc_Naziv", $sekcije[$i]["Sekc_Naziv"]); 
				if (is_null($attributes) || $attributes["htmlKeywords"])
					$foundIn .= search_formatResult($next, $regExpParameter, $matchCase, "Section keywords", "Sekc_HtmlKeywords", $sekcije[$i]["Sekc_HtmlKeywords"]); 
				if (is_null($attributes) || $attributes["extraParams"])
					$foundIn .= search_formatResult($next, $regExpParameter, $matchCase, "Extra parameters", "Sekc_ExtraParams", rawurldecode(strip_tags($sekcije[$i]["Sekc_ExtraParams"])));
				if (is_null($attributes) || $attributes["htmlDescription"])
					$foundIn .= search_formatResult($next, $regExpParameter, $matchCase, "Section description", "Sekc_HtmlDescription", $sekcije[$i]["Sekc_HtmlDescription"]);
				if ($foundIn != ""){
					$next["FoundIn"] = $foundIn;
					$foundObjects[] = $next;
				}
			}
		}
		
		//stranice
		$changed = 0;
		$strSQL = "select * from Stranica where (";
		if (is_null($attributes) || $attributes["menuTitle"]){ $strSQL .= "Stra_Naziv like '%".$parameter."%' or "; $changed = 1;	}
		if (is_null($attributes) || $attributes["htmlTitle"]){	$strSQL .= "Stra_HtmlTitle like '%".$parameter."%' or ";	$changed = 1;	}
		if (is_null($attributes) || $attributes["htmlKeywords"]){ $strSQL .= "Stra_HtmlKeywords like '%".$parameter."%' or "; $changed = 1; }
		if (is_null($attributes) || $attributes["extraParams"]){ $strSQL .= "Stra_ExtraParams like '%".$encParameter."%' or "; $changed = 1; }
		if (is_null($attributes) || $attributes["htmlDescription"]){ $strSQL .= "Stra_HtmlDescription like '%".$parameter."%' or "; $changed = 1; }
		if ($changed){
			$strSQL = substr($strSQL, 0, strlen($strSQL)-4) . ") and Stra_Valid=1";
			//utils_dump($strSQL);
			$stranice = con_getResults($strSQL);
			for ($i=0; $i<count($stranice); $i++){
				$next = array();

				$next["Type"] = "Stranica";
				$next["Label"] = "Page";
				$next["Id"] = $stranice[$i]["Stra_Id"];
				$next["Title"] = $stranice[$i]["Stra_Naziv"];

				$foundIn = "";
				if (is_null($attributes) || $attributes["menuTitle"])
					$foundIn .= search_formatResult($next, $regExpParameter, $matchCase, "Menu title", "Stra_Naziv", $stranice[$i]["Stra_Naziv"]); 
				if (is_null($attributes) || $attributes["htmlKeywords"])
					$foundIn .= search_formatResult($next, $regExpParameter, $matchCase, "Page keywords", "Stra_HtmlKeywords", $stranice[$i]["Stra_HtmlKeywords"]); 
				if (is_null($attributes) || $attributes["htmlTitle"])
					$foundIn .= search_formatResult($next, $regExpParameter, $matchCase, "Page title", "Stra_HtmlTitle", $stranice[$i]["Stra_HtmlTitle"]); 
				if (is_null($attributes) || $attributes["extraParams"])
					$foundIn .= search_formatResult($next, $regExpParameter, $matchCase, "Extra parameters", "Stra_ExtraParams", rawurldecode(strip_tags($stranice[$i]["Stra_ExtraParams"])));
				if (is_null($attributes) || $attributes["htmlDescription"])
					$foundIn .= search_formatResult($next, $regExpParameter, $matchCase, "Page description", "Stra_HtmlDescription", $stranice[$i]["Stra_HtmlDescription"]); 
				if ($foundIn != ""){
					$next["FoundIn"] = $foundIn;
					$foundObjects[] = $next;
				}
			}
		}
		//blokovi
		$blokovi = con_getResults("select Blok.*, TipB_Naziv from Blok, TipBloka where Blok_XmlPodaci like  '%".$encParameter."%' and Blok_Valid=1 and TipB_Id = Blok_TipB_Id");
		for ($i=0; $i<count($blokovi); $i++){
			$next = array();
			
			$next["Type"] = "Blok";
			$next["Label"] = "Block";
			$next["Id"] = $blokovi[$i]["Blok_Id"];
			$next["Title"] = $blokovi[$i]["TipB_Naziv"];
			$foundIn =	search_formatResult($next, $regExpParameter, $matchCase, "Text", "Blok_XmlPodaci", rawurldecode(strip_tags($blokovi[$i]["Blok_XmlPodaci"])));
			if ($foundIn != ""){
				$next["FoundIn"] = $foundIn;
				$next["Stra_Id"] = con_getValue("select StBl_Stra_Id from Stranica_Blok where StBl_Valid=1 and StBl_Blok_Id=".$blokovi[$i]["Blok_Id"]);
				$foundObjects[] = $next;
			}
		}

		return $foundObjects;		
	}

/*
Pretraga po objektima u obj manageru, polja tipa Strings i Texts
Smesta u session varijable, da se pretraga posle preview-a ne bi radila ponovo
==============================================================================*/
	function search_findByParameterOM($parameter, $matchCase){
		$regExpParameter = search_prepare4RegExp($parameter);
		
		$foundObjects = array();

		$types = tipobj_getAllTypesWithFields(NULL, NULL);

//utils_dump($types, 1);

		for ($i=0; $i<count($types); $i++){
			$type = $types[$i];
			
//utils_dump($type);

			$fields = polja_getStringFields($type["Id"]);
			$tempSQL = "";
			for ($j=0; $j<count($fields); $j++)
				$tempSQL .= $fields[$j]." like '%".$parameter."%' or ";

			if (utils_valid($tempSQL)){
				$tempSQL = "(" . substr($tempSQL, 0, strlen($tempSQL)-4) . ")";

				$strSQL = "select * from " . $type["Ime"] . " where " . $tempSQL . " and Valid=1";

//utils_dump($strSQL);

				$results = con_getResults($strSQL);
				if (count($results) > 0){
					$xmlFields = search_getFieldLabels($type["Ime"]);

					for ($k=0; $k<count($results); $k++){
						$next = array();

						$object = obj_get($type["Ime"], $results[$k]["Id"], 1);
	
						$next["Type"] = $type["Ime"];
						$next["Label"] = $type["Labela"];
						$next["Id"] = $results[$k]["Id"];
						$next["Title"] = xml_generateRecordIdenString($type["Ime"], $object, 1);
						
						$foundIn = "";
						for ($l=0; $l<count($fields); $l++){
							//problem iz html-editora imamo %0D%0A na kraju
							if ($xmlFields[$fields[$l]]["inputType"] == "html-editor"){
								$temp = rawurlencode($results[$k][$fields[$l]]);
								$temp = str_replace("%0D%0A", "", $temp);
								$results[$k][$fields[$l]] = rawurldecode($temp);
							}
							$foundIn .= search_formatResult($next, $regExpParameter, $matchCase, $xmlFields[$fields[$l]]["label"], $fields[$l], $results[$k][$fields[$l]]);
						}

						if ($foundIn != ""){
							$next["FoundIn"] = $foundIn;
							$foundObjects[] = $next;
						}
					}
				}
			}
		}

		return $foundObjects;
	}

/*
Update-uje sve objekte, iz Sessiona se izvlace properties koji
imaju parametar pretrage u sebi
==============================================================================*/
	function search_replaceAll($parameter, $matchCase, $replacement){
		foreach(getSVar("ocp_searchResults") as $nextType => $typesArr){
			//utils_dump("Tip je ".$nextType);
			foreach ($typesArr as $nextId => $typesIdArr){
				//utils_dump("Id je ".$nextId);
				search_replaceObject($nextType, $nextId, $parameter, $matchCase, $replacement);
			}
		}
	}

/*
Update-uje se jedan po jedan objekat, iz Sessiona se izvlace properties koji
imaju parametar pretrage u sebi
==============================================================================*/
	function search_replaceObject($type, $id, $parameter, $matchCase, $replacement){
		$sessVar = $_SESSION["ocp_searchResults"];

		//utils_dump("Tip je '".$type."' Id je '".$id."'");
		//utils_dump($sessVar, 1);
		//utils_dump(array_keys($_SESSION["ocp_searchResults"]), 1);

		if (!isset($sessVar[$type]) || is_null($sessVar[$type])) return;
		if (!isset($sessVar[$type][$id]) || is_null($sessVar[$type][$id])) return;

		$properties = $sessVar[$type][$id];
		//utils_dump($properties, 1);

		switch ($type){
			case "Root": 
				$root = root_get($id);
				for ($i=0; $i<count($properties); $i++)
					$root[$properties[$i]] = search_regReplace($root[$properties[$i]], $parameter, $matchCase, $replacement);
				root_edit($root);
				break;
			case "Verzija":
				$verzija = verzija_get($id);
				for ($i=0; $i<count($properties); $i++){
					if ($properties[$i] == "Verz_ExtraParams")
						$verzija[$properties[$i]] = search_regReplaceXml(0, $verzija[$properties[$i]], $parameter, $matchCase, $replacement);
					else
						$verzija[$properties[$i]] = search_regReplace($verzija[$properties[$i]], $parameter, $matchCase, $replacement);
				}
				verzija_edit($verzija);
				break;
			case "Sekcija":  
				$sekcija = sekcija_get($id);
				for ($i=0; $i<count($properties); $i++){
//utils_dump($properties[$i]." ".$replacement);
					if ($properties[$i] == "Sekc_ExtraParams")
						$sekcija[$properties[$i]] = search_regReplaceXml(0, $sekcija[$properties[$i]], $parameter, $matchCase, $replacement);
					else 
						$sekcija[$properties[$i]] = search_regReplace($sekcija[$properties[$i]], $parameter, $matchCase, $replacement);	
				}
				sekcija_edit($sekcija);
				break;
			case "Stranica": 
				$stranica = stranica_get($id);
				for ($i=0; $i<count($properties); $i++){
//					utils_dump($properties[$i]." ".$replacement);
					if ($properties[$i] == "Stra_ExtraParams")
						$stranica[$properties[$i]] = search_regReplaceXml(0, $stranica[$properties[$i]], $parameter, $matchCase, $replacement);
					else 
						$stranica[$properties[$i]] = search_regReplace($stranica[$properties[$i]], $parameter, $matchCase, $replacement);
//					utils_dump($stranica[$properties[$i]]);
				}
				stranica_edit($stranica);
				break;
			case "Blok": 
				$blok = blokFn_get($id);
				$blok["Blok_XmlPodaci"] = search_regReplaceXml(1, $blok["Blok_XmlPodaci"], $parameter, $matchCase, $replacement);
//utils_dump($blok["Blok_XmlPodaci"]);
				con_update("Update Blok set Blok_XmlPodaci='".$blok["Blok_XmlPodaci"]."' where Blok_Id=".$id);
				break;
			default:
				$objekat = obj_get($type, $id);
				for ($i=0; $i<count($properties); $i++){
//utils_dump("Property je ".$properties[$i]);
					$objekat[$properties[$i]] = search_regReplace($objekat[$properties[$i]], $parameter, $matchCase, $replacement);	
//utils_dump("Vrednost je ".$objekat[$properties[$i]]);
				}
				obj_update($type, $objekat, obj_get($type, $id), 0, 1);
				break;
		}
	}

/*
Prikazuje tacno mesto na kom je pronadjen podstring parametar
Smesta u session varijable, da se pretraga posle preview-a ne bi radila ponovo
==============================================================================*/
	function search_formatResult($searchObject, $parameter, $matchCase, $propertyLabel, $property, $value){
		//$value = strip_tags($value);
//utils_dump($parameter ."   ". $value);

		if ((substr_count($property,  "_ExtraParams") == 0) && ($property != "Blok_XmlPodaci")){//nisu extraparametri ili xml iz bloka
			//ovo radim zbog nasih slova
			$value = rawurlencode($value);
			$parameter = rawurlencode($parameter);
		}
		

//		utils_dump("Match case ".$matchCase);
//		utils_dump("Value ".$value);
//		utils_dump("Parameter ".$parameter);
		
		$reArray = ($matchCase == "1") ? 
			preg_split("/" . $parameter . "/", $value, 3) : preg_split("/" . $parameter . "/i", $value, 3);
		
//utils_dump($reArray, 1);

		$retVal = "";
		$limit = 20;
		if (count($reArray) > 1){
			$sessVar = getSVar("ocp_searchResults");
			if (!isset($sessVar[$searchObject["Type"]]) || is_null($sessVar[$searchObject["Type"]])){
				$typeArray = array();
				$typeArray[$searchObject["Id"]] = array($property);
				$_SESSION["ocp_searchResults"][$searchObject["Type"]] = $typeArray;
			} else {
				$typeArray = $sessVar[$searchObject["Type"]];
				if (!isset($typeArray[$searchObject["Id"]]) || is_null($typeArray[$searchObject["Id"]])){
					$typeArray[$searchObject["Id"]] = array($property);
				} else {
					if (!in_array($property, $typeArray[$searchObject["Id"]])){
						$typeArray[$searchObject["Id"]][] = $property;
					}
				}
				$_SESSION["ocp_searchResults"][$searchObject["Type"]] = $typeArray;
			}

			//utils_dump($_SESSION["ocp_searchResults"], 1);

			$leftContext = rawurldecode($reArray[0]);
			$leftContext = (strlen($leftContext) > $limit)  ?  substr($leftContext, strlen($leftContext)-$limit) : $leftContext;
			$leftContext = strip_tags($leftContext);

			$matches = array();
			($matchCase == "1") ? ereg($parameter, $value, $matches) : eregi($parameter, $value, $matches);
			$lastMatch = ((count($matches)>0)) ? rawurldecode($matches[0]) : rawurldecode($parameter);
			
			$rightContext = rawurldecode(substr($value, strlen($reArray[0])+strlen(rawurldecode($lastMatch))));
			$rightContext = (strlen($rightContext) > $limit) ? substr($rightContext, 0, $limit) : $rightContext;
			$rightContext = strip_tags($rightContext);

			$retVal = "<br><b>" . ocpLabels($propertyLabel) ."</b>: " . $leftContext . "<span class='ocp_opcije_obavezno'>" . $lastMatch . "</span>" . $rightContext;

		}
		return $retVal;
	}

/*
Fja koja u parametru pretrage zamenjuje sve sporne reg exp izraze njihovim 
heksadecimalnim kodovima
==============================================================================*/
	function search_prepare4RegExp($value){
		$notAllowedChars = array("\\", "^", "$", "*", "+", "?", ".", "(", ")", "|", "{", "}", "[", "]", ":", "/");
		
		for ($i=0; $i<count($notAllowedChars); $i++){
//			if ($notAllowedChars[$i] == "\\"){
//				$value = str_replace($notAllowedChars[$i], "@&&@&&", $value);
//			} else{
//				$value = str_replace($notAllowedChars[$i],"\\x".utils_dec2hex(ord($notAllowedChars[$i])), $value);
				$value = str_replace($notAllowedChars[$i],"\\".$notAllowedChars[$i], $value);
//			}
		}
//		$value = str_replace("@&&@&&", "\\x".utils_dec2hex(ord("\\")), $value);

		return $value;
	}

/*
Prikazuje tacno mesto na kom je pronadjen podstring parametar
Smesta u session varijable, da se pretraga posle preview-a ne bi radila ponovo
==============================================================================*/
	function search_regReplace($value, $parameter, $matchCase, $replacement){
		$value = rawurlencode($value);
		$parameter = rawurlencode($parameter);
		$replacement = rawurlencode($replacement);

		if ($matchCase == "1"){
			$value = ereg_replace( $parameter, $replacement, $value);
		} else {
			$value = eregi_replace( $parameter, $replacement, $value);
		}

		return rawurldecode($value);
	}

/*Spec za xml
Prikazuje tacno mesto na kom je pronadjen podstring parametar
Smesta u session varijable, da se pretraga posle preview-a ne bi radila ponovo
==============================================================================*/
	function search_regReplaceXml($blok, $value, $parameter, $matchCase, $replacement){
		$xml = xml_loadXML($value);

		$docTag = xml_documentElement($xml);
		if (!$blok){
			$docTagChilds = xml_childNodes($docTag);
			$docTag = $docTagChilds[0];
		}
		$childs = xml_childNodes($docTag);
		for ($j=0; $j<count($childs); $j++){
			$nextChild = $childs[$j];

			if (xml_nodeName($nextChild) == "import"){
				$childsImp = xml_childNodes($nextChild);
				for ($k = 0; $k < count($childsImp); $k++){
					$nextChild1 = $childsImp[$k];
					$content = rawurldecode(xml_getContent($nextChild1));
					$content = search_regReplace($content, $parameter, $matchCase, $replacement);
					xml_setContent($xml, $nextChild1, rawurlencode($content));
				}
			} else {
				$content = rawurldecode(xml_getContent($nextChild));
				$content = search_regReplace($content, $parameter, $matchCase, $replacement);
				xml_setContent($xml, $nextChild, rawurlencode($content));
			}
		}

		return xml_xml($xml);
	}

/*
Fja koja sluzi samo da se izvuku labele polja u om zbog lepseg prikaza
==============================================================================*/
	function search_getFieldLabels($imeTipa){
		$xml =  xml_getNode($imeTipa, 1);

		if (is_null($xml)) return NULL;
		$result = array();
		
		$fieldList = xml_getElementsByTagName($xml, "field");
		for ($j=0; $j<count($fieldList); $j++){
			$nextField = $fieldList->item($j);
			$childs = xml_childNodes($nextField);
			$name = "";
			$label = "";
			$inputType = "";
			for ($k=0; $k < count($childs); $k++){
				if (xml_nodeName($childs[$k]) == "name") $name = xml_getContent($childs[$k]);
				if (xml_nodeName($childs[$k]) == "label") $label = xml_getContent($childs[$k]);
				if (xml_nodeName($childs[$k]) == "inputType") $inputType = xml_getContent($childs[$k]);
			}
			if (utils_valid($name) && utils_valid($label) && utils_valid($inputType)) 
				$result[$name] = array("label" => $label, "inputType" => $inputType);
		}

		return $result;
	}
?>