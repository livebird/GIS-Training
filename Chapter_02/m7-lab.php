<?php $page = ['title' => '2.7 Guided lab: trace a prepared solution', 'chapter' => 2, 'module' => '2.7']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 2.7 · Guided lab</div>
    <h1>Trace a prepared solution</h1>
    <p class="lead">Read the evidence about Solution S-2. Say where geometry, styling, access decisions and app behaviour are managed. Where the evidence does not show it, say <strong>“not enough information”</strong> — that is a correct answer, not a failure.</p>
    <div class="outcomes"><h4>Objective, prerequisites, access</h4>
      <ul><li><strong>Objective:</strong> a labelled architecture sketch, a completed “where is it managed?” table, a role comparison table, and a list of what the evidence does not show.</li>
      <li><strong>Prerequisites:</strong> modules 2.1–2.6.</li>
      <li><strong>Software and access:</strong> none. No account, no publishing, no credits. Everything below is <span class="synthetic">made-up evidence</span> written as an instructor would copy it down from real screens.</li></ul></div>
  </div>

  <div class="callout note"><span class="label">What an evidence pack is</span><p>The chapter asks for “a screenshot set or supervised demonstration”. This page gives the text an instructor would read off each screen, labelled by the screen it came from. Your instructor may replace any card with a real screenshot; your tasks do not change. Your answers on this page are saved in this browser only.</p></div>

  <h2><span class="mod">2.7.2</span>Evidence pack — Solution S-2, “Request Tracker (Training)”</h2>
  <div>
    <div class="tabs" role="tablist">
      <button role="tab">E1 · Files</button><button role="tab">E2 · ArcGIS Pro project</button><button role="tab">E3 · ArcGIS Online items</button><button role="tab">E4 · Members &amp; group</button><button role="tab">E5 · Observed behaviour</button>
    </div>
    <div class="tabpanel">
      <p><strong>Directory listing of the source data package</strong> (as captured from a file browser).</p>
