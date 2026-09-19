<?php $page = ['title' => '8.1 Things, events, and questions', 'chapter' => 8, 'module' => '8.1']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 8.1 · General idea (works in any GIS or database)</div>
    <h1>Start from the things, the events, and the questions</h1>
    <p class="lead">Before you open any software, decide <em>what kinds of things</em> you are recording. In our town there are five: assets, inspections, service requests, wards and teams. Each one gets its own table. Then decide what <strong>one row</strong> of each table means. Then keep only the fields that answer a real question.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Tell apart the five kinds of thing and say which ones need their own location on the map.</li>
      <li>Say the <strong>grain</strong> of a table — exactly what one row stands for — and spot a table that mixes two grains.</li>
      <li>Justify every field with a business question, and remove the ones that have none.</li></ul></div>
  </div>

  <h2><span class="mod">8.1.1</span>Five kinds of thing that must not share a table</h2>
  <p>A word you will hear a lot from now on: <dfn title="A kind of thing you record — each kind becomes one table">entity</dfn>. It just means “a kind of thing”. The municipality deals with five kinds:</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Entity</th><th>One row is…</th><th>Has its own place on the map?</th><th>Points to another entity?</th></tr></thead>
    <tbody>
      <tr><td><strong>Asset</strong></td><td>one physical thing the town looks after — one streetlight pole, one drain, one tree</td><td class="yes">Yes — a point in our practice grid</td><td>Usually no; it is the thing others point <em>to</em></td></tr>
      <tr><td><strong>Inspection</strong></td><td>one visit by one crew to one asset on one day, and what they saw</td><td class="no">No — it happens <em>at</em> the asset, which already has the location</td><td>Yes — to the asset visited and to the crew</td></tr>
      <tr><td><strong>Service request</strong></td><td>one complaint from a citizen about a problem at a place</td><td class="yes">Yes — where the citizen said, which may not be exactly at any asset</td><td>Maybe — to an asset, if a clerk worked out which one is meant</td></tr>
      <tr><td><strong>Ward</strong></td><td>one administrative area</td><td class="yes">Yes — a polygon</td><td>No</td></tr>
      <tr><td><strong>Team</strong></td><td>one crew of people</td><td class="no">No — a crew is not a place</td><td>No; inspections point to it</td></tr>
    </tbody></table></div>
  <p>Two questions in that table do most of the design work. <strong>“Has its own place?”</strong> decides whether the table gets a shape column — in ArcGIS words, whether it becomes a <dfn title="ArcGIS name for a table where every row has a shape of the same kind (all points, or all lines, or all polygons)">feature class</dfn> or stays a plain table (Esri calls that a <em>stand-alone table</em>). <strong>“Points to another entity?”</strong> decides where the connecting keys go. An inspection does not need coordinates of its own: it <em>points to</em> the asset, and the asset’s location comes along with that pointer. Storing coordinates on the inspection as well would give you two copies that can disagree.</p>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Which table does each column belong to?</h3>
    <p>A colleague has thrown all of these columns into one big spreadsheet. Click a column, then click the table it really belongs to.</p>
    <div class="sorter" data-items='[
      {"t":"install_year","bin":"Asset","why":"a fact about the pole itself; it never changes per visit"},
      {"t":"condition_seen","bin":"Inspection","why":"what the crew saw on one visit"},
      {"t":"reporter_phone","bin":"Request","why":"belongs to the person who complained"},
      {"t":"crew_name","bin":"Team","why":"a crew is its own thing; inspections point to it"},
      {"t":"asset_type (streetlight/drain/tree)","bin":"Asset","why":"describes the physical thing"},
      {"t":"visit_date_time","bin":"Inspection","why":"the moment of one visit"},
      {"t":"ward_boundary (polygon)","bin":"Ward","why":"the area itself"},
      {"t":"reported_on","bin":"Request","why":"when the citizen complained"},
      {"t":"defects_found","bin":"Inspection","why":"counted on one visit"}
    ]'>
      <div class="items"></div>
      <div class="bins">
        <div class="bin" data-bin="Asset"><h5>Asset</h5></div>
        <div class="bin" data-bin="Inspection"><h5>Inspection</h5></div>
        <div class="bin" data-bin="Request"><h5>Request</h5></div>
        <div class="bin" data-bin="Ward"><h5>Ward</h5></div>
        <div class="bin" data-bin="Team"><h5>Team</h5></div>
      </div>
    </div>
  </div>

  <div class="callout dev"><span class="label">Developer view</span><p>This is ordinary database design: entities become tables, “points to” becomes a foreign key, and you keep each fact in one place (normalisation). <strong>Where the comparison stops:</strong> a GIS table has one special column that holds a <em>shape</em>. The software draws it, indexes it, and usually allows only one shape column of one kind per table (ArcGIS: exactly one geometry field; PostGIS: a column can be limited to, say, only points). So “where does the shape live?” is a design question of its own — module 8.2.</p></div>

  <h2><span class="mod">8.1.2</span>Grain: one asset, one visit, or one complaint?</h2>
  <p>The <dfn title="The exact answer to: what does ONE row of this table stand for?">grain</dfn> of a table is the exact answer to “what does one row stand for?”. Every table must have <strong>one</strong> grain, written down. Mixing grains is the most common and most expensive mistake in operational data — and it is usually an accident.</p>
  <div class="grid-3">
    <div class="card"><h4 style="margin-top:0">One physical asset</h4><p>The row exists as long as the pole, drain or tree exists. Values change slowly: installation year never; condition now and then.</p></div>
    <div class="card"><h4 style="margin-top:0">One inspection visit</h4><p>The row is created when a crew visits and never changes afterwards — it records what was seen <em>that day</em>. Ten visits = ten rows.</p></div>
    <div class="card"><h4 style="margin-top:0">One complaint (request)</h4><p>The row is created when a citizen reports something. Five people reporting the same broken light = five rows. (Chapter 1’s P6 was “a duplicate of another report” — that is a <em>link</em> between two rows, not a reason to delete one.)</p></div>
  </div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Table F4 from Chapter 1 has a hidden problem — find it</h3>
    <p>This is the asset table you have used since Chapter 1. Click each column heading and ask: <em>when this value changes, what kind of thing changed — the asset, or a visit?</em></p>
    <div class="table-wrap"><table id="grainTable">
      <thead><tr><th data-g="asset">Asset ID</th><th data-g="asset">Type</th><th data-g="asset">(x, y)</th><th data-g="asset">Installed</th><th data-g="visit">Last inspected</th><th data-g="visit">Condition</th></tr></thead>
      <tbody>
        <tr><td class="mono">SL-0113</td><td>Streetlight</td><td class="mono">(205, 195)</td><td>2018</td><td class="mono">2026-03-14</td><td>Fair</td></tr>
        <tr><td class="mono">DR-0042</td><td>Drain</td><td class="mono">(995, 510)</td><td>2011</td><td class="mono">2025-11-02</td><td>Poor</td></tr>
        <tr><td class="mono">TR-0301</td><td>Tree</td><td class="mono">(2190, 520)</td><td>2005</td><td>—</td><td>Unknown</td></tr>
      </tbody></table></div>
    <div class="status-line q" id="grainStatus">Click a column heading.</div>
    <details class="reveal"><summary>What went wrong here, in one story</summary>
      <p>SL-0113 was inspected on 14 September 2025 (all fine, zero defects) and again on 14 March 2026 (lamp flickers at dusk). Table F4 has room for <strong>one</strong> visit. When the March visit was typed in, the September values were overwritten. Now nobody can say <em>when</em> the flicker started, and nothing in the table even shows that something was lost. Two grains — asset and visit — shared one row, so the table could only ever remember the latest visit.</p>
    </details>
  </div>

  <div class="callout warn"><span class="label">Common mistake</span><p><em>“One row per asset with the latest inspection is simpler, and that is all the dashboard needs.”</em> The dashboard may indeed only need the latest state — but you can always <em>compute</em> the latest state from a full visit table, while you can never get the history back from a latest-only table. Keep the storage complete; make the display simple. Module 8.7 shows how to have both.</p></div>

  <h2><span class="mod">8.1.3</span>Every field must earn its place</h2>
  <p>A <dfn title="GIS word for a column of a table">field</dfn> (the GIS word for a column) costs money every time it exists: someone must capture the value, someone must keep it correct, and everyone who reads the table must understand it. The QGIS introduction says it plainly: collecting and storing information you do not need is a bad idea because of the cost and time of capturing it. Our rule is stricter: <strong>every field must be traceable to at least one business question or one step of a workflow.</strong> A field nobody can trace is removed — not “kept in case”.</p>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Keep or remove? Decide for each proposed Asset field</h3>
    <p>For each field, choose <strong>Keep</strong>, <strong>Remove</strong>, or <strong>Keep, but derived</strong> (the value is computed from another table). Then check.</p>
    <div class="dict" id="earn"></div>
    <p><button class="btn small" id="earnCheck">Check my answers</button> <span class="status-line q" id="earnStatus" style="display:inline-block"></span></p>
  </div>
  <p>Notice the last case: a field can be fully justified <em>and</em> still be the wrong place to store the original fact. “Current condition” is answered by a real question, but the true source is the inspection table; the asset row only carries a <strong>cached copy</strong> for convenience, and the design must say so.</p>

  <div class="quiz" data-answer="2" data-fb="Three grains are mixed: the complaint (reporter location, report date), the asset (its coordinates, installation year) and the visit (the date the crew came). The asset facts are copied on to every complaint, and the table can hold only one visit per complaint.">
    <div class="q">A colleague proposes one table called <code>Requests</code> with columns for the reporter’s location, the matched asset (including the asset’s coordinates and installation year), and the date the crew visited. How many grains are mixed?</div>
    <div class="opts">
      <button class="opt">One — it is all about a request.</button>
      <button class="opt">Two — request and asset.</button>
      <button class="opt">Three — request, asset, and inspection visit.</button>
      <button class="opt">None — copying the asset details is just convenient.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  /* grain table */
  const st = document.getElementById("grainStatus");
  document.querySelectorAll("#grainTable th").forEach(th => th.addEventListener("click", () => {
    document.querySelectorAll("#grainTable th").forEach(x => x.style.background = "");
    th.style.background = "var(--select)";
    if (th.dataset.g === "asset") { st.className = "status-line ok"; st.textContent = `“${th.textContent}” changes only when the ASSET changes. Asset grain — fine on this table.`; }
    else { st.className = "status-line bad"; st.textContent = `“${th.textContent}” changes every time a crew VISITS. Visit grain — it does not belong on an asset row. A second visit overwrites the first.`; }
  }));

  /* earn its place */
  const fields = [
    { f: "asset_type", q: "“How many streetlights do we maintain?” — also picks the inspection checklist", a: "keep" },
    { f: "install_year", q: "“Which lights are older than 10 years?” (replacement planning)", a: "keep" },
    { f: "lamp_wattage_w", q: "Ordering the right replacement lamp", a: "keep" },
    { f: "manufacturer_phone", q: "No question found — the purchase department already keeps supplier contacts", a: "remove" },
    { f: "colour_of_pole", q: "“Might be useful for photos some day”", a: "remove" },
    { f: "current_condition", q: "“Which assets are in poor condition right now?” — but the crews record condition per VISIT", a: "derived" }
  ];
  const opts = [["", "— choose —"], ["keep", "Keep"], ["remove", "Remove"], ["derived", "Keep, but derived"]];
  document.getElementById("earn").innerHTML = fields.map((x, i) => `<div class="drow" data-i="${i}"><span class="fname">${x.f}</span><span style="grid-column:span 3">${x.q}</span><select data-i="${i}">${opts.map(o => `<option value="${o[0]}">${o[1]}</option>`).join("")}</select></div>`).join("");
  document.getElementById("earnCheck").addEventListener("click", () => {
    let ok = 0;
    document.querySelectorAll("#earn .drow").forEach(r => { const i = +r.dataset.i, v = r.querySelector("select").value; const good = v === fields[i].a; r.className = "drow " + (good ? "ok" : "bad"); if (good) ok++; });
    const s = document.getElementById("earnStatus"); s.className = "status-line " + (ok === fields.length ? "ok" : "bad"); s.style.display = "inline-block";
    s.textContent = ok === fields.length ? "All six right. Two removed, three kept, one kept as a derived copy." : `${ok} of 6 right. Red rows: re-read the question column — is there a real question, and where does the value really come from?`;
  });
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
