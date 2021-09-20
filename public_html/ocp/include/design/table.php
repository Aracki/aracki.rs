<?php
	/*Tabela sa informacijama, infoArray je niz labela - vrednost 
	=============================================================*/
	function table_info($infoArray){
		$table = '<table class="ocp_info_table">';
		$table .= '<tr>';
		$table .= '<td class="ocp_info_td">';
		$first = true;
		for ($i=0; $i<count($infoArray); $i+=2){
			if (!$first) $table .= "<br>";
			else $first = false;
			$table .= $infoArray[$i] .'&nbsp;<span class="ocp_info_td_vrednost">'.$infoArray[$i+1].'</span>';
		}
		$table .= '</td></tr></table>';

		echo($table);
	}

	/*Tabela grupa, div je true ako su napredne
	==========================================*/
	function table_group($title, $div){
		$table = '<table class="ocp_naslovgrupe_table">'; 
		$table .= '<tr><td class="ocp_naslovgrupe_td_a"';
		if ($div)
			$table .= ' style=" cursor:pointer;" onclick="alternateAdvanceDiv(\'advancedDivId\', \''.ocpLabels("open").'\', \''.ocpLabels("close").'\');"';
		$table .= '>'.$title.'</td>';
		if ($div)
			$table .= '<td class="ocp_naslovgrupe_td_b" id="advancedDivId" style="cursor:pointer" onclick="alternateAdvanceDiv(\'advancedDivId\', \''.ocpLabels("open").'\', \''.ocpLabels("close").'\');"><a href="#" class="ocp_grupa_zatvori">'.ocpLabels("open").'<img src="/ocp/img/opsti/kontrole/strelica_nadole.gif" hspace="5" border="0"></a></td>';
		$table .= '</tr></table>';
		echo($table);
	}

	/*Tabela neke opcije, sa ikonom, i nizom tr-ova, pravih kontrola
	===============================================================*/
	function table_option($title, $icon, $inputs, $colspan = 2){
		$table = '<table class="ocp_opcije_table"><tr>';
		$table .= '<td rowspan="'.(count($inputs)+1).'" class="ocp_opcije_td_ikona"><img src="/ocp/img/opsti/opcije/ikone/'.$icon.'.gif" title="'.$title.'"></td>';
		$table .= '<td colspan="'.(utils_valid($colspan) ? $colspan : "2").'" class="ocp_opcije_td_naslov">'.$title.'</td></tr>';
		$keys = array_keys($inputs);
		for ($i=0; $i<count($keys); $i++)
			$table .= $inputs[$keys[$i]];
		$table .= '</table>';
		echo($table);
	}

	/*Opcija text polje
	==================================*/
	function table_option_text($label, $name, $value, $necessary = false, $colspan = 1){
		$className = utils_valid($colspan) && ($colspan > 1) ? "ocp_opcije_td_forma" : "ocp_opcije_td";
		$tr = '<tr><td class="'.$className.'" style="width:22%"><span class="ocp_opcije_tekst1">'.$label.(($necessary) ? '</span><span class="ocp_opcije_obavezno">*</span>' : '</span>').'</td>';
		$tr .= '<td class="'.$className.'"><input type="text" value="'.$value.'" name="'.$name.'" class="ocp_forma" style="width:100%"></td></tr>';

		return $tr;
	}

	/*Opcija label polje
	==================================*/
	function table_option_label($labelArr, $name, $value){
		$tr = '<tr>';
		if (is_array($labelArr))
			for ($i=0; $i<count($labelArr); $i++)
				$tr .= '<td class="ocp_opcije_td"><span class="ocp_opcije_tekst1">'.$labelArr[$i].'</span></td>';
		$tr .= '<td class="ocp_opcije_td"><span class="ocp_opcije_tekst2">'.$value.'</span><input type="hidden" name="'.$name.'" value="'.$value.'"></td></tr>';

		return $tr;
	}

	/*Opcija intLink polje
	==================================*/
	function table_option_intLink($label, $name, $value, $necessary = false, $straId, $rootFolder, $colspan = 1){
		$className = utils_valid($colspan) && ($colspan > 1) ? "ocp_opcije_td_forma" : "ocp_opcije_td";
		$tr = '<tr><td class="'.$className.'" style="width:22%" '.(utils_valid($colspan) ? 'colspan="'.$colspan.'"' : '').'><span class="ocp_opcije_tekst1">' . $label . '</span>' . (($necessary) ? '<span class="ocp_opcije_obavezno">*</span>' : '</span>').'</td>';
		$tr .= '<td class="'.$className.'">';
		$tr .= '<table class="ocp_uni_table"><tr><td class="ocp_dugmici_td_levi">';
		$tr .= '<input type="text" value="'.$value.'" name="'.$name.'" class="ocp_forma" style="width:100%">';
		$tr .= '</td><td class="ocp_dugmici_td_desni_3">';
		$tr .= '<a href="javascript: void(0);" onClick="window.open(\'/ocp/admin/siteManager/intlink.php?'.utils_randomQS().'&Id='.$straId.'&field=formObject.'.$name.'\', \'intLink\', \'top=100, left=150, width=600, height=260, scrollbars=no, resizable=no, status=yes\'); return false;">';
		$tr .= '<img src="/ocp/img/opsti/kontrole/kontrola_browse_page.gif" class="ocp_kontrola" title="'.ocpLabels("Create link on OCP page").'"/></a>';
		$tr .= '<a href="javascript:x = window.open(\'/ocp/controls/fileControl/frameset.php?'.utils_randomQS().'&root='.$rootFolder.'&field=formObject.'.$name.'\',\'fileControl\',\'top=100, left=50, width=760, height=560, scrollbars=yes, resizable=yes, status=yes\'); x.focus();"><img src="/ocp/img/opsti/kontrole/kontrola_browse.gif" class="ocp_kontrola" title="'.ocpLabels("Browse server").'"/></a>';
		$tr .= '<a href="javascript: void(0);" onClick="urlCont=formObject.'.$name.';window.open(urlCont.value, \'\', \'width=500, height=400, resizable, scrollbars\');"><img src="/ocp/img/opsti/kontrole/kontrola_preview.gif" class="ocp_kontrola" title="'.ocpLabels("Selected link preview").'"/></a>';
		$tr .= '</td></tr></table></td></tr>';

		return $tr;
	}

	/*Opcija text datum polje
	==================================*/
	function table_option_textDate($label, $name, $value, $necessary = false, $colspan = 1){
		$dd = $mm = $yyyy = $time = "";
		if (utils_valid($value)) {
			$tmp = strtotime($value);
			$dd = date("d", $tmp);
			$mm = date("m", $tmp);
			$yyyy = date("Y", $tmp);
			$time = date("H:i:s", $tmp);
		}
		$className = utils_valid($colspan) && ($colspan > 1) ? "ocp_opcije_td_forma" : "ocp_opcije_td";
		$tr = '<tr><td class="'.$className.'" style="width:22%" '.(utils_valid($colspan) ? 'colspan="'.$colspan.'"' : '').'><span class="ocp_opcije_tekst1">'.$label.'&nbsp;(dd/mm/yyyy)</span>';
		$tr .= '</td>';
		$tr .= '<td class="'.$className.'">';
		$tr .= '<input type="text" name="'.$name.'_dd" value="'.$dd.'" class="ocp_forma" style="width:28px;">';
        $tr .= '&nbsp;/&nbsp;';
        $tr .= '<input type="text" name="'.$name.'_mm" value="'.$mm.'" class="ocp_forma" style="width:28px;">';
        $tr .= '&nbsp;/&nbsp;';
        $tr .= '<input type="text" name="'.$name.'_yyyy" value="'.$yyyy.'" class="ocp_forma" style="width:40px;">&nbsp;';
		$tr .= '<input type="text" name="'.$name.'_time" value="'.$time.'" class="ocp_forma" style="width:60px;">&nbsp;';
        $tr .= '<a href="#" onClick="openDateFlash(event,\'formObject.'.$name.'\');return false;"><img src="/ocp/img/opsti/kontrole/kontrola_kalendar.gif" class="ocp_kontrola" title="'.ocpLabels("Calendar").'"></a>';
		$tr .= '</td></tr>';

		return $tr;
	}

	/*Opcija kljucne reci 
	==================================*/
	function table_option_textarea($label, $name, $value, $necessary = false, $colspan = 1){
		$className = utils_valid($colspan) && ($colspan > 1) ? "ocp_opcije_td_forma" : "ocp_opcije_td";
		$tr = '<tr><td class="'.$className.'" style="width:22%" '.(utils_valid($colspan) ? 'colspan="'.$colspan.'"' : '').'><span class="ocp_opcije_tekst1">'.$label.'</span></td>';
		$tr .= '<td class="'.$className.'"><textarea name="'.$name.'" cols="30" rows="3" class="ocp_forma" style="width:100%">'.$value.'</textarea></td></tr>';

		return $tr;
	}

	/*Opcija radio
	==================================*/
	function table_option_radio($label, $name, $value, $arr, $necessary = false, $colspan = 1){
		$className = utils_valid($colspan) && ($colspan > 1) ? "ocp_opcije_td_forma" : "ocp_opcije_td";
		$tr = '<tr><td class="'.$className.'" style="width:22%" '.(utils_valid($colspan) ? 'colspan="'.$colspan.'"' : '').'><span class="ocp_opcije_tekst1">'.$label.'</span></td>';
		$tr .= '<td class="'.$className.'">';
		for ($i=0; $i<count($arr); $i+=2){
			$tr .= '<span class="ocp_opcije_tekst2">' . $arr[$i+1] . '</span>&nbsp;<input type="radio" value="'.$arr[$i].'" name="'.$name.'" ';
			if ($arr[$i] == 1)
				$tr .= (($value == 1) ? "checked" : "" ) .  '>'; 
			else 
				$tr .= (($value == 0) ? "checked" : "" ) . '>'; 
		}
		$tr .= '</td></tr>';
		return $tr;
	}

	/*Opcija checkbox
	==================================*/
	function table_option_checkbox($label, $name, $value, $arr, $necessary = false, $colspan = 1){
		$className = utils_valid($colspan) && ($colspan > 1) ? "ocp_opcije_td_forma" : "ocp_opcije_td";
		$tr = '<tr><td class="'.$className.'" style="width:22%" '.(utils_valid($colspan) ? 'colspan="'.$colspan.'"' : '').'><span class="ocp_opcije_tekst1">'.$label.'</span></td>';
		$tr .= '<td class="'.$className.'">';
		for ($i=0; $i<count($arr); $i+=2){
			$tr .= '<span class="ocp_opcije_tekst2">' . $arr[$i+1] . '</span>&nbsp;<input type="checkbox" value="'.$arr[$i].'" name="'.$name.'" ';
			if ($arr[$i] == "1")
				$tr .= (($value == "1") ? "checked" : "" ) .  '>'; 
			else 
				$tr .= (($value == "0") ? "checked" : "" ) .  '>'; 
		}
		$tr .= '</td></tr>';
		return $tr;
	}

	/*Opcija select lista
	==================================*/
	function table_option_select($label, $name, $arr, $arr_val, $arr_lab, $value, $necessary = 0, $defaultValue = NULL, $colspan=1){
		$className = utils_valid($colspan) && ($colspan > 1) ? "ocp_opcije_td_forma" : "ocp_opcije_td";
		$tr = '<tr><td class="'.$className.'" style="width:22%"><span class="ocp_opcije_tekst1">'.$label.(($necessary) ? '</span><span class="ocp_opcije_obavezno">*</span>' : '</span>').'</td>';
		$tr .= '<td class="'.$className.'">';
		$tr .= '<select name="'.$name.'" class="ocp_forma" style="width:100%">';
		if (!is_null($defaultValue))
			$tr .= '<option value="">' .$defaultValue. '</option>';
		for ($k=0; $k<count($arr); $k++){
			$next = $arr[$k];
			$tr .= '<option value="'.$next[$arr_val].'" ' . (($next[$arr_val] == $value) ? 'selected' : '') . '>'.$next[$arr_lab].'</option>';
		}
		$tr .= '</select></td>';
		$tr .= '</tr>';

		return $tr;
	}

	/*Opcija text polje
	==================================*/
	function table_option_submit($labelSubmit, $labelCancel){
		$table = '<table width="100%"><tr><td height="40" align="center" class="ocp_text">';
		$table .= '<input type="submit" name="submit" value="'.$labelSubmit.'" class="ocp_dugme">';
		$table .= '<input type="button" value="'.$labelCancel.'" class="ocp_dugme" onclick="parent.menuFrame.defaultPage();"></td>';
		$table .= '</tr></table> ';

		echo($table);
	}

?>