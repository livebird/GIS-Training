<?php $page = ['title' => 'Chapter 3 glossary', 'chapter' => 3]; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Chapter 3</div>
    <h1>Glossary</h1>
    <p class="lead">Terms as used in this chapter, in plain words. Tags: <span class="pill">Esri</span> <span class="pill">QGIS</span> <span class="pill">format</span> — untagged terms are general ideas.</p>
  </div>
  <input class="gloss-search" id="q" type="search" placeholder="Type to filter… e.g. vertex, hole, extent, ObjectID" aria-label="Filter glossary">
  <dl class="gloss" id="gloss"></dl>
  <p style="margin-top:2rem"><a class="btn ghost" href="./">← Chapter home</a></p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const E = ' <span class="pill">Esri</span>', Q = ' <span class="pill">QGIS</span>', F = ' <span class="pill">format</span>';
  const terms = [
    ["Asset", "A thing the municipality owns and looks after (streetlight, drain, tree), stored as one feature with a current state."],
    ["Attribute", "One fact about a feature, kept in a column: status, priority, installed year."],
    ["Attribute table", "All the rows of one dataset, one row per feature — a spreadsheet with a hidden shape column."],
    ["Basemap", "Reference layers (streets, imagery) drawn beneath your working data for context. Usually someone else’s data; not yours to edit."],
    ["Business identifier", "An ID the office’s process assigns and owns (request_id = P3). Stable across copies and exports."],
    ["Dataset", "The stored collection of features: shapes + facts, one shape type, one set of columns. A file, a feature class, a database table."],
    ["Definition query" + E, "A layer setting in ArcGIS Pro that limits which rows the layer draws, lists, selects and passes to tools — without changing the dataset."],
    ["Provider feature filter" + Q, "The QGIS equivalent of a definition query (Layer ▸ Filter…, the Query Builder)."],
    ["Drawing order", "The order in which layers are painted: bottom of the list first, top last. Upper layers cover lower ones."],
    ["Empty (null) geometry", "A row that has facts but no shape. Counted in the table; ignored by every spatial operation. Complaint P7."],
    ["Extent (bounding box, envelope)", "The smallest upright rectangle containing a shape or a whole dataset: x-min, y-min, x-max, y-max. A summary, not the outline."],
    ["Exported copy", "A new, separate dataset written from a layer or selection. Later edits to the source and the copy do not reach each other."],
    ["Feature", "One stored thing: one shape (possibly empty, possibly in several parts) plus its facts, kept as one row. Includes invisible areas such as wards."],
    ["Feature class" + E, "Esri’s word for a dataset of features with one shape type and one set of columns."],
    ["Field", "A column in a table, holding one fact for every row."],
    ["Field alias" + E, "A friendlier display name for a column heading. Not a value, not an ID."],
    ["GeoJSON" + F, "A JSON text format (RFC 7946) for shapes, features and collections. Positions are x-like then y-like; outer rings counter-clockwise, holes clockwise; the coordinate system is fixed to WGS 84 degrees."],
    ["Esri JSON" + F, "The JSON form used by ArcGIS services: rings arrays with outer rings clockwise, holes counter-clockwise; paths arrays for lines; optional z and m."],
    ["WKT (Well-Known Text)" + F, "The OGC text form of a shape: POINT, LINESTRING, POLYGON, MULTIPOLYGON… No coordinate-system statement in the text itself."],
    ["Geometry", "The shape part of a feature: a point, a line or a polygon built from vertices."],
    ["Geometry field", "The special column that holds the shape. Called SHAPE in ArcGIS; a geometry column in PostGIS."],
    ["Hole (interior ring)", "A ring inside a polygon’s outer ring that cuts area out. A point in the hole is not inside the polygon. The depot courtyard."],
    ["Inspection", "A record of one visit to one asset on one date. Its own row, linked to the asset by asset_id; no shape of its own."],
    ["Label", "Text drawn beside a feature from a column or expression. A presentation setting, not data."],
    ["Layer", "A use of a dataset in a map: a pointer to the dataset plus colours, labels, a filter, visibility. It holds no copy of the data."],
    ["Line (polyline)", "A shape of two or more vertices joined by segments. Has length, no area. Roads, pipes, paths."],
    ["M (measure)", "An optional per-vertex value along a line that is not a spatial axis — distance from the start, time. Not supported by GeoJSON."],
    ["Map", "An ordered stack of layers sharing one view, stored in a project or a web map."],
    ["Multipart feature", "One row whose shape has several separate pieces (MultiPoint, MultiLineString, MultiPolygon). The two-piece park PK-01."],
    ["ObjectID" + E, "A whole-number system ID that ArcGIS assigns and owns for each row of one table. Cannot be empty or edited; a copied table gets new ones."],
    ["GlobalID" + E, "A 36-character ID a geodatabase assigns automatically to each row."],
    ["Operational layer", "A layer of the data you are actually working on, as opposed to the basemap."],
    ["Point", "A shape of a single vertex."],
    ["Polygon", "A shape of one outer ring and any number of inner rings (holes), enclosing an area."],
    ["Record (row)", "One feature’s stored values."],
    ["Representation", "The shape and facts chosen to stand for a real thing, for a stated purpose and scale. A school as a dot or as an outline."],
    ["Ring (linear ring)", "A closed vertex list (first = last) that bounds an area when stored as part of a polygon."],
    ["Scale", "Map distance : ground distance. 1 : 50,000 means 1 mm on the map is 50 m on the ground."],
    ["Segment", "The straight piece of a line or ring between two neighbouring vertices."],
    ["Selection", "A marked subset of rows shown in both the map and the table without changing them."],
    ["Symbol / symbology", "How features are drawn — colour, size, stroke thickness. A layer setting that does not change data."],
    ["System identifier", "An ID the software assigns and owns (ObjectID, GlobalID). Scoped to one table."],
    ["Vector data", "Data that represents things as points, lines and polygons built from coordinates. (Chapter 4 introduces the other kind: rasters.)"],
    ["Vertex", "One stored position — (x, y), optionally with z and m. Plural: vertices."],
    ["View (filtered view)", "A layer setting (or a hosted feature layer view, or a SQL view) that shows a subset of a dataset live, without copying it."],
    ["X, Y, Z", "The two flat-grid coordinates of a vertex and an optional third (height). What they mean is fixed by a coordinate reference system — Chapter 5."]
  ];
  const dl = document.getElementById("gloss");
  function render(f) { const q = (f || "").toLowerCase(); dl.innerHTML = terms.filter(t => !q || t[0].toLowerCase().includes(q) || t[1].toLowerCase().includes(q)).map(t => `<dt>${t[0]}</dt><dd>${t[1]}</dd>`).join("") || "<dd>No matches.</dd>"; }
  render(""); document.getElementById("q").addEventListener("input", e => render(e.target.value));
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
