<?php
	$brojKolona = 5;

	function tipblok_getChooseMenuHtml($type, $straURL, $straId){
		$html = "";
		$html .= '<div id="ocp_blok_menu_1">';
		$html .= '<table class="ocp_blokovi_table_0">';
		$html .= '<tr>';
		
		$link = "";
		if (!is_null($straURL)){
			$link = ($type != "static") ? '/ocp/siteManager/block_list.php?'.utils_randomQS().'&type=static&stranica='.$straURL.'&Id='.$straId : '' ;
			$html .= tipblok_getChooseMenuButtonHtml($link, "staticki", ocpLabels("Static blocks"));
			$html .= '<td width="11"><img src="/ocp/img/gornji_2/dugmici/crtka.gif" width="2" height="18" class="ocp_blokovi_crtka"></td> ';

			$link = ($type != "dinamic") ? '/ocp/siteManager/block_list.php?'.utils_randomQS().'&type=dinamic&stranica='.$straURL.'&Id='.$straId : '' ;
			$html .= tipblok_getChooseMenuButtonHtml($link, "dinamicki", ocpLabels("Dynamic blocks"));
			$html .= '<td width="11"><img src="/ocp/img/gornji_2/dugmici/crtka.gif" width="2" height="18" class="ocp_blokovi_crtka"></td> ';

			$link = ($type != "share") ? '/ocp/siteManager/block_list.php?'.utils_randomQS().'&type=share&stranica='.$straURL.'&Id='.$straId : '' ;
			$html .= tipblok_getChooseMenuButtonHtml($link, "deljeni", ocpLabels("Shared blocks"));
		} else {
			$link = ($type != "static") ? '/ocp/admin/siteManager/blockTypes_list.php?'.utils_randomQS().'&type=static' : '' ;
			$html .= tipblok_getChooseMenuButtonHtml($link, "staticki", ocpLabels("Static blocks"));
			$html .= '<td width="11"><img src="/ocp/img/gornji_2/dugmici/crtka.gif" width="2" height="18" class="ocp_blokovi_crtka"></td> ';

			$link = ($type != "dinamic") ? '/ocp/admin/siteManager/blockTypes_list.php?'.utils_randomQS().'&type=dinamic' : '' ;
			$html .= tipblok_getChooseMenuButtonHtml($link, "dinamicki", ocpLabels("Dynamic blocks"));
			$html .= '<td width="11"><img src="/ocp/img/gornji_2/dugmici/crtka.gif" width="2" height="18" class="ocp_blokovi_crtka"></td> ';

			$link = ($type != "share") ? '/ocp/admin/siteManager/blockTypes_list.php?'.utils_randomQS().'&type=share' : '' ;
			$html .= tipblok_getChooseMenuButtonHtml($link, "deljeni", ocpLabels("Shared blocks"));
		}

		$html .= '</tr>'; 
		$html .= '</table>';
		$html .= '</div>';

		return $html;
	}

	function tipblok_getChooseMenuButtonHtml($link, $ikona, $tekst){
		$button = '<td class="ocp_blokovi_td"';
		if (utils_valid($link))
			$button .= ' style="cursor:pointer;" onclick="window.open(\''.$link.'\', \'_self\');"';
		$button .= '><table> ';
		$button .= '<tr>';
		$button .= '<td class="ocp_gornji_2_td_dugmici"><img src="/ocp/img/opsti/blokovi/ikone/mali/'.$ikona.'.gif" width="21" height="16" class="ocp_blokovi_ikona"></td>';
		$button .= '<td class="ocp_blokovi_td_tekst_2"';
		if (!utils_valid($link)) $button .= ' style="font-weight:bold; color: #333333;"';
		$button .= ">" . $tekst; 
		$button .= '</td>';  
		$button .= '</tr>';
		$button .= '</table></td>'; 
		return $button;
	}

	function tipblok_getTipHtml($tipBloka){
		global $brojKolona;

		$className = (!is_null($tipBloka)) ? "ocp_opcije_td" : "ocp_opcije_td_forma";
		$html = '<td class="' . $className . '" width="' . round(100/$brojKolona) . '%"';
		if (!is_null($tipBloka)){
			if (isset($tipBloka["Blok_Id"]) && utils_valid($tipBloka["Blok_Id"]))
				$html .= ' style="cursor: pointer;" onclick="chooseShare(\''.$tipBloka["Blok_Id"].'\')"';
			else 
				$html .= ' style="cursor: pointer;" onclick="chooseType(\''.$tipBloka["TipB_Id"].'\')"';
		}
		$html .= '>';
		$html .= ' <table width="100%"><tr>';
		$html .= '<td align="center">';
		if (!is_null($tipBloka)){
			$html .= '<img src="'.str_replace("/srednji/", "/veliki/", $tipBloka["TipB_SlikaUrl"]).'" class="ocp_blokovi_ikona_velika" width="30"></td>'; 
			$html .= '</tr><tr><td class="ocp_blokovi_td_tekst" align="center">';
			if (isset($tipBloka["Blok_MetaNaziv"]))
				$html .= utils_toUpper($tipBloka["Blok_MetaNaziv"]);
			else
				$html .= utils_toUpper(ocpLabels($tipBloka["TipB_Naziv"]));
		} else $html .= '<img src="/ocp/img/blank.gif" class="ocp_blokovi_ikona_velika" width="30" border="0">';
		$html .= '</td>';
		$html .= '</tr></table>';
		$html .= '</td>';

		return $html;
	}
?>