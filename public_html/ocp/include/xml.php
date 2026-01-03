<?php
$xmlDelimiter = "|@$";

/*vraca ime xsl filea iz 
/ocp/objectManager/style/config.xml file
========================================*/
	function xml_getFile($ime){
		$doc = xml_load($_SERVER['DOCUMENT_ROOT']."/ocp/objectManager/style/config.xml");
		$cvor = xml_getFirstElementByTagName($doc, $ime);
		$nameOfFile = (!is_null($cvor)) ? $nameOfFile = xml_getContent($cvor) : "";
		return $nameOfFile;
	}

/*Vraca xml doc iz baze 
===========================*/
	function xml_getInputXml(){
		$inputXml = stripcslashes(con_getValue("select InputXml from Ocp where Id=1"));
		if (!utils_valid($inputXml)) 
			$inputXml = "<clientdef></clientdef>";
		return $inputXml;

	}

/*Vraca xml u bazu
==================================*/
	function xml_putInputXml($doc){
		$docStr = xml_xml($doc);
		$docStr = str_replace("\'", "'", $docStr);
		$docStr = str_replace("'", "\'", $docStr);
		con_update("update Ocp set InputXml = '".$docStr."' where Id=1");
	}

/*Fja koja kreira element
=========================*/
	function xml_setElement($xml, $ime, $vrednost){
		$newNode = xml_createElement($xml, $ime);
		return xml_setContent($xml, $newNode, $vrednost);
	}

/*Fja koja vraca input, name, labela, validate
==============================================*/
	function xml_getFieldNode($cvorList){
		$result = array();
		for ($j=0; $j < count($cvorList); $j++)
			$result[xml_nodeName($cvorList[$j])] = xml_getContent($cvorList[$j]);
		return $result;
	}

/*Fja koja vraca fields node asseta tipa imeTipa
================================================*/
	function xml_getAssetNode($imeTipa, $vrsta){
		$doc = xml_loadXML(xml_getInputXml());

		$searchName = xml_getSearchNodeName($imeTipa);
//utils_dump($imeTipa." ".$vrsta." ".$searchName);

		$assettypeList = xml_getElementsByTagName($doc, $searchName);
		$retNode = NULL;

		for ($i=0; $i < $assettypeList->length; $i++) {
			$assettypeNode = $assettypeList->item($i);
			if ((xml_nodeName($assettypeNode) == $searchName) && (xml_getContent($assettypeNode) == $imeTipa)) {
				while (true) {
					$fieldsNode = xml_nextSibling($assettypeNode);
					if (is_null($fieldsNode)) break;
					if (xml_nodeName($fieldsNode) == $vrsta) {
						$retNode = $fieldsNode;
						break;
					}
					$assettypeNode = $fieldsNode;
				}
			}
		}
		return $retNode;
	}

/*Vrati ime polja po kom se sortira pretrage, to je prvo ime u xmlu sa iden="1"
=============================================================================*/
	function xml_getSortName($imeTipa){
		$retString = "";

		$fieldsNode = xml_getAssetNode($imeTipa, "fields");
		$fieldNodes = xml_childNodes($fieldsNode);
		for ($j =0; $j< count($fieldNodes); $j++){
			$fieldNode = $fieldNodes[$j];
			if (xml_getAttribute($fieldNode, "iden") == "1"){
				$dictNode = xml_getFieldNode(xml_childNodes($fieldNode));
				$retString = $dictNode["name"];
				break;
			}
		}

		return $retString;
	}

