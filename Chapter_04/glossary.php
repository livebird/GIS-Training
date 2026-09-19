<?php $page = ['title' => 'Chapter 4 glossary', 'chapter' => 4]; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Chapter 4</div>
    <h1>Glossary</h1>
    <p class="lead">Terms as used in this chapter, in plain words. Tags: <span class="pill">Esri</span> <span class="pill">QGIS</span> <span class="pill">format</span> — untagged terms are general ideas.</p>
  </div>
  <input class="gloss-search" id="q" type="search" placeholder="Type to filter… e.g. NoData, band, bilinear, DTM" aria-label="Filter glossary">
  <dl class="gloss" id="gloss"></dl>
  <p style="margin-top:2rem"><a class="btn ghost" href="./">← Chapter home</a></p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const E = ' <span class="pill">Esri</span>', Q = ' <span class="pill">QGIS</span>', F = ' <span class="pill">format</span>';
  const terms = [
    ["Alignment (of grids)", "Two rasters line up when their cell edges coincide — same cell size, and origins that differ by a whole number of cells. Only then can you compare them cell by cell without resampling. (4.3.3)"],
    ["Band", "One complete grid of numbers. A colour photo has three bands (red, green, blue); an elevation grid has one. Every band of a raster has the same rows, columns, cell size and extent. (4.2.2)"],
    ["Bilinear interpolation", "A resampling method that sets each new cell to a distance-weighted average of the four nearest old cell centres. Smooth; fine for continuous surfaces; wrong for category codes. (4.5.1)"],
    ["Categorical (thematic, discrete) raster", "A raster whose numbers are codes for classes — land cover, soil type. You can count and measure area per class; you cannot average codes. (4.1.2)"],
    ["Cell (pixel)", "One square of the grid. It covers an area on the ground and holds one number per band. (4.1.1)"],
    ["Cell size (pixel size)", "How wide one cell is on the ground, in the raster’s units — 100 m in our fixtures. Same thing as spatial resolution. Not the same as accuracy. (4.3.1)"],
    ["Continuous raster", "A raster whose numbers are measured amounts with units — height, rainfall, temperature. Averaging and smoothing make sense. (4.1.2)"],
    ["Cubic convolution", "A resampling method fitting a smooth curve through the 16 nearest old centres. Smoothest result; can produce values outside the input range. (4.5.1)"],
    ["DEM / DTM / DSM", "Digital elevation model (generic); digital terrain model (bare ground, no buildings or trees); digital surface model (top of everything, including buildings and trees). Which one a file is must come from its own documentation — the names are not standard. (4.6.1)"],
    ["Display resampling", "The resampling a viewer does while drawing, whenever you zoom. Changes only the picture on screen, never the file. (4.5.1)" + Q + E],
    ["Esri ASCII raster (.asc)" + F, "A plain-text raster format: six header lines (columns, rows, lower-left corner, cell size, NoData value) then the values row by row, top row first. Used for the fixtures because you can type it."],
    ["Extent", "The rectangle a raster covers — left, right, bottom, top — computed as origin + count × cell size. (4.3.2)"],
    ["Georeferencing", "The information that places a raster’s rows and columns at world coordinates (a corner coordinate, cell size, rotation, and a coordinate system) — or the process of establishing it for a scan that has none. Absent from our fixtures on purpose. (4.3.2, S2)"],
    ["Height field", "A surface with exactly one height per (x, y) cell. Cannot hold a flyover, a tunnel, or the inside of a building. (4.6.1)"],
    ["Hillshade", "A grey-scale picture of an elevation surface lit from a chosen direction. Derived from the heights; holds no heights itself; changes with the light and with vertical exaggeration. (4.6.3)"],
    ["Majority / mode resampling", "A resampling method that gives each new cell the most common old value nearby. Suits categories, especially when making cells bigger. (4.5.2)" + E],
    ["Mask", "A processing-time instruction to ignore some cells (often defined by another layer or a boundary). Not stored in the raster’s values. (4.4.1)"],
    ["Missing-data rule (policy)", "What an operation does with NoData cells: leave them out, return NoData, or estimate. Must be stated with every statistic. (4.4.2)"],
    ["Nearest neighbour", "A resampling method that copies the value of the nearest old cell centre. Creates no new values; the usual choice for categories. (4.5.1)"],
    ["NoData", "A cell that has no value — “we do not know”. Stored as a reserved number (−9999, −1) or a mask. Not zero. (4.4.1)"],
    ["Origin", "The reference corner of a raster. In .asc files, the lower-left corner of the lower-left cell. (4.3.2)"],
    ["Pixel type / pixel depth", "How the numbers are stored: signed or unsigned, integer or floating point, and how many bits. A hint about meaning, not a dictionary. (4.2.3)" + E],
    ["Raster", "The grid data model: rows and columns of cells with values, positioned by a header. (4.1.1)"],
    ["Renderer (symbology type)", "The rule that turns cell values into colours — unique values, classified, stretched, colour map, RGB composite, hillshade. A layer setting; the file is untouched. (4.2.1)" + E + Q],
    ["Resampling", "Making a new raster on a different grid (cell size, origin or coordinate system) from an old one. Needs a method to decide each new value. (4.5)"],
    ["Row / column", "The two numbers that address a cell. Rows are listed from the top, so row 1 is the north edge once the raster is placed. (4.1.1, 4.3.2)"],
    ["Spatial resolution", "See cell size. Not the same as positional accuracy. (4.3.1)"],
    ["Spectral resolution", "The number of bands an image has and the wavelength ranges they cover. (4.2.2)"],
    ["Stretch", "A colour rule that spreads a range of values along a colour ramp. The range you choose changes the picture, not the data. (4.2.1)" + E],
    ["Vertical reference (vertical coordinate system)", "The zero level and direction (up or down) for heights, with a linear unit such as metres or feet. Detail in Chapter 5. (4.6.2)"],
    ["World file" + F, "A small text file holding the numbers that place an image on a map, for formats that do not keep georeferencing in their header. (4.3.2, S2)" + E],
    ["Z-factor", "A multiplier applied to heights — to convert their unit to the horizontal unit, or to exaggerate relief for display. (4.6.2, 4.6.3)" + E]
  ];
  terms.sort((a, b) => a[0].replace(/<[^>]+>/g, "").localeCompare(b[0].replace(/<[^>]+>/g, "")));
  const g = document.getElementById("gloss");
  function render(f) { g.innerHTML = terms.filter(t => !f || (t[0] + " " + t[1]).toLowerCase().includes(f)).map(t => `<dt>${t[0]}</dt><dd>${t[1]}</dd>`).join("") || "<dd>No matching term.</dd>"; }
  document.getElementById("q").addEventListener("input", e => render(e.target.value.trim().toLowerCase()));
  render("");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
