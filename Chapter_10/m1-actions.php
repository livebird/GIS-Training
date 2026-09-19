<?php $page = ['title' => '10.1 Filter, selection, join, and transformation', 'chapter' => 10, 'module' => '10.1']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 10.1 · General idea, with product names where they differ</div>
    <h1>Four things that look like “a query” — and only one changes your data</h1>
    <p class="lead">When you “run a query” in a GIS, one of four different things happens. On screen they look alike: fewer rows, some points highlighted. They differ in <strong>what changes</strong> and <strong>for how long</strong>. Mixing them up is how an edit meant for six rows gets applied to six thousand.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Tell a filter, a selection, an exported copy and an update apart — by what they change.</li>
      <li>Predict a record count and a “does the data change?” answer <em>before</em> executing.</li>
      <li>Start the worksheet (<em>input → condition → expected IDs</em>) that you keep for the whole chapter.</li></ul></div>
  </div>

  <h2><span class="mod">10.1.1</span>The four actions</h2>
  <div class="actions4">
    <div class="action safe"><div class="icon">🔍</div><h4>1. Filter a view</h4><p>Only the rows that pass the condition are <em>shown</em>. Nothing is changed. The filter is saved with the map, not with the data.</p><p class="small">ArcGIS Pro: <em>definition query</em>. ArcGIS Online: <em>filter</em>. QGIS: layer <em>filter</em>. SQL: a view.</p></div>
    <div class="action safe"><div class="icon">🖍️</div><h4>2. Select records</h4><p>All rows stay visible; the matching ones are <em>highlighted</em>. Nothing is changed — but other tools may now act on “selected rows only”.</p><p class="small"><em>Select by attributes</em>, <em>Select by location</em>. In code: a boolean flag in session state.</p></div>
    <div class="action safe"><div class="icon">📄</div><h4>3. Export a subset</h4><p>A <em>new</em> file or table holding only the matching rows. The source is untouched — but the copy will go stale.</p><p class="small"><em>Export Features</em>, <em>Extract by …</em>, <code>CREATE TABLE … AS SELECT</code>.</p></div>
    <div class="action changes"><div class="icon">✏️</div><h4>4. Modify source values</h4><p><strong>The stored data changes.</strong> Permanent unless you have a backup.</p><p class="small"><em>Calculate Field</em>, <code>UPDATE … SET</code>, editing. Esri’s own page warns: “This tool modifies the input data.”</p></div>
  </div>
  <p>Two sentences from the official pages explain why this matters. An ArcGIS Pro definition query “affects not only drawing, but also which features appear in the layer’s attribute table and can be selected, labeled, identified, and processed by geoprocessing tools” — so a tool run on a filtered layer quietly works on the filtered rows only. And Calculate Field, “when used with a selected set of features … will only update the selected records” — read that both ways: if you forgot a selection was on, you updated a subset; if you thought one was on and it was not, you updated everything.</p>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Same condition, four buttons — what happens to the table?</h3>
    <p>The condition is <code>priority = 'High'</code>. Press each button and watch the table <em>and</em> the “stored rows” counter.</p>
    <div class="controls">
      <button class="btn" data-act="filter">Filter view</button>
      <button class="btn" data-act="select">Select</button>
      <button class="btn" data-act="export">Export subset</button>
      <button class="btn accent" data-act="update">Calculate field: assigned_team = 'T-N'</button>
      <button class="btn ghost" data-act="reset">Reset</button>
    </div>
    <div class="grid-2">
      <div><h4 style="margin:.3rem 0">Requests (stored table)</h4><div class="table-wrap" id="actTable"></div><div class="counter" id="actCount"></div></div>
      <div><h4 style="margin:.3rem 0">What happened</h4><div class="result" id="actOut">Nothing yet. All six rows are stored, all six are shown, none is selected.</div><div id="exportBox"></div></div>
    </div>
  </div>

  <div class="callout dev"><span class="label">Developer view</span><p>Filter = <code>WHERE</code> on a view. Selection = a boolean flag the client remembers. Export = <code>SELECT … INTO new_table</code>. Modify = <code>UPDATE</code>. The analogy holds well. Where it breaks: in a GIS the <em>same dialog</em> often offers all four as buttons, and a selection can stay active, invisibly, across many tools — session state in your own code would not follow you around like that.</p></div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“It’s just a query, it can’t hurt anything.”</em> The condition gets copied from a harmless filter into a field calculation while a selection nobody noticed is active. 5,994 rows are overwritten. The condition was right. The <strong>action</strong> was wrong.</p></div>

  <h2><span class="mod">10.1.2</span>Predict before you execute</h2>
  <p>Before running anything, write down two predictions:</p>
  <ol>
    <li><strong>How many records</strong>, and which IDs? On the practice grid this is a hand count. On real data it is a rough estimate plus a spot check of two or three records you know.</li>
    <li><strong>Will the stored data change?</strong> For a filter, selection or export the answer must be <em>no</em>. If your plan says <em>yes</em>, stop and confirm you have a copy and a log (Chapter 9).</li>
  </ol>
  <div class="card">
    <h4 style="margin-top:0">Worked example — “show me the open requests”</h4>
    <p><strong>Condition:</strong> <code>status = 'Open'</code>. (Chapter 1’s word “unresolved” was wider — Open <em>or</em> Reopened. Module 10.2 comes back to that.)<br>
    <strong>Predicted IDs:</strong> P1, P3. <strong>Count:</strong> 2. <strong>Source change:</strong> none — this is a filter.</p>
    <p>After running you see two rows. Good. Had you seen <em>three</em>, the first suspect is not the software but your condition — did the data owner mean Reopened counts as open? Had you seen two rows <em>and the table still has only two rows after removing the filter</em> — you did not run a filter. You ran a delete or an export over the source.</p>
  </div>

  <div class="quiz" data-answer="2" data-fb="Calculate Field only updates the selected rows, so the three High rows (P2, P3, P6) now carry T-N — and yes, the stored data changed. Before step two the colleague should have written down the expected IDs and count (P2, P3, P6; 3), confirmed the selection was still active, and made sure a copy and a log existed.">
    <div class="q">A colleague runs <em>Select by attributes</em> with <code>priority = 'High'</code>, then runs <em>Calculate Field</em> to set <code>assigned_team = 'T-N'</code>, then clears the selection. What is true now?</div>
    <div class="opts"><button class="opt">All six rows carry T-N; the stored data changed.</button><button class="opt">Three rows carry T-N; nothing was stored because the selection was cleared.</button><button class="opt">Three rows (P2, P3, P6) carry T-N; the stored data changed; the colleague should have written the expected count and confirmed the selection first.</button><button class="opt">No rows changed — Calculate Field only previews values.</button></div><div class="fb"></div>
  </div>

  <h2><span class="mod">10.1.3</span>The worksheet: input → condition → expected IDs</h2>
  <p>Keep this worksheet for the rest of the chapter and for the lab. One row per query. The <em>expected IDs</em> column is filled <strong>before</strong> you run anything; the last two after. Two rules:</p>
  <ul>
    <li>Write the condition <strong>exactly</strong> as you will type it, quotes and all — 10.2.3 shows the quoting itself depends on the data source.</li>
    <li>When the result does not match, the explanation must be one of four causes: <em>my prediction was wrong</em>; <em>the condition means something different in this engine</em> (10.5); <em>the geometry or CRS is not what I assumed</em> (10.6); <em>the fixture was built differently from its definition</em> (10.8). “The software is weird” is not an explanation.</li>
  </ul>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Your worksheet (saved in this browser)</h3>
    <p>Row W1 is filled as an example. Type your predicted IDs for W2 and W3 as a comma-separated list (e.g. <code>P1, P3</code>), then press <strong>Check</strong> — the page evaluates the condition on the practice table and tells you if your prediction matched.</p>
    <div class="table-wrap"><table class="worksheet">
      <thead><tr><th>#</th><th>Input</th><th>Condition (exactly)</th><th>Action</th><th>Your expected IDs</th><th>Result</th></tr></thead>
      <tbody>
        <tr><td class="mono">W1</td><td>requests, 6 rows</td><td class="mono">status = 'Open'</td><td>select</td><td class="mono">P1, P3</td><td class="ok mono">P1, P3 ✓</td></tr>
        <tr><td class="mono">W2</td><td>requests, 6 rows</td><td class="mono">priority = 'High' AND status = 'Open'</td><td>select</td><td><input data-save="w2" id="w2" placeholder="e.g. P1, P3"></td><td class="mono" id="w2r">—</td></tr>
        <tr><td class="mono">W3</td><td>requests, 6 rows</td><td class="mono">channel = 'Phone'</td><td>filter</td><td><input data-save="w3" id="w3" placeholder="e.g. P1, P3"></td><td class="mono" id="w3r">—</td></tr>
      </tbody></table></div>
    <div class="controls"><button class="btn primary" id="wsCheck">Check</button> <span class="small">Blank = zero rows. Order does not matter.</span></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  // ---- four actions demo ----
  let rows = FIXTURE.requests.map(r => Object.assign({ assigned_team: null }, r));
  let state = { filter: false, selected: [], exported: null };
  const cond = r => r.priority === "High";
  function draw() {
    const shown = state.filter ? rows.filter(cond) : rows;
    document.getElementById("actTable").innerHTML = `<table class="attr"><thead><tr><th>request_id</th><th>priority</th><th>status</th><th>assigned_team</th></tr></thead><tbody>${shown.map(r => `<tr class="${state.selected.includes(r.id) ? "sel" : ""}"><td class="mono">${r.id}</td><td>${r.priority}</td><td>${r.status}</td><td class="mono">${r.assigned_team ?? '<span class="null">NULL</span>'}</td></tr>`).join("")}</tbody></table>`;
    document.getElementById("actCount").innerHTML = `<div class="c">stored rows <b>${rows.length}</b></div><div class="c">shown <b>${shown.length}</b></div><div class="c">selected <b>${state.selected.length}</b></div><div class="c">rows with T-N <b>${rows.filter(r => r.assigned_team).length}</b></div>`;
  }
  const out = document.getElementById("actOut"), exp = document.getElementById("exportBox");
  document.querySelectorAll("[data-act]").forEach(b => b.addEventListener("click", () => {
    const a = b.dataset.act;
    if (a === "filter") { state.filter = true; out.innerHTML = "<strong>Filter.</strong> Three rows are shown (P2, P3, P6). Six rows are still stored. Nothing changed. Tools run now would see only three rows."; }
    if (a === "select") { state.selected = rows.filter(cond).map(r => r.id); out.innerHTML = "<strong>Selection.</strong> All rows still shown; P2, P3, P6 highlighted. Nothing changed. But look at the next button…"; }
    if (a === "export") { state.exported = rows.filter(cond); out.innerHTML = "<strong>Export.</strong> A <em>new</em> table with 3 rows now exists (below). The source still has 6 rows and did not change. The copy will not update when the source does."; exp.innerHTML = `<h4 style="margin:.6rem 0 .2rem">high_requests (new table)</h4><table class="attr"><tbody>${state.exported.map(r => `<tr><td class="mono">${r.id}</td><td>${r.priority}</td></tr>`).join("")}</tbody></table>`; }
    if (a === "update") { const target = state.selected.length ? rows.filter(r => state.selected.includes(r.id)) : rows; target.forEach(r => r.assigned_team = "T-N"); out.innerHTML = `<strong>Calculate Field.</strong> ${state.selected.length ? `Only the <em>selected</em> rows were updated: ${target.map(r => r.id).join(", ")}.` : `<span style="color:var(--warn)"><strong>No selection was active, so ALL SIX rows were updated.</strong></span> This is the mistake the module warns about.`} The stored data has changed — this is permanent without a backup.`; }
    if (a === "reset") { rows = FIXTURE.requests.map(r => Object.assign({ assigned_team: null }, r)); state = { filter: false, selected: [], exported: null }; out.textContent = "Reset. Six rows stored, six shown, none selected."; exp.innerHTML = ""; }
    draw();
  }));
  draw();
  // ---- worksheet ----
  const parse = s => (s || "").toUpperCase().split(/[,\s]+/).filter(Boolean).sort();
  const same = (a, b) => a.length === b.length && a.every((v, i) => v === b[i]);
  document.getElementById("wsCheck").addEventListener("click", () => {
    const checks = [["w2", r => and3(evalClause(r, { field: "priority", op: "=", value: "High" }), evalClause(r, { field: "status", op: "=", value: "Open" }))], ["w3", r => evalClause(r, { field: "channel", op: "=", value: "Phone" })]];
    checks.forEach(([id, fn]) => { const got = runQuery(FIXTURE.requests, fn).sort(); const mine = parse(document.getElementById(id).value); const cell = document.getElementById(id + "r"); cell.textContent = (got.length ? got.join(", ") : "(none)") + (same(got, mine) ? " ✓ matched" : " ✗ differs from your prediction"); cell.className = "mono " + (same(got, mine) ? "ok" : "bad"); });
  });
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
