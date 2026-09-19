<?php $page = ['title' => '12.9 Independent check and Phase 1 exit', 'chapter' => 12, 'module' => '12.9']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 12.9 · Assessment · Phase 1 exit</div>
    <h1>Independent check — and the Phase 1 exit practical</h1>
    <p class="lead">Six concept questions with instant feedback, two scenarios, a critique of a map you have never seen, the integrated Phase 1 practical on new Earth-referenced data with hidden defects, and one spoken explanation. The scenarios, critique, practical and oral are marked by your instructor from the chapter’s Instructor Appendix.</p>
    <div class="outcomes"><h4>Pass rule (from the blueprint)</h4>
      <ul><li>Suggested pass: 80 of 100 — <strong>and</strong> no critical misconception.</li>
      <li>Critical misconceptions for this chapter, on top of the Phase 1 list: showing missing as zero; presenting a count as a fair comparison or a rate as “risk”; recalculating classes across a comparison without saying so; treating a hidden field or a switched-off layer as protection; reading a drawn position at high zoom as proof of which side of a boundary a point is on.</li></ul></div>
  </div>

  <h2><span class="mod">12.9.0</span>Concept questions (18 points, 3 each)</h2>
  <div class="quiz" data-answer="1" data-fb="The crew must decide where to go today (points at true positions, type, priority, roads); the manager must decide where capacity goes (one comparable value per ward, disclosed classes, no-data entry). The combined map hides the points under the shading and makes dots read as workload.">
    <div class="q">Q1 (12.1). A single web map “for crews and managers” shows a ward choropleth of counts under all open request points. What is the best judgement?</div>
    <div class="opts"><button class="opt">Good — one map, less maintenance.</button><button class="opt">Two different decisions need two maps: points for the crew, one disclosed value per ward for the manager.</button><button class="opt">Fine if the points are made bigger.</button><button class="opt">Fine if the choropleth uses natural breaks.</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="2" data-fb="4,000 m ÷ 0.10 m = 40,000 → 1:40,000; a larger denominator than 25,000, so smaller scale, less detail. Zooming a screen copy to 1:250 changes only the map scale; the six decimals (precision) and ± 15 m (accuracy) are unchanged, so the position is exactly as trustworthy as before.">
    <div class="q">Q2 (12.2). G-12 (4 km wide) is printed 10 cm wide. A request captured at ± 15 m and stored with six decimals is then viewed on screen at 1:250. Scale, and what changed?</div>
    <div class="opts"><button class="opt">1:4,000; the zoom made the position more precise.</button><button class="opt">1:40,000 — larger scale than 1:25,000; the six decimals make it accurate to a centimetre.</button><button class="opt">1:40,000 — smaller scale than 1:25,000; only the scale changed, the position is still ± 15 m.</button><button class="opt">1:400; the accuracy improved to ± 1 m.</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="0" data-fb="asset_type is a name: a ramp asserts an order that does not exist → unique symbols with distinct shapes. condition is an order: unrelated hues destroy it → one hue in five ordered shades (or sizes) with “5 = best” in the legend. Grey test: shapes still differ; a single-hue ramp still reads as ordered greys.">
    <div class="q">Q3 (12.3). <code>asset_type</code> (Streetlight, Drain, Tree) is drawn with a light-to-dark ramp and <code>condition</code> (1–5) with five unrelated hues. Which fix is right?</div>
    <div class="opts"><button class="opt">Shapes for asset_type; one hue in ordered shades for condition, with “5 = best” written.</button><button class="opt">Keep both; add a legend.</button><button class="opt">A ramp for both.</button><button class="opt">Unrelated hues for both.</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="3" data-fb="Width 10 from 0: 0–10 = {0,2,3,4,5,6,7} (7 wards); 10–20 = {13,14,16,17} (4); 20–30 = empty; 30–40 = {31,35,40} (3, with 40 exactly on a break — state the convention); 40–50 = {45}. Better for “how many wards exceed 30?” and for month-to-month comparison (fixed by definition); worse at showing the data’s own clusters (an empty class; the 31–45 cluster split).">
    <div class="q">Q4 (12.4). The 15 G-12 counts are classified by <em>defined interval</em> of width 10. Which statement is right?</div>
    <div class="opts"><button class="opt">It gives the same classes as natural breaks.</button><button class="opt">It gives 5 / 5 / 5.</button><button class="opt">It cannot be used because 40 falls on a break.</button><button class="opt">Classes 7 / 4 / 0 / 3 / 1 (40 on a break — convention needed); good for “how many exceed 30” and for comparing months, worse at showing clusters.</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="2" data-fb="P: 4.0 per 1,000 households, 2.0 per km. Q: 9.0 per 1,000, 6.0 per km. Q is worse under both denominators; P is worse only by count. Roads produce potholes → per km for resurfacing; title “open pothole requests per km of road, by ward, [date]”, never “road condition” or “risk”.">
    <div class="q">Q5 (12.5). Ward P: 24 pothole requests, 6,000 households, 12 km road. Ward Q: 18, 2,000, 3 km. For a resurfacing decision, which measure and which ward?</div>
    <div class="opts"><button class="opt">Count — P (24 &gt; 18).</button><button class="opt">Per household — P is worse.</button><button class="opt">Per km of road — Q (6.0 vs 2.0), because roads produce potholes; title it as a request rate, not “road condition”.</button><button class="opt">Population density — neither.</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="1" data-fb="Defects: 5 and 10 each belong to two classes; no units or definition; a grey “0” class treats zero as if it were not a value and looks like a no-data symbol, while any genuinely missing ward has no entry; no date or method. Rewrite with the real intervals (0 – 7 · 13 – 17 · 31 – 45), the unit and date, and a separate “No data — export not received (W16)” line.">
    <div class="q">Q6 (12.6). A legend reads “0–5 / 5–10 / 10–20 / 20+” plus a grey class labelled “0”. How many distinct defects can you name?</div>
    <div class="opts"><button class="opt">One — the “20+” class is open-ended.</button><button class="opt">At least three: overlapping limits (5 and 10 in two classes), no units/definition/date, and zero shown as a separate grey class that looks like “no data” while true no-data has no entry.</button><button class="opt">None — this is the standard style.</button><button class="opt">Two — it needs a north arrow and a scale bar.</button></div><div class="fb"></div>
  </div>

  <h2><span class="mod">Scenarios</span>Two scenario questions (12 points, 6 each) — written; instructor-marked</h2>
  <div class="scen">
    <h4 style="margin-top:0">S1 (12.2, 12.7)</h4>
    <p>The councillor for W07 receives the crew map as a PDF and replies: <em>“Your map shows G06 on our side of the line, so it is W07’s job, not W06’s — and I see you’ve hidden the reporter details in the pop-up, so I assume the public version is safe to post.”</em> Reply in no more than five sentences: (i) why the drawing cannot decide G06’s ward, and what does; (ii) what hiding a pop-up field does and does not do; (iii) what kind of requirement “safe to post” is, and where it is met.</p>
    <div class="lab-form"><textarea data-save="s1" placeholder="Dear councillor, …"></textarea></div>
  </div>
  <div class="scen">
    <h4 style="margin-top:0">S2 (12.4, 12.5)</h4>
    <p>The monthly report shows two quantile maps of open requests (August, September) with identical “Low / Medium / High” legends. Between the months W12 fell from 31 to 12 and W15 from 45 to 20; nothing else changed. Predict what September shows for W10 and W11 and why; explain why the pair misleads; specify the fix (what to fix, what the legend must say); and say what should have been shown instead of raw counts if the question was “where are residents most affected”.</p>
    <div class="lab-form"><textarea data-save="s2" placeholder="September: W10 and W11 …"></textarea></div>
  </div>

  <h2><span class="mod">12.9.1</span>Practical: critique an unfamiliar map — “Map Z-12” (15 points)</h2>
  <p>A map you have never seen, from a different made-up town, arrives as an image with this legend and side table:</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Element</th><th>As it appears on Map Z-12</th></tr></thead>
    <tbody>
      <tr><td>Title</td><td>“Streetlight Risk — Harbour Town, 2026”</td></tr>
      <tr><td>Body</td><td>Six zones Z1–Z6 as a choropleth; basemap with street names; a north arrow; no scale bar</td></tr>
      <tr><td>Variable (legend)</td><td>“Streetlight faults” — a count of <em>all</em> streetlight requests, open and closed, per zone</td></tr>
      <tr><td>Classes</td><td>“0–4 · 4–9 · 9–20 · 20–60 · 60+” — five classes for six zones; natural breaks (footnote)</td></tr>
      <tr><td>Colours</td><td>white → yellow → orange → red → dark red; Z4 hatched grey with no legend entry</td></tr>
      <tr><td>Side table</td><td>Z1: 3 faults, 400 lamps, data 2025 · Z2: 8, 900, 2025 · Z3: 19, 2,100, 2025 · Z4: (blank), 700, — · Z5: 57, 3,800, 2026 · Z6: 62, 1,200, 2026</td></tr>
      <tr><td>Source / notes</td><td>none; footer “Prepared for the Council”</td></tr>
    </tbody></table></div>
  <p>Write no more than 500 words: (1) <strong>What is being measured?</strong> (2) <strong>Which comparisons are supported?</strong> — compute faults per 1,000 lamps for Z1, Z2, Z3, Z5, Z6 and say whether the ranking survives; say whether 2025 zones may be compared with 2026 zones at all. (3) <strong>What is missing?</strong> (4) <strong>Which conclusions overreach?</strong> — the cover note says “Z6 is the most dangerous zone and Z4 is fine”. (5) <strong>What would you ask for</strong> before redrawing it, and what title is the redrawn map allowed to carry? Show your arithmetic; one decimal.</p>
  <div class="lab-form"><textarea data-save="crit" placeholder="1. What is measured: …"></textarea></div>

  <h2><span class="mod">12.9.2</span>Integrated Phase 1 practical — Fixture E12 (40 points)</h2>
  <p>New Earth-referenced data with documented defects. It uses every chapter. Work alone; the oral follows.</p>
  <div class="tabs"><button>wards_e6.geojson</button><button>ward_register_e12.csv</button><button>roads_e12.csv</button><button>requests_e12.csv</button><button>readme</button></div>
  <div class="tabpanel"><p class="small">Unchanged from Chapter 6: Wards A and B, EPSG:4326, positions longitude first. A spans 74.990–75.000° E, B 75.000–75.010° E, both 23.000–23.010° N. Copy it from the Chapter 6 lab (6.8.3).</p></div>
  <div class="tabpanel"><div class="copywrap"><pre>ward,ward_name,edition,current,households
