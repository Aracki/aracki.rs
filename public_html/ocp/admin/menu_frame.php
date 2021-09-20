<?php
	require_once("../include/session.php");
	require_once("../include/connect.php");
	require_once("../include/design/menu.php");
	
	session_checkAdministrator();

	$ocpId = utils_requestStr(getGVar("ocpId"));
	$loadDefaultPage = false; // da li korisnik ima pravo na default operaciju
	$menuArray = array();
	$objIme = NULL;

	switch ($ocpId){
		case "OB":
			$objIme = ocpLabels("Objects");
			$menuArray['lista_objekata'] = array(ocpLabels("List"), 'openSubmenu("/ocp/admin/database/types_list.php?'.utils_randomQS().'&types=objects");');
			$menuArray['admin_novi'] = array(ocpLabels("New"), 'window.open("/ocp/admin/database/types_edit.php?'.utils_randomQS().'&types=objects&typeId=-1&action=iu", "detailFrame");');
			break;
		case "FO":
			$objIme = ocpLabels("Forms");
			$menuArray['lista_objekata'] = array(ocpLabels("List"), 'openSubmenu("/ocp/admin/database/forms_list.php?'.utils_randomQS().'");');
			break;
		case "SL": 
		case "RL":
			$objIme = ($ocpId == "SL") ? ocpLabels("Select lists") : ocpLabels("Radio lists");
			$objType = ($ocpId == "SL") ? "select" : "radio";
			$menuArray['lista_objekata'] = array(ocpLabels("List"), 'openSubmenu("/ocp/admin/lists/lists_list.php?'.utils_randomQS().'&listType='.$objType.'");');
			$menuArray['admin_novi'] = array(ocpLabels("New"), 'window.open("/ocp/admin/lists/lists_edit.php?'.utils_randomQS().'&listType='.$objType.'&listId=-1&action=iu&noItems=5", "detailFrame");');
			break;
		case "OL":
			$objIme = ocpLabels("Objects list");
			$menuArray['lista_objekata'] = array(ocpLabels("List"), 'openSubmenu("/ocp/admin/lists/objlists_list.php?'.utils_randomQS().'");');
			break;
		case "UG":
			$objIme = ocpLabels("User groups");
			$menuArray['lista_objekata'] = array(ocpLabels("List"), 'openSubmenu("/ocp/admin/users/groups_list.php?'.utils_randomQS().'");');
			$menuArray['admin_novi'] = array(ocpLabels("New"), 'window.open("/ocp/admin/users/groups_edit.php?'.utils_randomQS().'&groupId=-1&action=iu", "detailFrame");');
			break;
		case "US":
			$objIme = ocpLabels("Users");
			$menuArray['lista_objekata'] = array(ocpLabels("List"), 'openSubmenu("/ocp/admin/users/users_list.php?'.utils_randomQS().'");');
			$menuArray['admin_novi'] = array(ocpLabels("New"), 'window.open("/ocp/admin/users/users_edit.php?'.utils_randomQS().'&userId=-1&action=iu", "detailFrame");');
			break;
		case "MI":
			$objIme = ocpLabels("Module installation");
			$menuArray['admin_novi'] = array(ocpLabels("New"), 'window.open("/ocp/admin/siteManager/module_install.php?'.utils_randomQS().'", "detailFrame");');
			break;
		case "BT":
			$objIme = ocpLabels("Block types");
			$menuArray['lista_objekata'] = array(ocpLabels("List"), 'openSubmenu("/ocp/admin/siteManager/blockTypes_list.php?'.utils_randomQS().'&type=static");');
			$menuArray['admin_novi'] = array(ocpLabels("New"), 'window.open("/ocp/admin/siteManager/blockTypes_edit.php?'.utils_randomQS().'&typeId=-1&action=iu", "detailFrame");');
			break;
		case "TE":
			$objIme = ocpLabels("Templates");
			$menuArray['lista_objekata'] = array(ocpLabels("List"), 'openSubmenu("/ocp/admin/siteManager/templates_list.php?'.utils_randomQS().'");');
			$menuArray['admin_novi'] = array(ocpLabels("New"), 'window.open("/ocp/admin/siteManager/templates_edit.php?'.utils_randomQS().'&tempId=-1&action=iu", "detailFrame");');
			break;
		case "LA":
			$objIme = ocpLabels("Languages");
			$menuArray['lista_objekata'] = array(ocpLabels("List"), 'openSubmenu("/ocp/admin/multiLanguage/languages_list.php?'.utils_randomQS().'");');
			$menuArray['admin_novi'] = array(ocpLabels("New"), 'window.open("/ocp/admin/multiLanguage/languages_edit.php?'.utils_randomQS().'&langId=-1&action=iu", "detailFrame");');
			break;
		case "LB":
			$objIme = ocpLabels("Labels");
			$menuArray['lista_objekata'] = array(ocpLabels("List"), 'openSubmenu("/ocp/admin/multiLanguage/labels_list.php?'.utils_randomQS().'");');
			$menuArray['admin_novi'] = array(ocpLabels("New"), 'window.open("/ocp/admin/multiLanguage/labels_edit.php?'.utils_randomQS().'&labId=-1&action=iu", "detailFrame");');
			break;
		case "TR":
			$objIme = ocpLabels("Translation");
			$menuArray['nadji_objekat'] = array(ocpLabels("Query"), 'openSubmenu("/ocp/admin/multiLanguage/translation_query.php?'.utils_randomQS().'");');
			$menuArray['lista_objekata'] = array(ocpLabels("List"), 'openSubmenu("/ocp/admin/multiLanguage/translation_list.php?'.utils_randomQS().'");');
			break;
		case "EI":
			$objIme = ocpLabels("Export/Import languages");
			$menuArray['lista_objekata'] = array(ocpLabels("Export/Import"), 'openSubmenu("/ocp/admin/multiLanguage/languages_export.php?'.utils_randomQS().'");');
			break;
		case "AL":
			$objIme = ocpLabels("Activity log");
			$menuArray['nadji_objekat'] = array(ocpLabels("Query"), 'openSubmenu("/ocp/admin/log/activities_query.php?'.utils_randomQS().'&action=view");');
			$menuArray['lista_objekata'] = array(ocpLabels("List"), 'openSubmenu("/ocp/admin/log/activities_list.php?'.utils_randomQS().'");');
			$menuArray['brisanje_objekata'] = array(ocpLabels("Delete"), 'openSubmenu("/ocp/admin/log/activities_query.php?'.utils_randomQS().'&action=delete");');
			break;
		case "VL":
			$objIme = ocpLabels("Visits log");
			$menuArray['nadji_objekat'] = array(ocpLabels("Query"), 'openSubmenu("/ocp/admin/log/visits_query.php?'.utils_randomQS().'");');
			$menuArray['izvestaj'] = array(ocpLabels("Report"), 'openSubmenu("/ocp/admin/log/visits_report.php?'.utils_randomQS().'");');
			break;
		case "RP":
			$objIme = ocpLabels("Reports");
			$menuArray['lista_objekata'] = array(ocpLabels("List"), 'openSubmenu("/ocp/admin/reports/reports_list.php?'.utils_randomQS().'");');
			$menuArray['admin_novi'] = array(ocpLabels("New"), 'window.open("/ocp/admin/reports/reports_edit.php?'.utils_randomQS().'&reportId=-1&action=iu", "detailFrame");');
			break;
		case "RG":
			$objIme = ocpLabels("Report rights");
			$menuArray['lista_objekata'] = array(ocpLabels("List"), 'openSubmenu("/ocp/admin/reports/rights_list.php?'.utils_randomQS().'");');
			break;
		case "DO":
			$objIme = ocpLabels("Deleted objects");
			$menuArray['nadji_objekat'] = array(ocpLabels("Query"), 'openSubmenu("/ocp/admin/recycleBin/objects_query.php?'.utils_randomQS().'");');
			$menuArray['lista_objekata'] = array(ocpLabels("List"), 'openSubmenu("/ocp/admin/recycleBin/objects_list.php?'.utils_randomQS().'");');
			break;
		case "DS":
			$objIme = ocpLabels("Deleted site items");
			$menuArray['lista_objekata'] = array("Site", 'openSubmenu("/ocp/admin/recycleBin/site_list.php?'.utils_randomQS().'");');
			break;
		default: break;
	
	}

	if (utils_valid($objIme))
		$loadDefaultPage = true;
