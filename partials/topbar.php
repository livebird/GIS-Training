<?php /* Sticky top bar. Chapter pages: course link, chapter brand, module pills, glossary, progress bar.
         Root pages: course brand and phase links. common.js marks finished modules and fills the bar. */ ?>
<header class="topbar">
  <div class="inner">
<?php if ($chapter): ?>
    <a class="home" href="<?= $HOME ?>" title="Course index">← Course</a>
    <a class="brand" href="./">GIS <?= h($phase['n']) ?> · <span>Chapter <?= $chapter['n'] ?></span></a>
    <nav class="modnav" aria-label="Modules">
<?php foreach ($modules as $m): ?>
      <a href="<?= h($m['file']) ?>" data-module="<?= h($m['id']) ?>" class="<?= $current && $current['id'] === $m['id'] ? 'active' : '' ?>" title="<?= h($m['title']) ?>"><?= h($m['id']) ?></a>
<?php endforeach; ?>
      <a href="glossary" class="<?= $HERE === 'glossary.php' ? 'active' : '' ?>">Glossary</a>
    </nav>
<?php else: ?>
    <a class="brand" href="<?= $HOME ?>">GIS &amp; ArcGIS <span>Training</span></a>
    <nav class="modnav" aria-label="Phases">
<?php foreach ($PHASES as $ph): ?>
      <a href="<?= $HOME ?>#<?= h($ph['id']) ?>"><?= h($ph['n']) ?></a>
<?php endforeach; ?>
    </nav>
<?php endif; ?>
  </div>
  <div class="progress"><div></div></div>
</header>
