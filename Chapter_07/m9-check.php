<?php $page = ['title' => '7.9 Independent check and progression gate', 'chapter' => 7, 'module' => '7.9']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 7.9 · Assessment</div>
    <h1>Independent check</h1>
    <p class="lead">Six concept questions, two scenarios, one practical on <em>new</em> data, and one spoken explanation. State your assumptions where a question leaves a choice open — a stated, defensible assumption is never penalised. The concept questions give instant feedback; the rest are marked by your instructor from the chapter’s Instructor Appendix.</p>
    <div class="outcomes"><h4>Pass rule (from the blueprint)</h4>
      <ul><li>Suggested pass: 80 of 100 — <strong>and</strong> no critical misconception.</li>
      <li>Critical misconceptions that block progress on their own: silently guessing a CRS or coordinate order; accepting a tool’s “completed” message as proof that meaning was preserved; not being able to choose a defensible format for a stated recipient; not being able to list what must be checked after an exchange.</li></ul></div>
  </div>

  <h2><span class="mod">7.9.1</span>Concept questions (25 points)</h2>
  <div class="quiz" data-answer="2" data-fb="(a) .shp alone lacks the required .shx and .dbf, so most software will not open it; also no .prj (CRS unknown), no .cpg, no readme. (b) The CSV lacks everything geographic: which columns, their order (70.01/20.02 are in range both ways, so the numbers cannot decide), CRS, units, encoding, types, publisher, date — ask. (c) Nothing structurally missing, but check it really is a GeoPackage (GPKG id, gpkg_contents) and whether gpkg_extensions lists anything; even then accuracy, freshness, fitness and licence come from the form, not the file.">
    <div class="q">Q1. A shared folder holds (a) <code>wards.shp</code> alone; (b) <code>sites.csv</code> with header <code>c1,c2,name</code> and first row <code>70.01,20.02,Depot</code>, no readme; (c) <code>assets.gpkg</code> with a completed intake form. Which line is right?</div>
    <div class="opts">
      <button class="opt">(a) opens fine; (b) is obviously longitude then latitude; (c) needs nothing more.</button>
      <button class="opt">All three are unusable until a service URL is provided.</button>
      <button class="opt">(a) is missing required files and its CRS; (b) is missing everything geographic and the order cannot be decided from the numbers; (c) is complete but still needs the GeoPackage checked and the form read for accuracy, freshness, fitness and licence.</button>
      <button class="opt">(a) needs only a .prj; (b) needs only a header rename; (c) proves the data is accurate.</button>
    </div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="0" data-fb="X is longitude only because the names say so (the Y, X pair is one ArcGIS Online recognises by name) — the values 20 and 70 fit both ranges. 007 can be typed as an integer by ArcGIS Pro’s text workspace, QGIS detection, or ArcGIS Online (“only numerals → integer”); prevent it with schema.ini Col3=ward_no Text, a QGIS type change or .csvt, or setting the type in Map Viewer. The Hindi note needs UTF-8: ArcGIS Online says non-English text “must be encoded as Unicode or UTF-8, not ASCII”.">
    <div class="q">Q2. Header <code>Y,X,ward_no,note</code>; first row <code>20.0000,70.0000,007,ठीक है</code>. Which column is longitude, what damages <code>ward_no</code>, and what must be true for the note to survive?</div>
    <div class="opts">
      <button class="opt">X (second column) — by its name only; 007 → 7 if a type is guessed, so declare it text; the file must be UTF-8.</button>
      <button class="opt">Y (first column) — the first number is always longitude.</button>
      <button class="opt">Cannot be decided; 007 is safe because it is short; the note always survives.</button>
      <button class="opt">X — proven by 70 being larger than 20; 007 is safe in every importer.</button>
    </div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="1" data-fb="RFC 7946 fixes WGS 84 lon/lat degrees and removed the crs member because of interoperability problems; a crs member is at best a foreign member a reader may ignore, and projected numbers must be transformed first. The single exception is a prior arrangement among all parties — which makes the file usable between them, not standard for anyone else.">
    <div class="q">Q3. Why does a <code>crs</code> member naming a projected CRS not make a GeoJSON file compliant, and when does RFC 7946 permit non-WGS 84 coordinates?</div>
    <div class="opts">
      <button class="opt">It does make it compliant, as long as the EPSG code is valid.</button>
      <button class="opt">Because the RFC fixes WGS 84 lon/lat and removed the crs member; only a prior arrangement among all involved parties permits another CRS — and then only between them.</button>
      <button class="opt">Because GeoJSON has no CRS at all, so any coordinates are fine.</button>
      <button class="opt">Because projected coordinates are illegal in JSON.</button>
    </div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="3" data-fb="The three required files are present, so it opens — with an unknown CRS. Adding the publisher’s .prj restores documented metadata; writing one yourself is Define Projection, legitimate only once the CRS is established from evidence, and it changes no geometry. cond_score = 0 cannot be told apart from a substituted null.">
    <div class="q">Q4. A delivery has <code>assets.shp</code>, <code>.shx</code>, <code>.dbf</code>. Will it open, what is the CRS, what is the difference between adding the publisher’s .prj and writing one yourself, and what does <code>cond_score</code> = 0 tell you?</div>
    <div class="opts">
      <button class="opt">It will not open without a .prj; writing one fixes it; 0 means a real zero.</button>
      <button class="opt">It opens with WGS 84 assumed; the .prj is decorative; 0 means null.</button>
      <button class="opt">It opens; CRS is unknown; writing your own .prj changes the geometry; 0 is unreadable.</button>
      <button class="opt">It opens with CRS unknown; adding the publisher’s .prj restores metadata while writing your own is Define Projection (evidence first, no geometry change); 0 cannot be told from a substituted null.</button>
    </div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="0" data-fb="GeoPackage keeps what a shapefile cannot: nulls, long names, date+time, Unicode. A file geodatabase keeps what GeoPackage’s core does not: relationship classes, domains/subtypes, rules, feature datasets. No data format keeps symbology. Extensions are declared in gpkg_extensions; absence or emptiness means a plain GeoPackage; a reader that finds an unknown extension should fail fast (or read safely if scope is write-only).">
    <div class="q">Q5. Name one thing GeoPackage keeps that a shapefile cannot, one thing a file geodatabase keeps that GeoPackage’s core does not, one thing no data format keeps, and GeoPackage’s mechanism for declaring “this file uses more than the core”.</div>
    <div class="opts">
      <button class="opt">Nulls / long names / date+time / Unicode; relationship classes, domains, rules; styling; the gpkg_extensions table (empty = plain GeoPackage; unknown → fail fast).</button>
      <button class="opt">Geometry; geometry; geometry; the .gpkg extension.</button>
      <button class="opt">Styling; nulls; relationships; the srs_id column.</button>
      <button class="opt">Nothing differs between the three; the file size declares extensions.</button>
    </div><div class="fb"></div>
  </div>
  <div class="quiz" data-answer="2" data-fb="(i) false — “If the database contains geodatabase system tables, it is considered a geodatabase in ArcGIS”; PostGIS is only a spatial type. (ii) false — ST_Geometry, PostGIS geometry and PostGIS geography are all supported. (iii) false — hosted layer data is in ArcGIS Data Store’s relational store and “access … is provided exclusively through web layers”.">
    <div class="q">Q6. (i) “It has PostGIS, so it is an enterprise geodatabase.” (ii) “Its enterprise geodatabase must be using PostGIS.” (iii) “Our hosted feature layers’ data is in that enterprise geodatabase because both are ArcGIS.” Which are true?</div>
    <div class="opts">
      <button class="opt">All three.</button>
      <button class="opt">(i) and (iii) only.</button>
      <button class="opt">None — the system tables decide (i); three spatial types are supported (ii); hosted data lives in ArcGIS Data Store, reached only through web layers (iii).</button>
      <button class="opt">(ii) only.</button>
    </div><div class="fb"></div>
  </div>

  <h2><span class="mod">7.9.2</span>Scenario questions (15 points) — write your answers; instructor-marked</h2>
  <div class="scen">
    <h4 style="margin-top:0">S1 (7.3, 7.7)</h4>
    <p>A contractor will only accept shapefiles for a tree-inventory export. The source is a geodatabase feature class with 14 fields including <code>species_scientific_name</code> (Unicode text), <code>last_pruned_at</code> (timestamp offset), <code>canopy_diameter_m</code> (double, nulls allowed) and <code>heritage_status</code> (coded-value domain). Write the “expected adaptations” and “expected losses” sections of the readme you would send — one line per affected field, with the rule behind each. Then name the two before/after checks that would detect the most damaging loss, and say which loss that is.</p>
    <div class="lab-form"><textarea data-save="s1" placeholder="Adaptations: … Losses: … Most damaging: … Checks: …"></textarea></div>
  </div>
  <div class="scen">
    <h4 style="margin-top:0">S2 (7.6)</h4>
    <p>A manager’s request counts per ward differ from yours by exactly two. The manager used a ward layer whose metadata says “2019 delimitation”; yours is the 2024 file. Using the 7.6 fixture: (a) show which requests moved and why; (b) say which intake-form fields would have exposed it and which (“official”) could not; (c) write the one-sentence rule you would add to the team’s intake practice.</p>
    <div class="lab-form"><textarea data-save="s2"></textarea></div>
  </div>

  <h2><span class="mod">7.9.3</span>Practical task on unfamiliar data (25 points)</h2>
  <p>A survey contractor delivers <code>drop_2026-09-17.zip</code> for a <strong>new</strong> district (made-up). Contents:</p>
  <div class="tabs"><button>inspections_sept.txt</button><button>district_boundary.*</button><button>imagery.tif</button><button>the email</button></div>
  <div class="tabpanel"><p class="small">Tab-separated. Hindi text; a 12-hour local time with no offset; <code>asset_ref</code> with a leading zero; coordinates in columns named <code>Y</code> and <code>X</code>, decimal degrees. Invent four more rows following the same patterns (label them synthetic).</p><div class="copywrap"><pre>insp_id	asset_ref	visited	score	remark	Y	X
