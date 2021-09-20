<?php
require_once("../../include/connect.php");
require_once("../../include/session.php");
require_once("../../include/users.php");
require_once("../../include/xml_tools.php");
require_once("../../siteManager/lib/root.php");
require_once("../../siteManager/lib/verzija.php");
require_once("../../siteManager/lib/meni.php");
require_once("../../siteManager/lib/sekcija.php");
require_once("../../siteManager/lib/stranica.php");
?>

<?php session_checkAdministrator(); ?>

<?php
	//Response.Charset = "UTF-8";

	$xmlDom = xml_createObject();
	
	//xml_createProcessingInstruction($xmlDom, "encoding='UTF-8'");
	//xml_appendChild($xmlDom, $pi);

	$groupId = utils_requestInt(getGVar("treeFilter"));
	$group = users_getUserGroup($groupId);
	$superUser = ($group["Super"] == "1") ? 1 : 0;

	$verz_prava = verzija_getRights($groupId);
	$sekc_prava = sekcija_getRights($groupId);
	$stra_prava = stranica_getRights($groupId);
	DrawTree(null, "R", "", 0, 0, null);

	echo(xml_xml($xmlDom));

/* DrawTree funkcija */
	function DrawTree($Id, $T, $MaxLevel, $CurrLevel, $Node){
		global $xmlDom, $verz_prava, $sekc_prava, $stra_prava, $superUser;

		switch($T){
			case "R":	$records = root_getAll();
						for ($i=0; $i<count($records); $i++){
							$record = $records[$i];
							$rootNode = xml_createElement($xmlDom, "root");
							xml_setAttribute($rootNode, "Root_Id", $record["Root_Id"]);
							xml_setAttribute($rootNode, "Root_Naziv", $record["Root_Naziv"]);
							xml_setAttribute($rootNode, "Root_MaxDubina", $record["Root_MaxDubina"]);
							xml_setAttribute($rootNode, "lastmodify", date_getMiliseconds());
							xml_setAttribute($rootNode, "collapsed", "0");

							xml_setAttribute($rootNode, "labelCollapse", ocpLabels("collapse all"));
							xml_setAttribute($rootNode, "labelExpand", ocpLabels("expand all"));
							xml_setAttribute($rootNode, "labelMenu", ocpLabels("Additional menu"));
							xml_setAttribute($rootNode, "labelWait", ocpLabels("executing... please wait..."));


							DrawTree($record["Root_Id"], "V", $record["Root_MaxDubina"], $CurrLevel+1, $rootNode);

							xml_appendChild($xmlDom, $rootNode);
						}
						break;

			case "V":	$records = root_getAllVerzija($Id);

						for ($i=0; $i<count($records); $i++){
							$record = $records[$i];
							
							$pravo = "";
							if (array_key_exists($record["Verz_Id"], $verz_prava))
								$pravo = $verz_prava[$record["Verz_Id"]];
							else {
								if ($superUser) $pravo = "4";
								//else $pravo = $verz_prava["null"];
							}

							$verzNode = xml_createElement($xmlDom, "version");
							xml_setAttribute($verzNode, "Verz_Id", $record["Verz_Id"]);
							xml_setAttribute($verzNode, "Verz_Naziv", $record["Verz_Naziv"]);
							xml_setAttribute($verzNode, "Verz_Sekc_Id", $record["Verz_Sekc_Id"]);
							xml_setAttribute($verzNode, "right", $pravo);
							xml_setAttribute($verzNode, "collapsed", "0");

							DrawTree($record["Verz_Id"], "S", $MaxLevel, $CurrLevel+1, $verzNode);

							xml_appendChild($Node, $verzNode);
						}	
						break;

			case "S":	$records = array();
						if ($CurrLevel==2)
							$records = verzija_getAllSekcija($Id);
						else 
							$records = sekcija_getAllPodsekcija($Id);
					
						for ($i=0; $i<count($records); $i++){
							$record = $records[$i];

							$pravo = "";
							if (array_key_exists($record["Sekc_Id"], $sekc_prava))
								$pravo = $sekc_prava[$record["Sekc_Id"]];
							else {
								if ($superUser) $pravo = "4";
//								else $pravo = $sekc_prava["null"];
							}

							$sekcNode = null;
							if ($CurrLevel == 2)
								$sekcNode = xml_createElement($xmlDom, "section");
							else 
								$sekcNode = xml_createElement($xmlDom, "subsection");

							xml_setAttribute($sekcNode, "Sekc_Id", $record["Sekc_Id"]);
							xml_setAttribute($sekcNode, "Sekc_Naziv", $record["Sekc_Naziv"]);
							xml_setAttribute($sekcNode, "Sekc_ParentId", $record["Sekc_ParentId"]);
							xml_setAttribute($sekcNode, "right", $pravo);
							xml_setAttribute($sekcNode, "collapsed", "1");

							DrawTree($record["Sekc_Id"], "Str", 0, 0, $sekcNode);
							DrawTree($record["Sekc_Id"], "S", $MaxLevel, $CurrLevel+1, $sekcNode);

							xml_appendChild($Node, $sekcNode);
						}
						break;

			
			case "Str": $records = sekcija_getAllStranica($Id);

						for ($i=0; $i<count($records); $i++){
							$record = $records[$i];

							$pravo = "";
							if (array_key_exists($record["Stra_Id"], $stra_prava))
								$pravo = $stra_prava[$record["Stra_Id"]];
							else {
								if ($superUser) $pravo = "4";
//								else $pravo = $stra_prava["null"];
							}

							$prikaz = $record["Stra_Prikaz"]; $prikaz = ($prikaz == "1") ? "1" : "0";
							$publish = $record["Stra_PublishDate"]; 
							if (!utils_valid($publish)) $publish ="";
							$expiry = $record["Stra_ExpiryDate"];
							if (!utils_valid($expiry)) $expiry = "";
							
							$straNode = xml_createElement($xmlDom, "page");
							xml_setAttribute($straNode, "Stra_Id", $record["Stra_Id"]);
							xml_setAttribute($straNode, "Stra_Naziv", $record["Stra_Naziv"]);
							xml_setAttribute($straNode, "Stra_Prikaz", $prikaz);
							xml_setAttribute($straNode, "Stra_Home", "0");
							xml_setAttribute($straNode, "Stra_PublishDate", $publish);
							xml_setAttribute($straNode, "Stra_ExpiryDate", $expiry);
							xml_setAttribute($straNode, "Valid", $record["Valid"]);
							xml_setAttribute($straNode, "right", $pravo);

							xml_appendChild($Node, $straNode);					
						}
						break;

			default: break;
		}
	}

	function notVisibleNode($pravo){
		if (intval($pravo) == 0) return true;
		return false;
	}
?>