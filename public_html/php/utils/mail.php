<?php 
	/*=================================
	Sends mail
	===================================*/
	function utils_mail($to, $subject, $message, $headers = null){
		if (is_null($headers)){
			$VerzLabele = getSVar("VerzLabele");
			$from = (utils_valid($VerzLabele) && isset($VerzLabele["system_mail"])) ? 
						$VerzLabele["system_mail"] : "noreply@noreply.com"; 

			$headers  = 'MIME-Version: 1.0' . "\n";
			$headers .= 'Content-type: text/html; charset=UTF-8' . "\n";
			$headers .= 'From: ' .$from . "\n";
		}

		mail($to, $subject, $message, $headers);
	}

	/*=================================
	Sends template mail
	======================*/
	function utils_templateMail($to, $subject, $message, $headers = NULL, $bcc = NULL){
		$serverAddress = utils_getServerAddress();

		$source = "";
		$filename = realpath(utils_getRelativePath() . "/templates/mail_template.htm");
		if (!$filename) { $filename = realpath(utils_getRelativePath() . "/templates/mail_template.htm"); }
		$fd = fopen ($filename, "r");
		$contents = fread($fd, filesize ($filename));
		fclose ($fd);
		$source =  str_replace("<!--naslov html dokumenta-->", menu_getVerzLabel("mail_headline"), $contents);
		$source = str_replace("<!--naslov maila-->", $subject, $contents);
		$source = str_replace("<!--body maila-->", $message, $source);

		$source = preg_replace('/<img src=("|\')\//i', '<img src=$1' . $serverAddress . "/", $source);
		$source = str_replace('url(', 'url(' .  $serverAddress, $source);
		$source = preg_replace('/<a href=("|\')\//i', '<a href=$1' . $serverAddress . "/", $source);
		$source = str_replace(">", ">\n", $source);

		utils_mail($to, $subject, $source, $headers); 
	}

	/*=================================
	 Check email validity
	 =================================*/
	function utils_validEmail($email_add) {
		$isValid = true;
		$atIndex = strrpos($email_add, "@");
		if (is_bool($atIndex) && !$atIndex) {
			$isValid = false;
		}else {
			$domain = substr($email_add, $atIndex+1);
			$local = substr($email_add, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);
			if ($localLen < 1 || $localLen > 64) {
				   $isValid = false; // local part lenght exceeded
			}
			else if ($domainLen < 1 || $domainLen > 255) {
					 $isValid = false; // domain part lenght exceeded
			 }
			else if ($local[0] == '.' || $local[$localLen-1] == '.') {
					  $isValid = false; // local part starts or ends with '.'
			}
			else if (preg_match('/\\.\\./', $local)) {
					  $isValid = false; // local part has 2 consecutive dots
			}
			else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
					  $isValid = false; // character not valid in domain part
			}
			else if (preg_match('/\\.\\./', $domain)) {
						$isValid = false; // domain part has 2 consecutive dots
			}
			else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
					if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) {
								$isValid = false; //character not valid in local part unless local part is quoted
					 }
			}
			if ($isValid && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
				  $isValid = false; // domain not found in DNS
			}
		}
		return $isValid;
	}
?>