/* Chapter 2 tutorial — shared data, diagram renderers, navigation, quizzes.
   Every product name is real; every deployment, item, user and file path is MADE UP. */

/* ---- the five responsibilities (the teaching model of the chapter) ---- */
const RESP = [
  { id: "R1", key: "r1", title: "Desktop authoring", q: "Where do skilled people create, edit, check and style data?", esri: "ArcGIS Pro", oss: "QGIS Desktop", icon: "✎" },
  { id: "R2", key: "r2", title: "Data storage", q: "Where is the official (authoritative) copy kept?", esri: "Geodatabase · hosted layer", oss: "PostgreSQL + PostGIS · files", icon: "▤" },
  { id: "R3", key: "r3", title: "Service delivery", q: "How do other machines get the data over the network?", esri: "ArcGIS Online services · ArcGIS Server", oss: "GeoServer (WMS / WFS…)", icon: "⇄" },
  { id: "R4", key: "r4", title: "Content organisation", q: "How are maps, layers and apps catalogued, shared and administered?", esri: "ArcGIS Online org · Portal for ArcGIS", oss: "(no direct equivalent)", icon: "☷" },
  { id: "R5", key: "r5", title: "User applications", q: "How does an inspector, manager or citizen actually use it?", esri: "Instant Apps · Dashboards · Field Maps · JS SDK", oss: "OpenLayers / MapLibre pages", icon: "☺" }
];

/* ---- Solution S-2 evidence (synthetic) ---- */
const S2 = {
  items: [
    { id: "gdb", kind: "File geodatabase (archived)", title: "Municipal_Training.gdb", data: true, x: 40, y: 330, w: 300, h: 110, note: "The file the author edited. Archived on 2026-09-15 — must not be edited any more.", stores: "A COPY of all four datasets (Wards, Roads, Requests, Assets) as they were on 2026-09-15.", refs: "Nothing.", del: "The archived copy is gone. The hosted layers are NOT affected — they were copied when published." },
    { id: "aprx", kind: "ArcGIS Pro project", title: "RequestTracker.aprx", x: 40, y: 150, w: 300, h: 110, note: "The author's saved workspace. Points at the archived file, not the hosted layer.", stores: "Maps, layer list, symbols, and the PATH to each dataset. No data.", refs: "The file geodatabase (by path).", del: "Only the author's workspace is gone. No data changes anywhere." },
    { id: "hfl", kind: "Hosted feature layer", title: "Requests_Training", data: true, x: 420, y: 150, w: 320, h: 120, note: "AUTHORITATIVE request records. Editing enabled. Shared with the group.", stores: "THE DATA — six request records, stored in ArcGIS Online.", refs: "Nothing (it is the source).", del: "The data is deleted. The view, both web maps, the dashboard, the Instant App and the Field Maps form all break." },
    { id: "view", kind: "Hosted feature layer VIEW", title: "Requests_Training_public", view: true, x: 420, y: 330, w: 320, h: 120, note: "Same data, seen through a filter: Note and Channel hidden, read-only, public.", stores: "Only its own settings (sharing, editing off, hidden fields). NO copy of the data.", refs: "Requests_Training — it reads the same records.", del: "No data is lost. The public web map and Instant App lose their layer." },
    { id: "base", kind: "Hosted feature layer (3 layers)", title: "Municipal_Base_Training", data: true, x: 420, y: 500, w: 320, h: 120, note: "Wards, Roads and Assets. Editing settings NOT captured.", stores: "THE DATA for wards, roads and assets.", refs: "Nothing.", del: "Those datasets are deleted; the team web map loses three layers." },
    { id: "wm1", kind: "Web map", title: "Request Overview (Training)", x: 820, y: 150, w: 320, h: 120, note: "Team map. Requests styled by Status (Open = red…). Shared with the group.", stores: "Layer list, symbols, pop-up settings. NO data.", refs: "Requests_Training and Municipal_Base_Training.", del: "The dashboard and the Field Maps form lose their map. No data changes." },
    { id: "wm2", kind: "Web map", title: "Request Overview (Public)", x: 820, y: 330, w: 320, h: 120, note: "Public map. Single symbol. Shared with everyone.", stores: "Layer list, one symbol, pop-up settings. NO data.", refs: "Requests_Training_public (the view).", del: "The Instant App loses its map. No data changes." },
    { id: "dash", kind: "Dashboard", title: "Request Monitoring (Training)", x: 1220, y: 60, w: 320, h: 110, note: "Indicator 'Open requests', list, map. Shared with the organisation.", stores: "Its own layout and the count rule (Status in Open / In progress / Reopened). NO data.", refs: "Request Overview (Training).", del: "Managers lose their screen. No data changes." },
    { id: "fm", kind: "Field Maps form", title: "on Request Overview (Training)", x: 1220, y: 220, w: 320, h: 110, note: "Form: Status (choice list), Note. Offline enabled.", stores: "The form design and offline settings. NO data.", refs: "Request Overview (Training), and through it Requests_Training.", del: "Crews lose the phone form. No data changes." },
    { id: "app", kind: "Instant App", title: "Request Locator (Training)", x: 1220, y: 380, w: 320, h: 110, note: "Public viewer: search, legend, zoom.", stores: "Which template and which tools are switched on. NO data.", refs: "Request Overview (Public).", del: "The public page is gone. No data changes." }
  ],
  refs: [
    ["aprx", "gdb", "references (path)", true],
    ["view", "hfl", "reads the same data"],
    ["wm1", "hfl", "references"],
    ["wm1", "base", "references"],
    ["wm2", "view", "references"],
    ["dash", "wm1", "references"],
    ["fm", "wm1", "references"],
    ["app", "wm2", "references"],
    ["gdb", "hfl", "copied on 2026-09-15", false, true]
  ]
};

