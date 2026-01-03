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
		$sortName = "UserName";
		$direction = "asc";
	} else if (!utils_valid($direction))
		$direction = "asc";

	$nizUsera = users_getAllUsers($sortName, $direction);
	list_header(count($nizUsera), NULL, NULL);
	list_tableHeader(array("Username", "Group"), $sortName, $direction, array("UserName", "Grupa"), NULL);
	for ($i=0; $i<count($nizUsera); $i++) {
		list_tableRow($i, array("UserName", "Grupa"), $nizUsera[$i], true, true, false); }
	list_tableFooter();
?><form action="users_list.php?<?php echo utils_randomQS();?>" method="POST" name="reconstructForm" id="reconstructForm">
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
	function goForm(userId, action){
		window.open("/ocp/admin/users/users_edit.php?<?php echo utils_randomQS();?>&userId="+userId+"&action=iu", "detailFrame");
	}
	function goDelete(userId, action){
		if (confirm("<?php echo ocpLabels('Are you sure you want to delete object');?>?"))
			window.open("/ocp/admin/users/users_edit.php?<?php echo utils_randomQS();?>&userId="+userId+"&action=delete", "detailFrame");
	}
</SCRIPT>
</BODY>
</HTML>