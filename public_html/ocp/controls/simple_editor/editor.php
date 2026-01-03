<?php 
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../include/design/menu.php");
	require_once("../../include/design/submenu.php");
	require_once("../../config/table.php");

	$field = utils_requestStr("formObject." . getGVar("field"));
	$label = utils_requestStr(getGVar("label"));
	$label = ocpLabels($label);
	$fieldNameArr = split("[.]", $field);
	$fieldName = $fieldNameArr[count($fieldNameArr)-1];
	$width = intval(utils_requestInt(getGVar("width")))-1;
	$height = intval(utils_requestInt(getGVar("height")))-30;
?><html>
<head>
<title>OCP Simple HTML Editor</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="/css/editor.css" rel="stylesheet" type="text/css">
<link href="/ocp/css/dugmici.css" rel="stylesheet" type="text/css">
<link href="/ocp/css/opcije.css" rel="stylesheet" type="text/css">
<link href="/ocp/css/opsti.css" rel="stylesheet" type="text/css">
<script src="/ocp/config/colors.js"></script>
<script src="/ocp/controls/advanced_editor/clean_word.js"></script>
<script src="/ocp/controls/advanced_editor/returnnl.js"></script>
<script src="/ocp/controls/advanced_editor/quickbuild.js"></script>
<script language="JavaScript" type="text/JavaScript">
	var labClickEditor = "<?php echo ocpLabels("Click to select editor")?>";
	var labClickCell = "<?php echo ocpLabels("Click to select cell")?>";
	var textareaNames = new Array("Tekst");
	var tableEditArray = new Array("1");
	var done = false;
	
	function init(){
		if (!done){
			fillEditor(); setTimeout("start();", 200); done = true;
		}
	}

	function fillEditor(){
		var x = parent.document.<?php echo $field?>.value;
		if (x != "" && x != "undefined"){
			x = x.replace(/&nbsp;/ig, " ");
			x = x.replace(/[<][b][r][>]\r\n/ig, "<br>");
			x = x.replace(/\r\n/ig, "");
			x = x.replace(/\n/ig, "");
			document.formObject.Tekst.value = x;
		}
		//parent.document.frames[simpleEditorObject_<?php echo $fieldName?>] = self;
	}
	
	function fillParent(){
		if (fID != "undefined"){
			var content= editorContents(fID);
			if (content != "undefined") 
				parent.document.<?php echo $field?>.value = content;
		}
	}
	function reloadSimpleEditor() {
		document.location.reload(true)
	}
</script>

</head>
<body class="ocp_body" onLoad="init();">
<div> 
<form name="formObject" id="formObject" method="post" onSubmit="return fillParent();">
	<table class="ocp_uni_table">
		<tr>
			<td class="ocp_napred_edit_skraceni" style="padding-left:4px;"><span class="ocp_opcije_tekst1"><?php echo $label?><span></td>
			<td class="ocp_napred_edit_skraceni" style="text-align:right">
			<img src="/ocp/img/kontrole/napredni_edit/dugmici/link.gif" width="21" height="21" border="0" class="ocp_napred_edit_dugme" style="cursor:pointer;" onclick="openLinkEditor('link');" title="<?php echo ocpLabels("Link")?>">
			<img src="/ocp/img/kontrole/napredni_edit/dugmici/link_mail.gif" width="21" height="21" border="0" class="ocp_napred_edit_dugme" style="cursor:pointer;" onclick="openLinkEditor('email');" title="<?php echo ocpLabels("E-mail link")?>">
			<img src="/ocp/img/kontrole/napredni_edit/dugmici/crtka.gif" width="2" height="16" class="ocp_napred_edit_crtka">
			<img src="/ocp/img/kontrole/napredni_edit/dugmici/bold.gif" width="21" height="21" border="0" class="ocp_napred_edit_dugme" style="cursor:pointer;" onclick="doFormatF('Bold');" title="<?php echo ocpLabels("Bold")?>">
			<img src="/ocp/img/kontrole/napredni_edit/dugmici/italic.gif" width="21" height="21" border="0" class="ocp_napred_edit_dugme" style="cursor:pointer;" onclick="doFormatF('Italic');" title="<?php echo ocpLabels("Italic")?>">
			<img src="/ocp/img/kontrole/napredni_edit/dugmici/crtka.gif" width="2" height="16" class="ocp_napred_edit_crtka">
			<img src="/ocp/img/kontrole/napredni_edit/dugmici/left.gif" width="21" height="21" class="ocp_napred_edit_dugme" style="cursor:pointer;" onclick="doFormatF('JustifyLeft');" title="<?php echo ocpLabels("Left")?>">
			<img src="/ocp/img/kontrole/napredni_edit/dugmici/center.gif" width="21" height="21" class="ocp_napred_edit_dugme" style="cursor:pointer;" onclick="doFormatF('JustifyCenter');" title="<?php echo ocpLabels("Center")?>">
			<img src="/ocp/img/kontrole/napredni_edit/dugmici/right.gif" width="21" height="21" class="ocp_napred_edit_dugme" style="cursor:pointer;" onclick="doFormatF('JustifyRight');" title="<?php echo ocpLabels("Right")?>">
			<img src="/ocp/img/kontrole/napredni_edit/dugmici/justify.gif" width="21" height="21" class="ocp_napred_edit_dugme" style="cursor:pointer;" onclick="doFormatF('JustifyFull');" title="Justify">
			<img src="/ocp/img/kontrole/napredni_edit/dugmici/crtka.gif" width="2" height="16" class="ocp_napred_edit_crtka">
			<img src="/ocp/img/kontrole/napredni_edit/dugmici/numlist.gif" width="21" height="21" class="ocp_napred_edit_dugme" style="cursor:pointer;" onclick="doFormatF('InsertOrderedList');" title="<?php echo ocpLabels("Ordered list")?>">
			<img src="/ocp/img/kontrole/napredni_edit/dugmici/bullist.gif" width="21" height="21" class="ocp_napred_edit_dugme" style="cursor:pointer;" onclick="doFormatF('InsertUnorderedList');" title="<?php echo ocpLabels("Unordered list")?>">
			<img src="/ocp/img/kontrole/napredni_edit/dugmici/crtka.gif" width="2" height="16" class="ocp_napred_edit_crtka">
			<img src="/ocp/img/kontrole/napredni_edit/dugmici/cleanword.gif" width="21" height="21" class="ocp_napred_edit_dugme" style="cursor:pointer;" onclick="cleanupWordHTML('Tekst');" title="<?php echo ocpLabels("Clean Word HTML")?>">
			</td>
		</tr>
		<tr>
			<td style="padding: 0px; margin:0px;" colspan="2"><textarea name="Tekst" class="normal" style="width: <?php echo $width?>%; height: <?php echo $height?>px; padding: 0px; margin:0px;"></textarea></td>
		</tr>
	</table>
</form>
</div>
</body>
</html>