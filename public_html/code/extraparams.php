<?php

/* Fja za type="stra, sekc, verz" i id vraca sve extra parametre
==============================================================*/
	function extra_getAll($type, $id){
		global $menu;
		$result = array();

		$nodeType = "";
		switch ($type) {
			case "stra": $nodeType = "stranica_".$id; break;
			case "sekc": $nodeType = "sekcija_".$id; break;
			case "verz": $nodeType = "verzija_".$id; break;
		}
		$node = xml_getFirstElementByTagName($menu->xmlDoc, $nodeType);

		if (!is_null($node)){//nod postoji u kesiranom meniju
			$attr = xml_attributes($node);
			
			$excludeAttributes = array("id", "naziv", "pocetna", "dubina");

			foreach($attr as $nextAtt){
				if(!in_array(xml_attrName($nextAtt), $excludeAttributes))
					$result[xml_attrName($nextAtt)] = xml_attrValue($nextAtt); 
			}
		} else { //nije nadjen u kes meniju, ide iz baze
			$result = lib_getExtraParams($type, $id);
		}

		return $result;
	}

/* Fja za type="stra, sekc, verz", id i naziv parametra vraca 
vrednost tog parametra. Ako je zadato $recursive=1 trazice se 
vrednost extra parametra na gore sve do nivoa verzije
==============================================================*/
	function extra_get($type, $id, $name, $recursive=0){
		global $menu;
		$retValue = "";

		$nodeType = "";
		switch ($type) {
			case "stra": $nodeType = "stranica_".$id; break;
			case "sekc": $nodeType = "sekcija_".$id; break;
			case "verz": $nodeType = "verzija_".$id; break;
		}

		$node = xml_getFirstElementByTagName($menu->xmlDoc, $nodeType);
		
		if (!is_null($node)){//nod postoji u kesiranom meniju
			$retValue = xml_getAttribute($node, $name); 
		} else { //nije nadjen u kes meniju, ide iz baze
			$result = lib_getExtraParams($type, $id);
			$retValue = (isset($result[$name])) ? $result[$name] : "";
		}

		if (!utils_valid($retValue) && $recursive){
			if ($type == "stra"){
				return extra_get("sekc", menu_getSekcId(), $name, $recursive);
			} else if ($type == "sekc"){
				$sekcija = con_getResult("select * from Sekcija where Sekc_Id=".$id);
				if (!utils_valid($sekcija["Sekc_ParentId"])){
					return extra_get("verz", $sekcija["Sekc_Verz_Id"], $name, $recursive);
				} else {
					return extra_get("sekc", $sekcija["Sekc_ParentId"], $name, $recursive);
				}
			}
		}

		return $retValue;
	}

?>
