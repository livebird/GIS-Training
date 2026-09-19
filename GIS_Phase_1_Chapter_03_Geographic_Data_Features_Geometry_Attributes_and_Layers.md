# GIS and ArcGIS Fundamentals — Phase 1

# Chapter 3 — Geographic data: features, geometry, attributes, and layers

## Document metadata

| Item | Value |
| --- | --- |
| Chapter ID | Phase 1, Chapter 3 |
| Title | Geographic data: features, geometry, attributes, and layers |
| Source specification | `GIS_Phase_1_Chapter_Authoring_Blueprint.md`, revision 1.0 (18 September 2026) |
| Document revision | 1.0 — 19 September 2026 |
| Reference-check date | 19 September 2026 (every cited page was opened and read on this date; see the reference list for exact URLs) |
| Audience | Software developers at LiveBird Technologies who have completed Chapters 1–2 and have no other GIS background |
| Software referenced | ArcGIS Pro (documentation at `doc.esri.com/en/arcgis-pro/latest/`, which was labelled ArcGIS Pro 3.7 on the check date); QGIS Desktop 3.40 (LTR documentation edition) and the QGIS *Gentle Introduction to GIS* 3.44 edition named in the blueprint; ArcGIS REST API geometry documentation; PostGIS documentation (current online manual); RFC 7946 (GeoJSON) |
| Execution status | **No procedure in this chapter has been executed in ArcGIS Pro, QGIS, or any other software by the author.** Interface steps were written from the official pages cited beside them and are marked *not execution-tested*. All geometry answers (distances, areas, counts, extents) were checked by hand arithmetic and are marked *hand-checked*. |
| Data status | Every coordinate, record, name, and date in this chapter is **synthetic training material** on a flat metre grid with no Earth location and no coordinate-system identifier. Nothing describes a real municipality, school, park, or depot. |

### How to use this document

Read the modules in order; each one assumes the ones before it. Boxes labelled **Procedure (version-specific)** contain software steps that will age; everything outside those boxes is durable concept. Boxes labelled **Platform note** separate ArcGIS-specific behaviour from general GIS principles and from format rules. Boxes labelled **Verification item** tell an instructor what must be checked in the installed environment before a step is treated as fact. Comprehension checks have no answers in the learner text; answers are in the Instructor Appendix, which learners should not open before the progression gate in 3.8.

Words in **bold** at first use are defined in the glossary.

---

## Prerequisites

- **Chapter 1 completed.** You can frame a spatial question, separate policy from evidence, tell an event (request) from an asset and from a boundary (ward), and you have seen a prepared map with layers, a selection, and an attribute table.
- **Chapter 2 completed.** You know that a desktop application (ArcGIS Pro or QGIS) authors and inspects data, that a *project* references datasets rather than containing them, and that a *layer* in a web map or a desktop map is a use of data, not the data itself.
- Ordinary developer experience: you know what a table, a row, a column, a primary key, a foreign key, and a view are. These are used as analogies and are not taught here.
- Software is **optional** for modules 3.1–3.6 (read the described demonstrations). The guided lab in 3.7 uses ArcGIS Pro, with a QGIS route; a paper route is also given so that no learner is blocked by licensing.

## Learning outcomes

By the end of this chapter you should be able to:

| ID | Outcome | Where it is taught | Where it is checked |
| --- | --- | --- | --- |
| LO1 | Separate a real-world thing from its representation; choose point, line, or polygon for a stated purpose and scale; distinguish an asset, an incident at it, and an inspection of it; explain that a feature can be an administrative or analytical area, not only a visible object. | 3.1 | 3.8 concept Q1; scenario S1 |
| LO2 | Describe point, polyline, and polygon geometry in terms of vertices and segments; explain why a line's symbol width is not the road's ground width; state what X, Y, Z, and M are and that formats differ in which they allow. | 3.2 | 3.8 concept Q2; practical |
| LO3 | Recognise single-part and multipart features and polygons with holes; distinguish a polygon ring from a closed line; read a GeoJSON geometry and state its ring rules. | 3.3 | 3.8 concept Q3; scenario S2; practical |
| LO4 | Use the words record, field, value, feature identifier, and attribute table correctly; find the attributes of a selected feature; explain why a display label is not automatically the business identifier; explain why inspection history belongs in related records. | 3.4 | 3.8 concept Q4 |
| LO5 | Distinguish a dataset, a layer, and a map; explain how two layers can show one source differently; distinguish a filtered view from an exported copy; explain how drawing order can hide information. | 3.5 | 3.8 concept Q5; scenario S2 |
| LO6 | Read an extent as a bounding box; record feature count, geometry type, field names, and empty-geometry cases; predict which stored records change under a presentation change versus a geometry edit. | 3.6 | 3.8 concept Q6; practical |
| LO7 | Inspect a small schematic town: justify geometry choices, produce two presentations of one source without changing its feature count, and identify an unsuitable representation. | 3.7 | 3.7 lab; 3.8 practical; oral |

## Required materials

- This document and a way to draw (paper or any drawing tool).
- The **Chapter 3 fixture** (Tables F1–F9 below; all values are printed in this document). The lab data package in 3.7 is specified as CSV text you or an instructor can build; instructions are included.
- **For the lab (3.7):** ArcGIS Pro (any current licence level suffices for the steps used; verify in the installed release) **or** QGIS Desktop 3.40 or later. If neither is available, complete the paper route in 3.7.8; it covers every learning outcome except the software-observation deliverable.
- **Not required:** an ArcGIS Online account, ArcGIS Enterprise, or any publishing step.

## Recap of the preceding chapter

Chapter 2 named five responsibilities that every complete GIS solution must cover — desktop authoring, data storage, service delivery, content organisation, and user applications — and placed ArcGIS Pro, ArcGIS Online, ArcGIS Enterprise, the app builders, and the SDKs on those roles, with QGIS, PostGIS, GeoServer, OpenLayers, and Mapbox compared by role rather than as one-to-one replacements. Two ideas from Chapter 2 are used constantly here: a **project** (`.aprx`, `.qgz`) references datasets and does not contain them; and a **layer** in a map is a *use* of a dataset with its own presentation settings. Chapter 2 ended by asking what information actually flows through those systems. This chapter answers that: it is **vector data** — features made of geometry plus attributes — organised into datasets, shown through layers, and assembled into maps.

## The recurring scenario and the Chapter 3 fixture

The fictional municipality from Chapters 1–2 continues: it maintains **assets** (streetlights, drains, trees), keeps **road** centrelines, divides its territory into **wards**, receives **service requests**, and records **inspections**. The Chapter 1 fixture is reused: Wards A and B (1 km² squares), Road R1, requests P1–P6, and assets SL-0113, DR-0042, TR-0301. The five nouns `asset`, `inspection`, `request`, `road`, `ward` keep exactly their Chapter 1 meanings.

**Coordinate frame (unchanged).** All coordinates are on a flat, two-dimensional training grid measured in **metres**, written as **(x, y)** with x increasing to the right and y increasing upward. The grid has **no Earth location and no coordinate-system identifier** — treat it as graph paper. Chapter 5 will explain what a coordinate reference system adds; until then a coordinate is simply an *ordered pair of numbers*, and the order (x first, then y) is a rule of this fixture that you must not assume for other data.

**New for Chapter 3 (synthetic).** To teach geometry structure the fixture gains: a second road with a bend (R2), a closed loop line (L1), a school stored twice (as a point and as a footprint polygon), a two-part park (PK-01), a depot compound with a courtyard hole (DP-01), a tree inside that courtyard (TR-0302), a seventh request with **no geometry** (P7), a deliberately unsuitable "wards as points" table, and a non-spatial inspections table. Every addition is listed below; later chapters may add further fields and will say so.

**Table F1 — Wards (synthetic; unchanged from Chapter 1).**

| Ward | Vertices in order (x, y), metres | Area (hand-checked) |
| --- | --- | --- |
| A | (0, 0), (1000, 0), (1000, 1000), (0, 1000), back to (0, 0) | 1,000,000 m² = 1 km² |
| B | (1000, 0), (2000, 0), (2000, 1000), (1000, 1000), back to (1000, 0) | 1,000,000 m² = 1 km² |

**Table F2 — Roads (synthetic; Chapter 3 variant). Line geometry.**

| Road ID | Name | Vertices in order (x, y) | Length (hand-checked) | `width_m` (attribute) | Note |
| --- | --- | --- | --- | --- | --- |
| R1 | Main Road | (0, 500), (2000, 500) | 2,000 m | 12 | Unchanged from Chapter 1; finite segment |
| R2 | Station Road | (500, 0), (500, 500), (800, 900) | 500 + 500 = 1,000 m | 7 | Two segments; touches R1 at (500, 500) |
| L1 | Bus turning loop | (1300, 300), (1350, 300), (1350, 350), (1300, 350), (1300, 300) | 4 × 50 = 200 m | 6 | A **closed line**, stored as a line, not an area |

*Length of R2's second segment:* from (500, 500) to (800, 900) is Δx = 300, Δy = 400, so √(300² + 400²) = √250,000 = 500 m.

**Table F3 — Service requests (synthetic; Chapter 3 variant). Point geometry; P7 has none.**

| `request_id` | (x, y) | `category` | `priority` | `status` | `reported_on` | `closed_on` | `channel` | `note` |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| P1 | (200, 200) | Streetlight out | Medium | Open | 2026-09-02 | — | Mobile app | |
| P2 | (800, 800) | Pothole | High | In progress | 2026-08-28 | — | Phone | Crew assigned 2026-09-01 |
| P3 | (1200, 250) | Water leak | High | Open | 2026-09-10 | — | Mobile app | |
| P4 | (1700, 900) | Pothole | Low | Resolved | 2026-08-15 | 2026-08-20 | Web form | |
| P5 | (1000, 500) | Blocked drain | Medium | Reopened | 2026-08-30 | — | Phone | Closed 2026-09-05; reopened 2026-09-12 |
| P6 | (2200, 500) | Fallen tree | High | Closed – duplicate | 2026-09-11 | 2026-09-12 | Phone | Duplicate of a report not in this package |
| P7 | **(none)** | Pothole | Medium | Open | 2026-09-14 | — | Phone | Caller could not give a location; to be located |

**Table F4 — Assets (synthetic; Chapter 3 variant). Point geometry.**

| `asset_id` | `asset_type` | (x, y) | `installed` | `condition` | Note |
| --- | --- | --- | --- | --- | --- |
| SL-0113 | Streetlight | (205, 195) | 2018 | Fair | 8 m mounting height (see 3.2.3) |
| DR-0042 | Drain | (995, 510) | 2011 | Poor | |
| TR-0301 | Tree | (2190, 520) | 2005 | Unknown | Never inspected |
| TR-0302 | Tree | (1500, 700) | 2019 | Good | Stands in the depot courtyard (see Table F7) |

**Table F5 — School, stored two ways (synthetic).**

| Layer | Geometry | Coordinates | Hand-checked measure |
| --- | --- | --- | --- |
| `Schools_point` | Point | (400, 700) | — |
| `Schools_footprint` | Polygon | (370, 680), (430, 680), (430, 720), (370, 720), back to (370, 680) | 60 m × 40 m = 2,400 m² |

Both rows carry `school_id` = SC-01, `name` = "Ward A Primary School". The point is at the centre of the footprint.

**Table F6 — Park PK-01, a multipart polygon (synthetic).** One record, two parts.

| Part | Vertices in order | Area |
| --- | --- | --- |
| 1 | (100, 50), (300, 50), (300, 150), (100, 150), back to (100, 50) | 200 × 100 = 20,000 m² |
| 2 | (1300, 50), (1500, 50), (1500, 150), (1300, 150), back to (1300, 50) | 200 × 100 = 20,000 m² |

Total area of the one feature: 40,000 m². Part 1 lies in Ward A, Part 2 in Ward B.

**Table F7 — Depot DP-01, a polygon with a hole (synthetic).** One record, one outer ring and one inner ring.

| Ring | Vertices in order | Area enclosed |
| --- | --- | --- |
| Outer | (1400, 600), (1600, 600), (1600, 800), (1400, 800), back to (1400, 600) | 200 × 200 = 40,000 m² |
| Inner (hole) | (1450, 650), (1550, 650), (1550, 750), (1450, 750), back to (1450, 650) | 100 × 100 = 10,000 m² |

Area of the polygon = 40,000 − 10,000 = 30,000 m². Tree TR-0302 at (1500, 700) is inside the outer ring **and inside the hole**, so it is *not* inside the depot polygon.

**Table F8 — Wards as points (synthetic; deliberately unsuitable).**

| Ward | (x, y) |
| --- | --- |
| A | (500, 500) |
| B | (1500, 500) |

**Table F9 — Inspections (synthetic; non-spatial table).**

| `inspection_id` | `asset_id` | `inspected_on` | `condition_found` | `inspector` |
| --- | --- | --- | --- | --- |
| INS-0001 | DR-0042 | 2024-10-15 | Fair | crew01 |
| INS-0002 | DR-0042 | 2025-11-02 | Poor | crew01 |
| INS-0003 | SL-0113 | 2026-03-14 | Fair | crew02 |

