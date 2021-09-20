<?php
	global $Id, $menu;

	if (utils_valid($Id) && !is_null(xml_documentElement($menu->xmlDoc))){
?>
<div class="block" id="map">
	<div>
		<ul>
<?php		DrawTree(xml_documentElement($menu->xmlDoc), "R", 1);	?>
		</ul>
	</div>
</div>
<?php

	}

/*
	DrawTree rekurzija za ispis drveta
*/
	function DrawTree($Node, $T, $CurrLevel){
		switch($T){
			case "R":	
?>	<li class="section_with_childs">
		<?php echo xml_getAttribute($Node, "naziv");?>
<?php
				DrawTree($Node, "A", $CurrLevel+1);
?></li><?php
				break;

			case "A":	
				$childNodes = xml_childNodes($Node);
				
				if (count($childNodes) > 0) 
					echo("<ul>");

				for ($i=0; $i<count($childNodes); $i++){
					$childNode = $childNodes[$i];
					$naziv = xml_getAttribute($childNode, "naziv");
					if (strlen($naziv) > 40) $naziv = utils_substr($naziv, 0, 37) . "...";
					$link = xml_getAttribute($childNode, "link");
					$id = xml_getAttribute($childNode, "id");
					
					if (!utils_valid($link)) continue;
					
					$branching = 0;
					if ($CurrLevel < 5){
						if (substr_count(xml_nodeName($childNode), "verzija") > 0){
							if ($id == menu_getVerzId()) $branching = 1;
						} else $branching = 1;
					}

					$className = "";
					if (substr_count(xml_nodeName($childNode), "verzija") > 0){ 
						if (count(xml_childNodes($childNode))>0 && $branching) $className = "section_with_childs";
						else $className="section";
					} else{
						if (substr_count(xml_nodeName($childNode), "sekcija") > 0){
							if (count(xml_childNodes($childNode))>0 && $branching) $className = "section_with_childs";
							else $className="page";
						} else $className = "page";
					}
?>
	<li class="<?php echo $className?>">
		<a href="<?php echo $link?>"><?php echo $naziv?></a>
		<?php if ($branching) DrawTree($childNode, "A", $CurrLevel+1);	?>
	</li>
<?php	
				}

				if (count($childNodes) > 0) 
					echo("</ul>");


				break;
			default: break;
		}
	}
?>