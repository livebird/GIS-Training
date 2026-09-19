/* Chapter 8 tutorial — practice data, small helpers and SVG drawings.
   All data here is MADE-UP practice data on the flat training grid (metres, no real place).
   It follows the Chapter 8 training document (GIS_Phase_1_Chapter_08_GIS_Data_Modeling_and_Relationships.md). */

/* ---------- Fixture: the municipality's records ---------- */
const F8 = {
  wards: [
    { id: "A", pts: [[0, 0], [1000, 0], [1000, 1000], [0, 1000]] },
    { id: "B", pts: [[1000, 0], [2000, 0], [2000, 1000], [1000, 1000]] }
  ],
  road: { id: "R1", from: [0, 500], to: [2000, 500] },
  assets: [
    { id: "SL-0113", type: "SL", typeName: "Streetlight", x: 205, y: 195, year: 2018, method: "SURVEY", watt: 70 },
    { id: "DR-0042", type: "DR", typeName: "Drain", x: 995, y: 510, year: 2011, method: "SURVEY", watt: null },
    { id: "TR-0301", type: "TR", typeName: "Tree", x: 2190, y: 520, year: 2005, method: "APPROX", watt: null }
  ],
  teams: [
    { id: "T-N", name: "North crew" },
    { id: "T-S", name: "South crew" }
  ],
  /* Table F8-2: times are IST as written in the field notebook */
  inspections: [
    { id: "INS-0001", asset: "DR-0042", ist: "2024-10-15 09:50", team: "T-S", cond: 3, defects: 0, remarks: null, entered: "2024-10-15" },
    { id: "INS-0002", asset: "DR-0042", ist: "2025-11-02 15:05", team: "T-S", cond: 2, defects: 2, remarks: "Grating cracked; silt", entered: "2025-11-02" },
    { id: "INS-0003", asset: "SL-0113", ist: "2026-03-14 10:42", team: "T-N", cond: 3, defects: 1, remarks: "Lamp flickers at dusk", entered: "2026-03-14" },
    { id: "INS-0004", asset: "SL-0113", ist: "2025-09-14 11:15", team: "T-N", cond: 3, defects: 0, remarks: null, entered: "2026-03-16" }
  ],
  requests: [
    { id: "P1", x: 200, y: 200, what: "Streetlight out", asset: "SL-0113" },
    { id: "P5", x: 1000, y: 500, what: "Blocked drain", asset: "DR-0042" },
    { id: "P6", x: 2200, y: 500, what: "Fallen tree", asset: null }
  ],
  condWords: { 1: "Very poor", 2: "Poor", 3: "Fair", 4: "Good", 5: "Very good" }
};

/* The deliberately bad table used in the lab (Table 8.8-A). Blank cells are "" on purpose. */
const FLAT = {
  cols: ["AssetName", "Type", "X", "Y", "Height", "Wattage", "Insp1_Date", "Insp1_Cond", "Insp1_By", "Insp2_Date", "Insp2_Cond", "Insp2_By", "Insp3_Date", "Insp3_Cond", "Insp3_By", "Photos", "Ward", "Notes"],
  rows: [
    ["Streetlight near Ward A market", "Streetlight", "205", "195", "7", "70", "14/09/2025", "Fair", "North", "2026-03-14", "Fair", "North crew", "", "", "", "sl113_a.jpg; sl113_b.jpg", "A", "Lamp flickers at dusk (Mar 2026)"],
    ["Streetlight near Ward A market", "Light", "260", "190", "6.5", "", "2026-02-02", "Good", "North", "", "", "", "", "", "", "", "A", ""],
    ["Main road light", "Streetlight", "600", "480", "22 ft", "", "2026-01-10", "Good", "T-N", "", "", "", "", "", "", "mr_light.jpg", "A", ""],
    ["Drain by temple", "Drain", "995", "510", "", "", "15/10/2024", "Fair", "South", "2025-11-02", "Poor", "South", "", "", "", "dr42_1.jpg, dr42_2.jpg", "A/B", "Grating cracked; silt"],
    ["Big tree", "Tree", "2190", "520", "12 m", "", "", "", "", "", "", "", "", "", "", "", "outside", ""],
    ["Drain Ward B", "Drain", "1450", "250", "0", "0", "2026-04-20", "Fair", "South", "", "", "", "", "", "", "", "B", ""],
    ["Light no. 250 (decommissioned)", "Streetlight", "1800", "700", "8", "0", "2024-05-05", "Good", "North", "2025-05-06", "Fair", "North", "2026-05-07", "Poor", "South", "", "B", "4th visit 2026-08-01 not recorded – no column"],
    ["Tree near school", "Tree", "400", "900", "", "", "2026-06-15", "3", "South", "", "", "", "", "", "", "tr303.jpg", "A", ""]
  ]
};

