<?php
/*Vraca sve sekcije koje sadrzi verzija Verz_Id
ako je allSection null ili false vraca samo validne
inace sve
===============================================*/
	function verzija_getAllSekcija($Verz_Id, $allSection=false){
		$strSQL = "select * from Sekcija where Sekc_Verz_Id = ".$Verz_Id." and Sekc_ParentId is null";
		if ((is_null($allSection)) || (!$allSection)) $strSQL .= " and Sekc_Valid=1";
		$strSQL .= " order by Sekc_RedPrikaza";
		return con_getResults($strSQL);
	}

/*Vraca sve sekcije koje sadrzi verzija Verz_Id
a koje ispunjavaju zadati filter
===============================================*/
	function verzija_getAllSekcijaFilter($Verz_Id, $Parametar){
		$strSQL = "select * from Sekcija where Sekc_Verz_Id = ".$Verz_Id." and Sekc_ParentId is null and Sekc_Valid=1 and Sekc_Naziv LIKE '%".$Parametar."%'";
		$strSQL .= " order by Sekc_RedPrikaza";
		return con_getResults($strSQL);
	}

/*Vraca id svih validnih verzija koji postoje u bazi
================================================*/
	function verzija_getIds($notvalid = 0){
		if ($notvalid)
			return con_getResultsArr("select Verz_Id from Verzija where Verz_Valid=0 order by Verz_Id asc");	
		return con_getResultsArr("select Verz_Id from Verzija where Verz_Valid=1 order by Verz_Id asc");
	}

/*Kreira novu verziju i istovremeno kreira novi 
dodatni meni koji ce imati ta verzija
================================================*/
	function verzija_new($verzija){
		$verzija = smobj_beforeInsert("Verzija", $verzija);

		$insert = "insert into Verzija (Verz_Root_Id, Verz_Naziv, Verz_HtmlTitle, Verz_HtmlKeywords, Verz_HtmlDescription, Verz_ExtraParams, Verz_LastModify) values (".$verzija["Verz_Root_Id"].", '".$verzija["Verz_Naziv"]."'";
		if	(utils_valid($verzija["Verz_HtmlTitle"])) $insert .= ",'".$verzija["Verz_HtmlTitle"]."'";
		else $insert .= ", NULL";
		if	(utils_valid($verzija["Verz_HtmlKeywords"])) $insert .= ",'".$verzija["Verz_HtmlKeywords"]."'";
		else $insert .= ", NULL";
		if	(utils_valid($verzija["Verz_HtmlDescription"])) $insert .= ",'".$verzija["Verz_HtmlDescription"]."'";
		else $insert .= ", NULL";
		if	(utils_valid($verzija["Verz_ExtraParams"])) $insert .= ",'".$verzija["Verz_ExtraParams"]."'";
		else $insert .= ", NULL";
		$insert .= ", '".date_getMiliseconds()."')";
		$verzija["Verz_Id"] = con_insert($insert);

		setSVar('ocpVR', 4, $verzija["Verz_Id"]);
		utils_updateSiteMenu();
		log_append("Insert", "Verzija", $verzija["Verz_Id"]);

		smobj_afterInsert("Verzija", $verzija);

		if (file_exists($_SERVER['DOCUMENT_ROOT']."/code/labele.xml"))
			verzija_saveLabels($verzija["Verz_Id"], array());
	}

