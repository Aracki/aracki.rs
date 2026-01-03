<?php

	$site_root = $_SERVER['DOCUMENT_ROOT'];
	require_once("$site_root/ocp/siteManager/lib/root.php");
	require_once("$site_root/ocp/include/utils.php");
	require_once("$site_root/ocp/include/date.php");
	require_once("$site_root/ocp/include/string.php");
	require_once("$site_root/ocp/include/xml_tools.php");
	
	$color_v = "#E9B972";
	$color_p = "#71B8FF";

	/*EXPORT IZVESTAJA
	=======================*/

	/*Funkcija koja radi export podataka iz Visit Loga 
	ukoliko je datum poslednjeg exporta + period istekao
	====================================================*/
	function visitrep_export(){
		$roots = root_getAll();
		
		if (count($roots) > 0){
			$Root_Id = $roots[0]["Root_Id"];

			$root = root_getVisitLogData($Root_Id);
			
			$today = strtotime ("now");
			$lastExport = strtotime($root["Root_LastExport"]);
			$nextExport = date_addPeriod($lastExport, $root["Root_VisitLogPeriod"]);
			
			if ($nextExport < $today){//export situacija
				$dates_xml = visitrep_buildXml($lastExport);
				$dates = array_keys($dates_xml);
				for ($i=0; $i<count($dates); $i++){
//utils_dump($dates[$i] . " -> " . $dates_xml[$dates[$i]]);
					visitrep_mergeXml($dates[$i], $dates_xml[$dates[$i]]);
				}
				root_updateExportDate($Root_Id);
			}
		}
	}

	function visitrep_buildXml($lastExport){
		$cn = new dbase(); $cn->open();
		$dates_xml = array();
		$today = strtotime ("now");
		$strSQL = NULL;

		$lastExportInfo = getdate($lastExport);
		$lastExport = strtotime($lastExportInfo['year']."-".$lastExportInfo['mon']."-".$lastExportInfo['mday']." 00:00:00");
		while ($lastExport < $today){ //svaki dan veci od datuma poslednjeg exporta
			$lastExportInfo = getdate($lastExport);
			$helpWhere = " where YEAR(PocetniDatum)= " . $lastExportInfo["year"] . 
							" and MONTH(PocetniDatum)=" . $lastExportInfo["mon"] . 
							" and DAYOFMONTH(PocetniDatum)=" . $lastExportInfo["mday"];
			$xmlDoc = xml_createObject();
			
			$rootElement = xml_createElement($xmlDoc, "report");

			//broj jedinstvenih poseta
			$strSQL = "select count(*) as count from VisitLog";
			$strSQL .= $helpWhere;

			$record = mysql_fetch_assoc($cn->query($strSQL));
			
			$brojJedPosetaNode = xml_createElement($xmlDoc, "brojJedPoseta");
			$textNode = xml_createTextNode($xmlDoc, intval($record["count"]));
			xml_appendChild($brojJedPosetaNode, $textNode);
			xml_appendChild($rootElement, $brojJedPosetaNode);

			//broj poseta po Ip-evima
			$strSQL = "select Ip, count(Id) as brojSaIpa from VisitLog";
			$strSQL .= $helpWhere;
			$strSQL .= " group by Ip";
			$strSQL .= " order by brojSaIpa desc";
			
			$brojIpPosetaNode = xml_createElement($xmlDoc, "brojIpPoseta");
			$result = $cn->query($strSQL);
			while ($record = mysql_fetch_assoc($result)){
				$ipNode = xml_createElement($xmlDoc, "ip");
				xml_setAttribute($ipNode, "value", $record["Ip"]);
				$textNode = xml_createTextNode($xmlDoc, $record["brojSaIpa"]);
				xml_appendChild($ipNode, $textNode);
				xml_appendChild($brojIpPosetaNode, $ipNode);
			}
			xml_appendChild($rootElement, $brojIpPosetaNode);

			//broj poseta po zemaljama
			$strSQL = "select Zemlja, count(VisitLog.Id) as brojSaIpa from VisitLog";
			$strSQL .= $helpWhere;
			$strSQL .= " group by Zemlja";
			$strSQL .= " order by brojSaIpa desc";

			$brojZemljePosetaNode = xml_createElement($xmlDoc, "brojZemljePoseta");
			$result = $cn->query($strSQL);
			while ($record = mysql_fetch_assoc($result)){
				$zemljaNode = xml_createElement($xmlDoc, "zemlja");
				xml_setAttribute($zemljaNode, "value", $record["Zemlja"]);
				$textNode = xml_createTextNode($xmlDoc, $record["brojSaIpa"]);
				xml_appendChild($zemljaNode, $textNode);
				xml_appendChild($brojZemljePosetaNode, $zemljaNode);
			}
			xml_appendChild($rootElement, $brojZemljePosetaNode);

			//Posete po stranama
			$strSQL = "select Stranice from VisitLog";
			$strSQL .= $helpWhere;
			
			$straniceNode = xml_createElement($xmlDoc, "stranice");
			$stranice = array();
			$ukupnoStranice = 0;

			$result = $cn->query($strSQL);
			while ($record = mysql_fetch_assoc($result)){
				$tmpStranice = split(",", $record["Stranice"]);
				$ukupnoStranice += count($tmpStranice);
				for ($i=0; $i<count($tmpStranice); $i++){
					$nextId = intval($tmpStranice[$i]);
					if (isset($stranice[$nextId]) && !is_null($stranice[$nextId])){
						$stranice[$nextId]++;
					} else {
						$stranice[$nextId] = 1;
					}
				}
			}
			arsort($stranice, SORT_NUMERIC); reset($stranice);

			while (list ($key, $value) = each ($stranice)) {
			    $stranicaNode = xml_createElement($xmlDoc, "stranica");
				xml_setAttribute($stranicaNode, "value", $key);
				$textNode = xml_createTextNode($xmlDoc, $value);
				xml_appendChild($stranicaNode, $textNode);
				xml_appendChild($straniceNode, $stranicaNode);
			}
			xml_setAttribute($straniceNode, "ukupno", $ukupnoStranice);
			xml_appendChild($rootElement, $straniceNode);

			//duzine poseta (0s-30s, 30s-2mn, 2mn-5mn, 5mn-15mn, 15mn-30mn, 30mn-1h, 1h+)
			$strSQL = "select (UNIX_TIMESTAMP(KrajnjiDatum) - UNIX_TIMESTAMP(PocetniDatum)) as brojSec from VisitLog";
			$strSQL .= $helpWhere;
			$strSQL .= " order by brojSec asc";
			
			$duzinaPosetaNode = xml_createElement($xmlDoc, "duzinaPoseta");
			$duzine = array(0, 0, 0, 0, 0, 0, 0);
			$result = $cn->query($strSQL);
			while ($record = mysql_fetch_assoc($result)){
				$seconds = intval($record["brojSec"]);
				
				if ($seconds < 30) $duzine[0]++;
				else if ($seconds < 120) $duzine[1]++;
				else if ($seconds < 300) $duzine[2]++;
				else if ($seconds < 900) $duzine[3]++;
				else if ($seconds < 1800) $duzine[4]++;
				else if ($seconds < 3600) $duzine[5]++;
				else $duzine[6]++;
			}
			
			for ($i=0; $i<count($duzine); $i++){
				$duzinaNode = xml_createElement($xmlDoc, "duzina");
				$textNode = xml_createTextNode($xmlDoc, $duzine[$i]);
				xml_appendChild($duzinaNode, $textNode);
				xml_appendChild($duzinaPosetaNode, $duzinaNode);
			}
			xml_appendChild($rootElement, $duzinaPosetaNode);

			//posete po satima
			$strSQL = "select HOUR(PocetniDatum) as sat, count(Id) as brojPoseta from VisitLog";
			$strSQL .= $helpWhere;
			$strSQL .= " group by HOUR(PocetniDatum)";
			$strSQL .= " order by sat asc";
			
			$satPosetaNode = xml_createElement($xmlDoc, "sati");
			$sati = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
			$result = $cn->query($strSQL);
			while ($record = mysql_fetch_assoc($result)){
				$sati[intval($record["sat"])] = intval($record["brojPoseta"]);
			}
			
			for ($i=0; $i<count($sati); $i++){
				$satiNode = xml_createElement($xmlDoc, "sat");
				$textNode = xml_createTextNode($xmlDoc, $sati[$i]);
				xml_appendChild($satiNode, $textNode);
				xml_appendChild($satPosetaNode, $satiNode);
			}
			xml_appendChild($rootElement, $satPosetaNode);

			xml_appendChild($xmlDoc, $rootElement);
			$dates_xml[date("Y/m/d H:i:s", $lastExport)] = xml_xml($xmlDoc);
			$lastExport = date_addPeriod($lastExport, "D", 1);
		}
		
		$cn->query("delete from VisitLog");
		$cn->close();

		return $dates_xml;
	}

	function visitrep_mergeXml($datum, $xml){
		$cn = new dbase(); $cn->open();
		$oldXmlStr = null;
		$strSQL = "select XmlStruktura from VisitReport where Datum = '$datum'";

		$result = $cn->query($strSQL);
		$count = mysql_num_rows($result);
		if ($count == 0){ //solo datum, nije parcijalan
			$cn->query("insert into VisitReport (Datum, XmlStruktura) values ('$datum', '$xml')");
		} else { //deo datuma vec postoji, mora da se radi merge podataka
			$record = mysql_fetch_assoc($result);
			$oldXmlStr = $record["XmlStruktura"];
		}

		if (!is_null($oldXmlStr)){
			$xmlDoc = xml_loadXML($xml);
			$oldXmlDoc = xml_loadXML($oldXmlStr);

			//brojJedPoseta
			$brojJedPosetaNodes = xml_getElementsByTagName($xmlDoc, "brojJedPoseta");
			$brojJedPosetaNodesOld = xml_getElementsByTagName($oldXmlDoc, "brojJedPoseta");
			$brojJedPoseta = (is_null(xml_getContent($brojJedPosetaNodes->item(0)))) ? 0 : intval(xml_getContent($brojJedPosetaNodes->item(0)));
			$brojJedPosetaOld = (is_null(xml_getContent($brojJedPosetaNodesOld->item(0)))) ? 0 : intval(xml_getContent($brojJedPosetaNodesOld->item(0)));
			xml_setContent($xmlDoc, $brojJedPosetaNodes->item(0), $brojJedPoseta + $brojJedPosetaOld);
			
			//brojStranica
			$straniceNodes = xml_getElementsByTagName($xmlDoc, "stranice");
			$straniceNodesOld = xml_getElementsByTagName($oldXmlDoc, "stranice");
			$brojStranica = (is_null(xml_getAttribute($straniceNodes->item(0), "ukupno"))) ? 0 : intval(xml_getAttribute($straniceNodes->item(0), "ukupno"));
			$brojStranicaOld = (is_null(xml_getAttribute($straniceNodesOld->item(0), "ukupno"))) ? 0 : intval(xml_getAttribute($straniceNodesOld->item(0), "ukupno"));
			xml_setAttribute($straniceNodes->item(0), "ukupno", $brojStranica + $brojStranicaOld);

			//sati
			$satNodes = xml_getElementsByTagName($xmlDoc, "sat");
			$satNodesOld = xml_getElementsByTagName($oldXmlDoc, "sat");
			for ($i=0; $i<24; $i++){
				$satI = intval(xml_getContent($satNodes->item($i)));
				$satIOld = intval(xml_getContent($satNodesOld->item($i)));
				xml_setContent($xmlDoc, $satNodes->item($i), $satI + $satIOld);
			}

			//duzine
			$duzinaNodes = xml_getElementsByTagName($xmlDoc, "duzina");
			$duzinaNodesOld = xml_getElementsByTagName($oldXmlDoc, "duzina");
			for ($i=0; $i<7; $i++){
				$duzinaI = intval(xml_getContent($duzinaNodes->item($i)));
				$duzinaIOld = intval(xml_getContent($duzinaNodesOld->item($i)));
				xml_setContent($xmlDoc, $duzinaNodes->item($i), $duzinaI + $duzinaIOld);
			}

			//ip
			$ips = array();
			
			$ipovi = xml_getFirstElementByTagName($xmlDoc, "brojIpPoseta");
			$ipChilds = xml_childNodes($ipovi);
			for ($i=0; $i < count($ipChilds); $i++){
				$nextIp = xml_getAttribute($ipChilds[$i], "value");
				$nextIpNo = xml_getContent($ipChilds[$i]);
				
				if (isset($ips[$nextIp])) $ips[$nextIp] += intval($nextIpNo);
				else $ips[$nextIp] = intval($nextIpNo);
				
				xml_removeChild($ipovi, $ipChilds[$i]);
				$ipChilds = xml_childNodes($ipovi);
				$i--;
			}

			$ipovi = xml_getFirstElementByTagName($oldXmlDoc, "brojIpPoseta");
			$ipChilds = xml_childNodes($ipovi);
			for ($i=0; $i < count($ipChilds); $i++){
				$nextIp = xml_getAttribute($ipChilds[$i], "value");
				$nextIpNo = xml_getContent($ipChilds[$i]);
				
				if (isset($ips[$nextIp])) $ips[$nextIp] += intval($nextIpNo);
				else $ips[$nextIp] = intval($nextIpNo);
			}
			arsort($ips, SORT_NUMERIC); reset($ips);
			
			$brojIpPosetaNode = xml_getFirstElementByTagName($xmlDoc, "brojIpPoseta");
			while (list ($key, $value) = each ($ips)) {
				$ipNode = xml_createElement($xmlDoc, "ip");
				xml_setAttribute($ipNode, "value", $key);
				$textNode = xml_createTextNode($xmlDoc, $value);
				xml_appendChild($ipNode, $textNode);
				xml_appendChild($brojIpPosetaNode, $ipNode);
			}

			//stranice
			$stranice = array();
			
			$straniceNodes = xml_getFirstElementByTagName($xmlDoc, "stranice");
			$straniceChilds = xml_childNodes($straniceNodes);
			for ($i=0; $i < count($straniceChilds); $i++){
				$nextStrId = xml_getAttribute($straniceChilds[$i], "value");
				$nextStrNo = xml_getContent($straniceChilds[$i]);

				if (isset($stranice[$nextStrId])) $stranice[$nextStrId] += intval($nextStrNo);
				else $stranice[$nextStrId] = intval($nextStrNo);
				
				xml_removeChild($straniceNodes, $straniceChilds[$i]);
				$straniceChilds = xml_childNodes($straniceNodes);
				$i--;
			}

			$straniceNodes = xml_getFirstElementByTagName($oldXmlDoc, "stranice");
			$straniceChilds = xml_childNodes($straniceNodes);
			for ($i=0; $i < count($straniceChilds); $i++){
				$nextStrId = xml_getAttribute($straniceChilds[$i], "value");
				$nextStrNo = xml_getContent($straniceChilds[$i]);

				if (isset($stranice[$nextStrId])) $stranice[$nextStrId] += intval($nextStrNo);
				else $stranice[$nextStrId] = intval($nextStrNo);
				
			}
			arsort($stranice, SORT_NUMERIC); reset($stranice);
			
			$straniceNode = xml_getFirstElementByTagName($xmlDoc, "stranice");
			while (list ($key, $value) = each ($stranice)) {
				$stranicaNode = xml_createElement($xmlDoc, "stranica");
				xml_setAttribute($stranicaNode, "value", $key);
				$textNode = xml_createTextNode($xmlDoc, $value);
				xml_appendChild($stranicaNode, $textNode);
				xml_appendChild($straniceNode, $stranicaNode);
			}

			//drzave
			$drzave = array();

			$zemlje = xml_getFirstElementByTagName($xmlDoc, "brojZemljePoseta");
			$zemljeChilds = xml_childNodes($zemlje);
			for ($i=0; $i < count($zemljeChilds); $i++){
				$nextZemNaziv = xml_getAttribute($zemljeChilds[$i], "value");
				$nextZemNo = xml_getContent($zemljeChilds[$i]);
				
				if (isset($drzave[$nextZemNaziv])) $drzave[$nextZemNaziv] += intval($nextZemNo);
				else $drzave[$nextZemNaziv] = intval($nextZemNo);
				
				xml_removeChild($zemlje, $zemljeChilds[$i]);
				$zemljeChilds = xml_childNodes($zemlje);
				$i--;
			}

			$zemlje = xml_getFirstElementByTagName($oldXmlDoc, "brojZemljePoseta");
			$zemljeChilds = xml_childNodes($zemlje);
			for ($i=0; $i < count($zemljeChilds); $i++){
				$nextZemNaziv = xml_getAttribute($zemljeChilds[$i], "value");
				$nextZemNo = xml_getContent($zemljeChilds[$i]);
				
				if (isset($drzave[$nextZemNaziv])) $drzave[$nextZemNaziv] += intval($nextZemNo);
				else $drzave[$nextZemNaziv] = intval($nextZemNo);
			}
			arsort($drzave, SORT_NUMERIC); reset($drzave);
			
			$brojDrzavaNode = xml_getFirstElementByTagName($xmlDoc, "brojZemljePoseta");
			while (list ($key, $value) = each ($drzave)) {
				$zemljaNode = xml_createElement($xmlDoc, "zemlja");
				xml_setAttribute($zemljaNode, "value", $key);
				$textNode = xml_createTextNode($xmlDoc, $value);
				xml_appendChild($zemljaNode, $textNode);
				xml_appendChild($brojDrzavaNode, $zemljaNode);
			}
			
			$cn->query("update VisitReport set XmlStruktura = '".xml_xml($xmlDoc)."' where Datum='$datum'");

		}
		$cn->close();
	}

	/*GENERISANJE IZVESTAJA
	=======================*/
	function visitrep_generate($mesec, $godina){
		global $color_p, $color_v;

		$cn = new dbase(); $cn->open();
		
		$strSQL = "select Datum, XmlStruktura from VisitReport";
		$strSQL .= " where MONTH(Datum)=$mesec and YEAR(Datum)=$godina";	

		//mesecni podaci
		$mo_visits = 0;
		$mo_pages = 0;
		$mo_hours = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$mo_ips = array();
		$mo_zemlje = array();
		$mo_duzine = array(0, 0, 0, 0, 0, 0, 0);
		$days_by_week = array();
		$mo_stranice = array();

		//izvestaj po danima
		$days = array();
		
		//Response.Write(strSQL);
		$result = $cn->query($strSQL);
		while ($record = mysql_fetch_assoc($result)){
			$datumInfo = getdate(strtotime($record["Datum"]));
			$day = $datumInfo['mday']; $day_in_week = $datumInfo['wday'];
			
			$dict = array();

			$xmlDoc = xml_loadXML($record["XmlStruktura"]);
			
			$poseteNodes = xml_getElementsByTagName($xmlDoc, "brojJedPoseta");			
			$brojJedPoseta = (is_null(xml_getContent($poseteNodes->item(0)))) ? 0 : intval(xml_getContent($poseteNodes->item(0)));
			$straniceNodes = xml_getElementsByTagName($xmlDoc, "stranice");
			$brojStranica = (is_null(xml_getAttribute($straniceNodes->item(0), "ukupno"))) ? 0 : intval(xml_getAttribute($straniceNodes->item(0), "ukupno"));

			$dict["poseta"] = $brojJedPoseta;
			$dict["stranica"] = $brojStranica;
			$days[$day] = $dict;
			
			//mesecni podaci
			$mo_visits += $brojJedPoseta;
			$mo_pages += $brojStranica;

			//nedeljne informacije
			if (isset($days_by_week[$day_in_week]) && !is_null($days_by_week[$day_in_week])){
				$days_by_week[$day_in_week]["poseta"] += $brojJedPoseta;
				$days_by_week[$day_in_week]["stranica"] += $brojStranica;
			} else {
				$days_by_week[$day_in_week] = $dict;
			}
			                            
			//sati
			$sati = xml_getFirstElementByTagName($xmlDoc, "sati");
			$satiChilds = xml_childNodes($sati);
			$realIndex = 0;
			for ($i=0; $i < count($satiChilds); $i++){
				$mo_hours[$realIndex] = $mo_hours[$realIndex] + intval(xml_getContent($satiChilds[$i]));
				$realIndex++;
			}
			//ipovi
			$ipovi = xml_getFirstElementByTagName($xmlDoc, "brojIpPoseta");
			$ipChilds = xml_childNodes($ipovi);
			for ($i=0; $i < count($ipChilds); $i++){
				$nextIp = xml_getAttribute($ipChilds[$i], "value");
				$nextIpNo = xml_getContent($ipChilds[$i]);
				
				if ((substr_count($nextIp, "66.249.65.") != 0) ||
					(substr_count($nextIp, "66.249.66.") != 0) ||
					(substr_count($nextIp, "65.214.45.") != 0) ||
					($nextIp == "128.194.135.81") ||
					(substr_count($nextIp, "65.54.188.") != 0) ||
					($nextIp == "207.44.198.66")){
					continue;
				} else {
					if (isset($mo_ips[$nextIp])) $mo_ips[$nextIp] += intval($nextIpNo);
					else $mo_ips[$nextIp] = intval($nextIpNo);
				}
			}

			//zemlje
			$zemlje = xml_getFirstElementByTagName($xmlDoc, "brojZemljePoseta");
			$zemljeChilds = xml_childNodes($zemlje);
			for ($i=0; $i<count($zemljeChilds); $i++){
				$nextZemNaziv = xml_getAttribute($zemljeChilds[$i], "value");
				$nextZemNo = xml_getContent($zemljeChilds[$i]);
				
				if (!isset($nextZemNaziv) || is_null($nextZemNaziv) || ($nextZemNaziv == "")) continue;
				
				if (isset($mo_zemlje[$nextZemNaziv])) $mo_zemlje[$nextZemNaziv] += intval($nextZemNo);
				else $mo_zemlje[$nextZemNaziv] = intval($nextZemNo);
			}
			
			//duzine
			$duzine = xml_getFirstElementByTagName($xmlDoc, "duzinaPoseta");
			$duzineChilds = xml_childNodes($duzine);
			$realIndex = 0;
			for ($i=0; $i < count($duzineChilds); $i++){
				$mo_duzine[$realIndex] = $mo_duzine[$realIndex] + intval(xml_getContent($duzineChilds[$i]));
				$realIndex++;
			}
			//stranice
			$straniceNode = xml_getFirstElementByTagName($xmlDoc, "stranice");
			$straniceChilds = xml_childNodes($straniceNode);
			for ($i=0; $i < count($straniceChilds); $i++){
				$nextStrId = xml_getAttribute($straniceChilds[$i], "value");
				$nextStrNo = xml_getContent($straniceChilds[$i]);
				if (intval($nextStrId) <= 0) continue;
				
				if (isset($mo_stranice[$nextStrId])) $mo_stranice[$nextStrId] += intval($nextStrNo);
				else $mo_stranice[$nextStrId] = intval($nextStrNo);
			}
		}

/*ISPIS IZVESATAJA*/

		//naslovni
		$mo_odnos_v_p = ($mo_visits != 0) ? visitrep_round($mo_pages/$mo_visits) : $mo_visits;
?><table class="ocp_opcije_table">
      <tr>
        <td width="13%" class="ocp_opcije_td_header" style="white-space: nowrap;"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("NUMBER OF VISITS")?></span></td>
        <td width="13%" class="ocp_opcije_td_header" style="white-space: nowrap;"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("NUMBER OF PAGES")?></span></td>
      </tr>
      <tr>
        <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $mo_visits?></span></td>
        <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $mo_pages?></span></td>
      </tr>
    </table><?php

//GODISNJI
	visitrep_generateAnual($godina);
		
//MESECNI PO DANIMA
		$brojDanaUMesecu = date_getNoDaysInMonth($mesec, $godina); //poslednji dan posmatranog meseca
?><table class="ocp_blokovi_table">
	<tr>
		<td class="ocp_blokovi_td" style="PADDING-RIGHT: 0px; PADDING-LEFT: 6px; PADDING-BOTTOM: 4px; PADDING-TOP: 4px"><?php echo ocpLabels("Per day in month")?></TD>
	</tr>
</table>
<table class="ocp_opcije_table">
      <tr>
        <td class="ocp_opcije_td_header" style="white-space: nowrap;width:25%"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("DAY")?></span></td>
        <td class="ocp_opcije_td_header" style="white-space: nowrap;width:25%"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("NUMBER OF VISITS")?></span></td>
        <td class="ocp_opcije_td_header" style="white-space: nowrap;width:25%"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("NUMBER OF PAGES")?></span></td>
		<td class="ocp_opcije_td_header" style="white-space: nowrap;width:25%">&nbsp;</td>
	</tr>
<?php	$max_poseta = 1;
		for ($i = 1; $i <= $brojDanaUMesecu; $i++){	
			$day = null; $poseta = $stranica = 0; 
			if (isset($days[$i])) $day = $days[$i];
			
			if (!is_null($day)){
				$poseta = intval($day["poseta"]); $max_poseta = max($poseta, $max_poseta);
				$stranica = intval($day["stranica"]); $max_poseta = max($stranica, $max_poseta);
			}
		}

		for ($i = 1; $i <= $brojDanaUMesecu; $i++){	
			$day = null; $poseta = $stranica = 0; 
			if (isset($days[$i])) $day = $days[$i];
			
			if (!is_null($day)){
				$poseta = intval($day["poseta"]); $stranica = intval($day["stranica"]);
			}
		?>
			<tr>
				<td align="left" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $i?>, <?php echo date_getMonth(intval($mesec)-1)?>, <?php echo $godina?></span></td>
				<td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $poseta?></span></td>
				<td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $stranica?></span></td>
				<td class="ocp_opcije_td">
					<?php echo visitrep_drawBar(($poseta/$max_poseta)*100, $color_v, true)?>
					<?php echo visitrep_drawBar(($stranica/$max_poseta)*100, $color_p, true)?>
				</td>
			</tr>
<?php	}	?>
</table><?php

//POSETE PO DANIMA U  NEDELJI
?><table class="ocp_blokovi_table">
	<tr>
		<td class="ocp_blokovi_td" style="PADDING-RIGHT: 0px; PADDING-LEFT: 6px; PADDING-BOTTOM: 4px; PADDING-TOP: 4px"><?php echo ocpLabels("Per day in week")?></TD>
	</tr>
</table>
<table class="ocp_opcije_table">
      <tr>
        <td class="ocp_opcije_td_header" style="white-space: nowrap;width:25%"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("DAY")?></span></td>
        <td class="ocp_opcije_td_header" style="white-space: nowrap;width:25%"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("NUMBER OF VISITS")?></span></td>
        <td class="ocp_opcije_td_header" style="white-space: nowrap;width:25%"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("NUMBER OF PAGES")?></span></td>
		<td class="ocp_opcije_td_header" style="white-space: nowrap;width:25%">&nbsp;</td>
	</tr><?php	
		$max_poseta = 1;
		for ($i = 0; $i < 7; $i++){
			$week_day = null;$poseta = $stranica = 0;
			if (isset($days_by_week[$i])) $week_day = $days_by_week[$i];
			
			if (!is_null($week_day)){
				$poseta = intval($week_day["poseta"]);	$max_poseta = max($poseta, $max_poseta);
				$stranica = intval($week_day["stranica"]);	$max_poseta = max($stranica, $max_poseta);
			}
		}

		for ($i = 0; $i < 7; $i++){	
			$week_day = null;$poseta = $stranica = 0;
			if (isset($days_by_week[$i])) $week_day = $days_by_week[$i];
			
			if (!is_null($week_day)){
				$poseta = intval($week_day["poseta"]);	$stranica = intval($week_day["stranica"]);
			}
		?>
			<tr>
				<td align="left" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo date_getDate($i)?></span></td>
				<td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $poseta?></span></td>
				<td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $stranica?></span></td>
				<td class="ocp_opcije_td">
					<?php echo visitrep_drawBar(($poseta/$max_poseta)*100, $color_v, true)?>
					<?php echo visitrep_drawBar(($stranica/$max_poseta)*100, $color_p, true)?>
				</td>
			</tr>
	<?php	}	?>
		</table><?php

//PO SATIMA
?><table class="ocp_blokovi_table">
	<tr>
		<td class="ocp_blokovi_td" style="PADDING-RIGHT: 0px; PADDING-LEFT: 6px; PADDING-BOTTOM: 4px; PADDING-TOP: 4px"><?php echo ocpLabels("Per hours")?></TD>
	</tr>
</table>
<table class="ocp_opcije_table">
      <tr>
        <td class="ocp_opcije_td_header" style="white-space: nowrap;width:33%"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("HOUR")?></span></td>
        <td class="ocp_opcije_td_header" style="white-space: nowrap;width:33%"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("NUMBER OF VISITS")?></span></td>
		<td class="ocp_opcije_td_header" style="white-space: nowrap;width:34%">&nbsp;</td>
	</tr><?php	
		$max_poseta = 1;
		for ($i = 0; $i < 24; $i++){	
			$poseta = 0;
			if (!is_null($mo_hours[$i])) $poseta = intval($mo_hours[$i]);
			$max_poseta = max($poseta, $max_poseta);
		}
		
		for ($i = 0; $i < 24; $i++){
			$poseta = 0;
			if (!is_null($mo_hours[$i])) $poseta = intval($mo_hours[$i]);
		?>
			<tr>
				<td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $i?></span></td>
				<td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $poseta?></span></td>
				<td class="ocp_opcije_td">
					<?php echo visitrep_drawBar(($poseta/$max_poseta)*100, $color_v, true)?>
				</td>
			</tr>
	<?php	}	?>
		</table><?php

/*SORTIRANJE NIZOVA*/		
		arsort($mo_ips, SORT_NUMERIC);reset($mo_ips);		
		arsort($mo_zemlje, SORT_NUMERIC);reset($mo_zemlje);
		arsort($mo_stranice, SORT_NUMERIC);reset($mo_stranice);

//ZEMLJE - TOP 10
		$maxCnt = min(10, count($mo_zemlje));
?><table class="ocp_blokovi_table">
	<tr>
		<td class="ocp_blokovi_td" style="PADDING-RIGHT: 0px; PADDING-LEFT: 6px; PADDING-BOTTOM: 4px; PADDING-TOP: 4px"><?php echo ocpLabels("Visits per countries")?>&nbsp;<?php if($maxCnt > 0){ ?>(<?php echo ocpLabels("first")?> <?php echo $maxCnt?>)<?php } ?></TD>
	</tr>
</table>
<table class="ocp_opcije_table">
      <tr>
        <td class="ocp_opcije_td_header" style="white-space: nowrap;width:33%"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("COUNTRY")?></span></td>
        <td class="ocp_opcije_td_header" style="white-space: nowrap;width:33%"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("NUMBER OF VISITS")?></span></td>
		<td class="ocp_opcije_td_header" style="white-space: nowrap;width:34%">&nbsp;</td>
	</tr><?php	
		$max_poseta = (count($mo_zemlje) > 0) ? max(array_values($mo_zemlje)) : 1;
		while (list ($key, $value) = each ($mo_zemlje)) {
		?><tr>
				<td align="left" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo strtoupper($key)?></span></td>
				<td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $value?></span></td>
				<td class="ocp_opcije_td">
					<?php echo visitrep_drawBar(($value/$max_poseta)*100, $color_v, true)?>
				</td>
			</tr>
	<?php	$maxCnt--; if ($maxCnt <= 0) break;
		}	?>
		</table><?php		

//HOSTOVI - TOP 10
	$maxCnt = min(10, count($mo_ips));
?><table class="ocp_blokovi_table">
	<tr>
		<td class="ocp_blokovi_td" style="padding: 4px 0px 4px 6px;">
			<?php echo ocpLabels("Visits per ips"); ?>&nbsp;
			<?php if ($maxCnt > 0) { ?>(<?php echo ocpLabels("first"); ?> <?php echo $maxCnt; ?>)<?php } ?>
		</td>
	</tr>
</table>
<table class="ocp_opcije_table">
      <tr>
        <td class="ocp_opcije_td_header" style="white-space: nowrap;width:33%"><span class="ocp_opcije_tekst3">IP</span></td>
        <td class="ocp_opcije_td_header" style="white-space: nowrap;width:33%"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("NUMBER OF VISITS")?></span></td>
		<td class="ocp_opcije_td_header" style="white-space: nowrap;width:34%">&nbsp;</td>
	</tr><?php	
		$max_poseta = (count($mo_ips) > 0) ? max(array_values($mo_ips)) : 1;
		while (list ($key, $value) = each ($mo_ips)) {
	?><tr>
				<td align="left" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $key?></span></td>
				<td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $value?></span></td>
				<td class="ocp_opcije_td">
					<?php echo visitrep_drawBar(($value/$max_poseta)*100, $color_v, true)?>
				</td>
			</tr>
	<?php	$maxCnt--; if ($maxCnt <= 0) break;
		}	?>
		</table><?php	

//POSETE PO DUZINI
		visitrep_duzine($mo_duzine, $mo_visits);

//POSETE PO STRANICAMA - TOP 10
		$maxCnt = min(10, count($mo_stranice));
?><table class="ocp_blokovi_table">
	<tr>
		<td class="ocp_blokovi_td" style="padding: 4px 0px 4px 6px;">
			<?php echo ocpLabels("Visits per pages"); ?>&nbsp;
			<?php if ($maxCnt > 0) { ?>(<?php echo ocpLabels("first"); ?> <?php echo $maxCnt; ?>)<?php } ?></td>
	</tr>
</table>
<table class="ocp_opcije_table">
      <tr>
        <td class="ocp_opcije_td_header" style="white-space: nowrap;width:33%"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("PAGE")?></span></td>
        <td class="ocp_opcije_td_header" style="white-space: nowrap;width:33%"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("NUMBER OF VISITS")?></span></td>
		<td class="ocp_opcije_td_header" style="white-space: nowrap;width:34%">&nbsp;</td>
	</tr><?php
		$max_poseta = (count($mo_stranice) > 0) ? max(array_values($mo_stranice)) : 1;
		while (list ($key, $value) = each ($mo_stranice)) {
			if (intval($key) <= 0) continue;
			$naziv = con_getValue("select Stra_Naziv from Stranica where Stra_Id=".$key);
			$naziv = (utils_valid($naziv)) ? $naziv : ocpLabels("Object doesn\'t exist anymore");
		?><tr>
				<td align="left" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><a href="/code/navigate.php?Id=<?php echo $key?>" class="ocp_link" target="_blank"><?php echo $naziv?></a></span></td>
				<td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $value?></span></td>
				<td class="ocp_opcije_td"><?php echo visitrep_drawBar(($value/$max_poseta)*100, $color_v, true)?></td>
			</tr>
	<?php	$maxCnt--; if ($maxCnt <= 0) break;
	}	?>
		</table><?php	
		$cn->close();
	}

		
	function visitrep_generateAnual($godina){
		global $color_p, $color_v;
		
		$strSQL = "select Datum, XmlStruktura from VisitReport where YEAR(Datum)=".$godina;	

		//izvestaj po mesecima
		$mo_posete = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
		$mo_stranice = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
		         
		$max_posete = 1; $max_stranice = 1;
		
		$cn = new dbase(); $cn->open();
		$result = $cn->query($strSQL);
		while ($record = mysql_fetch_assoc($result)){
			$dict = array();

			$month = date("m", strtotime($record["Datum"])) - 1;
			$xml = $record["XmlStruktura"];

			$xmlDoc = xml_loadXML($xml);
			$posetaNodes = xml_getElementsByTagName($xmlDoc, "brojJedPoseta");
			$brojJedPoseta = (is_null(xml_getContent($posetaNodes->item(0)))) ? 0 : intval(xml_getContent($posetaNodes->item(0)));
			$straniceNodes = xml_getElementsByTagName($xmlDoc, "stranice");
			$brojStranica = (is_null(xml_getAttribute($straniceNodes->item(0), "ukupno"))) ? 0 : intval(xml_getAttribute($straniceNodes->item(0), "ukupno"));

			$mo_posete[$month] = $mo_posete[$month] + $brojJedPoseta;
			$mo_stranice[$month] = $mo_stranice[$month] + $brojStranica;

			$max_posete = max($mo_posete[$month], $max_posete);
			$max_posete = max($mo_stranice[$month], $max_posete);
		}

		//-tabelarni
?><TABLE class="ocp_blokovi_table">
	<TR>
		<TD class="ocp_blokovi_td" style="PADDING-RIGHT:0px;PADDING-LEFT:6px;PADDING-BOTTOM:4px;PADDING-TOP:4px"><?php echo ocpLabels("Anual report")?></TD>
	</TR>
</TABLE>
<table class="ocp_opcije_table">
	<tr>
        <td class="ocp_opcije_td_header" style="white-space: nowrap;width:25%"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("MONTH")?></span></td>
        <td class="ocp_opcije_td_header" style="white-space: nowrap;width:25%"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("NUMBER OF VISITS")?></span></td>
        <td class="ocp_opcije_td_header" style="white-space: nowrap;width:25%"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("NUMBER OF PAGES")?></span></td>
        <td class="ocp_opcije_td_header" style="white-space: nowrap;width:25%">&nbsp;</td>
      </tr>
<?php		for ($i = 0; $i < 12; $i++){	?>
	  <tr>
        <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo date_getMonth($i)?>, <?php echo $godina?></span></td>
        <td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $mo_posete[$i]?></span></td>
        <td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $mo_stranice[$i]?></span></td>
        <td class="ocp_opcije_td">
			<?php echo visitrep_drawBar(($mo_posete[$i]/$max_posete)*100, $color_v, true)?>
			<?php echo visitrep_drawBar(($mo_stranice[$i]/$max_posete)*100, $color_p, true)?></td>
      </tr>
<?php		}	?>
 </table><?php
		$cn->close();
	}

	function visitrep_duzine($mo_duzine, $no_visits){
		global $color_p, $color_v;
		
		$no_visits = ($no_visits != 0) ? $no_visits : 1;
?><TABLE class="ocp_blokovi_table">
	<TR>
		<TD class="ocp_blokovi_td" style="PADDING-RIGHT:0px;PADDING-LEFT:6px;PADDING-BOTTOM:4px;PADDING-TOP:4px"><?php echo ocpLabels("Visits length")?></TD>
	</TR>
</TABLE>
<table class="ocp_opcije_table">
	<tr>
        <td class="ocp_opcije_td_header" style="white-space: nowrap;width:33%;"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("LENGTH")?></span></td>
        <td class="ocp_opcije_td_header" style="white-space: nowrap;width:33%;"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("NUMBER OF VISITS")?></span></td>
        <td class="ocp_opcije_td_header" style="white-space: nowrap;width:34%;"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("PERCENTAGE")?></span></td>
      </tr>
	  <tr>
        <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1">0 - 30s</span></td>
        <td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $mo_duzine[0]?></span></td>
        <td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo visitrep_round(($mo_duzine[0]/$no_visits)*100)?>%</span></td>
	  </tr>
	  <tr>
        <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1">30s - 2min</span></td>
        <td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $mo_duzine[1]?></span></td>
        <td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo visitrep_round(($mo_duzine[1]/$no_visits)*100)?>%</span></td>
	  </tr>
	  <tr>
        <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1">2min - 5min</span></td>
        <td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $mo_duzine[2]?></span></td>
        <td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo visitrep_round(($mo_duzine[2]/$no_visits)*100)?>%</span></td>
	  </tr>		
	  <tr>
        <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1">5min - 15min</span></td>
        <td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $mo_duzine[3]?></span></td>
        <td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo visitrep_round(($mo_duzine[3]/$no_visits)*100)?>%</span></td>
	  </tr>
	  <tr>
        <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1">15min - 30min</span></td>
        <td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $mo_duzine[4]?></span></td>
        <td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo visitrep_round(($mo_duzine[4]/$no_visits)*100)?>%</span></td>
	  </tr>
	  <tr>
        <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1">30min - 1h</span></td>
        <td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $mo_duzine[5]?></span></td>
        <td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo visitrep_round(($mo_duzine[5]/$no_visits)*100)?>%</span></td>
	  </tr>			
	  <tr>
        <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1">+1h</span></td>
        <td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $mo_duzine[6]?></span></td>
        <td align="right" class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo visitrep_round(($mo_duzine[6]/$no_visits)*100)?>%</span></td>
	  </tr>
	</table><?php
	}


	function visitrep_drawBar($no, $color, $horizontal){
		$width = 3;
		$height = 5;

		if (!$horizontal){
			$str = "<table heigth='".$no."' width='".$width."' cellpadding='0' cellspacing='0'>";
			$str .= "<tr><td height='".$no."' bgColor='".$color."' width='".$width."'><img src='/ocp/img/blank.gif' width='".$width."' height='".$no."'></td></tr>";
			$str .= "</table>";
		} else {
			$str = "<table heigth='".$height."' width='".$no."' cellpadding='0' cellspacing='0' border='0'>";
			$str .= "<tr><td height='".$height."' bgColor='".$color."' width='".$no."'><img src='/ocp/img/blank.gif' width='".$no."' height='".$height."' style='border:1px #999999 solid;'></td></tr>";
			$str .= "</table>";
		}

		return $str;
 	}

	function visitrep_round($no){
		$tmpNo = 0;
		$tmpStr = "";

		$tmpNo = round($no*100);

		if ($tmpNo < 10) $tmpStr = "00" . $tmpNo;
		else if ($tmpNo < 100) $tmpStr = "0" . $tmpNo;
		else $tmpStr = "" . $tmpNo;

		return substr($tmpStr, 0, strlen($tmpStr)-2) . "." . substr($tmpStr, strlen($tmpStr)-2, 2);
	}
?>