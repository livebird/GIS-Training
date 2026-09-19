<?php $page = ['title' => '9.10 Recap, media brief and what comes next', 'chapter' => 9, 'module' => '9.10']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 9.10 · Recap, media and transition</div>
    <h1>What to carry forward</h1>
    <p class="lead">Seven ideas, three numbers, one habit — and a checked dataset that Chapter 10 will query.</p>
  </div>

  <h2><span class="mod">Recap</span>Seven things to remember</h2>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">1. Five questions, one purpose</h4><p>Position, values, completeness, rules, freshness — asked against a <em>stated</em> decision. Fill the six-item intake checklist before any edit; the original copy is your only undo.</p></div>
    <div class="card"><h4 style="margin-top:0">2. Five ways to make a dot</h4><p>Tracing, importing, GNSS, address lookup, scanned map. Each needs its own history. GPS is one GNSS among several; accuracy belongs to a documented observation, never to “phones”.</p></div>
    <div class="card"><h4 style="margin-top:0">3. Georeferencing and the check point</h4><p>Three control points fit exactly, so a zero residual proves nothing. Spread the control, hold out a check point, report <em>its</em> error. Georeferencing creates coordinates; assignment relabels; reprojection recomputes; geocoding creates from text.</p></div>
    <div class="card"><h4 style="margin-top:0">4. Predict, edit a copy, review</h4><p>Snapping closes intended gaps — and with the wrong agent or a pixel tolerance at the wrong zoom it moves observations and joins unrelated things. Review visually, in the table, and against the check that started it.</p></div>
    <div class="card"><h4 style="margin-top:0">5. Invalid shape ≠ broken rule</h4><p>One shape that cannot be understood, versus valid shapes that break a business rule. Rules come from the meaning of the layer; cul-de-sacs, contractual overlaps and bridges are documented exceptions.</p></div>
    <div class="card"><h4 style="margin-top:0">6. Five queries; evidence, not distance</h4><p>Duplicate IDs, invalid categories, missing values, unit errors, orphans. Two complaints at one spot are deduplicated on reporter, text, time and channel — location last.</p></div>
    <div class="card" style="grid-column:1/-1"><h4 style="margin-top:0">7. The three numbers: 0 · 250,000 · 500,000 m²</h4><p>Ward C as typed (meaningless), as auto-repaired (valid and wrong), as corrected from the register (right). Keep the original, record before/after, fix the cause upstream, inspect what a repair did, and leave open items open — INS-0005’s blank score, INS-0006’s orphan, the SL-0114 pair, TR-0301 outside the wards.</p></div>
  </div>

  <h2><span class="mod">Media brief</span>What the slides, audio and diagrams should show</h2>
  <p>A summary for whoever builds the presentation and audio lesson; the full slide-by-slide outline, narration outline, diagram specifications and the 3D storyboard are in the Media Appendix of the chapter document.</p>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">D1 — Snapping, before/after</h4><p>Pair 1: R4’s 3 m gap closed onto R1. Pair 2: a 15-pixel snap circle at 1 m/px pulling complaint P9 onto drain DR-0042, 11.2 m away. Each labelled with agent and tolerance. (You used it live in 9.4.)</p></div>
    <div class="card"><h4 style="margin-top:0">D2 — Ward C, three panels</h4><p>As typed (crossing at (500, 1250), “0 — meaningless”); corrected by evidence (500,000 m²); automatic repair (two triangles, 250,000 m², “valid — and wrong”). The instructor replaces panel 3’s caption with the observed tool result.</p></div>
    <div class="card"><h4 style="margin-top:0">D3 — Gap and overlap</h4><p>The 2,000 m² sliver between A and the delivered B, with P5 marked “2 m outside B”; beside it a 10 m overlap strip. Under both: “valid shapes; rule broken”.</p></div>
    <div class="card"><h4 style="margin-top:0">D5 — The check point</h4><p>The scan with G1–G3; K1 true vs. modelled with a 12 m arrow; then ±3 m residual bars after the refit with “report improved, map worsened”.</p></div>
    <div class="card"><h4 style="margin-top:0">M.5 — the one 3D scene</h4><p>A flyover over Main Road at (1800, 500) shown in top view and tilted view, with a “level” toggle: under a plain 2D-crossing rule the network wrongly connects them; under “same level only” or a marked exception it does not. Heights are labelled <em>schematic</em>. A 2D plan-plus-section drawing is the text alternative. No other 3D content is proposed.</p></div>
    <div class="card"><h4 style="margin-top:0">Narration rules</h4><p>Say “GNSS”, “RMS”, “OGC”, “CRS” as letters. Read every distance with its unit (“three metres”; “ten pixels — which is a different ground distance at every zoom”). Walk Ward C’s corners aloud: “bottom-left, bottom-right, top-<em>left</em>, top-right — the second and fourth legs cross”. Pause for a prediction before each answer.</p></div>
  </div>

  <h2><span class="mod">Sources</span>Where the facts came from</h2>
  <p>Primary readings: the QGIS <em>Gentle Introduction</em> pages on Data Capture and Topology; the ArcGIS Pro pages Overview of georeferencing, Check Geometry, Repair Geometry, Topology in ArcGIS, the polygon and polyline topology-rule pages, Configure/Use snapping, A quick tour of editing, Save or discard edits, the geocoding result fields, Find Identical, Define Projection and Project; the ArcGIS Field Maps page on high-accuracy collection; GPS.gov on GNSS; the QGIS 3.44 User Guide pages on Editing, the Georeferencer, the Topology Checker plugin and the Check validity / Fix geometries algorithms; the PostGIS manual’s Geometry Validation section and ST_MakeValid. Product behaviour was read on 19 September 2026 (ArcGIS Pro 3.7, QGIS 3.44). The full reference list with links is in the chapter document.</p>

  <h2><span class="mod">Next</span>Chapter 10 — attribute queries and spatial relationships</h2>
  <p>You can now say what a dataset is fit for, how each location was obtained, whether its shapes are valid and its relationships intact, and what is still open. Chapter 10 <em>queries</em> that data: conditions on values, joins by identity, and spatial relationships — including the boundary case you met twice here, P5 on the shared ward line, whose answer changed when Ward B’s corner slipped 4 m. Checked data makes those answers mean something. <strong>Keep your defect log and unresolved list</strong>; Chapter 10 starts from the corrected copy.</p>
  <p><a class="btn primary" href="./">← Chapter home</a> &nbsp; <a class="btn ghost" href="glossary">Glossary</a></p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<?php require __DIR__ . '/../partials/end.php'; ?>