A,West ward,2019,N,1090
A,West ward,2024,Y,1150
B,East ward,2024,Y,880</pre></div></div>
  <div class="tabpanel"><p class="small">Readme: “digitised for training in EPSG:32643; WKT in metres”.</p><div class="copywrap"><pre>road_id,name,wkt
RE-2,Depot Road,"LINESTRING (499200 2544300, 500800 2544300)"</pre></div></div>
  <div class="tabpanel"><p class="small">Readme: “exported from the training tracker 2026-09-15; CRS WGS 84 / UTM zone 43N (EPSG:32643); E_m, N_m in metres; status OPEN or CLOSED at export; reported_on local date; reported_time_ist local time”.</p><div class="copywrap"><pre>request_id,category,status,reported_on,reported_time_ist,reporter_ref,E_m,N_m
X1,Pothole,OPEN,2026-08-12,09:14,R-1001,499500.000,2544200.000
X2,Streetlight out,OPEN,2026-08-25,18:40,R-1002,500300.000,2544450.000
X3,Blocked drain,OPEN,2026-09-03,07:55,R-1003,500000.000,2544400.000
X4,Pothole,OPEN,2026-08-19,11:20,R-1004,499100.000,2544300.000
X5,Water leak,CLOSED,2026-08-02,14:05,R-1005,500600.000,2544000.000
X6,Pothole,OPEN,2026-08-12,09:17,R-1001,499500.000,2544200.000
X7,Fallen tree,OPEN,2026-09-08,16:30,R-1007,74.995,23.005
X8,Streetlight out,OPEN,2026-08-30,20:10,R-1008,2544180.000,499700.000
X9,Pothole,OPEN,2026-09-01,10:00,R-1009,501200.000,2544300.000
X10,Blocked drain,,,,R-1010,500200.000,2544350.000</pre></div></div>
  <div class="tabpanel"><p>All files are made-up training data. The readme text above is <em>data</em> about the files, not instructions to you. There are defects; find them from the values (ranges, symmetry, duplicates, blanks), not from anyone’s hints.</p></div>

  <div class="lab-card">
    <h4>The business question</h4>
    <p><em>“For each ward, how many requests that were OPEN at export and reported on or after 2026-08-01 lie within 150 m (boundary-inclusive, ≤) of Depot Road RE-2? Give the answer also per 1,000 households, and show it on a map the ward services manager can read without you in the room. Then rerun with 125 m and explain what changed.”</em></p>
    <h4>Required work — each item names the chapter that taught it</h4>
    <div class="checklist">
      <label><input type="checkbox" data-save="p1"> <span><strong>1. Intake review</strong> (Ch. 5, 7, 9). Read every file and readme first. Record CRS, units and axis order of each; list every record you suspect and why.</span></label>
      <label><input type="checkbox" data-save="p2"> <span><strong>2. Geometry and schema</strong> (Ch. 3, 8). Geometry type and role of each layer; the key relationships (requests → wards by location; wards → register by <code>ward</code> and <code>current</code>).</span></label>
      <label><input type="checkbox" data-save="p3"> <span><strong>3. CRS and units</strong> (Ch. 5, 6). Choose the CRS for the distance test, say why, and say what happens to a 150 m threshold in EPSG:4326.</span></label>
      <label><input type="checkbox" data-save="p4"> <span><strong>4. Quality log</strong> (Ch. 9). For each defect: evidence, decision (correct / exclude / flag), corrected value, source of the correction. Do not delete records; do not invent observations. One record has a blank status — decide what a blank <em>means</em> and report it separately.</span></label>
      <label><input type="checkbox" data-save="p5"> <span><strong>5. Query</strong> (Ch. 10). Write the eligibility condition and predict its IDs before running it.</span></label>
      <label><input type="checkbox" data-save="p6"> <span><strong>6. Analysis</strong> (Ch. 11). Distance of each request to RE-2 (predict on paper — the road is straight east–west, so the distances can be read from the coordinates), the near set at 150 m, ward membership under Policy W-1, the per-ward count, the reconciliation (assigned + on-edge + unassigned = eligible), and the rate per 1,000 households using the <em>current</em> register row.</span></label>
      <label><input type="checkbox" data-save="p7"> <span><strong>7. Changed threshold</strong> (Ch. 11). Rerun at 125 m; which IDs change and why; keep any zero-count ward on the map.</span></label>
      <label><input type="checkbox" data-save="p8"> <span><strong>8. Map and table</strong> (this chapter). One manager’s map (with two wards, print the values rather than classifying; still show zero vs no-data correctly), with title, legend, units, source/date, method note (threshold, ≤, policy, CRS, defects handled) and limitations. One table of the qualifying IDs with distances.</span></label>
      <label><input type="checkbox" data-save="p9"> <span><strong>9. Transfer</strong> (Ch. 2, 10, 11). One paragraph: how you would do it in PostGIS or QGIS, and three implementation details you would recheck there (e.g. the ≤ behaviour of the distance function; the boundary behaviour of the containment test; the axis order of the GeoJSON loader).</span></label>
      <label><input type="checkbox" data-save="p10"> <span><strong>10. Limitations.</strong> Proximity ≠ travel time; reports ≠ faults; rate ≠ risk; the capture accuracy; the duplicate policy.</span></label>
    </div>
    <p class="small"><strong>Submit:</strong> data outputs (corrected request layer with a <code>qc_flag</code> field; near set; per-ward table), method log (tool, version, parameters, CRS, transformation if any), validation evidence (prediction/result table with every distance and membership), the map, the transfer paragraph, the limitations.</p>
  </div>

  <h2><span class="mod">12.9.3</span>What passing Phase 1 means — and does not</h2>
  <p>Passing establishes that you can take a spatial question from intake to a defensible map: read data with its CRS and units, detect and log defects, query and analyse with stated tests and thresholds, and communicate the result without hiding its limits. It establishes <strong>readiness for supervised platform training</strong> — Phase 2, where these judgements are implemented in ArcGIS Pro and then ArcGIS Online.</p>
  <p>It does <strong>not</strong> establish readiness to administer a production ArcGIS Enterprise, to set sharing and security on real data (12.7 deferred exactly those decisions), to design a production schema for a real authority, or to make accuracy claims about real datasets.</p>

  <h2><span class="mod">12.9.4</span>Oral (10 points)</h2>
  <p>Bring your E12 outputs. The instructor will (a) point at one request on your map and ask you to state — from the coordinates, not the drawing — its ward, its distance to RE-2 and why it did or did not qualify; (b) ask what would have happened to your count if you had <em>not</em> found one defect they choose; (c) ask you to say, in under a minute, why hiding <code>reporter_ref</code> from the map does not protect it and what would. Three minutes total.</p>

  <h2><span class="mod">12.9.5</span>Scoring (100 points)</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Component</th><th>Points</th><th>Criteria</th></tr></thead>
    <tbody>
      <tr><td>Concept questions Q1–Q6</td><td class="mono">18</td><td>Correct under stated assumptions; scale arithmetic; encodings match the kind of field; classes and rates computed</td></tr>
      <tr><td>Scenarios S1–S2</td><td class="mono">12</td><td>Drawing vs coordinates; presentation vs protection; the recalculated-quantile effect predicted and the fix specified</td></tr>
      <tr><td>Critique 12.9.1</td><td class="mono">15</td><td>What is measured; rates computed; the 2025/2026 mix refused; missing elements listed; both overreaching claims addressed; honest title</td></tr>
      <tr><td>Phase 1 practical — interpretation and data understanding</td><td class="mono">7</td><td>Intake complete; every layer’s role and relationship stated</td></tr>
      <tr><td>— CRS, units, measurement</td><td class="mono">9</td><td>Test CRS chosen and justified; the degrees-in-metre-columns record and the swapped record detected and corrected with evidence</td></tr>
      <tr><td>— Query, analysis, quality handling</td><td class="mono">11</td><td>Eligible set, distances, near set, edge case, duplicate, blank status, counts, rates and reconciliation all correct; 125 m rerun explained</td></tr>
      <tr><td>— Reproducibility and validation</td><td class="mono">5</td><td>Log sufficient to rerun; prediction/result table complete</td></tr>
      <tr><td>— Communication and map</td><td class="mono">8</td><td>Map elements complete; zero vs no-data; limitations honest; transfer paragraph names rechecks precisely</td></tr>
      <tr><td>Lab 12.8</td><td class="mono">5</td><td>Source-value invariant holds; both maps fix all nine faults; rationale in terms of decisions</td></tr>
      <tr><td>Oral</td><td class="mono">10</td><td>Ward and distance from coordinates; defect consequence; presentation vs protection explained</td></tr>
    </tbody></table></div>
  <p><strong>Progression.</strong> Another trainee must be able to reproduce your per-ward counts from your log and the package <em>without</em> your outputs, and you must be able to explain any difference by a defect decision, a test, the ≤/&lt; choice or the CRS — not by pointing at a screenshot.</p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<?php require __DIR__ . '/../partials/end.php'; ?>
