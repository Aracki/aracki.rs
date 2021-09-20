<?php

	/*Brise odredjeni file i menja sve linkove na njega
	===================================================*/
	function file_delete($file, $killLink, $replaceLink){
		global $webRoot;

		unlink($webRoot ."/". $file);

		if ((utils_valid($killLink) && ($killLink == "1")) || utils_valid($replaceLink)){
			$objects = file_getAllLinked($file);

			for ($i=0;$i<count($objects);$i++){
				$next = $objects[$i];

				if ($next["Type"] == "Blok"){//blok je u pitanju
					$searchLink = rawurlencode($file . "\"");

					$strSQL = "select Blok_Id, Blok_XmlPodaci from Blok, Stranica_Blok";
					$strSQL .= " where Blok_Id=StBl_Blok_Id and StBl_Stra_Id=".$next["Id"];
					$strSQL .= " and Blok_Valid=1 and StBl_Valid=1 and Blok_XmlPodaci like '%".rawurlencode($file)."%'";	

					$blokovi = con_getResults($strSQL);
					for ($j=0;$j<count($blokovi);$j++){
						$blok = $blokovi[$j];

						if (utils_valid($killLink) && ($killLink == "1")){
							$xmlPodaci = $blok["Blok_XmlPodaci"];

							while (is_integer(strpos($xmlPodaci, $searchLink))){//brisanje linkova fila u a tagovima
								$nextIndex = strpos($xmlPodaci, $searchLink);
								$xmlPodaciLeft = substr($xmlPodaci, 0, $nextIndex);
								$xmlPodaciRight = substr($xmlPodaci, $nextIndex + 1);
//utils_dump("Next index ".$nextIndex);
//utils_dump("********************************");
//utils_dump("Left ".$xmlPodaciLeft);
//utils_dump("********************************");
//utils_dump("Right ".$xmlPodaciRight);
								
								$escapedEndATag = rawurlencode("</A>");
								$escapedEndaTag = rawurlencode("</a>");
								$escapedEndStartATag = rawurlencode(">");

								$startATag = utils_lastIndexOf($xmlPodaciLeft, rawurlencode("<A"));
								$startaTag = utils_lastIndexOf($xmlPodaciLeft, rawurlencode("<a"));
								$startATag = max($startATag, $startaTag);
//utils_dump("StartATag ".$startATag);

								$endStartATag = strpos($xmlPodaciRight, $escapedEndStartATag);
								$endATag = strpos($xmlPodaciRight, $escapedEndATag); 
								$endaTag = strpos($xmlPodaciRight, $escapedEndaTag);
								
								if (!is_integer($endATag)) $endATag = $endaTag;
								else if (is_integer($endaTag)) $endATag = min($endATag, $endaTag);
//utils_dump("endATag ".$endATag);
//utils_dump("endStartATag ".$endStartATag);

								if (is_integer($startATag) &&  is_integer($endStartATag) && is_integer($endATag) && ($endStartATag < $endATag)){

//utils_dump("I " . rawurldecode(substr($xmlPodaciLeft, 0, $startATag)));
//utils_dump("********************************");
//utils_dump("II " . rawurldecode(substr($xmlPodaciRight, $endStartATag + strlen($escapedEndStartATag), $endATag - ($endStartATag + strlen($escapedEndStartATag)))));
//utils_dump("********************************");
//utils_dump("III " . rawurldecode(substr($xmlPodaciRight, $endATag + strlen($escapedEndATag))));
									$tempEndATag = $endStartATag + strlen($escapedEndStartATag);
									$xmlPodaci = substr($xmlPodaciLeft, 0, $startATag) . 
										substr($xmlPodaciRight, $tempEndATag, $endATag - $tempEndATag) . 
										substr($xmlPodaciRight, $endATag + strlen($escapedEndATag));

								} 
							}
//utils_dump(rawurldecode($xmlPodaci));
							//brisanje u tagovima koji nisu html editor
							$xmlPodaci = str_replace(">".rawurlencode($file)."<", "><", $xmlPodaci);

							$blok["Blok_XmlPodaci"] = $xmlPodaci;
						} else {
							$blok["Blok_XmlPodaci"] = str_replace($searchLink, rawurlencode($replaceLink . "\""), $blok["Blok_XmlPodaci"]);
						}
//utils_dump("UPDATE Blok SET Blok_XmlPodaci = '".$blok["Blok_XmlPodaci"]."' WHERE Blok_Id = ".$blok["Blok_Id"]);
						con_update("UPDATE Blok SET Blok_XmlPodaci = '".$blok["Blok_XmlPodaci"]."' WHERE Blok_Id = ".$blok["Blok_Id"]);
					}
				} else {
					$strSQL = "select ".$next["Field"]." from " . $next["Type"];
					$strSQL .= " where Id=".$next["Id"]." and ".$next["Field"]." like '%".$file."%'";	
					
					$searchLink = $file;

					$objekti = con_getResults($strSQL);
					for ($j=0;$j<count($objekti);$j++){
						$objekat = $objekti[$j];

						if (utils_valid($killLink) && ($killLink == "1")){
							$field = $objekat[$next["Field"]];

							while (is_integer(strpos($field, $searchLink))){
								$nextIndex = strpos($field, $searchLink);
								$fieldLeft = substr($field, 0, $nextIndex);
								$fieldRight = substr($field, $nextIndex + 1);
//utils_dump("Next index ".$nextIndex);
//utils_dump("********************************");
//utils_dump("Left ".$fieldLeft);
//utils_dump("********************************");
//utils_dump("Right ".$fieldRight);
								
								$escapedEndATag = "</A>";
								$escapedEndaTag = "</a>";
								$escapedEndStartATag = ">";

								$startATag = utils_lastIndexOf($fieldLeft, "<A");
								$startaTag = utils_lastIndexOf($fieldLeft, "<a");
								$startATag = max($startATag, $startaTag);
//utils_dump("StartATag ".$startATag);

								$endStartATag = strpos($fieldRight, $escapedEndStartATag);
								$endATag = strpos($fieldRight, $escapedEndATag); 
								$endaTag = strpos($fieldRight, $escapedEndaTag);
								
								if (!is_integer($endATag)) $endATag = $endaTag;
								else if (is_integer($endaTag)) $endATag = min($endATag, $endaTag);
//utils_dump("endATag ".$endATag);
//utils_dump("endStartATag ".$endStartATag);

								if (is_integer($startATag) &&  is_integer($endStartATag) && is_integer($endATag) && ($endStartATag < $endATag)){

//utils_dump("I " . rawurldecode(substr($fieldLeft, 0, $startATag)));
//utils_dump("********************************");
//utils_dump("II " . rawurldecode(substr($fieldRight, $endStartATag + strlen($escapedEndStartATag), $endATag - ($endStartATag + strlen($escapedEndStartATag)))));
//utils_dump("********************************");
//utils_dump("III " . rawurldecode(substr($fieldRight, $endATag + strlen($escapedEndATag))));
									$tempEndATag = $endStartATag + strlen($escapedEndStartATag);
									$field = substr($fieldLeft, 0, $startATag) . 
										substr($fieldRight, $tempEndATag, $endATag - $tempEndATag) . 
										substr($fieldRight, $endATag + strlen($escapedEndATag));

								} else { //nije u html-editoru vec u tagovima, kao link slike npr.
									$field = $fieldLeft . substr($fieldRight, strlen($searchLink));
								}
							}
//utils_dump($field);
							$objekat[$next["Field"]] = $field;
						} else {
							$objekat[$next["Field"]] = str_replace($searchLink, $replaceLink, $objekat[$next["Field"]]);
						}
//utils_dump("UPDATE ".$next["Type"]." SET ".$next["Field"]." = '".$objekat[$next["Field"]]."' WHERE Id = ".$next["Id"]);
						con_update("UPDATE ".$next["Type"]." SET ".$next["Field"]." = '".$objekat[$next["Field"]]."' WHERE Id = ".$next["Id"]);
					}
				}
			}
		}

		return ocpLabels("File is deleted");
	}

