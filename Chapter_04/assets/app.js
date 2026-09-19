/* Chapter 4 tutorial — raster fixtures, grid renderers, arithmetic helpers, navigation, quizzes.
   Every grid, value and file name here is MADE-UP practice material (synthetic). No real place is described. */

/* ---------- fixtures (from the Chapter 4 document) ---------- */
const ND = null; // NoData in memory

/* F5 — land-cover grid, 10 x 10 cells of 100 m, origin (0,0) = Ward A. Row 0 is the TOP row. */
const F5 = {
  name: "landcover_training.asc", cols: 10, rows: 10, cell: 100, x0: 0, y0: 0, nodataValue: -1, kind: "categorical",
  grid: [
    [2,2,2,2,2,2,2,0,0,0],
    [2,2,2,2,2,2,2,0,0,0],
    [2,2,2,3,3,2,2,2,0,2],
    [2,2,3,3,3,3,2,2,2,2],
    [1,1,1,1,1,1,1,1,1,1],
    [1,1,1,1,1,1,1,1,1,1],
    [2,2,1,1,2,2,2,3,3,2],
    [2,2,1,1,2,2,2,3,3,2],
    [ND,ND,2,2,2,2,2,2,2,2],
    [ND,2,2,2,2,2,2,2,2,2]
  ],
  dict: { 0: "Water", 1: "Built-up", 2: "Vegetation", 3: "Bare ground" },
  colors: { 0: "#2c5aa0", 1: "#8d8d8d", 2: "#4fa36b", 3: "#d8b46a" }
};

/* F6 — elevation grid, 4 x 4 cells of 100 m, origin (0,600). Metres above the made-up datum "TD-0". */
const F6 = {
  name: "elevation_training.asc", cols: 4, rows: 4, cell: 100, x0: 0, y0: 600, nodataValue: -9999, kind: "continuous", unit: "m above TD-0",
  grid: [
    [12,14,17,21],
    [11,13,15,18],
    [10,11,13,15],
    [9,ND,12,13]
  ]
};

/* F7 — the blueprint's 3 x 3 arithmetic grid (no position, no unit) */
const F7 = { name: "F7", cols: 3, rows: 3, cell: 1, x0: 0, y0: 0, kind: "continuous", grid: [[0,10,20],[10,ND,30],[20,30,40]] };

/* ---------- arithmetic helpers ---------- */
function statsOf(grid, policy = "exclude", nodataValue = -9999) {
  // policy: "exclude" | "zero" | "raw" (raw = the NoData marker was NOT recognised)
  let n = 0, sum = 0, min = Infinity, max = -Infinity, nd = 0;
  grid.forEach(r => r.forEach(v => {
    let val = v;
    if (v === ND) { nd++; if (policy === "exclude") return; val = policy === "zero" ? 0 : nodataValue; }
    n++; sum += val; if (val < min) min = val; if (val > max) max = val;
  }));
  return { n, sum, mean: n ? sum / n : NaN, min, max, nd };
}
function fmt(v, d = 2) { return v === ND ? "NoData" : (Math.round(v * 10 ** d) / 10 ** d).toString(); }

/* coordinate -> cell (0-based row from TOP, col from LEFT), using the floor rule (a cell owns its left and bottom edges) */
function cellAt(r, x, y) {
  const inside = x >= r.x0 && x <= r.x0 + r.cols * r.cell && y >= r.y0 && y <= r.y0 + r.rows * r.cell;
  if (!inside) return { inside: false };
  let col = Math.floor((x - r.x0) / r.cell), rowFromBottom = Math.floor((y - r.y0) / r.cell);
  if (col >= r.cols) col = r.cols - 1; if (rowFromBottom >= r.rows) rowFromBottom = r.rows - 1; // top/right edge belongs to last cell here
  const row = r.rows - 1 - rowFromBottom;
  const onXEdge = Number.isInteger((x - r.x0) / r.cell) && x > r.x0 && x < r.x0 + r.cols * r.cell;
  const onYEdge = Number.isInteger((y - r.y0) / r.cell) && y > r.y0 && y < r.y0 + r.rows * r.cell;
  return { inside: true, col, row, rowFromBottom, onEdge: onXEdge || onYEdge, onCorner: onXEdge && onYEdge, value: r.grid[row][col] };
}
function cellCenter(r, row, col) { return { x: r.x0 + (col + .5) * r.cell, y: r.y0 + (r.rows - row - .5) * r.cell }; }

