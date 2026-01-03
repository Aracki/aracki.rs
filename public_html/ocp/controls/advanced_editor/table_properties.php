<?php 
	require_once("../../include/session.php");
	require_once("../../config/table.php");
?><html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link href="/ocp/css/opcije.css" rel="stylesheet" type="text/css">
	<link href="/css/general.css" rel="stylesheet" type="text/css">
	<script>
		labClickCell = "<?php echo ocpLabels("labClickCell")?>";
		IE = (navigator.userAgent.indexOf("Firefox") == -1) ? true : false;
		var TABLE = null;
		var fobj = null;

		function init () {
/*			if(IE){
				TABLE = window.dialogArguments;
			} else {*/
				var cell=window.opener.cellSelect ;
				if(!cell){ alert(labClickCell); self.close();return}
				TABLE= cell.parentNode.parentNode.parentNode
//			}

			fobj = document.formObject;  
			with(TABLE){
				if (!align) align = "";
				al= align.toUpperCase();
				fobj.Alignment.value = al;
				fobj.Border.value = className;
				
			}
			// alert(TABLE.className)
		}
		
		function submitTableProperties(){
			with(TABLE){
				align = fobj.Alignment.value;
				className = fobj.Border.value;
				// alert (align + " | " + className);
			}
			window.close();
		}
	</script>
</head>
<body onload="init();" scroll="no" class="ocp_blokovi_body" style="background: #e8e8e8;"> 
  <table width="100%" class="ocp_blokovi_td"> 
    <tr> 
	 <td class="ocp_blokovi_td" style="padding: 0; padding-left:5px; color: #4c4e4e; font-weight: bold; font-size: 11px;"><img src="/ocp/img/kontrole/napredni_edit/dugmici/tabprop.gif" style="vertical-align: middle;"><?php echo ocpLabels("Edit table properties")?>:</td> 
    </tr> 
</table> 
  <table class="ocp_opcije_table">
	<form name="formObject">
    <tr>
      <td class="ocp_opcije_td" ><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Alignment")?>:</span></td>
      <td class="ocp_opcije_td" >
		<select name="Alignment" class="ocp_forma" >
			<option value=""><?php echo ocpLabels("Default")?></option>
			<option value="LEFT"><?php echo ocpLabels("Left")?></option>
			<option value="CENTER"><?php echo ocpLabels("Center")?></option>
			<option value="RIGHT"><?php echo ocpLabels("Right")?></option>
      </select></td>
      </tr>
    <tr>
      <td class="ocp_opcije_td" ><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Border")?>:</span></td>
      <td class="ocp_opcije_td" >
		<select name="Border" class="ocp_forma"><?php
			for ($k=0; $k<count($tableArr); $k++){
				?><option value='<?php echo $tableArr[$k]?>'><?php echo ocpLabels($tableLabelsArr[$k])?></option><?php
			}
			?></select>
	  </td>
    </tr>
    <tr>
      <td align="center" class="ocp_opcije_td" >&nbsp;</td>
      <td class="ocp_opcije_td" ><table border="0" cellspacing="0" cellpadding="1">
        <tr>
          <td>
			<button onclick="submitTableProperties()" title="Button" type="button" class="ocp_dugme_malo"><?php echo ocpLabels("Confirm")?></button>
          </td>
          <td>
			<button onclick="self.close()" title="Back" type="button" class="ocp_dugme_malo"><?php echo ocpLabels("Cancel")?></button>
          </td>
        </tr>
      </table></td>
    </tr>
	</form>
  </table>
</body>
</html>