<?php $page = ['title' => '7.5 ArcGIS geodatabases', 'chapter' => 7, 'module' => '7.5']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 7.5 · ArcGIS-specific, with the PostGIS comparison</div>
    <h1>Storage plus rules</h1>
    <p class="lead">An ArcGIS <strong>geodatabase</strong> is two things at once: a place where bytes are stored, and a set of extra knowledge — an <em>information model</em> — about what those bytes mean. Keeping the two apart explains three confusions developers hit on day one with PostgreSQL.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Explain what a geodatabase adds beyond “a table with a geometry column”, and name the three types.</li>
      <li>Tell a <strong>feature class</strong>, a <strong>table</strong> and a <strong>feature dataset</strong> apart (introductory level; creating them is Phase 2).</li>
      <li>Say why PostGIS is not automatically an enterprise geodatabase, and why ArcGIS Data Store is a different thing again.</li></ul></div>
  </div>

  <h2><span class="mod">7.5.1</span>Two levels</h2>
  <p>Esri defines it at both levels. Storage: “a collection of geographic datasets of various types held in a common file system folder, or a multiuser relational database management system such as IBM Db2, Microsoft SQL Server, Oracle, PostgreSQL, or SAP HANA.” Model: “geodatabases have a comprehensive information model … implemented as a series of tables containing feature classes and attributes. In addition, advanced GIS data objects add real-world behavior, rules for managing spatial integrity, and tools for working with spatial relationships.”</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Click the pieces</h3>
    <div class="twolevel">
      <div class="level model"><h4>Information model — what ArcGIS <em>knows</em> about the bytes</h4><div class="tilesrow" id="modelTiles"></div></div>
      <div class="level store"><h4>Physical store — where the bytes <em>are</em></h4><div class="tilesrow" id="storeTiles"></div></div>
      <div class="sidebox" id="dsBox"><strong>ArcGIS Data Store</strong> — deliberately drawn outside: ArcGIS-managed storage for <em>hosted</em> layers, reached only through web layers (7.5.3). Click me.</div>
    </div>
    <div class="result" id="lvlNote">Click a tile.</div>
  </div>
  <div class="table-wrap"><table>
    <thead><tr><th>Type</th><th>Physical form (Esri)</th><th>Editors</th><th>Notes</th></tr></thead>
    <tbody>
      <tr><td><strong>File geodatabase</strong></td><td>“stored as multiple files in a folder with a .gdb extension. Each dataset is contained in a single file.” Datasets grow to 1 TB by default.</td><td>Single editor, many readers</td><td>Free with ArcGIS Pro; full information model; no versioning</td></tr>
      <tr><td><strong>Mobile geodatabase</strong></td><td>“stored in an SQLite database that is entirely contained in a single file and has a .geodatabase extension”; 2 TB limit</td><td>Single editor</td><td>Domains, subtypes, relationship classes, attachments; readable with SQL without a licence because SQLite needs none; no raster datasets</td></tr>
      <tr><td><strong>Enterprise geodatabase</strong></td><td>“stored in relational databases” — Oracle, SQL Server, Db2, PostgreSQL, SAP HANA</td><td>Many editors</td><td>Versioning; security “managed through the DBMS”</td></tr>
    </tbody></table></div>
  <div class="callout dev"><span class="label">Developer view</span><p>A file geodatabase is like an app’s private data folder with its own schema registry: fast, portable, single-writer. An enterprise geodatabase is like adding an ORM’s metadata tables to a shared production database: it is still Postgres, but the application now recognises its own objects in it. <strong>Where the comparison breaks:</strong> an ORM registry only <em>describes</em> tables; the geodatabase model also <em>enforces spatial behaviour</em> (topology rules, network connectivity, attribute rules) that the database itself knows nothing about.</p></div>
  <p class="small"><strong>Transferability.</strong> The file geodatabase is an Esri format but not locked to Esri software: GDAL’s OpenFileGDB driver gives QGIS read access to file geodatabases from ArcGIS 10 onwards and, since GDAL 3.6, write and update, including field domains and relationships. As with GeoPackage extensions, what round-trips through a non-Esri writer must be checked, not assumed.</p>

  <h2><span class="mod">7.5.2</span>Feature class, table, feature dataset</h2>
  <div class="grid-3">
    <div class="rep"><h4>Feature class</h4><p>“A collection of geographic features with the same geometry type (such as point, line, or polygon), the same attributes, and the same spatial reference.”</p><p class="small">Town: <code>Assets</code> (points), <code>Roads</code> (lines), <code>Wards</code> (polygons). Developer reading: a table with exactly one geometry column, one geometry type and one CRS for every row.</p></div>
    <div class="rep"><h4>Table (non-spatial)</h4><p>“The basic storage object in the database … Tables that contain spatial attributes are called feature classes.”</p><p class="small">Town: <code>Inspections</code> — one row per visit, keyed to an asset by <code>asset_id</code> (Chapter 3, Table F9). Developer reading: an ordinary relational table, relatable to a feature class (Chapter 8).</p></div>
    <div class="rep"><h4>Feature dataset</h4><p>“A collection of related feature classes that share a common coordinate system”, used “to facilitate creation of controller datasets” such as a topology or a utility network.</p><p class="small">Town: a <code>Municipal</code> dataset holding <code>Wards</code> and <code>Roads</code> so a rule like “wards do not overlap” can be added later. Developer reading: a named group whose members are forced to share a CRS.</p></div>
  </div>
  <p>Two things you can already reason about: a shapefile or a single GeoPackage table maps onto a <em>feature class</em>; a <code>.dbf</code> alone or a GeoPackage attributes table onto a <em>table</em>; nothing outside a geodatabase maps onto a <em>feature dataset</em> — one reason “export the whole geodatabase to GeoPackage” flattens structure (7.4.3). And because a feature dataset fixes the CRS for its members, Esri warns not to run <em>Define Projection</em> on an existing one — Chapter 6’s lesson applied to a container.</p>
  <p class="small">In Phase 1 you only <em>inspect</em>: open the Catalog pane, expand a <code>.gdb</code>, note which items are feature classes (shape icon), tables (grid icon) and feature datasets (group), and read each item’s fields and spatial reference in its properties. That inspection is the ArcGIS form of the 7.7 “before” snapshot.</p>

  <h2><span class="mod">7.5.3</span>Three things that all involve PostgreSQL — and are not the same</h2>
  <div class="tri">
    <div class="t"><h4>PostGIS</h4><p>“Extends the capabilities of the PostgreSQL relational database by adding support for storing, indexing, and querying geospatial data.” Open source, vendor-neutral; QGIS, GeoServer and your own code use it directly.</p><p><span class="chip q">who defines the schema: you, with SQL</span></p></div>
    <div class="t"><h4>Enterprise geodatabase in PostgreSQL</h4><p>Esri: “If the database contains geodatabase system tables, it is considered a geodatabase in ArcGIS.” Those tables are created by the <em>Enable Enterprise Geodatabase</em> tool, which needs an ArcGIS Server authorisation file and ArcGIS Pro Standard/Advanced or ArcGIS Server. A spatial type must be enabled first: Esri ST_Geometry, PostGIS geometry, or PostGIS geography.</p><p><span class="chip q">you, through ArcGIS tools; ArcGIS adds its system tables</span></p></div>
    <div class="t"><h4>ArcGIS Data Store</h4><p>“An application that provides the tooling and functional capabilities required to create and maintain the system storage types … used by the hosting server”: relational, object, spatiotemporal, graph stores. “Access to the content of each data store … is provided exclusively through web layers.”</p><p><span class="chip q">ArcGIS, when a hosted layer is published</span></p></div>
  </div>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>True or false?</h3>
    <div id="claims"></div>
  </div>
  <div class="table-wrap"><table>
    <thead><tr><th></th><th>PostGIS database</th><th>Enterprise geodatabase (PostgreSQL)</th><th>ArcGIS Data Store (relational store)</th></tr></thead>
    <tbody>
      <tr><td>How you reach the bytes</td><td>Any SQL client</td><td>SQL client (read) or ArcGIS (read/write with model rules)</td><td><strong>Only</strong> through web layers</td></tr>
      <tr><td>Chapter 2 role</td><td>R2 storage, vendor-neutral</td><td>R2 storage, ArcGIS-aware (“user storage” in Enterprise terms)</td><td>R2 for hosted layers only (“system storage”)</td></tr>
      <tr><td>Phase 1 boundary</td><td>concept only</td><td>concept only — no administration here</td><td>concept only — no deployment here</td></tr>
    </tbody></table></div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“Data Store is where our enterprise geodatabase lives.”</em> No. The relational data store holds the data behind <em>hosted</em> feature layers, is managed by ArcGIS, and is reached only through web layers. An enterprise geodatabase is <em>your</em> database with ArcGIS system tables added, reachable by SQL and registered with the server as user storage. A deployment often has both, administered differently.</p></div>

  <div class="quiz" data-answer="2" data-fb="Editing through a database connection needs the geodatabase system tables (created by Enable Enterprise Geodatabase, which needs the ArcGIS Server authorisation file and Pro Standard/Advanced or Server) and the table registered with the geodatabase. Unregistered ‘database data’ can be viewed and published but not edited that way. PostGIS alone only supplies the spatial type.">
    <div class="q">A partner says: “Our PostgreSQL has PostGIS and your Assets table is in it, so just connect ArcGIS Pro and edit.” What must be true for editing through a database connection to work?</div>
    <div class="opts">
      <button class="opt">Nothing more — PostGIS is enough.</button>
      <button class="opt">The table needs a .prj file beside it.</button>
      <button class="opt">The database must contain the geodatabase system tables (Enable Enterprise Geodatabase, licensed) and the table must be registered with the geodatabase.</button>
      <button class="opt">The data must first be copied into ArcGIS Data Store.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const M = [
    ["feature classes", "Which tables are spatial, with which geometry type and CRS."],
    ["tables", "Non-spatial rows — inspections, teams — that can be related to features."],
    ["feature datasets", "Named groups whose members share a CRS, so cross-class rules can exist."],
    ["relationship classes", "Which table refers to which (Chapter 8) — kept by the model, not just by matching values."],
    ["domains & subtypes", "Allowed values and categories (Chapter 8), enforced when editing through ArcGIS."],
    ["topology / network rules", "‘Wards may not overlap’, ‘pipes must connect’ — behaviour the DBMS itself knows nothing about."],
    ["editor tracking, versioning", "Who changed what, and (enterprise only) parallel edit versions."]
  ];
  const S = [
    [".gdb folder", "File geodatabase: one file per dataset inside a folder. Single editor."],
    [".geodatabase (SQLite)", "Mobile geodatabase: one SQLite file, readable with plain SQL."],
    ["tables in PostgreSQL / Oracle / SQL Server…", "Enterprise geodatabase: your DBMS, plus Esri’s system tables that describe the model."]
  ];
  const mt = document.getElementById("modelTiles"), st = document.getElementById("storeTiles"), note = document.getElementById("lvlNote");
  M.forEach(([t, d]) => { const b = document.createElement("button"); b.className = "tl2 chipbtn"; b.textContent = t; b.addEventListener("click", () => note.innerHTML = `<strong>${t}</strong> (model level): ${d}`); mt.appendChild(b); });
  S.forEach(([t, d]) => { const b = document.createElement("button"); b.className = "tl2 chipbtn"; b.textContent = t; b.addEventListener("click", () => note.innerHTML = `<strong>${t}</strong> (store level): ${d} The <em>same</em> information model sits on top of all three stores — that is what makes them all “geodatabases”.`); st.appendChild(b); });
  document.getElementById("dsBox").addEventListener("click", () => note.innerHTML = "<strong>ArcGIS Data Store</strong> is not one of the three geodatabase types. Esri’s Enterprise storage page calls it <em>system storage</em>: “managed by ArcGIS, which means you interact with that data only using the web service or layer”. Your own databases, folders and cloud stores are <em>user storage</em>. Hosted feature layers live in the relational data store; layers published from an enterprise geodatabase are ‘by reference’ to user storage.");
  /* claims */
  const C = [
    { t: "“We have PostGIS, so we have an enterprise geodatabase.”", ans: false, why: "PostGIS supplies a spatial type. The geodatabase exists only once the geodatabase system tables have been created with the licensed Enable Enterprise Geodatabase tool." },
    { t: "“An enterprise geodatabase in PostgreSQL always uses PostGIS.”", ans: false, why: "It may use Esri’s ST_Geometry instead; PostGIS geometry/geography is one of three options — and the only option in the cloud database services Esri lists." },
    { t: "“A PostGIS table can be shown in ArcGIS Pro without a geodatabase.”", ans: true, why: "Through a database connection — viewable and publishable, but not editable through that connection (Esri: to edit database data you publish an editable web feature layer)." },
    { t: "“Our hosted feature layers’ data is in that enterprise geodatabase, because both are ArcGIS.”", ans: false, why: "Hosted layer data is in ArcGIS Data Store’s relational store (system storage), reached only through web layers. An enterprise geodatabase is user storage published by reference." }
  ];
  const cl = document.getElementById("claims");
  C.forEach(c => {
    const d = document.createElement("div"); d.className = "quiz"; d.innerHTML = `<div class="q">${c.t}</div><div class="opts"><button class="opt">True</button><button class="opt">False</button></div><div class="fb"></div>`; cl.appendChild(d);
    const ans = c.ans ? 0 : 1, fb = d.querySelector(".fb"), opts = d.querySelectorAll(".opt");
    opts.forEach((b, i) => b.addEventListener("click", () => { opts.forEach(x => x.classList.remove("right", "wrong")); const ok = i === ans; b.classList.add(ok ? "right" : "wrong"); if (!ok) opts[ans].classList.add("right"); fb.className = "fb show " + (ok ? "ok" : "no"); fb.textContent = (ok ? "Correct. " : "Not quite. ") + c.why; }));
  });
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
