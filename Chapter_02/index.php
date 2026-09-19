<?php $page = ['title' => 'Chapter 2 — Understanding the ArcGIS ecosystem', 'chapter' => 2]; require __DIR__ . '/../partials/head.php'; ?>
  <section class="hero">
    <div class="fade-up">
      <div class="page-head" style="padding:0">
        <div class="eyebrow">GIS Phase 1 · Chapter 2 · Interactive tutorial</div>
      </div>
      <h1 class="big">Where does each<br>part of the job <em>live</em>?</h1>
      <p class="lead">Chapter 1 gave you a working method: decision → question → data → operation → validation → communication. This chapter answers a practical question: <strong>which software does each step, and where does the data actually sit?</strong> You will meet the ArcGIS products by the <em>job</em> each one does — and see how the same jobs are done with QGIS, PostGIS, GeoServer, OpenLayers and Mapbox.</p>
      <p><a class="btn primary" href="m1-responsibilities.php">Begin module 2.1 →</a> &nbsp; <a class="btn ghost" href="glossary.php">Glossary</a></p>
      <p class="small">No software, no account and no licence is needed. Nothing on these pages publishes anything or uses up credits (Esri’s pay-as-you-go usage units). Every deployment, file name, user and item shown is <span class="synthetic">made-up practice material</span>. Your progress is saved in this browser only.</p>
    </div>
    <figure class="map-fig fade-up" id="heroFig"></figure>
  </section>

  <h2 style="border-top:0;margin-top:1rem"><span class="mod">The big idea</span>Five jobs, many products</h2>
  <div class="grid-2">
    <div class="card">
      <p>A map on a screen looks like “the system”. It is not. Behind every working GIS solution there are <strong>five separate jobs</strong>:</p>
      <ol>
        <li><strong>Desktop authoring</strong> — skilled people create, fix and style data.</li>
        <li><strong>Data storage</strong> — the official copy is kept somewhere.</li>
        <li><strong>Service delivery</strong> — other machines fetch it over the network.</li>
        <li><strong>Content organisation</strong> — maps, layers and apps are catalogued and shared with the right people.</li>
        <li><strong>User applications</strong> — an inspector, a manager or a citizen uses the result.</li>
      </ol>
      <p>Learn the five jobs once, and every product — Esri’s or anyone else’s — becomes easy to place.</p>
    </div>
    <div class="card">
      <h4 style="margin-top:0">Our practice town continues</h4>
      <p>Same imaginary city office (municipal corporation / nagar palika) as Chapter 1: two wards, one road, six citizen <strong>requests</strong> (complaints), three <strong>assets</strong>. Nothing has changed in the data.</p>
      <p>New this chapter: a made-up deployment called <strong>Solution S-2, “Request Tracker (Training)”</strong>. It has a file the author edited, an ArcGIS Pro project, some ArcGIS Online items, a dashboard, a phone app and a public web page. In the lab you will trace one request record through all of it and say where each decision is made — or say “not enough information” when the evidence does not show it.</p>
      <p class="small">Names like <code>gis.author</code>, <code>Requests_Training</code> and <code>\\gis-files\training\</code> are invented.</p>
    </div>
  </div>

  <h2><span class="mod">Modules</span>Work through them in order</h2>
  <div class="modcards" id="modcards"></div>

  <h2><span class="mod">Plain words</span>A few words you will see everywhere</h2>
  <div class="card"><table>
    <tr><th>Authoritative copy</th><td>The one copy of a dataset the office has <em>decided</em> is the official source of truth — like the original register kept at the ward office; the photocopies people carry around are not the record. Not a technical property — a decision.</td></tr>
    <tr><th>Web service</th><td>An address on the network (a URL) that hands out data or map images when asked. The way phones and browsers get GIS data.</td></tr>
    <tr><th>Publish</th><td>Turn a dataset into a web service (and, in ArcGIS, an item). Sometimes it <em>copies</em> the data to the cloud, sometimes it points at the data where it already is.</td></tr>
    <tr><th>Item</th><td>Anything catalogued in ArcGIS Online: a layer, a map, an app. It has an owner, a description and a sharing level.</td></tr>
    <tr><th>Configure vs build</th><td><em>Configure</em> = switch on and arrange things the product already does. <em>Build</em> = write your own application code against the services.</td></tr>
    <tr><th>Desktop GIS</th><td>A program installed on an ordinary computer or laptop for creating and editing GIS data — ArcGIS Pro, QGIS.</td></tr>
  </table></div>

  <h2><span class="mod">Outcomes</span>What you will be able to do</h2>
  <div class="card">
    <table>
      <tr><th>LO1</th><td>Name the five jobs and explain why the machine running an app is not the system holding the official data.</td></tr>
      <tr><th>LO2</th><td>Say what ArcGIS Pro is for, and why a saved project is not the datasets it points at.</td></tr>
      <tr><th>LO3</th><td>Recognise ArcGIS Online items (layer, view, web map, group, member, app) and explain why seeing a public map is not permission to edit or reuse its data.</td></tr>
      <tr><th>LO4</th><td>Explain what an organisation takes on when it runs ArcGIS Enterprise, and tell <em>using</em> a platform from <em>operating</em> it.</td></tr>
      <tr><th>LO5</th><td>Choose between configuring an app builder, extending it, or building with the JavaScript SDK, the Python API or ArcPy — from the requirement.</td></tr>
      <tr><th>LO6</th><td>Place OpenLayers, Mapbox, QGIS, PostGIS and GeoServer by the job each covers, without claiming one-to-one replacements.</td></tr>
      <tr><th>LO7</th><td>Trace a prepared solution from source file to app, and say “not enough information” when the evidence is missing.</td></tr>
    </table>
  </div>
  <p class="small">Source: <em>GIS_Phase_1_Chapter_02_Understanding_the_ArcGIS_Ecosystem.md</em>, revision 1.0 (19 September 2026). This tutorial follows that document; formal assessment answers are held by the instructor and are not on these pages. Product statements were checked against official documentation on that date and can change.</p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  renderResp(document.getElementById("heroFig"), { mode: "generic", caption: "The five jobs (schematic). Not a product diagram — a way of thinking." });
  const prog = getProgress();
  const blurbs = {
    "2.1": "Authoring, storage, services, catalogue, apps. Follow one dataset through all five. Why your phone is not the database.",
    "2.2": "What ArcGIS Pro does. A project file points at data — it does not contain it. Changing data vs changing how it looks.",
    "2.3": "Items, layers, views, web maps, groups, members. Only one box holds data. Visible ≠ editable ≠ reusable.",
    "2.4": "Same content ideas, very different responsibility. Portal, Server, Data Store. Using a platform vs operating one.",
    "2.5": "Instant Apps, Dashboards, Field Maps. JavaScript SDK vs Python API vs ArcPy. Configure, extend, or build?",
    "2.6": "OpenLayers, Mapbox, QGIS, PostGIS, GeoServer — by job, not by brand. The gap nobody notices.",
    "2.7": "Trace Solution S-2 from file to public app. Fill the ‘where is it managed?’ table. Label the arrows.",
    "2.8": "The assessment you submit to your instructor, and the progression rule.",
    "2.9": "Recap, media notes, and the question that leads into Chapter 3."
  };
  document.getElementById("modcards").innerHTML = MODULES.map(m => `<a class="modcard ${prog[m.id] ? "done" : ""}" href="${m.file}"><span class="done-badge">done</span><div class="id">MODULE ${m.id}</div><h3>${m.title}</h3><p>${blurbs[m.id]}</p></a>`).join("");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
