/* Chapter 10 tutorial — fixture data, hand-checkable geometry rules, SVG map helpers.
   Everything here is MADE-UP practice data on a flat training grid in metres. No real town, ward or office.
   The rules follow the definitions quoted in the chapter document (PostGIS ST_Contains / ST_Covers /
   ST_Intersects / ST_Touches; Esri "How proximity tools calculate distance"). They are written out
   in plain arithmetic so that every answer on these pages can be checked by hand. */

/* ---------- Fixture (Chapter 1 grid + Chapter 10 columns) ---------- */
const FIXTURE = {
  wards: [
    { id: "A", name: "Ward A", pts: [[0, 0], [1000, 0], [1000, 1000], [0, 1000]] },
    { id: "B", name: "Ward B", pts: [[1000, 0], [2000, 0], [2000, 1000], [1000, 1000]] }
  ],
  road: { id: "R1", name: "Main Road", a: [0, 500], b: [2000, 500] },
  requests: [
    { id: "P1", x: 200,  y: 200, category: "Streetlight out", priority: "Medium", status: "Open",               reported: "2026-09-02", closed: null,         channel: "Mobile app", ward_code: "A",  cost: 1500 },
    { id: "P2", x: 800,  y: 800, category: "Pothole",         priority: "High",   status: "In progress",        reported: "2026-08-28", closed: null,         channel: "Phone",      ward_code: "A",  cost: 12000 },
    { id: "P3", x: 1200, y: 250, category: "Water leak",      priority: "High",   status: "Open",               reported: "2026-09-10", closed: null,         channel: "Mobile app", ward_code: "B",  cost: null },
    { id: "P4", x: 1700, y: 900, category: "Pothole",         priority: "Low",    status: "Resolved",           reported: "2026-08-15", closed: "2026-08-20", channel: "Web form",   ward_code: "B",  cost: 0 },
    { id: "P5", x: 1000, y: 500, category: "Blocked drain",   priority: "Medium", status: "Reopened",           reported: "2026-08-30", closed: null,         channel: "Phone",      ward_code: null, cost: 2500 },
    { id: "P6", x: 2200, y: 500, category: "Fallen tree",     priority: "High",   status: "Closed – duplicate", reported: "2026-09-11", closed: "2026-09-12", channel: "Phone",      ward_code: "C",  cost: null }
  ],
  assets: [
    { id: "SL-0113", type: "Streetlight", x: 205,  y: 195 },
    { id: "DR-0042", type: "Drain",       x: 995,  y: 510 },
    { id: "TR-0301", type: "Tree",        x: 2190, y: 520 }
  ],
  inspections: [
    { id: "INS-0001", asset: "DR-0042", visit: "2024-10-15 09:50", team: "T-S", condition: 3, defects: 0 },
    { id: "INS-0002", asset: "DR-0042", visit: "2025-11-02 15:05", team: "T-S", condition: 2, defects: 2 },
    { id: "INS-0003", asset: "SL-0113", visit: "2026-03-14 10:42", team: "T-N", condition: 3, defects: 1 },
    { id: "INS-0004", asset: "SL-0113", visit: "2025-09-14 11:15", team: "T-N", condition: 3, defects: 0 }
  ],
  register: [
    { ward_code: "A", ward_name: "West ward", crew_team: "T-N", valid_from: "2019-04-01", is_current: "No" },
    { ward_code: "A", ward_name: "West ward", crew_team: "T-N", valid_from: "2024-01-01", is_current: "Yes" },
    { ward_code: "B", ward_name: "East ward", crew_team: "T-S", valid_from: "2019-04-01", is_current: "Yes" }
  ]
};
/* Depot DP-01 from Chapter 3: an outer wall with a courtyard hole. Used for the boundary lesson. */
const DEPOT = {
  outer: [[1400, 600], [1600, 600], [1600, 800], [1400, 800]],
  hole:  [[1450, 650], [1550, 650], [1550, 750], [1450, 750]],
  probes: [
    { id: "H1", x: 1420, y: 620, label: "inside the depot land" },
    { id: "H2", x: 1400, y: 700, label: "on the outer wall" },
    { id: "H3", x: 1500, y: 700, label: "in the courtyard (the hole)" },
    { id: "H4", x: 1700, y: 700, label: "outside, on the street" },
    { id: "H5", x: 1450, y: 700, label: "on the courtyard wall (the hole’s ring)" }
  ]
};
/* The L-shaped yard for the bounding-box lesson. */
const LYARD = [[0, 0], [300, 0], [300, 100], [100, 100], [100, 300], [0, 300]];

