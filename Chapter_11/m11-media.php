<?php $page = ['title' => '11.11 Recap, media brief, and what comes next', 'chapter' => 11, 'module' => '11.11']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 11.11 · Recap & next</div>
    <h1>What you can now do — and what Chapter 12 does with it</h1>
    <p class="lead">You started this chapter able to <em>ask</em> a GIS questions. You can now <em>build</em> answers — and, more importantly, say why each answer is right and what it does not mean.</p>
  </div>

  <h2><span class="mod">Recap</span>The chapter on one page</h2>
  <div class="grid-2">
    <div class="card">
      <h3 style="margin-top:0">The habit</h3>
      <ol>
        <li><strong>Write the specification first</strong> — decision, study area, time window, eligible records, units and method, output <em>kind</em>, checks (11.1).</li>
        <li>The kind — selection, new shape, extra columns, summary — <strong>chooses the operation</strong>. A worksheet row nobody reads is deleted.</li>
        <li><strong>Predict</strong> the result from the coordinates, run, compare (11.8).</li>
        <li><strong>Change one thing</strong> and see what moves.</li>
        <li><strong>Record enough to rerun</strong>: tool and version, all parameters, CRS and transformation, source edition, outputs, limitations, untouched inputs.</li>
      </ol>
    </div>
    <div class="card">
      <h3 style="margin-top:0">The five operations</h3>
      <ul>
        <li><strong>Buffer</strong> — the polygon within a distance. Distance and unit, end type, planar/geodesic, and dissolve type all change the answer. Never a travel time (11.2).</li>
        <li><strong>Clip</strong> — cuts geometry, copies attributes unchanged; stored lengths go stale (11.3).</li>
        <li><strong>Intersect</strong> — builds the shared parts with attributes from every input and splits records; never sum copied numbers without a rule (11.4).</li>
        <li><strong>Dissolve</strong> — GROUP BY for shapes; drops or scrambles the other columns; may give multipart features (11.5).</li>
        <li><strong>Spatial join</strong> — count or copy by location; keep the zeros, list the outsiders, reconcile assigned + twice + outside = total (11.6).</li>
      </ul>
      <p class="small">Raster: state the NoData policy (8 of 9 cells → 20, not 17.78); a mask needs units and a datum and is not a risk map; check extent, cell size, alignment and missing data before combining (11.7).</p>
    </div>
  </div>

  <h2><span class="mod">Media brief</span>Slides and narration, mapped to modules</h2>
  <p>For the presenter and the audio version. Each slide group has one learning objective and a question <em>before</em> the answer; every drawing is the practice fixture, to scale, labelled “made-up, metres, no CRS”.</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Module</th><th>Slide group</th><th>Question before the answer</th></tr></thead>
    <tbody>
      <tr><td>11.1</td><td>Seven lines first; the four kinds of answer; the worksheet row that goes</td><td>“Is ‘how many per ward’ a list, a shape, a column or a count?”</td></tr>
      <tr><td>11.2</td><td>Four buffer settings; R1 at 300 m; the threshold table</td><td>“Round or flat — which changes P6?”</td></tr>
      <tr><td>11.3</td><td>Select versus clip; the stale 2000</td><td>“Which number is stale?”</td></tr>
      <tr><td>11.4</td><td>Asks versus builds; two records from one road; allocation rules</td><td>“What does length_m = 2000 mean on each half?”</td></tr>
      <tr><td>11.5</td><td>Wards to zones; Z2 in two pieces; the QGIS first-feature value</td><td>“How many features? How many parts?”</td></tr>
      <tr><td>11.6</td><td>Vocabulary; A 3, B 3, C 0; the reconciliation table</td><td>“Three plus three is six. There are five complaints. Where is the sixth?”</td></tr>
      <tr><td>11.7</td><td>20 versus 17.78 versus −1093; the mask with units; the half-cell shift</td><td>“What should the NoData cell show?”</td></tr>
      <tr><td>11.8</td><td>Invariants; change one thing; the seven log items</td><td>“What does the history pane <em>not</em> record?”</td></tr>
      <tr><td>11.9–11.10</td><td>Lab overview; the five critical misconceptions</td><td>—</td></tr>
    </tbody></table></div>
  <p><strong>Narration notes.</strong> Say “buffer”, “clip”, “intersect” (the operation) and “intersects” (the question — stress the <em>s</em>). Say “NoData” as two words. Introduce every unit the first time (“three hundred metres, straight-line, on the flat grid”). Describe drawings in words: “a horizontal road two kilometres long; a band three hundred metres above and below; a half-circle bulging past each end.” Pause before each prediction. Detailed click steps stay in the lab handout.</p>
  <p><strong>Interactive material.</strong> The demos on these pages <em>are</em> the recommended interactive material: a 2D operation explorer on the paper fixture with the attribute table changing beside the map. The only 3D idea worth building is a column view of the 3 × 3 grid where the NoData cell is a <em>missing</em> column (not a zero-height one), with a toggle that fills it and moves the mean from 20.0 to 17.78 — and the 2D version in 11.7 already teaches the same thing. No other 3D content is proposed: buffer, clip, intersect, dissolve and spatial join are flat operations, and a 3D scene would only suggest the outputs are surfaces.</p>

  <h2><span class="mod">Next</span>Chapter 12 — showing the checked result on a map</h2>
  <p>You now have honest numbers: per-ward counts, an “outside” list, an on-the-line case, and a sensitivity note. Chapter 12 asks how to <em>show</em> them without hiding those limitations: raw counts versus rates and density (the Z1/Z2 “mean households” trap returns as normalisation), how classification changes a map’s message, how to draw the boundary and outside cases <em>on</em> the map instead of in a footnote, and how to state uncertainty plainly. Later phases pick up the honest answer to the supervisor in Scenario 1 — a road network and travel-time areas — and the automation of the lab as a model or a SQL script, for which your analysis worksheet is already the design.</p>
  <p><a class="btn primary" href="./">← Chapter home</a> &nbsp; <a class="btn ghost" href="glossary">Glossary</a> &nbsp; <a class="btn ghost" href="../">Course index</a></p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<?php require __DIR__ . '/../partials/end.php'; ?>
