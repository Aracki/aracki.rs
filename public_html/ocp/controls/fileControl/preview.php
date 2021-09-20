<?php 
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../fileControl/extensionGroups.php");

	$fileName = utils_requestStr(getGVar("fileName"));
	$field = utils_requestStr(getGVar("field"));
	$sirina = utils_requestStr(getGVar("sirina"));
	$visina = utils_requestStr(getGVar("visina"));
	$max = utils_requestInt(getGVar("max"));
	$fileNameShort = strtolower(substr($fileName, strrpos($fileName, "/")+1));
	$fileExt = strtolower(substr($fileName, strrpos($fileName, ".")+1));

	$image = (utils_lastIndexOf($extensionGroups[1], $fileExt) != -1) ? 1 : 0;
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="/ocp/css/opsti.css">
	<link rel="stylesheet" href="/ocp/css/opcije.css">

	<script type="text/javascript">
		var field = "<?php echo $field?>";
		var sirina = "<?php echo $sirina?>";
		var visina = "<?php echo $visina?>";
		var max = "<?php echo $max?>";
		
		function resizeImage() {
	<?php if ($image) { ?>
			winWidth = document.body.offsetWidth-24;
			winHeight = document.body.offsetHeight;
			if ((imgWidth > winWidth) || (imgHeight > winHeight)){
				resizePercHeight = (winHeight)/imgHeight;
				resizePercWidth = (winWidth)/imgWidth;
				resizePerc = resizePercHeight;
				if (resizePercWidth < resizePercHeight) {
					resizePerc = resizePercWidth;
				}
				img.width=resizePerc*imgWidth;
				img.height=resizePerc*imgHeight;
			} else {
				img.width=imgWidth;
				img.height=imgHeight;		
			}
	<?php	}	?>
		}
		function getDims(){
	<?php if ($image) { ?>
			img = new Image();
			img = document.getElementById("Slika");
			imgWidth = img.width;
			imgHeight = img.height;
			var strOutput = " x ";
			strOutput = imgWidth + strOutput + imgHeight;
			document.getElementById("Dimension").innerHTML = strOutput;
			resizeImage();
	<?php	}	?>
			document.getElementById("Potvrdi").style.visibility = "visible";
			document.getElementById("Obrisi").style.visibility = "visible";
		}<?php 
		
			if (getSVar('ocpAllowedDeleteFiles') == "1"){
		?>function deleteFile(){
			//if (confirm("<?php echo ocpLabels("Are you sure you want to delete file")?>?")){
				window.open("delete_file.php?fileName=<?php echo $fileName?>", "uploadFrame");
			//}
		}<?php
			}	
		?>
	</script>
</head>
<body class="ocp_body" onresize="resizeImage()">
<div align="center" class="ocp_opcije_tekst1" style="padding: 5px 0px 0px 0px">
<?php	
	// ucitavamo sliku

	if (utils_valid($fileExt) && isset($extensionGroups[1]) && ereg($fileExt, $extensionGroups[1]))
	{ 
?>				<img src="<?php echo $fileName?>" id="Slika" onLoad="getDims();" align="center">
				<DIV ID="Dimension" class="ocp_blokovi_td_tekst_3" ALIGN="CENTER">Loading image...</DIV>
				<a href="<?php echo $fileName?>" target="_blank"><?php echo $fileNameShort?></a>
<?php
	}
	else if (utils_valid($fileExt) && isset($extensionGroups[2]) && ereg($fileExt, $extensionGroups[2]))
	{
?>
				<a href="#" onclick="var x = window.open('/ocp/controls/fileControl/swf_popup.php?url=<?php echo $fileName?>', '', 'width=500, height=400, resizable, scrollbars' ); return false;"><img src="/ocp/img/kontrole/file_kontrola/<?php echo $fileExt?>.gif" onLoad="getDims();" border="0" align="center"></a><br>
				<a href="#" onclick="var x = window.open('/ocp/controls/fileControl/swf_popup.php?url=<?php echo $fileName?>', '', 'width=500, height=400, resizable, scrollbars' ); return false;"><?php echo $fileNameShort?></a>
<?php
	}
	else if (utils_valid($fileExt) && isset($extensionGroups[3]) && ereg($fileExt, $extensionGroups[3]))
	{
?>
				<a href="<?php echo $fileName?>" target="_blank"><img src="/ocp/img/kontrole/file_kontrola/<?php echo $fileExt?>.gif" onLoad="getDims();" border="0" align="center"></a><br>
				<a href="<?php echo $fileName?>" target="_blank"><?php echo $fileNameShort?></a>
<?php
	}
	else
	{
?>
				<a href="<?php echo $fileName?>" target="_blank"><img src="/ocp/img/kontrole/file_kontrola/undefined.gif" onLoad="getDims();" border="0" align="center"></a><br>
				<a href="<?php echo $fileName?>" target="_blank"><?php echo $fileNameShort?></a>
<?php
	}