<pre class="listing">\\gis-files\training\
├── README_Municipal_Training.txt   <span class="nc">(provenance — where the data came from: "Synthetic training fixture. Planar grid, metres.
│                                    No Earth location. Build copy uses a documented local
│                                    coordinate reference assigned by the instructor.
│                                    Owner: GIS training lead.")</span>
└── Municipal_Training.gdb\         (file geodatabase)
      Wards      polygon   2 records   fields: WardID, WardName
      Roads      line      1 record    fields: RoadID, RoadName
      Requests   point     6 records   fields: RequestID, Category, Priority, Status,
                                               Reported, Closed, Channel, Note
      Assets     point     3 records   fields: AssetID, Type, Installed, LastInspected, Condition</pre>
    </div>
    <div class="tabpanel">
      <p><strong>ArcGIS Pro project</strong> (as read from the Contents and Catalog panes and layer properties).</p>
<pre class="listing">Project file:  C:\Projects\RequestTracker\RequestTracker.aprx   (last saved 2026-09-15 by gis.author)

Map "Request Overview" — Contents pane, top to bottom:
   Requests   symbol: coloured by Status (<span class="hl">Open = red</span>, In progress = orange,
                       Reopened = purple, Resolved/Closed = grey)   labels: RequestID
   Assets     symbol: single symbol, small green circle
   Roads      symbol: dark grey line, 2 pt
   Wards      symbol: no fill, black outline
   (no basemap — the fixture has no Earth location)

Layer sources (Properties > Source):
   Requests → <span class="hl">\\gis-files\training\Municipal_Training.gdb\Requests</span>
   Assets   → \\gis-files\training\Municipal_Training.gdb\Assets
   Roads    → \\gis-files\training\Municipal_Training.gdb\Roads
   Wards    → \\gis-files\training\Municipal_Training.gdb\Wards

Catalog pane > Portals: signed in to the training ArcGIS Online organisation as gis.author</pre>
    </div>
    <div class="tabpanel">
      <p><strong>Item cards</strong> (as read from each item’s details page, 2026-09-16).</p>
      <div class="table-wrap"><table>
        <thead><tr><th>Item</th><th>Type</th><th>Owner</th><th>Sharing</th><th>Notes on the item page</th></tr></thead>
        <tbody>
          <tr><td class="mono">Requests_Training</td><td>Hosted feature layer</td><td>gis.author</td><td>Group: <em>Inspection Team – Training</em></td><td>Published from ArcGIS Pro on 2026-09-15 with <strong>copy all data</strong>. Editing: <strong>enabled</strong> — add, update (geometry and attributes), delete. Description: “Authoritative request records from 2026-09-15. The file geodatabase copy is archived and must not be edited.”</td></tr>
          <tr><td class="mono">Requests_Training_public</td><td>Hosted feature layer <strong>view</strong></td><td>gis.author</td><td>Everyone (public)</td><td>View of Requests_Training. Editing: <strong>disabled</strong>. Fields excluded from the view: <code>Channel</code>, <code>Note</code>.</td></tr>
          <tr><td class="mono">Municipal_Base_Training</td><td>Hosted feature layer (3 layers: Wards, Roads, Assets)</td><td>gis.author</td><td>Group: <em>Inspection Team – Training</em></td><td>Published 2026-09-15 with copy all data. Editing: <em style="color:var(--warn)">(the editing settings panel was not captured)</em>.</td></tr>
          <tr><td>Request Overview (Training)</td><td>Web map</td><td>gis.author</td><td>Group: <em>Inspection Team – Training</em></td><td>Layers: Requests_Training (styled by Status; pop-up shows RequestID, Category, Status, Reported), Municipal_Base_Training. Basemap: none.</td></tr>
          <tr><td>Request Overview (Public)</td><td>Web map</td><td>gis.author</td><td>Everyone (public)</td><td>Layers: Requests_Training_public (single symbol; pop-up shows RequestID, Category, Status). Basemap: none.</td></tr>
          <tr><td>Request Monitoring (Training)</td><td>Dashboard</td><td>gis.author</td><td>Organization</td><td>Built on <em>Request Overview (Training)</em>. Elements: indicator “Open requests”, list sorted by Reported, map.</td></tr>
          <tr><td>Request Locator (Training)</td><td>Instant App</td><td>gis.author</td><td>Everyone (public)</td><td>Built on <em>Request Overview (Public)</em>. Tools: search, legend, zoom.</td></tr>
          <tr><td>(Field Maps) Request Overview (Training)</td><td>Web map configured in Field Maps Designer</td><td>gis.author</td><td>(inherits the web map’s sharing: group)</td><td>Form on Requests_Training: Status (choice list), Note (text). Offline: enabled. <em style="color:var(--warn)">(Whether the form also writes to a related inspection table was not captured.)</em></td></tr>
        </tbody></table></div>
    </div>
    <div class="tabpanel">
      <p><strong>Members and group</strong> (as read from the organisation’s Members and Groups pages).</p>
      <table>
        <thead><tr><th>Member</th><th>User type</th><th>Role</th><th>Groups</th></tr></thead>
        <tbody>
          <tr><td class="mono">gis.author</td><td>Creator</td><td>Publisher</td><td>Inspection Team – Training (owner)</td></tr>
          <tr><td class="mono">crew01</td><td>Mobile Worker</td><td>Data Editor</td><td>Inspection Team – Training</td></tr>
          <tr><td class="mono">mgr.ops</td><td>Viewer</td><td>Viewer</td><td>Inspection Team – Training</td></tr>
          <tr><td class="mono">intern01</td><td>Viewer</td><td>Viewer</td><td><em>(none)</em></td></tr>
          <tr><td class="mono">org.admin</td><td>Creator</td><td>Administrator</td><td><em style="color:var(--warn)">(not captured)</em></td></tr>
        </tbody></table>
    </div>
    <div class="tabpanel">
      <p><strong>Application behaviour</strong> (observed by the instructor on 2026-09-16 and transcribed).</p>
      <ol>
        <li><code>crew01</code> opens Field Maps on a phone, sees <em>Request Overview (Training)</em>, taps P2, changes Status from <em>In progress</em> to <em>Resolved</em> in the form, submits. Thirty seconds later the dashboard’s “Open requests” indicator — which counts Status in (<em>Open</em>, <em>In progress</em>, <em>Reopened</em>) — drops by one.</li>
        <li>An anonymous visitor opens <em>Request Locator (Training)</em> without signing in, sees six points, clicks P2; the pop-up shows Status <em>Resolved</em>. No <em>Note</em> or <em>Channel</em> appears, and neither field is present in the layer’s field list.</li>
        <li><code>mgr.ops</code> signs in and opens the dashboard; it loads with indicator, list and map. <code>intern01</code> signs in and opens a link to <em>Request Overview (Training)</em>; the page reports the item is not accessible. <em style="color:var(--warn)">(What intern01 sees when opening the dashboard link was not captured.)</em></li>
        <li><code>gis.author</code> opens <code>RequestTracker.aprx</code> in ArcGIS Pro. The Requests layer there still shows P2 as <em>In progress</em>.</li>
      </ol>
    </div>
  </div>

  <h2><span class="mod">2.7.3</span>Steps 1–3: read, locate, explain</h2>
  <div class="card lab-form">
    <p><strong>Step 1.</strong> Read all five tabs once before writing anything. Note every place that says “not captured”.</p>
    <label>Step 2 — Which copy of the request records is authoritative <em>according to the organisation</em>? (one sentence)</label><textarea data-k="s2a"></textarea>
    <label>Step 2 — What evidence supports that? (one sentence, cite E-cards)</label><textarea data-k="s2b"></textarea>
    <label>Step 3 — Why does the ArcGIS Pro project still show P2 as In progress (E5 item 4)? Which module predicted it?</label><textarea data-k="s3"></textarea>
  </div>

  <h2><span class="mod">2.7.3</span>Step 4: where is it managed? (Table 2.7)</h2>
  <p>For each row choose the item or file where the thing is managed, the evidence card, and your confidence. Use <strong>not enough information</strong> whenever the pack is silent.</p>
  <div class="try">
    <span class="tag">Fill in</span>
    <div class="table-wrap"><table class="trace" id="trace"></table></div>
    <div class="controls"><button class="btn primary" id="saveLab">Save my answers</button> <button class="btn ghost" id="clearLab">Clear</button> <span class="saved" id="labSaved"></span></div>
  </div>

  <h2><span class="mod">2.7.3</span>Step 5: the annotated sketch</h2>
  <p>Label every arrow. An arrow either means <em>references</em> (points at the other item; no copy of the data) or <em>copy made on 2026-09-15</em> (the data was duplicated at that moment). Then tick the boxes that hold data.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <figure class="map-fig" id="sketchFig"></figure>
    <div class="two-col">
      <div><h4 style="margin-top:0">Arrows</h4><div id="arrowForm"></div></div>
      <div><h4 style="margin-top:0">Which boxes hold data?</h4><div id="dataForm" class="checklist"></div></div>
    </div>
    <div class="controls"><button class="btn" id="checkSketch">Check my sketch</button></div>
    <div class="result" id="sketchOut">Fill in the arrows and boxes, then check.</div>
  </div>
  <p class="small">On your paper sketch also add the group tag, the four members, and a <strong>?</strong> on anything the evidence does not establish. Draw the project’s arrow to the <em>archived file</em>, not to the hosted layer.</p>

  <h2><span class="mod">2.7.3</span>Steps 6–7: role comparison and the gaps</h2>
  <div class="card lab-form">
    <p><strong>Step 6.</strong> For each job, name (a) the S-2 component that covers it, (b) one non-Esri product from module 2.6 that covers the same job, (c) one thing to re-check if S-2 were rebuilt on that product.</p>
    <div class="table-wrap"><table id="roleTable" class="trace"></table></div>
    <label>Step 7 — For every “not enough information” row above, which single screen or setting would resolve it?</label><textarea data-k="s7" style="min-height:110px"></textarea>
  </div>

  <h2><span class="mod">2.7.4</span>Self-check before you submit</h2>
  <p>All of these must be true of your work. The page checks the ones it can; the rest are yours.</p>
  <div class="card checklist" id="checks">
    <label><input type="checkbox" data-k="c1"> Exactly one item is named as holding the authoritative request data — a hosted item, not the <code>.aprx</code> and not the file geodatabase.</label>
    <label><input type="checkbox" data-k="c2"> My sketch shows <strong>two</strong> copies of the request records (archived file, hosted layer) and <strong>one</strong> view that is <em>not</em> a copy.</label>
    <label><input type="checkbox" data-k="c3"> My sketch shows no data inside any web map or app.</label>
    <label><input type="checkbox" data-k="c4"> At least three rows of Table 2.7 are marked “not enough information” — the pack has deliberate gaps; resolving every row means I guessed.</label>
    <label><input type="checkbox" data-k="c5"> The public app’s hidden fields are attributed to the <strong>view</strong>, not to the web map or the Instant App.</label>
    <label><input type="checkbox" data-k="c6"> The dashboard count logic is attributed to the <strong>dashboard configuration</strong>, not the data.</label>
    <label><input type="checkbox" data-k="c7"> My role table has five rows and a re-check item in every row — a blank column (c) means I assumed a one-to-one replacement.</label>
  </div>
  <div class="result" id="autoCheck"></div>

  <h2><span class="mod">2.7.5</span>Troubleshooting</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Symptom</th><th>Likely cause</th><th>What to do</th></tr></thead>
    <tbody>
      <tr><td>“I can’t tell whether the public map or the view hides the fields.”</td><td>Mixing up presentation (pop-up field list) with data exposure (fields excluded from the view)</td><td>Re-read E5 item 2: the fields are absent from the <em>layer’s field list</em> — a view property. The pop-up merely chooses among fields that exist.</td></tr>
      <tr><td>“The ArcGIS Pro project must be wrong — it shows old data.”</td><td>Expecting a project to hold data (2.2.2)</td><td>The project references the archived file; it is <em>correct</em> and <em>stale</em> (out of date) at once. Note it as a risk: an author could edit the wrong copy by mistake.</td></tr>
      <tr><td>“I marked everything as shown.”</td><td>Reading confidence off the pack’s tone, not its content</td><td>Each “not captured” note in E3/E4/E5 should produce at least one “not enough information”.</td></tr>
      <tr><td>“I put OpenLayers as the replacement for ArcGIS Online.”</td><td>Comparing brands, not roles (2.6.1)</td><td>OpenLayers covers R5 only; split ArcGIS Online’s jobs (R2, R3, R4, part of R5) into separate rows.</td></tr>
      <tr><td>“The dashboard should count P6 as open.”</td><td>Misreading the count rule</td><td>E5 item 1 states the rule: Status in (Open, In progress, Reopened). P6 is <em>Closed – duplicate</em> and is excluded by the rule, whatever one thinks of the policy.</td></tr>
    </tbody></table></div>

  <h2><span class="mod">2.7.6</span>Deliverables</h2>
  <ol>
    <li>Labelled architecture sketch (one page): components, arrows labelled <em>references</em> / <em>copy</em>, question marks on unknowns.</li>
    <li>Completed Table 2.7 with evidence and confidence.</li>
    <li>Role comparison table (five rows, three columns).</li>
    <li>“Not enough information” list with, for each entry, the screen or setting that would resolve it.</li>
    <li>Two sentences for step 2 and one paragraph for step 3.</li>
  </ol>
  <details class="reveal"><summary>Open-source variant of this lab (for comparison; not execution-tested)</summary>
    <p>Replace the evidence: the data is a GeoPackage <code>municipal_training.gpkg</code>; the project is <code>request_tracker.qgz</code> (also references, not data); the request table is loaded into PostgreSQL + PostGIS; GeoServer publishes it as WFS and the base layers as WMS; an OpenLayers page draws the WFS layer and opens a pop-up; there is <strong>no portal</strong> — access is set in GeoServer’s own security settings, and the “public” page is just a URL.</p>
    <p><strong>Same:</strong> steps 2–7. <strong>Different, and you must say so:</strong> there are no items, sharing levels, groups or view items. The row “the public cannot see Note and Channel” has no single answer like a hosted view — it might be a database view in PostGIS, a GeoServer layer setting, or the OpenLayers page simply not <em>displaying</em> the fields (which would <strong>not</strong> hide them from the service). Say which the evidence shows; if none, “not enough information” — and note that “the page doesn’t show it” is presentation, not access control. That difference is module 2.6.2 made concrete.</p>
  </details>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const KEY = "gis-ch2-lab";
  const saved = JSON.parse(localStorage.getItem(KEY) || "{}");
  const places = ["— choose —", "Archived file geodatabase (Municipal_Training.gdb)", "ArcGIS Pro project (.aprx)", "Hosted feature layer Requests_Training", "Hosted feature layer view Requests_Training_public", "Hosted feature layer Municipal_Base_Training", "Web map Request Overview (Training)", "Web map Request Overview (Public)", "Dashboard Request Monitoring", "Instant App Request Locator", "Field Maps form (Field Maps Designer)", "Group / member settings (sharing, role)", "Esri-operated ArcGIS Online infrastructure", "Not enough information"];
  const evid = ["—", "E1", "E2", "E3", "E4", "E5", "E2+E5", "E3+E4", "E3+E5", "E1+E3"];
  const conf = ["—", "shown", "inferred", "not enough information"];
  const rows = ["The geometry of the six request points the public app draws", "The geometry of the request points gis.author sees in ArcGIS Pro", "The red colour of Open requests in the team web map", "The single symbol used in the public map", "The decision that the public can see request locations", "The decision that the public cannot see Note and Channel", "The decision that crew01 may change a request’s status", "The decision that intern01 cannot open the team web map", "Whether intern01 can use the dashboard", "Whether crew01 may edit ward boundaries", "The “Open requests” count logic", "The choice list crew01 sees for Status on the phone", "Whether the phone form also writes an inspection record", "Which machines host Requests_Training and who patches them"];
  const sel = (k, opts) => `<select data-k="${k}">${opts.map(o => `<option>${o}</option>`).join("")}</select>`;
  document.getElementById("trace").innerHTML = `<thead><tr><th>Thing to locate</th><th style="min-width:220px">Managed in</th><th>Evidence</th><th>Confidence</th></tr></thead><tbody>` + rows.map((r, i) => `<tr><td>${r}</td><td>${sel("p" + i, places)}</td><td>${sel("e" + i, evid)}</td><td>${sel("c" + i, conf)}</td></tr>`).join("") + "</tbody>";
  document.getElementById("roleTable").innerHTML = `<thead><tr><th>Job</th><th>(a) S-2 component</th><th>(b) non-Esri product</th><th>(c) re-check if rebuilt</th></tr></thead><tbody>` + RESP.map(r => `<tr><td><strong>${r.id}</strong> ${r.title}</td><td><input type="text" data-k="ra${r.id}"></td><td><input type="text" data-k="rb${r.id}"></td><td><input type="text" data-k="rc${r.id}"></td></tr>`).join("") + "</tbody>";

  const fields = () => document.querySelectorAll("[data-k]");
  fields().forEach(f => { const v = saved[f.dataset.k]; if (v == null) return; if (f.type === "checkbox") f.checked = !!v; else f.value = v; });
  function save() { const o = {}; fields().forEach(f => o[f.dataset.k] = f.type === "checkbox" ? f.checked : f.value); localStorage.setItem(KEY, JSON.stringify(o)); document.getElementById("labSaved").textContent = "Saved " + new Date().toLocaleTimeString(); autoCheck(); }
  document.getElementById("saveLab").addEventListener("click", save);
  document.getElementById("clearLab").addEventListener("click", () => { if (confirm("Clear all saved lab answers?")) { localStorage.removeItem(KEY); location.reload(); } });
  document.getElementById("checks").addEventListener("change", save);
  document.getElementById("trace").addEventListener("change", save);

  function autoCheck() {
    const nei = rows.filter((_, i) => (document.querySelector(`[data-k="c${i}"]`).value === "not enough information") || (document.querySelector(`[data-k="p${i}"]`).value === "Not enough information")).length;
    const filled = rows.filter((_, i) => document.querySelector(`[data-k="p${i}"]`).selectedIndex > 0).length;
    const roleOk = RESP.every(r => document.querySelector(`[data-k="rc${r.id}"]`).value.trim().length > 0);
    document.getElementById("autoCheck").innerHTML = `<strong>Automatic checks:</strong> ${filled}/${rows.length} rows filled · ${nei} row(s) marked “not enough information” ${nei >= 3 ? '<span class="chip yes">≥ 3 ✓</span>' : '<span class="chip no">need at least 3</span>'} · role table column (c) ${roleOk ? '<span class="chip yes">complete ✓</span>' : '<span class="chip no">has blanks</span>'}. <span class="small">The page does not judge which rows you chose — your instructor does.</span>`;
  }
  autoCheck();

  /* sketch: label arrows and tick data boxes */
  const arrows = S2.refs.map(r => ({ from: r[0], to: r[1], truth: r[4] ? "copy" : "references" }));
  const name = id => S2.items.find(i => i.id === id).title;
  document.getElementById("arrowForm").innerHTML = arrows.map((a, i) => `<label style="display:flex;gap:.5rem;align-items:center;margin:.3rem 0;font-size:.9rem"><span style="flex:1"><code>${name(a.from)}</code> → <code>${name(a.to)}</code></span><select data-a="${i}"><option value="">—</option><option value="references">references</option><option value="copy">copy made 2026-09-15</option></select></label>`).join("");
  document.getElementById("dataForm").innerHTML = S2.items.map(i => `<label><input type="checkbox" data-d="${i.id}"> ${i.kind}: <code>${i.title}</code></label>`).join("");
  function drawSketch() { renderItems(document.getElementById("sketchFig"), { labels: false, caption: "Solution S-2 (made up). Label the arrows using the form below." }); }
  drawSketch();
  document.getElementById("checkSketch").addEventListener("click", () => {
    let wrongA = 0, blankA = 0;
    arrows.forEach((a, i) => { const v = document.querySelector(`[data-a="${i}"]`).value; if (!v) blankA++; else if (v !== a.truth) wrongA++; });
    let wrongD = 0;
    S2.items.forEach(i => { const c = document.querySelector(`[data-d="${i.id}"]`).checked; if (c !== !!i.data) wrongD++; });
    const ok = !wrongA && !blankA && !wrongD;
    document.getElementById("sketchOut").innerHTML = (ok ? '<div class="verdict ok">Sketch is consistent with the evidence.</div>' : '<div class="verdict no">Not yet.</div>') +
      `Arrows: ${blankA} blank, ${wrongA} wrong. Data boxes: ${wrongD} wrong. ` +
      (wrongA || blankA ? "Only one arrow is a <em>copy</em> — the publish step on 2026-09-15 from the archived file to the hosted layer. Everything else <em>references</em>. " : "") +
      (wrongD ? "Exactly three boxes hold data: the archived file geodatabase and the two hosted feature layers. A view holds no copy; maps and apps hold configuration only." : "");
  });
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
