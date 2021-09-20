<?php
/*Vraca niz validnih objekata koji pripadaju tipu imeTipa
whereAppend ako je postavljen se dodaje jos neka 
restrikcija sem Valid=1
================================================*/
	function obj_getAll($imeTipa, $sortIme = NULL, $sortSmer = NULL, $whereAppend = NULL){
		$select = "select o.Id";
		$from = " from ".$imeTipa." o";
		$where = " where 1=1 AND o.Valid=1";
		if (utils_valid($whereAppend)){
			$whereAppend = str_replace("''", "'", $whereAppend); //problem sa dva '
			$where .= $whereAppend;
		}
		$orderBy = " order by o."; 

		$arrPolja = polja_getFields(NULL, $imeTipa);
		$listFields = array();
		for ($i=0; $i < count($arrPolja); $i++){
			$polja = $arrPolja[$i];
			if (($polja["TipTabela"] == "Radios") || ($polja["TipTabela"] == "Selects")){
				$temp = substr($polja["TipTabela"], 0, strlen($polja["TipTabela"])-1)."Lista";
				$listFields["ImePolja"] = con_getResultsDict("select Vrednost, Labela from $temp where Ime='".$polja["ImeListe"]."'"); 
			}
			$select .=", o.".$polja["ImePolja"]; 
		}
		if (!utils_valid($sortIme)) $orderBy = "";
		else $orderBy .= $sortIme ." ". $sortSmer;
//echo($select.$from.$where.$orderBy);
		$objekti = con_getResults($select.$from.$where.$orderBy);
		foreach($listFields as $imePolja => $vrednosti){
			for ($i=0; $i<count($objekti); $i++){
				if (isset($objekti[$i][$imePolja])) $objekti[$i][$imePolja] = $vrednosti[$objekti[$i][$imePolja]];
			}
		}
		return $objekti;
	}

/*Vraca sve nevalidne objekte tipa imeTipa
==========================================*/
	function obj_getAllNonValid($imeTipa, $sortIme = NULL, $sortSmer = NULL, $brojNaStrani, $doSadaPrikazano){
		global $recordCount;
		
		$select = "select o.Id";
		$from = " from ".$imeTipa." o";
		$where = " where o.Valid = 0";

		$arrPolja = polja_getFields(null, $imeTipa);
		$listFields = array();
		for ($i=0; $i < count($arrPolja); $i++){
			$polja = $arrPolja[$i];
			if (($polja["TipTabela"] == "Radios") || ($polja["TipTabela"] == "Selects")){
				$temp = substr($polja["TipTabela"], 0, strlen($polja["TipTabela"])-1)."Lista";
				$listFields["ImePolja"] = con_getResultsDict("select Vrednost, Labela from $temp where Ime='".$polja["ImeListe"]."'"); 
			}
			$select .=", o.".$polja["ImePolja"]; 
		}
		$orderBy = (!utils_valid($sortIme)) ? "" : " order by ".$sortIme . " " . $sortSmer;
		
		if (!utils_valid($doSadaPrikazano)) $doSadaPrikazano = 0;
		$indPocetka = intval($brojNaStrani)*intval($doSadaPrikazano);

		$recordCount = con_getValue("select count(*) ".$from . $where);

//utils_dump($select.$from.$where.$orderBy . " limit ".$indPocetka.", ".$brojNaStrani);
		$objekti = con_getResults($select.$from.$where.$orderBy . " limit " . $indPocetka . ", " . $brojNaStrani);
		foreach($listFields as $imePolja => $vrednosti){
			for ($i=0; $i<count($objekti); $i++){
				if (isset($objekti[$i][$imePolja])) $objekti[$i][$imePolja] = $vrednosti[$objekti[$i][$imePolja]];
			}
		}
		return $objekti;
	}

