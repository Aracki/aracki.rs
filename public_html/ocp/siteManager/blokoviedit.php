<?php 
	require_once("../include/connect.php");
	require_once("../include/session.php");
	require_once("../siteManager/lib/stranica.php");
?><html>
<head>
	<title>OCP</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="/ocp/css/opsti.css">
</head>
<body><?php
		$straId = utils_requestInt(getGVar("Stra_Id"));
		$stranica = stranica_get($straId);
?><script language="javascript">
		window.open('<?php echo $stranica["Temp_Url"]; ?>?<?php echo utils_randomQS();?>&Id=<?php echo $straId; ?>&editor=1', 'detailFrame');
	</script>
</body>

</html>