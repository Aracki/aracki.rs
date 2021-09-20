<?php 
	require_once("../../include/session.php");
	require_once("../../config/table.php");
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="/ocp/css/opcije.css" rel="stylesheet" type="text/css">
<script src="/ocp/controls/advanced_editor/table_edit.js"></script>
<script>
	IE = (navigator.userAgent.indexOf("Firefox") == -1) ? true : false;
	function init(){
		cellSelect = null;
		TABLE = null;
	}

	function submitTable(){
		if (((document.formObject.rows.value != '') && !isNaN(document.formObject.rows.value))
			&&
			((document.formObject.cols.value != '') && !isNaN(document.formObject.cols.value))){
				insertTableContinue(document.formObject.rows.value, document.formObject.cols.value);
		}
	}

	function insertTableContinue(rows, cols){
		if (IE){
			var el = window.opener.document.getElementById(window.opener.fID).contentWindow;//window.dialogArguments;
			var sel = el.document.selection;

			if(sel.type=="Control") return false; 
			var Range = sel.createRange();
			if(!Range.duplicate) return;
			el.curword=Range.duplicate();
			var wrd = '';
			wrd= el.curword.text;

			if (wrd == null || ((""+wrd) == "") || ((""+wrd) == "undefined")) wrd = "";

			var temp='';											//kreiranje tr i td
			for(var i=0; i<rows; i++){
				temp += "<tr>";
				for(var j=0; j<cols; j++){
					if(j==0 && i==0) temp += "<td class='<?php echo $cellsArr[0]?>'>"+wrd+" </td>";
					else temp += "<td class='<?php echo $cellsArr[0]?>'> </td>";
				}
				temp += "</tr>";
			}
			var TABLESTR ='<table width=\"100%\" class=\"<?php echo $tableArr[0]?>\" cellspacing=\"1\">' + temp + '</table>';

			var Range = sel.createRange();							//ako je nesto bilo selektovano ide u tabelu
			if(!Range.duplicate) return;
			Range.pasteHTML(TABLESTR);
		} else {
			var el = window.opener.document.getElementById(window.opener.fID).contentWindow;
			var sel = el.getSelection();
			var range= sel.getRangeAt(0);

			if (range == null || ((""+range) == "") || ((""+range) == "undefined")) range = "";

			var temp='';											//kreiranje tr i td
			for(var i=0; i<rows; i++){
				temp += "<tr>";
				for(var j=0; j<cols; j++){
					if(j==0 && i==0) temp += "<td class='<?php echo $cellsArr[0]?>'>"+range+" </td>";
					else temp += "<td class='<?php echo $cellsArr[0]?>'> </td>";
				}
				temp += "</tr>";
			}
			var TABLESTR ='<table width=\"100%\" class=\"<?php echo $tableArr[0]?>\" cellspacing=\"1\">' + temp + '</table>';

			window.opener.insertHTML(el,TABLESTR);
			// add event listener
			var tdA= el.document.getElementsByTagName('td');
			for(var i=0; i<tdA.length;i++)
				tdA[i].addEventListener("click", window.opener.clickTD, true);
		}
		
		window.close();
	}

</script>
</head>
<body scroll="no" onLoad="init()" class="ocp_blokovi_body" style="background: #e8e8e8;"> 
<table width="100%" class="ocp_blokovi_td">
  <form name="formObject"> 
    <tr> 
      <td class="ocp_blokovi_td" style="padding: 0; padding-left:5px; color: #4c4e4e; font-weight: bold; font-size: 11px;"><img src="/ocp/img/kontrole/napredni_edit/dugmici/instable.gif" style="vertical-align: middle;"><?php echo ocpLabels("Create table")?>:</td> 
    </tr> 
</table> 
<table class="ocp_opcije_table">
<tr>
  <td class="ocp_opcije_td" style="white-space: nowrap; width:120px;">
	<span class="ocp_opcije_tekst1"><?php echo ocpLabels("Number of rows")?>:</span></td>
	<td class="ocp_opcije_td"> 
		<input name="rows" type="text" class="ocp_forma" size="5" value="1">
	</td>
</tr>
<tr>
	<td class="ocp_opcije_td">
		<span class="ocp_opcije_tekst1"><?php echo ocpLabels("Number of columns")?>:</span>
	</td>
	<td class="ocp_opcije_td">
		<input name="cols" type="text" class="ocp_forma" size="5" value="1">
	</td>
</tr>
 <tr>
  <td class="ocp_opcije_td">
  </td>
<td class="ocp_opcije_td" style="padding-left:5px;">							
	<table border="0" cellspacing="0" cellpadding="1">
		<tr>
			<td><BUTTON onclick="submitTable()" class="ocp_dugme_malo"><?php echo ocpLabels("Create")?></button></td>
			<td><BUTTON onclick="window.close()" class="ocp_dugme_malo"><?php echo ocpLabels("Cancel")?></button></td>
		<tr>
	  </table>
  </td>
</tr>
</form>
</table>
</body>
</html>
