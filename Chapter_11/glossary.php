<?php $page = ['title' => 'Chapter 11 glossary', 'chapter' => 11]; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Chapter 11</div>
    <h1>Glossary</h1>
    <p class="lead">Terms as used in this chapter, in plain words. Tags: <span class="pill">Esri</span> <span class="pill">QGIS</span> <span class="pill">PostGIS</span> — untagged terms are general ideas.</p>
  </div>
  <input class="gloss-search" id="q" type="search" placeholder="Type to filter… e.g. buffer, dissolve, Join_Count, NoData" aria-label="Filter glossary">
  <dl class="gloss" id="gloss"></dl>
  <p style="margin-top:2rem"><a class="btn ghost" href="./">← Chapter home</a></p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const E = ' <span class="pill">Esri</span>', Q = ' <span class="pill">QGIS</span>', P = ' <span class="pill">PostGIS</span>';
  const terms = [
    ["Analysis specification", "The seven lines you write before any tool: decision, study area, time window, eligible records, units/CRS/method, output kind, and acceptance checks (11.1)."],
    ["Analysis worksheet", "One row per step with its operation, inputs, output and — most important — who consumes the output. A row nobody consumes is removed (11.1.3)."],
    ["Output kind", "Whether an answer is a selection (existing records), a new shape, extra columns, or a summary (one row per group). It decides the operation (11.1.2)."],
    ["Buffer", "The polygon made of every point within a chosen distance of a feature. Always a polygon, whatever the input (11.2)."],
    ["End type / end cap", "How a line’s buffer is closed at the line’s ends: round (a half-circle past the end) or flat (stops at the end). Esri: End Type; QGIS: End cap style; PostGIS: endcap=round|flat|square."],
    ["Dissolve type (buffer)", "Whether separate buffers are kept (None), all merged into one (All), or merged by shared field values (List)." + E],
    ["Planar method", "Buffer or distance computed on the flat map. In ArcGIS Pro’s Buffer, Planar on a geographic layer with a metre distance actually produces a geodesic buffer — product-specific behaviour." + E],
    ["Geodesic method", "Buffer or distance computed on the curved Earth model, whatever the CRS. Chapter 6 explains when it matters."],
    ["BUFF_DIST / ORIG_FID", "Fields ArcGIS Pro writes on a buffer output with Dissolve Type None: the distance used (in the input’s unit) and the source feature’s ID. BUFF_DIST = 300 is your first units check." + E],
    ["ST_Buffer / ST_DWithin", "PostGIS buffer (units of the geometry’s SRS; metres for geography) and the within-distance test PostGIS recommends instead of buffering for queries." + P],
    ["Clip", "Keeps only the parts of input features inside the cutter (usually the study area). Geometry is cut; input attributes are copied unchanged; nothing from the cutter is added (11.3)."],
    ["Stale measurement", "A stored length or area field that no longer describes the feature after its shape changed — e.g. length_m = 2000 on a 1,000 m clipped road (11.3.3)."],
    ["Ratio policy", "An ArcGIS Pro layer-field option that scales a copied number by the fraction of the shape kept in Clip or Intersect. Assumes the quantity is spread evenly." + E],
    ["Intersect (overlay)", "Builds the parts common to two (or more) layers as new features carrying attributes from all inputs, splitting records at overlay edges (11.4)."],
    ["Predicate versus overlay", "“Intersects?” is a yes/no about existing records (Chapter 10). “Intersection” constructs new geometry (this chapter)."],
    ["Output dimension", "Point 0, line 1, polygon 2. An overlay’s default output is the lowest dimension among its inputs; ArcGIS Pro’s Output Type can force LINE or POINT." + E],
    ["Allocation rule", "Your stated rule for a quantity copied onto split records: recompute from geometry (safest), weight by kept fraction (needs an assumption), or do not sum (labels) (11.4.3)."],
    ["Dissolve", "Groups features by a field’s values and unions each group’s shapes — GROUP BY for geometry. Other columns are dropped (Esri) or hold the first feature’s values (QGIS) (11.5)."],
    ["Multipart feature", "One feature made of several separate pieces — e.g. Zone Z2 after dissolving wards B and D, which do not touch."],
    ["Create multipart features / Keep disjoint features separate", "The Esri and QGIS options that decide whether a dissolved group with non-touching members becomes one multipart feature or several features." + E + Q],
    ["Statistics fields", "Esri Dissolve option: which fields to summarise and how (SUM, COUNT, MEAN…); output fields are named like SUM_households. Nulls are excluded." + E],
    ["ST_Union (aggregate)", "PostGIS: the union of a set of geometries, used with GROUP BY exactly like SUM()." + P],
    ["Append / merge", "Stacking records of several datasets into one table without changing shapes. Not a dissolve."],
    ["Spatial join", "Copies attributes from one layer onto another because of where features are, not a shared key. Used to count features per area or add an area’s name to points (11.6)."],
    ["Target / join features", "Esri’s names: target = the layer whose records appear in the output; join = the layer whose attributes are copied onto them." + E],
    ["Join one to one / one to many", "One output row per target (multiple matches aggregated by a merge rule) versus one row per target–match pair." + E],
    ["Keep all target features", "Ticked: every target appears, matched or not (outer join) — keeps the zero-count wards. Unticked: only matched targets (inner join)." + E],
    ["Join_Count / JOIN_FID", "Esri output fields: the number of join features matching each target; with one-to-many, the matched join feature’s ID (−1 = no match)." + E],
    ["Match option", "The yes/no test a spatial join uses: Intersect, Contains, Completely contains, Contains Clementini, Within, Closest, Have their center in, Largest overlap… Contains includes a point on the boundary; Completely contains does not." + E],
    ["Join attributes by location (summary) / Count points in polygon", "QGIS algorithms that count or summarise features per polygon (JOINED_COUNT, NUMPOINTS)." + Q],
    ["Boundary policy (Chapter 10)", "Raw membership is boundary-inclusive. For a one-ward-per-complaint report: interior → its ward (INTERIOR); on two wards’ shared line → the alphabetically lower ward code (BOUNDARY_TIEBREAK); in no ward → NULL (OUTSIDE), reported, never dropped."],
    ["Reconciliation", "The per-record table showing each complaint as assigned, counted twice, or outside; assigned + twice + outside must equal the number of complaints (11.6.3)."],
    ["NoData", "A raster cell with no known value. Not zero. Every statistic must say how NoData was treated (excluded, replaced, or propagated) (11.7.1)."],
    ["NoData policy", "Your stated choice for missing cells — e.g. “excluded (n = 8 of 9)”. The tool’s own behaviour is documented per tool; read it."],
    ["Mask", "A raster of 1 where a per-cell condition is true and 0 where it is false; NoData stays NoData. A WHERE clause for cells (11.7.2)."],
    ["Con / Raster Calculator", "ArcGIS Pro raster tools for conditions and map algebra; both need the Spatial Analyst or Image Analyst extension." + E],
    ["Snap Raster / Cell Size (environments)", "ArcGIS Pro settings that force an output onto a reference grid and set its cell size (default: the coarsest input)." + E],
    ["Alignment", "Whether two rasters’ cell edges coincide. Without it, cell-by-cell maths silently resamples one input (11.7.3)."],
    ["Invariant", "A property an output must have if the steps were done as stated: counts, IDs, geometry type, extent, CRS, units, validity, attributes (11.8.1)."],
    ["Spot-check", "One record verified by hand from its coordinates — not by looking at the map."],
    ["Sensitivity exercise", "Change one input or setting, predict the change, run, compare. Shows which parts of the result are fragile (11.8.2)."],
    ["Geoprocessing history / Processing history", "The product’s record of tool runs and parameters (ArcGIS Pro History pane; QGIS history manager). Raw material for your log — not the log itself." + E + Q],
    ["Near", "ArcGIS Pro tool that adds NEAR_FID and NEAR_DIST to its input (run it on a copy). Ties are broken at random." + E],
    ["Fixture E11", "The Earth-referenced practice data for the lab: Chapter 6’s wards (EPSG:4326) and requests (EPSG:32643) plus Road RE-1 and status/date columns. Made up."]
  ];
  const dl = document.getElementById("gloss");
  const render = f => { dl.innerHTML = terms.filter(([t, d]) => !f || (t + d).toLowerCase().includes(f)).map(([t, d]) => `<dt>${t}</dt><dd>${d}</dd>`).join("") || "<dd>No matching term.</dd>"; };
  document.getElementById("q").addEventListener("input", e => render(e.target.value.trim().toLowerCase())); render("");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
