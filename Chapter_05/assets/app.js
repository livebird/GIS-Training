/* Chapter 5 tutorial — shared data, coordinate maths, SVG renderers, navigation, quizzes.
   Every coordinate here is MADE-UP practice material. Set E5 uses round numbers in western India only so that
   "does this look right?" checks have something to compare with. No real municipality, asset or survey. */

/* ---- fixtures ---- */
const GRID = {
  wards: [{ id: "A", pts: [[0, 0], [1000, 0], [1000, 1000], [0, 1000]] }, { id: "B", pts: [[1000, 0], [2000, 0], [2000, 1000], [1000, 1000]] }],
  road: [[0, 500], [2000, 500]],
  reqs: [{ id: "P1", x: 200, y: 200 }, { id: "P2", x: 800, y: 800 }, { id: "P3", x: 1200, y: 250 }, { id: "P4", x: 1700, y: 900 }, { id: "P5", x: 1000, y: 500 }, { id: "P6", x: 2200, y: 500 }]
};
const E5 = {
  G: [{ id: "G1", lat: 23.0250, lon: 72.6000 }, { id: "G2", lat: 23.0375, lon: 72.5875 }, { id: "G3", lat: 23.0125, lon: 72.6125 }],
  U: [{ id: "U1", e: 254318.4, n: 2547906.2 }, { id: "U2", e: 254512.9, n: 2548110.7 }, { id: "U3", e: 254101.0, n: 2547650.5 }]
};
/* rough reference places for the world sketch (approximate, for orientation only) */
const WORLD_REFS = [
  { name: "India", lat: 22, lon: 79 }, { name: "Sri Lanka", lat: 7.5, lon: 80.7 }, { name: "Norway", lat: 64, lon: 12 },
  { name: "Svalbard", lat: 78, lon: 16 }, { name: "Bali", lat: -8.4, lon: 115.2 }, { name: "Greenwich", lat: 51.5, lon: 0 },
  { name: "Redlands (USA)", lat: 34, lon: -117.2 }, { name: "Gulf of Guinea (0°,0°)", lat: 0, lon: 0 },
  { name: "North Cape, Norway", lat: 71.2, lon: 25.8 }, { name: "Delhi", lat: 28.6, lon: 77.2 }
];

/* ---- coordinate maths (exact where the maths is exact; "rough" helpers say so) ---- */
function dmsToDd(d, m, s, neg) { const v = Math.abs(d) + Math.abs(m) / 60 + Math.abs(s) / 3600; return neg ? -v : v; }
function ddToDms(v) {
  const neg = v < 0; let a = Math.abs(v);
  let d = Math.floor(a); let mf = (a - d) * 60; let m = Math.floor(mf); let s = (mf - m) * 60;
  // tidy floating-point noise (e.g. 29.9999999 seconds)
  s = Math.round(s * 1000) / 1000; if (s >= 60) { s -= 60; m += 1; } if (m >= 60) { m -= 60; d += 1; }
  return { d, m, s, neg, mf: Math.round(mf * 1e6) / 1e6 };
}
const KM_PER_DEG_LAT = 111.32;             // teaching approximation on a sphere (equatorial circumference / 360)
function kmPerDegLon(lat) { return KM_PER_DEG_LAT * Math.cos(lat * Math.PI / 180); }
function utmZone(lon) { let z = Math.floor((lon + 180) / 6) + 1; if (z > 60) z = 60; if (z < 1) z = 1; return z; }
function utmCentralMeridian(zone) { return (zone - 1) * 6 - 180 + 3; }
/* Web Mercator forward formula on the sphere R = 6 378 137 m (the definition of EPSG:3857). Used only for the display read-out demo. */
const R_SPHERE = 6378137;
function webMercator(lat, lon) {
  const la = Math.max(-85.05, Math.min(85.05, lat)) * Math.PI / 180;
  return { x: R_SPHERE * lon * Math.PI / 180, y: R_SPHERE * Math.log(Math.tan(Math.PI / 4 + la / 2)) };
}
/* ROUGH back-estimate of latitude/longitude from a UTM easting/northing (north hemisphere). Ignores the 0.9996 scale and curvature. */
function roughFromUtm(e, n, zone) {
  const cm = utmCentralMeridian(zone);
  const lat = n / 111000;                         // "about 111 km per degree"
  const lon = cm + (e - 500000) / (kmPerDegLon(lat) * 1000);
  return { lat, lon, cm };
}
const fmt = (v, d = 4) => (Math.round(v * Math.pow(10, d)) / Math.pow(10, d)).toFixed(d);
const fmtInt = v => Math.round(v).toLocaleString("en-GB");

