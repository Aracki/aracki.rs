<?php
/*Vraca user grupu za postojeci id
======================================*/		
	function users_getUserGroup($idGrupe){
		return con_getResult("select UGrp_Name as Name, UGrp_Id as Id, UGrp_Super as Super from UserGroups where UGrp_Id=".$idGrupe);
	}

/*Da li userGrupa vec postoji u bazi
======================================*/		
	function users_userGroupExists($name){
		return ((con_getValue("select count(*) from UserGroups where UGrp_Name='".$name."'")>0) ? 1 : 0);
	}

/*Vraca Tipove - Prava dictionary za user grupu
===============================================*/
	function users_getGroupTypeRights($idGrupe){
		$tipovi = con_getResults("select Id, Ime, Labela from TipoviObjekata");
		
		for ($i=0; $i<count($tipovi); $i++){
			if (utils_valid($idGrupe) && ($idGrupe != 0)){
				$strSQL = "select OSec_Rights from SecurityObjects";
				$strSQL .= " where OSec_ObjT_Id=".$tipovi[$i]["Id"]." and OSec_UGrp_Id=".$idGrupe;
				$right = con_getValue($strSQL);
				if (utils_valid($right)) $tipovi[$i]["Rights"] = $right;
				else $tipovi[$i]["Rights"] = 0;
			} else {
				$tipovi[$i]["Rights"] = 0;
			}
		}
		
		return $tipovi;
	}

/*Vraca sve postojece usere u nekoj grupi
=========================================*/
	function users_getAllUsersInGroup($idGrupe){
		$strSQL ="select User_Name as UserName, User_Password as Password from Users";
		$strSQL .= " where User_Valid=1 and User_UGrp_Id=".$idGrupe; 
		return con_getResults($strSQL);
	}

/*Vraca sve postojece usere
=========================================*/
	function users_getAllUsers($sortName, $direction){
		$strSQL = "select User_Id as Id, User_Name as UserName, User_Password as Password, UGrp_Name as Grupa from Users, UserGroups where User_Valid=1 and UGrp_Valid=1 and User_UGrp_Id=UGrp_Id";
		if (utils_valid($sortName)) $strSQL .= " order by ".$sortName." ".$direction;
		return con_getResults($strSQL);
	}

/*Vraca sve user grupe koje postoje u bazi
======================================*/		
	function users_getAllUserGroups($sortName, $direction){
		$strSQL = "select UGrp_Name as Name, UGrp_Id as Id, UGrp_Super as Super from UserGroups".((utils_valid($sortName)) ? " order by ".$sortName." ".$direction : "");
		return con_getResults($strSQL);
	}

/*Brisanje user grupe
=====================*/	
	function users_deleteUserGroup($idGrupe){
		con_update("delete from SecurityVerzija where VSec_UGrp_Id=".$idGrupe);
		con_update("delete from SecuritySekcija where SkSec_UGrp_Id=".$idGrupe);
		con_update("delete from SecurityStranica where StSec_UGrp_Id=".$idGrupe);
		con_update("delete from SecurityObjects where OSec_UGrp_Id=".$idGrupe);
		con_update("delete from SecurityIzvestaj where IzSec_UGrp_Id=".$idGrupe);
		con_update("delete from Users where User_UGrp_Id=".$idGrupe);
		con_update("delete from Logs where UserGroupId=".$idGrupe);
		con_update("delete from UserGroups where UGrp_Id=".$idGrupe);
	}

