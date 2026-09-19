<?php $page = ['title' => 'Chapter 6 — Projections, transformations, and accurate measurement', 'chapter' => 6]; require __DIR__ . '/../partials/head.php'; ?>
  <section class="hero">
    <div class="fade-up">
      <div class="page-head" style="padding:0">
        <div class="eyebrow">GIS Phase 1 · Chapter 6 · Interactive tutorial</div>
      </div>
      <h1 class="big">The Earth is round.<br>Your screen is <em>flat</em>.</h1>
      <p class="lead">Chapter 5 taught you to <em>read</em> a coordinate together with its coordinate system. This chapter is about <em>doing things</em> to coordinates: flattening the round Earth onto a map (a <strong>projection</strong>), moving data from one coordinate system to another (a <strong>transformation</strong>), and then measuring a distance or an area so that the number you report is actually true on the ground.</p>
      <p><a class="btn primary" href="m1-distortion">Begin module 6.1 →</a> &nbsp; <a class="btn ghost" href="glossary">Glossary</a></p>
      <p class="small">No software is needed for modules 6.1–6.7; every demo runs in this page. The lab in 6.8 uses ArcGIS Pro or QGIS, with a paper route if you have neither. All places, wards and requests here are <span class="synthetic">made-up practice data</span> — no real town or office. Your progress is saved in this browser only.</p>
    </div>
    <figure class="map-fig fade-up" id="heroFig"></figure>
  </section>

  <h2 style="border-top:0;margin-top:1rem"><span class="mod">The big idea</span>Peel an orange, then press the peel flat</h2>
  <div class="grid-2">
    <div class="card">
      <p>Try to press an orange peel flat on a table. It tears, or it stretches. There is no way to make it flat <em>and</em> keep every distance, every area and every angle the same. A <strong>map projection</strong> is just a careful, mathematical way of pressing the peel flat — and every projection stretches <em>something</em>.</p>
      <p>Everything in this chapter follows from that one fact:</p>
      <ol>
        <li>Every flat map is wrong somewhere; you choose <em>what</em> to keep right and <em>where</em> (<strong>6.1</strong>).</li>
        <li>Changing the <em>label</em> on a dataset and <em>converting</em> its numbers are two different jobs — and the software will not stop you doing the wrong one (<strong>6.2</strong>).</li>
        <li>Two “latitude/longitude” systems can disagree by 100 metres, so moving between them needs a documented <strong>datum transformation</strong> (<strong>6.3</strong>).</li>
        <li>Layers that <em>line up on screen</em> are not automatically ready for analysis (<strong>6.4</strong>).</li>
        <li>You can measure on the flat map (<strong>planar</strong>) or on the curved Earth (<strong>geodesic</strong>) — and degrees are never metres (<strong>6.5</strong>).</li>
        <li>The <em>requirement</em> chooses the method, not a habit (<strong>6.6</strong>); you then check it with a small experiment (<strong>6.7</strong>) and a lab (<strong>6.8</strong>).</li>
      </ol>
    </div>
    <div class="card">
      <h3 style="margin-top:0">Before you start</h3>
      <p>You should be comfortable with these ideas from Chapter 5. If any feels new, revise it first.</p>
      <ul>
        <li>A coordinate needs its <strong>CRS</strong> (coordinate reference system), its <strong>units</strong> and its <strong>axis order</strong>.</li>
        <li><strong>Geographic CRS</strong> = angles in degrees (latitude, longitude). <strong>Projected CRS</strong> = a flat grid in metres.</li>
        <li><strong>EPSG:4326</strong> (WGS 84, degrees) and <strong>EPSG:3857</strong> (Web Mercator, metres) are the two you meet most.</li>
        <li><strong>UTM</strong> is a family of 60 zones, each 6° wide — not one system.</li>
        <li>GeoJSON writes <strong>longitude first</strong>, then latitude.</li>
        <li>An unknown CRS is an <em>investigation</em>, never a guess.</li>
      </ul>
      <div class="callout dev" style="margin-bottom:0"><span class="label">Developer view</span><p>Think of a CRS as the <em>type</em> of a number. Changing the type label without converting the value (<code>reinterpret_cast</code>) and converting the value properly (<code>static_cast</code>) are different operations. This whole chapter is about not mixing them up — with the twist that in GIS a wrong label does not crash; it quietly draws your data in the wrong place.</p></div>
    </div>
  </div>

  <h2><span class="mod">This chapter’s practice data</span>Fixture E6 — a made-up town on a real-shaped Earth</h2>
  <p>Earlier chapters used a flat practice grid with no Earth location. This chapter needs data that really sits on the Earth, so it introduces <strong>Fixture E6</strong>. Two wards, drawn as small rectangles in degrees, and five service requests. The place was chosen for one reason only: its middle line, longitude 75° E, is the <strong>central meridian of UTM zone 43N</strong>, which makes many answers come out exact and easy to explain. It is <span class="synthetic">not a real town</span>.</p>
  <div class="grid-2">
    <div class="table-wrap"><table>
      <thead><tr><th>Ward</th><th>Longitude (° E)</th><th>Latitude (° N)</th><th>Size on the ground</th></tr></thead>
      <tbody>
        <tr><td><strong>A</strong></td><td class="mono">74.990 – 75.000</td><td class="mono">23.000 – 23.010</td><td>about 1,025 m wide × 1,107 m tall</td></tr>
        <tr><td><strong>B</strong></td><td class="mono">75.000 – 75.010</td><td class="mono">23.000 – 23.010</td><td>same — the two share the line at 75.000° E</td></tr>
      </tbody></table>
      <p class="small">Each ward is a “square” of 0.010° × 0.010° — but on the ground it is a rectangle. Module 6.5 explains why.</p>
    </div>
    <div class="table-wrap"><table id="reqTable">
      <thead><tr><th>Request</th><th>Latitude, longitude</th><th>Delivered as UTM 43N (E, N in metres)</th><th>Where</th></tr></thead>
      <tbody></tbody></table>
      <p class="small">The requests were <em>defined</em> in degrees but <em>delivered</em> in UTM metres — that is the situation the lab in 6.8 resolves.</p>
    </div>
  </div>

  <h2><span class="mod">Modules</span>Work through them in order</h2>
  <div class="modcards" id="modcards"></div>

  <div class="callout note"><span class="label">What this tutorial is based on</span><p>These pages follow the Chapter 6 training document (<code>GIS_Phase_1_Chapter_06_Projections_Transformations_and_Measurement.md</code>). Every number shown here is calculated live in your browser from the published formulas (EPSG Guidance Note 7-2) and matches the document’s answer tables. The software steps in 6.8 are written from the official ArcGIS Pro 3.7 and QGIS 3.40 documentation and have <strong>not</strong> been run by the author — the lab page says what your instructor must check first.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  document.querySelector("#reqTable tbody").innerHTML = E6.requests.map(q => `<tr><td class="mono">${q.id}</td><td class="mono">${q.lat.toFixed(3)} N, ${q.lon.toFixed(3)} E</td><td class="mono">${fx(q.E)}, ${fx(q.N)}</td><td>${q.where}</td></tr>`).join("");
  renderE6(document.getElementById("heroFig"), { crs: "utm", caption: "Fixture E6 drawn on the UTM zone 43N grid. Made-up practice data." });
  const prog = getProgress();
  const blurbs = {
    "6.1": "Area, shape, distance, direction — a flat map keeps some of them right in some places, never all of them everywhere.",
    "6.2": "Define Projection changes a label. Project changes the numbers. Watch what happens when you pick the wrong one.",
    "6.3": "Why WGS 84 and an older Indian datum put the same place 112 m apart, and how to read a transformation record.",
    "6.4": "Layers drawn together on screen are only a view. Fill the five-row worksheet before you analyse anything.",
    "6.5": "Planar measures on the flat map; geodesic measures on the curved Earth. Degrees are angles, not metres.",
    "6.6": "A worksheet that turns the requirement into a method — and why one UTM zone cannot cover all of India.",
    "6.7": "Measure the same two points five ways and explain every difference by its cause.",
    "6.8": "Take two layers in different coordinate systems, make an analysis-ready copy, measure, log, spot-check.",
    "6.9": "Six concept questions, two scenarios, a practical on new data, and one spoken explanation.",
    "6.10": "The slides and diagrams in brief, the recap, and what Chapter 7 does with your log."
  };
  document.getElementById("modcards").innerHTML = MODULES.map(m => `<a class="modcard ${prog[m.id] ? "done" : ""}" href="${m.file}"><span class="done-badge">done</span><div class="id">MODULE ${m.id}</div><h3>${m.title}</h3><p>${blurbs[m.id]}</p></a>`).join("");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
