<?php
/*Vraca imePolja za polje (idPolja) datog tipa objekta 
==================================================*/
	function polja_getFieldName($idPolja){
		return con_getValue("select ImePolja from Polja where Id =".$idPolja);
	}

/*Vraca celo polje za dati idPolja
==================================================*/
	function polja_getField($idPolja){
		return con_getResult("select * from Polja where Id =".$idPolja);
	}

/*Vraca ime foreign key tipa
============================*/
	function polja_getForeignTypeName($imeTipa, $imePolja) {
		$strSQL = "SELECT t1.Ime FROM TipoviObjekata t1,Polja p,TipoviObjekata t2";
		$strSQL .= " WHERE p.TipId=t2.Id and t2.Ime ='".$imeTipa."'";
		$strSQL .= " and p.ImePolja='".$imePolja."' and p.PodtipId=t1.Id";

		return con_getValue($strSQL);	
	}

/*Funkcija inverzna prethodnoj, za dato ime fk tipa i tipa vraca imePolja
========================================================================*/
	function polja_getForeignFieldName($imeTipa, $imeFkTipa) {
		$strSQL = "SELECT p.ImePolja FROM TipoviObjekata t1,Polja p,TipoviObjekata t2";
		$strSQL .= " WHERE p.TipId=t2.Id and t2.Ime ='".$imeTipa."'";
		$strSQL .= " and t1.Ime='".$imeFkTipa."' and p.PodtipId=t1.Id";
		return con_getValue($strSQL);	
	}

/*Vraca tipTabelu za polje (imePolja) datog tipa objekta 
==================================================*/
	function polja_getFieldType($imeTipa, $imePolja){
		$strSQL = "select  p.tipTabela from Polja p, TipoviObjekata t";
		$strSQL .= " where p.ImePolja ='".$imePolja."' and p.TipId=t.Id and t.Ime='".$imeTipa."'";
		return con_getValue($strSQL);
	}


/*Vraca niz koji sadrzi imena polja za tip objekta datog id
odnosno datog imena
==========================================================*/
	function polja_getFields($idTipa, $imeTipa = NULL, $idenFields = NULL) {
		$strSQL = "SELECT p.*";
		if (!utils_valid($imeTipa)) $strSQL .= " FROM Polja p WHERE p.TipId = ".$idTipa;
		else $strSQL .= " FROM Polja p, TipoviObjekata t1 WHERE p.TipId = t1.Id and t1.Ime = '".$imeTipa."'";
		$addSQL = "";
		if (!is_null($idenFields) && (count($idenFields) > 0)){
			$addSQL = " and ImePolja in (";
			for ($i=0; $i<count($idenFields); $i++) $addSQL .= "'" . $idenFields[$i]["name"]."', ";
			$addSQL = substr($addSQL, 0, strlen($addSQL)-2) . ")";
		}
		$strSQL .= $addSQL;
		$strSQL .= " ORDER BY p.RedPrikaza asc";
		$polja = con_getResults($strSQL);
		for ($i=0; $i<count($polja); $i++){
			$podtipIme = "";
			if (intval($polja[$i]["PodtipId"]) > 0)
				$podtipIme = con_getValue("select t.Ime from TipoviObjekata t where t.Id = ".$polja[$i]["PodtipId"]);
			$polja[$i]["PodtipIme"] = $podtipIme;
		}

		if (!is_null($idenFields)){
			for ($i=0; $i<count($polja); $i++){
				$nextIdenPolje = $polja[$i];
				for ($j=0; $j<count($idenFields); $j++){
					if ($polja[$i]["ImePolja"] == $idenFields[$j]["name"]){
						$polja[$i]["Labela"] = $idenFields[$j]["label"];
						break;
					}
				}
			}
		}

		return $polja;
	}


/*Da li postoji polje tipa Uploads
============================================*/	
	function polja_existsUpload(){
		return (intval(con_getValue("select count(*) from Polja where TipTabela='Uploads'")) > 0);
	}

/*Imena polja koja su Upload tipa
=================================*/
	function polja_getUploadFields($idTipa, $imeTipa){
		$strSQL = "select p.ImePolja";
		if (!utils_valid($imeTipa)) $strSQL .= " FROM Polja p WHERE p.TipTabela='Uploads' and p.TipId = ".$idTipa;
		else $strSQL .= " FROM Polja p, TipoviObjekata t1 WHERE p.TipTabela='Uploads' and  p.TipId=t1.Id and t1.Ime='".$imeTipa."'";
		return con_getResultsArr($strSQL);
	}

/*Vraca sva imena tipova koja imaju idTipa za fk
================================================*/
	function polja_getAllFks($idTipa){
		$strSQL = "SELECT t.Ime, t.Labela, t.SamoPodforma, t.Veza, p.ImePolja, p.Id FROM TipoviObjekata t,Polja p";
		$strSQL .= " WHERE p.TipId=t.Id and p.PodtipId=".$idTipa;
		return con_getResults($strSQL);
	}

/*Vraca niz imena koja su string tipa
==============================================================*/
	function polja_getStringFields($idTipa) {
		$strSQL = "SELECT ImePolja FROM Polja WHERE TipId = ".$idTipa;
		$strSQL .= " and (TipTabela = 'ShortStrings' or TipTabela = 'LongStrings' or TipTabela = 'Texts')";
		return con_getResultsArr($strSQL);
	}

/*Da li tip ima OcpOrderColumn
==============================================================*/
	function polja_hasOrderColumn($imeTipa){
		$strSQL = "select count(*) FROM Polja p, TipoviObjekata t1 WHERE p.ImePolja='OcpOrderColumn' and  p.TipId=t1.Id and t1.Ime='".$imeTipa."'";
		return (intval(con_getValue($strSQL)) > 0);
	}

?>