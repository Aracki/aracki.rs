<?php

/*Vraca id nekog tipa objekta
==============================*/
	function tipobj_getName($idTipa){
		return con_getValue("select Ime from TipoviObjekata where Id=".$idTipa);
	}

/*Vraca ime nekog tipa objekta
==============================*/
	function tipobj_getId($imeTipa){
		return con_getValue("select Id from TipoviObjekata where Ime='".$imeTipa."'");
	}

/*Vraca ceo tip objekta
==============================*/
	function tipobj_get($idTipa){
		return con_getResult("select * from TipoviObjekata where Id=".$idTipa);
	}

/*Vraca labelu tipa 
===================*/
	function tipobj_getLabel($idTipa, $imeTipa=""){
		$strSQL = (utils_valid($idTipa)) ? 
			"SELECT Labela FROM TipoviObjekata WHERE Id = ".$idTipa :
			"SELECT Labela FROM TipoviObjekata WHERE Ime = '".$imeTipa."'";
		return con_getValue($strSQL);
	}

/*Vraca sve podatke o tipu objekta po idu
==============================================*/
	function tipobj_getAllAboutType($idTipa) {
		return con_getResult("SELECT * FROM TipoviObjekata WHERE Id=".$idTipa);
	}

/*Vraca sve podatke o tipu objekta po imenu
==============================================*/
	function tipobj_getAllAboutTypeByName($imeTipa) {
		return con_getResult("SELECT * FROM TipoviObjekata WHERE Ime='".$imeTipa."'");	
	}

/*Vraca sve tipove
==============================*/
	function tipobj_getAll($sortName = NULL, $direction = NULL){
		$nizTipova = (!utils_valid($sortName)) ? 
			con_getResults("SELECT * from TipoviObjekata order by Ime asc") : 
			con_getResults("SELECT * from TipoviObjekata order by ".$sortName." ".$direction);
		return $nizTipova;	
	}

/*Vraca sve tipove objekata koji imaju polja i cije forme mogu da se 
kreiraju
=====================================================================*/
	function tipobj_getAllTypesWithFields($sortName = NULL, $direction = NULL) {
		$nizTipova = (!utils_valid($sortName)) ? 
			con_getResults("select distinct t.* from TipoviObjekata as t, Polja as p where p.TipId = t.Id order by t.Ime asc") : 
			con_getResults("select distinct t.* from TipoviObjekata as t, Polja as p where p.TipId = t.Id order by t.".$sortName." ".$direction);
		return $nizTipova;
	}	

/*Vraca sve tipove objekata koji se vide u drvetu levo
=======================================================================*/
	function tipobj_getAllTypes4User() {
		global $TR;

		$where = " and Id not in(";
		foreach ($TR as $key => $value)
			if ( $value == "0") $where .= $key.",";
		if ($where != " and Id not in(") $where = substr($where, 0, strlen($where)-1) . ")";
		else $where = "";
		return con_getResults("SELECT Ime, Id, Labela, Grupa FROM TipoviObjekata WHERE SamoPodforma=0 ".$where." order by Grupa ASC, Labela ASC");
	}

/*Vraca sve tipove
==================*/
	function tipobj_getAllTypes(){
		return con_getResults("SELECT Id, Ime from TipoviObjekata order by Ime");	
	}
	
/*Vraca sve id-ove tipova objekata u nizu
========================================*/
	function tipobj_getIdsTipovi(){
		return con_getResultsArr("select Id  from TipoviObjekata");
	}

/*Vraca koliko tip ima validnih objekata
========================================*/
	function tipobj_getTypeCount($imeTipa){
		return con_getValue("select count(*) from " . $imeTipa." where Valid=1");
	}

