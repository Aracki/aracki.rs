<?php
	function home_welcomeNote(){
		?><div id="ocp_blok_menu_1">
  <table class="ocp_blokovi_table">
    <tr>
      <td class="ocp_home_dobrodosli"><?php echo ocpLabels("Welcome")?>, <?php echo getSVar("ocpUsername")?>!</td>
    </tr>
  </table>
</div><?php
	}

	function home_shortcuts(){
		$disable = getSVar("ocpDisable");

		?><table class="ocp_opcije_table" style="border-top: 1px solid #b5b5b5;">
    <tr>
      <td class="ocp_opcije_td ocp_home_links_holder" style="padding: 20px;"><?php
		if (substr_count($disable, "siteManager") == 0){	
		?>
		<a href="javascript:openPopupWindow('/ocp/home_popup.php?<?php echo utils_randomQS();?>&type=block');" class="ocp_home_v_link"><img src="/ocp/img/home/dodaj_blok.gif" width="29" height="27" align="absbottom"><?php echo ocpLabels("Add new block")?></a> | 
		<a href="javascript:openPopupWindow('/ocp/home_popup.php?<?php echo utils_randomQS();?>&type=page');" class="ocp_home_v_link"><img src="/ocp/img/home/dodaj_stranu.gif" width="24" height="27" align="absmiddle"><?php echo ocpLabels("Add new page")?></a> | <?php
		}
		
		if (substr_count($disable, "objectManager") == 0){
		?><a href="javascript:openPopupWindow('/ocp/home_popup.php?<?php echo utils_randomQS();?>&type=object');" class="ocp_home_v_link"><img src="/ocp/img/home/dodaj_objekat.gif" width="23" height="26" align="absmiddle"><?php echo ocpLabels("Add new object")?></a><?php
		}	
		?></td>
    </tr>
  </table><?php
	}


	function home_lastEditedHeader($type){
		$disable = getSVar("ocpDisable");

		if (substr_count($disable, "siteManager") != 0 && $type == "page") return;	
		if (substr_count($disable, "objectManager") != 0 && $type != "page") return;	
		

		?><table class="ocp_opcije_table"  style="border-bottom: 0; border-right: 1px solid #ccc;">
    <tr>
      <td width="53" rowspan="9" class="ocp_opcije_td_ikona"><img src="/ocp/img/home/<?php if ($type == "page") { echo "poslednje_editovane_strane"; } else { echo "poslednje_editovani_objekti"; } ?>.gif" width="39" height="33"></td>
      <td colspan="5" class="ocp_opcije_td_naslov"><?php if ($type == "page"){ echo ocpLabels("LAST EDITED PAGES"); } else { echo ocpLabels("LAST EDITED OBJECTS"); } ?></td>
    </tr>
    <tr><?php
		if ($type == "page"){
		?><td width="190" class="ocp_opcije_td_header" style="white-space: nowrap;"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("PAGE")?></span></td>
      <td width="368" class="ocp_opcije_td_header" style="white-space: nowrap;"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("PATH")?></span></td><?php
		} else {
		?><td width="190" class="ocp_opcije_td_header" style="white-space: nowrap;"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("OBJECT")?></span></td>
      <td width="368" class="ocp_opcije_td_header" style="white-space: nowrap;"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("GROUP")?></span></td><?php
		}
	?><td width="165" class="ocp_opcije_td_header" style="white-space: nowrap;"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("DATE")?></span></td>
      <td width="133" class="ocp_opcije_td_header" style="white-space: nowrap;"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("USER")?></span></td>
      <td width="50" class="ocp_opcije_td_header" style="white-space: nowrap;">&nbsp;</td>
    </tr><?php

	}

	function home_lastEditedRow($logLine, $type){
		$disable = getSVar("ocpDisable");

		if (substr_count($disable, "siteManager") != 0 && $type == "page") return;	
		if (substr_count($disable, "objectManager") != 0 && $type != "page") return;	

		$TR = getSvar('ocpTR');

	?><tr style="cursor:pointer" <?php
		if ($type == "page") {
			if (intval(stranica_getRight($logLine["Stra_Id"])) >= 2){
			?>onClick="parent.parent.leftFrame.openMenuInLowerFrame('Stranica', <?php echo $logLine["Stra_Id"]?>, 'Sekcija', <?php echo $logLine["Stra_Sekc_Id"]?>, <?php echo stranica_getRight($logLine["Stra_Id"])?>, null, null, null);"<?php 
			}
		} else {
			if (intval($TR[$logLine["Id"]]) >= 2){
			?>onClick="parent.parent.leftFrame.openMenuInLowerFrame(null, 'ocpId=<?php echo $logLine["Id"]?>&objId=<?php echo $logLine["IdObjekta"]?>', null, null, null, null, null, null, 'obj');"<?php 
			}
		} ?>><?php
		if ($type == "page"){
		?> <td class="ocp_opcije_td ocp_home_naziv"><span class="ocp_opcije_tekst1"> <img src="/ocp/img/opsti/opcije/ikone/stranica.gif" width="17" height="18" align="absmiddle"><strong><?php echo $logLine["Stra_Naziv"]?></strong></span></td>
      <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo stranica_getPath($logLine["Stra_Id"]); ?></span></td><?php
		} else {
		?><td class="ocp_opcije_td ocp_home_naziv"><span class="ocp_opcije_tekst1"><img src="/ocp/img/opsti/opcije/ikone/blok.gif" align="absmiddle"><strong><?php echo $logLine["IdenObjekta"]?></strong></span></td>
      <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $logLine["Grupa"]?><?php echo $logLine["Labela"]?></span></td><?php
		}
	?><td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo datetime_format4Database($logLine["Datum"]);?></span></td>
      <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $logLine["UserName"]?></span></td>
      <td class="ocp_opcije_td_forma" style="text-align: center;"><?php
		if ($type == "page") {
			if (intval(stranica_getRight($logLine["Stra_Id"])) >= 2){
			?><img src="/ocp/img/opsti/kontrole/kontrola_edituj_stranu.gif" width="20" height="21" border="0" style="cursor:pointer" title="<?php echo ocpLabels("Edit object")?>" onClick="parent.parent.leftFrame.openMenuInLowerFrame('Stranica', <?php echo $logLine["Stra_Id"]?>,  'Sekcija', <?php echo $logLine["Stra_Sekc_Id"]?>, <?php echo stranica_getRight($logLine["Stra_Id"])?>, null, null, null);"><?php 
			}
		} else {
			if (intval($TR[$logLine["Id"]]) >= 2){
			?><img src="/ocp/img/opsti/kontrole/kontrola_edituj_objekat.gif" width="20" height="21" border="0" style="cursor:pointer" title="<?php echo ocpLabels("Edit object")?>" onClick="parent.parent.leftFrame.openMenuInLowerFrame(null, 'ocpId=<?php echo $logLine["Id"]?>&objId=<?php echo $logLine["IdObjekta"]?>', null, null, null, null, null, null, 'obj');"><?php 
			}
		} ?></td>
    </tr><?php
	}

	function home_lastLoginHeader($type){
		if ($type == "login"){
?><table class="ocp_opcije_table">
    <tr>
      <td width="53" rowspan="13" class="ocp_opcije_td_ikona"><img src="/ocp/img/home/poslednja_logovanja.gif" width="39" height="33"></td>
      <td colspan="2" class="ocp_opcije_td_naslov"><?php echo ocpLabels("LOGS")?></td>
    </tr>
    <tr>
      <td colspan="2" class="ocp_opcije_td_header" style="white-space: nowrap;"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("LAST LOGINS")?></span></td>
    </tr><?php
		} else {
?><tr>
      <td colspan="2" class="ocp_opcije_td_header" style="white-space: nowrap;"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("LOG RECORDS")?></span></td>
    </tr><?php	
		}
	}

	function home_lastLoginRow($logLine){
	?><tr>
      <td width="653" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $logLine["UserName"]?></span></td>
      <td width="265" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo datetime_format4Database($logLine["Datum"])?></span></td>
    </tr><?php
	}

	function home_logRecordsRow($logsCount){
	?><tr>
      <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("activity log")?></span></td>
      <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1">
		<?php if (isset($max_no_logs) && $logsCount >= $max_no_logs) echo("<span class='ocp_opcije_obavezno'>"); ?>
		<?php echo $logsCount?>
		<?php if (isset($max_no_logs) && $logsCount >= $max_no_logs) echo("</span>"); ?></span></td>
    </tr><?php
		$disable = getSVar("ocpDisable");
	}
?>