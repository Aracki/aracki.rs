<?php
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
	
	header("Content-type: text/html; charset=utf-8");

	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/connect.php");

	if (!isset($JaSamSigurnoOnLine))
	{
		require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/session.php");
	}

	require_once($_SERVER["DOCUMENT_ROOT"]."/ocp/include/utils.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/code/keyboard.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/code/menu.php");

	require_once($_SERVER["DOCUMENT_ROOT"]."/php/classes.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

	<?php
		require_once($_SERVER["DOCUMENT_ROOT"]."/code/header.php");
		require_once($_SERVER["DOCUMENT_ROOT"]."/code/keywords.php");
		require_once($_SERVER["DOCUMENT_ROOT"]."/code/title.php");

		global $menu;
		global $Id;
		/* ekstra parametri stranice */
		if ($Id == "undefined" || $Id == NULL || $Id == 0) $Id = 1;
		$srpskeReci = extra_get("stra", $Id, "srpskeReci", 1);
		
		/* link za stranicu sa rezultatima tj listama po prvom slovu */
		$redirectUrl = menu_getStraLink(menu_getVerzLabel("pretraga_id"));

		/* link za prelaz na drugi jezik */
		$lang = $srpskeReci == 1 ? "se" : "es";
		$homePageOtherLanguage = $srpskeReci == 1 ? menu_getVerzLabel("engsrp_id") : menu_getVerzPocetna();
		$redirectUrlOtherLanguage = utils_valid($homePageOtherLanguage) && is_numeric($homePageOtherLanguage) ? utils_getStraLink($homePageOtherLanguage) : utils_getStraLink(menu_getVerzPocetna());

		/* prevodjenje */
		$akcija = utils_requestStr(getPVar("akcija"));
		$rec = "";
		$prevod = "";		
		if (utils_valid($akcija) && $akcija == "search")
		{
			$rec = utils_valid(getPVar("rec")) ? utils_requestStr(getPVar("rec")) : "";
			$lang = utils_valid(getPVar("lang")) ? utils_requestStr(getPVar("lang")) : "se";

			$tmpRecnik = new Recnik($lang,"");
			$prevod = $tmpRecnik->getPrevod($rec);
		}

		/* akcija na strani */
		$action = utils_requestStr(getGVar("action"));
		if (utils_valid($action) && $action == "print")
		{
		?>
				<link rel="stylesheet" type="text/css" href="/css/print.css" media="screen"/>
				<link rel="stylesheet" type="text/css" href="/css/print.css" media="print"/>
		<?php
			}
			else
			{
		?>
		<!-- CSS -->
				<link rel="stylesheet" type="text/css" media="screen, projection" href="/css/style.css" />
				<link rel="stylesheet" type="text/css" href="/css/print.css" media="print"/>
		<?php
			}
		?>

	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
	<link rel="icon" href="/favicon.ico" type="image/ico"/>

	<!--<script type="text/javascript" src="/jscript/menu.js"></script>-->
	<script type="text/javascript" src="/jscript/swfobject.js"></script>
	<script type="text/javascript" src="/jscript/tools.js"></script>

	<!--[if lte IE 6]>
	<link rel="stylesheet" type="text/css" href="css/ie.css" media="screen" />
	<![endif]-->
	
	<!--[if IE 7]>
	<link rel="stylesheet" type="text/css" href="css/ie7.css" media="screen" />
	<![endif]-->
	
	<!--[if IE 8]>
	<link rel="stylesheet" type="text/css" href="css/ie8.css" media="screen" />
	<![endif]-->
	
	<!--[if IE 9]>
	<link rel="stylesheet" type="text/css" href="css/ie9.css" media="screen" />
	<![endif]-->
	
	<!--[if lt IE 9]>
	<script src="js/html5.js"></script>
	<![endif]-->

	<!-- Java Script -->
	<link rel="stylesheet" type="text/css" href="/js/jquery.autocomplete.css" />
	<script type="text/javascript" src="/js/jquery.js"></script>
	<script type="text/javascript" src="/js/jquery.autocomplete.js"></script>
	

	<script type="text/javascript">
		 $(document).ready(function(){
			 <?php if ($srpskeReci == 1) { ?>
				$("#rec").autocomplete("/templates/ajax_autocomplete.php", {
							selectFirst: true,
							minChars:1,
							minLength: 1,
							matchSubset:1,
							cacheLength:10,
							onItemSelect:selectItem,
							formatItem:formatItem
				 });
			<?php }else{ ?>
				$("#rec").autocomplete("/templates/ajax_autocomplete_en.php", {
							selectFirst: true,
							minChars:1,
							minLength: 1,
							matchSubset:1,
							cacheLength:10,
							onItemSelect:selectItem,
							formatItem:formatItem
				 });
			<?php } ?>
		 });

	function lookup(){
		var oSuggest = $("#rec")[0].autocompleter;
		oSuggest.findValue();
		return false;
	}

	function findValue(li) {
		if( li == null ) return alert("No match!");
		if( !!li.extra ) var sValue = li.extra[0];
		else var sValue = li.selectValue;

		$.post("/templates/ajax_getPrevod.php", { rec: sValue, lang: '<?php echo $lang;?>' },   function(result) {    
			if (result.length > 35){
				$("#prevod").css('height','40px');
				$("#rec").css('height','40px');
			} else {
				$("#prevod").css('height','19px');
				$("#rec").css('height','19px');
			};
			$("#prevod").val(result);
			
			
			//$("#prevod").css('height','39px');
			//$("#prevod").style.height = $("#prevod").scrollHeight+'px';
			//$("#rec").style.height = $("#prevod").style.height;
		
		});		
	}

	function selectItem(li) {
		findValue(li);
	}

	function formatItem(row) {
		//return row[0] + " (id: " + row[1] + ")";
		return row[0];
	}
</script>
	
</head>
<body>
<section class="home">
	<header>
		
		<ul>
		<?php if ($srpskeReci == 1 ){?>
			<li><a href="<?php echo menu_getStraLink(menu_getVerzPocetna()); ?>">Naslovna</a></li>
		<?php }else{ ?>
			<li><a href="<?php echo menu_getStraLink(menu_getVerzLabel("engsrp_id")); ?>">Home</a></li>
		<?php } ?>
			
		<li><a href="http://aracki.rs/" target="_blank">Aracki studio</a></li>
		</ul>
		<div id="flash_container">
			<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="238" height="274" id="movie_name" align="middle">
				<param name="movie" value="../gfx/logo-anim.swf"/>
				<param name="wmode" value="transparent"/>
			<!--[if !IE]>-->
			<object type="application/x-shockwave-flash" data="../gfx/logo-anim.swf" width="238" height="274">
				<param name="movie" value="../gfx/logo-anim.swf"/>
				<param name="wmode" value="transparent"/>
				<!--<![endif]-->
				<a href="http://www.adobe.com/go/getflash">
				<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player"/>
				</a>
				<!--[if !IE]>-->
			</object>
		<!--<![endif]-->
		</object>
		</div>
		<!--h1><a href="<?php echo $redirectUrlOtherLanguage; ?>">Rečnik propagande, štampe, dizajna</a></h1-->
	</header>
	<div class="headerUndrline"></div>
	<div class="container">
		<div class="transBox">
		
		<?php if ($srpskeReci == 1) { ?>
			<form name="formPretraga" id="formPretraga" method="post" onSubmit="return validate();" action="#">
				<input type="hidden" name="lang" value="se"/>
				<input type="hidden" name="akcija" value="search"/>
				<div id="ie_fix1" class="entry srb">
					<h2><img src="/gfx/flagSR.png" alt="Srpski" /> <img src="/gfx/srpski.png" alt="Srpski" /></h2>
					<textarea rows="1" type="text" name="rec" id="rec" value="<?php echo $rec;?>" autocomplete="off" onfocus="this.select()" onchange="document.getElementById('prevod').value = '';" onclick="lookup();"><?php echo $rec;?></textarea>
				</div><!-- /entry -->
				<div class="entry mid">
					<a href="<?php echo menu_getStraLink(menu_getVerzLabel("engsrp_id")); ?>"></a>									
				</div>
				<div id="ie_fix2" class="entry eng">
					<h2><img src="/gfx/flagEN.png" alt="Engleski" /> <img src="/gfx/engleski.png" alt="Engleski" /></h2>
					<textarea rows="1" type="text" id="prevod" name="prevod" onfocus="this.select()" readonly ><?php echo $prevod;?></textarea>
				</div><!-- /entry -->
				<div class="clear"></div>
				<p class="translate"><input type="submit" class="submit" name="" value="" /></p>
			</form>
		<?php }else{ ?>
			<form name="formPretraga" id="formPretraga" method="post" onSubmit="return validate();" action="#">
				<input type="hidden" name="lang" value="es"/>
				<input type="hidden" name="akcija" value="search"/>
				<div id="ie_fix1" class="entry eng">
					<h2><img src="/gfx/flagEN.png" alt="Engleski" /> <img src="/gfx/engleski.png" alt="Engleski" /></h2>
					<textarea rows="1" type="text" name="rec" id="rec" value="<?php echo $rec;?>" autocomplete="off" onfocus="this.select()" onchange="document.getElementById('prevod').value = '';" onclick="lookup();"><?php echo $rec;?></textarea>
				</div><!-- /entry -->
				<div class="entry mid">
					<a href="<?php echo menu_getStraLink(menu_getVerzPocetna()); ?>"><!--img src="/gfx/strelice.png"/--></a>					
				</div>
				<div id="ie_fix2" class="entry srb">
					<h2><img src="/gfx/flagSR.png" alt="Srpski" /> <img src="/gfx/srpski.png" alt="Srpski" /></h2>
					<textarea rows="1" type="text" id="prevod" name="prevod" onfocus="this.select()" readonly onchange="resize();" ><?php echo $prevod;?></textarea>
				</div><!-- /entry -->		
					<div class="clear"></div>
				<p class="translate"><input type="submit" class="submit" name="" value="" /></p>
			</form>
		<?php } ?>			
			<div class="clear"></div>
		<?php if ($srpskeReci == 1) { ?>
				<div class="transBar se">			
				<p>srpsko-engleski</p>
				<div class="abeceda">
					<a href="<?php echo $redirectUrl ?>?l=se&w=a">a</a> 
					<a href="<?php echo $redirectUrl ?>?l=se&w=b">b</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=v">v</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=g">g</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=d">d</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=đ">đ</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=e">e</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=ž">ž</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=z">z</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=i">i</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=j">j</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=k">k</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=l">l</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=lj">lj</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=m">m</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=n">n</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=nj">nj</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=o">o</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=p">p</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=r">r</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=s">s</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=t">t</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=ć">ć</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=u">u</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=f">f</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=h">h</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=c">c</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=č">č</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=dž">dž</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=š">š</a> 
				</div>
			</div><!-- /transBar -->			
			<div class="transBar es">
				<p>englesko-srpski</p>
				<div class="alphabet">
					<a href="<?php echo $redirectUrl ?>?l=es&w=a">a</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=b">b</a>
					<a href="<?php echo $redirectUrl ?>?l=es&w=c">c</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=d">d</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=e">e</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=f">f</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=g">g</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=h">h</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=i">i</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=j">j</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=k">k</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=l">l</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=m">m</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=n">n</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=o">o</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=p">p</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=q">q</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=r">r</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=s">s</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=t">t</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=u">u</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=v">v</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=w">w</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=x">x</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=y">y</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=z">z</a>
				</div>
			</div><!-- /transBar -->
		<?php }else{ ?>
				<div class="transBar es">
				<p>englesko-srpski</p>
				<div class="alphabet">
					<a href="<?php echo $redirectUrl ?>?l=es&w=a">a</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=b">b</a>
					<a href="<?php echo $redirectUrl ?>?l=es&w=c">c</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=d">d</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=e">e</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=f">f</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=g">g</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=h">h</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=i">i</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=j">j</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=k">k</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=l">l</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=m">m</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=n">n</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=o">o</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=p">p</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=q">q</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=r">r</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=s">s</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=t">t</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=u">u</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=v">v</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=w">w</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=x">x</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=y">y</a> 
					<a href="<?php echo $redirectUrl ?>?l=es&w=z">z</a>
				</div>
			</div><!-- /transBar -->
			<div class="transBar se">			
				<p>srpsko-engleski</p>
				<div class="abeceda">
					<a href="<?php echo $redirectUrl ?>?l=se&w=a">a</a> 
					<a href="<?php echo $redirectUrl ?>?l=se&w=b">b</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=v">v</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=g">g</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=d">d</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=đ">đ</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=e">e</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=ž">ž</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=z">z</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=i">i</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=j">j</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=k">k</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=l">l</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=lj">lj</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=m">m</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=n">n</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=nj">nj</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=o">o</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=p">p</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=r">r</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=s">s</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=t">t</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=ć">ć</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=u">u</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=f">f</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=h">h</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=c">c</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=č">č</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=dž">dž</a>  
					<a href="<?php echo $redirectUrl ?>?l=se&w=š">š</a> 
				</div>
			</div><!-- /transBar -->
		<?php } ?>
		</div><!-- /transBox -->
	</div><!-- /container -->
</section>
<footer>
	<p>dictionary/advertising-print-design</p>
</footer>
</body>
</html>