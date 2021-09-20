<?php
$root = realpath(dirname(__FILE__) . "/../../../../");
$url = "http://chart.apis.google.com/chart?" . $_SERVER["QUERY_STRING"];
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
$result = curl_exec ($ch);
curl_close ($ch);
$filename = "/upload/images/charts/chart_" . time() . ".png";
if (file_put_contents($root . $filename, $result)){
    echo $filename;
}
?>
