<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../admin/design/list.php");
	require_once("../../include/language.php");
	require_once("../../include/users.php");
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
<body class="ocp_body" onload="parent.detailFrame.location.href = '/ocp/html/blank.html';">
<?php	
	$sortName = utils_requestStr(getPVar("sortName"));
	$direction = utils_requestStr(getPVar("direction"));

	if (!utils_valid($sortName)){
		$sortName = "Jezik";
		$direction = "asc";
	} else if (!utils_valid($direction))
		$direction = "asc";

	$niz = lang_getJezici($sortName, $direction);

	list_header(count($niz), NULL, NULL);
	list_tableHeader(array("Language"), $sortName, $direction, array("Jezik"), NULL);
	for ($i=0; $i<count($niz); $i++){
		list_tableRow($i, array("Jezik"), $niz[$i], true, true, false);
	}
	list_tableFooter();


?><FORM ACTION="languages_list.php?<?php echo utils_randomQS();?>" METHOD="POST" NAME="reconstructForm" ID="reconstructForm">
	<INPUT TYPE="HIDDEN" NAME="sortName" VALUE="<?php echo $sortName;?>">
	<INPUT TYPE="HIDDEN" NAME="direction" VALUE="<?php echo $direction;?>">
</FORM>
<SCRIPT>
	var pressed = false;
	function reconstruct(){ 
		document.reconstructForm.submit();
	}
	function sort(sortName, direction){
		document.reconstructForm.sortName.value = sortName;
		document.reconstructForm.direction.value = direction;
		document.reconstructForm.submit();
	}
	function goForm(langId, action){
window.open("/ocp/admin/multiLanguage/languages_edit.php?<?php echo utils_randomQS();?>&langId="+langId+"&action="+action, "detailFrame");
	}
	function goDelete(langId){
		if (confirm("<?php echo ocpLabels('Are you sure you want to delete object');?>?"))
			window.open("/ocp/admin/multiLanguage/languages_edit.php?<?php echo utils_randomQS();?>&langId="+langId+"&action=delete", "detailFrame");
	}
</SCRIPT></BODY>
</HTML>