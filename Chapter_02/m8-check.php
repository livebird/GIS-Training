<?php $page = ['title' => '2.8 Independent check and progression gate', 'chapter' => 2, 'module' => '2.8']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 2.8 · Assessment</div>
    <h1>Independent check and progression gate</h1>
    <p class="lead">This is what you submit to your instructor. There are no answers on this page — the instructor holds the answer key.</p>
    <div class="outcomes"><h4>Submit</h4>
      <ul><li>The module 2.7 deliverables (sketch, Table 2.7, role table, gap list, step 2–3 text).</li>
      <li>Written answers to Q1–Q6 and S1–S2.</li>
      <li>The practical task: a table for four requests plus one paragraph.</li>
      <li>Be available for the two-minute oral check.</li></ul></div>
  </div>
  <div class="callout note"><span class="label">Before you start</span><p>Answer every item under its stated assumptions. If you think an item is under-specified, say what is missing rather than guessing. The quick checks in modules 2.1–2.6 were practice; these are marked.</p></div>

  <h2><span class="mod">2.8.1</span>Concept questions (30 marks, 5 each)</h2>
  <div class="card"><p><strong>Q1.</strong> <span class="pill">2.1 · LO1</span><br>A field inspector’s tablet has the request map open and, with no signal, is working from an offline copy taken this morning. Which is correct under this chapter’s definitions? (a) The tablet now holds the authoritative data until it reconnects. (b) The tablet holds a copy; the authoritative data is wherever the organisation has designated it, and the tablet’s copy may be stale. (c) The tablet is authoritative for the requests it contains and the server for the rest. (d) “Authoritative” is a technical property of hosted layers, so the question does not apply.</p></div>
  <div class="card"><p><strong>Q2.</strong> <span class="pill">2.2 · LO2</span><br>An <code>.aprx</code> is copied to a machine that cannot reach the file share in the layers’ source paths. What opens? (a) Nothing. (b) The project, with maps, layer list and symbols, but the layers cannot draw. (c) The project, drawing from a copy embedded in the .aprx. (d) The project, which downloads the data from ArcGIS Online automatically.</p></div>
  <div class="card"><p><strong>Q3.</strong> <span class="pill">2.3 · LO3</span><br>Web map <code>M</code> references hosted feature layer <code>L</code>; view <code>V</code> is created from <code>L</code>. For each action say what happens to the <em>data</em>, one phrase each: (i) <code>M</code> is deleted; (ii) <code>V</code> is deleted; (iii) <code>L</code> is deleted.</p></div>
  <div class="card"><p><strong>Q4.</strong> <span class="pill">2.4 · LO4</span><br>Which is a difference in <em>responsibility</em> between ArcGIS Online and ArcGIS Enterprise, not a feature difference? (a) Enterprise has a portal with groups and items; ArcGIS Online does not. (b) With Enterprise the organisation provides, upgrades, backs up and monitors the infrastructure; with ArcGIS Online Esri does. (c) ArcGIS Online supports web maps; Enterprise only map services. (d) Enterprise cannot share content with ArcGIS Online.</p></div>
  <div class="card"><p><strong>Q5.</strong> <span class="pill">2.5 · LO5</span><br>Match each task to the single most appropriate tool from {ArcGIS Maps SDK for JavaScript, ArcGIS API for Python, ArcPy} with a one-phrase reason: (i) list every item in the organisation shared with everyone; (ii) run a geoprocessing tool on a file geodatabase from a scheduled script on a machine with ArcGIS Pro installed; (iii) add a custom drawing tool to a browser page residents use.</p></div>
  <div class="card"><p><strong>Q6.</strong> <span class="pill">2.6 · LO6</span><br>“We will replace ArcGIS Online with OpenLayers.” Name the job OpenLayers covers, then name two jobs of ArcGIS Online that the sentence leaves uncovered.</p></div>

  <h2><span class="mod">2.8.2</span>Scenario questions (25 marks)</h2>
  <div class="card"><p><strong>S1 (12 marks).</strong> <span class="pill">2.3, 2.1 · LO3</span><br>A colleague sends a link to a public web map from a neighbouring organisation showing pipe locations and asks you to “add these to our field map so the crews can correct the pipe positions when they are wrong”. The link opens without signing in. Write a short reply (four to six sentences) that (a) separates what the link proves from what it does not, (b) names the two settings on the <em>other</em> organisation’s layer that must be checked, (c) names the non-technical question that must be answered, and (d) says which copy of the pipe data would be authoritative if the crews’ corrections were accepted.</p></div>
  <div class="card"><p><strong>S2 (13 marks).</strong> <span class="pill">2.5, 2.6, 2.1 · LO5, LO6</span><br>A product manager writes: “We don’t need any of this platform stuff. One developer can write the whole request system in a week with a mapping library and a database.” The requirement: crews edit requests offline on phones; managers see live counts; the public sees locations but not notes; the service must be restored within four hours if a server fails. Using the five jobs, explain in one paragraph why “a mapping library and a database” is not the complete stack, and list the jobs the plan leaves unassigned.</p></div>

  <h2><span class="mod">2.8.3</span>Independent practical task (35 marks) — unfamiliar inputs</h2>
  <div class="card">
    <p><span class="synthetic">Made-up evidence: Solution S-X, “School Transport Planner (Training)” — deliberately different from the lab.</span></p>
    <ul>
      <li><strong>Data:</strong> an enterprise geodatabase (an Esri geodatabase kept inside a normal database — Chapter 7) in a PostgreSQL database <code>schools_db</code> on server <code>db01</code> holds <code>Schools</code> (points), <code>BusRoutes</code> (lines), <code>Catchments</code> (polygons). The readme names <code>schools_db</code> as authoritative.</li>
      <li><strong>Publishing:</strong> ArcGIS Pro published all three to an <strong>ArcGIS Enterprise</strong> portal <code>portal.training.example</code> by <strong>referencing registered data</strong> on the federated ArcGIS Server <code>gis01</code> (federated = joined to the portal; registered = the server was told where the database is; the data was not copied).</li>
      <li><strong>Content:</strong> web map <em>Transport Overview</em> (group <em>Transport Planners</em>) styles <code>BusRoutes</code> by operator and <code>Catchments</code> with a yellow fill. A second web map <em>Transport Public</em> (everyone) shows <code>Schools</code> only.</li>
      <li><strong>Apps:</strong> an Experience Builder app <em>Route Planner</em> (group) with a custom widget that computes walking distance from an address to the nearest school; an Instant App <em>Find My School</em> (public).</li>
      <li><strong>Operations:</strong> on Monday <code>gis01</code> failed; the portal stayed up but every layer in <em>Transport Overview</em> showed an error until <code>gis01</code> was restored by the operations team on Tuesday.</li>
    </ul>
    <p><strong>Four requests arrive.</strong> For each, state (1) which job (R1–R5, or “infrastructure operation”) it involves, (2) which component or item is changed, (3) whether the <em>data</em> changes, and (4) what evidence supports your answer — or whether there is not enough information. <span class="pill">6 marks each</span></p>
    <ol>
      <li>“Move the boundary of catchment C-14 two streets north — it was drawn wrongly.”</li>
      <li>“Change the catchment fill from yellow to light blue in the planners’ map only.”</li>
      <li>“When a planner clicks a route, the app should also show the number of pupils on it — that needs a different calculation from the current widget.”</li>
      <li>“The layers were down all Monday; make sure this cannot happen again.”</li>
    </ol>
    <p>Then, in one paragraph: <strong>why does the Experience Builder app’s custom-widget code, on its own, not provide the data-management and operations stack that S-X needs?</strong> Refer to what happened on Monday. <span class="pill">11 marks</span></p>
  </div>

  <h2><span class="mod">2.8.4</span>Oral explanation (10 marks) — two minutes, no notes</h2>
  <div class="card"><p>Describe the path of a single request record in Solution S-2 from the archived file geodatabase to the public <em>Request Locator</em> app, naming each component it passes through or is referenced by, and stating which component holds the authoritative copy. Finish by naming <strong>one thing you do not yet know</strong> about how the solution works and what you would need to see to find out.</p></div>

  <h2><span class="mod">2.8.5</span>Scoring and the progression rule</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Component</th><th>Marks</th><th>What earns the marks</th></tr></thead>
    <tbody>
      <tr><td>Concept questions Q1–Q6</td><td class="mono">30</td><td>Correct answer and, where asked, a reason consistent with the chapter’s definitions</td></tr>
      <tr><td>Scenario S1</td><td class="mono">12</td><td>All four parts; visibility, editing and usage rights kept separate; authoritative copy named</td></tr>
      <tr><td>Scenario S2</td><td class="mono">13</td><td>Jobs named correctly; reasoning from the requirement, not preference</td></tr>
      <tr><td>Practical — four requests</td><td class="mono">24</td><td>Job, component, data-change flag and evidence all correct; “not enough information” used where the description is silent</td></tr>
      <tr><td>Practical — paragraph</td><td class="mono">11</td><td>Explains Monday in terms of infrastructure operation and referenced data; separates app code from the rest of the stack</td></tr>
      <tr><td>Oral explanation</td><td class="mono">10</td><td>Complete path in order; authoritative copy correct; one genuine unknown with the evidence that would resolve it</td></tr>
    </tbody></table></div>
  <div class="callout warn"><span class="label">Pass rule</span><p><strong>80 marks out of 100, and no critical misconception.</strong> For this chapter these are: (1) treating a project, map, app or device as holding the authoritative data; (2) treating public visibility as edit or reuse permission; (3) presenting a single library or product as a complete replacement for all five jobs. Any of these means remediation and a fresh exercise, whatever the total. A learner who cannot name a limit of their own understanding in the oral item retakes it.</p></div>
  <div class="callout idea"><span class="label">Progression rule</span><p>You progress when you can describe the data-to-user path <em>and</em> the limits of your current understanding.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<?php require __DIR__ . '/../partials/end.php'; ?>
