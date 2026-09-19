<?php $page = ['title' => '10.5 Boundary semantics, explicitly', 'chapter' => 10, 'module' => '10.5']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 10.5 · PostGIS as the named example; ArcGIS Pro and QGIS checked separately</div>
    <h1>Inside, on the wall, in the hole, outside — what each product says</h1>
    <p class="lead">A point sitting <em>exactly</em> on a boundary is the single most common source of “the software is wrong” complaints — and the software is almost never wrong. It is applying a definition. This module makes you predict the answer for four positions <em>before</em> any tool runs, then shows that PostGIS, QGIS and ArcGIS Pro use the same word for different rules.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Predict, for a point inside / on the ring / in a hole / outside, what <code>ST_Contains</code>, <code>ST_Covers</code>, <code>ST_Intersects</code> and <code>ST_Touches</code> return.</li>
      <li>State the documented difference between <code>ST_Contains</code> and <code>ST_Covers</code> and why the PostGIS authors recommend the second.</li>
      <li>Map those rules onto ArcGIS Pro’s <em>Within</em> / <em>Completely within</em> / <em>Within Clementini</em> and QGIS’s <em>are within</em> / <em>touch</em> / <em>intersect</em> — from their own documentation.</li></ul></div>
  </div>

  <h2><span class="mod">10.5.1</span>Four positions, predicted first</h2>
  <p>Take Chapter 3’s depot DP-01: an outer wall from (1400, 600) to (1600, 800) with a courtyard hole from (1450, 650) to (1550, 750). The courtyard is <strong>not</strong> depot land — a hole is <em>outside</em> the polygon, exactly like the street. But the courtyard’s wall <em>is</em> part of the polygon’s boundary.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Drag the point. Watch every predicate update.</h3>
    <div class="depot-demo">
      <div>
        <figure class="map-fig" id="depotFig"></figure>
        <div class="controls"><span class="small">Jump to:</span> <button class="btn small" data-h="H1">H1 inside</button> <button class="btn small" data-h="H2">H2 on outer wall</button> <button class="btn small" data-h="H3">H3 in the hole</button> <button class="btn small" data-h="H4">H4 outside</button> <button class="btn small" data-h="H5">H5 on the hole’s wall</button></div>
        <div class="controls"><label>x <input type="number" id="px" value="1420" step="10" style="width:90px"></label><label>y <input type="number" id="py" value="620" step="10" style="width:90px"></label> <label><input type="checkbox" id="showBox"> show bounding box</label></div>
      </div>
      <div>
        <p style="margin:.2rem 0">Point is: <span class="posbadge" id="posBadge"></span></p>
        <div class="table-wrap"><table class="predtab" id="predTable"></table></div>
        <div class="result" id="predOut"></div>
      </div>
    </div>
    <p class="small">The values are computed from the definitions quoted below (point-in-ring arithmetic on the schematic shapes), not by calling any GIS library — so what you see <em>is</em> the rule. Your installed software must still be tested once on a boundary case; see the verification note further down.</p>
  </div>
  <div class="table-wrap"><table class="predtab">
    <thead><tr><th>Point</th><th>Position</th><th><code>ST_Contains(DP-01, pt)</code></th><th><code>ST_Covers(DP-01, pt)</code></th><th><code>ST_Intersects</code></th><th><code>ST_Touches</code></th></tr></thead>
    <tbody id="fourRows"></tbody></table></div>
  <p>Only the wall rows separate the predicates, and they do it cleanly: <em>contains</em> says no, <em>covers</em> yes, <em>intersects</em> yes, <em>touches</em> yes. H5 gets the same answers as H2 because the courtyard wall is part of the boundary too. H3, in the hole, gets “no” for everything except <em>disjoint</em>.</p>

  <h2><span class="mod">10.5.2</span>PostGIS: <code>ST_Contains</code> versus <code>ST_Covers</code>, in their own words</h2>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0"><code>ST_Contains(A, B)</code></h4><p>“A contains B if and only if all points of B lie inside (i.e. in the interior or boundary of) A … <strong>and the interiors of A and B have at least one point in common</strong>.” Then the subtlety: “polygons and lines do not contain lines and points lying fully in their boundary.”</p></div>
    <div class="card"><h4 style="margin-top:0"><code>ST_Covers(A, B)</code></h4><p>“Returns true if every point in B lies inside (i.e. intersects the interior or boundary of) A.” And the recommendation from the PostGIS manual itself: “Generally this function should be used instead of ST_Contains, since it has a simpler definition which does not have the quirk that ‘geometries do not contain their boundary’.”</p></div>
  </div>
  <p>So for P5 at (1000, 500) on the shared ward line: <code>ST_Contains(WardA, P5)</code> is <strong>false</strong> and <code>ST_Contains(WardB, P5)</code> is <strong>false</strong> — a point’s only point is on the boundary, so it shares nothing with either ward’s <em>interior</em>. <code>ST_Covers(WardA, P5)</code> and <code>ST_Covers(WardB, P5)</code> are both <strong>true</strong>. <code>ST_Within(P5, WardA)</code> is the mirror of <code>ST_Contains</code> (“ST_Within(A,B) = ST_Contains(B,A)”), so it is false too. The manual’s own example says the same with a circle and its ring: a polygon <em>covers</em> its own boundary but does not <em>contain</em> it.</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Business question</th><th>Predicate</th><th>What happens to P5</th></tr></thead>
    <tbody>
      <tr><td>“Requests that are unambiguously inside one ward” (strict)</td><td class="mono">ST_Contains(ward, request)</td><td>In <strong>neither</strong> ward — must be handled by policy</td></tr>
      <tr><td>“Requests a ward can be responsible for, boundary included” (inclusive)</td><td class="mono">ST_Covers(ward, request)</td><td>In <strong>both</strong> wards — must be handled by policy</td></tr>
      <tr><td>“Requests that touch the ward without being inside”</td><td class="mono">ST_Touches(ward, request)</td><td>Touches both — the boundary set, isolated</td></tr>
    </tbody></table></div>
  <p>Neither predicate is “right”. They answer different questions. Whichever you pick, P5 needs a written <strong>policy</strong> (10.7); the predicate only tells you which pile it lands in.</p>
  <div class="callout note"><span class="label">Three PostGIS cautions that transfer to every product</span><p>(1) “Do not use this function with invalid geometries. You will get unexpected results” — Chapter 9’s validity checks come first. (2) <code>ST_Intersects</code> on the <em>geography</em> type “has a distance tolerance of about 0.00001 meters and uses the sphere” — a boundary point can count as intersecting when it is a hundredth of a millimetre off. (3) The geometry predicates test 2D relationships; Z is ignored (10.6.3).</p></div>
  <div class="callout dev"><span class="label">Developer view</span><p><code>ST_Contains</code> is like a test that is closed on the body but <em>open</em> on the boundary; <code>ST_Covers</code> is plain <code>a ≤ x ≤ b</code>. Where the analogy stops: in one dimension “the boundary” is two numbers; in two dimensions it is every ring, holes included, so “on the boundary” is a whole family of positions.</p></div>

  <h2><span class="mod">10.5.3</span>ArcGIS Pro and QGIS — verified from their own pages, not assumed</h2>
  <p><code>ST_Intersects</code> is the most permissive: “Geometries intersect if they have any point in common”, and every other test except <em>disjoint</em> implies it. Boundary contact qualifies. For a <em>point</em>, <code>ST_Intersects(polygon, point)</code> and <code>ST_Covers(polygon, point)</code> always agree. Now the two desktop products, for the case that matters — a point on a polygon boundary:</p>
  <div class="tabs"><button>ArcGIS Pro 3.7</button><button>QGIS 3.44</button><button>Same word, four engines</button></div>
  <div class="tabpanel">
    <p>Select Layer By Location and Spatial Join share one set of relationship options and one explanatory page (“Select By Location graphic examples”). The definitions there:</p>
    <ul>
      <li><strong>Within</strong> — “Selects features in the input feature layer within or contained by features in the selecting features layer.”</li>
      <li><strong>Completely within</strong> — “identical to the Within option except when the feature in the input feature layer intersects the boundary of the feature in the selecting features layer; then it is not selected.”</li>
      <li><strong>Within Clementini</strong> — identical to Within “except when the entirety of the feature in the input feature layer is on the boundary”; and “the boundary of a point is always empty”.</li>
      <li><strong>Contains</strong> — “The selecting features can be inside as well as on the boundary”; <strong>Completely contains</strong> excludes anything that touches the boundary; <strong>Boundary touches</strong> selects boundary contact only.</li>
    </ul>
    <p>The page’s “Select point using polygon” table lists which labelled cases each option picks: <em>Intersect</em> A, C; <em>Within</em> A, C; <em>Completely within</em> A; <em>Within Clementini</em> A; <em>Boundary touches</em> C. The case only <em>Boundary touches</em> isolates (C) is the boundary point — and <em>Intersect</em> and <em>Within</em> select it while <em>Completely within</em> and <em>Within Clementini</em> do not.</p>
    <div class="callout idea"><span class="label">The finding</span><p><strong>Esri’s plain <em>Within</em> is boundary-inclusive — it matches PostGIS <code>ST_CoveredBy</code>, not <code>ST_Within</code>.</strong> The strict, PostGIS-<code>ST_Within</code>-like behaviour is <em>Completely within</em> (or <em>Within Clementini</em> for a point). The same word means different things in the two products, and each product’s documentation is internally consistent.</p></div>
    <div class="table-wrap"><table class="predtab">
      <thead><tr><th>ArcGIS Pro option (requests = input, wards = selecting)</th><th>P1, P2</th><th>P3, P4</th><th>P5</th><th>P6</th></tr></thead>
      <tbody>
        <tr><td>Intersect</td><td>A</td><td>B</td><td class="yes">A and B</td><td>none</td></tr>
        <tr><td>Within</td><td>A</td><td>B</td><td class="yes">A and B</td><td>none</td></tr>
        <tr><td>Completely within</td><td>A</td><td>B</td><td class="no">neither</td><td>none</td></tr>
        <tr><td>Within Clementini</td><td>A</td><td>B</td><td class="no">neither</td><td>none</td></tr>
        <tr><td>Boundary touches</td><td>none</td><td>none</td><td class="yes">A and B</td><td>none</td></tr>
      </tbody></table></div>
    <p class="small">Two more Esri facts change results without changing meaning: the tool “evaluates a spatial relationship in the coordinate system of the Input Features” and warns that “Features that intersect in one coordinate system may not intersect in another”; and it applies the feature class’s <strong>x,y tolerance</strong> on the client. On integer practice coordinates the tolerance does nothing; on real data a point 0.5 mm outside a ward may be <em>inside</em> under it.</p>
  </div>
  <div class="tabpanel">
    <p>Predicates in <em>Select by location</em> / <em>Join attributes by location</em>: <code>intersect</code>, <code>contain</code>, <code>disjoint</code>, <code>equal</code>, <code>touch</code>, <code>overlap</code>, <code>are within</code>, <code>cross</code>; choosing several combines them with OR. The documented definitions: <strong>contain</strong> — “if and only if no points of b lie in the exterior of a, and at least one point of the interior of b lies in the interior of a … This is the opposite of are within”; <strong>touch</strong> — “at least one point in common, but their interiors do not intersect”; <strong>intersect</strong> — “share any portion of space – overlap or touch”.</p>
    <p>The <em>contain</em> wording is the OGC/PostGIS <code>ST_Contains</code> rule word for word, so a boundary point is <em>not</em> contained and therefore not <em>within</em>; it <em>touches</em> and <em>intersects</em>. QGIS lines up with PostGIS, not with Esri’s plain <em>Within</em>. There is no <em>covers</em> option; “in or on” is <em>intersect</em> (for points), or <em>are within</em> OR <em>touch</em>.</p>
  </div>
  <div class="tabpanel">
    <div class="table-wrap"><table class="predtab">
      <thead><tr><th>Meaning (P5 on the ward line)</th><th class="eng">PostGIS</th><th class="eng">QGIS</th><th class="eng">ArcGIS Pro</th><th class="eng">ArcGIS REST spatialRel</th></tr></thead>
      <tbody>
        <tr><td>Any contact</td><td class="yes"><code>ST_Intersects</code> true</td><td class="yes"><em>intersect</em> true</td><td class="yes"><em>Intersect</em> selected</td><td><code>esriSpatialRelIntersects</code> (boundary rule not restated on the query page)</td></tr>
        <tr><td>Strict interior</td><td class="no"><code>ST_Contains</code> / <code>ST_Within</code> false</td><td class="no"><em>contain</em> / <em>are within</em> false</td><td class="no"><em>Completely within</em>, <em>Within Clementini</em> not selected</td><td><code>esriSpatialRelWithin</code> — verify; values are listed without boundary semantics</td></tr>
        <tr><td>Inclusive</td><td class="yes"><code>ST_Covers</code> true</td><td>(no covers option — use <em>intersect</em>)</td><td class="yes"><em>Within</em>, <em>Contains</em> selected</td><td>—</td></tr>
        <tr><td>Boundary only</td><td class="yes"><code>ST_Touches</code> true</td><td class="yes"><em>touch</em> true</td><td class="yes"><em>Boundary touches</em> selected</td><td><code>esriSpatialRelTouches</code> — verify</td></tr>
      </tbody></table></div>
  </div>
  <div class="callout warn"><span class="label">Verification item (both products)</span><p>The statements above are read from documentation, not from executed tests. Before the lab is issued, run the P5 case in the installed ArcGIS Pro with <em>Within</em>, <em>Completely within</em>, <em>Within Clementini</em> and <em>Boundary touches</em>, and in the installed QGIS with <em>are within</em>, <em>touch</em> and <em>intersect</em>; write the selected IDs beside each option. A difference is a finding to document, not to hide.</p></div>
  <div class="callout idea"><span class="label">The rule that transfers</span><p>Never trust the <em>word</em>; trust the <em>documented definition</em> and one <em>executed boundary case</em>. Every platform this course targets has a predicate called something like “within” or “contains”, and at least two of them disagree on a point on a line.</p></div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“P5 is on the line, so the software will put it in one ward or the other at random, and that’s fine.”</em> Under strict predicates it lands in <em>neither</em> and vanishes from every ward count; under inclusive ones it lands in <em>both</em> and is counted twice. Neither outcome is random, and neither is an assignment. Assignment is a policy decision (10.7), in writing.</p></div>
  <div class="quiz" data-answer="1" data-fb="The tree stands in the hole, which is the polygon’s exterior: ST_Covers false, ArcGIS Within not selected, ArcGIS Intersect not selected, QGIS are within false. An engine that tested only the outer ring would report the tree as inside — wrongly, because a hole is not part of the polygon.">
    <div class="q">Chapter 3’s tree TR-0302 stands in the depot courtyard at (1500, 700). For the pair (depot DP-01, tree): PostGIS <code>ST_Covers</code>, ArcGIS Pro <em>Within</em>, ArcGIS Pro <em>Intersect</em>, QGIS <em>are within</em>?</div>
    <div class="opts"><button class="opt">true, selected, selected, true — the tree is inside the outer wall</button><button class="opt">false, not selected, not selected, false — the hole is exterior; only an engine that ignored the hole would say otherwise</button><button class="opt">false, selected, selected, true — products differ on holes</button><button class="opt">true for Covers and Intersect only</button></div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const px = document.getElementById("px"), py = document.getElementById("py"), box = document.getElementById("showBox");
  let dep;
  const words = { interior: "inside the depot land (interior)", boundary: "on a wall (boundary)", hole: "in the courtyard — a hole is exterior", exterior: "outside (exterior)" };
  function update(x, y) {
    px.value = x; py.value = y;
    const pos = classify([x, y], DEPOT.outer, [DEPOT.hole]);
    const b = document.getElementById("posBadge"); b.textContent = words[pos]; b.className = "posbadge " + pos;
    const cols = engineColumns(pos);
    const groups = ["PostGIS", "ArcGIS Pro", "QGIS"];
    document.getElementById("predTable").innerHTML = groups.map(g => `<tr><th class="eng">${g}</th>${cols.filter(c => c.eng === g).map(c => `<td class="${c.v ? "yes" : "no"}"><span class="mono">${c.name}</span><br>${c.v ? "true / selected" : "false / not selected"}</td>`).join("")}</tr>`).join("");
    const p = predicates(pos);
    document.getElementById("predOut").innerHTML = pos === "boundary" ? "<strong>This is the position where the words split.</strong> Contains / Completely within / are within say <em>no</em>; Covers / Within (Esri) / Intersect say <em>yes</em>; Touches says <em>yes</em>." : pos === "hole" ? "<strong>Everything says no.</strong> The courtyard is not depot land. An engine that only tested the outer wall would wrongly say yes." : pos === "interior" ? "Inside: every membership test says yes; <em>touches</em> says no because the point is not on a wall." : "Outside: every test says no; only <em>disjoint</em> would be true.";
    if (box) { const bb = bboxOf(DEPOT.outer); if (document.getElementById("showBox").checked && inBox([x, y], bb) && !p.covers) document.getElementById("predOut").innerHTML += " <em>Inside the bounding box, though</em> — the box says “candidate”, the shape says no (10.4.3)."; }
  }
  function draw() { dep = renderDepot(document.getElementById("depotFig"), { probe: [+px.value, +py.value], showBox: box.checked, onMove: update, caption: "Depot DP-01 (Chapter 3), zoomed in. Drag anywhere. Made-up practice geometry; metres." }); }
  draw(); update(+px.value, +py.value);
  [px, py].forEach(e => e.addEventListener("input", () => { dep.setProbe(+px.value, +py.value); update(+px.value, +py.value); }));
  box.addEventListener("change", () => { draw(); update(+px.value, +py.value); });
  document.querySelectorAll("[data-h]").forEach(b => b.addEventListener("click", () => { const h = DEPOT.probes.find(q => q.id === b.dataset.h); dep.setProbe(h.x, h.y); update(h.x, h.y); }));
  document.getElementById("fourRows").innerHTML = DEPOT.probes.map(h => { const p = predicates(classify([h.x, h.y], DEPOT.outer, [DEPOT.hole])); return `<tr><td class="mono">${h.id} (${h.x}, ${h.y})</td><td>${h.label}</td>${tf(p.contains)}${tf(p.covers)}${tf(p.intersects)}${tf(p.touches)}</tr>`; }).join("");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
