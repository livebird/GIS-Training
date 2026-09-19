<?php $page = ['title' => '7.1 Model, encoding, container, service', 'chapter' => 7, 'module' => '7.1']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 7.1 · General idea, works on every platform</div>
    <h1>Four questions hiding in “send me the assets”</h1>
    <p class="lead">When a colleague asks for the assets, they might mean the <em>kind</em> of data, the <em>file type</em>, the <em>package</em> it comes in, or a <em>web address</em> to fetch it from. These are four different things. Most exchange problems come from answering the wrong one.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Tell apart a <strong>data model</strong>, an <strong>encoding</strong>, a <strong>container</strong> and a <strong>service</strong>, and place any file or link you meet on the right layer.</li>
      <li>Explain why a file name extension proves nothing about accuracy, completeness, freshness or fitness.</li>
      <li>Say what you get from a downloaded file versus a sign-in web link — and what can silently change in each.</li></ul></div>
  </div>

  <h2><span class="mod">7.1.1</span>The four layers</h2>
  <p>Read the table top to bottom as <em>what → how written → what holds it → how delivered</em>. Then play with the interactive below.</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Layer</th><th>Question it answers</th><th>Examples</th><th>What it does <em>not</em> decide</th></tr></thead>
    <tbody>
      <tr><td><strong>Data model</strong></td><td>How is the world represented?</td><td><strong>Vector</strong> (points, lines, polygons with attributes — Chapter 3); <strong>raster</strong> (a grid of cells — Chapter 4)</td><td>How the bytes are written or where they live</td></tr>
      <tr><td><strong>Encoding</strong></td><td>How is one dataset written down?</td><td>A comma-separated text file; a JSON text following GeoJSON rules; the binary layout of a <code>.shp</code>; the tags of a TIFF</td><td>Whether several datasets can travel together, or who may read it</td></tr>
      <tr><td><strong>Container</strong></td><td>What holds one or many datasets together, with shared bookkeeping?</td><td>A folder of shapefile files (a weak container); a <strong>GeoPackage</strong> (one SQLite file, many tables); a <strong>file geodatabase</strong> folder; an <strong>enterprise geodatabase</strong> inside PostgreSQL</td><td>Whether the data can be reached over a network</td></tr>
      <tr><td><strong>Service</strong></td><td>How do other computers get the data on request, with rules applied?</td><td>A hosted feature layer in ArcGIS Online (Chapter 2); a feature service on ArcGIS Server; a WFS endpoint on GeoServer</td><td>What the storage behind it is — the client often cannot tell</td></tr>
    </tbody></table></div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Where does this thing belong?</h3>
    <p>Click any item. The layer (or layers) it belongs to light up, and the note explains why. Notice that the same vector points can be written three ways, and that a container may or may not have a service in front of it.</p>
    <div class="controls" id="chips"></div>
    <div class="stack4" id="stack"></div>
    <div class="result" id="stackNote">Pick an item above.</div>
  </div>

  <div class="callout idea"><span class="label">Why a developer should care</span><p>Bugs get filed at the wrong layer. <em>“GeoJSON lost my Gujarati text”</em> is almost never a GeoJSON problem — JSON text is Unicode. It is usually a tool that saved the file in a non-UTF-8 code page, or a shapefile export that happened somewhere down the line. <em>“The service returns the wrong dates”</em> may be a service setting (which time zone was assumed when it was published — see 7.2) rather than a data-model fault. <strong>Name the layer before you debug.</strong></p></div>

  <h2><span class="mod">7.1.2</span>A file extension proves almost nothing</h2>
  <p>An extension like <code>.gpkg</code> or <code>.shp</code> is a <em>hint about the encoding</em> — nothing more. Treat it as a claim made by whoever named the file. Claims get checked.</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Property</th><th>Why the extension cannot prove it</th><th>What actually proves it</th></tr></thead>
    <tbody>
      <tr><td><strong>Accuracy</strong></td><td>A <code>.gpkg</code> made from a hand-drawn sketch is as rough as the sketch</td><td>The accuracy statement and capture method in the metadata (7.6)</td></tr>
      <tr><td><strong>Completeness</strong></td><td>A file can be a filtered extract, a half-finished download, or a write that stopped early</td><td>The publisher’s record count and coverage, compared with your own count (7.7)</td></tr>
      <tr><td><strong>Freshness</strong></td><td>A 2019 ward boundary saved yesterday has yesterday’s file date and 2019’s content</td><td>The edition date or the date the data was observed — not the file-system timestamp</td></tr>
      <tr><td><strong>Fitness for your job</strong></td><td>The right format for a quick web demo is the wrong one for a legal boundary archive</td><td>The recipient’s real requirement (7.3, 7.8)</td></tr>
      <tr><td><strong>Even the encoding itself</strong></td><td>A file renamed <code>.csv</code> may be tab-separated; a <code>.json</code> may not be GeoJSON; a <code>.tif</code> may have no georeferencing (7.4)</td><td>Opening it and looking</td></tr>
    </tbody></table></div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“It’s in a geodatabase, so it must be clean.”</em> A geodatabase <em>can</em> enforce rules (allowed values, relationships) — but only if someone defined the rules and loaded the data through them. An empty rule set enforces nothing. The box’s ability is not the same as the data’s quality.</p></div>

  <h2><span class="mod">7.1.3</span>A downloaded file versus a sign-in web link</h2>
  <p>Suppose the <code>Requests_Training</code> layer from Chapter 2 is offered to you two ways. Click each to compare. Neither is “better” — they answer different needs.</p>
  <div class="tabs"><button>You get a ZIP with requests.gpkg</button><button>You get a URL that needs sign-in</button></div>
  <div class="tabpanel">
    <div class="record">
      <div class="part geom"><h4>What you hold</h4>A <strong>copy</strong>, frozen at the moment you downloaded it.</div>
      <div class="part attr"><h4>Freshness</h4>Goes stale at once. To see changes you must download again.</div>
      <div class="part time"><h4>Completeness</h4>Whatever the publisher put in the file — no more, no less.</div>
      <div class="part"><h4>Speed / offline</h4>Local, fast, works without network.</div>
      <div class="part"><h4>Evidence of origin</h4>Readme, metadata inside the file, checksums.</div>
      <div class="part"><h4>What can silently change</h4>Nothing — but nothing updates either.</div>
    </div>
    <p class="small">Right choice when you need a <strong>reproducible snapshot</strong> — an analysis you must be able to rerun with exactly the same inputs.</p>
  </div>
  <div class="tabpanel">
    <div class="record">
      <div class="part geom"><h4>What you hold</h4>A <strong>capability</strong>: the right to ask a server for records, subject to its rules.</div>
      <div class="part attr"><h4>Freshness</h4>Each request returns the current state (or a cached one — the service decides).</div>
      <div class="part time"><h4>Completeness</h4>Whatever your account may see. Fields and rows can be hidden by a view (Chapter 2).</div>
      <div class="part"><h4>Speed / offline</h4>Needs network; big pulls may be paged or limited by the server.</div>
      <div class="part"><h4>Evidence of origin</h4>Item page, service metadata, sharing level, owner.</div>
      <div class="part"><h4>What can silently change</h4>Schema, default styling, permissions and the data itself — without notice.</div>
    </div>
    <p class="small">Right choice when you need <strong>current values</strong> and are content to be governed by the provider. Protocol details come in a later phase; the <em>questions</em> — who owns it, what may I see, what may change — are the ones the intake form in 7.6 asks of a file too.</p>
  </div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Sort the clues</h3>
    <p>A colleague emails: “Attached <code>wards.shp</code> — it’s the official ward layer.” Click each clue, then the box that says what it really tells you.</p>
    <div class="sorter" data-items='[
      {"t":"The extension is .shp","bin":"Encoding claim","why":"only says how the bytes are (probably) written"},
      {"t":"It came as an email attachment","bin":"Container / delivery fact","why":"a frozen copy, no service — and were all the companion files attached?"},
      {"t":"The word “official”","bin":"Says who issued it","why":"names the issuer; nothing about date, accuracy or licence"},
      {"t":"Wards are areas","bin":"Data model","why":"expect polygons — but open it and confirm"},
      {"t":"Whether it is the 2019 or the 2024 boundary","bin":"Not told at all","why":"only the metadata or the publisher can say (7.6)"},
      {"t":"Whether you may republish it","bin":"Not told at all","why":"licence and permission are separate concerns"}
    ]'>
      <div class="items"></div>
      <div class="bins">
        <div class="bin" data-bin="Data model"><h5>Data model</h5></div>
        <div class="bin" data-bin="Encoding claim"><h5>Encoding claim</h5></div>
        <div class="bin" data-bin="Container / delivery fact"><h5>Container / delivery</h5></div>
        <div class="bin" data-bin="Says who issued it"><h5>Who issued it</h5></div>
        <div class="bin" data-bin="Not told at all"><h5>Not told at all</h5></div>
      </div>
    </div>
  </div>

  <div class="quiz" data-answer="2" data-fb="The extension only claims an encoding. Freshness comes from the edition date in the metadata, completeness from a count you compare with the publisher’s, and fitness from your recipient’s need. A geodatabase can hold rules, but only if someone defined them.">
    <div class="q">A folder contains <code>assets_final.gdb</code>. Which statement is safe to make from the name alone?</div>
    <div class="opts">
      <button class="opt">It is the latest version of the assets.</button>
      <button class="opt">All records are present and validated, because geodatabases enforce rules.</button>
      <button class="opt">Somebody has claimed it is a file geodatabase; nothing about freshness, completeness or quality is known yet.</button>
      <button class="opt">It can be used for any purpose, since it is the final one.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const LAYERS = [
    { id: "model", nm: "Data model", sub: "what is represented", ex: ["vector features", "raster grid"] },
    { id: "enc", nm: "Encoding", sub: "how one dataset is written", ex: ["CSV text", "GeoJSON text", ".shp binary", "TIFF tags"] },
    { id: "cont", nm: "Container", sub: "what holds datasets together", ex: ["folder of .shp files", "GeoPackage (.gpkg)", "file geodatabase (.gdb)", "PostgreSQL database"] },
    { id: "svc", nm: "Service", sub: "how others fetch it", ex: ["hosted feature layer", "feature service", "WFS endpoint"] }
  ];
  const ITEMS = [
    { t: "assets_ch7.csv", lit: ["enc"], note: "An encoding of one vector table as text. The folder it sits in is barely a container (no shared bookkeeping), and there is no service unless someone publishes it." },
    { t: "assets.geojson", lit: ["enc"], note: "Also an encoding of vector data — the same six points could be written as CSV, GeoJSON or .shp. GeoJSON adds one rule the CSV lacks: the coordinates must be WGS 84 longitude/latitude (7.2)." },
    { t: "assets.shp + .shx + .dbf + .prj", lit: ["enc", "cont"], note: "The .shp/.dbf layouts are encodings; the group of companion files is a weak container — it only ‘holds together’ because the names match (7.3)." },
    { t: "town.gpkg", lit: ["cont"], note: "A container: one SQLite file that can hold many vector tables, tile layers and attribute tables, with bookkeeping tables listing what is inside (7.4). Its own geometry encoding is inside it." },
    { t: "municipal.gdb (folder)", lit: ["cont"], note: "A file geodatabase: a container with an information model — feature classes, tables, relationships, rules (7.5). No service until published." },
    { t: "PostgreSQL + PostGIS", lit: ["cont"], note: "A spatial database: a container reachable by SQL. It becomes an ArcGIS *enterprise geodatabase* only when Esri’s system tables are added (7.5)." },
    { t: "https://…/FeatureServer/0", lit: ["svc"], note: "A service. You cannot tell from the URL whether the data behind it is a geodatabase, a GeoPackage or a hosted store — and you are not supposed to need to." },
    { t: "a satellite image", lit: ["model"], note: "A raster data model. It could be encoded as GeoTIFF, held in a GeoPackage tile table or a raster dataset, and served as an image service." },
    { t: "‘the ward boundaries’", lit: ["model"], note: "Just a data model statement (polygons). Until you know the encoding, container and delivery, you know nothing about how to receive it." }
  ];
  const stack = document.getElementById("stack");
  stack.innerHTML = LAYERS.map(l => `<div class="layerband" id="band-${l.id}"><div class="nm">${l.nm}<small>${l.sub}</small></div><div class="ex">${l.ex.map(e => `<span class="pill">${e}</span>`).join("")}</div></div>`).join("");
  const chips = document.getElementById("chips");
  ITEMS.forEach((it, i) => { const b = document.createElement("button"); b.className = "chipbtn"; b.textContent = it.t; b.setAttribute("aria-pressed", "false"); b.addEventListener("click", () => {
    chips.querySelectorAll(".chipbtn").forEach(x => x.setAttribute("aria-pressed", "false")); b.setAttribute("aria-pressed", "true");
    LAYERS.forEach(l => { const el = document.getElementById("band-" + l.id); el.classList.toggle("lit", it.lit.includes(l.id)); el.classList.toggle("dimmed", !it.lit.includes(l.id)); });
    document.getElementById("stackNote").innerHTML = `<strong>${esc(it.t)}</strong> → ${it.lit.map(id => LAYERS.find(l => l.id === id).nm).join(" + ")}. ${it.note}`;
  }); chips.appendChild(b); });
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
