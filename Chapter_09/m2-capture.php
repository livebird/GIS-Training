<?php $page = ['title' => '9.2 Compare capture methods', 'chapter' => 9, 'module' => '9.2']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 9.2 · General idea, with ArcGIS / QGIS names where they matter</div>
    <h1>Five ways to put a dot on the map — and five kinds of doubt</h1>
    <p class="lead">In the table, every location looks the same: two numbers. But those numbers were made by very different work — someone clicking on a screen, a file from another system, a satellite receiver, an address lookup, or a scanned paper map. What differs is the <strong>doubt</strong> each one carries and the <strong>history</strong> (provenance) it must keep.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Tell apart digitising, coordinate import, GNSS observation, address geocoding, and georeferencing a scan.</li>
      <li>Explain why “GPS” is just one satellite system, and why accuracy belongs to <em>one device in one place at one moment</em>, not to “phones”.</li>
      <li>See that an address match, a roof location and a surveyed gate are three different facts, not three attempts at one fact.</li></ul></div>
  </div>

  <h2><span class="mod">9.2.1</span>Five workflows</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Method</th><th>What the person actually does</th><th>What the numbers mean</th><th>What can go wrong</th><th>History it must carry</th></tr></thead>
    <tbody>
      <tr><td><strong>Manual digitising</strong> (tracing on screen)</td><td>Clicks corners over a background — an aerial photo, a scanned plan, an existing layer. The QGIS guide: you can “trace the features off the raster layer into your vector layer”.</td><td>“Where the operator <em>judged</em> the thing to be on the background”</td><td>Background badly placed (“the newly captured data will be inaccurate!”); wrong zoom; snapping to the wrong thing (9.4)</td><td>Background name and date, zoom used, operator, snapping settings</td></tr>
      <tr><td><strong>Coordinate import</strong></td><td>Loads a table that already has X, Y columns (Chapter 7’s CSV)</td><td>Whatever the <em>upstream</em> system meant — you observed nothing</td><td>Wrong coordinate system, units or order (Chapter 5); decimals cut off; columns swapped</td><td>Upstream system, its CRS/units/order statement, export date — and <em>its</em> capture method</td></tr>
      <tr><td><strong>GNSS observation</strong> (“GPS”)</td><td>Stands at the thing with a receiver (phone or external) and records the position</td><td>The receiver’s <em>estimate</em> of where its antenna was, with its own accuracy estimate</td><td>Poor sky view (narrow streets, trees), no correction service, receiver datum differs from the map’s; the person not standing <em>at</em> the thing</td><td>Receiver, fix type, reported accuracy, satellites, DOP values, correction, time, antenna height</td></tr>
      <tr><td><strong>Address geocoding</strong></td><td>Types “12 Temple Lane”; a <em>locator</em> matches it against reference data and returns a point</td><td>“Where the reference data says that address is” — a rooftop point, a spot <em>estimated along the street</em> from house-number ranges, a PIN-code centre, or a town centre</td><td>Misspelt or vague addresses; reference data older than the street; a match at a coarser level than you assumed</td><td>Locator and its data date, match status (M/T/U), score 0–100, address type</td></tr>
      <tr><td><strong>Georeferencing a scan</strong></td><td>Places an image that has no coordinates, using control points (9.3)</td><td>“Where the image pixels land after the fit” — anything traced from it inherits the fit’s error and the paper’s distortions</td><td>Too few or badly spread control points; a stretched original; trusting the residual</td><td>Control-point list with residuals, transformation type, source document and date, the independent check</td></tr>
    </tbody></table></div>
  <div class="callout note"><span class="label">A gap in Chapter 8’s design</span><p>Chapter 8’s <code>location_method</code> list was {SURVEY, GNSS, DIGITISED, APPROX}. A geocoded point is none of those. Adding <code>GEOCODED</code> is a <em>schema change</em> — it has an owner and an impact checklist (Chapter 8, 8.7.3). An editor does not add it quietly in the middle of a correction. Module 9.7 comes back to this.</p></div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Which method made this point?</h3>
    <p>Click a clue, then the method it points to.</p>
    <div class="sorter" data-items='[
      {"t":"Row has fix type, 8 satellites, horizontal accuracy 0.4 m","bin":"GNSS observation","why":"only a receiver reports these"},
      {"t":"Row has Score 92, Addr_type = StreetAddress","bin":"Address geocoding","why":"match score and address type come from a locator"},
      {"t":"Coordinates end in .000 and match an old plan’s grid crosses","bin":"Georeferencing a scan","why":"traced from a placed image"},
      {"t":"Row came from the request tracker export with columns E_m, N_m","bin":"Coordinate import","why":"you did not observe anything; the tracker did"},
      {"t":"Log says: aerial photo 2024, zoom 1:500, operator crew02","bin":"Manual digitising","why":"backdrop, zoom and operator are the digitising history"},
      {"t":"Point sits exactly on the ward boundary line","bin":"Manual digitising","why":"a boundary-exact coordinate hints that snapping was on while tracing (9.4)"}
    ]'>
      <div class="items"></div>
      <div class="bins">
        <div class="bin" data-bin="Manual digitising"><h5>Manual digitising</h5></div>
        <div class="bin" data-bin="Coordinate import"><h5>Coordinate import</h5></div>
        <div class="bin" data-bin="GNSS observation"><h5>GNSS observation</h5></div>
        <div class="bin" data-bin="Address geocoding"><h5>Address geocoding</h5></div>
        <div class="bin" data-bin="Georeferencing a scan"><h5>Georeferencing a scan</h5></div>
      </div>
    </div>
  </div>

  <h2><span class="mod">9.2.2</span>GPS is one GNSS — and accuracy belongs to a device in a place</h2>
  <div class="grid-2">
    <div class="card">
      <h4 style="margin-top:0">The word</h4>
      <p><strong>GNSS</strong> (Global Navigation Satellite System) is the general word for <em>any</em> satellite constellation that gives position and time. <strong>GPS</strong> is the American one and the most used. Others: <strong>NavIC</strong> (India), <strong>Galileo</strong> (European Union), <strong>BeiDou</strong> (China), <strong>GLONASS</strong> (Russia), <strong>QZSS</strong> (Japan). A modern phone uses several together. For a municipality in India, a receiver that also tracks NavIC is normal — but what matters for your data is not <em>which</em> satellites, it is what the receiver <em>reported about its own answer</em>.</p>
    </div>
    <div class="card">
      <h4 style="margin-top:0">The rule</h4>
      <p><strong>Never give a fixed accuracy to “phones” or “receivers”.</strong> Esri’s Field Maps page lets a map author set a <em>required accuracy</em> and can store, with each captured point, the receiver name, fix type, horizontal and vertical accuracy, satellite count, DOP values, correction age and more — “for validating the quality of the GNSS positions”. It states no accuracy number for any phone, and neither should you. Accuracy is a property of <em>this receiver, in this sky view, with this correction, at this moment</em>.</p>
    </div>
  </div>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Two rows, both say “GNSS”</h3>
    <div class="grid-2">
      <div class="reccard"><div class="hdr">Asset row 1 — drain, method GNSS</div>
        <div class="row same"><b>horizontal acc.</b><span class="v">0.4 m</span></div><div class="row same"><b>fix type</b><span class="v">corrected (differential)</span></div><div class="row same"><b>satellites</b><span class="v">14</span></div><div class="row same"><b>sky</b><span class="v">open ground</span></div></div>
      <div class="reccard"><div class="hdr">Asset row 2 — drain, method GNSS</div>
        <div class="row changed"><b>horizontal acc.</b><span class="v">12 m</span></div><div class="row changed"><b>fix type</b><span class="v">uncorrected</span></div><div class="row changed"><b>satellites</b><span class="v">5</span></div><div class="row changed"><b>sky</b><span class="v">narrow lane, trees</span></div></div>
    </div>
    <div class="controls" style="margin-top:.8rem"><label>Decision <select id="gnssUse"><option value="0">Council overview map (which ward?)</option><option value="1">Excavation (where do we dig?)</option></select></label></div>
    <div class="result" id="gnssOut"></div>
  </div>
  <div class="callout warn"><span class="label">One more trap — the receiver’s datum</span><p>A receiver reports latitude/longitude on <em>its</em> reference. If that differs from the map’s, the position “must be transformed to match the map’s coordinate system” (Field Maps uses a <em>location profile</em> with a datum transformation for this). Chapter 6’s question arrives through the field app. A metre-level offset between “the same” drain from two devices is something to investigate, not average away.</p></div>

  <h2><span class="mod">9.2.3</span>One address, three different dots</h2>
  <p>The same building can be “located” three ways. None is wrong. The mistake is to put them in one layer as if they were the same fact — or to “correct” one to another.</p>
  <div class="grid-2">
    <figure class="map-fig" id="addrFig"></figure>
    <div>
      <div class="table-wrap"><table>
        <thead><tr><th></th><th>How obtained</th><th>What the dot stands for</th></tr></thead>
        <tbody>
          <tr><td><strong>A</strong> address match</td><td>“12 Temple Lane” geocoded; the locator returned a <em>StreetAddress</em> match — the house number is <em>estimated along the street</em> from a number range, offset to the correct side</td><td>A point near the road, computed — not the building</td></tr>
          <tr><td><strong>B</strong> roof location</td><td>An operator clicked the roof centre on an aerial image</td><td>The roof as seen in the image, with the image’s own placement error</td></tr>
          <tr><td><strong>C</strong> surveyed gate</td><td>A crew stood at the gate with a corrected receiver (accuracy 0.3 m)</td><td>The gate — the place a crew must reach</td></tr>
        </tbody></table></div>
      <p class="small">ArcGIS geocoding results say which level they are: <code>Status</code> (M matched / U unmatched / T tied), <code>Score</code> 0–100, and <code>Addr_type</code> — <em>PointAddress</em> (a real building point), <em>StreetAddress</em> (estimated along the street), <em>StreetName</em>, <em>Postal</em>, <em>Locality</em>… Keep these columns; do not export “location only”. Other platforms’ geocoders return similar fields under other names — check each one.</p>
    </div>
  </div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“A matched address is a located building.”</em> A complaint geocoded to a PIN-code centre (<code>Addr_type = Postal</code>) is treated as a point on a street and put in the wrong ward. Read <code>Addr_type</code> and <code>Score</code> before using a point for anything finer than its match level.</p></div>

  <div class="quiz" data-answer="2" data-fb="The method is known (GNSS) but the quality of each observation is not. Reported accuracy, fix type and correction status should have been stored per row. (a) gives a device class a fixed accuracy; (b) compares methods as if accuracy belonged to the method; (d) confuses “a GNSS” with “a datum” and ignores the receiver-to-map transformation.">
    <div class="q">A complaint table has <code>location_method = GNSS</code> on every row, captured with crews’ own phones, and no other GNSS columns. Which statement is correct?</div>
    <div class="opts">
      <button class="opt">The points are accurate to a few metres because phones are.</button>
      <button class="opt">The points are less accurate than geocoded points because geocoding uses reference data.</button>
      <button class="opt">The method is known but the quality of each point is not; accuracy, fix type and correction should have been stored per row.</button>
      <button class="opt">Because GPS is a GNSS, the points are on WGS 84 and need no transformation.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const out = document.getElementById("gnssOut");
  const g = v => { out.innerHTML = v === "1" ? "<strong>Different decisions.</strong> Row 1 (0.4 m, corrected) may be good enough to dig by; row 2 (12 m, no correction, under trees) is not — a 12 m circle is wider than the road. The question that separates them is <strong>positional accuracy</strong>, and you could only answer it because the accuracy was stored with each point." : "<strong>Same decision.</strong> For “which ward is this drain in?” both rows are fine — unless a drain sits within about 12 m of a ward boundary, in which case row 2 cannot say which side it is on."; };
  document.getElementById("gnssUse").addEventListener("change", e => g(e.target.value)); g("0");

  // address figure: a street segment, a building, three dots
  const el = document.getElementById("addrFig"); const W = 900, H = 520; const sx = x => (x - 260) * 10 + 60, sy = y => H - 60 - (y - 380) * 10;
  let s = `<svg viewBox="0 0 ${W} ${H}" role="img" aria-label="An address located three ways">`;
  s += `<line class="road" x1="${sx(262)}" y1="${sy(400)}" x2="${sx(345)}" y2="${sy(400)}"/><text class="road-label" x="${sx(263)}" y="${sy(400) - 20}">Temple Lane (road centre line)</text>`;
  s += `<rect x="${sx(300)}" y="${sy(412)}" width="${24 * 10}" height="${14 * 10}" fill="#efe7d7" stroke="#8b93a7" stroke-width="3"/><text class="note-text" x="${sx(303)}" y="${sy(405)}">building no. 12</text>`;
  const pts = [{ id: "A", x: 296, y: 402, c: "var(--accent)", t: "A address match (296, 402)" }, { id: "B", x: 312, y: 405, c: "var(--ward-line)", t: "B roof centre (312, 405)" }, { id: "C", x: 306.2, y: 398.7, c: "var(--ok)", t: "C surveyed gate (306.2, 398.7)" }];
  pts.forEach((p, i) => { s += `<circle cx="${sx(p.x)}" cy="${sy(p.y)}" r="14" fill="${p.c}" stroke="#fff" stroke-width="4"/><text class="asset-label" x="${sx(262)}" y="${40 + i * 34}" fill="${p.c}" style="fill:${p.c}">${p.t}</text>`; });
  s += `</svg>`;
  el.innerHTML = s + `<figcaption>Made-up example. Three facts about one address — not three guesses at one fact.</figcaption>`;
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
