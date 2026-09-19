<?php $page = ['title' => 'Chapter 4 — Raster data and geographic surfaces', 'chapter' => 4]; require __DIR__ . '/../partials/head.php'; ?>
  <section class="hero">
    <div class="fade-up">
      <div class="page-head" style="padding:0">
        <div class="eyebrow">GIS Phase 1 · Chapter 4 · Interactive tutorial</div>
      </div>
      <h1 class="big">A map made of<br><em>numbers in a grid</em></h1>
      <p class="lead">Chapter 3 showed you points, lines and areas — good for things with edges, like a streetlight, a road or a ward. But how do you store something with <strong>no edges</strong>: how high the ground is, how much rain fell, what covers the land? The answer is a <strong>raster</strong>: cut the area into equal squares and put one number in each square. This chapter teaches you to read those numbers correctly — and not to be fooled by the colours.</p>
      <p><a class="btn primary" href="m1-grid.php">Begin module 4.1 →</a> &nbsp; <a class="btn ghost" href="glossary.php">Glossary</a></p>
      <p class="small">No account, no licence and no download is needed for modules 4.1–4.6. The lab (4.7) uses ArcGIS Pro or the free QGIS, and you can still do most of it on paper. Every grid, number and file name here is <span class="synthetic">made-up practice material</span>. Your progress is saved in this browser only.</p>
    </div>
    <figure class="raster-fig fade-up" id="heroFig"></figure>
  </section>

  <h2 style="border-top:0;margin-top:1rem"><span class="mod">The big idea</span>Numbers first, colours later</h2>
  <div class="grid-2">
    <div class="card">
      <p>Look at the small grid above. It has <strong>3 rows and 3 columns</strong>. Each square (a <strong>cell</strong>) holds one number. The middle cell holds nothing — it is marked <strong>NoData</strong>, which means “we do not know”.</p>
      <p>That is the whole raster model. Everything in this chapter is about four questions:</p>
      <ol>
        <li><strong>What does the number mean?</strong> A height in metres? A code for “water”? You cannot tell by looking.</li>
        <li><strong>How big is one cell, and where is the grid?</strong> Ten cells of 10 m each make 100 m.</li>
        <li><strong>What about the empty cells?</strong> “No data” is not the same as “zero”.</li>
        <li><strong>What happens when you change the grid?</strong> Making the squares smaller does not add any new information.</li>
      </ol>
    </div>
    <div class="card">
      <h4 style="margin-top:0">Our practice town continues</h4>
      <p>Same imaginary city office (nagar palika) as before: two wards, one road, six citizen <strong>requests</strong>, three <strong>assets</strong>. All on the flat practice grid measured in metres — like graph paper, with no real-world location.</p>
      <p>New this chapter, two small rasters covering Ward A:</p>
      <ul>
        <li><strong>Land cover</strong> — a 10 × 10 grid of 100 m cells. Each cell holds a code: 0 = water, 1 = built-up, 2 = vegetation (green cover), 3 = bare ground. Three cells are NoData (hidden by cloud in the source photo).</li>
        <li><strong>Elevation</strong> — a 4 × 4 grid of 100 m cells in the north-west corner. Each cell holds the ground height in metres above a made-up zero level called <strong>TD-0</strong>. One cell is NoData.</li>
      </ul>
      <p class="small">Both are plain text files you can type yourself; the lab shows how.</p>
    </div>
  </div>

  <h2><span class="mod">Modules</span>Work through them in order</h2>
  <div class="modcards" id="modcards"></div>

  <h2><span class="mod">Plain words</span>A few words you will see everywhere</h2>
  <div class="card"><table>
    <tr><th>Raster</th><td>A grid of equal squares (cells) where each cell holds a number. Think of a chessboard where every square has a value written on it.</td></tr>
    <tr><th>Cell (pixel)</th><td>One square of the grid. It covers an area on the ground — say 100 m × 100 m — and holds one number for that whole area.</td></tr>
    <tr><th>Cell size (resolution)</th><td>How wide one cell is on the ground. Smaller cells = finer detail = bigger file. It does <em>not</em> tell you how accurate the data is.</td></tr>
    <tr><th>Band</th><td>One complete grid of numbers. A colour photo has three bands (red, green, blue). An elevation grid has one.</td></tr>
    <tr><th>NoData</th><td>A cell that has no value — “we don’t know”. Not zero. Like a blank in a register, not a 0.</td></tr>
    <tr><th>Resampling</th><td>Making a new grid with a different cell size from an old one. The software must <em>decide</em> each new number by a rule (a method).</td></tr>
    <tr><th>Elevation surface</th><td>A raster whose numbers are heights. One height per cell — like a tray of ice-cube boxes filled to different levels.</td></tr>
  </table></div>

  <h2><span class="mod">Outcomes</span>What you will be able to do</h2>
  <div class="card">
    <table>
      <tr><th>LO1</th><td>Describe a raster as rows and columns of cells with values, find a cell from a coordinate, and say when a grid is a better fit than points and polygons — and when it is not.</td></tr>
      <tr><th>LO2</th><td>Tell a stored value from its display colour, explain one-band and multi-band rasters, and say what dictionary or unit you need before a value means anything.</td></tr>
      <tr><th>LO3</th><td>Work out a raster’s extent from its origin, cell size and dimensions; explain why cell size is not accuracy; spot two grids that do not line up.</td></tr>
      <tr><th>LO4</th><td>Tell zero, NoData, a mask and transparent display apart; compute a mean under a stated missing-data rule; separate “no coverage” from “nothing observed”.</td></tr>
      <tr><th>LO5</th><td>Explain why resampling needs a method, choose nearest neighbour for categories and an interpolating method for continuous surfaces, and explain why smaller cells do not add detail.</td></tr>
      <tr><th>LO6</th><td>Describe an elevation raster as a height field, tell a terrain surface from one that includes buildings and trees, say why heights need a unit and a zero level, and tell a shaded picture from the numbers under it.</td></tr>
      <tr><th>LO7</th><td>Inspect two rasters, change styling without changing values, resample copies, and record the numerical effect.</td></tr>
    </table>
  </div>
  <p class="small">Source: <em>GIS_Phase_1_Chapter_04_Raster_Data_and_Geographic_Surfaces.md</em>, revision 1.0 (19 September 2026). This tutorial follows that document; formal assessment answers are held by the instructor and are not on these pages. Software statements were checked against official documentation on that date and can change.</p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  renderRaster(document.getElementById("heroFig"), F7, { renderer: "none", rowLabels: true, cellPx: 120, caption: "A raster is numbers first. 3 rows × 3 columns; the middle cell is NoData (made-up grid from the chapter)." });
  const prog = getProgress();
  const blurbs = {
    "4.1": "Rows, columns, cells, values. Objects with edges vs surfaces without. Which model fits which question.",
    "4.2": "A blue cell can mean four different things. One band vs many bands. What you need before a number means anything.",
    "4.3": "Cell size, extent and finding a cell from x, y. Ten 10 m cells = 100 m. Resolution is not accuracy. Grids that don’t line up.",
    "4.4": "Zero, NoData, mask, transparent — four different things. Watch the mean change when ‘blank’ is treated as 0.",
    "4.5": "Why a new grid needs a rule. Nearest neighbour vs bilinear. Why averaging category codes goes wrong. Smaller cells ≠ more detail.",
    "4.6": "An elevation grid as a height field. Terrain vs surface with buildings. Units and the zero level. A shaded picture is not proof.",
    "4.7": "Type two small rasters, load them, inspect, style, resample on copies and record the numbers — in ArcGIS Pro or QGIS.",
    "4.8": "The assessment you submit to your instructor, and the progression rule.",
    "4.9": "Recap, media notes, and the question that leads into Chapter 5."
  };
  document.getElementById("modcards").innerHTML = MODULES.map(m => `<a class="modcard ${prog[m.id] ? "done" : ""}" href="${m.file}"><span class="done-badge">done</span><div class="id">MODULE ${m.id}</div><h3>${m.title}</h3><p>${blurbs[m.id]}</p></a>`).join("");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
