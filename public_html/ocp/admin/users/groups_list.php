<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../include/users.php");
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
<body class="ocp_body" onload="parent.detailFrame.location.href = '/ocp/html/blank.html';">
<?php	
	$sortName = utils_requestStr(getPVar("sortName"));
	$direction = utils_requestStr(getPVar("direction"));

	if (!utils_valid($sortName)){
		$sortName = "Name";
		$direction = "asc";
	} else if (!utils_valid($direction))
		$direction = "asc";

	$nizGrupa = users_getAllUserGroups($sortName, $direction);
	list_header(count($nizGrupa), NULL, NULL);
	list_tableHeader(array("Name"), $sortName, $direction, NULL, NULL);
	for ($i=0; $i<count($nizGrupa); $i++) {
		list_tableRow($i, array("Name"), $nizGrupa[$i], true, true, false); }
	list_tableFooter();
?><form action="groups_list.php?<?php echo utils_randomQS();?>" method="POST" name="reconstructForm" id="reconstructForm">
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
	function goForm(groupId, action){
		window.open("/ocp/admin/users/groups_edit.php?<?php echo utils_randomQS();?>&groupId="+groupId+"&action=iu", "detailFrame");
	}
	function goDelete(groupId, action){
		if (confirm("<?php echo ocpLabels('Are you sure you want to delete object');?>?"))
			window.open("/ocp/admin/users/groups_edit.php?<?php echo utils_randomQS();?>&groupId="+groupId+"&action=delete", "detailFrame");
	}
</SCRIPT>
</BODY>
</HTML>