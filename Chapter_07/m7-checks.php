<?php $page = ['title' => '7.7 Design conversion checks', 'chapter' => 7, 'module' => '7.7']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 7.7 · General method, any platform</div>
    <h1>Snapshot, convert, compare — then sort every difference</h1>
    <p class="lead">A conversion check is a small experiment. Record the source’s properties, convert, record the output’s properties, and compare them line by line. Every difference goes into one of two columns: <em>“I decided this and can justify it”</em> or <em>“this was lost and nobody noticed”</em>.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Take a “before” snapshot that captures the things that can change — not just counts.</li>
      <li>Compare before and after, and classify each difference as <strong>intentional adaptation</strong> or <strong>accidental loss</strong>.</li>
      <li>Apply three working rules: keep the original, name outputs for their content, and distrust the green “completed” message.</li></ul></div>
  </div>

  <h2><span class="mod">7.7.1</span>Before: snapshot what can change</h2>
  <p>Take the snapshot from the source <em>as your tool reads it</em>, not from the readme — the importer’s interpretation (7.2) is already the first conversion. Six rows in, six rows out tells you nothing about whether TR-0301’s null became zero, so the snapshot has to include nulls, samples and three <strong>known records</strong>: an ordinary one, the null one, and the one whose timestamp crosses midnight in UTC.</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Property</th><th>How to read it</th><th>Value for Table F7</th></tr></thead>
    <tbody>
      <tr><td>Schema</td><td>Field list with declared / detected type and length</td><td>10 fields; record the <em>detected</em> type of <code>legacy_code</code> and <code>last_inspection_at</code></td></tr>
      <tr><td>Record count</td><td>Row count</td><td class="mono">6</td></tr>
      <tr><td>Geometry type(s)</td><td>From the importer, or “none — coordinates in columns”</td><td>Point (after XY import); one type</td></tr>
      <tr><td>CRS</td><td>Data source properties</td><td>None — local grid (documented); the importer may show “Unknown” or the local reference you assign</td></tr>
      <tr><td>Extent</td><td>Min / max of x and y</td><td class="mono">x 205–2190; y 195–950</td></tr>
      <tr><td>Null counts per field</td><td>Count nulls / empties per field</td><td><code>condition_score</code> 1; <code>last_inspection_at</code> 1; <code>inspector_note</code> 1 empty</td></tr>
      <tr><td>Free-text samples</td><td>Copy two values verbatim, including punctuation</td><td><code>Lamp flickers at dusk</code> (SL-0113); <code>Silt build-up — needs clearing</code> (DR-0042)</td></tr>
      <tr><td>Identifier sample</td><td>Copy the leading-zero code verbatim</td><td class="mono">0113 (SL-0113), 0007 (BN-0007)</td></tr>
      <tr><td>Date/time samples</td><td>Copy verbatim with offset</td><td class="mono">2026-08-30T02:10:00+05:30 (SL-0055)</td></tr>
      <tr><td>One statistic by hand</td><td>Compute it yourself</td><td class="mono">mean condition_score (non-null) = 3.2</td></tr>
      <tr><td>Known records</td><td>Three rows to re-find by business key</td><td class="mono">SL-0113 · TR-0301 · SL-0055</td></tr>
    </tbody></table></div>

  <h2><span class="mod">7.7.2</span>After: compare and classify</h2>
  <p>Pick an output format. The “after” column is worked out from the published rules (7.3–7.5). For each difference, <strong>you</strong> decide: adaptation or loss? Then check your answer. The same physical change can be either — “time dropped from the timestamp” is an <em>adaptation</em> if the recipient only needs dates and the readme says so, and a <em>loss</em> if nobody noticed. The difference is documentation and consent.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Three known records through a conversion</h3>
    <div class="controls">
      <label>Output <select id="fmt"><option value="gpkg">GeoPackage</option><option value="shp" selected>Shapefile</option><option value="geojson">GeoJSON-syntax file</option><option value="csv">CSV again</option><option value="gdb">File geodatabase</option></select></label>
      <label><input type="checkbox" id="typed" checked> timestamps typed as date/time before export</label>
      <label><input type="checkbox" id="documented"> the readme documents the changes</label>
    </div>
    <div class="table-wrap"><table class="diff" id="cmpTable"></table></div>
    <div class="result" id="cmpScore"></div>
  </div>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">Intentional adaptation</h4><p>A change you decided on because the target format or the recipient requires it, which you can describe, justify and (often) reverse. Examples: GeoPackage stores the timestamp as UTC with <code>Z</code>; shapefile names shortened with a written old → new mapping; a text <code>legacy_code</code> deliberately kept as text.</p></div>
    <div class="card"><h4 style="margin-top:0">Accidental loss</h4><p>A change you did not decide on and cannot justify to the recipient. Examples: null score became 0; <code>0113</code> became 113 because a type was guessed; TR-0301’s empty note became a single space; SL-0055’s date moved a day with nobody noting the UTC conversion; a record dropped because its geometry was empty.</p></div>
  </div>

  <h2><span class="mod">7.7.3</span>Three working rules</h2>
  <ol>
    <li><strong>Never convert in place.</strong> The original is the only evidence of what the data looked like before your tool touched it. Keep it read-only, beside its intake form.</li>
    <li><strong>Name outputs for content and purpose, not for the step.</strong> <code>assets_ch7_shp_for_contractorX_2026-09-19</code> says what it is, who it was for and when; <code>export1</code> and <code>final_final</code> do not. Put the format’s limits (the field-name mapping, for example) in the readme that travels with the output.</li>
    <li><strong>A “completed” message is not a semantic validation.</strong> The tool verified that it could write a structurally valid file. It did not verify that <code>0</code> still means “not assessed”, that <code>legacy_cod</code> is the field a downstream join expects, or that an empty note now reads the same as a real space. Only the before/after comparison verifies meaning, and only a person can say which column a change belongs in.</li>
  </ol>
  <div class="callout dev"><span class="label">Developer view</span><p>This is a data-migration test: snapshot, migrate, diff, classify diffs. Good analogy. <strong>Where it breaks:</strong> in most application migrations a lost null raises an error or a failed constraint. A shapefile writer replaces it with a legal-looking value and reports success. GIS conversions must be tested for what they <em>changed</em>, not only for what they <em>rejected</em>.</p></div>

  <div class="quiz" data-answer="1" data-fb="Count and field-count lines detect none of these. Nulls substituted → the null-count line and the TR-0301 known record; names shortened → the schema line; time/date changed → the SL-0055 sample; an empty note turned into a space → the free-text sample; zeros lost → the identifier sample.">
    <div class="q">After exporting F7 to a shapefile, your comparison shows six records, ten fields, and no error. Which snapshot lines could <em>still</em> reveal a change?</div>
    <div class="opts">
      <button class="opt">None — six in, six out, ten fields, no error means nothing changed.</button>
      <button class="opt">Null counts, the schema (names), the date/time sample, the free-text sample, and the identifier sample.</button>
      <button class="opt">Only the extent.</button>
      <button class="opt">Only the file size.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const t = document.getElementById("cmpTable"), sc = document.getElementById("cmpScore");
  const KNOWN = [0, 2, 5];
  let answered = {};
  function run() {
    answered = {};
    const fmt = document.getElementById("fmt").value, typed = document.getElementById("typed").checked, doc = document.getElementById("documented").checked;
    let html = `<thead><tr><th>record · field</th><th>before</th><th>after</th><th>rule says</th><th>your call</th></tr></thead><tbody>`;
    const rows = [];
    KNOWN.forEach(i => { const r = convertRow(F7.rows[i], fmt, { tsTyped: typed, utcFirst: fmt === "shp" && typed }); r.fields.forEach(f => { if (f.status !== "kept" || f.outName !== f.name) rows.push({ id: F7.rows[i].asset_id, f }); }); });
    if (!rows.length) html += `<tr><td colspan="5">No differences predicted for these settings.</td></tr>`;
    rows.forEach((x, k) => {
      const f = x.f;
      // the correct classification: adapted → adaptation; lost → loss; depends → adaptation only if documented, else loss
      const correct = f.status === "adapted" ? (doc ? "adapt" : "loss") : f.status === "lost" ? "loss" : (doc ? "adapt" : "loss");
      html += `<tr data-k="${k}" data-correct="${correct}"><td class="mono">${x.id} · ${esc(f.name)}${f.outName !== f.name ? ` → <em>${esc(f.outName)}</em>` : ""}</td><td class="mono">${esc(f.before)}</td><td class="mono ${f.status === "lost" ? "bad" : "chg"}">${esc(f.after)}</td><td><span class="st ${f.status}">${STATUS_LABEL[f.status]}</span><div class="why">${esc(f.why)}</div></td><td><div class="classify"><button data-c="adapt">adaptation</button><button data-c="loss">loss</button></div></td></tr>`;
    });
    t.innerHTML = html + `</tbody>`;
    t.querySelectorAll(".classify button").forEach(b => b.addEventListener("click", () => {
      const tr = b.closest("tr"), ok = b.dataset.c === tr.dataset.correct;
      tr.querySelectorAll("button").forEach(x => x.classList.remove("right", "wrong")); b.classList.add(ok ? "right" : "wrong"); if (!ok) tr.querySelector(`button[data-c="${tr.dataset.correct}"]`).classList.add("right");
      answered[tr.dataset.k] = ok; score(rows.length);
    }));
    score(rows.length);
  }
  function score(n) {
    const done = Object.keys(answered).length, ok = Object.values(answered).filter(Boolean).length;
    const doc = document.getElementById("documented").checked;
    sc.innerHTML = `${n} difference(s) predicted. You have classified ${done}, ${ok} correctly. ` + (doc ? "Because the readme documents changes, format-required changes and reader-dependent values count as <em>adaptations</em>; substitutions the format forces without telling anyone (null → 0, garbled text, lost zeros) are still <em>losses</em> — fix or declare them." : "<strong>Nothing is documented</strong>, so even a format-required change (UTC, shortened names) counts as a loss: the recipient was not told. Tick the readme box and see what changes.");
  }
  ["fmt", "typed", "documented"].forEach(id => document.getElementById(id).addEventListener("change", run)); run();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
