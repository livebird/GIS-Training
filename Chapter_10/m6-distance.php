<?php $page = ['title' => '10.6 Distance and dimension, carefully', 'chapter' => 10, 'module' => '10.6']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 10.6 · General idea; Esri, PostGIS, QGIS and REST behaviour quoted</div>
    <h1>“Nearest” is not “within a distance” — and metres are not degrees</h1>
    <p class="lead">Both questions use distance; they are not the same question. <strong>Within a distance</strong> is a yes/no test per pair. <strong>Nearest</strong> is a ranking that returns one winner — or a tie. Each needs four things written down before you run it: the unit, whether the threshold is inclusive, the search limit, and the tie rule.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Compute nearest-asset distances by hand and see why P6 is 200 m from the road, not 0 m.</li>
      <li>Say what a “within 300 m” query does with the two requests at <em>exactly</em> 300 m — and why the documentation does not settle it.</li>
      <li>Explain the tie rule Esri documents (random), the one QGIS does not document, and why that breaks reproducibility.</li>
      <li>Show with Chapter 6’s numbers why a threshold in degrees is an ellipse on the ground, and why a Z column does not make a query 3D.</li></ul></div>
  </div>

  <h2><span class="mod">10.6.1</span>Two different questions</h2>
  <div class="table-wrap"><table>
    <thead><tr><th></th><th>Within a distance</th><th>Nearest</th></tr></thead>
    <tbody>
      <tr><td>Type of answer</td><td>Yes/no <em>per pair</em> — a predicate like those in 10.4</td><td>A <em>ranking</em>: for each input, which candidate is closest</td></tr>
      <tr><td>Matches per input</td><td>0, 1 or many</td><td>Exactly 1 — or 0 with a search limit — or <em>ambiguous</em> on a tie</td></tr>
      <tr><td>Needs a threshold?</td><td>Yes: the distance and its unit</td><td>Optional: a search limit beyond which “nothing is near”</td></tr>
      <tr><td>Needs a tie rule?</td><td>No</td><td><strong>Yes</strong></td></tr>
      <tr><td>Typical use</td><td>“All requests within 300 m of a road”</td><td>“Which drain does this blocked-drain report probably refer to?”</td></tr>
    </tbody></table></div>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">1. The unit</h4><p>Metres on our grid. On real data it is the <em>layer’s CRS unit</em> unless the tool says otherwise. PostGIS: “the distance is specified in units defined by the spatial reference system of the geometries”. Esri’s Near writes the distance “in the linear unit of the input feature’s coordinate system, or meters when the Method parameter is set to Geodesic”. The REST API’s <code>distance</code> takes a <code>units</code> value whose <em>default differs by product</em>: “esriSRUnit_Foot when querying feature services in ArcGIS Enterprise, and esriSRUnit_Meter when querying feature services in ArcGIS Online”. Leave <code>units</code> out and you get feet on one deployment and metres on the other.</p></div>
    <div class="card"><h4 style="margin-top:0">2. Inclusive or not?</h4><p>P1 and P2 sit at <em>exactly</em> 300 m from R1. “Within 300 m” means either {P1, P2, P3, P5, P6} (≤ 300) or {P3, P5, P6} (&lt; 300). The chapter uses the inclusive reading. <strong>The documentation does not settle it for every tool</strong>: PostGIS says <code>ST_DWithin</code> “returns true if the geometries are within a given distance”; Esri says “within the specified distance”. Treat the exact-threshold case as something to <em>test</em> in your engine, and in production avoid depending on it (use 300.5 or 299.5 if the rule allows, and say so).</p></div>
    <div class="card"><h4 style="margin-top:0">3. The search limit</h4><p>Esri’s Near sets <code>NEAR_FID</code> and <code>NEAR_DIST</code> to −1 “if no feature is found within the search radius”; Spatial Join’s <em>Closest</em> writes −1 when nothing is within the radius; QGIS’s <em>Join attributes by nearest</em> has a <em>Maximum distance</em> — “only features which are closer than this distance will be matched”. Without a limit, “nearest” always finds <em>something</em>, however far.</p></div>
    <div class="card"><h4 style="margin-top:0">4. The tie rule</h4><p>Esri is explicit and honest: “When more than one near feature has the same shortest distance from an input feature, one of them is randomly chosen as the nearest feature.” Spatial Join’s <em>Closest</em> says the same. QGIS’s nearest-join page states no tie rule at all. A nearest result with an undocumented or random tie rule is <strong>not reproducible</strong> — Chapter 9’s traceability rule fails. Fix: fetch all equal-distance candidates and apply a written tie-break (lowest ID, earliest date), or report the tie.</p></div>
  </div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Within a distance of the road — slide the threshold</h3>
    <div class="controls">
      <label>Distance <input type="range" id="band" min="0" max="500" step="10" value="300"> <span class="mono" id="bandV">300 m</span></label>
      <label><input type="radio" name="incl" value="incl" checked> inclusive (≤)</label><label><input type="radio" name="incl" value="strict"> strict (&lt;)</label>
    </div>
    <figure class="map-fig" id="bandFig"></figure>
    <div class="result" id="bandOut"></div>
  </div>
  <p><strong>How distance to a line is measured.</strong> Esri’s rule matches school geometry: “The shortest distance from a point to a line segment is the perpendicular to the line segment. If a perpendicular cannot be drawn within the end vertices of the line segment, the distance to the closest end vertex is the shortest distance.” That is why P6 at (2200, 500) is <strong>200 m</strong> from R1, not 0: the perpendicular would land at (2200, 500), which is <em>past</em> the road’s end at (2000, 500), so the distance is measured to the end point. An engine that treated R1 as an endless line would report 0 — and a report “requests on Main Road” would gain a request 200 m past where the road stops. Distance to a polygon is measured to its edge and is zero for anything inside it.</p>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Nearest asset to each request</h3>
    <p>Click a request. The page draws a line to each asset and picks the shortest — plain √(Δx² + Δy²).</p>
    <div class="grid-2">
      <figure class="map-fig" id="nearFig"></figure>
      <div><div class="table-wrap" id="nearTable"></div><div class="result" id="nearOut">Click a request on the map.</div></div>
    </div>
  </div>
  <div class="callout warn"><span class="label">What nearest does not establish</span><p>P2 (a pothole) is nearest to drain DR-0042, 349 m away. That is a geometric fact. “This report is about that asset” is a <em>business</em> link — Chapter 8 modelled it as a nullable <code>asset_id</code> set by a person. Chapter 9’s rule stands: proximity is evidence, not identity.</p></div>
  <div class="card">
    <h4 style="margin-top:0">A tie you already own</h4>
    <p>“Assign each request to its nearest ward.” P1–P4 are inside a ward (distance 0 to it, more to the other). P6 is 200 m from Ward B’s east edge and 1,200 m from Ward A → B. <strong>P5 is on both boundaries: 0 m to A and 0 m to B — a tie.</strong> Under Esri’s documented rule the tool picks A or B “randomly”; a second run may differ. Notice also that “nearest ward” has quietly assigned P6, which is <em>outside every ward</em>, to B — a nearest query has no idea of “none of the above” unless you give it a search limit.</p>
  </div>
  <div class="quiz" data-answer="2" data-fb="P9 (600, 500): to SL-0113 √(395² + 305²) ≈ 499.05 m; to DR-0042 √(395² + 10²) ≈ 395.13 m; to TR-0301 ≈ 1,590 m. Nearest is DR-0042 at 395.13 m. A within-400 m query returns DR-0042 only, under both readings — 395.13 is neither at nor beyond the threshold.">
    <div class="q">A new request P9 is reported at (600, 500), on the road. Which asset is nearest, how far, and what does “within 400 m” return (inclusive vs strict)?</div>
    <div class="opts"><button class="opt">SL-0113, 395 m; within 400 m: SL-0113 and DR-0042</button><button class="opt">DR-0042, 349.46 m; within 400 m: DR-0042 (inclusive) or none (strict)</button><button class="opt">DR-0042, 395.13 m; within 400 m: DR-0042 under both readings</button><button class="opt">TR-0301, 1,590 m; within 400 m: none</button></div><div class="fb"></div>
  </div>

  <h2><span class="mod">10.6.2</span>Metres are not degrees — use Chapter 6 before you set a threshold</h2>
  <p>Everything above was on the flat grid, where a metre is a metre in every direction. The moment the coordinates are latitude and longitude, a distance threshold “in layer units” is a threshold in <strong>degrees</strong>. Chapter 6’s Fixture E6 gives the numbers: at about 23° N, 0.010° of <em>latitude</em> is about 1,107 m but 0.010° of <em>longitude</em> is about 1,025 m.</p>
  <div class="grid-2">
    <figure class="map-fig" id="degFig"></figure>
    <div>
      <div class="tiles">
        <div class="tile bad"><div class="k">A “0.003°” search radius reaches</div><div class="v">332 m north–south<br>307 m east–west</div><div class="s">an ellipse on the ground, and both numbers are wrong for a 300 m rule</div></div>
        <div class="tile bad"><div class="k">Q1 → Q3 “in degrees × 111 km”</div><div class="v">≈ 649 m</div><div class="s">√(0.003² + 0.005²) = 0.00583°, multiplied by the equator figure</div></div>
        <div class="tile good"><div class="k">Q1 → Q3 planar on the UTM 43N coordinates</div><div class="v">≈ 610.6 m</div><div class="s">√(512.389² + 332.108²) — the degree figure is 6 % out, and direction-dependent</div></div>
      </div>
    </div>
  </div>
  <div class="table-wrap"><table>
    <thead><tr><th>Option</th><th>How the query is written</th><th>When it is right</th></tr></thead>
    <tbody>
      <tr><td><strong>Project first, then planar</strong></td><td>Transform (not relabel — Chapter 6) both layers into a projected CRS in metres, then the ordinary test: <code>ST_DWithin(geom_32643, road_32643, 300)</code>; ArcGIS <em>Within a distance</em>; QGIS <em>Select within distance</em></td><td>Study area inside the CRS’s area of use and distortion acceptable — true for Fixture E6 in EPSG:32643</td></tr>
      <tr><td><strong>Geodesic on the ellipsoid</strong></td><td>PostGIS <code>geography</code>: <code>ST_DWithin(geog, geog, 300)</code> — “units are in meters”, spheroid by default; ArcGIS <em>Within a distance geodesic</em>, “a geodesic formula that takes into account the curvature of the spheroid”; Near with <em>Method</em> = Geodesic</td><td>Large extents, data kept in degrees, or rules stated as ground distance</td></tr>
      <tr><td><strong>Threshold in degrees</strong></td><td><code>ST_DWithin(geom_4326, road_4326, 0.0027)</code></td><td class="no">Not appropriate for a metre rule</td></tr>
    </tbody></table></div>
  <div class="callout note"><span class="label">Why PostGIS prefers ST_DWithin</span><p>It “includes a bounding box comparison that makes use of any indexes”, whereas <code>ST_Distance(a, b) &lt;= x</code> computes the distance for every row. On the geography type the intersection test uses a tolerance of about 0.00001 m and a <em>sphere</em>; the distance itself uses the spheroid by default (<code>use_spheroid = false</code> for the faster sphere) — Chapter 6 measured that difference on Q1–Q2: 664.46 m geodesic versus 667.17 m spherical.</p></div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“The layer is in WGS 84, so I’ll set the distance to 300 and pick metres from the unit list.”</em> Whether the tool converts, warns, or applies 300 <em>degrees</em> depends on the product; Esri’s 3D page notes that on geographic data “the conversion from decimal degrees to linear units is not consistent across large geographic extents” and recommends a projected CRS. Do not find out on production data.</p></div>
  <div class="quiz" data-answer="0" data-fb="0.010° of longitude ≈ 1,025 m here, so 0.005° ≈ 512.5 m (the delivered UTM eastings differ by 512.389 m). A “within 0.005°” query centred on the boundary reaches Q4 exactly at its threshold (inclusive reading); a “within 500 m” geodesic query does not, because 512 m > 500 m.">
    <div class="q">Fixture E6’s Q4 is 0.005° east of the shared boundary at 75.000° E, at about 23° N. Ground distance, and why would “within 0.005°” find Q4 while “within 500 m” (geodesic) would not?</div>
    <div class="opts"><button class="opt">About 512 m; the degree threshold is longer on the ground than 500 m in the east–west direction here</button><button class="opt">About 555 m; 1° is always 111 km</button><button class="opt">About 500 m; both queries find it</button><button class="opt">About 460 m; neither query finds it</button></div><div class="fb"></div>
  </div>

  <h2><span class="mod">10.6.3</span>2D or 3D? A Z column does not make a query three-dimensional</h2>
  <p>Chapter 9’s bridge over a road: from above, two lines cross; in reality one passes over the other and they never meet. <strong>The default is 2D.</strong> PostGIS shows it with a worked example — for a point at Z = 2 and a line whose Z runs 1 to 3 through the same x, y, <code>ST_3DIntersects</code> is <strong>false</strong> while <code>ST_Intersects</code> is <strong>true</strong>; and <code>ST_DWithin</code> is 2D (“Use ST_3DDWithin for 3D geometries”). ArcGIS Pro offers <em>Intersect</em> and <em>Within a distance</em> (2D) beside separate <em>Intersect 3D</em> and <em>Within a distance 3D</em> options; its 3D page shows stacked rooms being selected by a 2D distance “in the x- and y-coordinates only”, floors above and below included.</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Question</th><th>2D predicate says</th><th>3D predicate says</th><th>Which one the business wants</th></tr></thead>
    <tbody>
      <tr><td>“Does the bridge cross the road?” (map drawing order)</td><td class="yes">yes</td><td class="no">no</td><td>2D</td></tr>
      <tr><td>“Can a vehicle turn from the bridge onto the road here?”</td><td class="no">yes — wrongly</td><td>no (and correctly no connection)</td><td>Neither alone — a road network model, out of scope in Phase 1</td></tr>
      <tr><td>“Is this lamp within 5 m of the overhead line?”</td><td>yes if x, y within 5 m</td><td>only if the 3D separation ≤ 5 m</td><td>3D, <em>if</em> Z is real and referenced</td></tr>
    </tbody></table></div>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Horizontal 4 m, vertical 12 m — “within 5 m”?</h3>
    <div class="controls"><label>Horizontal gap <input type="range" id="hg" min="0" max="20" value="4"> <span class="mono" id="hgV">4 m</span></label><label>Vertical gap <input type="range" id="vg" min="0" max="20" value="12"> <span class="mono" id="vgV">12 m</span></label></div>
    <div class="tiles" id="zTiles"></div>
    <div class="result">Storing Z on the bridge and the road changes <em>nothing</em> about <code>ST_Intersects</code> or <em>Intersect</em>: the 2D test never reads Z. To get “they do not meet” you need Z on <em>both</em> features, in a stated vertical reference and unit (Chapter 5), and a 3D predicate — and then you accept that a 3D test on a road at ground level and a bridge at deck level says <em>false</em> even where the bridge’s pier stands on the road, because nobody drew the pier.</div>
  </div>
  <div class="quiz" data-answer="1" data-fb="With Z on both: ST_Intersects true (same x, y), ST_3DIntersects false (6 m apart). With the drain’s Z stored as 0 by an import: ST_Intersects still true; ST_3DIntersects false (0 vs 6) — numerically the same, but the 0 was fabricated. The data defect is the substituted zero; any 3D result built on it is not evidence.">
    <div class="q">Drain DR-0042 is at ground level; a footbridge deck passes directly above it at 6 m. Both carry Z. What do <code>ST_Intersects</code> and <code>ST_3DIntersects</code> return — and what changes if an import stored the drain’s missing Z as 0?</div>
    <div class="opts"><button class="opt">false and false; nothing changes</button><button class="opt">true and false; with Z = 0 the numbers may come out the same, but the zero is a data defect, not an observation</button><button class="opt">true and true; Z is ignored everywhere</button><button class="opt">false and true; 3D is the default</button></div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  // band demo
  const band = document.getElementById("band");
  function updBand() {
    const d = +band.value, incl = document.querySelector("input[name=incl]:checked").value === "incl";
    document.getElementById("bandV").textContent = d + " m";
    const hit = FIXTURE.requests.filter(r => { const x = distToRoad(r.x, r.y); return incl ? x <= d + 1e-9 : x < d - 1e-9; }).map(r => r.id);
    const exact = FIXTURE.requests.filter(r => Math.abs(distToRoad(r.x, r.y) - d) < 1e-9).map(r => r.id);
    renderGrid(document.getElementById("bandFig"), { band: d, hit, dim: FIXTURE.requests.map(r => r.id).filter(i => !hit.includes(i)), caption: `Green band = every point within ${d} m of the finite road R1 (note the round ends). Green points qualify.` });
    document.getElementById("bandOut").innerHTML = `Within ${d} m (${incl ? "≤, inclusive" : "<, strict"}): <strong class="ids">${hit.join(", ") || "none"}</strong> (${hit.length} of 6).` + (exact.length ? ` <strong>${exact.join(" and ")} ${exact.length > 1 ? "sit" : "sits"} at exactly ${d} m</strong> — ${incl ? "included by the inclusive reading" : "excluded by the strict reading"}. This is the case the documentation does not settle; test it in your engine.` : "") + ` Distances: ${FIXTURE.requests.map(r => `${r.id} ${fmt(distToRoad(r.x, r.y), 0)} m`).join(", ")}.`;
  }
  band.addEventListener("input", updBand); document.querySelectorAll("input[name=incl]").forEach(r => r.addEventListener("change", updBand)); updBand();
  // nearest demo
  let sel = "P5";
  function updNear() {
    const q = FIXTURE.requests.find(r => r.id === sel);
    const ds = FIXTURE.assets.map(a => ({ id: a.id, d: Math.hypot(a.x - q.x, a.y - q.y), a }));
    const best = ds.reduce((m, x) => x.d < m.d ? x : m);
    renderGrid(document.getElementById("nearFig"), { layers: { wards: true, roads: true, requests: true, assets: true }, selected: sel, onSelect: id => { sel = id; updNear(); }, extra: (svg, mk) => ds.forEach(x => mk("line", { x1: SX(q.x), y1: SY(q.y), x2: SX(x.a.x), y2: SY(x.a.y), class: "nearline" + (x.id === best.id ? " best" : "") })), caption: "Dashed lines: distance to each asset. Solid green: the nearest. Click another request." });
    document.getElementById("nearTable").innerHTML = `<table class="attr"><thead><tr><th>Asset</th><th>Working</th><th>Distance</th></tr></thead><tbody>${ds.map(x => `<tr class="${x.id === best.id ? "sel" : ""}"><td class="mono">${x.id}</td><td class="mono">√(${Math.abs(x.a.x - q.x)}² + ${Math.abs(x.a.y - q.y)}²)</td><td class="mono">${x.d.toFixed(2)} m</td></tr>`).join("")}</tbody></table>`;
    document.getElementById("nearOut").innerHTML = `<strong>${sel}</strong> → nearest asset <strong class="ids">${best.id}</strong> at <strong>${best.d.toFixed(2)} m</strong>. ${sel === "P5" ? "Blocked drain, 11 m from a drain — plausible, but still only proximity." : sel === "P2" ? "A pothole 349 m from a drain: nearest, and meaningless as a link." : ""}`;
  }
  updNear();
  // degrees ellipse
  (function () {
    const svg = newSvg(document.getElementById("degFig"), "0 0 900 520", "Schematic: a circle of 300 m on the ground versus the ellipse a 0.003-degree radius covers at 23 degrees north.");
    const cx = 450, cy = 250, s = 0.55; // 1 m = 0.55 units
    mkEl("circle", { cx, cy, r: 300 * s, class: "circle-true" }, svg);
    mkEl("ellipse", { cx, cy, rx: 307 * s, ry: 332 * s, class: "ellipse" }, svg);
    txt(svg, cx, cy - 300 * s - 14, "300 m circle (what the rule means)", "tag", { "text-anchor": "middle" });
    txt(svg, cx + 307 * s + 10, cy + 8, "307 m", "tag warn"); txt(svg, cx, cy + 332 * s + 34, "332 m", "tag warn", { "text-anchor": "middle" });
    txt(svg, 20, 500, "“0.003°” radius at 23° N: an ellipse, not a circle. Schematic; proportions from Chapter 6.", "tag");
  })();
  // 3D tiles
  const hg = document.getElementById("hg"), vg = document.getElementById("vg");
  function updZ() { const h = +hg.value, v = +vg.value, d3 = Math.hypot(h, v); document.getElementById("hgV").textContent = h + " m"; document.getElementById("vgV").textContent = v + " m"; document.getElementById("zTiles").innerHTML = `<div class="tile ${h <= 5 ? "bad" : "okish"}"><div class="k">2D test (x, y only)</div><div class="v">${h <= 5 ? "within 5 m" : "not within"}</div><div class="s">horizontal gap ${h} m</div></div><div class="tile ${d3 <= 5 ? "good" : "okish"}"><div class="k">3D test</div><div class="v">${d3 <= 5 ? "within 5 m" : "not within"}</div><div class="s">√(${h}² + ${v}²) = ${d3.toFixed(1)} m</div></div>`; }
  [hg, vg].forEach(e => e.addEventListener("input", updZ)); updZ();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
