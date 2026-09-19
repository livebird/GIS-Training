/* Chapter 7 tutorial — Fixture F7 data, the "what does each format do to a record" rules, SVG helpers.
   Table F7 is MADE-UP practice data on the flat training grid (metres, no Earth location).
   The format rules below are taken from the chapter document's cited sources:
   RFC 7946 (GeoJSON), OGC GeoPackage 1.4.0, Esri "Geoprocessing considerations for shapefile output",
   GDAL shapefile / CSV / GeoJSON driver pages, ArcGIS Pro field-type pages. They are predictions,
   not executed conversions — the lab asks you to record what your software really did. */

/* ---------- Fixture F7: assets, Chapter 7 exchange variant ---------- */
const F7 = {
  fields: [
    { name: "asset_id",           type: "text",      len: 8,  note: "Business ID: two letters, hyphen, four digits. Unique." },
    { name: "legacy_code",        type: "text",      len: 11, note: "Old register code. Leading zeros matter: 0113 is not 113." },
    { name: "asset_type_en",      type: "text",      len: 13, note: "Asset type, formal classification." },
    { name: "asset_type_local",   type: "text",      len: 16, note: "Asset type, the crew's everyday wording (not the formal classification)." },
    { name: "installed_year",     type: "integer",   len: 14, note: "Year of installation." },
    { name: "condition_score",    type: "decimal",   len: 15, note: "0.0 to 5.0. Null = not yet assessed (NOT zero)." },
    { name: "last_inspection_at", type: "timestamp", len: 18, note: "ISO 8601 local time with +05:30 offset. Null = never inspected." },
    { name: "inspector_note",     type: "text",      len: 14, note: "Free text. Empty = no note (different from null)." },
    { name: "x",                  type: "decimal",   len: 1,  note: "Training-grid metres. No Earth CRS." },
    { name: "y",                  type: "decimal",   len: 1,  note: "Training-grid metres. No Earth CRS." }
  ],
  rows: [
    { asset_id: "SL-0113", legacy_code: "0113", asset_type_en: "Streetlight", asset_type_local: "Street lamp", installed_year: 2018, condition_score: 3.5,  last_inspection_at: "2026-03-14T10:42:00+05:30", inspector_note: "Lamp flickers at dusk", x: 205,  y: 195, ward: "A" },
    { asset_id: "DR-0042", legacy_code: "0042", asset_type_en: "Drain",       asset_type_local: "Storm drain", installed_year: 2011, condition_score: 1.5,  last_inspection_at: "2025-11-02T15:05:00+05:30", inspector_note: "Silt build-up — needs clearing", x: 995,  y: 510, ward: "A" },
    { asset_id: "TR-0301", legacy_code: "0301", asset_type_en: "Tree",        asset_type_local: "Shade tree",  installed_year: 2005, condition_score: null, last_inspection_at: null,                        inspector_note: "",                      x: 2190, y: 520, ward: "outside" },
    { asset_id: "BN-0007", legacy_code: "0007", asset_type_en: "Bench",       asset_type_local: "Park bench",  installed_year: 2022, condition_score: 5.0,  last_inspection_at: "2026-06-01T09:00:00+05:30", inspector_note: "Repainted",             x: 1500, y: 300, ward: "B" },
    { asset_id: "DR-0110", legacy_code: "0110", asset_type_en: "Drain",       asset_type_local: "Storm drain", installed_year: 2015, condition_score: 2.0,  last_inspection_at: "2026-01-20T11:30:00+05:30", inspector_note: "Grate missing",         x: 400,  y: 700, ward: "A" },
    { asset_id: "SL-0055", legacy_code: "0055", asset_type_en: "Streetlight", asset_type_local: "Street lamp", installed_year: 2020, condition_score: 4.0,  last_inspection_at: "2026-08-30T02:10:00+05:30", inspector_note: "Night check: lamp OK",  x: 1800, y: 950, ward: "B" }
  ]
};
F7.csvText = () => [F7.fields.map(f => f.name).join(",")].concat(F7.rows.map(r => [
  r.asset_id, '"' + r.legacy_code + '"', r.asset_type_en, r.asset_type_local, r.installed_year,
  r.condition_score == null ? "" : r.condition_score.toFixed(1), r.last_inspection_at ?? "",
  /[,—]/.test(r.inspector_note) ? '"' + r.inspector_note + '"' : r.inspector_note, r.x, r.y].join(","))).join("\n");

