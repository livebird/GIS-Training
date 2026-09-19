<?php $page = ['title' => '4.8 Independent check and progression gate', 'chapter' => 4, 'module' => '4.8']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 4.8 · Assessment · answers are with your instructor</div>
    <h1>Independent check</h1>
    <p class="lead">Do this without looking back at the lab’s expected values. Answer every item under the stated assumptions; if you think an item is missing information, say what is missing instead of guessing. All grids here are <strong>new made-up data</strong>, not the fixtures. Write your answers in a document and submit them to your instructor.</p>
    <div class="outcomes"><h4>What you submit</h4>
      <ul><li>Written answers to Q1–Q6 and S1–S2.</li>
      <li>The practical task: your working, your tables, and (if you ran software) the readings with the software version.</li>
      <li>The oral explanation, given live or recorded.</li></ul></div>
  </div>

  <h2><span class="mod">4.8.1</span>Concept questions (six)</h2>
  <div class="card">
    <p><strong>Q1.</strong> <span class="pill">4.1 · LO1</span> A single-band raster covers Ward A with 10 × 10 cells of 100 m; each cell holds a land-cover code. Which <strong>one</strong> of these can it answer <em>directly</em>, and why can it not answer the other three?<br>(a) How many streetlights are in the ward? (b) How much of the ward is water? (c) Which streetlight is nearest request P3? (d) How long is Road R1?</p>
    <p><strong>Q2.</strong> <span class="pill">4.2 · LO2</span> A raster layer shows one cell in dark red. List, in the order you would check them, the things you must know before you can say what the dark red <em>means</em>, and say for each where you would look.</p>
    <p><strong>Q3.</strong> <span class="pill">4.3 · LO3</span> A header reads <code>NCOLS 8, NROWS 6, XLLCORNER 1200, YLLCORNER 300, CELLSIZE 50</code> (practice grid, metres, no coordinate system). (a) State the extent as left, right, bottom, top. (b) Give the 1-based row and column of the cell containing (1330, 460), showing the arithmetic and the edge rule you used. (c) Can a feature 5 m wide be represented in this raster? (d) Does the cell size tell you how accurately the raster is positioned? One sentence.</p>
    <p><strong>Q4.</strong> <span class="pill">4.4 · LO4</span> A one-row raster holds <code>4, 0, NoData, 6, 2</code> (requests counted per cell). (a) Give the mean if NoData is excluded and the mean if NoData is treated as 0. (b) In one sentence each: what does the 0 cell assert, and what does the NoData cell assert? (c) Which mean would you report, and what must go with it?</p>
    <p><strong>Q5.</strong> <span class="pill">4.5 · LO5</span> A soil-type raster (integer codes 1–6) at 30 m must be brought onto a 10 m grid to compare cell by cell with a 10 m raster. Which <strong>one</strong> method is defensible: (a) bilinear, smoother; (b) cubic, smoothest; (c) nearest neighbour, creates no new values; (d) average, so the 10 m cells represent the surrounding area? Explain why each of the other three fails, and state one thing the 10 m output will <em>not</em> contain.</p>
    <p><strong>Q6.</strong> <span class="pill">4.6 · LO6</span> You receive an elevation raster of Ward B with values 40–75 and a convincing hillshade. (a) Name the two facts about the values you need before comparing it with another elevation raster. (b) Does the hillshade tell you whether it is a terrain model or a surface model? Why or why not? (c) Name one real-world structure that a one-value-per-cell height field cannot represent.</p>
  </div>

  <h2><span class="mod">4.8.2</span>Scenario questions (two)</h2>
  <div class="card">
    <p><strong>S1.</strong> <span class="pill">4.2, 4.4 · LO2, LO4</span> The office receives <code>flood_extent_B.tif</code>: single band, integer, values 0 and 1, NoData value 255, covering Ward B at 20 m, with a one-line note “1 = flooded”. A dashboard built on it says “62 % of Ward B flooded”. Write the checks you would make before accepting that figure, in order; then write the <em>two</em> honest versions of the percentage statement that could be correct depending on what you find (state the denominator in each). Finally, say what the raster cannot tell you even if every check passes.</p>
    <p><strong>S2.</strong> <span class="pill">4.3, 4.8 · LO3</span> A contractor sends a scanned drainage plan of Ward B as a PNG, 3,000 × 2,000 pixels, with no world file and no georeferencing in the header, and writes: “We resampled the scan to 0.1 m pixels, so the plan is accurate to 0.1 m and can go straight onto the map.” (a) Can the image be placed reliably on the map as delivered? Say what is missing and what evidence would be needed. (b) Is the accuracy claim valid? Explain the two things the contractor has mixed up. (c) After the image <em>is</em> correctly placed, what one property would you still need to state before anyone measures from it?</p>
  </div>

  <h2><span class="mod">4.8.3</span>Independent practical task (new inputs)</h2>
  <div class="card">
    <p><strong>Inputs (made-up).</strong> A flood-depth raster over part of Ward B, as the file <code>flood_depth_check.asc</code>. Values are <strong>metres of standing water</strong> measured on 2026-09-15 (synthetic); <code>-99</code> marks cells that were not surveyed. Practice grid: metres, no coordinate system; row 1 is the top row.</p>
    <div class="copybar"><h4 style="margin:0;font-family:var(--font-mono);font-size:.95rem">flood_depth_check.asc</h4><button class="btn small" data-copy="fdtxt">Copy</button></div>
    <pre class="listing" id="fdtxt">NCOLS 5
