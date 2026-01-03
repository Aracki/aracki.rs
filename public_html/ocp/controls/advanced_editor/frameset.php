<?php 
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../include/design/menu.php");
	require_once("../../include/design/submenu.php");
	require_once("../../config/table.php");

	$field = utils_requestStr(getGVar("field"));
	$label = utils_requestStr(getGVar("label"));
	$label = ocpLabels($label);
	$fieldNameArr = split("[.]", $field);
	$fieldName = $fieldNameArr[count($fieldNameArr) - 1];
	$simple = utils_requestStr(getGVar("simple"));
	$menuArray = array();
	$menuArray['normal'] = array("Normal", 'swapMode("Text");');
	$menuArray['html'] = array("HTML", 'swapMode("HTML");');


?><html>
<head>
<title>OCP HTML Editor</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="/css/editor.css" rel="stylesheet" type="text/css">
<link href="/ocp/css/dugmici.css" rel="stylesheet" type="text/css">
<link href="/ocp/css/opcije.css" rel="stylesheet" type="text/css">
<link href="/ocp/css/opsti.css" rel="stylesheet" type="text/css">
<link href="/ocp/css/gornji.css" rel="stylesheet" type="text/css">
<script src="/ocp/config/colors.js"></script>
<script src="/ocp/controls/advanced_editor/table_edit.js"></script>
<script src="/ocp/controls/advanced_editor/returnnl.js"></script>
<script src="/ocp/controls/advanced_editor/advanced_options.js"></script>
<script src="/ocp/controls/advanced_editor/clean_word.js"></script>
<script src="/ocp/controls/advanced_editor/quickbuild.js"></script>
<script src="/ocp/jscript/menu.js"></script>

<script language="JavaScript" type="text/JavaScript">
	function loadSteps(){
		document.getElementById('normal_text').style.fontWeight = "bold";
		document.getElementById('normal_text').style.color = "#363636";
		//defaultPage();
	}

	function switchTabs(current, previous){
		switchMenuTabs(current, previous, null, "HTML-editor");
	}

	function switchSubmenuTab(idTaba){
		switchSubmenuTabHE(idTaba);
	}

<?php	menu_script($menuArray, false);	?>
	var labClickEditor = "<?php echo ocpLabels("Click to select editor")?>";
	var labClickCell = "<?php echo ocpLabels("Click to select cell")?>";
	var simple = "<?php echo $simple?>";
	var textareaNames = new Array("Tekst");
	var tableEditArray = new Array("1");
</script>

</head>
<body class="ocp_gornji_2_body" onLoad="loadSteps(); window.focus(); fillEditor(); start();">
<div class="ocp_gornji_body"> 
<table class="ocp_gornji_table" style="height:30px;"> 
	<tr> 
		<td class="ocp_gornji_td_levi">
			<table cellpadding="0" cellspacing="0"> 
				<tr> 
					<td align="left" class="ocp_gornji_title">
						<?php echo ocpLabels("Rich text format")?>
					</td> 
				</tr> 
			</table>
		</td> 
		<td class="ocp_gornji_td_desni" onclick="top.close();" style="cursor:pointer;">
			<?php echo ocpLabels("close window")?>
		</td>
	</tr> 
