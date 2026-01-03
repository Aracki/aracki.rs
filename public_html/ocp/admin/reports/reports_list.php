<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../include/izvestaji.php");
	require_once("../design/list.php");
	
	session_checkAdministrator() ?>
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
		$sortName = "Ime";
		$direction = "asc";
	} else if (!utils_valid($direction))
		$direction = "asc";

	$nizLista = izv_getAll($sortName, $direction);

	list_header(count($nizLista), null);
	list_tableHeader(array("Name", "Group"), $sortName, $direction, array("Ime", "Grupa"));
	for ($i=0; $i<count($nizLista); $i++){
		list_tableRow($i, array("Ime", "Grupa"), $nizLista[$i], 1, 1, 0);
	}
	list_tableFooter();
?><form action="reports_list.php?<?php echo utils_randomQS()?>" method="POST" name="reconstructForm" id="reconstructForm">
	<input type="hidden" name="sortName" value="<?php echo $sortName?>">
	<input type="hidden" name="direction" value="<?php echo $direction?>">
</form>
<script>
	var pressed = false;
	function reconstruct(){ 
		document.reconstructForm.submit();
	}
	function sort(sortName, direction){
		document.reconstructForm.sortName.value = sortName;
		document.reconstructForm.direction.value = direction;
		document.reconstructForm.submit();
	}
	function goForm(reportId, action){
		window.open("reports_edit.php?<?php echo utils_randomQS()?>&reportId="+reportId+"&action=iu", "detailFrame");
	}
	function goDelete(reportId, action){
		if (confirm("<?php echo ocpLabels('Are you sure you want to delete object')?>?"))
			window.open("reports_edit.php?<?php echo utils_randomQS()?>&reportId="+reportId+"&action=deleteReport", "detailFrame");
	}
</SCRIPT>
</BODY>
</HTML>