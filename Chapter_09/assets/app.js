/* Chapter 9 tutorial — the damaged practice town, small geometry helpers and SVG drawing.
   Everything here is MADE-UP practice data on a flat grid in metres (no real place, no CRS).
   Pages call the render helpers from their own pageInit(). */

/* ---------- fixture: the Chapter 9 damaged package (as delivered) ---------- */
const T9 = {
  wards: [
    { id: "A", name: "Ward A", ring: [[0, 0], [1000, 0], [1000, 1000], [0, 1000]] },
    { id: "B", name: "Ward B", ring: [[1004, 0], [2000, 0], [2000, 1000], [1000, 1000]] },     // DEFECT: first corner slipped 4 m east
    { id: "C", name: "Ward C", ring: [[0, 1000], [1000, 1000], [0, 1500], [1000, 1500]] }     // DEFECT: corners typed in the wrong order (bow-tie)
  ],
  wardsFixed: {
    B: [[1000, 0], [2000, 0], [2000, 1000], [1000, 1000]],
    C: [[0, 1000], [1000, 1000], [1000, 1500], [0, 1500]],
    Crepair: [[[0, 1000], [1000, 1000], [500, 1250]], [[0, 1500], [1000, 1500], [500, 1250]]]  // what an automatic repair would produce
  },
  roads: [
    { id: "R1", name: "Main Road", width: 12, pts: [[0, 500], [2000, 500]] },
    { id: "R2", name: "Station Road", width: 7, pts: [[500, 0], [500, 500], [800, 900]] },
    { id: "L1", name: "Bus turning loop", width: 6, pts: [[1300, 300], [1350, 300], [1350, 350], [1300, 350], [1300, 300]] },
    { id: "R4", name: "Temple Lane", width: 5, pts: [[300, 300], [300, 497]] },                // DEFECT: stops 3 m short of Main Road
    { id: "R5", name: "Depot Access", width: 6, pts: [[1500, 500], [1500, 600]] }             // NOT a defect: dead end at the depot gate
  ],
  depot: { outer: [[1400, 600], [1600, 600], [1600, 800], [1400, 800]], hole: [[1450, 650], [1550, 650], [1550, 750], [1450, 750]] },
  assets: [
    { id: "SL-0113", type: "SL", x: 205, y: 195, year: 2018, status: "ACTIVE", method: "SURVEY", h: null },
    { id: "DR-0042", type: "DR", x: 995, y: 510, year: 2011, status: "ACTIVE", method: "SURVEY", h: null },
    { id: "TR-0301", type: "TR", x: 2190, y: 520, year: 2005, status: "ACTIVE", method: "APPROX", h: null },
    { id: "TR-0302", type: "TR", x: 1500, y: 700, year: 2019, status: "ACTIVE", method: "SURVEY", h: null },
    { id: "SL-0114", type: "SL", x: 260, y: 190, year: 2024, status: "ACTIVE", method: "DIGITISED", h: 6.5 },
    { id: "SL-0127", type: "Lamp", x: 600, y: 480, year: 2020, status: "ACTIVE", method: "DIGITISED", h: 22 },   // DEFECTS: type not in list; 22 is feet
    { id: "DR-0043", type: "DR", x: 1450, y: 250, year: 2016, status: "ACTIVE", method: "SURVEY", h: null },
    { id: "SL-0250", type: "SL", x: 1800, y: 700, year: 2012, status: "REMOVED", method: "SURVEY", h: 8 },
    { id: "TR-0303", type: "TR", x: 400, y: 900, year: 2021, status: "ACTIVE", method: "APPROX", h: null },
    { id: "SL-0114", type: "SL", x: 1250, y: 420, year: 2025, status: "ACTIVE", method: "GNSS", h: 7 }         // DEFECT: same ID as another row
  ],
  teams: [{ id: "T-N", name: "North crew" }, { id: "T-S", name: "South crew" }],
  inspections: [
    { id: "INS-0001", asset: "DR-0042", at: "2024-10-15T04:20Z", entered: "2024-10-15", team: "T-S", cond: 3, defects: 0, remarks: "" },
    { id: "INS-0002", asset: "DR-0042", at: "2025-11-02T09:35Z", entered: "2025-11-02", team: "T-S", cond: 2, defects: 2, remarks: "Grating cracked; silt" },
    { id: "INS-0003", asset: "SL-0113", at: "2026-03-14T05:12Z", entered: "2026-03-14", team: "T-N", cond: 3, defects: 1, remarks: "Lamp flickers at dusk" },
    { id: "INS-0004", asset: "SL-0113", at: "2025-09-14T05:45Z", entered: "2026-03-16", team: "T-N", cond: 3, defects: 0, remarks: "" },
    { id: "INS-0005", asset: "SL-0250", at: "2026-08-01T04:30Z", entered: "2026-08-01", team: "T-S", cond: null, defects: 0, remarks: "Pole leaning; condition not scored" },
    { id: "INS-0006", asset: "DR-0044", at: "2026-09-05T03:50Z", entered: "2026-09-05", team: "T-S", cond: 4, defects: 0, remarks: "" },
    { id: "INS-0007", asset: "DR-0043", at: "2026-09-05T04:40Z", entered: "2026-09-05", team: "T-S", cond: 3, defects: 1, remarks: "Cover chipped" }
  ],
  requests: [
    { id: "P1", x: 200, y: 200, cat: "Streetlight out", pri: "Medium", status: "Open", on: "2026-09-02", time: "20:15", who: "R-1180", closed: "", via: "Mobile app", note: "" },
    { id: "P2", x: 800, y: 800, cat: "Pothole", pri: "High", status: "In progress", on: "2026-08-28", time: "08:40", who: "R-2231", closed: "", via: "Phone", note: "Crew assigned 2026-09-01" },
    { id: "P3", x: 1200, y: 250, cat: "Water leak", pri: "High", status: "Open", on: "2026-09-10", time: "09:13", who: "R-3391", closed: "", via: "Mobile app", note: "Water on road near shop" },
    { id: "P4", x: 1700, y: 900, cat: "Pothole", pri: "Low", status: "Resolved", on: "2026-08-15", time: "17:02", who: "R-0450", closed: "2026-08-20", via: "Web form", note: "" },
    { id: "P5", x: 1000, y: 500, cat: "Blocked drain", pri: "Medium", status: "Reopened", on: "2026-08-30", time: "07:55", who: "R-2790", closed: "", via: "Phone", note: "Closed 2026-09-05; reopened 2026-09-12" },
    { id: "P6", x: 2200, y: 500, cat: "Fallen tree", pri: "High", status: "Closed - duplicate", on: "2026-09-11", time: "06:30", who: "R-1902", closed: "2026-09-12", via: "Phone", note: "Duplicate of a report not in this package" },
    { id: "P7", x: null, y: null, cat: "Pothole", pri: "Medium", status: "Open", on: "2026-09-14", time: "11:20", who: "R-3010", closed: "", via: "Phone", note: "Caller could not give a location; to be located" },
    { id: "P8", x: 1200, y: 250, cat: "Water leak", pri: "High", status: "Open", on: "2026-09-10", time: "09:14", who: "R-3391", closed: "", via: "Mobile app", note: "Water on road near shop" },
    { id: "P9", x: 1000, y: 500, cat: "Streetlight out", pri: "Low", status: "Open", on: "2026-09-13", time: "19:45", who: "R-0077", closed: "", via: "Mobile app", note: "" }
  ],
  domains: { type: ["SL", "DR", "TR"], status: ["ACTIVE", "REMOVED", "MERGED"], method: ["SURVEY", "GNSS", "DIGITISED", "APPROX"], height: [3, 15], cond: [1, 5],
             reqStatus: ["Open", "In progress", "Resolved", "Reopened", "Closed - duplicate"] }
};

