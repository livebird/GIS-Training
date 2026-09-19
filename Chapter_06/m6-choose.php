<?php $page = ['title' => '6.6 Choose the method from the requirement', 'chapter' => 6, 'module' => '6.6']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 6.6 · General method</div>
    <h1>Let the requirement choose the method</h1>
    <p class="lead">Not “what do I usually do?” but “what does this question need?”. Six questions decide the method. Answer them first; press the buttons second.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Fill a six-row decision worksheet: study area, property, accuracy, data quality, supported algorithm, units.</li>
      <li>Choose between a local projected workflow and a wide-area geodesic one — and see why one UTM zone cannot cover all of India.</li>
      <li>Keep four different “distances” apart: straight-line, road/network, 2D, 3D.</li></ul></div>
  </div>

  <h2><span class="mod">6.6.1</span>The decision worksheet</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Row</th><th>Question</th><th>Why it matters</th><th>Fixture E6 lab answer</th></tr></thead>
    <tbody>
      <tr><td><strong>1. Study area</strong></td><td>Where, and how large? Inside one projected CRS’s area of use, or spanning zones / states / the globe?</td><td>Decides whether a local projected CRS is even <em>available</em></td><td>About 2 km × 1 km around 23° N, 75° E — entirely inside UTM zone 43N (“N hemisphere – 72°E to 78°E”)</td></tr>
      <tr><td><strong>2. Measured property</strong></td><td>Distance, area, direction, or shape?</td><td>Different projections keep different properties (6.1)</td><td>One distance and one area</td></tr>
      <tr><td><strong>3. Required accuracy</strong></td><td>What error is acceptable for the <em>decision</em>?</td><td>Decides whether 0.04 % (UTM at its meridian) or 9 % (Web Mercator here) is tolerable</td><td>“1 m per km is more than enough for sending a crew”</td></tr>
      <tr><td><strong>4. Data quality</strong></td><td>How accurate are the positions themselves?</td><td>No method can beat the input’s error; a 22 m datum shift dwarfs a 0.4 m projection error</td><td>Synthetic and exact; in real work, record the source’s stated accuracy</td></tr>
      <tr><td><strong>5. Supported algorithm</strong></td><td>Which methods does the chosen tool actually implement for this input CRS? (6.4.3)</td><td>Buffer / Measure / Calculate Geometry / QGIS ellipsoid settings all differ</td><td>ArcGIS Pro: flat on EPSG:32643, or geodesic property. QGIS: ellipsoidal by default</td></tr>
      <tr><td><strong>6. Output units</strong></td><td>What unit must the answer be in, rounded how?</td><td>Stops “0.010” being read as metres</td><td>Metres to 0.01 m; m² to 1 m²; rounding written in the log</td></tr>
    </tbody></table></div>
  <div class="callout dev"><span class="label">Developer view</span><p>This worksheet is a <strong>requirements review before picking a data type</strong>. You would not choose <code>float32</code> or <code>decimal(18,4)</code> for a money field without knowing the range, the precision the business needs, and which database functions support the type. Rows 1–3 are range and precision; row 5 is function support. <strong>Where it breaks:</strong> a numeric type’s error is the same for every value; a projection’s error depends on <em>where the value sits on the Earth</em> — which is why row 1 comes first.</p></div>

  <h2><span class="mod">6.6.2</span>Local projected, or wide-area geodesic?</h2>
  <div class="grid-2">
    <div class="card"><h3 style="margin-top:0">Local projected workflow</h3><p>When the study area fits inside the <strong>area of use</strong> of a suitable projected CRS — a UTM zone or a national/state grid — project the analysis copy into it and measure flat. Check three things: (i) the area of use covers the <em>whole</em> study area (look it up; do not remember it); (ii) the distortion there is within your accuracy row; (iii) the units are what you will report. Fixture E6 passes all three with EPSG:32643.</p></div>
    <div class="card"><h3 style="margin-top:0">Wide-area geodesic workflow</h3><p>When the area spans zones, states or the globe, no single projection keeps distances right everywhere. Use a <strong>supported geodesic method</strong> on degree (or, where supported, Web Mercator) input: Buffer’s Geodesic option, Calculate Geometry’s geodesic properties, QGIS ellipsoidal measurement, PostGIS <code>geography</code>, the JS SDK’s <code>geodesic*</code> functions. Record which ellipsoid (or sphere!) the method used.</p></div>
  </div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Which UTM zone is it? (Why one zone cannot cover all of India)</h3>
    <p>India’s mainland runs from roughly 68° E (Gujarat’s west coast) to about 97° E (Arunachal Pradesh) — a rough range for orientation only. UTM zones are 6° wide, so that range crosses <strong>six</strong> zones. Type a longitude to find its zone and central meridian.</p>
    <div class="controls"><label>Longitude (° E) <input type="number" id="lonIn" value="75" min="60" max="100" step="0.1" style="width:110px"></label>
      <button class="btn small" data-lon="72.6">≈ west coast</button><button class="btn small" data-lon="77.2">≈ Delhi longitude</button><button class="btn small" data-lon="80.3">≈ Chennai longitude</button><button class="btn small" data-lon="88.4">≈ Kolkata longitude</button><button class="btn small" data-lon="94.9">≈ far north-east</button></div>
    <div class="tiles" id="zoneTiles"></div>
    <figure class="map-fig" id="zonesFig"></figure>
    <div class="result">Data from one zone forced into another zone’s CRS is not “invalid” — it is measured where Transverse Mercator’s distortion has grown well past the small-error band. Choose the zone from the data’s location. If data straddles a zone edge, use a geodesic method or a national grid whose area of use covers it — and write down why.</div>
  </div>

  <h2><span class="mod">6.6.3</span>Four “distances” that answer four different questions</h2>
  <div class="fourd">
    <div class="d"><h4>Straight-line (planar or geodesic)</h4><p class="q">“How far apart are these two places, as the crow flies?”</p><p>Needs positions and a measurement method — this chapter. Q1–Q2: 664.46 m geodesic.</p></div>
    <div class="d"><h4>Road / network distance or time</h4><p class="q">“How far will the crew actually drive?”</p><p>Needs a connected road network and a routing method — <strong>not covered in Phase 1</strong> (Chapter 1 already made this point). Fixture E6 has no roads, so it cannot answer this at all.</p></div>
    <div class="d"><h4>2D ground distance</h4><p class="q">“How long is this path on the map surface?”</p><p>Horizontal positions only. Every number in this chapter is 2D.</p></div>
    <div class="d"><h4>3D slope distance</h4><p class="q">“How long a pipe must be laid along this slope?”</p><p>Needs heights with their vertical reference (Chapter 5). A 100 m horizontal run with a 10 m rise is √(100² + 10²) = <strong>100.50 m</strong> along the slope.</p></div>
  </div>
  <p>A stored Z value does not make a tool compute in 3D. Calculate Geometry has a separate “Length (3D)” property; the Measure tool reports 3D distance separately from 2D map distance in a scene. Check the operation’s behaviour before quoting a 3D number.</p>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Fill the worksheet for a new job</h3>
    <p>A logistics team wants the straight-line distance between every depot and every request <strong>across three states</strong>, reported in kilometres to 0.1 km, from request data stored in EPSG:4326. Choose the best entry for each row.</p>
    <div class="sheet" id="sheet2">
      <div class="srow"><div>1. Study area</div><div><select data-ok="b"><option value="">choose…</option><option value="a">Small — one UTM zone</option><option value="b">Three states — crosses several UTM zones; no single local CRS is safe</option></select><div class="fbk"></div></div></div>
      <div class="srow"><div>2. Property</div><div><select data-ok="a"><option value="">choose…</option><option value="a">Distance</option><option value="b">Area</option></select><div class="fbk"></div></div></div>
      <div class="srow"><div>3. Accuracy</div><div><select data-ok="a"><option value="">choose…</option><option value="a">0.1 km — so a 0.04 % or even 1 % method error is fine, but 9 % Web Mercator error over long distances is not</option><option value="b">Does not matter for a straight line</option></select><div class="fbk"></div></div></div>
      <div class="srow"><div>5. Supported algorithm</div><div><select data-ok="b"><option value="">choose…</option><option value="a">Pythagoras on the degree values, then × 111 km</option><option value="b">A geodesic method on the EPSG:4326 input (Calculate Geometry “Length (geodesic)”, PostGIS geography, SDK geodesicLength, QGIS ellipsoidal)</option></select><div class="fbk"></div></div></div>
      <div class="srow"><div>6. Units</div><div><select data-ok="a"><option value="">choose…</option><option value="a">Kilometres to 0.1 km, stated in the log</option><option value="b">Whatever the tool outputs</option></select><div class="fbk"></div></div></div>
      <div class="srow"><div>Bonus</div><div><select data-ok="b"><option value="">If their real question is “which depot can reach the request fastest”, which distance is that?</option><option value="a">Straight-line geodesic — same thing</option><option value="b">Road / network travel — a different question that straight-line distance of any kind does not answer</option></select><div class="fbk"></div></div></div>
    </div>
  </div>

  <div class="callout warn"><span class="label">Common mistake</span><p><em>“Geodesic is always more accurate, so I’ll always use it.”</em> Geodesic is more faithful to the ellipsoid, but that is not the only requirement: some tools do not support it for a given input (row 5), it is slower on very large datasets, and in a small area a local projection is equally accurate for the decision while giving flat coordinates that every downstream tool understands. The requirement decides — in either direction.</p></div>

  <div class="quiz" data-answer="2" data-fb="Team 1: flat in EPSG:32643 — study area inside one zone, and 1 m in 1.2 km is far looser than the 0.4 m/km scale effect. Team 2: geodesic — all of India spans six UTM zones, and a geodesic method works directly on the EPSG:4326 input.">
    <div class="q">Team 1 must report the length of a 1.2 km drainage line inside one ward (data in EPSG:32643) to the nearest metre. Team 2 must report the straight-line distance from a national control centre to 3,000 depots across all of India to the nearest kilometre (data in EPSG:4326). Best choices?</div>
    <div class="opts">
      <button class="opt">Both geodesic — it is always more accurate.</button>
      <button class="opt">Both flat in UTM 43N — metres are metres.</button>
      <button class="opt">Team 1 flat in UTM 43N (one zone, loose accuracy need); Team 2 geodesic (spans six zones).</button>
      <button class="opt">Team 1 geodesic; Team 2 flat in Web Mercator because the dashboard basemap is Web Mercator.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const lonIn = document.getElementById("lonIn");
  function drawZones(lon) {
    const z = utmZone(lon), cm = utmCM(z);
    document.getElementById("zoneTiles").innerHTML = `
      <div class="tile good"><div class="k">UTM zone</div><div class="v">${z}N</div><div class="s">covers ${cm - 3}° E to ${cm + 3}° E</div></div>
      <div class="tile"><div class="k">Central meridian</div><div class="v">${cm}° E</div><div class="s">scale 0.9996 along this line</div></div>
      <div class="tile"><div class="k">EPSG code (WGS 84)</div><div class="v">326${z}</div><div class="s">northern hemisphere: 32600 + zone (verify in the registry)</div></div>
      <div class="tile okish"><div class="k">Distance from the meridian</div><div class="v">${Math.abs(lon - cm).toFixed(1)}°</div><div class="s">${Math.abs(lon - cm) > 3 ? "outside the zone!" : Math.abs(lon - cm) > 2 ? "near the edge — scale a little over 1.0" : "well inside"}</div></div>`;
    const svg = newSvg(document.getElementById("zonesFig"), "0 0 1000 220", "Schematic strip of longitude from 66 to 102 degrees east showing UTM zones 42 to 47 and the chosen longitude.");
    const X = l => 20 + (l - 66) * (960 / 36);
    for (let zz = 42; zz <= 47; zz++) { const c = utmCM(zz); mkEl("rect", { x: X(c - 3), y: 40, width: X(c + 3) - X(c - 3), height: 110, class: "zone" + (zz === z ? "" : ""), style: zz === z ? "fill:rgba(47,125,79,.25)" : "" }, svg); mkEl("line", { x1: X(c), y1: 40, x2: X(c), y2: 150, class: "truescale" }, svg); txt(svg, X(c), 30, "zone " + zz, "lbl big", { "text-anchor": "middle" }); txt(svg, X(c), 175, c + "° E", "lbl", { "text-anchor": "middle" }); }
    mkEl("line", { x1: X(lon), y1: 40, x2: X(lon), y2: 150, class: "cm", style: "stroke:#d3541f" }, svg);
    txt(svg, X(lon), 205, lon + "° E", "lbl acc", { "text-anchor": "middle" });
    txt(svg, 20, 215, "roughly India’s longitude span (68–97° E) → six zones", "lbl warn");
  }
  lonIn.addEventListener("input", () => { const v = parseFloat(lonIn.value); if (!isNaN(v)) drawZones(v); });
  document.querySelectorAll("[data-lon]").forEach(b => b.addEventListener("click", () => { lonIn.value = b.dataset.lon; drawZones(+b.dataset.lon); }));
  drawZones(75);
  document.querySelectorAll("#sheet2 select").forEach(s => s.addEventListener("change", () => {
    const fb = s.parentElement.querySelector(".fbk"); if (!s.value) { fb.className = "fbk"; return; }
    const ok = s.value === s.dataset.ok; fb.className = "fbk show " + (ok ? "ok" : "no");
    fb.textContent = ok ? "Yes." : "Not this one — think about the size of the area and what the tool can actually do with degree input.";
  }));
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
