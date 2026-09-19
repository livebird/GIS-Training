<?php $page = ['title' => '8.3 Fields, blanks, and dates', 'chapter' => 8, 'module' => '8.3']; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Module 8.3 · General idea, with ArcGIS / ArcGIS Online / PostgreSQL behaviour</div>
    <h1>Describe every field — and say what a blank means</h1>
    <p class="lead">A <strong>data dictionary</strong> is the table that describes your tables: one row per field, saying what the value <em>means</em> and how it may be filled. It is the most useful document a GIS team can hand to a developer, and the one most often missing. This module builds one, then looks at the two places designs go quietly wrong: blanks and dates.</p>
    <div class="outcomes"><h4>In this module you will</h4>
      <ul><li>Write a dictionary row with seven parts: name, meaning, type, unit, blank rule, example, source.</li>
      <li>Tell apart four different kinds of “blank”: unknown, not applicable, zero, and empty text — and see how each leads to a different action.</li>
      <li>Store codes as text (so <code>0042</code> keeps its zero) and fix one written rule for dates and times — IST in the notebook, UTC in storage.</li></ul></div>
  </div>

  <h2><span class="mod">8.3.1</span>The data dictionary: seven parts per field</h2>
  <div class="table-wrap"><table>
    <thead><tr><th>Part</th><th>What to write</th><th>Why a programmer needs it</th></tr></thead>
    <tbody>
      <tr><td><strong>Field name</strong></td><td>The stored name: short, no spaces, starts with a letter, no reserved words (Esri states these rules for geodatabase fields)</td><td>It is what queries and code use</td></tr>
      <tr><td><strong>Meaning</strong></td><td>One sentence saying what the value <em>means</em>, not just what it is called</td><td>Separates <code>visited_at</code> from <code>recorded_at</code></td></tr>
      <tr><td><strong>Type</strong></td><td>Text with a length, whole number with a range, decimal, date/time</td><td>Decides what can be stored without loss (Chapter 7) and what arithmetic makes sense</td></tr>
      <tr><td><strong>Unit</strong></td><td>Metres, watts, years, or “none”; for dates, the time-zone rule (8.3.3)</td><td>Prevents the “22 ft” problem in the lab</td></tr>
      <tr><td><strong>Blank rule</strong></td><td>Is a blank allowed, and what does it <em>mean</em> (8.3.2)? Is there a default (8.6)?</td><td>Tells code what to do when the value is missing</td></tr>
      <tr><td><strong>Example</strong></td><td>One realistic value</td><td>Anchors the meaning</td></tr>
      <tr><td><strong>Source</strong></td><td>Who or what supplies the value: surveyor, field app, computed, imported</td><td>Tells you where to look when it is wrong</td></tr>
    </tbody></table></div>

  <h3>Types: the ArcGIS names next to the ones you know</h3>
  <p>GIS software has its own names for storage types. The ArcGIS ones (from Esri’s “ArcGIS field data types” page) map on to familiar ideas. QGIS and databases use their own words; the mapping is only approximate, so always check the target system.</p>
  <div class="table-wrap"><table>
    <thead><tr><th>You know it as</th><th>ArcGIS geodatabase type</th><th>Note for the designer</th></tr></thead>
    <tbody>
      <tr><td>16-bit integer</td><td>Short integer (−32,768 to 32,767)</td><td>Fine for counts, years, condition scores</td></tr>
      <tr><td>32-bit integer</td><td>Long integer (about ±2.14 billion)</td><td>Default whole number</td></tr>
      <tr><td><code>float</code></td><td>Float — “up to six digits” precisely</td><td>Rarely the right choice for measurements</td></tr>
      <tr><td><code>double</code></td><td>Double — “up to 15 digits”</td><td>Default for measurements like <code>pole_height_m</code></td></tr>
      <tr><td><code>varchar(n)</code></td><td>Text, with a length you set</td><td>Codes are text (8.3.3)</td></tr>
      <tr><td><code>datetime</code> without zone</td><td>Date — date and time, <strong>no time zone stored</strong>; Esri says values must be either all UTC or all in one local zone</td><td>The rule is <em>yours</em> to write (8.3.3)</td></tr>
      <tr><td><code>date</code> / <code>time</code></td><td>Date only / Time only</td><td>One half each</td></tr>
      <tr><td><code>datetime</code> with offset</td><td>Timestamp offset — date, time and offset from UTC</td><td>Carries “+05:30”; not the zone name</td></tr>
      <tr><td>UUID</td><td>GUID (you fill it) or GlobalID (the geodatabase fills it)</td><td>Module 8.4</td></tr>
      <tr><td>auto-number</td><td>ObjectID — unique, never blank, “maintained by ArcGIS”</td><td>Module 8.4</td></tr>
      <tr><td>—</td><td>Geometry — one per feature class</td><td>Module 8.2</td></tr>
    </tbody></table></div>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>Build three dictionary rows for the Inspection table</h3>
    <p>Choose the type and the blank rule for each field. Then check.</p>
    <div class="dict" id="dict"></div>
    <p><button class="btn small" id="dictCheck">Check</button> <span class="status-line q" id="dictStatus" style="display:inline-block"></span></p>
    <details class="reveal"><summary>Show the finished rows</summary>
      <div class="table-wrap"><table>
        <thead><tr><th>Field</th><th>Meaning</th><th>Type</th><th>Unit</th><th>Blank rule</th><th>Example</th><th>Source</th></tr></thead>
        <tbody>
          <tr><td class="mono">inspection_id</td><td>Stable ID of one visit; never reused</td><td>Text(8)</td><td>none</td><td>Never blank; unique</td><td class="mono">INS-0003</td><td>Given by the inspection app, in the order visits are typed in</td></tr>
          <tr><td class="mono">condition_code</td><td>Overall condition the crew judged, on the 1–5 scale</td><td>Short integer</td><td>scale 1–5</td><td>Blank allowed; blank = <em>not assessed on this visit</em></td><td class="mono">3</td><td>Crew’s judgement on site</td></tr>
          <tr><td class="mono">defects_found</td><td>Number of defects counted on the checklist for this asset type</td><td>Short integer</td><td>count</td><td>Blank allowed; blank = <em>checklist not completed</em>; 0 = <em>completed, none found</em></td><td class="mono">1</td><td>Crew’s checklist</td></tr>
        </tbody></table></div>
      <p>The two blank rules are different sentences on purpose. That is the whole of the next section.</p>
    </details>
  </div>

  <h2><span class="mod">8.3.2</span>Four kinds of blank</h2>
  <p>Programmers know that <code>NULL</code>, <code>0</code> and <code>''</code> are three different values. Data modeling adds one more twist: <strong>a blank itself can mean two different things</strong>, and a design that does not say which one will be misread.</p>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Same column, four assets — click each one</h3>
    <p>The field is <code>lamp_wattage_w</code> (the power of the lamp, in watts). See what the value means and what the maintenance office should <em>do</em>.</p>
    <div class="blankgrid" id="blanks"></div>
    <div class="status-line q" id="blankStatus">Click an asset.</div>
  </div>
  <p>If “unknown” and “not applicable” share the same blank in the same column, the lamp-replacement programme cannot tell SL-0127 from DR-0042. It will either waste a site visit on a drain or never find out SL-0127’s wattage. The fix is <strong>structural</strong>, not a comment: either (a) put streetlight-only fields on a separate table linked one-to-one to the asset (module 8.5), so a drain simply has no wattage row at all; or (b) use the asset type so that a rule can say “wattage is required when type = SL and must be blank otherwise” (module 8.6). Either way, the dictionary states what a blank means <em>for that field</em>.</p>
  <p>Same idea for <code>defects_found</code> on SL-0113’s two visits: <strong>0</strong> on 14 Sep 2025 means “checklist done, nothing found — close the visit”; <strong>1</strong> on 14 Mar 2026 means “raise a work order”. Had the first been <em>blank</em>, the right action would have been “checklist not done — go again” — the opposite of “close”.</p>
  <div class="callout dev"><span class="label">Developer view</span><p>This is <code>Optional&lt;T&gt;</code> versus <code>T</code> with a sentinel — and “not applicable” is really a type problem: a <code>Drain</code> type should simply not have a <code>wattage</code> member. <strong>Where the comparison stops:</strong> most GIS containers give you flat tables and no sum types, so the “type” has to be a code field plus a written rule, and the rule is enforced only where the platform enforces it (8.6).</p></div>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“Set numeric fields to 0 by default and avoid null-handling bugs.”</em> That turns a visible missing value into an invisible wrong one. With <code>defects_found</code> defaulted to 0, every un-inspected asset reports “no defects”, and the statistic “percent of assets with defects” looks better by exactly the number of visits that never happened. Chapter 4 taught the same for rasters: NoData is not zero.</p></div>

  <h2><span class="mod">8.3.3</span>Codes are text; dates need one written rule</h2>
  <h3>Codes are text</h3>
  <div class="try">
    <span class="tag">Try it</span>
    <h3>Type a code, store it as a number — watch what happens</h3>
    <div class="controls"><label>Code as written on the register: <input type="text" id="codeIn" value="0042" class="mono" style="font:inherit;font-family:var(--font-mono);padding:.3rem .5rem;border:1px solid var(--rule);border-radius:6px;width:120px"></label></div>
    <div class="grid-2">
      <div class="reccard"><div class="hdr">stored as a NUMBER</div><div class="row"><b>value</b><span class="v" id="codeNum"></span></div><div class="row"><b>matches “0042”?</b><span class="v" id="codeNumMatch"></span></div></div>
      <div class="reccard good"><div class="hdr">stored as TEXT</div><div class="row"><b>value</b><span class="v" id="codeTxt"></span></div><div class="row"><b>matches “0042”?</b><span class="v" id="codeTxtMatch"></span></div></div>
    </div>
    <p class="small">Try <code>07</code> (a ward code), a PIN code, or <code>SL-0113</code>. Anything you will never add up is text — even if it looks like a number.</p>
  </div>
  <p>Three reasons: leading zeros matter (<code>0042</code> ≠ <code>42</code> to a matching routine); arithmetic on a code is meaningless (adding two asset numbers, or averaging <em>category</em> codes); and many IDs mix letters and digits anyway. The exception is a genuine measurement or scale — the condition score 1–5 is a scale, so a short integer with a range rule (8.6) is right.</p>

  <h3>Dates and times: two questions, one written rule</h3>
  <p>Chapter 7 showed that a Shapefile date field cannot hold a time of day. Even where time <em>is</em> supported, two questions must be answered in the dictionary:</p>
  <ul>
    <li><strong>Which moment?</strong> When the <em>event</em> happened (the visit) or when the <em>record was typed</em> (module 8.7 keeps both as separate fields).</li>
    <li><strong>Which time zone — and does the storage remember it?</strong></li>
  </ul>
  <p>The second answer depends on the storage system, and no single behaviour can be assumed. What the documentation actually says:</p>
  <div class="table-wrap"><table>
    <thead><tr><th>Storage</th><th>What it does with time zones</th><th>So the designer must…</th></tr></thead>
    <tbody>
      <tr><td>ArcGIS geodatabase <code>Date</code> field</td><td>Stores date and time; <strong>no time zone</strong>. Esri: values must be “either all in UTC or all within the same local time zone”</td><td>Choose the rule and write it in the dictionary; the data does not enforce it</td></tr>
      <tr><td>ArcGIS geodatabase <code>Timestamp offset</code></td><td>Stores date, time and the offset from UTC (e.g. +05:30)</td><td>The offset travels with the value; the zone <em>name</em> does not</td></tr>
      <tr><td>ArcGIS Online hosted feature layer</td><td>“Date values in hosted feature layers are stored in coordinated universal time (UTC)”. If your source is in local time you “must specify that the date values are in the local time zone” when publishing; on display the value is converted back to local time; SQL date calculations must be given in UTC</td><td>Storage is UTC whether you like it or not; the <em>publishing step</em> is where a wrong assumption becomes a wrong stored value</td></tr>
      <tr><td>PostgreSQL <code>timestamp with time zone</code></td><td>Input with a zone “will be converted to UTC”; output is converted to the session’s zone</td><td>Same UTC-inside pattern</td></tr>
      <tr><td>PostgreSQL <code>timestamp without time zone</code></td><td>“will silently ignore any time zone indication”</td><td>Behaves like the ArcGIS <code>Date</code> field — the rule is yours</td></tr>
    </tbody></table></div>
  <p><strong>Our rule for the practice town:</strong> crews write times in <strong>IST</strong> (UTC+05:30) in the notebook; the database <strong>stores UTC</strong>; screens convert back to IST. IST is 5 hours 30 minutes <em>ahead</em> of UTC, so to store a value you <em>subtract</em> 5:30.</p>

  <div class="try">
    <span class="tag">Try it</span>
    <h3>IST → UTC converter (pure arithmetic, no browser clock involved)</h3>
    <div class="tconv">
      <div class="box"><h4>Written in the notebook (IST)</h4><input id="istIn" value="2026-03-14 10:42" placeholder="YYYY-MM-DD HH:MM"><p class="small" style="margin:.4rem 0 0">Try the four fixture visits, or a time before 05:30 such as <code>2026-03-14 02:00</code>.</p></div>
      <div class="box"><h4>Stored in the database (UTC)</h4><div class="big" id="utcOut">—</div><div class="small" id="utcNote"></div></div>
    </div>
    <div class="opbtns" id="istBtns"></div>
    <div class="table-wrap"><table>
      <thead><tr><th>Visit</th><th>Notebook (IST)</th><th>Stored (UTC)</th><th>Check</th></tr></thead>
      <tbody id="istTable"></tbody></table></div>
  </div>
  <p><strong>The edge case</strong> worth writing into the dictionary: an IST time before 05:30 becomes the <em>previous day</em> in UTC. A report that counts “visits on 14 March” must decide which day it means — the local day is almost always the business meaning — so the query must convert, not compare the raw stored date.</p>
  <div class="callout warn"><span class="label">Common mistake</span><p><em>“The database stores the time; the app can show it in any zone later.”</em> Only if the storage remembers the zone, or the rule is written down and followed by everyone. A <code>Date</code> field filled by two crews — one typing IST, one typing UTC — is unrecoverable: the values look identical and nothing says which is which.</p></div>

  <div class="quiz" data-answer="2" data-fb="7 and 6.5 have no unit; 22 ft converts exactly (22 × 0.3048 = 6.7056 m); 12 m is already metres; a height of 0 for a drain is “not applicable”. The new field is pole_height_m, Double, metres, blank = not measured, only meaningful for streetlights.">
    <div class="q">The lab’s bad table has a column <code>Height</code> with values <code>7</code>, <code>22 ft</code>, <code>12 m</code>, <code>6.5</code>, and <code>0</code> (on a drain). Which values can you convert to metres <em>without</em> going back to whoever wrote them?</div>
    <div class="opts">
      <button class="opt">All of them — assume metres where no unit is given.</button>
      <button class="opt">Only <code>12 m</code>.</button>
      <button class="opt">Only <code>22 ft</code> (→ 6.7056 m) and <code>12 m</code>; the others need a source check or a blank with a reason.</button>
      <button class="opt">None — the whole column must be re-surveyed.</button>
    </div><div class="fb"></div>
  </div>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  /* dictionary builder */
  const rows = [
    { f: "inspection_id", meaning: "Stable ID of one visit", type: "text", blank: "never" },
    { f: "condition_code", meaning: "Condition judged on the 1–5 scale", type: "short", blank: "notassessed" },
    { f: "defects_found", meaning: "Defects counted on the checklist", type: "short", blank: "notcompleted" }
  ];
  const types = [["", "type…"], ["text", "Text"], ["short", "Short integer"], ["double", "Double"], ["date", "Date"]];
  const blanks = [["", "blank rule…"], ["never", "Never blank"], ["notassessed", "Blank = not assessed"], ["notcompleted", "Blank = checklist not completed; 0 = none found"], ["zero", "Blank means 0"]];
  document.getElementById("dict").innerHTML = rows.map((r, i) => `<div class="drow" data-i="${i}"><span class="fname">${r.f}</span><span>${r.meaning}</span><select data-k="type">${types.map(o => `<option value="${o[0]}">${o[1]}</option>`).join("")}</select><select data-k="blank">${blanks.map(o => `<option value="${o[0]}">${o[1]}</option>`).join("")}</select><span></span></div>`).join("");
  document.getElementById("dictCheck").addEventListener("click", () => {
    let ok = 0;
    document.querySelectorAll("#dict .drow").forEach(r => { const i = +r.dataset.i; const t = r.querySelector('[data-k="type"]').value, b = r.querySelector('[data-k="blank"]').value; const good = t === rows[i].type && b === rows[i].blank; r.className = "drow " + (good ? "ok" : "bad"); if (good) ok++; });
    const s = document.getElementById("dictStatus"); s.className = "status-line " + (ok === 3 ? "ok" : "bad");
    s.textContent = ok === 3 ? "All three right. Note how the two blank rules differ." : `${ok} of 3 right. Hint: an ID is text; both counts are whole numbers; a blank must mean something specific — never “0”.`;
  });

  /* four kinds of blank */
  const cases = [
    { id: "SL-0113", v: "70", kind: "Known", mean: "A 70 W lamp is fitted.", act: "Order a 70 W replacement.", cls: "ok" },
    { id: "SL-0127", v: "(blank)", kind: "Unknown", mean: "Nobody has read the plate yet.", act: "Send someone to read the plate BEFORE ordering.", cls: "q" },
    { id: "DR-0042", v: "(blank)", kind: "Not applicable", mean: "A drain has no lamp. The question makes no sense here.", act: "Exclude from the lamp programme. Never send anyone to “find out”.", cls: "q" },
    { id: "SL-0250", v: "0", kind: "Zero", mean: "Known: no lamp is fitted (the light was decommissioned).", act: "Do not order; do not send a crew.", cls: "ok" }
  ];
  const bg = document.getElementById("blanks"), bs = document.getElementById("blankStatus");
  bg.innerHTML = cases.map((c, i) => `<div class="b" data-i="${i}"><div class="small mono">${c.id}</div><div class="val">${c.v}</div><h4>${c.kind}</h4></div>`).join("");
  bg.querySelectorAll(".b").forEach(b => b.addEventListener("click", () => {
    bg.querySelectorAll(".b").forEach(x => x.classList.remove("on")); b.classList.add("on");
    const c = cases[+b.dataset.i]; bs.className = "status-line " + c.cls; bs.innerHTML = `<strong>${c.kind}.</strong> ${c.mean} → <strong>${c.act}</strong>${(c.id === "SL-0127" || c.id === "DR-0042") ? " — and notice: the two blanks look identical in the table." : ""}`;
  }));

  /* codes as text */
  const ci = document.getElementById("codeIn");
  function code() {
    const raw = ci.value; const n = Number(raw);
    const numV = raw.trim() === "" ? "(empty)" : (isNaN(n) ? "ERROR — not a number" : String(n));
    document.getElementById("codeNum").textContent = numV;
    document.getElementById("codeNumMatch").textContent = numV === "0042" ? "yes" : "NO — “" + numV + "” ≠ “0042”";
    document.getElementById("codeTxt").textContent = raw === "" ? "(empty)" : raw;
    document.getElementById("codeTxtMatch").textContent = raw === "0042" ? "yes — stored exactly as written" : "stored exactly as written: “" + raw + "”";
  }
  ci.addEventListener("input", code); code();

  /* IST -> UTC */
  const inp = document.getElementById("istIn"), out = document.getElementById("utcOut"), note = document.getElementById("utcNote");
  function conv() {
    const u = istToUtc(inp.value.trim());
    if (!u) { out.textContent = "—"; note.textContent = "Type as YYYY-MM-DD HH:MM"; return; }
    out.textContent = u;
    const sameDay = u.slice(0, 10) === inp.value.trim().slice(0, 10);
    note.textContent = sameDay ? "IST − 5:30 = UTC. Same calendar day." : "IST − 5:30 crossed midnight: the UTC date is the PREVIOUS day. A “visits on this date” report must convert back to IST first.";
  }
  inp.addEventListener("input", conv); conv();
  document.getElementById("istBtns").innerHTML = F8.inspections.map(i => `<button class="btn small" data-t="${i.ist}">${i.id}</button>`).join("") + `<button class="btn small" data-t="2026-03-14 02:00">02:00 IST edge case</button>`;
  document.querySelectorAll("#istBtns button").forEach(b => b.addEventListener("click", () => { inp.value = b.dataset.t; conv(); }));
  document.getElementById("istTable").innerHTML = F8.inspections.map(i => { const u = istToUtc(i.ist); const h = i.ist.slice(11), m = u.slice(11); return `<tr><td class="mono">${i.id}</td><td class="mono">${i.ist}</td><td class="mono">${u}</td><td class="mono">${h} − 5:30 = ${m}</td></tr>`; }).join("");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
