<?php $page = ['title' => '8.5 Relationships', 'chapter' => 8, 'module' => '8.5']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 8.5 · General idea, with ArcGIS / QGIS / PostgreSQL names</div>
    <h1>Connect the tables — and know what actually enforces the connection</h1>
    <p class="lead">A <strong>relationship</strong> is a statement that rows in one table point to rows in another. Its <strong>cardinality</strong> says how many on each side. You already know the mechanics — a primary key here, a foreign key there — so this module concentrates on <em>which</em> kind of relationship the town’s facts need, and on a trap: a line on a diagram, a join in a map, and a rule the database enforces are three very different things.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Model one-to-many, one-to-one and many-to-many with keys, using assets, inspections, teams and wards.</li>
      <li>Store one asset with many inspections <em>without</em> copying the asset into every visit.</li>
      <li>Tell apart a conceptual relationship, a join, a QGIS relation, an ArcGIS relationship class, and a database foreign key — and say what each one enforces.</li></ul></div>
  </div>

  <h2><span class="mod">8.5.1</span>Three kinds of relationship</h2>
  <div class="grid-3">
    <div class="card"><h4 style="margin-top:0">One-to-many (1 : M) — the workhorse</h4><p>One asset has many inspections; each inspection is about exactly one asset. The pointer (<em>foreign key</em>) goes on the <strong>many</strong> side: <code>Inspection.asset_id</code>. Same pattern: one team does many inspections (<code>Inspection.team_id</code>).</p></div>
    <div class="card"><h4 style="margin-top:0">One-to-one (1 : 1) — for type-specific facts</h4><p>Module 8.3 showed that wattage is <em>not applicable</em> to drains. One clean fix: a table <code>StreetlightDetail</code> (wattage, lamp type, arm length) linked 1:1 to the asset by <code>asset_id</code>. A drain simply has no detail row. The foreign key sits on the detail side and is also <strong>unique</strong> there — that uniqueness is what makes it one-to-one.</p></div>
    <div class="card"><h4 style="margin-top:0">Many-to-many (M : N) — needs a third table</h4><p>A team covers several wards; a ward is covered by several teams. Neither table can hold the other’s key without repeating rows. The answer is a <strong>junction table</strong> (also called a link or bridge table): one row per (team, ward) pair, with any facts about the pair itself — <code>from_date</code>, <code>to_date</code>.</p></div>
  </div>
  <div class="table-wrap"><table>
    <thead><tr><th>If the business fact is…</th><th>Cardinality</th><th>Where the key goes</th><th>Town example</th></tr></thead>
    <tbody>
      <tr><td>“Each B belongs to exactly one A; an A can have many Bs”</td><td>1 : M</td><td>Key on B, never blank</td><td>Asset → Inspection</td></tr>
      <tr><td>“Each B may belong to one A, or to none”</td><td>1 : M, optional</td><td>Key on B, blank allowed</td><td>Asset → Request (a complaint may not match any asset)</td></tr>
      <tr><td>“Each A has at most one B, and B is only about that A”</td><td>1 : 1</td><td>Key on B, unique</td><td>Asset → StreetlightDetail</td></tr>
      <tr><td>“An A relates to many Bs and a B to many As”</td><td>M : N</td><td>Junction table with both keys</td><td>Team ↔ Ward</td></tr>
    </tbody></table></div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Which cardinality?</h3>
    <div class="sorter" data-items='[
      {"t":"A drain and its many cleaning visits","bin":"1 : M","why":"key on the visit"},
      {"t":"A bus shelter and the bus routes that stop there (each route serves many shelters)","bin":"M : N","why":"junction table: shelter_id + route_code"},
      {"t":"A streetlight and its one electricity-meter record","bin":"1 : 1","why":"key on the meter row, unique"},
      {"t":"A ward and the assets inside it","bin":"1 : M","why":"key on the asset (or derived from the map)"},
      {"t":"An inspector and the crews they have worked in over the years","bin":"M : N","why":"junction with from/to dates"},
      {"t":"A complaint and the one asset a clerk matched it to (sometimes none)","bin":"1 : M","why":"optional key on the complaint"}
    ]'>
      <div class="items"></div>
      <div class="bins">
        <div class="bin" data-bin="1 : M"><h5>1 : M</h5></div>
        <div class="bin" data-bin="1 : 1"><h5>1 : 1</h5></div>
        <div class="bin" data-bin="M : N"><h5>M : N</h5></div>
      </div>
    </div>
  </div>

  <h2><span class="mod">8.5.2</span>One asset, many inspections — without repeating the asset</h2>
  <p>Here is the whole design for the practice town as an <dfn title="Entity–relationship diagram: boxes for tables, lines for relationships, with how-many at each end">entity–relationship diagram</dfn>. <strong>Click any box</strong> to see its rows and what points to it. Blue boxes have a shape; white boxes are plain tables.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <figure class="map-fig" id="erFig"></figure>
    <div id="erInfo" class="status-line q">Click a box in the diagram.</div>
    <div id="erRows"></div>
  </div>
  <p>Read the diagram as sentences: an asset <em>is inspected in</em> zero or more inspections; an inspection <em>has</em> zero or more photos; a team <em>performs</em> many inspections; a team <em>covers</em> many wards through the coverage table; a complaint <em>may point to</em> one asset; a streetlight <em>has</em> one detail row.</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Fact</th><th>Stored on</th><th>Not stored on</th><th>Why</th></tr></thead>
    <tbody>
      <tr><td>Asset location</td><td>Asset</td><td>Inspection</td><td>One location, one place; a visit inherits it through <code>asset_id</code></td></tr>
      <tr><td>Asset type, installation year</td><td>Asset</td><td>Inspection</td><td>Copying them into every visit means a mistake must be fixed in N places</td></tr>
      <tr><td>Condition seen on a visit</td><td>Inspection</td><td>Asset (except as a cached copy, 8.7)</td><td>It is a fact about the <em>visit</em></td></tr>
      <tr><td>Crew that visited</td><td>Inspection (<code>team_id</code>)</td><td>Asset</td><td>Different visits, different crews</td></tr>
      <tr><td>Which ward an asset is in</td><td>Worked out from the map — or stored on Asset only if the office’s assignment can differ from the polygon</td><td>Inspection</td><td>P5 in Chapter 1 sat exactly on the A/B line; a stored assignment is how such a case is settled by policy</td></tr>
    </tbody></table></div>
  <p><strong>Counting the practice rows:</strong> 3 assets, 4 inspections, 2 teams. TR-0301 has no inspection rows at all — that is a <em>query result</em> (“assets never inspected”), not a special value on the asset. Every <code>asset_id</code> and <code>team_id</code> on an inspection exists in its parent table, so the relationships resolve.</p>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“Join the tables once and store the joined result as the working dataset — faster to query.”</em> The joined table has mixed grain (one row per inspection, each carrying a copy of the asset) and cannot show an asset with zero inspections without a blank-filled row. It is a fine <em>output</em> for a report (Chapter 11 produces such outputs) and a bad <em>source</em>.</p></div>

  <h2><span class="mod">8.5.3</span>A diagram, a join, and an enforced rule are three different things</h2>
  <p>The diagram states what is <em>true</em>. Several mechanisms can make software <em>act</em> on it — and they enforce very different amounts of it. Mixing them up is how a design “with relationships” ends up full of orphaned rows.</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Mechanism</th><th>What it is</th><th>Lives in</th><th>What it enforces</th></tr></thead>
    <tbody>
      <tr><td><strong>Conceptual relationship</strong></td><td>The diagram and the sentence “each inspection belongs to one asset”</td><td>Your design document</td><td class="no">Nothing by itself</td></tr>
      <tr><td><strong>Join</strong> (ArcGIS, QGIS)</td><td>A temporary lookup that appends matching fields from another table, for display or query</td><td>The map/layer, not the data. Esri: “a temporary table association … stored in the layer’s properties”. QGIS: a layer property, one-to-one; “if the join field contains duplicate matching values, only the first fetched feature is picked”</td><td class="no">Nothing; unmatched rows are just unmatched</td></tr>
      <tr><td><strong>Relate</strong> (ArcGIS)</td><td>A stored link to <em>select</em> related rows without appending fields</td><td>The project; “only available as long as the project is open”</td><td class="no">Nothing on the data</td></tr>
      <tr><td><strong>QGIS relation</strong></td><td>A parent/child link declared for forms and selection</td><td>The QGIS project (Project ▸ Properties ▸ Relations) — “project level settings”</td><td>In QGIS editing only; <em>Composition</em> strength cascades deletes (“on deleting a feature the children are deleted as well”). Nothing outside QGIS sees it</td></tr>
      <tr><td><strong>ArcGIS relationship class</strong></td><td>“a dataset type in the geodatabase that stores information about the relationship”; “physically stored and persists in the geodatabase”</td><td>The geodatabase, next to the tables; both must be in the same geodatabase</td><td>Cardinality and behaviour on edit: in a <em>simple</em> class deleting the origin sets the child’s foreign key to null; in a <em>composite</em> class the children are deleted too (“cascade delete”)</td></tr>
      <tr><td><strong>Database foreign key</strong> (PostgreSQL / PostGIS)</td><td>A declared constraint: values “must match the values appearing in some row of another table” — referential integrity</td><td>The database schema</td><td class="yes">Every insert/update/delete, from every client; <code>ON DELETE</code> can be NO ACTION, RESTRICT, CASCADE or SET NULL</td></tr>
    </tbody></table></div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>A join is not a relationship — watch what happens to the rows</h3>
    <p>Below, Assets is joined to Inspections in a map layer. Choose how the software treats the one-to-many match and see the result. (ArcGIS and QGIS behave differently here, so the result is not even portable between them.)</p>
    <div class="opbtns">
      <button class="btn small" id="jFirst" aria-pressed="true">Keep only the first match (QGIS join)</button>
      <button class="btn small" id="jAll">Repeat the asset for every match (one-to-many join)</button>
      <button class="btn small" id="jRel">Keep separate tables + relationship (the design)</button>
    </div>
    <div id="joinOut"></div>
    <div class="status-line q" id="joinStatus"></div>
  </div>

  <p><strong>Three consequences for the designer:</strong></p>
  <ol>
    <li><strong>The storage decides which rules exist.</strong> The same diagram in a Shapefile enforces nothing (a Shapefile has no relationship datasets and loses domains on conversion); in a QGIS project it is enforced only while editing in QGIS; in a geodatabase it is enforced by ArcGIS clients that honour relationship classes; in PostgreSQL by the database for every client. The design document must say which enforcement it <em>expects</em> and which it only <em>hopes</em> for (module 8.6.3).</li>
    <li><strong>A join is a read-time convenience.</strong> A one-to-many relationship seen through a join either repeats the “one” side or drops all but the first match.</li>
    <li><strong>Relationship classes have their own words.</strong> Esri calls the “one” side the <strong>origin</strong> and the “many” side the <strong>destination</strong>; both tables need a shared field of the same data type — the primary key in the origin, the foreign key in the destination. Cardinality may be 1:1, 1:M or M:N; an M:N (or any <em>attributed</em>) relationship class uses an intermediate table where “each row associates one origin object with one destination object” — exactly the junction table above, created and maintained by the geodatabase.</li>
  </ol>
  <div class="callout note"><span class="label">Which will the practice town use?</span><p><em>Conceptually:</em> Asset→Inspection is 1:M; Inspection→Photo is 1:M and a photo means nothing without its inspection (a candidate for cascade delete); Team↔Ward is M:N. <em>Implementation</em> is deferred: the lab asks for the relationship <em>definitions</em> — tables, keys, cardinality, and what should happen when a parent is deleted. Whether cascade delete is <em>wanted</em> is a business question: does removing a decommissioned asset really mean its inspection history should vanish? Module 8.7 says: usually not.</p></div>

  <div class="quiz" data-answer="1" data-fb="Nothing was lost: three complaints have a blank asset_id by design (optional relationship), so the join simply appends nothing for them. The mechanism that makes the optional link explicit is a relationship definition with a nullable foreign key — in a database, a FOREIGN KEY on a nullable column; in ArcGIS, a simple relationship class where unmatched complaints have no related row.">
    <div class="q">The Request table has an optional <code>asset_id</code>. A colleague joins Requests to Assets in a map and reports “the join lost three requests”. What really happened?</div>
    <div class="opts">
      <button class="opt">The join deleted three rows that had no match; they must be restored from backup.</button>
      <button class="opt">Nothing was lost: the three rows have a blank <code>asset_id</code> by design; the join just shows empty appended fields for them.</button>
      <button class="opt">The relationship class cascaded a delete.</button>
      <button class="opt">The three requests were duplicates and were merged.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  /* ER diagram */
  const info = {
    Asset: { s: "Asset — one row per physical thing. Has a point. Pointed to by Inspection, Request and StreetlightDetail.", cols: ["asset_id", "type", "x", "y", "install_year"], rows: F8.assets.map(a => [a.id, a.typeName, a.x, a.y, a.year]) },
    Inspection: { s: "Inspection — one row per visit. No shape: it points to the asset (asset_id) and the crew (team_id).", cols: ["inspection_id", "asset_id", "team_id", "visited (IST)", "condition", "defects"], rows: F8.inspections.map(i => [i.id, i.asset, i.team, i.ist, i.cond, i.defects]) },
    Team: { s: "Team — one row per crew. Pointed to by Inspection and by TeamWardCoverage.", cols: ["team_id", "name"], rows: F8.teams.map(t => [t.id, t.name]) },
    Ward: { s: "Ward — one row per administrative area (polygon). Pointed to by the coverage table.", cols: ["ward_code", "corners"], rows: F8.wards.map(w => [w.id, w.pts.map(p => `(${p[0]}, ${p[1]})`).join(" ")]) },
    Coverage: { s: "TeamWardCoverage — the junction table for Team ↔ Ward. One row per (team, ward) pairing; the dates belong to the PAIR, not to either side.", cols: ["team_id", "ward_code", "from_date", "to_date"], rows: [["T-N", "A", "2026-01-01", ""], ["T-S", "A", "2026-01-01", ""], ["T-S", "B", "2026-01-01", ""]] },
    Photo: { s: "InspectionPhoto — one row per photo file, pointing at its inspection. Never a comma-separated list of file names (8.7).", cols: ["photo_id", "inspection_id", "file_name", "subject"], rows: [["PH-0003-1", "INS-0003", "sl113_b.jpg", "LAMP"]] },
    Request: { s: "Request — one row per complaint, with its OWN point (where the citizen stood). asset_id may be blank.", cols: ["request_id", "x", "y", "what", "asset_id"], rows: F8.requests.map(r => [r.id, r.x, r.y, r.what, r.asset ?? ""]) },
    Detail: { s: "StreetlightDetail — streetlight-only facts, one row per streetlight (asset_id unique). Drains and trees simply have no row here, so “not applicable” is structural.", cols: ["asset_id", "lamp_wattage_w", "lamp_type"], rows: [["SL-0113", 70, "LED"]] }
  };
  const fig = document.getElementById("erFig");
  function showER(k) {
    renderER(fig, { lit: k, caption: "Design for the practice town (instructional; not a mandated Esri schema). Click a box.", onClick: showER });
    if (k) { document.getElementById("erInfo").textContent = info[k].s; document.getElementById("erRows").innerHTML = tableHTML(info[k].cols, info[k].rows, { mono: [0, 1] }); }
  }
  showER(null);

  /* join demo */
  const out = document.getElementById("joinOut"), st = document.getElementById("joinStatus");
  const insp = a => F8.inspections.filter(i => i.asset === a.id).sort((p, q) => p.id < q.id ? -1 : 1);
  function join(mode) {
    ["jFirst", "jAll", "jRel"].forEach(id => document.getElementById(id).setAttribute("aria-pressed", "false"));
    if (mode === "first") {
      document.getElementById("jFirst").setAttribute("aria-pressed", "true");
      const rows = F8.assets.map(a => { const i = insp(a)[0]; return [a.id, a.typeName, i ? i.id : "", i ? i.ist : "", i ? i.cond : ""]; });
      out.innerHTML = tableHTML(["asset_id", "type", "joined inspection", "visited", "condition"], rows, { mono: [0, 2, 3] });
      st.className = "status-line bad"; st.textContent = "3 rows out. DR-0042 and SL-0113 each show ONE of their visits (whichever came first) — the other is silently invisible. TR-0301 has blanks. Nothing is stored; close the map and the join is gone.";
    } else if (mode === "all") {
      document.getElementById("jAll").setAttribute("aria-pressed", "true");
      const rows = []; F8.assets.forEach(a => { const is = insp(a); if (!is.length) rows.push([a.id, a.typeName, a.year, "", "", ""]); is.forEach(i => rows.push([a.id, a.typeName, a.year, i.id, i.ist, i.cond])); });
      out.innerHTML = tableHTML(["asset_id", "type", "install_year", "inspection", "visited", "condition"], rows, { mono: [0, 3, 4] });
      st.className = "status-line bad"; st.textContent = "5 rows out. SL-0113’s type and year are now copied on two rows; TR-0301 needs a blank-filled row to exist at all. Mixed grain — a fine report, a bad place to store data.";
    } else {
      document.getElementById("jRel").setAttribute("aria-pressed", "true");
      out.innerHTML = `<div class="grid-2">${tableHTML(["asset_id", "type", "install_year"], F8.assets.map(a => [a.id, a.typeName, a.year]), { mono: [0], caption: "Asset — 3 rows" })}${tableHTML(["inspection_id", "asset_id", "visited", "condition"], F8.inspections.map(i => [i.id, i.asset, i.ist, i.cond]), { mono: [0, 1, 2], caption: "Inspection — 4 rows, each pointing at an asset" })}</div>`;
      st.className = "status-line ok"; st.textContent = "Every fact stored once. All four visits kept. “Assets never inspected” is just a query (TR-0301). Whether a bad asset_id is REJECTED depends on the storage — see the table above.";
    }
  }
  document.getElementById("jFirst").addEventListener("click", () => join("first"));
  document.getElementById("jAll").addEventListener("click", () => join("all"));
  document.getElementById("jRel").addEventListener("click", () => join("rel"));
  join("first");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
