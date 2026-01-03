<?php
	require_once("../include/connect.php");
	require_once("../include/session.php");
	require_once("../include/xml_tools.php");
	require_once("../siteManager/lib/root.php");
?>

<?php session_checkAdministrator(); ?>

<?php
	$xmlDom = xml_createObject();
	
	//$pi = xml_createProcessingInstruction($xmlDom, "xml", " version='1.0'");
	//xml_appendChild($xmlDom, $pi);
	
	$root_naziv = root_getProperty(1, "Root_Naziv");
	if (strpos($root_naziv,"www.") != false)
		$root_naziv = substr($root_naziv,strpos($root_naziv,"www.")+3);

	// root
	$rootNode = NULL;
	$rootNode = xml_createElement($xmlDom, "root");
	xml_setAttribute($rootNode, "Root_Naziv", $root_naziv);
	xml_setAttribute($rootNode, "collapsed", "1");
	xml_setAttribute($rootNode, "right", "4/");
	xml_setAttribute($rootNode, "labelCollapse", ocpLabels("collapse all"));
	xml_setAttribute($rootNode, "labelExpand", ocpLabels("expand all"));
	xml_setAttribute($rootNode, "labelMenu", ocpLabels("Additional menu"));
	xml_setAttribute($rootNode, "labelWait", ocpLabels("executing... please wait..."));

	// Database tables
	$versionNode = this_createVersionNode("Database tables");
	xml_appendChild($versionNode, this_createSectionNode("OB", "Objects"));
	xml_appendChild($versionNode, this_createSectionNode("FO", "Forms"));
	xml_appendChild($rootNode, $versionNode);

	// Lists
	$versionNode = this_createVersionNode("Lists");
	xml_appendChild($versionNode, this_createSectionNode("SL", "Select lists"));
	xml_appendChild($versionNode, this_createSectionNode("RL", "Radio lists"));
	xml_appendChild($versionNode, this_createSectionNode("OL", "Objects lists"));
	xml_appendChild($rootNode, $versionNode);

	// User rights
	$versionNode = this_createVersionNode("User rights");
	xml_appendChild($versionNode, this_createSectionNode("UG", "User groups"));
	xml_appendChild($versionNode, this_createSectionNode("US", "Users"));
	xml_appendChild($rootNode, $versionNode);

	// Presentation
	$versionNode = this_createVersionNode("Presentation");
	xml_appendChild($versionNode, this_createSectionNode("MI", "Module installation"));
	xml_appendChild($versionNode, this_createSectionNode("BT", "Block types"));
	xml_appendChild($versionNode, this_createSectionNode("TE", "Templates"));
	xml_appendChild($rootNode, $versionNode);

	// Multilanguage support
	$versionNode = this_createVersionNode("Multilanguage support");
	xml_appendChild($versionNode, this_createSectionNode("LA", "Languages"));
	xml_appendChild($versionNode, this_createSectionNode("LB", "Labels"));
	xml_appendChild($versionNode, this_createSectionNode("TR", "Translation"));
	xml_appendChild($versionNode, this_createSectionNode("EI", "Export/Import languages"));
	xml_appendChild($rootNode, $versionNode);

	//Reports
	$versionNode = this_createVersionNode("Reports");
	xml_appendChild($versionNode, this_createSectionNode("RP", "Reports"));
	xml_appendChild($versionNode, this_createSectionNode("RG", "Report rights"));
	xml_appendChild($rootNode, $versionNode);

	// Logs
	$versionNode = this_createVersionNode("Logs");
	xml_appendChild($versionNode, this_createSectionNode("AL", "Activity log"));
	xml_appendChild($rootNode, $versionNode);


	// Recycle Bin
	$versionNode = this_createVersionNode("Recycle bin");
	xml_appendChild($versionNode, this_createSectionNode("DO", "Deleted objects"));
	xml_appendChild($versionNode, this_createSectionNode("DS", "Deleted site items"));
	xml_appendChild($rootNode, $versionNode);

	xml_appendChild($xmlDom, $rootNode);
	echo(xml_xml($xmlDom));

	// f-je potrebne ovom file-u
	function this_createVersionNode($nodeLabel){
		global $xmlDom;
		$versionNode = xml_createElement($xmlDom, "version");
		xml_setAttribute($versionNode, "Verz_Naziv", ocpLabels($nodeLabel));
		xml_setAttribute($versionNode, "Verz_Id", "");
		xml_setAttribute($versionNode, "collapsed", "1");
		xml_setAttribute($versionNode, "right", "4/");
		xml_setAttribute($versionNode, "Verz_Sekc_Id", "null/");

		return $versionNode;
	}

	function this_createSectionNode($nodeId, $nodeLabel){
		global $xmlDom;
		$sectionNode = xml_createElement($xmlDom, "section");
		xml_setAttribute($sectionNode, "Sekc_Id", $nodeId);
		xml_setAttribute($sectionNode, "Sekc_Naziv", ocpLabels($nodeLabel));
		xml_setAttribute($sectionNode, "Sekc_ParentId", "null");
		xml_setAttribute($sectionNode, "right", "4");
		xml_setAttribute($sectionNode, "collapsed", "1");

		return $sectionNode;
	}
?>