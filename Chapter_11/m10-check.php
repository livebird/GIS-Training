<?php $page = ['title' => '11.10 Independent check and progression gate', 'chapter' => 11, 'module' => '11.10']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 11.10 · Assessment</div>
    <h1>Independent check</h1>
    <p class="lead">Six concept questions, two scenarios, one practical on <em>new</em> data, and one spoken explanation. State every assumption you make: an answer that is correct under a stated assumption scores; one that quietly picks an assumption does not. The concept questions give instant feedback; the scenarios, practical and oral are marked by your instructor from the chapter’s Instructor Appendix.</p>
    <div class="outcomes"><h4>Pass rule</h4>
      <ul><li>Suggested pass: 80 of 100 — <strong>and</strong> no critical misconception.</li>
      <li>Critical misconceptions that block progress on their own: applying a metre threshold to degree coordinates; presenting proximity as travel time or a mask as risk; summing attributes copied onto split records; dropping unmatched or zero-count records from a report; reporting a per-ward sum without reconciling it to the number of distinct complaints.</li></ul></div>
  </div>

  <h2><span class="mod">11.10.1</span>Concept questions (24 points, 4 each)</h2>
  <div class="quiz" data-answer="1" data-fb="The output is a selection of streetlights — existing records. Minimum: a nearest-distance number (Near) plus the condition NEAR_DIST > 50, or Select By Location “within a distance 50 m” and invert. Buffering every road and dissolving makes a polygon nobody uses.">
    <div class="q">Q1 (11.1). “Which streetlights are more than 50 m from any road?” What kind of answer is it, and what is the minimum set of operations?</div>
    <div class="opts"><button class="opt">A new shape — buffer all roads by 50 m, dissolve, then erase.</button><button class="opt">A selection — a nearest-distance number and the condition &gt; 50 (or a within-distance selection, inverted); no new shape needed.</button><button class="opt">A summary — count streetlights per road buffer.</button><button class="opt">Extra columns — spatial join the road name onto each streetlight.</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="2" data-fb="ArcGIS Pro: Planar method with a linear unit on geographic input → a geodesic buffer, a real 250 m area. PostGIS ST_Buffer on a geometry uses the SRS units → 250 degrees, meaningless. To make both the same: project to a metre-based CRS first (EPSG:32643 in the fixture), or cast to geography in PostGIS.">
    <div class="q">Q2 (11.2). Roads in EPSG:4326 are buffered in ArcGIS Pro with “250 Meters”, Method = Planar, and in PostGIS with <code>ST_Buffer(geom, 250)</code> on a <code>geometry</code> column. Which result is a 250 m proximity area?</div>
    <div class="opts"><button class="opt">Both.</button><button class="opt">Only PostGIS — it always works in metres.</button><button class="opt">Only ArcGIS Pro (it makes a geodesic buffer for a linear unit on geographic input); PostGIS made a 250-degree polygon. Project to a metre CRS first to make them agree.</button><button class="opt">Neither — buffers cannot be made from lat/long data.</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="0" data-fb="Clip copies attributes unchanged, so area_ha still shows the whole park’s area — wrong for the cut piece. Deliver either a recomputed area (on a copy — Calculate Geometry Attributes modifies its input) or the copied field renamed area_ha_orig beside a fresh field.">
    <div class="q">Q3 (11.3). A parks layer has <code>area_ha</code> filled in last year. You clip it to Ward A; one park is cut in half. What does the output record show, and how may you deliver it?</div>
    <div class="opts"><button class="opt">The old whole-park value — wrong for the piece; recompute it from the new shape, or rename it area_ha_orig and add a recomputed field.</button><button class="opt">Half the old value — the tool scales it automatically.</button><button class="opt">Null — clip clears measurement fields.</button><button class="opt">The old value, and it is fine to deliver as is.</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="3" data-fb="Line (1) is lower than polygon (2), so the default output is a line. A 900 m pipe crossing three zones gives three records, each carrying the original length_m = 900, which now describes none of them. Recompute length per piece and sum per zone (the three recomputed lengths add to 900).">
    <div class="q">Q4 (11.4). Intersect a <em>line</em> layer of pipes with a <em>polygon</em> layer of pressure zones. A 900 m pipe crosses three zones.</div>
    <div class="opts"><button class="opt">Polygon output; one record; length_m = 900 is correct.</button><button class="opt">Line output; one record per pipe; length_m = 900.</button><button class="opt">Line output; three records; length_m automatically 300 each.</button><button class="opt">Line output (lowest dimension); three records; each still carries length_m = 900, which describes none of them — recompute per piece.</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="1" data-fb="QGIS: “The values in the output layer’s fields are the ones of the first input feature that happens to be processed” — 1,200 is Ward A’s value. Z1 = A + C = 1,600. Get it with a statistics-capable step (QGIS: Join attributes by location (summary) with sum, or an aggregate expression; ArcGIS Pro: households in Statistics Fields with SUM → SUM_households).">
    <div class="q">Q5 (11.5). After dissolving the wards by zone in QGIS a colleague reads <code>households = 1200</code> on Z1 and reports “Zone Z1 has 1,200 households.” What went wrong?</div>
    <div class="opts"><button class="opt">Nothing — QGIS sums the field during dissolve.</button><button class="opt">The field holds the first processed feature’s value (Ward A); Z1 actually has 1,600 — use a SUM statistic instead.</button><button class="opt">The value should be the mean, 800.</button><button class="opt">The dissolve should have been by ward, not zone.</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="2" data-fb="Identity: 7 = distinct matched + extra matches. Consistent stories: all 6 matched with one on the A/B line (6 + 1); 5 matched, two on the line, one outside (5 + 2); duplicate source records at one spot. Only a per-complaint listing — the reverse one-to-many join — shows which IDs appear twice and which are missing.">
    <div class="q">Q6 (11.6). Wards (target) ← complaints (join), one to one, Keep All <em>unticked</em>, Contains: A 4, B 3 for a complaint layer of 6 records. What can you conclude, and what decides it?</div>
    <div class="opts"><button class="opt">There must be exactly 7 complaints; the layer count is wrong.</button><button class="opt">One complaint is on the boundary — that is the only possibility.</button><button class="opt">Several stories fit (one boundary point; or two boundary points and one outside; or duplicate records). Only the reverse one-to-many join — a per-complaint listing — decides.</button><button class="opt">Keep All being unticked caused the extra count.</button></div><div class="fb"></div>
  </div>

  <h2><span class="mod">11.10.2</span>Scenario questions (16 points, 8 each) — instructor-marked</h2>
  <div class="scen">
    <h4 style="margin-top:0">Scenario 1 (11.1, 11.2, 11.8)</h4>
    <p>The road-crew supervisor reads your Part 1 report and says: <em>“So P1, P3, P5 and P6 are the ones we can reach quickly from Main Road — I’ll tell the council these four are within a five-minute response.”</em> Write the four-sentence reply you would send. It must (i) say what the 300 m result <em>does</em> establish, (ii) say precisely why “reach quickly” and “five-minute response” are not supported, (iii) point out the one complaint whose inclusion depends on ≤ versus &lt; at exactly 300 m, and (iv) name the input that would be needed to answer the supervisor’s real question.</p>
    <div class="lab-form"><textarea data-save="s1" placeholder="Sentence 1 … Sentence 2 … Sentence 3 … Sentence 4 …"></textarea></div>
  </div>
  <div class="scen">
    <h4 style="margin-top:0">Scenario 2 (11.7, 11.8)</h4>
    <p>A colleague loads <code>elevation_training.asc</code>, builds the mask <code>value &lt;= 12</code>, publishes it as “Flood-prone cells (6 ha)”, and separately reports the grid’s mean height as 12.75 m. Identify the two errors, give the correct figure for the second with its cell count, explain what the NoData cell should show in the mask and why, and write the one-line title the mask layer should have had.</p>
    <div class="lab-form"><textarea data-save="s2"></textarea></div>
  </div>

  <h2><span class="mod">11.10.3</span>Practical task on unfamiliar data (50 points) — “Canal District”</h2>
  <p>A <strong>new made-up fixture</strong> on the paper grid (metres, no CRS). Do not reuse the Ward A/B numbers. Create the files by copying.</p>
  <div class="tabs"><button>sectors_c11.csv</button><button>canal_c11.csv</button><button>drain_requests_c11.csv</button></div>
  <div class="tabpanel"><div class="copywrap"><pre>sector,wkt
