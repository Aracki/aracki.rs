<?php  
	require_once("../include/connect.php");
	require_once("../include/session.php");
	require_once("../include/design/tipbloka.php");
	require_once("../siteManager/lib/tipoviblokova.php");
	require_once("../include/design/message.php");

	$straURL = utils_requestStr(getGVar("stranica"));
	$type = utils_requestStr(getGVar("type"));
	$straId = utils_requestInt(getGVar("Id"));
?>

<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<link href="/ocp/css/opcije.css" rel="stylesheet" type="text/css">
	<script language="javascript">
		function chooseType(TipB_Id) {
			parent.menuFrame.showSubmenuClose(false);

			window.open('<?php echo $straURL; ?>?<?php echo utils_randomQS();?>&Id=<?php echo $straId; ?>&editor=1&TipB_Id='+TipB_Id+'&Akcija=Novi#new', 'detailFrame');
		}

		function chooseShare(Blok_Id) {
			parent.menuFrame.showSubmenuClose(false);
			window.open('<?php echo $straURL; ?>?<?php echo utils_randomQS();?>&Id=<?php echo $straId; ?>&editor=1&Blok_Id='+Blok_Id+'&Akcija=Share', 'detailFrame');
		}
	</script>
</head>

<body class="ocp_blokovi_body">
<?php
	echo(tipblok_getChooseMenuHtml($type, $straURL, $straId));

	$blokovi = NULL;
	switch ($type) {
		case "static": $blokovi = tipblok_getAllStatic(); break;
		case "dinamic": $blokovi = tipblok_getAllDinamic(); break;
		default: $blokovi = tipBloka_getAllShared(); break;
	}
?>
	<div id="ocp_blok_menu_2">
		<table class="ocp_opcije_table">
<?php
			if (count($blokovi)) {
				for ($i = 0; $i < count($blokovi); $i++){
					if (fmod($i, $brojKolona) == 0){
						if ($i != 0) { echo "</tr>"; } else { echo "<tr>"; }
					}

					echo( tipblok_getTipHtml($blokovi[$i]));

					if (($i + 1) == count($blokovi)) {
						if (fmod(($i + 1), $brojKolona) != 0) {
							for ($j = 0; $j < ($brojKolona - (fmod(($i + 1), $brojKolona))) ; $j++) {
								echo( tipblok_getTipHtml(NULL));
							}
						}
?>
				</tr>
<?php
					}
				}
			} else {
				 echo message_info(ocpLabels("There is no data in database"));
			}
?>
		</table>
	</div>
</body>
</html>