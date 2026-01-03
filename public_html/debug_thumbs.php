<?php
// Diagnostic script to check thumbnail path resolution
// Upload this to your server and access it via browser to see diagnostic info

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Thumbnail Path Diagnostic</h2>";
echo "<pre>";

// Check DOCUMENT_ROOT
echo "DOCUMENT_ROOT: " . (isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : 'NOT SET') . "\n";
echo "SCRIPT_FILENAME: " . (isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : 'NOT SET') . "\n";
echo "Current working directory: " . getcwd() . "\n\n";

// Test path resolution methods
$root = 'prospekti'; // Test with prospekti folder

echo "Testing folder: $root\n\n";

// Method 1: DOCUMENT_ROOT
if (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] != '') {
    $path1 = $_SERVER['DOCUMENT_ROOT'].'/foto/'.$root.'/thumb/';
    echo "Method 1 (DOCUMENT_ROOT): $path1\n";
    echo "  Directory exists: " . (is_dir($path1) ? 'YES' : 'NO') . "\n";
    echo "  Is readable: " . (is_readable($path1) ? 'YES' : 'NO') . "\n";
    if (is_dir($path1)) {
        $files = scandir($path1);
        $imageFiles = array_filter($files, function($f) { return $f != '.' && $f != '..' && !is_dir($path1.$f); });
        echo "  Files found: " . count($imageFiles) . "\n";
        if (count($imageFiles) > 0) {
            echo "  Sample files: " . implode(', ', array_slice($imageFiles, 0, 3)) . "\n";
        }
    }
    echo "\n";
}

// Method 2: Relative to this script
$path2 = dirname(__FILE__).'/foto/'.$root.'/thumb/';
echo "Method 2 (dirname(__FILE__)): $path2\n";
echo "  Directory exists: " . (is_dir($path2) ? 'YES' : 'NO') . "\n";
echo "  Is readable: " . (is_readable($path2) ? 'YES' : 'NO') . "\n";
if (is_dir($path2)) {
    $files = scandir($path2);
    $imageFiles = array_filter($files, function($f) use ($path2) { return $f != '.' && $f != '..' && !is_dir($path2.$f); });
    echo "  Files found: " . count($imageFiles) . "\n";
    if (count($imageFiles) > 0) {
        echo "  Sample files: " . implode(', ', array_slice($imageFiles, 0, 3)) . "\n";
    }
}
echo "\n";

// Method 3: Realpath
$path3 = realpath(dirname(__FILE__).'/foto/'.$root.'/thumb/');
echo "Method 3 (realpath): " . ($path3 ? $path3 : 'FAILED') . "\n";
if ($path3) {
    echo "  Directory exists: YES\n";
    echo "  Is readable: " . (is_readable($path3) ? 'YES' : 'NO') . "\n";
    $files = scandir($path3);
    $imageFiles = array_filter($files, function($f) use ($path3) { return $f != '.' && $f != '..' && !is_dir($path3.$f); });
    echo "  Files found: " . count($imageFiles) . "\n";
}
echo "\n";

// Check if foto directory exists at all
$fotoBase = dirname(__FILE__).'/foto/';
echo "Base foto directory: $fotoBase\n";
echo "  Exists: " . (is_dir($fotoBase) ? 'YES' : 'NO') . "\n";
if (is_dir($fotoBase)) {
    $folders = scandir($fotoBase);
    $folders = array_filter($folders, function($f) { return $f != '.' && $f != '..' && is_dir($fotoBase.$f); });
    echo "  Subfolders found: " . implode(', ', $folders) . "\n";
}

echo "\n";
echo "Testing view_image function:\n";
include 'engine/function.php';
echo "Calling view_image(70, 70, 'prospekti'):\n";
view_image(70, 70, 'prospekti');

echo "</pre>";
?>

