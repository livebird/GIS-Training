<?php $page = ['title' => 'Chapter 11 — Spatial analysis fundamentals', 'chapter' => 11]; require __DIR__ . '/../partials/head.php'; ?>
  <section class="hero">
    <div class="fade-up">
      <div class="page-head" style="padding:0">
        <div class="eyebrow">GIS Phase 1 · Chapter 11 · Interactive tutorial</div>
      </div>
      <h1 class="big">Stop asking the map.<br>Start <em>making</em> answers.</h1>
      <p class="lead">Chapter 10 taught you to ask a GIS questions and get <em>existing</em> records back. This chapter teaches five small operations that make <strong>new shapes and new numbers</strong> — <strong>buffer</strong>, <strong>clip</strong>, <strong>intersect</strong>, <strong>dissolve</strong> and <strong>spatial join</strong> — plus one tiny raster calculation. More important than the buttons: write the question first, guess the answer, run the tool, check your guess, and write down what you did.</p>
      <p><a class="btn primary" href="m1-specify">Begin module 11.1 →</a> &nbsp; <a class="btn ghost" href="glossary">Glossary</a></p>
      <p class="small">No software is needed for modules 11.1–11.8 — every demo runs in this page. The lab in 11.9 uses ArcGIS Pro or QGIS, with a paper route if you have neither. All wards, roads and requests here are <span class="synthetic">made-up practice data</span> on a flat metre grid — no real town or office. Your progress is saved in this browser only.</p>
    </div>
    <figure class="map-fig fade-up" id="heroFig"></figure>
  </section>

  <h2 style="border-top:0;margin-top:1rem"><span class="mod">The big idea</span>A question, then a tool — never the other way round</h2>
  <div class="grid-2">
    <div class="card">
      <p>Imagine a works manager in a municipal office. She asks: <em>“How many open complaints near Main Road are there in each ward? I have to decide where the road crew goes next week.”</em></p>
      <p>A beginner opens the software, sees a long list of tools and clicks <strong>Buffer</strong> because it sounds right. An experienced person does something different first — writes down, in plain words:</p>
      <ol>
        <li>What decision is this for? (<strong>11.1</strong>)</li>
        <li>Which complaints count? (open ones, since 1 August)</li>
        <li>What does “near” mean, in metres, and is exactly 300 m “near” or not?</li>
        <li>What should the answer <em>look like</em> — a list, a shape, a count?</li>
        <li>How will I know the answer is right?</li>
      </ol>
      <p>Only then do the tools come in: a <strong>buffer</strong> to draw “near the road” (<strong>11.2</strong>), <strong>clip</strong> to keep only the part inside a ward (<strong>11.3</strong>), <strong>intersect</strong> to find overlaps and who they belong to (<strong>11.4</strong>), <strong>dissolve</strong> to merge wards into zones (<strong>11.5</strong>), and a <strong>spatial join</strong> to count complaints per ward (<strong>11.6</strong>). A small raster example follows (<strong>11.7</strong>), then how to check and record everything (<strong>11.8</strong>).</p>
    </div>
    <div class="card">
      <h3 style="margin-top:0">Before you start</h3>
      <p>You should be comfortable with these ideas from earlier chapters. If any feels new, revise it first.</p>
      <ul>
        <li><strong>Chapter 10:</strong> a <em>selection</em> returns existing records; a <em>join</em> adds columns; “inside” has two meanings — strictly inside, or inside-<em>or</em>-on-the-edge.</li>
        <li><strong>Chapter 10:</strong> the practice answers — P1, P2, P3, P5, P6 are within 300 m of Road R1; P5 sits exactly on the line between Wards A and B.</li>
        <li><strong>Chapter 6:</strong> a metre distance cannot be used on latitude/longitude numbers; move the data to a metre-based CRS first (EPSG:32643 for our practice town).</li>
        <li><strong>Chapter 4:</strong> a raster is a grid of cells; <strong>NoData</strong> means “not known”, and it is not zero.</li>
        <li><strong>Chapter 9:</strong> keep a log of what you changed and why.</li>
      </ul>
      <div class="callout dev" style="margin-bottom:0"><span class="label">Developer view</span><p>Think of Chapter 10 as <code>SELECT … WHERE</code> and this chapter as functions that <em>return new values</em>: <code>buffer()</code>, <code>clip()</code>, <code>intersection()</code>, <code>GROUP BY</code> with <code>ST_Union</code>, and a join that aggregates. Same discipline as any function you write: know the return <em>type</em> before you call it, and test with a case whose answer you already know.</p></div>
    </div>
  </div>

  <h2><span class="mod">This chapter’s practice data</span>The training grid — two wards, one road, six complaints</h2>
  <p>Everything in modules 11.1–11.8 sits on a flat sheet of graph paper measured in <strong>metres</strong>. x goes to the right, y goes up. It is <span class="synthetic">not a real place</span> and has <strong>no coordinate-system code</strong> — which is exactly why every answer can be checked by hand. Two new columns were added for this chapter: <code>status</code> and <code>reported_on</code>.</p>
  <div class="grid-2">
    <div class="table-wrap"><table id="reqTable">
      <thead><tr><th>Request</th><th>(x, y) m</th><th>Status</th><th>Reported on</th><th>Where it is</th><th>Distance to Road R1</th></tr></thead>
      <tbody></tbody></table>
      <p class="small">Road R1 (“Main Road”) runs from (0, 500) to (2000, 500) and stops there — it is 2,000 m long, not endless. P6 is 200 m beyond its east end.</p>
    </div>
    <div>
      <div class="table-wrap"><table>
        <thead><tr><th>Ward</th><th>Corners (m)</th><th>Area</th><th>Zone</th><th>Households</th></tr></thead>
        <tbody>
          <tr><td><strong>A</strong></td><td class="mono">(0,0)–(1000,1000)</td><td>1 km²</td><td>Z1</td><td>1,200</td></tr>
          <tr><td><strong>B</strong></td><td class="mono">(1000,0)–(2000,1000)</td><td>1 km²</td><td>Z2</td><td>900</td></tr>
          <tr><td><strong>C</strong></td><td class="mono">(0,1000)–(1000,1500)</td><td>0.5 km²</td><td>Z1</td><td>400</td></tr>
          <tr><td><strong>D</strong></td><td class="mono">(2500,0)–(3000,500)</td><td>0.25 km²</td><td>Z2</td><td>150</td></tr>
        </tbody></table>
      <p class="small">Wards C and D appear only in the dissolve (11.5) and spatial-join (11.6) modules. D is a small outlying ward that touches nothing — on purpose.</p></div>
    </div>
  </div>

  <h2><span class="mod">Modules</span>Work through them in order</h2>
  <div class="modcards" id="modcards"></div>

  <div class="callout note"><span class="label">What this tutorial is based on</span><p>These pages follow the Chapter 11 training document (<code>GIS_Phase_1_Chapter_11_Spatial_Analysis_Fundamentals.md</code>). Every number shown here is calculated live in your browser from the practice coordinates and matches the document’s hand-checked answers. The software steps in 11.9 are written from the official ArcGIS Pro and QGIS 3.44 documentation and have <strong>not</strong> been run by the author — the lab page says what your instructor must check first.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  document.querySelector("#reqTable tbody").innerHTML = REQUESTS.map(r => {
    const d = distToLine(r.x, r.y, ROADS.R1.pts);
    return `<tr><td class="mono">${r.id}</td><td class="mono">(${r.x}, ${r.y})</td><td>${r.status}</td><td class="mono">${r.date}</td><td>${r.where}</td><td class="mono">${fmt(d)} m</td></tr>`;
  }).join("");
  const m = gridMap(document.getElementById("heroFig"), { extent: { x1: -350, y1: -250, x2: 2350, y2: 1150 }, caption: "The training grid. Wards A and B, Road R1 and requests P1–P6. Made-up practice data, metres, no CRS." });
  drawWards(m, WARDS.slice(0, 2)); drawRoad(m, ROADS.R1); drawRequests(m, REQUESTS, r => r.status === "CLOSED" ? "out" : "");
  const prog = getProgress();
  const blurbs = {
    "11.1": "Decision, study area, time window, units, output kind, checks — and the worksheet row you should delete.",
    "11.2": "Draw “within 300 m of the road”. Move the slider and watch who falls in and out — and why a buffer is not a travel time.",
    "11.3": "Cut a road at the ward boundary. Keep the part, keep the attributes — and notice the length that is now wrong.",
    "11.4": "Build the overlap and split records. Two records from one road, and the copied number you must never add up.",
    "11.5": "GROUP BY for shapes: wards become zones, one zone comes out in two pieces, and the table loses columns.",
    "11.6": "Count complaints per ward. Keep the zeros, find the one counted twice, and reconcile every ID.",
    "11.7": "A 3×3 grid with one missing cell: mean 20, or 17.78, or −1093? Then a threshold mask that is not a flood map.",
    "11.8": "Invariants, spot-checks, change-one-thing, and the seven items a log needs so someone else can rerun it.",
    "11.9": "Filter, buffer, count per ward, reconcile — first on the paper grid, then on real-shaped Earth data in UTM.",
    "11.10": "Six concept questions, two scenarios, a practical on a new canal fixture, and one spoken explanation.",
    "11.11": "The recap, the slides in brief, and how Chapter 12 will show your checked result on a map."
  };
  document.getElementById("modcards").innerHTML = MODULES.map(m => `<a class="modcard ${prog[m.id] ? "done" : ""}" href="${m.file}"><span class="done-badge">done</span><div class="id">MODULE ${m.id}</div><h3>${m.title}</h3><p>${blurbs[m.id]}</p></a>`).join("");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
