<?php
	/*Vraca broj milisekundi koji ide u LastModify
	==============================================*/
	function date_getMiliseconds(){
		return time();
	}

	/*Pretvaranje datuma string iz u datum baze
	===========================================*/
	function date_format4Database($value = NULL){
		$tmp = "";
		if (is_null($value)) $tmp = time();
		else $tmp = strtotime($value);
		return date("Y/m/d", $tmp);
	}

	/*Pretvaranje datuma string iz u datum baze
	===========================================*/
	function datetime_format4Database($value = NULL){
		$tmp = "";
		if (is_null($value)) $tmp = time();
		else $tmp = strtotime($value);
		return date("Y/m/d H:i:s", $tmp);
	}	

	/*Pretvaranje datuma u kratki mm/yy format
	za select liste i identifikacione atribute
	===========================================*/
	function date_formatMonthYear($value = NULL){
		$tmp = "";
		if (is_null($value)) $tmp = time();
		else $tmp = strtotime($value);
		return date("m/y", $tmp);
	}
	
	/* Na dateStart dodaje odredjeni broj dana
	u skladu sa period konstantom
	=========================================*/
	function date_addPeriod($dateStart, $periodConst, $sameHHMMSS = 0){
		$dateEnd = NULL;
		$dateStartInfo = getdate($dateStart);
		
		$seconds = $dateStartInfo['seconds']; $minutes = $dateStartInfo['minutes']; $hours = $dateStartInfo['hours'];
		if (!$sameHHMMSS){
			$seconds = 59; $minutes = 59; $hours = 23;
		}
		
		switch ($periodConst){
			case "D": 
				$dateEnd = mktime ($hours, $minutes, $seconds, $dateStartInfo['mon'], $dateStartInfo['mday']+1, $dateStartInfo['year']);
				break;
			case "N": 
				$dateEnd = mktime ($hours, $minutes, $seconds, $dateStartInfo['mon'], $dateStartInfo['mday']+7, $dateStartInfo['year']);
				break;
			case "2N": 
				$dateEnd = mktime ($hours, $minutes, $seconds, $dateStartInfo['mon'], $dateStartInfo['mday']+14, $dateStartInfo['year']);
				break;
			case "M": 
				$dateEnd = mktime ($hours, $minutes, $seconds, $dateStartInfo['mon']+1, $dateStartInfo['mday'], $dateStartInfo['year']);
				break;
		}

		return $dateEnd;
	}

	/* Vraca broj dana u mesecu
	=========================================*/
	function date_getNoDaysInMonth($mesec, $godina){
		$dateEnd = mktime(0, 0, 0, intval($mesec)+1, 0, $godina);
		return date("d", $dateEnd);
	}

	/*Pretvaranje datuma iz formi u datum koji treba za bazu
	========================================================*/
	function date_getFormDate($fieldName){
		$value = utils_requestStr(getRVar($fieldName));
		if (utils_valid($value)){
			return $value;
		}
		
		$fieldName_dd	= utils_requestStr(getRVar($fieldName."_dd"));
		$fieldName_mm	= utils_requestStr(getRVar($fieldName."_mm"));
		$fieldName_yyyy	= utils_requestStr(getRVar($fieldName."_yyyy"));

		$value = "";
		if (($fieldName_dd == "0") && ($fieldName_mm == "0") && ($fieldName_yyyy == "0")) return $value;
		if (utils_valid($fieldName_dd) && utils_valid($fieldName_mm) && utils_valid($fieldName_yyyy))
			$value = $fieldName_yyyy . "/" . $fieldName_mm . "/" . $fieldName_dd;
		return $value;
	}

	/*Pretvaranje datumavremena iz formi u datumvreme koji treba za bazu
	========================================================*/
	function datetime_getFormDate($fieldName){
		$value = utils_requestStr(getRVar($fieldName));
		if (utils_valid($value)){
			return $value;
		}

		$fieldName_dd	= utils_requestStr(getRVar($fieldName."_dd"));
		$fieldName_mm	= utils_requestStr(getRVar($fieldName."_mm"));
		$fieldName_yyyy	= utils_requestStr(getRVar($fieldName."_yyyy"));
		$fieldName_time	= utils_requestStr(getRVar($fieldName."_time"));

		$value = "";
		if (($fieldName_dd == "0") && ($fieldName_mm == "0") && ($fieldName_yyyy == "0")) return $value;
		if (utils_valid($fieldName_dd) && utils_valid($fieldName_mm) && utils_valid($fieldName_yyyy)){
			if (utils_valid($fieldName_time))
				$value = $fieldName_yyyy . "/" . $fieldName_mm . "/" . $fieldName_dd . " " . $fieldName_time;
			else 
				$value = $fieldName_yyyy . "/" . $fieldName_mm . "/" . $fieldName_dd;
		}
		return $value;
	}

	/*Pomocna f-ja za kod koji se cesto zove
	=========================================*/
	function date_setPublishExpiry($object, $labPublish, $labExpiry){
		$dPrikaz = $object[$labPublish];
		$dUklanj = $object[$labExpiry];

		if (utils_valid($dPrikaz))
			if (strtotime($dPrikaz) > time()) $object["Valid"] = "green";
		if (utils_valid($dUklanj)){
			if (strtotime($dUklanj) < time()) $object["Valid"] = "red";
		}
		if (!isset($object["Valid"])) $object["Valid"] = "ok";

		return $object;
	}

	/*Vraca mesec u jeziku ocp-a
	=========================================*/
	function date_getMonth($i){
		$monthsArr = array(ocpLabels("January"), ocpLabels("February"), ocpLabels("March"), ocpLabels("April"), ocpLabels("May"), ocpLabels("June"), ocpLabels("July"), ocpLabels("August"), ocpLabels("September"), ocpLabels("October"), ocpLabels("November"), ocpLabels("December"));
		return (isset($monthsArr[$i]) ? $monthsArr[$i] : "");
	}

	/*Vraca dan u jeziku ocp-a
	=========================================*/
	function date_getDate($i){
		$daysArr = array(ocpLabels("Sunday"), ocpLabels("Monday"), ocpLabels("Tuesday"), ocpLabels("Wednesday"), 
							ocpLabels("Thursday"), ocpLabels("Friday"), ocpLabels("Saturday"));
		return (isset($daysArr[$i]) ? $daysArr[$i] : "");
	}
?>