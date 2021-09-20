<?
error_reporting(0);
/*Navigation function*/

function link_create($main,$link,$title)
	{
		echo '<a href="'.$link.'.html" title="'.$title.'">'.$main.'</a>';
	}




function nav($main,$link,$title)
	{
		$navi=data('SELECT * FROM nav');
		foreach ($navi as $index=>$nav)
		{
			$main_nav=$nav['m_nav'];
			$main_nav_link=$nav['main_nav_link'];
			$main_nav_title=$nav['main_nav_title'];
			
			
				echo '<a href="'.$main_nav_link.'" title="'.$main_nav_title.'">'.$main_nav.'</a><br>';
				echo $page;
				
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
		$illegal_signs=array(' ' , '/', '-', "");
		
		 if ($name1=strpos($entry, $illegal_signs))
		 
		 	{
				echo "Ilegal sign"; 
			 }
		
		$name1=strtolower($entry);
		return $name1;
		
	}

/* Haegory variable check*/
function check($kat)

	{
		
		$res=mysql_query('SELEC*FROM kategoraja WHERE kat="'.$kat.'"');
		if($res!==$kat) {$id=0;}
		echo $kat;
	}
	
	

	
	
?>