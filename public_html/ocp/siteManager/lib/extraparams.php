<?php
	require_once("../include/language.php");
	require_once("../include/objekti.php");
	require_once("../include/polja.php");
	require_once("../include/selectradio.php");
	require_once("../include/xml_tools.php");

/*Postoji extraParams xml za sekciju, verziju i stranicu
=======================================================*/
	function extra_existXml($Type){
		$doc = xml_load($_SERVER['DOCUMENT_ROOT'] . "/ocp/config/extraparams.xml");
		$exist = false;
		$nodes = xml_getElementsByTagName($doc, "type");
//utils_dump("existXml - start");
		for ($i=0; $i<$nodes->length; $i++){	
			$node = $nodes->item($i);
			if (!is_null(xml_getAttribute($node, "name")) && (xml_getAttribute($node, "name") == $Type)){
				if (count(xml_childNodes($node)) > 0) $exist = true;
				break;
			}
		}
//utils_dump("existXml - end");
		return $exist;		
	}	

/*Vraca extraParams xml za sekciju, verziju i stranicu
=======================================================*/
	function extra_getXml($Type){
//utils_dump("extra_getXml - start");
		$doc = xml_load($_SERVER['DOCUMENT_ROOT'] . "/ocp/config/extraparams.xml");
		
		$xmlRet = xml_createObject();
//		utils_dump($xmlRet, 1);
		xml_appendChild($xmlRet, xml_createElement($xmlRet, $Type));		
		$nodes = xml_getElementsByTagName($doc, "type");
		for ($i = 0; $i < $nodes->length; $i++){	
			$node = $nodes->item($i);
			if (!is_null(xml_getAttribute($node, "name")) && (xml_getAttribute($node, "name") == $Type)){
				$childs = xml_childNodes($node);
				if (count($childs) > 0){
					$nodeType = xml_documentElement($xmlRet);
//					utils_dump("Clone node");
					$nodeCloned = xml_cloneNode($xmlRet, $node);
//					utils_dump("Clone node end");
					xml_appendChild($nodeType, $nodeCloned);
				}
				break;
			}
		}
//utils_dump("extra_getXml - end");
		return $xmlRet;		
	}


/*Transform extra parameters string
================================*/
	function extra_transformString($str){
		$xmlDoc = xml_loadXML($str);
		extra_transform($xmlDoc);
	}

/*Transform extra parameters xml
================================*/
	function extra_transform($xmlDoc){
//utils_dump("extra_transform - start");

//utils_dump(xml_xml($xmlDoc));
		if ((!is_null(xml_documentElement($xmlDoc))) && (count(xml_childNodes(xml_documentElement($xmlDoc))) > 0)){
			$typeNode = xml_getFirstElementByTagName($xmlDoc, "type");
			$newXmlDoc = extra_getXml(xml_getAttribute($typeNode, "name"));
//utils_dump(xml_xml($newXmlDoc));
			if (!is_null(xml_documentElement($newXmlDoc)) && (count(xml_childNodes(xml_documentElement($newXmlDoc))) > 0)){
				$newTypeNode = xml_getFirstElementByTagName($newXmlDoc, "type");
				$insertedLabel = false;
				$params = xml_childNodes($newTypeNode);
//utils_dump("pre petlje");
				for ($j=0;$j<count($params);$j++){
					$paramsOldNodes = xml_getElementsByTagName($xmlDoc, xml_nodeName($params[$j]));
					if ($paramsOldNodes->length > 0){
						$paramsOldNode = $paramsOldNodes->item(0);
						$params[$j] = xml_setContent($newXmlDoc, $params[$j], xml_getContent($paramsOldNode));
//utils_dump(xml_nodeName($params[$j])." ".xml_getContent($params[$j]));
					}
					$insertedLabel = extra_prepareChild($newXmlDoc, $params[$j]) || $insertedLabel;
				}
//utils_dump("posle petlje");
				extra_appendLabels(xml_getFirstElementByTagName($newXmlDoc, "type"));
//utils_dump(xml_xml($newXmlDoc));
				echo(xml_transform($newXmlDoc, "../siteManager/style/extraparams.xsl", true));
				if ($insertedLabel){
?><script>alert("<?php echo ocpLabels("New labels have been added to Multilanguage support. Please translate them.");?>");</script><?php								}
			}
		}

//utils_dump("extra_transform - end");
	}

