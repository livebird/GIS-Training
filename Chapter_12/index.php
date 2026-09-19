<?php $page = ['title' => 'Chapter 12 — Cartography and interpreting maps correctly', 'chapter' => 12]; require __DIR__ . '/../partials/head.php'; ?>
  <section class="hero">
    <div class="fade-up">
      <div class="page-head" style="padding:0">
        <div class="eyebrow">GIS Phase 1 · Chapter 12 · Interactive tutorial · Last chapter of Phase 1</div>
      </div>
      <h1 class="big">Same data.<br>Honest map, or <em>misleading</em> map?</h1>
      <p class="lead">Chapters 3–11 taught you to understand, clean, question and analyse map data. This chapter is about the last step — the one everybody sees first: <strong>showing the result on a map so that the reader makes the right decision</strong>. The same numbers can be drawn in a way that is honest, or in a way that quietly misleads. You will learn to tell the difference, and to make the honest one.</p>
      <p><a class="btn primary" href="m1-purpose">Begin module 12.1 →</a> &nbsp; <a class="btn ghost" href="glossary">Glossary</a></p>
      <p class="small">No software is needed for modules 12.1–12.7; every demo runs in this page. The lab in 12.8 uses ArcGIS Pro or QGIS. All wards, requests, households and names here are <span class="synthetic">made-up practice data</span> — no real town, no real resident, no real risk. Your progress is saved in this browser only.</p>
    </div>
    <figure class="map-fig fade-up" id="heroFig"></figure>
  </section>

  <h2 style="border-top:0;margin-top:1rem"><span class="mod">The big idea</span>A map is a user interface for a result</h2>
  <div class="grid-2">
    <div class="card">
      <p>Think of the map as a <strong>screen in an app</strong>. The data behind it is fixed; the screen decides what the user understands. A badly designed screen makes a user click the wrong button. A badly designed map makes a ward officer send the repair crew to the wrong ward — while feeling completely sure.</p>
      <p>Everything in this chapter follows from that:</p>
      <ol>
        <li>Start with the <em>reader</em> and the <em>decision</em>, not with colours (<strong>12.1</strong>).</li>
        <li>Scale, pixel size, decimal places and accuracy are four different things; zooming in adds no truth (<strong>12.2</strong>).</li>
        <li>Match the symbol to the kind of data: names get shapes, amounts get a colour ramp (<strong>12.3</strong>).</li>
        <li>Putting numbers into “low / medium / high” groups is a <em>choice</em> — and the choice changes the picture (<strong>12.4</strong>).</li>
        <li>A count is not a comparison. “Requests per 1,000 households” needs a denominator (<strong>12.5</strong>).</li>
        <li>Title, legend, units, date, notes; zero is not “no data” (<strong>12.6</strong>).</li>
        <li>Hiding a field on the map is <em>not</em> data protection (<strong>12.7</strong>). Then a lab (<strong>12.8</strong>) and the Phase 1 exit check (<strong>12.9</strong>).</li>
      </ol>
    </div>
    <div class="card">
      <h3 style="margin-top:0">Before you start</h3>
      <p>You should be comfortable with these ideas from earlier chapters. If any feels new, revise it first.</p>
      <ul>
        <li>A <strong>layer</strong> is how a dataset is <em>drawn</em>; changing colours changes nothing in the data (Chapter 3).</li>
        <li>Raster <strong>cell size</strong> is not accuracy (Chapter 4); decimal places are not accuracy (Chapter 5); accuracy comes from how the data was captured (Chapter 9).</li>
        <li><code>NULL</code> (missing) is not the same as <code>0</code> (Chapter 10).</li>
        <li>A <strong>count per ward</strong> comes from a spatial join, with a written rule for requests on a ward edge — <strong>Policy W-1</strong> (Chapters 10–11).</li>
      </ul>
      <div class="callout dev" style="margin-bottom:0"><span class="label">Developer view</span><p>You already know that a dashboard and a work queue are built from the same tickets table but must look completely different. A map for the manager and a map for the field crew are the same thing. And you know that hiding a column with CSS does not stop the API from returning it — that is module 12.7 in one sentence.</p></div>
    </div>
  </div>

  <h2><span class="mod">This chapter’s practice data</span>District G-12 — sixteen wards, one summary table</h2>
  <p>Earlier chapters used two wards. Grouping numbers into classes (12.4) needs more than two, so this chapter adds <strong>District G-12</strong>: a 4 × 4 grid of sixteen wards, each exactly 1 km × 1 km, on the flat practice grid (metres, no coordinate system, <em>not a real place</em>). Every ward is the same size on purpose — so that in this chapter, <em>area</em> never explains a difference; people and roads do.</p>
  <div class="grid-2">
    <div class="table-wrap"><table id="g12Table">
      <thead><tr><th>Ward</th><th>Open requests</th><th>Households</th><th>Road (km)</th><th>Per 1,000 households</th><th>Per km of road</th></tr></thead>
      <tbody></tbody></table>
      <p class="small">Counted on 2026-09-15 under Policy W-1. <strong>W03 = 0</strong> means “checked, nothing open”. <strong>W16 = no data</strong> means its export never arrived. Those two are not the same, and module 12.6 makes sure the map says so.</p>
    </div>
    <div>
      <figure class="map-fig" id="dataFig"></figure>
      <p class="small">A second, smaller set — the 16 request points in wards W06 and W07, with Station Road — is used for the crew map. You meet it in 12.1.</p>
    </div>
  </div>

  <h2><span class="mod">Modules</span>Work through them in order</h2>
  <div class="modcards" id="modcards"></div>

  <div class="callout note"><span class="label">What this tutorial is based on</span><p>These pages follow the Chapter 12 training document (<code>GIS_Phase_1_Chapter_12_Cartography_and_Interpreting_Maps_Correctly.md</code>). Every number shown here is calculated live in your browser from the practice tables and matches the document. The software steps in 12.8 are written from the official ArcGIS Pro, ArcGIS Online and QGIS 3.44 documentation and have <strong>not</strong> been run by the author — the lab page says what your instructor must check first.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const f = (v, d) => v == null ? "<span class=\"synthetic\">no data</span>" : fmtN(v, d);
  document.querySelector("#g12Table tbody").innerHTML = G12.wards.map(w => `<tr><td class="mono">${w.id}</td><td class="mono">${f(w.open)}</td><td class="mono">${fmtN(w.hh)}</td><td class="mono">${w.road}</td><td class="mono">${f(w.rate, 1)}</td><td class="mono">${f(w.perkm, 2)}</td></tr>`).join("")
    + `<tr><td><strong>Total</strong></td><td class="mono"><strong>238</strong> over 15 wards</td><td class="mono">55,100</td><td class="mono">118</td><td></td><td></td></tr>`;
  renderG12(document.getElementById("dataFig"), { style: w => ({ fill: "#fff", label: w.open == null ? "?" : w.open, hatch: w.open == null }), caption: "District G-12 with each ward’s open-request count. W16 is hatched: no data. Made-up practice data." });
  const vals = G12.wards.filter(w => w.open != null).map(w => w.open), br = jenks(vals, 3);
  renderG12(document.getElementById("heroFig"), { style: w => w.open == null ? { hatch: true } : (c => ({ fill: RAMP[3][c], dark: c === 2 }))(classOf(w.open, br)), title: "Open requests per ward (natural breaks, 3 classes)", caption: "One honest way to show the table: three disclosed classes, and W16 shown as “no data”, not as zero." });
  const prog = getProgress();
  const blurbs = {
    "12.1": "Who reads this map and what must they decide? A crew map and a manager’s map from the same data — and what to leave out.",
    "12.2": "1:1,000 vs 1:100,000 — which shows more? Why zooming in and adding decimals never make a point more accurate.",
    "12.3": "Names get shapes, order gets shades, amounts get a ramp. Big symbols hide places. Check the map in grey.",
    "12.4": "Equal interval, quantile, natural breaks: the same 15 numbers, three different pictures. Always print the class limits.",
    "12.5": "100 requests in a big ward or 60 in a small one — which is worse? Totals, rates, densities and the right denominator.",
    "12.6": "Title, legend, units, date, method note. Zero is a value; ‘no data’ is not. A ward average is not every street.",
    "12.7": "Hiding a field in the pop-up is presentation. Protection is sharing and views — a later chapter. Never put personal notes on a map.",
    "12.8": "Lab: diagnose the deliberately bad ‘Map B-12’, then build a crew map and a manager’s map from the same table.",
    "12.9": "Six concept questions, two scenarios, a map critique, the Phase 1 exit practical and a spoken explanation.",
    "12.10": "The recap, the media brief, and what Phase 2 does with all of this."
  };
  document.getElementById("modcards").innerHTML = MODULES.map(m => `<a class="modcard ${prog[m.id] ? "done" : ""}" href="${m.file}"><span class="done-badge">done</span><div class="id">MODULE ${m.id}</div><h3>${m.title}</h3><p>${blurbs[m.id]}</p></a>`).join("");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
