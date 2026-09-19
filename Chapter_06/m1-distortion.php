<?php $page = ['title' => '6.1 Why flattening costs something', 'chapter' => 6, 'module' => '6.1']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 6.1 · General GIS idea</div>
    <h1>Why the choice of projection matters</h1>
    <p class="lead">A <strong>map projection</strong> is the maths that turns positions on the round Earth into positions on a flat sheet. It cannot be done without stretching. The only question is <em>what</em> you allow to stretch, and <em>where</em>.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Name the four things a flat map can get wrong: <strong>area</strong>, <strong>shape (angles)</strong>, <strong>distance</strong> and <strong>direction</strong>.</li>
      <li>See that “this projection preserves X” always means “X, in some places, under some conditions”.</li>
      <li>Decide which property matters for a small town task and which for a world map.</li></ul></div>
  </div>

  <h2><span class="mod">6.1.1</span>Four things that can go wrong</h2>
  <p>Think of the orange peel again. When you press it flat, some part of the peel is stretched. On a map that stretching shows up as one or more of these:</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Property</th><th>What it means on the ground</th><th>What it looks like when a map gets it wrong</th></tr></thead>
    <tbody>
      <tr><td><strong>Area</strong></td><td>How much land a ward covers (m², km²)</td><td>One ward drawn bigger than another although both are the same size on the ground</td></tr>
      <tr><td><strong>Shape / angles</strong></td><td>The corner where two roads meet; the outline of a water tank</td><td>A right-angle corner that is no longer 90°; a round tank drawn as an egg</td></tr>
      <tr><td><strong>Distance</strong></td><td>How long a road is</td><td>A 1 km road drawn as if it were 1.09 km</td></tr>
      <tr><td><strong>Direction</strong></td><td>Which way is north from the depot to a request</td><td>A “due north” line drawn tilted</td></tr>
    </tbody></table></div>
  <p>The official documentation says it in one sentence each way. ArcGIS: <em>“there is no perfect way to transpose a curved surface to a flat surface without some distortion.”</em> QGIS: it <em>“is usually impossible to preserve all characteristics at the same time in a map projection.”</em> A projection can keep <em>some</em> of these right — but only at one point, or along one line, or inside one zone. Never all of them, everywhere.</p>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Four coins on the globe — what happens when you flatten?</h3>
    <p>Imagine four <strong>equal</strong> coins stuck on a globe: one at the equator, one at 23° N (about the latitude of our practice town), one at 45° N and one at 70° N. Press the globe flat with different rules and watch the coins. <span class="synthetic">Schematic only</span> — the sizes here show the <em>kind</em> of change, not exact amounts.</p>
    <div class="controls">
      <button class="btn small" data-mode="ref" aria-pressed="true">On the globe (reference)</button>
      <button class="btn small" data-mode="conf">Conformal: keep shape</button>
      <button class="btn small" data-mode="area">Equal-area: keep area</button>
    </div>
    <figure class="map-fig" id="coinFig"></figure>
    <div class="result" id="coinOut"></div>
  </div>

  <h2><span class="mod">6.1.2</span>“Preserves” has small print</h2>
  <p>Projections come in families, named after what they keep right:</p>
  <div class="grid-3">
    <div class="card"><h3 style="margin-top:0">Conformal</h3><p>Keeps <strong>angles</strong> — so small shapes look right. Mercator and Web Mercator are conformal-type. The price: <strong>area</strong> goes badly wrong far from the equator. On a Mercator world map Greenland looks bigger than South America although it is only about one-eighth the size.</p></div>
    <div class="card"><h3 style="margin-top:0">Equal-area</h3><p>Keeps <strong>area</strong> in proportion everywhere. The price: shapes get squashed or stretched over large regions. Good for “how big is this state compared with that one”.</p></div>
    <div class="card"><h3 style="margin-top:0">Equidistant</h3><p>Keeps <strong>distance</strong> — but only from one centre point, or along particular lines. Not between every pair of points. Used for things like “how far from this radio tower”.</p></div>
  </div>
  <p>There are also <strong>compromise</strong> projections (Robinson, Winkel Tripel) that let everything be a little wrong so a world map looks pleasant.</p>
  <div class="callout warn"><span class="label">The word to watch</span><p><strong>“Preserves”</strong> never means “everywhere”. A conformal projection keeps angles right for <em>very small</em> shapes — a street corner, yes; the outline of India, no. An equidistant projection keeps distance from its <em>centre</em>, not between two random requests. Whenever you read “preserves X”, ask: <em>preserved where, and at what size?</em></p></div>

  <h3>UTM: excellent inside its zone, wrong outside it</h3>
  <p>UTM cuts the world into 60 strips, each 6° of longitude wide. Down the middle of each strip runs its <strong>central meridian</strong>. Along that line the map is drawn at scale <strong>0.9996</strong> — 0.04 % smaller than the ground. Moving away from the middle the scale grows through 1.0 and past it, so inside the strip the error stays tiny. Outside the strip it keeps growing, and the EPSG guidance note says the method becomes “quite unsuitable” there for engineering and large-scale mapping.</p>
  <figure class="map-fig" id="zoneFig"></figure>

  <h2><span class="mod">6.1.3</span>A town task and a world map need different things</h2>
  <p>Before choosing any projection, ask two questions: <strong>Which property matters?</strong> and <strong>Over what area?</strong> ArcGIS puts it as “the extent, location, and property you want to preserve must inform your choice”.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Pick the property and the extent for each job</h3>
    <div class="grid-2">
      <div class="card" style="margin:0">
        <h4 style="margin-top:0">Job 1 — the practice town</h4>
        <p class="small">Find the straight-line distance from each request to the nearest road, and the area of each ward. The wards together span about 2 km.</p>
        <label>Property that matters <select id="j1p"><option value="">choose…</option><option>Area and distance</option><option>Shape of continents</option><option>Compass direction across the globe</option></select></label>
        <label style="margin-top:.5rem;display:block">Extent <select id="j1e"><option value="">choose…</option><option>A few km — inside one UTM zone</option><option>The whole world</option></select></label>
        <div class="result" id="j1out">…</div>
      </div>
      <div class="card" style="margin:0">
        <h4 style="margin-top:0">Job 2 — a world map for the company newsletter</h4>
        <p class="small">Show the number of requests handled per country, so readers can compare countries by eye. Nobody will measure anything on it.</p>
        <label>Property that matters <select id="j2p"><option value="">choose…</option><option>Distance from our office</option><option>Area (so countries compare fairly)</option><option>Nothing — any projection is fine</option></select></label>
        <label style="margin-top:.5rem;display:block">Extent <select id="j2e"><option value="">choose…</option><option>A few km — inside one UTM zone</option><option>The whole world</option></select></label>
        <div class="result" id="j2out">…</div>
      </div>
    </div>
  </div>

  <h3>The same ward, three areas</h3>
  <p>Ward A of Fixture E6 was defined in degrees: 0.010° × 0.010°. Here is its area measured three ways. The numbers are calculated live from the published formulas.</p>
  <div class="tiles" id="areaTiles"></div>
  <p>Same ward, three answers. Only one was measured on the curved surface of the Earth model. The UTM answer is smaller by a known, tiny amount (0.08 %). The Web Mercator answer is <strong>18.6 % too big</strong> — not because of a bug, but because that projection stretches everything at this latitude. This is what “projection choice matters” means in practice.</p>

  <div class="callout dev"><span class="label">Developer view</span><p>A projection is a <strong>lossy encoder</strong> from a curved surface to a flat one; each projection is a different codec tuned to keep a different part of the signal. Good analogy for “lossless is impossible; pick the codec for the use”. <strong>Where it breaks:</strong> a lossy image codec degrades roughly evenly everywhere, but a projection’s error depends on <em>where you are</em> — near zero at one place, large elsewhere. That position-dependence, not an average, decides whether your measurement is trustworthy.</p></div>

  <div class="callout warn"><span class="label">Common mistake</span><p><em>“The coordinate system is in metres, so the distances are correct.”</em> No. Units tell you what to write after the number. The projection and your location decide whether the number is close to the ground value. Web Mercator is in metres and is 9 % off for lengths at 23° N.</p></div>

  <div class="quiz" data-answer="1" data-fb="No projection keeps distance right between every pair of points over such a large area — equidistant projections only keep distance from a centre or along special lines. The right first move is to ask which property matters and over what extent, and to consider a geodesic method (module 6.5) that does not need one shared projection at all.">
    <div class="q">A colleague proposes one projected coordinate system “for everything we do in South Asia and the Gulf, so all our distances are comparable”. What is the best response?</div>
    <div class="opts">
      <button class="opt">Agree — one system means one set of units, so distances will be comparable.</button>
      <button class="opt">Ask which property matters and over what extent; no single projection keeps distances right across such an area.</button>
      <button class="opt">Agree as long as the system is in metres rather than degrees.</button>
      <button class="opt">Suggest Web Mercator, because every web map uses it.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  // --- coins ---
  const lats = [0, 23, 45, 70];
  const modes = {
    ref: { title: "On the globe: all four coins are equal.", scale: () => [1, 1] },
    conf: { title: "Conformal (Mercator family): coins stay round but grow with latitude — shape kept, area not. At 70° N the coin is about 3× wider.", scale: l => { const s = 1 / Math.cos(rad(l)); return [s, s]; } },
    area: { title: "Equal-area: coins keep their area but get squashed toward the pole — area kept, shape not.", scale: l => { const s = 1 / Math.cos(rad(l)); return [s, 1 / s]; } }
  };
  function drawCoins(mode) {
    const el = document.getElementById("coinFig");
    const svg = newSvg(el, "0 0 1000 420", "Schematic: four equal coins at different latitudes, drawn under three flattening rules.");
    mkEl("rect", { x: 20, y: 20, width: 960, height: 340, class: "globe", rx: 18 }, svg);
    lats.forEach((l, i) => {
      const x = 150 + i * 240, y = 190; const [sx, sy] = modes[mode].scale(l);
      const r = 36;
      mkEl("ellipse", { cx: x, cy: y, rx: Math.min(r * sx, 118), ry: Math.min(r * sy, 118), class: "coin" + (mode === "ref" ? " ref" : "") }, svg);
      txt(svg, x, 330, l + "° N", "lbl big", { "text-anchor": "middle" });
      mkEl("line", { x1: x - 110, y1: 350, x2: x + 110, y2: 350, class: "grat" }, svg);
    });
    txt(svg, 30, 400, "Schematic only: sizes show the kind of change, not exact amounts.", "lbl warn");
    document.getElementById("coinOut").textContent = modes[mode].title;
    document.querySelectorAll("[data-mode]").forEach(b => b.setAttribute("aria-pressed", b.dataset.mode === mode));
  }
  document.querySelectorAll("[data-mode]").forEach(b => b.addEventListener("click", () => drawCoins(b.dataset.mode)));
  drawCoins("ref");

  // --- zone diagram ---
  (function () {
    const svg = newSvg(document.getElementById("zoneFig"), "0 0 1000 380", "Schematic of one UTM zone: central meridian at scale 0.9996, two true-scale lines, and growing error outside the zone.");
    mkEl("rect", { x: 0, y: 30, width: 1000, height: 280, class: "outside" }, svg);
    mkEl("rect", { x: 320, y: 30, width: 360, height: 280, class: "zone" }, svg);
    mkEl("line", { x1: 500, y1: 30, x2: 500, y2: 310, class: "cm" }, svg);
    mkEl("line", { x1: 400, y1: 30, x2: 400, y2: 310, class: "truescale" }, svg);
    mkEl("line", { x1: 600, y1: 30, x2: 600, y2: 310, class: "truescale" }, svg);
    txt(svg, 500, 340, "central meridian · scale 0.9996 (map 0.04 % smaller than ground)", "lbl big", { "text-anchor": "middle" });
    txt(svg, 400, 22, "scale 1.0", "lbl ok", { "text-anchor": "middle" });
    txt(svg, 600, 22, "scale 1.0", "lbl ok", { "text-anchor": "middle" });
    txt(svg, 340, 300, "zone edge: scale a little above 1.0", "lbl");
    txt(svg, 40, 170, "outside the zone:", "lbl warn"); txt(svg, 40, 200, "error keeps growing —", "lbl warn"); txt(svg, 40, 230, "use the next zone", "lbl warn");
    txt(svg, 720, 170, "one zone = 6° of longitude", "lbl"); txt(svg, 720, 200, "(75° E is the middle of zone 43)", "lbl");
    txt(svg, 20, 370, "Schematic; line positions not to scale. 0.9996 and 1.0 are the only exact numbers here.", "lbl warn");
  })();

  // --- jobs ---
  function job(pId, eId, outId, okP, okE, msgOk) {
    const upd = () => {
      const p = document.getElementById(pId).value, e = document.getElementById(eId).value, out = document.getElementById(outId);
      if (!p || !e) { out.textContent = "Choose both to see the reasoning."; return; }
      const good = p === okP && e === okE;
      out.innerHTML = good ? "<strong>Yes.</strong> " + msgOk : `Not the best pair. Property should be <strong>${okP}</strong>; extent should be <strong>${okE}</strong>. ` + msgOk;
    };
    document.getElementById(pId).addEventListener("change", upd); document.getElementById(eId).addEventListener("change", upd);
  }
  job("j1p", "j1e", "j1out", "Area and distance", "A few km — inside one UTM zone", "Inside one zone, close to its central meridian, UTM distances are short by only 0.04 % — irrelevant for sending a crew. So: a local projected CRS (UTM 43N) and flat-map measurement.");
  job("j2p", "j2e", "j2out", "Area (so countries compare fairly)", "The whole world", "Readers compare sizes by eye, so area must be honest: an equal-area projection. Web Mercator would show Canada and Russia enormously enlarged next to India. Nobody measures distance on a newsletter map, so distance error is acceptable.");

  // --- area tiles ---
  const A = E6.wards[0], ring = wardRing(A);
  const aEll = ellipsoidRectArea(A.lat[0], A.lat[1], A.lon[0], A.lon[1]);
  const aUtm = shoelace(ring.map(([lo, la]) => { const u = utm(la, lo, 43); return [u.E, u.N]; }));
  const aWm = shoelace(ring.map(([lo, la]) => { const w = webMerc(la, lo); return [w.X, w.Y]; }));
  document.getElementById("areaTiles").innerHTML = `
    <div class="tile good"><div class="k">On the Earth model (geodesic)</div><div class="v">${fmtN(aEll)} m²</div><div class="s">the surface area of the ward on the WGS 84 ellipsoid</div></div>
    <div class="tile okish"><div class="k">UTM zone 43N, flat</div><div class="v">${fmtN(aUtm)} m²</div><div class="s">${((aUtm / aEll - 1) * 100).toFixed(2)} % — tiny, and explainable (0.9996²)</div></div>
    <div class="tile bad"><div class="k">Web Mercator, flat</div><div class="v">${fmtN(aWm)} m²</div><div class="s">+${((aWm / aEll - 1) * 100).toFixed(1)} % — the map is stretched here</div></div>
    <div class="tile na"><div class="k">“Square degrees”</div><div class="v">0.0001</div><div class="s">not an area at all — degrees are angles</div></div>`;
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
