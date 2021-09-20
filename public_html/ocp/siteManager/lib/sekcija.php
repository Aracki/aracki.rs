<?php
/*Vraca sve stranice koje pripadaju sekciji Sekc_Id
===================================================*/
	function sekcija_getAllStranica($Sekc_Id, $allStranica=false){
		$strSQL = "select s.*, Sekc_Naziv from Stranica as s, Sekcija where Sekc_Id=Stra_Sekc_Id and Stra_Sekc_Id=".$Sekc_Id;
		if ((is_null($allStranica)) || (!$allStranica)) $strSQL .= " and Stra_Valid=1";
		$strSQL .= " order by Stra_RedPrikaza";

		$stranice = con_getResults($strSQL);
		for ($i=0;$i<count($stranice);$i++)
			$stranice[$i] = date_setPublishExpiry($stranice[$i], "Stra_PublishDate", "Stra_ExpiryDate");
		return $stranice;
	}

/*Vraca stranice koje ispunjavaju filter zadat Parametrom
=========================================================*/
	function sekcija_getAllStranicaFilter($Sekc_Id, $Parametar){
		$strSQL = "select distinct Stra_Id, Stra_Naziv, Stra_Prikaz, Stra_PublishDate, Stra_ExpiryDate, Stra_RedPrikaza ";
		$strSQL .= "from Stranica, Blok, Stranica_Blok where Stra_Valid=1 and Stra_Sekc_Id = ".$Sekc_Id." and Stra_Id=StBl_Stra_Id and StBl_Blok_Id=Blok_Id and (Blok_Tekst LIKE '%".$Parametar."%' or Stra_Naziv LIKE '%".$Parametar."%' or Stra_HtmlTitle LIKE '%".$Parametar."%') and Blok_Valid=1 and StBl_Valid=1 order by Stra_RedPrikaza";

		$stranice = con_getResults($strSQL);
		for ($i=0; $i<count($stranice); $i++)
			$stranice[$i] = date_setPublishExpiry($stranice[$i], "Stra_PublishDate", "Stra_ExpiryDate");
		
		return $stranice;
	}

/*Radi update patha stranice u sekciji rekurzivno
================================================*/
	function sekcija_updatePath($sekcId, $preffix = null){
		if (!utils_isRewrite()) return;

		if (is_null($preffix)){
			$urlName = con_getValue("select Sekc_LinkName from Sekcija where Sekc_Id=".$sekcId);
			
			$preffix = (utils_valid($urlName)) ? 
				"/" . convert_word($urlName) : 
				"/" . convert_word(sekcija_getProperty($sekcId, "Sekc_Naziv"));
			$parentId = sekcija_getProperty($sekcId, "Sekc_ParentId");

			while (utils_valid($parentId)){
				$result = con_getResult("select Sekc_Naziv, Sekc_ParentId, Sekc_LinkName from Sekcija where Sekc_Id=".$parentId);

				$urlName =  $result["Sekc_LinkName"];

				if (utils_valid($urlName)){
					$preffix = "/" . convert_word($urlName) . $preffix;
				} else {
					$preffix = "/" . convert_word($result["Sekc_Naziv"]) . $preffix;
				}
				$parentId = $result["Sekc_ParentId"];
			}
		}

		$results = con_getResults("select Stra_Id, Stra_Naziv, Stra_Prikaz, Stra_LinkName, Stra_Link from Stranica where Stra_Valid=1 and Stra_Sekc_Id=".$sekcId);
		for ($i=0; $i<count($results); $i++){
			$stranica = $results[$i];

			$old_path = $stranica["Stra_Link"];
			$path = "";
			
			
			$stra_urlName = $stranica["Stra_LinkName"];
			if (utils_valid($stra_urlName)){
				if ($stra_urlName != $urlName){
					$path = $preffix . "/" . convert_word($stra_urlName) . "." . $stranica["Stra_Id"] . ".html";	
				} else {
					$path = "." . $stranica["Stra_Id"] . ".html";
				}
			}else {
				if ($stranica["Stra_Prikaz"] == 1){
					$path = $preffix . "/" . convert_word($stranica["Stra_Naziv"]) . "." . $stranica["Stra_Id"] . ".html";
				} else {
					$path = $preffix . "." . $stranica["Stra_Id"] . ".html";
				}
			}
			
			
			con_update("update Stranica set Stra_Link = '".$path."' where Stra_Id=".$stranica["Stra_Id"]);

			if ($old_path != $path){
				convert_blockLinks(REWRITE_REWRITE_CHANGED_PATH, array("old"=>$old_path, "new"=>$path));
				convert_objectLinks(REWRITE_REWRITE_CHANGED_PATH, array("old"=>$old_path, "new"=>$path));
			}
		}
		
		$results = con_getResults("select Sekc_Id, Sekc_Naziv, Sekc_LinkName from Sekcija where Sekc_Valid=1 and Sekc_ParentId=".$sekcId);
		for ($i=0; $i<count($results); $i++){
			$urlName = $results[$i]["Sekc_LinkName"];
			
			if (utils_valid($urlName)){
				sekcija_updatePath($results[$i]["Sekc_Id"], $preffix . "/" . convert_word($urlName));
			} else {
				sekcija_updatePath($results[$i]["Sekc_Id"], $preffix . "/" . convert_word($results[$i]["Sekc_Naziv"]));
			}
		}
	}

