<?php $page = ['title' => 'Chapter 10 — Attribute queries and spatial relationships', 'chapter' => 10]; require __DIR__ . '/../partials/head.php'; ?>
  <section class="hero">
    <div class="fade-up">
      <div class="page-head" style="padding:0">
        <div class="eyebrow">GIS Phase 1 · Chapter 10 · Interactive tutorial</div>
      </div>
      <h1 class="big">Ask the map a question.<br>Then <em>check</em> the answer.</h1>
      <p class="lead">You already know how to write a <code>WHERE</code> clause. This chapter adds the map: “which requests are <em>inside</em> Ward A?”, “which are <em>within 300 metres</em> of the road?”, “which drain is <em>nearest</em>?”. The words look simple. The tricky part is the edge cases — a point sitting exactly on a ward line, a missing value, a duplicate key that doubles your totals — and the fact that two GIS products can give different answers to the same word.</p>
      <p><a class="btn primary" href="m1-actions">Begin module 10.1 →</a> &nbsp; <a class="btn ghost" href="glossary">Glossary</a></p>
      <p class="small">No software is needed for modules 10.1–10.7; every demo runs in this page. The lab in 10.8 uses PostGIS, QGIS or ArcGIS Pro. All wards, roads, requests and costs here are <span class="synthetic">made-up practice data</span> — no real town or office. Your progress is saved in this browser only.</p>
    </div>
    <figure class="map-fig fade-up" id="heroFig"></figure>
  </section>

  <h2 style="border-top:0;margin-top:1rem"><span class="mod">The big idea</span>Predict first, run second, then explain every difference</h2>
  <div class="grid-2">
    <div class="card">
      <p>Our practice town has six service requests on a small grid, two wards and one road. Small enough that you can work out every answer with a pencil. That is the whole method of this chapter:</p>
      <ol>
        <li>Write the question as a condition, and write down <strong>which IDs you expect</strong> (<strong>10.1</strong>).</li>
        <li>Build the condition properly — including the missing values, which behave in a way that surprises most programmers (<strong>10.2</strong>).</li>
        <li>Join tables by a key and watch how one duplicate key quietly doubles a total (<strong>10.3</strong>).</li>
        <li>Learn the six ways two shapes can relate — inside, touching, overlapping… — by <em>drawing</em> them first (<strong>10.4</strong>).</li>
        <li>Find out what happens to a point that sits <em>exactly on the line</em> — and why PostGIS, QGIS and ArcGIS Pro do not agree (<strong>10.5</strong>).</li>
        <li>Handle distance carefully: “nearest” is not “within a distance”, and metres are not degrees (<strong>10.6</strong>).</li>
        <li>Put it together into one honest answer with a written policy for the edge cases (<strong>10.7</strong>), then prove it in the lab (<strong>10.8</strong>).</li>
      </ol>
    </div>
    <div class="card">
      <h3 style="margin-top:0">Before you start</h3>
      <p>These ideas from earlier chapters are used without re-teaching. If one feels new, revise it first.</p>
      <ul>
        <li><strong>Chapter 8:</strong> a missing value (<code>NULL</code>) means “not known”. It is not zero and not empty text. One asset can have many inspections — that is a one-to-many relationship with a key.</li>
        <li><strong>Chapter 9:</strong> the data we query here has already been <em>checked</em>. A strange result now is usually a question of <em>meaning</em> (“what does inside mean?”), not a broken record.</li>
        <li><strong>Chapters 5–6:</strong> a metre threshold cannot be typed straight onto latitude/longitude data. Our practice grid is flat and in metres, so distances here are plain school geometry.</li>
        <li><strong>Chapter 3:</strong> point, line, polygon; a polygon can have a <em>hole</em>; every shape has a bounding box.</li>
      </ul>
      <div class="callout dev" style="margin-bottom:0"><span class="label">Developer view</span><p>A spatial predicate is just a boolean function on two shapes: <code>intersects(a, b) → true/false</code>. A spatial join is a <code>JOIN … ON intersects(a.geom, b.geom)</code> instead of <code>ON a.key = b.key</code>. Everything you know about <code>LEFT JOIN</code> row counts and <code>NULL</code> still applies — plus one new thing: the <em>boundary</em> of a shape, which has no equivalent in ordinary SQL.</p></div>
    </div>
  </div>

  <h2><span class="mod">This chapter’s practice data</span>The six requests, with two new columns</h2>
  <p>The wards, the road and the six requests are exactly the ones from Chapter 1. Chapter 10 adds two columns. <code>ward_code</code> is the ward <em>as typed by the call-centre clerk</em> when the call came in — a person typed it, so it is not perfect: for P5 the clerk left it blank (“the caller said it’s right on the ward line”), and for P6 the clerk typed <code>C</code>, which is not a ward at all. <code>est_cost_inr</code> is the estimated repair cost in rupees: <code>NULL</code> means <em>not estimated yet</em>; <code>0</code> means <em>estimated, and it is zero</em> (P4 was closed as “no fault found”).</p>
  <div class="table-wrap" id="reqTable"></div>
  <p class="small">Distances to Road R1 (worked out in Chapter 1): P1 300 m, P2 300 m, P3 250 m, P4 400 m, P5 0 m, P6 200 m — P6’s distance is to the road’s <em>end</em> at (2000, 500), because the road stops there. P1 and P2 are inside Ward A; P3 and P4 inside Ward B; P5 sits exactly on the shared ward line <em>and</em> on the road; P6 is outside both wards.</p>

  <div class="grid-2">
    <div class="table-wrap"><h4>Ward register export (made up)</h4>
      <table class="attr"><thead><tr><th>ward_code</th><th>ward_name</th><th>crew_team</th><th>valid_from</th><th>is_current</th></tr></thead>
      <tbody><tr class="hl"><td class="mono">A</td><td>West ward</td><td class="mono">T-N</td><td class="mono">2019-04-01</td><td>No</td></tr>
      <tr class="hl"><td class="mono">A</td><td>West ward</td><td class="mono">T-N</td><td class="mono">2024-01-01</td><td>Yes</td></tr>
      <tr><td class="mono">B</td><td>East ward</td><td class="mono">T-S</td><td class="mono">2019-04-01</td><td>Yes</td></tr></tbody></table>
      <p class="small">Ward A’s boundary was revised in 2024, and the register keeps the old row. So the export has <strong>two rows with code A</strong>. That duplicate is the star of module 10.3.</p>
    </div>
    <div class="table-wrap"><h4>Inspection history (from Chapter 8)</h4>
      <table class="attr"><thead><tr><th>inspection</th><th>asset</th><th>visit (IST)</th><th>team</th><th>condition</th></tr></thead>
      <tbody id="insRows"></tbody></table>
      <p class="small">Drain DR-0042 has two visits, streetlight SL-0113 has two, tree TR-0301 has none. Note INS-0004 is the <em>older</em> visit of SL-0113 even though it has the higher number — it was typed in late from a paper form.</p>
    </div>
  </div>

  <h2><span class="mod">Modules</span>Work through them in order</h2>
  <div class="modcards" id="modcards"></div>

  <div class="callout note"><span class="label">What this tutorial is based on</span><p>These pages follow the Chapter 10 training document (<code>GIS_Phase_1_Chapter_10_Attribute_Queries_and_Spatial_Relationships.md</code>). Every answer shown here is worked out live in your browser from the definitions quoted in that document (PostGIS <code>ST_Contains</code>/<code>ST_Covers</code>/<code>ST_Intersects</code>/<code>ST_Touches</code>, Esri’s Select By Location options, the Esri proximity rules) and matches the document’s answer tables. The software steps in 10.8 are written from the official ArcGIS Pro 3.7, QGIS 3.44 and PostGIS documentation and have <strong>not</strong> been run by the author — the lab page says what your instructor must check first.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  reqTable(document.getElementById("reqTable"), ["id", "xy", "category", "priority", "status", "reported", "closed", "channel", "ward_code", "cost"]);
  document.getElementById("insRows").innerHTML = FIXTURE.inspections.map(i => `<tr><td class="mono">${i.id}</td><td class="mono">${i.asset}</td><td class="mono">${i.visit}</td><td class="mono">${i.team}</td><td class="mono">${i.condition}</td></tr>`).join("");
  renderGrid(document.getElementById("heroFig"), { layers: { wards: true, roads: true, requests: true, assets: true }, caption: "The practice grid: two wards, one road, six requests, three assets. Made-up data; metres; no real place." });
  const prog = getProgress();
  const blurbs = {
    "10.1": "A filter, a selection, an export and an update all show “fewer rows”. Only one of them changes your data. Predict the count before you click.",
    "10.2": "Equality, ranges, AND/OR, LIKE — and the missing-value rule that puts two requests in neither the “cheap” list nor the “expensive” list.",
    "10.3": "Join requests to the ward register and watch six requests become eight rows. Decide what you are counting before you add anything up.",
    "10.4": "Intersects, disjoint, contains, touches, overlaps, nearest — drawn on the practice grid before any tool is named. Plus why a bounding box only says “maybe”.",
    "10.5": "Drag a point around a depot with a courtyard hole. Inside, on the wall, in the hole, outside — see what each named predicate says, in three products.",
    "10.6": "Nearest needs a tie rule; within-a-distance needs a unit and an inclusive/strict decision; metres are not degrees; a Z column does not make a query 3D.",
    "10.7": "“Open requests inside the wards near the road” as three testable conditions, a written ward-assignment policy, and what Join_Count really counts.",
    "10.8": "Predict every answer on paper, run the documented equivalents in PostGIS, QGIS or ArcGIS Pro, and explain every difference.",
    "10.9": "Six concept questions, two scenarios, a practical on new data with a hole, a tie and a duplicate key, and one spoken explanation.",
    "10.10": "The recap, the media brief in short, and what Chapter 11 builds from these relationships."
  };
  document.getElementById("modcards").innerHTML = MODULES.map(m => `<a class="modcard ${prog[m.id] ? "done" : ""}" href="${m.file}"><span class="done-badge">done</span><div class="id">MODULE ${m.id}</div><h3>${m.title}</h3><p>${blurbs[m.id]}</p></a>`).join("");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
