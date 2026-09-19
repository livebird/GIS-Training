<?php $page = ['title' => '11.1 Write the question before choosing a tool', 'chapter' => 11, 'module' => '11.1']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 11.1 · General idea (works in every GIS)</div>
    <h1>Write the question first. Pick the tool last.</h1>
    <p class="lead">Every GIS shows you a long list of analysis tools. The temptation is to start there: “Buffer sounds right, let’s click it.” Experienced people do the opposite. They write seven short lines about the <em>question</em>, decide what <em>kind</em> of answer is needed, and only then look at the tool list.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Fill in the seven items of an <strong>analysis specification</strong> for a real-sounding request.</li>
      <li>Sort questions into the <strong>four kinds of answer</strong> — a list, a new shape, extra columns, or a count — and see why each kind needs a different operation.</li>
      <li>Build an <strong>analysis worksheet</strong> and delete the one step that nobody uses.</li></ul></div>
  </div>

  <h2><span class="mod">11.1.1</span>Seven lines before any tool</h2>
  <p>Here is the request we will use through the whole chapter. It comes from the works manager of our made-up municipality:</p>
  <div class="callout note"><span class="label">The manager’s request (practice scenario)</span><p>“How many <strong>open</strong> complaints, reported <strong>since 1 August</strong>, are <strong>close to Main Road (R1)</strong> in <strong>each ward</strong>? I have to decide where the road crew goes next week.”</p></div>
  <p>“Close to” is not a number yet. “Each ward” does not say what happens to a complaint sitting exactly on the line between two wards. Before clicking anything, we turn the sentence into seven testable lines. Each line stops one specific kind of wrong answer:</p>
  <div class="table-wrap"><table>
    <thead><tr><th>#</th><th>Item</th><th>Why it is there</th><th>Our answer (practice)</th></tr></thead>
    <tbody>
      <tr><td>1</td><td><strong>Decision</strong></td><td>Stops you making a map nobody can act on</td><td>Where to send the road crew next week</td></tr>
      <tr><td>2</td><td><strong>Study area</strong></td><td>Tells you what “outside” means, so outside records are reported — not quietly lost</td><td>Wards A and B together; anything outside both is listed separately</td></tr>
      <tr><td>3</td><td><strong>Time window</strong></td><td>Stops old complaints inflating the count</td><td>Reported on or after <span class="mono">2026-08-01</span></td></tr>
      <tr><td>4</td><td><strong>Eligible records</strong></td><td>Turns “complaints” into a rule you can test</td><td><code>status = 'OPEN'</code></td></tr>
      <tr><td>5</td><td><strong>Units, CRS, method</strong></td><td>Stops degrees being treated as metres (Chapter 6)</td><td>Metres; on the paper grid plain flat geometry; on Earth data, flat geometry in EPSG:32643</td></tr>
      <tr><td>6</td><td><strong>Output — its kind</strong></td><td>Decides which operation you need (11.1.2)</td><td>A table: one row per ward (even a ward with zero), a count, plus a list of “outside” and “on the line” cases</td></tr>
      <tr><td>7</td><td><strong>Acceptance checks</strong></td><td>Turns “looks right” into a test</td><td>Every eligible ID appears exactly once in the assigned / on-the-line / outside lists; P2 and P4 are missing for the right reasons</td></tr>
    </tbody></table></div>
  <p>Notice what is <strong>not</strong> in the table: a tool name. The word “buffer” has not appeared. The threshold — <strong>300 metres, and exactly 300 counts (≤ 300)</strong> — is a decision the manager and you make together and write down. P1 sits at exactly 300 m from the road, so “≤ 300” and “&lt; 300” give different answers. That is why the line exists.</p>
  <div class="callout dev"><span class="label">Developer view</span><p>The specification is the failing test you write before the code: inputs, expected output <em>shape</em>, assertions. The picture holds well. Where it stops: with real GIS data you rarely have one exact expected value. You assert <em>invariants</em> (counts reconcile, everything stays inside the study area, geometry type is what you predicted) and you hand-check a few records — that is module 11.8.</p></div>

  <h2><span class="mod">11.1.2</span>Four kinds of answer, four kinds of operation</h2>
  <p>Look at the desired output and ask one question: <em>is it a selection, a new shape, extra columns, or a count?</em> The answer chooses the operation. Getting this wrong is the most common beginner mistake in the chapter — usually by building a new shape when a simple list would do.</p>
  <div class="kinds">
    <div class="kind k1"><h4>1 · A selection</h4><p>Some of the <em>existing</em> records, unchanged.</p><p class="eg">“Which streetlights are more than 50 m from any road?” → attribute query, Select By Location, or a nearest-distance number plus a condition (Chapter 10).</p></div>
    <div class="kind k2"><h4>2 · A new shape</h4><p>A geometry that exists in no input layer.</p><p class="eg">“Draw the area within 300 m of the road.” → buffer, clip, intersect, dissolve (11.2–11.5).</p></div>
    <div class="kind k3"><h4>3 · Extra columns</h4><p>Existing records with values copied from another layer.</p><p class="eg">“Add the ward name to every complaint.” → spatial join one-to-one (11.6) or a key join (Chapter 10).</p></div>
    <div class="kind k4"><h4>4 · A summary</h4><p>One number (or one row) per group.</p><p class="eg">“Complaints per ward.” → spatial join with a count, dissolve with statistics, <code>GROUP BY</code> in SQL.</p></div>
  </div>
  <p style="margin-top:1rem">Apply it to the manager’s request. The <em>final</em> answer is a <strong>summary</strong> — a count per ward. To get there we need a <strong>selection</strong> (eligible complaints close to the road) and then either extra columns or a summary (which ward each belongs to, counted). Nowhere does the question ask for a new shape. A buffer polygon is a <em>convenient way</em> to test “within 300 m”, and it makes a nice map — but it is a means, not the deliverable. If a distance number answers the same test, the buffer is optional. Say so in the worksheet.</p>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Sort the question into its kind of answer</h3>
    <p>Click a question, then click the box it belongs to. A second example shows the same rule works outside municipal work.</p>
    <div class="sorter" data-items='[
      {"t":"How many open complaints per ward?","bin":"summary","why":"one number per ward"},
      {"t":"Which of our 40 shops have a competitor within 1 km?","bin":"selection","why":"a subset of the shops, unchanged — a nearest-distance check, not a buffer"},
      {"t":"Draw the part of each ward that is within 300 m of Main Road","bin":"shape","why":"that shape exists in no input"},
      {"t":"Add each complaint’s ward name as a column","bin":"columns","why":"same records, one more column"},
      {"t":"Total households per maintenance zone","bin":"summary","why":"one row per zone (dissolve with SUM)"},
      {"t":"Which roads pass through Ward A, even partly?","bin":"selection","why":"whole roads selected, not cut"},
      {"t":"How long is the road inside Ward A?","bin":"shape","why":"you must cut the road first (clip), then measure the piece"}
    ]'>
      <div class="items"></div>
      <div class="bins">
        <div class="bin" data-bin="selection"><h5>1 · Selection</h5></div>
        <div class="bin" data-bin="shape"><h5>2 · New shape</h5></div>
        <div class="bin" data-bin="columns"><h5>3 · Extra columns</h5></div>
        <div class="bin" data-bin="summary"><h5>4 · Summary</h5></div>
      </div>
    </div>
  </div>

  <h2><span class="mod">11.1.3</span>The worksheet — and the row that has no reader</h2>
  <p>The <strong>analysis worksheet</strong> is a small table with one row per step. The important column is the last one: <em>who uses this output?</em> If no later step and no deliverable uses it, that step is unnecessary — and unnecessary steps are exactly where mistakes hide, because nobody ever reads their attribute tables.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Find the step nobody consumes, and strike it out</h3>
    <p>Below is the first draft of the worksheet for the manager’s request. Read the last column. Click <strong>Remove</strong> on the row that has no consumer.</p>
    <div class="table-wrap"><table class="ws" id="ws">
      <thead><tr><th>Step</th><th>Operation</th><th>Input(s)</th><th>Output</th><th>Consumed by</th><th></th></tr></thead>
      <tbody>
        <tr data-step="1"><td>1</td><td>Filter: <code>status = 'OPEN' AND reported_on &gt;= '2026-08-01'</code></td><td>requests</td><td><code>req_eligible</code> (P1, P3, P4, P5, P6)</td><td>steps 4, 5</td><td><button class="btn">Remove</button></td></tr>
        <tr data-step="2"><td>2</td><td>Buffer R1 by 300 m, round ends</td><td>roads</td><td><code>r1_buf300</code> (a polygon)</td><td>steps 3, 4</td><td><button class="btn">Remove</button></td></tr>
        <tr data-step="3"><td>3</td><td>Intersect <code>r1_buf300</code> with wards → “study corridor” split by ward</td><td>buffer, wards</td><td><code>corridor_by_ward</code> (2 polygons)</td><td class="noc">— nobody —</td><td><button class="btn">Remove</button></td></tr>
        <tr data-step="4"><td>4</td><td>Select <code>req_eligible</code> that intersect <code>r1_buf300</code></td><td>eligible requests, buffer</td><td><code>req_near_road</code> (P1, P3, P5, P6)</td><td>step 5</td><td><button class="btn">Remove</button></td></tr>
        <tr data-step="5"><td>5</td><td>Spatial join: count <code>req_near_road</code> per ward, keep every ward</td><td>wards, near-road requests</td><td><code>ward_counts</code></td><td>deliverable; step 6</td><td><button class="btn">Remove</button></td></tr>
        <tr data-step="6"><td>6</td><td>Reconcile: list every ID as assigned / on the line / outside</td><td>near-road requests, wards</td><td><code>reconciliation</code> table</td><td>deliverable</td><td><button class="btn">Remove</button></td></tr>
      </tbody></table></div>
    <div class="result" id="wsOut">Which row has no reader?</div>
  </div>
  <p>The corridor in step 3 <em>sounded</em> useful, but the count in step 5 comes from points and wards, not from a corridor. Worse, the corridor would carry split records with copied attributes (11.4.3) that someone might add up by mistake later. Deleting a row is a normal, expected act. Write it in the log — “step 3 removed: no consumer” — so a reviewer does not think you forgot it.</p>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“More intermediate layers means a more thorough analysis.”</em> Every extra layer has parameters, a CRS and a tolerance that can be wrong — and nobody reads its table. A reviewer must now check all of them. Fewer steps, each with a reader, is the professional habit.</p></div>

  <div class="quiz" data-answer="2" data-fb="The new request asks for a shape (the part of each ward within 300 m of the road) and a summary (its area). Step 3 now has a reader — the deliverable — so it stays. But the area must be recalculated from the new shape (600,000 m² for A and for B), never copied from a buffer field.">
    <div class="q">Quick check 11.1. The manager changes the request: “Show me, on a map, the part of each ward within 300 m of R1, and how much of each ward’s area that is.” What happens to step 3 now?</div>
    <div class="opts"><button class="opt">It stays deleted — a count never needs a corridor.</button><button class="opt">It comes back, and its copied area field can be used directly.</button><button class="opt">It comes back — the question now needs a new shape plus an area summary — but the area must be recomputed from the new shape.</button><button class="opt">Nothing changes; the question is the same as before.</button></div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  document.querySelectorAll("#ws tbody tr").forEach(tr => tr.querySelector("button").addEventListener("click", () => {
    const step = tr.dataset.step, out = document.getElementById("wsOut");
    if (step === "3") { tr.classList.add("struck"); tr.querySelector("button").disabled = true; out.innerHTML = "<span class='verdict ok'>Correct.</span> Step 3 produced a corridor polygon that no later step and no deliverable reads. Remove it and write “step 3 removed: no consumer” in the log. The worksheet now has five rows, each with a reader."; }
    else { out.innerHTML = `<span class='verdict no'>Not this one.</span> Step ${step} is consumed by <em>${tr.children[4].textContent}</em>. Removing it would break a later step or a deliverable. Look for the row whose last column says nobody.`; }
  }));
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