/*Brise tip objekta
====================*/
	function tipobj_delete($idTipa){
		$imeTipa = tipobj_getName($idTipa);
		$polja = polja_getAllFks($idTipa);				//drop fk polja ovog tipa
		for ($i=0; $i<count($polja); $i++){
			con_update("alter table ".$polja[$i]["Ime"]." drop column ".$polja[$i]["ImePolja"]);
			con_update("delete from Polja where Id=".$polja[$i]["Id"]);
		}
		con_update("delete from Polja where TipId=".$idTipa);					//sva polja koja sam Tip ima
		con_update("delete from SecurityObjects where OSec_ObjT_Id=".$idTipa);	//brise sve iz SecurityObjects tabele
		con_update("delete from TipoviObjekata where Id=".$idTipa);				//obrisi sve sto postoji u tipovima objekata
		con_update("delete from Logs where TipObjekta='".$imeTipa."'");			//Brisanje iz loga zapisa o ovoj tabeli
		con_update("drop table if exists ".$imeTipa);										//radim drop tabele
	}

/*Kreira tip objekta
======================*/
	function tipobj_newTip($data){
		$imeTipa = $data["ImeTipa"];
		$strSQL = "select count(*) from TipoviObjekata where Ime='$imeTipa'";
		$exists = con_getValue($strSQL);
		if ($exists) {
			return -1;
		}
		$strSQL = "insert into TipoviObjekata (Ime, Veza, Labela, Grupa, SamoPodforma) VALUES ('".$imeTipa."', 0, '".$data["Labela"]."', '".$data["Grupa"]."'" . (($data["Podforma"] == "1") ? ", 1)" : ", 0)");

		con_update($strSQL);
		$idTipa = con_getValue("select max(Id) from TipoviObjekata");
		
		$createSQL = "CREATE TABLE ".$imeTipa." ( Id int(11) NOT NULL auto_increment ";
		for ($i=0; $i< intval($data["n"]); $i++){
			if (!utils_valid($data["ImePolja".$i])) continue;

			$polje = array();
			$polje["Id"] = $idTipa;
			$polje["ImeTipa"] = $imeTipa;
			$polje["ImePolja"] = $data["ImePolja".$i];
			$polje["Tip"] = $data["Tip".$i];
			$polje["PodtipId"] = $data["PodtipId".$i];
			$polje["Null"] = $data["Null".$i];
			$polje["Default"] = $data["Default".$i];
			$polje["RedPrikaza"] = $data["RedPrikaza".$i];
			$sqls = tipobj_createFieldSQL(true, $polje);
			con_update($sqls["strSQL"]);
			$createSQL .= $sqls["fieldSQL"];
		}
		$createSQL .= ", Valid tinyint(1) NOT NULL default 1, lastModify int(10) unsigned NOT NULL default 0 ";
		$createSQL .= ", PRIMARY KEY (Id)) TYPE=MyISAM";

		con_update($createSQL);
		setSVar('ocpTR', 4, $idTipa);
		return $idTipa;
	}

/*	Azurira tip objekta
=======================*/	
	function tipobj_editTip($data){
		con_update("UPDATE TipoviObjekata SET Labela = '".$data["Labela"]."', Grupa='".$data["Grupa"]."', SamoPodforma=". (($data["Podforma"] == "1") ? "1" : "0") . " WHERE Id=".$data["Id"]);
		for ($i = 0; $i < intval($data["n"]); $i++)
			con_update("UPDATE Polja SET RedPrikaza=".$data["RedPrikaza".$i]." WHERE Id=".$data["PoljeId".$i]);
	}

/*	Dodaje polje tipu objekta
=============================*/
	function tipobj_addField($data){
		if (!utils_valid($data["Null"]) && !utils_valid($data["Default"])) return false;
		$sqls = tipobj_createFieldSQL(false, $data);
		con_update($sqls["fieldSQL"]);
		con_update($sqls["strSQL"]);
		return true;
	}