/* ---- shared SVG helpers ---- */
const NS = "http://www.w3.org/2000/svg";
function svgEl(tag, attrs, parent) { const n = document.createElementNS(NS, tag); for (const k in attrs) n.setAttribute(k, attrs[k]); if (parent) parent.appendChild(n); return n; }
function arrowDefs(svg) {
  const d = svgEl("defs", {}, svg);
  const m = svgEl("marker", { id: "arr", viewBox: "0 0 10 10", refX: "9", refY: "5", markerWidth: "7", markerHeight: "7", orient: "auto-start-reverse" }, d);
  svgEl("path", { d: "M 0 0 L 10 5 L 0 10 z", fill: "currentColor" }, m);
  svg.style.color = "#1f2a44";
}
function cap(el, text) { let c = el.querySelector("figcaption"); if (!c) { c = document.createElement("figcaption"); el.appendChild(c); } c.textContent = text; }

/* ---- training grid (Chapter 1 fixture) ---- */
function renderGrid(el, opts = {}) {
  el.innerHTML = "";
  const W = 2620, H = 1200, ox = 170, oy = 1100;   // origin (0,0) drawn at (170, 1100); 1 m = 1 unit; y flipped
  const X = x => ox + x, Y = y => oy - y;
  const svg = svgEl("svg", { viewBox: `0 0 ${W} ${H}`, role: "img", "aria-label": "Flat training grid with two wards, one road and six request points" }, el);
  arrowDefs(svg);
  for (let x = 0; x <= 2200; x += 500) svgEl("line", { x1: X(x), y1: Y(-40), x2: X(x), y2: Y(1000), class: "axis" }, svg);
  for (let y = 0; y <= 1000; y += 500) svgEl("line", { x1: X(-40), y1: Y(y), x2: X(2200), y2: Y(y), class: "axis" }, svg);
  GRID.wards.forEach(w => { svgEl("polygon", { points: w.pts.map(p => `${X(p[0])},${Y(p[1])}`).join(" "), class: "ward" }, svg); const t = svgEl("text", { x: X(w.pts[0][0] + 500), y: Y(900), class: "ward-label", "text-anchor": "middle" }, svg); t.textContent = "Ward " + w.id; });
  svgEl("line", { x1: X(0), y1: Y(500), x2: X(2000), y2: Y(500), class: "road" }, svg);
  const rl = svgEl("text", { x: X(1500), y: Y(540), class: "road-label" }, svg); rl.textContent = "Road R1 (ends at x = 2000)";
  GRID.reqs.forEach(r => { svgEl("circle", { cx: X(r.x), cy: Y(r.y), r: 22, class: "req" }, svg); const onRoad = r.y === 500; const t = svgEl("text", { x: X(r.x) + (onRoad ? 0 : 32), y: Y(r.y) + (onRoad ? 62 : 14), class: "req-label", "text-anchor": onRoad ? "middle" : "start" }, svg); t.textContent = `${r.id} (${r.x}, ${r.y})`; });
  if (opts.axes !== false) {
    svgEl("line", { x1: X(0), y1: Y(0), x2: X(2300), y2: Y(0), stroke: "#1f2a44", "stroke-width": 6, "marker-end": "url(#arr)" }, svg);
    svgEl("line", { x1: X(0), y1: Y(0), x2: X(0), y2: Y(1080), stroke: "#1f2a44", "stroke-width": 6, "marker-end": "url(#arr)" }, svg);
    const tx = svgEl("text", { x: X(2100), y: Y(-60), class: "note-text" }, svg); tx.textContent = "x → (metres)";
    const ty = svgEl("text", { x: X(40), y: Y(1050), class: "note-text" }, svg); ty.textContent = "y ↑ (metres)";
    svgEl("circle", { cx: X(0), cy: Y(0), r: 18, fill: "#d3541f", stroke: "#fff", "stroke-width": 5 }, svg);
    const to = svgEl("text", { x: X(30), y: Y(-60), class: "note-text", fill: "#d3541f" }, svg); to.textContent = "origin (0, 0)";
    [500, 1000, 1500, 2000].forEach(v => { const t = svgEl("text", { x: X(v), y: Y(-60), class: "axis-label", "text-anchor": "middle" }, svg); t.textContent = v; });
    [500, 1000].forEach(v => { const t = svgEl("text", { x: X(-40), y: Y(v) + 10, class: "axis-label", "text-anchor": "end" }, svg); t.textContent = v; });
  }
  if (opts.warn) { const t = svgEl("text", { x: X(1150), y: Y(1060), class: "note-text", fill: "#b9432e", "text-anchor": "middle", "font-weight": "700" }, svg); t.textContent = opts.warn; }
  cap(el, opts.caption || "Made-up flat grid in metres. No place on the Earth. No CRS code. Right is not east; up is not north.");
}

