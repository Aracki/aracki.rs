<?php
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/date.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/utils.php");
	header("Location: ./login.php?".utils_randomQS());	
	die();
?>