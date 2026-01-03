<?php
	 /*Vraca niz koji sadrzi sve jezike ocp-a
	 ========================================*/
	 function lang_getJezici($sortName, $direction){
		$strSQL = "select * from OcpJezik" . (!is_null($sortName) ? " order by ".$sortName." ".$direction : "");
		return con_getResults($strSQL);
	 }

	 /*Vraca jezik zadatog id-a
	 ========================================*/
	 function lang_getJezik($id){
		return con_getResult("select * from OcpJezik where Id=".$id);
	 }

	 /*Kreira novi jezik u ocp-u, proverava da 
	 li on postoji vec u listi jezika
	 ========================================*/
	 function lang_newJezik($jezik){
		con_update("insert into OcpJezik(Jezik) values('".$jezik."')");	
		$newId = con_getValue("select max(Id) from OcpJezik");
		return $newId;
	 }

	/*Menja naziv jezika u ocp-u, proverava da 
	 li on postoji vec u listi jezika
	 ========================================*/
	 function lang_editJezik($lang){
		$value = con_getValue("select Jezik from OcpJezik where Jezik='".$lang["Jezik"]."' and Id <> ".$lang["Id"]);
		if ($value != $lang["Jezik"]){
			con_update("update OcpJezik set Jezik = '".$lang["Jezik"]."' where Id=".$lang["Id"]);	
			return true;
		}
		return false;
	 }

	 /*Brise jezik u ocp-u zajedno i sa labelama
	 ========================================*/
	 function lang_deleteJezik($id){
		con_update("delete from OcpPrevod where IdJezika=".$id);
		con_update("delete from OcpJezik where Id=".$id);	
	 }

	/*Vraca prevod na osnovu zadatog id-a
	 ========================================*/
	function lang_getPrevodById($idPrevoda){
		return con_getResult("select * from OcpPrevod where Id=".$idPrevoda);
	}

	 /*Vraca prevod labela na neki od jezika
	 ========================================*/
	 function lang_getPrevod($sortName, $direction, $language, $firstLetters, $translation){
		$strSQL = "";

		$results = array();
		if (!utils_valid($translation)){
			$strSQL .= "select OcpLabela.Id as IdLabele, Labela, OcpPrevod.Id as IdPrevoda, Vrednost AS Prevod";
			$strSQL .= " from OcpLabela LEFT OUTER JOIN OcpPrevod on OcpLabela.Id = IdLabele";
			$strSQL .= " where  IdJezika=" . $language;
			if (utils_valid($firstLetters)) $strSQL .= " and Labela Like '".$firstLetters."%'";
			if (utils_valid($sortName)) $strSQL .= " order by ".$sortName." ".$direction;
			$results = con_getResults($strSQL);
		}		

		$strSQL = " select OcpLabela.Id as IdLabele, Labela, OcpPrevod.Id as IdPrevoda, Vrednost as Prevod";
		$strSQL .= "	from 	OcpLabela  LEFT OUTER JOIN ";
		$strSQL .= "		OcpPrevod on OcpLabela.Id = OcpPrevod.IdLabele and IdJezika = ".$language;
		$strSQL .= "	where Vrednost is null";
		if (utils_valid($firstLetters)) $strSQL .= " and Labela Like '".$firstLetters."%'";
		if (utils_valid($sortName)) $strSQL .= " order by ".$sortName." ".$direction;

		if (count($results) > 0)
			$results = utils_matrixSort(array_merge($results, con_getResults($strSQL)), $sortName, $direction);
		else $results = con_getResults($strSQL); 

		return $results;
	 }


	 /*Radi insert prevoda labele u jeziku
	 ========================================*/
	 function lang_insertPrevod($idJezika, $idLabele, $vrednost){
		con_update("insert into OcpPrevod(IdLabele, IdJezika, Vrednost) values (".$idLabele.", ".$idJezika.", '".$vrednost."')");
	 }
	 
	 /*Radi update prevoda labele u jeziku
	 ========================================*/
	 function lang_updatePrevod($idJezika, $idLabele, $vrednost){
		con_update("update OcpPrevod set Vrednost = '".$vrednost."' where IdLabele=".$idLabele." and IdJezika=".$idJezika);
	 }

	  /*Vraca labelu
	 ========================================*/
	 function lang_getLabela($idLabele){
		return con_getResult("select * from OcpLabela where Id=".$idLabele);
	 }

	  /*Vraca labele kao dictionary
	 ==============================*/
	 function lang_getLabeleDict(){
		$dictionary = array();
		$results = con_getResults("select * from OcpLabela");

		for ($i=0; $i<count($results); $i++){
			if (!isset($dictionary[$results[$i]["Labela"]]) || is_null($dictionary[$results[$i]["Labela"]]))
				$dictionary[$results[$i]["Labela"]] = $results[$i]["Id"];
		}
		return $dictionary;
	 }

	/*Vraca niz koji sadrzi sve labele ocp-a
	 ========================================*/
	 function lang_getLabele($sortBy = NULL, $direction = NULL){
		$strSQL = "select * from OcpLabela ";
		if (!is_null($sortBy)) $strSQL .= " order by ".$sortBy." ". $direction;
		$results = con_getResults($strSQL);
		return $results;
	 }

	/*Vraca niz koji sadrzi sve labele ocp-a
	za dato pocetno slovo
	 ========================================*/
	 function lang_getLabeleByLetter($sortBy = NULL, $direction = NULL, $ocpLetter = NULL){
		$strSQL = "select * from OcpLabela ";
		if ($ocpLetter != "0-9")//ako je alfabet znak
			$strSQL .= " where SUBSTRING(Labela, 1, 1) = '".$ocpLetter."'";
		else 
			$strSQL .= " where SUBSTRING(Labela, 1, 1) not in ('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h','i', 'j', 'k', 'l','m', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z')";
		if (!is_null($sortBy)) $strSQL .= " order by ".$sortBy." ". $direction;

		$results = con_getResults($strSQL);
		return $results;
	 }

	 /*Kreira novu labelu u ocp-u, proverava da 
	 li on postoji vec u listi jezika
	 ========================================*/
	 function lang_newLabela($labela){
		if (!utils_valid($labela)) return false;

		$results = con_getResultsArr("select Labela from OcpLabela where Labela='".$labela."'");
		$notfound = true;

		for ($k=0; $k<count($results); $k++){
			if ($results[$k] == $labela) {
				$notfound = false; break;
			}
		}
		
		if ($notfound){
			con_update("insert into OcpLabela (Labela) values('".$labela."')");	

			if (utils_valid(getSVar("ocpLanguage"))){//ovo vazi ako je iskljucena visejezicna
				$newLabelId = con_getValue("select max(Id) from OcpLabela");
				con_update("insert into OcpPrevod (IdLabele, IdJezika, Vrednost) values( ".$newLabelId.", ".getSVar("ocpLanguage").", '".$labela."')");	
				$notfound = false;
			}
		}

		return $notfound;
	 }

	/*Menja naziv jezika u ocp-u, proverava da 
	 li on postoji vec u listi jezika
	 ========================================*/
	 function lang_editLabela($id, $labela){
		$results = con_getResultsArr("select Labela from OcpLabela where Labela='".$labela."' and Id <> ".$id);
		$notfound = true;

		for ($i=0; $i<count($results); $i++){
			if ($results[$i] == $labela) {
				$notfound = false; break;
			}
		}
		if ($notfound){
			con_update("update OcpLabela set Labela = '".$labela."' where Id=".$id);	
		}
		return $notfound;
	 }

	 /*Brise jezik u ocp-u zajedno i sa labelama
	 ========================================*/
	 function lang_deleteLabela($id){
		con_update("delete from OcpPrevod where IdLabele=".$id);
		con_update("delete from OcpLabela where Id=".$id);	
	 }

	/*Vrsi export jezika u xml-formatu
	 ========================================*/
	function lang_exportLanguage($langId, $path){
		//language
		$language = "";
		if (!utils_valid($langId) || ($langId == 0))
			return "You have to choose existing language";
		else {
			$languageArr = lang_getJezik($langId);
			$language = $languageArr["Jezik"];
		}

		//export path
		$doc = (filesize($_SERVER['DOCUMENT_ROOT'] . $path) < 10) ? 
				xml_createObject() : xml_load($_SERVER['DOCUMENT_ROOT'] . $path);

		$newDoc = xml_loadXML("<?xml version=\"1.0\" encoding=\"UTF-8\"?><ocpLanguages/>");
		$rootNode = xml_documentElement($newDoc);

		$test_prom = xml_documentElement($doc);
		if (isset($test_prom) && $test_prom){//nije postojao xml
			$oldRootNode = xml_documentElement($doc);
			//obrisati language ako postoji vec u xml-u
			$mess = NULL;
			$langNodes = xml_childNodes($oldRootNode);
			for ($i=0; $i < count($langNodes); $i++){
				$langNode = $langNodes[$i];
				if (is_null(xml_getAttribute($langNode, "name"))){
					$mess = "Xml file is not properly formated";
					break;
				} else if (xml_getAttribute($langNode, "name") != $language){
					//svaki jezik koji nije onaj koji se exportuje kloniramo i dodamo newDoc-u
					$clonedNode = xml_cloneNode($newDoc, $langNode);
					xml_appendChild($rootNode, $clonedNode);
				}
			}
			if (!is_null($mess)) return $mess;
		}
		
		$doc = null;
		$prevod = lang_getPrevod("Labela", "asc", $langId, null, null);
		$langNode = xml_createElement($newDoc, "language");
		xml_setAttribute($langNode, "name", $language);

		for ($i=0; $i<count($prevod); $i++){
			$labelNode = xml_createElement($newDoc, "label");
			$label = $prevod[$i]["Labela"];
			$translation = $prevod[$i]["Prevod"];

			$label = str_replace(" ", "##$##", $label);
			$label = str_replace("'", "##@##", $label);
			$label = str_replace("\"", "##!##", $label);

			$translation = str_replace(" ", "##$##", $translation);
			$translation = str_replace("'", "##@##", $translation);
			$translation = str_replace("\"", "##!##", $translation);

			xml_setAttribute($labelNode, "name", $label);
			xml_setAttribute($labelNode, "translation", $translation);
			xml_appendChild($langNode, $labelNode);
		}

		xml_appendChild($rootNode, $langNode);
		xml_appendChild($newDoc, $rootNode);

		xml_save($newDoc, $_SERVER['DOCUMENT_ROOT'] . $path);

		return "Export has been successfully finished";
	}

	/*Vrsi import jezika u xml-formatu
	 ========================================*/
	function lang_importLanguage($langId, $language, $path, $append){
		$newLang = false;

		//language
		if ((!utils_valid($langId) || ($langId == 0)) && utils_valid($language)){
			$existLang = con_getValue("select Id from OcpJezik where Jezik='".$language."'");
			if (utils_valid($existLang) && ($existLang != 0)){
				return "Language under this name already exist";
			} else {
				$langId = lang_newJezik($language);
				$newLang = true;
			}
		} else if (utils_valid($langId) && ($langId != 0)){
			$languageArr = lang_getJezik($langId);
			$language = $languageArr["Jezik"];
		} else {
			return "You have to choose existing language or provide new one";
		}
		
		//import path
		$doc = xml_load($_SERVER['DOCUMENT_ROOT'] . $path);
		$langNode = null;
		if (is_null(xml_documentElement($doc))){//nije postojao xml
			return "Xml file is not properly formated";
		} else {
			$rootNode = xml_documentElement($doc);
			//pronaci language ako postoji u xml-u
			$mess = null;
			$langNodes = xml_childNodes($rootNode);
			for ($i=0; $i < count($langNodes); $i++){
				$langNodeTemp = $langNodes[$i];
				if (is_null(xml_getAttribute($langNodeTemp, "name"))){
					$mess = "Xml file is not properly formated";
					break;
				} else {
					if (xml_getAttribute($langNodeTemp, "name") == $language){
						$langNode = $langNodeTemp;
						break;
					}
				}
			}
			if (is_null($langNode)) $mess = "Language is not found in xml file";
			if (!is_null($mess)) return $mess;
		}

		if ($append != "1")
			con_update("delete from OcpPrevod where IdJezika =".$langId);

		$labels = lang_getLabeleDict();
		$labelNodes = xml_childNodes($langNode);
		$mess = null;
		for ($i=0; $i<count($labelNodes); $i++){
			$labelNode = $labelNodes[$i];

			if (is_null(xml_getAttribute($labelNode, "name")) || is_null(xml_getAttribute($labelNode, "translation"))){
				$mess = "Xml file is not properly formated";
				break;
			} else {
				$newLabel = xml_getAttribute($labelNode, "name");
				$newTranslation = xml_getAttribute($labelNode, "translation");

				$newLabel = str_replace("##$##", " ", $newLabel);
				$newLabel = str_replace("##@##", "'", $newLabel);
				$newLabel = str_replace("##!##", "\"", $newLabel);

				$newTranslation = str_replace("##$##", " ", $newTranslation);
				$newTranslation = str_replace("##@##", "'", $newTranslation);
				$newTranslation = str_replace("##!##", "\"", $newTranslation);

				if (!utils_valid($newTranslation)) continue;
				$newTranslation = utils_escapeSingleQuote($newTranslation);
				
				if (isset($labels[$newLabel])){//labela je postojala ranije
					if ($newLang){
						con_update("insert into OcpPrevod(IdJezika, IdLabele, Vrednost) values (".$langId.", ".$labels[$newLabel].", '".$newTranslation."')");
					} else {
						$transId = con_getValue("select Id from OcpPrevod where IdJezika=".$langId." and IdLabele=".$labels[$newLabel]);
						if (utils_valid($transId) && ($transId != 0)){
							con_update("update OcpPrevod set Vrednost = '".$newTranslation."' where Id = ".$transId);
						} else {
							con_update("insert into OcpPrevod(IdJezika, IdLabele, Vrednost) values (".$langId.", ".$labels[$newLabel].", '".$newTranslation."')");
						}
					}
					
				} else {//labela je postojala ranije
					$newLabel = utils_escapeSingleQuote($newLabel);

					con_update("insert into OcpLabela (Labela) values('".$newLabel."')");	
					$newId = con_getValue("select max(Id) from OcpLabela");
					$labels[$newLabel] = $newId;
					con_update("insert into OcpPrevod(IdJezika, IdLabele, Vrednost) values (".$langId.", ".$newId.", '".$newTranslation."')");
				}
			}
		}
		if (!is_null($mess)) return $mess;
		
		return "Import has been successfully finished";
	}


?>
