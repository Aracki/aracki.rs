<?php	
	global $Id;

	$description = menu_getStraHtmlDescription();
	if (utils_valid($description)){

?>
	<meta name="description" content="<?php echo $description;?>"/>
<?php

	}

	
	$htmlKeywords = menu_getStraHtmlKeywords();
	if (!utils_valid($htmlKeywords)){
		$htmlKeywords = "";
	}
	?>
	<meta name="keywords" content="<?php echo $htmlKeywords;?>"/>
	<meta name="robots" content="all"/>
	<meta http-equiv="generator" content="OCP, Omnicom`s Content Plaform"/>
	<meta http-equiv="author" content="Omnicom Solutions doo, http://www.omnicom.rs"/>