/* ---- globe (orthographic) + two angle dials ---- */
function orth(lat, lon, lat0, lon0) {
  const r = Math.PI / 180, f = lat * r, l = (lon - lon0) * r, f0 = lat0 * r;
  const cosc = Math.sin(f0) * Math.sin(f) + Math.cos(f0) * Math.cos(f) * Math.cos(l);
  return { x: Math.cos(f) * Math.sin(l), y: Math.cos(f0) * Math.sin(f) - Math.sin(f0) * Math.cos(f) * Math.cos(l), vis: cosc >= 0 };
}
function renderGlobe(el, opts = {}) {
  const lat = opts.lat ?? 23.025, lon = opts.lon ?? 72.6, lat0 = opts.lat0 ?? 15, lon0 = opts.lon0 ?? 60;
  el.innerHTML = "";
  const W = 1500, H = 900;
  const svg = svgEl("svg", { viewBox: `0 0 ${W} ${H}`, role: "img", "aria-label": "Globe with equator and prime meridian, plus two angle dials" }, el);
  arrowDefs(svg);
  const cx = 380, cy = 480, R = 300;
  svgEl("circle", { cx, cy, r: R, class: "globe" }, svg);
  const P = (la, lo) => { const o = orth(la, lo, lat0, lon0); return { x: cx + o.x * R, y: cy - o.y * R, vis: o.vis }; };
  const poly = (pts, cls) => { let seg = []; const flush = () => { if (seg.length > 1) svgEl("polyline", { points: seg.map(p => `${p.x},${p.y}`).join(" "), class: cls }, svg); seg = []; }; pts.forEach(p => { if (p.vis) seg.push(p); else flush(); }); flush(); };
  for (let la = -60; la <= 60; la += 30) { const pts = []; for (let lo = -180; lo <= 180; lo += 3) pts.push(P(la, lo)); poly(pts, "grat" + (la === 0 ? " eq" : "")); }
  for (let lo = -180; lo < 180; lo += 30) { const pts = []; for (let la = -90; la <= 90; la += 3) pts.push(P(la, lo)); poly(pts, "grat" + (lo === 0 ? " pm" : "")); }
  const pt = P(lat, lon);
  if (pt.vis) { svgEl("circle", { cx: pt.x, cy: pt.y, r: 14, class: "gpt" }, svg); const t = svgEl("text", { x: pt.x + 20, y: pt.y - 12, class: "glabel", fill: "#1f2a44", "font-weight": "700" }, svg); t.textContent = opts.label || "point"; }
  else { const t = svgEl("text", { x: cx, y: cy + R + 60, class: "glabel", "text-anchor": "middle" }, svg); t.textContent = "(point is on the far side of the globe)"; }
  const eqL = P(-4, lon0 - 75); if (eqL.vis) { const t = svgEl("text", { x: eqL.x + 6, y: eqL.y + 34, class: "glabel eq" }, svg); t.textContent = "equator (0° latitude)"; }
  const pmL = P(35, 0); if (pmL.vis) { const t = svgEl("text", { x: pmL.x + 12, y: pmL.y, class: "glabel pm" }, svg); t.textContent = "prime meridian (0° longitude)"; }
  const np = P(89.5, 0); if (np.vis) { const t = svgEl("text", { x: np.x, y: np.y - 16, class: "glabel", "text-anchor": "middle" }, svg); t.textContent = "N pole"; }
  const cg = svgEl("text", { x: cx, y: 56, class: "dtitle", "text-anchor": "middle" }, svg); cg.textContent = "Globe (schematic sphere)";
  /* dial 1: latitude — side view (cross-section through the poles) */
  const d1x = 1100, d1y = 270, dr = 130;
  const t1 = svgEl("text", { x: d1x, y: 56, class: "dtitle", "text-anchor": "middle" }, svg); t1.textContent = "Latitude: angle up from the equator";
  const s1 = svgEl("text", { x: d1x, y: 90, class: "dsub", "text-anchor": "middle" }, svg); s1.textContent = "side view — cut through the Earth, poles top and bottom";
  svgEl("circle", { cx: d1x, cy: d1y, r: dr, class: "dial" }, svg);
  svgEl("line", { x1: d1x - dr, y1: d1y, x2: d1x + dr, y2: d1y, class: "dial-ref" }, svg);
  const la = lat * Math.PI / 180;
  const p1 = { x: d1x + dr * Math.cos(la), y: d1y - dr * Math.sin(la) };
  const arcR = 70, a1 = { x: d1x + arcR, y: d1y }, a2 = { x: d1x + arcR * Math.cos(la), y: d1y - arcR * Math.sin(la) };
  svgEl("path", { d: `M ${d1x} ${d1y} L ${a1.x} ${a1.y} A ${arcR} ${arcR} 0 0 ${lat >= 0 ? 0 : 1} ${a2.x} ${a2.y} Z`, class: "dial-fill" }, svg);
  svgEl("path", { d: `M ${a1.x} ${a1.y} A ${arcR} ${arcR} 0 0 ${lat >= 0 ? 0 : 1} ${a2.x} ${a2.y}`, class: "dial-arc" }, svg);
  svgEl("line", { x1: d1x, y1: d1y, x2: p1.x, y2: p1.y, class: "dial-ray" }, svg);
  svgEl("circle", { cx: p1.x, cy: p1.y, r: 12, class: "gpt" }, svg);
  const e1 = svgEl("text", { x: d1x + dr + 10, y: d1y + 8, class: "glabel eq" }, svg); e1.textContent = "equator";
  const n1 = svgEl("text", { x: d1x, y: d1y - dr - 12, class: "glabel", "text-anchor": "middle" }, svg); n1.textContent = "N pole (90°)";
  const s1b = svgEl("text", { x: d1x, y: d1y + dr + 32, class: "glabel", "text-anchor": "middle" }, svg); s1b.textContent = "S pole (−90°)";
  const v1 = svgEl("text", { x: d1x + dr + 10, y: d1y + (lat >= 0 ? -36 : 48), class: "hl", fill: "#d3541f" }, svg); v1.textContent = `${fmt(Math.abs(lat), 3)}° ${lat >= 0 ? "N" : "S"}`;
  /* dial 2: longitude — top view (looking down on the north pole) */
  const d2x = 1100, d2y = 690;
  const t2 = svgEl("text", { x: d2x, y: 490, class: "dtitle", "text-anchor": "middle" }, svg); t2.textContent = "Longitude: angle around from the prime meridian";
  const s2 = svgEl("text", { x: d2x, y: 524, class: "dsub", "text-anchor": "middle" }, svg); s2.textContent = "top view — looking down on the North Pole; east runs anticlockwise";
  const dr2 = 110;
  svgEl("circle", { cx: d2x, cy: d2y, r: dr2, class: "dial" }, svg);
  svgEl("line", { x1: d2x, y1: d2y, x2: d2x, y2: d2y - dr2, class: "dial-ref" }, svg);
  const lo = lon * Math.PI / 180;
  const p2 = { x: d2x - dr2 * Math.sin(lo), y: d2y - dr2 * Math.cos(lo) };
  const ar2 = 60, b1 = { x: d2x, y: d2y - ar2 }, b2 = { x: d2x - ar2 * Math.sin(lo), y: d2y - ar2 * Math.cos(lo) };
  const big = Math.abs(lon) > 180 ? 1 : 0, sweep = lon >= 0 ? 0 : 1;
  svgEl("path", { d: `M ${d2x} ${d2y} L ${b1.x} ${b1.y} A ${ar2} ${ar2} 0 ${big} ${sweep} ${b2.x} ${b2.y} Z`, class: "dial-fill" }, svg);
  svgEl("path", { d: `M ${b1.x} ${b1.y} A ${ar2} ${ar2} 0 ${big} ${sweep} ${b2.x} ${b2.y}`, class: "dial-arc" }, svg);
  svgEl("line", { x1: d2x, y1: d2y, x2: p2.x, y2: p2.y, class: "dial-ray" }, svg);
  svgEl("circle", { cx: p2.x, cy: p2.y, r: 12, class: "gpt" }, svg);
  const pm2 = svgEl("text", { x: d2x, y: d2y - dr2 - 12, class: "glabel pm", "text-anchor": "middle" }, svg); pm2.textContent = "prime meridian (0°)";
  const e2 = svgEl("text", { x: d2x - dr2 - 12, y: d2y + 8, class: "glabel", "text-anchor": "end" }, svg); e2.textContent = "90° E";
  const w2 = svgEl("text", { x: d2x + dr2 + 12, y: d2y + 8, class: "glabel" }, svg); w2.textContent = "90° W";
  const v2 = svgEl("text", { x: d2x + (lon >= 0 ? -(dr2 + 12) : dr2 + 12), y: d2y - 44, class: "hl", fill: "#d3541f", "text-anchor": lon >= 0 ? "end" : "start" }, svg); v2.textContent = `${fmt(Math.abs(lon), 3)}° ${lon >= 0 ? "E" : "W"}`;
  cap(el, opts.caption || `Schematic. The globe is drawn as a perfect sphere; the real reference surface is a slightly squashed ellipsoid. Point: latitude ${fmt(lat)}, longitude ${fmt(lon)} (degrees).`);
}

