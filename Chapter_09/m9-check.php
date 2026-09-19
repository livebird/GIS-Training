<?php $page = ['title' => '9.9 Independent check and progression gate', 'chapter' => 9, 'module' => '9.9']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 9.9 · Assessment</div>
    <h1>Independent check</h1>
    <p class="lead">Six concept questions, two scenarios, one practical on <em>new</em> data, and one spoken explanation. The concept questions give instant feedback; the scenarios, practical and oral are marked by your instructor from the chapter’s Instructor Appendix. All data is made up and on the flat metre grid.</p>
    <div class="outcomes"><h4>Pass rule (from the blueprint)</h4>
      <ul><li>Suggested pass: 80 of 100 — <strong>and</strong> no critical misconception.</li>
      <li>Critical misconceptions that block progress on their own: treating a low RMS or zero residual as accuracy away from the control points; giving “phones” or “GPS” a fixed accuracy; mixing up georeferencing, geocoding, CRS assignment and reprojection; running an auto-repair or a topology fix as the correction without inspecting and logging it; deleting or re-keying an orphan, filling a missing value, merging complaints on distance alone, or “fixing” a documented exception; editing the original or saving without a before/after record.</li></ul></div>
  </div>

  <h2><span class="mod">9.9.1</span>Concept questions (25 points)</h2>
  <div class="quiz" data-answer="2" data-fb="Consistency passes (valid, no gaps/overlaps). Values and position cannot be judged from what is given (a 1:50,000 source suggests coarse boundaries, but state no number). Currency fails — two wards merged in 2024 — and that also leaves one boundary too many (completeness). The decisive decision is the council count: complaints get split across a ward that no longer exists.">
    <div class="q">Q1 (9.1). A ward layer digitised in 2019 from a 1:50,000 plan: every shape valid, no gaps, no overlaps, every ward named. Two wards were merged in 2024. Which line is right?</div>
    <div class="opts"><button class="opt">All five questions pass — the layer is clean.</button><button class="opt">Position fails because 1:50,000 is coarse; the rest pass.</button><button class="opt">Consistency passes; position and values cannot be judged; currency fails (and completeness with it); decisive for “which ward has most open complaints”.</button><button class="opt">Only completeness fails.</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="2" data-fb="The method is known, the quality of each point is not. (a) gives a device class a fixed accuracy; (b) treats accuracy as a property of the method; (d) confuses ‘a GNSS’ with a datum and ignores the receiver-to-map transformation.">
    <div class="q">Q2 (9.2). A complaint table has <code>location_method = GNSS</code> on every row, captured with crews’ phones, and nothing else about the GNSS. Which is correct?</div>
    <div class="opts"><button class="opt">The points are accurate to a few metres because phones are.</button><button class="opt">The points are less accurate than geocoded points, which use reference data.</button><button class="opt">The method is known but each point’s quality is not; accuracy, fix type and correction should have been stored per row.</button><button class="opt">Because GPS is a GNSS, the points are on WGS 84 and need no transformation.</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="1" data-fb="0.6 m is the RMS of the four residuals after least squares; it says nothing about the error between the points or whether one bad point was spread over the others. A surveyed point near the centre, withheld from the fit, gives the centre an accuracy statement. Pixel (300, 300) → (500, 500); ground (600, 600) → pixel (350, 250).">
    <div class="q">Q3 (9.3). A scan is georeferenced with four corner points, first-order, RMS 0.6 m. Which line is right about what 0.6 m means, what would let you speak about the scan’s <em>centre</em>, and — with x = 2c − 100, y = 1100 − 2r — where pixel (300, 300) lands and which pixel shows ground (600, 600)?</div>
    <div class="opts"><button class="opt">0.6 m is the accuracy everywhere; nothing more needed; (500, 500) and pixel (350, 250).</button><button class="opt">0.6 m is the fit error at the four points only; a held-out surveyed check point near the centre; (500, 500) and pixel (350, 250).</button><button class="opt">0.6 m is the scan resolution; add more corner points; (600, 600) and pixel (300, 300).</button><button class="opt">0.6 m is the datum shift; run Define Projection; (500, 500) and pixel (250, 350).</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="0" data-fb="Turn snapping off (all agents, or hold Spacebar) and type the surveyed coordinate — the target is a measured position, not an existing feature. Reading the stored coordinate (attribute review) is the proof; the picture alone can miss a 5 m snap.">
    <div class="q">Q4 (9.4). A survey found drain DR-0042 at (997, 512), not (995, 510). Complaint P5 sits at (1000, 500). How do you make the move, and which review would catch an accidental snap onto P5?</div>
    <div class="opts"><button class="opt">Snapping off (or Spacebar held), type (997, 512), then read the stored coordinate back — the attribute review.</button><button class="opt">Edge snapping on, drag towards the survey mark, save.</button><button class="opt">Point snapping on so the move is precise; the visual review is enough.</button><button class="opt">Delete DR-0042 and create a new drain at (997, 512).</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="2" data-fb="(a) invalid — unclosed ring; (b) exception — overlap by contract; (c) rule violation — dangle with evidence it should join; (d) a self-crossing LINE is valid, the rule question stays open; (e) invalid — hole outside the shell; (f) exception — a bridge.">
    <div class="q">Q5 (9.5). Classify: (a) ward ring not closed; (b) two contractor territories overlapping by contract; (c) a road 0.5 m short of the road it should join; (d) a footpath that crosses itself; (e) a hole outside its outer ring; (f) a flyover crossing a road with no junction.</div>
    <div class="opts"><button class="opt">(a) invalid, (b) violation, (c) violation, (d) invalid, (e) invalid, (f) violation</button><button class="opt">(a) violation, (b) exception, (c) invalid, (d) invalid, (e) violation, (f) exception</button><button class="opt">(a) invalid, (b) exception, (c) violation, (d) valid line — rule open, (e) invalid, (f) exception</button><button class="opt">All six invalid</button></div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="3" data-fb="1: GROUP BY stall_id (and licence_no) HAVING COUNT > 1. 2: stall_type NOT IN (…). 3: result IS NULL. 4: area_m2 outside 4–40. 5: LEFT JOIN stall … WHERE stall.stall_id IS NULL. A check-1 hit says two rows share a key — it cannot say whether that is one stall entered twice or two stalls wrongly numbered.">
    <div class="q">Q6 (9.6). For a <code>stall</code> table (stall_id, stall_type ∈ {FOOD, GOODS, SERVICE}, area_m2 4–40, licence_no) and a <code>check</code> table (check_id, stall_id, result): which line gives the five checks correctly, and why can a check-1 hit not tell you to delete a row?</div>
    <div class="opts"><button class="opt">One query — SELECT * FROM stall — finds all five; delete every duplicate.</button><button class="opt">Duplicates by coordinates; categories by LIKE; blanks by = 0; range by AVG; orphans by INNER JOIN.</button><button class="opt">Only checks 1 and 5 are possible without a map.</button><button class="opt">GROUP BY … HAVING COUNT &gt; 1; NOT IN (list); IS NULL; outside 4–40; LEFT JOIN … IS NULL — and a shared key alone cannot say whether it is one stall twice or two stalls wrongly numbered.</button></div><div class="fb"></div>
  </div>

  <h2><span class="mod">9.9.2</span>Scenario questions (20 points) — written; instructor-marked</h2>
  <div class="scen">
    <h4 style="margin-top:0">Scenario 1 (9.2, 9.3, Chapter 6)</h4>
    <p>Four things arrive the same day: (i) a GeoJSON of streetlights; (ii) a CSV of drains with <code>easting</code>, <code>northing</code> columns and no CRS stated; (iii) a scanned 1998 drainage plan as a PNG with no world file; (iv) a spreadsheet of 300 complaint addresses with no coordinates. For each, name the operation that makes it a usable layer — <em>georeferencing</em>, <em>geocoding</em>, <em>CRS assignment</em>, <em>reprojection</em>, or <em>none needed</em> — say what evidence that operation requires, and what history the result must carry. State your assumptions.</p>
    <div class="lab-form"><textarea data-save="s1" placeholder="(i) … (ii) … (iii) … (iv) …"></textarea></div>
  </div>
  <div class="scen">
    <h4 style="margin-top:0">Scenario 2 (9.5, 9.7)</h4>
    <p>A contractor delivers a ward layer in which the validity check reports two self-crossing polygons and the topology check reports six gaps and one overlap. The covering note says: “We ran the repair tool and the fix-gaps tool, so all errors are resolved; the attached copy is clean.” (a) List three questions you would ask before accepting the “clean” copy, each tied to a module. (b) Using the Ward C numbers (0 / 250,000 / 500,000 m²), explain how a repaired polygon can be valid and wrong. (c) Say what the delivery should have contained instead of that sentence.</p>
    <div class="lab-form"><textarea data-save="s2"></textarea></div>
  </div>

  <h2><span class="mod">9.9.3</span>Practical task on unfamiliar data (35 points)</h2>
  <p>The municipality’s <strong>market precinct</strong> — a <em>new</em> made-up fixture on the flat metre grid. Do the whole Chapter 9 workflow (intake → validity → rules → five checks → classify → correct a copy with records → unresolved list) and deliver the same six items as the lab. Do not use any tool you cannot name in the log.</p>
  <div class="callout note"><span class="label">Readme (made-up)</span><p>Zones are exclusive: the register gives Z1 “Market zone” as x 100–500, y 100–400; Z2 “Parking zone” as x 500–800, y 100–400; Z4 “Loading bay” as the 100 × 100 m square with south-west corner (600, 450), with no courtyard. Contractor area K1 covers the whole precinct by contract and is a separate layer. Paths: W1 is the main path; W3 ends at the north gate; W4 joins W1. Stalls are licensed only within the Market zone; licence numbers are unique per stall. Stall areas are in square metres. Checks CK-01 to CK-03 were typed from paper on 2026-09-03. Domains: stall_type {FOOD, GOODS, SERVICE}; area_m2 4–40; status {ACTIVE, DUPLICATE, REMOVED}; result {PASS, FAIL}.</p></div>
  <div class="tabs"><button>zones.csv</button><button>contractor_areas.csv</button><button>paths.csv</button><button>stalls.csv</button><button>checks.csv</button></div>
  <div class="tabpanel"><div class="copywrap"><pre>zone_id,name,wkt
