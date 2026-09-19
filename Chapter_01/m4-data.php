<?php $page = ['title' => '1.4 Data and its meaning', 'chapter' => 1, 'module' => '1.4']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 1.4</div>
    <h1>Data and its meaning</h1>
    <p class="lead">One record holds three kinds of information. And not every record is the same kind of thing.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Pull one request apart into geometry (where), attributes (what), and time (when).</li>
      <li>Tell an observed event from an asset and from a boundary — and know which to update.</li>
      <li>Ask who collected the data, when, why, and what is missing.</li></ul></div>
  </div>

  <h2><span class="mod">1.4.1</span>One request, three kinds of information</h2>
  <p>Pick a request. Its record splits into <em>where</em> (the geometry), <em>what</em> (the attributes — the describing columns), and <em>when</em> (the time fields).</p>
  <div class="try">
    <span class="tag">Try it</span>
    <div class="controls" id="reqBtns"></div>
    <div class="record" id="recordCard"></div>
    <div class="result" id="recordNote"></div>
  </div>
  <p>Three informal observations (Chapter 3 gives the formal structure):</p>
  <ul>
    <li><strong>Geometry</strong> here is a single position, so each request is drawn as a <em>point</em>. Roads and wards need more than one coordinate — that is where lines and polygons come in. For now “geometry” means “the part of the record that is space”.</li>
    <li><strong>Attributes</strong> are ordinary typed columns — the “what” of a record. Their <em>meanings</em> are not obvious: “Reopened” only means something because the helpline process defines it. When you take over someone else’s data, first find the definition of every code.</li>
    <li><strong>Time</strong> is more than one field. P5 has a reported time, a closed time (now empty), and, buried in a free-text note, two more events. Free text is very hard to search and filter; a GIS cannot reliably find every request that was “reopened after a second call”.</li>
  </ul>
  <div class="callout dev"><span class="label">Developer view</span><p>A request is like an event object with a typed <code>location</code> property. Good analogy — except the location property has <em>its own</em> validity rules (is it on the grid? right units? does “on the boundary” count?) that a normal string or number property does not.</p></div>

  <h2><span class="mod">1.4.2</span>Event, asset, boundary</h2>
  <p>Our practice data holds three very different kinds of record. Treating them alike causes real damage. Ask of every record: <em>is this a thing, a happening, or a rule?</em></p>
  <div class="grid-3">
    <div class="card" style="border-top:5px solid var(--accent)"><h4 style="margin-top:0">Observed event</h4><p><strong>Request P5.</strong> Something that <em>happened</em> at a time and place, as the citizen reported it.</p><p>The report itself should not change. Only its handling status changes.</p><p class="small"><strong>Updating means:</strong> add a new status entry; never overwrite the original report.</p></div>
    <div class="card" style="border-top:5px solid var(--ink)"><h4 style="margin-top:0">Asset</h4><p><strong>Drain DR-0042.</strong> A thing that <em>exists</em> and has a present condition.</p><p>Yes, it changes — condition, last inspection, replacement.</p><p class="small"><strong>Updating means:</strong> change the fields that describe it now; keep the history of visits separately.</p></div>
    <div class="card" style="border-top:5px solid var(--ward-line)"><h4 style="margin-top:0">Administrative boundary</h4><p><strong>Ward A.</strong> An area drawn by an authority (like the Election Commission or the Corporation) — a rule about space.</p><p>Rarely changes, and only when that authority changes it.</p><p class="small"><strong>Updating means:</strong> replace with the new version; keep the old one with the dates it was valid.</p></div>
  </div>

  <div class="try sorter" data-items='[{"t":"Request P1 (streetlight out)","bin":"event"},{"t":"Streetlight SL-0113","bin":"asset"},{"t":"Ward B","bin":"boundary"},{"t":"The team’s visit to P5 on 13 September 2026","bin":"event"},{"t":"Tree TR-0301","bin":"asset"},{"t":"The service area polygon","bin":"boundary"}]'>
    <span class="tag">Try it</span>
    <h3>Thing, happening, or rule?</h3>
    <div class="items"></div>
    <div class="bins">
      <div class="bin" data-bin="event"><h5>Observed event</h5></div>
      <div class="bin" data-bin="asset"><h5>Asset</h5></div>
      <div class="bin" data-bin="boundary"><h5>Boundary</h5></div>
    </div>
  </div>

  <p><strong>The report and the asset are not at the same point.</strong> A citizen tapping a phone is not a surveyor with measuring instruments. Look how far each request is from the asset it is probably about:</p>
  <figure class="map-fig" id="offsetMap"></figure>
  <div class="table-wrap"><table>
    <thead><tr><th>Request</th><th>Asset</th><th>Gap between them (worked out by hand)</th></tr></thead>
    <tbody>
      <tr><td class="mono">P1 (200, 200)</td><td class="mono">SL-0113 (205, 195)</td><td class="mono">√(5² + 5²) ≈ 7.1 m</td></tr>
      <tr><td class="mono">P5 (1000, 500)</td><td class="mono">DR-0042 (995, 510)</td><td class="mono">√(5² + 10²) ≈ 11.2 m</td></tr>
      <tr><td class="mono">P6 (2200, 500)</td><td class="mono">TR-0301 (2190, 520)</td><td class="mono">√(10² + 20²) ≈ 22.4 m</td></tr>
    </tbody></table></div>
  <div class="callout warn"><span class="label">Misconception</span><p>“When the team fixes the drain, we move the request’s coordinates onto the drain, set status to Resolved, and we’re done.” Overwriting the report destroys the evidence that report and drain were 11 m apart, that the request was reopened, and when. Six months later nobody can answer “how often do citizens mark the wrong spot?”</p></div>

  <h2><span class="mod">1.4.3</span>Who, when, why, and what is missing</h2>
  <p>Chapter 7 will teach formal “data about data” (metadata). Before that, four plain questions must be asked of any dataset. Click each to see the answer for the request table — and what it means for our recurring question.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <div class="controls" id="provBtns">
      <button class="btn" data-k="who">Who collected it?</button>
      <button class="btn" data-k="when">When?</button>
      <button class="btn" data-k="why">For what purpose?</button>
      <button class="btn" data-k="missing">What was omitted?</button>
    </div>
    <div class="result" id="provOut">Pick a question.</div>
  </div>
  <div class="callout idea"><span class="label">Habit to form</span><p>Write the answers next to the data <em>before</em> you use it. A week later you will not remember, and the person who receives your map never knew.</p></div>

  <div class="quiz" data-answer="1" data-fb="The asset is the thing whose state changed. P1 is the observation: its status can move to Resolved, but its reported position and date stay as evidence.">
    <div class="q">The team replaces the bulb in streetlight SL-0113, reported by request P1. Which record should get its condition and last-inspected fields updated?</div>
    <div class="opts">
      <button class="opt">Request P1 — move its coordinates onto the light and mark it Resolved</button>
      <button class="opt">Asset SL-0113 — update condition and inspection date; P1 only changes status</button>
      <button class="opt">Ward A — it contains both</button>
      <button class="opt">Neither; merge P1 and SL-0113 into one record</button>
    </div><div class="fb"></div>
  </div>

  <div class="callout note"><span class="label">Comprehension check (write it down)</span><p>For asset SL-0113 and request P1 (about 7 m apart), explain (a) why the two records must stay separate rather than being merged into one “streetlight problem” record, and (b) which of the two should have its condition fields updated after the team visits.</p></div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const rb = document.getElementById("reqBtns");
  rb.innerHTML = FIXTURE.requests.map((p, i) => `<button class="btn small" data-i="${i}" aria-pressed="${p.id === "P5"}">${p.id}</button>`).join("");
  function showRec(i) {
    const p = FIXTURE.requests[i];
    rb.querySelectorAll("button").forEach(b => b.setAttribute("aria-pressed", String(+b.dataset.i === i)));
    document.getElementById("recordCard").innerHTML = `
      <div class="part geom"><h4>Geometry · where</h4><div class="kv"><b>x</b><span>${p.x}</span><b>y</b><span>${p.y}</span></div><p class="small" style="margin:.5rem 0 0">Draw it; measure distance; test containment.</p></div>
      <div class="part attr"><h4>Attributes · what</h4><div class="kv"><b>category</b><span>${p.category}</span><b>priority</b><span>${p.priority}</span><b>status</b><span>${p.status}</span><b>channel</b><span>${p.channel}</span><b>note</b><span>${p.note || "—"}</span></div></div>
      <div class="part time"><h4>Time · when</h4><div class="kv"><b>reported</b><span>${p.reported}</span><b>closed</b><span>${p.closed || "— (empty)"}</span></div><p class="small" style="margin:.5rem 0 0">Age; ordering; change over time.</p></div>`;
    const notes = { P5: "P5 has a reported time, an empty closed time, and two more events hidden in free text. That history is real information — and hard to query.", P6: "P6 is closed as a duplicate, yet the tree may still be down under another ID. The status describes the <em>record</em>, not the problem.", P2: "P2 is “In progress” and its note says a team was assigned. Is that unresolved? Depends on the definition (1.1.2).", P4: "P4 is the only request with a closed date and a clean Resolved status.", P1: "P1 is the simplest case: one position, one status, one date.", P3: "P3 is the newest High-priority Open request and only 250 m from the road." };
    document.getElementById("recordNote").innerHTML = notes[p.id];
  }
  rb.addEventListener("click", e => { const b = e.target.closest("button"); if (b) showRec(+b.dataset.i); });
  showRec(4);

  renderMap(document.getElementById("offsetMap"), { layers: { wards: true, roads: true, requests: true, assets: true }, caption: "Diamonds are assets; dots are requests. Each pair is a few metres apart." });

  const prov = {
    who: "<strong>Who:</strong> citizens, via mobile app, helpline phone, and web form; the position was tapped by the citizen or typed in by helpline staff from a spoken description. <em>Consequence:</em> positions are rough, and how rough depends on the channel.",
    when: "<strong>When:</strong> reports dated 15 August to 11 September 2026; the data was put together on 18 September 2026. <em>Consequence:</em> a request reported after 11 September is not here. “Unresolved today” cannot be answered with certainty.",
    why: "<strong>Purpose:</strong> to send teams to jobs — not to measure asset condition or map the city. <em>Consequence:</em> there is no field saying whether the reported problem was <em>confirmed</em> by anyone.",
    missing: "<strong>Left out:</strong> problems nobody reported (no smartphone, no balance, night-time, gave up); duplicate reports that were merged (P6’s twin is absent); requests outside the two wards unless someone happened to file one. <em>Consequence:</em> a cluster of requests tells you who reports as much as what is broken — module 1.6."
  };
  document.getElementById("provBtns").addEventListener("click", e => { const b = e.target.closest("button"); if (b) document.getElementById("provOut").innerHTML = prov[b.dataset.k]; });
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
