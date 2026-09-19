<?php $page = ['title' => 'Chapter 7 glossary', 'chapter' => 7]; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Chapter 7</div>
    <h1>Glossary</h1>
    <p class="lead">Terms as used in this chapter, in plain words. Tags: <span class="pill">Esri</span> <span class="pill">QGIS/GDAL</span> <span class="pill">Standard</span> — untagged terms are general ideas.</p>
  </div>
  <input class="gloss-search" id="q" type="search" placeholder="Type to filter… e.g. shapefile, null, GeoPackage, intake" aria-label="Filter glossary">
  <dl class="gloss" id="gloss"></dl>
  <p style="margin-top:2rem"><a class="btn ghost" href="./">← Chapter home</a></p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const E = ' <span class="pill">Esri</span>', Q = ' <span class="pill">QGIS/GDAL</span>', S = ' <span class="pill">Standard</span>';
  const terms = [
    ["Data model", "The way the world is represented: vector (features with shapes and attributes) or raster (a grid of cells)."],
    ["Encoding", "How one dataset is written down as bytes or text — CSV, GeoJSON, the .shp record layout, TIFF tags."],
    ["Container", "A file, folder or database that holds one or more datasets with shared bookkeeping: GeoPackage, file geodatabase, enterprise geodatabase, a folder of shapefile files (weakly)."],
    ["Service", "A network endpoint through which other computers request data or map images under the provider’s rules (Chapter 2)."],
    ["Coordinate CSV", "A delimited text table with coordinate columns. It carries no CRS, order, types or null rule inside the file; all of that must travel beside it."],
    ["schema.ini", "A Microsoft ODBC text-driver file that ArcGIS Pro honours to declare a delimited file’s field types and format (for example Col2=legacy_code Text)." + E],
    [".csvt", "A one-line sidecar declaring CSV column types, read by GDAL and QGIS." + Q],
    ["Leading zero", "A zero at the start of a code (0113, STD code 079). Lost when an importer decides the column is a number. Prevent it by declaring the type."],
    ["GeoJSON", "A JSON text format for geographic data defined by RFC 7946. Positions are longitude, latitude in WGS 84 decimal degrees; an optional third value is height in metres above the WGS 84 ellipsoid." + S],
    ["Geometry / Feature / FeatureCollection", "The three GeoJSON object kinds: a shape; a shape with properties; a list of features." + S],
    ["Linear ring", "A closed LineString of four or more positions bounding a polygon area. In GeoJSON exterior rings run counterclockwise and holes clockwise; shapefiles use the opposite." + S],
    ["Prior arrangement", "RFC 7946’s condition under which the parties involved may exchange non-WGS 84 coordinates in GeoJSON syntax. Makes the file usable between them, not standard for anyone else." + S],
    ["Foreign member", "A JSON member not defined by RFC 7946 (such as a collection-level name) that is nevertheless allowed in a GeoJSON document." + S],
    ["Shapefile", "Esri’s multi-file vector format: .shp geometry, .shx index, .dbf attributes (all required), optional .prj (CRS), .cpg (code page), index and metadata files."],
    ["dBASE (.dbf)", "The 1980s table format holding shapefile attributes; the source of the 10-character names, no-null, date-only and code-page limits."],
    ["Code page / .cpg", "The character encoding declared for a .dbf’s text, read from the .cpg file or the dBASE header. Without a Unicode code page honoured by the reader, Gujarati becomes ? or boxes."],
    ["Null substitution", "A shapefile writer’s replacement of a null with 0, a single space, or a zero date — with no error. Afterwards a real value and a substituted null look the same."],
    ["GeoPackage", "An OGC standard container: an SQLite 3 database with gpkg_* bookkeeping tables holding features, tiles and attribute tables. Current version 1.4.0." + S],
    ["gpkg_contents / gpkg_spatial_ref_sys / gpkg_extensions", "The GeoPackage tables listing datasets, coordinate reference systems (always including 4326, −1 undefined Cartesian, 0 undefined geographic) and any extensions in use." + S],
    ["Extended GeoPackage", "A GeoPackage that uses one or more registered extensions (spatial index, related tables, curved geometry, metadata…). Support for each extension must be checked per product." + S],
    ["DATETIME (GeoPackage)", "ISO 8601 date-time in UTC with a Z suffix. A local time with +05:30 is converted — same instant, different clock, possibly a different calendar date." + S],
    ["GeoTIFF", "A TIFF 6.0 image carrying georeferencing and CRS tags (ModelTiepoint, ModelPixelScale, ModelTransformation, GeoKeyDirectory). An ordinary image viewer ignores the tags." + S],
    ["World file (.tfw)", "A small text sidecar giving a raster’s placement when the image itself carries none; lost if the .tif is copied alone." + Q],
    ["NoData tag", "GDAL’s non-standard TIFF tag (42113) storing a band’s NoData value; not part of the OGC GeoTIFF standard." + Q],
    ["Geodatabase", "Esri’s physical store plus information model for geographic datasets: not just tables with geometry, but rules, relationships and behaviour." + E],
    ["File / mobile / enterprise geodatabase", "Geodatabases stored, respectively, in a .gdb folder, a .geodatabase SQLite file, and a relational database (Oracle, SQL Server, Db2, PostgreSQL, SAP HANA)." + E],
    ["Feature class", "A collection of features with the same geometry type, the same attributes and the same spatial reference." + E],
    ["Table (geodatabase)", "A non-spatial dataset of rows and columns — inspections, teams — that can be related to a feature class." + E],
    ["Feature dataset", "A group of related feature classes that share a coordinate system, used to build topologies and networks." + E],
    ["Geodatabase system tables", "Tables created by Enable Enterprise Geodatabase; their presence is what makes a database a geodatabase in ArcGIS." + E],
    ["ST_Geometry", "Esri’s own spatial type for databases; one of three ArcGIS supports in PostgreSQL (with PostGIS geometry and PostGIS geography)." + E],
    ["PostGIS", "Open-source extension adding spatial storage, indexing and functions to PostgreSQL. Not, by itself, an enterprise geodatabase."],
    ["ArcGIS Data Store", "The Enterprise application that creates ArcGIS-managed system storage (relational, object, spatiotemporal, graph stores) for hosted layers, reached only through web layers." + E],
    ["System storage / user storage", "Enterprise’s distinction between ArcGIS-managed stores and the databases, folders and cloud stores you bring and can reach with your own tools." + E],
    ["Intake form", "This course’s 14-field record admitting a dataset into a project: publisher, URL, retrieval date, edition, coverage, CRS, units, accuracy, licence, omissions, data class, processing history, encoding/types, checks performed."],
    ["Measured / digitized / geocoded / derived / simulated", "The five data classes describing how geometry and values came to exist, each with its own evidence to demand."],
    ["Processing history", "The reproducible record of inputs, operation, parameters, software, date and person for a derived dataset. Your Chapter 6 lab log is one."],
    ["Before/after snapshot", "The property list recorded before and after a conversion: schema, count, geometry type, CRS, extent, nulls, Unicode and identifier samples, date/time samples, a hand statistic, three known records."],
    ["Intentional adaptation", "A conversion change you decided on because the format or recipient requires it, and can describe and justify (UTC timestamps, shortened names with a mapping)."],
    ["Accidental loss", "A conversion change you did not decide on and cannot justify to the recipient (null → 0, garbled text, lost zeros, a date moved a day without notice)."],
    ["Item description / metadata style", "ArcGIS Pro’s default one-page metadata view, and the fuller styles a metadata specialist can select." + E],
    [".qmd", "QGIS’s sidecar metadata file, used for formats without internal metadata storage." + Q],
    ["Table F7", "This chapter’s made-up practice data: six assets on the flat training grid with a leading-zero code, a null score, +05:30 timestamps and Gujarati text. Not a real place."]
  ];
  const dl = document.getElementById("gloss");
  const render = f => { dl.innerHTML = terms.filter(t => !f || (t[0] + " " + t[1]).toLowerCase().includes(f)).map(t => `<dt>${t[0]}</dt><dd>${t[1]}</dd>`).join("") || "<dd>No matching term.</dd>"; };
  document.getElementById("q").addEventListener("input", e => render(e.target.value.trim().toLowerCase()));
  render("");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