const fmtN = (n, d = 0) => n.toLocaleString("en-IN", { minimumFractionDigits: d, maximumFractionDigits: d });
const fmt = (n, d = 2) => Number.isInteger(n) ? fmtN(n) : n.toFixed(d);
const NULLTXT = "NULL";

/* ---------- geometry rules (planar metres) ---------- */
function distToSeg(p, a, b) {
  const dx = b[0] - a[0], dy = b[1] - a[1];
  const t = Math.max(0, Math.min(1, ((p[0] - a[0]) * dx + (p[1] - a[1]) * dy) / (dx * dx + dy * dy)));
  return Math.hypot(p[0] - (a[0] + t * dx), p[1] - (a[1] + t * dy));
}
function distToRoad(x, y) { return distToSeg([x, y], FIXTURE.road.a, FIXTURE.road.b); } // finite segment: past x=2000 the answer is the distance to the end point
function onRing(p, ring) { // is the point exactly on one of the ring's edges?
  for (let i = 0; i < ring.length; i++) if (distToSeg(p, ring[i], ring[(i + 1) % ring.length]) < 1e-9) return true;
  return false;
}
function strictlyInRing(p, ring) { // ray casting; the caller has already checked "on the edge"
  let inside = false;
  for (let i = 0, j = ring.length - 1; i < ring.length; j = i++) {
    const a = ring[i], b = ring[j];
    if ((a[1] > p[1]) !== (b[1] > p[1]) && p[0] < (b[0] - a[0]) * (p[1] - a[1]) / (b[1] - a[1]) + a[0]) inside = !inside;
  }
  return inside;
}
/* Where is a point relative to a polygon (outer ring + optional holes)?
   Returns "interior" | "boundary" | "hole" | "exterior". The hole's ring counts as boundary. */
function classify(p, outer, holes = []) {
  if (onRing(p, outer)) return "boundary";
  for (const h of holes) if (onRing(p, h)) return "boundary";
  if (!strictlyInRing(p, outer)) return "exterior";
  for (const h of holes) if (strictlyInRing(p, h)) return "hole";
  return "interior";
}
/* Named predicates for a POINT against a polygon, from the quoted definitions.
   contains: every point of B in A, and interiors share a point  -> only "interior"
   covers:   no point of B outside A                              -> interior or boundary
   intersects: any point in common                                -> interior or boundary
   touches:  common points, but only on the boundary              -> boundary only
   disjoint: nothing in common                                    -> hole or exterior */
