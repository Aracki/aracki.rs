<?php
require_once("../../include/connect.php");
require_once("../../include/session.php");
require_once("../../include/selectradio.php");
require_once("../../include/language.php");
?>

<?php session_checkAdministrator(); ?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>OCP</title>
<link rel="stylesheet" href="/ocp/css/opsti.css">
<link rel="stylesheet" href="/ocp/css/opcije.css">
</head>
<body class="ocp_body"><?php
	$action = utils_requestStr(getPVar("Action"));

	if (utils_valid($action)){//submitovana je lista 
		$listType = utils_requestStr(getPVar("TipTabela"));
		$listId = utils_requestStr(getPVar("Ime"));
		$noItems = utils_requestInt(getPVar("noItems"));

		$insertedLabels = false;
		if (($action == "Izmeni") || ($action == "IzmeniIzForme")){//update postojece liste
			$records = selrad_getListValues($listId, $listType);
			for ($i=0; $i<count($records); $i++){
				$label = utils_requestStr(getPVar("Labela".$i));
				$value = utils_requestStr(getPVar("Vrednost".$i));
				$oldLabel = $records[$i]["Labela"];
				$oldValue = $records[$i]["Vrednost"];

				//update u svim tabelama promeniti value
				if (utils_valid($label) && utils_valid($value) && 
					(($label != $oldLabel) || ($value != $oldValue)) ){
					selradio_updateList($listId, $listType, $value, $label, $oldValue, $oldLabel);	
					$insertedLabels = lang_newLabela($label) || $insertedLabels;
				}
			}
			//dodavanje novih vrednosti
			for ($i=0; $i<$noItems; $i++){ 
				$newLabela = utils_requestStr(getPVar("NewLabela" . $i));
				$newVrednost = utils_requestStr(getPVar("NewVrednost" . $i));
				if (utils_valid($newLabela) && utils_valid($newVrednost)){
					selradio_updateList($listId, $listType, $newVrednost, $newLabela, NULL, NULL);
					$insertedLabels = lang_newLabela($newLabela) || $insertedLabels;
				}
			}
		} else {//insert nove liste
			for ($i=0; $i<$noItems; $i++){
				$label = utils_requestStr(getPVar("NewLabela".$i));
				$value = utils_requestStr(getPVar("NewVrednost".$i));

				if (utils_valid($label) && utils_valid($value)){
					selradio_updateList($listId, $listType, $value, $label, NULL, NULL);
					$insertedLabels = lang_newLabela($label) || $insertedLabels;
				}
			}
		}

		?><script>
		<?php	if ($insertedLabels){	?>
		alert("<?php echo ocpLabels("New labels have been added to Multilanguage support. Please translate them.");?>");
		<?php	}
			if ($action != "IzmeniIzForme"){
		?>
		parent.subMenuFrame.reconstruct();
		parent.document.getElementById("resizableFrameset").setAttribute("rows", "100%,*");
		<?php	}	?>
		</script><?php
	} else { //pre submita
		$action = utils_requestStr(getGVar("action"));	
		$listId = utils_requestStr(getGVar("listId"));	//ime liste
		$listType = utils_requestStr(getGVar("listType"));		//select ili radio
		$noItems = utils_requestStr(getGVar("noItems"));		//select ili radio

		switch ($action){
			case "deleteValue" : 
				$oldValue = utils_requestStr(getGVar("oldValue"));
				$oldLabel = utils_requestStr(getGVar("oldLabel"));
				//delete iz liste - kada se ubije neka label i njena value
				//u svim tabelama u kojima je iskoristena postaviti na ""
				selradio_deleteValue($listId, $listType, $oldValue, $oldLabel);
				$action = utils_requestStr(getGVar("oldAction"));
				drawEditForm($listId, $listType, $action, $noItems);
				break;
			case "deleteList" : 
				selradio_deleteList($listId, $listType);
				?><script>parent.subMenuFrame.reconstruct();</script><?php
				break;
			case "iu" :
			case "iuForm":
				drawEditForm($listId, $listType, $action, $noItems);
				break;
		}
	}

	function drawEditForm($listId, $listType, $action, $noItems){
?><div id="ocp_main_table">
<form name="formObject" id="formObject" method="post" onSubmit="return validate();" action="lists_edit.php?<?php echo utils_randomQS();?>">
	<?php 
	if ($listId != "-1"){
		if ($action == "iu"){
	?><input type="hidden" name="Action" value="Izmeni"><?php	
		} else {
	?><input type="hidden" name="Action" value="IzmeniIzForme"><?php	
		}
	?><input type="hidden" name="Ime" value="<?php echo $listId;?>"><?php
	} else {
	?><input type="hidden" name="Action" value="Novi"><?php
	}	?>
	<input type="hidden" name="TipTabela" value="<?php echo $listType;?>">
	<input type="hidden" name="noItems" value="<?php echo $noItems;?>">
	<table class="ocp_naslov_table">
		<tr>
			<td class="ocp_naslov_td"><?php if ($listId != "-1") echo(ocpLabels("Edit list").": ".$listId); else echo(ocpLabels("New list"));?></td>
		</tr>
	</table>
	<table class="ocp_opcije_table" border="0" cellpadding="0" cellspacing="0"><?php 
	if ($listId == "-1"){//insert
	?><tr>
		<td class="ocp_opcije_td ocp_opcije_tekst1" colspan="3"><?php echo ocpLabels("Name");?></td>
	  </tr>
	  <tr>
		<td class="ocp_opcije_td ocp_opcije_tekst1" colspan="3">
			<input name="Ime" type="text" class="ocp_forma" style="width: 100%;" value=""></td>
	  </tr><?php
	}
	?><tr>
			<td width="44%" class="ocp_opcije_td ocp_opcije_tekst1"><?php echo ocpLabels("Label");?></td>
			<td width="48%" class="ocp_opcije_td ocp_opcije_tekst1"><?php echo ocpLabels("Value");?></td>
			<td width="8%" class="ocp_opcije_td ocp_opcije_tekst1"><?php if ($listId != "-1"){ ?><?php echo ocpLabels("Delete");?><?php } ?></td>
		</tr><?php
		if ($listId != "-1"){
			$records = selrad_getListValues($listId, $listType);
			for ($i=0; $i<count($records); $i++){
				$record = $records[$i];
	?><tr style="position:relative; top:0px">
		<td class="ocp_opcije_td ocp_opcije_tekst1" style="width: 22%;">
			<input name="Labela<?php echo $i;?>" type="text" class="ocp_forma" style="width: 100%;" value="<?php echo $record["Labela"];?>"></td>
		<td class="ocp_opcije_td ocp_opcije_tekst1">
			<input name="Vrednost<?php echo $i;?>" type="text" class="ocp_forma" style="width: 100%;" value="<?php echo $record["Vrednost"];?>">
		</td>
		<td class="ocp_opcije_td_forma" align="center" style="border-top:0px;"><img src="/ocp/img/blank.gif" border="0"><img src="/ocp/img/blank.gif" border="0"><img src="/ocp/img/opsti/kontrole/kontrola_obrisi_objekat.gif" border="0" width="20" height="21" onClick="deleteValue('<?php echo $record["Labela"];?>', '<?php echo $record["Vrednost"];?>');" title="<?php echo ocpLabels("Delete object");?>" style="cursor:pointer;"></td>
	  </tr><?php
			}
		}

		for ($i=0; $i<$noItems; $i++){
		?><tr style="position:relative; top:0px">
		<td class="ocp_opcije_td ocp_opcije_tekst1"><input name="NewLabela<?php echo $i;?>" type="text" class="ocp_forma" style="width: 100%;"></td>
		<td class="ocp_opcije_td ocp_opcije_tekst1"><input name="NewVrednost<?php echo $i;?>" type="text" class="ocp_forma" style="width: 100%;">    </td>
		<td align="center" class="ocp_opcije_td ocp_opcije_tekst1">&nbsp;</td>
	 </tr><?php
		} 
	?></table>
	<table width="100%">
		<tr>
			<td height="40" align="center" class="ocp_text"><input type="submit" name="submitSave" class="ocp_dugme" value="<?php echo (ocpLabels("Save")); ?>">&nbsp;<input type="button" name="submitCancel" class="ocp_dugme" onClick="<?php if ($action == "iu"){ ?>parent.menuFrame.showSubmenuClose(true, true);<?php } else { ?>window.close();<?php } ?>" value="<?php echo (ocpLabels("Cancel"));?>"></td>
		</tr>
	</table>
</form>
</div><?php
	$userHeight = utils_valid(getSVar("ocpUserHeight")) ? getSVar("ocpUserHeight") : "25%";
?>
<script src="/ocp/validate/validate_double_quotes.js"></script>
<script language="javascript">
<?php 
	if ($action != "iuForm"){	?>
	window.onload = function(){
		parent.document.getElementById("resizableFrameset").setAttribute("rows", "<?php echo $userHeight;?>,*");
	}
<?php 
	}	?>
	function deleteValue(lab, val){
		if (confirm("<?php echo ocpLabels('Are you sure you want to delete object');?>?"))	window.open("lists_edit.php?<?php echo utils_randomQS();?>&listType=<?php echo $listType;?>&listId=<?php echo $listId;?>&action=deleteValue&oldAction=<?php echo $action;?>&oldValue="+val+"&oldLabel="+lab, "_self");
	}

<?php	$lists = selrad_getAllUsedListNames($listType);

	for ($j=0; $j<count($lists); $j++) {
		if ($j==0) echo("elems = new Array(");
		if ($j != (count($lists)-1)) echo('"'.$lists[$j].'",');
		else echo('"'.$lists[$j].'");');
	}

	if (count($lists) == 0)	echo("elems = new Array();");
	echo("\n");	?>

	function validate(){
		var lista = document.formObject;
		
		if (lista.Action.value == "Novi") {//da li je jedinstveno ime liste
			var listName = lista.Ime.value;
			if (listName == ""){
				alert("<?php echo ocpLabels("List name");?>"+ " " + "<?php echo ocpLabels("must have value");?>.");
				return false;
			}
			var found = false;
			for (var j=0; j<elems.length; j++){	
				if (elems[j] == listName) {
					found = true; break;
				}
			}
			if (found){
				alert("<?php echo ocpLabels("List with this name already exist");?>.");
				return false;
			}
		}

		var i = 0;
		var nextLabel = eval("lista.Labela"+i);
		var oldValues = new Array();
		var oldLabels = new Array();
		while (nextLabel != null){
			var nextValue = eval("lista.Vrednost"+i);
			
			if (((nextLabel.value=="") && (nextValue.value!="")) || 
				((nextLabel.value!="") && (nextValue.value=="")) ){
				alert("<?php echo ocpLabels("List element must have label and value");?>.");
				return false;
			}

			for (var j=0; j<oldValues.length; j++){
				if (oldValues[j] == nextValue.value){
					alert("<?php echo ocpLabels("List values must be different");?>.");
					return false;
				}
			}

			for (var j=0; j<oldLabels.length; j++){
				if (oldLabels[j] == nextLabel.value){
					alert("<?php echo ocpLabels("List labels must be different");?>.");
					return false;
				}
			}
			
			if (nextLabel.value!="") oldLabels[oldLabels.length] = nextLabel.value;
			if (nextValue.value!="") oldValues[oldValues.length] = nextValue.value;	

			i++;
			nextLabel = eval("lista.Labela"+i);
		}
	
		var i = 0;
		var nextLabel = eval("lista.NewLabela"+i);
		while (nextLabel != null){
			var nextValue = eval("lista.NewVrednost"+i);
			if (	((nextLabel.value=="") && (nextValue.value!="")) || 
					((nextLabel.value!="") && (nextValue.value==""))	){
				alert("<?php echo ocpLabels("List element must have label and value");?>.");
				return false;
			}
			
			for (var j=0; j<oldValues.length; j++){
				if (oldValues[j] == nextValue.value){
					alert("<?php echo ocpLabels("List values must be different");?>.");
					return false;
				}
			}

			for (var j=0; j<oldLabels.length; j++){
				if (oldLabels[j] == nextLabel.value){
					alert("<?php echo ocpLabels("List labels must be different");?>.");
					return false;
				}
			}

			if (nextLabel.value!="") oldLabels[oldLabels.length] = nextLabel.value;
			if (nextValue.value!="") oldValues[oldValues.length] = nextValue.value;	
			i++;
			nextLabel = eval("lista.NewLabela"+i);
		} 

		validate_double_quotes(document.formObject);

		return true;
	}
</script><?php
	}
?></body>
</html>