<?php 
	require_once("../../include/session.php");
	require_once("../../include/xml_tools.php");


	$treeFilter = utils_requestStr(getGVar("treeFilter"));
	$root = is_integer(strrpos($treeFilter, ",")) ? substr($treeFilter, 0, strrpos($treeFilter, ",")) : $treeFilter;
	$basicFolder = is_integer(strrpos($treeFilter, ",")) ? substr($treeFilter, strrpos($treeFilter, ",")+1) : "";
	if (substr($basicFolder, 0, 1) == "/") $basicFolder = substr($basicFolder, 1);
	$basicFolderArray  = split("/", $basicFolder);

//kreiranje xml-a
	$xmlDom = xml_createObject();
// root
	$folderNode = xml_CreateElement($xmlDom, "root");
	xml_setAttribute($folderNode, "Root_Naziv", $root);
	xml_setAttribute($folderNode, "collapsed", "1");
	xml_setAttribute($folderNode, "right", "4/");
	xml_setAttribute($folderNode, "labelCollapse", ocpLabels("collapse all"));
	xml_setAttribute($folderNode, "labelExpand", ocpLabels("expand all"));
	xml_setAttribute($folderNode, "labelMenu", ocpLabels("Additional menu"));
	xml_setAttribute($folderNode, "labelWait", ocpLabels("executing... please wait..."));

	if ($root != "/")
		xml_setAttribute($folderNode, "Root_Id", $root."/");
	else
		xml_setAttribute($folderNode, "path", "/");

	if ($root != "" && $root != "/") $root = "/" . substr($root, 1, strlen($root));
	else $root = "";
	
	if ($root != ""){
		//rekurzivno obilazak i xml
		getAllFolderFiles(realpath("../../.."), $root, null);
	
		xml_appendChild($xmlDom, $folderNode);

		echo(xml_xml($xmlDom));
	}

	

	function getAllFolderFiles($webRoot, $root, $parentNode){
		global $xmlDom, $folderNode, $basicFolderArray;
		
		$files = scandir($webRoot.$root);
			
		for ($j=0; $j<count($files); $j++){
			$file = $files[$j];

			if ($file == "." || $file == "..") continue;
			if (!is_dir("$webRoot$root/$file")) continue;

			// folderi
			if (!is_null($parentNode)){
				if (xml_nodeName($parentNode) == "version"){
					$thisNode = xml_CreateElement($xmlDom, "section");
					xml_setAttribute($thisNode, "Sekc_ParentId", "null/");			
				} else {
					$thisNode = xml_CreateElement($xmlDom, "subsection");
					xml_setAttribute($thisNode, "Sekc_ParentId", xml_getAttribute($parentNode, "Sekc_Id"));			
				}
				xml_setAttribute($thisNode, "Sekc_Naziv", $file);
				xml_setAttribute($thisNode, "Sekc_Id", "$root/$file/");
				xml_setAttribute($thisNode, "collapsed", "1");
				for ($i=0; $i < count($basicFolderArray); $i++) {
					if ($file == $basicFolderArray[$i])
						xml_setAttribute($thisNode, "collapsed", "0");
				}
				xml_setAttribute($thisNode, "right", "4/");
			} else {
				$thisNode = xml_CreateElement($xmlDom, "version");
				xml_setAttribute($thisNode, "Verz_Naziv", $file);
				xml_setAttribute($thisNode, "Verz_Id", "$root/$file/");
				xml_setAttribute($thisNode, "collapsed", "1");
				for ($i=0; $i < count($basicFolderArray); $i++) {
					if ($file == $basicFolderArray[$i])
						xml_setAttribute($thisNode, "collapsed", "0");
				}
				xml_setAttribute($thisNode, "right", "4/");
				xml_setAttribute($thisNode, "Verz_Sekc_Id", "null/");
			}
			if (!is_null($parentNode))
				xml_appendChild($parentNode, $thisNode);
			else 
				xml_appendChild($folderNode, $thisNode);
			getAllFolderFiles($webRoot, $root.'/'.$file, $thisNode);
		}
	}
	
?>