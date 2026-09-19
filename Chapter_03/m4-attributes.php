<?php $page = ['title' => '3.4 Attributes: the facts behind each shape', 'chapter' => 3, 'module' => '3.4']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 3.4 · General GIS idea</div>
    <h1>Attributes: the facts behind each shape</h1>
    <p class="lead">Every feature carries facts — its <strong>attributes</strong> — in a table that looks like a spreadsheet. The map and the table are <strong>two views of the same rows</strong>. This module gives you the words, shows you the link, and warns you about the three jobs people wrongly give to one column.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Use <strong>record, field, value, identifier</strong> and <strong>attribute table</strong> correctly, and find a clicked feature’s row.</li>
      <li>Use one column to <strong>colour</strong>, another to <strong>filter</strong>, another to <strong>label</strong> — and see that none of them changes the data.</li>
      <li>Keep inspection history in its own table, linked by an ID — never as extra columns.</li></ul></div>
  </div>

  <h2><span class="mod">3.4.1</span>Rows, columns, values, IDs</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Word</th><th>Meaning</th><th>Example</th></tr></thead>
    <tbody>
      <tr><td><strong>Attribute table</strong></td><td>All the rows of one dataset, one row per feature</td><td>The whole requests table below</td></tr>
      <tr><td><strong>Record</strong> (row)</td><td>One feature’s stored values</td><td>The P3 row</td></tr>
      <tr><td><strong>Field</strong> (column)</td><td>One named, typed fact</td><td><code>priority</code></td></tr>
      <tr><td><strong>Value</strong></td><td>What one field holds in one row</td><td><code>High</code> in P3’s <code>priority</code></td></tr>
      <tr><td><strong>Feature identifier</strong></td><td>A value that uniquely names one feature</td><td><code>request_id</code> = P3 (the office’s ID); the software’s own row number (system ID)</td></tr>
      <tr><td><strong>Geometry field</strong></td><td>The special column that holds the shape</td><td>Called <code>SHAPE</code> in ArcGIS; a <code>geometry</code> column in PostGIS</td></tr>
    </tbody></table></div>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Click a dot on the map, or a row in the table — they are the same record</h3>
    <figure class="map-fig" id="linkFig"></figure>
    <div class="table-wrap"><table class="attr" id="linkTable"></table></div>
    <div class="tablefoot" id="linkFoot">0 of 7 selected</div>
    <div class="controls"><button class="btn small" id="showSel">Show selected records</button><button class="btn small" id="showAll" aria-pressed="true">Show all records</button></div>
    <div class="result" id="linkOut">Try clicking P7’s row. Where is it on the map?</div>
  </div>
  <details class="reveal"><summary>Doing this in ArcGIS Pro and QGIS (from the official documentation; not execution-tested)</summary>
    <p><strong>ArcGIS Pro:</strong> click a request with the <em>Select</em> tool. Right-click the layer in the <em>Contents</em> pane → <em>Attribute Table</em>, or select the layer and press <kbd>Ctrl</kbd>+<kbd>T</kbd>. At the bottom of the table, <em>Show Selected Records</em> / <em>Show All Records</em>; the footer reads like “1 of 7 selected”. Or right-click the layer → <em>Selection</em> → <em>Open Attribute Table Showing Selection</em>.</p>
    <p><strong>QGIS 3.40:</strong> <em>Select Features</em> tool; then <em>Layer ▸ Open Attribute Table</em> or <kbd>F6</kbd>. The title bar shows total / filtered / selected counts; the drop-down at bottom-left switches <em>Show All Features</em> / <em>Show Selected Features</em>. <kbd>Shift</kbd>+<kbd>F6</kbd> opens the table already filtered to the selection.</p>
  </details>
  <h3>Two identifiers, two jobs</h3>
  <p>Every ArcGIS table gets a <strong>system ID</strong> — the <em>ObjectID</em>: a unique whole number the software owns, which cannot be empty and cannot be edited. Geodatabases may also add a <em>GlobalID</em>, a 36-character code the database assigns. The office’s own <code>request_id</code> (“P3”) is a <strong>business ID</strong>: the office’s process owns it, it appears on the citizen’s SMS receipt, and it must survive copying and exporting.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Copy the dataset — watch which ID survives</h3>
    <div class="controls"><button class="btn accent" id="copyBtn">Copy features to a new dataset</button><button class="btn ghost" id="copyReset">Reset</button></div>
    <div class="two-col">
      <div><h4 style="margin-top:0">Original: <code>Requests</code></h4><div class="table-wrap"><table class="attr" id="idA"></table></div></div>
      <div><h4 style="margin-top:0">Copy: <code id="copyName">—</code></h4><div class="table-wrap"><table class="attr" id="idB"></table></div></div>
    </div>
    <div class="result" id="idOut">A system ID belongs to <em>one table</em>. When rows are copied into a new dataset, the new table hands out its own numbers.</div>
  </div>
  <div class="callout warn"><span class="label">Misconception</span><p>“The row number I see is the record’s ID.” A report says “request 4 is resolved” — row 4 is P4 today, but after an export, a sort or a deletion, row 4 is something else. Always quote <code>request_id</code>. (This is why ArcGIS Pro’s <em>Multipart To Singlepart</em> tool writes the <em>old</em> IDs into a separate <code>ORIG_FID</code> column: the output table has new ObjectIDs.)</p></div>
  <div class="callout dev"><span class="label">Developer view</span><p>ObjectID = auto-increment surrogate key; <code>request_id</code> = natural key. <strong>Where the comparison stops:</strong> in many databases you may pick either as the primary key; in ArcGIS the ObjectID is required and cannot be removed, so the business ID is always an <em>additional</em> column whose uniqueness <em>you</em> must maintain (Chapter 8).</p></div>

  <h2><span class="mod">3.4.2</span>Colour by one column, filter by another, label by a third</h2>
  <p>Attributes drive three different things on a map, and each can use a different column. <strong>None of them changes the data.</strong></p>
  <div class="try">
    <span class="tag">Try it</span>
    <div class="controls">
      <label>Colour by <select id="styleBy"><option value="">one symbol</option><option value="category">category</option><option value="status">status</option></select></label>
      <label>Filter <select id="filterBy"><option value="">none</option><option value="open">status is Open or Reopened</option><option value="high">priority = High</option><option value="pothole">category = Pothole</option></select></label>
      <label>Label with <select id="labelBy"><option value="id">request_id</option><option value="category">category</option><option value="">no label</option></select></label>
    </div>
    <figure class="map-fig" id="styleFig"></figure>
    <div class="legend" id="legend"></div>
    <div class="tiles" id="styleTiles"></div>
    <div class="table-wrap"><table class="attr" id="styleTable"></table></div>
    <div class="result" id="styleOut"></div>
  </div>
  <div class="callout idea"><span class="label">Why a map label is not the ID</span><p>A <strong>label</strong> is chosen for <em>reading</em>: short, meaningful, often repeated (“Pothole” three times). An <strong>identifier</strong> is chosen for <em>linking</em>: unique, stable, often meaningless (“P3”). “Main Road” is a fine label for R1 but a poor key — many towns have several Main Roads, and roads get renamed. Keep both columns; show the label; join on the ID. (A <em>field alias</em> is a third, separate thing: a friendlier column <em>heading</em>, not a value.)</p></div>
  <div class="callout note"><span class="label">Platform note — where the filter lives</span><p>In ArcGIS Pro, colours, labels and the <em>definition query</em> are properties of the <strong>layer</strong>, not the dataset; the query limits which rows the layer draws, lists, selects, labels and passes to tools. In QGIS the same thing is the layer’s <em>Provider Feature Filter</em> (Query Builder). In ArcGIS Online a <em>hosted feature layer view</em> plays this role. In PostGIS you would write a SQL <code>VIEW</code>. One idea, four names.</p></div>
  <div class="quiz" data-answer="2" data-fb="priority = High keeps P2, P3 and P6 — three rows, and all three have a location, so three dots. For a crew supervisor, category (what is wrong) is the most useful label; request_id is fine for paperwork; priority is useless as a label here because every visible dot would say High.">
    <div class="q">Filter the requests to <code>priority = 'High'</code>. How many rows and how many dots, and which column is the best label for a crew supervisor?</div>
    <div class="opts">
      <button class="opt">4 rows, 3 dots; label with priority</button>
      <button class="opt">3 rows, 2 dots; label with request_id</button>
      <button class="opt">3 rows, 3 dots; label with category</button>
    </div><div class="fb"></div>
  </div>

  <h2><span class="mod">3.4.3</span>Inspection history lives in its own table</h2>
  <p>Drain DR-0042 has been inspected twice. Its asset row has one <code>condition</code> value, “Poor”. Where do the two visits go?</p>
  <div class="tri">
    <div class="t"><h4><span class="chip no">wrong</span> Extra columns</h4><p><code>inspection1_date</code>, <code>inspection1_result</code>, <code>inspection2_date</code>… The row grows with every visit; most assets have empty columns; “all inspections in 2025” becomes a search across dozens of fields.</p></div>
    <div class="t"><h4><span class="chip no">wrong</span> Copies of the asset row</h4><p>One asset row per visit, each with the drain’s coordinates. Now the drain shows up three times on the map and every asset count is a guess.</p></div>
    <div class="t"><h4><span class="chip yes">right</span> A separate table, linked by ID</h4><p>A table with <strong>no shapes</strong> whose <code>asset_id</code> column names the asset. One visit, one row. Chapter 8 makes this formal.</p></div>
  </div>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Click an asset to see its inspections</h3>
    <figure class="map-fig" id="insFig"></figure>
    <div class="table-wrap"><table class="attr" id="insTable"></table></div><div class="result" id="insOut">Click a diamond (streetlight, drain) or a green circle (tree).</div>
  </div>
  <div class="callout dev"><span class="label">Developer view</span><p><code>inspections.asset_id</code> is a foreign key to <code>assets.asset_id</code> — one asset, many inspections. <strong>Where the comparison stops:</strong> GIS software also offers <em>joins</em> that temporarily flatten related rows onto the map table, and a one-to-many join can multiply or collapse rows in ways a map count will not reveal. That trap is Chapter 10’s.</p></div>

  <div class="callout note"><span class="label">Comprehension check (write it down)</span><p>Tree TR-0301 has <code>condition</code> = Unknown and no rows in the inspections table. Give two different reasons this could be so, and say which record you would look at to tell them apart.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  /* 3.4.1 linked selection */
  let sel = null, onlySel = false;
  function drawLink() {
    renderTown(document.getElementById("linkFig"), { visible: ["wards", "roads", "requests"], selected: sel ? { layer: "requests", id: sel } : null, onSelect: (l, id) => { if (l !== "requests") return; sel = id; drawLink(); }, caption: "Requests layer. Click a dot." });
    const t = document.getElementById("linkTable");
    t.innerHTML = REQ_HEAD + TOWN.requests.filter(p => !onlySel || p.id === sel).map(p => requestRow(p, p.id === sel ? 'class="sel"' : "")).join("");
    document.getElementById("linkFoot").textContent = (sel ? 1 : 0) + " of 7 selected";
    document.getElementById("showSel").setAttribute("aria-pressed", onlySel); document.getElementById("showAll").setAttribute("aria-pressed", !onlySel);
    if (sel) { const p = TOWN.requests.find(r => r.id === sel); document.getElementById("linkOut").innerHTML = p.x == null ? `<strong>${p.id}</strong> is selected in the table — but there is <strong>no dot to highlight</strong>. Its row exists (category ${p.category}, status ${p.status}); its shape is empty. The map cannot show what has no location.` : `<strong>${p.id}</strong>: the highlighted dot at (${p.x}, ${p.y}) and the highlighted row are <em>one record</em>. Category ${p.category}, priority ${p.priority}, status ${p.status}.`; }
  }
  document.getElementById("linkTable").addEventListener("click", e => { const r = e.target.closest("tr[data-id]"); if (r) { sel = r.dataset.id; drawLink(); } });
  document.getElementById("showSel").addEventListener("click", () => { onlySel = true; drawLink(); });
  document.getElementById("showAll").addEventListener("click", () => { onlySel = false; drawLink(); });
  drawLink();

  /* two identifiers */
  const idRows = (offset, sys) => `<tr><th>${sys}</th><th>request_id</th><th>category</th></tr>` + TOWN.requests.map((p, i) => `<tr><td class="mono">${i + 1 + offset}</td><td class="mono">${p.id}</td><td>${p.category}</td></tr>`).join("");
  document.getElementById("idA").innerHTML = idRows(0, "OBJECTID");
  document.getElementById("idB").innerHTML = `<tr><td colspan="3" class="small">(no copy yet)</td></tr>`;
  document.getElementById("copyBtn").addEventListener("click", () => {
    document.getElementById("copyName").textContent = "Requests_copy";
    // a fresh table numbers its rows from 1 again — and in real tools the order can differ; we show a sorted-by-category copy to make the point
    const sorted = [...TOWN.requests].sort((a, b) => a.category.localeCompare(b.category));
    document.getElementById("idB").innerHTML = `<tr><th>OBJECTID</th><th>request_id</th><th>category</th></tr>` + sorted.map((p, i) => `<tr><td class="mono">${i + 1}</td><td class="mono">${p.id}</td><td>${p.category}</td></tr>`).join("");
    document.getElementById("idOut").innerHTML = "The copy has its <strong>own</strong> ObjectIDs, assigned in whatever order the rows were written (here they came out sorted by category). <code>request_id</code> travelled with each row unchanged. A note that said “ObjectID 3 is the water leak” is now wrong in the copy; “P3 is the water leak” is still right. <span class='small'>Simulated — real tools may keep or change the order; the point is that you cannot rely on it.</span>";
  });
  document.getElementById("copyReset").addEventListener("click", () => { document.getElementById("copyName").textContent = "—"; document.getElementById("idB").innerHTML = `<tr><td colspan="3" class="small">(no copy yet)</td></tr>`; });

  /* 3.4.2 style / filter / label */
  const FILTERS = { "": () => true, open: p => p.status === "Open" || p.status === "Reopened", high: p => p.priority === "High", pothole: p => p.category === "Pothole" };
  function drawStyle() {
    const by = document.getElementById("styleBy").value, f = document.getElementById("filterBy").value, lab = document.getElementById("labelBy").value;
    const kept = TOWN.requests.filter(FILTERS[f]);
    const drawn = kept.filter(p => p.x != null);
    renderTown(document.getElementById("styleFig"), { visible: ["wards", "roads", "requests"], reqStyle: { by: by || null, filter: FILTERS[f], label: lab || null }, caption: "Presentation settings only. The dataset is untouched." });
    const cols = by === "category" ? CAT_COL : by === "status" ? STATUS_COL : null;
    const classes = cols ? [...new Set(kept.map(p => p[by]))] : [];
    document.getElementById("legend").innerHTML = cols ? classes.map(c => `<span style="--sw:${cols[c]}">${c}</span>`).join("") : `<span style="--sw:var(--accent)">all requests</span>`;
    document.getElementById("styleTable").innerHTML = REQ_HEAD + kept.map(p => requestRow(p)).join("");
    document.getElementById("styleTiles").innerHTML = `<div class="tile"><div class="v">${kept.length}</div><div class="l">rows in the layer’s table</div></div><div class="tile ${kept.length !== drawn.length ? "warn" : ""}"><div class="v">${drawn.length}</div><div class="l">dots on the map</div></div><div class="tile"><div class="v">${cols ? classes.length : 1}</div><div class="l">colour classes</div></div><div class="tile ok"><div class="v">7</div><div class="l">rows in the dataset</div></div>`;
    document.getElementById("styleOut").innerHTML = (kept.length !== drawn.length ? `<strong>${kept.length} rows but ${drawn.length} dots</strong> — P7 passes the filter but has no location. A count from the map and a count from the table legitimately differ, and the difference is itself information. ` : "") + "Whatever you choose above, the dataset still has <strong>7 rows</strong>: colour, filter and label are settings of the <em>layer</em> (module 3.5).";
  }
  ["styleBy", "filterBy", "labelBy"].forEach(id => document.getElementById(id).addEventListener("change", drawStyle)); drawStyle();

  /* 3.4.3 related records */
  let aSel = null;
  function drawIns() {
    renderTown(document.getElementById("insFig"), { visible: ["wards", "roads", "assets"], selected: aSel ? { layer: "assets", id: aSel } : null, onSelect: (l, id) => { if (l !== "assets") return; aSel = id; drawIns(); }, caption: "Assets layer. Click one." });
    const rows = TOWN.inspections.filter(i => i.asset_id === aSel);
    document.getElementById("insTable").innerHTML = `<tr><th>inspection_id</th><th>asset_id</th><th>inspected_on</th><th>found</th><th>by</th></tr>` + TOWN.inspections.map(i => `<tr class="${i.asset_id === aSel ? "sel" : ""}"><td class="mono">${i.id}</td><td class="mono">${i.asset_id}</td><td class="mono">${i.date}</td><td>${i.found}</td><td class="mono">${i.by}</td></tr>`).join("");
    if (aSel) { const a = TOWN.assets.find(x => x.id === aSel); document.getElementById("insOut").innerHTML = `<strong>${a.id}</strong> (${a.type}, condition <em>${a.condition}</em>): ${rows.length} inspection${rows.length === 1 ? "" : "s"} found by looking up <code>asset_id = '${a.id}'</code>` + (rows.length ? ` — ${rows.map(r => r.date + " (" + r.found + ")").join(", ")}. The asset row’s condition is just a summary of the latest one.` : ". <strong>No rows.</strong> Never inspected? Or inspected and the record lost? The asset row alone cannot tell you."); }
  }
  drawIns();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
