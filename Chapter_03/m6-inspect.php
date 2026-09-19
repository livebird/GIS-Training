<?php $page = ['title' => '3.6 Extent, count, and what an edit changes', 'chapter' => 3, 'module' => '3.6']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 3.6 · General idea, with version-specific steps in boxes</div>
    <h1>Inspect before you trust: extent, count, and what an edit changes</h1>
    <p class="lead">Three habits that take a minute and save days: read the <strong>extent</strong> as a box, not a shape; write an <strong>intake record</strong> (count, shape type, columns, rows with no shape); and <strong>predict which stored rows will change</strong> before you touch anything.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Read a bounding box and see what it hides — gaps between parts, holes, empty corners, outliers.</li>
      <li>Fill in an intake record for the town and check it.</li>
      <li>Change a layer’s look (no rows change) and move a point on a scratch copy (one row changes) — after predicting both.</li></ul></div>
  </div>

  <h2><span class="mod">3.6.1</span>Extent is a box, not the shape</h2>
  <p>The <strong>extent</strong> (also called the bounding box or envelope) of a shape or a whole dataset is the smallest upright rectangle that contains it — four numbers: x-min, y-min, x-max, y-max. Software uses it constantly (<em>Zoom To Layer</em> zooms to it; layer properties report it; spatial indexes are built on it), so it is the <strong>first</strong> thing to record — and the first thing not to over-read.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <div class="controls" id="extPick">
      <button class="btn small" data-k="wards">Wards</button><button class="btn small" data-k="roads">Roads</button><button class="btn small" data-k="requests">Requests</button><button class="btn small" data-k="assets">Assets</button><button class="btn small" data-k="park">Park PK-01</button><button class="btn small" data-k="depot">Depot DP-01</button><button class="btn small" data-k="R2">Road R2 only</button><button class="btn small" data-k="schoolPt">School point</button>
    </div>
    <div class="two-col">
      <figure class="map-fig" id="extFig"></figure>
      <div><div class="tiles" id="extTiles"></div><div class="result" id="extOut">Pick a layer.</div></div>
    </div>
  </div>
  <div class="grid-3">
    <div class="card"><h4 style="margin-top:0">Boxes overlap ≠ shapes overlap</h4><p>The Roads box contains the whole Park box, yet no road touches the park. The Requests box contains the whole Depot box, yet no complaint is inside the depot. Engines use box tests as a fast <em>first filter</em>, then test the real shapes. Never stop at the first filter.</p></div>
    <div class="card"><h4 style="margin-top:0">Boxes hide shape</h4><p>The park’s box spans the gap between its parts (only 29 % of the box is park). The depot’s box ignores its hole. A diagonal road’s box is mostly empty.</p></div>
    <div class="card"><h4 style="margin-top:0">One outlier stretches the box</h4><p>One mislocated complaint at (20000, 500) would make the Requests box ten times wider; <em>Zoom To Layer</em> would show an almost empty map with a cluster in one corner — a classic first sign of a data error (Chapter 9).</p></div>
  </div>
  <div class="callout dev"><span class="label">Developer view</span><p>An extent is <code>min()</code>/<code>max()</code> on the x and y columns. <strong>Where the comparison stops:</strong> extents are compared <em>as rectangles</em> (do box A and box B intersect?), which makes them a good index and a bad answer — two shapes whose boxes overlap can be far apart.</p></div>

  <h2><span class="mod">3.6.2</span>The intake record: count, type, columns, rows with no shape</h2>
  <p>An <strong>intake record</strong> is a short written check you do every time a dataset arrives, before any analysis. For this chapter it has four items; later chapters add the coordinate system, provenance and quality.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Fill in the record for the town, then check it</h3>
    <p class="small">Use the fixture tables in the chapter document (or the figures on the earlier modules). Type counts as numbers; choose the shape type; type the number of rows with no shape.</p>
    <div class="table-wrap"><table class="trace" id="intake"></table></div>
    <div class="controls"><button class="btn" id="checkIntake">Check my record</button><button class="btn ghost" id="fillIntake">Show the expected values</button></div>
    <div class="result" id="intakeOut"></div>
  </div>
  <div class="callout idea"><span class="label">Why “7, of which 1 has no shape” — not “6”</span><p>A row without a shape is still a row. It is counted in the table, returned by attribute queries, and ignored by every spatial operation. Report “6 requests” and you silently dropped a live complaint. Report “7 requests near the road” and you silently included one that cannot be tested. The honest line is <strong>“7 rows, 6 located, 1 awaiting location”</strong>. Chapter 9 covers fixing P7; this chapter only asks you to <em>find and record</em> it.</p></div>
  <details class="reveal"><summary>Reading count, type, extent and empty shapes in ArcGIS Pro and QGIS (from the official documentation; not execution-tested)</summary>
    <p><strong>ArcGIS Pro (3.7 documentation).</strong> <em>Count:</em> the attribute-table footer (“n of m selected”), or the <em>Get Count</em> tool — clear any selection first, because with a selection “only the selected records will be counted”; an active definition query also limits the layer, so count the dataset from the <em>Catalog</em> pane if in doubt. <em>Extent and source:</em> Layer Properties (right-click ▸ Properties) ▸ <em>Source</em> tab, which shows “the layer’s extent, spatial reference, domain, resolution, and tolerance”. <em>Verify in your installed release</em> where the geometry type is displayed. <em>Columns:</em> the Fields view. <em>Empty shapes:</em> run <em>Check Geometry</em>; its report lists “Null geometry” among the problems — or select everything on the map with a box and compare the selected count with the table total.</p>
    <p><strong>QGIS 3.40.</strong> Layer Properties ▸ <em>Information</em> tab shows “geometry type, data source encoding, extent, feature count” and the fields. The attribute table title bar shows Features Total / Filtered / Selected. For empty shapes, compare <em>Show Features Visible On Map</em> (after zooming to the layer) with <em>Show All Features</em>, or use an expression filter that tests for an empty/null geometry (verify the exact function name in your release’s expression help).</p>
  </details>
  <div class="quiz" data-answer="3" data-fb="A definition query would filter the table as well as the map, so it cannot explain a table of 120 and a map of 118. The other three can: two rows with no shape (check with Check Geometry or a null-geometry test); two dots drawn exactly on top of others (box-select and count); two dots hidden by drawing order or a switched-off symbol class (move the layer to the top).">
    <div class="q">A dataset’s table says 120 rows; Zoom To Layer shows 118 dots and nothing else. Which of these is NOT a possible explanation?</div>
    <div class="opts">
      <button class="opt">Two rows have no shape</button>
      <button class="opt">Two dots sit exactly on top of two others</button>
      <button class="opt">Two dots are covered by a layer drawn above them</button>
      <button class="opt">A definition query on the layer hides two rows</button>
    </div><div class="fb"></div>
  </div>

  <h2><span class="mod">3.6.3</span>Predict what changes — then act on a scratch copy</h2>
  <p>Two actions look similar on screen and are opposite in effect:</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Action</th><th>Example</th><th>Stored rows that change</th><th>Undo how?</th></tr></thead>
    <tbody>
      <tr><td><strong>Presentation change</strong></td><td>Circles → squares; add labels; set a filter; reorder layers; hide a layer</td><td class="yes">None</td><td>Change the setting back</td></tr>
      <tr><td><strong>Geometry edit</strong></td><td>Move P1 from (200, 200) to (200, 300)</td><td class="no">One row — P1’s shape</td><td>Edit again, or don’t save the edit</td></tr>
      <tr><td><strong>Attribute edit</strong></td><td>Set P2’s status to Resolved</td><td class="no">One row, one field</td><td>Same</td></tr>
      <tr><td><strong>Delete</strong></td><td>Delete P6</td><td class="no">One row removed; 7 → 6</td><td>Don’t save, or restore from backup</td></tr>
    </tbody></table></div>
  <p>Editing is the one action in this chapter that can damage source data — so practise on a <strong>disposable copy</strong>, named so nobody mistakes it for the source (<code>Requests_scratch_2026-09-19</code>). And both desktop tools hold edits in memory until you <em>save the edits</em>: ArcGIS Pro’s <em>Save</em> “saves all edits you made since the last time you saved”, <em>Discard</em> “rolls back all edits”; QGIS: “any changes remain in the memory of QGIS … not committed/saved immediately to the data source” until <em>Save Layer Edits</em>. Saving the <em>project</em> is a different action from saving <em>edits</em>.</p>
  <div class="try predict">
    <span class="tag">Try it</span>
    <h3>Step 1 — predict. Step 2 — do it. Step 3 — compare.</h3>
    <p>We will make a scratch copy of Requests and move <strong>P1</strong> from (200, 200) to (200, 300). Before that, write your predictions:</p>
    <div id="predForm"></div>
    <div class="controls" style="margin-top:.8rem"><button class="btn accent" id="doMove">Copy the dataset and move P1 on the copy</button><button class="btn ghost" id="resetMove">Reset</button></div>
    <div class="two-col">
      <figure class="map-fig" id="origFig"></figure>
      <figure class="map-fig" id="scratchFig"></figure>
    </div>
    <div class="result" id="predOut">Make your five predictions first, then press the button.</div>
  </div>
  <div class="callout warn"><span class="label">Misconception</span><p>“I only nudged the point a little, so nothing important changed.” The nudge moved P5 off the shared boundary and into Ward B — and every boundary-inclusive report from Chapter 1 now has a different answer. Small edits are still edits.</p></div>
  <div class="callout dev"><span class="label">Developer view</span><p>A presentation change is a change to a view-model or a stylesheet; a geometry edit is an <code>UPDATE</code> inside an open transaction, committed on Save. <strong>Where the comparison stops:</strong> GIS keeps the “transaction” open across many operations and a whole session — an unsaved move stays pending while you do other work, and Save commits <em>all</em> pending edits on all layers in the map, not only the one you were thinking about.</p></div>

  <div class="callout note"><span class="label">Comprehension check (write it down)</span><p>Before each action, say how many rows of the <em>original</em> Requests dataset change: (a) setting the filter status = Open on its layer; (b) removing the layer from the map; (c) moving P5 on a scratch copy and saving; (d) moving P5 on the original layer and then clicking Discard.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  /* 3.6.1 extent explorer */
  const EX = {
    wards: { vis: ["wards", "roads"], note: "The box equals the union of the two squares exactly — a coincidence of squares." },
    roads: { vis: ["wards", "roads"], note: "Almost the whole town, although roads occupy zero area. The box contains the whole Park box, yet no road touches the park." },
    requests: { vis: ["wards", "roads", "requests"], note: "P7 has no shape and does not contribute. The box extends past the wards because of P6." },
    assets: { vis: ["wards", "roads", "assets"], note: "Four points; the box runs from SL-0113 in the south-west to TR-0301 outside Ward B." },
    park: { vis: ["wards", "park"], note: "One feature, two parts. Occupied area 40,000 m² = 29 % of the box; the gap between the parts is inside the extent.", occ: 40000 },
    depot: { vis: ["wards", "depot", "assets"], note: "The box area equals the outer ring (40,000 m²); the polygon area is 30,000 m² because of the hole.", occ: 30000 },
    R2: { vis: ["wards", "roads"], note: "A bent road’s box is mostly empty. The point (600, 300) is inside this box but 100 m from the road." },
    schoolPt: { vis: ["wards", "schoolPt"], note: "A single point’s extent is a zero-size box. “Zoom to layer” on it needs a minimum scale — expect the software to pick one." }
  };
  function showExt(k) {
    document.querySelectorAll("#extPick .btn").forEach(b => b.setAttribute("aria-pressed", b.dataset.k === k));
    const e = layerExtent(k), w = e.xmax - e.xmin, h = e.ymax - e.ymin, box = w * h;
    renderTown(document.getElementById("extFig"), { visible: EX[k].vis, wardStyle: "outline", extentOf: k, caption: `Dashed red box = extent of “${k}”.` });
    document.getElementById("extTiles").innerHTML = `<div class="tile"><div class="v">${e.xmin}</div><div class="l">x-min</div></div><div class="tile"><div class="v">${e.ymin}</div><div class="l">y-min</div></div><div class="tile"><div class="v">${e.xmax}</div><div class="l">x-max</div></div><div class="tile"><div class="v">${e.ymax}</div><div class="l">y-max</div></div><div class="tile"><div class="v">${fmtN(w)} × ${fmtN(h)}</div><div class="l">box size (m)</div></div>` + (EX[k].occ ? `<div class="tile warn"><div class="v">${Math.round(EX[k].occ / box * 100)} %</div><div class="l">of the box is actually the shape</div></div>` : "");
    document.getElementById("extOut").innerHTML = EX[k].note;
  }
  document.getElementById("extPick").addEventListener("click", e => { const b = e.target.closest(".btn"); if (b) showExt(b.dataset.k); });
  showExt("park");

  /* 3.6.2 intake record */
  const DS = [
    ["Wards", 2, "Polygon", 0], ["Roads", 3, "Line", 0], ["Requests", 7, "Point", 1], ["Assets", 4, "Point", 0],
    ["Schools_point", 1, "Point", 0], ["Schools_footprint", 1, "Polygon", 0], ["Parks", 1, "Polygon", 0], ["Depot", 1, "Polygon", 0],
    ["Wards_as_points", 2, "Point", 0], ["Inspections", 3, "No shapes (table only)", "n/a"]
  ];
  const TYPES = ["—", "Point", "Line", "Polygon", "No shapes (table only)"];
  const KEY = "gis-ch3-intake"; const saved = JSON.parse(localStorage.getItem(KEY) || "{}");
  document.getElementById("intake").innerHTML = `<thead><tr><th>Dataset</th><th>Feature count</th><th>Shape type</th><th>Rows with no shape</th><th></th></tr></thead><tbody>` + DS.map((d, i) => `<tr><td>${d[0]}</td><td><input type="text" data-k="c${i}" size="4" value="${saved["c" + i] || ""}"></td><td><select data-k="t${i}">${TYPES.map(t => `<option ${saved["t" + i] === t ? "selected" : ""}>${t}</option>`).join("")}</select></td><td><input type="text" data-k="e${i}" size="4" value="${saved["e" + i] || ""}"></td><td class="mono" id="r${i}"></td></tr>`).join("") + "</tbody>";
  function saveIntake() { const o = {}; document.querySelectorAll("#intake [data-k]").forEach(f => o[f.dataset.k] = f.value); localStorage.setItem(KEY, JSON.stringify(o)); }
  document.getElementById("intake").addEventListener("change", saveIntake);
  document.getElementById("checkIntake").addEventListener("click", () => {
    let ok = 0;
    DS.forEach((d, i) => {
      const c = document.querySelector(`[data-k="c${i}"]`).value.trim(), t = document.querySelector(`[data-k="t${i}"]`).value, e = document.querySelector(`[data-k="e${i}"]`).value.trim().toLowerCase();
      const good = c === String(d[1]) && t === d[2] && (e === String(d[3]).toLowerCase() || (d[3] === "n/a" && (e === "-" || e === "—" || e === "na")));
      document.getElementById("r" + i).innerHTML = good ? '<span class="chip yes">✓</span>' : '<span class="chip no">✗</span>';
      document.getElementById("r" + i).parentElement.className = good ? "ok" : "bad";
      if (good) ok++;
    });
    document.getElementById("intakeOut").innerHTML = `<strong>${ok} of ${DS.length} rows correct.</strong> ` + (ok === DS.length ? "This is the record you would attach to the dataset before analysis." : "Common slips: Requests is 7 (not 6) with 1 empty; Parks is 1 row even though it has two parts; Inspections has no shape type at all — write “n/a” for the last column.");
  });
  document.getElementById("fillIntake").addEventListener("click", () => { DS.forEach((d, i) => { document.querySelector(`[data-k="c${i}"]`).value = d[1]; document.querySelector(`[data-k="t${i}"]`).value = d[2]; document.querySelector(`[data-k="e${i}"]`).value = d[3]; }); saveIntake(); document.getElementById("intakeOut").innerHTML = "Expected values filled in. Compare them with what you had."; });

  /* 3.6.3 predict then move */
  const PRED = [
    { q: "Rows in the scratch copy after the move", opts: ["6", "7", "8"], a: "7", why: "moving changes a shape, not the row count" },
    { q: "P1’s distance to Road R1 on the copy", opts: ["300 m", "200 m", "100 m"], a: "200 m", why: "|300 − 500| = 200" },
    { q: "Is P1 still strictly inside Ward A on the copy?", opts: ["yes", "no"], a: "yes", why: "(200, 300) is inside (0–1000, 0–1000)" },
    { q: "P1’s position in the ORIGINAL dataset", opts: ["(200, 200)", "(200, 300)"], a: "(200, 200)", why: "the copy is a separate dataset" },
    { q: "Rows shown by a layer “High priority” on the copy", opts: ["changes", "no change (P2, P3, P6)"], a: "no change (P2, P3, P6)", why: "P1 is Medium priority; moving it changes no attribute" }
  ];
  document.getElementById("predForm").innerHTML = PRED.map((p, i) => `<div class="row"><span>${i + 1}. ${p.q}</span><select data-p="${i}"><option value="">— predict —</option>${p.opts.map(o => `<option>${o}</option>`).join("")}</select><span id="pr${i}"></span></div>`).join("");
  function drawMove(moved) {
    renderTown(document.getElementById("origFig"), { visible: ["wards", "roads", "requests"], selected: { layer: "requests", id: "P1" }, caption: "ORIGINAL Requests — P1 at (200, 200). Never edited." });
    renderTown(document.getElementById("scratchFig"), { visible: moved ? ["wards", "roads", "requests"] : ["wards", "roads"], moved: moved ? { P1: [200, 300] } : {}, selected: { layer: "requests", id: "P1" }, caption: moved ? "SCRATCH COPY Requests_scratch — P1 moved to (200, 300)." : "SCRATCH COPY — not made yet." });
  }
  drawMove(false);
  document.getElementById("doMove").addEventListener("click", () => {
    const blanks = PRED.filter((p, i) => !document.querySelector(`[data-p="${i}"]`).value).length;
    if (blanks) { document.getElementById("predOut").innerHTML = `<strong>Predict first.</strong> ${blanks} prediction${blanks > 1 ? "s are" : " is"} still blank — the whole point is to write them down <em>before</em> looking.`; return; }
    drawMove(true);
    let right = 0;
    PRED.forEach((p, i) => { const v = document.querySelector(`[data-p="${i}"]`).value; const ok = v === p.a; if (ok) right++; document.getElementById("pr" + i).innerHTML = ok ? `<span class="chip yes">✓ ${p.a}</span>` : `<span class="chip no">✗ actual: ${p.a} — ${p.why}</span>`; });
    document.getElementById("predOut").innerHTML = `<strong>${right} of 5 predictions correct.</strong> Observed on the copy: 7 rows; P1 at (200, 300), 200 m from R1, still inside Ward A; the original still has P1 at (200, 200); the High-priority layer still lists P2, P3, P6. ` + (right < 5 ? "A wrong prediction means you edited the wrong dataset, the wrong row, or expected an edit to do something it cannot — find out which before editing real data." : "Every prediction held, so you know exactly what the edit did — and did not do.") + " Now imagine pressing <em>Discard</em>: the copy returns to (200, 200) too.";
  });
  document.getElementById("resetMove").addEventListener("click", () => { PRED.forEach((p, i) => { document.querySelector(`[data-p="${i}"]`).value = ""; document.getElementById("pr" + i).innerHTML = ""; }); drawMove(false); document.getElementById("predOut").innerHTML = "Make your five predictions first, then press the button."; });
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
