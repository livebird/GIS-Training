/* Chapter 6 tutorial — Fixture E6 data, projection maths, SVG helpers, navigation, quizzes.
   Fixture E6 is MADE-UP practice data. Its coordinates were chosen only because they sit on the
   central meridian of UTM zone 43N (75° E). It is not a real town, ward or authority.
   Formulas: IOGP/EPSG Guidance Note 7-2 (Sept 2019) §3.2.3.1 (Transverse Mercator, JHS),
   §3.2.1.2 (Web Mercator), §4.1.1 & §4.2.4 (geocentric); Vincenty (1975) inverse for geodesics. */

/* ---------- ellipsoid ---------- */
const WGS84 = { a: 6378137.0, invf: 298.257223563 };
WGS84.f = 1 / WGS84.invf; WGS84.b = WGS84.a * (1 - WGS84.f); WGS84.e2 = 2 * WGS84.f - WGS84.f * WGS84.f;
const EVEREST75 = { a: 6377299.151, invf: 300.8017255 };
EVEREST75.f = 1 / EVEREST75.invf; EVEREST75.e2 = 2 * EVEREST75.f - EVEREST75.f * EVEREST75.f;
const rad = d => d * Math.PI / 180, deg = r => r * 180 / Math.PI;

/* ---------- Transverse Mercator (JHS / Krüger series), lat0 = 0 ---------- */
function tmForward(lat, lon, lon0, k0 = 0.9996, FE = 500000, FN = 0, el = WGS84) {
  const { a, f } = el, e = Math.sqrt(2 * f - f * f), n = f / (2 - f);
  const B = (a / (1 + n)) * (1 + n * n / 4 + Math.pow(n, 4) / 64);
  const h1 = n / 2 - (2 / 3) * n * n + (5 / 16) * Math.pow(n, 3) + (41 / 180) * Math.pow(n, 4);
  const h2 = (13 / 48) * n * n - (3 / 5) * Math.pow(n, 3) + (557 / 1440) * Math.pow(n, 4);
  const h3 = (61 / 240) * Math.pow(n, 3) - (103 / 140) * Math.pow(n, 4);
  const h4 = (49561 / 161280) * Math.pow(n, 4);
  const phi = rad(lat), dl = rad(lon - lon0);
  const Q = Math.asinh(Math.tan(phi)) - e * Math.atanh(e * Math.sin(phi));
  const beta = Math.atan(Math.sinh(Q));
  const eta0 = Math.atanh(Math.cos(beta) * Math.sin(dl));
  const xi0 = Math.asin(Math.sin(beta) * Math.cosh(eta0));
  const xi = xi0 + h1 * Math.sin(2 * xi0) * Math.cosh(2 * eta0) + h2 * Math.sin(4 * xi0) * Math.cosh(4 * eta0) + h3 * Math.sin(6 * xi0) * Math.cosh(6 * eta0) + h4 * Math.sin(8 * xi0) * Math.cosh(8 * eta0);
  const eta = eta0 + h1 * Math.cos(2 * xi0) * Math.sinh(2 * eta0) + h2 * Math.cos(4 * xi0) * Math.sinh(4 * eta0) + h3 * Math.cos(6 * xi0) * Math.sinh(6 * eta0) + h4 * Math.cos(8 * xi0) * Math.sinh(8 * eta0);
  return { E: FE + k0 * B * eta, N: FN + k0 * B * xi };
}
function utmZone(lon) { return Math.floor((lon + 180) / 6) + 1; }
function utmCM(zone) { return -183 + 6 * zone; }
function utm(lat, lon, zone) { const z = zone || utmZone(lon); return Object.assign(tmForward(lat, lon, utmCM(z)), { zone: z, cm: utmCM(z) }); }

/* ---------- Web Mercator (EPSG:3857) ---------- */
function webMerc(lat, lon) { return { X: WGS84.a * rad(lon), Y: WGS84.a * Math.log(Math.tan(Math.PI / 4 + rad(lat) / 2)) }; }
function rho(lat, el = WGS84) { return el.a * (1 - el.e2) / Math.pow(1 - el.e2 * Math.sin(rad(lat)) ** 2, 1.5); }
function nu(lat, el = WGS84) { return el.a / Math.sqrt(1 - el.e2 * Math.sin(rad(lat)) ** 2); }
function wmFactors(lat) { const c = Math.cos(rad(lat)); return { h: WGS84.a / (rho(lat) * c), k: WGS84.a / (nu(lat) * c) }; }

