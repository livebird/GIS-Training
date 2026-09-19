<?php
/* Router for PHP's built-in server only (see .claude/launch.json):
       php -S localhost:8760 dev-router.php
   It mirrors .htaccess so extensionless URLs work locally. Not used on the live server. */
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$file = __DIR__ . $path;

if (preg_match('#^/(partials|config)/#', $path)) { http_response_code(404); echo '404 Not Found'; return true; }

// /path/index.php -> /path/ and /path/page.php -> /path/page
if (preg_match('#^(.*/)index\.php$#', $path, $m)) { header('Location: ' . $m[1], true, 301); return true; }
if (preg_match('#^(.+)\.php$#', $path, $m))       { header('Location: ' . $m[1], true, 301); return true; }

if (is_dir($file)) {
    if (substr($path, -1) !== '/') { header('Location: ' . $path . '/', true, 301); return true; }
    if (is_file($file . 'index.php')) { $script = $file . 'index.php'; $path .= 'index.php'; }
    else { return false; }
} elseif (is_file($file)) {
    return false;                                    // static asset: let the server send it
} elseif (is_file($file . '.php')) {
    $script = $file . '.php'; $path .= '.php';
} else {
    http_response_code(404); echo '404 Not Found'; return true;   // the built-in server would otherwise fall back to /index.php
}

$_SERVER['SCRIPT_NAME'] = $path;                     // partials/site.php uses basename() of this
$_SERVER['SCRIPT_FILENAME'] = $script;
chdir(dirname($script));
require $script;
