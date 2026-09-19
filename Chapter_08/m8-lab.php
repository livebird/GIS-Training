<?php $page = ['title' => '8.8 Lab: design an inspection schema', 'chapter' => 8, 'module' => '8.8']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 8.8 · Guided lab · paper or spreadsheet — no GIS software needed</div>
    <h1>Lab: turn one bad table into a good design</h1>
    <p class="lead">You receive a single spreadsheet that a colleague has been using as the “asset register”. It is deliberately bad. Your job: find what is wrong, split it into proper tables, describe every field, define the relationships, fill ten example records, and write three business questions the new design can answer that the old one could not.</p>
    <div class="outcomes"><h4>Objective</h4>
      <ul><li>A defect list (at least ten), an entity–relationship diagram, a data dictionary, relationship definitions, allowed-value lists and defaults.</li>
      <li>Ten example records across at least three tables, with a conversion log explaining every blank and every converted value.</li>
      <li>Three business questions with answers, and a validation record showing repeated visits kept and every key resolving.</li></ul></div>
  </div>

  <div class="callout note"><span class="label">What you need</span><p>A spreadsheet (any) or a text editor, and something to draw a diagram with — paper is fine, or the ASCII style of module 8.5. <strong>Optional:</strong> ArcGIS Pro or QGIS if you want to implement your design afterwards; those routes are in the chapter document’s Instructor Appendix I.6 and are <em>not execution-tested</em>. The lab itself is complete without them.</p></div>

  <h2><span class="mod">8.8.2</span>Input data — the bad table (made-up; reproduce exactly)</h2>
  <p>Create a file <code>Assets_Flat_Ch8.csv</code> with these rows <strong>exactly as printed, including the mistakes</strong> — they are the point. Coordinates are on the Chapter 1 training grid (metres, no real place). Blank cells are blank. The <strong>#</strong> column is only for reading; it is <em>not</em> a field and must not be used as an ID.</p>
  <div class="tabs"><button>Read it as a table</button><button>Copy it as CSV</button></div>
  <div class="tabpanel">
    <div class="try">
      <span class="tag">Try it</span>
      <h3>Step 1 — click any cell you think is a defect</h3>
      <p>Each click flags a cell and shows you whether that cell is part of a known problem and which module explains it. Find as many of the 14 defects as you can. (Flagged cells and your defect count are saved in this browser.)</p>
      <div class="table-wrap" style="max-height:420px;overflow:auto"><table class="flat" id="flat"></table></div>
      <div class="status-line q" id="flatStatus">Click a cell.</div>
      <h4>Defects found: <span id="defCount">0</span> of 14</h4>
      <div class="defects" id="defList"></div>
    </div>
  </div>
  <div class="tabpanel">
    <div class="copywrap"><pre id="csvPre"></pre></div>
    <p class="small">Correspondence to earlier practice data: row 1 is SL-0113 and row 4 is DR-0042 (with the visits from the chapter’s inspection history); row 5 is TR-0301. Rows 2, 3, 6, 7 and 8 are new assets for this lab only.</p>
  </div>

  <h2><span class="mod">8.8.3</span>Steps</h2>
  <p>Tick each step as you finish it. Your ticks and notes are saved in this browser only.</p>
  <div class="checklist lab-card">
    <label><input type="checkbox" data-save="s1"> <span><strong>1. Diagnose (30 min).</strong> List every defect, each tagged with the module that explains it. Aim for at least ten. Do not fix anything yet.</span></label>
    <label><input type="checkbox" data-save="s2"> <span><strong>2. Entities and grains (15 min).</strong> Name the kinds of thing hiding in the columns and write the one-sentence grain of each. You should find at least Asset, Inspection, Team and Photo. Decide whether Ward is a table here or a fact worked out from the map.</span></label>
    <label><input type="checkbox" data-save="s3"> <span><strong>3. Draw the diagram (20 min).</strong> Boxes, lines, how-many at each end, key names on the lines. Module 8.5’s diagram is a reference, not a template — your diagram must reflect <em>your</em> decisions (for example, streetlight-only fields in a 1:1 detail table, or on Asset with a type rule).</span></label>
    <label><input type="checkbox" data-save="s4"> <span><strong>4. Data dictionary (45 min).</strong> Seven parts per field for <em>every</em> field of Asset and Inspection, plus the key fields of every other table. Include the shape specification for Asset (8.2) and the date/time rule (8.3). Mark derived fields.</span></label>
    <label><input type="checkbox" data-save="s5"> <span><strong>5. Relationships (15 min).</strong> For each line: the two tables, parent key, child key, cardinality, whether the child key may be blank, and what should happen when a parent is deleted (restrict / set blank / cascade — with one sentence why). Say where you expect it to be enforced: schema, app or review.</span></label>
    <label><input type="checkbox" data-save="s6"> <span><strong>6. Allowed values and defaults (15 min).</strong> Every category field gets a coded list (code + description); every measurement gets a range or an explicit “no range”. Write the default policy per field (8.6).</span></label>
    <label><input type="checkbox" data-save="s7"> <span><strong>7. Ten example records (30 min).</strong> Convert the bad table into rows of your tables — ten rows across at least three tables (for example 3 assets + 2 teams + 4 inspections + 1 photo). Assign IDs by a rule you write down. Dates → <code>YYYY-MM-DD</code>; times → the stored rule; heights → metres or blank with a reason; teams → team keys; conditions → 1–5. Where a value <em>cannot</em> be converted without asking the source (a unitless 7; a 0 that may mean unknown), record blank and log the reason.</span></label>
    <label><input type="checkbox" data-save="s8"> <span><strong>8. Three business questions (15 min).</strong> Each answerable by hand from your ten records, using at least two tables, and unreliable or impossible against the bad table. Write the question, tables and keys used, and the answer.</span></label>
    <label><input type="checkbox" data-save="s9"> <span><strong>9. Validate (15 min).</strong> Do the checks below by hand and record the results.</span></label>
  </div>

  <h3>Helpers</h3>
  <div class="grid-2">
    <div class="card">
      <h4 style="margin-top:0">Feet to metres</h4>
      <p>1 foot = 0.3048 m exactly (international foot).</p>
      <div class="controls"><label>feet: <input type="number" id="ft" value="22" step="0.1" style="width:90px;font:inherit;padding:.2rem .4rem;border:1px solid var(--rule);border-radius:6px"></label></div>
      <div class="result" id="ftOut"></div>
    </div>
    <div class="card">
      <h4 style="margin-top:0">Date and team normaliser</h4>
      <p>Rows 1 and 4 use <code>DD/MM/YYYY</code>; the rest <code>YYYY-MM-DD</code>. Teams appear as “North”, “North crew” and “T-N” — one crew, three spellings. Conditions appear as words and as “3” — one scale: Very poor 1 · Poor 2 · Fair 3 · Good 4 · Very good 5.</p>
      <div class="controls"><label>date as written: <input type="text" id="dIn" value="14/09/2025" style="width:130px;font:inherit;font-family:var(--font-mono);padding:.2rem .4rem;border:1px solid var(--rule);border-radius:6px"></label></div>
      <div class="result" id="dOut"></div>
    </div>
  </div>

  <h2><span class="mod">8.8.4</span>Expected results and validation checks</h2>
  <p>Because the input is fixed, several results are fixed too. Your design may differ from the instructor’s, but these must hold. Tick what you have verified.</p>
  <div class="checklist lab-card">
    <label><input type="checkbox" data-save="v1"> <span><strong>8 distinct assets</strong> — one per row. The two rows called “Streetlight near Ward A market” are two different poles: their coordinates differ by 55 m in x and 5 m in y.</span></label>
    <label><input type="checkbox" data-save="v2"> <span><strong>11 inspection observations with data</strong> (rows 1:2, 2:1, 3:1, 4:2, 5:0, 6:1, 7:3, 8:1) <strong>plus 1 known visit with no data</strong> (row 7’s fourth visit on 2026-08-01). The 2026-08-01 visit must appear somewhere — as a row with a blank condition and a remark, or in the conversion log.</span></label>
    <label><input type="checkbox" data-save="v3"> <span><strong>6 photo files</strong> (2 + 1 + 2 + 1), after splitting on “;” and “,”.</span></label>
    <label><input type="checkbox" data-save="v4"> <span><strong>2 teams</strong> — North / North crew / T-N are one; South is the other.</span></label>
    <label><input type="checkbox" data-save="v5"> <span><strong>Repeated visits kept</strong> — both SL-0113 visits and all three SL-0250 visits exist as separate rows.</span></label>
    <label><input type="checkbox" data-save="v6"> <span><strong>Every child key resolves</strong> — the orphan check of module 8.7, run by hand, returns zero rows for Inspection→Asset and Photo→Inspection.</span></label>
    <label><input type="checkbox" data-save="v7"> <span><strong>Row 4’s ward</strong> — the table says “A/B”, but (995, 510) is strictly inside Ward A (995 &lt; 1000) by 5 m. Either derive the ward from the map (A) or store an office assignment <em>with a written reason</em>.</span></label>
    <label><input type="checkbox" data-save="v8"> <span><strong>Row 5</strong> at (2190, 520) is outside both wards, as in Chapter 1.</span></label>
    <label><input type="checkbox" data-save="v9"> <span><strong>Heights</strong> — 22 ft = 6.7056 m (round to 6.71 and say so); 12 m = 12; 7, 6.5 and 8 → blank with reason “unit not recorded” (or a documented assumption, flagged for confirmation); 0 on a drain → not applicable.</span></label>
    <label><input type="checkbox" data-save="v10"> <span><strong>Wattage</strong> — row 1: 70; rows 2 and 3: unknown (blank); row 6 is a drain — <em>not applicable</em>, the 0 is wrong; row 7: 0 only if your dictionary defines 0 as “no lamp fitted”, otherwise blank with reason.</span></label>
    <label><input type="checkbox" data-save="v11"> <span><strong>Conditions</strong> — row 8’s “3” is Fair; the words in other rows map to the 1–5 scale. <strong>Dates</strong> — every converted date matches the chapter’s inspection history where the visit exists there (row 1’s 14/09/2025 is INS-0004; row 4’s 15/10/2024 is INS-0001).</span></label>
  </div>
  <p class="small">All counts are exact. The only numeric conversion is 22 ft → 6.7056 m. No software output is involved anywhere in this lab.</p>

  <h2><span class="mod">8.8.5</span>Troubleshooting</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Symptom</th><th>Likely cause</th><th>Fix</th></tr></thead>
    <tbody>
      <tr><td>Your Asset table has 7 rows</td><td>You merged the two same-name streetlights</td><td>8.4.1: a label is not an ID; the coordinates differ</td></tr>
      <tr><td>Your Inspection table has 8 rows</td><td>You kept “latest visit” per asset</td><td>8.1.2 and 8.5.2: one row per visit</td></tr>
      <tr><td>You cannot decide where Wattage goes</td><td>The not-applicable problem</td><td>A 1:1 StreetlightDetail table (8.5) or a type rule in app/review (8.6) — say which</td></tr>
      <tr><td>Insp_By values stored as typed</td><td>Team is an entity</td><td>Team table with a key; inspections carry <code>team_id</code></td></tr>
      <tr><td>Photos still one text field</td><td>Habit</td><td>8.7.2: one row per file</td></tr>
      <tr><td>Every field has a default so nothing is blank</td><td>8.6.2</td><td>Remove defaults from observed fields; document what blank means</td></tr>
      <tr><td>Ward “A/B” kept as text</td><td>You trusted the source over the geometry</td><td>Derive from the map, or store an assignment with a reason — never both silently</td></tr>
      <tr><td>You used # or the spreadsheet row number as asset_id</td><td>Storage number used as a business key</td><td>8.4.1: assign business IDs by a written rule</td></tr>
      <tr><td>14/09/2025 became 2025-14-09, or “9 January”</td><td>Date-format ambiguity</td><td>Convert everything to <code>YYYY-MM-DD</code> and log the source format</td></tr>
    </tbody></table></div>

  <h2><span class="mod">8.8.6</span>What to submit</h2>
  <ol>
    <li><strong>Defect list</strong> — at least ten, each tagged with a module number.</li>
    <li><strong>Entity–relationship diagram</strong> with cardinalities and key names.</li>
    <li><strong>Data dictionary</strong> — all Asset and Inspection fields with all seven parts; keys of every other table; the shape specification; the date/time rule.</li>
    <li><strong>Relationship definitions</strong> — one block per relationship.</li>
    <li><strong>Allowed values and defaults</strong>.</li>
    <li><strong>Ten example records</strong> across at least three tables, plus the <strong>conversion log</strong>.</li>
    <li><strong>Three business questions</strong> with tables, keys and answers.</li>
    <li><strong>Validation record</strong> — the checks above with your results, and the hand-run orphan check.</li>
  </ol>
  <p>The two non-negotiable checks: repeated visits are kept as separate rows, and every child key resolves to a parent.</p>
  <div class="lab-form">
    <label>Your conversion log (saved in this browser)</label><textarea data-save="convlog" placeholder="Row 3 Height '22 ft' → 6.7056 m, rounded 6.71 m. Row 1 Height '7' → blank: unit not recorded. …"></textarea>
    <label>Your three business questions and answers</label><textarea data-save="bq"></textarea>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  /* the flat table with defect flags */
  const D = [
    { n: 1, t: "Inspections stored as repeated column groups Insp1_…Insp3_; a fourth visit (row 7) has nowhere to go", m: "8.1.2, 8.5.2", cells: r => r.col >= 6 && r.col <= 14 && r.row === 6 },
    { n: 2, t: "AssetName used as the identifier — rows 1 and 2 share it", m: "8.4.1", cells: r => r.col === 0 && (r.row === 0 || r.row === 1) },
    { n: 3, t: "Height mixes units: 22 ft, 12 m, unitless 7 / 6.5 / 8, and 0", m: "8.3.1, 8.3.3", cells: r => r.col === 4 && r.val !== "" },
    { n: 4, t: "Wattage blank means both ‘unknown’ (rows 2, 3) and ‘not applicable’ (rows 4, 5, 8); 0 means ‘not applicable’ (row 6) and maybe ‘no lamp’ (row 7)", m: "8.3.2", cells: r => r.col === 5 && r.row !== 0 },
    { n: 5, t: "Mixed date formats: 14/09/2025 and 15/10/2024 vs 2026-03-14", m: "8.3.3", cells: r => (r.col === 6 || r.col === 9 || r.col === 12) && r.val.includes("/") },
    { n: 6, t: "No time of visit, no time zone, no typing time", m: "8.3.3, 8.7.1", cells: r => (r.col === 6 || r.col === 9 || r.col === 12) && r.val !== "" && !r.val.includes("/") },
    { n: 7, t: "Team as free text with three spellings for one crew (North, North crew, T-N)", m: "8.5.1, 8.6.1", cells: r => (r.col === 8 || r.col === 11 || r.col === 14) && r.val !== "" },
    { n: 8, t: "Condition mixes words and a number (Fair … 3) with no stated scale", m: "8.6.1", cells: r => (r.col === 7 || r.col === 10 || r.col === 13) && r.val !== "" },
    { n: 9, t: "Photos in one field with inconsistent separators (; and ,)", m: "8.7.2", cells: r => r.col === 15 && r.val !== "" },
    { n: 10, t: "Ward holds ‘A/B’ and ‘outside’ — and row 4’s A/B contradicts its coordinates (strictly in A)", m: "8.5.2, 8.2", cells: r => r.col === 16 && (r.val === "A/B" || r.val === "outside") },
    { n: 11, t: "Type uses ‘Streetlight’ and ‘Light’ for the same category", m: "8.6.1", cells: r => r.col === 1 && r.val === "Light" },
    { n: 12, t: "Decommissioning hidden inside the name (row 7) instead of a status field with a date", m: "8.3.1, 8.7.1", cells: r => r.col === 0 && r.row === 6 },
    { n: 13, t: "History overflow acknowledged only in free-text Notes (row 7)", m: "8.7.1", cells: r => r.col === 17 && r.row === 6 },
    { n: 14, t: "No business identifier, no location method, no CRS statement anywhere", m: "8.4.1, 8.2", cells: r => r.col === 2 || r.col === 3 }
  ];
  const key = "gis-ch8-labdefects";
  let found = new Set(); try { found = new Set(JSON.parse(localStorage.getItem(key) || "[]")); } catch {}
  const flat = document.getElementById("flat"), st = document.getElementById("flatStatus");
  flat.innerHTML = `<thead><tr><th>#</th>${FLAT.cols.map(c => `<th>${c}</th>`).join("")}</tr></thead><tbody>${FLAT.rows.map((r, i) => `<tr><td class="mono">${i + 1}</td>${r.map((v, j) => `<td data-row="${i}" data-col="${j}">${v === "" ? "" : esc(v)}</td>`).join("")}</tr>`).join("")}</tbody>`;
  function drawDefects() {
    document.getElementById("defCount").textContent = found.size;
    document.getElementById("defList").innerHTML = D.map(d => `<div class="d ${found.has(d.n) ? "found" : ""}"><span class="n">${found.has(d.n) ? "✓" : "?"} ${d.n}</span><span>${found.has(d.n) ? d.t + " <em>(" + d.m + ")</em>" : "not found yet"}</span></div>`).join("");
    try { localStorage.setItem(key, JSON.stringify([...found])); } catch {}
  }
  flat.querySelectorAll("td[data-row]").forEach(td => td.addEventListener("click", () => {
    const r = { row: +td.dataset.row, col: +td.dataset.col, val: FLAT.rows[+td.dataset.row][+td.dataset.col] };
    const hits = D.filter(d => d.cells(r));
    td.classList.toggle("flag");
    if (hits.length) { hits.forEach(h => found.add(h.n)); st.className = "status-line ok"; st.textContent = "Yes — " + hits.map(h => `defect ${h.n}: ${h.t} (${h.m})`).join(" · "); }
    else { st.className = "status-line q"; st.textContent = `“${r.val || "(blank)"}” in ${FLAT.cols[r.col]} is not one of the 14 listed defects — but write down why you clicked it; there may be more than 14.`; }
    drawDefects();
  }));
  drawDefects();
  document.getElementById("csvPre").textContent = FLAT.cols.join(",") + "\n" + FLAT.rows.map(r => r.map(v => /[,;]/.test(v) ? `"${v}"` : v).join(",")).join("\n");

  /* helpers */
  const ft = document.getElementById("ft"), fo = document.getElementById("ftOut");
  const f2m = () => { const v = Number(ft.value); fo.innerHTML = `${v} ft × 0.3048 = <strong>${(v * 0.3048).toFixed(4)} m</strong> → record as ${(v * 0.3048).toFixed(2)} m and state the rounding.`; };
  ft.addEventListener("input", f2m); f2m();
  const di = document.getElementById("dIn"), dO = document.getElementById("dOut");
  const dn = () => { const m = di.value.trim().match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/); if (m) { const dd = +m[1], mm = +m[2]; dO.innerHTML = dd <= 12 ? `Ambiguous! Read as DD/MM/YYYY it is <strong>${m[3]}-${String(mm).padStart(2, "0")}-${String(dd).padStart(2, "0")}</strong>; read as MM/DD/YYYY it would be ${m[3]}-${String(dd).padStart(2, "0")}-${String(mm).padStart(2, "0")}. The register is Indian, so DD/MM — but write that assumption in the log.` : `Only DD/MM/YYYY is possible (day ${dd} > 12) → <strong>${m[3]}-${String(mm).padStart(2, "0")}-${String(dd).padStart(2, "0")}</strong>. Log the source format anyway.`; } else if (/^\d{4}-\d{2}-\d{2}$/.test(di.value.trim())) dO.textContent = "Already YYYY-MM-DD. Keep it."; else dO.textContent = "Type a date like 14/09/2025 or 2026-03-14."; };
  di.addEventListener("input", dn); dn();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