/* ---------- time helpers ---------- */
/* "2026-03-14 10:42" in IST (UTC+05:30) -> "2026-03-14 05:12" in UTC. Pure arithmetic, no browser time zone involved. */
function istToUtc(s) {
  const m = s.match(/^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})$/);
  if (!m) return null;
  const d = new Date(Date.UTC(+m[1], +m[2] - 1, +m[3], +m[4], +m[5]));
  d.setUTCMinutes(d.getUTCMinutes() - 330);
  const p = n => String(n).padStart(2, "0");
  return `${d.getUTCFullYear()}-${p(d.getUTCMonth() + 1)}-${p(d.getUTCDate())} ${p(d.getUTCHours())}:${p(d.getUTCMinutes())}`;
}
function fmt(n, d = 2) { return Number(n).toLocaleString("en-IN", { minimumFractionDigits: d, maximumFractionDigits: d }); }
function esc(s) { return String(s).replace(/[&<>"]/g, c => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" }[c])); }

/* ---------- SVG: the training grid with wards, road, assets, requests ---------- */
/* opts: { assets:true, requests:true, sel:"SL-0113", caption, hideRoad, width } */
function renderGrid(el, opts = {}) {
  const W = 2600, H = 1300, ox = 200, oy = 1150, s = 1; // grid units = metres; y up
  const X = x => ox + x * s, Y = y => oy - y * s;
  let g = `<svg viewBox="0 0 ${W} ${H}" role="img" aria-label="Training grid map (made-up data)">`;
  g += `<rect x="0" y="0" width="${W}" height="${H}" fill="#fff"/>`;
  for (let x = 0; x <= 2000; x += 500) g += `<line class="axis" x1="${X(x)}" y1="${Y(0)}" x2="${X(x)}" y2="${Y(0) + 20}"/><text class="axis-label" x="${X(x)}" y="${Y(0) + 60}" text-anchor="middle">${x}</text>`;
  for (let y = 0; y <= 1000; y += 500) g += `<line class="axis" x1="${X(0) - 20}" y1="${Y(y)}" x2="${X(0)}" y2="${Y(y)}"/><text class="axis-label" x="${X(0) - 30}" y="${Y(y) + 10}" text-anchor="end">${y}</text>`;
  g += `<text class="axis-label" x="${X(2300)}" y="${Y(0) + 60}" text-anchor="middle">x (m)</text><text class="axis-label" x="${X(0) - 30}" y="${Y(1100)}" text-anchor="end">y (m)</text>`;
  F8.wards.forEach(w => { g += `<polygon class="ward" points="${w.pts.map(p => X(p[0]) + "," + Y(p[1])).join(" ")}"/><text class="ward-label" x="${X(w.pts[0][0] + 500)}" y="${Y(880)}" text-anchor="middle">Ward ${w.id}</text>`; });
  if (!opts.hideRoad) g += `<line class="road" x1="${X(0)}" y1="${Y(500)}" x2="${X(2000)}" y2="${Y(500)}"/><text class="road-label" x="${X(1500)}" y="${Y(540)}">R1</text>`;
  if (opts.requests) F8.requests.forEach(r => { g += `<circle class="req ${opts.sel === r.id ? "sel" : ""}" cx="${X(r.x)}" cy="${Y(r.y)}" r="22"/><text class="req-label" x="${X(r.x) + 30}" y="${Y(r.y) - 24}">${r.id}</text>`; });
  if (opts.assets !== false) F8.assets.forEach(a => {
    const sel = opts.sel === a.id;
    g += `<rect class="asset ${sel ? "sel" : ""}" x="${X(a.x) - 20}" y="${Y(a.y) - 20}" width="40" height="40" rx="6" data-id="${a.id}"/><text class="asset-label" x="${X(a.x) + 30}" y="${Y(a.y) + 44}">${a.id}</text>`;
  });
  g += `</svg>`;
  el.innerHTML = g + (opts.caption ? `<figcaption>${opts.caption}</figcaption>` : "");
}