/*generise formu za odredjeni objekat
type --> tip cija forma se otvara
data --> objekat koja se otvara
action --> koji action forme je
U zavisnosti od actiona generise se odgovarajuci
dokument. 
==============================================*/
	function xml_generateForm($type, $data, $action, $editGroup = NULL) {
		global $xmlDoc, $validateFunc, $validateNamesFunc, $typeId, $subFormAppend, $TR, $xmlDelimiter;
//utils_dump("xml_generateForm - start");


		$insert = (is_null($data) || !isset($data["Id"]) || ($data["Id"] == 0)) ? 1 : 0;
		$xmlDoc = xml_getNode($type, $insert);
		$typeId = tipobj_getId($type);

		//	change of fieldsNode
		$fieldsNode = xml_getFirstElementByTagName($xmlDoc, "fields");
		if (!is_null($fieldsNode)){
			//isprazni sve koji nemaju editGrupu datu (Id i LastModify iskljuci)
			$childs  = xml_childNodes($fieldsNode);

			for ($k=0; $k < count($childs); $k++){
				$nextNode = $childs[$k];
				if (is_null($editGroup) || ($editGroup == "")){//opsta polja se traze
					$attEditGroup = xml_getAttribute($nextNode, "editGroup");
					if (!is_null($attEditGroup) && utils_valid($attEditGroup)){
						xml_removeChild($fieldsNode, $nextNode);
						$childs = xml_childNodes($fieldsNode);
						$k--;
					}
				} else {
					$attEditGroup = xml_getAttribute($nextNode, "editGroup");
					$dictNode = xml_getFieldNode(xml_childNodes($nextNode));
					if ((!is_null($attEditGroup) && ($attEditGroup != $editGroup)) || 
						(is_null($attEditGroup) && ($dictNode["name"] != "Id") && ($dictNode["name"] != "LastModify"))){
						xml_removeChild($fieldsNode, $nextNode);
						$childs = xml_childNodes($fieldsNode);
						$k--;
					}
				}
			}

			xml_setAttribute($fieldsNode, "action", $action);
			xml_setAttribute($fieldsNode, "type", $type);
			xml_setAttribute($fieldsNode, "random", date_getMiliseconds());

			if (xml_isForm($action)){
				xml_setAttribute($fieldsNode, "right", $TR[$typeId]);
				if (utils_valid($editGroup))
					xml_setAttribute($fieldsNode, "editGroup", $editGroup);

				if ($action == "subforms.php"){
					$keys = array_keys($subFormAppend);
					for ($k=0; $k<count($keys); $k++)
						xml_setAttribute($fieldsNode, $keys[$k], $subFormAppend[$keys[$k]]);
				}
			} else {
				xml_setAttribute($fieldsNode, "typeId", $typeId);
			}

			//da li postoje editgrupe
			if (!$insert && xml_isForm($action)){
				$editGroups = xml_getDiffrentEditGroups($type);
				if (count($editGroups) > 0){
					$editGroupsStr = ""; $editGroupsLabelsStr = "";
					for ($v=0; $v< count($editGroups); $v++){
						$editGroupsStr .= $editGroups[$v];
						$editGroupsLabelsStr .= ocpLabels($editGroups[$v]);
						if (($v+1) != count($editGroups)){
							$editGroupsStr .= "|";
							$editGroupsLabelsStr .= "|";
						}
					}
					xml_setAttribute($fieldsNode, "editGroups", $editGroupsStr);
					xml_setAttribute($fieldsNode, "editGroupsLabels", $editGroupsLabelsStr);
				}
			}

			$fieldNodes	= xml_childNodes($fieldsNode);
//utils_dump("xml_generateForm - obrada fieldova start");
			if (count($fieldNodes) > 0){
				for ($i = 0; $i < count($fieldNodes); $i++){
					$fieldNode = $fieldNodes[$i];
					if (xml_nodeName($fieldNode) != "field") continue;

					if (xml_hasChildNodes($fieldNode)) {
						$dictNode = xml_getFieldNode(xml_childNodes($fieldNode)); 
//utils_dump($dictNode, 1);	
						$oldInputType = $dictNode["inputType"];
						
						$value = "";
						$name = $dictNode["name"];
						if (!is_null($data) && array_key_exists($name, $data) && utils_valid($data[$name])) 
							$value = $data[$name];
//utils_dump($name."=>".$value);
						$allvalue =	array();
						$records = array();
						$hiddenId = "";
						$restrict = NULL;
	
						xml_setAttribute($fieldNode, "label", ocpLabels($dictNode["label"]));

						/*ako je inputType labela, a forma je pretraga, moraju da se zamene inputType-ovi*/
						if (($dictNode["inputType"] == "labela") && xml_isSearchForm($action)){
							$tipTable = polja_getFieldType($type, $dictNode["name"]);
							switch($tipTable){
								case "Objects":	$dictNode["inputType"] = "complex";	break;
								case "Selects": $dictNode["inputType"] = "select";	break;
								case "Radios":	$dictNode["inputType"] = "radio";	break;
								case "Dates":	$dictNode["inputType"] = "textDatetime"; break;
								case "Bits":	$dictNode["inputType"] = "check"; break;
								default:		$dictNode["inputType"] = "textBox"; break;
							}
						}
						
						//ako ne moze da se uredjuje podforma, postavljam fk na hidden
						if (($action == "subforms.php") && ($dictNode["inputType"] == "complex")){
							if ($subFormAppend["TypeField"] == $dictNode["name"]){
								$dictNode["inputType"] = "hidden";
								$value = $subFormAppend["SuperTypeId"];
							} else if ($subFormAppend["Editable"] == ""){
								$dictNode["inputType"] = "labela";
							}
						}

//utils_dump("xml_generateForm - obrada field pre");
						switch ($dictNode["inputType"]){
							case "labela" : 
								$tipTable = polja_getFieldType($type, $dictNode["name"]);
								switch($tipTable){
									case "Objects":	
										$hiddenId = $value;
										$podtip = polja_getForeignTypeName($type, $dictNode["name"]);
										if (utils_valid($value) && $value != 0)
											$value = xml_generateRecordIdenString($podtip, obj_get($podtip, $value), 0);		
										break;
									case "Selects":	
									case "Radios":
										$hiddenId = $value;
										$value = selrad_getListLabel($type, $dictNode["name"], $dictNode["inputType"], $value);
										break;
									case "Dates":
										if (utils_valid($value)) $value = datetime_format4Database($value);
									default: break;
								}
								break;
							case "complex":	
								if (utils_valid(xml_getAttribute($fieldNode, "where")))
									$restrict = xml_getAttribute($fieldNode, "where");
								$podtip = polja_getForeignTypeName($type, $dictNode["name"]);
								if (($action == "subforms.php") && $insert && 
									($subFormAppend["SuperType"] == $podtip) && ($subFormAppend["TypeField"] == $dictNode["name"])){
									//u subformi postavljamo vestacki fk roditelja
									$value = $subFormAppend["SuperTypeId"];
								}
								xml_appendChild($fieldNode, xml_setElement($xmlDoc, "type", $podtip));
								$records = obj_getForeignKeyObjectsSimple($type, $dictNode["name"], $value, $restrict);

								break;
			  case "fkMultiple" :
                            if (utils_valid(xml_getAttribute($fieldNode, "where")))
                                $restrict = xml_getAttribute($fieldNode, "where");
                            $podtip = polja_getForeignTypeName($type, $dictNode["name"]);
                            if (($action == "subforms.php") && $insert &&
                                    ($subFormAppend["SuperType"] == $podtip) && ($subFormAppend["TypeField"] == $dictNode["name"])) {
                                //u subformi postavljamo vestacki fk roditelja
                                $value = $subFormAppend["SuperTypeId"];
                            }
                            xml_appendChild($fieldNode, xml_setElement($xmlDoc, "type", $podtip));
                            $records = obj_getForeignKeyObjectsSimple($type, $dictNode["name"], $value, $restrict);

                            break;

							case "fkAutoComplete":	
								$podtip = polja_getForeignTypeName($type, $dictNode["name"]);
								if (($action == "subforms.php") && $insert && 
									($subFormAppend["SuperType"] == $podtip) && ($subFormAppend["TypeField"] == $dictNode["name"])){
									//u subformi postavljamo vestacki fk roditelja
									$value = $subFormAppend["SuperTypeId"];
								}
								if ($value > 0){
									xml_appendChild($fieldNode, xml_setElement($xmlDoc, "value_label", xml_generateRecordIdenString($podtip, obj_get($podtip, $value), 0)));
								}
								xml_appendChild($fieldNode, xml_setElement($xmlDoc, "type", $podtip));

								break;

							case "select":	
								$allvalue = selrad_getListValuesByFieldName($type, "SelectLista", $dictNode["name"]);
								break;
							case "radio":	
								$allvalue = selrad_getListValuesByFieldName($type, "RadioLista", $dictNode["name"]);
								break;
							case "textDatetime":	
								if (utils_valid($value)) $value = datetime_format4Database($value);
								break;
							case "textDate":	
								if (utils_valid($value)) $value = date_format4Database($value);
								break;
							case "upload":	
								if (utils_valid(xml_getAttribute($fieldNode, "type")))
									$restrict = xml_getAttribute($fieldNode, "type");
								xml_appendChild($fieldNode, xml_setElement($xmlDoc, "type", "Upload"));
								$records = upload_getUploadLabels($value, $restrict);
								break;
							default: break;
						}
						if (utils_valid($value)) $value = str_replace("&quot;", "\"", $value);
						if (utils_valid($restrict)) xml_appendChild($fieldNode, xml_setElement($xmlDoc, "restrict", $restrict));

						if ($dictNode["inputType"] != $oldInputType){
							xml_removeChild($fieldNode, xml_getFirstElementByTagName($fieldNode, "inputType"));
							xml_appendChild($fieldNode, xml_setElement($xmlDoc, "inputType", $dictNode["inputType"]));						
						}

						if (count($records) > 0){ // ako je foreign key
//utils_dump("xml_generateForm - obrada complex start");
							  if ($dictNode["inputType"] == "fkMultiple") {
                            $fieldNode = xml_fkMultiple($fieldNode, $records, $xmlDoc, $data[$dictNode["name"]]);
                        } else {
                            $fieldNode = xml_complexControl($fieldNode, $records, $dictNode["inputType"], $xmlDoc, $restrict);
                        }

						}else{ // svi ostali slucajevi
							$valueNode = xml_setElement($xmlDoc, "value", $value);
							if ($hiddenId != "")	xml_setAttribute($valueNode, "hiddenId", $hiddenId);
							xml_appendChild($fieldNode, $valueNode);

							$allValue =	"";
							$allLabel =	"";
							if (count($allvalue) > 0){
								for ($l = 0; $l < count($allvalue);	$l++){
									$allValue .= $allvalue[$l]["Vrednost"] . $xmlDelimiter;
									$allLabel .= $allvalue[$l]["Labela"] . $xmlDelimiter;
								}
								xml_appendChild($fieldNode, xml_setElement($xmlDoc,"allvalues",utils_substr($allValue, 0, utils_strlen($allValue) -3)));
								xml_appendChild($fieldNode, xml_setElement($xmlDoc,"alllabels",utils_substr($allLabel, 0, utils_strlen($allLabel) -3)));
							}
						}
						
						if (!xml_isSearchForm($action) && ($dictNode["inputType"] != "hidden")){
							$validate = (isset($dictNode["validate"]))? $dictNode["validate"] : false;
							if (!is_null($validate)){
								$validateFuncs = split(",", $validate);
								for ($f = 0; $f<count($validateFuncs); $f++){
									if (!utils_valid($validateFuncs[$f])) continue;
									if (($validateFuncs[$f] == "is_necessary") && !is_null($dictNode["inputType"])){
										if (($dictNode["inputType"] == "textDate") || ($dictNode["inputType"] == "textDatetime")){
											$validateFunc[] = $validateFuncs[$f]."('formObject.".$dictNode["name"]."', '".$dictNode["inputType"]."')";
										} else {
											$validateFunc[] = $validateFuncs[$f]."('formObject.".$dictNode["name"]."', null, '".ocpLabels($dictNode["label"])."')";
										}
									} else {
										$validateFunc[] = $validateFuncs[$f]."('formObject.".$dictNode["name"]."', '".ocpLabels($dictNode["label"])."')";
									}

									if (!in_array($validateFuncs[$f], $validateNamesFunc))
										$validateNamesFunc[] = $validateFuncs[$f];
								}
							}
						}
					}
				}
			}
		}

		$subformNodes = xml_getElementsByTagName($xmlDoc, "subform");
		if ($subformNodes->length > 0){
			for ($i=0; $i < $subformNodes->length; $i++){
				$subformNode = $subformNodes->item($i);
				if (utils_valid($editGroup)){
					$parent_node = xml_parentNode($subformNode);
					xml_removeChild($parent_node, $subformNode);
					$subformNodes = xml_getElementsByTagName($xmlDoc, "subform");
					$i--; continue;
				}
				$subTypeLabel = tipobj_getLabel(null, xml_getAttribute($subformNode, "SubType"));
				xml_setAttribute($subformNode, "SubTypeLabel", ocpLabels($subTypeLabel));
				xml_setAttribute($subformNode, "Url", "SuperType=" . $type . "&SuperTypeId=" . $data["Id"]);
			}
		}
		
		$includeNodes = xml_getElementsByTagName($xmlDoc, "include");
		if ($includeNodes->length > 0){
			for ($i=0; $i < $includeNodes->length; $i++){
				$includeNode = $includeNodes->item($i);
				if (utils_valid($editGroup)){
					$parent_node = xml_parentNode($includeNode);
					xml_removeChild($parent_node, $includeNode);
					$includeNodes = xml_getElementsByTagName($xmlDoc, "include");
					$i--; continue;
				}
				xml_setAttribute($includeNode, "url", xml_getAttribute($includeNode, "url")."?Type=".$type."&TypeId=".$data["Id"]);
			}
		}

		$actionNodes = xml_getElementsByTagName($xmlDoc, "action");
		if ($actionNodes->length > 0){
			for ($i=0; $i<$actionNodes->length; $i++){
				$actionNode = $actionNodes->item($i);
				xml_setAttribute($actionNode, "label", ocpLabels(xml_getAttribute($actionNode, "label")));
			}
		}
//utils_dump(xml_xml($xmlDoc));
	}

