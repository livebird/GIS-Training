<?php $page = ['title' => '6.4 Lined up on screen is not analysis-ready', 'chapter' => 6, 'module' => '6.4']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 6.4 · General idea, with ArcGIS / QGIS behaviour</div>
    <h1>Lined up on screen is not the same as ready for analysis</h1>
    <p class="lead">ArcGIS Pro and QGIS will happily draw a layer in degrees on top of a layer in UTM metres, perfectly aligned. That is called <strong>on-the-fly reprojection</strong>. It is a <em>view</em>: nothing stored has changed. Before you measure or analyse, you need to know which coordinate system each step actually runs in.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>See what on-the-fly display reprojection does — and what it proves (very little).</li>
      <li>Fill in a five-row worksheet: input CRS, display CRS, processing CRS, transformation, units.</li>
      <li>Read three real tool rules to see that “the tool uses the map CRS” is <em>not</em> a general truth.</li></ul></div>
  </div>

  <h2><span class="mod">6.4.1</span>The map is a view</h2>
  <p>QGIS: “QGIS transparently reprojects all layers contained within your project into the project’s CRS.” ArcGIS Pro: “ArcGIS Pro reprojects data on the fly so any data you add to a map adopts the coordinate system definition of the first layer added.” Three consequences:</p>
  <ol>
    <li><strong>The stored coordinates do not change.</strong> Your UTM file still contains eastings and northings after you drop it on a degrees map.</li>
    <li><strong>It only works if the labels are right.</strong> A wrong label (6.2) or a missing transformation (6.3) gives a misaligned — or <em>subtly</em> shifted — layer. And a subtly shifted layer looks aligned.</li>
    <li><strong>It is for looking, not for analysis or editing.</strong> Esri says this approach “should not be used for analysis or editing, because it can lead to inaccuracies from misaligned data among layers”, and recommends you “first project it into a consistent coordinate system shared by all your layers”.</li>
  </ol>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Two layers, two coordinate systems, one map</h3>
    <p>The wards are stored in <strong>degrees</strong> (EPSG:4326). The requests are stored in <strong>UTM metres</strong> (EPSG:32643). Change the <em>map’s</em> coordinate system and watch: the picture changes, the stored numbers do not.</p>
    <div class="controls">
      <label>Map (display) CRS
        <select id="mapCrs"><option value="deg">EPSG:4326 — degrees (first layer added)</option><option value="utm">EPSG:32643 — UTM 43N metres</option><option value="wm">EPSG:3857 — Web Mercator metres</option></select></label>
    </div>
    <div class="map-with-panel">
      <div class="layerlist"><div class="hdr">Layers (stored CRS)</div>
        <label><span class="sw" style="background:#dbe7f3;border:1px solid #5b7ea3"></span> wards_e6 — <span class="mono">EPSG:4326</span></label>
        <label><span class="sw" style="background:#d3541f"></span> requests_e6 — <span class="mono">EPSG:32643</span></label>
        <div class="hdr" style="margin-top:.6rem">Q1 stored values</div>
        <div class="mono small" id="storedQ1"></div>
        <div class="hdr" style="margin-top:.6rem">Ward A first corner, stored</div>
        <div class="mono small">74.990, 23.000 (lon, lat)</div>
      </div>
      <figure class="map-fig" id="otfFig"></figure>
    </div>
    <div class="result" id="otfOut"></div>
  </div>

  <h3>Where to see the map’s CRS and each layer’s CRS</h3>
  <div class="tabs"><button>ArcGIS Pro 3.7 (from the docs; not run by the author)</button><button>QGIS 3.40</button></div>
  <div class="tabpanel"><p>Right-click the map in the <strong>Contents</strong> pane ▸ <strong>Properties</strong> ▸ <strong>Coordinate Systems</strong> tab. “Current Map Coordinate Systems” shows the map’s horizontal and vertical CRS; expand the <strong>Layers</strong> folder to see each coordinate system and the layers that use it. The map’s transformation is on the <strong>Transformation</strong> tab of the same dialog. A single layer’s own CRS: right-click the layer ▸ <strong>Properties</strong> ▸ <strong>Source</strong> — it shows “the layer’s extent, spatial reference, domain, resolution, and tolerance information”.</p></div>
  <div class="tabpanel"><p>The project CRS is shown at the bottom-right of the status bar and set under <strong>Project ▸ Properties ▸ CRS</strong>. A layer’s CRS is under the layer’s <strong>Properties ▸ Source</strong>. A new project starts in EPSG:4326 unless QGIS is configured to take the first layer’s CRS — check which applies on your installation.</p></div>
  <div class="callout dev"><span class="label">Developer view</span><p>Display reprojection is a <strong>database view</strong> over tables with different schemas: convenient, read-only, and slower to aggregate over — and it depends entirely on the mapping being right. When you need serious computation you <em>materialise</em> a clean table. <strong>Where it breaks:</strong> a bad SQL view usually returns visibly wrong rows; a bad display reprojection returns a picture that looks fine.</p></div>

  <h2><span class="mod">6.4.2</span>The five-row worksheet</h2>
  <p>Before any measurement or analysis, fill this in. The rows are deliberately separate, because people collapse them into “the CRS”. Rows 2 and 3 are the ones most often confused: what you <em>see</em> versus what the numbers are <em>computed in</em>.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Fill the worksheet for the 6.8 lab</h3>
    <p>Choose the right answer in each row for the Fixture E6 lab (wards in EPSG:4326, requests in EPSG:32643, analysis to be done in UTM 43N).</p>
    <div class="sheet" id="sheet">
      <div class="srow"><div>1. Input CRS(s)</div><div><select data-ok="c"><option value="">choose…</option><option value="a">Both files are in EPSG:4326</option><option value="b">Both files are in whatever the map is set to</option><option value="c">wards_e6: EPSG:4326 (degrees); requests_e6: EPSG:32643 (metres) — from their provenance notes</option></select><div class="fbk"></div></div></div>
      <div class="srow"><div>2. Display CRS</div><div><select data-ok="b"><option value="">choose…</option><option value="a">Must be UTM 43N or the data is wrong</option><option value="b">Whatever the map adopted (usually the first layer’s). Note it; it does not change the stored data</option><option value="c">Not needed — display and processing are the same thing</option></select><div class="fbk"></div></div></div>
      <div class="srow"><div>3. Processing / output CRS</div><div><select data-ok="a"><option value="">choose…</option><option value="a">EPSG:32643 — small area inside one zone; metres; flat-map measurement acceptable</option><option value="b">EPSG:3857 — because the basemap is Web Mercator</option><option value="c">EPSG:4326 — because degrees are the “original”</option></select><div class="fbk"></div></div></div>
      <div class="srow"><div>4. Transformation</div><div><select data-ok="b"><option value="">choose…</option><option value="a">Use the default transformation the software offers</option><option value="b">None required — both inputs are on WGS 84. Write exactly that in the log</option><option value="c">Kalianpur 1975 to WGS 84 (1), because the town is in India</option></select><div class="fbk"></div></div></div>
      <div class="srow"><div>5. Units</div><div><select data-ok="c"><option value="">choose…</option><option value="a">Metres everywhere</option><option value="b">Degrees everywhere</option><option value="c">Inputs: degrees and metres. Computation: metres. Report: m and m², with the rounding stated</option></select><div class="fbk"></div></div></div>
    </div>
  </div>

  <h2><span class="mod">6.4.3</span>Read the actual tool’s rule</h2>
  <p>Do not generalise. It is <em>not</em> true that every tool silently uses the map CRS, and it is <em>not</em> true that every tool needs physically reprojected input. Each tool has its own rule. Three verified ArcGIS Pro examples and one QGIS example:</p>
  <div class="grid-2">
    <div class="card"><h3 style="margin-top:0">Geoprocessing in general</h3><p>When a tool takes several inputs in different CRSs, “the spatial reference of the first input dataset will be used” — unless the <strong>Output Coordinate System</strong> environment is set, in which case “the input is projected to the output coordinate system during tool operation” and processing happens in that system. Warning from the same page: “A projection will not occur if either the input or output coordinate system is unknown.” And the default tolerance for an unknown CRS, 0.001 units, “could be as large as 110 meters” if the units are degrees.</p></div>
    <div class="card"><h3 style="margin-top:0">Buffer</h3><p>Its rule depends on the <em>input’s</em> CRS and on <em>what units you type</em>: “If the input features have a projected coordinate system, Euclidean buffers will be created”; “If the input features have a geographic coordinate system and you specify a Buffer Distance value in linear units … geodesic buffers will be created.” You can also choose <strong>Geodesic (shape preserving)</strong> explicitly. So Buffer neither uses the map CRS nor needs reprojected input — but its answer depends on what you typed.</p></div>
    <div class="card"><h3 style="margin-top:0">Calculate Geometry Attributes</h3><p>“The coordinate system of the input features is used by default”, and results are in that CRS’s units unless you choose others. But: “Length and area calculations are not supported when the input features have a geographic coordinate system or a projected coordinate system based on Web Mercator” — choose the <em>geodesic</em> length/area properties for those. (And note: this tool <em>modifies the input data</em>. Work on a copy.)</p></div>
    <div class="card"><h3 style="margin-top:0">QGIS measurements</h3><p>QGIS measure tools and geometry expressions default to <strong>ellipsoidal</strong> calculation using the ellipsoid set under <strong>Project ▸ Properties ▸ General ▸ Measurements</strong>. Set it to <strong>None / Planimetric</strong> and they become flat-map values in the project CRS. The <em>Add geometry attributes</em> algorithm has a <strong>Calculate using</strong> option: Layer CRS, Project CRS, or Ellipsoidal. So in QGIS a project <em>setting</em> can decide the method — a different rule from ArcGIS Pro.</p></div>
  </div>

  <div class="callout warn"><span class="label">Common mistake</span><p><em>“I set the map to UTM, so everything I compute is in UTM.”</em> In ArcGIS Pro, geoprocessing uses the first input’s CRS (or the environment), not the map’s, unless you pick “Current Map” in the environment. In QGIS the ellipsoid setting can make a measurement ellipsoidal even though the project is in UTM. The map is a view; the tool’s documentation page is the contract.</p></div>

  <div class="quiz" data-answer="1" data-fb="The map adopted the first layer’s CRS — EPSG:4326. The tool processes in the first input’s CRS (whichever was given first), not the map’s; the learner cannot know without checking. Rows 3 (processing CRS: EPSG:32643, chosen), 4 (none required — same WGS 84) and 5 (metres, rounding) were skipped.">
    <div class="q">A learner adds wards_e6 (EPSG:4326) and then requests_e6 (EPSG:32643) to an empty ArcGIS Pro map, sees them aligned, and runs a tool with both as inputs and no environment set. Which statement is correct?</div>
    <div class="opts">
      <button class="opt">The map is in EPSG:32643 and the tool processes in EPSG:32643, because the requests are in metres.</button>
      <button class="opt">The map is in EPSG:4326 (first layer); the tool processes in the first <em>input’s</em> CRS, not the map’s; worksheet rows 3, 4 and 5 were skipped.</button>
      <button class="opt">Because the layers are aligned, the tool will produce correct metre results whatever the settings.</button>
      <button class="opt">The map is in Web Mercator because all ArcGIS maps are, and the tool processes there too.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const q1 = E6.requests[0];
  document.getElementById("storedQ1").textContent = `${fx(q1.E)}, ${fx(q1.N)} (E, N metres)`;
  const msgs = {
    deg: "Map in degrees: the requests were converted for drawing only. Their file still says 500000.000 / 2543741.163. Notice the wards look like squares here — degrees are stretched east–west compared with the ground.",
    utm: "Map in UTM 43N: now the wards are converted for drawing and look like the rectangles they really are on the ground. The wards file still says 74.990, 23.000 …",
    wm: "Map in Web Mercator: both layers converted for drawing. Everything is about 9 % bigger than on the UTM map — but the stored numbers in both files are exactly as before."
  };
  const sel = document.getElementById("mapCrs");
  const upd = () => { renderE6(document.getElementById("otfFig"), { crs: sel.value, caption: "Same two files, drawn in the map CRS you chose. Stored coordinates unchanged (made-up data)." }); document.getElementById("otfOut").textContent = msgs[sel.value]; };
  sel.addEventListener("change", upd); upd();
  // worksheet
  document.querySelectorAll("#sheet select").forEach(s => s.addEventListener("change", () => {
    const fb = s.parentElement.querySelector(".fbk"); if (!s.value) { fb.className = "fbk"; return; }
    const ok = s.value === s.dataset.ok; fb.className = "fbk show " + (ok ? "ok" : "no");
    fb.textContent = ok ? "Yes — that is what the log should say." : "Not this one. Re-read 6.4.1–6.4.2: what is stored, what is displayed, and what is computed are three different things.";
  }));
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
