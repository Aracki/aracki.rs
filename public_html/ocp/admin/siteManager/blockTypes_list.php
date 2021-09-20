<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../include/design/tipbloka.php");
	require_once("../../siteManager/lib/tipoviblokova.php");
?>

<?php session_checkAdministrator(); ?>

<html>
<?php $type = utils_requestStr(getGVar("type")); ?>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link href="/ocp/css/opcije.css" rel="stylesheet" type="text/css">

	<script>
	function chooseType(typeId) {
		window.open("/ocp/admin/siteManager/blockTypes_edit.php?<?php echo utils_randomQS();?>&type=<?php echo $type;?>&typeId="+typeId+"&action=iu", "detailFrame");
	}

	function chooseShare(typeId) {
		window.open("/ocp/admin/siteManager/blockTypes_edit.php?<?php echo utils_randomQS();?>&type=<?php echo $type;?>&typeId="+typeId+"&action=iu", "detailFrame");
	}
	</script>
</head>
<body class="ocp_blokovi_body" onload="parent.detailFrame.location.href = '/ocp/html/blank.html';"><?php
	echo(tipblok_getChooseMenuHtml($type, NULL, NULL));

	$blokovi = NULL;
	switch ($type){
		case "static": $blokovi = tipblok_getAllStatic(); break;
		case "dinamic": $blokovi = tipblok_getAllDinamic(); break;
		default: $blokovi = tipBloka_getAllShared(); break;
	}

?><div id="ocp_blok_menu_2">
	<table class="ocp_opcije_table"><?php
	if (count($blokovi)){
		for ($i=0; $i<count($blokovi); $i++){
			if ($i%$brojKolona == 0){
				if ($i != 0) ?></tr><?php 
				?><tr><?php				
			}

			echo(tipblok_getTipHtml($blokovi[$i], $brojKolona));

			if (($i+1) == count($blokovi)){
				if (($i+1) % $brojKolona != 0)
					for ($j=0; $j < ($brojKolona - (($i+1) % $brojKolona)) ; $j++)
						echo(tipblok_getTipHtml(NULL, $brojKolona));
				?></tr><?php
			}
		}
	} else {
		?><?php require_once("../../include/design/message.php");?>
		<?php echo message_info(ocpLabels("There are no data in database"));?><?php
	}
?></table></div></body>
</html>
