<?php
	require_once("../include/session.php");
	require_once("../include/connect.php");
	require_once("../include/izvestaji.php");
	require_once("../include/xml_tools.php");
	require_once("../include/design/izvestaj.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/ocp/css/opsti.css">
<link rel="stylesheet" href="/ocp/css/opcije.css">
<?php  
	$reportId = utils_requestInt(getGVar("reportId"));
	$back = utils_requestStr(getGVar("back"));
	$naslov = "";
	$izvestaj = null;
	$paramDict = null;

	if (utils_valid($reportId) && ($reportId != "0")){
		$izvestaj = izv_get($reportId);
		if (!is_null($izvestaj) && isset($izvestaj["Ime"])){
			$naslov = ocpLabels($izvestaj["Ime"]);
			$paramDict = izv_getParametersDict($izvestaj["ParametarXml"]);
		}
	}
?>
<title><?php echo $naslov?></title>
<script src="/ocp/jscript/tools.js"></script>
</head>
<body class="ocp_body">
<?php  
	$userHeight = utils_valid(getSVar("ocpUserHeight")) ? getSVar("ocpUserHeight") : "25%";
?><script language="javascript">
	window.onload = function(){
		parent.document.getElementById("resizableFrameset").setAttribute("rows", "<?php echo $userHeight?>,*");
	}
</script>
	<table class="ocp_naslov_table">
		<tr>
			<td class="ocp_naslov_td"><?php echo $naslov?></td>
			<td class="ocp_naslov_td" align="right"><?php   if ($back == "true"){?><a href="javascript:history.back()" class="ocp_link"><?php echo ocpLabels("Back")?></a>&nbsp;<?php  
			}?><a href="javascript:printURL()" class="ocp_link"><?php echo ocpLabels("Print")?></a></td>
		</tr>
	</table><?php  
	if (!is_null($izvestaj) && isset($izvestaj["Ime"])){
		$paramArray = izvexecute_getAllParameters($izvestaj["Upit"]);
		$paramQueryString = "?reportId=" . $izvestaj["DetaljniIzvestaj"];
//utils_dump($paramArray, 1);
		if (count($paramArray) > 0){ //izvrsavanje upita
			for ($i=0; $i<count($paramArray); $i++){
				if (isset($paramDict[$paramArray[$i]]))
					$izvestaj["Upit"] = izvexecute_putParam($izvestaj["Upit"], $paramArray[$i], $paramDict[$paramArray[$i]]);
				else 
					$izvestaj["Upit"] = izvexecute_putParam($izvestaj["Upit"], $paramArray[$i], "textBox");
			}
		}

		$queries = explode(";", $izvestaj["Upit"]);

	?><!-- print_start --><?php  

		for ($i=0; $i<count($queries); $i++){
			if (utils_valid($queries[$i])){
				$queries[$i] = izvexecute_prepareSql($queries[$i]);

//utils_dump($queries[$i]);
				
				$results = con_getResults($queries[$i]);

				if (count($results) > 0){
					$firstRow = $results[0];
					$keys  = array_keys($firstRow);
					izvdesign_reportHeader($keys);
					for ($j=0; $j<count($results); $j++){
						if ($i == 0){
							izvdesign_reportRow($keys, $results[$j], $izvestaj["DetaljniIzvestaj"]);
						} else {
							izvdesign_reportRow($keys, $results[$j], 0);
						}
						
					}
					izvdesign_reportFooter();
				} else {
					require_once("../include/design/message.php");
					echo(message_info(ocpLabels("There are no data in database")));
				}
			}
		}

	?><!-- print_end --><?php  	
		
	} else {
		require_once("../include/design/message.php");
		echo(message_info(ocpLabels("There are no data in database")));
	}
?>
</body>
</html>