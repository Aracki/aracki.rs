<?
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
	
	$zabranjeniznaci=array(',','.','\'','\\','/',' ','š','ð','è','æ','ž','(',')','&');
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
			$dir = opendir('foto/'.$root.'/thumb/');
			while(($file = readdir($dir)) !== false)
			{
					 if($file !== '.' && $file !== '..' && !is_dir($file))
					 {
					   $files[] = $file;
					 }
			}
				closedir($dir);
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