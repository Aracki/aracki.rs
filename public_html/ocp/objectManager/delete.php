<?php
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/connect.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/session.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/polja.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/objekti.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/tipoviobjekata.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/selectradio.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/xml.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/xml_tools.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/config/triggers.php");
?>
<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/ocp/css/opsti.css">
</HEAD>
<BODY class="ocp_body"><?php
	$objId = utils_requestInt(getGVar("objId"));
	$potvrda = utils_requestStr(getGVar("confirmed"));
	$typeId = utils_requestStr(getGVar("typeId"));
	$type = tipobj_getName($typeId);

	$data = obj_get($type, $objId);

	if ($potvrda == "1")
		obj_delete($type, $data["Id"]);
?><script>
	var m = "<?php echo $potvrda;?>";
	if (m != "1"){
		if (confirm("<?php echo ocpLabels('Are you sure you want to delete object');?>?")) 
			window.open("/ocp/objectManager/delete.php?<?php echo utils_randomQS();?>&typeId=<?php echo $typeId;?>&objId=<?php echo $objId;?>&confirmed=1", "detailFrame");
	} else parent.subMenuFrame.reconstruct();
</SCRIPT>
</BODY>
</HTML>