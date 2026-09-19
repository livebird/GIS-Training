<?php $page = ['title' => '12.1 Who is the map for?', 'chapter' => 12, 'module' => '12.1']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 12.1 · General GIS idea</div>
    <h1>Start with the reader and the decision</h1>
    <p class="lead">Before you pick a single colour, write two sentences: <strong>who</strong> will read this map, and <strong>what must they decide</strong> after reading it? Every other choice in this chapter follows from those two sentences.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>See a <strong>crew map</strong> and a <strong>manager’s map</strong> built from the same data, and why one map cannot do both jobs.</li>
      <li>Write down the <strong>medium</strong> (paper, projector, phone), the <strong>viewing size</strong>, whether the reader can <strong>zoom and click</strong>, and how much <strong>detail</strong> is really needed.</li>
      <li>Remove every layer that does not help the decision.</li></ul></div>
  </div>

  <h2><span class="mod">12.1.1</span>Two readers, two decisions</h2>
  <p>Our practice municipality has service requests — potholes, streetlights, blocked drains. Two people look at the same requests and need opposite things.</p>
  <div class="grid-2">
    <div class="card"><h3 style="margin-top:0">The road crew supervisor</h3><p>Monday morning, in the van, on a phone. Decision: <em>“Which open requests do we visit today, in what order, and where exactly are they?”</em> Needs every open request as a <strong>point at its true place</strong>, its type and priority readable at a glance, the roads to plan a route. Does not need last month’s totals.</p></div>
    <div class="card"><h3 style="margin-top:0">The ward services manager</h3><p>Monthly review meeting, projector, then a PDF in the minutes. Decision: <em>“Which wards need more crew next quarter — and is that fair to say from the numbers?”</em> Needs <strong>one number per ward</strong> that can be compared fairly. A hundred dots would hide the pattern.</p></div>
  </div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Same data, two maps — switch between them</h3>
    <p>Both maps below are drawn from the same practice tables (F12-1 and F12-2). Switch the reader and watch what appears and what disappears.</p>
    <div class="controls">
      <button class="btn small" data-reader="crew" aria-pressed="true">Crew supervisor (W06–W07)</button>
      <button class="btn small" data-reader="mgr">Ward services manager (district)</button>
      <button class="btn small" data-reader="both">“One map for everyone” (the mistake)</button>
    </div>
    <figure class="map-fig" id="readerFig"></figure>
    <div class="result" id="readerOut"></div>
  </div>

  <div class="table-wrap"><table>
    <thead><tr><th>Design question</th><th>Crew map</th><th>Manager’s map</th></tr></thead>
    <tbody>
      <tr><td>The decision</td><td>Where do we go today, in what order?</td><td>Where should crew capacity go next quarter?</td></tr>
      <tr><td>Unit shown</td><td>Individual request points</td><td>Wards, one value each</td></tr>
      <tr><td>Must-have attributes</td><td>type, priority, status = OPEN, request ID for the job sheet</td><td>open requests <strong>and a denominator</strong> (12.5); the count date; W16 = not received</td></tr>
      <tr><td>Supporting context</td><td>Roads, ward outlines</td><td>Ward outlines and codes — nothing else</td></tr>
      <tr><td>Wrong things to add</td><td>Household totals, last year’s closed requests</td><td>Every point, the asset list, shop names from the basemap</td></tr>
      <tr><td>A conclusion the map must <em>not</em> invite</td><td>“The pothole G10 is worse than the drain G04” (priority says the opposite)</td><td>“W15 is the worst-run ward” (a count is not performance — 12.5)</td></tr>
    </tbody></table></div>

  <div class="callout dev"><span class="label">Developer view</span><p>A <strong>work queue</strong> (tickets sorted by priority, with location) and a <strong>KPI dashboard</strong> (tickets per team per month) come from the same table and are both correct. Put the KPI tile on the technician’s phone, or the raw ticket list on the CEO’s dashboard, and each becomes useless. <strong>Where the analogy stops:</strong> a wrong number on a dashboard is usually caught by someone checking it; a map that <em>looks</em> convincing is often believed without anyone checking the number. Maps carry more unearned trust than tables — so the discipline has to be stricter.</p></div>

  <div class="callout warn"><span class="label">Common mistake</span><p><em>“One good map can serve everyone — put everything on it and let readers pick.”</em> The crew cannot find the point under the ward shading; the manager reads dot density as workload; both draw a conclusion nobody checked. A map with two purposes has none.</p></div>

  <h2><span class="mod">12.1.2</span>Medium, size, interaction, detail</h2>
  <p>The reader’s <em>device</em> decides as much as the reader’s purpose. Write these four things next to the decision:</p>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">1. Medium</h4><p>Printed A4 page, projector slide, desktop web map, phone screen — or a PDF that someone will print in black and white on the office printer. That last case is very common in India, and it is why module 12.3 asks you to check every map in grey.</p></div>
    <div class="card"><h4 style="margin-top:0">2. Viewing size</h4><p>An A4 sheet is 210 mm × 297 mm; a phone is about 70 mm wide; a projected slide is read from metres away. The same 6-point label is fine on paper, invisible on a projector, unreadable on a phone. Design for the size at which the map is <em>read</em>.</p></div>
    <div class="card"><h4 style="margin-top:0">3. Interaction</h4><p>Paper cannot be zoomed, filtered or clicked, so everything must be visible at once. A web map can hide detail behind zoom levels and pop-ups — but the <em>default view</em> must still be honest, because many readers never zoom or click.</p></div>
    <div class="card"><h4 style="margin-top:0">4. Required detail</h4><p>The crew map needs every open request at its place. The manager’s map needs sixteen numbers. Detail beyond what the decision needs is not “extra information” — it competes for the reader’s attention.</p></div>
  </div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>How big is the map, really?</h3>
    <p>District G-12 is 4 km wide. Choose where the map will be read and see the natural map scale and what a 12-point symbol covers on the ground. (Scale is explained properly in 12.2 — here just notice how much the <em>device</em> changes.)</p>
    <div class="controls">
      <label>Read on <select id="medium">
        <option value="160">A4 paper, 160 mm map frame</option>
        <option value="70">Phone, whole district (70 mm)</option>
        <option value="600">Projector, 600 mm wide image</option>
        <option value="2000">Wall poster, 2 m wide</option>
      </select></label>
    </div>
    <div class="tiles" id="mediumTiles"></div>
    <p class="small">A printed 1:25,000 is exact — paper does not change size. An on-screen “1:25,000” is only <em>nominal</em>: the software cannot know the real pixel size of every phone and monitor. Treat the on-screen scale number as approximate and the scale bar drawn in the same pixels as self-consistent.</p>
  </div>

  <h2><span class="mod">12.1.3</span>What can be removed?</h2>
  <p>Every layer, label and number on the map should pass one test: <em>if I removed this, would the reader’s decision get worse?</em> If not, remove it. Removing is not hiding — the full data stays in the package and in the analysis log (Chapter 11). The map shows the <em>decision-relevant part</em> and says so in its notes.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Keep or remove — for the <em>crew</em> map of W06–W07</h3>
    <p>Click a layer, then click the bin it belongs in.</p>
    <div class="sorter" data-items='[
      {"t":"Open requests (13 points)","bin":"keep","why":"the subject of the map"},
      {"t":"Station Road and other roads","bin":"keep","why":"the crew plans a route on them; drawn subdued"},
      {"t":"Ward outlines W06 / W07","bin":"keep","why":"the crew boundary; the G06 edge case must be visible"},
      {"t":"Ward shading by open-request count","bin":"remove","why":"a ward total tells the crew nothing about where to go and hides the points"},
      {"t":"All assets: every streetlight, drain and tree","bin":"remove","why":"hundreds of symbols; the request already names its asset"},
      {"t":"Basemap with every shop name","bin":"remove","why":"names compete with the request labels; use a light basemap or none"},
      {"t":"Reporter notes as labels","bin":"remove","why":"personal details (12.7) and clutter"},
      {"t":"Inspections table","bin":"remove","why":"not needed for today’s route"}
    ]'>
      <div class="items"></div>
      <div class="grid-2" style="margin-top:.6rem">
        <div class="bin" data-bin="keep"><h4>Keep</h4></div>
        <div class="bin" data-bin="remove"><h4>Remove (stays in the data, not on this map)</h4></div>
      </div>
    </div>
  </div>
  <p class="small-note">The three closed requests (G14–G16) are a judgement call: usually remove them; if the crew wants them to avoid a repeat visit, show them hollow and grey, and say so in the notes.</p>

  <div class="callout warn"><span class="label">Common mistake</span><p><em>“Removing layers hides information from the reader.”</em> A defensive author keeps everything, the reader cannot find the part that matters, and the map delivers <em>less</em> useful information than the sparse one. Whether data may be <em>seen</em> at all is a governance question (12.7), decided by access rules — not by what is drawn.</p></div>

  <div class="quiz" data-answer="2" data-fb="The councillor’s sentence hides two possible decisions: which lamps to repair this week (a crew map: individual assets/requests) or whether W11 needs more lighting budget than other wards (a manager’s map: one value per ward). Ask which — then choose the unit and the layers.">
    <div class="q">A councillor asks for “a map of the streetlight situation in W11”. What is the best first step?</div>
    <div class="opts">
      <button class="opt">Open the software and add all streetlight layers for W11.</button>
      <button class="opt">Make one map with every streetlight and the ward totals, so it covers all needs.</button>
      <button class="opt">Ask what decision the map is for — repairs this week, or budget next year — because the two need different maps.</button>
      <button class="opt">Send the attribute table instead; tables are always clearer.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const fig = document.getElementById("readerFig"), out = document.getElementById("readerOut");
  const vals = G12.wards.filter(w => w.open != null).map(w => w.open), br = jenks(vals, 3);
  const cls = w => classOf(w.open, br);
  function show(mode) {
    document.querySelectorAll("[data-reader]").forEach(b => b.setAttribute("aria-pressed", b.dataset.reader === mode));
    if (mode === "crew") {
      renderCrew(fig, { pt: 10, scale: 12000, showClosed: "hollow", caption: "Crew map: 13 open requests as points (shape = type, size = priority), closed ones hollow, Station Road, ward outlines. Made-up data." });
      out.innerHTML = "<strong>The crew supervisor</strong> sees every open job at its place, its type by shape and its priority by size. No ward totals, no shading — nothing competes with the points.";
    } else if (mode === "mgr") {
      renderG12(fig, { style: w => w.open == null ? { hatch: true } : { fill: RAMP[3][cls(w)], dark: cls(w) === 2, label: w.open }, title: "Open requests per ward · 2026-09-15", caption: "Manager’s map: one number per ward in three disclosed classes; W16 hatched = no data. Made-up data." });
      out.innerHTML = "<strong>The manager</strong> sees sixteen wards and one value each. No points. (Whether a raw <em>count</em> is the fair thing to compare is module 12.5 — for now, notice only what the map contains.)";
    } else {
      renderG12(fig, { style: w => w.open == null ? { hatch: true } : { fill: RAMP[3][cls(w)], dark: cls(w) === 2 }, title: "Everything for everyone", caption: "Ward shading with all request points on top: the crew cannot find its points, the manager reads dot density as workload." });
      const svg = fig.querySelector("svg"), pad = 60, cell = 220, top = pad + 70;
      REQ.forEach(q => { const w = ward(q.x < 2000 ? "W06" : "W07"); const x = pad + w.col * cell + (q.x - w.x0) / 1000 * cell, y = top + w.row * cell + (1000 - (q.y - w.y0)) / 1000 * cell; mkEl("circle", { cx: x, cy: y, r: 9, fill: q.status === "OPEN" ? "#f4c542" : "#fff", stroke: "#1f2a44", "stroke-width": 2 }, svg); });
      out.innerHTML = "<strong>The mistake.</strong> Points on top of shading: the dots in W06–W07 are lost against the colour, and a reader sees “many dots in the dark wards” even though the dots are only the two wards we have point data for. Two purposes, one map — neither is served.";
    }
  }
  document.querySelectorAll("[data-reader]").forEach(b => b.addEventListener("click", () => show(b.dataset.reader)));
  show("crew");

  const med = document.getElementById("medium"), tiles = document.getElementById("mediumTiles");
  function medium() {
    const mm = +med.value, den = rf(4000, mm), fp = footprintM(12, den);
    tiles.innerHTML = `<div class="tile"><div class="k">Map scale (4 km on ${fmtN(mm)} mm)</div><div class="v">1:${fmtN(Math.round(den))}</div><div class="s">1 mm on the map = ${fmtN(den / 1000, 1)} m on the ground</div></div>
      <div class="tile ${fp > 60 ? "bad" : "good"}"><div class="k">A 12-point symbol covers</div><div class="v">${fmtN(fp)} m</div><div class="s">on the ground — ${fp > 60 ? "wider than a whole street" : "about one house plot"}</div></div>
      <div class="tile"><div class="k">A 10 m-wide road, to scale</div><div class="v">${(10000 / den).toFixed(2)} mm</div><div class="s">${10000 / den < 0.5 ? "thinner than a printed line — draw it as a symbol" : "can be drawn to width"}</div></div>`;
  }
  med.addEventListener("change", medium); medium();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
