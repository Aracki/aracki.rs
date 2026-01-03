<?php
require_once("../../include/connect.php");
require_once("../../include/session.php");
require_once("../../include/users.php");
require_once("../../include/language.php");
?>

<?php
	$Akcija = utils_requestStr(getPVar("Akcija"));
	$poruka = "";
	
	if (utils_valid($Akcija)){
		switch ($Akcija){
			case "IzmenaPassworda": 
				$oldpass = utils_requestStr(getPVar("oldpass"));
				$newpass1 = utils_requestStr(getPVar("newpass1"));
				$newpass2 = utils_requestStr(getPVar("newpass2"));

				if (utils_valid($oldpass) && utils_valid($newpass2)) {
					$tempold = users_getUserById(getSVar("ocpUserId"));
					$tempold = $tempold["User_Password"];

					$oldpass = md5($oldpass);
	
					if (!utils_valid($tempold) || ($tempold != $oldpass)) {
						$poruka = ocpLabels('Password is not correct. Try again.');
					} else if (!utils_valid($newpass1) && !utils_valid($newpass2)) {
						$poruka = ocpLabels('Password is not correct. Try again.');
					} else if ($newpass1 != $newpass2) {
						$poruka = ocpLabels('Password is not correct. Try again.');
					} else {
						$poruka = ocpLabels('Changes were succesefully saved.');
						users_savePassword(getSVar("ocpUserId"), $newpass1);
					}
				} else {
					$poruka = ocpLabels('Password is not correct. Try again.');
				}
				break;
			case "IzmenaSirine":
				$width = utils_requestInt(getPVar("width"));
				$height = utils_requestStr(getPVar("height"));
				if (utils_valid($width) && ($width != 0)){
					users_saveSettings(getSVar("ocpUserGroup"), getSVar("ocpUserId"), $width, $height, NULL);
					setSVar("ocpUserWidth", $width);
					setSVar("ocpUserHeight", $height);
					$poruka = ocpLabels('Changes were succesefully saved.');
				}
				break;
			case "IzmenaJezika":
				$userLanguage = utils_requestInt(getPVar("userLanguage"));
				$newLanguage = utils_requestInt(getPVar("newLanguage"));

				if (utils_valid($userLanguage) && utils_valid($newLanguage) && 
					($newLanguage != 0) && ($userLanguage != $newLanguage)){
					users_saveSettings(getSVar("ocpUserGroup"), getSVar("ocpUserId"),getSVar("ocpUserWidth"), getSVar("ocpUserHeight"), $newLanguage);
				}
				if (utils_valid($newLanguage) && ($newLanguage != 0)){
					setSVar("ocpLabels", con_getResultsDict("select Labela, Vrednost from OcpPrevod, OcpLabela where IdLabele = OcpLabela.Id and IdJezika=".$newLanguage));
				
					$poruka = ocpLabels('Changes were succesefully saved.');
?><script>if (opener){
				opener.top.location.href = "/ocp/frameset.php?<?php echo utils_randomQS();?>";
			}</script><?php
				} else {
					$poruka = ocpLabels('Changes were succesefully saved.');
				}
				break;
		}
	}	
	$sessOcpLabels = getSVar("ocpLabels");
?>
<html>
<head>
	<TITLE>OCP</TITLE>
	<link rel="STYLESHEET" type="text/css" href="/ocp/css/style_ocp.css">
	<link rel="STYLESHEET" type="text/css" href="/ocp/css/gornji.css">
	<link rel="STYLESHEET" type="text/css" href="/ocp/css/opcije.css">
	<link rel="STYLESHEET" type="text/css" href="/ocp/css/dugmici.css">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script>
		function getUserWidthAndHeight(){
			if (opener){
				var cols = opener.parent.parent.document.getElementById("leftFrameset" ).getAttribute("cols");
				if (cols.indexOf(",") > -1){
					document.changeUserWidth.width.value=cols.substring(0, cols.indexOf(","));
				}

				if (opener.parent.document.getElementById("resizableFrameset") != null){
					var rows = opener.parent.document.getElementById("resizableFrameset").getAttribute("rows");
					if (rows.indexOf(",") > -1){
						document.changeUserWidth.height.value=rows.substring(0, rows.indexOf(","));
					}
				}
			}
			return true;
		}
	</script>
</head>
<body class="ocp_gornji_2_body">
<div class="ocp_gornji_body"> 
  <table class="ocp_gornji_table" style="height:30px;"> 
    <tr> 
      <td class="ocp_gornji_td_levi"><table cellpadding="0" cellspacing="0"> 
          <tr> 
            <td align="left" class="ocp_gornji_title"><?php echo ocpLabels('Settings');?></td> 
          </tr> 
        </table></td> 
    <td class="ocp_gornji_td_desni" onclick="top.close();" style="cursor:pointer;"><?php echo ocpLabels("close window");?></td>
    </tr> 
  </table> 
</div> 
<div class="ocp_gornji_2_body"> 
  <table width="100%" class="ocp_gornji_2_table"> 
    <tr> 
      <td valign="top" class="ocp_gornji_2_naslov"><img src="/ocp/img/kontrole/podesavanja/ikona_pored_naslova.gif" width="15" height="15" border="0" align="absbottom" style="margin-right: 5px; margin-top: 3px;"><?php echo ocpLabels('Username');?> <b><?php echo getSVar("ocpUsername");?></b> </td> 
    </tr> 
  </table> 
  <table class="ocp_opcije_table" border="0"> 
    <tr> 
      <td colspan="2" class="ocp_opcije_td_naslov"><?php echo ocpLabels('Password change');?></td> 
    </tr>