/*Edituje postojecu verziju
============================*/
	function verzija_edit($verzija){
		if (!verzija_security(2, $verzija["Verz_Id"])){	?>
	<script>alert("<?php echo ocpLabels("You don\'t have sufficient privileges for this operation");?>");</script>
<?php			return ;
		}
		$verzija = smobj_beforeUpdate("Verzija", $verzija);

		$strSQL = "update Verzija set Verz_Naziv='".$verzija["Verz_Naziv"]."', Verz_LastModify='".date_getMiliseconds()."'"; 
		if (utils_valid($verzija["Verz_Sekc_Id"])) $strSQL .= ", Verz_Sekc_Id=".$verzija["Verz_Sekc_Id"];
		else $strSQL .= ", Verz_Sekc_Id=null";
		if	(utils_valid($verzija["Verz_ExtraParams"])) $strSQL .= ",Verz_ExtraParams = '". $verzija["Verz_ExtraParams"]."'";
		else $strSQL .= ",Verz_ExtraParams = null";
		if	(utils_valid($verzija["Verz_HtmlTitle"])) $strSQL .= ", Verz_HtmlTitle = '".$verzija["Verz_HtmlTitle"]. "'";
		else $strSQL .= ", Verz_HtmlTitle = null";
		if	(utils_valid($verzija["Verz_HtmlKeywords"])) $strSQL .= ", Verz_HtmlKeywords = '". $verzija["Verz_HtmlKeywords"]."'";
		else $strSQL .= ", Verz_HtmlKeywords = null";
		if	(utils_valid($verzija["Verz_HtmlDescription"])) $strSQL .= ", Verz_HtmlDescription = '". $verzija["Verz_HtmlDescription"]."'";
		else $strSQL .= ", Verz_HtmlDescription = null";
		$strSQL .= " where Verz_Id=".$verzija["Verz_Id"]." and Verz_Valid=1";
		if (utils_valid($verzija["Verz_LastModify"])) 
			$strSQL .= " and Verz_LastModify='".$verzija["Verz_LastModify"]."'";
		//echo($strSQL);
		$affected = con_update($strSQL);

		if (intval($affected) == 0){
?><script>alert("<?php echo ocpLabels("Another user has changed object. Your changes are not saved");?>");</script>
<?php	} else {
			log_append("Update", "Verzija", $verzija["Verz_Id"]);
			utils_updateSiteMenu();

			smobj_afterUpdate("Verzija", $verzija);
		}
	}

/*Brise verziju, tj. postavlja svim potomcima i njoj Valid na 0	
===============================================================*/
	function verzija_delete($Verz_Id){
		if (!verzija_security(4, $Verz_Id)){
?>	<script>alert("<?php echo ocpLabels("You don\'t have sufficient privileges for this operation")?>");</script><?php	
			return ;
		}

		smobj_beforeDelete("Verzija", $Verz_Id);

		$sekcije = verzija_getAllSekcija($Verz_Id);
		for ($i=0;$i<count($sekcije);$i++){
			$sekcija = $sekcije[$i];
			sekcija_delete($sekcija["Sekc_Id"], true);
								
		}
		con_update("Update Root set Root_Verz_Id=NULL where Root_Verz_Id=".$Verz_Id);
		con_update("Update Verzija Set Verz_Valid=0 where Verz_Id=".$Verz_Id);
		con_update("Delete from SecurityVerzija where VSec_Verz_Id=".$Verz_Id);
		con_update("Delete from DodatniMeni where Meni_Verz_To_Id=".$Verz_Id." or Meni_Verz_Id=".$Verz_Id);
		log_append("Delete", "Verzija", $Verz_Id);
		utils_updateSiteMenu();

		smobj_afterDelete("Verzija", $Verz_Id);
	}

/*Vraca verziju sa idom Verz_Id
================================*/
	function verzija_get($Verz_Id){
		$verzija = con_getResult("select * from Verzija where Verz_Valid=1 and Verz_Id=".$Verz_Id);
		if	(!utils_valid($verzija["Verz_HtmlTitle"])) $verzija["Verz_HtmlTitle"] = "";
		if	(!utils_valid($verzija["Verz_HtmlKeywords"])) $verzija["Verz_HtmlKeywords"] = "";
		if	(!utils_valid($verzija["Verz_HtmlDescription"])) $verzija["Verz_HtmlDescription"] = "";
		return $verzija;
	}

/*Vraca odredjeni property verzije Verz_Id
==========================================*/
	function verzija_getProperty($Verz_Id, $PropertyName){
		return con_getValue("select ".$PropertyName." from Verzija where Verz_Id=".$Verz_Id);
	}
	
/*Vraca sve verzije u bazi kao niz slogova id - ime
===================================================*/
	function verzija_getAllAvailable(){
		$strSQL = "select Verz_Id, Verz_Naziv from Verzija where Verz_Valid=1";
		return con_getResults($strSQL);
	}

/*Vraca sve stranice koje pripadaju verziji Verz_Id
===================================================*/
	function verzija_getAllStranica($Verz_Id){
		$strSQL = "select Stra_Id, Stra_Naziv, Sekc_Naziv from Stranica, Sekcija ";
		$strSQL .= "where Stra_Valid=1 and Sekc_Verz_Id=".$Verz_Id." and Sekc_Id=Stra_Sekc_Id";
		$strSQL .= " and Stra_Valid=1 order by Sekc_Naziv, Stra_Naziv asc";
		return con_getResults($strSQL);
	}

/*Vraca parove Id verzije - Pravo nad njom za usera iz UserGroupId
==================================================================*/
	function verzija_getRights($UserGroupId){
		return con_getResultsDict("select VSec_Verz_Id, VSec_Rights from SecurityVerzija where VSec_UGrp_Id=".$UserGroupId);
	}

/*Vraca pravo nad verzijom Id
==================================================================*/
	function verzija_getRight($Id){
		$Right = getSvar('ocpVR',$Id);
		if (is_null($Right)) $Right = getSVar('ocpVR',"Default");
		else $Right = intval($Right);
		return $Right;
	}

/*Samo proverava da li nad verzijom Id user ispunjava Pravo level
=================================================================*/
	function verzija_security($level, $Id){
		$Right = verzija_getRight($Id);
		if ($Right >= $level) return true;
		return false;
	}

/*Recycle bin
=============*/
	function verzija_recycleBin($verzije){
		for ($k=0;$k<count($verzije);$k++){
			$verzAction = utils_requestStr(getPVar("verz".$verzije[$k]));
			switch($verzAction){
				case "0": verzija_restoreOnly($verzije[$k]); break;//just restore
				case "1": verzija_restore($verzije[$k]); break;//restore with parents
				case "2": verzija_reallyDelete($verzije[$k]); break;//delete
			}
			
		}
	}


/*Radi samo restore verzije, bez restora njenog sadrzaja
========================================================*/
	function verzija_restoreOnly($Verz_Id){
		con_update("Update Verzija Set Verz_Valid=1 where Verz_Id=".$Verz_Id);
		utils_updateSiteMenu();
	}

/*Radi restore verzije sa restorom punog njenog sadrzaja
========================================================*/
	function verzija_restore($Verz_Id){
		$sekcije = verzija_getAllSekcija($Verz_Id, true);
		for ($i=0;$i<count($sekcije);$i++){
			$sekcija = $sekcije[$i];
			sekcija_restore($sekcija["Sekc_Id"], true);
		}
		con_update("Update Verzija Set Verz_Valid=1 where Verz_Id=".$Verz_Id);
		utils_updateSiteMenu();
	}

/*Zaista brise verziju sa svom njenom decom
===========================================*/
	function verzija_reallyDelete($Verz_Id){
		$sekcije = verzija_getAllSekcija($Verz_Id, true);
		for ($i=0;$i<count($sekcije);$i++){
			$sekcija = $sekcije[$i];
			sekcija_reallyDelete($sekcija["Sekc_Id"], true);
		}
		con_update("Delete from Verzija where Verz_Id=".$Verz_Id);
	}

/*Fja koja cita labele za zadatu verziju
===========================================*/
	function verzija_readLabels($Verz_Id){
		$result = array();
		$xmlDoc = xml_load($_SERVER['DOCUMENT_ROOT']."/code/labele.xml");

		$jeziciNodes = xml_getElementsByTagname($xmlDoc, "jezik");
		$jezikNode = null;
		if ($jeziciNodes->length > 0) {
			for ($i = 0; $i < $jeziciNodes->length; $i++){
				$nextNode = $jeziciNodes->item($i);
				if (xml_getAttribute($nextNode, "id") == $Verz_Id){
					$jezikNode = $nextNode;
					break;
				}
			}
		}

		if (isset($jezikNode) && !is_null($jezikNode)){
			$result["skracenica"] = xml_getAttribute($jezikNode, "skracenica");
			$jezikChilds = xml_childNodes($jezikNode);
			for ($i=0; $i<count($jezikChilds); $i++){
				$pageNode = $jezikChilds[$i];
				$result[xml_nodeName($pageNode)] = xml_getAttribute($pageNode, "text");
			}
		}

		return $result;
	}

/*Fja koja snima labele za zadatu verziju
===========================================*/
	function verzija_saveLabels($Verz_Id, $labels){
		$xmlDoc = xml_load($_SERVER['DOCUMENT_ROOT']."/code/labele.xml");
		$jeziciNodes = xml_getElementsByTagname($xmlDoc, "jezik");
		$jezikNode = null;
		if ($jeziciNodes->length > 0) {
			for ($i = 0; $i < $jeziciNodes->length; $i++){
				$nextNode = $jeziciNodes->item($i);
				if (xml_getAttribute($nextNode, "id") == $Verz_Id){
					$jezikNode = $nextNode;
					break;
				}
			}
		}

		$parent = xml_documentElement($xmlDoc);
		$skracenica = "";

		if (isset($jezikNode) && !is_null($jezikNode)){
			$skracenica = xml_getAttribute($jezikNode, "skracenica");
			xml_removeChild($parent, $jezikNode);
		} 
		
		$newNode = xml_createElement($xmlDoc, "jezik");
		xml_setAttribute($newNode, "id", $Verz_Id);
		xml_setAttribute($newNode, "skracenica", $skracenica);

		foreach ($labels as $key=>$value){
			$labelNode = xml_createElement($xmlDoc, $key);
			xml_setAttribute($labelNode, "text", $value);
			xml_appendChild($newNode, $labelNode);
		}

		xml_appendChild($parent, $newNode);
		
		$xmlTxt = str_replace("\n", "", xml_xml($xmlDoc));
		$xmlTxt = str_replace("\t", "", $xmlTxt);
		$xmlTxt = str_replace(">", ">\n", $xmlTxt);
		$xmlTxt = preg_replace("/(<(?!jezici|jezik|\?xml|\/))/", "\t\t<", $xmlTxt);
		$xmlTxt = preg_replace("/<jezik/", "\t<jezik", $xmlTxt);
		$xmlTxt = preg_replace("/<\/jezik/", "\t</jezik", $xmlTxt);
		$xmlTxt = preg_replace("/<\/jezici/", "</jezici", $xmlTxt);
		if (strlen($xmlTxt) < 40){
			echo ("labele.xml error happend");
			die();
		}
		$handle = fopen($_SERVER['DOCUMENT_ROOT'] . "/code/labele.xml", 'w');
		fwrite($handle, $xmlTxt);
		fclose($handle);

//		xml_save($xmlDoc, $_SERVER['DOCUMENT_ROOT'] . "/code/labele.xml");

		//snimanje bkp verzije labele xml u polje u bazi
		$xmlTxt = str_replace("\n", "", xml_xml($xmlDoc));
		$xmlTxt = utils_escapeSingleQuote($xmlTxt);
		con_update("update Ocp set LabeleXml='".$xmlTxt."' where Id=1");

	}
?>