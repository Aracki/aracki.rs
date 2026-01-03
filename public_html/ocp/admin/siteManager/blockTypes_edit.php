<?php
require_once("../../include/connect.php");
require_once("../../include/session.php");
require_once("../../siteManager/lib/tipoviblokova.php");
require_once("../../include/language.php");
require_once("../../include/xml_tools.php");
?>

<?php session_checkAdministrator(); ?>

<html>
<head>
<title>OCP</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/ocp/css/opsti.css">
<link rel="stylesheet" href="/ocp/css/opcije.css">
</head>
<body class="ocp_body"><?php
	$action = utils_requestStr(getPVar("Action"));

	if (utils_valid($action)) {
		$insertedLabels = false;
	
		if ($action == "Obrisi"){
			tipblok_delete(utils_requestInt(getPVar("TipB_Id")));
		} else if ($action == "UNedeljeni"){
			tipblok_breakShare(utils_requestInt(getPVar("Blok_Id")));
		} else if ($action == "ObrisiDeljeni"){
			tipblok_deleteShare(utils_requestInt(getPVar("Blok_Id")));
		} else {
			$blockType = array();
			$blockType["TipB_Id"] = utils_requestInt(getPVar("TipB_Id"));
			$blockType["TipB_Naziv"] = utils_requestStr(getPVar("TipB_Naziv"));
			$blockType["TipB_XslUrl"] = utils_requestStr(getPVar("TipB_XslUrl"));
			$blockType["TipB_SlikaUrl"] = utils_requestStr(getPVar("TipB_SlikaUrl"));
			$blockType["TipB_Dinamic"] = utils_requestStr(getPVar("TipB_Dinamic"));

			$blockXml = utils_requestStr(getPVar("TipB_Xml"), true);

			$insertedLabels = lang_newLabela($blockType["TipB_Naziv"]);
	/*Izmene importovanih nodova*/
			$xmlDoc = xml_loadXML($blockXml);

			$impNodes = xml_getElementsByTagName($xmlDoc, "import");
			for ($i=0; $i<$impNodes->length; $i++){
				$impNode = $impNodes->item($i);
				if (!xml_hasChildNodes($impNode)){
					$doc = xml_load($_SERVER['DOCUMENT_ROOT'] . "/ocp/siteManager/style/".xml_getAttribute($impNode, "type").".xml");
					$childs = xml_childNodes(xml_documentElement($doc));
					for ($j=0; $j<count($childs); $j++){
						$child = $childs[$j];
						$cloneNode = xml_cloneNode($xmlDoc, $child);
						if (!is_null(xml_getAttribute($child, "label")))
							$insertedLabels = lang_newLabela(xml_getAttribute($child, "label")) || $insertedLabels;
						xml_appendChild($impNode, $cloneNode);
					}
				}  else {
					$childs = xml_childNodes($impNode);
					for ($j=0; $j<count($childs); $j++){
						$child = $childs[$j];
						if (!is_null(xml_getAttribute($child, "label")))
							$insertedLabels = lang_newLabela(xml_getAttribute($child, "label")) || $insertedLabels;
					}
				}
			}
			
			$childs = xml_childNodes(xml_documentElement($xmlDoc));
			for ($j=0; $j<count($childs); $j++){
				$child = $childs[$j];
				if (!is_null(xml_getAttribute($child, "label"))){
					$insertedLabels = lang_newLabela(xml_getAttribute($child, "label")) || $insertedLabels;
				}
			}

			$blockType["TipB_Xml"] = xml_xml($xmlDoc);
			
			tipblok_new($blockType);
		}

		?><script>parent.subMenuFrame.location.reload();
		<?php	if ($insertedLabels){	?>
		alert("<?php echo ocpLabels("New labels have been added to Multilanguage support. Please translate them.");?>");
		<?php	}?>
		</script><?php
	} else {//pre submita
		$action = utils_requestStr(getGVar("action"));
		$type = utils_requestStr(getGVar("type"));
		$typeId = utils_requestInt(getGVar("typeId"));

		switch ($type){
			case "share" :	drawShareForm($typeId);	break;
			default: drawEditForm($typeId);	break;
		}
	}

	function drawEditForm($typeId){
		// Ubaceno za uzimanje svih sablona iz foldera
		$xmlTypes = array();
		$dir = $_SERVER["DOCUMENT_ROOT"]."/ocp/siteManager/style";
		$dh = opendir($dir);
		
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (false !== ($filename = readdir($dh))) {
					if ($filename != "." && $filename != "..") {
						if (is_integer(strpos($filename, ".xml")) && ($filename != "extraparams.xml"))
						$xmlTypes[] = substr($filename, 0, utils_lastIndexOf($filename, "."));
					}
				}
			   closedir($dh);
			}
		}
		sort($xmlTypes);

		$blockType = ($typeId != "-1") ? tipblok_get($typeId) : array();
