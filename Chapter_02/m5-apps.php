<?php $page = ['title' => '2.5 Configurable apps, APIs and SDKs', 'chapter' => 2, 'module' => '2.5']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 2.5 · Platform-specific (Esri)</div>
    <h1>Configure, extend, or build?</h1>
    <p class="lead">Most GIS apps are never coded — they are configured. When you do code, three Esri tools do three different jobs. Choose from the requirement, not from what you enjoy writing.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Meet the configurable builders: Instant Apps, Experience Builder, Dashboards, Field Maps.</li>
      <li>Tell the ArcGIS Maps SDK for JavaScript, the ArcGIS API for Python and ArcPy apart by <em>role</em>.</li>
      <li>Use a three-step worksheet to decide configure / extend / build.</li></ul></div>
  </div>

  <h2><span class="mod">2.5.1</span>Configurable builders: arranging what already exists</h2>
  <p><dfn title="Selecting and arranging capabilities a product already supports — templates, widgets, settings — without writing application code.">Configuring</dfn> an app means choosing a template, switching widgets on (ready-made building blocks such as a map, a chart, a search box, a list), pointing them at a map or layer, and setting labels and filters. The product’s authors decided what is <em>possible</em>; you decide what is <em>used</em>. Click a builder to see Esri’s description and what it would do for our town.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <div class="controls" id="bBtns">
      <button class="btn" data-b="ia">Instant Apps</button>
      <button class="btn" data-b="exb">Experience Builder</button>
      <button class="btn" data-b="dash">Dashboards</button>
      <button class="btn" data-b="fm">Field Maps</button>
    </div>
    <div class="result" id="bOut">Pick a builder.</div>
  </div>
  <p>Notice that every builder works <em>on top of</em> a web map or layers (module 2.3). The builder stores an application item; the data stays in the layer. Configuring an app never changes the data model, and switching builders usually leaves the map and layers untouched.</p>
  <div class="callout note"><span class="label">A retired builder</span><p>Esri’s developer documentation lists <strong>ArcGIS Web AppBuilder</strong> as retired. You will still see it in old tutorials and existing deployments. Do not choose it for new work; treat any material that assumes it as dated.</p></div>
  <div class="callout dev"><span class="label">Developer view</span><p>Configuring a builder is like building a page in a website builder such as WordPress or Wix: pick blocks, set options, publish. <strong>Where it stops:</strong> at the edge of the supported capability. A website builder usually lets you add a plugin you write yourself; some ArcGIS builders have one (Experience Builder can be extended with custom widgets), others do not. Knowing where that edge lies is the whole skill of 2.5.3.</p></div>
  <div class="callout warn"><span class="label">Misconception</span><p>“Configurable means limited, so real developers build custom.” Teams write and maintain code for a dashboard that a configured product already does — and then own every bug and every upgrade themselves.</p></div>

  <h2><span class="mod">2.5.2</span>When you do write code: three tools, three jobs</h2>
  <p><strong>The foundation is services.</strong> Everything a client does with ArcGIS Online or Enterprise — query features, edit them, fetch tiles, list items — is a request to a web service. Esri’s developer site lists them: a <em>feature service</em> lets you “add, update, delete, and query feature data”; a <em>portal service</em> lets you “securely store, access, and manage content items, users, and groups”. The SDKs below are convenient, supported ways of calling those services. They do not add powers the services lack.</p>
  <div class="grid-3">
    <div class="card"><h4 style="margin-top:0">ArcGIS Maps SDK for JavaScript</h4><p class="small"><span class="pill">the user’s side</span></p><p>“A developer product for building mapping and spatial analysis applications for the web” — 2D and 3D, with ready-made web components such as <code>&lt;arcgis-map&gt;</code>. Runs in the <strong>browser</strong>.</p><p class="small"><strong>Town example:</strong> a custom public page where residents submit a request with your own UI and workflow, writing to the request feature service.<br><strong>Not:</strong> a data store, a server, an admin tool.</p></div>
    <div class="card"><h4 style="margin-top:0">ArcGIS API for Python</h4><p class="small"><span class="pill">the platform’s side</span></p><p>The <code>arcgis</code> package: “GIS organization administration”, “content management”, “spatial analysis and data science” against ArcGIS Online and Enterprise, often from Jupyter notebooks. Runs on <strong>any machine that can reach the portal</strong>.</p><p class="small"><strong>Town example:</strong> a nightly script that lists every public item and checks the public view still hides the Note field.<br><strong>Not:</strong> a browser UI toolkit; not tied to ArcGIS Pro.</p></div>
    <div class="card"><h4 style="margin-top:0">ArcPy</h4><p class="small"><span class="pill">the desktop / server tool’s side</span></p><p>“A Python site package that provides a useful and productive way to perform geographic data analysis, data conversion, data management, and map automation.” Runs where <strong>ArcGIS Pro</strong> is installed (“inside a conda environment, which ArcGIS Pro uses” — a Python setup that ArcGIS Pro installs and manages) — or on an <strong>ArcGIS Server</strong>, which also includes ArcPy.</p><p class="small"><strong>Town example:</strong> a weekly script that validates the Assets dataset, computes a field and exports a map layout.<br><strong>Not:</strong> a web SDK; cannot run without ArcGIS Pro or Server.</p></div>
  </div>
  <p class="small">Two more families, one sentence each: the <strong>ArcGIS Maps SDKs for native apps</strong> (Kotlin, Swift, .NET, Flutter, Qt, Java…) build installed mobile and desktop apps; and Esri lists open-source browser libraries — Leaflet, MapLibre GL JS, OpenLayers, CesiumJS — that “can be used to build web apps with ArcGIS services”. That last point matters for module 2.6.</p>

  <h3>Match the task to the tool</h3>
  <div class="try sorter" data-items='[{"t":"List every item in the organisation shared with everyone","bin":"Python API","why":"an inventory of portal items over the network"},{"t":"Run a geoprocessing tool (a ready-made ArcGIS data-processing tool, e.g. buffer) on a file geodatabase from a scheduled script on a machine with ArcGIS Pro","bin":"ArcPy","why":"geoprocessing where Pro is installed"},{"t":"Add a custom drawing tool to a browser page residents use","bin":"JavaScript SDK","why":"browser UI"},{"t":"Regenerate a fixed PDF map layout every Monday","bin":"ArcPy","why":"map automation from a project"},{"t":"Show a 3D scene with clickable buildings on a website","bin":"JavaScript SDK","why":"browser, 2D/3D"},{"t":"Clone a web map and its layers into another organisation","bin":"Python API","why":"content management across portals"}]'>
    <span class="tag">Try it</span>
    <div class="items"></div>
    <div class="bins">
      <div class="bin" data-bin="JavaScript SDK"><h5>JavaScript SDK</h5></div>
      <div class="bin" data-bin="Python API"><h5>Python API (arcgis)</h5></div>
      <div class="bin" data-bin="ArcPy"><h5>ArcPy</h5></div>
    </div>
  </div>
  <div class="callout warn"><span class="label">Misconception</span><p>“ArcPy is the ArcGIS Python API.” They are different packages with different jobs and different installation needs. Installing <code>arcgis</code> on a server and expecting geoprocessing tools fails; expecting ArcPy to work in a plain Python environment with no ArcGIS Pro also fails.</p></div>
  <div class="callout dev"><span class="label">Developer view</span><p>JS SDK ≈ a front-end framework with a map component; Python API ≈ a cloud provider’s admin SDK; ArcPy ≈ a build tool’s scripting layer that only works where the tool is installed. <strong>Where it stops:</strong> all three read and write the <em>same</em> items and services, so a change made through one is visible through the others at once — there is no separate “API copy” of the data.</p></div>

  <h2><span class="mod">2.5.3</span>The decision: configure, extend, or build</h2>
  <p>Cheaper options are considered first: try step 1, and only if it fails move to step 2, then step 3. Work each case with the ladder, then read the reasoning.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <div class="controls"><label>Case: <select id="caseSel">
      <option value="0">A · Managers want live counts of open requests per ward on a control-room screen</option>
      <option value="1">B · Crews update request status on a phone, sometimes offline, and attach an inspection</option>
      <option value="2">C · Residents submit requests from a page inside the city’s citizen website, using that website’s own login, with a 3-step form and a check for nearby duplicate complaints</option>
    </select></label></div>
    <div class="ladder" id="ladder">
      <div class="rung" data-r="0"><h4>1 · Configure</h4><p class="small">Can a builder’s <em>supported</em> capability meet the stated requirement — not a near miss?</p></div>
      <div class="rung" data-r="1"><h4>2 · Extend</h4><p class="small">Is the gap small and confined, with a documented extension mechanism?</p></div>
      <div class="rung" data-r="2"><h4>3 · Build</h4><p class="small">Is the gap the <em>core</em> of the requirement — a workflow, UI or integration the builders do not model?</p></div>
    </div>
    <div class="controls" style="margin-top:.8rem"><span>Your call:</span> <button class="btn" data-pick="0">Configure</button> <button class="btn" data-pick="1">Extend</button> <button class="btn" data-pick="2">Build</button></div>
    <div class="result" id="caseOut">Pick a case and make your call.</div>
  </div>
  <p>Three checks apply to every step: <strong>ownership</strong> (who fixes it in two years?), <strong>upgrade risk</strong> (what happens when the platform changes?) and <strong>evidence</strong> (has someone confirmed, on the current release, that the capability exists — a search snippet does not count?). If none of the three steps fits, the requirement is unclear: rewrite it.</p>

  <div class="quiz" data-answer="0" data-fb="“Looks too simple” is developer preference. The worksheet asks first which stated requirement the template fails to meet (evidence: the requirement list matched against the template’s documented tools), and who will own and upgrade a custom app in two years.">
    <div class="q">A colleague proposes a custom React app because “Instant Apps look too simple”. What do you ask first?</div>
    <div class="opts">
      <button class="opt">Which stated requirement does the template fail to meet, and who will own the custom code in two years?</button>
      <button class="opt">Which JavaScript framework is fastest?</button>
      <button class="opt">Whether the Python API could do it instead</button>
      <button class="opt">Nothing — custom is always more flexible</button>
    </div><div class="fb"></div>
  </div>

  <div class="callout note"><span class="label">Comprehension check (write it down)</span><p>Using the ladder, write the two questions you would ask that colleague first, and state what evidence would settle each.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const B = {
    ia: "<strong>ArcGIS Instant Apps.</strong> Esri: “Share your maps as apps and provide your audience with an intuitive and focused experience”; apps come from “a gallery of app templates” with an express setup. <em>Town:</em> a public <strong>Request Locator</strong> — a focused viewer of the public request view with search and a legend, built from a template in minutes.",
    exb: "<strong>ArcGIS Experience Builder.</strong> Esri: “Create unique web experiences using flexible layouts, content, and widgets that interact with 2D and 3D data.” <em>Town:</em> an internal <strong>Inspection Console</strong> combining a map, a table of open requests, a chart by ward and a filter — laid out with widgets. Can be extended with custom widgets.",
    dash: "<strong>ArcGIS Dashboards.</strong> Esri: “A presentation of geographic information and data that allows you to monitor events, make decisions, inform others, and see trends”, using maps, lists, charts, gauges, indicators and tables. <em>Town:</em> <strong>Request Monitoring</strong> — count of open requests, a list sorted by age, a map.",
    fm: "<strong>ArcGIS Field Maps.</strong> Esri: “A mobile solution that allows mobile workers to explore maps, collect data, complete tasks, and share their location from the field.” Maps and forms are prepared in the <em>Field Maps Designer</em> web app and used in the mobile app, “online, offline, outdoors, and indoors”. <em>Town:</em> a crew opens the request map on a phone, updates Status through a form, and adds an inspection record to a related table."
  };
  document.getElementById("bBtns").addEventListener("click", e => { const b = e.target.closest("button"); if (!b) return; document.getElementById("bOut").innerHTML = B[b.dataset.b]; });

  const cases = [
    { best: 0, why: "A dashboard’s documented purpose is to “monitor events” with indicators, charts and maps. The requirement names no custom interaction. <strong>Configure (Dashboards).</strong> A custom build here would be preference, not requirement." },
    { best: 0, why: "Field Maps’ documentation covers exactly this — forms prepared in Field Maps Designer, working “online, offline”, adding records “to a related table (for inspection workflows)”. <strong>Configure (Field Maps).</strong> Not established by reading the overview: whether the specific form logic the crews want is supported. That is a verification item — the kind of check that can turn a configure into an extend or build." },
    { best: 2, why: "The embedding, the external login and the custom wizard with a pre-submission spatial check are the <em>core</em> of the requirement, not a gap at the edge. <strong>Build with the JavaScript SDK</strong> against the request feature service. “Extend Experience Builder with a custom widget” is acceptable only with evidence that embedding and login can be met that way." }
  ];
  const sel = document.getElementById("caseSel"), out = document.getElementById("caseOut");
  function lightRung(i) { document.querySelectorAll("#ladder .rung").forEach(r => r.classList.toggle("pick", +r.dataset.r === i)); }
  document.querySelectorAll("[data-pick]").forEach(b => b.addEventListener("click", () => { const c = cases[+sel.value], p = +b.dataset.pick; lightRung(c.best); out.innerHTML = (p === c.best ? '<div class="verdict ok">Agreed.</div>' : '<div class="verdict no">Look again.</div>') + c.why; }));
  sel.addEventListener("change", () => { lightRung(-1); out.textContent = "Make your call."; });
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
