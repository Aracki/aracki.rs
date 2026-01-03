<?php
error_reporting(0);
/*Navigation function*/

function link_create($main,$link,$title)
	{
		echo '<a href="'.$link.'.html" title="'.$title.'">'.$main.'</a>';
	}




function nav($main,$link,$title)
	{
		for($i=0; $i<count($main); $i++)
			{
				echo '<a href="/'.$link[$i][0].'" title="'.$title[$i][0].'">'.$main[$i][0].'</a><br>';
			}
	}


/*Load content function */
function load() 
	{
		global $page;
		include 'pages/'.$page.'.php';			
	}


/*From database to array*/

function data($kveri) 
	
	{
	
	
	$citanje = mysql_query($kveri) or die(mysql_error());
	
	$totalRows_citanje = mysql_num_rows($citanje);
	
	while ( $row_citanje = mysql_fetch_array($citanje) ) {
	
	
	$niz[]=$row_citanje;
	
	};
	
	if ( is_array($niz)) {	
	return $niz;
	}
	else {
	
	return false;
	
	};
		
	};
	
/*Testing data entry*/

function data_entry($entry)

	{
	
	$zabranjeniznaci=array(',','.','\'','\\','/',' ','�','�','�','�','�','(',')','&');
	$noviznaci=array('','','','-','-','-','s','dj','c','c','z','','','');
	
	$naziv=strtolower($entry);
	$naziv=str_replace($zabranjeniznaci,$noviznaci,$naziv);
	return $naziv;
	};

/* variable check*/

function variable($var)
	
	{		
			
			if ($var==NULL)
				{
					echo '<p>Niste dobro popunili polja</p>';
					exit;
				
				}else
				{
					return $var;
					
				}
	}



/*function check($kat)

	{
		
		$res=mysql_query('SELEC*FROM kategoraja WHERE kat="'.$kat.'"');
		if($res!==$kat) {$id=0;}
		echo $kat;
		
		
	}
	
	*/

/*Pregled slika galerija*/
function view_image($width,$height,$root)

	{
		
		
			
					$files = array();
			// Try multiple path resolution methods for compatibility across different servers
			$thumbDir = null;
			
			// Method 1: DOCUMENT_ROOT (most common)
			if (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] != '') {
				$testPath = $_SERVER['DOCUMENT_ROOT'].'/foto/'.$root.'/thumb/';
				$testPath = str_replace('\\', '/', $testPath);
				if (is_dir($testPath)) {
					$thumbDir = $testPath;
				}
			}
			
			// Method 2: Relative to this script (engine/function.php -> public_html/foto/...)
			if (!$thumbDir) {
				$testPath = dirname(dirname(__FILE__)).'/foto/'.$root.'/thumb/';
				$testPath = str_replace('\\', '/', $testPath);
				if (is_dir($testPath)) {
					$thumbDir = $testPath;
				}
			}
			
			// Method 3: Try realpath for absolute resolution
			if (!$thumbDir) {
				$testPath = realpath(dirname(dirname(__FILE__)).'/foto/'.$root.'/thumb/');
				if ($testPath && is_dir($testPath)) {
					$thumbDir = $testPath;
				}
			}
			
			// Method 4: Try relative path from current working directory (fallback)
			if (!$thumbDir) {
				$testPath = getcwd().'/foto/'.$root.'/thumb/';
				$testPath = str_replace('\\', '/', $testPath);
				if (is_dir($testPath)) {
					$thumbDir = $testPath;
				}
			}
			
			// Open directory and read files
			if ($thumbDir && is_dir($thumbDir)) {
				$dir = opendir($thumbDir);
				if ($dir !== false) {
					while(($file = readdir($dir)) !== false)
					{
						 if($file !== '.' && $file !== '..' && !is_dir($thumbDir.$file))
						 {
						   $files[] = $file;
						 }
					}
					closedir($dir);
				}
			} else {
				// Debug: Output error if directory not found (remove in production)
				if (isset($_GET['debug'])) {
					echo '<!-- DEBUG: thumbDir not found. Tried: ';
					if (isset($_SERVER['DOCUMENT_ROOT'])) echo 'DOCUMENT_ROOT='.$_SERVER['DOCUMENT_ROOT'].'; ';
					echo 'dirname='.dirname(dirname(__FILE__)).'; ';
					echo 'getcwd='.getcwd().'; ';
					echo 'root='.$root.' -->';
				}
			}
				sort($files);
				
				
								
				
				//
				
					for($i=0; $i<(count($files)); $i++)
					{
						
						
					
						echo '
						 						 
					
						
						
						<a href="/foto/'.$root.'/big/'.$files[$i].'" class="lightbox" rel="'.$root.'"><img src="/foto/'.$root.'/thumb/'.$files[$i].'" alt="" width="'.$width.'" height="'.$height.'"></a>
						
						
						
						
						';
									
					
					
					
					
					
					}	
		echo '<div class="clear"></div>';
				
		}
	
	
function contact($ime,$telefon,$mail,$poruka)
		{
			
			
			
			$to = "varacki@sbb.rs";
			$subject = "Kontakt sa sajta aracki.rs";
			$message = 'OD:'.$mail.' Poruka'.$poruka;
			$from = $mail;
			$headers = "From: $ime";
			mail($to,$subject,$message,$headers);
		
			
				
			
		}
	
	
?>