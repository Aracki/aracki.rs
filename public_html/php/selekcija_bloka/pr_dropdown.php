<?php 
	require_once("../ocp/include/connect.php");	
	require_once("../ocp/include/utils.php");	
	require_once("../ocp/siteManager/lib/stranica.php");	
	require_once("../code/menu_functions.php");

	$params = $_SESSION["urlParams"];
	unset($_SESSION["urlParams"]);

	$StraId = utils_requestInt(getGVar("Id"));
	if (is_null($StraId) || ($StraId == 0)) $StraId = utils_requestInt(getPVar("Stra_Id"));
	if (!is_null($StraId)) $StraId = intval($StraId);

	$blokovi = stranica_getAllBlok($StraId);
	
	if (count($blokovi) > 0){ ?>
<div class="block select_block">
	<h4><?php echo menu_getVerzLabel("select_block_title")?></h4>
	<ul>
<?php		
		for ($i=0; $i < count($blokovi); $i++){
			$blok = $blokovi[$i];

			$xmlDoc=xml_loadXML($blok["Blok_XmlPodaci"]);

			$naslov = xml_getFirstElementByTagName($xmlDoc, "naslov");
			if (utils_valid($naslov)){
				$naslov = xml_getContent($naslov);
				if (utils_valid($naslov)){
					$naslov = utils_substr($naslov, 0, 60) . "...";
?>			<li><a href="#<?php echo $blok["StBl_Id"]?>"><?php echo rawurldecode($naslov); ?></a></li> <?php
				}
			}
		}
?>
	</ul>
</div>
<?php
	}

?>
