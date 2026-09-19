# GIS and ArcGIS Fundamentals — Phase 1

# Chapter 8 — GIS data modeling and relationships

## Document metadata

| Item | Value |
| --- | --- |
| Chapter ID | Phase 1, Chapter 8 |
| Title | GIS data modeling and relationships |
| Source specification | `GIS_Phase_1_Chapter_Authoring_Blueprint.md`, revision 1.0 (18 September 2026) |
| Document revision | 1.0 — 19 September 2026 |
| Reference-check date | 19 September 2026 (every cited page was opened and read on this date; see the reference list for exact URLs) |
| Audience | Software developers at LiveBird Technologies who have completed Chapters 1–7 and have no other GIS background |
| Software referenced | ArcGIS Pro ("latest" documentation, whose page metadata identifies the release as **3.7** on the check date); ArcGIS Online (continuously updated web service; documentation read on the check date); ArcGIS Enterprise ("latest" documentation, one page); QGIS Desktop 3.40 (long-term-release User Guide edition) and the QGIS *Gentle Introduction to GIS* 3.44 edition named in the blueprint; PostgreSQL ("current" documentation) and PostGIS documentation for the transferable database examples |
| Execution status | **No procedure in this chapter has been executed in ArcGIS Pro, ArcGIS Online, QGIS, or a database by the author.** The chapter's primary lab is a *design* exercise done on paper or in a spreadsheet, so it needs no software. The optional software procedures (Instructor Appendix I.6) were written from the official pages cited beside them and are marked *not execution-tested*. |
| Data status | Every coordinate, record, name, identifier, photo file name, team, date, and time in this chapter is **synthetic training material**. Nothing describes a real municipality, asset register, or organisation. The "poor design" table in the lab was written deliberately badly for teaching. |

### How to use this document

Read the modules in order; each one assumes the ones before it. Boxes labelled **Procedure (version-specific)** contain software steps that will age; everything outside those boxes is durable concept. Boxes labelled **Platform note** separate ArcGIS-, QGIS-, or database-specific behaviour from general GIS principles, and boxes labelled **Verification item** tell an instructor what must be checked in the installed environment before a step is treated as fact. Comprehension checks have no answers in the learner text; answers are in the Instructor Appendix, which learners should not open before the progression gate in 8.9.

This chapter uses ordinary database vocabulary — table, row, column, primary key, foreign key — because you already know it. Where GIS software uses a different word for the same thing (for example *feature class* for a table with geometry, or *field* for a column), the GIS word is introduced at first use and both words are listed in the glossary.

---

## Prerequisites

- **Chapter 3 completed.** You can explain a feature (geometry plus attributes), the three basic geometry types (point, line, polygon), single-part versus multipart features, and the difference between a stored dataset and a layer that presents it. You have seen an attribute table and know that a record's display label is not automatically its identifier.
- **Chapter 7 completed.** You can tell a file encoding, a container, a spatial database, and a service apart; you know a shapefile's attribute limits, that GeoPackage is a SQLite-based container, and that an ArcGIS geodatabase is storage *plus* an information model. You have run a before/after conversion check.
- **Chapters 5–6 (context only).** You know that every coordinate needs a stated coordinate reference system (CRS), units, and coordinate order, and that measuring on the training grid is planar arithmetic. This chapter only asks you to *record* those facts in a schema; it does not ask you to choose a projection.
- Ordinary developer experience with relational databases: tables, primary and foreign keys, `NULL`, and a rough idea of normalisation. These are used as the starting point, not taught from scratch.
- **No software is required** for the main path of this chapter. The lab is completed with a text editor, a spreadsheet, or paper.

## Learning outcomes

By the end of this chapter you should be able to:

| ID | Outcome | Where it is taught | Where it is checked |
| --- | --- | --- | --- |
| LO1 | Separate assets, inspections, requests, wards, and teams into distinct entities, state the record grain of each, and justify every field by a business question. | 8.1 | 8.9 concept Q1; practical |
| LO2 | Write a geometry specification for each spatial entity — geometry type, multipart policy, CRS and units, optional Z, what the point *represents*, and whether the location is measured or approximate. | 8.2 | 8.9 concept Q2; practical |
| LO3 | Produce a data dictionary with type, units, null policy, example, and source, and distinguish unknown, not applicable, zero, and empty text. State a timestamp and time-zone convention for a named storage system. | 8.3 | 8.9 concept Q3; scenario Q1 |
| LO4 | Separate a stable business identifier from a display label and from a storage-generated row identifier; state an identity policy for imports, duplicates, and related records; explain what ArcGIS ObjectID and GlobalID are and what they do not guarantee. | 8.4 | 8.9 concept Q4; scenario Q2 |
| LO5 | Model 1:1, 1:M, and M:N relationships with keys, and distinguish a conceptual relationship from a join, a QGIS relation, a database foreign key, and an ArcGIS relationship class. | 8.5 | 8.9 concept Q5; practical |
| LO6 | Define coded-value and range domains, separate stored code from displayed description, explain why a default is not an observation, and decide whether a rule belongs in the schema, the application, or a review procedure. | 8.6 | 8.9 concept Q6; scenario Q1 |
| LO7 | Model current state versus history, event time versus entry time, and attachments as related records; write a query that finds orphaned records; name the owner of a schema change and complete a migration impact checklist. | 8.7 | 8.9 scenario Q2; practical; oral |
| LO8 | Turn a deliberately poor single-table design into a coherent multi-table schema with a diagram, a dictionary, relationship definitions, example records, and answerable business questions. | 8.8 | 8.8 lab; 8.9 practical |

## Required materials

- This document, a text editor or spreadsheet, and something to draw a diagram with (paper is fine).
- The **Chapter 8 fixture** printed in this document (the Chapter 1 tables, plus the new Team table and the deliberately poor table used in the lab). No download is needed.
- **Optional:** ArcGIS Pro **or** QGIS Desktop, only if you want to implement the schema you design in the lab. The optional implementation route is in Instructor Appendix I.6 and is marked *not execution-tested*.
- **Not required:** an ArcGIS Online account, a database server, or any publishing action. Nothing in this chapter consumes credits.

## Recap of the preceding chapter

Chapter 7 looked at the *outside* of data: how a dataset is encoded (a CSV with separate CRS information, GeoJSON with its fixed coordinate convention), contained (a shapefile's companion files, a GeoPackage, a geodatabase), or served. You learned that a shapefile cannot hold a null number or a time of day, that field names are truncated to ten characters on export, and that a successful conversion can still lose meaning [S20]. You met the ArcGIS geodatabase as storage *plus* an information model — a place that can hold not only tables and feature classes but also rules and relationships between them [S21]. You filled in a dataset intake form for Chapter 7's exchange variant of the assets table (Table F7 — six assets with a leading-zero `legacy_code`, a null `condition_score` that means "not yet assessed", an ISO timestamp with a `+05:30` offset, and an empty `inspector_note` that is different from null) and learned to compare counts, types, and sample records before and after a conversion.

Chapter 7 closed by saying that it had shown what a container *can* hold and how to check what it *did* hold, and that Chapter 8 designs what *should* go inside. That is this chapter. The container is chosen; now you decide what tables exist, what each row means, which columns they carry, how rows identify themselves, and how rows in different tables refer to one another.

## The recurring scenario and the Chapter 8 fixture

The fictional municipality continues. It maintains **assets** (streetlights, drains, trees), keeps **road** centrelines, divides its territory into **wards**, receives **service requests** from the public, and sends **teams** to record **inspections**. The Chapter 1 fixture is reused unchanged: two ward squares (A and B, each 1 km²), one road (R1, from (0, 500) to (2000, 500)), six requests (P1–P6), and three assets (SL-0113, DR-0042, TR-0301). All coordinates are on the flat, metre-based training grid with **no Earth location and no coordinate-system identifier**. If you need those tables, they are Tables F1–F4 in the Chapter 1 document; the parts this chapter relies on are repeated below.

**Table F4 (from Chapter 1) — Assets (synthetic).**

| Asset ID | Type | (x, y) | Installed | Last inspected | Condition |
| --- | --- | --- | --- | --- | --- |
| SL-0113 | Streetlight | (205, 195) | 2018 | 2026-03-14 | Fair |
| DR-0042 | Drain | (995, 510) | 2011 | 2025-11-02 | Poor |
| TR-0301 | Tree | (2190, 520) | 2005 | — | Unknown |

Notice that Table F4 already contains a design problem that this chapter will fix: *Last inspected* and *Condition* describe **one** inspection, so a second visit would overwrite the first. Chapter 3 warned about this ("do not flatten all historical observations into one asset row") and previewed a separate non-spatial inspections table (Chapter 3, Table F9: INS-0001 to INS-0003, keyed by `asset_id`); Chapter 7's exchange variant (Table F7) carried a decimal `condition_score` and an ISO timestamp with a `+05:30` offset on the asset row. Chapter 8 does the fixing: inspections become rows of their own, and the condition becomes an observation *per visit*.

**New for Chapter 8 — Table F8-1, Teams (synthetic).** Two inspection teams are introduced so that relationships involving people can be modelled. Chapter 3's inspector user names map onto them: `crew01` belongs to T-S and `crew02` to T-N.

| Team ID | Team name | Active |
| --- | --- | --- |
| T-N | North crew | Yes |
| T-S | South crew | Yes |

**New for Chapter 8 — Table F8-2, inspection history (synthetic).** Chapter 3's three inspections are kept with their IDs, dates, and conditions; this chapter adds the time of day (taken from Chapter 7's timestamps where one exists), the team, a defect count, and one further visit (INS-0004) that was typed in late from a paper form. Times are Indian Standard Time (IST, UTC+05:30) as written in the field notebook; module 8.3 converts them.

| Inspection ID | Asset | Visit date and time (IST) | Team | Condition (1–5) | Defects found | Remarks | Entered on |
| --- | --- | --- | --- | --- | --- | --- | --- |
| INS-0001 | DR-0042 | 2024-10-15 09:50 | T-S | 3 | 0 | *(none)* | 2024-10-15 |
| INS-0002 | DR-0042 | 2025-11-02 15:05 | T-S | 2 | 2 | Grating cracked; silt | 2025-11-02 |
| INS-0003 | SL-0113 | 2026-03-14 10:42 | T-N | 3 | 1 | Lamp flickers at dusk | 2026-03-14 |
| INS-0004 | SL-0113 | 2025-09-14 11:15 | T-N | 3 | 0 | *(none)* | 2026-03-16 (paper form found and typed in) |

Note that INS-0004 describes the *earliest* SL-0113 visit but has the *highest* number: identifiers are assigned when a record is entered, not when the event happened. Module 8.7 uses this to separate event time from entry time.

The condition scale used from this chapter on is **1 = Very poor, 2 = Poor, 3 = Fair, 4 = Good, 5 = Very good**; Chapter 1's and Chapter 3's words "Fair" and "Poor" map to 3 and 2, and Chapter 7's decimal `condition_score` is a different (per-asset, latest-only) field that this chapter replaces. "Unknown" is not a value on the scale — it is the *absence* of a value, which module 8.3 discusses.

The deliberately poor table for the lab (Table 8.8-A) is printed in module 8.8, not here, so that you meet it after learning what is wrong with it.

---

## 8.1 Start from entities, events, and questions

### 8.1.1 Five concepts that must not share a table

Data modeling starts before any software: you decide *what kinds of things* you are recording. In the municipal scenario there are at least five, and the first rule of this chapter is that they are different **entities** — different kinds of things, each deserving its own table.

| Entity | What one row stands for | Does it have its own location? | Does it refer to another entity? |
| --- | --- | --- | --- |
| **Asset** | One physical thing the municipality maintains: one streetlight pole, one drain, one tree | Yes — it sits somewhere (a point in our fixture) | Usually not; it is the thing others refer to |
| **Inspection** | One visit by one team to one asset on one date, and what they observed | No — it happens *at* the asset; the asset already holds the location | Yes — to the asset it inspected and to the team that did it |
| **Service request** | One report from the public about a problem at a place | Yes — the reporter's stated place, which may not match any asset | Optionally — to an asset, if a clerk identified which asset is meant |
| **Ward** | One administrative area | Yes — a polygon | No (it is referenced *by* others) |
| **Team** | One crew of people who inspect | No — a team is not a place | No; it is referenced by inspections |

Two questions in the table headings do most of the work of an early design. **"Does it have its own location?"** decides whether the entity becomes a spatial table (in ArcGIS vocabulary, a **feature class** — a table where every row has a geometry column, all of the same geometry type [X18]) or a plain **nonspatial table** (ArcGIS calls it a *stand-alone table*). **"Does it refer to another entity?"** decides where the foreign keys go. An inspection does not need coordinates of its own because it can *refer* to the asset, and the asset's coordinates come along through that reference. Storing coordinates on the inspection as well would give you two copies that can disagree.

**Why this matters.** Every later capability depends on getting this split right. "Show me all assets not inspected in 12 months" is a simple query when inspections are rows in their own table with an `asset_id`; it is a fragile spreadsheet formula when inspections are columns on the asset row. "Attach three photos to the second visit" is trivial when the visit is a row with its own identifier; it is impossible when the visit is a pair of columns.

**Developer analogy (and where it stops).** This is ordinary relational design: entities become tables, references become foreign keys, and you normalise so that each fact is stored once. The analogy is accurate as far as it goes. It stops at geometry: a GIS table has a column whose value is a *shape* with a CRS, and the software treats that column specially — it draws it, indexes it spatially, and constrains it: an ArcGIS feature class has exactly one geometry field and one geometry type [X18], and a PostGIS geometry column can be declared with a type modifier that restricts the shapes it accepts [X23]. You cannot treat geometry as "just another column" when deciding which table an entity belongs in; module 8.2 is devoted to that column.

### 8.1.2 Record grain: one asset, one visit, or one incident

The **grain** of a table is the precise answer to "what does one row represent?". Every table must have exactly one grain, written down. Mixing grains is the most common and most expensive design error in operational data, and it is usually accidental.

Three grains appear in the scenario:

- **One physical asset.** Row exists as long as the pole, drain, or tree exists. Attributes change slowly (installation year never; condition occasionally).
- **One inspection visit.** Row is created when a team visits and never changes afterwards, because it records what was seen *on that day*. Ten visits to the same asset are ten rows.
- **One reported incident.** Row is created when a member of the public reports something. Five people reporting the same broken light produce five rows (Chapter 1's P6 was marked a duplicate of another report — that is a *link between incident rows*, not a reason to delete one).

**Worked example — spotting mixed grain in Table F4.** Table F4 has columns *Installed* (asset grain), *Last inspected* and *Condition* (inspection grain, but only the latest one), and *Type* (asset grain). Because two grains share a row, the table can only ever hold one inspection per asset. When INS-0003 was recorded in March 2026, the previous *Condition* for SL-0113 (also 3, as it happens) was overwritten, and the evidence that the lamp had been checked and found sound six months earlier (INS-0004, September 2025) disappeared — so nobody can say *when* the flicker began. Nothing in the table tells you that history was lost, which is exactly the problem.

**The test to apply.** For each column, ask "when this value changes, *which kind of thing* changed — the asset, the visit, or the report?" If a table's columns give different answers, the table has mixed grain. The repair is always the same: move the columns that belong to a different grain into their own table and connect the two with a key (module 8.5).

**Misconception.** *"One row per asset with the latest inspection is simpler, and that is all the dashboard needs."* The dashboard may indeed need only the latest state — but a *derived* latest-state view can be computed from an inspection table at any time, while the reverse (recovering history from a latest-state table) is impossible. Simplicity in the display is not a reason to simplify the storage. Module 8.7 shows how to keep both.

### 8.1.3 Every field must earn its place

A **field** (the GIS word for a column) costs money every time it exists: someone must capture the value, someone must keep it correct, and everyone who reads the table must understand it. The QGIS introduction puts the principle plainly — decide attributes from the intended use, and note that "Collecting and storing unneeded information is a bad idea because of the cost and time required to research and capture the information" [S23]. In this course the test is stricter and more concrete: **every field must be traceable to at least one business question or workflow step.** A field that nobody can trace is removed from the design — not "kept in case".

**Worked example — justifying the Asset fields.**

| Proposed field | Business question or workflow it serves | Verdict |
| --- | --- | --- |
| `asset_type` | "How many streetlights do we maintain?"; selects the inspection checklist | Keep |
| `install_year` | "Which lights are older than 10 years?" (replacement planning) | Keep |
| `lamp_wattage_w` | Ordering the correct replacement lamp | Keep, but only meaningful for streetlights (module 8.3.2) |
| `pole_height_m` | Choosing the correct vehicle/ladder for a repair | Keep |
| `manufacturer_phone` | No question identified; the procurement system holds supplier contacts | Remove |
| `colour_of_pole` | "Might be useful for photos" — no question | Remove |
| `last_condition` | "Which assets are in poor condition right now?" | Keep as a *derived* value, recomputed from inspections (module 8.7) |

The last row shows the nuance: a field can be justified *and* still be the wrong place to store the original fact. `last_condition` is justified by a real question, but the *source of truth* is the inspection table; the asset row carries a cached copy for convenience, and the design must say so.

**Comprehension check 8.1.** A colleague proposes a single table called `Requests` with columns for the reporter's location, the asset the clerk matched it to (including the asset's coordinates and installation year), and the date the inspection team visited. Name the three grains mixed in this proposal and say which columns belong to each.

## 8.2 Define geometry and spatial reference deliberately

### 8.2.1 A geometry specification for every spatial entity

A relational designer writes `amount NUMERIC(12,2) NOT NULL` and has said everything about a column. A geometry column needs a longer sentence. For each spatial entity the design must state:

1. **Geometry type** — point, line, or polygon (Chapter 3). In an ArcGIS feature class every row has the same geometry type, the same attributes, and the same spatial reference [X18]; in PostGIS a column can be declared with a type modifier such as `geometry(POINT, 4326)` that "restricts the kind of shapes and dimensions allowed in the column" [X23]. A "mixed" column is possible in some systems but is a design decision, not a default.
2. **Multipart policy** — whether one row may hold several disconnected shapes. Esri's documentation notes that "Line and polygon feature classes can be composed of single parts or multiple parts" [X18]. The design must say which it wants and why (Chapter 3.3 gave the examples: an island group as one ward is multipart; two separate drains are two rows).
3. **Horizontal CRS and units** — the coordinate reference system in which coordinates are stored, its linear or angular units, and the coordinate order used in any exchange (Chapter 5). For the training fixture this is "local planar training grid, metres, (x, y), no EPSG code, no Earth location" — an unusual but complete statement.
4. **Z, and its unit and reference** — if heights are stored, what zero means (Chapter 5.6). Most municipal asset points do not need Z; if a design includes it, the vertical reference must be named, and "no Z" is itself a valid, explicit statement.
5. **What the shape represents** — module 8.2.2.
6. **How the location was obtained and how good it is** — module 8.2.3.

**Table 8.2 — Geometry specification for the Chapter 8 fixture (synthetic).**