/* ---------- small geometry helpers (flat grid, metres) ---------- */
const dist = (a, b) => Math.hypot(a[0] - b[0], a[1] - b[1]);
function shoelace(ring) { let s = 0; for (let i = 0; i < ring.length; i++) { const [x1, y1] = ring[i], [x2, y2] = ring[(i + 1) % ring.length]; s += x1 * y2 - x2 * y1; } return s / 2; }
function segIntersect(p1, p2, p3, p4) {                  // proper crossing of two segments, or null
  const d = (p2[0] - p1[0]) * (p4[1] - p3[1]) - (p2[1] - p1[1]) * (p4[0] - p3[0]);
  if (Math.abs(d) < 1e-9) return null;
  const t = ((p3[0] - p1[0]) * (p4[1] - p3[1]) - (p3[1] - p1[1]) * (p4[0] - p3[0])) / d;
  const u = ((p3[0] - p1[0]) * (p2[1] - p1[1]) - (p3[1] - p1[1]) * (p2[0] - p1[0])) / d;
  if (t > 1e-9 && t < 1 - 1e-9 && u > 1e-9 && u < 1 - 1e-9) return [p1[0] + t * (p2[0] - p1[0]), p1[1] + t * (p2[1] - p1[1])];
  return null;
}
function ringSelfCrossings(ring) {                        // non-adjacent edges that cross each other
  const n = ring.length, out = [];
  for (let i = 0; i < n; i++) for (let j = i + 2; j < n; j++) {
    if (i === 0 && j === n - 1) continue;
    const p = segIntersect(ring[i], ring[(i + 1) % n], ring[j], ring[(j + 1) % n]);
    if (p) out.push({ i, j, p });
  }
  return out;
}
function pointInRing(pt, ring) {                          // strict inside test (ray casting); boundary points are "not inside"
  let inside = false; const [x, y] = pt;
  for (let i = 0, j = ring.length - 1; i < ring.length; j = i++) {
    const [xi, yi] = ring[i], [xj, yj] = ring[j];
    if ((yi > y) !== (yj > y) && x < (xj - xi) * (y - yi) / (yj - yi) + xi) inside = !inside;
  }
  return inside;
}
function pointSegDist(p, a, b) {
  const dx = b[0] - a[0], dy = b[1] - a[1], l2 = dx * dx + dy * dy;
  let t = l2 ? ((p[0] - a[0]) * dx + (p[1] - a[1]) * dy) / l2 : 0; t = Math.max(0, Math.min(1, t));
  return dist(p, [a[0] + t * dx, a[1] + t * dy]);
}
const fmt = n => n.toLocaleString("en-IN");
const fx = (n, d = 2) => Number(n).toLocaleString("en-IN", { minimumFractionDigits: d, maximumFractionDigits: d });

