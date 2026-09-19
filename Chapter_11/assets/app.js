/* Chapter 11 tutorial — practice data, small geometry helpers and SVG drawing.
   Everything here is MADE-UP practice data on a flat "training grid" measured in metres.
   The grid has no place on the Earth and no coordinate-system code. Fixture E11 (lab, Part 2)
   reuses Chapter 6's Fixture E6 and is also made up.
   Numbers match the Chapter 11 training document (GIS_Phase_1_Chapter_11_Spatial_Analysis_Fundamentals.md). */

/* ---------- Fixture F11-1: requests on the training grid (metres) ---------- */
const REQUESTS = [
  { id: "P1", x: 200,  y: 200, status: "OPEN",   date: "2026-08-15", cat: "Blocked drain",   where: "inside Ward A" },
  { id: "P2", x: 800,  y: 800, status: "CLOSED", date: "2026-07-30", cat: "Streetlight out", where: "inside Ward A" },
  { id: "P3", x: 1200, y: 250, status: "OPEN",   date: "2026-09-01", cat: "Pothole",         where: "inside Ward B" },
  { id: "P4", x: 1700, y: 900, status: "OPEN",   date: "2026-08-22", cat: "Water leak",      where: "inside Ward B" },
  { id: "P5", x: 1000, y: 500, status: "OPEN",   date: "2026-09-05", cat: "Fallen tree",     where: "on the A/B boundary line" },
  { id: "P6", x: 2200, y: 500, status: "OPEN",   date: "2026-08-30", cat: "Road damage",     where: "outside both wards" }
];

/* ---------- Fixture F11-2: wards with zones ---------- */
const WARDS = [
  { id: "A", x1: 0,    y1: 0,    x2: 1000, y2: 1000, zone: "Z1", households: 1200 },
  { id: "B", x1: 1000, y1: 0,    x2: 2000, y2: 1000, zone: "Z2", households: 900 },
  { id: "C", x1: 0,    y1: 1000, x2: 1000, y2: 1500, zone: "Z1", households: 400 },
  { id: "D", x1: 2500, y1: 0,    x2: 3000, y2: 500,  zone: "Z2", households: 150 }
];
const wardById = id => WARDS.find(w => w.id === id);

/* ---------- Roads (polylines, metres) ---------- */
const ROADS = {
  R1: { id: "R1", name: "Main Road",   pts: [[0, 500], [2000, 500]], length_m: 2000 },
  R2: { id: "R2", name: "School Road", pts: [[500, 0], [500, 500], [800, 900]], length_m: 1000 },
  R6: { id: "R6", name: "North Lane",  pts: [[500, 800], [500, 1300]], length_m: 500 }
};

/* ---------- Fixture E11 (Earth-referenced; EPSG:32643 metres) ---------- */
const E11 = {
  requests: [
    { id: "Q1", E: 500000.000, N: 2543741.163, status: "OPEN",   date: "2026-08-20", cat: "Blocked drain",   where: "on the A/B boundary", dist: 158.837 },
    { id: "Q2", E: 500000.000, N: 2544405.362, status: "CLOSED", date: "2026-07-03", cat: "Streetlight out", where: "on the A/B boundary", dist: 505.362 },
    { id: "Q3", E: 499487.611, N: 2544073.271, status: "OPEN",   date: "2026-09-02", cat: "Pothole",         where: "inside Ward A",      dist: 173.271 },
    { id: "Q4", E: 500512.389, N: 2544073.271, status: "OPEN",   date: "2026-08-28", cat: "Water leak",      where: "inside Ward B",      dist: 173.271 },
    { id: "Q5", E: 501229.733, N: 2544073.313, status: "OPEN",   date: "2026-09-10", cat: "Fallen tree",     where: "outside both wards", dist: 287.775 }
  ],
  road: { id: "RE-1", pts: [[499000, 2543900], [501000, 2543900]] }
};

