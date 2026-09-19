<?php $page = ['title' => '9.8 Lab: inspect a deliberately damaged dataset', 'chapter' => 9, 'module' => '9.8']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 9.8 · Guided lab · ArcGIS Pro (primary) or QGIS · paper route available</div>
    <h1>Lab: the damaged town — classify, correct, and leave the rest open</h1>
    <p class="lead">You receive the full damaged package. Complete the intake checklist, run the shape checks, the rule checks and the five table checks, classify every finding as <strong>C</strong> (correct with evidence), <strong>O</strong> (needs the owner) or <strong>X</strong> (exception / false alarm), apply only the C corrections on a working copy with before/after records, and deliver a defect log, a corrected copy and an unresolved-issues list.</p>
    <div class="outcomes"><h4>Objective</h4>
      <ul><li>Nine real defects found and classified; three items left open with the owner’s question written; no valid feature “repaired”.</li>
      <li>Corrected copy <code>Chapter09_Corrected_v1/</code> with nothing deleted and nothing invented.</li>
      <li>A log a second person can check against the expected results below.</li></ul></div>
  </div>

  <div class="callout warn"><span class="label">Read this first</span><p>The software steps below were written from the official ArcGIS Pro 3.7 and QGIS 3.44 documentation and have <strong>not been run</strong> by the author. The expected values are hand-checked arithmetic on the printed data. Your instructor must run the lab once — especially to record what Repair Geometry does to Ward C — before you rely on any tool behaviour (chapter document, Instructor Appendix I.6).</p></div>

  <h2><span class="mod">9.8.1–9.8.2</span>Software and input data</h2>
  <ul>
    <li><strong>Primary route:</strong> ArcGIS Pro 3.x, any licence level for the tools used (Check Geometry and Repair Geometry work on file data at Basic). A geodatabase <em>topology</em> needs Standard or Advanced and is optional.</li>
    <li><strong>Alternative:</strong> QGIS Desktop 3.44 with the core Topology Checker plugin enabled.</li>
    <li><strong>Paper route:</strong> graph paper and a spreadsheet. Every check here can be done by hand — the expected values were.</li>
    <li>No ArcGIS Online account, credits, GNSS receiver, scanner or locator is needed.</li>
  </ul>
  <p>Seven CSV files, exactly as in the Chapter 3 town package: shapes as a <code>wkt</code> column for lines and areas, <code>x</code>, <code>y</code> columns for points. Flat practice grid, metres, <strong>no coordinate system</strong>. <span class="synthetic">Made-up data</span>. <strong>Every mistake is deliberate — do not tidy while copying.</strong> Your instructor converts them to a GeoPackage or file geodatabase by the Chapter 3 routes; if no built package is available, every check can still be done from these texts.</p>
  <div class="tabs"><button>wards_d.csv</button><button>roads_d.csv</button><button>assets_d.csv</button><button>inspections_d.csv</button><button>requests_d.csv</button><button>teams.csv</button><button>depot.csv</button><button>readme</button></div>
  <div class="tabpanel"><div class="copywrap"><pre>ward,name,wkt
A,Ward A,"POLYGON((0 0,1000 0,1000 1000,0 1000,0 0))"
B,Ward B,"POLYGON((1004 0,2000 0,2000 1000,1000 1000,1004 0))"
C,Ward C,"POLYGON((0 1000,1000 1000,0 1500,1000 1500,0 1000))"</pre></div></div>
  <div class="tabpanel"><div class="copywrap"><pre>road_id,name,width_m,wkt
R1,Main Road,12,"LINESTRING(0 500,2000 500)"
R2,Station Road,7,"LINESTRING(500 0,500 500,800 900)"
L1,Bus turning loop,6,"LINESTRING(1300 300,1350 300,1350 350,1300 350,1300 300)"
R4,Temple Lane,5,"LINESTRING(300 300,300 497)"
R5,Depot Access,6,"LINESTRING(1500 500,1500 600)"</pre></div></div>
  <div class="tabpanel"><p class="small">Blank <code>pole_height_m</code> is blank in the file; drains and trees have no pole.</p><div class="copywrap"><pre id="assetsPre"></pre></div></div>
  <div class="tabpanel"><p class="small"><code>visited_at_utc</code> is the event time in UTC (IST = UTC + 5:30), as designed in Chapter 8.</p><div class="copywrap"><pre id="inspPre"></pre></div></div>
  <div class="tabpanel"><p class="small">P7 has blank coordinates on purpose; times are IST as exported by the request system.</p><div class="copywrap"><pre id="reqPre"></pre></div></div>
  <div class="tabpanel"><div class="copywrap"><pre>team_id,team_name,active
