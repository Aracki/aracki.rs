<?php
	require_once($_SERVER['DOCUMENT_ROOT']."/ocp/config/logs.php");

/*Priprema log sloga
==============================================*/
	function log_append($akcija, $type, $id){
		global $max_no_logs;

		if (!utils_valid(getSvar("Log")) ||  (getSvar("Log") == "0")) return;
		$uName = getSVar("ocpUsername");
		if (isset($uName)){
			if (log_count() < $max_no_logs){
				$uName = getSVar("ocpUsername");
				$uGroup = getSVar("ocpUserGroup");
				if (!utils_valid($uGroup)) $uGroup = "1";

				$identifikacija = log_identifikacija($akcija, $type, $id);
				$sql = "insert into Logs (UserGroupId, UserName, Akcija, TipObjekta, IdObjekta, IdenObjekta, Datum) ";
				$sql .= "values (".$uGroup.", '".utils_escapeSingleQuote($uName)."', '".$akcija."', '".$type."', ".$id.", '".$identifikacija."', NOW())";
				con_update($sql);
			}
		}
		
	}

/*Fja koja vraca broj logova u bazi
========================================*/
	function log_count(){
		return intval(con_getValue("select count(*) from Logs"));
	}

/*Fja koja loguje usera kada udje u ocp
========================================*/
	function log_loggedUser(){
		global $max_no_logs;
		$_SESSION["Log"] = con_getValue("select Log from Ocp where Id = 1");
		if ($_SESSION["Log"]  == 0) return;
		$uName = $_SESSION["ocpUsername"];

		if (isset($uName)){
			if (log_count() < $max_no_logs){
				$uName = $_SESSION["ocpUsername"];
				$uGroup = $_SESSION["ocpUserGroup"];
				if (!utils_valid($uGroup)) $uGroup = "1";
				
				$sql = "insert into Logs (UserGroupId, UserName, Akcija, TipObjekta, IdObjekta, IdenObjekta, Datum) ";
				$sql .= "values (".$uGroup.", '".utils_escapeSingleQuote($uName)."', 'Login', 'Login', 0, 'Login', NOW())";
				con_update($sql);
			}
		}
	}

/*Fja koja vraca log recorde
============================*/
	function log_getAll($groupId, $userName, $action, $type, $dateFrom, $dateTo, $sortName, $direction, $brojNaStrani, $doSadaPrikazano){
		global $recordCount;

		$select = "SELECT Ugrp_Name as UserGroup, Id, UserName, Akcija, TipObjekta, IdObjekta, IdenObjekta, Datum ";
		$from = " FROM Logs, UserGroups ";
		$where = " WHERE Ugrp_Id=UserGroupId ";
		$orderBy = (utils_valid($sortName)) ? " ORDER BY " . $sortName . " " . $direction : "";

		if (utils_valid($groupId) && ($groupId != 0)) $where .= " AND UserGroupId=".$groupId;
		if (utils_valid($userName)) $where .= " AND UserName='".$userName."'";
		if (utils_valid($action)) $where .= " AND Akcija='".$action."'";
		if (utils_valid($type)) $where .= " AND TipObjekta='".$type."'";
		if (utils_valid($dateFrom)) $where .= " AND Datum>= '".$dateFrom."'";
		if (utils_valid($dateTo)) $where .= " AND Datum <= '".$dateTo."'";
		
		if (!utils_valid($doSadaPrikazano)) $doSadaPrikazano = 0;
		$indPocetka = intval($brojNaStrani)*intval($doSadaPrikazano);

		$recordCount = con_getValue("select count(*) ".$from . $where);

//utils_dump($select . $from . $where . $orderBy . " limit ".$indPocetka.", ".$brojNaStrani);
//die();

		$logovi = con_getResults($select . $from . $where . $orderBy . " limit ".$indPocetka.", ".$brojNaStrani);
		for ($i=0; $i<count($logovi); $i++){
			$log = $logovi[$i];
			
			$identifikacija = $log["IdenObjekta"];
			if (!utils_valid($identifikacija)){ 
				if ($log["TipObjekta"] == "Login") $identifikacija = "Login";
				else $identifikacija = log_identifikacija( $log["Akcija"], $log["TipObjekta"], $log["IdObjekta"]);
				con_update("update Logs set IdenObjekta='".$identifikacija."' where Id=".$log["Id"]);
			}
			$log["Objekat"] = $identifikacija;
			switch ($log["TipObjekta"]){
				case "Root": $log["TipObjekta"] = ocpLabels("Root"); break;
				case "Verzija": $log["TipObjekta"] = ocpLabels("Version"); break;
				case "Sekcija": $log["TipObjekta"] = ocpLabels("Section"); break;
				case "Stranica": $log["TipObjekta"] = ocpLabels("Page"); break;
				case "Blok": $log["TipObjekta"] = ocpLabels("Block"); break;
				default : $log["TipObjekta"] = ocpLabels($log["TipObjekta"]); break;
			}

			$logovi[$i] = $log;
		}
		
		return $logovi;
	}

/*Brise log slogove u bazi po odredjenim parametrima
====================================================*/
	function log_delete($type, $dateFrom, $dateTo){
		$select = "SELECT count(*) ";
		$from = " FROM Logs ";
		$where = " WHERE 1=1";

		if (utils_valid($type)) $where .= " AND TipObjekta='".$type."'";
		if (utils_valid($dateFrom)) $where .= " AND Datum>= '".$dateFrom."'";
		if (utils_valid($dateTo)) $where .= " AND Datum <= '".$dateTo."'";

		$affected = intval(con_getValue($select . $from . $where));
		con_update("delete " . $from . $where);

		return $affected;
	}

