<?php

	require_once("../ocp/include/connect.php");
	require_once("../ocp/include/utils.php");
	require_once("../ocp/include/string.php");
	require_once("../ocp/include/xml_tools.php");
	require_once("../code/lib.php");
	require_once("../code/keyboard.php");
	
	$verzId = utils_requestInt($_GET["verzId"]);
	$verzId = utils_valid($verzId) ? $verzId : 1;
	$strXml = lib_getXmlMenu();

	$xmlDoc = xml_loadXML($strXml);
	
	echo (this_getFlashMenu($verzId, $xmlDoc));

	//vraca flash meni
	//&titles=O NAMA;USLUGE;PUBLIKACIJE;NOVOSTI;FAQ;LINKOVI;SERVISI&ids=1;2;3;4;5;6;7&kraj=OK
	function this_getFlashMenu($verzId, $xmlDoc){
		$verzNode = xml_getFirstElementByTagName( $xmlDoc, "verzija_".$verzId);
		$sekcNodes = xml_childNodes($verzNode);
		
		$titles = "&titles=";
		$ids = "&ids=";
		
		for ($i=0; $i < count($sekcNodes); $i++) {//obilazimo sve sekcije 0 dubine
			$sekcNode = $sekcNodes[$i];
			$id = xml_getAttribute($sekcNode, "id");
			$pocetna = xml_getAttribute($sekcNode, "pocetna");
			$naziv = xml_getAttribute($sekcNode, "naziv");
			
			if (!utils_valid($pocetna)) continue; //nema mu spasa
			
			$title = xml_getAttribute($sekcNode, "flash_naziv_upper");
			$title = keyboard_convert($title);
	
			$titles .=  $title.";";
			$ids .= $pocetna.";";
		}
		
		if ($titles != "&titles="){
			$titles = utils_substr($titles, 0, strlen($titles)-1);
			$ids = substr($ids, 0, strlen($ids)-1);
		}
		
		return $titles . $ids . "&kraj=OK";
	}
?>