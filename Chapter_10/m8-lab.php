<?php $page = ['title' => '10.8 Lab: query predictable geometry', 'chapter' => 10, 'module' => '10.8']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 10.8 · Guided lab · PostGIS, QGIS or ArcGIS Pro</div>
    <h1>Lab: predict every answer, then make the software agree — or explain why it does not</h1>
    <p class="lead">Three families of query on the practice grid — strict ward membership, boundary-inclusive membership, and a 300 m road-distance threshold — plus the attribute join and the spatial join from earlier modules. You predict on paper first. The software run exists to <em>test</em> your predictions and the documentation, not to produce the answer.</p>
    <div class="outcomes"><h4>Objective</h4>
      <ul><li>Fill the prediction table (10 questions) <strong>before</strong> touching software; your entries are saved in this browser and checked against the hand-computed answers.</li>
      <li>Run the <em>documented equivalents</em> in one engine and record every result, option and version.</li>
      <li>Reconcile every mismatch to one of four causes, and write a shared-boundary policy.</li></ul></div>
  </div>

  <div class="callout warn"><span class="label">Read this first</span><p>The software routes below were written from the official PostGIS, QGIS 3.44 and ArcGIS Pro 3.7 documentation and have <strong>not been run</strong> by the author. The expected numbers are hand-checked geometry (modules 10.5–10.7). Your instructor must reproduce them in the installed software before treating any route as fact; each route lists what remains to be checked. If your version differs from the one documented, write that down — it is a valid lab result.</p></div>

  <h2><span class="mod">10.8.1–10.8.2</span>Prerequisites, software and input data</h2>
  <ul>
    <li>Modules 10.1–10.7; the worksheet from 10.1.3; Chapter 9’s habit of working on a copy.</li>
    <li><strong>One of:</strong> PostgreSQL + PostGIS (Route A — the route whose predicate definitions are most completely documented); QGIS Desktop (Route B); ArcGIS Pro (Route C — every tool used is available at the Basic licence level according to its page). No ArcGIS Online sign-in, credits or downloads are needed.</li>
  </ul>
  <p>Four small CSV files. Create them exactly as shown. Coordinates are on the flat training grid in metres with <strong>no CRS</strong>; the <code>wkt</code> column is the well-known-text syntax from Chapter 3. Do <strong>not</strong> save any of this as GeoJSON — GeoJSON coordinates are degrees by definition (Chapter 7).</p>
  <div class="tabs"><button>wards_ch10.csv</button><button>roads_ch10.csv</button><button>requests_ch10.csv</button><button>ward_register_ch10.csv</button></div>
  <div class="tabpanel"><div class="copywrap"><pre>ward_code,wkt
A,"POLYGON((0 0,1000 0,1000 1000,0 1000,0 0))"
B,"POLYGON((1000 0,2000 0,2000 1000,1000 1000,1000 0))"</pre></div></div>
  <div class="tabpanel"><div class="copywrap"><pre>road_id,name,wkt
R1,Main Road,"LINESTRING(0 500,2000 500)"</pre></div></div>
  <div class="tabpanel"><p class="small">Empty cell = null.</p><div class="copywrap"><pre>request_id,x,y,category,priority,status,reported,closed,channel,ward_code,est_cost_inr
P1,200,200,Streetlight out,Medium,Open,2026-09-02,,Mobile app,A,1500
P2,800,800,Pothole,High,In progress,2026-08-28,,Phone,A,12000
P3,1200,250,Water leak,High,Open,2026-09-10,,Mobile app,B,
P4,1700,900,Pothole,Low,Resolved,2026-08-15,2026-08-20,Web form,B,0
P5,1000,500,Blocked drain,Medium,Reopened,2026-08-30,,Phone,,2500
P6,2200,500,Fallen tree,High,Closed – duplicate,2026-09-11,2026-09-12,Phone,C,</pre></div></div>
  <div class="tabpanel"><div class="copywrap"><pre>ward_code,ward_name,crew_team,valid_from,is_current
