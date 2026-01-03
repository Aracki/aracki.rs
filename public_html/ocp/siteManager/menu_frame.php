<?php
	require_once("../include/session.php");
	require_once("../include/connect.php");
	require_once("../siteManager/lib/root.php");
	require_once("../siteManager/lib/verzija.php");
	require_once("../siteManager/lib/sekcija.php");
	require_once("../siteManager/lib/stranica.php");
	require_once("../siteManager/lib/tipoviblokova.php");
	require_once("../siteManager/lib/meni.php");
	require_once("../include/design/menu.php");
	require_once("../include/design/submenu.php");
?>

<?php
	$objType = utils_requestStr(getGVar("ocpType"));
	$objId = utils_requestInt(intval(getGVar("ocpId")));
	$objIme = "";
	$objValid = true;

	$menuArray = array();
	$submenuArray = array();
	$objIkona = "";
	// da li korisnik ima pravo na default operaciju
	$loadDefaultPage = false;
	$submenuDefaultTab = NULL;

	switch ($objType){
		case "Root":
			$root = root_get($objId);
			$objIme = $root["Root_Naziv"];
			$objIkona = "root";

			if ($root["Root_Valid"] != "1"){ $objValid = false; break; }

			if (getSVar("ocpUserGroup") == "null"){ // samo Admin moze da uredjuje root site-a
				$loadDefaultPage = true;
				$submenuDefaultTab = "uredi_root";
				$menuArray['setup'] = array(ocpLabels("Setup"), 'window.open("/ocp/siteManager/rootedit.php?'.utils_randomQS().'&Root_Id='.$objId.'&Akcija=Uredi", "detailFrame");');
				$submenuArray['uredi_root'] = array(ocpLabels("Edit site"), "window.open('/ocp/siteManager/rootedit.php?".utils_randomQS()."&Root_Id=".$objId."&Akcija=Uredi', 'detailFrame');");
				$submenuArray['dodaj_verziju'] = array(ocpLabels("Add version"), "window.open('/ocp/siteManager/rootedit.php?".utils_randomQS()."&Root_Id=".$objId."&Akcija=DodajVerziju', 'detailFrame');");
			}

			break;

		case "Verzija":
			$verzija = verzija_get($objId);
			$objIme = $verzija["Verz_Naziv"];
			if ($verzija["Verz_Valid"] != "1") { $objValid = false; break; }
			$objTypeR = utils_requestStr(getGVar("ocpTypeR"));
			$objPravo = getGVar("ocpPravo");
			
			if ($objTypeR == "Root") { // verzija pod rootom
				$objIkona = "verzija";
				if ($objPravo >= 1) {
					$loadDefaultPage = true;
				}

				if ($objPravo >= 2) {
					$submenuDefaultTab = "uredi_verziju";
					$menuArray['setup'] = array(ocpLabels("Setup"), 'window.open("/ocp/siteManager/verzijaedit.php?'.utils_randomQS().'&Verz_Id='.$objId.'&Akcija=Uredi", "detailFrame");');
					$submenuArray['uredi_verziju'] = array(ocpLabels("Edit version"),  "window.open('/ocp/siteManager/verzijaedit.php?".utils_randomQS()."&Verz_Id=".$objId."&Akcija=Uredi', 'detailFrame');");
				}

				if ($objPravo >= 3) {
					$submenuArray['dodaj_sekciju'] = array(ocpLabels("Add section"),  "window.open('/ocp/siteManager/verzijaedit.php?".utils_randomQS()."&Verz_Id=".$objId."&Akcija=DodajSekciju', 'detailFrame');");

					$submenuArray['kopiraj_verziju'] = array(ocpLabels("Copy version"), "window.open('/ocp/siteManager/copy_tree.php?".utils_randomQS()."&type=verzija&srcId=".$objId."', 'detailFrame');");
				}

				if ($objPravo == 4) {
					$submenuArray['obrisi_verziju'] = array(ocpLabels("Delete version"),  "window.open('/ocp/siteManager/verzijaedit.php?".utils_randomQS()."&Verz_Id=".$objId."&Akcija=Obrisi', 'detailFrame');");
				}
			} else {
				$objIdR = utils_requestInt(getGVar("ocpIdR"));
				$objIkona = "dodatni_meni_verzija";
				if (verzija_security(4, $objIdR)) // ako ima delete pravo nad verzijom
					$loadDefaultPage = true;
					$objIme = meni_getMenuVersion($objIdR, $objId);
					$objIme = $objIme["Meni_Naziv"];
					$menuArray['setup'] = array(ocpLabels("Setup"), 'window.open("/ocp/siteManager/menuedit.php?'.utils_randomQS().'&Verz_Id='.$objIdR.'&Verz_To_Id='.$objId.'&Akcija=UrediVerziju", "detailFrame");');
					$submenuArray['prikazi_original'] = array(ocpLabels("Find original"),  "parent.parent.leftFrame.openMenuInLowerFrame('Verzija', ".$objId.", 'Root', ".$verzija["Verz_Root_Id"].", ".verzija_getRight($verzija["Verz_Id"]).", null, null, null);");
					$submenuArray['obrisi_link_verzije'] = array(ocpLabels("Delete version shortcut"),  "window.open('/ocp/siteManager/menuedit.php?".utils_randomQS()."&Verz_Id=".$objIdR."&Verz_To_Id=".$objId."&Akcija=ObrisiVerziju', 'detailFrame');");
			}

			break;

		case "Sekcija":
			$sekcija = sekcija_get($objId);
			$objIme = $sekcija["Sekc_Naziv"];
			if ($sekcija["Sekc_Valid"] != "1"){ $objValid = false; break; }

			$objTypeR = utils_requestStr(getGVar("ocpTypeR"));
			$objIdR = getGVar("ocpIdR");
			$objPravo = getGVar("ocpPravo");
			$dubina = getGVar("ocpDubina");
			$maxDubina = getGVar("ocpMaxDubina");
			
			if ($objPravo >= 1) {
				$loadDefaultPage = true;
			}

			$objIkona = "sekcija";

			if ($objPravo >= 2) {
				$submenuDefaultTab = "uredi_sekciju";
				$menuArray['setup'] = array(ocpLabels("Setup"), 'window.open("/ocp/siteManager/sekcijaedit.php?'.utils_randomQS().'&Sekc_Id='.$objId.'&Akcija=Uredi", "detailFrame");');
				$submenuArray['uredi_sekciju'] = array(ocpLabels("Edit section"), "window.open('/ocp/siteManager/sekcijaedit.php?".utils_randomQS()."&Sekc_Id=".$objId."&Akcija=Uredi', 'detailFrame');");
			}

			if (($dubina < $maxDubina) && ($objPravo >=3)) {
				$submenuArray['dodaj_podsekciju'] = array(ocpLabels("Add subsection"), "window.open('/ocp/siteManager/sekcijaedit.php?".utils_randomQS()."&Sekc_Id=".$objId."&Akcija=DodajPodsekciju', 'detailFrame');");
			}

			if ($objPravo >=3) {
				$submenuArray['dodaj_stranicu'] = array(ocpLabels("Add page"), "window.open('/ocp/siteManager/sekcijaedit.php?".utils_randomQS()."&Sekc_Id=".$objId."&Akcija=DodajStranicu', 'detailFrame');");

				$submenuArray['kopiraj_sekciju'] = array(ocpLabels("Copy section"), "window.open('/ocp/siteManager/copy_tree.php?".utils_randomQS()."&type=sekcija&srcId=".$objId."', 'detailFrame');");
			}

			if ($objPravo == 4) {
				$submenuArray['obrisi_sekciju'] = array(ocpLabels("Delete section"), "window.open('/ocp/siteManager/sekcijaedit.php?".utils_randomQS()."&Sekc_Id=".$objId."&Akcija=Obrisi', 'detailFrame');");
			}

			break;

		case "Stranica": 	
			$stranica = stranica_get($objId);
			$objIme = $stranica["Stra_Naziv"];	
			$templateUrl = $stranica["Temp_Url"]."&Id=".$objId."&editor=1";
			if ($stranica["Stra_Valid"] != "1"){ $objValid = false; break; }

			$objTypeR = utils_requestStr(getGVar("ocpTypeR"));
			$objIdR = getGVar("ocpIdR");
			$objPravo = getGVar("ocpPravo");

			if ($objTypeR == "Sekcija"){ // stranica pod sekcijom
				$objIkona = stranica_getIconName($stranica);

				if ($objPravo >= 1) {
					$loadDefaultPage = true;
				}

				if ($objPravo >= 2) {
					$menuArray['edit'] = array(ocpLabels("Edit"), 'window.open("/ocp/siteManager/blokoviedit.php?'.utils_randomQS().'&Stra_Id='.$objId.'", "detailFrame");');
					$menuArray['setup'] = array(ocpLabels("Setup"), 'window.open("/ocp/siteManager/stranicaedit.php?'.utils_randomQS().'&Stra_Id='.$objId.'&Akcija=Uredi", "detailFrame");');
					$menuArray['preview'] = array("Preview", 'window.open("'.utils_getStraLink($objId).'", "_blank");');

					$submenuArray['novi_blok'] = array(ocpLabels("New block"), "openSubmenu('/ocp/siteManager/block_list.php?".utils_randomQS()."&type=static&stranica=".$templateUrl."');");
					$submenuArray['uredi_blokove'] = array(ocpLabels("Move blocks"), "openSubmenu('/ocp/siteManager/lib/move/frameset.php?".utils_randomQS()."&Stra_Id=".$objId."');");
					$submenuArray['kopiraj_stranica'] = array(ocpLabels("Copy page"), "openSubmenu('/ocp/siteManager/copy_tree.php?".utils_randomQS()."&type=stranica&srcId=".$objId."');");
				}
				
				if ($objPravo == 4) {
					$submenuArray['obrisi_stranicu'] = array(ocpLabels("Delete page"), "openSubmenu('/ocp/siteManager/stranicaedit.php?".utils_randomQS()."&Stra_Id=".$objId."&Akcija=Obrisi');");
				}
			} else {
				$objIkona = "dodatni_meni_stranica";
				if (verzija_security(4, $objIdR)) // ako ima delete pravo nad verzijom
					$loadDefaultPage = true;
					$objIme = meni_getMenuPage($objIdR, $objId);
					$objIme = $objIme["Meni_Naziv"];
					$menuArray['setup'] = array(ocpLabels("Setup"), 'window.open("/ocp/siteManager/menuedit.php?'.utils_randomQS().'&Verz_Id='.$objIdR.'&Stra_Id='.$objId.'&Akcija=UrediStranicu", "detailFrame");');
					$submenuArray['prikazi_original'] = array(ocpLabels("Find original"), "parent.parent.leftFrame.openMenuInLowerFrame('Stranica', ".$objId.", 'Sekcija', ".$stranica["Stra_Sekc_Id"].", ".stranica_getRight($stranica["Stra_Id"]).", null, null, null);");
					$submenuArray['obrisi_link_stranice'] = array(ocpLabels("Delete page shortcut"), "window.open('/ocp/siteManager/menuedit.php?".utils_randomQS()."&Verz_Id=".$objIdR."&Stra_Id=".$objId."&Akcija=ObrisiStranicu', 'detailFrame');");
			}

			break;
	}
