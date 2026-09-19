<?php $page = ['title' => '11.4 Intersect — build the overlap', 'chapter' => 11, 'module' => '11.4']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 11.4 · General idea, with ArcGIS Pro, QGIS and PostGIS wording</div>
    <h1>Intersect: the question asks, the overlay builds</h1>
    <p class="lead">Chapter 10 asked “does R1 <em>intersect</em> Ward A?” and got <strong>yes</strong>. This module asks “what <em>is</em> the intersection of R1 and Ward A?” and gets <strong>a line from (0, 500) to (1000, 500)</strong>. Same word, two kinds of answer: a yes/no about existing records, or brand-new records made from the shared part.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Tell the <em>intersects?</em> test from the <em>intersection</em> operation, and clip from intersect.</li>
      <li>Predict the output’s geometry type (its “dimension”) and how one record splits into several.</li>
      <li>Spot the copied-number trap and choose a defensible rule instead of inventing a new total.</li></ul></div>
  </div>

  <h2><span class="mod">11.4.1</span>Predicate versus overlay — and clip versus intersect</h2>
  <p>ArcGIS Pro’s Intersect “Computes a geometric intersection of the input features. Features or portions of features that overlap in all layers or feature classes will be written to the output feature class.” QGIS’s Intersection: “Extracts the portions of features from the input layer that overlap features in the overlay layer. Features in the intersection layer are assigned the attributes of the overlapping features from both the input and overlay layers.” PostGIS’s <code>ST_Intersection</code> “Returns a geometry representing the point-set intersection of two geometries.”</p>
  <p>The important clause is at the end of the QGIS sentence: <strong>attributes from both inputs are carried</strong>. So the intersection of roads and wards tells you, for each output piece, <em>which road</em> and <em>which ward</em> — which clip could not.</p>
  <div class="table-wrap"><table>
    <thead><tr><th></th><th>Clip (11.3)</th><th>Intersect</th></tr></thead>
    <tbody>
      <tr><td><strong>Geometry kept</strong></td><td>Input parts inside the cutter</td><td>Parts common to all inputs</td></tr>
      <tr><td><strong>Attributes in output</strong></td><td>Input’s only</td><td>All inputs’ (ArcGIS Pro: <em>Attributes To Join</em> = All, All except FIDs, or Only FIDs)</td></tr>
      <tr><td><strong>Output geometry type</strong></td><td>Same as input</td><td>By default the <em>lowest dimension</em> among the inputs</td></tr>
      <tr><td><strong>Records</strong></td><td>One per input feature with a part inside</td><td>One per overlapping <em>pair</em> — records split</td></tr>
      <tr><td><strong>Use when</strong></td><td>The study area is just a boundary</td><td>You need to know <em>which</em> overlay feature each piece belongs to</td></tr>
    </tbody></table></div>
  <div class="callout dev"><span class="label">Developer view</span><p>In PostGIS people literally write <code>SELECT r.*, w.*, ST_Intersection(r.geom, w.geom) FROM roads r JOIN wards w ON ST_Intersects(r.geom, w.geom)</code>. The <code>JOIN … ON ST_Intersects</code> is the yes/no test; <code>ST_Intersection</code> in the select list is the construction. Close to exact. Where it stops: a SQL join never changes what a column <em>means</em>, but the overlay changes the geometry from “the whole road” to “this piece of it” — and every stored measurement quietly keeps its old meaning (11.4.3).</p></div>

  <h2><span class="mod">11.4.2</span>Predict the dimension and the splitting</h2>
  <p><strong>Dimension:</strong> a point is 0, a line is 1, a polygon is 2. ArcGIS Pro: “If the inputs have different geometry types, the output geometry type will default to the lowest dimension of the inputs.” Its <em>Output Type</em> parameter can force it lower: INPUT (the lowest input), LINE (“only valid if none of the inputs are points”), or POINT (“If the inputs are line or polygon, the output will be a multipoint feature class”). PostGIS warns the result “may return geometries of lower dimensionality than expected” — a line that only touches a polygon’s edge yields a point. Predict the type <em>before</em> you run; then check the output’s geometry type first.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Three intersections — predict, then reveal</h3>
    <div class="controls">
      <label><input type="radio" name="ix" value="line" checked> A · Road R1 × Wards (line × polygon)</label>
      <label><input type="radio" name="ix" value="poly"> B · R1’s 300 m buffer × Wards (polygon × polygon)</label>
      <label><input type="radio" name="ix" value="touch"> C · Ward A × Ward B (they only touch)</label>
    </div>
    <figure class="map-fig" id="ixFig"></figure>
    <div class="grid-2">
      <div class="recs" id="ixRecs"></div>
      <div class="result" id="ixOut"></div>
    </div>
  </div>
  <h3>Reading the three cases</h3>
  <ul>
    <li><strong>A — line × polygon → line.</strong> R1 passes through both wards, so it is <strong>split into two records</strong>: R1/A from (0,500) to (1000,500) and R1/B from (1000,500) to (2000,500), each 1,000 m, each carrying R1’s attributes <em>plus</em> a ward code. Their lengths add up to 2,000 m only because R1 lies entirely inside the two wards; a road that stuck out would lose its outside part.</li>
    <li><strong>B — polygon × polygon → polygon.</strong> The buffer’s two half-circle ends (x &lt; 0 and x &gt; 2000) overlap no ward and disappear. What remains is two 1000 × 600 rectangles — 600,000 m² each, 1,200,000 m² in total — <em>less</em> than the buffer’s 1,482,743 m².</li>
    <li><strong>C — two polygons that only touch.</strong> They share an edge and no area. With the default polygon output type, expect an <strong>empty</strong> result. Ask for Output Type = LINE and you get the shared edge, 1,000 m from (1000, 0) to (1000, 1000). Chapter 10’s “touches” test said <em>yes</em>; the overlay shows <em>what</em> touches. (An instructor should confirm both behaviours in the installed version.)</li>
  </ul>
  <div class="callout note"><span class="label">Platform note — how many inputs</span><p>ArcGIS Pro’s Intersect takes a <em>list</em> of inputs; with Basic and Standard licences “the number of input feature classes or layers is limited to two”, and the page points to <strong>Pairwise Intersect</strong>, which works on pairs of features rather than all combinations. QGIS’s Intersection takes exactly one input and one overlay layer. Everything in this chapter uses two inputs.</p></div>

  <h2><span class="mod">11.4.3</span>The copied-number trap</h2>
  <p>Look again at case B. Each output piece carries <code>BUFF_DIST = 300</code>, copied from the buffer — fine, the distance is still 300 for each piece; it is a <em>label</em>. Now imagine the buffer also had a field <code>buf_area_m2 = 1482743</code>, written before the intersect. Both pieces would carry <code>buf_area_m2 = 1482743</code>. Add that column up and you get 2,965,486 m² for 1,200,000 m² of actual ground. <strong>Nothing in the tool prevents this.</strong> It did exactly what its manual says — copied the attribute values.</p>
  <p>Same trap in case A: <code>length_m = 2000</code> on R1 becomes <code>length_m = 2000</code> on <em>both</em> halves. A dashboard that sums <code>length_m</code> per ward reports 2,000 m of road in each ward — double the truth.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Pick a rule before you add anything up</h3>
    <p>Case A produced two records, both carrying <code>length_m = 2000</code>. The manager wants “road length per ward”. Which rule do you apply?</p>
    <div class="controls">
      <label><input type="radio" name="rule" value="sum"> Sum the copied <code>length_m</code></label>
      <label><input type="radio" name="rule" value="recalc" checked> Recompute length from each piece’s shape</label>
      <label><input type="radio" name="rule" value="ratio"> Split 2000 in proportion to the kept fraction (ratio policy)</label>
      <label><input type="radio" name="rule" value="label"> Treat it as a label — do not add it</label>
    </div>
    <div class="table-wrap"><table><thead><tr><th>Ward</th><th>Reported road length</th></tr></thead><tbody id="ruleRows"></tbody></table></div>
    <div class="result" id="ruleOut"></div>
  </div>
  <p>The rules, from safest to most assumption-laden:</p>
  <ol>
    <li><strong>Recompute from geometry.</strong> Length and area belong to the output shape; recalculate them. Always correct for geometric measures, no assumption needed.</li>
    <li><strong>Weighted allocation (ratio policy).</strong> For a quantity assumed to be spread evenly over the original feature — a road’s maintenance budget per metre; a ward’s population <em>only if</em> you accept uniform density — allocate in proportion to the kept fraction. ArcGIS Pro’s <em>Use Ratio Policy</em> on layer fields does this; the uniformity assumption must be written in the worksheet.</li>
    <li><strong>Do not sum.</strong> Names, codes, <code>BUFF_DIST</code> — these are labels, not quantities. They are correctly copied and must never be added.</li>
    <li><strong>Count distinct source features.</strong> If the question was “how many roads touch Ward A?”, count distinct road IDs in the output, not records.</li>
  </ol>
  <div class="callout warn"><span class="label">Not defensible</span><p>Inventing a new total by summing copied values and reporting it. Treat any summed column on an intersect output as suspect until the worksheet names the rule that justifies it.</p></div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“Intersect and clip are the same when the cutter is one polygon.”</em> The kept shape is the same. The outputs differ in attributes (intersect adds the polygon’s fields) and, when the overlay has several polygons, in record structure (intersect splits by polygon).</p></div>

  <div class="quiz" data-answer="1" data-fb="R6 (500,800)→(500,1300) crosses A (y ≤ 1000) and C (y ≥ 1000), never B. Two records: R6/A 200 m and R6/C 300 m. Both carry length_m = 500, so the copied values add to 1,000 m for 500 m of road; the recomputed lengths add to 500 m.">
    <div class="q">Quick check 11.4. Ward C (0,1000)–(1000,1500) is added, and a new road R6 runs from (500, 800) straight north to (500, 1300) with <code>length_m = 500</code>. Intersect {R6} with {A, B, C}. What comes out?</div>
    <div class="opts"><button class="opt">One record (R6 is one road), ward = A, length 500.</button><button class="opt">Two records — R6/A 200 m and R6/C 300 m — each still carrying length_m = 500 (copied values sum to 1,000).</button><button class="opt">Three records, one per ward, each 500 m.</button><button class="opt">Two records with length_m automatically corrected to 200 and 300.</button></div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const fig = document.getElementById("ixFig"), A = wardById("A"), B = wardById("B");
  function draw() {
    const mode = document.querySelector("input[name=ix]:checked").value;
    const m = gridMap(fig, { extent: { x1: -450, y1: -200, x2: 2450, y2: 1200 }, caption: mode === "line" ? "Intersect R1 × wards: one road becomes two line records, one per ward." : mode === "poly" ? "Intersect buffer × wards: two rectangles remain; the round ends (dashed) overlap no ward and are dropped." : "Intersect Ward A × Ward B: no shared area — only a shared edge (Output Type = LINE)." });
    drawWards(m, [A, B], "faded");
    let recs = "", out = "";
    if (mode === "line") {
      drawRoad(m, ROADS.R1, "ghost");
      const sa = clipLineToRect(ROADS.R1.pts, A)[0], sb = clipLineToRect(ROADS.R1.pts, B)[0];
      mk("polyline", { points: sa.map(p => `${m.X(p[0])},${m.Y(p[1])}`).join(" "), class: "road partA", fill: "none" }, m.svg);
      mk("polyline", { points: sb.map(p => `${m.X(p[0])},${m.Y(p[1])}`).join(" "), class: "road partB", fill: "none" }, m.svg);
      mk("line", { x1: m.X(1000), y1: m.Y(-100), x2: m.X(1000), y2: m.Y(1100), class: "cutline" }, m.svg);
      recs = `<div class="rec new"><h5>Output record 1</h5><div class="kv"><b>road_id</b><span>R1</span><b>ward</b><span>A</span><b>geometry</b><span>(0,500)–(1000,500)</span><b>length_m</b><span class="stale">2000</span><b>recomputed</b><span class="fresh">1,000 m</span></div></div><div class="rec new"><h5>Output record 2</h5><div class="kv"><b>road_id</b><span>R1</span><b>ward</b><span>B</span><b>geometry</b><span>(1000,500)–(2000,500)</span><b>length_m</b><span class="stale">2000</span><b>recomputed</b><span class="fresh">1,000 m</span></div></div>`;
      out = `<strong>Prediction:</strong> lowest dimension = line (1) vs polygon (2) → <strong>line</strong> output. <strong>2 records</strong> where there was 1. Recomputed lengths 1,000 + 1,000 = 2,000 m = R1’s length. Copied <code>length_m</code> values would add to 4,000 m — wrong.`;
    } else if (mode === "poly") {
      drawBuffer(m, ROADS.R1, 300, { cls: "" }); drawRoad(m, ROADS.R1, "ghost");
      const band = { x1: 0, y1: 200, x2: 2000, y2: 800 }, oa = rectOverlap(band, A), ob = rectOverlap(band, B);
      mk("rect", { x: m.X(oa.x1), y: m.Y(oa.y2), width: m.S(oa.x2 - oa.x1), height: m.S(oa.y2 - oa.y1), class: "piece" }, m.svg);
      mk("rect", { x: m.X(ob.x1), y: m.Y(ob.y2), width: m.S(ob.x2 - ob.x1), height: m.S(ob.y2 - ob.y1), class: "piece b" }, m.svg);
      stext(m.svg, m.X(-300), m.Y(950), "dropped: no ward here", "note-text"); stext(m.svg, m.X(1700), m.Y(950), "dropped: no ward here", "note-text");
      recs = `<div class="rec new"><h5>Output record 1</h5><div class="kv"><b>ward</b><span>A</span><b>BUFF_DIST</b><span>300 (a label — fine)</span><b>geometry</b><span>0–1000 × 200–800</span><b>area recomputed</b><span class="fresh">${fmtM2(oa.area)}</span></div></div><div class="rec new"><h5>Output record 2</h5><div class="kv"><b>ward</b><span>B</span><b>BUFF_DIST</b><span>300</span><b>geometry</b><span>1000–2000 × 200–800</span><b>area recomputed</b><span class="fresh">${fmtM2(ob.area)}</span></div></div>`;
      out = `<strong>Prediction:</strong> polygon × polygon → <strong>polygon</strong> output, split by ward. Total ${fmtM2(oa.area + ob.area)} — <em>less</em> than the buffer’s ${fmtM2(r1BufferArea(300))}, because the two round ends overlap no ward.`;
    } else {
      mk("line", { x1: m.X(1000), y1: m.Y(0), x2: m.X(1000), y2: m.Y(1000), class: "road partA" }, m.svg);
      recs = `<div class="rec gone"><h5>Default output type (polygon)</h5><div class="kv"><b>records</b><span>0 — an <em>expected</em> empty output</span></div></div><div class="rec new"><h5>Output Type = LINE</h5><div class="kv"><b>ward</b><span>A and B</span><b>geometry</b><span>(1000,0)–(1000,1000)</span><b>length</b><span class="fresh">1,000 m</span></div></div>`;
      out = `The two wards share an edge and <strong>no area</strong>. The point-set intersection is a line, but the default output type for two polygons is polygon — so the default result is <strong>empty</strong>. Ask for LINE to get the shared edge. Log an expected empty output as expected; any <em>other</em> empty output is a defect.`;
    }
    document.getElementById("ixRecs").innerHTML = recs; document.getElementById("ixOut").innerHTML = out;
  }
  document.querySelectorAll("input[name=ix]").forEach(r => r.addEventListener("change", draw)); draw();

  function rule() {
    const v = document.querySelector("input[name=rule]:checked").value;
    const tb = document.getElementById("ruleRows"), out = document.getElementById("ruleOut");
    if (v === "sum") { tableRows(tb, [{ cells: ["A", "2,000 m"] }, { cells: ["B", "2,000 m"] }, { cls: "hl", cells: ["Total", "4,000 m"] }]); out.innerHTML = `<span class="verdict no">Not defensible.</span> R1 is 2,000 m long in total. You have just invented 2,000 m of road. The tool copied a label onto two pieces; you added the label twice.`; }
    else if (v === "recalc") { tableRows(tb, [{ cells: ["A", "1,000 m"] }, { cells: ["B", "1,000 m"] }, { cls: "hl", cells: ["Total", "2,000 m"] }]); out.innerHTML = `<span class="verdict ok">Safest rule.</span> Length belongs to the new shape, so measure the new shape. No assumption needed. The total equals R1’s length — your check.`; }
    else if (v === "ratio") { tableRows(tb, [{ cells: ["A", "2000 × (1000/2000) = 1,000 m"] }, { cells: ["B", "2000 × (1000/2000) = 1,000 m"] }, { cls: "hl", cells: ["Total", "2,000 m"] }]); out.innerHTML = `<span class="verdict ok">Defensible for evenly spread quantities.</span> For length it gives the same as recomputing. For a budget or a population it <em>assumes</em> the quantity is spread evenly along the feature — write that assumption in the worksheet.`; }
    else { tableRows(tb, [{ cells: ["A", "— (label, not a quantity)"] }, { cells: ["B", "—"] }]); out.innerHTML = `<span class="verdict ok">Right for labels, wrong here.</span> Not adding is correct for codes, names and <code>BUFF_DIST</code>. But <code>length_m</code> <em>is</em> a quantity the manager asked for — so you still owe an answer: recompute it.`; }
  }
  document.querySelectorAll("input[name=rule]").forEach(r => r.addEventListener("change", rule)); rule();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