?>
<div id="ocp_main_table">
<form name="formObject" id="formObject" method="post" onSubmit="return validate();" action="blockTypes_edit.php?<?php echo utils_randomQS();?>">
	<input type="hidden" name="TipB_Id" value="<?php echo $typeId;?>">
	<input type="hidden" name="Action" value="Sacuvaj">
	<table class="ocp_naslov_table">
		<tr>
			<td class="ocp_naslov_td">
				<?php if ($typeId != "-1") echo (ocpLabels("Edit block type").": ".ocpLabels($blockType["TipB_Naziv"])); else  echo (ocpLabels("New block type"));?>
			</td></tr>
	</table>
	<table class="ocp_opcije_table" id="naziv" name="listTable" style="width:100%; display: block;"><?php
		if ($typeId != "-1"){
			?><tr>
          <td width="100%" align="right" class="ocp_opcije_td" colspan="2"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Delete");?>&nbsp;</span><img src="/ocp/img/opsti/kontrole/kontrola_obrisi_objekat.gif" border="0" width="20" height="21" onClick="deleteBlockType('<?php echo $typeId;?>');" title="<?php echo ocpLabels("Delete object");?>" style="cursor: pointer;"></td>
      </tr><?php
		}	
		?><tr>
          <td width="22%" align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Title");?></span></td><?php 
		$blockTitle = ($typeId != "-1") ? $blockType["TipB_Naziv"] : "";
		?><td valign="top" class="ocp_opcije_td"><input type="text" class="ocp_forma" name="TipB_Naziv" style="width: 100%;" value="<?php echo $blockTitle;?>"/></td>
      </tr>
	  <tr>
		<td width="22%" align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Xml structure");?></span></td><?php 
		$blockXml = ($typeId != "-1") ? $blockType["TipB_Xml"] : "";
		?><td valign="top" class="ocp_opcije_td"><table width="100%">
              <tr>
                <td  style="width: 100%"><textarea cols="30" rows="10" class="ocp_forma" style="width:100%" name="TipB_Xml"><?php echo $blockXml;?></textarea></td>
                <td><?php	
		if (count($xmlTypes) > 0) {
					?><select class="ocp_forma" onChange="importNode(this.value); this.selectedIndex=0;">
						<option value="">--<?php echo ocpLabels("choose xml template");?>--</option>
<?php			for ($i = 0; $i < count($xmlTypes); $i++) { ?>
						<option value="<?php echo $xmlTypes[$i];?>"><?php echo $xmlTypes[$i];?></option><?php
			}			?></select><?php	
		}		?></td>
			  </tr>
			 </table>
		 </td>
	  </tr>
	  <tr>
		<td width="22%" align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Xsl file");?></span></td><?php 
		$blockXsl = ($typeId != "-1") ? $blockType["TipB_XslUrl"] : "";
		?><td valign="top" class="ocp_opcije_td"><table class="ocp_uni_table">
				<tr>
					<td class="ocp_dugmici_td_levi"><input type="text" class="ocp_forma" name="TipB_XslUrl" style="width: 100%;" value="<?php echo $blockXsl;?>"/></td>
					<td class="ocp_dugmici_td_desni_3"><a href="javascript:x = window.open('/ocp/controls/fileControl/frameset.php?<?php echo utils_randomQS();?>&root=/styles&field=formObject.TipB_XslUrl','imgKontrola','top=100, left=50, width=760, height=560, scrollbars=yes, resizable=yes, status=yes'); x.focus();"><img src="/ocp/img/opsti/kontrole/kontrola_browse.gif" class="ocp_kontrola" title="<?php echo ocpLabels("Browse server");?>"/></a>
					<a href="javascript: void(0);" onClick="var urlCont = document.formObject.TipB_XslUrl; window.open(urlCont.value, '', 'width=500, height=400, resizable, scrollbars');"><img src="/ocp/img/opsti/kontrole/kontrola_preview.gif" class="ocp_kontrola" title="<?php echo ocpLabels("Selected file preview");?>"/></a></td>
				</tr>
			</table></td>
	  </tr>
	  <tr>
		<td width="22%" align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Image");?></span></td><?php 
		$blockImage = ($typeId != "-1") ? $blockType["TipB_SlikaUrl"] : "";
		?><td valign="top" class="ocp_opcije_td"><table class="ocp_uni_table">
				<tr>
					<td class="ocp_dugmici_td_levi"><input type="text" class="ocp_forma" name="TipB_SlikaUrl" style="width: 100%;" value="<?php echo $blockImage;?>"/></td>
					<td class="ocp_dugmici_td_desni_3"><a href="javascript:x = window.open('/ocp/controls/fileControl/frameset.php?<?php echo utils_randomQS();?>&root=/ocp/img/opsti/blokovi/ikone/srednji&field=formObject.TipB_SlikaUrl','imgKontrola','top=100, left=50, width=760, height=560, scrollbars=yes, resizable=yes, status=yes'); x.focus();"><img src="/ocp/img/opsti/kontrole/kontrola_browse.gif" class="ocp_kontrola" title="<?php echo ocpLabels("Browse server");?>"/></a>
					<a href="javascript: void(0);" onClick="var urlCont = document.formObject.TipB_SlikaUrl; window.open(urlCont.value, '', 'width=500, height=400, resizable, scrollbars');"><img src="/ocp/img/opsti/kontrole/kontrola_preview.gif" class="ocp_kontrola" title="<?php echo ocpLabels("Selected file preview");?>"/></a></td>
				</tr>
			</table></td>
	  </tr>
	 <tr><?php	if (!isset($blockType["TipB_Dinamic"])) $blockType["TipB_Dinamic"] = "0";	?>
          <td class="ocp_opcije_td" align="right"><span class="ocp_opcije_tekst1"><?php echo ocpLabels("Block type");?></span></td>
          <td valign="top" class="ocp_opcije_td"><span class="ocp_opcije_tekst2"><?php echo ocpLabels("Static");?></span>&nbsp;<input type="radio" name="TipB_Dinamic" value="0" <?php if ($blockType["TipB_Dinamic"] == "0" || ($typeId == "-1")) {?>checked<?php } ?>/>&nbsp;<span class="ocp_opcije_tekst2"><?php echo ocpLabels("Dinamic");?></span>&nbsp;<input type="radio" name="TipB_Dinamic" value="1" <?php if ($blockType["TipB_Dinamic"]=="1") {?>checked<?php } ?>/></td>
      </tr>
	  </table>
	<table width="100%">
		<tr>
			<td height="40" align="center" class="ocp_text"><input type="submit" name="submitSave" class="ocp_dugme" value="<?php echo ocpLabels("Save");?>">&nbsp;<input type="button" name="submitCancel" class="ocp_dugme" onClick="window.open('/ocp/html/blank.html', '_self')" value="<?php echo ocpLabels("Cancel");?>"></td>
		</tr>
	</table>
