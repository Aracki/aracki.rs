<?php 
	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/connect.php");		
?>
<HTML>
<HEAD>
<link rel="STYLESHEET" type="text/css" href="/ocp/style/style_ocp.css">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">
</HEAD>
<BODY leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"  class="ocp_body">
<?php
	$cn = new dbase();
	$cn->open();
	

	$cn->query("ALTER TABLE `IpZemlja` ADD INDEX IpDo (IpDo)");

	$cn->query("delete from IpZemlja");

	$fop = fopen("./ip-to-country.csv", "r");
		
	$newLine = fgets($fop, 4096);
	$i=1;
	while (!feof ($fop)) {
    	parseLine($cn, $newLine, $i);
		$newLine = fgets($fop, 4096);
    	$i++;
	}
	parseLine($cn, $newLine, $i);
	
	fclose($fop);
	$cn->close();
	echo("Obradjeno tacno ".$i." redova");

/*
	Functions section
*/
	function parseLine($cn, $line, $no){
		$lineParts = split("\",\"", $line);
		$lineParts = cleanQuotesAndTranslation($lineParts);
		
		if (count($lineParts) != 5){
			return;
		}

		$ip_od = substr($lineParts[0], 1);
		$ip_do = $lineParts[1];
		$domen = $lineParts[2];
		$skracenica = $lineParts[3];
		$zemlja = $lineParts[4];
		if (strlen($zemlja) > 0)
			$zemlja = substr($zemlja, 0, strpos($zemlja, "\""));

		$strSQL = "insert into IpZemlja (IpOd, IpDo, Domen, Skracenica, Naziv)"; 
		$strSQL .= " values (".$ip_od.", ".$ip_do.",'".$domen."', '".$skracenica."','".$zemlja."')";
		//echo($no." ".$strSQL."<br>");

		//$cn->query($strSQL);
	}

	function cleanQuotesAndTranslation($arr){
		for ($i=0; $i<count($arr); $i++){
			//trimovanje
			$tekst = $arr[$i];
			if ($tekst != ""){
				$tekst = trim($tekst);
				$tekst = str_replace("'", "`", $tekst);
				$arr[$i] = $tekst;
			}
		}

		return $arr;
	}
?>
</BODY>
</HTML>