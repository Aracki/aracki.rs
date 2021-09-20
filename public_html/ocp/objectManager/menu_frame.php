<?php
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/session.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/connect.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/tipoviobjekata.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/design/menu.php");

	$ocpId = utils_requestInt(getGVar("ocpId"));
	$objId = utils_requestInt(getGVar("objId"));
	$ocpDefaultValues = utils_requestStr(getGVar("ocpDefaultValues"));

	$loadDefaultPage = false;	//da li korisnik ima pravo na default operaciju

	$objIkona = "klasa";
	$objType = ocpLabels(tipobj_getLabel($ocpId));
	$objIme = $objType;
	if (utils_valid($objType))
		$loadDefaultPage = true;

	$menuArray = array();
	if (utils_valid($ocpId) && ($ocpId != 0)){
		$href = 'openSubmenu("/ocp/objectManager/query.php?'.utils_randomQS().'&typeId='.$ocpId.'&case=redirect';
		if (utils_valid($objId)) $href .= "&objId=".$objId;
		$href .= '&ocpDefaultValues='.$ocpDefaultValues.'");';
		$menuArray['lista_objekata'] = array(ocpLabels("Object's list"), $href);
		$menuArray['nadji_objekat'] = array(ocpLabels("Find object"), 'openSubmenu("/ocp/objectManager/query.php?'.utils_randomQS().'&typeId='.$ocpId.'&case=noCount&ocpDefaultValues='.$ocpDefaultValues.'");');
		if (intval($TR[$ocpId]) > 2){
			$menuArray['novi_objekat'] = array(ocpLabels("New object"), 'window.open("/ocp/objectManager/form.php?'.utils_randomQS().'&objId=-1&action=iu&typeId='.$ocpId.'&ocpDefaultValues='.$ocpDefaultValues.'", "detailFrame");');
		}
	}
?>
<html>
<head>
<title>OCP</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="/ocp/css/dugmici.css" rel="stylesheet" type="text/css">
<script src="/ocp/jscript/menu.js"></script>
<script language="JavaScript" type="text/JavaScript">
	function loadSteps(){
		parent.document.getElementById("resizableFrameset").setAttribute("rows", "0,*");
		defaultPage();
	}

	function switchTabs(current, previous, action){
		switchMenuTabs(current, previous, action, "OBJECT MANAGER");
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

	function switchSubmenuTab(idTaba){
		switchSubmenuTabOM(idTaba);
	}

	var oldSubmenuTab = '';
	var submenuArray = new Array();

	function populateNavigationSubmenu(noPerPage, offset, recordCount, refresh){
		submenuArray = new Array(); oldSubmenuTab = '';
		switchTabs('lista_objekata', 'nadji_objekat', true);

		if (recordCount > 0){
			var submenu = '<table cellpadding="0" cellspacing="0">';
			submenu += '<tr>';
			submenu += '<td nowrap class="ocp_gornji_2_dugme"><span style="color: #C42E00;"><?php echo ocpLabels("Page");?>:</span></td>';
				
			var brStavki = 15;
			var right = "";
			var half = parseInt(brStavki/2);

			if (offset - half > 0) 
				submenu += createNavigationCell(offset-half-1, "parent.subMenuFrame.newOffset(\'"+(offset-half-1)+"\');", "&lt;&lt;");
			for (var i = half; i >= 1;i--){ //left bar
				if ((offset-i) >= 0)
					submenu += createNavigationCell(offset-i, "parent.subMenuFrame.newOffset(\'"+(offset-i)+"\');", offset - i + 1);
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
	}

<?php	menu_script($menuArray, $loadDefaultPage);	?>
</script>
</head>
<body class="ocp_gornji_2_body" onload="loadSteps();"><?php
	if (utils_valid($ocpId) && ($ocpId != 0)){	
?><table class="ocp_gornji_2_table">
  <tr>
    <td class="ocp_gornji_2_naslov"><img src="/ocp/img/gornji_2/ikone/<?php echo $objIkona;?>_ikonica.gif" class="ocp_gornji_2_nasl_ikona" title="<?php echo $objIme;?>">&nbsp;<?php echo $objIme;?></td>
	<td class="ocp_gornji_2_desni"><?php
		menu_html($menuArray);
	?></td>
  </tr><tr>
    <td class="ocp_gornji_2_td_donji" colspan="2">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr class="ocp_gornji_2_table">
	  <td class="ocp_gornji_2_td_donji" id="submenuTd"></td>
	  <td height="28" nowrap align="right"></td>
	  </tr>
	</table>
	</td>
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