/* ---------- SVG: the entity–relationship diagram (clickable boxes) ---------- */
const ER = {
  boxes: {
    Ward:      { x: 40,  y: 40,  w: 300, h: 120, geom: "polygon",  key: "ward_code",     title: "Ward" },
    Coverage:  { x: 460, y: 40,  w: 300, h: 120, geom: null,       key: "team_id + ward_code", title: "TeamWardCoverage" },
    Team:      { x: 880, y: 40,  w: 300, h: 120, geom: null,       key: "team_id",       title: "Team" },
    Asset:     { x: 40,  y: 330, w: 300, h: 120, geom: "point",    key: "asset_id",      title: "Asset" },
    Inspection:{ x: 460, y: 330, w: 300, h: 120, geom: null,       key: "inspection_id", title: "Inspection" },
    Photo:     { x: 880, y: 330, w: 300, h: 120, geom: null,       key: "photo_id",      title: "InspectionPhoto" },
    Request:   { x: 40,  y: 620, w: 300, h: 120, geom: "point",    key: "request_id",    title: "Request" },
    Detail:    { x: 460, y: 620, w: 300, h: 120, geom: null,       key: "asset_id (unique)", title: "StreetlightDetail" }
  },
  links: [
    { from: "Asset", to: "Inspection", card: "1 : many", id: "a-i" },
    { from: "Team", to: "Inspection", card: "1 : many", id: "t-i" },
    { from: "Inspection", to: "Photo", card: "1 : many", id: "i-p" },
    { from: "Asset", to: "Request", card: "1 : 0..many", id: "a-r", dash: true },
    { from: "Asset", to: "Detail", card: "1 : 1", id: "a-d" },
    { from: "Ward", to: "Coverage", card: "1 : many", id: "w-c" },
    { from: "Team", to: "Coverage", card: "1 : many", id: "t-c" }
  ]
};
/* point on the border of box k, on the line from its centre towards point p */
function erEdge(k, p) {
  const b = ER.boxes[k], cx = b.x + b.w / 2, cy = b.y + b.h / 2;
  const dx = p.x - cx, dy = p.y - cy;
  if (dx === 0 && dy === 0) return { x: cx, y: cy };
  const sx = Math.abs(dx) > 0 ? (b.w / 2) / Math.abs(dx) : Infinity, sy = Math.abs(dy) > 0 ? (b.h / 2) / Math.abs(dy) : Infinity;
  const s = Math.min(sx, sy);
  return { x: cx + dx * s, y: cy + dy * s };
}
function renderER(el, opts = {}) {
  const b = ER.boxes;
  const centre = k => ({ x: b[k].x + b[k].w / 2, y: b[k].y + b[k].h / 2 });
  let g = `<svg viewBox="0 0 1220 780" role="img" aria-label="Entity relationship diagram">`;
  g += `<defs><marker id="crow" markerWidth="22" markerHeight="22" refX="20" refY="11" orient="auto" markerUnits="userSpaceOnUse"><path d="M2,2 L20,11 L2,20 M2,11 L20,11" fill="none" stroke="#1f2a44" stroke-width="2.5"/></marker></defs>`;
  ER.links.forEach(l => {
    const a = erEdge(l.from, centre(l.to)), c = erEdge(l.to, centre(l.from));
    const lit = opts.lit === l.id || opts.lit === l.from || opts.lit === l.to;
    g += `<line class="erlink ${l.dash ? "dash" : ""} ${lit ? "lit" : ""}" x1="${a.x}" y1="${a.y}" x2="${c.x}" y2="${c.y}" marker-end="url(#crow)"/>`;
    const mx = (a.x + c.x) / 2, my = (a.y + c.y) / 2;
    g += `<rect x="${mx - 70}" y="${my - 16}" width="140" height="32" rx="8" fill="#fff" stroke="#cfc6b3"/><text class="erlabel" x="${mx}" y="${my + 6}" text-anchor="middle">${l.card}</text>`;
  });
  Object.entries(b).forEach(([k, v]) => {
    const lit = opts.lit === k;
    g += `<g class="erbox ${v.geom ? "spatial" : ""} ${lit ? "lit" : ""}" data-k="${k}"><rect x="${v.x}" y="${v.y}" width="${v.w}" height="${v.h}" rx="14"/>`;
    g += `<text class="ertitle" x="${v.x + 16}" y="${v.y + 38}">${v.title}</text>`;
    g += `<text class="ersub" x="${v.x + 16}" y="${v.y + 70}">key: ${v.key}</text>`;
    g += `<text class="ersub" x="${v.x + 16}" y="${v.y + 98}">${v.geom ? "shape: " + v.geom : "plain table, no shape"}</text></g>`;
  });
  g += `</svg>`;
  el.innerHTML = g + (opts.caption ? `<figcaption>${opts.caption}</figcaption>` : "");
  if (opts.onClick) el.querySelectorAll(".erbox").forEach(x => x.addEventListener("click", () => opts.onClick(x.dataset.k)));
}