A,West ward,T-N,2019-04-01,No
A,West ward,T-N,2024-01-01,Yes
B,East ward,T-S,2019-04-01,Yes</pre></div></div>
  <div class="callout note"><span class="label">Fixture invariants — check these after loading, before any query</span><p>2 wards, 1 road, 6 requests, 3 register rows; each ward’s area = 1,000,000 m²; R1’s length = 2,000 m; the wards share the edge x = 1000 and do not overlap; P5 is exactly (1000, 500). If any fails, the fixture was built differently from its definition and every later mismatch is suspect.</p></div>

  <h2><span class="mod">10.8.3</span>Step 1 — predict (before touching software)</h2>
  <div class="try">
    <span class="tag">Your predictions</span>
    <h3>Table 10.8-A</h3>
    <p>Type IDs as a comma-separated list (e.g. <code>P1, P3</code>); for A9 and A10 type the numbers asked. Entries are saved in this browser. Press <strong>Check my predictions</strong> only when every row is filled — that is your “dated before execution” moment.</p>
    <div class="table-wrap"><table class="worksheet" id="predTab">
      <thead><tr><th>Q#</th><th>Concept</th><th>Definition to apply</th><th>Your prediction</th><th>Check</th></tr></thead>
      <tbody>
        <tr><td class="mono">A1</td><td>Strict interior, Ward A</td><td>request in A’s interior (not on its rings)</td><td><input data-save="a1" id="a1"></td><td id="a1c">—</td></tr>
        <tr><td class="mono">A2</td><td>Strict interior, Ward B</td><td></td><td><input data-save="a2" id="a2"></td><td id="a2c">—</td></tr>
        <tr><td class="mono">A3</td><td>Boundary-inclusive, Ward A</td><td>interior <em>or</em> on A’s boundary</td><td><input data-save="a3" id="a3"></td><td id="a3c">—</td></tr>
        <tr><td class="mono">A4</td><td>Boundary-inclusive, Ward B</td><td></td><td><input data-save="a4" id="a4"></td><td id="a4c">—</td></tr>
        <tr><td class="mono">A5</td><td>Boundary only (touches), either ward</td><td>on a ward boundary, in no interior</td><td><input data-save="a5" id="a5"></td><td id="a5c">—</td></tr>
        <tr><td class="mono">A6</td><td>Within 300 m of R1, inclusive (≤)</td><td>planar distance to the finite segment</td><td><input data-save="a6" id="a6"></td><td id="a6c">—</td></tr>
        <tr><td class="mono">A7</td><td>Within 300 m of R1, strict (&lt;)</td><td></td><td><input data-save="a7" id="a7"></td><td id="a7c">—</td></tr>
        <tr><td class="mono">A8</td><td>Combined: status ∈ {Open, Reopened} ∧ (A3 or A4) ∧ A6</td><td>10.7.1</td><td><input data-save="a8" id="a8"></td><td id="a8c">—</td></tr>
        <tr><td class="mono">A9</td><td>Attribute join requests → register on ward_code, keep all: <strong>row count</strong></td><td>10.3.1</td><td><input data-save="a9" id="a9" placeholder="a number"></td><td id="a9c">—</td></tr>
        <tr><td class="mono">A10</td><td>Spatial join target = wards, join = requests, inclusive, one-to-one: <strong>Join_Count A, B</strong></td><td>10.7.3</td><td><input data-save="a10" id="a10" placeholder="e.g. 2, 4"></td><td id="a10c">—</td></tr>
      </tbody></table></div>
    <div class="controls"><button class="btn primary" id="predCheck">Check my predictions</button> <span class="small" id="predScore"></span></div>
  </div>

  <h2><span class="mod">10.8.4–10.8.5</span>Steps 2–3 — build the fixture and run the documented equivalents</h2>
  <p>Use this table to pick, per route, the option whose <em>documented definition</em> matches each concept. Do not run an option because its name “sounds right”.</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Concept</th><th>PostGIS</th><th>QGIS <em>Select by location</em> / <em>Select within distance</em></th><th>ArcGIS Pro <em>Select Layer By Location</em></th></tr></thead>
    <tbody>
      <tr><td>Strict interior (A1, A2)</td><td class="mono">ST_Contains(ward, req)</td><td><em>are within</em></td><td><em>Completely within</em> (or <em>Within Clementini</em> for a point)</td></tr>
      <tr><td>Boundary-inclusive (A3, A4)</td><td class="mono">ST_Covers(ward, req)</td><td><em>intersect</em> (or <em>are within</em> + <em>touch</em>, OR-ed)</td><td><em>Within</em> (or <em>Intersect</em>)</td></tr>
      <tr><td>Boundary only (A5)</td><td class="mono">ST_Touches(ward, req)</td><td><em>touch</em></td><td><em>Boundary touches</em></td></tr>
      <tr><td>Within 300 m (A6/A7)</td><td class="mono">ST_DWithin(req, road, 300)</td><td><em>Select within distance</em>, 300</td><td><em>Within a distance</em>, Search Distance 300</td></tr>
      <tr><td>Attribute join (A9)</td><td class="mono">LEFT JOIN … ON ward_code</td><td><em>Join attributes by field value</em>, METHOD 0 then 1</td><td><em>Add Join</em>, default then <em>Join one to first</em></td></tr>
      <tr><td>Spatial join count (A10)</td><td class="mono">COUNT … GROUP BY (ST_Covers)</td><td><em>Join attributes by location (summary)</em> — record its boundary behaviour</td><td><em>Spatial Join</em>, target = wards, one-to-one, keep all; read Join_Count</td></tr>
    </tbody></table></div>
  <div class="tabs"><button>Route A — PostGIS</button><button>Route B — QGIS 3.44</button><button>Route C — ArcGIS Pro 3.7</button></div>
  <div class="tabpanel">
    <p>SRID 0 is PostGIS’s “unknown” spatial reference — a pure flat plane, which is exactly what this fixture is. Distances are in coordinate units (metres, by our declaration). <em>Not execution-tested.</em></p>
    <div class="copywrap"><pre>CREATE TABLE wards (ward_code text PRIMARY KEY, geom geometry(Polygon, 0));
