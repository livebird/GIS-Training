<?php $page = ['title' => '8.10 Recap, media brief and what comes next', 'chapter' => 8, 'module' => '8.10']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 8.10 · Recap, media and transition</div>
    <h1>What to carry forward</h1>
    <p class="lead">Seven ideas, one worked example, and a design that Chapter 9 will fill with real, imperfect data.</p>
  </div>

  <h2><span class="mod">Recap</span>Seven things to remember</h2>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">1. Different kinds of thing, different tables</h4><p>Assets, inspections, requests, wards and teams have different <strong>grains</strong>. Only some have their own shape; the others point to them. Every field is justified by a question, or it goes.</p></div>
    <div class="card"><h4 style="margin-top:0">2. A shape needs a six-part sentence</h4><p>Type, multipart rule, grid and units, Z, <em>what the dot stands for</em>, and how it was captured. A pole base, a lamp head and “where the citizen stood” are three different points.</p></div>
    <div class="card"><h4 style="margin-top:0">3. Describe every field; say what a blank means</h4><p>Seven-part dictionary rows. Unknown, not applicable, zero and empty text are four facts. Codes are text. Dates need a written rule: IST in the notebook, UTC in storage — and hosted layers store UTC whether you like it or not.</p></div>
    <div class="card"><h4 style="margin-top:0">4. Three “names”, one key</h4><p>Business ID (<code>SL-0113</code>), display label, storage row number. ObjectIDs are per copy and can even be run-time numbers; GlobalIDs are regenerated on Append by default and do not switch sync on by themselves. Related records carry the business ID.</p></div>
    <div class="card"><h4 style="margin-top:0">5. Keys, and what enforces them</h4><p>1:M puts the key on the many side; 1:1 makes it unique; M:N needs a junction table. A diagram, a join, a QGIS relation, an ArcGIS relationship class and a database foreign key enforce different things against different people.</p></div>
    <div class="card"><h4 style="margin-top:0">6. Store the code, show the description</h4><p>Coded lists and ranges. A default is not an observation — blank stays meaningful for anything observed. Each rule lives in the schema, the app, or a review, and the storage format decides which schema rules even exist.</p></div>
    <div class="card"><h4 style="margin-top:0">7. History, two clocks, photos as rows, orphans, a checklist</h4><p>Current state is a derived cache; history is the inspection table; visit time ≠ typing time; one row per photo; the left-join orphan query; a named owner and eight questions before any change.</p></div>
    <div class="card"><h4 style="margin-top:0">The practice numbers</h4><p>3 assets, 4 inspections, 2 teams; INS-0004 visited 14 Sep 2025 but typed 16 Mar 2026; 10:42 IST = 05:12 UTC; P1 and SL-0113 are √50 ≈ 7.07 m apart; the lab table has 8 assets, 11 (+1) observations, 6 photos, 2 teams; 22 ft = 6.7056 m.</p></div>
  </div>

  <h2><span class="mod">Media brief</span>What the slides, audio and diagrams should show</h2>
  <p>A summary for whoever builds the presentation and audio lesson; the full specifications are in the Media Appendix of the chapter document.</p>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">D1 — Entity–relationship diagram</h4><p>Eight boxes (Asset, Ward and Request with a shape; the rest plain), crow’s-foot ends, key names on every line, the optional Asset–Request link dashed. Caption: “instructional design; not a mandated Esri schema”. (You used it live in 8.5.)</p></div>
    <div class="card"><h4 style="margin-top:0">D2 — Field-definition cards</h4><p>One card per Inspection field with seven labelled lines; the blank-rule line set larger. The <code>defects_found</code> card shows “blank = checklist not completed; 0 = completed, none found” in two colours.</p></div>
    <div class="card"><h4 style="margin-top:0">D3 — Before / after record example</h4><p>Left: row 1 of the bad table with the March values “overwriting” the September values (crossed out). Right: the SL-0113 asset row above two inspection rows (INS-0004, INS-0003) and one photo row, each joined by <code>asset_id</code>. Times shown as “10:42 IST (05:12 UTC)”. (You built it live in 8.7.)</p></div>
    <div class="card"><h4 style="margin-top:0">One animation, no 3D</h4><p>Rows of the bad table move into their own tables while a highlighted key stays attached; the counter must end at Assets 8, Inspections 11 (+1 placeholder), Photos 6, Teams 2. No 3D scene: nothing here is spatial in a way a 3D scene would clarify.</p></div>
  </div>
  <div class="callout note"><span class="label">Narration rules for the audio lesson</span><p>Tell the lifecycle of SL-0113: installed 2018 → visit, all fine (Sep 2025) → visit, lamp flickers (Mar 2026) → the paper form is found and typed in → decommissioned one day, marked REMOVED, never deleted. Say “asset”, “inspection” and “request” as different words; never read a time without its zone; pause before each answer.</p></div>

  <h2><span class="mod">Sources</span>Where the facts came from</h2>
  <p>Primary readings for this chapter: the QGIS <em>Gentle Introduction</em> page on vector attribute data; Esri’s introduction to the geodatabase and its overview of attribute domains. Product behaviour was read on 19 September 2026 from ArcGIS Pro 3.7 documentation (field data types, unique identifier fields, Add Global IDs, Preserve Global IDs, relationship class fundamentals and types, attributed relationship classes, data relationship options, attachments, editor tracking, field-property modification, feature class basics, attribute rules), ArcGIS Online help (date and time fields; hosted feature layer editing), ArcGIS Enterprise help (preparing data for feature services), the QGIS 3.40 User Guide (vector properties; joins and relations), PostgreSQL documentation (constraints; date/time types) and PostGIS documentation (spatial table management). The full reference list with links is in the chapter document. The schema and policies here are instructional designs for this course, not a mandated Esri schema.</p>

  <h2><span class="mod">Next</span>Chapter 9 — data capture, editing and quality</h2>
  <p>You now have a design that says what each row means, how rows are named, and how they connect. Chapter 9 puts real, imperfect data into it: how locations are captured, how editing and snapping work, what makes a shape valid, and how to find and fix attribute and relationship problems while leaving an audit trail. <strong>Keep your 8.8 design and your orphan query</strong> — they are the first two tools you will use there.</p>
  <p><a class="btn primary" href="./">← Chapter home</a> &nbsp; <a class="btn ghost" href="glossary">Glossary</a></p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<?php require __DIR__ . '/../partials/end.php'; ?>
