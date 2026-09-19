<?php $page = ['title' => '5.10 Recap and what comes next', 'chapter' => 5, 'module' => '5.10']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 5.10</div>
    <h1>Recap, media notes, and what comes next</h1>
    <p class="lead">Seven ideas to carry forward, one card to keep, and the question that opens Chapter 6.</p>
  </div>

  <h2><span class="mod">Recap</span>Seven things to remember</h2>
  <div class="try">
    <span class="tag">Self-test</span>
    <p>Before opening each one, say it aloud in your own words.</p>
    <details class="reveal"><summary>1 · Two numbers are not a place</summary><p>A coordinate needs a <strong>CRS</strong>, <strong>units</strong>, the <strong>container’s axis order</strong>, and a stated <strong>precision</strong>. Write the five-line card; never just “X/Y”.</p></details>
    <details class="reveal"><summary>2 · Latitude and longitude are angles</summary><p>Latitude: angle from the equator, positive north. Longitude: angle from the prime meridian, positive east. DD = d + m/60 + s/3600, and back. One degree of latitude ≈ 111 km; a degree of longitude shrinks toward the poles. Check the two signs separately.</p></details>
    <details class="reveal"><summary>3 · Shape, ellipsoid, datum</summary><p>The Earth is modelled by a squashed ball (the ellipsoid); a datum anchors it. The same point has different numbers in different datums (the Redlands example: about 80 m). “WGS 84” names a datum, not a complete CRS.</p></details>
    <details class="reveal"><summary>4 · Geographic vs projected</summary><p>A geographic CRS stores degrees; a projected CRS stores metres or feet and <em>contains</em> a geographic CRS plus a projection. Read the whole record: type, unit, axes, datum, area of use. EPSG:4326 (degrees, world), EPSG:3857 (web maps, metres, distorted), UTM zones (six of them across India). A metre unit does not prove measurement suitability.</p></details>
    <details class="reveal"><summary>5 · Coordinate order follows the contract</summary><p>GeoJSON: longitude, latitude on WGS 84 (CRS84) — no choice. EPSG:4326 formally: latitude, longitude. Leaflet: latitude first. Esri JSON, the JS SDK, PostGIS and XY Table To Point: x = longitude. A swapped pair with both values inside ±90 passes every range check; only an expected-area check or the source columns catch it.</p></details>
    <details class="reveal"><summary>6 · Height needs a reference</summary><p>Unit, surface (ellipsoid or geoid/sea level), direction, and <em>what</em> is measured (pole height vs ground elevation). <code>height = 30</code> is undefined as stored. A stored Z is not automatically used by the next tool.</p></details>
    <details class="reveal"><summary>7 · Data CRS ≠ map CRS; clues are not licences</summary><p>The map converts each layer for display; alignment on screen proves nothing about stored numbers. Run the seven-point checklist. A layer in a strange place is a clue: investigate the source, log the finding, and never assign a CRS by guesswork.</p></details>
  </div>

  <h2><span class="mod">The one card</span>Coordinate cards for the three points you know best</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Field</th><th>G1 (Case A)</th><th>U1 (Case B)</th><th>Case C first position</th></tr></thead>
    <tbody>
      <tr><td>Values</td><td>latitude 23.0250, longitude 72.6000</td><td>easting 254 318.4, northing 2 547 906.2</td><td class="mono">[23.0250, 72.6000]</td></tr>
      <tr><td>CRS</td><td>WGS 84, EPSG:4326</td><td>WGS 84 / UTM zone 43N, EPSG:32643</td><td>fixed by GeoJSON: WGS 84 degrees (CRS84)</td></tr>
      <tr><td>Units</td><td>degree</td><td>metre</td><td>degree</td></tr>
      <tr><td>Container order</td><td>CSV columns <code>lat, lon</code></td><td>CSV columns <code>easting, northing</code></td><td>RFC 7946: longitude, latitude → <strong>asserts lat 72.6°N, lon 23.025°E</strong></td></tr>
      <tr><td>Precision</td><td>4 dp ≈ 11 m</td><td>0.1 m (as written)</td><td>4 dp</td></tr>
      <tr><td>Plausible?</td><td class="yes">Yes</td><td class="yes">Yes (rough checks)</td><td class="no">No</td></tr>
    </tbody></table></div>

  <h2><span class="mod">Media notes</span>If you are making slides or audio from this chapter</h2>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">Four visuals</h4><ul><li>A <strong>labelled globe</strong>: equator, prime meridian, one parallel, one meridian, the two angles for G1.</li><li>The <strong>graticule</strong> on the globe and then on a flat sheet — to show the same lines become a grid, and that flattening distorts (Chapter 6).</li><li>The <strong>flat practice grid</strong>: origin, axes, units, and the warning “right is not east”.</li><li>The <strong>coordinate card</strong> filled in for G1, U1 and the swapped Case C.</li></ul><p class="small">A 3D globe is for showing axes and angles only. It must never suggest that flattening can be done without distortion.</p></div>
    <div class="card"><h4 style="margin-top:0">Narration rules</h4><ul><li>Every time a coordinate pair is read aloud, say the order in words: “longitude seventy-two point six, then latitude twenty-three point zero two five” for GeoJSON; “latitude … then longitude …” for prose.</li><li>Never say “the first number” without naming what it means in the current format.</li><li>Say “approximately” before every rule-of-thumb distance. Say “degrees”, “metres” and “seconds of arc” explicitly.</li><li>Pronounce: “E-P-S-G”, “U-T-M”, “W-G-S eighty-four”, “Geo-JSON”.</li></ul></div>
  </div>
  <div class="card"><h4 style="margin-top:0">Sources read for this chapter (19 September 2026)</h4><p class="small">QGIS <em>Gentle Introduction to GIS</em> — Coordinate Reference Systems (3.44); Esri ArcGIS Pro — <em>Coordinate systems, map projections, and transformations</em>, <em>Work with coordinate systems</em> (labelled ArcGIS Pro 3.7), <em>Use an unknown coordinate system</em>, <em>Properties of a spatial reference</em>, <em>Vertical coordinate systems</em>, <em>Set layer properties</em>, <em>Map units, display units, and location units</em>, <em>XY Table To Point</em>, <em>Mercator</em>; Esri ArcMap — <em>What are geographic coordinate systems?</em>, <em>Datums</em>, <em>Spheroids and spheres</em>; Esri REST API — <em>Geometry objects</em>, <em>Using spatial references</em>; Esri Developer — <em>Spatial references</em>; ArcGIS Maps SDK for JavaScript 5.1 — <em>Point</em>; RFC 7946; EPSG registry v13.103 (codes 4326, 3857, 32643, 16043, 4979, 3855); QGIS 3.40 user guide — <em>Working with Projections</em>, <em>Options</em>, <em>QGIS GUI</em>, <em>Opening Data</em>; PostGIS <em>ST_Point</em>; Leaflet 1.9.4 reference. Full links are in the chapter document.</p></div>

  <h2><span class="mod">Next</span>The question that opens Chapter 6</h2>
  <div class="callout idea"><span class="label">Transition</span><p>You can now <em>read</em> a reference. Chapter 6 <em>uses</em> it: it separates <strong>assigning</strong> a CRS (changing the label) from <strong>transforming</strong> coordinates (changing the numbers), chooses datum transformations with evidence, and measures distances and areas with a documented method — including why a Web Mercator metre is not a ground metre. Every Chapter 6 lab begins with the seven-point checklist from 5.7. The question to carry across: <strong>“Now that I know what the numbers mean — how do I change them safely, and how do I measure with them?”</strong></p></div>
  <p><a class="btn primary" href="index.php">← Chapter home</a> &nbsp; <a class="btn ghost" href="glossary.php">Glossary</a></p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<?php require __DIR__ . '/../partials/end.php'; ?>
