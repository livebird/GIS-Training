<?php $page = ['title' => '5.7 Data CRS vs map CRS', 'chapter' => 5, 'module' => '5.7']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 5.7 · General idea + platform procedures (ArcGIS Pro, QGIS)</div>
    <h1>The data’s CRS and the map’s CRS are two different things</h1>
    <p class="lead">Whenever you look at a map, two CRSs are in play: the one the stored coordinates are actually in, and the one the map view draws in. They can differ — and layers lining up on screen proves nothing about the stored numbers. This module shows where each product reports both, gives you a seven-point checklist, and explains why a strange-looking layer is a clue, never a licence to guess.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Find the layer’s CRS and the map’s CRS in ArcGIS Pro and in QGIS.</li>
      <li>Run the seven-item coordinate-metadata checklist on any new dataset.</li>
      <li>Say what to do when a CRS is unknown — and what <em>not</em> to do.</li></ul></div>
  </div>

  <h2><span class="mod">5.7.1</span>On-the-fly: the map converts for display only</h2>
  <p>The <strong>data CRS</strong> (layer CRS) is the reference the stored coordinates are in. The <strong>map CRS</strong> (project/display CRS) is the one the view draws everything in. The application converts each layer <em>for display</em> so that layers line up — QGIS: “all layers that you then load, no matter what coordinate reference system they have, will be automatically displayed in the projection you defined”. ArcGIS Pro: when layers are added, “they are automatically displayed using the current coordinate system of the map or scene. If the map or scene’s geographic coordinate system is different than the geographic coordinate system of the layer, the data is projected in real time using a transformation.” Both products warn that this is a display convenience — ArcGIS Pro says real-time projection “is not advisable if you are editing data or performing analysis”.</p>
  <p>Below is a <strong>mock-up</strong> of a map window (not a real product screen). Switch the map CRS and watch the pointer read-out change; then click each layer and notice that <em>its</em> CRS does not change.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <div class="controls"><label>Map CRS <select id="mapCrs"><option value="3857">WGS 1984 Web Mercator (auxiliary sphere) — 3857, metres</option><option value="4326">WGS 1984 — 4326, degrees</option></select></label></div>
    <div id="gisWin"></div>
    <div class="result" id="gisOut">Move the pointer over the map, then click a layer in the Contents list.</div>
    <p class="small">Survey points U1–U3 are drawn at nominal positions only; their conversion from UTM to the map is Chapter 6 and is <em>not</em> computed here. The Web Mercator read-out uses the standard spherical formula on which EPSG:3857 is defined.</p>
  </div>
  <div class="callout idea"><span class="label">Where does the map CRS come from?</span><p>ArcGIS Pro: “Empty maps and scenes derive their coordinate systems from the first layer added to them”, and “In a new, empty map or local scene, the default horizontal coordinate system is WGS84 Web Mercator”. QGIS: the option <em>CRS for projects</em> offers “Use CRS from first layer added” or “Use a default CRS”. Practical consequence: <strong>if your map started with a web basemap, its map CRS is very likely EPSG:3857, whatever your data’s CRS is.</strong></p></div>

  <h3>Where each product shows it</h3>
  <div class="tabs"><button>ArcGIS Pro (docs labelled 3.7)</button><button>QGIS 3.40</button></div>
  <div class="tabpanel">
    <p class="small"><span class="synthetic">Procedure — version-specific · not execution-tested</span> Written from the official pages on 19 September 2026; labels can change between releases.</p>
    <h4>Data (layer) CRS</h4>
    <ol>
      <li>In the <strong>Contents</strong> pane, right-click the layer and click <strong>Properties</strong> (or double-click the layer name).</li>
      <li>Open the <strong>Source</strong> tab — Esri: “You can also view the layer’s extent, spatial reference, domain, resolution, and tolerance information from this tab.” Expand <strong>Spatial Reference</strong>; read the name, WKID, unit and datum onto a coordinate card.</li>
    </ol>
    <h4>Map CRS</h4>
    <ol start="3">
      <li>Right-click the map in <strong>Contents</strong> → <strong>Properties</strong> → <strong>Coordinate Systems</strong> tab. “The Current Map Coordinate Systems heading shows the current horizontal and vertical coordinate systems of the map or scene … Click the name of the coordinate system … (in blue text) to see how they are defined.”</li>
      <li>Under <strong>Available Coordinate Systems</strong>, expand the <strong>Layers</strong> folder: “Expand a coordinate system heading to see the layers that reference it.” The fastest way to see every distinct data CRS in the map at once.</li>
      <li>Right-click any coordinate system → <strong>Details</strong> to read its parameters and its area of use.</li>
    </ol>
    <h4>Pointer coordinates</h4>
    <p>The read-out at the bottom of the map view shows “the real-world coordinate values corresponding with the pointer’s location”; its <strong>display units</strong> can be changed from the arrow beside the coordinates or from <strong>Map Properties › General › Display Units</strong>. Changing display units changes only what is printed — <strong>map units</strong> “are read-only, and you can only change them by changing the coordinate system of the map”.</p>
  </div>
  <div class="tabpanel">
    <p class="small"><span class="synthetic">Procedure — version-specific · not execution-tested</span></p>
    <h4>Data (layer) CRS</h4>
    <ol>
      <li>Right-click the layer → <strong>Properties</strong> → <strong>Source</strong> tab → read <strong>Assigned Coordinate Reference System (CRS)</strong>.</li>
      <li>Read the manual’s warning: “changing the CRS in this setting does not alter the underlying data source in any way, rather it just changes how QGIS interprets the raw coordinates from the layer in the current QGIS project.” This box is a <em>label</em>, not a conversion — the same distinction Chapter 6 makes for ArcGIS <em>Define Projection</em>.</li>
    </ol>
    <h4>Project (map) CRS</h4>
    <ol start="3">
      <li><strong>Project › Properties › CRS</strong>, or click the CRS button at the right end of the status bar — it displays the “current project CRS” and, when clicked, “opens the Project Properties dialog”.</li>
      <li>In the CRS selector, use the <strong>Filter</strong> box (by code or name) and read the preview map of the “approximate area of use” and the PROJ text.</li>
    </ol>
    <h4>Pointer coordinates</h4>
    <p>The status-bar coordinate box shows “the current position of the mouse, following it while moving across the map view”; the units follow the project settings.</p>
  </div>
  <div class="callout note"><span class="label">Verification item for instructors</span><p>Before teaching these steps live, confirm in the installed release: the exact tab and heading labels; that the <strong>Layers</strong> folder appears in the Coordinate Systems list; the wording of the QGIS Source tab; and what a layer with no CRS shows in each product (ArcGIS Pro: unknown; QGIS: the unknown-CRS icon, with coordinates “treated as purely numerical, non-earth values”).</p></div>

  <h2><span class="mod">5.7.2</span>The seven-point checklist</h2>
  <p>Run this on <strong>every</strong> new dataset before anything else is done with it. Tick what you can establish; the panel tells you what an unticked item means.</p>
  <div class="try">
    <span class="tag">Checklist</span>
    <div class="checklist" id="chk"></div>
    <div class="result" id="chkOut"></div>
  </div>
  <p class="small">Items 1–3 are read from definitions; item 4 is arithmetic; items 5–7 are reading and comparing documents. None of them requires a transformation.</p>

  <h2><span class="mod">5.7.3</span>Strange numbers are a clue, not a licence to guess</h2>
  <p>Sooner or later you will open a dataset that draws in the wrong place, or whose CRS is simply missing. Try both reactions.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <p>A layer with <strong>no CRS label</strong> draws far away from the basemap. What do you do?</p>
    <div class="controls">
      <button class="btn accent" id="guessBtn">Set its CRS to EPSG:32643 — it lands on the city — and save</button>
      <button class="btn" id="investBtn">Log the anomaly and investigate where the file came from</button>
      <button class="btn ghost" id="resetBtn">Reset</button>
    </div>
    <div class="result" id="guessOut">Choose one.</div>
  </div>
  <div class="table-wrap"><table>
    <thead><tr><th>You observe</th><th>Candidate explanations</th><th>Next question</th></tr></thead>
    <tbody>
      <tr><td>Values in −90…90 / −180…180</td><td>Geographic degrees; <strong>or</strong> a swap</td><td>Which column is which? Does the extent match the expected area?</td></tr>
      <tr><td>Eastings roughly 166 000–834 000 m, northings 0–10 000 000 m</td><td>UTM (some zone, some hemisphere); other similar grids</td><td>Which zone? Which hemisphere? Does the supplier say?</td></tr>
      <tr><td>Values up to about ±20 037 508 m in both axes</td><td>Web Mercator (EPSG:3857) — the limit is half the circumference of the 6 378 137 m sphere: π × 6 378 137 ≈ 20 037 508 m</td><td>Was this exported from a web map?</td></tr>
      <tr><td>Values 0 to a few thousand, no metadata</td><td>Local/engineering grid, our practice grid, or scaled units</td><td>Is there a documented origin and unit?</td></tr>
      <tr><td>Layer appears at (0, 0) in the ocean off West Africa</td><td>Nulls or zeros written as coordinates (Esri’s <em>XY Table To Point</em> treats 0 as a valid coordinate)</td><td>Are there missing values in the source?</td></tr>
      <tr><td>Right shape, offset by tens of metres</td><td>Datum mismatch (5.3)</td><td>Which datum does each dataset actually use?</td></tr>
    </tbody></table></div>
  <div class="callout idea"><span class="label">Working rule</span><p><strong>Unknown or implausible reference → investigate the source → write down what you found → only then assign, and only if the correct reference has been established.</strong> The lab’s Case D exists so that you practise stopping.</p></div>
  <div class="callout dev"><span class="label">Developer view</span><p>A stack trace tells you where to look; it does not tell you the fix. Magnitude and location anomalies are the stack trace of a CRS problem. <strong>Where the analogy stops:</strong> a stack trace is produced by the failing code, so it is trustworthy evidence. A “sensible-looking” map after a guess is produced by <em>your guess</em>, so it is not.</p></div>

  <div class="quiz" data-answer="1" data-fb="On-the-fly display conversion changes what is drawn, not what is stored. Only the layer’s own Source properties (or the file’s metadata) tell you the stored CRS.">
    <div class="q">Two layers — one in 4326, one in 32643 — line up perfectly on a Web Mercator basemap. What does this prove about their stored coordinates?</div>
    <div class="opts">
      <button class="opt">Both have been converted to Web Mercator.</button>
      <button class="opt">Nothing — the map converted each one for display; the stored numbers are unchanged.</button>
      <button class="opt">Both are now in 4326.</button>
      <button class="opt">The 32643 layer must have been mislabelled.</button>
    </div><div class="fb"></div>
  </div>

  <div class="callout note"><span class="label">Comprehension check (write it down)</span><p>A layer with no CRS metadata draws far from the basemap. A colleague sets its CRS to EPSG:32643 “because then it lands on the city” and saves. List what is now true, what is now unknown, and what should have happened instead.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const win = document.getElementById("gisWin"), gout = document.getElementById("gisOut");
  const drawWin = () => renderGisWindow(win, { mapCrs: document.getElementById("mapCrs").value, onLayer: l => { gout.innerHTML = `<b>${l.name}</b> — data CRS: <span style="font-family:var(--font-mono)">${l.crs}</span>, stored unit: <b>${l.unit}</b>.<br>Changing the map CRS above did <b>not</b> change this. The map converts it for display each time it draws.`; } });
  document.getElementById("mapCrs").addEventListener("change", () => { drawWin(); gout.innerHTML = "Map CRS changed. The pointer read-out now uses different units — but click any layer: its own CRS is exactly as before."; });
  drawWin();

  const items = [
    ["CRS known?", "Layer/dataset properties; embedded metadata (.prj file, database SRID, GeoPackage table); the supplier’s notes", "Stop. Do not assign one. Investigate the source (5.7.3)."],
    ["Units", "Read the CRS record’s unit of measure", "You cannot judge magnitudes or distances."],
    ["Coordinate order", "Read the container’s contract (5.5) and, for tables, the column names", "Risk of a silent swap (5.5.3)."],
    ["Extent plausibility", "Compare the data’s bounding extent (Chapter 3) with where the data should be, in the CRS’s units, and with the CRS’s area of use", "Swap, wrong CRS, wrong zone or wrong units."],
    ["Datum", "Read the CRS record’s base GCS / datum", "Possible offsets of metres to hundreds of metres (5.3)."],
    ["Vertical reference (if Z present)", "Vertical CRS in the record, or the schema’s own notes", "Z is undefined (5.6)."],
    ["Source metadata", "Who captured it, when, with what, and in which CRS they say", "Any disagreement with items 1–6 is a defect to log, not a detail to smooth over."]
  ];
  const chk = document.getElementById("chk"), cout = document.getElementById("chkOut");
  chk.innerHTML = items.map((it, i) => `<label><input type="checkbox" data-i="${i}"> <span><b>${i + 1} · ${it[0]}</b><br><span class="small" style="color:var(--ink-soft)">How: ${it[1]}</span></span></label>`).join("");
  const upd = () => { const un = [...chk.querySelectorAll("input")].filter(c => !c.checked).map(c => +c.dataset.i); cout.innerHTML = un.length === 0 ? `<div class="verdict ok">All seven established.</div>The dataset can go forward to Chapter 6 work.` : `<div class="verdict no">${un.length} item(s) not established.</div>` + un.map(i => `<b>${i + 1} · ${items[i][0]}:</b> ${items[i][2]}`).join("<br>"); };
  chk.querySelectorAll("input").forEach(c => c.addEventListener("change", upd)); upd();

  const g = document.getElementById("guessOut");
  document.getElementById("guessBtn").addEventListener("click", () => { g.innerHTML = `<div class="verdict no">It “works” — and that is the problem.</div><b>Now true:</b> the file carries the label EPSG:32643 and draws near the city.<br><b>Now unknown:</b> whether the numbers were ever UTM 43N (the numbers did not change — only the label); which of the plausible alternatives (zone 42N or 44N, a local grid, a Web Mercator export) was the real source; and whether anyone later can tell the label was a guess.<br><b>Consequence:</b> the guess is now indistinguishable from a verified fact for every future user. Chapter 6 shows that assigning a CRS changes only the label, never the numbers — so a wrong label permanently misdescribes correct numbers.`; });
  document.getElementById("investBtn").addEventListener("click", () => { g.innerHTML = `<div class="verdict ok">Correct reaction.</div>Record the anomaly. Run the seven-point checklist. Ask: what device or script produced the file? Is there a readme, an export log, a colleague who remembers? Check the file’s companions (a shapefile without its .prj, a CSV with a header sheet). If the CRS still cannot be established, <b>keep it unknown and log it</b>. Assign a CRS only once the correct reference is established with evidence — Chapter 6.`; });
  document.getElementById("resetBtn").addEventListener("click", () => { g.textContent = "Choose one."; });
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
