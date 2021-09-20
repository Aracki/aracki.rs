<?php
	require_once("../include/session.php");
	require_once("../include/connect.php");
	require_once("../include/izvestaji.php");
	require_once("../include/design/menu.php");

	$reportId = utils_requestInt(getGVar("reportId"));
	$loadDefaultPage = 1;	//da li korisnik ima pravo na default operaciju

	$reportIme = "";

	$menuArray = array();
	if (utils_valid($reportId) && ($reportId != 0)){
		$report = izv_get($reportId);
		$reportIme = ocpLabels($report["Ime"]);
		$menuArray['izvestaj'] = array(ocpLabels("Report"), 'openSubmenu("/ocp/reports/upper.php?'.utils_randomQS().'&reportId='.$reportId.'");');
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
		switchMenuTabs(current, previous, action, "REPORTS");
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

<?php  	menu_script($menuArray, $loadDefaultPage);	?>
</script>
</head>
<body class="ocp_gornji_2_body" onLoad="loadSteps();"><?php  
	if (utils_valid($reportId) && ($reportId != 0)){	
?><table class="ocp_gornji_2_table">
  <tr>
    <td class="ocp_gornji_2_naslov"><img src="/ocp/img/gornji_2/ikone/klasa_ikonica.gif" class="ocp_gornji_2_nasl_ikona" title="<?php echo $reportIme?>">&nbsp;<?php echo $reportIme?></td>
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
<?php  	}	?>
</body>
</html>
