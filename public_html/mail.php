<link href="/stil.css" rel="stylesheet" type="text/css" />
<div id="poslato">
<?

include 'engine/function.php';

if($_POST['_submit_check']==1)
{

			$ime=variable($_POST['ime']);
			$telefon=variable($_POST['telefon']);
			$mail=variable($_POST['mail']);
			$poruka=variable($_POST['poruka']);
			
	if (($ime==NULL) or ($mail==NULL) or ($poruka==NULL))
				{
					$napomena='<p style="color:red;">Niste dobro popunili polja!</p>';
			 			
				}else {
					
					
					contact($ime,$telefon,$mail,$poruka);
					 $napomena='<p>Vasa poruka je poslata , uskoro cete biti redirektovani na pocetnu stranu</p>
			<meta http-equiv=Refresh content=3;url=../>';
					
					}
					
					 
														
														
					
					
					
}			

echo $napomena;



?>
</div>

