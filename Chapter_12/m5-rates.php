<?php $page = ['title' => '12.5 Totals, rates and density', 'chapter' => 12, 'module' => '12.5']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 12.5 · General GIS idea</div>
    <h1>A count is workload. A rate is a comparison.</h1>
    <p class="lead">A ward with more people, more roads and more streetlights will have more requests — of course. To compare wards <em>fairly</em> you divide the count by something that measures “how much is there to go wrong”. Choosing that <strong>denominator</strong> is the analysis; the map only shows the result.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Explain what a <strong>choropleth</strong> is and why comparing raw totals across wards is usually unfair.</li>
      <li>Work the blueprint’s example: 100 requests among 10,000 people vs 60 among 3,000.</li>
      <li>Choose a denominator that matches the <em>question</em> — households, road length, number of assets — and refuse to call any rate “risk”.</li></ul></div>
  </div>

  <h2><span class="mod">12.5.1</span>The choropleth and the denominator</h2>
  <p>A <strong>choropleth</strong> map fills each area (ward, district, state) with a colour for the class of a number belonging to that area. ArcGIS Pro’s help notes that graduated-colour maps are “usually called choropleth maps”. It is the standard manager’s map, and it invites a comparison: <em>“W15 is darker than W05, so W15 is worse.”</em> That is fair only if the number is comparable between the areas.</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Word</th><th>Formula</th><th>Answers</th><th>Example (W15, made-up)</th></tr></thead>
    <tbody>
      <tr><td><strong>Total (count)</strong></td><td>number of requests</td><td>How much work is there here?</td><td>45 open requests</td></tr>
      <tr><td><strong>Rate</strong></td><td>total ÷ number of things exposed × constant</td><td>How much per household / per lamp?</td><td>45 ÷ 9,000 × 1,000 = <strong>5.0 per 1,000 households</strong></td></tr>
      <tr><td><strong>Density</strong></td><td>total ÷ area or length</td><td>How concentrated in space?</td><td>45 ÷ 10 km = <strong>4.5 per km of road</strong></td></tr>
    </tbody></table></div>
  <p>A total is not <em>wrong</em>. It is the right number for a workload question (“how many crews does W15 need?”). It is the wrong number for a comparison question (“is W15 unusually bad?”). ArcGIS Pro provides the mechanics under <strong>Normalization</strong> (“choose a field from the Normalization menu”); ArcGIS Online calls it <strong>Divide by</strong>.</p>
  <div class="callout dev"><span class="label">Developer view</span><p>A service logging 1,000 errors a day is not worse than one logging 100 if it handles 100× the traffic — every SRE divides by requests before comparing. The ward’s count is the error count; households or road length is the traffic. <strong>Where it stops:</strong> in monitoring the denominator is measured by the same system and is unambiguous. In GIS the candidate denominators are <em>different datasets</em> with their own dates and gaps, and choosing between them is a judgement the map must disclose.</p></div>

  <h2><span class="mod">12.5.2</span>The blueprint’s arithmetic — and G-12’s</h2>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Two wards, two answers</h3>
    <p>Ward A has 100 requests and 10,000 residents. Ward B has 60 requests and 3,000 residents (made-up numbers). Change them and watch both rankings.</p>
    <div class="grid-2">
      <div class="card" style="margin:0"><h4 style="margin-top:0">Ward A</h4><label>Requests <input type="number" id="aR" value="100" min="0" style="width:90px"></label> &nbsp; <label>Residents <input type="number" id="aP" value="10000" min="1" style="width:110px"></label></div>
      <div class="card" style="margin:0"><h4 style="margin-top:0">Ward B</h4><label>Requests <input type="number" id="bR" value="60" min="0" style="width:90px"></label> &nbsp; <label>Residents <input type="number" id="bP" value="3000" min="1" style="width:110px"></label></div>
    </div>
    <div class="tiles" id="abTiles"></div>
    <div class="result" id="abOut"></div>
  </div>
  <p>Both statements are true at once: A has <em>more requests</em>; B has <em>more requests per 1,000 residents</em> (20 vs 10). A reader shown only the count map “knows” A is worse; shown only the rate map, B. The map’s author must know which question is being asked — and must write the variable and its denominator on the map.</p>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>District G-12: three denominators, three “worst wards”</h3>
    <div class="controls">
      <button class="btn small" data-den="open" aria-pressed="true">Count</button>
      <button class="btn small" data-den="rate">Per 1,000 households</button>
      <button class="btn small" data-den="perkm">Per km of road</button>
    </div>
    <div class="pair">
      <figure class="map-fig" id="denFig"></figure>
      <div><h4>Top three</h4><div class="tiles" id="denTiles"></div><div class="result" id="denOut"></div></div>
    </div>
  </div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“Divide by area — that is what density means and it is always right.”</em> Every G-12 ward is 1 km², so density by area <em>is</em> the count and changes nothing. On a real city, dividing by area makes big, thinly populated wards look pale whether or not their residents are well served. Area is one candidate denominator among several.</p></div>

  <h2><span class="mod">12.5.3</span>Is population the right denominator?</h2>
  <p>Population is the usual choice because it is usually available. It is the <em>right</em> choice only when the thing you count is produced by people in proportion to their number. Ask: <strong>what produces the requests?</strong></p>
  <div class="table-wrap"><table>
    <thead><tr><th>Request type</th><th>What produces them</th><th>Better denominator than households</th><th>Available in our data?</th></tr></thead>
    <tbody>
      <tr><td>Streetlight out</td><td>Streetlights</td><td>Number of streetlight assets per ward</td><td>Yes — the assets layer (Chapter 3)</td></tr>
      <tr><td>Pothole</td><td>Roads, traffic</td><td><code>road_km</code></td><td>Yes</td></tr>
      <tr><td>Blocked drain</td><td>Drains, rainfall</td><td>Number of drain assets</td><td>Drains yes; rainfall no</td></tr>
      <tr><td>Fallen tree</td><td>Trees, storms</td><td>Number of recorded trees</td><td>Trees yes</td></tr>
      <tr><td>All types together</td><td>a mixture</td><td>no single honest denominator — show types separately, or state the compromise</td><td>—</td></tr>
    </tbody></table></div>
  <p>And one denominator people forget: <strong>reporting</strong>. Requests are <em>reports</em>. A ward with an active residents’ association and a well-known app produces more requests per fault than a ward where people do not report. A high rate can mean more faults <em>or</em> more reporting; the request table cannot tell them apart. Only an independent check — an inspection survey of a sample of assets (Chapter 8) — can.</p>
  <div class="callout idea"><span class="label">So the rule is</span><p>A rate map may be titled <em>“open requests per 1,000 households”</em> and never <em>“service quality”</em>, <em>“risk”</em> or <em>“problem areas”</em>. The notes say which denominator was used, its source and date, and why. If the decision is about <strong>crew capacity</strong>, the honest number may be the <em>total</em> after all — a crew visits requests, not rates — shown beside a rate map that answers the fairness question.</p></div>

  <div class="quiz" data-answer="2" data-fb="P: 24 ÷ 6,000 × 1,000 = 4.0 per 1,000 households and 24 ÷ 12 = 2.0 per km. Q: 18 ÷ 2,000 × 1,000 = 9.0 and 18 ÷ 3 = 6.0. Q is “worse” under both denominators (P is worse by count). Roads produce potholes, so use per km of road for resurfacing — and title it “open pothole requests per km of road”, not “road condition”.">
    <div class="q">Ward P: 24 open pothole requests, 6,000 households, 12 km of road. Ward Q: 18, 2,000 households, 3 km of road (made-up). Which ward should a road-resurfacing decision look at first, and by which measure?</div>
    <div class="opts"><button class="opt">P — it has more requests.</button><button class="opt">P — per household it is worse.</button><button class="opt">Q — 6.0 requests per km of road vs 2.0; roads produce potholes.</button><button class="opt">Neither — the numbers are too small to use.</button></div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const ids = ["aR", "aP", "bR", "bP"].map(i => document.getElementById(i));
  function ab() {
    const [aR, aP, bR, bP] = ids.map(e => +e.value || 0); const ra = aR / Math.max(1, aP) * 1000, rb = bR / Math.max(1, bP) * 1000;
    document.getElementById("abTiles").innerHTML = `<div class="tile ${aR >= bR ? "bad" : ""}"><div class="k">A — count</div><div class="v">${fmtN(aR)}</div></div><div class="tile ${bR > aR ? "bad" : ""}"><div class="k">B — count</div><div class="v">${fmtN(bR)}</div></div><div class="tile ${ra >= rb ? "bad" : ""}"><div class="k">A — per 1,000 residents</div><div class="v">${fmtN(ra, 1)}</div></div><div class="tile ${rb > ra ? "bad" : ""}"><div class="k">B — per 1,000 residents</div><div class="v">${fmtN(rb, 1)}</div></div>`;
    document.getElementById("abOut").innerHTML = `By count, <strong>${aR >= bR ? "A" : "B"}</strong> is higher. Per 1,000 residents, <strong>${ra >= rb ? "A" : "B"}</strong> is higher${(aR >= bR) !== (ra >= rb) ? " — <strong>the opposite ward</strong>. Same data, two honest maps, two different “worst” wards" : ""}.`;
  }
  ids.forEach(e => e.addEventListener("input", ab)); ab();

  let den = "open";
  const meta = { open: ["Open requests (count)", w => w.open, v => fmtN(v), "open requests"], rate: ["Open requests per 1,000 households", w => w.rate, v => fmtN(v, 1), "per 1,000 households"], perkm: ["Open requests per km of road", w => w.perkm, v => fmtN(v, 2), "per km of road"] };
  function denDraw() {
    document.querySelectorAll("[data-den]").forEach(b => b.setAttribute("aria-pressed", b.dataset.den === den));
    const [title, get, f, unit] = meta[den], ws = G12.wards.filter(w => w.open != null), vals = ws.map(get);
    const br = den === "rate" ? [2.5, 5.0, 15.5] : den === "perkm" ? [1.0, 2.6, 4.5] : jenks(vals, 3);
    renderG12(document.getElementById("denFig"), { style: w => w.open == null ? { hatch: true } : (c => ({ fill: RAMP[3][c], dark: c === 2, label: f(get(w)) }))(classOf(get(w), br)), title, caption: `${title}. Classes ${classLabels(vals, br).join(" / ")} (${den === "open" ? "natural breaks" : "manual — stated because several wards tie"}). W16: no data.` });
    const top = [...ws].sort((a, b) => get(b) - get(a)).slice(0, 3);
    document.getElementById("denTiles").innerHTML = top.map((w, i) => `<div class="tile ${i === 0 ? "bad" : ""}"><div class="k">${i + 1}. ${w.id}</div><div class="v">${f(get(w))}</div><div class="s">${unit}</div></div>`).join("");
    document.getElementById("denOut").innerHTML = den === "open" ? "By <strong>count</strong>, the bottom row wins — W15, W14, W13 — which are also the three most populous wards."
      : den === "rate" ? "Per <strong>1,000 households</strong>, W12 (15.5) and W11 (10.0) lead: small populations, many requests. W14 (40 requests, 10,000 households) drops to 4.0."
      : "Per <strong>km of road</strong>, W15 (4.5) and W11 (4.25) lead. W12, with 20 km of road, drops to 1.55 — its many requests are spread along a long network.";
  }
  document.querySelectorAll("[data-den]").forEach(b => b.addEventListener("click", () => { den = b.dataset.den; denDraw(); })); denDraw();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