/* ---------- geodesic (Vincenty inverse) ---------- */
function geodesic(lat1, lon1, lat2, lon2, el = WGS84) {
  const { a, b, f } = el;
  const U1 = Math.atan((1 - f) * Math.tan(rad(lat1))), U2 = Math.atan((1 - f) * Math.tan(rad(lat2)));
  const L = rad(lon2 - lon1); let lam = L;
  const sU1 = Math.sin(U1), cU1 = Math.cos(U1), sU2 = Math.sin(U2), cU2 = Math.cos(U2);
  let sinS, cosS, sigma, sinA, cos2A, cos2SM;
  for (let i = 0; i < 200; i++) {
    const sl = Math.sin(lam), cl = Math.cos(lam);
    sinS = Math.sqrt((cU2 * sl) ** 2 + (cU1 * sU2 - sU1 * cU2 * cl) ** 2);
    if (sinS === 0) return 0;
    cosS = sU1 * sU2 + cU1 * cU2 * cl; sigma = Math.atan2(sinS, cosS);
    sinA = cU1 * cU2 * sl / sinS; cos2A = 1 - sinA * sinA;
    cos2SM = cos2A !== 0 ? cosS - 2 * sU1 * sU2 / cos2A : 0;
    const C = f / 16 * cos2A * (4 + f * (4 - 3 * cos2A));
    const prev = lam;
    lam = L + (1 - C) * f * sinA * (sigma + C * sinS * (cos2SM + C * cosS * (-1 + 2 * cos2SM * cos2SM)));
    if (Math.abs(lam - prev) < 1e-15) break;
  }
  const u2 = cos2A * (a * a - b * b) / (b * b);
  const A = 1 + u2 / 16384 * (4096 + u2 * (-768 + u2 * (320 - 175 * u2)));
  const Bc = u2 / 1024 * (256 + u2 * (-128 + u2 * (74 - 47 * u2)));
  const dS = Bc * sinS * (cos2SM + Bc / 4 * (cosS * (-1 + 2 * cos2SM * cos2SM) - Bc / 6 * cos2SM * (-3 + 4 * sinS * sinS) * (-3 + 4 * cos2SM * cos2SM)));
  return b * A * (sigma - dS);
}
function haversine(lat1, lon1, lat2, lon2, R = 6371008.8) {
  const p1 = rad(lat1), p2 = rad(lat2), dp = p2 - p1, dl = rad(lon2 - lon1);
  const h = Math.sin(dp / 2) ** 2 + Math.cos(p1) * Math.cos(p2) * Math.sin(dl / 2) ** 2;
  return 2 * R * Math.asin(Math.sqrt(h));
}
function meridianArc(lat1, lat2, steps = 2000) { // Simpson integration of rho
  const p1 = rad(lat1), p2 = rad(lat2), h = (p2 - p1) / steps; const r = p => WGS84.a * (1 - WGS84.e2) / Math.pow(1 - WGS84.e2 * Math.sin(p) ** 2, 1.5);
  let s = r(p1) + r(p2); for (let i = 1; i < steps; i++) s += (i % 2 ? 4 : 2) * r(p1 + i * h); return s * h / 3;
}
function parallelArc(lat, dlon) { return nu(lat) * Math.cos(rad(lat)) * rad(dlon); }
function ellipsoidRectArea(lat1, lat2, lon1, lon2, steps = 2000) {
  const p1 = rad(lat1), p2 = rad(lat2), h = (p2 - p1) / steps;
  const g = p => { const w = 1 - WGS84.e2 * Math.sin(p) ** 2; return WGS84.a * (1 - WGS84.e2) / Math.pow(w, 1.5) * WGS84.a / Math.sqrt(w) * Math.cos(p); };
  let s = g(p1) + g(p2); for (let i = 1; i < steps; i++) s += (i % 2 ? 4 : 2) * g(p1 + i * h); return s * h / 3 * rad(lon2 - lon1);
}
function shoelace(pts) { let s = 0; for (let i = 0; i < pts.length; i++) { const a = pts[i], b = pts[(i + 1) % pts.length]; s += a[0] * b[1] - b[0] * a[1]; } return Math.abs(s) / 2; }
const hyp = (p, q) => Math.hypot(q[0] - p[0], q[1] - p[1]);