</table>
</div>
<div class="ocp_gornji_2_body"> 
<form name="formObject" id="formObject" method="post" onSubmit="return fillParent();" style="display: inline">
<table class="ocp_gornji_2_table" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td class="ocp_gornji_2_naslov"><img src="/ocp/img/kontrole/napredni_edit/ikona_pored_naslova.gif" width="20" height="16" align="absbottom" style="margin-right: 3px;"><?php echo $label?></td>
		<td class="ocp_gornji_2_desni"><?php menu_html($menuArray);	?></td>
	</tr>
	<tr>
		<td colspan="2" style="padding-top: 2px;" class="ocp_gornji_2_td_dugmici_editor">	
		<table class="ocp_opcije_table_univ">
		  <tr>
		    <td height="28" class="ocp_gornji_2_td_dugmici">
				<img src="/ocp/img/kontrole/napredni_edit/dugmici/link.gif" class="ocp_napred_edit_dugme" title="<?php echo ocpLabels("Link")?>" width="21" height="21" style="cursor:pointer;" onclick="openLinkEditor('link')">
	        </td>
			<td>
				<img src="/ocp/img/kontrole/napredni_edit/dugmici/link_mail.gif" class="ocp_napred_edit_dugme" title="<?php echo ocpLabels("E-mail link")?>" width="21" height="21" style="cursor:pointer;" onclick="openLinkEditor('email')">
			</td>
		    <td>
				<img src="/ocp/img/kontrole/napredni_edit/dugmici/crtka.gif" width="2" height="16" class="ocp_napred_edit_crtka">
			</td>
		    <td>
				<img src="/ocp/img/kontrole/napredni_edit/dugmici/bold.gif" class="ocp_napred_edit_dugme" title="<?php echo ocpLabels("Bold")?>" width="21" height="21" style="cursor:pointer;" onclick="doFormatF('Bold');">
	        </td>
		    <td>
				<img src="/ocp/img/kontrole/napredni_edit/dugmici/italic.gif" class="ocp_napred_edit_dugme" title="<?php echo ocpLabels("Italic")?>" width="21" height="21" style="cursor:pointer;" onclick="doFormatF('Italic');">
	        </td>
		    <td>
				<img src="/ocp/img/kontrole/napredni_edit/dugmici/under.gif" class="ocp_napred_edit_dugme" title="<?php echo ocpLabels("Underline")?>" width="21" height="21" style="cursor:pointer;" onclick="doFormatF('Underline');">
	        </td>
		    <td>
				<img src="/ocp/img/kontrole/napredni_edit/dugmici/crtka.gif" width="2" height="16" class="ocp_napred_edit_crtka">
			</td>
		    <td>
		        <img src="/ocp/img/kontrole/napredni_edit/dugmici/left.gif" class="ocp_napred_edit_dugme" title="<?php echo ocpLabels("Left")?>" width="21" height="21" style="cursor:pointer;" onclick="doFormatF('JustifyLeft');">
	        </td>
		    <td>
				<img src="/ocp/img/kontrole/napredni_edit/dugmici/center.gif" class="ocp_napred_edit_dugme" title="<?php echo ocpLabels("Center")?>" width="21" height="21" style="cursor:pointer;" onclick="doFormatF('JustifyCenter');">
			</td>
		    <td>
		        <img src="/ocp/img/kontrole/napredni_edit/dugmici/right.gif" class="ocp_napred_edit_dugme" title="<?php echo ocpLabels("Right")?>" width="21" height="21" style="cursor:pointer;" onclick="doFormatF('JustifyRight');">
	        </td>
			<td>
		        <img src="/ocp/img/kontrole/napredni_edit/dugmici/justify.gif" class="ocp_napred_edit_dugme" title="Justify" width="21" height="21" style="cursor:pointer;" onclick="doFormatF('JustifyFull');">
	        </td>
		    <td>
				<img src="/ocp/img/kontrole/napredni_edit/dugmici/crtka.gif" width="2" height="16" class="ocp_napred_edit_crtka"></td>
		    <td>
		         <img src="/ocp/img/kontrole/napredni_edit/dugmici/outdent.gif" class="ocp_napred_edit_dugme" title="<?php echo ocpLabels("Outdent")?>" width="21" height="21" style="cursor:pointer;" onclick="doFormatF('Outdent');">
			</td>
		    <td>
				<img src="/ocp/img/kontrole/napredni_edit/dugmici/indent.gif" class="ocp_napred_edit_dugme" title="<?php echo ocpLabels("Indent")?>" width="21" height="21" style="cursor:pointer;" onclick="doFormatF('Indent');">
			</td>
		    <td>
				<td class="ocp_gornji_2_td_dugmici"><img src="/ocp/img/kontrole/napredni_edit/dugmici/numlist.gif" class="ocp_napred_edit_dugme" title="<?php echo ocpLabels("Ordered list")?>" width="21" height="21" style="cursor:pointer;" onclick="doFormatF('InsertOrderedList');">
	        </td>
		    <td>
				<img src="/ocp/img/kontrole/napredni_edit/dugmici/bullist.gif" class="ocp_napred_edit_dugme" title="<?php echo ocpLabels("Unordered list")?>" width="21" height="21" style="cursor:pointer;" onclick="doFormatF('InsertUnorderedList');">
	        </td>
		    <td>
				<img src="/ocp/img/kontrole/napredni_edit/dugmici/crtka.gif" width="2" height="16" class="ocp_napred_edit_crtka">
			</td>
		    <td>
				<img src="/ocp/img/kontrole/napredni_edit/dugmici/color.gif" class="ocp_napred_edit_dugme" title="<?php echo ocpLabels("Color")?>" width="21" height="21" style="cursor:pointer;" onclick="openColorEditor()">
	        </td>
		    <td>
				<img src="/ocp/img/kontrole/napredni_edit/dugmici/crtka.gif" width="2" height="16" class="ocp_napred_edit_crtka">
			</td>
		    <td>
				<img src="/ocp/img/kontrole/napredni_edit/dugmici/instable.gif" class="ocp_napred_edit_dugme" title="<?php echo ocpLabels("Insert table")?>" width="21" height="21" style="cursor:pointer;" onclick="openTableCreator()">
	        </td>
		    <td>
				<img src="/ocp/img/kontrole/napredni_edit/dugmici/tabprop.gif" class="ocp_napred_edit_dugme" title="<?php echo ocpLabels("Table properties")?>" width="21" height="21" style="cursor:pointer;" onclick="openTableProperties()">
	        </td>
		    <td>
		       <img src="/ocp/img/kontrole/napredni_edit/dugmici/cellprop.gif" class="ocp_napred_edit_dugme" title="<?php echo ocpLabels("Cell properties")?>" width="21" height="21" style="cursor:pointer;" onclick="openCellProperties()">
	        </td>
		    <td>
				<img src="/ocp/img/kontrole/napredni_edit/dugmici/insrow.gif" class="ocp_napred_edit_dugme" title="<?php echo ocpLabels("Insert row")?>" width="21" height="21" style="cursor:pointer;" onclick="insertNewRow()">
	        </td>
		    <td>
				<img src="/ocp/img/kontrole/napredni_edit/dugmici/delrow.gif" class="ocp_napred_edit_dugme" title="<?php echo ocpLabels("Delete row")?>" width="21" height="21" style="cursor:pointer;" onclick="deleteThisRow()">
			</td>
		    <td>
				<img src="/ocp/img/kontrole/napredni_edit/dugmici/inscol.gif" class="ocp_napred_edit_dugme" title="<?php echo ocpLabels("Insert column")?>" width="21" height="21" style="cursor:pointer;" onclick="insertCol()">
	        </td>
		    <td>
				<img src="/ocp/img/kontrole/napredni_edit/dugmici/delcol.gif" class="ocp_napred_edit_dugme" title="<?php echo ocpLabels("Delete column")?>" width="21" height="21" style="cursor:pointer;" onclick="deleteCol()">
	        </td>
		    <td>
				<img src="/ocp/img/kontrole/napredni_edit/dugmici/crtka.gif" width="2" height="16" class="ocp_napred_edit_crtka">
			</td>
		    <td>
				<img src="/ocp/img/kontrole/napredni_edit/dugmici/cleanword.gif" class="ocp_napred_edit_dugme" title="<?php echo ocpLabels("Clean Word HTML")?>" width="21" height="21" style="cursor:pointer;" onclick="cleanupWordHTML('Tekst');">
			</td>
	      </tr>
	    </table></td>
	</tr>
	<tr>
		<td colspan="2" style="padding:0; margin: 0px;">
			<textarea name="Tekst" style="width:10px; height:300px;padding:0; margin: 0;" class="normal"></textarea>
		</td>
	</tr>
	<tr>
		<td height="40" align="center" colspan="2" class="ocp_info_td" style="border-top:1px solid #000000">
			<input type="submit" name="submit" value="<?php echo ocpLabels("Confirm")?>" class="ocp_dugme">
			<input name="button" type="button" class="ocp_dugme" onclick="parent.close();" value="<?php echo ocpLabels("Cancel")?>">
		</td>
	</tr>
</table>
</form>
</div>
<script>
	function fillEditor(){
		var x = opener.document.<?php echo $field?>.value;
		if (x != ""){
			x = x.replace(/[<][b][r](\/){0,1}[>]\r\n/ig, "<br/>");
		}
		document.formObject.Tekst.value = x;
	}

	function fillParent(){
		var content= editorContents(fID);
		if (content != "undefined") 
			opener.document.<?php echo $field?>.value = content;

		if (simple=='1') {
			opener.window.frames["simpleEditorObject_<?php echo $fieldName?>"].reloadSimpleEditor();
		}
		close();
		return false;
	}
</script>
</body>
</html>