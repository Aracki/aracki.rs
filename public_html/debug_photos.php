<?php
/**
 * Photo Loading Diagnostic Script
 * Access via: http://46.224.186.132/debug_photos.php
 *
 * This script will help identify why photos are not loading.
 * DELETE THIS FILE after debugging is complete for security.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test folders that should contain photos
$photoFolders = array('logotipi', 'prospekti', 'kalendari', 'bilbordi', '3d', 'knjige', 'ilustracije', 'beograd', 'vojvodina', 'biljke', 'ljudi', 'priroda');

?>
<!DOCTYPE html>
<html>
<head>
    <title>Photo Loading Diagnostics - Aracki.rs</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        h1 { color: #333; }
        .section { background: #fff; padding: 15px; margin: 15px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .ok { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; font-weight: bold; }
        .mono { font-family: monospace; background: #eee; padding: 2px 5px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #333; color: #fff; }
        tr:nth-child(even) { background: #f9f9f9; }
    </style>
</head>
<body>
    <h1>🔍 Photo Loading Diagnostics</h1>

    <div class="section">
        <h2>1. Server Environment</h2>
        <table>
            <tr>
                <th>Variable</th>
                <th>Value</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>DOCUMENT_ROOT</td>
                <td class="mono"><?php echo isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : 'NOT SET'; ?></td>
                <td><?php
                    if (isset($_SERVER['DOCUMENT_ROOT']) && is_dir($_SERVER['DOCUMENT_ROOT'])) {
                        echo '<span class="ok">✓ EXISTS</span>';
                    } else {
                        echo '<span class="error">✗ NOT FOUND</span>';
                    }
                ?></td>
            </tr>
            <tr>
                <td>Current Working Directory</td>
                <td class="mono"><?php echo getcwd(); ?></td>
                <td><span class="ok">✓</span></td>
            </tr>
            <tr>
                <td>Script Location (__FILE__)</td>
                <td class="mono"><?php echo __FILE__; ?></td>
                <td><span class="ok">✓</span></td>
            </tr>
            <tr>
                <td>PHP Version</td>
                <td class="mono"><?php echo phpversion(); ?></td>
                <td><span class="ok">✓</span></td>
            </tr>
            <tr>
                <td>Web Server</td>
                <td class="mono"><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></td>
                <td><span class="ok">✓</span></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>2. Main "foto" Directory</h2>
        <?php
        $fotoPath = null;
        $testPaths = array(
            'DOCUMENT_ROOT' => (isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '') . '/foto/',
            'dirname' => dirname(__FILE__) . '/foto/',
            'getcwd' => getcwd() . '/foto/',
        );

        echo '<table>';
        echo '<tr><th>Method</th><th>Path</th><th>Exists?</th><th>Readable?</th></tr>';

        foreach ($testPaths as $method => $path) {
            $path = str_replace('\\', '/', $path);
            $exists = is_dir($path);
            $readable = $exists ? is_readable($path) : false;

            if ($exists && $readable && !$fotoPath) {
                $fotoPath = $path;
            }

            echo '<tr>';
            echo '<td>' . $method . '</td>';
            echo '<td class="mono">' . $path . '</td>';
            echo '<td>' . ($exists ? '<span class="ok">✓ YES</span>' : '<span class="error">✗ NO</span>') . '</td>';
            echo '<td>' . ($readable ? '<span class="ok">✓ YES</span>' : '<span class="error">✗ NO</span>') . '</td>';
            echo '</tr>';
        }
        echo '</table>';

        if (!$fotoPath) {
            echo '<p class="error">❌ CRITICAL: The "foto" directory was NOT found! This is the main problem.</p>';
            echo '<p>Possible solutions:</p>';
            echo '<ul>';
            echo '<li>Ensure the "foto" folder was uploaded to the server</li>';
            echo '<li>Check that folder permissions allow the web server to read it (chmod 755)</li>';
            echo '<li>Verify the folder is in the correct location relative to index.php</li>';
            echo '</ul>';
        } else {
            echo '<p class="ok">✓ "foto" directory found at: ' . $fotoPath . '</p>';
        }
        ?>
    </div>

    <div class="section">
        <h2>3. Photo Subfolders Analysis</h2>
        <?php if ($fotoPath): ?>
        <table>
            <tr>
                <th>Folder</th>
                <th>thumb/ exists?</th>
                <th>thumb/ readable?</th>
                <th>Thumb files</th>
                <th>big/ exists?</th>
                <th>big/ readable?</th>
                <th>Big files</th>
                <th>Status</th>
            </tr>
            <?php foreach ($photoFolders as $folder):
                $thumbPath = $fotoPath . $folder . '/thumb/';
                $bigPath = $fotoPath . $folder . '/big/';

                $thumbExists = is_dir($thumbPath);
                $thumbReadable = $thumbExists ? is_readable($thumbPath) : false;
                $thumbFiles = 0;
                if ($thumbReadable) {
                    $files = scandir($thumbPath);
                    $thumbFiles = count(array_filter($files, function($f) use ($thumbPath) {
                        return $f != '.' && $f != '..' && !is_dir($thumbPath . $f);
                    }));
                }

                $bigExists = is_dir($bigPath);
                $bigReadable = $bigExists ? is_readable($bigPath) : false;
                $bigFiles = 0;
                if ($bigReadable) {
                    $files = scandir($bigPath);
                    $bigFiles = count(array_filter($files, function($f) use ($bigPath) {
                        return $f != '.' && $f != '..' && !is_dir($bigPath . $f);
                    }));
                }

                $allOk = $thumbExists && $thumbReadable && $thumbFiles > 0 && $bigExists && $bigReadable && $bigFiles > 0;
            ?>
            <tr>
                <td><strong><?php echo $folder; ?></strong></td>
                <td><?php echo $thumbExists ? '<span class="ok">✓</span>' : '<span class="error">✗</span>'; ?></td>
                <td><?php echo $thumbReadable ? '<span class="ok">✓</span>' : '<span class="error">✗</span>'; ?></td>
                <td><?php echo $thumbFiles > 0 ? '<span class="ok">' . $thumbFiles . '</span>' : '<span class="error">0</span>'; ?></td>
                <td><?php echo $bigExists ? '<span class="ok">✓</span>' : '<span class="error">✗</span>'; ?></td>
                <td><?php echo $bigReadable ? '<span class="ok">✓</span>' : '<span class="error">✗</span>'; ?></td>
                <td><?php echo $bigFiles > 0 ? '<span class="ok">' . $bigFiles . '</span>' : '<span class="error">0</span>'; ?></td>
                <td><?php echo $allOk ? '<span class="ok">✓ OK</span>' : '<span class="error">✗ PROBLEM</span>'; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
        <p class="error">Cannot analyze subfolders because main "foto" directory was not found.</p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2>4. File Permission Check</h2>
        <?php if ($fotoPath): ?>
        <table>
            <tr><th>Path</th><th>Permissions</th><th>Owner</th><th>Group</th></tr>
            <?php
            $checkPaths = array($fotoPath);
            if (isset($photoFolders[0])) {
                $checkPaths[] = $fotoPath . $photoFolders[0] . '/';
                $checkPaths[] = $fotoPath . $photoFolders[0] . '/thumb/';
            }

            foreach ($checkPaths as $path) {
                if (file_exists($path)) {
                    $perms = substr(sprintf('%o', fileperms($path)), -4);
                    $owner = function_exists('posix_getpwuid') ? posix_getpwuid(fileowner($path))['name'] : fileowner($path);
                    $group = function_exists('posix_getgrgid') ? posix_getgrgid(filegroup($path))['name'] : filegroup($path);
                    echo '<tr>';
                    echo '<td class="mono">' . $path . '</td>';
                    echo '<td>' . $perms . '</td>';
                    echo '<td>' . $owner . '</td>';
                    echo '<td>' . $group . '</td>';
                    echo '</tr>';
                }
            }
            ?>
        </table>
        <p><strong>Expected:</strong> Folders should have at least 755 permissions (rwxr-xr-x), files should have at least 644 (rw-r--r--)</p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2>5. Direct Image Access Test</h2>
        <?php
        // Find a sample image to test
        $sampleImage = null;
        $sampleWebPath = null;
        if ($fotoPath) {
            foreach ($photoFolders as $folder) {
                $thumbPath = $fotoPath . $folder . '/thumb/';
                if (is_dir($thumbPath) && is_readable($thumbPath)) {
                    $files = scandir($thumbPath);
                    foreach ($files as $file) {
                        if ($file != '.' && $file != '..' && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
                            $sampleImage = $thumbPath . $file;
                            $sampleWebPath = '/foto/' . $folder . '/thumb/' . $file;
                            break 2;
                        }
                    }
                }
            }
        }

        if ($sampleImage):
        ?>
        <p><strong>Testing image:</strong> <span class="mono"><?php echo $sampleWebPath; ?></span></p>
        <p><strong>Full path:</strong> <span class="mono"><?php echo $sampleImage; ?></span></p>
        <p><strong>File exists:</strong> <?php echo file_exists($sampleImage) ? '<span class="ok">✓ YES</span>' : '<span class="error">✗ NO</span>'; ?></p>
        <p><strong>File readable:</strong> <?php echo is_readable($sampleImage) ? '<span class="ok">✓ YES</span>' : '<span class="error">✗ NO</span>'; ?></p>
        <p><strong>File size:</strong> <?php echo file_exists($sampleImage) ? number_format(filesize($sampleImage)) . ' bytes' : 'N/A'; ?></p>

        <p><strong>Direct browser test:</strong></p>
        <img src="<?php echo $sampleWebPath; ?>" alt="Test Image" style="max-width: 150px; border: 2px solid #333;"
             onerror="this.style.border='2px solid red'; this.alt='IMAGE FAILED TO LOAD - This is the problem!';" />
        <p><small>If you see a broken image or red border above, the web server cannot serve images from the foto folder.</small></p>
        <?php else: ?>
        <p class="error">No sample images found to test.</p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2>6. HTTP Response Test</h2>
        <p>Testing if the web server returns the image via HTTP (not just file system access):</p>
        <?php
        if ($sampleWebPath) {
            // Build the full URL
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $fullUrl = $protocol . '://' . $host . $sampleWebPath;

            echo '<p><strong>Full URL:</strong> <a href="' . $fullUrl . '" target="_blank" class="mono">' . $fullUrl . '</a></p>';

            // Try to fetch the image via HTTP
            $context = stream_context_create([
                'http' => [
                    'method' => 'HEAD',
                    'timeout' => 5,
                    'ignore_errors' => true
                ]
            ]);

            $headers = @get_headers($fullUrl, 1);

            if ($headers) {
                $statusLine = is_array($headers[0]) ? $headers[0][0] : $headers[0];
                $is200 = strpos($statusLine, '200') !== false;
                $is404 = strpos($statusLine, '404') !== false;
                $is403 = strpos($statusLine, '403') !== false;

                echo '<p><strong>HTTP Status:</strong> ';
                if ($is200) {
                    echo '<span class="ok">' . $statusLine . '</span>';
                } elseif ($is404) {
                    echo '<span class="error">' . $statusLine . ' - IMAGE NOT FOUND VIA HTTP</span>';
                } elseif ($is403) {
                    echo '<span class="error">' . $statusLine . ' - ACCESS FORBIDDEN</span>';
                } else {
                    echo '<span class="warning">' . $statusLine . '</span>';
                }
                echo '</p>';

                if (isset($headers['Content-Type'])) {
                    $ct = is_array($headers['Content-Type']) ? $headers['Content-Type'][0] : $headers['Content-Type'];
                    echo '<p><strong>Content-Type:</strong> ' . $ct . '</p>';
                }

                if ($is404) {
                    echo '<div class="error" style="padding: 15px; background: #ffe0e0; margin: 10px 0;">';
                    echo '<strong>🚨 PROBLEM IDENTIFIED: 404 Not Found</strong><br><br>';
                    echo 'The file exists on disk but the web server returns 404. Possible causes:<br>';
                    echo '<ol>';
                    echo '<li><strong>Apache mod_rewrite conflict:</strong> Check your .htaccess file - it may be rewriting /foto/ URLs incorrectly</li>';
                    echo '<li><strong>Virtual host DocumentRoot:</strong> The DocumentRoot may not point to the folder containing /foto/</li>';
                    echo '<li><strong>Nginx location block:</strong> May need to add a location block for static files</li>';
                    echo '</ol>';
                    echo '</div>';
                }

                if ($is403) {
                    echo '<div class="error" style="padding: 15px; background: #ffe0e0; margin: 10px 0;">';
                    echo '<strong>🚨 PROBLEM IDENTIFIED: 403 Forbidden</strong><br><br>';
                    echo 'The web server is blocking access. Run these commands on your server:<br>';
                    echo '<pre>chmod -R 755 ' . dirname($fotoPath) . '/foto' . "\n";
                    echo 'find ' . dirname($fotoPath) . '/foto -type f -exec chmod 644 {} \\;</pre>';
                    echo '</div>';
                }
            } else {
                echo '<p class="warning">Could not fetch headers (allow_url_fopen may be disabled)</p>';
            }
        }
        ?>
    </div>

    <div class="section">
        <h2>7. .htaccess Check</h2>
        <?php
        $htaccessPath = dirname(__FILE__) . '/.htaccess';
        if (file_exists($htaccessPath)) {
            echo '<p class="ok">✓ .htaccess file exists</p>';
            echo '<p><strong>Contents:</strong></p>';
            echo '<pre style="background: #eee; padding: 10px; overflow-x: auto;">' . htmlspecialchars(file_get_contents($htaccessPath)) . '</pre>';

            $content = file_get_contents($htaccessPath);
            if (strpos($content, 'RewriteEngine') === false) {
                echo '<p class="warning">⚠️ Warning: RewriteEngine directive not found. Add "RewriteEngine On" at the top.</p>';
            }

            // Check if there's a rule that might catch /foto/ URLs
            if (preg_match('/RewriteRule.*\^.*\$.*index\.php/i', $content)) {
                echo '<p class="warning">⚠️ Warning: There are rewrite rules that may catch all URLs and redirect to index.php. ';
                echo 'You may need to add a condition to skip /foto/ directory:</p>';
                echo '<pre style="background: #ffffcc; padding: 10px;">RewriteCond %{REQUEST_URI} !^/foto/</pre>';
            }
        } else {
            echo '<p>.htaccess file not found (this may be fine if using Nginx)</p>';
        }
        ?>
    </div>

    <div class="section">
        <h2>8. Recommendations</h2>
        <ol>
            <?php if (!$fotoPath): ?>
            <li class="error"><strong>Upload the "foto" folder:</strong> The main photo directory is missing. Upload the entire "foto" folder to your server's document root.</li>
            <?php endif; ?>

            <li><strong>Check folder structure:</strong> Ensure each photo category has both <code>thumb/</code> and <code>big/</code> subfolders:
                <pre>
foto/
├── logotipi/
│   ├── thumb/
│   │   ├── 01.jpg
│   │   └── ...
│   └── big/
│       ├── 01.jpg
│       └── ...
├── prospekti/
│   ├── thumb/
│   └── big/
└── ...</pre>
            </li>

            <li><strong>Fix permissions:</strong> Run these commands on the server:
                <pre>
chmod -R 755 /path/to/public_html/foto
find /path/to/public_html/foto -type f -exec chmod 644 {} \;</pre>
            </li>

            <li><strong>Check Apache/Nginx config:</strong> Ensure your web server is configured to serve static files from the document root.</li>
        </ol>
    </div>

    <div class="section" style="background: #ffe6e6;">
        <h2>⚠️ Security Warning</h2>
        <p><strong>DELETE THIS FILE</strong> after you've finished debugging! It exposes server information.</p>
        <p>File location: <code><?php echo __FILE__; ?></code></p>
    </div>
</body>
</html>