/* ---------- Rasters ---------- */
const NODATA = null;
const GRID_A3 = { name: "grid_a3.asc", cell: 100, x0: 1000, y0: 700, rows: [[0, 10, 20], [10, NODATA, 30], [20, 30, 40]], unit: "" };
const ELEV_F6 = { name: "elevation_training.asc", cell: 100, x0: 0, y0: 600, unit: "m", rows: [[12, 14, 17, 21], [11, 13, 15, 18], [10, 11, 13, 15], [9, NODATA, 12, 13]] };

/* ---------- geometry helpers (flat plane, metres) ---------- */
const hyp = (dx, dy) => Math.sqrt(dx * dx + dy * dy);
/* distance from point to one segment; with flat ends, only the part with a perpendicular foot counts */
function distToSeg(px, py, a, b, flat = false) {
  const dx = b[0] - a[0], dy = b[1] - a[1], L2 = dx * dx + dy * dy;
  let t = L2 === 0 ? 0 : ((px - a[0]) * dx + (py - a[1]) * dy) / L2;
  if (flat && (t < 0 || t > 1)) return Infinity;
  t = Math.max(0, Math.min(1, t));
  return hyp(px - (a[0] + t * dx), py - (a[1] + t * dy));
}
function distToLine(px, py, pts, flat = false) {
  let d = Infinity;
  for (let i = 0; i < pts.length - 1; i++) d = Math.min(d, distToSeg(px, py, pts[i], pts[i + 1], flat));
  return d;
}
function lineLength(pts) { let s = 0; for (let i = 0; i < pts.length - 1; i++) s += hyp(pts[i + 1][0] - pts[i][0], pts[i + 1][1] - pts[i][1]); return s; }
/* is a point inside a rectangle ward? inclusive = boundary counts */
function inWard(px, py, w, inclusive = true) {
  return inclusive ? (px >= w.x1 && px <= w.x2 && py >= w.y1 && py <= w.y2)
                   : (px > w.x1 && px < w.x2 && py > w.y1 && py < w.y2);
}
function wardsOf(px, py, wards, inclusive = true) { return wards.filter(w => inWard(px, py, w, inclusive)).map(w => w.id); }
/* clip a segment to an axis-aligned rectangle (Liang–Barsky); returns [[x,y],[x,y]] or null */
function clipSegToRect(a, b, r) {
  let t0 = 0, t1 = 1; const dx = b[0] - a[0], dy = b[1] - a[1];
  const checks = [[-dx, a[0] - r.x1], [dx, r.x2 - a[0]], [-dy, a[1] - r.y1], [dy, r.y2 - a[1]]];
  for (const [p, q] of checks) {
    if (p === 0) { if (q < 0) return null; continue; }
    const t = q / p;
    if (p < 0) { if (t > t1) return null; if (t > t0) t0 = t; } else { if (t < t0) return null; if (t < t1) t1 = t; }
  }
  return [[a[0] + t0 * dx, a[1] + t0 * dy], [a[0] + t1 * dx, a[1] + t1 * dy]];
}
function clipLineToRect(pts, r) {
  const out = [];
  for (let i = 0; i < pts.length - 1; i++) { const s = clipSegToRect(pts[i], pts[i + 1], r); if (s && hyp(s[1][0] - s[0][0], s[1][1] - s[0][1]) > 1e-9) out.push(s); }
  return out; // list of segments
}
/* rectangle ∩ rectangle area */
function rectOverlap(a, b) { const w = Math.min(a.x2, b.x2) - Math.max(a.x1, b.x1), h = Math.min(a.y2, b.y2) - Math.max(a.y1, b.y1); return (w > 0 && h > 0) ? { x1: Math.max(a.x1, b.x1), y1: Math.max(a.y1, b.y1), x2: Math.min(a.x2, b.x2), y2: Math.min(a.y2, b.y2), area: w * h } : null; }
/* buffer of the horizontal road R1 — exact areas */
function r1BufferArea(r, round = true) { return 2000 * 2 * r + (round ? Math.PI * r * r : 0); }
const fmt = (n, d = 0) => Number(n).toLocaleString("en-US", { minimumFractionDigits: d, maximumFractionDigits: d });
const fmtM2 = n => fmt(n) + " m²";

