<?php $page = ['title' => '6.10 Recap, media brief and what comes next', 'chapter' => 6, 'module' => '6.10']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 6.10 · Recap, media and transition</div>
    <h1>What to carry forward</h1>
    <p class="lead">Six ideas, one worked example, and a log that becomes the start of Chapter 7.</p>
  </div>

  <h2><span class="mod">Recap</span>Six things to remember</h2>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">1. Every projection stretches something</h4><p>“Preserves” always means <em>some property, somewhere, under conditions</em>. Choose from <strong>extent, location, property</strong>.</p></div>
    <div class="card"><h4 style="margin-top:0">2. Label ≠ transform</h4><p>Define Projection / Assign projection / <code>ST_SetSRID</code> change a label. Project / Reproject layer / <code>ST_Transform</code> change the numbers. The wrong choice raises no error — the data is just somewhere else: loudly, quietly, or very quietly.</p></div>
    <div class="card"><h4 style="margin-top:0">3. A datum change needs a record</h4><p>Read it for applicability, area, resources and accuracy. Never hard-code one. Here: Kalianpur 1975 → WGS 84 ≈ 112 m, known to 22 m.</p></div>
    <div class="card"><h4 style="margin-top:0">4. Lined up ≠ ready</h4><p>Layers aligned on screen are <em>displayed</em> together, not <em>prepared</em> together. Fill the five-row worksheet; then read the tool’s own rule.</p></div>
    <div class="card"><h4 style="margin-top:0">5. Planar vs geodesic; degrees are not metres</h4><p>Flat is exact on the sheet and right on the ground only where the projection is; geodesic is right on the ellipsoid anywhere. Degree data still measures correctly with a geodesic method. Web Mercator’s metres are real units stretched by h and k.</p></div>
    <div class="card"><h4 style="margin-top:0">6. The Fixture E6 numbers</h4><p>Q1–Q2: geodesic <strong>664.46 m</strong>; UTM flat 0.9996 × that = 664.20; Web Mercator flat 1.092 × that = 725.63; sphere +0.4 %; degrees 0.006 — not comparable.</p></div>
  </div>

  <h2><span class="mod">Media brief</span>What the slides, audio and diagrams should show</h2>
  <p>This is a summary for whoever builds the presentation and audio lesson; the full specifications (slide-by-slide, narration outline, diagram specs, interactive spec) are in the Media Appendix of the chapter document.</p>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">Diagram D6-1 — four coins</h4><p>Four equal coins at 0°, 23°, 45°, 70° N; under a conformal rule they stay round but grow; under an equal-area rule they keep their size but squash. Caption must say “schematic; sizes not computed from any projection”. (You saw it live in 6.1.)</p></div>
    <div class="card"><h4 style="margin-top:0">Diagram D6-2 — one UTM zone</h4><p>A 6° strip; centre line at scale 0.9996; two dashed lines at scale 1.0; edges slightly above 1.0; red band outside. Only 0.9996 and 1.0 are exact numbers on the figure.</p></div>
    <div class="card"><h4 style="margin-top:0">Animation D6-3 — label change vs coordinate change</h4><p>Two panels, each with Q1’s record card and a mini-map. Left: Define Projection — label flips, numbers stay, dot leaves the town. Right: Project — label flips <em>and</em> numbers become 75.000 / 23.002, dot stays. Both show before/after values as text. (You saw it live in 6.2.)</p></div>
    <div class="card"><h4 style="margin-top:0">Optional interactive — drag a square across latitudes</h4><p>A 1 km ground square shown with its Web Mercator and UTM sizes as the learner changes latitude, with h, k and 0.9996 displayed. Must reproduce the 6.7 table to 0.01 m before classroom use. No 3D scene is proposed: a globe with exaggerated distortion would risk exactly the “stylised animation as evidence” mistake the blueprint forbids.</p></div>
  </div>
  <div class="callout note"><span class="label">Narration rules for the audio lesson</span><p>Say “assign” and “transform” as different words — never “reproject” for both. Read every coordinate with its unit and order aloud: “latitude twenty-three point zero zero two north, longitude seventy-five east”, or “easting five hundred thousand metres, northing two million five hundred forty-three thousand seven hundred forty-one point one six three metres”. Pause for a prediction before each answer.</p></div>

  <h2><span class="mod">Sources</span>Where the facts came from</h2>
  <p>Primary readings for this chapter: the QGIS <em>Gentle Introduction</em> page on coordinate reference systems; the ArcGIS Pro pages for <em>Define Projection</em>, <em>Project</em> and <em>Buffer</em>; the Esri page on coordinate systems, projections and transformations; the EPSG registry entries for EPSG:4326, 3857, 32643 and transformation 1156; and the IOGP/EPSG Guidance Note 7-2 for the formulas. Product behaviour was read on 19 September 2026 from ArcGIS Pro 3.7 and QGIS 3.40 documentation. The full reference list with links is in the chapter document.</p>

  <h2><span class="mod">Next</span>Chapter 7 — formats, sources and metadata</h2>
  <p>Every decision this chapter asked you to log — input CRS, transformation record and its accuracy, analysis CRS and why, measurement method, units, rounding, software version — is <strong>provenance</strong>. Chapter 7 shows where that lives inside a dataset: why a Shapefile keeps its CRS in a separate <code>.prj</code> file that can go missing (exactly Q2 of the check), and what a GeoPackage or a geodatabase records instead. <strong>Keep your 6.8 log</strong>; it becomes a Chapter 7 metadata example.</p>
  <p><a class="btn primary" href="index.php">← Chapter home</a> &nbsp; <a class="btn ghost" href="glossary.php">Glossary</a></p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<?php require __DIR__ . '/../partials/end.php'; ?>