/* Chapter 1 requests (planar grid) — used by the old-boundary story in 7.6 */
const REQUESTS = [
  { id: "P1", x: 200, y: 200 }, { id: "P2", x: 800, y: 800 }, { id: "P3", x: 1200, y: 250 },
  { id: "P4", x: 1700, y: 900 }, { id: "P5", x: 1000, y: 500 }, { id: "P6", x: 2200, y: 500 }
];
/* Strict-interior ward membership when the A/B dividing line is at x = split (A: 0..split, B: split..2000). */
function wardOf(p, split) {
  if (p.y <= 0 || p.y >= 1000 || p.x <= 0 || p.x >= 2000) return "outside";
  if (p.x === split) return "boundary";
  return p.x < split ? "A" : "B";
}

/* ---------- time helpers ---------- */
/* "2026-08-30T02:10:00+05:30" → "2026-08-29T20:40:00Z" (exact arithmetic on the string; no Date-object timezone surprises) */
function toUTC(iso) {
  const m = iso.match(/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})([+-])(\d{2}):(\d{2})$/);
  if (!m) return iso;
  const sign = m[7] === "+" ? 1 : -1;
  const t = Date.UTC(+m[1], +m[2] - 1, +m[3], +m[4], +m[5], +m[6]) - sign * ((+m[8]) * 60 + (+m[9])) * 60000;
  return new Date(t).toISOString().replace(".000Z", "Z");
}
const dateOnly = iso => iso ? iso.slice(0, 10) : null;

/* ---------- the format rules engine ----------
   convertRow(row, format, opts) → { fields: [ { name, outName, before, after, status, why } ] }
   status: kept | adapted | lost | depends
   opts: { tsTyped: true|false (timestamp typed as date-time before export),
           utcFirst: true|false, quoted: true|false (leading-zero code declared as text), nullPolicy: "zero"|"neg" } */
