<?php
	require_once("../include/session.php");
	require_once("../include/connect.php");
	require_once("../include/objekti.php");
	require_once("../include/selectradio.php");
	require_once("../include/tipoviobjekata.php");
	require_once("../include/polja.php");
	require_once("../include/language.php");
	require_once("../include/xml.php");
	require_once("../include/xml_tools.php");
	require_once("../include/izvestaji.php");
	require_once("../include/design/izvestaj.php");
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/ocp/css/opsti.css">
<link rel="stylesheet" href="/ocp/css/opcije.css">
<script language="javascript" src="/ocp/validate/user/is_necessary.js"></script>
<SCRIPT SRC="/ocp/jscript/helpcalendar.js" type="text/javascript"></SCRIPT>
<SCRIPT SRC="/ocp/jscript/select.js" type="text/javascript"></SCRIPT>
<SCRIPT SRC="/ocp/jscript/pallete.js" type="text/javascript"></SCRIPT>
<SCRIPT SRC="/ocp/validate/validate_double_quotes.js" type="text/javascript"></SCRIPT>
<?php require_once("../controls/auto_complete/require.php");?>
<title>Reports</title>
<script>
	simpleEditorExists = false;
	submited = false;
</script>
</head>
<body class="ocp_body"><?php  
	$reportId = utils_requestInt(getGVar("reportId"));
	$naslov = "";
	$izvestaj = array();
	$paramDef = null;

//utils_dump(reportId);
	
	if (utils_valid($reportId) && ($reportId != "0")){
		
		$izvestaj = izv_get($reportId);
		$naslov = ocpLabels($izvestaj["Ime"]);
		
		$paramDef = izv_getParameters($izvestaj["ParametarXml"]);
	}

?><SCRIPT>
	function goReport(){
		window.open("lower.php?<?php echo utils_randomQS()?>&reportId=<?php echo $reportId?>", "detailFrame");
	}
</script><?php  
//	utils_dump($izvestaj, 1);
	if (!is_null($izvestaj) && isset($izvestaj["Ime"])){

		if (count($paramDef) > 0){ //forma parametara
			$paramXml = array();
			for ($i=0; $i<count($paramDef); $i++){
				$next = $paramDef[$i];

				if (isset($next["inputType"]))
					$paramXml[] = $next;
			}

			$validateNamesFunc = array();
			$validateFunc = array();

			$validateNamesFunc[] = "is_necessary";
			
			$xmlDoc = xml_createObject();
//utils_dump("tu sam");
			izvdesign_parseForm($izvestaj, $paramXml);

//			utils_dump(xml_xml(xmlDoc));
			
			echo (xml_transform($xmlDoc, "style/parameters.xsl", 1));

			utils_getValidation($validateNamesFunc, $validateFunc);
			?><script>
				window.onload = function(){
					if (!submited){
						parent.detailFrame.location.href = "/ocp/html/blank.html";
					}
				}
			</script><?php  
		} else {
			?><script>goReport();</script><?php  			
		}
	} else {
		require_once("../include/design/message.php");
		echo(message_info(ocpLabels("There are no data in database")));
	}
?>
</body>
</html>
