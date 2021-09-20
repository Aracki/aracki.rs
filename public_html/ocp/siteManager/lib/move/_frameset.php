<?php
	require_once("../../../include/connect.php");
	require_once("../../../include/session.php");
	require_once("../stranica.php");
	require_once("../blokfunctions.php");
?><HTML>
<HEAD>
<link href="/ocp/css/opsti.css" rel="stylesheet" type="text/css">
<link href="/ocp/css/opcije.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title> OCP </title><?php
	$straId = utils_requestInt(getGVar("Stra_Id"));
	if ($straId == "undefined") $straId = utils_requestInt(getPVar("Stra_Id"));
	$BlokCount = count(getGVar("StBl_Id"));

	$straId = utils_requestInt(getGVar("Stra_Id"));
	if (!utils_valid($straId) || ($straId == 0)) 
		$straId = utils_requestInt(getPVar("Stra_Id"));

	$BlokCount = 0;
	if (isset($_POST["StBl_Id"])){
		$tempArray = utils_requestInt(getPVar("StBl_Id"));
		$BlokCount = count($tempArray);
	}
?><SCRIPT>
	straIdOrigin = "<?php echo $straId;?>"
	var ocpSized = false;
	function check(){
		var forma = document.getElementById("formObject");
		
		var checkGroup = new Array();
		for (var i=0; i< forma.elements.length; i++){
			if (forma.elements[i].name.indexOf("StBl_Id[]") == 0){
				checkGroup[checkGroup.length] = forma.elements[i];
			}
		}

		var checked = false;
		var finishedFirstGroup = false;
		var startFirstGroup = false;
		var existSecondGroup = false;

		if (checkGroup.length){
			for (var i=0; i<checkGroup.length; i++){
				var check = checkGroup[i];

				if (check.checked){
					if (!checked) {
						checked = true;
						startFirstGroup = true;
					}
				
					if (finishedFirstGroup) { 
						existSecondGroup = true;
						break;
					}
				}else{
					if (startFirstGroup && !finishedFirstGroup)
						finishedFirstGroup = true;
				}
			}
		}else {
			if (checkGroup.checked) checked = true;
		}

		if (!checked){
			alert("<?php echo ocpLabels("You have to choose at least one block");?>");
			return false;
		} else {
			if (existSecondGroup){
				alert("<?php echo ocpLabels("Blocks must be successive");?>");
				return false;
			}
		}

		return true;
	}
	function CloseMe(){
		parent.menuFrame.showSubmenuClose(false, "move frameset");
		this.ocpSized = true;
	}
	var newState = true;
	function selection(){
		var forma = document.getElementById("formObject");
		var checkGroup = new Array();
		for (var i=0; i< forma.elements.length; i++){
			if (forma.elements[i].name.indexOf("StBl_Id[]") == 0){
				checkGroup[checkGroup.length] = forma.elements[i];
			}
		}

		if (checkGroup.length){
			for (var i=0; i<checkGroup.length; i++)
				checkGroup[i].checked = newState;
		} else
			checkGroup.checked = newState;

		var span = document.getElementById("selectionSpan");
		if (newState) span.innerText = "<?php echo ocpLabels("Cancel selection");?>";
		else span.innerText = "<?php echo ocpLabels("Select all");?>";

		newState = !newState;
	}
</SCRIPT><?php	
	if ($BlokCount != 0){	
?><script src="/ocp/jscript/frameset.js"></script><?php	
	}	
