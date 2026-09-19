<?php $page = ['title' => '7.6 Evaluate sources and metadata', 'chapter' => 7, 'module' => '7.6']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 7.6 · General practice, with ArcGIS Pro / QGIS / GeoPackage places to store it</div>
    <h1>Every dataset comes with a story. Write it down first.</h1>
    <p class="lead">Who made it, when it was true, how it was made, how accurate it is, and what you are allowed to do with it. Formats cannot hold most of that (7.1–7.4), so it lives in an <strong>intake form</strong> that travels with the data — and it is what saves you when the download link is dead six months later.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Fill the 14-field intake form and refuse to leave a cell blank.</li>
      <li>Classify data as <strong>measured, digitized, geocoded, derived or simulated</strong> and know what evidence each needs.</li>
      <li>Watch an “official” but old ward boundary silently move two requests — and see which form field would have caught it.</li></ul></div>
  </div>

  <h2><span class="mod">7.6.1</span>The intake form</h2>
  <p>Fill it for Table F7 (the answers are in the chapter; this is practice). Your entries are saved in this browser. The rule: if you cannot fill a field, write <em>“unknown”</em> and the question you sent the publisher. <strong>An empty cell is the one unacceptable value</strong> — the form highlights it.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Intake form — assets_ch7.csv</h3>
    <div class="intake" id="intake"></div>
    <div class="result" id="intakeStatus"></div>
    <p class="small" style="margin-top:.6rem"><button class="btn small" id="fillDemo">Show the model answers</button> &nbsp; <button class="btn small ghost" id="clearForm">Clear</button></p>
  </div>
  <p><strong>Where products keep it.</strong> ArcGIS Pro stores metadata with the item (“in the geodatabase for geodatabase items … on the file system for file-based items”) and shows a one-page <em>item description</em> by default, which also appears on the item page when published to ArcGIS Online or Enterprise; other metadata styles show more; only ArcGIS-format metadata can be edited there (ISO and FGDC records can be imported). QGIS’s layer <em>Metadata</em> tab covers identification, keywords, access (licences, rights, constraints), extent, contacts, links and history, saved to a <code>.qmd</code> file beside the data or inside a GeoPackage’s metadata tables. The GeoPackage standard itself has metadata tables. Use whatever the container supports — <em>and</em> keep the intake form as plain text in the package, because the readme survives every conversion.</p>

  <h2><span class="mod">7.6.2</span>Five kinds of data — how did it come to exist?</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Class</th><th>How it came to exist</th><th>Evidence to demand</th><th>Town example</th></tr></thead>
    <tbody>
      <tr><td><strong>Measured</strong></td><td>Observed with an instrument at the place (survey, GNSS receiver, sensor)</td><td>Instrument specification, survey report</td><td>Streetlight positions captured with a GNSS receiver during an inventory</td></tr>
      <tr><td><strong>Digitized</strong></td><td>Traced by a person from a map, image or plan</td><td>Source scale / image resolution; tracing tolerance</td><td>Ward boundaries traced from a scanned notification map</td></tr>
      <tr><td><strong>Geocoded</strong></td><td>A position computed from text (an address) by a matching process</td><td>Match score, locator version, reference data date</td><td>Requests placed from the address a caller gave</td></tr>
      <tr><td><strong>Derived</strong></td><td>Computed from other datasets by an operation</td><td>The <em>processing history</em>: inputs, operation, parameters, software, date, person</td><td>“Requests within 300 m of R1” — Chapter 1’s list</td></tr>
      <tr><td><strong>Simulated</strong></td><td>Invented for a purpose (training, testing)</td><td>None claimed; the purpose statement is the only evidence</td><td>Every fixture in this course</td></tr>
    </tbody></table></div>
  <p>Why it matters: a <em>geocoded</em> request point on a footpath does not mean the caller stood there — it means the address matched a spot along the street; a <em>digitized</em> boundary is only as fine as the source map’s line width; a <em>derived</em> result inherits the weakest input <em>and</em> the choices of the operation (flat or geodesic, boundary in or out — Chapters 6 and 10). Mixing classes in one layer without a field that records the class is a common, invisible defect.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Which kind is it?</h3>
    <div class="sorter" data-items='[
      {"t":"Drain positions traced from a 1:5,000 plan","bin":"Digitized","why":"ask for the plan scale and date"},
      {"t":"Request points placed from callers’ addresses","bin":"Geocoded","why":"ask for match scores and the locator version"},
      {"t":"Table F7","bin":"Simulated","why":"the purpose statement is the only evidence"},
      {"t":"A “priority zone” made by buffering R1 by 300 m","bin":"Derived","why":"needs the processing history: input, 300 m, planar, software, date"},
      {"t":"Streetlight positions from a GNSS receiver survey","bin":"Measured","why":"ask for the receiver specification and survey report"},
      {"t":"Your Chapter 6 lab outputs in UTM","bin":"Derived","why":"the 6.8 log IS the processing history"}
    ]'>
      <div class="items"></div>
      <div class="bins">
        <div class="bin" data-bin="Measured"><h5>Measured</h5></div>
        <div class="bin" data-bin="Digitized"><h5>Digitized</h5></div>
        <div class="bin" data-bin="Geocoded"><h5>Geocoded</h5></div>
        <div class="bin" data-bin="Derived"><h5>Derived</h5></div>
        <div class="bin" data-bin="Simulated"><h5>Simulated</h5></div>
      </div>
    </div>
  </div>
  <div class="callout idea"><span class="label">Processing history — the test is reproducibility</span><p>A derived dataset must carry: the inputs (with their own intake references), the operation and every parameter (“distance ≤ 300 m, boundary inclusive, planar on the training grid”), the software and version, the date, the person. Another person following it from the same inputs must get the same output. Chapter 1’s answer {P1, P3, P5} was reproducible only because the rules were written down; your Chapter 6 lab log — input CRSs, transformation record, analysis CRS, method, units, rounding, software — is a complete processing history. Keep it with the outputs it describes.</p></div>

  <h2><span class="mod">7.6.3</span>“Official” is not “current”: the old boundary</h2>
  <p>A colleague finds <code>wards_official.shp</code> on the shared drive and counts this month’s requests per ward with it. Its metadata — once someone opens it — says <strong>2019 delimitation</strong>: back then Ward A ran to x = 1300. In 2024 the line moved to x = 1000 (the Chapter 1 fixture). Toggle the file and watch two requests change ward without anyone deciding so.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Which boundary file is loaded?</h3>
    <div class="opbtns"><button class="btn small" id="bOld">wards_official.shp (2019, line at x = 1300)</button><button class="btn small" id="bNew" aria-pressed="true">wards_2024.gpkg (current, line at x = 1000)</button></div>
    <div class="grid-2">
      <figure class="map-fig" id="bFig"></figure>
      <div>
        <div class="tiles" id="bTiles"></div>
        <div class="table-wrap"><table id="bTable"></table></div>
        <div class="status-line q" id="bNote"></div>
      </div>
    </div>
  </div>
  <p>Both counts are arithmetically correct <em>for their inputs</em>. Only one answers “how many current requests fall in each current ward”. The report built on the old file gives Ward A two extra requests and hands P5 to A without the boundary-policy decision Chapter 1 discussed.</p>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">Which form field would have caught it</h4><ul><li><strong>4 — edition / date of observation:</strong> 2019, against a question about 2026.</li><li><strong>5 — coverage:</strong> an x-range for Ward A that does not match the current wards.</li><li><strong>10 — known omissions:</strong> “superseded by the 2024 delimitation”, if the publisher was careful.</li></ul></div>
    <div class="card"><h4 style="margin-top:0">Which field could not</h4><p><strong>1 — publisher / “official”.</strong> It names the issuer. It says nothing about <em>when</em> the data was true or <em>what</em> it is fit for. A dataset suits a question when its content date, coverage, class, accuracy and licence all fit the question. Authority is not on the list.</p></div>
  </div>
  <p class="small">The same mismatch appears as a road network from before a bypass opened used for routing, a 2015 land-cover raster used to explain 2026 flooding, or a geocoded request table whose locator used an older street reference. The question for any source: <em>as of when, and for what purpose, is this true?</em></p>

  <div class="quiz" data-answer="3" data-fb="(a) digitized — plan scale and date; (b) geocoded — match scores and locator version; (c) simulated — the purpose statement; (d) derived — the processing history (input R1, 300 m, planar, software, date, person).">
    <div class="q">Match the classes: (a) drains traced from a 1:5,000 plan; (b) requests placed from callers’ addresses; (c) Table F7; (d) a zone made by buffering R1 by 300 m.</div>
    <div class="opts">
      <button class="opt">(a) measured, (b) digitized, (c) derived, (d) simulated</button>
      <button class="opt">(a) digitized, (b) measured, (c) simulated, (d) geocoded</button>
      <button class="opt">(a) derived, (b) geocoded, (c) simulated, (d) digitized</button>
      <button class="opt">(a) digitized, (b) geocoded, (c) simulated, (d) derived</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const FIELDS = [
    ["1 Publisher", "pub", "LiveBird Technologies training team (made-up)"],
    ["2 Download / service URL", "url", "Printed in the chapter document (no URL)"],
    ["3 Retrieval date", "ret", "19 September 2026 — the day I obtained it, not the file date"],
    ["4 Edition / date of observation", "ed", "Fixture revision 1.0; last_inspection_at per record"],
    ["5 Geographic coverage", "cov", "Training grid; x 205–2190, y 195–950"],
    ["6 CRS", "crs", "None — local training grid, not Earth-referenced"],
    ["7 Units", "units", "Metres (coordinates); condition_score is a 0–5 score, no unit"],
    ["8 Accuracy statement", "acc", "None — synthetic; no positional accuracy claimed"],
    ["9 Licence / usage conditions", "lic", "Training use only"],
    ["10 Known omissions", "om", "TR-0301 never inspected; no assets recorded in the far east of Ward B"],
    ["11 Data class", "cls", "Simulated"],
    ["12 Processing history", "hist", "Not applicable (primary synthetic fixture)"],
    ["13 Encoding, delimiter, field types", "enc", "UTF-8; comma; per schema dictionary"],
    ["14 Checks performed on receipt", "chk", "Count 6; extent as above; nulls: score 1, timestamp 1, note 1 empty; spot-checked SL-0113, TR-0301, SL-0055"]
  ];
  const form = document.getElementById("intake");
  form.innerHTML = FIELDS.map(([l, k]) => `<label><b>${l}</b><input type="text" data-save="intake-${k}" placeholder="write ‘unknown + question sent’ rather than leaving it empty"></label>`).join("");
  const st = document.getElementById("intakeStatus");
  function check() { let empty = 0; form.querySelectorAll("input").forEach(i => { const e = !i.value.trim(); i.classList.toggle("empty", e); if (e) empty++; }); st.innerHTML = empty ? `<strong>${empty} field(s) empty.</strong> Empty is the one value that is not allowed — write “unknown” and what you asked.` : "<strong>Every field has a value.</strong> The form can now travel with the file."; }
  form.addEventListener("input", check); initNotes(); check();
  document.getElementById("fillDemo").addEventListener("click", () => { FIELDS.forEach(([, k, v]) => { const i = form.querySelector(`[data-save="intake-${k}"]`); i.value = v; i.dispatchEvent(new Event("input")); }); check(); });
  document.getElementById("clearForm").addEventListener("click", () => { form.querySelectorAll("input").forEach(i => { i.value = ""; i.dispatchEvent(new Event("input")); }); check(); });
  /* boundary story */
  function showB(split) {
    document.getElementById("bOld").setAttribute("aria-pressed", split === 1300); document.getElementById("bNew").setAttribute("aria-pressed", split === 1000);
    renderGrid(document.getElementById("bFig"), { requests: true, split, caption: split === 1300 ? "The 2019 file: Ward A extends to x = 1300 (dashed = today’s line). Made-up data." : "The current file: the A/B line is at x = 1000. Made-up data." });
    const w = REQUESTS.map(p => ({ id: p.id, w: wardOf(p, split), w1300: wardOf(p, 1300), w1000: wardOf(p, 1000) }));
    const cA = w.filter(x => x.w === "A").length, cB = w.filter(x => x.w === "B").length, cBd = w.filter(x => x.w === "boundary").length;
    document.getElementById("bTiles").innerHTML = `<div class="tile ${split === 1300 ? "warn" : ""}"><div class="v">${cA}</div><div class="l">Ward A</div></div><div class="tile"><div class="v">${cB}</div><div class="l">Ward B</div></div><div class="tile"><div class="v">${cBd}</div><div class="l">on the line</div></div><div class="tile"><div class="v">1</div><div class="l">outside (P6)</div></div>`;
    document.getElementById("bTable").innerHTML = `<thead><tr><th>Request</th><th>(x, y)</th><th>2019 line (x = 1300)</th><th>current line (x = 1000)</th></tr></thead><tbody>` + REQUESTS.map((p, i) => `<tr class="${w[i].w1300 !== w[i].w1000 ? "hl" : ""}"><td class="mono">${p.id}</td><td class="mono">(${p.x}, ${p.y})</td><td>${w[i].w1300}</td><td>${w[i].w1000}</td></tr>`).join("") + `</tbody>`;
    const n = document.getElementById("bNote"); n.className = "status-line " + (split === 1300 ? "bad" : "ok");
    n.textContent = split === 1300 ? "P3 (1200 < 1300) is counted in A, and P5 at x = 1000 is now strictly inside A. Ward A = 4, B = 1. Correct arithmetic, wrong question." : "P3 (1200 > 1000) is in B; P5 sits exactly on the line and needs the boundary policy. Ward A = 2, B = 2.";
  }
  document.getElementById("bOld").addEventListener("click", () => showB(1300)); document.getElementById("bNew").addEventListener("click", () => showB(1000)); showB(1000);
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