| Entity | Geometry type | Multipart? | Horizontal CRS and units | Z | The shape represents |
| --- | --- | --- | --- | --- | --- |
| Asset | Point | Not applicable (one point per row; a point row is single by definition in ArcGIS, where multipoint is a *separate* geometry type [X18]) | Training grid, metres, (x, y), no EPSG | None | See Table 8.3 (varies by asset type) |
| Ward | Polygon | Allowed — a ward split by a river would still be one ward row | Training grid, metres | None | The administrative boundary as declared; edges are exact by definition in the fixture |
| Road | Line (polyline) | Not allowed — each physically separate carriageway segment is its own row | Training grid, metres | None | The road centreline, not the edge of the pavement |
| Request | Point | Not applicable | Training grid, metres | None | The place the reporter indicated, which is *not* an asset location |
| Inspection, Team, Coverage | No geometry | — | — | — | — |

**Platform note (ArcGIS versus others).** The *list* of things to specify is a general principle. *How* they are declared differs: an ArcGIS feature class fixes geometry type and spatial reference at creation and stores them as dataset properties [X18]; PostGIS stores them as a column type modifier and an SRID on the column [X23]; a GeoPackage records them in its metadata tables (Chapter 7); a shapefile records the CRS in the optional `.prj` file, which can be missing (Chapter 7 [S20]). The design document is where the decision lives; the container is where it is implemented.

### 8.2.2 What does the point stand for?

A point has no size, so a point representing a real object is always a *choice* of which spot on the object to record. Different choices give different coordinates for the same asset, and they are **not interchangeable**. Consider one streetlight:

| Candidate meaning of the point | Typical use | Consequence of confusing it with another |
| --- | --- | --- |
| Centre of the pole base at ground level | Maintenance crews, "which asset is this?" | Metres apart from the lamp head if the arm is long |
| Position of the lamp head | Lighting-coverage analysis | Not where a vehicle should stop |
| Entrance or access point (for a building or enclosure) | Navigation | Can be tens of metres from the object's centroid |
| Centroid of the footprint (for an area object) | Labelling, counting | May fall outside an L-shaped building |
| Reported incident position | Service requests | Is the *reporter's* estimate, not the asset |

**Table 8.3 — Point meaning per asset type in the fixture (synthetic policy).**

| Asset type | The point is | Not |
| --- | --- | --- |
| Streetlight (SL) | Centre of the pole base at ground level | The lamp head, the switch box |
| Drain (DR) | Centre of the grating / inlet opening | The chamber below, the outfall |
| Tree (TR) | Centre of the trunk at ground level | The canopy centre |

**Worked example.** In Chapter 1 (comprehension check 1.4), request P1 at (200, 200) and asset SL-0113 at (205, 195) were kept as separate records even though they describe "the same streetlight problem". On the training grid they are 5 m apart in x and 5 m apart in y, a straight-line separation of √(5² + 5²) = √50 ≈ **7.07 m** (planar arithmetic; hand-checked). That is entirely consistent with a reporter standing near a pole and a surveyor measuring the pole base — *because* the two points mean different things. A schema that stored both under one column called `location` with no statement of meaning would invite someone to "correct" one to match the other.

**Developer analogy (and where it stops).** Point meaning is like the unit and reference of a timestamp: `2026-03-14T10:42` is useless until you know whether it is IST or UTC and whether it is the visit time or the entry time (module 8.3.3). The analogy holds for the principle "a value without its convention is ambiguous". It stops because there is no automatic conversion between point meanings: you can convert IST to UTC exactly, but you cannot compute the lamp head position from the pole base without knowing the arm length and direction, which are not in the data.

### 8.2.3 Measured or approximate, and what the geometry cannot tell you

Two locations that look identical in a table can differ enormously in reliability. The schema should record **how** the location was obtained, using a small controlled list (module 8.6 explains such lists formally):

| `location_method` code | Meaning | Typical positional quality |
| --- | --- | --- |
| `SURVEY` | Measured by a surveyor with survey-grade equipment | Best; a stated accuracy figure may exist |
| `GNSS` | Consumer or field-app satellite positioning | Metres; depends on device and sky view |
| `DIGITISED` | Clicked on a map or aerial image | Depends on map scale and image alignment (Chapter 9 covers georeferencing) |
| `APPROX` | Estimated from a description ("near the temple") | Tens of metres or worse |

Recording the method is a **general principle** (Chapter 7.6 asked for it in the intake form); the *accuracy figures* are not general and must never be invented. The fixture's coordinates are synthetic and carry no accuracy claim at all — the correct value for every fixture asset is a documented statement that the positions are schematic.

