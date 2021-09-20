<?php

/*da li postoji neki tip koji ima radio ili select, 
  ali nije odabrano ime te liste
===================================================*/
	function selrad_isEverythingReady(){
		$strSQL = "select count(*) from Polja";
		$strSQL .= " where (TipTabela='Radios' or TipTabela='Selects') and ImeListe='NijeLista'";
		return (intval(con_getValue($strSQL)) == 0);
	}

/*vraca sva polja koja su Radios ili Selects
============================================*/
	function selrad_getListFields($sortName = NULL, $direction = NULL){
		$strSQL = "select p.Id, t.Ime as Tip, p.ImePolja as Polje, p.TipTabela as TipListe, p.ImeListe as Lista";
		$strSQL .= " from TipoviObjekata as t,Polja as p";
		$strSQL .= " where (p.TipTabela='Radios' or p.TipTabela='Selects') and p.TipId=t.Id";
		if (utils_valid($sortName))
			$strSQL .= " order by ".$sortName." ".$direction;
		$objekti = con_getResults($strSQL);
		for ($i=0; $i < count($objekti); $i++){
			$next = $objekti[$i];
			$next["ImeListe"] = (!isset($next["ImeListe"]) || $next["ImeListe"] == "NijeLista")? "" : $next["ImeListe"];
			$objekti[$i] = $next;
		}
		return $objekti;
	}

/*vraca samo polja u kojima je odabrana data lista
==================================================*/
	function selrad_getGivenListFields($imeListe, $vrstaListe){
		$strSQL = "select p.Id, p.ImePolja, t.Ime as Tip";
		$strSQL .= " from TipoviObjekata as t,Polja as p";
		$strSQL .= " where p.TipTabela='".selrad_getVrsta($vrstaListe)."' and p.ImeListe='".$imeListe."' and p.TipId=t.Id";
		return con_getResults($strSQL);
	}

/*Ova funkcija za ime tipa, polja, tabele (Selects ili Radios) 
i vrednosti koja je tu sacuvana vraca Labelu u listi
================================================================*/
	function selrad_getListLabel($imeTipa, $imePolja, $vrstaListe, $vrednost){
		$idTipa = tipobj_getId($imeTipa);
		$listName = selrad_getListName($idTipa, $imePolja);
		$strSQL = "select Labela from ".selrad_getTabela($vrstaListe)." where Vrednost='".$vrednost."' and Ime='".$listName."'";
		$retString = con_getValue($strSQL);
		if (!utils_valid($retString)) $retString = $vrednost;
		return ocpLabels($retString);
	}

/*vraca imeListe (Selects/Radios) za tip 
idTipa i polje imePolja
==========================================*/
	function selrad_getListName($idTipa, $imePolja){
		$strSQL = "select ImeListe from Polja where TipId=".$idTipa." and ImePolja='".$imePolja."'";
		return con_getValue($strSQL);
	}

/*Vraca celu listu po imenu imeListe, vrste vrstaListe
=====================================================*/
	function selrad_getListValues($imeListe, $vrstaListe){
		$strSQL = "select Vrednost, Labela from ".selrad_getTabela($vrstaListe)." where Ime='".$imeListe."'";
		return con_getResults($strSQL);
	}

/*Radi update vrednosti ili labele kao i insert u listu
=====================================================*/
	function selradio_updateList($imeListe, $vrstaListe, $noviV, $noviL, $stariV = NULL, $stariL = NULL){
		$strSQL = NULL;
		$vrstaListe = selrad_getTabela($vrstaListe);

		if (utils_valid($stariL)){ //update
			$strSQL = "update ".$vrstaListe;
			$strSQL .= " set Labela='".$noviL."', Vrednost='".$noviV."'";
			$strSQL .= " where Ime='".$imeListe."' and Labela='".$stariL."' and Vrednost='".$stariV."'";
			con_update($strSQL);

			if ($noviV != $stariV){//update
				$fields = selrad_getGivenListFields($imeListe, $vrstaListe);
				for ($i=0; $i<count($fields); $i++){
					$nextField = $fields[$i];
					$strSQL = "update ".$nextField["Tip"]." set ".$nextField["ImePolja"]."='".$noviV."' where ".$nextField["ImePolja"]."='".$stariV."'";
					con_update($strSQL);	
				}
			}
		} else {//insert
			if (utils_valid($noviV)){
				$strSQL = "insert into " . $vrstaListe . "(Ime, Labela, Vrednost) values "; 
				$strSQL .= "( '".$imeListe."', '".$noviL."', '".$noviV."')";		
			} else { //sistem sam generise vrednosti (brojacane su)
				$strSQL = "insert into " . $vrstaListe . "(Ime, Labela, Vrednost) values "; 
				$strSQL .= "( '".$imeListe."', '".$noviL."', '".selrad_getMaxValue($vrstaListe, $imeListe)."')";
			}
			con_update($strSQL);	
		}
	}


