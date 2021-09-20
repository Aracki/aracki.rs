<?php
	require_once("../../ocp/include/connect.php");
	require_once("../../ocp/include/utils.php");
	require_once("../../ocp/include/security.php");
	require_once("../../php/utils/mail.php");
	require_once("../../code/menu_functions.php");
	require_once("../../php/utils/request.php");

	$csrf = new Csrf();

	$kontakt = utils_requestStr(getPVar("kontakt"));
	$id = utils_requestInt(getPVar("id"));
	$name = utils_requestStr(getPVar("name"));
	$email = utils_requestStr(getPVar("email"));
	$text = utils_requestStr(getPVar("message"));

	if ($csrf->checkToken()){

		$subject = "Kontakt forma - poruka sa sajta";
		$title = "Kontakt forma - poruka sa sajta";

		$headers  = 'MIME-Version: 1.0' . "\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\n";
		$headers .= 'From: '.$name.' <'.$email.'>' . "\n";

		
		$message = $title . "<br><br>\n\n";
		$message .= "Poruka od ".$name." E-mail:".$email.":<br>\n";
		$message .= "--------------------------------------------------------------------<br>\n";
		$message .= $text."<br>\n";
		$message .= "--------------------------------------------------------------------<br>\n";
		$message .= "<br><br><br>\n\n\n";

		utils_templateMail($kontakt, $subject, $message, $headers);
		

		$currLocation = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], "/")). menu_getStraLink($id) . "?mess=succ" ;
		header("Location: ".$currLocation);	
		die();
	} else {
		$currLocation = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], "/")). menu_getStraLink($id) . "?mess=insuff" ;
		header("Location: ".$currLocation);	
		die();
	}
?>
