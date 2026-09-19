<?php $page = ['title' => '12.8 Lab: redesign a misleading map', 'chapter' => 12, 'module' => '12.8']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 12.8 · Guided lab · ArcGIS Pro (primary) or QGIS; optional ArcGIS Online</div>
    <h1>Lab: from “Map B-12” to two honest maps</h1>
    <p class="lead">You receive one checked dataset (the G-12 tables) and a deliberately bad map made from it. Diagnose the bad map, then build a <strong>crew map</strong> and a <strong>manager’s map</strong> from the same data, each with legend, notes and a source-value table — and document why the two look different.</p>
    <div class="outcomes"><h4>Objective</h4>
      <ul><li>List the nine faults of Map B-12 and the module that names each.</li>
      <li>Produce two exported maps whose styling fixes every fault <em>without changing a single value</em>.</li>
      <li>Hand in the source-value table and a one-page rationale.</li></ul></div>
  </div>

  <div class="callout warn"><span class="label">Read this first</span><p>The software steps below were written from the official ArcGIS Pro, ArcGIS Online and QGIS 3.44 documentation and have <strong>not been run</strong> by the author. The expected class memberships and counts are hand-checked from the tables (modules 12.4–12.6). Your instructor must build the package and run both routes once before you rely on them (chapter document, Instructor Appendix I.6) — in particular: what each product does with a scale bar when the map has no coordinate system, and how QGIS draws the NULL ward.</p></div>

  <h2><span class="mod">12.8.1–12.8.2</span>Prerequisites and software</h2>
  <ul>
    <li>Modules 12.1–12.7; Chapter 10’s join by key (10.3); Chapter 11’s count-per-ward (11.6) — you <em>use</em> the summary table, you do not recompute it.</li>
    <li><strong>Primary route:</strong> ArcGIS Pro 3.x, Basic licence or higher, no extension, no ArcGIS Online account. <strong>Alternative:</strong> QGIS Desktop 3.44. <strong>Optional web route:</strong> ArcGIS Online Map Viewer with an organisation account (12.8.10).</li>
    <li>A spreadsheet or text editor for the source-value table and the rationale.</li>
  </ul>

  <h2><span class="mod">12.8.3</span>Input data — copy these five files (made-up data)</h2>
  <p>Flat practice grid, metres, <strong>no coordinate system</strong>. The instructor converts them to a GeoPackage or file geodatabase by the Chapter 3 routes and leaves the coordinate system undefined.</p>
  <div class="tabs"><button>wards_g12.csv</button><button>ward_summary_g12.csv</button><button>station_road_g12.csv</button><button>requests_g12.csv</button><button>readme_g12.txt</button></div>
  <div class="tabpanel"><div class="copywrap"><pre id="wardsCsv"></pre></div></div>
  <div class="tabpanel"><p class="small">An empty cell is NULL. Do not type 0 into W16.</p><div class="copywrap"><pre id="sumCsv"></pre></div></div>
  <div class="tabpanel"><div class="copywrap"><pre>road_id,name,wkt
SR-1,Station Road,"LINESTRING (1000 2500, 3000 2500)"</pre></div></div>
  <div class="tabpanel"><div class="copywrap"><pre id="reqCsv"></pre></div></div>
  <div class="tabpanel"><div class="copywrap"><pre>District G-12 training package. Synthetic. Planar training grid, metres, no CRS, y axis is "up" (not north).
wards_g12: 16 wards, 1 km squares, 2024 boundaries.
ward_summary_g12: open requests per ward at 2026-09-15 counted by spatial join under Policy W-1
  (edge -> lower ward code); households from the 2024 synthetic register; road_km = centreline
  length clipped to ward, rounded to whole km. W16 open_requests is NULL: export not received.
requests_g12: complete request list for W06 and W07 at 2026-09-15; positions reporter-supplied,
  nominal accuracy +/- 15 m; reporter_ref is an opaque reference; reporter_note is free text.
