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
	$xmlDom = xml_createObject();
	DrawTree(null, "R", "", 0, 0, null);
	echo(xml_xml($xmlDom));

/* DrawTree funkcija */
	function DrawTree($Id, $T, $MaxLevel, $CurrLevel, $Node){
		global $xmlDom;

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

			case "V":	$records = root_getAllVerzija($Id, 1);

						for ($i=0; $i<count($records); $i++){
							$record = $records[$i];
							
							$verzNode = xml_createElement($xmlDom, "version");
							xml_setAttribute($verzNode, "Verz_Id",  $record["Verz_Id"]);
							xml_setAttribute($verzNode, "Verz_Naziv",  $record["Verz_Naziv"]);
							xml_setAttribute($verzNode, "Verz_Sekc_Id",  $record["Verz_Sekc_Id"]);
							xml_setAttribute($verzNode, "right", "4");
							xml_setAttribute($verzNode, "Valid",  ($record["Verz_Valid"] == "1" ? "true" : "false"));
							xml_setAttribute($verzNode, "collapsed", "0");

							DrawTree($record["Verz_Id"], "S", $MaxLevel, $CurrLevel+1, $verzNode);

							xml_appendChild($Node, $verzNode);
						}	
						break;

			case "S":	$records = array();
						if ($CurrLevel==2)
							$records = verzija_getAllSekcija($Id, 1);
						else 
							$records = sekcija_getAllPodsekcija($Id, 1);
					
						for ($i=0; $i<count($records); $i++){
							$record = $records[$i];

							$sekcNode = null;
							if ($CurrLevel == 2)
								$sekcNode = xml_createElement($xmlDom, "section");
							else 
								$sekcNode = xml_createElement($xmlDom, "subsection");

							xml_setAttribute($sekcNode, "Sekc_Id", $record["Sekc_Id"]);
							xml_setAttribute($sekcNode, "Sekc_Naziv", $record["Sekc_Naziv"]);
							xml_setAttribute($sekcNode, "Sekc_ParentId", $record["Sekc_ParentId"]);
							xml_setAttribute($sekcNode, "right", "4");
							xml_setAttribute($sekcNode, "Valid", ($record["Sekc_Valid"] == "1" ? "true" : "false"));
							xml_setAttribute($sekcNode, "collapsed", "1");

							DrawTree($record["Sekc_Id"], "Str", 0, 0, $sekcNode);
							DrawTree($record["Sekc_Id"], "S", $MaxLevel, $CurrLevel+1, $sekcNode);

							xml_appendChild($Node, $sekcNode);
						}
						break;

			
			case "Str": $records = sekcija_getReallyAllStranica($Id);

						for ($i=0; $i<count($records); $i++){
							$record = $records[$i];

							$publish = $record["Stra_PublishDate"]; 
							if (!utils_valid($publish)) $publish ="";
							$expiry = $record["Stra_ExpiryDate"];
							if (!utils_valid($expiry)) $expiry = "";
							
							$straNode = xml_createElement($xmlDom, "page");
							xml_setAttribute($straNode, "Stra_Id", $record["Stra_Id"]);
							xml_setAttribute($straNode, "Stra_Naziv", $record["Stra_Naziv"]);
							xml_setAttribute($straNode, "Stra_Prikaz", $record["Stra_Prikaz"]);
							xml_setAttribute($straNode, "Stra_Home", "0");
							xml_setAttribute($straNode, "Stra_PublishDate", $publish);
							xml_setAttribute($straNode, "Stra_ExpiryDate", $expiry);
							xml_setAttribute($straNode, "Valid", ($record["Stra_Valid"] == "1" ? "true" : "false"));
							xml_setAttribute($straNode, "right", "4");

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