<?php
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/connect.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/utils.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/code/menu.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/php/classes.php");

	$lang = "es";
	$letter = "a";
	if (isset($_GET["lang"])){
		$lang = utils_requestStr($_GET["lang"]);
	}
	if (isset($_GET['q'])) {
		$letter = utils_requestStr($_GET['q']);
		//$letter = mysql_real_escape_string($letter);

		$tmpRecnik = new Recnik($lang,$letter);
		$wordList = $tmpRecnik->getWordList();

		if (count($wordList) > 0){		
			for ($i = 0; $i < count($wordList); $i++)
			{
				$rec = $wordList[$i];
				if (0 == strncmp($rec->rec, $letter, strlen($letter))) { 
					echo $rec->rec."\n";
				}
			}		
		}
	}	
?>