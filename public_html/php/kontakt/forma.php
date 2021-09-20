<?php
	require_once("../ocp/include/security.php");
	
	$params = $_SESSION["urlParams"];
	$contact = $params["contact"];
	$contact = str_replace("%40", "@", $contact);
	unset($_SESSION["urlParams"]);

	$mess = utils_requestStr(getGVar("mess"));
	$messExpanded = NULL;

	switch ($mess)
	{
		case "succ": $messExpanded = menu_getVerzLabel("contact_message"); break;
		case "insuff": $messExpanded = menu_getVerzLabel("contact_error"); break;
	}

	global $Id;

	$csrf = new Csrf();
	$csrf->createToken();
?>
<div id="contactForm" class="block">
	<h3><?php echo menu_getVerzLabel("contact_welcome"); ?></h3>

	<form name="formContact" id="formContact" method="post" onSubmit="return validate();" action="/php/kontakt/posalji.php">
		<?php $csrf->getFormToken();?>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<?php
			if (utils_valid($messExpanded))
			{
		?>
			<tr>
				<td align="left" valign="middle">
					<b><?php echo $messExpanded; ?></b>
				</td>
			<tr>
		<?php
			}
			else
			{
		?>
			<tr> 
				<td>
					<table border="0" cellspacing="3" cellpadding="1">
						<tr> 
							<td align="right">* <?php echo menu_getVerzLabel("contact_name"); ?></td>
							<td align="left"> 
								<input type="text" name="name" size="25">
							</td>
						</tr>
						<tr> 
							<td align="right">* E-mail:</td>
							<td align="left"> 
								<input type="text" name="email" size="25">
							</td>
						</tr>
						<tr> 
							<td align="right" valign="top">* <?php echo menu_getVerzLabel("contact_text"); ?></td>
							<td align="left" valign="top"> 
								<textarea name="message"></textarea>
							</td>
						</tr>
						<tr> 
							<td align="left" valign="top">&nbsp;</td>
							<td align="left" valign="top"> 
								<input type="hidden" name="kontakt" value="<?php echo $contact; ?>">
								<input type="hidden" name="id" value="<?php echo $Id; ?>">

								<input type="submit" name="Submit" class="button" value="<?php echo menu_getVerzLabel("contact_submit"); ?>">
								<input type="reset" name="Reset" class="button" value="<?php echo menu_getVerzLabel("contact_reset"); ?>">
							</td>
						</tr>
						<tr> 
							<td align="center" colspan="2"><br><?php echo menu_getVerzLabel("contact_necessary"); ?></td>
						</tr>
					</table>
				</td>
			</tr>
		<?php
			}
		?>
		</table>
	</form>
</div>
 
<script language="javascript" type="text/javascript">
	function validate()
	{
		forma = document.formContact;

		if ((forma.name.value == "") || (forma.email.value == ""))
		{
			alert("<?php echo menu_getVerzLabel("contact_necessary_name"); ?>");
			return false;
		}

		if (forma.message.value == "")
		{
			alert("<?php echo menu_getVerzLabel("contact_necessary_text"); ?>");
			return false;
		}

		return true;
	}
</script>