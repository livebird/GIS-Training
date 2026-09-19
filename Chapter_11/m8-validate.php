<?php $page = ['title' => '11.8 Check the result and write it down', 'chapter' => 11, 'module' => '11.8']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 11.8 · General idea (works in every GIS)</div>
    <h1>“Looks right” is not a check</h1>
    <p class="lead">An analysis output is checked with two kinds of evidence: <strong>invariants</strong> — things that <em>must</em> be true if the steps did what the worksheet says — and <strong>spot-checks</strong> — a few records verified by hand from their coordinates. Then you change one thing and see what moves. Then you write enough down that someone else can rerun it.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Run through an eight-row invariants list on the chapter’s outputs.</li>
      <li>Do a <em>change-one-thing</em> sensitivity test and read what it teaches.</li>
      <li>List the seven items a log needs — and why the software’s history pane is not the log.</li></ul></div>
  </div>

  <h2><span class="mod">11.8.1</span>Invariants and spot-checks</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Property</th><th>What must be true</th><th>Example from this chapter</th></tr></thead>
    <tbody>
      <tr><td><strong>Counts</strong></td><td>Record counts are what you predicted; sums reconcile (11.6.3)</td><td>5 eligible; 4 near the road; per-ward sum = distinct assigned + double-counts</td></tr>
      <tr><td><strong>IDs</strong></td><td>The <em>set</em> of IDs matches, not just the count</td><td>{P1, P3, P5, P6}, not merely “4” — four <em>wrong</em> IDs give the same count</td></tr>
      <tr><td><strong>Geometry type</strong></td><td>The output type is the predicted dimension</td><td>Clip a line → line; intersect line × polygon → line; any buffer → polygon</td></tr>
      <tr><td><strong>Extent</strong></td><td>The output lies inside the expected region</td><td>Clipped road inside Ward A’s box; buffer box = road box grown by the distance</td></tr>
      <tr><td><strong>CRS</strong></td><td>The output CRS is the intended one, by <em>code</em>, on the Source tab</td><td>EPSG:32643 for every E11 output; “training grid, no CRS” for paper outputs</td></tr>
      <tr><td><strong>Units</strong></td><td>The buffer field says what you typed, in the unit you meant; magnitudes are plausible</td><td><code>BUFF_DIST = 300</code>; a ward is about 10⁶ m², not 10⁻⁴ or 10¹²</td></tr>
      <tr><td><strong>Validity</strong></td><td>Outputs pass Chapter 9’s validity check; no unexpected empty geometry</td><td>Intersect of the two wards being empty is <em>expected</em>; any other empty output is a defect</td></tr>
      <tr><td><strong>Attributes</strong></td><td>Copied measures were recomputed or renamed; statistics say their cell/record count</td><td><code>length_m</code> recalculated on clipped roads; “n = 8 of 9” beside the raster mean</td></tr>
    </tbody></table></div>
  <p><strong>Spot-checks.</strong> Pick two or three records that sit on the edges — one on a boundary (P5), one exactly at the threshold (P1 at 300 m), one outside (P6) — and verify them from the coordinates, <em>not</em> by looking at the map. “P1: |200 − 500| = 300 ≤ 300 → in” is a spot-check. “P1 looks inside the buffer on screen” is not: at most zoom levels a point 5 m outside a line draws on the line.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Spot-check from the numbers</h3>
    <p>Pick a complaint. The page shows the arithmetic you would do on paper — the map is deliberately not shown.</p>
    <div class="controls"><label>Complaint <select id="spot"></select></label> <label>Threshold <select id="spotT"><option>250</option><option selected>300</option><option>400</option></select> m, edge counts</label></div>
    <div class="result" id="spotOut"></div>
  </div>

  <h2><span class="mod">11.8.2</span>Change one thing</h2>
  <p>Change <strong>one</strong> input or setting, predict what should change, run, compare. This is not a proof; it shows which parts of your result are fragile — and a reader deserves to know that.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Sensitivity on the paper fixture</h3>
    <p class="small">Baseline: eligible = OPEN and reported since 2026-08-01 → P1, P3, P4, P5, P6; threshold 300 m (edge counts), round ends, boundary-inclusive ward membership.</p>
    <div class="controls">
      <label>Threshold <select id="sT"><option>250</option><option selected>300</option><option>400</option></select> m</label>
      <label><input type="checkbox" id="sRound" checked> round ends</label>
      <label><input type="checkbox" id="sIncl" checked> boundary counts as inside</label>
      <label>Since <select id="sDate"><option selected>2026-08-01</option><option>2026-09-01</option></select></label>
    </div>
    <div class="grid-2">
      <div class="table-wrap"><table><thead><tr><th></th><th>Baseline</th><th>Now</th></tr></thead><tbody id="sRows"></tbody></table></div>
      <div class="result" id="sOut"></div>
    </div>
  </div>
  <div class="table-wrap"><table>
    <thead><tr><th>Change</th><th>Prediction</th><th>What it teaches</th></tr></thead>
    <tbody>
      <tr><td>300 → 250 m</td><td>P1 leaves (300 &gt; 250); P3 stays (exactly 250); set {P3, P5, P6}</td><td>A complaint sits on the edge at <em>both</em> thresholds — round numbers do that, and so do rounded real coordinates</td></tr>
      <tr><td>Round → flat ends</td><td>P6 leaves (beyond the end point); {P1, P3, P5}</td><td>Complaints near road <em>ends</em> depend on an easily overlooked setting</td></tr>
      <tr><td>Boundary counts → strictly inside</td><td>P5 leaves both wards; A 1 (P1), B 1 (P3); outside P5, P6</td><td>A boundary point’s fate is a rule choice, not a fact</td></tr>
      <tr><td>Since 1 Aug → since 1 Sep</td><td>P1, P4, P6 drop out of eligibility; eligible {P3, P5}; near road {P3, P5}</td><td>The definition of “recent” moves the answer more than any map setting did</td></tr>
      <tr><td>Planar → geodesic (E11 only)</td><td>Distances change by about 0.1 m at most (UTM scale factor 0.9996, Chapter 6); no complaint changes class at 200 m</td><td>The method matters in principle; here its effect is tiny — <em>say so with the number</em></td></tr>
    </tbody></table></div>
  <p>Report the sensitivity table <em>with</em> the result. A reader who sees that P1’s inclusion depends on ≤ versus &lt; at exactly 300 m will treat “4” with proper care; a reader who sees only “4” will not.</p>

  <h2><span class="mod">11.8.3</span>Record enough to rerun it</h2>
  <p>The record of an analysis is not the output layer. It is whatever lets someone else <em>produce the same output layer</em>. Minimum contents:</p>
  <ol class="steps-list">
    <li><span class="badge-step">1</span><strong>Tool and version.</strong> Product and release (ArcGIS Pro 3.x / QGIS 3.44 / PostGIS x.y) and the tool’s name in that version.</li>
    <li><span class="badge-step">2</span><strong>Parameters — including the ones left at default.</strong> A default is still a decision (11.2, 11.7). ArcGIS Pro’s <em>History</em> pane records “The tool input, output, and other parameter settings”, environments, timing, and messages; entries can be reopened with the same parameters, and the history can be written into the output’s metadata as a “Geoprocessing history” section. QGIS’s history manager stores “The date and time of the execution … along with the parameters used”, “as a command-line expression”, re-runnable by double-click. Copy the entries into the log — the panes are convenient, but they are not the deliverable.</li>
    <li><span class="badge-step">3</span><strong>Processing reference.</strong> The CRS every step ran in, by code, and the transformation used — or “none required” (Chapter 6).</li>
    <li><span class="badge-step">4</span><strong>Source edition.</strong> Which version of each input: file name, date, the provenance note (Chapter 7). Last month’s complaint export is not this month’s.</li>
    <li><span class="badge-step">5</span><strong>Output paths</strong> with geometry type, record count and CRS — so a reader can confirm they opened the right thing.</li>
    <li><span class="badge-step">6</span><strong>Known limitations.</strong> What the output does not establish (11.2.2, 11.7.2) and whatever the data’s provenance said.</li>
    <li><span class="badge-step">7</span><strong>Preserved inputs.</strong> Keep the untouched inputs beside the outputs. Some tools <em>modify their input</em> — Near “adds fields directly to input features”; Calculate Geometry Attributes “modifies the input data” — so run them on copies and say so.</li>
  </ol>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Build one log entry</h3>
    <p class="small">Fill the fields for the buffer step of the paper fixture. Saved in this browser only.</p>
    <div class="lab-form grid-2">
      <div><label>Tool and version</label><input type="text" data-save="log-tool" placeholder="e.g. ArcGIS Pro 3.7 — Buffer (Analysis)"></div>
      <div><label>Parameters (including defaults)</label><input type="text" data-save="log-params" placeholder="Distance 300 (unit Unknown = grid metres); Round ends; Dissolve None; Method Planar"></div>
      <div><label>CRS / transformation</label><input type="text" data-save="log-crs" placeholder="training grid, undefined CRS, metres by declaration; no transformation"></div>
      <div><label>Source edition</label><input type="text" data-save="log-src" placeholder="roads_p11.csv from Chapter 11 §11.9.3, 19 Sep 2026"></div>
      <div><label>Output (path, type, count, extent)</label><input type="text" data-save="log-out" placeholder="r1_buf300 — polygon — 1 record — x −300…2300, y 200…800"></div>
      <div><label>Known limitations</label><input type="text" data-save="log-lim" placeholder="straight-line distance to the centre line; not travel time"></div>
    </div>
    <p class="saved">Saved automatically in this browser.</p>
  </div>
  <div class="callout dev"><span class="label">Developer view</span><p>Outputs are build artefacts; the log plus the preserved inputs are the source and the build script. A binary nobody can rebuild is of unknown provenance. Exact — except that two GIS products given the same inputs can legitimately differ at the millimetre (buffer segments, tolerance, geodesic algorithm), so the log must also record the tolerance within which “the same” is judged.</p></div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“The geoprocessing history is my documentation.”</em> It records what the tool was told — not <em>why</em>, not which source edition, not what the output does not mean — and in ArcGIS Pro it lives in the project unless you deliberately write it to metadata. Raw material for the log, not the log.</p></div>

  <div class="quiz" data-answer="0" data-fb="Check in order: (1) threshold rule — ≤ 300 vs < 300 drops P1; (2) end type — flat ends drop P6; (3) test method — a polygon test can lose P1 by tolerance where the Near number keeps it; (4) time window / date literal — a window from 1 Sep gives {P3, P5} = 2, so it does not explain 3 but a mis-parsed date can drop any subset. The reviewer is right only if the specification actually said “< 300” or “flat ends”.">
    <div class="q">Quick check 11.8. A reviewer reruns your paper-fixture analysis and gets 3 eligible near-road complaints instead of your 4 ({P1, P3, P5, P6}). Which is the best order of things to check?</div>
    <div class="opts"><button class="opt">Threshold ≤ vs &lt; (P1) → end type (P6) → polygon test vs Near number (P1) → date literal.</button><button class="opt">Software version → screen resolution → colour of the buffer.</button><button class="opt">Ask them to send a screenshot and compare visually.</button><button class="opt">Rerun with a bigger distance until the counts match.</button></div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const sel = document.getElementById("spot"); sel.innerHTML = REQUESTS.map(r => `<option>${r.id}</option>`).join("");
  function spot() {
    const r = REQUESTS.find(q => q.id === sel.value), t = +document.getElementById("spotT").value;
    const inX = r.x >= 0 && r.x <= 2000, d = inX ? Math.abs(r.y - 500) : hyp(r.x - (r.x < 0 ? 0 : 2000), r.y - 500);
    const w = wardsOf(r.x, r.y, WARDS.slice(0, 2), true), ws = wardsOf(r.x, r.y, WARDS.slice(0, 2), false);
    document.getElementById("spotOut").innerHTML = `<strong>${r.id} at (${r.x}, ${r.y}).</strong><br>Distance to R1: ${inX ? `the road runs along y = 500 and x = ${r.x} is between 0 and 2000, so distance = |${r.y} − 500| = <strong>${fmt(d)} m</strong>` : `x = ${r.x} is past the road’s end at x = 2000, so the nearest point is the end (2000, 500): √((${r.x} − 2000)² + (${r.y} − 500)²) = <strong>${fmt(d)} m</strong>`}. ${fmt(d)} ≤ ${t} → <strong>${d <= t ? "in" : "out"}</strong>${Math.abs(d - t) < 1e-9 ? " — <em>exactly on the threshold</em>; the ≤ rule decides, not the software" : ""}.<br>Ward, boundary counts: x ${r.x}, y ${r.y} → ${w.length ? w.map(id => `inside ${id}`).join(" and ") : "inside no ward"}${w.length > 1 ? " (on the shared line x = 1000)" : ""}. Strictly inside: ${ws.length ? ws.join(", ") : "none"}.<br>Eligible? status ${r.status}, reported ${r.date} → <strong>${r.status === "OPEN" && r.date >= "2026-08-01" ? "yes" : "no"}</strong>.`;
  }
  sel.addEventListener("change", spot); document.getElementById("spotT").addEventListener("change", spot); spot();

  function run(t, rnd, incl, since) {
    const el = eligible(REQUESTS, since), near = el.filter(r => distToLine(r.x, r.y, ROADS.R1.pts, !rnd) <= t + 1e-9);
    const per = {}; near.forEach(r => per[r.id] = wardsOf(r.x, r.y, WARDS.slice(0, 2), incl));
    const cnt = { A: near.filter(r => per[r.id].includes("A")).length, B: near.filter(r => per[r.id].includes("B")).length };
    return { el: el.map(r => r.id), near: near.map(r => r.id), cnt, out: near.filter(r => per[r.id].length === 0).map(r => r.id) };
  }
  const base = run(300, true, true, "2026-08-01");
  function sens() {
    const now = run(+sT.value, sRound.checked, sIncl.checked, sDate.value);
    const cell = (a, b) => a === b ? b : `<mark>${b}</mark>`;
    tableRows(document.getElementById("sRows"), [
      { cells: ["Eligible", idsText(base.el), { html: cell(idsText(base.el), idsText(now.el)) }] },
      { cells: ["Near the road", idsText(base.near), { html: cell(idsText(base.near), idsText(now.near)) }] },
      { cells: ["Raw count A / B", `${base.cnt.A} / ${base.cnt.B}`, { html: cell(`${base.cnt.A} / ${base.cnt.B}`, `${now.cnt.A} / ${now.cnt.B}`) }] },
      { cells: ["Outside both wards", idsText(base.out), { html: cell(idsText(base.out), idsText(now.out)) }] }
    ]);
    const changes = [];
    if (+sT.value !== 300) changes.push(`threshold ${sT.value} m`); if (!sRound.checked) changes.push("flat ends"); if (!sIncl.checked) changes.push("strict interior"); if (sDate.value !== "2026-08-01") changes.push(`since ${sDate.value}`);
    document.getElementById("sOut").innerHTML = changes.length === 0 ? "This is the baseline. Change exactly one setting and read what moved." : changes.length > 1 ? `<span class="chip no">${changes.length} things changed</span> — you can no longer tell which one caused the difference. Change one at a time.` : `<span class="chip yes">one change: ${changes[0]}</span> — highlighted cells moved. Write the before/after pair in the report next to the result.`;
  }
  const sT = document.getElementById("sT"), sRound = document.getElementById("sRound"), sIncl = document.getElementById("sIncl"), sDate = document.getElementById("sDate");
  [sT, sRound, sIncl, sDate].forEach(e => e.addEventListener("change", sens)); sens();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
