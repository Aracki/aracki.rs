<?php 
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="/ocp/css/opcije.css" rel="stylesheet" type="text/css">
<script src="/ocp/config/colors.js"></script>
<script>
	IE = (navigator.userAgent.indexOf("Firefox") == -1) ? true : false;
	var colorVar= "";
	var td = null;

	function init(){
		var color = "";
		if (IE){
			el = window.opener.document.getElementById(window.opener.fID).contentWindow;//window.dialogArguments;
			sel = el.document.selection;
			
			var temp = el.document.selection.createRange();
			var tag = temp.parentElement();
			if (tag.tagName == "FONT") {
				color = tag.color;
			} else {
				color = tag.style.color;
			}
		} else {
			var win= window.opener.document.getElementById(window.opener.fID).contentWindow;
			var sel= win.getSelection()
			var range= sel.getRangeAt(0);

			if (range == null || ((""+range) == "")) return;

			var container = range.startContainer;
			var el
			
			if(container.nodeType != 1) el= container.parentNode
			else el= container

			if (el.nodeName == "FONT") {
				color = el.color;
			} else {
				color = el.style.color;
			}
		}
		td = document.getElementById("sample");
		setLocalColor (color);
	}

	function setLocalColor (color) {
		td.style.backgroundColor = color;
		colorVar = color;
	}

	function setColor () {
		if (colorVar != "") {
			if (IE){
				el = window.opener.document.getElementById(window.opener.fID).contentWindow;//window.dialogArguments;
				el.document.execCommand('ForeColor', true, colorVar);
			} else {
				window.opener.doFormatF('ForeColor,' + colorVar);
			}
		} else {
			cleanFontTag();
		}
		//window.close();
	}

	/*Brise font tag
	================*/
	function cleanFontTag(){
		if (!IE){
			window.opener.doFormatF('RemoveFormat');
		} else {
			el = window.opener.document.getElementById(window.opener.fID).contentWindow;//window.dialogArguments;
			var MARK= "!$#$#!";
			var selType=el.document.selection.type;

			if(selType!="Control"){
				var caret=el.document.selection.createRange();
				var sadrzaj = caret.text;
				
				if (sadrzaj != ""){
					el.document.execCommand('RemoveFormat', false, null);
				} else {
					var caret=el.document.selection.createRange();
					el.curword=caret.duplicate();
					el.curword.text = MARK;

					var inner= el.document.body.innerHTML
					var leftString = inner.substring(0, inner.indexOf(MARK));
					var rightString = inner.substring(inner.indexOf(MARK) + MARK.length, inner.length);
					
					var startFont = leftString.lastIndexOf("<FONT");
					var endFont = rightString.indexOf("</FONT>"); 

					if (startFont == -1 || endFont == -1){
						inner = inner.replace(MARK, "");
						el.document.body.innerHTML = inner;
						return;
					}
					leftString = leftString.substring(0, startFont) + leftString.substring(leftString.indexOf(">", startFont)+1, leftString.length);
					rightString = rightString.substring(0, endFont) + rightString.substring(endFont+7, rightString.length);

					inner = leftString + rightString;
					inner = inner.replace(MARK, "");

					el.document.body.innerHTML = inner;
				}
			}
		}
	}

</script>
</head>
<body scroll=no onLoad=init() class="ocp_blokovi_body" style="background: #e8e8e8;"> 
<table width="100%" class="ocp_blokovi_td">
  <form name="formObject" onsubmit="return false;"> 
    <tr> 
      <td class="ocp_blokovi_td" style="padding: 0; padding-left:5px; color: #4c4e4e; font-weight: bold; font-size: 11px;"><img src="/ocp/img/kontrole/napredni_edit/dugmici/color.gif" style="vertical-align: middle;"> <?php echo ocpLabels("Create color")?>:</td> 
    </tr> 
</table> 
<table class="ocp_opcije_table">
	<tr>
	  <td class="ocp_opcije_td" style="white-space: nowrap; width:120px;"><span class="ocp_opcije_tekst1">
		<?php echo ocpLabels("Choose color")?>:</span></td>
		<td class="ocp_opcije_td"> 
			<table cellspacing=3 cellpadding=0 border=0>
				<tr>
					<script>
						var str = "";
						for (var k=0; k<colorsArr.length; k++){
							str += "<td style='background-color:"+colorsArr[k]+"'><img src='/ocp/img/blank.gif' onClick='setLocalColor(\""+colorsArr[k]+"\");' style=\"cursor: pointer; border: 1px solid #666; width: 14px; height: 14px;\"></td>";
						}
						document.write (str);
					</script>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="ocp_opcije_td">
			<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td id="sample"><img src='/ocp/img/blank.gif' style="width: 110px; height: 18px; border: 1px solid #666;"></td>
				</tr>
			</table>
		</td>
		<td class="ocp_opcije_td" style="padding-left:5px;">							
			<table border="0" cellspacing="0" cellpadding="1">
				<tr>
					<td><input type="button" name="Create" onclick="setColor()" value="<?php echo ocpLabels("Change")?>" class="ocp_dugme_malo"></td>
					<td><input type="button" name="Unlink" onclick="cleanFontTag(); window.close();" value="<?php echo ocpLabels("Remove color")?>" class="ocp_dugme_malo"></td>
					<td><input type="button" name="Cancel" onclick="window.close();" value="<?php echo ocpLabels("Cancel")?>" class="ocp_dugme_malo"></td>
				<tr>
			</table>
		</td>
	</tr>
</form>
</table>
</body>
</html>
