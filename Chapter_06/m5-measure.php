<?php $page = ['title' => '6.5 Planar vs geodesic measurement', 'chapter' => 6, 'module' => '6.5']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 6.5 · General idea, tied to each product’s wording</div>
    <h1>Measuring on the flat map, or on the curved Earth</h1>
    <p class="lead">There are two honest ways to measure a distance. <strong>Planar</strong>: on the flat map, with school geometry (Pythagoras). <strong>Geodesic</strong>: along the curved surface of the Earth model. Each is right for its own question. What is <em>never</em> right is subtracting degrees and calling the result metres.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Define planar and geodesic distance and find where each product lets you choose.</li>
      <li>See with numbers why degrees are angles, not lengths — and why lat/long data can <em>still</em> give a correct metre distance with the right method.</li>
      <li>State exactly what Web Mercator’s metres do and do not promise.</li></ul></div>
  </div>

  <h2><span class="mod">6.5.1</span>Two ways to measure</h2>
  <div class="grid-2">
    <div class="card"><h3 style="margin-top:0">Planar (flat-map) distance</h3><p>Take the two points’ projected x, y in metres and use Pythagoras: √((x₂−x₁)² + (y₂−y₁)²). Exactly right <em>on the map sheet</em>. How close that is to the ground depends on how much the projection stretches at that place (6.1). Esri: planar buffers “are appropriate when analyzing distances around features in a projected coordinate system in a relatively small area (such as one UTM zone)”.</p></div>
    <div class="card"><h3 style="margin-top:0">Geodesic distance</h3><p>The length of the <em>shortest path along the surface</em> of the Earth model (the ellipsoid). ArcGIS Pro’s Measure tool: “The shortest line between two points on the earth’s surface on a spheroid (ellipsoid)”. Works at any size — a street or a continent — because it never flattens anything. Esri recommends it when inputs “cover multiple UTM zones, large regions, or the entire globe”.</p></div>
  </div>
  <p>There is also a third, cheaper option some libraries use: a <strong>spherical</strong> calculation (treat the Earth as a perfect ball). It differs from the ellipsoid answer by up to about 0.5 %. PostGIS shows both in its manual: 123.80 m on the spheroid versus 123.48 m on the sphere. When a library says “geodesic” or “great-circle”, check which one it means.</p>
  <h3>Where each product lets you choose</h3>
  <div class="table-wrap"><table>
    <thead><tr><th>Product</th><th>How you pick</th></tr></thead>
    <tbody>
      <tr><td>ArcGIS Pro <strong>Buffer</strong></td><td><strong>Method</strong>: Planar (default) or Geodesic (shape preserving). Projected input ⇒ flat; geographic input + a distance in metres ⇒ geodesic.</td></tr>
      <tr><td>ArcGIS Pro <strong>Measure</strong> tool</td><td>Modes: Planar (default; “only available when measuring in a projected coordinate system”), Geodesic, Loxodromic, Great Elliptic.</td></tr>
      <tr><td>ArcGIS Pro <strong>Calculate Geometry Attributes</strong></td><td>“Length” / “Area” (flat) versus “Length (geodesic)” / “Area (geodesic)”. Flat is refused for geographic or Web Mercator input.</td></tr>
      <tr><td><strong>QGIS</strong> measure tools &amp; expressions</td><td>Ellipsoidal by default (project ellipsoid); “None / Planimetric” gives flat values. The Measure dialog offers Cartesian or Ellipsoidal.</td></tr>
      <tr><td><strong>PostGIS</strong></td><td><code>geometry</code> type: flat, “in projected units (spatial ref units)”. <code>geography</code> type: geodesic “in meters, compute on the spheroid” (or a sphere with <code>use_spheroid=false</code>).</td></tr>
      <tr><td><strong>ArcGIS Maps SDK for JavaScript</strong> 5.1</td><td><code>planarLength</code>/<code>planarArea</code> use projected coordinates and ignore curvature; <code>geodesicLength</code>/<code>geodesicArea</code>/<code>geodesicBuffer</code> “only works with WGS84 (wkid: 4326) and Web Mercator” — and for those two the docs say geodesic is best practice.</td></tr>
    </tbody></table></div>
  <div class="callout dev"><span class="label">Developer view</span><p>Planar vs geodesic is like fixed-point integer maths vs floating point: the first is cheap and exact <em>within its model</em>; the second models the real thing more faithfully at a cost. <strong>Where it breaks:</strong> with fixed-point you can compute the error bound from the type alone. The planar error depends on <em>where on the Earth</em> you are.</p></div>

  <h2><span class="mod">6.5.2</span>Degrees are angles, not metres</h2>
  <p>Q1 is at 23.002° N and Q2 at 23.008° N, same longitude. The difference is 0.006°. That is <em>not</em> a distance; it is an angle. To turn it into metres you need to know how long a degree is <em>here</em> — and that is not a constant:</p>
  <ul>
    <li><strong>One degree of latitude</strong> is about 110.7 km at 23° N (roughly 111 km anywhere). So 0.006° ≈ 664 m.</li>
    <li><strong>One degree of longitude</strong> shrinks as you go towards the poles — by the factor cos(latitude), because the circles of longitude get smaller. At 23° N, cos 23° = 0.92, and a degree of longitude is about 102.5 km. At Kashmir’s latitude (34° N) it is about 92 km; at the equator 111 km.</li>
  </ul>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>How long is one degree here?</h3>
    <div class="controls"><label>Latitude <input type="range" id="latR" min="0" max="80" step="1" value="23"> <span class="mono" id="latV">23° N</span></label></div>
    <div class="tiles" id="degTiles"></div>
    <figure class="map-fig" id="degFig"></figure>
    <div class="result">Try 0°, 23°, 34° and 60°. The north–south length hardly moves; the east–west length collapses towards the pole. This is why a “square” of 0.010° × 0.010° is a <em>rectangle</em> on the ground, and why Fixture E6’s wards are about 1,025 m wide but 1,107 m tall.</div>
  </div>
  <h3>The trap, in numbers</h3>
  <p>Someone works out the “distance” from Q3 (74.995° E) to Q4 (75.005° E) with Pythagoras on the degrees: √(0.010² + 0²) = 0.010 “units”. Then, because the requirement is in metres, they multiply by 111,000 “because a degree is 111 km” and report <strong>1,110 m</strong>. The true ground distance is <strong>1,025 m</strong> — 8 % out, and it gets worse further north (at 60° N the same mistake is out by a factor of two). Worse still: if they simply typed <code>0.010</code> into a tool expecting metres, they asked for a <em>one-centimetre</em> buffer.</p>
  <div class="callout idea"><span class="label">The correct statement has two halves</span><p>1. A raw difference in degrees is not a metre distance, and Pythagoras on degrees is wrong by an amount that changes with latitude and direction.<br>2. <strong>Latitude/longitude data is still a perfectly good input for measuring</strong> — if you use a <em>geodesic</em> method, which is built for exactly that. Buffer makes geodesic buffers from degree input when you type a metre distance; the Measure tool’s geodesic modes work in a geographic CRS; PostGIS <code>geography</code> takes lon/lat and returns metres. You do not have to project the data to measure it. You have to <em>choose the right method</em>.</p></div>

  <h2><span class="mod">6.5.3</span>Web Mercator: metres, but stretched — and not “always wrong”</h2>
  <p>EPSG:3857 (“WGS 84 / Pseudo-Mercator”, alias Web Mercator) is a projected CRS whose units really are metres. The registry gives its scope as “Web mapping and visualisation” and remarks that it is “not a recognised geodetic system”. The formulas are short (a = 6,378,137 m):</p>
  <pre class="code">E = a · longitude(in radians)          N = a · ln( tan(45° + latitude/2) )</pre>
  <p>Because <em>a</em> multiplies longitude directly, one degree of longitude is the <em>same</em> 111.32 km of easting at every latitude — which is exactly wrong on the ground, where it shrinks by cos(latitude). The EPSG guidance note gives the stretch factors: east–west <strong>k = a / (ν cos φ)</strong> and north–south <strong>h = a / (ρ cos φ)</strong>, and notes “h and k are not equal, which demonstrates the non-conformality of the Pseudo-Mercator method”. (ν and ρ are the two curvature radii of the ellipsoid at that latitude — you never need to compute them by hand; the page does it below.)</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>How much does Web Mercator stretch a 1 km square?</h3>
    <div class="controls"><label>Latitude <input type="range" id="wmR" min="0" max="75" step="1" value="23"> <span class="mono" id="wmV">23° N</span></label></div>
    <div class="grid-2">
      <div class="tiles" id="wmTiles"></div>
      <figure class="map-fig" id="wmFig"></figure>
    </div>
    <div class="result" id="wmOut"></div>
  </div>
  <p><strong>Now the balance.</strong> None of this makes “any operation involving EPSG:3857” wrong. Correct uses: drawing tiled basemaps (its purpose); locating a feature (a Web Mercator coordinate identifies a place exactly, because the projection is reversible); <em>geodesic</em> measurement on Web Mercator geometries (the JavaScript SDK’s <code>geodesicLength</code> explicitly supports it); even flat measurement <em>when the accepted error is written down and small enough</em> — which is rarely true at 23° N (9 %) and never for comparing areas across latitudes. What <em>is</em> wrong is flat measurement in Web Mercator <strong>presented as ground distance or area</strong>. ArcGIS Pro’s Calculate Geometry Attributes refuses to do that for exactly this reason.</p>

  <div class="callout warn"><span class="label">Common mistake</span><p><em>“Web Mercator distances are wrong by the same factor everywhere, so I’ll just correct them.”</em> The factor depends on latitude, on direction (h ≠ k), and on the ellipsoid radii at that latitude. One correction factor is itself only an approximation over a small area — at which point a proper local projection or a geodesic method is simpler and honest.</p></div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>True, false, or “depends on the method”?</h3>
    <div class="sorter" data-items='[
      {"t":"A layer stored in EPSG:4326 cannot give a correct distance in metres.","bin":"False","why":"geodesic methods take degree input and return metres"},
      {"t":"A layer in EPSG:3857 has metre units, so a flat distance from it is a ground distance.","bin":"False","why":"units are metres but lengths are stretched by h and k"},
      {"t":"Two points 0.010° apart in longitude are the same ground distance apart at 23° N as at 60° N.","bin":"False","why":"a degree of longitude shrinks with cos(latitude)"},
      {"t":"A spherical great-circle distance and an ellipsoidal geodesic distance are the same thing.","bin":"False","why":"different Earth models; they differ by up to ~0.5 %"},
      {"t":"Measuring Q1–Q2 on a UTM 43N map gives a number close to the ground distance.","bin":"True","why":"inside the zone near the meridian the scale is 0.9996"},
      {"t":"Typing 500 into a buffer tool on a degrees layer gives a 500 m buffer.","bin":"Depends on the method","why":"ArcGIS Buffer: yes if you give the unit (metres) — it goes geodesic; a tool that uses layer units would read 500 degrees"}
    ]'>
      <div class="items"></div>
      <div class="bins">
        <div class="bin" data-bin="True"><h5>True</h5></div>
        <div class="bin" data-bin="False"><h5>False</h5></div>
        <div class="bin" data-bin="Depends on the method"><h5>Depends on the method</h5></div>
      </div>
    </div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  // one degree tiles
  const latR = document.getElementById("latR");
  function updDeg() {
    const l = +latR.value; document.getElementById("latV").textContent = l + "° N";
    const dlat = meridianArc(l - 0.5 < 0 ? 0 : l - 0.5, l + 0.5), dlon = parallelArc(l, 1);
    document.getElementById("degTiles").innerHTML = `
      <div class="tile okish"><div class="k">1° of latitude (north–south)</div><div class="v">${(dlat / 1000).toFixed(1)} km</div><div class="s">almost the same everywhere</div></div>
      <div class="tile good"><div class="k">1° of longitude (east–west)</div><div class="v">${(dlon / 1000).toFixed(1)} km</div><div class="s">cos(${l}°) = ${Math.cos(rad(l)).toFixed(3)}</div></div>
      <div class="tile"><div class="k">0.010° × 0.010° “square”</div><div class="v">${(dlon / 100).toFixed(0)} × ${(dlat / 100).toFixed(0)} m</div><div class="s">wide × tall on the ground</div></div>`;
    const svg = newSvg(document.getElementById("degFig"), "0 0 1000 300", "Schematic: a 0.010 by 0.010 degree cell drawn at its true ground proportions for the chosen latitude.");
    const w = 220 * (dlon / dlat), h = 220; const x = 500 - w / 2, y = 30;
    mkEl("rect", { x, y, width: w, height: h, class: "wardE" }, svg);
    txt(svg, 500, y + h / 2 + 8, "0.010° × 0.010°", "lbl big", { "text-anchor": "middle" });
    txt(svg, 500, y + h + 40, `${(dlon / 100).toFixed(0)} m wide`, "lbl", { "text-anchor": "middle" });
    txt(svg, x + w + 16, y + h / 2 + 8, `${(dlat / 100).toFixed(0)} m tall`, "lbl");
    txt(svg, 20, 290, "Drawn to true ground proportions (schematic).", "lbl warn");
  }
  latR.addEventListener("input", updDeg); updDeg();
  // web mercator stretch
  const wmR = document.getElementById("wmR");
  function updWm() {
    const l = +wmR.value; document.getElementById("wmV").textContent = l + "° N";
    const f = wmFactors(l);
    document.getElementById("wmTiles").innerHTML = `
      <div class="tile good"><div class="k">On the ground</div><div class="v">1,000 × 1,000 m</div><div class="s">the real square</div></div>
      <div class="tile bad"><div class="k">On a Web Mercator map</div><div class="v">${fmtN(1000 * f.k)} × ${fmtN(1000 * f.h)} “m”</div><div class="s">wide × tall in map metres</div></div>
      <div class="tile"><div class="k">Stretch east–west, k</div><div class="v">${f.k.toFixed(3)}</div><div class="s">≈ 1 / cos(${l}°)</div></div>
      <div class="tile"><div class="k">Stretch north–south, h</div><div class="v">${f.h.toFixed(3)}</div><div class="s">not equal to k → shapes distort slightly</div></div>
      <div class="tile bad"><div class="k">Area factor ≈ k × h</div><div class="v">${(f.k * f.h).toFixed(3)}</div><div class="s">+${((f.k * f.h - 1) * 100).toFixed(1)} % area</div></div>`;
    const svg = newSvg(document.getElementById("wmFig"), "0 0 1000 420", "Schematic: a 1 km ground square (green) and its Web Mercator drawing (red dashed) at the chosen latitude.");
    const s = 120; const sw = Math.min(s * f.k, 480), sh = Math.min(s * f.h, 380);
    mkEl("rect", { x: 500 - s / 2, y: 210 - s / 2, width: s, height: s, class: "wardE utm" }, svg);
    mkEl("rect", { x: 500 - sw / 2, y: 210 - sh / 2, width: sw, height: sh, class: "wardE wm" }, svg);
    txt(svg, 500, 210 + s / 2 - 8, "ground 1 km", "lbl ok", { "text-anchor": "middle" });
    txt(svg, 500 + sw / 2 + 10, 210 - sh / 2 + 24, "Web Mercator", "lbl warn");
    txt(svg, 20, 400, "Schematic: proportions are exact (k, h from the EPSG formulas); absolute size is arbitrary.", "lbl warn");
    document.getElementById("wmOut").innerHTML = `At ${l}° N a flat metre on a Web Mercator map is about <strong>${((f.k - 1) * 100).toFixed(1)} % longer</strong> than a ground metre going east and <strong>${((f.h - 1) * 100).toFixed(1)} %</strong> going north. Areas are inflated by about <strong>${((f.k * f.h - 1) * 100).toFixed(1)} %</strong>. ${l >= 60 ? "By here the map is roughly double size — this is why Greenland looks huge." : l <= 5 ? "Near the equator the stretch almost disappears (k = 1 exactly at 0°)." : ""}`;
  }
  wmR.addEventListener("input", updWm); updWm();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
