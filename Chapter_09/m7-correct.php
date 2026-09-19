<?php $page = ['title' => '9.7 Apply corrections with traceability', 'chapter' => 9, 'module' => '9.7']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 9.7 · General idea, with ArcGIS / QGIS / PostGIS repair tools</div>
    <h1>Fix it so that someone else can see exactly what you did</h1>
    <p class="lead">Three rules, in order: the original survives untouched; every change has a before/after record; you chase the <em>cause</em> upstream instead of correcting every export forever. Then two warnings: automatic repair tools change shapes — you still have to look — and a dataset that “passes” because you filled in the blanks is <em>worse</em> than the damaged one.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Write a before/after record for a shape edit and for a value edit.</li>
      <li>See what an automatic repair actually does to Ward C, and why the manual fix is the right one.</li>
      <li>Keep unresolved items explicit instead of inventing values.</li></ul></div>
  </div>

  <h2><span class="mod">9.7.1</span>Original, before/after, cause</h2>
  <div class="grid-3">
    <div class="card"><h4 style="margin-top:0">Rule 1 — the original survives</h4><p>Item 5 of the intake checklist. <code>Chapter09_Damaged/</code> stays read-only; <code>Chapter09_Work/</code> is edited; <code>Chapter09_Corrected_v1/</code> is what you release. Once an edit is <em>saved</em> it cannot be reversed from the data itself.</p></div>
    <div class="card"><h4 style="margin-top:0">Rule 2 — every change has a record</h4><p>Value edit: ID, field, old, new, evidence, who, when. Shape edit: ID, what moved (“north end point”), old coordinate, new coordinate, evidence, who, when — and the old shape as WKT if you redrew it. <em>Editor tracking</em> (Chapter 8) records who and when, not what or why.</p></div>
    <div class="card"><h4 style="margin-top:0">Rule 3 — fix the cause</h4><p>If SL-0127’s height is wrong in your copy because it is wrong in the asset register, correcting the copy fixes this release only. Push it upstream: the register, the form (add a unit field), the conversion script, the domain (add <code>GEOCODED</code>). Downstream fixes are a stopgap — flag them: “corrected in copy; source not yet corrected; owner told”.</p></div>
  </div>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Build the before/after record for R4</h3>
    <p>Choose values from the practice data; the record assembles below.</p>
    <div class="controls">
      <label>Feature <select id="baF"><option>R4</option><option>R1</option><option>R5</option></select></label>
      <label>Element <select id="baE"><option>north end point (vertex 2 of 2)</option><option>whole line</option><option>width_m</option></select></label>
      <label>Before <select id="baB"><option>(300, 497)</option><option>(300, 300)</option><option>5</option></select></label>
      <label>After <select id="baA"><option>(300, 500)</option><option>(300, 497)</option><option>12</option></select></label>
      <label>Evidence <select id="baV"><option>Road register: “Temple Lane, temple to junction with Main Road”; R1 passes through (300, 500); gap 3 m</option><option>It looked wrong on the map</option><option>A colleague said so</option></select></label>
    </div>
    <div class="table-wrap"><table class="logtable"><thead><tr><th>Feature</th><th>Element</th><th>Before</th><th>After</th><th>Evidence</th><th>Editor / when</th></tr></thead><tbody id="baRow"></tbody></table></div>
    <div class="status-line" id="baMsg"></div>
  </div>
  <div class="callout dev"><span class="label">Developer view</span><p>Rules 1 and 2 are version control: the original is the previous commit, the before/after record is the diff, the evidence is the commit message. Rule 3 is “fix the bug, not the symptom”. <strong>Where the comparison breaks:</strong> geometry engines do not give readable diffs of shapes, so you write the old coordinates down yourself; and a data “commit” may later prove wrong in the light of evidence you did not have, so the log must keep the evidence, not just the change.</p></div>

  <h2><span class="mod">9.7.2</span>Automatic repair changes shapes — you still have to look</h2>
  <p>Every platform has a “make it valid” operation. They are useful — and they <em>change the geometry</em> by design.</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Platform</th><th>Operation</th><th>What it does (from the documentation)</th><th>What it may change</th></tr></thead>
    <tbody>
      <tr><td>ArcGIS Pro</td><td><strong>Repair Geometry</strong> — “This tool modifies the input data”</td><td>Null shape: the record is <em>deleted</em> (default on; can be unticked). Short segments deleted. Self-intersections: “the areas of overlap in a polygon will be dissolved”. Unclosed rings closed by joining the ends. Duplicate vertices removed. Ring order and envelopes corrected.</td><td>Rows can disappear; shapes can lose parts or gain segments. Basic licence cannot run it on enterprise geodatabase inputs.</td></tr>
      <tr><td>QGIS 3.44</td><td><strong>Fix geometries</strong></td><td>“Attempts to create a valid representation of a given invalid geometry without losing any of the input vertices… Always outputs multi-geometry layer.” Methods: <em>Linework</em> (nodes all rings and extracts valid polygons) and <em>Structure</em> (fixes rings, then unions shells and subtracts holes; needs GEOS ≥ 3.10).</td><td>Output type becomes multi for the whole layer; a self-crossing polygon becomes a multipolygon of its lobes; M values dropped.</td></tr>
      <tr><td>PostGIS</td><td><code>ST_MakeValid(geom)</code></td><td>Same two methods, <code>linework</code> (default) and <code>structure</code>; <code>keepcollapsed</code> controls degenerate parts.</td><td>A polygon can come back as a MultiPolygon or a GeometryCollection.</td></tr>
    </tbody></table></div>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Repair Ward C automatically — then compare with the evidence</h3>
    <div class="opbtns"><button class="btn small" id="rp0" aria-pressed="true">As typed</button><button class="btn accent small" id="rp1">Run the “linework” repair on a copy</button><button class="btn small" id="rp2">Manual fix from the register</button></div>
    <div class="grid-2">
      <figure class="map-fig" id="rpFig"></figure>
      <div id="rpInfo"></div>
    </div>
  </div>
  <div class="callout note"><span class="label">Honesty note</span><p>The two-triangle result follows from the <em>documented</em> linework method (QGIS / PostGIS). How ArcGIS Pro’s <em>Esri</em> method treats a bow-tie whose lobes do not overlap is <strong>not stated</strong> on the page we read, so the chapter does not assert it. Your instructor runs Repair Geometry on a copy first and records what actually happened (chapter document, Instructor Appendix I.6). In the lab you may run the repair on a <em>second</em> copy as an experiment — never as the correction for Ward C.</p></div>
  <p><strong>When automatic repair is right:</strong> a duplicated vertex, an unclosed ring whose ends are a centimetre apart, a bad envelope — cases where the repaired shape is what anyone would draw by hand. Even then: run it on the copy, compare counts and areas before/after, and log which tool, which method, and what changed.</p>

  <h2><span class="mod">9.7.3</span>Leave open items open — never invent an observation</h2>
  <p>A corrected dataset is not one with zero open issues. It is one where every open issue is <em>listed</em>, with what would resolve it. Four items stay open at the end of the lab:</p>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">INS-0005 has no condition score</h4><p>The visit happened (“pole leaning” was written); the score was not. It stays <strong>blank</strong>. Filling in 3 “because most visits are 3” or 2 “because it is leaning” is fabrication: a number in that column claims an inspector <em>judged</em> the pole. Unresolved list: “needs re-inspection or the inspector’s memory; excluded from statistics until resolved”.</p></div>
    <div class="card"><h4 style="margin-top:0">INS-0006 refers to DR-0044</h4><p>Stays an orphan, kept. Unresolved list: “needs the paper form”.</p></div>
    <div class="card"><h4 style="margin-top:0">Two rows are called SL-0114</h4><p>(260, 190) digitised 2024, and (1250, 420) GNSS 2025 — about 1,016 m apart. Not one light. Which keeps the number is the owner’s decision under the identity policy; renumbering one yourself breaks any work order or photo file that uses it.</p></div>
    <div class="card"><h4 style="margin-top:0">TR-0301 is outside every ward</h4><p>Either the ward layer is incomplete east of x = 2000, or the tree is outside the town (or misplaced — its method is APPROX). Deciding evidence: the ward register’s eastern extent; a better observation of the tree.</p></div>
  </div>
  <div class="callout warn"><span class="label">Why this matters beyond neatness</span><p>A dataset that “passes validation” because blanks were filled and orphans re-keyed is <strong>worse</strong> than the damaged one: its problems are now invisible. Chapter 12 will ask you to show uncertainty on a map; you cannot show what the data no longer admits.</p></div>

  <div class="quiz" data-answer="1" data-fb="With the default ‘Delete Features with Null Geometry’ ticked, P7’s record is deleted — a legitimate ‘to be located’ complaint destroyed. Untick that option, or run the tool only on layers where a missing shape really is a defect.">
    <div class="q">Chapter 3’s complaint P7 has no location on purpose (“caller could not give a location; to be located”). If its layer were run through ArcGIS Pro’s Repair Geometry with default settings, what would happen?</div>
    <div class="opts">
      <button class="opt">Nothing — the tool only fixes polygons.</button>
      <button class="opt">P7’s record would be deleted, because “Delete Features with Null Geometry” is on by default.</button>
      <button class="opt">P7 would be given the map’s centre as its location.</button>
      <button class="opt">The tool would refuse to run.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const ids = ["baF", "baE", "baB", "baA", "baV"], row = document.getElementById("baRow"), msg = document.getElementById("baMsg");
  function ba() {
    const v = ids.map(i => document.getElementById(i).value);
    row.innerHTML = `<tr><td class="mono">${v[0]}</td><td>${v[1]}</td><td class="mono">${v[2]}</td><td class="mono">${v[3]}</td><td>${v[4]}</td><td>learner / ${new Date().toISOString().slice(0, 10)}</td></tr>`;
    const good = v[0] === "R4" && v[1].startsWith("north") && v[2] === "(300, 497)" && v[3] === "(300, 500)" && v[4].startsWith("Road register");
    msg.className = "status-line " + (good ? "ok" : "bad");
    msg.textContent = good ? "Complete: a second person can find the feature, see what moved, and check the evidence. Add the stored length change 197 → 200 m if length_m is a stored column." : !v[4].startsWith("Road register") ? "Weak evidence. “It looked wrong” and “a colleague said” do not let anyone check you later; cite the register and the measured gap." : "Check the feature, element, before and after — the R4 fix moves its north end point from (300, 497) to (300, 500).";
  }
  ids.forEach(i => document.getElementById(i).addEventListener("change", ba)); ba();

  const fig = document.getElementById("rpFig"), info = document.getElementById("rpInfo");
  function rp(n) {
    [0, 1, 2].forEach(i => document.getElementById("rp" + i).setAttribute("aria-pressed", i === n));
    renderWardC(fig, ["typed", "repair", "fixed"][n]);
    info.innerHTML = [
      `<p class="bigno">0 m² <small>as typed — invalid; the number is meaningless</small></p><p>Two lobes: the triangle (0, 1000)–(1000, 1000)–(500, 1250) and the triangle (0, 1500)–(1000, 1500)–(500, 1250).</p>`,
      `<p class="bigno">250,000 m² <small>multipolygon of two triangles touching at (500, 1250)</small></p><p>The repair did exactly what its documentation says: it cut the ring at the crossing, kept every vertex, and returned a <strong>valid</strong> shape. It is also <strong>the wrong ward</strong> — half the register’s area. No tool can know that the vertex <em>order</em> was the mistake. Log this result as an experiment; do not release it.</p>`,
      `<p class="bigno">500,000 m² <small>rectangle (0, 1000) → (1000, 1000) → (1000, 1500) → (0, 1500)</small></p><p>Corners re-ordered by hand because the register says Ward C is that rectangle. Log: the old ring as WKT, the new ring, the evidence, and the note that the automatic repair would have given 250,000 m².</p>`
    ][n];
  }
  [0, 1, 2].forEach(i => document.getElementById("rp" + i).addEventListener("click", () => rp(i))); rp(0);
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
