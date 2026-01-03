<?php

require_once("../../include/connect.php");
require_once("../../include/session.php");
require_once("settings.php");
$json = new Services_JSON();
$jsonString = $_REQUEST["json"];
$jsonString = preg_replace("/(,|{|\[)/", "\\1\n", $jsonString);
$jsonString = preg_replace("/(}|\])/", "\n\\1", $jsonString);
$lines = preg_split("/\n/", $jsonString);
$formattedString = "";
$indent = 0;
foreach ($lines as $line) {
    $prepend = "";
    for ($i = 0; $i < $indent; $i++) {
        $prepend .= "\t";
    }
    if (preg_match("/({|\[)$/", $line)) {
        $indent++;
    }
    if (preg_match("/^(}|\])/", $line)) {
        $indent--;
        $prepend = preg_replace("/\t$/", "", $prepend);
    }
    $line = $prepend . $line;
    $formattedString .= $line . "\n";
}
if (file_put_contents(thumbnailer::getSettingsFilePath(), $formattedString)){
    echo "true";
} else {
    echo "false";
}