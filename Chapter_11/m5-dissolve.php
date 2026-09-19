<?php $page = ['title' => '11.5 Dissolve — merge shapes by a shared value', 'chapter' => 11, 'module' => '11.5']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 11.5 · General idea, with ArcGIS Pro, QGIS and PostGIS wording</div>
    <h1>Dissolve: GROUP BY for shapes</h1>
    <p class="lead"><strong>Dissolve</strong> groups features that share the same value in a chosen field and merges each group’s shapes into one. Four wards with two zone codes become two zones. Shared edges between neighbours disappear — that is why it is called <em>dissolve</em>. Members that do not touch stay as separate <em>parts</em> of one feature.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>See the three things a dissolve decides: how many groups, what shape each gets, and what happens to the other columns.</li>
      <li>Watch one zone come out as a single rectangle and the other as one feature in <em>two pieces</em> — and decide whether that is a problem.</li>
      <li>Avoid the QGIS “first feature’s value” trap and the ArcGIS “where did my columns go?” surprise.</li></ul></div>
  </div>

  <h2><span class="mod">11.5.1</span>Three decisions inside one tool</h2>
  <p>ArcGIS Pro’s Dissolve “Aggregates features based on specified attributes.” QGIS’s Dissolve “Takes a vector layer and combines its features into new features. One or more attributes can be specified to dissolve features belonging to the same class.” In PostGIS the aggregate <code>ST_Union</code> “returns a geometry that is the union of a rowset of geometries” and is used with <code>GROUP BY</code> exactly like <code>SUM()</code>.</p>
  <div class="grid-3">
    <div class="card"><h3 style="margin-top:0">1 · The grouping field</h3><p>One output feature per distinct value. With <em>no</em> field, ArcGIS Pro “will dissolve all features together” into one feature.</p></div>
    <div class="card"><h3 style="margin-top:0">2 · The geometry</h3><p>Each group’s shapes are unioned. Neighbours merge; non-touching members become one <strong>multipart</strong> feature. ArcGIS Pro’s <em>Create multipart features</em> is on by default; unchecked, “Individual features will be created for each part.” QGIS: “All output geometries will be converted to multi geometries”, with a <em>Keep disjoint features separate</em> option.</p></div>
    <div class="card"><h3 style="margin-top:0">3 · The other columns</h3><p>ArcGIS Pro <strong>drops</strong> every field that is not a dissolve field or a requested statistic; statistics are named <code>SUM_households</code> and so on, and “Null values are excluded from all statistical calculations”. QGIS <strong>keeps</strong> the columns but fills them with “the ones of the first input feature that happens to be processed” — arbitrary for everything except the dissolve field itself.</p></div>
  </div>
  <p><strong>Dissolve is not “merge” or “append”.</strong> Appending two datasets stacks their records: two wards in, two records out, shapes untouched. Dissolving two wards with the same zone gives <em>one</em> record and <em>one</em> merged shape. If a colleague says “I merged the wards”, ask which they mean.</p>
  <div class="callout dev"><span class="label">Developer view</span><p><code>SELECT zone, ST_Union(geom), SUM(households), COUNT(*) FROM wards GROUP BY zone</code> is a faithful description. The picture stops at the geometry: <code>SUM</code> of numbers is one number, but the union of shapes can be one polygon <em>or</em> several pieces — and a business rule like “a zone is one connected area” is not enforced by the operation.</p></div>

  <h2><span class="mod">11.5.2</span>Wards to zones — with statistics and a two-piece result</h2>
  <p><strong>Question.</strong> The works department manages by <em>zone</em>, not ward. Produce one feature per zone with the number of wards, total households and area. <strong>Inputs.</strong> Wards A, B, C, D (chapter home page). Dissolve field <code>zone</code>; statistics <code>households</code> SUM and <code>ward</code> COUNT; area recomputed from the output shape — never summed from a stored field (11.4.3).</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Press Dissolve, then read the table two ways</h3>
    <div class="controls">
      <button class="btn primary" id="doDis">Dissolve by zone →</button>
      <button class="btn ghost" id="undo">Reset</button>
      <label><input type="checkbox" id="multipart" checked> allow multipart features</label>
    </div>
    <figure class="map-fig" id="disFig"></figure>
    <div class="tabs"><button>ArcGIS Pro view (statistics fields)</button><button>QGIS view (first-feature values)</button></div>
    <div class="tabpanel"><div class="table-wrap"><table><thead><tr><th>zone</th><th>parts</th><th>COUNT_ward</th><th>SUM_households</th><th>area (recomputed)</th></tr></thead><tbody id="esriRows"></tbody></table></div><p class="small">Fields not listed as dissolve fields or statistics are gone — that is expected.</p></div>
    <div class="tabpanel"><div class="table-wrap"><table><thead><tr><th>zone</th><th>parts</th><th>ward</th><th>households</th><th>area (recomputed)</th></tr></thead><tbody id="qgisRows"></tbody></table></div><p class="small">The <code>ward</code> and <code>households</code> columns survive but hold the <em>first processed feature’s</em> values — not a total. Do not read them as meaningful.</p></div>
    <div class="result" id="disOut">Four wards, two zone codes. Predict: how many features, how many pieces, what totals?</div>
  </div>
  <h3>Reading the result</h3>
  <ul>
    <li><strong>Z1 = A + C.</strong> A (0–1000 × 0–1000) and C (0–1000 × 1000–1500) share the edge y = 1000, so the union is <strong>one single rectangle</strong> 0–1000 × 0–1500. Two wards, 1,200 + 400 = 1,600 households, 1,500,000 m².</li>
    <li><strong>Z2 = B + D.</strong> B (1000–2000 × 0–1000) and D (2500–3000 × 0–500) do not touch, so the union is <strong>one feature with two parts</strong>. Two wards, 900 + 150 = 1,050 households, 1,000,000 + 250,000 = 1,250,000 m².</li>
  </ul>
  <p><strong>Is a two-part zone a problem?</strong> If the department’s rule is “a zone is one connected area”, Z2 breaks it — but the dissolve did not fail; it did what it was told. Whether to split Z2 (untick multipart → two Z2 records, which breaks “one row per zone”) or to report “one zone, two parts” is a <em>business</em> decision to write in the worksheet. Do not assume every group must be one connected area; do not assume the opposite either. <strong>Look.</strong></p>
  <p><strong>Check the grouping field against the requirement.</strong> Dissolving by <code>zone</code> answers “per zone”. If the requirement was “per maintenance <em>crew</em>” and crews map one-to-one to zones, fine. If two crews share Z1, dissolving by zone has thrown that away — the worksheet is wrong at step 1, not the tool. Read the requirement’s noun and make it the dissolve field.</p>
  <div class="callout warn"><span class="label">A statistic that misleads</span><p>MEAN of <code>households</code> per zone (Z1: 800, Z2: 525) is a mean <em>per ward</em>, not a household density; it depends on how the wards happened to be drawn. Chapter 12 returns to totals, rates and density. For now: never report a MEAN without saying what unit is being averaged.</p></div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“Dissolve keeps all the attributes.”</em> ArcGIS Pro keeps only the dissolve fields and the statistics you asked for. QGIS keeps the columns but with first-feature values. Someone who dissolves by zone in QGIS and reads <code>households = 1200</code> as Z1’s total has read Ward A’s value (or C’s — whichever came first).</p></div>

  <div class="quiz" data-answer="0" data-fb="One output feature. A, B and C are all connected (A–B share x = 1000; A–C share y = 1000), so they union into one part; D is detached — two parts in total. COUNT_ward = 4; SUM_households = 1200 + 900 + 400 + 150 = 2,650.">
    <div class="q">Quick check 11.5. You dissolve wards A–D with <em>no</em> dissolve field, statistics households SUM and ward COUNT. What comes out?</div>
    <div class="opts"><button class="opt">One feature with two parts; COUNT 4; SUM 2,650.</button><button class="opt">Four features (nothing to group by); SUM 2,650 each.</button><button class="opt">One feature with one part; COUNT 4; SUM 2,650.</button><button class="opt">Two features (Z1 and Z2) — the zone field is used automatically.</button></div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const fig = document.getElementById("disFig"); let dissolved = false;
  const zones = { Z1: { rects: [{ x1: 0, y1: 0, x2: 1000, y2: 1500 }], members: ["A", "C"] }, Z2: { rects: [{ x1: 1000, y1: 0, x2: 2000, y2: 1000 }, { x1: 2500, y1: 0, x2: 3000, y2: 500 }], members: ["B", "D"] } };
  function draw() {
    const mp = document.getElementById("multipart").checked;
    const m = gridMap(fig, { extent: { x1: -200, y1: -250, x2: 3200, y2: 1700 }, caption: dissolved ? `Dissolved by zone. Z1 is one rectangle; Z2 is ${mp ? "one feature with two parts" : "two separate features (multipart not allowed)"}.` : "Four wards coloured by zone code. Z1 = A, C (green). Z2 = B, D (yellow). Shared edges still drawn." });
    if (!dissolved) { WARDS.forEach(w => drawWards(m, [w], w.zone === "Z1" ? "zone1" : "zone2")); }
    else Object.entries(zones).forEach(([z, o]) => o.rects.forEach((r, i) => { mk("rect", { x: m.X(r.x1), y: m.Y(r.y2), width: m.S(r.x2 - r.x1), height: m.S(r.y2 - r.y1), class: "ward merged " + (z === "Z1" ? "zone1" : "zone2") }, m.svg); stext(m.svg, m.X(r.x1) + 18, m.Y(r.y2) + 58, z + (o.rects.length > 1 ? (mp ? ` (part ${i + 1})` : ` #${i + 1}`) : ""), "ward-label"); }));
    const e = document.getElementById("esriRows"), q = document.getElementById("qgisRows"), out = document.getElementById("disOut");
    if (!dissolved) { e.innerHTML = q.innerHTML = `<tr><td colspan="5" class="small">Press Dissolve.</td></tr>`; return; }
    const rowsE = [], rowsQ = [];
    Object.entries(zones).forEach(([z, o]) => {
      const mem = o.members.map(wardById), hh = mem.reduce((s, w) => s + w.households, 0), area = o.rects.reduce((s, r) => s + (r.x2 - r.x1) * (r.y2 - r.y1), 0);
      if (mp || o.rects.length === 1) { rowsE.push({ cells: [z, o.rects.length, mem.length, fmt(hh), fmtM2(area)] }); rowsQ.push({ cells: [z, o.rects.length, mem[0].id + " ← first feature only", fmt(mem[0].households) + " ← first feature only", fmtM2(area)] }); }
      else o.rects.forEach((r, i) => { rowsE.push({ cells: [z, "1 (feature " + (i + 1) + ")", "?", "?", fmtM2((r.x2 - r.x1) * (r.y2 - r.y1))] }); rowsQ.push({ cells: [z, "1", "?", "?", fmtM2((r.x2 - r.x1) * (r.y2 - r.y1))] }); });
    });
    tableRows(e, rowsE); tableRows(q, rowsQ);
    out.innerHTML = mp ? `<strong>2 features</strong> (one per zone). Z1: 1 part, 2 wards, <strong>1,600</strong> households, 1,500,000 m². Z2: <strong>2 parts</strong>, 2 wards, <strong>1,050</strong> households, 1,250,000 m². The QGIS view shows Z1 “households = 1200” — that is Ward A’s value, not the zone’s.`
                       : `<strong>3 features</strong>: Z1, and Z2 split into its two pieces. Now there are two rows for Z2 — which breaks “one row per zone”, and the statistics per piece would need re-deciding (marked ?). Either choice is a business decision; write it down.`;
  }
  document.getElementById("doDis").addEventListener("click", () => { dissolved = true; draw(); });
  document.getElementById("undo").addEventListener("click", () => { dissolved = false; draw(); });
  document.getElementById("multipart").addEventListener("change", draw);
  draw();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
