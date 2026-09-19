<?php $page = ['title' => '6.7 The comparison experiment', 'chapter' => 6, 'module' => '6.7']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 6.7 · Experiment (numbers computed live in this page)</div>
    <h1>Measure the same two points five ways</h1>
    <p class="lead">Take two pairs of requests from Fixture E6 and measure each pair with an appropriate flat method (UTM 43N), a geodesic method, a deliberately unsuitable flat method (Web Mercator), a sphere, and — as a warning — raw degrees. Then explain <em>every</em> difference by its cause. Nothing here is “ground truth”; each column is the right answer to a different question.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Read the answer table and the method behind every column.</li>
      <li>Explain the ratios 0.9996, 1.092 and 1.086 without recomputing anything.</li>
      <li>Understand what the table does <em>not</em> prove.</li></ul></div>
  </div>

  <h2><span class="mod">6.7.1</span>Inputs, methods, results</h2>
  <p>Two pairs, chosen so that every difference has an exact, explainable cause: <strong>Q1–Q2</strong> runs north–south exactly along the central meridian; <strong>Q3–Q4</strong> runs east–west across it.</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Label</th><th>Method</th><th>Reference surface / CRS</th><th>Formula source</th></tr></thead>
    <tbody>
      <tr><td><strong>G</strong> geodesic</td><td>Shortest path on the WGS 84 ellipsoid</td><td>a = 6,378,137 m; 1/f = 298.257223563</td><td>Vincenty’s inverse formulas; cross-checked two other ways (below)</td></tr>
      <tr><td><strong>U</strong> UTM 43N flat</td><td>Pythagoras on EPSG:32643 E, N</td><td>Transverse Mercator, k₀ = 0.9996, meridian 75° E</td><td>EPSG Guidance Note 7-2 §3.2.3.1</td></tr>
      <tr><td><strong>W</strong> Web Mercator flat</td><td>Pythagoras on EPSG:3857 X, Y</td><td>Sphere of radius 6,378,137 m</td><td>Guidance Note 7-2 §3.2.1.2</td></tr>
      <tr><td><strong>S</strong> sphere</td><td>Great-circle (haversine)</td><td>Sphere, mean radius 6,371,008.8 m</td><td>Spherical trigonometry</td></tr>
      <tr><td><strong>D</strong> degrees</td><td>Pythagoras on the degree values</td><td>none</td><td>— (not a distance)</td></tr>
    </tbody></table></div>
  <div class="callout note"><span class="label">How G was checked</span><p>Q1–Q2 lies on one meridian, so its geodesic is the meridian arc between the two latitudes. That arc was computed three independent ways — Vincenty, the Guidance Note’s series (the on-meridian northing ÷ 0.9996), and numerical integration of the ellipsoid’s curvature — and all three agree to <strong>664.4645 m</strong>. Q3–Q4 lies on one parallel; Vincenty agrees with the parallel-arc formula to four decimals. That is why G is trusted to 0.01 m.</p></div>

  <div class="try">
    <span class="tag">Live table</span>
    <h3>Results for Fixture E6 (computed in your browser from the formulas above)</h3>
    <div class="table-wrap"><table id="resTable">
      <thead><tr><th>Pair</th><th>G geodesic (m)</th><th>U UTM 43N (m)</th><th>W Web Mercator (m)</th><th>S sphere (m)</th><th>D degrees</th></tr></thead>
      <tbody></tbody></table></div>
    <h4>Ward A area, four ways</h4>
    <div class="tiles" id="areaTiles"></div>
    <figure class="map-fig" id="expFig"></figure>
  </div>

  <h2><span class="mod">6.7.2</span>Explain every difference</h2>
  <div class="grid-2">
    <div class="card"><h3 style="margin-top:0">U ÷ G = <span id="rUG"></span> — exactly the UTM scale factor</h3><p>Both pairs sit on or within 0.5 km of the central meridian, where the map is drawn at 0.9996 of ground size. The area ratio is 0.9992 = 0.9996², a scale factor applied in two directions. <strong>U is not “wrong”</strong>: it is smaller than the ground by a known, tiny, documented amount.</p></div>
    <div class="card"><h3 style="margin-top:0">W ÷ G = <span id="rWG"></span> — the h and k stretch factors</h3><p>North–south the ratio is h = a/(ρ cos φ); east–west it is k = a/(ν cos φ), both at 23.005° N (module 6.5). They are <em>different</em>: Web Mercator does not even stretch a small square into a bigger square, but into a slightly taller rectangle. Area ratio ≈ h × k ≈ 1.186. <strong>W is wrong by about 9 % for lengths and 19 % for areas here</strong>, and that is a property of the projection at this place, not a bug.</p></div>
    <div class="card"><h3 style="margin-top:0">S ÷ G = <span id="rSG"></span> — a different Earth model</h3><p>A ball of mean radius is a slightly different shape from the WGS 84 ellipsoid. At 23° N the ellipsoid curves more tightly north–south than the sphere (so the true N–S arc is shorter) and less tightly east–west. Which way the error goes depends on latitude and direction. <strong>S is a different model, not a measurement error</strong> — but if a library reports S while calling it “geodesic”, that is a documentation problem to write down.</p></div>
    <div class="card"><h3 style="margin-top:0">D — not comparable to anything</h3><p>0.006 and 0.010 are angles. Multiply by “111 km” and you get 666 m and 1,110 m: the first is close by luck (degrees of latitude are nearly constant), the second is 8 % out (degrees of longitude shrink with cos φ). <strong>D must never appear in a results table with a unit.</strong></p></div>
  </div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Your own two points</h3>
    <p>Enter any two points within a few km of the practice town (keep latitude between 22 and 24 and longitude between 74 and 76 so UTM zone 43N still applies) and see all five methods.</p>
    <div class="controls">
      <label>Point A lat <input type="number" id="aLat" value="23.002" step="0.001" style="width:100px"></label><label>lon <input type="number" id="aLon" value="75.000" step="0.001" style="width:100px"></label>
      <label>Point B lat <input type="number" id="bLat" value="23.005" step="0.001" style="width:100px"></label><label>lon <input type="number" id="bLon" value="74.995" step="0.001" style="width:100px"></label>
    </div>
    <div class="tiles" id="ownTiles"></div>
  </div>

  <h2><span class="mod">6.7.3</span>What the table does not prove</h2>
  <ul>
    <li><strong>No column is “ground truth”.</strong> G is the most faithful <em>to the WGS 84 ellipsoid</em>, which is itself a model. The real ground has slope (6.6.3), and the ellipsoid surface is not sea level (Chapter 5). If a colleague’s tool also gives 664.46 m, that shows both implement the same model — not that a tape measure would read 664.46 m.</li>
    <li><strong>These numbers were not produced by ArcGIS Pro or QGIS.</strong> They are the <em>expected</em> values for an instructor to reproduce (Lab page, and the Instructor Appendix of the chapter document). Until that reproduction is recorded, the lab’s expected results are formula results.</li>
    <li><strong>The experiment is local.</strong> The tidy 0.9996 and h/k explanations hold because the pairs are within 0.5 km of a central meridian at one latitude. Move to the zone edge or to 60° N and the ratios change — that is the whole point of 6.1.</li>
  </ul>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“If two tools give different numbers, one of them has a bug.”</em> In the table every column differs and none is a bug. A discrepancy between tools is a prompt to compare <strong>methods, reference surfaces and units</strong> — the fields of your log — before suspecting the software.</p></div>

  <div class="quiz" data-answer="1" data-fb="(a) Still 0.9996 — points on the central meridian have that scale at any latitude. (b) Larger: h = a/(ρ cos φ) grows as cos φ shrinks; at 45° N it is about 1.42. (c) More misleading — a degree of longitude falls to about 79 km at 45° N, so a 111 km multiplier is further wrong.">
    <div class="q">Without recomputing: for two points at 45° N on the central meridian of their zone, what happens to (a) the U/G ratio, (b) the W/G ratio, and (c) how misleading the D column is for an east–west pair?</div>
    <div class="opts">
      <button class="opt">(a) rises above 1; (b) stays 1.092; (c) unchanged.</button>
      <button class="opt">(a) stays 0.9996; (b) larger than 1.092 (about 1.42); (c) more misleading.</button>
      <button class="opt">(a) falls to 0.999; (b) smaller than 1.092; (c) less misleading.</button>
      <button class="opt">(a) stays 0.9996; (b) becomes exactly 1.0; (c) unchanged.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const [Q1, Q2, Q3, Q4] = E6.requests;
  const row = (name, a, b) => {
    const G = geodesic(a.lat, a.lon, b.lat, b.lon), U = hyp([a.E, a.N], [b.E, b.N]), W = hyp([a.X, a.Y], [b.X, b.Y]), S = haversine(a.lat, a.lon, b.lat, b.lon), D = Math.hypot(b.lon - a.lon, b.lat - a.lat);
    return { name, G, U, W, S, D };
  };
  const r1 = row("Q1–Q2 (N–S, on the central meridian)", Q1, Q2), r2 = row("Q3–Q4 (E–W, across the meridian)", Q3, Q4);
  document.querySelector("#resTable tbody").innerHTML = [r1, r2].map(r => `<tr><td>${r.name}</td><td class="mono"><strong>${fmtN(r.G, 2)}</strong></td><td class="mono">${fmtN(r.U, 2)}</td><td class="mono">${fmtN(r.W, 2)}</td><td class="mono">${fmtN(r.S, 2)}</td><td class="mono">${r.D.toFixed(3)}</td></tr>`).join("");
  document.getElementById("rUG").textContent = (r1.U / r1.G).toFixed(4);
  document.getElementById("rWG").textContent = (r1.W / r1.G).toFixed(3) + " (N–S) and " + (r2.W / r2.G).toFixed(3) + " (E–W)";
  document.getElementById("rSG").textContent = (r1.S / r1.G).toFixed(3) + " (N–S), " + (r2.S / r2.G).toFixed(3) + " (E–W)";
  const A = E6.wards[0], ring = wardRing(A);
  const aEll = ellipsoidRectArea(A.lat[0], A.lat[1], A.lon[0], A.lon[1]);
  const aUtm = shoelace(ring.map(([lo, la]) => { const u = utm(la, lo, 43); return [u.E, u.N]; }));
  const aWm = shoelace(ring.map(([lo, la]) => { const w = webMerc(la, lo); return [w.X, w.Y]; }));
  document.getElementById("areaTiles").innerHTML = `
    <div class="tile good"><div class="k">Ellipsoidal (geodesic)</div><div class="v">${fmtN(aEll, 1)} m²</div><div class="s">true surface area of the lat/lon rectangle</div></div>
    <div class="tile okish"><div class="k">UTM 43N flat</div><div class="v">${fmtN(aUtm, 1)} m²</div><div class="s">ratio ${(aUtm / aEll).toFixed(4)} = 0.9996²</div></div>
    <div class="tile bad"><div class="k">Web Mercator flat</div><div class="v">${fmtN(aWm, 1)} m²</div><div class="s">ratio ${(aWm / aEll).toFixed(3)} ≈ h × k</div></div>
    <div class="tile na"><div class="k">“square degrees”</div><div class="v">0.0001</div><div class="s">not an area</div></div>`;
  renderE6(document.getElementById("expFig"), { crs: "utm", lines: [{ from: [Q1.lat, Q1.lon], to: [Q2.lat, Q2.lon], cls: "geo", label: "Q1–Q2", dx: 14 }, { from: [Q3.lat, Q3.lon], to: [Q4.lat, Q4.lon], cls: "geo", label: "Q3–Q4", dy: -14 }], caption: "The two measured pairs on the UTM 43N grid (made-up data)." });
  // own points
  const ids = ["aLat", "aLon", "bLat", "bLon"];
  function own() {
    const [la1, lo1, la2, lo2] = ids.map(i => parseFloat(document.getElementById(i).value));
    if (ids.some(i => isNaN(parseFloat(document.getElementById(i).value)))) return;
    const ua = utm(la1, lo1, 43), ub = utm(la2, lo2, 43), wa = webMerc(la1, lo1), wb = webMerc(la2, lo2);
    const G = geodesic(la1, lo1, la2, lo2), U = hyp([ua.E, ua.N], [ub.E, ub.N]), W = hyp([wa.X, wa.Y], [wb.X, wb.Y]), S = haversine(la1, lo1, la2, lo2), D = Math.hypot(lo2 - lo1, la2 - la1);
    const warn = (Math.abs(lo1 - 75) > 3 || Math.abs(lo2 - 75) > 3) ? "<div class='tile bad'><div class='k'>warning</div><div class='v'>outside zone 43</div><div class='s'>the U column is now meaningless</div></div>" : "";
    document.getElementById("ownTiles").innerHTML = `
      <div class="tile good"><div class="k">G geodesic</div><div class="v">${fmtN(G, 2)} m</div><div class="s">on the WGS 84 ellipsoid</div></div>
      <div class="tile okish"><div class="k">U UTM 43N</div><div class="v">${fmtN(U, 2)} m</div><div class="s">ratio ${(U / G).toFixed(4)}</div></div>
      <div class="tile bad"><div class="k">W Web Mercator</div><div class="v">${fmtN(W, 2)} m</div><div class="s">ratio ${(W / G).toFixed(3)}</div></div>
      <div class="tile"><div class="k">S sphere</div><div class="v">${fmtN(S, 2)} m</div><div class="s">ratio ${(S / G).toFixed(4)}</div></div>
      <div class="tile na"><div class="k">D degrees</div><div class="v">${D.toFixed(4)}</div><div class="s">not a distance</div></div>${warn}`;
  }
  ids.forEach(i => document.getElementById(i).addEventListener("input", own)); own();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
