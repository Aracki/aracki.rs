<?php
	/*fja koja crta header iznad list tabele;
	moze imati: navigationChain, filter pretrage
	============================================*/
	function list_header($recordCount, $filter = NULL, $linkArr = NULL){
?>		<div id="ocp_blok_menu_1">
		  <table class="ocp_blokovi_table">
			<tr>
			  <td class="ocp_blokovi_td" style="padding-left: 6px;"><?php echo ocpLabels("Found items list");?>: <?php echo $recordCount;?></td>
			  <td class="ocp_blokovi_td" style="text-align: right;"><?php 
		  if (!is_null($linkArr))
			  for ($i=0; $i<count($linkArr); $i++){
				echo($linkArr[$i]."&nbsp;");
			  }
			  ?></td><?php
			  if (!is_null($filter)){?>
			  <td class="ocp_blokovi_td" style="text-align: right;">
					<span style="color: #C42E00;"><?php echo ocpLabels("Filter");?></span>
					<?php echo $filter;?>
				</td>
			  <?php	}
			?></tr>
		  </table>
		</div><?php
	}

	/*fja koja table header sa podatkom koja je 
	sort kolona i koji je smer sortiranja trenutni
	============================================*/
	function list_tableHeader($fields, $sortName, $direction, $sortFields = NULL, $noTools = NULL){
		if (is_null($sortFields)) $sortFields = $fields;
?><div id="stickyHeaderDiv" style="overflow:auto;"><table class="ocp_opcije_table">
    <tr>
      <td width="5%" class="ocp_opcije_td_header" style="white-space: nowrap;"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("NO");?></span></td><?php
		for ($i=0; $i<count($fields); $i++){
			if (isset($sortFields[$i]) && $sortFields[$i] != ""){
				$currentSort = ($sortName == $sortFields[$i]) ? 1 : 0;
			?>
			<td class="ocp_opcije_td_header" style="white-space: nowrap;<?php ($currentSort ? "border-bottom: 2px solid #C42E00;" : "") ?>">
				<span class="ocp_opcije_tekst3"><?php echo utils_toUpper(ocpLabels($fields[$i]));?></span>
				<img src="/ocp/img/blank.gif" width="3" border="0">
				<a href="javascript:sort('<?php echo $sortFields[$i];?>','asc')"><img border="0" src="/ocp/img/opsti/kontrole/strelica_filter_gore<?php echo($currentSort && ($direction == "asc") ? "_select" : ""); ?>.gif" title="<?php echo ocpLabels("Sort ascending");?>"></a><a href="javascript:sort('<?php echo $sortFields[$i];?>','desc')"><img border="0" src="/ocp/img/opsti/kontrole/strelica_filter_dole<?php echo($currentSort && ($direction == "desc") ? "_select" : ""); ?>.gif" title="<?php echo ocpLabels("Sort descending");?>"></a>
			</td>
			<?php
			} else {
			?><td class="ocp_opcije_td_header" style="white-space: nowrap;"><span class="ocp_opcije_tekst3"><?php echo utils_toUpper(ocpLabels($fields[$i]));?></span></td><?php
			}
		}
		if (!isset($noTools) || !utils_valid($noTools) || !$noTools){
?>    <td width="7%" class="ocp_opcije_td_header" style="white-space: nowrap;">&nbsp;</td>
<?php		} ?>
    </tr><?php
	}

	/*fja koja crta jedan red podataka u tabeli
	============================================*/
	function list_tableRow($index, $fields, $data, $edit, $del, $translate){
		?><tr style="cursor:pointer;" id="tr4" onClick="if (!pressed) {goForm('<?php echo $data["Id"];?>', 'iu');} pressed = false;">
      <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo ($index+1);?></span></td><?php
		for ($i=0; $i<count($fields); $i++){
			?><td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php 
			if (isset($data[$fields[$i]]) && utils_valid($data[$fields[$i]])){
				if ($translate){
					?><?php echo ocpLabels($data[$fields[$i]]) ;?><?php
				} else {
					?><?php echo ($data[$fields[$i]]) ;?>
		<?php	} ?></span>
	<?php	}	?>
			</td>
<?php	} ?>
	  <td class="ocp_opcije_td_forma" style="text-align: center;">
	<?php	if ($edit) { ?><img src="/ocp/img/opsti/kontrole/kontrola_edituj_objekat.gif" width="20" height="21" border="0" title="<?php echo ocpLabels("Edit object");?>" onClick="goForm('<?php echo $data["Id"]; ?>', 'iu');pressed=true;"><?php } ?><?php 
			if ($del) { ?><img src="/ocp/img/blank.gif" border="0"><img src="/ocp/img/opsti/kontrole/kontrola_obrisi_objekat.gif" width="20" height="21" title="<?php echo ocpLabels("Delete object");?>" onClick="goDelete('<?php echo $data["Id"];?>');pressed=true;"><?php } ?><img src="/ocp/img/blank.gif" border="0">
		</td>
    </tr><?php
	}

	/*fja koja crta jedan red podataka u tabeli
	============================================*/
	function list_tableRowPrevod($index, $fields, $data){
		?><tr style="cursor:pointer;" id="tr4" onClick="if (!pressed) {goForm('<?php echo $data["IdLabele"];?>', '<?php echo $data["IdPrevoda"];?>', 'iu');}	pressed = false;">
      <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo ($index+1);?></span></td><?php
		for ($i=0; $i<count($fields); $i++){
			?><td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php 
			if (utils_valid($data[$fields[$i]])){
				?><?php echo $data[$fields[$i]];?><?php
			}
			?></span></td><?php
		}
	  ?><td class="ocp_opcije_td_forma" style="text-align: center;"><img src="/ocp/img/opsti/kontrole/kontrola_edituj_objekat.gif" width="20" height="21" border="0" title="<?php echo ocpLabels("Edit object");?>" onClick="goForm('<?php echo $data["IdLabele"];?>', '<?php echo $data["IdPrevoda"];?>', 'iu');pressed=true;"></td>
    </tr><?php
	}

	/*fja koja crta jedan red podataka u tabeli
	============================================*/
	function list_tableRowDeletedObjects($index, $fields, $data){
		?><tr>
      <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo ($index+1);?></span></td><?php
		for ($i=0; $i<count($fields); $i++){
			?><td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php 
			if (utils_valid($data[$fields[$i]])){
				?><?php echo $data[$fields[$i]];?><?php
			}
			?></span></td><?php
		}
	  ?><td class="ocp_opcije_td_forma" style="text-align: center;"><img src="/ocp/img/opsti/kontrole/kontrola_edituj_objekat.gif" width="20" height="21" border="0" title="<?php echo ocpLabels("Restore object");?>" onClick="goRestore('<?php echo $data["Id"];?>');"><img src="/ocp/img/blank.gif" border="0"><img src="/ocp/img/opsti/kontrole/kontrola_obrisi_objekat.gif" width="20" height="21" title="<?php echo ocpLabels("Delete object");?>" onClick="goDelete('<?php echo $data["Id"];?>');"><img src="/ocp/img/blank.gif" border="0"></td>
    </tr><?php
	}

	/*fja koja crta footer tabele
	============================================*/
	function list_tableFooter(){
		?> </table>
</div><?php
	}
?>