/*Vraca sve podsekcije koje pripadaju sekciji Sekc_Id
=====================================================*/
	function sekcija_getAllPodsekcija($Sekc_Id, $allSection=false){
		$strSQL = "select * from Sekcija where Sekc_ParentId = ".$Sekc_Id;
		if ((is_null($allSection)) || (!$allSection)) $strSQL .= " and Sekc_Valid=1";
		$strSQL .= " order by Sekc_RedPrikaza";
		return con_getResults($strSQL);
	}

/*Vraca sve podsekcije koje pripadaju sekciji Sekc_Id i ispunjavaju zadat Filter
================================================================================*/
	function sekcija_getAllPodsekcijaFilter($Sekc_Id, $Parametar){
		$strSQL = "select * from Sekcija";
		$strSQL .= " where Sekc_ParentId = ".$Sekc_Id." and Sekc_Valid=1 and Sekc_Naziv LIKE '%".$Parametar."%'";
		$strSQL .= " order by Sekc_RedPrikaza";
		return con_getResults($strSQL);
	}

/*Vraca Id-ove svih validnih sekcija koje postoje u bazi
========================================================*/
	function sekcija_getIds($Verz_Id = NULL, $notvalid = 0){
		if ($notvalid) return con_getResultsArr("select Sekc_Id from Sekcija where Sekc_Valid=0"); 

		$strSQL = "select Sekc_Id from Sekcija where Sekc_Valid=1";
		if (!is_null($Verz_Id)) $strSQL .= " and Sekc_Verz_Id=".$Verz_Id;
		$strSQL .= " order by Sekc_Id asc";
		return con_getResultsArr($strSQL);
	}

