<?php $page = ['title' => '7.8 Lab: select and test an exchange format', 'chapter' => 7, 'module' => '7.8']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 7.8 · Guided lab · ArcGIS Pro (primary) or QGIS · paper route if neither</div>
    <h1>Lab: choose a box for a real recipient, then prove what survived</h1>
    <p class="lead">Admit Table F7 through the intake form, choose an exchange format for a <em>named</em> recipient, convert, and produce a before/after comparison that separates adaptation from loss. Everyone also makes a shapefile as the “control”, so you see loss happen on your own machine.</p>
    <div class="outcomes"><h4>Deliverables (what your instructor marks)</h4>
      <ul><li>Intake form (14 fields) · format decision · before/after comparison with <strong>observed</strong> values · recipient readme + shapefile-control readme · software log · a statement that <code>00_original</code> is untouched.</li></ul></div>
  </div>

  <div class="callout warn"><span class="label">Read this first</span><p>The software steps below were written from the ArcGIS Pro 3.7 and QGIS 3.44 documentation and have <strong>not been run</strong> by the author. The “expected results” are predictions from the format rules. Your deliverable must show what <em>your</em> software actually did next to each prediction. Disagreement with a prediction is not a failure; an <em>unexplained</em> disagreement is. Your instructor should run the lab once before issuing it (chapter document, Instructor Appendix I.6).</p></div>

  <h2><span class="mod">7.8.1–7.8.2</span>Objective, prerequisites, software</h2>
  <ul>
    <li>Modules 7.1–7.7 read; Chapter 5’s CRS checklist and Chapter 6’s assign-versus-transform distinction understood; you can save a UTF-8 text file.</li>
    <li><strong>Primary route:</strong> ArcGIS Pro 3.x, any licence level, no sign-in, no credits. The project’s default file geodatabase is the working store.</li>
    <li><strong>Alternative:</strong> QGIS Desktop 3.44 (section 7.8.10). Optional for both: the <code>sqlite3</code> command or any SQLite browser to look inside the GeoPackage.</li>
    <li><strong>No software:</strong> paper route (7.8.8) — full marks for reasoning and documentation, not for verification.</li>
  </ul>

  <h2><span class="mod">7.8.3</span>Input data — create it exactly as printed (made-up data)</h2>
  <p>Make a folder <code>ch7_lab</code> with a subfolder <code>00_original</code>. After this step, treat <code>00_original</code> as read-only.</p>
  <div class="tabs"><button>assets_ch7.csv</button><button>README_assets_ch7.md</button><button>schema.ini (ArcGIS Pro)</button><button>assets_ch7.csvt (QGIS)</button></div>
  <div class="tabpanel"><p class="small">Save as <strong>UTF-8</strong>, comma-delimited. The leading-zero codes are quoted on purpose.</p><div class="copywrap"><pre id="csvPre"></pre></div></div>
  <div class="tabpanel"><p class="small">Copy the schema dictionary and your filled intake form from module 7.6 into it, and add this line:</p><div class="copywrap"><pre>Coordinates are on a local training grid in metres with no Earth reference.
Do not assign an Earth CRS. Any reference assigned for software convenience
must be recorded here as "assigned for this exercise only".</pre></div></div>
  <div class="tabpanel"><p class="small">ArcGIS Pro honours this Microsoft ODBC text-driver file to fix field types. Put it in the same folder as the CSV, named exactly <code>schema.ini</code>. <code>last_inspection_at</code> is declared Text on purpose so the string survives import unchanged; converting it to a real date/time becomes a <em>visible, chosen</em> step.</p><div class="copywrap"><pre>[assets_ch7.csv]
