/* Chapter 12 tutorial — District G-12 practice data, classification maths and SVG map helpers.
   Everything here is MADE-UP practice data (blueprint Appendix A style). No real town, ward, resident or risk.
   Numbers match the chapter document GIS_Phase_1_Chapter_12_Cartography_and_Interpreting_Maps_Correctly.md. */

/* ---------- Fixture F12-1: District G-12, sixteen 1 km wards on the flat practice grid (no CRS) ---------- */
/* Rows are listed from the TOP (largest y). W01 = top-left square (0,3000)–(1000,4000); W16 = bottom-right. */
const G12 = {
  wards: [
    { id: "W01", col: 0, row: 0, open: 3,    hh: 1500,  road: 5 },
    { id: "W02", col: 1, row: 0, open: 2,    hh: 1000,  road: 4 },
    { id: "W03", col: 2, row: 0, open: 0,    hh: 500,   road: 2, note: "surveyed; no open requests" },
    { id: "W04", col: 3, row: 0, open: 4,    hh: 800,   road: 4 },
    { id: "W05", col: 0, row: 1, open: 5,    hh: 2000,  road: 6 },
    { id: "W06", col: 1, row: 1, open: 6,    hh: 1500,  road: 3, note: "includes G06 (on the edge; Policy W-1)" },
    { id: "W07", col: 2, row: 1, open: 7,    hh: 3500,  road: 7 },
    { id: "W08", col: 3, row: 1, open: 13,   hh: 2600,  road: 5 },
    { id: "W09", col: 0, row: 2, open: 14,   hh: 7000,  road: 10 },
    { id: "W10", col: 1, row: 2, open: 16,   hh: 4000,  road: 8 },
    { id: "W11", col: 2, row: 2, open: 17,   hh: 1700,  road: 4 },
    { id: "W12", col: 3, row: 2, open: 31,   hh: 2000,  road: 20 },
    { id: "W13", col: 0, row: 3, open: 35,   hh: 7000,  road: 12 },
    { id: "W14", col: 1, row: 3, open: 40,   hh: 10000, road: 15 },
    { id: "W15", col: 2, row: 3, open: 45,   hh: 9000,  road: 10 },
    { id: "W16", col: 3, row: 3, open: null, hh: 1000,  road: 3, note: "export not received — NO DATA" }
  ],
  /* next month, for the "comparing months" demo (12.4.3): W12 31→12, W15 45→20, W13 35→33, W14 40→38 */
  september: { W12: 12, W15: 20, W13: 33, W14: 38 }
};
G12.wards.forEach(w => {
  w.x0 = w.col * 1000; w.y0 = (3 - w.row) * 1000;                 // lower-left corner in grid metres
  w.rate = w.open == null ? null : w.open / w.hh * 1000;           // per 1,000 households
  w.perkm = w.open == null ? null : w.open / w.road;               // per km of road
});
const ward = id => G12.wards.find(w => w.id === id);
function g12Values(month) {                                        // {W01: 3, …} for August (default) or September
  const o = {}; G12.wards.forEach(w => { o[w.id] = w.open; }); if (month === "sep") Object.assign(o, G12.september); return o;
}

