<?php
	/*Vraca sve izvestaje iz baze
	==================================*/
	function izv_getAll($sortName, $direction){
		$strSQL = "select * from Izvestaj";
		if (utils_valid($sortName)) $strSQL .= " order by ".$sortName." ".$direction;
		return con_getResults($strSQL);
	}

	/*Vraca sve izvestaje iz baze
	==================================*/
	function izv_getAll4User(){
		global $RR;

		$where = " and Id not in(";
		$a = array_keys($RR);
		foreach ($a as $i){
			if (!is_numeric($i)) continue;
			if ($RR[$i] != "1")
				$where .= $i . ",";
		}
		if ($where != " and Id not in(") $where = substr($where, 0, strlen($where)-1) . ")";
		else $where = "";
//utils_dump("SELECT Id, Ime, Grupa FROM Izvestaj WHERE Aktivan=1 ".$where." order by Grupa, Ime");
		return con_getResults("SELECT Id, Ime, Grupa FROM Izvestaj WHERE Aktivan=1 ".$where." order by Grupa, Ime");
	}

	/*Vraca sve izvestaje iz baze (samo Id)
	==================================*/
	function izv_getIds(){
		return con_getResultsArr("select Id from Izvestaj where Aktivan=1");
	}

	/*Vraca odredjeni izvestaj iz baze
	==================================*/
	function izv_get($reportId){
		return con_getResult("select * from Izvestaj where Id=".$reportId);
	}

	/*Vraca parametre za odredjeni izvestaj
	=======================================*/
	function izv_getParameters($parameterStrXml){
		$params = array();

		$paramXml = xml_loadXML($parameterStrXml);
		
		$paramList = xml_childNodes(xml_documentElement($paramXml));
		for ($i=0; $i<count($paramList); $i++){
			$attributes = xml_attributes(xml_item($paramList, $i));
			$result = array();

			foreach($attributes as $nextAtt){
//utils_dump(attributes.item(j).name." ".attributes.item(j).value);
				$result[xml_attrName($nextAtt)] = xml_attrValue($nextAtt);
			}
			$params[] = $result;
		}
		
		return $params;
	}

	/*Vraca parametre za odredjeni izvestaj
	=======================================*/
	function izv_getParametersDict($parameterStrXml){
		$result = array();

		$paramXml = xml_loadXML($parameterStrXml);
		
		$paramList = xml_childNodes(xml_documentElement($paramXml));
		for ($i=0; $i<count($paramList); $i++)
			$result[xml_getAttribute(xml_item($paramList, $i), "name") ] = xml_getAttribute(xml_item($paramList, $i), "inputType");

		return $result;
	}
	
	/*Vraca default parametar - ako ga nema u xml
	=======================================*/
	function izv_getDefaultParam($paramName){
		return array("name"=>$paramName, "inputType" => "textBox", "label" => $paramName);
	}
	
	/*Kreira novi izvestaj u bazi
	=======================================*/
	function izv_insert($report){
		$strSQL = "INSERT INTO Izvestaj (Ime, Grupa, Upit, ParametarXml, DetaljniIzvestaj, Aktivan) VALUES ('".$report["Ime"]."', '".$report["Grupa"]."', '".$report["Upit"]."'";
		$strSQL .= (utils_valid($report["ParametarXml"]) ? ", '".$report["ParametarXml"] . "'" : ", '<parameters></parameters>'");
		$strSQL .= (utils_valid($report["DetaljniIzvestaj"]) ? ", ".$report["DetaljniIzvestaj"] : ", NULL");
		$strSQL .= (($report["Aktivan"] == "1") ? ", 1)" : ", 0)");

//utils_dump(strSQL);
		con_update($strSQL);

		$idIzvestaja = con_getValue("select max(Id) from Izvestaj");

		$RR[$idIzvestaja] = "1";
		return $idIzvestaja;
	}

	/*Update izvestaja u bazi
	=======================================*/
	function izv_update($report){
		$strSQL = "UPDATE Izvestaj SET Ime='" . $report["Ime"] . "', Grupa='" . $report["Grupa"] . "', Upit='" . $report["Upit"] . "', ParametarXml = ";
		$strSQL .= (utils_valid($report["ParametarXml"]) ? "'".$report["ParametarXml"] . "'" : "'<parameters></parameters>'");
		$strSQL .= ", DetaljniIzvestaj = ";
		$strSQL .= (utils_valid($report["DetaljniIzvestaj"]) ? $report["DetaljniIzvestaj"] : "NULL");
		$strSQL .= (($report["Aktivan"] == "1") ? ", Aktivan = 1" : ", Aktivan = 0");
		$strSQL .= " WHERE Id= ".$report["Id"];
		con_update($strSQL);
	}

	/*Brisanje izvestaja iz bazi
	=======================================*/
	function izv_delete($reportId){
		con_update("delete from SecurityIzvestaj where IzSec_Izve_Id=".$reportId);
		con_update("delete from Izvestaj where Id=".$reportId);
	}

	/*Vraca Izvestaj - Prava dictionary za user grupu
	===============================================*/
	function izv_getGroupReportRights($idGrupe){
		$strSQL = "select *, 0 as Rights from Izvestaj";
		if (utils_valid($idGrupe)){
			$strSQL = "select i.Ime, i.Grupa, i.Id, s.IzSec_Visible as Rights";
			$strSQL .= " from Izvestaj as i left join SecurityIzvestaj s on s.IzSec_Izve_Id=i.Id where s.IzSec_UGrp_Id=".$idGrupe;
		}
		return con_getResults($strSQL);
	}

	/*Vraca relaciju Izvestaj - UserGrupa 
	===============================================*/
	function izv_getReportRights($sortName, $direction){
		$izvestaji = izv_getAll($sortName, $direction);
		
		for ($i=0; $i<count($izvestaji); $i++){
			$strSQL = "select UGrp_Name from UserGroups, SecurityIzvestaj";
			$strSQL .= " where IzSec_UGrp_Id=UGrp_Id and IzSec_Izve_Id=".$izvestaji[$i]["Id"]." and IzSec_Visible = 1";

			$izvestaji[$i]["UserGroups"] = implode(",", con_getResultsArr($strSQL));
		}

		return $izvestaji;
	}
	
	/*Vraca true/false ako je izvestaj vidljiv za tu grupu
	===================================================*/
	function izv_isVisible($reportId, $groupId){
		return (intval(con_getValue("select count(*) from SecurityIzvestaj where IzSec_Visible=1 and IzSec_UGrp_Id=".$groupId." and IzSec_Izve_Id=".$reportId)) > 0);
	}
	
	/*Snima izmene prava nad izvestajem
	===============================================*/
	function izv_saveReportRights($reportId, $reportRights){
		$oldReportRigths = con_getResultsDict("select IzSec_UGrp_Id, IzSec_Visible from SecurityIzvestaj where IzSec_Izve_Id=".$reportId);

		$groupIds = array_keys($reportRights);
		for ($i=0; $i<count($groupIds); $i++){
			if (!isset($reportRights[$groupIds[$i]]) || !utils_valid($reportRights[$groupIds[$i]]) || ($reportRights[$groupIds[$i]] == 0)) continue;

			if (!array_key_exists($groupIds[$i], $oldReportRigths)){ //staro pravo nije postojalo
				con_update("insert into SecurityIzvestaj(IzSec_Izve_Id, IzSec_UGrp_Id, IzSec_Visible) values (".$reportId.", ".$groupIds[$i].", 1)");
			} else if ($oldReportRigths[$groupIds[$i]] == "0"){//staro pravo postojalo, ali bilo je false
				con_update("update SecurityIzvestaj set IzSec_Visible=1 where IzSec_Izve_Id=".$reportId." and IzSec_UGrp_Id=".$groupIds[$i]);
			}
		}

		$groupIds = array_keys($reportRights);
		for ($i=0; $i<count($groupIds); $i++){
			if (!array_key_exists($groupIds[$i], $reportRights) || !utils_valid($reportRights[$groupIds[$i]]) || ($reportRights[$groupIds[$i]] == 0)){ //staro pravo treba da se postavi na 0
				con_update("update SecurityIzvestaj set IzSec_Visible=0 where IzSec_Izve_Id=".$reportId." and IzSec_UGrp_Id=".$groupIds[$i]);
			}
		}

	}

