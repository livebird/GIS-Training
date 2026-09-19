<?php
/* Shared helpers and page context. Included by head.php; pages never include this directly.
   A page sets $page before requiring head.php:
     $page = ['title' => '1.1 Start with a decision', 'chapter' => 1, 'module' => '1.1'];
   'chapter' and 'module' are optional (root pages have neither, glossaries have no module).
   Chapter pages live one folder below the root, so $ROOT defaults to '../'. */

function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

$ROOT   = $ROOT ?? (isset($page['chapter']) ? '../' : '');
$HOME   = $ROOT === '' ? './' : $ROOT;   // link to the course index (URLs are extensionless; see .htaccess)
$PHASES = require __DIR__ . '/../config/chapters.php';
$ALL_CHAPTERS = array_merge(...array_column($PHASES, 'chapters'));

$chapter = null; $phase = null; $modules = []; $current = null; $prev = null; $next = null;
if (isset($page['chapter'])) {
    foreach ($PHASES as $ph) {
        foreach ($ph['chapters'] as $ch) {
            if ($ch['n'] === (int)$page['chapter']) { $chapter = $ch; $phase = $ph; }
        }
    }
    if (!$chapter || $chapter['status'] !== 'ready') {
        trigger_error("Chapter {$page['chapter']} is not a built chapter in config/chapters.php", E_USER_ERROR);
    }
    $modules = $chapter['modules'];
    if (isset($page['module'])) {
        foreach ($modules as $i => $m) {
            if ($m['id'] === $page['module']) {
                $current = $m;
                $home = ['file' => './', 'title' => 'Chapter home', 'id' => ''];
                $prev = $i > 0 ? $modules[$i - 1] : $home;
                $next = $i < count($modules) - 1 ? $modules[$i + 1] : $home;
            }
        }
        if (!$current) trigger_error("Module {$page['module']} is not in chapter {$page['chapter']}", E_USER_ERROR);
    }
}
$HERE = basename($_SERVER['SCRIPT_NAME'] ?? '');