station_road_g12: one centreline.</pre></div></div>

  <h2><span class="mod">Step 0</span>Diagnose Map B-12</h2>
  <p>Your instructor gives you Map B-12 as an image, built from the specification below. Here is a schematic of its ward colouring and its legend. Click each part you think is a fault, then compare with the list.</p>
  <div class="pair">
    <figure class="map-fig" id="badFig"></figure>
    <div>
      <div class="mapcard">
        <div class="t">Ward Request Risk Map</div>
        <div class="leg"><div class="row"><span class="sw" style="background:#3fa34d"></span>Low</div><div class="row"><span class="sw" style="background:#c6d94a"></span>Moderate</div><div class="row"><span class="sw" style="background:#f0a030"></span>High</div><div class="row"><span class="sw" style="background:#c8282a"></span>Severe</div></div>
        <div class="notes">16 wards, 238 open requests. ↑ N</div>
      </div>
      <p class="small">Also on the real Map B-12: all 16 request points at 18 pt, every asset, inspections, a full basemap with names, <code>households</code> printed on each ward, and <code>reporter_note</code> as labels.</p>
    </div>
  </div>
  <div class="faults" id="faults"></div>
  <p class="small">Your written diagnosis (saved in this browser):</p>
  <div class="lab-form"><textarea data-save="diag" placeholder="Fault 1: … (module 12.x) — damages the reader’s decision because …"></textarea></div>

  <h2><span class="mod">12.8.4 · Part A</span>The crew map (W06–W07) — ArcGIS Pro route, not execution-tested</h2>
  <div class="steps" id="stepsA"></div>

  <h2><span class="mod">12.8.4 · Part B</span>The manager’s map (district) — ArcGIS Pro route, not execution-tested</h2>
  <div class="steps" id="stepsB"></div>

  <h2><span class="mod">12.8.5</span>Expected results and checks (hand-checked; not observed in software)</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Check</th><th>Expected</th><th>If it differs, first suspect</th></tr></thead>
    <tbody>
      <tr><td>Open requests drawn on the crew map</td><td class="mono">13 (G01–G13)</td><td>Definition query text or case (<code>'OPEN'</code>)</td></tr>
      <tr><td>Open requests by type</td><td>Pothole 5 · Streetlight out 4 · Blocked drain 2 · Fallen tree 1 · Water leak 1</td><td>A typo in the CSV; a value in “all other values”</td></tr>
      <tr><td>Largest symbols (priority 1)</td><td class="mono">G04, G06, G09, G11</td><td>Size mapping reversed — 1 must be largest</td></tr>
      <tr><td>G02/G03 at 12 pt</td><td>merged at 1:10,000 (42 m footprint); separate at 1:2,000 (8.5 m)</td><td>Symbol size not in points; on-screen scale is nominal</td></tr>
      <tr><td>Joined wards</td><td>16; one NULL (W16); one zero (W03)</td><td>Join key case/spaces; <strong>NULL read as 0 on import — stop and fix</strong></td></tr>
      <tr><td>Count classes, 3 each</td><td>Equal interval 9 / 2 / 4 · Quantile 5 / 5 / 5 · Natural breaks 7 / 4 / 4</td><td>A 16th value present; the product’s tie rule; record observed breaks</td></tr>
      <tr><td>Rate, manual classes ≤2.5 / ≤5.0 / &gt;5.0</td><td>6 / 7 / 2, W16 excluded</td><td>Boundary convention (2.5 and 5.0 belong to the <em>lower</em> class); not per 1,000</td></tr>
      <tr><td>Legend</td><td>numeric intervals with units; a no-data entry with one ward</td><td>Labels left at defaults</td></tr>
      <tr><td>Caption</td><td>“238 over 15 wards; W16 not received”</td><td>NULL replaced by 0 (would read “16 wards”)</td></tr>
      <tr><td>Grey check</td><td>shapes/sizes/fills readable; hatch ≠ palest class</td><td>Colour-only distinctions</td></tr>
      <tr><td>Source-value table</td><td>every value equals the summary table</td><td>A value edited to “fix” the map — never</td></tr>
    </tbody></table></div>
  <p>The <strong>invariant</strong> the instructor checks first: the source-value table sums to 238, W16 is NULL there and “no data” on the map, and W03 is 0 there and in the lowest class on the map. A prettier map that fails this fails the lab.</p>

  <h2><span class="mod">12.8.6</span>Troubleshooting</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Symptom</th><th>Likely cause</th><th>What to do</th></tr></thead>
    <tbody>
      <tr><td>W16 draws in the palest class</td><td>NULL became 0 on import, or the excluded class is off</td><td>Check W16 in the attribute table; re-import with a real NULL; enable “Show excluded values”</td></tr>
      <tr><td>W03 disappears</td><td>Zero excluded with the NULL, or a query <code>open_requests &gt; 0</code></td><td>Zero is a value — remove the exclusion</td></tr>
      <tr><td>Scale bar shows no units or refuses</td><td>Undefined coordinate system</td><td>Verification item; draw a 1,000 m line and label it</td></tr>
      <tr><td>Natural breaks gives 6 / 5 / 4 or similar</td><td>Implementation detail, or a 16th value</td><td>Confirm 15 values; record the observed breaks; 7 / 4 / 4 is the mathematical expectation</td></tr>
      <tr><td>G02/G03 never separate</td><td>Symbol size in map units, or a reference scale set</td><td>Check the symbol unit; clear the reference scale</td></tr>
      <tr><td>Labels overlap everywhere</td><td>No scale range on the label class</td><td>Set the label visibility range</td></tr>
      <tr><td>Join gives 0 matches</td><td>Key mismatch (W01 vs W1, trailing spaces)</td><td>Inspect both key fields (Chapter 10)</td></tr>
    </tbody></table></div>

  <h2><span class="mod">12.8.7–12.8.8</span>Deliverables, and what the instructor checks</h2>
  <div class="lab-card">
    <h4>Hand in</h4>
    <div class="checklist">
      <label><input type="checkbox" data-save="d1"> The Map B-12 diagnosis (step 0).</label>
      <label><input type="checkbox" data-save="d2"> Two exported PDFs — crew map and manager’s map — plus three screenshots of the count legends by method.</label>
      <label><input type="checkbox" data-save="d3"> The source-value table: <code>ward, open_requests, households, road_km, rate_per_1000, class_EI, class_Q, class_NB, class_rate_manual, note</code> — values unchanged from the summary table.</label>
      <label><input type="checkbox" data-save="d4"> One-page rationale: for each of the nine faults, what you changed on which map and why; the design choices that differ between the two maps, stated as reader decisions; every verification item you hit.</label>
      <label><input type="checkbox" data-save="d5"> Your grey-check note: which mode you used and what changed because of it.</label>
    </div>
    <p class="small">The instructor verifies: the source-value table equals the summary table; class memberships match 12.4 (or any difference is explained by a recorded product detail, not an edited value); W16 visibly “no data”, W03 visibly zero; the caption says 15 wards; no <code>reporter_note</code> text on any map; symbol sizes justified for the reading scale; the rationale explains differences in terms of reader decisions, not taste.</p>
  </div>

  <h2><span class="mod">12.8.9</span>QGIS 3.44 route (equivalent exercise; not execution-tested)</h2>
  <ul>
    <li><strong>Load</strong> the CSVs with <em>Add Delimited Text Layer</em> (WKT geometry for wards and road; X/Y for requests), leaving the CRS unset if the installed release allows it (Chapter 3’s verification item). Join the summary to the wards with the layer’s <em>Joins</em> property on <code>ward</code>.</li>
    <li><strong>Crew map:</strong> Symbology ▸ <em>Categorized</em> on <code>category</code>; give each category a different marker shape. Priority by size: data-defined size, or three rule-based classes. Filter with the Query Builder (<code>"status" = 'OPEN'</code>). Labels on <code>request_id</code> with scale-dependent visibility.</li>
    <li><strong>Manager’s map:</strong> Symbology ▸ <em>Graduated</em> on <code>open_requests</code>, mode Equal Interval / Equal Count (Quantile) / Natural Breaks (Jenks) in turn, 3 classes; record memberships. For the rate, add a field <code>"open_requests" / "households" * 1000</code> and edit the class limits to 2.5 and 5.0. <strong>Verification item:</strong> how the graduated renderer draws W16 (NULL) — if it is undrawn, add a rule-based copy that draws <code>"open_requests" IS NULL</code> with a hatch on top.</li>
    <li><strong>Grey check:</strong> View ▸ Preview Mode ▸ Simulate Achromatopsia (Grayscale), then a Protanopia/Deuteranopia mode.</li>
    <li><strong>Layout:</strong> Print Layout with map, legend, scale bar (verification item without a CRS), title and text; export to PDF. Same deliverables and checks as above. Do not claim the class labels or NULL handling match ArcGIS Pro without checking — record what each product shows.</li>
  </ul>

  <h2><span class="mod">12.8.10</span>Optional web route — ArcGIS Online Map Viewer (not execution-tested)</h2>
  <p>Only with an organisation account, and only for the styling part: the flat practice grid cannot be published as an Earth-referenced layer (Chapter 7), so use a copy the instructor has placed in a documented projected CRS <em>for display only</em> and labelled as such. In Map Viewer: <strong>Styles ▸ Types (unique symbols)</strong> on <code>category</code> for the crew map; <strong>Counts and Amounts (color)</strong> on <code>open_requests</code> with <strong>Divide by</strong> = <code>households</code>, <strong>Classify data</strong> on with a chosen method and 3 classes, and <strong>Show features with no value</strong> on with its own style and the label “No data”. Remove <code>reporter_ref</code> and <code>reporter_note</code> from the pop-up’s field list — and write in your rationale, citing 12.7, why that is presentation and not protection.</p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  document.getElementById("wardsCsv").textContent = "ward,wkt\n" + G12.wards.map(w => `${w.id},"POLYGON ((${w.x0} ${w.y0}, ${w.x0 + 1000} ${w.y0}, ${w.x0 + 1000} ${w.y0 + 1000}, ${w.x0} ${w.y0 + 1000}, ${w.x0} ${w.y0}))"`).join("\n");
  document.getElementById("sumCsv").textContent = "ward,open_requests,households,road_km,count_date,note\n" + G12.wards.map(w => `${w.id},${w.open == null ? "" : w.open},${w.hh},${w.road},2026-09-15,${w.note || ""}`).join("\n");
  document.getElementById("reqCsv").textContent = "request_id,x,y,category,priority,status,reported_on,reporter_ref,reporter_note\n" + REQ.map(q => `${q.id},${q.x},${q.y},${q.cat},${q.pri},${q.status},${q.on},${q.ref},${q.note.includes(",") ? '"' + q.note + '"' : q.note}`).join("\n");

  // Map B-12: NULL treated as 0, quantile with 4 classes over 16 values, green→red
  const vals = G12.wards.map(w => w.open == null ? 0 : w.open), br = quantile(vals, 4);
  renderG12(document.getElementById("badFig"), { style: w => { const v = w.open == null ? 0 : w.open; const c = classOf(v, br); return { fill: BADRAMP[c], dark: c === 3, label: v }; }, title: "Ward Request Risk Map", caption: "Schematic of Map B-12’s colouring: quantile, 4 classes of 4 wards, W16’s missing value treated as 0 (bottom-right, green)." });

  const faults = [
    ["Title “Risk Map”; no date, source or method", "Claims risk from a count of reports (12.5.3); no context (12.6.1)."],
    ["W16’s NULL replaced by 0 and classified", "Missing shown as zero (12.6.2). The ward nobody counted is “Low”."],
    ["Quantile, 4 classes of 4 wards — method not stated", "Undisclosed method; W16 forced into “Low” (12.4, 12.6.2)."],
    ["Legend “Low / Moderate / High / Severe” — no numbers, no units", "Intervals and units not disclosed (12.4.2)."],
    ["Green→red ramp; W03 (0) and W16 (no data) the same green", "Colour-only; red/green; zero = missing (12.3.3, 12.6.2)."],
    ["Raw count compared across wards of very different population and road length", "Inappropriate comparison variable (12.5)."],
    ["Every layer on: 18 pt points, all assets, inspections, basemap names, households labels, reporter notes", "Clutter, no hierarchy, oversized symbols, personal details (12.1.3, 12.3.2, 12.7.3)."],
    ["North arrow on the schematic grid; no scale bar", "Decoration that claims a direction; the useful element missing (12.6.1)."],
    ["Caption “16 wards, 238 open requests”", "Implies W16 was counted (12.6.2)."]
  ];
  document.getElementById("faults").innerHTML = faults.map((f, i) => `<div class="fault"><span class="n">${i + 1}</span><div>${f[0]}<div class="why">${f[1]}</div></div></div>`).join("");
  document.querySelectorAll("#faults .fault").forEach(f => f.addEventListener("click", () => f.classList.toggle("open")));

  const A = [
    ["Open the package", "Add wards, road and requests to a new map. The coordinate system is undefined by design — do <em>not</em> assign one (Chapter 6). Record what the software reports for the map’s coordinate system and units (verification item)."],
    ["Filter to the decision", "Definition query <code>status = 'OPEN'</code> on the requests. Expected: 13 features. Decide, and write down, whether the 3 closed ones are shown hollow or left out."],
    ["Categories as shapes", "Symbology ▸ <strong>Unique values</strong> on <code>category</code>. Give each of the five values a different <em>shape</em> (circle, square, triangle, diamond, star) as well as a hue. The “all other values” class should be empty. Expected counts: 5 / 4 / 2 / 1 / 1."],
    ["Priority as size", "Vary symbol <em>size</em> by <code>priority</code> so that 1 is largest (verification item: the exact control in the installed release; if not available, three manual size classes — document it). Expected largest: G04, G06, G09, G11."],
    ["Labels", "Label <code>request_id</code> only. Show labels only when zoomed in past a stated scale (e.g. 1:5,000). Never label <code>reporter_note</code>."],
    ["Context and hierarchy", "Station Road mid-grey; ward outlines thin grey, no fill; small grey ward codes. No basemap (there is no Earth position to put one under)."],
    ["Scale range", "Requests always visible; the label class hidden beyond 1:5,000. Record both numbers."],
    ["The close pair", "Zoom to G02/G03 at 1:2,000 and 1:10,000; record whether they separate. Decide how the whole-area view handles the pair (count label, cluster, or accept the merge with a note)."],
    ["Grey check", "View tab ▸ Accessibility ▸ Color Vision Simulator ▸ Achromatopsia, then a red/green mode. Confirm type (shape), priority (size) and status (fill) still read. Exit the simulator."],
    ["Layout and export", "A4 layout; Insert ▸ Map Surrounds ▸ Legend and Scale Bar (verification item on an undefined CRS — else a drawn 1,000 m line). Title “Open requests — W06/W07 — crew sheet, 2026-09-15 (made-up)”, source line, “positions ± 15 m”, “G06 on the W06/W07 edge — assigned to W06 under Policy W-1”. No north arrow; write “schematic grid — not oriented to north”. Export to PDF."]
  ];
  const B = [
    ["Join the summary", "Join <code>ward_summary_g12</code> to the wards on <code>ward</code> (one-to-one, 16 to 16). Check: <code>open_requests IS NULL</code> → 1 (W16); <code>open_requests = 0</code> → 1 (W03)."],
    ["Three classifications of the count", "Symbology ▸ <strong>Graduated Colors</strong> on <code>open_requests</code>, 3 classes. Equal Interval, then Quantile, then Natural Breaks (Jenks): record the breaks and every ward’s class; compare with module 12.4 (expected 9/2/4, 5/5/5, 7/4/4). Screenshot each legend with its numbers."],
    ["The rate", "Normalization = <code>households</code> (or a field <code>open_requests / households * 1000</code>). <strong>Manual</strong> classes with breaks 2.5 and 5.0, upper limit inclusive. Expected: 6 / 7 / 2, W16 excluded. Write why manual (ties at 2.0, 4.0, 5.0)."],
    ["Show the missing ward as missing", "Symbology pane ▸ More ▸ <strong>Show excluded values</strong> (or Show values out of range); give W16 a hatch that is not on the ramp; legend text “No data — export not received”. Expected: exactly one feature."],
    ["Legend labels", "Edit the class labels to intervals with units: “0 – 2.5”, “&gt; 2.5 – 5.0”, “&gt; 5.0 open requests per 1,000 households”. Never leave “Low / High”."],
    ["Hierarchy", "Thin ward outlines; small ward codes; <em>no</em> points, no road, no household labels. One-hue light-to-dark ramp, not red/green."],
    ["Grey check", "As in Part A; the hatch must still differ from the palest class."],
    ["Layout and export", "A4; title “Open service requests per 1,000 households — District G-12 wards — 2026-09-15 (made-up data)”; legend with the three intervals and the no-data entry; the full method note from 12.6.1; caption “238 open requests over 15 wards; W16 not received”; the 1 km reference; no north arrow. Export to PDF."],
    ["Source-value table", "One row per ward with all values and the class columns. This is what the instructor uses to prove the maps did not change or hide anything."],
    ["Rationale", "One page: each of the nine faults → what changed on which map and why; the choices that differ between the two maps (12.1’s table is the frame); what you considered and rejected; every verification item."]
  ];
  const mk = (id, steps, key) => { document.getElementById(id).innerHTML = steps.map((s, i) => `<label class="step"><input type="checkbox" data-save="${key}${i}" style="margin-top:.35rem"><span class="n">${i + 1}</span><div><strong>${s[0]}.</strong> ${s[1]}</div></label>`).join(""); };
  mk("stepsA", A, "a"); mk("stepsB", B, "b");
  initNotes();   // the step checkboxes were created after common.js ran; bind their saved state now
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