/*Vraca identifikaciju za sve log objekte.
Ocekuje se da su sve navedene funkcije prisutne
==============================================*/
	function log_identifikacija($akcija, $type, $id){
		$objekatIden = "";	

		$strSQL = "";
		switch ($type){
			case "Root": $strSQL = "select Root_Naziv as IdenObjekat from Root where Root_Id=".$id; break;
			case "Verzija": $strSQL = "select Verz_Naziv as IdenObjekat from Verzija where Verz_Id=".$id; break;
			case "Sekcija": $strSQL = "select Sekc_Naziv as IdenObjekat from Sekcija where Sekc_Id=".$id; break;
			case "Stranica": $strSQL = "select Stra_Naziv as IdenObjekat from Stranica where Stra_Id=".$id; break;
			case "Blok": 
				$strSQL = "select concat(Stra_Naziv, ' -> ', TipB_Naziv) as IdenObjekat";
				$strSQL .= " from Stranica, Blok, Stranica_Blok, TipBloka";
				$strSQL .= " where Blok_Id=".$id." and Blok_TipB_Id=TipB_Id";
				$strSQL .= " and Blok_Id=StBl_Blok_Id and StBl_Stra_Id=Stra_Id"; break;
			default: 
				$objekat = obj_get($type, $id, true);
				if (isset($objekat["Id"]) && utils_valid($objekat["Id"])){
					$objekatIden = xml_generateRecordIdenString($type, $objekat, 0);
				}else $objekatIden = ocpLabels("Object doesn't exist anymore");
			break;
		}

		if ($strSQL != ""){
			$objekatIden = con_getValue($strSQL);
			if (!utils_valid($objekatIden)) $objekatIden = ocpLabels("Object doesn't exist anymore");
		}

		return utils_escapeSingleQuote($objekatIden);
	}

/*Poslednje editovane stane
============================*/
	function log_getLastEditedPages(){
		$retArr = array();
		$strSQL = "SELECT Stra_Naziv, Stra_Id, Stra_Sekc_Id, UserName, Akcija, Datum ";
		$strSQL .= " FROM Logs, Stranica, Stranica_Blok";
		$strSQL .= " WHERE TipObjekta='Blok' and Akcija <> 'Delete' and Stra_Id=StBl_Stra_Id and StBl_Blok_Id=IdObjekta and Stra_Valid=1";
		$strSQL .= " ORDER BY Datum desc limit 0, 10";
		$results = con_getResults($strSQL);

		$strSQL = " SELECT Stra_Naziv, Stra_Id, Stra_Sekc_Id, UserName, Akcija, Datum ";
		$strSQL .= " FROM Logs, Stranica";
		$strSQL .= " WHERE TipObjekta='Stranica' and Akcija <> 'Delete' and Stra_Id=IdObjekta and Stra_Valid=1";
		$strSQL .= " ORDER BY Datum desc limit 0, 10";
		
		$results = utils_matrixSort(array_merge($results, con_getResults($strSQL)), "Datum", "desc");

		$editedPages = "";
		$delimiter = "#@#$";
		for ($i=0; $i<count($results); $i++){
			$result = $results[$i];
			if (!is_integer(strpos($editedPages, $delimiter . $result["Stra_Naziv"] . $delimiter))){
				$retArr[] = $result;
				if (count($retArr) == 5) break;
				else $editedPages .= $delimiter . $result["Stra_Naziv"] . $delimiter;
			}
		}

		return $retArr;
	}

/*Poslednje editovani objekti
=============================*/
	function log_getLastEditedObjects(){
		$retArr = array();
		$strSQL = "SELECT Labela, Grupa, TipoviObjekata.Id, TipId1, TipId2, TipObjekta, IdObjekta, IdenObjekta, UserName, Datum ";
		$strSQL .= " FROM Logs, TipoviObjekata";
		$strSQL .= " WHERE (TipObjekta <> 'Blok' and TipObjekta <> 'Stranica' and TipObjekta <> 'Sekcija' and TipObjekta <> 'Verzija' and TipObjekta <> 'Root') and Akcija <> 'Delete' and Ime=TipObjekta and Veza=0 and SamoPodforma=0";
		$strSQL .= " ORDER BY Datum desc";
		$editedObjects = array();
		$delimiter = "#@#$";

		$results = con_getResults($strSQL);
		for ($i=0; $i<count($results); $i++){
			$result = $results[$i];
			$found = 0;

			if (obj_isValid($result["TipObjekta"], $result["IdObjekta"])){
				for ($k=0; $k<count($editedObjects); $k++){
					if ($editedObjects[$k]["Tip"] == $result["Id"] && 
						$editedObjects[$k]["IdObjekta"] == $result["IdObjekta"]){
						$found = 1;
						break;
					}
				}
			}

			if (!$found){
				$result["Labela"] =	ocpLabels($result["Labela"]);
				$result["Grupa"] =	ocpLabels($result["Grupa"]) . "/";

				$retArr[] = $result;
				if (count($retArr) == 5) break;
				else {
					$nextEdited = array();
					$nextEdited["Tip"] = $result["Id"];
					$nextEdited["IdObjekta"] = $result["IdObjekta"];
					$editedObjects[] = $nextEdited;
				}
			}
		}

		return $retArr;
	}

/*Poslednji ulogovani useri
============================*/
	function log_getLastLoggedUsers(){
		$strSQL = "SELECT Ugrp_Name, UserName, Akcija, Datum ";
		$strSQL .= " FROM Logs, UserGroups  WHERE Akcija='Login' and Ugrp_Id=UserGroupId";
		$strSQL .=" ORDER BY Datum desc limit 0, 5";
		return con_getResults($strSQL);
	}
?>