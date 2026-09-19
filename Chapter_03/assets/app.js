/* Chapter 3 tutorial — shared fixture data, geometry helpers, SVG town renderer, navigation, quizzes.
   Every coordinate is on a MADE-UP flat training grid in metres. No real town, school, park or depot. */

/* ---- the Chapter 3 town (synthetic) ---- */
const TOWN = {
  wards: [
    { id: "A", name: "Ward A", ring: [[0, 0], [1000, 0], [1000, 1000], [0, 1000]] },
    { id: "B", name: "Ward B", ring: [[1000, 0], [2000, 0], [2000, 1000], [1000, 1000]] }
  ],
  roads: [
    { id: "R1", name: "Main Road", width_m: 12, pts: [[0, 500], [2000, 500]] },
    { id: "R2", name: "Station Road", width_m: 7, pts: [[500, 0], [500, 500], [800, 900]] },
    { id: "L1", name: "Bus turning loop", width_m: 6, pts: [[1300, 300], [1350, 300], [1350, 350], [1300, 350], [1300, 300]] }
  ],
  requests: [
    { id: "P1", x: 200, y: 200, category: "Streetlight out", priority: "Medium", status: "Open", reported: "2026-09-02", closed: "", channel: "Mobile app", note: "" },
    { id: "P2", x: 800, y: 800, category: "Pothole", priority: "High", status: "In progress", reported: "2026-08-28", closed: "", channel: "Phone", note: "Crew assigned 2026-09-01" },
    { id: "P3", x: 1200, y: 250, category: "Water leak", priority: "High", status: "Open", reported: "2026-09-10", closed: "", channel: "Mobile app", note: "" },
    { id: "P4", x: 1700, y: 900, category: "Pothole", priority: "Low", status: "Resolved", reported: "2026-08-15", closed: "2026-08-20", channel: "Web form", note: "" },
    { id: "P5", x: 1000, y: 500, category: "Blocked drain", priority: "Medium", status: "Reopened", reported: "2026-08-30", closed: "", channel: "Phone", note: "Closed 2026-09-05; reopened 2026-09-12" },
    { id: "P6", x: 2200, y: 500, category: "Fallen tree", priority: "High", status: "Closed – duplicate", reported: "2026-09-11", closed: "2026-09-12", channel: "Phone", note: "Duplicate of a report not in this package" },
    { id: "P7", x: null, y: null, category: "Pothole", priority: "Medium", status: "Open", reported: "2026-09-14", closed: "", channel: "Phone", note: "Caller could not give a location; to be located" }
  ],
  assets: [
    { id: "SL-0113", type: "Streetlight", x: 205, y: 195, installed: 2018, condition: "Fair" },
    { id: "DR-0042", type: "Drain", x: 995, y: 510, installed: 2011, condition: "Poor" },
    { id: "TR-0301", type: "Tree", x: 2190, y: 520, installed: 2005, condition: "Unknown" },
    { id: "TR-0302", type: "Tree", x: 1500, y: 700, installed: 2019, condition: "Good" }
  ],
  school: { id: "SC-01", name: "Ward A Primary School", pt: [400, 700], ring: [[370, 680], [430, 680], [430, 720], [370, 720]] },
  park: { id: "PK-01", name: "Riverside Park", parts: [[[100, 50], [300, 50], [300, 150], [100, 150]], [[1300, 50], [1500, 50], [1500, 150], [1300, 150]]] },
  depot: { id: "DP-01", name: "Central depot", outer: [[1400, 600], [1600, 600], [1600, 800], [1400, 800]], hole: [[1450, 650], [1550, 650], [1550, 750], [1450, 750]] },
  wardPts: [{ id: "A", x: 500, y: 500 }, { id: "B", x: 1500, y: 500 }],
  inspections: [
    { id: "INS-0001", asset_id: "DR-0042", date: "2024-10-15", found: "Fair", by: "crew01" },
    { id: "INS-0002", asset_id: "DR-0042", date: "2025-11-02", found: "Poor", by: "crew01" },
    { id: "INS-0003", asset_id: "SL-0113", date: "2026-03-14", found: "Fair", by: "crew02" }
  ]
};
const CATS = ["Streetlight out", "Pothole", "Water leak", "Blocked drain", "Fallen tree"];
const STATUSES = ["Open", "In progress", "Resolved", "Reopened", "Closed – duplicate"];
const STATUS_COL = { "Open": "#d3541f", "In progress": "#f4c542", "Resolved": "#8b93a7", "Reopened": "#b9432e", "Closed – duplicate": "#c9c1b3" };
const CAT_COL = { "Streetlight out": "#d3541f", "Pothole": "#5b7ea3", "Water leak": "#2f7d4f", "Blocked drain": "#8a6a12", "Fallen tree": "#7a3e9d" };