/* ---------- Fixture F12-2: the W06/W07 request list and Station Road ---------- */
const ROAD = { id: "SR-1", name: "Station Road", from: [1000, 2500], to: [3000, 2500] };
const REQ = [
  { id: "G01", x: 1100, y: 2100, cat: "Pothole",         pri: 2, status: "OPEN",   on: "2026-09-02", ref: "R-2201", note: "" },
  { id: "G02", x: 1200, y: 2700, cat: "Streetlight out", pri: 3, status: "OPEN",   on: "2026-09-04", ref: "R-2202", note: "Second lamp from the corner" },
  { id: "G03", x: 1210, y: 2695, cat: "Streetlight out", pri: 3, status: "OPEN",   on: "2026-09-04", ref: "R-2202", note: "Same street, next lamp" },
  { id: "G04", x: 1500, y: 2480, cat: "Blocked drain",   pri: 1, status: "OPEN",   on: "2026-09-10", ref: "R-2203", note: "Water on the road after rain" },
  { id: "G05", x: 1850, y: 2950, cat: "Pothole",         pri: 2, status: "OPEN",   on: "2026-08-28", ref: "R-2204", note: "" },
  { id: "G06", x: 2000, y: 2400, cat: "Fallen tree",     pri: 1, status: "OPEN",   on: "2026-09-12", ref: "R-2205", note: "" },
  { id: "G07", x: 2100, y: 2900, cat: "Pothole",         pri: 3, status: "OPEN",   on: "2026-08-30", ref: "R-2206", note: "" },
  { id: "G08", x: 2300, y: 2550, cat: "Streetlight out", pri: 3, status: "OPEN",   on: "2026-09-01", ref: "R-2207", note: "Reporter says her father, 82, walks here at night — house no. 14" },
  { id: "G09", x: 2500, y: 2200, cat: "Blocked drain",   pri: 1, status: "OPEN",   on: "2026-09-11", ref: "R-2208", note: "" },
  { id: "G10", x: 2600, y: 2500, cat: "Pothole",         pri: 2, status: "OPEN",   on: "2026-09-06", ref: "R-2209", note: "" },
  { id: "G11", x: 2750, y: 2750, cat: "Water leak",      pri: 1, status: "OPEN",   on: "2026-09-13", ref: "R-2210", note: "" },
  { id: "G12", x: 2900, y: 2100, cat: "Streetlight out", pri: 3, status: "OPEN",   on: "2026-08-26", ref: "R-2211", note: "" },
  { id: "G13", x: 2950, y: 2950, cat: "Pothole",         pri: 2, status: "OPEN",   on: "2026-09-08", ref: "R-2212", note: "" },
  { id: "G14", x: 1400, y: 2200, cat: "Pothole",         pri: 2, status: "CLOSED", on: "2026-08-10", ref: "R-2213", note: "" },
  { id: "G15", x: 2200, y: 2300, cat: "Water leak",      pri: 1, status: "CLOSED", on: "2026-08-14", ref: "R-2214", note: "" },
  { id: "G16", x: 2850, y: 2600, cat: "Blocked drain",   pri: 1, status: "CLOSED", on: "2026-08-20", ref: "R-2215", note: "" }
];
const CATS = ["Pothole", "Streetlight out", "Blocked drain", "Fallen tree", "Water leak"];
const CAT_SHAPE = { "Pothole": "circle", "Streetlight out": "square", "Blocked drain": "triangle", "Fallen tree": "diamond", "Water leak": "star" };
const CAT_COLOR = { "Pothole": "#d3541f", "Streetlight out": "#5b7ea3", "Blocked drain": "#2f7d4f", "Fallen tree": "#8a6a12", "Water leak": "#7a3e9d" };

/* ---------- small maths ---------- */
const fmtN = (n, d = 0) => n.toLocaleString("en-IN", { minimumFractionDigits: d, maximumFractionDigits: d });
const PT_MM = 25.4 / 72;                                            // one point = 0.3528 mm
function footprintM(pt, scaleDen) { return pt * PT_MM * scaleDen / 1000; }   // ground width of a symbol, metres
function rf(groundM, paperMm) { return groundM * 1000 / paperMm; }           // representative-fraction denominator

