<?php $page = ['title' => '4.9 Recap, media brief and next chapter', 'chapter' => 4, 'module' => '4.9']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 4.9 · Recap · media notes · transition</div>
    <h1>Recap, and where this leads</h1>
    <p class="lead">Seven ideas to keep, one slide-by-slide map of the chapter for anyone making a presentation or audio lesson, and the question that opens Chapter 5.</p>
  </div>

  <h2><span class="mod">Recap</span>Seven things to remember</h2>
  <div class="card"><ol>
    <li><strong>A raster is numbers in a grid</strong>, placed by a header: origin, cell size, rows and columns. Row 1 is the top.</li>
    <li><strong>Values are measurements or codes; colours are a layer’s rule.</strong> What a value <em>means</em> comes from a unit or a dictionary the file rarely contains. Never guess units from the look of a map.</li>
    <li><strong>Cell size is resolution — not accuracy</strong>, and not a promise that small things exist in the data. Ten 10 m cells span 100 m; that is extent arithmetic, nothing more.</li>
    <li><strong>Zero is a measurement; NoData is a stated absence.</strong> The elevation grid’s mean is 13.6 m excluding NoData, 12.75 m if NoData is counted as zero — so report the rule with the number.</li>
    <li><strong>Changing the grid needs a method.</strong> Nearest neighbour copies values (categories); bilinear/cubic invent smooth values (continuous surfaces). Averaging codes makes nonsense. A finer grid holds no new information.</li>
    <li><strong>An elevation raster is a height field</strong> — one height per cell. It needs a unit and a zero level; whether it is bare ground or the tops of buildings comes from the dataset’s own definition.</li>
    <li><strong>A shaded 3D picture is a picture of the numbers</strong>, not proof that the numbers are right.</li>
  </ol></div>

  <h2><span class="mod">Media brief</span>For slides and audio (mapped to modules)</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Slide group</th><th>Module</th><th>Sequence</th><th>Question to ask before revealing</th></tr></thead>
    <tbody>
      <tr><td>A. A grid of numbers</td><td>4.1</td><td>Chapter 3’s town → “what about the ground itself?” → F7 as nine numbers → rows/columns/cells/NoData labelled → objects vs surface → which-model table</td><td>“What is in row 2, column 3 — and what does it mean?”</td></tr>
      <tr><td>B. Values and colours</td><td>4.2</td><td>F5 in random colours → the same F5 in class colours → one cell under three renderers → one band vs three bands → the “what you need” table</td><td>“Which of these two maps is right?” (neither is data)</td></tr>
      <tr><td>C. Resolution and extent</td><td>4.3</td><td>Ten 10 m cells = 100 m → header → extent → DR-0042 arithmetic → P1 on a corner → decimal-places analogy → misaligned grids</td><td>“Which cell is (995, 510) in?”</td></tr>
      <tr><td>D. NoData</td><td>4.4</td><td>Four-way table → F7 mean two ways → F6 mean three ways → count grid: not counted vs zero</td><td>“What is the mean of these nine cells?”</td></tr>
      <tr><td>E. Resampling</td><td>4.5</td><td>Why a rule → methods table → display vs data resampling → the 0.5 land-cover cell → enlarging a grid</td><td>“What value should the four new cells get?”</td></tr>
      <tr><td>F. Surfaces</td><td>4.6</td><td>F6 as a stepped height field → DTM vs DSM → unit and zero level → numbers / ramp / hillshade side by side → realistic ≠ validated</td><td>“Is the ground 100 m higher in the contractor’s data?”</td></tr>
      <tr><td>G. Lab briefing</td><td>4.7</td><td>The two files → Tables A and C → why the “record” rows exist</td><td>—</td></tr>
      <tr><td>H. Gate</td><td>4.8</td><td>Assessment structure and the five critical misconceptions</td><td>—</td></tr>
    </tbody></table></div>
  <div class="callout note"><span class="label">Audio rule</span><p>Describe every grid by naming rows, columns and values — “row one, column three holds twenty” — never by colour. Say “the cells holding code zero, which the legend paints blue”, not “the blue cells”. Introduce each term once; pause before each computed answer so the listener can predict it. Click-by-click instructions stay in the lab page, not the audio.</p></div>
  <div class="callout idea"><span class="label">3D scene (optional, schematic)</span><p>The block model in module 4.6 is the proposed scene: sixteen cells of F6 raised to their heights, a hole for NoData, a visible vertical-exaggeration control, a light-direction control, and a “show numbers” toggle. The point of the scene is that the picture changes and the numbers do not. Every frame should carry “schematic · synthetic data · exaggeration ×N”. A flat grid and the file listing are the text alternative.</p></div>

  <h2><span class="mod">Cross-references</span>What later chapters build on</h2>
  <div class="table-wrap"><table>
    <tbody>
      <tr><th>5 — Coordinates and CRS</th><td>The header’s origin and cell size only mean something with a coordinate reference system; vertical references for heights.</td></tr>
      <tr><th>6 — Projections and measurement</th><td>Reprojecting a raster is resampling with a change of coordinate system; z-factors and unit mismatches.</td></tr>
      <tr><th>7 — Formats and metadata</th><td>Where the unit, the dictionary, the NoData definition and the DTM/DSM definition are supposed to be recorded; GeoTIFF and other raster formats.</td></tr>
      <tr><th>9 — Data capture and quality</th><td>Georeferencing a scanned image with control points (scenario S2); NoData as a quality issue.</td></tr>
      <tr><th>10 — Queries and spatial relationships</th><td>Point-in-cell lookup and its edge rule, next to point-in-polygon and its boundary rule.</td></tr>
      <tr><th>11 — Spatial analysis</th><td>Raster overlay, zonal statistics and surface analysis all depend on alignment, missing-data and resampling rules from here.</td></tr>
      <tr><th>12 — Cartography</th><td>Choosing renderers, class breaks and hillshade settings honestly.</td></tr>
    </tbody></table></div>

  <h2><span class="mod">Transition</span>Where is this raster?</h2>
  <div class="card">
    <p>Every raster in this chapter was placed by four numbers in a header, on a practice grid with no location on Earth. Ask “where is Ward A?” and the honest answer is: <em>nowhere in particular</em>. Chapter 5 supplies what the header lacked — the <strong>coordinate reference system</strong> that turns “x = 995, y = 510” into a real place, with its units, its axis order, and its horizontal and vertical references.</p>
    <p><a class="btn primary" href="./">Back to chapter home</a> &nbsp; <a class="btn ghost" href="glossary">Glossary</a></p>
  </div>
  <p class="small">Source: <em>GIS_Phase_1_Chapter_04_Raster_Data_and_Geographic_Surfaces.md</em>, revision 1.0 (19 September 2026). Reference pages (QGIS Gentle Introduction 3.44 — Raster Data; ArcGIS Pro — Introduction to image and raster data, NoData in raster datasets, Pixel size, Raster bands, Resample, Raster dataset properties, Hillshade function, Vertical coordinate systems; QGIS 3.44 user guide — raster properties and GDAL/raster-analysis algorithms; GDAL AAIGrid driver and gdal_translate) were read on that date; the full list with links is in the chapter document.</p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<?php require __DIR__ . '/../partials/end.php'; ?>
