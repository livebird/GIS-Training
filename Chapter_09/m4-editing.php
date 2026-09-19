<?php $page = ['title' => '9.4 Careful editing and snapping', 'chapter' => 9, 'module' => '9.4']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 9.4 · General idea, with ArcGIS Pro and QGIS names</div>
    <h1>Edit on a copy, predict first, and use snapping on purpose</h1>
    <p class="lead"><strong>Editing</strong> means changing stored shapes or values. Four shape edits cover most work: create, move, split, reshape. Each has side-effects on other columns and other records — so you write the prediction <em>before</em> the click. <strong>Snapping</strong> makes two roads meet at exactly the same point instead of 3 m apart; with the wrong setting it also drags a citizen’s complaint onto a nearby drain.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Predict what else changes when you create, move, split or reshape a feature.</li>
      <li>Use vertex/edge snapping with a tolerance you can explain — and see why “10 pixels” is a different ground distance at every zoom.</li>
      <li>Review every edit three ways; “saved successfully” proves nothing.</li></ul></div>
  </div>

  <h2><span class="mod">9.4.1</span>Four edits and their side-effects</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Edit</th><th>What it does</th><th>Typical reason</th><th>What else changes</th></tr></thead>
    <tbody>
      <tr><td><strong>Create</strong></td><td>Adds a feature: click a point, or click corners for a line/area and finish</td><td>A new asset; a complaint located after the fact (Chapter 3’s P7)</td><td>A new row; its ID must follow the identity policy; the software asks for the attribute values</td></tr>
      <tr><td><strong>Move</strong></td><td>Shifts a whole feature</td><td>A point placed on the wrong side of the road</td><td>Any derived value (distance to road, which ward) is now stale; features snapped to it do <em>not</em> move with it unless topological editing is on</td></tr>
      <tr><td><strong>Split</strong></td><td>Cuts one feature into two</td><td>A road maintained in two sections</td><td>One row becomes two: which keeps the ID? which values are copied, which recomputed (length!)? QGIS keeps the original ID on the <em>biggest</em> piece and creates new rows for the rest</td></tr>
      <tr><td><strong>Reshape</strong></td><td>Redraws part of a boundary or line</td><td>A ward edge follows the wrong road</td><td>Area/length changes; the neighbour sharing that edge may now overlap or leave a gap (9.5) unless edited topologically</td></tr>
    </tbody></table></div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Predict, then edit: fix Temple Lane (R4)</h3>
    <p>R4 runs from the temple at (300, 300) to (300, 497) — it stops <strong>3 m short</strong> of Main Road R1 (y = 500). The road register says it joins Main Road. Before moving the end point, tick the predictions you agree with, then apply the edit.</p>
    <div class="grid-2">
      <div>
        <div class="checklist">
          <label><input type="checkbox" data-save="pred1"><span>R4’s length changes from 197 m to 200 m — so a stored <code>length_m</code> column would now be wrong until recomputed.</span></label>
          <label><input type="checkbox" data-save="pred2"><span><code>width_m</code> = 5 does not change.</span></label>
          <label><input type="checkbox" data-save="pred3"><span>No inspection or complaint refers to a road, so no foreign key is touched.</span></label>
          <label><input type="checkbox" data-save="pred4"><span>R4 will now touch R1 in the middle of R1’s only segment; whether R1 <em>gains a vertex</em> at (300, 500) depends on the tool’s settings — and on a road network that decides whether the two roads are connected.</span></label>
          <label><input type="checkbox" data-save="pred5"><span>R4 keeps its ID; nothing is split.</span></label>
        </div>
        <div class="opbtns"><button class="btn accent small" id="applyR4">Apply the edit on the working copy</button><button class="btn small" id="undoR4">Back to as delivered</button></div>
        <div class="status-line q" id="r4msg">Not edited yet. Length of R4 = 197 m.</div>
      </div>
      <figure class="map-fig" id="r4fig"></figure>
    </div>
  </div>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">ArcGIS Pro (3.7 docs)</h4><p>“There are no buttons to start or stop an edit session” — it starts when you change something. <strong>Create Features</strong> pane for new features; <strong>Modify Features</strong> pane for move, reshape, split and vertex editing; <strong>Attributes</strong> pane for values. <strong>Save</strong> and <strong>Discard</strong> live on the <em>Edit</em> tab, <em>Manage Edits</em> group. <span class="small">Exact tool names inside Modify Features: verify in your installed version.</span></p></div>
    <div class="card"><h4 style="margin-top:0">QGIS 3.44</h4><p><strong>Toggle Editing</strong> on the layer; Add Point/Line/Polygon Feature; the <strong>Vertex Tool</strong> (with the Vertex Editor panel for typing exact coordinates); Move Feature(s), Reshape Features, Split Features on the Advanced Digitizing toolbar; <strong>Save Layer Edits</strong> commits, <strong>Rollback</strong> discards.</p><p class="small">PostGIS has no edit session: an <code>UPDATE</code> is committed with the transaction. “Work on a copy” becomes “work in a transaction or a copied table”.</p></div>
  </div>

  <h2><span class="mod">9.4.2</span>Snapping and tolerance</h2>
  <p><strong>Snapping</strong> is a drawing aid: when you click within a small distance (the <strong>tolerance</strong>) of an existing vertex or edge, the software stores <em>that</em> coordinate instead of where you clicked. You choose <em>what</em> to snap to — ArcGIS Pro calls these <em>snap agents</em> (Point, Endpoint, Vertex, Edge, Intersection, Midpoint…); QGIS calls them snap modes (Vertex, Segment, Area, Centroid…). The default tolerance in ArcGIS Pro is <strong>10 pixels</strong>; QGIS suggests 10–12 pixels. Both let you use map units instead.</p>
  <div class="callout warn"><span class="label">The catch</span><p>A tolerance in <strong>pixels</strong> is a different ground distance at every zoom. At a view where 1 pixel = 1 m, 10 pixels = 10 m. Zoom in until 1 pixel = 0.1 m and the same setting is 1 m. The QGIS guide warns: “If you specify a value that is too big, the GIS may snap to a wrong vertex, especially if you are dealing with a large number of vertices close together.”</p></div>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Snapping simulator — the good case and the bad case</h3>
    <p>Two situations from the practice town. Change the zoom and the tolerance and watch what happens.</p>
    <div class="controls">
      <label>Zoom: 1 pixel = <select id="zoom"><option value="5">5 m</option><option value="1" selected>1 m</option><option value="0.5">0.5 m</option><option value="0.1">0.1 m</option></select></label>
      <label>Tolerance <input type="range" id="tol" min="2" max="20" value="10"> <span id="tolv">10 px</span></label>
      <label><input type="checkbox" id="agEdge" checked> Edge snapping on</label>
      <label><input type="checkbox" id="agPoint" checked> Point snapping on</label>
    </div>
    <div class="snapsim">
      <div>
        <h4 style="margin:.4rem 0">Good: close the 3 m gap of R4</h4>
        <figure class="map-fig" id="snapA"></figure>
        <div class="status-line" id="snapAmsg"></div>
      </div>
      <div>
        <h4 style="margin:.4rem 0">Bad: place complaint P9 near (1000, 500)</h4>
        <figure class="map-fig" id="snapB"></figure>
        <div class="status-line" id="snapBmsg"></div>
      </div>
    </div>
    <p class="small">Drain DR-0042 is at (995, 510); the click for P9 is at (1000, 500). Distance = √(5² + 10²) = √125 ≈ <strong>11.2 m</strong>. With Point snapping on and a tolerance that reaches 11.2 m, the citizen’s reported position is silently replaced by the surveyed drain position — two different facts now share one coordinate, and module 9.6’s “same location?” test is poisoned by the editing tool.</p>
  </div>
  <div class="grid-2">
    <div class="card"><h4 style="margin-top:0">When snapping does harm</h4><ul>
      <li><strong>Moving real observations</strong> onto assets (the P9 case). Turn Point/Vertex snapping <em>off</em> when capturing independent reports.</li>
      <li><strong>Joining unrelated things</strong>: a flyover traced near Main Road snaps onto it and now “touches” a road it passes over on a bridge (9.5.3).</li>
      <li><strong>A boundary-exact coordinate is a clue.</strong> A complaint stored at exactly (1000, 500) — right on the ward line — suggests Edge snapping to the ward layer was on during capture.</li>
    </ul></div>
    <div class="card"><h4 style="margin-top:0">Handy controls</h4><ul>
      <li>ArcGIS Pro: snapping on/off from the <em>Edit</em> tab or the status bar; <strong>hold the Spacebar</strong> to turn snapping off temporarily while drawing; settings in <em>Editor Settings ▸ Snapping</em>.</li>
      <li>QGIS: <em>Project ▸ Snapping Options…</em>; <strong>Topological editing</strong> moves a shared boundary in both neighbours together; <strong>Avoid Overlap</strong> cuts a new polygon to fit its neighbours.</li>
      <li>Typing a known coordinate beats dragging: when the register says the junction is (300, 500), type it.</li>
    </ul></div>
  </div>

  <h2><span class="mod">9.4.3</span>Three reviews after every edit</h2>
  <ol>
    <li><strong>Visual review.</strong> Zoom to the edited feature. For R4: the end sits <em>on</em> R1 — no gap, no overshoot.</li>
    <li><strong>Attribute review.</strong> Open the row and <em>read</em> the stored coordinate — (300, 500) — do not infer it from the picture. For a split: two rows, IDs as intended, values on both.</li>
    <li><strong>Invariant review.</strong> Re-run the check that caused the edit. Fixed a gap? Re-run the gap check. Fixed a wrong category? Re-run the domain query. Moved a point? Confirm the count is unchanged.</li>
  </ol>
  <p>Then write the before/after record (9.7): feature, what changed, value before, value after, evidence, who, when.</p>

  <div class="quiz" data-answer="1" data-fb="55.2 m needs 10 pixels ≥ 55.2 m, i.e. 1 pixel ≥ 5.52 m — a view about 5.5 km wide on a 1,000-pixel map. So capture points zoomed in, or set the tolerance in map units, and turn Point snapping off for independent observations.">
    <div class="q">Streetlights SL-0113 (205, 195) and SL-0114 (260, 190) are 55.2 m apart. At what zoom would a 10-pixel Point-snapping tolerance let a click meant for SL-0114 land on SL-0113?</div>
    <div class="opts">
      <button class="opt">Never — 55 m is too far for snapping.</button>
      <button class="opt">When 1 pixel is about 5.5 m or more (a very zoomed-out view).</button>
      <button class="opt">When 1 pixel is 0.1 m (a very zoomed-in view).</button>
      <button class="opt">Only if Edge snapping is on.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const fig = document.getElementById("r4fig"), msg = document.getElementById("r4msg");
  const drawR4 = fixed => renderTown(fig, { fixR4: fixed, show: ["wards", "roads"], marks: fixed ? [{ x: 300, y: 500, r: 40, t: "now on R1 at (300, 500)" }] : [{ x: 300, y: 497, r: 40, t: "3 m gap" }], caption: fixed ? "Working copy after the edit." : "As delivered: R4 ends at (300, 497)." });
  document.getElementById("applyR4").addEventListener("click", () => { drawR4(true); msg.className = "status-line ok"; msg.textContent = "Edited on the copy. R4 end point (300, 497) → (300, 500). Length 197 m → 200 m. width_m unchanged. Now do the three reviews (9.4.3) and write the before/after record."; });
  document.getElementById("undoR4").addEventListener("click", () => { drawR4(false); msg.className = "status-line q"; msg.textContent = "Not edited yet. Length of R4 = 197 m."; });
  drawR4(false);

  // snapping simulator
  const A = document.getElementById("snapA"), B = document.getElementById("snapB"), Am = document.getElementById("snapAmsg"), Bm = document.getElementById("snapBmsg");
  function sim() {
    const mpp = +document.getElementById("zoom").value, tolPx = +document.getElementById("tol").value, tolM = tolPx * mpp;
    document.getElementById("tolv").textContent = `${tolPx} px = ${fx(tolM, 1)} m at this zoom`;
    const edge = document.getElementById("agEdge").checked, point = document.getElementById("agPoint").checked;
    // panel A: local view around (300, 500), 60 m wide
    const view = (el, cx, cy, half, body) => { const W = 600, H = 400, k = W / (2 * half); const sx = x => (x - cx) * k + W / 2, sy = y => H / 2 - (y - cy) * k; el.innerHTML = `<svg viewBox="0 0 ${W} ${H}" role="img">${body(sx, sy, k)}</svg>`; };
    const gapA = 3, snapA = edge && tolM >= gapA;
    view(A, 300, 498, 12, (sx, sy, k) => `<line class="road" x1="${sx(280)}" y1="${sy(500)}" x2="${sx(320)}" y2="${sy(500)}"/><text class="road-label" x="${sx(289)}" y="${sy(500) - 14}" style="font-size:22px">R1 (y = 500)</text>
      <line class="road bad" x1="${sx(300)}" y1="${sy(480)}" x2="${sx(300)}" y2="${sy(497)}"/><text class="road-label" x="${sx(302)}" y="${sy(488)}" style="font-size:22px">R4</text>
      <circle class="snapring" cx="${sx(300)}" cy="${sy(497)}" r="${Math.min(tolM * k, 500)}"/><circle class="cursor" cx="${sx(300)}" cy="${sy(497)}" r="8"/>
      ${snapA ? `<line class="dline" x1="${sx(300)}" y1="${sy(497)}" x2="${sx(300)}" y2="${sy(500)}"/><circle class="req hit" cx="${sx(300)}" cy="${sy(500)}" r="9"/>` : ""}
      <text class="note-text" x="12" y="${400 - 12}" style="font-size:20px">view 24 m wide · dashed ring = snap tolerance ${fx(tolM, 1)} m</text>`);
    Am.className = "status-line " + (snapA ? "ok" : "bad");
    Am.textContent = !edge ? "Edge snapping is off — the end point stays wherever you drop it. Turn it on, or type (300, 500)." : snapA ? `Tolerance ${fx(tolM, 1)} m ≥ gap 3 m: the pointer snaps to R1’s edge and (300, 500) is stored.` : `Tolerance ${fx(tolM, 1)} m < gap 3 m: no snap. Zoom out, set the tolerance in map units, or type the coordinate.`;
    // panel B: around (1000,500); DR-0042 at (995,510); distance 11.18
    const d = Math.hypot(5, 10), snapB = point && tolM >= d;
    view(B, 998, 505, 16, (sx, sy, k) => `<line class="axis" x1="${sx(1000)}" y1="${sy(480)}" x2="${sx(1000)}" y2="${sy(530)}"/><text class="axis-label" x="${sx(1000) + 6}" y="${sy(528)}" style="font-size:20px">ward line x = 1000</text>
      <circle class="asset" cx="${sx(995)}" cy="${sy(510)}" r="10"/><text class="asset-label" x="${sx(995) - 130}" y="${sy(510) - 14}" style="font-size:20px">DR-0042 (995, 510)</text>
      <circle class="snapring" cx="${sx(1000)}" cy="${sy(500)}" r="${Math.min(tolM * k, 500)}"/><circle class="cursor" cx="${sx(1000)}" cy="${sy(500)}" r="8"/><text class="req-label" x="${sx(1000) + 12}" y="${sy(500) + 26}" style="font-size:22px">click (1000, 500)</text>
      ${snapB ? `<line class="dline" x1="${sx(1000)}" y1="${sy(500)}" x2="${sx(995)}" y2="${sy(510)}"/><circle class="req" cx="${sx(995)}" cy="${sy(510)}" r="11"/>` : `<circle class="req" cx="${sx(1000)}" cy="${sy(500)}" r="11"/>`}
      <text class="note-text" x="12" y="${400 - 12}" style="font-size:20px">view 32 m wide · DR-0042 is 11.2 m from the click</text>`);
    Bm.className = "status-line " + (snapB ? "bad" : "ok");
    Bm.textContent = !point ? "Point snapping is off — P9 is stored where the citizen said: (1000, 500). Correct." : snapB ? `Tolerance ${fx(tolM, 1)} m ≥ 11.2 m: P9 is stored at (995, 510) — the drain’s position, not the complaint’s. A real observation has been moved.` : `Tolerance ${fx(tolM, 1)} m < 11.2 m: no snap this time — but the risk is one zoom step away.`;
  }
  ["zoom", "tol", "agEdge", "agPoint"].forEach(id => document.getElementById(id).addEventListener("input", sim)); sim();
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