/*Radi delete vrednosti i labele iz liste
=====================================================*/
	function selradio_deleteValue($imeListe, $vrstaListe, $stariV, $stariL){
		$strSQL = null;
		$vrstaListe = selrad_getTabela($vrstaListe);

	//moraju da se update-uju sve vrednosti
		$fields = selrad_getGivenListFields($imeListe, $vrstaListe);
		for ($i=0; $i<count($fields); $i++){
			$nextField = $fields[$i];
			//postavila sam da se updatuju na prazan string, jer ne znam da li smeju biti null
			$strSQL = "update ".$nextField["Tip"]." set ".$nextField["ImePolja"]."='' where ".$nextField["ImePolja"]."='".$stariV."'";
			con_update($strSQL);	
		}

		//brisanje iz same liste
		$strSQL = "delete from ".$vrstaListe." where Ime='".$imeListe."' and Labela='".$stariL."' and Vrednost='".$stariV."'";
		con_update($strSQL);
	}

/*Radi delete cele liste
=====================================================*/
	function selradio_deleteList($imeListe, $vrstaListe){
		$vrstaListe = selrad_getTabela($vrstaListe);

		$fields = selrad_getGivenListFields($imeListe, $vrstaListe);
		for ($i=0; $i<count($fields); $i++){
			$nextField = $fields[$i];
			//postavila sam da se updatuju na prazan string, jer ne znam da li smeju biti null
			con_update("update ".$nextField["Tip"]." set ".$nextField["ImePolja"]."=''");	
			$tipTabela = ($vrstaListe == "RadioLista") ? "Radios" : "Selects";
			con_update("update Polja set ImeListe='NijeLista' where TipTabela='".$tipTabela."' and ImeListe='".$imeListe."'");
		}
		//brisanje same liste
		con_update("delete from ".$vrstaListe." where Ime='".$imeListe."'");
	}

/*Vraca sva iskoristena imena listi da ne bi 
bilo ponavljanja kod kreiranja istih
===========================================*/
	function selrad_getAllUsedListNames($vrstaListe, $sortName = NULL, $direction = NULL){
		$strSQL = "select distinct Ime from ".selrad_getTabela($vrstaListe);
		if (utils_valid($sortName)) $strSQL .= " order by ".$sortName." ".$direction;
		return con_getResultsArr($strSQL);
	}

/*vraca sve vrednosti i labele iz tabele RadioList ili SelectList
za dato ime polja
====================================================================*/
	function selrad_getListValuesByFieldName($imeTipa, $tabela, $imePolja){
		$strSQL = "select l.Vrednost, l.Labela from ".$tabela." l, Polja p, TipoviObjekata t";
		$strSQL .= " where p.ImeListe=l.Ime and p.ImePolja='".$imePolja."' and t.Ime='".$imeTipa."' and t.Id=p.TipId";
		$strSQL .= " order by l.Labela";
		return con_getResults($strSQL);
	}

/*vraca najvecu mogucu vrednost iz Liste,
radi samo ako je vrednost integer
========================================*/
	function selrad_getMaxValue($imeTipa, $imeListe){
		$strSQL = "select max(cast(Vrednost as int)) from ".$imeTipa." where Ime='".$imeListe."'";
		$retValue = 0;
		$maxVal = con_getValue($strSQL);
		if (is_int($maxVal))
			$retValue = intval($maxVal) + 1;
		
		return $retValue;
	}

/*pomocna f-ja koja vraca naziv tabele
=======================================*/
	function selrad_getVrsta($vrstaListe){
		if ($vrstaListe == "radio" || $vrstaListe=="Radios" || $vrstaListe=="RadioLista") return "Radios";
		return "Selects";
	}

/*pomocna f-ja koja vraca naziv tabele
=======================================*/
	function selrad_getTabela($vrstaListe){
		if ($vrstaListe == "radio" || $vrstaListe=="Radios" || $vrstaListe=="RadioLista") return "RadioLista";
		return "SelectLista";
	}
?>