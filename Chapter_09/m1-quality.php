<?php $page = ['title' => '9.1 Define quality for the intended use', 'chapter' => 9, 'module' => '9.1']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 9.1 · General idea</div>
    <h1>“Is this data good?” is not a question. “Good <em>for what</em>?” is.</h1>
    <p class="lead">A dataset does not have one quality score. It has five separate report cards — and the same dataset can pass four of them and fail the one that matters for your decision. Before you edit anything, you also fill a short intake checklist, because an edit is the one thing in this course that changes the truth.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Ask five separate questions of any dataset: <strong>position</strong>, <strong>values</strong>, <strong>completeness</strong>, <strong>rules</strong>, <strong>freshness</strong>.</li>
      <li>Judge a dataset against a <em>stated</em> decision — an overview map vs. digging up a drain.</li>
      <li>Fill the six-item intake checklist and understand why “keep the original” is your only real undo.</li></ul></div>
  </div>

  <h2><span class="mod">9.1.1</span>Five separate questions</h2>
  <p>Think of a service request (a complaint) table for a city. Each question below has a <em>different cure</em>. If you mix them into one score you lose the information about what to do next.</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Question</th><th>Plain meaning</th><th>Example of a failure</th><th>How you find out</th></tr></thead>
    <tbody>
      <tr><td><strong>Positional accuracy</strong></td><td>How far is the stored dot from the real thing?</td><td>A drain stored 6 m from its grating, because the point was taken with a phone in a narrow lane</td><td>Compare a sample against a better measurement; read how it was captured (9.2)</td></tr>
      <tr><td><strong>Attribute correctness</strong></td><td>Are the stored values true?</td><td>Asset type says “Lamp”; pole height says 22 for a 6.7 m pole</td><td>Compare with the paper form, a photo, a site visit; check against allowed values</td></tr>
      <tr><td><strong>Completeness</strong></td><td>Is everything there that should be — and nothing extra?</td><td>A visit with no condition score; an inspection of an asset that does not exist; a complaint with no location</td><td>Count against what you expect; look for blanks in must-have columns; run the orphan check</td></tr>
      <tr><td><strong>Logical consistency</strong></td><td>Do records follow the rules they are supposed to follow?</td><td>A ward shape that crosses itself; two wards with a thin gap; a lane that stops 3 m before the road; two rows with the same ID</td><td>Validity checks, topology rules, uniqueness and domain queries (9.5, 9.6)</td></tr>
      <tr><td><strong>Currency</strong></td><td>Is it recent enough for this decision?</td><td>A 2019 ward map used for 2026 complaints; a 2024 inspection used as “current condition”</td><td>Compare the data’s “as of” date with the decision’s date</td></tr>
    </tbody></table></div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Which report card fails?</h3>
    <p>Pick a situation from our practice town. The five cards light up: <span class="pill" style="background:var(--warn-soft)">red = fails</span> <span class="pill" style="background:var(--ok-soft)">green = passes</span> <span class="pill" style="background:#fbf3d8">yellow = cannot tell from this alone</span>.</p>
    <div class="controls"><label>Situation <select id="sit">
      <option value="0">Inspection INS-0004 happened in Sept 2025 but was typed in six months later (Mar 2026)</option>
      <option value="1">Ward C was typed with its corners in the wrong order and crosses itself</option>
      <option value="2">SL-0127’s pole height is stored as 22 (it was 22 feet on the paper form)</option>
      <option value="3">A drain point was captured with a phone under trees; reported accuracy 15 m</option>
      <option value="4">The ward map is from 2019; two wards were merged in 2024</option>
      <option value="5">Inspection INS-0006 refers to asset DR-0044, which is not in the asset table</option>
    </select></label></div>
    <div class="qcards" id="qcards"></div>
    <div class="result" id="sitWhy"></div>
  </div>

  <div class="callout dev"><span class="label">Developer view</span><p>The five questions are five test suites on a data release: an accuracy suite, a schema-validation suite, a completeness suite, an integrity suite and a freshness check. Failing one suite still blocks the release, and it tells you <em>which team to call</em>. <strong>Where the comparison breaks:</strong> there is no unit test for positional accuracy. You only learn how far a stored dot is from the real grating by going and measuring again — so accuracy is usually <em>estimated from a sample</em> and <em>described</em>, never “tested to pass”.</p></div>

  <h2><span class="mod">9.1.2</span>Good enough for <em>this</em> decision?</h2>
  <p>Never invent an accuracy number (“must be within 3 m”) out of the air. Instead, compare what the decision <em>needs</em> with what the data’s history says it <em>delivers</em>. Same asset row, two decisions, two answers:</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>One drain row, two uses</h3>
    <p>Drain <strong>DR-0042</strong> is stored at (995, 510), type DR, last inspected 2025-11-02, captured by <em>digitising from a 1:5,000 plan</em>. Choose what the municipality wants to do with it.</p>
    <div class="opbtns">
      <button class="btn small" id="useA" aria-pressed="true">Use 1 — council map: which ward has the most open complaints?</button>
      <button class="btn small" id="useB">Use 2 — excavation: where exactly do we dig to reach this drain?</button>
    </div>
    <div class="grid-2">
      <div class="reccard" id="useCard"></div>
      <div class="status-line q" id="useVerdict"></div>
    </div>
  </div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“The data was collected by professionals, so it is accurate.”</em> “Collected by professionals” is not a capture method. Ask <em>how</em>, <em>with what</em>, <em>when</em>, and <em>what accuracy was reported with each point</em> (9.2). Otherwise the digging crew is 6 m from the drain.</p></div>

  <h2><span class="mod">9.1.3</span>The intake checklist — fill it before you edit</h2>
  <p>Chapter 7 gave you an intake form for a <em>conversion</em>. Editing needs the same discipline, because saving an edit rewrites the source. Six items, always:</p>
  <div class="table-wrap"><table>
    <thead><tr><th>#</th><th>Item</th><th>What to write</th><th>Why it matters here</th></tr></thead>
    <tbody>
      <tr><td>1</td><td><strong>Source</strong></td><td>Who sent it, from what system, and what the readme says about how each layer was made</td><td>Decides what evidence a correction can rest on, and who the “source owner” is when you cannot decide</td></tr>
      <tr><td>2</td><td><strong>Date</strong></td><td>The “as of” date of each layer and of the readme</td><td>Freshness; tells you whether a “defect” may be a real later change</td></tr>
      <tr><td>3</td><td><strong>Reference</strong></td><td>Coordinate system, units, axis order — or for our grid: “flat, metres, no CRS”</td><td>Every tolerance you set later is in these units</td></tr>
      <tr><td>4</td><td><strong>Schema</strong></td><td>The data dictionary and allowed values (Chapter 8)</td><td>Without the list of allowed types, “Lamp” is just a word</td></tr>
      <tr><td>5</td><td><strong>Original copy</strong></td><td>Path of the untouched original, kept read-only</td><td>Your only undo after saving; every before/after compares to it</td></tr>
      <tr><td>6</td><td><strong>Intended output</strong></td><td>What the corrected copy is for, and which checks it must pass</td><td>Decides which problems block release and which can stay open</td></tr>
    </tbody></table></div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Fill the checklist for the Chapter 9 package</h3>
    <p>Use the readme on the chapter home page. Your answers are saved in this browser and you will reuse them in the lab (9.8). Click <em>Compare</em> to see a model answer.</p>
    <div class="lab-form">
      <label>1. Source</label><input type="text" data-save="ck-source" placeholder="Who sent it? Which earlier packages is it built from?">
      <label>2. Date</label><input type="text" data-save="ck-date" placeholder="As of when?">
      <label>3. Reference</label><input type="text" data-save="ck-ref" placeholder="Coordinate system, units, order">
      <label>4. Schema</label><input type="text" data-save="ck-schema" placeholder="Which dictionary and allowed values apply?">
      <label>5. Original copy</label><input type="text" data-save="ck-orig" placeholder="Where is the untouched copy and how is it protected?">
      <label>6. Intended output</label><input type="text" data-save="ck-out" placeholder="What is the corrected copy for?">
    </div>
    <details class="reveal" style="margin-top:.8rem"><summary>Compare with a model answer</summary>
      <ol>
        <li><strong>Source:</strong> instructor package <code>Chapter09_Damaged/</code>; wards/roads from the Chapter 3 town plus Ward C, R4, R5 digitised in Sept 2026 from the registers; assets/inspections from the Chapter 8 conversion; P8/P9 from the mobile app.</li>
        <li><strong>Date:</strong> readme dated September 2026; inspections up to 2026-09-05; complaints up to 2026-09-14.</li>
        <li><strong>Reference:</strong> flat training grid, metres, (x, y) with y upward, <em>no</em> coordinate system.</li>
        <li><strong>Schema:</strong> Chapter 8 dictionary; asset type ∈ {SL, DR, TR}; height 3–15 m; condition 1–5; location method ∈ {SURVEY, GNSS, DIGITISED, APPROX}.</li>
        <li><strong>Original copy:</strong> <code>Chapter09_Damaged/</code> kept read-only; all edits in <code>Chapter09_Work/</code>.</li>
        <li><strong>Intended output:</strong> <code>Chapter09_Corrected_v1/</code> for Chapter 10’s queries; must pass validity, uniqueness, domain and orphan checks, with open items listed.</li>
      </ol>
    </details>
  </div>

  <div class="callout note"><span class="label">Why “keep the original” is item 5 and not a footnote</span><p>Inside an editing session you can undo. Once you <strong>save</strong>, the edit is written to the file. ArcGIS Pro’s <em>Save</em> “saves all edits you made since the last time you saved”, and <em>Discard</em> only rolls back to the last save; QGIS’s <em>Save Layer Edits</em> and <em>Rollback</em> work the same way. After a save, the only undo is the copy you kept.</p></div>

  <div class="quiz" data-answer="2" data-fb="Delay is about currency (for six months the ‘latest visit’ was wrong) and completeness (a visit that happened was missing). It says nothing about where the pole is (position) or whether the inspector’s values were true (correctness); and an entry date after the event date is normal, so consistency is fine.">
    <div class="q">Inspection INS-0004 describes a visit on 14 Sept 2025 but was typed in on 16 March 2026 from a paper form found late. Which two of the five questions does the six-month delay affect?</div>
    <div class="opts">
      <button class="opt">Positional accuracy and attribute correctness</button>
      <button class="opt">Logical consistency and positional accuracy</button>
      <button class="opt">Currency and completeness</button>
      <button class="opt">All five equally</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const Q = ["Positional accuracy", "Attribute correctness", "Completeness", "Logical consistency", "Currency"];
  // status per situation: F fail, P pass, U unknown
  const S = [
    { s: "PPFPF", why: "Currency fails (for six months the latest visit was wrong) and completeness fails (a real visit was missing). Position and the recorded values are unaffected; an entry date after the event date is normal." },
    { s: "UUPFU", why: "Logical consistency fails: the shape breaks the validity rule (a ring must not cross itself). Whether the ward is complete is fine (one row exists); position and values cannot be judged until the shape is fixed." },
    { s: "PFPFP", why: "Attribute correctness fails (22 is not the height in metres) and logical consistency fails (22 is outside the allowed 3–15 m range — the rule caught it). The position and freshness are unaffected." },
    { s: "FPPPP", why: "Positional accuracy fails for any precise use — but only if we read the reported 15 m. Nothing else is wrong with the row; that is why the accuracy must be stored per point." },
    { s: "PUFPF", why: "Currency fails (2024 merger not reflected). Completeness fails in the sense that the map has one boundary too many for 2026. Names and codes may be correct — unknown." },
    { s: "UPFFP", why: "Completeness fails (a parent record is missing — or the child points at the wrong one) and logical consistency fails (the orphan rule). The values on the row may be perfectly true, so correctness passes on its own; position is not about a table row." }
  ];
  const cards = document.getElementById("qcards"), why = document.getElementById("sitWhy");
  function showSit(i) {
    cards.innerHTML = Q.map((q, k) => { const c = S[i].s[k]; return `<div class="qcard ${c === "F" ? "fail" : c === "P" ? "pass" : "unknown"}"><h5>${q}</h5>${c === "F" ? "Fails" : c === "P" ? "Passes" : "Cannot tell"}</div>`; }).join("");
    why.textContent = S[i].why;
  }
  document.getElementById("sit").addEventListener("change", e => showSit(+e.target.value)); showSit(0);

  const card = document.getElementById("useCard"), verdict = document.getElementById("useVerdict");
  function use(b) {
    document.getElementById("useA").setAttribute("aria-pressed", !b); document.getElementById("useB").setAttribute("aria-pressed", b);
    card.innerHTML = `<div class="hdr">Asset row — DR-0042 (drain)</div>
      <div class="row ${b ? "changed" : "same"}"><b>position</b><span class="v">(995, 510) — digitised from a 1:5,000 plan</span></div>
      <div class="row ${b ? "changed" : "same"}"><b>what the dot means</b><span class="v">not stated (grating? chamber? connection?)</span></div>
      <div class="row same"><b>type / ward</b><span class="v">DR · strictly inside Ward A (995 &lt; 1000)</span></div>
      <div class="row ${b ? "" : "same"}"><b>last inspected</b><span class="v">2025-11-02, condition 2 (Poor)</span></div>`;
    verdict.className = "status-line " + (b ? "bad" : "ok");
    verdict.textContent = b ? "NOT adequate. Digging needs a position whose stated doubt is small compared with the trench, and a statement of what the dot stands for. A point traced from a 1:5,000 paper plan has neither. Get a survey or a corrected-GNSS observation first." : "Adequate. Counting complaints per ward only needs the right ward and current status values. Being 5 m out still leaves the drain in Ward A — unless it sits on a boundary (P5 is the one to watch).";
  }
  document.getElementById("useA").addEventListener("click", () => use(false)); document.getElementById("useB").addEventListener("click", () => use(true)); use(false);
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
