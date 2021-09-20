<?php
	require_once("../include/connect.php");
	require_once("../include/session.php");
	require_once("../siteManager/lib/verzija.php");
	require_once("../include/xml.php");
	require_once("../include/xml_tools.php");
	require_once("../include/design/button.php");

	$verz_id = utils_requestInt(getGVar("Verz_Id"));
	$sortName = "";
	$direction = "";
	$new_counter = 0;
	$filter_text = "";

	if ($verz_id == 0){
		$verz_id = utils_requestInt(getPVar("verz_id"));
		$sortName = utils_requestStr(getPVar("sortName"));
		$direction = utils_requestStr(getPVar("direction"));
		$new_counter = intval(utils_requestInt(getPVar("new_label_counter")));
		$filter_text = utils_requestStr(getPVar("filter_text"));
	}
	$labels = verzija_readLabels($verz_id);
	
	$action = utils_requestStr(getPVar("action"));
	if ($action == "delete"){
		foreach ($labels as $key=>$value){
			if (utils_requestInt(getPVar($key . "_label_delete")) == 1){
				unset($labels[$key]);
			}
		}
		verzija_saveLabels($verz_id, $labels);
	} else if ($action == "add"){
		$new_counter += 1;
//utils_dump($action." ".$new_counter);
	} 
	
	if (utils_valid($filter_text)){
		foreach ($labels as $key=>$value){
			if (!is_integer(strpos(utils_toLower($key), utils_toLower($filter_text))) && 
				!is_integer(strpos(utils_toLower($value), utils_toLower($filter_text)))){
				unset($labels[$key]);
			}
		}
	}

	if (utils_valid($sortName) && utils_valid($direction)){
		if ($sortName == "Text"){
			if ($direction == "asc") asort($labels);
			else arsort($labels);
		} else {
			if ($direction == "asc") ksort($labels);
			else krsort($labels);
		}
	}

	?><script src="/ocp/jscript/prototype.js" type="text/javascript"></script>
	<input type="hidden" name="verz_id" value="<?php echo $verz_id?>"/>
	<input type="hidden" name="sortName" value="<?php echo $sortName?>"/>
	<input type="hidden" name="direction" value="<?php echo $direction?>"/>
	<input type="hidden" name="new_label_counter" value="<?php echo $new_counter?>"/>
	<table class="ocp_opcije_table" border="0">
		<tr>
			<td rowspan="<?php echo count($labels)+3+$new_counter ?>" class="ocp_opcije_td_ikona">
				<img src="/ocp/img/opsti/opcije/ikone/ikona_labele.gif" title="<?php echo ocpLabels("LABELS")?>"/>
			</td>
			<td colspan="3" class="ocp_opcije_td_naslov"><?php echo ocpLabels("LABELS")?></td>
		</tr>
		<tr>
			<td colspan="3">
				<!-- <div id="ocp_blok_menu_1"> -->
					<table class="ocp_blokovi_table" border="0">
						<tr>
							<td class="ocp_blokovi_td" align="right" style="padding-left: 6px;" style="width:40%;">
								<input type="text" name="filter_text" value="<?php echo $filter_text?>" class="ocp_forma" style="width:80%;"/>
							</td>
							<td class="ocp_blokovi_td" style="text-align: right;width:50%;">
							<?php 
								if (!utils_valid($filter_text))	
									button_html(ocpLabels("Delete labels"), "delete_labels();");
								button_html(ocpLabels("Add label"), "add_label();");
								button_html(ocpLabels("Filter labels"), "filter_labels();");
							?></td>
						</tr>
					</table>
				<!-- </div> -->
			</td>
		</tr><?php

		if (count($labels) > 0){
			?>
		<tr>
			<td valign="top" style="margin: 0px; padding: 0px;">
				<table width="100%" cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px;">
					<tr>
						<td class="ocp_opcije_td_header" style="white-space: nowrap;">
							<span class="ocp_opcije_tekst3"><?php echo ocpLabels("Label")?></span><img src="/ocp/img/blank.gif" width="3" border="0"/><a href="javascript:sort('Label','asc')"><img border="0" height="7" src="/ocp/img/opsti/kontrole/strelica_filter_gore.gif" title="<?php echo ocpLabels("Sort ascending")?>"/></a><a href="javascript:sort('Label','desc')"><img border="0" height="7" src="/ocp/img/opsti/kontrole/strelica_filter_dole.gif" title="<?php echo ocpLabels("Sort descending")?>"/></a>
						</td>
					</tr>
				</table>
			</td>
			<td valign="top" style="margin: 0px; padding: 0px;">
				<table width="100%" cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px;">
					<tr>
						<td class="ocp_opcije_td_header" style="white-space: nowrap;">
							<span class="ocp_opcije_tekst3"><?php echo ocpLabels("Text")?></span><img src="/ocp/img/blank.gif" width="3" border="0"/><a href="javascript:sort('Text','asc')"><img border="0" height="7" src="/ocp/img/opsti/kontrole/strelica_filter_gore.gif" title="<?php echo ocpLabels("Sort ascending")?>"/></a><a href="javascript:sort('Text','desc')"><img border="0" height="7" src="/ocp/img/opsti/kontrole/strelica_filter_dole.gif" title="<?php echo ocpLabels("Sort descending")?>"/></a>
						</td>
					</tr>
				</table>
			</td>
			<td valign="top" style="margin: 0px; padding: 0px;">
				<table width="100%" cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px;">
					<tr>
						<td class="ocp_opcije_td_header" style="white-space: nowrap;">
							<span class="ocp_opcije_tekst3"><?php echo ocpLabels("Choose")?></span>
						</td>
					</tr>
				</table>
			</td>
		</tr><?php
		}
		foreach ($labels as $key=>$value){
			?><tr>
				<td class="ocp_opcije_td" style="width:22%">
					<span class="ocp_opcije_tekst1"><?php echo $key?></span>
				</td>
				<td class="ocp_opcije_td"><?php
			if (utils_strlen($value)>70){
				?><textarea name="<?php echo $key?>" cols="30" rows="3" class="ocp_forma" style="width:100%"><?php echo $value?></textarea><?php
			} else {
				?><input type="text" value="<?php echo $value?>" name="<?php echo $key?>" class="ocp_forma" style="width:100%"/><?php
			}
				?></td>
				<td class="ocp_opcije_td_forma" width="15px;">
					<input type="checkbox" value="1" name="<?php echo $key?>_label_delete"/>
				</td>
			</tr><?php
		}
		
		for ($i=0; $i<$new_counter; $i++){
			$key = utils_requestStr(getPVar("new_key_".$i));
			$value = utils_requestStr(getPVar("new_value_".$i), 0, 1);
		?><tr>
			<td class="ocp_opcije_td" style="width:22%">
				<span class="ocp_opcije_tekst1">
					<input type="text" value="<?php echo $key?>" name="new_key_<?php echo $i?>" class="ocp_forma" style="width:100%"/>
				</span>
			</td>
			<td class="ocp_opcije_td"><?php
			if (utils_strlen($value)>70){
				?><textarea name="new_value_<?php echo $i?>" cols="30" rows="3" class="ocp_forma" style="width:100%"><?php echo $value?></textarea><?php
			} else {
				?><input type="text" value="<?php echo $value?>" name="new_value_<?php echo $i?>" class="ocp_forma" style="width:100%"/><?php
			}
				?></td>
			<td class="ocp_opcije_td_forma" width="15px;">
				
			</td>
		</tr><?php
		}

		?>
	</table><?php