/*Vraca konkretan objekat 
sve je true kada se i ne validni uzimaju
========================================*/
	function obj_get($imeTipa, $idObjekta, $notvalid = NULL){
		$select = "select o.Id, o.LastModify";
		$from = " from ".$imeTipa." o ";
		$where = " where o.Id=".$idObjekta;
		
		if (is_null($notvalid) || !$notvalid)
			$where .= " AND o.Valid=1";

		$arrPolja = polja_getFields(null, $imeTipa);
		for ($i=0; $i < count($arrPolja); $i++)
			$select .=", o.".$arrPolja[$i]["ImePolja"]; 

		return con_getResult($select . $from . $where);
	}

/*Vraca sve instance nekog tipa objekta zadatog id-om
idTipa			-->		id tipa
arrPolja		-->		fields i njihove klauzule u where
sortIme    -->     sortirano po tom fieldu
sortSmer		-->		smer sortiranja asc or desc
brojNaStrani		-->		broj na stranici
doSadaPrikazano		-->		offset
whereAppend		-->		eventualna restrikcija
-recordCount je globalna promenljiva koja se menja
==============================================*/
	function obj_getList($idTipa, $arrPolja, $sortIme, $sortSmer,
						$brojNaStrani, $doSadaPrikazano, $whereAppend = NULL){
		global $recordCount;

		$objekti = array();
		$generator = 0;
		$imeTipa = tipobj_getName($idTipa);
		$select = "select o.Id";
		$from = " from ".$imeTipa." o";
		$where = " where o.Valid=1 ";
		if (utils_valid($whereAppend)){
			$whereAppend = str_replace("''", "'", $whereAppend); //problem sa dva '
			$where .= $whereAppend;
		}

		$orderBy = "";
		$listFields = array();

		for ($i=0; $i<count($arrPolja); $i++){
			$record = $arrPolja[$i];
			switch ($record["TipTabela"]){
				case "Texts" :	
					$select .= ",o.".$record["ImePolja"];
					if (isset($record["Value"]) && utils_valid($record["Value"]))
						$where.= utils_advancedSearchSQL("o.".$record["ImePolja"], $record["Value"]);
					break;
				case "Selects":
				case "Radios":
					$tabela = substr($record["TipTabela"], 0, strlen($record["TipTabela"])-1) . "Lista";
					if (isset($record["Value"]) && utils_valid($record["Value"])){
						$select .= ",".$tabela.$generator.".Labela as ".$record["ImePolja"];
						$from .= ",".$tabela." as ".$tabela.$generator;
						$where .= " and  " . $tabela . $generator . ".Vrednost=o." . $record["ImePolja"] . " and ".$tabela . $generator.".Ime='".$record["ImeListe"]."' and o.".$record["ImePolja"]."='".$record["Value"]."'";	
						$generator++;
					}else {
						$listFields[$record["ImePolja"]] = con_getResultsDict("select Vrednost, Labela from $tabela where Ime='".$record["ImeListe"]."'"); 
						$select .=", o.".$record["ImePolja"]." as ".$record["ImePolja"];
					}
					break;

				case "Dates":
					$select .= ",o.".$record["ImePolja"];
					if (isset($record["Value"]) && utils_valid($record["Value"]))
						$where.= " and o.".$record["ImePolja"]." >= '".$record["Value"]."'";
					if (isset($record["Value1"]) && utils_valid($record["Value1"]))
						$where .= " and o.".$record["ImePolja"]." <= '".$record["Value1"]."'"; 
					break;

				case "Objects": //izdvojeno zbog sortNamea
					$select .= ", o.".$record["ImePolja"];
					if (isset($record["Value"]) && utils_valid($record["Value"]) && ($record["Value"] != 0))
						$where.= " and o.".$record["ImePolja"]." = ".$record["Value"];

					if ($sortIme == $record["ImePolja"]){
						$realSortName = xml_getSortName($record["PodtipIme"]);
						$from .= ", ".$record["PodtipIme"]." as sort";
						$where .= " and o.".$record["ImePolja"]."=sort.Id";
						$orderBy = " order by sort.".$realSortName." ".$sortSmer;
					}
					break;

				case "ShortStrings":
				case "LongStrings":
					$select .= ",o.".$record["ImePolja"];
					if (isset($record["Value"]) && utils_valid($record["Value"]))
						$where .= utils_advancedSearchSQL("o.".$record["ImePolja"], $record["Value"]);
					break;

				default: 
						$select .= ",o.".$record["ImePolja"];
						if (isset($record["Value"]) && utils_valid($record["Value"]))
							$where.= " and o.".$record["ImePolja"]." = ".$record["Value"];
						break;
			}
		}

		$iskaz = $select . $from . $where;

		if ($orderBy == ""){
			if (utils_valid($sortIme)) $iskaz .= " order by o." . $sortIme . " " . $sortSmer;
		} else {
			$iskaz .= $orderBy;
		}
		
		if (!utils_valid($doSadaPrikazano)) $doSadaPrikazano = 0;
		$indPocetka = intval($brojNaStrani)*intval($doSadaPrikazano);

		$recordCount = con_getValue("select count(*) ".$from . $where);

//utils_dump($iskaz . " limit ".$indPocetka.", ".$brojNaStrani);
//die();

		$objekti = con_getResults($iskaz . " limit ".$indPocetka.", ".$brojNaStrani);
		foreach($listFields as $imePolja => $vrednosti){
			for ($i=0; $i<count($objekti); $i++){
				if (isset($objekti[$i][$imePolja])) $objekti[$i][$imePolja] = $vrednosti[$objekti[$i][$imePolja]];
			}
		}
		return $objekti;
	}