/* ---------- datum shift (3-parameter geocentric translation) ---------- */
function geogToGeoc(lat, lon, h, el) { const p = rad(lat), l = rad(lon), n = nu(lat, el); return [(n + h) * Math.cos(p) * Math.cos(l), (n + h) * Math.cos(p) * Math.sin(l), ((1 - el.e2) * n + h) * Math.sin(p)]; }
function geocToGeog(X, Y, Z, el) {
  const p = Math.hypot(X, Y); let lat = Math.atan2(Z, p * (1 - el.e2));
  for (let i = 0; i < 50; i++) { const n = el.a / Math.sqrt(1 - el.e2 * Math.sin(lat) ** 2); lat = Math.atan2(Z + el.e2 * n * Math.sin(lat), p); }
  return { lat: deg(lat), lon: deg(Math.atan2(Y, X)) };
}
function kalianpurToWGS84(lat, lon) { const [X, Y, Z] = geogToGeoc(lat, lon, 0, EVEREST75); return geocToGeog(X + 295, Y + 736, Z + 257, WGS84); } // EPSG:1156

/* ---------- Fixture E6 (synthetic) ---------- */
const E6 = {
  wards: [
    { id: "A", lon: [74.990, 75.000], lat: [23.000, 23.010] },
    { id: "B", lon: [75.000, 75.010], lat: [23.000, 23.010] }
  ],
  requests: [
    { id: "Q1", lat: 23.002, lon: 75.000, cat: "Blocked drain", where: "On the shared A/B boundary" },
    { id: "Q2", lat: 23.008, lon: 75.000, cat: "Streetlight out", where: "On the shared A/B boundary" },
    { id: "Q3", lat: 23.005, lon: 74.995, cat: "Pothole", where: "Inside Ward A" },
    { id: "Q4", lat: 23.005, lon: 75.005, cat: "Water leak", where: "Inside Ward B" },
    { id: "Q5", lat: 23.005, lon: 75.012, cat: "Fallen tree", where: "Outside both wards (east of B)" }
  ],
  zone: 43, cm: 75
};
E6.requests.forEach(q => { const u = utm(q.lat, q.lon, 43); q.E = u.E; q.N = u.N; const w = webMerc(q.lat, q.lon); q.X = w.X; q.Y = w.Y; });
function wardRing(w) { return [[w.lon[0], w.lat[0]], [w.lon[1], w.lat[0]], [w.lon[1], w.lat[1]], [w.lon[0], w.lat[1]]]; } // lon,lat
const fx = (n, d = 3) => n.toFixed(d);
const fmtN = (n, d = 0) => n.toLocaleString("en-IN", { minimumFractionDigits: d, maximumFractionDigits: d });

/* ---------- SVG helpers ---------- */
const NS = "http://www.w3.org/2000/svg";
function mkEl(tag, attrs, parent) { const n = document.createElementNS(NS, tag); for (const k in attrs) n.setAttribute(k, attrs[k]); if (parent) parent.appendChild(n); return n; }
function txt(parent, x, y, s, cls = "lbl", extra = {}) { const t = mkEl("text", Object.assign({ x, y, class: cls }, extra), parent); t.textContent = s; return t; }
function newSvg(el, viewBox, label) { const svg = mkEl("svg", { viewBox, role: "img", "aria-label": label }); el.innerHTML = ""; el.appendChild(svg); return svg; }

