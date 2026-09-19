<?php $page = ['title' => '5.9 Independent check', 'chapter' => 5, 'module' => '5.9']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 5.9 · Assessment</div>
    <h1>Independent check and progression gate</h1>
    <p class="lead">This is what you submit to your instructor. There are no answers on this page — the instructor holds the answer key. Where a question says “per RFC 7946” or “per the EPSG record”, use that contract and no other.</p>
    <div class="outcomes"><h4>Submit</h4>
      <ul><li>Written answers to Q1–Q6 and S1–S2.</li>
      <li>The practical task: a diagnosis table for X1–X3 plus the five deliverables.</li>
      <li>Be available for the three-minute oral check.</li></ul></div>
  </div>
  <div class="callout note"><span class="label">Before you start</span><p>Answer every item under its stated assumptions. If you think an item is under-specified, say what is missing rather than guessing. The quick checks in modules 5.1–5.7 were practice; these are marked.</p></div>

  <h2><span class="mod">5.9.1</span>Concept questions (30 marks, 5 each)</h2>
  <div class="card"><p><strong>Q1.</strong> <span class="pill">5.1 · LO1</span><br>A message reads: “Point is at 254318.4, 2547906.2.” List the <em>minimum</em> additional items that would make this a complete coordinate description, and name the one that the <em>file format</em> rather than the CRS supplies.</p></div>
  <div class="card"><p><strong>Q2.</strong> <span class="pill">5.2 · LO2</span><br>Convert <code>8° 31′ 12″ S, 115° 13′ 30″ E</code> to signed decimal degrees. Show the arithmetic, state the sign rule you applied to each axis, and verify by converting back.</p></div>
  <div class="card"><p><strong>Q3.</strong> <span class="pill">5.3 · LO3</span><br>Which one is the best statement, and why do the others fail? (a) “WGS 84” identifies a unique coordinate reference system. (b) A datum fixes where the ellipsoid sits relative to the Earth; a CRS built on it still needs axes, units and (if projected) a projection. (c) Changing the datum of a dataset changes the physical positions of its features. (d) An ellipsoid and a datum are two names for the same thing.</p></div>
  <div class="card"><p><strong>Q4.</strong> <span class="pill">5.4 · LO4</span><br>Under the EPSG records read in this chapter, which statement is correct? (a) EPSG:3857 has degree units. (b) EPSG:32643’s area of use is the whole of India. (c) EPSG:3857’s unit is the metre, but its own record states scale errors relative to a proper Mercator, so the unit does not establish measurement suitability. (d) EPSG:4326’s axes are easting, northing.</p></div>
  <div class="card"><p><strong>Q5.</strong> <span class="pill">5.5 · LO5</span><br>A file is supposed to hold points in Sri Lanka (roughly 6–10°N, 79–82°E). It contains <code>{"type":"Point","coordinates":[6.93, 79.86]}</code>. Per RFC 7946, what place does this position describe (latitude and longitude with hemispheres)? Would a validator that checks only that latitude is within ±90 and longitude within ±180 accept it? Name one additional check that would expose the problem, and explain why the same problem in a file of points from Bali (roughly 8–9°S, 114–116°E) <em>would</em> be caught by the range check alone.</p></div>
  <div class="card"><p><strong>Q6.</strong> <span class="pill">5.6, 5.7 · LO6, LO7</span><br>A layer’s <em>Source</em> tab in ArcGIS Pro says its spatial reference is WGS 1984 (WKID 4326); the map’s <em>Coordinate Systems</em> tab says WGS 1984 Web Mercator (auxiliary sphere). The layer also has a <code>z</code> attribute with values around 55. State (i) whether the layer’s stored coordinates are degrees or metres, (ii) why the map can still draw it aligned with a Web Mercator basemap, and (iii) two questions that must be answered before <code>z = 55</code> can be compared with another dataset’s elevations.</p></div>

  <h2><span class="mod">5.9.2</span>Scenario questions (20 marks, 10 each)</h2>
  <div class="card"><p><strong>S1.</strong> <span class="pill">5.4, 5.5, 5.7 · LO4, LO5, LO7</span><br>A mobile inspection app posts JSON like <code>{"lat": 23.0312, "lng": 72.5934, "alt": 61.2}</code> to your API. Your backend must store the point in PostGIS with <code>ST_Point(…, 4326)</code>, and a reporting job must emit RFC 7946 GeoJSON. Write the argument order for the <code>ST_Point</code> call and the GeoJSON position array, cite the contract you used for each, and state one check you would add so that a future developer cannot swap the values without a test failing. Then explain why you should <em>not</em> write <code>alt</code> into the GeoJSON third element until one specific fact about it is known.</p></div>
  <div class="card"><p><strong>S2.</strong> <span class="pill">5.3, 5.6 · LO3, LO6</span><br>A contractor delivers a drain survey as a CSV with <code>easting, northing</code> in “UTM 43N, WGS 84” and a <code>height</code> column. Your existing drain layer, which the previous contractor delivered “in WGS 84”, is offset from the new one by about 3 m horizontally, and the heights differ by a roughly constant amount for the same manholes. Give two <em>different</em> categories of explanation for the horizontal offset and two for the vertical difference, and for each category name the evidence (a record field, a document, or a check from the 5.7 checklist) that would confirm or rule it out. Do not propose a transformation.</p></div>

  <h2><span class="mod">5.9.3</span>Independent practical task (40 marks) — unfamiliar inputs</h2>
  <div class="card">
    <p><span class="synthetic">Made-up files — deliberately different from the lab.</span> Three files arrive with the note “all from our Delhi pilot” and the expected area “roughly 28.4–28.9°N, 76.8–77.4°E”. No answer from the guided lab applies.</p>
    <div class="grid-2">
      <div class="case"><h4>X1 — <code>pilot_points.geojson</code></h4><pre>{ "type": "Feature",
  "properties": { "id": "X1" },
  "geometry": { "type": "Point",
    "coordinates": [28.6, 77.2] } }</pre></div>
      <div class="case"><h4>X2 — <code>pilot_survey.csv</code></h4><p class="small">Header sheet: “WGS 84 / UTM zone 43N, metres; height from the phone”</p><pre>id,northing,easting,height
