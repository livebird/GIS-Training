<?php $page = ['title' => '6.2 Changing the label vs changing the numbers', 'chapter' => 6, 'module' => '6.2']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 6.2 · General idea, with ArcGIS / QGIS / PostGIS names</div>
    <h1>Changing the label is not the same as changing the numbers</h1>
    <p class="lead">Every GIS has two operations that both mention “projection”, and they do <em>opposite</em> things. One rewrites the <strong>label</strong> that says which coordinate system the numbers belong to. The other <strong>recalculates the numbers</strong> into a new system. Mixing them up is the single most common CRS mistake — and the software will not stop you.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Tell <strong>assigning</strong> a CRS (metadata only) from <strong>transforming</strong> data (new coordinates), and name each in ArcGIS Pro, QGIS and PostGIS.</li>
      <li>Learn the one situation where assigning is correct, and the evidence it needs.</li>
      <li>Watch three kinds of failure — loud, quiet, and very quiet — so you can recognise them later.</li></ul></div>
  </div>

  <h2><span class="mod">6.2.1</span>Two operations, opposite effects</h2>
  <div class="table-wrap"><table>
    <thead><tr><th></th><th>Assign a CRS (write a label)</th><th>Transform / project (compute new numbers)</th></tr></thead>
    <tbody>
      <tr><td><strong>What changes</strong></td><td>Only the metadata saying “these numbers are in CRS X”</td><td>The stored coordinate values themselves; output is in the new CRS</td></tr>
      <tr><td><strong>Numbers in the table</strong></td><td class="yes">Unchanged</td><td class="no">Different</td></tr>
      <tr><td><strong>When correct</strong></td><td>The CRS label is missing or wrong <em>and</em> you have evidence of what the numbers really are</td><td>You know the current CRS and want the data in another one</td></tr>
      <tr><td><strong>ArcGIS Pro</strong></td><td class="mono">Define Projection</td><td class="mono">Project (features) · Project Raster (rasters)</td></tr>
      <tr><td><strong>QGIS</strong></td><td class="mono">Assign projection · Define Shapefile projection · “Set CRS of layer(s)”</td><td class="mono">Reproject layer</td></tr>
      <tr><td><strong>PostGIS</strong></td><td class="mono">ST_SetSRID()</td><td class="mono">ST_Transform()</td></tr>
    </tbody></table></div>
  <p>The documentation is very clear. Esri’s Define Projection page: it “overwrites the coordinate system information … stored with a dataset” and “does not modify any geometry”; for real reprojection “use the Project tool instead”. QGIS warns that changing the layer CRS setting “does not alter the underlying data source in any way”. PostGIS: <code>ST_SetSRID</code> “does not transform the geometry coordinates in any way – it simply sets the meta data”.</p>
  <div class="callout dev"><span class="label">Developer view</span><p>Assignment is like changing a text file’s declared encoding from <code>latin-1</code> to <code>utf-8</code> without touching the bytes. If the bytes really were UTF-8, you fixed a wrong label. If not, every non-ASCII character is now garbage — and the file still opens. Transformation is actually transcoding the bytes. <strong>Where the comparison breaks:</strong> a mis-labelled text file looks obviously wrong on screen. A mis-labelled dataset can look perfectly fine at town scale while being 100 m out (see the “very quiet” failure below).</p></div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>One request, two operations — watch the record card</h3>
    <p>Request <strong>Q1</strong> is delivered in UTM zone 43N (EPSG:32643). You want it in WGS 84 degrees (EPSG:4326). Click each operation and compare what happens to the label, to the numbers, and to the dot on the map.</p>
    <div class="opbtns">
      <button class="btn small" id="opReset" aria-pressed="true">Original (as delivered)</button>
      <button class="btn accent small" id="opProject">Transform: Project → EPSG:4326</button>
      <button class="btn small" id="opDefine">Assign: Define Projection → EPSG:4326</button>
    </div>
    <div class="grid-2">
      <div>
        <div class="reccard" id="card">
          <div class="hdr">Record card — Q1 (Blocked drain)</div>
          <div class="row" id="rLabel"><b>CRS label</b><span class="v">EPSG:32643 — WGS 84 / UTM zone 43N</span></div>
          <div class="row" id="rX"><b>1st number</b><span class="v">500000.000 (easting, m)</span></div>
          <div class="row" id="rY"><b>2nd number</b><span class="v">2543741.163 (northing, m)</span></div>
          <div class="row" id="rDisk"><b>file on disk</b><span class="v">unchanged</span></div>
        </div>
        <div class="status-line q" id="opStatus">Choose an operation.</div>
      </div>
      <figure class="map-fig" id="opFig"></figure>
    </div>
  </div>

  <h2><span class="mod">6.2.2</span>When is assigning the right thing to do?</h2>
  <p>Only in one situation: the dataset’s CRS information is <strong>missing or wrong</strong>. And even then, the tool cannot check you. Esri’s page says Define Projection will run, with only a warning, on a dataset that already has a correct CRS — so it will happily let you overwrite a right label with a wrong one. The safety has to come from <em>your process</em>:</p>
  <ol>
    <li><strong>Find the CRS from evidence, not from the numbers.</strong> Who exported the file? From which system, with which settings? Is there a separated <code>.prj</code> or metadata file? Has the data owner confirmed in writing? Numbers of the right size are a <em>clue</em> that narrows the candidates — never proof.</li>
    <li><strong>Test the candidate before committing.</strong> Put the data on a map that already has a trusted layer of the same area. In QGIS the layer CRS setting is a harmless place to try, because it changes nothing on disk. In ArcGIS Pro, the Project tool’s <strong>Input Coordinate System</strong> parameter lets you say what the data is <em>without modifying the input</em>. Do the known features line up?</li>
    <li><strong>Only then</strong> write the label (Define Projection / Define Shapefile projection / <code>ST_SetSRID</code>) — and write in your log <em>what evidence justified it</em>.</li>
  </ol>
  <div class="callout note"><span class="label">Platform note — where ArcGIS Pro refuses</span><p>Define Projection is blocked on some inputs: feature classes in an enterprise geodatabase that already contain rows, feature classes inside a feature dataset, and feature datasets themselves. The page suggests Append or export/re-import workflows for enterprise data. Read the restrictions before telling a colleague “we’ll just define it”.</p></div>

  <h2><span class="mod">6.2.3</span>Three volumes of failure: loud, quiet, very quiet</h2>
  <p>You just saw the <strong>loud</strong> failure: UTM metres labelled as degrees give a “longitude” of 500,000°, which is impossible, so the mistake shows at once. Now the other two.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Where does the data end up?</h3>
    <div class="tabs">
      <button>Loud: metres labelled as degrees</button>
      <button>Quiet: degrees labelled as UTM metres</button>
      <button>Very quiet: wrong datum</button>
    </div>
    <div class="tabpanel">
      <p>Q1’s numbers <code>500000.000, 2543741.163</code> are relabelled “EPSG:4326”. The software now reads a longitude of 500,000° and a latitude of 2,543,741°. Longitude only goes from −180 to 180 and latitude from −90 to 90, so the point cannot be placed on the Earth at all. Depending on the software the feature is dropped, drawn at a clamped position, or causes an error.</p>
      <div class="status-line bad">You will notice this one immediately. Good — it is the easy case.</div>
    </div>
    <div class="tabpanel">
      <p>Now the other way round. Q1’s <em>correct</em> degree values <code>75.000, 23.002</code> are wrongly labelled “EPSG:32643”. The software reads: easting 75 m, northing 23.002 m. Both are perfectly valid UTM numbers — just nowhere near our town.</p>
      <ul>
        <li>Easting 75 m is 499,925 m <strong>west</strong> of the central meridian (500,000 − 75).</li>
        <li>Northing 23 m is 23 m north of the equator.</li>
        <li>At the equator one degree of longitude is about 111 km, so 499.9 km west of 75° E is about <strong>70.5° E</strong>. The point lands near (0° N, 70.5° E) — open ocean, roughly 2,600 km from the practice town.</li>
      </ul>
      <div class="status-line bad">No error. The layer “works”. It is simply in the sea. You only notice if you look at the whole map — or check the extent in Layer Properties.</div>
    </div>
    <div class="tabpanel">
      <p>The quietest of all. Suppose a legacy survey file stores Q3 as <code>23.005° N, 74.995° E</code> — but in the <strong>Kalianpur 1975</strong> datum (India’s older survey reference), and a colleague labels it “WGS 84” because “it’s lat/long anyway”. Both are degrees. Both are latitudes near 23. The point draws in the right town, the right ward — just about <strong id="kalShift">112</strong> metres from where it really is.</p>
      <figure class="map-fig" id="kalFig"></figure>
      <div class="status-line bad">At town scale that is barely a pixel. At ward-boundary scale it can put a request in the wrong ward. Module 6.3 explains the datum and how the 112 m was worked out.</div>
    </div>
  </div>

  <h3>How to recognise each failure</h3>
  <div class="table-wrap"><table>
    <thead><tr><th>Symptom</th><th>Likely cause</th><th>First check</th></tr></thead>
    <tbody>
      <tr><td>Values impossible for the labelled CRS (a “latitude” of 2.5 million)</td><td>Projected numbers labelled as geographic</td><td>Compare the size of the numbers with what the CRS allows (Chapter 5)</td></tr>
      <tr><td>Layer draws thousands of km away, often near (0, 0)</td><td>Geographic numbers labelled as projected; or wrong zone / hemisphere</td><td>Look at the extent in Layer Properties ▸ Source. Is it near the false origin?</td></tr>
      <tr><td>Right town, but shifted by tens to hundreds of metres</td><td>Wrong datum in the label, or missing / wrong transformation</td><td>Overlay a trusted layer; compare the same known feature in both</td></tr>
      <tr><td>Right town, but slightly rotated or scaled against a reference</td><td>Wrong projection parameters (zone, scale factor, false easting)</td><td>Open the full CRS definition, not just its name</td></tr>
    </tbody></table></div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Sort the situations: label, transform, or stop and investigate?</h3>
    <p>Click a situation, then click the box it belongs in.</p>
    <div class="sorter" data-items='[
      {"t":"Shapefile has no .prj; the survey office documents that it exports in UTM 43N","bin":"Assign the label","why":"documented evidence → write the label, then test it"},
      {"t":"Data is known to be in UTM 43N; you need it in WGS 84 degrees","bin":"Transform","why":"known input CRS → Project / Reproject layer / ST_Transform"},
      {"t":"CSV with X,Y around 500000 / 2544000 and no note about its source","bin":"Stop and investigate","why":"magnitude is only a clue; ask the provider"},
      {"t":"Layer draws in the sea; a colleague suggests running Define Projection until it lands in the town","bin":"Stop and investigate","why":"guessing labels is never a repair"},
      {"t":"Raster in WGS 84 degrees must be combined with UTM 43N vectors for analysis","bin":"Transform","why":"Project Raster (rasters are resampled, not just relabelled)"},
      {"t":"Web map layer already lines up with the basemap; boss says define it as Web Mercator to be safe","bin":"Stop and investigate","why":"it lines up because its current label is right; overwriting it can only break it"}
    ]'>
      <div class="items"></div>
      <div class="bins">
        <div class="bin" data-bin="Assign the label"><h5>Assign the label</h5></div>
        <div class="bin" data-bin="Transform"><h5>Transform</h5></div>
        <div class="bin" data-bin="Stop and investigate"><h5>Stop and investigate</h5></div>
      </div>
    </div>
  </div>

  <div class="callout warn"><span class="label">Common mistake</span><p><em>“The layer lines up on the map now, so Define Projection fixed it.”</em> It lined up because the label you chose happened to be right. Had the label been wrong by one datum, the layer would still “line up” to within a screen pixel and every measurement from it would be biased by 100 m. Alignment on screen is a <em>necessary</em> check, not a <em>sufficient</em> one. Module 6.4 takes this further.</p></div>

  <div class="quiz" data-answer="0" data-fb="The file has no CRS at all, so the only operation that can ever come first is assignment — and only after evidence (the exporting system’s documented CRS, a separated .prj, alignment against a trusted layer). Running Project on an unknown CRS cannot work: Esri’s environment page says a projection will not occur if either coordinate system is unknown, and if you guess an input CRS the tool will faithfully transform your wrong guess.">
    <div class="q">A CSV of asset locations arrives with columns X, Y (values like 500512.4, 2544073.3) and no CRS statement. The sender says it “came out of the same system as the requests”. What is the only operation that could ever be appropriate first?</div>
    <div class="opts">
      <button class="opt">Assign a CRS — but only after establishing it from evidence, then testing it against a trusted layer.</button>
      <button class="opt">Run Project to WGS 84 straight away; the numbers are obviously UTM.</button>
      <button class="opt">Run Define Projection to WGS 84, since the requests are wanted in degrees.</button>
      <button class="opt">Leave the CRS unknown and load it into the map; the software will work it out.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const q1 = E6.requests[0];
  const rows = { L: document.getElementById("rLabel"), X: document.getElementById("rX"), Y: document.getElementById("rY"), D: document.getElementById("rDisk") };
  const card = document.getElementById("card"), st = document.getElementById("opStatus");
  const set = (r, v, cls) => { r.querySelector(".v").textContent = v; r.className = "row " + (cls || ""); };
  function show(state) {
    ["opReset", "opProject", "opDefine"].forEach(id => document.getElementById(id).setAttribute("aria-pressed", false));
    if (state === "orig") {
      document.getElementById("opReset").setAttribute("aria-pressed", true);
      set(rows.L, "EPSG:32643 — WGS 84 / UTM zone 43N"); set(rows.X, fx(q1.E) + " (easting, m)"); set(rows.Y, fx(q1.N) + " (northing, m)"); set(rows.D, "unchanged");
      card.className = "reccard"; st.className = "status-line q"; st.textContent = "As delivered: metres on the UTM 43N grid. The dot sits on the shared A/B boundary.";
      renderE6(document.getElementById("opFig"), { crs: "utm", caption: "Q1 as delivered, on the UTM 43N grid (made-up data)." });
    } else if (state === "proj") {
      document.getElementById("opProject").setAttribute("aria-pressed", true);
      set(rows.L, "EPSG:4326 — WGS 84 (degrees)", "changed"); set(rows.X, q1.lon.toFixed(3) + " (longitude, °)", "changed"); set(rows.Y, q1.lat.toFixed(3) + " (latitude, °)", "changed"); set(rows.D, "a NEW output dataset was written", "changed");
      card.className = "reccard good"; st.className = "status-line ok"; st.textContent = "Transformed. The label changed AND the numbers were recalculated. The dot is still on the A/B boundary. (No datum change was needed: both systems are on WGS 84.)";
      renderE6(document.getElementById("opFig"), { crs: "deg", caption: "Q1 after Project: same place, now stored as degrees." });
    } else {
      document.getElementById("opDefine").setAttribute("aria-pressed", true);
      set(rows.L, "EPSG:4326 — WGS 84 (degrees)", "changed"); set(rows.X, fx(q1.E) + "  ← now read as “longitude”", "same"); set(rows.Y, fx(q1.N) + "  ← now read as “latitude”", "same"); set(rows.D, "metadata overwritten; numbers untouched", "changed");
      card.className = "reccard bad"; st.className = "status-line bad"; st.textContent = "Relabelled. The numbers did not change, so the software now thinks Q1 is at longitude 500,000° — impossible (max 180°). The dot cannot be drawn anywhere sensible. This is the LOUD failure.";
      const el = document.getElementById("opFig"); const svg = renderE6(el, { crs: "deg", ghost: ["Q1"], caption: "Q1 after Define Projection: the label says degrees, the numbers are still metres — nowhere valid to draw it." });
      txt(svg, 500, 60, "Q1: longitude 500000° ?  → off the Earth", "lbl warn", { "text-anchor": "middle" });
    }
  }
  document.getElementById("opReset").addEventListener("click", () => show("orig"));
  document.getElementById("opProject").addEventListener("click", () => show("proj"));
  document.getElementById("opDefine").addEventListener("click", () => show("def"));
  show("orig");
  // Kalianpur shift figure
  const q3 = E6.requests[2]; const s = kalianpurToWGS84(q3.lat, q3.lon);
  document.getElementById("kalShift").textContent = geodesic(q3.lat, q3.lon, s.lat, s.lon).toFixed(0);
  renderE6(document.getElementById("kalFig"), { crs: "utm", shift: { Q3: [s.lat, s.lon] }, caption: "Orange = where the mislabelled file puts Q3. Red = its true WGS 84 position after the documented Kalianpur 1975 → WGS 84 transformation (EPSG:1156). About 112 m apart." });
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
