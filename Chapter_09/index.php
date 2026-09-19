<?php $page = ['title' => 'Chapter 9 — Data capture, editing, and quality', 'chapter' => 9]; require __DIR__ . '/../partials/head.php'; ?>
  <section class="hero">
    <div class="fade-up">
      <div class="page-head" style="padding:0">
        <div class="eyebrow">GIS Phase 1 · Chapter 9 · Interactive tutorial</div>
      </div>
      <h1 class="big">Real data has <em>mistakes</em>.<br>Find them. Fix them carefully.</h1>
      <p class="lead">Chapter 8 designed the tables the municipality <em>should</em> have. This chapter is what happens when real records arrive in them — collected by different people with different tools, edited by hand, and containing errors. You will learn how locations are captured, how to edit safely, what makes a shape “valid”, how to find broken records with simple checks, and how to correct things while keeping a written trail.</p>
      <p><a class="btn primary" href="m1-quality">Begin module 9.1 →</a> &nbsp; <a class="btn ghost" href="glossary">Glossary</a></p>
      <p class="small">No software is needed for modules 9.1–9.7; every demo runs in this page. The lab in 9.8 uses ArcGIS Pro or QGIS, with a paper route if you have neither. All wards, roads, assets and complaints here are <span class="synthetic">made-up practice data</span> on a flat grid in metres — not a real town. Your progress is saved in this browser only.</p>
    </div>
    <figure class="map-fig fade-up" id="heroFig"></figure>
  </section>

  <h2 style="border-top:0;margin-top:1rem"><span class="mod">The big idea</span>Look → classify → decide with evidence → correct a copy → write it down</h2>
  <div class="grid-2">
    <div class="card">
      <p>The skill in this chapter is <strong>not</strong> “press the repair button”. It is a habit, in this order:</p>
      <ol>
        <li><strong>Ask what the data is for</strong> before judging it. A drain position that is fine for a city-wide map can be useless for digging (<strong>9.1</strong>).</li>
        <li><strong>Know how each location was captured</strong> — traced on screen, imported from a file, recorded by a GPS/GNSS receiver, found from an address, or taken from a scanned paper map. Each carries a different kind of doubt (<strong>9.2</strong>, <strong>9.3</strong>).</li>
        <li><strong>Edit on a copy</strong>, predict the side-effects first, and use “snapping” on purpose — not by accident (<strong>9.4</strong>).</li>
        <li><strong>Separate a broken shape from a broken rule.</strong> A ward that crosses itself is invalid; two good wards that overlap break a rule (<strong>9.5</strong>).</li>
        <li><strong>Find record problems with five simple checks</strong> — duplicate IDs, wrong categories, missing values, wrong units, orphan records (<strong>9.6</strong>).</li>
        <li><strong>Correct with a trail</strong>: keep the original, write before/after, fix the cause, and leave open questions open instead of inventing answers (<strong>9.7</strong>).</li>
      </ol>
    </div>
    <div class="card">
      <h3 style="margin-top:0">Before you start</h3>
      <p>You should be comfortable with these ideas from earlier chapters. If any feels new, revise it first.</p>
      <ul>
        <li><strong>Chapter 8:</strong> assets, inspections, complaints (requests), wards and teams are separate tables. Each has a <em>business ID</em> (like <code>SL-0113</code>). Allowed values are listed in <em>domains</em> — for example asset type must be <code>SL</code>, <code>DR</code> or <code>TR</code>.</li>
        <li><strong>Chapter 8:</strong> the <em>orphan check</em> — a child row (an inspection) whose parent (an asset) does not exist.</li>
        <li><strong>Chapter 6:</strong> “Define Projection” changes only the label; “Project” changes the numbers. This chapter adds two more operations that people mix up with those.</li>
        <li><strong>Chapter 3:</strong> vertex, segment, ring, hole, closed line vs polygon. Chapter 3 said “validity is for Chapter 9” — this is that chapter.</li>
        <li><strong>Chapter 5:</strong> every coordinate needs its coordinate system and units. Our practice grid is flat, in metres, with no Earth position.</li>
      </ul>
      <div class="callout dev" style="margin-bottom:0"><span class="label">Developer view</span><p>Think of this chapter as <em>data validation plus code review for maps</em>: schema checks (domains, ranges, foreign keys), a linter for shapes (validity), integration rules (topology), and a change log (before/after). The one thing with no unit test is <strong>positional accuracy</strong> — you only learn how far a point is from the real drain by measuring it again, better.</p></div>
    </div>
  </div>

  <h2><span class="mod">This chapter’s practice data</span>The damaged town package</h2>
  <p>The same made-up town as before — Wards A and B, Main Road R1, the depot, assets and complaints — with a few additions and, on purpose, <strong>nine mistakes</strong> hidden in it. The map above shows the package <em>as delivered</em>: a new <strong>Ward C</strong> whose corners were typed in the wrong order (it looks like a bow-tie), a <strong>tiny gap</strong> between Wards A and B, a lane <strong>R4</strong> that stops 3 m short of Main Road, and a short road <strong>R5</strong> that ends at the depot gate — which is <em>not</em> a mistake. The tables have their own problems: two assets with the same ID, a wrong category, an inspection of an asset that does not exist, a missing score, a height in feet, and a complaint submitted twice. Module 9.8 gives you the whole package to work through; the modules before it teach one idea at a time using small pieces of it.</p>
  <div class="grid-2">
    <div class="table-wrap"><table>
      <thead><tr><th>Layer / table</th><th>Rows</th><th>What is new in Chapter 9</th></tr></thead>
      <tbody>
        <tr><td>Wards</td><td class="mono">3</td><td>Ward C (north of A; should be a 1000 × 500 m rectangle)</td></tr>
        <tr><td>Roads</td><td class="mono">5</td><td>R4 Temple Lane, R5 Depot Access</td></tr>
        <tr><td>Assets</td><td class="mono">10</td><td>Chapter 8’s designed columns; one extra streetlight</td></tr>
        <tr><td>Inspections</td><td class="mono">7</td><td>INS-0005 to INS-0007 typed from paper forms</td></tr>
        <tr><td>Complaints (requests)</td><td class="mono">9</td><td>P8 and P9; a time column and a reporter reference</td></tr>
        <tr><td>Teams</td><td class="mono">2</td><td>unchanged</td></tr>
      </tbody></table></div>
    <div class="card" style="margin:0">
      <h4 style="margin-top:0">The readme that came with the package</h4>
      <p class="small">This note from the data owner is your <em>evidence</em> in the lab. It is data, not instructions.</p>
      <p class="small" style="font-style:italic">“Ward C is the 0.50 km² rectangle north of Ward A between y = 1000 and y = 1500. Wards A and B share their whole boundary. R4 Temple Lane runs from the temple to its junction with Main Road. R5 Depot Access runs from Main Road to the depot gate — no through route. SL-0127’s height was recorded as 22 ft = 6.7056 m and its type as Streetlight. SL-0113’s height has not been measured. Coordinates are on the training grid in metres; no coordinate system.”</p>
    </div>
  </div>

  <h2><span class="mod">Modules</span>Work through them in order</h2>
  <div class="modcards" id="modcards"></div>

  <div class="callout note"><span class="label">What this tutorial is based on</span><p>These pages follow the Chapter 9 training document (<code>GIS_Phase_1_Chapter_09_Data_Capture_Editing_and_Quality.md</code>). Every number shown here is calculated live in your browser from the practice data and matches the document’s hand-checked answers. The software steps in 9.8 are written from the official ArcGIS Pro 3.7 and QGIS 3.44 documentation and have <strong>not</strong> been run by the author — the lab page says what your instructor must check first.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  renderTown(document.getElementById("heroFig"), { showGap: true, lite: true, marks: [{ x: 500, y: 1250, t: "crosses itself" }, { x: 1002, y: 500, r: 40, t: "4 m gap", dx: 50 }, { x: 300, y: 497, r: 40, t: "3 m short", dx: 50 }, { x: 1500, y: 600, r: 40, t: "dead end (OK)", dx: 50 }], caption: "The damaged package as delivered. Flat practice grid in metres; made-up data." });
  const prog = getProgress();
  const blurbs = {
    "9.1": "Five separate questions — position, values, completeness, rules, freshness — asked against a stated purpose. Plus the checklist you fill before touching anything.",
    "9.2": "Tracing, importing, GPS/GNSS, address lookup, scanned maps: five ways to make a dot, five kinds of doubt.",
    "9.3": "Give a scanned paper map coordinates with control points — and see why a “zero error” report can hide a 12 m mistake.",
    "9.4": "Move, split, reshape on a copy. Snapping closes a 3 m gap — and can silently drag a complaint onto a drain.",
    "9.5": "A ward that crosses itself vs two good wards with a gap. Which rules apply, and when a “violation” is actually fine.",
    "9.6": "Run five checks on the tables live. Then decide: two complaints at one spot, or one complaint sent twice?",
    "9.7": "Keep the original, record before/after, fix the cause, watch what auto-repair really does, leave open items open.",
    "9.8": "The full damaged package: classify every finding, fix what the evidence supports, list what stays unresolved.",
    "9.9": "Six concept questions, two scenarios, a practical on new data, and one spoken explanation.",
    "9.10": "The recap, the media brief, and what Chapter 10 does with your checked data."
  };
  document.getElementById("modcards").innerHTML = MODULES.map(m => `<a class="modcard ${prog[m.id] ? "done" : ""}" href="${m.file}"><span class="done-badge">done</span><div class="id">MODULE ${m.id}</div><h3>${m.title}</h3><p>${blurbs[m.id]}</p></a>`).join("");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