X2,3168500.0,715200.0,216</pre></div>
      <div class="case"><h4>X3 — <code>pilot_legacy.csv</code></h4><p class="small">No metadata; found with no companion files</p><pre>id,x,y
X3,1234.5,987.6</pre></div>
    </div>
    <p><strong>Deliver:</strong></p>
    <ol>
      <li>A diagnosis table in the 5.8 format for X1–X3.</li>
      <li>For X1: the latitude/longitude the file asserts per RFC 7946, and whether it is inside the expected area.</li>
      <li>For X2: the two rough plausibility estimates of 5.4.3 with arithmetic; an explicit statement about the <em>column order</em> and why it does not contradict the EPSG axis definition; and the questions that <code>height = 216</code> leaves open.</li>
      <li>For X3: the hypotheses you can exclude by range and the ones you cannot, and the evidence you would request.</li>
      <li>A statement of what you did <strong>not</strong> do (no transformation, no CRS assignment) and why.</li>
    </ol>
    <p class="small">Marks: reasoning 12, correctness 12, verification 10, documentation 6. Verification requires the arithmetic and the extent check to be <em>shown</em>, not asserted.</p>
  </div>

  <h2><span class="mod">5.9.4</span>Oral explanation (10 marks)</h2>
  <div class="card"><p>In no more than three minutes, explain to a colleague who has just said “the file is in WGS 84, so just load it” why that sentence is not enough. Your explanation must mention: the difference between a datum and a CRS; at least two CRSs on WGS 84 with different units; the container’s axis-order contract; and what you would do if the CRS turned out to be unknown.</p></div>

  <h2><span class="mod">5.9.5</span>Scoring and the progression gate</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Component</th><th>Marks</th><th>Criteria</th></tr></thead>
    <tbody>
      <tr><td>Concept Q1–Q6</td><td>30</td><td>Correct answer <em>and</em> correct reason; multiple-choice items score 0 without the reason</td></tr>
      <tr><td>Scenario S1–S2</td><td>20</td><td>Correct contracts cited; evidence named for each explanation; no unrequested transformation proposed</td></tr>
      <tr><td>Practical</td><td>40</td><td>Reasoning 12, correctness 12, verification 10, documentation 6</td></tr>
      <tr><td>Oral</td><td>10</td><td>All four required elements present; no term used without a plain-language meaning</td></tr>
    </tbody></table></div>
  <div class="callout warn"><span class="label">Progression gate</span><p>Advance to Chapter 6 only when: coordinate order, angular versus linear units, and dataset versus display reference are each handled correctly at least once in the practical; X1 is identified as swapped with the RFC cited; and X3 is <em>not</em> assigned a CRS. Any of the following is a <strong>critical misconception</strong> requiring remediation and a fresh exercise regardless of the total score: treating degrees as metres; silently guessing coordinate order or CRS; claiming a Web Mercator metre is a ground metre; treating “WGS 84” as a complete CRS specification.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<?php require __DIR__ . '/../partials/end.php'; ?>