/*	Brise polje tipu objekta
=============================*/
	function tipobj_deleteField($idTipa, $idPolja){
		$imeTipa = tipobj_getName($idTipa);
		$polje = con_getResult("select ImePolja, TipTabela from Polja where Id=".$idPolja);
		con_update("DELETE FROM Polja WHERE Id=".$idPolja);
		con_update("ALTER TABLE ".$imeTipa." DROP COLUMN ".$polje["ImePolja"]);
	}


/*	Kreira sql iskaze neophodne za
kreiranje ocp i sql server polja
===================================*/	
	function tipobj_createFieldSQL($create, $data){
		$idTipa = $data["Id"];
		$imeTipa = $data["ImeTipa"];
		$imePolja = $data["ImePolja"];
		$tipTabela = $data["Tip"];
		$podtipId = $data["PodtipId"];
		$Null = $data["Null"]; if ($tipTabela == "Uploads") $Null = "";
		$Default = $data["Default"];
		$redPrikaza = $data["RedPrikaza"];
		
		$fieldSQL = ($create) ? "" : "ALTER TABLE ".$imeTipa." ADD ".$imePolja." ";
		$strSQL = "INSERT INTO Polja (TipId, ImePolja, TipTabela, PodtipId, RedPrikaza) VALUES (".$idTipa.", '".$imePolja."', '".$tipTabela."',";
		if ($podtipId == "") {$podtipId = 0;}
		
		switch($tipTabela){
			case "ShortStrings":
			case "LongStrings":
			case "Radios":
			case "Selects":
			case "Texts":
			case "Dates":
				//$strSQL .=  "0, ".$redPrikaza.")";
				$strSQL .= " " . in_array($tipTabela, array("ShortStrings", "LongStrings")) ? $podtipId : "0";
                                $strSQL .= ", ".$redPrikaza.")";
				if ($create) $fieldSQL = ", ".$imePolja." ".tipobj_sqlFieldType($tipTabela);
				else $fieldSQL .= tipobj_sqlFieldType($tipTabela);
				if ($Null == "Null") $fieldSQL .= " NULL ";
				else $fieldSQL .= " NOT NULL ";
				if (utils_valid($Default)) $fieldSQL .= " default '" . $Default . "'";
				break;

			case "Ints":
			case "Floats":
			case "Uploads":
			case "Bits":
			case "Objects":
				//$strSQL .= (($tipTabela == "Objects") ? $podtipId : "0") . ", ".$redPrikaza.")";
				$strSQL .= " " . in_array($tipTabela, array("Objects")) ? $podtipId : "0";
                                $strSQL .= ", ".$redPrikaza.")";
				if ($create) $fieldSQL = ", ".$imePolja." ".tipobj_sqlFieldType($tipTabela);
				else $fieldSQL .= tipobj_sqlFieldType($tipTabela);
				if ($Null == "Null") $fieldSQL .= " NULL ";
				else $fieldSQL .= " NOT NULL ";
				if (utils_valid($Default)) $fieldSQL .= " default " . $Default;
				break;
			default : break;
		}
		
		$sqlExpressions = array();
		$sqlExpressions["strSQL"] = $strSQL;
		$sqlExpressions["fieldSQL"] = $fieldSQL;

//utils_dump($strSQL);
		
		return $sqlExpressions;
	}

	function tipobj_sqlFieldType($tipTabela){
		$sqlTip = "";
		switch ($tipTabela) {
			case "ShortStrings": $sqlTip = " varchar(255) character set utf8 "; break;
			case "LongStrings": $sqlTip = " text character set utf8 "; break;
			case "Dates": $sqlTip = " datetime "; break;
			case "Bits": $sqlTip = " tinyint(1) "; break;
			case "Floats": $sqlTip = " float(11, 2)"; break;
			case "Texts": $sqlTip = " text character set utf8 "; break;
			case "Radios":
			case "Selects": $sqlTip = " varchar(255) character set utf8 "; break;
			case "Ints":
			case "Objects":
			case "Uploads": $sqlTip = " int "; break;
		}

		return $sqlTip;
	}

?>
