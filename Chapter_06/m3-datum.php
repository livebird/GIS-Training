<?php $page = ['title' => '6.3 Datum transformations and evidence', 'chapter' => 6, 'module' => '6.3']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 6.3 · General idea, with ArcGIS / QGIS behaviour</div>
    <h1>Datum transformations: when a projection change is not enough</h1>
    <p class="lead">Two “latitude/longitude” systems can give <em>different numbers for the same place</em>, because they model the Earth with slightly different ellipsoids anchored at different points. Moving between them needs a <strong>datum transformation</strong> — a measured shift with an accuracy, an area where it is valid, and often several competing versions. You must read the record before you trust it.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>See why changing between geographic references needs more than projection maths.</li>
      <li>Read a real transformation record for its <strong>applicability, area, resources and accuracy</strong>.</li>
      <li>Know what your instructor must verify in the installed software before writing exact steps.</li></ul></div>
  </div>

  <h2><span class="mod">6.3.1</span>Same datum or different datum?</h2>
  <p>Remember from Chapter 5: a projected CRS = a geographic CRS + a map projection. So when you move data between two projected systems, there are two possible kinds of change:</p>
  <div class="grid-2">
    <div class="card"><h3 style="margin-top:0">Same geographic CRS underneath</h3><p>Example: WGS 84 / UTM zone 43N → WGS 84 degrees. Only the projection maths changes. This is exact and needs no extra information. In ArcGIS Pro’s Project tool the Geographic Transformation box simply stays empty: “When no geographic or datum transformation is required, no drop-down list will appear.”</p><div class="status-line ok">Projection only. Fixture E6 is like this — both files are on WGS 84.</div></div>
    <div class="card"><h3 style="margin-top:0">Different geographic CRS underneath</h3><p>Example: Kalianpur 1975 → WGS 84. Now the two systems model the Earth differently, and the <em>same place has different latitude/longitude values in each</em>. Converting needs a <strong>datum transformation</strong> as well: ArcGIS builds a drop-down list of candidates “based on the input and output datums, and a default transformation will be applied”.</p><div class="status-line bad">Projection + transformation. Read the record.</div></div>
  </div>
  <figure class="map-fig" id="datumFig"></figure>
  <div class="callout note"><span class="label">Words</span><p>In the EPSG registry a <strong>conversion</strong> is exact maths within one datum (a projection is a conversion). A <strong>transformation</strong> changes datum and was <em>measured</em> — so it has an accuracy and an area where it applies. Esri calls it a “geographic transformation”; QGIS says “datum transformation” or “coordinate operation”. Same idea.</p></div>
  <div class="callout dev"><span class="label">Developer view</span><p>A projection change is like converting a timestamp between time zones that both hang off the same UTC — a pure function, exact answer. A datum transformation is like converting between two <em>clocks that were set independently</em>: you need a measured offset, valid for a period, with a stated uncertainty, and there may be several published offsets of different quality. <strong>Where it breaks:</strong> a datum offset also changes <em>with location</em>, which is why every record carries an area of use.</p></div>

  <h2><span class="mod">6.3.2</span>Read the record, don’t accept the default</h2>
  <p>Here is a real record from the EPSG registry (dataset v13.103, read 19 Sept 2026). Click each row to see what it tells you. The four highlighted rows are the ones you must always inspect.</p>
  <div class="epsg" id="epsg">
    <div class="row key"><b>Name / code</b><span>Kalianpur 1975 to WGS 84 (1) — EPSG:1156</span><span class="why">“(1)” means other variants may exist. Always log the code, not just the name.</span></div>
    <div class="row key"><b>Applicability</b><span>Source: Kalianpur 1975 (EPSG:4146, ellipsoid Everest 1830 (1975 Definition)) → Target: WGS 84. Method reversible: yes.</span><span class="why">Check the pair and direction match your data. “Reversible” is true of nearly every method — it says nothing about quality.</span></div>
    <div class="row"><b>Method</b><span>Geocentric translations (geog2D domain): ΔX = 295 m, ΔY = 736 m, ΔZ = 257 m</span><span class="why">A simple 3-number shift applied to Earth-centred X, Y, Z. Needs no extra files. Other methods (7-parameter, or grid files like NTv2) can be more accurate but need resources.</span></div>
    <div class="row key"><b>Area of use</b><span>“Asia – India mainland and Nepal”</span><span class="why">Do NOT use it for Sri Lanka, Bangladesh or offshore areas — they have their own records. Outside its area the shift is simply wrong.</span></div>
    <div class="row key"><b>Accuracy</b><span>22 m</span><span class="why">After transforming, positions may still be wrong by this order. The accuracy is part of your answer, not a footnote.</span></div>
    <div class="row"><b>Remarks</b><span>“Derived at 7 stations. Accuracy 12m, 10m and 15m in X, Y and Z axes. Care! DMA ellipsoid is inconsistent with EPSG ellipsoid – transformation parameter values may not be appropriate. Also source CRS may not apply to Nepal.”</span><span class="why">The registry itself is warning you. Read remarks every time.</span></div>
    <div class="row"><b>Scope</b><span>“Military survey.”</span><span class="why">What it was made for. Not a promise about your municipal data.</span></div>
  </div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>What happens if the datum difference is ignored?</h3>
    <p>A legacy survey file stores request Q3 as <code>23.005° N, 74.995° E</code> in <strong>Kalianpur 1975</strong>. Someone labels it “WGS 84”. Apply the EPSG:1156 shift (with the published geocentric formulas, height taken as 0) to see where the point really is in WGS 84.</p>
    <div class="controls"><button class="btn accent small" id="applyShift">Apply EPSG:1156 shift</button> <button class="btn small" id="resetShift">Reset</button></div>
    <div class="grid-2">
      <div class="tiles" id="shiftTiles"></div>
      <figure class="map-fig" id="shiftFig"></figure>
    </div>
    <div class="result" id="shiftOut">Orange dot = where the mislabelled file puts Q3.</div>
  </div>
  <p>Two lessons. First, the datum matters even though “it’s all degrees” — here by about 112 m, roughly the width of a cricket ground. Second, the record’s <strong>22 m accuracy</strong> means even the corrected position is only known to a few tens of metres. Both facts belong in your log.</p>
  <div class="callout warn"><span class="label">Instructor must verify</span><p>The 112 m figure is a hand calculation from the published parameters. ArcGIS Pro and QGIS may offer a <em>different</em> transformation as their default for this pair, giving a different shift. Before quoting any number to learners, run the real transformation in the installed software and record its name, code and result.</p></div>

  <h3>How the products choose, and what they need</h3>
  <div class="tabs"><button>ArcGIS Pro</button><button>QGIS</button><button>PostGIS</button></div>
  <div class="tabpanel"><p>Candidates are listed “based on data extents and transformation accuracy. By default, the first transformation in the list is applied.” Equation-based methods (Molodensky, geocentric translation, coordinate frame, position vector…) need nothing extra. File-based methods (HARN, NADCON, NADCON5, GEOCON, NTv2) need grid files, some of which “are not installed with ArcGIS Pro” and must be downloaded (the ArcGIS Coordinate Systems Data package). Transformations are bidirectional: choosing “NAD_1927_to_WGS_1984_3” when going from WGS 84 to NAD 1927 is fine — the tool applies it the right way. The map’s own transformation is set on the <strong>Transformation</strong> tab of Map Properties.</p></div>
  <div class="tabpanel"><p>QGIS “will attempt to use the most accurate transformation available”. If a better one needs a grid file that is missing, it warns you and greys out that option in the list (usually with a download button). Under Settings ▸ Options ▸ Transformations you can turn on “Ask for datum transformation if several are available”, and you can save a preferred operation for a CRS pair.</p></div>
  <div class="tabpanel"><p><code>ST_Transform</code> picks a conversion automatically, can fail when grid-shift files are absent (by default it throws an error), and <code>ST_TransformPipeline</code> lets you name a specific method when it matters.</p></div>

  <h2><span class="mod">6.3.3</span>Rules for this course</h2>
  <ol>
    <li><strong>Never hard-code one transformation for everything.</strong> The right record depends on the source datum, the target datum, the area, and the accuracy you need.</li>
    <li><strong>Inspect four things</strong> before accepting one: applicability (pair and direction), area of use, required resources (grid files installed?), documented accuracy. Missing one → not yet justified.</li>
    <li><strong>Know the edge of this course.</strong> Centimetre-level, time-dependent reference-frame work (even “WGS 84” is really a family of realisations; the registry rates the ensemble at 2 m) needs a geodesist. In Phase 1 you must be able to <em>read</em> a record and <em>log</em> a choice, not derive one.</li>
  </ol>
  <div class="callout note"><span class="label">Verification item for instructors</span><p>Before writing exact lab steps that involve a datum change, record: the ArcGIS Pro version; whether the Coordinate Systems Data package (grids) is installed; the transformations the Project tool lists for the pair; the QGIS and PROJ versions and whether equivalent grids are present. The lab in 6.8 avoids all of this by keeping both files on WGS 84.</p></div>

  <div class="callout warn"><span class="label">Common mistake</span><p><em>“Latitude/longitude is latitude/longitude; the datum is a detail.”</em> Two datums give two different pairs of numbers for one place. Here the difference is about 100 m; elsewhere in the world it ranges from a few metres to several hundred. A pair of degrees without a datum is as incomplete as a UTM pair without a zone.</p></div>

  <div class="quiz" data-answer="2" data-fb="Applicability, area of use, required resources, and accuracy. “The software picked it” is a list-ordering mechanism (extent + accuracy + first in list), not a justification for your requirement; “reversible” is true of almost every method and says nothing about fit.">
    <div class="q">Two records are offered for a datum change: one covers “country – onshore”, accuracy 1 m, but needs a grid file that is not installed; the other covers “region – all”, accuracy 20 m, needs nothing, and is the software’s default. Which set of things must you check before choosing?</div>
    <div class="opts">
      <button class="opt">Only the accuracy — pick the 1 m one and install whatever it needs later.</button>
      <button class="opt">Nothing — the default was chosen by the software, so it is the safe choice.</button>
      <button class="opt">Applicability (pair and direction), area of use, required resources, and accuracy — for both; then decide from your accuracy requirement and log the reason.</button>
      <button class="opt">Whether the method is reversible; if it is, either will do.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  // EPSG record rows toggle
  document.querySelectorAll("#epsg .row").forEach(r => r.addEventListener("click", () => r.classList.toggle("on")));
  // datum schematic: two ellipses with different centres and a point
  (function () {
    const svg = newSvg(document.getElementById("datumFig"), "0 0 1000 420", "Schematic: two slightly different Earth models (ellipsoids) with different centres; the same physical point gets different latitude/longitude on each.");
    mkEl("ellipse", { cx: 500, cy: 210, rx: 300, ry: 170, class: "ellip a" }, svg);
    mkEl("ellipse", { cx: 530, cy: 225, rx: 296, ry: 168, class: "ellip b" }, svg);
    mkEl("circle", { cx: 500, cy: 210, r: 6, class: "centre" }, svg); mkEl("circle", { cx: 530, cy: 225, r: 6, fill: "#d3541f" }, svg);
    mkEl("circle", { cx: 730, cy: 95, r: 10, fill: "#2f7d4f" }, svg);
    txt(svg, 750, 90, "one physical place", "lbl ok");
    txt(svg, 750, 118, "WGS 84 says: 23.0055° N", "lbl");
    txt(svg, 750, 144, "Kalianpur 1975 says: 23.0050° N", "lbl acc");
    txt(svg, 120, 380, "solid = WGS 84 ellipsoid (centre at Earth’s mass centre)", "lbl");
    txt(svg, 120, 406, "dashed = Everest 1830 ellipsoid used by Kalianpur 1975 (fitted to India; centre offset by ~ΔX, ΔY, ΔZ)", "lbl acc");
    txt(svg, 20, 30, "Schematic — the offset is exaggerated enormously so you can see it.", "lbl warn");
  })();
  // shift demo
  const q3 = E6.requests[2]; const s = kalianpurToWGS84(q3.lat, q3.lon);
  const d = geodesic(q3.lat, q3.lon, s.lat, s.lon), dn = geodesic(q3.lat, q3.lon, s.lat, q3.lon), de = geodesic(q3.lat, q3.lon, q3.lat, s.lon);
  const tiles = document.getElementById("shiftTiles"), out = document.getElementById("shiftOut");
  function base() {
    tiles.innerHTML = `<div class="tile"><div class="k">As labelled (“WGS 84”)</div><div class="v">${q3.lat.toFixed(5)} N</div><div class="s">${q3.lon.toFixed(5)} E — numbers copied from the Kalianpur file</div></div><div class="tile na"><div class="k">True WGS 84 position</div><div class="v">?</div><div class="s">apply the shift</div></div>`;
    renderE6(document.getElementById("shiftFig"), { crs: "utm", caption: "Q3 as the mislabelled file places it (made-up data)." });
    out.textContent = "Orange dot = where the mislabelled file puts Q3.";
  }
  document.getElementById("applyShift").addEventListener("click", () => {
    tiles.innerHTML = `<div class="tile"><div class="k">As labelled (“WGS 84”)</div><div class="v">${q3.lat.toFixed(5)} N</div><div class="s">${q3.lon.toFixed(5)} E</div></div><div class="tile bad"><div class="k">True WGS 84 position (EPSG:1156)</div><div class="v">${s.lat.toFixed(5)} N</div><div class="s">${s.lon.toFixed(5)} E</div></div><div class="tile bad"><div class="k">Ground shift</div><div class="v">${d.toFixed(0)} m</div><div class="s">about ${dn.toFixed(0)} m north and ${de.toFixed(0)} m west</div></div><div class="tile okish"><div class="k">Record accuracy</div><div class="v">22 m</div><div class="s">even the corrected point is only known this well</div></div>`;
    renderE6(document.getElementById("shiftFig"), { crs: "utm", shift: { Q3: [s.lat, s.lon] }, caption: "Orange = mislabelled position; red = true WGS 84 position. ~112 m apart (made-up data)." });
    out.innerHTML = `The point moves about <strong>${d.toFixed(0)} m</strong>. On a map of the whole town you would not notice. On a ward-boundary question you would get the wrong answer. Computed with the geocentric formulas of Guidance Note 7-2 (§4.1.1, §4.2.4) and the EPSG:1156 parameters; not run in GIS software.`;
  });
  document.getElementById("resetShift").addEventListener("click", base);
  base();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
