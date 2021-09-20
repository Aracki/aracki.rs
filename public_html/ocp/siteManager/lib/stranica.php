<?php
/*Vraca ime vlasnika stranice
==============================*/
	function stranica_getOwnerName($UserId) {
		return con_getValue("select User_Name from Users where User_Id = ".$UserId);
	}

/*Radi update patha stranice
==============================*/
	function stranica_updatePath($straId){
		if (!utils_isRewrite()) return;

		$old_path = con_getValue("select Stra_Link from Stranica where Stra_Id=".$straId);

		$path = "";

		$stranica = con_getResult("select Stra_Id, Stra_Naziv, Stra_Sekc_Id, Stra_Prikaz, Stra_LinkName from Stranica where Stra_Id=".$straId);

		$urlName = $stranica["Stra_LinkName"];

		if (utils_valid($urlName)){
			$sekc_urlName = con_getValue("select Sekc_LinkName from Sekcija where Sekc_Id=".$stranica["Stra_Sekc_Id"]);
			if ($urlName != $sekc_urlName){
				$path = "/" . convert_word($urlName) . "." . $straId . ".html";
			} else {
				$path = "." . $straId . ".html";
			}
		} else {
			if ($stranica["Stra_Prikaz"] == 1){
				$path = "/" . convert_word($stranica["Stra_Naziv"]) . "." . $straId . ".html";
			} else {
				$path = "." . $straId . ".html";
			}
		}
//		utils_dump($urlName);

		$parentId = $stranica["Stra_Sekc_Id"];

		while (utils_valid($parentId)){
			$result = con_getResult("select Sekc_Naziv, Sekc_ParentId, Sekc_LinkName from Sekcija where Sekc_Id=".$parentId);

			$urlName =  $result["Sekc_LinkName"];

			if (utils_valid($urlName)){
				$path = "/" . convert_word($urlName) . $path;
			} else {
				$path = "/" . convert_word($result["Sekc_Naziv"]) . $path;
			}
			$parentId = $result["Sekc_ParentId"];
		}
//		utils_dump($path);
		con_update("update Stranica set Stra_Link = '".$path."' where Stra_Id=".$stranica["Stra_Id"]);

		if (utils_valid($old_path) && $old_path != $path){
			convert_blockLinks(REWRITE_REWRITE_CHANGED_PATH, array("old"=>$old_path, "new"=>$path));
			convert_objectLinks(REWRITE_REWRITE_CHANGED_PATH, array("old"=>$old_path, "new"=>$path));
		}
//		die();
	}
	
/*Vraca naziv ikone stranice
============================*/
	function stranica_getIconName($stranica) {
		$objIkona = "stranica";
		if ($stranica["Stra_Prikaz"] == "0") $objIkona .= "_ne";	//prikaz
		$stranica = date_setPublishExpiry($stranica, "Stra_PublishDate", "Stra_ExpiryDate"); //datum
		if (isset($stranica["Valid"]) && ($stranica["Valid"] == "red")) $objIkona .= "_istek";
		else if (isset($stranica["Valid"]) && ($stranica["Valid"] == "green"))$objIkona .= "_tektreba";
		return $objIkona;
	}
	
/*Vraca sve blokove koji pripadaju stranici Stra_Id
===================================================*/
	function stranica_getAllBlok($Stra_Id, $allBlok = NULL, $editMode = NULL){
		$strSQL = "select Blok.*, TipBloka.*, StBl_Id, StBl_RedPrikaza";
		$strSQL .= " from Blok, Stranica_Blok, TipBloka ";
		$strSQL .= " where StBl_Stra_Id=".$Stra_Id." and StBl_Blok_Id=Blok_Id and TipB_Id=Blok_TipB_Id";
		if ((is_null($allBlok)) && (!$allBlok))
			$strSQL .= " and Blok_Valid=1 and StBl_Valid=1";
		if ((!is_null($editMode)) && ($editMode != "1")){
			$strToday = datetime_format4Database();
			$strSQL .= " and ((Blok_ExpiryDate >= '".$strToday."' and Blok_PublishDate is null) or (Blok_PublishDate <= '".$strToday."' and Blok_ExpiryDate is null) or (Blok_PublishDate is null and Blok_ExpiryDate is null) or (Blok_PublishDate <= '".$strToday."' and Blok_ExpiryDate >= '".$strToday."'))";
		}
		$strSQL .= " order by StBl_RedPrikaza";

		$blokovi = con_getResults($strSQL);
		for ($i=0;$i<count($blokovi);$i++){
			$result = $blokovi[$i];

			$expiry = $result["Blok_ExpiryDate"];
			if (utils_valid($expiry)) {
				$result["Blok_ExpiryDate"] = datetime_format4Database($expiry, true);
			} else {
				$result["Blok_ExpiryDate"] = "";
			}

			$publish =  $result["Blok_PublishDate"];
			if (utils_valid($publish)) {
				$result["Blok_PublishDate"] = datetime_format4Database($publish, true);
			} else { 
				$result["Blok_PublishDate"] = "";
			}

			$blokovi[$i] = $result;
		}

		return $blokovi;
	}
	
