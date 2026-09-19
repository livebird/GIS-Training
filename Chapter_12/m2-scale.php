<?php $page = ['title' => '12.2 Scale, detail, resolution, accuracy', 'chapter' => 12, 'module' => '12.2']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 12.2 · General GIS idea</div>
    <h1>Scale is a fraction — and it is not accuracy</h1>
    <p class="lead">“1:25,000” is a fraction: one unit on the map is 25,000 of the same units on the ground. Four numbers in every GIS project <em>sound</em> like accuracy — map scale, raster cell size, decimal places, and real positional accuracy. Only one of them tells you how close a drawn point is to the truth.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Read and check a scale such as 1:1,000 or 1:100,000 with simple arithmetic.</li>
      <li>Use “large scale” and “small scale” the way map people use them (it is the opposite of everyday speech).</li>
      <li>Keep scale, cell size, coordinate precision and positional accuracy apart — and explain why zooming in adds no evidence.</li></ul></div>
  </div>

  <h2><span class="mod">12.2.1</span>The representative fraction</h2>
  <p>A map scale of <strong>1:25,000</strong> means: 1 cm on the paper is 25,000 cm on the ground, which is 250 m. The QGIS introduction says it in one line — “any distance on the map is 1/25,000th of the real distance” — and calls 25,000 the <strong>scale denominator</strong>. ArcGIS Pro defines scale the same way: “a ratio between measurements on a map view and measurements in the real world”.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Scale calculator</h3>
    <p>Type a ground distance and a paper distance, or pick a scale, and read the others.</p>
    <div class="controls">
      <label>Scale 1 : <input type="number" id="den" value="25000" min="100" step="100" style="width:110px"></label>
      <label>1 cm on the map = <strong id="cmOut">250 m</strong></label>
      <label>A 20 cm × 20 cm map covers <strong id="areaOut">5 km × 5 km</strong></label>
    </div>
    <div class="tiles" id="scaleTiles"></div>
  </div>
  <div class="table-wrap"><table>
    <thead><tr><th>Scale</th><th>1 cm on paper =</th><th>20 cm × 20 cm covers</th><th>What fits</th></tr></thead>
    <tbody>
      <tr><td class="mono">1:1,000</td><td>10 m</td><td>200 m × 200 m (0.04 km²)</td><td>Individual lamps, drains, house outlines</td></tr>
      <tr><td class="mono">1:25,000</td><td>250 m</td><td>5 km × 5 km</td><td>All of District G-12 on an A4 page — roads as lines, not to width</td></tr>
      <tr><td class="mono">1:100,000</td><td>1 km</td><td>20 km × 20 km (400 km²)</td><td>Whole city; a ward is a few millimetres</td></tr>
    </tbody></table></div>
  <p>So for the same sheet of paper, <strong>1:1,000 shows far more local detail than 1:100,000</strong> — ten thousand times less ground on the page, so there is room for every lamp.</p>

  <h3>Large scale = zoomed in (the words that trip everyone)</h3>
  <p>The scale is the <em>fraction</em>. 1/1,000 is a <strong>bigger number</strong> than 1/100,000. So:</p>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">Large-scale map</h4><p>Large fraction → <strong>small denominator</strong> → small ground area → <strong>much detail</strong>. Example: 1:1,000, a street plan of one colony.</p></div>
    <div class="card"><h4 style="margin-top:0">Small-scale map</h4><p>Small fraction → <strong>large denominator</strong> → large ground area → little detail. Example: 1:10,000,000, a map of all India on one page.</p></div>
  </div>
  <p>The QGIS page puts it with an exclamation mark: “a small scale map covers a large area, and a large scale map covers a small area!” A memory trick: <em>scale is a zoom factor written as a fraction — “large scale” means zoomed in.</em> If there is any doubt in a meeting, say “large area” or “1:25,000”, never just “large scale”.</p>
  <div class="callout dev"><span class="label">Developer view</span><p>Web maps count zoom levels <em>up</em> as you zoom in (zoom 18 = a street, zoom 3 = a continent). Map scale does the same job as a fraction, so the number you see gets <em>smaller</em> as you zoom in (1:1,000 at the street, 1:50,000,000 at the continent). <strong>Where it stops:</strong> a zoom level is a fixed tile scheme; a scale denominator also depends on the projection and the latitude (Chapter 6). Two web maps at “zoom 12” share tiles; two maps “at 1:25,000” in different projections can differ in true ground scale across the page.</p></div>

  <div class="quiz" data-answer="1" data-fb="4,000 m ÷ 0.08 m = 50,000, so 1:50,000 — a larger denominator than 25,000, so a smaller-scale map with less detail. A 12 m road would be 12 ÷ 50,000 = 0.24 mm: thinner than any printed line, so it is drawn as a symbol.">
    <div class="q">District G-12 (4 km wide) is printed 8 cm wide. What is the scale, and is it larger- or smaller-scale than 1:25,000?</div>
    <div class="opts"><button class="opt">1:5,000 — larger scale, more detail</button><button class="opt">1:50,000 — smaller scale, less detail</button><button class="opt">1:50,000 — larger scale, because 50,000 is bigger</button><button class="opt">1:500 — smaller scale</button></div><div class="fb"></div>
  </div>

  <h2><span class="mod">12.2.2</span>Four numbers that are not each other</h2>
  <p>Chapters 4, 5 and 9 introduced these one at a time. Here they are side by side, with the values used in this course.</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Quantity</th><th>What it describes</th><th>Belongs to</th><th>Course example</th><th>Tells you how close a drawn point is to the truth?</th></tr></thead>
    <tbody>
      <tr><td><strong>Map scale</strong></td><td>Map distance ÷ ground distance in one <em>drawing</em></td><td>The map, not the data</td><td>1:25,000 for the A4 print of G-12</td><td class="no">No — any data can be drawn at any scale</td></tr>
      <tr><td><strong>Raster cell size</strong> (Ch. 4)</td><td>Ground size of one cell; the smallest thing a raster can show</td><td>The raster file</td><td>100 m cells in the practice elevation grid</td><td class="no">No — “cell size is not positional accuracy”; a 1 m grid can be 50 m out of place</td></tr>
      <tr><td><strong>Coordinate precision</strong> (Ch. 5)</td><td>How many digits a coordinate is <em>written</em> with</td><td>The number in the record</td><td>4 decimal places of a degree ≈ 11 m</td><td class="no">No — extra digits are free; extra truth is not</td></tr>
      <tr><td><strong>Positional accuracy</strong> (Ch. 9)</td><td>How close the recorded position is to the real position</td><td>The <em>capture method</em>: phone, survey instrument, address match</td><td>Phone-reported requests: about ± 15 m</td><td class="yes"><strong>Yes — the only one that does</strong></td></tr>
    </tbody></table></div>
  <div class="callout dev"><span class="label">Developer view</span><p>Showing a number in 48-point type (scale) does not change it. Storing it as <code>DECIMAL(12,6)</code> (precision) does not make the sensor better. The sensor’s error (accuracy) belongs to the sensor. Cell size is like a sampling interval: a temperature logged every 10 minutes cannot show a 30-second spike. <strong>Where it stops:</strong> in software these four live in four obvious places (CSS, schema, sensor spec, sampling config). In GIS they are often not written down anywhere, and a map shows none of them — which is why 12.6 asks you to write them <em>on</em> the map.</p></div>

  <h2><span class="mod">12.2.3</span>Zooming in adds nothing; decimals add nothing</h2>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Zoom in on request G06 — does it become more accurate?</h3>
    <p>G06 (a fallen tree) is stored at exactly x = 2000 — the shared edge of W06 and W07 — and was reported from a phone with about ± 15 m accuracy. The dashed circle is that ± 15 m. Zoom in and watch what the symbol does and what the circle does.</p>
    <div class="controls">
      <label>Scale 1 : <input type="range" id="zoom" min="0" max="4" value="0" step="1"> <strong id="zoomLbl">25,000</strong></label>
    </div>
    <figure class="map-fig" id="zoomFig"></figure>
    <div class="result" id="zoomOut"></div>
  </div>
  <p>At 1:200 the dot sits crisply on one side of the line. The renderer <em>invented</em> that side: the evidence behind the dot is the same ± 15 m blob it was at 1:25,000. Zooming increases the <strong>scale</strong>; it cannot increase <strong>accuracy</strong>, which was fixed when the request was captured. Adding decimal places does the same thing with numbers: <code>2000.000000</code> is not a better observation than <code>2000</code>. Which ward G06 belongs to is decided by the <em>coordinates and the written rule</em> (Policy W-1 says: on the edge → the lower ward code, W06), not by the drawing at any zoom.</p>

  <h3>Generalisation, and showing layers only at some scales</h3>
  <p>Because a 1:25,000 map cannot show a 10 m road at its true width, or two lamps 11 m apart as separate dots, the map maker <em>simplifies</em>: roads become lines of fixed width, close points become one symbol, small bends are smoothed. This is <strong>generalisation</strong>. It is not an error; <em>undisclosed</em> generalisation is.</p>
  <p>Interactive maps handle the phone-versus-street problem by showing a layer only within a <strong>scale range</strong>. ArcGIS Pro’s help says the purpose plainly: “By setting a visible scale range for a layer, you automatically limit its visibility to suitable scales only” — you would not draw building footprints on a map of Europe, nor generalised climate zones on a neighbourhood map. For the crew map: request labels appear only when zoomed in past about 1:5,000; ward outlines always. The <em>default</em> view must still be complete: a reader who never zooms must not miss a whole category.</p>
  <div class="callout note"><span class="label">Platform note</span><p>Every platform has this — QGIS “scale dependent visibility”, OpenLayers and Mapbox layer min/max zoom, GeoServer style rules with scale denominators — but the <em>numbers</em> are not interchangeable. A web zoom level is a tile scheme; an ArcGIS Pro scale range is a denominator. Record the setting per platform when you move a map; never assume “zoom 14” and “1:35,000” mean the same thing.</p></div>

  <div class="callout warn"><span class="label">Common mistake</span><p><em>“The layer draws crisply at 1:500, so it must be accurate to a metre.”</em> Crispness is a property of the renderer. A report that gives lamp positions to the millimetre from a phone capture is a red flag to an informed reader, not a sign of quality.</p></div>

  <div class="quiz" data-answer="3" data-fb="(a) precision — no; (b) cell size — no, resampling to smaller cells invents no new measurements; (c) map scale — no; (d) positional accuracy — yes, the only one.">
    <div class="q">Which one of these actually makes a drawn position more trustworthy?</div>
    <div class="opts"><button class="opt">(a) “We re-exported the file with nine decimal places.”</button><button class="opt">(b) “The elevation raster was resampled from 30 m to 10 m cells.”</button><button class="opt">(c) “The print was enlarged from A4 to A3.”</button><button class="opt">(d) “The points were re-captured with a survey-grade instrument.”</button></div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const den = document.getElementById("den"), tiles = document.getElementById("scaleTiles");
  function sc() {
    const d = Math.max(1, +den.value || 1);
    document.getElementById("cmOut").textContent = d / 100 >= 1000 ? fmtN(d / 100000, 2) + " km" : fmtN(d / 100, 1) + " m";
    const side = 0.2 * d; document.getElementById("areaOut").textContent = side >= 1000 ? fmtN(side / 1000, 1) + " km × " + fmtN(side / 1000, 1) + " km" : fmtN(side) + " m × " + fmtN(side) + " m";
    const big = d <= 5000 ? "large-scale (zoomed in, much detail)" : d <= 50000 ? "medium — a town or district on a page" : "small-scale (zoomed out, little detail)";
    tiles.innerHTML = `<div class="tile"><div class="k">This is a</div><div class="v" style="font-size:1.1rem">${big}</div><div class="s">the fraction 1/${fmtN(d)}</div></div>
      <div class="tile"><div class="k">A 10 m road, to scale</div><div class="v">${(10000 / d).toFixed(2)} mm</div><div class="s">${10000 / d < 0.5 ? "too thin to print — use a symbol" : "can be drawn to width"}</div></div>
      <div class="tile"><div class="k">A 12-point symbol covers</div><div class="v">${fmtN(footprintM(12, d), 1)} m</div><div class="s">on the ground</div></div>`;
  }
  den.addEventListener("input", sc); sc();

  const scales = [25000, 5000, 2000, 500, 200], zoom = document.getElementById("zoom"), fig = document.getElementById("zoomFig"), out = document.getElementById("zoomOut");
  function draw() {
    const s = scales[+zoom.value]; document.getElementById("zoomLbl").textContent = fmtN(s);
    const half = s * 0.08;                                            // show a window 160 mm wide on paper, in ground metres
    const cx = 2000, cy = 2400, x0 = cx - half, x1 = cx + half, y0 = cy - half * .6, y1 = cy + half * .6;
    const svg = newSvg(fig, `${x0} ${-y1} ${x1 - x0} ${y1 - y0}`, "Request G06 on the W06/W07 edge at increasing zoom, with its ± 15 m accuracy circle.");
    const sw = half / 200;
    mkEl("rect", { x: x0, y: -y1, width: cx - x0, height: y1 - y0, fill: "#f4f7fb" }, svg);
    mkEl("rect", { x: cx, y: -y1, width: x1 - cx, height: y1 - y0, fill: "#fff" }, svg);
    mkEl("line", { x1: cx, y1: -y1, x2: cx, y2: -y0, stroke: "#1f2a44", "stroke-width": sw * 2 }, svg);
    if (half > 400) { mkEl("line", { x1: x0, y1: -2500, x2: x1, y2: -2500, stroke: "#7a5c2e", "stroke-width": sw * 4 }, svg); }
    mkEl("circle", { cx, cy: -cy, r: 15, class: "acc-circle", style: `stroke-width:${sw}px;stroke-dasharray:${sw * 4} ${sw * 3}` }, svg);
    const r = footprintM(12, s) / 2;
    marker(svg, "diamond", cx, -cy, r, { fill: "#8a6a12", stroke: "#1f2a44", "stroke-width": sw });
    const fs = half / 12;
    txt(svg, x0 + half * .05, -y1 + fs * 1.3, "W06", "lbl", { style: `font-size:${fs}px` }); txt(svg, cx + half * .05, -y1 + fs * 1.3, "W07", "lbl", { style: `font-size:${fs}px` });
    txt(svg, x0 + half * .05, -y0 - fs * .4, `window ≈ ${fmtN(2 * half)} m wide · symbol covers ${fmtN(footprintM(12, s), 1)} m · dashed circle = ± 15 m`, "lbl", { style: `font-size:${fs * .45}px` });
    out.innerHTML = s >= 5000 ? "At this scale the symbol is <strong>wider than the accuracy circle</strong>: the symbol is a label of a place, not a picture of it. Nobody can read which side of the line the tree is on — and that is honest."
      : s >= 500 ? "Now the symbol is smaller than the circle. The tree could be anywhere inside the dashed circle — on either side of the line. The drawing shows a dot on the line; the truth is a 30 m-wide blob."
      : "At 1:200 the dot looks precise and sits exactly on the line. Nothing improved: the circle is still 30 m wide. Which ward gets G06 is decided by the written rule (Policy W-1 → W06), not by this picture.";
  }
  zoom.addEventListener("input", draw); draw();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
