<?php $page = ['title' => 'Chapter 12 glossary', 'chapter' => 12]; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Chapter 12</div>
    <h1>Glossary</h1>
    <p class="lead">Terms as used in this chapter, in plain words. Tags: <span class="pill">Esri</span> <span class="pill">QGIS</span> <span class="pill">Web</span> — untagged terms are general ideas.</p>
  </div>
  <input class="gloss-search" id="q" type="search" placeholder="Type to filter… e.g. scale, quantile, choropleth, legend" aria-label="Filter glossary">
  <dl class="gloss" id="gloss"></dl>
  <p style="margin-top:2rem"><a class="btn ghost" href="./">← Chapter home</a></p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const E = ' <span class="pill">Esri</span>', Q = ' <span class="pill">QGIS</span>', W = ' <span class="pill">Web</span>';
  const terms = [
    ["Operations map", "A map whose reader must find and act on individual things — the crew map. The unit shown is the feature (a request point)."],
    ["Management map", "A map whose reader compares areas — the manager’s map. The unit shown is the area (a ward) with one value each."],
    ["Medium / viewing size / interaction", "Where, how large, and how interactively the map is read (paper, projector, phone; A4 or 70 mm; zoom and click or not). Decides sizes, scale ranges and what must be visible by default."],
    ["Map scale / representative fraction (RF)", "Map distance ÷ ground distance, written 1:25,000. The 25,000 is the scale denominator: 1 cm on the map = 250 m on the ground."],
    ["Large scale / small scale", "Large scale = large fraction = small denominator = small area, much detail (1:1,000). Small scale = large denominator = large area, little detail (1:1,000,000). Opposite of everyday speech."],
    ["Scale bar (graphic scale)", "A bar labelled with its ground length. The only form of scale that stays right when the map is enlarged or shrunk. In an ArcGIS Pro layout it updates when the map frame’s scale changes." + E],
    ["Cell size / resolution", "The ground size of one raster cell — not positional accuracy (Chapter 4)."],
    ["Coordinate precision", "How many digits a coordinate is written with — not accuracy (Chapter 5)."],
    ["Positional accuracy", "How close a recorded position is to the true one; a property of the capture method (Chapter 9). The only one of the four that says how trustworthy a drawn point is."],
    ["Generalisation", "Deliberately simplifying geometry and detail to suit the scale and purpose: roads as fixed-width lines, close points merged. Not an error; undisclosed generalisation is."],
    ["Scale range / visibility range", "The scales between which a layer or its labels are drawn. ArcGIS Pro: Feature Layer tab ▸ Visibility Range. Web libraries use min/max zoom — not the same numbers." + E + W],
    ["Nominal / ordinal / quantitative", "Name (no order), order (no meaningful distance), amount (numbers you can subtract). The kind of field decides the symbol."],
    ["Unique values (Types)", "One symbol per category value. ArcGIS Pro: Unique values; ArcGIS Online: Types (unique symbols); QGIS: Categorized." + E + Q],
    ["Graduated colours / graduated symbols / proportional symbols", "Amounts shown by classes of a colour ramp / classes of symbol size / size in exact proportion. ArcGIS Online: Counts and Amounts (color / size); QGIS: Graduated." + E + Q],
    ["Visual hierarchy", "Data in front (strong), context in the middle (subdued), basemap behind (quiet)."],
    ["Symbol footprint", "The ground width a symbol covers at a given scale: paper size × scale denominator. 12 pt at 1:25,000 ≈ 106 m."],
    ["Grey check / colour-vision check", "Viewing the map without colour, or with a simulated colour deficiency, to confirm essential distinctions survive. ArcGIS Pro: View ▸ Accessibility ▸ Color Vision Simulator; QGIS: View ▸ Preview Mode." + E + Q],
    ["Classification / class breaks", "Dividing a numeric range into classes; the limits between them."],
    ["Equal interval", "Classes of equal value width (0–15, 15–30, 30–45)."],
    ["Quantile", "Classes with equal numbers of features (five wards each). Can split near-identical values and group very different ones."],
    ["Natural breaks (Jenks)", "Classes chosen so values inside a class are similar and classes differ; the breaks are tuned to this data, so two maps classified this way cannot be compared."],
    ["Manual / defined interval", "Author-chosen breaks / classes of a stated width. The comparison-safe choices."],
    ["Fixed classes", "Breaks held constant across every map in a set that will be compared."],
    ["Choropleth", "A map that fills each area with a colour for the class of a value belonging to that area (KLOR-o-pleth)."],
    ["Total / rate / density", "Count / count per unit of an exposure count (per 1,000 households) / count per unit of area or length (per km of road)."],
    ["Denominator (normalisation, Divide by)", "The exposure quantity a total is divided by to make areas comparable. ArcGIS Pro: Normalization; ArcGIS Online: Divide by." + E],
    ["Reporting exposure", "How much an area’s people report; a hidden part of any request rate that the request table cannot separate from real faults."],
    ["Map elements", "Title, legend, scale, orientation aid, source/date (acknowledgement), method notes, border — used because they help, not as decoration."],
    ["Zero vs missing", "0 is a value: classified, lowest class colour. Missing (NULL) is not: excluded from classes, drawn with a hatch off the ramp, named in the legend, left out of the total."],
    ["Show excluded values / Show features with no value", "ArcGIS Pro’s and ArcGIS Online’s controls for drawing NULL or out-of-range features with their own symbol." + E],
    ["Aggregation unit", "The area a value is summarised over. A ward value describes the ward, not the streets inside it."],
    ["Policy W-1", "Chapter 10’s written rule for a request on a shared ward edge: raw membership counts it in every touching ward; for a unique assignment it goes to the ward with the lower code; requests outside every ward are reported as unassigned, never dropped."],
    ["Presentation control", "Layer visibility, pop-up fields, filters, labels, symbology — changes what this map shows, not what a client can get from the service."],
    ["Data protection / access control", "Sharing, hosted views, editing rights — changes what a client can obtain. The later administration chapters."],
    ["Hosted feature layer view", "An ArcGIS Online item that references a source layer with its own field, feature and editing restrictions and its own sharing." + E],
    ["outFields", "The ArcGIS REST query parameter listing which fields to return; * returns all — including any field hidden from a pop-up." + E + W],
    ["Data minimisation (for a map)", "Build the map from only the fields its purpose needs; never label or export free text by default."],
    ["Source-value table", "The table of every value behind a map, delivered with it, so styling cannot hide a discrepancy."],
    ["District G-12", "This chapter’s made-up practice district: sixteen 1 km wards on the flat grid with open-request counts, households and road km; W03 = 0, W16 = no data."],
    ["Map B-12 / Map Z-12", "The deliberately bad map of the lab (nine faults) / the unfamiliar map of the critique task. Both invented."],
    ["Fixture E12", "The Phase 1 exit practical’s Earth-referenced data: Chapter 6’s wards, Depot Road RE-2 and requests X1–X10 in EPSG:32643 with planted defects."]
  ];
  const dl = document.getElementById("gloss");
  const render = f => { dl.innerHTML = terms.filter(t => !f || (t[0] + " " + t[1]).toLowerCase().includes(f)).map(t => `<dt>${t[0]}</dt><dd>${t[1]}</dd>`).join("") || "<dd>No matching term.</dd>"; };
  document.getElementById("q").addEventListener("input", e => render(e.target.value.trim().toLowerCase()));
  render("");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
