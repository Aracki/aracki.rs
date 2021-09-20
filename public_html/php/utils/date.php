<?php	
	/*=================================
	formats date for site display
	===================================*/
	function date_formatDateSite($value = null){
		if (is_null($value)){
			$value = date("d/m/Y", strtotime("now"));
		} else {
			$value = date("d/m/Y", strtotime($value));
		}
		 
		return $value;
	}
	
	/*=================================
	formats date for database query
	===================================*/
	function date_formatDateDb($value = null){
		if (is_null($value)){
			$value = date("Y/m/d", strtotime("now"));
		} else {
			$value = date("Y/m/d", $value);
		}
		 
		return $value;
	}
	?>