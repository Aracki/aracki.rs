<?php 
	require_once("../../include/session.php");

	$newFolderName = utils_requestStr(getGVar("newFolder"));
	$webRoot = realpath("../../.."); //root web site-a
	$destinationPath = "";
	$message = "";

	if (utils_valid($newFolderName)){
		$destinationPath = utils_requestStr(getGVar("Destination"));
		mkdir($webRoot ."/". $destinationPath . $newFolderName, 0777);
		$message = ocpLabels("Folder created");
	}

	if ($destinationPath == "")
		$destinationPath = utils_requestStr(getGVar("putanja"));

	$folderNames = array();
	if ($handle = @opendir("$webRoot/$destinationPath")) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				if (is_dir("$webRoot/$destinationPath/$file")){
					$folderNames[] = $file;
				}
			}
		}
		closedir($handle);
	}
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/ocp/css/opsti.css">
<link rel="stylesheet" href="/ocp/css/opcije.css">
</head>
<body class="ocp_body">
<SCRIPT>
	window.onload = function(){
		parent.document.getElementById("resizableFrameset").setAttribute("rows", "*,130");
	}
	var folderNames = new Array();
<?php
	for ($i=0; $i<count($folderNames); $i++){
?>
	folderNames[<?php echo $i?>] = "<?php echo $folderNames[$i]?>"; 
<?php			
	}
?>

	function check(){
		var x = document.getElementById("formFolder");
		var value = x.newFolder.value;
		if (value == ""){
			alert('<?php echo ocpLabels("You must choose new folder name")?>');
			return false;
		} 
		
		for (var i=0; i<folderNames.length; i++){
			if (folderNames[i] == value){
				alert('<?php echo ocpLabels("Folder with this name exists")?>');
				return false;
			}
		}

		for (var i =0; i<value.length; i++){
			var c = value.charAt(i);
			if ((c >="a" && c <= "z") || (c >="A" && c <= "Z") || (c >="0" && c <= "9") || (c == "_")){
				;
			} else {
				alert('<?php echo ocpLabels("Character")?> '+c+' <?php echo ocpLabels("is not allowed")?>');
				return false;
			}
		}
		return true;
	}
</SCRIPT><?php 
	if ($message != "") { ?>
<script>
	top.top.frames.leftFrameset.frames.treeFrame.refreshTree();
</script><?php 
		require_once("../../include/design/message.php");
		echo( message_info($message));
	} 
?><table class="ocp_naslov_table" width="100%">
	<tr>
		<td class="ocp_naslov_td"><b><?php echo ocpLabels("Add folder")?></b></td>
		<td class="ocp_naslov_td" align="right"><?php echo $destinationPath?></td>
	</tr>
</table>
<table class="ocp_opcije_table" border="0" cellpadding="0" cellspacing="0" width="100%">
	<FORM METHOD="GET" NAME="formFolder" ID="formFolder" onSubmit = "return check();">
	<input type="hidden" name="Destination" value="<?php echo $destinationPath?>">
	<input type="hidden" name="akcija" value="save">	
		<tr>
			<td class="ocp_opcije_td" width="20%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Folder name")?></span><span class="ocp_opcije_obavezno">*</span></td>
			<td class="ocp_opcije_td" width="80%"><input type="text" class="ocp_forma" style="width:100%" name="newFolder" id="newFolder"><td>
		</tr>
</table>
<table align= "center" width="100%">
	<tr>
		<td height="40" align="center" class="ocp_text" colspan="2">
			<input type="submit" name="submitSave" class="ocp_dugme" value="<?php echo ocpLabels("Save")?>">
			&nbsp;
			<input type="button" name="submitCancel" class="ocp_dugme" onclick="parent.menuFrame.showSubmenuClose(true, true);" value="<?php echo ocpLabels("Cancel")?>">
		</td>
	</tr>
</form>
</table>
</body>
</html>