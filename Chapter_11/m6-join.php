<?php $page = ['title' => '11.6 Spatial join — count complaints per ward', 'chapter' => 11, 'module' => '11.6']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 11.6 · General idea, with ArcGIS Pro and QGIS wording</div>
    <h1>Spatial join: count per ward — and make the numbers balance</h1>
    <p class="lead">A <strong>spatial join</strong> copies attributes from one layer onto another because of <em>where</em> the features are, not because of a shared key. Its most common use is a count: “how many complaints in each ward?” The count is easy. Keeping the zeros, seeing the ones counted twice, and listing the ones that matched nothing — that is the skill.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Learn the vocabulary: target, join, one-to-one, one-to-many, keep-all, match option.</li>
      <li>Count complaints per ward under a stated boundary rule and keep the ward with zero.</li>
      <li>Run the join the other way round to <strong>reconcile</strong> every complaint: assigned, counted twice, or outside.</li></ul></div>
  </div>

  <h2><span class="mod">11.6.1</span>Target, join, and the boundary rule</h2>
  <p>ArcGIS Pro’s Spatial Join “Joins attributes from one or more inputs to another input based on the spatial relationship.” Its words, which this chapter adopts:</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Word</th><th>Meaning</th></tr></thead>
    <tbody>
      <tr><td><strong>Target features</strong></td><td>The layer whose records appear in the output — one output row per target (or per target–match pair).</td></tr>
      <tr><td><strong>Join features</strong></td><td>The layer whose attributes are copied onto the targets.</td></tr>
      <tr><td><strong>Join one to one</strong></td><td>“If multiple join features are found that have the same spatial relationship with a single target feature, the attributes from the multiple join features will be aggregated using a field map merge rule.” One row per target.</td></tr>
      <tr><td><strong>Join one to many</strong></td><td>One output row per target–join pair.</td></tr>
      <tr><td><strong>Keep all target features</strong></td><td>Ticked: “All target features will be maintained in the output (outer join).” Unticked: “Only those target features that have the specified spatial relationship with the join features will be maintained (inner join).”</td></tr>
      <tr><td><strong>Match option</strong></td><td>The yes/no test: Intersect, Contains, Completely contains, Contains Clementini, Within, Completely within, Closest, Have their center in, Largest overlap, and more.</td></tr>
      <tr><td><code>Join_Count</code></td><td>Output field: “The number of join features that match each target feature.” With one-to-many, <code>JOIN_FID</code> is added and −1 means no match.</td></tr>
    </tbody></table></div>
  <p>QGIS splits the idea in two: <strong>Join attributes by location</strong> (predicates intersect, contain, equal, touch, overlap, are within, cross; join types one-to-many, first match, or largest overlap; an option to “Discard records which could not be joined”) and <strong>Join attributes by location (summary)</strong>, which writes a <code>JOINED_COUNT</code> and statistics. A separate <strong>Count points in polygon</strong> writes <code>NUMPOINTS</code>. In PostGIS it is <code>LEFT JOIN … ON ST_Covers(ward.geom, request.geom) … GROUP BY ward</code> — the <code>LEFT</code> keeps the zero-count wards.</p>
  <h3>Counting complaints per ward</h3>
  <p>Target = <strong>wards</strong> (we want one row per ward, even one with zero). Join = <strong>complaints</strong>. Operation = <strong>one to one</strong>, so <code>Join_Count</code> becomes the count. <strong>Keep all target features</strong> must be ticked so that a ward with no complaints still appears. The match option encodes the boundary rule from Chapter 10:</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Requirement</th><th>ArcGIS Pro match option (target = wards)</th><th>What happens to P5, on the A/B line</th></tr></thead>
    <tbody>
      <tr><td>Raw membership, boundary counts</td><td><strong>Contains</strong> — Esri’s examples page: “The selecting features can be inside as well as on the boundary”</td><td>Counted in A <em>and</em> in B</td></tr>
      <tr><td>Strictly inside only</td><td><strong>Completely contains</strong> — matched only if the feature “does not intersect the boundary”</td><td>Counted in neither</td></tr>
      <tr><td>One ward per complaint (the Chapter 10 policy)</td><td>No single option does this. Compute the raw boundary-inclusive matches, then apply the tie-break in a table step (11.6.3)</td><td>Counted once, in A, marked BOUNDARY_TIEBREAK</td></tr>
    </tbody></table></div>
  <div class="callout note"><span class="label">The Chapter 10 policy, restated</span><p>Raw membership is boundary-inclusive. For a <em>one-ward-per-complaint</em> report: an interior complaint gets its ward (<code>INTERIOR</code>); a complaint touching two wards goes to the ward with the alphabetically lower code, marked <code>BOUNDARY_TIEBREAK</code>; a complaint touching no ward keeps <code>assigned_ward = NULL</code>, marked <code>OUTSIDE</code>, and is <em>reported</em>, never dropped and never quietly given the nearest ward. Raw counts and policy counts are always shown side by side.</p></div>

  <h2><span class="mod">11.6.2</span>Counts per ward: the three things to inspect</h2>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Join complaints onto wards A, B, C</h3>
    <div class="controls">
      <label>Match option
        <select id="match"><option value="incl">Contains (boundary counts)</option><option value="strict">Completely contains (strictly inside)</option></select></label>
      <label><input type="checkbox" id="keepAll" checked> Keep all target features</label>
      <label><input type="checkbox" id="elig"> only eligible complaints (OPEN, since 1 Aug)</label>
    </div>
    <figure class="map-fig" id="joinFig"></figure>
    <div class="grid-2">
      <div class="table-wrap"><table><thead><tr><th>Ward</th><th>Join_Count</th><th>Matched IDs</th></tr></thead><tbody id="joinRows"></tbody></table></div>
      <div>
        <div class="sum-box" id="sumBox"></div>
        <div class="result" id="joinOut"></div>
      </div>
    </div>
  </div>
  <p>With Contains, all six complaints, and Keep all ticked, you get A 3, B 3, C 0. Three things to inspect, in this order:</p>
  <ol>
    <li><strong>Unmatched records.</strong> P6 at (2200, 500) is in no ward. It appears in no count — and it is <em>invisible in this output</em>. A wards-as-target join cannot show you a complaint that matched nothing. To see it you need the reverse join (11.6.3). Every analysis must list its unmatched records explicitly.</li>
    <li><strong>Zero-count areas.</strong> Ward C has <code>Join_Count = 0</code>. Untick Keep all and C vanishes — a reader cannot tell whether C has no complaints or was never examined. Zero is information; keep it.</li>
    <li><strong>Multiple matches.</strong> The counts sum to 6 for 5 distinct complaints, because P5 is counted in both A and B. A sum larger than the number of complaints is <em>always</em> explained by records counted more than once (or by duplicate source records — Chapter 9). Find them and name them.</li>
  </ol>
  <p><strong>If you want names, not counts.</strong> One-to-one with the default settings gives you <em>one</em> complaint’s attributes per ward — the merge rule picks which, silently. Use <strong>one to many</strong> to get one row per ward–complaint pair (six rows), or the <strong>Concatenate</strong> merge rule to get “P1,P2,P5” in one cell. Either way, write in the worksheet what one output row <em>is</em>: a ward, or a ward–complaint pair (Chapter 10’s rule about what you are counting).</p>

  <h2><span class="mod">11.6.3</span>Reconcile every complaint: the join the other way round</h2>
  <p>The check that catches most mistakes in this chapter costs one small table. Run the join with <strong>complaints as the target</strong> and wards as the join layer, <strong>one to many</strong>, Keep all ticked, match option <strong>Within</strong> (the mirror of Contains). Every complaint then shows how many wards it touched: 1 = assigned, 2 = counted twice, 0 = outside.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Reverse join and the accounting identity</h3>
    <p class="small">Uses the same match option and eligibility choice as the demo above.</p>
    <div class="table-wrap"><table><thead><tr><th>Complaint</th><th>Ward rows returned</th><th>Class</th><th>Policy result</th></tr></thead><tbody id="recRows"></tbody></table></div>
    <div class="result" id="recOut"></div>
  </div>
  <p>The identity to check: <strong>assigned + counted-twice + outside = number of complaints</strong>. If it does not hold, something was dropped or duplicated — find it by ID before reporting anything. The per-ward sum of 6 is now <em>explained</em>: five distinct complaints plus one double count (P5). Under the policy, the report reads A 3 (P1, P2, P5†), B 2 (P3, P4), C 0, outside 1 (P6) — five assignments plus one explicit “outside” = six.</p>
  <div class="callout dev"><span class="label">Developer view</span><p>In a SQL left join, <code>rows = matched pairs + unmatched left rows</code>; if you cannot make the numbers balance you have mis-stated the join. Exact. Where it stops: SQL keys match or they don’t; spatial matches also have a <em>tolerance</em> and a <em>boundary rule</em>, so the same P5 is “matched” under one option and not under another. Name the option in the table heading.</p></div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“The per-ward counts are the report.”</em> They are one view of it. Without the reconciliation — the outside list, the double-count list, the policy applied — a reader cannot tell whether 3 + 3 = 6 means six complaints or five. Make the explanation part of the deliverable, not a footnote.</p></div>

  <div class="quiz" data-answer="2" data-fb="Completely contains: A = 2 (P1, P2), B = 2 (P3, P4), C = 0; outside = 2 (P5, P6); identity 4 + 0 + 2 = 6. P5 moved from “counted twice” to “outside” — a boundary point is not strictly inside either ward.">
    <div class="q">Quick check 11.6. Using all six complaints and wards A, B, C with <em>Completely contains</em> (strictly inside): what are the counts and the identity?</div>
    <div class="opts"><button class="opt">A 3, B 3, C 0; 6 assigned.</button><button class="opt">A 2, B 2, C 0; P5 counted in A by default.</button><button class="opt">A 2, B 2, C 0; outside = P5 and P6; 4 + 0 + 2 = 6.</button><button class="opt">A 3, B 2, C 0; outside = P6.</button></div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const wards3 = WARDS.slice(0, 3), fig = document.getElementById("joinFig");
  function draw() {
    const incl = document.getElementById("match").value === "incl", keep = document.getElementById("keepAll").checked, el = document.getElementById("elig").checked;
    const list = el ? eligible(REQUESTS) : REQUESTS;
    const m = gridMap(fig, { extent: { x1: -250, y1: -250, x2: 2450, y2: 1700 }, caption: `Spatial join, target = wards, match = ${incl ? "Contains (boundary counts)" : "Completely contains (strictly inside)"}. Red = counted twice; grey = matched nothing.` });
    drawWards(m, wards3);
    const matches = wards3.map(w => ({ w, ids: list.filter(r => inWard(r.x, r.y, w, incl)).map(r => r.id) }));
    const per = {}; list.forEach(r => per[r.id] = wardsOf(r.x, r.y, wards3, incl));
    drawRequests(m, list, r => per[r.id].length === 0 ? "out" : per[r.id].length > 1 ? "dup" : "in");
    const rows = matches.filter(x => keep || x.ids.length).map(x => ({ cls: x.ids.length === 0 ? "dim" : "", cells: [x.w.id, x.ids.length, idsText(x.ids)] }));
    tableRows(document.getElementById("joinRows"), rows);
    const sum = matches.reduce((s, x) => s + x.ids.length, 0), distinct = list.filter(r => per[r.id].length > 0).length, twice = list.filter(r => per[r.id].length > 1), none = list.filter(r => per[r.id].length === 0);
    document.getElementById("sumBox").innerHTML = `<div class="t ${sum !== distinct ? "bad" : "good"}"><span class="n">${sum}</span><span class="l">sum of Join_Count</span></div><div class="t good"><span class="n">${distinct}</span><span class="l">distinct complaints matched</span></div><div class="t ${none.length ? "bad" : "good"}"><span class="n">${none.length}</span><span class="l">matched nothing (invisible here)</span></div>`;
    document.getElementById("joinOut").innerHTML = (sum !== distinct ? `Sum ${sum} &gt; ${distinct} distinct — explained by <strong class="ids">${idsText(twice.map(r => r.id))}</strong> counted in two wards. ` : `Sum equals distinct — nothing counted twice. `) + (none.length ? `<strong class="ids">${idsText(none.map(r => r.id))}</strong> matched no ward and does not appear in this table at all. ` : "") + (keep ? "Ward C is kept with 0 — good." : "<span class='chip no'>Ward C has disappeared</span> — Keep all target features is unticked (inner join).");
    // reconciliation
    const recRows = list.map(r => { const w = per[r.id]; const cls = w.length === 0 ? "outside" : w.length > 1 ? "counted twice" : "assigned"; const pol = w.length === 0 ? `assigned_ward = NULL · <span class="chip no">OUTSIDE</span>` : w.length > 1 ? `${[...w].sort()[0]} · <span class="chip tie">BOUNDARY_TIEBREAK</span>` : `${w[0]} · <span class="chip yes">INTERIOR</span>`; return { cells: [r.id, w.length ? w.join(", ") : "(none; JOIN_FID = −1)", { html: cls, cls: cls === "assigned" ? "yes" : "no" }, { html: pol }] }; });
    tableRows(document.getElementById("recRows"), recRows);
    const a = list.filter(r => per[r.id].length === 1).length;
    document.getElementById("recOut").innerHTML = `<strong>Identity:</strong> ${a} assigned + ${twice.length} counted twice + ${none.length} outside = <strong>${a + twice.length + none.length}</strong> = number of complaints in the input (${list.length}) <span class="chip ${a + twice.length + none.length === list.length ? "yes" : "no"}">${a + twice.length + none.length === list.length ? "balances" : "does not balance"}</span>.<br>Rows in the one-to-many output: ${a} + ${2 * twice.length} + ${none.length} = <strong>${a + 2 * twice.length + none.length}</strong>. Policy report: ${wards3.map(w => `${w.id} ${list.filter(r => (per[r.id].length === 1 && per[r.id][0] === w.id) || (per[r.id].length > 1 && [...per[r.id]].sort()[0] === w.id)).length}`).join(", ")}, outside ${none.length}.`;
  }
  ["match", "keepAll", "elig"].forEach(id => document.getElementById(id).addEventListener("change", draw)); draw();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
