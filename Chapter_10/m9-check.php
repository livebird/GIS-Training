<?php $page = ['title' => '10.9 Independent check and progression gate', 'chapter' => 10, 'module' => '10.9']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 10.9 · Assessment</div>
    <h1>Independent check</h1>
    <p class="lead">Six concept questions, two scenarios, one practical on <em>new</em> data, and one spoken explanation. Where a question does not say otherwise: coordinates are planar metres on the training grid, distances are straight-line, and “inclusive” means the boundary case counts. The concept questions give instant feedback; the scenarios, practical and oral are marked by your instructor from the chapter’s Instructor Appendix.</p>
    <div class="outcomes"><h4>Pass rule (from the blueprint)</h4>
      <ul><li>Suggested pass: 80 of 100 — <strong>and</strong> no critical misconception.</li>
      <li>Critical misconceptions that block progress on their own: using <code>= NULL</code> or <code>&lt;&gt;</code> to find missing values; summing a left-table column over rows multiplied by a join; applying a metre threshold to degree coordinates; presenting a spatial join’s raw match count as an assignment; assuming a tool’s “within” is strict (or inclusive) without citing its documentation.</li></ul></div>
  </div>

  <h2><span class="mod">10.9.1</span>Concept questions (30 points, 5 each)</h2>
  <div class="quiz" data-answer="1" data-fb="A definition query limits which features the layer retrieves, draws, lists — and “can be … processed by geoprocessing tools”. So Spatial Join sees P1 and P3 only, and nothing in the stored table changes: a definition query is a layer property.">
    <div class="q">Q1 (10.1). A colleague puts a definition query <code>status = 'Open'</code> on the requests layer in ArcGIS Pro, then runs Spatial Join with that layer as the target. Which requests does the tool process, and did the stored table change?</div>
    <div class="opts"><button class="opt">All six; nothing changed</button><button class="opt">P1 and P3; nothing changed</button><button class="opt">P1 and P3; the other four were deleted</button><button class="opt">All six; the query was written into the table</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="2" data-fb="NOT (cost > 2000) → P1 (1500), P4 (0). cost > 2000 → P2 (12000), P5 (2500). Neither → P3, P6, whose cost is unknown. To include them: NOT (est_cost_inr > 2000) OR est_cost_inr IS NULL → P1, P3, P4, P6. P4’s zero is a known small value.">
    <div class="q">Q2 (10.2). On the practice table, which IDs does <code>NOT (est_cost_inr &gt; 2000)</code> return, which does <code>est_cost_inr &gt; 2000</code> return, and which are in <em>neither</em>? How do you also return the unknown-cost requests?</div>
    <div class="opts"><button class="opt">{P1, P3, P4, P6} / {P2, P5} / none — nothing to add</button><button class="opt">{P1, P4} / {P2, P3, P5, P6} / none — add OR est_cost_inr = NULL</button><button class="opt">{P1, P4} / {P2, P5} / {P3, P6} — add OR est_cost_inr IS NULL</button><button class="opt">{P1} / {P2, P5} / {P3, P4, P6} — add OR est_cost_inr &lt;&gt; 0</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="0" data-fb="The QGIS layer join returns all target features and, for duplicate join keys, “only the first fetched feature is picked”: 6 rows. P1 and P2 (code A) may carry the 2019 or the 2024 valid_from; P5 and P6 carry nulls. “First fetched” is not defined by any column, so it can depend on storage order, indexing or caching and may differ between runs — filter the register to is_current = 'Yes' before joining.">
    <div class="q">Q3 (10.3). The requests are joined to the ward register in QGIS’s layer <em>Joins</em> tab (documented as one-to-one, “only the first fetched feature is picked”, all target features kept). Row count, which requests may carry the wrong <code>valid_from</code>, and why can “which” differ between runs?</div>
    <div class="opts"><button class="opt">6 rows; P1 and P2; “first fetched” is not defined by any column, so it may change with storage order or caching</button><button class="opt">8 rows; P1 and P2 appear twice; it never differs</button><button class="opt">4 rows; P5 and P6 are dropped; it differs only if the CSV is re-sorted</button><button class="opt">6 rows; P3 and P4; the join is random by design</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="3" data-fb="The bounding box 0–300 × 0–300 contains (250, 250) → candidate. ST_Contains(Y, point) is false: x > 100 and y > 100 puts the point in the notch. For a point, ST_Intersects and ST_Covers never differ — both are true exactly when the point is in the interior or on the boundary; they differ only for shapes partly outside.">
    <div class="q">Q4 (10.4). For the L-shaped yard Y and the point (250, 250): bounding-box result, exact <code>ST_Contains(Y, point)</code> result, and can <code>ST_Intersects</code> ever differ from <code>ST_Covers</code> for a point?</div>
    <div class="opts"><button class="opt">Not a candidate; false; yes, often</button><button class="opt">Candidate; true; no</button><button class="opt">Candidate; false; yes — on the boundary</button><button class="opt">Candidate; false; no — for a point they always agree</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="1" data-fb="ST_Contains(B, P5) false (the point lies on B’s boundary; interiors must share a point). ST_Covers(B, P5) true. ArcGIS Within: selected (inclusive per the point/polygon table). ArcGIS Completely within: not selected (the input intersects the boundary). QGIS are within: false (opposite of contain, which needs an interior point). QGIS touch: true.">
    <div class="q">Q5 (10.5). P5 at (1000, 500) and Ward B. Give the six results: PostGIS <code>ST_Contains(B, P5)</code>, <code>ST_Covers(B, P5)</code>; ArcGIS Pro <em>Within</em>, <em>Completely within</em>; QGIS <em>are within</em>, <em>touch</em>.</div>
    <div class="opts"><button class="opt">true, true, selected, selected, true, false</button><button class="opt">false, true, selected, not selected, false, true</button><button class="opt">false, false, not selected, not selected, false, true</button><button class="opt">true, true, not selected, selected, true, true</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="2" data-fb="With units omitted, the default is esriSRUnit_Foot on ArcGIS Enterprise and esriSRUnit_Meter on ArcGIS Online — 300 ft (≈ 91 m) versus 300 m — so the two calls return different sets. The second parameter is inSR: “If the inSR is not specified, the geometry is assumed to be in the spatial reference of the layer”, so grid metres supplied to a degree-based layer land near (0°, 0°). The layer must also report supportsQueryWithDistance.">
    <div class="q">Q6 (10.6). A developer calls a hosted layer’s <code>query</code> with <code>geometry=&lt;P5&gt;</code>, <code>spatialRel=esriSpatialRelIntersects</code>, <code>distance=300</code> and <strong>no</strong> <code>units</code>, once on ArcGIS Online and once on ArcGIS Enterprise. Why can the results differ, and which second parameter must be checked when the layer is in degrees?</div>
    <div class="opts"><button class="opt">They cannot differ; check <code>outFields</code></button><button class="opt">Online is faster; check <code>resultRecordCount</code></button><button class="opt">The default unit differs (feet on Enterprise, metres on Online); check <code>inSR</code>, the spatial reference of the query geometry</button><button class="opt">Enterprise ignores <code>distance</code>; check <code>returnGeometry</code></button></div><div class="fb"></div>
  </div>

  <h2><span class="mod">10.9.2</span>Scenario questions (20 points, 10 each) — write your answers; instructor-marked</h2>
  <div class="scen">
    <h4 style="margin-top:0">Scenario 1 (10.3, 10.7)</h4>
    <p>A dashboard reports “Requests per crew — T-N: 6, T-S: 3, unassigned: 0; total 9”. It was built by (i) a Spatial Join of requests (target) to wards (join) with <em>Intersect</em>, <em>Join one to many</em>, keep all; then (ii) an attribute join of that result to the ward register export on <code>ward_code</code>, one-to-many, keeping only matches; then (iii) a count of rows per <code>crew_team</code>. Reproduce the 6 and the 3, explain each inflation step, and specify a corrected procedure with its totals. Say which unit each of the three counts (6, 3, and yours) actually measures.</p>
    <div class="lab-form"><textarea data-save="s1" placeholder="Step (i) gives … rows because … Step (ii) … Corrected: …"></textarea></div>
  </div>
  <div class="scen">
    <h4 style="margin-top:0">Scenario 2 (10.6, 10.7)</h4>
    <p>The drainage team asks: “For every blocked-drain request, find the drain it refers to — the nearest drain within 15 m — so we can auto-fill <code>asset_id</code>.” On the practice grid there is one blocked-drain request (P5) and one drain (DR-0042). (a) Give the distance and say whether the rule fills <code>asset_id</code>. (b) The team wants the same rule on Chapter 6’s Fixture E6, whose coordinates are in EPSG:4326; write the two-step method you would use and the one you would refuse, with reasons. (c) State three things the rule must specify beyond “nearest within 15 m” before it is reproducible, and the one thing a filled <code>asset_id</code> from this rule does <em>not</em> establish.</p>
    <div class="lab-form"><textarea data-save="s2"></textarea></div>
  </div>

  <h2><span class="mod">10.9.3</span>Practical task on unfamiliar data (40 points)</h2>
  <p><strong>Fixture Z-10 (made up; planar metres; no CRS; new for this task — do not reuse Chapter 10 numbers).</strong> A depot has two yards and two underground pipes; six inspection points were logged with a yard code typed by the inspector. Z1 has a <strong>hole</strong> (its inner ring). Z1 and Z2 share the edge x = 600.</p>
  <div class="grid-2">
    <figure class="map-fig" id="zFig"></figure>
    <div>
      <div class="tabs"><button>zones_z10.csv</button><button>pipes_z10.csv</button><button>points_z10.csv</button><button>zone_register_z10.csv</button></div>
      <div class="tabpanel"><div class="copywrap"><pre>zone_code,wkt