/*Priprema ExtraParams posle Request.Form
=========================================*/
	function extra_setXml($type){
		$xmlDoc = extra_getXml($type);
		if (!is_null(xml_documentElement($xmlDoc)) && (count(xml_childNodes(xml_documentElement($xmlDoc))) > 0)){
			$childs = xml_childNodes(xml_getFirstElementByTagName($xmlDoc, "type"));
			for ($i=0;$i<count($childs);$i++){
				$child = $childs[$i];
				$inputT = !is_null(xml_getAttribute($child, "inputType")) ? xml_getAttribute($child, "inputType") : NULL;
				$value = "";

				if (!is_null($inputT)){
					if ($inputT == "textDate"){
						$value = date_getFormDate(xml_nodeName($child));
					} else if ($inputT == "textDatetime"){
						$value = datetime_getFormDate(xml_nodeName($child));
					} else {
						$value = utils_requestStr(getPVar(xml_nodeName($child)), true, true);
						if ($inputT == "check" && !utils_valid($value)) {
							$value = "0";
						}
						//$value = ereg_replace("''", "'", $value);//ovde nam nije potrebno pretvaranje u ''
					}
				}

				if (utils_valid($value)) $child = xml_setContent($xmlDoc, $child, rawurlencode($value));
				else $child = xml_setContent($xmlDoc, $child, "");
			}
			return xml_xml($xmlDoc);
		} else {
			return NULL;
		}
	}