/* eligibility filter used everywhere in the chapter */
function eligible(list, since = "2026-08-01", status = "OPEN") { return list.filter(r => r.status === status && r.date >= since); }

/* ---------- SVG helpers ---------- */
const NS = "http://www.w3.org/2000/svg";
function mk(tag, attrs, parent) { const n = document.createElementNS(NS, tag); for (const k in attrs) n.setAttribute(k, attrs[k]); if (parent) parent.appendChild(n); return n; }
function stext(parent, x, y, s, cls, extra = {}) { const t = mk("text", Object.assign({ x, y, class: cls }, extra), parent); t.textContent = s; return t; }

/* A training-grid map. opts.extent = {x1,y1,x2,y2} in metres. Returns {svg, X, Y, S} where X/Y convert metres → pixels and S scales lengths. */
function gridMap(el, opts = {}) {
  const ex = opts.extent || { x1: -400, y1: -400, x2: 2400, y2: 1600 };
  const W = 1200, pad = 70, padL = 120;                 // extra room on the left for the y-axis numbers
  const sc = (W - pad - padL) / (ex.x2 - ex.x1);
  const H = (ex.y2 - ex.y1) * sc + 2 * pad;
  const X = x => padL + (x - ex.x1) * sc, Y = y => pad + (ex.y2 - y) * sc, S = d => d * sc;
  const svg = mk("svg", { viewBox: `0 0 ${W} ${H}`, role: "img", "aria-label": opts.label || "Schematic map of made-up practice data on a flat metre grid" });
  el.innerHTML = ""; el.appendChild(svg);
  // faint grid every 500 m
  for (let x = Math.ceil(ex.x1 / 500) * 500; x <= ex.x2; x += 500) { mk("line", { x1: X(x), y1: Y(ex.y1), x2: X(x), y2: Y(ex.y2), class: "g500" }, svg); stext(svg, X(x), Y(ex.y1) + 30, x, "axis-label", { "text-anchor": "middle" }); }
  for (let y = Math.ceil(ex.y1 / 500) * 500; y <= ex.y2; y += 500) { mk("line", { x1: X(ex.x1), y1: Y(y), x2: X(ex.x2), y2: Y(y), class: "g500" }, svg); stext(svg, X(ex.x1) - 8, Y(y) + 10, y, "axis-label", { "text-anchor": "end" }); }
  if (opts.caption) { const c = document.createElement("figcaption"); c.textContent = opts.caption; el.appendChild(c); }
  return { svg, X, Y, S, W, H };
}
function drawWards(m, wards, cls = "") {
  wards.forEach(w => {
    mk("rect", { x: m.X(w.x1), y: m.Y(w.y2), width: m.S(w.x2 - w.x1), height: m.S(w.y2 - w.y1), class: "ward " + cls, "data-ward": w.id }, m.svg);
    stext(m.svg, m.X(w.x1) + 18, m.Y(w.y2) + 58, "Ward " + w.id, "ward-label");
  });
}
function drawRoad(m, road, cls = "") {
  mk("polyline", { points: road.pts.map(p => `${m.X(p[0])},${m.Y(p[1])}`).join(" "), class: "road " + cls, fill: "none" }, m.svg);
  const p = road.pts[0];                                 // label at the start of the line, clear of P5/P6
  stext(m.svg, m.X(p[0]) + 12, m.Y(p[1]) - 16, road.id, "road-label");
}
function drawRequests(m, list, classFn) {
  list.forEach(r => {
    const c = mk("circle", { cx: m.X(r.x), cy: m.Y(r.y), r: 13, class: "req " + (classFn ? classFn(r) : ""), "data-id": r.id }, m.svg);
    stext(m.svg, m.X(r.x) + 18, m.Y(r.y) - 14, r.id, "req-label");
  });
}
/* buffer band of one segment; round or flat ends. Drawn in pixel space with uniform scale, so arcs are true circles. */
function bandPath(m, a, b, r, round = true) {
  const ax = m.X(a[0]), ay = m.Y(a[1]), bx = m.X(b[0]), by = m.Y(b[1]), R = m.S(r);
  const dx = bx - ax, dy = by - ay, L = hyp(dx, dy); if (L === 0) return "";
  const nx = -dy / L * R, ny = dx / L * R;               // unit normal × R
  const p1 = [ax + nx, ay + ny], p2 = [bx + nx, by + ny], p3 = [bx - nx, by - ny], p4 = [ax - nx, ay - ny];
  if (round) return `M${p1} L${p2} A${R},${R} 0 0 0 ${p3} L${p4} A${R},${R} 0 0 0 ${p1} Z`;
  return `M${p1} L${p2} L${p3} L${p4} Z`;
}
function drawBuffer(m, road, r, { round = true, cls = "" } = {}) {
  const g = mk("g", { class: "buffer " + cls }, m.svg);
  for (let i = 0; i < road.pts.length - 1; i++) mk("path", { d: bandPath(m, road.pts[i], road.pts[i + 1], r, round) }, g);
  return g;
}

