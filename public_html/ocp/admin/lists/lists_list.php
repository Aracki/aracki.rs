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
	$listType = utils_requestStr(getPVar("listType"));

	if (!utils_valid($listType)){
		$listType = utils_requestStr(getGVar("listType"));
		$sortName = "Ime";
		$direction = "asc";
	} else if (!utils_valid($direction))
		$direction = "asc";

	$nizLista = selrad_getAllUsedListNames($listType, $sortName, $direction);
	list_header(count($nizLista), NULL);
	list_tableHeader(array("Name"), $sortName, $direction, array("Ime"));
	for ($i=0; $i<count($nizLista); $i++){
		$lista = array();
		$lista["Id"] = $nizLista[$i];
		$lista["Ime"] = $nizLista[$i];
		list_tableRow($i, array("Ime"), $lista, 1, 1, 0);
	}
	list_tableFooter();
?><form action="lists_list.php?<?php echo utils_randomQS();?>" method="POST" name="reconstructForm" id="reconstructForm">
	<input type="hidden" name="listType" value="<?php echo $listType;?>">
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
	function goForm(listId, action){
		var noItems = prompt("<?php echo ocpLabels("How much values will be added to list")?>?");
		window.open("/ocp/admin/lists/lists_edit.php?<?php echo utils_randomQS();?>&listType=<?php echo $listType;?>&listId="+listId+"&action=iu&noItems="+noItems, "detailFrame");
	}
	function goDelete(listId, action){
		if (confirm("<?php echo ocpLabels('Are you sure you want to delete object');?>?"))
			window.open("/ocp/admin/lists/lists_edit.php?<?php echo utils_randomQS();?>&listType=<?php echo $listType;?>&listId="+listId+"&action=deleteList", "detailFrame");
	}
</SCRIPT>
</BODY>
</HTML>