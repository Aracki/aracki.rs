<?php
// Simple direct test - no tabs, just images
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . '/engine/function.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple Thumbnail Test</title>
    <style>
        body { background: #000; color: #fff; padding: 20px; }
        .images { background: #333; padding: 20px; }
        .images img { border: 2px solid #fff; margin: 5px; }
    </style>
</head>
<body>
    <h1>Direct Image Test - prospekti</h1>
    <p>If you see images below, the function is working:</p>
    <div class="images">
        <?php view_image(70, 70, 'prospekti'); ?>
    </div>
    
    <h2>Check Browser Console</h2>
    <p>Open Developer Tools (F12) and check:</p>
    <ul>
        <li>Console tab - any JavaScript errors?</li>
        <li>Network tab - are image requests being made? What status codes?</li>
        <li>Elements tab - can you see the &lt;img&gt; tags in the HTML?</li>
    </ul>
    
    <h2>View Source</h2>
    <p>Right-click and "View Page Source" - search for "foto/prospekti" - do you see the image tags?</p>
</body>
</html>

