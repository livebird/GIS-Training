<?php $page = ['title' => '8.7 History, photos, and changing the design', 'chapter' => 8, 'module' => '8.7']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 8.7 · General idea, with ArcGIS attachments and editor tracking</div>
    <h1>Keep the history, store photos as rows, find the orphans, change the design carefully</h1>
    <p class="lead">The asset row answers “what is true now?”. The inspection table answers “what was seen, and when?”. This module keeps both, adds a second clock (when was it <em>typed</em>?), puts photographs where they belong, shows the one query that finds broken links, and ends with the checklist you fill in before changing any table that other people depend on.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Cache “current state” on the asset as a <em>derived</em> value with a written refresh rule, while history stays in the inspection table.</li>
      <li>Keep <strong>visit time</strong> and <strong>typing time</strong> as two fields, and see why editor tracking cannot replace the first.</li>
      <li>Store photos as one row per file; write the orphan query and run it by hand.</li>
      <li>Name a schema owner and fill the eight-question migration checklist.</li></ul></div>
  </div>

  <h2><span class="mod">8.7.1</span>Current state versus history; visit time versus typing time</h2>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Record visits to SL-0113 and watch both tables</h3>
    <p>Start from the pole’s installation. Press the buttons in order. The left card is the <strong>asset row</strong> (current state, with cached fields). The right table is the <strong>inspection history</strong>.</p>
    <div class="opbtns">
      <button class="btn small" id="h0" aria-pressed="true">2018: installed</button>
      <button class="btn small" id="h1">Sep 2025: visit, all fine</button>
      <button class="btn small" id="h2">Mar 2026: visit, lamp flickers</button>
      <button class="btn small" id="h3">16 Mar 2026: the Sep 2025 paper form is found and typed in</button>
    </div>
    <div class="hist">
      <div class="reccard" id="hCard">
        <div class="hdr">Asset row — SL-0113</div>
        <div class="row"><b>asset_id</b><span class="v">SL-0113</span></div>
        <div class="row"><b>install_year</b><span class="v">2018</span></div>
        <div class="row" id="hLast"><b>last_inspected_at (derived)</b><span class="v">—</span></div>
        <div class="row" id="hCond"><b>last_condition (derived)</b><span class="v">—</span></div>
        <div class="row" id="hDef"><b>open_defects (derived)</b><span class="v">—</span></div>
      </div>
      <div id="hTable"></div>
    </div>
    <div class="status-line q" id="hStatus"></div>
  </div>
  <div class="table-wrap"><table>
    <thead><tr><th>Cached field on Asset</th><th>Computed from</th><th>Rule</th><th>Why cache it at all</th></tr></thead>
    <tbody>
      <tr><td class="mono">last_inspected_at</td><td>Inspection</td><td>Latest <code>visited_at</code> for this asset</td><td>Map colouring “not inspected in 12 months” needs it on the feature</td></tr>
      <tr><td class="mono">last_condition_code</td><td>Inspection</td><td><code>condition_code</code> of the latest visit, ignoring blank assessments</td><td>Same</td></tr>
      <tr><td class="mono">open_defects</td><td>Inspection</td><td><code>defects_found</code> of the latest completed checklist</td><td>Work planning</td></tr>
    </tbody></table></div>
  <p>Each cached field is marked <em>derived</em> in the dictionary, with its rule and how it is refreshed (“recomputed nightly by query Q-1” or “updated by the field app on save”). A derived field nobody refreshes is a stale fact wearing the clothes of a current one.</p>
  <h3>Two clocks for every event</h3>
  <p>Every observation has a <strong>visit time</strong> (when it happened — <code>visited_at</code>, from the notebook) and a <strong>typing time</strong> (when the row was written — <code>recorded_at</code>). They differ whenever a crew types up the day at the depot, a paper form is entered later, or an offline phone syncs. Keep both: “which assets were visited in March?” uses visit time; “which records were entered after the report ran?” uses typing time. INS-0004 is the fixture’s example — visited 14 Sep 2025, typed 16 Mar 2026, numbered <em>after</em> INS-0003 because numbers are given at typing time.</p>
  <div class="callout note"><span class="label">Platform note — editor tracking</span><p>ArcGIS can fill typing-time facts automatically. <em>Editor tracking</em> records “who edits features and when” in four fields — creator, creation date, last editor, last edit date. The Enable Editor Tracking tool lets you record dates in UTC (the default; recommended because “editors can apply edits from potentially anywhere”) or in the database’s time zone. ArcGIS Online has the same setting (“Keep track of who edited the data”). Two limits: it records <em>who</em> and <em>when</em>, not <em>what</em> changed; and its dates are typing times — they can never replace <code>visited_at</code>. Keeping every previous value of every field (archiving, versioning) is enterprise functionality and outside this chapter.</p></div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“The <code>created_date</code> field tells us when the inspection happened.”</em> It tells you when the row was created. For a crew that types up the week on Friday, every inspection “happened” on Friday. Visit time must be its own field, captured at the source.</p></div>

  <h2><span class="mod">8.7.2</span>Photos are rows; orphans are found by a query</h2>
  <p>A photo belongs to <em>something</em> — a specific visit, or asset, or complaint — and the design must say which. The wrong design is a text field on the parent holding <code>"sl113_a.jpg; sl113_b.jpg"</code>. It fails four ways: you cannot store anything <em>about</em> one photo (who took it, when, of what); you cannot ask “visits with more than two photos” without string parsing; renaming one file means editing the middle of a string; and the separator is a convention nobody enforces (the lab’s bad table uses “;” in one row and “,” in the next). The right design is one row per file with a key to its parent:</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Field</th><th>Meaning</th><th>Type</th><th>Blank rule</th><th>Example</th></tr></thead>
    <tbody>
      <tr><td class="mono">photo_id</td><td>ID of one photo record</td><td>Text(12)</td><td>Never blank; unique</td><td class="mono">PH-0003-1</td></tr>
      <tr><td class="mono">inspection_id</td><td>The visit this photo documents</td><td>Text(8)</td><td>Never blank; must exist in Inspection</td><td class="mono">INS-0003</td></tr>
      <tr><td class="mono">file_name</td><td>Original file name</td><td>Text(255)</td><td>Never blank</td><td class="mono">sl113_20260314_1.jpg</td></tr>
      <tr><td class="mono">taken_at</td><td>When the photo was taken (UTC)</td><td>Date</td><td>Blank = not available from the phone</td><td class="mono">2026-03-14 05:14</td></tr>
      <tr><td class="mono">subject</td><td>What it shows (coded: LAMP, POLE, GRATING, TRUNK, OTHER)</td><td>Text</td><td>Blank = not classified</td><td class="mono">LAMP</td></tr>
    </tbody></table></div>
  <div class="callout note"><span class="label">Platform note — ArcGIS attachments</span><p>ArcGIS builds exactly this pattern for you. “Attachments are used to associate copies of media files, such as documents and images, with features … or rows”. Enabling attachments “creates a stand-alone table to store the media … and a one-to-many relationship class”; the table gets the suffix <code>__ATTACH</code> and the relationship class <code>__ATTACHREL</code>; the <em>Global IDs</em> option is checked by default and Esri recommends leaving it on. Feature services can query and edit attachments once the geodatabase is set up for them. Design consequence: the parent of an attachment is <em>whatever row you enabled attachments on</em> — so to attach photos to the <strong>visit</strong> rather than the asset, enable them on the Inspection table.</p></div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Break a link, then find the orphan</h3>
    <p>An <dfn title="A child row whose key points at a parent that does not exist">orphan</dfn> is a child row whose key matches no parent. Delete a parent below (as a careless script might), then run the check.</p>
    <div class="opbtns">
      <button class="btn small" id="oDel">Delete asset DR-0042 (no cascade, no restrict)</button>
      <button class="btn small" id="oTypo">A typo: change INS-0004’s asset_id to “SL-0131”</button>
      <button class="btn small" id="oReset">Reset</button>
      <button class="btn accent small" id="oRun">Run the orphan check</button>
    </div>
    <div class="grid-2"><div id="oAssets"></div><div id="oInsp"></div></div>
    <div class="copywrap"><pre>-- Inspections whose asset does not exist (expected: zero rows)
