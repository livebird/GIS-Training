<?php $page = ['title' => '11.2 Buffer — draw “within a distance”', 'chapter' => 11, 'module' => '11.2']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 11.2 · General idea, with each product’s wording where it matters</div>
    <h1>Buffer: the shape of “within 300 metres”</h1>
    <p class="lead">A <strong>buffer</strong> is the area made of every point that is within a chosen distance of a feature. Buffer a point and you get a circle. Buffer a road (a line) and you get a band with rounded ends. Buffer a ward (a polygon) and you get a fatter ward. The output is <em>always</em> a polygon.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Name the four settings that change a buffer: distance and its units, end type, planar or geodesic method, and whether overlapping buffers are merged.</li>
      <li>Move a threshold and predict which complaints fall in or out — including the ones sitting <em>exactly</em> on the edge.</li>
      <li>Say clearly what a buffer does <strong>not</strong> mean: it is not a travel time and not a “response zone”.</li></ul></div>
  </div>

  <h2><span class="mod">11.2.1</span>Four settings that change the answer</h2>
  <div class="grid-2">
    <div class="card"><h3 style="margin-top:0">1 · Distance and its units</h3><p>In ArcGIS Pro you can type the distance with a unit (“300 Meters”) or read it from a field. If you type no unit, the tool uses “the linear unit of the input features’ spatial reference”. On our paper grid that is metres by declaration. On a layer in EPSG:32643 it is metres by definition. On a latitude/longitude layer (EPSG:4326) the layer unit is the <strong>degree</strong> — the Chapter 6 trap.</p></div>
    <div class="card"><h3 style="margin-top:0">2 · End type (line buffers)</h3><p>A road is a finite line, so its buffer has two ends. <strong>Round</strong> ends bulge out a half-circle past each end of the road. <strong>Flat</strong> ends stop exactly at the road’s end. ArcGIS Pro calls this <em>End Type</em> (Flat needs an Advanced licence); QGIS calls it <em>End cap style</em>; PostGIS uses <code>endcap=round|flat|square</code>. P6 is 200 m past the east end of R1 — inside a round 300 m buffer, outside a flat one.</p></div>
    <div class="card"><h3 style="margin-top:0">3 · Method: planar or geodesic</h3><p>ArcGIS Pro’s <em>Method</em> has two values. <strong>Planar</strong> (default): “If the input features have a projected coordinate system, Euclidean buffers will be created. If the input features have a geographic coordinate system and you specify a Buffer Distance value in linear units (meters, feet…), geodesic buffers will be created.” <strong>Geodesic</strong>: shape-preserving buffers on the curved Earth whatever the CRS. So in ArcGIS Pro a “300 Meters” buffer on lat/long data is <em>not</em> the degrees mistake — the tool notices the unit. That is ArcGIS-specific; do not assume it elsewhere.</p></div>
    <div class="card"><h3 style="margin-top:0">4 · Dissolve type: keep separate or merge</h3><p>Buffer two roads that meet and their bands overlap. ArcGIS Pro’s <em>Dissolve Type</em>: <strong>None</strong> — “An individual buffer for each feature will be maintained, regardless of overlap”; <strong>All</strong> — one merged polygon; <strong>List</strong> — merge buffers that share values in chosen fields. QGIS has a <em>Dissolve result</em> tick box. To <em>count complaints near any road</em> you want All (no double counting); to say <em>which road</em> each complaint is near you want None.</p></div>
  </div>
  <div class="callout note"><span class="label">Other engines — the units rule is different</span><p>PostGIS <code>ST_Buffer</code> on a <code>geometry</code> column uses the units of that geometry’s spatial reference system: a lat/long geometry buffered by 300 gives a 300-<em>degree</em> polygon, which wraps the world. The <code>geography</code> type measures in metres. QGIS’s core Buffer takes a plain number; the 3.44 documentation does not state the unit, and it is understood to be the layer’s CRS unit — so a lat/long layer would be buffered in degrees (an instructor should confirm this on the installed version). <strong>The safe rule everywhere:</strong> move the data to a suitable metre-based projected CRS first (Chapter 6), then buffer in metres. Part 2 of the lab does exactly that.</p></div>
  <p><strong>Read the output table.</strong> With Dissolve Type None, ArcGIS Pro writes a <code>BUFF_DIST</code> field (the distance used, in the input’s linear unit) and an <code>ORIG_FID</code> that points back to the source feature; neither is written for All or List. <code>BUFF_DIST = 300</code> is your first check that the units were understood as you meant.</p>

  <h2><span class="mod">11.2.2</span>Road R1 buffered by 300 m — and what the polygon does not mean</h2>
  <p><strong>Question.</strong> Draw the 300 m proximity area of Road R1 on the training grid and state its area. <strong>Inputs.</strong> R1 from (0, 500) to (2000, 500), metres, flat geometry; round ends; one road, so dissolve does not matter.</p>
  <p><strong>Reasoning.</strong> The band is a rectangle 2,000 m long and 600 m wide (300 m each side), plus a half-circle of radius 300 m at each end — together one full circle.</p>
  <ul>
    <li>Rectangle: 2000 × 600 = <strong>1,200,000 m²</strong></li>
    <li>Circle: π × 300² = <strong>282,743 m²</strong></li>
    <li>Total: <strong>1,482,743 m²</strong> (about 1.48 km²). A GIS gives slightly less, because it draws the round ends with short straight segments — expect agreement within about 0.5 %.</li>
  </ul>
  <p>With flat ends the area is exactly 1,200,000 m². The round-ended buffer reaches from x = −300 to x = 2300 — it pokes 300 m <em>outside</em> the wards at both ends, which matters in module 11.3.</p>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Change the distance, the rule at the edge, and the end type</h3>
    <div class="controls">
      <label>Distance <input type="range" id="dist" min="0" max="500" step="10" value="300"> <span class="mono" id="distV">300 m</span></label>
      <label><input type="checkbox" id="incl" checked> exactly on the edge counts (≤)</label>
      <label><input type="checkbox" id="round" checked> round ends</label>
      <label><input type="checkbox" id="onlyOpen"> only eligible complaints (OPEN, since 1 Aug)</label>
    </div>
    <figure class="map-fig" id="bufFig"></figure>
    <div class="grid-2">
      <div class="table-wrap"><table><thead><tr><th>Request</th><th>Distance to R1</th><th>Inside?</th></tr></thead><tbody id="bufRows"></tbody></table></div>
      <div class="result" id="bufOut"></div>
    </div>
  </div>

  <div class="callout warn"><span class="label">What the polygon does not mean</span><p>The band is the set of places within 300 m <em>in a straight line</em> of the road’s centre line. It says nothing about whether a crew can <strong>drive</strong> there, how many <strong>minutes</strong> it takes, which side of a canal or railway line a complaint is on, or where the road’s edge is (a centre line has zero width). Writing “complaints within 300 m of R1” is honest. Writing “complaints reachable from R1” or “5-minute response zone” is not supported by anything in the buffer. Travel-time areas need a road network and a routing tool — later material.</p></div>

  <h2><span class="mod">11.2.3</span>Predict before you run: thresholds and overlaps</h2>
  <h3>Change the threshold</h3>
  <p>Using the distances on the chapter home page, predict which complaints are inside a round 300 m buffer at three thresholds (ignore status for now):</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Threshold (edge counts)</th><th>Inside</th><th>Outside</th><th>Note</th></tr></thead>
    <tbody>
      <tr><td>250 m</td><td class="mono">P3 (250), P5 (0), P6 (200)</td><td class="mono">P1, P2 (300), P4 (400)</td><td>P3 sits exactly on the edge</td></tr>
      <tr><td>300 m</td><td class="mono">P1, P2, P3, P5, P6</td><td class="mono">P4</td><td>P1 and P2 sit exactly on the edge — the blueprint’s set</td></tr>
      <tr><td>400 m</td><td class="mono">all six</td><td>—</td><td>P4 sits exactly on the edge</td></tr>
    </tbody></table></div>
  <p>Three thresholds, three cases with a complaint <em>exactly</em> on the edge. That is not a quirk of practice data — real data with rounded coordinates does it constantly. Whether the software sees a point as “on the edge” depends on floating-point rounding and the dataset’s tolerance. The reliable test is the <strong>number</strong>: the Near tool gives P1 a distance of exactly 300, and <code>300 &lt;= 300</code> is true.</p>
  <div class="callout idea"><span class="label">Rule</span><p>For an exact-threshold decision, <strong>compare distances</strong>. Use the buffer polygon to <strong>draw</strong>. PostGIS says the same in its own manual: use <code>ST_DWithin</code> for a within-distance query, not <code>ST_Buffer</code>.</p></div>

  <h3>Overlapping buffers</h3>
  <p>Add School Road R2 from Chapter 3 — (500, 0) → (500, 500) → (800, 900). It meets R1 at (500, 500), so the two 300 m bands overlap around the junction. P1 at (200, 200) is 300 m from R1 <em>and</em> 300 m from R2’s vertical part. Switch between “keep separate” and “merge” and watch the count.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Dissolve type: None or All?</h3>
    <div class="controls">
      <label><input type="radio" name="dis" value="none" checked> None — one polygon per road</label>
      <label><input type="radio" name="dis" value="all"> All — one merged polygon</label>
    </div>
    <figure class="map-fig" id="ovFig"></figure>
    <div class="result" id="ovOut"></div>
  </div>
  <p>Predict the consequences before running: <strong>selecting</strong> complaints that touch the None output and counting them gives each complaint once — a selection is a set. A <strong>spatial join from buffers to complaints, one-to-many</strong>, gives P1 twice (once per band); summing a count column then double-counts. Dissolving (All) removes the duplication but also removes the “which road” information. Choose from the <em>kind of answer</em> you need (11.1.2). This is Chapter 10’s row-inflation lesson, arriving through geometry instead of keys.</p>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“The default Buffer settings are fine — the tool knows the units.”</em> The tool knows the <em>layer’s</em> units, which on an unprojected layer are degrees, and it knows nothing about whether you wanted planar or geodesic. Chapter 6’s decision table applies unchanged.</p></div>

  <div class="quiz" data-answer="1" data-fb="The 200 m buffer on the EPSG:32643 road is in metres (projected input → flat, Euclidean buffer). The 0.002 buffer on the EPSG:4326 copy is in degrees — about 205 m east–west but 221 m north–south at this latitude, so not even the same width in both directions. Had they typed “200 Meters” on the EPSG:4326 copy with Method = Planar, ArcGIS Pro’s documentation says a geodesic buffer would have been created.">
    <div class="q">Quick check 11.2. A colleague buffers the E11 road by 200 m in ArcGIS Pro (Method = Planar) and, separately, by “0.002” (no unit) after reprojecting it to EPSG:4326. Which buffer is in metres?</div>
    <div class="opts"><button class="opt">Both — the tool converts automatically.</button><button class="opt">Only the first; the second is 0.002 degrees, which is a different width east–west and north–south.</button><button class="opt">Only the second — degrees are more precise.</button><button class="opt">Neither; buffers always need geodesic mode.</button></div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const fig = document.getElementById("bufFig");
  function draw() {
    const r = +dist.value, inc = incl.checked, rnd = round.checked, onlyOpen = onlyOpen_.checked;
    distV.textContent = r + " m";
    const list = onlyOpen ? eligible(REQUESTS) : REQUESTS;
    const m = gridMap(fig, { extent: { x1: -600, y1: -200, x2: 2600, y2: 1200 }, caption: `R1 buffered by ${r} m (${rnd ? "round" : "flat"} ends). Green = inside. Yellow = exactly on the edge. Made-up data, metres.` });
    drawWards(m, WARDS.slice(0, 2), "faded"); if (r > 0) drawBuffer(m, ROADS.R1, r, { round: rnd }); drawRoad(m, ROADS.R1);
    const rows = [], inside = [];
    REQUESTS.forEach(q => {
      const shown = list.includes(q);
      const d = distToLine(q.x, q.y, ROADS.R1.pts, !rnd);
      const edge = Math.abs(d - r) < 1e-9, isIn = inc ? d <= r + 1e-9 : d < r - 1e-9;
      q._cls = !shown ? "gone" : edge ? "edge" : isIn ? "in" : "out";
      if (shown && isIn) inside.push(q.id);
      if (shown) rows.push({ cells: [q.id, isFinite(d) ? fmt(d) + " m" : "beyond the flat end", { html: isIn ? (edge ? "yes — exactly on the edge" : "yes") : "no", cls: isIn ? "yes" : "no" }] });
    });
    drawRequests(m, REQUESTS, q => q._cls);
    tableRows(document.getElementById("bufRows"), rows);
    const area = r1BufferArea(r, rnd);
    document.getElementById("bufOut").innerHTML = `<strong>Inside:</strong> <strong class="ids">${idsText(inside)}</strong> (${inside.length}).<br>Buffer area by hand: ${rnd ? `2000 × ${2 * r} + π × ${r}² = ` : `2000 × ${2 * r} = `}<strong>${fmtM2(area)}</strong>${rnd ? " (a GIS shows slightly less — the round ends are drawn with straight segments)" : ""}.<br><span class="small">Edge cases: ${REQUESTS.filter(q => list.includes(q) && Math.abs(distToLine(q.x, q.y, ROADS.R1.pts, !rnd) - r) < 1e-9).map(q => q.id).join(", ") || "none at this distance"} — decided by your ≤ / &lt; rule, not by the software.</span>`;
  }
  const dist = document.getElementById("dist"), incl = document.getElementById("incl"), round = document.getElementById("round"), onlyOpen_ = document.getElementById("onlyOpen"), distV = document.getElementById("distV");
  [dist, incl, round, onlyOpen_].forEach(el => el.addEventListener("input", draw)); draw();

  const ov = document.getElementById("ovFig");
  function drawOv() {
    const mode = document.querySelector("input[name=dis]:checked").value;
    const m = gridMap(ov, { extent: { x1: -450, y1: -400, x2: 2400, y2: 1300 }, caption: `R1 and R2 buffered by 300 m — Dissolve Type ${mode === "none" ? "None (two overlapping polygons)" : "All (one merged polygon)"}.` });
    drawWards(m, WARDS.slice(0, 2), "faded");
    drawBuffer(m, ROADS.R1, 300, { cls: mode === "all" ? "merged" : "" }); drawBuffer(m, ROADS.R2, 300, { cls: mode === "all" ? "merged" : "overlap" });
    drawRoad(m, ROADS.R1); drawRoad(m, ROADS.R2);
    const hits = REQUESTS.map(q => ({ q, n: [ROADS.R1, ROADS.R2].filter(rd => distToLine(q.x, q.y, rd.pts) <= 300 + 1e-9).length }));
    drawRequests(m, REQUESTS, q => { const h = hits.find(x => x.q === q).n; return h === 2 ? "dup" : h === 1 ? "in" : "out"; });
    const total = hits.reduce((s, h) => s + h.n, 0), distinct = hits.filter(h => h.n > 0).length;
    document.getElementById("ovOut").innerHTML = mode === "none"
      ? `Two polygons. Complaints touching at least one: <strong class="ids">${idsText(hits.filter(h => h.n > 0).map(h => h.q.id))}</strong> (${distinct} distinct). Complaint–buffer pairs: <strong>${total}</strong> — <span class="chip no">P1 is in both bands</span>. A one-to-many join from buffers to complaints would list P1 twice; a selection lists it once.`
      : `One merged polygon. Complaints inside: <strong class="ids">${idsText(hits.filter(h => h.n > 0).map(h => h.q.id))}</strong> (${distinct}). No double counting — but you can no longer say <em>which road</em> a complaint is near.`;
  }
  document.querySelectorAll("input[name=dis]").forEach(r => r.addEventListener("change", drawOv)); drawOv();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