?>

<html>
<head>
	<title>OCP</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link href="/ocp/css/dugmici.css" rel="stylesheet" type="text/css">
	<script src="/ocp/jscript/menu.js"></script>
	<script language="JavaScript" type="text/JavaScript">
	var IE = (navigator.userAgent.indexOf("Firefox") == -1) ? true : false;

<?php if ($submenuDefaultTab){?>
	var	submenuDefaultTab = '<?php echo $submenuDefaultTab?>';
<?php } else { ?>
	var	submenuDefaultTab = '';
<?php } ?>

	function loadSteps(){
		parent.document.getElementById("rightFrameset").setAttribute("rows", "30,52,0,*");
		defaultPage();
<?php
		$action = utils_requestStr(getGVar("action"));
		if ($action == "newPage"){
?>
		switchSubmenuTab('dodaj_stranicu');
		eval(submenuArray['dodaj_stranicu'][1]);
<?php
		} else if ($action == "newBlock"){
?>
		switchSubmenuTab('novi_blok');
		eval(submenuArray['novi_blok'][1]);
<?php
		}
?>
	}

	function switchTabs(current, previous){
		switchMenuTabs(current, previous, null, "SITE MANAGER");
	}

	function openSubmenu(url){
		parent.subMenuFrame.location.href=url;
	}

	function showSubmenuClose(show, from){
		if (show){
			document.getElementById("ocpTdCloseSubmenu").style.visibility = "visible";	
		} else {
			document.getElementById("ocpTdCloseSubmenu").style.visibility = "hidden";
			switchSubmenuTab('');
			parent.document.getElementById("rightFrameset").setAttribute("rows", "30,52,0,*");
		}
	}

	function switchSubmenuTab(idTaba){
		switchSubmenuTabSM(idTaba);
	}

	function changeObjName(objName, escaped){
		if (escaped) objName = unescape(objName);
		var strHtml = '<img src="/ocp/img/gornji_2/ikone/<?php echo $objIkona;?>_ikonica.gif" class="ocp_gornji_2_nasl_ikona" title="'+objName+'">&nbsp;'+objName;
		document.getElementById("objIme").innerHTML = strHtml;
	}
	
	function changeObjLink(newLink){
		menuArray['preview'] = new Array('Preview',  'window.open("'+newLink+'", "_blank");');
	}