T-N,North crew,true
T-S,South crew,true</pre></div></div>
  <div class="tabpanel"><p class="small">Unchanged from Chapter 3; included so R5’s end can be checked against the depot gate.</p><div class="copywrap"><pre>depot_id,name,wkt
DP-01,Central depot,"POLYGON((1400 600,1600 600,1600 800,1400 800,1400 600),(1450 650,1550 650,1550 750,1450 750,1450 650))"</pre></div></div>
  <div class="tabpanel"><div class="copywrap"><pre>Chapter09_Damaged — readme (made-up data)
Wards A and B are the Chapter 3 Town boundaries; Ward C was digitised in September 2026
from the ward register plan. The register states: Ward A 1.00 km2; Ward B 1.00 km2, sharing
its entire western boundary with Ward A; Ward C 0.50 km2, the rectangle north of Ward A
between y = 1000 and y = 1500.
Roads R1, R2, L1 are the Chapter 3 Town roads; R4 and R5 were digitised in September 2026
from the road register: R4 "Temple Lane, temple to junction with Main Road";
R5 "Depot Access, Main Road to depot gate, no through route".
Assets are the Chapter 8 lab conversion, with install years supplied by the asset register,
plus one new streetlight recorded by a field crew with a phone in 2025. The Chapter 8
conversion log recorded SL-0127's height as 22 ft = 6.7056 m and its type as Streetlight;
the owner has confirmed that heights 6.5, 7 and 8 are metres. SL-0113's height has not
been measured. INS-0001..INS-0004 are Chapter 8's; INS-0005..INS-0007 were typed from
paper forms in Aug-Sep 2026. Requests P1..P7 are Chapter 3's; P8 and P9 arrived through
the mobile app in September 2026.
Coordinates: training grid, metres; no CRS.
Domains in force: asset_type {SL, DR, TR}; status {ACTIVE, REMOVED, MERGED};
location_method {SURVEY, GNSS, DIGITISED, APPROX}; condition 1-5; pole_height_m 3-15;
request status {Open, In progress, Resolved, Reopened, Closed - duplicate}.</pre></div></div>

  <h2><span class="mod">9.8.3</span>Steps</h2>
  <p>Keep a lab log from the first minute. Tick each step as you do it; ticks and notes are saved in this browser only.</p>
  <div class="lab-card"><h4>Step 1 — Intake (15 min)</h4><div class="checklist">
    <label><input type="checkbox" data-save="l1a"><span>Copy the package to <code>Chapter09_Work/</code>; make the original read-only. Complete the six-item checklist (you started it in 9.1).</span></label>
    <label><input type="checkbox" data-save="l1b"><span>Record counts: wards 3, roads 5, assets 10 rows, teams 2, inspections 7, requests 9 (8 with a location). <strong>Do not edit anything yet.</strong></span></label>
  </div></div>
  <div class="lab-card"><h4>Step 2 — Shape validity (20 min)</h4><div class="checklist">
    <label><input type="checkbox" data-save="l2a"><span>Run a validity check on <code>wards_d</code>, <code>roads_d</code> and <code>depot</code>; log every reported problem with the engine and method. <em>ArcGIS Pro:</em> <strong>Check Geometry</strong> (Data Management), once with Validation Method = Esri and once = OGC; read <code>FEATURE_ID</code> and <code>PROBLEM</code>. <em>QGIS:</em> Vector ▸ Geometry Tools ▸ <strong>Check Validity</strong>, method GEOS; read the <code>_errors</code> field. <em>Paper:</em> walk each ring on graph paper.</span></label>
    <label><input type="checkbox" data-save="l2b"><span>Expected: exactly <strong>one</strong> invalid feature. Log the crossing point.</span></label>
  </div></div>
  <div class="lab-card"><h4>Step 3 — Topology and business rules (30 min)</h4><div class="checklist">
    <label><input type="checkbox" data-save="l3a"><span>Apply and log: wards <em>must not overlap</em>, wards <em>must not have gaps</em>, roads <em>must not have dangles</em>, assets <em>inside a ward</em>. For each hit decide defect or exception using the readme, and write the evidence.</span></label>
    <label><input type="checkbox" data-save="l3b"><span><em>ArcGIS Pro (any licence):</em> zoom to the A/B boundary at y = 0 and y = 500 and measure the gap; select each road and read its end coordinates. <em>Optional, Standard/Advanced:</em> feature dataset → geodatabase topology with the four rules → validate → Error Inspector; mark R5’s dangle as an exception. <em>QGIS:</em> enable Topology Checker; add the four rules; <strong>Validate All</strong>. <span class="small">Verification item: if Topology Checker reports the <em>outside</em> of the whole ward set as a gap, that is an exception — log it, do not create a polygon.</span></span></label>
  </div></div>
  <div class="lab-card"><h4>Step 4 — Table checks (30 min)</h4><div class="checklist">
    <label><input type="checkbox" data-save="l4a"><span>Run the five checks of 9.6 on assets, inspections and requests (SQL, spreadsheet filter, Select By Attributes, or QGIS expression), plus the P3/P8 and P5/P9 comparison. Log every hit <strong>and</strong> every examined false alarm.</span></label>
  </div></div>
  <div class="lab-card"><h4>Step 5 — Classify (20 min)</h4><div class="checklist">
    <label><input type="checkbox" data-save="l5a"><span>Give every log row one class: <strong>C</strong> correct with evidence (name it), <strong>O</strong> needs the owner (write the question), <strong>X</strong> exception / false alarm (name the evidence). Practise below before you commit.</span></label>
  </div></div>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Classify the findings</h3>
    <p>Pick C, O or X for each. The game checks against the instructor’s manifest.</p>
    <div class="cox" id="cox"></div>
    <div class="status-line q" id="coxMsg">0 of 12 classified.</div>
  </div>
  <div class="lab-card"><h4>Step 6 — Correct, on the working copy only (40 min)</h4><div class="checklist">
    <label><input type="checkbox" data-save="l6a"><span>For each C row: write the proposed correction (before → after) in the log <em>first</em>, then edit, then do the three reviews (9.4.3), then complete the before/after record.</span></label>
    <label><input type="checkbox" data-save="l6b"><span>Use snapping for R4 with the agent and tolerance recorded; re-order Ward C’s corners <em>by hand</em> (typed coordinates preferred). <strong>Do not run Repair Geometry / Fix geometries as the correction for Ward C.</strong> Do not touch O or X rows.</span></label>
    <label><input type="checkbox" data-save="l6c"><span><em>ArcGIS Pro:</em> Modify Features pane — vertex editing for Ward B’s stray corner and Ward C’s ring; end-point move with <strong>Edge</strong> snapping for R4; Attributes pane for SL-0127 and P8; <strong>Save</strong> after each reviewed edit. <em>QGIS:</em> Toggle Editing; Vertex Tool + Vertex Editor panel for typed coordinates; <strong>Segment</strong> snapping for R4; attribute table for SL-0127 and P8; Save Layer Edits.</span></label>
  </div></div>
  <div class="lab-card"><h4>Step 7 — Experiment on a second copy (15 min, optional)</h4><div class="checklist">
    <label><input type="checkbox" data-save="l7a"><span>Copy <code>wards_d</code> again; run the automatic repair (Repair Geometry / Fix geometries) on the <em>copy</em>; record what happened to Ward C — geometry type, part count, area. Compare with your manual fix and with 9.7.2. Label or delete the copy.</span></label>
  </div></div>
  <div class="lab-card"><h4>Step 8 — Validate and package (25 min)</h4><div class="checklist">
    <label><input type="checkbox" data-save="l8a"><span>Re-run steps 2–4 on the corrected copy. Confirm the invariants below. Confirm O and X rows are unchanged.</span></label>
    <label><input type="checkbox" data-save="l8b"><span>Save as <code>Chapter09_Corrected_v1/</code> with the log, the before/after records, the unresolved list, and a one-paragraph readme: what was corrected, what was not, which tool and version.</span></label>
  </div></div>

  <h2><span class="mod">9.8.4</span>Expected results (hand-checked; not observed in software)</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Check</th><th>Expected</th></tr></thead>
    <tbody>
      <tr><td>Validity — invalid features</td><td><strong>Exactly 1:</strong> Ward C, self-crossing at <strong>(500, 1250)</strong>. A, B, all roads (L1 is a closed <em>line</em>, valid) and the depot are valid.</td></tr>
      <tr><td>Ward B as delivered</td><td>Area <strong>998,000 m²</strong>; gap sliver <strong>2,000 m²</strong>; at y = 500 the B edge is at x = <strong>1002</strong>, so P5 (1000, 500) is <em>not</em> on B’s boundary in the damaged data.</td></tr>
      <tr><td>Ward B after correction</td><td>Corner (1004, 0) → <strong>(1000, 0)</strong>; area <strong>1,000,000 m²</strong>; no gap; P5 back on the shared line.</td></tr>
      <tr><td>Ward C after correction</td><td>Ring <strong>(0 1000, 1000 1000, 1000 1500, 0 1500, 0 1000)</strong>; valid; area <strong>500,000 m²</strong>; total ward area <strong>2,500,000 m²</strong>.</td></tr>
      <tr><td>Ward C if auto-repaired (step 7 copy)</td><td>From the documented linework method: two triangles, <strong>250,000 m²</strong>. The Esri method’s result is <em>not asserted</em> — record what you see.</td></tr>
      <tr><td>Dangles</td><td><strong>R4</strong> north end (300, 497): defect → <strong>(300, 500)</strong>, length 197 → <strong>200 m</strong>. <strong>R5</strong> north end (1500, 600): exception (register; the point lies on the depot’s south edge). L1: none. R2’s south end (500, 0) is at the package edge — log as outside the evidence; do not extend.</td></tr>
      <tr><td>Assets inside a ward</td><td><strong>TR-0301</strong> (2190, 520) outside all three wards → needs the owner. All others strictly inside A or B.</td></tr>
      <tr><td>Check 1 — duplicate IDs</td><td><strong>SL-0114 × 2</strong>, about 1,016 m apart → needs the owner.</td></tr>
      <tr><td>Check 2 — invalid categories</td><td><strong>SL-0127 type “Lamp”</strong> → SL (readme: Streetlight; SL- prefix). No invalid status, method or request-status values.</td></tr>
      <tr><td>Check 3 — missing observation</td><td><strong>INS-0005</strong> condition blank → stays blank; unresolved.</td></tr>
      <tr><td>Check 4 — range</td><td><strong>SL-0127 height 22</strong> → <strong>6.71</strong> (22 × 0.3048 = 6.7056; rounding stated). SL-0113’s blank is <em>not</em> a defect.</td></tr>
      <tr><td>Check 5 — orphans</td><td><strong>INS-0006 → DR-0044</strong> → needs the owner; kept. All team IDs resolve.</td></tr>
      <tr><td>Requests</td><td><strong>P8 duplicate of P3</strong> (same reporter R-3391, same text, 09:13/09:14, same channel): status → <em>Closed - duplicate</em>, note “Duplicate of P3”, <strong>row kept</strong>. <strong>P5/P9: two reports</strong> — no change. P7: Chapter 3’s known case — no change.</td></tr>
      <tr><td>Counts after correction</td><td>Wards 3; roads 5; assets <strong>10 rows</strong> (the SL-0114 pair is unresolved); inspections <strong>7</strong>; requests <strong>9</strong> — <strong>nothing deleted</strong>.</td></tr>
      <tr><td>Log</td><td>≥ 9 defect rows (Ward C; Ward B gap; R4; SL-0114 pair; SL-0127 type; SL-0127 height; INS-0005; INS-0006; P8) and ≥ 3 exception rows (R5; P5/P9; the outer boundary if reported), plus the R2 edge note; unresolved list has <strong>4</strong> entries (SL-0114 pair; INS-0006; INS-0005; TR-0301).</td></tr>
    </tbody></table></div>
  <p class="small"><strong>Tolerance:</strong> all coordinates are whole metres and every expected value is exact flat arithmetic; the only rounded value is 6.7056 → 6.71 m. A tool may show an area with decimals or a minus sign (a clockwise ring) — same shape. If Ward C’s damaged area shows as anything but 0, write it down: it is engine-dependent and meaningless.</p>

  <h2><span class="mod">9.8.5</span>Troubleshooting</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Symptom</th><th>Likely cause</th><th>Fix</th></tr></thead>
    <tbody>
      <tr><td>Validity check flags Ward A or B for “ring orientation”</td><td>Esri method expects clockwise outer rings; the CSV rings are anticlockwise</td><td>Not a defect (Chapter 3); log it as an engine convention; confirm with the OGC method</td></tr>
      <tr><td>No problem reported for Ward C</td><td>Wrong layer, or the WKT was “tidied” while typing</td><td>Ward C’s 2nd corner must be (1000, 1000) and its 3rd (0, 1500)</td></tr>
      <tr><td>Topology Checker reports a gap around the <em>outside</em> of the wards</td><td>The rule sees the town’s outer edge as a void</td><td>Exception; log it; do not create a polygon</td></tr>
      <tr><td>Your corrected Ward C is 250,000 m²</td><td>You ran the auto-repair instead of re-ordering corners</td><td>Restore from the original; re-read 9.7.2; fix by hand</td></tr>
      <tr><td>R4 did not snap</td><td>Pixel tolerance too small at your zoom</td><td>Zoom out, use map units, or type (300, 500)</td></tr>
      <tr><td>R4 snapped but R1 now has an extra vertex</td><td>Segment/edge snapping added a node</td><td>Not wrong; log it and note the connectivity consequence</td></tr>
      <tr><td>You changed INS-0006 to DR-0043</td><td>“Nearby ID” reasoning</td><td>Restore; re-read 9.6.1; move to the unresolved list</td></tr>
      <tr><td>You deleted P8</td><td>“Duplicate” treated as “delete”</td><td>Restore; set status and note (Chapter 3’s P6 convention)</td></tr>
      <tr><td>You gave INS-0005 a condition of 3</td><td>Fabrication</td><td>Restore the blank; 9.7.3</td></tr>
      <tr><td>You extended R5 or split R1 under it</td><td>“Fix every error”</td><td>Restore; 9.5.3</td></tr>
      <tr><td>Points were built with WGS 84 assigned</td><td>Tool default (Chapter 3 build notes)</td><td>Rebuild with no CRS; never “fix” this later with Define Projection</td></tr>
      <tr><td>Asset count is 9 after correction</td><td>You merged or deleted one SL-0114</td><td>Restore; the pair is class O</td></tr>
    </tbody></table></div>

  <h2><span class="mod">9.8.6</span>Deliverables</h2>
  <ol>
    <li><strong>Intake checklist</strong> (six items) and the initial counts.</li>
    <li><strong>Defect log</strong> with every finding classified C / O / X and evidence named.</li>
    <li><strong>Before/after records</strong> for every C correction, including the old Ward C ring as WKT and the snapping settings used for R4.</li>
    <li><strong>Corrected copy</strong> <code>Chapter09_Corrected_v1/</code> (built package <em>and</em> re-exported CSV) with a readme naming software and version, and stating that O and X rows are unchanged.</li>
    <li><strong>Unresolved-issues list</strong> — four entries, each with the owner’s question and the evidence that would settle it.</li>
    <li><strong>Validation record</strong> — steps 2–4 re-run on the corrected copy with the invariants ticked, and the step-7 result if you did it.</li>
  </ol>
  <div class="callout idea"><span class="label">The two checks that cannot fail</span><p>No valid feature was “repaired” (R5, P5/P9, L1, R2 untouched; Ward C is the rectangle, not the triangles) — and nothing was invented (INS-0005 blank; INS-0006 kept as an orphan; both SL-0114 rows present).</p></div>
  <div class="lab-form"><label>Your lab notes (saved in this browser)</label><textarea data-save="labnotes" placeholder="Tool and version · what each check reported · classification · before/after · open items"></textarea></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  document.getElementById("assetsPre").textContent = "asset_id,asset_type,x,y,install_year,status,location_method,pole_height_m\n" + T9.assets.map(a => [a.id, a.type, a.x, a.y, a.year, a.status, a.method, a.h ?? ""].join(",")).join("\n");
  document.getElementById("inspPre").textContent = "inspection_id,asset_id,visited_at_utc,recorded_on,team_id,condition_code,defects_found,remarks\n" + T9.inspections.map(i => [i.id, i.asset, i.at, i.entered, i.team, i.cond ?? "", i.defects, i.remarks].join(",")).join("\n");
  document.getElementById("reqPre").textContent = "request_id,x,y,category,priority,status,reported_on,reported_time_ist,reporter_ref,closed_on,channel,note\n" + T9.requests.map(q => [q.id, q.x ?? "", q.y ?? "", q.cat, q.pri, q.status, q.on, q.time, q.who, q.closed, q.via, q.note].join(",")).join("\n");
  const items = [
    { t: "Ward C crosses itself at (500, 1250)", a: "C", why: "register says a rectangle; re-order the corners by hand" },
    { t: "Ward B leaves a 4 m sliver next to Ward A", a: "C", why: "register: whole boundary shared; A’s east edge is x = 1000" },
    { t: "R4 stops 3 m short of Main Road", a: "C", why: "register: joins Main Road" },
    { t: "R5 ends at the depot gate", a: "X", why: "register: no through route" },
    { t: "Two rows called SL-0114, 1 km apart", a: "O", why: "only the owner can say which keeps the number" },
    { t: "SL-0127 asset_type = Lamp", a: "C", why: "readme: Streetlight; SL- prefix" },
    { t: "SL-0127 pole_height_m = 22", a: "C", why: "readme: 22 ft = 6.7056 m" },
    { t: "INS-0006 refers to DR-0044 (no such asset)", a: "O", why: "needs the paper form; DR-0043 is a guess" },
    { t: "INS-0005 has no condition score", a: "O", why: "never fill it in; needs re-inspection" },
    { t: "P8 is identical to P3, one minute later, same reporter", a: "C", why: "mark Closed - duplicate of P3; keep the row" },
    { t: "P9 has the same coordinates as P5", a: "X", why: "different problem, reporter and date — two reports" },
    { t: "TR-0301 lies outside every ward", a: "O", why: "ward coverage or tree position — owner" }
  ];
  const box = document.getElementById("cox"), msg = document.getElementById("coxMsg"); let done = 0, right = 0;
  box.innerHTML = items.map((it, i) => `<div class="item" data-i="${i}"><span>${it.t}</span><span class="btns">${["C", "O", "X"].map(k => `<button data-k="${k}" aria-pressed="false">${k}</button>`).join("")}</span></div>`).join("");
  box.querySelectorAll(".item").forEach(el => {
    const it = items[+el.dataset.i];
    el.querySelectorAll("button").forEach(b => b.addEventListener("click", () => {
      if (el.classList.contains("ok") || el.classList.contains("bad")) return;
      el.querySelectorAll("button").forEach(x => x.setAttribute("aria-pressed", x === b));
      const ok = b.dataset.k === it.a; el.classList.add(ok ? "ok" : "bad"); done++; if (ok) right++;
      const w = document.createElement("div"); w.className = "why"; w.textContent = (ok ? "✓ " : `✗ It is ${it.a}. `) + it.why; el.appendChild(w);
      msg.textContent = `${done} of ${items.length} classified, ${right} right.`; msg.className = "status-line " + (done === items.length ? (right === items.length ? "ok" : "bad") : "q");
    }));
  });
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
