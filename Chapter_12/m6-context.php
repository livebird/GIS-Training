<?php $page = ['title' => '12.6 Context and honest uncertainty', 'chapter' => 12, 'module' => '12.6']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 12.6 · General GIS idea · with ArcGIS Pro / ArcGIS Online / QGIS notes</div>
    <h1>Title, legend, units, date — and “no data” is not zero</h1>
    <p class="lead">A map without a title, legend, units and date is like a chart without axes. And two special cases must be visible on the map itself: a ward whose value is <strong>zero</strong>, and a ward whose value is <strong>missing</strong>. They must never look the same.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Put the right elements on a map — and leave out a north arrow or scale bar when it would mislead.</li>
      <li>Draw zero and missing differently, in the legend and on the map, and keep the unmatched and edge-case records in the notes.</li>
      <li>Write limitations on the map: aggregation, boundary policy, and “a ward average is not every street”.</li></ul></div>
  </div>

  <h2><span class="mod">12.6.1</span>The elements — because they help, not as decoration</h2>
  <p>The QGIS introduction lists the usual parts: “the title, map body, legend, north arrow, scale bar, acknowledgement, and map border”. The title is “usually the first thing a reader will look at”; the legend is “a dictionary that allows you to understand the meaning of what the map shows”; the acknowledgement says “how, by whom and when a map was created”. ArcGIS Pro’s layout offers the same objects — map frame, scale bar, north arrow, title, text, legend. That is the inventory. Use each one <em>because it helps this reader</em>.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Build the manager’s map card</h3>
    <p>Tick the elements. The preview shows what a reader would get — and flags what is missing or misleading.</p>
    <div class="grid-2">
      <div class="checklist" id="elems">
        <label><input type="checkbox" data-el="title" checked> Title with variable, unit, area and date</label>
        <label><input type="checkbox" data-el="legend" checked> Legend with numeric intervals and a “no data” entry</label>
        <label><input type="checkbox" data-el="units"> Units on every legend label</label>
        <label><input type="checkbox" data-el="source"> Source and date line</label>
        <label><input type="checkbox" data-el="method"> Method note (classes, Policy W-1, W16 missing, denominator)</label>
        <label><input type="checkbox" data-el="scale"> 1 km reference (scale bar)</label>
        <label><input type="checkbox" data-el="north"> North arrow</label>
        <label><input type="checkbox" data-el="shops"> Basemap with shop names</label>
      </div>
      <div class="mapcard" id="card"></div>
    </div>
    <div class="result" id="elemOut"></div>
  </div>
  <div class="table-wrap"><table>
    <thead><tr><th>Element</th><th>Manager’s map (print)</th><th>Crew map (phone)</th><th>When to leave it out</th></tr></thead>
    <tbody>
      <tr><td><strong>Title</strong></td><td>“Open service requests per 1,000 households, District G-12 wards, 2026-09-15 (made-up data)”</td><td>“Open requests — W06/W07 — crew sheet 2026-09-15”</td><td>Never</td></tr>
      <tr><td><strong>Legend</strong></td><td>Every class with its numbers and unit; the “no data” entry; zero visibly inside the lowest class</td><td>Category shapes; priority sizes with “1 = highest”; open/closed fill</td><td>Never</td></tr>
      <tr><td><strong>Source and date</strong></td><td>“Requests: tracker export 2026-09-15. Households: 2024 register. Boundaries: 2024 wards.”</td><td>Same, shorter, in the layer description</td><td>Never</td></tr>
      <tr><td><strong>Method notes</strong></td><td>Method and class count; Policy W-1; W16 not received; denominator and why</td><td>“positions ± 15 m (phone)”; symbol footprint if it matters</td><td>Never — on a phone they live in the layer description</td></tr>
      <tr><td><strong>Scale bar</strong></td><td>Yes — a 0 / 500 m / 1 km bar</td><td>Yes (self-consistent on screen)</td><td>When the medium resizes freely (a slide that may be cropped)</td></tr>
      <tr><td><strong>North arrow</strong></td><td><strong>No</strong> — the practice grid has no Earth direction; write “schematic grid — not oriented to north”</td><td>Same</td><td>Whenever north is meaningless or obvious; add when the map is rotated</td></tr>
    </tbody></table></div>
  <div class="callout note"><span class="label">Platform note</span><p>A scale bar can only be labelled in ground units if the map’s coordinate system has units. ArcGIS Pro’s help says a layout scale bar “updates to remain correct” when the map scale changes — but our practice grid has <em>no</em> coordinate system. How each product behaves then is a <strong>verification item</strong> for the instructor (lab 12.8); if the bar cannot be labelled, draw a 1,000 m line and label it — that is an honest scale bar.</p></div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“A north arrow and a scale bar make it a proper map.”</em> A north arrow on a schematic grid claims a direction the data do not have. A scale bar on a projection that stretches across the page (Chapter 6’s Web Mercator) is wrong at most points. ArcGIS Pro’s own scale-bar help warns that map scales “can vary based on coordinate system, latitude, direction, and map extent”.</p></div>

  <h2><span class="mod">12.6.2</span>Zero is a value; missing is not</h2>
  <p>District G-12 has <strong>W03 = 0</strong> (checked; nothing open) and <strong>W16 = no data</strong> (export not received). Chapter 10 kept <code>IS NULL</code> apart from <code>= 0</code>; the map must keep them apart <em>visually</em>.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>What happens if someone “fixes” the NULL with a 0?</h3>
    <div class="controls">
      <button class="btn small" data-nd="right" aria-pressed="true">Right: W16 excluded and hatched</button>
      <button class="btn small" data-nd="wrong">Wrong: W16 replaced by 0 on import</button>
    </div>
    <div class="pair">
      <figure class="map-fig" id="ndFig"></figure>
      <div><div id="ndLegend"></div><div class="result" id="ndOut"></div></div>
    </div>
  </div>
  <div class="table-wrap"><table>
    <thead><tr><th></th><th>Zero (W03)</th><th>Missing (W16)</th></tr></thead>
    <tbody>
      <tr><td>Is it a value?</td><td>Yes, 0</td><td>No</td></tr>
      <tr><td>In the classification?</td><td>Yes (lowest class)</td><td>No</td></tr>
      <tr><td>Symbol</td><td>Lowest class colour</td><td>Hatch or neutral texture, <em>off</em> the ramp; survives grey</td></tr>
      <tr><td>Legend text</td><td>“0 – 7 open requests”</td><td>“No data — export not received”</td></tr>
      <tr><td>In the total?</td><td>Yes (adds 0)</td><td>No — the caption says “238 over 15 wards; W16 unknown”</td></tr>
      <tr><td>In a rate?</td><td>0.0 per 1,000</td><td>NULL — never 0.0</td></tr>
    </tbody></table></div>
  <div class="callout note"><span class="label">How each product shows the missing case</span><p><strong>ArcGIS Pro</strong> graduated colours: values with nulls end up “out of range”; “Show excluded values” adds an <code>&lt;excluded&gt;</code> class you can symbolise — that is where W16’s hatch goes. <strong>ArcGIS Online</strong> Counts and Amounts: the toggle “Show features with no value” draws locations with missing data with their own style and label. <strong>QGIS</strong>: the categorized renderer has an “all other values” class; how the <em>graduated</em> renderer draws a NULL is not stated in the manual section read — a verification item — and a NULL must never be silently undrawn, because an undrawn ward looks like a hole. In a web library, the missing case is a style rule you write yourself.</p></div>
  <p><strong>Keep the unmatched and edge cases in the notes.</strong> The per-ward count came from a join that had requests outside every ward and requests on an edge (G06). A choropleth cannot show them. The notes must: “1 request outside all wards, not counted; 1 request on the W06/W07 edge, counted in W06 under Policy W-1.” The reconciliation of Chapter 11 — assigned + on-edge + unassigned = total — is the <em>source-value table</em> the lab asks for; the map is trustworthy only if that table can be produced beside it.</p>

  <h2><span class="mod">12.6.3</span>A ward value is not every street in the ward</h2>
  <p>W12’s 15.5 per 1,000 households is a <em>ward average</em>. Inside W12 there may be one market street with twenty open requests and residential lanes with none. Reading an area value as if it applied to every point inside the area is the oldest error in thematic mapping. Guard against it in three places on the map:</p>
  <ol>
    <li><strong>State the aggregation.</strong> “Values are per ward (16 units of 1 km²).” The unit of the claim is like a raster’s cell size (Chapter 4). A different unit — per 500 m cell, per street — gives a different map from the same requests.</li>
    <li><strong>State the boundary policy</strong> and how many records it affected: “1 edge case (G06 → W06); 0 unassigned in W06/W07.”</li>
    <li><strong>State the limitations on the map:</strong> capture accuracy; the date and the meaning of “open”; the denominator’s date; the types mixed together; that the rate measures <em>reports</em>, not faults; that no individual street is described.</li>
  </ol>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Fix the sentence that overreaches</h3>
    <p>Draft report: <em>“Residents of W12 are three times as likely to have a problem as residents of W14.”</em> Rates: W12 15.5, W14 4.0 per 1,000 households. Click the problems you can find.</p>
    <div class="faults" id="sentence">
      <div class="fault" data-w="The number is wrong: 15.5 ÷ 4.0 = 3.9, about four times, not three."><span class="n">1</span><div>“three times”<div class="why"></div></div></div>
      <div class="fault" data-w="It compares requests-per-household, which are ward averages of reports — not the chance that a given resident has a problem."><span class="n">2</span><div>“residents … are … likely to have a problem”<div class="why"></div></div></div>
      <div class="fault" data-w="A rate counts reports. More reports may mean more faults or more reporting; the map cannot separate them."><span class="n">3</span><div>“a problem” (treating reports as faults)<div class="why"></div></div></div>
    </div>
    <details class="reveal"><summary>Show the corrected sentence</summary><p>“At 2026-09-15, W12 had 15.5 open requests per 1,000 households and W14 had 4.0 — about four times the rate. The figures are ward averages of <em>reports</em>; they do not describe individual streets, and they do not separate reporting behaviour from underlying faults.” The finding stays, the number is right, and the claims about individuals and causes are gone.</p></details>
  </div>
  <div class="callout dev"><span class="label">Developer view</span><p>“Service A’s mean latency is 200 ms” says nothing about the request that took 4 s; every developer looks at the distribution. A ward value is a mean over a spatial unit, and the distribution inside it is invisible on a choropleth. <strong>Where it stops:</strong> the latency histogram is one click away; the inside-the-ward distribution is a <em>different map</em> (the points) — which is why the lab makes both maps from one dataset.</p></div>

  <div class="quiz" data-answer="0" data-fb="W16 now has a value (9) and joins the classification. W03’s count is now unknown, so it leaves the classification and takes the no-data symbol with a legend entry that says why. The total becomes 238 − 0 + 9 = 247 over 15 wards, W03 unknown.">
    <div class="q">Next month W16’s export arrives with 9 open requests, and W03’s export turns out to have been empty because of a filter error (its true count is unknown). What changes on the map?</div>
    <div class="opts"><button class="opt">W16 is classified normally; W03 becomes “No data — filter error”; caption “247 over 15 wards; W03 unknown”.</button><button class="opt">Both are classified; W03 keeps 0 because that is what the file said.</button><button class="opt">Both are shown as no data, to be safe.</button><button class="opt">Nothing — the map was published last month.</button></div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const boxes = document.querySelectorAll("#elems input"), card = document.getElementById("card"), out = document.getElementById("elemOut");
  function build() {
    const on = {}; boxes.forEach(b => on[b.dataset.el] = b.checked);
    const u = on.units ? " open requests per 1,000 households" : "";
    card.innerHTML = `<div class="t ${on.title ? "" : "missing"}">${on.title ? "Open service requests per 1,000 households — District G-12 wards — 2026-09-15 (made-up data)" : "(no title — what is this map of? when?)"}</div>
      <div class="pair"><div style="border:1px dashed var(--rule);border-radius:6px;padding:.4rem;text-align:center;font-size:.85rem;color:var(--ink-soft)">[ 4 × 4 ward map ]${on.shops ? "<br><span class=\"missing\">…Sharma Sweets · Patel Medical · Bus stand · Krishna Kirana…</span>" : ""}${on.north ? "<br><span class=\"missing\">↑ N (on a grid with no north!)</span>" : ""}${on.scale ? "<br>▬▬▬ 1 km" : ""}</div>
      <div>${on.legend ? `<div class="leg"><div class="row"><span class="sw" style="background:#fbe3cf"></span>0 – 2.5${u}</div><div class="row"><span class="sw" style="background:#e78a4e"></span>&gt; 2.5 – 5.0${u}</div><div class="row"><span class="sw" style="background:#8a2f0b"></span>&gt; 5.0${u}</div><div class="row"><span class="sw hatch"></span>No data — export not received (W16)</div></div>` : "<span class=\"missing\">(no legend — what do the colours mean?)</span>"}</div></div>
      <div class="notes">${on.source ? "Requests: training tracker export 2026-09-15 · Households: 2024 synthetic register · Boundaries: 2024 ward layer." : "<span class=\"missing\">(no source or date)</span>"}<br>${on.method ? "Manual classes (ties at 2.0, 4.0, 5.0). Numerator: status OPEN, assigned by Policy W-1. Denominator: households. W16 not received — shown as no data, not zero. Positions ± 15 m. This rate measures reports per household; it is not a measure of risk." : "<span class=\"missing\">(no method note — how were the classes made? what is W16?)</span>"}</div>`;
    const problems = []; if (!on.title) problems.push("no title"); if (!on.legend) problems.push("no legend"); if (on.legend && !on.units) problems.push("legend numbers without units"); if (!on.source) problems.push("no source/date"); if (!on.method) problems.push("no method note"); if (!on.scale) problems.push("no size reference"); if (on.north) problems.push("north arrow on a grid that has no north — misleading"); if (on.shops) problems.push("shop names compete with the wards — clutter");
    out.innerHTML = problems.length ? `<span class="verdict no">Not yet.</span> ${problems.join("; ")}.` : "<span class=\"verdict ok\">Complete.</span> Title, legend with units and a no-data entry, source/date, method note, a 1 km reference — and no decoration that claims what the data cannot.";
  }
  boxes.forEach(b => b.addEventListener("change", build)); build();

  let nd = "right";
  function ndDraw() {
    document.querySelectorAll("[data-nd]").forEach(b => b.setAttribute("aria-pressed", b.dataset.nd === nd));
    const vals = G12.wards.map(w => w.open == null ? (nd === "wrong" ? 0 : null) : w.open).filter(v => v != null), br = jenks(vals, 3);
    renderG12(document.getElementById("ndFig"), { style: w => { const v = w.open == null ? (nd === "wrong" ? 0 : null) : w.open; return v == null ? { hatch: true } : (c => ({ fill: RAMP[3][c], dark: c === 2, label: v }))(classOf(v, br)); }, title: nd === "wrong" ? "16 wards, 238 open requests" : "238 open requests over 15 wards; W16 not received" });
    document.getElementById("ndLegend").innerHTML = legendHtml(RAMP[3], classLabels(vals, br, "open requests"), nd === "wrong" ? null : "No data — export not received (W16)");
    document.getElementById("ndOut").innerHTML = nd === "wrong" ? "<span class=\"verdict no\">Wrong.</span> W16 and W03 are now the same pale colour. The reader concludes both are fine; W16’s requests — which nobody has counted — are invisible; the caption “16 wards” is false; and a rate map would give W16 the <em>best</em> rate (0.0) for a ward with no data." : "<span class=\"verdict ok\">Right.</span> W03 (0) sits in the lowest class as a real value. W16 is hatched, off the ramp, with its own legend line and a caption that says 15 wards. Nothing pretends to know what it does not.";
  }
  document.querySelectorAll("[data-nd]").forEach(b => b.addEventListener("click", () => { nd = b.dataset.nd; ndDraw(); })); ndDraw();

  document.querySelectorAll("#sentence .fault").forEach(f => f.addEventListener("click", () => { f.classList.toggle("open"); f.querySelector(".why").textContent = f.dataset.w; }));
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