/*Priprema jednog noda u extraParams xml-u
==========================================*/
	function  extra_prepareChild($xmlDoc, $node){
		global $validate;

		if (xml_nodeName($node) == "#comment") return $node;
		$node = xml_setContent($xmlDoc, $node, rawurldecode(xml_getContent($node)));

		$insertedLabel = false;
		if (xml_attributes($node)){
			$inputT = !is_null(xml_getAttribute($node, "inputType")) ? xml_getAttribute($node, "inputType") : NULL;
			if (!is_null(xml_getAttribute($node, "validate"))){
				$valFunctions = split(",", xml_getAttribute($node, "validate"));

				for ($k=0; $k<count($valFunctions); $k++){
					if (utils_valid($valFunctions[$k])){
//utils_log($valFunctions[$k], "validate.log");
						if ($valFunctions[$k] == "is_necessary"){
							if (!is_null($inputT)){
								if (($inputT == "textDate") || ($inputT == "textDatetime")){
									$validate .= " && is_necessary('formObject.".xml_nodeName($node)."', '".$inputT."')"; 
								} else {
									$validate .= " && is_necessary('formObject.".xml_nodeName($node)."', null,  '".ocpLabels(xml_getAttribute($node, "label"))."')";
								}
							} else {
								$validate .= " && is_necessary('formObject.".xml_nodeName($node)."', null,  '".ocpLabels(xml_getAttribute($node, "label"))."')";
							}	
						} else {
							$validate .= " && ".$valFunctions[$k]."('formObject.".xml_nodeName($node)."')";
							?><script language="javascript" src="/ocp/validate/user/<?php echo $valFunctions[$k]?>.js"></script><?php
						}
					}
				}
				
			}
				
			if (!is_null($inputT)){
				switch ($inputT){
					case "select":
					case "radio":	
								$tipListe = $inputT;
								$listName = xml_getAttribute($node, "listName");
								$tipListe = ($tipListe=="select") ? "Selects" : "Radios";
								$lista = selrad_getListValues($listName, $tipListe);

								$allValue = "";
								$allLabel = "";
								for ($l=0;$l<count($lista);$l++){
									$slog = $lista[$l];
									$allValue .= $slog["Vrednost"]."|@$";
									if (!is_numeric($slog["Labela"])){
										$allLabel .= ocpLabels($slog["Labela"])."|@$";
										$insertedLabel = lang_newLabela($slog["Labela"]) || $insertedLabel;
									} else {
										$allLabel .= $slog["Labela"]."|@$";
									}
								}
								xml_setAttribute($node, "allvalues", utils_substr($allValue, 0, utils_strlen($allValue)-3));
								xml_setAttribute($node, "alllabels", utils_substr($allLabel, 0, utils_strlen($allLabel)-3));

								break;
					case "complex" :
						$podtipName = xml_getAttribute($node, "podtip");
						if ($podtipName == "Verzija" || $podtipName == "Sekcija" || $podtipName == "Stranica"){

							$records = ($podtipName == "Verzija") ? 
									con_getResults("select Verz_Id as Id, Verz_Naziv as Naziv from Verzija where Verz_Valid=1 order by Verz_Id") : (
									($podtipName == "Sekcija") ? 
										con_getResults("select Sekc_Id as Id, Sekc_Naziv as Naziv from Sekcija where Sekc_Valid=1 order by Sekc_Verz_Id, Sekc_RedPrikaza") : 
										con_getResults("select Stra_Id as Id, Stra_Naziv as Naziv from Stranica where Stra_Valid=1 order by Stra_Sekc_Id, Stra_RedPrikaza")
								);
							$allValues = "";
							$allLabels = "";
							for ($h=0;$h<count($records);$h++) {
								$allValues .= $records[$h]["Id"]."|@$"; 
								$allLabels .= $records[$h]["Naziv"]."|@$"; 
							}
							xml_setAttribute($node, "allvalues", utils_substr($allValues, 0, utils_strlen($allValues)-3));
							xml_setAttribute($node, "alllabels", utils_substr($allLabels, 0, utils_strlen($allLabels)-3));
						} else {
							$records = obj_getAll($podtipName, null, null, null);
							$allValues = "";
							for ($h=0;$h<count($records);$h++) {
								$allValues .= $records[$h]["Id"]."|@$"; }
							$allLabels = xml_generateRecordsIdenString($podtipName, $records);
							xml_setAttribute($node, "allvalues", utils_substr($allValues, 0, utils_strlen($allValues)-3));
							xml_setAttribute($node, "alllabels", $allLabels);
						}
						break;
					case "foreignKey":
						?><script language="javascript" src="/ocp/jscript/select.js"></script><?php
						$restrict = xml_getAttribute($node, "where") ? xml_getAttribute($node, "where") : NULL;
						$records = obj_getForeignKeyObjectsSimple(xml_getAttribute($node, "podtip"), NULL, xml_getContent($node), $restrict);
						if (count($records) > 0)
							$node = xml_complexControl($node, $records, "complex", $xmlDoc, $restrict, 1);
						break;
					case "fkAutoComplete":
						require_once("../ocp/controls/auto_complete/require.php");
						$podtip = xml_getAttribute($node, "podtip");
						$value = xml_getContent($node);
						if ($value > 0){
							xml_setAttribute($node, "value_label", xml_generateRecordIdenString($podtip, obj_get($podtip, $value), 0));
						}
					case "color":
						?><script language="javascript" src="/ocp/jscript/pallete.js"></script><?php
						break;
					case "textDate":
					case "textDatetime":
						?><script language="javascript" src="/ocp/jscript/helpcalendar.js"></script><?php
						if (utils_valid(xml_getContent($node)) && ($inputT == "textDatetime")) 
							$node = xml_setContent($xmlDoc, $node, datetime_format4Database(xml_getContent($node)));
						else if (utils_valid(xml_getContent($node))) 
							$node = xml_setContent($xmlDoc, $node, date_format4Database(xml_getContent($node)));
						break;
					default:
						break;
				}
			}

			//labela
			if (!is_null(xml_getAttribute($node, "label")) && ($inputT != "html-editor")){
				$insertedLabel = lang_newLabela(xml_getAttribute($node, "label")) || $insertedLabel;
				xml_setAttribute($node, "label", ocpLabels(xml_getAttribute($node, "label")));
			}
		}
		return $insertedLabel;
	}

	function extra_appendLabels($node){
		xml_setAttribute($node, "Title", ocpLabels("EXTRA PARAMETERS"));
		xml_setAttribute($node, "labCalendar", ocpLabels("Calendar"));
		xml_setAttribute($node, "labCreateLinkOnPage", ocpLabels("Create link on OCP page"));
		xml_setAttribute($node, "labCreateLinkOnBlock", ocpLabels("Create link on block"));
		xml_setAttribute($node, "labBrowseServer", ocpLabels("Browse server"));
		xml_setAttribute($node, "labSelectedImagePreview", ocpLabels("Selected image preview"));
		xml_setAttribute($node, "labSelectedLinkPreview", ocpLabels("Selected link preview"));
		xml_setAttribute($node, "labRichTextFormat", ocpLabels("Rich text format"));
		xml_setAttribute($node, "labColorPallete", ocpLabels("Color pallete"));
		xml_setAttribute($node, "labSelect", ocpLabels("Choose"));
	}
?>