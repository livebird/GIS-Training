<?php $page = ['title' => 'Chapter 3 — Geographic data: features, geometry, attributes, and layers', 'chapter' => 3]; require __DIR__ . '/../partials/head.php'; ?>
  <section class="hero">
    <div class="fade-up">
      <div class="page-head" style="padding:0">
        <div class="eyebrow">GIS Phase 1 · Chapter 3 · Interactive tutorial</div>
      </div>
      <h1 class="big">What is actually<br><em>inside</em> a map layer?</h1>
      <p class="lead">Chapter 2 showed you <em>where</em> data lives and <em>who</em> serves it. This chapter opens the box. You will see that a map layer is made of <strong>features</strong> — each one a <strong>shape</strong> (a point, a line or an area) plus a <strong>row of facts</strong> about it — and you will learn to tell the stored data apart from the way it is drawn.</p>
      <p><a class="btn primary" href="m1-representation.php">Begin module 3.1 →</a> &nbsp; <a class="btn ghost" href="glossary.php">Glossary</a></p>
      <p class="small">No software is needed for modules 3.1–3.6. The lab in 3.7 uses ArcGIS Pro or QGIS, with a paper route if you have neither. Every coordinate, name and date on these pages is <span class="synthetic">made-up practice material</span> on a flat grid measured in metres — no real town. Your progress is saved in this browser only.</p>
    </div>
    <figure class="map-fig fade-up" id="heroFig"></figure>
  </section>

  <h2 style="border-top:0;margin-top:1rem"><span class="mod">The big idea</span>Shape + facts = one feature</h2>
  <div class="grid-2">
    <div class="card">
      <p>Think of a municipal register of streetlights. Each streetlight has a <strong>row</strong>: its ID, when it was installed, its condition. A GIS adds one special column to that row — the <strong>shape</strong> (in this case, a point on the ground). Row + shape = one <strong>feature</strong>.</p>
      <p>Everything in this chapter follows from that:</p>
      <ol>
        <li>A real thing can be stored as different shapes for different jobs (<strong>3.1</strong>).</li>
        <li>Shapes are built from <strong>vertices</strong> — stored positions (<strong>3.2</strong>).</li>
        <li>Some shapes have several parts, or holes; and different file formats write them differently (<strong>3.3</strong>).</li>
        <li>The facts live in an <strong>attribute table</strong>; the map and the table are two views of the same rows (<strong>3.4</strong>).</li>
        <li>A <strong>layer</strong> is how a stored dataset is <em>used</em> in a map; two layers can show one dataset in two ways (<strong>3.5</strong>).</li>
        <li>Before you trust a dataset: count it, check its shape type, find rows with no shape, and predict what an edit changes (<strong>3.6</strong>).</li>
      </ol>
    </div>
    <div class="card">
      <h4 style="margin-top:0">Our practice town grows</h4>
      <p>Same imaginary municipal office as Chapters 1–2: Ward A and Ward B (each 1 km × 1 km), Main Road R1, six citizen <strong>requests</strong> P1–P6, and three <strong>assets</strong>.</p>
      <p>New this chapter, so that you can see every kind of shape:</p>
      <ul>
        <li><strong>R2 Station Road</strong> — a road with a bend, and <strong>L1</strong> — a bus loop that closes on itself.</li>
        <li>A <strong>school</strong> stored twice: once as a dot, once as its building outline.</li>
        <li><strong>PK-01 Riverside Park</strong> — one park in two separate pieces.</li>
        <li><strong>DP-01 Central depot</strong> — a compound with an open courtyard (a hole) and a tree standing in it.</li>
        <li><strong>P7</strong> — a complaint whose location is not known yet.</li>
        <li>An <strong>inspections</strong> table with no shapes at all.</li>
      </ul>
      <p class="small">Coordinates are written (x, y) in metres on graph paper — x to the right, y upward. There is no latitude, longitude or “coordinate system” yet; that comes in Chapter 5.</p>
    </div>
  </div>

  <h2><span class="mod">Modules</span>Work through them in order</h2>
  <div class="modcards" id="modcards"></div>

  <h2><span class="mod">Plain words</span>A few words you will see everywhere</h2>
  <div class="card"><table>
    <tr><th>Feature</th><td>One stored thing: a shape plus its row of facts. A streetlight, a ward, a complaint.</td></tr>
    <tr><th>Geometry</th><td>The shape part of a feature — a point, a line or an area (polygon) built from stored positions.</td></tr>
    <tr><th>Vertex</th><td>One stored position, written (x, y). Lines and areas are lists of vertices. Plural: vertices.</td></tr>
    <tr><th>Attribute</th><td>One fact about a feature, kept in a column: <code>status</code>, <code>priority</code>, <code>installed</code>.</td></tr>
    <tr><th>Attribute table</th><td>All the rows of one dataset, one row per feature — like a spreadsheet with a hidden “shape” column.</td></tr>
    <tr><th>Dataset</th><td>The stored collection of features (a file or a database table).</td></tr>
    <tr><th>Layer</th><td>A dataset as used in one map: it points at the dataset and adds colours, labels and filters. It does not hold a copy.</td></tr>
    <tr><th>Extent</th><td>The smallest rectangle that contains a shape or a whole dataset — four numbers, not the real outline.</td></tr>
  </table></div>

  <h2><span class="mod">Outcomes</span>What you will be able to do</h2>
  <div class="card">
    <table>
      <tr><th>LO1</th><td>Choose a point, a line or an area for a stated job and scale; tell an asset from a complaint about it and from an inspection of it; accept that a ward is a feature even though nobody can see it on the ground.</td></tr>
      <tr><th>LO2</th><td>Describe shapes as vertices and segments; explain why the thickness of a drawn line says nothing about the real width of a road; say what X, Y, Z and M are.</td></tr>
      <tr><th>LO3</th><td>Recognise multipart shapes and holes; tell a polygon ring from a line that merely closes; read a GeoJSON shape and state its rules.</td></tr>
      <tr><th>LO4</th><td>Use the words record, field, value, identifier and attribute table correctly; find a selected feature’s row; explain why a label is not an ID and why inspection history stays in its own table.</td></tr>
      <tr><th>LO5</th><td>Tell a dataset from a layer from a map; show one dataset two ways; tell a filtered view from an exported copy; explain how drawing order hides things.</td></tr>
      <tr><th>LO6</th><td>Read an extent as a box; record feature count, shape type, field names and rows with no shape; predict which stored rows change before you act.</td></tr>
      <tr><th>LO7</th><td>Inspect a small schematic town end to end and prove that changing how a layer looks did not change its feature count.</td></tr>
    </table>
  </div>
  <p class="small">Source: <em>GIS_Phase_1_Chapter_03_Geographic_Data_Features_Geometry_Attributes_and_Layers.md</em>, revision 1.0 (19 September 2026). This tutorial follows that document; formal assessment answers are held by the instructor and are not on these pages. Software statements were checked against official documentation on that date and can change.</p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  renderTown(document.getElementById("heroFig"), { caption: "The Chapter 3 practice town (schematic). Made-up grid in metres; no real place." });
  const prog = getProgress();
  const blurbs = {
    "3.1": "One school, two shapes. Asset vs complaint vs inspection. What a feature is — and is not.",
    "3.2": "Points, lines, areas as lists of vertices. Why a thick drawn line is not a wide road. X, Y, Z, M.",
    "3.3": "A park in two pieces, a depot with a courtyard, a loop that is not an area. WKT, GeoJSON, Esri JSON.",
    "3.4": "Rows, columns, values, IDs. Click a dot, find its row. Style, filter and label are three different things.",
    "3.5": "A dataset is stored; a layer uses it. Two layers, one source. View vs copy. Drawing order hides dots.",
    "3.6": "Extent is a box. Count, type, fields, rows with no shape. Predict before you edit.",
    "3.7": "Inspect the whole town: justify shapes, record counts, make two presentations, find the unsuitable layer.",
    "3.8": "The assessment you submit to your instructor, and the progression rule.",
    "3.9": "Recap, media notes, and the question that leads into Chapter 4."
  };
  document.getElementById("modcards").innerHTML = MODULES.map(m => `<a class="modcard ${prog[m.id] ? "done" : ""}" href="${m.file}"><span class="done-badge">done</span><div class="id">MODULE ${m.id}</div><h3>${m.title}</h3><p>${blurbs[m.id]}</p></a>`).join("");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
