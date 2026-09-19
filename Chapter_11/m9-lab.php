<?php $page = ['title' => '11.9 Guided lab — a service-request analysis', 'chapter' => 11, 'module' => '11.9']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 11.9 · Guided lab · ArcGIS Pro primary, QGIS alternative, paper route always possible</div>
    <h1>Lab: how many open complaints near the road, per ward?</h1>
    <p class="lead">You answer one business question twice. <strong>Part 1</strong> uses the paper grid, where every number can be checked by hand. <strong>Part 2</strong> uses Fixture E11 — real-shaped Earth data — where the coordinate system, the units and the method must be chosen and logged. Deliver the outputs, the worksheet, the checks, and an interpretation that claims no more than the data supports.</p>
    <div class="outcomes"><h4>The question (both parts)</h4>
      <ul><li><em>For each ward, how many complaints that are OPEN and were reported on or after 2026-08-01 lie within the stated distance of the road? Report outside and on-the-line cases explicitly.</em></li>
      <li>Threshold: <strong>300 m</strong> on the paper grid (Part 1), <strong>200 m</strong> on E11 (Part 2); both <strong>edge counts (≤)</strong>. Boundary rule: the Chapter 10 policy (11.6.1).</li>
      <li><strong>Not run by the author.</strong> The steps below are written from the official ArcGIS Pro and QGIS 3.44 documentation. Expected values are hand-checked. Your instructor completes the verification items before issuing the lab.</li></ul></div>
  </div>

  <h2><span class="mod">11.9.2</span>What you need</h2>
  <ul>
    <li><strong>Primary route:</strong> ArcGIS Pro 3.x. Tools: Select Layer By Attribute, Select Layer By Location, Buffer, Near, Clip, Spatial Join, XY Table To Point, Project. All are listed at Basic, Standard and Advanced (Buffer’s <em>flat</em> end type is Advanced-only — this lab uses round ends). No extension, no ArcGIS Online account, no credits.</li>
    <li><strong>Alternative:</strong> QGIS Desktop 3.44, core algorithms only (tab below).</li>
    <li><strong>The Chapter 11 package</strong>, built by your instructor from the text below: <code>Chapter11_Planar.gpkg</code> (or a file geodatabase) with an <em>undefined</em> coordinate system, exactly as the Chapter 3 Town package was built, plus the E11 text files.</li>
    <li>A text editor for the log, and graph paper.</li>
  </ul>

  <h2><span class="mod">11.9.3</span>Input data — copy the text</h2>
  <p>All files are <span class="synthetic">made-up practice data</span>. Part 1 files sit on the paper grid (metres, no CRS); lines and polygons are written as WKT, points as x/y columns.</p>
  <div class="tabs"><button>wards_p11.csv</button><button>roads_p11.csv</button><button>requests_p11.csv</button><button>requests_e11.csv</button><button>roads_e11.csv</button><button>provenance_e11.txt</button></div>
  <div class="tabpanel"><p class="small">Paper grid, metres, no CRS. Ward D is not needed in the lab.</p><div class="copywrap"><pre>ward,zone,households,wkt
A,Z1,1200,"POLYGON ((0 0, 1000 0, 1000 1000, 0 1000, 0 0))"
B,Z2,900,"POLYGON ((1000 0, 2000 0, 2000 1000, 1000 1000, 1000 0))"
C,Z1,400,"POLYGON ((0 1000, 1000 1000, 1000 1500, 0 1500, 0 1000))"</pre></div></div>
  <div class="tabpanel"><div class="copywrap"><pre>road_id,name,length_m,wkt
R1,Main Road,2000,"LINESTRING (0 500, 2000 500)"</pre></div></div>
  <div class="tabpanel"><div class="copywrap"><pre>request_id,category,status,reported_on,x,y
P1,Blocked drain,OPEN,2026-08-15,200,200
P2,Streetlight out,CLOSED,2026-07-30,800,800
P3,Pothole,OPEN,2026-09-01,1200,250
P4,Water leak,OPEN,2026-08-22,1700,900
P5,Fallen tree,OPEN,2026-09-05,1000,500
P6,Road damage,OPEN,2026-08-30,2200,500</pre></div></div>
  <div class="tabpanel"><p class="small">EPSG:32643 (WGS 84 / UTM zone 43N), metres. The wards file <code>wards_e6.geojson</code> is Chapter 6’s, unchanged (EPSG:4326, longitude first).</p><div class="copywrap"><pre>request_id,category,status,reported_on,E_m,N_m
