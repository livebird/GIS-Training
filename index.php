<?php $page = ['title' => 'GIS & ArcGIS Training — Course index']; require __DIR__ . '/partials/head.php'; ?>
  <section class="hero">
    <div class="fade-up">
      <div class="page-head" style="padding:0">
        <div class="eyebrow">GIS &amp; ArcGIS training · Interactive tutorials</div>
      </div>
      <h1 class="big">From <em>developer</em><br>to GIS delivery.</h1>
      <p class="lead">A 37-chapter curriculum for software developers with no GIS background: shared foundations first, then ArcGIS Pro, ArcGIS Online, custom development and ArcGIS Enterprise administration. Each chapter is a self-contained interactive tutorial — pick one below.</p>
      <p style="display:flex;flex-wrap:wrap;gap:.6rem"><a class="btn primary" href="Chapter_01/">Start with Chapter 1 →</a> <a class="btn ghost" href="#phase-1">Browse chapters</a></p>
      <p class="small">No software is needed for Phase 1. Every map uses <span class="synthetic">made-up practice data</span> — a small imaginary town, not a real place. Your progress is saved in this browser only.</p>
      <div class="course-stats" id="courseStats"></div>
    </div>
    <figure class="map-fig fade-up">
      <svg viewBox="-140 40 2560 1190" role="img" aria-label="Schematic map of the practice town: Ward A and Ward B side by side, Main Road R1 across the middle, six request points.">
        <line x1="0" y1="1140" x2="2300" y2="1140" class="axis"/>
        <line x1="-40" y1="1100" x2="-40" y2="50" class="axis"/>
        <text x="0" y="1195" class="axis-label" text-anchor="middle">0</text>
        <text x="500" y="1195" class="axis-label" text-anchor="middle">500</text>
        <text x="1000" y="1195" class="axis-label" text-anchor="middle">1000</text>
        <text x="1500" y="1195" class="axis-label" text-anchor="middle">1500</text>
        <text x="2000" y="1195" class="axis-label" text-anchor="middle">2000</text>
        <text x="2320" y="1152" class="axis-label">x (m)</text>
        <text x="-75" y="1110" class="axis-label" text-anchor="end">0</text>
        <text x="-75" y="610" class="axis-label" text-anchor="end">500</text>
        <text x="-75" y="110" class="axis-label" text-anchor="end">1000</text>
        <text x="-120" y="20" class="axis-label">y (m)</text>
        <polygon points="0,1100 1000,1100 1000,100 0,100" class="ward"/>
        <polygon points="1000,1100 2000,1100 2000,100 1000,100" class="ward"/>
        <text x="500" y="300" class="ward-label" text-anchor="middle">Ward A</text>
        <text x="1500" y="300" class="ward-label" text-anchor="middle">Ward B</text>
        <line x1="0" y1="600" x2="2000" y2="600" class="road"/>
        <text x="1020" y="570" class="road-label">R1 · Main Road</text>
        <circle cx="200" cy="900" r="26" class="req"/><text x="240" y="915" class="req-label">P1</text>
        <circle cx="650" cy="700" r="26" class="req"/><text x="690" y="715" class="req-label">P2</text>
        <circle cx="1100" cy="450" r="26" class="req"/><text x="1140" y="465" class="req-label">P3</text>
        <circle cx="1450" cy="650" r="26" class="req"/><text x="1490" y="665" class="req-label">P4</text>
        <circle cx="1800" cy="950" r="26" class="req"/><text x="1840" y="965" class="req-label">P5</text>
        <circle cx="2250" cy="350" r="26" class="req"/><text x="2290" y="365" class="req-label">P6</text>
        <rect x="380" y="380" width="44" height="44" class="asset"/><text x="440" y="415" class="asset-label">Depot</text>
      </svg>
      <figcaption>Practice data (made up) · metres · not a real place</figcaption>
    </figure>
  </section>

  <h2 style="border-top:0;margin-top:1rem"><span class="mod">How the course is built</span>Five phases, one team</h2>
  <div class="grid-2">
    <div class="card">
      <p><strong>Everyone learns the shared foundations and ArcGIS configuration</strong> (Phases 1–3). Application development and Enterprise administration are then separate tracks (Phases 4 and 5) that come back together for the final delivery project.</p>
      <p>Each chapter has practical work from the beginning: a small imaginary town, a running question — <em>“Which unresolved service requests should an inspection team investigate first?”</em> — and a lab you submit to your instructor.</p>
      <div class="phase-toc">
        <a href="#phase-1">Phase 1 · Foundations</a>
        <a href="#phase-2">Phase 2 · ArcGIS Pro</a>
        <a href="#phase-3">Phase 3 · ArcGIS Online</a>
        <a href="#phase-4">Phase 4 · Development</a>
        <a href="#phase-5">Phase 5 · Enterprise</a>
        <a href="#phase-6">Chapter 37 · Project</a>
      </div>
    </div>
    <div class="card">
      <h4 style="margin-top:0">Reading the cards</h4>
      <ul>
        <li><strong>Solid card</strong> — an interactive tutorial is ready. Click to open it.</li>
        <li><strong>Dashed card marked <span class="status drafted">Drafted</span></strong> — the chapter document is written; its tutorial is not built yet.</li>
        <li><strong>Dashed card marked <span class="status">Planned</span></strong> — on the curriculum, not yet written.</li>
        <li>The thin bar under a ready chapter shows how many of its modules you have marked done.</li>
      </ul>
    </div>
  </div>

