<?php
	
	//novi link u stari
	define("REWRITE_OLD", "0");
	//novi link u novi sa mapiranjem kod kopiranja sm objekata
	define("REWRITE_REWRITE_COPY", "1");
	//novi link u novi kada se izmeni path stranice
	define("REWRITE_REWRITE_CHANGED_PATH", "4");
	//stari link u novi
	define("OLD_REWRITE", "2");
	//stari link u stari sa mapiranjem kod kopiranja sm objekata
	define("OLD_OLD_COPY", "3");


	function convert_word($value){
		$value = utils_toLower($value);
		
	//	utils_dump($value);
		$value = rawurlencode($value);
//%C4%8D %C4%87 %C5%A1 %C4%91 %C5%BE
		
		$value = str_replace("%C4%8D", "c", $value);
		$value = str_replace("%C4%87", "c", $value);
		$value = str_replace("%C5%A1", "s", $value);
		$value = str_replace("%C4%91", "dj", $value);
		$value = str_replace("%C5%BE", "z", $value);
		$value = str_replace("%27", "", $value);

		$value = rawurldecode($value);

		$value = str_replace("&quot;", "", $value);
		$value = str_replace(" ", "-", $value);
		$value = str_replace("_", "-", $value);
		$value = preg_replace("/[\.\/&?=\%\#]/", "", $value);

		return $value;
	}
	
	/*Konvertuje linkove u blokovima u zavisnosti od moda
	====================================================*/
	function convert_blockLinks($mode, $params = null){
//utils_dump($mode);
		$search_text = ($mode == OLD_REWRITE || $mode == OLD_OLD_COPY) ? "/code/navigate.php" : ".html";
		if ($mode == REWRITE_REWRITE_CHANGED_PATH) $search_text = $params["old"];
		
		$sql = "select distinct Blok_Id, Blok_XmlPodaci from Blok";

		if ($mode == OLD_OLD_COPY || $mode == REWRITE_REWRITE_COPY)
			$sql .= " inner join Stranica_Blok on StBl_Blok_Id=Blok_Id where StBl_Stra_Id=".$params["Id"];
		else $sql .= " where 1=1";
	
		$results = con_getResults($sql . " and Blok_XmlPodaci like '%".rawurlencode($search_text)."%'");

//utils_dump($sql . " and Blok_XmlPodaci like '%".rawurlencode($search_text)."%'");

		for ($i=0; $i<count($results); $i++){
			$xmlDoc = xml_loadXML($results[$i]["Blok_XmlPodaci"]);

			$childs = xml_childNodes(xml_documentElement($xmlDoc));
			for ($j=0;$j<count($childs);$j++){
				$child = $childs[$j];

				if (xml_nodeName($child) == "import"){//importovani
					$impNodes = xml_childNodes($child);
					for ($k=0;$k<count($impNodes);$k++){
						$impNode = xml_item($impNodes, $k);
						$nodeValue = rawurldecode(xml_getContent($impNode));
						
						if ($mode == REWRITE_REWRITE_CHANGED_PATH){
							xml_setContent($xmlDoc, $impNode, rawurlencode(str_replace($params["old"], $params["new"], $nodeValue)));
						} else {
							xml_setContent($xmlDoc, $impNode, rawurlencode(convert_link($mode, $params, $nodeValue)));
						}
					}
				} else {//obicni
					$nodeValue = rawurldecode(xml_getContent($child));
					
					if ($mode == REWRITE_REWRITE_CHANGED_PATH){
						xml_setContent($xmlDoc, $child, rawurlencode(str_replace($params["old"], $params["new"], $nodeValue)));
					} else {
						xml_setContent($xmlDoc, $child, rawurlencode(convert_link($mode, $params, $nodeValue)));
					}
				}
			}
//utils_dump(xml_xml($xmlDoc));
			con_update("update Blok set Blok_XmlPodaci='".xml_xml($xmlDoc)."' where Blok_Id=".$results[$i]["Blok_Id"]);
		}
	}
	
	/*Konvertuje linkove u objektima u zavisnosti od moda
	====================================================*/
	function convert_objectLinks($mode, $params = null){
		$search_text = ($mode == OLD_REWRITE || $mode == OLD_OLD_COPY) ? "/code/navigate.php" : ".html";
		if ($mode == REWRITE_REWRITE_CHANGED_PATH) $search_text = $params["old"];

		$results = con_getResults("select TipoviObjekata.Ime, Polja.ImePolja from Polja inner join TipoviObjekata on TipoviObjekata.Id=Polja.TipId where Polja.TipTabela in ('ShortStrings', 'LongStrings', 'Texts') order by TipoviObjekata.Ime, Polja.ImePolja");
		for ($i=0; $i<count($results); $i++){
			$field = $results[$i]["ImePolja"];
			$type = $results[$i]["Ime"];

			$objects = con_getResults("select Id, ".$field." from ".$type." where ".$field." like '%".$search_text."%'");
			for ($j=0; $j<count($objects); $j++){
				if ($mode == REWRITE_REWRITE_CHANGED_PATH)
					con_update("update ".$type." set ".$field."='".str_replace($params["old"], $params["new"], $objects[$j][$field])."' where Id=".$objects[$j]["Id"]);
				 else 
					con_update("update ".$type." set ".$field."='".convert_link($mode, $params, $objects[$j][$field])."' where Id=".$objects[$j]["Id"]);
			}
		}
	}

	/*Konvertuje stare linkove u njihove rewrite linkove
	====================================================*/
	function convert_link($mode, $params, $text){
		if (!utils_valid($text)) return $text;
		
		$info = array();

		if ($mode == OLD_REWRITE || $mode == OLD_OLD_COPY){
			preg_match_all("/\/code\/navigate\.php\?Id=(\d+$|\d+\'|\d+\")/", $text, $info);
		}else if ($mode == REWRITE_OLD || $mode == REWRITE_REWRITE_COPY){ 
			preg_match_all("/\/(([^\/<>]+)\/){0,5}([^?\/\.]+)\.([0-9]+)\.html/", $text, $info);
		}
//utils_dump($info, 1);

		for ($i=0; $i<count($info[0]); $i++){
			if (!utils_valid($info[0][$i])) continue;
			$address = $info[0][$i];

//utils_dump($address);
			
			$lastChar = "";

			if ($mode == OLD_REWRITE){
				$addressId = substr($address, strpos($address, "?Id=")+4);
				if (!is_numeric(substr($addressId, -1, 1))){
					$lastChar = substr($addressId, -1, 1);
					$addressId = substr($addressId, 0, strlen($addressId)-1);
				}
				$address = con_getValue("select Stra_Link from Stranica where Stra_Id=" . $addressId) . $lastChar;
			} else if ($mode == REWRITE_OLD) {
				$addressId = substr($address, 0, strrpos($address, ".html"));
				$addressId = substr($addressId, strrpos($addressId, ".")+1);
				$address = "/code/navigate.php?Id=".$addressId;
			} else if ($mode == OLD_OLD_COPY){
				$addressId = substr($address, strpos($address, "?Id=")+4);
				if (!is_numeric(substr($addressId, -1, 1))){
					$lastChar = substr($addressId, -1, 1);
					$addressId = substr($addressId, 0, strlen($addressId)-1);
				}
				if (isset($params["StraMap"][$addressId]) && utils_valid($params["StraMap"][$addressId])){
					$addressId = $params["StraMap"][$addressId];
				}
				$address = "/code/navigate.php?Id=".$addressId . $lastChar;
			} else if ($mode == REWRITE_REWRITE_COPY){
				$addressId = substr($address, 0, strrpos($address, ".html"));
				$addressId = substr($addressId, strrpos($addressId, ".")+1);
				if (isset($params["StraMap"][$addressId]) && utils_valid($params["StraMap"][$addressId])){
					$addressId = $params["StraMap"][$addressId];
				}
				$address = con_getValue("select Stra_Link from Stranica where Stra_Id=" . $addressId);
			}
			$text = str_replace($info[0][$i], $address, $text);
		}

		return $text;
	}

?>