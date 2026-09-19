<?php $page = ['title' => '10.10 Recap, media brief and what comes next', 'chapter' => 10, 'module' => '10.10']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 10.10 · Recap, media and transition</div>
    <h1>What to carry forward</h1>
    <p class="lead">Eight ideas, the fixture numbers you should now know by heart, and what Chapter 11 builds from them.</p>
  </div>

  <h2><span class="mod">Recap</span>Eight things to remember</h2>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">1. Four actions, one of them destructive</h4><p>Filter, selection, export, modify. Predict the count and “does the data change?” on a visible worksheet <em>before</em> you click (10.1).</p></div>
    <div class="card"><h4 style="margin-top:0">2. Null is neither true nor false</h4><p><code>IS NULL</code> is the only test for missing values. A null is excluded from a condition <em>and</em> from its negation. Zero is a value; unknown is not. Quotes, field delimiters, case and date literals depend on the data source (10.2).</p></div>
    <div class="card"><h4 style="margin-top:0">3. Duplicate keys multiply, unmatched keys vanish</h4><p>Six requests → eight rows. Say whether you are counting entities, matched pairs or distinct matches. Never sum a left-table column over multiplied rows (10.3).</p></div>
    <div class="card"><h4 style="margin-top:0">4. Interior, boundary, exterior</h4><p>Six predicates are statements about which parts meet. “Intersects?” is a question; “intersection” is a construction. A bounding box says “maybe” (10.4).</p></div>
    <div class="card"><h4 style="margin-top:0">5. On the line, the words split</h4><p>PostGIS/QGIS <em>contains/within</em> say no to a boundary point; <em>covers/intersects</em> say yes. Esri’s plain <em>Within</em> is inclusive; <em>Completely within</em> is strict. A hole is exterior. Trust definitions and one executed case (10.5).</p></div>
    <div class="card"><h4 style="margin-top:0">6. Nearest needs a tie rule; within needs a unit</h4><p>Esri’s tie rule is random; QGIS documents none. Whether “within 300” includes exactly 300 is per engine. Metre thresholds on degrees are wrong; a Z column does not make a query 3D (10.6).</p></div>
    <div class="card"><h4 style="margin-top:0">7. Policy turns matches into assignments</h4><p>Boundary, overlap, unmatched, multiple matches — write the rule, apply it, and report raw and policy results side by side. <code>Join_Count</code> counts matches: a point in two polygons is counted twice (10.7).</p></div>
    <div class="card"><h4 style="margin-top:0">8. The practice-grid numbers</h4><p>Strict A = {P1, P2}, B = {P3, P4}; inclusive adds P5 to both; ≤ 300 m = {P1, P2, P3, P5, P6}; open ∧ inside ∧ near = {P1, P3, P5}; six matches, five distinct requests, one policy decision (10.8).</p></div>
  </div>

  <h2><span class="mod">Media brief</span>What the slides, audio and diagrams should show</h2>
  <p>A summary for whoever builds the presentation and audio lesson; the full slide-by-slide outline, narration outline, diagram specifications and interactive spec are in the Media Appendix of the chapter document.</p>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">Interactive “move the point” (built here as module 10.5)</h4><p>One polygon with a hole, one draggable point, a live table of predicate results computed by the <em>stated</em> PostGIS definitions, with ArcGIS Pro and QGIS option names mapped to the same rules. Acceptance test: H1 → T,T,T,F; H2 → F,T,T,T; H3 → all F; H4 → all F; H5 → same as H2. No 3D scene is needed for the principal lesson.</p></div>
    <div class="card"><h4 style="margin-top:0">Diagram D3 — four positions</h4><p>The depot with outer and inner rings, points H1–H5, the hole shaded like the street, and the 5 × 4 predicate table beside it. Caption must say “schematic; made-up grid; metres”.</p></div>
    <div class="card"><h4 style="margin-top:0">Diagram D4 — join inflation</h4><p>Six request cards on the left, three register cards on the right, lines for each match: P1 and P2 with two lines, P5 and P6 with none. Counter: “8 rows, 6 matches, 4 requests matched, 2 unmatched”.</p></div>
    <div class="card"><h4 style="margin-top:0">Diagram D5 — bridge, 2D versus 3D</h4><p>Top-down view: two crossing lines, “intersects (2D): true”. Oblique view: the deck 6 m above, “3D intersects: false”. Label Z as schematic.</p></div>
  </div>
  <div class="callout note"><span class="label">Narration rules for the audio lesson</span><p>Walk one point around the depot: inside, on the wall, in the courtyard, on the street — say each predicate’s answer and why, and pause before each. Say “neither” out loud whenever a count leaves out the unknowns. Read the Esri tie sentence verbatim. Keep click instructions out of the audio; they belong in the lab handout.</p></div>

  <h2><span class="mod">Sources</span>Where the facts came from</h2>
  <p>Primary readings for this chapter: the ArcGIS Pro <em>SQL reference for query expressions</em>; the PostGIS manual pages for <code>ST_Contains</code>, <code>ST_Covers</code>, <code>ST_Intersects</code>, <code>ST_Within</code>, <code>ST_Touches</code>, <code>ST_Disjoint</code>, <code>ST_Overlaps</code>, <code>ST_DWithin</code>, <code>ST_Distance</code> and <code>ST_3DIntersects</code>; the ArcGIS Pro pages for <em>Spatial Join</em>, <em>Select Layer By Location</em> and its graphic examples, <em>Add Join</em>, <em>Select Layer By Attribute</em>, definition queries, <em>Calculate Field</em>, <em>Near</em> and “How proximity tools calculate distance”; the ArcGIS REST API <em>Query</em> reference; the QGIS 3.44 pages on vector selection, joins and expression operators; and PostgreSQL’s comparison-operator page. Every page was read on 19 September 2026; the chapter document lists the exact URLs, and its Instructor Appendix lists what must still be verified in installed software.</p>

  <h2><span class="mod">Next</span>Chapter 11 — spatial analysis fundamentals</h2>
  <p>This chapter only ever <em>tested</em> relationships. Chapter 11 <em>builds</em> from them: a <strong>buffer</strong> makes the “within 300 m” band a real polygon; <strong>clip</strong> and <strong>intersect</strong> construct the shared shape that 10.4.2 said we would not build yet; <strong>dissolve</strong> merges; and spatial joins <em>transfer and summarise</em> attributes with the target/join and one-to-many rules you verified here. Every Chapter 11 output should be checked against a Chapter 10 predicate count — the six-match, five-distinct answer is your first invariant.</p>
  <p><a class="btn primary" href="./">← Chapter home</a> &nbsp; <a class="btn ghost" href="glossary">Glossary</a></p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<?php require __DIR__ . '/../partials/end.php'; ?>