/*generise listu objekata koji su odredjenog tipa, pri tom se 
izvlaci xml dokument objektima koji sadrze identifikacione podatke,
eventualno se provlaci tip i id objekta da mogli pozivi dalje iz 
javascripta da se generisu
==================================================*/
	function xml_generateList($type, $typeId, $data, $action) {
		global $xmlDoc;

		$TR = getSvar('ocpTR');

		$fieldsNode = xml_getAssetNode($type, "fields");

		//isprazni sve koji nisu iden=1
		$childs  = xml_childNodes($fieldsNode);
		$idIden = "1";
		for ($i=0; $i<count($childs); $i++){
			$nextNode = $childs[$i];
			$dictNode = xml_getFieldNode(xml_childNodes($nextNode));
			if ((xml_getAttribute($nextNode, "iden") != "1") && ($dictNode["name"] != "Id")){
				xml_removeChild($fieldsNode, $nextNode);
				$childs = xml_childNodes($fieldsNode);
				$i--;
			} else if ($dictNode["name"] == "Id"){
				$idIden = xml_getAttribute($nextNode, "iden");
				
				$oldNode = xml_replaceChild($fieldsNode, $nextNode, $childs[0]); 
				$childs  = xml_childNodes($fieldsNode);
				for ($j=1; $j<=$i; $j++)
					$oldNode = xml_replaceChild($fieldsNode, $oldNode, $childs[$j]);
			}
		}

		$elemBody = xml_createElement($xmlDoc, "Root");
		xml_setAttribute($elemBody, "random", date_getMiliseconds());
		xml_setAttribute($elemBody, "type", $type);
		xml_setAttribute($elemBody, "action", $action);
		xml_setAttribute($elemBody, "right", $TR[$typeId]);
		xml_setAttribute($elemBody, "idIden", $idIden);

		for ($i = 0; $i<count($data); $i++){
			$nodeCloned = xml_cloneNode($xmlDoc, $fieldsNode);
			xml_appendChild($elemBody, $nodeCloned);
			
			$fieldNodes = xml_childNodes($nodeCloned);
			if (count($fieldNodes) > 0){
				for ($j=0; $j<count($fieldNodes); $j++){
					$fieldNode = $fieldNodes[$j];
					$dictNode = xml_getFieldNode(xml_childNodes($fieldNode));
					$value = "";
					$d = $data[$i];
					if (!is_null($data) && utils_valid($dictNode["name"]))
						$value = $d[$dictNode["name"]];
					if (utils_valid($value))
						$value = xml_prepareNode($type, $dictNode, $value);
 if ($dictNode["inputType"] == "fkMultiple") {
                    $fkObjName = polja_getForeignTypeName($type, $dictNode["name"]);
                    $_ids = explode(", ", $d[$dictNode["name"]]);
                    $value = "";
                    $fkObjs = array();
                    foreach($_ids as $_id){
                        array_push($fkObjs, obj_get($fkObjName, $_id));
                    }
                    $value .= str_replace("|@$", ", ", xml_generateRecordsIdenString($fkObjName, $fkObjs));
                    
                }

					if ($dictNode["name"] != "Id"){
						xml_setAttribute($fieldNode, "label", utils_toUpper(ocpLabels($dictNode["label"])));
					} else {
						xml_setAttribute($fieldNode, "label", utils_toUpper($dictNode["label"]));
					}
					xml_appendChild($fieldNode, xml_setElement($xmlDoc, "value", $value));
				}
			}
			xml_appendChild($nodeCloned, xml_setElement($xmlDoc, "Count", $i));
		}

		$actionsNode = xml_getAssetNode($type, "actions");
		if (!is_null($actionsNode)){
			$nodeCloned = xml_cloneNode($xmlDoc, $actionsNode);
			$childs = xml_childNodes($nodeCloned);
			foreach($childs as $child)
				xml_setAttribute($child, "label", utils_toUpper(ocpLabels(xml_getAttribute($child, "label"))));
			xml_appendChild($elemBody, $nodeCloned);
		}

		xml_appendChild($xmlDoc, $elemBody);
	}