/* ---- shared SVG helpers ---- */
const NS = "http://www.w3.org/2000/svg";
function svgEl(tag, attrs, parent) { const n = document.createElementNS(NS, tag); for (const k in attrs) n.setAttribute(k, attrs[k]); if (parent) parent.appendChild(n); return n; }
function arrowDefs(svg) {
  const d = svgEl("defs", {}, svg);
  const m = svgEl("marker", { id: "arr", viewBox: "0 0 10 10", refX: "9", refY: "5", markerWidth: "8", markerHeight: "8", orient: "auto-start-reverse" }, d);
  svgEl("path", { d: "M 0 0 L 10 5 L 0 10 z", fill: "currentColor" }, m);
  svg.style.color = "#1f2a44";
}
function wrapText(t, text, width, lineH) {
  // crude word-wrap for SVG text
  const words = text.split(" "); let line = "", lines = [];
  const approx = w => w.length * (parseFloat(t.getAttribute("font-size") || 22) * 0.52);
  words.forEach(w => { const test = line ? line + " " + w : w; if (approx(test) > width && line) { lines.push(line); line = w; } else line = test; });
  if (line) lines.push(line);
  const x = t.getAttribute("x"), y = parseFloat(t.getAttribute("y"));
  t.textContent = "";
  lines.forEach((l, i) => { const ts = svgEl("tspan", { x, y: y + i * lineH }, t); ts.textContent = l; });
  return lines.length;
}

/* ---- responsibility diagram (Figure 2.1 / 2.6) ----
   opts.mode: "generic" | "esri" | "oss"; opts.lit: array of ids to highlight; opts.onClick(id) */