I-00021	0042	17/09/2026 4:15 PM	4	ठीक है — कोई कार्रवाई नहीं	20.0007	70.0012</pre></div></div>
  <div class="tabpanel"><p>Two files only: <code>district_boundary.shp</code> and <code>district_boundary.dbf</code>. <strong>No <code>.shx</code>, no <code>.prj</code>.</strong></p></div>
  <div class="tabpanel"><p>A TIFF with no sidecar files and no explanation.</p></div>
  <div class="tabpanel"><p>“Everything is in WGS 84; the boundary is the official one.”</p></div>
  <h4>Required work</h4>
  <ol>
    <li>Complete an intake form (7.6) for each of the three items — “unknown” plus the question you would send wherever the delivery is silent.</li>
    <li>For the inspections file: every field that can be damaged on import and the type you would force; the coordinate order you would set and the evidence you would demand before accepting “WGS 84”; how you would handle <code>visited</code> (time-zone assumption, and what a <code>+05:30</code> alternative would change).</li>
    <li>For the boundary: can it open; what each missing file contributes; what you would and would not do about the missing <code>.prj</code> (Chapter 6 terms).</li>
    <li>For the TIFF: the seven inspection items of 7.4.2, where you would look for each, and what “not present” would mean.</li>
    <li>Choose a format to forward the inspections to the internal GIS team (R-C) and to a web developer (R-B); justify each from the 7.4 table; write the before/after checklist you would run (three known records by ID, and the properties to snapshot).</li>
    <li>If software is available, build the five-row file, do the two conversions, and submit the comparison with observed values. If not, submit predictions labelled as such.</li>
  </ol>
  <div class="lab-form"><label>Notes for your practical (saved in this browser)</label><textarea data-save="prac"></textarea></div>

  <h2><span class="mod">7.9.4</span>Oral explanation (10 points)</h2>
  <p>In no more than three minutes, using the shapefile companion files or the GeoPackage bookkeeping tables as your only prop, explain to a new colleague why <strong>file format, source authority, access permission and data quality are four different concerns</strong> — with one town example where each one alone would have misled the team. The examiner asks one follow-up where your explanation was thinnest.</p>

  <h2><span class="mod">7.9.5</span>Scoring</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Component</th><th>Points</th><th>What earns them</th></tr></thead>
    <tbody>
      <tr><td>Concept questions</td><td>25</td><td>Correct distinctions with the deciding rule named; product behaviour tied to the page that establishes it</td></tr>
      <tr><td>Scenario questions</td><td>15</td><td>Predictions tied to rules; fixture arithmetic correct; adaptation and loss kept apart</td></tr>
      <tr><td>Guided lab (7.8)</td><td>25</td><td>Intake form complete; before/after with observed values; every difference classified and justified; original untouched; readmes name every changed field</td></tr>
      <tr><td>Independent practical</td><td>25</td><td>Unknowns declared rather than guessed; coordinate order and CRS evidence demanded; format choices argued from the recipient; checklist would actually detect the losses</td></tr>
      <tr><td>Oral</td><td>10</td><td>Four concerns kept distinct with one example each; a wrong follow-up answer corrected on reflection still passes</td></tr>
    </tbody></table></div>
  <div class="callout warn"><span class="label">Progression rule</span><p>Do not progress a learner who silently guesses a CRS or coordinate order, treats “completed” as proof of preserved meaning, cannot pick a defensible format for a stated recipient, or cannot list what must be checked after an exchange — regardless of the total.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<?php require __DIR__ . '/../partials/end.php'; ?>
