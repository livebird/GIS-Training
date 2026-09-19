<?php $page = ['title' => 'Chapter 10 glossary', 'chapter' => 10]; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Chapter 10</div>
    <h1>Glossary</h1>
    <p class="lead">Terms as used in this chapter, in plain words. Tags: <span class="pill">Esri</span> <span class="pill">QGIS</span> <span class="pill">PostGIS</span> — untagged terms are general ideas.</p>
  </div>
  <input class="gloss-search" id="q" type="search" placeholder="Type to filter… e.g. null, covers, join, boundary, tie" aria-label="Filter glossary">
  <dl class="gloss" id="gloss"></dl>
  <p style="margin-top:2rem"><a class="btn ghost" href="./">← Chapter home</a></p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const E = ' <span class="pill">Esri</span>', Q = ' <span class="pill">QGIS</span>', P = ' <span class="pill">PostGIS</span>';
  const terms = [
    ["Attribute condition", "A yes/no test on a record’s columns — a SQL WHERE clause. GIS query builders write one for you."],
    ["Attribute join (join by identity)", "Pairing rows whose key values are equal. The geometry stays with the left table and the right table’s columns are added."],
    ["Filter (view)", "Shows only the rows that pass a condition. Changes nothing. In ArcGIS Pro a definition query; in ArcGIS Online a filter; in QGIS a layer filter."],
    ["Definition query", "An ArcGIS Pro layer filter that limits which features the layer retrieves, draws, lists — and which features tools process." + E],
    ["Selection", "A set of highlighted records held by the session. Other tools may act on “selected records only”. Nothing is stored."],
    ["Export (subset)", "A new file or table holding only the matching rows. The source is untouched; the copy goes stale."],
    ["Modify (update)", "Changing stored values: Calculate Field, UPDATE, editing. Permanent without a backup."],
    ["Null (unknown)", "A missing value. Comparing anything with it gives unknown, not true or false, so the row passes neither a condition nor its opposite. Test with IS NULL / IS NOT NULL."],
    ["Three-valued logic", "true / false / unknown. WHERE keeps only true. A null in a comparison makes the result unknown."],
    ["Zero vs null vs empty", "0 is a known value; NULL is unknown; '' is known-empty text. Chapter 8’s distinction, now visible in query counts."],
    ["Precedence (AND before OR)", "AND is evaluated before OR. When both appear, add brackets."],
    ["LIKE / ILIKE", "Text pattern test. % = any run of characters, _ = exactly one. LIKE is case-sensitive; QGIS’s ILIKE is not. Matches letters, not meaning." + Q],
    ["Date literal", "How a date is written in a condition — differs by source: timestamp '…' / date '…' in a file geodatabase, JULIANDAY() in SQLite, TIMESTAMP '…' via the REST API."],
    ["Field delimiter", "Quotes around a field name. Double quotes for file geodatabases and shapefiles; none for enterprise geodatabases." + E],
    ["Unmatched key", "A left row whose key equals nothing on the right (a null key, or a value that does not exist). Kept with empty columns, or dropped, depending on a setting."],
    ["Duplicate key", "The same key value on more than one right-hand row. Each matching left row is repeated once per duplicate — and any sum over those rows is inflated."],
    ["Keep all (outer join) / matches only (inner join)", "Whether unmatched left rows survive the join. ArcGIS: “Keep all input records” / “Keep All Target Features”; QGIS: “Discard records which could not be joined”; SQL: LEFT vs INNER JOIN."],
    ["One-to-many / one-to-first (one-to-one)", "Whether several matches produce several rows, or only the first match is used. “First” is not defined by any column and may change between runs." + E + Q],
    ["Counting unit", "What a count measures: left entities, right entities, matched pairs, distinct matched entities, or unmatched entities. Always say which."],
    ["Spatial predicate", "A yes/no test on two shapes: intersects, disjoint, contains/within, touches, overlaps…"],
    ["Interior / boundary / exterior", "The three parts of any shape. A polygon’s boundary is its rings (outer and holes); a line’s boundary is its two end points; a point has no boundary."],
    ["Intersects", "Any point in common — boundary contact counts. The most permissive test; the opposite of disjoint."],
    ["Disjoint", "No point in common."],
    ["Contains / within", "Every point of B is in A and the interiors share a point. A boundary-only point is NOT contained (OGC rule; PostGIS ST_Contains, QGIS contain / are within, Esri Completely within)."],
    ["Covers / covered by", "Every point of B is in A, boundary included. PostGIS ST_Covers; the PostGIS manual recommends it over ST_Contains." + P],
    ["Within (Esri option)", "Boundary-inclusive: “within or contained by”. Behaves like PostGIS covered-by, not ST_Within. Completely within and Within Clementini are the strict versions." + E],
    ["Clementini options", "Esri’s Within Clementini / Contains Clementini: the OGC interior/boundary rules, excluding features lying entirely on the boundary." + E],
    ["Touches", "Common points exist, and all of them lie on boundaries — no interior contact. Two wards sharing an edge; a point on a ward line."],
    ["Overlaps", "Same dimension, interiors meet, neither covers the other. A point never overlaps a polygon."],
    ["Nearest", "The candidate with the smallest distance. Needs a search limit and a tie rule; Esri picks randomly on a tie, QGIS documents no rule."],
    ["Within a distance", "A predicate: distance ≤ (or <) a threshold in stated units. Whether the exact threshold counts is per engine — test it."],
    ["Tie rule", "What a nearest query does when two candidates are equally close. Without a written one the result is not reproducible."],
    ["Search limit (search radius)", "A distance beyond which “nearest” returns nothing (Esri writes −1)."],
    ["Finite line", "A road segment stops at its end points; distance past the end is measured to the end vertex, not to an imaginary extension."],
    ["Bounding box (extent, envelope)", "The smallest upright rectangle containing a shape. Cheap to compare; used to find candidates. Box overlap never proves shape overlap."],
    ["Intersection (operation)", "Constructing the shared shape of two inputs — Chapter 11. Not the same as the intersects question."],
    ["Hole (interior ring)", "A ring inside a polygon that removes area. Inside the hole is exterior; on the hole’s ring is boundary."],
    ["x,y tolerance", "A tiny distance within which ArcGIS treats coordinates as the same during client-side operations." + E],
    ["Geodesic distance", "Distance along the ellipsoid, in metres — right for degree data and large extents. Contrast planar (flat map)."],
    ["Threshold in degrees", "What you get by typing 300 into a tool on a latitude/longitude layer. An ellipse on the ground, wrong for any metre rule."],
    ["2D vs 3D predicate", "The default tests ignore Z. ST_3DIntersects / Intersect 3D / Within a distance 3D read Z — only useful if Z is real and referenced on both features."],
    ["Assignment policy", "The written business rule that turns raw matches into one answer per record: boundary, overlap, unmatched, multiple matches."],
    ["Spatial join", "Transfers attributes between layers when a spatial relationship holds. Target keeps geometry; join supplies columns."],
    ["Target / join features", "The layer that keeps its geometry and gains columns / the layer that supplies them." + E],
    ["Match Option", "Spatial Join’s name for the predicate (Intersect, Within, Closest, Within a distance…)." + E],
    ["Join_Count / TARGET_FID / JOIN_FID", "Fields Spatial Join adds: matches per target; target ID; with one-to-many, which join feature made the row (−1 = none)." + E],
    ["Merge rule", "How one-to-one Spatial Join combines several matches into one row (First, Sum, Mean…). Text defaults are a verification item." + E],
    ["Join attributes by location", "QGIS’s spatial join: one-to-many, first match only, or largest overlap; summary variant aggregates." + Q],
    ["Fixture Z-10", "The assessment’s made-up data: two yards (one with a hole), two pipes, six points, a register with a duplicated code."]
  ];
  const dl = document.getElementById("gloss");
  const render = f => { dl.innerHTML = terms.filter(t => !f || (t[0] + " " + t[1]).toLowerCase().includes(f)).map(t => `<dt>${t[0]}</dt><dd>${t[1]}</dd>`).join("") || "<dd>No matching term.</dd>"; };
  document.getElementById("q").addEventListener("input", e => render(e.target.value.trim().toLowerCase()));
  render("");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
