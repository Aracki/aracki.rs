<?php
function xml_getElementsByTagName($xml, $str) {
	// return $xml->get_elements_by_tagname($str);
	return $xml->getElementsByTagName($str);
}

function xml_getElementsByTagNameCount($xml, $str, $count) {
	$nodes = $xml->getElementsByTagName($str);
	if ($nodes->length > 0) return $nodes->item($count);
	return NULL;
}

function xml_getFirstElementByTagName($xml, $str) {
	// $nodes = $xml->get_elements_by_tagname($str);
	$nodes = $xml->getElementsByTagName($str);
	// if (count($nodes) > 0) return $nodes[0];
	if ($nodes->length > 0) { return $nodes->item(0); } else { return NULL; }
}

function xml_childNodes($xml) {
	$retArray = array();
	// $childArray = $xml->child_nodes();
	if (isset($xml)) {
		$childArray = $xml->childNodes;
		for ($i = 0; $i < $childArray->length; $i++){
			// $node = $childArray[$i];
			$node = $childArray->item($i);
			// if ($node->node_type() == XML_ELEMENT_NODE)
			if ($node->nodeType == XML_ELEMENT_NODE)
				$retArray[] = $node;
		}
	}

	return $retArray;
}

function xml_firstChild($xml) {
	$childArray = $xml->childNodes;
	return $childArray->item(0);
}

function xml_nodeName($xml) {
	// return $xml->node_name();
	return $xml->nodeName;
}

function xml_nodeValue($xml) {
	// return $xml->node_name();
	return $xml->nodeValue;
}

function xml_setNodeValue($node, $value) {
	$node->nodeValue = $value;
	return $node;
}

function xml_nodeType($xml){
	// return $xml->node_type();
	return $xml->nodeType;
}

function xml_attributes($xml) {
	// return $xml->attributes();
	return $xml->attributes;
}

function xml_attrName($attr) {
	// return $attr->name();
	return $attr->name;
}

function xml_attrValue($attr) {
	// return $attr->value();
	return $attr->value;
}

function xml_getNamedItem($xml, $str) {
	return $xml->getNamedItem($str);
}

function xml_setNamedItem($xml, $str) {
	return $xml->setNamedItem($str);
}

function xml_item($xml,$item) {
	// var_dump($xml); var_dump($item);

	return $xml[$item];
	// return $xml->item($item);
}

function xml_createObject() {
	// return domxml_new_doc("1.0");
	return new DomDocument('1.0');
}

function xml_setAttribute($xml, $str1, $str2) {
	// return $xml->set_attribute($str1, $str2);
	return $xml->setAttribute($str1, $str2);
}

function xml_createProcessingInstruction(&$xml, $str1, $str2) {
	// return $xml->create_processing_instruction($str1, $str2);
	return $xml->createProcessingInstruction($str1, $str2);
}

function xml_appendChild($xml, $n) {
	// $xml->append_child($n);
	$xml->appendChild($n);
}

function xml_createElement($xml, $str) {
	// return $xml->create_element($str);
	return $xml->createElement($str);
}

function xml_createAttribute(&$xml, $str1, $str2) {
	// $xml->create_attribute($str1, $str2);
	$xml->createAttribute($str1, $str2);
}

function xml_load($str) {
	// return domxml_open_file($str);
	$xml = new DOMDocument();
	$xml->load($str);
	return $xml;
}

function xml_save($xml, $filename) {
	$xml->save($filename);
}

function xml_loadXML($str) {
	// return domxml_open_mem($str);
	// var_dump($str);
	$xml = new DOMDocument('1.0');
	$xml->loadXML($str);
	return $xml;
}

function xml_hasChildNodes($xml) {
	// return $xml->has_child_nodes();
	return $xml->hasChildNodes();
}

function xml_documentElement($xml) {
	// return $xml->document_element();
	return $xml->documentElement;
}

function xml_parentNode($xml) {
	// return $xml->parent_node();
	return $xml->parentNode;
}

function xml_removeChild($node, $removeNode){
	// $node->remove_child($removeNode);
	$node->removeChild($removeNode);
}

function xml_nextSibling($node){
	// return $node->next_sibling();
	return $node->nextSibling;
}

function xml_createTextNode($xmlDoc, $str){
	// return $xmlDoc->create_text_node($str);
	return $xmlDoc->createTextNode($str);
}

function xml_replaceChild($parentNode, $newNode, $oldNode){
	// return $parentNode->replace_child($newNode, $oldNode);
	return $parentNode->replaceChild($newNode, $oldNode);
}

function xml_xml($xml, $encoding = NULL) {
	// if (!is_null($encoding)) $xml->dump_mem($encoding);
	if (!is_null($encoding)) $xml->saveXML($encoding);
	// return $xml->dump_mem();
	return $xml->saveXML();
}

