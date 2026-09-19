<?php $page = ['title' => '7.10 Recap, media brief and what comes next', 'chapter' => 7, 'module' => '7.10']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 7.10 · Recap, media and transition</div>
    <h1>What to carry forward</h1>
    <p class="lead">Eight ideas, one record you can now follow through any format, and a habit — snapshot, convert, compare — that Chapter 8 builds on.</p>
  </div>

  <h2><span class="mod">Recap</span>Eight things to remember</h2>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">1. Four layers</h4><p>Model, encoding, container, service. Name the layer before you debug. An extension is a claim — not proof of accuracy, completeness, freshness or fitness.</p></div>
    <div class="card"><h4 style="margin-top:0">2. A CSV says nothing about itself</h4><p>Columns, order, CRS, units, delimiter, encoding, types, nulls — all beside the file, all verified in the importer, which otherwise guesses. Leading zeros and timestamps go first.</p></div>
    <div class="card"><h4 style="margin-top:0">3. GeoJSON has one CRS</h4><p>WGS 84, longitude first. A <code>crs</code> member changes nothing. Non-WGS 84 exchange needs a written prior arrangement. Read a small file by eye first.</p></div>
    <div class="card"><h4 style="margin-top:0">4. Shapefile = five roles</h4><p><code>.prj</code> is optional and its absence means <em>unknown</em>. The <code>.dbf</code> shortens names, substitutes nulls, drops times, depends on a code page — and the export still says “completed”.</p></div>
    <div class="card"><h4 style="margin-top:0">5. GeoPackage and GeoTIFF</h4><p>SQLite with bookkeeping tables and optional extensions (check each per product); an honest slot for a flat grid; timestamps as UTC. A TIFF with georeferencing tags — still inspect CRS, bands, cell size, value units, NoData, data type, cell reference.</p></div>
    <div class="card"><h4 style="margin-top:0">6. Geodatabase = storage + model</h4><p>Feature classes, tables, feature datasets, behaviour. PostGIS gives spatial types; the system tables make an enterprise geodatabase; Data Store is ArcGIS-managed storage reached only through web layers.</p></div>
    <div class="card"><h4 style="margin-top:0">7. Every dataset has a story</h4><p>Intake form, all 14 fields. Measured / digitized / geocoded / derived / simulated. Suitability = content date, coverage, class, accuracy, licence — never “who published it”.</p></div>
    <div class="card"><h4 style="margin-top:0">8. Snapshot, convert, compare</h4><p>Known records, nulls, samples — not just counts. Every difference is an adaptation (decided, documented) or a loss (fix or declare). Keep the original; name outputs for content; the green message is structural, never semantic.</p></div>
  </div>

  <h2><span class="mod">Media brief</span>What the slides, audio and diagrams should show</h2>
  <p>A summary for whoever builds the presentation and the audio lesson; the full slide-by-slide outline, narration script, diagram specifications and the interactive spec are in the Media Appendix of the chapter document.</p>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">D1 — Four-layer stack</h4><p>Four bands with example chips; one thread “Table F7” passing through one chip per band. Side note: “extension = a claim about the encoding band only”. (You used it live in 7.1.)</p></div>
    <div class="card"><h4 style="margin-top:0">D2 — Package inspection</h4><p>Left: the shapefile folder with each file wired to a role card; red “if missing → unknown” tags on <code>.prj</code> and <code>.cpg</code>. Right: a database browser on the GeoPackage showing the <code>gpkg_*</code> tables with rows 4326, −1, 0. Caption: “Inspect the package, not the icon.” (7.3 and 7.4.)</p></div>
    <div class="card"><h4 style="margin-top:0">D3 — Before/after record comparison (the blueprint’s preferred visual)</h4><p>SL-0113, TR-0301 and SL-0055 before and after a shapefile export; changed cells outlined amber (adaptation) or red (loss); toggles for code page and “UTC first”. Values labelled “predicted from format rules; replace with observed values after the instructor’s run”. (7.3 and 7.7.)</p></div>
    <div class="card"><h4 style="margin-top:0">D4 — Old versus current boundary</h4><p>The training-grid canvas; current line at x = 1000 solid, 2019 line at x = 1300 dashed; P3 and P5 with arrows; the two count tables. Caption: “Synthetic training grid; no real boundary.” (7.6.)</p></div>
  </div>
  <div class="callout note"><span class="label">Narration rules for the audio lesson</span><p>Say “longitude then latitude” aloud every time GeoJSON coordinates are read; never say “the first number” without naming the axis. Read <code>0113</code> as “zero one one three, four characters”. Name the five shapefile roles, not the extensions. Pause before each answer: “Did the tool report an error?” — “No.” No 3D scene: nothing here is spatial in a way a 3D view would clarify; the before/after comparison teaches better.</p></div>

  <h2><span class="mod">Sources</span>Where the facts came from</h2>
  <p>Primary readings for this chapter: RFC 7946 (GeoJSON); the OGC GeoPackage site and the 1.4.0 Encoding Standard text; Esri’s <em>Geoprocessing considerations for shapefile output</em>; Esri’s geodatabase overview and <em>Types of geodatabases</em>; the QGIS <em>Gentle Introduction</em> page on raster data; the OGC GeoTIFF 1.1 standard; the GDAL shapefile, CSV, GeoJSON and GTiff driver pages; Esri’s pages on delimited files, XY Table To Point, Features To JSON, JSON To Features, SQLite/GeoPackage, Create SQLite Database, Export Features, field types and metadata; Esri’s PostgreSQL geodatabase, spatial-types, Enable Enterprise Geodatabase, Data Store and data-storage pages; ArcGIS Online’s CSV reference; the QGIS 3.44 user guide pages on delimited text, Save Features As, metadata and projections. Product behaviour was read on 19 September 2026 (ArcGIS Pro 3.7 documentation; QGIS 3.44). The full reference list with links is in the chapter document.</p>

  <h2><span class="mod">Next</span>Chapter 8 — data modelling and relationships</h2>
  <p>You can now tell what a box <em>can</em> hold and check what it <em>did</em> hold. Chapter 8 designs what <em>should</em> go inside: entities, identifiers, attribute types, allowed values, and the relationships between assets, inspections and requests — the information model that Table F7’s flat row only hints at. Its inspection history replaces F7’s single <code>condition_score</code> with one row per visit.</p>
  <p><a class="btn primary" href="./">← Chapter home</a> &nbsp; <a class="btn ghost" href="glossary">Glossary</a></p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<?php require __DIR__ . '/../partials/end.php'; ?>
