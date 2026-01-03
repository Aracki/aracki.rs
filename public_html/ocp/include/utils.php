<?php 

	require_once($_SERVER['DOCUMENT_ROOT']."/ocp/include/convert_url.php");
	
/*Kreira random dodatak query stringu
========================================*/
	function utils_randomQS(){
		return "random=" . date_getMiliseconds();
	}

/*Vraca 1/0 ako je rewrite enabled/disabled
========================================*/
	function utils_isRewrite(){
		if (!isset($_SESSION["RewriteUrl"]))
			$_SESSION["RewriteUrl"] = con_getValue("select Root_Rewrite from Root where Root_Id=1");
		return $_SESSION["RewriteUrl"];
	}

/*Fja koja vraca path strane
================================*/
	function utils_getStraLink($id){
		//provera da ne bi pucali linkovi
		if (!is_numeric($id)) return 1;

		$extraXml = con_getValue("select Stra_ExtraParams from Stranica where Stra_Id=".$id);
		preg_match_all("/>(([^\/<]+)<)\/extern_link>/", $extraXml, $info);

		if (isset($info[0][0]) && utils_valid($info[0][0])) {
			$link = substr($info[0][0], 1, strpos($info[0][0], "</extern_link>")-1);
			return rawurldecode($link);
		}

		if (utils_isRewrite()) return con_getValue("select Stra_Link from Stranica where Stra_Id=".$id);
		return "/code/navigate.php?Id=" . $id;	
	}

/*Vraca adresu servera
==================================*/
	function utils_getServerAddress(){
		$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
		$protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0, strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")) . $s;
		$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
		$address = ($protocol."://".$_SERVER['SERVER_NAME'].$port);

		return $address;
	}

/*Generise kod proizvoljne duzine
==================================*/
	function utils_generateCode($length){
		$PATTERN  = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";

		$code = "";
		
		for( $i=0; $i< $length; $i++){
			$code .= substr($PATTERN, rand(0,35), 1);
		}
		return $code;
	}

/*Fja koja pretvara apsolutni path u relative
========================================*/
	function utils_getRelativePath(){
		//return preg_replace("/\/(\w+)/", "../", substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], "/")));
		//$script  = substr($_SERVER["SCRIPT_FILENAME"], strlen($_SERVER['DOCUMENT_ROOT']));
		//return preg_replace("/\/(\w+)/", "../", substr($script, 0, strrpos($script, "/")));
		return $_SERVER["DOCUMENT_ROOT"];
	}

/*Provera da li je neka vrednost validna
========================================*/
function utils_valid($value){
	if (isset($value) && !is_null($value) && ($value != "undefined") && ($value != "") && ($value != "null")) 
		return 1;
	return 0;
}

/*Php promena nl u <br>
========================================*/
	function utils_changeNl2Br($str){
		if (utils_valid($str))
			return ereg_replace("\r\n", "<br>", $str);
		return $str;
	}

/*Html encode
========================================*/
	function utils_htmlEncode($str){
		if (utils_valid($str))
			$str = htmlspecialchars($str, ENT_NOQUOTES);
		return $str;
	}

/*Debug
========================================*/
	function utils_log($value){
		$dir = $_SERVER['DOCUMENT_ROOT'] . "/ocp/logs";
		if (file_exists($dir) && is_writable($dir)){
			$handle = fopen($dir . "/log" . date("Ymd").".txt", "a+");
			fwrite($handle, date("Y/m/d H:m:s", time()) . " | " . $value . "\n\n");
			fclose($handle);
		}
	}

/*Sql injection podrska za integer vrednosti
========================================*/
	function utils_requestInt($value){
		if (utils_valid($value)){
			$value = intval($value);
			if (is_int($value)) {
				$value = intval($value);
			} else {
				$value = 0;
			}
		} else {
			$value = 0;
		}

		// sada trik vracam string da mi ne bi pucali stari skriptovi
		return ("" . $value);
	}

