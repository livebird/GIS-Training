<?php $page = ['title' => '4.2 Interpret cell values and bands', 'chapter' => 4, 'module' => '4.2']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 4.2 · General GIS principle · with platform notes</div>
    <h1>Values, colours and bands</h1>
    <p class="lead">The software paints every raster in colours the moment you open it. The colours look like a finished map. They are not. <strong>The colour is a rule the layer applies; the value is what the file stores.</strong> This module teaches you to always ask for the number, the band and the dictionary.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>See the same cell painted four different ways without one number changing.</li>
      <li>Understand one band (elevation) versus many bands (a colour photo).</li>
      <li>List what you must know before a value means anything — and why you never guess units from the look of a map.</li></ul></div>
  </div>

  <h2><span class="mod">4.2.1</span>Stored values are not display colours</h2>
  <p>Open the land-cover grid in any GIS and it appears as coloured squares. Which colours? A default rule decides — ArcGIS Pro, for example, gives <em>random</em> colours to a single-band raster with 25 or fewer different values; QGIS shows it in grey. Neither rule knows that 0 means water. The colour belongs to the <strong>layer</strong> (Chapter 3); the value belongs to the <strong>dataset</strong>.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <p>Paint the same grid four ways. Watch the cell at <strong>row 1, column 8</strong> (its value is 0 = water). Then check: did any number change?</p>
    <div class="toggle-row">
      <button class="btn small" data-r="unique" aria-pressed="true">Class colours (unique values)</button>
      <button class="btn small" data-r="grey">Grey stretch (low = black, high = white)</button>
      <button class="btn small" data-r="random">Random default colours</button>
      <button class="btn small" data-r="mistake">Mistake: “NoData = 0”</button>
      <button class="btn small ghost" id="valToggle">Show / hide numbers</button>
    </div>
    <div class="fig-panel">
      <figure class="raster-fig" id="f5paint"></figure>
      <div class="layerlist">
        <div class="legend" id="leg"></div>
        <div class="result" id="paintOut"></div>
      </div>
    </div>
  </div>
  <p>So <strong>a colour on screen is evidence of nothing until you know the rule that produced it</strong>. A blue cell may be:</p>
  <div class="table-wrap"><table>
    <thead><tr><th>The blue cell could be…</th><th>Because…</th><th>How to find out</th></tr></thead>
    <tbody>
      <tr><td>a <strong>category</strong> (code 0 = water)</td><td>a unique-values rule assigned blue to code 0</td><td>read the legend <em>and</em> the dictionary</td></tr>
      <tr><td>a <strong>range of a continuous value</strong> (heights 9–12 m)</td><td>a stretched or classified rule mapped the low end to blue</td><td>read the class breaks and the layer’s unit</td></tr>
      <tr><td><strong>NoData painted blue</strong></td><td>the layer was told to show NoData in a colour instead of transparent</td><td>check the NoData display setting</td></tr>
      <tr><td><strong>nothing at all</strong> — a see-through cell showing a blue layer underneath</td><td>the cell is NoData or was made transparent, and the layer beneath happens to be blue</td><td>switch off the layer underneath</td></tr>
    </tbody></table></div>
  <div class="callout note"><span class="label">Platform note</span><p><strong>ArcGIS Pro</strong> calls the colour rules <em>Stretch</em> (values along a colour ramp), <em>Classify</em>, <em>Unique Values</em> (“appropriate for qualitative data such as land cover”), <em>Discrete</em>, <em>Colormap</em> and <em>RGB</em>. <strong>QGIS</strong> calls them <em>Singleband gray</em>, <em>Singleband pseudocolor</em> (a continuous palette, “e.g. an elevation map”), <em>Paletted/Unique values</em>, <em>Multiband color</em>, <em>Hillshade</em>. Different names, same two families: <strong>one colour per value</strong> for categories, <strong>a ramp</strong> for continuous values. Both let you paint NoData in a colour or leave it transparent.</p></div>
  <div class="callout dev"><span class="label">Developer view</span><p>The value-to-colour step is a stylesheet: <code>.water { fill: blue }</code> is styling, not data. <strong>Where the comparison stops:</strong> a web page with no stylesheet still shows readable text; a raster with a default colour rule shows colours that <em>look</em> meaningful and are not.</p></div>
  <div class="callout warn"><span class="label">Common mistake</span><p>“The legend shows me what the values are.” A legend shows the <em>mapping</em> the layer applies. The value and its dictionary are separate facts. Reading a random-colour default legend as if it were a classification is how “the north-east is blue, so it is water” ends up in a report about an elevation grid.</p></div>

  <h2><span class="mod">4.2.2</span>One band and many bands</h2>
  <p>So far each cell held one number. A <strong>band</strong> is one complete grid of values. A raster with several bands holds several grids covering exactly the same cells — one number per cell <em>per band</em>.</p>
  <div class="grid-2">
    <div class="card">
      <h4 style="margin-top:0">One band: elevation</h4>
      <p>Our elevation grid is single-band: one height per cell. A digital elevation model is the standard example — each cell “contains only one value representing surface elevation”. A one-band raster is shown as grey levels or through a colour ramp.</p>
    </div>
    <div class="card">
      <h4 style="margin-top:0">Many bands: a photo</h4>
      <p>A colour photo is stored as three bands — red, green and blue — that the screen mixes. Satellites record more: bands the eye cannot see, such as <strong>near-infrared</strong> (useful for spotting water and healthy crops). Esri’s docs note that Landsat-9 imagery has 11 bands. The number of bands is the image’s <em>spectral resolution</em>.</p>
    </div>
  </div>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>A tiny three-band image (made-up)</h3>
    <p>Four cells, three bands. Click a band to see its grid of numbers; click <strong>Mixed on screen</strong> to see the colour the screen makes from all three. “The value of the cell” means nothing until you say which band.</p>
    <div class="tabs band-tabs" role="tablist">
      <button role="tab">Band 1: Red</button><button role="tab">Band 2: Green</button><button role="tab">Band 3: Blue</button><button role="tab">Mixed on screen</button>
    </div>
    <div class="tabpanel"><figure class="raster-fig" id="bR"></figure></div>
    <div class="tabpanel"><figure class="raster-fig" id="bG"></figure></div>
    <div class="tabpanel"><figure class="raster-fig" id="bB"></figure></div>
    <div class="tabpanel"><figure class="raster-fig" id="bRGB"></figure>
      <table class="mini-table"><thead><tr><th>Cell</th><th>Red</th><th>Green</th><th>Blue</th><th>Looks like</th></tr></thead><tbody id="rgbTab"></tbody></table></div>
    <p class="small">Both platforms let you choose <em>which</em> band feeds the screen’s red, green and blue. Put the near-infrared band into red and healthy vegetation shows up red — the “colour infrared” view. For this course you only need to read metadata such as “4 bands: Blue, Green, Red, NIR”: NIR is near-infrared, band order is a fact about the file, and every band has the same rows, columns, cell size and extent. Interpreting reflectance is remote-sensing science and outside Phase 1.</p>
  </div>
  <div class="callout warn"><span class="label">Common mistake</span><p>“A raster has <em>a</em> value per cell.” Code that reads band 1 of a three-band image and reports “the value is 34” for a cell that is really (34, 120, 200). Always state the band.</p></div>

  <h2><span class="mod">4.2.3</span>What you need before a value means anything</h2>
  <p>A value is a number. To make it a <strong>fact about the ground</strong> you always need two answers: <em>what kind of quantity is this</em>, and <em>in which unit or dictionary</em>?</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Raster</th><th>Value seen</th><th>Needed to read it</th><th>Where it should be</th><th>If missing</th></tr></thead>
    <tbody>
      <tr><td>Land cover</td><td>2</td><td>The category dictionary (0 water, 1 built-up…)</td><td>Dataset documentation; a raster attribute table; a colour map</td><td>2 is just an integer. Do not guess that it “looks like” vegetation.</td></tr>
      <tr><td>Elevation</td><td>13.0</td><td>The unit (metres) and the zero level (TD-0)</td><td>Documentation; the vertical coordinate system, if defined (4.6)</td><td>13.0 could be metres or feet; a hill “looks” the same in both.</td></tr>
      <tr><td>3-band photo</td><td>(34, 120, 200)</td><td>Which band is which; the value range</td><td>Band metadata; the sensor’s documentation</td><td>You can display it but measure nothing.</td></tr>
      <tr><td>Rainfall grid</td><td>0</td><td>Whether 0 means “measured, no rain” or “not measured”</td><td>The NoData definition (4.4)</td><td>The statistic will be wrong.</td></tr>
    </tbody></table></div>
  <div class="callout warn"><span class="label">Rule</span><p><strong>Never infer elevation units from appearance.</strong> A surface in metres and the same surface in feet look identical on screen; only the numbers differ (by about 3.28×). The same for categories: a random colour tells you nothing about the class.</p></div>
  <div class="callout note"><span class="label">Platform note — the storage type is visible, the meaning is not</span><p>ArcGIS Pro’s raster properties show the <em>pixel type</em> (signed/unsigned, integer/floating point), bit depth, NoData value and whether a colour map exists. QGIS’s <em>Information</em> tab shows the data type, band statistics and NoData values. That tells you <em>how</em> the numbers are stored. A useful hint — integers are “best used to represent categorical data” and floating point suits continuous surfaces — but a hint is not a dictionary. Meaning comes from documentation (Chapter 7 calls it metadata).</p></div>
  <div class="try">
    <span class="tag">Worked example</span>
    <h3>“Find the highest point”</h3>
    <p>A colleague WhatsApps you <code>elevation_training.asc</code> with no note. Click the highest cell.</p>
    <div class="fig-panel">
      <figure class="raster-fig" id="f6hi"></figure>
      <div class="result" id="hiOut">Click the cell you think is highest.</div>
    </div>
  </div>

  <div class="quiz" data-answer="2" data-fb="The legend shows the colour rule's range, not the meaning. 21 could be 21 metres, 21 feet, a category code drawn with a ramp by mistake, a count, a temperature. Only the dataset's documented unit or dictionary for that band settles it.">
    <div class="q">A single-band raster shows a red-to-green ramp and the legend reads “9 … 21”. What settles what the value 21 means?</div>
    <div class="opts">
      <button class="opt">The colour — green is high ground</button>
      <button class="opt">The pixel type — floating point means metres</button>
      <button class="opt">The dataset’s documented unit or dictionary for that band</button>
      <button class="opt">The software’s default rule</button>
    </div><div class="fb"></div>
  </div>
  <div class="callout note"><span class="label">Comprehension check (write it down)</span><p>List three different things the value 21 could be, and state the single piece of information that would settle it.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  let r = "unique", showVals = true;
  const ext = statsOf(F5.grid);
  function paint() {
    const isMistake = r === "mistake";
    const g = isMistake ? Object.assign({}, F5, { grid: F5.grid.map(row => row.map(v => v === 0 ? ND : v)) }) : F5;
    renderRaster(document.getElementById("f5paint"), g, { renderer: isMistake ? "unique" : r, showValues: showVals, cellPx: 44, nodata: isMistake ? "transparent" : "hatch", selected: [0, 7],
      caption: isMistake ? "Someone set the layer's NoData value to 0. The pond becomes transparent — but the file still holds 0 in those cells." : "Same file, different colour rule. The outlined cell is row 1, column 8 (value 0)." });
    document.getElementById("leg").innerHTML = legendFor(F5, isMistake ? "unique" : r, ext);
    const msg = { unique: "Row 1, col 8 is <strong>blue = water</strong>, because the rule says 0 → blue and the dictionary says 0 = water.",
      grey: "Row 1, col 8 is <strong>black</strong>, because 0 is the lowest value and the rule paints the lowest value black. Nothing about water is visible.",
      random: "Row 1, col 8 got whatever colour the software picked for the value 0. The colour carries no meaning.",
      mistake: "Row 1, col 8 <strong>disappeared</strong>. The layer treats 0 as ‘no data’, so it paints nothing there. The file is unchanged — it still says 0." }[r];
    document.getElementById("paintOut").innerHTML = msg + "<br><span class='small'>Four appearances, one stored value: <strong>0</strong>.</span>";
  }
  document.querySelectorAll("[data-r]").forEach(b => b.onclick = () => { r = b.dataset.r; document.querySelectorAll("[data-r]").forEach(x => x.setAttribute("aria-pressed", x === b)); paint(); });
  document.getElementById("valToggle").onclick = () => { showVals = !showVals; paint(); };
  paint();

  // 3-band demo
  const R = [[200, 34], [40, 250]], G = [[60, 120], [160, 250]], B = [[40, 200], [50, 250]];
  const mk = g => ({ cols: 2, rows: 2, cell: 1, x0: 0, y0: 0, grid: g });
  renderRaster(document.getElementById("bR"), mk(R), { renderer: "grey", rowLabels: true, cellPx: 110, caption: "Band 1 (red) — one number per cell, 0–255. Shown as grey levels." });
  renderRaster(document.getElementById("bG"), mk(G), { renderer: "grey", rowLabels: true, cellPx: 110, caption: "Band 2 (green) — a different grid of numbers for the same four cells." });
  renderRaster(document.getElementById("bB"), mk(B), { renderer: "grey", rowLabels: true, cellPx: 110, caption: "Band 3 (blue)." });
  const rgb = mk([[0, 1], [2, 3]]);
  const svg = renderRaster(document.getElementById("bRGB"), rgb, { renderer: "none", rowLabels: true, cellPx: 110, showValues: false, caption: "Screen mixes the three bands. Each cell has THREE values, not one." });
  svg.querySelectorAll("rect.cell").forEach(c => { const i = +c.dataset.r, j = +c.dataset.c; c.setAttribute("fill", `rgb(${R[i][j]},${G[i][j]},${B[i][j]})`); });
  const names = ["reddish-brown", "blue", "green", "near-white"];
  document.getElementById("rgbTab").innerHTML = [[0, 0], [0, 1], [1, 0], [1, 1]].map(([i, j], k) => `<tr><td>row ${i + 1}, col ${j + 1}</td><td>${R[i][j]}</td><td>${G[i][j]}</td><td>${B[i][j]}</td><td><span class="pix" style="background:rgb(${R[i][j]},${G[i][j]},${B[i][j]})"></span> ${names[k]}</td></tr>`).join("");

  // highest point
  let hs = null;
  const drawHi = () => renderRaster(document.getElementById("f6hi"), F6, { renderer: "ramp", rowLabels: true, cellPx: 80, selected: hs, onCell: (i, j, v) => { hs = [i, j]; drawHi();
    document.getElementById("hiOut").innerHTML = (i === 0 && j === 3) ? "<strong>Right: 21.0 at row 1, column 4</strong> (x 300–400, y 900–1000). But: 21.0 <em>what</em>? The file has no unit and no zero level. In this course’s fixture it is metres above TD-0 — that came from the documentation, not the file. And it is the highest of sixteen <em>cell</em> values, each standing for a 100 m square; anything inside the cell was never measured." : (v === ND ? "That cell is NoData — nothing is known there." : `That cell holds ${v}. The largest valid value is 21.0 — keep looking.`); },
    caption: "F6 painted with a colour ramp from 9 (blue) to 21 (orange). The ramp is a display choice." });
  drawHi();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