Q1,Blocked drain,OPEN,2026-08-20,500000.000,2543741.163
Q2,Streetlight out,CLOSED,2026-07-03,500000.000,2544405.362
Q3,Pothole,OPEN,2026-09-02,499487.611,2544073.271
Q4,Water leak,OPEN,2026-08-28,500512.389,2544073.271
Q5,Fallen tree,OPEN,2026-09-10,501229.733,2544073.313</pre></div></div>
  <div class="tabpanel"><div class="copywrap"><pre>road_id,name,wkt
RE-1,Training Road,"LINESTRING (499000.000 2543900.000, 501000.000 2543900.000)"</pre></div></div>
  <div class="tabpanel"><div class="copywrap"><pre>wards_e6.geojson  : Ward boundaries digitised for training. CRS WGS 84 (EPSG:4326).
                    GeoJSON positions, longitude first (RFC 7946). Unchanged from Chapter 6.
requests_e11.csv  : Request locations exported from the training request tracker on 2026-09-15.
                    CRS WGS 84 / UTM zone 43N (EPSG:32643). E_m, N_m in metres.
                    status = OPEN or CLOSED at export time; reported_on = local calendar date logged.
roads_e11.csv     : Road centreline digitised for training in EPSG:32643; WKT LINESTRING, metres.
All files are synthetic training data and describe no real place.</pre></div></div>

  <figure class="map-fig" id="e11Fig"></figure>

  <h2><span class="mod">11.9.4</span>Part 1 — the paper grid</h2>
  <p><strong>Step 1 — before any tool.</strong> Write the seven-item specification and the five-row worksheet (11.1.3, with step 3 already removed) in your log. Then fill the <em>prediction</em> column below on graph paper from the coordinates. Do not open the GIS until this is done. Your entries are saved in this browser.</p>
  <div class="table-wrap"><table class="pred-table">
    <thead><tr><th>Check</th><th>Your prediction</th><th>Your result</th><th>Hand-checked answer</th></tr></thead>
    <tbody>
      <tr><td>Eligible complaints</td><td><input type="text" data-save="p1-elig-p"></td><td><input type="text" data-save="p1-elig-r"></td><td><details class="reveal"><summary>show</summary>P1, P3, P4, P5, P6 (5)</details></td></tr>
      <tr><td>R1 buffer 300 m, round: area</td><td><input type="text" data-save="p1-area-p"></td><td><input type="text" data-save="p1-area-r"></td><td><details class="reveal"><summary>show</summary>≈ 1,482,743 m² (GIS slightly less; within 0.5 %)</details></td></tr>
      <tr><td><code>BUFF_DIST</code></td><td><input type="text" data-save="p1-bd-p"></td><td><input type="text" data-save="p1-bd-r"></td><td><details class="reveal"><summary>show</summary>300</details></td></tr>
      <tr><td>Eligible within 300 m (≤) of R1</td><td><input type="text" data-save="p1-near-p"></td><td><input type="text" data-save="p1-near-r"></td><td><details class="reveal"><summary>show</summary>P1, P3, P5, P6 (4); P4 out at 400 m</details></td></tr>
      <tr><td>Near distances (m)</td><td><input type="text" data-save="p1-dist-p"></td><td><input type="text" data-save="p1-dist-r"></td><td><details class="reveal"><summary>show</summary>P1 300; P3 250; P4 400; P5 0; P6 200</details></td></tr>
      <tr><td>R1 clipped by Ward A</td><td><input type="text" data-save="p1-clip-p"></td><td><input type="text" data-save="p1-clip-r"></td><td><details class="reveal"><summary>show</summary>one line (0,500)–(1000,500); 1,000 m; <code>length_m</code> still 2000 (stale)</details></td></tr>
      <tr><td>Raw ward counts (Contains), wards A/B/C</td><td><input type="text" data-save="p1-cnt-p"></td><td><input type="text" data-save="p1-cnt-r"></td><td><details class="reveal"><summary>show</summary>A: P1, P5 (2); B: P3, P5 (2); C: 0 — sum 4 for 3 distinct</details></td></tr>
      <tr><td>Reconciliation (complaints as target)</td><td><input type="text" data-save="p1-rec-p"></td><td><input type="text" data-save="p1-rec-r"></td><td><details class="reveal"><summary>show</summary>P1→A; P3→B; P5→A,B (twice); P6→none (outside)</details></td></tr>
      <tr><td>Policy report</td><td><input type="text" data-save="p1-pol-p"></td><td><input type="text" data-save="p1-pol-r"></td><td><details class="reveal"><summary>show</summary>A = 2 (P1, P5 by tie-break); B = 1 (P3); C = 0; outside = 1 (P6)</details></td></tr>
    </tbody></table></div>

  <div class="tabs"><button>ArcGIS Pro steps — Part 1</button><button>ArcGIS Pro steps — Part 2 (E11)</button><button>QGIS 3.44 alternative</button></div>
  <div class="tabpanel">
    <p class="small"><strong>Procedure (version-specific; not run by the author).</strong> Tool names as on the documentation pages cited in the chapter document.</p>
    <ol class="steps-list">
      <li><span class="badge-step">2</span><strong>Open and inspect.</strong> Open the instructor’s project. Confirm the map’s coordinate system is <em>unknown</em> — do not set one (Chapter 3) — and the counts are 3 wards, 1 road, 6 complaints. Log them.</li>
      <li><span class="badge-step">3</span><strong>Eligible complaints (a selection).</strong> <em>Select Layer By Attribute</em> on <code>requests_p11</code>: <code>status = 'OPEN' And reported_on &gt;= date '2026-08-01'</code>. The date-literal syntax depends on the workspace (Chapter 10) — if it fails, use the form the SQL reference gives for your source and log it. Expected 5: P1, P3, P4, P5, P6. Export the selection to <code>req_eligible</code> so later tools use a fixed input.</li>
      <li><span class="badge-step">4</span><strong>Buffer the road (new shape).</strong> <em>Buffer</em>: Input <code>roads_p11</code>; Output <code>r1_buf300</code>; Distance <strong>300</strong> — on the undefined-CRS package leave the unit as <em>Unknown</em> so the value is taken in grid units (metres by declaration); Side Full; End Type <strong>Round</strong>; Dissolve None; Method Planar. <span class="chip q">Verification item</span> how the Distance parameter behaves with an unknown spatial reference. Open the table: one polygon, <code>BUFF_DIST = 300</code>. Calculate the area into a new field <em>on a copy</em> (Calculate Geometry Attributes modifies its input).</li>
      <li><span class="badge-step">5</span><strong>Complaints near the road, two ways.</strong> (a) <em>Select Layer By Location</em>: Input <code>req_eligible</code>, Relationship <strong>Intersect</strong>, Selecting Features <code>r1_buf300</code>. Expected 4. If P1 is missing, it is the exact-threshold case — do (b) before concluding. (b) On a <em>copy</em> of <code>req_eligible</code> (Near adds fields to its input), run <em>Near</em>: Near Features <code>roads_p11</code>, Method Planar. Read <code>NEAR_DIST</code>; select <code>NEAR_DIST &lt;= 300</code>. <strong>The number is the authoritative test</strong>; (a) is the map-friendly one. Export the four as <code>req_near_road</code>.</li>
      <li><span class="badge-step">6</span><strong>Clip the road to Ward A.</strong> Select Ward A, run <em>Clip</em>: Input <code>roads_p11</code>, Clip Features the wards layer (A selected), Output <code>r1_in_A</code>. Expect one line (0,500)–(1000,500). <code>length_m</code> still reads 2000 — add <code>length_m_clip</code> with Calculate Geometry Attributes (Length) and record 1000. Clear the selection.</li>
      <li><span class="badge-step">7</span><strong>Count per ward.</strong> <em>Spatial Join</em>: Target <code>wards_p11</code>; Join <code>req_near_road</code>; Output <code>ward_counts_raw</code>; Join one to one; <strong>Keep All Target Features ticked</strong>; Match Option <strong>Contains</strong>. Expected <code>Join_Count</code> A 2, B 2, C 0. <span class="chip q">Verification item</span> if Contains gives A 1, B 1, record the discrepancy with the documentation and use the option that includes the boundary.</li>
      <li><span class="badge-step">8</span><strong>Reconcile.</strong> Spatial Join the other way: Target <code>req_near_road</code>; Join <code>wards_p11</code>; Join one to many; Keep All ticked; Match Option <strong>Within</strong>; Output <code>req_to_ward</code>. Expected 5 rows: P1–A, P3–B, P5–A, P5–B, P6–(no match, <code>JOIN_FID = −1</code>). Build the reconciliation table (2 assigned, 1 twice, 1 outside = 4). Apply the policy in a table step (sort by ward, keep the first row per complaint; <code>assign_rule</code> BOUNDARY_TIEBREAK for P5, OUTSIDE for P6).</li>
      <li><span class="badge-step">9</span><strong>Sensitivity.</strong> Rerun steps 4–5(b) at 250 m (expect {P3, P5, P6}); rerun step 7 with <em>Completely contains</em> (expect A 1, B 1, C 0; P5 outside). One change at a time.</li>
      <li><span class="badge-step">10</span><strong>Log.</strong> For each tool, copy the History entry and add why each value was chosen, the CRS line (“training grid; undefined; metres by declaration”), the output’s type, count and extent, and the removed worksheet step.</li>
    </ol>
  </div>
  <div class="tabpanel">
    <p class="small"><strong>Procedure (version-specific; not run by the author).</strong> Predictions are hand-checked from Table F11-3 and Chapter 6.</p>
    <ol class="steps-list">
      <li><span class="badge-step">11</span><strong>Intake and CRS decision.</strong> Read <code>provenance_e11.txt</code>. Decide the analysis CRS by Chapter 6’s worksheet: <strong>EPSG:32643</strong> — the study area is inside zone 43N, the property needed is distance, flat measurement there has a documented 0.04 % scale effect, and two of three inputs are already in it. Write it down.</li>
      <li><span class="badge-step">12</span><strong>Load with the right CRS.</strong> Add <code>wards_e6.geojson</code> (Source tab must read WGS 1984 / 4326). <em>XY Table To Point</em> on <code>requests_e11.csv</code> with <strong>Coordinate System = WGS 1984 UTM Zone 43N (WKID 32643)</strong> — the default is WGS 84 geographic and would mislabel metres as degrees (Chapter 6). <code>roads_e11</code>: <span class="chip q">Verification item</span> the instructor builds it into the package from the WKT (Chapter 3 route) or you create the two-vertex line in an edit session by typing coordinates. Confirm its Source tab says 32643 and its length is 2,000.000 m.</li>
      <li><span class="badge-step">13</span><strong>Transform the wards.</strong> <em>Project</em> to EPSG:32643 → <code>wards_e6_utm</code>; confirm no geographic transformation is offered (same datum), exactly as Chapter 6 step 8. Expected extent E ≈ 498,975–501,025; N ≈ 2,543,520–2,544,627. Nothing in Part 2 runs on a degree-based layer.</li>
      <li><span class="badge-step">14</span><strong>Eligible complaints.</strong> As step 3. Expected Q1, Q3, Q4, Q5 (Q2 is CLOSED and from July). Export <code>req_e11_eligible</code>.</li>
      <li><span class="badge-step">15</span><strong>Buffer and Near.</strong> Buffer <code>roads_e11</code> by <strong>200 Meters</strong>, Round, Dissolve None, Method <strong>Planar</strong> (projected input → Euclidean buffer). Log that a planar 200 m is about 200.08 m on the ground here (scale factor 0.9996). Near (Planar) on a copy of the eligible complaints against the road: expected Q1 158.837; Q3 173.271; Q4 173.271; Q5 287.775 (to 0.01 m). Select <code>NEAR_DIST &lt;= 200</code> → Q1, Q3, Q4. Export <code>req_e11_near</code>.</li>
      <li><span class="badge-step">16</span><strong>Clip the buffer to the wards.</strong> Clip <code>re1_buf200</code> by <code>wards_e6_utm</code>. Area roughly <strong>820,000 m²</strong> (± 1,000): the buffer’s round ends stick about 175 m past the ward edges and are removed. Record the software’s value — do not hand-derive it more finely; the ward edges are projected meridians and parallels, not exact grid lines.</li>
      <li><span class="badge-step">17</span><strong>Count, reconcile, policy.</strong> As steps 7–8 with <code>wards_e6_utm</code> as target and <code>req_e11_near</code> as join. Expected raw A 2 (Q1, Q3), B 2 (Q1, Q4); reverse join Q1→A,B; Q3→A; Q4→B; policy A 2 (Q3, Q1 by tie-break), B 1 (Q4), outside 0. Q1 sits <em>exactly</em> on the central meridian (easting 500,000.000), which is also the shared ward edge — its double match is the boundary case by construction. If Q1 matches only one ward, investigate tolerance and the Source tab before accepting.</li>
      <li><span class="badge-step">18</span><strong>Sensitivity and log.</strong> Threshold <strong>170 m</strong>: near set {Q1} only (Q3, Q4 at 173.271 drop); <strong>175 m</strong>: all three remain. Two complaints sit within 5 m of a plausible threshold — the report must say so. Complete the log with the CRS/transformation lines from Chapter 6.</li>
    </ol>
  </div>
  <div class="tabpanel">
    <p class="small"><strong>Equivalent foundational exercise, written from the QGIS 3.44 documentation; not run by the author.</strong> Same expected values. The differences are in which algorithm carries which attribute and how the date condition is written.</p>
    <ol class="steps-list">
      <li><strong>Load.</strong> Add the paper CSVs with <em>Add Delimited Text Layer</em> — WKT files with geometry “Well known text (WKT)”, requests as point coordinates x/y, no Earth CRS (Chapter 3 route). For E11: <code>wards_e6.geojson</code>; the requests CSV as points with <strong>Geometry CRS = EPSG:32643</strong>; <code>roads_e11.csv</code> as WKT with CRS 32643; <em>Reproject layer</em> on the wards to 32643 (Chapter 6).</li>
      <li><strong>Eligible.</strong> <em>Select features by expression</em>: <code>"status" = 'OPEN' AND "reported_on" &gt;= '2026-08-01'</code> — if the loader typed <code>reported_on</code> as text the comparison is textual (log it); otherwise use <code>to_date('2026-08-01')</code>. <span class="chip q">Verify</span> the column type. Export the selection. Expected 5 / 4.</li>
      <li><strong>Buffer.</strong> Processing ▸ <em>Buffer</em>: Distance 300 (layer units — the fixture’s metres), Segments 5 or more, End cap <strong>Round</strong>, Dissolve result unticked. <em>Add geometry attributes</em> for the area; it will be a little under 1,482,743 because of the segment count — raise Segments and watch it approach.</li>
      <li><strong>Near the road.</strong> <em>Select by location</em> (predicate <em>intersect</em>, against the buffer) for the map view; for the authoritative number use a field-calculator expression such as <code>distance($geometry, geometry(get_feature('roads_p11','road_id','R1')))</code>, or <em>Join attributes by nearest</em>. <span class="chip q">Verify</span> which algorithm reports the perpendicular distance to a <em>line</em> — <em>Distance to nearest hub</em> uses feature centres and is the wrong tool for this. Filter ≤ 300. Expected {P1, P3, P5, P6} / {Q1, Q3, Q4}.</li>
      <li><strong>Clip.</strong> Processing ▸ <em>Clip</em>: Input roads, Overlay Ward A (selected features only). Attributes unchanged while “length … will be modified by the overlay operation” — <code>length_m</code> stays 2000; add <code>$length</code> in a new field → 1000.</li>
      <li><strong>Count per ward.</strong> <em>Join attributes by location (summary)</em>: Join to = wards; By comparing to = near-road complaints; predicate <strong>contains</strong> (“no points of b lie in the exterior of a” — the boundary is not the exterior, so P5 should count; <span class="chip q">verify</span>); statistic count on <code>request_id</code>. Expected <code>JOINED_COUNT</code> A 2, B 2, C 0. Alternatively <em>Count points in polygon</em> → <code>NUMPOINTS</code> (check that a boundary point is counted).</li>
      <li><strong>Reconcile.</strong> <em>Join attributes by location</em>: Join to = near-road complaints; By comparing to = wards; predicate <em>are within</em>; join type <strong>one-to-many</strong>; leave “Discard records which could not be joined” unticked so P6 appears with a null ward. Expected 5 rows. Apply the policy in the attribute table.</li>
      <li><strong>Log.</strong> Copy the entries from Processing ▸ History; record the QGIS and PROJ versions, the project CRS and the Measurements ellipsoid setting (Chapter 6).</li>
    </ol>
    <p class="small"><strong>Not claimed:</strong> that QGIS and ArcGIS Pro give identical buffer areas (segmentation differs), that both treat a boundary point identically under “contains” (both are expected to include it; the instructor records what was observed), or that the date comparison behaves identically (it depends on how each loader typed the column).</p>
  </div>

  <h2><span class="mod">11.9.6</span>Expected results and what to do when a check fails</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Check</th><th>Expected (Part 1 / Part 2)</th><th>If it fails</th></tr></thead>
    <tbody>
      <tr><td>Eligible count</td><td>5 / 4</td><td>Date literal parsed wrongly — test the date condition alone; check <code>status</code> case</td></tr>
      <tr><td><code>BUFF_DIST</code></td><td>300 / 200</td><td>Units misread (degrees or unknown units) — rerun with explicit units on a projected input</td></tr>
      <tr><td>Buffer area</td><td>≈ 1,482,743 / ≈ 925,664 m², slightly less</td><td>Wrong distance, or flat ends (exactly 1,200,000 / 800,000)</td></tr>
      <tr><td>Near distances</td><td>Match the fixture to 0.01 m</td><td>Wrong Method, wrong near features, or a request layer in the wrong CRS</td></tr>
      <tr><td>Near-road eligible set</td><td>{P1, P3, P5, P6} / {Q1, Q3, Q4}</td><td>P1 lost by the polygon test — use the Near number; P6 lost — flat ends</td></tr>
      <tr><td>Clipped road</td><td>1 line, 1,000 m; <code>length_m</code> stale (2000)</td><td>Whole road returned (2,000 m) — you selected instead of clipping</td></tr>
      <tr><td>Raw per-ward counts</td><td>A 2, B 2, C 0 / A 2, B 2</td><td>Boundary point not matched — predicate is strict; C missing — Keep All unticked</td></tr>
      <tr><td>Reconciliation identity</td><td>assigned + twice + outside = near-road count</td><td>A complaint dropped or duplicated — find it by ID before anything else</td></tr>
      <tr><td>Output CRS (Part 2)</td><td>WKID 32643 on every output</td><td>A tool ran on the GeoJSON or the mislabelled points — rebuild from step 12</td></tr>
      <tr><td>Originals</td><td>Byte-identical to the text above</td><td>Near or Calculate Geometry was run on an original — restore from the text</td></tr>
    </tbody></table></div>
  <details class="reveal"><summary>More troubleshooting</summary>
    <ul>
      <li><strong>The date condition returns nothing.</strong> The literal syntax is source-specific; confirm <code>reported_on</code> imported as a date, not text. A text comparison happens to work for ISO dates — a coincidence, not a method; log which happened.</li>
      <li><strong>Buffer on the paper package complains about units.</strong> Leave the unit as Unknown (grid units). If your version insists on a defined CRS for linear units, record that and use the Near number for the threshold test.</li>
      <li><strong>P1 not selected by Intersect but <code>NEAR_DIST</code> = 300.</strong> The expected exact-threshold behaviour. Report the Near result as authoritative and note the polygon test’s tolerance sensitivity.</li>
      <li><strong>P5 or Q1 counted in one ward only.</strong> Strict predicate, or the point is not exactly on the edge in the built package — check the stored coordinates.</li>
      <li><strong>Ward C absent.</strong> Keep All Target Features unticked (inner join). Rerun.</li>
      <li><strong>Part 2 points appear near the equator.</strong> XY Table To Point’s Coordinate System was left at its default — delete and rerun (Chapter 6).</li>
      <li><strong>Q5’s distance is not 287.775.</strong> Its nearest road point is the <em>end</em> (501,000, 2,543,900); a value of 173.313 means the road was extended — check its vertices.</li>
    </ul>
  </details>

  <h2><span class="mod">11.9.8</span>Deliverables</h2>
  <div class="lab-card checklist">
    <h4>Tick as you finish (saved in this browser)</h4>
    <label><input type="checkbox" data-save="d1"> Output layers/tables: <code>req_eligible</code>, <code>r1_buf300</code>, <code>req_near_road</code>, <code>r1_in_A</code>, <code>ward_counts_raw</code>, <code>req_to_ward</code> and the E11 equivalents — <em>plus the untouched originals</em></label>
    <label><input type="checkbox" data-save="d2"> Analysis worksheet: the seven-item specification; the five-row worksheet with the removed step and its reason; both prediction/result tables with every mismatch explained</label>
    <label><input type="checkbox" data-save="d3"> Validation evidence: the invariants table (11.8.1) for each output; hand spot-checks for P1, P5, P6 and Q1, Q5; the sensitivity runs</label>
    <label><input type="checkbox" data-save="d4"> Log: tool and version, parameters (including defaults), CRS/transformation lines, source editions, output paths with type/count/extent, tools run on copies, known limitations</label>
    <label><input type="checkbox" data-save="d5"> Interpretation (≤ 150 words): the policy counts per ward, the outside and on-the-line cases, and what the result does <em>not</em> say — no travel times, no risk, nothing about other roads, and one sentence on exact-threshold sensitivity</label>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  // E11 sketch in UTM metres (ward edges drawn as straight lines — an approximation; real projected edges curve very slightly)
  const el = document.getElementById("e11Fig");
  const ex = { x1: 498700, y1: 2543350, x2: 501500, y2: 2544800 };
  const W = 1200, pad = 70, sc = (W - 2 * pad) / (ex.x2 - ex.x1), H = (ex.y2 - ex.y1) * sc + 2 * pad;
  const X = x => pad + (x - ex.x1) * sc, Y = y => pad + (ex.y2 - y) * sc, S = d => d * sc;
  const svg = mk("svg", { viewBox: `0 0 ${W} ${H}`, role: "img", "aria-label": "Sketch of Fixture E11 in UTM zone 43N metres: two wards, Road RE-1 and requests Q1–Q5" });
  el.innerHTML = ""; el.appendChild(svg);
  const wa = { x1: 498975, y1: 2543520, x2: 500000, y2: 2544627 }, wb = { x1: 500000, y1: 2543520, x2: 501025, y2: 2544627 };
  [["A", wa], ["B", wb]].forEach(([id, w]) => { mk("rect", { x: X(w.x1), y: Y(w.y2), width: S(w.x2 - w.x1), height: S(w.y2 - w.y1), class: "ward" }, svg); stext(svg, X(w.x1) + 18, Y(w.y2) + 58, "Ward " + id, "ward-label"); });
  const g = mk("g", { class: "buffer" }, svg); mk("path", { d: (() => { const a = E11.road.pts[0], b = E11.road.pts[1], R = S(200), ax = X(a[0]), ay = Y(a[1]), bx = X(b[0]), by = Y(b[1]); return `M${ax},${ay - R} L${bx},${by - R} A${R},${R} 0 0 1 ${bx},${by + R} L${ax},${ay + R} A${R},${R} 0 0 1 ${ax},${ay - R} Z`; })() }, g);
  mk("polyline", { points: E11.road.pts.map(p => `${X(p[0])},${Y(p[1])}`).join(" "), class: "road", fill: "none" }, svg); stext(svg, X(501000) + 10, Y(2543900) - 14, "RE-1", "road-label");
  E11.requests.forEach(q => { const el_ = eligible([q]).length > 0; mk("circle", { cx: X(q.E), cy: Y(q.N), r: 13, class: "req " + (!el_ ? "gone" : q.dist <= 200 ? "in" : "out") }, svg); stext(svg, X(q.E) + 18, Y(q.N) - 14, q.id, "req-label"); });
  mk("line", { x1: X(498800), y1: Y(2543420), x2: X(499300), y2: Y(2543420), class: "axis" }, svg); stext(svg, X(498800), Y(2543440), "500 m (UTM grid)", "axis-label");
  const c = document.createElement("figcaption"); c.textContent = "Fixture E11 sketched in EPSG:32643 metres with a 200 m buffer of Road RE-1. Green = eligible and within 200 m (Q1, Q3, Q4); grey = eligible but too far (Q5); faded = not eligible (Q2). Ward edges drawn straight — an approximation. Made-up data."; el.appendChild(c);
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
