<?php $page = ['title' => '10.4 Spatial predicates, drawn first', 'chapter' => 10, 'module' => '10.4']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 10.4 · General idea (OGC Simple Features), product names come later</div>
    <h1>Six ways two shapes can relate — drawn before they are named</h1>
    <p class="lead">A <strong>spatial predicate</strong> is a yes/no test on the <em>shapes</em> of two features: does this point lie in that ward, does this road cross that ward, is this drain near that request. It is the map version of the attribute condition from 10.2. Every product has these tests under slightly different names — so learn the <em>pictures</em> first.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Name and sketch intersects, disjoint, contains/within, touches, overlaps and nearest on the practice grid.</li>
      <li>Separate the <em>question</em> “do they intersect?” from the <em>operation</em> that builds the shared shape (Chapter 11).</li>
      <li>Explain why a bounding box can only say “maybe”, using a concave shape.</li></ul></div>
  </div>

  <h2><span class="mod">10.4.1</span>Interior, boundary, exterior — then the six tests</h2>
  <p>Every shape has three parts. The <strong>interior</strong>: the inside of a polygon; a line itself (minus its two ends); a point itself. The <strong>boundary</strong>: a polygon’s rings (outer <em>and</em> any holes); a line’s two end points; <em>a point has no boundary at all</em>. The <strong>exterior</strong>: everything else. Each named test is just a statement about which parts of shape A meet which parts of shape B. (Esri puts the boundary rule the same way: “the boundary of a line is defined as its end points, and the boundary of a point is always empty.”)</p>
  <div class="sixpanel" id="six"></div>
  <p class="small">Each panel shows Ward A (blue square) and one other feature from the practice grid. Green = the test is true for that pair. Definitions are the ones quoted in the chapter document from the PostGIS manual and the QGIS user guide.</p>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">Touches</h4><p>What Ward A and Ward B do to each other: they share an edge and nothing else. It is also what P5 does to <em>each</em> ward — P5 is on the boundary of A and on the boundary of B, in the interior of neither. PostGIS: “A and B have at least one point in common, and the common points lie in at least one boundary … For Point/Point inputs the relationship is always FALSE, since points do not have a boundary.”</p></div>
    <div class="card"><h4 style="margin-top:0">Overlaps is picky</h4><p>Same dimension, interiors meet, and <em>neither</em> covers the other. A point can never overlap a polygon (different dimensions), and a polygon fully inside another does not overlap it. If your instinct says “P1 overlaps Ward A”, the word you want is <strong>within</strong>.</p></div>
  </div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Fill in the truth table for five pairs</h3>
    <p>For each pair, decide which of the five yes/no tests are true. Press <strong>Show answers</strong> to compare.</p>
    <div class="table-wrap"><table class="predtab" id="pairTable"></table></div>
    <div class="controls"><button class="btn primary" id="showPairs">Show answers</button></div>
    <div class="result">Check: in every row exactly one of <em>intersects</em> / <em>disjoint</em> is true — they are opposites. What the table does <em>not</em> tell you is how a particular <em>tool</em> labels these; 10.5 shows that Esri’s “Within” and PostGIS’s <code>ST_Within</code> disagree on P5.</div>
  </div>
  <div class="callout dev"><span class="label">Developer view</span><p>Predicates are pure boolean functions <code>f(geomA, geomB) → bool</code> with no side effects; <code>intersects</code>, <code>disjoint</code>, <code>touches</code>, <code>overlaps</code> are symmetric, and <code>contains</code>/<code>within</code> are mirror images. Where the analogy stops: the arguments are <em>sets of points</em>, and set rules — interior versus boundary — decide the edge cases in a way an <code>equals()</code> on two objects never would.</p></div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“Contains and intersects are the same for points — a point is either in the polygon or not.”</em> The boundary case is forgotten. P5 <em>intersects</em> both wards but is <em>contained</em> by neither under the strict rule — so it either disappears from every ward report or appears in two.</p></div>
  <div class="quiz" data-answer="2" data-fb="Intersects true, disjoint false, contains false (one part of the park lies in the other ward), touches false (interiors overlap), overlaps TRUE — same dimension, interiors meet, neither covers the other. The deciding fact: contains applies to the whole multipart feature; a feature with any part outside is not contained.">
    <div class="q">Chapter 3’s park PK-01 is one multipart feature with one piece inside Ward A and one inside Ward B. For the pair (Ward A, PK-01), which tests are true?</div>
    <div class="opts"><button class="opt">intersects and contains</button><button class="opt">intersects only</button><button class="opt">intersects and overlaps — contains is false because part of the park is outside A</button><button class="opt">touches and overlaps</button></div><div class="fb"></div>
  </div>

  <h2><span class="mod">10.4.2</span>“Intersects?” is a question; “intersection” is a construction</h2>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">Predicate: <em>intersects</em></h4><p><strong>Input:</strong> two shapes. <strong>Output:</strong> <code>true</code> / <code>false</code>. <strong>Changes data?</strong> No.<br>On the grid: “Does R1 intersect Ward A?” → <strong>true</strong>.<br>Lives in: <code>ST_Intersects</code>; Select By Location <em>Intersect</em>; REST <code>spatialRel=esriSpatialRelIntersects</code>.</p></div>
    <div class="card"><h4 style="margin-top:0">Operation: <em>intersection</em></h4><p><strong>Input:</strong> two shapes (or two layers). <strong>Output:</strong> a <strong>new shape</strong> — the shared part. <strong>Changes data?</strong> Creates new features.<br>On the grid: “What part of R1 lies in Ward A?” → the segment from (0, 500) to (1000, 500), 1,000 m long.<br>Lives in: <code>ST_Intersection</code>; the <em>Intersect</em> and <em>Clip</em> tools — <strong>Chapter 11</strong>.</p></div>
  </div>
  <figure class="map-fig" id="opFig"></figure>
  <p>This chapter uses only the left-hand column. When 10.7 asks “which requests are inside the wards”, it wants a list of IDs, not a new layer of clipped shapes. The moment a task needs <em>the shape of the overlap</em> — the length of road inside each ward, the area of a park in Ward B — it has become an overlay, and that is Chapter 11.</p>
  <div class="quiz" data-answer="0" data-fb="Ask whether the output has new shapes (features cut to the overlap, with new areas/lengths) or the original request rows highlighted/extended, and which tool was used. Seven rows from a predicate-based join would be a one-to-many spatial join keeping all targets (P5 twice, P6 unmatched). An overlay of six points with two wards would give six output points (P5 split into two) and drop P6 — so “seven” itself points at a join, not an overlay.">
    <div class="q">A colleague says “I ran an intersect of requests and wards and got seven rows.” What do you ask, and what does “seven” suggest?</div>
    <div class="opts"><button class="opt">“Are the output rows new shapes or the original requests?” and “which tool?” — seven suggests a one-to-many spatial join (P5 twice, P6 unmatched), not an overlay.</button><button class="opt">Nothing — seven is right because there are six requests plus one road.</button><button class="opt">“Did you use metres or degrees?” — seven means a CRS problem.</button><button class="opt">“Which ward has seven requests?”</button></div><div class="fb"></div>
  </div>

  <h2><span class="mod">10.4.3</span>Bounding boxes find candidates; they do not prove anything</h2>
  <p>Every feature has a <strong>bounding box</strong> (its extent, Chapter 3): the smallest upright rectangle that contains it. Boxes are cheap to compare, so engines test boxes <em>first</em> to throw away pairs that cannot possibly meet. PostGIS says of <code>ST_Intersects</code>, <code>ST_Contains</code> and friends that each “automatically includes a bounding box comparison that makes use of any spatial indexes”; the exact test runs only on pairs whose boxes overlap. The REST API even exposes the box-only test as its own option, <code>esriSpatialRelEnvelopeIntersects</code>, beside the exact <code>esriSpatialRelIntersects</code>.</p>
  <p>The box step is <em>necessary but not sufficient</em>, and a <strong>concave</strong> shape shows why.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>The L-shaped yard</h3>
    <p>Yard Y has corners (0, 0), (300, 0), (300, 100), (100, 100), (100, 300), (0, 300). Its bounding box is 0–300 × 0–300. Type a point and compare the two tests.</p>
    <div class="grid-2">
      <figure class="map-fig" id="lFig"></figure>
      <div>
        <div class="controls"><label>x <input type="number" id="lx" value="200" min="-50" max="350" step="10" style="width:90px"></label><label>y <input type="number" id="ly" value="200" min="-50" max="350" step="10" style="width:90px"></label> <button class="btn small" data-p="200,200">K (200, 200)</button> <button class="btn small" data-p="50,50">(50, 50)</button> <button class="btn small" data-p="250,250">(250, 250)</button> <button class="btn small" data-p="150,100">(150, 100) on the edge</button></div>
        <div class="tiles" id="lTiles"></div>
        <div class="result" id="lOut"></div>
      </div>
    </div>
  </div>
  <p>The yard occupies the strip 0 ≤ y ≤ 100 for all x in 0–300, and the strip 0 ≤ x ≤ 100 for y in 100–300. Point K (200, 200) has y &gt; 100 and x &gt; 100, so it is in neither strip: <strong>outside Y, inside the box</strong>. The box said “maybe”; the shape said “no”. On the practice grid’s <em>convex</em> square wards the box and the shape coincide, which is why the boundary cases (10.5), not box cases, are the interesting ones there.</p>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“Its extent overlaps the ward, so it’s in the ward.”</em> Chapter 3’s park PK-01 has an extent from x = 100 to 1500 — a box that covers a kilometre of ground that is not park. Anything in the gap between its two parts “intersects the park’s extent” and is not in the park at all. <code>EnvelopeIntersects</code> and <code>IndexIntersects</code> are performance tools for a developer who will apply the exact test afterwards. Never report their result as a spatial answer.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  // six panels, each a mini SVG of the grid
  const panels = [
    { t: "Intersects", d: "Do they share any point? Road R1 passes through Ward A → true.", draw: (svg, mk) => {} , road: true, req: [] },
    { t: "Disjoint", d: "Do they share no point? P6 at (2200, 500) and Ward A → true.", req: ["P6"] },
    { t: "Contains / within", d: "Is every point of B inside A, with some of B in A’s interior? Ward A contains P1 → true.", req: ["P1"] },
    { t: "Touches", d: "Do they meet only at boundaries, with no interior in common? Ward A and Ward B along x = 1000 → true.", wardB: true },
    { t: "Overlaps", d: "Same dimension, interiors meet, neither covers the other? Ward A and an old Ward A shifted 300 m east → true.", ghost: true },
    { t: "Nearest", d: "Not yes/no on one pair but a ranking over many: which asset is closest to P2? DR-0042 at 349.46 m.", req: ["P2"], assets: true }
  ];
  document.getElementById("six").innerHTML = panels.map((p, i) => `<div class="pnl"><figure class="map-fig" id="p${i}" style="margin:0;padding:.2rem"></figure><h4>${i + 1}. ${p.t}</h4><p>${p.d}</p></div>`).join("");
  panels.forEach((p, i) => {
    const el = document.getElementById("p" + i);
    const svg = mkEl("svg", { viewBox: "-60 40 2400 1140", role: "img", "aria-label": p.t });
    const mk = (tag, a) => mkEl(tag, a, svg);
    mk("polygon", { points: ptsStr(FIXTURE.wards[0].pts), class: "ward" });
    txt(svg, SX(500), SY(120), "A", "ward-label", { "text-anchor": "middle" });
    if (p.wardB) { mk("polygon", { points: ptsStr(FIXTURE.wards[1].pts), class: "ward lit" }); txt(svg, SX(1500), SY(120), "B", "ward-label", { "text-anchor": "middle" }); mk("line", { x1: SX(1000), y1: SY(0), x2: SX(1000), y2: SY(1000), stroke: "#2f7d4f", "stroke-width": 18 }); }
    if (p.ghost) { mk("polygon", { points: ptsStr([[300, 0], [1300, 0], [1300, 1000], [300, 1000]]), class: "ward lit", style: "fill-opacity:.6" }); mk("rect", { x: SX(300), y: SY(1000), width: 700, height: 1000, fill: "rgba(47,125,79,.35)" }); txt(svg, SX(650), SY(500), "shared", "tag", { "text-anchor": "middle" }); }
    if (p.road) { mk("line", { x1: SX(0), y1: SY(500), x2: SX(2000), y2: SY(500), class: "road" }); mk("line", { x1: SX(0), y1: SY(500), x2: SX(1000), y2: SY(500), stroke: "#2f7d4f", "stroke-width": 26 }); }
    if (p.assets) FIXTURE.assets.forEach(a => { mk("rect", { x: SX(a.x) - 18, y: SY(a.y) - 18, width: 36, height: 36, class: "asset", transform: `rotate(45 ${SX(a.x)} ${SY(a.y)})` }); });
    if (p.assets) { const p2 = FIXTURE.requests[1]; FIXTURE.assets.forEach(a => mk("line", { x1: SX(p2.x), y1: SY(p2.y), x2: SX(a.x), y2: SY(a.y), class: "nearline" + (a.id === "DR-0042" ? " best" : "") })); }
    (p.req || []).forEach(id => { const q = FIXTURE.requests.find(r => r.id === id); mk("circle", { cx: SX(q.x), cy: SY(q.y), r: 34, class: "req hit" }); txt(svg, SX(q.x) + 44, SY(q.y) - 30, q.id, "req-label"); });
    el.innerHTML = ""; el.appendChild(svg);
  });
  // pair table
  const pairs = [
    ["Ward A, P1", [true, false, true, false, false], "P1 is inside A"],
    ["Ward A, P6", [false, true, false, false, false], "P6 is far outside"],
    ["Ward A, Ward B", [true, false, false, true, false], "share an edge; no interior in common"],
    ["Ward A, Road R1", [true, false, false, false, false], "R1 continues beyond A, so A does not contain it; dimensions differ, so no overlap"],
    ["Road R1, P5", [true, false, true, false, false], "P5 is on the line, not at an end point — a line contains such a point"]
  ];
  const heads = ["intersects", "disjoint", "A contains B", "touches", "overlaps"];
  const pt = document.getElementById("pairTable");
  pt.innerHTML = `<thead><tr><th>Pair (A, B)</th>${heads.map(h => `<th>${h}</th>`).join("")}<th>Why</th></tr></thead><tbody>${pairs.map((p, i) => `<tr>${[`<td class="mono">${p[0]}</td>`].concat(heads.map((h, j) => `<td><select data-i="${i}" data-j="${j}"><option value="">?</option><option value="1">true</option><option value="0">false</option></select></td>`)).join("")}<td class="small" style="display:none">${p[2]}</td></tr>`).join("")}</tbody>`;
  document.getElementById("showPairs").addEventListener("click", () => {
    pt.querySelectorAll("select").forEach(s => { const want = pairs[+s.dataset.i][1][+s.dataset.j]; const td = s.parentElement; td.innerHTML = `${want ? "true" : "false"}${s.value === "" ? "" : (s.value === "1") === want ? " ✓" : " ✗"}`; td.className = want ? "yes" : "no"; });
    pt.querySelectorAll("td.small").forEach(td => td.style.display = "");
  });
  // predicate vs operation figure
  renderGrid(document.getElementById("opFig"), { layers: { wards: true, roads: true, requests: false }, extra: (svg, mk) => { mk("line", { x1: SX(0), y1: SY(500), x2: SX(1000), y2: SY(500), stroke: "#2f7d4f", "stroke-width": 30 }); txt(svg, SX(500), SY(430), "intersection of R1 and Ward A: a 1,000 m segment (Chapter 11 builds this)", "note-text", { "text-anchor": "middle" }); txt(svg, SX(1500), SY(430), "intersects? → true (this chapter)", "note-text", { "text-anchor": "middle" }); }, caption: "Predicate: one word, true. Operation: one new shape. Same two inputs." });
  // L yard
  const lx = document.getElementById("lx"), ly = document.getElementById("ly");
  function updL() {
    const p = [+lx.value, +ly.value]; renderLYard(document.getElementById("lFig"), p);
    const box = inBox(p, bboxOf(LYARD)), pos = classify(p, LYARD), exact = pos === "interior" || pos === "boundary";
    document.getElementById("lTiles").innerHTML = `<div class="tile ${box ? "okish" : "bad"}"><div class="k">Box test</div><div class="v">${box ? "candidate" : "no"}</div><div class="s">0 ≤ x ≤ 300 and 0 ≤ y ≤ 300?</div></div><div class="tile ${exact ? "good" : "bad"}"><div class="k">Exact test (in or on Y)</div><div class="v">${exact ? "yes" : "no"}</div><div class="s">position: ${pos}</div></div>`;
    document.getElementById("lOut").innerHTML = box && !exact ? "<strong>The box says maybe; the shape says no.</strong> This point is in the notch — the region where a box-only answer is wrong." : !box ? "Outside the box, so the exact test is never even run — that is the saving the box provides." : pos === "boundary" ? "On the yard’s edge: in the box, and the exact test says <em>on the boundary</em> — 10.5 is about exactly this case." : "In the box <em>and</em> in the yard. The box was right this time — but only the exact test proved it.";
  }
  [lx, ly].forEach(e => e.addEventListener("input", updL));
  document.querySelectorAll("[data-p]").forEach(b => b.addEventListener("click", () => { const [x, y] = b.dataset.p.split(",").map(Number); lx.value = x; ly.value = y; updL(); }));
  updL();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
