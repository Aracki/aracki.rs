<?php
	session_start();
	session_unset();
	session_cache_expire(10);

	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/connect.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/utils.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/date.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/lockout.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/string.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/tipoviobjekata.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/users.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/log.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/izvestaji.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/siteManager/lib/root.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/siteManager/lib/verzija.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/siteManager/lib/sekcija.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/siteManager/lib/stranica.php");

	$message = "";
	$action = isset($_POST["Action"]) ? utils_requestStr($_POST["Action"]) : "";

	if ($action == "Login") {
		$user = utils_requestStr($_POST["user"]);
		$level = "";

		session_cache_expire(100);
		
		if (lockout_pass(utils_requestStr($_POST["user"]), utils_requestStr($_POST["password"]))){
			$userOK = users_checkLogin(utils_requestStr($_POST["user"]), utils_requestStr($_POST["password"]));
			if ($userOK) {
				log_loggedUser();	// u Session se smesta podatak da li se loguju operacije
				$currLocation = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], "/"))."/frameset.php?".utils_randomQS();
				header("Location: ".$currLocation);	
				die();
			} else {
				lockout_entry(utils_requestStr($_POST["user"]));
				$message = "<br>Login error! Please, check your username and password.<br><br>";
			}
		} else {
			$message = "<br>Login error! Please, check your username and password.<br><br>";
		}
	} else {
		if (!isset($_GET["random"]) || !utils_valid($_GET["random"])){
			header("Location: ./login.php?".utils_randomQS());	
			die();
		}
	}
	
	clearSession();
?>
<HTML>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="css/login.css">
	<script language="javascript" type="text/javascript" src="/ocp/jscript/swfobject.js"></script>
	<title>OCP Login</title>
</head>
<body class="ocp_login_body" onload="document.formObject.user.focus();">
<script src="/ocp/validate/validate_double_quotes.js"></script>
<form action="/ocp/login.php?<?php echo utils_randomQS(); ?>" method="post" name="formObject" class="ocp_login_body" onSubmit="validate_double_quotes(document.formObject); return true;"> 
	<input type="hidden" name="Action" value="Login">
 <table class="ocp_login_table">
    <tr>
      <td class="ocp_login_td_1"><img src="img/login/logo.gif"></td>
    </tr>
	<tr>
     <td class="ocp_login_td_2" >
	  <table border="0" align="center" cellpadding="0" cellspacing="0">
		  <tr>
            <td class="ocp_opcije_tekst1">Username:</td>
            <td>
              <input type="text" name="user" value="" class="ocp_forma">
            </td>
       </tr>   
		<tr>
            <td class="ocp_opcije_tekst1">Password:</td>
            <td>
              <input type="password" name="password" value="" class="ocp_forma">
            </td>
          </tr>

        <tr>
            <td>&nbsp;</td>
            <td style="padding: 4px 0px 0px 1px;">
			  <input type="submit" name="Submit" value="Submit" class="ocp_dugme">
            </td>
          </tr>
      </table></td>
    </tr>
	</table>

	<table align="center">
	<tr>
		<td align="center">
			<span class="ocp_opcije_tekst1" style="color:#c42e00; font-weight: bold;"><?php echo $message;?></span>
		</td></tr>
	<tr>
		<td>
			<div id="login_flash"></div>
			<script type="text/javascript">
			   var so = new SWFObject("flash/detect_v6.swf?noflash=Your Flash player is not updated for OCP. Please visit www.macromedia.com and download updated version.", "mymovie", "700", "50", "6", "#ffffff", "transparent");
			   so.write("login_flash");
			</script></td>
	</tr>
</table>
<div align="center"></div> 
</form> 
</body>

</html>

<?php
	function clearSession(){
		session_unset();
	}
?>