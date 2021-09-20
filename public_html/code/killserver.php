<?php
	require_once("../ocp/include/connect.php");
	require_once("../ocp/include/utils.php");

	$cn = new dbase();
	$cn->open();

	$rst = con_getResults("select * from Blok where Blok_Valid=1");

	utils_dump(count($rst));

	for ($broj=0; $broj < count($rst); $broj++) {
		$id = $rst[$broj]["Blok_Id"];
		$oldstring = $rst[$broj]["Blok_XmlPodaci"];
		
		$oldstring = killserver_replace($oldstring, rawurlencode("http:///code"));
		$oldstring = killserver_replace($oldstring, rawurlencode("http://www.ocp-prevod.co.yu"));
		$oldstring = killserver_replace($oldstring, rawurlencode("http://217.169.211.18:8045"));
		
		if ($oldstring != $rst[$broj]["Blok_XmlPodaci"]){
			$strSql2 = "update Blok set Blok_XmlPodaci = '".$oldstring."' where Blok_Id=".$id;
			echo($strSql2."<br>");
			$cn->query($strSql2);
		}
	}
	$cn->close();

	
	function killserver_replace($org, $rep){
		if (substr_count($org, $rep) > 0)
			echo("<b>".$rep."</b><br>".$org."<br><br>");
		$org = str_replace($rep, "", $org);
		return $org;
	}

?>