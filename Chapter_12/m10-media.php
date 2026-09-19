<?php $page = ['title' => '12.10 Recap, media brief and what comes next', 'chapter' => 12, 'module' => '12.10']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 12.10 · Recap, media and transition · End of Phase 1</div>
    <h1>What to carry into Phase 2</h1>
    <p class="lead">Eight habits, the media brief, and where these judgements get implemented next.</p>
  </div>

  <h2><span class="mod">Recap</span>Eight things to remember</h2>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">1. Reader and decision first</h4><p>A crew map locates things for a crew; a manager’s map compares wards for a manager. One map cannot do both. Remove whatever does not serve the decision; design at the reading size.</p></div>
    <div class="card"><h4 style="margin-top:0">2. Scale is a fraction</h4><p>1:1,000 is <em>large</em> scale and shows more than 1:100,000. Scale, cell size, decimal places and accuracy are four different numbers; only accuracy says how close a drawn point is to the truth. Zoom adds nothing.</p></div>
    <div class="card"><h4 style="margin-top:0">3. Symbol matches the kind of field</h4><p>Names → shapes; order → ordered shades with the order written; amounts → a ramp or sizes. Data in front, context behind, basemap quiet. Check the map in grey.</p></div>
    <div class="card"><h4 style="margin-top:0">4. Classification is analysis</h4><p>Equal interval, quantile and natural breaks put the same 15 wards into different classes with no change to any value. Print the intervals and units. For a comparison, fix the classes once.</p></div>
    <div class="card"><h4 style="margin-top:0">5. A count is not a comparison</h4><p>100 in 10,000 vs 60 in 3,000: 10 vs 20 per 1,000. The denominator must match what produces the requests — households, road km, assets — and no rate proves risk.</p></div>
    <div class="card"><h4 style="margin-top:0">6. Context, and zero ≠ missing</h4><p>Title, legend, units, source/date, method note always; scale bar and north arrow only when they help. Zero is classified; missing is excluded, hatched, and named. A ward average is not every street.</p></div>
    <div class="card"><h4 style="margin-top:0">7. Presentation ≠ protection</h4><p>Hiding a field or a layer changes the map, not the service — <code>outFields=*</code> still returns it. Protection is sharing and views (later chapters). Never put personal notes on a map.</p></div>
    <div class="card"><h4 style="margin-top:0">8. Ship the source-value table</h4><p>Every map goes out with the table of values behind it, so that no styling can hide a discrepancy in the analysis.</p></div>
  </div>

  <h2><span class="mod">Media brief</span>What the slides, audio and diagrams should show</h2>
  <p>Summary for whoever builds the presentation and audio lesson; the full slide-by-slide outline, narration notes, diagram specs and the interactive/3D spec are in the chapter document’s Media Appendix.</p>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">Side-by-side maps, one changed choice</h4><p>Three pairs on District G-12 with the source-value table always visible: (a) the count by equal interval / quantile / natural breaks; (b) count vs rate per 1,000 households (W15 dark on the left, W12 on the right); (c) W16 as “no data” vs W16 as a false zero, with the caption changing from “15 wards + 1 not received” to the wrong “16 wards”. (You used all three live in 12.4–12.6.)</p></div>
    <div class="card"><h4 style="margin-top:0">Symbol footprint</h4><p>A 200 m patch around G02/G03 at 1:25,000, 1:10,000 and 1:2,000 with a 12 pt symbol at its true ground size (106 m, 42 m, 8.5 m) and the two lamps 11.18 m apart: merge, merge, separate.</p></div>
    <div class="card"><h4 style="margin-top:0">Presentation vs protection</h4><p>A layered diagram: map (pop-up field unticked) → layer/service (all fields) → REST query returning them; beside it, a view item with the field excluded and its own sharing badge, labelled “later chapters”.</p></div>
    <div class="card"><h4 style="margin-top:0">Optional 3D: extruded columns vs the 2D map</h4><p>Each ward raised in proportion to its count or rate, to show occlusion (a tall W15 hides W11 from some angles) and perspective. Every frame labelled “column height = data value (thematic), not building height”; W16 flat and hatched, never a zero-height column. A 2D fallback (choropleth + bar chart) for every point the scene makes.</p></div>
  </div>
  <div class="callout note"><span class="label">Narration rules for the audio lesson</span><p>Say “choropleth” (KLOR-o-pleth) and “quantile” clearly; say every unit the first time (“open requests per one thousand households”); describe each figure in words before drawing a conclusion; pause before each answer so listeners can predict. Say “zero is a value; missing is not” twice.</p></div>

  <h2><span class="mod">Sources</span>Where the facts came from</h2>
  <p>Primary readings for this chapter: the QGIS <em>Gentle Introduction</em> pages on map production and vector attribute data; the ArcGIS Pro pages on data classification methods, graduated colors, symbolizing feature layers, unique values, map scales, scale ranges, scale bars, legends, layouts, pop-ups, basemaps and the Color Vision Deficiency Simulator; the ArcGIS Online pages on styles, styling numbers, pop-ups and hosted feature layer views; the ArcGIS REST API query operation; and the QGIS 3.44 user guide on preview modes and the vector properties dialog. All were read on 19 September 2026; the full reference list with links is in the chapter document.</p>

  <h2><span class="mod">Next</span>Phase 2 — doing this systematically in ArcGIS Pro</h2>
  <p>Phase 1 taught judgement: what a map should say and what it must not hide. Phase 2 implements it as product skill — symbology and label classes, layouts and map series, style files, classification and normalisation controls in ArcGIS Pro — and then publishes it through ArcGIS Online, where the <strong>protection</strong> requirements you flagged in 12.7 (sharing levels, views with excluded fields, editing rights) are finally configured. Keep your lab rationale and your E12 log: they are the first entries of your Phase 2 notebook.</p>
  <p><a class="btn primary" href="./">← Chapter home</a> &nbsp; <a class="btn ghost" href="glossary">Glossary</a> &nbsp; <a class="btn ghost" href="../">Course index</a></p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<?php require __DIR__ . '/../partials/end.php'; ?>