<?php	if (($Akcija == "IzmenaPassworda") && utils_valid($poruka)) {	?>
	<tr> 
      <td align="left" class="ocp_opcije_td" colspan="2"><span class="ocp_opcije_tekst1 ocp_opcije_obavezno"><?php echo $poruka;?></span></td>   
    </tr> 
<?php	}	?>
<script src="/ocp/validate/validate_double_quotes.js"></script>
<form action="/ocp/admin/users/user_settings.php?<?php echo utils_randomQS();?>" name="changePass" method="POST" onSubmit="validate_double_quotes(changePass); return true;">
<input type="hidden" name="Akcija" value="IzmenaPassworda">
<tr> 
      <td align="left" class="ocp_opcije_td" style="width:50%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels('Active password');?></span></td> 
      <td class="ocp_opcije_td"><input name="oldpass" type="password" class="ocp_forma" style="width:100%;">
	  </td> 
    </tr> 
    <tr> 
      <td align="left" class="ocp_opcije_td" style="width:50%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels('New password');?></span></td> 
      <td class="ocp_opcije_td"><input name="newpass1" type="password" class="ocp_forma" style="width:100%;">
	  </td> 
    </tr> 
    <tr> 
      <td align="left" class="ocp_opcije_td" style="width:50%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels('New password');?></span></td> 
      <td class="ocp_opcije_td"><input name="newpass2" type="password" class="ocp_forma" style="width:100%;"></td> 
    </tr>
    <tr>
      <td align="left" class="ocp_opcije_td" style="width:50%">
     </td>
      <td align="left" class="ocp_opcije_td">
        <input type="submit" class="ocp_dugme_malo" value="<?php echo ocpLabels('Save');?>">
      </td>
    </tr>
</form>
</table> 

<table class="ocp_opcije_table" border="0">
    <tr>
      <td colspan="2" class="ocp_opcije_td_naslov"><?php echo ocpLabels('OCP layout');?></td>
    </tr>
<?php	if (($Akcija == "IzmenaSirine") && utils_valid($poruka)) {	?>
<tr>
      <td align="left" class="ocp_opcije_td" colspan="2"><span class="ocp_opcije_tekst1 ocp_opcije_obavezno"><?php echo $poruka;?></span></td>
</tr>
<?php	} ?>
<form action="/ocp/admin/users/user_settings.php?<?php echo utils_randomQS();?>" name="changeUserWidth" method="POST" onSubmit="return getUserWidthAndHeight();">
<input type="hidden" name="Akcija" value="IzmenaSirine">
<input type="hidden" name="width" value="">
<input type="hidden" name="height" value="">
    <tr>
      <td align="left" class="ocp_opcije_td" style="width:50%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels('Save current layout');?>.</span></td>
      <td align="left" class="ocp_opcije_td"><input type="submit" class="ocp_dugme_malo" value="<?php echo ocpLabels('Save');?>"></td>
    </tr>
</form>
</table>
<?php
	
	if (!utils_valid(getSVar("ocpLanguage")) || (getSVar("ocpLanguage") == 0)){
?>

  <table class="ocp_opcije_table" border="0">
    <tr>
      <td colspan="2" class="ocp_opcije_td_naslov"><?php echo ocpLabels('Language');?></td>
    </tr>
<?php		if (($Akcija == "IzmenaJezika") && utils_valid($poruka)) {	?>
<tr>
      <td class="ocp_opcije_td" colspan="2"><span class="ocp_opcije_tekst1 ocp_opcije_obavezno"><?php echo $poruka;?></span></td>
</tr>
<?php		}
			$jezici = lang_getJezici(NULL, NULL);
			// var_dump ($jezici);
			$userJezik = con_getValue("select User_Language from Users where User_Id=".getSVar("ocpUserId"));
?>
<tr>
<form action="/ocp/admin/users/user_settings.php?<?php echo utils_randomQS();?>" method="POST">
				<input type="hidden" name="Akcija" value="IzmenaJezika">
				<input type="hidden" name="userLanguage" value="<?php echo $userJezik;?>">

      <td class="ocp_opcije_td" style="width:50%"><span class="ocp_opcije_tekst1"><?php echo ocpLabels('Change language');?></span><span class="ocp_opcije_obavezno"></span></td>
      <td valign="top" class="ocp_opcije_td"> 
	  <select name="newLanguage" class="ocp_forma" style="width:100%">
						<?php	for ($i=0; $i<count($jezici); $i++){	
								$selected = ($jezici[$i]["Id"] == $userJezik) ? "selected" : "";	?>
							<option value="<?php echo $jezici[$i]["Id"];?>" <?php echo $selected;?>><?php echo $jezici[$i]["Jezik"];?></option>
						<?php	}	?>
				</select>
	  	  </td>
    </tr>
	 <tr>
      <td align="left" class="ocp_opcije_td" style="width:50%">
     </td>
      <td align="left" class="ocp_opcije_td">
        <input type="submit" class="ocp_dugme_malo" value="<?php echo ocpLabels('Set new language');?>">
      </td>
    </tr>
<?php			
	}
?>
</table>
</div>
</body>
</html>
