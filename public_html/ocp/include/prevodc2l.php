<?php
	//sa escape
	function cir2lat($value){
		$value = rawurlencode($value);
		$value = cir2lat2($value);
		$value = rawurldecode($value);
		return $value;
	}

//bez escape
	function cir2lat2($value){
		//fali sa ministarstva prosvete ili napisati ponovo
		return $value;
	}

?>