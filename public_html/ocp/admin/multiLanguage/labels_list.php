<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../include/language.php");
	require_once("../../admin/design/list.php");
?>

<?php session_checkAdministrator(); ?>

<HTML>
<HEAD>
	<TITLE> Ocp </TITLE>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link href="/ocp/css/opsti.css" rel="stylesheet" type="text/css">
	<link href="/ocp/css/dugmici.css" rel="stylesheet" type="text/css">
	<link href="/ocp/css/opcije.css" rel="stylesheet" type="text/css">
</HEAD>
<body class="ocp_body">
<?php	
	$sortName = utils_requestStr(getPVar("sortName"));
	$direction = utils_requestStr(getPVar("direction"));
	$ocpLetter = utils_requestStr(getPVar("ocpLetter"));

	if (!utils_valid($sortName)){
		$sortName = "Labela";
		$direction = "asc";
		$ocpLetter = "0-9";
	} else if (!utils_valid($direction))
		$direction = "asc";

	$niz = lang_getLabeleByLetter($sortName, $direction, $ocpLetter);

	list_header(count($niz), NULL, NULL);
	list_tableHeader(array("Label"), $sortName, $direction, array("Labela"), NULL);
	for ($i=0; $i<count($niz); $i++){
		list_tableRow($i, array("Labela"), $niz[$i], true, true, false);
	}
	list_tableFooter();


?><FORM ACTION="labels_list.php?<?php echo utils_randomQS();?>" METHOD="POST" NAME="reconstructForm" ID="reconstructForm">
	<INPUT TYPE="HIDDEN" NAME="sortName" VALUE="<?php echo $sortName;?>">
	<INPUT TYPE="HIDDEN" NAME="direction" VALUE="<?php echo $direction;?>">
	<INPUT TYPE="HIDDEN" NAME="ocpLetter" VALUE="<?php echo $ocpLetter;?>">
</FORM>
<SCRIPT>
	var pressed = false;
	window.onload = function(){
		parent.menuFrame.populateLetterNavigationSubmenu('<?php echo $ocpLetter;?>');
		parent.detailFrame.location.href = "/ocp/html/blank.html";
	}
	function reconstruct(){ 
		document.reconstructForm.submit();
	}
	function sort(sortName, direction){
		document.reconstructForm.sortName.value = sortName;
		document.reconstructForm.direction.value = direction;
		document.reconstructForm.submit();
	}
	function newOffset(ocpLetter){
		document.reconstructForm.ocpLetter.value = ocpLetter;
		document.reconstructForm.submit();
	}
	function goForm(labId, action){
		window.open("/ocp/admin/multiLanguage/labels_edit.php?<?php echo utils_randomQS();?>&labId="+labId+"&action="+action, "detailFrame");
	}
	function goDelete(labId){
		if (confirm("<?php echo ocpLabels('Are you sure you want to delete object');?>?"))
			window.open("/ocp/admin/multiLanguage/labels_edit.php?<?php echo utils_randomQS();?>&labId="+labId+"&action=delete", "detailFrame");
	}
</SCRIPT></BODY>
</HTML>