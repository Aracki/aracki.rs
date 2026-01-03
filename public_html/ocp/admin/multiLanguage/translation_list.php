<?php
require_once("../../include/connect.php");
require_once("../../include/session.php");
require_once("../../admin/design/list.php");
require_once("../../include/language.php");
require_once("../../include/users.php");
?>
<?php session_checkAdministrator(); ?>
<?php
	$language = utils_requestStr(getPVar("language"));
	if (!utils_valid($language)) { ?><script>location="/ocp/admin/multiLanguage/translation_query.php?<?php echo utils_randomQS();?>";</script><?php }
?>
<HTML>
<HEAD>
<TITLE> Ocp </TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="/ocp/css/opsti.css" rel="stylesheet" type="text/css">
<link href="/ocp/css/dugmici.css" rel="stylesheet" type="text/css">
<link href="/ocp/css/opcije.css" rel="stylesheet" type="text/css">
</HEAD>
<body class="ocp_body" onload="">
<?php	
	$sortName = utils_requestStr(getPVar("sortName"));
	$direction = utils_requestStr(getPVar("direction"));

	$firstLetters = utils_requestStr(getPVar("firstLetters"));
	$translation = utils_requestStr(getPVar("translation"));
	$automatic = utils_requestStr(getPVar("automatic"));

	if (!utils_valid($sortName)){
		$sortName = "Labela";
		$direction = "asc";
	} else if (!utils_valid($direction))
		$direction = "asc";

	$filterText = ": ";
	if (utils_valid($language)) {
		$langObject = lang_getJezik($language);
		$filterText .= ocpLabels("Language").": \"".$langObject["Jezik"]."\", ";
	}
	if (utils_valid($firstLetters))
		$filterText .= ocpLabels("Label's first letters").": \"".$firstLetters."\", ";
	if (utils_valid($translation)) $filterText .= ocpLabels("Not translated").": \"".$translation."\", ";
	$filterText = ($filterText != ": ") ? substr($filterText, 0, strlen($filterText) - 2) : NULL;
	
	$niz = lang_getPrevod($sortName, $direction, $language, $firstLetters, $translation);

	list_header(count($niz), $filterText);
	list_tableHeader(array("Label", "Translation"), $sortName, $direction, array("Labela", "Prevod"));
	for ($i=0; $i<count($niz); $i++){
		list_tableRowPrevod($i, array("Labela", "Prevod"), $niz[$i]);
	}
	list_tableFooter();


?><FORM ACTION="translation_list.php?<?php echo utils_randomQS();?>" METHOD="POST" NAME="reconstructForm" ID="reconstructForm">
	<INPUT TYPE="HIDDEN" NAME="language" VALUE="<?php echo $language;?>">
	<INPUT TYPE="HIDDEN" NAME="firstLetters" VALUE="<?php echo $firstLetters;?>">
	<INPUT TYPE="HIDDEN" NAME="translation" VALUE="<?php echo $translation;?>">
	<INPUT TYPE="HIDDEN" NAME="automatic" VALUE="<?php echo $automatic;?>">
	<INPUT TYPE="HIDDEN" NAME="sortName" VALUE="<?php echo $sortName;?>">
	<INPUT TYPE="HIDDEN" NAME="direction" VALUE="<?php echo $direction;?>">
</FORM>
<SCRIPT>
	var pressed = false;

	<?php 
	if (utils_valid($automatic) && ($automatic == "1") && (count($niz) > 0)){
	?>
		window.onload = function(){ 
			parent.detailFrame.location.href = '/ocp/html/blank.html';parent.menuFrame.eraseQuerySubmenu();
			goForm('<?php echo $niz[0]["IdLabele"];?>', '<?php echo $niz[0]["IdPrevoda"];?>', null); 
		} 
	<?php
	} else {
	?>
		window.onload = function(){ 
			parent.detailFrame.location.href = '/ocp/html/blank.html';parent.menuFrame.eraseQuerySubmenu();
		} 
	<?php
	}
	?>

	function reconstruct(){ 
		document.reconstructForm.submit();
	}
	function sort(sortName, direction){
		document.reconstructForm.sortName.value = sortName;
		document.reconstructForm.direction.value = direction;
		document.reconstructForm.submit();
	}
	function goForm(labId, transId, action){
		window.open("/ocp/admin/multiLanguage/translation_edit.php?<?php echo utils_randomQS();?>&langId=<?php echo $language;?>&labId="+labId+"&transId="+transId, "detailFrame");
	}
</SCRIPT></BODY>
</HTML>