Z1,"POLYGON((0 0,600 0,600 600,0 600,0 0),(200 200,400 200,400 400,200 400,200 200))"
Z2,"POLYGON((600 0,1200 0,1200 600,600 600,600 0))"</pre></div></div>
      <div class="tabpanel"><div class="copywrap"><pre>pipe_id,wkt
L1,"LINESTRING(0 100,1200 100)"
L2,"LINESTRING(0 500,1200 500)"</pre></div></div>
      <div class="tabpanel"><div class="copywrap"><pre>point_id,x,y,zone_code
K1,100,100,Z1
K2,300,260,Z1
K3,600,300,Z2
K4,900,150,Z2
K5,1300,250,Z3
K6,200,350,</pre></div></div>
      <div class="tabpanel"><div class="copywrap"><pre>zone_code,zone_name,valid_from,is_current
Z1,North yard,2023-01-01,No
Z1,North yard,2025-01-01,Yes
Z2,South yard,2023-01-01,Yes</pre></div></div>
    </div>
  </div>
  <ol>
    <li><strong>Predict on paper</strong>, for each point: its position relative to Z1 and Z2 (interior / boundary / hole / exterior), the strict-interior zone (if any), the inclusive zone(s), and whether it <em>touches</em> a zone. Handle the point in the hole and the point on the hole’s ring explicitly.</li>
    <li><strong>Predict</strong> the points within 150 m (inclusive) of <em>any</em> pipe, the points within 150 m (strict), and each point’s nearest pipe with its distance to two decimals. Identify the equal-distance tie and state a documented tie rule you would apply and why “random” is unacceptable.</li>
    <li><strong>Predict</strong> the row count of the attribute join points → zone register on <code>zone_code</code> with all points kept, the rows per point, the number of distinct points matched, and the number unmatched (with their two different reasons).</li>
    <li><strong>Predict</strong> <code>Join_Count</code> per zone for a spatial join target = zones, join = points, inclusive, one-to-one, and the total versus distinct counts.</li>
    <li><strong>Execute</strong> items 1–4 in one engine (any route from 10.8), record engine/version/options, and reconcile every difference using the four causes of 10.1.3.</li>
    <li><strong>Write</strong> the zone-assignment policy (boundary, hole, outside, unmatched register key, duplicate register key) and apply it, giving one assignment or an explicit “none” per point, labelled separately from the raw matches.</li>
  </ol>
  <div class="lab-form"><label for="prac">Your prediction tables (paste or type; saved in this browser only — submit the full work to your instructor)</label><textarea id="prac" data-save="prac" style="min-height:140px"></textarea></div>

  <h2><span class="mod">10.9.4</span>Oral explanation (10 points)</h2>
  <div class="scen"><p>In no more than three minutes, using request P5, explain to a colleague who has never used a GIS why the same request is “in neither ward” in one tool and “in both wards” in another, why <em>neither</em> tool is wrong, and what the one sentence in the team’s written policy must say so that the crew-dispatch report shows P5 exactly once.</p></div>

  <h2><span class="mod">10.9.5</span>Submission and scoring</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Component</th><th>Weight</th><th>What earns the marks</th></tr></thead>
    <tbody>
      <tr><td>Concept questions (6)</td><td>30 %</td><td>Correct IDs/counts <em>with</em> the reason; a right answer with no reasoning scores half</td></tr>
      <tr><td>Scenario questions (2)</td><td>20 %</td><td>Each inflation or error step identified; the corrected procedure’s counts stated and unit-labelled</td></tr>
      <tr><td>Practical task</td><td>40 %</td><td>Predictions dated before execution (10); correct predictions for hole, boundary, tie, duplicate key and unmatched key (15); execution recorded with engine, version and exact options (5); every mismatch reconciled to one of the four causes (5); policy complete, applied, and labelled separately from raw matches (5)</td></tr>
      <tr><td>Oral explanation</td><td>10 %</td><td>Distinguishes predicate meaning from data quality; names the policy sentence; does not claim either tool is wrong</td></tr>
    </tbody></table></div>
  <p><strong>Progression.</strong> Move to Chapter 11 when you can explain every selected ID and count in your practical — including the zero (the point in the hole under every membership option), the multiple match (the point on the shared edge), the tie (its two pipes), and the join inflation (the duplicated zone code) — without the answer key.</p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const el = document.getElementById("zFig");
  const s = 0.6, sx = x => 40 + x * s, sy = y => 40 + (600 - y) * s;
  const svg = mkEl("svg", { viewBox: "0 0 880 440", role: "img", "aria-label": "Fixture Z-10: two square yards side by side, the left one with a square hole; two horizontal pipes; six labelled points." });
  const Z1o = [[0, 0], [600, 0], [600, 600], [0, 600]], Z1h = [[200, 200], [400, 200], [400, 400], [200, 400]], Z2 = [[600, 0], [1200, 0], [1200, 600], [600, 600]];
  const ring = r => r.map(p => `${sx(p[0])},${sy(p[1])}`).join(" ");
  mkEl("path", { d: [Z1o, Z1h].map(r => "M " + r.map(p => `${sx(p[0])} ${sy(p[1])}`).join(" L ") + " Z").join(" "), class: "ward", "fill-rule": "evenodd", style: "stroke-width:3" }, svg);
  mkEl("polygon", { points: ring(Z2), class: "ward", style: "stroke-width:3" }, svg);
  txt(svg, sx(300), sy(520), "Z1", "ward-label", { "text-anchor": "middle", style: "font-size:40px" }); txt(svg, sx(900), sy(520), "Z2", "ward-label", { "text-anchor": "middle", style: "font-size:40px" });
  txt(svg, sx(300), sy(300), "hole", "tag", { "text-anchor": "middle", style: "font-size:18px" });
  [[100, "L1"], [500, "L2"]].forEach(([y, id]) => { mkEl("line", { x1: sx(0), y1: sy(y), x2: sx(1200), y2: sy(y), class: "road", style: "stroke-width:6" }, svg); txt(svg, sx(1210), sy(y) + 6, id, "road-label", { style: "font-size:18px" }); });
  [["K1", 100, 100], ["K2", 300, 260], ["K3", 600, 300], ["K4", 900, 150], ["K5", 1300, 250], ["K6", 200, 350]].forEach(([id, x, y]) => { mkEl("circle", { cx: sx(x), cy: sy(y), r: 7, class: "req" }, svg); txt(svg, sx(x) + 10, sy(y) - 8, id, "req-label", { style: "font-size:18px" }); });
  txt(svg, 40, 430, "Fixture Z-10 — schematic; made-up; metres. Predict before you run anything.", "tag", { style: "font-size:16px" });
  el.innerHTML = ""; el.appendChild(svg);
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