/* ---------- SVG town drawing ----------
   renderTown(el, opts): opts.fixB / opts.fixC / opts.repairC / opts.fixR4 switch the corrected shapes on;
   opts.show = array of layer names to draw: "wards","roads","depot","assets","requests"; opts.marks = extra callouts. */
function renderTown(el, opts = {}) {
  const W = 2400, H = 1700, pad = 120;                    // grid 0..2300 × 0..1600 in a 2400×1700 view (y flipped)
  const sx = x => pad + x, sy = y => H - pad - y;
  const show = new Set(opts.show || ["wards", "roads", "depot", "assets", "requests"]);
  let s = `<svg viewBox="0 0 ${W} ${H}" role="img" aria-label="${opts.aria || "Practice town on a flat grid"}">`;
  // axes
  s += `<line class="axis" x1="${sx(0)}" y1="${sy(0)}" x2="${sx(2300)}" y2="${sy(0)}"/><line class="axis" x1="${sx(0)}" y1="${sy(0)}" x2="${sx(0)}" y2="${sy(1550)}"/>`;
  for (let x = 0; x <= 2000; x += 500) s += `<text class="axis-label" x="${sx(x) - 30}" y="${sy(0) + 50}">${x}</text>`;
  for (let y = 500; y <= 1500; y += 500) s += `<text class="axis-label" x="${sx(0) - 105}" y="${sy(y) + 12}">${y}</text>`;
  s += `<text class="axis-label" x="${sx(2100)}" y="${sy(0) + 50}">x (m)</text><text class="axis-label" x="${sx(0) - 105}" y="${sy(1550) - 10}">y (m)</text>`;
  if (show.has("wards")) {
    T9.wards.forEach(w => {
      let ring = w.ring, cls = "ward";
      if (w.id === "B" && opts.fixB) ring = T9.wardsFixed.B;
      if (w.id === "C") {
        if (opts.repairC) { T9.wardsFixed.Crepair.forEach(r => { s += `<polygon class="ward repaired" points="${r.map(p => sx(p[0]) + "," + sy(p[1])).join(" ")}"/>`; }); s += `<text class="ward-label" x="${sx(430)}" y="${sy(1230)}">C</text>`; return; }
        if (opts.fixC) ring = T9.wardsFixed.C; else cls = "ward bad";
      }
      s += `<polygon class="${cls}" points="${ring.map(p => sx(p[0]) + "," + sy(p[1])).join(" ")}"/>`;
      const c = w.id === "C" ? [430, 1230] : w.id === "A" ? [440, 720] : [1460, 880];
      s += `<text class="ward-label" x="${sx(c[0])}" y="${sy(c[1])}">${w.id}</text>`;
    });
    if (opts.showGap && !opts.fixB) s += `<polygon class="gapfill" points="${[[1000, 0], [1004, 0], [1000, 1000]].map(p => sx(p[0]) + "," + sy(p[1])).join(" ")}"/>`;
  }
  if (show.has("depot")) {
    const o = T9.depot.outer, h = T9.depot.hole;
    s += `<path class="depotp" fill-rule="evenodd" d="M${o.map(p => sx(p[0]) + " " + sy(p[1])).join("L")}Z M${h.map(p => sx(p[0]) + " " + sy(p[1])).join("L")}Z"/>`;
    s += `<text class="note-text" x="${sx(1405)}" y="${sy(815)}">Depot</text>`;
  }
  if (show.has("roads")) {
    T9.roads.forEach(r => {
      let pts = r.pts; if (r.id === "R4" && opts.fixR4) pts = [[300, 300], [300, 500]];
      s += `<polyline class="road ${r.id === "R4" && !opts.fixR4 ? "bad" : ""}" fill="none" points="${pts.map(p => sx(p[0]) + "," + sy(p[1])).join(" ")}"/>`;
    });
    s += `<text class="road-label" x="${sx(1250)}" y="${sy(500) - 22}">R1 Main Road</text><text class="road-label" x="${sx(520)}" y="${sy(150)}">R2</text><text class="road-label" x="${sx(1370)}" y="${sy(300)}">L1</text><text class="road-label" x="${sx(215)}" y="${sy(380)}">R4</text><text class="road-label" x="${sx(1520)}" y="${sy(560)}">R5</text>`;
  }
  if (show.has("assets")) {
    T9.assets.forEach(a => { s += `<circle class="asset ${a.type === "Lamp" ? "odd" : ""}" cx="${sx(a.x)}" cy="${sy(a.y)}" r="16"/>`; if (!opts.lite) s += `<text class="asset-label" x="${sx(a.x) + 22}" y="${sy(a.y) - 14}">${a.id}</text>`; });
  }
  if (show.has("requests")) {
    T9.requests.filter(q => q.x != null).forEach(q => { const off = q.id === "P8" ? 40 : q.id === "P9" ? 40 : 0; s += `<circle class="req" cx="${sx(q.x)}" cy="${sy(q.y)}" r="18"/>`; if (!opts.lite || off) s += `<text class="req-label" x="${sx(q.x) + 24}" y="${sy(q.y) + 14 + off}">${q.id}</text>`; });
  }
  (opts.marks || []).forEach(m => { s += `<circle class="mark" cx="${sx(m.x)}" cy="${sy(m.y)}" r="${m.r || 46}"/>`; if (m.t) s += `<text class="mark-text" x="${sx(m.x) + (m.dx ?? 55)}" y="${sy(m.y) + (m.dy ?? 12)}">${m.t}</text>`; });
  s += `</svg>`;
  el.innerHTML = s + (opts.caption ? `<figcaption>${opts.caption}</figcaption>` : "");
}