Format=CSVDelimited
ColNameHeader=True
Col1=asset_id Text
Col2=legacy_code Text
Col3=asset_type_en Text
Col4=asset_type_local Text
Col5=installed_year Long
Col6=condition_score Double
Col7=last_inspection_at Text
Col8=inspector_note Text
Col9=x Double
Col10=y Double</pre></div><p class="small"><strong>Instructor verification item:</strong> whether ArcGIS Pro reads this UTF-8 CSV correctly with this <code>schema.ini</code>, and whether a byte-order mark helps or hurts, must be checked in the installed version; record both outcomes.</p></div>
  <div class="tabpanel"><p class="small">Optional one-line sidecar that QGIS and GDAL honour for column types.</p><div class="copywrap"><pre>"String","String","String","String","Integer","Real","String","String","Real","Real"</pre></div></div>

  <h2><span class="mod">7.8.4</span>Choose a recipient</h2>
  <p>Your instructor assigns one, or you pick one and justify it. Click a card; the note shows what to ask them and what Table 7.4 predicts.</p>
  <div class="grid-3" id="recips"></div>
  <div class="result" id="recipNote">Pick a recipient.</div>
  <div class="lab-form"><label>Your format decision (recipient, format, rejected alternatives, expected adaptations/losses)</label><textarea data-save="decision"></textarea></div>

  <h2><span class="mod">7.8.5</span>Steps (ArcGIS Pro route — not execution-tested)</h2>
  <p>Tick each step as you do it. Ticks and notes are saved in this browser only.</p>
  <div class="lab-card"><h4>Part 1 — Intake (no software)</h4><div class="checklist">
    <label><input type="checkbox" data-save="p1a"><span><strong>1.</strong> Fill the intake form (module 7.6) for <code>assets_ch7.csv</code>. Every field; “unknown” where you must; never blank.</span></label>
    <label><input type="checkbox" data-save="p1b"><span><strong>2.</strong> Read the CSV in your text editor. Confirm: UTF-8 in the status bar; ten header names; six data rows; the TR-0301 row has three consecutive empty fields.</span></label>
  </div></div>
  <div class="lab-card"><h4>Part 2 — Load the source as ArcGIS Pro reads it</h4><div class="checklist">
    <label><input type="checkbox" data-save="p2a"><span><strong>3.</strong> New project <code>ch7_lab.aprx</code> in <code>ch7_lab</code>. Its default file geodatabase is created with it.</span></label>
    <label><input type="checkbox" data-save="p2b"><span><strong>4.</strong> <strong>Map ▸ Add Data</strong>, add <code>00_original/assets_ch7.csv</code>. It appears as a standalone table.</span></label>
    <label><input type="checkbox" data-save="p2c"><span><strong>5.</strong> Open the table and its fields view. Record in your “before” snapshot the <em>detected type</em> of every field and the displayed values of <code>legacy_code</code> for SL-0113 and <code>last_inspection_at</code> for SL-0055. Esri states tools read exactly what the table view shows — so this is your true “before”. If <code>legacy_code</code> shows <code>113</code>, the <code>schema.ini</code> was not honoured: check its name and folder, remove and re-add the table, record what fixed it.</span></label>
    <label><input type="checkbox" data-save="p2d"><span><strong>6.</strong> Analysis ▸ Tools ▸ <strong>XY Table To Point</strong>: Input Table = the CSV; Output = <code>assets_ch7_src</code> in the project geodatabase; X Field = <code>x</code>; Y Field = <code>y</code>; <strong>Coordinate System = the local/engineering reference your instructor designates</strong> for the training grid (record it as “assigned for this exercise only; not an Earth location”). Do <strong>not</strong> accept the tool’s default, which Esri documents as WGS 84 — that would relabel grid metres as degrees, Chapter 6’s relabelling error. <em>Instructor verification item: whether the parameter may be left empty for an unknown-CRS output, or a custom local coordinate system must be defined first.</em></span></label>
    <label><input type="checkbox" data-save="p2e"><span><strong>7.</strong> Add <code>assets_ch7_src</code> to the map; open its table; complete the “before” snapshot (module 7.7): count 6; Point; extent x 205–2190, y 195–950; nulls per field; the three known records copied verbatim. This feature class is the working source for every export. <code>00_original</code> stays untouched.</span></label>
  </div></div>
  <div class="lab-card"><h4>Part 3 — Convert for your recipient</h4><div class="checklist">
    <label><input type="checkbox" data-save="p3a"><span><strong>8. R-C (GeoPackage):</strong> run <strong>Create SQLite Database</strong> with Spatial Type = <em>GeoPackage 1.4</em> (or the latest offered) to make <code>10_outputs/assets_ch7_R-C.gpkg</code>. Then <strong>Export Features</strong>: input <code>assets_ch7_src</code>, output inside the <code>.gpkg</code>, name <code>assets_ch7</code>; leave the field map unchanged. <em>Instructor verification item: confirm Export Features accepts a .gpkg output location in the installed version; if another tool is needed, the instructor pack names it.</em></span></label>
    <label><input type="checkbox" data-save="p3b"><span><strong>9. R-B (GeoJSON):</strong> run <strong>Features To JSON</strong>: input <code>assets_ch7_src</code>; Output JSON = <code>10_outputs/assets_ch7_R-B_gridJSON_nonstandard.geojson</code>; <em>Output to GeoJSON</em> ticked; <em>Project to WGS84</em> <strong>unticked</strong> (there is no Earth reference to project from). Open the file: expect a <code>crs</code> member that Esri says “is not fully supported under the GeoJSON specification”. Your R-B readme must say: GeoJSON <em>syntax</em> under a <strong>prior arrangement</strong> (RFC 7946 §4); coordinates are grid metres; not to be passed to anyone else.</span></label>
    <label><input type="checkbox" data-save="p3c"><span><strong>10. R-A (shapefile) — everyone does this control:</strong> <strong>Export Features</strong> with output <code>10_outputs/assets_ch7_R-A_shp/assets_ch7.shp</code> (a folder path with a <code>.shp</code> name produces a shapefile).</span></label>
  </div></div>
  <div class="lab-card"><h4>Part 4 — “After” snapshot and comparison</h4><div class="checklist">
    <label><input type="checkbox" data-save="p4a"><span><strong>11.</strong> Add each output to the map. For each, fill an “after” column beside your “before”: field names and types, count, geometry type, CRS as reported, extent, nulls per field, the two free-text samples, the identifier sample, the date/time samples, the three known records.</span></label>
    <label><input type="checkbox" data-save="p4b"><span><strong>12.</strong> For the GeoPackage, if you can, open it outside ArcGIS: <code>sqlite3 assets_ch7_R-C.gpkg</code> then <code>SELECT table_name, data_type, srs_id FROM gpkg_contents;</code> and <code>SELECT srs_id, organization, organization_coordsys_id FROM gpkg_spatial_ref_sys;</code>. Record the <code>srs_id</code> your export used.</span></label>
    <label><input type="checkbox" data-save="p4c"><span><strong>13.</strong> In <code>assets_ch7_R-A_shp</code>, list the companion files. Was a <code>.prj</code> written? A <code>.cpg</code>? What does it contain?</span></label>
    <label><input type="checkbox" data-save="p4d"><span><strong>14.</strong> Classify every difference as <strong>intentional adaptation</strong> or <strong>accidental loss</strong> (module 7.7). For each loss, fix it (re-export with a different option, repeat 11–14) or declare it in the recipient readme with a warning.</span></label>
  </div></div>
  <div class="lab-card"><h4>Part 5 — Package</h4><div class="checklist">
    <label><input type="checkbox" data-save="p5a"><span><strong>15.</strong> Write <code>10_outputs/README_&lt;recipient&gt;.md</code>: recipient, format, field-name mapping if any names changed, every adaptation, every declared loss, the CRS statement, dates, your name. Confirm <code>00_original</code> is unchanged (same sizes; open the CSV once more).</span></label>
  </div></div>
  <div class="lab-form"><label>Your lab notes (saved in this browser)</label><textarea data-save="labnotes" placeholder="Software and version · the coordinate reference assigned at step 6 and why · tool names · anything that differed from the printed steps"></textarea></div>

  <h2><span class="mod">7.8.6</span>Expected results (predictions from the rules — write your observed value next to each)</h2>
  <div class="controls"><label><input type="checkbox" id="expTyped"> timestamp typed as date/time before export</label></div>
  <div class="table-wrap"><table class="diff" id="expTable"></table></div>
  <div class="card"><h4 style="margin-top:0">Validation checks — all must pass before you package</h4>
    <div class="checklist">
      <label><input type="checkbox" data-save="v1"><span><code>00_original/assets_ch7.csv</code> is byte-identical to what you typed.</span></label>
      <label><input type="checkbox" data-save="v2"><span>Every “after” cell has an observed value; none says “probably”.</span></label>
      <label><input type="checkbox" data-save="v3"><span>Every difference is in exactly one column, with a one-line justification.</span></label>
      <label><input type="checkbox" data-save="v4"><span>The recipient readme names every field whose name changed, old → new.</span></label>
      <label><input type="checkbox" data-save="v5"><span>The R-B readme contains the words “prior arrangement” and “not standard RFC 7946”.</span></label>
    </div></div>

  <h2><span class="mod">7.8.7</span>Troubleshooting</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Symptom</th><th>Likely cause</th><th>What to do</th></tr></thead>
    <tbody>
      <tr><td><code>legacy_code</code> shows <code>113</code></td><td>Typed as a number by the importer</td><td>Check <code>schema.ini</code> name and folder; values quoted; re-add the table</td></tr>
      <tr><td><code>condition_score</code> shows a text type</td><td>Empty value or decimal symbol confused detection</td><td><code>Col6=condition_score Double</code>; check your system’s decimal symbol</td></tr>
      <tr><td>XY Table To Point warns about coordinates</td><td>Coordinate System left at WGS 84; values outside ±400 are treated as invalid</td><td>Set the designated local reference; never “fix” the numbers</td></tr>
      <tr><td>Points draw somewhere on Earth over a basemap</td><td>WGS 84 was accepted — grid metres relabelled as degrees</td><td>Delete the output; rerun step 6; record the mistake in your log as a Chapter 6 relabelling error</td></tr>
      <tr><td>Export into the <code>.gpkg</code> fails or a <code>.sqlite</code> appears</td><td>Database created with Spatial Type ST_Geometry / SpatiaLite instead of GeoPackage</td><td>Recreate with GeoPackage 1.4; the extension must be <code>.gpkg</code></td></tr>
      <tr><td>Shapefile export warns about field names</td><td>Expected: names longer than 10 characters</td><td>Record the mapping the tool chose; do not rename source fields to “avoid” the warning</td></tr>
      <tr><td>GeoJSON has no <code>crs</code> and coordinates look like degrees</td><td><em>Project to WGS84</em> was ticked</td><td>Untick it; the grid cannot be placed on Earth</td></tr>
    </tbody></table></div>

  <h2><span class="mod">7.8.8–7.8.9</span>Paper route and deliverables</h2>
  <p><strong>Paper route.</strong> Do Part 1, the format decision, and the <em>predicted</em> “after” column of 7.8.6 for your recipient and for the shapefile control, citing the rule for each prediction. Write the recipient readme as if the predictions were observed, marking each line “predicted, not observed”. Full marks for reasoning and documentation; verification marks need a machine later.</p>
  <p><strong>Deliverables.</strong> (1) intake form; (2) format decision; (3) before/after comparison with predictions and observed values, and the adaptation/loss classification; (4) recipient readme plus shapefile-control readme; (5) software log — product and version, tool names, the coordinate reference assigned at step 6 and why, any deviation; (6) a statement that <code>00_original</code> is unchanged. A map screenshot is not a deliverable; typed values are.</p>

  <h2><span class="mod">7.8.10</span>QGIS alternative (equivalent exercise; not execution-tested; QGIS 3.44 manual and GDAL driver pages)</h2>
  <div class="lab-card"><div class="checklist">
    <label><input type="checkbox" data-save="q1"><span><strong>1. Project setting.</strong> Project ▸ Properties ▸ CRS: choose <strong>No CRS (or unknown/non-Earth projection)</strong>. The manual: this makes “all layers and map coordinates to be treated as simple 2D Cartesian coordinates, with no relation to positions on the Earth’s surface” — the honest setting for the training grid. Record it.</span></label>
    <label><input type="checkbox" data-save="q2"><span><strong>2. Load the CSV.</strong> Data Source Manager ▸ <strong>Delimited Text</strong>; file <code>assets_ch7.csv</code>; File format = CSV; <em>First record has field names</em> on; <em>Detect field types</em> on; check the sample preview and <strong>change <code>legacy_code</code> to Text if it was detected as a whole number</strong>; Geometry = Point coordinates, X = <code>x</code>, Y = <code>y</code>; Geometry CRS: follow the instructor’s designation (<em>verification item: what the CRS widget offers for a non-Earth layer in 3.44</em>); Add. If you shipped the <code>.csvt</code>, note whether the detected types match it.</span></label>
    <label><input type="checkbox" data-save="q3"><span><strong>3. “Before” snapshot.</strong> Layer Properties ▸ Fields (types), Information (count, extent, CRS, encoding); attribute table for the three known records.</span></label>
    <label><input type="checkbox" data-save="q4"><span><strong>4. Convert.</strong> Right-click the layer ▸ <strong>Export ▸ Save Features As…</strong>. <strong>R-C:</strong> Format GeoPackage, file <code>assets_ch7_R-C.gpkg</code>, layer name <code>assets_ch7</code>, Encoding UTF-8, tick <em>Persist layer metadata</em>. <strong>R-B:</strong> Format GeoJSON; in Layer Options leave <code>RFC7946</code> at its default NO (YES would try to reproject to WGS 84); label the output non-standard exactly as in the Pro route. <strong>R-A (control):</strong> Format ESRI Shapefile, Encoding UTF-8 (GDAL writes the <code>.cpg</code>); observe the field-name renaming — record the mapping the tool chose.</span></label>
    <label><input type="checkbox" data-save="q5"><span><strong>5. “After” snapshot and comparison</strong> exactly as in the Pro route, including the <code>sqlite3</code> queries and the companion-file listing.</span></label>
    <label><input type="checkbox" data-save="q6"><span><strong>6. Expect differences from ArcGIS Pro:</strong> GDAL’s duplicate-name rule (cut to 8 characters plus a number) may give different shapefile names; GDAL’s null handling on shapefile write is not stated on its page — observe it; the GeoPackage <code>DATETIME</code> conversion applies only if the field was typed as date/time before export. None of these is a defect; all must be recorded.</span></label>
  </div></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  document.getElementById("csvPre").textContent = F7.csvText();
  const R = [
    { id: "R-A", who: "R-A — contractor", say: "“Our old desktop tool only reads shapefiles.”", need: "Points, IDs and types; they will not use timestamps or the local-name field.", note: "Shapefile is justified <em>as a delivered copy</em>. Predict: 7 names shortened (document the mapping), null score → 0 (declare it or agree a sentinel), time dropped. Send all companion files + readme." },
    { id: "R-B", who: "R-B — web developer", say: "“Give me GeoJSON for the prototype.”", need: "Draw six points on a schematic grid in a browser, no basemap; wants the friendlier local-name labels for display.", note: "GeoJSON <em>syntax</em> only under a written <strong>prior arrangement</strong> (RFC 7946 §4): the coordinates are grid metres, not degrees, so the file is not standard GeoJSON and must not be handed on. Nulls, long names and the ISO timestamp string all survive." },
    { id: "R-C", who: "R-C — internal GIS team", say: "“One file for the field tablet with everything intact.”", need: "Full fidelity: nulls, timestamps with their instant, the local-name field, long names; single-user edits.", note: "GeoPackage (or a file/mobile geodatabase). Predict: everything kept; the timestamp becomes UTC <code>Z</code> if typed as date/time (an adaptation to document — SL-0055’s date changes); the CRS registers as an undefined Cartesian grid (<code>srs_id −1</code>) or your assigned local reference — record which." }
  ];
  const rc = document.getElementById("recips");
  R.forEach((r, i) => { const d = document.createElement("div"); d.className = "recip"; d.innerHTML = `<div class="who">${r.who}</div><div class="say">${r.say}</div><p class="small">Real need: ${r.need}</p>`; d.addEventListener("click", () => { rc.querySelectorAll(".recip").forEach(x => x.classList.remove("on")); d.classList.add("on"); document.getElementById("recipNote").innerHTML = r.note; try { localStorage.setItem("gis-ch7-recip", r.id); } catch {} }); rc.appendChild(d); });
  try { const s = localStorage.getItem("gis-ch7-recip"); const i = R.findIndex(r => r.id === s); if (i >= 0) rc.children[i].click(); } catch {}
  /* expected results table from the engine */
  function exp() {
    const typed = document.getElementById("expTyped").checked;
    const fmts = ["gpkg", "geojson", "shp"];
    const heads = ["GeoPackage (R-C)", "GeoJSON-syntax (R-B)", "Shapefile (R-A control)"];
    const checks = [
      { p: "Record count", v: () => ["6", "6", "6"] },
      { p: "Field names", v: () => ["unchanged", "unchanged", "≤ 10 characters; the two asset_type_* names disambiguated — record the exact names"] },
      { p: "legacy_code for SL-0113", v: () => fmts.map(f => convertRow(F7.rows[0], f, { tsTyped: typed }).fields[1].after) },
      { p: "condition_score for TR-0301", v: () => fmts.map(f => convertRow(F7.rows[2], f, { tsTyped: typed }).fields[5].after) },
      { p: "last_inspection_at for SL-0055", v: () => fmts.map(f => String(convertRow(F7.rows[5], f, { tsTyped: typed, utcFirst: false }).fields[6].after) + (f === "shp" && typed ? " (or 2026-08-29 if UTC first)" : "")) },
      { p: "inspector_note for TR-0301", v: () => ["'' or NULL — record which", "\"\" or null", "one space"] },
      { p: "CRS reported", v: () => ["assigned reference in gpkg_spatial_ref_sys, or srs_id −1", "a non-standard crs member — labelled", ".prj with the assigned reference, or none (unknown) — record which"] },
      { p: "Extent", v: () => ["x 205–2190, y 195–950", "same numbers", "same numbers"] },
      { p: "Mean condition_score (non-null)", v: () => ["3.2", "3.2", "if nulls became 0 and you average all six: ≈ 2.67 — the visible symptom"] }
    ];
    document.getElementById("expTable").innerHTML = `<thead><tr><th>Property</th>${heads.map(h => `<th>${h}</th>`).join("")}<th>Observed (yours)</th></tr></thead><tbody>` + checks.map(c => { const v = c.v(); return `<tr><td>${c.p}</td>${v.map(x => `<td class="mono">${esc(x)}</td>`).join("")}<td><input type="text" data-save="obs-${c.p.replace(/\W+/g, "_")}" style="width:100%;font:inherit;font-size:.85rem;padding:.2rem .4rem;border:1px solid var(--rule);border-radius:6px"></td></tr>`; }).join("") + `</tbody>`;
    initNotes();
  }
  document.getElementById("expTyped").addEventListener("change", exp); exp();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