SELECT i.inspection_id, i.asset_id
FROM   inspection i
LEFT JOIN asset a ON a.asset_id = i.asset_id
WHERE  a.asset_id IS NULL;</pre></div>
    <div class="status-line q" id="oStatus">Press “Run the orphan check”.</div>
  </div>
  <p>The same query shape finds photos without an inspection, and — with the tables swapped — <em>parents that should have children and do not</em>, which is not an integrity error but a work list (“assets never inspected”). In a database with foreign keys the first two cases should be <em>impossible</em> (<code>RESTRICT</code> “prevents deletion of a referenced row”); running the query anyway is how you prove the constraint is actually in place. In ArcGIS and QGIS the same logic is a join whose unmatched rows you then select — Chapter 10 makes that a hands-on skill.</p>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“Cascade delete keeps the data clean, so it is always right.”</em> Cascade keeps referential integrity <em>by deleting history</em>. When an asset is decommissioned the office almost always wants its inspection record kept — so the asset row is marked <code>status = REMOVED</code> with a date, and nothing is deleted. Cascade suits children with no meaning and no audit value without their parent (a photo of a deleted test record). It is a per-relationship decision, written into the relationship definition.</p></div>

  <h2><span class="mod">8.7.3</span>Who owns a change, and what a change touches</h2>
  <p>A schema is an interface. Forms, scripts, published layers, reports and other people’s exports all depend on field names, types, domains and keys. Changing one without a process is the GIS equivalent of changing a public API in a patch release.</p>
  <p><strong>Ownership (course policy):</strong> every table has a named <strong>schema owner</strong> — a person or role, not “the GIS team” — who alone approves a change to its fields, domains, keys or relationships. Anyone may <em>propose</em> a change by submitting it with the checklist below filled in; the owner approves, schedules and logs it. Who can <em>make</em> the change on the server is a later-phase topic; here the point is that the <em>decision</em> has an owner.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Migration impact checklist — worked for one change, saved for your own</h3>
    <p>Proposed change: <em>rename <code>Height</code> to <code>pole_height_m</code> and convert it to metres.</em> Read the worked answers, then use the boxes for a change of your own (saved in this browser).</p>
    <div class="table-wrap"><table class="mig">
      <thead><tr><th>#</th><th>Question</th><th>Why it matters</th><th>Worked answer</th></tr></thead>
      <tbody>
        <tr><td>1</td><td class="q">What exactly changes — name, type, length, blank rule, domain, key, relationship, shape?</td><td>Different changes have different reversibility</td><td>Name, type (text → double), unit, blank rule</td></tr>
        <tr><td>2</td><td class="q">Is it reversible on a table that already has rows?</td><td>ArcGIS: data type “can only be changed if the table is empty”; Allow NULL “can only be set to false if the table is empty”; text length “can only be increased for tables with data”</td><td>Not in place: add a new field, convert with checks, then drop the old one</td></tr>
        <tr><td>3</td><td class="q">Which rows cannot be converted without loss?</td><td>7, 6.5 and 0 have no unit; 22 ft and 12 m convert</td><td>Three rows need source checks; the log records each</td></tr>
        <tr><td>4</td><td class="q">Which apps, forms, scripts, published layers, views and reports use the field?</td><td>Each is a breaking change waiting to happen</td><td>Field-app form; the “tall poles” report; the web layer’s pop-up</td></tr>
        <tr><td>5</td><td class="q">Which related tables, domains or relationship classes reference it?</td><td>Keys and domains are shared</td><td>None (not a key)</td></tr>
        <tr><td>6</td><td class="q">What is the data fix and the verification query?</td><td>A migration is not done until a query proves it</td><td><code>COUNT(*) WHERE pole_height_m IS NULL AND asset_type = 'SL'</code> before/after</td></tr>
        <tr><td>7</td><td class="q">What is the rollback?</td><td>“Undo” rarely exists for schema changes</td><td>Keep the old field until the report has been re-run on the new one</td></tr>
        <tr><td>8</td><td class="q">Who approved it, when, and where is it logged?</td><td>Traceability (Chapter 9 asks the same for data edits)</td><td>Schema owner; change-log entry</td></tr>
      </tbody></table></div>
    <div class="lab-form">
      <label>Your proposed change</label><input type="text" data-save="mig-change" placeholder="e.g. add ward_code to Asset">
      <label>Q1–Q3: what changes, reversible?, rows that cannot convert</label><textarea data-save="mig-123"></textarea>
      <label>Q4–Q5: who and what depends on it</label><textarea data-save="mig-45"></textarea>
      <label>Q6–Q8: fix + verification query, rollback, owner and log</label><textarea data-save="mig-678"></textarea>
      <p class="saved">Saved automatically in this browser.</p>
    </div>
  </div>
  <div class="callout dev"><span class="label">Developer view</span><p>This is a database migration with a code review. <strong>Where the comparison stops:</strong> a web app has one database and one deploy. A GIS dataset may exist as a master geodatabase, a web layer that <em>copied</em> the data when it was published (Chapter 2), three contractors’ Shapefile exports, and a dashboard — and the migration must reach, or deliberately exclude, every copy. Question 4 is where most GIS migrations fail.</p></div>

  <div class="quiz" data-answer="1" data-fb="Overwriting keeps only the latest cleaning; frequency, crew and history are lost, and a default would make a cleaning that never happened look real. The alternative is a cleaning-visit table (or a visit-type code on Inspection) with last_cleaned_at as a derived cache — which still needs checklist questions 4, 6, 7 and 8 answered.">
    <div class="q">A colleague wants to add “date of last cleaning” to the Asset table for drains, overwritten after each cleaning. What is the better design?</div>
    <div class="opts">
      <button class="opt">Fine as proposed — one date per drain is all anyone needs.</button>
      <button class="opt">A cleaning-visit table (one row per cleaning) with <code>last_cleaned_at</code> on Asset only as a derived, refreshed copy.</button>
      <button class="opt">Add last_cleaning_1, last_cleaning_2, last_cleaning_3 columns.</button>
      <button class="opt">Store the cleaning dates as a comma-separated list in one text field.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  /* history demo */
  const H = { last: document.getElementById("hLast"), cond: document.getElementById("hCond"), def: document.getElementById("hDef") };
  const tbl = document.getElementById("hTable"), st = document.getElementById("hStatus");
  const set = (r, v, cls) => { r.querySelector(".v").textContent = v; r.className = "row " + (cls || ""); };
  const I3 = F8.inspections.find(i => i.id === "INS-0003"), I4 = F8.inspections.find(i => i.id === "INS-0004");
  const row = (i, cls) => ({ cells: [i.id, i.ist, "T-N", i.cond, i.defects, i.remarks ?? "", i.entered], cls });
  function draw(rows) { tbl.innerHTML = tableHTML(["inspection_id", "visited (IST)", "team", "cond", "defects", "remarks", "recorded_on"], rows.map(r => r.cells), { mono: [0, 1, 2, 6], caption: "Inspection history — SL-0113", rowClass: (r, i) => rows[i].cls }); }
  function state(k) {
    ["h0", "h1", "h2", "h3"].forEach(id => document.getElementById(id).setAttribute("aria-pressed", "false")); document.getElementById(k).setAttribute("aria-pressed", "true");
    if (k === "h0") { set(H.last, "— (never)"); set(H.cond, "— (blank = never assessed)"); set(H.def, "—"); draw([]); st.className = "status-line q"; st.textContent = "One Asset row. No inspection rows. “Never inspected” is the absence of rows, not a special value."; }
    if (k === "h1") { set(H.last, "— (nothing typed in yet)"); set(H.cond, "—"); set(H.def, "—"); draw([{ cells: ["(paper form)", I4.ist, "T-N", I4.cond, I4.defects, "", "not yet typed in"], cls: "dim" }]); st.className = "status-line q"; st.textContent = "The crew visits on 14 Sep 2025 and finds nothing wrong — but the form stays on paper in the depot. The database has no row and the cache stays empty. Keep this in mind for the last button."; }
    if (k === "h2") { set(H.last, "2026-03-14 05:12 UTC (10:42 IST)", "changed"); set(H.cond, "3 (Fair)", "changed"); set(H.def, "1", "changed"); draw([row(I3, "new")]); st.className = "status-line ok"; st.textContent = "INS-0003 is typed in the same day. Asset cache refreshed: one open defect. The asset row itself did not change except for the derived fields."; }
    if (k === "h3") { set(H.last, "2026-03-14 05:12 UTC (10:42 IST)", "same"); set(H.cond, "3 (Fair)", "same"); set(H.def, "1", "same"); draw([row(I3, ""), row(I4, "new")]); st.className = "status-line ok"; st.textContent = "INS-0004 gets the NEXT number although the visit was six months earlier — numbering happens at typing time. The cache does not change (the latest VISIT is still March). Now we know the lamp was sound in September: the flicker began between the two visits. Table F4 could never have told you that."; }
  }
  ["h0", "h1", "h2", "h3"].forEach(id => document.getElementById(id).addEventListener("click", () => state(id)));
  state("h0");

  /* orphan demo */
  let assets, insp;
  function reset() { assets = F8.assets.map(a => ({ id: a.id, type: a.typeName })); insp = F8.inspections.map(i => ({ id: i.id, asset: i.asset })); drawO(); document.getElementById("oStatus").className = "status-line q"; document.getElementById("oStatus").textContent = "Press “Run the orphan check”."; }
  function drawO(orph = []) {
    document.getElementById("oAssets").innerHTML = tableHTML(["asset_id", "type"], assets.map(a => [a.id, a.type]), { mono: [0], caption: "asset (parent)" });
    document.getElementById("oInsp").innerHTML = tableHTML(["inspection_id", "asset_id"], insp.map(i => [i.id, i.asset]), { mono: [0, 1], caption: "inspection (child)", rowClass: (r) => orph.includes(r[0]) ? "orphan" : "" });
  }
  document.getElementById("oDel").addEventListener("click", () => { assets = assets.filter(a => a.id !== "DR-0042"); drawO(); });
  document.getElementById("oTypo").addEventListener("click", () => { const i = insp.find(x => x.id === "INS-0004"); if (i) i.asset = "SL-0131"; drawO(); });
  document.getElementById("oReset").addEventListener("click", reset);
  document.getElementById("oRun").addEventListener("click", () => {
    const ids = new Set(assets.map(a => a.id)); const orph = insp.filter(i => !ids.has(i.asset)).map(i => i.id); drawO(orph);
    const s = document.getElementById("oStatus");
    if (!orph.length) { s.className = "status-line ok"; s.textContent = "0 rows returned — every inspection finds its asset."; }
    else { s.className = "status-line bad"; s.textContent = `${orph.length} orphan row(s): ${orph.join(", ")}. A foreign key with RESTRICT would have refused the delete; a typo needs a domain/lookup or a review. Fix: restore the parent, or move the child to a quarantine table — never silently delete it.`; }
  });
  reset();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
