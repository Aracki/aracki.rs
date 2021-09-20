<?php 
	require_once("../../include/session.php");
?><html>
<head>
<title>OCP</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="/ocp/css/gornji.css" rel="stylesheet" type="text/css">
</head>
<body class="ocp_gornji_body">
<table class="ocp_gornji_table">
  <tr>
    <td class="ocp_gornji_td_levi"><table cellpadding="0" cellspacing="0">
		<tr>
		  <td align="left" class="ocp_gornji_title"><?php echo ocpLabels("File manager")?></td>
		</tr></table>
	</td>
    <td class="ocp_gornji_td_desni" onclick="top.close();" style="cursor:pointer;"><?php echo ocpLabels("close window")?></td>
  </tr>
</table>
</body>
</html>