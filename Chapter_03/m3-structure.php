<?php $page = ['title' => '3.3 Parts, holes, and file formats', 'chapter' => 3, 'module' => '3.3']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 3.3 · General idea + format rules</div>
    <h1>Shapes in pieces, shapes with holes, and how files write them</h1>
    <p class="lead">Most shapes are simple: one dot, one path, one ring. Two things break that simplicity — a feature in <strong>several pieces</strong>, and an area with a <strong>hole</strong>. Both are common, both trip up counting and “inside” questions, and different file formats write them with different rules.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Meet a park stored as <strong>one feature in two pieces</strong> and a depot with a <strong>courtyard hole</strong>, and predict what they do to counts, areas and “inside” tests.</li>
      <li>See why a line that happens to close is <strong>not</strong> a polygon.</li>
      <li>Read the same depot written as <strong>WKT</strong>, <strong>GeoJSON</strong> and <strong>Esri JSON</strong> — and spot the rule the last two disagree on.</li></ul></div>
  </div>

  <h2><span class="mod">3.3.1</span>One feature, two pieces — and an area with a hole</h2>
  <p><strong>Riverside Park PK-01</strong> is one park — one name, one budget, one <code>park_id</code> — that happens to be two separate patches of ground, one in each ward. It is stored as <strong>one row whose shape has two parts</strong>. GIS people call this a <em>multipart</em> feature; the GeoJSON and PostGIS word is <em>MultiPolygon</em>. (Think of Andaman &amp; Nicobar: many islands, one union territory, one row in a states table.)</p>
  <p><strong>Central depot DP-01</strong> is a walled compound with an open courtyard in the middle. The courtyard is not depot land, so it is a <strong>hole</strong>: a polygon has one <em>outer ring</em> and any number of <em>inner rings</em> that cut area out. Tree TR-0302 stands in the courtyard.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Click a shape for its numbers — then click anywhere on the map to test “is this point inside the depot?”</h3>
    <figure class="map-fig" id="sFig"></figure>
    <div class="tiles" id="sTiles"></div><div class="result" id="sOut">Click the park, the depot, or the tree in the courtyard. Or click any spot on the map — for example inside the courtyard, next to the tree.</div>
  </div>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">What the two-part park does</h4>
      <ul><li><strong>Count:</strong> the parks dataset has a feature count of <strong>1</strong>, not 2.</li>
      <li><strong>Area:</strong> the one feature’s area is the sum of its parts: 20,000 + 20,000 = <strong>40,000 m²</strong>. “Park area in Ward A” cannot be read from the row — it needs the Ward A part only.</li>
      <li><strong>Facts:</strong> one row, so one <code>condition</code> for both pieces. If part 2 floods and part 1 is fine, the row cannot say so.</li>
      <li><strong>Extent:</strong> the feature’s bounding box spans x = 100 … 1500 — a kilometre of gap that is not park (module 3.6).</li></ul></div>
    <div class="card"><h4 style="margin-top:0">What the hole does</h4>
      <ul><li><strong>Area</strong> = outer − hole: 40,000 − 10,000 = <strong>30,000 m²</strong>.</li>
      <li><strong>Inside?</strong> Tree TR-0302 at (1500, 700) is inside the outer ring but inside the hole, so it is <strong>not inside DP-01</strong>. A query “assets inside the depot” must leave it out.</li>
      <li><strong>A hole is not a feature.</strong> Nothing in the depot dataset represents “the courtyard”. If the courtyard is itself managed (a garden), it needs its own row in a suitable dataset.</li></ul></div>
  </div>
  <h3>One multipart row, or several rows?</h3>
  <div class="table-wrap"><table>
    <thead><tr><th>Store as one multipart feature when…</th><th>Store as separate features when…</th></tr></thead>
    <tbody>
      <tr><td>The pieces are managed, named and reported as one unit</td><td>The pieces have different facts (condition, owner, opening hours)</td></tr>
      <tr><td>The facts are the same for all pieces by definition</td><td>You need to count, select or edit pieces one by one</td></tr>
      <tr><td>The system that consumes the data expects one ID per unit</td><td>“In which ward?” must have one answer per piece</td></tr>
    </tbody></table></div>
  <p>Both are legitimate; the wrong choice is the <em>unrecorded</em> one. Software converts either way: ArcGIS Pro’s <em>Multipart To Singlepart</em> tool splits a feature into one row per part, keeps the facts on every row, and adds an <code>ORIG_FID</code> column holding the original row’s ID; QGIS has <em>Multipart to singleparts</em> and, for the reverse, <em>Collect geometries</em>.</p>
  <div class="callout warn"><span class="label">Misconception</span><p>“Two shapes with the same name must be a duplicate row.” A developer “de-duplicates” the parks by deleting the second row — and finds there was only ever one row. Always check the <strong>feature count and the part count</strong> before touching anything.</p></div>
  <div class="quiz" data-answer="1" data-fb="Two single-part rows (one per ward) make the monthly per-ward area trivial: 20,000 m² each. What you lose is the “one park = one row” identity — park_id would now repeat, so you need a rule for unique IDs (Chapter 8), and any park-level fact must be kept consistent across two rows.">
    <div class="q">The office must report “park area per ward” every month. Which storage choice makes that simplest?</div>
    <div class="opts">
      <button class="opt">Keep one multipart row and read its area field</button>
      <button class="opt">Store one single-part row per piece, one in each ward</button>
      <button class="opt">Store the park as a point at its centre</button>
    </div><div class="fb"></div>
  </div>

  <h2><span class="mod">3.3.2</span>A ring is not a closed line</h2>
  <p>The bus loop L1 and the depot’s outer ring look alike on paper: both are vertex lists that end where they start. They are <strong>different kinds of shape</strong>. Switch L1’s storage below and watch what the software can and cannot say about it.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <div class="controls"><span>Store L1 as:</span> <button class="btn small" id="asLine" aria-pressed="true">a line</button> <button class="btn small" id="asPoly">a polygon</button></div>
    <div class="two-col">
      <figure class="map-fig" id="l1Fig"></figure>
      <div class="result" id="l1Out"></div>
    </div>
  </div>
  <p>The closed shape of L1 is a <strong>coincidence of the path</strong>, not a statement that the asphalt inside belongs to anything. Buses <em>drive along</em> it: the question is length and connection. If the office also needs “the area inside the loop” (a traffic island to plant), that is a <em>separate polygon feature</em>. Closing the list is <em>necessary</em> for an area, not <em>sufficient</em> — the list must also be <em>stored as a polygon</em>.</p>
  <div class="callout note"><span class="label">Validity rules come later</span><p>May a ring touch itself? May a hole touch the outer ring? What happens when a ring is left open or written in the wrong direction? These are <em>validation</em> questions for Chapter 9. ArcGIS Pro’s <em>Check Geometry</em> tool reports problems with names like “Unclosed rings”, “Incorrect ring ordering”, “Self intersections” and “Null geometry” — read the names now only to recognise the vocabulary.</p></div>

  <h2><span class="mod">3.3.3</span>The same depot in three written forms</h2>
  <p>You will meet three ways of writing a shape. Read them; do not worry about coordinate systems yet (Chapter 5).</p>
  <div class="tabs">
    <button>WKT</button><button>GeoJSON</button><button>Esri JSON</button>
  </div>
  <div class="tabpanel">
    <p><strong>Well-Known Text</strong> — the OGC text form used by PostGIS, GeoPackage tools and many libraries. Coordinates are <code>x y</code> pairs; rings are bracketed lists; the first ring is the shell, later rings are holes. WKT carries no statement about the coordinate system in the text itself — which is why the lab data package uses it.</p>
