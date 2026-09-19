<?php $page = ['title' => '4.5 Resampling and its consequences', 'chapter' => 4, 'module' => '4.5']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 4.5 · General principle · method names checked against ArcGIS Pro 3.7 and QGIS 3.44 docs</div>
    <h1>Resampling: changing the grid means choosing a rule</h1>
    <p class="lead">Take a 100 m cell and ask for 50 m cells instead. Four new cells now sit where one old one was. What number does each get? <strong>Nothing in the data answers that</strong> — the ground inside the old cell was never measured. A method must decide. This module shows the methods, what they do to categories, and why a finer grid is not more information.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Resample a small elevation grid with nearest neighbour and bilinear, and see the arithmetic for any cell.</li>
      <li>Watch bilinear turn “water next to vegetation” into “built-up”.</li>
      <li>Prove to yourself that 50 m cells made from 100 m data contain no new information.</li></ul></div>
  </div>

  <h2><span class="mod">4.5.1</span>Why a new grid needs a rule</h2>
  <p><strong>Resampling</strong> means producing a new raster on a different grid — a different cell size, a different origin, or (Chapter 6) a different coordinate system — from an existing one. Because new cells do not sit exactly on old ones, <strong>each new value must be derived from the old values by a rule</strong>, the <em>resampling method</em>. ArcGIS Pro’s <em>Resample</em> tool describes itself as changing the cell size while setting “rules for aggregating or interpolating values across the new pixel sizes”.</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Method</th><th>How the new value is chosen</th><th>Makes new values?</th><th>Suits</th><th>Does not suit</th></tr></thead>
    <tbody>
      <tr><td><strong>Nearest neighbour</strong></td><td>Copies the value of the one old cell whose centre is nearest the new cell’s centre. “It will not change the values of the cells.”</td><td><strong>No</strong></td><td>Categories (land cover); anywhere original values must be kept</td><td>Smooth-looking surfaces (result is blocky)</td></tr>
      <tr><td><strong>Bilinear interpolation</strong></td><td>A distance-weighted average of the four nearest old cell centres. “Will cause some smoothing.”</td><td><strong>Yes</strong> — values between the inputs</td><td>Continuous surfaces (elevation, temperature)</td><td>Categories: “should not be used with categorical data, since the cell values may be altered”</td></tr>
      <tr><td><strong>Cubic convolution</strong></td><td>Fits a smooth curve through the 16 nearest old centres. Smoothest — but “may result in … values outside the range of the input”.</td><td><strong>Yes</strong> — possibly outside the input range</td><td>Continuous surfaces where smoothness matters</td><td>Categories; anything that must stay within measured range</td></tr>
      <tr><td><strong>Majority</strong> (ArcGIS Pro) / <strong>Mode</strong> (GDAL/QGIS)</td><td>The most common value among the nearby old cells.</td><td><strong>No</strong></td><td>Categories, especially when making cells <em>bigger</em></td><td>Continuous data</td></tr>
    </tbody></table></div>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>One block of four cells becomes sixteen</h3>
    <p>The top-left 2 × 2 of the elevation grid (12, 14 / 11, 13) is resampled from 100 m to 50 m. Pick a method, then <strong>click any new cell</strong> to see how its number was made.</p>
    <div class="toggle-row">
      <button class="btn small" id="mNear" aria-pressed="true">Nearest neighbour</button>
      <button class="btn small" id="mBil" aria-pressed="false">Bilinear</button>
    </div>
    <div class="grid-2">
      <figure class="raster-fig" id="srcFig"></figure>
      <figure class="raster-fig" id="dstFig"></figure>
    </div>
    <div class="calc" id="rsCalc">Click a cell in the right-hand grid.</div>
    <p class="small">Cells marked <strong>?</strong> sit at the edge, where fewer than four old centres surround them. What the software puts there is an implementation detail the documentation does not fix — the lab asks you to <em>record</em> it, not predict it.</p>
  </div>
  <div class="callout note"><span class="label">Two very different places where resampling happens</span><p>Both platforms also resample <em>while drawing</em>, every time you zoom, to fit cells to screen pixels. QGIS’s Symbology tab has a <em>Resampling</em> section that applies “when you zoom in and out”; ArcGIS Pro picks a default display method from the raster’s <em>Source Type</em> (bilinear for elevation, nearest for thematic). <strong>Display resampling changes only what you see.</strong> The <em>Resample</em> tool (or GDAL Warp/Translate in QGIS) creates a <strong>new dataset</strong> whose values are permanently derived by the chosen method. The lab does the second kind, on copies, so the effect can be measured.</p></div>
  <div class="callout dev"><span class="label">Developer view</span><p>Resampling is image scaling: nearest neighbour is the blocky sprite scale-up, bilinear/cubic are the smooth photo scale-up a browser applies. Good for the <em>look</em>. <strong>Where the comparison stops:</strong> a browser only needs the result to look right; a resampled elevation grid will be <em>measured</em>, and a resampled land-cover grid holds codes for which “smooth” means nothing (next section).</p></div>

  <h2><span class="mod">4.5.2</span>Averaging category codes makes meaningless categories</h2>
  <p>Take the land-cover grid where the pond (code 0) meets vegetation (code 2) and resample it with bilinear. A new cell that sits three-quarters of the way toward the water cells gets 0.75 × 0 + 0.25 × 2 = <strong>0.5</strong>. Its neighbour, the other way, gets <strong>1.5</strong>. Neither is in the dictionary. If the output is stored as whole numbers, 0.5 and 1.5 become 0, 1 or 2 depending on the software — and <strong>1 is built-up</strong>.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>The pond edge, resampled two ways</h3>
    <p>Rows 1–3, columns 6–10 of the land-cover grid, 100 m → 50 m. Click cells in the results to check them against the dictionary.</p>
    <figure class="raster-fig" id="lcSrc"></figure>
    <div class="grid-2">
      <figure class="raster-fig" id="lcNear"></figure>
      <figure class="raster-fig" id="lcBil"></figure>
    </div>
    <div class="result" id="lcOut">Click any cell in the two resampled grids.</div>
  </div>
  <p>Nothing was built on the ground. The tool did what it was told — computed an average — and the average of two codes is either a number with no meaning or a <em>third code that means something unrelated</em>. The second case is worse: 1 is a valid code, so nothing flags it, and a thin strip of “built-up” appears around every pond. Esri’s tool page says it directly: bilinear and cubic “should not be used with categorical data, since the cell values may be altered”.</p>
  <div class="callout dev"><span class="label">Developer view</span><p>Category codes are members of an <code>enum</code>. <code>(RED + BLUE) / 2</code> is a type error in any language that takes enums seriously. A raster stores enums as integers — so the type checker is <em>you</em>.</p></div>
  <div class="callout warn"><span class="label">Not a universal law</span><p>“Nearest for categories” is the usual choice, not a law. When making cells <em>bigger</em> (100 m → 500 m), nearest keeps whichever one old cell happens to be nearest the new centre and throws away the other 24 — a class covering most of the new cell can vanish while a one-cell class survives by luck. <em>Majority / mode</em> picks the most common class and is often better there. The real rule: <strong>never do arithmetic on codes; choose among the non-arithmetic methods by what the aggregation should mean.</strong> And the software will not stop you — ArcGIS Pro offers all four methods for any raster.</p></div>

  <h2><span class="mod">4.5.3</span>Smaller cells do not recover unmeasured detail</h2>
  <p>Resample the whole elevation grid from 100 m to 50 m and you get 8 × 8 = 64 cells — four times as many. You do not get four times as much information. Esri’s docs: “resampling an image to have a smaller pixel size does not produce greater detail.” The original measured one value per 100 m; nothing inside those cells was ever observed. The methods just <em>fill</em> the new cells.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <div class="toggle-row">
      <button class="btn small" id="wSrc" aria-pressed="true">Original 4 × 4</button>
      <button class="btn small" id="wNear" aria-pressed="false">Nearest 8 × 8</button>
      <button class="btn small" id="wBil" aria-pressed="false">Bilinear 8 × 8</button>
    </div>
    <div class="fig-panel">
      <figure class="raster-fig" id="wFig"></figure>
      <div><div class="stat-row" id="wStat"></div><div class="result" id="wOut"></div></div>
    </div>
  </div>
  <div class="table-wrap"><table>
    <thead><tr><th>Method</th><th>The 8 × 8 looks like…</th><th>What it adds</th></tr></thead>
    <tbody>
      <tr><td>Nearest neighbour</td><td>The 4 × 4 with each cell copied into a 2 × 2 block — the same picture drawn with more squares</td><td>Nothing. Every statistic over the valid area is unchanged (mean still 13.6 m)</td></tr>
      <tr><td>Bilinear</td><td>A smoother surface sloping gently between cells</td><td>Plausible-looking <em>guesses</em>, assuming the surface changes in straight lines between old centres</td></tr>
      <tr><td>Cubic</td><td>Smoother still</td><td>Guesses under a different assumption; possibly values outside the measured range</td></tr>
    </tbody></table></div>
  <div class="callout warn"><span class="label">The trap</span><p>An upscaled photo looks blurry and warns your eye. A bilinearly upscaled elevation grid looks <em>better</em> — smoother, more natural — than the honest blocky original, and warns nobody. Resampling to a finer grid is fine for <em>alignment</em> with another layer (4.3.3), as long as the report says which layer was the coarse one. Calling the output “1 m data” when the source was 10 m is never fine.</p></div>

  <div class="quiz" data-answer="2" data-fb="Nearest neighbour creates no new values, so every 10 m cell holds a code that exists in the input. Bilinear and cubic average codes; ‘average’ is also arithmetic on codes. The 10 m output will not contain any boundary or class finer than 30 m — each 3 × 3 block is a copy of one input cell.">
    <div class="q">A soil-type raster (codes 1–6) at 30 m must be brought onto a 10 m grid to compare cell by cell with a 10 m raster. Which method?</div>
    <div class="opts">
      <button class="opt">Bilinear — smoother result</button>
      <button class="opt">Cubic — smoothest result</button>
      <button class="opt">Nearest neighbour — creates no new codes</button>
      <button class="opt">Average — the 10 m cells should represent the surrounding area</button>
    </div><div class="fb"></div>
  </div>
  <div class="callout note"><span class="label">Comprehension check (write it down)</span><p>You are given a 30 m land-cover raster and asked for a 10 m version “so it matches the building footprints”. (a) Which method, and why? (b) One thing the 10 m output will show that the 30 m input did not, and one thing it will not. (c) The sentence you would put in the output’s documentation.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  /* 2x2 block demo */
  const blk = { cols: 2, rows: 2, cell: 100, x0: 0, y0: 800, grid: [[12, 14], [11, 13]], kind: "continuous" };
  let method = "near", sel = null;
  renderRaster(document.getElementById("srcFig"), blk, { renderer: "ramp", rowLabels: true, cellPx: 120, caption: "Source: 2 × 2 cells of 100 m (top-left of the elevation grid). Centres at x = 50, 150; y = 950, 850." });
  function drawDst() {
    const out = method === "near" ? resampleNearest(blk, 2) : resampleBilinear(blk, 2);
    renderRaster(document.getElementById("dstFig"), out, { renderer: "ramp", rowLabels: true, cellPx: 60, selected: sel, flags: out.flags, onCell: (i, j, v) => { sel = [i, j]; drawDst(); explain(i, j, v, out); },
      caption: method === "near" ? "Nearest: each old cell copied into a 2 × 2 block. No new numbers." : "Bilinear: interior cells are weighted averages; ? = edge, software-dependent." });
  }
  function explain(i, j, v, out) {
    const x = 25 + 50 * j, y = 975 - 50 * i;
    if (method === "near") {
      const si = Math.floor(i / 2), sj = Math.floor(j / 2);
      document.getElementById("rsCalc").innerHTML = `new cell row ${i + 1}, col ${j + 1}: centre (${x}, ${y})\nnearest old centre: (${50 + 100 * sj}, ${950 - 100 * si}) → old cell row ${si + 1}, col ${sj + 1}\nvalue = <span class="hl">${v}</span> (copied, unchanged)`;
    } else if (v === ND) {
      document.getElementById("rsCalc").innerHTML = `new cell row ${i + 1}, col ${j + 1}: centre (${x}, ${y})\n<span class="bad">This centre is outside the square of the four old centres — fewer than four neighbours.\nThe documentation does not say what value goes here. Record what your software does.</span>`;
    } else {
      const wx = (x - 50) / 100, wy = (950 - y) / 100;
      document.getElementById("rsCalc").innerHTML = `new cell row ${i + 1}, col ${j + 1}: centre (${x}, ${y})\nx is ${x - 50} m past old centre 50 → weight ${1 - wx} on column 1, ${wx} on column 2\ny is ${950 - y} m below old centre 950 → weight ${1 - wy} on row 1, ${wy} on row 2\nvalue = ${(1 - wx) * (1 - wy)} × 12 + ${wx * (1 - wy)} × 14 + ${(1 - wx) * wy} × 11 + ${wx * wy} × 13\n      = <span class="hl">${fmt(v, 4)}</span>  (a new number — nobody measured it)`;
    }
  }
  drawDst();
  document.getElementById("mNear").onclick = () => { method = "near"; sel = null; pr("mNear"); drawDst(); document.getElementById("rsCalc").textContent = "Click a cell in the right-hand grid."; };
  document.getElementById("mBil").onclick = () => { method = "bil"; sel = null; pr("mBil"); drawDst(); document.getElementById("rsCalc").textContent = "Click a cell in the right-hand grid."; };
  function pr(id) { ["mNear", "mBil"].forEach(x => document.getElementById(x).setAttribute("aria-pressed", x === id)); }

  /* land-cover pond edge */
  const sub = { cols: 5, rows: 3, cell: 100, x0: 500, y0: 700, grid: F5.grid.slice(0, 3).map(r => r.slice(5, 10)), dict: F5.dict, colors: F5.colors, nodataValue: -1 };
  renderRaster(document.getElementById("lcSrc"), sub, { renderer: "unique", cellPx: 64, caption: "Source codes (100 m)." });
  const nr = resampleNearest(sub, 2), br = resampleBilinear(sub, 2);
  const brRound = Object.assign({}, br, { colors: F5.colors });
  renderRaster(document.getElementById("lcNear"), nr, { renderer: "unique", cellPx: 42, onCell: (i, j, v) => document.getElementById("lcOut").innerHTML = `<strong>Nearest, row ${i + 1}, col ${j + 1}: ${v}</strong> = ${F5.dict[v]}. A code that exists in the source — copied, not invented.`, caption: "Nearest neighbour (50 m): only codes 0, 2, 3 appear." });
  const svg = renderRaster(document.getElementById("lcBil"), br, { renderer: "none", cellPx: 42, flags: br.flags, valueFormat: v => Number.isInteger(v) ? String(v) : fmt(v, 1), onCell: (i, j, v) => { const bad = v !== ND && !Number.isInteger(v); document.getElementById("lcOut").innerHTML = v === ND ? "Edge cell — software-dependent." : `<strong>Bilinear, row ${i + 1}, col ${j + 1}: ${fmt(v, 3)}</strong>. ${bad ? "Not in the dictionary at all. If stored as a whole number it becomes " + Math.floor(v) + " (" + F5.dict[Math.floor(v)] + ") or " + Math.ceil(v) + " (" + F5.dict[Math.ceil(v)] + ") — neither was observed here." : "= " + F5.dict[v] + " — this cell's four neighbours all held the same code, so the average is still that code."}`; }, caption: "Bilinear (50 m): fractions appear where classes meet. Pink = touches NoData; grey = edge." });
  svg.querySelectorAll("rect.cell").forEach(c => { const v = br.grid[+c.dataset.r][+c.dataset.c]; if (v !== ND) c.setAttribute("fill", Number.isInteger(v) ? F5.colors[v] : "#ffe08a"); });

  /* whole-grid comparison */
  const near8 = resampleNearest(F6, 2), bil8 = resampleBilinear(F6, 2);
  function show(which) {
    const r = which === "src" ? F6 : which === "near" ? near8 : bil8;
    const s = statsOf(r.grid);
    renderRaster(document.getElementById("wFig"), r, { renderer: "ramp", cellPx: which === "src" ? 100 : 56, flags: r.flags, rowLabels: true, valueFormat: v => fmt(v, 1), caption: which === "src" ? "Original: 16 cells, 100 m." : which === "near" ? "Nearest: 64 cells, 50 m. Same picture, more squares." : "Bilinear: 64 cells, 50 m. Smoother — but the ? cells (edge, and next to NoData) are software-dependent." });
    document.getElementById("wStat").innerHTML = [["valid cells", s.n], ["NoData / ?", r.rows * r.cols - s.n], ["mean", fmt(s.mean, 2)], ["min", fmt(s.min)], ["max", fmt(s.max)]].map(([k, v]) => `<div class="stat"><div class="k">${k}</div><div class="v">${v}</div></div>`).join("");
    document.getElementById("wOut").innerHTML = which === "src" ? "15 valid cells, mean 13.6 m." : which === "near" ? "60 valid cells (15 × 4), sum 816 (204 × 4), mean <strong>still 13.6</strong>. Four times the cells, zero new information." : "The 28 interior cells here are exact arithmetic (all between 10.56 and 19.31 — inside the input range, as bilinear must be). The 36 ? cells depend on the software. So the whole-grid mean cannot be predicted from the maths alone — you must run it and record it.";
    ["wSrc", "wNear", "wBil"].forEach(x => document.getElementById(x).setAttribute("aria-pressed", x === "w" + which[0].toUpperCase() + which.slice(1)));
  }
  document.getElementById("wSrc").onclick = () => show("src"); document.getElementById("wNear").onclick = () => show("near"); document.getElementById("wBil").onclick = () => show("bil");
  show("src");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
