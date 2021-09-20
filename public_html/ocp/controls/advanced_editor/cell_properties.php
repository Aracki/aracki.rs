<?php 
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../config/table.php");
?><html>
<head>
<title> OCP </title>
<link href="/ocp/css/opcije.css" rel="stylesheet" type="text/css">
<link href="/css/general.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script>
	var labClickCell = "<?php echo ocpLabels("labClickCell")?>";
	var IE = (navigator.userAgent.indexOf("Firefox") == -1) ? true : false;
	var fobj = null;

	var cells = null;

	function init(){
		cells = window.opener.cellsSelected;
		
		if (cells == null){
			var cell=window.opener.cellSelect;
			
			if(cell == null){ 
				alert(labClickCell); 
				self.close();
				return;
			} else {
				cells = new Array();
				cells[0] = cell;
			}
		}

		fobj=document.formObject;  
		
		if (cells.length >= 1){
			with(cells[0]) {
				fobj.OP_align.value= align.toUpperCase();
				fobj.OP_valign.value= vAlign.toUpperCase();
				fobj.OP_style.value = className;
			}
			if (fobj.OP_align.selectedIndex == 0) fobj.OP_align.selectedIndex =1;
			if (fobj.OP_valign.selectedIndex == 0) fobj.OP_valign.selectedIndex =1;
			if (fobj.OP_style.selectedIndex == 0) fobj.OP_style.selectedIndex =1;
		}
	}

	function retcellPropert(){
		for (var i=0; i<cells.length; i++){
			with(cells[i]){
				align = fobj.OP_align.value;
				vAlign = fobj.OP_valign.value;
				className = fobj.OP_style.value;
			}
		}

		if (IE) window.opener.deselectCells();
		
		window.close();
	}
</script>
</head>
<body onLoad="init()" scroll="no" class="ocp_blokovi_body" style="background: #e8e8e8;">
<form name="formObject"> 
<table width="100%" class="ocp_blokovi_td">
    <tr> 
		<td class="ocp_blokovi_td" style="padding: 0; padding-left:5px; color: #4c4e4e; font-weight: bold; font-size: 11px;"><img src="/ocp/img/kontrole/napredni_edit/dugmici/cellprop.gif" style="vertical-align: middle;"><?php echo ocpLabels("Cell properties")?>:</td> 
    </tr> 
</table> 
<table class="ocp_opcije_table">
	<tr>
	  <td class="ocp_opcije_td" style="white-space: nowrap; width:120px;">
		<span class="ocp_opcije_tekst1"><?php echo ocpLabels("Hor. alignment")?>:</span></td>
		<td class="ocp_opcije_td"> 
		<select name="OP_align" class="ocp_forma">
				<option value=''>--<?php echo ocpLabels("Choose")?>--</option>
				<option value='LEFT'><?php echo ocpLabels("Left")?></option>
				<option value='CENTER'><?php echo ocpLabels("Center")?></option>
				<option value='RIGHT'><?php echo ocpLabels("Right")?></option>
		  </select>
		</td>
	</tr>
	<tr>
	   <td class="ocp_opcije_td" style="white-space: nowrap; width:120px;">
		<span class="ocp_opcije_tekst1"><?php echo ocpLabels("Ver. alignment")?>:</span></td>
		<td class="ocp_opcije_td">
		<select name="OP_valign" class="ocp_forma">
			<option value=''>--<?php echo ocpLabels("Choose")?>--</option>
			<option value='TOP'><?php echo ocpLabels("Top")?></option>
			<option value='MIDDLE'><?php echo ocpLabels("Middle")?></option>
			<option value='BOTTOM'><?php echo ocpLabels("Bottom")?></option>
		</select>
		</td>
	</tr>
	<tr>
	  <td class="ocp_opcije_td" style="white-space: nowrap; width:120px;">
	  <span class="ocp_opcije_tekst1">
		<?php echo ocpLabels("Cell properties")?>:</span></td>
		<td class="ocp_opcije_td">
			<select name="OP_style" class="ocp_forma">
				<option value=''> --<?php echo ocpLabels("Choose")?>--</option>
				<?php
					for ($k=0; $k<count($cellsArr); $k++){
						?><option value='<?php echo $cellsArr[$k]?>' class='<?php echo $cellsArr[$k]?>'><?php echo ocpLabels($cellsLabelsArr[$k])?></option><?php
					}
				?>
			</select>
		</td>
	</tr>
	<TR>
		<td class="ocp_opcije_td" style="white-space: nowrap; width:120px;"></td>
		<td class="ocp_opcije_td">	
			<table border="0" cellspacing="0" cellpadding="1">
				<tr>
					<td><button onclick="retcellPropert()" title="Button" type="button" class='ocp_dugme_malo'><?php echo ocpLabels("Change")?></button></td>
				    <td><button onclick="self.close()" title="Back" type="button" class='ocp_dugme_malo'><?php echo ocpLabels("Cancel")?></button></td>
				</tr>
			</table>
		</td>
	</tr>
  </table>
</form>
</body>
</html>