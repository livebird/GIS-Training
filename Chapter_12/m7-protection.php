<?php $page = ['title' => '12.7 Presentation is not protection', 'chapter' => 12, 'module' => '12.7']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 12.7 · ArcGIS Pro, ArcGIS Online and the REST API · general principle</div>
    <h1>Hiding a field is not protecting it</h1>
    <p class="lead">Every mapping product lets you hide things: switch a layer off, remove a field from the pop-up, filter what is drawn. Those controls change <em>what this map shows</em>. They do not change <em>what a reader can get from the data</em> — because the data lives in the layer or service, and the map is only one client of it.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>See a field vanish from the pop-up and still come back from the service.</li>
      <li>Sort requirements into <strong>presentation</strong> (this chapter) and <strong>protection</strong> (the later administration chapters).</li>
      <li>Review a map for personal details it does not need — on made-up data only.</li></ul></div>
  </div>

  <h2><span class="mod">12.7.1</span>The pop-up and the API</h2>
  <p>Read the products’ own words. ArcGIS Pro’s pop-up help: changes made there “are exclusive to the pop-up display and do not impact the format of the field in the table”. ArcGIS Online’s Map Viewer lets you “rearrange and remove fields” in the pop-up and turn pop-ups off entirely. Meanwhile the ArcGIS REST API’s <code>query</code> operation takes an <code>outFields</code> parameter — “the list of fields to be included in the returned result set” — and with <code>*</code> “the query results include all the field values”. A field removed from a pop-up is one HTTP request away for anyone who can reach the service.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Untick a field in the pop-up, then call the service</h3>
    <div class="fieldtoggles" id="ft"></div>
    <div class="pair">
      <div><h4>What the map’s pop-up shows (request G08)</h4><div class="popup" id="popup"></div></div>
      <div><h4>What the service returns to <em>any</em> client</h4><pre class="api" id="api"></pre></div>
    </div>
    <div class="result" id="ftOut"></div>
  </div>
  <div class="table-wrap"><table>
    <thead><tr><th>Control</th><th>Changes</th><th>Does <em>not</em> change</th></tr></thead>
    <tbody>
      <tr><td>Layer switched off</td><td>This map’s drawing</td><td>Who can open the layer item or query the service</td></tr>
      <tr><td>Field removed from the pop-up</td><td>This map’s pop-up</td><td>The field in the layer, in the attribute table, in a query</td></tr>
      <tr><td>Filter / definition query in the map</td><td>Which features this map draws</td><td>Which features the service returns to another client</td></tr>
      <tr><td>Labels, symbology</td><td>This map’s drawing</td><td>Nothing about the data</td></tr>
    </tbody></table></div>
  <div class="callout dev"><span class="label">Developer view</span><p><code>display: none</code> on the salary column does not stop the API returning it — everyone has seen the “hidden” field in the network tab. A pop-up configuration is CSS. Access control is the authorisation layer on the API — a different system, usually with different owners. <strong>Where it stops:</strong> in a web app the same team usually builds and reviews both layers together; in a GIS platform the map author often is not the layer owner and may not even see the sharing settings. The gap between “hidden” and “protected” is easy to miss, so check it explicitly.</p></div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“I removed the reporter’s phone number from the pop-up, so the public map is safe to share.”</em> The field is still in the hosted layer; anyone with the layer URL queries it with <code>outFields=*</code>. The author believed the data was protected. It was not.</p></div>

  <h2><span class="mod">12.7.2</span>Presentation or protection? Sort the requirement</h2>
  <p>This chapter does not teach security settings; the later chapters on ArcGIS Online administration and ArcGIS Enterprise do. What you must do <em>now</em> is read a requirement and say which kind it is. The test question: <strong>if a determined reader ignored the map and went straight to the layer, would the requirement still be met?</strong> If it must be, it is a protection requirement — flag it, do not “solve” it with symbology.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <div class="sorter" data-items='[
      {"t":"The crew should not be distracted by closed requests","bin":"presentation","why":"a filter or symbology in the crew map"},
      {"t":"Field staff must see priority at a glance","bin":"presentation","why":"symbol size"},
      {"t":"The public must not be able to obtain reporter names","bin":"protection","why":"a hosted feature layer view that excludes the field, shared separately from the source — later chapters"},
      {"t":"Only the drainage team may edit drain requests","bin":"protection","why":"editing settings and sharing on the layer or a view — later chapters"},
      {"t":"Managers need the rate; residents need the points","bin":"presentation","why":"two maps"},
      {"t":"Personal notes must never leave the internal system","bin":"protection","why":"sharing rules — and, today, never put them on any map (12.7.3)"},
      {"t":"The councillor wants a version showing only W11 (for convenience)","bin":"presentation","why":"a map filter; if the councillor MUST NOT see other wards it becomes protection — ask which"}
    ]'>
      <div class="items"></div>
      <div class="grid-2" style="margin-top:.6rem">
        <div class="bin" data-bin="presentation"><h4>Presentation — this chapter</h4></div>
        <div class="bin" data-bin="protection"><h4>Protection — later chapters (sharing, views, editing rights)</h4></div>
      </div>
    </div>
  </div>
  <div class="callout note"><span class="label">What the later chapters use (read, not tested here)</span><p>ArcGIS Online’s help describes a <strong>hosted feature layer view</strong> as a separate item that references the source data, in which you can “exclude fields from the view if the view users do not need to access them”, and which is shared on its own. Whether an excluded field is also absent from the view’s REST query output was <em>not</em> verified for this chapter — it is recorded as a check for the administration chapters. Until then: a requirement of that kind is <strong>not met by anything in this chapter</strong>.</p></div>

  <h2><span class="mod">12.7.3</span>Review the output for personal details (made-up data)</h2>
  <p>Whatever the access rules, a map should not <em>carry</em> personal details it does not need. A map is a publication — a PDF gets forwarded, printed, left in a van. Run this review before the crew map leaves your desk.</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Field</th><th>Crew needs it?</th><th>Manager needs it?</th><th>On the maps</th></tr></thead>
    <tbody>
      <tr><td><code>request_id</code></td><td>Yes (job sheet)</td><td>No</td><td>Label on the crew map only</td></tr>
      <tr><td><code>category</code>, <code>priority</code>, <code>status</code></td><td>Yes</td><td>Only as counts</td><td>Symbolised on the crew map</td></tr>
      <tr><td><code>reporter_ref</code></td><td><strong>No</strong> — the crew fixes the asset, not the reporter; contact goes through the office</td><td>No</td><td>Not on any map, pop-up or export</td></tr>
      <tr><td><code>reporter_note</code></td><td>Sometimes useful (“second lamp from the corner”) — <strong>but</strong> G08’s note names a relative, an age and a house number</td><td>No</td><td>Never as a label; only an office-written, checked <code>location_hint</code></td></tr>
      <tr><td>Coordinates to the millimetre</td><td>No — accuracy is ± 15 m</td><td>No</td><td>Round exports to match the accuracy</td></tr>
    </tbody></table></div>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>The note that should not be on a map</h3>
    <p>Toggle “notes as labels” — this is fault 7 of the bad map in the lab.</p>
    <div class="controls"><label><input type="checkbox" id="notes"> show <code>reporter_note</code> as labels</label></div>
    <figure class="map-fig" id="noteFig"></figure>
  </div>
  <p>Two habits: <strong>never label or export free-text fields by default</strong> (free text is where personal details hide), and <strong>build the map from a purpose-specific copy</strong> whose field list is the minimum the purpose needs. A map that never had the field cannot leak it. Everything in F12-2 — references, notes, the age, the house number — is invented for this exercise; the blueprint forbids using real resident information for a map-design lesson.</p>

  <div class="quiz" data-answer="2" data-fb="(a) presentation; (b) an access setting — and inconsistent: public readers of the map cannot load an organisation-only layer; (c) protection; (d) presentation.">
    <div class="q">Which of these is a <em>protection</em> action? (a) unticking a field in the Map Viewer pop-up; (b) sharing the web map publicly while the layer stays organisation-only; (c) creating a hosted feature layer view with the field excluded; (d) a scale range so the layer draws only past 1:5,000.</div>
    <div class="opts"><button class="opt">(a) and (d)</button><button class="opt">(a) only</button><button class="opt">(c) — and (b) is an access setting too, but a broken one</button><button class="opt">All four</button></div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const q = REQ.find(r => r.id === "G08");
  const fields = [["request_id", q.id], ["category", q.cat], ["priority", q.pri], ["status", q.status], ["reported_on", q.on], ["reporter_ref", q.ref], ["reporter_note", q.note]];
  const on = {}; fields.forEach(f => on[f[0]] = true);
  const ft = document.getElementById("ft");
  ft.innerHTML = fields.map(f => `<label><input type="checkbox" data-f="${f[0]}" checked> ${f[0]}</label>`).join("");
  function draw() {
    document.getElementById("popup").innerHTML = `<div class="hd">Request G08</div>` + fields.map(f => `<div class="row ${on[f[0]] ? "" : "off"}"><b>${f[0]}</b><span>${f[1]}</span></div>`).join("");
    const hidden = fields.filter(f => !on[f[0]]).map(f => f[0]);
    document.getElementById("api").innerHTML = `GET /FeatureServer/0/query?where=request_id%3D'G08'&amp;<span class="hl">outFields=*</span>&amp;f=json\n\n{ "features": [ { "attributes": {\n` + fields.map(f => `    "${f[0]}": "${String(f[1]).replace(/"/g, "'")}"${hidden.includes(f[0]) ? '   <span class="bad">← still returned</span>' : ""}`).join(",\n") + `\n} } ] }`;
    document.getElementById("ftOut").innerHTML = hidden.length ? `<strong>${hidden.join(", ")}</strong> ${hidden.length > 1 ? "are" : "is"} gone from the pop-up — and still in the service response. Presentation changed; protection did not.` : "Untick reporter_ref and reporter_note to see what the pop-up hides — and what the service still returns.";
  }
  ft.querySelectorAll("input").forEach(b => b.addEventListener("change", () => { on[b.dataset.f] = b.checked; draw(); })); draw();

  const notes = document.getElementById("notes");
  const nd = () => renderCrew(document.getElementById("noteFig"), { pt: 9, scale: 12000, showClosed: "hide", noteLabels: notes.checked, window: [1000, 2200, 3000, 3000], caption: notes.checked ? "Free-text notes as labels: G08’s note now names a relative, an age and a house number — on a printed sheet." : "IDs only. Location hints, if needed, come from an office-checked field." });
  notes.addEventListener("change", nd); nd();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