/*Sql injection podrska za string vrednosti
not_xss je true samo ako treba iskljuciti
cross-scripting validaciju
not_quote je true samo ako treba iskljuciti
single quote validaciju
not_dirTrans ako treba iskljuciti directory 
transversal validaciju
not_HttpResSplit ako treba iskljciti Http 
Response Splitting validaciju
========================================*/
	function utils_requestStr($value, $not_xss = NULL, $not_quote = NULL, $not_dirTrans = NULL, $not_HttpResSplit = NULL){
		if (is_array($value))
			$value = utils_requestStr(implode(", ", $value));

		if (!utils_valid($value)) return $value;

		$value = "" . $value;

		//replacuje sve izmedju tagova u <...> u blank
		if (is_null($not_xss) || !$not_xss){
//< -> [ i > -> ] 
			$value = preg_replace("/(\%3C|<)/", "[", $value);
			$value = preg_replace("/(\%3E|>)/", "]", $value);
			$value = preg_replace("/\"/", "&quot;", $value);
		}

		//moram prvo sve ' da pretvorim u 'cudan string, zatim cudan string u jos jedan '
		// ne moras, desice te to u utils_escapeSingleQuote :)
		if (is_null($not_quote) || !$not_quote){
			$value = utils_escapeSingleQuote($value);
		}

		//directory transversal
		//replacuje sve ../ u blank
		if (is_null($not_dirTrans) || !$not_dirTrans){	
			//ocpije ../|%2e%2e%2f|..%2f|%2e.%2f|.%2e%2f|%2e%2e/  
			$pattern = "/(%2E|\\x2E)(%2E|\\x2E)(%2F|\\x2F)/i";
			$value = preg_replace($pattern, "", $value);
		}

		//HTTP response splitting
		//"%0d%0aSet-Cookie%3Asome%3Dvalue
		if (is_null($not_HttpResSplit) || !$not_HttpResSplit){
			$pattern = "/(%0d|\x0D){0,1}(%0a|\x0A){0,1}".utils_stringRegEx("Set-Cookie")."(%3A|\x3A)/i";
			$value = preg_replace($pattern, "", $value);
		}

		return $value;
	}

/* Escape single quote-a u formatu za bazu
===========================================*/
	function utils_escapeSingleQuote($value){
		if (!utils_valid($value)) return $value;

		$value = preg_replace("/\\\'/", "\x27", $value);
		$value = preg_replace("/\x27/", "\\\x27", $value);

		return $value;
	}

/*Vraca objekat sa popunjenim default vrednostima
format: &ocp_default_values=name1:value1|name2:value2|....|namen:valuen
===========================================*/
	function utils_parseOcpDefaultValues($ocpDefaultValues){
		$data = NULL;
		if (utils_valid($ocpDefaultValues)){
			//&ocpDefaultValues=name1:value1|name2:value2|....|namen:valuen
			$ocpDefaultArray = !is_integer(strpos($ocpDefaultValues, "|")) ? array($ocpDefaultValues) : split("[|]", $ocpDefaultValues);
//utils_dump($ocpDefaultArray, 1);
			if (count($ocpDefaultArray) > 0){
				$data = array();
				for ($i=0; $i<count($ocpDefaultArray); $i++){
				
					if (!is_integer(strpos($ocpDefaultArray[$i], ":"))) continue;

					$key = substr($ocpDefaultArray[$i], 0, strpos($ocpDefaultArray[$i], ":"));
					$value = substr($ocpDefaultArray[$i], strpos($ocpDefaultArray[$i], ":")+1);

					$data[$key] = $value;
				}
				if (count($data) == 0) $data = NULL;
//utils_dump($data, 1);
			}
		}
		return $data;
	}

/*Ubijanje localhosta u adresama
========================================*/
	function utils_killBadLinks($value){
		if (utils_valid($value)){
			$lokalnaAdresa = getSVar("ocpLocalAddress");
			if (utils_valid($lokalnaAdresa)){
				$value = str_replace(rawurlencode("http://".$lokalnaAdresa), "", $value);
				$value = str_replace("http://".$lokalnaAdresa, "", $value);			
			}
			$trenutnaAdresa = $_SERVER['HTTP_HOST'];
			if (utils_valid($trenutnaAdresa)) {
				$value = str_replace(rawurlencode("http://".$trenutnaAdresa), "", $value);
				$value = str_replace("http://".$trenutnaAdresa, "", $value);		
			}
			$value = str_replace(rawurlencode("http://localhost"), "", $value);
			$value = str_replace("http://localhost", "", $value);
			$value = str_replace(rawurlencode("http://127.0.0.1"), "", $value);
			$value = str_replace("http://127.0.0.1", "", $value);
			// Milos promenio
			$serverName = "http://".$_SERVER["SERVER_NAME"];
			$value = str_replace(rawurlencode($serverName), "", $value);
			$value = str_replace($serverName, "", $value);
			// kraj Milos
			$value = str_replace(rawurlencode("http:///"), "", $value);
			$value = str_replace("http:///", "", $value);
		}
		return $value;
	}


/*Vrsi update i vraca broj updatovanih redova
==============================================*/
	function utils_executeUpdate($updateStr){
		return con_update($updateStr);
	}
	
