<?php

	$no_of_tries = 5;
	$minutes_to_wait = 60;
	$emails = "info@omnicom.rs,milosevic@omnicom.rs,mackovic@omnicom.rs";

	// da li postoji vise od $no_of_tries pokusaja neuspesnih za posledjih $minutes_to_wait minuta
	function lockout_pass($user, $pass){
		global $no_of_tries, $minutes_to_wait, $emails;

		$strSQL = "select count(*) from Logs where Akcija='Failure Login' and Username='".$user."' and (TIMESTAMPDIFF(MINUTE, Datum, now()) <= ".$minutes_to_wait.")";
		
		$cn=new dbase();
		$cn->open();
		
		$retVal = 0;
					
		$res = $cn->query($strSQL);
		if (!is_null($res)){
			if ($record = mysql_fetch_array($res)) $retVal = $record[0];
			$cn->close();
		}

//echo ($retVal . "<br/>");

		if ($retVal >= $no_of_tries){
			//slanje maila o lockoutu
			$sql = "select count(*) from Logs where Akcija='Failure Login Sent Mail' and Username='".$user."' and (TIMESTAMPDIFF(MINUTE, Datum, now()) <= ".$minutes_to_wait.")";

			$sentMail = 0;
					
			$res = $cn->query($sql);
			if (!is_null($res)){
				if ($record = mysql_fetch_array($res)) $sentMail = $record[0];
			}

			
			if ($sentMail == 0){
				$mail_domain = $_SERVER['SERVER_NAME'];
				if (substr_count($mail_domain, "www.") > 0){
					$mail_domain = substr($mail_domain, strpos($mail_domain, "www.") + 4);
				}

				$headers  = 'MIME-Version: 1.0' . "\n";
				$headers .= 'Content-type: text/html; charset=UTF-8' . "\n";
				$headers .= 'From: ocp@' .$mail_domain. "\n";

				$message = "Desio se lockout za username '".$user."' na sajtu " . $_SERVER['SERVER_NAME'] . " <br /><br />\n";

				$http_headers = @apache_request_headers();
				if (isset($http_headers) && is_array($http_headers)){
					foreach ($http_headers as $key => $value) {
						$message .= "<b>" . $key . ":</b> " . $value . " <br />\n";
					}
				}
				
				$message .= "<br />\n";

				foreach ($_SERVER as $key => $value){
					$message .= "<b>" . $key . ":</b> " . $value . " <br />\n";
				}

				$sql = "insert into Logs (UserGroupId, UserName, Akcija, TipObjekta, IdObjekta, IdenObjekta, Datum) ";
				$sql .= "values (1, '".$user."', 'Failure Login Sent Mail', 'Login', 0, 'Login', NOW())";

				$cn->query($sql);
				@mail($emails, $_SERVER['SERVER_NAME'] . " lockout" , $message, $headers);
			}
			return false;
		}
		
		return true;
	}
	
	//zapis u bazu neuspesnog pokusaja
	function lockout_entry($user){
		$sql = "select count(*) from Users where User_Name='".$user."'";

		$cn=new dbase();
		$cn->open();
		
		$retVal = 0;
					
		$res = $cn->query($sql);
		if (!is_null($res)){
			if ($record = mysql_fetch_array($res)) $retVal = $record[0];
		}

		if ($retVal > 0){
			$sql = "insert into Logs (UserGroupId, UserName, Akcija, TipObjekta, IdObjekta, IdenObjekta, Datum) ";
			$sql .= "values (1, '".$user."', 'Failure Login', 'Login', 0, 'Login', NOW())";

//echo ("upisujem<br/>");

			$cn->query($sql);
			$id = mysql_insert_id($cn->link);
		}
		$cn->close();
	}

?>