/* ---- world sketch (equirectangular, schematic) ---- */
function renderWorld(el, opts = {}) {
  el.innerHTML = "";
  const W = 1480, H = 800, L = 60, T = 40, MW = 1360, MH = 680;
  const X = lon => L + (lon + 180) / 360 * MW, Y = lat => T + (90 - lat) / 180 * MH;
  const svg = svgEl("svg", { viewBox: `0 0 ${W} ${H}`, role: "img", "aria-label": "Schematic world grid of latitude and longitude with plotted points" }, el);
  svgEl("rect", { x: L, y: T, width: MW, height: MH, class: "wframe" }, svg);
  for (let lo = -180; lo <= 180; lo += 30) { svgEl("line", { x1: X(lo), y1: T, x2: X(lo), y2: T + MH, class: "wgrid" + (lo === 0 ? " grat pm" : "") }, svg); const t = svgEl("text", { x: X(lo), y: T + MH + 28, class: "glabel", "text-anchor": "middle" }, svg); t.textContent = lo === 0 ? "0°" : `${Math.abs(lo)}°${lo < 0 ? "W" : "E"}`; }
  for (let la = -90; la <= 90; la += 30) { svgEl("line", { x1: L, y1: Y(la), x2: L + MW, y2: Y(la), class: "wgrid" + (la === 0 ? " grat eq" : "") }, svg); const t = svgEl("text", { x: L - 8, y: Y(la) + 8, class: "glabel", "text-anchor": "end" }, svg); t.textContent = la === 0 ? "0°" : `${Math.abs(la)}°${la < 0 ? "S" : "N"}`; }
  const te = svgEl("text", { x: X(-175), y: Y(0) - 8, class: "glabel eq" }, svg); te.textContent = "equator";
  const tp = svgEl("text", { x: X(0) + 6, y: Y(-80), class: "glabel pm" }, svg); tp.textContent = "prime meridian";
  (opts.refs !== false ? WORLD_REFS : []).forEach(r => { svgEl("circle", { cx: X(r.lon), cy: Y(r.lat), r: 6, class: "wrefpt" }, svg); const t = svgEl("text", { x: X(r.lon) + 10, y: Y(r.lat) - 6, class: "wref" }, svg); t.textContent = r.name + " (approx.)"; });
  if (opts.box) { const b = opts.box; svgEl("rect", { x: X(b.lonMin), y: Y(b.latMax), width: X(b.lonMax) - X(b.lonMin), height: Y(b.latMin) - Y(b.latMax), class: "wbox" }, svg); const t = svgEl("text", { x: X(b.lonMin), y: Y(b.latMax) - 10, class: "glabel", fill: "#2f7d4f" }, svg); t.textContent = b.label || "expected area"; }
  (opts.points || []).forEach(p => {
    if (Math.abs(p.lat) > 90 || Math.abs(p.lon) > 180) { const t = svgEl("text", { x: W / 2, y: H - 6, class: "glabel", "text-anchor": "middle", fill: "#b9432e" }, svg); t.textContent = `${p.label}: cannot be drawn — latitude ${fmt(p.lat, 3)} or longitude ${fmt(p.lon, 3)} is outside the allowed range`; return; }
    svgEl("circle", { cx: X(p.lon), cy: Y(p.lat), r: 13, class: "wpt " + (p.cls || "good") }, svg);
    const t = svgEl("text", { x: X(p.lon) + 18, y: Y(p.lat) + 34, class: "wlabel" }, svg); t.textContent = p.label;
  });
  cap(el, opts.caption || "Schematic world grid (every 30°). Grey dots are rough reference places for orientation only; they are not accurate positions.");
}

