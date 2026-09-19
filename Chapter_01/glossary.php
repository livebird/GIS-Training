<?php $page = ['title' => 'Chapter 1 glossary', 'chapter' => 1]; require __DIR__ . '/../partials/head.php'; ?>
  <div class="page-head fade-up">
    <div class="eyebrow">Chapter 1</div>
    <h1>Glossary</h1>
    <p class="lead">Terms as used in this chapter. Later chapters refine several of them.</p>
  </div>
  <input class="gloss-search" id="q" type="search" placeholder="Type to filter… e.g. layer, extent, ward" aria-label="Filter glossary">
  <dl class="gloss" id="gloss"></dl>
  <p style="margin-top:2rem"><a class="btn ghost" href="./">← Chapter home</a></p>
<?php require __DIR__ . '/../partials/foot.php'; ?>
<script>
function pageInit() {
  const terms = [
    ["Absolute location", "A position given directly as numbers in a coordinate system, e.g. (995, 510) on the practice grid."],
    ["Asset", "A physical thing the city office maintains (streetlight, drain, tree) with a present condition."],
    ["Attribute table", "The table of values behind a map layer, one row per record."],
    ["Crew / field team", "The workers who go out to fix things. Their base is the depot."],
    ["Attribute", "A descriptive, non-spatial value stored with a record (category, status, date)."],
        ["Boundary-inclusive / strict", "Whether a value exactly on the edge counts. ≤ 300 m includes a request at exactly 300 m (inclusive); < 300 m does not (strict). Must be stated for every rule."],
    ["Capture, store, analyse, communicate", "The four functions of a GIS."],
    ["Containment", "The question of whether one geometry lies inside another."],
    ["Dataset", "The stored collection of records (a file or database table) that a layer draws."],
    ["Extent (spatial extent)", "The area you are looking at for a question. A decision you make, not something the data tells you."],
    ["Fitness for purpose", "Whether a dataset is good enough for a particular job, rather than “accurate” on its own."],
    ["Fixture / practice data", "The made-up town (two wards, one road, six requests, three assets) used in every example. The chapter document calls it the fixture."],
    ["Geocoding", "Turning a text address into a position. A separate, error-prone step (Chapter 9)."],
    ["Geometry", "The part of a record that represents space; in this chapter, a point position."],
    ["GIS", "Geographic(al) Information System: data, software, hardware, and the people and processes that use them to capture, store, analyse, and communicate location-linked information."],
    ["Inspection", "A record of a field team’s visit to an asset or request; designed properly in Chapter 8."],
    ["Legend", "The key on a map that explains what each symbol and colour means."],
    ["Layer", "A set of records of one kind drawn together on a map from a referenced dataset; can be reordered, hidden, and shown."],
    ["Observed event", "Something that happened at a time and place as reported (a request). Its observation should not be overwritten."],
    ["Policy versus evidence", "Policy = the rules the office decides (not an insurance or government policy). Evidence = the facts a GIS can produce."],
    ["Proximity", "The question of how near things are; requires a distance, a unit, and a comparison rule."],
    ["Provenance", "Where the data came from: who produced it, when, why, and what it leaves out."],
    ["Relative location", "A place described by its relation to something else (“beside the school”)."],
    ["Request (service request)", "A citizen’s report of a problem — a complaint or grievance — with a reported position, attributes, and times."],
    ["Schematic", "A simple drawing to explain an idea; not to scale and not measured."],
    ["Selection", "Marking records for attention in both map and table without changing them."],
    ["Straight-line distance", "Distance “as the crow flies” between two positions; not the distance by road, and not travel time."],
    ["Validation", "Checking that an answer looks reasonable and that someone else could repeat it, before acting on it."],
    ["Ward", "An administrative area drawn by an authority — the same idea as a municipal ward you vote in. Here, two made-up 1 km squares."]
  ];
  terms.sort((a, b) => a[0].localeCompare(b[0]));
  const g = document.getElementById("gloss");
  function render(f) { g.innerHTML = terms.filter(t => !f || (t[0] + " " + t[1]).toLowerCase().includes(f)).map(t => `<dt>${t[0]}</dt><dd>${t[1]}</dd>`).join("") || "<dd>No matching term.</dd>"; }
  document.getElementById("q").addEventListener("input", e => render(e.target.value.trim().toLowerCase()));
  render("");
}
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