/* ---- geometry helpers (all hand-checkable) ---- */
function segLen(a, b) { return Math.hypot(b[0] - a[0], b[1] - a[1]); }
function lineLength(pts) { let s = 0; for (let i = 1; i < pts.length; i++) s += segLen(pts[i - 1], pts[i]); return s; }
function ringArea(r) { let s = 0; for (let i = 0; i < r.length; i++) { const a = r[i], b = r[(i + 1) % r.length]; s += a[0] * b[1] - b[0] * a[1]; } return s / 2; } // signed: + = counter-clockwise
function inRing(p, r) { // ray casting; points exactly on the edge are treated as inside for this tutorial
  let inside = false;
  for (let i = 0, j = r.length - 1; i < r.length; j = i++) {
    const a = r[i], b = r[j];
    if (onSeg(p, a, b)) return true;
    if ((a[1] > p[1]) !== (b[1] > p[1]) && p[0] < (b[0] - a[0]) * (p[1] - a[1]) / (b[1] - a[1]) + a[0]) inside = !inside;
  }
  return inside;
}
function onSeg(p, a, b) { const cross = (b[0] - a[0]) * (p[1] - a[1]) - (b[1] - a[1]) * (p[0] - a[0]); if (Math.abs(cross) > 1e-9) return false; return p[0] >= Math.min(a[0], b[0]) - 1e-9 && p[0] <= Math.max(a[0], b[0]) + 1e-9 && p[1] >= Math.min(a[1], b[1]) - 1e-9 && p[1] <= Math.max(a[1], b[1]) + 1e-9; }
function distToSeg(p, a, b) { const dx = b[0] - a[0], dy = b[1] - a[1]; const t = Math.max(0, Math.min(1, ((p[0] - a[0]) * dx + (p[1] - a[1]) * dy) / (dx * dx + dy * dy))); return Math.hypot(p[0] - (a[0] + t * dx), p[1] - (a[1] + t * dy)); }
function distToLine(p, pts) { let d = Infinity; for (let i = 1; i < pts.length; i++) d = Math.min(d, distToSeg(p, pts[i - 1], pts[i])); return d; }
function extentOf(points) { const xs = points.map(p => p[0]), ys = points.map(p => p[1]); return { xmin: Math.min(...xs), ymin: Math.min(...ys), xmax: Math.max(...xs), ymax: Math.max(...ys) }; }
function fmt(n, d = 1) { return Number.isInteger(n) ? n.toLocaleString("en-IN") : n.toFixed(d); }
function fmtN(n) { return n.toLocaleString("en-IN"); }

/* ---- SVG renderer ---- */
const NS = "http://www.w3.org/2000/svg";
const SX = x => x, SY = y => 1100 - y;
function mkEl(tag, attrs, parent) { const n = document.createElementNS(NS, tag); for (const k in attrs) n.setAttribute(k, attrs[k]); if (parent) parent.appendChild(n); return n; }
function ptsStr(r) { return r.map(p => `${SX(p[0])},${SY(p[1])}`).join(" "); }
function pathOf(rings) { return rings.map(r => "M " + r.map(p => `${SX(p[0])} ${SY(p[1])}`).join(" L ") + " Z").join(" "); }