?>
<html>
<head>
<title>OCP</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="/ocp/css/dugmici.css" rel="stylesheet" type="text/css">
<script src="/ocp/jscript/menu.js"></script>
<script language="JavaScript" type="text/JavaScript">
	var ocpId = '<?php echo $ocpId; ?>';

	function loadSteps(){
		parent.document.getElementById("resizableFrameset").setAttribute("rows", "0,*");
		defaultPage();
	}

	function switchTabs(current, previous, action){
		switchMenuTabs(current, previous, action, "ADMIN");
	}

	function openSubmenu(url){
		parent.subMenuFrame.location.href=url;
	}

	var oldHeight = "100%";

	function showSubmenuClose(show, newHeight){
		if (show){
			if (newHeight) oldHeight = "100%";
			parent.document.getElementById("resizableFrameset").setAttribute("rows", oldHeight + ",*");
		} else {
			var rows = parent.document.getElementById("resizableFrameset").getAttribute("rows");
			if (rows.indexOf(",") > -1)
				oldHeight = rows.substring(0, rows.indexOf(","));
			parent.document.getElementById("resizableFrameset").setAttribute("rows", "0,*");
		}
	}

	var oldSubmenuTab = '';
	var submenuArray = new Array();

	function switchSubmenuTab(idTaba){
		switchSubmenuTabAdmin(idTaba);
	}
	
	function populateLetterNavigationSubmenu(ocpLetter, refresh){
		submenuArray = new Array(); oldSubmenuTab = '';
		switchTabs('lista_objekata', 'nadji_objekat', true);

		var submenu = '<table cellpadding="0" cellspacing="0">';
		submenu += '<tr>';
		submenu += '<td nowrap class="ocp_gornji_2_dugme"><span style="color:#C42E00;"><?php echo ocpLabels("Page");?>:</span></td>';
		submenu += createNavigationCell('0-9', "parent.subMenuFrame.newOffset(\'0-9\');", '0-9');
		var alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		for (var i = 0; i < 26; i++){ //left bar
			var nextChr = alphabet.charAt(i);
			submenu += createNavigationCell(nextChr, "parent.subMenuFrame.newOffset(\'"+nextChr+"\');", nextChr);
		}
		submenu += '</tr></table>';
			
		document.getElementById("submenuTd").innerHTML = submenu;
		switchSubmenuTab(ocpLetter);
	}

	function populateNavigationSubmenu(noPerPage, offset, recordCount, refresh){
		submenuArray = new Array(); oldSubmenuTab = '';
		switchTabs('lista_objekata', 'nadji_objekat', true);

		if (recordCount > 0){
			var submenu = '<table cellpadding="0" cellspacing="0">';
			submenu += '<tr>';
			submenu += '<td nowrap class="ocp_gornji_2_dugme"><span style="color: #C42E00;"><?php echo ocpLabels("Page")?>:</span></td>';
				
			var brStavki = 15;
			var right = "";
			var half = parseInt(brStavki/2);

			if (offset - half > 0) 
				submenu += createNavigationCell(offset-half-1, "parent.subMenuFrame.newOffset(\'"+(offset-half-1)+"\');", "&lt;&lt;");
			for (var i = half; i >= 1;i--){ //left bar
				if ((offset-i) >= 0)
					submenu += createNavigationCell(offset-i, "parent.subMenuFrame.newOffset(\'"+(offset-i)+"\');", (offset-i) + 1);
			}
			
			submenu += createNavigationCell(offset, "parent.subMenuFrame.newOffset(\'"+offset+"\');", offset+1);

			var start = noPerPage*offset + noPerPage;
			var i=1;
			if ((start + half*noPerPage) < recordCount)
				right = createNavigationCell(offset+half+1, "parent.subMenuFrame.newOffset(\'"+(offset+half+1)+"\');", "&gt;&gt;");

			 while ((start < recordCount) && (i <= half)){
				submenu += createNavigationCell(offset+i, "parent.subMenuFrame.newOffset(\'"+(offset+i)+"\');", offset+i+1);
				start = start + noPerPage;
				i++;
			}
			submenu += right;

			submenu += '</tr></table>';
			
			document.getElementById("submenuTd").innerHTML = submenu;
			switchSubmenuTab(offset);
		} else {
			document.getElementById("submenuTd").innerHTML = "&nbsp;";
		}
	}

	function createNavigationCell(nextKey, nextItem, nextLabel){
		submenuArray[submenuArray.length] = nextKey;

		var str = '<td height="28" class="ocp_gornji_2_dugme" id="'+nextKey+'" onMouseOver="if (oldSubmenuTab != \''+nextKey+'\'){ this.className=\'ocp_gornji_2_dugme_selected\';}" onMouseOut="if (oldSubmenuTab != \''+nextKey+'\'){ this.className=\'ocp_gornji_2_dugme\';}" style="cursor:pointer;"   onclick="if (oldSubmenuTab != \''+nextKey+'\') { switchSubmenuTab(\''+nextKey+'\'); '+nextItem+'}">';
		str += nextLabel;
		str += '</td>';

		return str;
	}

	function populateQuerySubmenu(){
		submenuArray[submenuArray.length] = '<?php echo ocpLabels("Query");?>';
		var submenu = '<table cellpadding="0" cellspacing="0">';
		submenu += '<tr>';
		submenu += '<td height="28" class="ocp_gornji_2_dugme" id="<?php echo ocpLabels("Query");?>" style="cursor:pointer;">';
		submenu += "<?php echo ocpLabels("Query");?>";
		submenu += '</td>';
		submenu += '</tr></table>';
	
		document.getElementById("submenuTd").innerHTML = submenu;

		switchSubmenuTab('<?php echo ocpLabels("Query");?>');

		if (ocpId == "VL"){
			switchTabs('nadji_objekat', 'izvestaj', true);
		} else if (ocpId == "TR" || ocpId == "DO" )
			switchTabs('nadji_objekat', 'lista_objekata', true);
	}

	function populateDeleteSubmenu(){
		submenuArray[submenuArray.length] = '<?php echo ocpLabels("Delete");?>';
		var submenu = '<table cellpadding="0" cellspacing="0">';
		submenu += '<tr>';
		submenu += '<td height="28" class="ocp_gornji_2_dugme" id="<?php echo ocpLabels("Delete");?>" style="cursor:pointer;">';
		submenu += "<?php echo ocpLabels("Delete");?>";
		submenu += '</td>';
		submenu += '</tr></table>';
	
		document.getElementById("submenuTd").innerHTML = submenu;

		switchSubmenuTab('<?php echo ocpLabels("Delete");?>');
	}

	function eraseQuerySubmenu(){
		document.getElementById("submenuTd").innerHTML = '';

		if (ocpId == "VL")
			switchTabs('izvestaj', 'nadji_objekat', true);
		else if (ocpId == "TR" || ocpId == "DO")
			switchTabs('lista_objekata', 'nadji_objekat', true);
		else if (ocpId == "AL")
			switchTabs('lista_objekata', 'nadji_objekat', true);
	}

	function eraseDeleteSubmenu(){
		document.getElementById("submenuTd").innerHTML = '';

		if (ocpId == "AL"){
			switchTabs(selected, 'brisanje_objekata', true);
		}
	}

<?php menu_script($menuArray, $loadDefaultPage); ?>
</script>
</head>

<body class="ocp_gornji_2_body" onload="loadSteps();">
<?php
	if (utils_valid($ocpId)){	
?>
<table class="ocp_gornji_2_table">
<tr>
	<td class="ocp_gornji_2_naslov">
		<img src="/ocp/img/gornji_2/ikone/admin_ikonica.gif" class="ocp_gornji_2_nasl_ikona" title="<?php echo $objIme;?>">&nbsp;<?php echo $objIme;?>
	</td>
	<td class="ocp_gornji_2_desni">
		<?php menu_html($menuArray); ?>
	</td></tr>
<tr>
	<td class="ocp_gornji_2_td_donji" id="submenuTd"> </td>
	<td height="28" nowrap align="right"> </td></tr>
</table>
<?php 
	} else {	
?>
<table width="100%" height="52" border="0" cellpadding="0" cellspacing="0">
<tr><td width="100%" height="24" class="naslov"> </td></tr>
<tr><td height="28"></td></tr>
</table>
<?php } ?>
</body>
</html>