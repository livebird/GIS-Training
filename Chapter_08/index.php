<?php $page = ['title' => 'Chapter 8 — GIS data modeling and relationships', 'chapter' => 8]; require __DIR__ . '/../partials/head.php'; ?>
  <section class="hero">
    <div class="fade-up">
      <div class="page-head" style="padding:0">
        <div class="eyebrow">GIS Phase 1 · Chapter 8 · Interactive tutorial</div>
      </div>
      <h1 class="big">One streetlight.<br>Many <em>visits</em>.</h1>
      <p class="lead">Chapter 7 showed you the <em>boxes</em> data comes in — CSV, GeoJSON, Shapefile, GeoPackage, geodatabase. This chapter is about what goes <em>inside</em> the box: which tables you need, what one row means, how a row gets its name, and how rows in different tables point to each other. In plain words: how to design the data so that a streetlight inspected ten times has ten records, not one overwritten row.</p>
      <p><a class="btn primary" href="m1-entities">Begin module 8.1 →</a> &nbsp; <a class="btn ghost" href="glossary">Glossary</a></p>
      <p class="small">No software is needed for any module in this chapter — the lab in 8.8 is done on paper or in a spreadsheet, and every demo runs in this page. All assets, wards, crews and dates here are <span class="synthetic">made-up practice data</span> — no real town or office. Your progress is saved in this browser only.</p>
    </div>
    <figure class="map-fig fade-up" id="heroFig"></figure>
  </section>

  <h2 style="border-top:0;margin-top:1rem"><span class="mod">The big idea</span>A table is a promise about what one row means</h2>
  <div class="grid-2">
    <div class="card">
      <p>Think of a school register. One register lists <em>students</em> (one row per student). Another lists <em>attendance</em> (one row per student per day). Nobody would put 200 “Day 1 … Day 200” columns on the student register — yet that is exactly what many asset tables do with inspections.</p>
      <p>Everything in this chapter follows from that one idea:</p>
      <ol>
        <li>Decide which <strong>kinds of things</strong> you record — assets, inspections, requests, wards, teams — and what <strong>one row</strong> of each means (<strong>8.1</strong>).</li>
        <li>Write down the <strong>shape rules</strong> for anything with a location: what kind of shape, in which grid, and what the dot actually stands for (<strong>8.2</strong>).</li>
        <li>Describe every <strong>field</strong> — its type, unit, and what a blank means — and fix one rule for dates and times (<strong>8.3</strong>).</li>
        <li>Give every row a <strong>stable name</strong> that is not its display label and not a number the software made up (<strong>8.4</strong>).</li>
        <li>Connect tables with <strong>keys</strong>, and know the difference between drawing a line on a diagram and a rule the software actually enforces (<strong>8.5</strong>).</li>
        <li>Limit values with <strong>allowed lists and ranges</strong>, and never let a default pretend to be an observation (<strong>8.6</strong>).</li>
        <li>Keep <strong>history</strong>, store <strong>photos</strong> as rows, find <strong>orphans</strong>, and change the design carefully (<strong>8.7</strong>). Then fix a really bad table in the lab (<strong>8.8</strong>).</li>
      </ol>
    </div>
    <div class="card">
      <h3 style="margin-top:0">Before you start</h3>
      <p>You should be comfortable with these ideas from Chapters 3 and 7. If any feels new, revise it first.</p>
      <ul>
        <li>A <strong>feature</strong> = a shape (point, line or polygon) plus its describing values (<strong>attributes</strong>).</li>
        <li>A table has <strong>rows</strong> (records) and <strong>columns</strong> (fields).</li>
        <li>A display label (“Streetlight near market”) is <em>not</em> automatically the record’s ID.</li>
        <li>A Shapefile cannot store a blank number or a time of day; field names get cut to 10 letters.</li>
        <li>A geodatabase is storage <em>plus</em> rules; a GeoPackage is a SQLite file with tables inside.</li>
        <li>Every coordinate needs its grid (CRS), unit and order stated — Chapter 5. Here we only <em>write that down</em>; we do not choose projections.</li>
      </ul>
      <div class="callout dev" style="margin-bottom:0"><span class="label">Developer view</span><p>If you have designed a database with tables, primary keys and foreign keys, most of this chapter is familiar ground. The GIS twist is one special column — the <strong>shape</strong> — and the fact that GIS data is copied around a lot (exports, field apps, web layers), so “the auto-number is my ID” breaks much sooner than in a normal app.</p></div>
    </div>
  </div>

  <h2><span class="mod">This chapter’s practice data</span>The same small town — now with crews and visit history</h2>
  <p>We keep the flat practice grid from Chapter 1 (metres, no real place): Wards A and B, road R1, three assets. Two things are new. A <strong>Team</strong> table (two crews), and an <strong>inspection history</strong> — four visits to two assets, with the time of day written in <strong>IST</strong> (Indian Standard Time) as a crew would write it in a notebook.</p>
  <div class="grid-2">
    <div>
      <div class="table-wrap"><table>
        <thead><tr><th>Asset</th><th>Type</th><th>(x, y) metres</th><th>Installed</th></tr></thead>
        <tbody id="assetRows"></tbody></table></div>
      <div class="table-wrap"><table>
        <thead><tr><th>Team</th><th>Name</th></tr></thead>
        <tbody><tr><td class="mono">T-N</td><td>North crew</td></tr><tr><td class="mono">T-S</td><td>South crew</td></tr></tbody></table></div>
    </div>
    <div class="table-wrap"><table id="inspTable">
      <thead><tr><th>Visit</th><th>Asset</th><th>Date &amp; time (IST)</th><th>Team</th><th>Condition 1–5</th><th>Defects</th><th>Remarks</th></tr></thead>
      <tbody></tbody></table>
      <p class="small">Condition scale: 1 = Very poor, 2 = Poor, 3 = Fair, 4 = Good, 5 = Very good. Look at INS-0004: it is the <em>oldest</em> visit but has the <em>highest</em> number — its paper form was found and typed in late. Module 8.7 uses this.</p>
    </div>
  </div>

  <h2><span class="mod">Modules</span>Work through them in order</h2>
  <div class="modcards" id="modcards"></div>

  <div class="callout note"><span class="label">What this tutorial is based on</span><p>These pages follow the Chapter 8 training document (<code>GIS_Phase_1_Chapter_08_GIS_Data_Modeling_and_Relationships.md</code>). Statements about ArcGIS, QGIS and PostgreSQL behaviour were read from the official documentation on 19 September 2026 (ArcGIS Pro 3.7, QGIS 3.40). The optional software steps mentioned in the lab have <strong>not</strong> been run by the author; the lab itself needs no software.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  document.getElementById("assetRows").innerHTML = F8.assets.map(a => `<tr><td class="mono">${a.id}</td><td>${a.typeName}</td><td class="mono">(${a.x}, ${a.y})</td><td>${a.year}</td></tr>`).join("");
  document.querySelector("#inspTable tbody").innerHTML = F8.inspections.map(i => `<tr><td class="mono">${i.id}</td><td class="mono">${i.asset}</td><td class="mono">${i.ist}</td><td class="mono">${i.team}</td><td>${i.cond} (${F8.condWords[i.cond]})</td><td>${i.defects}</td><td>${i.remarks ?? '<span class="blank">— none —</span>'}</td></tr>`).join("");
  renderGrid(document.getElementById("heroFig"), { requests: true, caption: "The practice town: two wards, one road, three assets (squares) and three requests (dots). Made-up data on a flat metre grid." });
  const prog = getProgress();
  const blurbs = {
    "8.1": "Assets, inspections, requests, wards, teams — five different kinds of thing. What one row means, and why a field must earn its place.",
    "8.2": "Point, line or polygon; which grid; and what the dot stands for — the pole base, the lamp head, or where someone stood.",
    "8.3": "A data dictionary. Four different kinds of ‘blank’. Why codes are text. One rule for IST and UTC.",
    "8.4": "Business ID, display label, and the software’s own row number are three things. What ObjectID and GlobalID do and do not promise.",
    "8.5": "One-to-many, one-to-one, many-to-many — with keys. A diagram, a join, and a rule the software enforces are not the same.",
    "8.6": "Allowed lists (code vs description), allowed ranges, defaults that lie, and where each rule should live.",
    "8.7": "Current state vs history. Visit time vs typing time. Photos as rows. Finding orphans. A checklist before changing a table.",
    "8.8": "Take a truly bad single table and redesign it: diagram, dictionary, relationships, ten records, three questions.",
    "8.9": "Six concept questions, two scenarios, a practical on bus shelters, and one spoken explanation.",
    "8.10": "The recap, the media brief, and what Chapter 9 does with your design."
  };
  document.getElementById("modcards").innerHTML = MODULES.map(m => `<a class="modcard ${prog[m.id] ? "done" : ""}" href="${m.file}"><span class="done-badge">done</span><div class="id">MODULE ${m.id}</div><h3>${m.title}</h3><p>${blurbs[m.id]}</p></a>`).join("");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
