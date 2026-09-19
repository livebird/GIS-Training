<?php $page = ['title' => '12.3 Match symbols to the data', 'chapter' => 12, 'module' => '12.3']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 12.3 · General GIS idea · with ArcGIS Pro, ArcGIS Online and QGIS notes</div>
    <h1>Names get shapes, amounts get a ramp</h1>
    <p class="lead">Every field you might show is one of three <em>kinds</em>: a <strong>name</strong> (category), an <strong>order</strong> (priority 1, 2, 3), or an <strong>amount</strong> (a count). The symbol must respect the kind — otherwise the map claims an order or a quantity the data does not have.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Choose <strong>unique symbols</strong> for categories and <strong>graduated colour or size</strong> for amounts, and state the order for ordered codes.</li>
      <li>Arrange a map so the eye lands first on the data, then the context, and only then the basemap — and see how an oversized symbol hides the very place it marks.</li>
      <li>Check every map in <strong>grey</strong>, so it still works on a black-and-white printer and for readers who cannot tell red from green.</li></ul></div>
  </div>

  <h2><span class="mod">12.3.1</span>Three kinds of field, three kinds of symbol</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Kind of field</th><th>Meaning</th><th>Examples in our data</th><th>Honest symbol</th><th>Dishonest symbol</th></tr></thead>
    <tbody>
      <tr><td><strong>Name (nominal)</strong></td><td>Values are names; no order, no arithmetic</td><td><code>category</code> (Pothole, Streetlight out…), <code>status</code>, ward code</td><td><strong>Unique symbols</strong>: a different shape or hue per value</td><td>A light-to-dark ramp — it says “Pothole &lt; Drain”</td></tr>
      <tr><td><strong>Order (ordinal)</strong></td><td>Values have an order but no meaningful distance</td><td><code>priority</code> 1, 2, 3; condition 1–5</td><td>Ordered shades or sizes, <strong>with the order written in the legend</strong> (“1 = highest”)</td><td>Unrelated hues — the order disappears</td></tr>
      <tr><td><strong>Amount (quantitative)</strong></td><td>Numbers with meaningful differences</td><td><code>open_requests</code>, <code>households</code>, a rate</td><td><strong>Graduated colour</strong> (classes of a ramp) or <strong>graduated size</strong></td><td>One random hue per value — 16 colours for 16 counts</td></tr>
    </tbody></table></div>
  <p>The QGIS introduction draws exactly this line: unique-value symbols are for when “the attributes of features are not numeric, but instead strings are used” (its example is road types), while graduated symbols are “most useful when you want to show clear differences between features with attribute values in different value ranges”. ArcGIS Pro lists the same families — <strong>Unique values</strong> for categories, <strong>Graduated colors</strong> and <strong>Graduated symbols</strong> for amounts, plus <strong>Proportional</strong> (size in exact proportion) and <strong>Unclassed colors</strong>. ArcGIS Online’s Map Viewer calls them <strong>Types (unique symbols)</strong> and <strong>Counts and Amounts (color / size)</strong>.</p>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Which symbol for which field?</h3>
    <p>For each field, choose how you would draw it. Feedback appears at once.</p>
    <div class="grid-2" id="pickGrid"></div>
  </div>

  <h3>Colour or size for an amount?</h3>
  <p>Both are honest; they suit different shapes. For <strong>wards (polygons)</strong>, colour fill is natural — that is the choropleth of 12.5. For <strong>request points</strong>, size is often better: a small dot’s colour is hard to read, and a sized dot does not colour an area the value does not describe. For <strong>roads (lines)</strong>, line width is the “size” channel.</p>
  <div class="callout dev"><span class="label">Developer view</span><p>You would never sort an enum by its string, average a status code, or compare two UUIDs with <code>&lt;</code>. Name / order / amount are the <em>types</em> of a map variable, and a colour ramp is an operation defined only on ordered types. <strong>Where it stops:</strong> a compiler refuses <code>enum &lt; enum</code>; every GIS happily draws a category with a ramp and never warns you. The type check is yours.</p></div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“Red-to-green for status — red is bad, green is done.”</em> Status is a name, not an order (is “Reopened” more or less than “In progress”?). And red/green is the pair most often confused by colour-blind readers. Use distinct hues <em>and</em> shapes.</p></div>

  <h2><span class="mod">12.3.2</span>Foreground, context, basemap — and the oversized symbol</h2>
  <p>The eye goes to whatever has the most contrast, size and colour. <strong>Visual hierarchy</strong> means arranging the map so the eye lands in order on (1) the data the decision is about, (2) the context that helps read it, (3) the basemap, which should be felt, not read. ArcGIS Pro’s help describes a basemap as “a reference map on which you overlay data from layers” — context, not the message.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Symbol size vs the place it marks</h3>
    <p>Requests G02 and G03 are two lamps on one street, <strong>11.18 m apart</strong>. Change the symbol size and the map scale, and watch when the two become one blob. The symbol is drawn at its true ground footprint (points × 0.3528 mm × scale).</p>
    <div class="controls">
      <label>Symbol <select id="pt"><option>6</option><option selected>12</option><option>24</option></select> pt</label>
      <label>Scale 1 : <select id="sc"><option>25000</option><option selected>10000</option><option>2500</option><option>2000</option></select></label>
      <label><input type="checkbox" id="hier" checked> show hierarchy (roads and outlines subdued)</label>
    </div>
    <figure class="map-fig" id="sizeFig"></figure>
    <div class="result" id="sizeOut"></div>
  </div>
  <div class="table-wrap"><table>
    <thead><tr><th>Symbol</th><th>On paper</th><th>On the ground at 1:25,000</th><th>Effect on our data</th></tr></thead>
    <tbody>
      <tr><td class="mono">6 pt</td><td>2.12 mm</td><td><strong>53 m</strong></td><td>G02/G03 (11 m apart) merge; G10 “on the road” covers the road and 26 m either side</td></tr>
      <tr><td class="mono">12 pt</td><td>4.23 mm</td><td><strong>106 m</strong></td><td>G04 (20 m from the road) and G10 (on it) look the same; the G06 edge case straddles both wards</td></tr>
      <tr><td class="mono">24 pt</td><td>8.47 mm</td><td><strong>212 m</strong></td><td>One symbol covers a fifth of a ward’s width</td></tr>
    </tbody></table></div>
  <p>The lesson is not “use tiny symbols” — a 2 pt dot is invisible from a metre away. It is that at small scales a point symbol is a <em>label of a location</em>, not a picture of it. Any “which side of the line?” question is answered from the coordinates (Chapter 10), never from the drawing.</p>

  <h2><span class="mod">12.3.3</span>Readable, distinguishable, and more than colour — the grey check</h2>
  <ol>
    <li><strong>Readable labels.</strong> Legible at the reading size; never on top of the symbol or each other. On the crew map only the request ID; on the manager’s map only the ward code. If a label cannot fit, drop it or show it only when zoomed in.</li>
    <li><strong>Distinguishable symbols.</strong> Every legend entry must be tellable from every other <em>on the map</em>. Two similar blues for “Blocked drain” and “Water leak” fail. Use shape as well as colour; keep to about six categories — ArcGIS Pro’s help notes that with many classes “it may help to group values together”.</li>
    <li><strong>More than colour for anything essential.</strong> Some readers cannot tell red from green; every reader loses colour on a black-and-white printout or a photocopy. Open vs closed, priority 1 vs 3, “no data” vs zero — each must survive as a shape, a pattern, a size, a label, or a legend sentence.</li>
  </ol>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>The grey check</h3>
    <p>Two designs of the crew map. Turn colour off and ask: can the supervisor still tell the type, the priority and open-vs-closed?</p>
    <div class="controls">
      <button class="btn small" data-design="good" aria-pressed="true">Design A: shapes + sizes + hollow/filled</button>
      <button class="btn small" data-design="bad">Design B: five colours, all circles, same size</button>
      <label><input type="checkbox" id="grey"> view in grey</label>
    </div>
    <figure class="map-fig" id="greyFig"></figure>
    <div class="result" id="greyOut"></div>
  </div>
  <div class="callout note"><span class="label">Procedure (version-specific; not run by the author)</span><p><strong>ArcGIS Pro:</strong> with a map or layout active, on the <strong>View</strong> tab, <strong>Accessibility</strong> group, open <strong>Color Vision Simulator</strong> and choose Protanopia, Deuteranopia, Tritanopia or <strong>Achromatopsia</strong> (the grey view). It is a checking view only — the help says it is not included when you export or share. <strong>QGIS 3.44:</strong> <strong>View ▸ Preview Mode</strong> ▸ Simulate Achromatopsia (Grayscale), or one of the Protanopia / Deuteranopia / Tritanopia modes. <strong>Web maps</strong> (OpenLayers, Mapbox, the ArcGIS SDK) have no built-in simulator: export an image and grey it, or use browser developer tools.</p></div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“The legend explains the colours, so colour alone is fine.”</em> A reader who cannot separate the two reds in the legend cannot separate them on the map either. The test is on the map, in grey, by someone who has not seen the colour version.</p></div>

  <div class="quiz" data-answer="2" data-fb="asset_type is a name → unique symbols with distinct shapes. condition 1–5 is an order → one hue in five ordered shades (or five sizes) with “5 = best” in the legend. In grey, shapes still differ and a single-hue ramp still reads as an ordered sequence; five unrelated hues do not.">
    <div class="q">An assets layer has <code>asset_type</code> (Streetlight, Drain, Tree) and <code>condition</code> (1–5, 5 = best). A colleague draws <code>asset_type</code> with a light-to-dark blue ramp and <code>condition</code> with five unrelated hues. What should change?</div>
    <div class="opts"><button class="opt">Nothing — both use five colours, which is fine.</button><button class="opt">Swap them: hues for asset_type, and keep unrelated hues for condition too.</button><button class="opt">Shapes (unique symbols) for asset_type; one hue in ordered shades for condition, with “5 = best” in the legend.</button><button class="opt">Use the same ramp for both so the map looks consistent.</button></div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  // --- picker ---
  const fields = [
    { f: "category (Pothole, Streetlight out, …)", kind: "name", ok: "Unique symbols (a shape/hue per value)", why: "names have no order — a ramp would say Pothole < Drain." },
    { f: "priority (1, 2, 3)", kind: "order", ok: "Ordered sizes or shades, with “1 = highest” in the legend", why: "an order, but no arithmetic — “3× more urgent” is not a thing." },
    { f: "open_requests per ward", kind: "amount", ok: "Graduated colour (classes of one ramp)", why: "an amount on a polygon — the choropleth of 12.5." },
    { f: "ward code (W01 … W16)", kind: "name", ok: "Unique symbols — or just a label", why: "a code is a name; sixteen fills would be noise; a label is enough." },
    { f: "households per ward", kind: "amount", ok: "Graduated colour (classes of one ramp)", why: "an amount; usually a denominator (12.5) rather than a map of its own." },
    { f: "status (OPEN / CLOSED)", kind: "name", ok: "Unique symbols (a shape/hue per value)", why: "two names; filled vs hollow survives grey." }
  ];
  const opts = ["Unique symbols (a shape/hue per value)", "Ordered sizes or shades, with “1 = highest” in the legend", "Graduated colour (classes of one ramp)", "Unique symbols — or just a label"];
  document.getElementById("pickGrid").innerHTML = fields.map((x, i) => `<div class="card" style="margin:0"><h4 style="margin-top:0"><code>${x.f}</code></h4><select data-i="${i}"><option value="">choose…</option>${opts.map(o => `<option>${o}</option>`).join("")}</select><div class="result" id="pk${i}">…</div></div>`).join("");
  document.querySelectorAll("#pickGrid select").forEach(s => s.addEventListener("change", () => { const x = fields[+s.dataset.i], out = document.getElementById("pk" + s.dataset.i); if (!s.value) return; const good = s.value === x.ok || (x.kind === "name" && s.value.startsWith("Unique")); out.innerHTML = (good ? "<strong>Yes.</strong> " : `<strong>Not this.</strong> Use: ${x.ok}. `) + `This field is a <strong>${x.kind}</strong> — ${x.why}`; }));

  // --- size demo ---
  const pt = document.getElementById("pt"), sc = document.getElementById("sc"), hier = document.getElementById("hier");
  function size() {
    const p = +pt.value, s = +sc.value, fp = footprintM(p, s);
    renderCrew(document.getElementById("sizeFig"), { pt: p, scale: s, showClosed: "hide", labels: true, wardFill: !hier.checked, window: [1000, 2200, 2000, 3000], caption: `W06 only. ${hier.checked ? "Data in front, context subdued." : "No hierarchy: ward fill competes with the points."}` });
    document.getElementById("sizeOut").innerHTML = `A ${p} pt symbol at 1:${fmtN(s)} covers <strong>${fmtN(fp, 1)} m</strong> on the ground. G02 and G03 are 11.18 m apart → ${fp > 11.18 ? "<strong>they overlap</strong> — the supervisor sees one job where there are two." : "<strong>they separate</strong> (just)."} G04 is 20 m from the road → ${fp / 2 > 20 ? "its symbol touches the road; “is it on the road?” cannot be read from the picture." : "it is visibly off the road."}`;
  }
  [pt, sc, hier].forEach(e => e.addEventListener("change", size)); size();

  // --- grey check ---
  let design = "good";
  const grey = document.getElementById("grey");
  function greyDraw() {
    document.querySelectorAll("[data-design]").forEach(b => b.setAttribute("aria-pressed", b.dataset.design === design));
    const good = design === "good";
    renderCrew(document.getElementById("greyFig"), { pt: 10, scale: 12000, shapes: good, sizeByPriority: good, showClosed: "hollow", labels: false, gray: grey.checked, caption: good ? "Design A: shape = type, size = priority, hollow = closed." : "Design B: colour = type; all circles; same size; closed shown by a paler colour." });
    if (!good) { const svg = document.querySelector("#greyFig svg"); svg.querySelectorAll(".reqm").forEach((m, i) => { const q = REQ[i]; if (q.status === "CLOSED") { m.setAttribute("fill", CAT_COLOR[q.cat]); m.setAttribute("opacity", ".45"); } }); }
    document.getElementById("greyOut").innerHTML = grey.checked
      ? (good ? "<span class=\"verdict ok\">Passes.</span> Shapes, sizes and hollow-vs-filled do not need colour. The supervisor can still read type, priority and status." : "<span class=\"verdict no\">Fails.</span> Five hues became three or four similar greys; Pothole and Water leak are now the same; closed requests are just slightly paler. The legend cannot rescue this.")
      : "Now tick <em>view in grey</em>. This is what the office printer — and a colour-blind reader — will see.";
  }
  document.querySelectorAll("[data-design]").forEach(b => b.addEventListener("click", () => { design = b.dataset.design; greyDraw(); }));
  grey.addEventListener("change", greyDraw); greyDraw();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