/* resampling to a finer grid by an integer factor (2 => half the cell size).
   nearest: exact. bilinear: exact where 4 valid neighbours exist; "edge" / "nd" where the answer depends on the software. */
function resampleNearest(r, f) {
  const out = [];
  for (let i = 0; i < r.rows * f; i++) { out.push([]); for (let j = 0; j < r.cols * f; j++) out[i].push(r.grid[Math.floor(i / f)][Math.floor(j / f)]); }
  return Object.assign({}, r, { rows: r.rows * f, cols: r.cols * f, cell: r.cell / f, grid: out, name: r.name + " (nearest, " + r.cell / f + " m)" });
}
function resampleBilinear(r, f) {
  const out = [], flags = [];
  const cx = j => r.x0 + (j + .5) * r.cell, cy = i => r.y0 + (r.rows - i - .5) * r.cell; // input centres
  for (let i = 0; i < r.rows * f; i++) {
    out.push([]); flags.push([]);
    const y = r.y0 + (r.rows * f - i - .5) * (r.cell / f);
    for (let j = 0; j < r.cols * f; j++) {
      const x = r.x0 + (j + .5) * (r.cell / f);
      if (x < cx(0) || x > cx(r.cols - 1) || y > cy(0) || y < cy(r.rows - 1)) { out[i].push(ND); flags[i].push("edge"); continue; }
      let jx = Math.min(Math.max(...[...Array(r.cols).keys()].filter(k => cx(k) <= x)), r.cols - 2);
      let iy = Math.min(Math.max(...[...Array(r.rows).keys()].filter(k => cy(k) >= y)), r.rows - 2);
      const wx = (x - cx(jx)) / r.cell, wy = (cy(iy) - y) / r.cell;
      const v = [r.grid[iy][jx], r.grid[iy][jx + 1], r.grid[iy + 1][jx], r.grid[iy + 1][jx + 1]];
      if (v.includes(ND)) { out[i].push(ND); flags[i].push("nd"); continue; }
      out[i].push((1 - wx) * (1 - wy) * v[0] + wx * (1 - wy) * v[1] + (1 - wx) * wy * v[2] + wx * wy * v[3]); flags[i].push("ok");
    }
  }
  return Object.assign({}, r, { rows: r.rows * f, cols: r.cols * f, cell: r.cell / f, grid: out, flags, name: r.name + " (bilinear, " + r.cell / f + " m)" });
}

/* ---------- colour rules (renderers) ---------- */
function rampColor(t) { // 0..1 -> blue -> green -> yellow -> orange
  const stops = [[44,90,160],[79,163,107],[244,197,66],[211,84,31]];
  const p = Math.min(Math.max(t, 0), 1) * (stops.length - 1), k = Math.floor(p), u = p - k;
  const a = stops[k], b = stops[Math.min(k + 1, stops.length - 1)];
  return `rgb(${Math.round(a[0] + (b[0] - a[0]) * u)},${Math.round(a[1] + (b[1] - a[1]) * u)},${Math.round(a[2] + (b[2] - a[2]) * u)})`;
}
function greyColor(t) { const g = Math.round(Math.min(Math.max(t, 0), 1) * 255); return `rgb(${g},${g},${g})`; }
function colorFor(r, v, renderer, ext) {
  // renderer: "none" | "unique" | "grey" | "ramp" | "random"
  if (v === ND) return null;
  if (renderer === "none") return "#ffffff";
  if (renderer === "unique") return (r.colors && r.colors[v]) || "#ccc";
  if (renderer === "random") { const rc = ["#e07a5f","#81b29a","#f2cc8f","#3d405b","#a8dadc","#c77dff"]; return rc[Math.abs(Math.round(v)) % rc.length]; }
  const t = ext.max === ext.min ? .5 : (v - ext.min) / (ext.max - ext.min);
  return renderer === "grey" ? greyColor(t) : rampColor(t);
}