/*Kreiranje nove user grupe
===========================*/
	function users_createUserGroup($imeGrupe, $tipPrava){
		$idGrupe = -1;
		
		$strSQL = "insert into UserGroups(UGrp_Name, UGrp_Super)";
		$strSQL .= " values ('".$imeGrupe."', '0')";
		con_update($strSQL);

		$idGrupe = con_getValue("select max(UGrp_Id) as max from UserGroups");
		
		$arrTipId = array_keys($tipPrava);
		for ($i=0; $i<count($arrTipId); $i++){
			$idTipa = $arrTipId[$i];
			$pravo = $tipPrava[$idTipa];
			if (utils_valid($pravo))
				con_update("insert into SecurityObjects(OSec_UGrp_Id, OSec_ObjT_Id, OSec_Rights) values (".$idGrupe.", ".$idTipa.", ".$pravo.")");
			else
				con_update("insert into SecurityObjects(OSec_UGrp_Id, OSec_ObjT_Id, OSec_Rights) values (".$idGrupe.", ".$idTipa.", 0)");
		}
//daje not visible pravo nad sekcijama, verzijama i stranicama
		con_update("insert into SecurityVerzija(VSec_UGrp_Id, VSec_Rights) values (".$idGrupe.", 0)");
		con_update("insert into SecuritySekcija(SkSec_UGrp_Id, SkSec_Rights) values (".$idGrupe.", 0)");
		con_update("insert into SecurityStranica(StSec_UGrp_Id, StSec_Rights) values (".$idGrupe.", 0)");

		return $idGrupe;
	}

/*Azurira postojecu grupu
=========================*/
	function users_updateUserGroup($idGrupe, $staroImeGrupe, $novoImeGrupe, $tipPrava){
		if ($staroImeGrupe != $novoImeGrupe)
			con_update("update UserGroups set UGrp_Name='".$novoImeGrupe."', UGrp_Super=0 where UGrp_Id=".$idGrupe);

		$arrTipId = array_keys($tipPrava);
		for ($i=0; $i<count($arrTipId); $i++){
			$idTipa = $arrTipId[$i];
			$pravo = $tipPrava[$idTipa];

			$strSQL = "select OSec_Rights from SecurityObjects";
			$strSQL .= " where OSec_ObjT_Id=".$idTipa." and OSec_UGrp_Id=".$idGrupe;
			
			$oldPravo = con_getValue($strSQL);

			if (utils_valid($oldPravo) && $pravo != con_getValue($strSQL)){
				con_update("update SecurityObjects set OSec_Rights=".$pravo." where OSec_UGrp_Id=".$idGrupe." and OSec_ObjT_Id=".$idTipa);
			} else {
				if (utils_valid($pravo)){
					con_update("insert into SecurityObjects(OSec_ObjT_Id, OSec_UGrp_Id, OSec_Rights) values (".$idTipa.", ".$idGrupe.", ".$pravo.")");
				}else{
					con_update("insert into SecurityObjects(OSec_ObjT_Id, OSec_UGrp_Id, OSec_Rights) values (".$idTipa.", ".$idGrupe.", 0)");
				}
			}
		}
		return $novoImeGrupe;
	}

