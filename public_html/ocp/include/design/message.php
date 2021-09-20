<?php
	function message_info($message){
		$str = '<table class="ocp_opcije_table">';
		$str .= '<tr>';
		$str .= '<td align="center" class="ocp_opcije_td">';
		$str .= '<table><tr>';
		$str .= '<td><img src="/ocp/img/opsti/razno/info.gif"></td>';
		$str .= '<td align="center"><span class="ocp_opcije_tekst1" style="font-weight:bold;">'.$message.'</span></td>';
		$str .= '</tr></table>';
		$str .= '</td>';
		$str .= '</tr>';
		$str .= '</table>';

		return $str;
	}

	/*Opcija label polje
	==================================*/
	function message_option_info($message){
		$str = '<tr><td class="ocp_opcije_td" align="left" colspan="2">';
		$str .= '<table><tr>';
		$str .= '<td><img src="/ocp/img/opsti/razno/info.gif"></td>';
		$str .= '<td align="center"><span class="ocp_opcije_tekst1">'.$message.'</span></td>';
		$str .= '</tr></table>';
		$str .= '</td></tr>';

		return $str;
	}

	/*Samo tekst
	==================================*/
	function message_simple_info($message){
		$str = '<table><tr>';
		$str .= '<td><img src="/ocp/img/opsti/razno/info.gif"></td>';
		$str .= '<td align="center"><span class="ocp_opcije_tekst1">'.$message.'</span></td>';
		$str .= '</tr></table>';

		return $str;
	}
?>