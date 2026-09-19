<?php $page = ['title' => '1.9 Recap and next steps', 'chapter' => 1, 'module' => '1.9']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 1.9</div>
    <h1>Recap, media notes, and the road to Chapter 2</h1>
    <p class="lead">Six ideas to carry forward — and one question that opens the next chapter.</p>
  </div>

  <h2><span class="mod">Recap</span>What Chapter 1 taught</h2>
  <div class="grid-2">
    <div class="card"><div class="synthetic" style="color:var(--accent)">1.1</div><p>A GIS answers exactly the question it is asked. The decision and its policy come from people; the evidence from data. “Near”, “inside”, “unresolved” must be written down before any operation is chosen.</p></div>
    <div class="card"><div class="synthetic" style="color:var(--accent)">1.2</div><p>A GIS is data + software + hardware + people/processes doing capture, storage, analysis, and communication. A map image is an <em>output</em>, a navigation app a <em>product</em>, an address table an <em>input</em>.</p></div>
    <div class="card"><div class="synthetic" style="color:var(--accent)">1.3</div><p>Six shapes of spatial question. Absolute and relative location differ. Straight-line distance is not travel time.</p></div>
    <div class="card"><div class="synthetic" style="color:var(--accent)">1.4</div><p>A record carries geometry, attributes, and time. Events, assets, and boundaries have different update rules. Always ask who, when, why, and what is missing.</p></div>
    <div class="card"><div class="synthetic" style="color:var(--accent)">1.5</div><p>Layers are views of stored records. Hiding is not deleting. Keep an observation log: suggests / confirms / uncertain.</p></div>
    <div class="card"><div class="synthetic" style="color:var(--accent)">1.6</div><p>Many reports in one place needs other explanations before you name a cause. “Good enough” depends on the job. A convincing map can be incomplete, out of date, or misread.</p></div>
  </div>

  <h2><span class="mod">1.9.1–1.9.4</span>Notes for slides, audio, and visuals</h2>
  <p>If you are turning this chapter into a presentation or an audio lesson, the chapter document’s Media Appendix has the full specification. The short version:</p>
  <ul>
    <li><strong>Slides</strong> follow five beats — problem, table/map comparison, question types, workflow, limitations — mapped to modules 1.1, 1.1.3, 1.3, 1.2.3, and 1.6.</li>
    <li><strong>Audio</strong> names every record by ID and coordinates (“P5 at x one thousand, y five hundred”), says “metres” and “straight-line” out loud, and never says “this one here” or “the red dot”.</li>
    <li><strong>Visuals</strong> reveal the same six records step by step: as a table, then dots, then with road and wards layered on — exactly what the “Load the table into a GIS” button in module 1.2 does.</li>
    <li><strong>No 3D scene.</strong> There is no height information in this chapter; a table or 2D diagram teaches every concept better.</li>
    <li><strong>Reading.</strong> The QGIS <em>Gentle Introduction to GIS</em>, chapter “Introducing GIS”, is short and covers the ideas in module 1.2. The problem brief and assessment are original to this course.</li>
  </ul>

  <h2><span class="mod">1.9.5</span>Transition to Chapter 2</h2>
  <p>Take the eight-step workflow from module 1.2 and ask, for each step: <strong>where could this happen?</strong> On an inspector’s phone? On an analyst’s desktop? In a database? On a server? In a browser?</p>
  <div class="try">
    <span class="tag">Think ahead</span>
    <div class="steps" id="whereSteps"></div>
    <div class="result" id="whereOut">Click a step and guess where it might run. Chapter 2 introduces the ArcGIS products — and their open-source counterparts — by exactly this question: roles first, product names second.</div>
  </div>

  <div class="callout idea"><span class="label">You are ready for Chapter 2 when</span><p>You can explain the intended decision, the evidence a GIS supplies, and an important limitation — <em>without</em> a software demonstration. If you have submitted the 1.8 package, tick this module done and continue.</p></div>
  <p><a class="btn primary" href="index.php">Back to the chapter home</a> &nbsp; <a class="btn ghost" href="glossary.php">Glossary</a></p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const steps = [["Decision", "Made by a person — in a meeting, an email, a dashboard."], ["Question", "Written by an analyst or developer; lives in a document or a ticket."], ["Data inventory", "Files on a shared drive, a database, a hosted service, a field app’s sync store…"], ["Selection rules", "A query expression, a filter setting, a form of words in the brief."], ["Operation", "Desktop GIS, a database function, a server-side service, a browser library, a Python script."], ["Validation", "Anywhere a person can look at a count and a record — table view, notebook, spreadsheet."], ["Communication", "A web map, a printed page, a dashboard, an API response inside another application."], ["Decision (again)", "Back to the person."]];
  const el = document.getElementById("whereSteps");
  steps.forEach((s, i) => { const d = document.createElement("div"); d.className = "step"; d.innerHTML = `<span class="n">${i + 1}</span><div><strong>${s[0]}</strong></div>`; d.addEventListener("click", () => { el.querySelectorAll(".step").forEach((x, j) => x.classList.toggle("active", i === j)); document.getElementById("whereOut").innerHTML = `<strong>${s[0]}:</strong> ${s[1]} <span class="small">(Chapter 2 gives these places names.)</span>`; }); el.appendChild(d); });
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
