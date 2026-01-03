<?php
/*Database conection parameters*/

$admin='milorad.markovic87@hotmail.com';
$host='localhost';
$username='root';
$password='';
$db='admin';

mysql_connect($host,$username,$password);
mysql_select_db($db);

if (!@mysql_connect)
	{
	echo 'MySQL conection is unavailable at this moment. Please contact administrator '.$admin.'';
	}
?>