/* ---- vertical surfaces cross-section ---- */
function renderSurfaces(el, opts = {}) {
  el.innerHTML = "";
  const W = 1400, H = 620;
  const svg = svgEl("svg", { viewBox: `0 0 ${W} ${H}`, role: "img", "aria-label": "Cross-section showing ground, geoid and ellipsoid with three different height arrows" }, el);
  arrowDefs(svg);
  const ellY = x => 470 + 0.00012 * (x - 700) * (x - 700) * 0.6;         // smooth arc
  const geoY = x => ellY(x) - 55 + 18 * Math.sin(x / 140);               // wavy, above the ellipsoid here
  const grdY = x => geoY(x) - 95 + 30 * Math.sin(x / 90) + 12 * Math.cos(x / 37); // irregular ground
  const path = (fn, extra) => { let d = ""; for (let x = 60; x <= 1340; x += 10) d += (d ? " L " : "M ") + x + " " + fn(x).toFixed(1); return d + (extra || ""); };
  svgEl("path", { d: path(grdY, " L 1340 600 L 60 600 Z"), class: "ground" }, svg);
  svgEl("path", { d: path(geoY), class: "geoid" }, svg);
  svgEl("path", { d: path(ellY), class: "ellip" }, svg);
  const px = 700, gy = grdY(px), Gy = geoY(px), Ey = ellY(px);
  svgEl("line", { x1: px, y1: gy, x2: px, y2: gy - 120, class: "pole" }, svg);
  svgEl("circle", { cx: px, cy: gy - 128, r: 14, fill: "#f4c542", stroke: "#1f2a44", "stroke-width": 4 }, svg);
  const lbl = (x, y, txt, cls, anchor) => { const t = svgEl("text", { x, y, class: cls || "glabel", "text-anchor": anchor || "start" }, svg); t.textContent = txt; return t; };
  lbl(px, gy - 150, "streetlight SL-0113", "glabel", "middle");
  lbl(80, grdY(80) - 40, "ground (real surface)", "glabel");
  lbl(80, geoY(80) - 12, "geoid ≈ mean sea level (follows gravity)", "glabel pm");
  lbl(80, ellY(80) + 30, "ellipsoid (smooth maths surface)", "glabel", "start").setAttribute("fill", "#2f7d4f");
  // arrows: ellipsoidal height h, gravity-related height H, pole height
  const ax1 = px - 200, ax2 = px - 100;
  svgEl("line", { x1: ax1, y1: ellY(ax1), x2: ax1, y2: grdY(ax1) + 4, class: "harrow h" }, svg);
  lbl(ax1 - 14, (ellY(ax1) + grdY(ax1)) / 2 + 8, "h = ellipsoidal height", "hl h", "end");
  svgEl("line", { x1: ax2, y1: geoY(ax2), x2: ax2, y2: grdY(ax2) + 4, class: "harrow H" }, svg);
  lbl(ax2 + 14, (geoY(ax2) + grdY(ax2)) / 2 + 40, "H = height above sea level", "hl H");
  svgEl("line", { x1: px + 40, y1: gy, x2: px + 40, y2: gy - 118, class: "harrow g" }, svg);
  lbl(px + 54, gy - 50, "pole height above ground", "hl g");
  lbl(W / 2, 40, "Three different “heights” for one streetlight base", "dtitle", "middle");
  lbl(W / 2, 74, "gaps between the surfaces are exaggerated so that you can see them; not to scale", "dsub", "middle");
  cap(el, opts.caption || "Schematic. h (green, dashed surface) and H (blue surface) are different numbers for the same point. The pole height is measured from the ground, not from any datum.");
}

