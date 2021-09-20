<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");

?><?php  
	$type = utils_requestStr(getGVar("type"));
	$fieldValue = utils_requestInt(getGVar("fieldValue"));
	$fieldValue = ($fieldValue == 0) ? utils_requestInt(getGVar("amp;fieldValue")) : $fieldValue;
	
	$label = "";

	if ($fieldValue != 0){
		if ($type == "verzija")
			$label = con_getValue("select Verz_Naziv from Verzija where Verz_Id=".$fieldValue);
		else if ($type == "sekcija")
			$label = con_getValue("select Sekc_Naziv from Sekcija where Sekc_Id=".$fieldValue);
		else if ($type == "stranica"){
			$label = con_getValue("select Stra_Naziv from Stranica where Stra_Id=".$fieldValue);
		}
	}
	
?> <span class="ocp_opcije_tekst1"><?php echo $label?></span>