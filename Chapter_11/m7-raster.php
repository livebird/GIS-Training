<?php $page = ['title' => '11.7 A small raster calculation', 'chapter' => 11, 'module' => '11.7']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 11.7 · General idea, with ArcGIS Pro and QGIS wording</div>
    <h1>A 3 × 3 grid, one missing cell, three different “means”</h1>
    <p class="lead">Chapter 4 said it: <strong>NoData</strong> means “nobody knows”, and zero is a real value. This module shows what that costs when you calculate. Then it builds a simple <em>mask</em> — cells at or below a height — and explains why that mask is not a flood map. Finally, four checks before you ever combine two rasters.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Compute the mean of a tiny grid three ways and see which one is honest.</li>
      <li>Build a threshold mask with units and a datum, and keep the unknown cell unknown.</li>
      <li>Check extent, cell size, alignment and missing data before combining rasters.</li></ul></div>
  </div>

  <h2><span class="mod">11.7.1</span>The mean of grid_a3 — exclude NoData, or pretend it is zero?</h2>
  <p>Esri’s NoData page puts it plainly: “NoData means that not enough information is known about a cell location to assign it a value” and “NoData and 0 are not the same—0 is a valid numerical value.” A tool can either “Always return NoData for that specified cell location” or “Ignore the NoData and compute with the available values” — and “The behavior of NoData is addressed for each tool in its respective tool reference documentation.” That last sentence is the whole lesson: <strong>the policy is chosen per tool, so you must read which one applies.</strong></p>
  <p>Our practice grid <code>grid_a3.asc</code>: 3 × 3 cells of 100 m, placed at the north-west corner of Ward B for display only; the values have no unit (think of them as an “observation index”). The centre cell is NoData, written in the file as <code>-9999</code>. The value 0 in the corner is a real observation, so 0 cannot be the NoData marker.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Choose a NoData policy and watch the mean</h3>
    <div class="raster-wrap">
      <figure class="map-fig" id="a3Fig"></figure>
      <div>
        <div class="controls" style="flex-direction:column;align-items:flex-start">
          <label><input type="radio" name="pol" value="exclude" checked> Exclude the NoData cell (mean of what was observed)</label>
          <label><input type="radio" name="pol" value="zero"> Replace NoData with 0</label>
          <label><input type="radio" name="pol" value="marker"> Forget the header — treat −9999 as a value</label>
        </div>
        <div class="tiles" id="a3Tiles"></div>
        <div class="result" id="a3Out"></div>
      </div>
    </div>
  </div>
  <div class="grid-3">
    <div class="card"><h3 style="margin-top:0">Policy 1 — exclude</h3><p>Eight valid cells. Sum 0 + 10 + 20 + 10 + 30 + 20 + 30 + 40 = 160. Mean <strong>20.0</strong>. The correct policy for “the mean of what was observed”.</p></div>
    <div class="card"><h3 style="margin-top:0">Policy 2 — zero</h3><p>Nine cells, sum still 160, mean <strong>17.78</strong>. The mean dropped 11 % because an <em>unknown</em> was treated as <em>nothing</em>. Wrong unless you truly know the missing cell is zero.</p></div>
    <div class="card"><h3 style="margin-top:0">Policy 3 — lost marker</h3><p>Nine cells, sum 160 − 9999 = −9839, mean about <strong>−1093</strong>. This is how an unrecognised NoData marker announces itself: a wildly impossible number. Chapter 4 saw the same signature (−612 m) on the elevation grid.</p></div>
  </div>
  <p><strong>Which policy does the software apply?</strong> ArcGIS Pro’s <em>Calculate Statistics</em> has an <em>Ignore Values</em> option that “allows you to exclude a specific value from the calculation of statistics. You may want to ignore a value if it is a NoData value or if it will skew your calculation.” A raster whose NoData is properly declared in its header is normally excluded without that option, but the page does not say so in those words — so your instructor should load <code>grid_a3.asc</code>, calculate statistics and confirm the mean is 20.0. QGIS’s <em>Raster layer statistics</em> “Calculates basic statistics from the values in a given band of the raster layer” (MIN, MAX, RANGE, SUM, MEAN, STD_DEV…). Get 17.78 and the marker was lost on import; get −1093 and it was never declared.</p>
  <div class="callout idea"><span class="label">State the policy — every time</span><p>The worksheet line for any raster statistic must say: <em>“NoData cells excluded (n = 8 of 9)”</em>. A reader who sees “mean 20” with no cell count cannot tell which policy produced it.</p></div>

  <h2><span class="mod">11.7.2</span>A threshold mask — with units, and what it is not</h2>
  <p>A <strong>mask</strong> is a raster of 1 where a condition is true and 0 (or NoData) where it is not — a <code>WHERE</code> clause evaluated cell by cell. We use Chapter 4’s elevation grid <code>elevation_training.asc</code>: 4 × 4 cells of 100 m, heights in <strong>metres above the made-up datum TD-0</strong>, one NoData cell. A threshold without a datum is meaningless: 12 m above <em>what</em>?</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Cells at or below a height</h3>
    <div class="controls"><label>Height threshold <input type="range" id="thr" min="8" max="22" step="1" value="12"> <span class="mono" id="thrV">≤ 12 m (TD-0)</span></label></div>
    <div class="raster-wrap">
      <figure class="map-fig" id="maskFig"></figure>
      <div><div class="tiles" id="maskTiles"></div><div class="result" id="maskOut"></div></div>
    </div>
  </div>
  <p>At ≤ 12 m, <strong>six</strong> cells satisfy the condition (60,000 m² = 6 hectares), nine do not, and one <strong>stays NoData</strong> — a condition cannot be evaluated where there is no value. The unknown cell is neither “low” nor “high”; the mask must say <em>unknown</em>, not 0.</p>
  <p><strong>In software.</strong> ArcGIS Pro’s <em>Con</em> tool “Performs a conditional if/else evaluation on each of the input cells of an input raster” — expression <code>VALUE &lt;= 12</code>, true value 1, false value 0 — and for missing cells: “If NoData does not satisfy the expression, it does not receive the value of the input false raster; it remains NoData.” Con and Raster Calculator both <strong>need the Spatial Analyst or Image Analyst extension</strong> at every licence level. In QGIS the core Raster Calculator needs no extension: “Conditional expressions (=, !=, &lt;, &gt;=, …) return either 0 for false or 1 for true”, so <code>"elevation_training@1" &lt;= 12</code> is the mask; how QGIS treats the NoData cell in that expression is something to inspect in the output, not assume.</p>
  <div class="callout warn"><span class="label">What the mask is not</span><p>It is a set of cells whose stored height is at or below a number. It is <strong>not a flood-risk map</strong>. Flooding depends on where water comes from, how it flows, how much rain falls, what the ground absorbs, what stands in the way — none of which is in a 4 × 4 height grid. The honest name is “elevation ≤ 12 m (TD-0) mask”. A colleague who labels it “flood zone” has claimed something the input cannot support; that claim is one of the scenario questions in 11.10.</p></div>

  <h2><span class="mod">11.7.3</span>Before combining two rasters: four checks</h2>
  <p>Raster tools combine rasters <em>cell by cell</em>. That only means something if cell (row 2, column 3) of one raster covers the same ground as cell (row 2, column 3) of the other. Four checks, all in Chapter 4 words:</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Check</th><th>Question</th><th>grid_a3 versus elevation_training</th><th>If you skip it</th></tr></thead>
    <tbody>
      <tr><td><strong>Extent</strong></td><td>Do they cover the same ground?</td><td>No. grid_a3 covers x 1000–1300, y 700–1000 (in Ward B); elevation covers x 0–400, y 600–1000 (in Ward A). No overlap at all.</td><td>ArcGIS Pro uses the environment’s <em>Extent</em> — here empty — so the output is empty or all NoData</td></tr>
      <tr><td><strong>Cell size</strong></td><td>Same size cells?</td><td>Yes, both 100 m.</td><td>The <em>Cell Size</em> environment defaults to “Maximum of Inputs” — “Use the largest cell size of all input datasets”, the coarsest; the finer raster is resampled (Chapter 4) without asking</td></tr>
      <tr><td><strong>Alignment</strong></td><td>Do cell edges line up?</td><td>Both have corners on multiples of 100 m, so they <em>would</em> align. A copy of grid_a3 starting at x = 1050 would not — every cell straddles two.</td><td>The <em>Snap Raster</em> environment makes tools “adjust the extent of output rasters so that they match the cell alignment of the specified snap raster” — “the lower left corner of the extent is snapped to a cell corner of the snap raster”. Without it, one input is resampled onto the other’s grid, changing values</td></tr>
      <tr><td><strong>Missing data</strong></td><td>Where is NoData in each, and what is the policy?</td><td>grid_a3: centre cell. elevation: row 4, column 2.</td><td>Per tool — usually NoData in either input gives NoData in the output for cell-by-cell maths, or is ignored where the tool offers that option; read the tool page</td></tr>
    </tbody></table></div>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Shift a grid by half a cell</h3>
    <div class="controls"><label><input type="checkbox" id="shift"> shift the copy 50 m to the right</label></div>
    <figure class="map-fig" id="alignFig" style="max-width:520px"></figure>
    <div class="result" id="alignOut"></div>
  </div>
  <div class="callout note"><span class="label">Scope note</span><p>Detailed raster work — resampling choices, zonal statistics, chains of map algebra, terrain analysis — comes later. This module only establishes that rasters must be <em>checked for compatibility</em> before they are combined, and that the missing-data policy must be written down. QGIS’s Raster Calculator sets the output grid from a reference layer or an explicit extent and cell size; ArcGIS Pro sets it through the environment settings named above. Either way <em>you</em> decide — never let a default decide silently.</p></div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“The software will line the rasters up.”</em> It will <em>produce</em> an output — by resampling one input onto the other’s grid, at the coarser cell size, over whatever extent the environment picked. Each of those is a decision with consequences for the values, taken without you.</p></div>

  <div class="quiz" data-answer="1" data-fb="13.6 m is the mean of the 15 valid cells (sum 204). 12.75 = 204 ÷ 16 treats the NoData cell as 0 — that product, or that import, lost the NoData marker and is wrong about the file. The worksheet line: “mean of 15 valid cells out of 16; NoData (−9999.0) excluded”.">
    <div class="q">Quick check 11.7. A colleague computes the mean of the elevation grid in two products and gets 13.6 in one and 12.75 in the other. Which is wrong about the <em>file</em>, and what one line prevents the confusion?</div>
    <div class="opts"><button class="opt">13.6 is wrong — it ignores a cell; write “mean of 16 cells”.</button><button class="opt">12.75 is wrong — it counts the NoData cell as 0; write “mean of 15 valid cells of 16; NoData excluded”.</button><button class="opt">Both are right; products differ.</button><button class="opt">Neither — the mean must use −9999.</button></div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  // 11.7.1
  const a3fig = document.getElementById("a3Fig");
  function a3() {
    const pol = document.querySelector("input[name=pol]:checked").value, st = rasterStats(GRID_A3, pol);
    drawRaster(a3fig, { rows: GRID_A3.rows.map(r => r.map(v => v === NODATA && pol !== "exclude" ? (pol === "zero" ? 0 : -9999) : v)), unit: "" }, { colour: (v, i, j) => v === NODATA ? "#fff" : (GRID_A3.rows[i][j] === NODATA ? "#f5d9d3" : "#dbe7f3"), title: "grid_a3.asc — 100 m cells, no unit" });
    document.getElementById("a3Tiles").innerHTML = `<div class="tile"><span class="big">${st.n}</span><span class="lab">cells used</span></div><div class="tile"><span class="big">${fmt(st.sum)}</span><span class="lab">sum</span></div><div class="tile ${pol === "exclude" ? "cool" : "hot"}"><span class="big">${st.mean.toFixed(2)}</span><span class="lab">mean</span></div>`;
    document.getElementById("a3Out").innerHTML = pol === "exclude" ? `<span class="verdict ok">Honest.</span> Eight observed cells, mean 20.0. Write: “NoData excluded (n = 8 of 9)”.` : pol === "zero" ? `<span class="verdict no">An unknown became a zero.</span> Nine cells, mean 17.78 — 11 % lower than the observed mean. Only acceptable if you <em>know</em> the missing cell is zero, and you don’t.` : `<span class="verdict no">Marker lost.</span> −9999 was treated as a measurement. A mean of −1093 on a grid whose values run 0–40 is the file shouting that its header was ignored.`;
  }
  document.querySelectorAll("input[name=pol]").forEach(r => r.addEventListener("change", a3)); a3();
  // 11.7.2
  const mfig = document.getElementById("maskFig");
  function mask() {
    const t = +document.getElementById("thr").value; document.getElementById("thrV").textContent = `≤ ${t} m (TD-0)`;
    let yes = 0, no = 0, nd = 0;
    ELEV_F6.rows.flat().forEach(v => v === NODATA ? nd++ : v <= t ? yes++ : no++);
    drawRaster(mfig, ELEV_F6, { colour: v => v === NODATA ? "#fff" : v <= t ? "#d9ecdf" : "#efe7d7", mark: v => v === NODATA ? "?" : v <= t ? "1" : "0", title: "elevation_training.asc — m above TD-0" });
    document.getElementById("maskTiles").innerHTML = `<div class="tile cool"><span class="big">${yes}</span><span class="lab">cells = 1 (${fmt(yes * 10000)} m² = ${yes} ha)</span></div><div class="tile"><span class="big">${no}</span><span class="lab">cells = 0</span></div><div class="tile hot"><span class="big">${nd}</span><span class="lab">stays NoData</span></div>`;
    document.getElementById("maskOut").innerHTML = `Condition: <code>value &lt;= ${t}</code>, metres above TD-0, threshold inclusive. Honest title: <em>“elevation ≤ ${t} m (TD-0) mask — ${yes} of 15 valid cells; 1 cell no data”</em>. Not a flood map.`;
  }
  document.getElementById("thr").addEventListener("input", mask); mask();
  // 11.7.3
  const afig = document.getElementById("alignFig");
  function align() {
    const sh = document.getElementById("shift").checked ? 50 : 0;
    const m = gridMap(afig, { extent: { x1: 900, y1: 600, x2: 1500, y2: 1100 }, caption: sh ? "The copy (orange) starts at x = 1050: every cell straddles two cells of the original." : "Both grids start at x = 1000: cell edges coincide." });
    for (let i = 0; i < 3; i++) for (let j = 0; j < 3; j++) mk("rect", { x: m.X(1000 + j * 100), y: m.Y(1000 - i * 100), width: m.S(100), height: m.S(100), fill: "rgba(91,126,163,.25)", stroke: "#5b7ea3", "stroke-width": 4 }, m.svg);
    for (let i = 0; i < 3; i++) for (let j = 0; j < 3; j++) mk("rect", { x: m.X(1000 + sh + j * 100), y: m.Y(1000 - i * 100), width: m.S(100), height: m.S(100), fill: "rgba(211,84,31,.18)", stroke: "#d3541f", "stroke-width": 4, "stroke-dasharray": "14 10" }, m.svg);
    document.getElementById("alignOut").innerHTML = sh ? `Cell-by-cell maths is now meaningless: which orange cell pairs with which blue one? The software will <em>resample</em> one grid onto the other — values change. Use <strong>Snap Raster</strong> (ArcGIS Pro) or a reference layer (QGIS) so outputs share one grid, and say so in the log.` : `Same origin, same 100 m cells: cell (r, c) covers the same ground in both. Cell-by-cell maths is meaningful — but check extent and NoData too.`;
  }
  document.getElementById("shift").addEventListener("change", align); align();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
