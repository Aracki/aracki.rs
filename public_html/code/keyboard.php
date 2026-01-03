<?php
	//encode i pretvaranje u encode
	function keyboard_convert($value){
		//$value = rawurlencode($value);

		while (utils_lastIndexOf($value, "%C4%8C")!=-1) $value = str_replace("%C4%8C", "%C8", $value);
		while (utils_lastIndexOf($value, "%C4%86")!=-1) $value = str_replace("%C4%86", "%C6", $value);
		while (utils_lastIndexOf($value, "%C5%BD")!=-1) $value = str_replace("%C5%BD", "%9E", $value);
		while (utils_lastIndexOf($value, "%C5%A0")!=-1) $value = str_replace("%C5%A0", "%8A", $value);
		while (utils_lastIndexOf($value, "%C4%90")!=-1) $value = str_replace("%C4%90", "%D0", $value);

		while (utils_lastIndexOf($value, "%C4%8D")!=-1) $value = str_replace("%C4%8D", "%E8", $value);
		while (utils_lastIndexOf($value, "%C4%87")!=-1) $value = str_replace("%C4%87", "%E6", $value);
		while (utils_lastIndexOf($value, "%C5%BE")!=-1) $value = str_replace("%C5%BE", "%9E", $value);
		while (utils_lastIndexOf($value, "%C5%A1")!=-1) $value = str_replace("%C5%A1", "%9A", $value);
		while (utils_lastIndexOf($value, "%C4%91")!=-1) $value = str_replace("%C4%91", "%F0", $value);

		return $value;
	}

?>