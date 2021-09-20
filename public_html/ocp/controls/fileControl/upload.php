<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	
	//no of files which can be uploaded simultaneously
	$no_files = 10;

	$webRoot = realpath("../../.."); //root web site-a
	$destinationPath = "";
	$message = "";
	
	for ($i=0; $i<$no_files; $i++){
		save_file('filePath', $i);
	}

	function save_file($param_name, $index){
		global $webRoot, $destinationPath, $message;

		if (isset($_FILES[$param_name]) && !is_null($_FILES[$param_name])){
			$error = $_FILES[$param_name]['error'][$index];
			if ($error) {
				$message = $error;
			} else {
				$destinationPath = $_REQUEST["destination"];
				$uploadfile = $webRoot ."/". $destinationPath . basename($_FILES[$param_name]['name'][$index]);
				move_uploaded_file($_FILES[$param_name]['tmp_name'][$index], $uploadfile);
				chmod($uploadfile, 0777);
				$message = ocpLabels("File saved");
			}
		}
	}
	
	if ($destinationPath == "")
		$destinationPath = utils_requestStr(getGVar("putanja"));
	$fileNames = array();
	if ($handle = @opendir($webRoot."/".$destinationPath)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				if (is_file("$webRoot/$destinationPath/$file")){
					$fileNames[] = $file;
				}
			}
		}
		closedir($handle);
	}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/ocp/css/opsti.css">
<link rel="stylesheet" href="/ocp/css/opcije.css">
</head>
<body class="ocp_body">
<script src="/ocp/validate/user/is_necessary.js"></script>
	<script>
		window.onload = function(){
			parent.document.getElementById("resizableFrameset").setAttribute("rows", "*,<?php echo ($no_files*35)?>");
		}
		var labField = "<?php echo ocpLabels("Field")?>";
		var labMustHaveValue = "<?php echo ocpLabels("must have value")?>";

		var fileNames = new Array();
<?php
	for ($i=0; $i<count($fileNames); $i++){
?>
	fileNames[<?php echo $i?>] = "<?php echo $fileNames[$i]?>"; 
<?php			
	}
?>
		function check(){
			var value= true;
			// uporedjujem fajlove
			for (var i=0; i<document.formUpload.elements.length; i++){
				if (document.formUpload.elements[i].name.indexOf("filePath") == 0){
					value = value && overwrite(document.formUpload.elements[i].value);
				}
			}

			return value;
		}

		function overwrite(file){
			if (file == "") return true;

			var value = true;

			if (file.indexOf("\\") != -1)
				file = file.substring(file.lastIndexOf("\\")+1);
			else 
				file = file.substring(value.lastIndexOf("/")+1);
			
			for (var i=0; i<fileNames.length; i++){
				// alert (" | " + file + " | " + fileNames[i]);
				if (fileNames[i] == file){
					var value = confirm(file +": <?php echo ocpLabels("File with this name already exists. Do you want to overwrite it?")?>");
				}
			}

			return value;
		}
	</script><?php 
	if ($message != "") { ?>
<script> parent.listFrame.location.reload(true);</script><?php 
		require_once("../../include/design/message.php");
		echo( message_info($message));
	} 
?><table class="ocp_naslov_table" width="100%">
	<tr>
		<td class="ocp_naslov_td"><b><?php echo ocpLabels("Add file")?></b></td>
		<td class="ocp_naslov_td" align="right"><?php echo $destinationPath?></td>
	</tr>
</table>
<table class="ocp_opcije_table" border="0" cellpadding="0" cellspacing="0" width="100%">
<form method="POST" enctype="multipart/form-data" name="formUpload" id="formUpload" onsubmit = "return check();">
	<input type="hidden" name="destination" value="<?php echo $destinationPath?>">
	<input type="hidden" name="akcija" value="save">	
	<?php for ($i=0; $i<$no_files; $i++){ ?>
		<tr>
		<td class="ocp_opcije_td" width="20%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("File path")?> <?php echo $i+1?></span><?php if ($i==0){?><span class="ocp_opcije_obavezno">*</span><?php } ?></td>
		<td class="ocp_opcije_td" width="80%"><input type="file" class="ocp_forma" style="width:100%" value="Upload" name="filePath[]"><td>
	</tr>
	<?php } ?>
</table>
<table align= "center" width="100%">
	<tr>
		<td height="40" align="center" class="ocp_text" colspan="2">
			<input type="submit" name="submitSave" class="ocp_dugme" value="<?php echo ocpLabels("Submit")?>">
			&nbsp;
			<input type="button" name="submitCancel" class="ocp_dugme" onclick="parent.menuFrame.showSubmenuClose(true, true);" value="<?php echo ocpLabels("Cancel")?>">
		</td>
	</tr>
</form>
</table>
</body>
</html>