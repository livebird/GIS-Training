<?php $page = ['title' => '3.8 Independent check and progression gate', 'chapter' => 3, 'module' => '3.8']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 3.8 · Assessment</div>
    <h1>Independent check and progression gate</h1>
    <p class="lead">This is what you submit to your instructor. There are no answers on this page — the instructor holds the answer key.</p>
    <div class="outcomes"><h4>Submit</h4>
      <ul><li>The module 3.7 deliverables A–F (a prerequisite for this gate).</li>
      <li>Written answers to Q1–Q6 and S1–S2.</li>
      <li>The practical task on the Campus C-3 data, with a note on what was done in software and what on paper.</li>
      <li>Be available for the two-minute oral check.</li></ul></div>
  </div>
  <div class="callout note"><span class="label">Before you start</span><p>Answer every item under its stated assumptions. If you think an item is under-specified, say what is missing rather than guessing. The quick checks in modules 3.1–3.6 were practice; these are marked.</p></div>

  <h2><span class="mod">3.8.1</span>Concept questions (30 marks, 5 each)</h2>
  <div class="card"><p><strong>Q1.</strong> <span class="pill">3.1 · LO1</span><br>The tree register stores each tree as a point. A new requirement: “report the canopy area shaded by each tree”. Which statement is correct? (a) Add a <code>canopy_area_m2</code> column to the point layer; a point with an area value is enough to map shaded ground. (b) Convert the point layer to a polygon layer; the points are then no longer needed. (c) Points can answer count, location and nearness questions; a canopy <em>area on the map</em> needs a separate polygon layer captured for that purpose, while a column is enough for table totals. (d) Store each tree as a polygon and derive the point later, since polygons hold more information.</p></div>
  <div class="card"><p><strong>Q2.</strong> <span class="pill">3.2 · LO2</span><br>Road R2 is drawn with a 3 pt stroke at 1 : 10,000. (i) How wide on the ground does the stroke <em>appear</em> (1 pt ≈ 0.353 mm)? (ii) What is R2’s stored width, and where is that fact held? (iii) Name one question for which the line is enough and one for which an area is necessary. Show working.</p></div>
  <div class="card"><p><strong>Q3.</strong> <span class="pill">3.3 · LO3</span><br>A GeoJSON <code>Polygon</code> has two rings. The first runs clockwise, the second counter-clockwise. Under RFC 7946: (a) valid — ring order decides which is the hole; direction is only advice. (b) invalid — exterior rings MUST be counter-clockwise and holes clockwise. (c) valid — GeoJSON adopted Esri’s clockwise convention. (d) invalid — a polygon cannot have two rings.</p></div>
  <div class="card"><p><strong>Q4.</strong> <span class="pill">3.4 · LO4</span><br>The Assets table has <code>OBJECTID</code>, <code>asset_id</code>, <code>asset_type</code>, <code>condition</code>. A field app labels each asset with <code>asset_type</code>. A colleague proposes making <code>asset_type</code> the key that links inspections to assets “because that is what the crew sees”. In no more than five sentences, explain why neither <code>asset_type</code> nor <code>OBJECTID</code> should be that key, and which column should.</p></div>
  <div class="card"><p><strong>Q5.</strong> <span class="pill">3.5 · LO5</span><br>A map has layers L1 and L2, both on feature class Roads (3 rows); L2 has the definition query <code>width_m >= 10</code>. A user deletes L2 from the map. Afterwards Roads has: (a) 1 row. (b) 2 rows. (c) 3 rows — nothing was deleted, because a layer references the dataset. (d) It depends on whether the project was saved.</p></div>
  <div class="card"><p><strong>Q6.</strong> <span class="pill">3.6 · LO6</span><br>A layer’s extent is x 0 … 5000, y 0 … 5000, yet 39 of its 40 features lie within x 0 … 1000, y 0 … 1000. (i) What does that tell you about the remaining feature? (ii) State one consequence for <em>Zoom To Layer</em> and one for any tool that uses extents as a first filter. (iii) Which intake item (module 3.6.2) would you check next?</p></div>

  <h2><span class="mod">3.8.2</span>Scenario questions (25 marks)</h2>
  <div class="card"><p><strong>S1 (12 marks).</strong> <span class="pill">3.1, 3.2, 3.3 · LO1–LO3</span><br>A courier company asks you to model three things for a delivery-planning map: <strong>a river</strong> that crosses the service area, <strong>a delivery stop</strong> at a customer’s gate, and <strong>a restricted area</strong> where vans may not enter (a military compound with a public road through it, so the restricted land is in two separate pieces). For each, state (a) the shape type, (b) the scale and purpose assumption that justifies it, (c) one question the choice can answer and one it cannot, and (d) for the restricted area, whether you would store one multipart feature or two single-part features, with a reason based on the table in module 3.3.1. Then say what would change in your river choice if the purpose were “flood-risk area per property” instead of “route planning”.</p></div>
  <div class="card"><p><strong>S2 (13 marks).</strong> <span class="pill">3.3, 3.5 · LO3, LO5</span><br>A colleague writes: “I fixed the town data. (1) The park showed as one row but is obviously two parks, so I split it into two rows and gave both the ID PK-01. (2) The depot had a strange square inside it, so I deleted that inner ring to make it a clean square. (3) The Wards layer was covering the requests, so I removed the Wards layer from the map. (4) The requests table had a row with no location, so I deleted it.” For each of (1)–(4), state whether data was changed or only presentation, whether the change was justified under this chapter’s rules, and what should have been done instead if not. Refer to the fixture values (areas, counts, identifiers) where relevant.</p></div>

  <h2><span class="mod">3.8.3</span>Independent practical task (35 marks) — unfamiliar inputs</h2>
  <div class="card">
    <p><span class="synthetic">Campus C-3 — made-up flat grid, metres, (x, y), no coordinate reference system, deliberately different from the lab.</span> Build it in software from the files below (or work on paper) and answer the tasks.</p>
    <div id="cdl"></div>
    <details class="reveal"><summary>File contents</summary><div id="cview"></div></details>
    <figure class="map-fig" id="campusFig" style="max-width:620px;margin:1rem auto"></figure>
    <ol>
      <li>Produce the intake record (count, shape type, columns, extent, rows with no shape) for all five datasets.</li>
      <li>For B1, LK1 and W1, name the structural feature (multipart / hole / closed line) and give the area or length by hand, showing working.</li>
      <li>Say which bins are inside a building, which bin is inside the lake polygon, and which bin is on the island — with a one-sentence reason for K2.</li>
      <li>Give the extent of B1 and the fraction of that extent actually occupied by the building.</li>
      <li>Identify one dataset whose representation is <em>unsuitable</em> for “how long is the shoreline walk around the lake?”, and say what representation would answer it.</li>
      <li>Build two layers on <code>bins</code> — one coloured by <code>bin_type</code>, one filtered to <code>bin_type = 'General'</code> — and report each layer’s row count and dot count, and the source count. State the invariant.</li>
      <li>Predict, then (if using software) verify, what changes if K5 is moved to (450, 250) on a scratch copy.</li>
    </ol>
    <p>Submit the intake record, the working for items 2–4, the layer definitions and counts for item 6, the prediction log for item 7, and a statement of what was done in software versus on paper.</p>
  </div>

  <h2><span class="mod">3.8.4</span>Oral explanation (10 marks) — two minutes, no notes</h2>
  <div class="card"><p>Choose either the <em>Parks</em> or the <em>Depot</em> dataset from the lab. Explain <strong>what information it represents, what it omits, and what is merely styled</strong> — naming the feature count, the part or ring structure, one question it answers, one it cannot, and one presentation setting that changes how it looks without changing it. Finish by naming one thing you would need to check before trusting the dataset for a real decision.</p></div>

  <h2><span class="mod">3.8.5</span>Scoring and the progression rule</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Component</th><th>Marks</th><th>What earns the marks</th></tr></thead>
    <tbody>
      <tr><td>Concept questions Q1–Q6</td><td class="mono">30</td><td>Correct answer and reasoning consistent with the chapter; arithmetic shown where asked</td></tr>
      <tr><td>Scenario S1</td><td class="mono">12</td><td>Shape type, scale/purpose assumption, can/cannot questions and a table-based multipart decision for all three objects; the purpose-change reflection</td></tr>
      <tr><td>Scenario S2</td><td class="mono">13</td><td>Each of (1)–(4) correctly classified as data vs presentation and justified vs not, with the correct alternative action</td></tr>
      <tr><td>Practical — intake and structure (items 1–2)</td><td class="mono">12</td><td>Counts, types, extents, the empty-shape row (K4); areas/lengths with working</td></tr>
      <tr><td>Practical — containment, extent, unsuitability (items 3–5)</td><td class="mono">12</td><td>K2 reasoned from the hole; extent fraction computed; unsuitable representation named with a suitable alternative</td></tr>
      <tr><td>Practical — two presentations and prediction (items 6–7)</td><td class="mono">11</td><td>Row/dot/source counts correct; invariant stated; predictions written before observation</td></tr>
      <tr><td>Oral explanation</td><td class="mono">10</td><td>Represented / omitted / styled all addressed; counts and structure correct; a genuine check named</td></tr>
    </tbody></table></div>
  <div class="callout warn"><span class="label">Pass rule</span><p><strong>80 marks out of 100, and no critical misconception.</strong> For this chapter these are: (1) treating a layer’s filter, colours or removal as a change to, or deletion of, the dataset; (2) treating a closed line as an area, or a bounding box as the shape; (3) reporting a feature count that silently drops or silently includes rows with no shape; (4) using a map label or a system ObjectID as the business identifier for linking. Any of these means remediation and a fresh exercise, whatever the total. A learner who cannot say what a dataset <em>omits</em> in the oral item retakes it.</p></div>
  <div class="callout idea"><span class="label">Progression rule</span><p>You progress when you can explain which information a dataset <em>represents</em>, which it <em>omits</em>, and which is <em>merely styled</em>.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const FILES = {
    "buildings.csv": 'bldg_id,name,wkt\nB1,Library,"MULTIPOLYGON(((100 100,200 100,200 180,100 180,100 100)),((220 100,300 100,300 180,220 180,220 100)))"\nB2,Laboratory,"POLYGON((400 100,500 100,500 200,400 200,400 100))"\n',
    "lake.csv": 'lake_id,name,wkt\nLK1,Campus lake,"POLYGON((100 300,400 300,400 500,100 500,100 300),(200 350,300 350,300 450,200 450,200 350))"\n',
    "island.csv": 'island_id,wkt\nI1,"POLYGON((200 350,300 350,300 450,200 450,200 350))"\n',
    "paths.csv": 'path_id,wkt\nW1,"LINESTRING(420 300,480 300,480 360,420 360,420 300)"\nW2,"LINESTRING(0 250,500 250)"\n',
    "bins.csv": 'bin_id,x,y,bin_type\nK1,150,140,Recycling\nK2,250,400,General\nK3,450,150,General\nK4,,,Recycling\nK5,600,600,General\n'
  };
  document.getElementById("cdl").innerHTML = Object.keys(FILES).map(f => `<a class="btn small dl" download="${f}" href="data:text/csv;charset=utf-8,${encodeURIComponent(FILES[f])}">⬇ ${f}</a>`).join("");
  document.getElementById("cview").innerHTML = Object.keys(FILES).map(f => `<h4>${f}</h4><pre class="code">${FILES[f]}</pre>`).join("");
  /* campus sketch — drawn directly, deliberately without answers (no labels of inside/outside) */
  const svg = mkEl("svg", { viewBox: "-60 -60 760 740", role: "img", "aria-label": "Campus C-3 sketch: two buildings, a lake with an island, two paths and four located bins." });
  const S = 1, Y = y => 620 - y;
  const poly = (r, cls) => mkEl("polygon", { points: r.map(p => `${p[0]},${Y(p[1])}`).join(" "), class: cls }, svg);
  mkEl("path", { d: "M 100 " + Y(300) + " L 400 " + Y(300) + " L 400 " + Y(500) + " L 100 " + Y(500) + " Z M 200 " + Y(350) + " L 300 " + Y(350) + " L 300 " + Y(450) + " L 200 " + Y(450) + " Z", class: "poly", style: "fill:rgba(91,126,163,.35);stroke:#5b7ea3;fill-rule:evenodd" }, svg);
  poly([[200, 350], [300, 350], [300, 450], [200, 450]], "poly park");
  poly([[100, 100], [200, 100], [200, 180], [100, 180]], "poly school"); poly([[220, 100], [300, 100], [300, 180], [220, 180]], "poly school"); poly([[400, 100], [500, 100], [500, 200], [400, 200]], "poly school");
  mkEl("polyline", { points: [[420, 300], [480, 300], [480, 360], [420, 360], [420, 300]].map(p => `${p[0]},${Y(p[1])}`).join(" "), class: "line", style: "stroke-width:5" }, svg);
  mkEl("polyline", { points: `0,${Y(250)} 500,${Y(250)}`, class: "line", style: "stroke-width:5" }, svg);
  [["K1", 150, 140], ["K2", 250, 400], ["K3", 450, 150], ["K5", 600, 600]].forEach(k => { mkEl("circle", { cx: k[1], cy: Y(k[2]), r: 9, class: "req" }, svg); const t = mkEl("text", { x: k[1] + 12, y: Y(k[2]) - 8, class: "vlabel", style: "font-size:16px" }, svg); t.textContent = k[0]; });
  [["B1 Library", 110, 190], ["B2 Laboratory", 405, 210], ["LK1 lake", 105, 510], ["I1 island", 205, 460], ["W1", 425, 372], ["W2", 5, 262]].forEach(l => { const t = mkEl("text", { x: l[1], y: Y(l[2]), class: "tag", style: "font-size:15px" }, svg); t.textContent = l[0]; });
  for (let x = 0; x <= 600; x += 100) { const t = mkEl("text", { x, y: 655, class: "axis-label", "text-anchor": "middle", style: "font-size:14px" }, svg); t.textContent = x; }
  for (let y = 0; y <= 600; y += 100) { const t = mkEl("text", { x: -15, y: Y(y) + 5, class: "axis-label", "text-anchor": "end", style: "font-size:14px" }, svg); t.textContent = y; }
  const f = document.getElementById("campusFig"); f.appendChild(svg); const c = document.createElement("figcaption"); c.textContent = "Campus C-3 (schematic; K4 has no location and is not drawn). Work out the answers yourself — the sketch shows only the data."; f.appendChild(c);
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
