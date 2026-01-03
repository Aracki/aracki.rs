# aracki.rs

A graphic design studio portfolio website.

## Deployment Troubleshooting: Photos Not Loading

If photos from the `foto/` directory are not loading after deployment, check the following:

### Quick Diagnosis
1. Visit `http://your-server/debug_photos.php` to run the diagnostic script
2. Check browser DevTools (F12) → Network tab to see if image requests are failing

### Common Issues & Solutions

#### 1. Missing `foto/` folder
The `foto/` directory must be uploaded with the following structure:
```
public_html/
└── foto/
    ├── logotipi/
    │   ├── thumb/    ← thumbnails shown in gallery
    │   │   ├── 01.jpg
    │   │   └── ...
    │   └── big/      ← full-size images for lightbox
    │       ├── 01.jpg
    │       └── ...
    ├── prospekti/
    ├── kalendari/
    ├── bilbordi/
    ├── 3d/
    ├── knjige/
    ├── ilustracije/
    ├── beograd/
    ├── vojvodina/
    ├── biljke/
    ├── ljudi/
    └── priroda/
```

#### 2. File Permissions
The web server needs read access to the foto directory:
```bash
# Set directory permissions
chmod -R 755 /path/to/public_html/foto

# Set file permissions  
find /path/to/public_html/foto -type f -exec chmod 644 {} \;
```

#### 3. Web Server Configuration
Ensure your Apache/Nginx is configured to serve static files. For Apache, check that:
- `Options Indexes` is not blocking directory listing (though not needed)
- No `.htaccess` rules are blocking access to image files

#### 4. DOCUMENT_ROOT Issues
The `view_image()` function in `engine/function.php` uses `$_SERVER['DOCUMENT_ROOT']` to locate photos. If your server's DOCUMENT_ROOT doesn't point to `public_html/`, update your virtual host configuration.

### How Photos Are Loaded
The `view_image($width, $height, $folder)` function in `engine/function.php`:
1. Scans `/foto/{folder}/thumb/` for thumbnail images
2. Generates `<img>` tags pointing to `/foto/{folder}/thumb/{filename}`
3. Links each thumbnail to `/foto/{folder}/big/{filename}` for the lightbox

### Files to Check
- `engine/function.php` - contains `view_image()` function
- `naslovna.php` - main page that displays the portfolio
- `debug_photos.php` - diagnostic script (DELETE after use!)

