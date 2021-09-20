<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../include/tipoviobjekata.php");
	require_once("../../include/selectradio.php");
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
		$sortName = "Ime";
		$direction = "asc";
	} else if (!utils_valid($direction))
		$direction = "asc";

	if (!selrad_isEverythingReady()){
?><SCRIPT>
		alert("<?php echo ocpLabels("You have to choose radio and select lists for all database objects");?>.");
	</SCRIPT><?php
	} else {
		$nizTipova = tipobj_getAllTypesWithFields($sortName, $direction);
	
		list_header(count($nizTipova), NULL, NULL);
		list_tableHeader(array("Name", "Group"), $sortName, $direction, array("Ime", "Grupa"), NULL);
		for ($i=0; $i<count($nizTipova); $i++){
			list_tableRow($i, array("Labela", "Grupa"), $nizTipova[$i], 1, 0, 1);				
		}
		list_tableFooter();
?><FORM ACTION="forms_list.php?<?php echo utils_randomQS();?>" METHOD="POST" NAME="reconstructForm" ID="reconstructForm">
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
	function goForm(typeId, action){
		window.open("/ocp/admin/database/forms_edit.php?<?php echo utils_randomQS();?>&typeId="+typeId, "detailFrame");
	}
</SCRIPT><?php
	}
?></BODY>
</HTML>