/*Generise listu polja datog tipa koji su iden
===============================================*/
	function xml_getIdenFields($type){
		$fieldsNode = xml_getAssetNode($type, "fields");
		$idenAtts = array();
		if (!is_null($fieldsNode)){
			$fieldNodes = xml_childNodes($fieldsNode);
			for ($f =0; $f < count($fieldNodes); $f++){
				$fieldNode = $fieldNodes[$f];
				if (xml_getAttribute($fieldNode, "iden") == "1"){
					$idenAtts[] = xml_getFieldNode(xml_childNodes($fieldNode));
				}
			}
		}
		return $idenAtts;
}

/*Generise listu polja datog tipa
===============================================*/
	function xml_getAllFields($type){
		$fieldsNode = xml_getAssetNode($type, "fields");
		$fields = array();
		if (!is_null($fieldsNode)){
			$fieldNodes = xml_childNodes($fieldsNode);
			for ($f =0; $f < count($fieldNodes); $f++){
				$fieldNode = $fieldNodes[$f];
				$fields[] = xml_getFieldNode(xml_childNodes($fieldNode));
			}
		}
		return $fields;
}

/* Generise string koji sadrzi identifikaciju za svaki objekat u records.
Identifikacije su razdvojene |@$ znakom.
=========================================================================*/
	function xml_generateRecordsIdenString($imeTipa, $records){
		global $xmlDelimiter;

		$retString = "";
		$idenAtts = xml_getIdenFields($imeTipa);

		for ($i=0; $i<count($records); $i++){
			$record = $records[$i];
			$idenVals = array();
			for ($j = 0; $j < count($idenAtts); $j++){
				$dictNode = $idenAtts[$j];
				$value = $record[$dictNode["name"]];
				if (utils_valid($value))
					$value = xml_prepareNode($imeTipa, $dictNode, $value);
				else $value = "-";
				$idenVals[] = $value;
			}
			//utils_dump($i." '".xml_prepareIdenString($idenVals)."'");
			$retString .= xml_prepareIdenString($idenVals) . $xmlDelimiter; // prvi_att (..|..|..)
			//utils_dump($i." '".$retString."'");
			//utils_dump("<br><br>");
		}

		$retString = utils_substr($retString, 0, utils_strlen($retString) - 3);

		//utils_dump("last ".$retString);
		return $retString;
	}

/* Generise string koji sadrzi identifikaciju za objekat slog.
Ako je samoPrvi true onda samo prvi identifikacioni atribut.
==============================================================*/
	function  xml_generateRecordIdenString($imeTipa, $slog, $samoPrvi = NULL){
		$retString = "";
		$idenVals = array();
		$idenAtts = xml_getIdenFields($imeTipa);

		for ($e =0; $e<count($idenAtts); $e++){
			$dictNode = $idenAtts[$e];
			$value = isset($slog[$dictNode["name"]]) ? $slog[$dictNode["name"]] : "";

			if (utils_valid($value))
				$value = xml_prepareNode($imeTipa, $dictNode, $value);
			else $value = "-";
			$idenVals[] = $value;

			if (!is_null($samoPrvi) && $samoPrvi) break;
		}

		if ($samoPrvi) $retString = $idenVals[0];
		else $retString = xml_prepareIdenString($idenVals);

		return $retString;
	}

