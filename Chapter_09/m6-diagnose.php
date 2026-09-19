<?php $page = ['title' => '9.6 Diagnose attribute and relationship problems', 'chapter' => 9, 'module' => '9.6']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 9.6 · General idea, written as plain SQL</div>
    <h1>Five questions the table cannot dodge</h1>
    <p class="lead">Table problems hide in plain sight because every row <em>looks</em> normal. You find them by asking questions the data must answer: are any IDs repeated? are any categories outside the allowed list? is any must-have value missing? is any number outside its range? does any child row point at a parent that does not exist? Then one harder question: are two complaints at the same spot one complaint sent twice — or two different problems?</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Run the five checks on the damaged tables and read what each hit really means.</li>
      <li>Decide “duplicate or not” from evidence about the <em>reports</em>, never from distance alone.</li>
      <li>Write a defect log with six fixed columns — including rows for false alarms.</li></ul></div>
  </div>

  <h2><span class="mod">9.6.1</span>The five checks</h2>
  <p>Each check is written as ordinary SQL against Chapter 8’s tables. The same logic works as a spreadsheet filter, ArcGIS Pro’s <em>Select By Attributes</em>, or a QGIS expression. Run all five at intake, before editing — and again after.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Run the checks on the damaged package</h3>
    <p>Each button runs the query live on the tables printed in the lab (9.8). Read the hit, then the reasoning beside it.</p>
    <div class="opbtns">
      <button class="btn small" data-chk="dup">1 · Duplicate IDs</button>
      <button class="btn small" data-chk="cat">2 · Invalid categories</button>
      <button class="btn small" data-chk="miss">3 · Missing observations</button>
      <button class="btn small" data-chk="range">4 · Unit / range</button>
      <button class="btn small" data-chk="orph">5 · Orphans</button>
      <button class="btn accent small" data-chk="all">Run all five</button>
    </div>
    <div class="checkout" id="checkOut"><div class="hit clean">Press a button.</div></div>
  </div>
  <div class="table-wrap"><table>
    <thead><tr><th>#</th><th>Check</th><th>Query</th><th>What a hit means</th></tr></thead>
    <tbody>
      <tr><td>1</td><td><strong>Duplicate business IDs</strong></td><td class="mono">SELECT asset_id, COUNT(*) FROM asset GROUP BY asset_id HAVING COUNT(*) &gt; 1</td><td>The identity policy was broken: either one thing was entered twice, or two things share a number. <em>The query cannot tell which.</em></td></tr>
      <tr><td>2</td><td><strong>Invalid categories</strong></td><td class="mono">SELECT * FROM asset WHERE asset_type NOT IN ('SL','DR','TR')</td><td>A value outside the allowed list: a typo, an old code, or a genuinely new category the schema lacks</td></tr>
      <tr><td>3</td><td><strong>Missing required observations</strong></td><td class="mono">SELECT * FROM inspection WHERE condition_code IS NULL</td><td>A visit happened but the score was not recorded — <em>not</em> the same as “condition is fine” (Chapter 8)</td></tr>
      <tr><td>4</td><td><strong>Unit / range problems</strong></td><td class="mono">SELECT * FROM asset WHERE pole_height_m &lt; 3 OR pole_height_m &gt; 15</td><td>Outside the range domain; usually a unit slipped in (feet as metres) or a decimal point moved</td></tr>
      <tr><td>5</td><td><strong>Orphaned related records</strong></td><td class="mono">SELECT i.* FROM inspection i LEFT JOIN asset a ON a.asset_id = i.asset_id WHERE a.asset_id IS NULL</td><td>A child row whose parent does not exist: a typo in the key, a deleted parent, or a parent never loaded</td></tr>
    </tbody></table></div>

  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">Reading check 4’s hit</h4><p><code>pole_height_m = 22</code> for SL-0127. Poles here are 3–15 m, so 22 is impossible. Typo for 2.2? For 12? Or 22 <em>feet</em>? The readme records the source value as <strong>22 ft = 6.7056 m</strong>. That is <em>evidence</em>: correct to <strong>6.71 m</strong> and state the rounding. Without that note the right action would be to blank the value with the reason “out of range; unit unknown” and ask the owner — not to divide by 3.28 on a hunch.</p></div>
    <div class="card"><h4 style="margin-top:0">Reading check 5’s hit</h4><p>INS-0006 refers to <code>DR-0044</code>; no such asset. DR-0043 exists and was visited by the same team 50 minutes later the same morning. The temptation is obvious: “DR-0044 is a typo for DR-0043.” <strong>Resist it.</strong> If it were DR-0043, the team would have scored one drain <em>4</em> and then <em>3</em> within an hour — a contradiction that needs explaining, not confirming. Only the paper form can settle it. Until then INS-0006 stays an orphan, <em>kept</em>, listed as unresolved, and left out of any “current condition” figure. Deleting it destroys an observation; re-keying it invents one.</p></div>
  </div>
  <div class="callout dev"><span class="label">Developer view</span><p>These are the database constraints — <code>UNIQUE</code>, <code>CHECK (x IN …)</code>, <code>NOT NULL</code>, <code>CHECK (x BETWEEN …)</code>, <code>FOREIGN KEY</code> — run <em>after the fact</em> on data that arrived without them. <strong>Where the comparison breaks:</strong> a database rejects the row; a reviewer must decide whether the row, the rule, or the reference is wrong. “Lamp” fails the domain — but the domain may be the thing that is incomplete.</p></div>
  <div class="callout note"><span class="label">Platform note — finding exact duplicates by shape</span><p>ArcGIS Pro’s <strong>Find Identical</strong> tool reports records with identical values in the fields you list; include <em>Shape</em> and it compares geometry within an XY tolerance, giving identical records the same <code>FEAT_SEQ</code> number. It finds features that sit on top of each other; it cannot tell you whether they are one fact or two — that is 9.6.2.</p></div>

  <h2><span class="mod">9.6.2</span>Two complaints at one place — or one complaint twice?</h2>
  <p>Complaints are <em>incidents</em>: five people reporting one broken light are five legitimate rows. So “two complaints at the same coordinates” is <strong>not</strong> a duplicate by itself. You need evidence about the <em>reports</em>, not their distance.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Compare the pairs</h3>
    <div class="opbtns"><button class="btn small" id="pairA" aria-pressed="true">P3 and P8 — both at (1200, 250)</button><button class="btn small" id="pairB">P5 and P9 — both at (1000, 500)</button></div>
    <div class="cmp" id="cmp"></div>
    <div class="status-line q" id="cmpMsg" style="margin-top:.8rem"></div>
  </div>
  <p><strong>The order of evidence</strong> for “same report?”: same reporter → same problem text → same time window → same channel → and only <em>then</em> location. Location comes last because every capture method can put two different reports at one coordinate (a geocoder returns the same street point for every “near the temple”; snapping pulls nearby clicks together) — and can put one report at two coordinates (a phone drifts between the first and second tap).</p>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“Complaints within 5 m of each other are duplicates — merge them.”</em> P5 (drain) and P9 (light) get merged; the light is never fixed; the P9 caller is told “already logged”. Distance is a prompt to <em>look</em>, never the decision.</p></div>

  <h2><span class="mod">9.6.3</span>The defect log</h2>
  <p>Every finding — shape, value, relationship, and every false alarm you examined — goes into one log with these columns. It is what lets a second person check you and lets the data owner answer your questions.</p>
  <div class="table-wrap"><table class="logtable">
    <thead><tr><th>Column</th><th>Content</th><th>Example (SL-0127 height)</th></tr></thead>
    <tbody>
      <tr><td><strong>ID</strong></td><td>Sequential number</td><td class="mono">D-06</td></tr>
      <tr><td><strong>Symptom</strong></td><td>What the check reported, naming the check and the engine/query</td><td>pole_height_m = 22 fails range 3–15 (check 4)</td></tr>
      <tr><td><strong>Affected records</strong></td><td>Business IDs, never row numbers</td><td class="mono">SL-0127</td></tr>
      <tr><td><strong>Likely cause</strong></td><td>Your hypothesis, labelled as one</td><td>Feet entered as metres at the Chapter 8 conversion</td></tr>
      <tr><td><strong>Evidence</strong></td><td>What supports the cause and the fix</td><td>Readme: source value 22 ft; 22 × 0.3048 = 6.7056</td></tr>
      <tr><td><strong>Proposed correction</strong></td><td>Exactly what changes, before → after; or “none — exception”; or “none — needs owner”</td><td>22 → 6.71 (rounded to 0.01 m)</td></tr>
      <tr><td><strong>Reviewer decision</strong></td><td>Accepted / rejected / deferred, by whom, when</td><td>Accepted, instructor, 2026-09-19</td></tr>
    </tbody></table></div>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Write the log row for INS-0006</h3>
    <p>Fill the seven columns for the orphan inspection. Leave the decision as “deferred”. Saved in this browser; compare with the model afterwards.</p>
    <div class="lab-form">
      <label>Symptom</label><input type="text" data-save="log6-sym" placeholder="What did check 5 report?">
      <label>Affected records</label><input type="text" data-save="log6-rec" placeholder="Which ID?">
      <label>Likely cause (a hypothesis)</label><input type="text" data-save="log6-cause">
      <label>Evidence</label><input type="text" data-save="log6-ev" placeholder="What do you have — and what do you NOT have?">
      <label>Proposed correction</label><input type="text" data-save="log6-fix">
    </div>
    <details class="reveal" style="margin-top:.8rem"><summary>Compare with a model row</summary>
      <p><strong>D-07</strong> · <em>Symptom:</em> inspection.asset_id = DR-0044 matches no asset (check 5, LEFT JOIN on asset). · <em>Affected:</em> INS-0006. · <em>Likely cause:</em> mis-typed asset number on the paper form (DR-0043 was visited by the same team 50 min later) — <strong>hypothesis only</strong>. · <em>Evidence:</em> none in the package; the paper form is needed; DR-0043’s own visit scores 3 while INS-0006 scores 4, so re-keying would create a contradiction. · <em>Correction:</em> none — keep; exclude from condition statistics; ask the owner for the form. · <em>Decision:</em> deferred.</p>
    </details>
  </div>
  <p>Two habits make the log useful: <strong>false alarms get rows too</strong> (“R5 end reported as dangle; register says dead end; no correction; exception”), so the next reviewer does not re-investigate; and <strong>the proposed correction is written before it is made</strong>, so the before/after record in 9.7 can be checked against the intention.</p>

  <div class="quiz" data-answer="0" data-fb="Check 1 says two rows share a key. It cannot say whether they are one thing entered twice (mark one as a duplicate) or two things wrongly numbered (renumber one under the owner’s policy). That needs the other columns — and usually the owner.">
    <div class="q">Check 1 reports <code>SL-0114</code> twice — one row at (260, 190) from 2024, one at (1250, 420) from 2025, about a kilometre apart. What can you conclude from the check alone?</div>
    <div class="opts">
      <button class="opt">Only that two rows share a key; whether to mark one as a duplicate or renumber one needs other evidence and the owner.</button>
      <button class="opt">The 2025 row is the wrong one — delete it.</button>
      <button class="opt">They are the same light, so merge the rows.</button>
      <button class="opt">Renumber the second row SL-0115 and move on.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const out = document.getElementById("checkOut"), R = runChecks();
  const blocks = {
    dup: `<div class="hit"><strong>1 · Duplicate IDs</strong> → <code>${R.dup.join(", ")}</code> appears twice: (260, 190) DIGITISED 2024 and (1250, 420) GNSS 2025 — about 1,016 m apart. They cannot be one light. Which one is <em>the</em> SL-0114 is the owner’s decision. <strong>Needs owner.</strong></div>`,
    cat: `<div class="hit"><strong>2 · Invalid categories</strong> → asset_type: <code>${R.badType.join(", ")}</code>. Request status: ${R.badStatus.length ? R.badStatus.join(", ") : "none"}. “Lamp” is not in {SL, DR, TR}. The readme says its type was Streetlight and its ID starts SL-. <strong>Correct to SL — with that evidence.</strong></div>`,
    miss: `<div class="hit"><strong>3 · Missing observations</strong> → <code>${R.missing.join(", ")}</code> has no condition score (remark: “pole leaning; condition not scored”). The visit happened; the score was not recorded. <strong>Stays blank. Never invent a 3.</strong></div>`,
    range: `<div class="hit"><strong>4 · Unit / range</strong> → <code>${R.range.join(", ")}</code>: 22 is outside 3–15 m. Readme: source value 22 ft = 6.7056 m. <strong>Correct to 6.71 m, rounding stated.</strong> (SL-0113’s blank height is <em>not</em> a hit: the readme says it was never measured.)</div>`,
    orph: `<div class="hit"><strong>5 · Orphans</strong> → <code>${R.orphans.join(", ")}</code>: no asset DR-0044. Team IDs: ${R.orphanTeams.length ? R.orphanTeams.join(", ") : "all resolve"}. <strong>Keep the row; needs the paper form.</strong> Do not change it to DR-0043.</div>`
  };
  document.querySelectorAll("[data-chk]").forEach(b => b.addEventListener("click", () => { const k = b.dataset.chk; out.innerHTML = k === "all" ? Object.values(blocks).join("") : blocks[k]; }));

  const cmp = document.getElementById("cmp"), cm = document.getElementById("cmpMsg");
  const row = (q, cls) => `<div class="side ${cls}"><h5>${q.id}</h5><div class="record"><div class="kv"><b>where</b><span>(${q.x}, ${q.y})</span><b>problem</b><span>${q.cat}</span><b>reporter</b><span>${q.who}</span><b>when</b><span>${q.on} ${q.time} IST</span><b>via</b><span>${q.via}</span><b>note</b><span>${q.note || "—"}</span></div></div>`;
  const byId = id => T9.requests.find(q => q.id === id);
  function pair(b) {
    document.getElementById("pairA").setAttribute("aria-pressed", !b); document.getElementById("pairB").setAttribute("aria-pressed", b);
    if (!b) { cmp.innerHTML = row(byId("P3"), "dup") + row(byId("P8"), "dup"); cm.className = "status-line bad"; cm.textContent = "One report, sent twice (a double tap, 60 seconds apart): same reporter, same text, same minute, same channel. Action: mark P8 as ‘Closed - duplicate’ with note ‘Duplicate of P3’ — and KEEP the row. The double submission is itself a fact about the app."; }
    else { cmp.innerHTML = row(byId("P5"), "two") + row(byId("P9"), "two"); cm.className = "status-line ok"; cm.textContent = "Two reports. Same spot, different problems, different people, two weeks apart. The identical coordinates (both exactly on the ward line) are a capture clue worth noting — not a reason to merge. Action: none, beyond logging that you looked."; }
  }
  document.getElementById("pairA").addEventListener("click", () => pair(false)); document.getElementById("pairB").addEventListener("click", () => pair(true)); pair(false);
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
