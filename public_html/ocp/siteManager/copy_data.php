<?php
	require_once("../include/connect.php");
	require_once("../include/session.php");
	require_once("../include/xml_tools.php");
	require_once("../siteManager/lib/root.php");
	require_once("../siteManager/lib/verzija.php");
	require_once("../siteManager/lib/sekcija.php");
	
	$xmlDom = xml_createObject();
	
	//xml_createProcessingInstruction($xmlDom, "encoding='UTF-8'");
	
	$smlistType = utils_requestStr(getGVar("treeFilter"));

	DrawTree(NULL, "R", "", 0, 0, NULL);

	echo (xml_xml($xmlDom));

	/* DrawTree funkcija */
	function DrawTree($Id, $T, $MaxLevel, $CurrLevel, $Node){
		global $smlistType, $xmlDom;
		switch ($T) {
		case "R":
			$records = root_getAll();
			for ($i = 0; $i < count($records); $i++){
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

		case "V":
			$records = root_getAllVerzija($Id);
			for ($i = 0; $i < count($records); $i++) {
				$record = $records[$i];
				$pravo = verzija_getRight($record["Verz_Id"]);

				if (notVisibleNode($pravo)) continue;

				$verzNode = xml_createElement($xmlDom, "version");
				xml_setAttribute($verzNode, "Verz_Id", $record["Verz_Id"]);
				xml_setAttribute($verzNode, "Verz_Naziv", $record["Verz_Naziv"]);
				xml_setAttribute($verzNode, "Verz_Sekc_Id", $record["Verz_Sekc_Id"]);
				xml_setAttribute($verzNode, "right", $pravo);
				xml_setAttribute($verzNode, "collapsed", "0");
				
				if ($smlistType != "verzija"){
					DrawTree($record["Verz_Id"], "S", $MaxLevel, $CurrLevel+1, $verzNode);
				}
				xml_appendChild($Node, $verzNode);
			}	

			break;

		case "S":	
			$records = array();

			if ($CurrLevel == 2) $records = verzija_getAllSekcija($Id);
			else $records = sekcija_getAllPodsekcija($Id);

			for ($i = 0; $i < count($records); $i++){
				$record = $records[$i];
				$pravo = sekcija_getRight($record["Sekc_Id"]);

				if (notVisibleNode($pravo)) continue;

				$sekcNode = NULL;

				if ($CurrLevel == 2) $sekcNode = xml_createElement($xmlDom, "section");
				else $sekcNode = xml_createElement($xmlDom, "subsection");

				xml_setAttribute($sekcNode, "Sekc_Id", $record["Sekc_Id"]);
				xml_setAttribute($sekcNode, "Sekc_Naziv", $record["Sekc_Naziv"]);
				xml_setAttribute($sekcNode, "Sekc_ParentId", $record["Sekc_ParentId"]);
				xml_setAttribute($sekcNode, "right", $pravo);
				xml_setAttribute($sekcNode, "collapsed", "1");
				
				if ($smlistType == "stranica"){
					DrawTree($record["Sekc_Id"], "S", $MaxLevel, $CurrLevel+1, $sekcNode);
				}
				xml_appendChild($Node, $sekcNode);
			}

			break;

		default:
			break;
		}
	}

	function notVisibleNode($pravo) {
		if (intval($pravo) == 0) return true; 
		return false;
	}
?>