/*Vraca sve stranice u bazi u paru sa verzijom kojoj pripadaju
==============================================================*/
	function stranica_getAll(){
		$strSQL = "select Stra_Id, Stra_Naziv, Verz_Id, Verz_Naziv from Stranica, Sekcija, Verzija ";
		$strSQL .= " where Stra_Valid=1 and Sekc_Valid=1 and Verz_Valid=1 ";
		$strSQL .= " and Sekc_Id = Stra_Sekc_Id and Sekc_Verz_Id = Verz_Id order by Verz_Id";
		return con_getResults($strSQL);
	}

/*Vraca stranicu odredjenog Stra_Id
===================================*/
	function stranica_get($Stra_Id){
		$strSQL = "select * from Stranica, Template where Stra_Id=".$Stra_Id." and Stra_Temp_Id = Temp_Id";
		$stranica = con_getResult($strSQL);

		if (!utils_valid($stranica["Stra_PublishDate"])) {
			$stranica["Stra_PublishDate"] = "";
		} else {
			$stranica["Stra_PublishDate"] = datetime_format4Database($stranica["Stra_PublishDate"]);
		}
		if (!utils_valid($stranica["Stra_ExpiryDate"])) {
			$stranica["Stra_ExpiryDate"] = "";
		} else {
			$stranica["Stra_ExpiryDate"] = datetime_format4Database($stranica["Stra_ExpiryDate"]);
		}
		if (!utils_valid($stranica["Stra_HtmlTitle"])) $stranica["Stra_HtmlTitle"] = "";
		if (!utils_valid($stranica["Stra_HtmlKeywords"])) $stranica["Stra_HtmlKeywords"] = "";
		if (!utils_valid($stranica["Stra_HtmlDescription"])) $stranica["Stra_HtmlDescription"] = "";
	
		return $stranica;
	}

/*Vraca odredjeni property sekcije Sekc_Id
==========================================*/
	function stranica_getProperty($Stra_Id, $PropertyName){
		return con_getValue("select ".$PropertyName." from Stranica where Stra_Id=".$Stra_Id);
	}