/* ---------- classification ---------- */
/* All three return the UPPER bound of every class, k values, last one = max. A value belongs to the first class whose upper bound is ≥ the value. */
function equalInterval(vals, k) { const mn = Math.min(...vals), mx = Math.max(...vals), w = (mx - mn) / k; return Array.from({ length: k }, (_, i) => i === k - 1 ? mx : mn + w * (i + 1)); }
function quantile(vals, k) {                                        // equal count per class; if n/k is not whole, the earlier classes get the extra
  const s = [...vals].sort((a, b) => a - b), n = s.length, out = []; let start = 0;
  for (let i = 0; i < k; i++) { const size = Math.ceil((n - start) / (k - i)); const end = start + size; out.push(s[Math.min(end, n) - 1]); start = end; }
  out[k - 1] = s[n - 1]; return out;
}
function jenks(vals, k) {                                           // classic Jenks natural breaks (minimise within-class squared deviation)
  const d = [...vals].sort((a, b) => a - b), n = d.length;
  const LC = Array.from({ length: n + 1 }, () => Array(k + 1).fill(0)), V = Array.from({ length: n + 1 }, () => Array(k + 1).fill(0));
  for (let i = 1; i <= k; i++) { LC[1][i] = 1; V[1][i] = 0; for (let j = 2; j <= n; j++) V[j][i] = Infinity; }
  for (let l = 2; l <= n; l++) {
    let s1 = 0, s2 = 0, w = 0;
    for (let m = 1; m <= l; m++) {
      const i3 = l - m + 1, val = d[i3 - 1]; s2 += val * val; s1 += val; w++;
      const v = s2 - s1 * s1 / w, i4 = i3 - 1;
      if (i4 !== 0) for (let j = 2; j <= k; j++) if (V[l][j] >= v + V[i4][j - 1]) { LC[l][j] = i3; V[l][j] = v + V[i4][j - 1]; }
    }
    LC[l][1] = 1; V[l][1] = s2 - s1 * s1 / w;
  }
  const out = Array(k); let kk = n; out[k - 1] = d[n - 1];
  for (let j = k; j >= 2; j--) { const id = LC[kk][j] - 2; out[j - 2] = d[id]; kk = LC[kk][j] - 1; }
  return out;
}
function classOf(v, breaks) { for (let i = 0; i < breaks.length; i++) if (v <= breaks[i] + 1e-9) return i; return breaks.length - 1; }
function sdcm(vals, breaks) {                                       // within-class sum of squared deviations (the number Jenks minimises)
  let t = 0; breaks.forEach((b, i) => { const g = vals.filter(v => classOf(v, breaks) === i); if (!g.length) return; const m = g.reduce((a, c) => a + c, 0) / g.length; g.forEach(v => t += (v - m) ** 2); }); return t;
}
function classLabels(vals, breaks, unit) {                          // "0 – 7", "13 – 17", … using the values actually present (gaps visible)
  return breaks.map((b, i) => { const g = vals.filter(v => classOf(v, breaks) === i); if (!g.length) return "(empty)"; const lo = Math.min(...g), hi = Math.max(...g); const f = x => Number.isInteger(x) ? String(x) : x.toFixed(1); return (lo === hi ? f(lo) : f(lo) + " – " + f(hi)) + (unit ? " " + unit : ""); });
}
const RAMP = { 2: ["#fbe3cf", "#a33e13"], 3: ["#fbe3cf", "#e78a4e", "#8a2f0b"], 4: ["#fbe3cf", "#f0a86f", "#d3541f", "#7a2a0b"], 5: ["#fbe3cf", "#f4b98a", "#e78a4e", "#c2461a", "#6e2308"] };
const BADRAMP = ["#3fa34d", "#c6d94a", "#f0a030", "#c8282a"];      // the green→red ramp of the "bad map" (used only to show why it is bad)

/* ---------- SVG helpers ---------- */
const NS = "http://www.w3.org/2000/svg";
function mkEl(tag, attrs, parent) { const n = document.createElementNS(NS, tag); for (const k in attrs) n.setAttribute(k, attrs[k]); if (parent) parent.appendChild(n); return n; }
function txt(parent, x, y, s, cls = "lbl", extra = {}) { const t = mkEl("text", Object.assign({ x, y, class: cls }, extra), parent); t.textContent = s; return t; }
function newSvg(el, viewBox, label) { const svg = mkEl("svg", { viewBox, role: "img", "aria-label": label }); el.innerHTML = ""; el.appendChild(svg); return svg; }
function addHatch(svg, id = "hatch12") {
  const defs = mkEl("defs", {}, svg);
  const p = mkEl("pattern", { id, width: 36, height: 36, patternUnits: "userSpaceOnUse", patternTransform: "rotate(45)" }, defs);
  mkEl("rect", { width: 36, height: 36, fill: "#ffffff" }, p);
  mkEl("rect", { width: 12, height: 36, fill: "#9aa0ad" }, p);
  return defs;
}
function caption(el, s) { const c = document.createElement("figcaption"); c.textContent = s; el.appendChild(c); }

