<?php $page = ['title' => '8.4 Identifiers', 'chapter' => 8, 'module' => '8.4']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 8.4 · General idea, then ArcGIS-specific behaviour</div>
    <h1>Three things that look like an ID — and are not the same</h1>
    <p class="lead">Every row in a working GIS table ends up with three “names”: the name the organisation uses (<code>SL-0113</code>), the name people see on the map (“Streetlight near market”), and a number the software made up for its own use (ObjectID 17). Confuse them and the failure is always the same: inspections, photos and complaints stop pointing at the right asset.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Separate a <strong>business identifier</strong> from a <strong>display label</strong> and from a <strong>storage row number</strong>, and say why each changes for a different reason.</li>
      <li>State what ArcGIS <strong>ObjectID</strong> and <strong>GlobalID</strong> are — and, from the documentation, what they do <em>not</em> promise.</li>
      <li>Write a short <strong>identity policy</strong>: how imports match, how duplicates are handled, and which key related records carry.</li></ul></div>
  </div>

  <h2><span class="mod">8.4.1</span>Business ID, display label, storage row number</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Kind</th><th>Purpose</th><th>Who gives it</th><th>Stable?</th><th>Example</th></tr></thead>
    <tbody>
      <tr><td><span class="idtag biz">Business identifier</span></td><td>The name the organisation uses everywhere — reports, phone calls, paper files, other systems</td><td>The organisation, by a written rule</td><td class="yes">Must be — for the life of the thing</td><td class="mono">SL-0113</td></tr>
      <tr><td><span class="idtag label">Display label</span></td><td>What a map, pop-up or list shows a person</td><td>Anyone; often built from other fields</td><td class="no">No — changes with a re-wording or a translation</td><td>“Streetlight near Ward A market”</td></tr>
      <tr><td><span class="idtag store">Storage row number</span></td><td>What the storage engine uses to find and update a row</td><td>The storage engine, automatically</td><td class="no">Only inside that one copy of the dataset</td><td class="mono">ObjectID 17 · GlobalID {6F1A…}</td></tr>
    </tbody></table></div>
  <p><strong>Why three, not one?</strong> They change for different reasons. A label changes when someone decides “near the market” is clearer than “opposite shop no. 12”. A storage number changes when the data is copied into a new file. A business ID must change for <em>neither</em> reason — because the maintenance history, the photos, the complaints and the paper records all point at it.</p>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Copy the dataset, rename the label — which name survives?</h3>
    <p>SL-0113 has three inspections pointing at it. Press the buttons and watch which “name” keeps the inspections attached.</p>
    <div class="opbtns">
      <button class="btn small" id="idReset" aria-pressed="true">Original dataset</button>
      <button class="btn small" id="idExport">Export to a Shapefile for a contractor</button>
      <button class="btn small" id="idRename">Re-word the label</button>
      <button class="btn small" id="idReimport">Contractor sends it back; re-import</button>
    </div>
    <div class="idgrid">
      <div class="reccard" id="idCard">
        <div class="hdr">Asset row</div>
        <div class="row" id="rBiz"><b>asset_id</b><span class="v">SL-0113</span></div>
        <div class="row" id="rLabel"><b>label</b><span class="v">Streetlight near Ward A market</span></div>
        <div class="row" id="rOid"><b>ObjectID / FID</b><span class="v">17</span></div>
        <div class="row" id="rGid"><b>GlobalID</b><span class="v">{6F1A-…}</span></div>
      </div>
      <div>
        <div class="reccard" id="linkCard"><div class="hdr">Inspections that still find this asset</div>
          <div class="row"><b>keyed on asset_id</b><span class="v" id="lBiz">3 of 3</span></div>
          <div class="row"><b>keyed on label</b><span class="v" id="lLabel">3 of 3</span></div>
          <div class="row"><b>keyed on ObjectID</b><span class="v" id="lOid">3 of 3</span></div>
        </div>
        <div class="status-line q" id="idStatus">Press a button.</div>
      </div>
    </div>
  </div>
  <div class="callout dev"><span class="label">Developer view</span><p>Business ID = natural key agreed with the domain (an order number). Storage number = surrogate primary key (<code>SERIAL</code>). Label = the <code>toString()</code>. In an app you control one database for its whole life, so the surrogate is stable and often exposed in URLs. <strong>Where the comparison stops:</strong> a GIS dataset is routinely exported to a Shapefile for a contractor, copied into a file geodatabase for a field project, published as a web layer, and re-imported — each copy is a new instance with its own surrogates. That is why GIS needs the business ID <em>on the row</em>, not only in the application.</p></div>

  <h2><span class="mod">8.4.2</span>ObjectID and GlobalID — what they are, what they do not promise</h2>
  <div class="callout note"><span class="label">Platform note</span><p>Everything in this section is ArcGIS behaviour, taken from Esri’s documentation as read on 19 September 2026. It is not a general GIS rule.</p></div>
  <div class="grid-2">
    <div class="card">
      <h4 style="margin-top:0">ObjectID</h4>
      <p>When a geodatabase table is created, ArcGIS adds an object identifier: “a unique integer field that cannot have null values”, which “guarantees a unique ID for each row in the table” and is “maintained by ArcGIS”. You see it as <code>OBJECTID</code>, <code>OID</code> or <code>FID</code>. Selection, scrolling and editing use it internally; you cannot edit or calculate it.</p>
      <p><strong>What the documentation does <em>not</em> say</strong> is that an ObjectID survives copying. Two facts show why you must not assume it:</p>
      <ul>
        <li>For a database table or view opened as a <em>query layer</em> without a suitable key, ArcGIS “adds an attribute called ESRI_OID” — and that attribute “is only part of the layer definition; the underlying database table is not altered”. An ObjectID can be a number that exists only while the layer is open.</li>
        <li>A copy, export or append writes rows into a <em>new</em> table that assigns its own ObjectIDs.</li>
      </ul>
      <p><strong>Course rule:</strong> an ObjectID is valid only inside the copy you read it from. Never store it in another table as a key that must survive a copy.</p>
    </div>
    <div class="card">
      <h4 style="margin-top:0">GlobalID (and GUID)</h4>
      <p>A GlobalID is a 128-bit value “automatically assigned and managed by a geodatabase when a row or feature is created”, giving each row “a unique fingerprint that cannot be changed”. A <em>GUID</em> field holds the same kind of value but “must be manually populated and maintained” by you. The <em>Add Global IDs</em> tool adds the field (all licence levels).</p>
      <p>ArcGIS uses GlobalIDs for its own bookkeeping — relationships, versioning, replication — and two features depend on them: <strong>attribute rules</strong> (“the dataset must have GlobalIDs”) and <strong>offline sync</strong>, which “relies on unchanging unique IDs to identify matching rows in the source and the offline version of a layer”; ArcGIS Online adds “a global ID field … to all layers … when you enable synchronization”.</p>
      <p><strong>Two things a GlobalID does not promise:</strong></p>
      <ol>
        <li><strong>Not preserved by default when rows are appended.</strong> The <em>Preserve Global IDs</em> setting exists precisely because the default is the opposite: without it “the operation will not preserve the Global IDs of features and will instead create new IDs. This is the default.” And for the Append tool it “only applies to enterprise geodatabase data … with a Global ID field with a unique index”.</li>
        <li><strong>Having a GlobalID does not switch anything on.</strong> Sync is a layer setting you enable; the GlobalID is a prerequisite the platform adds when you do. Same for attachments (8.7) and attribute rules.</li>
      </ol>
    </div>
  </div>
  <p><strong>Where this leaves the design.</strong> Every table gets a business ID as the key people and other tables use (<code>asset_id</code>, <code>inspection_id</code>, <code>team_id</code>, <code>request_id</code>, <code>ward_code</code>). ObjectID exists because ArcGIS creates it; nothing in the design refers to it. A GlobalID is added to every table because platform features will need it — and the design notes which workflows must preserve it.</p>
  <div class="callout warn"><span class="label">Instructor must verify</span><p>Before promising any ObjectID or GlobalID behaviour in a real procedure — export, Append, publishing, download and re-upload — run that exact workflow on a throw-away copy in the installed version and compare the IDs before and after. The documentation supports the statements above and nothing stronger.</p></div>

  <h2><span class="mod">8.4.3</span>An identity policy: three short answers</h2>
  <p>An <dfn title="A short written rule set for matching, duplicates and keys">identity policy</dfn> is a short written answer to three questions. Without it, every import becomes a fresh argument.</p>
  <div class="grid-3">
    <div class="card"><h4 style="margin-top:0">1. On import, new thing or existing thing?</h4><p><strong>Our rule:</strong> match on <code>asset_id</code>. An incoming row with an existing <code>asset_id</code> is an <em>update candidate</em>, reviewed field by field; one with no <code>asset_id</code> is rejected until an ID is assigned — the importer never invents one. Matching on coordinates alone is forbidden: two points 7 m apart can be the same asset seen by two methods, or two assets.</p></div>
    <div class="card"><h4 style="margin-top:0">2. How are duplicates found and resolved?</h4><p><strong>Our rule:</strong> a duplicate is two rows with the same <code>asset_id</code>, or two rows of the same type within 2 m of each other — flagged for a <em>person</em> to review, never auto-merged. The kept row is the one with the older ID; all inspections and requests are moved to it; the other row is marked <code>status = MERGED</code> with a <code>merged_into</code> pointer. It is not deleted — deleting destroys the audit trail (8.7).</p></div>
    <div class="card"><h4 style="margin-top:0">3. Which key do related records carry?</h4><p><strong>Our rule:</strong> the business ID (<code>asset_id</code> on inspections and requests; <code>inspection_id</code> on photos). Never an ObjectID. A GlobalID may be carried <em>in addition</em> where the platform needs it (ArcGIS attachments), but the business ID stays the key humans and other systems use.</p></div>
  </div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Sort the situations: what does the identity policy say?</h3>
    <div class="sorter" data-items='[
      {"t":"Contractor spreadsheet keyed on the Shapefile FID column","bin":"Reject / fix first","why":"FID is a storage number of one file copy; results cannot be trusted to attach to the right assets"},
      {"t":"Incoming row has asset_id SL-0113 with a new pole height","bin":"Review as update","why":"same business ID → update candidate, checked field by field"},
      {"t":"Two streetlight rows, 1.5 m apart, different IDs","bin":"Flag for a person","why":"within 2 m → possible duplicate; never auto-merge"},
      {"t":"Field app row with coordinates but no asset_id","bin":"Reject / fix first","why":"no business ID → held back until one is assigned"},
      {"t":"Complaint P6 marked duplicate of another complaint","bin":"Link, do not delete","why":"a duplicate_of pointer keeps both rows and the history"}
    ]'>
      <div class="items"></div>
      <div class="bins">
        <div class="bin" data-bin="Review as update"><h5>Review as update</h5></div>
        <div class="bin" data-bin="Flag for a person"><h5>Flag for a person</h5></div>
        <div class="bin" data-bin="Reject / fix first"><h5>Reject / fix first</h5></div>
        <div class="bin" data-bin="Link, do not delete"><h5>Link, do not delete</h5></div>
      </div>
    </div>
  </div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“A UUID column is a synchronisation solution.”</em> A UUID is an identifier; synchronisation is a workflow that needs identifiers <em>plus</em> change tracking <em>plus</em> a switched-on capability. The documentation is explicit: sync is a setting you enable, and the platform adds the GlobalID for you when you do. Adding the column yourself does nothing on its own.</p></div>

  <div class="quiz" data-answer="2" data-fb="Only the business identifier is designed to survive a copy. ObjectIDs are per table (and can even be run-time numbers); labels are neither unique nor stable — the lab has two lights with the same label; a GlobalID is regenerated by Append unless a preserving workflow is configured, which the option rules out.">
    <div class="q">Which may be used as the key that inspection rows carry to identify their asset <em>across</em> an export to a file geodatabase and a re-import?</div>
    <div class="opts">
      <button class="opt">The asset’s ObjectID.</button>
      <button class="opt">The asset’s display label.</button>
      <button class="opt">The asset’s business identifier <code>asset_id</code>.</button>
      <button class="opt">The asset’s GlobalID, with no further workflow configuration.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const R = { biz: document.getElementById("rBiz"), label: document.getElementById("rLabel"), oid: document.getElementById("rOid"), gid: document.getElementById("rGid") };
  const L = { biz: document.getElementById("lBiz"), label: document.getElementById("lLabel"), oid: document.getElementById("lOid") };
  const st = document.getElementById("idStatus"), card = document.getElementById("idCard");
  const set = (r, v, cls) => { r.querySelector(".v").textContent = v; r.className = "row " + (cls || ""); };
  const link = (k, n, cls) => { L[k].textContent = n; L[k].parentElement.className = "row " + cls; };
  function state(s) {
    ["idReset", "idExport", "idRename", "idReimport"].forEach(id => document.getElementById(id).setAttribute("aria-pressed", "false"));
    if (s === "orig") {
      document.getElementById("idReset").setAttribute("aria-pressed", "true"); card.className = "reccard";
      set(R.biz, "SL-0113"); set(R.label, "Streetlight near Ward A market"); set(R.oid, "17"); set(R.gid, "{6F1A-…}");
      link("biz", "3 of 3", "same"); link("label", "3 of 3", "same"); link("oid", "3 of 3", "same");
      st.className = "status-line q"; st.textContent = "In the original copy all three names work — which is why the problem is invisible at first.";
    } else if (s === "export") {
      document.getElementById("idExport").setAttribute("aria-pressed", "true"); card.className = "reccard";
      set(R.biz, "SL-0113", "same"); set(R.label, "Streetlight near Ward A market", "same"); set(R.oid, "FID 3 (this file’s own numbering)", "changed"); set(R.gid, "text copy only — no longer maintained", "changed");
      link("biz", "3 of 3", "same"); link("label", "3 of 3", "same"); link("oid", "0 of 3 — they point at ObjectID 17, which now belongs to a different row", "gone");
      st.className = "status-line bad"; st.textContent = "The new file numbers its rows from scratch. Anything keyed on the old ObjectID now points at the wrong pole.";
    } else if (s === "rename") {
      document.getElementById("idRename").setAttribute("aria-pressed", "true"); card.className = "reccard";
      set(R.biz, "SL-0113", "same"); set(R.label, "Streetlight opposite shop no. 12", "changed"); set(R.oid, "17", "same"); set(R.gid, "{6F1A-…}", "same");
      link("biz", "3 of 3", "same"); link("label", "0 of 3 — they say “near Ward A market”", "gone"); link("oid", "3 of 3", "same");
      st.className = "status-line bad"; st.textContent = "One harmless re-wording, and every inspection keyed on the label is orphaned.";
    } else {
      document.getElementById("idReimport").setAttribute("aria-pressed", "true"); card.className = "reccard good";
      set(R.biz, "SL-0113", "same"); set(R.label, "Streetlight near Ward A market", "same"); set(R.oid, "41 (assigned again on import)", "changed"); set(R.gid, "{9C2B-…} (new — Append made a new one)", "changed");
      link("biz", "3 of 3", "same"); link("label", "3 of 3", "same"); link("oid", "0 of 3", "gone");
      st.className = "status-line ok"; st.textContent = "Only asset_id came through every step unchanged. That is what “business identifier” means.";
    }
  }
  document.getElementById("idReset").addEventListener("click", () => state("orig"));
  document.getElementById("idExport").addEventListener("click", () => state("export"));
  document.getElementById("idRename").addEventListener("click", () => state("rename"));
  document.getElementById("idReimport").addEventListener("click", () => state("reimport"));
  state("orig");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
