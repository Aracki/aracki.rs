<?php
	function utils_toLower($value){
		mb_internal_encoding("UTF-8");
		$value = mb_strtolower($value);

/*		//ako mbstring modul nije instaliran
		$value = strtolower($value);
*/
		return $value;
	}

	function utils_toUpper($value){
		mb_internal_encoding("UTF-8");
		$value = mb_strtoupper($value);

/*		//ako mbstring modul nije instaliran
		$value = strtoupper($value);
*/
		return $value;
	}

/*Skracivanje mb stringa
	========================*/
	function utils_substr($value, $start, $length = NULL){
		mb_internal_encoding("UTF-8");
		if (!is_null($length))
			$value = mb_substr($value, $start, $length);
		else
			$value = mb_substr($value, $start);

		//ako mbstring modul nije instaliran
/*		if (!is_null($length))
			$value = substr($value, $start, $length);
		else
			$value = substr($value, $start);*/

		return $value;
	}

	/*Skracivanje mb stringa
	========================*/
	function utils_strlen($value){
		mb_internal_encoding("UTF-8");
		$ret = mb_strlen($value);

/*		//ako mbstring modul nije instaliran
		$ret = strlen($value);*/ 

		return $ret;
	}

	/*LastIndexOf
	=============*/
	function utils_lastIndexOf($instr, $sub_str) { 
		if(utils_valid($sub_str) && strstr($instr, $sub_str) != "") { 
			return( strlen($instr) - strpos(strrev($instr), strrev($sub_str)) - strlen($sub_str)); 
		} 
		return(-1); 
	}

	function utils_strpos($value, $searchString, $offset = NULL){
		mb_internal_encoding("UTF-8");
		$ret = mb_strpos($value, $searchString, $offset);

/*		//ako mbstring modul nije instaliran
		$ret = strpos($value, $searchString, $offset);*/ 

		return $ret;
	}

	function utils_strrpos($value, $searchString, $offset = NULL){
		mb_internal_encoding("UTF-8");
		$ret = mb_strrpos($value, $searchString, $offset);

/*		//ako mbstring modul nije instaliran
		$ret = strrpos($value, $searchString, $offset);*/ 

		return $ret;
	}
	
?>