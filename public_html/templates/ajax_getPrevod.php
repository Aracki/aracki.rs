<?php
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/connect.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/utils.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/code/menu.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/php/classes.php");	
	$lang = "";
	$letter = "";
	if (isset($_POST["lang"])){
		$lang = utils_requestStr($_POST["lang"]);
	}
	if (isset($_POST['rec'])) {
		$letter = utils_requestStr($_POST['rec']);
		$tmpRecnik = new Recnik($lang,"");
		$prevod = $tmpRecnik->getPrevod($letter);
		echo $prevod."\n";
	}	
?>