/* ---- mock GIS window (data CRS vs map CRS) ---- */
function renderGisWindow(el, opts = {}) {
  const mapCrs = opts.mapCrs || "3857";
  const layers = [
    { id: "base", name: "Basemap (web tiles)", crs: "WGS 1984 Web Mercator (auxiliary sphere) · 3857", unit: "metre" },
    { id: "req", name: "Requests (Case A)", crs: "WGS 1984 · 4326", unit: "degree" },
    { id: "srv", name: "Survey (Case B)", crs: "WGS 1984 UTM Zone 43N · 32643", unit: "metre" }
  ];
  const names = { "3857": "WGS 1984 Web Mercator (auxiliary sphere) · WKID 3857 · metre", "4326": "WGS 1984 · WKID 4326 · degree" };
  el.innerHTML = `<div class="gisw"><div class="title">Practice map window (mock-up — not a real product screen)</div>
    <div class="body"><div class="contents"><div class="hdr">Contents / Layers</div>${layers.map(l => `<div class="lyr" data-id="${l.id}"><b>${l.name}</b><span class="crs">data CRS: ${l.crs}</span></div>`).join("")}
      <div class="hdr" style="margin-top:.8rem">Map properties</div><div class="lyr" style="cursor:default"><span class="crs">map CRS: ${names[mapCrs]}</span></div></div>
    <div class="view"><svg viewBox="0 0 800 400" style="width:100%;height:100%;display:block" id="gisSvg"></svg></div></div>
    <div class="status"><span>Pointer: <b id="gisRead">move the pointer over the map</b></span><span>Map CRS: ${mapCrs === "3857" ? "3857 (metres)" : "4326 (degrees)"}</span></div></div>`;
  const svg = el.querySelector("#gisSvg");
  // draw a little "map" of the E5 area in the chosen map CRS (only the shape/relative placement matters here)
  const box = { latMin: 22.99, latMax: 23.06, lonMin: 72.56, lonMax: 72.64 };
  const toXY = (lat, lon) => { if (mapCrs === "4326") return { x: (lon - box.lonMin) / (box.lonMax - box.lonMin) * 800, y: (box.latMax - lat) / (box.latMax - box.latMin) * 400 }; const a = webMercator(box.latMin, box.lonMin), b = webMercator(box.latMax, box.lonMax), p = webMercator(lat, lon); return { x: (p.x - a.x) / (b.x - a.x) * 800, y: (b.y - p.y) / (b.y - a.y) * 400 }; };
  svgEl("rect", { x: 0, y: 0, width: 800, height: 400, fill: "#eef3f8" }, svg);
  for (let i = 1; i < 8; i++) svgEl("line", { x1: i * 100, y1: 0, x2: i * 100, y2: 400, stroke: "#d5dde8", "stroke-width": 1 }, svg);
  for (let i = 1; i < 4; i++) svgEl("line", { x1: 0, y1: i * 100, x2: 800, y2: i * 100, stroke: "#d5dde8", "stroke-width": 1 }, svg);
  const bt = svgEl("text", { x: 12, y: 24, "font-size": 16, fill: "#8b93a7", "font-family": "monospace" }, svg); bt.textContent = "basemap tiles (schematic)";
  E5.G.forEach(g => { const p = toXY(g.lat, g.lon); svgEl("circle", { cx: p.x, cy: p.y, r: 10, fill: "#d3541f", stroke: "#fff", "stroke-width": 3 }, svg); const t = svgEl("text", { x: p.x + 14, y: p.y + 5, "font-size": 18, "font-family": "monospace", fill: "#1f2a44" }, svg); t.textContent = g.id; });
  // survey points are drawn at nominal positions ONLY (their UTM → lat/lon conversion is Chapter 6; nothing here computes it)
  [[23.03, 72.566], [23.048, 72.58], [23.012, 72.585]].forEach((s, i) => { const p = toXY(s[0], s[1]); svgEl("rect", { x: p.x - 8, y: p.y - 8, width: 16, height: 16, fill: "#5b7ea3", stroke: "#fff", "stroke-width": 3 }, svg); const t = svgEl("text", { x: p.x + 14, y: p.y + 5, "font-size": 18, "font-family": "monospace", fill: "#1f2a44" }, svg); t.textContent = "U" + (i + 1); });
  const read = el.querySelector("#gisRead");
  svg.addEventListener("mousemove", ev => {
    const r = svg.getBoundingClientRect(); const fx = (ev.clientX - r.left) / r.width, fy = (ev.clientY - r.top) / r.height;
    let lat, lon;
    if (mapCrs === "4326") { lon = box.lonMin + fx * (box.lonMax - box.lonMin); lat = box.latMax - fy * (box.latMax - box.latMin); read.textContent = `${fmt(lon, 5)}, ${fmt(lat, 5)}  (x = longitude, y = latitude, decimal degrees)`; }
    else { const a = webMercator(box.latMin, box.lonMin), b = webMercator(box.latMax, box.lonMax); const x = a.x + fx * (b.x - a.x), y = b.y - fy * (b.y - a.y); read.textContent = `${fmtInt(x)} m, ${fmtInt(y)} m  (x, y in Web Mercator metres)`; }
  });
  el.querySelectorAll(".lyr[data-id]").forEach(d => d.addEventListener("click", () => { el.querySelectorAll(".lyr").forEach(x => x.classList.remove("on")); d.classList.add("on"); if (opts.onLayer) opts.onLayer(layers.find(l => l.id === d.dataset.id)); }));
}

