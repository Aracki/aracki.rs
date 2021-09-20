<?php 
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../include/design/menu.php");
        $type = getRVar("objType");
	$root = utils_requestStr(getGVar("root"));
	$field= utils_requestStr(getGVar("field"));
	$sirina = utils_requestStr(getGVar("sirina"));
	$visina = utils_requestStr(getGVar("visina"));
	$max = utils_requestInt(getGVar("max"));
	$putanja = utils_requestStr(getGVar("putanja"));

	$parameters = "root=".$root."&field=".$field."&sirina=".$sirina."&visina=".$visina."&max=".$max."&putanja=".$putanja."&objType=".$type;
	$loadDefaultPage = false;	//da li postoji default operacija

	$menuArray = array();

	$loadDefaultPage = true;
	$menuArray['lista_objekata'] = array(ocpLabels("List"), 'window.open("/ocp/controls/imageControl/list.php?'.utils_randomQS().'&listType=list&'.$parameters.'", "listFrame");');
	$menuArray['thumbs'] = array(ocpLabels("Thumbs"), 'window.open("/ocp/controls/imageControl/list.php?'.utils_randomQS().'&listType=thumbs&'.$parameters.'", "listFrame");');
	$menuArray['novi_fajl'] = array(ocpLabels("New file"),  'window.open("/ocp/controls/imageControl/upload.php?'.utils_randomQS().'&putanja='.$putanja.'", "uploadFrame");');
	$menuArray['novi_folder'] = array(ocpLabels("New folder"),  'window.open("/ocp/controls/imageControl/newfolder.php?'.utils_randomQS().'&putanja='.$putanja.'", "uploadFrame");');
	//$menuArray['setup'] = array(ocpLabels("Setup"),  'window.open("/ocp/controls/imageControl/setup.php?'.utils_randomQS().'&putanja='.$putanja.'&objType='.$type.'", "listFrame");');
	