function shortName(name, used) {
  // Shapefile: 10 characters. Two names that collide after cutting must be told apart — HOW is product-specific.
  let s = name.slice(0, 10);
  if (used.has(s)) { let i = 1, cand; do { cand = name.slice(0, 8) + "_" + i; i++; } while (used.has(cand)); used.add(cand); return cand + " (or similar — the product decides)"; }
  used.add(s);
  return s;
}
function convertRow(row, format, opts = {}) {
  const o = Object.assign({ tsTyped: false, utcFirst: false, quoted: true, nullPolicy: "zero" }, opts);
  const out = { fields: [] };
  const used = new Set();
  const isNull = v => v === null || v === undefined;
  F7.fields.forEach(f => {
    const before = row[f.name];
    let outName = f.name, after = before, status = "kept", why = "";
    /* --- field names --- */
    if (format === "shp" && f.name.length > 10) {
      outName = shortName(f.name, used);
      status = "adapted"; why = "Shapefile field names are limited to 10 characters (Esri; GDAL). Write down the old → new mapping.";
    } else if (format === "shp") { used.add(f.name); }
    /* --- leading zeros --- */
    if (f.name === "legacy_code") {
      if (!o.quoted) { after = String(parseInt(before, 10)); status = "lost"; why = "The importer guessed 'number' because every value was digits, so the zeros are gone. Declare the type (quote it, schema.ini, .csvt) to keep them."; }
      else if (format === "geojson") { after = '"' + before + '"'; why = "Written as a JSON string — zeros kept."; }
      else { why = (why ? why + " " : "") + "Text type declared — zeros kept."; }
    }
    /* --- nulls --- */
    if (f.name === "condition_score" && isNull(before)) {
      if (format === "shp") { after = o.nullPolicy === "zero" ? 0 : -1.7976931348623158e308; status = "lost"; why = "Shapefiles cannot store null. Esri's table: most export tools write 0; tools that must output NULL/NaN write the extreme negative number. 'Not assessed' silently becomes a score."; }
      else if (format === "csv") { after = "(empty)"; status = "depends"; why = "An empty field. Whether the reader treats it as null or as text is up to the reader — say so in the readme."; }
      else { after = format === "geojson" ? "null" : "NULL"; why = "Real null kept."; }
    }
    /* --- timestamps --- */
    if (f.name === "last_inspection_at") {
      if (isNull(before)) {
        if (format === "shp") { after = "0 (shows as <null>)"; status = "lost"; why = "A date null is stored as zero but displayed as <null> — other software cannot tell it from a real zero date."; }
        else if (format === "csv") { after = "(empty)"; status = "depends"; why = "Empty field; its meaning must come from the readme."; }
        else { after = "null"; why = "Real null kept."; }
      } else if (format === "shp") {
        if (o.tsTyped) { after = dateOnly(o.utcFirst ? toUTC(before) : before); status = "lost"; why = "Shapefile date fields hold a date only — no time, no offset. " + (o.utcFirst ? "Converted to UTC first, so the calendar date may have moved back a day." : "The local date was kept; the time is gone."); }
        else { after = before; why = "Kept as text (the field was typed as Text, so the string survives unchanged). Its meaning as a time is up to the reader."; }
      } else if (format === "gpkg") {
        if (o.tsTyped) { after = toUTC(before).replace("Z", ".000Z"); status = "adapted"; why = "GeoPackage DATETIME is ISO 8601 in UTC with a Z suffix. Same instant, different clock — the +05:30 offset is gone. Document it."; }
        else { after = before; why = "Kept as TEXT because the field was typed as text."; }
      } else if (format === "geojson") { after = '"' + before + '"'; why = "GeoJSON has no date type; the ISO string travels as text."; }
      else if (format === "gdb") { after = before; status = o.tsTyped ? "kept" : "depends"; why = "A geodatabase 'timestamp offset' field (ArcGIS Pro 3.2+) keeps the offset; a plain Date field would drop it — choose deliberately."; }
    }
    if (f.name === "inspector_note" && before === "") {
      if (format === "shp") { after = "' ' (one space)"; status = "lost"; why = "Empty text is written as a single space; 'no note' and 'a space' now look the same."; }
      else if (format === "csv") { after = "(empty)"; status = "depends"; why = "Empty field — null or empty string? Only the readme can say."; }
      else { after = format === "geojson" ? '""' : "'' or NULL (record which)"; status = "depends"; why = "Some writers turn an empty string into NULL. Either is fine if the readme says so."; }
    }
    /* --- coordinates / CRS --- */
    if (f.name === "x" || f.name === "y") {
      if (format === "geojson") { status = "lost"; why = "RFC 7946 says GeoJSON coordinates are WGS 84 longitude/latitude in degrees. Grid metres do not fit. Only a written 'prior arrangement' between sender and receiver allows this, and the file must be labelled non-standard."; }
      else if (format === "shp") { status = "depends"; why = "Geometry is fine; the CRS lives in the optional .prj. No .prj means 'unknown' (which is not the same as 'a local grid'). Write the readme."; }
      else if (format === "gpkg") { why = "Geometry kept; gpkg_spatial_ref_sys has an honest slot for an undefined flat grid: srs_id −1."; }
      else if (format === "gdb") { why = "Geometry kept; the feature class can carry an 'Unknown' or a documented local coordinate system."; }
      else { status = "depends"; why = "Numbers kept; a CSV has nowhere to say what they mean — the readme must."; }
    }
    if (format === "csv" && f.name === "installed_year") { status = "depends"; why = "Digits only — will be read as a number (fine here). If a value had a leading zero, it would not be."; }
    out.fields.push({ name: f.name, outName, before: isNull(before) ? "null" : before === "" ? '"" (empty)' : before, after: isNull(after) ? "null" : after, status, why });
  });
  return out;
}
const FORMAT_LABEL = { csv: "Coordinate CSV", geojson: "GeoJSON (RFC 7946)", shp: "Shapefile", gpkg: "GeoPackage", gdb: "File geodatabase" };
const STATUS_LABEL = { kept: "kept", adapted: "adapted (documented change)", lost: "LOST (silent change)", depends: "depends on tool / readme" };

