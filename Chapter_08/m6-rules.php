<?php $page = ['title' => '8.6 Allowed values and rules', 'chapter' => 8, 'module' => '8.6']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 8.6 · General idea, with ArcGIS / QGIS / PostgreSQL behaviour</div>
    <h1>Allowed lists, allowed ranges, defaults that lie, and where a rule should live</h1>
    <p class="lead">“Open”, “open”, “OPEN”, “Open ” and “Opne” are five different values to a computer. An <strong>allowed list</strong> stops that. An <strong>allowed range</strong> stops a pole height of 22 (feet, typed as metres). A <strong>default</strong> saves typing — and can quietly turn a visit that never happened into “Fair”. This module is about those three tools, and about a question developers rarely ask: which rules belong in the storage, which in the app, and which in a weekly check?</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Define a coded-value list (stored code + displayed description) and a range, and explain why you store the code, not the wording.</li>
      <li>Say when a default is safe and when it is dangerous, and why a blank must stay allowed for anything that is <em>observed</em>.</li>
      <li>Place each rule in the schema, the application, or a review procedure — and see that different storage formats enforce different amounts.</li></ul></div>
  </div>

  <h2><span class="mod">8.6.1</span>Coded lists and ranges: store the code, show the description</h2>
  <p>ArcGIS calls a fixed set of permitted values an <dfn title="ArcGIS: a rule describing the available values of a field — either a list of codes or a min–max range">attribute domain</dfn>: “rules that describe the available values of a field type”, which limit “what can be placed on a field to a valid list or range of choices”. Two kinds:</p>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">Coded-value domain — “which category?”</h4><p>A list of allowed <strong>codes</strong>, each paired with a human <strong>description</strong>. The code is what is <em>stored</em>; the description is what is <em>shown</em>. Esri’s own example: stored “1”, shown “pavement”. Works for any field type — text, number, date.</p></div>
    <div class="card"><h4 style="margin-top:0">Range domain — “within which limits?”</h4><p>A minimum and maximum for a number or date. Esri: applies to short/long/big integer, float, double, date, date-only and time-only fields.</p></div>
  </div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Fill in a request — see what is stored versus what is shown</h3>
    <div class="grid-2">
      <div class="formdemo">
        <label for="fStatus">Status (coded-value domain <code>RequestStatus</code>)</label>
        <select id="fStatus">
          <option value="OPEN">Open</option><option value="INPROG">In progress</option><option value="RESOLVED">Resolved</option><option value="CLOSED">Closed</option><option value="REOPENED">Reopened</option><option value="DUP">Closed – duplicate</option>
        </select>
        <label for="fCond">Condition score (range domain <code>ConditionScore</code>, 1 to 5)</label>
        <input id="fCond" type="number" value="3" min="0" max="9">
        <label for="fHeight">Pole height in metres (range domain <code>PoleHeightM</code>, 3 to 15)</label>
        <input id="fHeight" type="number" value="7" step="0.1">
        <label><input type="checkbox" id="fGuj"> Show descriptions in Gujarati (display only)</label>
      </div>
      <div>
        <div class="reccard"><div class="hdr">What the database stores</div>
          <div class="row"><b>status</b><span class="v" id="sStatus"></span></div>
          <div class="row"><b>condition_code</b><span class="v" id="sCond"></span></div>
          <div class="row"><b>pole_height_m</b><span class="v" id="sHeight"></span></div>
        </div>
        <div class="status-line q" id="fmsg"></div>
      </div>
    </div>
  </div>
  <p>Chapter 1’s status column had free text — “Open”, “In progress”, “Closed – duplicate”. A coded list guarantees that “In Progress”, “in progress” and “In-progress” cannot coexist, and lets a report rename a description (say, into Gujarati) without touching a single stored row or query. Ranges for the town: <code>ConditionScore</code> 1–5; <code>PoleHeightM</code> 3–15 (so “22”, feet misread as metres, is rejected); <code>InstallYear</code> 1950–2100 (catches “18” meaning 2018, and “0”). A domain is defined <strong>once</strong> in the geodatabase and assigned to fields — Esri: “you can share attribute domains across feature classes, tables, and subtypes in a geodatabase”.</p>
  <div class="callout note"><span class="label">Platform note — the same idea elsewhere</span><p>QGIS has no stored domain for a plain file, but its <strong>Value Map</strong> widget is the display-side twin: “the value is stored in the attribute, the description is shown in the combo box” — and QGIS reads domains already stored in a GeoPackage or file geodatabase and assigns a Value Map or Range widget automatically. In PostgreSQL the equivalent is a <code>CHECK</code> constraint (“the value in a certain column must satisfy a Boolean expression”) or a foreign key to a lookup table. A <strong>Shapefile has none of these</strong>: converting from a geodatabase loses subtypes and attribute domains (Chapter 7).</p></div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“Storing the description instead of the code is friendlier and avoids a lookup.”</em> It also means the data changes every time the wording changes, every query embeds the wording, and a translation becomes a data migration. Store the code; show the description.</p></div>

  <h2><span class="mod">8.6.2</span>A default is not an observation</h2>
  <p>A <dfn title="The value a new row receives for a field when nobody typed one">default</dfn> is what a new row gets when nobody supplied a value. Defaults are convenient and dangerous in equal measure, because a default <em>looks exactly like</em> a real observation.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Two field apps, one crew that could not reach the asset</h3>
    <p>The crew visits SL-0250 but the gate is locked; they cannot assess it. They save the form anyway. Compare the two app designs.</p>
    <div class="grid-2">
      <div class="reccard bad"><div class="hdr">App A — condition defaults to 3</div>
        <div class="row"><b>condition_code</b><span class="v">3 (Fair)</span></div>
        <div class="row"><b>defects_found</b><span class="v">0</span></div>
        <div class="row"><b>the report says</b><span class="v">“Fair, no defects” ✓ closed</span></div>
      </div>
      <div class="reccard good"><div class="hdr">App B — no default; blank allowed</div>
        <div class="row"><b>condition_code</b><span class="v">(blank) = not assessed</span></div>
        <div class="row"><b>defects_found</b><span class="v">(blank) = checklist not completed</span></div>
        <div class="row"><b>the report says</b><span class="v">“1 visit without assessment” → work list</span></div>
      </div>
    </div>
    <div class="controls"><label>Crews that could not assess this month: <input type="range" id="naSlider" min="0" max="20" value="5"></label></div>
    <div class="result" id="naOut"></div>
  </div>
  <p><strong>The rule.</strong> A default is fine when the value is <em>administratively true</em> for a new row and would otherwise be typed identically every time: <code>Request.status = OPEN</code> on creation, <code>Asset.status = ACTIVE</code>. A default is wrong for anything that is supposed to be <em>observed</em>: condition, defects, wattage, height. For observed fields, <strong>blank stays allowed and keeps its meaning</strong>.</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Field</th><th>Default?</th><th>Blank allowed?</th><th>Blank means</th></tr></thead>
    <tbody>
      <tr><td class="mono">Request.status</td><td class="mono">OPEN</td><td>No</td><td>—</td></tr>
      <tr><td class="mono">Asset.status</td><td class="mono">ACTIVE</td><td>No</td><td>—</td></tr>
      <tr><td class="mono">Asset.location_method</td><td>None</td><td>No</td><td>— (whoever captured it must say how)</td></tr>
      <tr><td class="mono">Inspection.condition_code</td><td>None</td><td>Yes</td><td>Not assessed on this visit</td></tr>
      <tr><td class="mono">Inspection.defects_found</td><td>None</td><td>Yes</td><td>Checklist not completed (0 = completed, none found)</td></tr>
      <tr><td class="mono">Inspection.remarks</td><td>None</td><td>Yes</td><td>No remark; the empty string is never stored</td></tr>
      <tr><td class="mono">StreetlightDetail.lamp_wattage_w</td><td>None</td><td>Yes</td><td>Unknown (not-applicable cannot occur — non-streetlights have no row here)</td></tr>
    </tbody></table></div>
  <div class="callout note"><span class="label">A one-way door</span><p>In an ArcGIS geodatabase the <em>Allow NULL</em> property “can only be set to false if the table is empty”, and the data type “can only be changed if the table is empty”. Decide required-or-not <em>before</em> the first row is loaded. That is why the dictionary is written first (8.7.3 lists more one-way doors). QGIS forms offer the same choices — a default value, even an expression like <code>now()</code> — and the same trap: a <code>now()</code> default is a typing-time fact, not a visit-time fact (8.7).</p></div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“Make every field NOT NULL and the data will be complete.”</em> It guarantees every field <em>contains something</em> — which pushes people to defaults and sentinel values (<code>0</code>, <code>-1</code>, <code>N/A</code>, <code>.</code>) that are worse than blanks because they hide inside statistics. Require values only where a row without them is meaningless: <code>asset_id</code>, <code>visited_at</code>, <code>asset_type</code>, <code>location_method</code>.</p></div>

  <h2><span class="mod">8.6.3</span>Schema, application, or review — where does each rule live?</h2>
  <p>Not every rule can — or should — be enforced by the storage. There are three places, and the design must assign each rule to one, because each place guards against a different set of people and programs.</p>
  <div class="ladder3">
    <div class="band schema"><h4>Schema (domain, blank rule, unique key, foreign key, shape type)</h4><p>Guards against <em>every client that honours the schema</em>. Strong where the platform enforces it; silently absent where it does not (Shapefile, CSV). Example: <code>condition_code</code> in 1–5; <code>asset_id</code> unique; <code>Inspection.asset_id</code> must exist in Asset.</p></div>
    <div class="band app"><h4>Application (field-app form, web app, import script)</h4><p>Guards only <em>users of that app</em>. A second app or a bulk load walks straight past it. Example: “wattage required when type = SL”; “a photo is required when defects &gt; 0”; “visit time may not be in the future”.</p></div>
    <div class="band review"><h4>Review procedure (a query run weekly; a supervisor’s sign-off)</h4><p>Guards <em>everything, after the fact</em>. Detects, does not prevent. Example: “assets with no inspection in 12 months”; “inspections whose asset_id matches no asset” (8.7); “distinct unit strings in Height” during an import.</p></div>
  </div>
  <p><strong>Why all three are needed.</strong> Esri’s domain page says that once a domain is on a field, “the field will not accept a value that is not in that domain” — but a domain lives in a <em>geodatabase</em>. The same data exported to CSV for a contractor has no domain at all, so the rule must be re-checked on return (review). And the cross-field rule “wattage only for streetlights” is not a simple domain: in ArcGIS it needs subtypes with per-subtype domains, or an <em>attribute rule</em> (a scripted rule needing GlobalIDs — a later chapter). Until then it lives in the app and the review.</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Rule</th><th>ArcGIS geodatabase</th><th>QGIS on a GeoPackage</th><th>PostgreSQL / PostGIS</th><th>Shapefile</th></tr></thead>
    <tbody>
      <tr><td>Value from a fixed list</td><td>Coded-value domain</td><td>Value Map widget; expression constraint — <em>soft</em> (yellow, “does not prevent you to save”) or <em>hard</em> (blocks saving); QGIS editing only</td><td><code>CHECK</code> or lookup-table foreign key</td><td class="no">None</td></tr>
      <tr><td>Number within limits</td><td>Range domain</td><td>Range widget / constraint</td><td><code>CHECK</code></td><td class="no">None</td></tr>
      <tr><td>Value required</td><td>Allow NULL = false (empty table only)</td><td>Not-null constraint (soft/hard)</td><td><code>NOT NULL</code></td><td class="no">Cannot say so; blanks become 0 or “ ”</td></tr>
      <tr><td>Unique</td><td>Unique index (verify per platform)</td><td>Unique constraint</td><td><code>UNIQUE</code> / <code>PRIMARY KEY</code></td><td class="no">None</td></tr>
      <tr><td>Child must have a parent</td><td>Relationship class behaviour on edit</td><td>Relation — QGIS editing only</td><td><code>FOREIGN KEY</code></td><td class="no">None</td></tr>
    </tbody></table></div>
  <div class="callout warn"><span class="label">Instructor must verify</span><p>Whether an ArcGIS <em>bulk</em> load (Append, a Python script, a CSV upload to ArcGIS Online) rejects out-of-domain values, or writes them for someone to find later, must be checked in the installed version before any procedure promises either. The safe position: every import is followed by a review query regardless.</p></div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Where does each rule live for the practice town?</h3>
    <div class="sorter" data-items='[
      {"t":"condition_code must be 1–5","bin":"Schema","why":"range domain; a review query catches bulk loads that skip it"},
      {"t":"Every inspection must name an existing asset","bin":"Schema","why":"foreign key / relationship class where the platform enforces it; the orphan query as backstop"},
      {"t":"A streetlight must have a wattage before a replacement lamp is ordered","bin":"Application","why":"a cross-table, cross-process rule; bypassed by a phone order → review"},
      {"t":"visited_at must not be later than recorded_at","bin":"Application","why":"cross-field rule; scripts bypass it → review query"},
      {"t":"Two assets of the same type may not be within 2 m of each other","bin":"Review","why":"a spatial check (Chapter 11 tools), run weekly, human decides"},
      {"t":"A DUP request must point at another request","bin":"Application","why":"plus a review query: status = DUP AND duplicate_of IS NULL"}
    ]'>
      <div class="items"></div>
      <div class="bins">
        <div class="bin" data-bin="Schema"><h5>Schema</h5></div>
        <div class="bin" data-bin="Application"><h5>Application</h5></div>
        <div class="bin" data-bin="Review"><h5>Review</h5></div>
      </div>
    </div>
    <p class="small">Some rules can reasonably sit in two places — the point is to write down where, and what happens if that place is bypassed.</p>
  </div>

  <div class="quiz" data-answer="0" data-fb="Check whether condition_code has a default of 3 (then the values may be un-assessed visits wearing the default) and what the blank rule is (if blanks are not allowed and 3 is the default, the “fair” result is manufactured). A third check: defects_found — blank would mean incomplete checklists.">
    <div class="q">A supervisor sees that every drain inspected last month has <code>condition_code = 3</code> and concludes the drains are in fair condition. What should you check before accepting that?</div>
    <div class="opts">
      <button class="opt">Whether the field has a default of 3, and whether blanks are allowed and what they mean.</button>
      <button class="opt">Whether the drains are in Ward A or Ward B.</button>
      <button class="opt">Whether the crew used the right time zone.</button>
      <button class="opt">Nothing — the data says 3, so it is 3.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const desc = { en: { OPEN: "Open", INPROG: "In progress", RESOLVED: "Resolved", CLOSED: "Closed", REOPENED: "Reopened", DUP: "Closed – duplicate" },
                 gu: { OPEN: "ખુલ્લું", INPROG: "કામ ચાલુ", RESOLVED: "ઉકેલાયું", CLOSED: "બંધ", REOPENED: "ફરી ખોલ્યું", DUP: "બંધ – ડુપ્લિકેટ" } };
  const sel = document.getElementById("fStatus"), cond = document.getElementById("fCond"), ht = document.getElementById("fHeight"), guj = document.getElementById("fGuj"), msg = document.getElementById("fmsg");
  function upd() {
    const lang = guj.checked ? "gu" : "en";
    Array.from(sel.options).forEach(o => o.textContent = desc[lang][o.value]);
    document.getElementById("sStatus").textContent = sel.value + "   ← the code, whatever language the screen shows";
    const c = Number(cond.value), h = Number(ht.value);
    const cOk = Number.isInteger(c) && c >= 1 && c <= 5, hOk = h >= 3 && h <= 15;
    document.getElementById("sCond").textContent = cOk ? c + " (" + F8.condWords[c] + ")" : "REJECTED — " + cond.value + " is outside 1–5";
    document.getElementById("sHeight").textContent = hOk ? h + " m" : "REJECTED — " + ht.value + " is outside 3–15 m (feet typed as metres?)";
    msg.className = "status-line " + (cOk && hOk ? "ok" : "bad");
    msg.textContent = cOk && hOk ? "All values accepted. Switch the language: the stored status code does not change." : "A range domain refuses the value at entry time (in software that honours the domain). Fix the value; do not widen the range.";
  }
  [sel, cond, ht, guj].forEach(e => e.addEventListener("input", upd)); guj.addEventListener("change", upd); upd();

  const sl = document.getElementById("naSlider"), out = document.getElementById("naOut");
  function na() {
    const n = +sl.value, total = 100, real = 60; // 100 visits, 60 genuinely fair-or-better
    const withDefault = ((real + n) / total * 100).toFixed(0), honest = (real / (total - n) * 100).toFixed(0);
    out.innerHTML = `Out of <strong>100</strong> visits this month, <strong>${n}</strong> could not be assessed and 60 of the assessed ones were genuinely Fair or better.<br>App A reports <strong>${withDefault}%</strong> “Fair or better” — the ${n} unassessed visits were counted as Fair.<br>App B reports <strong>${honest}%</strong> of <em>assessed</em> visits, plus a work list of <strong>${n}</strong> visits to redo. Only one of these numbers is true.`;
  }
  sl.addEventListener("input", na); na();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
