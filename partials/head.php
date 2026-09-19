<?php require __DIR__ . '/site.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= h($page['title']) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,400;0,600;0,700;1,400&family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= $ROOT ?>assets/theme.css">
<?php if ($chapter): ?>
<link rel="stylesheet" href="assets/chapter.css">
<?php endif; ?>
</head>
<body>
<?php require __DIR__ . '/topbar.php'; ?>
<main>