/* ---------- SVG grid renderer ----------
   opts: renderer, showValues, nodata: "hatch"|"color"|"transparent"|{color}, onCell(row,col), selected:[row,col], axes (true = metre axes from the fixture),
         markers: [{x,y,label}], caption, cellPx (SVG units per cell), rowLabels, flags (from bilinear), valueFormat(v) */
const NS = "http://www.w3.org/2000/svg";
function svgEl(tag, attrs, parent) { const n = document.createElementNS(NS, tag); for (const k in attrs) n.setAttribute(k, attrs[k]); if (parent) parent.appendChild(n); return n; }
function renderRaster(el, r, opts = {}) {
  const o = Object.assign({ renderer: "none", showValues: true, nodata: "hatch", onCell: null, selected: null, axes: false, markers: [], caption: null, cellPx: null, rowLabels: false, flags: null, valueFormat: v => fmt(v, 2), dim: null }, opts);
  const cp = o.cellPx || Math.max(44, Math.min(90, 720 / Math.max(r.cols, r.rows)));
  const padL = o.axes ? 70 : (o.rowLabels ? 60 : 8), padT = o.rowLabels ? 44 : (o.axes ? 14 : 8), padR = o.axes ? 36 : 8, padB = o.axes ? 60 : 8;
  const W = padL + r.cols * cp + padR, H = padT + r.rows * cp + padB;
  const svg = svgEl("svg", { viewBox: `0 0 ${W} ${H}`, role: "img", "aria-label": `A grid of ${r.rows} rows and ${r.cols} columns of cells with values.` });
  const defs = svgEl("defs", {}, svg);
  const pat = svgEl("pattern", { id: "hatch", width: "8", height: "8", patternUnits: "userSpaceOnUse", patternTransform: "rotate(45)" }, defs);
  svgEl("rect", { width: "8", height: "8", fill: "#fff" }, pat); svgEl("rect", { width: "3", height: "8", fill: "#b9432e" }, pat);
  const ext = statsOf(r.grid, "exclude");
  for (let i = 0; i < r.rows; i++) for (let j = 0; j < r.cols; j++) {
    const v = r.grid[i][j], x = padL + j * cp, y = padT + i * cp;
    let fill = colorFor(r, v, o.renderer, ext);
    if (v === ND) fill = o.nodata === "hatch" ? "url(#hatch)" : o.nodata === "transparent" ? "none" : (o.nodata.color || o.nodata);
    const flag = o.flags ? o.flags[i][j] : null;
    if (flag === "edge") fill = "#eee"; if (flag === "nd") fill = "#f5d9d3";
    const rect = svgEl("rect", { x, y, width: cp, height: cp, fill, class: "cell" + (o.onCell ? " clickable" : "") + (o.selected && o.selected[0] === i && o.selected[1] === j ? " sel" : ""), "data-r": i, "data-c": j }, svg);
    if (o.dim && !o.dim(i, j)) rect.setAttribute("opacity", ".25");
    if (o.onCell) rect.addEventListener("click", () => o.onCell(i, j, v));
    if (o.showValues) {
      const dark = fill && fill.startsWith("rgb") ? (parseInt(fill.slice(4)) < 110) : (fill === "#2c5aa0" || fill === "#3d405b" || fill === "#8d8d8d");
      const t = svgEl("text", { x: x + cp / 2, y: y + cp / 2 + 1, class: "cellval" + (dark ? " light" : ""), "font-size": Math.round(cp * .34) }, svg);
      t.textContent = flag === "edge" ? "?" : flag === "nd" ? "?" : (v === ND ? "ND" : o.valueFormat(v));
    }
  }
  if (o.selected) { const [i, j] = o.selected; svgEl("rect", { x: padL + j * cp, y: padT + i * cp, width: cp, height: cp, fill: "none", class: "cell sel" }, svg); }
  if (o.rowLabels) {
    for (let j = 0; j < r.cols; j++) { const t = svgEl("text", { x: padL + j * cp + cp / 2, y: padT - 14, class: "axis", "text-anchor": "middle" }, svg); t.textContent = "col " + (j + 1); }
    for (let i = 0; i < r.rows; i++) { const t = svgEl("text", { x: padL - 8, y: padT + i * cp + cp / 2 + 6, class: "axis", "text-anchor": "end" }, svg); t.textContent = "row " + (i + 1); }
  }
  if (o.axes) {
    const step = r.cell * Math.max(1, Math.round(r.cols / 5));
    for (let j = 0; j <= r.cols; j += Math.round(step / r.cell)) { const xv = r.x0 + j * r.cell; const t = svgEl("text", { x: padL + j * cp, y: padT + r.rows * cp + 22, class: "axis", "text-anchor": "middle" }, svg); t.textContent = xv; }
    for (let i = 0; i <= r.rows; i += Math.round(step / r.cell)) { const yv = r.y0 + (r.rows - i) * r.cell; const t = svgEl("text", { x: padL - 10, y: padT + i * cp + 6, class: "axis", "text-anchor": "end" }, svg); t.textContent = yv; }
    const tx = svgEl("text", { x: padL + r.cols * cp / 2, y: padT + r.rows * cp + 50, class: "axis title", "text-anchor": "middle" }, svg); tx.textContent = "x (metres) →";
    const ty = svgEl("text", { x: 18, y: padT + r.rows * cp / 2, class: "axis title", "text-anchor": "middle", transform: `rotate(-90 18 ${padT + r.rows * cp / 2})` }, svg); ty.textContent = "y (metres) ↑";
  }
  (o.markers || []).forEach(m => {
    const px = padL + (m.x - r.x0) / r.cell * cp, py = padT + (r.y0 + r.rows * r.cell - m.y) / r.cell * cp;
    svgEl("circle", { cx: px, cy: py, r: Math.max(6, cp * .11), class: "marker" }, svg);
    const t = svgEl("text", { x: px + cp * .16, y: py - cp * .12, class: "marker-label" }, svg); t.textContent = m.label;
  });
  svg.style.maxWidth = W + "px"; svg.style.margin = "0 auto";
  el.innerHTML = ""; el.appendChild(svg);
  if (o.caption) { const c = document.createElement("figcaption"); c.textContent = o.caption; el.appendChild(c); }
  return svg;
}
function legendFor(r, renderer, ext) {
  if (renderer === "unique") return Object.keys(r.dict).map(k => `<span><span class="sw" style="background:${r.colors[k]}"></span>${k} = ${r.dict[k]}</span>`).join("") + `<span><span class="sw hatch"></span>NoData (${r.nodataValue})</span>`;
  if (renderer === "grey") return `<span><span class="sw" style="background:#000"></span>lowest value (${fmt(ext.min)})</span><span><span class="sw" style="background:#fff"></span>highest value (${fmt(ext.max)})</span><span><span class="sw hatch"></span>NoData</span>`;
  if (renderer === "ramp") return `<span><span class="ramp"></span> ${fmt(ext.min)} → ${fmt(ext.max)}</span><span><span class="sw hatch"></span>NoData</span>`;
  if (renderer === "random") return `<span>Colours chosen by the software with no meaning — a legend would just list the numbers</span>`;
  return `<span>Numbers only — no colour rule applied</span>`;
}

/* Navigation, progress, quizzes, sorters and tabs live in /assets/common.js (shared by every chapter). */
