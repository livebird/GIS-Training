<?php $page = ['title' => 'Chapter 7 — GIS data formats, sources, and metadata', 'chapter' => 7]; require __DIR__ . '/../partials/head.php'; ?>
  <section class="hero">
    <div class="fade-up">
      <div class="page-head" style="padding:0">
        <div class="eyebrow">GIS Phase 1 · Chapter 7 · Interactive tutorial</div>
      </div>
      <h1 class="big">Same data,<br>different <em>boxes</em>.</h1>
      <p class="lead">Chapters 3–6 were about what is <em>inside</em> geographic data: shapes, values, coordinates and their reference systems. This chapter is about the <strong>box</strong> the data travels in — a CSV, a GeoJSON file, a Shapefile, a GeoPackage, a geodatabase, or a web service — and the one thing every developer learns the hard way: <strong>every box keeps some of the meaning and quietly drops the rest.</strong></p>
      <p><a class="btn primary" href="m1-layers">Begin module 7.1 →</a> &nbsp; <a class="btn ghost" href="glossary">Glossary</a></p>
      <p class="small">No software is needed for modules 7.1–7.7; every demo runs in this page. The lab in 7.8 uses ArcGIS Pro or QGIS, with a paper route if you have neither. All assets, wards and notes here are <span class="synthetic">made-up practice data</span> — no real town or office. Your progress is saved in this browser only.</p>
    </div>
    <figure class="map-fig fade-up" id="heroFig"></figure>
  </section>

  <h2 style="border-top:0;margin-top:1rem"><span class="mod">The big idea</span>A courier parcel, not a magic box</h2>
  <div class="grid-2">
    <div class="card">
      <p>Think of sending a wedding gift by courier. The gift is the same whatever box you use — but a thin envelope will not carry a glass bowl, a plastic bag will not keep a sweet box dry, and a sealed carton hides what is inside until you open it. <strong>A data format is a box.</strong> Some boxes cannot carry a null value, some cannot carry Gujarati text, some cannot carry the time of day, and some have no place to write which coordinate system the numbers are in.</p>
      <p>Everything in this chapter follows from that:</p>
      <ol>
        <li>Separate the <em>thing</em> (vector or raster), the <em>way it is written</em> (encoding), the <em>box it travels in</em> (container) and the <em>counter you collect it from</em> (service) — and never trust a file name extension (<strong>7.1</strong>).</li>
        <li>The two simplest boxes, <strong>CSV</strong> and <strong>GeoJSON</strong>, are the ones with the most silent mistakes (<strong>7.2</strong>).</li>
        <li>A <strong>Shapefile</strong> is five files pretending to be one, with 1990s limits you must predict before exporting (<strong>7.3</strong>).</li>
        <li><strong>GeoPackage</strong> is an SQLite database with rules; <strong>GeoTIFF</strong> is a picture with a position stapled on — check both before use (<strong>7.4</strong>).</li>
        <li>An ArcGIS <strong>geodatabase</strong> is storage <em>plus</em> rules; PostGIS is not one automatically; ArcGIS Data Store is something else again (<strong>7.5</strong>).</li>
        <li>Every dataset comes with a <strong>story</strong> — who, when, how accurate, allowed for what. Write it down before you use the data (<strong>7.6</strong>).</li>
        <li>Snapshot before, convert, snapshot after, and sort every difference into <em>“I decided this”</em> or <em>“this was lost”</em> (<strong>7.7</strong>). The lab makes you do it (<strong>7.8</strong>).</li>
      </ol>
    </div>
    <div class="card">
      <h3 style="margin-top:0">Before you start</h3>
      <p>You should be comfortable with these ideas from Chapters 3–6. If any feels new, revise it first.</p>
      <ul>
        <li>A <strong>feature</strong> = a shape (point, line, polygon) + its attributes (Chapter 3). A <strong>raster</strong> = a grid of cells with values and a NoData rule (Chapter 4).</li>
        <li>A coordinate is meaningless without its <strong>CRS</strong>, <strong>units</strong> and <strong>order</strong>; GeoJSON writes longitude first (Chapter 5).</li>
        <li><strong>Assigning</strong> a CRS changes only a label; <strong>transforming</strong> changes the numbers (Chapter 6). You kept a log in the 6.8 lab — bring it.</li>
        <li>Everyday developer things: files and folders, UTF-8, JSON, CSV, SQLite or another database, primary keys, null.</li>
      </ul>
      <div class="callout dev" style="margin-bottom:0"><span class="label">Developer view</span><p>Model → encoding → container → service is roughly <em>domain object → serialization format → database/file system → API</em>. The twist in GIS: the <em>serialization format</em> often decides which coordinate systems and which value types are even allowed. Choosing JSON over XML never changed what an invoice is. Choosing GeoJSON over GeoPackage changes what your coordinates may be.</p></div>
    </div>
  </div>

  <h2><span class="mod">This chapter’s practice data</span>Table F7 — six assets built to break things</h2>
  <p>The same imaginary town as Chapters 1–3, on the flat metre grid with <strong>no real-world location</strong>. Six assets, each field chosen to trip up at least one format: a code with a <em>leading zero</em>, a <em>null</em> score, a timestamp with India’s <em>+05:30</em> offset, Gujarati text, and seven field names longer than ten characters.</p>
  <div class="table-wrap"><table id="f7Table" class="attr"></table></div>
  <div class="grid-3">
    <div class="tile"><div class="v">6</div><div class="l">records</div></div>
    <div class="tile"><div class="v">10</div><div class="l">fields · 7 names over 10 chars</div></div>
    <div class="tile"><div class="v">3.2</div><div class="l">mean score (5 assessed)</div></div>
    <div class="tile warn"><div class="v">2.67</div><div class="l">mean if null is read as 0</div></div>
    <div class="tile"><div class="v">205–2190</div><div class="l">x range (m)</div></div>
    <div class="tile"><div class="v">195–950</div><div class="l">y range (m)</div></div>
  </div>
  <p class="small">The <code>condition_score</code> here is a decimal on the asset row; Chapter 8 will replace it with a proper inspection history. For GeoJSON only, the chapter also uses one separate made-up point <strong>SL-9001</strong> at longitude 70° E, latitude 20° N — in open sea on purpose, so it cannot be mistaken for a real streetlight.</p>

  <h2><span class="mod">Modules</span>Work through them in order</h2>
  <div class="modcards" id="modcards"></div>

  <div class="callout note"><span class="label">What this tutorial is based on</span><p>These pages follow the Chapter 7 training document (<code>GIS_Phase_1_Chapter_07_GIS_Data_Formats_Sources_and_Metadata.md</code>). Every “what will happen to this field” shown here is worked out in your browser from the published rules — RFC 7946, the OGC GeoPackage 1.4.0 standard, Esri’s shapefile and geodatabase pages, the GDAL driver pages — and matches the document. The software steps in 7.8 are written from the ArcGIS Pro 3.7 and QGIS 3.44 documentation and have <strong>not</strong> been run by the author; the lab page says what your instructor must check first.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  renderTable(document.getElementById("f7Table"), F7.fields.map(f => f.name), F7.rows);
  renderGrid(document.getElementById("heroFig"), { assets: true, caption: "Table F7 assets on the training grid. Made-up practice data; metres; no real place." });
  const prog = getProgress();
  const blurbs = {
    "7.1": "Model, encoding, container, service — four different questions hiding in “send me the assets”. Why a file extension proves nothing.",
    "7.2": "Read a coordinate CSV and a small GeoJSON by eye. Watch a leading zero vanish and a timestamp change its date.",
    "7.3": "Five files pretending to be one. Tick the companion files, then watch one record go through a shapefile export.",
    "7.4": "Open a GeoPackage like a database. Inspect a TIFF’s tags. See which properties each format has a place for.",
    "7.5": "Storage plus rules. Feature class, table, feature dataset — and why PostGIS ≠ enterprise geodatabase ≠ Data Store.",
    "7.6": "The intake form, five kinds of data, and a 2019 ward boundary that silently moves two requests.",
    "7.7": "Snapshot, convert, compare. Sort every change into “adapted on purpose” or “lost by accident”.",
    "7.8": "Choose a format for a real recipient, convert Table F7, and prove what survived.",
    "7.9": "Six concept questions, two scenarios, a practical on new data, and one spoken explanation.",
    "7.10": "The recap, the media brief, and what Chapter 8 designs inside these boxes."
  };
  document.getElementById("modcards").innerHTML = MODULES.map(m => `<a class="modcard ${prog[m.id] ? "done" : ""}" href="${m.file}"><span class="done-badge">done</span><div class="id">MODULE ${m.id}</div><h3>${m.title}</h3><p>${blurbs[m.id]}</p></a>`).join("");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