function predicates(pos) {
  return {
    contains:   pos === "interior",
    covers:     pos === "interior" || pos === "boundary",
    intersects: pos === "interior" || pos === "boundary",
    touches:    pos === "boundary",
    disjoint:   pos === "hole" || pos === "exterior"
  };
}
/* The same answers, using each product's documented option names (point against polygon). */
function engineColumns(pos) {
  const p = predicates(pos);
  return [
    { eng: "PostGIS", name: "ST_Contains", v: p.contains },
    { eng: "PostGIS", name: "ST_Covers", v: p.covers },
    { eng: "PostGIS", name: "ST_Intersects", v: p.intersects },
    { eng: "PostGIS", name: "ST_Touches", v: p.touches },
    { eng: "ArcGIS Pro", name: "Completely within", v: p.contains },
    { eng: "ArcGIS Pro", name: "Within", v: p.covers },
    { eng: "ArcGIS Pro", name: "Intersect", v: p.intersects },
    { eng: "ArcGIS Pro", name: "Boundary touches", v: p.touches },
    { eng: "QGIS", name: "are within", v: p.contains },
    { eng: "QGIS", name: "intersect", v: p.intersects },
    { eng: "QGIS", name: "touch", v: p.touches }
  ];
}
function wardPos(p, ward) { return classify(p, ward.pts); }
function wardsOf(x, y, mode = "strict") { // mode: strict | inclusive | touch
  return FIXTURE.wards.filter(w => {
    const pos = wardPos([x, y], w);
    if (mode === "strict") return pos === "interior";
    if (mode === "touch") return pos === "boundary";
    return pos === "interior" || pos === "boundary";
  }).map(w => w.id);
}
function bboxOf(ring) { const xs = ring.map(p => p[0]), ys = ring.map(p => p[1]); return { xmin: Math.min(...xs), xmax: Math.max(...xs), ymin: Math.min(...ys), ymax: Math.max(...ys) }; }
function inBox(p, b) { return p[0] >= b.xmin && p[0] <= b.xmax && p[1] >= b.ymin && p[1] <= b.ymax; }
function nearestAsset(p) {
  let best = null;
  FIXTURE.assets.forEach(a => { const d = Math.hypot(a.x - p[0], a.y - p[1]); if (!best || d < best.d - 1e-9) best = { id: a.id, d, tie: false }; else if (best && Math.abs(d - best.d) < 1e-9) best.tie = true; });
  return best;
}

/* ---------- attribute conditions (a tiny, safe evaluator for the fixture) ----------
   A clause is { field, op, value }. Ops: "=", "<>", ">", ">=", "<", "<=", "IN", "LIKE", "IS NULL", "IS NOT NULL".
   Three-valued logic: a comparison with NULL returns null (unknown), and only true rows pass. */
function evalClause(row, c) {
  const v = row[c.field];
  if (c.op === "IS NULL") return v === null;
  if (c.op === "IS NOT NULL") return v !== null;
  if (v === null) return null;                       // unknown
  if (c.op === "IN") return c.value.includes(v);
  if (c.op === "LIKE") { const re = new RegExp("^" + c.value.replace(/[.*+?^${}()|[\]\\]/g, "\\$&").replace(/%/g, ".*").replace(/_/g, ".") + "$"); return re.test(String(v)); }
  const a = v, b = c.value;
  switch (c.op) { case "=": return a === b; case "<>": return a !== b; case ">": return a > b; case ">=": return a >= b; case "<": return a < b; case "<=": return a <= b; }
  return null;
}
function and3(a, b) { if (a === false || b === false) return false; if (a === null || b === null) return null; return true; }
function or3(a, b) { if (a === true || b === true) return true; if (a === null || b === null) return null; return false; }
function not3(a) { return a === null ? null : !a; }
function clauseText(c) {
  const q = x => typeof x === "string" ? `'${x}'` : x;
  if (c.op === "IS NULL" || c.op === "IS NOT NULL") return `${c.field} ${c.op}`;
  if (c.op === "IN") return `${c.field} IN (${c.value.map(q).join(", ")})`;
  return `${c.field} ${c.op} ${q(c.value)}`;
}
function runQuery(rows, fn) { return rows.filter(r => fn(r) === true).map(r => r.id); }

/* ---------- joins (row multiplication made visible) ---------- */
function joinRows(left, right, lkey, rkey, keepAll = true) {
  const out = [];
  left.forEach(l => {
    const matches = l[lkey] === null ? [] : right.filter(r => r[rkey] === l[lkey]);
    if (matches.length) matches.forEach(r => out.push({ left: l, right: r }));
    else if (keepAll) out.push({ left: l, right: null });
  });
  return out;
}