/*Vraca sve checkboxove zbog pretrage
=====================================*/
	function xml_getIdenCheckbox($imeTipa){
		$checkboxes = array(); 
		$fieldsNode = xml_getAssetNode($imeTipa, "fields");
		if (!is_null($fieldsNode)){
			$fieldNodes = xml_childNodes($fieldsNode);
			for ($f =0; $f< count($fieldNodes); $f++){
				$fieldNode = $fieldNodes[$f];
				if (xml_getAttribute($fieldNode, "iden") == "1"){
					$field = xml_getFieldNode(xml_childNodes($fieldNode));
					if ($field["inputType"] == "check") 
						$checkboxes[$field["name"]] = "1";
				}
			}
		}
		return $checkboxes;
	}

/*Kreira novi nod u input.xml-u
Ovo je deo u kom se krerira novi tip u xml-u tj. dodaje se forma
za postojeci tip ili updateuje stara
=============================================================*/
	function xml_createNode($object){
		global $xmlDelimiter;

//utils_dump($object, 1);
		$newObjType	= $object["typeName"];
		$newObjTypeID = $object["typeId"];
		$cnt = $object["cnt"];

		$oldFormDoc	= xml_loadXML(xml_getInputXml());
		$newDoc = xml_createObject();
		
		$nodeTypeName = "assettype";
		$typesList = xml_getElementsByTagName($oldFormDoc, $nodeTypeName);

		$typeFound = false;
		$typeNode =	null;
		if ($typesList->length > 0){
			for ($i = 0; $i < $typesList->length; $i++){
				$typeNode =	$typesList->item($i);
				if ((xml_nodeName($typeNode) == $nodeTypeName) && (xml_getContent($typeNode) ==	$newObjType)){
					$typeFound = true;
					break;
				}
			}
		}
//utils_dump($typeFound, 1);

		if ($typeFound){
			$rootElem =	xml_createElement($newDoc, "clientdef");
//utils_dump($nodeTypeName);
			$typesList = xml_getElementsByTagName($oldFormDoc, $nodeTypeName);
			if ($typesList->length > 0){
				for ($i = 0; $i < $typesList->length; $i++){
					$typeNode =	$typesList->item($i);
//utils_dump(xml_nodeName($typeNode)." ".$nodeTypeName." ".xml_getContent($typeNode)." ".$newObjType);
					if ((xml_nodeName($typeNode) == $nodeTypeName) && (xml_getContent($typeNode) == $newObjType)){
						continue;
					}else{
						$assetNode = xml_parentNode($typeNode);
						$clonedNode	= xml_cloneNode($newDoc, $assetNode);
						xml_appendChild($rootElem, $clonedNode);
					}
				}
			}
			xml_appendChild($newDoc, $rootElem);

		}else{
			$newDoc = xml_loadXML( xml_xml($oldFormDoc));
		}
		$root = xml_documentElement($newDoc);
		$assetNode = xml_createElement($newDoc, "asset");
		$assetTypeNode = xml_createElement($newDoc, $nodeTypeName);
		xml_appendChild($assetTypeNode, xml_createTextNode($newDoc, $newObjType));
		
		$fieldsNode	= xml_createElement($newDoc, "fields");
		xml_setAttribute($fieldsNode, "action", "form.php");
		
		for ($j = 0; $j < $cnt; $j++){
			if (!isset($object["fieldtype".$j])) 
					continue;

			$labelJ = $object["label" . $j];
			$inputTypeJ	= $object["fieldtype".$j];
			$nameJ = $object["fieldName" . $j];
			$idenJ = isset($object["iden" . $j]) ? $object["iden" . $j] : 0;
			$editGroupJ = $object["editGroup" . $j];
			$rootJ = $object["root" . $j];
			$widthJ = $object["width" . $j];
			$heightJ = $object["height" . $j];
			$maxJ = $object["max" . $j];
			$importJ = $object["import".$j];
			$whereJ = $object["where".$j];		
			$valStringJ	= "";
			$val = (isset($object["validate".$j])) ? intval($object["validate".$j]) : 0;
			if ($val > 0){
				for ($k = 0; $k < $val; $k++)
					$valStringJ .= $object["_validate".$j.($k+1)] . ",";
			}
			$fieldJ = xml_createElement($newDoc, "field");
			if (utils_valid($editGroupJ))
				xml_setAttribute($fieldJ, "editGroup", $editGroupJ);
			
			if ($idenJ == "1") xml_setAttribute($fieldJ, "iden", "1");
			else xml_setAttribute($fieldJ, "iden", "0");
			
			xml_appendChild($fieldJ, xml_setElement($newDoc, "label", $labelJ));
			xml_appendChild($fieldJ, xml_setElement($newDoc, "inputType", $inputTypeJ));
			xml_appendChild($fieldJ, xml_setElement($newDoc, "name", $nameJ));
			if ($val > 0)
				xml_appendChild($fieldJ, xml_setElement($newDoc, "validate", substr($valStringJ, 0, strlen($valStringJ)-1)));
			xml_setAttribute($fieldJ, "import", $importJ);
			xml_setAttribute($fieldJ, "where", $whereJ);
			xml_setAttribute($fieldJ, "max", $maxJ);
			switch ($inputTypeJ){
				case "image":
				case "file":
				case "intLink":
				case "folder":
					xml_setAttribute($fieldJ, "root", $rootJ);
					xml_setAttribute($fieldJ, "height", $heightJ);
					xml_setAttribute($fieldJ, "width", $widthJ);
					break;
				default:break;
			}
			
			xml_appendChild($fieldsNode, $fieldJ);
		}

		$field = xml_createElement($newDoc, "field");//Id
		if (isset($object["IdenId"]) && utils_valid($object["IdenId"])) xml_setAttribute($field, "iden","1");
		else xml_setAttribute($field, "iden","0");
		xml_appendChild($field, xml_setElement($newDoc, "label", "Id")); 
		xml_appendChild($field, xml_setElement($newDoc, "inputType", "hidden"));
		xml_appendChild($field, xml_setElement($newDoc, "name", "Id"));
		xml_appendChild($fieldsNode, $field);

		$field = xml_createElement($newDoc, "field");//Last modify
		xml_setAttribute($field, "iden", "0");
		xml_appendChild($field, xml_setElement($newDoc, "label", "LastModify"));
		xml_appendChild($field, xml_setElement($newDoc, "inputType", "hidden"));
		xml_appendChild($field, xml_setElement($newDoc, "name", "LastModify"));
		xml_appendChild($fieldsNode, $field);

		$subformsNode = null; //Podforme
		if (isset($object["subform"]) && utils_valid($object["subform"]) && intval($object["subform"]) > 0){
			$subformsNode = xml_createElement($newDoc, "subforms");
			$subformCount = intval($object["subform"]);
			for ($k=0; $k < $subformCount; $k++){
				$next = $object["subform_".($k+1)];

				$subFormNode = xml_createElement($newDoc, "subform");
				xml_setAttribute($subFormNode, "SubType", substr($next, 0, strpos($next, $xmlDelimiter)));
				xml_setAttribute($subFormNode, "SubTypeField", substr($next, strpos($next, $xmlDelimiter)+3));
				xml_setAttribute($subFormNode, "Editable", $object["subform_editable_".($k+1)]);
				xml_appendChild($subformsNode, $subFormNode);
			}
		}

		$includesNode = null;//includes
		$cntInclude = isset($object["include"]) ? $object["include"] : 0;
//utils_dump($cntInclude);
		if ($cntInclude > 0){
			$includesNode = xml_createElement($newDoc, "includes");
			for ($k=0; $k < $cntInclude; $k++){
				$includeNode = xml_createElement($newDoc, "include");
				xml_setAttribute($includeNode, "label", $object["includeLabel".$k]);
				xml_setAttribute($includeNode, "url", $object["includeUrl".$k]);
				xml_appendChild($includesNode, $includeNode);
			}
			
		}

		$actionsNode = null;//actions
		$cntAction = isset($object["action"]) ? $object["action"] : 0;

		if ($cntAction > 0){
			$actionsNode = xml_createElement($newDoc, "actions");
			for ($k=0; $k < $cntAction; $k++){
				$actionNode = xml_createElement($newDoc, "action");
				xml_setAttribute($actionNode, "label", $object["actionLabel".$k]);
				xml_setAttribute($actionNode, "image", $object["actionImage".$k]);
				xml_setAttribute($actionNode, "url", $object["actionUrl".$k]);
				xml_setAttribute($actionNode, "place", $object["actionPlace".$k]);
				xml_appendChild($actionsNode, $actionNode);
			}
			
		}

		xml_appendChild($assetNode, $assetTypeNode);
		xml_appendChild($assetNode, $fieldsNode);
		if (isset($subformsNode) && !is_null($subformsNode)) xml_appendChild($assetNode, $subformsNode);
		if (isset($includesNode) && !is_null($includesNode)) xml_appendChild($assetNode, $includesNode);
		if (isset($actionsNode) && !is_null($actionsNode)) xml_appendChild($assetNode, $actionsNode);
		xml_appendChild($root, $assetNode);
//utils_dump(xml_xml($newDoc));
//die();
		xml_putInputXml($newDoc);
	}