/* ---- navigation, progress ---- */
const MODULES = [
  { id: "5.1", file: "m1-two-numbers.html", title: "Two numbers are not a place" },
  { id: "5.2", file: "m2-latlon.html", title: "Latitude and longitude" },
  { id: "5.3", file: "m3-earth-model.html", title: "Shape, ellipsoid, datum" },
  { id: "5.4", file: "m4-geo-vs-projected.html", title: "Geographic vs projected" },
  { id: "5.5", file: "m5-order.html", title: "Coordinate order" },
  { id: "5.6", file: "m6-height.html", title: "Height and Z" },
  { id: "5.7", file: "m7-inspect.html", title: "Data CRS vs map CRS" },
  { id: "5.8", file: "m8-lab.html", title: "Lab: coordinate detective" },
  { id: "5.9", file: "m9-check.html", title: "Independent check" },
  { id: "5.10", file: "m10-media.html", title: "Recap & next" }
];
const PROG_KEY = "gis-ch5-progress";
function getProgress() { try { return JSON.parse(localStorage.getItem(PROG_KEY) || "{}"); } catch { return {}; } }
function setDone(id, v) { try { const p = getProgress(); if (v) p[id] = true; else delete p[id]; localStorage.setItem(PROG_KEY, JSON.stringify(p)); } catch {} }