function renderResp(el, opts = {}) {
  const o = Object.assign({ mode: "generic", lit: [], onClick: null, caption: null, showArrows: true }, opts);
  const svg = svgEl("svg", { viewBox: "0 0 1600 720", role: "img", "aria-label": "Five boxes: desktop authoring, data storage, service delivery on the top row; content organisation and user applications on the bottom row; arrows lead from one to the next." });
  arrowDefs(svg);
  const pos = { R1: [40, 60], R2: [580, 60], R3: [1120, 60], R4: [310, 400], R5: [850, 400] };
  const W = 440, H = 220;
  RESP.forEach(r => {
    const [x, y] = pos[r.id];
    const g = svgEl("g", { class: o.onClick ? "clickable" : "" }, svg);
    const box = svgEl("rect", { x, y, width: W, height: H, class: `rbox ${r.key} ${o.lit.includes(r.id) ? "lit" : ""} ${o.lit.length && !o.lit.includes(r.id) && o.dimOthers ? "off" : ""}` }, g);
    const id = svgEl("text", { x: x + 22, y: y + 36, class: "rid" }, g); id.textContent = r.id;
    const ic = svgEl("text", { x: x + W - 50, y: y + 44, class: "rtitle", "font-size": "34" }, g); ic.textContent = r.icon;
    const t = svgEl("text", { x: x + 22, y: y + 78, class: "rtitle" }, g); t.textContent = r.title;
    const s = svgEl("text", { x: x + 22, y: y + 114, class: "rsub", "font-size": "22" }, g); wrapText(s, r.q, W - 44, 26);
    if (o.mode !== "generic") { const p = svgEl("text", { x: x + 22, y: y + 190, class: "rprod", "font-size": "22" }, g); p.textContent = o.mode === "esri" ? r.esri : r.oss; if (o.mode === "oss" && r.id === "R4") svgEl("rect", { x: x + 8, y: y + 8, width: W - 16, height: H - 16, class: "gap" }, g); }
    if (o.onClick) g.addEventListener("click", () => o.onClick(r.id));
  });
  if (o.showArrows) {
    const lit = a => (o.litArrows || []).includes(a) ? " lit" : "";
    const arrow = (d, key, label, lx, ly, dash) => { svgEl("path", { d, class: "rarrow" + (dash ? " dash" : "") + lit(key) }, svg); if (label) { const t = svgEl("text", { x: lx, y: ly, class: "rlabel" }, svg); t.textContent = label; } };
    arrow("M 480 170 L 572 170", "12", "edits", 492, 158);
    arrow("M 1020 170 L 1112 170", "23", "served from", 1000, 158);
    arrow("M 1160 280 L 1160 345 L 700 345 L 700 392", "34", "registered as an item", 720, 337);
    arrow("M 750 510 L 842 510", "45", "used by", 758, 498);
    arrow("M 1200 400 L 1200 330 L 1400 330 L 1400 290", "53", "asks for data at run time", 1210, 322, true);
  }
  el.innerHTML = ""; el.appendChild(svg);
  if (o.caption) { const c = document.createElement("figcaption"); c.textContent = o.caption; el.appendChild(c); }
  return svg;
}

/* ---- item reference diagram (Figure 2.3 / lab sketch) ----
   opts.onSelect(itemId); opts.gone: array of deleted ids; opts.selected; opts.showCopy */