/*Vrati ceo nod u xml-u na osnovu datog tipa
============================================*/
	function xml_getNode($imeTipa, $insert){
		$retDoc = xml_createObject();
		$doc = xml_loadXML(xml_getInputXml());
		$searchNode	= xml_getSearchNodeName($imeTipa);
		$found = false;

		$assetLista	= xml_getElementsByTagName($doc, "asset");
		
		$elemBody =	xml_createElement($retDoc, "asset");
		xml_appendChild($retDoc, $elemBody);
		if ($assetLista->length > 0){
			for ($i = 0; $i < $assetLista->length; $i++) {
				$assetNode = $assetLista->item($i);
				$assetDeca = xml_childNodes($assetNode);
				for ($j = 0;$j < count($assetDeca);$j++){
					$assetDete = $assetDeca[$j];
//utils_dump(xml_nodeName($assetDete)." ".$searchNode." ".xml_getContent($assetDete)." ".$imeTipa);
					if ((xml_nodeName($assetDete) == $searchNode) && (xml_getContent($assetDete) == $imeTipa)){
						$found = true;
						for ($k = $j + 1; $k < count($assetDeca); $k++){
							$cvor = $assetDeca[$k];
							$cvorName = xml_nodeName($cvor);
							if (($cvorName == "fields") || ($cvorName == "subforms" && !$insert) || 
								($cvorName == "includes" && !$insert) || ($cvorName == "actions")){
								$body = xml_getFirstElementByTagName($retDoc, "asset");
								if (is_null($body)){
									$elemBody =	xml_createElement($retDoc, "asset");
									xml_appendChild($elemBody, xml_cloneNode($retDoc, $cvor));
									xml_appendChild($retDoc, $elemBody);
								}else{
									xml_appendChild($body, xml_cloneNode($retDoc, $cvor));
								}
							}else{
								continue;
							}
						}
						break;
					}
				}
				if ($found) break;
			}
		}

		return (($found) ? $retDoc : null);
	}

