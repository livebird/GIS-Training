<?php $page = ['title' => '8.9 Independent check and progression gate', 'chapter' => 8, 'module' => '8.9']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 8.9 · Assessment</div>
    <h1>Independent check</h1>
    <p class="lead">Six concept questions, two scenarios, one practical on <em>new</em> data (bus shelters), and one spoken explanation. The concept questions give instant feedback so you can self-check; the scenarios, practical and oral are marked by your instructor from the chapter’s Instructor Appendix. Where a question says “state your assumptions”, an answer without them is incomplete even if the conclusion is right.</p>
    <div class="outcomes"><h4>Pass rule (from the blueprint)</h4>
      <ul><li>Suggested pass: 80 of 100 — <strong>and</strong> none of these critical misconceptions anywhere in your work:</li>
      <li>using a display label, a spreadsheet row number or an ObjectID as the key related records carry;</li>
      <li>storing repeated inspections as repeated columns, or overwriting history with the latest visit;</li>
      <li>treating a default as an observation, or turning blanks into zeros in a count or measurement;</li>
      <li>claiming that a diagram, a join or a UUID column enforces integrity by itself;</li>
      <li>mixing units or time-zone rules in one field without a written rule.</li></ul></div>
  </div>

  <h2><span class="mod">8.9.1</span>Concept questions (25 points)</h2>
  <div class="quiz" data-answer="2" data-fb="Three grains: asset (asset_id, asset_type, install_year), visit (visit_date, condition, team) and complaint (reporter_phone, report_date). Challenge reporter_phone: which question needs it, who may see it, and why is it on an asset/inspection table at all?">
    <div class="q">Q1 (8.1). A table has <code>asset_id, asset_type, install_year, visit_date, condition, team, reporter_phone, report_date</code>. How many grains are mixed, and which column would you challenge under “every field earns its place”?</div>
    <div class="opts"><button class="opt">One grain; challenge <code>team</code>.</button><button class="opt">Two grains (asset, visit); challenge <code>install_year</code>.</button><button class="opt">Three grains (asset, visit, complaint); challenge <code>reporter_phone</code>.</button><button class="opt">Three grains; nothing to challenge — all fields might be useful.</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="1" data-fb="The points mean different things (surveyed pole base vs a citizen’s estimate), their capture methods and reliability differ, and their grains differ (a thing vs an event). Correct design: two tables, each with its own shape specification, linked by an optional asset_id on Request.">
    <div class="q">Q2 (8.2). Two point datasets on the same grid — surveyed streetlight pole bases and citizen-reported incident positions — are proposed for merging into one table <code>Locations</code>. Why is that wrong?</div>
    <div class="opts"><button class="opt">It is not wrong: same shape type and same grid means they can be one table.</button><button class="opt">The points stand for different things, were captured with different reliability, and the rows have different grains — keep Asset and Request separate, linked by an optional <code>asset_id</code>.</button><button class="opt">Only because the coordinate system differs.</button><button class="opt">Only because a table cannot hold more than 1,000 points.</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="0" data-fb="0 = checklist completed, no defects (close the visit); blank = checklist not completed (re-inspect). With a default of 0, every incomplete checklist counts as “no defects”, so the percentage with defects is understated by exactly the number of incomplete visits. Hosted feature layers store dates in UTC; a publisher with IST source data must say so at publish time so the values are converted.">
    <div class="q">Q3 (8.3). <code>defects_found</code> is a whole number, blank allowed, no default. Which statement is right?</div>
    <div class="opts"><button class="opt">0 means “checked, none found”; blank means “not checked”. A default of 0 would understate the share of visits with defects. In an ArcGIS Online hosted layer the visit time is stored in UTC, so IST source data must be declared as local time when publishing.</button><button class="opt">0 and blank mean the same thing; a default of 0 is harmless.</button><button class="opt">Blank should be replaced by −1 to mark “not checked”.</button><button class="opt">Hosted feature layers store dates in the publisher’s local time, so nothing needs declaring.</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="2" data-fb="Only the business identifier is designed to survive a copy. ObjectIDs are per table and may be run-time values; labels are neither unique nor stable; a GlobalID is regenerated by Append unless a preserving workflow is configured — which the option rules out.">
    <div class="q">Q4 (8.4). Which may be used as the foreign key inspection rows carry to identify their asset across an export to a file geodatabase and re-import?</div>
    <div class="opts"><button class="opt">The asset’s ObjectID.</button><button class="opt">The asset’s display label.</button><button class="opt">The asset’s business identifier <code>asset_id</code>.</button><button class="opt">The asset’s GlobalID, with no further workflow configuration.</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="3" data-fb="(a) conceptual — enforces nothing; (b) QGIS relation — enforced only while editing in QGIS; (c) database foreign key — enforced against every client including scripts; (d) join — a temporary layer property, enforces nothing; (e) ArcGIS simple relationship class — behaviour applied by ArcGIS clients that honour it; verify for scripts.">
    <div class="q">Q5 (8.5). Which of these is enforced when a script inserts rows <em>directly</em> into the storage? (a) a line on an ER diagram; (b) a Composition-strength QGIS relation; (c) <code>FOREIGN KEY … ON DELETE RESTRICT</code> in PostgreSQL; (d) fields appended by a join in one map; (e) an ArcGIS simple relationship class.</div>
    <div class="opts"><button class="opt">All five.</button><button class="opt">(b), (c) and (e).</button><button class="opt">(a) and (c).</button><button class="opt">(c) for certain; (e) for ArcGIS-aware clients (verify for scripts); (a), (b) and (d) not at all.</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="1" data-fb="Stored code OPEN; displayed description “Open”; default OPEN on creation (administrative fact, so a default is right); a missing status would be a blank — which this design forbids. Rules: (i) schema (coded domain) with a review query as backstop; (ii) application plus review; (iii) review — it is a work list, not an integrity rule.">
    <div class="q">Q6 (8.6). For <code>Request.status</code> with the <code>RequestStatus</code> list: where should these rules live? (i) status must be one of the six codes; (ii) a DUP request must reference another request; (iii) requests open for more than 30 days must be listed weekly.</div>
    <div class="opts"><button class="opt">All three in the schema.</button><button class="opt">(i) schema (with a review backstop); (ii) application plus review; (iii) review.</button><button class="opt">All three in the field app.</button><button class="opt">(i) review; (ii) schema; (iii) application.</button></div><div class="fb"></div>
  </div>

  <h2><span class="mod">8.9.2</span>Scenario questions (20 points, 10 each) — write your answers; instructor-marked</h2>
  <div class="scen">
    <h4 style="margin-top:0">Scenario 1 (8.3, 8.6)</h4>
    <p>A field crew’s app defaults <code>condition_code</code> to 3 and <code>defects_found</code> to 0, and stores <code>visited_at</code> from the phone clock at the moment the form is <em>saved</em> — which crews do at the depot each evening. The supervisor’s dashboard shows 97% of assets “Fair or better” and “no visits after 4 p.m.”. (a) Identify three separate design faults and the module that explains each. (b) For each fault, state the schema or dictionary change that fixes it. (c) State one thing the dashboard <em>cannot</em> tell you even after the fixes.</p>
    <div class="lab-form"><textarea data-save="s1" placeholder="Fault 1 … Fault 2 … Fault 3 … Fixes … What it still cannot tell you …"></textarea></div>
  </div>
  <div class="scen">
    <h4 style="margin-top:0">Scenario 2 (8.4, 8.7)</h4>
    <p>The municipality receives a contractor’s spreadsheet of 40 drain inspections keyed on the contractor’s own row numbers 1–40 and the drain’s street description. Twelve descriptions match no asset exactly; three match two assets each. (a) Write the identity policy you would apply (import matching, duplicates, related records). (b) Say which rows go into Inspection and which into a quarantine table, and why the quarantine table’s <code>asset_id</code> may be blank while Inspection’s may not. (c) Write the orphan query (any SQL dialect or plain words) you would run after loading, and its expected result.</p>
    <div class="lab-form"><textarea data-save="s2"></textarea></div>
  </div>

  <h2><span class="mod">8.9.3</span>Practical task on unfamiliar data (35 points)</h2>
  <p>The municipality is adding a new asset type: <strong>bus shelters</strong>. Requirements (all made up):</p>
  <ul>
    <li>Each shelter is one structure at one location; it has a number of seats, a yes/no for lighting, and an advertising panel that may be absent.</li>
    <li>Each shelter serves one or more <strong>bus routes</strong>, and each route serves many shelters. Routes have a code (for example <code>07</code>, <code>12A</code>) and a name.</li>
    <li>Shelters receive <strong>cleaning visits</strong> (date and time, crew, cleanliness score 1–5, remarks) and <strong>structural inspections</strong> (date and time, crew, condition score 1–5, defects found). A shelter can have many of each.</li>
    <li>Both kinds of visit can have <strong>photos</strong>, several per visit.</li>
    <li>The existing Asset, Inspection, Team and InspectionPhoto tables from the lab must keep working; the existing field app writes to Inspection.</li>
  </ul>
  <h4>Deliver</h4>
  <ol>
    <li>An extended entity–relationship diagram.</li>
    <li>Dictionary rows for every new or changed field.</li>
    <li>Relationship definitions, including the new many-to-many.</li>
    <li>Your decision: are cleaning visits Inspection rows with a visit-type code, or a separate table? Give the reasoning and the consequence for the existing app.</li>
    <li>Six example records including one shelter on two routes and one cleaning visit with two photos.</li>
    <li>The migration impact checklist (8.7.3) for the change to the Asset table.</li>
    <li>A statement of which integrity rules your intended platform enforces and which still rely on the app or a review — name the platform you assumed.</li>
  </ol>
  <p><strong>Constraints:</strong> no destructive duplication (no copy of shelter facts on visit rows; no copy of route facts on shelter rows); route codes are text; every table has a business identifier.</p>
  <div class="lab-form"><label>Notes for your practical (saved in this browser)</label><textarea data-save="prac"></textarea></div>

  <h2><span class="mod">8.9.4</span>Oral explanation (5 points)</h2>
  <p>In under three minutes, tell the story of asset SL-0113 from installation in 2018 through the two inspections in the chapter’s history (INS-0004, then INS-0003), naming at each step which table gains a row, which fields change, which do not, and which identifiers connect the rows. Finish with one integrity rule in your design that the storage enforces and one that it does not.</p>

  <h2><span class="mod">8.9.5</span>Scoring</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Component</th><th>Weight</th><th>What earns it</th></tr></thead>
    <tbody>
      <tr><td>Concept questions Q1–Q6</td><td>25%</td><td>Correct answer with stated assumptions</td></tr>
      <tr><td>Scenarios S1–S2</td><td>20%</td><td>Faults identified with module references; concrete schema fixes; a correct orphan query</td></tr>
      <tr><td>Practical (bus shelters)</td><td>35%</td><td>Coherent diagram and dictionary; M:N via a junction table; no duplicated facts; six records that resolve; checklist done; honest enforcement statement for the named platform</td></tr>
      <tr><td>Guided lab (8.8)</td><td>15%</td><td>All validation checks met; conversion log complete</td></tr>
      <tr><td>Oral</td><td>5%</td><td>Correct sequence of rows and identifiers; enforcement distinction stated</td></tr>
    </tbody></table></div>
  <p class="small">An internal recommendation following the blueprint’s assessment policy, not an external standard. Any critical misconception (see the box at the top) requires correction and a fresh exercise before Chapter 9, whatever the total.</p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<?php require __DIR__ . '/../partials/end.php'; ?>
