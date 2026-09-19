<?php $page = ['title' => '7.3 Shapefile: compatibility and limits', 'chapter' => 7, 'module' => '7.3']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 7.3 · Format rules from Esri’s shapefile page and the GDAL driver</div>
    <h1>Five files pretending to be one</h1>
    <p class="lead">The <strong>Shapefile</strong> is Esri’s 1990s format. It is still the most widely <em>accepted</em> way to hand over vector data — so you must know it — and it is also the format most likely to lose meaning without a single error message — so you should rarely <em>choose</em> it for new work.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Name the companion files, which are required, and what happens when <code>.prj</code> or <code>.cpg</code> is missing.</li>
      <li>Predict, field by field, what a shapefile export does to Table F7 — before running anything.</li>
      <li>Decide, from the <em>receiver’s</em> needs, when a shapefile is a justified delivery.</li></ul></div>
  </div>

  <div class="callout note"><span class="label">In Esri’s own words</span><p>Shapefiles “cannot store null values, they round up numbers, they have poor support for Unicode character strings, they do not allow field names longer than 10 characters, and they cannot store time in a date field … So unless your data will have very simple attributes and does not require geodatabase capabilities, do not use shapefiles.” (ArcGIS Pro, <em>Geoprocessing considerations for shapefile output</em>.)</p></div>

  <h2><span class="mod">7.3.1</span>What is in the folder</h2>
  <p>A “shapefile” is a <strong>group of files with the same base name in one folder</strong>. ArcGIS Pro shows them as one item; File Explorer shows the truth. Tick and untick files to see what you would be able to do with the delivery.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Inspect the delivery folder</h3>
    <div class="grid-2">
      <div class="folder" id="folder"><div class="fh">assets_ch7 — companion files</div></div>
      <div>
        <div class="result" id="folderVerdict"></div>
        <p class="small" style="margin-top:.6rem">Narrate it as five roles — <em>geometry, its index, the attribute table, the CRS note, the encoding note</em> — rather than reciting extensions. Index and metadata files are harmless if absent.</p>
      </div>
    </div>
  </div>
  <p><strong>Handling rule.</strong> Copy, rename, zip and email <em>all</em> the companion files together. Renaming <code>roads.shp</code> to <code>streets.shp</code> while leaving <code>roads.dbf</code> behind gives you geometry with no attributes — if it opens at all.</p>

  <h2><span class="mod">7.3.2</span>Why “.prj not required” is a trap</h2>
  <p>The <code>.prj</code> holds the coordinate system definition as text. Esri lists it as <em>not required</em> — so a shapefile without it opens perfectly, with an <strong>unknown</strong> coordinate system. Chapter 5 taught what to do: investigate, never guess. Chapter 6 taught the remedy once the CRS is <em>established from evidence</em>: <em>Define Projection</em> “only updates the existing coordinate system information; it does not modify any geometry” — and writing a <code>.prj</code> is exactly that. Writing the <em>wrong</em> <code>.prj</code> is the relabelling error: the data still opens, still draws, and is now confidently wrong. (Chapter 6’s check question about a shapefile delivered without its <code>.prj</code> was exactly this case.)</p>
  <p class="small">Different software may write slightly different <code>.prj</code> text for the same CRS. Do not compare <code>.prj</code> files as strings to decide whether two datasets share a CRS — read the definitions.</p>

  <h2><span class="mod">7.3.3</span>Predict the loss before you export</h2>
  <p>Every limit below is a rule of the <code>.dbf</code> (dBASE) table format, so it applies in every product. <em>How</em> a product copes — rename, substitute, warn — is product behaviour. Pick a record and the export settings; the simulation applies the published rules. <strong>The lab asks you to record what your software really did.</strong></p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>One record through a shapefile export</h3>
    <div class="controls">
      <label>Record <select id="recSel"></select></label>
      <label><input type="checkbox" id="tsTyped" checked> timestamp typed as date/time before export</label>
      <label><input type="checkbox" id="utcFirst"> tool converts to UTC first</label>
    </div>
    <div class="table-wrap"><table class="diff" id="shpDiff"></table></div>
    <div class="result" id="shpSummary"></div>
  </div>

  <div class="table-wrap"><table>
    <thead><tr><th>Limit (format rule)</th><th>F7 field it bites</th><th>What happens</th></tr></thead>
    <tbody>
      <tr><td>“Field names cannot be longer than 10 characters.”</td><td>7 of the 10 names</td><td>Names are cut. <code>asset_type_en</code> and <code>asset_type_local</code> both start with the same ten letters, so the writer must invent different names; GDAL cuts to 8 and adds a number, ArcGIS Pro has its own way — <em>observe it</em>. Any code or join keyed on the old names breaks.</td></tr>
      <tr><td>“Null values are not supported in shapefiles.”</td><td><code>condition_score</code>, <code>last_inspection_at</code> for TR-0301</td><td>Esri’s table: for most tools a numeric null becomes <strong>0</strong>; text null becomes one space; a date null is “stored as zero, but displays &lt;null&gt;”. Afterwards “ArcGIS cannot determine whether a field value represents a null value or a legitimate value”. <em>Not assessed</em> becomes <em>score 0</em> — the worst possible condition — and the mean drops from 3.2 to 2.67.</td></tr>
      <tr><td>“Date fields only support date; they do not support time.”</td><td><code>last_inspection_at</code></td><td>Time dropped. Depending on whether the writer converts to UTC first, SL-0055’s night check is stored as 2026-08-<strong>30</strong> or 2026-08-<strong>29</strong>. Either way the +05:30 is gone.</td></tr>
      <tr><td>Text width 254; record 4,000 bytes; 255 fields</td><td>a long <code>inspector_note</code></td><td>Cut off. Not triggered by F7, but check on real data.</td></tr>
      <tr><td>Loss of “Subtypes, Attribute domains, Geometric networks, Topologies, Annotation”</td><td>(geodatabase sources — 7.5)</td><td>Rules vanish; codes stay (e.g. <code>2</code> instead of “Drain”) unless the export is told to write descriptions.</td></tr>
      <tr><td>2 GB per component file; one geometry type per file; curves densified</td><td>—</td><td>Big or mixed data must split or use another container.</td></tr>
    </tbody></table></div>
  <div class="callout warn"><span class="label">Why the green message is not a check</span><p>The exporter checks that it could <em>write bytes in the format’s structure</em>. It does not know that <code>0</code> meant “not assessed”, that <code>legacy_cod</code> used to be joined by a longer name, or that an empty note now reads as a single space. Format validity and meaning preservation are two different tests, and only you can run the second one (7.7).</p></div>

  <h2><span class="mod">7.3.4</span>A format you will meet, not a default you choose</h2>
  <p>You will receive shapefiles from contractors, government portals and older systems for years. Some recipients will accept nothing else. Treat the shapefile as an <strong>export chosen for a named recipient</strong>, never as a working or archive store. The question to answer is the recipient’s, not yours:</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Recipient says…</th><th>Shapefile justified?</th><th>What to do</th></tr></thead>
    <tbody>
      <tr><td>“Our old desktop tool only reads .shp”</td><td class="yes">Yes, as a delivered copy</td><td>Export from the master; document every rename and substitution; send all companion files plus a readme</td></tr>
      <tr><td>“We just need to see the points on a web map”</td><td class="no">Usually no</td><td>GeoJSON (if the data is WGS 84) or a service (Chapter 2)</td></tr>
      <tr><td>“We need the full schema — nulls, timestamps, the local-name field”</td><td class="no">No</td><td>GeoPackage or a geodatabase (7.4, 7.5)</td></tr>
      <tr><td>“Send whatever, we’ll figure it out”</td><td>Not an answer</td><td>Ask what they will do with it; choose from the answer</td></tr>
    </tbody></table></div>
  <div class="callout dev"><span class="label">Developer view</span><p>A shapefile is like a legacy fixed-width flat-file interface with 8.3-style names: everyone can read it, and every modern data type must be squashed to fit. <strong>Where the comparison breaks:</strong> no flat file falls apart when one of its five companion files is left behind, and most flat-file conventions at least allow an explicit null marker.</p></div>

  <div class="quiz" data-answer="1" data-fb="After export, Esri says ArcGIS cannot tell a substituted null from a real value. Only the original (pre-export) data, its intake record, or the inspection log can settle it — which is why 7.7 says never convert in place.">
    <div class="q">A <code>.dbf</code> shows <code>condition_</code> = 0 for TR-0301. Can you tell, from the shapefile alone, whether the tree scored 0 or was never assessed?</div>
    <div class="opts">
      <button class="opt">Yes — 0 always means the writer substituted a null.</button>
      <button class="opt">No. Only the original dataset, its intake record, or the inspection log can settle it.</button>
      <button class="opt">Yes — look at the .prj file.</button>
      <button class="opt">No, but re-exporting the shapefile will fix it.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  /* folder inspector */
  const FILES = [
    { fn: "assets_ch7.shp", role: "geometry only — 6 point records, no attributes", req: true, on: true },
    { fn: "assets_ch7.shx", role: "index: where each record starts inside .shp", req: true, on: true },
    { fn: "assets_ch7.dbf", role: "attribute table (dBASE): 10-char names, no true nulls", req: true, on: true },
    { fn: "assets_ch7.prj", role: "coordinate system text — optional, but without it CRS = unknown", req: false, on: true, key: "prj" },
    { fn: "assets_ch7.cpg", role: "code page of the .dbf text — optional; without it the encoding is a guess", req: false, on: true, key: "cpg" },
    { fn: "assets_ch7.sbn / .sbx", role: "spatial index — optional, harmless if missing", req: false, on: false },
    { fn: "assets_ch7.shp.xml", role: "ArcGIS metadata — optional", req: false, on: false }
  ];
  const folder = document.getElementById("folder");
  FILES.forEach((f, i) => { const l = document.createElement("label"); l.className = f.req ? "req" : ""; l.innerHTML = `<input type="checkbox" ${f.on ? "checked" : ""} data-i="${i}"><span class="fn">${f.fn}</span><span class="role">${f.role}</span>`; folder.appendChild(l); });
  function verdict() {
    const on = i => folder.querySelector(`input[data-i="${i}"]`).checked;
    folder.querySelectorAll("label").forEach((l, i) => l.classList.toggle("off", !on(i)));
    const v = document.getElementById("folderVerdict");
    if (!on(0)) v.innerHTML = "<strong>No .shp:</strong> there is no geometry. Nothing to open.";
    else if (!on(1) || !on(2)) v.innerHTML = `<strong>Missing a required file</strong> (${!on(1) ? ".shx" : ""}${!on(1) && !on(2) ? " and " : ""}${!on(2) ? ".dbf" : ""}). Esri lists .shp, .shx and .dbf as required; most software will refuse to open it (GDAL can rebuild a missing .shx only with a special option). Ask the sender for the full set — do not try to repair it.`;
    else v.innerHTML = `<strong>Opens.</strong> ${on(3) ? "CRS is defined by the .prj — read it, do not assume it is right." : "<span style='color:var(--warn)'>No .prj → coordinate system <em>unknown</em>.</span> Investigate (Chapter 5); write a .prj only once the CRS is established from evidence (Chapter 6)."} ${on(4) ? "Text encoding is declared by the .cpg." : "<span style='color:var(--warn)'>No .cpg → the text encoding is a guess</span>; accented or non-English characters may show as ? in some software."}`;
  }
  folder.addEventListener("change", verdict); verdict();
  /* export simulator */
  const sel = document.getElementById("recSel"); F7.rows.forEach((r, i) => { const o = document.createElement("option"); o.value = i; o.textContent = r.asset_id + (i === 2 ? " (the null case)" : i === 5 ? " (night timestamp)" : ""); sel.appendChild(o); }); sel.value = 2;
  function runShp() {
    const r = F7.rows[+sel.value];
    const res = convertRow(r, "shp", { tsTyped: document.getElementById("tsTyped").checked, utcFirst: document.getElementById("utcFirst").checked });
    const t = document.getElementById("shpDiff");
    t.innerHTML = `<thead><tr><th>field (before)</th><th>value before</th><th>field (after)</th><th>value after</th><th>status</th></tr></thead><tbody>` + res.fields.map(f => `<tr><td class="mono">${esc(f.name)}</td><td class="mono">${esc(f.before)}</td><td class="mono ${f.outName !== f.name ? "chg" : ""}">${esc(f.outName)}</td><td class="mono ${f.status === "lost" ? "bad" : f.status === "adapted" ? "chg" : ""}">${esc(f.after)}</td><td><span class="st ${f.status}">${STATUS_LABEL[f.status]}</span>${f.why ? `<div class="why">${esc(f.why)}</div>` : ""}</td></tr>`).join("") + `</tbody>`;
    const lost = res.fields.filter(f => f.status === "lost").length, ad = res.fields.filter(f => f.status === "adapted").length, dep = res.fields.filter(f => f.status === "depends").length;
    document.getElementById("shpSummary").innerHTML = `<strong>Tool message: “Completed.”</strong> Reality: ${ad} field(s) adapted, <span style="color:var(--warn)"><strong>${lost} lost</strong></span>, ${dep} depend on the reader. None of this raised an error.`;
  }
  ["recSel", "tsTyped", "utcFirst"].forEach(id => document.getElementById(id).addEventListener("change", runShp)); runShp();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