/* ---------- SVG helpers ---------- */
const NS = "http://www.w3.org/2000/svg";
const SX = x => x, SY = y => 1100 - y;              // grid metres -> SVG (y flipped so "up" is north)
function mkEl(tag, attrs, parent) { const n = document.createElementNS(NS, tag); for (const k in attrs) n.setAttribute(k, attrs[k]); if (parent) parent.appendChild(n); return n; }
function txt(parent, x, y, s, cls = "lbl", extra = {}) { const t = mkEl("text", Object.assign({ x, y, class: cls }, extra), parent); t.textContent = s; return t; }
function newSvg(el, viewBox, label) { const svg = mkEl("svg", { viewBox, role: "img", "aria-label": label }); el.innerHTML = ""; el.appendChild(svg); return svg; }
function ptsStr(r) { return r.map(p => `${SX(p[0])},${SY(p[1])}`).join(" "); }
function pathOf(rings) { return rings.map(r => "M " + r.map(p => `${SX(p[0])} ${SY(p[1])}`).join(" L ") + " Z").join(" "); }
function caption(el, s) { const c = document.createElement("figcaption"); c.textContent = s; el.appendChild(c); }

/* The training-grid map: wards, road, requests, assets, optional distance band and highlights. */
function renderGrid(el, opts = {}) {
  const o = Object.assign({ layers: { wards: true, roads: true, requests: true, assets: false }, band: null, hit: null, dim: null, selected: null, onSelect: null, labels: true, axes: true, caption: null, extra: null, viewBox: "-140 40 2560 1190", wardFill: {} }, opts);
  const svg = mkEl("svg", { viewBox: o.viewBox, role: "img", "aria-label": "Schematic map of the practice grid: Ward A and Ward B side by side, Main Road R1 across the middle, six request points P1 to P6." });
  const mk = (tag, attrs, parent = svg) => mkEl(tag, attrs, parent);
  if (o.axes) {
    mk("line", { x1: SX(0), y1: SY(-40), x2: SX(2300), y2: SY(-40), class: "axis" });
    mk("line", { x1: SX(-40), y1: SY(0), x2: SX(-40), y2: SY(1050), class: "axis" });
    for (let x = 0; x <= 2000; x += 500) { mk("line", { x1: SX(x), y1: SY(-40), x2: SX(x), y2: SY(-60), class: "axis" }); txt(svg, SX(x), SY(-95), x, "axis-label", { "text-anchor": "middle" }); }
    for (let y = 0; y <= 1000; y += 500) { mk("line", { x1: SX(-40), y1: SY(y), x2: SX(-60), y2: SY(y), class: "axis" }); txt(svg, SX(-75), SY(y) + 10, y, "axis-label", { "text-anchor": "end" }); }
    txt(svg, SX(2320), SY(-52), "x (m)", "axis-label"); txt(svg, SX(-120), SY(1080), "y (m)", "axis-label");
  }
  if (o.layers.wards) FIXTURE.wards.forEach(w => {
    mk("polygon", { points: ptsStr(w.pts), class: "ward " + (o.wardFill[w.id] || "") });
    txt(svg, SX(500 + (w.id === "B" ? 1000 : 0)), SY(800), w.name, "ward-label", { "text-anchor": "middle" });
  });
  if (o.band != null && o.band > 0) {
    const r = o.band, g = mk("g", {});
    mk("rect", { x: SX(0), y: SY(500 + r), width: 2000, height: 2 * r, class: "band" }, g);
    mk("circle", { cx: SX(0), cy: SY(500), r, class: "band" }, g);
    mk("circle", { cx: SX(2000), cy: SY(500), r, class: "band" }, g);
  }
  if (o.layers.roads) {
    const r = FIXTURE.road;
    mk("line", { x1: SX(r.a[0]), y1: SY(r.a[1]), x2: SX(r.b[0]), y2: SY(r.b[1]), class: "road" });
    txt(svg, SX(1500), SY(540), "R1 Main Road", "road-label");
    mk("line", { x1: SX(2000), y1: SY(470), x2: SX(2000), y2: SY(530), class: "road" });
  }
  if (o.extra) o.extra(svg, mk);
  if (o.layers.assets) FIXTURE.assets.forEach(a => {
    mk("rect", { x: SX(a.x) - 16, y: SY(a.y) - 16, width: 32, height: 32, class: "asset", transform: `rotate(45 ${SX(a.x)} ${SY(a.y)})` });
    if (o.labels) txt(svg, SX(a.x) + 26, SY(a.y) + 46, a.id, "asset-label");
  });
  if (o.layers.requests) FIXTURE.requests.forEach(p => {
    let cls = "req";
    if (o.dim && o.dim.includes(p.id)) cls += " dim";
    if (o.hit && o.hit.includes(p.id)) cls += " hit";
    if (o.selected === p.id) cls += " sel";
    const c = mk("circle", { cx: SX(p.x), cy: SY(p.y), r: 26, class: cls, "data-id": p.id });
    if (o.onSelect) c.addEventListener("click", () => o.onSelect(p.id));
    if (o.labels) txt(svg, SX(p.x) + 34, SY(p.y) - 24, p.id, "req-label");
  });
  el.innerHTML = ""; el.appendChild(svg);
  if (o.caption) caption(el, o.caption);
  return svg;
}