/*Transformacija xml-a
	================================*/
	function xml_transform($xmlDoc, $xslFile, $siteManager = 0){
	/*
		// $xh = xslt_create();
		$xh = new xsltprocessor();
		$xmlString = $xmlDoc->dump_mem(true);
		$filename =	realpath(xml_getFile($xslFile));
		$fd = fopen($filename, "r");
		$xslString = fread ($fd, filesize ($filename));
		fclose ($fd);
		$arguments = array('/_xml' => $xmlString,'/_xsl' => $xslString);
		$result = xslt_process($xh, 'arg:/_xml', 'arg:/_xsl', NULL, $arguments);
		xslt_free($xh);
		return($result);
	*/

		// $xsldoc = null;
		$xsldoc = new DomDocument;
		if ($siteManager){
			// $xsldoc = domxml_xslt_stylesheet_file($xslFile);
			$xsldoc->load($xslFile);
		} else{
			// $xsldoc = domxml_xslt_stylesheet_file($_SERVER['DOCUMENT_ROOT'] . xml_getFile($xslFile));
			$xsldoc->load($_SERVER['DOCUMENT_ROOT'].xml_getFile($xslFile));
		}

		// $result  = $xsldoc->process($xmlDoc);
		$proc = new XsltProcessor;
		$xsl = $proc->ImportStylesheet($xsldoc);
		$result = $proc->transformToDoc($xmlDoc);

		// return $result->dump_mem();
		return $result->saveXML();
	}

	/*Vraca vrednost zadatog atributa zadatog noda
	==============================================*/
	function xml_getAttribute($node, $name){
		if (isset($node)) {
			// $attList = $node->attributes();
			$attList = $node->attributes;
			if (isset($attList) && !is_null($attList)){
				foreach ($attList as $att){
					// if ($att->name() == $name){
					if ($att->name == $name){
						// return $att->value();
						return $att->value;
					}
				}
			}
		} else {
			return NULL;
		}
	}

	/*xml_getContent: opsta XML funkcija koja vraca sadrzaj teksta noda
	============================================================= */
	function xml_getContent($node){
		// $children = $node->child_nodes();
//		$children =	$node->childNodes;
//		if ($children){
//			foreach ($children as $child){
//				// if ($child->node_type() == XML_TEXT_NODE){
//				if ($child->nodeType == XML_TEXT_NODE){
//					// return $child->node_value();
//					return $child->nodeValue;
//					break;
//				}
//			}
//		}
		return $node->nodeValue;
	}
	/*opsta XML funkcija koja postavlja / replace-uje
	  sadrzaj teksta noda
	==================================================*/
	function xml_setContent($xmlDoc, $node, $value){
		// $children = $node->child_nodes();
//		$children =	$node->childNodes; // var_dump($children);
//		if ($children){
//			foreach ($children as $child){
//				// if ($child->node_type() == XML_TEXT_NODE){
//				if ($child->nodeType == XML_TEXT_NODE){
//					// $node->remove_child($child);
//					$node->removeChild($child);
//					break;
//				}
//			}
//		}
//		// $textNode = $xmlDoc->create_text_node($value);
//		// $textNode = new DomText();
//		$textNode = $xmlDoc->createTextNode($value);
//		// $node->append_child($textNode);
//		// var_dump($node);
//		$node->appendChild($textNode);
		$value = str_replace("&", "&amp;", $value);

		$node->nodeValue = $value;
		return $node;
	}

	/*klonira node	i svu njegovu decu, rekurzivno
	==============================================*/
	function xml_cloneNode($doc, $node){
		if (!is_null($node)){
//utils_dump("kloniram node ".$node->nodeType);
			switch($node->nodeType){
				case XML_ELEMENT_NODE:
				$xml_cloneNode = $doc->createElement($node->nodeName);
				if ($atr = $node->attributes){
					for ($i = 0; $i < $atr->length;$i++){
						$newAtr = $atr->item($i);
						$xml_cloneNode->setAttribute($newAtr->name, $newAtr->value);
					}
				}
				break;
				case XML_TEXT_NODE;
				$xml_cloneNode = $doc->createTextNode($node->nodeValue);
				break;
				case XML_COMMENT_NODE;
				$xml_cloneNode = $doc->createComment($node->nodeValue);
				break;
				default:
				die('error [xml_cloneNode]: unknown node type!');
			}
			$childs = $node->childNodes;
			if (isset($childs) && !is_null($childs)){
				for ($i = 0; $i < $childs->length;$i++){
					$newNode = xml_cloneNode($doc, $childs->item($i));
					if (!is_null($newNode)){
						$xml_cloneNode->appendChild($newNode);
					}
				}
			}
			return $xml_cloneNode;
		}else
		return null;
	}

	function xml_convertChar($str, $oldChar, $newChar, $encode = false){
		if ($encode) $str = rawurlencode($str);
		$str = str_replace($oldChar, $newChar, $str);
		if ($encode) $str = rawurldecode($str);
		return $str;
	}

?>