NROWS 4
XLLCORNER 1500
YLLCORNER 100
CELLSIZE 25
NODATA_VALUE -99
0.0 0.0 0.2 0.4 0.5
0.0 0.1 0.3 0.6 -99
0.0 0.0 0.2 -99 -99
0.0 0.0 0.0 0.1 0.3</pre>
    <p>Also given: a new inspection point <strong>Q1 at (1560, 130)</strong> and the Chapter 1 request <strong>P3 at (1200, 250)</strong>.</p>
    <p><strong>Tasks — show your working for every number.</strong></p>
    <ol>
      <li>From the header alone: number of cells, area of one cell in m², and the extent (left, right, bottom, top).</li>
      <li>Count the valid cells, the NoData cells, and the cells whose value is exactly 0. In one sentence, what does a 0 cell assert and what does a NoData cell assert?</li>
      <li>Compute the mean depth over valid cells, and the mean if NoData were treated as 0. Which would you report? Write the reporting sentence with rule and coverage.</li>
      <li>Give the depth at Q1, showing the row/column arithmetic, and the depth at P3 — or explain why there is none.</li>
      <li>Predict the result of resampling to 12.5 m cells with nearest neighbour: dimensions, the value at Q1, the number of NoData cells, and the mean over valid cells. Then say which of nearest neighbour and bilinear is appropriate for <em>this</em> dataset, with the numerical reason.</li>
      <li>State one thing the raster cannot establish about flooding in Ward B, and one thing it cannot establish about the cell containing Q1.</li>
      <li><strong>If software is available:</strong> create the file, load it, and confirm items 1–4 from the layer’s properties and cell readings; note any difference from your hand results and explain it. If not available, say so; items 1–6 stand on their own.</li>
    </ol>
  </div>

  <h2><span class="mod">4.8.4</span>Oral explanation (one)</h2>
  <div class="card"><p>In no more than two minutes, explain to a manager who has never used GIS why the mean of the chapter’s elevation grid is 13.6 m and not 12.75 m, and why the smoother-looking 50 m bilinear copy is not “better data” than the 100 m original. You may draw one grid. You will get one follow-up question.</p></div>

  <h2><span class="mod">4.8.5</span>Scoring and progression</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Component</th><th>Points</th><th>What earns them</th></tr></thead>
    <tbody>
      <tr><td>Concept questions Q1–Q6</td><td>30 (5 each)</td><td>The one defensible answer under the stated conditions, with the reason; for Q1 and Q5, why the other options fail</td></tr>
      <tr><td>Scenario questions S1–S2</td><td>20 (10 each)</td><td>Checks in a sensible order; both honest statements with denominators; the georeferencing/accuracy distinction made explicitly</td></tr>
      <tr><td>Practical task</td><td>40</td><td>Reasoning and assumptions (10); numerical correctness of items 1–5 (15); verification — the cross-checks between header arithmetic, counts and means, and the software comparison if run (10); documentation — rule, coverage and limitations written as for a report (5)</td></tr>
      <tr><td>Oral explanation</td><td>10</td><td>The two ideas conveyed without jargon, with the numbers, and the follow-up answered</td></tr>
    </tbody></table></div>
  <div class="callout warn"><span class="label">Progression rule</span><p>Suggested pass: 80 points. Whatever the total, these are <strong>critical misconceptions</strong> and need correction plus a fresh exercise before Chapter 5: treating NoData as zero (or zero as NoData) in a statistic; stating positional accuracy from cell size; applying an averaging method to category codes; inferring a unit or a class from a colour; and claiming an image with no georeferencing is correctly placed. A screenshot is not evidence of a value — the value must be read and written down.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<?php require __DIR__ . '/../partials/end.php'; ?>
