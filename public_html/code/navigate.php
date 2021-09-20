<?php
	require_once("../ocp/include/connect.php");
	require_once("../ocp/include/utils.php");
	require_once("../code/lib.php");
	require_once("../php/utils/request.php");
	include_once("../analyticstracking.php");

//	read template URL and redirect to it, sending PageId as querystring
//	template contains include files for reading menu and blocks, based on PageId

	$id = isset($_REQUEST["Id"]) ? utils_requestInt($_REQUEST["Id"]) : null;
	$id = intval($id);
	
	if (!utils_valid($id) || ($id == "0")){
		$id  = utils_requestInt(getPVar("Id"));

		if (!utils_valid($id) || ($id == "0")){
			$id = lib_getHomePage();
			if (!utils_valid($id) || ($id == "0")) {
				header("Location: /code/error.php");
				die();
			}
			header("Location: ".utils_getStraLink($id));
			die();
		}
	}
	$dbTemplateUrl = lib_getTemplateUrl($id);
	if ($dbTemplateUrl == ""){
		header("Location: /code/error.php");
		die();
	} 
	$JaSamSigurnoOnLine=true;

	require("..".$dbTemplateUrl);
?>
