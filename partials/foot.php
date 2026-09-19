<?php /* Closes <main>: module pager (prev / done checkbox / next), then the site footer and shared scripts.
         The page's own <script> follows this include, then end.php closes the document. */ ?>
<?php if ($current): ?>
  <div class="pager">
    <a href="<?= h($prev['file']) ?>">← <?= h(trim($prev['id'] . ' ' . $prev['title'])) ?></a>
    <div class="done-wrap"><label><input type="checkbox" id="doneBox"> I have finished module <?= h($current['id']) ?></label></div>
    <a href="<?= h($next['file']) ?>"><?= h(trim($next['id'] . ' ' . $next['title'])) ?> →</a>
  </div>
<?php endif; ?>
</main>
<footer class="sitefoot">
  <div class="inner">
    <span>GIS &amp; ArcGIS training · <a href="<?= $ROOT ?>index.php">Course index</a><?php if ($chapter): ?> · <a href="index.php">Chapter <?= $chapter['n'] ?></a> · <a href="glossary.php">Glossary</a><?php endif; ?></span>
    <span class="small">Practice data on these pages is made up. Progress is saved in this browser only.</span>
  </div>
</footer>
<script>
window.CHAPTER = <?= json_encode(['n' => $chapter['n'] ?? 0, 'modules' => $modules, 'current' => $current['id'] ?? null], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>
<script src="<?= $ROOT ?>assets/common.js"></script>
<?php if ($chapter): ?>
<script src="assets/app.js"></script>
<?php endif; ?>
