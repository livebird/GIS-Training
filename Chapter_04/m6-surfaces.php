<?php $page = ['title' => '4.6 Elevation rasters and 3D', 'chapter' => 4, 'module' => '4.6']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 4.6 · General principle · 3D scene is schematic</div>
    <h1>Elevation rasters: one height per cell</h1>
    <p class="lead">An elevation raster is a grid whose numbers are heights. Stand each cell up to its value and you get a stepped landscape — a <strong>height field</strong>. It is the most persuasive kind of raster, which is exactly why you must keep asking: heights of <em>what</em>, in <em>which unit</em>, above <em>which zero</em> — and is that pretty shaded picture evidence of anything?</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Lift the 16 cells of the elevation grid into a 3D block model and play with vertical exaggeration and lighting — while the numbers stay put.</li>
      <li>Tell a bare-ground surface (DTM) from one that includes buildings and trees (DSM).</li>
      <li>See why a height without a unit and a zero level is not a fact.</li></ul></div>
  </div>

  <h2><span class="mod">4.6.1</span>A height field — and which surface it is</h2>
  <p>For every (row, column) there is exactly <strong>one</strong> height. That is the whole representation, and also its limit: a height field cannot hold two heights at one spot, so it cannot represent a flyover with a road beneath it, a tunnel, a balcony, or the inside of a building. It represents <em>the</em> surface — whichever surface it was built to represent.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Sixteen cells, one surface (schematic 3D)</h3>
    <div class="slider-row"><label for="ex">Vertical exaggeration</label><input type="range" id="ex" min="1" max="10" step="1" value="3"><output id="exOut"></output></div>
    <div class="slider-row"><label for="az">Light from (azimuth)</label><input type="range" id="az" min="0" max="359" step="5" value="315"><output id="azOut"></output><button class="btn small ghost" id="numTog">Show numbers</button></div>
    <div class="fig-panel">
      <figure class="raster-fig" id="iso"></figure>
      <div class="result" id="isoOut"></div>
    </div>
    <p class="small">Made-up data. Heights in metres above the made-up zero “TD-0”. The hole is the NoData cell — it gets no height, no colour and no guess. The x/y grid is 100 m per cell; the vertical scale is stretched by the exaggeration you choose, and the label says so.</p>
  </div>
  <p>Change the exaggeration and the light: the picture changes completely; not one printed number changes. Keep that in mind for 4.6.3.</p>
  <h3>Which surface? Terrain, or terrain plus everything on it</h3>
  <p>Esri’s documentation distinguishes two products with the same structure and different content:</p>
  <div class="try">
    <span class="tag">Try it</span>
    <div class="toggle-row">
      <button class="btn small" id="dtmBtn" aria-pressed="true">DTM — bare ground</button>
      <button class="btn small" id="dsmBtn" aria-pressed="false">DSM — top of everything</button>
    </div>
    <figure class="raster-fig" id="xsec"></figure>
    <div class="result" id="xsOut"></div>
  </div>
  <div class="table-wrap"><table>
    <thead><tr><th>Term</th><th>What the heights are</th><th>Includes buildings and trees?</th><th>Typical use</th></tr></thead>
    <tbody>
      <tr><td><strong>Digital terrain model (DTM)</strong></td><td>The ground — “not including the elevation of any objects on it”, also called bare-earth</td><td>No</td><td>Drainage, slope, flood modelling</td></tr>
      <tr><td><strong>Digital surface model (DSM)</strong></td><td>The first thing seen from above — “including the elevation of objects on it such as trees and buildings”</td><td>Yes</td><td>Line of sight, building heights, tree canopy</td></tr>
      <tr><td><strong>Digital elevation model (DEM)</strong></td><td>Used loosely for either; often for bare earth</td><td>Depends on the dataset</td><td>—</td></tr>
    </tbody></table></div>
  <div class="callout warn"><span class="label">The names are not standard</span><p>A file called <code>dem.tif</code> might be either. The only reliable answer to “does this surface include the roof of the depot?” is <strong>the dataset’s own definition</strong> (Chapter 7). Under dense forest a DTM cannot even be made, because the ground is never seen; a DSM of the tree canopy is what you get.</p></div>
  <div class="try">
    <span class="tag">Worked example</span>
    <h3>How high is the ground at drain DR-0042?</h3>
    <p>DR-0042 is at (995, 510). The elevation grid covers x 0–400, y 600–1000. So: <strong>no answer from this grid</strong> — a raster answers only inside its extent (4.3.2). Suppose a second, whole-ward surface says 14.0 m there. Is that the ground or the roof of the building over the drain? Depends on whether that surface is a DTM or a DSM. The number cannot tell you. And the drain’s invert (the bottom of the pipe) is below ground — outside any height field’s vocabulary.</p>
  </div>
  <div class="callout dev"><span class="label">Developer view</span><p>A height field is the <strong>heightmap</strong> of a game engine’s terrain: a 2-D array of heights, cheap to store and draw, and famously unable to hold caves or overhangs (those need separate mesh objects). Exact analogy — except that a game heightmap is <em>designed</em>, while a GIS surface is <em>measured</em>, so “terrain or surface, and measured how?” has no equivalent in the game.</p></div>

  <h2><span class="mod">4.6.2</span>Heights need a unit and a zero level</h2>
  <p>A height is a distance <em>above something</em>. Our fixture says “metres above TD-0” — two facts, both needed, neither in the file.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Same shape, different numbers</h3>
    <div class="toggle-row">
      <button class="btn small" data-u="m" aria-pressed="true">Metres above TD-0</button>
      <button class="btn small" data-u="ft">Feet above TD-0</button>
      <button class="btn small" data-u="c">Metres above the contractor’s datum (100 m lower)</button>
    </div>
    <div class="fig-panel">
      <figure class="raster-fig" id="unitFig"></figure>
      <div class="result" id="unitOut"></div>
    </div>
  </div>
  <p><strong>The unit.</strong> A vertical coordinate system “includes a unit of measure … usually feet or meters”. A surface in feet looks identical on screen and is 3.28 times too steep if read as metres. When heights are in a different unit from the horizontal grid, a conversion factor is needed before slope, shading or 3D make sense — ArcGIS Pro’s hillshade calls it the <em>z-factor</em>.</p>
  <p><strong>The zero level (vertical reference).</strong> A vertical coordinate system “defines the origin for height or depth values” and a direction — positive up for heights, positive down for depths. Mean sea level is one common zero; a harbour’s low-water mark is another; the same physical point has different numbers in each. Two elevation rasters with different zero levels cannot be compared cell by cell, any more than Celsius and Fahrenheit readings can.</p>
  <div class="callout note"><span class="label">Defer the detail</span><p>What the zero level physically <em>is</em> — an ellipsoid, a geoid, a tide gauge at Mumbai — and how to convert between them is Chapter 5’s subject. For now the rule is procedural: <strong>a height is incomplete without its unit and its stated zero, and a raster file often carries neither.</strong> ArcGIS Pro lists a raster’s coordinate system in its properties (possibly “undefined”); a vertical coordinate system is set on a map or scene and may be absent. QGIS shows the CRS on the layer’s <em>Information</em> tab. For our fixture all of these read “unknown”; the unit and zero come from the chapter text.</p></div>
  <div class="callout warn"><span class="label">Common mistake</span><p>“Elevation is elevation; the number is the number.” Two surfaces are subtracted to find “ground change” and the result is a uniform 100 m step that is entirely two different zero levels. Unit and reference first, arithmetic second.</p></div>

  <h2><span class="mod">4.6.3</span>A shaded picture is a picture of the numbers, not the numbers</h2>
  <p>The most convincing raster display is the <strong>hillshade</strong>: each cell lit as if by a sun at a chosen direction and height, so slopes facing the light are bright and slopes facing away are dark. ArcGIS Pro’s Hillshade function makes “a grayscale 3D representation of the terrain surface” from an azimuth (default 315°, north-west) and altitude (default 45°); QGIS has <em>Hillshade</em> as a renderer. Go back to the 3D block model above and drag the light around. Three things to hold on to:</p>
  <ol>
    <li><strong>It is derived, not measured.</strong> A hillshade holds no heights. Esri’s page says so: it “does not give absolute elevation values”.</li>
    <li><strong>It is a display choice.</strong> Move the light and a ridge can look like a valley. A z-factor above 1 adds “vertical exaggeration for visual effect” and makes a gentle slope look dramatic.</li>
    <li><strong>It hides nothing and proves nothing.</strong> A surface with a 100 m datum error, a unit mix-up or a bilinear artefact shades just as beautifully as a validated one. <strong>A visually realistic surface is not automatically a validated terrain model.</strong></li>
  </ol>
  <div class="callout idea"><span class="label">Everyday picture</span><p>A hillshade is like a photo of a papier-mâché model of the hills. The photo can be lit dramatically from any side; the model underneath may or may not be built to scale. To check the model you measure it — you do not admire the photo.</p></div>
  <div class="callout warn"><span class="label">Common mistake</span><p>“The 3D view proves the elevation data is good.” A datum offset ships because the scene looked right. The check is numerical: spot-check cell values against an independent measurement, look at the NoData pattern, read the metadata — never the look alone.</p></div>

  <div class="quiz" data-answer="1" data-fb="“You can see the buildings” means the surface includes objects — a DSM. The view cannot establish the unit, the zero level, the accuracy, or whether parts are interpolated or NoData. Before using it, read the unit and vertical reference, and the dataset's own definition (terrain or surface).">
    <div class="q">A colleague sends a beautifully shaded 3D view of a new elevation raster and writes “Ground truth confirmed — you can see the buildings.” What does the sentence actually tell you?</div>
    <div class="opts">
      <button class="opt">The raster is a validated bare-ground model</button>
      <button class="opt">The surface includes objects (a DSM) — and nothing about units, zero level or accuracy</button>
      <button class="opt">The raster is in metres</button>
      <button class="opt">The buildings’ heights are accurate to the cell size</button>
    </div><div class="fb"></div>
  </div>
  <div class="callout note"><span class="label">Comprehension check (write it down)</span><p>For that message: (a) which kind of surface does it imply? (b) two things the view cannot establish about the raster; (c) the first two properties you would read from the dataset before using it.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  /* isometric block model of F6 */
  let showNum = false;
  function hillshade(i, j, azDeg, exag) {
    const g = F6.grid, get = (a, b) => (g[a] && g[a][b] !== undefined && g[a][b] !== ND) ? g[a][b] : g[i][j];
    const dzdx = (get(i, j + 1) - get(i, j - 1)) / 200 * exag, dzdy = (get(i - 1, j) - get(i + 1, j)) / 200 * exag; // y grows upward (row i-1 is north)
    const slope = Math.atan(Math.hypot(dzdx, dzdy)), aspect = Math.atan2(dzdy, -dzdx);
    const zen = (90 - 45) * Math.PI / 180, az = (360 - azDeg + 90) * Math.PI / 180;
    let s = Math.cos(zen) * Math.cos(slope) + Math.sin(zen) * Math.sin(slope) * Math.cos(az - aspect);
    return Math.max(0, Math.min(1, s));
  }
  function iso() {
    const ex = +document.getElementById("ex").value, az = +document.getElementById("az").value;
    document.getElementById("exOut").value = "×" + ex; document.getElementById("azOut").value = az + "°";
    const el = document.getElementById("iso"), a = 70, b = 35, ox = 400, oy = 300, zs = 4.2 * ex;
    const svg = svgEl("svg", { viewBox: "0 0 760 560" });
    // ground grid
    for (let i = 0; i < F6.rows; i++) for (let j = 0; j < F6.cols; j++) {
      const v = F6.grid[i][j];
      const X = k => ox + (j - i + k) * a, Y = k => oy + (j + i + k) * b;
      const pts = (z) => [[X(0), Y(0) - z], [X(1), Y(1) - z], [X(0), Y(2) - z], [X(-1), Y(1) - z]];
      if (v === ND) { svgEl("polygon", { points: pts(0).map(p => p.join(",")).join(" "), fill: "url(#hatch)", stroke: "#b9432e" }, svg); continue; }
      const z = v * zs, top = pts(z), base = pts(0);
      const sh = hillshade(i, j, az, ex);
      const shade = t => `hsl(28 40% ${Math.round(25 + 55 * t)}%)`;
      svgEl("polygon", { points: [base[1], base[2], top[2], top[1]].map(p => p.join(",")).join(" "), fill: shade(.25), stroke: "#1f2a44", "stroke-width": .8 }, svg); // right side
      svgEl("polygon", { points: [base[2], base[3], top[3], top[2]].map(p => p.join(",")).join(" "), fill: shade(.45), stroke: "#1f2a44", "stroke-width": .8 }, svg); // left side
      svgEl("polygon", { points: top.map(p => p.join(",")).join(" "), fill: shade(sh), stroke: "#1f2a44", "stroke-width": .8 }, svg);
      if (showNum) { const t = svgEl("text", { x: X(0), y: Y(1) - z + 6, class: "cellval", "font-size": "17" }, svg); t.textContent = v.toFixed(1); }
    }
    const d = svgEl("defs", {}, svg); const pat = svgEl("pattern", { id: "hatch", width: "8", height: "8", patternUnits: "userSpaceOnUse", patternTransform: "rotate(45)" }, d);
    svgEl("rect", { width: "8", height: "8", fill: "#fff" }, pat); svgEl("rect", { width: "3", height: "8", fill: "#b9432e" }, pat);
    let t = svgEl("text", { x: 20, y: 30, class: "axis title" }, svg); t.textContent = `Schematic · made-up heights, metres above TD-0 · exaggeration ×${ex}`;
    t = svgEl("text", { x: 20, y: 56, class: "axis" }, svg); t.textContent = `Light from azimuth ${az}° (0 = north, 90 = east). Row 1 is at the back (north).`;
    // z axis
    svgEl("line", { x1: 60, y1: 520, x2: 60, y2: 520 - 21 * zs, stroke: "#1f2a44", "stroke-width": 2 }, svg);
    [0, 10, 20].forEach(h => { svgEl("line", { x1: 54, y1: 520 - h * zs, x2: 66, y2: 520 - h * zs, stroke: "#1f2a44" }, svg); const l = svgEl("text", { x: 72, y: 524 - h * zs, class: "axis" }, svg); l.textContent = h + " m"; });
    el.innerHTML = ""; el.appendChild(svg);
    document.getElementById("isoOut").innerHTML = `Exaggeration <strong>×${ex}</strong>, light from <strong>${az}°</strong>. Cell values: still 12, 14, 17, 21 / 11, 13, 15, 18 / 10, 11, 13, 15 / 9, NoData, 12, 13. <strong>15 valid cells, mean 13.6 m</strong> — unchanged by anything you do here.`;
  }
  document.getElementById("ex").oninput = iso; document.getElementById("az").oninput = iso;
  document.getElementById("numTog").onclick = () => { showNum = !showNum; iso(); };
  iso();

  /* cross-section DTM / DSM */
  function xsec(kind) {
    const el = document.getElementById("xsec"), svg = svgEl("svg", { viewBox: "0 0 760 260" });
    // ground profile
    const ground = "M 20 200 L 120 190 L 220 180 L 320 175 L 420 160 L 520 150 L 620 140 L 740 130";
    svgEl("path", { d: ground + " L 740 250 L 20 250 Z", fill: "#e9dcc3", stroke: "none" }, svg);
    // building on ground between 300-400 (ground ~177) and tree at 560
    svgEl("rect", { x: 300, y: 110, width: 100, height: 67, fill: "#c9c9c9", stroke: "#1f2a44" }, svg);
    svgEl("rect", { x: 555, y: 120, width: 10, height: 27, fill: "#7a5c2e" }, svg); svgEl("circle", { cx: 560, cy: 105, r: 28, fill: "#4fa36b", stroke: "#1f2a44" }, svg);
    let t = svgEl("text", { x: 310, y: 100, class: "axis" }, svg); t.textContent = "depot";
    t = svgEl("text", { x: 600, y: 90, class: "axis" }, svg); t.textContent = "tree";
    const dtm = ground, dsm = "M 20 200 L 120 190 L 220 180 L 300 177 L 300 110 L 400 110 L 400 172 L 420 160 L 520 150 L 532 130 L 545 85 L 575 80 L 588 125 L 620 140 L 740 130";
    svgEl("path", { d: kind === "dtm" ? dtm : dsm, fill: "none", stroke: "#d3541f", "stroke-width": 5, "stroke-linejoin": "round" }, svg);
    t = svgEl("text", { x: 20, y: 30, class: "axis title", "font-size": "16" }, svg); t.textContent = kind === "dtm" ? "DTM — the orange line follows the bare ground" : "DSM — the orange line goes over roof and canopy";
    el.innerHTML = ""; el.appendChild(svg);
    document.getElementById("xsOut").innerHTML = kind === "dtm" ? "Cell values at the depot are the <strong>ground height</strong>. Flood and drainage models need this one." : "Cell values at the depot are the <strong>roof height</strong>; at the tree, the canopy. Line-of-sight and building-height work need this one. Run a flood model on it and every building becomes a hill.";
    ["dtmBtn", "dsmBtn"].forEach(x => document.getElementById(x).setAttribute("aria-pressed", x === kind + "Btn"));
  }
  document.getElementById("dtmBtn").onclick = () => xsec("dtm"); document.getElementById("dsmBtn").onclick = () => xsec("dsm"); xsec("dtm");

  /* unit / datum demo */
  function unit(u) {
    const f = u === "m" ? (v => v) : u === "ft" ? (v => Math.round(v * 3.2808 * 10) / 10) : (v => v + 100);
    const g = Object.assign({}, F6, { grid: F6.grid.map(r => r.map(v => v === ND ? ND : f(v))) });
    renderRaster(document.getElementById("unitFig"), g, { renderer: "ramp", rowLabels: true, cellPx: 80, valueFormat: v => fmt(v, 1), caption: u === "m" ? "Metres above TD-0 (the fixture)." : u === "ft" ? "The same ground in feet. The colours are identical." : "The same ground measured from a zero level 100 m lower. Identical colours again." });
    document.getElementById("unitOut").innerHTML = u === "m" ? "Highest cell: <strong>21.0 m</strong> above TD-0." : u === "ft" ? "Highest cell: <strong>68.9 ft</strong>. Read this as metres by mistake and the hill is 3.28 times too steep — and the picture gives no hint." : "Highest cell: <strong>121.0</strong>. A contractor's survey with this zero level looks like “the ground is 100 m higher”. It is not — until the zero levels are reconciled, the two grids cannot be compared or subtracted.";
    document.querySelectorAll("[data-u]").forEach(b => b.setAttribute("aria-pressed", b.dataset.u === u));
  }
  document.querySelectorAll("[data-u]").forEach(b => b.onclick = () => unit(b.dataset.u)); unit("m");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
