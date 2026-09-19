<?php $page = ['title' => 'Chapter 2 glossary', 'chapter' => 2]; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Chapter 2</div>
    <h1>Glossary</h1>
    <p class="lead">Terms as used in this chapter. Tags: <span class="pill">Esri</span> <span class="pill">QGIS</span> <span class="pill">open source</span> — untagged terms are general ideas.</p>
  </div>
  <input class="gloss-search" id="q" type="search" placeholder="Type to filter… e.g. item, view, ArcPy, portal" aria-label="Filter glossary">
  <dl class="gloss" id="gloss"></dl>
  <p style="margin-top:2rem"><a class="btn ghost" href="index.php">← Chapter home</a></p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const E = ' <span class="pill">Esri</span>', Q = ' <span class="pill">QGIS</span>', O = ' <span class="pill">open source</span>';
  const terms = [
    ["Responsibility (R1–R5)", "One of five jobs a complete GIS solution must cover: desktop authoring, data storage, service delivery, content organisation, user applications. A teaching model from this course, not a vendor term."],
    ["Authoritative data", "The copy of a dataset the organisation has designated as the source of truth. A decision, not a technical property."],
    ["Web service", "A network endpoint (a URL) that returns geographic data, map images or tiles on request."],
    ["Publish", "Create a web service (and, in ArcGIS, an item) from a dataset. May copy the data to a managed store or reference it in place."],
    ["Hosted layer" + E, "A web layer whose data is stored and served by ArcGIS Online or an Enterprise data store; deleting it deletes the data."],
    ["Referenced layer" + E, "A web layer backed by a service whose data stays in its original registered location."],
    ["Project (.aprx)" + E, "An ArcGIS Pro file holding maps, scenes, layouts and connections to data; it stores paths, not data."],
    ["Project (.qgz / .qgs)" + Q, "The QGIS equivalent; also stores references, not data."],
    ["Contents pane / Catalog pane" + E, "ArcGIS Pro panels listing, respectively, the active view’s layers and the project’s items and connections."],
    ["Layers panel / Browser panel" + Q, "The corresponding QGIS panels."],
    ["Connection" + E, "A stored pointer from a project to a folder, database, toolbox, server or portal."],
    ["Item" + E, "A catalogued object in ArcGIS Online or a portal, with owner, type, metadata and sharing level."],
    ["Web layer" + E, "An item exposing geographic data as a service — feature, tile, map image, imagery, scene and other types."],
    ["Hosted feature layer view" + E, "A separate item that reads the same data as a source hosted feature layer with its own sharing, editing and field settings; not a copy."],
    ["Web map" + E, "An item holding a basemap, layer references, styling, pop-ups and extent; it references layers rather than storing data."],
    ["Group" + E, "A collection of items shared with a set of members; the owner controls membership and contribution."],
    ["Member, user type, role" + E, "A signed-in person; user type and role together determine privileges. Default roles include Viewer, Data Editor, User, Publisher, Facilitator, Administrator."],
    ["Sharing level" + E, "Owner-only, organisation, group(s), or everyone."],
    ["Credits" + E, "ArcGIS Online’s metering currency for specific transactions and storage; most everyday use does not consume them."],
    ["Portal for ArcGIS" + E, "The Enterprise component for content organisation and sign-in (items, groups, members)."],
    ["ArcGIS Server" + E, "The Enterprise component that runs services and processing; one instance is the hosting server for the portal."],
    ["ArcGIS Data Store" + E, "The Enterprise component that stores hosted-layer data. A product — not a synonym for your own database."],
    ["ArcGIS Web Adaptor" + E, "The Enterprise component that integrates the portal and server with an existing web server and security setup."],
    ["Hosting server" + E, "The ArcGIS Server configured to host the portal’s hosted layers."],
    ["Configuration", "Arranging capabilities a product already supports, without writing application code."],
    ["Instant Apps" + E, "Configurable builder for focused apps made from templates on a web map or scene."],
    ["Experience Builder" + E, "Configurable builder for composed web experiences with layouts and widgets; extensible with custom widgets."],
    ["Dashboards" + E, "Configurable builder for monitoring displays: indicators, charts, lists, maps."],
    ["Field Maps" + E, "Mobile solution for field work: maps and forms prepared in Field Maps Designer, used in the mobile app online or offline."],
    ["Web AppBuilder" + E, "A configurable builder now listed as retired in Esri’s developer documentation. Do not choose it for new work."],
    ["ArcGIS Maps SDK for JavaScript" + E, "Esri’s browser SDK for 2D/3D web mapping applications."],
    ["ArcGIS API for Python" + E, "The arcgis Python package for portal administration, content management and analysis over the network."],
    ["ArcPy" + E, "Python site package for geoprocessing and map automation, available where ArcGIS Pro or ArcGIS Server is installed. Not the same as the ArcGIS API for Python."],
    ["REST API / service API", "The HTTP interfaces through which all clients read and write ArcGIS services and items."],
    ["OpenLayers" + O, "A free, BSD-licensed JavaScript library for dynamic maps in web pages; a browser application library (R5)."],
    ["Mapbox", "A vendor of mapping, navigation, search and data products and SDKs."],
    ["QGIS" + O, "A free desktop GIS application (R1)."],
    ["PostGIS" + O, "An extension adding spatial storage, indexing and functions to PostgreSQL (R2)."],
    ["GeoServer" + O, "A Java-based server that serves geospatial data through OGC standards such as WMS, WFS, WCS and WMTS (R3)."],
    ["OGC", "The Open Geospatial Consortium, publisher of the open standards GeoServer implements. Named only; standards are taught later."],
    ["Evidence pack", "This course’s term for a documented set of screens, listings and observations from a deployed solution, used in the lab."],
    ["Basemap", "The background map (streets, satellite image) that your own layers are drawn on top of."],
    ["Pop-up", "The small information box that opens when you click a feature on a web map."],
    ["Field (column)", "One column of a table — Status, Category, Note. Each record has a value in each field."],
    ["File geodatabase (.gdb)" + E, "Esri’s file-based storage folder for datasets on a disk or shared drive. Chapter 7 explains it properly."],
    ["Geoprocessing tool" + E, "A ready-made data-processing tool in ArcGIS (for example buffer or clip) that can be run from the desktop, a script, or a server."],
    ["Widget", "A ready-made building block in an app builder — a map, a chart, a search box, a list — that you switch on and point at a layer."],
    ["Registered data" + E, "Data in your own database or folder that ArcGIS Server has been told about, so it can serve it without making a copy."],
    ["Federated server" + E, "An ArcGIS Server that has been joined to a portal so that the portal manages its security and its services appear as items."],
    ["Stale copy", "A copy of the data that was correct when it was fetched but has not been refreshed since the authoritative copy changed."],
    ["Municipal corporation / nagar palika", "The city or town office in the practice scenario — the organisation that owns the assets and answers the requests."],
    ["Solution S-2", "The made-up deployment traced in the lab: an archived file, an ArcGIS Pro project, hosted layers, a view, two web maps, a dashboard, a Field Maps form and an Instant App."]
  ];
  terms.sort((a, b) => a[0].replace(/<[^>]+>/g, "").localeCompare(b[0].replace(/<[^>]+>/g, "")));
  const g = document.getElementById("gloss");
  function render(f) { g.innerHTML = terms.filter(t => !f || (t[0] + " " + t[1]).toLowerCase().includes(f)).map(t => `<dt>${t[0]}</dt><dd>${t[1]}</dd>`).join("") || "<dd>No matching term.</dd>"; }
  document.getElementById("q").addEventListener("input", e => render(e.target.value.trim().toLowerCase()));
  render("");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
