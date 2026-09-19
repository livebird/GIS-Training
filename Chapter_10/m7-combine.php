<?php $page = ['title' => '10.7 Combine spatial and attribute logic', 'chapter' => 10, 'module' => '10.7']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 10.7 · General idea; ArcGIS Pro Spatial Join and QGIS options quoted</div>
    <h1>One sentence, three testable conditions, one written policy</h1>
    <p class="lead">“Open requests inside the study area and within 300 m of a road.” As one condition it cannot be checked. As three, each can be predicted, executed and reconciled. Then the raw matches must be turned into <em>one answer per request</em> — and that is a business policy, not a predicate.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Split a business question into attribute and spatial conditions with stated assumptions, and see how each assumption changes the answer.</li>
      <li>Write a ward-assignment policy for boundary, overlap, unmatched and multiple-match cases and compare it with the clerk’s codes.</li>
      <li>Predict a spatial join’s row count and <code>Join_Count</code> for one-to-one and one-to-many, and explain why the two ward totals add to more than the requests.</li></ul></div>
  </div>

  <h2><span class="mod">10.7.1</span>Three conditions, one intersection</h2>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Change an assumption; watch the answer change</h3>
    <div class="controls">
      <label>“Open” means <select id="openDef"><option value="or">Open or Reopened</option><option value="o">Open only</option><option value="oip">Open, Reopened or In progress</option></select></label>
      <label>Study area (both wards) <select id="area"><option value="incl">boundary-inclusive</option><option value="strict">strict interior</option><option value="none">no study-area rule</option></select></label>
      <label>Within 300 m of R1 <select id="dist"><option value="incl">≤ 300 (inclusive)</option><option value="strict">&lt; 300 (strict)</option></select></label>
    </div>
    <div class="grid-2">
      <div class="table-wrap"><table class="predtab" id="condTable"></table></div>
      <figure class="map-fig" id="combFig"></figure>
    </div>
    <div class="result" id="combOut"></div>
  </div>
  <p>With the default assumptions (Open/Reopened; inclusive study area; ≤ 300 m) the answer is <strong>P1, P3, P5</strong> — the same set Chapter 1’s hand exercise produced. P2 fails on status (In progress); P4 fails on status and distance; P6 fails on status and study area — it <em>passes</em> the distance test (200 m to the road’s end), which is why you must not silently drop the study-area rule. Three defensible readings, three different lists: the <em>assumptions line</em> is what makes the result reproducible.</p>
  <div class="callout note"><span class="label">Order of evaluation is yours; the result is not</span><p>In SQL the three conditions are one <code>WHERE</code> with <code>AND</code>. In ArcGIS Pro you would typically run <em>Select Layer By Attribute</em> (status), then <em>Select Layer By Location</em> twice with <em>Selection Type</em> = “Select subset from the current selection”. In QGIS, <em>Select by expression</em> then <em>Select by location</em> / <em>Select within distance</em> with “selecting within current selection”. Write down the count after each step: 3 → 3 → 3 if you start with status, 5 → 5 → 3 if you start with the study area. Those counts are your audit trail — the selection itself is invisible session state (10.1).</p></div>
  <div class="quiz" data-answer="3" data-fb="Reported in September → P1, P3, P6. NOT within 300 m (inclusive) → only P4 (400 m). Both → nothing — P4 was reported in August. The assumption that changes it most is the threshold’s inclusiveness: under a strict “within” (< 300), P1 at exactly 300 m is NOT within, so NOT-within = {P1, P4} and the answer becomes {P1}.">
    <div class="q">Rewrite “requests reported in September 2026 that are <em>not</em> within 300 m of the road” as testable conditions. What is the answer, and which single assumption changes it most?</div>
    <div class="opts"><button class="opt">P1, P3, P6 — the distance rule does not matter</button><button class="opt">P4 — reported date does not matter</button><button class="opt">P1 — always</button><button class="opt">Empty set under an inclusive threshold; {P1} under a strict one — the exact-300 m case decides it</button></div><div class="fb"></div>
  </div>

  <h2><span class="mod">10.7.2</span>Assigning requests to wards: the policy, not the predicate</h2>
  <p>A predicate returns <em>matches</em>. A <strong>ward assignment</strong> returns <em>one ward per request</em> (or an explicit “none”), and it must cover four cases before anyone runs a tool:</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Case</th><th>On the practice grid</th><th>Policy question</th><th>One defensible choice (made up for training)</th></tr></thead>
    <tbody>
      <tr><td><strong>Interior</strong></td><td>P1–P4</td><td>none — the predicate answer is the assignment</td><td>Assign to the containing ward</td></tr>
      <tr><td><strong>Boundary</strong></td><td>P5 on x = 1000</td><td>Which ward, or both, or neither?</td><td>The ward with the alphabetically lower code (<strong>A</strong>); record <code>assign_rule = 'BOUNDARY_TIEBREAK'</code></td></tr>
      <tr><td><strong>Overlap</strong> (two wards claim the same ground)</td><td>none here — Chapter 9 said wards must not overlap; Chapter 7’s 2019/2024 boundary story is how one arises</td><td>Defect, or legitimate shared area?</td><td>Treat as a <strong>data defect</strong> (Chapter 9 log); until fixed, assign by the <em>current</em> register year and flag</td></tr>
      <tr><td><strong>Unmatched</strong></td><td>P6, outside both wards</td><td>Drop, keep as NULL, or push to the nearest ward?</td><td>Keep with <code>assigned_ward = NULL</code>, <code>assign_rule = 'OUTSIDE'</code>; <strong>do not</strong> silently assign to nearest — that hides a service-area question</td></tr>
      <tr><td><strong>Multiple matches allowed?</strong></td><td>only if the report is “matches”, not “assignment”</td><td>Unique assignment, or a match list?</td><td>Crew dispatch: unique. “Who might be affected by this boundary?”: list all</td></tr>
    </tbody></table></div>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Apply the policy and compare with the clerk’s codes</h3>
    <div class="controls"><label>Boundary rule <select id="tie"><option value="A">lower code wins (A)</option><option value="B">higher code wins (B)</option><option value="both">list both (match list, not assignment)</option><option value="none">neither (strict)</option></select></label></div>
    <div class="table-wrap"><table class="policy" id="policyTable"></table></div>
    <div class="result" id="policyOut"></div>
  </div>
  <p>The last two columns are the reason to keep <em>both</em> the clerk’s code and the geometric assignment: they disagree on exactly the records that need a human decision, and the disagreement itself is a data-quality finding for Chapter 9’s log.</p>
  <div class="callout idea"><span class="label">The key sentence</span><p>A policy-driven single assignment is <em>different from</em> the raw geometric match result. Six matches (P1, P2, P3, P4, P5 × 2) became five assignments plus one explicit “outside”. Report the raw count (6 ward–request matches, 5 distinct requests matched, 1 unmatched) <em>and</em> the policy result (A: 3, B: 2, outside: 1) — never one dressed up as the other.</p></div>
  <div class="quiz" data-answer="1" data-fb="Open (Open/Reopened) per crew from the current register: T-N (A) has P1 → 1; T-S (B) has P3 → 1. A tie, so the rule cannot decide P5. Its weakness: it depends on a number that changes hour by hour, so the same request can be assigned differently on two days — not reproducible, and it can tie.">
    <div class="q">Change one policy: boundary requests go to the ward whose crew has <em>fewer</em> open requests. Using the current register (A → T-N, B → T-S), where does P5 go, and what is wrong with this rule?</div>
    <div class="opts"><button class="opt">A — T-N has fewer; the rule is fine</button><button class="opt">It cannot decide: T-N and T-S each have one open request (P1, P3) — and the rule is not reproducible over time</button><button class="opt">B — T-S has fewer; the rule is fine</button><button class="opt">Neither — P5 is not open</button></div><div class="fb"></div>
  </div>

  <h2><span class="mod">10.7.3</span>Spatial joins: enrich or aggregate, and know your target</h2>
  <p>A <strong>spatial join</strong> moves attributes from one layer (the <em>join</em> features) to another (the <em>target</em> features) when a spatial relationship holds, instead of when a key matches. ArcGIS Pro’s tool summary: “Joins attributes from one or more inputs to another input based on the spatial relationship.” Its parameters are the ones this chapter has been building toward:</p>
  <ul>
    <li><strong>Target / Join features.</strong> The target keeps its geometry and gains columns. Esri’s own mapping: Select By Location’s <em>Selecting Features</em> = Spatial Join’s <em>Join Features</em>; <em>Relationship</em> = <em>Match Option</em>.</li>
    <li><strong>Join Operation.</strong> <em>One to one</em> (default): when several join features match one target, their attributes “will be aggregated using a field map merge rule … If one polygon has an attribute value of 3 and the other has a value of 7, and a Sum merge rule is specified, the aggregated value … will be 10.” <em>One to many</em>: “the output feature class will contain multiple copies (records) of the target feature.”</li>
    <li><strong>Join_Count</strong> — “The number of join features that match each target feature” — plus <code>TARGET_FID</code>, and with one-to-many <code>JOIN_FID</code>, where “-1 … means no feature meets the specified spatial relationship”.</li>
    <li><strong>Keep All Target Features.</strong> Checked (default): “All target features will be maintained in the output (outer join)”; unchecked: only those with the relationship (inner join).</li>
  </ul>
  <p>And the usage note that turns this module’s arithmetic into a documented fact: “If a join feature has a spatial relationship with multiple target features, it will be counted as many times as it is matched … if a point is in three polygons, the point will be counted three times.”</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Spatial join simulator (Match Option = Intersect, boundary-inclusive)</h3>
    <div class="controls">
      <label>Target <select id="sjTarget"><option value="req">requests (enrich with ward)</option><option value="ward">wards (count requests)</option></select></label>
      <label>Join Operation <select id="sjOp"><option value="one">one to one</option><option value="many">one to many</option></select></label>
      <label><input type="checkbox" id="sjKeep" checked> Keep all target features</label>
    </div>
    <div class="grid-2">
      <div class="table-wrap" id="sjTable"></div>
      <div><div class="counter" id="sjCount"></div><div class="result" id="sjOut"></div></div>
    </div>
  </div>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">Check the enrich case</h4><p>One-to-one with <em>keep all</em>: the row count equals the target count (6) — a spatial join can never lose a target under that setting, nor gain one under one-to-one. One-to-many, matches only: 6 = the sum of Join_Count over targets = 1 + 1 + 1 + 1 + 2 + 0 — the raw match count from the blueprint.</p></div>
    <div class="card"><h4 style="margin-top:0">Check the aggregate case</h4><p>Target = wards: Join_Count A = 3 (P1, P2, P5), B = 3 (P3, P4, P5). Cost merge rule Sum: A = 1,500 + 12,000 + 2,500 = ₹16,000; B = NULL + 0 + 2,500 = ₹2,500 (Esri: “Null values in join fields are ignored for statistic calculation”). P5’s ₹2,500 is in <em>both</em> ward sums, so the two totals add to ₹18,500 although only ₹16,000 of cost exists — module 10.3’s duplicate-key lesson in spatial form. Under the 10.7.2 policy A has 3 requests and B has 2; the raw aggregation says 3 and 3.</p></div>
  </div>
  <div class="callout note"><span class="label">QGIS and SQL</span><p>QGIS <em>Join attributes by location</em> offers the same three shapes: <code>0 — Create separate feature for each matching feature (one-to-many)</code>, <code>1 — Take attributes of the first matching feature only (one-to-one)</code>, <code>2 — … largest overlap only</code>, plus <em>Discard records which could not be joined</em>; the “first matching feature” option is exactly as arbitrary for P5 as Esri’s field-map <em>First</em> rule. In PostGIS the whole thing is one statement — learn to write it, then recognise it in a dialog:</p>