?>		
	<br clear="all"><br>
	<form name="forma" id="forma" action="/ocp/html/blank.html" onsubmit="return submitFiles();">
		<input type="submit" id="Potvrdi" name="Potvrdi" value="<?php echo ocpLabels("Confirm")?>" class="ocp_forma" style="visibility:hidden"  align="center"/>

		<?php 
		if (getSVar('ocpAllowedDeleteFiles') == "1"){
			?><br/><br/>
			<input type="button" id="Obrisi" name="Obrisi" value="<?php echo ocpLabels("Delete file")?>" class="ocp_forma" style="visibility:hidden" align="center" onclick="deleteFile();"/><?php
		}	
	?>
	</form>
</div>
<script type="text/javascript">
	function submitFiles()
	{
		// kada je izbor linka u tableeditoru ovaj prozor je modalni i treba da vrati 
		// url, a field nije definisan
		if (top.opener && field != "undefined")
		{
			if (field.indexOf("document") > -1)
			{
				var inputField = field.substring(field.indexOf('.')+1);
				inputField = inputField.split(".");

				var x = eval("top.opener.document.forms['"+inputField[0]+"'].elements['"+inputField[1]+"']");
				var y = true; z = true;
			}
			else
			{
				var inputField = field.split(".");

				var modal = top.opener.window.document.body;
				var targetFields = modal.getElementsByTagName("input");

				for (var i = 0; i < targetFields.length; i++)
				{
					if (targetFields[i].name == inputField[1])
					{
						var x = targetFields[i];
					}
				}

				// var x = eval("window.modal.forms[0].elements['"+inputField[1]+"']");
				var y = true; z = true;
			}
<?php 
			if ($image)
			{
?>
				// alert(sirina+" "+visina);
				img = document.getElementById("Slika");

				if (top.opener && x && y && z)
				{
					if (!isNaN(sirina) && sirina > 0 && sirina < imgWidth)
					{
						alert("<?php echo ocpLabels("Image maximum width is ")?>" + sirina + "!");
						return false;
					}

					if (!isNaN(visina) && visina > 0 && visina < imgHeight)
					{
						alert("<?php echo ocpLabels("Image maximum height is ")?>" + visina + "!");
						return false;
					}

					if ((sirina != "undefined") && (visina != "undefined"))
					{
						var img = new Image();
						img = document.getElementById("Slika");
						y.value = img.width;
						z.value = img.height;
					}

					//specijalno za prikaz slike u formi
					var imgField = field.replace('urlSlike', 'imgSlike');
					imgField = imgField.replace('formObject.', '');
					var xImg = eval("top.opener.document.getElementById('"+imgField+"');");

					if (xImg && xImg.src)
					{
						xImg.src = "<?php echo $fileName?>";
					}
				}
				else
				{
					alert("<?php echo ocpLabels("Operation not allowed")?>");
					return false;
				}
<?php
			}
?>
			x.value = "<?php echo $fileName?>";
		}
		else
		{
			top.returnValue = "<?php echo $fileName?>";
			top.close();

		}

		return true;
	}

	if ((sirina == "undefined") && (visina == "undefined"))
	{
		document.getElementById("Potvrdi").style.visibility = "visible";
		document.getElementById("Obrisi").style.visibility = "visible";
	}
</script>
</body>
</html>