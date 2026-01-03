<?php
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/connect.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/session.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/log.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/tipoviobjekata.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/objekti.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/language.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/users.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/siteManager/lib/stranica.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/siteManager/lib/root.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/design/home.php");
?>

<html>
<head>
<title>OCP</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="/ocp/css/home.css" rel="stylesheet" type="text/css">
<link href="/ocp/css/opsti.css" rel="stylesheet" type="text/css">
<link href="/ocp/css/opcije.css" rel="stylesheet" type="text/css">

<script>
	function openPopupWindow(url, name, width, height) { 
		w = 400; h = 400;
		if (width != null){ w = width;}
		if (height != null){ h = height;}
		var x = window.open(url, name, "top=10, left=50, width=" + w + ", height=" + h + ", scrollbars=yes, resizable=yes, status=yes");
		x.focus();
	}
</script>
</head>
<body class="ocp_body"><?php

	$type = utils_requestStr(getGVar("type"));

	if (!utils_valid($type)){//posle submit-a
		$type = utils_requestStr(getPVar("type"));
		
		$log = getSVar("Log");
		$logRequest = utils_requestInt(getPVar("Log"));

		if (utils_valid($logRequest) && ($logRequest == 1)){//log
			if (!utils_valid($log) || ($log == 0)){//enable loga
				con_update("update Ocp set Log = 1 where Id = 1");
				setSVar("Log", 1);
			}
		} else {
			if (utils_valid($log) && ($log == 1)){//disable loga
				con_update("update Ocp set Log = 0 where Id = 1");
				unset($_SESSION["Log"]);
			}
		}

		//multilanguage support
		$ocpLanguage = utils_requestInt(getPVar("OcpLanguage"));
		$multiSupport = utils_requestInt(getPVar("MS"));
		if (utils_valid($ocpLanguage) && ($ocpLanguage != 0)){
			users_saveSettings(getSVar("ocpUserGroup"), getSVar("ocpUserId"), getSVar("ocpUserWidth"), getSVar("ocpUserHeight"), $ocpLanguage);
			setSVar("ocpLabels", con_getResultsDict("select Labela, Vrednost from OcpPrevod, OcpLabela where IdLabele = OcpLabela.Id and IdJezika=".$ocpLanguage));
?><script>
			top.parent.location.href = "/ocp/frameset.php?<?php echo utils_randomQS();?>";
</script><?php
		} else if (utils_valid($multiSupport) && ($multiSupport != 0)){//povratak na multilanguage support
			con_update("update Ocp set OcpJezik = NULL where Id=1");
			unset($_SESSION["ocpLanguage"]);
		}
	}

?><form action="/ocp/home.php?<?php echo utils_randomQS();?>" method="post" name="formObject">
		<input type="hidden" name="type" value="<?php echo $type;?>"><?php
	home_welcomeNote();
 //svi useri
	home_shortcuts();
	if ($type == "siteManager"){ //siteManager
		//last edited pages
		home_lastEditedHeader("page");
		$lastPagesLogs = log_getLastEditedPages();
		for ($i=0; $i<count($lastPagesLogs); $i++) {
			home_lastEditedRow($lastPagesLogs[$i], "page");}
	?></table><?php
		//last edited objects
		home_lastEditedHeader("object");
		$lastObjectsLogs = log_getLastEditedObjects();
		for ($i=0; $i<count($lastObjectsLogs); $i++) {
			home_lastEditedRow($lastObjectsLogs[$i], "object");}
	?></table><?php	
	} else if ($type == "objectManager"){
		//last edited objects
		home_lastEditedHeader("object");
		$lastObjectsLogs = log_getLastEditedObjects();
		for ($i=0; $i<count($lastObjectsLogs); $i++) {
			home_lastEditedRow($lastObjectsLogs[$i], "object");}
	?></table><?php
		//last edited pages
		home_lastEditedHeader("page");
		$lastPagesLogs = log_getLastEditedPages();
		for ($i=0; $i<count($lastPagesLogs); $i++) {
			home_lastEditedRow($lastPagesLogs[$i], "page");}
	?></table><?php
	}
 //administratorski deo
	if (getSVar("ocpUserGroup") == "null") {
		//login-ovi
		home_lastLoginHeader("login");
		$lastUsersLogged = log_getLastLoggedUsers();
		for ($i=0; $i<count($lastUsersLogged); $i++) {
			home_lastLoginRow($lastUsersLogged[$i]);}
		//log records
		home_lastLoginHeader("log records");
		home_logRecordsRow(log_count());
	?></table><?php
		//settings
?><table class="ocp_opcije_table" style="border-bottom: 0;">
    <tr>
      <td width="53" rowspan="8" class="ocp_opcije_td_ikona"><img src="/ocp/img/home/podesavanja.gif" width="39" height="33"></td>
      <td colspan="2" class="ocp_opcije_td_naslov"><?php echo ocpLabels("SETTINGS");?></td>
    </tr>
    <tr>
      <td width="653" class="ocp_opcije_td_header" style="white-space: nowrap;"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("TYPE");?></span></td>
      <td width="265" class="ocp_opcije_td_header" style="white-space: nowrap;"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("STATE");?>/<?php echo ocpLabels("VALUE");?></span></td>
    </tr>
	<tr>
      <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("activity log enabled");?></span></td>
      <td class="ocp_opcije_td"><input type="checkbox" name="Log" value="1" <?php if (getSVar("Log")) echo("checked");?>></td>
    </tr><?php
	if (!utils_valid(getSVar("ocpLanguage"))){	
	?><tr>
      <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("switch ocp to this language");?></span></td>
      <td class="ocp_opcije_td"><select name="OcpLanguage" style="width: 100%;" class="ocp_forma"><?php
		$langs = lang_getJezici("Jezik", "asc");
	  ?><option value="">--<?php echo ocpLabels("choose language");?>--</option><?php
		for ($i=0; $i<count($langs); $i++) {
			echo('<option value='.$langs[$i]["Id"].' '.(getSVar("ocpUserLanguage") == $langs[$i]["Id"] ? 'selected' : '').'>'.$langs[$i]["Jezik"].'</option>');}		
	  ?></select></td>
    </tr><?php
	} else {
	?><tr>
      <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("turn multilanguage support on");?></span></td>
      <td class="ocp_opcije_td"><input type="checkbox" name="MS" value="1"></td>
    </tr><?php	
	}
?></table>
<table width="100%">
	<tr>
		<td height="40" align="center" class="ocp_text"><input type="submit" name="submitSave" class="ocp_dugme" value="<?php echo ocpLabels("Save");?>">&nbsp;<input type="button" name="submitCancel" class="ocp_dugme" onClick="document.formObject.reset();" value="<?php echo ocpLabels("Cancel");?>"></td>
	</tr>
</table>
<?php
	}
?></form></body>
</html>