/*Pitanje kreiranja parametara za kontrole complex i upload
===========================================================*/
	function xml_complexControl($fieldNode, $slogovi, $input, $xmlDoc, $restrict, $siteManager = NULL){
		global $xmlDelimiter;

		$brojObjekata = 20;
		$startIndex = 1;
		$control = (count($slogovi) > $brojObjekata) ? true : false;
		$chooseLabels = "";
		$d = $slogovi[0];
		$complexType = $d["Tip"];
		$sortName = "";
		$sortType = "";
		$value = "";

//prvo da izvucem sortName, po kom ce se kreirati chooseLista
		if ($control){
			if (($input == "complex") || is_integer(strpos($input, "complex_")) || is_integer(strpos($input, "labela"))){
				$sortName = xml_getSortName($complexType);
				$sortType = polja_getFieldType($complexType, $sortName);
				$podtip = $complexType;
				while ($sortType == "Objects"){
					$podtip = polja_getForeignTypeName($podtip, $sortName);
					$sortName = xml_getSortName($podtip);
					$sortType = polja_getFieldType($podtip, $sortName);
				}
			} else {
				$sortName = "Label";
			}
		}

//allValues i allLabels treba da imaju kao i do sada sve vrednosti, mora da postoji chooseList, koja sadrzi samo prve redove grupa po dvadeset i to po sortName-u, moram da imam starting index, odakle pocinje select lista
		for ($h=0; $h < count($slogovi); $h++){
			$d = $slogovi[$h];
			if ((($h+1)%$brojObjekata == 1) && ($control)) {
				//gornja granica
				switch ($sortType){
					case "Dates": $chooseLabels .= date_formatMonthYear($d[$sortName]) . "-"; break;
					default: $chooseLabels .= utils_substr($d[$sortName], 0, 3) . "-"; break;
				}
				$temp = null;
				
				if (($h + ($brojObjekata-1)) >= count($slogovi))
					$temp = $slogovi[count($slogovi)-1];
				else
					$temp = $slogovi[$h + ($brojObjekata-1)];
				if ($temp){
					switch ($sortType){ //donja granica
						case "Dates": $chooseLabels .= date_formatMonthYear($temp[$sortName]) . $xmlDelimiter; break;
						default: $chooseLabels .= utils_substr($temp[$sortName], 0, 3) . $xmlDelimiter; break; break;
					}
				}
			}
			if ($d["Selected"]=="1") {
				$value = $d["Id"];
				$startIndex = floor($h/$brojObjekata) + 1;
			}
		}
		if (utils_valid($chooseLabels)) $chooseLabels = utils_substr($chooseLabels, 0, utils_strlen($chooseLabels)-3);
		if (!is_null($siteManager) && $siteManager){ //extraparams i blokovi
			xml_setContent($xmlDoc, $fieldNode, $value);	
			xml_setAttribute($fieldNode, "startIndex", $startIndex);
			xml_setAttribute($fieldNode, "alllabels", $chooseLabels);
		} else {
			xml_appendChild($fieldNode, xml_setElement($xmlDoc, "value", $value));
			xml_appendChild($fieldNode, xml_setElement($xmlDoc, "startIndex", $startIndex));
			xml_appendChild($fieldNode, xml_setElement($xmlDoc, "chooseLabels", $chooseLabels));
		}

		return $fieldNode;
	}

/**
 *
 * @global string $xmlDelimiter delimiter string for generated xml values
 * @param DOMElement $fieldNode XML node for the given field
 * @param array $slogovi an array of existing objects for select list
 * @param DOMDocument $xmlDoc
 * @param string $selected
 * @return DOMElement
 */
function xml_fkMultiple($fieldNode, $slogovi, $xmlDoc, $selected = "") {
    global $xmlDelimiter;
    //utils_dump(xml_xml($xmlDoc), true);
    if (!empty($selected)) {
        $selected;
    }
    $chooseLabels = "";
    $complexType = $slogovi[0]["Tip"];
    $value = "";
    $records = obj_getAll($complexType, null, null, null);
    $allValues = "";
    for ($i=0;$i<count($records);$i++) {
        $allValues .= $records[$i]["Id"].$xmlDelimiter;
    }
    $allLabels = strip_tags(xml_generateRecordsIdenString($complexType, $records));

    xml_appendChild($fieldNode, xml_setElement($xmlDoc, "selected", $selected));
    xml_appendChild($fieldNode, xml_setElement($xmlDoc, "value", utils_substr($allValues, 0, utils_strlen($allValues)-strlen($xmlDelimiter))));
    //xml_appendChild($fieldNode, xml_setElement($xmlDoc, "value", $allValues));
    xml_appendChild($fieldNode, xml_setElement($xmlDoc, "startIndex", "1"));
    xml_appendChild($fieldNode, xml_setElement($xmlDoc, "chooseLabels", $allLabels));

    //utils_dump(xml_xml($xmlDoc));
    return $fieldNode;
}







