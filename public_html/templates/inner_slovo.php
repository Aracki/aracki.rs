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

	$lang = utils_valid(getGVar("l")) ? utils_requestStr(getGVar("l")) : "";
	$letter = utils_valid(getGVar("w")) ? utils_requestStr(getGVar("w")) : "";

	$tmpRecnik = new Recnik($lang,$letter);
	$wordList = $tmpRecnik->getWordList();
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
			<link rel="stylesheet" type="text/css" media="screen, projection" href="css/style.css" />
			<link rel="stylesheet" type="text/css" href="/css/print.css" media="print"/>
	<?php
		}
	?>

	<!-- Java Script -->
	<script src="js/jquery-1.7.2.min.js"></script>
	<script src="js/jquery.selectbox-0.2.min.js"></script>

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
</head>
<body>
<section class="inner">
	<header>
		<ul>
			<?php
			if ($lang == "se" ){				
					?><li><a href="<?php echo menu_getStraLink(menu_getVerzPocetna()); ?>">Naslovna</a></li><?php
				}else{
					
					?><li><a href="<?php echo menu_getStraLink(menu_getVerzLabel("engsrp_id")); ?>">Home</a></li><?php
				}
			?>
			<li><a href="http://aracki.rs/" target="_blank">Aracki studio</a></li>
		</ul>
		<h1>
		<p><?php 
		//var_dump($letter);
		$title_letter = $letter;
		if ($title_letter == 'đ') {$title_letter = 'Đ';}
		if ($title_letter == 'ž') {$title_letter = 'Ž';}
		if ($title_letter == 'č') {$title_letter = 'Č';}
		if ($title_letter == 'ć') {$title_letter = 'Ć';}
		if ($title_letter == 'š') {$title_letter = 'Š';}
		echo strtoupper($title_letter);

		?>
		</p></h1>
	</header>
	<div class="container">	
			<div class="words">
				<ul>
				<?php
					
					if (count($wordList) > 0){
						$words = array();
						$j = 0;
						for ($i = 0; $i < count($wordList); $i++)
						{
							$rec = $wordList[$i];
							if (0 == strncmp($rec->rec, $letter, strlen($letter))) {
								$words[$j]['rec']  = trim($rec->rec);
								$words[$j]['prevod']  = trim($rec->prevod);	
								$j++;
							}
						}										
						
						if (count($words) > 0) {
							$rows = count($words) > 2 ? ceil(count($words) / 3) : count($words);
							for ($k = 0; $k < count($words); $k++)
							{
								$rec = $words[$k];
								$breaker = ($k+1)%$rows;									
								?>							
								<li><strong><?php echo $rec["rec"];?></strong> - <?php echo $rec["prevod"];?></li>							
								<?php
								if ($k+1 > 0 && $breaker == 0 && $k < count($words) -1 ){
									echo "</ul><ul>";
								}														
							}
						}
					}
				?>
				</ul>
				
			</div><!-- /words -->
			<div class="clear"></div>
	</div><!-- /container -->
</section>
<footer class="inner">
	<p>dictionary/advertising-print-design</p>
</footer>
</body>
</html>