/* ---------- raster drawing ---------- */
function drawRaster(el, ras, { colour, showValues = true, mark = null, title = "" } = {}) {
  const n = ras.rows[0].length, mrows = ras.rows.length, cs = 120, pad = 30;
  const W = n * cs + 2 * pad, H = mrows * cs + 2 * pad + 30;
  const svg = mk("svg", { viewBox: `0 0 ${W} ${H}`, role: "img", "aria-label": "A small grid of raster cells with their values" });
  el.innerHTML = ""; el.appendChild(svg);
  ras.rows.forEach((row, i) => row.forEach((v, j) => {
    const x = pad + j * cs, y = pad + i * cs;
    const fill = colour ? colour(v, i, j) : (v === NODATA ? "url(#nodata)" : "#dbe7f3");
    const r = mk("rect", { x, y, width: cs, height: cs, class: "cell" + (v === NODATA ? " nodata" : ""), fill, "data-r": i, "data-c": j }, svg);
    if (showValues) stext(svg, x + cs / 2, y + cs / 2 + 12, v === NODATA ? "NoData" : String(v) + (ras.unit && v !== NODATA ? "" : ""), "cellv" + (v === NODATA ? " nd" : ""), { "text-anchor": "middle" });
    if (mark && mark(v, i, j)) stext(svg, x + cs - 14, y + 28, mark(v, i, j), "cellmark", { "text-anchor": "end" });
  }));
  if (title) stext(svg, pad, H - 8, title, "rastertitle");
  return svg;
}
function rasterStats(ras, policy = "exclude") {
  const vals = [];
  ras.rows.flat().forEach(v => { if (v === NODATA) { if (policy === "zero") vals.push(0); else if (policy === "marker") vals.push(-9999); } else vals.push(v); });
  const sum = vals.reduce((a, b) => a + b, 0);
  return { n: vals.length, sum, mean: vals.length ? sum / vals.length : NaN };
}

/* ---------- little UI helpers ---------- */
const $ = s => document.querySelector(s);
const $$ = s => Array.from(document.querySelectorAll(s));
function idsText(arr) { return arr.length ? arr.join(", ") : "— none —"; }
function tableRows(tbody, rows) { tbody.innerHTML = rows.map(r => "<tr" + (r.cls ? ` class="${r.cls}"` : "") + ">" + r.cells.map(c => `<td${c.cls ? ` class="${c.cls}"` : ""}>${c.html ?? c}</td>`).join("") + "</tr>").join(""); }
