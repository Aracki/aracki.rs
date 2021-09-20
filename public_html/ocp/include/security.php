<?php
	
	/*Cross-Site Request Forgery*/
	class Csrf {
		var $secToken = "";

		/*kreira random token za odredjenog usera i smesta ga u user sesiju*/
		function createToken() {
			$token = md5(uniqid(rand(), 1)); 
			$_SESSION["securityToken"] = $token;
			$this->secToken = $token;
		}

		/*na istoj stranici gde se poziva prethodna f-ja u formi se ubacuje ovaj kod*/
		function getFormToken(){
			if ($this->secToken != ""){
				echo("<input type='hidden' name='fgeartsecg' value='" . $this->secToken . "' /> ");
			}
		}
		
		/*na stranici koja preuzima podatke iz prethodne forme ovaj poziv se smesta
		na vrhu strani da bi se identifikovao user koji je pozvao formu.
		ukoliko je user validan vraca se true, u suprotnom false*/
		function checkToken(){
			$retValue = 0;
			if (isset($_REQUEST["fgeartsecg"]) && isset($_SESSION["securityToken"])){
				if ($_SESSION["securityToken"] == $_REQUEST["fgeartsecg"]){
					$retValue = 1;
				}
				unset($_SESSION["securityToken"]);
			}
			return $retValue;
		}

	}

?>