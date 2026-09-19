<?php $page = ['title' => '11.3 Clip — keep only what is inside', 'chapter' => 11, 'module' => '11.3']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 11.3 · General idea, with ArcGIS Pro and QGIS wording</div>
    <h1>Clip: a cookie cutter, not a filter</h1>
    <p class="lead"><strong>Clip</strong> takes an input layer and a “cutter” layer (usually your study-area polygon) and keeps only the <em>parts</em> of the input that lie inside. The shapes are cut. The attribute values are copied exactly as they were — which is convenient, and also the source of a very common mistake.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>See the difference between <em>selecting</em> a whole road that touches Ward A and <em>clipping</em> the road to keep only its Ward A portion.</li>
      <li>Check what a clip keeps (geometry cut, input attributes unchanged, nothing from the cutter).</li>
      <li>Decide when a stored length or area field must be recalculated — and why the software will not do it for you.</li></ul></div>
  </div>

  <h2><span class="mod">11.3.1</span>What clip does</h2>
  <p>ArcGIS Pro’s Clip tool “Extracts input features that overlay the clip features.” QGIS’s Clip says it more fully: “Only the parts of the features in the input layer that fall within the polygons of the overlay layer will be added to the resulting layer.” Two rules follow:</p>
  <div class="grid-2">
    <div class="card"><h3 style="margin-top:0">1 · Geometry is cut; attributes are copied unchanged</h3><p>ArcGIS Pro: the output “will contain all the attributes of the Input Features”. QGIS is blunt about the consequence: “This operation modifies only the features geometry. The attribute values of the features are not modified, although properties such as area or length of the features will be modified by the overlay operation.” So a 2,000 m road with a field <code>length_m = 2000</code> becomes a 1,000 m line <em>still carrying</em> <code>length_m = 2000</code>.</p></div>
    <div class="card"><h3 style="margin-top:0">2 · The cutter must suit the input</h3><p>In ArcGIS Pro, polygon inputs need polygon cutters; line inputs can be cut by lines or polygons; point inputs by points, lines or polygons. In practice the cutter is almost always your study-area polygon — the only case this chapter uses.</p></div>
  </div>
  <p>Clip is the operation for <strong>“give me only what is inside the study area”</strong> when the study area itself is not interesting. Its output has the <em>same geometry type</em> as the input (lines in, lines out) and carries <em>only the input’s attributes</em> — nothing from the cutter is added. If you also need to know <em>which</em> study-area polygon each piece fell in, that is intersect (11.4), not clip.</p>
  <div class="callout dev"><span class="label">Developer view</span><p>A <code>WHERE</code> filter keeps or drops whole rows. Clip can keep <em>part of a row</em>: it produces a shape the input never contained. The cookie-cutter picture is exact for geometry — you get the dough inside the cutter, and the dough keeps its ingredients (attributes). Where the picture stops: the packet label said “weight: 500 g”. That was true of the whole sheet of dough, not of the cookie.</p></div>

  <h2><span class="mod">11.3.2</span>Road R1 crosses the Ward A boundary: select, or clip?</h2>
  <p>The road crew is responsible only for Ward A. Two people ask two different questions about R1, which runs from x = 0 to x = 2000 — right through Ward A <em>and</em> Ward B.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Two questions, two operations</h3>
    <div class="controls">
      <label><input type="radio" name="op" value="select" checked> Person 1: “Which roads does Ward A contain, even partly?” → <strong>Select</strong></label>
      <label><input type="radio" name="op" value="clip"> Person 2: “How much road is <em>in</em> Ward A?” → <strong>Clip</strong></label>
      <label><input type="radio" name="op" value="buffer"> Bonus: clip the 300 m buffer of R1 by Ward A</label>
    </div>
    <figure class="map-fig" id="clipFig"></figure>
    <div class="grid-2">
      <div class="recs" id="clipRecs"></div>
      <div class="result" id="clipOut"></div>
    </div>
  </div>
  <p><strong>Two different answers to “how long is R1 in Ward A?”</strong> Person 1’s selection says 2,000 m — the whole feature was selected because part of it touches Ward A. Person 2’s clip says 1,000 m — only the part from (0, 500) to (1000, 500) remains, and (1000, 500) itself is kept because the boundary counts as inside. Both are correct answers to their own questions. Your worksheet must say which question is being answered; “roads in Ward A” is ambiguous until it does.</p>
  <p><strong>How to check a clip.</strong> Read the output: one feature, still a line, end points (0, 500) and (1000, 500). Compute its length from the coordinates (1,000 m). Confirm the output’s extent lies inside the cutter’s extent — an output bigger than the cutter means something else was run. Confirm the table has R1’s fields and <em>no ward field</em>. <strong>What it does not tell you:</strong> that the crew can reach every metre of that kilometre; that the ward line is legally where the practice data says; that R1’s other attributes (surface, width) apply evenly to the kept part.</p>

  <h2><span class="mod">11.3.3</span>Stale lengths and areas: recalculate, or rename</h2>
  <p>Suppose the roads layer had a <code>length_m</code> field filled in before the clip. After the clip, R1’s output row says <code>length_m = 2000</code> on a line that is 1,000 m long. Neither ArcGIS Pro’s Clip nor QGIS’s Clip touches user fields. Two exceptions and one rule:</p>
  <ul>
    <li><strong>Geodatabase geometry fields.</strong> A feature class in an ArcGIS geodatabase keeps <code>Shape_Length</code> / <code>Shape_Area</code> up to date automatically — those <em>do</em> describe the new shape. A user field named <code>length_m</code> does not. A shapefile or CSV has no automatic field at all (Chapter 7).</li>
    <li><strong>Ratio policy.</strong> ArcGIS Pro’s Clip (and Intersect) can, if a layer field has <em>Use Ratio Policy</em> switched on, scale a copied number by the fraction of the shape kept. That is a deliberate allocation rule (11.4.3), not a default, and it assumes the quantity is spread evenly along the feature — true of length, sometimes true of population, false of “number of streetlights” if they are clustered.</li>
    <li><strong>The rule.</strong> After any operation that changes geometry, either <strong>recalculate</strong> stored measurements from the new shape (ArcGIS Pro: Calculate Geometry Attributes — it “modifies the input data”, so run it on a copy; QGIS: Add geometry attributes) or <strong>rename</strong> the copied field (<code>length_m_orig</code>) so nobody reads it as a description of the new shape. Never deliver a stale measurement under its original name.</li>
  </ul>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“Clip keeps the features that are inside.”</em> It keeps the <strong>parts</strong> that are inside. A feature entirely outside disappears; a feature partly inside is cut; a feature entirely inside is kept whole. Someone expecting the first behaviour is surprised that a 2,000 m road became a 1,000 m road — and may report the wrong length.</p></div>

  <div class="quiz" data-answer="2" data-fb="R2 = (500,0)→(500,500)→(800,900): two segments of 500 m each, total 1,000 m, entirely inside Ward A. Clip by A keeps the whole road unchanged — length_m = 1000 still describes it (recheck anyway; it is cheap). Clip by B returns no features — an expected empty output, to be logged as such.">
    <div class="q">Quick check 11.3. School Road R2 runs (500, 0) → (500, 500) → (800, 900), entirely inside Ward A. You clip R2 by Ward A, and separately by Ward B. What comes out?</div>
    <div class="opts"><button class="opt">A: half the road; B: the other half.</button><button class="opt">A: the whole road with length_m now wrong; B: the whole road too.</button><button class="opt">A: the whole road, unchanged (length_m still correct); B: no features at all — an expected empty output.</button><button class="opt">Both clips fail because R2 has two segments.</button></div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const fig = document.getElementById("clipFig"), A = wardById("A");
  function draw() {
    const op = document.querySelector("input[name=op]:checked").value;
    const m = gridMap(fig, { extent: { x1: -450, y1: -200, x2: 2450, y2: 1200 }, caption: op === "select" ? "Select By Location: the whole of R1 is selected because part of it touches Ward A." : op === "clip" ? "Clip: only the part of R1 inside Ward A is kept." : "Clip the 300 m buffer of R1 by Ward A: the rectangle 0–1000 × 200–800 remains." });
    drawWards(m, [wardById("B")], "faded"); drawWards(m, [A], "study");
    let recs = "", out = "";
    if (op === "select") {
      drawRoad(m, ROADS.R1, "kept");
      recs = `<div class="rec"><h5>Selected record (unchanged)</h5><div class="kv"><b>road_id</b><span>R1</span><b>name</b><span>Main Road</span><b>geometry</b><span>line (0,500)–(2000,500)</span><b>length_m</b><span class="fresh">2000 (still true)</span></div></div>`;
      out = `<strong>Answer to Person 1:</strong> R1 is selected. It stays a <strong>2,000 m</strong> line — selection never cuts anything. Kind of answer: a <em>selection</em> (11.1.2).`;
    } else if (op === "clip") {
      drawRoad(m, ROADS.R1, "ghost");
      const segs = clipLineToRect(ROADS.R1.pts, A);
      segs.forEach(s => mk("polyline", { points: s.map(p => `${m.X(p[0])},${m.Y(p[1])}`).join(" "), class: "road kept", fill: "none" }, m.svg));
      mk("line", { x1: m.X(1000), y1: m.Y(-100), x2: m.X(1000), y2: m.Y(1100), class: "cutline" }, m.svg);
      const L = segs.reduce((s, sg) => s + hyp(sg[1][0] - sg[0][0], sg[1][1] - sg[0][1]), 0);
      recs = `<div class="rec new"><h5>Output record (new geometry)</h5><div class="kv"><b>road_id</b><span>R1</span><b>name</b><span>Main Road</span><b>geometry</b><span>line (0,500)–(1000,500)</span><b>length_m</b><span class="stale">2000</span> <span class="small">copied — stale!</span><b>recomputed</b><span class="fresh">${fmt(L)} m</span></div></div><div class="rec gone"><h5>Discarded</h5><div class="kv"><b>part</b><span>(1000,500)–(2000,500), 1000 m, in Ward B</span></div></div>`;
      out = `<strong>Answer to Person 2:</strong> a new line of <strong>${fmt(L)} m</strong>. The end point (1000, 500) is on the shared boundary and is kept — boundary counts as inside. Kind of answer: a <em>new shape</em>. Notice <code>length_m</code> still says 2000.`;
    } else {
      drawBuffer(m, ROADS.R1, 300, { cls: "" }); drawRoad(m, ROADS.R1, "ghost");
      const band = { x1: 0, y1: 200, x2: 2000, y2: 800 }, ov = rectOverlap(band, A);
      mk("rect", { x: m.X(ov.x1), y: m.Y(ov.y2), width: m.S(ov.x2 - ov.x1), height: m.S(ov.y2 - ov.y1), class: "piece" }, m.svg);
      recs = `<div class="rec new"><h5>Output polygon</h5><div class="kv"><b>geometry</b><span>rectangle 0–1000 × 200–800</span><b>BUFF_DIST</b><span>300 (copied — still true, it is a label)</span><b>area recomputed</b><span class="fresh">${fmtM2(ov.area)}</span></div></div>`;
      out = `The west half-circle of the buffer lies at x &lt; 0 — outside Ward A — and the east half of the band is in Ward B, so both are cut away. What remains is exactly 1000 × 600 = <strong>${fmtM2(ov.area)}</strong> (60 % of Ward A). A <em>polygon</em> cut by a <em>polygon</em>.`;
    }
    document.getElementById("clipRecs").innerHTML = recs; document.getElementById("clipOut").innerHTML = out;
  }
  document.querySelectorAll("input[name=op]").forEach(r => r.addEventListener("change", draw)); draw();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