INSERT INTO wards VALUES
  ('A', ST_GeomFromText('POLYGON((0 0,1000 0,1000 1000,0 1000,0 0))', 0)),
  ('B', ST_GeomFromText('POLYGON((1000 0,2000 0,2000 1000,1000 1000,1000 0))', 0));
CREATE TABLE roads (road_id text PRIMARY KEY, name text, geom geometry(LineString, 0));
INSERT INTO roads VALUES ('R1', 'Main Road', ST_GeomFromText('LINESTRING(0 500,2000 500)', 0));
CREATE TABLE requests (request_id text PRIMARY KEY, category text, priority text, status text,
  reported date, closed date, channel text, ward_code text, est_cost_inr integer, geom geometry(Point, 0));
INSERT INTO requests VALUES
  ('P1','Streetlight out','Medium','Open','2026-09-02',NULL,'Mobile app','A',1500, ST_SetSRID(ST_MakePoint(200,200),0)),
  ('P2','Pothole','High','In progress','2026-08-28',NULL,'Phone','A',12000, ST_SetSRID(ST_MakePoint(800,800),0)),
  ('P3','Water leak','High','Open','2026-09-10',NULL,'Mobile app','B',NULL, ST_SetSRID(ST_MakePoint(1200,250),0)),
  ('P4','Pothole','Low','Resolved','2026-08-15','2026-08-20','Web form','B',0, ST_SetSRID(ST_MakePoint(1700,900),0)),
  ('P5','Blocked drain','Medium','Reopened','2026-08-30',NULL,'Phone',NULL,2500, ST_SetSRID(ST_MakePoint(1000,500),0)),
  ('P6','Fallen tree','High','Closed – duplicate','2026-09-11','2026-09-12','Phone','C',NULL, ST_SetSRID(ST_MakePoint(2200,500),0));
CREATE TABLE ward_register (ward_code text, ward_name text, crew_team text, valid_from date, is_current text);
INSERT INTO ward_register VALUES
  ('A','West ward','T-N','2019-04-01','No'), ('A','West ward','T-N','2024-01-01','Yes'), ('B','East ward','T-S','2019-04-01','Yes');

