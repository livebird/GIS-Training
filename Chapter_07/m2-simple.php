<?php $page = ['title' => '7.2 Simple exchange formats: CSV and GeoJSON', 'chapter' => 7, 'module' => '7.2']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 7.2 · Format rules (RFC 7946) plus ArcGIS / QGIS behaviour</div>
    <h1>The two simplest boxes have the most silent mistakes</h1>
    <p class="lead">A <strong>coordinate CSV</strong> is a plain table with two coordinate columns. <strong>GeoJSON</strong> is JSON with shapes in it. Every developer can read both — which is exactly why people load them without looking. This module teaches you to read them <em>by eye first</em>.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>List what a coordinate CSV cannot say about itself, and see a leading zero and a timestamp get damaged on import.</li>
      <li>Recognise GeoJSON’s three object kinds and its one non-negotiable rule: coordinates are <strong>WGS 84, longitude first</strong>.</li>
      <li>Check a small GeoJSON file with five questions before loading it.</li></ul></div>
  </div>

  <h2><span class="mod">7.2.1</span>A coordinate CSV carries nothing geographic inside it</h2>
  <p>Here is Table F7 as the text file the lab uses. Read it before any software does. Ten header names; the codes in the second column are in quotes on purpose; the TR-0301 row has <em>three empty fields in a row</em>.</p>
  <div class="copywrap"><pre id="csvPre" class="listing"></pre></div>
  <p>Nothing in that file says what <code>x</code> and <code>y</code> mean, what units they are in, which coordinate system they belong to, or whether an empty field means “null” or “no note”. <strong>All of that must travel beside the file</strong>, in a readme — and the receiver must check it in the software, because every importer <em>guesses</em>.</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Must be explicit</th><th>Why it cannot be guessed safely</th><th>Table F7’s answer (in the readme)</th></tr></thead>
    <tbody>
      <tr><td>Which columns are coordinates</td><td>Names vary: <code>x</code>, <code>lon</code>, <code>Longitude</code>, <code>easting</code>, <code>POINT_X</code>…</td><td><code>x</code>, <code>y</code></td></tr>
      <tr><td>Coordinate order and meaning</td><td>“First column” means nothing until you know which axis it is (Chapter 5)</td><td><code>x</code> across, <code>y</code> up, on the training grid</td></tr>
      <tr><td>CRS and units</td><td>A CSV has no place to write it; numbers alone cannot prove it</td><td>Local training grid, metres, <strong>no Earth reference</strong></td></tr>
      <tr><td>Delimiter</td><td>“CSV” files use commas, semicolons or tabs; a decimal comma (<code>3,5</code>) collides with a comma separator</td><td>Comma; decimal point</td></tr>
      <tr><td>Character encoding</td><td>Bytes outside ASCII mean nothing until you know the encoding</td><td>UTF-8</td></tr>
      <tr><td>Field types</td><td>Text has no types; each importer decides from the values it sees</td><td>Given in the schema dictionary</td></tr>
      <tr><td>Null representation</td><td>Empty, <code>NULL</code>, <code>NA</code>, <code>-9999</code> and <code>0</code> are all used somewhere</td><td>Empty = null for numbers/timestamps; empty text = “no note”</td></tr>
    </tbody></table></div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Load the CSV with different settings — what does the table look like afterwards?</h3>
    <p>Two switches every importer has somewhere. Flip them and watch the SL-0113 and SL-0055 rows. This is a simulation of the rules, not a real import — that is what the lab is for.</p>
    <div class="controls">
      <label><input type="checkbox" id="optQuote" checked> Types declared (quotes / <code>schema.ini</code> / <code>.csvt</code>)</label>
      <label><input type="checkbox" id="optTs"> Convert timestamp to UTC on import</label>
    </div>
    <div class="table-wrap"><table id="csvSim" class="diff"></table></div>
    <div class="result" id="csvSimNote"></div>
  </div>

  <h3>The leading-zero trap — an everyday Indian example</h3>
  <p>Ahmedabad’s STD code is <code>079</code>. Type it into a spreadsheet cell and it becomes <code>79</code>, because the software decided “digits only, so this is a number”. Table F7’s <code>legacy_code</code> has the same problem: <code>0113</code> becomes <code>113</code>, and a later match against the old register (which has <code>0113</code>) finds nothing. The defence is to <strong>declare the type instead of letting it be guessed</strong>:</p>
  <ul>
    <li><strong>ArcGIS Pro</strong> reads CSVs through its “text file workspace”, and a tool “cannot change the field properties or values of a delimited file”. Esri’s own example file has an <code>ID</code> column with <code>001, 002, 003</code>. Values in double quotes are read as text; a <code>schema.ini</code> line such as <code>Col2=legacy_code Text</code> forces the type.</li>
    <li><strong>ArcGIS Online</strong> decides types from the field names and value patterns; values that are “only numerals” become an integer unless you set the field type when adding the file. It also needs UTF-8 for non-English characters and assumes date/time values are UTC unless you choose a time zone when publishing.</li>
    <li><strong>QGIS</strong> (Data Source Manager ▸ Delimited Text) scans the whole file to detect types; you can change a detected type to Text in the preview, or ship a one-line <code>.csvt</code> file.</li>
    <li><strong>GDAL/OGR</strong> (the library under QGIS) reads every column as text unless a <code>.csvt</code> exists, and treats all CSVs as UTF-8.</li>
  </ul>
  <p>Then <em>verify</em> by opening the table and reading the value — not the type label.</p>

  <h3>The timestamp trap</h3>
  <p>SL-0055 was checked at night: <code>2026-08-30T02:10:00+05:30</code>. Three things can happen on import, and all three are fine <em>if written down</em>:</p>
  <div class="tri">
    <div class="t"><h4>Offset kept</h4><p>A “timestamp offset” field (ArcGIS Pro 3.2+ geodatabases) stores <code>2026-08-30 02:10 +05:30</code> as is.</p><span class="chip yes">no change</span></div>
    <div class="t"><h4>Converted to UTC</h4><p>Stored as <code id="utcEx"></code>. Same instant — but the <strong>calendar date moved back a day</strong>. A report grouped by date now puts the check on the 29th.</p><span class="chip q">adaptation — document it</span></div>
    <div class="t"><h4>Read as text</h4><p>The string survives untouched; the software does not know it is a time. Sorting and filtering by time will not work until you convert it deliberately.</p><span class="chip q">fine, but say so</span></div>
  </div>
  <div class="callout note"><span class="label">Another Indian trap: 07/08/2026</span><p>Is that 7 August or 8 July? ArcGIS Online’s list of recognised formats starts with <code>M/DD/YYYY</code> (American order); <code>DD/MM/YYYY</code> is recognised only as a date-only type. Indian offices write day first. Use the ISO form <code>2026-08-07</code> in exchange files and the question never arises.</p></div>

  <h2><span class="mod">7.2.2</span>GeoJSON: three object kinds and one rule</h2>
  <p><strong>GeoJSON</strong> is a JSON text format for geographic data, standardised as <strong>RFC 7946</strong>. Browsers and web mapping libraries all read it. You need to recognise three kinds of object:</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Object</th><th>What it holds</th><th>Required members (RFC 7946 §3)</th></tr></thead>
    <tbody>
      <tr><td><strong>Geometry</strong></td><td>A shape: <code>Point</code>, <code>MultiPoint</code>, <code>LineString</code>, <code>MultiLineString</code>, <code>Polygon</code>, <code>MultiPolygon</code>, <code>GeometryCollection</code></td><td><code>"type"</code> and <code>"coordinates"</code></td></tr>
      <tr><td><strong>Feature</strong></td><td>One thing: a geometry plus its properties</td><td><code>"type": "Feature"</code>, <code>"geometry"</code> (a Geometry or <code>null</code>), <code>"properties"</code> (an object or <code>null</code>); <code>"id"</code> optional</td></tr>
      <tr><td><strong>FeatureCollection</strong></td><td>A list of features</td><td><code>"type": "FeatureCollection"</code> and <code>"features"</code>, an array of Feature objects</td></tr>
    </tbody></table></div>
  <div class="callout note"><span class="label">Format rule — the position and the CRS (RFC 7946 §3.1.1 and §4)</span><p>“The first two elements are longitude and latitude, or easting and northing, precisely in that order and using decimal numbers.” And: “The coordinate reference system for all GeoJSON coordinates is a geographic coordinate reference system, using the World Geodetic System 1984 (WGS 84) datum, with longitude and latitude units of decimal degrees.” The old 2008 draft allowed a <code>"crs"</code> member; RFC 7946 <em>removed</em> it because it “has proven to have interoperability issues”. One escape hatch remains: “where all involved parties have a prior arrangement, alternative coordinate reference systems can be used”.</p></div>
  <p>Two consequences that matter for us:</p>
  <ol>
    <li>Table F7 (grid metres, no Earth location) <strong>cannot be written as standard GeoJSON at all</strong>. Two teams may agree in writing to exchange the grid in GeoJSON syntax — a “prior arrangement” — but the file must be labelled non-standard and never passed to a third party. The lab makes you write that label.</li>
    <li>Earth-referenced projected data (the UTM requests from Chapter 6) must be <strong>transformed</strong> to WGS 84 degrees before it becomes standard GeoJSON. That is <em>Project</em>, not <em>Define Projection</em>.</li>
  </ol>
  <div class="callout dev"><span class="label">Platform note — the tool will write the wrong file for you</span><p>ArcGIS Pro’s <strong>Features To JSON</strong> tool has an <em>Output to GeoJSON</em> box and a <em>Project to WGS84</em> box. Esri’s page: if the second box is left unticked (the default), “the output .geojson file will contain a crs tag … This tag is not fully supported under the GeoJSON specification.” In QGIS, the GDAL GeoJSON writer defaults to the 2008 style; only the <code>RFC7946=YES</code> layer option makes it reproject to WGS 84 and follow the ring rule. In both products “export to GeoJSON” does not automatically mean “standard GeoJSON”.</p></div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Longitude first — see what a swap does</h3>
    <p>The made-up streetlight <strong>SL-9001</strong> is at longitude 70° E, latitude 20° N (open sea, on purpose). Click to swap the two numbers. Both pairs are <em>inside the allowed ranges</em>, so a range check cannot catch the mistake — only knowing the rule can.</p>
    <div class="opbtns"><button class="btn small" id="btnRight" aria-pressed="true">[70.0, 20.0] — longitude, latitude (correct)</button><button class="btn small" id="btnSwap">[20.0, 70.0] — swapped</button></div>
    <figure class="map-fig" id="worldFig"></figure>
    <div class="status-line q" id="swapNote"></div>
  </div>

  <h2><span class="mod">7.2.3</span>Read a tiny file by hand before loading it</h2>
  <p>Below is a FeatureCollection with one point and one small square (both made up, in open sea). Click the highlighted parts of the file, or the five questions, and check each answer. This five-question pass takes a minute and finds most GeoJSON problems before any code runs.</p>
  <div class="grid-2">
    <pre class="json" id="jsonView"></pre>
    <div>
      <div class="steps" id="qsteps"></div>
      <div class="step-detail" id="qdetail">Click a question.</div>
    </div>
  </div>
  <div class="callout note"><span class="label">Format rule — polygons (RFC 7946 §3.1.6)</span><p>“A linear ring is a closed LineString with four or more positions. The first and last positions are equivalent, and they MUST contain identical values.” Exterior rings run <strong>counterclockwise</strong>, holes clockwise (“the right-hand rule”); the first ring is the outside, any others are holes. Other formats have other conventions — GDAL notes that shapefile outer rings are <em>clockwise</em>. Never assume one format’s ring rule holds in another.</p></div>
  <p class="small">Two more rules developers hit early: the set of types is closed (“Implementations MUST NOT extend the fixed set of GeoJSON types”), but extra “foreign members” such as a collection-level <code>"name"</code> are allowed. And precision: six decimal places of a degree is about 10 cm — publishing fifteen adds bytes, not accuracy. The media type is <code>application/geo+json</code>.</p>

  <div class="callout warn"><span class="label">Common mistake</span><p><em>“GeoJSON is just JSON, so I can put any coordinates in it.”</em> You can put anything in a JSON file. It stops being standard GeoJSON the moment the coordinates are not WGS 84 longitude/latitude. Expect other software to draw your projected metres as degrees — at the wrong place or nowhere.</p></div>

  <div class="quiz" data-answer="1" data-fb="Numbers in the thousands are not degrees, and RFC 7946 removed the crs member. A compliant reader will treat them as degrees and fail. Acceptable routes: ask for a transformed WGS 84 export, or accept the file under a written prior arrangement and convert it yourself. Deleting the crs member and loading as degrees is the Chapter 6 relabelling error.">
    <div class="q">A partner sends <code>sites.geojson</code> whose first position is <code>[2381.5, 6120.0]</code> and which contains <code>"crs": {"properties": {"name": "EPSG:32643"}}</code>. Which is right?</div>
    <div class="opts">
      <button class="opt">It is standard GeoJSON — the crs member says what the numbers mean.</button>
      <button class="opt">Not standard: a compliant reader will read the numbers as degrees. Ask for a transformed WGS 84 export, or treat it as a documented prior arrangement and convert it yourself.</button>
      <button class="opt">Delete the crs member so the file validates, then load it.</button>
      <button class="opt">Load it; the web map will work out the projection.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  document.getElementById("csvPre").textContent = F7.csvText();
  document.getElementById("utcEx").textContent = toUTC("2026-08-30T02:10:00+05:30");
  /* CSV import simulator */
  const sim = document.getElementById("csvSim"), note = document.getElementById("csvSimNote");
  function runSim() {
    const quoted = document.getElementById("optQuote").checked, ts = document.getElementById("optTs").checked;
    const rows = [F7.rows[0], F7.rows[5]];
    const cols = ["asset_id", "legacy_code", "asset_type_local", "condition_score", "last_inspection_at"];
    const th = cols.map(c => `<th>${c}</th>`).join("");
    const problems = [];
    const tb = rows.map(r => `<tr>` + cols.map(c => {
      let v = r[c], cls = "";
      if (c === "legacy_code" && !quoted) { v = String(parseInt(v, 10)); cls = "bad"; problems.push("leading zero lost"); }
      if (c === "last_inspection_at" && ts) { v = toUTC(v); cls = "chg"; if (r.asset_id === "SL-0055") problems.push("SL-0055’s date moved to the 29th"); }
      return `<td class="${cls} ${/^\d/.test(String(v)) ? "mono" : ""}">${esc(v)}</td>`;
    }).join("") + `</tr>`).join("");
    sim.innerHTML = `<thead><tr>${th}</tr></thead><tbody>${tb}</tbody>`;
    const u = [...new Set(problems)];
    note.innerHTML = u.length ? `<strong>What changed:</strong> ${u.join("; ")}. ${ts ? "The UTC change is an <em>adaptation</em> if the readme says so. " : ""}${!quoted ? "The others are <em>losses</em> — nothing warned you." : ""}` : "Every value survived. Now imagine you had not looked: the same table would have looked equally ‘successful’ with the zeros gone.";
  }
  ["optQuote", "optTs"].forEach(id => document.getElementById(id).addEventListener("change", runSim)); runSim();
  /* swap demo */
  const fig = document.getElementById("worldFig"), sn = document.getElementById("swapNote");
  function showSwap(swap) {
    document.getElementById("btnRight").setAttribute("aria-pressed", !swap); document.getElementById("btnSwap").setAttribute("aria-pressed", swap);
    const pts = [{ lon: 70, lat: 20, label: "[70, 20] as written", cls: "" }];
    if (swap) pts.push({ lon: 20, lat: 70, label: "[20, 70] swapped", cls: "swap" });
    renderWorld(fig, pts, swap ? "Swapped: 20° E, 70° N is near the coast of northern Norway. Both numbers are still ‘valid’." : "Correct: longitude 70° E, latitude 20° N — Arabian Sea, off India’s west coast. Schematic, not a real map.");
    sn.className = "status-line " + (swap ? "bad" : "ok");
    sn.textContent = swap ? "Range check passes (20 is a fine longitude, 70 a fine latitude). The streetlight has moved 5,800 km. Only the rule ‘longitude first’ catches it." : "Longitude 70, latitude 20 — the order the RFC requires. Chapter 5’s swapped-pair lesson, now in a real file.";
  }
  document.getElementById("btnRight").addEventListener("click", () => showSwap(false)); document.getElementById("btnSwap").addEventListener("click", () => showSwap(true)); showSwap(false);
  /* JSON viewer with five questions */
  const J = document.getElementById("jsonView");
  J.innerHTML = `{
  <span class="k">"type"</span>: <span class="s hot" data-q="4">"FeatureCollection"</span>,
  <span class="k">"features"</span>: [
    {
      <span class="k">"type"</span>: <span class="s">"Feature"</span>,
      <span class="k">"id"</span>: <span class="s">"SL-9001"</span>,
      <span class="k">"geometry"</span>: { <span class="k">"type"</span>: <span class="s">"Point"</span>, <span class="k">"coordinates"</span>: <span class="n hot" data-q="0">[70.000000, 20.000000]</span> },
      <span class="k">"properties"</span>: {
        <span class="k">"asset_id"</span>: <span class="s">"SL-9001"</span>,
        <span class="k">"legacy_code"</span>: <span class="s hot" data-q="3">"9001"</span>,
        <span class="k">"asset_type_local"</span>: <span class="s">"Street lamp"</span>,
        <span class="k">"condition_score"</span>: <span class="n hot" data-q="3">null</span>,
        <span class="k">"last_inspection_at"</span>: <span class="s hot" data-q="3">"2026-03-14T10:42:00+05:30"</span>
      }
    },
    {
      <span class="k">"type"</span>: <span class="s">"Feature"</span>,
      <span class="k">"geometry"</span>: {
        <span class="k">"type"</span>: <span class="s hot" data-q="4">"Polygon"</span>,
        <span class="k">"coordinates"</span>: [ <span class="n hot" data-q="1">[ [70.000, 20.000], [70.010, 20.000], [70.010, 20.010], [70.000, 20.010], [70.000, 20.000] ]</span> ]
      },
      <span class="k">"properties"</span>: { <span class="k">"name"</span>: <span class="s">"training square (synthetic)"</span> }
    }
  ]
  <span class="c hot" data-q="2">// no "crs" member anywhere — and none is needed</span>
}`;
  const QS = [
    { t: "Where is the point?", a: "<code>[70.000000, 20.000000]</code>: first element longitude 70° E, second latitude 20° N, WGS 84. Not “x then y in whatever CRS I like”. If you saw <code>[20.0, 70.0]</code> the file would still parse — 20° E, 70° N is near northern Norway — which is why Chapter 5 said range checks alone cannot catch a swap." },
    { t: "Is the polygon ring valid?", a: "Five positions, first equals last ✔. It runs east along the south edge, north up the east edge, west along the north edge, south back to the start — counterclockwise with east right and north up — so it satisfies the right-hand rule for an exterior ring ✔." },
    { t: "What is the CRS?", a: "Nothing is written, and nothing needs to be: WGS 84 degrees is the only standard answer. If a <code>\"crs\"</code> member <em>had</em> been present you would treat the file as suspect and ask the publisher what they meant." },
    { t: "What types do the properties have?", a: "<code>legacy_code</code> is a JSON <em>string</em> <code>\"9001\"</code> — the zero-keeping choice. <code>condition_score</code> is a real JSON <code>null</code> (GeoJSON has nulls; a shapefile does not — 7.3). <code>last_inspection_at</code> is a string because GeoJSON has no date type; the receiving tool decides how to read it." },
    { t: "How many features, of what types?", a: "Two, of <em>different</em> geometry types. That is allowed in GeoJSON. A tool that needs one geometry type per dataset — ArcGIS Pro’s JSON To Features tool, any feature class — will make you choose one and drop the other." }
  ];
  const steps = document.getElementById("qsteps"), det = document.getElementById("qdetail");
  QS.forEach((q, i) => { const d = document.createElement("div"); d.className = "step"; d.innerHTML = `<span class="n">${i + 1}</span><span>${q.t}</span>`; d.addEventListener("click", () => pick(i)); steps.appendChild(d); });
  function pick(i) { steps.querySelectorAll(".step").forEach((s, j) => s.classList.toggle("active", i === j)); J.querySelectorAll(".hot").forEach(h => h.classList.toggle("on", +h.dataset.q === i)); det.innerHTML = `<strong>${i + 1}. ${QS[i].t}</strong><p>${QS[i].a}</p>`; }
  J.querySelectorAll(".hot").forEach(h => h.addEventListener("click", () => pick(+h.dataset.q)));
  pick(0);
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
