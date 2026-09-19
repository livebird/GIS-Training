/* Chapter 1 tutorial — shared data, map renderer, navigation, quizzes.
   All coordinates are on a SYNTHETIC flat training grid in metres. No real place. */

const FIXTURE = {
  wards: [
    { id: "A", name: "Ward A", pts: [[0, 0], [1000, 0], [1000, 1000], [0, 1000]] },
    { id: "B", name: "Ward B", pts: [[1000, 0], [2000, 0], [2000, 1000], [1000, 1000]] }
  ],
  road: { id: "R1", name: "Main Road", a: [0, 500], b: [2000, 500] },
  requests: [
    { id: "P1", x: 200, y: 200, category: "Streetlight out", priority: "Medium", status: "Open", reported: "2026-09-02", closed: "", channel: "Mobile app", note: "" },
    { id: "P2", x: 800, y: 800, category: "Pothole", priority: "High", status: "In progress", reported: "2026-08-28", closed: "", channel: "Phone", note: "Crew assigned 2026-09-01" },
    { id: "P3", x: 1200, y: 250, category: "Water leak", priority: "High", status: "Open", reported: "2026-09-10", closed: "", channel: "Mobile app", note: "" },
    { id: "P4", x: 1700, y: 900, category: "Pothole", priority: "Low", status: "Resolved", reported: "2026-08-15", closed: "2026-08-20", channel: "Web form", note: "" },
    { id: "P5", x: 1000, y: 500, category: "Blocked drain", priority: "Medium", status: "Reopened", reported: "2026-08-30", closed: "", channel: "Phone", note: "Closed 2026-09-05; reopened 2026-09-12 after a second call" },
    { id: "P6", x: 2200, y: 500, category: "Fallen tree", priority: "High", status: "Closed – duplicate", reported: "2026-09-11", closed: "2026-09-12", channel: "Phone", note: "Marked as duplicate of a report not in this package" }
  ],
  assets: [
    { id: "SL-0113", type: "Streetlight", x: 205, y: 195, installed: 2018, inspected: "2026-03-14", condition: "Fair" },
    { id: "DR-0042", type: "Drain", x: 995, y: 510, installed: 2011, inspected: "2025-11-02", condition: "Poor" },
    { id: "TR-0301", type: "Tree", x: 2190, y: 520, installed: 2005, inspected: "", condition: "Unknown" }
  ]
};

/* ---- geometry (hand-checkable rules used throughout the chapter) ---- */
function distToRoad(x, y) {
  // Road R1 is the FINITE segment (0,500)–(2000,500).
  if (x >= 0 && x <= 2000) return Math.abs(y - 500);
  const ex = x < 0 ? 0 : 2000;
  return Math.hypot(x - ex, y - 500);
}
function wardsOf(x, y, inclusive) {
  const out = [];
  if (inclusive) {
    if (x >= 0 && x <= 1000 && y >= 0 && y <= 1000) out.push("A");
    if (x >= 1000 && x <= 2000 && y >= 0 && y <= 1000) out.push("B");
  } else {
    if (x > 0 && x < 1000 && y > 0 && y < 1000) out.push("A");
    if (x > 1000 && x < 2000 && y > 0 && y < 1000) out.push("B");
  }
  return out;
}
function fmt(n) { return Number.isInteger(n) ? String(n) : n.toFixed(1); }

