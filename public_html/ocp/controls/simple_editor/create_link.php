<?php 
	require_once("../../include/session.php");
?><HTML>
<HEAD>
<TITLE>Link editor</TITLE>
<link rel="stylesheet" href="/ocp/css/opcije.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script src="/ocp/controls/advanced_editor/create_link.js"></script>
</HEAD>
<body scroll=no onload=init() class="ocp_blokovi_body">
	<form id="formaZaLink" name="formaZaLink">
	<input type="hidden" name="type" value="http://">
		<table class="ocp_blokovi_td" width="100%"> 
		<tr> 
		  <td class="ocp_blokovi_td" style="padding-left:5px; font-weight: bold;"><?php echo ocpLabels("Create link")?></td> 
		</tr> 
	  </table> 
		<table class="ocp_opcije_table"> 
			<tr> 
				<td>
					<table class="ocp_uni_table"> 
						<tr> 
							<td class="ocp_opcije_td" style="white-space: nowrap; width:100px;"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Address")?></span></td> 
							<td class="ocp_opcije_td">
								<table  border="0" cellspacing="0" cellpadding="0"> 
									<tr> 
										<td><input name="href" value="" class="ocp_forma" style="width:200px"></td> 
										<td style="padding-left:5px;"><img src="/ocp/img/opsti/kontrole/kontrola_browse_page.gif" border="0"  class="ocp_kontrola" title="<?php echo ocpLabels("Create link on OCP page")?>" onClick="anchorPage()" style="cursor:pointer"><img src="/ocp/img/opsti/kontrole/kontrola_browse.gif" width="20" height="21" class="ocp_kontrola" title='<?php echo ocpLabels("Browse server")?>' onclick='anchorDocument()' style="cursor:pointer"><img src="/ocp/img/opsti/kontrole/kontrola_browse_blok.gif" width="20" height="21" class="ocp_kontrola" title='<?php echo ocpLabels("Create link on block")?>' onclick='anchorBlock()' style="cursor:pointer"></td> 
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class="ocp_opcije_td"  style="white-space: nowrap; width:100px;"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Opens in")?>:</span></td> 
							<td class="ocp_opcije_td">
							<table  border="0" cellspacing="0" cellpadding="0"> 
								<tr> 
									<td>
										<select name="target" class="ocp_forma" >
										<option value="_self" selected><?php echo ocpLabels("Current page")?></otprion>
										<option value="_blank" ><?php echo ocpLabels("New page")?></option>
										</select>
									</td> 
								</tr> 
							</table>
						</td> 
					</tr> 
					<tr>
						<td class="ocp_opcije_td">&nbsp;</td>
						<td class="ocp_opcije_td" style="white-space: nowrap; width:100px;">
							<table border="0" cellspacing="0" cellpadding="1">
								<tr>
									<td><BUTTON onclick="doCreate()" class="ocp_dugme_malo"><?php echo ocpLabels("Create")?></button></td><td><BUTTON onclick="window.close()" class="ocp_dugme_malo"><?php echo ocpLabels("Cancel")?></button></td><td><BUTTON onclick="doUnLink()" class="ocp_dugme_malo"><?php echo ocpLabels("Unlink")?></button></td>
								<tr>
							</table>
						</td>
					</tr>
			</table>
	</form>
</div>
</BODY>
</HTML>
