<?php $page = ['title' => '10.2 Build attribute conditions', 'chapter' => 10, 'module' => '10.2']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 10.2 · General SQL idea, with source-specific syntax called out</div>
    <h1>Attribute conditions — and the missing-value rule</h1>
    <p class="lead">An <strong>attribute condition</strong> is a yes/no test on a record’s columns — the <code>WHERE</code> clause you already know. GIS software wraps it in a query builder, but underneath it is SQL. The blocks are familiar. What catches people is the <strong>missing value</strong>: a comparison with <code>NULL</code> is neither true nor false, and the row quietly drops out of <em>both</em> a condition and its opposite.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Write equality, range, AND/OR/NOT, parentheses, text-pattern and null conditions on the practice table and predict their IDs.</li>
      <li>Explain why <code>est_cost_inr &gt;= 5000</code> returns 1 row, its negation returns 3, and the table has 6.</li>
      <li>Know which parts of a condition (quotes, field delimiters, case, date literals) depend on the data source.</li></ul></div>
  </div>

  <h2><span class="mod">10.2.1</span>The building blocks, on the practice table</h2>
  <p>Each row below applies one block to the six requests and shows the hand-counted answer. Click a row to see the matching requests highlighted in the table underneath.</p>
  <div class="grid-2">
    <div class="table-wrap"><table class="attr" id="blocksTable">
      <thead><tr><th>Block</th><th>Condition</th><th>IDs</th><th>Note</th></tr></thead><tbody></tbody></table></div>
    <div><div class="table-wrap" id="reqTable"></div><div class="result" id="blockOut">Click a block on the left.</div></div>
  </div>
  <div class="callout warn"><span class="label">Read the LIKE row twice</span><p><code>category LIKE '%tree%'</code> finds “Fallen <strong>tree</strong>” — and also “S<strong>tree</strong>tlight out”. Pattern matching is about letters, not meaning. On real data, look at a sample of the matches, not just the count.</p></div>
  <div class="callout warn"><span class="label">Parentheses</span><p><code>AND</code> is evaluated before <code>OR</code> (“standard operator precedence”, as the ArcGIS SQL reference puts it). So <code>priority = 'High' AND status = 'Open' OR status = 'Reopened'</code> means <em>(High and Open) or Reopened</em> → P3, P5. With brackets around the two statuses it means <em>High and (Open or Reopened)</em> → P3 only. When you mix AND and OR, always add brackets.</p></div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Build a two-part condition and predict its rows</h3>
    <div class="qb">
      <div><select id="f1"></select> <select id="o1"></select> <input id="v1" placeholder="value"></div>
      <div class="op"><select id="join"><option>AND</option><option>OR</option></select></div>
      <div><select id="f2"></select> <select id="o2"></select> <input id="v2" placeholder="value"></div>
    </div>
    <div class="controls"><label><input type="checkbox" id="neg"> wrap the whole thing in <code>NOT ( … )</code></label></div>
    <pre class="sql" id="sqlOut"></pre>
    <div class="result" id="qbOut"></div>
    <p class="small">Values: text is compared exactly as typed (case matters here, as in a file geodatabase). Numbers for <code>est_cost_inr</code>. Dates as <code>YYYY-MM-DD</code>. A row whose value is NULL shows as <span class="chip q">unknown</span> and never passes.</p>
  </div>

  <div class="card">
    <h4 style="margin-top:0">Worked example — turning a sentence into a condition</h4>
    <p>The policy owner asks for “high-priority requests that are still unresolved”. <strong>Assumption to state:</strong> unresolved = status <code>Open</code> or <code>Reopened</code> (Chapter 1’s definition; <em>In progress</em> is left out because a crew is already on it — if the owner disagrees, the <em>condition</em> changes, not the data).</p>
    <pre class="sql">priority = 'High' AND status IN ('Open', 'Reopened')</pre>
    <p>High → P2, P3, P6. Unresolved → P1, P3, P5. Both → <strong>P3</strong>, count 1. Check: P2 is High but In progress; P5 is Reopened but Medium; P6 is High but closed as a duplicate. What it does <em>not</em> establish: that P3 is the only urgent job — P5 is a reopened blocked drain, and priority labels were typed by clerks (Chapter 8: a controlled value is not an observation).</p>
  </div>

  <h2><span class="mod">10.2.2</span><code>IS NULL</code> is not equality, and unknown is not zero</h2>
  <p>Chapter 8 said a null means <em>not known</em> — a different fact from zero and from empty text. Queries make this real, because a comparison with an unknown value is itself unknown, and an unknown never passes a <code>WHERE</code> test. Take the cost column: 1500, 12000, NULL, 0, 2500, NULL.</p>
  <div class="table-wrap"><table class="predtab" id="truthTable"></table></div>
  <div class="grid-3">
    <div class="card"><h4 style="margin-top:0">1. A condition and its opposite do not add up</h4><p><code>&gt;= 5000</code> returns 1 row, <code>NOT (&gt;= 5000)</code> returns 3, the table has 6. The two nulls are in <em>neither</em>. “Expensive = 1, so cheap = 6 − 1 = 5” has silently counted two unknowns as cheap.</p></div>
    <div class="card"><h4 style="margin-top:0">2. Zero passes <code>&lt; 5000</code></h4><p>P4’s cost is <em>known</em> to be zero, so it is a small cost. Whether zero-cost jobs belong in a “cheap repairs” report is a business question — the query did exactly what it was asked.</p></div>
    <div class="card"><h4 style="margin-top:0">3. <code>= NULL</code> is not the test</h4><p>PostgreSQL: “7 = NULL yields null, as does 7 &lt;&gt; NULL.” QGIS documents <code>5 = NULL → NULL</code> and <code>NULL = NULL → NULL</code>. ArcGIS states it as syntax: “The NULL keyword is always preceded by IS or IS NOT.” The test is <code>est_cost_inr IS NULL</code>.</p></div>
  </div>
  <p><strong>The <code>&lt;&gt;</code> trap runs the other way.</strong> ArcGIS notes that <code>&lt;&gt;</code> “will exclude fields with null values”. So <code>est_cost_inr &lt;&gt; 0</code> returns P1, P2, P5 — it drops P4 (which <em>is</em> zero, correctly) <em>and</em> P3 and P6 (unknown, silently). If the question was “requests not known to cost zero”, write <code>est_cost_inr &lt;&gt; 0 OR est_cost_inr IS NULL</code> — five rows.</p>
  <div class="callout dev"><span class="label">Developer view</span><p>SQL null is three-valued logic (true / false / unknown) where <code>WHERE</code> keeps only <em>true</em>. If your language says <code>null == null</code> is true, unlearn it here. The analogy stops at <em>storage</em>: a shapefile cannot store a null number at all (Chapter 7), so the same query on a shapefile export may see a <code>0</code> where the geodatabase held a null — and the truth table collapses.</p></div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“<code>status &lt;&gt; 'Resolved'</code> gives me everything that is not resolved.”</em> Any request whose status is null — an old record, an import that lost the value — disappears from the “not resolved” list. Those are exactly the records most likely to need attention.</p></div>

  <div class="quiz" data-answer="1" data-fb="closed IS NULL gives P1, P2, P3, P5; of those, cost IS NOT NULL keeps P1, P2, P5. IS NULL is a clean yes/no test, so its negation is a clean complement (P4, P6). And est_cost_inr = NULL returns no rows at all in PostgreSQL/QGIS (every comparison is unknown); ArcGIS rejects the syntax.">
    <div class="q">Predict: (a) <code>closed IS NULL AND est_cost_inr IS NOT NULL</code>, (b) <code>NOT (closed IS NULL)</code>, (c) <code>est_cost_inr = NULL</code> as typed.</div>
    <div class="opts"><button class="opt">(a) P1, P2, P3, P5 — (b) P4, P6 — (c) P3, P6</button><button class="opt">(a) P1, P2, P5 — (b) P4, P6 — (c) zero rows (or a syntax error in ArcGIS)</button><button class="opt">(a) P1, P2, P5 — (b) P1, P2, P3, P5 — (c) P3, P6</button><button class="opt">(a) P1, P2, P3, P5 — (b) P4, P6 — (c) zero rows</button></div><div class="fb"></div>
  </div>

  <h2><span class="mod">10.2.3</span>Quotes, dates and syntax are decided by the data source</h2>
  <p><code>status = 'Open'</code> looks portable. Its <em>pieces</em> are not. The same layer moved from a file geodatabase to a GeoPackage to a PostgreSQL database can change which date literal is legal.</p>
  <div class="tabs"><button>Which SQL dialect</button><button>Field names & quotes</button><button>Case</button><button>Date literals</button><button>Wildcards</button></div>
  <div class="tabpanel"><p>ArcGIS Pro’s SQL reference: “The SQL syntax you use in an expression differs depending on the data source.” <strong>File-based data</strong> (file geodatabases, shapefiles, CSV/text tables, feature services using standardized queries) use “the ArcGIS SQL dialect that supports a subset of SQL capabilities”. <strong>Mobile geodatabases, SQLite, GeoPackage, Excel</strong> use the SQLite dialect. <strong>Enterprise databases</strong> (Oracle, SQL Server, PostgreSQL, SAP HANA, Db2) use the database’s own SQL — ArcGIS “will pass the SQL expression to the RDBMS”.</p></div>
  <div class="tabpanel"><p>Select Layer By Attribute: “file geodatabases and shapefiles use double quotes, and enterprise geodatabases don’t use field delimiters” — <code>"status" = 'Open'</code> versus <code>status = 'Open'</code>. Text values always take <em>single</em> quotes; an apostrophe inside a value is doubled: <code>'Alfie''s Trough'</code>. In ArcPy, <code>AddFieldDelimiters</code> writes the right style for you.</p></div>
  <div class="tabpanel"><p>“Strings are case sensitive in expressions, except when run on geodatabases in Microsoft SQL Server.” So <code>status = 'open'</code> finds nothing in a file geodatabase and finds P1 and P3 in a SQL Server geodatabase. To search case-insensitively on purpose: <code>UPPER(status) = 'OPEN'</code>. QGIS gives two operators: <code>LIKE</code> (case-sensitive) and <code>ILIKE</code> (case-insensitive) — <code>'A' LIKE 'a'</code> is FALSE, <code>'A' ILIKE 'a'</code> is TRUE.</p></div>
  <div class="tabpanel"><div class="table-wrap"><table>
    <thead><tr><th>Source</th><th>Documented form (ArcGIS Pro SQL reference)</th></tr></thead>
    <tbody>
      <tr><td>File geodatabase, date-time field</td><td class="mono">Datefield = timestamp 'yyyy-mm-dd hh:mm:ss'</td></tr>
      <tr><td>File geodatabase, date-only field</td><td class="mono">DateOnlyField = date '2003-01-08'</td></tr>
      <tr><td>File geodatabase, time-only / offset fields</td><td class="mono">time '14:35:00' &nbsp;·&nbsp; timestamp '2003-01-08 14:35:00 -08:00'</td></tr>
      <tr><td>Mobile geodatabase (SQLite)</td><td class="mono">Datefield = JULIANDAY('yyyy-mm-dd')</td></tr>
      <tr><td>Shapefile and other file-based sources</td><td class="mono">Datefield = date 'yyyy-mm-dd' &nbsp;(no time part)</td></tr>
      <tr><td>Hosted feature layer via the REST API</td><td class="mono">field = TIMESTAMP '2015-02-09 13:00:00' &nbsp;— issued in the layer’s time zone; “the query operation always returns date values in UTC”</td></tr>
    </tbody></table></div><p class="small">Chapter 8’s time-zone rule (events stored in UTC; IST = UTC+05:30) is why that last sentence matters.</p></div>
  <div class="tabpanel"><p>ArcGIS <code>LIKE</code> uses <code>%</code> (any run of characters, including none) and <code>_</code> (exactly one); QGIS uses the same two plus <code>\</code> to escape them. Older Esri formats used <code>*</code> and <code>?</code>; the current SQL reference documents only <code>%</code> and <code>_</code>, so treat <code>*</code> as untested for any source you have not tried.</p></div>

  <div class="callout note"><span class="label">Where you type the condition</span><p><strong>ArcGIS Pro 3.7:</strong> a definition query on the layer’s <em>Data</em> tab → <em>Build Definition Query</em> (designer mode with clauses, editor mode with SQL, or a saved <code>.exp</code> file; value menus “are specific to the underlying source data”). A selection with <em>Select Layer By Attribute</em> (Selection Type: New / Add to / Remove from / Select subset / Switch / Clear). <strong>QGIS 3.44:</strong> <em>Select by expression</em> and the layer <em>Filter</em> dialog. <strong>PostGIS:</strong> the <code>WHERE</code> clause. <strong>ArcGIS REST API:</strong> the <code>where</code> parameter of the layer’s <code>query</code> operation (“SQL-92 WHERE clause syntax … for most data sources”).</p><p><strong>Verification item:</strong> before putting any expression in a lab handout, run it once on the <em>actual</em> installed source and note the source type and the exact literal that worked.</p></div>

  <div class="quiz" data-answer="3" data-fb="Identify the source type first, then its dialect, then run a known-answer test. PostgreSQL judges the literal itself (date '…' is fine there, but the double-quoted field name is a case-sensitive identifier, and ArcGIS says enterprise geodatabases use no field delimiters). A GeoPackage uses the SQLite dialect, where the shapefile date form is not documented.">
    <div class="q"><code>"reported" &gt;= date '2026-09-01'</code> works on a shapefile. A colleague copies it unchanged to a PostgreSQL enterprise geodatabase layer and to a GeoPackage layer. What should you do before trusting either a result or an error?</div>
    <div class="opts"><button class="opt">Nothing — SQL is SQL.</button><button class="opt">Replace the double quotes with single quotes everywhere.</button><button class="opt">Convert the date to a number of days.</button><button class="opt">Identify each source’s dialect (RDBMS SQL vs SQLite), adjust delimiters and the date literal accordingly, and test with a query whose answer you already know.</button></div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const R = FIXTURE.requests;
  const C = (field, op, value) => r => evalClause(r, { field, op, value });
  const blocks = [
    ["Equality", "status = 'Open'", C("status", "=", "Open"), "Text is compared as typed"],
    ["Set", "status IN ('Open', 'Reopened')", C("status", "IN", ["Open", "Reopened"]), "Same as two = joined by OR"],
    ["Range", "est_cost_inr BETWEEN 1000 AND 3000", r => and3(evalClause(r, { field: "cost", op: ">=", value: 1000 }), evalClause(r, { field: "cost", op: "<=", value: 3000 })), "BETWEEN includes both ends; P4 (0) and the nulls are out"],
    ["Comparison", "est_cost_inr < 5000", C("cost", "<", 5000), "P4 qualifies: zero is a value. P3, P6 are unknown, not small"],
    ["Date", "reported >= date '2026-09-01'", C("reported", ">=", "2026-09-01"), "How the date is written depends on the source — 10.2.3"],
    ["AND", "priority = 'High' AND status = 'Open'", r => and3(evalClause(r, { field: "priority", op: "=", value: "High" }), evalClause(r, { field: "status", op: "=", value: "Open" })), "Both must hold"],
    ["OR", "priority = 'High' OR status = 'Open'", r => or3(evalClause(r, { field: "priority", op: "=", value: "High" }), evalClause(r, { field: "status", op: "=", value: "Open" })), "Either may hold"],
    ["NOT", "NOT status = 'Open'", r => not3(evalClause(r, { field: "status", op: "=", value: "Open" })), "The complement — within the non-null rows"],
    ["Brackets", "priority = 'High' AND (status = 'Open' OR status = 'Reopened')", r => and3(evalClause(r, { field: "priority", op: "=", value: "High" }), or3(evalClause(r, { field: "status", op: "=", value: "Open" }), evalClause(r, { field: "status", op: "=", value: "Reopened" }))), "Compare the next row"],
    ["No brackets", "priority = 'High' AND status = 'Open' OR status = 'Reopened'", r => or3(and3(evalClause(r, { field: "priority", op: "=", value: "High" }), evalClause(r, { field: "status", op: "=", value: "Open" })), evalClause(r, { field: "status", op: "=", value: "Reopened" })), "AND binds first: (High AND Open) OR Reopened"],
    ["Text pattern", "category LIKE '%tree%'", C("category", "LIKE", "%tree%"), "“Streetlight” contains t-r-e-e!"],
    ["Null check", "closed IS NULL", C("closed", "IS NULL"), "The only correct way to find missing values"]
  ];
  const tb = document.querySelector("#blocksTable tbody");
  tb.innerHTML = blocks.map((b, i) => `<tr data-i="${i}"><td>${b[0]}</td><td class="mono">${b[1]}</td><td class="mono">${runQuery(R, b[2]).join(", ") || "(none)"}</td><td class="small">${b[3]}</td></tr>`).join("");
  const rt = document.getElementById("reqTable");
  function showBlock(i) {
    const b = blocks[i];
    reqTable(rt, ["id", "category", "priority", "status", "reported", "closed", "channel", "cost"], p => { const v = b[2](p); return v === true ? "pass" : v === null ? "unk" : "fail"; });
    const ids = runQuery(R, b[2]);
    document.getElementById("blockOut").innerHTML = `<strong>${b[1]}</strong> → <strong class="ids">${ids.join(", ") || "no rows"}</strong> (${ids.length} of 6). Green = passes; blue = unknown (a NULL was compared); grey = fails.`;
    tb.querySelectorAll("tr").forEach(tr => tr.classList.toggle("sel", +tr.dataset.i === i));
  }
  tb.querySelectorAll("tr").forEach(tr => tr.addEventListener("click", () => showBlock(+tr.dataset.i)));
  showBlock(0);

  // ---- builder ----
  const fields = { status: "status", priority: "priority", category: "category", channel: "channel", ward_code: "ward_code", est_cost_inr: "cost", reported: "reported", closed: "closed" };
  const ops = ["=", "<>", ">", ">=", "<", "<=", "LIKE", "IS NULL", "IS NOT NULL"];
  ["f1", "f2"].forEach(id => document.getElementById(id).innerHTML = Object.keys(fields).map(f => `<option>${f}</option>`).join(""));
  ["o1", "o2"].forEach(id => document.getElementById(id).innerHTML = ops.map(o => `<option>${o}</option>`).join(""));
  document.getElementById("f1").value = "priority"; document.getElementById("f2").value = "status"; document.getElementById("v1").value = "High"; document.getElementById("v2").value = "Open";
  function clause(n) {
    const f = document.getElementById("f" + n).value, op = document.getElementById("o" + n).value; let v = document.getElementById("v" + n).value;
    if (f === "est_cost_inr") v = v === "" ? null : Number(v);
    return { field: fields[f], op, value: v, label: f };
  }
  function runBuilder() {
    const c1 = clause(1), c2 = clause(2), j = document.getElementById("join").value, neg = document.getElementById("neg").checked;
    const t = c => clauseText(Object.assign({}, c, { field: c.label }));
    let sql = `${t(c1)} ${j} ${t(c2)}`; if (neg) sql = `NOT (${sql})`;
    document.getElementById("sqlOut").textContent = "SELECT request_id FROM requests\nWHERE " + sql + ";";
    const fn = r => { let v = j === "AND" ? and3(evalClause(r, c1), evalClause(r, c2)) : or3(evalClause(r, c1), evalClause(r, c2)); return neg ? not3(v) : v; };
    const rows = R.map(r => ({ id: r.id, v: fn(r) }));
    const pass = rows.filter(r => r.v === true).map(r => r.id), unk = rows.filter(r => r.v === null).map(r => r.id);
    document.getElementById("qbOut").innerHTML = `Rows returned: <strong class="ids">${pass.join(", ") || "none"}</strong> (${pass.length} of 6).` + (unk.length ? ` Unknown, so <em>not</em> returned — and not returned by the opposite either: <strong class="ids">${unk.join(", ")}</strong>.` : "") + `<br><span class="small">${rows.map(r => `${r.id} ${yesno(r.v)}`).join(" &nbsp; ")}</span>`;
  }
  ["f1", "o1", "v1", "join", "f2", "o2", "v2", "neg"].forEach(id => { const el = document.getElementById(id); el.addEventListener("input", runBuilder); el.addEventListener("change", runBuilder); });
  runBuilder();

  // ---- truth table ----
  const cols = [["est_cost_inr >= 5000", r => evalClause(r, { field: "cost", op: ">=", value: 5000 })], ["NOT (est_cost_inr >= 5000)", r => not3(evalClause(r, { field: "cost", op: ">=", value: 5000 }))], ["est_cost_inr < 5000", r => evalClause(r, { field: "cost", op: "<", value: 5000 })], ["est_cost_inr IS NULL", r => evalClause(r, { field: "cost", op: "IS NULL" })]];
  const cell = v => v === null ? `<td style="background:var(--note-soft)"><em>unknown</em></td>` : v ? `<td class="yes">true</td>` : `<td class="no">false</td>`;
  document.getElementById("truthTable").innerHTML = `<thead><tr><th>request</th><th>est_cost_inr</th>${cols.map(c => `<th class="mono">${c[0]}</th>`).join("")}</tr></thead><tbody>${R.map(r => `<tr><td class="mono">${r.id}</td><td class="mono">${r.cost === null ? '<span class="null">NULL</span>' : fmtN(r.cost)}</td>${cols.map(c => cell(c[1](r))).join("")}</tr>`).join("")}<tr><th>rows returned</th><th></th>${cols.map(c => `<th>${runQuery(R, c[1]).length} (${runQuery(R, c[1]).join(", ")})</th>`).join("")}</tr></tbody>`;
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
