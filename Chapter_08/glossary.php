<?php $page = ['title' => 'Chapter 8 glossary', 'chapter' => 8]; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Chapter 8</div>
    <h1>Glossary</h1>
    <p class="lead">Terms as used in this chapter, in plain words. Tags: <span class="pill">Esri</span> <span class="pill">QGIS</span> <span class="pill">SQL</span> — untagged terms are general ideas that work in any GIS or database.</p>
  </div>
  <input class="gloss-search" id="q" type="search" placeholder="Type to filter… e.g. grain, domain, orphan, GlobalID" aria-label="Filter glossary">
  <dl class="gloss" id="gloss"></dl>
  <p style="margin-top:2rem"><a class="btn ghost" href="./">← Chapter home</a></p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const E = ' <span class="pill">Esri</span>', Q = ' <span class="pill">QGIS</span>', S = ' <span class="pill">SQL</span>';
  const terms = [
    ["Entity", "A kind of thing you record — asset, inspection, request, ward, team. Each kind becomes one table."],
    ["Feature class", "Esri’s name for a table where every row has a shape of the same kind (all points, or all lines, or all polygons), the same fields and the same coordinate system. General word: spatial table." + E],
    ["Stand-alone table", "A table without a shape column (Inspection, Team). Esri’s term; QGIS just calls it a table or layer without geometry." + E],
    ["Grain", "The exact statement of what one row of a table stands for: one asset, one visit, one complaint. A table must have one grain only."],
    ["Field / record", "GIS words for a column and a row of a table."],
    ["Data dictionary", "The document describing every field: name, meaning, type, unit, blank rule, example, source."],
    ["Shape specification", "The six-part statement for a spatial table: shape type, multipart rule, grid and units, Z, what the shape stands for, how the location was obtained."],
    ["Multipart", "One row holding several separate pieces (an island group as one ward). Allowed for lines and polygons in ArcGIS; a design decision, not a default."],
    ["Location method", "A coded value saying how a position was obtained: SURVEY, GNSS (phone/GPS), DIGITISED (clicked on a map), APPROX (estimated from words)."],
    ["Blank rule (null policy)", "Whether a field may be empty, and what empty means there — unknown, or not applicable, or not assessed."],
    ["Unknown / not applicable / zero / empty text", "Four different facts that must not share one representation without a written rule. A drain’s blank wattage (not applicable) is not a streetlight’s blank wattage (unknown)."],
    ["Visit time / typing time", "When something happened (event time, e.g. visited_at) versus when its row was written (entry time, e.g. recorded_at). Keep both."],
    ["IST / UTC", "Indian Standard Time is UTC + 5:30. To store a notebook time as UTC, subtract 5 h 30 min; times before 05:30 IST fall on the previous UTC day."],
    ["Date · Date only · Time only · Timestamp offset", "Geodatabase date/time field types. Date stores date and time with no time zone; Timestamp offset also stores the offset from UTC (e.g. +05:30)." + E],
    ["Business identifier", "The stable, organisation-assigned name of a thing (SL-0113). Related records carry it."],
    ["Display label", "What people see on the map or in a list. Not a key — it can be re-worded."],
    ["Storage row number", "A number the storage engine gives a row for its own use (ObjectID, FID, SERIAL). Valid only inside that one copy of the dataset."],
    ["ObjectID", "A non-null unique integer per row, maintained by ArcGIS. For a database view without a key it may be generated at run time (ESRI_OID) and not stored at all." + E],
    ["GlobalID / GUID", "128-bit identifiers. A GlobalID is assigned and maintained by the geodatabase; a GUID you fill yourself. Append makes new GlobalIDs unless the Preserve Global IDs setting is on (enterprise geodatabases only)." + E],
    ["Identity policy", "Written rules for three questions: how imports are matched, how duplicates are handled, and which key related records carry."],
    ["Cardinality", "How many rows on each side of a relationship: one-to-one (1:1), one-to-many (1:M), many-to-many (M:N)."],
    ["Foreign key", "A column in one table holding the key of a row in another table — the pointer that makes a relationship real."],
    ["Junction table", "The table that implements many-to-many: one row per pair (team, ward), with any facts about the pair. Also called a link, bridge or intermediate table; ArcGIS creates one for M:N relationship classes."],
    ["Join", "A temporary read-time lookup that appends fields from another table into a layer. Lives in the map, not the data; enforces nothing."],
    ["Relate", "An ArcGIS project-level link used to select related rows without appending fields; available only while the project is open." + E],
    ["Relation", "A QGIS project-level parent/child declaration used for forms and selection; Composition strength cascades deletes inside QGIS only." + Q],
    ["Relationship class", "A geodatabase dataset that stores a relationship: origin and destination tables, keys, cardinality, and simple or composite behaviour on edit." + E],
    ["Simple vs composite relationship class", "Simple: deleting the origin sets the child’s foreign key to null. Composite: deleting the origin deletes the children (cascade delete)." + E],
    ["Foreign key constraint", "A database rule that a column’s values must exist in another table (referential integrity). ON DELETE may be NO ACTION, RESTRICT, CASCADE or SET NULL." + S],
    ["Attribute domain", "An ArcGIS rule describing a field’s allowed values: coded-value (code + description) or range (min–max). Defined once in the geodatabase, shared by many fields." + E],
    ["Coded-value list", "A fixed list of allowed codes, each with a human description. Store the code; show the description."],
    ["Range", "A minimum and maximum for a number or date."],
    ["Value Map / Range widget", "QGIS form widgets that store a code and show a description, or limit a number; assigned automatically from GeoPackage or file geodatabase domains." + Q],
    ["Constraint (soft / hard)", "QGIS not-null, unique or expression rules checked in forms; a hard constraint blocks saving; only while editing in QGIS." + Q],
    ["CHECK / NOT NULL / UNIQUE", "Database constraints: a value must satisfy an expression; may not be empty; may not repeat." + S],
    ["Default value", "What a new row receives for a field when nobody typed one. Never an observation."],
    ["Derived (cached) field", "A field whose value is computed from other rows (last_inspected_at) and must be refreshed by a written rule."],
    ["Editor tracking", "ArcGIS fields filled automatically: creator, creation date, last editor, last edit date. Records who and when — not what changed — and its dates are typing times." + E],
    ["Attachment", "An ArcGIS media file stored in a __ATTACH table linked one-to-many to its feature or row through a __ATTACHREL relationship class." + E],
    ["Orphan", "A child row whose foreign key matches no parent row. Found with a LEFT JOIN … WHERE parent IS NULL query."],
    ["Quarantine table", "A holding table for rows that cannot yet be linked to a parent (asset_id blank), so nothing is silently dropped or wrongly attached."],
    ["Schema owner", "The named role that approves changes to a table’s fields, domains, keys and relationships."],
    ["Migration impact checklist", "Eight questions answered before any schema change: what changes, reversible?, rows that cannot convert, who depends on it, related tables, fix and verification query, rollback, approval and log."],
    ["Attribute rule", "An ArcGIS scripted rule (calculation, constraint or validation) written in Arcade; needs GlobalIDs. Outside this chapter." + E],
    ["Table F8-1 / F8-2", "This chapter’s made-up practice data: two crews, and four inspections of two assets with IST times."]
  ];
  const dl = document.getElementById("gloss");
  const render = f => { dl.innerHTML = terms.filter(t => !f || (t[0] + " " + t[1]).toLowerCase().includes(f)).map(t => `<dt>${t[0]}</dt><dd>${t[1]}</dd>`).join("") || "<dd>No matching term.</dd>"; };
  document.getElementById("q").addEventListener("input", e => render(e.target.value.trim().toLowerCase()));
  render("");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