/*Vraca sve stranice koje imaju link ka stranici Stra_Id
===================================================*/
	function file_getAllLinked($file){
		$strSQL = "select distinct Stra_Id, Sekc_Naziv, Stra_Naziv, Blok_XmlPodaci";
		$strSQL .= " from Blok, Stranica_Blok, Stranica, Sekcija";
		$strSQL .= " where Blok_Id=StBl_Blok_Id and StBl_Stra_Id=Stra_Id and Sekc_Id=Stra_Sekc_Id";
		$strSQL .= " and Blok_Valid=1 and StBl_Valid=1 and Stra_Valid=1 and Blok_XmlPodaci like '%". rawurlencode($file)."%'";
		$strSQL .= " order by Stra_Id";

		$results = array();
		$temp = con_getResults($strSQL);
		$lastStraId = 0;
		for ($i=0; $i<count($temp); $i++){
			$xml = rawurldecode($temp[$i]["Blok_XmlPodaci"]);
			if ($lastStraId != $temp[$i]["Stra_Id"] && is_integer(strpos($xml, file))){
				$next = array(	"Id"=>$temp[$i]["Stra_Id"], 
								"Label"=>$temp[$i]["Sekc_Naziv"] . " &gt; " . $temp[$i]["Stra_Naziv"], 
								"TypeLabel"=>ocpLabels("Block"), 
								"Type"=>"Blok");

				$results[] = $next;

				$lastStraId = $temp[$i]["Stra_Id"];
			}
		}

		$temp = con_getResults("select TipoviObjekata.Ime, TipoviObjekata.Grupa, TipoviObjekata.Labela, Polja.ImePolja from Polja inner join TipoviObjekata on TipoviObjekata.Id=Polja.TipId where Polja.TipTabela in ('ShortStrings', 'LongStrings', 'Texts') order by TipoviObjekata.Ime, Polja.ImePolja");
		for ($i=0; $i<count($temp); $i++){
			$field = $temp[$i]["ImePolja"];
			$type = $temp[$i]["Ime"];
			$label = ocpLabels($temp[$i]["Grupa"]) . " &gt; " . ocpLabels($temp[$i]["Labela"]);

			$sortName = xml_getSortName($type);

//utils_dump($type." ".$field);

			$objects = con_getResults("select Id, ".$sortName.", ".$field." from ".$type." where ".$field." like '%".$file."%'");
//utils_dump("select Id, ".$sortName.", ".$field." from ".$type." where ".$field." like '%".$file."%'");
			for ($j=0; $j<count($objects); $j++){
				$next = array(	"Id"=>$objects[$j]["Id"], 
								"Label"=>$objects[$j][$sortName], 
								"Type"=>$type,
								"TypeLabel"=>$label,
								"Field"=>$field);

				$results[] = $next;
			}
		}

		return $results;
	}
?>