/*Brise odredjeni objekat
==============================================*/
	function obj_delete($imeTipa, $idObjekta){
		$strSQL = null;

		$tip = tipobj_getAllAboutTypeByName($imeTipa);

		obj_beforeDelete($imeTipa, $idObjekta);

//brisanje fk
		$subTypes = polja_getAllFks($tip["Id"]);
		for ($i=0; $i<count($subTypes); $i++){
			if ($subTypes[$i]["SamoPodforma"] == "0")
				con_update("Update ".$subTypes[$i]["Ime"]." set Valid=0 where ".$subTypes[$i]["ImePolja"]."=".$idObjekta);
			else 
				con_update("delete from ".$subTypes[$i]["Ime"]." where ".$subTypes[$i]["ImePolja"]."=".$idObjekta);
		}

		con_update("update ".$imeTipa." set Valid=0 where Id=".$idObjekta);

		log_append("Delete", $imeTipa, $idObjekta);

		obj_afterDelete($imeTipa, $idObjekta);
	}

/*Azurira konkretan objekat
Novi je dictionary sa parovima imenaPolja -> nove vrednosti
Stari je dictionary sa parovima imenaPolja -> stare vrednosti
==============================================*/
function obj_update($imeTipa, $novi, $stari){
		$strSQL = null;
		$update = "update " . $imeTipa;
		$set = " set ";
		$where = " where Id=" . $novi["Id"] . " and Valid=1";

		$novi = obj_beforeUpdate($imeTipa, $novi);

		if ($novi["LastModify"] != "") $where .= " and LastModify = '".$novi["LastModify"]."'";

		$arrPolja = polja_getFields(null, $imeTipa);

		for ($i=0; $i < count($arrPolja); $i++){
			$polje = $arrPolja[$i];
		
			$stVred = "".$stari[$polje["ImePolja"]];
			$nvVred = "".$novi[$polje["ImePolja"]];

			if (utils_valid($nvVred)){
				
				if ($nvVred == $stVred) continue;

				if (($polje["TipTabela"] =="Ints") || ($polje["TipTabela"] == "Floats") || ($polje["TipTabela"] == "Objects") || ($polje["TipTabela"] == "Uploads")){ 
					$set .= $polje["ImePolja"]."=" . $nvVred . ",";
				} else {
					if ($polje["TipTabela"] == "Bits"){
						if ($nvVred == "true" || $nvVred == "1")
							$set .= $polje["ImePolja"] . "=1,";
						else 
							$set .= $polje["ImePolja"] . "=0,";
					}else{
						$nvVred = utils_killBadLinks($nvVred);
						$set .= $polje["ImePolja"]."='" . $nvVred."',";
					}
				}
			} else {
				if (utils_valid($stVred)){
					if ($polje["TipTabela"] == "Bits") $set .= $polje["ImePolja"] . "=0,";
					else $set .= $polje["ImePolja"] ."=null,";
				}
			}
		}

		if ($set != " set "){
			$set .= "LastModify='".date_getMiliseconds()."', Valid=1";

//utils_dump($update.$set.$where);

			$affected = con_update($update.$set.$where);
			if (intval($affected) == 0){
?><script>alert('<?php echo ocpLabels("Another user has changed object. Your changes are not saved.") ?>');</script>
<?php
			} else {
				log_append("Update", $imeTipa, $novi["Id"]);

				obj_afterUpdate($imeTipa, $novi);
			}
		}
	}

