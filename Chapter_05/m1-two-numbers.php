<?php $page = ['title' => '5.1 Two numbers are not a place', 'chapter' => 5, 'module' => '5.1']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 5.1 · General GIS idea</div>
    <h1>Two numbers are not a place</h1>
    <p class="lead">A coordinate is a pair of numbers <em>plus an agreement</em> about what the numbers mean. This module shows what goes wrong when the agreement is missing, and gives you a five-line card to write it down.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>See why the same two numbers can point to completely different places.</li>
      <li>Name the four things a flat grid needs: origin, axes, direction, unit — and what an Earth-referenced dataset has on top.</li>
      <li>Fill in a <strong>coordinate card</strong> and spot when a line is missing.</li></ul></div>
  </div>

  <h2><span class="mod">5.1.1</span>Where is <code>23.025, 72.6</code>?</h2>
  <p>A colleague pastes two numbers into a chat message. Click each pair below and read what it <em>could</em> mean.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <div class="controls">
      <button class="btn" data-pair="0" aria-pressed="true">23.025, 72.6</button>
      <button class="btn" data-pair="1">1200, 250</button>
      <button class="btn" data-pair="2">254318.4, 2547906.2</button>
    </div>
    <div class="result" id="pairOut"></div>
  </div>
  <p>Every pair has more than one reading. That is the whole lesson of this module: <strong>a coordinate is not complete until you know what the numbers refer to.</strong> The written agreement is called a <dfn title="Coordinate reference system: the definition of origin, axes, units and Earth model that gives coordinate numbers their meaning.">coordinate reference system</dfn>, or <strong>CRS</strong>. Esri’s software often calls the same thing a “coordinate system” or a “spatial reference”; Esri’s own definition is that a spatial reference “describes where features are located in the real world”.</p>
  <div class="callout idea"><span class="label">Everyday picture</span><p>A house number without the area name. “Number 14” is useless until you add “Navrangpura” or “Satellite”. And even then, “Navrangpura” in <em>which</em> city? The CRS is the area name and the city name together — it is what makes the number find one house only.</p></div>

  <h2><span class="mod">5.1.2</span>Origin, axes, direction, unit — on the grid you already know</h2>
  <p>Our Chapter 1 practice grid is a <strong>Cartesian</strong> system: two straight axes at right angles, an origin, and a unit. The QGIS introduction describes exactly this: “A two-dimensional coordinate reference system is commonly defined by two axes. At right angles to each other, they form a so called XY-plane.” Look at the four labelled facts on the figure.</p>
  <figure class="map-fig" id="gridFig"></figure>
  <div class="grid-2" style="grid-template-columns:repeat(2,1fr)">
    <div class="card"><h4 style="margin-top:0">1 · Origin</h4><p>(0, 0) — the bottom-left corner of Ward A. Every value is measured from here.</p></div>
    <div class="card"><h4 style="margin-top:0">2 · Axes</h4><p>x grows to the right, y grows upward. Two numbers, two directions.</p></div>
    <div class="card"><h4 style="margin-top:0">3 · Direction</h4><p>“Right” and “up” are just the directions we chose when we drew the grid. <strong>Nothing says right is east.</strong></p></div>
    <div class="card"><h4 style="margin-top:0">4 · Unit</h4><p>One grid step is one metre — because the fixture’s notes <em>say so</em>. The numbers themselves cannot tell you.</p></div>
  </div>
  <p>Now compare the practice grid with real, <strong>Earth-referenced</strong> data — which is what almost all GIS data is:</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Property</th><th>Practice grid (Chapter 1)</th><th>Earth-referenced dataset</th></tr></thead>
    <tbody>
      <tr><td>Origin</td><td>(0, 0) at a corner we chose</td><td>Defined by the CRS — e.g. the equator and the prime meridian (5.2), or a zone’s centre line with offsets added (5.4)</td></tr>
      <tr><td>Axis directions</td><td>Chosen by us</td><td>Defined by the CRS: east and north for most “metres” systems; north (latitude) and east (longitude) for “degrees” systems</td></tr>
      <tr><td>Unit</td><td>Metres, by declaration</td><td>Degrees, metres, feet… — defined by the CRS</td></tr>
      <tr><td>Tie to the Earth</td><td><strong>None.</strong> It is graph paper.</td><td>Yes: the CRS includes a model of the Earth (5.3)</td></tr>
      <tr><td>Identifier</td><td>None</td><td>Usually a code such as <code>EPSG:4326</code> (5.4)</td></tr>
    </tbody></table></div>
  <div class="callout note"><span class="label">Platform note — what software does with a grid that has no CRS</span>
    <p><strong>ArcGIS Pro</strong> calls this an <em>unknown</em> coordinate system. Esri’s documentation says that in a map set to unknown, “values are unitless”, “editing is not permitted”, and the measure tool “measures unitless, planar quantities only”. <strong>QGIS</strong> has a project option <em>No CRS (or unknown/non-Earth projection)</em>, which “will disable ALL projection handling within the QGIS project, causing all layers and map coordinates to be treated as simple 2D Cartesian coordinates, with no relation to positions on the Earth’s surface”.</p>
    <p>Both behaviours are <em>correct</em> for our practice grid. They are a disaster for real data that merely <em>lost</em> its CRS label — a case you will meet in the lab (5.8).</p></div>

  <h2><span class="mod">5.1.3</span>The coordinate card — five lines, never just “X/Y”</h2>
  <p>From now on, every coordinate in this course (other than the practice grid) gets a five-line card. Fill the card below for point G1 by choosing an entry for each line. Watch the verdict change.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <div class="two-col">
      <div>
        <div class="check-row"><span class="lab">1 · Values</span><select id="c1"><option value="">(not written)</option><option value="a">23.025, 72.6</option><option value="b" selected>latitude 23.0250, longitude 72.6000</option></select></div>
        <div class="check-row"><span class="lab">2 · CRS</span><select id="c2"><option value="">(not written)</option><option value="a">WGS 84</option><option value="b">WGS 84, EPSG:4326</option></select></div>
        <div class="check-row"><span class="lab">3 · Units</span><select id="c3"><option value="">(not written)</option><option value="b">degrees</option><option value="a">metres</option></select></div>
        <div class="check-row"><span class="lab">4 · Order in this file</span><select id="c4"><option value="">(not written)</option><option value="a">“X/Y”</option><option value="b">CSV columns lat, lon (latitude first)</option></select></div>
        <div class="check-row"><span class="lab">5 · Precision</span><select id="c5"><option value="">(not written)</option><option value="b">4 decimals ≈ 11 m</option><option value="a">as many digits as the GPS gave</option></select></div>
      </div>
      <div class="ccard" id="ccard"></div>
    </div>
    <div class="result" id="cardOut"></div>
  </div>
  <p><strong>Line 4 is the one developers skip</strong>, and the one that causes the most silent errors. The CRS says what each axis <em>means</em>; the file or API says in which <em>position</em> each axis is written. Two different contracts. Module 5.5 shows they can disagree.</p>
  <div class="callout dev"><span class="label">Developer view</span><p><code>09:30</code> is not a moment in time until you know the time zone — and <code>03/04</code> is 4 March or 3 April depending on the locale. A coordinate without a CRS is like a time without a zone; a coordinate without an order rule is like a date without a locale. <strong>Where the analogy stops:</strong> converting a time between zones is exact. Converting coordinates between two Earth models (5.3) is <em>not</em> exact — it needs a chosen transformation with a stated accuracy. “Just convert it” is safe for time zones and unsafe for coordinates. Chapter 6 deals with that.</p></div>
  <div class="callout warn"><span class="label">Misconception</span><p>“Every GIS uses x = longitude and y = latitude, so I never need to write the order down.” Many engines do store data that way — but the formal definition of EPSG:4326 lists its axes as <em>latitude, longitude</em>; some libraries take (latitude, longitude); and a CSV can put a <code>lat</code> column first. A developer who assumes one universal rule will one day write a swapped pair that passes every range check.</p></div>

  <div class="quiz" data-answer="2" data-fb="“WGS 84” names the Earth model (the datum). It does not say whether the numbers are degrees or metres, which column is which, or how precise they are. Module 5.3 explains why.">
    <div class="q">A CSV has columns <code>id, x, y</code> and the note “these are in WGS 84”. Which is true?</div>
    <div class="opts">
      <button class="opt">The note is enough — WGS 84 fixes the units and the order.</button>
      <button class="opt">x must be latitude because it comes first.</button>
      <button class="opt">You still need the units, which column is which, the exact CRS code and the precision.</button>
      <button class="opt">Nothing can be known until the file is opened in GIS software.</button>
    </div><div class="fb"></div>
  </div>

  <div class="callout note"><span class="label">Comprehension check (write it down)</span><p>A colleague sends you a CSV with columns <code>id, x, y</code> and the message “these are in WGS 84”. List the questions you still have to ask before you can load the file correctly, and say which of them the phrase “WGS 84” does <em>not</em> answer.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const pairs = [
    { t: "23.025, 72.6", rows: [["Latitude then longitude, in degrees", "A point in western India (that is where set E5 lives)"], ["Longitude then latitude, in degrees", "A point in the sea north of Norway — same numbers, other order (see 5.5)"]], need: "Which number is which? Which reference system? Which unit?" },
    { t: "1200, 250", rows: [["Metres on our practice grid", "Request P3 — inside Ward B"], ["Feet in some local engineering grid", "A point about 366 m and 76 m from a different origin"], ["Nothing on the Earth", "The grid may be pure graph paper"]], need: "Is the grid tied to the Earth? What is the unit? Where is the origin?" },
    { t: "254318.4, 2547906.2", rows: [["Easting and northing in metres, in a UTM zone", "A point in that zone (5.4) — but which of the 120 zones?"], ["Some other projected system with a similar range", "Somewhere else entirely"]], need: "The full name of the CRS — including the zone and the hemisphere." }
  ];
  const out = document.getElementById("pairOut");
  const show = i => { const p = pairs[i]; out.innerHTML = `<strong style="font-family:var(--font-mono)">${p.t}</strong><table style="margin:.5rem 0"><thead><tr><th>Could be…</th><th>Then it is…</th></tr></thead><tbody>${p.rows.map(r => `<tr><td>${r[0]}</td><td>${r[1]}</td></tr>`).join("")}</tbody></table><b>What you must know first:</b> ${p.need}`; document.querySelectorAll("[data-pair]").forEach(b => b.setAttribute("aria-pressed", b.dataset.pair == i ? "true" : "false")); };
  document.querySelectorAll("[data-pair]").forEach(b => b.addEventListener("click", () => show(+b.dataset.pair))); show(0);

  renderGrid(document.getElementById("gridFig"));

  const labels = ["Values", "CRS", "Units", "Order in this file", "Precision"];
  const good = { c1: { b: "latitude 23.0250, longitude 72.6000" }, c2: { b: "WGS 84, EPSG:4326" }, c3: { b: "degrees" }, c4: { b: "CSV columns lat, lon — latitude first" }, c5: { b: "4 decimals ≈ 11 m" } };
  const weak = { c1: "23.025, 72.6 — which is which?", c2: "WGS 84 — a datum name, not a full CRS (5.3)", c3: "metres — wrong for latitude/longitude", c4: "“X/Y” — says nothing about which axis is first", c5: "unstated — implies more accuracy than exists" };
  const card = document.getElementById("ccard"), cout = document.getElementById("cardOut");
  const build = () => {
    let missing = 0, weakN = 0, html = "";
    ["c1", "c2", "c3", "c4", "c5"].forEach((id, i) => {
      const v = document.getElementById(id).value;
      let cls = "ok", txt = good[id].b;
      if (!v) { cls = "missing"; txt = "missing"; missing++; } else if (v === "a") { cls = "missing"; txt = weak[id]; weakN++; }
      html += `<div class="row ${cls}"><span class="k">${i + 1} · ${labels[i]}</span><span class="v">${txt}</span></div>`;
    });
    card.innerHTML = html;
    cout.innerHTML = (missing === 0 && weakN === 0) ? `<div class="verdict ok">Complete card.</div>Another developer can now load this point without asking you anything.` : `<div class="verdict no">${missing ? missing + " line(s) missing" : ""}${missing && weakN ? ", " : ""}${weakN ? weakN + " line(s) too vague" : ""}.</div>Someone loading this point will have to guess — and a guess that draws “somewhere” looks exactly like a correct answer.`;
  };
  ["c1", "c2", "c3", "c4", "c5"].forEach(id => document.getElementById(id).addEventListener("change", build)); build();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