<pre class="sql">-- Enrich (one-to-many, keep all): one row per request per covering ward
SELECT r.request_id, w.ward_code
FROM requests r LEFT JOIN wards w ON ST_Covers(w.geom, r.geom);
-- Aggregate: raw match count per ward (P5 counted in both)
SELECT w.ward_code, COUNT(r.request_id) AS n_requests
FROM wards w LEFT JOIN requests r ON ST_Covers(w.geom, r.geom)
GROUP BY w.ward_code;</pre><p>Swap <code>ST_Covers</code> for <code>ST_Contains</code> and P5 drops out of both wards — that one word <em>is</em> the study-area policy.</p></div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“Join_Count is the number of requests in the ward.”</em> The ward totals sum to 6 and the requests table has 6 rows, so the analyst is satisfied — but one request is in both wards and one is in none. The two errors cancelled. Always check the <em>distinct</em> count as well as the sum.</p></div>
  <div class="quiz" data-answer="0" data-fb="Within 25 m: SL-0113 ← P1 (7.07 m), DR-0042 ← P5 (11.18 m), TR-0301 ← P6 (22.36 m); every other pair is farther. So 3 rows, Join_Count 1, 1, 1. It suggests P5 (“Blocked drain”) is about DR-0042 — a suggestion, not evidence; only a recorded asset_id or inspection establishes the link.">
    <div class="q">Predict Spatial Join with target = <strong>assets</strong>, join = requests, Match Option = <em>Within a distance</em>, radius 25 m, one to one, keep all. Then: which pair looks like “the same incident”, and why is that not evidence?</div>
    <div class="opts"><button class="opt">3 rows; Join_Count 1, 1, 1 (P1, P5, P6); P5–DR-0042 looks related, but proximity is not identity</button><button class="opt">6 rows; every request joins its nearest asset</button><button class="opt">3 rows; Join_Count 0, 1, 0 — only the drain has a request within 25 m</button><button class="opt">2 rows; TR-0301 is dropped because P6 is closed</button></div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const R = FIXTURE.requests;
  // ---- combined query ----
  function updComb() {
    const od = document.getElementById("openDef").value, ar = document.getElementById("area").value, di = document.getElementById("dist").value;
    const sets = { or: ["Open", "Reopened"], o: ["Open"], oip: ["Open", "Reopened", "In progress"] }[od];
    const c1 = R.filter(r => sets.includes(r.status)).map(r => r.id);
    const c2 = ar === "none" ? R.map(r => r.id) : R.filter(r => wardsOf(r.x, r.y, ar === "incl" ? "inclusive" : "strict").length > 0).map(r => r.id);
    const c3 = R.filter(r => di === "incl" ? distToRoad(r.x, r.y) <= 300 + 1e-9 : distToRoad(r.x, r.y) < 300 - 1e-9).map(r => r.id);
    const all = R.filter(r => c1.includes(r.id) && c2.includes(r.id) && c3.includes(r.id)).map(r => r.id);
    const rows = [["C1", "status", `status IN (${sets.map(s => `'${s}'`).join(", ")})`, c1], ["C2", "spatial", ar === "none" ? "(no condition)" : `request ${ar === "incl" ? "intersects / covered by" : "strictly inside"} Ward A or B`, c2], ["C3", "spatial", `distance(request, R1) ${di === "incl" ? "≤" : "<"} 300 m`, c3], ["C1 ∧ C2 ∧ C3", "", "", all]];
    document.getElementById("condTable").innerHTML = `<thead><tr><th>#</th><th>Kind</th><th>Condition</th><th>IDs</th><th>Count</th></tr></thead><tbody>${rows.map(r => `<tr${r[0].length > 2 ? ' class="hl"' : ""}><td class="mono">${r[0]}</td><td>${r[1]}</td><td class="mono">${r[2]}</td><td class="mono">${r[3].join(", ") || "none"}</td><td class="mono">${r[3].length}</td></tr>`).join("")}</tbody>`;
    renderGrid(document.getElementById("combFig"), { band: 300, hit: all, dim: R.map(r => r.id).filter(i => !all.includes(i)), caption: "Green = passes all three conditions." });
    const notes = [];
    if (od === "or" && ar === "incl" && di === "incl") notes.push("The default reading: <strong>P1, P3, P5</strong>.");
    if (ar === "strict") notes.push("Strict study area: P5 on the boundary is out.");
    if (di === "strict") notes.push("Strict distance: P1 at exactly 300 m is out.");
    if (od === "oip") notes.push("Counting In progress as open brings P2 in (inside A, 300 m).");
    if (ar === "none") notes.push("No study-area rule — P6 still fails on status here; loosen status too and P6 appears, 200 m past the end of the road.");
    document.getElementById("combOut").innerHTML = `Result: <strong class="ids">${all.join(", ") || "no requests"}</strong>. ${notes.join(" ")}`;
  }
  ["openDef", "area", "dist"].forEach(id => document.getElementById(id).addEventListener("change", updComb)); updComb();
  // ---- policy ----
  function updPolicy() {
    const tie = document.getElementById("tie").value;
    let rows = R.map(r => {
      const m = wardsOf(r.x, r.y, "inclusive"); let assigned, rule;
      if (m.length === 1) { assigned = m[0]; rule = "interior"; }
      else if (m.length === 0) { assigned = "NULL"; rule = "OUTSIDE"; }
      else { assigned = tie === "both" ? m.join(" + ") : tie === "none" ? "NULL" : tie; rule = tie === "both" ? "match list" : tie === "none" ? "STRICT (unassigned)" : "BOUNDARY_TIEBREAK"; }
      const clerk = r.ward_code === null ? "NULL" : r.ward_code, agree = clerk === assigned;
      return { r, m, assigned, rule, clerk, agree };
    });
    document.getElementById("policyTable").innerHTML = `<thead><tr><th>Request</th><th>Geometric matches (inclusive)</th><th>Assigned ward</th><th>Rule</th><th>Clerk’s ward_code</th><th>Agree?</th></tr></thead><tbody>${rows.map(x => `<tr><td class="mono">${x.r.id}</td><td class="mono">${x.m.join(", ") || "none"}</td><td class="mono">${x.assigned}</td><td class="mono">${x.rule}</td><td class="mono">${x.clerk}</td><td class="${x.agree ? "" : "disagree"}">${x.agree ? "yes" : "no"}</td></tr>`).join("")}</tbody>`;
    const cnt = {}; rows.forEach(x => { cnt[x.assigned] = (cnt[x.assigned] || 0) + 1; });
    document.getElementById("policyOut").innerHTML = `Raw: 6 matches, 5 distinct requests matched, 1 unmatched. Policy result: ${Object.entries(cnt).map(([k, v]) => `<strong>${k}: ${v}</strong>`).join(", ")}. ${tie === "both" ? "A match list is not an assignment — P5 appears twice, so the counts add to 7 for 6 requests." : tie === "none" ? "Under the strict rule P5 joins P6 in the unassigned pile — and a crew report would never see it." : "Every request appears exactly once. The two disagreements with the clerk (P5 blank, P6 = C) go to the defect log."}`;
  }
  document.getElementById("tie").addEventListener("change", updPolicy); updPolicy();
  // ---- spatial join ----
  function updSJ() {
    const t = document.getElementById("sjTarget").value, op = document.getElementById("sjOp").value, keep = document.getElementById("sjKeep").checked;
    let html = "", counts = "", msg = "";
    if (t === "req") {
      let rows = [];
      R.forEach(r => { const m = wardsOf(r.x, r.y, "inclusive"); if (op === "many") { if (m.length) m.forEach(w => rows.push({ id: r.id, jc: m.length, ward: w, jfid: w })); else if (keep) rows.push({ id: r.id, jc: 0, ward: null, jfid: "-1" }); } else { if (m.length || keep) rows.push({ id: r.id, jc: m.length, ward: m.length ? (m.length > 1 ? m[0] + " (merged: First)" : m[0]) : null }); } });
      html = `<table class="attr"><thead><tr><th>TARGET_FID</th><th>request</th><th>Join_Count</th><th>ward_code (joined)</th>${op === "many" ? "<th>JOIN_FID</th>" : ""}</tr></thead><tbody>${rows.map((x, i) => `<tr class="${x.jc === 2 ? "hl" : x.jc === 0 ? "dim" : ""}"><td class="mono">${R.findIndex(r => r.id === x.id) + 1}</td><td class="mono">${x.id}</td><td class="mono">${x.jc}</td><td class="mono">${x.ward ?? '<span class="null">NULL</span>'}</td>${op === "many" ? `<td class="mono">${x.jfid}</td>` : ""}</tr>`).join("")}</tbody></table>`;
      counts = `<div class="c">rows <b>${rows.length}</b></div><div class="c">targets <b>6</b></div><div class="c">sum of Join_Count <b>${R.reduce((s, r) => s + wardsOf(r.x, r.y, "inclusive").length, 0)}</b></div>`;
      msg = op === "one" ? (keep ? "<strong>6 rows</strong> — one per request. P5 has Join_Count 2 and its ward columns were <em>merged</em> by the field-map rule (default <em>First</em> for text — verify in your build). P6 has Join_Count 0 and null ward columns." : "<strong>5 rows</strong> — P6 dropped (inner join). P5 still merged into one row.") : (keep ? "<strong>7 rows</strong> — P5 appears twice (A and B); P6 once with JOIN_FID −1. Now every match is visible." : "<strong>6 rows</strong> — P5 twice, P6 absent. This equals the raw match count.");
    } else {
      const ws = FIXTURE.wards.map(w => { const m = R.filter(r => wardsOf(r.x, r.y, "inclusive").includes(w.id)); return { id: w.id, m, sum: m.reduce((s, r) => s + (r.cost || 0), 0) }; });
      if (op === "one") { html = `<table class="attr"><thead><tr><th>ward</th><th>Join_Count</th><th>requests matched</th><th>Sum est_cost_inr</th></tr></thead><tbody>${ws.map(w => `<tr><td class="mono">${w.id}</td><td class="mono">${w.m.length}</td><td class="mono">${w.m.map(r => r.id).join(", ")}</td><td class="mono">₹${fmtN(w.sum)}</td></tr>`).join("")}</tbody></table>`; counts = `<div class="c">rows <b>2</b></div><div class="c">sum of Join_Count <b>6</b></div><div class="c">distinct requests <b>5</b></div><div class="c">sum of sums <b>₹18,500</b></div>`; msg = "<strong>2 rows, Join_Count 3 and 3.</strong> Six matches for five distinct requests: P5 is counted in both wards, and its ₹2,500 sits in both sums (₹18,500 total against ₹16,000 of real cost). Under the 10.7.2 policy the honest figures are A: 3, B: 2."; }
      else { const rows = []; ws.forEach(w => w.m.forEach(r => rows.push({ w: w.id, r }))); html = `<table class="attr"><thead><tr><th>ward</th><th>request (JOIN_FID)</th><th>est_cost_inr</th></tr></thead><tbody>${rows.map(x => `<tr class="${x.r.id === "P5" ? "hl" : ""}"><td class="mono">${x.w}</td><td class="mono">${x.r.id}</td><td class="mono">${x.r.cost === null ? '<span class="null">NULL</span>' : fmtN(x.r.cost)}</td></tr>`).join("")}</tbody></table>`; counts = `<div class="c">rows <b>${rows.length}</b></div><div class="c">wards <b>2</b></div><div class="c">distinct requests <b>5</b></div>`; msg = "<strong>6 rows</strong> — one per ward–request match; P5 appears under A and under B. P6 appears nowhere: it matches no ward, and a ward with no requests would be the only thing <em>keep all</em> protects here."; }
    }
    document.getElementById("sjTable").innerHTML = html; document.getElementById("sjCount").innerHTML = counts; document.getElementById("sjOut").innerHTML = msg;
  }
  ["sjTarget", "sjOp", "sjKeep"].forEach(id => document.getElementById(id).addEventListener("change", updSJ)); updSJ();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