/* ---------- small formatting helpers ---------- */
const fmtN = (n, d = 0) => n.toLocaleString("en-IN", { minimumFractionDigits: d, maximumFractionDigits: d });
const esc = s => String(s).replace(/[&<>"]/g, c => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" }[c]));

/* ---------- SVG helpers ---------- */
const NS = "http://www.w3.org/2000/svg";
function mkEl(tag, attrs, parent) { const n = document.createElementNS(NS, tag); for (const k in attrs) n.setAttribute(k, attrs[k]); if (parent) parent.appendChild(n); return n; }
function txt(parent, x, y, s, cls = "lbl", extra = {}) { const t = mkEl("text", Object.assign({ x, y, class: cls }, extra), parent); t.textContent = s; return t; }
function newSvg(el, viewBox, label) { const svg = mkEl("svg", { viewBox, role: "img", "aria-label": label }); el.innerHTML = ""; el.appendChild(svg); return svg; }

/* Draw the training grid (wards A/B, road R1) with optional request points, assets, and an alternative boundary line. */
function renderGrid(el, opts = {}) {
  const o = Object.assign({ requests: false, assets: false, split: null, caption: null, hl: {} }, opts);
  const W = 2560, H = 1250, ox = 140;
  const SX = x => ox + x, SY = y => 1140 - y;
  const svg = newSvg(el, `0 0 ${W} ${H}`, "Schematic map of the made-up practice town on a flat metre grid.");
  mkEl("line", { x1: SX(0), y1: SY(0), x2: SX(2300), y2: SY(0), class: "axis" }, svg);
  mkEl("line", { x1: SX(-40), y1: SY(40), x2: SX(-40), y2: SY(1090), class: "axis" }, svg);
  [0, 500, 1000, 1500, 2000].forEach(v => txt(svg, SX(v), SY(0) + 55, String(v), "axis-label", { "text-anchor": "middle" }));
  [0, 500, 1000].forEach(v => txt(svg, SX(-75), SY(v) + 10, String(v), "axis-label", { "text-anchor": "end" }));
  txt(svg, SX(2320), SY(0) + 12, "x (m)", "axis-label"); txt(svg, SX(-120), SY(1090) - 30, "y (m)", "axis-label");
  const split = o.split ?? 1000;
  mkEl("polygon", { points: `${SX(0)},${SY(0)} ${SX(split)},${SY(0)} ${SX(split)},${SY(1000)} ${SX(0)},${SY(1000)}`, class: "ward" + (o.hl.A ? " hl" : "") }, svg);
  mkEl("polygon", { points: `${SX(split)},${SY(0)} ${SX(2000)},${SY(0)} ${SX(2000)},${SY(1000)} ${SX(split)},${SY(1000)}`, class: "ward" + (o.hl.B ? " hl" : "") }, svg);
  if (o.split && o.split !== 1000) { mkEl("line", { x1: SX(1000), y1: SY(0), x2: SX(1000), y2: SY(1000), class: "dline" }, svg); txt(svg, SX(1000) + 14, SY(60), "current line (2024)", "lbl"); txt(svg, SX(split) + 14, SY(120), "line in this file (2019)", "lbl warn"); }
  txt(svg, SX(split / 2), SY(760), "Ward A", "ward-label", { "text-anchor": "middle" });
  txt(svg, SX((split + 2000) / 2), SY(760), "Ward B", "ward-label", { "text-anchor": "middle" });
  mkEl("line", { x1: SX(0), y1: SY(500), x2: SX(2000), y2: SY(500), class: "road" }, svg);
  txt(svg, SX(1020), SY(530), "R1 · Main Road", "road-label");
  if (o.requests) REQUESTS.forEach(p => { const w = wardOf(p, split); mkEl("circle", { cx: SX(p.x), cy: SY(p.y), r: 26, class: "req" + (w === "A" ? " ina" : w === "B" ? " inb" : w === "boundary" ? " onb" : " dim") }, svg); txt(svg, SX(p.x) + 40, SY(p.y) + 15, p.id, "req-label"); });
  if (o.assets) F7.rows.forEach(a => { mkEl("rect", { x: SX(a.x) - 22, y: SY(a.y) - 22, width: 44, height: 44, class: "asset" }, svg); txt(svg, SX(a.x) + 34, SY(a.y) + 12, a.asset_id, "asset-label"); });
  if (o.caption) { const c = document.createElement("figcaption"); c.textContent = o.caption; el.appendChild(c); }
  return svg;
}

