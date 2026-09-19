<?php $page = ['title' => '9.5 Geometry validity vs topology rules', 'chapter' => 9, 'module' => '9.5']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 9.5 · General idea (OGC rules), with ArcGIS / QGIS / PostGIS names</div>
    <h1>A broken shape is not the same as a broken rule</h1>
    <p class="lead">Two things both get called “geometry errors”. A <strong>valid</strong> shape is one shape that can be understood on its own — an area that does not cross itself. A <strong>topology rule</strong> is about how <em>several</em> shapes sit together — wards do not overlap, roads meet where they should. A ward can be perfectly valid and still break a rule. Knowing which one you have decides who may fix it and how.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Tell an invalid polygon (Ward C crosses itself) from two valid polygons that break a rule (a gap or an overlap between A and B).</li>
      <li>Pick rules from the <em>meaning</em> of a layer: wards, roads, assets.</li>
      <li>Recognise exceptions that look like errors: a dead-end road, an intentional overlap, a bridge.</li></ul></div>
  </div>

  <h2><span class="mod">9.5.1</span>Invalid shape vs valid shapes that break a rule</h2>
  <p><strong>Validity</strong> rules come from the OGC Simple Features standard (PostGIS restates them): a polygon is valid if its rings are <em>simple</em> (do not cross or touch themselves), rings do not cross each other, they may touch only at single points, holes lie inside the outer ring, and the inside stays in one piece. For lines the only rule is “at least two different points” — a line that crosses itself is still valid. Points have no validity rules.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Ward C three ways — watch the area</h3>
    <div class="opbtns">
      <button class="btn small" id="wc0" aria-pressed="true">As typed (corners in the wrong order)</button>
      <button class="btn small" id="wc1">Corrected from the register (rectangle)</button>
      <button class="btn small" id="wc2">What an automatic repair would give</button>
    </div>
    <div class="grid-2">
      <figure class="map-fig" id="wcFig"></figure>
      <div id="wcInfo"></div>
    </div>
  </div>
  <p>Walk the typed ring on graph paper: bottom-left (0, 1000) → bottom-right (1000, 1000) → top-<em>left</em> (0, 1500) → top-right (1000, 1500) → back. The second and fourth edges cross at <strong>(500, 1250)</strong>. The ring is not simple; the polygon is <strong>invalid</strong>. Its “area” is not even defined: the corner formula gives <strong>0</strong>, because the two lobes cancel. Software may report 0, a negative number, or one lobe — all meaningless. Remember the three numbers: <strong>0</strong> (as typed), <strong>500,000 m²</strong> (the register’s rectangle), <strong>250,000 m²</strong> (two triangles from a repair). Module 9.7 uses them.</p>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Two good wards, one broken rule</h3>
    <p>Now Ward B. Each version below is a perfectly <em>valid</em> four-sided shape. Look at how it sits next to Ward A.</p>
    <div class="opbtns">
      <button class="btn small" id="wb0" aria-pressed="true">As delivered: first corner at (1004, 0)</button>
      <button class="btn small" id="wb1">Alternative: first corner at (990, 0)</button>
      <button class="btn small" id="wb2">Corrected: (1000, 0)</button>
    </div>
    <div class="grid-2">
      <figure class="map-fig" id="wbFig"></figure>
      <div id="wbInfo"></div>
    </div>
  </div>
  <div class="table-wrap"><table>
    <thead><tr><th></th><th>Invalid shape (Ward C)</th><th>Rule violation (A/B gap or overlap)</th></tr></thead>
    <tbody>
      <tr><td>How many features</td><td>One</td><td>Two or more</td></tr>
      <tr><td>Who sets the rule</td><td>The geometry standard or the engine</td><td>The organisation’s business rule, written as a topology rule</td></tr>
      <tr><td>Usable as is?</td><td>No — area, “inside”, overlay are undefined</td><td>Each shape yes; the <em>set</em> double-counts or misses ground</td></tr>
      <tr><td>Found by</td><td>Check Geometry (ArcGIS) · Check validity (QGIS) · <code>ST_IsValid</code> (PostGIS)</td><td>Topology rules (ArcGIS) · Topology Checker (QGIS) · spatial queries (PostGIS)</td></tr>
      <tr><td>Typical cause</td><td>Wrong vertex order, an unclosed ring, a stray click</td><td>Two features drawn separately without snapping</td></tr>
      <tr><td>Automatic fix?</td><td>Yes — and it may change the shape (9.7)</td><td>Sometimes — and it always chooses <em>which</em> feature gives way, which is a business decision</td></tr>
    </tbody></table></div>
  <div class="callout note"><span class="label">Platform note — one idea, three vocabularies</span><p><strong>ArcGIS Pro Check Geometry</strong> reports “Self intersections”, “Unclosed rings”, “Incorrect ring ordering”, “Null geometry”, “Duplicate vertex”, “Short segment” and more, with two validation methods: <em>Esri</em> (default; expects outer rings clockwise — the <em>opposite</em> of GeoJSON, so an “incorrectly ordered” ring here can be a perfect GeoJSON ring) and <em>OGC</em>. <strong>QGIS Check validity</strong> offers <em>GEOS</em> and <em>QGIS</em> methods and writes an <code>_errors</code> column; the two methods word the same failure differently. <strong>PostGIS</strong> <code>ST_IsValidDetail</code> returns a reason and a location, e.g. “Self-intersection” at a point. Always write in your log <em>which engine and method</em> reported a problem.</p></div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“The layer draws fine, so the geometry is fine.”</em> The bow-tie Ward C draws as two triangles and looks like a ward. Its area is 0 in one tool and 250,000 m² in another; a “complaints per ward” join drops or double-counts. Run a validity check on every polygon layer at intake; “draws fine” is no evidence at all.</p></div>

  <h2><span class="mod">9.5.2</span>Rules come from the meaning of the layer</h2>
  <p>The software only offers a vocabulary; the organisation chooses the rules. ArcGIS names below; QGIS Topology Checker has the same ideas (“must not have gaps”, “must not overlap”, “must not have dangles”, “must be inside”…), and any of them can be a PostGIS query.</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Layer</th><th>Rule</th><th>Plain meaning</th><th>In our town</th></tr></thead>
    <tbody>
      <tr><td rowspan="2"><strong>Wards</strong> (no overlap, no holes between)</td><td>Must Not Overlap</td><td>Every spot belongs to at most one ward</td><td>The (990, 0) version: a 10 m strip in both wards</td></tr>
      <tr><td>Must Not Have Gaps</td><td>Every spot belongs to at least one ward</td><td>The delivered version: a 2,000 m² sliver nobody owns</td></tr>
      <tr><td rowspan="2"><strong>Roads</strong> (connected where intended)</td><td>Must Not Have Dangles</td><td>A line end must touch another line — Esri’s page itself says dead-end roads are the usual <em>exception</em></td><td>R4’s 3 m undershoot (defect); R5’s end at the depot gate (exception)</td></tr>
      <tr><td>Must Not Intersect</td><td>Lines must not cross each other inside one layer</td><td>A flyover crossing R1 is a legitimate exception (9.5.3)</td></tr>
      <tr><td><strong>Assets</strong> (inside a service area)</td><td>Must Be Properly Inside / Contains Point</td><td>Assets lie in the area the municipality maintains</td><td>TR-0301 at (2190, 520) is outside every ward — defect <em>or</em> exception? (9.8)</td></tr>
    </tbody></table></div>
  <p>“Wards must not overlap” is a hard rule: an overlap is always wrong. “Assets must lie inside a ward” is really a <em>question</em>: an asset outside every ward means the asset is misplaced, or the ward layer is incomplete, or the municipality maintains something beyond its wards. Your log must be able to say “flagged; needs the owner”, not only “error”.</p>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">ArcGIS Pro — geodatabase topology</h4><p>Built on feature classes inside one <em>feature dataset</em>. Validation uses a <strong>cluster tolerance</strong> (default 0.001 m): vertices closer than that “may move slightly”; higher-<em>rank</em> layers move less. Violations are stored as <strong>errors</strong>; “certain errors may be acceptable, in which case the error features can be marked as exceptions”. <strong>Licence: Standard or Advanced</strong> — so the lab’s main route uses Check Geometry plus your own review, and treats topology as optional.</p></div>
    <div class="card"><h4 style="margin-top:0">QGIS 3.44 — Topology Checker</h4><p>A core plugin: enable it under <em>Plugins ▸ Manage and Install Plugins</em>; it appears in the <em>Vector</em> menu. Configure rules per layer, then <strong>Validate All</strong> or <strong>Validate Extent</strong>. Errors appear in a table with type, layer and feature ID, and can be shown on the map. It <em>reports</em>; you fix with the editing tools.</p><p class="small">PostGIS: an overlap is a query — <code>ST_Overlaps(a.geom, b.geom)</code> between pairs of wards (Chapter 10 teaches the predicates).</p></div>
  </div>

  <h2><span class="mod">9.5.3</span>Exceptions: things that look like errors and are not</h2>
  <p>A topology error is a <em>report</em>, not a verdict. For each one ask: <strong>is there evidence that the real world is as the shape says?</strong> If yes, it is an exception — write the evidence down. If there is no evidence either way, the report stays <em>open</em>. Only if the evidence says the shape is wrong do you correct it.</p>
  <div class="grid-3">
    <div class="card"><h4 style="margin-top:0">The dead end</h4><p><strong>R5 Depot Access</strong> ends at the depot gate (1500, 600). The register says “no through route”. Under <em>Must Not Have Dangles</em> its end is a dangle — and Esri’s page names cul-de-sacs as the standard exception. Do <em>not</em> extend it until it touches something. Compare R4: same symptom, 3 m from a road it is documented to join — opposite decision. The difference is the register, not the geometry.</p></div>
    <div class="card"><h4 style="margin-top:0">The intentional overlap</h4><p>Wards must not overlap. But a <em>contractor territory</em> layer where two contractors both cover the market during a handover month overlaps on purpose. Applying the ward rule to that layer turns every overlap into an “error”. Write next to each rule <em>why</em> it holds for <em>this</em> layer.</p></div>
    <div class="card"><h4 style="margin-top:0">The bridge</h4><p>A flyover crosses Main Road at (1800, 500) with no junction. Seen from above the two lines cross; under <em>Must Not Intersect</em> that is an error. In reality they are at different heights. A road layer that must support “can I drive from here to there?” needs a <code>level</code> column or Z values, and the crossing is an exception. <strong>Crossing in the top view does not prove a connection</strong> — see the 3D idea in 9.10.</p></div>
  </div>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Defect or exception?</h3>
    <p>Click a report, then the box it belongs in. Every one of these comes from the practice town.</p>
    <div class="sorter" data-items='[
      {"t":"R4 ends 3 m from Main Road; register says it joins Main Road","bin":"Defect — correct it","why":"evidence says the shape is wrong"},
      {"t":"R5 ends at the depot gate; register says no through route","bin":"Exception — log it","why":"evidence says the world is like this"},
      {"t":"Ward B leaves a 4 m sliver next to Ward A; register says they share the whole boundary","bin":"Defect — correct it","why":"gap contradicts the register"},
      {"t":"Two contractor areas overlap during a handover month","bin":"Exception — log it","why":"the overlap is by contract; the rule belongs to wards, not territories"},
      {"t":"A flyover crosses R1 with no junction","bin":"Exception — log it","why":"a bridge: different heights; add a level field"},
      {"t":"R2’s north end at (800, 900) touches nothing; nothing in the register says either way","bin":"Open — ask the owner","why":"no evidence in either direction"},
      {"t":"TR-0301 sits outside every ward","bin":"Open — ask the owner","why":"the ward layer may be incomplete, or the tree may be outside the town"}
    ]'>
      <div class="items"></div>
      <div class="bins">
        <div class="bin" data-bin="Defect — correct it"><h5>Defect — correct it</h5></div>
        <div class="bin" data-bin="Exception — log it"><h5>Exception — log it</h5></div>
        <div class="bin" data-bin="Open — ask the owner"><h5>Open — ask the owner</h5></div>
      </div>
    </div>
  </div>

  <div class="quiz" data-answer="2" data-fb="(a) invalid — unclosed ring. (b) valid exception — the overlap is by contract. (c) rule violation — a dangle/undershoot with evidence it should join. (d) a self-crossing LINE is valid; whether it breaks a rule needs evidence, so ‘open’. (e) invalid — a hole must be inside the outer ring. (f) valid exception — a bridge.">
    <div class="q">Classify: (a) a ward ring whose last vertex is not the first; (b) two contractor territories that overlap by contract; (c) a road that stops 0.5 m short of the road it is documented to join; (d) a footpath that crosses itself; (e) a polygon whose hole lies outside its outer ring; (f) a flyover crossing a road with no junction. Which line is right?</div>
    <div class="opts">
      <button class="opt">(a) invalid, (b) violation, (c) violation, (d) invalid, (e) invalid, (f) violation</button>
      <button class="opt">(a) violation, (b) exception, (c) invalid, (d) invalid, (e) violation, (f) exception</button>
      <button class="opt">(a) invalid, (b) exception, (c) violation, (d) valid line — rule question open, (e) invalid, (f) exception</button>
      <button class="opt">All six are invalid geometry</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const wcFig = document.getElementById("wcFig"), wcInfo = document.getElementById("wcInfo");
  const wcStates = ["typed", "fixed", "repair"];
  function wc(n) {
    [0, 1, 2].forEach(i => document.getElementById("wc" + i).setAttribute("aria-pressed", i === n));
    renderWardC(wcFig, wcStates[n]);
    if (n === 0) { const cr = ringSelfCrossings(T9.wards[2].ring); wcInfo.innerHTML = `<p class="bigno">${fmt(Math.round(shoelace(T9.wards[2].ring)))} m² <small>corner-formula (shoelace) area of the ring as typed — meaningless</small></p><p>Self-crossing found at <strong class="ids">(${cr[0].p[0]}, ${cr[0].p[1]})</strong>: edges 2 and 4 cross. <strong>Invalid.</strong> Not usable for “which ward is this complaint in?”.</p>`; }
    else if (n === 1) wcInfo.innerHTML = `<p class="bigno">${fmt(shoelace(T9.wardsFixed.C))} m² <small>rectangle 1,000 × 500 m — matches the register’s 0.50 km²</small></p><p>Same four corners, typed in walking order: (0, 1000) → (1000, 1000) → (1000, 1500) → (0, 1500). <strong>Valid, and correct.</strong> This is the fix — done by hand, from evidence.</p>`;
    else wcInfo.innerHTML = `<p class="bigno">${fmt(T9.wardsFixed.Crepair.reduce((a, r) => a + Math.abs(shoelace(r)), 0))} m² <small>two triangles of 125,000 m² touching at (500, 1250)</small></p><p>An automatic repair (QGIS <em>Fix geometries</em> / PostGIS <code>ST_MakeValid</code>, “linework” method) cuts the ring at the crossing and keeps every vertex. <strong>Valid — and wrong.</strong> It is half the real ward, and no tool can know that the vertex <em>order</em>, not the vertices, was the mistake.</p>`;
  }
  [0, 1, 2].forEach(i => document.getElementById("wc" + i).addEventListener("click", () => wc(i))); wc(0);

  const wbFig = document.getElementById("wbFig"), wbInfo = document.getElementById("wbInfo");
  const rings = [[[1004, 0], [2000, 0], [2000, 1000], [1000, 1000]], [[990, 0], [2000, 0], [2000, 1000], [990, 1000]], [[1000, 0], [2000, 0], [2000, 1000], [1000, 1000]]];
  function wb(n) {
    [0, 1, 2].forEach(i => document.getElementById("wb" + i).setAttribute("aria-pressed", i === n));
    const W = 1200, H = 720, pad = 60; const sx = x => pad + (x - 600) * 1.35, sy = y => H - pad - y * 0.6;   // zoomed on the A/B boundary; schematic, not to scale
    let s = `<svg viewBox="0 0 ${W} ${H}" role="img" aria-label="Wards A and B side by side">`;
    s += `<polygon class="ward" points="${T9.wards[0].ring.map(p => sx(p[0]) + "," + sy(p[1])).join(" ")}"/><polygon class="ward" style="fill:rgba(219,231,243,.6)" points="${rings[n].map(p => sx(p[0]) + "," + sy(p[1])).join(" ")}"/>`;
    if (n === 0) s += `<polygon class="gapfill" style="stroke:var(--warn);stroke-width:5" points="${[[1000, 0], [1004, 0], [1000, 1000]].map(p => sx(p[0]) + "," + sy(p[1])).join(" ")}"/><text class="mark-text" x="${sx(1012)}" y="${sy(60)}">4 m gap at the bottom, 0 at the top</text>`;
    if (n === 1) s += `<polygon class="gapfill" style="fill:var(--warn);opacity:.55" points="${[[990, 0], [1000, 0], [1000, 1000], [990, 1000]].map(p => sx(p[0]) + "," + sy(p[1])).join(" ")}"/><text class="mark-text" x="${sx(1010)}" y="${sy(60)}">10 m strip in both wards</text>`;
    s += `<text class="ward-label" x="${sx(760)}" y="${sy(450)}">A</text><text class="ward-label" x="${sx(1200)}" y="${sy(450)}">B</text><circle class="req" cx="${sx(1000)}" cy="${sy(500)}" r="12"/><text class="req-label" x="${sx(1000) + 16}" y="${sy(500) - 12}" style="font-size:28px">P5 (1000, 500)</text></svg>`;
    wbFig.innerHTML = s;
    const area = Math.abs(shoelace(rings[n]));
    const info = [
      `<p class="bigno">${fmt(area)} m² <small>Ward B as delivered — a valid quadrilateral</small></p><p>Gap between A and B: a triangle with base 4 m and height 1,000 m = <strong>2,000 m²</strong> that belongs to no ward. At y = 500 the B edge is at x = 1002, so complaint <strong>P5 (1000, 500) is 2 m outside Ward B</strong> — Chapter 1’s “on the shared boundary” answer silently changed. Rule broken: <em>Must Not Have Gaps</em>. Valid shapes, broken rule.</p>`,
      `<p class="bigno">${fmt(area)} m² <small>Ward B if its west edge were at x = 990 — also valid</small></p><p>Now the strip 990 ≤ x ≤ 1000 is “in” both wards: 10 m × 1,000 m = <strong>10,000 m²</strong> counted twice. Rule broken: <em>Must Not Overlap</em>. Every shape passes the validity check; the pair fails the business rule.</p>`,
      `<p class="bigno">${fmt(area)} m² <small>Ward B corrected — corner back at (1000, 0)</small></p><p>No gap, no overlap; P5 is back on the shared boundary. The evidence for this edit: the register says A and B share their whole boundary, and Ward A’s east edge is at x = 1000.</p>`
    ];
    wbInfo.innerHTML = info[n];
  }
  [0, 1, 2].forEach(i => document.getElementById("wb" + i).addEventListener("click", () => wb(i))); wb(0);
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