/*Generise idenString prvi_att (..|..|..)	
=========================================*/
	function xml_prepareIdenString($values){
		$retStr = "";
		for ($i=0; $i < count($values); $i++){
			if ($i == 0) $retStr .= $values[$i] . " ";
			else 
				if ($i == 1) $retStr .= "(" . $values[$i]." | ";
				else $retStr .= $values[$i] . " | ";
		}

		if (count($values) == 1)
			$retStr = utils_substr($retStr, 0, utils_strlen($retStr)-1);
		else 
			$retStr = utils_substr($retStr, 0, utils_strlen($retStr) - 3) . ")";

		if (utils_strlen($retStr) > 50)
			$retStr = utils_substr($retStr, 0, 50) . "...";

//echo($retStr."<br><br>");

		return $retStr;	
	}

	/*Funkcija koja priprema na osnovu inputType-a node
	===================================================*/
	function xml_prepareNode($type, $dictNode, $value){
		if (utils_valid($value)) $value = str_replace("&quot;", "\"", $value);

		switch ($dictNode["inputType"]){
			case "textDate": 
				$value = date_format4Database($value); break;
			case "textDatetime":
				$value = datetime_format4Database($value); break;
			case "complex":
			case "fkAutoComplete":
				if (!isset($GLOBALS["ocp_recursion_cycle"]) || !utils_valid($GLOBALS["ocp_recursion_cycle"])){
//utils_dump("ocp_recursion_cycle nije validan");
					$GLOBALS["ocp_recursion_cycle"] = 1;
				} else if ($GLOBALS["ocp_recursion_cycle"] == 3){
//utils_dump("ocp_recursion_cycle jeste validan");
					$value = ""; 
					unset($GLOBALS["ocp_recursion_cycle"]);
					break;
				} else {
					$GLOBALS["ocp_recursion_cycle"]++;
				}
//utils_dump("ocp_recursion_cycle ".$GLOBALS["ocp_recursion_cycle"]);
				$podTip = polja_getForeignTypeName($type, $dictNode["name"]);
				$result = obj_get($podTip, $value);
//utils_dump("ocp_recursion_cycle ".$GLOBALS["ocp_recursion_cycle"]);
//utils_dump("xml_generateRecordIdenString poziv");
				$value = xml_generateRecordIdenString($podTip, $result); 
				unset($GLOBALS["ocp_recursion_cycle"]);
				break;
			case "select":
			case "radio":
				$value = selrad_getListLabel($type, $dictNode["name"], $dictNode["inputType"], $value); break;
			case "labela":
				$tipTable = polja_getFieldType($type, $dictNode["name"]);
				switch($tipTable){
					case "Objects":	
						$objekat = obj_getForeignKeyObject($type, $dictNode["name"], $value);
						$value = xml_generateRecordIdenString($objekat["Tip"], $objekat); break;
					case "Selects":	
					case "Radios":					
						$value = selrad_getListLabel($type, $dictNode["name"], $tipTable, $value); break;
					case "Dates":
						$value = date_format4Database($value); break;
					default: break;
				} //switch
				break;
			case "versionList":
				if ($value != 0) 
					$value = con_getValue("select Verz_Naziv from Verzija where Verz_Id=".$value);
				break;
			case "sectionList":
				if ($value != 0) 
					$value = con_getValue("select Sekc_Naziv from Sekcija where Sekc_Id=".$value);
				break;
			case "pageList":
				if ($value != 0) 
					$value = con_getValue("select Stra_Naziv from Stranica where Stra_Id=".$value);
				break;
		}
		
		return $value;
	}

	/*Funkcija koja vadi nazive razlicitih editgroupa
	===================================================*/
	function xml_getDiffrentEditGroups($imeTipa){
		$fieldsNode = xml_getAssetNode($imeTipa, "fields");
		$editGroups = array();
		if (!is_null($fieldsNode)){
			$fieldNodes = xml_childNodes($fieldsNode);
			for ($f=0; $f< count($fieldNodes); $f++){
				$fieldNode = $fieldNodes[$f];
				$editGroupAtt = xml_getAttribute($fieldNode, "editGroup");
				if (utils_valid($editGroupAtt) && !in_array($editGroupAtt, $editGroups))
					$editGroups[] = $editGroupAtt;
			}
		}
		return $editGroups;
	}

	/*Funkcija koja vadi za odredjenu editGrupu nazive svih polja
	=============================================================*/
	function xml_getEditGroupFields($imeTipa, $editGroup = NULL){
		$fieldsNode = xml_getAssetNode($imeTipa, "fields");
		$editGroupFields = array();

		if (!is_null($fieldsNode)){
			$fieldNodes = xml_childNodes($fieldsNode);
			for ($f=0; $f< count($fieldNodes); $f++){
				$fieldNode = $fieldNodes[$f];
				$editGroupAtt = xml_getAttribute($fieldNode, "editGroup");
//utils_dump("EditGroup je ".$editGroup.", a attribute je ".$editGroupAtt);
				//validna oba
				$validBoth = (utils_valid($editGroupAtt) && utils_valid($editGroup) && ($editGroup == $editGroupAtt));
				//ne validna oba
				$notvalidBoth = (!utils_valid($editGroupAtt) && !utils_valid($editGroup));

				if ($validBoth || $notvalidBoth){
					$dictNode = xml_getFieldNode(xml_childNodes($fieldNode));
					$editGroupFields[] = $dictNode["name"];
				} 
			}
		}

		return $editGroupFields;
	}

/*Brise ceo nod u xml-u na osnovu datog tipa
============================================*/
	function xml_removeNode($imeTipa){
		$doc = xml_loadXML(xml_getInputXml());
		$searchNode = xml_getSearchNodeName($imeTipa);

		$assetLista	= xml_getElementsByTagName($doc, "asset");
		$node2Remove = null;
		if ($assetLista->length > 0) {
			for ($i=0; $i < $assetLista->length; $i++) {
				$assetNode = $assetLista->item($i);
				$assetDeca = xml_childNodes($assetNode);

				for ($j=0; $j<count($assetDeca); $j++){
					$assetDete = $assetDeca[$j];
					if ((xml_nodeName($assetDete)==$searchNode) && (xml_getContent($assetDete)==$imeTipa)){
						$node2Remove = $assetNode;
						break;
					} else {
						continue;
					}
				}
				if (!is_null($node2Remove)) break;
			}
		}

		if (!is_null($node2Remove)){
			$parentNode = xml_parentNode($node2Remove);
			xml_removeChild($parentNode, $node2Remove);
			xml_putInputXml($doc);
		}
	}

/*Brise field u nekoj xml definiciji
============================================*/
	function xml_removeFieldNode($imeTipa, $imePolja){
		$doc = xml_loadXML(xml_getInputXml());
		$searchNode = xml_getSearchNodeName($imeTipa, NULL);

		$assetLista	= xml_getElementsByTagName($doc, "asset");
		$node2Search = null;
		if ($assetLista->length > 0) {
			for ($i=0; $i < $assetLista->length; $i++) {
				$assetNode = $assetLista->item($i);
				$assetDeca = xml_childNodes($assetNode);

				for ($j=0; $j<count($assetDeca); $j++){
					$assetDete = $assetDeca[$j];
					if ((xml_nodeName($assetDete)==$searchNode) && (xml_getContent($assetDete)==$imeTipa)){
						while (true) {
							$fieldsNode = xml_nextSibling($assetDete);
							if (is_null($fieldsNode)) break;
							if (xml_nodeName($fieldsNode) == "fields") {
								$node2Search = $fieldsNode;
								break;
							}
							$assetDete = $fieldsNode;
						}
						break;
					} else {
						continue;
					}
				}
				if (!is_null($node2Search)) break;
			}
		}

		$node2Remove = null;
		if (!is_null($node2Search)){
			$fieldNodes = xml_childNodes($node2Search);
			for ($f =0; $f< count($fieldNodes); $f++){
				$fieldNode = $fieldNodes[$f];
				$dictNode = xml_getFieldNode(xml_childNodes($fieldNode));

				if ($dictNode["name"] == $imePolja){
					$node2Remove = $fieldNode;
					break;
				}
			}
		}


		if (!is_null($node2Remove)){
			$parentNode = xml_parentNode($node2Remove);
			xml_removeChild($parentNode, $node2Remove);
			xml_putInputXml($doc);
		}
	}

	/*Vraca searchNode u xml-u na osnovu imena tipa
	============================================*/
	function xml_getSearchNodeName($imeTipa){
		return "assettype";
	}

	function xml_isForm($action){
		return (($action == "form.php") || ($action == "subforms.php"));
	}

	function xml_isSearchForm($action){
		return ($action == "objects.php");
	}

?>