<?php
/*Na osnovu datog niza gradi niz ikona u submeniju	
==================================================*/
	function submenu_html($submenuArray){
		$submenu = '<table cellpadding="0" cellspacing="0">';
		$submenu .= '<tr>';

		$keys = array_keys($submenuArray);
		for ($i = 0; $i<count($keys); $i++){
			$nextKey = $keys[$i]; $nextItem = $submenuArray[$nextKey]; 

			$nextIcon = (isset($nextItem[2]) && !is_null($nextItem[2])) ? $nextItem[2] : "/ocp/img/gornji_2/dugmici/ikona_".$nextKey.".gif";

			$submenu .= '<td height="28" class="ocp_gornji_2_td_dugmici" id="'.$nextKey.'">';
			$submenu .= '<table class="ocp_gornji_2_table_dugmici" style="cursor:pointer;" onclick="';
			$submenu .= 'switchSubmenuTab(\''.$nextKey.'\'); ';
			$submenu .= $nextItem[1].'"><tr>';
			$submenu .= '<td class="ocp_gornji_2_td_dugmici"><img src="'.$nextIcon.'" class="ocp_gornji_2_dugme_ikona" title="'.$nextItem[0].'"></td>';
			$submenu .= '<td nowrap class="ocp_gornji_2_dugme" id="'.$nextItem[0].'Id">'.$nextItem[0].'</td>';
			$submenu .= '</tr>'; 
			$submenu .= '</table>';
			$submenu .= '</td>';

			if ($nextKey == 'uredi_blokove')
				$submenu .= '<td width="2" id="crtka"><img src="/ocp/img/gornji_2/dugmici/crtka.gif" class="ocp_gornji2_crtka"></td>';
		}
		$submenu .= '</tr></table>';
		
		echo($submenu);
	}

/*Gradi javascript array koji odgovara datom nizu	
==================================================*/
	function submenu_script($submenuArray){
		$script = "";
		$script .= "var oldSubmenuTab = '';\n";
		$script .= "var submenuArray = new Array();\n";

		$keys = array_keys($submenuArray);
		for ($i = 0; $i<count($keys); $i++){
			$nextKey = $keys[$i]; $nextItem = $submenuArray[$nextKey]; 

			if (!isset($nextItem[2]) || is_null($nextItem[2]))
				$script .= "submenuArray['".$nextKey."'] = new Array(\"".$nextItem[0]."\",  \"".$nextItem[1]."\");\n";
			else
				$script .= "submenuArray['".$nextKey."'] = new Array(\"".$nextItem[0]."\",  \"".$nextItem[1]."\",  \"".$nextItem[2]."\", ".$nextItem[3].");\n";
		}
		echo($script);
	}

/*Na osnovu datog niza gradi niz ikona u submeniju	
==================================================*/
	function submenu_htmlEditor($submenuArray){
		$submenu = '<table class="ocp_opcije_table_univ">';
		$submenu .= '<tr>';

		$keys = array_keys($submenuArray);
		for ($i = 0; $i<count($keys); $i++){
			$nextKey = $keys[$i]; $nextItem = $submenuArray[$nextKey]; 

			$submenu .= '<td height="28" class="ocp_gornji_2_td_dugmici" id="'.$nextKey.'">';
			$submenu .= '<table class="ocp_gornji_2_table_dugmici" style="cursor:pointer;" onclick="';
			if ($nextItem[3])
				$submenu .= 'if ('.$nextItem[1].') switchSubmenuTab(\''.$nextKey.'\'); "><tr>';
			else 
				$submenu .= $nextItem[1].'"><tr>';
			$submenu .= '<td class="ocp_gornji_2_td_dugmici"><img src="'.$nextItem[2].'" class="ocp_napred_edit_dugme" title="'.$nextItem[0].'" width="21" height="21"></td>';
			$submenu .= '</tr>'; 
			$submenu .= '</table>';
			$submenu .= '</td>';
			if (($nextKey == 'link_mail') || ($nextKey == 'under') || ($nextKey == 'right') || 
				($nextKey == 'bullist') || ($nextKey == 'color') || ($nextKey == 'delcol') )
				$submenu .= '<td width="4"><img src="/ocp/img/kontrole/napredni_edit/dugmici/crtka.gif" width="2" height="16" class="ocp_napred_edit_crtka"></td>';
		}
		$submenu .= '</tr></table>';
		
		echo($submenu);
	}
?>