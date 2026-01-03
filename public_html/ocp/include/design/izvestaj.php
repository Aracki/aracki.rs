<?php
	function izvdesign_parseForm($report, $params){
		global $xmlDoc, $validateFunc;
//utils_dump($xmlDoc, 1);

		$elemBody = xml_createElement($xmlDoc, "parameters");
		xml_appendChild($xmlDoc, $elemBody);
		
		xml_setAttribute($elemBody, "reportId", $report["Id"]);
		xml_setAttribute($elemBody, "title", ocpLabels($report["Ime"]));
		xml_setAttribute($elemBody, "labCalendar", ocpLabels("Calendar"));
		xml_setAttribute($elemBody, "labSearch", ocpLabels("Search"));
		xml_setAttribute($elemBody, "labCancel", ocpLabels("Cancel"));

		$insertedLabel = 0;

//preparing parameters for xml
		if (count($params)>0){
			for ($i=0; $i<count($params); $i++){
				$param = $params[$i];

				$param["name"] = substr($param["name"], 1);

				$paramNode = xml_createElement($xmlDoc, "parameter");

				xml_appendChild($elemBody, $paramNode);

				$insertedLabel = lang_newLabela($param["label"]) || $insertedLabel;

				xml_setAttribute($paramNode, "label", ocpLabels($param["label"]));
				xml_setAttribute($paramNode, "name", $param["name"]);
				xml_setAttribute($paramNode, "inputType", $param["inputType"]);
				xml_setAttribute($paramNode, "listName", isset($param["listName"]) ? $param["listName"] : "");
				xml_setAttribute($paramNode, "podtip", isset($param["podtip"]) ? $param["podtip"] : "");
				xml_setAttribute($paramNode, "where", isset($param["where"]) ? $param["where"] : "");
				xml_setAttribute($paramNode, "url", isset($param["url"]) ? $param["url"] : "");
				
				$paramNode = xml_setContent( $xmlDoc, $paramNode, isset($param["default"]) && utils_valid($param["default"]) ? $param["default"] : "");

				switch ($param["inputType"]){
					case "select":
					case "radio":	
						$lista = selrad_getListValues($param["listName"], 
														($param["inputType"] == "select" ? "Selects" : "Radios"));
						$allValue = "";
						$allLabel = "";
						for ($l=0; $l<count($lista); $l++){
							$slog = $lista[$l];
							$allValue .= $slog["Vrednost"] . "|@$";
							if (!is_numeric($slog["Labela"])){
								$allLabel .= ocpLabels($slog["Labela"]) . "|@$";
								$insertedLabel = lang_newLabela($slog["Labela"]) || $insertedLabel;
							} else {
								$allLabel .= $slog("Labela") . "|@$";
							}
						}
						xml_setAttribute($paramNode, "allvalues", substr($allValue, 0, strlen($allValue) -3));
						xml_setAttribute($paramNode, "alllabels", substr($allLabel, 0, strlen($allLabel) -3));

						break;
					case "complex" :
						$restrict = ($param["where"]) ? $param["where"] : null;
						$records = obj_getAll($param["podtip"], null, null, $restrict);
						$allValues = "";
						for ($h=0; $h < count($records); $h++)
							$allValues .= $records[$h]["Id"] . "|@$";
						$allLabels = xml_generateRecordsIdenString($param["podtip"], $records);
						xml_setAttribute($paramNode, "allvalues", substr($allValues, 0, strlen($allValue) -3));
						xml_setAttribute($paramNode, "alllabels", $allLabels);
						break;
					case "foreignKey":
						?><script language="javascript" src="/ocp/jscript/select.js"></script><?php  
						$restrict = ($param["where"]) ? $param["where"] : null;
						$records = obj_getForeignKeyObjectsSimple($param["podtip"], null, xml_getContent($paramNode), $restrict);
						if (count($records) > 0)
							$paramNode = xml_complexControl($paramNode, $records, "complex", $xmlDoc, $restrict, 1);
						break;
					case "textDate":
					case "textDatetime":
						?><script language="javascript" src="/ocp/jscript/helpcalendar.js"></script><?php  
						if (utils_valid(xml_getContent($paramNode)) && ($param["inputType"] == "textDatetime")) 
							$paramNode = xml_setContent($xmlDoc, $paramNode, datetime_format4Database($value));
						else if (utils_valid(xml_getContent($paramNode))) 
							$paramNode = xml_setContent($xmlDoc, $paramNode, date_format4Database($value));
						break;
					default:
						break;
				}

				//validacija
				if (isset($param["validate"]) && utils_valid($param["validate"]) && $param["inputType"] != "include"){
					$valFunctions = explode(",", $param["validate"]);
					
					for ($k=0; $k<count($valFunctions); $k++){
						if (utils_valid($valFunctions[$k])){
							if ($valFunctions[$k] == "is_necessary") 
								$validateFunc[] = "is_necessary('formObject.".$param["name"]."', '".$param["inputType"]."', '".ocpLabels($param["label"])."')";
							else {
								$validateFunc[] = $valFunctions[$k]."('formObject.".$param["name"]."')";
								?><script language="javascript" src="/ocp/validate/user/<?php echo $valFunctions[$k]?>.js"></script><?php  
							}
						}
					}
					
				}

			}		
		}
//utils_dump(xml_xml($xmlDoc));
		if ($insertedLabel){
?><script>alert("<?php echo ocpLabels("New labels have been added to Multilanguage support. Please translate them.")?>");</script><?php  					
		}

	}

	function izvdesign_reportHeader($header){
	?><table class="ocp_opcije_table" border="0" cellpadding="0" cellspacing="0">
		<tr><?php  
		for ($i=0; $i<count($header); $i++){
			?><td valign="top" ><span class="ocp_opcije_tekst1"><b><?php echo utils_toUpper($header[$i])?></b></span></td><?php  		
		}	
	?></tr><?php  
	}

	function izvdesign_reportRow($header, $row, $detail){
		global $paramQueryString;
	?><tr <?php  
		if (utils_valid($detail) && ($detail != "0")){
			echo(" style='cursor:pointer'");
			echo(" onclick='window.open(\"lower.php".$paramQueryString.izvexecute_rowParamQueryString($header, $row, $paramQueryString)."&back=true\", \"_self\")'");
		}
	?>><?php  					
		for ($i=0; $i<count($header); $i++){
			?><td valign="top" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $row[$header[$i]]?></span></td><?php  		
		}	
	?></tr><?php  
	}

	function izvdesign_reportFooter(){
	?></table><?php  
	}

?>