/* Draw the 4 × 4 ward grid. style(w) returns { fill, hatch, label (text under the ward code), stroke } */
function renderG12(el, opts = {}) {
  const o = Object.assign({ style: () => ({ fill: "#fff" }), title: null, caption: null, labels: true, gray: false, small: false }, opts);
  const S = 1000, pad = 60, cell = 220, W = pad * 2 + cell * 4, H = pad * 2 + cell * 4 + (o.title ? 70 : 0);
  const svg = newSvg(el, `0 0 ${W} ${H}`, "Schematic map of made-up District G-12: sixteen square wards in a 4 by 4 grid.");
  addHatch(svg);
  const top = pad + (o.title ? 70 : 0);
  if (o.title) txt(svg, W / 2, pad + 10, o.title, "lbl title", { "text-anchor": "middle" });
  G12.wards.forEach(w => {
    const st = o.style(w) || {}; const x = pad + w.col * cell, y = top + w.row * cell;
    mkEl("rect", { x, y, width: cell, height: cell, fill: st.hatch ? "url(#hatch12)" : (st.fill || "#fff"), stroke: st.stroke || "#1f2a44", "stroke-width": st.strokeW || 3, class: "wardsq" }, svg);
    if (o.labels) {
      txt(svg, x + cell / 2, y + cell / 2 - (st.label != null ? 14 : -10), w.id, "lbl code" + (st.dark ? " onDark" : ""), { "text-anchor": "middle" });
      if (st.label != null) txt(svg, x + cell / 2, y + cell / 2 + 34, String(st.label), "lbl val" + (st.dark ? " onDark" : ""), { "text-anchor": "middle" });
    }
  });
  txt(svg, pad, H - 18, "made-up practice grid · metres · not oriented to north", "lbl warn small");
  txt(svg, W - pad, H - 18, "1 square = 1 km × 1 km", "lbl small", { "text-anchor": "end" });
  el.classList.toggle("gray", !!o.gray);
  if (o.caption) caption(el, o.caption);
  return svg;
}
function isDark(hex) { const c = hex.replace("#", ""); const r = parseInt(c.substr(0, 2), 16), g = parseInt(c.substr(2, 2), 16), b = parseInt(c.substr(4, 2), 16); return (r * 299 + g * 587 + b * 114) / 1000 < 120; }

/* Draw a marker of a given shape centred at (cx, cy) with "radius" r (in the SVG's units). */
function marker(svg, shape, cx, cy, r, attrs) {
  const a = Object.assign({}, attrs);
  if (shape === "circle") return mkEl("circle", Object.assign({ cx, cy, r }, a), svg);
  if (shape === "square") return mkEl("rect", Object.assign({ x: cx - r, y: cy - r, width: 2 * r, height: 2 * r }, a), svg);
  const pts = shape === "triangle" ? [[cx, cy - r], [cx + r, cy + r * .8], [cx - r, cy + r * .8]]
    : shape === "diamond" ? [[cx, cy - r * 1.15], [cx + r, cy], [cx, cy + r * 1.15], [cx - r, cy]]
    : Array.from({ length: 10 }, (_, i) => { const ang = -Math.PI / 2 + i * Math.PI / 5, rr = i % 2 ? r * .45 : r; return [cx + rr * Math.cos(ang), cy + rr * Math.sin(ang)]; });
  return mkEl("polygon", Object.assign({ points: pts.map(p => p.join(",")).join(" ") }, a), svg);
}