-- invariants
SELECT ward_code, ST_Area(geom), ST_IsValid(geom) FROM wards;      -- 1000000, t
SELECT ST_Length(geom) FROM roads;                                   -- 2000
SELECT ST_Touches(a.geom,b.geom), ST_Overlaps(a.geom,b.geom)
  FROM wards a, wards b WHERE a.ward_code='A' AND b.ward_code='B';  -- t, f

-- A1/A2 strict · A3/A4 inclusive · A5 boundary only
SELECT w.ward_code, r.request_id FROM wards w JOIN requests r ON ST_Contains(w.geom, r.geom) ORDER BY 1,2;
SELECT w.ward_code, r.request_id FROM wards w JOIN requests r ON ST_Covers(w.geom, r.geom)   ORDER BY 1,2;
SELECT w.ward_code, r.request_id FROM wards w JOIN requests r ON ST_Touches(w.geom, r.geom)  ORDER BY 1,2;
-- A6 within 300 m, with the exact distances for the A7 check
SELECT r.request_id, ST_Distance(r.geom, d.geom) AS dist_m, ST_DWithin(r.geom, d.geom, 300) AS within_300
  FROM requests r CROSS JOIN roads d ORDER BY 1;
-- A8 combined
SELECT r.request_id FROM requests r
 WHERE r.status IN ('Open','Reopened')
   AND EXISTS (SELECT 1 FROM wards w WHERE ST_Covers(w.geom, r.geom))
   AND EXISTS (SELECT 1 FROM roads d WHERE ST_DWithin(r.geom, d.geom, 300)) ORDER BY 1;
-- A9 attribute join, keep all
SELECT r.request_id, g.ward_name, g.valid_from FROM requests r
  LEFT JOIN ward_register g ON g.ward_code = r.ward_code ORDER BY 1, 3;
