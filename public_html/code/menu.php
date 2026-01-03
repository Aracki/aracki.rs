<?php
	require_once("../code/lib.php");
	require_once("../code/menu_functions.php");
	require_once("../code/extraparams.php");
	require_once("../ocp/siteManager/lib/root.php");
	require_once("../ocp/siteManager/lib/verzija.php");
	require_once("../ocp/siteManager/lib/sekcija.php");

	class Menu{
		var $straid = 0, $xmlDoc = NULL;

		
		//konfiguracioni parametri - pocetak

		//vrednosti dubina su apsolutne pocevsi od verzije
		//verzija dubina 0
		//sekcije prvog nivoa dubina 1
		//sekcije drugog nivoa dubina 2 ...

		var $topDepth = 1;		//najvisi nivo koji se ispisuje,po defaultu je 1 - sekcija prvog nivoa
		var $downDepth = 1000;	//najnizi nivo koji se ispisuje, po defaultu je 1000, ispisuje se sve
		var $showAll = false;	//
		//konfiguracioni parametri - kraj

		var $selectedIdsArr = array();

		/*Inicijalno postavljanje parametara, neophodan poziv na vrhu templatea
		========================================================================*/
		function init(){
			$this->straid = utils_requestInt(getGVar("Id"));
			if (!utils_valid($this->straid) || ($this->straid == 0)) 
				$this->straid = utils_requestInt(getPVar("Stra_Id"));

			$this->xmlDoc = xml_loadXML(lib_getXmlMenu());

			$this->selectedIdsArr = lib_getParentsIdArray($this->straid);

			$_SESSION["sekcVerz"] = lib_getStrInfo($this->straid);
		}
		
		/*Vraca meni u vidu query string (flash)
		========================================*/
		function getQueryString($delimiter = NULL){
			global $VerzLabele;

			if (!utils_valid($delimiter)) $delimiter = ",";
			$verzNode = xml_getFirstElementByTagName($this->xmlDoc, "verzija_" . menu_getVerzId());
			$sekcNodes = xml_childNodes($verzNode);
			
			$sections = "sections=";
			$ids = "&amp;ids=";
			$title = "&amp;title=";
			$selected = "&amp;selected=";
			
			for ($i=0; $i < count($sekcNodes); $i++) {//obilazimo sve sekcije 0 dubine
				$sekcNode = $sekcNodes[$i];
				$id = xml_getAttribute($sekcNode, "id");
				$pocetna = xml_getAttribute($sekcNode, "pocetna");
				$naziv = xml_getAttribute($sekcNode, "naziv");

				if (!utils_valid($pocetna)) continue; //nema mu spasa
				$sections .= keyboard_convert(rawurlencode($naziv)) . $delimiter;
				$ids .= $id  . $delimiter;
				if ($id == menu_getSekcFirstLevelId()) $selected .= $i;

			}
			
			$title .= keyboard_convert(rawurlencode(menu_getStraNaziv()));
			if ($sections != "sections="){
				$sections = utils_substr($sections, 0, utils_strlen($sections) - utils_strlen($delimiter));
				$ids = utils_substr($ids, 0, utils_strlen($ids) - utils_strlen($delimiter));
			}

			return $sections . $ids . $title . $selected . "&amp;verzId=" . menu_getVerzId();
		}
		
		/*Vraca meni u ul liste
		========================*/
		function getInner($items = NULL) {
			$first = 0;

			if (is_null($items)){//inicijalizacija prvi put
				//korekcija dubina koje ogranicavaju ispis
				if (!is_numeric($this->topDepth) || ($this->topDepth < 0)) $this->topDepth = 1;
				if (!is_numeric($this->downDepth) || ($this->topDepth < 1)) $this->downDepth = 1000;
//utils_dump($this->topDepth." ".$this->downDepth);
//utils_dump($this->selectedIdsArr, 1);

				echo("<ul>");
				$first = 1;

				if ($this->topDepth >= count($this->selectedIdsArr)) return;
				
				$realIndex = ($this->topDepth > 0) ? $this->topDepth-1 : $this->topDepth;
				$node = xml_getFirstElementByTagName($this->xmlDoc, $this->selectedIdsArr[$realIndex]);
				if (!is_null($node)){
					if (xml_getAttribute($node, "dubina") == 0 && $this->topDepth == 0){
						echo("<li class=\"opened\">");
						echo("<a href=\"" . xml_getAttribute($node, "link") . "\"	class=\"selected\">" . xml_getAttribute($node, "naziv") . "</a><ul>");
					}
					$items = xml_childNodes($node);
				}
			}

			for ($i=0; $i < count($items); $i++){
				$node = $items[$i];
				
				$id = xml_getAttribute($node, "id");
				$naziv = xml_getAttribute($node, "naziv");
				$dubina = xml_getAttribute($node, "dubina");
				$pocetna = xml_getAttribute($node, "pocetna");
				$link = xml_getAttribute($node, "link");

				if ($dubina > $this->downDepth) break;

				if (!is_numeric($pocetna)) continue; //nema mu spasa

				$opened = xml_getAttribute($node, "opened");
				$selected = in_array(xml_nodeName($node), $this->selectedIdsArr);

				echo("<li");
				if ($opened || $selected)
					echo (" class='opened'");
				echo(">");
				
				echo("<a href='".$link."'");
				if ($selected) echo (" class='selected'");
				echo(">".$naziv."</a>");

				if (($opened || $selected || $this->showAll) && xml_hasChildNodes($node) && $dubina < $this->downDepth) {
					echo("<ul>");
					$this->getInner(xml_childNodes($node));
					echo("</ul>");
				}
				echo("</li>");
			}

			if ($first){
				if ($this->topDepth == 0) echo("</ul></li>");
				echo ("</ul>");
			}
		}

		/*Vraca dodatni meni
		========================*/
		function getAdditional($delimiter = NULL){
			$addMeni  = lib_getAdditionalMenu($this->straid);
			$retString = "";
			$delimiter = !utils_valid($delimiter) ? " | " : $delimiter;

			if (count($addMeni) > 0){
				for ($i=0;$i<count($addMeni);$i++){
					$addClan = $addMeni[$i];
					$idLinka = 0;
					$nazivLinka = "";

					if (utils_valid($addClan["Meni_Stra_Id"]) && ($addClan["Meni_Stra_Id"] != 0)){
						$idLinka = $addClan["Meni_Stra_Id"];
						$nazivLinka = $addClan["Naziv"];
					} else {
						//prvo analogija
						$analogie = lib_getAnalogiePageInVersion($this->straid, $addClan["Meni_Verz_To_Id"]);
						if (utils_valid($analogie) && is_numeric($analogie)){
							$idLinka = $analogie;
							$nazivLinka = $addClan["Naziv"];
						} else { // ako nema pocetna verzije 
							$homePage = lib_getHomePageVerzija($addClan["Meni_Verz_To_Id"], true);
							if (utils_valid($homePage) && is_numeric($homePage)){
								$idLinka = $homePage;
								$nazivLinka = $addClan["Naziv"];
							}
						}
					}
					
					if ($idLinka != 0)
						$retString .= "<a href='".utils_getStraLink($idLinka)."'>".$nazivLinka."</a>".$delimiter;
				}
			}

			if ($retString != "") $retString = utils_substr($retString, 0, utils_strlen($retString) - strlen($delimiter));

			return $retString;
		}
		
		/*Vraca donji meni, slicno query string meniju
		===============================================*/
		function getLower($delimiter = NULL){
			$delimiter = !utils_valid($delimiter) ? " | " : $delimiter;
			$verzNode = xml_getFirstElementByTagName($this->xmlDoc, "verzija_" . menu_getVerzId());
			$sekcNodes = xml_childNodes($verzNode);

			for ($i=0;$i<count($sekcNodes);$i++) {//obilazimo sve sekcije 0 dubine
				$sekcNode = xml_item($sekcNodes, $i);
				$id = xml_getAttribute($sekcNode, "id");
				$pocetna = xml_getAttribute($sekcNode, "pocetna");
				$naziv = xml_getAttribute($sekcNode, "naziv");
				$link = xml_getAttribute($sekcNode, "link");

				if (!is_numeric($pocetna)) continue; //nema mu spasa			
				
				echo("<a href='".$link."'>".utils_toLower($naziv)."</a>");
				if (($i+1) < count($sekcNodes)) echo($delimiter);
			}
		}
		
		/*Vraca path ka stranici $straId, ako taj parametar nije zadat podrazumeva se tekuca stranica
		=============================================================================================*/
		function getPath($straId = 0, $delimiter = NULL){
			global $VerzLabele;

			$straId = ($straId == 0) ? $this->straid : $straId;
			$delimiter = !utils_valid($delimiter) ? " > " : $delimiter;

			$pathStr = "";
			$path = lib_getStranicaPath($straId, menu_getVerzLabel("homepage_name"));
			for ($i=0; $i<count($path); $i++){
				if (utils_valid ($path[$i]["Stra_Url"]))
					$pathStr = "<a href='" . $path[$i]["Stra_Url"] . "'>" . $path[$i]["Stra_Naziv"] . "</a>" . $delimiter . $pathStr;
				else 
					$pathStr = $path[$i]["Stra_Naziv"] . $path[$i]["Stra_Url"] . $delimiter . $pathStr;
			}
			
			return (utils_substr($pathStr, 0, utils_strlen($pathStr) - strlen($delimiter))); 
		}
	}

?>