<pre class="code"><span class="k">POLYGON</span>((1400 600, 1600 600, 1600 800, 1400 800, 1400 600),
        (1450 650, 1550 650, 1550 750, 1450 750, 1450 650))

<span class="k">MULTIPOLYGON</span>(((100 50, 300 50, 300 150, 100 150, 100 50)),
             ((1300 50, 1500 50, 1500 150, 1300 150, 1300 50)))</pre>
    <p class="small">The PostGIS manual section on this does not impose a direction of travel around a ring. Do not assume one without checking the engine’s validity rules.</p>
  </div>
  <div class="tabpanel">
    <p><strong>GeoJSON (RFC 7946)</strong> — the JSON form used on the web. Shown here for its <em>structure only</em>.</p>
<pre class="code">{
  <span class="n">"type"</span>: <span class="k">"Feature"</span>,
  <span class="n">"id"</span>: "DP-01",
  <span class="n">"geometry"</span>: {
    <span class="n">"type"</span>: <span class="k">"Polygon"</span>,
    <span class="n">"coordinates"</span>: [
      [[1400, 600], [1600, 600], [1600, 800], [1400, 800], [1400, 600]],   <span class="c">← outer ring, counter-clockwise</span>
      [[1450, 650], [1450, 750], [1550, 750], [1550, 650], [1450, 650]]    <span class="c">← hole, clockwise</span>
    ]
  },
  <span class="n">"properties"</span>: { "depot_id": "DP-01", "name": "Central depot" }
}</pre>
    <div class="callout warn"><span class="label">Do not save this as a .geojson file</span><p>RFC 7946 fixes the coordinate system of <em>every</em> GeoJSON file to WGS 84 longitude and latitude in <strong>decimal degrees</strong>. Our numbers are metres on a practice grid; 1400 is not a longitude. A web library would draw nothing, or plot the town in the middle of the ocean. Chapter 5 explains why; Chapter 7 shows a valid file.</p></div>
    <ol>
      <li><strong>Position:</strong> each <code>[1400, 600]</code> is an array — “longitude and latitude, or easting and northing, <em>precisely in that order</em>”. That is: the x-like number first, then the y-like number. Same order as our grid.</li>
      <li><strong>Ring:</strong> “a closed LineString with four or more positions”; first and last “MUST contain identical values”.</li>
      <li><strong>Direction:</strong> “exterior rings are counterclockwise, and holes are clockwise”. Walk the outer ring: right, up, left, down — counter-clockwise on a grid with y upward. The hole goes up, right, down, left — clockwise.</li>
      <li><strong>Order:</strong> the first ring MUST be the exterior; any others are holes.</li>
      <li><strong>Feature:</strong> <code>"geometry"</code> is a shape <em>or</em> <code>null</code> for an “unlocated” feature — complaint P7 would be written that way. <code>"properties"</code> holds the facts; <code>"id"</code> is optional.</li>
      <li><strong>Collection:</strong> a <code>FeatureCollection</code> is an array of features — what a whole layer looks like when exported.</li>
    </ol>
  </div>
  <div class="tabpanel">
    <p><strong>Esri JSON</strong> — used by ArcGIS services and SDKs. A polygon is a <code>rings</code> array; first point equals last; but here <strong>exterior rings are clockwise and holes counter-clockwise</strong> — the <em>opposite</em> of GeoJSON.</p>
