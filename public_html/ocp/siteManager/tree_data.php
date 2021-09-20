<?php
	require_once("../include/connect.php");
	require_once("../include/session.php");
	require_once("../include/xml_tools.php");
	require_once("../siteManager/lib/root.php");
	require_once("../siteManager/lib/verzija.php");
	require_once("../siteManager/lib/meni.php");
	require_once("../siteManager/lib/sekcija.php");
	require_once("../siteManager/lib/stranica.php");
?>
<?php
	$userGroupId = getSVar("ocpUserGroup");

	$xmlDom = xml_createObject();
	
	// xmlDom.appendChild(pi);

	$filter = utils_requestStr(getGVar("treeFilter"));
	$int_link = false;
	if ($filter == "intLink"){
		$filter = "";
		$int_link = true;
	}

	DrawTree($xmlDom, NULL, "R", 0, 0, NULL);

	echo(xml_xml($xmlDom));

	/* DrawTree funkcija */
	function DrawTree($xmlDom, $Id, $T, $MaxLevel, $CurrLevel, $Node) {
		global $filter, $int_link;

		switch($T){
			case "R":	
				$records = root_getAll();
				for ($i=0; $i < count($records); $i++) {
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

					DrawTree($xmlDom, $record["Root_Id"], "V", $record["Root_MaxDubina"], $CurrLevel+1, $rootNode);

					xml_appendChild($xmlDom, $rootNode); 
				}
				break;

			case "V":	
				$records = root_getAllVerzija($Id);

				for ($i = 0; $i < count($records); $i++) {
					$record = $records[$i];
					$pravo = verzija_getRight($record["Verz_Id"]);
					if (notVisibleNode($pravo))
						continue;

					$verzNode = xml_createElement($xmlDom, "version");
					xml_setAttribute($verzNode, "Verz_Id", $record["Verz_Id"]);
					xml_setAttribute($verzNode, "Verz_Naziv", $record["Verz_Naziv"]);
					xml_setAttribute($verzNode, "Verz_Sekc_Id", $record["Verz_Sekc_Id"]);
					xml_setAttribute($verzNode, "right", $pravo);
					xml_setAttribute($verzNode, "collapsed", "0");

					if (verzija_security("4", $record["Verz_Id"])) {
						DrawTree($xmlDom, $record["Verz_Id"], "M", 0, 0, $verzNode);
					}
	
					DrawTree($xmlDom, $record["Verz_Id"], "S", $MaxLevel, $CurrLevel+1, $verzNode);

					xml_appendChild($Node, $verzNode);
				}	
				break;

			case "M":	
				$menuNode = xml_createElement($xmlDom, "menu");
				xml_setAttribute($menuNode, "collapsed", "1");

				// prvo verzije
				$records = meni_getMenuVersions($Id); 
				for ($i = 0; $i < count($records); $i++){
					$record = $records[$i]; 
					$mVerzNode = xml_createElement($xmlDom, "menuversion"); 
					xml_setAttribute($mVerzNode, "Verz_Id", $record["Meni_Verz_To_Id"]);
					xml_setAttribute($mVerzNode, "Verz_Naziv", $record["Meni_Naziv"]);

					xml_appendChild($menuNode, $mVerzNode);
				}
				// zatim stranice
				$records = meni_getMenuPages($Id);
				for ($i = 0; $i < count($records); $i++){
					$record = $records[$i];
					$mStraNode = xml_createElement($xmlDom, "menupage");
					xml_setAttribute($mStraNode, "Stra_Id", $record["Meni_Stra_Id"]);
					xml_setAttribute($mStraNode, "Stra_Naziv", $record["Meni_Naziv"]);

					xml_appendChild($menuNode, $mStraNode);
				}

				xml_appendChild($Node, $menuNode);

				break;

			case "S":	
				$records = array();
		
				if ($CurrLevel == 2)
					$records = verzija_getAllSekcija($Id); 		
				else 
					$records = sekcija_getAllPodsekcija($Id);
//utils_dump(count($records));
				$otvoren = false;
				for ($i = 0; $i < count($records); $i++) {
					$record = $records[$i];

					if ($CurrLevel == 2) { $otvoren = false; } // resetovanje u petlji

					$pravo = sekcija_getRight($record["Sekc_Id"]);
//utils_dump('pravo :'.$record["Sekc_Id"].':  :'.$pravo);
					if (notVisibleNode($pravo)) continue;

					$sekcNode = NULL;

					if ($CurrLevel == 2) {
						$sekcNode = xml_createElement($xmlDom, "section");
					} else {
						$sekcNode = xml_createElement($xmlDom, "subsection");
					}

					xml_setAttribute($sekcNode, "Sekc_Id", $record["Sekc_Id"]);
					xml_setAttribute($sekcNode, "Sekc_Naziv", $record["Sekc_Naziv"]);
					xml_setAttribute($sekcNode, "Sekc_ParentId", $record["Sekc_ParentId"]);
					xml_setAttribute($sekcNode, "right", $pravo);

					$otvoren1 = DrawTree($xmlDom, $record["Sekc_Id"], "Str", 0, 0, $sekcNode);
					$otvoren2 = DrawTree($xmlDom, $record["Sekc_Id"], "S", $MaxLevel, $CurrLevel+1, $sekcNode);
					$otvoren = $otvoren1 || $otvoren2 || $otvoren; // ???

					xml_setAttribute($sekcNode, "collapsed", "0");

					if ((utils_valid($filter) && ($otvoren1 || $otvoren2)) || !utils_valid($filter)) {
						xml_appendChild($Node, $sekcNode);
					}
					
					if (!$otvoren)
						xml_setAttribute($sekcNode, "collapsed", "1");
				}
				if ($CurrLevel > 2) { // samo podsekcije imaju potrebu da vracaju broj dece
					if (utils_valid($filter)) {
						return $otvoren;
					} else {
						return false;
					}
				}

				break;

			case "Str": 
				$records = array(); 
				if (utils_valid($filter)) {
					$records = sekcija_getAllStranicaFilter($Id, $filter);
				} else { 
					$records = sekcija_getAllStranica($Id);
				}

				for ($i = 0; $i < count($records); $i++){
					$record = $records[$i];
					$pravo = stranica_getRight($record["Stra_Id"]);

					if (notVisibleNode($pravo)) { continue; }

					$prikaz = $record["Stra_Prikaz"];
					$prikaz = ($prikaz == 1) ? 1 : 0;

					$publish = $record["Stra_PublishDate"];
					if (!utils_valid($publish)) $publish = "";
					else $publish = strtotime($publish);

					$expiry = $record["Stra_ExpiryDate"];
					if (!utils_valid($expiry)) $expiry = "";
					else $expiry = strtotime($expiry);

					$straNode = xml_createElement($xmlDom, "page");
					if ($int_link)
						xml_setAttribute($straNode, "Stra_Id", utils_getStraLink($record["Stra_Id"]));
					else 
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

				if (utils_valid($filter) && count($records) > 0) {
					return true;
				} else {
					return false;
				}

				break;

			default: break;
		}
		return $xmlDom;
	}

	function notVisibleNode($pravo){
		if (intval($pravo) == 0) return true;
		return false;
	}
?>