function buildChrome() {
  const here = location.pathname.split("/").pop() || "index.html";
  const idx = MODULES.findIndex(m => m.file === here);
  const prog = getProgress();
  const doneCount = MODULES.filter(m => prog[m.id]).length;
  const bar = document.createElement("header");
  bar.className = "topbar";
  bar.innerHTML = `<div class="inner">
    <a class="brand" href="index.html">GIS Phase 1 · <span>Chapter 5</span></a>
    <nav class="modnav" aria-label="Modules">${MODULES.map((m, i) => `<a href="${m.file}" class="${i === idx ? "active" : ""} ${prog[m.id] ? "done" : ""}" title="${m.title}">${m.id}</a>`).join("")}<a href="glossary.html" class="${here === "glossary.html" ? "active" : ""}">Glossary</a></nav>
  </div><div class="progress"><div style="width:${(doneCount / MODULES.length) * 100}%"></div></div>`;
  document.body.prepend(bar);
  if (idx >= 0) {
    const m = MODULES[idx];
    const pager = document.createElement("div");
    pager.className = "pager";
    const prev = idx > 0 ? MODULES[idx - 1] : { file: "index.html", title: "Chapter home", id: "" };
    const next = idx < MODULES.length - 1 ? MODULES[idx + 1] : { file: "index.html", title: "Chapter home", id: "" };
    pager.innerHTML = `<a href="${prev.file}">← ${prev.id} ${prev.title}</a>
      <div class="done-wrap"><label><input type="checkbox" id="doneBox" ${prog[m.id] ? "checked" : ""}> I have finished module ${m.id}</label></div>
      <a href="${next.file}">${next.id} ${next.title} →</a>`;
    document.querySelector("main").appendChild(pager);
    pager.querySelector("#doneBox").addEventListener("change", e => { setDone(m.id, e.target.checked); location.reload(); });
  }
}

/* ---- quick-check quizzes (same markup as Chapters 1–3) ---- */
function initQuizzes() {
  document.querySelectorAll(".quiz").forEach(q => {
    const ans = parseInt(q.dataset.answer, 10);
    const fb = q.querySelector(".fb");
    q.querySelectorAll(".opt").forEach((b, i) => {
      b.addEventListener("click", () => {
        q.querySelectorAll(".opt").forEach(x => x.classList.remove("right", "wrong"));
        const ok = i === ans;
        b.classList.add(ok ? "right" : "wrong");
        if (!ok) q.querySelectorAll(".opt")[ans].classList.add("right");
        fb.className = "fb show " + (ok ? "ok" : "no");
        fb.textContent = (ok ? "Correct. " : "Not quite. ") + (q.dataset.fb || "");
      });
    });
  });
}

/* ---- click-to-sort activity ---- */
function initSorters() {
  document.querySelectorAll(".sorter").forEach(s => {
    const items = JSON.parse(s.dataset.items);
    const wrap = s.querySelector(".items");
    let active = null;
    items.forEach((it, i) => {
      const b = document.createElement("button"); b.className = "item"; b.textContent = it.t; b.dataset.i = i;
      b.addEventListener("click", () => { if (b.classList.contains("placed")) return; wrap.querySelectorAll(".item").forEach(x => x.classList.remove("active")); b.classList.add("active"); active = i; });
      wrap.appendChild(b);
    });
    s.querySelectorAll(".bin").forEach(bin => {
      bin.addEventListener("click", () => {
        if (active == null) return;
        const it = items[active];
        const ok = it.bin === bin.dataset.bin;
        const d = document.createElement("div"); d.className = "placed-item " + (ok ? "ok" : "bad");
        d.textContent = (ok ? "✓ " : "✗ ") + it.t + (ok ? "" : ` → belongs in “${it.bin}”`) + (it.why ? " — " + it.why : "");
        bin.appendChild(d);
        const btn = wrap.querySelector(`.item[data-i="${active}"]`); btn.classList.remove("active"); btn.classList.add("placed");
        active = null;
      });
    });
  });
}

/* ---- tabs ---- */
function initTabs() {
  document.querySelectorAll(".tabs").forEach(tabs => {
    const btns = tabs.querySelectorAll("button"); const panels = tabs.parentElement.querySelectorAll(".tabpanel");
    btns.forEach((b, i) => b.addEventListener("click", () => { btns.forEach(x => x.setAttribute("aria-selected", "false")); panels.forEach(p => p.classList.remove("show")); b.setAttribute("aria-selected", "true"); panels[i].classList.add("show"); }));
    if (btns[0]) btns[0].click();
  });
}

document.addEventListener("DOMContentLoaded", () => {
  buildChrome();
  initQuizzes();
  initSorters();
  initTabs();
  if (window.pageInit) window.pageInit();
});