<pre class="code">{
  <span class="n">"rings"</span>: [
    [[1400, 600], [1400, 800], [1600, 800], [1600, 600], [1400, 600]],   <span class="c">← outer ring, clockwise</span>
    [[1450, 650], [1550, 650], [1550, 750], [1450, 750], [1450, 650]]    <span class="c">← hole, counter-clockwise</span>
  ]
}</pre>
    <p>A line is a <code>paths</code> array (any number of paths, so a line in pieces needs no separate type). A point is <code>{"x": …, "y": …}</code> with optional <code>z</code> and <code>m</code>; a point is <em>empty</em> when <code>x</code> is <code>null</code>.</p>
  </div>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Walk the outer ring in each format’s direction</h3>
    <div class="controls"><button class="btn small" data-f="geojson">GeoJSON direction</button> <button class="btn small" data-f="esri">Esri JSON direction</button></div>
    <figure class="map-fig" id="ringFig" style="max-width:560px;margin:1rem auto"></figure>
    <div class="result" id="ringOut">Pick a format. The arrows show which way the vertex list travels around the outer ring and around the hole.</div>
  </div>
  <h3>Same ideas, three vocabularies</h3>
  <div class="table-wrap"><table>
    <thead><tr><th>Idea</th><th>WKT / PostGIS</th><th>GeoJSON</th><th>Esri</th></tr></thead>
    <tbody>
      <tr><td>One point</td><td><code>POINT</code></td><td><code>Point</code></td><td>point</td></tr>
      <tr><td>A connected path</td><td><code>LINESTRING</code></td><td><code>LineString</code></td><td>polyline with one path</td></tr>
      <tr><td>Several paths, one row</td><td><code>MULTILINESTRING</code></td><td><code>MultiLineString</code></td><td>polyline with several paths</td></tr>
      <tr><td>One area, maybe with holes</td><td><code>POLYGON</code></td><td><code>Polygon</code></td><td>polygon with rings</td></tr>
      <tr><td>Several areas, one row</td><td><code>MULTIPOLYGON</code></td><td><code>MultiPolygon</code></td><td>polygon with several outer rings (multipart)</td></tr>
      <tr><td>Outer ring direction</td><td>not stated in the section read</td><td>counter-clockwise</td><td>clockwise</td></tr>
      <tr><td>No shape</td><td><code>POINT EMPTY</code></td><td><code>"geometry": null</code></td><td>empty point / empty paths</td></tr>
      <tr><td>Extra numbers per vertex</td><td>Z, M, ZM</td><td>Z only</td><td>z, m with <code>hasZ</code> / <code>hasM</code></td></tr>
    </tbody></table></div>
  <div class="callout dev"><span class="label">Developer view</span><p>Three serialisations of one data model — like JSON, XML and Protocol Buffers for one message type. <strong>Where the comparison stops:</strong> the models are <em>not quite</em> identical (GeoJSON has no M; ring directions differ; Esri has curves and multipoints), so a round-trip is lossy in specific, documentable ways. Chapter 7 makes those checks explicit.</p></div>
  <div class="quiz" data-answer="1" data-fb="RFC 7946 §3.1.6 uses MUST: exterior rings counter-clockwise, holes clockwise. Esri JSON is the other way round — a file that is right for one is wrong for the other.">
    <div class="q">A GeoJSON Polygon has two rings. The first runs clockwise, the second counter-clockwise. Under RFC 7946 this is…</div>
    <div class="opts">
      <button class="opt">Fine — ring order decides which is the hole; direction is only advice</button>
      <button class="opt">Wrong — exterior rings MUST be counter-clockwise and holes clockwise</button>
      <button class="opt">Fine — GeoJSON copied Esri’s clockwise rule</button>
    </div><div class="fb"></div>
  </div>

  <div class="callout note"><span class="label">Comprehension check (write it down)</span><p>Write Ward B’s ring — (1000, 0), (2000, 0), (2000, 1000), (1000, 1000), back to (1000, 0) — as it would appear (i) inside a GeoJSON <code>Polygon</code> and (ii) inside an Esri JSON <code>rings</code> array, respecting each format’s direction rule. Say which way you walked.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  /* 3.3.1 explorer with probe */
  const fig = document.getElementById("sFig");
  let selected = null, probe = null;
  function draw() {
    const svg = renderTown(fig, { visible: ["wards", "park", "depot", "assets"], wardStyle: "outline", selected, onSelect: (layer, id) => { selected = { layer, id }; probe = null; draw(); describe(layer, id); },
      extra: mk => { if (probe) { mk("circle", { cx: SX(probe[0]), cy: SY(probe[1]), r: 20, class: "probe" }); const t = mk("text", { x: SX(probe[0]) + 26, y: SY(probe[1]) - 20, class: "vlabel" }); t.textContent = `(${probe[0]}, ${probe[1]})`; } },
      caption: "Outline wards, the two-part park, the depot with its hatched courtyard hole, and the four assets. Click a shape, or click any empty spot." });
    svg.addEventListener("click", ev => {
      const pt = svg.createSVGPoint(); pt.x = ev.clientX; pt.y = ev.clientY;
      const p = pt.matrixTransform(svg.getScreenCTM().inverse());
      probe = [Math.round(p.x), Math.round(1100 - p.y)]; selected = null; draw(); testProbe();
    });
  }
  function describe(layer, id) {
    const T = document.getElementById("sTiles"), O = document.getElementById("sOut");
    if (layer === "park") { T.innerHTML = tile(1, "feature count") + tile(2, "parts") + tile("40,000 m²", "area (20,000 + 20,000)"); O.innerHTML = "<strong>PK-01</strong> is <em>one row</em>. Its two rectangles are one shape value. Part 1 lies in Ward A, part 2 in Ward B — the row cannot say which part is in which ward."; }
    else if (layer === "depot") { T.innerHTML = tile(1, "feature count") + tile(2, "rings (1 outer + 1 hole)") + tile("30,000 m²", "area (40,000 − 10,000)"); O.innerHTML = "<strong>DP-01</strong>: the hatched square is the courtyard — a hole. The area inside the hole is <em>not</em> depot."; }
    else if (layer === "assets" && id === "TR-0302") { T.innerHTML = tile("in", "outer ring?") + tile("in", "hole?") + tile("NO", "inside DP-01?", "warn"); O.innerHTML = "<strong>TR-0302</strong> at (1500, 700) is inside the outer ring <em>and</em> inside the hole → <strong>not inside the depot</strong>. An engine that tested only the outer ring would wrongly include it."; }
    else if (layer === "assets") { T.innerHTML = tile("out", "inside DP-01?"); O.innerHTML = `<strong>${id}</strong> is outside the depot’s outer ring.`; }
    else { T.innerHTML = ""; O.innerHTML = `Ward ${id}: a plain single-ring polygon, 1,000,000 m².`; }
  }
  function testProbe() {
    const p = probe, inOuter = inRing(p, TOWN.depot.outer), inHole = inRing(p, TOWN.depot.hole);
    const inPark = TOWN.park.parts.some(r => inRing(p, r));
    const inside = inOuter && !inHole;
    document.getElementById("sTiles").innerHTML = tile(inOuter ? "yes" : "no", "inside outer ring?") + tile(inHole ? "yes" : "no", "inside hole?") + tile(inside ? "YES" : "NO", "inside DP-01?", inside ? "ok" : "warn") + tile(inPark ? "yes" : "no", "inside PK-01?");
    document.getElementById("sOut").innerHTML = `Test point (${p[0]}, ${p[1]}): ` + (inside ? "inside the depot polygon (in the outer ring, not in the hole)." : inOuter ? "<strong>in the courtyard</strong> — inside the outer ring but inside the hole, so <strong>not</strong> inside DP-01." : "outside the depot’s outer ring.") + (inPark ? " It is also inside one part of the park — one feature, whichever part you hit." : "");
  }
  const tile = (v, l, cls = "") => `<div class="tile ${cls}"><div class="v">${v}</div><div class="l">${l}</div></div>`;
  draw();

  /* 3.3.2 L1 as line vs polygon */
  const L1 = TOWN.roads[2].pts;
  function drawL1(asPoly) {
    document.getElementById("asLine").setAttribute("aria-pressed", !asPoly); document.getElementById("asPoly").setAttribute("aria-pressed", asPoly);
    renderTown(document.getElementById("l1Fig"), { view: [1230, 700, 200, 140], axes: false, labels: false, visible: asPoly ? [] : ["roads"], roadThin: true,
      extra: mk => {
        if (asPoly) mk("polygon", { points: ptsStr(L1.slice(0, -1)), class: "poly" });
        mk("circle", { cx: 1325, cy: SY(325), r: 5, class: "req" }); const t = mk("text", { x: 1332, y: SY(325) + 4, class: "vlabel", style: "font-size:10px" }); t.textContent = "(1325, 325)";
        const h = mk("text", { x: 1240, y: 715, class: "tag", style: "font-size:11px" }); h.textContent = asPoly ? "stored as POLYGON" : "stored as LINE";
      }, caption: "Zoomed in on L1 (1300–1350, 300–350). Same vertex list either way." });
    document.getElementById("l1Out").innerHTML = asPoly
      ? `<div class="tiles">${tile("200 m", "perimeter")}${tile("2,500 m²", "area")}${tile("YES", "is (1325, 325) inside?", "ok")}</div>As a polygon the software reports an area of 2,500 m² and says the centre point is <strong>inside</strong>. But is that true of the world? Only if the loop really encloses something that is “the loop’s land”. For a bus loop it does not.`
      : `<div class="tiles">${tile("200 m", "length")}${tile("—", "area (a line has none)")}${tile("not meaningful", "is (1325, 325) inside?", "warn")}</div>As a line: 4 segments, 200 m. “Inside” has no meaning — the best the software can say is that (1325, 325) is <strong>25 m away</strong> from the nearest segment.`;
  }
  document.getElementById("asLine").addEventListener("click", () => drawL1(false)); document.getElementById("asPoly").addEventListener("click", () => drawL1(true)); drawL1(false);

  /* 3.3.3 ring direction */
  function drawRing(fmt) {
    const outer = fmt === "geojson" ? TOWN.depot.outer : [TOWN.depot.outer[0], TOWN.depot.outer[3], TOWN.depot.outer[2], TOWN.depot.outer[1]];
    const hole = fmt === "geojson" ? [TOWN.depot.hole[0], TOWN.depot.hole[3], TOWN.depot.hole[2], TOWN.depot.hole[1]] : TOWN.depot.hole;
    renderTown(document.getElementById("ringFig"), { view: [1305, 230, 390, 340], axes: false, labels: false, visible: ["depot"],
      extra: mk => {
        const arrows = (ring, cls) => ring.forEach((a, i) => { const b = ring[(i + 1) % ring.length]; const mx = (a[0] + b[0]) / 2, my = (a[1] + b[1]) / 2; mk("path", { d: `M ${SX(a[0])} ${SY(a[1])} L ${SX(mx)} ${SY(my)}`, class: "arrowpath", style: "stroke-width:1.5" }); });
        arrows(outer); arrows(hole);
        outer.forEach((v, i) => { mk("circle", { cx: SX(v[0]), cy: SY(v[1]), r: 4, class: "vertex", style: "stroke-width:2" }); const t = mk("text", { x: SX(v[0]) + (v[0] > 1500 ? 6 : -6), y: SY(v[1]) + (v[1] > 700 ? -7 : 13), class: "vlabel", style: "font-size:8px", "text-anchor": v[0] > 1500 ? "start" : "end" }); t.textContent = `${i + 1}: (${v[0]}, ${v[1]})`; });
        const h = mk("text", { x: 1315, y: 245, class: "tag", style: "font-size:9px" }); h.textContent = fmt === "geojson" ? "GeoJSON: outer counter-clockwise, hole clockwise" : "Esri JSON: outer clockwise, hole counter-clockwise";
      }, caption: "Arrows sit at the middle of each edge and point the way the vertex list travels. Schematic." });
    document.getElementById("ringOut").innerHTML = fmt === "geojson"
      ? "Outer ring: (1400, 600) → right → up → left → down = <strong>counter-clockwise</strong>. Hole: up → right → down → left = <strong>clockwise</strong>. Hand check: the “shoelace” sum for the outer ring is +80,000 (positive = counter-clockwise), and half of it, 40,000 m², is the outer area."
      : "Outer ring: (1400, 600) → up → right → down → left = <strong>clockwise</strong>. Hole: right → up → left → down = <strong>counter-clockwise</strong>. Same corners, opposite travel — a file written for GeoJSON is “wrong” for Esri JSON and vice versa.";
  }
  document.querySelectorAll("[data-f]").forEach(b => b.addEventListener("click", () => { document.querySelectorAll("[data-f]").forEach(x => x.setAttribute("aria-pressed", x === b)); drawRing(b.dataset.f); }));
  drawRing("geojson"); document.querySelector('[data-f="geojson"]').setAttribute("aria-pressed", "true");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