?></HEAD><?php 
	if ($BlokCount == 0){ //jos nisu selektovani blokovi
		$blokovi = stranica_getAllBlok($straId);
?><BODY class="ocp_blokovi_body">
	<div id="ocp_blok_menu_1"> 
		<table class="ocp_blokovi_table"> 
			<tr> 
				<td class="ocp_blokovi_td"><img src="/ocp/img/opsti/razno/hint.gif" style="vertical-align: bottom; margin-left:3px;"> <b>1/3</b> <?php echo ocpLabels("Choose blocks you want to move or copy (blocks must be successive)");?></td> 
				<td class="ocp_blokovi_td" style="width: 90px; text-align:center;"><?php if (count($blokovi) > 0){ ?><a href="javascript:selection();" class="ocp_grupa_zatvori"><span id="selectionSpan"><?php echo ocpLabels("Select all");?></span></a><?php } ?></td> 
			</tr> 
		</table> 
	</div><?php	
		if (count($blokovi) > 0){ //ako ima blokova
?><div id="ocp_blok_menu_2">
	<table class="ocp_opcije_table">
	<form id="formObject" method="Post" action="/ocp/siteManager/lib/move/frameset.php?<?php echo utils_randomQS();?>" onSubmit="return check();">
			<input type="hidden" name="Stra_Id" value="<?php echo $straId;?>">
			<input type="hidden" name="Select" value="Select"><?php
			for ($i=0;$i<count($blokovi);$i++){
				$blok = $blokovi[$i];
				$tekst = $blok["Blok_Tekst"];
				if (utils_strlen($tekst) > 100) $tekst = utils_substr($tekst, 0, 100)."...";
				else if ($tekst == "null") $tekst = "";
	?><tr> 
		<td class="ocp_opcije_td" style="width:30px;text-align:right;"><span class="ocp_opcije_tekst1">#<?php echo $blok["StBl_Id"];?></span></td> 
		<td class="ocp_opcije_td" style="width:40px; text-align: center;"><img src="<?php echo str_replace("/srednji/", "/veliki/", $blok["TipB_SlikaUrl"])?>" width="30" height="25" title="<?php if ($blok["Blok_Share"] == "1") echo("deljeni: ".$blok["Blok_MetaNaziv"]); else echo($blok["TipB_Naziv"]);?>"></td>
		<td class="ocp_opcije_td"><span class="ocp_opcije_tekst2"><?php echo $tekst;?></span></td>
		<td class="ocp_opcije_td_forma" style="width:90px; text-align: center;">
			<input type="checkbox" name="StBl_Id[]" value="<?php echo $blok["StBl_Id"];?>">
		</td>
	</tr><?php
			}
	?><tr>
		<td colspan="4" class="ocp_opcije_td_forma" style="text-align: right;"><span style="width:90px; text-align: center;"><input name="submit" type="submit" class="ocp_dugme" value="<?php echo ocpLabels("Choose");?>"></span></td>
		</tr>
	</form>
	</table>
</div>
<?php		} else { //ako nema blokova
			?><?php require_once("../../../include/design/message.php"); ?>
			<?php echo message_info(ocpLabels("THERE ARE NO BLOCKS IN THIS PAGE"));?><?php	
		}			
?></body><?php
	} else { //vec su selectovani blokovi
		$strHref = "Stra_Id=" . utils_requestInt(getPVar("Stra_Id"));
		$tempArray = utils_requestInt(getPVar("StBl_Id"));
		for ($i=0; $i< count($tempArray); $i++){
			$strHref .= "&StBl_Id[]=".$tempArray[$i]; 
		}
?><frameset cols="300,*" frameborder="no" border="0" framespacing="0" cols="*"> 
    <frame name="move1" scrolling="no" noresize src="/ocp/siteManager/lib/move/tree.php?<?php echo utils_randomQS();?>&<?php echo $strHref;?>" marginwidth="0" marginheight="0" frameborder="NO" onload="if (!ocpSized){ adjustIFrameSize(parent, move1.window, 0, 226);} else {ocpSized = false;}">
	<frame name="move2" src="/ocp/siteManager/lib/move/move.php?<?php echo utils_randomQS();?>&<?php echo $strHref;?>" marginwidth="0" marginheight="0" frameborder="NO" noresize scrolling="auto">
</frameset>
<noframes><body bgcolor="#FFFFFF">
</body></noframes>
<?php
	
	}
?>
</HTML>