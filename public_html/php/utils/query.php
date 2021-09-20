<?php
	require_once("../php/utils/navChain.php");
	
	$params = $_SESSION["urlParams"];
	$noOnPage = $params["noOnPage"];
	unset($_SESSION["urlParams"]);

	if (!utils_valid($noOnPage)) $noOnPage = 20;

	if (isset($_GET["SearchText"]))
	{
		query_displayResults(utils_requestStr($_GET["SearchText"]), $noOnPage);
	}

/******** Funkcije ********/
	function query_displayResults($content, $noOnPage){
		global $Id;

		$VerzLabele = getSVar("VerzLabele");

		$offset = utils_requestInt(getGVar("offset"));

		$results = array();
		$recordCount = 0;
		if (utils_valid($content)){
			$strSQL = query_getSql(menu_getVerzId(), null, $content);
			$recordCount = query_getCnt($strSQL);
			$results = query_getResults($strSQL, $noOnPage, $offset);
		}
?>
		<div class="block" id="query">
			<h3>
					<?php echo menu_getVerzLabel("query_parameter"); ?><span class="upit"> &quot;<?php echo utils_requestStr($_GET["SearchText"]); ?>&quot;</span>. 
					<?php echo menu_getVerzLabel("query_number");?>&nbsp;<?php echo $recordCount?>.
			</h3>
<?php		echo utils_getNavigationChain(menu_getStraLink($Id)."?SearchText=".$content, $recordCount, $noOnPage, $offset);
?>			
			<div>
				<ul>
<?php			for ($i=0; $i<count($results); $i++){
					$stranica = $results[$i];
?>
					<li>
						<h2><a href="<?php echo menu_getStraLink($stranica["Stra_Id"]);?>"><?php echo $stranica["Stra_HtmlTitle"];?></a></h2>
						<p class='preamble'><?php echo $stranica["Path"];?>
<?php							if (utils_valid($stranica["Stra_HtmlKeywords"])){?>
							<br><?php echo menu_getVerzLabel("query_keywords");?>: <?php echo $stranica["Stra_HtmlKeywords"];?><?php	}	?>
						</p>
					</li><?php		
			}
?>
				</ul>
			</div>
		</div>
<?php
	}

	function query_getCnt($strSQL){
		return con_getValue("select count(distinct Stra_Id) " . $strSQL);
	}
	
	function query_getResults($strSQL, $noOnPage, $offset){
		global $menu;

		$strSQL = "select distinct Stra_Id, Stra_Naziv, Stra_HtmlTitle, Stra_HtmlKeywords " 
				. $strSQL . 
				" limit " . ($noOnPage*$offset) . ", " . $noOnPage;
//utils_dump($strSQL);

		$stranice = con_getResults($strSQL);
		for ($i = 0; $i < count($stranice); $i++)
			$stranice[$i]["Path"] = $menu->getPath($stranice[$i]["Stra_Id"]);
		return $stranice;
	}

	function query_getSql($Verz_Id, $Sekc_Id, $content){
		$Tekst_SQL = "(Blok_Tekst like '%";
		$Keywords_SQL = "(Stra_HtmlKeywords like '%";
		$Title_SQL = "(Stra_Naziv like '%";

		$re = "/\sAND\s/";
		
		$content1 = preg_replace($re, "%' and Blok_Tekst like '%", $content);
		$content2 = preg_replace($re, "%' and Stra_HtmlKeywords like '%", $content);
		$content3 = preg_replace($re, "%' and Stra_Naziv like '%", $content);

		$re = "/\sOR\s/";

		$content1 = preg_replace($re, "%' or Blok_Tekst like '%", $content1);
		$content2 = preg_replace($re, "%' or Stra_HtmlKeywords like '%", $content2);
		$content3 = preg_replace($re, "%' or Stra_Naziv like '%", $content3);

		$Tekst_SQL .= $content1;
		$Tekst_SQL .= "%')";
		$Keywords_SQL .= $content2;
		$Keywords_SQL .= "%')";
		$Title_SQL .= $content3;
		$Title_SQL .= "%')";

		$strToday = date("m/d/Y");

		$strSQL = "";
		if (!is_null($Sekc_Id)){//samo u sekciji
			$strSQL .= " from Stranica ";
			$strSQL .= "		inner join Stranica_Blok on StBl_Stra_Id = Stra_Id";
			$strSQL .= "		inner join Blok on StBl_Blok_Id = Blok_Id";
			$strSQL .= " where Stra_Valid=1 and Stra_Sekc_Id=".$Sekc_Id." and Blok_Valid=1 and ";
		} else { //sve u verziji
			$strSQL .= " from Sekcija ";
			$strSQL .= "		inner join Stranica on Sekc_Id=Stra_Sekc_Id";
			$strSQL .= "		inner join Stranica_Blok on StBl_Stra_Id = Stra_Id";
			$strSQL .= "		inner join Blok on StBl_Blok_Id = Blok_Id";
			$strSQL .= " where Stra_Valid=1 and Sekc_Verz_Id=".$Verz_Id." and Blok_Valid=1 and ";
		}

		$strSQL .= " (".$Tekst_SQL." or ".$Keywords_SQL." or ".$Title_SQL.") and ";
		
		$strSQL .= " ((Stra_PublishDate<='".$strToday."' and Stra_ExpiryDate>='".$strToday."')";
		$strSQL .= " or (Stra_PublishDate <= '".$strToday."' and Stra_ExpiryDate is null)";
		$strSQL .= " or (Stra_PublishDate is null and Stra_ExpiryDate >= '".$strToday."')";
		$strSQL .= " or (Stra_PublishDate is null and Stra_ExpiryDate is null))";
		$strSQL .= " and ((Blok_ExpiryDate >= '".$strToday."' and Blok_PublishDate is null)";
		$strSQL .= " or (Blok_PublishDate <= '".$strToday."' and Blok_ExpiryDate is null)";
		$strSQL .= " or ((Blok_PublishDate is null and Blok_ExpiryDate >= '".$strToday."'))";
		$strSQL .= " or (Blok_PublishDate is null and Blok_ExpiryDate is null))";

		return $strSQL;
	}
?>