/*Insertuje objekat, $novi sadrzi parove imenaPolja -> vrednosti
==============================================*/
	function obj_insert($imeTipa, $novi){
		$insert = "insert into ".$imeTipa."(";
		$values = " values (";

		$novi = obj_beforeInsert($imeTipa, $novi);

		$arrPolja = polja_getFields(null, $imeTipa);
		for ($i=0; $i < count($arrPolja); $i++){
			$polje = $arrPolja[$i];
		
			$vred = $novi[$polje["ImePolja"]];

			if (utils_valid($vred)){
				$insert .= $polje["ImePolja"].",";
				if (($polje["TipTabela"] =="Ints") || ($polje["TipTabela"] == "Floats") || ($polje["TipTabela"] == "Objects") || ($polje["TipTabela"] == "Uploads")){
					$values .= $vred.",";
				} else {
					if ($polje["TipTabela"] == "Bits"){
						if ($vred == "true" || $vred == "1")
							$values .= "1,";
						else 
							$values .= "0,";
					}else{
						$vred = utils_killBadLinks($vred);
						$values .= "'". $vred."',";	
					}
				}
			} else {
				if ($polje["TipTabela"] == "Bits"){
					$insert .= $polje["ImePolja"].",";
					$values .= "0,";
				}
			}
		}

		$insert .= "LastModify)";
		$values .= "'".date_getMiliseconds()."')";
		
		con_update($insert.$values);
		$idObjekta = con_getValue("select max(Id) from ".$imeTipa);
		log_append("Insert", $imeTipa, $idObjekta);
		
		$novi["Id"] = $idObjekta;
		obj_afterInsert($imeTipa, $novi);

		return $idObjekta;
	}

/*Vraca ceo objekat (kao dictionary) koji je fk u objektima tipa 
imeTipa i to u polju imePolja i njegov id je vrednost
================================================================*/
	function obj_getForeignKeyObject($imeTipa, $imePolja, $idObjekta){
		$podTip = polja_getForeignTypeName($imeTipa, $imePolja);
		$objekat = obj_get($podTip, $idObjekta);
		$objekat["Tip"] = $podTip;

		return $objekat;
	}

/*Prethodna funkcija prosirena da vrati sve objekte
===================================================*/
	function obj_getForeignKeyObjects($imeTipa, $imePolja, $idObjekta, $whereAppend = NULL){
		$podtip = polja_getForeignTypeName($imeTipa, $imePolja);
		$results = obj_getAll($podtip, xml_getSortName($podtip), "asc", $whereAppend);

		for ($i=0; $i < count($results); $i++){
			$object = $results[i];
			if ($object["Id"] == $idObjekta) $object["Selected"] = "1";
			else $object["Selected"] = "0";
			$object["Tip"] = $podtip;
			$results[$i] = $object;
		}

		return $results;
	}

