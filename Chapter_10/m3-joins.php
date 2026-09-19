<?php $page = ['title' => '10.3 Join records by identity', 'chapter' => 10, 'module' => '10.3']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 10.3 · General database idea; each product’s join settings named</div>
    <h1>Joins by key — and how six requests become eight rows</h1>
    <p class="lead">A <strong>join by identity</strong> (an <em>attribute join</em>) pairs a row in one table with rows in another whose key values are <em>equal</em>. No geometry is involved — it is the <code>JOIN … ON a.key = b.key</code> you already know. GIS adds one twist: the join is usually made <em>onto a layer</em>, so the geometry stays with the left table and the right table’s columns are stuck on. Two situations produce wrong counts: <strong>unmatched keys</strong> and <strong>duplicate keys</strong>. Our practice data has both.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Predict the row count of a join with unmatched and duplicate keys, under “keep all” and “matches only”.</li>
      <li>See why the asset–inspection join <em>should</em> have more rows than assets, and what “current condition” really means.</li>
      <li>Say which unit a count measures — requests, register rows, matched pairs, or distinct matches — before summing anything.</li></ul></div>
  </div>

  <h2><span class="mod">10.3.1</span>Requests joined to the ward register</h2>
  <p>Question: “attach each request’s ward name and crew team.” Left table: the six requests (key <code>ward_code</code> — typed by the clerk). Right table: the ward register export (key <code>ward_code</code> — with <strong>A twice</strong>, because the 2024 boundary revision added a row and the old one was kept).</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Watch the rows multiply</h3>
    <div class="controls">
      <label><input type="checkbox" id="keepAll" checked> Keep all requests (LEFT JOIN / “Keep all input records”)</label>
      <label><input type="checkbox" id="onlyCurrent"> First filter the register to <code>is_current = 'Yes'</code></label>
      <label>Cardinality <select id="card"><option value="many">one-to-many (all matches)</option><option value="first">one-to-first (first match only)</option></select></label>
    </div>
    <div class="joinviz">
      <div><h4 style="margin:.3rem 0">How each request matches</h4><div id="matchList"></div></div>
      <div><h4 style="margin:.3rem 0">Joined result</h4><div class="table-wrap" id="joinTable"></div><div class="counter" id="joinCount"></div></div>
    </div>
    <div class="result" id="joinOut"></div>
  </div>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">Why P5 and P6 never match</h4><p>P5’s key is <code>NULL</code>, and a null never equals anything (10.2.2). P6’s key is <code>C</code>, and no register row has code C. With <em>keep all</em> they stay as rows with empty ward columns; with <em>matches only</em> they vanish. Neither outcome tells you P6 is “in no ward” — geometrically it is outside both wards, but an <em>attribute</em> join cannot know that. Module 10.7 assigns wards by geometry and policy.</p></div>
    <div class="card"><h4 style="margin-top:0">What each product does with the duplicate</h4><ul style="margin:0">
      <li><strong>ArcGIS Pro <em>Add Join</em></strong>: defaults to one-to-many where the data source allows it (same file/mobile/enterprise geodatabase) and warns that the table has duplicate object IDs; <em>Join one to first</em> “will use the first match in the join table, which may result in different matches each time you run the join”. <em>Keep all input records</em> is on by default.</li>
      <li><strong>QGIS layer join</strong> (Properties ► Joins): strictly one-to-one — “If the join field contains duplicate matching values, only the first fetched feature is picked”; all target features are returned regardless of a match.</li>
      <li><strong>QGIS <em>Join attributes by field value</em></strong> (Processing): a choice — <code>0 — one-to-many</code> or <code>1 — first matching feature only</code> (the default) — plus a <em>Discard records which could not be joined</em> switch.</li>
      <li><strong>SQL</strong>: <code>LEFT JOIN</code> vs <code>INNER JOIN</code>; duplicates simply multiply rows.</li></ul></div>
  </div>
  <div class="callout warn"><span class="label">The same join gives 8 rows in one tool, 6 in another, and 6 with an arbitrary register row in a third — all as documented</span><p>“First match” is the most dangerous, because it hides the duplicate and can pick the 2019 row for P1 and the 2024 row for P2. The fix is not in the join tool; it is to join to the <em>current</em> register rows only — tick the second box above and watch the count settle. That is a Chapter 8 identity policy applied before the join.</p></div>

  <h2><span class="mod">10.3.2</span>One asset, several inspections: the flat result</h2>
  <p>Chapter 8 deliberately kept inspections in their own table: one drain, many visits. Joining assets to inspections is the clearest case where a joined row count <em>should</em> exceed the entity count.</p>
  <div class="grid-2">
    <div class="table-wrap" id="assetJoin"></div>
    <div>
      <div class="counter" id="assetCount"></div>
      <p>Keeping all assets: <strong>5 rows</strong> (2 + 2 + 1 unmatched). Matches only: <strong>4 rows</strong>. The asset’s geometry is repeated on each of its rows. Check: rows with an inspection ID = 4 = the inspection count.</p>
      <p>What the flat result does <em>not</em> give you is a “current condition” per asset — DR-0042 has two values (3, then 2) and SL-0113 has two (3 and 3). Which is current depends on <strong>visit time</strong>, not row order and not ID order: SL-0113’s latest visit is INS-0003 (2026-03-14), although INS-0004 has the higher number — it was typed in late (Chapter 8).</p>
      <div class="callout idea" style="margin-bottom:0"><span class="label">Rule</span><p>The flat join is a <em>report</em>, built on demand and thrown away. It is never the storage.</p></div>
    </div>
  </div>
  <div class="quiz" data-answer="2" data-fb="Three rows have condition 3 (INS-0001, INS-0003, INS-0004). The average over the four inspection rows is 2.75 — it is a mean of inspection observations, weighted by how often each asset was visited, and says nothing about TR-0301, which was never inspected. Current condition = the inspection with the latest visit time per asset: DR-0042 → INS-0002 (2), SL-0113 → INS-0003 (3).">
    <div class="q">On the flat asset–inspection result (all assets kept): how many rows have condition 3, what does the average of the condition column measure, and which rule picks each asset’s <em>current</em> condition?</div>
    <div class="opts"><button class="opt">2 rows; the municipality’s average asset condition; the row with the highest inspection ID</button><button class="opt">3 rows; the average condition of the three assets; the last row in the table</button><button class="opt">3 rows; the mean of inspection observations (visit-weighted, TR-0301 absent); the inspection with the latest visit time per asset</button><button class="opt">4 rows; the average per team; the row entered most recently</button></div><div class="fb"></div>
  </div>

  <h2><span class="mod">10.3.3</span>Decide what you are counting before you add anything up</h2>
  <p>Every count from a joined table measures <strong>one</strong> of these units, and you must say which:</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Unit</th><th>Question it answers</th><th>Asset–inspection join (all kept)</th><th>Request–register join (all kept)</th></tr></thead>
    <tbody>
      <tr><td>Left-hand entities</td><td>How many assets / requests?</td><td class="mono">3</td><td class="mono">6</td></tr>
      <tr><td>Right-hand entities</td><td>How many inspections / register rows?</td><td class="mono">4</td><td class="mono">3</td></tr>
      <tr><td>Matched pairs</td><td>How many asset–inspection pairings?</td><td class="mono">4</td><td class="mono">6</td></tr>
      <tr><td>Distinct left entities with ≥ 1 match</td><td>How many assets inspected / requests with a ward name?</td><td class="mono">2</td><td class="mono">4</td></tr>
      <tr><td>Left entities with no match</td><td>Never inspected / no ward name resolved</td><td class="mono">1 (TR-0301)</td><td class="mono">2 (P5, P6)</td></tr>
    </tbody></table></div>
  <p>Sums inherit the same ambiguity. “Total defects found” = 0 + 2 + 1 + 0 = <strong>3</strong>, summed over <em>inspections</em> — correct. “Total estimated cost by crew” summed over the request–register join gives T-N: ₹1,500 × 2 + ₹12,000 × 2 = <strong>₹27,000</strong>, when the true figure for the two requests is ₹13,500 — the duplicate key doubled every rupee.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Cost per crew — the naive way and the honest way</h3>
    <div class="grid-2">
      <div class="card"><h4 style="margin-top:0">Naive: sum over the joined rows</h4><div id="naiveSum"></div></div>
      <div class="card"><h4 style="margin-top:0">Honest: current register only, unmatched reported</h4><div id="honestSum"></div></div>
    </div>
    <div class="result">Check the honest version: 13,500 + 0 + 2,500 = 16,000 = the sum of all <em>known</em> costs in the table (1,500 + 12,000 + 0 + 2,500). What it still does <em>not</em> establish: the true wards — the codes were typed by clerks; 10.7 compares them with the geometric answer and finds two disagreements.</div>
  </div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“Aggregate on the joined table; the GROUP BY will sort it out.”</em> The grouping is right and every number in it is inflated by the duplicate factor. <code>GROUP BY</code> does not de-duplicate; <code>COUNT(DISTINCT request_id)</code> does — and only for counts, not sums. <strong>Never sum a left-table column over rows multiplied by the right table.</strong> Sum before joining, count distinct IDs, or fix the duplicate key first.</p></div>
  <div class="quiz" data-answer="1" data-fb="“Inspections per team 2 + 2 = 4” is right (four inspection rows). “Assets per team 2 + 2 = 4” counted inspection rows grouped by team, not assets. Distinct assets with a team: DR-0042 (T-S) and SL-0113 (T-N) — one each — plus TR-0301 with no team. Total 3.">
    <div class="q">A dashboard shows “Inspections per team: T-S 2, T-N 2, total 4” and beside it “Assets per team: T-S 2, T-N 2, total 4”. The municipality has three assets. Which is the unit error?</div>
    <div class="opts"><button class="opt">The inspections figure — there are only three assets, so there cannot be four inspections.</button><button class="opt">The assets figure — it counted inspection rows per team; the honest answer is T-S 1, T-N 1, no team 1.</button><button class="opt">Both are fine; the fourth asset must be missing from the map.</button><button class="opt">Neither — totals always equal the row count of the join.</button></div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const R = FIXTURE.requests, G = FIXTURE.register;
  function runJoin() {
    const keep = document.getElementById("keepAll").checked, cur = document.getElementById("onlyCurrent").checked, card = document.getElementById("card").value;
    const reg = cur ? G.filter(g => g.is_current === "Yes") : G;
    let rows = joinRows(R, reg, "ward_code", "ward_code", keep);
    if (card === "first") { const seen = new Set(); rows = rows.filter(r => { if (seen.has(r.left.id)) return false; seen.add(r.left.id); return true; }); }
    document.getElementById("matchList").innerHTML = R.map(r => { const m = r.ward_code === null ? [] : reg.filter(g => g.ward_code === r.ward_code); const n = card === "first" ? Math.min(1, m.length) : m.length; return `<div class="jcard ${m.length > 1 && card === "many" ? "dup" : m.length === 0 ? "none" : ""}"><span class="k">${r.id}</span> ward_code = ${r.ward_code === null ? '<span class="null">NULL</span>' : r.ward_code} → ${m.length === 0 ? (r.ward_code === null ? "no match (null never equals anything)" : "no match (no such code)") : `${n} row${n > 1 ? "s" : ""}${m.length > 1 && card === "first" ? " (first of " + m.length + " — which one?)" : ""}`}</div>`; }).join("");
    document.getElementById("joinTable").innerHTML = `<table class="attr"><thead><tr><th>request_id</th><th>ward_code</th><th>ward_name</th><th>crew_team</th><th>valid_from</th></tr></thead><tbody>${rows.map(x => `<tr class="${x.right && G.filter(g => g.ward_code === x.right.ward_code).length > 1 && !cur ? "hl" : ""}"><td class="mono">${x.left.id}</td><td class="mono">${x.left.ward_code ?? '<span class="null">NULL</span>'}</td><td>${x.right ? x.right.ward_name : '<span class="null">NULL</span>'}</td><td class="mono">${x.right ? x.right.crew_team : '<span class="null">NULL</span>'}</td><td class="mono">${x.right ? x.right.valid_from : '<span class="null">NULL</span>'}</td></tr>`).join("")}</tbody></table>`;
    const matched = rows.filter(x => x.right), distinct = new Set(matched.map(x => x.left.id)).size, unmatched = R.length - distinct;
    document.getElementById("joinCount").innerHTML = `<div class="c">rows <b>${rows.length}</b></div><div class="c">requests <b>6</b></div><div class="c">matched pairs <b>${matched.length}</b></div><div class="c">distinct requests matched <b>${distinct}</b></div><div class="c">unmatched <b>${unmatched}</b></div>`;
    let msg = "";
    if (!cur && card === "many") msg = keep ? "<strong>8 rows for 6 requests.</strong> P1 and P2 each appear twice (one per register row for A); P5 and P6 appear once with empty ward columns." : "<strong>6 rows for 4 requests.</strong> P5 and P6 have silently vanished; P1 and P2 are doubled.";
    else if (!cur && card === "first") msg = `<strong>${rows.length} rows, one per request${keep ? "" : " that matched"}</strong> — but which A row did P1 and P2 get? The tool picked “the first”. Nothing in the data says which that is, and it may differ next time.`;
    else msg = `<strong>${rows.length} rows${keep ? ", one per request" : " (matches only)"}.</strong> With the register filtered to current rows the key is unique, so the join can no longer multiply. P5 (null key) and P6 (code C) are still unmatched — that is a data finding to report, not to hide.`;
    document.getElementById("joinOut").innerHTML = msg;
  }
  ["keepAll", "onlyCurrent", "card"].forEach(id => document.getElementById(id).addEventListener("change", runJoin)); runJoin();

  // asset–inspection
  const aj = joinRows(FIXTURE.assets, FIXTURE.inspections, "id", "asset", true);
  document.getElementById("assetJoin").innerHTML = `<table class="attr"><thead><tr><th>asset</th><th>(x, y)</th><th>inspection</th><th>visit</th><th>condition</th></tr></thead><tbody>${aj.map(x => `<tr class="${x.right ? "" : "dim"}"><td class="mono">${x.left.id}</td><td class="mono">(${x.left.x}, ${x.left.y})</td><td class="mono">${x.right ? x.right.id : '<span class="null">NULL</span>'}</td><td class="mono">${x.right ? x.right.visit : '<span class="null">NULL</span>'}</td><td class="mono">${x.right ? x.right.condition : '<span class="null">NULL</span>'}</td></tr>`).join("")}</tbody></table>`;
  document.getElementById("assetCount").innerHTML = `<div class="c">rows (keep all) <b>${aj.length}</b></div><div class="c">assets <b>3</b></div><div class="c">inspections <b>4</b></div><div class="c">assets inspected <b>2</b></div><div class="c">never inspected <b>1</b></div>`;

  // cost per crew
  const naive = {}; joinRows(R, G, "ward_code", "ward_code", true).forEach(x => { const t = x.right ? x.right.crew_team : "unassigned"; naive[t] = (naive[t] || 0) + (x.left.cost || 0); });
  document.getElementById("naiveSum").innerHTML = `<pre class="sql">SELECT g.crew_team, SUM(r.est_cost_inr)\nFROM requests r LEFT JOIN ward_register g\n  ON g.ward_code = r.ward_code\nGROUP BY g.crew_team;</pre>` + Object.entries(naive).map(([k, v]) => `<div class="jcard ${k === "T-N" ? "dup" : ""}"><span class="k">${k}</span> ₹${fmtN(v)}${k === "T-N" ? " ← doubled by the duplicate key" : ""}</div>`).join("");
  const cur = G.filter(g => g.is_current === "Yes"); const honest = {}; const unk = {};
  joinRows(R, cur, "ward_code", "ward_code", true).forEach(x => { const t = x.right ? x.right.crew_team : "unassigned"; honest[t] = (honest[t] || 0) + (x.left.cost || 0); if (x.left.cost === null) unk[t] = (unk[t] || 0) + 1; });
  document.getElementById("honestSum").innerHTML = `<pre class="sql">-- 1. register WHERE is_current = 'Yes'  (key now unique)\n-- 2. LEFT JOIN, keep the unmatched\n-- 3. report unknown costs separately</pre>` + Object.entries(honest).map(([k, v]) => `<div class="jcard"><span class="k">${k}</span> ₹${fmtN(v)}${unk[k] ? ` — with ${unk[k]} cost unknown` : ""}</div>`).join("") + `<p class="small" style="margin:.4rem 0 0">Unassigned = P5 (₹2,500) and P6 (unknown): reported, not hidden.</p>`;
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
