<?php $page = ['title' => 'Chapter 5 — Coordinates and coordinate reference systems', 'chapter' => 5]; require __DIR__ . '/../partials/head.php'; ?>
  <section class="hero">
    <div class="fade-up">
      <div class="page-head" style="padding:0">
        <div class="eyebrow">GIS Phase 1 · Chapter 5 · Interactive tutorial</div>
      </div>
      <h1 class="big">Two numbers are<br>not a <em>place</em></h1>
      <p class="lead">Until now every coordinate in this course sat on a flat practice grid — graph paper, nothing more. This chapter answers the question we kept postponing: <strong>what do the numbers actually refer to?</strong> You will learn to read latitude and longitude, to tell a “degrees” system from a “metres” system, to write coordinates in the right order for each file format, and — most important — to stop and ask when a file does not say what its numbers mean.</p>
      <p><a class="btn primary" href="m1-two-numbers.php">Begin module 5.1 →</a> &nbsp; <a class="btn ghost" href="glossary.php">Glossary</a></p>
      <p class="small">No software, no account and no licence is needed. Everything here runs in the browser. Every coordinate, file and record on these pages is <span class="synthetic">made-up practice material</span>. Your progress is saved in this browser only.</p>
    </div>
    <figure class="map-fig fade-up" id="heroFig"></figure>
  </section>

  <h2 style="border-top:0;margin-top:1rem"><span class="mod">The big idea</span>A coordinate is numbers <em>plus</em> an agreement</h2>
  <div class="grid-2">
    <div class="card">
      <p>Someone sends you <code>23.025, 72.6</code> on WhatsApp. Where is it? You may guess “somewhere in Gujarat”. But the same two numbers, read the other way round, are a spot in the sea near Norway. And <code>1200, 250</code> could be request P3 on our practice grid — or nothing on the Earth at all.</p>
      <p>The agreement that gives numbers their meaning is called a <strong>coordinate reference system (CRS)</strong>. It says where zero is, which way the axes go, what one step is worth (a degree? a metre?), and which model of the Earth is being used. Without it, coordinates are just numbers.</p>
      <p>Think of an amount written as <code>30</code> with no ₹ or $ sign, or a train time written as <code>6:30</code> with no AM/PM. The number is there; the meaning is missing.</p>
    </div>
    <div class="card">
      <h4 style="margin-top:0">Two practice datasets, kept strictly apart</h4>
      <p><strong>The flat grid</strong> from Chapter 1: two wards, one road, requests P1–P6, metres on graph paper, <em>no Earth location</em>. Same as before.</p>
      <p><strong>New — “set E5”</strong>: a few made-up points that <em>do</em> sit on the Earth, using round numbers in western India (for example latitude 23.0250, longitude 72.6000), plus three points in a “metres” system for the same region, and one streetlight record that just says <code>height = 30</code>.</p>
      <p class="small">Mixing a flat practice grid with Earth coordinates is exactly the kind of mistake this chapter teaches you to catch — so the tutorial never mixes them either.</p>
    </div>
  </div>

  <h2><span class="mod">Modules</span>Work through them in order</h2>
  <div class="modcards" id="modcards"></div>

  <h2><span class="mod">Plain words</span>A few words you will see everywhere</h2>
  <div class="card"><table>
    <tr><th>Coordinate</th><td>An ordered set of numbers giving a position — like <code>(1200, 250)</code> or <code>23.025, 72.6</code>.</td></tr>
    <tr><th>CRS</th><td><em>Coordinate reference system.</em> The written agreement that gives the numbers meaning: origin, axes, units, Earth model. Esri’s software often says “coordinate system” or “spatial reference” for the same idea.</td></tr>
    <tr><th>Latitude / longitude</th><td>Two <em>angles</em> on the round Earth. Latitude: how far north or south of the equator. Longitude: how far east or west of the line through Greenwich (London).</td></tr>
    <tr><th>Geographic vs projected</th><td>A <em>geographic</em> CRS stores angles (degrees). A <em>projected</em> CRS flattens the Earth and stores distances (metres or feet) on that flat sheet.</td></tr>
    <tr><th>EPSG code</th><td>A number that looks up one CRS in a public register — like a PIN code for a CRS. <code>EPSG:4326</code> is the common latitude/longitude system; <code>EPSG:3857</code> is what web maps draw in.</td></tr>
    <tr><th>Datum</th><td>The part of a CRS that says where the model Earth is anchored. Change the datum and the same spot gets different numbers.</td></tr>
    <tr><th>Coordinate order</th><td>Which number is written first. It depends on the <em>file or API</em>, not on the CRS. GeoJSON writes longitude first; many people say “lat, long”.</td></tr>
  </table></div>

  <h2><span class="mod">Outcomes</span>What you will be able to do</h2>
  <div class="card">
    <table>
      <tr><th>LO1</th><td>Explain why two numbers alone do not identify a place, and write a complete “coordinate card” (values, CRS, units, order, precision).</td></tr>
      <tr><th>LO2</th><td>Read and write latitude/longitude in decimal degrees and in degrees–minutes–seconds, with the right signs, and convert between them.</td></tr>
      <tr><th>LO3</th><td>Say what the Earth’s shape, the ellipsoid and the datum each add — and why “WGS 84” alone is not a complete CRS.</td></tr>
      <tr><th>LO4</th><td>Tell a geographic CRS (degrees) from a projected CRS (metres), read the full record of EPSG:4326, EPSG:3857 and a UTM zone, and explain why “metres” does not prove the system is good for measuring.</td></tr>
      <tr><th>LO5</th><td>State the coordinate order GeoJSON requires, contrast it with other APIs, and catch a swapped pair that passes a range check.</td></tr>
      <tr><th>LO6</th><td>Explain why <code>height = 30</code> is meaningless on its own, and why a stored Z value is not automatically used.</td></tr>
      <tr><th>LO7</th><td>Find a dataset’s CRS and a map’s display CRS in ArcGIS Pro or QGIS, and explain why they can differ.</td></tr>
      <tr><th>LO8</th><td>Classify a coordinate set as geographic, projected, swapped, or “cannot be resolved from the numbers alone” — with evidence — and refuse to guess an unknown CRS.</td></tr>
    </table>
  </div>
  <p class="small">Source: <em>GIS_Phase_1_Chapter_05_Coordinates_and_Coordinate_Reference_Systems.md</em>, revision 1.0 (19 September 2026). This tutorial follows that document; formal assessment answers are held by the instructor and are not on these pages. Software statements were checked against official documentation on that date and can change.</p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  renderGlobe(document.getElementById("heroFig"), { lat: 23.025, lon: 72.6, label: "G1", caption: "Made-up point G1: latitude 23.025° N, longitude 72.6° E. Two angles on a round Earth — the two dials show them." });
  const prog = getProgress();
  const blurbs = {
    "5.1": "23.025, 72.6 — where is that? What a coordinate is missing until you add the agreement. The five-line coordinate card.",
    "5.2": "Equator, prime meridian, angles. Decimal degrees vs degrees-minutes-seconds. A converter that shows its working. Signs for N/S and E/W.",
    "5.3": "The Earth is not a ball. Ellipsoid, then datum. The same point, three sets of numbers. Why “WGS 84” is not enough.",
    "5.4": "Degrees or metres? Nested boxes: a projected CRS contains a geographic one. EPSG:4326, EPSG:3857, UTM zones. Reading the whole record.",
    "5.5": "GeoJSON says longitude first. Leaflet says latitude first. Same numbers, two places. The swap that passes every range check.",
    "5.6": "height = 30 — of what, from where, in what unit? Ellipsoid vs sea level. Having a Z is not the same as using it.",
    "5.7": "Where ArcGIS Pro and QGIS show the layer’s CRS and the map’s CRS. Why they differ. The seven-point checklist. Clues are not answers.",
    "5.8": "Four case files. Classify each one with evidence. One of them cannot be solved from the numbers — and that is the right answer.",
    "5.9": "The assessment you submit to your instructor, and the progression rule.",
    "5.10": "Recap, media notes, and the question that leads into Chapter 6."
  };
  document.getElementById("modcards").innerHTML = MODULES.map(m => `<a class="modcard ${prog[m.id] ? "done" : ""}" href="${m.file}"><span class="done-badge">done</span><div class="id">MODULE ${m.id}</div><h3>${m.title}</h3><p>${blurbs[m.id]}</p></a>`).join("");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