/* The depot with its courtyard hole, zoomed in. Returns { svg, setProbe(x,y) }. */
function renderDepot(el, opts = {}) {
  const o = Object.assign({ probes: true, probe: null, showBox: false, caption: null, onMove: null }, opts);
  const VB = { x0: 1340, x1: 1760, y0: 540, y1: 860 };
  const sx = x => (x - VB.x0) * 2, sy = y => (VB.y1 - y) * 2;    // 2 svg units per metre, y flipped
  const svg = mkEl("svg", { viewBox: `0 0 ${(VB.x1 - VB.x0) * 2} ${(VB.y1 - VB.y0) * 2}`, role: "img", "aria-label": "The depot DP-01: a square outer wall with a square courtyard hole in the middle." });
  const defs = mkEl("defs", {}, svg);
  const pat = mkEl("pattern", { id: "hatch10", width: 12, height: 12, patternUnits: "userSpaceOnUse", patternTransform: "rotate(45)" }, defs);
  mkEl("line", { x1: 0, y1: 0, x2: 0, y2: 12, stroke: "#b9432e", "stroke-width": 3, opacity: .5 }, pat);
  // street background label
  txt(svg, sx(1620), sy(560), "outside (street)", "tag");
  // depot land = outer minus hole (evenodd)
  const d = [DEPOT.outer, DEPOT.hole].map(r => "M " + r.map(p => `${sx(p[0])} ${sy(p[1])}`).join(" L ") + " Z").join(" ");
  mkEl("path", { d, class: "depotland", "fill-rule": "evenodd" }, svg);
  mkEl("polygon", { points: DEPOT.hole.map(p => `${sx(p[0])},${sy(p[1])}`).join(" "), class: "holehatch", fill: "url(#hatch10)" }, svg);
  if (o.showBox) { const b = bboxOf(DEPOT.outer); mkEl("rect", { x: sx(b.xmin), y: sy(b.ymax), width: (b.xmax - b.xmin) * 2, height: (b.ymax - b.ymin) * 2, class: "extent" }, svg); }
  txt(svg, sx(1500), sy(662), "courtyard (hole)", "tag", { "text-anchor": "middle" });
  txt(svg, sx(1410), sy(780), "DP-01 depot land", "tag");
  // walls drawn on top
  mkEl("polygon", { points: DEPOT.outer.map(p => `${sx(p[0])},${sy(p[1])}`).join(" "), class: "wall" }, svg);
  mkEl("polygon", { points: DEPOT.hole.map(p => `${sx(p[0])},${sy(p[1])}`).join(" "), class: "wall" }, svg);
  if (o.probes) DEPOT.probes.forEach(h => { mkEl("circle", { cx: sx(h.x), cy: sy(h.y), r: 9, class: "probe-fixed" }, svg); txt(svg, sx(h.x) + 12, sy(h.y) - 10, h.id, "vlabel"); });
  let probe = null, probeLbl = null;
  function setProbe(x, y) {
    if (!probe) { probe = mkEl("circle", { cx: 0, cy: 0, r: 13, class: "probe" }, svg); probeLbl = txt(svg, 0, 0, "", "vlabel lit"); }
    probe.setAttribute("cx", sx(x)); probe.setAttribute("cy", sy(y));
    probeLbl.setAttribute("x", sx(x) + 16); probeLbl.setAttribute("y", sy(y) - 14); probeLbl.textContent = `(${x}, ${y})`;
  }
  if (o.probe) setProbe(o.probe[0], o.probe[1]);
  if (o.onMove) {
    const toGrid = ev => { const r = svg.getBoundingClientRect(); const px = (ev.clientX - r.left) / r.width * (VB.x1 - VB.x0) + VB.x0; const py = VB.y1 - (ev.clientY - r.top) / r.height * (VB.y1 - VB.y0); return [Math.round(px), Math.round(py)]; };
    let down = false;
    svg.addEventListener("pointerdown", ev => { down = true; svg.setPointerCapture(ev.pointerId); const [x, y] = toGrid(ev); setProbe(x, y); o.onMove(x, y); });
    svg.addEventListener("pointermove", ev => { if (!down) return; const [x, y] = toGrid(ev); setProbe(x, y); o.onMove(x, y); });
    svg.addEventListener("pointerup", () => { down = false; });
    svg.style.cursor = "crosshair"; svg.style.touchAction = "none";
  }
  el.innerHTML = ""; el.appendChild(svg);
  if (o.caption) caption(el, o.caption);
  return { svg, setProbe };
}

