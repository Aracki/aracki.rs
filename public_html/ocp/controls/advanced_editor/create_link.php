<?php 
	require_once("../../include/session.php");
?><HTML>
<HEAD>
<TITLE>Link editor</TITLE>
<link rel="stylesheet" href="/ocp/css/opcije.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script src="/ocp/controls/advanced_editor/create_link.js"></script>
</HEAD>
<body scroll=no onload=init() class="ocp_blokovi_body" style="background: #e8e8e8;">
	<form id="formaZaLink" name="formaZaLink" onsubmit="return false;">
	<input type="hidden" name="type" value="http://">
		<table class="ocp_blokovi_td" width="100%"> 
		<tr> 
		 <td class="ocp_blokovi_td" style="padding: 0; padding-left:5px; color: #4c4e4e; font-weight: bold; font-size: 11px;"><img src="/ocp/img/kontrole/napredni_edit/dugmici/link.gif" style="vertical-align: middle;"><?php echo ocpLabels("Create link")?></td> 
		</tr> 
	  </table> 
		<table class="ocp_opcije_table"> 
			<tr> 
				<td>
					<table class="ocp_uni_table"> 
						<tr> 
							<td class="ocp_opcije_td" style="white-space: nowrap; width:120px;"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Address")?></span></td> 
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
							<td class="ocp_opcije_td" ><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Opens in")?>:</span></td> 
							<td class="ocp_opcije_td">
							<table  border="0" cellspacing="0" cellpadding="0"> 
								<tr> 
									<td>
										<select name="target" class="ocp_forma" >
										<option value="_self" selected><?php echo ocpLabels("Current page")?></option>
										<option value="_blank" ><?php echo ocpLabels("New page")?></option>
										</select>
									</td> 
								</tr> 
							</table>
						</td> 
					</tr> 
					<tr>
						<td class="ocp_opcije_td" style="border-bottom: 1px solid #999;">&nbsp;</td>
						<td class="ocp_opcije_td" style="border-bottom: 1px solid #999;">
							<table border="0" cellspacing="0" cellpadding="1">
								<tr>
									<td>
									<td><input type="button" name="Create" onclick="doCreate()" value="<?php echo ocpLabels("Create")?>" class="ocp_dugme_malo"></td>
									<td><input type="button" name="Cancel" onclick="window.close()" value="<?php echo ocpLabels("Cancel")?>" class="ocp_dugme_malo"></td>
									<td><input type="button" name="Unlink" onclick="doUnLink()" value="<?php echo ocpLabels("Unlink")?>" class="ocp_dugme_malo"></td>
								<tr>
							</table>
						</td>
					</tr>
			</table>
	</form>
</div>
</BODY>
</HTML>
