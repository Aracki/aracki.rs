<?php
	require_once("../../../include/connect.php");
	require_once("../../../include/session.php");
	require_once("../stranica.php");
	require_once("../blokfunctions.php");
?><HTML>
<HEAD>
<TITLE> OCP </TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="/ocp/css/opsti.css" rel="stylesheet" type="text/css">
<link href="/ocp/css/opcije.css" rel="stylesheet" type="text/css">
<SCRIPT>
	function submitForm(Akcija){
		var forma = document.getElementById("formObject");
		var radioGroup = forma.StBl_IdMove;
		var checked = false;
	
		if (radioGroup != null){
			if (radioGroup.length){
				for (var i=0; i<radioGroup.length; i++){
					var radio = radioGroup[i];
					if (radio.checked){
						checked = true;
						break;
					}
				}
			} else 
				if (radioGroup.checked) checked = true;
		}

		if (!checked){
			alert("Niste odabrali orjentacioni blok");
			return false;
		}
		if (Akcija == "C")	{
			forma.Akcija.value = "Copy"; 
			Verz_IdOrigin = forma.Verz_IdOrigin.value;
			Verz_IdMove = forma.Verz_IdMove.value;
		}
		forma.submit();
	}

	function submitForm2(Akcija){
		var forma = document.getElementById("formObject");

		if (Akcija == "C")	{
			forma.Akcija.value = "Copy"; 
			Verz_IdOrigin = forma.Verz_IdOrigin.value;
			Verz_IdMove = forma.Verz_IdMove.value;
		}
		forma.submit();
	}
</SCRIPT>
</HEAD>
<?php
	$Stra_Id = utils_requestInt(getGVar("Stra_Id"));
	$Akcija = utils_requestStr(getPVar("Akcija"));