S1,"POLYGON ((0 0, 1200 0, 1200 800, 0 800, 0 0))"
S2,"POLYGON ((1200 0, 2400 0, 2400 800, 1200 800, 1200 0))"</pre></div></div>
  <div class="tabpanel"><div class="copywrap"><pre>canal_id,wkt
K1,"LINESTRING (600 0, 600 800)"</pre></div></div>
  <div class="tabpanel"><div class="copywrap"><pre>request_id,status,reported_on,x,y
D1,OPEN,2026-09-03,500,100
D2,OPEN,2026-09-04,760,300
D3,CLOSED,2026-09-01,600,700
D4,OPEN,2026-09-06,1300,400
D5,OPEN,2026-09-02,450,800
D6,OPEN,2026-09-05,700,850
D7,OPEN,2026-08-25,1200,450</pre></div></div>
  <figure class="map-fig" id="canalFig"></figure>
  <div class="callout note"><span class="label">The drainage engineer asks two things</span><p>(1) <em>For each sector, how many drainage requests that are OPEN and reported on or after 2026-09-01 lie within 150 m (≤) of canal K1? Report on-the-line and outside cases under the Chapter 10 policy.</em><br>(2) <em>What area of each sector lies within 150 m of K1, and what fraction of the sector is that?</em></p></div>
  <ol class="steps-list">
    <li><span class="badge-step">1</span>Write the seven-item specification and a worksheet for <em>both</em> questions. Name each output kind. Choose the subset of {buffer, clip, intersect, dissolve, spatial join, Near / select-by-location} you need for each and <strong>justify the sequence</strong>; name at least one operation you considered and rejected, with the reason.</li>
    <li><span class="badge-step">2</span>Predict on paper every intermediate result — eligible IDs, distances, near-canal IDs, sector membership, counts, areas — before running anything.</li>
    <li><span class="badge-step">3</span>Run it (either route) and fill a prediction/result table.</li>
    <li><span class="badge-step">4</span>Run one sensitivity change of your choosing and report its effect.</li>
    <li><span class="badge-step">5</span><strong>The misleading alternative.</strong> A colleague proposes to present the 150 m corridor as the “canal flood-risk zone” and to rank the sectors by raw request count as a “risk ranking”. In no more than 120 words, explain why neither is supported by the inputs and what each output <em>can</em> honestly be called.</li>
    <li><span class="badge-step">6</span>Deliver: outputs, worksheet, prediction/result table, log, sensitivity note, and the 120-word explanation.</li>
  </ol>
  <div class="lab-form"><label>Your notes for the practical (saved in this browser)</label><textarea data-save="prac" placeholder="Specification … worksheet … predictions … sensitivity … misleading alternative …"></textarea></div>

  <h2><span class="mod">11.10.4</span>Oral explanation (10 points)</h2>
  <p>Bring your Part 1 outputs. The instructor changes <strong>one</strong> setting — threshold, end type, match option or time window — without telling you which, and shows you the new per-ward counts. In under three minutes, explain which setting changed and how you know, <em>using the coordinates</em>, not the screen. Then explain why the per-ward sum in the raw join can exceed the number of complaints.</p>

  <h2><span class="mod">11.10.5</span>Scoring</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Component</th><th>Points</th><th>Criteria</th></tr></thead>
    <tbody>
      <tr><td>Concept questions Q1–Q6</td><td>24</td><td>Correct under stated assumptions; cites documented behaviour where asked; separates general principle from product behaviour</td></tr>
      <tr><td>Scenarios S1–S2</td><td>16</td><td>Says what the result establishes and what it does not; corrects the figure with its count; names the missing input</td></tr>
      <tr><td>Practical — reasoning and sequence</td><td>14</td><td>Specification complete; output kinds right; sequence justified; a rejected operation named with reason</td></tr>
      <tr><td>Practical — correctness and edge cases</td><td>16</td><td>Eligible set, near-canal set, on-the-line and outside cases, counts, areas and fractions correct; identity holds</td></tr>
      <tr><td>Practical — validation and documentation</td><td>12</td><td>Prediction/result table; invariants; one sensitivity change; a log sufficient to rerun</td></tr>
      <tr><td>Practical — the misleading alternative</td><td>8</td><td>Both claims refused with the right reasons; honest names given</td></tr>
      <tr><td>Oral</td><td>10</td><td>Setting identified from the coordinates; sum-versus-distinct explained</td></tr>
    </tbody></table></div>
  <p><strong>Progression.</strong> Another trainee must be able to reproduce your Part 1 counts from your log and inputs <em>without</em> your outputs. If their result differs, you progress when you can explain the difference by setting, rule, tolerance or fixture — not when the screenshots match.</p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const el = document.getElementById("canalFig");
  const m = gridMap(el, { extent: { x1: -150, y1: -250, x2: 2550, y2: 1050 }, caption: "Canal District (made-up): sectors S1 and S2, canal K1 from (600, 0) to (600, 800), drain requests D1–D7. Metres, no CRS. Predict before you run." });
  drawWards(m, [{ id: "S1", x1: 0, y1: 0, x2: 1200, y2: 800 }, { id: "S2", x1: 1200, y1: 0, x2: 2400, y2: 800 }].map(s => Object.assign(s, { id: s.id })));
  m.svg.querySelectorAll(".ward-label").forEach(t => t.textContent = t.textContent.replace("Ward ", "Sector "));
  const canal = { id: "K1", pts: [[600, 0], [600, 800]] }; drawRoad(m, canal);
  drawRequests(m, [["D1", 500, 100], ["D2", 760, 300], ["D3", 600, 700], ["D4", 1300, 400], ["D5", 450, 800], ["D6", 700, 850], ["D7", 1200, 450]].map(([id, x, y]) => ({ id, x, y })), () => "");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