/* The L-shaped yard with its bounding box. */
function renderLYard(el, probe = null) {
  const s = 1.2, sx = x => 40 + x * s, sy = y => 40 + (300 - y) * s;
  const svg = mkEl("svg", { viewBox: "0 0 440 400", role: "img", "aria-label": "An L-shaped yard inside its rectangular bounding box, with a test point in the notch." });
  const b = bboxOf(LYARD);
  mkEl("rect", { x: sx(b.xmin), y: sy(b.ymax), width: (b.xmax - b.xmin) * s, height: (b.ymax - b.ymin) * s, class: "extent" }, svg);
  mkEl("polygon", { points: LYARD.map(p => `${sx(p[0])},${sy(p[1])}`).join(" "), class: "lyard" }, svg);
  txt(svg, sx(30), sy(40), "yard Y", "tag"); txt(svg, sx(150), sy(280), "bounding box", "tag warn");
  if (probe) { mkEl("circle", { cx: sx(probe[0]), cy: sy(probe[1]), r: 9, class: "probe" }, svg); txt(svg, sx(probe[0]) + 12, sy(probe[1]) - 8, `(${probe[0]}, ${probe[1]})`, "vlabel"); }
  el.innerHTML = ""; el.appendChild(svg); return svg;
}

/* ---------- small HTML helpers ---------- */
function reqTable(el, cols, rowClass = () => "") {
  const head = { id: "request_id", xy: "(x, y) m", category: "category", priority: "priority", status: "status", reported: "reported", closed: "closed", channel: "channel", ward_code: "ward_code", cost: "est_cost_inr", dist: "distance to R1" };
  const cell = (p, c) => {
    if (c === "xy") return `(${p.x}, ${p.y})`;
    if (c === "dist") return fmt(distToRoad(p.x, p.y)) + " m";
    const v = p[c]; return v === null ? `<span class="null">NULL</span>` : (c === "cost" ? fmtN(v) : v);
  };
  el.innerHTML = `<table class="attr"><thead><tr>${cols.map(c => `<th class="mono">${head[c]}</th>`).join("")}</tr></thead><tbody>${FIXTURE.requests.map(p => `<tr class="${rowClass(p)}" data-id="${p.id}">${cols.map(c => `<td class="${["xy", "id", "cost", "dist", "reported", "closed", "ward_code"].includes(c) ? "mono" : ""}">${cell(p, c)}</td>`).join("")}</tr>`).join("")}</tbody></table>`;
}
const yesno = v => v === null ? `<span class="chip q">unknown</span>` : v ? `<span class="chip yes">true</span>` : `<span class="chip no">false</span>`;
const tf = v => v ? `<td class="yes">✓ yes</td>` : `<td class="no">✗ no</td>`;