/* ---------- SVG: one streetlight, three possible "points" ---------- */
function renderLamp(el, which) {
  const pts = { base: [300, 560], head: [470, 150], report: [180, 610] };
  let g = `<svg viewBox="0 0 700 680" role="img" aria-label="A streetlight with three possible point positions">`;
  g += `<rect x="0" y="0" width="700" height="680" fill="#fff"/>`;
  g += `<rect x="0" y="600" width="700" height="80" fill="#efe7d7"/><text class="note-text" x="20" y="660">ground</text>`;
  g += `<rect x="288" y="150" width="24" height="450" fill="#7a5c2e"/><path d="M300,160 Q380,120 470,150" stroke="#7a5c2e" stroke-width="20" fill="none"/>`;
  g += `<ellipse cx="470" cy="150" rx="42" ry="22" fill="#f4c542" stroke="#8a6a12" stroke-width="4"/>`;
  g += `<rect x="255" y="560" width="90" height="40" fill="#8b93a7"/>`;
  g += `<circle cx="180" cy="610" r="18" fill="#d3541f" opacity=".5"/><text class="note-text" x="120" y="590">person who</text><text class="note-text" x="120" y="626" >reported it</text>`;
  Object.entries(pts).forEach(([k, p]) => {
    const on = which === k;
    g += `<circle cx="${p[0]}" cy="${p[1]}" r="${on ? 22 : 12}" fill="${on ? "#d3541f" : "#fff"}" stroke="#1f2a44" stroke-width="5"/>`;
  });
  g += `</svg>`;
  el.innerHTML = g;
}

/* ---------- small table renderer ---------- */
function tableHTML(cols, rows, opts = {}) {
  const th = cols.map(c => `<th>${esc(c)}</th>`).join("");
  const tr = rows.map((r, i) => `<tr class="${opts.rowClass ? opts.rowClass(r, i) : ""}">${r.map((c, j) => `<td class="${opts.mono && opts.mono.includes(j) ? "mono" : ""}">${c === null || c === "" ? '<span class="blank">' + (opts.blank ?? "—") + "</span>" : esc(c)}</td>`).join("")}</tr>`).join("");
  return `<div class="table-wrap"><table>${opts.caption ? `<caption>${opts.caption}</caption>` : ""}<thead><tr>${th}</tr></thead><tbody>${tr}</tbody></table></div>`;
}