?><BODY class="ocp_body" style="background-color:#ffffff;">
<?php
	if (utils_valid($Akcija)){//posle submita
		$Stra_Id = utils_requestInt(getPVar("Stra_Id"));
		$arrStBlId = $_POST["StBl_Id"];
		$StBl_IdMove = utils_requestInt(getPVar("StBl_IdMove"));
		$Dir = utils_requestStr(getPVar("Dir"));

		if ($Akcija == "Move"){//pomeranje
//utils_dump ($Stra_Id." | "..$arrStBlId." | ".$StBl_IdMove." | ".$Dir." <br> ");
			blokFn_move($Stra_Id, $arrStBlId, $StBl_IdMove, $Dir);
	?><SCRIPT>
		parent.CloseMe();
		window.open("/ocp/siteManager/blokoviedit.php?<?php echo utils_randomQS();?>&Stra_Id="+parent.straIdOrigin, "detailFrame");
	</SCRIPT><?php		
		} else {//kopiranje
//utils_dump ("COPY!");
			blokFn_copy($Stra_Id, $arrStBlId, $StBl_IdMove, $Dir);	?>
	<SCRIPT>
		parent.CloseMe();
		if (parent.straIdOrigin == "<?php echo $Stra_Id?>")
			window.open("/ocp/siteManager/blokoviedit.php?<?php echo utils_randomQS();?>&Stra_Id="+parent.straIdOrigin, "detailFrame");
	</SCRIPT>
<?php		
		}
	} else {//pre submita
		$arrStBlId =$_GET["StBl_Id"];
		$Stra_IdOrigin = con_getValue("select StBl_Stra_Id from Stranica_Blok where StBl_Id=".$arrStBlId[0]);
		$Verz_IdOrigin = stranica_getVersion($Stra_IdOrigin);
		$Verz_IdMove = stranica_getVersion($Stra_Id);
?>
<div id="ocp_blok_menu_1"> 
	<table class="ocp_blokovi_table"> 
		<tr> 
			<td class="ocp_blokovi_td"><img src="/ocp/img/opsti/razno/hint.gif" style="vertical-align: bottom; margin-left:3px;"> <b>3/3</b> <?php echo ocpLabels("Choose block from page");?> <b><?php echo stranica_getProperty($Stra_Id, "Stra_Naziv");?></b></td> 
		</tr> 
	</table> 
</div>
<?php	
		$blokovi = stranica_getAllBlok($Stra_Id);
?>
<div id="ocp_blok_menu_2">
	<table class="ocp_opcije_table">
	<form id="formObject" method="Post" action="/ocp/siteManager/lib/move/move.php?<?php echo utils_randomQS();?>">
		<input type="hidden" name="Akcija" value="Move">
		<input type="hidden" name="Stra_Id" value="<?php echo $Stra_Id;?>">
		<input type="hidden" name="Verz_IdOrigin" value="<?php echo $Verz_IdOrigin;?>">
		<input type="hidden" name="Verz_IdMove" value="<?php echo $Verz_IdMove;?>">
<?php
		for ($i=0;$i<count($arrStBlId);$i++){
?>
		<input type="hidden" name="StBl_Id[]" value="<?php echo $arrStBlId[$i];?>">
<?php		
		}
		if (count($blokovi) > 0){ //ako ima blokova	
			$allSelected = true;
			if ($Stra_IdOrigin == $Stra_Id){
				for ($i=0;$i<count($blokovi);$i++) {
					$allSelected = $allSelected && in_array($blokovi[$i]["StBl_Id"], $arrStBlId); }
			} else $allSelected = 0;

			for ($i=0; $i < count($blokovi);$i++){
				$blok = $blokovi[$i];
				$tekst = $blok["Blok_Tekst"];

				if (utils_strlen($tekst) > 30) 
					$tekst = utils_substr($tekst, 0, 30) . "...";
				else if (!utils_valid($tekst)) $tekst = "";
?>
	<tr> 
		<td class="ocp_opcije_td" style="width:30px;text-align:right;"><span class="ocp_opcije_tekst1">#<?php echo $blok["StBl_Id"];?></span></td> 
		<td class="ocp_opcije_td" style="width:40px; text-align: center;"><img src="<?php echo str_replace("/srednji/", "/veliki/", $blok["TipB_SlikaUrl"])?>" width="30" height="25" title="<?php if ($blok["Blok_Share"] == "1") echo("deljeni: ".$blok["Blok_MetaNaziv"]); else echo($blok["TipB_Naziv"]);?>"></td>
		<td class="ocp_opcije_td" style="width:100%;"><span class="ocp_opcije_tekst2"><?php echo $tekst;?></span></td>
		<td class="ocp_opcije_td_forma" style="width:90px; text-align: center;"><?php			
			if (($allSelected && (($i+1)==count($blokovi))) || (!in_array($blok["StBl_Id"], $arrStBlId))){
			?><input type="radio" name="StBl_IdMove" value="<?php echo $blok["StBl_Id"];?>" checked><?php	
			}	
?>
		</td>
	</tr>
<?php
			}
		} else { //ako nema blokova
			?><tr><td style="text-align:left;"><?php require_once("../../../include/design/message.php"); ?>
			<?php echo message_info(ocpLabels("THERE ARE NO BLOCKS IN THIS PAGE"));?>
			</td></tr>
<?php
		}
?>
	<tr>
		<td colspan="4">
		
		
		<table align="center">
			<tr>
<?php		
		if (count($blokovi) > 0) {
?>
				<td class="ocp_opcije_tekst1" style="padding-right: 5px;">
					<input type="radio" name="Dir" value="Up">&nbsp;<?php echo ocpLabels("Above");?>
					<input type="radio" name="Dir" value="Down" checked>&nbsp;<?php echo ocpLabels("Behind");?>
				</td>
				<td>
					<input type="button" class="ocp_dugme" name="Pomeri" value="<?php echo ocpLabels("Move");?>" onClick="submitForm('M')">
					<input type="button" class="ocp_dugme" id="Kopiraj" name="Kopiraj" value="<?php echo ocpLabels("Copy");?>" onClick="submitForm('C')">
				</td>
<?php		
		} else {
?>
				<td>
					<input type="button" class="ocp_dugme" name="Pomeri" value="<?php echo ocpLabels("Move");?>" onClick="submitForm2('M')">
					<input type="button" class="ocp_dugme" id="Kopiraj" name="Kopiraj" value="<?php echo ocpLabels("Copy");?>" onClick="submitForm2('C')">
				</td>		
<?php		
		}
?>

			</tr>
		</table>
		</td>
	</tr>
	</form>
	</table>
</div>
<?php
	}
?>
</BODY>
</HTML>