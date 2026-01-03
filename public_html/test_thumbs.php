<?php
// Simple test script to check if thumbnails are being generated
// Access via: http://46.224.186.132/test_thumbs.php?folder=prospekti

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'engine/function.php';

$folder = isset($_GET['folder']) ? $_GET['folder'] : 'prospekti';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Thumbnail Test</title>
    <style>
        body { background: #000; color: #fff; padding: 20px; }
        .test-section { margin: 20px 0; padding: 10px; border: 1px solid #fff; }
        .debug { background: #333; padding: 10px; margin: 10px 0; font-family: monospace; }
    </style>
</head>
<body>
    <h1>Thumbnail Test for: <?php echo htmlspecialchars($folder); ?></h1>
    
    <div class="test-section">
        <h2>Server Info</h2>
        <div class="debug">
            DOCUMENT_ROOT: <?php echo isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : 'NOT SET'; ?><br>
            SCRIPT_FILENAME: <?php echo isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : 'NOT SET'; ?><br>
            Current working directory: <?php echo getcwd(); ?><br>
            __FILE__: <?php echo __FILE__; ?><br>
            dirname(dirname(__FILE__)): <?php echo dirname(dirname(__FILE__)); ?><br>
        </div>
    </div>
    
    <div class="test-section">
        <h2>Path Tests</h2>
        <div class="debug">
            <?php
            $testPaths = array(
                'DOCUMENT_ROOT' => (isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '') . '/foto/' . $folder . '/thumb/',
                'dirname' => dirname(dirname(__FILE__)) . '/foto/' . $folder . '/thumb/',
                'getcwd' => getcwd() . '/foto/' . $folder . '/thumb/',
            );
            
            foreach ($testPaths as $method => $path) {
                $path = str_replace('\\', '/', $path);
                $exists = is_dir($path);
                $readable = $exists ? is_readable($path) : false;
                echo "<strong>$method:</strong> $path<br>";
                echo "&nbsp;&nbsp;Exists: " . ($exists ? 'YES' : 'NO') . "<br>";
                echo "&nbsp;&nbsp;Readable: " . ($readable ? 'YES' : 'NO') . "<br>";
                if ($exists && $readable) {
                    $files = scandir($path);
                    $imageFiles = array_filter($files, function($f) use ($path) { 
                        return $f != '.' && $f != '..' && !is_dir($path . $f); 
                    });
                    echo "&nbsp;&nbsp;Files found: " . count($imageFiles) . "<br>";
                    if (count($imageFiles) > 0) {
                        echo "&nbsp;&nbsp;Sample: " . implode(', ', array_slice($imageFiles, 0, 3)) . "<br>";
                    }
                }
                echo "<br>";
            }
            ?>
        </div>
    </div>
    
    <div class="test-section">
        <h2>Function Output</h2>
        <div style="background: #fff; color: #000; padding: 20px;">
            <?php view_image(70, 70, $folder); ?>
        </div>
    </div>
    
    <div class="test-section">
        <h2>Raw HTML Output</h2>
        <div class="debug">
            <?php
            ob_start();
            view_image(70, 70, $folder);
            $output = ob_get_clean();
            echo htmlspecialchars($output);
            ?>
        </div>
    </div>
    
    <div class="test-section">
        <h2>Test Other Folders</h2>
        <p>
            <a href="?folder=prospekti">prospekti</a> | 
            <a href="?folder=logotipi">logotipi</a> | 
            <a href="?folder=kalendari">kalendari</a> | 
            <a href="?folder=bilbordi">bilbordi</a>
        </p>
    </div>
</body>
</html>