-- A10 spatial join count per ward (inclusive)
SELECT w.ward_code, COUNT(r.request_id) AS join_count FROM wards w
  LEFT JOIN requests r ON ST_Covers(w.geom, r.geom) GROUP BY 1 ORDER BY 1;</pre></div>
  </div>
  <div class="tabpanel">
    <p><em>Not execution-tested.</em> Add each CSV with <em>Layer ► Add Layer ► Add Delimited Text Layer</em>: for the wards and roads files choose the well-known-text geometry definition on the <code>wkt</code> field; for the requests file choose point coordinates with X = <code>x</code>, Y = <code>y</code>; add the register with no geometry. Then, from the Processing toolbox: <em>Select by location</em> (predicates per the table above), <em>Select within distance</em> (300), <em>Select by expression</em> for the status condition (use “selecting within current selection” to chain), <em>Join attributes by field value</em> with METHOD 0 and then 1, and <em>Join attributes by location (summary)</em>.</p>
    <p><strong>Verification items:</strong> the exact dialog labels, how to leave the geometry CRS <em>unset</em> (a non-Earth, planar layer) and confirm the project CRS does not silently reproject; the units <em>Select within distance</em> uses (layer units); whether <em>are within</em> excludes P5 as its documented definition implies; and what the “summary” join does with P5. Check the invariants with the field calculator (<code>$area</code>, <code>$length</code>).</p>
  </div>
  <div class="tabpanel">
    <p><em>Not execution-tested.</em> Requests: <em>XY Table To Point</em> with X Field <code>x</code>, Y Field <code>y</code>, and the <em>Coordinate System</em> parameter <strong>cleared</strong> so the output has an unknown spatial reference (Chapter 3 recorded that the tool’s default is WGS 84, which would label metres as degrees). Wards and road: open the GeoPackage built in Route B, or create the two polygons and the line from the WKT with the ArcPy <code>FromWKT</code> insert-cursor approach from Chapter 3’s Route C, or digitise them by typing absolute coordinates (Chapter 9). Register: add the CSV as a stand-alone table. Then <em>Select Layer By Attribute</em>, <em>Select Layer By Location</em> (options per the table; Selection Type “Select subset from the current selection” to chain), <em>Add Join</em>, and <em>Spatial Join</em>.</p>
    <p><strong>Verification items:</strong> whether <em>Select Layer By Location</em>’s Search Distance accepts a value with the unit <em>Unknown</em> for an unknown-CRS layer (if not, the instructor’s fallback is a documented local engineering CRS in metres — never a real geographic CRS, which would make the metre values wrong); the P5 result under <em>Within</em>, <em>Completely within</em>, <em>Within Clementini</em> and <em>Boundary touches</em>; the exact-300 m behaviour for P1/P2 (also run <em>Near</em> and confirm NEAR_DIST 300.000 and 200.000 for P6); Spatial Join’s default text merge rule; and whether a CSV register (no ObjectID) only allows <em>Join one to first</em>.</p>
  </div>

  <h2><span class="mod">10.8.6–10.8.7</span>Steps 4 — reconcile, validate, troubleshoot</h2>
  <p>For each result that differs from your prediction, write one of the four causes and the evidence:</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Cause</th><th>How it shows on this fixture</th><th>What to do</th></tr></thead>
    <tbody>
      <tr><td><strong>Predicate meaning</strong></td><td>P5 appears under a “within” you expected to be strict (Esri <em>Within</em> is inclusive); or P5 is missing from an option you expected to be inclusive (QGIS <em>are within</em>, PostGIS <code>ST_Within</code>)</td><td>Not an error. Correct the <em>mapping</em>, keep the result — this is the lab’s central finding</td></tr>
      <tr><td><strong>CRS / units</strong></td><td>Distances come out ≈ 0.0027 or ≈ 33,000 instead of 300 — a geographic CRS was assigned, or the distance was read in another unit</td><td>Rebuild with no CRS (or the documented local CRS); log the mistake</td></tr>
      <tr><td><strong>Tolerance</strong></td><td>ArcGIS applies the feature class x,y tolerance client-side; a fixture rebuilt with rounded coordinates might put P5 a millimetre inside one ward</td><td>Inspect P5’s stored coordinates to full precision</td></tr>
      <tr><td><strong>Fixture construction</strong></td><td>An invariant fails: area ≠ 1,000,000, length ≠ 2,000, P5 ≠ (1000, 500), or the wards overlap</td><td>Fix the fixture, re-check invariants, rerun everything</td></tr>
    </tbody></table></div>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">Validation checks (no answer key needed)</h4><ol style="margin:0">
      <li>For each ward: strict set + touches set = inclusive set.</li>
      <li>Under the inclusive rule, total matches − distinct matched requests = number of boundary requests (1).</li>
      <li>A7 ⊆ A6, and A6 − A7 is exactly the requests at 300.000 m (P1, P2). If it is empty, your engine reads the threshold as strict <em>or</em> your distances are not exactly 300 — compute them to full precision to tell which.</li>
      <li>A8 ⊆ the status set, ⊆ A3 ∪ A4, ⊆ A6; counts in a chained selection never increase.</li>
      <li>A9 keep-all rows = 6 + extra register rows matched; requests with no ward name = 2.</li>
      <li>Join_Count total = A3 + A4 total; distinct = 5.</li></ol></div>
    <div class="card"><h4 style="margin-top:0">Troubleshooting</h4><div class="table-wrap"><table>
      <tbody>
        <tr><td>Every request is “within 300”, even P4</td><td>Threshold in a bigger unit, or degrees assigned</td></tr>
        <tr><td>P6 shows distance 0</td><td>The road was not built as a finite segment — check the WKT and length</td></tr>
        <tr><td>P5 in neither ward under every option</td><td>All chosen options strict, or P5 stored as (1000.0001, 500)</td></tr>
        <tr><td>P5 in both wards under every option</td><td>Expected for inclusive options — now run a strict one</td></tr>
        <tr><td>Join returns 6 rows and P1 has the 2019 row</td><td>One-to-first picked “the first”; rerun one-to-many or filter to current</td></tr>
        <tr><td>Join drops P5 and P6</td><td>Unmatched rows discarded by the setting — record both counts</td></tr>
        <tr><td>Tool warns “unknown coordinate system”</td><td>Expected; decline any offer to assign a CRS (Chapter 5)</td></tr>
      </tbody></table></div></div>
  </div>

  <h2><span class="mod">10.8.8</span>Step 5 — the shared-boundary policy, and deliverables</h2>
  <div class="lab-form">
    <label for="policy">Write a policy (half a page at most) a colleague could apply without you. It must state: the predicate used for membership (strict or inclusive, by its documented name in your engine); the rule for a request on a shared boundary; the rule for a request outside all wards; the rule for a request in a hole; whether the deliverable is a unique assignment or a match list; and how the policy result is labelled so it is never confused with the raw geometric result.</label>
    <textarea id="policy" data-save="policy" style="min-height:160px" placeholder="Membership predicate: … Boundary rule: … Outside rule: … Hole rule: … Deliverable: unique assignment / match list. Labelling: …"></textarea>
    <label for="engine">Engine, version and exact options / SQL used</label>
    <textarea id="engine" data-save="engine" placeholder="e.g. PostGIS 3.4 on PostgreSQL 16; ST_Covers for A3/A4; ST_DWithin(…, 300) returned P1, P2 = … "></textarea>
    <label for="recon">Reconciliation notes (one of the four causes per mismatch), including the exact-300 m observation for P1/P2</label>
    <textarea id="recon" data-save="recon"></textarea>
  </div>
  <div class="lab-card"><h4>Deliverables checklist</h4><div class="checklist">
    <label><input type="checkbox" data-save="d1"> Table 10.8-A with my predictions, dated before Step 3</label>
    <label><input type="checkbox" data-save="d2"> Executed results with engine, version, exact options/SQL, and reconciliation notes (including P1/P2 at exactly 300 m)</label>
    <label><input type="checkbox" data-save="d3"> The invariant checks with their values</label>
    <label><input type="checkbox" data-save="d4"> The shared-boundary policy and the resulting assignment table, beside the raw match counts (total, distinct, unmatched)</label>
    <label><input type="checkbox" data-save="d5"> A three-line note on any documentation I found ambiguous and what I tested to resolve it</label>
  </div><p class="small">A screenshot alone is not a deliverable: it shows a result without showing the condition or the option that produced it.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const R = FIXTURE.requests;
  const ids = fn => R.filter(fn).map(r => r.id);
  const inA = m => r => wardsOf(r.x, r.y, m).includes("A"), inB = m => r => wardsOf(r.x, r.y, m).includes("B");
  const expect = {
    a1: ids(inA("strict")), a2: ids(inB("strict")), a3: ids(inA("inclusive")), a4: ids(inB("inclusive")), a5: ids(r => wardsOf(r.x, r.y, "touch").length > 0),
    a6: ids(r => distToRoad(r.x, r.y) <= 300 + 1e-9), a7: ids(r => distToRoad(r.x, r.y) < 300 - 1e-9),
    a8: ids(r => ["Open", "Reopened"].includes(r.status) && wardsOf(r.x, r.y, "inclusive").length > 0 && distToRoad(r.x, r.y) <= 300 + 1e-9),
    a9: [String(joinRows(R, FIXTURE.register, "ward_code", "ward_code", true).length)],
    a10: [String(ids(inA("inclusive")).length), String(ids(inB("inclusive")).length)]
  };
  const parse = s => (s || "").toUpperCase().split(/[,\s]+/).filter(Boolean);
  document.getElementById("predCheck").addEventListener("click", () => {
    let ok = 0;
    Object.keys(expect).forEach(k => {
      const mine = parse(document.getElementById(k).value), want = expect[k].map(s => String(s).toUpperCase());
      const same = k === "a10" ? mine.join(",") === want.join(",") : mine.length === want.length && [...mine].sort().every((v, i) => v === [...want].sort()[i]);
      const c = document.getElementById(k + "c"); c.textContent = same ? "✓ matches the hand-checked answer" : `✗ hand-checked: ${expect[k].join(", ") || "(none)"}`; c.className = same ? "ok" : "bad"; if (same) ok++;
    });
    document.getElementById("predScore").textContent = `${ok} of 10 match. For any ✗, first re-read the definition column — the usual cause is “inclusive vs strict”, not arithmetic.`;
  });
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
