<?php $page = ['title' => '7.4 Richer containers: GeoPackage and GeoTIFF', 'chapter' => 7, 'module' => '7.4']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 7.4 · Format rules from the OGC GeoPackage 1.4.0 and GeoTIFF 1.1 standards</div>
    <h1>A database with rules, and a picture with a position stapled on</h1>
    <p class="lead"><strong>GeoPackage</strong> is an ordinary SQLite database file that follows a published set of table rules. <strong>GeoTIFF</strong> is an ordinary TIFF image with extra tags that say where on Earth it belongs. Both are better boxes than a shapefile — and both still need inspecting before use.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Open a GeoPackage the way you would open any SQLite file and read its bookkeeping tables.</li>
      <li>Understand what “extension” means in GeoPackage and why “supports GeoPackage” is not a full promise.</li>
      <li>Run the seven-item raster inspection on any TIFF instead of assuming it is georeferenced.</li>
      <li>Read the format comparison table and know the three ways a conversion loses things around the geometry.</li></ul></div>
  </div>

  <h2><span class="mod">7.4.1</span>GeoPackage: SQLite plus rules</h2>
  <p>The OGC site describes GeoPackage as “an open, standards-based, platform-independent, portable, self-describing, compact format for transferring geospatial information”, holding “vector features, tile matrix sets of imagery and raster maps at various scales, attributes (non-spatial data), extensions” in one file. The version adopted today is <strong>1.4.0</strong>. Keep the site’s own distinction in mind: “a GeoPackage is the SQLite container and the GeoPackage Encoding Standard governs the rules and requirements of content stored in a GeoPackage container.”</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Open assets_ch7_R-C.gpkg like a database</h3>
    <p>This mock shows what a plain SQLite browser would show after the lab’s export (simulated). Click a table. The <code>gpkg_*</code> tables are the bookkeeping the standard requires; <code>assets_ch7</code> is your data.</p>
    <div class="sqlite">
      <div class="tables" id="gpkgTables"></div>
      <div class="view" id="gpkgView"></div>
    </div>
  </div>
  <div class="table-wrap"><table>
    <thead><tr><th>Rule (requirement number as printed)</th><th>What it says</th><th>Why you care</th></tr></thead>
    <tbody>
      <tr><td>Req 1, 2</td><td>A GeoPackage “SHALL be a SQLite … database file using version 3”; its header carries the application id <code>GPKG</code>.</td><td>Any SQLite tool opens it. The id — not the extension — is what tells a reader it is a GeoPackage.</td></tr>
      <tr><td>Req 3</td><td>File extension “.gpkg”.</td><td>ArcGIS Pro requires the extension to open one.</td></tr>
      <tr><td>Req 10, 11</td><td><code>gpkg_spatial_ref_sys</code> is mandatory and must contain at least: 4326 (WGS 84), <strong>−1 “for undefined Cartesian coordinate reference systems”</strong>, 0 (undefined geographic).</td><td>An honest slot for Table F7 — a flat grid with no Earth reference. GeoJSON has no such slot.</td></tr>
      <tr><td>Req 13</td><td><code>gpkg_contents</code> lists every dataset with its type (<code>features</code>, <code>tiles</code>, <code>attributes</code>), extent and <code>srs_id</code>.</td><td>One query tells you what is inside — the “self-describing” claim.</td></tr>
      <tr><td>Req 5, Table 1</td><td>Column types are fixed. <code>TEXT</code> is UTF-8/UTF-16; <code>DATE</code> is <code>YYYY-MM-DD</code>; <code>DATETIME</code> is “YYYY-MM-DDTHH:MM:SS.SSSZ with … Z suffix for coordinated universal time (UTC)”.</td><td>Long names safe; real nulls kept. But a timestamp with an offset <strong>must become UTC</strong> — <code>2026-08-30T02:10:00+05:30</code> → <code>2026-08-29T20:40:00.000Z</code>. An adaptation you must write down.</td></tr>
      <tr><td>Req 29–31</td><td>A feature table has an integer primary key and “only one geometry column”.</td><td>One geometry per table, like a feature class.</td></tr>
      <tr><td>Req 119</td><td>Non-spatial “attributes” tables are allowed.</td><td>Inspections without geometry can travel in the same file.</td></tr>
      <tr><td>Req 58–64</td><td>Extensions are declared in <code>gpkg_extensions</code>; if that table is absent or empty “the file is a GeoPackage (as opposed to an Extended GeoPackage)”. Each row has a scope <code>read-write</code> or <code>write-only</code>.</td><td>A reader can “fail fast” by checking one table. Extensions add spatial indexes, curved geometry, metadata, related tables.</td></tr>
    </tbody></table></div>
  <div class="callout warn"><span class="label">“Supports GeoPackage” means “supports the core”</span><p>The standard is designed for extension, and OGC has adopted extensions such as the Related Tables Extension. But an extension only helps if <em>both</em> the writer and the reader implement it; the standard itself warns that custom extensions “do introduce interoperability risks”. A GeoPackage written by product A with related tables may open in product B with the tables present and the <em>relationship</em> invisible. Check each extension per product.</p></div>
  <div class="callout dev"><span class="label">Developer view</span><p>A GeoPackage is an SQLite file with a schema convention — like a mobile app’s local database. That is nearly exact, which is the point: use your SQL skills to inspect it. <strong>Where the comparison breaks:</strong> the geometry column is a BLOB in the GeoPackage binary encoding that plain SQL cannot read, and the <code>gpkg_*</code> tables must stay consistent with the user tables. Hand-editing with SQL can give you a file that is valid SQLite and invalid GeoPackage.</p></div>
  <p class="small"><strong>Platform notes.</strong> ArcGIS Pro opens a GeoPackage through a folder connection; it “supports a single-user connection”; fields can be added but not renamed or deleted from the fields view; the <em>Create SQLite Database</em> tool creates a GeoPackage at a chosen standard version (1.0 to 1.4). QGIS creates GeoPackage layers directly and exports to them with <em>Export ▸ Save Features As…</em>, where <em>Persist layer metadata</em> stores metadata inside the GeoPackage.</p>

  <h2><span class="mod">7.4.2</span>GeoTIFF: a TIFF with its position in the tags</h2>
  <p>OGC’s GeoTIFF 1.1 standard “defines the Geographic Tagged Image File Format (GeoTIFF) by specifying the content and structure of a group of industry-standard tag sets for the management of georeferenced or geocoded raster imagery”. The key fact: “A GeoTIFF file is a TIFF 6.0 file and inherits the file structure” — an ordinary image viewer opens it as a picture and simply ignores the geography. In plain words (QGIS’s introduction): georeferencing is “a coordinate for the top left pixel … the size of each pixel in the X direction, the size of each pixel in the Y direction, and the amount (if any) by which the image is rotated” — sometimes “provided in a small text file accompanying the raster”.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Inspect a .tif — which tags are present?</h3>
    <p>Tick the tags a file carries. The verdict tells you what you can and cannot conclude. The <code>.tif</code> extension itself proves only that it is a TIFF.</p>
    <div class="grid-2">
      <div class="taglist" id="tags"></div>
      <div class="result" id="tagVerdict"></div>
    </div>
  </div>
  <div class="table-wrap"><table>
    <thead><tr><th>Check</th><th>Where it lives in a GeoTIFF</th><th>What goes wrong if skipped</th></tr></thead>
    <tbody>
      <tr><td><strong>CRS</strong></td><td>GeoKeys — an EPSG code in <code>ProjectedCRSGeoKey</code> / <code>GeodeticCRSGeoKey</code>, or a user-defined definition</td><td>Cells drawn in the wrong place, or the “unknown CRS” guessing error</td></tr>
      <tr><td><strong>Bands</strong></td><td>Samples per pixel; band order is not self-explanatory</td><td>Reading band 1 of a colour image as elevation</td></tr>
      <tr><td><strong>Cell size</strong></td><td><code>ModelPixelScaleTag</code> or the transformation matrix, in the CRS’s units</td><td>Confusing 10 <em>degrees</em> with 10 <em>metres</em></td></tr>
      <tr><td><strong>Units of the values</strong></td><td><em>Not</em> in the standard tags — must come from metadata or the publisher</td><td>Reading elevation in feet as metres</td></tr>
      <tr><td><strong>NoData</strong></td><td><em>Not</em> part of the OGC standard. GDAL stores it in the “non standard TIFFTAG_GDAL_NODATA” tag 42113, one value for all bands</td><td>NoData counted as a real value — Chapter 4’s mean error (20 → 17.78)</td></tr>
      <tr><td><strong>Data type</strong></td><td>TIFF sample format (Byte, Int16, UInt16, Int32, Float32, Float64…)</td><td>Integer truncation of a smooth surface</td></tr>
      <tr><td><strong>Cell reference</strong></td><td><code>GTRasterTypeGeoKey</code>: <code>PixelIsArea</code> or <code>PixelIsPoint</code></td><td>A half-cell shift when combining rasters</td></tr>
    </tbody></table></div>
  <p class="small">GDAL adds a fallback: “if no georeferencing information is available in the TIFF file itself, GDAL will also check for, and use an ESRI world file with the extension .tfw, .tifw/.tiffw or .wld”. So a raster can look georeferenced in one folder and lose it when the <code>.tif</code> is copied without its <code>.tfw</code> — the same companion-file discipline as the shapefile. GeoTIFF is the <em>common</em> raster exchange choice, not the only one: GeoPackage can hold tile pyramids, and “Cloud-optimised GeoTIFF” is a layout of the same format.</p>

  <h2><span class="mod">7.4.3</span>Geometry survives; what is around it may not</h2>
  <p>Every format has a slot for geometry, so converters handle it well. What sits <em>around</em> the geometry — nulls, times, rules, relationships, styling — has no slot in many formats. Click a format column to highlight it. “Yes” means the format <em>has a place</em> for the property; it does not mean every tool fills it. The lab checks the tool.</p>
  <div class="controls" id="fmtBtns"></div>
  <div class="table-wrap"><table id="cmp">
    <thead><tr><th>Property</th><th>Coordinate CSV</th><th>GeoJSON</th><th>Shapefile</th><th>GeoPackage</th><th>File / enterprise geodatabase</th></tr></thead>
    <tbody>
      <tr><td>Geometry types</td><td>points only (columns)</td><td class="yes">point, line, polygon, multi-*</td><td>one type per file; no curves</td><td class="yes">all; curves via extension</td><td class="yes">all, incl. curves</td></tr>
      <tr><td>CRS stored inside</td><td class="no">no — readme only</td><td>fixed: WGS 84 lon/lat only</td><td>optional <code>.prj</code></td><td class="yes">yes; undefined flat grid allowed (−1)</td><td class="yes">yes; “Unknown” allowed</td></tr>
      <tr><td>Field name length</td><td>any (importer may limit)</td><td>any</td><td class="no">10 characters</td><td class="yes">practically unlimited</td><td class="yes">128 in a file geodatabase</td></tr>
      <tr><td>Null values</td><td>convention only</td><td class="yes">yes</td><td class="no">no — substituted</td><td class="yes">yes</td><td class="yes">yes</td></tr>
      <tr><td>Date <em>and</em> time</td><td>text</td><td>text (no date type)</td><td class="no">date only</td><td>DATETIME as UTC (Z)</td><td class="yes">date, date-only, time-only, timestamp offset</td></tr>
      <tr><td>Unicode text</td><td>if UTF-8 declared</td><td class="yes">yes</td><td class="no">code-page dependent</td><td class="yes">yes</td><td class="yes">yes</td></tr>
      <tr><td>Several datasets in one package</td><td class="no">no</td><td>one collection (mixed geometry allowed)</td><td class="no">no</td><td class="yes">yes</td><td class="yes">yes</td></tr>
      <tr><td>Non-spatial tables</td><td class="yes">it is one</td><td class="no">no</td><td><code>.dbf</code> alone</td><td class="yes">yes (attributes)</td><td class="yes">yes</td></tr>
      <tr><td>Relationships between tables</td><td class="no">no</td><td class="no">no</td><td class="no">no</td><td>extension only</td><td class="yes">relationship classes</td></tr>
      <tr><td>Rules: domains, subtypes, attribute rules</td><td class="no">no</td><td class="no">no</td><td class="no">lost on export</td><td class="no">not in core</td><td class="yes">yes</td></tr>
      <tr><td>Styling / symbology</td><td class="no">no</td><td class="no">no</td><td class="no">no (sidecar files)</td><td class="no">not in core; product-specific tables</td><td class="no">not in the dataset — maps / layer files</td></tr>
      <tr><td>Metadata inside the package</td><td class="no">no</td><td class="no">no</td><td><code>.shp.xml</code> sidecar</td><td class="yes">metadata extension tables</td><td class="yes">item metadata</td></tr>
      <tr><td>Multi-user editing</td><td class="no">no</td><td class="no">no</td><td class="no">no</td><td class="no">single-user</td><td>enterprise geodatabase: yes</td></tr>
    </tbody></table></div>
  <div class="tri">
    <div class="t"><h4>1. Geometry survives, typing degrades</h4><p>CSV or GeoJSON → anything: field types are re-guessed; dates become text or vice versa.</p></div>
    <div class="t"><h4>2. Geometry and typing survive, structure is lost</h4><p>Geodatabase → GeoPackage: relationship classes, domains, subtypes have no core slot. The tables arrive; the rules that kept them consistent do not — and nothing tells the recipient that a <code>ward_code</code> of <code>7</code> used to be checked against a list.</p></div>
    <div class="t"><h4>3. Everything except styling survives</h4><p>Any → any: symbology, labels and pop-ups live in maps, layer files or product sidecars (Chapter 3). A container that “stores styles” stores <em>its own product’s</em> styles.</p></div>
  </div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“GeoPackage is the open version of a file geodatabase.”</em> Both are single-file/folder containers holding many tables, and both keep nulls, long names and Unicode. But the geodatabase’s <em>information model</em> — relationships, domains, subtypes, topologies, versioning (7.5) — is mostly outside GeoPackage’s core. Converting one to the other keeps tables and loses model.</p></div>

  <div class="quiz" data-answer="0" data-fb="For the TIFF, the first question is whether it carries any georeferencing tags (GeoKeyDirectoryTag plus a tiepoint/scale or transformation tag); for the GeoPackage, the first question is what it contains — one SELECT on gpkg_contents. NoData and CRS come next in each case.">
    <div class="q">You receive <code>survey.tif</code> (no sidecars) and <code>survey.gpkg</code>. What is the very first thing to inspect in each?</div>
    <div class="opts">
      <button class="opt">TIFF: does it carry georeferencing tags at all? GeoPackage: <code>SELECT table_name, data_type, srs_id FROM gpkg_contents</code>.</button>
      <button class="opt">TIFF: the pixel colours. GeoPackage: the file size.</button>
      <button class="opt">Both: the file extension confirms what they are.</button>
      <button class="opt">TIFF: the NoData value. GeoPackage: the styling tables.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  /* GeoPackage browser mock */
  const T = {
    gpkg_contents: { sql: "SELECT table_name, data_type, identifier, min_x, min_y, max_x, max_y, srs_id FROM gpkg_contents;", cols: ["table_name", "data_type", "identifier", "min_x", "min_y", "max_x", "max_y", "srs_id"], rows: [["assets_ch7", "features", "Assets (Chapter 7 exchange variant, synthetic)", 205, 195, 2190, 950, -1]], note: "Req 13: one row per dataset. data_type is features / tiles / attributes. The extent matches the fixture (x 205–2190, y 195–950). srs_id −1 says ‘undefined flat grid’ — honest, not a guess. If your export wrote a different srs_id, record it." },
    gpkg_spatial_ref_sys: { sql: "SELECT srs_id, srs_name, organization, organization_coordsys_id FROM gpkg_spatial_ref_sys;", cols: ["srs_id", "srs_name", "organization", "organization_coordsys_id"], rows: [[4326, "WGS 84 geodetic", "EPSG", 4326], [-1, "Undefined Cartesian SRS", "NONE", -1], [0, "Undefined geographic SRS", "NONE", 0]], note: "Req 11: these three rows must always exist. A real file would also list any CRS its data uses (the definition column holds the WKT text)." },
    gpkg_geometry_columns: { sql: "SELECT table_name, column_name, geometry_type_name, srs_id, z, m FROM gpkg_geometry_columns;", cols: ["table_name", "column_name", "geometry_type_name", "srs_id", "z", "m"], rows: [["assets_ch7", "geom", "POINT", -1, 0, 0]], note: "Req 29–31: one geometry column per feature table, its type and SRS registered here." },
    gpkg_extensions: { sql: "SELECT * FROM gpkg_extensions;", cols: ["table_name", "column_name", "extension_name", "definition", "scope"], rows: [], note: "Empty (or absent) → plain GeoPackage, no extensions. If a writer added an R-tree spatial index you would see gpkg_rtree_index here with scope write-only; related tables would show gpkg_related_tables. A reader checks this table first to ‘fail fast’." },
    assets_ch7: { sql: "SELECT fid, asset_id, legacy_code, asset_type_local, condition_score, last_inspection_at FROM assets_ch7;", cols: ["fid", "asset_id", "legacy_code", "asset_type_local", "condition_score", "last_inspection_at"], rows: F7.rows.map((r, i) => [i + 1, r.asset_id, r.legacy_code, r.asset_type_local, r.condition_score, r.last_inspection_at ? toUTC(r.last_inspection_at).replace("Z", ".000Z") : null]), note: "Your data. Long names kept; NULL kept (TR-0301); legacy_code kept as TEXT with its zeros; last_inspection_at shown as GeoPackage DATETIME in UTC — SL-0055 now reads 2026-08-29. (Simulated: if you exported the timestamp as text it would stay as typed.)" }
  };
  const tabs = document.getElementById("gpkgTables"), view = document.getElementById("gpkgView");
  tabs.innerHTML = `<div class="grp">bookkeeping (gpkg_*)</div>` + ["gpkg_contents", "gpkg_spatial_ref_sys", "gpkg_geometry_columns", "gpkg_extensions"].map(n => `<button data-t="${n}">${n}</button>`).join("") + `<div class="grp">your data</div><button data-t="assets_ch7">assets_ch7</button>`;
  function showT(n) {
    tabs.querySelectorAll("button").forEach(b => b.classList.toggle("on", b.dataset.t === n));
    const t = T[n];
    view.innerHTML = `<div class="sql">${esc(t.sql)}</div><table><thead><tr>${t.cols.map(c => `<th>${c}</th>`).join("")}</tr></thead><tbody>${t.rows.length ? t.rows.map(r => `<tr>${r.map(v => `<td class="mono">${v === null ? '<span class="nullv">NULL</span>' : esc(v)}</td>`).join("")}</tr>`).join("") : `<tr><td colspan="${t.cols.length}" class="nullv">(no rows)</td></tr>`}</tbody></table><p class="small">${t.note}</p>`;
  }
  tabs.querySelectorAll("button").forEach(b => b.addEventListener("click", () => showT(b.dataset.t))); showT("gpkg_contents");
  /* TIFF tags */
  const TAGS = [
    { tg: "ModelTiepointTag (33922)", d: "which cell sits at which model coordinate", on: true, k: "tie" },
    { tg: "ModelPixelScaleTag (33550)", d: "cell size in model units", on: true, k: "scale" },
    { tg: "GeoKeyDirectoryTag (34735)", d: "the CRS description (EPSG code or user-defined)", on: true, k: "keys" },
    { tg: "GTRasterTypeGeoKey", d: "PixelIsArea or PixelIsPoint", on: true, k: "rt" },
    { tg: "TIFFTAG_GDAL_NODATA (42113)", d: "NoData value — GDAL’s non-standard tag", on: false, k: "nd" },
    { tg: "sidecar .tfw world file", d: "fallback placement if the tags are missing", on: false, k: "tfw" }
  ];
  const tl = document.getElementById("tags");
  TAGS.forEach((t, i) => { const l = document.createElement("label"); l.innerHTML = `<input type="checkbox" ${t.on ? "checked" : ""} data-i="${i}"><span class="tg">${t.tg}</span><span class="small">${t.d}</span>`; tl.appendChild(l); });
  function tagV() {
    const on = k => tl.querySelector(`input[data-i="${TAGS.findIndex(t => t.k === k)}"]`).checked;
    const placed = (on("tie") && on("scale")) ? "tags" : on("tfw") ? "tfw" : null;
    let s = "";
    if (!placed) s += "<strong style='color:var(--warn)'>Not georeferenced.</strong> It is a picture with rows and columns and no place in the world (Chapter 4’s independent check). Ask the publisher for placement information; do not guess. ";
    else if (placed === "tfw") s += "<strong>Placed only by the world file.</strong> Copy the .tif without its .tfw and the placement is gone. ";
    else s += "<strong>Placed by its own tags</strong> (tiepoint + pixel scale). ";
    if (placed) s += on("keys") ? "CRS is declared in the GeoKeys — read the code, check its units. " : "<span style='color:var(--warn)'>No GeoKeys → the CRS is unknown</span> even though the cells have a position; investigate. ";
    s += on("nd") ? "NoData is declared (non-standard GDAL tag; other writers use sidecar metadata). " : "<span style='color:var(--warn)'>No NoData declared</span> — every cell will count in statistics unless the publisher tells you the missing value. ";
    s += on("rt") ? "Cell reference declared (area vs point)." : "Cell reference not declared — assume PixelIsArea only with the publisher’s confirmation.";
    s += " <em>Still unknown from any tag: the units of the values (metres? feet? a category code?) — only the publisher can say.</em>";
    document.getElementById("tagVerdict").innerHTML = s;
  }
  tl.addEventListener("change", tagV); tagV();
  /* comparison highlighter */
  const names = ["Coordinate CSV", "GeoJSON", "Shapefile", "GeoPackage", "Geodatabase"];
  const fb = document.getElementById("fmtBtns");
  names.forEach((n, i) => { const b = document.createElement("button"); b.className = "chipbtn"; b.textContent = n; b.setAttribute("aria-pressed", "false"); b.addEventListener("click", () => { const on = b.getAttribute("aria-pressed") !== "true"; fb.querySelectorAll(".chipbtn").forEach(x => x.setAttribute("aria-pressed", "false")); b.setAttribute("aria-pressed", on); document.querySelectorAll("#cmp tr").forEach(tr => { [...tr.children].forEach((c, j) => { c.style.background = (on && j === i + 1) ? "#fff6d6" : ""; c.style.opacity = (on && j !== 0 && j !== i + 1) ? ".45" : ""; }); }); }); fb.appendChild(b); });
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
