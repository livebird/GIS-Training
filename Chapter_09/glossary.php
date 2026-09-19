<?php $page = ['title' => 'Chapter 9 glossary', 'chapter' => 9]; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Chapter 9</div>
    <h1>Glossary</h1>
    <p class="lead">Terms as used in this chapter, in plain words. Tags: <span class="pill">Esri</span> <span class="pill">QGIS</span> <span class="pill">PostGIS</span> — untagged terms are general ideas.</p>
  </div>
  <input class="gloss-search" id="q" type="search" placeholder="Type to filter… e.g. snapping, orphan, GNSS, residual" aria-label="Filter glossary">
  <dl class="gloss" id="gloss"></dl>
  <p style="margin-top:2rem"><a class="btn ghost" href="./">← Chapter home</a></p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const E = ' <span class="pill">Esri</span>', Q = ' <span class="pill">QGIS</span>', P = ' <span class="pill">PostGIS</span>';
  const terms = [
    ["Positional accuracy", "How far the stored dots are from where the real things are. Found by comparing a sample with a better measurement — never by a unit test."],
    ["Attribute correctness", "Whether the stored values (type, height, score) are true."],
    ["Completeness", "Whether everything that should be present is — and nothing extra: no blank must-have values, no orphan rows, no missing visits."],
    ["Logical consistency", "Whether records obey their rules: shape validity, topology rules, unique IDs, allowed values."],
    ["Currency", "Whether the data’s “as of” date is recent enough for the decision."],
    ["Fitness for purpose", "Whether a dataset is good enough for a stated decision — judged from its history, not from a universal number."],
    ["Intake checklist", "Six items filled before editing: source, date, reference (CRS/units), schema, original copy, intended output."],
    ["Provenance (history)", "How a location or value came to exist: method, device, source document, operator, date, settings, reported doubt."],
    ["Digitising (tracing)", "Drawing features on screen over a background image or layer. Inherits the background’s placement error and the operator’s care."],
    ["Coordinate import", "Loading a table that already holds X, Y. You observed nothing; the history comes from the upstream system."],
    ["GNSS / GPS", "GNSS = any satellite positioning system; GPS is the American one and the most used. Others: NavIC (India), Galileo, BeiDou, GLONASS, QZSS."],
    ["GNSS metadata", "Per-point quality columns a field app can store: receiver, fix type, horizontal/vertical accuracy, satellites, DOP values, correction age." + E],
    ["Location profile", "Field Maps setting carrying the datum transformation between the receiver’s reference and the map’s." + E],
    ["Geocoding / locator", "Turning address text into a point using a locator — “a portable file used to perform geocoding” holding a snapshot of reference data." + E],
    ["Status / Score / Addr_type", "Geocoding history columns: M/T/U, 0–100, and the match level (PointAddress, StreetAddress, Postal, Locality…)." + E],
    ["Georeferencing", "Giving an image real coordinates by fitting a formula through control points; the image gets a CRS as part of it."],
    ["Control point (GCP)", "A spot you can identify both on the image and on the ground."],
    ["Affine (first-order) transformation", "x = a + b·c + d·r, y = e + f·c + g·r — shift, scale, rotate, shear. Needs at least 3 control points."],
    ["Residual / RMS error", "The gap between where the formula puts a control point and where it should be; RMS is their root-mean-square. Not accuracy away from the points."],
    ["Check point", "A known spot kept out of the fit so it can test the fit honestly (course term)."],
    ["World file / auxiliary file", "A small side file holding a raster’s georeferencing without rewriting its pixels."],
    ["Edit session", "Starts by itself when you change data; ends with Save or Discard." + E],
    ["Create / move / split / reshape", "The four shape edits. Each changes other things too: lengths, IDs, neighbours."],
    ["Snapping / snap agent", "A drawing aid that stores an existing coordinate when you click within the tolerance; agents (Esri) or modes (QGIS) choose vertex, edge, endpoint, etc."],
    ["Snapping tolerance", "How close the pointer must be for snapping to act. Default 10 pixels in ArcGIS Pro; pixels or map units. A pixel value is a different ground distance at every zoom."],
    ["Search radius", "How far QGIS looks for the vertex you click to move." + Q],
    ["Topological editing / Avoid Overlap", "Moving shared vertices in both neighbours together; cutting a new polygon to fit its neighbours." + Q],
    ["Geometry validity", "Single-shape rules (OGC): rings simple, no crossings, touch only at points, holes inside the shell, inside in one piece. Lines only need two different points."],
    ["Self-intersection", "A ring or line crossing itself. Makes a polygon invalid; a line stays valid."],
    ["Validation method (Esri / OGC)", "Check/Repair Geometry option. Esri expects outer rings clockwise (opposite of GeoJSON); OGC follows the standard." + E],
    ["Topology", "How point, line and area features share geometry — the spatial relationships between neighbours."],
    ["Topology rule", "A stated allowed relationship: Must Not Overlap, Must Not Have Gaps, Must Not Have Dangles, Must Be Properly Inside…"],
    ["Dangle / undershoot / overshoot", "A line end touching nothing; a line stopping short of, or running past, the line it should meet."],
    ["Sliver / gap", "A thin void or overlap where polygon boundaries do not match."],
    ["Error / exception", "A stored rule violation; an error marked as acceptable with evidence." + E],
    ["Cluster tolerance / rank", "Distance within which vertices are pulled together during validation (default 0.001 m); rank decides which layer moves less." + E],
    ["Geodatabase topology", "Rules stored on feature classes in a feature dataset; needs a Standard or Advanced licence." + E],
    ["Topology Checker", "Core plugin that validates rules per layer and lists errors." + Q],
    ["Orphan", "A child row (inspection) whose key matches no parent (asset) — from Chapter 8."],
    ["Defect log", "ID, symptom, affected records, likely cause, evidence, proposed correction, reviewer decision — one row per finding, false alarms included."],
    ["C / O / X", "Correct with evidence / needs the Owner / eXception or false alarm (course convention)."],
    ["Before/after record", "Feature, element, old value, new value, evidence, editor, time — written for every change."],
    ["Automatic repair", "Repair Geometry, Fix geometries, ST_MakeValid: produce a valid shape, possibly a different shape or type. Inspect the result."],
    ["Linework / Structure", "The two repair methods: node all rings and extract polygons; or fix rings then union shells and subtract holes." + Q + P],
    ["Unresolved-issues list", "Open items with the question and evidence that would settle each. Never replaced by invented values."],
    ["Find Identical", "Tool reporting records identical in listed fields, optionally geometry within a tolerance." + E],
    ["Ward C numbers", "0 m² as typed (meaningless) · 250,000 m² auto-repaired (valid, wrong) · 500,000 m² corrected from the register (right)."]
  ];
  const dl = document.getElementById("gloss");
  const render = f => { dl.innerHTML = terms.filter(t => !f || (t[0] + " " + t[1]).toLowerCase().includes(f)).map(t => `<dt>${t[0]}</dt><dd>${t[1]}</dd>`).join("") || "<dd>No matching term.</dd>"; };
  document.getElementById("q").addEventListener("input", e => render(e.target.value.trim().toLowerCase()));
  render("");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
