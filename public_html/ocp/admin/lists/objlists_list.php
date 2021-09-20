<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../include/selectradio.php");
	require_once("../design/list.php");
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
		$sortName = "Tip";
		$direction = "asc";
	} else if (!utils_valid($direction))
		$direction = "asc";

	$nizLista = selrad_getListFields($sortName, $direction);

	list_header(count($nizLista), NULL, NULL);
	list_tableHeader(array("Type", "Field", "List type", "List"), $sortName, $direction, array("Tip", "Polje", "TipListe", "Lista"), NULL);
	for ($i=0; $i<count($nizLista); $i++){
		$lista = $nizLista[$i];
		$lista["Tip liste"] = $lista["TipListe"];
		list_tableRow($i, array("Tip", "Polje", "Tip liste", "Lista"), $lista, 1, 0, 0);
	}
	list_tableFooter();
?><form action="objlists_list.php?<?php echo utils_randomQS();?>" method="POST" name="reconstructForm" id="reconstructForm">
	<input type="hidden" name="sortName" value="<?php echo $sortName;?>">
	<input type="hidden" name="direction" value="<?php echo $direction;?>">
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
	function goForm(fieldId, action){
		window.open("/ocp/admin/lists/objlists_edit.php?<?php echo utils_randomQS();?>&fieldId="+fieldId+"&action=iu", "detailFrame");
	}
</SCRIPT>
</BODY>
</HTML>