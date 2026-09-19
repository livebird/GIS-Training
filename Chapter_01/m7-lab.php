<?php $page = ['title' => '1.7 Guided lab: write a spatial problem brief', 'chapter' => 1, 'module' => '1.7']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 1.7 · Guided lab</div>
    <h1>Write a spatial problem brief</h1>
    <p class="lead">No software. Take a short scenario and turn it into three clear “where” questions, each with inputs, rules, output, and a way to check it. A problem brief is simply a one-page written plan.</p>
    <div class="outcomes"><h4>Objective</h4>
      <ul><li>Produce a one-page problem brief and a sketch with notes on it.</li>
      <li>Prerequisites: modules 1.1–1.6. Time: about 60–90 minutes.</li>
      <li>You can fill the brief in on this page; it saves in your browser. Print it to submit.</li></ul></div>
  </div>

  <h2><span class="mod">1.7.2</span>The scenario <span class="synthetic">(synthetic)</span></h2>
  <div class="card">
    <p><strong>Decision-maker:</strong> the Inspection Team Lead for Wards A and B.<br>
    <strong>Date of the brief:</strong> 18 September 2026.<br>
    <strong>Date range of interest:</strong> requests reported from <strong>1 August 2026 to 15 September 2026</strong>, inclusive.<br>
    <strong>Study area:</strong> Wards A and B. A request on the shared boundary counts as inside both wards. A request outside both wards is not part of this task unless a question says otherwise.</p>
    <p><strong>Situation:</strong> one field team is available tomorrow. It starts at the west end of Main Road, at (0, 500), and can work anywhere along the road (“the road corridor” — the strip of land along it). The Team Lead wants to know which requests to visit first, and whether any request needs someone other than the team (for example a duplicate report to sort out, or a request outside the area to pass to another office).</p>
    <p><strong>Data available:</strong> the wards, road, requests, and assets tables from the chapter. Distances are straight-line, in metres, on the training grid.</p>
  </div>
  <details class="reveal"><summary>Show the request table</summary><div id="labTable"></div></details>

  <h2><span class="mod">1.7.3</span>Steps</h2>
  <ol>
    <li><strong>Underline every unclear word.</strong> Expect at least: “first”, “needs someone”, “road corridor”, and the unstated “unresolved”.</li>
    <li><strong>Write the decision in one sentence</strong> beginning “Tomorrow the team will…”.</li>
    <li><strong>Draft three questions</strong>, each of a different type (for example one proximity, one containment, one change through time). Each must be answerable from the tables <em>after</em> you define its terms.</li>
    <li><strong>Fill the four-part card</strong> for each: inputs, selection rules (exact status values, distance, unit, whether the boundary counts), output, validation.</li>
    <li><strong>Apply your own rules by hand</strong> and write the resulting IDs. If you cannot, the question is not answerable yet — that is a useful discovery.</li>
    <li><strong>Draw the sketch</strong>: use the helper below, then add your notes to it (or draw your own).</li>
    <li><strong>Put the one-page brief together</strong> and add a “limitations” line of at most three sentences (what the answer does not tell you).</li>
    <li><strong>Self-check</strong> with the list at the bottom before submitting.</li>
  </ol>

  <div class="try lab-form">
    <span class="tag">Your brief</span>
    <label for="decision">Decision sentence (“Tomorrow the team will…”)</label>
    <input type="text" id="decision" data-k="decision">
    <div id="cards"></div>
    <label for="limits">Limitations (max three sentences)</label>
    <textarea id="limits" data-k="limits"></textarea>
    <div class="controls" style="margin-top:1rem"><button class="btn primary" id="saveBrief">Save brief</button> <span class="saved" id="briefSaved"></span> <button class="btn ghost" onclick="window.print()">Print brief</button> <button class="btn ghost" id="clearBrief">Clear</button></div>
  </div>

  <div class="try">
    <span class="tag">Sketch helper</span>
    <h3>Sketch with notes</h3>
    <p>Set the distance you used on your proximity card. The band along the road must match the number on the card. P5 and P6 are marked because each needs a clearly written rule.</p>
    <div class="controls">
      <label>Distance band: <input type="range" id="sk" min="0" max="500" step="10" value="300"> <strong><span id="skVal">300</span> m</strong></label>
      <label><input type="checkbox" id="skExt" checked> Draw study-area boundary</label>
    </div>
    <figure class="map-fig" id="sketch"></figure>
  </div>

  <h2><span class="mod">1.7.4</span>Self-check before you submit</h2>
  <p>There is no single correct set of questions, but every correct brief satisfies all of these.</p>
  <div class="card checklist" id="checks">
    <label><input type="checkbox" data-k="c1"> Every unclear word in the scenario has a clear, checkable meaning somewhere on the page.</label>
    <label><input type="checkbox" data-k="c2"> Each card’s rules, applied to the request table, give a definite result — and I wrote it down and checked the arithmetic.</label>
    <label><input type="checkbox" data-k="c3"> P5 (on the shared boundary and on the road) and P6 (outside both wards; closed as duplicate) are each mentioned with a stated treatment.</label>
    <label><input type="checkbox" data-k="c4"> At least one question uses a time condition tied to the date range, and I noted that nothing was reported after 2026-09-11.</label>
    <label><input type="checkbox" data-k="c5"> No question needs data the tables do not contain (travel time, population, P6’s twin) — unless the card says so and marks it “not answerable yet”.</label>
    <label><input type="checkbox" data-k="c6"> The band on my sketch matches the distance on my proximity card.</label>
  </div>

  <h2><span class="mod">1.7.5</span>Troubleshooting</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Symptom</th><th>Likely cause</th><th>Fix</th></tr></thead>
    <tbody>
      <tr><td>Two people applying your rules get different IDs</td><td>A word is still unclear (usually the edge value or the status list)</td><td>Rewrite with “≤” or “&lt;” and a full list of status values</td></tr>
      <tr><td>Your proximity question selects P6 but you “meant” to exclude it</td><td>The study-area condition was assumed, not written</td><td>Add the containment condition, or accept P6 and say why</td></tr>
      <tr><td>You cannot decide which ward P5 belongs to</td><td>That is a rule to be decided, not a fact in the data</td><td>Write the rule on the card; note that pure geometry gives two matches</td></tr>
      <tr><td>A question needs travel time</td><td>The practice data has no road network</td><td>Mark it “not answerable with current data”; do not quietly use straight-line distance instead</td></tr>
      <tr><td>The sketch shows the road continuing past x = 2000</td><td>Misreading the road table</td><td>R1 is finite; P6’s distance is measured to the end point</td></tr>
    </tbody></table></div>

  <h2><span class="mod">1.7.6</span>Deliverables</h2>
  <ol>
    <li>The one-page problem brief (decision sentence, three cards with results worked out by hand, limitations line).</li>
    <li>The sketch with notes.</li>
    <li>The observation log from module 1.5 for all six requests.</li>
  </ol>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  requestTable(document.getElementById("labTable"), { cols: ["id", "xy", "category", "priority", "status", "reported", "closed", "channel", "dist"] });
  const KEY = "gis-ch1-brief";
  const saved = JSON.parse(localStorage.getItem(KEY) || "{}");
  const types = ["location", "distribution", "proximity", "containment", "connectivity", "change through time"];
  document.getElementById("cards").innerHTML = [1, 2, 3].map(n => `
    <div class="lab-card"><h4>Question ${n}</h4>
      <label>Question (with every term defined)</label><textarea data-k="q${n}"></textarea>
      <label>Type</label><select data-k="t${n}">${types.map(t => `<option>${t}</option>`).join("")}</select>
      <label>Inputs (tables, fields, date range, study area)</label><textarea data-k="i${n}"></textarea>
      <label>Selection rules (status values, distance + unit, ≤ or &lt;, boundary treatment)</label><textarea data-k="r${n}"></textarea>
      <label>Output (list of IDs / count per ward / yes-no per record)</label><input type="text" data-k="o${n}">
      <label>Validation (how to check by hand; one thing the answer does NOT establish)</label><textarea data-k="v${n}"></textarea>
      <label>Hand result (IDs)</label><input type="text" data-k="h${n}" placeholder="e.g. P1, P3, P5">
    </div>`).join("");
  const fields = () => document.querySelectorAll(".lab-form [data-k], #checks [data-k]");
  fields().forEach(f => { const v = saved[f.dataset.k]; if (v == null) return; if (f.type === "checkbox") f.checked = !!v; else f.value = v; });
  document.getElementById("saveBrief").addEventListener("click", () => { const o = {}; fields().forEach(f => o[f.dataset.k] = f.type === "checkbox" ? f.checked : f.value); localStorage.setItem(KEY, JSON.stringify(o)); document.getElementById("briefSaved").textContent = "Saved " + new Date().toLocaleTimeString(); });
  document.getElementById("clearBrief").addEventListener("click", () => { if (confirm("Clear the saved brief?")) { localStorage.removeItem(KEY); location.reload(); } });
  document.getElementById("checks").addEventListener("change", () => document.getElementById("saveBrief").click());

  const sk = document.getElementById("sk");
  function drawSketch() {
    document.getElementById("skVal").textContent = sk.value;
    renderMap(document.getElementById("sketch"), { band: +sk.value, layers: { wards: true, roads: true, requests: true, assets: true }, extent: document.getElementById("skExt").checked ? "wards" : null,
      extra: (NS, svg, mk) => { const a = mk("text", { x: SX(1030), y: SY(420), class: "note-text" }); a.textContent = "P5: boundary rule needed"; const b = mk("text", { x: SX(2040), y: SY(420), class: "note-text" }); b.textContent = "P6: outside wards"; },
      caption: `Sketch: band = ${sk.value} m straight-line from R1 · study area dashed · assets as diamonds` });
  }
  sk.addEventListener("input", drawSketch); document.getElementById("skExt").addEventListener("change", drawSketch); drawSketch();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