/* renderTown(el, opts)
   order: layer keys bottom→top. visible: keys shown. wardStyle: fill|solid|outline.
   reqStyle: { by: "category"|"status"|null, filter: fn(req)→bool, label: field|null, size }
   selected: { layer, id }; onSelect(layer, id); vertices: layer key; extentOf: layer key; moved: {P1:[x,y]}
   view: [x0,y0,w,h] in SVG units (default whole town); extra(mk, svg) hook; caption */
function renderTown(el, opts = {}) {
  const o = Object.assign({
    order: ["wards", "park", "depot", "schoolFoot", "roads", "assets", "schoolPt", "wardPts", "requests"],
    visible: ["wards", "park", "depot", "schoolFoot", "roads", "assets", "schoolPt", "requests"],
    wardStyle: "fill", reqStyle: {}, selected: null, onSelect: null, vertices: null, extentOf: null, moved: {},
    view: null, axes: true, labels: true, extra: null, caption: null
  }, opts);
  const svg = mkEl("svg", { viewBox: o.view ? o.view.join(" ") : "-140 40 2560 1190", role: "img", "aria-label": "Schematic map of the made-up training town: two ward squares, roads, requests, assets, a school, a two-part park and a depot with a courtyard." });
  const defs = mkEl("defs", {}, svg);
  const pat = mkEl("pattern", { id: "hatch", width: "24", height: "24", patternUnits: "userSpaceOnUse", patternTransform: "rotate(45)" }, defs);
  mkEl("rect", { width: "24", height: "24", fill: "#fff" }, pat);
  mkEl("line", { x1: 0, y1: 0, x2: 0, y2: 24, stroke: "#c9c1b3", "stroke-width": 6 }, pat);
  const m = mkEl("marker", { id: "arr", viewBox: "0 0 10 10", refX: "9", refY: "5", markerWidth: "7", markerHeight: "7", orient: "auto-start-reverse" }, defs);
  mkEl("path", { d: "M 0 0 L 10 5 L 0 10 z", fill: "#d3541f" }, m);
  const mk = (tag, attrs, parent = svg) => mkEl(tag, attrs, parent);
  const sel = (layer, id) => o.selected && o.selected.layer === layer && o.selected.id === id ? " sel" : "";
  const click = (n, layer, id) => { if (o.onSelect) { n.style.cursor = "pointer"; n.addEventListener("click", ev => { ev.stopPropagation(); o.onSelect(layer, id); }); } };

  if (o.axes) {
    mk("line", { x1: SX(0), y1: SY(-40), x2: SX(2300), y2: SY(-40), class: "axis" });
    mk("line", { x1: SX(-40), y1: SY(0), x2: SX(-40), y2: SY(1050), class: "axis" });
    for (let x = 0; x <= 2000; x += 500) { mk("line", { x1: SX(x), y1: SY(-40), x2: SX(x), y2: SY(-60), class: "axis" }); const t = mk("text", { x: SX(x), y: SY(-95), class: "axis-label", "text-anchor": "middle" }); t.textContent = x; }
    for (let y = 0; y <= 1000; y += 500) { mk("line", { x1: SX(-40), y1: SY(y), x2: SX(-60), y2: SY(y), class: "axis" }); const t = mk("text", { x: SX(-75), y: SY(y) + 10, class: "axis-label", "text-anchor": "end" }); t.textContent = y; }
    const tx = mk("text", { x: SX(2320), y: SY(-52), class: "axis-label" }); tx.textContent = "x (m)";
    const ty = mk("text", { x: SX(-120), y: SY(1080), class: "axis-label" }); ty.textContent = "y (m)";
  }

  const draw = {
    wards() {
      TOWN.wards.forEach(w => {
        const p = mk("polygon", { points: ptsStr(w.ring), class: "ward " + (o.wardStyle === "outline" ? "nofill" : o.wardStyle === "solid" ? "solid" : "fill") + sel("wards", w.id) });
        click(p, "wards", w.id);
        if (o.labels) { const t = mk("text", { x: SX(w.id === "A" ? 500 : 1500), y: SY(940), class: "ward-label", "text-anchor": "middle" }); t.textContent = w.name; t.style.pointerEvents = "none"; }
      });
    },
    park() {
      const p = mk("path", { d: pathOf(TOWN.park.parts), class: "poly park" + sel("park", "PK-01") });
      click(p, "park", "PK-01");
      if (o.labels) { const t = mk("text", { x: SX(200), y: SY(15), class: "tag", "text-anchor": "middle" }); t.textContent = "PK-01 (part 1)"; const t2 = mk("text", { x: SX(1400), y: SY(15), class: "tag", "text-anchor": "middle" }); t2.textContent = "PK-01 (part 2)"; }
    },
    depot() {
      const h = mk("path", { d: pathOf([TOWN.depot.hole]), class: "hole-hatch" }); h.style.pointerEvents = "none";
      const p = mk("path", { d: pathOf([TOWN.depot.outer, TOWN.depot.hole]), class: "poly depot" + sel("depot", "DP-01") });
      click(p, "depot", "DP-01");
      if (o.labels) { const t = mk("text", { x: SX(1500), y: SY(820), class: "tag", "text-anchor": "middle" }); t.textContent = "DP-01"; }
    },
    schoolFoot() {
      const p = mk("polygon", { points: ptsStr(TOWN.school.ring), class: "poly school" + sel("schoolFoot", "SC-01") });
      click(p, "schoolFoot", "SC-01");
      if (o.labels) { const t = mk("text", { x: SX(400), y: SY(740), class: "tag", "text-anchor": "middle" }); t.textContent = "SC-01 footprint"; }
    },
    schoolPt() {
      const c = mk("circle", { cx: SX(400), cy: SY(700), r: 22, class: "schoolpt" + sel("schoolPt", "SC-01") });
      click(c, "schoolPt", "SC-01");
      if (o.labels) { const t = mk("text", { x: SX(440), y: SY(690), class: "tag" }); t.textContent = "SC-01"; }
    },
    roads() {
      TOWN.roads.forEach(r => {
        const l = mk("polyline", { points: ptsStr(r.pts), class: "line" + sel("roads", r.id) + (o.roadThin ? " thin" : "") });
        click(l, "roads", r.id);
        if (o.labels) { const lp = { R1: [1550, 540], R2: [520, 250], L1: [1370, 360] }[r.id]; const t = mk("text", { x: SX(lp[0]), y: SY(lp[1]), class: "road-label" }); t.textContent = r.id; t.style.pointerEvents = "none"; }
      });
      mk("line", { x1: SX(2000), y1: SY(470), x2: SX(2000), y2: SY(530), class: "line thin" });
    },
    assets() {
      TOWN.assets.forEach(a => {
        let n;
        if (a.type === "Tree") n = mk("circle", { cx: SX(a.x), cy: SY(a.y), r: 20, class: "tree" + sel("assets", a.id) });
        else n = mk("rect", { x: SX(a.x) - 16, y: SY(a.y) - 16, width: 32, height: 32, class: "asset" + sel("assets", a.id), transform: `rotate(45 ${SX(a.x)} ${SY(a.y)})` });
        click(n, "assets", a.id);
        if (o.labels) { const t = mk("text", { x: SX(a.x) + 26, y: SY(a.y) + 46, class: "asset-label" }); t.textContent = a.id; t.style.pointerEvents = "none"; }
      });
    },
    wardPts() {
      TOWN.wardPts.forEach(w => { const c = mk("circle", { cx: SX(w.x), cy: SY(w.y), r: 26, class: "wardpt" + sel("wardPts", w.id) }); click(c, "wardPts", w.id); if (o.labels) { const t = mk("text", { x: SX(w.x) + 34, y: SY(w.y) - 30, class: "req-label" }); t.textContent = "Ward " + w.id + " (point)"; } });
    },
    requests() {
      const rs = o.reqStyle || {};
      TOWN.requests.forEach(p => {
        const pos = o.moved[p.id] || (p.x == null ? null : [p.x, p.y]);
        if (!pos) return;
        if (rs.filter && !rs.filter(p)) return;
        let cls = "req" + sel("requests", p.id);
        const attrs = { cx: SX(pos[0]), cy: SY(pos[1]), r: rs.size || 26, class: cls, "data-id": p.id };
        if (rs.by === "category") attrs.style = `fill:${CAT_COL[p.category]}`;
        if (rs.by === "status") attrs.style = `fill:${STATUS_COL[p.status]}`;
        if (rs.dim && rs.dim.includes(p.id)) attrs.style = "fill:#c9c1b3";
        const c = mk("circle", attrs);
        click(c, "requests", p.id);
        const lab = rs.label === undefined ? (o.labels ? "id" : null) : rs.label;
        if (lab) { const t = mk("text", { x: SX(pos[0]) + 34, y: SY(pos[1]) - 24, class: "req-label" }); t.textContent = p[lab]; t.style.pointerEvents = "none"; }
      });
    }
  };
  o.order.forEach(k => { if (o.visible.includes(k) && draw[k]) draw[k](); });

  if (o.extentOf) {
    const e = layerExtent(o.extentOf, o.moved);
    if (e) { mk("rect", { x: SX(e.xmin), y: SY(e.ymax), width: Math.max(e.xmax - e.xmin, 1), height: Math.max(e.ymax - e.ymin, 1), class: "extent" }); }
  }
  if (o.vertices) {
    const vs = layerVertices(o.vertices);
    const r = o.vertexR || 14;
    vs.forEach((v, i) => { mk("circle", { cx: SX(v[0]), cy: SY(v[1]), r, class: "vertex" + (o.litVertex === i ? " lit" : ""), style: r < 10 ? "stroke-width:2" : "" }); if (o.vertexLabels) { const t = mk("text", { x: SX(v[0]) + 18, y: SY(v[1]) - 18, class: "vlabel" }); t.textContent = `(${v[0]}, ${v[1]})`; } if (o.vertexNumbers && i < vs.length - 1) { const t = mk("text", { x: SX(v[0]) + r + 3, y: SY(v[1]) - r - 2, class: "vlabel", style: `font-size:${r * 2.5}px;font-weight:600` }); t.textContent = i + 1; } });
  }
  if (o.extra) o.extra(mk, svg);
  el.innerHTML = ""; el.appendChild(svg);
  if (o.caption) { const c = document.createElement("figcaption"); c.textContent = o.caption; el.appendChild(c); }
  return svg;
}
function layerVertices(key) {
  if (key === "R1" || key === "R2" || key === "L1") return TOWN.roads.find(r => r.id === key).pts;
  if (key === "wardA") return [...TOWN.wards[0].ring, TOWN.wards[0].ring[0]];
  if (key === "schoolFoot") return [...TOWN.school.ring, TOWN.school.ring[0]];
  if (key === "depot") return [...TOWN.depot.outer, TOWN.depot.outer[0], ...TOWN.depot.hole, TOWN.depot.hole[0]];
  return [];
}
function layerExtent(key, moved = {}) {
  const pts = {
    wards: TOWN.wards.flatMap(w => w.ring),
    roads: TOWN.roads.flatMap(r => r.pts),
    requests: TOWN.requests.filter(p => p.x != null || moved[p.id]).map(p => moved[p.id] || [p.x, p.y]),
    assets: TOWN.assets.map(a => [a.x, a.y]),
    park: TOWN.park.parts.flat(),
    depot: TOWN.depot.outer,
    schoolFoot: TOWN.school.ring,
    schoolPt: [TOWN.school.pt],
    R2: TOWN.roads[1].pts
  }[key];
  return pts ? extentOf(pts) : null;
}

/* Navigation, progress, quizzes, sorters and tabs live in /assets/common.js (shared by every chapter). */

/* ---- attribute table helper ---- */
function requestRow(p, extra = "") {
  const g = p.x == null ? `<td class="nogeom">(no location)</td>` : `<td class="mono">(${p.x}, ${p.y})</td>`;
  return `<tr data-id="${p.id}" ${extra}><td class="mono">${p.id}</td>${g}<td>${p.category}</td><td>${p.priority}</td><td>${p.status}</td><td class="mono">${p.reported}</td></tr>`;
}

const REQ_HEAD = `<tr><th>request_id</th><th>geometry</th><th>category</th><th>priority</th><th>status</th><th>reported_on</th></tr>`;