</form>
</div>
<script src="/ocp/validate/validate_double_quotes.js"></script>
<script>
	function deleteBlockType(){
		if (confirm("<?php echo ocpLabels('Are you sure you want to delete object');?>?")){
			document.formObject.Action.value = "Obrisi";
			document.formObject.submit();
		}
	}

	function importNode(tipImporta){
		if (tipImporta != '')
			document.formObject.TipB_Xml.value += '<import type="'+tipImporta+'" name="" label=""/>';
	}

	function validate(){
		if (document.formObject.TipB_Naziv.value == ""){
			alert("<?php echo ocpLabels("Title");?>"+" "+"<?php echo ocpLabels("must have value");?>.");
			return false;
		}

		if (document.formObject.TipB_Xml.value == ""){
			alert("<?php echo ocpLabels("Xml structure");?>"+" "+"<?php echo ocpLabels("must have value");?>.");
			return false;
		}

		if (document.formObject.TipB_SlikaUrl.value == ""){
			alert("<?php echo ocpLabels("Image");?>"+" "+"<?php echo ocpLabels("must have value");?>.");
			return false;
		}
		validate_double_quotes_field(document.formObject.TipB_Naziv);
		validate_double_quotes_field(document.formObject.TipB_XslUrl);
		validate_double_quotes_field(document.formObject.TipB_SlikaUrl);
		return true;
	}
</script><?php
	}	
	
	function drawShareForm($blockId){
		$block = tipBloka_getShare($blockId);
?><div id="ocp_main_table">
<form name="formObject" id="formObject" method="post" action="blockTypes_edit.php?<?php echo utils_randomQS();?>" onSubmit="return validate();">
	<input type="hidden" name="Blok_Id" value="<?php echo $blockId;?>">
	<input type="hidden" name="Action" value="UNedeljeni">
	<table class="ocp_naslov_table">
		<tr>
			<td class="ocp_naslov_td"><?php echo ocpLabels("Share block");?>:&nbsp;<?php echo $block["Blok_MetaNaziv"];?></td>
		</tr>
	</table>
	<table class="ocp_opcije_table" id="naziv" name="listTable" style="width:100%; display: block;">
	 <tr>
          <td class="ocp_opcije_td" align="center" valign="top"  style="width:100%;text-align:center;"><span class="ocp_opcije_tekst2"><?php echo ocpLabels("Turn to static");?></span>&nbsp;<input type="radio" name="Shared" value="0" checked/>&nbsp;<span class="ocp_opcije_tekst2"><?php echo ocpLabels("Delete shared block");?></span>&nbsp;<input type="radio" name="Shared" value="1"></td>
      </tr>
	  </table>
	<table width="100%">
		<tr>
			<td height="40" align="center" class="ocp_text"><input type="submit" name="submitSave" class="ocp_dugme" value="<?php echo ocpLabels("Submit");?>">&nbsp;<input type="button" name="submitCancel" class="ocp_dugme" onClick="window.open('/ocp/html/blank.html', '_self')" value="<?php echo ocpLabels("Cancel");?>"></td>
		</tr>
	</table>
</form>
</div><script>
	function validate(){
		if (document.formObject.Shared[0].checked) document.formObject.Action.value='UNedeljeni';
		if (document.formObject.Shared[1].checked) document.formObject.Action.value='ObrisiDeljeni';

		return true;
	}
</script><?php		
	}
?></body>
</html>
