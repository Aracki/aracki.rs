<?php

/*Na osnovu datog niza gradi niz buttona u meniju	
==================================================*/
	function menu_html($menuArray) {

		// var_dump($menuArray);

		$menu = '<table class="ocp_gornji_2_table_dugmici"><tr>';
		$first = true;
		$previous = "";

		$keys = array_keys($menuArray);
		for ($i = 0; $i<count($keys); $i++){
			$key = $keys[$i]; $item = $menuArray[$key];

			if ($first){
				$menu .= '<td class="ocp_gornji_2_td_dugmici"><img src="/ocp/img/gornji_2/tabovi/levi_s.gif" width="10" height="24" id="'.$key.'_levi"></td>';
				$menu .= '<td style="background-image:url(\'/ocp/img/gornji_2/tabovi/bg_s.gif\');" class="ocp_gornji_2_dugme" id="'.$key.'_background" style="cursor:pointer;" onclick="switchTabs(\''.$key.'\', null);"><table class="ocp_gornji_2_table_dugmici">';
				$menu .= '<tr>';
				$menu .= '<td class="ocp_gornji_2_td_dugmici"><img src="/ocp/img/gornji_2/tabovi/'.$key.'_mod_ikona.gif" title="'.$item[0].'"></td>';
				$menu .= '<td class="ocp_gornji_2_dugme" id="'.$key.'_text" style="font-weight: normal; color: #000000;">'.$item[0].'</td>';
				$menu .= '</tr>';
				$menu .= '</table></td>';
				if (($i+1) < count($keys))
					$menu .= '<td class="ocp_gornji_2_td_dugmici"><img src="/ocp/img/gornji_2/tabovi/izm_sn.gif" id="'.$key.'_desni" width="8" height="24"></td>';
				else {
					$menu .= '<td class="ocp_gornji_2_td_dugmici"><img src="/ocp/img/gornji_2/tabovi/desni_s.gif" id="'.$key.'_desni" width="5" height="24"></td>';	
				}
			
				$first = false;
			} else {
				$menu .= '<td valign="bottom" style="background-image:url(\'/ocp/img/gornji_2/tabovi/bg_n.gif\');" id="'.$key.'_background" class="ocp_gornji_2_dugme">';
				$menu .= '<table class="ocp_gornji_2_table_dugmici"><tr>';
				$menu .= '<td class="ocp_gornji_2_td_dugmici" style="cursor: pointer;" onclick="switchTabs(\''.$key.'\', \''.$previous.'\');"><img src="/ocp/img/gornji_2/tabovi/'.$key.'_mod_ikona.gif" border="0" title="'.$item[0].'"></td>';
				$menu .= '<td class="ocp_gornji_2_dugme" style="cursor:pointer;font-weight:normal;color:#000000;" onclick="switchTabs(\''.$key.'\', \''.$previous.'\');" id="'.$key.'_text">'.$item[0].'</td>';
				$menu .= '</tr></table>';
				$menu .= '</td>';

				if (($i+1) < count($keys))
					$menu .= '<td class="ocp_gornji_2_td_dugmici"><img src="/ocp/img/gornji_2/tabovi/izm_nn.gif" id="'.$key.'_desni" width="8" height="24"></td>';
				else 
					$menu .= '<td class="ocp_gornji_2_td_dugmici"><img src="/ocp/img/gornji_2/tabovi/desni_n.gif" id="'.$key.'_desni" width="6" height="24"></td>';
			}
			$previous = $key;
		}

		$menu .= '</tr></table>';

		echo $menu;
	}

/*Gradi javascript array koji odgovara datom nizu i
 postavlja default akciju
==================================================*/
	function menu_script($menuArray, $loadDefaultPage){
		$script = "";
		$first = true;

		$keys = array_keys($menuArray);
		for ($i = 0; $i < count($keys); $i++){
			$key = $keys[$i]; $item = $menuArray[$key];

			if ($first){
				$script .= "var selected = '".$key."';\n";
				$script .= "var selected_previous = null;\n";
				$script .= "var menuArray = new Array();\n";

				$script .= "function defaultPage(){\n";
				if ($loadDefaultPage)
					$script .= "switchTabs('".$key."', null);\n";
				$script .= "}\n";
			
				$first = false;
			}

			$key = str_replace("'", "\'", $key);
			$item[0] = str_replace("'", "\'", $item[0]);
			$item[1] = str_replace("'", "\'", $item[1]);

			$script .= "menuArray['".$key."'] = new Array('".$item[0]."',  '".$item[1]."');\n";
		}

		// ako nema menija
		if ($script == "")
			$script = "function defaultPage(){}\n";

		echo $script;
	}
?>