/*Prethodna funkcija suzena da vrati samo Id i sortName za vrati sve objekte
============================================================================*/
	function obj_getForeignKeyObjectsSimple($imeTipa, $imePolja, $idObjekta = NULL, $whereAppend = NULL){
		$podtip = (utils_valid($imePolja)) ? polja_getForeignTypeName($imeTipa, $imePolja) : $imeTipa;
		$sortName = xml_getSortName($podtip);
		$sortType = polja_getFieldType($podtip, $sortName);
		$strSQL = "";

		if ($sortType == "Objects"){
			$select = "select o.Id";
			$from = " from ".$podtip." as o";
			$where = " where 1=1 AND o.Valid=1";
			if (utils_valid($whereAppend)){
				$whereAppend = str_replace("''", "'", $whereAppend); //problem sa dva '
				$where .= " ".$whereAppend;
			}
			$order_by = " order by";
			$counter = 0;
			while ($sortType == "Objects"){
				$imePodPodTipa = polja_getForeignTypeName($podtip, $sortName);
				$prethodniAlias = ($counter == 0) ? "o" : "o".($counter-1);
				$from .= ", ".$imePodPodTipa." as o".$counter;
				$where .= " and  o".$counter.".Id=".$prethodniAlias.".".$sortName;
				$sortName = xml_getSortName($imePodPodTipa);
				$sortType = polja_getFieldType($imePodPodTipa, $sortName);
				$counter++;
			}
			$select .= ", o".($counter-1).".".$sortName;
			$order_by .= " o".($counter-1).".".$sortName." asc ";

			$strSQL = $select . $from . $where . $order_by;
		} else {
			$strSQL = "select o.Id, o.".$sortName;
			$strSQL .= " from ".$podtip." as o";
			$strSQL .= " where 1=1 AND o.Valid=1";
			if (utils_valid($whereAppend)){
				$whereAppend = str_replace("''", "'", $whereAppend); //problem sa dva '
				$strSQL .= " ". $whereAppend;
			}
			$strSQL .= " order by o.".$sortName." asc";	
		}

		$objekti = con_getResults($strSQL);
		for ($i=0; $i<count($objekti); $i++){
			$result = $objekti[$i];
			$result["Tip"] = $podtip;
			if (utils_valid($idObjekta)){
				if ($result["Id"] == $idObjekta) $result["Selected"] = "1";
				else $result["Selected"] = "0";
			} else $result["Selected"] = "0";
			$objekti[$i] = $result;
		}

		return $objekti;
	}


/*Da li je objekat datog ida i tipa validan?
============================================*/
	function obj_isValid($imeTipa, $idObjekta){
		return utils_valid(con_getValue("select Id from ".$imeTipa." where Id=".$idObjekta." and Valid=1"));
	}

/*Brisanje fk relacije sa varijantom da se brise i objekat u podformi
=====================================================================*/
	function obj_deleteSubType($imeTipa, $idTipa, $imePolja){
//utils_dump($imeTipa." ".$idTipa." ".$imePolja);
		$tip = tipobj_getAllAboutTypeByName($imeTipa);
		if ($tip["SamoPodforma"] == "1"){ //podformin objekat nije nezavisan
			obj_delete($imeTipa, $idTipa);
		} else {	//podformin objekat jeste nezavisan, zelimo samo fk da raskinemo
			con_update("update ".$imeTipa." set " . $imePolja . "=0 where Id=" . $idTipa);
		}
	}

/*Kopiranje objekta tipa imeTipa i id-a idObjekta
================================================*/
	function obj_copy($imeTipa, $idObjekta){
		$copiedObj = obj_get($imeTipa, $idObjekta);
		$copiedObj["Id"] = "";
		return $copiedObj;
	}

/*Vraca najveci redosled uvecan za jedan za tip imeTipa
================================================*/
	function obj_getMaxOrder($imeTipa){
		$max = 0;
		$temp = con_getValue("select Max(OcpOrderColumn) from $imeTipa");
		if (utils_valid($temp))
			$max = intval($temp) + 1;
		return "".$max;
	}
?>