/* ---------- Ward C close-up (three states) ---------- */
function renderWardC(el, state) {                         // state: "typed" | "fixed" | "repair"
  const W = 1200, H = 720, pad = 90; const sx = x => pad + x, sy = y => H - pad - (y - 1000);
  const ring = state === "fixed" ? T9.wardsFixed.C : T9.wards[2].ring;
  let s = `<svg viewBox="0 0 ${W} ${H}" role="img" aria-label="Ward C drawn three ways">`;
  if (state === "repair") T9.wardsFixed.Crepair.forEach(r => { s += `<polygon class="ward repaired" points="${r.map(p => sx(p[0]) + "," + sy(p[1])).join(" ")}"/>`; });
  else s += `<polygon class="ward ${state === "typed" ? "bad" : ""}" points="${ring.map(p => sx(p[0]) + "," + sy(p[1])).join(" ")}"/>`;
  const verts = state === "repair" ? [] : ring;
  verts.forEach((p, i) => { const r = p[0] > 500; s += `<circle class="vtx" cx="${sx(p[0])}" cy="${sy(p[1])}" r="14"/><text class="vlabel" x="${sx(p[0]) + (r ? -22 : 22)}" y="${sy(p[1]) + (p[1] > 1250 ? -24 : 44)}" text-anchor="${r ? "end" : "start"}">${i + 1} (${p[0]}, ${p[1]})</text>`; });
  if (state === "typed") s += `<circle class="crossmark" cx="${sx(500)}" cy="${sy(1250)}" r="22"/><text class="mark-text" x="${sx(530)}" y="${sy(1250) + 10}">edges cross at (500, 1250)</text>`;
  if (state === "repair") s += `<text class="mark-text" x="${sx(330)}" y="${sy(1110)}">triangle 125,000 m²</text><text class="mark-text" x="${sx(330)}" y="${sy(1400)}">triangle 125,000 m²</text>`;
  s += `</svg>`;
  el.innerHTML = s;
}

