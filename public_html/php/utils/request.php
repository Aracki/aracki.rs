<?php
	session_start();

	/*=================================
	gets request variable
	===================================*/
	function getRVar($var, $predefined=NULL){
		return (isset($_REQUEST[$var]) ? $_REQUEST[$var] : $predefined);
	}

	/*=================================
	gets get variable
	===================================*/
	function getGVar($var, $predefined=NULL){
		return (isset($_GET[$var]) ? $_GET[$var] : $predefined);
	}

	/*=================================
	gets post variable
	===================================*/
	function getPVar($var, $predefined=NULL){
		return (isset($_POST[$var]) ? $_POST[$var] : $predefined);
	}
	
	/*=================================
	sets cookie variable
	===================================*/
	function setCVar($var, $value, $exp = NULL){
		if (is_null($exp))
			setcookie ($var, $value);
		else
			setcookie ($var, $value, $exp);
	}
	
	
	/**
* @return mixed
* @param var string
* @param [key mixed]
* @desc vraca vrednost session variable
*/
function getSVar($var,$key=NULL){
	if(isset($_SESSION[$var])){
		$SVar=$_SESSION[$var];
		if(is_array($SVar) && !is_null($key)){
			if(array_key_exists($key,$SVar))
			return $SVar[$key];
			else
			return NULL;
		}else{
			return $SVar;
		}
	}else{
		return NULL;
	}
}
/**
* @return void
* @param var string
* @param val mixed
* @param key = NULL mixed
* @desc kreira session varijablu, ako postoji $key, $var je array
*/
function setSVar($var,$val,$key=NULL){
	if(is_null($key)){
		// not an array
		$_SESSION[$var]=$val;
	}else{
		// is an array
		$SVar = getSVar($var,NULL);
		if (is_null($SVar)){
			// create new array
			$SVar = array ($key => $val);
		}else{
			// add to an existing array
			$SVar[$key] = $val;
		}
		$_SESSION[$var]=$SVar;
	}
}

?>