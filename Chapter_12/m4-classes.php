<?php $page = ['title' => '12.4 Classification is a choice', 'chapter' => 12, 'module' => '12.4']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 12.4 · General GIS idea · methods as named in ArcGIS Pro, ArcGIS Online and QGIS</div>
    <h1>Same numbers, different “low / medium / high”</h1>
    <p class="lead">A coloured ward map puts each ward into a <strong>class</strong> and gives every ward in a class the same colour. Where the class limits (<strong>breaks</strong>) fall is decided by a <strong>classification method</strong>. Change the method and the picture changes — with no change to any number.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Classify the same 15 values by <strong>equal interval</strong>, <strong>quantile</strong> and <strong>natural breaks</strong>, and say what each method is trying to do.</li>
      <li>See six wards change colour when only the method changes — and learn why the legend must print the numeric limits and units.</li>
      <li>Compare two months and see why recalculating classes for each map breaks the comparison.</li></ul></div>
  </div>

  <h2><span class="mod">12.4.1</span>Three methods, one small table</h2>
  <p>The fifteen open-request counts of District G-12, sorted (W16 has no data and stays <em>out</em> of the classification):</p>
  <p class="mono" style="font-size:1.05rem">0, 2, 3, 4, 5, 6, 7, 13, 14, 16, 17, 31, 35, 40, 45</p>
  <div class="grid-3">
    <div class="card"><h4 style="margin-top:0">Equal interval</h4><p><em>Goal: classes of equal width.</em> Range 0 to 45, three classes → width 15 → breaks at 15 and 30. Esri’s help: best for “familiar data ranges, such as percentages and temperature”.</p></div>
    <div class="card"><h4 style="margin-top:0">Quantile</h4><p><em>Goal: the same number of wards in each class.</em> Fifteen values, three classes → five per class. Esri’s own warning: “Similar features can be placed in adjacent classes, or features with widely different values can be put in the same class.”</p></div>
    <div class="card"><h4 style="margin-top:0">Natural breaks (Jenks)</h4><p><em>Goal: groups that are similar inside and different from each other.</em> It searches for the split with the smallest total spread inside the classes. QGIS calls it “Natural Breaks (Jenks)”; Esri says it is “not suitable for comparing multiple maps built from different underlying information”.</p></div>
  </div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Classification explorer</h3>
    <p>Pick a method and a number of classes. The map, the legend and the membership table update together. The numbers never change — only the grouping.</p>
    <div class="controls">
      <label>Method <select id="method"><option value="ei">Equal interval</option><option value="q">Quantile</option><option value="nb" selected>Natural breaks (Jenks)</option></select></label>
      <label>Classes <select id="k"><option>2</option><option selected>3</option><option>4</option><option>5</option></select></label>
      <label><input type="checkbox" id="showVals" checked> show values on the map</label>
    </div>
    <div class="pair">
      <figure class="map-fig" id="clsFig"></figure>
      <div>
        <h4>Legend (as it should be printed)</h4>
        <div id="clsLegend"></div>
        <div class="result" id="clsOut"></div>
      </div>
    </div>
    <div class="table-wrap"><table id="memTable"><thead><tr><th>Ward</th><th>Value</th><th>Equal interval</th><th>Quantile</th><th>Natural breaks</th></tr></thead><tbody></tbody></table></div>
    <p class="small">Read the rows for W06–W11: with three classes, six wards change class depending on the method. For a value exactly on a break, the convention here is “a class includes its upper limit”; no value in this table falls on 15 or 30, so it does not matter here — when it does, write the convention on the map.</p>
  </div>

  <div class="callout dev"><span class="label">Developer view</span><p>Equal interval is <code>FLOOR(value / 15)</code>; quantile is <code>NTILE(3) OVER (ORDER BY value)</code>; natural breaks is a one-dimensional clustering on sorted data. Every developer knows a histogram’s shape depends on the bin rule. <strong>Where it stops:</strong> a histogram usually prints its bin edges; a coloured map often does not — and then readers compare <em>colours</em> across maps whose bins differ.</p></div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“Natural breaks is the smart one, so use it by default.”</em> Its breaks are tuned to <em>this</em> table. Next month’s table gets different breaks, and the two maps’ colours cannot be compared (12.4.3). No method is best everywhere — the blueprint forbids saying so.</p></div>

  <h2><span class="mod">12.4.2</span>Print the limits and the units</h2>
  <p>Draw the three maps above with a “Low / Medium / High” legend and you have three different stories from 238 requests: <em>“the south is the problem”</em> (equal interval), <em>“a third of the district is bad”</em> (quantile), <em>“three tiers”</em> (natural breaks). None is a lie — but a reader who sees one map with a wordy legend cannot know which story they are being told. So the final map <strong>must</strong>:</p>
  <ul>
    <li>Show the <strong>numeric interval</strong> for each class — <code>0 – 7</code>, <code>13 – 17</code>, <code>31 – 45</code> — never only words. Where the data leave gaps between classes, show the real value ranges so the reader sees that no ward has 8–12 requests (ArcGIS Pro’s “Use feature values in labels” does exactly this).</li>
    <li>State the <strong>unit and definition</strong>: “open requests per ward, count at 2026-09-15, Policy W-1”.</li>
    <li>Name the <strong>method and class count</strong> in the notes: “natural breaks, 3 classes”.</li>
    <li>State the boundary convention if any value sits exactly on a break.</li>
  </ul>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Which class is W11 in?</h3>
    <p>W11 has 17 open requests. Predict its class under each method (three classes), then reveal.</p>
    <div class="controls">
      <label>Equal interval <select class="w11"><option value="">?</option><option>1 (pale)</option><option>2 (mid)</option><option>3 (dark)</option></select></label>
      <label>Quantile <select class="w11"><option value="">?</option><option>1 (pale)</option><option>2 (mid)</option><option>3 (dark)</option></select></label>
      <label>Natural breaks <select class="w11"><option value="">?</option><option>1 (pale)</option><option>2 (mid)</option><option>3 (dark)</option></select></label>
    </div>
    <div class="result" id="w11Out">Choose all three.</div>
  </div>
  <div class="callout note"><span class="label">Platform note</span><p>“Print the limits” is a general rule; how the text is produced is product-specific. ArcGIS Pro formats class labels in the Symbology pane; ArcGIS Online writes the ranges in the legend when “Classify data” is on; QGIS has a legend-format setting on the graduated renderer. In a web library (OpenLayers, Mapbox) the legend is <em>yours to write</em> — the library draws features, not legends.</p></div>

  <h2><span class="mod">12.4.3</span>Comparing months: fix the classes</h2>
  <p>One map is read alone. A <em>pair</em> of maps is read by comparing colours across them — which is only valid if the same colour means the same interval on both. Suppose next month extra crews were sent to W12 and W15: W12 falls from 31 to <strong>12</strong>, W15 from 45 to <strong>20</strong>; W13 and W14 edge down to 33 and 38; the other eleven wards are unchanged.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>August vs September — recalculated, or fixed?</h3>
    <div class="controls">
      <button class="btn small" data-cmp="recalc" aria-pressed="true">Quantile, recalculated each month (the default)</button>
      <button class="btn small" data-cmp="fixed">Fixed classes 0–7 / 8–17 / 18–45 on both</button>
    </div>
    <div class="pair">
      <div><h4>August</h4><figure class="map-fig compact" id="augFig"></figure></div>
      <div><h4>September</h4><figure class="map-fig compact" id="sepFig"></figure></div>
    </div>
    <div class="result" id="cmpOut"></div>
  </div>
  <p>The rule: <strong>for a comparison over time or between areas, fix the classes once, state them, and use them on every map in the set.</strong> Any <em>data-derived</em> method (quantile, natural breaks, standard deviation) re-derives itself when the data change. Manual classes — or equal interval on a fixed, stated range — are the comparison-safe choices. The same applies between areas: a district map and a city map each classified by quantile both show “one third dark” and mean different numbers.</p>
  <div class="callout dev"><span class="label">Developer view</span><p>Two line charts with independently auto-scaled y-axes both fill the frame; the reader sees two similar curves and misses that one is ten times the other. Fixing the axis range across charts is the same discipline as fixing class breaks. <strong>Where it stops:</strong> an auto-scaled axis usually prints its range, so a careful reader can recover the truth; an auto-classified map with a “Low/High” legend gives nothing to recover it from.</p></div>

  <div class="quiz" data-answer="1" data-fb="Quantile re-sorts the new values: the top five are now W10 (16), W11 (17), W15 (20), W13 (33), W14 (38). W10 and W11 turn dark although nothing changed in them, and the biggest improvement (W12, 31→12) is what pushed them there. Fix the classes and state them on both legends.">
    <div class="q">The monthly report shows two quantile maps of open requests with identical “Low / Medium / High” legends. Between the months W12 fell from 31 to 12 and W15 from 45 to 20; nothing else changed. What does the September map show for W10 (16) and W11 (17)?</div>
    <div class="opts"><button class="opt">Unchanged — their values did not change.</button><button class="opt">They turn “High” although unchanged, because two wards fell past them.</button><button class="opt">They turn “Low”, because the district improved overall.</button><button class="opt">They disappear from the map.</button></div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const vals = G12.wards.filter(w => w.open != null).map(w => w.open);
  const M = { ei: ["Equal interval", equalInterval], q: ["Quantile", quantile], nb: ["Natural breaks (Jenks)", jenks] };
  const method = document.getElementById("method"), kSel = document.getElementById("k"), showVals = document.getElementById("showVals");
  function draw() {
    const k = +kSel.value, [name, fn] = M[method.value], br = fn(vals, k), ramp = RAMP[k];
    renderG12(document.getElementById("clsFig"), { style: w => w.open == null ? { hatch: true, label: showVals.checked ? "n/a" : null } : (c => ({ fill: ramp[c], dark: isDark(ramp[c]), label: showVals.checked ? w.open : null }))(classOf(w.open, br)), title: `Open requests per ward · ${name}, ${k} classes` });
    const labels = classLabels(vals, br, "open requests"), counts = br.map((b, i) => vals.filter(v => classOf(v, br) === i).length);
    document.getElementById("clsLegend").innerHTML = `<p class="small" style="margin:.2rem 0"><strong>Open service requests per ward</strong> · count at 2026-09-15 · Policy W-1 · ${name}, ${k} classes</p>` + legendHtml(ramp, labels.map((l, i) => `${l} <span class="small">(${counts[i]} ward${counts[i] === 1 ? "" : "s"})</span>`), "No data — export not received (W16)");
    document.getElementById("clsOut").innerHTML = `Breaks (upper limits): <span class="mono">${br.map(b => Number.isInteger(b) ? b : b.toFixed(2)).join(" · ")}</span>. Wards per class: <strong>${counts.join(" / ")}</strong>. Total spread inside the classes (what natural breaks minimises): <span class="mono">${sdcm(vals, br).toFixed(1)}</span>.`;
    const b3 = { ei: equalInterval(vals, 3), q: quantile(vals, 3), nb: jenks(vals, 3) };
    document.querySelector("#memTable tbody").innerHTML = G12.wards.map(w => w.open == null ? `<tr><td class="mono">${w.id}</td><td class="mono">no data</td><td class="nd" colspan="3">not classified</td></tr>` : `<tr><td class="mono">${w.id}</td><td class="mono">${w.open}</td>${["ei", "q", "nb"].map(m => { const c = classOf(w.open, b3[m]); return `<td class="c${c + 1}">class ${c + 1}</td>`; }).join("")}</tr>`).join("");
  }
  [method, kSel, showVals].forEach(e => e.addEventListener("change", draw)); draw();

  const sel = document.querySelectorAll(".w11");
  sel.forEach(s => s.addEventListener("change", () => { if ([...sel].some(x => !x.value)) return; const want = ["2 (mid)", "3 (dark)", "2 (mid)"], got = [...sel].map(x => x.value); const ok = got.every((g, i) => g === want[i]); document.getElementById("w11Out").innerHTML = (ok ? "<strong>Correct.</strong> " : "<strong>Not all right.</strong> ") + "W11 (17) is class 2 under equal interval (16–30), class 3 under quantile (top five: 17, 31, 35, 40, 45), and class 2 under natural breaks (13–17). A councillor for W11 shown the quantile map will ask why the ward is “red”; shown the natural-breaks map they will not. Which is right depends on the question — and the legend must let the reader see the difference."; }));

  let cmp = "recalc";
  function cmpDraw() {
    document.querySelectorAll("[data-cmp]").forEach(b => b.setAttribute("aria-pressed", b.dataset.cmp === cmp));
    const aug = g12Values("aug"), sep = g12Values("sep");
    const av = Object.values(aug).filter(v => v != null), sv = Object.values(sep).filter(v => v != null);
    const bA = cmp === "recalc" ? quantile(av, 3) : [7, 17, 45], bS = cmp === "recalc" ? quantile(sv, 3) : [7, 17, 45];
    const st = (vals, br) => w => vals[w.id] == null ? { hatch: true } : (c => ({ fill: RAMP[3][c], dark: c === 2, label: vals[w.id] }))(classOf(vals[w.id], br));
    renderG12(document.getElementById("augFig"), { style: st(aug, bA), caption: "Classes: " + classLabels(av, bA).join(" / ") });
    renderG12(document.getElementById("sepFig"), { style: st(sep, bS), caption: "Classes: " + classLabels(sv, bS).join(" / ") });
    document.getElementById("cmpOut").innerHTML = cmp === "recalc"
      ? "<strong>Recalculated:</strong> September’s top five by value are W10 (16), W11 (17), W15 (20), W13 (33), W14 (38). <strong>W10 and W11 turned dark although their values did not change</strong>, and W12 — the biggest improvement — simply left the dark class. A reader concludes “W10 and W11 got worse”. False."
      : "<strong>Fixed classes:</strong> W12 (12) moves from dark to mid; W15 (20) stays dark; nothing else moves. The pair now shows exactly what happened: one ward improved out of the top class, one improved but is still there. The legend on both maps says “classes fixed across both months”.";
  }
  document.querySelectorAll("[data-cmp]").forEach(b => b.addEventListener("click", () => { cmp = b.dataset.cmp; cmpDraw(); })); cmpDraw();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