/***********************************************************
** deo koji je vezan za izvrsavanje konkretnog izvestaja
***********************************************************/
	
	/*
SELECT     Lokacija.Sifra, Lokacija.Naziv, COUNT(Korisnik.Id) AS BrojKorisnika
FROM         Korisnik INNER JOIN
                      Lokacija ON Korisnik.LokacijaUnosa = Lokacija.Id
WHERE {Korisnik.DatumUnosa > @datum}
GROUP BY Lokacija.Sifra, Lokacija.Naziv
*/
	/*Fja koja izvlaci sve parametre iz sql-a*/
	function izvexecute_getAllParameters($input){
		$temp = array();
		preg_match_all("/(@){1}[a-z_0-9]+/i", $input, $temp);
		return array_unique($temp[0]);
	}

	function izvexecute_putParam($input, $name, $type){
		global $paramQueryString;

		$reParam = "/" . $name . "/i";
		$name = substr($name, 1);
		switch ($type){
			case "textBox": //tekst
			case "select":
			case "radio": 
			case "hidden":
				$value = utils_requestStr(getGVar($name));
				if (utils_valid($value) && !is_numeric(strpos($paramQueryString, "&" . $name . "="))){
					$paramQueryString .= "&" . $name . "=" . $value;
					$input = preg_replace($reParam, "'".$value."'", $input);
				}
				break;
			case "textDate": //datum
			case "textDatetime": //datum
				$value = ($type == "textDate") ? date_getFormDate($name) : datetime_getFormDate($name);
				if (utils_valid($value) && !is_numeric(strpos($paramQueryString, "&" . $name . "_dd="))){
					$paramQueryString .= "&" . $name . "_dd=" . utils_requestStr(getGVar($name."_dd"));
					$paramQueryString .= "&" . $name . "_mm=" . utils_requestStr(getGVar($name."_mm"));
					$paramQueryString .= "&" . $name . "_yyyy=" . utils_requestStr(getGVar($name."_yyyy"));
					$paramQueryString .= "&" . $name . "_time=" . utils_requestStr(getGVar($name."_time"));

					$input = preg_replace($reParam, "'".$value."'", $input);
				}
				break;
			case "complex"://multiselect 
				$value = utils_requestStr(getGVar($name));
				if (utils_valid($value) && !is_numeric(strpos($paramQueryString, "&" . $name . "="))){
					$paramQueryString .= "&" . $name . "=" . $value;
					$input = preg_replace($reParam, $value, $input);
				}
				break;
			case "foreignKey"://fk id 
				$value = utils_requestInt(getGVar($name));
				if (utils_valid($value) && ($value!=0) && !is_numeric(strpos($paramQueryString, "&" . $name . "="))){
					$paramQueryString .= "&" . $name . "=" . $value;
					$input = preg_replace($reParam, $value, $input);
				}
				break;
			case "check": //boolean
				$value = utils_requestInt(getGVar($name));
				if (utils_valid($value) && ($value == "1") && !is_numeric(strpos($paramQueryString, "&" . $name . "="))){
					$paramQueryString .= "&" . $name . "=" . $value;
					$input = preg_replace($reParam, "'1'", $input);
				} else {
					$input = preg_replace($reParam, "'0'", $input);
				}
				break;
		}
		return $input;
	}
	
	/*Fja koja izvlaci sve ImeKolone(=|<|>|<=|>=)@parametar ili
	@parametar=ImeKolone pretvara u 1=1
	=========================================*/
	function izvexecute_prepareSql($input){
//utils_dump($input);
		$reParamLeft = "/([a-z_0-9.]*[a-z_0-9]+)( ){0,1}(=|<|>|<=|>=)( ){0,1}((@){1}[a-z_0-9]+)/i";
		$input = preg_replace($reParamLeft, "1=1", $input);

		$reParamRight = "/((@){1}[a-z_0-9]+)( ){0,1}(=|<|>|<=|>=)( ){0,1}([a-z_0-9.]*[a-z_0-9]+)/i";
		$input = preg_replace($reParamRight, "1=1", $input);

//utils_dump($input);

		return $input;
	}


	function izvexecute_rowParamQueryString($keys, $row, $paramQueryString){
		$rowParamQueryString = "";
		for ($i=0; $i<count($keys); $i++){
			if (!is_numeric(strpos(utils_toLower($paramQueryString), "&".utils_toLower($keys[$i])."="))){
				$rowParamQueryString .= "&".$keys[$i]."=".$row[$keys[$i]];
			}
		}
		return $rowParamQueryString;
	}

?>