**What the geometry does not establish.** A point tells you where; it does not tell you *what* is there now, *when* the position was captured, or whether the asset still exists. A polygon ward boundary tells you the declared area; it does not tell you whether the declaration is current (Chapter 7.6's stale-boundary scenario). These limits belong in the design document under the geometry specification, so that nobody reads precision into the coordinates that the capture method cannot support.

**Reuse, do not repeat.** Everything about CRS choice, units, degrees versus metres, and transformation belongs to Chapters 5–6 and is not re-taught here. The Chapter 8 obligation is only that the design *states* the CRS, the units, and the coordinate order, and that every spatial table in one design uses the same statement unless a documented reason exists.

**Misconception.** *"If two datasets are both points in the same CRS, they can be merged into one table."* Only if their points *mean* the same thing and their rows have the same grain. Requests and assets in the fixture share a CRS and a geometry type, but a request point is a reporter's estimate of an incident and an asset point is a surveyed pole base. Merging them produces a table with mixed grain (8.1.2) and mixed point meaning, and the first spatial analysis on it (Chapter 11) will silently treat estimates as measurements.

**Comprehension check 8.2.** Write the six-part geometry specification (8.2.1) for the **Request** entity in the fixture, including what the point represents and what should be recorded about how it was obtained.

## 8.3 Design attribute types and meaning

### 8.3.1 The data dictionary

A **data dictionary** is the table that describes your tables: one row per field, stating what the field means and how it may be filled. It is the single most useful document a GIS team can hand to a developer, and the one most often missing. In this course a dictionary row has seven parts:

| Part | What to write | Why a programmer needs it |
| --- | --- | --- |
| **Field name** | The stored name: short, no spaces, starts with a letter, no reserved words (Esri states these rules for geodatabase fields [X16]) | It is what queries and code use |
| **Description** | One sentence saying what the value *means* (not just what it is called) | Removes guesswork; distinguishes `visited_at` from `recorded_at` |
| **Type** | The storage type and size: text with a length, integer with a range, decimal, date/time | Determines what can be stored without loss (Chapter 7) and what arithmetic is valid |
| **Units** | Metres, watts, years, or "none"; for dates, the time-zone convention (8.3.3) | Prevents the "22 ft" problem in the lab |
| **Null policy** | Whether null is allowed and what null *means* (8.3.2); whether a default exists (8.6.2) | Tells code what to do when the value is missing |
| **Example** | One realistic value from the fixture | Anchors the description |
| **Source** | Who or what supplies the value: surveyor, field app, derived by a query, imported from the asset register | Tells you where to look when it is wrong |

**Developer-friendly type comparison.** GIS software uses its own names for storage types. The ArcGIS names (documented in the "ArcGIS field data types" page [X01]) map onto familiar concepts; QGIS and databases use their own words, and the mapping across systems is only approximate — always check the target system before assuming a value will fit.

| Concept a developer knows | ArcGIS geodatabase type [X01] | Notes for the designer |
| --- | --- | --- |
| 16-bit integer | Short integer — "−32,768 to 32,767" | Fine for counts, years, condition scores |
| 32-bit integer | Long integer — about ±2.14 billion | Default for whole-number keys |
| 64-bit integer | Big integer (64-bit) | Only where values exceed the long range; check downstream support |
| `float` (single) | Float — "can precisely store numbers that contain up to six digits only" | Rarely the right choice for measurements |
| `double` | Double — "up to 15 digits" | Default for measurements such as `pole_height_m` |
| `varchar(n)` | Text, with a length you set | Choose a length; codes are text (8.3.3) |
| `datetime` without zone | Date — "date and time values" with second precision by default, and no stored time zone; values must be "either all in UTC or all within the same local time zone" | The convention is *yours* to state (8.3.3) |
| `date` | Date only | No time part |
| `time` | Time only — "00:00:00 - 23:59:59" | No date part |
| `datetime` with offset | Timestamp offset — "date, time, and offset from ... UTC" | Carries the offset; the page notes it is "not a fully time zone-aware data structure" |
| UUID | GUID (you populate it) or GlobalID (the geodatabase populates it) | Module 8.4 |
| Auto-increment row id | ObjectID — "a unique integer field that cannot have null values", "maintained by ArcGIS" | Module 8.4 |
| Binary | BLOB | Used internally by attachments (8.7.2); rarely designed by hand |
| — | Geometry — one per feature class | Module 8.2 |

**Worked example — three rows of the Inspection dictionary (synthetic).**

| Field name | Description | Type | Units | Null policy | Example | Source |
| --- | --- | --- | --- | --- | --- | --- |
| `inspection_id` | Stable business identifier of one visit; never reused | Text(8) | none | Not null; unique | `INS-0003` | Assigned by the inspection app from a single sequence at entry time |
| `condition_code` | Observed overall condition on the 1–5 scale (Table F8-2) | Short integer | scale 1–5 | Null allowed; null = *not assessed on this visit* | `3` | Inspector's judgement on site |
| `defects_found` | Number of defects counted against the checklist for this asset type | Short integer | count | Null allowed; null = *checklist not completed*; `0` = *completed and none found* | `1` | Inspector's checklist |

The two null policies are different sentences on purpose. That is the subject of the next section.

### 8.3.2 Unknown, not applicable, zero, and empty text are four different facts

Programmers already know that `NULL`, `0`, and `''` are different values. What data modeling adds is that **null itself has more than one meaning**, and a schema that does not say which one is meant will be misread. Four cases recur:

| Fact | Meaning | Typical storage | The danger |
| --- | --- | --- | --- |
| **Unknown** | The value exists in the world but nobody has recorded it | Null | Being read as "none" or "zero" |
| **Not applicable** | The question makes no sense for this row | Null — or, better, the field is not on this row's table/subtype at all | Being read as "unknown, go and find out" |
| **Zero** | Measured, and the answer is nothing | `0` | Being read as "not recorded" when someone loads data with nulls turned into zeros (Chapter 7 [S20]: a shapefile export turns some numeric nulls into `0`) |
| **Empty text** | Deliberately blank text | `''` — or null, but the policy must choose one | Two representations of "no remark" that a query treats differently |

**Worked example — one field, four operational decisions (synthetic).** Take the field `lamp_wattage_w` (watts) and `defects_found` (count) across four fixture assets:

| Asset | `lamp_wattage_w` | What it means | Operational decision it drives |
| --- | --- | --- | --- |
| SL-0113 | `70` | Known: 70 W lamp | Order a 70 W replacement |
| SL-0127 (new in the lab) | null, meaning *unknown* | Plate never read | Send someone to read the plate **before** ordering |
| DR-0042 | null, meaning *not applicable* (a drain has no lamp) | — | Exclude from the lamp programme entirely; never send anyone to "find out" |
| SL-0250 (new in the lab), decommissioned | `0` | Known: no lamp is fitted | Do not order; do not send a crew |

And for `defects_found` on the two SL-0113 inspections in Table F8-2: `0` on 2025-09-14 (checklist completed, nothing found, visit closed) and `1` on 2026-03-14 (a work order is raised for the flicker). Had the first value been null, the correct action would have been "the checklist was not completed — re-inspect", the opposite of "closed".

If unknown and not applicable share the same null in the same column, the lamp programme cannot tell SL-0127 from DR-0042 and will either waste a site visit on a drain or never discover SL-0127's wattage. The cure is structural, not a comment: either (a) put type-specific fields on a type-specific table related 1:1 to the asset (module 8.5), or (b) use a subtype/type code so that a rule can say "`lamp_wattage_w` is required when `asset_type = 'SL'` and must be null otherwise" (module 8.6.3). Either way the dictionary states the meaning of null for that field.

**Developer analogy (and where it stops).** This is `Optional<T>` versus `T` with a sentinel, and the "not applicable" case is a type-system problem: a `Drain` type simply should not have a `wattage` member. The analogy is exact for the reasoning. It stops at the storage: many GIS containers give you flat tables and no sum types, so the "type" has to be expressed as a code field plus a documented rule, and the rule is enforced only where the platform enforces it (8.6.3).

**Misconception.** *"Defaulting numeric fields to 0 avoids null-handling bugs."* It converts a visible missing value into an invisible wrong value. With `defects_found` defaulted to 0, every un-inspected asset reports "no defects", and a "percentage of assets with defects" statistic becomes optimistic by exactly the number of visits that were never completed. Chapter 4 taught the same lesson for rasters: NoData is not zero.

### 8.3.3 Codes are text; timestamps need a stated convention

**Business codes are text.** A value on which you will never do arithmetic is text, even when it contains only digits. Three reasons:

1. **Leading zeros matter.** `0042` in `DR-0042`, a ward code `07`, or a pin code stored as an integer loses its zeros, and `42 ≠ 0042` to a matching routine. Chapter 7 met this in coordinate CSVs; the schema is where it is prevented.
2. **Arithmetic is meaningless.** Adding two asset numbers, or averaging condition *category codes*, produces a number with no meaning. Storing them as text makes the mistake harder.
3. **Identifiers combine letters and digits.** `SL-0113` cannot be an integer at all; making *some* IDs integer and others text creates two identifier systems.

The exception is a genuinely ordinal or numeric measurement (the condition score 1–5 is a *scale*, so a short integer with a range domain is appropriate; module 8.6.1). The dictionary says which is which.

**Timestamps: two questions and one convention per storage system.** Chapter 7 showed that a shapefile date field "only support[s] date; they do not support time" [S20]. Even where time is supported, two questions must be answered in the dictionary:

- **Which moment?** The time the *event* happened (the visit, the report) or the time the *record was written* (module 8.7.1 treats these as separate fields).
- **In which time zone, and does the storage remember it?**

The answer to the second question depends on the storage system, and the blueprint is right to insist that no single behaviour be assumed. Verified statements for three systems:

| Storage | What the documentation says | Consequence for the design |
| --- | --- | --- |
| ArcGIS geodatabase `Date` field | Stores date and time; holds no time zone; values must be "either all in UTC or all within the same local time zone" [X01] | *You* choose the convention and write it in the dictionary; nothing in the data enforces it |
| ArcGIS geodatabase `Timestamp offset` field | Stores "date, time, and offset from ... UTC"; "not a fully time zone-aware data structure" [X01] | Offset travels with the value; the zone name (IST) does not |
| ArcGIS Online hosted feature layer, date field | "Date values in hosted feature layers are stored in coordinated universal time (abbreviated UTC)"; when publishing local-time data "you must specify that the date values are in the local time zone"; on display "the date is converted from UTC to your local time or in the time zone offset you chose when you published the layer"; in SQL date calculations "you must provide the time in UTC" [X13] | Storage is UTC whether you like it or not; the publishing step is where a wrong convention becomes a wrong stored value |
| PostgreSQL `timestamp with time zone` | Input with an explicit zone "will be converted to UTC"; on output "it is always converted from UTC to the current `timezone` zone" [X24] | Same UTC-inside pattern as hosted layers |
| PostgreSQL `timestamp without time zone` | "PostgreSQL will silently ignore any time zone indication" in the input [X24] | Behaves like the ArcGIS `Date` field: convention is yours |

**The course convention for the fixture (synthetic policy):** event times are recorded in the field in IST (UTC+05:30) and **stored as UTC**; the dictionary says so; displays convert back to IST. The worked conversion for Table F8-2, hand-checked:

| Inspection | Field notebook (IST, UTC+05:30) | Stored value (UTC) | Check |
| --- | --- | --- | --- |
| INS-0001 | 2024-10-15 09:50 | 2024-10-15 04:20 | 09:50 − 5:30 = 04:20 |
| INS-0002 | 2025-11-02 15:05 | 2025-11-02 09:35 | 15:05 − 5:30 = 09:35 |
| INS-0003 | 2026-03-14 10:42 | 2026-03-14 05:12 | 10:42 − 5:30 = 05:12 |
| INS-0004 | 2025-09-14 11:15 | 2025-09-14 05:45 | 11:15 − 5:30 = 05:45 |

Chapter 7's Table F7 wrote the same two moments as `2026-03-14T10:42:00+05:30` and `2025-11-02T15:05:00+05:30` — an ISO 8601 text form that *carries* its offset. The stored UTC values above are what those strings become in a system that normalises to UTC; a system that stores a plain `Date` receives whichever of the two you give it, with no record of which.

An edge case worth writing into the dictionary: an IST time before 05:30 converts to the **previous day** in UTC (for example 2026-03-14 02:00 IST = 2026-03-13 20:30 UTC). A report that counts "visits on 14 March" must decide which day it means — the local day is almost always the business meaning, so the query must convert, not compare raw stored dates.

**Platform note.** The *need* for a stated convention is general. *Which* convention is enforced is platform behaviour: ArcGIS Online and PostgreSQL `timestamptz` normalise to UTC for you; a geodatabase `Date` or a PostgreSQL `timestamp` does not. Moving data between these (Chapter 7's conversion check) is exactly where an unstated convention shifts every value by five and a half hours.

**Misconception.** *"The database stores the time; the app can show it in any zone later."* True only when the storage remembers the zone or the convention is documented and applied consistently. A `Date` field filled by two crews, one entering IST and one UTC, is unrecoverable: the values look identical and nothing in the data says which is which.

**Comprehension check 8.3.** The lab's poor table has a column `Height` with the values `7`, `22 ft`, `12 m`, `6.5`, and `0`. Write the dictionary row that replaces it (all seven parts), and say what each of the five original values becomes under your null policy — including which ones you cannot convert without going back to the source.

## 8.4 Design identifiers

### 8.4.1 Three things that look like an ID and are not the same

Every row in an operational GIS table ends up with at least three "names", and a design that confuses them fails in a predictable way. Chapter 3.4 warned that a display label is not automatically the business identifier; this module completes the picture.

| Kind | Purpose | Who assigns it | Stable? | Human-readable? | Example (fixture) |
| --- | --- | --- | --- | --- | --- |
| **Business identifier** | The name by which the organisation refers to the thing in every system, report, and conversation | The organisation, by a written rule | Must be — for the life of the thing | Yes, by design | `SL-0113` |
| **Display label** | What a map, pop-up, or list shows to a person | Anyone; often derived from other fields | No — can change with a re-word or a translation | Yes | "Streetlight near Ward A market" |
| **Storage row identifier** | What the storage engine uses to find, select, and update a row | The storage engine, automatically | Only inside that one dataset instance (8.4.2) | No | ObjectID `17`; GlobalID `{6F1A…}` |

**Why three and not one.** They change for different reasons. A label changes when someone decides "near Ward A market" is clearer than "opposite shop no. 12". A storage id changes when the data is copied to a new container. A business id must change for neither reason, because the maintenance history, the photos, the requests, and the paper records all point at it. Using a label as a key means the first re-wording orphans every related record (8.7.2). Using a storage id as a key means the first export or republication does the same.

**Worked example — what goes wrong with a name as a key.** The lab's poor table (8.8) contains two different streetlights both called "Streetlight near Ward A market". Inspections recorded against that name cannot be assigned to either pole. There is no query that can repair this; someone has to visit the site with the photos. The cost of a missing business identifier is paid in the field, not in the database.

**Developer analogy (and where it stops).** Business id = natural key agreed with the domain (an order number); storage id = surrogate primary key (`SERIAL`); label = the `toString()` output. Developers often use the surrogate as the only key and expose it in URLs. The analogy stops at *portability*: in an application you control the database for its whole life, so the surrogate is stable. In GIS the same dataset is routinely exported to a shapefile for a contractor, copied into a file geodatabase for a field project, published as a hosted layer, and re-imported — each copy is a new instance with its own surrogates. That is why GIS needs the business identifier *on the row*, not only in the application layer.

### 8.4.2 ArcGIS ObjectID and GlobalID — what they are, and what they do not promise

**Platform note.** Everything in this section is ArcGIS-specific behaviour, stated from the official pages read on the check date.

**ObjectID.** When a geodatabase table or feature class is created, ArcGIS adds an object identifier: "a unique integer field that cannot have null values", which "guarantees a unique ID for each row in the table" and is "maintained by ArcGIS" [X01]. You will see it under aliases such as `OBJECTID`, `OID`, or `FID`. It is a storage row identifier in the sense of Table 8.4: it is what selection, scrolling, and editing use internally, and you cannot calculate or edit it.

What the documentation **does not** say is that an ObjectID survives copying. Two verified facts show why you must not assume it does:

- For a database table or view that ArcGIS reads as a *query layer* without a suitable integer key, "ArcGIS adds an attribute called ESRI_OID and stores a unique integer value in it", and this generated attribute "is only part of the layer definition; the underlying database table is not altered" [X02]. In other words, an ObjectID can be a *run-time* number that exists only while the layer is open. The same page warns that ArcGIS "does not enforce the uniqueness of values in the unique identifier field used in a query layer" and that non-unique values give "inconsistent results in selection sets" [X02].
- A copy, export, or append creates rows in a *new* table whose ObjectIDs are assigned by that table. The documentation describes the ObjectID as maintained per table; it makes no promise that the numbers match the source. **Course rule:** treat an ObjectID as valid only inside the dataset instance you read it from, and never store it in another table as a foreign key that must survive a copy. (Instructor Appendix I.7 lists this as a verification item for any workflow that wants to rely on more.)

**GlobalID and GUID.** A GlobalID is a 128-bit identifier "automatically assigned and managed by a geodatabase when a row or feature is created" that gives each row "a unique fingerprint that cannot be changed" [X01]; the geodatabase "will automatically generate and maintain these global ID values" [X01]. A GUID field holds the same kind of 36-character value but "must be manually populated and maintained" by you [X01]. The Add Global IDs tool adds a GlobalID field to geodatabase feature classes, tables, and feature datasets, and is available at all licence levels [X03].

GlobalIDs are the identifier ArcGIS uses for its own bookkeeping — the field-types page lists relationships, versioning, change-only updates, and replication as their uses [X01] — and two platform features depend on them: attribute rules ("the dataset must have GlobalIDs" [X19]) and offline synchronisation, which "relies on unchanging unique IDs to identify matching rows in the source and the offline version of a layer"; ArcGIS Online adds "a global ID field ... to all layers in the hosted feature layer or table when you enable synchronization" [X14].

Two things a GlobalID **does not** promise, both stated by the documentation:

1. **It is not preserved by default when rows are appended.** The *Preserve Global IDs* environment exists precisely because the default is the opposite: without it, "The operation will not preserve the Global IDs of features and will instead create new IDs. This is the default." For the Append tool the setting "only applies to enterprise geodatabase data and will only work on data that has a Global ID field with a unique index" [X04]. So a row's "unchangeable fingerprint" is unchangeable *within its table*; a copy of the row in another table may receive a different one unless the workflow explicitly preserves it.
2. **Having a GlobalID does not, by itself, configure synchronisation or any other capability.** Sync is a layer setting that must be enabled ("Enable sync (required for offline use and collaboration)" [X14]); the GlobalID is a *prerequisite* that the platform adds when you enable it, not the enabling act. The same holds for attachments (8.7.2) and attribute rules [X19]: the GlobalID is required, and a separate configuration step does the work.

**Where this leaves the design.** For the fixture: every table gets a business identifier as the key that other tables and people use (`asset_id`, `inspection_id`, `team_id`, `request_id`, `ward_code`). ObjectID exists because ArcGIS creates it, and nothing in the design refers to it. GlobalID is added to every table because platform features will need it, and the design notes *which* workflows must preserve it and *which* have not yet been verified to do so.

**Verification item (instructor).** Before promising any ObjectID or GlobalID behaviour in a production procedure — export to file geodatabase, Append, publishing to ArcGIS Online, downloading and re-uploading — run that exact workflow in the installed version on a throwaway copy and compare identifiers before and after. The documentation read for this chapter supports the statements above and nothing stronger.

### 8.4.3 An identity policy: imports, duplicates, and related records

An **identity policy** is a short written answer to three questions. Without it, every import is a fresh argument.

1. **On import, how do we decide whether an incoming row is a new thing or an existing thing?** *Fixture policy:* match on `asset_id`. An incoming row with an `asset_id` that already exists is an *update candidate* and is reviewed field by field; one without an `asset_id` is rejected until an ID is assigned — never given a provisional ID by the importer. Matching on coordinates alone is forbidden: 8.2.2 showed that two points 7 m apart can be the same asset seen by two methods, or two assets.
2. **How are duplicates detected and resolved?** *Fixture policy:* a duplicate is two rows with the same `asset_id`, or two rows of the same `asset_type` within 2 m of each other on the training grid flagged for *human* review (never auto-merged). Resolution keeps the row with the older `asset_id` assignment date, moves all related inspection and request rows to it (8.5), and marks the other row `status = 'MERGED'` with a `merged_into` reference — it is not deleted, because deletion destroys the audit trail (8.7).
3. **Which key do related records carry?** *Fixture policy:* the business identifier (`asset_id` on inspections and requests; `inspection_id` on photos). Related records never carry an ObjectID. A GlobalID may additionally be carried where the platform requires it (attachments in ArcGIS, 8.7.2), but the business identifier remains the key that humans and other systems use.

**Schema exercise (do this now; answers in I.1).** Table F3 in Chapter 1 marked request P6 "Closed – duplicate" of "a report not in this package". Extend the Request table with the field(s) needed to record that link *without* deleting P6, and state what happens to the link if the referenced report is later merged into a third one. Keep to fields and keys; how a *service* behaves when rows are edited is Chapter 9 and later.

**Misconception.** *"A UUID column is a synchronisation solution."* A UUID is an identifier; synchronisation is a workflow that needs identifiers plus change tracking plus a configured capability. The documentation is explicit that sync is a setting you enable, and that the platform adds the GlobalID for you when you do [X14]. Adding the column yourself does nothing until the capability is configured.

**Comprehension check 8.4.** A dataset is exported to a shapefile for a contractor, who adds inspection results in a spreadsheet keyed on the shapefile's `FID` column and returns it. Explain, using the three kinds of identifier, why the returned results may not attach to the right assets, and what one change to the export would have prevented it.

## 8.5 Model relationships and cardinality

### 8.5.1 One-to-one, one-to-many, many-to-many — with keys you already know

A **relationship** is a statement that rows in one table refer to rows in another. Its **cardinality** says how many rows on each side may take part. You know the mechanics — a primary key on one side, a foreign key on the other — so this section concentrates on *which* cardinality the municipal facts require, using original examples from the fixture.

**One-to-many (1:M) — the workhorse.** One asset has many inspections; each inspection is about exactly one asset. The foreign key goes on the *many* side.

```
Asset (1) ────────────< Inspection (M)
asset_id  ◄──────────  asset_id (foreign key, not null)
```

Similarly, one team performs many inspections (`team_id` on Inspection), and one ward contains many assets (if the design chooses to *store* `ward_code` on Asset rather than derive it spatially; 8.5.2 discusses that choice).

**One-to-one (1:1) — rarer, and used to separate type-specific facts.** Module 8.3.2 showed that `lamp_wattage_w` is *not applicable* to drains. One clean solution is a table `StreetlightDetail` holding fields that exist only for streetlights (`lamp_wattage_w`, `lamp_type`, `arm_length_m`), related 1:1 to the Asset row via `asset_id`. A drain simply has no `StreetlightDetail` row, and "not applicable" is now structural rather than a null with a footnote. The foreign key sits on the detail side and is also unique there — that uniqueness is what makes the relationship one-to-one rather than one-to-many.

**Many-to-many (M:N) — needs a third table.** A team covers several wards, and a ward is covered by several teams (North crew covers Ward A on weekdays; South crew covers Wards A and B on weekends). Neither table can hold the other's key without repeating rows. The standard solution is a **junction table** (also called a link, bridge, or intermediate table), one row per (team, ward) pairing, optionally with attributes of the pairing itself:

```
Team (1) ──< TeamWardCoverage (M) >── (1) Ward
team_id       team_id + ward_code           ward_code
              from_date, to_date
```

The junction row is the only place where "North crew covers Ward A from 2026-01-01" can live, because that fact belongs to the *pair*, not to the team or the ward alone.

**Table 8.5 — Cardinality decision guide (general principle).**

| If the business fact is… | Cardinality | Where the key goes | Fixture example |
| --- | --- | --- | --- |
| "Each B belongs to exactly one A; an A can have many Bs" | 1:M | Foreign key on B, not null | Asset → Inspection |
| "Each B may belong to one A or none" | 1:M optional | Foreign key on B, nullable | Asset → Request (a request may not match any asset) |
| "Each A has at most one B, and the B is about that A only" | 1:1 | Foreign key on B, unique | Asset → StreetlightDetail |
| "An A relates to many Bs and a B to many As" | M:N | Junction table with both keys | Team ↔ Ward |

**Developer analogy (and where it stops).** Identical to relational modeling; an ER diagram is an ER diagram. It stops when the platform *implements* the relationship: a database enforces a foreign key at commit time; an ArcGIS relationship class is a stored dataset with its own rules and behaviours; a QGIS relation is a project setting used to build forms; and a join is a temporary lookup. 8.5.3 separates these.

### 8.5.2 One asset, many inspections — without repeating the asset

The blueprint's specific requirement is to model repeated inspections without copying asset facts into every observation. The two-table design does this:

**Figure 8.1 — Entity–relationship diagram for the Chapter 8 fixture (schematic; crow's-foot notation, keys underlined in the dictionary).**

```
                    ┌──────────────┐
                    │    Ward      │  polygon
                    │  ward_code   │
                    └──────┬───────┘
                           │ 1
                           │        covers (M:N)
                    ┌──────┴───────────┐        ┌──────────────┐
                    │ TeamWardCoverage │ M    1 │    Team      │
                    │ team_id,ward_code├────────┤  team_id     │  no geometry
                    │ from_date,to_date│        └──────┬───────┘
                    └──────────────────┘               │ 1
                                                       │ performs
   ┌──────────────┐ 1                M ┌───────────────┴──────┐ 1        M ┌──────────────────┐
   │    Asset     ├────────────────────┤     Inspection       ├────────────┤ InspectionPhoto  │
   │  asset_id    │  is inspected in   │  inspection_id       │  has       │  photo_id        │
   │  point       │                    │  asset_id  (FK)      │            │  inspection_id FK│
   └──────┬───────┘                    │  team_id   (FK)      │            └──────────────────┘
          │ 1                          │  visited_at, ...     │
          │ optional                   └──────────────────────┘
          │ 0..M
   ┌──────┴───────┐        ┌────────────────────┐
   │   Request    │        │ StreetlightDetail  │ 1:1 with Asset (asset_id FK, unique)
   │  request_id  │ point  │ asset_id (FK,uniq) │
   │  asset_id FK │        │ lamp_wattage_w ... │
   └──────────────┘        └────────────────────┘
```

Read it as sentences: an asset *is inspected in* zero or more inspections; an inspection *has* zero or more photos; a team *performs* many inspections; a team *covers* many wards through the coverage table; a request *may refer to* one asset; a streetlight asset *has* one detail row.

**What is stored where, and why.**

| Fact | Stored on | Not stored on | Reason |
| --- | --- | --- | --- |
| Asset location | Asset | Inspection | One location, one place; an inspection *at* the asset inherits it through `asset_id` |
| Asset type, install year | Asset | Inspection | Slowly changing asset facts; copying them into every visit means an error must be fixed in N places |
| Condition observed on a visit | Inspection | Asset (except as a derived cache, 8.7.1) | It is a fact about the *visit* |
| Team that visited | Inspection (`team_id`) | Asset | Different visits, different teams |
| Ward an asset is in | Derived spatially, or stored on Asset as `ward_code` if the business assignment can differ from geometry | Inspection | Store only if the business meaning differs from "which polygon contains the point"; P5 in Chapter 1 sat on a shared boundary, and a stored assignment is how such a case is resolved by policy |

**Worked example — Table F8-2 as related rows (synthetic).** With the design above, SL-0113's two visits are two Inspection rows sharing `asset_id = 'SL-0113'`; the asset row is untouched by either visit except for the derived cache fields. DR-0042's two visits are two more rows. TR-0301 has no Inspection rows at all — which is a *query result* ("assets with no inspections") rather than a special value on the asset. Counting rows: 3 assets, 4 inspections, 2 teams, and the relationships resolve because every `asset_id` and `team_id` on an Inspection row exists on the parent table. Instructor Appendix I.3 tabulates this as the lab's expected result.

**Misconception.** *"Join the tables once and store the joined result as the operational dataset — it is faster to query."* The joined table has mixed grain (one row per inspection, each carrying a copy of the asset) and cannot represent an asset with zero inspections without a null-filled row. It is a fine *output* for a report (Chapter 11's spatial joins produce exactly such outputs), and a bad *source*.

### 8.5.3 A conceptual relationship, a join, and an implemented relationship are three different things

The ER diagram states what is *true*. Several mechanisms can make software *act* on it, and they enforce different amounts of it. Confusing them is how a design "with relationships" ends up with orphaned rows.

| Mechanism | What it is | Where it lives | What it enforces | Verified statement |
| --- | --- | --- | --- | --- |
| **Conceptual relationship** | The diagram and the sentence "each inspection belongs to one asset" | The design document | Nothing by itself | General principle |
| **Join** (ArcGIS, QGIS) | A temporary lookup that appends matching fields from another table for display or query | The layer or map, not the data | Nothing; rows without a match are simply unmatched | ArcGIS: a join is "a temporary table association", "stored in the layer's properties and persists temporarily inside the one map it was created in" [X08]. QGIS: a join is a layer property "in a one-to-one relationship"; "If the join field contains duplicate matching values, only the first fetched feature is picked" [X21] |
| **Relate** (ArcGIS) | A stored *link* used to select related rows, without appending fields | The project or layer file; "only available as long as the project is open" [X08] | Nothing on the data | Platform behaviour [X08] |
| **QGIS relation** | A parent/child link declared for forms and selection | The QGIS project: relations "are project level settings" set in *Project ► Properties ► Relations* [X21] | In QGIS editing only; *Composition* strength cascades deletes and duplicates through QGIS ("on deleting a feature the children are deleted as well") [X21]; nothing outside QGIS sees it | Platform behaviour [X21] |
| **ArcGIS relationship class** | "a dataset type in the geodatabase that stores information about the relationship" — it "is physically stored and persists in the geodatabase" [X08] | The geodatabase, alongside the tables; "can only be defined between feature classes or tables in the same geodatabase" [X05] | Cardinality, and behaviour on edit: in a *simple* relationship class deleting an origin row leaves the destination row but "the foreign key field value for the matching destination object is set to <Null>"; in a *composite* one "the related destination objects are also deleted in a process called a cascade delete" [X06] | Platform behaviour [X05] [X06] [X08] |
| **Database foreign key** (PostgreSQL/PostGIS, and other SQL databases) | A declared constraint: values "must match the values appearing in some row of another table" — "referential integrity" [X22] | The database schema | Every insert/update/delete, regardless of client; `ON DELETE` may be `NO ACTION`, `RESTRICT`, `CASCADE`, or `SET NULL` [X22] | Database behaviour [X22] |

Three consequences follow for the designer:

1. **Storage format and tooling decide which rules are enforced.** The same ER diagram implemented in a shapefile enforces nothing (a shapefile has no relationship datasets and loses domains and subtypes on conversion [S20]); in a QGIS project it is enforced only while editing in QGIS; in a geodatabase it is enforced by ArcGIS clients that honour relationship classes; in PostgreSQL it is enforced by the database for every client. The design document must say which enforcement it *expects* and which it *only assumes* (module 8.6.3 turns this into a checklist).
2. **A join is not a relationship.** It is a read-time convenience. A one-to-many relationship viewed through a join either repeats the "one" side or drops all but the first match — and the two GIS applications behave differently, so the result is not even portable between them [X08] [X21].
3. **Relationship classes have their own vocabulary.** Esri calls the "one" side the **origin** and the "many" side the **destination**; both tables must share "a field ... that shares common values and are the same data type", known as the primary key in the origin and the foreign key in the destination [X05]. Cardinality may be 1:1, 1:M, or M:N; an M:N (or any *attributed*) relationship class uses "an intermediate table that stores and provides additional information about the relationships", where "Each row associates one origin object with one destination object" [X07] — precisely the junction table of 8.5.1, created and maintained by the geodatabase. A *composite* relationship class "always ha[s] one-to-many cardinality when you create them but can be constrained to be one-to-one with relationship rules" [X06].

**Which should the fixture use?** *Conceptually:* Asset→Inspection is 1:M; Inspection→InspectionPhoto is 1:M and the photo has no meaning without its inspection (a candidate for composite/cascade behaviour); Team↔Ward is M:N. *Implementation* is deferred: the lab asks for the relationship *definitions* (participants, keys, cardinality, and the delete behaviour the business wants); the Instructor Appendix's optional procedure shows one way to implement them in ArcGIS Pro and one in QGIS, both untested. Deciding whether cascade delete is *wanted* is a business question — does deleting a decommissioned asset row really mean its inspection history should vanish? Module 8.7 argues that it usually does not, and that "delete" is rarely the right operation for an asset anyway.

**Comprehension check 8.5.** The requests table has an optional `asset_id`. A colleague joins Requests to Assets in a map and reports "the join lost three requests". Explain what actually happened, why the three rows are not lost, and which mechanism from the table above would have made the optional relationship explicit.

## 8.6 Introduce controlled values and validation rules

### 8.6.1 Coded-value and range domains: stored code, displayed description

A **controlled value list** is a fixed set of permitted values for a field. Every platform has some version of it; ArcGIS calls the mechanism an **attribute domain**: "rules that describe the available values of a field type", which "provide a method for enforcing data integrity by limiting what can be placed on a field to a valid list or range of choices" [S22]. Two kinds exist, and they answer different questions.

**Coded-value domain — "which category?"** A list of allowed codes, each paired with a human description. The **code is what is stored**; the **description is what is displayed**. Esri's own example pairs the stored value "1" with the description "pavement" [S22]; "A coded value domain can apply to any attribute data type, be it text, numeric, date, and so forth" [S22].

**Table 8.6a — Coded-value domain `RequestStatus` for the fixture (synthetic).**

| Stored code (Text) | Displayed description | Meaning in the workflow |
| --- | --- | --- |
| `OPEN` | Open | Received, nobody assigned |
| `INPROG` | In progress | Crew assigned |
| `RESOLVED` | Resolved | Work done, awaiting closure |
| `CLOSED` | Closed | Verified and closed |
| `REOPENED` | Reopened | Reported again after closure |
| `DUP` | Closed – duplicate | Linked to another request (8.4.3) |

This is Chapter 1's Table F3 status column made rigorous. Table F3 used the free-text words "Open", "In progress", "Resolved", "Closed – duplicate", and "Reopened"; a coded domain guarantees that "In Progress", "in progress", and "In-progress" cannot coexist, and that a report can rename a description (say, to Gujarati) without touching a single stored row or query.

**Range domain — "within which bounds?"** A minimum and maximum for a numeric or date value: "A range domain specifies a valid range of values for a numeric or date attribute data type", applicable to "short integer, long integer, big integer, float, double, date, date-only and time-only field types" [S22].

**Table 8.6b — Range domains for the fixture (synthetic).**

| Domain | Field | Minimum | Maximum | Why these limits |
| --- | --- | --- | --- | --- |
| `ConditionScore` | `Inspection.condition_code` | 1 | 5 | The 1–5 scale in Table F8-2 |
| `PoleHeightM` | `Asset.pole_height_m` | 3 | 15 | Streetlight poles the municipality installs fall in this range; a `22` (feet, misread as metres) is rejected |
| `InstallYear` | `Asset.install_year` | 1950 | 2100 | Catches `18` (meaning 2018) and `0` |

A domain is defined *once* in the geodatabase and assigned to fields: "You can share attribute domains across feature classes, tables, and subtypes in a geodatabase" [S22]. That sharing is why `ConditionScore` can be used by the Inspection table today and by a future "condition at installation" field tomorrow without redefining the scale.

**Worked example — the same fact three ways.** The condition of SL-0113 on 2026-03-14 is:

| Representation | Value | Used for |
| --- | --- | --- |
| Stored code | `3` (short integer, `ConditionScore` range 1–5) | Queries: `condition_code <= 2` finds Poor and Very poor |
| Displayed description | "Fair" — supplied by a *coded* domain if the design prefers named categories to a range | Pop-ups, reports |
| Chapter 1's free text | "Fair" | Nothing — it cannot be relied on to match "fair" or "FAIR" |

The design chooses **either** a range domain on a numeric scale (arithmetic such as "average condition of drains" is then meaningful) **or** a coded domain with named categories (no arithmetic; safer for purely qualitative classes). It should not do both for the same field.

**Platform note — the same idea elsewhere.** QGIS has no stored domain of its own for a plain file, but its *Value Map* widget is the display-side twin: "A combo box with predefined items. The value is stored in the attribute, the description is shown in the combo box" [X20]; and QGIS reads existing domains from some containers — "Some layers, such as GeoPackage or ESRI File Geodatabase, with predefined coded Field Domains will be automatically recognized by QGIS and assigned a Value Map widget", and range domains are likewise mapped to a *Range* widget [X20]. In PostgreSQL the equivalent of a range or list is a `CHECK` constraint — "the value in a certain column must satisfy a Boolean (truth-value) expression" — or a foreign key to a lookup table [X22]. A shapefile has none of these: converting from a geodatabase loses "Subtypes, Attribute domains" [S20].

**Misconception.** *"Storing the description instead of the code is friendlier and avoids a lookup."* It also means the stored data changes every time the wording changes, every query embeds the wording, and a translation becomes a data migration. Store the code; display the description.

### 8.6.2 Defaults are not observations

A **default value** is what a new row receives for a field when nobody supplied a value. Defaults are convenient and dangerous in equal measure, because a default *looks exactly like* an observation.

**Worked example (synthetic).** Suppose `Inspection.condition_code` has the default `3` (Fair). A crew records a visit but never assesses condition — perhaps the asset was inaccessible. The row now says "Fair". Every report that counts "assets in fair condition" includes it; the assessment that never happened has become evidence. Compare the correct design: `condition_code` has **no default** and is nullable, with the dictionary meaning "null = not assessed on this visit". The unassessed visit now appears in "visits without an assessment", which is a work list, not a false statistic.

**The rule.** A default is appropriate when the value is *administratively true* for a new row and would otherwise be typed identically every time (for example `Request.status = 'OPEN'` on creation, or `Asset.status = 'ACTIVE'`). A default is inappropriate for any field whose value is supposed to be *observed* (`condition_code`, `defects_found`, `lamp_wattage_w`, `pole_height_m`). For observed fields, **null remains meaningful** and must stay allowed.

**A related trap — "Allow NULL" is hard to reverse.** In an ArcGIS geodatabase the *Allow NULL* property "Can only be set to false if the table is empty" [X17], so the decision to require a value must be made at design time, and likewise the data type "Can only be changed if the table is empty" [X17]. This is why the dictionary exists before the first row is loaded (8.7.3 lists more such one-way doors).

**Table 8.6c — Default policy for selected fixture fields (synthetic).**

| Field | Default? | Null allowed? | Meaning of null |
| --- | --- | --- | --- |
| `Request.status` | `OPEN` | No | — |
| `Asset.status` | `ACTIVE` | No | — |
| `Asset.location_method` | None | No | — (the capturer must state how the location was obtained) |
| `Inspection.condition_code` | None | Yes | Not assessed on this visit |
| `Inspection.defects_found` | None | Yes | Checklist not completed (`0` = completed, none found) |
| `Inspection.remarks` | None | Yes | No remark made; the empty string is never stored |
| `StreetlightDetail.lamp_wattage_w` | None | Yes | Unknown (the *not applicable* case cannot occur — non-streetlights have no detail row) |

QGIS's form designer offers the same choices — a *Default value* that "for new features, automatically populates by default the field with a predefined value or an expression-based one", including `now()` for creation time [X20] — and the same trap: an expression default such as the current time is an entry-time fact, not an event-time fact (8.7.1).

**Misconception.** *"A `NOT NULL` constraint on every field guarantees complete data."* It guarantees that every field *contains something*, which pushes people towards defaults and sentinel values (`0`, `-1`, `N/A`, `.`) that are worse than nulls because they hide in statistics. Require values only where a row without them is genuinely meaningless (`asset_id`, `visited_at`, `asset_type`, `location_method`).

### 8.6.3 Which rules belong in the schema, the application, or the review procedure?

Not every rule can — or should — be enforced by the storage. Three places exist, and the design must assign each rule to one of them explicitly, because each place enforces against a different set of actors.

| Layer | Enforces against | Strength | Examples for the fixture |
| --- | --- | --- | --- |
| **Schema** (domain, null policy, uniqueness, foreign key, geometry type) | Every client that honours the schema | Strong where the platform enforces it; silently absent where it does not (shapefile, CSV) | `condition_code` in 1–5; `asset_id` unique and not null; `Inspection.asset_id` must exist in Asset; Asset geometry is Point |
| **Application** (field app form, web app, import script) | Only users of *that* application | Strong for that path; a second application or a bulk load bypasses it | "`lamp_wattage_w` required when `asset_type = 'SL'`"; "a photo is mandatory when `defects_found > 0`"; "`visited_at` may not be in the future" |
| **Review procedure** (a query run weekly; a supervisor's sign-off) | Everything, after the fact | Detects, does not prevent | "Assets with no inspection in 12 months"; "inspections whose `asset_id` matches no asset" (8.7.2); "distinct `Height` unit strings" during an import |

**Why all three are needed.** The ArcGIS domain page states that once a domain is associated with a field, "the field will not accept a value that is not in that domain" [S22] — but a domain lives in a geodatabase, and the same data exported to CSV for a contractor has no domain at all; the rule must then be re-checked on return (review). Conversely, the cross-field rule "wattage required only for streetlights" is not expressible as a simple domain; in ArcGIS it needs either a subtype with per-subtype domains (the domain page notes that with subtypes "you can assign different attribute domains to each of the subtypes" [S22]) or an *attribute rule*, which is beyond this chapter's boundary (attribute rules are Arcade-scripted rules that require GlobalIDs [X19]; they belong to a later phase). Until then the rule lives in the application and the review.

**Platform note — the same rule, different enforcement.**

| Rule | ArcGIS geodatabase | QGIS (file/GeoPackage layer) | PostgreSQL/PostGIS | Shapefile |
| --- | --- | --- | --- | --- |
| Value in a fixed list | Coded-value domain [S22] | Value Map widget and/or an *expression* constraint; a constraint is either *soft* (yellow warning, "does not prevent you to save") or *hard* ("does not allow you to save your modifications until they meet the constraints", when *Enforce constraint* is checked) — and it applies only when editing in QGIS [X20] | `CHECK` constraint or foreign key to a lookup table [X22] | None [S20] |
| Number within bounds | Range domain [S22] | Range widget / expression constraint [X20] | `CHECK` [X22] | None |
| Value required | Allow NULL = false (empty table only) [X17] | *Not null* constraint, soft or hard [X20] | `NOT NULL` [X22] | Not representable: nulls become 0 or blank [S20] |
| Unique | Unique index (verify per platform); the design states it | *Unique* constraint [X20] | `UNIQUE` / `PRIMARY KEY` [X22] | None |
| Child must have a parent | Relationship class behaviour on edit [X06] (verify what your clients honour) | Relation, in QGIS editing only [X21] | `FOREIGN KEY` [X22] | None |

**Verification item (instructor).** Whether an ArcGIS *bulk* load (Append, a Python script, a CSV import into ArcGIS Online) rejects out-of-domain values, or writes them and leaves them to be found later, must be checked in the installed version before a lab or procedure promises either. The course's safe position is that every import is followed by a review query regardless.

**Trainee task (do it now; I.1 has a model answer).** For the six rules below, write the layer (schema / application / review) where you would put it for the fixture, and one sentence on what happens if that layer is bypassed:

1. `condition_code` must be 1–5.
2. Every inspection must name an existing asset.
3. A streetlight must have a wattage before a replacement lamp is ordered.
4. `visited_at` must not be later than `recorded_at`.
5. Two assets of the same type may not be within 2 m of each other.
6. A `DUP` request must point at another request.

**Comprehension check 8.6.** A supervisor sees that every drain inspected last month has `condition_code = 3` and concludes the drains are in fair condition. List two schema facts you would check before accepting the conclusion, and say what each would reveal.

## 8.7 Plan history, attachments, and maintenance

### 8.7.1 Current state versus history; event time versus entry time

**Current state and history are different tables.** The asset row answers "what is true now?"; the inspection table answers "what was observed, when?". Module 8.5.2 put the observations in their own rows. The remaining decision is what, if anything, the asset row *caches* from them.

**Table 8.7a — Current-state fields on Asset and their derivation (synthetic design).**

| Field on Asset | Derived from | Rule | Why cache it at all |
| --- | --- | --- | --- |
| `last_inspected_at` | Inspection | Maximum `visited_at` over this asset's inspections | Map symbology "not inspected in 12 months" needs it on the feature |
| `last_condition_code` | Inspection | `condition_code` of the inspection with the maximum `visited_at`, *excluding* null assessments | Same |
| `open_defects` | Inspection | `defects_found` of the latest completed checklist | Work planning |

Each cached field is marked *derived* in the dictionary, with the rule and the refresh method ("recomputed nightly by query Q-1" or "updated by the field app on save"). A derived field that nobody refreshes is a stale fact wearing the clothes of a current one; the dictionary's *source* column is where this is made visible.

**Worked example.** After INS-0003, SL-0113's cache reads `last_inspected_at = 2026-03-14 05:12 UTC` (10:42 IST), `last_condition_code = 3`, `open_defects = 1`. The history still shows that the lamp was sound in September 2025 (INS-0004: zero defects), so the flicker began somewhere in the intervening six months. Chapter 1's Table F4 could not have told you that; neither could Chapter 7's Table F7, whose `inspector_note` holds only the latest remark.

**Two times per event.** Every observation has an **event time** (when the visit happened; `visited_at`, from the notebook) and an **entry time** (when the row was written; `recorded_at`). They differ whenever a crew records visits at the end of the day, when a paper form is typed in later, or when an offline device syncs. INS-0004 in Table F8-2 is the fixture's example: visited 2025-09-14, entered 2026-03-16, and numbered after INS-0003 because numbering happens at entry. Both are kept, because they answer different questions: "which assets were visited in March?" uses event time; "which records were entered after the report was run?" uses entry time.

**Platform note — editor tracking.** ArcGIS can record entry-time facts automatically. *Editor tracking* records "who edits features and when edits are made to a feature class or table" in four fields — creator, creation date, last editor, last edit date [X25] — and the Enable Editor Tracking tool lets you choose whether "Dates will be recorded in UTC" (the default, recommended because "editors can apply edits from potentially anywhere in the world") or in the database's time zone, which the documentation recommends "only if you are certain that all edits will be performed in that time zone" [X12]. ArcGIS Online offers the equivalent setting, "Keep track of who edited the data (editor name, date and time)" [X14]. Two limits matter for the design: editor tracking records *who* and *when*, not *what* changed, and its dates are **entry times** — they can never replace `visited_at`. QGIS can fill an entry-time field with a `now()` default expression, and can recalculate it on update with *Apply default value on update* [X20]; the same limits apply.

**What is out of scope.** Keeping every *previous value* of every field (full change history, geodatabase archiving, versioning) is enterprise functionality outside Phase 1. The Phase 1 design captures history at the *event* grain (one row per visit) and entry-time metadata; it does not attempt to reconstruct the asset row as it was on a past date.

**Misconception.** *"The `created_date` field tells us when the inspection happened."* It tells you when the row was created. For a crew that types up the week's visits on Friday, every inspection "happened" on Friday. Event time must be its own field, captured from the source.

### 8.7.2 Photographs and documents are related records; orphans are found by query

A photograph belongs to *something* — a specific inspection, or a specific asset, or a specific request — and the design must say which. The wrong design is a text field on the parent holding `"sl113_a.jpg; sl113_b.jpg"`. It fails in four ways: you cannot store anything *about* an individual photo (who took it, when, of what); you cannot query "inspections with more than two photos" without string parsing; renaming or deleting one file means editing a string in the middle; and the separator is a convention nobody enforces (the lab's poor table uses `;` in one row and `,` in another). The right design is the one already in Figure 8.1: a table with one row per file, carrying a foreign key to its parent.

**Table 8.7b — InspectionPhoto dictionary (synthetic).**

| Field name | Description | Type | Null policy | Example | Source |
| --- | --- | --- | --- | --- | --- |
| `photo_id` | Business identifier of one photo record | Text(12) | Not null; unique | `PH-0003-1` | Field app |
| `inspection_id` | The inspection this photo documents | Text(8) | Not null; must exist in Inspection | `INS-0003` | Field app |
| `file_name` | Original file name as captured | Text(255) | Not null | `sl113_20260314_1.jpg` | Device |
| `content_type` | Media type | Text(50) | Not null | `image/jpeg` | Device |
| `taken_at` | Event time the photo was captured (UTC; 8.3.3) | Date | Null allowed = not available from the device | 2026-03-14 05:02 | Device metadata |
| `subject` | What the photo shows | Text, coded (`LAMP`, `POLE`, `GRATING`, `TRUNK`, `OTHER`) | Null allowed = not classified | `LAMP` | Inspector |

Whether the *bytes* of the file live in the same table (a BLOB), in a file store with a path, or in a platform-managed attachment store is an implementation choice that does not change the design: there is still one row per file with a key to its parent.

**Platform note — ArcGIS attachments.** ArcGIS implements exactly this pattern for you. "Attachments are used to associate copies of media files, such as documents and images, with features in a feature class or rows in a stand-alone table" [X11]. Enabling attachments "creates a stand-alone table to store the media that will be attached and a one-to-many relationship class to manage the association between the media and the feature class or table"; the table is named with the suffix `__ATTACH` and the relationship class `__ATTACHREL` [X09]. Attachments "are related to their assigned feature using a unique ID", and when enabling them the *Global IDs* option "is checked by default, and it is recommended that you leave it checked" [X09]. Feature services "allow you to query and edit attachments but, to use this feature, you must configure the datasets in the geodatabase to support attachments" [X15]. Two design consequences: the parent of an ArcGIS attachment is *whatever row you enabled attachments on* — so to attach photos to the *inspection* rather than the asset, attachments are enabled on the Inspection table; and the relationship is maintained by the geodatabase, which is convenient but means the user-facing `photo_id`/`subject` fields of Table 8.7b would have to live as extra fields on the attachment table or be dropped. Which is chosen is an implementation decision for Phase 2; the conceptual design is unchanged.

**Orphan detection.** An **orphan** is a child row whose foreign key matches no parent — an inspection for an `asset_id` that does not exist, a photo for an inspection that was deleted. Orphans arise from typos, from deletes in systems that do not cascade or restrict, and from imports that arrived in the wrong order. Whatever the platform, the detection is the same query shape, written here in standard SQL because every learner reads it; ArcGIS and QGIS expose the same logic through a join whose unmatched rows are then selected (Chapter 10 makes this a hands-on skill):

```sql
-- Inspections whose asset does not exist (expected result: zero rows)
SELECT i.inspection_id, i.asset_id
FROM   inspection i
LEFT JOIN asset a ON a.asset_id = i.asset_id
WHERE  a.asset_id IS NULL;

-- Photos whose inspection does not exist (expected result: zero rows)
SELECT p.photo_id, p.inspection_id
FROM   inspection_photo p
LEFT JOIN inspection i ON i.inspection_id = p.inspection_id
WHERE  i.inspection_id IS NULL;
```

The complementary check — *parents that should have children and do not* — is not an integrity error but a work list ("assets never inspected"), and is written the same way with the tables swapped. Both queries belong in the review layer of 8.6.3 and run on a schedule; in a database with foreign keys the first two should be *impossible* to violate ("`RESTRICT` ... prevents deletion of a referenced row" [X22]), and running them anyway is how you prove the constraint is actually in place.

**Worked example (synthetic).** In the lab's poor table, the two "Streetlight near Ward A market" rows each have inspection columns. Once inspections become rows keyed by `asset_id`, the inspector's notes that named only the label cannot be assigned to `SL-0113` or `SL-0114` — they are orphans-in-waiting. The correct treatment is to load them with `asset_id = NULL` into a *quarantine* table (never into Inspection, where `asset_id` is not null), and resolve them by site visit with the photos. Silently attaching them to the first matching label is the error the identity policy (8.4.3) forbids.

**Misconception.** *"Cascade delete keeps the data clean, so it is always the right choice."* Cascade delete keeps *referential integrity* by deleting history. For an asset that is decommissioned, the business almost always wants the inspection record kept — so the asset row is marked `status = 'REMOVED'` with a date, and nothing is deleted. Cascade is appropriate for children that have no meaning without the parent *and* no audit value (a photo of a deleted test record); it is a per-relationship decision, written into the relationship definition (8.5.3).

### 8.7.3 Who owns a schema change, and what a change touches

A schema is an interface. Applications, forms, scripts, published layers, reports, and other people's exports all depend on field names, types, domains, and keys. Changing one without a process is the GIS equivalent of changing a public API signature in a patch release.

**Ownership (course policy, applicable to any platform).** Every table has a named **schema owner** — a person or role, not "the GIS team" — who is the only party permitted to approve a change to its fields, domains, keys, or relationships. Anyone may *propose* a change by submitting the change and the completed impact checklist below; the owner approves, schedules, and records it in a change log (the Instructor Appendix I.8 of this document is itself an example of the form). Access management — who can *make* the change on the server — belongs to a later phase; here the point is that the *decision* has an owner.

**Table 8.7c — Migration impact checklist (complete one per proposed change).**

| # | Question | Why it matters | Example answer for "rename `Height` to `pole_height_m` and convert to metres" |
| --- | --- | --- | --- |
| 1 | What exactly changes — field name, type, length, null policy, domain, key, relationship, geometry? | Different changes have different reversibility | Name, type (text → double), unit, null policy |
| 2 | Is the change reversible on a populated table in the target platform? | In ArcGIS: data type "Can only be changed if the table is empty"; Allow NULL "Can only be set to false if the table is empty"; text length "Can only be increased for tables with data" [X17] | Not in place: requires a new field, a checked conversion, then dropping the old field |
| 3 | Which rows cannot be converted losslessly? | The lab's `7`, `6.5`, and `0` have no unit; `22 ft` converts; `12 m` converts | Three rows need source checks; the conversion log records each |
| 4 | Which applications, forms, scripts, published layers, views, and reports reference the field? | Each is a breaking change waiting to happen | Field app form; the "tall poles" report; the hosted layer's pop-up |
| 5 | Which related tables, domains, or relationship classes reference it? | Keys and domains are shared [S22] | None (not a key) |
| 6 | What is the data-fix and the verification query? | A migration is not done until a query proves it | `SELECT COUNT(*) WHERE pole_height_m IS NULL AND asset_type = 'SL'` before/after |
| 7 | What is the rollback? | "Undo" rarely exists for schema changes | Keep the old field until the report has been re-run against the new one |
| 8 | Who approved it, when, and where is it logged? | Traceability (Chapter 9 will require the same for data edits) | Schema owner; change log entry |

**Developer analogy (and where it stops).** This is a database migration with a code review. The analogy is close. It stops at *distribution*: a web application has one database and one deploy; a GIS dataset may exist as a master geodatabase, a published hosted layer that *copied* the data at publish time (Chapter 2), three contractors' shapefile exports, and a dashboard — and the migration must reach, or deliberately exclude, each copy. Question 4 in the checklist is where most GIS migrations fail.

**Comprehension check 8.7.** A colleague wants to add "date of last cleaning" as a new column on the Asset table for drains, updated by overwriting it after each cleaning. Using 8.7.1 and 8.7.3, explain what is lost by that design, what the alternative is, and which checklist questions the alternative still has to answer.

## 8.8 Guided lab: design an inspection schema

### 8.8.1 Objective, prerequisites, and what this lab is (and is not)

**Objective.** Take a deliberately poor single-table asset register, diagnose its defects, and redesign it as a coherent multi-table schema: an entity–relationship diagram, a data dictionary, relationship definitions, ten example records spread across the new tables, and three business questions the new design can answer that the old one could not answer reliably.

**Prerequisites.** Modules 8.1–8.7 of this chapter. No software skills are required beyond a spreadsheet or text editor.

**Required software/access.** None. A spreadsheet (any), a text editor, and a way to draw or type a diagram (the ASCII style of Figure 8.1 is acceptable). *Optional:* ArcGIS Pro or QGIS Desktop if you want to implement the result afterwards — see Instructor Appendix I.6, which is *not execution-tested*.

**What this lab is.** A design exercise with a checkable answer. The blueprint's primary laboratory approach for this chapter is a schema design, and everything you produce is verifiable by reading, counting, and hand-running the orphan check.

**What it is not.** It is not a data-loading exercise, not a service-configuration exercise, and it does not ask you to decide *how* ArcGIS or PostgreSQL will enforce your relationships — only to state what you want enforced (8.5.3, 8.6.3).

### 8.8.2 Input data — the poor design (synthetic; reproduce exactly)

Create a spreadsheet or CSV named `Assets_Flat_Ch8.csv` with the rows below **exactly as printed, including the inconsistencies** — they are the point of the exercise. Coordinates are on the Chapter 1 training grid (metres, (x, y), no Earth location). All names, dates, teams, and photo file names are invented. Blank cells are blank in the source.

**Table 8.8-A — `Assets_Flat_Ch8` (synthetic, deliberately poor).**

| # | AssetName | Type | X | Y | Height | Wattage | Insp1_Date | Insp1_Cond | Insp1_By | Insp2_Date | Insp2_Cond | Insp2_By | Insp3_Date | Insp3_Cond | Insp3_By | Photos | Ward | Notes |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| 1 | Streetlight near Ward A market | Streetlight | 205 | 195 | 7 | 70 | 14/09/2025 | Fair | North | 2026-03-14 | Fair | North crew | | | | sl113_a.jpg; sl113_b.jpg | A | Lamp flickers at dusk (Mar 2026) |
| 2 | Streetlight near Ward A market | Light | 260 | 190 | 6.5 | | 2026-02-02 | Good | North | | | | | | | | A | |
| 3 | Main road light | Streetlight | 600 | 480 | 22 ft | | 2026-01-10 | Good | T-N | | | | | | | mr_light.jpg | A | |
| 4 | Drain by temple | Drain | 995 | 510 | | | 15/10/2024 | Fair | South | 2025-11-02 | Poor | South | | | | dr42_1.jpg, dr42_2.jpg | A/B | Grating cracked; silt |
| 5 | Big tree | Tree | 2190 | 520 | 12 m | | | | | | | | | | | | outside | |
| 6 | Drain Ward B | Drain | 1450 | 250 | 0 | 0 | 2026-04-20 | Fair | South | | | | | | | | B | |
| 7 | Light no. 250 (decommissioned) | Streetlight | 1800 | 700 | 8 | 0 | 2024-05-05 | Good | North | 2025-05-06 | Fair | North | 2026-05-07 | Poor | South | | B | 4th visit 2026-08-01 not recorded – no column |
| 8 | Tree near school | Tree | 400 | 900 | | | 2026-06-15 | 3 | South | | | | | | | tr303.jpg | A | |

The "#" column is a print aid only; it is **not** a field in the file and you must not use it as an identifier.

**Correspondence to earlier fixtures (for the instructor and for checking your work):** row 1 is asset SL-0113 and row 4 is DR-0042 from Table F4, with their Table F8-2 inspections (INS-0004 and INS-0003; INS-0001 and INS-0002); row 5 is TR-0301. Rows 2, 3, 6, 7, and 8 are new assets introduced for this lab; they do not appear in the Chapter 3 or Chapter 7 fixtures, and the Chapter 7 assets BN-0007, DR-0110, and SL-0055 are deliberately left out of this table.

### 8.8.3 Ordered steps

1. **Diagnose (30 minutes).** Read Table 8.8-A row by row and list every defect you find, each tagged with the module that explains it. Aim for at least ten distinct defects. Do not fix anything yet. (A defect is anything that makes a value ambiguous, a row unidentifiable, a history unrecoverable, or a query unreliable.)
2. **Identify entities and grains (15 minutes).** From the columns, name the entities hiding in the table and write the one-sentence grain of each (8.1.2). You should find at least Asset, Inspection, Team, and Photo; decide whether Ward is an entity in *this* design or a derived spatial fact (8.5.2).
3. **Draw the entity–relationship diagram (20 minutes).** Boxes for entities, lines for relationships, cardinality at each end, key field names on the lines. Figure 8.1 is a reference, not a template to copy: your diagram must reflect the decisions you made in step 2 (for example, whether streetlight-only fields go in a 1:1 detail table or stay on Asset with a type rule).
4. **Write the data dictionary (45 minutes).** Seven parts per field (8.3.1) for **every** field of Asset and Inspection, and for the key fields of every other table. Include the geometry specification for Asset (8.2.1: type, multipart policy, CRS/units, Z, meaning of the point per asset type, location method). State the timestamp convention (8.3.3). State the null policy in words for every nullable field. Mark derived fields.
5. **Define the relationships (15 minutes).** For each line in your diagram write: the two tables, the origin/parent key, the destination/child key, the cardinality, whether the child key is nullable, and the *desired* behaviour when a parent is deleted (restrict, set null, cascade — with one sentence of justification, 8.7.2). Also state, per relationship, whether you expect it to be enforced by the schema, the application, or a review query in the target platform you have in mind (8.6.3).
6. **Define the controlled values (15 minutes).** Every field that takes a category gets a coded list with stored code and displayed description; every measured number gets a range or an explicit "no range". Write the default policy for each field (8.6.2).
7. **Populate ten example records (30 minutes).** Convert the content of Table 8.8-A into rows of your new tables. Ten rows in total across *at least three* tables (for example 4 assets + 2 teams + 3 inspections + 1 photo). Assign business identifiers by a rule you write down (for example `SL-0113` for row 1 as in Chapter 1; new numbers for the new assets). Convert every date to `YYYY-MM-DD`, every time to the stored convention, every height to metres or null with a reason, every team name to a team key, every condition to the 1–5 scale. Where a value *cannot* be converted without going back to the source (a unitless `7`, a `0` that may mean unknown), record null and write the reason in a conversion log.
8. **Write three business questions (15 minutes).** Each must be answerable from your ten records by hand, must involve at least two tables, and must have been unreliable or impossible against Table 8.8-A. Write the question, the tables and keys it uses, and the answer from your records.
9. **Validate (15 minutes).** Perform the checks in 8.8.4 by hand and record the results.

### 8.8.4 Expected results and validation checks

Because the input is fixed, several results are fixed too. Your design choices may differ from the instructor's (I.3), but these invariants must hold.

| Check | Expected result | How to verify |
| --- | --- | --- |
| Number of distinct assets recognised | **8** (one per row; the two identical names are two assets because their coordinates differ by 55 m in x and 5 m in y) | Count rows in your Asset table |
| Number of inspection observations recoverable from the flat table | **11** with data (rows 1:2, 2:1, 3:1, 4:2, 5:0, 6:1, 7:3, 8:1) plus **1** known event with no data (row 7's fourth visit, 2026-08-01) | Count; the 2026-08-01 visit may appear as an Inspection row with null condition and a remark, or in the conversion log — but it must appear somewhere |
| Number of photo files | **6** (2 + 1 + 2 + 1) | Count after splitting on `;` and `,` |
| Number of teams | **2** (`North`, `North crew`, and `T-N` are one team; `South` is the other) | Your Team table |
| Repeated inspections retained | Both SL-0113 visits and all three SL-0250 visits exist as separate rows; nothing was overwritten | Look for `asset_id` values with more than one Inspection row |
| Every related ID resolves | The hand-run orphan check of 8.7.2 returns zero rows for Inspection→Asset and Photo→Inspection | Walk each child row and find its parent |
| Ward for row 4 | The flat table says `A/B`; the coordinates (995, 510) are **strictly inside Ward A** (995 < 1000) by 5 m. Your design either derives ward spatially (and records A) or stores a business assignment with a documented reason for `A/B` | Compare with Table F1 |
| Ward for row 5 | (2190, 520) is outside both wards, as in Chapter 1 | Compare with Table F1 |
| Heights | `22 ft` → 6.7056 m exactly (22 × 0.3048), which you may round to 6.71 m and must state the rounding; `12 m` → 12; `7`, `6.5`, `8` → null with reason "unit not recorded" (or a documented assumption that they are metres, flagged for confirmation); `0` on a drain → not applicable (no value in a drain row, or null with that meaning) | Conversion log |
| Wattage | Row 1: 70; row 2: unknown (null); row 3: unknown (null); row 6: a drain — *not applicable*, the `0` is wrong; row 7: `0`, plausibly "no lamp fitted" for a decommissioned light — record it as 0 only if your dictionary defines 0 that way, otherwise null with reason | Conversion log |
| Row 8's condition `3` | Consistent with the 1–5 scale (Fair); the words in other rows map Very poor=1, Poor=2, Fair=3, Good=4, Very good=5 | Dictionary |
| Dates | Two rows use `DD/MM/YYYY` (`14/09/2025`, `15/10/2024`); all others `YYYY-MM-DD`; every converted date must match Table F8-2 where the visit exists there | Conversion log |
| Business questions | Each answer is reproducible from the ten records by a second person | Swap with a colleague |

**The tolerance statement.** All counts above are exact. The only numeric conversion is 22 ft to metres, using the international foot (1 ft = 0.3048 m exactly); 22 × 0.3048 = 6.7056 m, hand-checked. No software output is involved anywhere in this lab.

### 8.8.5 Troubleshooting

| Symptom | Likely cause | Fix |
| --- | --- | --- |
| Your Asset table has 7 rows | You merged the two "Streetlight near Ward A market" rows | Re-read 8.4.1: a label is not an identifier; the coordinates differ |
| Your Inspection table has 8 rows | You kept "latest inspection" per asset | Re-read 8.1.2 and 8.5.2: one row per visit |
| You cannot decide where `Wattage` goes | The not-applicable problem | Either a `StreetlightDetail` 1:1 table (8.5.1) or a type rule in the application/review layer (8.6.3); say which |
| You stored `Insp1_By` values as typed | Team is an entity | Team table with a key; inspections carry `team_id` |
| Your photos are still one text field | Habit | 8.7.2: one row per file |
| You gave every field a default so nothing is null | 8.6.2 | Remove defaults from observed fields; document null meanings |
| Ward `A/B` for row 4 kept as text | You trusted the source over the geometry | Either derive spatially or store an assignment *with a reason*; never both silently |
| You used the `#` column or the spreadsheet row number as `asset_id` | Storage row identifier used as a business key (8.4.1) | Assign business identifiers by a written rule |
| `14/09/2025` became 2025-14-09 or September 14 became 9 January | Date format ambiguity | Convert every date to `YYYY-MM-DD` and record the source format in the conversion log; the fixture's `DD/MM/YYYY` in row 1 is the same visit as INS-0004 in Table F8-2, and row 4's `15/10/2024` is INS-0001 |

### 8.8.6 Required learner deliverables

Submit one document (or folder) containing:

1. **Defect list** — at least ten defects in Table 8.8-A, each tagged with a module number.
2. **Entity–relationship diagram** with cardinalities and key names.
3. **Data dictionary** — all fields of Asset and Inspection with all seven parts; key fields of every other table; the geometry specification; the timestamp convention.
4. **Relationship definitions** — one block per relationship as in step 5.
5. **Controlled values and defaults** — coded lists with code and description; range limits; default policy table.
6. **Ten example records** across at least three tables, plus the **conversion log** explaining every null and every converted value.
7. **Three business questions** with tables, keys, and answers.
8. **Validation record** — the checks of 8.8.4 with your results, and the hand-run orphan check.

**Scoring** follows 8.9.5. The two non-negotiable checks are: repeated inspections are retained as separate rows, and every child key resolves to a parent.

### 8.8.7 ArcGIS Pro and QGIS implementation routes (optional; not execution-tested)

The design above can be implemented in a file geodatabase (feature class, stand-alone tables, domains, relationship classes, attachments) or in a GeoPackage opened in QGIS (tables, project relations, value-map widgets and constraints). Both routes are described in Instructor Appendix I.6 with citations to the pages they were written from. **Neither has been executed by the author**; an instructor must run them once in the installed version, record the version and any label differences, and only then issue them to learners. The design lab is complete without them.

## 8.9 Independent check and progression gate

Complete this assessment without the Instructor Appendix. Where a question says "state your assumptions", an answer without stated assumptions is incomplete even if the conclusion is right. Every question names the module(s) and outcome(s) it tests.

### 8.9.1 Concept questions (six)

**Q1.** [8.1; LO1] A table has the columns `asset_id`, `asset_type`, `install_year`, `visit_date`, `condition`, `team`, `reporter_phone`, `report_date`. (a) Name the grains mixed in this table. (b) Assign each column to a grain. (c) Which column would you challenge under the "every field earns its place" rule, and what question would you ask its proposer?

**Q2.** [8.2; LO2] Two point datasets, both on the training grid in metres, are proposed for merging into one table called `Locations`: surveyed streetlight pole bases and citizen-reported incident positions. Give two reasons from the geometry specification (8.2.1–8.2.3) why the merge is wrong, and state what a correct design keeps separate.

**Q3.** [8.3; LO3] A field `defects_found` (short integer) is nullable with no default. Explain the operational difference between the values `0` and null for a visit, and what would go wrong for a "percentage of visits with defects" statistic if the field were given the default `0`. Then state, for an ArcGIS Online hosted feature layer, in which time zone a `visited_at` date value is stored and what a publisher must do if the source data is in IST [X13].

**Q4.** [8.4; LO4] Choose the single best answer and justify it in two sentences. Which of the following may be used as the foreign key that inspection rows carry to identify their asset across an export to a file geodatabase and re-import?
(a) The asset's ObjectID. (b) The asset's display label. (c) The asset's business identifier `asset_id`. (d) The asset's GlobalID, with no further workflow configuration.

**Q5.** [8.5; LO5] Classify each of the following as a *conceptual relationship*, a *join*, a *QGIS relation*, an *ArcGIS relationship class*, or a *database foreign key*, and state for each whether it is enforced when a script inserts rows directly into the storage: (a) a line on an ER diagram; (b) a *Composition*-strength link declared in *Project ► Properties ► Relations*; (c) `FOREIGN KEY (asset_id) REFERENCES asset(asset_id) ON DELETE RESTRICT`; (d) fields from Asset appended to Inspection in one map's layer properties; (e) a stored geodatabase dataset that sets a destination foreign key to null when the origin row is deleted.

**Q6.** [8.6; LO6] For the field `Request.status`, distinguish the *stored code*, the *displayed description*, the *default*, and a *missing observation* using the `RequestStatus` domain (Table 8.6a). Then say where each of these rules should live (schema / application / review) and why: (i) status must be one of the six codes; (ii) a `DUP` request must reference another request; (iii) requests open for more than 30 days must be listed weekly.

### 8.9.2 Scenario questions (two)

**S1.** [8.3, 8.6; LO3, LO6] A field crew's app defaults `condition_code` to `3` and `defects_found` to `0`, and stores `visited_at` from the device clock at the moment the form is *saved*, which crews do at the depot each evening. The supervisor's dashboard shows 97% of assets "Fair or better" and "no visits after 4 p.m." (a) Identify three separate design faults and the module that explains each. (b) For each fault, state the schema or dictionary change that fixes it. (c) State one thing the dashboard *cannot* tell you even after the fixes.

**S2.** [8.4, 8.7; LO4, LO7] The municipality receives a contractor's spreadsheet of 40 drain inspections keyed on the contractor's own row numbers 1–40 and the drain's street description. Twelve descriptions match no asset exactly; three match two assets each. (a) Write the identity policy you would apply to load this file (import matching, duplicates, related records). (b) Say which rows go into Inspection and which go into quarantine, and why the quarantine table's `asset_id` is nullable while Inspection's is not. (c) Write the orphan query (any SQL dialect or plain words) you would run after loading, and its expected result.

### 8.9.3 Independent practical task (unfamiliar inputs)

[8.1–8.8; LO1–LO8] The municipality is adding a new asset type: **bus shelters**. Requirements, all synthetic:

- Each shelter is a physical structure at one location; it has a number of seats, a flag for whether it has lighting, and an advertising panel that may be absent.
- Each shelter serves one or more **bus routes**, and each route serves many shelters. Routes have a code (for example `07`, `12A`) and a name.
- Shelters receive **cleaning visits** (a date and time, a crew, a cleanliness score 1–5, and free-text remarks) and **structural inspections** (a date and time, a crew, a condition score 1–5, defects found). A shelter can have many of each.
- Both kinds of visit can have **photos**, several per visit.
- The existing Asset, Inspection, Team, and InspectionPhoto tables from the lab must continue to work; the existing field app writes to Inspection.

Deliver: (1) an extended ER diagram; (2) the dictionary rows for every new or changed field; (3) relationship definitions including the new M:N; (4) your decision on whether cleaning visits are Inspection rows with a visit-type code, or a separate table, with the reasoning and the consequence for the existing app; (5) six example records that include one shelter on two routes and one cleaning visit with two photos; (6) the migration impact checklist (Table 8.7c) for the change to the Asset table; (7) a statement of which integrity rules are enforced by your intended platform and which still rely on application or review — name the platform you assumed.

Constraints: no destructive duplication (no copy of shelter facts on visit rows; no copy of route facts on shelter rows); route codes are text; every table has a business identifier.

### 8.9.4 Oral explanation (one)

[8.4, 8.7; LO4, LO7] In under three minutes, tell the story of asset SL-0113 from installation in 2018 through the two inspections in Table F8-2 (INS-0004, then INS-0003), naming at each step which table gains a row, which fields change, which do not, and which identifiers connect the rows. Finish by stating one integrity rule in your design that the storage enforces and one that it does not.

### 8.9.5 Submission requirements and scoring

Submit the 8.8 deliverables, written answers to Q1–Q6 and S1–S2, the practical task's seven items, and be ready for the oral question. Suggested weighting (an internal recommendation, following the blueprint's Appendix B):

| Component | Weight | Evidence required |
| --- | --- | --- |
| Concept questions Q1–Q6 | 25% | Correct answer with stated assumptions; one defensible best answer for Q4 |
| Scenario questions S1–S2 | 20% | Faults identified with module references; concrete schema fixes; a correct orphan query |
| Independent practical task | 35% | Coherent diagram and dictionary; M:N correctly implemented with a junction table; no duplicated facts; six records that resolve; checklist completed; enforcement statement honest about the assumed platform |
| Guided lab (8.8) | 15% | Invariants of 8.8.4 met; conversion log complete |
| Oral explanation | 5% | Correct sequence of rows and identifiers; enforcement distinction stated |

**Progression rule.** Pass at 80% overall **and** none of the following critical misconceptions present anywhere in the submission; any one of them requires remediation and a fresh exercise (I.5) before Chapter 9:

- Using a display label, a spreadsheet row number, or an ObjectID as the key that related records carry.
- Storing repeated inspections as repeated columns, or overwriting history with the latest visit.
- Treating a default value as an observation, or turning nulls into zeros in a count or measurement field.
- Claiming that an ER diagram, a join, or a UUID column enforces integrity by itself.
- Mixing units or time-zone conventions in one field without a documented rule.

---

## 8.10 Media and source brief

The full media brief — slide outline, audio-lesson outline, diagram specifications, and the one justified animation — is in the Media Appendix at the end of this document so that it can be updated independently of the teaching text. This module records the three requirements the blueprint places on it.

1. **Three visual devices.** An **entity–relationship diagram** (Figure 8.1, redrawn as D1 in the Media Appendix), **field-definition cards** (one card per dictionary row for the Inspection table, D2), and a **before/after record example** showing the same two SL-0113 visits as one overwritten flat row and as two related rows (D3). The audio lesson tells the **lifecycle of one asset** — SL-0113 from installation, through the September 2025 all-clear and the March 2026 flicker, to the design of the tables that hold that story.
2. **No 3D scene.** Nothing in this chapter is spatial in a way that a 3D scene would clarify. The blueprint allows a relationship animation only if it makes record ownership clearer; the Media Appendix specifies exactly one (M.5), showing rows *moving* from the flat table into their own tables while a highlighted key stays attached to each — and states how it is validated against the counts in 8.8.4.
3. **Sources.** Attributes and their meaning were read in the QGIS introduction [S23]; the geodatabase information model in the Esri introduction [S21]; domains in the Esri domains overview [S22]. The schema, the identity policy, the domains, the enforcement table, and the checklist in this chapter are **instructional designs written for this course**; they are not an Esri-mandated schema and should not be presented as one. The additional pages [X01]–[X25] were read to support specific claims and are listed in the reference list.

**Transition to Chapter 9.** You now have a schema that says what each row means, how rows are identified, and how they relate. Chapter 9 puts real, imperfect data into it: how locations are captured, how editing and snapping work, what makes geometry valid, and how to detect and correct attribute and relationship problems while leaving an audit trail. The orphan query and the identity policy from this chapter are the first two tools you will use there.

---

## Glossary

Terms are listed in the order they first appear. Platform-specific terms are marked **[ArcGIS]**, **[QGIS]**, or **[SQL]**; unmarked terms are general.

| Term | Meaning in this chapter |
| --- | --- |
| **Entity** | A kind of thing the organisation records (asset, inspection, request, ward, team); each becomes a table. |
| **Feature class** **[ArcGIS]** | A table whose rows all have a geometry of one type, one set of attributes, and one spatial reference [X18]. Generic term: spatial table. |
| **Stand-alone (nonspatial) table** | A table without geometry (Inspection, Team). |
| **Grain** | The precise statement of what one row represents. |
| **Field** | GIS word for a column; a **record** is a row. |
| **Data dictionary** | The document describing every field: name, description, type, units, null policy, example, source. |
| **Geometry specification** | The design statement for a spatial table: geometry type, multipart policy, CRS and units, Z, what the shape represents, how the location was obtained. |
| **Multipart** | One row holding several disconnected shapes (Chapter 3); allowed for lines and polygons in ArcGIS [X18]. |
| **Location method** | A controlled value recording how a position was obtained (survey, GNSS, digitised, approximate). |
| **Null policy** | Whether null is allowed in a field and what it means there. |
| **Unknown / not applicable / zero / empty text** | Four distinct facts that must not share one representation without a stated rule. |
| **Event time / entry time** | When something happened versus when its record was written. |
| **Date, Date only, Time only, Timestamp offset** **[ArcGIS]** | Geodatabase date/time field types; `Date` stores no time zone [X01]. |
| **Business identifier** | The stable, organisation-assigned key for a thing (`SL-0113`). |
| **Display label** | What people see; not a key. |
| **Storage row identifier** | An engine-assigned row key (ObjectID, a `SERIAL`); valid inside one dataset instance. |
| **ObjectID** **[ArcGIS]** | A non-null unique integer per row, maintained by ArcGIS [X01]; may be generated at run time for query layers [X02]. |
| **GlobalID / GUID** **[ArcGIS]** | 128-bit identifiers; GlobalID is assigned and maintained by the geodatabase, GUID by you [X01]. Not preserved on Append unless the Preserve Global IDs environment is set (enterprise geodatabase only) [X04]. |
| **Identity policy** | Written rules for import matching, duplicate handling, and which key related records carry. |
| **Cardinality** | How many rows on each side of a relationship: 1:1, 1:M, M:N. |
| **Junction (intermediate) table** | The table that implements M:N, one row per pair; ArcGIS creates one for attributed/M:N relationship classes [X07]. |
| **Join** | A temporary, read-time lookup appending fields from another table [X08] [X21]. |
| **Relate** **[ArcGIS]** | A project-level link for selecting related rows without appending fields [X08]. |
| **Relation** **[QGIS]** | A project-level parent/child declaration used for forms and cascading edits within QGIS [X21]. |
| **Relationship class** **[ArcGIS]** | A geodatabase dataset storing a relationship with origin, destination, keys, cardinality, and simple/composite behaviour [X05] [X06] [X08]. |
| **Simple / composite relationship class** **[ArcGIS]** | Simple: deleting the origin nulls the destination's foreign key; composite: deleting the origin cascade-deletes destinations [X06]. |
| **Foreign key constraint** **[SQL]** | A database rule that a column's values must exist in another table; `ON DELETE` options include `RESTRICT`, `CASCADE`, `SET NULL` [X22]. |
| **Attribute domain** **[ArcGIS]** | A rule describing a field's available values: coded-value (code + description) or range (min–max) [S22]. |
| **Value Map / Range widget** **[QGIS]** | Form widgets that store a code and show a description, or bound a number; auto-assigned from GeoPackage/file geodatabase domains [X20]. |
| **Constraint (soft / hard)** **[QGIS]** | Not-null, unique, or expression rules checked in QGIS forms; hard constraints block saving [X20]. |
| **Default value** | The value a new row receives when none is supplied; never an observation. |
| **Derived (cached) field** | A field whose value is computed from other rows and must be refreshed by a stated rule. |
| **Editor tracking** **[ArcGIS]** | Automatic creator/creation date/editor/last-edit date fields; records who and when, not what; dates in UTC by default [X12] [X25]. |
| **Attachment** **[ArcGIS]** | A media file stored in a `__ATTACH` table related 1:M to its feature or row via `__ATTACHREL` [X09] [X11]. |
| **Orphan** | A child row whose foreign key matches no parent. |
| **Schema owner** | The named role that approves schema changes. |
| **Migration impact checklist** | Table 8.7c; completed before any schema change. |
| **Attribute rule** **[ArcGIS]** | Arcade-scripted calculation/constraint/validation rules requiring GlobalIDs [X19]; out of scope for Phase 1. |

## Recap

1. Assets, inspections, requests, wards, and teams are different entities with different grains; only some have their own geometry, and the others refer to them. Every field is justified by a business question or it is removed.
2. A geometry column needs a full specification — type, multipart policy, CRS and units, Z, what the point stands for, and how it was obtained. Points with different meanings are not interchangeable.
3. A data dictionary states type, units, null policy, example, and source for every field. Unknown, not applicable, zero, and empty text are four facts; codes are text; timestamps need a stated event/entry meaning and a per-storage time-zone convention (hosted layers and `timestamptz` store UTC; a geodatabase `Date` stores whatever you put in it).
4. A business identifier, a display label, and a storage row identifier are three things. ObjectIDs are per-table and can be run-time values; GlobalIDs are geodatabase-maintained but not preserved on Append by default and do not configure sync by themselves. Related records carry the business identifier. An identity policy is written before the first import.
5. 1:M puts the key on the many side; 1:1 puts a unique key on the detail side; M:N needs a junction table. Inspections are rows related to the asset, never copies of it. A conceptual relationship, a join, a QGIS relation, an ArcGIS relationship class, and a database foreign key enforce different things against different actors.
6. Domains store a code and display a description, or bound a number. A default is not an observation; null stays meaningful for observed fields. Each rule is assigned to the schema, the application, or a review query — and the platform decides which schema rules actually exist.
7. Current state is a derived cache; history is the inspection table; event time and entry time are separate fields. Photos are related rows, never a delimited string. Orphans are found by a left-join query. A named owner approves schema changes after a completed impact checklist.

## Cross-references to later chapters

- Capturing locations, editing, snapping, geometry validity, and correcting attribute and relationship defects with an audit trail — using this chapter's orphan query and identity policy: **Chapter 9**.
- Attribute queries, joins in practice, and the boundary semantics that decide whether DR-0042 at (995, 510) is "in Ward A": **Chapter 10**.
- Spatial joins that *produce* joined tables as outputs (contrast with 8.5.2's warning about storing them): **Chapter 11**.
- Symbolising the derived `last_condition_code` honestly, including the "not assessed" class: **Chapter 12**.
- Implementing relationship classes, attachments, domains, subtypes, attribute rules, editor tracking, versioning, and publishing related tables as services: later phases (ArcGIS Pro and Enterprise tracks). The Chapter 2 solution "S-2, Request Tracker (Training)" showed a related inspection table in a field app; this chapter is the design behind such a table.

---

## References

All pages were opened and read by the author on **19 September 2026**. IDs [S20]–[S23] are the blueprint's reference register entries used by this chapter; [X01]–[X25] are additional official pages consulted to support specific claims. Esri "latest" pages (whose page metadata identified ArcGIS Pro **3.7** on the check date) and ArcGIS Online pages change without notice; re-check before reuse. The blueprint's `pro.arcgis.com` addresses redirect permanently to `doc.esri.com`; the destinations are recorded.

| ID | Publisher | Page title | URL | Accessed |
| --- | --- | --- | --- | --- |
| S20 | Esri | ArcGIS Pro — Geoprocessing considerations for shapefile output | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/appendices/geoprocessing-considerations-for-shapefile-output.html | 2026-09-19 |
| S21 | Esri | ArcGIS Pro — Introduction to the geodatabase (What is a geodatabase?) | https://doc.esri.com/en/arcgis-pro/latest/help/data/geodatabases/overview/what-is-a-geodatabase-.html | 2026-09-19 |
| S22 | Esri | ArcGIS Pro — Introduction to attribute domains | https://doc.esri.com/en/arcgis-pro/latest/help/data/geodatabases/overview/an-overview-of-attribute-domains.html | 2026-09-19 |
| S23 | QGIS Project | A Gentle Introduction to GIS — Vector Attribute Data (3.44) | https://docs.qgis.org/3.44/en/docs/gentle_gis_introduction/vector_attribute_data.html | 2026-09-19 |
| X01 | Esri | ArcGIS Pro — ArcGIS field data types | https://doc.esri.com/en/arcgis-pro/latest/help/data/geodatabases/overview/arcgis-field-data-types.html | 2026-09-19 |
| X02 | Esri | ArcGIS Pro — Unique identifier fields in database tables (the `pro.arcgis.com` address redirected here, HTTP 301) | https://doc.esri.com/en/arcgis-pro/latest/help/data/databases/unique-identifier-fields-in-database-tables.html | 2026-09-19 |
| X03 | Esri | ArcGIS Pro — Add Global IDs (Data Management) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/add-global-ids.html | 2026-09-19 |
| X04 | Esri | ArcGIS Pro — Preserve Global IDs (environment setting) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/environment-settings/preserve-globalids.html | 2026-09-19 |
| X05 | Esri | ArcGIS Pro — Geodatabase relationship class fundamentals | https://doc.esri.com/en/arcgis-pro/latest/help/data/relationships/geodatabase-relationship-class-fundamentals.html | 2026-09-19 |
| X06 | Esri | ArcGIS Pro — Geodatabase relationship class types | https://doc.esri.com/en/arcgis-pro/latest/help/data/relationships/geodatabase-relationship-class-types.html | 2026-09-19 |
| X07 | Esri | ArcGIS Pro — Attributed relationship classes | https://doc.esri.com/en/arcgis-pro/latest/help/data/relationships/understanding-attributed-relationship-classes.html | 2026-09-19 |
| X08 | Esri | ArcGIS Pro — Data relationship options | https://doc.esri.com/en/arcgis-pro/latest/help/data/relationships/data-relationship-options.html | 2026-09-19 |
| X09 | Esri | ArcGIS Pro — Enable attachments (help) | https://doc.esri.com/en/arcgis-pro/latest/help/data/geodatabases/overview/enable-attachments.html | 2026-09-19 |
| X10 | Esri | ArcGIS Pro — Enable Attachments (Data Management tool) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/enable-attachments.html | 2026-09-19 |
| X11 | Esri | ArcGIS Pro — Work with attachments in the geodatabase | https://doc.esri.com/en/arcgis-pro/latest/help/data/geodatabases/overview/attachments-in-the-geodatabase.html | 2026-09-19 |
| X12 | Esri | ArcGIS Pro — Enable Editor Tracking (Data Management tool) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/enable-editor-tracking.html | 2026-09-19 |
| X13 | Esri | ArcGIS Online — Date and time fields in ArcGIS Online | https://doc.arcgis.com/en/arcgis-online/manage-data/work-with-date-fields.htm | 2026-09-19 |
| X14 | Esri | ArcGIS Online — Manage hosted feature layer editing | https://doc.arcgis.com/en/arcgis-online/manage-data/manage-editing-hfl.htm | 2026-09-19 |
| X15 | Esri | ArcGIS Enterprise — Prepare data to publish a feature service | https://doc.esri.com/en/arcgis-enterprise/latest/administer/prepare-data-for-feature-services.html | 2026-09-19 |
| X16 | Esri | ArcGIS Pro — Define fields in tables | https://doc.esri.com/en/arcgis-pro/latest/help/data/geodatabases/overview/defining-fields-in-tables.html | 2026-09-19 |
| X17 | Esri | ArcGIS Pro — Modification of field properties | https://doc.esri.com/en/arcgis-pro/latest/help/data/geodatabases/overview/modifying-field-properties.html | 2026-09-19 |
| X18 | Esri | ArcGIS Pro — Feature class basics | https://doc.esri.com/en/arcgis-pro/latest/help/data/geodatabases/overview/feature-class-basics.html | 2026-09-19 |
| X19 | Esri | ArcGIS Pro — Introduction to attribute rules | https://doc.esri.com/en/arcgis-pro/latest/help/data/geodatabases/overview/an-overview-of-attribute-rules.html | 2026-09-19 |
| X20 | QGIS Project | QGIS Desktop 3.40 User Guide — The Vector Properties Dialog (Attributes Form Properties: constraints, default values, edit widgets) | https://docs.qgis.org/3.40/en/docs/user_manual/working_with_vector/vector_properties.html | 2026-09-19 |
| X21 | QGIS Project | QGIS Desktop 3.40 User Guide — Connecting and Editing Data Across Layers (joins and relations) | https://docs.qgis.org/3.40/en/docs/user_manual/working_with_vector/joins_relations.html | 2026-09-19 |
| X22 | PostgreSQL Global Development Group | PostgreSQL Documentation ("current") — 5.5 Constraints | https://www.postgresql.org/docs/current/ddl-constraints.html | 2026-09-19 |
| X23 | PostGIS Project | PostGIS Documentation — Spatial Data Management (creating a spatial table; geometry type modifiers) | https://postgis.net/docs/using_postgis_dbmanagement.html | 2026-09-19 |
| X24 | PostgreSQL Global Development Group | PostgreSQL Documentation ("current") — 8.5 Date/Time Types | https://www.postgresql.org/docs/current/datatype-datetime.html | 2026-09-19 |
| X25 | Esri | ArcGIS Pro — Enable editor tracking (help) | https://doc.esri.com/en/arcgis-pro/latest/help/data/geodatabases/overview/enable-editor-tracking.html | 2026-09-19 |

---

# Instructor Appendix (separate from learner material)

Learners should not read this appendix before completing the 8.9 gate.

## I.1 Answers and reasoning — comprehension checks and in-module exercises

**8.1.** Three grains: *incident* (reporter's location, report date — one row per report), *asset* (the matched asset's coordinates and installation year — one row per physical thing), and *inspection visit* (the date the team visited — one row per visit). The proposal copies asset facts onto every report (so two reports of one pole carry two copies of its installation year, which can disagree) and can hold only one visit per report. Correct design: Request rows carry `asset_id` (nullable); asset facts stay on Asset; visits are Inspection rows keyed by `asset_id` (and optionally `request_id` if a visit was triggered by a request).

**8.2.** Request geometry specification: (1) Point. (2) Not applicable — one point per row. (3) Training grid, metres, (x, y), no EPSG code, no Earth location (in production: the organisation's chosen projected CRS from Chapter 6, stated once). (4) No Z. (5) The point represents *the place the reporter indicated* — not an asset location, not a surveyed position. (6) `location_method` should record the channel: `APPROX` for phone reports geocoded from a description, `GNSS` for mobile-app reports using the device position, `DIGITISED` for a web-form map click; no accuracy figure is asserted for the fixture. Limits: the point does not establish which asset is meant; that is the nullable `asset_id`, set by a clerk.

**8.3.** Dictionary row: `pole_height_m` — "Height of the pole from ground level to the top of the pole, in metres, for streetlights only" — Double — metres — Null allowed; null = *not measured* (drains and trees have no pole; for those the field is not applicable and, in the 1:1 detail design, does not exist on their rows) — example `6.71` — source: surveyor or installation record. Conversions: `22 ft` → 6.7056 m (22 × 0.3048), recorded as 6.71 with rounding stated; `12 m` → 12.0; `7` and `6.5` → null with reason "unit not recorded; confirm with source" (or, if the instructor allows a documented assumption of metres, load them flagged `unit_assumed = true` in the conversion log — never silently); `0` on a drain → not applicable (no value). Only `22 ft` and `12 m` are convertible without returning to the source.

**8.4.** The contractor keyed results on `FID`, a storage row identifier assigned by the shapefile (the export). Any re-export, a sort, a deletion, or an edit in another tool can renumber rows, and the master geodatabase's own ObjectIDs need not match the shapefile's FIDs at all. The returned spreadsheet therefore attaches results to *positions in a file*, not to assets. The one change: include the business identifier `asset_id` in the export and instruct the contractor to key on it. (A label would fail for the same reason as in 8.4.1; a GlobalID would survive the export only if the shapefile carried it as text and the contractor used it, and is still not the key humans should use.)

**8.5.** A join in ArcGIS Pro returns target rows and appends matching join-table fields; rows with no match are not deleted, they simply have empty appended fields (and depending on the join settings a "keep only matching records" option can *hide* them). The three requests have `asset_id` null — they never matched an asset by design (optional relationship). Nothing is lost; the colleague read empty appended fields, or a filtered view, as lost rows. The mechanism that makes optionality explicit is the relationship definition with a *nullable* foreign key — in a database, `asset_id` nullable with a `FOREIGN KEY`; in a geodatabase, a simple relationship class where unrelated requests simply have no related row.

**8.6.** Check (a) whether `condition_code` has a **default** of 3 — if so, the values may be un-assessed visits wearing the default (8.6.2); and (b) the **null policy and domain**: whether null is allowed and whether the domain is a range 1–5 or a coded list — if nulls are disallowed and 3 is the default, the "fair" result is manufactured. A third useful check: `defects_found` for those visits (null would indicate incomplete checklists). Each reveals whether "3" is an observation or an artefact of the schema.

**8.7.** Overwriting `last_cleaned` keeps only the latest cleaning; the frequency, the crew, and the history are lost, and a cleaning that never happened cannot be told from one that did if the field has a default. Alternative: a `CleaningVisit` (or a visit-type code on Inspection) table, one row per cleaning, with `Asset.last_cleaned_at` as a *derived* cache refreshed by rule (8.7.1). The alternative still answers checklist questions 4 (which apps/reports reference the cached field), 6 (the verification query for the refresh), 7 (rollback), and 8 (owner and log); question 2 is easy because adding a table is reversible.

**8.4.3 schema exercise.** Add to Request a nullable field `duplicate_of_request_id` (Text, same type as `request_id`) holding the business identifier of the report it duplicates, plus the `DUP` status code. Rule: `duplicate_of_request_id` is required when `status = 'DUP'` and must be null otherwise (application/review layer). If the referenced report is later merged into a third report, P6's link still points at the *second* report, which itself now points at the third — a chain. Policy choice to write down: either follow the chain when reporting (accept chains), or re-point all duplicates to the surviving report at merge time (flatten). Either is acceptable if stated; silently leaving a link to a merged-away row is not. P6 is never deleted.

**8.6.3 trainee task (model).** (1) Schema — range domain; bypassed by a bulk load that ignores domains: caught by the review query `condition_code NOT BETWEEN 1 AND 5`. (2) Schema — relationship/foreign key where the platform enforces it; bypassed in a shapefile or by an import out of order: caught by the orphan query. (3) Application (the ordering workflow), because it is a cross-table, cross-process rule; bypassed by a phone order: review query of orders against null wattage. (4) Application (form) and review; no simple domain expresses a cross-field rule; bypassed by scripts: review query `visited_at > recorded_at`. (5) Review only (a spatial check; Chapter 11 tools); bypassed by everything: run weekly and route to human review, never auto-merge. (6) Application plus review; bypassed by direct edits: review query `status = 'DUP' AND duplicate_of_request_id IS NULL`.

## I.2 Answer key — 8.9 concept and scenario questions

**Q1.** (a) Asset, inspection visit, incident report. (b) Asset: `asset_id`, `asset_type`, `install_year`. Visit: `visit_date`, `condition`, `team`. Incident: `reporter_phone`, `report_date`. (c) `reporter_phone`: which question needs it, who is allowed to see it, and why is it on an asset/inspection table at all? If a callback workflow exists, it belongs on Request with an access rule; otherwise it is removed. Full marks require all three grains and a justified challenge.

**Q2.** Any two of: the points *represent* different things (surveyed pole base versus a reporter's estimate — 8.2.2); their *location methods and reliability* differ (SURVEY versus APPROX/GNSS — 8.2.3); their *grains* differ (physical thing versus event — 8.1.2), so a merged table cannot state what a row is. Correct design: Asset and Request tables, each with its own geometry specification, connected by an optional `asset_id` on Request.

**Q3.** `0` = checklist completed and no defects (visit can be closed); null = checklist not completed (re-inspect). With a default of 0, every incomplete checklist counts as "no defects", so the percentage with defects is understated by the number of incomplete visits — an invisible error. Hosted feature layer: date values are stored in UTC [X13]; a publisher whose source dates are IST must specify at publish time that the values are in the local time zone so that they are converted correctly [X13] (and, per the course convention, the dictionary states the convention).

**Q4.** **(c)** — the business identifier. (a) fails because ObjectIDs are maintained per table and may be regenerated on export/import or even at run time [X01] [X02]; (b) fails because labels are neither unique nor stable (8.4.1; the lab's duplicate names); (d) fails because GlobalIDs are regenerated by Append unless *Preserve Global IDs* is set, which applies only to enterprise geodatabase data with a unique index [X04] — "no further workflow configuration" is exactly the missing piece. Accept (d) only if the learner explicitly adds the preservation workflow, in which case the answer is no longer (d) as written.

**Q5.** (a) Conceptual — enforces nothing. (b) QGIS relation — enforced only while editing in QGIS; a script writing to the GeoPackage bypasses it [X21]. (c) Database foreign key — enforced against every client including scripts [X22]. (d) Join — a temporary layer property; enforces nothing [X08]. (e) ArcGIS relationship class (simple) — behaviour applied by ArcGIS clients that honour relationship classes [X06]; whether a given script path honours it is a verification item (I.7), so "enforced for ArcGIS-aware clients; verify for scripts" is the full-marks answer.

**Q6.** Stored code `OPEN`; displayed description "Open"; default `OPEN` on creation (an administrative fact, so a default is appropriate); a missing observation would be a null status — which this design forbids, because a request without a status is meaningless. Rules: (i) schema (coded-value domain), with a review query as backstop for bulk loads; (ii) application (cross-field rule) plus review; (iii) review (it is a work list, not an integrity rule).

**S1.** (a) Fault 1 — defaulted observed fields (`condition_code = 3`, `defects_found = 0`): 8.6.2. Fault 2 — event time replaced by entry time (`visited_at` taken at save, at the depot): 8.7.1. Fault 3 — no distinction between "assessed" and "not assessed", i.e. nulls disallowed or unused: 8.3.2. (b) Remove the defaults and allow null with dictionary meanings "not assessed"/"checklist not completed"; capture `visited_at` on site as event time (device time at form *start*, or a manual field) and keep a separate `recorded_at`/editor-tracking entry time [X12]; add a review query for visits with null assessments. (c) Even after the fixes, the dashboard cannot tell you whether the crews *actually inspected* what they recorded — only a photo, a second visit, or a supervisor check can; nor can it recover the true visit times of the historical rows already saved with depot times.

**S2.** (a) Import matching on the business identifier `asset_id`; since the contractor has none, matching is by *human-confirmed* street description → `asset_id`, never by description text alone; duplicates: a description matching two assets is unresolved, not assigned to the first; related records carry `asset_id`. (b) Rows with a single confirmed match (40 − 12 − 3 = 25) go into Inspection with `asset_id` set; the 12 unmatched and the 3 ambiguous (15 rows) go into a quarantine table with `asset_id` null and the raw description kept. Inspection's `asset_id` is not null because an inspection of nothing is meaningless; the quarantine table's is nullable because its grain is "an observation awaiting identification". The contractor's row numbers are kept as a `source_row` text field for traceability, never as a key. (c) `SELECT i.* FROM inspection i LEFT JOIN asset a ON a.asset_id = i.asset_id WHERE a.asset_id IS NULL` — expected zero rows; a second query counting quarantine rows (expected 15) proves nothing was dropped.

## I.3 Expected results — 8.8 lab

**Model defect list (minimum ten; learners may find more).**

| # | Defect in Table 8.8-A | Module |
| --- | --- | --- |
| 1 | Inspections stored as repeated column groups `Insp1_…Insp3_…`; a fourth visit (row 7) has nowhere to go | 8.1.2, 8.5.2 |
| 2 | `AssetName` used as the identifier; rows 1 and 2 share it | 8.4.1 |
| 3 | `Height` mixes units (`22 ft`, `12 m`, unitless `7`, `6.5`, `8`, and `0`) | 8.3.1, 8.3.3 |
| 4 | `Wattage` blank means *unknown* (rows 2, 3) and *not applicable* (rows 4, 5, 8) and `0` means *not applicable* (row 6) and possibly *no lamp* (row 7) — four meanings, two representations | 8.3.2 |
| 5 | Mixed date formats (`14/09/2025` versus `2026-03-14`) | 8.3.3 |
| 6 | No time of visit, no time zone, no entry time | 8.3.3, 8.7.1 |
| 7 | Team recorded as free text with three spellings for one team (`North`, `North crew`, `T-N`) | 8.5.1, 8.6.1 |
| 8 | Condition mixes words and a number (`Fair`, `3`) with no stated scale | 8.6.1 |
| 9 | Photos in one delimited field with inconsistent separators (`;` and `,`) | 8.7.2 |
| 10 | `Ward` holds `A/B` and `outside` — free text where the fact is spatial or a coded assignment; and row 4's `A/B` contradicts its coordinates (strictly in A) | 8.5.2, 8.2 |
| 11 | `Type` uses `Streetlight` and `Light` for the same category | 8.6.1 |
| 12 | Decommissioning recorded inside the name (row 7) rather than as a status field with a date | 8.3.1, 8.7.1 |
| 13 | History overflow acknowledged only in free-text `Notes` (row 7) | 8.7.1 |
| 14 | No business identifier, no location method, no CRS statement | 8.4.1, 8.2 |

**Model schema (one acceptable answer; learners' designs may differ).** Tables: `Asset` (point), `StreetlightDetail` (1:1 to Asset), `Inspection`, `Team`, `InspectionPhoto`; `Ward` derived spatially from Table F1 (or stored `ward_code` with reason). Relationships: Asset 1:M Inspection (`asset_id`, not null, delete = restrict); Team 1:M Inspection (`team_id`, not null, restrict); Inspection 1:M InspectionPhoto (`inspection_id`, not null, cascade acceptable with justification); Asset 1:1 StreetlightDetail (`asset_id` unique, cascade acceptable). Domains: `AssetType` {SL, DR, TR}; `AssetStatus` {ACTIVE, REMOVED, MERGED}; `ConditionScore` 1–5; `PoleHeightM` 3–15; `LocationMethod` {SURVEY, GNSS, DIGITISED, APPROX}. Timestamp convention: event times IST in the field, stored UTC.

**Identifier assignment used below (synthetic):** row 1 SL-0113, row 2 SL-0114, row 3 SL-0127, row 4 DR-0042, row 5 TR-0301, row 6 DR-0043, row 7 SL-0250, row 8 TR-0303 (TR-0302 already exists in the Chapter 3 fixture, at (1500, 700)); teams T-N (North) and T-S (South); inspections keep the Table F8-2 identifiers where the visit exists there and continue the single sequence (INS-0005 onward) for the new ones.

**Ten example records (model; synthetic).** The flat table has no time-of-day; the model uses the Table F8-2 times where the visit exists there. For visits that exist only in the flat table (rows 2, 3, 6, 7, 8) a learner must either record the date with a documented placeholder time or state an assumption — the model keeps those out of its ten rows to avoid inventing times.

*Asset (3 rows)*

| asset_id | asset_type | x | y | install_year | status | location_method | pole_height_m |
| --- | --- | --- | --- | --- | --- | --- | --- |
| SL-0113 | SL | 205 | 195 | 2018 | ACTIVE | SURVEY | null (unit not recorded) |
| DR-0042 | DR | 995 | 510 | 2011 | ACTIVE | SURVEY | — (not on drain rows) |
| TR-0301 | TR | 2190 | 520 | 2005 | ACTIVE | APPROX | — |

(Row 3's `22 ft` → 6.7056 m, rounded 6.71, belongs to SL-0127, which is converted in the learner's conversion log even if it is not among their ten rows.)

*Team (2 rows)*: T-N "North crew" active; T-S "South crew" active.

*Inspection (4 rows)*

| inspection_id | asset_id | visited_at (UTC) | recorded_on | team_id | condition_code | defects_found | remarks |
| --- | --- | --- | --- | --- | --- | --- | --- |
| INS-0001 | DR-0042 | 2024-10-15 04:20 | 2024-10-15 | T-S | 3 | 0 | null |
| INS-0002 | DR-0042 | 2025-11-02 09:35 | 2025-11-02 | T-S | 2 | 2 | Grating cracked; silt |
| INS-0003 | SL-0113 | 2026-03-14 05:12 | 2026-03-14 | T-N | 3 | 1 | Lamp flickers at dusk |
| INS-0004 | SL-0113 | 2025-09-14 05:45 | 2026-03-16 | T-N | 3 | 0 | null |

*InspectionPhoto (1 row)*: PH-0003-1, inspection INS-0003, `sl113_b.jpg`, image/jpeg, taken_at null, subject LAMP. (The flat table's `sl113_a.jpg; sl113_b.jpg` cannot be assigned to a specific visit from the source — the conversion log must say which assumption was made; the model assigns `_b` to the 2026 visit and leaves `_a` in the log as unassigned.)

Total: 3 + 2 + 4 + 1 = **10** rows. Orphan check: every Inspection `asset_id` ∈ {SL-0113, DR-0042} ⊂ Asset; every `team_id` ∈ {T-N, T-S} ⊂ Team; the photo's `inspection_id` ∈ Inspection. Zero orphans.

**Three business questions with answers from the model records (hand-checked, "today" = 2026-09-19).**

1. *Which assets have no inspection with `visited_at` on or after 2025-09-19?* Join Asset ← Inspection; SL-0113 has 2026-03-14 (yes), DR-0042 has 2025-11-02 (yes, after 2025-09-19), TR-0301 has none → **TR-0301**. (A learner whose ten rows include SL-0127 without its 2026-01-10 visit would also list SL-0127 — the answer depends on which rows were loaded, which is why the question must be answered *from their records* and the records must be shown.)
2. *How many inspections did team T-N perform in calendar year 2026 (local date)?* INS-0003 (05:12 UTC = 10:42 IST on 2026-03-14) counts; INS-0004 was *entered* in 2026 but *visited* in 2025 and does not → **1**. A learner who counts by `recorded_on` gets 2, which is the event-time/entry-time confusion of 8.7.1.
3. *What was DR-0042's condition at its most recent inspection, and when?* Maximum `visited_at` for DR-0042 is INS-0002 → **condition 2 (Poor) on 2025-11-02 (15:05 IST)**; INS-0001 (2024, condition 3) is the earlier visit.

Each question uses two tables and was unreliable against the flat table (question 1 needs "no rows" logic that column groups cannot express; question 2 needs a team key; question 3 needs "latest of many").

**Validation results expected:** as in 8.8.4 — 8 assets, 10 recorded observations plus 1 known unrecorded, 6 photo files, 2 teams; row 4 in Ward A by geometry; row 5 outside; 22 ft = 6.7056 m.

## I.4 Common mistakes and remediation

| Mistake | Where it shows | Remediation |
| --- | --- | --- |
| Merging the two same-name streetlights | Asset count 7 | Re-teach 8.4.1 with the coordinate difference (55 m, 5 m); ask the learner to explain what an inspector on site would see |
| Keeping "latest inspection" columns on Asset | Inspection count ≤ 8 | Re-teach 8.1.2 with Table F4's lost September 2025 all-clear |
| Inventing times of day or units | Conversion log absent | Require the log; a null with a reason scores higher than a guessed value |
| Turning blank wattage into 0 | Drain rows with `0` | 8.3.2 four-fact table; ask what a lamp order for a drain would cost |
| Cascade delete everywhere | Relationship definitions | 8.7.2 misconception; ask what happens to history when an asset is removed |
| ObjectID as the foreign key in the diagram | Relationship definitions | 8.4.2; assign the Q4 reasoning as a written exercise |
| Claiming the diagram enforces integrity | Enforcement statement missing | 8.5.3 table; require the per-relationship enforcement column |
| Storing descriptions instead of codes | Domain definitions | 8.6.1; show the translation cost |
| UTC conversion errors (adding instead of subtracting 5:30) | Inspection times | Redo the Table F8-2 conversions; check the previous-day edge case |

## I.5 Fresh exercise for retesting (different values, same concepts)

Provide a new flat table of **six library branches** with columns `BranchName`, `X`, `Y`, `FloorArea` (values `450`, `4800 sq ft`, `320 m2`, blank, `0`, `600`), `Visit1_Date`/`Visit1_Score`/`Visit1_By` through `Visit3_…`, `Docs` (delimited file names), and `Zone` (free text). Two branches share the name "Central". Ask for the same eight deliverables. Invariants for the instructor: 6 branches; count the visit observations from the table you issue; 4800 sq ft = 445.93 m² (4800 × 0.09290304 = 445.934…, state rounding); blank and `0` area need a null policy. Keep the wards/zones as a derived spatial fact using a fresh pair of rectangles so that the boundary case differs from DR-0042's.

## I.6 Optional implementation routes — not execution-tested

Everything in this section was written from the cited pages on 19 September 2026 and **has not been executed**. An instructor must run it once in the installed version, record the version and any label differences, and only then issue it. The design lab (8.8) does not depend on it.

**Procedure (version-specific) — ArcGIS Pro 3.7 documentation, file geodatabase.**

1. Create a file geodatabase and, inside it, a point feature class `Asset` with the training grid as an unknown/local planar spatial reference in metres (Chapter 7 and Chapter 5 discussed local frames; the exact dialog options for a local coordinate system must be verified in the installed version), and stand-alone tables `Inspection`, `Team`, `StreetlightDetail`. Field names must start with a letter and contain no spaces or reserved words [X16]; set data types and *Allow NULL* at creation, because the type can only be changed and Allow NULL can only be set to false while the table is empty [X17].
2. Create domains in the geodatabase — coded-value `AssetType`, `AssetStatus`, `LocationMethod`; range `ConditionScore` (1–5), `PoleHeightM` (3–15) — and assign them to the fields [S22]. When a domain is assigned, the field's default must be one of the domain's values (Esri's field-modification page; see I.7).
3. Add Global IDs to every table with the Add Global IDs tool (all licence levels) [X03].
4. Create relationship classes: `Asset`→`Inspection` (origin `Asset`, destination `Inspection`, origin primary key `asset_id`, destination foreign key `asset_id`, cardinality 1:M, **simple**); `Team`→`Inspection` (1:M, simple); `Asset`→`StreetlightDetail` (1:1 — created as composite 1:M and constrained to 1:1 with relationship rules if desired [X06], or simple 1:1). Key fields must be the same data type [X05]. Both tables must be in the same geodatabase [X05].
5. Enable attachments on `Inspection` (Catalog pane ► dataset ► Manage ► Attachments, or the Enable Attachments tool) — this creates `Inspection__ATTACH` and `Inspection__ATTACHREL` [X09] [X10]; leave *Global IDs* checked [X09].
6. Enable editor tracking on `Inspection` with *Record Dates in* = UTC [X12]; these fields are entry-time fields (8.7.1).
7. Load the ten example records, then verify: count rows per table; open an Asset row's attributes and confirm related Inspection rows appear; delete a *test* origin row on a throwaway copy and confirm the simple relationship class nulls the destination foreign key [X06]; attempt to enter `condition_code = 7` and confirm the domain rejects it in the attribute editor [S22]; run the orphan check as a join with "unmatched" selection.

**Verification items for the ArcGIS route:** the exact spatial-reference dialog options for a local planar frame; the relationship-class creation dialog labels and whether a 1:1 simple class can be created directly; whether an Append of the ten records honours domains; whether GlobalIDs survive a copy of the geodatabase (expected yes) and an Append (expected no, per [X04]).

**Procedure (version-specific) — QGIS Desktop 3.40 documentation, GeoPackage.**

1. Create a GeoPackage with a point layer `asset` (a planar/unknown CRS in metres — verify the option label in the installed version) and tables `inspection`, `team`, `streetlight_detail`, `inspection_photo`. Because a GeoPackage is SQLite, `NOT NULL`, `UNIQUE`, and `CHECK` constraints can be declared in the *DB Manager ► SQL Window*; the QGIS relations page notes that GeoPackage "doesn't support ADD CONSTRAINT statements" after creation, so constraints go in the `CREATE TABLE` [X21].
2. In *Project ► Properties ► Relations*, add relations `asset`→`inspection` (referenced layer `asset`, field `asset_id`; referencing layer `inspection`, field `asset_id`), `team`→`inspection`, `inspection`→`inspection_photo`; set strength *Association* for the first two and, if cascade is wanted for photos, *Composition* [X21]. Note that these are project-level settings [X21].
3. For each coded field, set the *Value Map* widget with code → description; for `condition_code`, the *Range* widget 1–5; add *Not null* constraints (hard) on `asset_id`, `inspection_id`, `visited_at`; add an expression constraint if desired [X20]. If domains were instead stored in the GeoPackage, QGIS assigns the widgets automatically [X20].
4. Set a `now()` default on an entry-time field with *Apply default value on update* if wanted [X20]; keep `visited_at` manual.
5. Load the records; open an asset's form and confirm the related inspections table appears [X21]; attempt an out-of-range condition and observe the soft/hard constraint behaviour [X20]; run the orphan check via *Join attributes by field value* or the SQL window.

**Verification items for the QGIS route:** the CRS option label for a planar/unknown frame in 3.40; whether the GeoPackage `CHECK` constraints are surfaced as provider constraints in the form; whether *Composition* deletes photos when an inspection is deleted in the installed version.

**Equivalence statement.** The two routes implement the same design but do **not** behave identically: ArcGIS stores domains and relationship classes *in the geodatabase*; QGIS stores widgets, constraints, and relations *in the project* unless they were declared in the GeoPackage itself. Learners comparing the two should be asked to name, for each rule, where it now lives (8.6.3).

## I.7 Blueprint corrections, source notes, and verification items

- **No factual error was found in the Chapter 8 blueprint text.** Its cautions about ObjectID stability and UUIDs versus synchronisation are *confirmed* by the documentation: query-layer ObjectIDs can be run-time values [X02]; Append regenerates GlobalIDs unless *Preserve Global IDs* is set, and that setting applies only to enterprise geodatabase data with a unique index [X04]; sync is a layer setting that adds the GlobalID for you [X14].
- **Blueprint URLs.** [S21] and [S22] resolved directly at `doc.esri.com`. The `pro.arcgis.com` address for the unique-identifier page returned HTTP 301 to `doc.esri.com` [X02]; the guessed addresses `.../overview/object-id-fields.html`, `.../overview/create-fields.html`, and `.../feature-classes/feature-class-basics.html` returned 404 and were replaced by the pages actually read ([X01], [X17], [X18]).
- **Version label.** No visible "Released version" banner was captured on the Esri pages read; the page metadata of the domains page carried `version 3.7`, which this document records as the ArcGIS Pro documentation release on the check date.
- **Domain enforcement scope.** The domains page states that a field with a domain "will not accept a value that is not in that domain" [S22]. The chapter reports this and adds a verification item, because the page does not say whether *every* load path (Append, scripts, ArcGIS Online CSV imports) applies the rule. Treat post-load review queries as mandatory regardless.
- **ObjectID persistence across copies.** The documentation read describes ObjectIDs as maintained per table and shows they can be run-time values [X01] [X02]; it makes no statement either way about matching values after a copy or export. The chapter therefore forbids relying on them, and lists an explicit before/after identifier comparison as a verification step for any workflow that wants more.
- **Domain default rule.** I.6 step 2 states that a field's default must be one of the domain's values; this appeared in a search summary of Esri's "Modification of field properties" page but was not among the sentences captured on the page read. Verify in the installed version before teaching it.
- **Editor-tracking help page [X25]** confirms the four tracked fields and the who/when scope; the UTC/database-time option and its default were read on the tool page [X12].
- **QGIS join storage.** The QGIS join is configured as a layer property and, like all layer properties, saved with the project; the page does not use the phrase "stored in the project", so the chapter says "a layer property … not the data" [X21].
- **Unverified procedures flagged in the text:** every step of I.6 (both routes); the ArcGIS relationship-class dialog details; the local/planar spatial-reference option labels in both products.

## I.8 Instructor change log

| Date | Change | Affected sections |
| --- | --- | --- |
| 2026-09-19 | Initial release, revision 1.0. No execution of software procedures. | All |

---

# Media Appendix

## M.1 Slide outline (mapped to module IDs)

| Slide group | Module | Slides | Learning objective of the sequence | Question to pose before revealing |
| --- | --- | --- | --- | --- |
| 0 | Front matter | 2 | State outcomes; show Table F4 and ask what is wrong with it | "How many inspections can this table hold per asset?" |
| 1 | 8.1 | 4 | Separate five entities; define grain; justify fields | "Which of these five things has its own location?" |
| 2 | 8.2 | 4 | Six-part geometry specification; point meaning per asset type; measured versus approximate | "P1 and SL-0113 are 7 m apart — is one of them wrong?" |
| 3 | 8.3 | 6 | Dictionary parts; type comparison table; four kinds of missing; codes as text; time-zone table with the IST→UTC conversions | "What does a blank wattage mean for a drain?" |
| 4 | 8.4 | 5 | Three identifiers; ObjectID and GlobalID facts and non-promises; identity policy | "Which column should the contractor's spreadsheet be keyed on?" |
| 5 | 8.5 | 6 | 1:M, 1:1, M:N with the fixture; Figure 8.1; the five-mechanism enforcement table | "Does drawing this line make orphans impossible?" |
| 6 | 8.6 | 5 | Code versus description; range domains; defaults are not observations; schema/application/review | "Every drain scored 3 last month — good news?" |
| 7 | 8.7 | 5 | Current state versus history; event versus entry time; photos as rows; orphan query; migration checklist | "When did this inspection happen: the visit or the save?" |
| 8 | 8.8 | 3 | Lab briefing: Table 8.8-A, deliverables, invariants | "Find ten defects in five minutes." |
| 9 | 8.9–8.10 | 2 | Gate rules; transition to Chapter 9 | — |

Speaker notes for every slide are drawn from the corresponding module text; each visual sequence carries one objective; the question is shown, a pause is given, then the answer slide follows.

## M.2 Audio-lesson outline

**Format:** one narrated lesson of roughly 35–40 minutes, structured as the lifecycle of asset SL-0113, with the detailed click instructions left to the lab handout.

**First-mention script for acronyms and terms.** "GIS — geographic information system"; "CRS — coordinate reference system, the thing from Chapter 5 that tells you what the numbers mean"; "IST — Indian Standard Time, five and a half hours ahead of UTC, Coordinated Universal Time"; "ER diagram — entity–relationship diagram, boxes for kinds of things and lines for how they refer to each other"; "ObjectID and GlobalID — two identifiers that ArcGIS adds to rows for its own use". Thereafter short forms only.

**Sequence.**

1. *Installation, 2018.* A pole goes up at (205, 195) on the training grid — "two hundred and five metres east, one hundred and ninety-five metres north; remember this grid has no real-world location". One row appears in the Asset table: business identifier SL-0113, type streetlight, installed 2018, location method survey, the point meaning the centre of the pole base. Pause: "Which of those facts will ever change?"
2. *The old table.* Describe Table F4 aloud: one row, with a column for the last inspection and its condition. "Picture a row with a slot for exactly one visit."
3. *September 2025, first inspection.* North crew arrives at 11:15 IST; condition 3 — fair; checklist complete, zero defects. In the new design a row appears in the Inspection table — not on the asset. Say the conversion aloud: "eleven fifteen IST becomes five forty-five UTC". "Zero defects means they looked and found nothing. If the field were empty, it would mean they did not look. Those are different sentences." Pause: "In the old table, where would this visit go?"
4. *March 2026, second inspection.* Same crew, 10:42 IST, fair, one defect: the lamp flickers at dusk. A second Inspection row; the asset row's cached fields update to one open defect. Then the twist: the September visit's paper form is found and typed in on 16 March — it gets the *next* number, INS-0004, although it happened first. "The number says when we wrote it down. The visit time says when it happened. Keep both."
5. *The photos.* Two files. Describe the wrong design — one text field with a semicolon — then the right one: one row per photo, each carrying the inspection's identifier.
6. *The diagram, verbally.* Describe Figure 8.1 as sentences: "An asset is inspected in zero or more inspections. An inspection has zero or more photos. A team performs many inspections. A team covers many wards through a coverage table." Pause: "Where does the team's name live — on the inspection, or in its own table?"
7. *The mechanisms.* One breath each for join, QGIS relation, relationship class, foreign key: what enforces, against whom.
8. *Decommissioning.* The pole is removed in a future year. "We do not delete the row. We set status to removed with a date. The two inspections stay. History is the point."
9. *Close.* The three questions the flat table could not answer, and the sentence "a schema is an interface: name an owner, fill the checklist, then change it".

Units are introduced explicitly at every first use ("metres", "watts", "the one-to-five condition scale"); the narrator never reads a time without its zone.

## M.3 Diagram specifications

**D1 — Entity–relationship diagram (Figure 8.1, redrawn).** Boxes: Asset (with a small point icon), Ward (polygon icon), Request (point icon), Inspection, Team, InspectionPhoto, StreetlightDetail, TeamWardCoverage — the last five with a "no geometry" tag. Crow's-foot ends: Asset—Inspection 1:M; Team—Inspection 1:M; Inspection—InspectionPhoto 1:M; Asset—StreetlightDetail 1:1; Asset—Request 1:0..M (dashed for optional); Team—TeamWardCoverage—Ward as two 1:M lines through the junction box. Key names printed on each line (`asset_id`, `team_id`, `inspection_id`, `ward_code`). Caption: "Instructional design for the training fixture; not an Esri-mandated schema. Synthetic."

**D2 — Field-definition cards.** One card per row of the Inspection dictionary (eight cards: `inspection_id`, `asset_id`, `team_id`, `visited_at`, `recorded_at`, `condition_code`, `defects_found`, `remarks`). Each card has seven labelled lines matching 8.3.1 (name, description, type, units, null policy, example, source), with the null policy line typeset larger than the rest. The `defects_found` card shows "null = checklist not completed; 0 = completed, none found" in two colours. Cards are the same size so they can be laid out as a grid or shown one per slide.

**D3 — Before/after record example.** Left panel, "Before": one row of Table 8.8-A (row 1) with the `Insp2_…` columns highlighted and an arrow labelled "overwrites" pointing from the March 2026 values onto the September 2025 values, which are shown crossed out. Right panel, "After": the Asset row (SL-0113) above two Inspection rows (INS-0004, INS-0003), each connected to the asset by a line labelled `asset_id = 'SL-0113'`, and one InspectionPhoto row connected to INS-0003. Times shown as "10:42 IST (05:12 UTC)"; INS-0004 carries a small tag "entered 2026-03-16". Caption: "Synthetic records; the training grid has no Earth location."

**D4 — Three identifiers (Table 8.4 as a graphic).** One asset drawn three times with a different tag: a badge "SL-0113" (business identifier, solid border, labelled "assigned by the organisation; never changes"), a speech bubble "Streetlight near Ward A market" (display label, dotted border, "may be re-worded"), and a small grey number "17" (ObjectID, "assigned by storage; valid in this dataset only"). A second copy of the asset in a second container shows the badge and the bubble unchanged and the grey number changed to "3".

**D5 — Enforcement ladder (8.6.3).** Three horizontal bands — Schema, Application, Review — with the six rules of the trainee task placed as chips in the band where they belong; an arrow along the right edge labelled "bypassed by → caught by" showing that a chip skipped in a higher band must be caught in Review.

**D6 — Time-zone conversion strip (8.3.3).** A horizontal line with three visit times marked in IST above and their UTC equivalents below, each connected by a vertical bar labelled "−5:30"; a fourth, greyed example at 02:00 IST shows the UTC mark landing on the previous day with the caption "local day ≠ UTC day".

All diagrams are schematic. None represents measured geography, and none asserts a real-world position for the fixture.

## M.4 Table for slides — where each rule is enforced (from 8.6.3)

| Rule | ArcGIS geodatabase | QGIS project on GeoPackage | PostgreSQL/PostGIS | Shapefile | Source |
| --- | --- | --- | --- | --- | --- |
| Value in a list | Coded-value domain | Value Map widget; expression constraint (soft/hard) | CHECK or lookup FK | None | [S22] [X20] [X22] [S20] |
| Number in bounds | Range domain | Range widget; constraint | CHECK | None | [S22] [X20] [X22] |
| Required value | Allow NULL = false (empty table only) | Not null constraint (soft/hard) | NOT NULL | Not representable | [X17] [X20] [X22] [S20] |
| Child needs parent | Relationship class behaviour on edit | Relation (QGIS editing only) | FOREIGN KEY | None | [X06] [X21] [X22] |
| Delete behaviour | Simple: null FK; composite: cascade | Association / Composition | NO ACTION / RESTRICT / CASCADE / SET NULL | — | [X06] [X21] [X22] |

Claims dated 19 September 2026; re-verify before reuse.

## M.5 Interactive and 3D material

**No 3D scene is proposed.** The chapter's concepts are structural, and a table or 2D diagram teaches each of them better, as the blueprint anticipates.

**One relationship animation is proposed, because it makes record ownership clearer (blueprint 8.10.2).**

- **Learning objective.** Show that moving inspection facts out of the flat row into their own rows does not lose any fact, and that the key is what keeps ownership.
- **Objects.** The eight rows of Table 8.8-A rendered as a flat grid; empty Asset, Inspection, Team, and InspectionPhoto grids beside it. Every cell value is a label; nothing is a measured quantity.
- **Labels and units.** Row 1 labelled "SL-0113"; inspection cells labelled with their dates; a persistent chip `asset_id = SL-0113` attached to each inspection cell as it moves; times shown as "IST (UTC)".
- **Controls.** *Step* (advance one row); *Play*; *Reset*; a toggle "show keys" that hides or shows the key chips; a counter panel showing Assets / Inspections / Photos / Teams.
- **Expected behaviour.** On each step, the row's asset facts become one Asset row; each non-empty `InspN_…` group becomes one Inspection row carrying the key chip; each photo file becomes one InspectionPhoto row; the team text snaps to a Team row (with the three spellings of North collapsing to one). Row 7's "4th visit" note produces a highlighted placeholder Inspection row with null condition and the caption "known event, no data". Row 4's ward text `A/B` triggers a caption "geometry says Ward A".
- **Validation.** At the end the counter must read **Assets 8, Inspections 11 (+1 placeholder), Photos 6, Teams 2**, matching 8.8.4; with "show keys" on, clicking any Inspection row highlights its Asset row (zero rows may highlight nothing — that is the orphan condition and must not occur). The animation is validated by comparing its final counts with 8.8.4 and its final rows with I.3.
- **Static alternative.** Diagram D3 and the counts table in 8.8.4 convey the same content without the animation.
- **Labels for synthetic content.** A permanent footer reads "Synthetic training records on a planar training grid; no real municipality, no Earth location."
