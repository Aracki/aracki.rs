<?php
/*Vraca sve stranice koje se nalaze u dodatnom meniju odredjene verzije
=======================================================================*/
	function meni_getMenuPages($Verz_Id){
		$strSQL = "select Meni_Stra_Id, Meni_Naziv from DodatniMeni where Meni_Valid=1 and Meni_Verz_Id=".$Verz_Id;
		$strSQL .= " and Meni_Stra_Id is not null and Meni_Verz_To_Id is null order by Meni_RedPrikaza";
		return con_getResults($strSQL);
	}
	
/*Vraca sve verzije koje se nalaze u dodatnom meniju odredjene verzije
======================================================================*/
	function meni_getMenuVersions($Verz_Id){
		$strSQL = "select Meni_Verz_To_Id, Meni_Naziv from DodatniMeni where Meni_Valid=1 and Meni_Verz_Id=".$Verz_Id;
		$strSQL .= " and Meni_Verz_To_Id is not null and Meni_Stra_Id is null order by Meni_RedPrikaza";
		return con_getResults($strSQL);
	}

/*Vraca sve stranice koje se nalaze u dodatnom meniju odredjene verzije
=======================================================================*/
	function meni_getMenuPage($Verz_Id, $Stra_Id){
		return con_getResult("select * from DodatniMeni where Meni_Verz_Id=".$Verz_Id." and Meni_Stra_Id=".$Stra_Id);
	}
	
/*Vraca sve verzije koje se nalaze u dodatnom meniju odredjene verzije
======================================================================*/
	function meni_getMenuVersion($Verz_Id, $Verz_To_Id){
		return con_getResult("select * from DodatniMeni where Meni_Verz_Id=".$Verz_Id." and Meni_Verz_To_Id=".$Verz_To_Id);
	}
	
/*Dodaje stranicu meniju odredjene verzije
==========================================*/
	function meni_addPage($Stra_Id, $Verz_Id) {
		$max = 0;
		$temp = con_getValue("select max(Meni_RedPrikaza) from DodatniMeni where Meni_Verz_To_Id is null and Meni_Valid = 1 and Meni_Verz_Id = ".$Verz_Id);
		if (utils_valid($temp)){
			$max = intval($temp);
			++$max;
		}
		$strSQL = "insert into DodatniMeni (Meni_Verz_Id, Meni_Stra_Id, Meni_Naziv, Meni_RedPrikaza, Meni_Valid) ";
		$strSQL .= "values (".$Verz_Id.", ".$Stra_Id.", '".utils_escapeSingleQuote(stranica_getProperty($Stra_Id, "Stra_Naziv"))."', ".$max.", 1)";
		con_update($strSQL);
	}
	
/*Dodaje verziju meniju odredjene verzije
=========================================*/
	function meni_addVersion($Verz_To_Id, $Verz_Id) {
		$max = 0;
		$temp = con_getValue("select max(Meni_RedPrikaza) from DodatniMeni where Meni_Stra_Id is null and Meni_Valid = 1 and Meni_Verz_Id = ".$Verz_Id);
		if (utils_valid($temp)){
			$max = intval($temp);
			++$max;
		}
		$strSQL = "insert into DodatniMeni (Meni_Verz_Id, Meni_Verz_To_Id, Meni_Naziv, Meni_RedPrikaza, Meni_Valid) ";
		$strSQL .= "values (".$Verz_Id.",".$Verz_To_Id.",'".utils_escapeSingleQuote(verzija_getProperty($Verz_To_Id, "Verz_Naziv"))."', ".$max.", 1)";
		con_update($strSQL);
	}

/*Brise link na stranicu iz menija odredjene verzije
====================================================*/	
	function meni_deletePage($Stra_Id, $Verz_Id) {
		con_update("delete from DodatniMeni where Meni_Stra_Id = ".$Stra_Id." and Meni_Verz_Id=".$Verz_Id);
	}
	
/*Brise link na verziju iz menija odredjene verzije
====================================================*/	
	function meni_deleteVersion($Verz_To_Id, $Verz_Id) {
		con_update("delete from DodatniMeni where Meni_Verz_To_Id=".$Verz_To_Id." and Meni_Verz_Id=".$Verz_Id);
	}

/*Update meni linka 
===================*/
	function meni_updatePage($Meni_Verz_Id, $Meni_Stra_Id, $Meni_Naziv){
		con_update("update DodatniMeni set Meni_Naziv='".$Meni_Naziv."' where Meni_Verz_Id=".$Meni_Verz_Id." and Meni_Stra_Id=".$Meni_Stra_Id);
	}

/*Update meni linka 
===================*/
	function meni_updateVersion($Meni_Verz_Id, $Meni_Verz_To_Id, $Meni_Naziv){
		con_update("update DodatniMeni set Meni_Naziv='".$Meni_Naziv."' where Meni_Verz_Id=".$Meni_Verz_Id." and Meni_Verz_To_Id=".$Meni_Verz_To_Id);
	}

?>