<?php $page = ['title' => '8.2 Shape and location rules', 'chapter' => 8, 'module' => '8.2']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 8.2 · General idea, with ArcGIS / PostGIS notes</div>
    <h1>Decide the shape, the grid, and what the dot stands for</h1>
    <p class="lead">A database designer writes <code>amount NUMERIC(12,2) NOT NULL</code> and has said everything about a column. A <strong>shape</strong> column needs a longer sentence. This module gives you that sentence — six parts — and then spends most of its time on the part people forget: a point has no size, so <em>which spot</em> on a streetlight does the point stand for?</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Write a six-part <strong>shape specification</strong> for any table that has a location.</li>
      <li>Explain why “the pole base”, “the lamp head” and “where the citizen stood” are three different points that must not be mixed.</li>
      <li>Record <em>how</em> a location was obtained (survey, phone GPS, clicked on a map, guessed) instead of pretending all dots are equally exact.</li></ul></div>
  </div>

  <h2><span class="mod">8.2.1</span>The six-part shape specification</h2>
  <p>For every table that has a shape, the design must state:</p>
  <ol>
    <li><strong>Shape type</strong> — point, line or polygon (Chapter 3). In ArcGIS every row of a feature class has the same shape type, the same fields and the same coordinate system. In PostGIS you can declare a column as, for example, <code>geometry(POINT, 4326)</code>, which limits the column to points only. A “mixed” column is possible in some systems, but it is a decision, never a default.</li>
    <li><strong>Multipart rule</strong> — may one row hold several separate pieces? Esri’s help says line and polygon feature classes can be single-part or multipart. You decide, and you say why (a ward cut in two by a river is still one ward; two separate drains are two rows).</li>
    <li><strong>Grid and units</strong> — the coordinate reference system, its units, and the coordinate order used when exchanging files (Chapter 5). For our practice data: “flat training grid, metres, (x, y), no EPSG code, no real place.” Unusual, but complete.</li>
    <li><strong>Height (Z)</strong> — if you store heights, what does zero mean? Most municipal asset points do not need it. “No Z” is itself a valid, clear statement.</li>
    <li><strong>What the shape stands for</strong> — 8.2.2 below.</li>
    <li><strong>How the location was obtained, and how good it is</strong> — 8.2.3 below.</li>
  </ol>
  <div class="table-wrap"><table>
    <thead><tr><th>Entity</th><th>Shape</th><th>Multipart?</th><th>Grid &amp; units</th><th>Z</th><th>The shape stands for</th></tr></thead>
    <tbody>
      <tr><td><strong>Asset</strong></td><td>Point</td><td>Not applicable — one point per row (in ArcGIS, “multipoint” is a separate shape type)</td><td>Training grid, metres, (x, y), no EPSG</td><td>None</td><td>Depends on asset type — see 8.2.2</td></tr>
      <tr><td><strong>Ward</strong></td><td>Polygon</td><td>Allowed — a ward split by a river is still one ward row</td><td>Training grid, metres</td><td>None</td><td>The declared administrative boundary</td></tr>
      <tr><td><strong>Road</strong></td><td>Line</td><td>Not allowed — each separate stretch is its own row</td><td>Training grid, metres</td><td>None</td><td>The centre line of the road, not its edge</td></tr>
      <tr><td><strong>Request</strong></td><td>Point</td><td>Not applicable</td><td>Training grid, metres</td><td>None</td><td>The place the citizen pointed at — <em>not</em> an asset location</td></tr>
      <tr><td>Inspection, Team, Coverage</td><td colspan="5">No shape — plain tables</td></tr>
    </tbody></table></div>
  <div class="callout note"><span class="label">Platform note</span><p>The <em>list</em> of six things is a general principle. <em>Where</em> each is written differs: ArcGIS fixes shape type and coordinate system when the feature class is created; PostGIS keeps them on the column (<code>geometry(POINT, 4326)</code>); a GeoPackage records them in its metadata tables; a Shapefile keeps the CRS in a separate <code>.prj</code> file that can go missing (Chapter 7). The <em>decision</em> lives in your design document; the container only implements it.</p></div>

  <h2><span class="mod">8.2.2</span>What does the point stand for?</h2>
  <p>A point has no size. So a point that represents a real object is always a <em>choice</em> of which spot to record. Different choices give different coordinates for the same streetlight — and they are <strong>not interchangeable</strong>.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>One streetlight, three possible points</h3>
    <p>Click a button to see which spot the dot marks, what it is good for, and what goes wrong if you confuse it with another.</p>
    <div class="opbtns">
      <button class="btn small" id="pBase" aria-pressed="true">Centre of the pole base</button>
      <button class="btn small" id="pHead">The lamp head</button>
      <button class="btn small" id="pReport">Where the citizen stood</button>
    </div>
    <div class="grid-2">
      <figure class="map-fig" id="lampFig" style="max-width:420px"></figure>
      <div>
        <div class="reccard" id="lampCard"><div class="hdr">This point…</div><div class="row"><b>stands for</b><span class="v" id="lpFor"></span></div><div class="row"><b>good for</b><span class="v" id="lpGood"></span></div><div class="row"><b>if confused</b><span class="v" id="lpBad"></span></div></div>
        <div class="status-line q" id="lampStatus"></div>
      </div>
    </div>
  </div>
  <p><strong>Our rule for the practice town</strong> (written into the design, not left to guesswork):</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Asset type</th><th>The point is…</th><th>Not…</th></tr></thead>
    <tbody>
      <tr><td>Streetlight (SL)</td><td>centre of the pole base at ground level</td><td>the lamp head, the switch box</td></tr>
      <tr><td>Drain (DR)</td><td>centre of the grating (the opening you see on the road)</td><td>the chamber below, the outlet</td></tr>
      <tr><td>Tree (TR)</td><td>centre of the trunk at ground level</td><td>the middle of the canopy</td></tr>
    </tbody></table></div>

  <div class="try">
    <span class="tag">Worked example</span>
    <h3>P1 and SL-0113 are 7 metres apart — and that is fine</h3>
    <p>In Chapter 1, request <strong>P1</strong> (“streetlight out”) was at (200, 200) and asset <strong>SL-0113</strong> at (205, 195). They were kept as two separate records even though they are about the same problem. Why are the coordinates different?</p>
    <div class="grid-2">
      <figure class="map-fig" id="p1Fig"></figure>
      <div>
        <p>Difference in x: 205 − 200 = <strong>5 m</strong>. Difference in y: 195 − 200 = <strong>−5 m</strong>.</p>
        <p>Straight-line distance: √(5² + 5²) = √50 ≈ <strong id="p1d">7.07</strong> m (flat-grid arithmetic, checked by hand).</p>
        <p>A citizen standing near a pole and a surveyor measuring the pole base <em>should</em> give different points — because the two points mean different things. A design that put both into one column called <code>location</code>, with no statement of meaning, would tempt someone to “correct” one to match the other.</p>
      </div>
    </div>
  </div>
  <div class="callout dev"><span class="label">Developer view</span><p>Point meaning is like the time zone of a timestamp: <code>2026-03-14 10:42</code> is useless until you know whether it is IST or UTC, and whether it is the visit time or the typing time (module 8.3). Same principle — a value without its convention is ambiguous. <strong>Where the comparison stops:</strong> IST converts to UTC exactly, but you cannot compute the lamp-head position from the pole base without knowing the arm length and direction, which are not in the data. There is no automatic conversion between point meanings.</p></div>

  <h2><span class="mod">8.2.3</span>Measured or approximate? Say so.</h2>
  <p>Two rows can show identical-looking coordinates and be wildly different in reliability. Record <strong>how</strong> the location was obtained using a small fixed list (module 8.6 explains such lists properly):</p>
  <div class="table-wrap"><table>
    <thead><tr><th><code>location_method</code></th><th>Meaning</th><th>Typical quality</th></tr></thead>
    <tbody>
      <tr><td class="mono">SURVEY</td><td>Measured by a surveyor with proper survey equipment</td><td>Best; a stated accuracy may exist</td></tr>
      <tr><td class="mono">GNSS</td><td>Phone or field-app satellite position (GPS)</td><td>A few metres; depends on the phone and open sky</td></tr>
      <tr><td class="mono">DIGITISED</td><td>Clicked on a map or aerial image</td><td>Depends on the map scale and image alignment (Chapter 9)</td></tr>
      <tr><td class="mono">APPROX</td><td>Estimated from a description (“near the temple”)</td><td>Tens of metres or worse</td></tr>
    </tbody></table></div>
  <p>Recording the method is a general principle (Chapter 7’s intake form asked for it). The <em>accuracy numbers</em> are not general and must never be invented. Our practice coordinates are made up and carry no accuracy claim at all.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Which method would you record?</h3>
    <div class="sorter" data-items='[
      {"t":"Crew stood at the pole and tapped “use my location” in the field app","bin":"GNSS","why":"phone satellite position"},
      {"t":"Survey contractor measured the pole base with a total station and gave a report","bin":"SURVEY","why":"measured with survey equipment"},
      {"t":"Clerk clicked the spot on the web map from a phone complaint saying “opposite the market gate”","bin":"APPROX","why":"the clerk estimated from words; clicking does not make a guess exact"},
      {"t":"Intern traced the drain gratings from a recent aerial image","bin":"DIGITISED","why":"clicked on an image; quality depends on the image"}
    ]'>
      <div class="items"></div>
      <div class="bins">
        <div class="bin" data-bin="SURVEY"><h5>SURVEY</h5></div>
        <div class="bin" data-bin="GNSS"><h5>GNSS</h5></div>
        <div class="bin" data-bin="DIGITISED"><h5>DIGITISED</h5></div>
        <div class="bin" data-bin="APPROX"><h5>APPROX</h5></div>
      </div>
    </div>
  </div>
  <p><strong>What a shape cannot tell you.</strong> A point says <em>where</em>. It does not say <em>what</em> is there now, <em>when</em> the position was captured, or whether the asset still exists. A ward polygon shows the declared area; it does not tell you whether that declaration is current. Write these limits into the design so nobody reads more precision into the coordinates than the capture method can support.</p>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“Both datasets are points on the same grid, so merge them into one table.”</em> Only if the points <em>mean</em> the same thing and the rows have the same grain. Requests and assets share a grid and a shape type — but a request point is a citizen’s estimate of a problem, an asset point is a surveyed pole base. Merged, the table has mixed grain and mixed point meaning, and the first analysis on it (Chapter 11) will silently treat guesses as measurements.</p></div>

  <div class="quiz" data-answer="1" data-fb="The request point is a citizen’s estimate, recorded with its method (for example APPROX for a phone complaint, GNSS for an app report). Its location is not an asset location — the optional asset_id says which asset is meant, once a clerk decides.">
    <div class="q">Which is the correct “stands for” line in the shape specification of the <strong>Request</strong> table?</div>
    <div class="opts">
      <button class="opt">The surveyed position of the asset the complaint is about.</button>
      <button class="opt">The place the citizen indicated — which is not an asset location; the method (app GPS, phone description) is recorded separately.</button>
      <button class="opt">The centre of the ward the complaint came from.</button>
      <button class="opt">Whatever the field app records; no statement is needed.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const info = {
    base: { f: "the centre of the pole base, at ground level", g: "maintenance crews; “which asset is this?”; where the vehicle stops", b: "metres away from the lamp head if the arm is long — lighting analysis would be off", s: "This is our chosen meaning for streetlights (SL)." },
    head: { f: "the position of the lamp itself", g: "lighting-coverage analysis (where the light falls)", b: "not where a vehicle should stop; a crew may search for the pole in the wrong spot", s: "A valid meaning — but a different one. Do not mix it with pole-base points in one column." },
    report: { f: "where the person who complained was standing", g: "service requests — it is the citizen’s estimate", b: "if treated as the asset position, the asset “moves” a few metres each time someone complains", s: "This is a REQUEST point, not an asset point. It lives in the Request table." }
  };
  const fig = document.getElementById("lampFig");
  function show(k) {
    ["pBase", "pHead", "pReport"].forEach(id => document.getElementById(id).setAttribute("aria-pressed", "false"));
    document.getElementById({ base: "pBase", head: "pHead", report: "pReport" }[k]).setAttribute("aria-pressed", "true");
    renderLamp(fig, k);
    document.getElementById("lpFor").textContent = info[k].f; document.getElementById("lpGood").textContent = info[k].g; document.getElementById("lpBad").textContent = info[k].b;
    const st = document.getElementById("lampStatus"); st.className = "status-line " + (k === "base" ? "ok" : "q"); st.textContent = info[k].s;
  }
  document.getElementById("pBase").addEventListener("click", () => show("base"));
  document.getElementById("pHead").addEventListener("click", () => show("head"));
  document.getElementById("pReport").addEventListener("click", () => show("report"));
  show("base");
  renderGrid(document.getElementById("p1Fig"), { requests: true, sel: "SL-0113", caption: "P1 (dot) and SL-0113 (square, highlighted) sit 7.07 m apart in Ward A. Made-up data." });
  document.getElementById("p1d").textContent = Math.sqrt(50).toFixed(2);
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
