<?php
	$Id = (isset($_GET["Id"]))? utils_requestInt(getGVar("Id")) : 0;
	$editor = (isset($_GET["editor"])) ? utils_requestStr(getGVar("editor")) : "";

	if (($editor=="1") || (!utils_valid($editor) && (!utils_valid($Id)  || ($Id == 0)))){
		if (!utils_valid($Id) || ($Id == 0))	$Id = utils_requestInt(getPVar("Stra_Id"));
		if (!utils_valid($Id) || ($Id == 0))	$Id = utils_requestInt(getPVar("Id"));
		else $editor = "1";
	}

	$menu = new Menu();
	$menu->init();
	
	$VerzLabele = menu_getVerzLabels(menu_getVerzId());
	$_SESSION["VerzLabele"] = $VerzLabele;

	// vraca parametar koji se upisuje u input polje pretrage
	function header_getQueryParameter() {
		echo(utils_requestStr(getGVar("SearchText"), 0, 1));
	}
?>