/* ---- SVG map renderer ---- */
// Grid metres -> SVG: x stays, y is flipped so "up" is north.
const SX = x => x, SY = y => 1100 - y;
function renderMap(el, opts = {}) {
  const o = Object.assign({
    layers: { wards: true, roads: true, requests: true, assets: false },
    band: null, bandInclusive: true,
    hit: null,          // array of request IDs to colour as "qualifying"
    dim: null,          // array of IDs to grey out
    selected: null,
    onSelect: null,
    labels: true,
    extent: null,       // "wards" | "rect" | null
    axes: true,
    caption: null,
    extra: null         // function(svgNS, g) to draw extra shapes
  }, opts);
  const NS = "http://www.w3.org/2000/svg";
  const svg = document.createElementNS(NS, "svg");
  svg.setAttribute("viewBox", "-140 40 2560 1190");
  svg.setAttribute("role", "img");
  svg.setAttribute("aria-label", "Schematic map of the synthetic training grid: Ward A and Ward B side by side, Main Road R1 across the middle, six request points P1 to P6.");
  const mk = (tag, attrs, parent = svg) => { const n = document.createElementNS(NS, tag); for (const k in attrs) n.setAttribute(k, attrs[k]); parent.appendChild(n); return n; };

  // axes
  if (o.axes) {
    mk("line", { x1: SX(0), y1: SY(-40), x2: SX(2300), y2: SY(-40), class: "axis" });
    mk("line", { x1: SX(-40), y1: SY(0), x2: SX(-40), y2: SY(1050), class: "axis" });
    for (let x = 0; x <= 2000; x += 500) { mk("line", { x1: SX(x), y1: SY(-40), x2: SX(x), y2: SY(-60), class: "axis" }); const t = mk("text", { x: SX(x), y: SY(-95), class: "axis-label", "text-anchor": "middle" }); t.textContent = x; }
    for (let y = 0; y <= 1000; y += 500) { mk("line", { x1: SX(-40), y1: SY(y), x2: SX(-60), y2: SY(y), class: "axis" }); const t = mk("text", { x: SX(-75), y: SY(y) + 10, class: "axis-label", "text-anchor": "end" }); t.textContent = y; }
    const tx = mk("text", { x: SX(2320), y: SY(-52), class: "axis-label" }); tx.textContent = "x (m)";
    const ty = mk("text", { x: SX(-120), y: SY(1080), class: "axis-label" }); ty.textContent = "y (m)";
  }
  // wards
  if (o.layers.wards) {
    FIXTURE.wards.forEach(w => {
      mk("polygon", { points: w.pts.map(p => `${SX(p[0])},${SY(p[1])}`).join(" "), class: "ward" });
      const cx = (w.pts[0][0] + w.pts[2][0]) / 2, cy = (w.pts[0][1] + w.pts[2][1]) / 2;
      const t = mk("text", { x: SX(cx), y: SY(cy + 300), class: "ward-label", "text-anchor": "middle" }); t.textContent = w.name;
    });
  }
  // band along the finite road
  if (o.band != null && o.band > 0) {
    const r = o.band;
    const g = mk("g", {});
    mk("rect", { x: SX(0), y: SY(500 + r), width: 2000, height: 2 * r, class: "band" }, g);
    mk("circle", { cx: SX(0), cy: SY(500), r, class: "band" }, g);
    mk("circle", { cx: SX(2000), cy: SY(500), r, class: "band" }, g);
  }
  // extent
  if (o.extent === "wards") mk("rect", { x: SX(0), y: SY(1000), width: 2000, height: 1000, class: "extent" });
  if (o.extent === "rect") mk("rect", { x: SX(0), y: SY(1000), width: 2200, height: 1000, class: "extent" });
  // road
  if (o.layers.roads) {
    const r = FIXTURE.road;
    mk("line", { x1: SX(r.a[0]), y1: SY(r.a[1]), x2: SX(r.b[0]), y2: SY(r.b[1]), class: "road" });
    const t = mk("text", { x: SX(1500), y: SY(540), class: "road-label" }); t.textContent = "R1 Main Road";
    // end cap marker
    mk("line", { x1: SX(2000), y1: SY(470), x2: SX(2000), y2: SY(530), class: "road" });
  }
  if (o.extra) o.extra(NS, svg, mk);
  // assets
  if (o.layers.assets) {
    FIXTURE.assets.forEach(a => {
      mk("rect", { x: SX(a.x) - 16, y: SY(a.y) - 16, width: 32, height: 32, class: "asset", transform: `rotate(45 ${SX(a.x)} ${SY(a.y)})` });
      if (o.labels) { const t = mk("text", { x: SX(a.x) + 26, y: SY(a.y) + 46, class: "asset-label" }); t.textContent = a.id; }
    });
  }
  // requests
  if (o.layers.requests) {
    FIXTURE.requests.forEach(p => {
      let cls = "req";
      if (o.dim && o.dim.includes(p.id)) cls += " dim";
      if (o.hit && o.hit.includes(p.id)) cls += " hit";
      if (o.selected === p.id) cls += " sel";
      const c = mk("circle", { cx: SX(p.x), cy: SY(p.y), r: 26, class: cls, "data-id": p.id });
      if (o.onSelect) c.addEventListener("click", () => o.onSelect(p.id));
      if (o.labels) { const t = mk("text", { x: SX(p.x) + 34, y: SY(p.y) - 24, class: "req-label" }); t.textContent = p.id; }
    });
  }
  el.innerHTML = "";
  el.appendChild(svg);
  if (o.caption) { const c = document.createElement("figcaption"); c.textContent = o.caption; el.appendChild(c); }
  return svg;
}

/* Navigation, progress, quizzes, sorters and tabs live in /assets/common.js (shared by every chapter). */

/* ---- request table helper ---- */
function requestTable(el, opts = {}) {
  const cols = opts.cols || ["id", "xy", "category", "priority", "status", "reported", "closed", "channel"];
  const head = { id: "ID", xy: "(x, y) m", category: "Category", priority: "Priority", status: "Status", reported: "Reported", closed: "Closed", channel: "Channel", dist: "Distance to R1", note: "Note" };
  const rows = FIXTURE.requests.map(p => {
    const cells = cols.map(c => {
      if (c === "xy") return `<td class="mono">(${p.x}, ${p.y})</td>`;
      if (c === "dist") return `<td class="mono">${fmt(distToRoad(p.x, p.y))} m</td>`;
      if (c === "id") return `<td class="mono">${p.id}</td>`;
      return `<td>${p[c] || "—"}</td>`;
    }).join("");
    return `<tr data-id="${p.id}">${cells}</tr>`;
  }).join("");
  el.innerHTML = `<div class="table-wrap"><table><thead><tr>${cols.map(c => `<th>${head[c]}</th>`).join("")}</tr></thead><tbody>${rows}</tbody></table></div>`;
}