/*Azurira postojecu stranicu
=============================*/
	function stranica_edit($stranica){
		if (!stranica_security(2, $stranica["Stra_Id"])){	?>
	<script>alert("<?php echo ocpLabels("You don\'t have sufficient privileges for this operation");?>");</script><?php
			return ;
		}

		$stranica = smobj_beforeUpdate("Stranica", $stranica);

		$strSQL = "update Stranica set ";
		$strSQL .= "Stra_Naziv='".$stranica["Stra_Naziv"]."', ";
		$strSQL .= "Stra_Temp_Id = ".$stranica["Stra_Temp_Id"].", ";
		$strSQL .= "Stra_HtmlKeywords = '".$stranica["Stra_HtmlKeywords"]."', ";
		if (utils_valid($stranica["Stra_HtmlTitle"])) $strSQL .= "Stra_HtmlTitle = '".$stranica["Stra_HtmlTitle"]."', ";
		else $strSQL .= "Stra_HtmlTitle = NULL, ";
		if (utils_valid($stranica["Stra_HtmlDescription"])) $strSQL .= "Stra_HtmlDescription = '".$stranica["Stra_HtmlDescription"]."', ";
		else $strSQL .= "Stra_HtmlDescription = NULL, ";
		if (utils_valid($stranica["Stra_LinkName"])) $strSQL .= "Stra_LinkName = '".$stranica["Stra_LinkName"]."', ";
		else $strSQL .= "Stra_LinkName = NULL, ";
		if (utils_valid($stranica["Stra_PublishDate"])) $strSQL .= "Stra_PublishDate = '".$stranica["Stra_PublishDate"]."', ";
		else $strSQL .= "Stra_PublishDate = null, ";
		if (utils_valid($stranica["Stra_ExpiryDate"])) $strSQL .= "Stra_ExpiryDate = '".$stranica["Stra_ExpiryDate"]."', ";
		else $strSQL .= "Stra_ExpiryDate = null, ";
		if (isset($stranica["Stra_ExtraParams"]) && utils_valid($stranica["Stra_ExtraParams"])) $strSQL .= "Stra_ExtraParams = '" . $stranica["Stra_ExtraParams"]."',";
		else $strSQL .= "Stra_ExtraParams = null,";
		$strSQL .= "Stra_Prikaz=". (($stranica["Stra_Prikaz"] == 1)? 1 : 0);
		$strSQL .= ", Stra_LastModify = '".date_getMiliseconds()."'";
		$strSQL .= " where Stra_Id=".$stranica["Stra_Id"]." and Stra_Valid=1";
		if (!is_null($stranica["Stra_LastModify"])){
			$strSQL .= "   and Stra_LastModify='".$stranica["Stra_LastModify"]."'";
		}

//utils_dump($strSQL);
		$affected = con_update($strSQL);
		if (intval($affected) == 0){	?>
<script>alert("<?php echo ocpLabels("Another user has changed object. Your changes are not saved.");?>");</script>
<?php	} else {
			log_append("Update", "Stranica", $stranica["Stra_Id"]);
			stranica_updatePath($stranica["Stra_Id"]);
			utils_updateSiteMenu();

			smobj_afterUpdate("Stranica", $stranica);
		}
	}

/*Brise odredjenu stranicu tj. postavlja je na Valid=0 kao i svim njenim blokovima
==================================================================================*/
	function stranica_delete($Stra_Id, $TransFree = NULL, $killLink, $replaceLink){
		if (!stranica_security(4, $Stra_Id)) {	?>
	<script>alert("<?php echo ocpLabels("You don\'t have sufficient privileges for this operation")?>");</script>
<?php		return ;
		}
		
		smobj_beforeDelete("Stranica", $Stra_Id);

		con_update("update Stranica_Stranica set StSt_Valid=0 where StSt_Stra_Id1=".$Stra_Id." or StSt_Stra_Id2=".$Stra_Id);
		$blokovi = stranica_getAllBlok($Stra_Id);
		for ($f=0;$f<count($blokovi);$f++){
			$blok = $blokovi[$f];
			con_update("update Stranica_Blok set StBl_Valid=0 where StBl_Stra_Id=".$Stra_Id." and StBl_Blok_Id=".$blok["Blok_Id"]);
			con_update("Update Blok set Blok_Valid=0 where Blok_Share = 0 and Blok_Id=".$blok["Blok_Id"]);
		}
		con_update("Update DodatniMeni set Meni_Valid=0 where Meni_Stra_Id=".$Stra_Id);
		con_update("Update Stranica set Stra_Valid=0 where Stra_Id=".$Stra_Id);
		con_update("Delete from SecurityStranica where StSec_Stra_Id=".$Stra_Id);
		log_append("Delete", "Stranica", $Stra_Id);
		utils_updateSiteMenu();

		smobj_afterDelete("Stranica", $Stra_Id);

		if ((utils_valid($killLink) && ($killLink == "1")) || utils_valid($replaceLink)){
			$stranice = stranica_getAllLinked($Stra_Id);

			$searchLink = rawurlencode(utils_getStraLink($Stra_Id) . "\"");

			for ($i=0;$i<count($stranice);$i++){
				$stranica = $stranice[$i];
				$strSQL = "select Blok_Id, Blok_XmlPodaci from Blok, Stranica_Blok";
				$strSQL .= " where Blok_Id=StBl_Blok_Id and StBl_Stra_Id=".$stranica["Stra_Id"];
				$strSQL .= " and Blok_Valid=1 and StBl_Valid=1 and Blok_XmlPodaci like '%".rawurlencode(utils_getStraLink($Stra_Id))."%'";	

				$blokovi = con_getResults($strSQL);
				for ($j=0;$j<count($blokovi);$j++){
					$blok = $blokovi[$j];

					if (utils_valid($killLink) && ($killLink == "1")){
						$xmlPodaci = $blok["Blok_XmlPodaci"];

						while (is_integer(strpos($xmlPodaci, $searchLink))){
							$nextIndex = strpos($xmlPodaci, $searchLink);
							$xmlPodaciLeft = substr($xmlPodaci, 0, $nextIndex);
							$xmlPodaciRight = substr($xmlPodaci, $nextIndex + 1);
//utils_dump("Next index ".$nextIndex);
//utils_dump("********************************");
//utils_dump("Left ".$xmlPodaciLeft);
//utils_dump("********************************");
//utils_dump("Right ".$xmlPodaciRight);
							
							$escapedEndATag = rawurlencode("</A>");
							$escapedEndaTag = rawurlencode("</a>");
							$escapedEndStartATag = rawurlencode(">");

							$startATag = utils_lastIndexOf($xmlPodaciLeft, rawurlencode("<A"));
							$startaTag = utils_lastIndexOf($xmlPodaciLeft, rawurlencode("<a"));
							$startATag = max($startATag, $startaTag);
//utils_dump("StartATag ".$startATag);

							$endStartATag = strpos($xmlPodaciRight, $escapedEndStartATag);
							$endATag = strpos($xmlPodaciRight, $escapedEndATag); 
							$endaTag = strpos($xmlPodaciRight, $escapedEndaTag);
							
							if (!is_integer($endATag)) $endATag = $endaTag;
							else if (is_integer($endaTag)) $endATag = min($endATag, $endaTag);
//utils_dump("endATag ".$endATag);
//utils_dump("endStartATag ".$endStartATag);

							if (is_integer($startATag) &&  is_integer($endStartATag) && is_integer($endATag) && ($endStartATag < $endATag)){

//utils_dump("I " . rawurldecode(substr($xmlPodaciLeft, 0, $startATag)));
//utils_dump("********************************");
//utils_dump("II " . rawurldecode(substr($xmlPodaciRight, $endStartATag + strlen($escapedEndStartATag), $endATag - ($endStartATag + strlen($escapedEndStartATag)))));
//utils_dump("********************************");
//utils_dump("III " . rawurldecode(substr($xmlPodaciRight, $endATag + strlen($escapedEndATag))));
								$tempEndATag = $endStartATag + strlen($escapedEndStartATag);
								$xmlPodaci = substr($xmlPodaciLeft, 0, $startATag) . 
									substr($xmlPodaciRight, $tempEndATag, $endATag - $tempEndATag) . 
									substr($xmlPodaciRight, $endATag + strlen($escapedEndATag));

							} else { //nije u html-editoru vec u tagovima, kao link slike npr.
								$xmlPodaci = $xmlPodaciLeft . substr($xmlPodaciRight, strlen($searchLink) - 1);
							}
						}
//echo(rawurldecode($xmlPodaci));
						$blok["Blok_XmlPodaci"] = $xmlPodaci;
					} else {
						$blok["Blok_XmlPodaci"] = str_replace($searchLink, rawurlencode($replaceLink . "\""), $blok["Blok_XmlPodaci"]);
					}
					//utils_dump($blok["Blok_XmlPodaci"]);
					con_update("UPDATE Blok SET Blok_XmlPodaci = '".$blok["Blok_XmlPodaci"]."' WHERE Blok_Id = ".$blok["Blok_Id"]);
				}
			}
		}
	}

/*Vraca sve stranice koje imaju link ka stranici Stra_Id
===================================================*/
	function stranica_getAllLinked($Stra_Id){
		$straLink = utils_getStraLink($Stra_Id);

		$strSQL = "select distinct Stra_Id, Sekc_Naziv, Stra_Naziv, Stra_PublishDate, Stra_ExpiryDate, Stra_Prikaz, Blok_XmlPodaci";
		$strSQL .= " from Blok, Stranica_Blok, Stranica, Sekcija";
		$strSQL .= " where Blok_Id=StBl_Blok_Id and StBl_Stra_Id=Stra_Id and Sekc_Id=Stra_Sekc_Id";
		$strSQL .= " and Blok_Valid=1 and StBl_Valid=1 and Stra_Valid=1 and Blok_XmlPodaci like '%". rawurlencode($straLink)."%'";
		$strSQL .= " order by Stra_Id";

		$results = array();
		$temp = con_getResults($strSQL);
		$lastStraId = 0;
		for ($i=0; $i<count($temp); $i++){
			$xml = rawurldecode($temp[$i]["Blok_XmlPodaci"]);
			if ($lastStraId != $temp[$i]["Stra_Id"] && is_integer(strpos($xml, $straLink))){
				$results[] = $temp[$i];
				$lastStraId = $temp[$i]["Stra_Id"];
			}
		}

		return $results;
	}

/*Vraca Id-ove svih validnih stranica u bazi
============================================*/
	function stranica_getIds($notvalid = 0){
		if ($notvalid) return con_getResultsArr("select Stra_Id from Stranica where Stra_Valid=0");
		return con_getResultsArr("select Stra_Id from Stranica where Stra_Valid=1");
	}

/*Kreira novu stranicu
======================*/
	function stranica_new($stranica, $TransFree = NULL){
		if (!sekcija_security(3, $stranica["Stra_Sekc_Id"])){	?>
	<script>alert("<?php echo ocpLabels("You don\'t have sufficient privileges for this operation");?>");</script>
<?php			return ;
		}
		$stranica = smobj_beforeInsert("Stranica", $stranica);

		$max = 0;
		$temp = con_getValue("select Max(Stra_RedPrikaza) from Stranica where Stra_Valid=1 and Stra_Sekc_Id=".$stranica["Stra_Sekc_Id"]);
		if (utils_valid($temp)){
			$max = intval($temp);
			++$max;
		}
		
		$strSQL = "insert into Stranica ";
		$strSQL .= " (Stra_Sekc_Id, Stra_Temp_Id,  Stra_Naziv, Stra_RedPrikaza, Stra_PublishDate, Stra_ExpiryDate, Stra_HtmlTitle, Stra_HtmlKeywords, Stra_HtmlDescription, Stra_LinkName, Stra_Prikaz, Stra_User_Id, Stra_LastModify ";
		if	(isset($stranica["Stra_ExtraParams"]) && utils_valid($stranica["Stra_ExtraParams"]))	$strSQL .= ",Stra_ExtraParams";
		$strSQL .= ") values (".$stranica["Stra_Sekc_Id"].",".$stranica["Stra_Temp_Id"].", '".$stranica["Stra_Naziv"]."', ".$max.",";
		if	(utils_valid($stranica["Stra_PublishDate"])) $strSQL .= " '".$stranica["Stra_PublishDate"]."', ";
		else $strSQL .= " NULL, ";
		if	(utils_valid($stranica["Stra_ExpiryDate"])) $strSQL .= " '".$stranica["Stra_ExpiryDate"]."', ";
		else $strSQL .= " NULL, ";
		if (utils_valid($stranica["Stra_HtmlTitle"])) $strSQL .= "'".$stranica["Stra_HtmlTitle"]."', ";
		else $strSQL .= "NULL, ";
		$strSQL .= "'".$stranica["Stra_HtmlKeywords"]."', ";
		if (utils_valid($stranica["Stra_HtmlDescription"])) $strSQL .= "'".$stranica["Stra_HtmlDescription"]."', ";
		else $strSQL .= "NULL, ";
		if (utils_valid($stranica["Stra_LinkName"])) $strSQL .= "'".$stranica["Stra_LinkName"]."', ";
		else $strSQL .= "NULL, ";
		if	(utils_valid($stranica["Stra_Prikaz"]) && ($stranica["Stra_Prikaz"] != "0")) $strSQL .= "1";
		else $strSQL .= "0";
		$strSQL .= ", ".$stranica["Stra_User_Id"].", '".date_getMiliseconds()."'";
		if	(isset($stranica["Stra_ExtraParams"]) && utils_valid($stranica["Stra_ExtraParams"])) $strSQL .= ",'".$stranica["Stra_ExtraParams"]."'";
		$strSQL .= ")";
		$stranica["Stra_Id"] = con_insert($strSQL);

		$userGroup = getSVar("ocpUserGroup");
		if ($userGroup != "null"){
			con_update("insert into SecurityStranica(StSec_UGrp_Id, StSec_Stra_Id, StSec_Rights) values(".$userGroup.", ".$stranica["Stra_Id"].", 4)");
		}

		setSVar('ocpPR', 4, $stranica["Stra_Id"]);
		log_append("Insert", "Stranica", $stranica["Stra_Id"]);
		stranica_updatePath($stranica["Stra_Id"]);
		utils_updateSiteMenu();

		smobj_afterInsert("Stranica", $stranica);

		return $stranica["Stra_Id"];
	}

/*Premestanje stranice pod novog parenta
========================================*/
	function stranica_changePosition($Stra_Id, $newParent, $TransFree = NULL){
		if (!sekcija_security(3, $newParent)){	?>
	<script>alert("<?php echo ocpLabels("You don\'t have sufficient privileges for this operation");?>");</script>
<?php			return ;
		}
		$stranica = stranica_get($Stra_Id);
		$sekcija =  sekcija_get($stranica["Stra_Sekc_Id"]);
		if ($sekcija["Sekc_Stra_Id"] == $Stra_Id)
			con_update("update Sekcija set Sekc_Stra_Id=null where Sekc_Id=".$sekcija["Sekc_Id"]);
		con_update("update Stranica set Stra_Sekc_Id=".$newParent." where Stra_Id=".$Stra_Id);
		stranica_changeLastOrder($Stra_Id, $newParent);
		utils_updateSiteMenu();	
	}

/*Premestanje stranice na poslednje mesto
=========================================*/
	function stranica_changeLastOrder($Stra_Id, $newParent){
		$max = 0;
		$temp = con_getValue("select max(Stra_RedPrikaza) from Stranica where Stra_Sekc_Id=".$newParent);
		if (utils_valid($temp)){
			$max = intval($temp);
			++$max;
		}
		con_update("Update Stranica set Stra_RedPrikaza=".$max." where Stra_Id=".$Stra_Id);
	}

/*Vraca Verz_Id stranice
========================*/
	function stranica_getVersion($Stra_Id){
		$strSQL = "select Sekc_Verz_Id from Stranica, Sekcija where Stra_Sekc_Id = Sekc_Id and Stra_Id=".$Stra_Id;
		return con_getValue($strSQL);
	}

	
/*Kopiranje stranice
====================*/
	function stranica_copy($Stra_Id, $Sekc_Id) {
		$stranica = stranica_get($Stra_Id);
		$stranica["Stra_Sekc_Id"] = $Sekc_Id;
		$stranica["Stra_Prikaz"] = "0";
		$stranica["Stra_PublishDate"] = "undefined";
		$stranica["Stra_ExpiryDate"] = "undefined";
		$stranica["Stra_Naziv"] = utils_escapeSingleQuote($stranica["Stra_Naziv"])." (kopija)";
		$stranica["Stra_HtmlTitle"] = utils_escapeSingleQuote($stranica["Stra_HtmlTitle"]);
		$stranica["Stra_HtmlKeywords"] = utils_escapeSingleQuote($stranica["Stra_HtmlKeywords"]);
		$stranica["Stra_HtmlDescription"] = utils_escapeSingleQuote($stranica["Stra_HtmlDescription"]);
		$stranica["Stra_LinkName"] = utils_escapeSingleQuote($stranica["Stra_LinkName"]);
		$stranica["Stra_ExtraParams"] = (isset($stranica["Stra_ExtraParams"]))? utils_escapeSingleQuote($stranica["Stra_ExtraParams"]) : "";
		$stranica["Stra_User_Id"] = getSVar("ocpUserId");

		$idNew = stranica_new($stranica, true);
		$blokovi = stranica_getAllBlok($Stra_Id);
		for ($i=0;$i<count($blokovi);$i++) {
			$blok = $blokovi[$i];
			$idNewBlok = $blok["Blok_Id"];
			if ($blok["Blok_Share"] == "0") { //nedeljeni blok mora da se kopira i veze za kopiju stranice
				$strSQL = "insert into Blok (Blok_TipB_Id, Blok_XmlPodaci, Blok_Share, Blok_Tekst, Blok_LastModify, Blok_PublishDate, Blok_ExpiryDate)";
				$strSQL .= " values (".$blok["Blok_TipB_Id"].", ";
				$strSQL .= " '".utils_escapeSingleQuote($blok["Blok_XmlPodaci"])."', 0, '".utils_escapeSingleQuote($blok["Blok_Tekst"])."', '".date_getMiliseconds()."'";
				if (utils_valid($blok["Blok_PublishDate"])) $strSQL .= ", '".$blok["Blok_PublishDate"]."'";
				else $strSQL .= ", null";
				if (utils_valid($blok["Blok_ExpiryDate"])) $strSQL .= ", '".$blok["Blok_ExpiryDate"]."'";
				else $strSQL .= ", null";
				$strSQL .= ")";
				con_update($strSQL);

				$idNewBlok = con_getValue("select max(Blok_Id) from Blok");
			}
			$strSQL = "  insert into Stranica_Blok ";
			$strSQL .= " (StBl_Stra_Id, StBl_Blok_Id, StBl_RedPrikaza) ";
			$strSQL .= " values (".$idNew.", ".$idNewBlok.", ".$blok["StBl_RedPrikaza"].") ";
			con_update($strSQL);
		}
		utils_updateSiteMenu();	

		return $idNew;
	}

/*Vraca putanju stranice sve do verzije
=======================================*/
	function stranica_getPath($Stra_Id){
		$path = "";
		$delimiter = "/";
		
		$strSQL = "SELECT Stra_Naziv, Sekc_Naziv, Sekc_Id, Sekc_ParentId, Sekc_Verz_Id FROM Sekcija, Stranica";
		$strSQL .= " WHERE Stra_Id=".$Stra_Id." and Sekc_Id = Stra_Sekc_Id";
		$result = con_getResult($strSQL);

		$path = $result["Sekc_Naziv"] . $delimiter;
		if ($result["Sekc_Naziv"] != $result["Stra_Naziv"]) 
			$path .= $result["Stra_Naziv"] . $delimiter;

		while (utils_valid($result["Sekc_ParentId"]) && ($result["Sekc_ParentId"] != 0)){//izvlacim sekciju prvog nivoa za sekciju
			$result = con_getResult("select Sekc_Id, Sekc_ParentId, Sekc_Naziv, Sekc_Verz_Id from Sekcija where Sekc_Id=".$result["Sekc_ParentId"]);
			$path = $result["Sekc_Naziv"] . $delimiter . $path;
		}
		$path = con_getValue("select Verz_Naziv from Verzija where Verz_Id=".$result["Sekc_Verz_Id"]).$delimiter.$path;
		return $path;
	}

/*Vraca sve analogije koje stranica ima
=======================================*/
	function stranica_getAllAnalogies($Stra_Id){
		$strSQL = "select StSt_Id, StSt_Stra_Id1, StSt_Stra_Id2 from Stranica_Stranica where StSt_Stra_Id1=".$Stra_Id." or StSt_Stra_Id2=".$Stra_Id." and StSt_Valid=1";
		return con_getResults($strSQL);
	}

/*Trazi odredjenu analogiju
===========================*/
	function stranica_getAnalogie($StSt_Id){
		$strSQL = "select StSt_Id, StSt_Stra_Id1, StSt_Stra_Id2 from Stranica_Stranica where StSt_Id=".$StSt_Id." and StSt_Valid=1";
		return con_getResult($strSQL);
	}

/*Kreira novu analogiju
=======================*/
	function stranica_newAnalogie($data){
		$Stra_Id = $data["Stra_Id"];
		if (!stranica_security(2, $Stra_Id)){	?>
	<script>alert("<?php echo ocpLabels("You don\'t have sufficient privileges for this operation");?>");</script>
<?php			return ;
		}
		$Stra_Ana = $data["StSt_Stra_Id"];
		$StSt_Id = $data["StSt_Id"];

		if (!utils_valid($StSt_Id) || ($StSt_Id == 0)){	//nije postojala analogija
			if (utils_valid($Stra_Ana) && ($Stra_Ana != 0)) {
				con_update("delete from Stranica_Stranica where ((StSt_Stra_Id1=".$Stra_Ana." and StSt_Stra_Id2=".$Stra_Id.") or (StSt_Stra_Id2=".$Stra_Ana." and StSt_Stra_Id1=".$Stra_Id." ))");
//utils_dump("insert into Stranica_Stranica (StSt_Stra_Id1, StSt_Stra_Id2) values (".$Stra_Id.", ".$Stra_Ana.")");
				con_update("insert into Stranica_Stranica (StSt_Stra_Id1, StSt_Stra_Id2) values (".$Stra_Id.", ".$Stra_Ana.")");
			}
		} else {	//postojala je analogija
			if (utils_valid($Stra_Ana) && ($Stra_Ana != 0)) {
				$analogie = stranica_getAnalogie($StSt_Id);
				
				con_update("delete from Stranica_Stranica where ((StSt_Stra_Id1=".$Stra_Ana." and StSt_Stra_Id2=".$Stra_Id.") or (StSt_Stra_Id2=".$Stra_Ana." and StSt_Stra_Id1=".$Stra_Id." )) and StSt_Id <> ".$StSt_Id);

				$strSQL = "update Stranica_Stranica set";
				if ($Stra_Id == $analogie["StSt_Stra_Id1"]) $strSQL .= " StSt_Stra_Id2=".$Stra_Ana;
				else $strSQL .= " StSt_Stra_Id1=".$Stra_Ana;
				$strSQL .= " where StSt_Valid=1 and StSt_Id = ".$StSt_Id;
				con_update($strSQL);
			} else {
//utils_dump("delete from Stranica_Stranica where StSt_Id=".$StSt_Id);
				con_update("delete from Stranica_Stranica where StSt_Id=".$StSt_Id);
			}
		}
		
		utils_updateSiteMenu();
	}

/*Vracanje parova id stranice - pravo za UserGroupId
====================================================*/
	function stranica_getRights($UserGroupId){
		$strSQL = "select s1.StSec_Stra_Id, s1.StSec_Rights from SecurityStranica s1 where s1.StSec_UGrp_Id=".$UserGroupId;
		return con_getResultsDict($strSQL);
	}

/*Vraca pravo nad stranicom Id
==================================================================*/
	function stranica_getRight($Id){
		$Right = getSvar('ocpPR',$Id);
		if (is_null($Right)) $Right = getSVar('ocpPR',"Default");
		else $Right = intval($Right);
		return $Right;
	}

/*Da li user ima bar level pravo nad stranicom Id
================================================*/
	function stranica_security($level, $Id){
		$Right = stranica_getRight($Id);
		if ($Right >= $level) return true;
		return false;
	}

/*Recycle bin
=============*/
	function stranica_recycleBin($stranice){
		for ($k=0;$k<count($stranice);$k++){
			$straAction = utils_requestStr(getPVar("stra".$stranice[$k]));
			switch($straAction){
				case "0": stranica_restoreMove($stranice[$k], utils_requestStr(getPVar("stra".$stranice[$k]."path"))); break;//just restore
				case "1": stranica_restore($stranice[$k], false); break;//restore with parents
				case "2": stranica_reallyDelete($stranice[$k], false); break;//delete
			}
			
		}
	}

/*Restore i move stranice
=========================*/
	function stranica_restoreMove($Stra_Id, $ParentId){
		stranica_changePosition($Stra_Id, $ParentId, true);
		stranica_restore($Stra_Id, true);
	}

/*Radim restore parenta, ne njihovog sadrzaja, samo definicija, ako je potrebno
===============================================================================*/
	function stranica_restore($Stra_Id, $TransFree = NULL){
		$sekc_id = con_getValue("select Sekc_Id from Sekcija, Stranica where Stra_Sekc_Id = Sekc_Id and Stra_Id=".$Stra_Id);

		sekcija_restoreOnly($sekc_id, true);
		stranica_restoreOnly($Stra_Id); //sada zaista ceo restore
		utils_updateSiteMenu();
	}

	function stranica_restoreOnly($Stra_Id){
		con_update("update Stranica_Stranica set StSt_Valid=1 where StSt_Stra_Id1=".$Stra_Id." or StSt_Stra_Id2=". $Stra_Id);
		$blokovi = stranica_getAllBlok($Stra_Id, true);
		for ($f=0;$f<count($blokovi);$f++){
			$blok = $blokovi[$f];
			con_update("update Stranica_Blok set StBl_Valid=1 where StBl_Stra_Id=".$Stra_Id." and StBl_Blok_Id=".$blok["Blok_Id"]);
			con_update("Update Blok set Blok_Valid=1 where Blok_Share = 0 and Blok_Id=".$blok["Blok_Id"]);
		}
		con_update("Update DodatniMeni set Meni_Valid=1 where Meni_Stra_Id=".$Stra_Id);
		con_update("Update Stranica set Stra_Valid=1 where Stra_Id=".$Stra_Id);
	}

/*Brisanje stranice sa svim podacima
====================================*/
	function stranica_reallyDelete($Stra_Id, $TransFree = NULL){
		con_update("Delete from Stranica_Stranica where StSt_Stra_Id1=".$Stra_Id." or StSt_Stra_Id2=".$Stra_Id);
		$blokovi = stranica_getAllBlok($Stra_Id, true);
		for ($f=0;$f<count($blokovi);$f++){
			$blok = $blokovi[$f];

			con_update("delete from Stranica_Blok where StBl_Stra_Id=".$Stra_Id." and StBl_Blok_Id=".$blok["Blok_Id"]);
			con_update("delete from Blok where Blok_Share = 0 and Blok_Id=".$blok["Blok_Id"]);
		}
		con_update("delete from DodatniMeni where Meni_Stra_Id=".$Stra_Id);
		con_update("delete from Stranica where Stra_Id=".$Stra_Id);
	}
?>