/*Radi update prava na drvetu SMa	
=================================*/
	function users_updateSMPrava($type, $id, $idGrupe, $hijerarhija, $roditeljPravo = NULL){
		switch ($type){
			case "Root" : 
				$records = root_getAllVerzija(1);
				for ($i = 0; $i < count($records); $i++)
					users_updateSMPrava("Verzija", $records[$i]["Verz_Id"], $idGrupe, $hijerarhija, $roditeljPravo);
				
				//opste pravo verzija
				$opstePravoVerzija = utils_requestStr(getPVar("Verz_General"));
				$strSQL = "update SecurityVerzija set VSec_Rights=".$opstePravoVerzija;
				$strSQL .= " where VSec_UGrp_Id=".$idGrupe." and VSec_Verz_Id is null";
				con_update($strSQL);

				//opste pravo sekcija
				$opstePravoSekcija = utils_requestStr(getPVar("Sekc_General"));
				$strSQL = "update SecuritySekcija set SkSec_Rights=".$opstePravoSekcija;
				$strSQL .= " where SkSec_UGrp_Id=".$idGrupe." and SkSec_Sekc_Id is null";
				con_update($strSQL);

				//opste pravo stranica
				$opstePravoStranica = utils_requestStr(getPVar("Stra_General"));
				$strSQL = "update SecurityStranica set StSec_Rights=".$opstePravoStranica;
				$strSQL .= " where StSec_UGrp_Id=".$idGrupe." and StSec_Stra_Id is null";
				con_update($strSQL);

				break;
			case "Verzija" : 
				$whereAppend = " where VSec_UGrp_Id=".$idGrupe." and VSec_Verz_Id=".$id;

				$staroPravo = con_getValue("select VSec_Rights from SecurityVerzija" . $whereAppend);	
				$novoPravo = getPVar("Verz_Rights" . $id);
				$novoPravo = ($novoPravo == "" || $novoPravo == "undefined") ? "" : $novoPravo;
//utils_dump($id." '". $staroPravo."' '".$novoPravo."'");

				if (!utils_valid($novoPravo)){//povratak na generalno
					if (utils_valid($staroPravo))
						con_update("delete from SecurityVerzija" . $whereAppend);	
				} else {
					if (utils_valid($staroPravo)){//update starog prava
						con_update("update SecurityVerzija set VSec_Rights=".$novoPravo . $whereAppend);	
					} else{//potpuno novo pravo
						con_update("insert into SecurityVerzija(VSec_UGrp_Id, VSec_Rights, VSec_Verz_Id) values(".$idGrupe.", ".$novoPravo.", ".$id.")");
					}
				}

				$records = verzija_getAllSekcija($id);
				for ($i = 0; $i < count($records); $i++)
					users_updateSMPrava("Sekcija", $records[$i]["Sekc_Id"], $idGrupe, $hijerarhija, NULL);
				
				break;
			case "Sekcija":
				$whereAppend = " where SkSec_UGrp_Id=".$idGrupe." and SkSec_Sekc_Id=".$id;
				
				$staroPravo = con_getValue("select SkSec_Rights from SecuritySekcija" . $whereAppend);	
				$novoPravo = getPVar("Sekc_Rights" . $id);
				$novoPravo = ($novoPravo == "" || $novoPravo == "undefined") ? "" : $novoPravo;
				
				if ($hijerarhija == "1" && !is_null($roditeljPravo)){
					if ($novoPravo == $staroPravo)
						$novoPravo = $roditeljPravo;
				}
//utils_dump($id." ".con_getValue("select Sekc_Naziv from Sekcija where Sekc_Id=".$id).": '". $staroPravo."' '".$novoPravo."' '".$roditeljPravo."'");

				if (!utils_valid($novoPravo)){//povratak na generalno
					if (utils_valid($staroPravo))
						con_update("delete from SecuritySekcija" . $whereAppend);	
				} else {
					if (utils_valid($staroPravo)){//update starog prava
						con_update("update SecuritySekcija set SkSec_Rights=".$novoPravo . $whereAppend);	
					} else{//potpuno novo pravo
						con_update("insert into SecuritySekcija(SkSec_UGrp_Id, SkSec_Rights, SkSec_Sekc_Id) values(".$idGrupe.", ".$novoPravo.", ".$id.")");
					}
				}

				if ($novoPravo > 0)
					users_getValidationSectionUp($idGrupe, $id);

				$records = sekcija_getAllPodsekcija($id);
				for ($i = 0; $i < count($records); $i++)
					users_updateSMPrava("Sekcija", $records[$i]["Sekc_Id"], $idGrupe, $hijerarhija, $novoPravo);
				
				$records = sekcija_getAllStranica($id);
				for ($i = 0; $i < count($records); $i++)
					users_updateSMPrava("Stranica", $records[$i]["Stra_Id"], $idGrupe, $hijerarhija, $novoPravo);

				break;
			case "Stranica":
				$whereAppend = " where StSec_UGrp_Id=".$idGrupe." and StSec_Stra_Id=".$id;

				$staroPravo = con_getValue("select StSec_Rights from SecurityStranica" . $whereAppend);	
				$novoPravo = getPVar("Stra_Rights" . $id);
				$novoPravo = ($novoPravo == "" || $novoPravo == "undefined") ? "" : $novoPravo;

				if ($hijerarhija == "1" && !is_null($roditeljPravo)){
					$roditeljPravo = ($roditeljPravo == 3) ? 2 : $roditeljPravo;
					if ($novoPravo == $staroPravo)
						$novoPravo = $roditeljPravo;
				}
//utils_dump($id." '". $staroPravo."' '".$novoPravo."'");

				if (!utils_valid($novoPravo)){//povratak na generalno
					if (utils_valid($staroPravo))
						con_update("delete from SecurityStranica" . $whereAppend);	
				} else {
					if (utils_valid($staroPravo)){//update starog prava
						con_update("update SecurityStranica set StSec_Rights=".$novoPravo . $whereAppend);	
					} else{//potpuno novo pravo
						con_update("insert into SecurityStranica(StSec_UGrp_Id, StSec_Rights, StSec_Stra_Id) values(".$idGrupe.", ".$novoPravo.", ".$id.")");
					}
				}

				if ($novoPravo > 0)
					users_getValidationPageUp($idGrupe, $id);
//				
				break;
		}

	}

