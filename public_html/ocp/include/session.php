<?php
	session_start();

	if (get_magic_quotes_gpc()) {
	   function stripslashes_deep($value)
	   {
		   $value = is_array($value) ?
					   array_map('stripslashes_deep', $value) :
					   stripslashes($value);

		   return $value;
	   }

	   $_POST = array_map('stripslashes_deep', $_POST);
	   $_GET = array_map('stripslashes_deep', $_GET);
	   $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
	   $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
	}
	
	$site_root = $_SERVER['DOCUMENT_ROOT'];

	require_once($site_root."/ocp/include/utils.php");
	require_once($site_root."/ocp/include/date.php");
	require_once($site_root."/ocp/include/string.php");
	require_once($site_root."/ocp/include/log.php");
	require_once($site_root."/ocp/include/prevod.php");

	
	if (isset($_SESSION['ocpUsername'])) {
		$TR=$_SESSION['ocpTR'];
		$VR=$_SESSION['ocpVR'];
		$SR=$_SESSION['ocpSR'];
		$PR=$_SESSION['ocpPR'];
		$RR = $_SESSION["ocpRR"];
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: public");
	} else {
		session_clearSessions();
	}

/*Cisti session promenljive kada se uradi logout
==============================================*/
	function session_clearSessions(){
		foreach ($_SESSION as $obj) {
			$obj = null;
			unset($obj);
		}
		session_unset();
		session_destroy();
   	?>
	<SCRIPT>
	var d = new Date();
	if (top && top.opener) {
		if (top.opener.top){
			top.opener.top.location.href = "/ocp/login.php?random="+Date.parse(d);
		} else {
			top.opener.location.href = "/ocp/login.php?random="+Date.parse(d);
		}
		top.close();
	} else {
		if (opener){
			if (opener.top){
				opener.top.location.href = "/ocp/login.php?random="+Date.parse(d); 
			} else {
				opener.location.href = "/ocp/login.php?random="+Date.parse(d); 
			}
			this.close();
		} else {
			if (top)
				top.location.href = "/ocp/login.php?random="+Date.parse(d);
			else 
				this.location.href = "/ocp/login.php?random="+Date.parse(d);
		}
	}
	</SCRIPT>
   <?php
	   die();
	}

/*Loaduje konfiguraciju ocpa
========================================*/
	function session_loadConfig(){
		$doc = xml_load($_SERVER['DOCUMENT_ROOT'] . "/ocp/config/config.xml");
		
		$disable = xml_getFirstElementByTagName($doc, "disable");
		if (is_null($disable) || !utils_valid(xml_getContent($disable))) $disable = "";
		else $disable = xml_getContent($disable);
		$_SESSION['ocpDisable'] = $disable;
		
		$firstOpen = xml_getFirstElementByTagName($doc, "first_open");
		if (is_null($firstOpen) || !utils_valid(xml_getContent($firstOpen))){
			if (substr_count($disable, "siteManager") == 0)
				$firstOpen = "siteManager";
			else if (substr_count($disable, "objectManager") == 0)
				$firstOpen = "objectManager";
			else 
				$firstOpen = "reports";	

		} else $firstOpen = xml_getContent($firstOpen);
		$_SESSION['ocpFirstOpen'] = $firstOpen;

		$allowed_delete_files = xml_getFirstElementByTagName($doc, "allowed_delete_files");
		if (is_null($allowed_delete_files) || !utils_valid(xml_getContent($allowed_delete_files))) $allowed_delete_files = "0";
		else $allowed_delete_files = xml_getContent($allowed_delete_files);
		$_SESSION['ocpAllowedDeleteFiles'] = $allowed_delete_files;
	}

/*Da li je ulogovani super administrator
========================================*/
	function session_checkAdministrator(){
		if ($_SESSION['ocpUserGroup'] != "null") {
		?>
<SCRIPT>
	if (top && top.opener) top.close();
	else if (opener) this.close();
</SCRIPT><?php
			die();
		}
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
				$SVar += array($key => $val);
			}
			$_SESSION[$var]=$SVar;
		}
	}
	//===============================================================
	//request: vraca vrednost iz $_REQUEST, ako ne postoji vraca NULL
	//===============================================================
	function getRVar($var){
		return (isset($_REQUEST[$var]) ? $_REQUEST[$var] : NULL);
	}
	/**
	* @return mixed
	* @param var string
	* @desc vraca vrednost iz $_GET, ako ne postoji vraca NULL
	*/
	function getGVar($var){
		return (isset($_GET[$var]) ? $_GET[$var] : NULL);
	}
	
	//===============================================================
	//post: vraca vrednost iz $_POST, ako ne postoji vraca NULL
	//===============================================================
	function getPVar($var){
		return (isset($_POST[$var]) ? $_POST[$var] : NULL);
	}
?>