Z1,Market zone,"POLYGON((100 100,500 100,500 400,100 400,100 100))"
Z2,Parking zone,"POLYGON((480 100,800 100,800 400,480 400,480 100))"
Z4,Loading bay,"POLYGON((600 450,700 450,700 550,600 550,600 450),(750 460,780 460,780 490,750 490,750 460))"</pre></div></div>
  <div class="tabpanel"><div class="copywrap"><pre>area_id,name,wkt
K1,Contractor K,"POLYGON((100 100,800 100,800 400,100 400,100 100))"</pre></div></div>
  <div class="tabpanel"><div class="copywrap"><pre>path_id,name,wkt
W1,Main path,"LINESTRING(100 50,800 50)"
W3,Gate access,"LINESTRING(300 50,300 20)"
W4,East link,"LINESTRING(600 60,600 300)"
W5,Zigzag,"LINESTRING(200 200,300 300,200 300,300 200)"</pre></div></div>
  <div class="tabpanel"><div class="copywrap"><pre>stall_id,stall_type,x,y,area_m2,licence_no,status
ST-01,FOOD,150,150,9,L-1001,ACTIVE
ST-02,GOODS,200,150,12,L-1002,ACTIVE
ST-03,GOOD,250,150,12,L-1003,ACTIVE
ST-04,FOOD,300,150,900,L-1004,ACTIVE
ST-05,SERVICE,350,150,6,L-1002,ACTIVE
ST-06,FOOD,620,150,8,L-1006,ACTIVE
ST-07,FOOD,150,150,9,L-1001,ACTIVE</pre></div></div>
  <div class="tabpanel"><div class="copywrap"><pre>check_id,stall_id,checked_on,result