/* ---------- georeferencing example (scan 600×600 px; truth x = 2c − 100, y = 1100 − 2r) ---------- */
const GEO = {
  gcps: [{ id: "G1", c: 150, r: 50, x: 200, y: 1000 }, { id: "G2", c: 550, r: 50, x: 1000, y: 1000 }, { id: "G3", c: 150, r: 450, x: 200, y: 200 }],
  check: { id: "K1", c: 550, r: 456, x: 1000, y: 200 },   // the benchmark really is at (1000, 200) but the stretched scan shows it 6 rows lower
  fit3: (c, r) => ({ x: 2 * c - 100, y: 1100 - 2 * r })
};
/* least-squares affine through any list of points {c, r, x, y} — used for the "add K1 as a 4th point" demo */
function fitAffine(pts) {
  const n = pts.length, m = k => pts.reduce((a, p) => a + p[k], 0) / n;
  const cb = m("c"), rb = m("r"), xb = m("x"), yb = m("y");
  let Scc = 0, Srr = 0, Scr = 0, Scx = 0, Srx = 0, Scy = 0, Sry = 0;
  pts.forEach(p => { const dc = p.c - cb, dr = p.r - rb; Scc += dc * dc; Srr += dr * dr; Scr += dc * dr; Scx += dc * (p.x - xb); Srx += dr * (p.x - xb); Scy += dc * (p.y - yb); Sry += dr * (p.y - yb); });
  const det = Scc * Srr - Scr * Scr;
  const bx = (Scx * Srr - Scr * Srx) / det, dx = (Scc * Srx - Scr * Scx) / det;
  const by = (Scy * Srr - Scr * Sry) / det, dy = (Scc * Sry - Scr * Scy) / det;
  const ax = xb - bx * cb - dx * rb, ay = yb - by * cb - dy * rb;
  const f = (c, r) => ({ x: ax + bx * c + dx * r, y: ay + by * c + dy * r });
  const res = pts.map(p => { const q = f(p.c, p.r); return { id: p.id, ex: p.x - q.x, ey: p.y - q.y }; });
  const rms = Math.sqrt(res.reduce((a, e) => a + e.ex * e.ex + e.ey * e.ey, 0) / n);
  return { f, res, rms };
}
function renderScan(el, opts = {}) {
  const W = 760, H = 700, ox = 80, oy = 60, sc = 1.0;   // scan drawn 600×600 at 1 px = 1 unit
  const px = c => ox + c * sc, py = r => oy + r * sc;
  let s = `<svg viewBox="0 0 ${W} ${H}" role="img" aria-label="A scanned plan with control points">`;
  s += `<rect class="scanpaper" x="${ox}" y="${oy}" width="600" height="600"/>`;
  for (let i = 0; i <= 600; i += 100) s += `<line class="scangrid" x1="${px(i)}" y1="${oy}" x2="${px(i)}" y2="${oy + 600}"/><line class="scangrid" x1="${ox}" y1="${py(i)}" x2="${ox + 600}" y2="${py(i)}"/>`;
  s += `<text class="axis-label" x="${ox}" y="${oy - 18}">column c → (0 at left)</text><text class="axis-label" transform="translate(${ox - 22} ${oy + 300}) rotate(-90)" text-anchor="middle">row r ↓ (0 at top)</text>`;
  // a fake drain line on the paper
  s += `<polyline class="scanink" fill="none" points="${[[100, 300], [300, 300], [300, 520], [560, 520]].map(p => px(p[0]) + "," + py(p[1])).join(" ")}"/>`;
  GEO.gcps.forEach(g => {
    const right = g.c < 300, below = g.id !== "G2";      // label where there is room: G1/G3 below-right, G2 above-left
    s += `<g class="gcp"><line x1="${px(g.c) - 18}" y1="${py(g.r)}" x2="${px(g.c) + 18}" y2="${py(g.r)}"/><line x1="${px(g.c)}" y1="${py(g.r) - 18}" x2="${px(g.c)}" y2="${py(g.r) + 18}"/></g>`;
    s += `<text class="gcp-label" x="${px(g.c) + (right ? 24 : -24)}" y="${py(g.r) + (below ? 40 : -26)}" text-anchor="${right ? "start" : "end"}">${g.id} pixel (${g.c}, ${g.r}) → ground (${g.x}, ${g.y})</text>`;
  });
  if (opts.showCheck) {
    const k = GEO.check;
    s += `<g class="gcp check"><circle cx="${px(k.c)}" cy="${py(k.r)}" r="16"/></g><text class="gcp-label check" x="${px(k.c) - 24}" y="${py(585)}" text-anchor="end">K1 seen at pixel (${k.c}, ${k.r}); truth (${k.x}, ${k.y})</text>`;
    if (opts.showGhost) s += `<circle class="ghost" cx="${px(550)}" cy="${py(450)}" r="16"/><text class="gcp-label ghost" x="${px(550) - 24}" y="${py(450) - 24}" text-anchor="end">on a perfect scan K1 would be at (550, 450)</text>`;
  }
  if (opts.probe) { const p = opts.probe; s += `<circle class="probe" cx="${px(p.c)}" cy="${py(p.r)}" r="12"/>`; }
  s += `</svg>`;
  el.innerHTML = s + `<figcaption>"Ward A drainage plan (1998)" — a made-up 600 × 600 pixel scan with no coordinates. Three printed crosses have known ground positions.</figcaption>`;
}

/* ---------- attribute checks used on 9.6 and the lab page ---------- */
function runChecks() {
  const dom = T9.domains;
  const ids = {}; T9.assets.forEach(a => { ids[a.id] = (ids[a.id] || 0) + 1; });
  const dup = Object.keys(ids).filter(k => ids[k] > 1);
  const badType = T9.assets.filter(a => !dom.type.includes(a.type)).map(a => a.id + " (" + a.type + ")");
  const badStatus = T9.requests.filter(q => !dom.reqStatus.includes(q.status)).map(q => q.id);
  const missing = T9.inspections.filter(i => i.cond == null).map(i => i.id);
  const range = T9.assets.filter(a => a.h != null && (a.h < dom.height[0] || a.h > dom.height[1])).map(a => a.id + " (" + a.h + ")");
  const assetIds = new Set(T9.assets.map(a => a.id)), teamIds = new Set(T9.teams.map(t => t.id));
  const orphans = T9.inspections.filter(i => !assetIds.has(i.asset)).map(i => i.id + " → " + i.asset);
  const orphanTeams = T9.inspections.filter(i => !teamIds.has(i.team)).map(i => i.id);
  return { dup, badType, badStatus, missing, range, orphans, orphanTeams };
}
