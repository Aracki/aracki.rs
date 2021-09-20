<?php

	function button_html($title, $action){

		?><table cellpadding="0" cellspacing="0" style="float: right; margin-right: 3px;">
			<tr>
				 <td style="cursor:pointer" onclick="<?php echo $action;?>"><img src="/ocp/img/opsti/kontrole/dugme_novi_obj.gif" width="21" height="21" title="<?php echo $title;?>"></td>
				<td style="cursor:pointer" class="ocp_opcije_dugme" onclick="<?php echo $action;?>"><?php echo $title;?></td>
				<td style="cursor:pointer" onclick="<?php echo $action;?>"><img src="/ocp/img/opsti/kontrole/dugme_desni.gif" width="6" height="21" title="<?php echo $title;?>"></td>
			</tr>
		</table><?php
	}
?>