?>
<html>
<head>
<title>OCP</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="/ocp/css/dugmici.css" rel="stylesheet" type="text/css">
<script src="/ocp/jscript/menu.js"></script>
<script language="JavaScript" type="text/JavaScript">
	function loadSteps(){
		defaultPage();
	}

	function switchTabs(current, previous, action){
		switchMenuTabs(current, previous, action, "FILE MANAGER");
	}

	function switchSubmenuTab(idTaba){
		switchSubmenuTabOM(idTaba);
	}

	var oldSubmenuTab = '';
	var submenuArray = new Array();

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
	
	function populateNavigationSubmenu(noPerPage, offset, recordCount, refresh){
		submenuArray = new Array(); oldSubmenuTab = '';
		
		var submenu = '<table cellpadding="0" cellspacing="0" width="100%" height="28">';
		submenu += '<tr>';
		if (recordCount > 0){
			submenu += '<td nowrap class="ocp_gornji_2_dugme"><span style="color: #C42E00;"><?php echo ocpLabels("Page")?>:</span></td>';
				
			var brStavki = 15;
			var right = "";
			var half = parseInt(brStavki/2);

			if (offset - half > 0) 
				submenu += createNavigationCell(offset-half-1, "parent.listFrame.newOffset(\'"+(offset-half-1)+"\');", "&lt;&lt;");
			for (var i = half; i >= 1;i--){ //left bar
				if ((offset-i) >= 0)
					submenu += createNavigationCell(offset-i, "parent.listFrame.newOffset(\'"+(offset-i)+"\');", offset - i + 1);
			}
				
			submenu += createNavigationCell(offset, "parent.listFrame.newOffset(\'"+offset+"\');", offset+1);

			var start = noPerPage*offset + noPerPage;
			var i=1;
			if ((start + half*noPerPage) < recordCount)
				right = createNavigationCell(offset+half+1, "parent.listFrame.newOffset(\'"+(offset+half+1)+"\');", "&gt;&gt;");

			 while ((start < recordCount) && (i <= half)){
				submenu += createNavigationCell(offset+i, "parent.listFrame.newOffset(\'"+(offset+i)+"\');", offset+i+1);
				start = start + noPerPage;
				i++;
			}
			submenu += right;
		}

		<?php	if (getSVar('ocpAllowedDeleteFiles') == "1"){			?>
			submenu += '<td height="28" class="ocp_gornji_2_dugme" style="cursor:pointer;"   onclick="deleteSubfolder('+recordCount+');" align="right" width="100%" onMouseOver="this.className=\'ocp_gornji_2_dugme_selected\';" onMouseOut="this.className=\'ocp_gornji_2_dugme\';">';
			submenu += '<table class="ocp_gornji_2_table_dugmici"><tr> ';
			submenu += '<td class="ocp_gornji_2_td_dugmici"><img src="/ocp/img/gornji_2/dugmici/plavo_levo.gif" width="2" height="28"></td> ';
			submenu += '<td background="/ocp/img/gornji_2/dugmici/plavo_bg.gif">';
			submenu += '<table class="ocp_gornji_2_table_dugmici"> ';
			submenu += '<tr> ';
			submenu += '<td nowrap class="ocp_gornji_2_dugme"><?php echo ocpLabels("Delete subfolder")?></td> ';
			submenu += '</tr> ';
			submenu += '</table></td> ';
			submenu += '<td class="ocp_gornji_2_td_dugmici"><img src="/ocp/img/gornji_2/dugmici/plavo_desno.gif" width="10" height="28"></td> ';
			submenu += '</tr></table>';
			submenu += '</td>';
		<?php	}	?>

		submenu += '</tr></table>';
			
		document.getElementById("submenuTd").innerHTML = submenu;
		document.getElementById("submenuTd").style.width = "100%";

		if (recordCount > 0)
			switchSubmenuTab(offset);

	}

	function createNavigationCell(nextKey, nextItem, nextLabel){
		submenuArray[submenuArray.length] = nextKey;

		var str = '<td height="28" class="ocp_gornji_2_dugme" id="'+nextKey+'" onMouseOver="if (oldSubmenuTab != \''+nextKey+'\'){ this.className=\'ocp_gornji_2_dugme_selected\';}" onMouseOut="if (oldSubmenuTab != \''+nextKey+'\'){ this.className=\'ocp_gornji_2_dugme\';}" style="cursor:pointer;"   onclick="if (oldSubmenuTab != \''+nextKey+'\') { switchSubmenuTab(\''+nextKey+'\'); '+nextItem+'}">';
		str += nextLabel;
		str += '</td>';

		return str;
	}
	
	<?php 
	
		if (getSVar('ocpAllowedDeleteFiles') == "1"){
	?>function deleteSubfolder(recordCount){
		if (recordCount > 0){
			alert("<?php echo ocpLabels("The folder must be empty in order to be deleted")?>!");
			return false;
		}
		if (confirm("<?php echo ocpLabels("Are you sure you want to delete folder")?>?")){
			window.open("delete_folder.php?folderName=<?php echo substr($putanja, 0, strlen($putanja)-1)?>", "uploadFrame");
		}
	}<?php
		}	
	?>
<?php	menu_script($menuArray, $loadDefaultPage);	?>
</script>
</head>
<body class="ocp_gornji_2_body" onload="loadSteps();"><?php
	if (utils_valid($root)){
		$folder = substr($putanja, 0, strrpos($putanja, "/"));
		$folder = substr($folder, strrpos($folder, "/")+1);
		// milos dodao
		$path = "/";
		if ($root != "/"){
			$path = substr($putanja, 0, strrpos($putanja, "/"));
			$path = substr($path, 0, strrpos($path, "/"));
			$path .= "/";
		}
		$folder = $path."<b>".$folder."</b>";
		//------------
?><table class="ocp_gornji_2_table">
  <tr>
    <td class="ocp_gornji_2_naslov"><img src="/ocp/img/gornji_2/ikone/sekcija_ikonica.gif" class="ocp_gornji_2_nasl_ikona" title="<?php echo preg_replace("/[<](\w|\W)+[>]/U", "", $folder)?>">&nbsp;<?php echo $folder?></td>
	<td class="ocp_gornji_2_desni">
	<?php menu_html($menuArray); ?></td>
  </tr><tr>
    <td  height="28" class="ocp_gornji_2_td_donji" id="submenuTd" colspan="2"> </td>
	<!-- <td height="28" nowrap align="right"> </td> -->
  </tr>
</table><?php 
	}	else {	
?><table width="100%" height="52" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="100%" height="24" class="naslov"> </td>
  </tr>
  <tr>
    <td height="28"></td>
  </tr>
</table>
<?php	}	?>
</body>
</html>