<?php foreach ($PHASES as $ph): ?>
  <section class="phase" id="<?= h($ph['id']) ?>">
    <h2><span class="mod"><?= h($ph['n']) ?></span><?= h($ph['title']) ?></h2>
<?php if (!empty($ph['intro'])): ?>
    <p class="lead" style="font-size:1.05rem"><?= h($ph['intro']) ?></p>
<?php endif; ?>
    <div class="chcards">
<?php foreach ($ph['chapters'] as $ch): ?>
<?php if ($ch['status'] === 'ready'): ?>
      <a class="chcard" href="<?= h($ch['dir']) ?>/" data-chapter="<?= $ch['n'] ?>" data-modules="<?= h(implode(',', array_column($ch['modules'], 'id'))) ?>">
        <span class="done-badge">done</span>
        <div class="id">CHAPTER <?= $ch['n'] ?></div>
        <h3><?= h($ch['title']) ?></h3>
        <p><?= h($ch['desc']) ?></p>
        <div class="foot"><span class="small mono progress-text"><?= count($ch['modules']) ?> modules</span><span class="go">Open tutorial →</span></div>
        <div class="bar" aria-hidden="true"><div></div></div>
      </a>
<?php else: ?>
      <div class="chcard soon" aria-disabled="true">
        <div class="id">CHAPTER <?= $ch['n'] ?></div>
        <h3><?= h($ch['title']) ?></h3>
        <p><?= h($ch['desc']) ?></p>
        <div class="foot"><span class="small"><?= $ch['status'] === 'drafted' ? 'Document written · tutorial coming' : 'On the curriculum' ?></span><span class="status <?= h($ch['status']) ?>"><?= $ch['status'] === 'drafted' ? 'Drafted' : 'Planned' ?></span></div>
      </div>
<?php endif; ?>
<?php endforeach; ?>
    </div>
<?php if (!empty($ph['checkpoint'])): ?>
    <div class="checkpoint"><span class="label">Phase checkpoint</span><?= h($ph['checkpoint']) ?></div>
<?php endif; ?>
  </section>
<?php endforeach; ?>

  <p class="small" style="margin-top:2.5rem">Source: <em>GIS-ArcGIS-Training Chapters List.md</em> (revised master chapter sequence). Train everyone through Phase 3, then let application developers and Enterprise administrators deepen their respective tracks before collaborating on Chapter 37.</p>
<?php require __DIR__ . '/partials/foot.php'; ?>
<script>
// Cards are server-rendered from config/chapters.php; this only adds what the browser knows — module progress.
function pageInit() {
  const cards = document.querySelectorAll(".chcard[data-chapter]");
  let completed = 0;
  cards.forEach(card => {
    const ids = card.dataset.modules.split(",");
    let p = {};
    try { p = JSON.parse(localStorage.getItem(`gis-ch${card.dataset.chapter}-progress`) || "{}"); } catch { p = {}; }
    const done = ids.filter(id => p[id]).length;
    card.querySelector(".progress-text").textContent = `${done} / ${ids.length} modules done`;
    card.querySelector(".bar > div").style.width = `${Math.round(100 * done / ids.length)}%`;
    if (done === ids.length) { card.classList.add("done"); completed++; }
  });
  const all = <?= count($ALL_CHAPTERS) ?>, ready = cards.length;
  document.getElementById("courseStats").innerHTML =
    `<span><strong>${all}</strong> chapters</span><span><strong>${ready}</strong> tutorials ready</span><span><strong>${completed}</strong> completed by you</span>`;
}
window.addEventListener("pageshow", e => { if (e.persisted) pageInit(); });
</script>
<?php require __DIR__ . '/partials/end.php'; ?>
