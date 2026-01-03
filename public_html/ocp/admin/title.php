<?php
	require_once("../include/session.php");
?>

<?php session_checkAdministrator(); ?>

<html>
<head>
<title>OCP title frame</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="/ocp/css/gornji.css" rel="stylesheet" type="text/css">
<script>
	var userWidth = 0;
	function getUserWidth(){
		var cols = parent.parent.document.getElementById("leftFrameset").getAttribute("cols");
		if (cols.indexOf(",") > -1)
			return cols.substring(0, cols.indexOf(","));

		return null;
	}

	function openLeftFrameset(){
		if (getUserWidth() == "0"){
			var tdHtml = '<a href="javascript:closeLeftFrameset();"><img src="/ocp/img/gornji_1/kontrole/dugme_zatvori_frame.gif" width="40" height="30" border="0" title="<?php echo ocpLabels("Close OCP\'s left frame");?>">';
			var td = document.getElementById('openCloseButton');
			td.innerHTML = tdHtml;
			parent.parent.document.getElementById("leftFrameset").setAttribute('cols', userWidth + ',*');
		} else {
			closeLeftFrameset();
		}
	}

	function closeLeftFrameset(){
		userWidth = getUserWidth();
		var tdHtml = '<a href="javascript:openLeftFrameset();"><img src="/ocp/img/gornji_1/kontrole/dugme_otvori_frame.gif" width="40" height="30" border="0" title="<?php echo ocpLabels("Close OCP\'s left frame");?>">';
		var td = document.getElementById('openCloseButton');
		td.innerHTML = tdHtml;
		parent.parent.document.getElementById("leftFrameset").setAttribute('cols', '0,*');
	}
</script>
</head>
<body class="ocp_gornji_body">
<table class="ocp_gornji_table">
  <tr>
    <td class="ocp_gornji_td_levi" valign="top"><table cellpadding="0" cellspacing="0">
		<tr><td id="openCloseButton"><a href="javascript:closeLeftFrameset();"><img src="/ocp/img/gornji_1/kontrole/dugme_zatvori_frame.gif" width="40" height="30" border="0" title="<?php echo ocpLabels("Close OCP\'s left frame"); ?>"></a></td><td align="left" class="ocp_gornji_title"><?php echo ocpLabels("Administration"); ?></td></tr></table>
	</td>
    <td class="ocp_gornji_td_desni">
	  <table class="ocp_gornji_table_desni">
        <tr>
          <td class="ocp_gornji_td_desni" style="cursor:pointer" onclick="parent.close();"><?php echo ocpLabels("close window"); ?></td>
        </tr>
      </table>
	</td>
  </tr>
</table>
</body>
</html>