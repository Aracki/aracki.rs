<?php
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/session.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/connect.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/polja.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/tipoviobjekata.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/xml_tools.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/siteManager/lib/root.php");

	$xmlDom = xml_createObject();
	$objNiz = tipobj_getAllTypes4User();

	// utils_dump($objNiz, 1);

	$siteNaziv = root_getProperty(1, "Root_Naziv");
	$rootNode = xml_createElement($xmlDom, "root");

	xml_setAttribute($rootNode, "Root_Id", "1");
	xml_setAttribute($rootNode, "Root_Naziv", $siteNaziv);
	xml_setAttribute($rootNode, "Root_MaxDubina", "4");
	xml_setAttribute($rootNode, "lastmodify", date_getMiliseconds());
	xml_setAttribute($rootNode, "collapsed", "0");

	xml_setAttribute($rootNode, "labelCollapse", ocpLabels("collapse all"));
	xml_setAttribute($rootNode, "labelExpand", ocpLabels("expand all"));
	xml_setAttribute($rootNode, "labelMenu", ocpLabels("Additional menu"));
	xml_setAttribute($rootNode, "labelWait", ocpLabels("executing... please wait..."));

	$verzNode = null;
	$lastGrupa = "";
	for ($i=0; $i < count($objNiz); $i++) {
		$record = $objNiz[$i];

		if ($lastGrupa != $record["Grupa"]){
			if ($lastGrupa != "") xml_appendChild($rootNode, $verzNode);
			$lastGrupa = $record["Grupa"];
			$verzNode = xml_createElement($xmlDom, "version");
			xml_setAttribute($verzNode, "Verz_Id", $i);
			xml_setAttribute($verzNode, "Verz_Naziv", ocpLabels($lastGrupa));
			xml_setAttribute($verzNode, "Verz_Sekc_Id", "1");
			xml_setAttribute($verzNode, "right", "4");
			xml_setAttribute($verzNode, "collapsed", "1");
		}

		$sekcNode = xml_createElement($xmlDom, "section");
		xml_setAttribute($sekcNode, "Sekc_Id", "ocpId=".$record["Id"]);
		xml_setAttribute($sekcNode, "Sekc_Naziv", ocpLabels($record["Labela"]));
		xml_setAttribute($sekcNode, "Sekc_ParentId", "null");
		xml_setAttribute($sekcNode, "right", "4");
		xml_setAttribute($sekcNode, "collapsed", "1");

		xml_appendChild($verzNode, $sekcNode);
	}

	if (!is_null($verzNode)) //poslednji ako je ostao neprikacen
		xml_appendChild($rootNode, $verzNode);

	xml_appendChild($xmlDom, $rootNode);

	echo(xml_xml($xmlDom));
?>