<?php
/*Vraca sve podatke o dinamickim tipovima blokova
=================================================*/
	function tipblok_getAllDinamic(){
		return con_getResults("select * from TipBloka where TipB_Valid=1 and TipB_Dinamic=1");
	}
	
/*Vraca sve podatke o statickim tipovima blokova
=================================================*/
	function tipblok_getAllStatic(){
		return con_getResults("select * from TipBloka where TipB_Valid=1 and TipB_Dinamic=0 order by TipB_Id desc");
	}

/*Vraca sve sharovane blokove
=====================================*/
	function tipBloka_getAllShared(){
		$strSQL = "select Blok_Id, Blok_TipB_Id, Blok_XmlPodaci, TipB_Naziv, TipB_Dinamic, TipB_XslUrl, TipB_SlikaUrl, Blok_MetaNaziv from Blok, TipBloka where Blok_Valid=1 and TipB_Id=Blok_TipB_Id and Blok_Share=1 order by Blok_MetaNaziv";
		return con_getResults($strSQL);
	}

/*Vraca sve o sharovanom bloku
=====================================*/
	function tipBloka_getShare($blockId){
		$strSQL = "select Blok_Id, Blok_TipB_Id, Blok_XmlPodaci, TipB_Naziv, TipB_Dinamic, TipB_XslUrl, TipB_SlikaUrl, Blok_MetaNaziv from Blok, TipBloka where Blok_Valid=1 and TipB_Id=Blok_TipB_Id and Blok_Share=1  and Blok_Id = ".$blockId;
		return con_getResult($strSQL);
	}

/*Vraca odredjeni tip bloka
===========================*/
	function tipblok_get($TipB_Id){
		return con_getResult("select * from TipBloka where TipB_Valid=1 and TipB_Id=".$TipB_Id);
	}

/*Vraca sve stranice koje sadrze blok Blok_Id
=================================================*/
	function tipBloka_getAllStranicaForBlok($Blok_Id){
		return con_getResults("select StBl_Id, StBl_Stra_Id, StBl_RedPrikaza, StBl_Valid from Stranica_Blok where StBl_Blok_Id=".$Blok_Id);
	}

/*Vraca odredjeni blok Blok_Id
=================================================*/
	function tipblok_getBlok($Blok_Id){
		return con_getResult("select Blok_Id, Blok_TipB_Id, Blok_XmlPodaci, Blok_Share, Blok_MetaNaziv from Blok where Blok_Id=".$Blok_Id);
	}
	
/*Kreira novi tip bloka
=======================*/
	function tipblok_new($obj) {
		$strSQL = "";
		if ($obj["TipB_Id"]>0) {
			// Block Type Id exists => update block type
			$strSQL .= " UPDATE TipBloka SET";
			$strSQL .= " TipB_Naziv = '".$obj["TipB_Naziv"]."', ";
			$strSQL .= " TipB_Xml = '".$obj["TipB_Xml"]."', ";
			$strSQL .= " TipB_XslUrl = '".$obj["TipB_XslUrl"]."', ";
			$strSQL .= " TipB_SlikaUrl = '".$obj["TipB_SlikaUrl"]."', ";
			$strSQL .= " TipB_Dinamic = '".$obj["TipB_Dinamic"]."' ";
			$strSQL .= " WHERE TipB_Id = ".$obj["TipB_Id"];
		} else {
			// BlockTypeId = 0 => new block type
			$strSQL .= " INSERT INTO TipBloka";
			$strSQL .= " (TipB_Naziv, TipB_Xml, TipB_XslUrl, TipB_SlikaUrl, TipB_Dinamic, TipB_Valid) VALUES ";
			$strSQL .= " ('".$obj["TipB_Naziv"]."', '".$obj["TipB_Xml"]."', ";
			$strSQL .= " '".$obj["TipB_XslUrl"]."', '".$obj["TipB_SlikaUrl"]."', ".$obj["TipB_Dinamic"].", 1)";
		}
		con_update($strSQL);
	}

/*Brise tip bloka
=======================*/
	function tipblok_delete($typeId) {
		$blokovi = con_getResultsArr("select Blok_Id from Blok where Blok_Valid=1 and Blok_TipB_Id=".$typeId);
		for ($i=0;$i<count($blokovi);$i++){
			con_update("update Stranica_Blok set StBl_Valid=0 where StBl_Blok_Id=".$blokovi[$i]);
			con_update("update Blok set Blok_Valid=0 where Blok_Id=".$blokovi[$i]);
		}
		con_update("update TipBloka set TipB_Valid=0 where TipB_Id=".$typeId);		
	}



/*Pretvara deljeni blok u nedeljeni
=================================================*/
	function tipblok_breakShare($BlokId){
		$veze = tipBloka_getAllStranicaForBlok($BlokId);
		$blok = tipblok_getBlok($BlokId);

		for ($j=0;$j<count($veze);$j++){
			$veza = $veze[$j];
			$valid = ($veza["StBl_Valid"] == "1") ? "1" : "0";
			if ($j != (count($veze)-1)){
				$strSQL = "insert into Blok(Blok_TipB_Id, Blok_XmlPodaci, Blok_Valid)";
				$strSQL .= " values (".$blok["Blok_TipB_Id"].", '".$blok["Blok_XmlPodaci"]."',".$valid.")";
				con_update($strSQL);

				$newId = con_getValue("select max(Blok_Id) from Blok");
				$strSQL = "update Stranica_Blok set StBl_Blok_Id=".$newId." where StBl_Id=".$veza["StBl_Id"];
				con_update($strSQL);
			} else {
				con_update("Update Blok set Blok_Valid=".$valid.",Blok_Share=0, Blok_MetaNaziv=null where Blok_Id=".$BlokId);				
			}
		}
	}

/*Brise deljeni blok sa svim instancama
=================================================*/
	function tipblok_deleteShare($BlokId){
		$veze = con_getResultsArr("select StBl_Id from Stranica_Blok where StBl_Valid=1 and StBl_Blok_Id=".$BlokId);
		for ($i=0;$i<count($veze);$i++)
			con_update("update Stranica_Blok set StBl_Valid=0 where StBl_Id=".$veze[$i]);
		con_update("update Blok set Blok_Valid=0 where Blok_Id=".$BlokId);
	}
?>