/* A tiny schematic "world" (a longitude/latitude rectangle) to show where [lon, lat] vs [lat, lon] land. Not a real map. */
function renderWorld(el, points, caption) {
  const W = 1000, H = 560, pad = 40;
  const SX = lon => pad + (lon + 180) / 360 * (W - 2 * pad), SY = lat => pad + (90 - lat) / 180 * (H - 2 * pad - 40);
  const svg = newSvg(el, `0 0 ${W} ${H}`, "Schematic world rectangle with longitude and latitude lines, showing where two coordinate pairs land.");
  mkEl("rect", { x: pad, y: pad, width: W - 2 * pad, height: H - 2 * pad - 40, class: "worldbox" }, svg);
  for (let lon = -180; lon <= 180; lon += 60) { mkEl("line", { x1: SX(lon), y1: SY(90), x2: SX(lon), y2: SY(-90), class: "grat" }, svg); txt(svg, SX(lon), H - 45, `${lon}°`, "lbl", { "text-anchor": "middle" }); }
  for (let lat = -90; lat <= 90; lat += 30) { mkEl("line", { x1: SX(-180), y1: SY(lat), x2: SX(180), y2: SY(lat), class: "grat" + (lat === 0 ? " eq" : "") }, svg); txt(svg, 8, SY(lat) + 8, `${lat}°`, "lbl"); }
  txt(svg, W / 2, 28, "longitude → (west −180 … east +180)", "lbl", { "text-anchor": "middle" });
  txt(svg, SX(66), SY(4), "≈ India", "lbl faint"); txt(svg, SX(8), SY(60), "≈ Norway", "lbl faint", { "text-anchor": "end" });
  points.forEach(p => { mkEl("circle", { cx: SX(p.lon), cy: SY(p.lat), r: 12, class: "wpt " + (p.cls || "") }, svg); txt(svg, SX(p.lon) + 16, SY(p.lat) + 8, p.label, "wlbl " + (p.cls || "")); });
  if (caption) { const c = document.createElement("figcaption"); c.textContent = caption; el.appendChild(c); }
  return svg;
}

/* Render rows into a <table> element */
function renderTable(el, cols, rows) {
  const th = cols.map(c => `<th>${esc(c)}</th>`).join("");
  const tb = rows.map(r => `<tr>${cols.map(c => { const v = r[c]; const mono = typeof v === "number" || /^\d/.test(String(v)); return `<td class="${mono ? "mono" : ""}">${v === null || v === undefined ? '<span class="nullv">null</span>' : v === "" ? '<span class="nullv">(empty)</span>' : esc(v)}</td>`; }).join("")}</tr>`).join("");
  el.innerHTML = `<thead><tr>${th}</tr></thead><tbody>${tb}</tbody>`;
}

if (typeof module !== "undefined") module.exports = { F7, REQUESTS, wardOf, toUTC, convertRow };
