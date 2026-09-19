<?php $page = ['title' => '9.3 Georeferencing without confusing it with projection', 'chapter' => 9, 'module' => '9.3']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 9.3 · General idea, with ArcGIS Pro and QGIS tool names</div>
    <h1>Giving a scanned paper map a place on the Earth</h1>
    <p class="lead">A scanned plan is just a picture: its “coordinates” are pixel column and row, counted from the top-left corner. <strong>Georeferencing</strong> gives it real coordinates by finding a few points you know in both worlds (<em>control points</em>) and fitting a formula through them. The surprise of this module: a report that says “error 0.00 m” can hide a 12 m mistake — and adding more points can make the report look better while the map gets worse.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Fit a simple image-to-ground formula through three control points by hand — and check it.</li>
      <li>Learn why the residual (the fit error at the control points) is not accuracy, and why you must keep an independent <strong>check point</strong>.</li>
      <li>Keep four operations apart: georeferencing, geocoding, CRS assignment, reprojection.</li></ul></div>
  </div>

  <h2><span class="mod">9.3.1</span>Control points and a formula</h2>
  <p>Our made-up scan, <em>“Ward A drainage plan (1998)”</em>, is 600 × 600 pixels. Its image coordinates are (c, r): <strong>column c</strong> increases to the right, <strong>row r</strong> increases <em>downward</em> — the usual picture convention, opposite to the grid’s upward y. Three grid crosses printed on the sheet are identifiable, and their ground positions are known:</p>
  <div class="grid-2">
    <figure class="map-fig" id="scanFig"></figure>
    <div>
      <div class="table-wrap"><table>
        <thead><tr><th>Control point</th><th>Image (c, r)</th><th>Ground (x, y) m</th></tr></thead>
        <tbody><tr><td class="mono">G1</td><td class="mono">(150, 50)</td><td class="mono">(200, 1000)</td></tr><tr><td class="mono">G2</td><td class="mono">(550, 50)</td><td class="mono">(1000, 1000)</td></tr><tr><td class="mono">G3</td><td class="mono">(150, 450)</td><td class="mono">(200, 200)</td></tr></tbody></table></div>
      <p><strong>Fit the simplest useful formula</strong> — a first-order (<em>affine</em>) transformation, which can shift, scale, rotate and shear: x = a + b·c + d·r and y = e + f·c + g·r. Three points give exactly enough equations:</p>
      <ul>
        <li>G1 → G2: c grows by 400, r by 0; x grows by 800, y by 0. So one column = 2 m in x, 0 in y.</li>
        <li>G1 → G3: r grows by 400, c by 0; y falls by 800, x by 0. So one row = −2 m in y, 0 in x.</li>
        <li>Put G1 in: 200 = a + 2·150 → a = −100; 1000 = e − 2·50 → e = 1100.</li>
      </ul>
      <p class="bigno">x = 2c − 100 &nbsp;·&nbsp; y = 1100 − 2r <small>2 metres per pixel, no rotation, y flipped — exactly what a “world file” for this image would say</small></p>
    </div>
  </div>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Click any pixel of the scan — where does it land on the ground?</h3>
    <div class="controls"><label>column c <input type="range" id="pc" min="0" max="600" value="400"></label><label>row r <input type="range" id="pr" min="0" max="600" value="250"></label></div>
    <div class="result" id="probeOut"></div>
    <p class="small">Check the three control points yourself: G2 → x = 2·550 − 100 = 1000 ✓, y = 1100 − 100 = 1000 ✓. Every control point lands exactly where it should, so each <strong>residual</strong> (fitted minus true) is 0 and the <strong>RMS error</strong> is 0.00 m. That is not an achievement: three points <em>always</em> fit an affine exactly — six unknowns, six equations.</p>
  </div>
  <div class="callout dev"><span class="label">Developer view</span><p>This is linear regression. With as many points as parameters the fit is exact and the residuals are meaningless — same as a line through two points. The tools genuinely do least squares when you give more points than the minimum. <strong>Where the comparison breaks:</strong> a regression fits a trend through noisy data. Here the <em>picture itself</em> may be stretched (paper, a curled scan) in a way no simple formula can follow, so a perfect fit at the crosses can sit beside a big error between them.</p></div>

  <h2><span class="mod">9.3.2</span>The check point — and the trap of “improving” the fit</h2>
  <p>Esri’s own page says: “don’t confuse a low RMS error with an accurate registration” — a poorly placed control point can still leave big errors. Three habits protect you: <strong>spread</strong> the control points (one near each corner and a few inside), keep an <strong>independent check point</strong> that you do <em>not</em> use in the fit, and do not add the check point to the fit to make the number smaller.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>The stretched corner</h3>
    <p>A surveyed benchmark <strong>K1</strong> is truly at ground (1000, 200) — the bottom-right corner, where we have no control point. On a perfect 2 m/pixel scan it would sit at pixel (550, 450). On <em>this</em> scan the paper was stretched as it went through the scanner, and the mark appears at pixel <strong>(550, 456)</strong> — six rows lower.</p>
    <div class="opbtns">
      <button class="btn small" id="st0" aria-pressed="true">1 · Fit with G1–G3 only</button>
      <button class="btn small" id="st1">2 · Test with K1 as a check point</button>
      <button class="btn small" id="st2">3 · “Improve” by adding K1 as a 4th control point</button>
    </div>
    <div class="grid-2">
      <figure class="map-fig" id="scanFig2"></figure>
      <div>
        <div id="residBox"></div>
        <div class="status-line q" id="stMsg"></div>
      </div>
    </div>
  </div>
  <div class="callout warn"><span class="label">What to do instead</span><p>Report the check-point discrepancy (12 m at K1) as the evidence of accuracy. Say the fit used three corners. Then either get more control in the stretched region <em>and</em> hold out new check points, or accept the scan for coarse use only and write “about 12 m out in the south-east; not for excavation” into its history. What you must not do is quote “RMS 0.00 m” as the accuracy.</p></div>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">ArcGIS Pro (3.7 docs)</h4><p>Select the raster layer → <strong>Imagery</strong> tab → <strong>Georeference</strong>. <em>Prepare</em> group: Set SRS, Fit To Display. <em>Adjust</em> group: Add Control Points; choose the transformation (first-order needs 3 points, second-order 6, third-order 10, projective 4). <em>Review</em> group: the <strong>Control Point Table</strong> shows each residual and lets you delete a point. <em>Save</em> writes auxiliary files; <em>Save as New</em> writes a new raster. <span class="small">Button labels: verify in your installed version.</span></p></div>
    <div class="card"><h4 style="margin-top:0">QGIS 3.44</h4><p><strong>Layer ▸ Georeferencer</strong>. Add GCP Point, type the map coordinates or click <em>From map canvas</em> on a layer you trust; points are saved in a <code>.points</code> text file. Transformation types: Linear (world file only, ≥ 2 points), Helmert (adds rotation, ≥ 2), Polynomial 1 (affine, ≥ 3), Polynomial 2/3 (≥ 6 / ≥ 10 — can bend edges far from points), Projective (≥ 4), Thin Plate Spline (≥ 10, matches every point exactly). The PDF report lists every GCP’s error.</p></div>
  </div>

  <h2><span class="mod">9.3.3</span>Four operations that beginners mix up</h2>
  <p>Ask one question of each: <em>what exists before the operation?</em></p>
  <div class="table-wrap"><table>
    <thead><tr><th>Operation</th><th>Before</th><th>After</th><th>Numbers changed?</th><th>Label changed?</th><th>ArcGIS Pro tool</th></tr></thead>
    <tbody>
      <tr><td><strong>Georeferencing</strong> (this module)</td><td>A picture; pixels have no ground coordinates</td><td>Every pixel has a ground position; a CRS is attached</td><td class="yes">Yes — created</td><td class="yes">Yes</td><td class="mono">Georeference tab</td></tr>
      <tr><td><strong>CRS assignment</strong> (Chapter 6)</td><td>Coordinates exist; the label is missing or wrong</td><td>Same numbers, corrected label</td><td class="no">No</td><td class="yes">Yes</td><td class="mono">Define Projection</td></tr>
      <tr><td><strong>Reprojection</strong> (Chapter 6)</td><td>Correct coordinates in CRS 1</td><td>Equivalent coordinates in CRS 2</td><td class="yes">Yes — recomputed</td><td class="yes">Yes (new CRS)</td><td class="mono">Project</td></tr>
      <tr><td><strong>Geocoding</strong> (9.2)</td><td>Text — an address</td><td>A point plus match status / score / type</td><td class="yes">Yes — created</td><td>Inherits the locator’s CRS</td><td class="mono">Geocode Addresses</td></tr>
    </tbody></table></div>
  <p>Two of the four <em>create</em> coordinates where there were none (georeferencing, geocoding); one <em>recomputes</em> them (reprojection); one only <em>relabels</em> them (assignment). The dangerous mix-up: running Define Projection on a scan. It has nothing to relabel — the pixels still have no ground position. <em>Orthorectification</em> (correcting aerial and satellite images for terrain and camera tilt) is a different, specialised job and is not in Phase 1.</p>

  <div class="quiz" data-answer="1" data-fb="Pixel (300, 300): x = 2·300 − 100 = 500, y = 1100 − 600 = 500 — the R1/R2 junction. Ground (600, 600): c = (600 + 100)/2 = 350, r = (1100 − 600)/2 = 250.">
    <div class="q">Using x = 2c − 100 and y = 1100 − 2r: where does pixel (300, 300) land, and which pixel shows ground point (600, 600)?</div>
    <div class="opts">
      <button class="opt">(600, 500) and pixel (250, 350)</button>
      <button class="opt">(500, 500) and pixel (350, 250)</button>
      <button class="opt">(500, 500) and pixel (250, 350)</button>
      <button class="opt">(700, 800) and pixel (300, 300)</button>
    </div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="3" data-fb="Six points along the top edge test only the top edge; the fit gets even better there and the RMS falls, while the stretched south-east corner has no control and no check. Accuracy elsewhere stays unmeasured until a check point is placed there.">
    <div class="q">After the three-point fit, a colleague proposes adding six more control points, all along the top edge of the scan, “to bring the RMS down”. What does that achieve?</div>
    <div class="opts">
      <button class="opt">It fixes the south-east error, because more points always help.</button>
      <button class="opt">It tests the south-east error, because the fit is now over-determined.</button>
      <button class="opt">It makes the transformation second-order automatically.</button>
      <button class="opt">It lowers the reported RMS without testing or fixing the south-east corner.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const fig1 = document.getElementById("scanFig"), po = document.getElementById("probeOut");
  function probe() {
    const c = +document.getElementById("pc").value, r = +document.getElementById("pr").value, g = GEO.fit3(c, r);
    renderScan(fig1, { probe: { c, r } });
    po.innerHTML = `Pixel (<strong>${c}</strong>, <strong>${r}</strong>) → ground x = 2·${c} − 100 = <strong>${g.x}</strong>, y = 1100 − 2·${r} = <strong>${g.y}</strong> → <strong class="ids">(${g.x}, ${g.y})</strong> metres. A drain traced here inherits the scan’s date (1998), its placement error, and the operator’s click precision.`;
  }
  ["pc", "pr"].forEach(id => document.getElementById(id).addEventListener("input", probe)); probe();

  const fig2 = document.getElementById("scanFig2"), rb = document.getElementById("residBox"), msg = document.getElementById("stMsg");
  const bar = (id, v, max) => { const w = Math.min(50, Math.abs(v) / max * 50), left = v < 0 ? 50 - w : 50; return `<div class="resid"><span>${id}</span><div class="track"><i style="left:${left}%;width:${w}%"></i></div><span>${v >= 0 ? "+" : ""}${fx(v, 2)} m</span></div>`; };
  function state(n) {
    [0, 1, 2].forEach(i => document.getElementById("st" + i).setAttribute("aria-pressed", i === n));
    if (n === 0) {
      renderScan(fig2, {});
      rb.innerHTML = `<p><strong>Residuals at the control points (y):</strong></p>` + GEO.gcps.map(g => bar(g.id, 0, 4)).join("") + `<p class="small">RMS error = <strong>0.00 m</strong>. Three points, six unknowns: the formula passes through all of them exactly.</p>`;
      msg.className = "status-line q"; msg.textContent = "The report looks perfect. It has proved nothing about the rest of the sheet.";
    } else if (n === 1) {
      const k = GEO.check, g = GEO.fit3(k.c, k.r);
      renderScan(fig2, { showCheck: true, showGhost: true });
      rb.innerHTML = `<p><strong>K1 is not in the fit.</strong> Apply the formula to where K1 appears: x = 2·550 − 100 = ${g.x}; y = 1100 − 2·456 = <strong>${g.y}</strong>.</p><p class="bigno">${k.y - g.y} m <small>K1 lands at (${g.x}, ${g.y}); the survey says (${k.x}, ${k.y}). Anything traced from the lower part of this scan is about 12 m south of the truth.</small></p>`;
      msg.className = "status-line bad"; msg.textContent = "RMS still 0.00 m. The check point found the error the residuals could not.";
    } else {
      const fit = fitAffine([...GEO.gcps, GEO.check]);
      renderScan(fig2, { showCheck: true });
      rb.innerHTML = `<p><strong>Least-squares fit through four points (y residuals):</strong></p>` + fit.res.map(e => bar(e.id, e.ey, 4)).join("") + `<p class="small">x residuals stay 0. RMS error = <strong>${fx(fit.rms, 2)} m</strong> (hand check in the chapter document: ≈ 3.0 m).</p>`;
      msg.className = "status-line bad"; msg.textContent = "The report ‘improved’ from 12 m to about 3 m — but the 12 m stretch is still there, spread as ±3 m over four points, and the three good corners each moved about 3 m. Better number, worse map, and no independent check left.";
    }
  }
  [0, 1, 2].forEach(i => document.getElementById("st" + i).addEventListener("click", () => state(i))); state(0);
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