CK-01,ST-01,2026-09-01,PASS
CK-02,ST-09,2026-09-01,FAIL
CK-03,ST-02,2026-09-02,</pre></div></div>
  <p><strong>Constraints:</strong> nothing is deleted; every correction cites the readme or a table; every exception is logged; the unresolved list names the owner’s question for each open item.</p>
  <div class="lab-form"><label>Working notes (saved in this browser)</label><textarea data-save="prac" placeholder="Findings → class → evidence → correction / question"></textarea></div>

  <h2><span class="mod">9.9.4</span>Oral explanation (5 points)</h2>
  <p>In under three minutes, using the scan example from 9.3, explain to a project manager why a georeferenced plan with “RMS error 0.00 m” placed a benchmark 12 m from its surveyed position, what a check point is, why adding the check point to the fit made the report look better and the map worse, and what sentence about accuracy you would put in the plan’s history instead.</p>

  <h2><span class="mod">9.9.5</span>Scoring</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Component</th><th>Weight</th><th>Evidence required</th></tr></thead>
    <tbody>
      <tr><td>Concept questions Q1–Q6</td><td>25%</td><td>Correct answer with stated assumptions; correct arithmetic in Q3</td></tr>
      <tr><td>Scenarios S1–S2</td><td>20%</td><td>Four operations correctly assigned with evidence and history; S2 questions tied to modules; the 0 / 250,000 / 500,000 explanation</td></tr>
      <tr><td>Practical task</td><td>35%</td><td>All defects found and classified; corrections cite evidence; no valid feature altered; nothing deleted or invented; before/after records complete; unresolved list with owner questions</td></tr>
      <tr><td>Guided lab (9.8)</td><td>15%</td><td>Expected results met; log complete including exceptions</td></tr>
      <tr><td>Oral</td><td>5%</td><td>Residual vs check point distinguished; the “spread” effect explained; an honest history sentence</td></tr>
    </tbody></table></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<?php require __DIR__ . '/../partials/end.php'; ?>