**Hand-checked facts carried from Chapter 1:** straight-line distances to R1 are P1 = 300 m, P2 = 300 m, P3 = 250 m, P4 = 400 m, P5 = 0 m, P6 = 200 m (to the road's end point). P1, P2 strictly inside Ward A; P3, P4 strictly inside Ward B; P5 on the shared boundary; P6 outside both.

---

## 3.1 Separate the real-world object from its representation

### 3.1.1 The same school as a point and as a polygon

Look at the school in Table F5. The real school has a gate, three buildings, a playground, and a boundary wall. None of that is in the fixture. What the fixture holds are two **representations**: a single point at (400, 700), and a rectangle of four corners. Neither is "the school". Each is a deliberate simplification chosen for a purpose.

A **representation** is the shape and the values we choose to store about a real thing. The choice depends on the question you want to answer and the **scale** at which you will look. Scale is the ratio between a distance on the map and the distance on the ground: at 1:50,000 one millimetre on the map is 50 m on the ground; at 1:500 one millimetre is 0.5 m. The QGIS introduction makes the same point with a city: on a small-scale map covering a large area "it may make sense to represent a city using a point feature", but as you zoom in "it makes more sense to show the city limits as a polygon" [S10, §3.2].

**Table 3.1 — What each representation of the school can and cannot answer.**

| Question | `Schools_point` (one point) | `Schools_footprint` (polygon) |
| --- | --- | --- |
| How many schools are in Ward A? | Yes — count points inside the ward | Yes — but you must decide what "inside" means for an area that could straddle a boundary |
| Which requests are within 300 m of the school? | Yes, measured from the point (400, 700) — a reasonable overview answer | Yes, measured from the nearest edge; the answers differ near the building |
| How large is the school site? | No — a point has no area | Yes — 2,400 m² (hand-checked) |
| Does the new footpath cross school land? | No | Yes |
| Where should the "school" marker go on a city overview map at 1:50,000? | Yes — the point | The 60 m × 40 m rectangle is 1.2 mm × 0.8 mm at that scale, too small to be seen as a shape |
| Where is the gate? | No | No — the footprint is a rectangle; the gate was never captured |

The last row matters most. A polygon looks more "complete" than a point, but it only holds what was captured. **A richer geometry does not mean more information; it means different information.**

**Worked example — choosing for the 300 m question.** Request P1 is at (200, 200). Distance from P1 to the school point (400, 700): Δx = 200, Δy = 500, √(200² + 500²) = √290,000 ≈ 538.5 m. Distance from P1 to the nearest point of the footprint (its lower-left corner (370, 680)): Δx = 170, Δy = 480, √(170² + 480²) = √259,300 ≈ 509.2 m. Both are well over 300 m, so for P1 the choice does not change the answer. But if a request lay at (400, 400), the point gives exactly 300 m (a boundary case under a "≤ 300 m" rule) while the footprint's bottom edge at y = 680 gives 280 m — clearly inside. The representation you pick changes which records qualify. Record the choice.

**Developer analogy.** A point-versus-polygon choice is like storing a customer's address as a single geocoded pin versus storing the parcel outline. Both are "the address"; one serves a delivery list, the other a property-tax map. *Where the analogy stops:* in software you can often add fields later without disturbing the old ones; in GIS a layer holds **one geometry type** (see 3.2.1), so switching from point to polygon means a second dataset, not an extra column.

**Misconception:** "The polygon is the accurate version and the point is the approximate version." *Consequence:* teams discard point layers that were fit for purpose, or trust a footprint's boundary as a legal line when it was sketched from an aerial image. Both representations are approximations; each is accurate enough for some questions and not for others (Chapter 1, fitness for purpose).

> **Comprehension check 3.1.1.** A planner wants to know how many wards each school "serves", defined as "the ward containing the school". Which representation would you use, and what extra decision does the other representation force on you?

### 3.1.2 Asset, incident, and inspection record

Three different things can share one location and must never be merged into one record. Table F4 has drain **DR-0042** at (995, 510). Table F3 has request **P5**, "Blocked drain", at (1000, 500) — five metres away, almost certainly about this drain. Table F9 has **INS-0002**, an inspection of DR-0042 on 2025-11-02 that found it "Poor".

| Kind | Fixture example | What it is | Lifetime | What changes over time |
| --- | --- | --- | --- | --- |
| **Asset** | DR-0042 | A thing the municipality owns and maintains | Years; exists until removed | Its *current* state (condition, last inspection) |
| **Incident** (here, a request) | P5 | Something that happened or was reported at a place and time | Opens, progresses, closes; may reopen | Its status and handling; the *observation* itself should not be rewritten |
| **Inspection record** | INS-0002 | An observation someone made about an asset on a date | Fixed once written | Nothing — a new visit creates a new record |

The distinction was previewed in Chapter 1 (event vs asset vs boundary). Here it becomes a data-structure decision: assets, requests, and inspections are **three collections**, each with its own geometry decision. Assets and requests carry geometry (points). Inspections in this fixture carry **no geometry**; they point at an asset through `asset_id` (Table F9), and the asset supplies the location. Chapter 8 will formalise this as a relationship; for now notice that it would be wrong to give INS-0002 its own copy of the drain's coordinates — if the drain's position is corrected, three inspection rows would silently disagree with it.

**Misconception:** "Put the latest inspection result in the asset row and you are done." *Consequence:* the second inspection overwrites the first; the trend (Fair in 2024 → Poor in 2025) is lost, which is exactly the evidence a maintenance planner needs. Table F4's `condition` column *is* a convenience summary, but Table F9 is the record.

> **Comprehension check 3.1.2.** A crew replaces streetlight SL-0113 with a new pole at the same spot. Which of the following should happen to the records: (a) SL-0113's `installed` year is changed to 2026; (b) SL-0113 is retired and a new asset row is created; (c) request P1 is edited to point to the new asset? Explain your choice in two sentences.

### 3.1.3 What a feature is

A **feature** is a represented entity: **one geometry plus its descriptive properties (attributes)**, stored as one record. DR-0042 is a feature. Ward A is a feature. Request P5 is a feature.

Three points about this definition matter for developers:

1. **Features need not be visible objects.** The QGIS introduction says "a feature is anything you can see on the landscape" [S10, §3.1]; that is a helpful starting picture but too narrow for this course. Ward A is a feature, and nobody can see a ward boundary on the ground. Esri's list of polygon feature-class examples includes "states, counties, parcels, soil types, and land-use zones" [X01] — administrative and analytical areas, several of them invisible. A "300 m service zone" computed in Chapter 11 will also be a feature. **Instructor note:** this is a source-coverage difference, not an error in the blueprint, which explicitly asks for the broader definition (blueprint 3.1.3).
2. **A feature may have no geometry.** GeoJSON allows a Feature whose `geometry` is `null` for "the case that the Feature is unlocated" [S18, §3.2]. Request P7 in Table F3 is such a feature: a real report with attributes but no position yet. Whether a particular dataset format or database *allows* this varies; ArcGIS Pro's XY Table To Point tool, for instance, writes "an empty geometry" for rows whose coordinates are null [X20]. Module 3.6 shows how to find such records.
3. **A feature is one record.** It has exactly one geometry value (which may be multipart — 3.3.1) and one set of attribute values. "Three drains" are three features.

**Developer analogy.** A feature is a row in a table that has one column of a special type, *geometry*, next to ordinary typed columns. ArcGIS names that column `SHAPE` and states that "each feature class can contain only one geometry type field" [X05]; PostGIS stores it in a column of type `geometry`. *Where the analogy stops:* unlike a text or number column, the geometry column has its own structure (vertices, parts, rings) and its own rules of validity, and most GIS engines constrain all rows in the table to one geometry type (3.2.1).

**Diagram D1 (described; specification in the Media Appendix).** *Left:* a hand sketch of a street with a drain, a school, and a ward line. *Middle:* the same scene reduced to a point, a rectangle, and a square outline with labelled vertex coordinates. *Right:* three "attribute cards" attached by lines to the three shapes: `asset_id = DR-0042`, `school_id = SC-01`, `ward = A`. Caption: "Real thing → geometry → attributes = one feature per record. Synthetic training grid; no real-world location."

> **Comprehension check 3.1.3.** Which of these are features in the fixture, and which are not: (i) Ward B; (ii) the 300 m distance from P1 to R1; (iii) inspection INS-0003; (iv) the fixture's x-axis? Give one reason for each.

---

## 3.2 Teach basic vector geometry in order

### 3.2.1 Point, polyline, polygon: vertices and segments

**Vector data** represents things as shapes built from coordinates. A **vertex** (plural vertices) is one stored position — in this fixture an (x, y) pair. Every vector shape is a list of vertices plus a rule for how to read the list [S10, §3.1: "The geometry is made up of one or more interconnected vertices"].

| Geometry type | Made of | Fixture example | Rule for reading the vertex list | What it can represent |
| --- | --- | --- | --- | --- |
| **Point** | One vertex | Asset DR-0042 at (995, 510) | The position itself | A location with no extent at this scale [S10, §3.2: "A point feature has an X, Y and optionally, Z value"] |
| **Line / polyline** | Two or more vertices; a **segment** joins each consecutive pair | Road R2: (500, 0) → (500, 500) → (800, 900) | Walk the vertices in order; the shape is the path | Centrelines: "roads, rivers, contours, footpaths" [S10, §3.3] |
| **Polygon** | A **ring**: a vertex list whose last vertex repeats the first, enclosing an area | Ward A: (0, 0), (1000, 0), (1000, 1000), (0, 1000), (0, 0) | Walk the ring; everything enclosed is inside | "Enclosed areas like dams, islands, country boundaries" [S10, §3.4] |

Two properties follow directly from the vertex lists (hand-checked):

- **Road R2** has 3 vertices and therefore 2 segments. Segment 1 is vertical, 500 m; segment 2 is diagonal, 500 m; total 1,000 m. A polyline's length is the sum of its segment lengths — it is *not* the straight-line distance between its ends (from (500, 0) to (800, 900) that is √(300² + 900²) ≈ 948.7 m).
- **Ward A** has 4 distinct corners but 5 stored vertices, because the ring is closed by repeating the first vertex. Its area, 1,000,000 m², is the area enclosed, not a property of any vertex.

**Platform note — Esri feature classes and the "one type per collection" rule.** Esri defines a feature class as a "homogeneous collection of common features, each having the same spatial representation—such as points, lines, or polygons—and a common set of attribute columns" [X01]. QGIS states the same rule for layers: "Features in a layer have the same geometry type (e.g. they will all be points) and the same kinds of attributes" [S10, §3.5]. This is why the school is stored *twice* (Table F5): a points layer and a polygons layer, never both shapes in one. Esri additionally lists **multipoint** (one feature made of several points, used for very large point sets), **multipatch** (3D shells), **annotation**, and **dimension** feature classes [X01]; these are out of scope for Phase 1 and are named only so that you recognise the words.

**Platform note — segments are not always straight.** In this fixture every segment is a straight line. Esri geometry may also use "circular arcs, elliptical arcs, or Bézier curves between vertices" [X01]. GeoJSON has no curves: a LineString is simply "an array of two or more positions" [S18, §3.1.4]. If curved data is exported to a straight-segment format it must be approximated. Chapter 7 returns to this.

**Developer analogy.** A polyline is an ordered array of points; a polygon is such an array with a "closed" invariant (`first == last`). Think of `[(500,0), (500,500), (800,900)]` as a path drawn with `lineTo` calls. *Where the analogy stops:* in most drawing APIs the *path* is the object; in GIS the *segments between* vertices are also geometry with length and position, and operations such as "distance to the road" measure to the nearest point on a segment, not to the nearest vertex. P1's 300 m to R1 is to the point (200, 500) on the segment, which is not a stored vertex.

**Misconception:** "More vertices = more accurate." *Consequence:* datasets bloated with vertices copied from a curved source, or the opposite — a river straightened to two vertices and then used for a 50 m proximity rule that the missing bends would have changed. Vertex density should match the purpose and the capture method (Chapter 9); the QGIS introduction notes that polylines "may look very angular" when zoomed in if vertices are sparse [S10, §3.6].

> **Comprehension check 3.2.1.** The bus loop L1 in Table F2 has five stored vertices, of which the last equals the first. How many segments does it have, and what is its length? Then state one reason it might still be correct to store it as a line rather than as a polygon.

### 3.2.2 A line's symbol width is not the road's ground width

Road R1 is stored as a segment with zero width: a line has length but no area. On a map it is drawn with a **symbol** — a stroke of some width, colour, and style. The stroke width is a **display decision**, chosen in the layer's symbology, and it has nothing to do with the road's real width, which is a separate fact stored in the attribute `width_m` = 12 (Table F2).

**Worked example — what a 2-point stroke means on the ground.** Symbol widths are usually given in *points* (pt), a printing unit: 1 pt = 1/72 inch ≈ 0.353 mm. A 2 pt stroke is about 0.71 mm wide *on the page or screen*. What that width "means" on the ground depends only on the map scale:

| Map scale | Ground width represented by a 0.71 mm stroke | Real width of R1 (12 m) drawn at this scale |
| --- | --- | --- |
| 1:50,000 | 0.71 mm × 50,000 ≈ 35 m | 12 m ÷ 50,000 = 0.24 mm — thinner than the stroke |
| 1:5,000 | 0.71 mm × 5,000 ≈ 3.5 m | 2.4 mm — the road is wider than the stroke |
| 1:500 | 0.71 mm × 500 ≈ 0.35 m | 24 mm — the stroke is a thin line down the middle of a wide road |

At 1:50,000 the symbol is three times wider than the road; at 1:500 the symbol is a thread. The stored geometry is identical in every row. (These are arithmetic consequences of the unit definitions; hand-checked.)

**When an area representation is necessary.** Use a line while the question is about *connection, length, or position along* the road: "which requests are within 300 m of R1?", "how long is Station Road?", "which roads cross ward boundaries?". Switch to a **polygon** (a road *surface* or carriageway area) when the question is about the *area itself*: "how many square metres of asphalt need resurfacing?", "does the proposed kiosk overlap the carriageway?", "which side of the kerb is the drain on?". A 12 m width stored as an attribute lets you *estimate* area (2,000 m × 12 m = 24,000 m² for R1) but cannot tell you the shape of a junction or a widening at a bus stop. If the municipality later needs pavement-condition surveys by lane, it needs a polygon layer, captured for that purpose, in addition to the centreline.

**Developer analogy.** Stroke width in a map symbol is like `border-width` in CSS: it changes rendering, not the model. *Where the analogy stops:* in CSS the border is drawn around a box that has real dimensions; in GIS the line has *no* box at all, so nothing in the geometry tells you where the kerb is.

**Misconception:** "I made the road symbol 12 m wide using a real-world unit, so now the map shows the true road." *Consequence:* the symbol will scale correctly, but it is still centred on a centreline that may not be the geometric centre of the carriageway, and it cannot show a lay-by, a median, or a widening. It is a better picture, not a measurement.

> **Comprehension check 3.2.2.** A colleague asks for "the area of all roads in Ward A" and proposes multiplying each road's length by `width_m`. Name one question this estimate answers acceptably and one it answers wrongly, and say what data would be needed for the second.

### 3.2.3 Coordinate dimensions: X, Y, optional Z, and measures

Every vertex in this fixture has exactly two values, x and y. Real formats allow more, and they do not agree on which.

| Dimension | Meaning | Fixture illustration (synthetic) | Notes |
| --- | --- | --- | --- |
| **X, Y** | Position in the plane | (995, 510) | Always present. Their *interpretation* (metres? degrees? which is first?) is what a coordinate reference system supplies — Chapter 5. In this fixture x is first and both are metres by declaration. |
| **Z** | A third coordinate, usually height or depth | Streetlight SL-0113 could be stored with z = 0.0 at its base; the *lamp* is 8 m up, but that is an attribute (`mount_height_m` = 8), not a coordinate | A Z value is one number per vertex. "z-values … are used to represent elevations or other measurements" [X01]. Storing z at the base does **not** make the streetlight a 3D object; it makes it a point at a stated height. See the 3D scene note in 3.9. |
| **M** ("measure") | A value along a line that is *not* a spatial axis — distance along a route, time, chainage | Road R1 could carry m = 0 at (0, 500) and m = 2000 at (2000, 500) so that "pothole at m = 800" locates a point 800 m from the start without a coordinate | "Linear feature vertices can also include m-values", used for "linear referencing systems" such as highway mileposts [X01]; PostGIS says M "may represent time or distance" [X22, §4.1.1]. Linear referencing is beyond Phase 1; you only need to recognise the letter. |

**Format rules differ — check before assuming.**

| Format / engine | Allowed per vertex | Source |
| --- | --- | --- |
| GeoJSON (RFC 7946) | Two or more elements; "Altitude or elevation MAY be included as an optional third element"; "Implementations SHOULD NOT extend positions beyond three elements" — so **no M** | [S18, §3.1.1] |
| Esri geometry (REST JSON, geodatabase) | x, y, optional z, optional m; polylines and polygons carry `hasZ` / `hasM` flags | [X02]; [X01] |
| PostGIS (OGC Simple Features) | XY, XYZ, XYM, or XYZM | [X22, §4.1.1] |
| QGIS new layers (GeoPackage, temporary scratch layer) | Geometry type plus optional "Include Z dimension" and "Include M values" | [X28] |

So a road with measures stored in a geodatabase or PostGIS **cannot be written to GeoJSON without losing M**. Chapter 7 treats such conversion checks; here the lesson is that "a coordinate" is not one fixed thing.

**Developer analogy.** X, Y, Z, M are like a tuple type: `(float, float)`, `(float, float, float)`, or `(float, float, float, float)`. A schema that accepts only 2-tuples will reject or truncate a 4-tuple. *Where the analogy stops:* Z and M are not interchangeable third elements — Z is a spatial axis with a vertical reference (Chapter 5), M is not spatial at all — so "the third number" in a file must be read with its format's rule, never guessed.

**Misconception:** "If a vertex has a Z, the dataset is 3D." *Consequence:* a points layer with Z is treated as if it describes building heights or volumes. A Z per vertex gives each vertex a height; it does not give a point a roof, or a footprint polygon walls (3.9.2). True solids are a different geometry type (Esri's multipatch [X01]) and are outside Phase 1.

> **Comprehension check 3.2.3.** The municipality receives a GeoJSON file of streetlights in which each position has three numbers. The supplier says the third number is "distance from the start of the street". Using the GeoJSON rules quoted above, explain what is wrong with this file and what you would ask the supplier.

---

## 3.3 Introduce geometry structure and exceptions

### 3.3.1 Single-part and multipart features; polygons with holes

Most features are **single-part**: one point, one connected path, one ring. Two structures break that simplicity, and both appear in the fixture.

**Multipart features.** Park PK-01 (Table F6) is *one* park that happens to be two separate pieces of ground, one in each ward. The municipality treats it as one asset with one name, one budget, one `park_id`. It is stored as **one record whose geometry has two parts**. Esri: "line and polygon feature classes can comprise single or multiple parts", with the islands of Hawaii as one state feature [X01]. In the OGC / GeoJSON / PostGIS vocabulary the same idea is a **MultiPolygon**: "an array of Polygon coordinate arrays" [S18, §3.1.7]; PostGIS lists MultiPoint, MultiLineString, and MultiPolygon as collection types [X22, §4.1.1].

Consequences you must be able to predict:

- **Count.** The parks layer has a feature count of **1**, not 2. A report "parks per ward" that counts *features* inside each ward gives A = 0, B = 0 if the engine requires the whole feature to be inside (neither ward contains all of PK-01), or A = 1, B = 1 if it counts any overlap — a boundary-rule question in the spirit of Chapter 1, revisited formally in Chapter 10.
- **Area.** The one feature's area is the sum of its parts: 40,000 m². "Area of parks in Ward A" cannot be read from the feature's area field; it needs the part in A (20,000 m²), which only an overlay operation (Chapter 11) or a split into single-part features gives.
- **Attributes.** There is one row, so a single `condition` value applies to both pieces. If Part 2 is flooded and Part 1 is fine, the row cannot say so. That is a reason to store two records instead — see the decision table below.
- **Extent.** The bounding box of PK-01 spans x = 100 … 1500 (3.6.1), covering a kilometre of ground that is not park.

**Table 3.2 — One multipart record or several single-part records?**

| Store as **one multipart feature** when… | Store as **separate features** when… |
| --- | --- |
| The pieces are managed, named, and reported as one unit | The pieces have different attributes (condition, owner, opening hours) |
| Attribute values are the same for all pieces by definition | You need to count, select, or edit pieces individually |
| The external system that consumes the data expects one ID per unit | Boundary questions ("in which ward?") must have one answer per piece |

Both choices are legitimate; the wrong choice is the *unrecorded* one. Software can convert either way: ArcGIS Pro's **Multipart To Singlepart** tool "creates a feature class of singlepart features by separating multipart input features", keeps the attributes on every output row, and adds an `ORIG_FID` field holding the input feature ID [X12]; QGIS's **Multipart to singleparts** algorithm does the equivalent [X26], and **Collect geometries** goes the other way [X26].

**Polygons with holes.** Depot DP-01 (Table F7) is a compound with an open courtyard. The courtyard is not depot land — it is a **hole**. A polygon is defined by an **exterior ring** and zero or more **interior rings**; PostGIS: "an exterior boundary (the shell) and zero or more interior boundaries (holes). Each boundary is a LinearRing" [X22, §4.1.1.4]. GeoJSON: "the first MUST be the exterior ring, and any others MUST be interior rings" [S18, §3.1.6].

Consequences:

- **Area** is outer minus holes: 40,000 − 10,000 = 30,000 m².
- **Containment.** Tree TR-0302 at (1500, 700) lies inside the outer ring but inside the hole, so it is **not inside DP-01**. A query "assets inside the depot" must return SL/DR/TR assets in the ring-minus-hole area only; an engine that tested only the outer ring would wrongly include TR-0302.
- **A hole is not a feature.** Nothing in the depot layer represents "the courtyard". If the courtyard is itself a managed thing (a garden), it needs its own record in a suitable layer — possibly a polygon whose exterior ring coincides with the depot's interior ring.

**Diagram D3 (described).** *Panel A:* the two park rectangles far apart, both hatched the same way, with one attribute card "PK-01, parts = 2, area = 40,000 m²". *Panel B:* the depot square with the courtyard square cut out, the tree symbol in the courtyard, and a card "DP-01, rings = 2 (1 exterior, 1 interior), area = 30,000 m²". Both panels labelled "schematic; synthetic grid; metres".

**Developer analogy.** A multipart geometry is like a one-to-many child list inside a single record (`parts: [ ... ]`) — the record is still one row. A hole is like a subtraction in constructive geometry. *Where the analogy stops:* a JSON list may be empty, reordered, or nested freely; geometry parts and rings must obey the format's structural rules (3.3.3), and a ring that crosses another ring makes the whole polygon invalid (Chapter 9).

**Misconception:** "If two shapes have the same name, they are two features with duplicate names." *Consequence:* a developer "deduplicates" a multipart layer by deleting the second row — and finds there was only one row, or splits a legitimately multipart feature into two records without updating the identifier, breaking the link to the asset register. Always check the feature count *and* the part count.

> **Comprehension check 3.3.1.** The municipality decides that "park area per ward" must be reported every month. Using Table 3.2, state which storage choice makes that report simplest, and what is lost by that choice.

### 3.3.2 A polygon ring is not a closed line

The bus loop L1 (Table F2) and the depot's outer ring (Table F7) look alike on paper: both are vertex lists that end where they start. They are **different kinds of geometry**:

| | L1 (Table F2) | DP-01 outer ring (Table F7) |
| --- | --- | --- |
| Stored in a layer of type | Line | Polygon |
| Has length? | Yes, 200 m | The ring's perimeter is 800 m, but *the feature's* primary measure is area |
| Has area? | **No** — 0 m² by definition; the "enclosed" 2,500 m² is not part of the geometry | Yes, 30,000 m² |
| A point at its centre, e.g. (1325, 325) — is it "inside"? | Not meaningful: a line has no inside. Distance from (1325, 325) to L1 is 25 m | (1500, 700) is inside the outer ring, in the hole; (1425, 625) is inside the polygon |
| Why this type? | Buses *drive along* it; the question is connectivity and length | The depot *occupies* it; the question is what lies within |

The closed shape of L1 is a **coincidence of the path**, not a statement that the enclosed asphalt belongs to anything. If the municipality also needs "the area inside the loop" (a traffic island to be planted), that is a separate polygon feature. The QGIS introduction's rule that for polygons "the first and last vertices should always be at the same place" [S10, §3.4] is *necessary* for an area, not *sufficient* — the vertex list must also be stored **as a polygon** for the software to treat it as one.

**Validity rules are deferred.** Whether a ring may touch itself, whether holes may touch the shell, what happens when a ring is left unclosed or is stored with the wrong orientation — these are validation questions covered in Chapter 9. For orientation the fixture quietly follows the strictest common convention (3.3.3); ArcGIS Pro's **Check Geometry** tool reports problems such as "Unclosed rings", "Incorrect ring ordering", "Incorrect segment orientation", "Self intersections", and "Null geometry" [X06] — read that list now only to recognise the vocabulary.

**Misconception:** "Software can tell it is a polygon because it closes." *Consequence:* a road-survey team digitises property boundaries as closed lines in the roads layer; every "area" and "inside" query later returns nothing, and converting requires a new polygon layer plus attribute transfer.

> **Comprehension check 3.3.2.** A dataset called `Fences` stores each fence as a line. Some fences fully enclose a compound. A colleague proposes "convert the closed ones to polygons so we can compute the compound areas". Give one reason this may be correct and one reason it may produce wrong areas.

### 3.3.3 Serialisation differs by format: a GeoJSON reading

The same geometry is written differently by different formats. You will meet three written forms in this course. Read them; do not yet worry about coordinate reference systems.

**(a) WKT — Well-Known Text (the OGC text form used by PostGIS, GeoPackage tooling, and many libraries).** The depot with its hole, then the two-part park, as WKT on the training grid:

```text
POLYGON((1400 600, 1600 600, 1600 800, 1400 800, 1400 600),
        (1450 650, 1550 650, 1550 750, 1450 750, 1450 650))

MULTIPOLYGON(((100 50, 300 50, 300 150, 100 150, 100 50)),
             ((1300 50, 1500 50, 1500 150, 1300 150, 1300 50)))
```

Coordinates are `x y` pairs separated by spaces; rings are parenthesised lists; the first ring is the shell and later rings are holes [X22, §4.1.1.4, §4.1.3]. WKT carries no coordinate-system statement in the text itself, which is why the lab data package uses it (3.7.2). The PostGIS section read for this chapter does not impose a direction of travel around a ring; check an engine's validity rules before relying on orientation.

**(b) GeoJSON (RFC 7946) — structure only.** The same depot, written in GeoJSON form:

```json
{
  "type": "Feature",
  "id": "DP-01",
  "geometry": {
    "type": "Polygon",
    "coordinates": [
      [[1400, 600], [1600, 600], [1600, 800], [1400, 800], [1400, 600]],
      [[1450, 650], [1450, 750], [1550, 750], [1550, 650], [1450, 650]]
    ]
  },
  "properties": { "depot_id": "DP-01", "name": "Central depot" }
}
```

> **Do not save this as a `.geojson` file.** RFC 7946 §4 fixes the coordinate reference system of every GeoJSON file to WGS 84 with "longitude and latitude units of decimal degrees" [S18, §4]. The numbers above are metres on a training grid; 1400 is not a longitude. The snippet is shown only so that you can read its **structure**. Chapter 5 explains why this matters; Chapter 7 shows what a valid file looks like.

Reading the structure against the RFC:

1. **Positions.** Each `[1400, 600]` is a *position*: "an array of numbers. There MUST be two or more elements. The first two elements are longitude and latitude, or easting and northing, precisely in that order" [S18, §3.1.1]. That is, **x-like value first, then y-like value** — the same order as this fixture. Chapter 5 discusses why other systems write latitude first and how much trouble that causes.
2. **Rings.** "A linear ring is a closed LineString with four or more positions. The first and last positions are equivalent, and they MUST contain identical values" [S18, §3.1.6]. Both rings above have five positions with first = last.
3. **Orientation.** "A linear ring MUST follow the right-hand rule with respect to the area it bounds, i.e., exterior rings are counterclockwise, and holes are clockwise" [S18, §3.1.6]. Check the exterior ring: from (1400, 600) go *right* to (1600, 600), *up* to (1600, 800), *left* to (1400, 800), *down* — that is counter-clockwise on a grid with y upward. The hole goes (1450, 650) *up* to (1450, 750), *right*, *down*, *left* — clockwise. (Hand-checked; the shoelace sum for the exterior ring is positive, +80,000, confirming counter-clockwise.)
4. **Ring order.** "The first MUST be the exterior ring, and any others MUST be interior rings" [S18, §3.1.6].
5. **Feature wrapper.** A Feature has `"type": "Feature"`, a `geometry` that is a Geometry object "or, in the case that the Feature is unlocated, a JSON null value", a `properties` object (or null), and, if it "has a commonly used identifier", an `id` that is a string or number [S18, §3.2]. Request P7 would be written with `"geometry": null`.
6. **Collections.** A FeatureCollection has a `features` array of Feature objects [S18, §3.3]. This is what a whole layer looks like when exported to GeoJSON.

**(c) Esri JSON (ArcGIS REST API, used by ArcGIS services and the Esri SDKs).** The polygon is a `rings` array; "the first point of each ring is always the same as the last point"; but here "exterior rings are oriented clockwise, while holes are oriented counterclockwise" [X02] — the **opposite** of GeoJSON. The depot's exterior ring in Esri JSON must therefore run (1400, 600) → (1400, 800) → (1600, 800) → (1600, 600) → (1400, 600). A polyline is a `paths` array (any number of paths, so multipart lines need no separate type), and an empty polyline is "an empty array for the paths array" [X02]. A point is `{"x": …, "y": …}` with optional `z` and `m`, and "a point is empty when its x property is present and has the value null" [X02].

**Table 3.3 — The same ideas, three vocabularies.**

| Concept | WKT / OGC / PostGIS | GeoJSON (RFC 7946) | Esri (feature class / REST JSON) |
| --- | --- | --- | --- |
| One point | `POINT` | `Point` | point |
| Connected path | `LINESTRING` | `LineString` | polyline with one path |
| Several paths, one record | `MULTILINESTRING` | `MultiLineString` | polyline with several paths (multipart) |
| One area, possibly with holes | `POLYGON` (shell + holes) | `Polygon` (rings array) | polygon with rings |
| Several areas, one record | `MULTIPOLYGON` | `MultiPolygon` | polygon with several exterior rings (multipart) |
| Exterior ring direction | not stated in the section read | counter-clockwise | clockwise |
| No geometry | `POINT EMPTY` etc. [X22, §4.1.1] | `"geometry": null` | empty point / empty paths |
| Extra dimensions | Z, M, ZM | Z only (3 elements) | z, m with `hasZ` / `hasM` |

**Developer analogy.** These are three serialisations of one data model, like JSON, XML, and Protocol Buffers for one message type. *Where the analogy stops:* the models are not quite identical (GeoJSON has no M; GeoJSON and Esri JSON disagree on ring direction; Esri has multipoint and curves), so a round-trip is lossy in specific, documentable ways. Chapter 7 makes those conversion checks explicit.

**Misconception:** "GeoJSON is just the standard way to write coordinates, so any x, y data can be saved as GeoJSON." *Consequence:* engineering coordinates in metres are saved as `.geojson`; a web library reads them as degrees and either draws nothing or plots the town in the Pacific Ocean. This is exactly the blueprint's warning "do not publish it as geographic GeoJSON" (Appendix A.2).

> **Comprehension check 3.3.3.** Rewrite Ward B's ring (Table F1) as it would appear (i) inside a GeoJSON `Polygon` and (ii) inside an Esri JSON `rings` array, respecting each format's orientation rule. Show only the coordinate arrays and state the direction you used.

---

## 3.4 Connect attributes to geometry

### 3.4.1 Record, field, value, feature identifier, and attribute table

Every feature has attributes; the attributes of all features in a dataset are shown together as its **attribute table**. The vocabulary is the ordinary relational one:

| Term | Meaning | Example from Table F3 | Sources |
| --- | --- | --- | --- |
| **Attribute table** | The table of all records of one dataset, one row per feature | Table F3 as a whole | "The records in the attribute table in a GIS each correspond to one feature" [S23, §4.2]; "Each row in the table represents a feature (with or without geometry)" [X25] |
| **Record** (row) | One feature's stored values | The P3 row | "Each row in the table is a record" [S23, §4.2]; Esri: "A row in a table" [X07] |
| **Field** (column) | One named, typed attribute | `priority` | "Each column in the table is called a field" [S23, §4.2]; Esri: "A column in a table that stores the values for a single attribute" [X07] |
| **Value** | The content of one field in one record | `High` in P3's `priority` | |
| **Feature identifier** | A value that uniquely names one feature | `request_id` = P3 (business identifier); the software's own row number (system identifier) | Esri ObjectID: "A system-managed value that uniquely identifies a record or feature" [X07] |
| **Geometry field** | The special column holding the shape | `SHAPE` in ArcGIS [X05]; a `geometry` column in PostGIS | |

The attribute table and the map are **two views of the same records**. Select a feature on the map and its row is selected in the table; select a row and the feature is selected on the map. Chapter 1 showed this for P5; here you should be able to do it deliberately.

> **Procedure (version-specific) — find a selected feature's attributes. Not execution-tested; written from the ArcGIS Pro "latest" documentation (labelled 3.7) and the QGIS 3.40 manual, read 2026-09-19.**
>
> *ArcGIS Pro.* (1) With the Chapter 3 map open, use the **Select** tool on the **Map** tab to click request P3 on the map. (2) In the **Contents** pane right-click the *Requests* layer and click **Attribute Table**, or select the layer and press **Ctrl+T** [X08]. (3) At the bottom of the table view click **Show Selected Records**; the selection count reads in the form "1 of 7 selected" [X09]. Alternatively, right-click the layer, point to **Selection**, and click **Open Attribute Table Showing Selection** [X09]. (4) Click **Show All Records** to return [X09]. *Verify in the installed release:* the exact button icons and the count format.
>
> *QGIS 3.40.* (1) Use **Select Features** on the toolbar to click P3. (2) Open the table with **Layer ▸ Open Attribute Table**, **F6**, or right-click the layer ▸ **Open Attribute Table** [X25]. (3) The title bar shows the total, filtered, and selected counts; the drop-down at the bottom-left switches between **Show All Features** and **Show Selected Features** [X25]. **Shift+F6** opens the table already filtered to the selection [X25].

**Two identifiers, two jobs.** In ArcGIS every table gets "a unique integer field that cannot be null … maintained by ArcGIS" — the **ObjectID** [X05]. Geodatabases may also add a **GlobalID**, "automatically assigned and managed by a geodatabase when a row or feature is created", a 36-character string such as `{C53E50CE-…}` [X05]. Both are **system identifiers**: the software owns them. `request_id` = "P3" is a **business identifier**: the organisation's process owns it, it appears on the caller's SMS receipt, and it must survive export, copy, and migration. A system identifier is scoped to one table; when features are copied into a new feature class the new table has its own ObjectIDs — which is why the Multipart To Singlepart tool writes the *input* IDs into a separate `ORIG_FID` field rather than preserving them as the output's ObjectID [X12]. Never store an ObjectID in another system as "the" key to a request.

**Developer analogy.** ObjectID is an auto-increment surrogate key; `request_id` is a natural key. *Where the analogy stops:* in many databases you may choose either as the primary key; in ArcGIS the ObjectID is required and cannot be removed [X05], so a business identifier is always an *additional* field and you must maintain its uniqueness yourself (Chapter 8 covers designing identifiers).

**Misconception:** "The row number I see in the table is the record's ID." *Consequence:* a report says "request 4 is resolved" — row 4 is P4 today, but after an export, a sort, or a deletion, row 4 is something else. Quote `request_id`.

> **Comprehension check 3.4.1.** In Table F3, which field(s) could serve as a feature identifier, and why is `reported_on` + `channel` not a safe choice even though it happens to be unique in this fixture?

### 3.4.2 One value for styling, another for filtering; label versus identifier

Attributes drive three different things on a map: **symbology** (how a feature looks), **filtering** (which features are shown or counted), and **labelling** (what text is drawn beside a feature). Each can use a different field, and none of them changes the data.

**Worked example.** Take the Chapter 3 requests (Table F3, seven records).

- *Styling by `category`.* A unique-values style assigns one symbol per distinct value. Distinct values: Streetlight out, Pothole, Water leak, Blocked drain, Fallen tree — **five classes** (hand-checked; Pothole covers P2, P4, P7). QGIS: the GIS "will scan through all the different string values in the attribute field and build a list of unique strings" [S23, §4.6]. ArcGIS Pro: "Unique values symbology symbolizes feature layers into qualitative categories" [X16].
- *Filtering by `status`.* A filter `status IN ('Open', 'Reopened')` keeps P1, P3, P5, P7 — **four records**. But only **three** can be drawn, because P7 has no geometry. The table shows four rows; the map shows three symbols. Notice that a count taken from the *map* and a count taken from the *table* legitimately differ, and the difference is itself information (one request still needs locating).
- *Labelling by `category`.* Drawing "Pothole" beside P2, P4, and P7 helps a reader; drawing "P2" helps a clerk. Neither label is the data; it is a rendering of one field.

**Why a display label should not automatically be the business identifier.** A label is chosen for *reading*: short, meaningful, sometimes deliberately non-unique ("Pothole" ×3). An identifier is chosen for *linking*: unique, stable, often meaningless ("P3"). Making the label field the identifier fails in both directions — "Main Road" is a fine label for R1 but a poor key (many towns have several Main Roads, and roads get renamed), while "R1" is a fine key but tells a reader nothing. Keep both fields; render the label; join on the identifier. ArcGIS supports a **field alias**, "an alternative name specified for fields that is more descriptive and user-friendly than the actual name" [X07], which is a third, separate idea: it renames the *column heading*, not the values.

**Platform note — where each setting lives.** In ArcGIS Pro, symbology, labels, and the **definition query** are layer properties, not dataset properties [X03]; the definition query "limits which features are retrieved from the dataset by the layer" and "affects not only drawing, but also which features appear in the layer's attribute table and can be selected, labeled, identified, and processed by geoprocessing tools" [X04]. In QGIS the equivalent filter is the layer's *Provider Feature Filter* (Query Builder): "only the features corresponding to its result are available in the project" [X23]. In ArcGIS Online a **hosted feature layer view** plays a similar role for a hosted layer (3.5.2). In PostGIS you would write a SQL view. Same idea, four names — a general principle with platform-specific spelling.

**Misconception:** "I filtered the layer to Open requests, so the dataset now has four records." *Consequence:* a developer counts the layer, reports four, and a colleague opening the same file elsewhere sees seven. The filter is a property of the *layer* (3.5); the dataset is untouched.

> **Comprehension check 3.4.2.** Using Table F3, state the records kept by the filter `priority = 'High'`, how many symbols the map would draw, and which single field you would use to *label* those features for a crew supervisor — with one sentence of justification.

### 3.4.3 Related records: inspections stay in their own table

Drain DR-0042 has been inspected twice (Table F9). Its asset row (Table F4) has one `condition` value, "Poor". Where do the two inspections go?

**Not into the asset row.** Adding fields `inspection1_date`, `inspection1_result`, `inspection2_date`, … is the classic "repeating group" mistake: the row grows with every visit, most assets have empty columns, and "all inspections in 2025" becomes a search across dozens of fields. **Not as copies of the asset row.** Storing one *asset* record per inspection, each with the drain's coordinates and attributes, makes the drain appear three times on the map and turns every asset count into a guess.

**As related records.** Table F9 is a **non-spatial table** — Esri: "a stand-alone table is a table of attributes that do not have associated geographic features" [X08] — whose `asset_id` field carries the asset's business identifier. Selecting DR-0042 on the map and asking "show me its inspections" is a lookup of `asset_id = 'DR-0042'` in Table F9, returning INS-0001 and INS-0002 in date order. The asset's `condition` field is at most a *cached summary* of the latest inspection (3.1.2) and could be dropped or recomputed.

Chapter 8 formalises this as a **relationship** (cardinality, keys, relationship classes, joins versus relates). Chapter 10 shows how to query across it. For now, the rule is: **one observation, one record; link by identifier, do not copy geometry.**

**Developer analogy.** `inspections.asset_id` is a foreign key to `assets.asset_id`; one asset, many inspections. *Where the analogy stops:* GIS software also offers *joins* that temporarily flatten related rows onto the spatial table for display — Esri distinguishes "joining" (appending fields) from a "relate" (a temporary connection between records) [X07] — and a one-to-many join can multiply or collapse rows in ways that a naïve map count will not reveal. That trap is treated in Chapter 10.

**Misconception:** "The asset's `condition` field says Poor, so the last inspection said Poor." *Consequence:* usually true, but only if a process keeps the cache updated. In the fixture it is consistent (INS-0002 → Poor). Trust the inspections table; treat the summary as derived.

> **Comprehension check 3.4.3.** Tree TR-0301 has `condition` = Unknown and no rows in Table F9. Give two different reasons this could be the case, and say which record you would inspect to distinguish them.

---

## 3.5 Distinguish datasets, layers, and maps

### 3.5.1 Dataset, layer, map — and why the words vary

Three things are easy to confuse because software often shows them with one icon and one name.

| Thing | What it is | Where it lives | Fixture example |
| --- | --- | --- | --- |
| **Dataset** | The stored collection of features: geometry + attributes, one geometry type, one schema | A file (GeoPackage, shapefile), a geodatabase feature class, a database table, a hosted layer's storage | The Requests feature class with 7 records |
| **Layer** | A *use* of a dataset in a map: a reference to the dataset plus presentation and query settings (symbology, labels, filter, visibility, scale range) | Inside a map, which lives inside a project or web map | "Requests — by status" |
| **Map** | An ordered stack of layers with a shared view (extent, and later a coordinate system) | A project (`.aprx`, `.qgz`) or a web map item | "Chapter 3 Town" |

The defining fact: **a layer references a dataset; it does not contain it.** Esri: "Layers reference a data source, and if ArcGIS Pro interprets data as spatial, the data's properties and attributes specify how the layer draws on a map, scene, or layout" [X03]; "Layers are reusable and flexible. For example, you can use the same imagery layer in every map you create" [X03]. Chapter 2 made the same point about projects: delete the layer and the dataset is untouched; delete the dataset and the layer is broken.

**Terminology varies by platform — the concept does not.**

| Platform | The stored collection is called… | The map-level use is called… | Note |
| --- | --- | --- | --- |
| ArcGIS Pro / geodatabase | feature class (or table) | feature layer | Layer files (`.lyrx`) save layer *settings*, not data [X03] |
| ArcGIS Online | a **hosted feature layer** item (this one *does* store data — a naming trap noted in Chapter 2) | a layer in a web map; a **hosted feature layer view** for a filtered use | [X30] |
| QGIS | often just "layer" — the word is used for the file/table *and* the map entry; the *Information* tab distinguishes the source path from the layer name | layer in the Layers panel | [X23] |
| PostGIS | table with a geometry column | whatever the client builds (a QGIS layer, a GeoServer layer, a SQL view) | [X22] |
| GeoJSON file | the FeatureCollection | whatever the reading application makes of it | [S18, §3.3] |

When you read documentation, translate the local word to *dataset* or *layer* before deciding what an operation does to your data.

**Developer analogy.** Dataset : layer : map ≈ table : view/query with presentation settings : dashboard. *Where the analogy stops:* a database view is defined in the database and shared by all clients; a GIS layer's settings are defined in *one* map or project, so two colleagues' maps of the same dataset can filter and style it differently without either knowing.

**Misconception:** "I added the layer to my map, so my project has the data." *Consequence:* the project is emailed, opened elsewhere, and every layer shows a broken-source warning (Chapter 2, 2.2.2).

> **Comprehension check 3.5.1.** A colleague says "I deleted the Wards layer." Write the two questions you must ask before you know whether any data was lost.

### 3.5.2 Two layers, one source; filtered view versus exported copy

Because a layer is a reference plus settings, one dataset can appear as **several layers in the same map**, each configured differently. This is the practical heart of the chapter and the invariant your lab (3.7) must demonstrate.

**Worked example — the Requests dataset shown twice (hand-checked).**

| Layer name | Source | Filter (definition query / provider filter) | Symbology | Rows in attribute table | Symbols drawn |
| --- | --- | --- | --- | --- | --- |
| Requests — by status | Requests (7 records) | none | unique values on `status` (5 classes: Open, In progress, Resolved, Reopened, Closed – duplicate) | 7 | 6 (P7 has no geometry) |
| Requests — High priority | Requests (same 7 records) | `priority = 'High'` | single symbol, larger | 3 (P2, P3, P6) | 3 |

Both layers point at one dataset. Change a value in the dataset — say P2 is resolved — and both layers reflect it at the next redraw. Add a request and the first layer shows 8 rows; the second shows 4 only if the new one is High. **The dataset's feature count stays 7 no matter what you do to either layer**; that count is what you will verify in the lab.

**Filtered view versus exported copy.** There are two ways to obtain "only the High-priority requests", and they behave differently afterwards:

| | **Filtered view** (definition query, provider filter, hosted view) | **Exported copy** (Copy Features, Export Features, Save Features As) |
| --- | --- | --- |
| What is created | Layer settings only | A **new dataset** with its own storage |
| Storage | None extra | A new file or table |
| Effect of editing the source later | The view reflects it | The copy does not change |
| Effect of editing the copy | — | The source does not change |
| Feature identifiers | Same records, same IDs | New table, new system IDs (business IDs copied as attributes) |
| Use when | You want live, reproducible sub-sets for display, selection, or analysis inputs | You need a snapshot: an archive, a hand-off to another team, a disposable scratch copy for editing practice (3.6.3) |
| ArcGIS Pro | Definition query [X04] | Copy Features: "Copies features from the input feature class or layer to a new feature class"; "If the input is a layer and has a selection, only the selected features are copied" [X13] |
| QGIS | Layer ▸ Filter… (Query Builder) [X23], [X27] | Export ▸ Save Features As…, with "Save only selected features" [X27], [X28] |
| ArcGIS Online | Hosted feature layer view: "hosted views reference existing data" and "edits made to the data in the source appear in the view" [X30] | Export item / download |
| PostGIS | `CREATE VIEW` | `CREATE TABLE … AS SELECT` |

A subtle trap: many tools honour a layer's *current selection* and *definition query* as their input. Copy Features copies only selected features when a selection exists [X13]; Get Count reports "only the selected records" if a selection exists [X15]. A copy made from a filtered layer is a copy of the *filtered* set — which may be exactly what you wanted, or a silent loss of four records.

**Diagram D6 (described).** One cylinder labelled "Requests dataset (7 records)" on the left. Two arrows to the right, each ending at a map-layer card: "Layer 1: filter none, style by status" and "Layer 2: filter priority = High, single symbol". A third arrow labelled "Copy Features" ends at a second, separate cylinder "Requests_High (3 records)" with a broken-link mark between the two cylinders. Caption: "Layers reference; copies duplicate."

**Misconception:** "A view and a copy are the same thing as long as the rows match today." *Consequence:* a planning team analyses a copy for six months while the source is being edited, and delivers results about data that no longer exists. Record which you used and, for a copy, the date and the filter it was made with.

> **Comprehension check 3.5.2.** You need a layer of *Open or Reopened* requests for a dashboard that must stay current, and a separate dataset of the same requests to send to an external auditor "as of today". Say which mechanism you use for each and what you write in the file name or metadata of the second.

### 3.5.3 Basemap, operational data, and drawing order

Maps have two kinds of layers with different jobs:

- **Basemap layers** provide context: streets, terrain, imagery, place names. Esri: "Basemaps serve as a reference map on which you overlay data from layers and visualize geographic information" [X10]. Only one basemap is active in an ArcGIS Pro map at a time, chosen from the **Map** tab ▸ **Layer** group ▸ **Basemap** gallery [X10]. Basemaps are usually not yours to edit or query; they are someone else's dataset shown for orientation.
- **Operational layers** are the data you are working on: requests, assets, wards. You select, query, edit, and analyse these.

In this chapter's schematic fixture there is **no basemap** — the training grid has no Earth location for a basemap to align with (Chapters 5–6 explain alignment). That absence is itself instructive: everything you see is operational data, and nothing gives you "the streets" for free.

**Drawing order.** Layers draw from the bottom of the layer list upward: "The layer lowest in the list draws first, followed by the next-lowest layer, and so on, until the features in the topmost layer draw above all else" [X11]; QGIS: "layers listed nearer the top of the legend are drawn over layers listed lower down" [X27]. A layer higher in the list therefore **covers** whatever is beneath it.

**Worked example — a polygon fill hides the points.** Put *Wards* (with a solid fill) *above* *Requests* in the layer list. Every request inside a ward — P1 to P5 — is painted over by the ward fill; only P6, outside both wards, remains visible. The map now suggests "one request, outside our area". The data has seven records. A reader who trusts the picture is wrong by six. Move *Wards* to the bottom, or give it an outline-only symbol, and all six located requests reappear. Nothing in the dataset changed at any point.

Drawing order also interacts with features *within* one layer (Esri offers a feature-level drawing order setting) and with symbol size; those refinements belong to Chapter 12 (cartography). For now: **when a map looks emptier than the table, check drawing order and filters before believing it.**

**Developer analogy.** Drawing order is `z-index` in a rendering stack. *Where the analogy stops:* there is no "pointer-events" separate from painting — in most desktop GIS a covered feature can still be selected by clicking, which makes the confusion worse: the table selects a feature you cannot see.

**Misconception:** "The basemap is part of my data." *Consequence:* a developer tries to "select the roads from the basemap" or assumes the basemap's road positions match the municipality's centreline dataset. A basemap is context from another source with its own accuracy and date (Chapter 7).

> **Comprehension check 3.5.3.** The map shows Wards (solid fill) above Roads above Requests. List which of R1, R2, L1, and P1–P6 are fully visible, partly visible, or hidden, and justify one of each.

---

## 3.6 Inspect extent, selection, and identity

### 3.6.1 Bounding extent is a summary, not the shape

The **extent** (bounding box, envelope) of a geometry or a dataset is the smallest axis-aligned rectangle that contains it: four numbers, x-min, y-min, x-max, y-max. Esri's REST envelope is exactly that, with optional z and m ranges [X02]; GeoJSON's optional `bbox` is "all axes of the most southwesterly point followed by all axes of the more northeasterly point" [S18, §5].

Extent is cheap to compute and store, and software uses it constantly: **Zoom To Layer** zooms to a layer's extent [X19]; layer properties report it [X14], [X23]; spatial indexes are built on it. It is therefore the *first* thing you should record when inspecting a dataset — and the first thing to *not* over-interpret.

**Worked example — extents of the fixture layers (hand-checked from Tables F1–F8).**

| Layer | x-min | y-min | x-max | y-max | Box size | Comment |
| --- | --- | --- | --- | --- | --- | --- |
| Wards | 0 | 0 | 2000 | 1000 | 2000 × 1000 | Box equals the union of the squares exactly — a coincidence of squares |
| Roads (R1, R2, L1) | 0 | 0 | 2000 | 900 | 2000 × 900 | Almost the whole town, though roads occupy zero area |
| Requests (P1–P6; P7 has no geometry and does not contribute) | 200 | 200 | 2200 | 900 | 2000 × 700 | Extends past the wards because of P6 |
| Assets (4 points) | 205 | 195 | 2190 | 700 | 1985 × 505 | |
| Park PK-01 (one multipart feature) | 100 | 50 | 1500 | 150 | 1400 × 100 = 140,000 m² | The feature occupies 40,000 m² — **29 %** of its own box; the gap between the parts is inside the extent |
| Depot DP-01 | 1400 | 600 | 1600 | 800 | 200 × 200 = 40,000 m² | The box area equals the outer ring; the polygon area is 30,000 m² because of the hole |
| Schools_point | 400 | 700 | 400 | 700 | 0 × 0 | A single point's extent is degenerate; "zoom to layer" on it needs a minimum scale — expect software to pick one |

Three lessons:

1. **Extent overlap is not feature overlap.** The Roads extent (0 … 2000, 0 … 900) contains the whole Park extent, yet no road touches either part of the park. The Requests extent (200 … 2200, 200 … 900) contains the whole Depot extent, yet no request lies inside the depot — the boxes overlap, the shapes do not. (The Requests and Park extents, by contrast, do not overlap at all: Requests' y-min is 200 and Park's y-max is 150.) Engines use extent tests as a fast *pre-filter* and then test the true geometry; you must not stop at the pre-filter.
2. **Extent hides shape.** A multipart feature's box spans its gaps; a polygon's box ignores its holes; a diagonal road's box is mostly empty.
3. **Extent is affected by outliers.** One mislocated request at (20000, 500) would make the Requests extent ten times wider and "Zoom To Layer" would show a nearly empty map — a common first symptom of a data error (Chapter 9).

**Developer analogy.** Extent is like `min()`/`max()` summary statistics on the x and y columns. *Where the analogy stops:* extents are compared as rectangles (does box A intersect box B?), which makes them useful as an index but also means two features whose boxes intersect may be far apart.

**Misconception:** "The layer's extent shows where the data is." *Consequence:* a team concludes "we have coverage of Ward B" because the extent spans it, when in fact every feature is in Ward A and one outlier stretched the box.

> **Comprehension check 3.6.1.** Give the extent of a layer containing only Road R2, then explain why a point at (600, 300) is inside that extent but 100 m from the road.

### 3.6.2 Recording count, geometry type, field names, and missing geometry

An **intake check** is a short, written inspection you do every time a dataset arrives, before any analysis. For Chapter 3 it has four items; Chapters 5, 7, and 9 will add coordinate system, provenance, and quality items.

**Table 3.4 — Intake record for the Chapter 3 fixture (expected values, hand-checked from the tables).**

| Dataset | Feature count | Geometry type | Fields (besides the system ID and geometry) | Records with empty / missing geometry |
| --- | --- | --- | --- | --- |
| Wards | 2 | Polygon | `ward`, (area, if the software adds it) | 0 |
| Roads | 3 | Line (polyline) | `road_id`, `name`, `width_m` | 0 |
| Requests | **7** | Point | `request_id`, `category`, `priority`, `status`, `reported_on`, `closed_on`, `channel`, `note` | **1 (P7)** |
| Assets | 4 | Point | `asset_id`, `asset_type`, `installed`, `condition` | 0 |
| Schools_point | 1 | Point | `school_id`, `name` | 0 |
| Schools_footprint | 1 | Polygon | `school_id`, `name` | 0 |
| Parks | 1 (2 parts) | Polygon (multipart / MultiPolygon) | `park_id`, `name` | 0 |
| Depot | 1 (1 hole) | Polygon | `depot_id`, `name` | 0 |
| Wards_as_points | 2 | Point | `ward` | 0 |
| Inspections | 3 | *none — non-spatial table* | `inspection_id`, `asset_id`, `inspected_on`, `condition_found`, `inspector` | not applicable |

**Why "7, of which 1 empty" and not "6".** A record without geometry is still a record. It is counted in the table, returned by attribute queries, and ignored by every spatial operation. If you report "6 requests" you have silently dropped a live report. If you report "7 requests near the road" you have silently included one that cannot be tested. The honest statement is "7 records, 6 located, 1 awaiting location". Chapter 9 covers *repairing* such cases (locating P7, or deciding it should be a non-spatial table entry until located); this chapter only requires you to **find and record** them.

> **Procedure (version-specific) — read count, type, fields, and empty geometries. Not execution-tested; ArcGIS Pro "latest" (3.7) and QGIS 3.40 documentation, read 2026-09-19.**
>
> *ArcGIS Pro.* (1) **Count:** open the attribute table (Ctrl+T) and read the total in the "n of m selected" footer [X09], or run the **Get Count** tool, which "returns the total number of rows for a table" — clear any selection first, because "if the input is a layer or table view containing a selected set of records, only the selected records will be counted" [X15]. Note that an active definition query also limits what the layer returns [X04]; check the layer has none, or count the dataset from the **Catalog** pane. (2) **Geometry type and extent:** open **Layer Properties** (right-click ▸ **Properties**) ▸ **Source** tab, which shows the data source and "the layer's extent, spatial reference, domain, resolution, and tolerance" [X14]. **Verification item:** confirm where the *geometry type* is displayed in the installed release (the Source tab's data-source details, or the item's properties in the Catalog pane); the page read does not list it explicitly. (3) **Fields:** the **Fields** view (open from the attribute table or the **Data** tab). (4) **Empty geometries:** run **Check Geometry** on the dataset; its report lists problems including "Null geometry" [X06]. Alternatively sort the table by a shape-length/area field if one exists, or select all features on the map with a box and compare the selected count with the table total: the difference is the number of records that could not be drawn.
>
> *QGIS 3.40.* (1) **Layer Properties** ▸ **Information** tab shows "geometry type, data source encoding, extent, feature count" and the fields [X23]. (2) The attribute table title bar shows "Features Total / Filtered / Selected" [X25]. (3) **Empty geometries:** in the attribute table, select all features while viewing **Show Features Visible On Map** after zooming to the layer, and compare with **Show All Features** [X25]; or use an expression in the **Advanced Filter (Expression)** mode [X25] that tests for an empty or null geometry (QGIS provides such geometry functions; **verify the exact function name** in the installed release's expression help before teaching). **Verification item:** the exact behaviour of the delimited-text import for a row with blank x/y (whether it loads as a null-geometry feature or is skipped with an error) must be checked in the installed release before the lab (see 3.7.2).

**Misconception:** "Feature count is what the map shows." *Consequence:* counts differ between the map (drawn features), the layer (after filter), and the dataset (all records). Always say which you counted.

> **Comprehension check 3.6.2.** A dataset you receive reports 120 features in its attribute table; "Zoom To Layer" shows 118 symbols scattered over the town and nothing else. List three different explanations for the missing two and, for each, the check that would confirm it.

### 3.6.3 Predicting what changes: presentation change versus geometry edit

The last skill in this chapter is to **predict, before acting, which stored records change.** Two operations look similar on screen and are opposite in effect.

| Operation | Example | Which stored records change? | Reversible how? |
| --- | --- | --- | --- |
| **Presentation-only change** | Change the Requests symbol from circles to squares; add labels; set a definition query; reorder layers; hide a layer | **None.** The layer's settings change; the dataset is byte-for-byte the same | Change the setting back, or discard project changes |
| **Geometry edit** | Move P1 from (200, 200) to (200, 300) | **One record**: P1's geometry. All other records unchanged. Any layer referencing the dataset shows the new position | Only by editing again, or by *not saving* the edit |
| **Attribute edit** | Set P2's `status` to Resolved | One record, one field | Same as above |
| **Delete** | Delete P6 | One record removed; count 7 → 6 | Only by not saving, or from a backup |

**Worked example — a move, hand-checked.** Move P1 to (200, 300) on a *disposable copy* of Requests. Predictions before the edit: (i) the copy's count stays 7; (ii) P1's distance to R1 becomes |300 − 500| = **200 m** (was 300 m); (iii) P1 is still strictly inside Ward A; (iv) the *original* Requests dataset still has P1 at (200, 200), so any layer on the original still shows 300 m; (v) the layer "Requests — High priority" on the copy shows no change at all, because P1 is Medium. After the edit, verify (i)–(v). If any prediction fails, you have edited the wrong dataset or the wrong record.

**Why a disposable copy.** Editing is the one operation in this chapter that can damage source data. Make a copy with Copy Features [X13] or Export ▸ Save Features As… [X27], name it so that nobody mistakes it for the source (`Requests_scratch_2026-09-19`), and edit that. The copy is an *exported copy* in the sense of 3.5.2 — its records are independent, so nothing you do to it reaches the source.

**Edits are staged before they are stored.** Both desktop applications hold edits in memory until you save them: ArcGIS Pro's **Save** command "saves all edits you made since the last time you saved your changes" and **Discard** "rolls back all edits you made since the last time you clicked Save" (Edit tab ▸ Manage Edits group) [X17]; in QGIS, "when a layer is in editing mode, any changes remain in the memory of QGIS. Therefore, they are not committed/saved immediately to the data source or disk" until **Save Layer Edits** [X24]. Saving the *project* is a separate action from saving *edits*: a project stores layer settings and references (Chapter 2); edits go to the dataset.

> **Procedure (version-specific) — presentation change, then a geometry edit on a copy. Not execution-tested.**
>
> *ArcGIS Pro (3.7 docs).* (1) Select the *Requests* layer; **Feature Layer** tab ▸ **Drawing** group ▸ **Symbology**; in the **Symbology** pane choose **Unique Values** and set **Field 1** to `status` [X16]. Observe: the map changes, the attribute table does not. (2) Run **Copy Features** with *Requests* as **Input Features** (no selection active) to a new feature class `Requests_scratch` [X13]. Add it to the map. (3) Select P1 in `Requests_scratch`. **Edit** tab ▸ **Features** group ▸ **Modify** ▸ **Move** tool; drag P1 to approximately (200, 300); click **Finish** or press F2 [X18]. (For an exact position, the **Move To** tool accepts absolute coordinates — verify its label in the installed release.) (4) Open both attribute tables. Predict, then check, the five points above. (5) **Edit** tab ▸ **Manage Edits** ▸ **Discard** to roll the move back, or **Save** to keep it on the scratch copy [X17]. Never save edits to the original in this chapter.
>
> *QGIS 3.40.* (1) Layer Properties ▸ **Symbology** ▸ **Categorized**, Value = `status`, **Classify** [X23]. (2) Right-click *Requests* ▸ **Export ▸ Save Features As…** to `Requests_scratch` (GeoPackage) [X27], [X28]; add it to the project. (3) Right-click `Requests_scratch` ▸ **Toggle Editing**; use **Move Feature(s)**: select P1, click an origin point, click the destination [X24]. (4) Compare the two attribute tables. (5) **Save Layer Edits** or **Cancel** [X24].

**Developer analogy.** A presentation change is a change to a view-model or a CSS file; a geometry edit is an `UPDATE` inside an open transaction that is committed on Save. *Where the analogy stops:* GIS applications keep the "transaction" open across many operations and across a session — an unsaved move remains pending while you do other work, and Save commits *all* pending edits on all layers in the map [X17], not only the one you are thinking about.

**Misconception:** "I only changed the colour, so the file's modified date should be the same" — true, and useful as a check. The reverse misconception is more dangerous: "I only nudged the point a little, so nothing important changed." *Consequence:* the nudge moved P5 from the shared boundary to inside Ward B, and every boundary-inclusive report from Chapter 1 now has a different answer.

> **Comprehension check 3.6.3.** Before each action, state how many records in the *original* Requests dataset change: (a) setting the definition query `status = 'Open'` on its layer; (b) deleting the layer from the map; (c) moving P5 on a scratch copy and saving; (d) moving P5 on the original layer and clicking Discard.

---

## 3.7 Guided lab: model and inspect a small town

### 3.7.1 Objective, prerequisites, and what this lab is (and is not)

**Objective.** Using a supplied schematic town, (a) justify the geometry chosen for each dataset, (b) inspect every dataset's count, type, fields, extent, and empty-geometry cases, (c) build two layers from one source without changing that source, and (d) identify the dataset whose representation cannot answer its question.

**Prerequisites.** Modules 3.1–3.6 read; the Chapter 1 map walk-through (1.5) done or read. No coordinate-system knowledge is needed: the data is on the training grid and the software will report an *unknown* or *undefined* coordinate system — **that is correct and you must not "fix" it** (Chapter 5 explains why).

**What this lab is not.** It does not measure anything on the Earth, does not need a basemap, does not publish anything, and does not require editing the supplied data except on a scratch copy in step 8.

**Execution status.** The instructor builds the data package from the specification in 3.7.2 and verifies it before the session (Instructor Appendix I.5). The software steps below were written from the documentation cited and have **not been executed by the author**; the expected results in 3.7.4 are **hand-checked from the tables**, not observed in software. Anything that differs in the installed release must be recorded in the instructor change log (I.8).

### 3.7.2 Input data — the Chapter 3 Town package (synthetic)

**Coordinate frame.** Planar training grid, metres, (x, y), y upward, **no coordinate reference system**. Not Earth-referenced. Not accurate for any real place.

**Files.** Ten UTF-8 CSV files with a header row. Geometry is given as a `wkt` column (Well-Known Text, 3.3.3) for lines and polygons, and as `x`, `y` columns for points so that the point tools of both applications can be used. The instructor converts these to a GeoPackage `Chapter03_Town.gpkg` (or a file geodatabase) by one of the routes in I.5; learners receive the converted package and, for reference, these CSV texts.

`wards.csv`
```text
ward,wkt
A,"POLYGON((0 0,1000 0,1000 1000,0 1000,0 0))"
B,"POLYGON((1000 0,2000 0,2000 1000,1000 1000,1000 0))"
```

`roads.csv`
```text
road_id,name,width_m,wkt
R1,Main Road,12,"LINESTRING(0 500,2000 500)"
R2,Station Road,7,"LINESTRING(500 0,500 500,800 900)"
L1,Bus turning loop,6,"LINESTRING(1300 300,1350 300,1350 350,1300 350,1300 300)"
```

`requests.csv` (P7 has blank coordinates on purpose; the value `Closed - duplicate` uses a plain hyphen in the file)
```text
request_id,x,y,category,priority,status,reported_on,closed_on,channel,note
P1,200,200,Streetlight out,Medium,Open,2026-09-02,,Mobile app,
P2,800,800,Pothole,High,In progress,2026-08-28,,Phone,Crew assigned 2026-09-01
P3,1200,250,Water leak,High,Open,2026-09-10,,Mobile app,
P4,1700,900,Pothole,Low,Resolved,2026-08-15,2026-08-20,Web form,
P5,1000,500,Blocked drain,Medium,Reopened,2026-08-30,,Phone,Closed 2026-09-05; reopened 2026-09-12
P6,2200,500,Fallen tree,High,Closed - duplicate,2026-09-11,2026-09-12,Phone,Duplicate of a report not in this package
P7,,,Pothole,Medium,Open,2026-09-14,,Phone,Caller could not give a location; to be located
```

`assets.csv`
```text
asset_id,asset_type,x,y,installed,condition
SL-0113,Streetlight,205,195,2018,Fair
DR-0042,Drain,995,510,2011,Poor
TR-0301,Tree,2190,520,2005,Unknown
TR-0302,Tree,1500,700,2019,Good
```

`schools_point.csv`
```text
school_id,name,x,y
SC-01,Ward A Primary School,400,700
```

`schools_footprint.csv`
```text
school_id,name,wkt
SC-01,Ward A Primary School,"POLYGON((370 680,430 680,430 720,370 720,370 680))"
```

`parks.csv` (one multipart record)
```text
park_id,name,wkt
PK-01,Riverside Park,"MULTIPOLYGON(((100 50,300 50,300 150,100 150,100 50)),((1300 50,1500 50,1500 150,1300 150,1300 50)))"
```

`depot.csv` (one polygon with a hole)
```text
depot_id,name,wkt
DP-01,Central depot,"POLYGON((1400 600,1600 600,1600 800,1400 800,1400 600),(1450 650,1550 650,1550 750,1450 750,1450 650))"
```

`wards_as_points.csv` (deliberately unsuitable)
```text
ward,x,y
A,500,500
B,1500,500
```

`inspections.csv` (non-spatial)
```text
inspection_id,asset_id,inspected_on,condition_found,inspector
INS-0001,DR-0042,2024-10-15,Fair,crew01
INS-0002,DR-0042,2025-11-02,Poor,crew01
INS-0003,SL-0113,2026-03-14,Fair,crew02
```

**Ring direction note.** The WKT rings above are written counter-clockwise for exterior rings and — for the depot hole — also counter-clockwise, because WKT as documented by PostGIS does not require an orientation and the conversion tools are expected to accept either. If the instructor's build route reports an orientation problem, reverse the hole ring and record the change (I.5, I.8).

### 3.7.3 Ordered steps (ArcGIS Pro primary route; QGIS in 3.7.7; paper in 3.7.8)

Keep a **lab log** (a text file or the template in 3.7.6) and write each observation as you make it. "I saw" and "I expected" are both required.

1. **Open the prepared project** `Chapter03_Town.aprx` (or create a new map and add every feature class from `Chapter03_Town.gpkg`; ArcGIS Pro reads GeoPackage feature classes directly — the Check Geometry tool, for example, lists GeoPackage among its supported inputs [X06]). Confirm the map shows an *unknown* coordinate system without a warning you are tempted to act on. **Do not set one.**
2. **Justify geometry choices (LO1, LO2).** Before touching the software further, complete the *geometry-choice table* (deliverable A in 3.7.6) for all ten datasets: geometry type used, the question it can answer, one question it cannot, and the scale/purpose assumption. Use Tables 3.1 and 3.2 and module 3.2.2.
3. **Intake check (LO6).** For each spatial dataset, record feature count, geometry type, field names, extent, and the number of records with empty geometry, following the 3.6.2 procedure. Compare with Table 3.4 and the extent table in 3.6.1. Every difference is either a build error (report it) or a misunderstanding (resolve it) — do not move on with an unexplained difference.
4. **Locate the structural exceptions (LO3).** Open the attribute tables of *Parks* and *Depot*. Confirm one row each. Zoom to *Parks* and describe what one row looks like on the map. Zoom to *Depot* and click inside the courtyard with the **Explore** tool (the default identification tool [X19]): record whether a pop-up for DP-01 appears. Then click on the depot ring itself and compare. Record both observations. Do the same for L1: identify it and note what the pop-up reports (a length if the software adds one; never an area).
5. **Attributes and related records (LO4).** Select DR-0042 on the map; open the Assets table with **Show Selected Records** [X09]; record the row. Open the *Inspections* stand-alone table and, without any join, find the rows with `asset_id = 'DR-0042'` (sort or use **Select By Attributes**). Record the count (expected 2) and the dates, in order.
6. **Two presentations of one source (LO5).** Add the *Requests* feature class to the map **a second time**, so the map has two layers on one source. Rename them *Requests — by status* and *Requests — High priority*. On the first, set **Unique Values** symbology on `status` [X16]. On the second, set the definition query `priority = 'High'` (**Feature Layer** ▸ **Data** ▸ **Build Definition Query** ▸ **New definition query**, build the clause, **Apply**) [X04]. Record the attribute-table row count of each layer and the number of symbols visible. **Then record the feature count of the source dataset** from the Catalog pane or with **Get Count** on the feature class with no selection [X15].
7. **Drawing order (LO5).** Drag *Wards* to the top of the Contents pane with a solid fill. Count the visible requests. Move it to the bottom. Count again. Record the two numbers and the explanation.
8. **Presentation change versus geometry edit (LO6).** Follow the 3.6.3 procedure: make `Requests_scratch` with Copy Features (no selection active) [X13]; on the scratch copy move P1 to (200, 300); *before* the move, write the five predictions of 3.6.3 in your log; after the move, check them; then **Discard** the edit [X17]. Confirm the original *Requests* still has P1 at (200, 200) and the count is 7.
9. **The unsuitable representation (LO1, LO7).** Using only `Wards_as_points`, try to answer "which ward is P3 in?". Write down why the layer cannot answer it, what question it *could* answer, and what representation is needed.
10. **Close without saving edits.** Save the *project* if you wish (it stores only layer settings and references). Verify by reopening that the source datasets are unchanged: count 7 requests, P1 at (200, 200).

### 3.7.4 Expected results and validation checks (hand-checked; not observed in software)

| Step | Expected | How to validate |
| --- | --- | --- |
| 3 | Counts 2, 3, 7 (1 empty), 4, 1, 1, 1, 1, 2; Inspections 3 rows, no geometry. Extents as in 3.6.1. | Table 3.4; any difference is reported before continuing |
| 4 | Parks: 1 row, two rectangles far apart. Depot courtyard click: no DP-01 identified (the point is in the hole); ring click: DP-01 identified. L1 pop-up: no area | Table F6, F7; TR-0302 lies in the hole |
| 5 | DR-0042 row; inspections INS-0001 (2024-10-15, Fair) then INS-0002 (2025-11-02, Poor) | Table F9 |
| 6 | Layer 1: 7 rows, 6 symbols, 5 status classes. Layer 2: 3 rows (P2, P3, P6), 3 symbols. **Source count: 7, unchanged** | Table F3; the invariant the blueprint requires |
| 7 | Wards on top with solid fill: 1 visible request (P6). Wards at bottom: 6 visible | 3.5.3 worked example |
| 8 | Scratch copy: 7 records, P1 at (200, 300), 200 m from R1; original unchanged | 3.6.3 predictions (i)–(v) |
| 9 | Cannot answer; a point per ward can answer "where is the ward's reference point / label position"; polygons are needed | 3.1, 3.2 |
| 10 | Requests count 7; P1 at (200, 200) | Reopen and inspect |

**Invariant to verify explicitly:** across steps 6–8 the *Requests* feature class count is 7 at every check. If it is ever not 7, stop and find which step wrote to the source.

### 3.7.5 Troubleshooting

| Symptom | Likely cause | What to do |
| --- | --- | --- |
| Software offers to assign a coordinate system, or warns "unknown spatial reference" | Expected for this fixture | Decline; record the message; Chapter 5 |
| Points appear but polygons and lines are missing after the instructor's build | WKT column not recognised during conversion | Instructor: I.5 build routes; check the column name and quoting |
| Requests table shows 6 rows, not 7 | The build route dropped the blank-coordinate row | Instructor: verify the route's null handling (Pro's XY Table To Point keeps it as an empty geometry [X20]; QGIS delimited-text behaviour is a verification item) |
| *Requests — High priority* shows 7 rows | Definition query not applied or not active (only one query can be active at a time [X04]) | Layer Properties ▸ Definition Query; check the active query |
| Get Count returns 3 for the source | A selection or definition query was in effect on the layer you counted [X15] | Clear selection; count the feature class from the Catalog pane, not the filtered layer |
| Clicking in the depot courtyard identifies DP-01 | Either the hole was lost in conversion (the polygon has one ring) or the click landed on the ring | Check the ring count in the geometry (Check Geometry / Information tab); zoom in and click again near (1500, 700) |
| Moving P1 changed the original | The move was made on the original layer, or the "copy" is a second layer on the same source rather than an exported copy | Discard edits; confirm `Requests_scratch` is a separate feature class in the Catalog pane |
| The Parks row count is 2 | The build route split the multipart feature | Instructor: rebuild; or teach the difference and continue with 2 rows, recording the change |

### 3.7.6 Required learner deliverables

**A. Geometry-choice table** (one row per dataset, ten rows):

| Dataset | Geometry used | Question it answers | Question it cannot answer | Scale / purpose assumption | Would you change it? Why? |
| --- | --- | --- | --- | --- | --- |

**B. Intake record** in the form of Table 3.4 with your observed values and a column "matches expected? (Y/N + note)".

**C. Annotated record/map pairs** — for DR-0042, DP-01, PK-01, and P7: a sketch or screenshot of the feature (or, for P7, of the table row and the empty map) with its attribute row, and one sentence each on what the geometry represents and what it omits.

**D. Two-presentations evidence** — the two layers' settings (symbology field; definition query text), their row counts and symbol counts, and the source feature count before and after, with the statement "presentation changes did not alter the source feature count (7)".

**E. Prediction log for step 8** — the five predictions written *before* the move, and the observed results.

**F. Unsuitable-representation note** — three to five sentences on `Wards_as_points`.

The instructor checks A against Tables 3.1–3.2, B and D against 3.7.4, and E for predictions made *before* observation (Instructor Appendix I.3).

### 3.7.7 QGIS alternative (equivalent foundational exercise; not execution-tested)

Every step has a QGIS 3.40 equivalent, and the expected results in 3.7.4 are the same because they depend on the data, not the application. Differences to expect:

- **Adding data:** **Layer ▸ Add Layer ▸ Add Vector Layer** for the GeoPackage. For the CSV route, **Add Delimited Text Layer** offers "Point coordinates" (X/Y fields) or "Well known text (WKT)" geometry, and asks for a Geometry CRS [X29]. **Verification item:** whether a layer can be left without a CRS and what QGIS does with P7's blank coordinates. The instructor must test both before the session and record the outcome (I.5).
- **Intake check:** Layer Properties ▸ **Information** tab for geometry type, extent, and feature count [X23]; attribute table title bar for counts [X25]. QGIS will report the parks layer's geometry type as *MultiPolygon* and may report the other polygon layers as *Polygon* or *MultiPolygon* depending on the build route — record what you see; both are polygon layers.
- **Two presentations:** **Duplicate Layer** in the Layers panel context menu [X27] gives a second layer on the same source. Symbology ▸ **Categorized** on `status`, **Classify** [X23]; on the duplicate, **Filter…** with `"priority" = 'High'` [X23], [X27]. The source is one GeoPackage table; check its count from the Information tab of a *third*, unfiltered layer or from the Browser panel.
- **Drawing order:** drag layers in the Layers panel; the one nearer the top draws over those below [X27].
- **Scratch copy and move:** **Export ▸ Save Features As…** to a new GeoPackage layer [X27], [X28]; **Toggle Editing**; **Move Feature(s)**; then **Save Layer Edits** or cancel [X24].
- **Identify:** the **Identify Features** tool; clicking in the depot courtyard should identify nothing from *Depot* if the hole survived the build.

Do not claim that QGIS and ArcGIS Pro produce identical *displays* (default symbols, pop-up layouts, and area/length field names differ); claim only that the *counts, types, extents, and invariants* agree, and record any that do not.

### 3.7.8 Paper route (no software)

Using Tables F1–F9 only: complete deliverables A, B (from the tables), C (sketches), and F; for D, write the two layer definitions and derive their row and symbol counts by hand; for E, write the five predictions and derive the observed values arithmetically. State on the submission that no software observation was made. All learning outcomes except the software-observation part of LO6/LO7 are assessable from this route.

---

## 3.8 Independent check and progression gate

Complete this without the Instructor Appendix. Answer under the stated assumptions; if you think an item is under-specified, say what is missing rather than guessing. Item-to-module mapping is shown so that you know what each item tests.

### 3.8.1 Concept questions (six; Q1, Q3, Q5 are multiple choice with one best answer)

**Q1 (3.1; LO1).** The municipality's tree register stores each tree as a point. A new requirement is "report the canopy area shaded by each tree". Which statement is correct?
(a) Add a `canopy_area_m2` field to the point layer; a point with an area attribute is enough to map shaded ground.
(b) Convert the point layer to a polygon layer; the points are then no longer needed.
(c) Points can answer count, location, and proximity questions; a canopy *area on the map* needs a separate polygon representation captured for that purpose, while an attribute suffices for tabular totals.
(d) Store each tree as a polygon and derive the point later when needed, since polygons contain more information.

**Q2 (3.2; LO2).** Road R2 is drawn with a 3 pt stroke at 1:10,000. (i) How wide on the ground does the stroke *appear* (1 pt ≈ 0.353 mm)? (ii) What is R2's stored width, and where is that fact held? (iii) Name one question for which the line representation is sufficient and one for which an area representation is necessary. Show working.

**Q3 (3.3; LO3).** A GeoJSON `Polygon` has two rings. The first ring runs clockwise and the second counter-clockwise. Which statement is correct under RFC 7946?
(a) The file is valid; ring order determines which is exterior, orientation is only advisory.
(b) The file violates the right-hand rule in §3.1.6; exterior rings MUST be counter-clockwise and holes clockwise.
(c) The file is valid because Esri JSON uses clockwise exterior rings and GeoJSON adopted the same convention.
(d) The file is invalid because a polygon cannot have two rings.

**Q4 (3.4; LO4).** The Assets attribute table has fields `OBJECTID`, `asset_id`, `asset_type`, `condition`. A field crew's app labels each asset with `asset_type`. A colleague proposes making `asset_type` the key used to link inspections to assets "because that is what the crew sees". Explain, in no more than five sentences, why neither `asset_type` nor `OBJECTID` should be that key, and which field should.

**Q5 (3.5; LO5).** A map contains layers *L1* and *L2*, both referencing feature class *Roads* (3 records); *L2* has the definition query `width_m >= 10`. A user deletes *L2* from the map. Afterwards, *Roads* has:
(a) 1 record, because the filtered records were removed with the layer.
(b) 2 records, because the records matching the query were deleted.
(c) 3 records; nothing was deleted, because a layer references the dataset.
(d) It depends on whether the project was saved.

**Q6 (3.6; LO6).** A layer's extent is x 0 … 5000, y 0 … 5000, yet its 40 features all lie within x 0 … 1000, y 0 … 1000 except one. (i) What does that tell you about that one feature? (ii) State one consequence for "Zoom To Layer" and one for any tool that uses extents as a pre-filter. (iii) Which module-3.6.2 intake item would you check next?

### 3.8.2 Scenario questions (two)

**S1 (3.1, 3.2, 3.3; LO1, LO2, LO3).** A logistics company asks you to model three things for a delivery-planning map: **a river** that crosses the service area, **a delivery stop** at a customer's gate, and **a restricted area** where vans may not enter (a military compound with a public road running through it, so the restricted land is in two separate pieces). For each, state (a) the geometry type, (b) the scale and purpose assumption that justifies it, (c) one question the choice can answer and one it cannot, and (d) for the restricted area, whether you would store one multipart feature or two single-part features, with a reason based on Table 3.2. Then say what would change in your river choice if the purpose were "flood-risk area per property" instead of "route planning".

**S2 (3.3, 3.5; LO3, LO5).** A colleague sends this note: "I fixed the town data. (1) The park showed as one row but is obviously two parks, so I split it into two rows and gave both the ID PK-01. (2) The depot had a weird square inside it, so I deleted that inner ring to make it a clean square. (3) The Wards layer was covering the requests, so I removed the Wards layer from the map. (4) The requests table had a row with no location so I deleted it." For each of (1)–(4), state whether data was changed or only presentation, whether the change was justified under this chapter's rules, and what should have been done instead if not. Refer to the fixture values where relevant (areas, counts, identifiers).

### 3.8.3 Independent practical task (unfamiliar inputs)

**Campus C-3 — synthetic planar fixture, metres, (x, y), no coordinate reference system, deliberately different from the lab.** Build it in software from the WKT below (or work on paper) and answer the questions. All values are training data.

`buildings.csv`
```text
bldg_id,name,wkt
B1,Library,"MULTIPOLYGON(((100 100,200 100,200 180,100 180,100 100)),((220 100,300 100,300 180,220 180,220 100)))"
B2,Laboratory,"POLYGON((400 100,500 100,500 200,400 200,400 100))"
```

`lake.csv`
```text
lake_id,name,wkt
LK1,Campus lake,"POLYGON((100 300,400 300,400 500,100 500,100 300),(200 350,300 350,300 450,200 450,200 350))"
```

`island.csv`
```text
island_id,wkt
I1,"POLYGON((200 350,300 350,300 450,200 450,200 350))"
```

`paths.csv`
```text
path_id,wkt
W1,"LINESTRING(420 300,480 300,480 360,420 360,420 300)"
W2,"LINESTRING(0 250,500 250)"
```

`bins.csv`
```text
bin_id,x,y,bin_type
K1,150,140,Recycling
K2,250,400,General
K3,450,150,General
K4,,,Recycling
K5,600,600,General
```

**Tasks.**

1. Produce the intake record (count, geometry type, fields, extent, empty-geometry cases) for all five datasets.
2. For each of B1, LK1, W1, state the structural feature (multipart / hole / closed line) and give the area or length by hand, showing working.
3. Say which bins are inside a building, which bin is inside the lake polygon, and which bin is on the island — with a one-sentence reason for K2.
4. Give the extent of B1 and the fraction of that extent actually occupied by the building.
5. Identify one dataset whose representation is *unsuitable* for the question "how long is the shoreline walk around the lake?" and say what representation would answer it.
6. Build two layers on `bins` — one styled by `bin_type`, one filtered to `bin_type = 'General'` — and report each layer's row count and symbol count, and the source count. State the invariant.
7. Predict, then (if using software) verify, what changes if K5 is moved to (450, 250) on a scratch copy.

Submit the intake record, the working for items 2–4, the layer definitions and counts for item 6, the prediction log for item 7, and a statement of what was done in software versus on paper.

### 3.8.4 Oral explanation (one)

Choose either the *Parks* or the *Depot* dataset from the lab. In no more than two minutes, without notes, explain **what information it represents, what it omits, and what is merely styled** — naming the feature count, the part or ring structure, one question it answers, one it cannot, and one presentation setting that changes how it looks without changing it. Finish by naming one thing you would need to check before trusting the dataset for a real decision.

### 3.8.5 Submission requirements and scoring

**Submit:** written answers to Q1–Q6 and S1–S2; the practical task package (3.8.3); attend a short oral session. The lab deliverables from 3.7 are submitted separately and are a prerequisite for the gate.

**Scoring (100 points).**

| Component | Points | What earns the points |
| --- | --- | --- |
| Concept questions Q1–Q6 | 30 (5 each) | Correct answer *and* reasoning consistent with the chapter; arithmetic shown where asked |
| Scenario S1 | 12 | Geometry type, scale/purpose assumption, can/cannot questions, and a Table 3.2-based multipart decision for all three objects; the purpose-change reflection |
| Scenario S2 | 13 | Each of (1)–(4) correctly classified as data vs presentation and justified vs not, with the correct alternative action |
| Practical — intake and structure (items 1–2) | 12 | Counts, types, extents, empty-geometry case (K4) correct; areas/lengths correct with working |
| Practical — containment, extent, unsuitability (items 3–5) | 12 | K2's status reasoned from the hole; extent fraction computed; unsuitable representation identified with a suitable alternative |
| Practical — two presentations and prediction (items 6–7) | 11 | Row/symbol/source counts correct; invariant stated; predictions written before observation |
| Oral explanation | 10 | Represented / omitted / styled all addressed; counts and structure correct; a genuine check named |

**Progression rule.** Pass at 80 points or more **and** no critical misconception. For this chapter the critical misconceptions are: (1) treating a layer's filter, symbology, or removal as a change to, or deletion of, the dataset; (2) treating a closed line as an area, or an area's bounding extent as its shape; (3) reporting a feature count that silently drops or silently includes records with no geometry; (4) using a display label or a system ObjectID as the business identifier for linking. Any of these triggers remediation and a fresh exercise (Instructor Appendix I.5) regardless of the aggregate score. A learner who cannot say what a dataset *omits* in the oral item has not met the blueprint's gate condition ("which information is represented, omitted, and merely styled") and should retake the oral item.

---

## 3.9 Media and source brief

The full media brief — slide outline, audio-lesson outline, diagram specifications, and the optional 3D scene — is in the Media Appendix so that it can be updated independently of the teaching text. This module records the blueprint's three requirements and how they are met.

1. **Sketch → geometry → attribute cards → layer styles.** Diagram D1 (3.1.3) and the four-frame sequence D2 (Media Appendix M.3) show one hand sketch of the street becoming vertices, then a feature with an attribute card, then the same feature drawn two ways by two layers. All drawings are original schematics on the training grid; none is presented as measured evidence.
2. **Optional 3D teaching scene.** Media Appendix M.5 specifies a scene in which one vertex of the school footprint is raised along a labelled Z axis, and then shows why a Z per vertex does not make a building solid. It is labelled a schematic, uses the fixture's metres, and has a 2D/static alternative (D9) so that no essential concept depends on a graphics device. No other 3D content is proposed: multipart features, holes, extents, and layers all teach better as 2D diagrams and tables.
3. **Sources.** Vector representation and layers: [S10]; attributes and attribute-driven symbology: [S23]; format-specific structure: [S18], with Esri JSON [X02] and WKT/PostGIS [X22] for contrast. Platform procedures cite the ArcGIS Pro and QGIS pages listed in the reference list. **Transition:** Chapter 4 introduces the other fundamental representation — a grid of cells, each holding a value — and asks what happens to "feature", "attribute", and "extent" when there are no vertices at all.

---

## Glossary

Terms are defined as used in this chapter; later chapters refine several of them.

| Term | Definition |
| --- | --- |
| **Asset** | A thing the municipality owns and maintains, represented as one feature with a current state. |
| **Attribute** | A descriptive, non-spatial value stored with a feature; "nonspatial information about a geographic feature" [X07]. |
| **Attribute table** | The table of all records of one dataset, one row per feature [S23, §4.2]. |
| **Basemap** | Reference layers (streets, imagery) drawn beneath operational data for context [X10]. |
| **Business identifier** | An identifier assigned and maintained by the organisation's process (e.g. `request_id`), stable across copies and exports. |
| **Dataset** | The stored collection of features: geometry plus attributes, one geometry type, one schema (feature class, table, file). |
| **Definition query / provider filter** | A layer setting that limits which records the layer retrieves, draws, lists, selects, and processes, without changing the dataset [X04], [X23]. |
| **Drawing order** | The order in which layers are painted: bottom of the list first, top last, so upper layers cover lower ones [X11], [X27]. |
| **Empty (null) geometry** | A record that has attributes but no geometry; counted in the table, ignored by spatial operations [S18, §3.2], [X02]. |
| **Extent (bounding box, envelope)** | The smallest axis-aligned rectangle containing a geometry or dataset: x-min, y-min, x-max, y-max [X02], [S18, §5]. |
| **Exported copy** | A new, independent dataset written from a layer or selection; later edits to source and copy do not propagate [X13]. |
| **Feature** | A represented entity: one geometry (possibly empty or multipart) plus attributes, stored as one record. Includes administrative and analytical areas. |
| **Feature class** | Esri's term for a dataset of features with one geometry type and one set of attribute columns [X01]. |
| **Field** | A column in a table holding one attribute for every record [S23, §4.2]. |
| **Field alias** | A display name for a field, distinct from its stored name [X07]. |
| **GeoJSON** | A JSON text format for geometry, features, and collections defined by RFC 7946; positions are x-like then y-like; exterior rings counter-clockwise; CRS fixed to WGS 84 [S18]. |
| **Geometry** | The part of a feature that represents shape and position: a point, a polyline, or a polygon built from vertices. |
| **Hole (interior ring)** | A ring inside a polygon's exterior ring that excludes an area from the polygon [S18, §3.1.6], [X22]. |
| **Inspection** | A record of an observation of an asset on a date; stored as its own record, linked by `asset_id`. |
| **Label** | Text drawn beside a feature from a field or expression; a presentation setting. |
| **Layer** | A use of a dataset in a map: a reference plus presentation and query settings [X03]. |
| **Line / polyline** | Geometry of two or more vertices joined by segments; has length, no area [S10, §3.3]. |
| **M (measure)** | An optional per-vertex value along a line that is not a spatial axis (distance along a route, time) [X01], [X22]. |
| **Map** | An ordered stack of layers sharing one view, stored in a project or web map. |
| **Multipart feature** | One record whose geometry has several disjoint parts (MultiPoint, MultiLineString, MultiPolygon) [X01], [S18, §3.1.7]. |
| **ObjectID** | A system-managed integer that uniquely identifies a record in one ArcGIS table; cannot be null; owned by the software [X05], [X07]. |
| **Operational layer** | A layer of the data being worked on, as opposed to the basemap. |
| **Point** | Geometry of a single vertex [S10, §3.2]. |
| **Polygon** | Geometry of one exterior ring and zero or more interior rings enclosing an area [S10, §3.4], [X22]. |
| **Representation** | The geometry and attributes chosen to stand for a real thing, for a stated purpose and scale. |
| **Ring (linear ring)** | A closed vertex list (first = last) that bounds an area when stored as part of a polygon [S18, §3.1.6]. |
| **Scale** | The ratio of map distance to ground distance (1:50,000 means 1 mm on the map is 50 m on the ground). |
| **Segment** | The piece of a line or ring between two consecutive vertices [X01]. |
| **Selection** | A marked subset of records shown in both map and table without changing them [X07]. |
| **Symbol / symbology** | How features are drawn (colour, size, stroke width); a layer setting that does not change data [S23, §4.3–4.6]. |
| **System identifier** | An identifier the software assigns and owns (ObjectID, GlobalID); scoped to one table [X05]. |
| **Vector data** | Data representing things as points, lines, and polygons built from coordinates. |
| **Vertex** | One stored position (x, y, optionally z and m) in a geometry [S10, §3.1]. |
| **WKT (Well-Known Text)** | The OGC text form of geometry (`POINT`, `LINESTRING`, `POLYGON`, `MULTIPOLYGON`, …) [X22]. |
| **X, Y, Z** | The two planar coordinates and an optional third (height) coordinate of a vertex; their meaning is fixed by a coordinate reference system (Chapter 5). |

## Recap

- A real thing and its representation are different; point versus polygon is a choice made for a purpose and a scale, and richer geometry is different information, not more truth. Assets, incidents, and inspections are three collections. A feature is one record of geometry plus attributes, may be an invisible area, and may even lack geometry (3.1).
- Points, polylines, and polygons are lists of vertices with reading rules; a layer holds one geometry type; a symbol's stroke width is a display decision unrelated to ground width; X, Y are always present, Z and M are optional and format-dependent (3.2).
- Multipart features are one record with several parts; holes exclude area; a closed line is not a polygon; WKT, GeoJSON, and Esri JSON serialise the same ideas with different rules — including opposite ring orientations (3.3).
- Records, fields, and values live in the attribute table; the map and the table are two views of one record; the business identifier is not the system ID and not the label; inspections are related records, not extra columns (3.4).
- A dataset is stored; a layer references it with its own filter and style; a map stacks layers; a filtered view stays live while an exported copy diverges; drawing order can hide data (3.5).
- Extent is a bounding box, not a shape; an intake record notes count, type, fields, and empty geometries; predict which stored records change before you act — presentation changes touch none, edits touch exactly the records you edit, and edits are staged until saved (3.6).

**Next:** Chapter 4 introduces raster data — grids of cells with values, not vertices — and revisits extent, resolution, and missing data (NoData) for that model.

## Cross-references to later chapters

| Topic deferred here | Where it is taught |
| --- | --- |
| What a coordinate reference system adds to (x, y); coordinate order in other systems; vertical reference for Z | Chapter 5 |
| Measuring length and area on the Earth rather than on a flat grid | Chapter 6 |
| Format conversion checks (WKT ↔ GeoJSON ↔ Esri JSON; lost M values; curves); GeoPackage, shapefile, geodatabase details | Chapter 7 |
| Designing identifiers, relationships (assets ↔ inspections), cardinality, domains | Chapter 8 |
| Geometry validity (unclosed rings, self-intersections, orientation), repairing empty geometries, editing with an audit trail | Chapter 9 |
| Boundary semantics for containment (P5 on the shared edge; TR-0302 in the hole), joins across related tables | Chapter 10 |
| Overlay operations that split a multipart park by ward; buffers around points versus footprints | Chapter 11 |
| Symbol design, label placement, drawing order as cartography | Chapter 12 |

---

## References

All pages were opened and read by the author on **19 September 2026**. IDs [S10], [S18], [S23] are the blueprint's reference register entries used by this chapter; [X01]–[X30] are additional official pages consulted to support specific claims. Esri "latest" pages change without notice; several `doc.esri.com` URLs guessed from older `pro.arcgis.com` paths returned HTTP 404 during preparation, so only the URLs listed below were actually read. Re-check before reuse.

| ID | Publisher | Page title | URL | Accessed |
| --- | --- | --- | --- | --- |
| S10 | QGIS Project | A Gentle Introduction to GIS — Vector Data (3.44) | https://docs.qgis.org/3.44/en/docs/gentle_gis_introduction/vector_data.html | 2026-09-19 |
| S18 | RFC Editor | RFC 7946 — The GeoJSON Format (sections 3.1.1–3.1.7, 3.2, 3.3, 4, 5) | https://www.rfc-editor.org/rfc/rfc7946 | 2026-09-19 |
| S23 | QGIS Project | A Gentle Introduction to GIS — Vector Attribute Data (3.44) | https://docs.qgis.org/3.44/en/docs/gentle_gis_introduction/vector_attribute_data.html | 2026-09-19 |
| X01 | Esri | ArcGIS Pro — Feature class basics | https://doc.esri.com/en/arcgis-pro/latest/help/data/geodatabases/overview/feature-class-basics.html | 2026-09-19 |
| X02 | Esri | ArcGIS REST APIs — Geometry objects | https://developers.arcgis.com/rest/services-reference/enterprise/geometry-objects/ | 2026-09-19 |
| X03 | Esri | ArcGIS Pro — Layers | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/layer-properties/layers.html | 2026-09-19 |
| X04 | Esri | ArcGIS Pro — Definition query | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/layer-properties/definition-query.html | 2026-09-19 |
| X05 | Esri | ArcGIS Pro — ArcGIS field data types | https://doc.esri.com/en/arcgis-pro/latest/help/data/geodatabases/overview/arcgis-field-data-types.html | 2026-09-19 |
| X06 | Esri | ArcGIS Pro — Check Geometry (Data Management) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/check-geometry.html | 2026-09-19 |
| X07 | Esri | ArcGIS Pro — Essential table and attribute information vocabulary | https://doc.esri.com/en/arcgis-pro/latest/help/data/tables/essential-table-and-attribute-information-vocabulary.html | 2026-09-19 |
| X08 | Esri | ArcGIS Pro — Open tabular data | https://doc.esri.com/en/arcgis-pro/latest/help/data/tables/open-tabular-data.html | 2026-09-19 |
| X09 | Esri | ArcGIS Pro — View all or only the selected records | https://doc.esri.com/en/arcgis-pro/latest/help/data/tables/view-all-or-only-the-selected-records.html | 2026-09-19 |
| X10 | Esri | ArcGIS Pro — Basemaps | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/map-authoring/author-a-basemap.html | 2026-09-19 |
| X11 | Esri | ArcGIS Pro — Symbol layer drawing (layer drawing-order statement) | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/layer-properties/symbol-layer-drawing.html | 2026-09-19 |
| X12 | Esri | ArcGIS Pro — Multipart To Singlepart (Data Management) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/multipart-to-singlepart.html | 2026-09-19 |
| X13 | Esri | ArcGIS Pro — Copy Features (Data Management) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/copy-features.html | 2026-09-19 |
| X14 | Esri | ArcGIS Pro — Set layer properties | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/layer-properties/set-layer-properties.html | 2026-09-19 |
| X15 | Esri | ArcGIS Pro — Get Count (Data Management) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/get-count.html | 2026-09-19 |
| X16 | Esri | ArcGIS Pro — Unique values | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/layer-properties/unique-value.html | 2026-09-19 |
| X17 | Esri | ArcGIS Pro — Save or discard edits | https://doc.esri.com/en/arcgis-pro/latest/help/editing/save-or-discard-edits.html | 2026-09-19 |
| X18 | Esri | ArcGIS Pro — Move, rotate, or scale features | https://doc.esri.com/en/arcgis-pro/latest/help/editing/move-or-rotate-or-scale-a-feature.html | 2026-09-19 |
| X19 | Esri | ArcGIS Pro — Navigate maps and scenes | https://doc.esri.com/en/arcgis-pro/latest/get-started/navigate-your-data.html | 2026-09-19 |
| X20 | Esri | ArcGIS Pro — XY Table To Point (Data Management) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/xy-table-to-point.html | 2026-09-19 |
| X21 | Esri | ArcGIS Pro — ArcPy FromWKT | https://doc.esri.com/en/arcgis-pro/latest/arcpy/functions/fromwkt.html | 2026-09-19 |
| X22 | PostGIS Project | PostGIS Manual — Data Management (§4.1 Spatial Data Model: geometry types, Z/M, WKT, empty geometries) | https://postgis.net/docs/using_postgis_dbmanagement.html | 2026-09-19 |
| X23 | QGIS Project | QGIS Desktop 3.40 User Guide — The Vector Properties Dialog | https://docs.qgis.org/3.40/en/docs/user_manual/working_with_vector/vector_properties.html | 2026-09-19 |
| X24 | QGIS Project | QGIS Desktop 3.40 User Guide — Editing | https://docs.qgis.org/3.40/en/docs/user_manual/working_with_vector/editing_geometry_attributes.html | 2026-09-19 |
| X25 | QGIS Project | QGIS Desktop 3.40 User Guide — Working with the Attribute Table | https://docs.qgis.org/3.40/en/docs/user_manual/working_with_vector/attribute_table.html | 2026-09-19 |
| X26 | QGIS Project | QGIS Desktop 3.40 User Guide — Processing algorithms: Vector geometry | https://docs.qgis.org/3.40/en/docs/user_manual/processing_algs/qgis/vectorgeometry.html | 2026-09-19 |
| X27 | QGIS Project | QGIS Desktop 3.40 User Guide — General Tools (Layers panel) | https://docs.qgis.org/3.40/en/docs/user_manual/introduction/general_tools.html | 2026-09-19 |
| X28 | QGIS Project | QGIS Desktop 3.40 User Guide — Creating Layers | https://docs.qgis.org/3.40/en/docs/user_manual/managing_data_source/create_layers.html | 2026-09-19 |
| X29 | QGIS Project | QGIS Desktop 3.40 User Guide — Opening Data (delimited text) | https://docs.qgis.org/3.40/en/docs/user_manual/managing_data_source/opening_data.html | 2026-09-19 |
| X30 | Esri | ArcGIS Online — Create hosted feature layer views | https://doc.arcgis.com/en/arcgis-online/manage-data/create-hosted-views.htm | 2026-09-19 |

---

# Instructor Appendix (separate from learner material)

Do not distribute this section to learners before the 3.8 gate. All answers below were derived by hand from Tables F1–F9 and the Campus C-3 fixture; none was observed in software.

## I.1 Answers and reasoning — comprehension checks

| Check | Expected answer and reasoning |
| --- | --- |
| 3.1.1 | Use `Schools_point`: one point falls in exactly one ward (the only ambiguity is a point exactly on the shared edge, as with P5 in Chapter 1). The footprint forces a rule for a school straddling a boundary — fully inside, majority of area, or any overlap — which must be stated before the count is meaningful. |
| 3.1.2 | (b). A new pole is a new asset with its own install year and inspection history; overwriting `installed` (a) destroys the record that a 2018 pole existed; (c) is wrong because P1 is an event that was reported *when the old pole existed* — its history should not be rewritten to point at an asset that did not exist at the time. Accept a nuanced answer that keeps SL-0113's row with a retired status and records the replacement. |
| 3.1.3 | (i) Yes — an administrative area with geometry and attributes. (ii) No — a derived measurement; it could be stored as an attribute of P1 but is not itself a feature. (iii) In this fixture, no — a non-spatial related record; note that GeoJSON would allow an "unlocated" Feature, so accept "a record, not a spatial feature". (iv) No — part of the coordinate frame, not data. |
| 3.2.1 | 4 segments (5 vertices, closed); length 4 × 50 = 200 m. Store as a line because the question is what buses drive *along* (length, connectivity), not what the loop encloses. |
| 3.2.2 | Acceptable: a rough asphalt total for a budget estimate (R1: 24,000 m²; R2: 7,000 m²; L1: 1,200 m²). Wrong: any question about *where* the surface is — junctions are double-counted (R1/R2 meet at (500, 500)), widenings and lay-bys are missed. A road-surface polygon layer is needed. |
| 3.2.3 | RFC 7946 §3.1.1 defines the optional third element as altitude/elevation and discourages more than three elements; "distance along the street" is a measure (M), which GeoJSON cannot carry. Ask the supplier to put the distance in `properties`, confirm the third element is not being read as height, and confirm the first two elements are longitude then latitude. |
| 3.3.1 | Two single-part records (one per ward) make the monthly per-ward area trivial (20,000 m² each). Lost: the "one park = one record" identity — the `park_id` is now repeated, so a unique-ID rule is needed (Chapter 8), and any park-level attribute must be kept consistent across two rows. |
| 3.3.2 | May be correct: a fence that closes does enclose a compound whose area is meaningful. May be wrong: the fence line is the fence's position, not the legal boundary; two compounds may share a fence (which side gets it?); a "closed" fence may close only by coincidence or with a gate gap; and the area computed is inside the fence centreline, not the plot. |
| 3.3.3 | GeoJSON (counter-clockwise): `[[1000,0],[2000,0],[2000,1000],[1000,1000],[1000,0]]` — Table F1's order is already counter-clockwise (right, up, left, down). Esri JSON (clockwise): `[[1000,0],[1000,1000],[2000,1000],[2000,0],[1000,0]]`. |
| 3.4.1 | `request_id` (business identifier) and the software's system ID. `reported_on` + `channel` is not designed as an identifier: two phone reports on one day would collide, and either value may be corrected later. |
| 3.4.2 | P2, P3, P6 kept (3 records); 3 symbols (all have geometry). Label: `category` is the best choice for a supervisor (what is wrong); `request_id` is acceptable if the justification is cross-referencing paperwork. `priority` is a poor label because every visible feature has the same value. |
| 3.4.3 | (1) Never inspected (Table F9 has no row — consistent with `Unknown`). (2) Inspected, but the record was lost or kept in another system. (3) Inspected and the summary field was never updated. Check the inspections table first; then provenance (who maintains it, since when); the asset row alone cannot distinguish (1) from (2). |
| 3.5.1 | "Did you remove the *layer* from the map, or delete the *dataset* (file / feature class) from storage?" and "Do any other maps, projects, or services reference that dataset?" |
| 3.5.2 | Dashboard: a filtered view (definition query in Pro; provider filter in QGIS; hosted feature layer view in ArcGIS Online) so that it stays current. Auditor: an exported copy, named and described with the date and the filter, e.g. `Requests_OpenReopened_asof_2026-09-19` with "filter: status IN ('Open','Reopened')" in its metadata. |
| 3.5.3 | Fully visible: P6 only (outside both wards). Hidden: R1, R2, L1, P1–P5 (all lie within the ward squares; R2 and L1 entirely inside A and B respectively). Partly visible: none in a strict reading; R1's end points (0, 500) and (2000, 500) lie exactly on ward edges and may show depending on the outline symbol — accept this as an edge-case observation. |
| 3.6.1 | R2 extent: x 500 … 800, y 0 … 900. (600, 300) is inside that box; the nearest point of R2 is (500, 300) on the vertical segment, 100 m away. |
| 3.6.2 | Any three of: two records with empty geometry (check: Check Geometry / null-geometry expression; table count minus box-selected count); two features drawn exactly on top of others (check: select by box and count, or sort by coordinates); two features hidden by drawing order or a symbol class turned off (check: move layer to top / check class visibility); two features far outside the visible area — but then Zoom To Layer would have expanded the extent, so this is unlikely (check: read the extent). A definition query is *not* an explanation because it would also filter the table. |
| 3.6.3 | (a) 0; (b) 0; (c) 0 in the original (1 in the copy); (d) 0. |

## I.2 Answer key — 3.8 concept and scenario questions

**Q1 — (c).** (a) an attribute cannot be *drawn as an area*; it can total areas in a table. (b) discards a representation still needed for counts and proximity. (d) repeats the "richer geometry is more truth" misconception (3.1.1); polygons are not derivable from points, and the reverse (a point from a polygon) is derivable but not the point of the question.

**Q2.** (i) 3 pt ≈ 3 × 0.353 mm = 1.06 mm on the page; at 1:10,000 that represents 1.06 mm × 10,000 ≈ **10.6 m** on the ground. (ii) 7 m, held in the attribute `width_m` (Table F2), not in the geometry. (iii) Sufficient: "how long is Station Road?" (1,000 m) or "which requests are within 300 m of it?". Necessary: "how many square metres of Station Road need resurfacing at the junction?" or "does the kiosk overlap the carriageway?".

**Q3 — (b).** §3.1.6: exterior rings counter-clockwise, holes clockwise; (a) is false because the RFC uses MUST; (c) confuses the two formats — Esri JSON is clockwise-exterior [X02], GeoJSON is the opposite; (d) polygons may have any number of interior rings.

**Q4.** `asset_type` is a label for reading: it is not unique (TR-0301 and TR-0302 are both "Tree"), so a link on it would attach every tree's inspections to every tree. `OBJECTID` is unique but system-owned and scoped to one table: it is assigned by the software, cannot be edited, and a copy or export of the assets gets new values, so links made on it break the moment the data is moved. `asset_id` is the business identifier: unique by the organisation's process, meaningful to crews, and stable across copies.

**Q5 — (c).** A layer references a dataset; removing the layer removes the reference and its settings (including the definition query) and nothing else. (d) is a distractor: saving the project stores layer settings, not data.

**Q6.** (i) One feature lies at or near (5000, 5000) — an outlier, most likely a data-entry or units error (Chapter 9). (ii) Zoom To Layer shows a mostly empty map with a cluster in one corner; an extent pre-filter will treat this layer as a candidate over a 5 km square, which is slower but not wrong — the danger is the reverse: someone reading the extent as "coverage" (3.6.1). (iii) The extent item itself, then identify the outlier record (sort by coordinates or select by box) and record it as a defect for Chapter 9; the empty-geometry check is *not* the next step here because the outlier *has* geometry.

**S1 — model answer points.** *River:* line for route planning (which roads cross it; where bridges are); at planning scale its width is not needed; cannot answer "how much land is within the flood channel". *Delivery stop:* point at the gate; answers "how far from the depot / which route"; cannot answer which side of the building the loading bay is. *Restricted area:* polygon; answers "does the route enter it"; cannot answer *why* it is restricted unless an attribute says so. *Multipart decision:* one multipart feature if one authority, one rule, one ID, and the report is "avoid this compound"; two single-part features if the pieces have different rules, times, or contacts, or if per-piece incident counts are needed (Table 3.2). *Purpose change:* for flood-risk-per-property the river must become a polygon (the water surface or the modelled flood area), because the question is about area overlap with parcels; a centreline cannot answer it.

**S2.** (1) *Data changed; not justified as done.* PK-01 is managed as one park; if per-ward pieces are needed, split on a copy with Multipart To Singlepart (which records the origin in `ORIG_FID`) and assign distinct identifiers — two rows with the same `park_id` break the identifier rule (3.4.1). (2) *Data changed; not justified.* The inner ring is the courtyard; deleting it changes the area from 30,000 m² to 40,000 m² and makes TR-0302 "inside the depot". Restore the ring. (3) *Presentation only; justified* — nothing was deleted; a better fix is to move Wards to the bottom or use an outline symbol so the wards remain visible. (4) *Data changed; not justified.* P7 is a live request awaiting location; keep it as a record with empty geometry (count stays 7) and resolve it through the Chapter 9 process.

## I.3 Expected results — 3.7 lab deliverables

- **A (geometry-choice table).** Accept any justified choice consistent with Tables 3.1–3.2. Required insights: school point for overview/counting and footprint for area; roads as lines with `width_m` as an attribute; wards, park, depot as polygons; requests and assets as points; `Wards_as_points` flagged as unsuitable for containment; inspections as non-spatial. A learner who proposes polygons for requests "because they are more accurate" has the 3.1.1 misconception.
- **B (intake record).** Must match Table 3.4 and the 3.6.1 extents. Requests must read 7 with 1 empty. Parks 1 record / 2 parts; Depot 1 record / 2 rings.
- **C (record/map pairs).** DR-0042: point, omits the drain's size and cover type; DP-01: ring-minus-hole, omits buildings inside; PK-01: two parts, one row, omits per-part condition; P7: row present, no symbol, omits location.
- **D (two presentations).** Layer 1: 7 rows, 6 symbols, 5 status classes; Layer 2: 3 rows / 3 symbols (P2, P3, P6); source 7 before and after. The explicit invariant statement is required.
- **E (prediction log).** Predictions must be dated/timed before the observations: copy count 7; P1 → 200 m from R1; still in Ward A; original unchanged; High-priority layer unchanged. Marks are for *prediction before observation*, not for the move itself.
- **F.** Must state that a point per ward cannot decide containment, can serve as a label/reference position, and that polygons are required.

## I.4 Hand verification of fixture arithmetic

- **Ward areas:** 1000 × 1000 = 1,000,000 m² each. Ring orientation of Table F1 as listed: shoelace sum for A = (0·0 − 1000·0) + (1000·1000 − 1000·0) + (1000·1000 − 0·1000) + (0·0 − 0·1000) = 2,000,000 > 0 → counter-clockwise (GeoJSON-exterior convention).
- **R2 length:** 500 + √(300² + 400²) = 500 + 500 = 1,000 m. Straight-line end-to-end: √(300² + 900²) = √900,000 ≈ 948.7 m.
- **L1:** 4 segments × 50 m = 200 m; enclosed square 50 × 50 = 2,500 m² (not part of the geometry).
- **School footprint:** 60 × 40 = 2,400 m²; centroid (400, 700). Distances from P1 (200, 200): to the point √(200² + 500²) = √290,000 ≈ 538.5 m; to the corner (370, 680) √(170² + 480²) = √259,300 ≈ 509.2 m. A hypothetical request at (400, 400): 300 m to the point; 280 m to the footprint's bottom edge (y = 680).
- **Park PK-01:** 200 × 100 × 2 = 40,000 m²; extent 100 … 1500 × 50 … 150 = 140,000 m²; occupied fraction 40,000 / 140,000 ≈ 0.286.
- **Depot DP-01:** outer 40,000 − hole 10,000 = 30,000 m²; extent box 40,000 m². Shoelace for the outer ring as written (1400, 600) → (1600, 600) → (1600, 800) → (1400, 800): −120,000 + 320,000 + 160,000 − 280,000 = +80,000 → counter-clockwise; area 40,000 ✔. TR-0302 (1500, 700): 1450 < 1500 < 1550 and 650 < 700 < 750 → in the hole.
- **Stroke arithmetic:** 1 pt = 1/72 in = 25.4/72 mm ≈ 0.3528 mm. 2 pt ≈ 0.706 mm → ×50,000 = 35.3 m; ×5,000 = 3.53 m; ×500 = 0.353 m. 12 m at 1:50,000 = 0.24 mm; at 1:5,000 = 2.4 mm; at 1:500 = 24 mm. 3 pt ≈ 1.058 mm → ×10,000 = 10.6 m.
- **Filters:** `status IN ('Open','Reopened')` → P1, P3, P5, P7 (4; 3 drawable). `priority = 'High'` → P2, P3, P6 (3). Distinct `category`: 5. Distinct `status`: 5 (Open, In progress, Resolved, Reopened, Closed – duplicate).
- **Extents:** Requests 200–2200 × 200–900; Roads 0–2000 × 0–900; Assets 205–2190 × 195–700; Wards 0–2000 × 0–1000; Parks 100–1500 × 50–150; Depot 1400–1600 × 600–800; R2 alone 500–800 × 0–900.
- **Move check:** P1 at (200, 300) → |300 − 500| = 200 m to R1; still inside A.
- **Campus C-3 (3.8.3):** Buildings 2 records (B1: 2 parts, 8,000 + 6,400 = 14,400 m²; B2: 10,000 m²), extent 100–500 × 100–200; B1 extent 100–300 × 100–180 = 16,000 m², occupied 14,400 / 16,000 = 0.90. Lake 1 record, 1 hole: 60,000 − 10,000 = 50,000 m², extent 100–400 × 300–500. Island 1 record, 10,000 m². Paths 2 records: W1 closed line 4 × 60 = 240 m (no area); W2 500 m; extent 0–500 × 250–360. Bins 5 records, 4 with geometry, K4 empty; extent 150–600 × 140–600. K1 inside B1 part a; K3 inside B2; K2 inside the lake's hole → not inside LK1, inside I1; K5 outside all. Item 5: the Paths layer contains no walk around the lake; the lake polygon's outer perimeter (2 × (300 + 200) = 1,000 m) is the *water edge*, a proxy, not the path — a line feature for the walkway is needed. Item 6: styled layer 5 rows / 4 symbols / 2 classes (Recycling: K1, K4; General: K2, K3, K5); filtered layer 3 rows / 3 symbols; source 5. Item 7: K5 → (450, 250) lies exactly on W2 (distance 0, a boundary case), outside every polygon; Bins extent shrinks to 150–450 × 140–400 because K5 was the x-max and y-max; the filtered layer still has 3 rows.

## I.5 Building and verifying the Chapter 3 Town package (not execution-tested)

Nothing in this section has been run by the author; each route names what to verify. Record the application version and the outcome in I.8.

**Inputs.** The ten CSV files in 3.7.2, saved UTF-8 with the header rows exactly as printed. Keep the files under `Chapter03_Data/` beside the project.

**Route A — GDAL `ogr2ogr` to GeoPackage.** Chapter 1 Instructor Appendix I.5 documents the GDAL CSV driver behaviour (a `WKT`-named column read as geometry; `X_POSSIBLE_NAMES`/`Y_POSSIBLE_NAMES` for points; no `-a_srs`; how an undefined CRS is recorded) with its citations; reuse it. Note that the Chapter 3 files name the geometry column `wkt` in lower case — confirm the driver's column-name matching or rename to `WKT`. *Verify:* ten layers (nine spatial + `inspections` as a non-spatial table); counts 2, 3, 7, 4, 1, 1, 1, 1, 2, 3; `parks` reports one feature of type MultiPolygon; `depot` one feature with two rings; `requests` has seven rows including P7 with an empty geometry — if the driver dropped P7, load `requests.csv` as a plain table and build the point layer in the application instead.

**Route B — QGIS 3.40.** Add each CSV with **Add Delimited Text Layer**: WKT files with geometry "Well known text (WKT)", point files with "Point coordinates" (X = `x`, Y = `y`), `inspections.csv` with "No geometry (attribute only table)" [X29]. **Verification items:** (1) what to enter for *Geometry CRS* so the layer is left without an Earth reference, and what QGIS reports for it; (2) whether the P7 row loads as a feature with null geometry or is rejected — if rejected, document the count as 6 in the package readme and adjust 3.7.4 for the QGIS route, or create P7 by adding a geometry-less feature in an editing session. Then **Export ▸ Save Features As…** each layer to `Chapter03_Town.gpkg` [X28]. Re-open the GeoPackage and repeat the count/type/ring checks above from the Information tab [X23].

**Route C — ArcGIS Pro (3.7 docs).** Points: **XY Table To Point** with X Field `x`, Y Field `y`; the tool's default **Coordinate System** is WGS 84 [X20] — clear it so the output is *unknown*, otherwise metre values are labelled degrees (Chapter 1 I.5 caution). The tool writes "an empty geometry" for P7's null coordinates [X20], which is the desired behaviour; verify the count is 7. Lines and polygons: there is no ribbon tool that reads a WKT column; use ArcPy — `arcpy.FromWKT(wkt_string)` "creates a geometry object from a well-known text (WKT) string" [X21] — and write the geometries and attributes to new feature classes with an insert cursor. **Verification item:** the insert-cursor procedure and the handling of multipart and holed polygons by `FromWKT` were not re-read for this chapter; test with `parks.csv` and `depot.csv` and inspect the ring/part counts. Alternatively open the Route A/B GeoPackage directly in Pro.

**Ring orientation.** The depot hole is written counter-clockwise in `depot.csv`. If any route reports an orientation problem or draws the hole as filled, reverse the hole ring to (1450 650, 1450 750, 1550 750, 1550 650, 1450 650), rebuild, and log the change.

**Project assembly.** Layer order top to bottom: Requests, Assets, Schools_point, Roads, Schools_footprint, Depot, Parks, Wards (outline symbol), Wards_as_points (off by default). Label Requests on `request_id`. Save as `Chapter03_Town.aprx` / `Chapter03_Town.qgz` with relative paths. Before issuing the lab, run steps 3–9 of 3.7.3 yourself and enter observed values in I.8; any difference from 3.7.4 must be resolved or documented.

**Retest fixture (fresh values, same concepts) for learners who fail the gate.** Shift every fixture coordinate by (+300, +100), rename identifiers (Q1…Q7, WA/WB, etc.), make the park's parts unequal (150 × 100 and 250 × 100), and place the depot hole off-centre. Recompute the expected counts, areas, extents, and filter results by hand before use; keep the retest key with this appendix.

## I.6 Common mistakes and remediation

| Mistake | Symptom | Remediation |
| --- | --- | --- |
| Filter treated as deletion | "The dataset has 3 requests now" | Show the source count from the Catalog/Browser panel while the filtered layer is open; re-do 3.5.2 |
| Feature count read from the map | 6 requests reported | Compare table count and drawn symbols; introduce P7 explicitly |
| Closed line treated as area | "L1 is 2,500 m²" | Identify L1; show no area field; 3.3.2 |
| Extent read as shape | "The park covers a strip from x = 100 to 1500" | Draw the two parts inside the box; compute the 29 % occupancy |
| Label or ObjectID used as key | Inspections linked by `asset_type` | Show two trees; re-do 3.4.1 with the ORIG_FID example |
| Editing the original instead of the copy | Source P1 at (200, 300) | Discard edits; rebuild from the package; re-do step 8 with Catalog-pane confirmation of the copy |
| "Fixing" the unknown coordinate system | Layer assigned WGS 84 | Explain that this is Chapter 5's topic; restore the package; note it as a preview of the CRS-assignment misconception |
| Multipart split with duplicate IDs | Two PK-01 rows | Show `ORIG_FID` after Multipart To Singlepart; discuss identifier rules |

## I.7 Blueprint corrections, source notes, and verification items

- **No factual error was found in the Chapter 3 specification.** All fixture arithmetic (Appendix A.2 items 5–8 and the new Chapter 3 values) was re-derived by hand and agrees.
- **Source coverage — [S10] definition of a feature.** The QGIS introduction says "a feature is anything you can see on the landscape" (§3.1). The blueprint (3.1.3) correctly asks the author *not* to restrict features to visible objects; the chapter therefore uses the broader definition and supports it with Esri's feature-class examples [X01] and the GeoJSON "unlocated" Feature [S18, §3.2]. This is a coverage note, not a correction.
- **Source coverage — [S10] on symbol width.** [S10] does not discuss line symbol width versus ground width (checked: §3.3, §3.7, §3.8). Module 3.2.2 is original teaching supported by unit arithmetic; the QGIS scale discussion (§3.7) is cited only for the scale-dependence of captured data.
- **Ring orientation.** GeoJSON: exterior counter-clockwise [S18, §3.1.6]. Esri JSON: exterior clockwise [X02]. The PostGIS section read [X22, §4.1.1.4] states no orientation rule; the chapter says so rather than asserting one.
- **Esri URLs.** Several `doc.esri.com` URLs derived from older `pro.arcgis.com` paths returned HTTP 404 on the check date (e.g. `.../object-id-fields.html`, `.../checking-and-repairing-geometries.html`, `.../tables.html`, `.../basemaps.html`, `.../unique-values.html`, `.../navigation-in-2d.html`). Only pages that were actually opened are cited.
- **Verification items for the installed environment (before the lab):** (1) where ArcGIS Pro 3.x displays a layer's *geometry type* (Source tab details or Catalog item properties); (2) the exact **Move To** tool label for absolute-coordinate moves; (3) QGIS delimited-text handling of the blank-coordinate row and of a layer left without a CRS; (4) how each build route reports the fixture's undefined coordinate system; (5) the Show Selected Records count format in the installed Pro release.

## I.8 Instructor change log

| Date | Change | Dependent items to recheck |
| --- | --- | --- |
| 2026-09-19 | Chapter drafted from documentation only; data package not yet built; no procedure executed | I.5 routes; 3.7.4 expected results after the first build; M.3 diagrams if any fixture value changes |

---

# Media Appendix

## M.1 Slide outline (mapped to module IDs)

One learning objective per slide group; each group ends with a prediction question before the answer slide. Speaker notes should read the tables aloud rather than the bullets.

| # | Title | Module | Visual | Prediction question (before reveal) |
| --- | --- | --- | --- | --- |
| 1 | What flows through the systems of Chapter 2? | intro | Chapter 2 role diagram with "vector data" written across the arrows | — |
| 2 | One school, two representations | 3.1.1 | D2 frame 1–2: sketch → point and footprint | "Which can tell you the site area?" |
| 3 | What each representation answers | 3.1.1 | Table 3.1 | — |
| 4 | Asset, incident, inspection | 3.1.2 | Three cards at one location: DR-0042, P5, INS-0002 | "Which card should never be overwritten?" |
| 5 | A feature is one record | 3.1.3 | D1 | "Is Ward B a feature? Is the 300 m distance?" |
| 6 | Point, line, polygon = vertex lists | 3.2.1 | D3a: R2's three vertices and two segments; Ward A's five vertices | "How many segments does L1 have?" |
| 7 | Stroke width is not road width | 3.2.2 | The three-scale table with a 2 pt line drawn identically on each | "At which scale is the symbol wider than the road?" |
| 8 | X, Y, Z, M — and which formats allow them | 3.2.3 | Format-rule table | "Can GeoJSON carry M?" |
| 9 | Multipart and holes | 3.3.1 | D3 | "What is the park's feature count?" |
| 10 | Ring versus closed line | 3.3.2 | D4: L1 and DP-01 side by side with area fields | "Does L1 have an area?" |
| 11 | Same geometry, three serialisations | 3.3.3 | WKT / GeoJSON / Esri JSON of the depot; orientation arrows | "Which way does the GeoJSON exterior ring turn?" |
| 12 | Record, field, value, identifier | 3.4.1 | D5: table row highlighted ↔ P3 on the map | "Which field is the business identifier?" |
| 13 | Style, filter, label — three fields, no data change | 3.4.2 | Requests styled by category, filtered by status, labelled by category | "How many rows? How many symbols?" |
| 14 | Inspections stay in their own table | 3.4.3 | Assets ↔ Inspections cards joined by `asset_id` | "Where does the 2024 inspection go?" |
| 15 | Dataset, layer, map | 3.5.1 | D6 | "Delete the layer — what happens to the data?" |
| 16 | View versus copy | 3.5.2 | D6 with the Copy Features branch | "Edit the source — which one changes?" |
| 17 | Drawing order hides data | 3.5.3 | D8: two frames, Wards on top / at bottom | "How many requests are visible?" |
| 18 | Extent is a box | 3.6.1 | D7 | "What fraction of the park's box is park?" |
| 19 | Intake record | 3.6.2 | Table 3.4 with P7's row highlighted | "6 or 7?" |
| 20 | Predict before you act | 3.6.3 | The four-row operation table | "Which records change when P1 moves on a copy?" |
| 21 | Lab briefing | 3.7 | Deliverables A–F | — |
| 22 | Gate and transition | 3.8–3.9 | Critical misconceptions; Chapter 4 teaser (a grid) | — |

## M.2 Audio-lesson outline

Narration must describe every essential visual in words, introduce each unit and acronym once, and pause for prediction.

1. **Opening (3.1).** Say "representation" and define it; describe the school as "a single dot at four hundred, seven hundred" and "a rectangle sixty metres by forty metres, twenty-four hundred square metres". Read Table 3.1 as pairs: "site area — the point cannot, the footprint can". Pause: "Which representation can tell you where the gate is? … Neither."
2. **Feature definition (3.1.3).** State the three points (invisible areas count; geometry may be absent; one feature is one record). Name P7 aloud as "the request with no location yet".
3. **Geometry (3.2).** Describe R2 as "starts at five hundred, zero; goes straight up to five hundred, five hundred; then diagonally to eight hundred, nine hundred — three vertices, two segments, a thousand metres in total". Introduce "point", the printing unit, once: "one point is about a third of a millimetre". Read the three-scale table slowly with units. Pause before "thirty-five metres".
4. **Dimensions (3.2.3).** Spell out X, Y, Z, M once each; say "GeoJSON allows a third number for altitude and nothing more".
5. **Structure (3.3).** Describe the park as "two rectangles a kilometre apart, one row"; the depot as "a square with a square cut out, and a tree standing in the cut-out". Describe ring direction by walking: "right, up, left, down — counter-clockwise". Pause: "Esri JSON — same or opposite? … Opposite."
6. **Attributes (3.4).** Read the vocabulary table as definitions. Contrast "P3" (identifier) with "Water leak" (label) and "row 3" (system number). Describe the inspections table as "three rows, no shapes, each pointing at an asset by its ID".
7. **Layers (3.5).** Say "references, does not contain" twice. Describe D6 in words: "one cylinder, two arrows to two layer cards, a third arrow to a second cylinder with a broken link". Describe the drawing-order frames: "with the wards painted on top, only P6 remains — six requests are still there, under the paint".
8. **Extent and inspection (3.6).** Describe the park's box: "fourteen hundred metres wide, a hundred tall; the park fills less than a third of it". Read Table 3.4's Requests row: "seven records, one without geometry". Read the four operations and pause after each: "how many records change?"
9. **Lab and gate (3.7–3.8).** Read deliverables A–F; state the invariant "the source count stays seven"; list the four critical misconceptions.
10. **Transition.** "Chapter 4 replaces vertices with cells."

## M.3 Diagram specifications

All diagrams are original schematics on the training grid (metres, y upward, no Earth location), with a caption stating so. Labels use text, not colour alone; a shape or hatch difference accompanies any colour difference.

- **D1 — Sketch to feature (3.1.3).** Three panels, left to right. Panel 1: freehand sketch of a street corner with a drain grate, a school building, and a dashed ward line. Panel 2: the same scene as geometry: a dot labelled "(995, 510)", a rectangle with corners labelled "(370, 680) … (430, 720)", a straight dashed line labelled "x = 1000". Panel 3: three attribute cards tethered to the shapes: `asset_id = DR-0042 / asset_type = Drain`, `school_id = SC-01`, `ward = A | B`. Caption: "Real thing → geometry → attributes = one feature per record."
- **D2 — Progressive reveal, four frames (3.1, 3.4, 3.5; slide 2).** Frame 1: the sketch of the school. Frame 2: point and footprint drawn on a 100 m grid. Frame 3: attribute card attached (SC-01, name). Frame 4: two map-layer cards drawing the same footprint — one with a hatched fill and a label, one with an outline only — with the caption "same record, two layers".
- **D3 — Multipart and hole (3.3.1).** Canvas x 0–2000, y 0–1000 with the ward outlines faint. Panel A: PK-01's two rectangles with identical hatch; one attribute card "PK-01, parts = 2, area = 40,000 m²"; a dashed box around both labelled "extent 100–1500 × 50–150". Panel B: DP-01 as a square with a white square cut out; tree symbol at (1500, 700); card "DP-01, rings = 2, area = 30,000 m²"; annotation "inside outer ring, inside hole → not inside DP-01".
- **D4 — Ring versus closed line (3.3.2).** L1 drawn as a thick stroke with arrowheads along its path and the text "length 200 m, area —"; beside it DP-01's outer ring as a filled polygon with "area 30,000 m²". A small table below each: layer type, length, area.
- **D5 — Table ↔ map (3.4.1).** Left: Table F3 rendered as a table with the P3 row highlighted by a heavy border. Right: the six located points with P3 highlighted by a halo; P7 shown as a table row with an arrow to an empty "no symbol" note on the map. A double-headed arrow between the row and the symbol labelled "one record, two views".
- **D6 — Dataset, layers, copy (3.5.1–3.5.2).** As described in 3.5.2.
- **D7 — Extents (3.6.1).** The fixture map with dashed bounding boxes for Requests, Parks, Depot, and R2, each labelled with its four numbers; the park's box shaded lightly with "29 % occupied".
- **D8 — Drawing order (3.5.3).** Two frames of the fixture map: Frame 1 Wards (solid fill) above Roads above Requests — only P6 visible, with ghost outlines where P1–P5 and the roads are hidden; Frame 2 Wards at the bottom — everything visible. The Contents-pane order shown as a list beside each frame.
- **D9 — 2D alternative to the 3D scene (M.5).** Side view (x–z) of the school footprint as a flat line at z = 0 with one vertex lifted to z = 8 m, labelled "z is per vertex; nothing else is defined"; beside it, a labelled cross-section of what a building solid would need (four wall faces, a roof) marked "not stored".

## M.4 Comparison tables reused on slides

Tables 3.1 (representations), 3.2 (multipart decision), 3.3 (three vocabularies), 3.4 (intake record), the format-dimension table in 3.2.3, the view-versus-copy table in 3.5.2, and the operation table in 3.6.3 are reproduced on slides without change; every value on them is hand-checked and dated 19 September 2026.

## M.5 Optional interactive 3D scene — "Lifting one vertex" (3.9.2)

**Learning objective.** Show that a Z value belongs to a *vertex*, so that raising one vertex of a polygon does not create a building, and that "storing Z" is not "modelling a solid".

**Objects (schematic, synthetic).** The school footprint SC-01 as a polygon with four vertices at z = 0: (370, 680, 0), (430, 680, 0), (430, 720, 0), (370, 720, 0). A labelled Z axis at the polygon's corner, graduated 0–10 m. A ghosted wireframe box 60 × 40 × 8 m labelled "what a solid would need (not stored)".

**Labels and units.** Axes "x (m)", "y (m)", "z (m)"; each vertex labelled with its (x, y, z); a persistent banner "Schematic — training grid — no vertical exaggeration — synthetic data".

**Controls.** A slider "z of vertex 3" from 0 to 8 m; a toggle "show ghost solid"; orbit/zoom.

**Expected behaviour.** Moving the slider raises only vertex 3; the polygon becomes a tilted, non-planar quadrilateral; the attribute card shows `hasZ = true` and the changed vertex's z; the area label reads "planar area 2,400 m² (x–y projection)". Nothing else appears: no walls, no roof. With "show ghost solid" on, the wireframe box appears with the caption "a solid needs faces, not a z per vertex (multipatch — beyond Phase 1)".

**Validation.** (1) With the slider at 0 the four z values are 0 and the projected area is 2,400 m². (2) With the slider at 8 the vertex list reads (430, 720, 8) for vertex 3 and the projected area is still 2,400 m² (raising z does not change x–y). (3) The banner remains visible in every view. (4) The 2D alternative D9 conveys the same statements for readers without a 3D-capable device.

**Not proposed.** No 3D for multipart features, holes, extents, or layers — the 2D diagrams D3, D4, D7, and D8 teach those better, as the blueprint anticipates.