/*Kreira novu sekciju
=====================*/
	function sekcija_new($sekcija){
		if (!verzija_security(3, $sekcija["Sekc_Verz_Id"])){?>
	<script>alert("<?php echo ocpLabels("You don\'t have sufficient privileges for this operation");?>");</script>
<?php			return ;
		}
		
		$sekcija = smobj_beforeInsert("Sekcija", $sekcija);

		$temp = con_getValue("select Max(Sekc_RedPrikaza) from Sekcija where Sekc_Valid=1 and Sekc_Verz_Id=".$sekcija["Sekc_Verz_Id"]." and Sekc_ParentId is null");
		$max = 0;
		if (utils_valid($temp)){
			$max = intval($temp);
			++$max;
		}

		$insert = "insert into Sekcija (Sekc_Verz_Id, Sekc_Naziv, Sekc_RedPrikaza, Sekc_LastModify";
		$values = " values (".$sekcija["Sekc_Verz_Id"].", '".$sekcija["Sekc_Naziv"]."', ".$max.", '".date_getMiliseconds()."'";

		if	(isset($sekcija["Sekc_ExtraParams"]) && utils_valid($sekcija["Sekc_ExtraParams"])){
			$insert .= ",Sekc_ExtraParams";
			$values .= ",'".$sekcija["Sekc_ExtraParams"]."'";
		}
		if	(isset($sekcija["Sekc_HtmlKeywords"]) && utils_valid($sekcija["Sekc_HtmlKeywords"])){
			$insert .= ",Sekc_HtmlKeywords";
			$values .= ",'".$sekcija["Sekc_HtmlKeywords"]."'";
		}
		if	(isset($sekcija["Sekc_HtmlDescription"]) && utils_valid($sekcija["Sekc_HtmlDescription"])){
			$insert .= ",Sekc_HtmlDescription";
			$values .= ",'".$sekcija["Sekc_HtmlDescription"]."'";
		}
		if	(isset($sekcija["Sekc_LinkName"]) && utils_valid($sekcija["Sekc_LinkName"])){
			$insert .= ",Sekc_LinkName";
			$values .= ",'".$sekcija["Sekc_LinkName"]."'";
		}
		$insert .= ")";
		$values .= ")";
		$sekcija["Sekc_Id"] = con_insert($insert.$values);

		$userGroup = getSVar("ocpUserGroup");
		if (utils_valid($userGroup))
			con_update("insert into SecuritySekcija(SkSec_UGrp_Id, SkSec_Sekc_Id, SkSec_Rights) values(".$userGroup.", ".$sekcija["Sekc_Id"].", 4)");
		log_append("Insert", "Sekcija", $sekcija["Sekc_Id"]);
		setSVar('ocpSR', 4, $sekcija["Sekc_Id"]);
		utils_updateSiteMenu();

		smobj_afterInsert("Sekcija", $sekcija);
	}

/*Azurira sekciju
=================*/
	function sekcija_edit($sekcija){
		if (!sekcija_security(2, $sekcija["Sekc_Id"])){	?>
	<script>alert("<?php echo ocpLabels("You don\'t have sufficient privileges for this operation");?>");</script>
<?php			return ;
		}
		
		$sekcija = smobj_beforeUpdate("Sekcija", $sekcija);

		$strSQL = "update Sekcija set Sekc_Naziv='".$sekcija["Sekc_Naziv"]."', Sekc_LastModify='".date_getMiliseconds()."'";
		if (utils_valid($sekcija["Sekc_Stra_Id"])) $strSQL .= ", Sekc_Stra_Id=".$sekcija["Sekc_Stra_Id"];
		else $strSQL .= ", Sekc_Stra_Id=null";
		if (utils_valid($sekcija["Sekc_HtmlKeywords"])) $strSQL .= ", Sekc_HtmlKeywords='".$sekcija["Sekc_HtmlKeywords"]."'";
		else $strSQL .= ", Sekc_HtmlKeywords=null";
		if (utils_valid($sekcija["Sekc_HtmlDescription"])) $strSQL .= ", Sekc_HtmlDescription='".$sekcija["Sekc_HtmlDescription"]."'";
		else $strSQL .= ", Sekc_HtmlDescription=null";
		if (utils_valid($sekcija["Sekc_LinkName"])) $strSQL .= ", Sekc_LinkName='".$sekcija["Sekc_LinkName"]."'";
		else $strSQL .= ", Sekc_LinkName=null";
		if	(isset($sekcija["Sekc_ExtraParams"]) && utils_valid($sekcija["Sekc_ExtraParams"])) 	$strSQL .= ",Sekc_ExtraParams = '".$sekcija["Sekc_ExtraParams"]."'";
		else $strSQL .= ",Sekc_ExtraParams = null";
		$strSQL .= " where Sekc_Id=".$sekcija["Sekc_Id"]." and Sekc_Valid=1";
		if (utils_valid($sekcija["Sekc_LastModify"]))
			$strSQL .= "  and Sekc_LastModify='".$sekcija["Sekc_LastModify"]."'";

		$affected = con_update($strSQL);
		if (intval($affected) == 0){	?>
	<script>alert("<?php echo ocpLabels("Another user has changed object. Your changes are not saved");?>");</script>
<?php		} else {
			log_append("Update", "Sekcija", $sekcija["Sekc_Id"]);
			
			sekcija_updatePath($sekcija["Sekc_Id"], null);

			utils_updateSiteMenu();

			smobj_afterInsert("Sekcija", $sekcija);
		}
	}

/*Brisanje sekcije, tj. postavljanje valid sekcije i svih njenih potomaka na 0
==============================================================================*/
	function sekcija_delete($Sekc_Id, $TransFree){
		if (!sekcija_security(4, $Sekc_Id)){	?>
	<script>alert("<?php echo ocpLabels("You don\'t have sufficient privileges for this operation");?>");</script>
<?php		return ;
		}

		smobj_beforeDelete("Sekcija", $Sekc_Id);

		$podsekcije = sekcija_getAllPodsekcija($Sekc_Id);
		if (count($podsekcije) > 0){
			for ($j=0;$j<count($podsekcije);$j++){
				$podsekcija = $podsekcije[$j];
				sekcija_delete($podsekcija["Sekc_Id"], true);
				$stranice = sekcija_getAllStranica($Sekc_Id);
				for ($k=0;$k<count($stranice);$k++){
					$stranica = $stranice[$k];
					stranica_delete($stranica["Stra_Id"], true, 
						utils_requestStr(getPVar("KillLink".$stranica["Stra_Id"])), 
						utils_requestStr(getPVar("NewLink".$stranica["Stra_Id"])) );
				}
			}
		} else{
			$stranice = sekcija_getAllStranica($Sekc_Id);
			for ($k=0;$k<count($stranice);$k++){
				$stranica = $stranice[$k];
				stranica_delete($stranica["Stra_Id"], true, 
						utils_requestStr(getPVar("KillLink".$stranica["Stra_Id"])), 
						utils_requestStr(getPVar("NewLink".$stranica["Stra_Id"])) );
			}
		}
		
		con_update("update Sekcija set Sekc_Valid=0 where Sekc_Id=".$Sekc_Id);
		con_update("delete from SecuritySekcija where SkSec_Sekc_Id=".$Sekc_Id);
		utils_updateSiteMenu();

		smobj_afterDelete("Sekcija", $Sekc_Id);
	}

/*Vadjenje svih stranica koje pripadaju ovoj sekciji
puni se globalni niz straniceDepth
==============================================================================*/
	function sekcija_getAllDepthStranice($Sekc_Id){
		global $straniceDepth;

		$straniceDepth = array_merge($straniceDepth, sekcija_getAllStranica($Sekc_Id));
		$podsekcije = sekcija_getAllPodsekcija($Sekc_Id);
		for ($j=0;$j<count($podsekcije);$j++)
			sekcija_getAllDepthStranice($podsekcije[$j]["Sekc_Id"]);
	}

/*Vraca sekciju odredjenog Id-a
===============================*/
	function sekcija_get($Sekc_Id){
		$sekcija = con_getResult("select * from Sekcija where Sekc_Valid=1 and Sekc_Id=".$Sekc_Id);
		if (!isset($sekcija["Sekc_HtmlKeywords"]) || !utils_valid($sekcija["Sekc_HtmlKeywords"])) $sekcija["Sekc_HtmlKeywords"] = "";
		if (!isset($sekcija["Sekc_HtmlDescription"]) || !utils_valid($sekcija["Sekc_HtmlDescription"])) $sekcija["Sekc_HtmlDescription"] = "";
		return $sekcija;
	}

/*Vraca odredjeni property sekcije Sekc_Id
==========================================*/
	function sekcija_getProperty($Sekc_Id, $PropertyName){
		return con_getValue("select ".$PropertyName." from Sekcija where Sekc_Id=".$Sekc_Id);
	}

/*Kreira novu podsekciju 
========================*/
	function sekcija_newPodsekcija($podsekcija){
		if (!sekcija_security(3, $podsekcija["Sekc_ParentId"])){	?>
	<script>alert("<?php echo ocpLabels("You don\'t have sufficient privileges for this operation");?>");</script>
<?php			return ;
		}

		$podsekcija = smobj_beforeInsert("Sekcija", $podsekcija);


		$temp = con_getValue("select Max(Sekc_RedPrikaza) from Sekcija where Sekc_Valid=1 and Sekc_Verz_Id=".$podsekcija["Sekc_Verz_Id"]." and Sekc_ParentId=".$podsekcija["Sekc_ParentId"]);
		$max = 0;
		if (utils_valid($temp)){
			$max = intval($temp);
			++$max;
		}

		$insert = "insert into Sekcija (Sekc_Verz_Id, Sekc_Naziv, Sekc_RedPrikaza, Sekc_ParentId, Sekc_LastModify";
		$values = " values (".$podsekcija["Sekc_Verz_Id"].", '".$podsekcija["Sekc_Naziv"]."', ".$max.",".$podsekcija["Sekc_ParentId"].", '".date_getMiliseconds()."'";

		if	(utils_valid($podsekcija["Sekc_ExtraParams"])){
			$insert .= ",Sekc_ExtraParams";
			$values .= ",'".$podsekcija["Sekc_ExtraParams"]."'";
		}

		if	(utils_valid($podsekcija["Sekc_HtmlKeywords"])){
			$insert .= ", Sekc_HtmlKeywords";
			$values .= ", '".$podsekcija["Sekc_HtmlKeywords"]."'";
		}

		if	(utils_valid($podsekcija["Sekc_HtmlDescription"])){
			$insert .= ", Sekc_HtmlDescription";
			$values .= ", '".$podsekcija["Sekc_HtmlDescription"]."'";
		}

		if	(utils_valid($podsekcija["Sekc_LinkName"])){
			$insert .= ", Sekc_LinkName";
			$values .= ", '".$podsekcija["Sekc_LinkName"]."'";
		}

		$insert .= ")";
		$values .= ")";
		$podsekcija["Sekc_Id"] = con_insert($insert.$values);

		$userGroup = getSVar("ocpUserGroup");
		if (utils_valid($userGroup)){
			con_update("insert into SecuritySekcija(SkSec_UGrp_Id, SkSec_Sekc_Id, SkSec_Rights) values(".$userGroup.", ".$podsekcija["Sekc_Id"].", 4)");
		}
		
		setSVar('ocpSR', 4, $podsekcija["Sekc_Id"]);
		log_append("Insert", "Sekcija", $podsekcija["Sekc_Id"]);
		utils_updateSiteMenu();

		smobj_afterInsert("Sekcija", $podsekcija);
	}

/*Smesta sekciju pod sekciju ParentId
=====================================*/
	function sekcija_changeSekcija2Subsekcija($Sekc_Id, $ParentId){
		if (!sekcija_security(3, $ParentId)){	?>
	<script>alert("<?php echo ocpLabels("You don\'t have sufficient privileges for this operation");?>");</script>
<?php			return ;
		}
		$sekcija = sekcija_get($ParentId);
		$OrderParent = con_getValue("select Sekc_Id from Sekcija where Sekc_ParentId=".$ParentId." order By Sekc_RedPrikaza desc");
		con_update("update Sekcija set Sekc_ParentId=".$ParentId.", Sekc_Verz_Id=".$sekcija["Sekc_Verz_Id"]." where Sekc_Id=".$Sekc_Id);	
		$ret = sekcija_changeParentPodsekcija($Sekc_Id, $sekcija["Sekc_Verz_Id"]);
		if (!$ret) return;
		sekcija_changeLastOrder($Sekc_Id, $ParentId, $sekcija["Sekc_Verz_Id"]);
		utils_updateSiteMenu();
	}

/*Premesta sekciju pod novu sekciju
===================================*/
	function sekcija_changePosition($Sekc_Id, $ParentId){
		$sekcija = sekcija_get($ParentId);
		con_update("update Sekcija set Sekc_ParentId=NULL, Sekc_Verz_Id=".$ParentId." where Sekc_Id=".$Sekc_Id);
		$ret = sekcija_changeParentPodsekcija($Sekc_Id, $ParentId);
		if (!$ret) return;
		sekcija_changeLastOrder($Sekc_Id, "null", $ParentId);
		utils_updateSiteMenu();
	}

/*Menja verziju svim podsekcijama
=================================*/
	function sekcija_changeParentPodsekcija($Sekc_Id, $Verz_Id){
		if (!verzija_security(3, $Verz_Id)){	?>
<script>alert("<?php echo ocpLabels("You don\'t have sufficient privileges for this operation");?>");</script>
<?php		return false;
		}
		$podsekcije = sekcija_getAllPodsekcija($Sekc_Id);
		if (count($podsekcije) > 0){
			for ($j=0;$j<count($podsekcije);$j++){
				$podsekcija = $podsekcije[$j];
				$ret = sekcija_changeParentPodsekcija($podsekcija["Sekc_Id"], $Verz_Id);
				if (!$ret) return false;
				con_update("update Sekcija set Sekc_Verz_Id=".$Verz_Id." where Sekc_Id=".$podsekcija["Sekc_Id"]);
			}
		} 
		return true;
	}

/*smesta sekciju pod sekciju ili samo pod verziju i to na poslednje mesto
=========================================================================*/
	function sekcija_changeLastOrder($Sekc_Id, $ParentId, $Verz_Id){
		$strSQL = "select max(Sekc_RedPrikaza) from Sekcija where Sekc_ParentId";
		if ($ParentId == "null") $strSQL .= " is null";
		else $strSQL .= "=".$ParentId;
		$strSQL .= " and Sekc_Verz_Id=".$Verz_Id;

		$temp = con_getValue($strSQL);
		$max = 0;
		if (utils_valid($temp)){
			$max = intval($temp);
			++$max;
		}
		con_update("Update Sekcija set Sekc_RedPrikaza=".$max." where Sekc_Id=".$Sekc_Id);
		utils_updateSiteMenu();
	}

/*Vraca parove sekc_Id - pravo nad sekcijom UserGroupId
=======================================================*/
	function sekcija_getRights($UserGroupId){
		$strSQL = "select SkSec_Sekc_Id, SkSec_Rights from SecuritySekcija where SkSec_UGrp_Id=".$UserGroupId;
		return con_getResultsDict($strSQL);
	}

/*Vraca pravo nad sekcijom Id
==================================================================*/
	function sekcija_getRight($Id){
		$Right = getSvar('ocpSR',$Id);
		if (is_null($Right)) $Right = getSVar('ocpSR',"Default");
		else $Right = intval($Right);
		return $Right;
	}

/*Proverava samo da li je user ima bar level pravo nad sekcijom Id
==================================================================*/
	function sekcija_security($level, $Id){
		if (sekcija_getRight($Id) >= $level) return true;
		return false;
	}

/*Vraca sve stranice koje pripadaju sekciji , ali ne koristim getAllStranica
zato sto ona ima neke naprednije opcije
==========================================================================*/
	function sekcija_getReallyAllStranica($Sekc_Id){
		$strSQL = "select Stra_Id, Stra_Naziv, Stra_Prikaz, Stra_Valid from Stranica where Stra_Sekc_Id=".$Sekc_Id." order by Stra_RedPrikaza";
		return con_getResults($strSQL);
	}

/*Recycle bin
=============*/
	function sekcija_recycleBin($sekcije){
		for ($k=0;$k<count($sekcije);$k++){
			$sekcAction = utils_requestStr(getPVar("sekc".$sekcije[$k]));
			switch($sekcAction){
				case "0": sekcija_restoreOnly($sekcije[$k], false); break;//just restore
				case "1": sekcija_restore($sekcije[$k], false); break;//restore with parents
				case "2": sekcija_reallyDelete($sekcije[$k], false); break;//delete
			}
			
		}
	}

/*Radim restore sekcije, i to samo njene definicije i isto za njene parente
koji nisu validni, ne vracam kompletan sadrzaj
====================================================================*/
	function sekcija_restoreOnly($Sekc_Id, $TransFree){
		$strSQL = "select Sekc_ParentId, Sekc_Verz_Id, Sekc_Valid from Sekcija where Sekc_Id=".$Sekc_Id;
		$parentId = "";
		$verz_id = "";
		$broj=0;

		while (!is_null($parentId)){
			$record = con_getResult($strSQL);
			$parentId = $record["Sekc_ParentId"];
			$verz_id = $record["Sekc_Verz_Id"];
			$valid = $record["Sekc_Valid"];
			if ($valid == "0") con_update("update Sekcija set Sekc_Valid=1 where Sekc_Id=".$Sekc_Id);
			$strSQL = "select Sekc_ParentId, Sekc_Verz_Id, Sekc_Valid from Sekcija where Sekc_Id=".$parentId;
		}
		
		verzija_restoreOnly($verz_id);
	}

/*Radim restore Sekcije sa svim njenim sadrzajem
================================================*/
	function sekcija_restore($Sekc_Id, $TransFree){
		$podsekcije = sekcija_getAllPodsekcija($Sekc_Id, true);
		if (count($podsekcije) > 0){
			for ($j=0;$j<count($podsekcije);$j++){
				$podsekcija = $podsekcije[$j];
				sekcija_restore($podsekcija["Sekc_Id"], true);
				$stranice = sekcija_getAllStranica($Sekc_Id, true);
				for ($k=0;$k<count($stranice);$k++){
					$stranica = $stranice[$k];
					stranica_restoreOnly($stranica["Stra_Id"]);
				}
			}
		} else{
			$stranice = sekcija_getAllStranica($Sekc_Id, true);
			for ($k=0;$k<count($stranice);$k++){
				$stranica = $stranice[$k];
				stranica_restoreOnly($stranica["Stra_Id"]);
			}
		}
		con_update("update Sekcija set Sekc_Valid=1 where Sekc_Id=".$Sekc_Id);
		utils_updateSiteMenu();
	}

/*Brisem zaista sekciju sa svim njenim sadrzajem
================================================*/
	function sekcija_reallyDelete($Sekc_Id, $TransFree){
		$podsekcije = sekcija_getAllPodsekcija($Sekc_Id, true);
		if (count($podsekcije) > 0){
			for ($j=0;$j<count($podsekcije);$j++){
				$podsekcija = $podsekcije[$j];
				sekcija_reallyDelete($podsekcija["Sekc_Id"], true);
				$stranice = sekcija_getAllStranica($Sekc_Id, true);
				for ($k=0;$k<count($stranice);$k++){
					$stranica = $stranice[$k];
					stranica_reallyDelete($stranica["Stra_Id"], true);
				}
			}
		} else{
			$stranice = sekcija_getAllStranica($Sekc_Id, true);
			for ($k=0;$k<count($stranice);$k++){
				$stranica = $stranice[$k];
				stranica_reallyDelete($stranica["Stra_Id"], true);
			}
		}
		con_update("delete from Sekcija where Sekc_Id=".$Sekc_Id);
	}
?>
