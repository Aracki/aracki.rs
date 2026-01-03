<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../siteManager/lib/sekcija.php");
	require_once("../../siteManager/lib/stranica.php");
?>
<html>
<head>
	<title>OCP</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="/ocp/css/opcije.css"/>
	<script language="javascript" type="text/javascript" src="/ocp/jscript/swfobject.js"></script>
</head>
<?php
	$StraId = getGVar("Id");
	$type = getGVar("type");
	$field = getGVar("field");
	$VerzId = "";
	$swfArgs = "treeSource=/ocp/siteManager/tree_data.php&treeFilter=intLink";
	$swfArgs .= "&dragDisallowed=1&menuDisallowed=1";
	$swfArgs .= "&versionClickDisallowed=1&sectionClickDisallowed=1&pageClickDisallowed=0";
?>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="document.body.style.overflowX = 'hidden'">
<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
	<tr>
		<td width="100%" valign="top">
			<div id="intlink" style="height:100%"></div>
			<script type="text/javascript">
			   var so = new SWFObject("/ocp/flash/tree.swf?<?php echo $swfArgs?>", "treeFlash", "100%", "100%", "6", "#ffffff");
			   so.write("intlink");
			</script>
		</td>
	</tr>
</table>

<script type="text/javascript">
	var field = "<?php echo $field;?>";

	function openMenuInLowerFrame(type, id)
	{
		var url = id;

		if (type == "Stranica")
		{
			if (field != "undefined")
			{
				if (field.indexOf("document") > -1)
				{
					var inputField = field.substring(field.indexOf('.')+1);
					inputField = inputField.split(".");

					var x = eval("top.opener.document.forms['"+inputField[0]+"'].elements['"+inputField[1]+"']");
				}
				else
				{
					var inputField = field.split(".");

					var modal = opener.window.document.body;
					var targetFields = modal.getElementsByTagName("input");

					for (var i = 0; i < targetFields.length; i++)
					{
						if (targetFields[i].name == inputField[1])
						{
							var x = targetFields[i];
						}
					}
				}
				
				if (x)
				{
					x.value = url;
					window.close();
				}
			}
			else
			{ // ovo mi ne radi, ne znam zasto
				// alert ("modalDebil");
				top.returnValue = url;
				top.close();
			}
		}
	}
</script>
</body>
</html>