?><script type="text/javascript">
		function sort(sortName, direction){
			var targetDiv = "verz_labels";
			var element = $(targetDiv);

			var url = "/ocp/siteManager/labeleedit.php";	
			$("formObject").sortName.value = sortName;
			$("formObject").direction.value = direction;
			var pars = Form.serialize($("formObject"));

			var ajax = new Ajax.Updater(
				{success: targetDiv},
				url,
				{	method: 'post', parameters: pars, asynchronous:false, evalScripts:true,
					onLoading:function(request, json){}}
			);
		}

		function delete_labels(){
			var targetDiv = "verz_labels";
			var element = $(targetDiv);

			var url = "/ocp/siteManager/labeleedit.php";	
			var pars = Form.serialize($("formObject")) + "&action=delete";

			var ajax = new Ajax.Updater(
				{success: targetDiv},
				url,
				{	method: 'post', parameters: pars, asynchronous:false, evalScripts:true,
					onLoading:function(request, json){}}
			);
		}

		function add_label(){
			var targetDiv = "verz_labels";
			var element = $(targetDiv);

			var url = "/ocp/siteManager/labeleedit.php";	
			var pars = Form.serialize($("formObject")) + "&action=add";

			var ajax = new Ajax.Updater(
				{success: targetDiv},
				url,
				{	method: 'post', parameters: pars, asynchronous:false, evalScripts:true,
					onLoading:function(request, json){}}
			);
		}

		function filter_labels(){
			var targetDiv = "verz_labels";
			var element = $(targetDiv);

			var url = "/ocp/siteManager/labeleedit.php";	
			var pars = Form.serialize($("formObject"));

			var ajax = new Ajax.Updater(
				{success: targetDiv},
				url,
				{	method: 'post', parameters: pars, asynchronous:false, evalScripts:true,
					onLoading:function(request, json){}}
			);
		}
	</script>