function renderItems(el, opts = {}) {
  const o = Object.assign({ onSelect: null, gone: [], selected: null, caption: null, showCopy: true, showAprx: true, labels: true }, opts);
  const svg = svgEl("svg", { viewBox: "0 0 1580 660", role: "img", "aria-label": "Boxes for a project file, an archived file geodatabase, hosted layers, a view, two web maps and three apps, with arrows showing which item references which." });
  arrowDefs(svg);
  const byId = Object.fromEntries(S2.items.map(i => [i.id, i]));
  const isGone = id => o.gone.includes(id);
  // an item is broken if anything it references (transitively) is gone
  const brokenSet = new Set();
  function broken(id, seen = new Set()) { if (seen.has(id)) return false; seen.add(id); if (isGone(id)) return true; return S2.refs.some(r => r[0] === id && !r[4] && broken(r[1], seen)); }
  S2.items.forEach(i => { if (!isGone(i.id) && broken(i.id)) brokenSet.add(i.id); });
  // arrows first (under boxes)
  S2.refs.forEach(([from, to, label, dash, copy]) => {
    if (copy && !o.showCopy) return;
    if ((from === "aprx" || to === "aprx") && !o.showAprx) return;
    const a = byId[from], b = byId[to];
    let x1, y1, x2, y2;
    if (copy) { x1 = a.x + a.w; y1 = a.y + 40; x2 = b.x; y2 = b.y + b.h - 20; }
    else if (Math.abs(a.x - b.x) < 10) { // vertical
      if (a.y > b.y) { x1 = a.x + a.w / 2; y1 = a.y; x2 = b.x + b.w / 2; y2 = b.y + b.h; } else { x1 = a.x + a.w / 2; y1 = a.y + a.h; x2 = b.x + b.w / 2; y2 = b.y; }
    } else { x1 = a.x; y1 = a.y + a.h / 2; x2 = b.x + b.w; y2 = b.y + b.h / 2; }
    const cls = "ref" + (dash || copy ? " broken" : "") + ((isGone(to) || isGone(from)) ? " broken" : "");
    const mid = [(x1 + x2) / 2, (y1 + y2) / 2];
    const d = copy ? `M ${x1} ${y1} C ${x1 + 60} ${y1}, ${x2 - 60} ${y2}, ${x2} ${y2}` : `M ${x1} ${y1} L ${x2} ${y2}`;
    const p = svgEl("path", { d, class: cls, style: copy ? "stroke:#d3541f;stroke-dasharray:none;stroke-width:4" : "" }, svg);
    if (isGone(from) || isGone(to)) p.style.opacity = .25;
    if (o.labels) { const t = svgEl("text", { x: mid[0], y: mid[1] - 8, class: "itag", "text-anchor": "middle", style: copy ? "fill:#d3541f;font-weight:600" : "" }, svg); t.textContent = label; if (copy) { t.setAttribute("y", mid[1] + 60); t.setAttribute("x", mid[0] - 10); } }
  });
  S2.items.forEach(i => {
    if (i.id === "aprx" && !o.showAprx) return;
    if (i.id === "gdb" && !o.showCopy) return;
    const g = svgEl("g", {}, svg);
    let cls = "item" + (i.data ? " data" : "") + (i.view ? " view" : "") + (o.selected === i.id ? " sel" : "") + (isGone(i.id) ? " gone" : "") + (brokenSet.has(i.id) ? " broken" : "");
    svgEl("rect", { x: i.x, y: i.y, width: i.w, height: i.h, class: cls }, g);
    const k = svgEl("text", { x: i.x + 16, y: i.y + 28, class: "itag" }, g); k.textContent = i.kind;
    const t = svgEl("text", { x: i.x + 16, y: i.y + 62, class: "ititle" }, g); t.textContent = i.title;
    const s = svgEl("text", { x: i.x + 16, y: i.y + 92, class: "isub" }, g); s.textContent = i.data ? "holds DATA" : (i.view ? "no copy — reads the data" : "configuration only");
    if (brokenSet.has(i.id)) { const b = svgEl("text", { x: i.x + i.w - 90, y: i.y + 28, class: "itag", style: "fill:#b9432e;font-weight:700" }, g); b.textContent = "BROKEN"; }
    if (isGone(i.id)) { const b = svgEl("text", { x: i.x + i.w - 100, y: i.y + 28, class: "itag", style: "fill:#b9432e;font-weight:700" }, g); b.textContent = "DELETED"; }
    if (o.onSelect) { g.classList.add("clickable"); g.addEventListener("click", () => o.onSelect(i.id)); }
  });
  el.innerHTML = ""; el.appendChild(svg);
  if (o.caption) { const c = document.createElement("figcaption"); c.textContent = o.caption; el.appendChild(c); }
  return svg;
}

/* ---- navigation, progress ---- */
const MODULES = [
  { id: "2.1", file: "m1-responsibilities.html", title: "Five responsibilities" },
  { id: "2.2", file: "m2-pro.html", title: "ArcGIS Pro" },
  { id: "2.3", file: "m3-online.html", title: "ArcGIS Online" },
  { id: "2.4", file: "m4-enterprise.html", title: "ArcGIS Enterprise" },
  { id: "2.5", file: "m5-apps.html", title: "Apps, APIs, SDKs" },
  { id: "2.6", file: "m6-platforms.html", title: "Other platforms" },
  { id: "2.7", file: "m7-lab.html", title: "Lab: trace a solution" },
  { id: "2.8", file: "m8-check.html", title: "Independent check" },
  { id: "2.9", file: "m9-media.html", title: "Recap & next" }
];
const PROG_KEY = "gis-ch2-progress";
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
    <a class="brand" href="index.html">GIS Phase 1 · <span>Chapter 2</span></a>
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

/* ---- quick-check quizzes (same markup as Chapter 1) ---- */
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

/* ---- click-to-sort activity (same markup as Chapter 1) ---- */
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