/*Azurira upis poslednjeg upisa u bazu
======================================*/
	function utils_updateSiteMenu(){
		con_update("update SiteMenu set LastModifyOcp=NOW() where Id=1");
	}


/*Generise javascript validacione f-je u
svim file-ovima gde se gradi forma
=========================================*/
	function utils_getValidation($validateNamesFunc, $validateFunc, $blok = NULL){
		jsValidateOcpLabels();

		?><script src="/ocp/validate/validate_double_quotes.js"></script>
		<script SRC="/ocp/jscript/select.js" type="text/javascript"></script>
		<script SRC="/ocp/jscript/pallete.js" type="text/javascript"></script>
		<script SRC="/ocp/jscript/helpcalendar.js" type="text/javascript"></script><?php	
		if (utils_valid($blok) && $blok){?>
		<script src="/ocp/validate/user/validate_datetimes.js"></script>
<?php	}
		$nl =	"\n";
		$systemValidate ="validate_dve_slike,validate_velika_slika,validate_tekst_slika";
		for ($i = 0; $i< count($validateNamesFunc); $i++){
			if (utils_valid($validateNamesFunc[$i])){
				$valAddFolder = "";
				if (!is_integer(strpos($systemValidate, $validateNamesFunc[$i]))) {
					$valAddFolder = "/user";
				}
				echo("<script src=\"/ocp/validate".$valAddFolder."/".$validateNamesFunc[$i].".js\"></script>".$nl);
			}
		}
		for ($i = 0; $i< count($validateNamesFunc); $i++){
			echo('<script src="/ocp/validate/user/'.$validateNamesFunc[$i].'.js"></script>'.$nl);
		}
		echo("<script>".$nl);
		echo("function	validate(){".$nl);
		echo(" if (simpleEditorExists){".$nl);
		echo("checkHtmlEditors(simpleEditorArr); ".$nl);
		echo("}".$nl);
		$output = "var value=";
		$change = false;
		for ($k=0; $k<count($validateFunc);	$k++){
			$output .= $validateFunc[$k]." && ";
			$change = true;
		}
		if	($change) $output = substr($output, 0, strlen($output)-4);
		else $output .="true";
		echo ($output.";".$nl);
		if (utils_valid($blok) && $blok){
			echo("if (document.formObject.Blok_PublishDate_dd){".$nl);
			echo('value = value && validate_datetimes("formObject.Blok_PublishDate") && validate_datetimes("formObject.Blok_ExpiryDate");'.$nl);
			echo("}".$nl);
			echo("value = value && checkMetaNaziv();".$nl);
		}
		echo ("if (!value) return false;".$nl);
		if ($blok) echo("if (document.formObject.Blok_MetaNaziv) validate_double_quotes_field(document.formObject.Blok_MetaNaziv);".$nl);
		else echo(" validate_double_quotes(document.formObject);".$nl);
		echo ("return true;".$nl);
		echo ("}".$nl);
		echo ("</script>".$nl);
	}

	function utils_stringRegEx($str){
		$strRegEx = "";
		for ($i=0; $i<strlen($str); $i++){
			$chr = substr($str, $i, 1);
			$strRegEx .= "(" . strtolower($chr) . "|" . strtoupper($chr) . "|%" . utils_dec2hex(ord($chr)) . 
						"|%" . utils_dec2hex(ord($chr)+32) . ")";
		}
		return $strRegEx;
	}
	
	function utils_dec2hex($n){
		$retstr = dechex($n);
		if (strlen($retstr) == 1) $retstr = "0" . $retstr;
		if (strlen($retstr) == 0) $retstr = "00";
		return $retstr;
	}

	function utils_dump($message, $var_dump = 0){
		if ($var_dump) { 
			var_dump($message); echo("<br>");
		} else{
			$message = str_replace("<", "&lt;", $message);
			$message = str_replace(">", "&gt;", $message);
			echo($message . "<br>");
		}
		echo("-----------------------------------------<br>");
	}

	function utils_matrixSort($matrix, $sortKey, $direction) {
		for ($i=0; $i < count($matrix); $i++) {
			$ni = $matrix[$i];
			for ($j = $i; $j < count($matrix); $j++) {
				$nj = $matrix[$j];
				if ($sortKey == "desc") {
					if ($ni[$sortKey] > $nj[$sortKey]) {
						$tmp = $ni; $ni = $nj; $nj = $tmp;
						$matrix[$i] = $ni;
						$matrix[$j] = $nj;
					}
				} else {
					if ($ni[$sortKey] < $nj[$sortKey]) {
						$tmp = $ni; $ni = $nj; $nj = $tmp;
						$matrix[$i] = $ni;
						$matrix[$j] = $nj;
					}
				}
			}		
		}
		return $matrix;
	}

	function utils_escape($sourceStr){
		$replacmentArray = array(	'\\'=>'\\\\', "'"=>"\\'",
									'"'=>'\\"', "\r"=>'\\r', 
									"\n"=>'\\n', '</'=>'<\/'
							);
		return strtr($sourceStr, $replacmentArray);
	}

	/*Prepare SQL expression
//example: onload onblur (parent OR window OR top) "Firefox iframe" -event
================================*/
	function utils_advancedSearchSQL($field, $value){
//utils_dump("Field je " . $field);
//utils_dump("Value je " . $value);
		//disjunkcija
		$patternLeast = "/[(][a-z0-9_\s]+[)]/i";
		$reArray = array();
		preg_match_all($patternLeast, $value, $reArray);
		$least = "";
		if (!is_null($reArray)){
			for ($i=0; $i<count($reArray[0]); $i++){
				$least = substr($reArray[0][$i], 1, strlen($reArray[0][$i])-2);
				$least = preg_replace("/\sOR/", "", $least);
				$value = preg_replace($patternLeast, "", $value);
				$value = preg_replace("/\s\s/", "", $value);
			}
//utils_dump("Least ".$least);
		}
		
		//exact phrase
		$patternExact = "/\"[a-z0-9_\s]+\"/i";
		$value = preg_replace("/&quot;/", "\"", $value);
		$reArray = array();
		preg_match_all($patternExact, $value, $reArray);
		$exact = "";
		if (!is_null($reArray)){
			for ($i=0; $i<count($reArray[0]); $i++){
				$exact = substr($reArray[0][$i], 1, strlen($reArray[0][$i])-2);
			}
			if ($exact != ""){
				$value = preg_replace($patternExact, "", $value);
				$value = preg_replace("/\s\s/", "", $value);
			}
//utils_dump("Exact ".$exact);
		}

		//negacija
		$patternWithout = "/\x2D[a-z0-9_\s]+/i";
		$reArray = array();
		preg_match_all($patternWithout, $value, $reArray);
		$without = "";
		if (!is_null($reArray)){
			for ($i=0; $i<count($reArray[0]); $i++){
				$without .= substr($reArray[0][$i], 1) . " ";
			}
			if ($without != ""){
				$without = substr($without, 0, strlen($without)-1);
				$value = preg_replace($patternWithout, "", $value);
				$value = preg_replace("/\s\s/", "", $value);	
			}
//utils_dump("Without '".$without."'");
		}
		
		//konjukcija ostatak
		$all = trim($value);
//utils_dump("All ".$all);

		$sql = "";
		if ($all != ""){
			$sql = $field . " LIKE '%" . preg_replace("/\s/", "%' AND ".$field." LIKE '%", $all) . "%'";
			//korekcija ciklicnog replacovanja
			$sql = str_replace("AND ".$field." LIKE '%%' ", "", $sql);
		}
		if ($exact != ""){
			if ($sql != "") $sql .= " AND ";
			$sql .= $field . " LIKE '%" . $exact . "%'";
		}

		if ($least != ""){
			if ($sql != "") $sql .= " AND ";			
			$sql .= "(";
			$sql .= $field . " LIKE '%" . preg_replace("/\s/", "%' OR ".$field." LIKE '%", $least) . "%'";
			$sql .= ")";
//utils_dump($sql);
			//korekcija ciklicnog replacovanja
			$sql = str_replace("OR ".$field." LIKE '%%' ", "", $sql);
		}
		if ($without != ""){
			if ($sql != "") $sql .= " AND ";			
			$sql .= $field . " NOT LIKE '%" . preg_replace("/\s/", "%' AND ".$field." NOT LIKE '%", $without) . "%'";
			//korekcija ciklicnog replacovanja
			$sql = str_replace("AND ".$field." NOT LIKE '%%' ", "", $sql);
		}

		$sql = trim($sql);
		$sql = " AND (".$field." IS NOT NULL AND (".$sql."))";

//utils_dump($sql);

		return $sql;
	}

	function utils_strictHtml($str){
//utils_dump($str);
		$str = str_replace("<?xml version=\"1.0\" standalone=\"yes\"?>", "", $str);

		//upper tags -> lower tags
		$str = preg_replace("/<(\w+)>/e", "'<'.strtolower('\\1').'>'", $str);
		$str = preg_replace("/<(\w+)\s/e", "'<'.strtolower('\\1').' '", $str);
		$str = preg_replace("/<\/(\w+)>/e", "'</'.strtolower('\\1').'>'", $str);

		//attributes enclosed with double-quotes
		//class=name -> class="name"
		$str = preg_replace("/\s(\w+)=(\w+)\s/", " $1=\"$2\" ", $str);
		$str = preg_replace("/\s(\w+)=(\w+)>/", " $1=\"$2\">", $str);

//utils_log($str);

		//special cases
		//table within p tag
		$str = preg_replace("/<p><table/sU", "<table", $str);
		$str = preg_replace("/<\/table><\/p>/sU", "</table>", $str);
//utils_log($str);
		//br tag
		$str = preg_replace("/<br>/", "<br/>", $str);
		//strong tag
		$str = preg_replace("/<strong>/", "<b>", $str);
		$str = preg_replace("/<\/strong>/", "</b>", $str);
		//em tag
		$str = preg_replace("/<em>/", "<i>", $str);
		$str = preg_replace("/<\/em>/", "</i>", $str);
		//u tag
		$str = preg_replace("/<u>/", "<span class=\"underlined\">", $str);
		$str = preg_replace("/<\/u>/", "</span>", $str);

		//target
		//$str = preg_replace("/target=(\")*_self(\")*/", "", $str);
		//$str = preg_replace("/target=(\")*_blank(\")*/", "", $str);
//utils_log($str);

		return $str;
	}

	function utils_convertTitleToLink($title)
	{
		$title = strtolower($title);
		$title = stripslashes($title);
		$title = trim(strip_tags($title));

		$title = preg_replace("/!(a-zA-Z0-9-)+/", "", $title);
		$title = preg_replace("/\s/", "-", $title);

		$title = str_replace("!", "", $title);
		$title = str_replace("@", "", $title);
		$title = str_replace("#", "", $title);
		$title = str_replace("$", "", $title);
		$title = str_replace("%", "", $title);
		$title = str_replace("^", "", $title);
		$title = str_replace("&", "", $title);
		$title = str_replace("*", "", $title);
		$title = str_replace("(", "", $title);
		$title = str_replace(")", "", $title);
		$title = str_replace("=", "", $title);
		$title = str_replace("_", "-", $title);
		$title = str_replace("+", "-", $title);
		$title = str_replace(",", "", $title);
		$title = str_replace(".", "-", $title);
		$title = str_replace("/", "", $title);
		$title = str_replace(";", "", $title);
		$title = str_replace("'", "", $title);
		$title = str_replace("\\", "", $title);
		$title = str_replace("[", "", $title);
		$title = str_replace("]", "", $title);
		$title = str_replace("<", "", $title);
		$title = str_replace(">", "", $title);
		$title = str_replace("?", "", $title);
		$title = str_replace(":", "", $title);
		$title = str_replace("\"", "", $title);
		$title = str_replace("\"", "", $title);
		$title = str_replace("”", "", $title);
		$title = str_replace("|", "", $title);
		$title = str_replace("{", "", $title);
		$title = str_replace("}", "", $title);
		$title = str_replace(" ", "-", $title);

		$title = preg_replace("/(\-)+/", "-", $title);

		$title = urlencode($title);

		$title = str_replace("%C4%8D", "c", $title);
		$title = str_replace("%E4%8D", "c", $title);
		$title = str_replace("%C4%87", "c", $title);
		$title = str_replace("%E4%87", "c", $title);
		$title = str_replace("%C5%BE", "z", $title);
		$title = str_replace("%E5%BE", "z", $title);
		$title = str_replace("%C5%A1", "s", $title);
		$title = str_replace("%E5%A1", "s", $title);
		$title = str_replace("%C4%91", "dj", $title);
		$title = str_replace("%E4%91", "dj", $title);
		$title = str_replace("%C4%8C", "c", $title);
		$title = str_replace("%E4%8C", "c", $title);
		$title = str_replace("%E4%9C", "c", $title);
		$title = str_replace("%C4%86", "c", $title);
		$title = str_replace("%E4%86", "c", $title);
		$title = str_replace("%C5%BD", "z", $title);
		$title = str_replace("%E5%BD", "z", $title);
		$title = str_replace("%C5%A0", "s", $title);
		$title = str_replace("%E5%A0", "s", $title);
		$title = str_replace("%E5-", "s", $title);
		$title = str_replace("%C4%90", "dj", $title);
		$title = str_replace("%E4%90", "dj", $title);
		$title = str_replace("%E2%80%9D", "", $title);

		$title = urldecode($title);

		return $title;
	}
	/**
	* Returns filesystem path (/var/www...) to site root without
	* return string filesystem path to site root
	*/
	function utils_getSiteFsRoot(){
            return realpath(dirname(__FILE__). "/../../");
        }
?>