/*Brisanje usera
================*/
	function users_deleteUser($idUsera){
		con_update("delete from Users where User_Id=".$idUsera);
	}

/*Kreiranje usera
=================*/
	function users_insertUser($idGrupe, $imeUsera, $sifUsera){
		$updateAllowed = true;
		//provera da li takav sa novim imeUsera i sifUsera postoji vec
		$count = con_getValue("select count(*) from Users where User_Name ='".$imeUsera."'");
		if (intval($count) > 0){
?>	<script>alert("<?php echo ocpLabels("User with given username already exist")?>.");</script>
<?php		$updateAllowed = false;
		}
		
		if ($updateAllowed){
			$strSQL = "insert into Users(User_UGrp_Id, User_Name, User_Password)";
			$strSQL .= " values (".$idGrupe.",'".$imeUsera."', '".md5($sifUsera)."')";
			con_update($strSQL);
		}
		return $updateAllowed;
	}

/*Update usera
==============*/
	function users_updateUser($idGrupe, $idUsera, $imeUsera, $sifUsera){
		$updateAllowed = true;

		//provera da li takav sa novim imeUsera i sifUsera postoji vec
		$count = con_getValue("select count(*) from Users where User_Name ='".$imeUsera."' and User_Id <> ".$idUsera);
		if (intval($count) > 0){	?>
	<script>alert("<?php echo ocpLabels("User with given username already exist")?>.");</script>
<?php		$updateAllowed = false;
		}

		if ($updateAllowed){
			$strSQL = "update Users set User_Name='".$imeUsera."', User_Password='".md5($sifUsera)."', User_UGrp_Id = ".$idGrupe;
			$strSQL .= " where User_Id =".$idUsera;
			con_update($strSQL);
		}
		return $updateAllowed;
	}

/*Save settings usera
=====================*/
	function users_saveSettings($idGrupe, $idUsera, $userWidth, $userHeight, $userLanguage){
		if (!utils_valid($userWidth)) $userWidth = "NULL";
		$idGrupe = (!utils_valid($idGrupe)) ? 1 : $idGrupe;
		$strSQL = "update Users set User_Width=".$userWidth;
		if (utils_valid($userHeight)) $strSQL .= ", User_Height='".$userHeight."'";
		if (utils_valid($userLanguage)){
			$strSQL .= ", User_Language=".$userLanguage;
			setSVar("ocpUserLanguage", $userLanguage);
		}
		$strSQL .= " where User_UGrp_Id = ".$idGrupe." and  User_Id =".$idUsera;
//		echo ($strSQL);
		con_update($strSQL);
	}

/*User po imenu
=====================*/
	function users_getUserByName($imeUsera){
		return con_getResult("select * from Users where User_Name = '".$imeUsera."' and User_Valid = 1");
	}

/*User po id-u
=====================*/
	function users_getUserById($idUsera){
		return con_getResult("select * from Users where User_Id = ".$idUsera);
	}

/*Save password usera
=====================*/
	function users_savePassword($idUsera, $newPassword){
		con_update("update Users set User_Password = '".md5($newPassword)."' where User_Id = ".$idUsera." and User_Valid = 1");
	}

