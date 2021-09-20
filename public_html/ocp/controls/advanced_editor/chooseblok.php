<?php 
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../siteManager/lib/stranica.php");

	$straId = getGVar("Stra_Id");
	if (is_integer(strpos($straId, "Id="))){
		$straId = substr($straId, strpos($straId, "Id=") + 3);
	} else if (is_integer(strpos($straId, "/")) && strpos($straId, "/") == 0) {
		$straId = con_getValue("select Stra_Id from Stranica where Stra_Link='".$straId."'");
	} else {
		$straId = intval($straId);
	}
?><HTML>
<HEAD>
<link href="/ocp/css/opsti.css" rel="stylesheet" type="text/css">
<link href="/ocp/css/opcije.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title> OCP </title>
<SCRIPT>
	var IE = (navigator.userAgent.indexOf("Firefox") == -1) ? true : false;
	
	var checkedValue = "";

	function check(){
		var forma = document.getElementById("formObject");
		var checkGroup = forma.StBl_Id;
		var checked = false;

		var checkLength = checkGroup.length;
		if (isNaN(checkLength)){
			var check = checkGroup;
	
			if (check.checked){
				checked = true;
				checkedValue = check.value;
			}
		} else {
			for (var i=0; i<checkLength; i++){
				var check = checkGroup[i];
	
				if (check.checked){
					checked = true;
					checkedValue = check.value;
					break;
				}
			}
		}

		if (!checked){
			alert("<?php echo ocpLabels("You have to choose at least one block")?>");
			return false;
		}
		
		atTheEnd();
		return false;
	}

	function atTheEnd(){
		if (checkedValue != ""){
			var formField = '<?php echo utils_requestStr(getGVar("field"))?>';

			if (formField != "undefined" && formField != ""){
				eval("opener.document." + formField + ".value = '"+checkedValue+"'");
			} else {
				opener.document.forms[0].href.value = checkedValue;
			}
			this.close();
		}
	}
</SCRIPT>
</HEAD>
<BODY class="ocp_blokovi_body">
	<div id="ocp_blok_menu_1"> 
		<table class="ocp_blokovi_table"> 
			<tr> 
				<td class="ocp_blokovi_td"><?php echo ocpLabels("Choose block from page")?></td> 
			</tr> 
		</table> 
	</div><?php	
	

	$blokovi = stranica_getAllBlok(utils_requestInt($straId));

	if (count($blokovi) > 0){ //ako ima blokova
?><div id="ocp_blok_menu_2">
	<table class="ocp_opcije_table">
	<form id="formObject" method="Post" action="" onSubmit="return check();"><?php
		for ($i =0; $i<count($blokovi); $i++){
			$blok = $blokovi[$i];
			$tekst = $blok["Blok_Tekst"];
			if (utils_strlen($tekst) > 100) $tekst = utils_substr($tekst, 0, 100) . "...";
			else if (!utils_valid($tekst)) $tekst = "";
	?><tr> 
		<td class="ocp_opcije_td" style="width:30px;text-align:right;">
			<span class="ocp_opcije_tekst1">#<?php echo $blok["StBl_Id"]?></span>
		</td> 
		<td class="ocp_opcije_td" style="width:40px; text-align: center;">
			<img src="<?php echo str_replace("/srednji/", "/veliki/", $blok["TipB_SlikaUrl"])?>" width="30" height="25" title="<?php if ($blok["Blok_Share"] == "1") echo("deljeni: ".$blok["Blok_MetaNaziv"]); else echo($blok["TipB_Naziv"]);?>">
		</td>
		<td class="ocp_opcije_td"><span class="ocp_opcije_tekst2"><?php echo $tekst?></span></td>
		<td class="ocp_opcije_td_forma" style="width:90px;">
			<input type="radio" name="StBl_Id" value="<?php echo utils_getStraLink($straId) . "#" .$blok["StBl_Id"]?>">
		</td>
	</tr><?php
		}
	?><tr>
		<td colspan="3" class="ocp_opcije_td_forma">&nbsp;</td>
		<td class="ocp_opcije_td_forma"><span class="ocp_dugme_td"><input name="submit" type="submit" class="ocp_dugme" value="<?php echo ocpLabels("Choose")?>"></span></td>
		</tr>
	</form>
	</table>
</div><?php
	} else { //ako nema blokova
		require_once("../../include/design/message.php");
		echo message_info(ocpLabels("THERE ARE NO BLOCKS IN THIS PAGE"));
	}	
?>
</body>
</HTML>