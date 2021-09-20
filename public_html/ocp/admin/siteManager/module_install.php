<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../include/tipoviobjekata.php");
	require_once("../../include/selectradio.php");
	require_once("../../siteManager/lib/tipoviblokova.php");
	require_once("../../include/xml.php");
	require_once("../../include/xml_tools.php");

	global $xmlDelimiter;
?>
<?php session_checkAdministrator(); ?>
<HTML>
<HEAD>
<TITLE> OCP </TITLE>
<link rel="stylesheet" href="/ocp/css/opsti.css">
<link rel="stylesheet" href="/ocp/css/opcije.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</HEAD>
<BODY  class="ocp_body"><?php
	$path = utils_requestStr(getPVar("path"));

	if (utils_valid($path)){
		$installDoc = xml_load($_SERVER['DOCUMENT_ROOT'] . $path);

		$root = xml_documentElement($installDoc);
		$phpFile = xml_getAttribute($root, "php");
	//objekti -- baza
		$sql = xml_getElementsByTagName($installDoc, "sql-iskaz");
		if (!is_null($sql) && ($sql->length > 0)){
			for ($i=0; $i<$sql->length; $i++){
				$sqlIskaz = $sql->item($i);
				$result = array(); 

				$fkOnSameTable = array();

				$nazivObjekta = xml_getAttribute($sqlIskaz, "naziv");
				$result["ImeTipa"] = $nazivObjekta;
				$result["Labela"] = xml_getAttribute($sqlIskaz, "labela");
				$result["Grupa"] = xml_getAttribute($sqlIskaz, "grupa");
				$result["Podforma"] = xml_getAttribute($sqlIskaz, "podforma");

				$fields = xml_childNodes($sqlIskaz);
				if ($fields != null){
					for ($k=0; $k < count($fields); $k++){
						$field = $fields[$k];
						$result["ImePolja".$k] = xml_getAttribute($field, "imePolja");
						$result["Tip".$k] = xml_getAttribute($field, "tipPolja");
						$podtipName = xml_getAttribute($field, "podtip");
						if (!utils_valid($podtipName)){
							$result["PodtipId".$k] = "0";
						} else {
							if ($podtipName == $nazivObjekta){
								$result["PodtipId".$k] = "0";
								$fkOnSameTable[] = "update Polja set PodtipId=#tipId where ImePolja='" . $result["ImePolja".$k] . "'";
							} else {
								$result["PodtipId".$k] = tipobj_getId($podtipName);
							}
						}
						$Null = xml_getAttribute($field, "null");
						if ($Null == "true"){
							$result["Null".$k] = "Null";
						} else {
							$result["Null".$k] = $Null;
						}
						$result["Default".$k] = xml_getAttribute($field, "default");
						$result["RedPrikaza".$k] = xml_getAttribute($field, "redPrikaza");
					}
				}
				$result["n"] = ((!is_null($fields)) ? count($fields) : 0);
				
				$newTipId = tipobj_newTip($result);

				for ($j=0; $j < count($fkOnSameTable); $j++)
					con_update(str_replace("#tipId", $newTipId, $fkOnSameTable[$j]));	

				echo("<span class='ocp_opcije_tekst1'>".ocpLabels("Table is created").": ".$nazivObjekta."</span><br>");
			}
		}

		$xmlDefs= xml_getElementsByTagName($installDoc, "xml-def");
		if (!is_null($xmlDefs) && ($xmlDefs->length > 0)){
	//objekti -- input.xml
			$lists = array();
			for ($j=0; $j<$xmlDefs->length; $j++){
				$xmlDef = $xmlDefs->item($j);
				$temp = "";
				$nazivObjekta = xml_getAttribute($xmlDef, "naziv");

				$result = array(); 
				$result["typeName"] = $nazivObjekta;
				$result["typeId"] = tipobj_getId($nazivObjekta);
				if (!is_null(xml_getAttribute($xmlDef, "IdenId")) && (xml_getAttribute($xmlDef, "IdenId") != "0")) $result["IdenId"] = "1";
				$fields = xml_childNodes($xmlDef);
				$cnt = 0;
				for ($k=0; $k < count($fields); $k++){
					$field = $fields[$k];
					if (xml_nodeName($field) == "subform" || xml_nodeName($field) == "include") break;
					$cnt++;
					$attField = xml_attributes($field);
					$result["label".$k] = xml_getAttribute($field, "labela");
					$result["fieldtype".$k] = xml_getAttribute($field, "inputType");
					$result["fieldName".$k] = xml_getAttribute($field, "name");
					if (($result["fieldtype".$k] == "select") || 
						($result["fieldtype".$k] == "radio")){
						$lists[$result["fieldName".$k]] = xml_getAttribute($field, "listName");	
					}	
	//iden	
					$iden = xml_getAttribute($field, "iden");
					if (!utils_valid($iden))
						$result["iden".$k] = "0";
					else 
					$result["iden".$k] = xml_getAttribute($field, "iden");
					$result["editGroup".$k] = install_addAttribute($field, "editGroup");
					$result["root".$k] = install_addAttribute($field, "root");
					$result["width".$k] = install_addAttribute($field, "width");
					$result["height".$k] = install_addAttribute($field, "height");
					$result["max".$k] = install_addAttribute($field, "max");
					$result["import".$k] = install_addAttribute($field, "import");
					$result["where".$k] = install_addAttribute($field, "where");
	//validate				
					$validate = xml_getAttribute($field, "validate");
					$valCnt = 0;
					if (utils_valid($validate)){
						while (is_integer(strpos($validate, ","))){
							$valCnt++;
							$result["_validate".$k.$valCnt] = substr($validate, 0, strpos($validate, ","));
							$validate = substr($validate, strpos($validate, ",") + 1);
						}
						$valCnt++;
						$result["_validate".$k.$valCnt] = $validate;
						$result["validate".$k] = $valCnt;
					}
				}
				$result["cnt"] = $cnt;
				//subforms 
				$subformCount = 0;
				for ($k=0; $k < count($fields); $k++){
					$field = $fields[$k];
					if (xml_nodeName($field) != "subform") continue;
					$subformCount++;
					$result["subform_".$subformCount] = xml_getAttribute($field, "name") . $xmlDelimiter . xml_getAttribute($field, "field");
					$result["subform_editable_".$subformCount] = xml_getAttribute($field, "editable");
				}
				$result["subform"] = $subformCount;
				//includes
				$includeCount = 0;
				for ($k=0; $k < count($fields); $k++){
					$field = $fields[$k];
					if (xml_nodeName($field) != "include") continue;
					$includeCount++;
					$result["includeLabel".$includeCount] = xml_getAttribute($field, "label");
					$result["includeUrl".$includeCount] = xml_getAttribute($field, "url");
				}
				$result["include"] = $includeCount;
				//actions
				$actionCount = 0;
				for ($k=0; $k < count($fields); $k++){
					$field = $fields[$k];
					if (xml_nodeName($field) != "action") continue;
					$actionCount++;
					$result["actionLabel".$actionCount] = xml_getAttribute($field, "label");
					$result["actionImage".$actionCount] = xml_getAttribute($field, "image");
					$result["actionUrl".$actionCount] = xml_getAttribute($field, "url");
					$result["actionPlace".$actionCount] = xml_getAttribute($field, "place");
				}
				$result["action"] = $actionCount;
//utils_dump($result, 1);
				xml_createNode($result);
				echo("<span class='ocp_opcije_tekst1'>".ocpLabels("Xml node is created").": ".$nazivObjekta."</span><br>");
				
				foreach ($lists as $key=>$value){
					if (utils_valid($value))
						con_update("update Polja set ImeListe = '".$value."' where ImePolja='".$key."' and TipId=".$result["typeId"]);
				}
			}
		}

	//tipBloka
		$tipBloka = xml_getFirstElementByTagName($installDoc, "tipBloka");
		$xmlParams = xml_childNodes($tipBloka);
		if (utils_valid(xml_getAttribute($tipBloka, "naziv"))){
			$typeBlockXml = "<blok tip=\"".xml_getAttribute($tipBloka, "naziv")."\"";
			if (!is_null(xml_getAttribute($tipBloka, "type")))
				$typeBlockXml .= " type=\"".xml_getAttribute($tipBloka, "type")."\"";
				$typeBlockXml .= " ><url>".$phpFile."</url>";

			if (count($xmlParams) > 0){
				for ($i=0; $i<count($xmlParams); $i++){
					$param = $xmlParams[$i];
					$typeBlockXml .= "<param name=\"".xml_getAttribute($param, "name")."\"";
					$typeBlockXml .= " label=\"".xml_getAttribute($param, "label")."\"";
					$typeBlockXml .= " inputType=\"".xml_getAttribute($param, "inputType")."\"";
					if (!is_null(xml_getAttribute($param, "root"))){
						$typeBlockXml .= " root=\"".xml_getAttribute($param, "root")."\"";
					}
					if (!is_null(xml_getAttribute($param, "listName"))){
						$typeBlockXml .= " listName=\"".xml_getAttribute($param, "listName")."\"";
					}
					if (!is_null(xml_getAttribute($param, "validate"))){
						$typeBlockXml .= " validate=\"".xml_getAttribute($param, "validate")."\"";
					}
					if (!is_null(xml_getAttribute($param, "podtip"))){
						$typeBlockXml .= " podtip=\"".xml_getAttribute($param, "podtip")."\"";
					}
					$typeBlockXml .= "></param>";
				}
			}
			$typeBlockXml .= "</blok>";
			$result = array( "TipB_Id" => 0, "TipB_Naziv" => xml_getAttribute($tipBloka, "naziv"), "TipB_Xml" => $typeBlockXml, "TipB_XslUrl" => "", "TipB_SlikaUrl" => xml_getAttribute($tipBloka, "imageUrl"), "TipB_Dinamic" => "1");
			tipblok_new($result);
			echo("<span class='ocp_opcije_tekst1'>".ocpLabels("Block type is created").": ".xml_getAttribute($tipBloka, "naziv")."</span><br>");
		}
	} 

	drawEditForm();


	function drawEditForm(){
?><div id="ocp_main_table">
<form name="formObject" id="formObject" method="post" onSubmit="return validate();" action="module_install.php?<?php echo utils_randomQS();?>">
	<table class="ocp_naslov_table">
		<tr>
			<td class="ocp_naslov_td"><?php echo ocpLabels("Module installation");?></td>
		</tr>
	</table>
	<table class="ocp_opcije_table" border="0" cellpadding="0" cellspacing="0">
		<tr style="position:relative; top:0px">
			<td class="ocp_opcije_td ocp_opcije_tekst1" style="width: 22%;"><?php echo ocpLabels("Module path");?></td>
			<td class="ocp_opcije_td ocp_opcije_tekst1">
			<table class="ocp_uni_table">
				<tr>
					<td class="ocp_dugmici_td_levi"><input type="text" class="ocp_forma" name="path" style="width: 100%;"/></td>
					<td class="ocp_dugmici_td_desni_3"><a href="javascript:x = window.open('/ocp/controls/fileControl/frameset.php?<?php echo utils_randomQS();?>&root=/&field=formObject.path','imgKontrola','top=100, left=50, width=760, height=560, scrollbars=yes, resizable=yes, status=yes'); x.focus();"><img src="/ocp/img/opsti/kontrole/kontrola_browse.gif" class="ocp_kontrola" title="<?php echo ocpLabels("Browse server");?>"/></a>
					<a href="javascript: void(0);" onClick="urlCont=formObject.path;window.open(urlCont.value, '', 'width=500, height=400, resizable, scrollbars');"><img src="/ocp/img/opsti/kontrole/kontrola_preview.gif" class="ocp_kontrola" title="<?php echo ocpLabels("Selected link preview");?>"/></a></td>
				</tr>
			</table></td>
		</tr>
	</table>
	<table width="100%">
		<tr>
			<td height="40" align="center" class="ocp_text"><input type="submit" name="submitSave" class="ocp_dugme" value="<?php echo ocpLabels("Save");?>">&nbsp;<input type="button" name="submitCancel" class="ocp_dugme" onClick="document.formObject.path.value=''" value="<?php echo ocpLabels("Cancel");?>"></td>
		</tr>
	</table>
</form>
</div>
<script src="/ocp/validate/validate_double_quotes.js"></script>
<script src="/ocp/validate/user/is_necessary.js"></script>
<script>
	function validate(){
		var value = is_necessary("formObject.path", null, "<?php echo ocpLabels("path");?>");
		if (value) validate_double_quotes(document.formObject);
		return value;
	}
</script><?php	
	}

	function install_addAttribute($node, $imeAtt){
		$vredAtt = xml_getAttribute($node, $imeAtt);
		if (!utils_valid($vredAtt)) $vredAtt = "";
		return $vredAtt;
	}
?>
</SPAN>
</BODY>
</HTML>