/*Fja koja u ocpTR, ocpVR, ocpSR, ocpPR smesta prava usera
Zove se iz login.php
==============================================*/
	function users_checkLogin($user, $pass) {
		$pass = md5($pass);

		$strSQL = "SELECT * FROM UserGroups, Users WHERE User_Name='".$user."' AND User_Password='".$pass."'";
		$strSQL .= " AND UGrp_Id = User_UGrp_Id AND UGrp_Valid = 1 AND User_Valid = 1";

		$ocpUser = con_getResults($strSQL);

		if (isset($ocpUser[0]["UGrp_Id"])) {

			$d = array();
			if ($ocpUser[0]["User_Name"] == $user){
				
				//!!!sprecava Session fixation
				session_regenerate_id();

				if (($ocpUser[0]["UGrp_Super"] == "1")){ // superuser
//tipovi
					$d = array();
					$tipovi = tipobj_getIdsTipovi();
					for ($i=0; $i<count($tipovi); $i++) $d[$tipovi[$i]] = 4;
					$_SESSION["ocpTR"] = $d;
//verzije
					$d = array("Default" => 4);
					$verzije = verzija_getIds();
					for ($i=0; $i<count($verzije); $i++) $d[$verzije[$i]] = 4;
					$_SESSION["ocpVR"] = $d;
//sekcije					
					$d = array("Default" => 4);
					$sekcije = sekcija_getIds();
					for ($i=0; $i<count($sekcije); $i++) $d[$sekcije[$i]] = 4;
					$_SESSION["ocpSR"] = $d;
//stranice					
					$d = array("Default" => 4);
					$stranice = stranica_getIds();
					for ($i=0; $i<count($stranice); $i++) $d[$stranice[$i]] = 4;
					$_SESSION["ocpPR"] = $d;
//izvestaji
					$d = array("Default" => "1");
					$izvestaji = izv_getIds();
					for ($i=0; $i<count($izvestaji); $i++) $d[$izvestaji[$i]] = 1;
					$_SESSION["ocpRR"] = $d;
					
					$_SESSION["ocpUserGroup"] = "null";
					$_SESSION["ocpUserLanguage"] = $ocpUser[0]["User_Language"];
				} else { // nije superuser
//tipovi
					// nije superuser
					$d = array();
					$tipovi = users_getGroupTypeRights($ocpUser[0]["UGrp_Id"]);
					for ($i=0; $i<count($tipovi); $i++)	$d[$tipovi[$i]["Id"]] = $tipovi[$i]["Rights"];
					$_SESSION["ocpTR"] = $d;
//verzije					
					$d = array();
					$verzije = verzija_getIds();
					$Prava = verzija_getRights($ocpUser[0]["UGrp_Id"]);
					foreach ($verzije as $i){
						if (array_key_exists($i, $Prava)) {
							$d += array($i => $Prava[$i]);
						} else {
							$d += array($i => $Prava[""]);
						}
					}
					$d += array("Default" => $Prava[""]);
					$_SESSION["ocpVR"] = $d;
//sekcije					
					$d = array();
					$sekcije = sekcija_getIds();
					$Prava = sekcija_getRights($ocpUser[0]["UGrp_Id"]);
					foreach ($sekcije as $i){
						if (array_key_exists($i, $Prava)) {
							$d += array($i => $Prava[$i]);
						} else {
							$d += array($i => $Prava[""]);
						}
					}
					$d += array("Default" => $Prava[""]);
					$_SESSION["ocpSR"] = $d;
//stranice					
					$d = array();
					$stranice = stranica_getIds();
					$Prava = stranica_getRights($ocpUser[0]["UGrp_Id"]);
					foreach ($stranice as $i){
						if (array_key_exists($i, $Prava)) {
							$d += array($i => $Prava[$i]);
						} else {
							$d += array($i => $Prava[""]);
						}
					}
					$d += array("Default" => $Prava[""]);
					$_SESSION["ocpPR"] = $d;
//izvestaji
					$d = array("Default" => "0");
					$izvestaji = izv_getGroupReportRights($ocpUser[0]["UGrp_Id"]);
					for ($i=0; $i<count($izvestaji); $i++) $d += array($izvestaji[$i]["Id"] => $izvestaji[$i]["Rights"]);
					$_SESSION["ocpRR"] = $d;

					$_SESSION["ocpUserGroup"] = $ocpUser[0]["UGrp_Id"];
				}

				$_SESSION["ocpLocalAddress"] = con_getValue("select Root_LokalnaAdresa from Root where Root_Valid = 1");
				$_SESSION["ocpUsername"] = $user;
				$_SESSION["ocpUserId"] = $ocpUser[0]["User_Id"];
				$_SESSION["ocpUserWidth"] = $ocpUser[0]["User_Width"];
				$_SESSION["ocpUserHeight"] = $ocpUser[0]["User_Height"];
				
				$_SESSION["ocpLanguage"] = con_getValue("select OcpJezik from Ocp where Id = 1");
				if (utils_valid($_SESSION["ocpLanguage"]) && ($_SESSION["ocpLanguage"] != 0)){
					$_SESSION["ocpLabels"] = con_getResultsDict("select Labela, Vrednost from OcpPrevod, OcpLabela where IdLabele = OcpLabela.Id and IdJezika = ".$_SESSION["ocpLanguage"]);
				} else {
					$_SESSION["ocpLabels"] = con_getResultsDict("select Labela, Vrednost from OcpPrevod, OcpLabela where IdLabele = OcpLabela.Id and IdJezika = ".$ocpUser[0]["User_Language"]);
				}
				$_SESSION["ocpUserLanguage"] = $ocpUser[0]["User_Language"];

				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

/*Fja koja proverava da li je korektna promena 
prava na sekciji
==============================================*/
	function users_getValidationSectionUp($idGrupe, $sekc_Id){
		global $napomene;

		$strSQL = null;
		$retString = "";

		$strSQL = "SELECT Sekc_Naziv, Sekc_Verz_Id, Sekc_ParentId";
		$strSQL .= " FROM Sekcija WHERE Sekc_Id = ".$sekc_Id;

		$record = con_getResult($strSQL);
		$nazivSekcije = $record["Sekc_Naziv"];
		$verzId = $record["Sekc_Verz_Id"];
		$parentId = $record["Sekc_ParentId"];

//sve do nivo verzije
		while (!is_null($parentId) && ($parentId != "") && ($parentId != "null")){
			$strSQL = "select Sekc_Id, Sekc_Naziv, Sekc_ParentId ";
			$strSQL .= " from Sekcija";
			$strSQL .= " where Sekc_Id=".$parentId ;

			$record = con_getResult($strSQL);
			$parentId = $record["Sekc_ParentId"];
			$newSekcNaziv = $record["Sekc_Naziv"];
			$newSekcId = $record["Sekc_Id"];
			
			$strSQL = "select SkSec_Rights from SecuritySekcija";
			$strSQL .= " where ".$newSekcId."=SkSec_Sekc_Id and SkSec_UGrp_Id=".$idGrupe;
			//echo($strSQL);
			$recordTemp = con_getResult($strSQL);
			$pravo = $recordTemp["SkSec_Rights"];

			$correct = true;
			if (!is_null($pravo)){ //ima partikularno
				if ($pravo == "0") $correct = false;
			} else { //trazi generalno
				$strSQL = "select SkSec_Rights";
				$strSQL .= " from SecuritySekcija";
				$strSQL .= " where SkSec_Sekc_Id is null and SkSec_UGrp_Id=".$idGrupe;

				$recordTemp = con_getResult($strSQL);
				$pravo = $recordTemp["SkSec_Rights"];

				if ($pravo == "0") $correct = false;
			}

			if (!$correct){
				$retString = ocpLabels("Changes on section")." ".$nazivSekcije." ".ocpLabels(" are not correct.");
				$retString .= ocpLabels("Section")." ".$newSekcNaziv." ".ocpLabels(" is not visible."); 

				$parentId = "null";
				break;
			}
		}
		
		//echo($retString);

//Ako nije nadjeno na sekcijama trazimo na verziji
		if ($retString == ""){
			$strSQL = "select Verz_Naziv";
			$strSQL .= " from Verzija";
			$strSQL .= " where Verz_Id=".$verzId;
			$record = con_getResult($strSQL);
			$verzijaNaziv = $record["Verz_Naziv"];
			
			$strSQL = "select VSec_Rights from SecurityVerzija";
			$strSQL .= " where ".$verzId."=VSec_Verz_Id and VSec_UGrp_Id=".$idGrupe;
			$record = con_getResult($strSQL);
			$pravo = $record["VSec_Rights"];

			$correct = true;
			
			if (!is_null($pravo)){ //ima partikularno
				if ($pravo == "0") $correct = false;
			} else { //trazi generalno
				$strSQL = "select VSec_Rights from SecurityVerzija";
				$strSQL .= " where VSec_Verz_Id is null and VSec_UGrp_Id=".$idGrupe;
				$record = con_getResult($strSQL);
				$pravo = $record["VSec_Rights"];

				if ($pravo == "0") $correct = false;
			}

			if (!$correct){
				$retString = ocpLabels("Changes on section")." ".$nazivSekcije." ".ocpLabels(" are not correct.");
				$retString .= ocpLabels("Version")." ".$verzijaNaziv." ".ocpLabels(" is not visible."); 

			}
		}
		
		$napomene .= $retString;
	}

/*Fja koja proverava da li je korektna promena 
prava na stranici
==============================================*/
	function users_getValidationPageUp($idGrupe, $stra_Id){
		global $napomene;

//utils_dump("validationPageUp -start");
		$strSQL = null;
		$retString = "";
		
//samo prvi nivo iznad za pocetak
		$strSQL = "SELECT Stra_Naziv, Sekc_Id, Sekc_Naziv";
		$strSQL .= " FROM Stranica, Sekcija WHERE Stra_Id = ".$stra_Id." and Sekc_Id=Stra_Sekc_Id";

		$record = con_getResult($strSQL);
		$nazivStranice = $record["Stra_Naziv"];
		$newSekcNaziv = $record["Sekc_Naziv"];		
		$sekcId = $record["Sekc_Id"];

		$record = con_getResult($strSQL);
		$strSQL = "select SkSec_Rights from SecuritySekcija";
		$strSQL .= " where SkSec_Sekc_Id=".$sekcId." and SkSec_UGrp_Id=".$idGrupe;
		$record = con_getResult($strSQL);
		$pravo = $record["SkSec_Rights"];

		$correct = true;

		if (!is_null($pravo)){ //ima partikularno
			if ($pravo == 0) $correct = false;
		} else { //trazi generalno
			$strSQL = "select SkSec_Rights from SecuritySekcija";
			$strSQL .= " where SkSec_Sekc_Id is null and SkSec_UGrp_Id=".$idGrupe;

			$record = con_getResult($strSQL);
			$pravo = $record["SkSec_Rights"];

			if ($pravo == 0) $correct = false;
		}

		if (!$correct){
			$retString = ocpLabels("Changes on page")." ".$nazivStranice.ocpLabels(" are not correct.");
			$retString .=  ocpLabels("Section")." ".$newSekcNaziv.ocpLabels(" is not visible."); 
		}

//Ako nije nadjeno na sekciji odmah iznad trazimo nad njom
		if ($retString == ""){
			$retString = users_getValidationSectionUp($idGrupe, $sekcId);
			if ($retString != ""){
				$dotIndex = strpos($retString, ".");
				$retString = utils_substr($retString, $dotIndex+1, utils_strlen($retString) - $dotIndex);
				$retString = ocpLabels("Changes on page")." ".$nazivStranice.ocpLabels(" are not correct.") .$retString;
				//echo($retString);
			}
		}
//utils_dump("validationPageUp ".$retString);

		$napomene .= $retString;
	}
?>