<?php	menu_script($menuArray, $loadDefaultPage);	
	submenu_script($submenuArray);	?>

	function openHelpFrameset(topic){
		top.top.document.getElementById("helpFrame").src="http://www.ocp2.com/redirect.asp?<?php echo utils_randomQS()?>&topic="+topic;
		setTimeout('parent.titleFrame.openHelpFrameset();', 200);
	}
</script>
</head>
<body class="ocp_gornji_2_body" onload="loadSteps();">
<?php
	if (utils_valid($objType) && $objValid){	
?><table class="ocp_gornji_2_table">
  <tr>
    <td class="ocp_gornji_2_naslov"><span id="objIme"><img src="/ocp/img/gornji_2/ikone/<?php echo $objIkona;?>_ikonica.gif" class="ocp_gornji_2_nasl_ikona" title="<?php echo $objIme; ?>">&nbsp;<?php echo $objIme; ?></span>
		<span class="ocp_gornji_2_id">&nbsp;[id=<?php echo $objId; ?>]</span>
	</td>
	<td class="ocp_gornji_2_desni"><?php
		menu_html($menuArray);
	?></td>
  </tr>
  <tr>
	<td height="28" colspan="2" class="ocp_gornji_2_td_donji">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr class="ocp_gornji_2_table">
    <td class="ocp_gornji_2_td_donji"><?php 
		submenu_html($submenuArray);
	?></td>
	<td height="28" nowrap align="right" style="visibility: hidden; padding-right: 5px;" id="ocpTdCloseSubmenu"><a href="#" onclick="showSubmenuClose(false, null);return false;" class="ocp_grupa_zatvori"><?php echo ocpLabels("close"); ?><img src="/ocp/img/opsti/kontrole/strelica_nagore.gif" hspace="5" border="0"></a></td>
	<td width="70" height="28" align="right" nowrap><a href="#" onClick="openHelpFrameset('<?php echo $objType;?>'); return false;" class="ocp_grupa_zatvori" title="<?php echo ocpLabels("Help"); ?>"><?php echo ocpLabels("Help");?><img src="/ocp/img/gornji_2/dugmici/ikona_help.gif" style="vertical-align: bottom; border: 0;"></a></td>
  </tr>
</table></td>
  </tr>
</table>
<?php 
	}	else {	
		if (!$objValid){
?><script>alert("<?php echo ocpLabels("Object you want to edit doesn\'t exist anymore")?>.");</script><?php
		}
?><table width="100%" height="52" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="100%" height="24" class="naslov"></td>
  </tr>
  <tr>
    <td height="28"></td>
  </tr>
</table>
<?php	}	?>
</body>
</html>