<?php $page = ['title' => '5.4 Geographic vs projected CRS', 'chapter' => 5, 'module' => '5.4']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 5.4 · General GIS idea (with EPSG register examples)</div>
    <h1>Degrees or metres? Geographic vs projected CRS</h1>
    <p class="lead">Every dataset you meet belongs to one of two families: it stores <em>angles</em> on the round Earth, or it stores <em>distances</em> on a flattened sheet. This module teaches you to tell them apart, to read the full record of the three CRSs you will meet most, and why “metres” does not mean “ready for measuring”.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Explain that a projected CRS <em>contains</em> a geographic CRS plus a map projection.</li>
      <li>Read the EPSG records for 4326, 3857 and a UTM zone: type, unit, axes, area of use.</li>
      <li>Work out a UTM zone from a longitude, and see why India needs six of them.</li></ul></div>
  </div>

  <h2><span class="mod">5.4.1</span>Angles on a ball, or distances on a sheet</h2>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">Geographic CRS</h4><p>Stores <strong>angles</strong> — latitude and longitude — on the ellipsoid. Unit: <strong>degrees</strong>. Good for the whole world; bad for ruler arithmetic (a degree is a different distance in different places, 5.2).</p></div>
    <div class="card"><h4 style="margin-top:0">Projected CRS</h4><p>Stores <strong>flat</strong> coordinates — easting/northing or x/y — in a <strong>linear unit</strong> such as metres or feet. A mathematical <em>map projection</em> flattens the angles onto the sheet. Good for subtracting two eastings and getting metres — <em>inside the area the projection was designed for</em>.</p></div>
  </div>
  <p>The key sentence, from Esri’s definition: a projected coordinate system “consists of a linear unit of measure (usually meters or feet), a map projection, the specific parameters used by the map projection, and a geographic coordinate system”. So a projected CRS <strong>contains</strong> a geographic CRS. Click the boxes.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <div class="two-col">
      <div>
        <div class="nest l1" data-n="0"><div class="nt">Projected CRS</div><div class="nv">WGS 84 / UTM zone 43N (EPSG:32643) — unit: metre</div>
          <div class="nest l2" data-n="1"><div class="nt">Map projection + parameters</div><div class="nv">Transverse Mercator; centre line 75° E; scale 0.9996; false easting 500 000 m; false northing 0</div>
            <div class="nest l3" data-n="2"><div class="nt">Geographic CRS</div><div class="nv">WGS 84 (EPSG:4326) — unit: degree</div>
              <div class="nest l4" data-n="3"><div class="nt">Datum</div><div class="nv">World Geodetic System 1984 ensemble</div>
                <div class="nest l5" data-n="4"><div class="nt">Ellipsoid</div><div class="nv">WGS 84: a = 6 378 137 m, 1/f = 298.257223563</div></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card" id="nestOut" style="margin:0"></div>
    </div>
  </div>
  <div class="callout note"><span class="label">Why both families exist</span><p>Flattening the Earth always distorts something — the QGIS introduction: “every map shows distortions of angular conformity, distance and area”. <em>Which</em> property is distorted, and by how much, is Chapter 6. This chapter’s job is only to recognise the family, the unit and the identifier.</p></div>

  <h3>Reading the numbers as a first clue (not proof)</h3>
  <div class="table-wrap"><table>
    <thead><tr><th>You see</th><th>It is probably</th><th>Because</th></tr></thead>
    <tbody>
      <tr><td>Values within −90…90 and −180…180 with decimals</td><td>Geographic (degrees)</td><td>Only angles fit that range — but see 5.5 for the trap</td></tr>
      <tr><td>Values in the hundreds of thousands and millions</td><td>Projected (metres or feet)</td><td>Typical of eastings/northings with offsets added</td></tr>
      <tr><td>Small values like 0–2000 with a stated local origin</td><td>A local/engineering grid or our practice grid</td><td>Real projected systems rarely put a city near (0, 0)</td></tr>
    </tbody></table></div>
  <p class="small">These are clues. Module 5.7 explains why a clue never allows you to <em>assign</em> a CRS.</p>

  <h2><span class="mod">5.4.2</span>EPSG:4326, EPSG:3857 and the UTM family</h2>
  <p><strong>What “EPSG” means.</strong> The EPSG Geodetic Parameter Dataset is a public register of CRS definitions, each with a number. ArcGIS calls the number a <strong>WKID</strong> (well-known ID). The number is a <em>key</em>; the record is the <em>definition</em>. Memorising keys without reading records is the mistake 5.4.3 corrects. The three cards below were read from the register (dataset v13.103) on 19 September 2026.</p>
  <div class="tabs"><button>EPSG:4326</button><button>EPSG:3857</button><button>EPSG:32643</button></div>
  <div class="tabpanel"><div class="rec"><h4>EPSG:4326 — WGS 84</h4><table>
    <tr><td>Type</td><td>Geographic 2D</td></tr><tr><td>Datum</td><td>World Geodetic System 1984 ensemble</td></tr>
    <tr><td>Coordinate system</td><td>“Ellipsoidal 2D CS. Axes: latitude, longitude. Orientations: north, east. UoM: degree”</td></tr>
    <tr><td>Extent (area of use)</td><td>World (by country)</td></tr><tr><td>Scope</td><td>“Horizontal component of 3D system”</td></tr>
    <tr><td>In everyday words</td><td>The latitude/longitude system of GPS-style data, and the one GeoJSON assumes (5.5). Esri’s developer site calls 4326 the most common spatial reference for storing data across the whole world.</td></tr>
    <tr><td>Software labels (verify in your install)</td><td>ArcGIS Pro: <em>WGS 1984</em> (WKID 4326). QGIS: <em>EPSG:4326 - WGS 84</em>.</td></tr></table></div></div>
  <div class="tabpanel"><div class="rec"><h4>EPSG:3857 — WGS 84 / Pseudo-Mercator (“Web Mercator”)</h4><table>
    <tr><td>Type</td><td>Projected</td></tr><tr><td>Base CRS</td><td>WGS 84</td></tr>
    <tr><td>Coordinate system</td><td>“Cartesian 2D CS. Axes: easting, northing (X,Y). Orientations: east, north. UoM: m.”</td></tr>
    <tr><td>Extent</td><td>World — 85°S to 85°N</td></tr><tr><td>Scope</td><td>“Web mapping and visualisation.”</td></tr>
    <tr><td>Remarks (quoted)</td><td>“Not a recognised geodetic system. Uses spherical development of ellipsoidal coordinates. Relative to WGS 84 / World Mercator (CRS code 3395) gives errors of 0.7 percent in scale and differences in northing of up to 43km in the map (21km on the ground).”</td></tr>
    <tr><td>In everyday words</td><td>What Google Maps, OpenStreetMap and ArcGIS basemaps draw in. Esri: “the de facto standard for web maps and online services”, with “enormous area and distance distortions away from the equator”.</td></tr>
    <tr><td>Software labels (verify)</td><td>ArcGIS Pro: <em>WGS 1984 Web Mercator (auxiliary sphere)</em> — WKID 3857; older files say 102100. QGIS: <em>EPSG:3857 - WGS 84 / Pseudo-Mercator</em>.</td></tr></table></div></div>
  <div class="tabpanel"><div class="rec"><h4>EPSG:32643 — WGS 84 / UTM zone 43N</h4><table>
    <tr><td>Type</td><td>Projected</td></tr><tr><td>Base CRS</td><td>WGS 84</td></tr>
    <tr><td>Coordinate system</td><td>“Cartesian 2D CS. Axes: easting, northing (E,N). Orientations: east, north. UoM: m.”</td></tr>
    <tr><td>Extent</td><td>World — N hemisphere — 72°E to 78°E — by country</td></tr><tr><td>Scope</td><td>“Navigation and medium accuracy spatial referencing.”</td></tr>
    <tr><td>Projection parameters (conversion 16043)</td><td>Transverse Mercator; longitude of origin 75°; scale 0.9996; false easting 500 000 m; false northing 0</td></tr>
    <tr><td>In everyday words</td><td>The metres-based system for a 6°-wide strip of the northern hemisphere between 72°E and 78°E. Next strip east is zone 44N; south of the equator it is 43S.</td></tr>
    <tr><td>Software labels (verify)</td><td>ArcGIS Pro: <em>WGS 1984 UTM Zone 43N</em> (WKID 32643). QGIS: <em>EPSG:32643 - WGS 84 / UTM zone 43N</em>.</td></tr></table></div></div>

  <h3>UTM is a family, not one CRS</h3>
  <p>UTM divides the world into “60 equal zones that are all 6 degrees wide in longitude … numbered 1 to 60, starting at the antimeridian (zone 1 at 180 degrees West longitude) and progressing East” (QGIS). Each zone has a <strong>north</strong> and a <strong>south</strong> version; southern zones add “a false northing value of 10,000,000 m”. Inside a zone, the easting is measured from the zone’s centre line plus a false easting of 500 000 m, and the northing from the equator. Type a longitude:</p>
  <div class="try">
    <span class="tag">Try it</span>
    <div class="controls"><label>Longitude (° E, negative = W) <input type="number" id="zLon" value="72.6" step="0.1" min="-180" max="180" style="width:110px"></label>
      <label>Hemisphere <select id="zHem"><option value="N">north</option><option value="S">south</option></select></label></div>
    <div class="calc" id="zCalc"></div>
    <p style="margin:.6rem 0 .2rem"><b>India’s longitudes (about 68°E to 97°E) fall across six zones:</b></p>
    <div class="zones" id="zones"></div>
    <p class="small">That is why the course rule says: <em>never prescribe one UTM zone for all of India</em>. A UTM coordinate without its zone and hemisphere could be in any of 120 places.</p>
  </div>

  <h3>“Metres” does not mean “ground metres”</h3>
  <p>Read the EPSG:3857 card again: its unit is the metre, and its own remarks say its scale is wrong by 0.7 % even against a proper Mercator — and Mercator itself stretches distances more and more away from the equator. A unit tells you what one step is <em>called</em>, not that one step is one metre on the ground.</p>
  <div class="try">
    <span class="tag">Preview of Chapter 6</span>
    <div class="controls"><label>Latitude <input type="range" id="mLat" min="0" max="80" value="23"> <b id="mLatV" style="font-family:var(--font-mono)"></b></label></div>
    <div class="result" id="mOut"></div>
    <p class="small">Uses the standard Mercator stretch factor 1 ÷ cos(latitude). Shown here only to make the point concrete; Chapter 6 measures properly.</p>
  </div>

  <h2><span class="mod">5.4.3</span>Read the whole record — do not memorise numbers</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Field in the record</th><th>Question it answers</th><th>EPSG:32643 example</th></tr></thead>
    <tbody>
      <tr><td>Name</td><td>What is it called?</td><td>WGS 84 / UTM zone 43N</td></tr>
      <tr><td>Authority and code</td><td>What is the unique key, and who issued it?</td><td>EPSG, 32643</td></tr>
      <tr><td>Type</td><td>Geographic or projected?</td><td>Projected</td></tr>
      <tr><td>Base geographic CRS / datum</td><td>Which model of the Earth?</td><td>WGS 84 (ensemble)</td></tr>
      <tr><td>Unit of measure</td><td>What is one step called?</td><td>metre</td></tr>
      <tr><td>Axes and orientation</td><td>What do the two numbers mean, and which way do they grow?</td><td>easting (east), northing (north)</td></tr>
      <tr><td>Projection method and parameters</td><td>How was the sheet made?</td><td>Transverse Mercator; 75°E; 500 000 m; 0.9996</td></tr>
      <tr><td><strong>Area of use / extent</strong></td><td>Where is it valid?</td><td>N hemisphere, 72°E–78°E</td></tr>
      <tr><td>Scope</td><td>What was it designed for?</td><td>Navigation and medium-accuracy referencing</td></tr>
    </tbody></table></div>
  <h3>Worked example: are the survey points plausible?</h3>
  <p>Set E5-U claims “EPSG:32643”. Enter an easting and northing and let the page do the rough arithmetic from the chapter. This is a <strong>plausibility check</strong>, not a conversion: it ignores the 0.9996 scale factor and the curve of the meridians.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <div class="controls"><label>Easting (m) <input type="number" id="pE" value="254318.4" step="0.1" style="width:130px"></label><label>Northing (m) <input type="number" id="pN" value="2547906.2" step="0.1" style="width:130px"></label><label>Zone <input type="number" id="pZ" value="43" min="1" max="60" style="width:70px"></label></div>
    <div class="calc" id="pCalc"></div>
  </div>
  <p><strong>Where to read the record in software.</strong> ArcGIS Pro: on the map’s <em>Coordinate Systems</em> tab, right-click any entry and choose <strong>Details</strong> — “The valid area of use for each coordinate system is specified in the list of details, and also visually as a blue rectangle on the map at the bottom of the dialog box”. QGIS: the CRS selector shows a preview map of the “approximate area of use” and the read-only PROJ text. The EPSG register at epsg.org shows the same fields directly. Steps are in 5.7.</p>
  <div class="callout dev"><span class="label">Developer view</span><p><code>lib@4326</code> is a version key; the README tells you the API, the units and the supported platforms. Nobody ships code after reading only the version number. <strong>Where the analogy stops:</strong> a wrong library version usually fails loudly. A dataset with the wrong CRS usually draws <em>somewhere</em>, silently.</p></div>
  <div class="callout warn"><span class="label">Misconception</span><p>“The CRS unit is metres, so the dataset is ready for measurement.” EPSG:3857 is the standing counter-example: metres as a unit, distorted as a sheet. Fitness for measuring depends on the projection, the location and the area of use — Chapter 6.</p></div>

  <div class="quiz" data-answer="2" data-fb="Its unit is the metre, but the EPSG record itself states scale errors, and Mercator stretches with latitude. (a) is wrong — 3857 is in metres. (b) is wrong — 32643 covers only 72°E–78°E. (d) is wrong — 4326’s axes are latitude, longitude.">
    <div class="q">Under the EPSG records above, which statement is correct?</div>
    <div class="opts">
      <button class="opt">EPSG:3857 has degree units.</button>
      <button class="opt">EPSG:32643’s area of use is the whole of India.</button>
      <button class="opt">EPSG:3857’s unit is the metre, but the unit does not establish measurement suitability.</button>
      <button class="opt">EPSG:4326’s axes are easting, northing.</button>
    </div><div class="fb"></div>
  </div>

  <div class="callout note"><span class="label">Comprehension check (write it down)</span><p>For each of EPSG:4326, EPSG:3857 and EPSG:32643 state: the family (geographic/projected), the unit, the axis meanings, and the area of use. Then say which one you would expect a web basemap to use, and which you would <em>not</em> use for a dataset in southern India (about 8–13°N, 76–80°E) — naming the field of the record that tells you so.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const nestInfo = [
    "<h4 style='margin-top:0'>Projected CRS</h4><p>The outermost box is what the file claims. Its unit is the metre; its axes are easting and northing. Everything inside it is <em>part of its definition</em>.</p>",
    "<h4 style='margin-top:0'>Map projection + parameters</h4><p>The rule that flattens angles to a sheet. UTM zone 43N uses Transverse Mercator centred on 75°E, with 500 000 m added to every easting so that no easting is negative inside the zone. Parameters quoted from EPSG conversion 16043.</p>",
    "<h4 style='margin-top:0'>Geographic CRS</h4><p>The angles that were flattened. Change this box (a different datum) and every easting/northing above it changes too.</p>",
    "<h4 style='margin-top:0'>Datum</h4><p>Where the ellipsoid is anchored (5.3). “Ensemble” = a family of very close versions.</p>",
    "<h4 style='margin-top:0'>Ellipsoid</h4><p>The squashed-ball shape: a = 6 378 137 m, 1/f = 298.257223563 (5.3).</p>"
  ];
  const nests = document.querySelectorAll(".nest[data-n]"), nout = document.getElementById("nestOut");
  nests.forEach(n => n.addEventListener("click", ev => { ev.stopPropagation(); nests.forEach(x => x.classList.remove("on")); n.classList.add("on"); nout.innerHTML = nestInfo[+n.dataset.n]; }));
  nests[0].classList.add("on"); nout.innerHTML = nestInfo[0];

  const zCalc = document.getElementById("zCalc"), zones = document.getElementById("zones");
  const zone = () => {
    const lon = +document.getElementById("zLon").value || 0, hem = document.getElementById("zHem").value;
    const z = utmZone(lon), cm = utmCentralMeridian(z);
    zCalc.innerHTML = `zone = floor((longitude + 180) ÷ 6) + 1 = floor((${lon} + 180) ÷ 6) + 1 = floor(${fmt((lon + 180) / 6, 2)}) + 1 = <b>${z}</b>
zone ${z} covers ${cm - 3}°E to ${cm + 3}°E (centre line ${cm}°E); with hemisphere ${hem} → <b>WGS 84 / UTM zone ${z}${hem}</b>${z === 43 && hem === "N" ? " = EPSG:32643" : z >= 42 && z <= 47 && hem === "N" ? " = EPSG:326" + z : ""}`;
    zones.innerHTML = [42, 43, 44, 45, 46, 47].map(k => `<div class="${k === z ? "on" : ""}">zone ${k}<br><span style="font-size:.7rem">${utmCentralMeridian(k) - 3}°–${utmCentralMeridian(k) + 3}°E</span></div>`).join("");
  };
  ["zLon", "zHem"].forEach(id => document.getElementById(id).addEventListener("input", zone)); zone();

  const mLat = document.getElementById("mLat");
  const merc = () => { const la = +mLat.value, k = 1 / Math.cos(la * Math.PI / 180); document.getElementById("mLatV").textContent = la + "°"; document.getElementById("mOut").innerHTML = `At ${la}° latitude one Web Mercator “metre” is about <b>${fmt(k, 3)}</b> ground metres — a stretch of about <b>${fmt((k - 1) * 100, 1)} %</b>. A 1 000 m distance measured on the sheet is really about ${fmtInt(1000 / k)} m on the ground.${la >= 60 ? " Near the poles the sheet is wildly stretched — this is why Greenland looks bigger than India on web maps." : ""}`; };
  mLat.addEventListener("input", merc); merc();

  const pCalc = document.getElementById("pCalc");
  const plaus = () => {
    const e = +document.getElementById("pE").value || 0, n = +document.getElementById("pN").value || 0, z = +document.getElementById("pZ").value || 43;
    const r = roughFromUtm(e, n, z);
    const eOk = e >= 166000 && e <= 834000, nOk = n >= 0 && n <= 10000000;
    pCalc.innerHTML = `Easting band check: 166 000 – 834 000 m (3° each side of the centre at the equator) → ${eOk ? "<b>inside</b>" : "<b>OUTSIDE — not a normal UTM easting</b>"}
Northing check: 0 – 10 000 000 m (north hemisphere) → ${nOk ? "<b>inside</b>" : "<b>OUTSIDE</b>"}
Distance from centre line: ${fmtInt(e)} − 500 000 = <b>${fmtInt(e - 500000)} m</b> (${e < 500000 ? "west" : "east"} of ${r.cm}°E)
Rough latitude: ${fmtInt(n)} ÷ 111 000 ≈ <b>${fmt(r.lat, 2)}°</b>
One degree of longitude there ≈ 111.32 × cos(${fmt(r.lat, 1)}°) ≈ ${fmt(kmPerDegLon(r.lat), 1)} km
Rough longitude: ${r.cm} ${e < 500000 ? "−" : "+"} ${fmt(Math.abs(e - 500000) / 1000, 1)} ÷ ${fmt(kmPerDegLon(r.lat), 1)} ≈ <b>${fmt(r.lon, 2)}°E</b>
Inside zone ${z}’s band ${r.cm - 3}°–${r.cm + 3}°E? ${r.lon >= r.cm - 3 && r.lon <= r.cm + 3 ? "<b>yes — consistent with the claim</b>" : "<b>no — the claim looks wrong</b>"}

What this establishes: the numbers are CONSISTENT with EPSG:${z >= 1 && z <= 60 ? 32600 + z : "?"}.
What it does not: that the claim is TRUE — a file in the next zone with a wrong label would pass the same checks.`;
  };
  ["pE", "pN", "pZ"].forEach(id => document.getElementById(id).addEventListener("input", plaus)); plaus();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