/* Draw Fixture E6 wards + requests in a chosen "map CRS" (utm | wm | deg) into el. */
function renderE6(el, opts = {}) {
  const o = Object.assign({ crs: "utm", caption: null, moved: {}, ghost: [], shift: null, lines: [], labels: true }, opts);
  const proj = (lat, lon) => o.crs === "utm" ? (u => [u.E, u.N])(utm(lat, lon, 43)) : o.crs === "wm" ? (w => [w.X, w.Y])(webMerc(lat, lon)) : [lon * 1000, lat * 1000];
  const wards = E6.wards.map(w => wardRing(w).map(([lo, la]) => proj(la, lo)));
  const all = wards.flat().concat(E6.requests.map(q => proj(q.lat, q.lon)));
  const xs = all.map(p => p[0]), ys = all.map(p => p[1]);
  const xmin = Math.min(...xs), xmax = Math.max(...xs), ymin = Math.min(...ys), ymax = Math.max(...ys);
  const W = 1000, pad = 90; const sc = (W - 2 * pad) / Math.max(xmax - xmin, (ymax - ymin));
  const H = (ymax - ymin) * sc + 2 * pad + 60;
  const SX = x => pad + (x - xmin) * sc, SY = y => pad + (ymax - y) * sc;
  const svg = newSvg(el, `0 0 ${W} ${H}`, "Schematic map of made-up Fixture E6: two ward rectangles and five request points, drawn in the chosen map coordinate system.");
  wards.forEach((r, i) => { mkEl("polygon", { points: r.map(p => `${SX(p[0])},${SY(p[1])}`).join(" "), class: "wardE " + (o.crs === "wm" ? "wm" : o.crs === "utm" ? "utm" : "") }, svg); if (o.labels) txt(svg, SX((r[0][0] + r[1][0]) / 2), SY(r[0][1] + (r[2][1] - r[0][1]) * 0.12), "Ward " + E6.wards[i].id, "lbl big", { "text-anchor": "middle" }); });
  o.lines.forEach(l => { const a = proj(l.from[0], l.from[1]), b = proj(l.to[0], l.to[1]); mkEl("line", { x1: SX(a[0]), y1: SY(a[1]), x2: SX(b[0]), y2: SY(b[1]), class: "meas " + (l.cls || "") }, svg); if (l.label) txt(svg, (SX(a[0]) + SX(b[0])) / 2 + (l.dx || 12), (SY(a[1]) + SY(b[1])) / 2 + (l.dy || 0), l.label, "lbl " + (l.lcls || "")); });
  E6.requests.forEach(q => {
    const p = proj(q.lat, q.lon);
    if (o.ghost.includes(q.id)) { mkEl("circle", { cx: SX(p[0]), cy: SY(p[1]), r: 12, class: "reqE ghost" }, svg); }
    else mkEl("circle", { cx: SX(p[0]), cy: SY(p[1]), r: 14, class: "reqE" }, svg);
    if (o.labels) txt(svg, SX(p[0]) + 20, SY(p[1]) - 16, q.id, "lbl big");
    if (o.shift && o.shift[q.id]) { const s = o.shift[q.id], sp = proj(s[0], s[1]); mkEl("line", { x1: SX(p[0]), y1: SY(p[1]), x2: SX(sp[0]), y2: SY(sp[1]), class: "meas wm" }, svg); mkEl("circle", { cx: SX(sp[0]), cy: SY(sp[1]), r: 13, class: "reqE shift" }, svg); }
  });
  // scale bar: 500 m in this CRS' units (only meaningful for utm/wm)
  if (o.crs !== "deg") { const L = 500 * sc; mkEl("line", { x1: pad, y1: H - 30, x2: pad + L, y2: H - 30, class: "cm" }, svg); txt(svg, pad, H - 40, o.crs === "utm" ? "500 m on the UTM grid" : "500 “map metres” (Web Mercator)", "lbl"); }
  else { txt(svg, pad, H - 40, "0.005° of longitude ≈ 513 m on the ground here", "lbl"); }
  if (o.caption) { const c = document.createElement("figcaption"); c.textContent = o.caption; el.appendChild(c); }
  return svg;
}

/* Navigation, progress, quizzes, sorters and tabs live in /assets/common.js (shared by every chapter). */

if (typeof module !== "undefined") module.exports = { E6, utm, webMerc, geodesic, haversine, meridianArc, parallelArc, ellipsoidRectArea, shoelace, wmFactors, kalianpurToWGS84, wardRing, utmZone };
