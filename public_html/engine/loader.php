<?
$page=$_GET['page'];

for ($i=0; $i<=count($link)-1; $i++)
	{
		
		if ($page==$link[$i][0])
			{
				$pageID=$i;
			}
	}

if(!isset($pageID))
	{
		$pageID=0;
		
	}
	$id=$_GET['id'];
	if($id==NULL)
		{
			$id='1';	
		}
	echo $id;
	
	
	
	
$page=$link[$pageID][0];
$maine=$main[$pageID][0];
$titles=$title[$pageID][0];

/*checking variable */


if ($titles==NULL)
	{
		$titles= 'Woodstock CMS Empty content';
	}

?>