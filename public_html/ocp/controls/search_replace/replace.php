<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../include/tipoviobjekata.php");
	require_once("../../include/polja.php");
	require_once("../../include/xml.php");
	require_once("../../include/xml_tools.php");
	require_once("../../include/objekti.php");
	require_once("../../include/selectradio.php");
	require_once("../../include/search_replace.php");
	require_once("../../siteManager/lib/root.php");
	require_once("../../siteManager/lib/verzija.php");
	require_once("../../siteManager/lib/sekcija.php");
	require_once("../../siteManager/lib/stranica.php");
	require_once("../../siteManager/lib/blokfunctions.php");
	require_once("../../config/triggers.php");
?>
<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="STYLESHEET" type="text/css" href="/ocp/css/opsti.css">
<link rel="STYLESHEET" type="text/css" href="/ocp/css/opcije.css">
<link rel="STYLESHEET" type="text/css" href="/ocp/css/dugmici.css">
<?php	
	$Action = utils_requestStr(getPVar("Akcija"));
	$foundObjects = array();

	if (utils_valid($Action) && ($Action == "Pretraga")){
		$content = utils_requestStr(getPVar("content"));
		$parameter = utils_requestStr(getPVar("parameter"));
		$matchCase = utils_requestStr(getPVar("matchCase"));
		$replacement = utils_requestStr(getPVar("replacement"));
		if (!utils_valid($replacement)) $replacement = "";
		$preview = utils_requestStr(getPVar("preview"));

//		$parameter = search_prepare4RegExp($parameter);

		$attributes = NULL;
		if ($content == "site"){
			$attributes = array();
			$postAttributes = getPVar("attribute");
			for ($i=0; $i< count($postAttributes); $i++)
				$attributes[$postAttributes[$i]] = 1;
		}

		if ($content == "site" || $content == "all"){
			$foundObjects = search_findByParameterSM($parameter, $matchCase, $attributes);
		}
		if ($content == "objects" || $content == "all"){
			$foundObjects = array_merge($foundObjects, search_findByParameterOM($parameter, $matchCase));
		}
		if ($preview != "1"){//odmah snimanje
			search_replaceAll($parameter, $matchCase, $replacement);
			$_SESSION["ocp_searchResults"] =  array();
		}

?> <script language="javascript">
	window.onload = function(){
		parent.document.getElementById("downFrameset").setAttribute("rows", "25%,*");
	}
	</script><?php

	} else if (utils_valid($Action) && ($Action == "Replace")){
		$content = utils_requestStr(getPVar("content"));
		$parameter = utils_requestStr(getPVar("parameter"));
		$matchCase = utils_requestStr(getPVar("matchCase"));
		$replacement = utils_requestStr(getPVar("replacement"));
		if (!utils_valid($replacement)) $replacement = "";
		
		$replaceArr = getPVar("replace");
		for ($i=0; $i<count($replaceArr); $i++){
			$next = $replaceArr[$i];
			$nextType = substr($next, 0, strrpos($next, "_"));
			$nextId = substr($next, strrpos($next, "_") + 1);
			
			search_replaceObject($nextType, $nextId, $parameter, $matchCase, $replacement);
		}
		$_SESSION["ocp_searchResults"] =  array();
?> <script language="javascript">
	parent.document.getElementById("downFrameset").setAttribute("rows", "100%,*");
	</script><?php			
	}
?> 
</HEAD>
<BODY class="ocp_body"><?php
	if (count($foundObjects) == 0){
		require_once("../../include/design/message.php");
		echo(message_info(ocpLabels("There is no data in database matching given text.")));
	} else {
		if ($preview == "1"){
			dumpPreview($foundObjects, $content, $parameter, $matchCase, $replacement, 1);
		} else {
			dumpPreview($foundObjects, $content, $parameter, $matchCase, $replacement, 0);
		}
	}
	
	?><?php
	
	function dumpPreview($foundObjects, $content, $parameter, $matchCase, $replacement, $preview){
		if ($preview){
			if (count($foundObjects) > 100){
				?> <script>alert("<?php echo ocpLabels("First 100 objects is displayed.")?>")</script><?php
			}
?> <form action="/ocp/controls/search_replace/replace.php?<?php echo utils_randomQS()?>" name="formObject" method="post" style="display:inline;">
	<input type="hidden" name="Akcija" value="Replace">
	<input type="hidden" name="content" value="<?php echo $content?>">
	<input type="hidden" name="parameter" value="<?php echo $parameter?>">
	<input type="hidden" name="matchCase" value="<?php echo $matchCase?>">
	<input type="hidden" name="replacement" value="<?php echo $replacement?>"><?php
		}	
?> <table class="ocp_opcije_table" border="0"> 
    <tr> 
      <td <?php if ($preview){ ?> colspan="2"<?php } ?>  class="ocp_opcije_td_naslov">
		<?php echo ocpLabels('Found items list')?> : <?php echo count($foundObjects)?>
	  </td> 
    </tr><?php	
		$limit = min(100, count($foundObjects));
		for ($i=0; $i<$limit; $i++){
			$next = $foundObjects[$i];
	?> <tr> 
<?php		if ($preview){?> 
      <td class="ocp_opcije_td">
		<input name="replace[]" type="checkbox" value="<?php echo $next["Type"]?>_<?php echo $next["Id"]?>" checked></span>
	  </td> <?php
		}	  
?> 	  <td align="left" class="ocp_opcije_td" <?php	if ($preview){ ?> style="width:95%"<?php } ?>>
		<span class="ocp_opcije_tekst1"><?php echo ocpLabels($next["Label"])?>  &gt; <?php echo $next['Title']?>  &gt; <?php echo $next['FoundIn']?><?php
		if (isset($next["Stra_Id"]) && utils_valid($next["Stra_Id"])){
			$pageLink = utils_getStraLink($next["Stra_Id"]);
			?><br/><a href="<?php echo $pageLink?>" class="ocp_link" target="_blank"><?php echo $pageLink?></a><?php
		}
?></span>
	  </td> 
   </tr><?php	
	}?> 
<?php		if ($preview){
	?> <tr>
      <td align="left" class="ocp_opcije_td">
     </td>
      <td align="left" class="ocp_opcije_td">
        <input type="submit" class="ocp_dugme_malo" value="<?php echo ocpLabels('Confirm')?>">&nbsp;<input type="button" name="submitCancel" class="ocp_dugme_malo" onClick="document.formObject.reset();" value="<?php echo ocpLabels("Cancel")?>">
      </td>
    </tr><?php
		}?> 
</table>
<?php		if ($preview){
?> </form><?php		
		}
	}	

?> </BODY>
</HTML>