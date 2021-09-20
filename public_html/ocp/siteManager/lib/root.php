<?php
/*Vraca verzije roota Root_Id
ako je allVersion null ili false samo
validne, u suprotnom sve
=====================================*/
	function root_getAllVerzija($Root_Id, $allVersion = NULL){
		$strSQL = "select Verz_Id, Verz_Naziv, Verz_Sekc_Id, Verz_Valid, Verz_ExtraParams";
		$strSQL .= " from Verzija";
		$strSQL .= " where Verz_Root_Id = ".$Root_Id;
		if ((is_null($allVersion)) || (!$allVersion)) $strSQL .= " and Verz_Valid=1";
		return con_getResults($strSQL);
	}

/*Vraca sve rootove iz baze
Za future use kada bude vise rootova
==================================*/
	function root_getAll(){
		return con_getResults("select * from Root where Root_Valid = 1");
	}

/*Vraca odredjeni root Root_Id
==================================*/
	function root_get($Root_Id){

		$root = con_getResult("select * from Root where Root_Valid=1 and Root_Id = ".$Root_Id);

		if (!utils_valid($root["Root_LokalnaAdresa"])) $root["Root_LokalnaAdresa"] = "";
		if (!utils_valid($root["Root_HtmlTitle"])) $root["Root_HtmlTitle"] = "";
		if (!utils_valid($root["Root_HtmlKeywords"])) $root["Root_HtmlKeywords"] = "";
		if (!utils_valid($root["Root_HtmlDescription"])) $root["Root_HtmlDescription"] = "";
		return $root;
	}

/*Vraca odredjeni property root Root_Id
==================================*/
	function root_getProperty($Root_Id, $PropertyName){
		return con_getValue("select ".$PropertyName." from Root where Root_Id=".$Root_Id);
	}

/*Radi update roota
==================================*/
	function root_edit($root){
		$rewrite_set = false;
		$rewrite_reset = false;

		$root = smobj_beforeUpdate("Root", $root);
		
		$strSQL = "update Root set Root_Naziv='".$root["Root_Naziv"]."'";
		if (utils_valid($root["Root_Verz_Id"]) && (intval($root["Root_Verz_Id"]) != 0)) $strSQL .= ", Root_Verz_Id = ".$root["Root_Verz_Id"];
		else $strSQL .= ", Root_Verz_Id = NULL";
		if (utils_valid($root["Root_LokalnaAdresa"])) $strSQL .= ", Root_LokalnaAdresa = '".$root["Root_LokalnaAdresa"]."'";
		else $strSQL .= ", Root_LokalnaAdresa = NULL";
		if (utils_valid($root["Root_HtmlTitle"])) $strSQL .= ", Root_HtmlTitle = '".$root["Root_HtmlTitle"]."'";
		else $strSQL .= ", Root_HtmlTitle = NULL";
		if (utils_valid($root["Root_HtmlKeywords"])) $strSQL .= ", Root_HtmlKeywords = '".$root["Root_HtmlKeywords"]."'";
		else $strSQL .= ", Root_HtmlKeywords = NULL";
		if (utils_valid($root["Root_HtmlDescription"])) $strSQL .= ", Root_HtmlDescription = '".$root["Root_HtmlDescription"]."'";
		else $strSQL .= ", Root_HtmlDescription = NULL";
		if ($root["Root_Rewrite"] == 1) {
			$rewrite_set = root_getProperty($root["Root_Id"], "Root_Rewrite") == 0 ? true : false;
			$strSQL .= ", Root_Rewrite = 1";
		} else {
			$rewrite_reset = root_getProperty($root["Root_Id"], "Root_Rewrite") == 1 ? true : false;
			$strSQL .= ", Root_Rewrite = 0";
		}
		$strSQL .= ", Root_MaxDubina=".$root["Root_MaxDubina"].", Root_LastModify = '".date_getMiliseconds()."' where Root_Id=".$root["Root_Id"] ." and Root_Valid=1";
		if (utils_valid($root["Root_LastModify"]))
			$strSQL .= " and Root_LastModify='".$root["Root_LastModify"]."'";

		//utils_dump($strSQL);

		$affected = con_update($strSQL);

		smobj_afterUpdate("Root", $root);

		if (intval($affected) == 0){	
	?> <script>alert("<?php echo ocpLabels("Another user has changed object. Your changes are not saved.")?>");</script><?php
		} else {
			log_append("Update", "Root", $root["Root_Id"]);
		}

		//prilikom prebacivanja sa starog na nove linkove 
		//treba izvrsiti proveru da li postoji stranica bez kesiranog urla;
		//takodje i prebaciti sve linkove po tekstovima
		if ($rewrite_set){
			$_SESSION["RewriteUrl"] = "1";

			require_once($_SERVER['DOCUMENT_ROOT'] . "/ocp/siteManager/lib/stranica.php");
			$results = con_getResults("select Stra_Id from Stranica");
			for ($i=0; $i<count($results); $i++)
				stranica_updatePath($results[$i]["Stra_Id"]);

			convert_blockLinks(OLD_REWRITE);
			convert_objectLinks(OLD_REWRITE);
			utils_updateSiteMenu();
		}
		
		//prilikom prebacivanja sa novog na stare linkove 
		//prebaciti sve linkove po teksovima
		if ($rewrite_reset){
			$_SESSION["RewriteUrl"] = "0";
			
			convert_blockLinks(REWRITE_OLD);
			convert_objectLinks(REWRITE_OLD);
			utils_updateSiteMenu();
		}
//die();
	}
?>