/* Draw the W06/W07 crew map. The SVG's units are grid METRES, so symbol size in metres is honest. */
function renderCrew(el, opts = {}) {
  const o = Object.assign({ pt: 12, scale: 25000, showClosed: "hollow", shapes: true, sizeByPriority: true, labels: true, gray: false, noteLabels: false, wardFill: false, caption: null, window: [1000, 2000, 3000, 3000], hue: true }, opts);
  const [x0, y0, x1, y1] = o.window, W = x1 - x0, H = y1 - y0, pad = 80;
  const svg = newSvg(el, `${x0 - pad} ${-(y1) - pad} ${W + 2 * pad} ${H + 2 * pad + 60}`, "Schematic crew map of wards W06 and W07: Station Road and the request points, drawn to scale on the practice grid.");
  const X = x => x, Y = y => -y;
  ["W06", "W07"].forEach(id => { const w = ward(id); mkEl("rect", { x: X(w.x0), y: Y(w.y0 + 1000), width: 1000, height: 1000, class: "wardsq", fill: o.wardFill ? "#dbe7f3" : "#fff" }, svg); txt(svg, X(w.x0) + 30, Y(w.y0 + 1000) + 60, id, "lbl code faint"); });
  mkEl("line", { x1: X(ROAD.from[0]), y1: Y(ROAD.from[1]), x2: X(ROAD.to[0]), y2: Y(ROAD.to[1]), class: "road" }, svg);
  txt(svg, X((x0 + x1) / 2), Y(2500) - 40, ROAD.name, "lbl road", { "text-anchor": "middle" });
  const r0 = footprintM(o.pt, o.scale) / 2;                          // symbol radius in metres at this scale
  REQ.forEach(q => {
    if (q.status === "CLOSED" && o.showClosed === "hide") return;
    const r = o.sizeByPriority ? r0 * (q.pri === 1 ? 1.35 : q.pri === 2 ? 1 : .7) : r0;
    const shape = o.shapes ? CAT_SHAPE[q.cat] : "circle";
    const fill = q.status === "CLOSED" ? "#fff" : (o.hue ? CAT_COLOR[q.cat] : "#1f2a44");
    marker(svg, shape, X(q.x), Y(q.y), r, { fill, stroke: q.status === "CLOSED" ? "#8b93a7" : "#1f2a44", "stroke-width": Math.max(2, r * .1), class: "reqm" });
    if (o.labels) txt(svg, X(q.x) + r + 6, Y(q.y) - r - 4, q.id, "lbl id");
    if (o.noteLabels && q.note) txt(svg, X(q.x) + r + 6, Y(q.y) + r + 26, q.note, "lbl note");
  });
  mkEl("line", { x1: X(x0), y1: Y(y0) + 50, x2: X(x0 + 500), y2: Y(y0) + 50, class: "sbar" }, svg);
  txt(svg, X(x0), Y(y0) + 40, "500 m on the practice grid", "lbl small");
  txt(svg, X(x1), Y(y0) + 40, `symbol ${o.pt} pt at 1:${fmtN(o.scale)} covers ${fmtN(footprintM(o.pt, o.scale))} m on the ground`, "lbl small", { "text-anchor": "end" });
  el.classList.toggle("gray", !!o.gray);
  if (o.caption) caption(el, o.caption);
  return svg;
}

/* HTML legend for a classified map: rows of swatch + label, plus an optional no-data row */
function legendHtml(colors, labels, extra) {
  return `<div class="leg">${labels.map((l, i) => `<div class="row"><span class="sw" style="background:${colors[i]}"></span><span>${l}</span></div>`).join("")}${extra ? `<div class="row"><span class="sw hatch"></span><span>${extra}</span></div>` : ""}</div>`;
}

if (typeof module !== "undefined") module.exports = { G12, REQ, equalInterval, quantile, jenks, classOf, sdcm, classLabels, footprintM, rf, g12Values };
