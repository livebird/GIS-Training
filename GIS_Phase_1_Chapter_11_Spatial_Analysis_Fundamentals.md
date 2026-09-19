# GIS and ArcGIS Fundamentals — Phase 1

# Chapter 11 — Spatial analysis fundamentals

## Document metadata

| Item | Value |
| --- | --- |
| Chapter ID | Phase 1, Chapter 11 |
| Title | Spatial analysis fundamentals |
| Source specification | `GIS_Phase_1_Chapter_Authoring_Blueprint.md`, revision 1.0 (18 September 2026) |
| Document revision | 1.0 — 19 September 2026 |
| Reference-check date | 19 September 2026 (every cited page was opened and read on this date; see the reference list for exact URLs) |
| Audience | Software developers at LiveBird Technologies who have completed Chapters 1–10 and have no other GIS background |
| Software referenced | ArcGIS Pro ("latest" documentation; earlier chapters recorded the release as **3.7** on the check date — record the installed version when the lab is run); QGIS Desktop **3.44** User Guide (the long-term-release edition on the check date); PostGIS documentation for the transferable database examples |
| Execution status | **No procedure in this chapter has been executed in ArcGIS Pro, QGIS, or a database by the author.** Every expected number was derived by hand from the printed fixtures and is labelled *hand-checked*. Every software procedure is written from the official page cited beside it and is marked **not execution-tested**; an instructor must run the lab once in the installed versions (Instructor Appendix I.6) before issuing it. |
| Data status | Every coordinate, record, identifier, date, name, and raster value in this chapter is **synthetic training material**. Nothing describes a real municipality, a real road, a real request, or a real terrain. The Earth-referenced fixture is placed at an arbitrary location chosen for arithmetic convenience. |

### How to use this document

Read the modules in order; each one assumes the ones before it. Boxes labelled **Procedure (version-specific)** contain software steps that will age; everything outside those boxes is durable concept. Boxes labelled **Platform note** separate ArcGIS-, QGIS-, or database-specific behaviour from general GIS principles, and boxes labelled **Verification item** tell an instructor what must be checked in the installed environment before a step is treated as fact. Comprehension checks have no answers in the learner text; the answers are in the Instructor Appendix, which learners should not open before the progression gate in 11.10.

This chapter is the first one in which you *make new things* from GIS data. Chapters 3–9 taught you to understand and repair data; Chapter 10 taught you to ask it questions that return existing records. Chapter 11 teaches five operations that return **new geometry or new numbers** — buffer, clip, intersect, dissolve, spatial join — plus one small raster calculation, and, more importantly, the discipline around them: write the question first, predict the answer, run the operation, check the result against the prediction, and write down what you did. The tools are easy to click. The skill is knowing which one the question needs, and being able to say why the output is right.

---

## Prerequisites

- **Chapter 6 completed.** You can choose between planar and geodesic measurement from the requirement, you know that a metre threshold cannot be applied directly to degree coordinates, and you know Fixture E6 (Wards A and B in EPSG:4326; requests Q1–Q5 delivered in EPSG:32643). This chapter buffers and measures in that fixture and reuses its checked values.
- **Chapter 9 completed.** You can tell valid geometry from a topology-rule violation, and you keep a log of what you changed. Analysis outputs are new datasets and need the same honesty: an output that "looks right" is not evidence.
- **Chapter 10 completed.** You can build an attribute condition, predict which record IDs it returns, tell a *selection* from a *join*, read the spatial predicates (intersects, contains, within, touches, nearest), and you know the training-grid answers for strict-interior versus boundary-inclusive ward membership and for the 300 m road-distance threshold (Appendix A.2 of the blueprint, restated below). Chapter 11 turns those predicates into constructed outputs.
- **Chapter 4 (raster vocabulary).** Cell, cell size, extent, NoData, alignment. Module 11.7 uses them without re-teaching them.
- Ordinary developer experience: reading a small SQL query, keeping a worksheet, and the idea that a function's *output type* matters as much as its value.

> **Instructor note (drafting dependency).** The Chapter 10 learner document (`GIS_Phase_1_Chapter_10_Attribute_Queries_and_Spatial_Relationships.md`) was completed by another author on the same day this chapter was drafted. The recap and the boundary policy below were checked against it: the policy restated here is Chapter 10's example policy of module 10.7.2 (boundary → alphabetically lower ward code, `assign_rule = 'BOUNDARY_TIEBREAK'`; outside → `assigned_ward = NULL`, `assign_rule = 'OUTSIDE'`), using its field names. If Chapter 10 is revised, re-align (Instructor Appendix I.7).

## Learning outcomes

By the end of this chapter you should be able to:

| ID | Outcome | Where it is taught | Where it is checked |
| --- | --- | --- | --- |
| LO1 | Write an analysis specification — decision, study area, time window, eligible records, units, output type, acceptance checks — and decide whether the answer needs a selection, a new geometry, transferred attributes, or a summary; keep an analysis worksheet and remove operations that have no consumer. | 11.1 | 11.10 concept Q1; scenario S1; lab step 1 |
| LO2 | Explain buffer distance, input geometry, planar versus geodesic method, and separate versus dissolved output; predict the effect of a changed threshold and of overlapping buffers; state what a buffer does *not* represent. | 11.2 | 11.10 concept Q2; scenario S2; lab |
| LO3 | Clip features to a study boundary; distinguish selecting a whole feature from keeping only its portion; decide when stored length/area attributes must be recalculated. | 11.3 | 11.10 concept Q3; practical |
| LO4 | Construct an intersection (overlay) and contrast it with the *intersects?* predicate; predict output dimension and record splitting; refuse to sum attributes copied onto split records without an allocation rule. | 11.4 | 11.10 concept Q4; practical |
| LO5 | Dissolve features by attribute with summary statistics; distinguish dissolve from append; expect multipart outputs and check that the grouping matches the business requirement. | 11.5 | 11.10 concept Q5 |
| LO6 | Count requests per ward with a spatial join under a stated boundary policy; preserve zero-count areas; reconcile source IDs with assigned, unassigned, and multiply-assigned outcomes and explain any sum larger than the number of requests. | 11.6 | 11.10 concept Q6; lab |
| LO7 | Compute a raster statistic with NoData excluded and show the effect of a NoData-as-zero policy; build a conceptual threshold mask with stated units; check alignment, cell size, extent, and missing data before combining rasters. | 11.7 | 11.10 scenario S2; oral |
| LO8 | Validate an analysis with invariants and spot-checks, run a one-change sensitivity test, and document tool, version, parameters, CRS, transformation, inputs, outputs, and limitations so that another person can rerun it. | 11.8, 11.9 | 11.9 lab; 11.10 practical and oral |

## Required materials

- This document, a text editor, and a spreadsheet or plain table for the analysis worksheet.
- The **Chapter 11 fixtures**, printed in full in the fixture section below and in 11.9.3 as CSV text with WKT geometry (planar) and as GeoJSON/CSV (Earth-referenced). The instructor builds the GeoPackage or file geodatabase from them by the routes used in Chapter 3 (Chapter 3, Instructor Appendix I.5) and Chapter 6 (6.8.3), and issues both the built package and the text. Every expected value in this chapter can be checked on graph paper from the text alone.
- **Primary route:** ArcGIS Pro 3.x. Buffer [S17], Clip [S32], Dissolve [S34], Spatial Join [S31], Near [X02], and Select Layer By Location [X01] are listed on their pages as available at Basic, Standard, and Advanced. **Intersect** [S33] is listed as *Limited* at Basic and Standard ("the number of input feature classes or layers is limited to two") and full at Advanced; the two-input examples in this chapter fit the limited form. The raster tools in 11.7 (Con [X06], Raster Calculator [X03]) **require the Spatial Analyst or Image Analyst extension** at every licence level; Calculate Statistics [X05] does not. No ArcGIS Online account or credits are needed. **Alternative route:** QGIS Desktop 3.44, core algorithms only; its raster calculator needs no extension.
- Graph paper (or any drawing tool) for the prediction sketches in 11.2–11.6.

## Recap of the preceding chapter

Chapter 10 separated four actions that look alike on screen: *filtering* a view, *selecting* records, *exporting* a subset, and *modifying* source values. It built attribute conditions (equality, ranges, AND/OR/NOT, text matching, `IS NULL` as distinct from equality to zero) and warned that SQL syntax depends on the data source. It joined records by identity and showed why a one-asset-to-many-inspections join makes row counts exceed asset counts — so you must decide *what unit you are counting* before summing. It introduced the spatial predicates with diagrams before naming any tool, insisted that a bounding-box overlap is only a candidate test, and made you predict the four cases — inside, on the boundary, in a hole, outside — before running anything. For PostGIS it named the documented difference between `ST_Contains` (interior required) and `ST_Covers` (boundary included). Finally it combined spatial and attribute logic into separately testable conditions, showed with Fixture F10-1 that the clerk's typed `ward_code` and the geometric answer disagree exactly on the boundary and outside cases (P5 and P6), and produced a **written shared-boundary policy** (10.7.2) for assigning a request to a ward — reported *beside* the raw match result, never dressed up as it.

Chapter 10's predicates *answer yes or no about existing records*. This chapter uses the same relationships to *build* outputs: the polygon of everything within 300 m of a road, the part of a road inside a ward, the pieces where two layers overlap, one polygon per zone, and a count per ward.

## The recurring scenario and the Chapter 11 fixtures

The fictional municipality continues: **assets**, **roads**, **wards**, **requests**, **inspections**. Two kinds of fixture appear, and — as in Chapter 6 — they are never mixed in one operation:

1. **The planar training grid** (Chapters 1, 3, 8, 9): flat, metre-based, x to the right, y upward, **no Earth location and no coordinate-system identifier**. Every distance and area on it is ordinary planar arithmetic, so every expected result is exact and hand-checkable. It is used in modules 11.2–11.6 and in Part 1 of the lab.
2. **Fixture E6** from Chapter 6, extended here as **Fixture E11**: Wards A and B defined in WGS 84 (EPSG:4326), requests Q1–Q5 delivered in WGS 84 / UTM zone 43N (EPSG:32643), plus one new road and two new request fields. It is used in Part 2 of the lab, where CRS, units, and method matter.

### Fixture F11-1 — Requests on the training grid, Chapter 11 variant (synthetic)

The blueprint's Appendix A.2 defines six requests, all eligible for the *basic* spatial exercise, and says that status and date filters "must be introduced explicitly in later fixture variants". This is that variant. The geometry is unchanged from Chapters 1–10; two fields are new.

| Request | (x, y) m | `status` | `reported_on` | Strict-interior ward | Distance to finite Road R1 (m) |
| --- | --- | --- | --- | --- | --- |
| P1 | (200, 200) | OPEN | 2026-08-15 | A | 300 |
| P2 | (800, 800) | CLOSED | 2026-07-30 | A | 300 |
| P3 | (1200, 250) | OPEN | 2026-09-01 | B | 250 |
| P4 | (1700, 900) | OPEN | 2026-08-22 | B | 400 |
| P5 | (1000, 500) | OPEN | 2026-09-05 | on the shared A/B boundary | 0 |
| P6 | (2200, 500) | OPEN | 2026-08-30 | outside both wards | 200 (to the road's east endpoint) |

Ward A is the square (0, 0)–(1000, 1000); Ward B is (1000, 0)–(2000, 1000); each has planar area exactly 1,000,000 m² = 1 km². Road R1 is the *finite* segment from (0, 500) to (2000, 500), length 2,000 m. P1 and P2 are both exactly 300 m from R1 (|200 − 500| = 300 and |800 − 500| = 300); P6 is 200 m beyond the east end of the road, so its distance is to the endpoint (2000, 500), not to an imaginary extension of the line.

**Restated Chapter 10 answers (blueprint Appendix A.2, hand-checked):**

- Boundary-inclusive road threshold of **300 m** on *all six*: P1, P2, P3, P5, P6 qualify; P4 (400 m) does not. Adding "must also lie in Ward A ∪ Ward B" removes P6.
- Strict-interior ward membership: A contains P1, P2; B contains P3, P4; P5 (boundary) and P6 (outside) belong to neither.
- Boundary-inclusive membership: A covers P1, P2, P5; B covers P3, P4, P5 — **six ward–request matches, five distinct matched requests, P6 unmatched**.

**Boundary policy carried forward from Chapter 10 (10.7.2; called "Policy W-1" in this chapter for brevity; synthetic).** *Raw* geometric membership is boundary-inclusive (a request on a ward edge matches every ward whose boundary it touches). For a *unique-assignment* report, each request receives one `assigned_ward` and an `assign_rule`: an interior request gets its containing ward (`INTERIOR`); a request touching more than one ward is assigned to the ward with the alphabetically lower code (A before B before C…) with `assign_rule = 'BOUNDARY_TIEBREAK'`; a request touching no ward keeps `assigned_ward = NULL` with `assign_rule = 'OUTSIDE'` and is reported in an explicit "unassigned" row, never dropped and never silently given the nearest ward. Under W-1, P5 is assigned to A by tie-break, and P6 is unassigned. The raw match counts and the policy counts are always reported side by side. (Chapter 10's Fixture F10-1 uses a richer `status` vocabulary; F11-1 below deliberately simplifies it to OPEN/CLOSED and adds `reported_on`.)

### Fixture F11-2 — Ward register with zones (synthetic; module 11.5 only)

| `ward` | Geometry (training grid, m) | Planar area (m²) | `zone` | `households` |
| --- | --- | --- | --- | --- |
| A | square (0, 0)–(1000, 1000) | 1,000,000 | Z1 | 1200 |
| B | square (1000, 0)–(2000, 1000) | 1,000,000 | Z2 | 900 |
| C | rectangle (0, 1000)–(1000, 1500) (Chapter 9's intended Ward C) | 500,000 | Z1 | 400 |
| D | rectangle (2500, 0)–(3000, 500) — a detached outlying ward, new in this chapter | 250,000 | Z2 | 150 |

`zone` is a maintenance zone code and `households` a synthetic count. Ward D does not touch any other ward (its west edge at x = 2500 is 500 m east of Ward B's east edge at x = 2000). That is deliberate: it produces a multipart zone in 11.5.

### Fixture E11 — Earth-referenced fixture (synthetic; extends Chapter 6's E6)

Wards A and B are exactly Chapter 6's Table F6-1 (0.010° × 0.010° rectangles in EPSG:4326, sharing the edge at longitude 75.000° E, which is the central meridian of UTM zone 43N). Requests Q1–Q5 keep Chapter 6's Table F6-2 coordinates (EPSG:32643, metres). Two things are new:

**Table F11-3 — Requests Q1–Q5 with status and date (EPSG:32643, metres; hand-checked distances).**

| Request | `category` | `E_m` | `N_m` | `status` | `reported_on` | Where it lies | Planar distance to Road RE-1 (m) |
| --- | --- | --- | --- | --- | --- | --- | --- |
| Q1 | Blocked drain | 500000.000 | 2543741.163 | OPEN | 2026-08-20 | on the shared A/B boundary | 158.837 |
| Q2 | Streetlight out | 500000.000 | 2544405.362 | CLOSED | 2026-07-03 | on the shared A/B boundary | 505.362 |
| Q3 | Pothole | 499487.611 | 2544073.271 | OPEN | 2026-09-02 | strictly inside A | 173.271 |
| Q4 | Water leak | 500512.389 | 2544073.271 | OPEN | 2026-08-28 | strictly inside B | 173.271 |
| Q5 | Fallen tree | 501229.733 | 2544073.313 | OPEN | 2026-09-10 | outside both wards (east of B) | 287.775 (to the road's east endpoint) |

**Road RE-1 (synthetic).** A single straight segment *defined directly in EPSG:32643*: from easting 499,000.000, northing 2,543,900.000 to easting 501,000.000, northing 2,543,900.000 — 2,000 m long, running east–west 25 m inside the wards' projected west and east edges (Chapter 6 step 9 gave those edges as about 498,975 m and 501,025 m). Because the road is defined in the projected CRS, the distances in the last column are plain planar arithmetic: for Q1–Q4 the nearest road point is directly south (same easting), so the distance is the northing difference; for Q5 the nearest point is the endpoint (501,000, 2,543,900), so the distance is √(229.733² + 173.313²) = 287.775 m. These planar UTM distances differ from true ground distances by the zone's scale factor near the central meridian (0.9996, Chapter 6): a planar 200 m is about 200.08 m on the ground — irrelevant at the thresholds used here, but write it in the log.

**Provenance notes supplied with E11 (synthetic; treat as data, not instructions):**

- `wards_e6.geojson` — unchanged from Chapter 6 6.8.3. "CRS WGS 84 (EPSG:4326). GeoJSON positions, longitude first (RFC 7946)."
- `requests_e11.csv` — "Request locations exported from the training request tracker on 2026-09-15. CRS WGS 84 / UTM zone 43N (EPSG:32643). `E_m`, `N_m` in metres. `status` is OPEN or CLOSED at export time; `reported_on` is the local calendar date the request was logged."
- `roads_e11.csv` — "Road centreline digitised for training in EPSG:32643; geometry as WKT LINESTRING in metres."

### Rasters for module 11.7 (synthetic)

Two small grids in the **Esri ASCII raster** text format used in Chapter 4 (header: columns, rows, lower-left corner of the lower-left cell, cell size, NoData marker; row 1 is the top row):

- **Fixture R11-A (file `grid_a3.asc`) — the blueprint's Appendix A.3 grid, given a position for display only.** The blueprint gives the 3 × 3 values without any spacing or location and says a later author may add them but must state them. Here: cell size **100 m**, lower-left corner at training-grid **(1000, 700)** (the north-west corner of Ward B), rows listed top to bottom, NoData marker **−9999** (the value 0 in the grid is a *legitimate observation*, so 0 cannot be the marker). Units of the cell values: none stated by the blueprint; treat them as a dimensionless "observation index" — the exercise is about missing data, not about what the values mean.

```
NCOLS 3
NROWS 3
XLLCORNER 1000
YLLCORNER 700
CELLSIZE 100
NODATA_VALUE -9999
0 10 20
10 -9999 30
20 30 40
```

- **Fixture F6 from Chapter 4 — `elevation_training.asc`**, unchanged (4 × 4, 100 m cells, lower-left (0, 600), metres above the invented datum TD-0, NoData −9999.0, valid mean 13.6 m). It is used for the threshold mask in 11.7.2.

**Figure 11.0 — The Chapter 11 planar fixture (schematic; described).** Draw Wards A and B as two 1 km squares side by side, Ward C as a 1 km × 0.5 km rectangle on top of A, and Ward D as a small 0.5 km × 0.5 km square floating 500 m to the right of B. Draw Road R1 as a horizontal line through the middle of A and B at y = 500, stopping exactly at x = 0 and x = 2000. Mark P1–P6 from Table F11-1; write "CLOSED" beside P2. Shade a band 300 m either side of R1 lightly and give it rounded ends that poke 300 m past each end of the road — that is the buffer of 11.2. Label the drawing "training grid, metres, no CRS".

---
## 11.1 Specify the analysis before choosing tools

### 11.1.1 Write the specification first

In every GIS product, the analysis tools sit in a searchable list, and the temptation is to start with the list: "there is a Buffer tool — let's buffer something." Chapter 1 taught the opposite habit for *questions*; this module applies it to *analysis*. Before any tool is opened, write down seven things. The list is short because each item, if missing, produces a specific kind of wrong answer.

| # | Item | What it fixes | Chapter 11 running example (synthetic) |
| --- | --- | --- | --- |
| 1 | **Decision** — what somebody will do with the answer | Stops you producing a map nobody can act on | The works manager wants to know *how many open requests per ward are close to Road R1*, to decide where to send the road crew next week |
| 2 | **Study area** — the geometry inside which records count | Stops "outside" records leaking in, and tells you what "unmatched" means | The union of Wards A and B; records outside both are reported separately, not silently dropped |
| 3 | **Time window** — which dates count | Stops old records inflating counts | Requests reported on or after **2026-08-01** (the manager's "recent") |
| 4 | **Eligible records** — attribute conditions | Turns "requests" into a testable set | `status = 'OPEN'` |
| 5 | **Units, CRS, and method** | Stops degrees being treated as metres and planar being confused with geodesic (Chapter 6) | Distances in **metres**; on the training grid planar arithmetic; in E11 planar in EPSG:32643 |
| 6 | **Desired output** — its *type* | Determines which operation you need (11.1.2) | A table: one row per ward (including wards with zero), with a count, plus a list of unassigned and boundary cases |
| 7 | **Acceptance checks** — how you will know the output is right | Turns "looks right" into a test | Every eligible request ID appears exactly once in assigned/unassigned/boundary lists; per-ward counts sum to the number of distinct assigned requests; P4 and P2 are absent, with reasons |

Notice what is *not* on the list: tool names. A specification that says "buffer the road by 300 m" has skipped ahead. The specification says "close to" and gives the threshold, **300 m, boundary-inclusive (≤ 300 m)**, and the method. Whether that becomes a Buffer, a Near calculation, or a within-a-distance selection is decided in 11.1.2.

**Threshold and method are decisions, not defaults.** The 300 m was chosen because the blueprint's fixture makes it hand-checkable; a real works manager might say 250 m or "walking distance", and the specification must record *who chose the number and why*. Likewise "boundary-inclusive" is a decision: P1 sits at exactly 300 m, so ≤ and < give different answers (11.2.3).

**Developer analogy — the acceptance test before the implementation.** The specification is a failing test written before the code: it names inputs, the expected output shape, and the assertions. The analogy holds well; where it stops is that GIS tests rarely have a single exact expected value for real data — you assert *invariants* (counts reconcile, extents are inside the study area, no unexpected geometry types) and *spot-checks* (this one request, by hand), which is what 11.8 develops.

### 11.1.2 Four kinds of answer, four kinds of operation

Ask of the desired output: *is it a selection, a new geometry, transferred attributes, or a summary?* The four answers lead to different operations, and choosing the wrong kind is the most common beginner error in this chapter — usually by building geometry when a selection would do.

| The question needs… | Meaning | Typical operation | Output |
| --- | --- | --- | --- |
| **A selection** | Some of the *existing* records, unchanged | Attribute query; Select By Location; Near + a condition (Chapter 10) | The same features, a subset; or their IDs |
| **A new geometry** | A shape that does not exist in any input | Buffer, clip, intersect, dissolve (11.2–11.5) | A new feature class; new records; often new IDs |
| **Transferred attributes** | Existing records *enriched* with values from another layer | Spatial join, one-to-one (11.6); attribute join by key (Chapter 10) | Same records as the target, extra columns |
| **A summary** | One number (or row) per group | Dissolve with statistics (11.5); spatial join with count/merge rules (11.6); Summarize Within [X10]; SQL `GROUP BY` | A table or polygon layer with one row per group |

Apply it to the running example: "How many open, recent requests per ward are within 300 m of R1?" The **final** output is a *summary* (a count per ward). To get there you need a *selection* (eligible requests within the threshold) and then *transferred attributes or a summary* (which ward each belongs to, counted). Nowhere does the question ask for a *new geometry*. A buffer polygon is one convenient way to test "within 300 m", and it makes a good map, but it is a means, not the deliverable; if a Near distance answers the same test, the buffer is optional. Say that in the worksheet.

**A second scenario, to show the same rule outside municipal work.** A retailer asks "which of our 40 stores have a competitor within 1 km?" That is a *selection* of stores (with, perhaps, a transferred attribute: the competitor's name and distance). A GIS beginner will buffer every competitor by 1 km and intersect; the experienced analyst will run a nearest-feature distance and filter ≤ 1000 m, because the output type is "a subset of stores", not "polygons".

### 11.1.3 The analysis worksheet — and removing an operation that has no consumer

The worksheet is a table with one row per intermediate output. Each row must say *why the output exists* — who consumes it. If no later row and no deliverable consumes an output, the operation that produced it is unnecessary, and unnecessary operations are where errors hide (every one has parameters, a CRS, and a tolerance that can be wrong).

**Worksheet, first draft (running example, training grid):**

| Step | Operation | Input(s) | Output | Consumed by |
| --- | --- | --- | --- | --- |
| 1 | Attribute filter: `status = 'OPEN' AND reported_on >= '2026-08-01'` | requests (F11-1) | `req_eligible` (selection: P1, P3, P4, P5, P6) | steps 4, 5 |
| 2 | Buffer R1 by 300 m, round ends, no dissolve needed (one input feature) | roads | `r1_buf300` (polygon) | step 3, step 4 |
| 3 | **Intersect `r1_buf300` with wards** to make a "study corridor" split by ward | `r1_buf300`, wards | `corridor_by_ward` (2 polygons) | — |
| 4 | Select `req_eligible` that intersect `r1_buf300` | `req_eligible`, `r1_buf300` | `req_near_road` (P1, P3, P5, P6) | step 5 |
| 5 | Spatial join wards ← `req_near_road`, count, keep all wards | wards, `req_near_road` | `ward_counts` | deliverable; step 6 |
| 6 | Reconcile: list each ID of `req_near_road` as assigned / boundary / unassigned | `req_near_road`, wards | `reconciliation` table | deliverable |

Step 3 has nothing in its "Consumed by" column. The author added it because "a corridor per ward" sounded useful, but the count in step 5 comes from *points and wards*, not from the corridor; and the corridor would carry split records with copied attributes (11.4.3) that someone might later sum by mistake. **Remove step 3.** The final worksheet has five rows. Deleting a row is a normal, expected act in this chapter — record it in the log ("step 3 removed: no consumer") so a reviewer does not wonder whether it was forgotten.

**Misconception — "more intermediate layers means more thorough analysis."** The consequence is that a reviewer must check every layer, and each unnecessary one can carry a wrong CRS, a stale selection, or a tolerance artefact into the result without anyone noticing, because nobody reads its attributes.

**Comprehension check 11.1.** The manager changes the question to: "Show me, on a map, the part of each ward that lies within 300 m of R1, and how much of each ward's area that is." Which of the four output kinds does *this* question need, and does step 3 now have a consumer?

---

## 11.2 Create proximity areas with buffers

### 11.2.1 What a buffer is: distance, input geometry, method, and dissolve

A **buffer** is the set of all points whose distance from an input feature is less than or equal to a chosen distance. The output is always a polygon, whatever the input: buffering a point gives a disc, buffering a line gives a band with (usually) rounded ends, buffering a polygon gives a larger polygon. In PostGIS the same idea is written as `ST_Buffer`, which "computes a POLYGON or MULTIPOLYGON that represents all points whose distance from a geometry/geography is less than or equal to a given distance" [P01]. Four parameters decide what you get, and every one of them must appear in the worksheet.

**1. Buffer distance and its units.** In ArcGIS Pro's Buffer tool the distance can be typed with a unit ("300 Meters") or taken from a field; "If linear units are not specified or are entered as Unknown, the linear unit of the input features' spatial reference will be used" [S17]. On the training grid the units are metres by declaration; on an EPSG:32643 layer they are metres by the CRS definition; on an EPSG:4326 layer the layer unit is the *degree*, which is the trap of Chapter 6 — see method, below.

**2. Input geometry.** The road is a *finite* line. Its buffer therefore has two ends. With **round** ends the band extends a semicircle of radius 300 m beyond each endpoint; with **flat** ends it stops at the endpoints. ArcGIS Pro exposes this as the **End Type** parameter (ROUND or FLAT; FLAT requires an Advanced licence) [S17]; QGIS's Buffer algorithm calls it **End cap style** (Round, Flat, Square) [Q01]; PostGIS's `ST_Buffer` takes `endcap=round|flat|square` [P01]. The choice changes the answer for a request near a road *end*: P6 is 200 m from R1's east endpoint, inside a round-ended 300 m buffer but outside a flat-ended one.

**3. Method — planar or geodesic.** In ArcGIS Pro the **Method** parameter has two values. With **Planar** (the default), "If the input features have a projected coordinate system, Euclidean buffers will be created. If the input features have a geographic coordinate system and you specify a Buffer Distance value in linear units (meters, feet, and so forth, as opposed to angular units such as degrees), geodesic buffers will be created"; with **Geodesic**, "All buffers will be created using a shape-preserving geodesic buffer method, regardless of the input coordinate system" [S17]. So in ArcGIS Pro a 300 m buffer on an EPSG:4326 layer is *not* the degrees-as-metres mistake — the tool notices the linear unit and goes geodesic. That is ArcGIS-specific behaviour, not a general GIS rule.

> **Platform note — units in other engines.** PostGIS `ST_Buffer` on a *geometry* uses the units of the geometry's spatial reference system, so a `geometry` in EPSG:4326 buffered by 300 gives a 300-*degree* polygon; the *geography* variant measures in metres [P01]. QGIS's core Buffer algorithm takes a plain number for **Distance** [Q01]; the QGIS 3.44 algorithm page does not state the unit, and the author's understanding is that it is the input layer's CRS unit — so an EPSG:4326 layer would be buffered in degrees. *Verification item:* confirm on the installed QGIS (buffer a copy of `wards_e6.geojson` by 0.001 and by 300 and look at the result) before telling learners what happens. The safe rule that holds everywhere: **project to a suitable projected CRS first (Chapter 6), then buffer in metres.** Part 2 of the lab does exactly that.

**4. Dissolve type — separate or merged output.** When several input features are buffered, their buffers overlap wherever the features are closer than twice the distance. ArcGIS Pro's **Dissolve Type** offers NONE ("An individual buffer for each feature will be maintained, regardless of overlap"), ALL (all buffers merged into one feature), and LIST ("Any buffers sharing attribute values in the listed fields (carried over from the input features) will be dissolved") [S17]. QGIS has a **Dissolve result** checkbox [Q01]. Which you want depends on the *output kind* from 11.1.2: to *count requests within 300 m of any road*, you want ALL (one polygon, no double counting); to *report which road each request is near*, you want NONE (one polygon per road, carrying the road's attributes).

**Output attributes.** With NONE, ArcGIS Pro's output "will include a `BUFF_DIST` field that contains the buffer distance used to buffer each feature in the linear unit of the input's coordinate system" and an `ORIG_FID` linking back to the source feature; those two fields are not written for ALL or LIST [S17]. Z and M values are not transferred [S17]. Read the output table after every buffer — the presence of `BUFF_DIST = 300` is your first check that the units were interpreted as intended.

**Developer analogy — a buffer is a materialised query.** `WHERE distance(request, road) <= 300` is a predicate; the buffer is that predicate turned into a stored shape you can draw, reuse, and intersect. The analogy holds: like any materialised view, it goes stale when the road changes, and it costs storage. Where it stops: the buffer is an *approximation* of the predicate — curved edges are stored as short straight segments (QGIS's **Segments** parameter, default 5 per quarter circle [Q01]; PostGIS `quad_segs`, default 8 [P01]), so a point exactly at the threshold may fall a few millimetres inside or outside the polygon. PostGIS's own documentation says not to use `ST_Buffer` for within-distance queries but `ST_DWithin` [P01]. For an exact ≤ 300 m test, compute the distance (Near [X02]) and compare numbers; use the buffer for the map.

### 11.2.2 Worked example — Road R1 buffered by 300 m, and what the polygon does not mean

**Question.** Build the 300 m proximity area of Road R1 on the training grid and state its area.

**Inputs and assumptions.** R1 from (0, 500) to (2000, 500), metres, planar; distance 300 m; round ends; single feature so dissolve is irrelevant.

**Reasoning.** The buffer is a rectangle 2,000 m long and 600 m wide (300 m each side of the line), plus a semicircle of radius 300 m at each end, which together make one full circle.

- Rectangle: 2000 × 600 = **1,200,000 m²**
- Circle: π × 300² = 282,743.34 m²
- **Total: 1,482,743.34 m²** (hand-checked; a GIS will give slightly less, because the round ends are stored as polygons with straight segments — expect agreement within about 0.5 % and record the actual value).

With **flat** ends the area is exactly 1,200,000 m². The extent of the round-ended buffer is x from −300 to 2300 and y from 200 to 800 — note that it pokes 300 m *outside* the wards at both ends, which matters in 11.3.

**What it does not mean.** The polygon is the set of locations within 300 m *in a straight line* of the road centreline. It says nothing about whether a crew can *drive* there, how long it takes, which side of a canal or railway a request is on, or where the road's kerb or edge is (the centreline is a line of zero width). Writing "requests within 300 m of R1" is honest; writing "requests accessible from R1" or "5-minute response zone" is not supported by anything in the buffer. Travel-time areas need a network and a routing solver, which are outside Phase 1 by the blueprint's boundary for this chapter.

### 11.2.3 Predict before running: a changed threshold and overlapping buffers

**Changed threshold.** Using the F11-1 distances, predict which requests fall inside a round-ended buffer of R1 at three thresholds (ignore status for the moment):

| Threshold (boundary-inclusive) | Inside | Outside | Note |
| --- | --- | --- | --- |
| 250 m | P3 (250), P5 (0), P6 (200) | P1, P2 (300), P4 (400) | P3 sits exactly on the edge |
| 300 m | P1, P2, P3, P5, P6 | P4 | P1 and P2 sit exactly on the edge — the blueprint's set |
| 400 m | all six | — | P4 sits exactly on the edge |

Three of the four thresholds have a request *exactly* on the buffer edge. That is not an accident of this fixture; real data with rounded coordinates produces such ties constantly. A point exactly on a polygon edge is *intersecting* the polygon in the boundary-inclusive sense (Chapter 10), and ArcGIS Pro's Select Layer By Location **Intersect** relationship selects features that "intersect a selecting feature" [X01] — but whether the software *sees* the point as on the edge depends on how the buffer's straight edge and the point's coordinates round in floating point and on the dataset's XY tolerance. On this fixture the edge at y = 200 is a straight line through integer coordinates, so P1 should be found; a point on a *curved* part of the boundary might not be. The robust check is the number: Near gives `NEAR_DIST = 300` for P1 exactly, and `300 <= 300` is true. **Rule: for exact-threshold decisions, compare distances; use the buffer polygon to draw.**

**Overlapping buffers.** Suppose a second road R2 (Chapter 3's: (500, 0)–(500, 500)–(800, 900)) is buffered by 300 m together with R1, Dissolve Type NONE. R2 meets R1 at (500, 500), so the two buffers overlap in a large area around that junction. P1 (200, 200) is 300 m from R1 *and* 300 m from R2's vertical part (|200 − 500| = 300), so it lies on the edge of both buffers. Predict the consequences:

- Selecting requests that intersect the NONE output and *counting the selected requests* gives each request once — a selection is a set.
- Running a spatial join **from buffers to requests** with one-to-many gives P1 *twice* (once per buffer). Summing a count column then double-counts.
- Dissolving the buffers (ALL) before the join removes the duplication but also removes the "which road" information.

Predict which you need from the output kind in 11.1.2 *before* running. This is the same lesson as Chapter 10's join row-count inflation, arriving through geometry instead of keys.

**Misconception — "the default Buffer settings are fine; the tool knows the units."** The tool knows the *layer's* units, which on an unprojected layer are degrees, and it knows nothing about whether you wanted planar or geodesic. Chapter 6's decision table applies unchanged; the only new fact is that ArcGIS Pro's Planar method switches to geodesic for linear units on geographic data [S17] — behaviour you must not assume in other software.

**Comprehension check 11.2.** A colleague buffers the E11 road RE-1 by 200 m in ArcGIS Pro with Method = Planar and, separately, by 0.002 (no unit) after reprojecting the road to EPSG:4326. Which of the two buffers is in metres, which is in degrees, and what does the ArcGIS Pro documentation say would have happened if they had typed "200 Meters" on the EPSG:4326 copy?

---
## 11.3 Extract a study area with clip

### 11.3.1 What clip does: geometry cut, attributes kept

**Clip** takes an *input* layer and a *clip* layer and keeps only the parts of the input features that lie inside the clip features. ArcGIS Pro's Clip tool "Extracts input features that overlay the clip features" [S32]; QGIS's Clip algorithm says "Only the parts of the features in the input layer that fall within the polygons of the overlay layer will be added to the resulting layer" [Q02]. Two rules follow, and both are easy to forget:

1. **Geometry is cut; attributes are copied unchanged.** ArcGIS Pro: the output "will contain all the attributes of the Input Features … when the output is a feature class" [S32]. QGIS is more explicit about the consequence: "This operation modifies only the features geometry. The attribute values of the features are not modified, although properties such as area or length of the features will be modified by the overlay operation" [Q02]. A road that was 2,000 m long with a field `length_m = 2000` becomes a 1,000 m line *still carrying* `length_m = 2000` (11.3.3).
2. **What the clip layer may be depends on the input.** In ArcGIS Pro, polygon inputs need polygon clip features; line inputs may be clipped by lines or polygons; point inputs by points, lines, or polygons [S32]. In practice the clip layer is almost always the study-area polygon, and that is the only case this chapter uses.

Clip is the operation for **"give me only what is inside the study area"** when the study area is not itself interesting. Its output has the *same geometry type* as the input (lines in, lines out) and carries *only the input's attributes*; nothing from the clip layer is transferred. If you need to know *which* study-area polygon each piece fell in, you need intersect (11.4), not clip.

**Developer analogy — clip is a cookie cutter, not a filter.** A filter (`WHERE`) keeps or drops whole rows. Clip can keep *part of a row*: it produces a shape the input never contained. The analogy of a cookie cutter pressed onto rolled dough is exact for geometry — you get the dough inside the cutter, and the dough keeps its ingredients (attributes) — and it stops where the ingredients list says "weight: 500 g": the label was true of the whole sheet, not of the cookie.

### 11.3.2 Worked example — Road R1 crossing the Ward A boundary: select, or clip?

**Question.** The road crew is responsible only for Ward A. Two people ask two different questions about R1, which runs from x = 0 to x = 2000 — through all of Ward A and all of Ward B:

- Person 1: "Which roads does Ward A contain, even partly?" → a **selection** (11.1.2). Select By Location with the Intersect relationship [X01] returns the *whole* R1, all 2,000 m of it, because part of it intersects Ward A.
- Person 2: "How much road is *in* Ward A?" → **new geometry**. Clip R1 by Ward A.

**Inputs and assumptions.** Training grid, metres, planar; R1 from (0, 500) to (2000, 500); Ward A the square (0, 0)–(1000, 1000), boundary-inclusive.

**Reasoning and result.** The part of R1 with 0 ≤ x ≤ 1000 lies inside Ward A (the endpoint (1000, 500) is on the shared boundary; boundary-inclusive, so it is kept as the clipped line's endpoint). The clipped output is one line from (0, 500) to (1000, 500), **length 1,000 m** (hand-checked). Its attributes are R1's, unchanged. Ward B's part, (1000, 500)–(2000, 500), is discarded — it is not in the output at all.

**Two different answers to "how long is R1 in Ward A?"** Person 1's selection says 2,000 m (the whole feature was selected); Person 2's clip says 1,000 m. Both are correct answers to their own questions. The worksheet must say which question is being answered; "roads in Ward A" is ambiguous until it does.

**Clipping the buffer, too.** Clip the 11.2.2 buffer polygon of R1 by Ward A. The buffer's part inside A is the rectangle 0 ≤ x ≤ 1000, 200 ≤ y ≤ 800 — the west semicircular end lies entirely at x < 0, outside A, and the east half of the band is in B. Area **600,000 m²** exactly (1000 × 600; hand-checked). This is a *polygon* clipped by a *polygon*, the case the ArcGIS Pro page requires for polygon inputs [S32].

**How to check.** Read the output's geometry: one feature, still a line, endpoints (0, 500) and (1000, 500). Compute its length from the coordinates (1000 m). Confirm the extent of the clipped layer lies inside the clip polygon's extent — an output extent larger than the clip layer's is an immediate sign that something else was run. Confirm the output attribute table has R1's fields and no ward field. **What it does not establish:** that the crew can reach every metre of that kilometre; that the ward boundary is legally where the fixture says; that R1's other attributes (surface type, width) apply uniformly to the kept part.

### 11.3.3 Stored length and area after a clip: recalculate, or explain

Suppose the roads layer has a field `length_m` filled in before the clip (Chapter 3 introduced such stored-measurement fields; Chapter 8 warned that they are derived values that can go stale). After the clip, the output row for R1 says `length_m = 2000` on a line that is 1,000 m long. Neither ArcGIS Pro's Clip nor QGIS's Clip touches user fields [S32] [Q02]. Two exceptions and one rule:

- **Geodatabase geometry fields.** A feature class in an ArcGIS geodatabase maintains `Shape_Length` / `Shape_Area` automatically; those *do* describe the new shape. A user field named `length_m` does not. In a shapefile or CSV export there is no automatic field at all (Chapter 7).
- **Ratio policy.** ArcGIS Pro's Clip (and Intersect) can, if a *layer* field's **Use Ratio Policy** option is enabled, calculate "a ratio of the input attribute value" for the output based on the fraction of the geometry kept [S32] [S33]. This is a deliberate allocation rule (11.4.3), not a default, and it assumes the attribute is spread evenly along the feature — true of length, sometimes true of population, false of "number of streetlights" if they cluster.
- **The rule.** After any operation that changes geometry, either **recalculate** stored measurements from the new geometry (ArcGIS Pro: Calculate Geometry Attributes, which "modifies the input data" — run it on a copy [X11]; QGIS: Add geometry attributes [Q01], as in Chapter 6's lab) or **rename/annotate** the copied field (`length_m_orig`) so nobody reads it as a description of the new shape. Never leave a stale measurement under its original name in a delivered output.

**Misconception — "clip keeps the features that are inside."** It keeps the *parts* that are inside. A feature entirely outside the clip layer disappears; a feature partly inside is cut; a feature entirely inside is kept whole. A learner who expects the first behaviour will be surprised that a 2,000 m road became a 1,000 m road and may report the wrong length.

**Comprehension check 11.3.** Road R2 from Chapter 3 runs (500, 0) → (500, 500) → (800, 900), entirely inside Ward A. Clip R2 by Ward A, and separately clip R2 by Ward B. What is in each output? Does either output's `length_m` field need recalculating?

---

## 11.4 Construct overlap with intersect

### 11.4.1 The predicate asks; the overlay builds

Chapter 10 asked "does R1 *intersect* Ward A?" and the answer was *yes*. This module asks "what *is* the intersection of R1 and Ward A?" and the answer is *a line from (0, 500) to (1000, 500)*. Same word, different kind of answer: the predicate returns a Boolean about existing features; the **overlay** returns new geometry (and new records) made from the common part of the inputs.

ArcGIS Pro's Intersect tool "Computes a geometric intersection of the input features. Features or portions of features that overlap in all layers or feature classes will be written to the output feature class" [S33]. QGIS's Intersection algorithm "Extracts the portions of features from the input layer that overlap features in the overlay layer. Features in the intersection layer are assigned the attributes of the overlapping features from both the input and overlay layers" [Q02]. PostGIS's `ST_Intersection` "Returns a geometry representing the point-set intersection of two geometries" [P03].

The difference from clip is in the last clause of the QGIS sentence: **attributes from both inputs are carried**. ArcGIS Pro: "Attribute values from the input feature classes will be copied to the output feature class" [S33], with an **Attributes To Join** parameter offering All attributes (default), All attributes except feature IDs, or Only feature IDs [S33]. So the intersection of roads and wards tells you, for each output piece, *which road* and *which ward* — which clip could not.

| | Clip | Intersect |
| --- | --- | --- |
| Geometry kept | Input parts inside the clip features | Parts common to all inputs |
| Attributes in output | Input's only | All inputs' (configurable) |
| Output geometry type | Same as input | By default, the *lowest dimension* among the inputs [S33] |
| Records | One per input feature that has an inside part | One per *pair* of overlapping pieces — splitting happens |
| Use when | The study area is just a boundary | You need to know *which* overlay feature each piece belongs to |

**Developer analogy — a spatial inner join that also computes a value.** `SELECT r.*, w.*, ST_Intersection(r.geom, w.geom) FROM roads r JOIN wards w ON ST_Intersects(r.geom, w.geom)` is, literally, what PostGIS users write for an overlay: the `JOIN … ON ST_Intersects` is the predicate; the `ST_Intersection` in the select list is the construction. The analogy is close to exact. Where it stops: a SQL join never *changes* a column's meaning, but the overlay changes the geometry column's meaning from "the whole road" to "this piece of it", and every derived attribute (length, area, anything stored per feature) silently inherits the old meaning (11.4.3).

### 11.4.2 Predicting output dimension and splitting

**Dimension.** Points have dimension 0, lines 1, polygons 2. ArcGIS Pro: "If the inputs have different geometry types, the output geometry type will default to the lowest dimension of the inputs" [S33], and the **Output Type** parameter can force it lower: INPUT (same as the lowest-dimension input), LINE ("only valid if none of the inputs are points"), or POINT ("If the inputs are line or polygon, the output will be a multipoint feature class") [S33]. PostGIS warns that the result "may return geometries of lower dimensionality than expected" — a line that only touches a polygon's boundary yields a point [P03]. Predict the dimension before you run; then check the output's geometry type as the first validation.

**Worked example A — line / polygon: R1 with the wards.**

*Question.* Intersect roads (R1 only) with wards (A and B, F11-2 without C and D for simplicity).
*Prediction.* Lowest dimension: line (1) versus polygon (2) → **line** output. R1 passes through both wards, so it is **split into two records**:

| Output record | Geometry | `road` | `ward` | Length (m) |
| --- | --- | --- | --- | --- |
| 1 | line (0, 500)–(1000, 500) | R1 | A | 1000 |
| 2 | line (1000, 500)–(2000, 500) | R1 | B | 1000 |

Two records where there was one. Their lengths sum to 2,000 m = R1's length, *because R1 lies entirely within the union of the wards*. If R1 extended beyond the wards, the parts outside would be absent and the sum would be less than the original — that is the "portions … that overlap in all layers" clause at work.

**Worked example B — polygon / polygon: the R1 buffer with the wards.**

*Question.* Intersect the round-ended 300 m buffer of R1 (11.2.2) with the wards.
*Prediction.* Both polygons → **polygon** output, split by ward:

| Output record | Geometry | `ward` | `BUFF_DIST` (copied from the buffer) | Area (m²) |
| --- | --- | --- | --- | --- |
| 1 | rectangle 0–1000 × 200–800 | A | 300 | 600,000 |
| 2 | rectangle 1000–2000 × 200–800 | B | 300 | 600,000 |

Total 1,200,000 m² — *less* than the buffer's 1,482,743 m², because the two semicircular ends (x < 0 and x > 2000) overlap no ward and are dropped. (Hand-checked; a GIS computing the buffer with segmented arcs will report the buffer area slightly smaller but the intersect areas exactly 600,000, since the rectangles have straight edges.)

**Worked example C — where the answer is a point.** Intersect Ward A and Ward B *with each other* (polygon/polygon) and ask for the default output type. They share only an edge — no area in common. What should the output be? The point-set intersection is the shared edge, a line, but the *default* output type for two polygons is polygon, and there is no polygon in common; expect an **empty** polygon output, and use Output Type = LINE to obtain the shared edge (1,000 m from (1000, 0) to (1000, 1000)) [S33]. *Verification item:* confirm in the installed version that the default polygon output is empty and that LINE returns the shared edge (Instructor Appendix I.6). Chapter 10's "touches" predicate said *yes*; the overlay shows *what* touches.

> **Platform note — how many inputs.** ArcGIS Pro's Intersect accepts a *list* of inputs and, with Basic and Standard licences, "the number of input feature classes or layers is limited to two" [S33]; the page also points to **Pairwise Intersect**, which computes intersections "on pairs of features rather than all combinations of features" [S33]. QGIS's Intersection takes exactly one input and one overlay layer [Q02]. Everything in this chapter uses two inputs.

### 11.4.3 The copied-attribute trap and allocation rules

Look again at worked example B. Each output record carries `BUFF_DIST = 300`, copied from the buffer, which is fine — the distance is still 300 for each piece. Now imagine the buffer also had a field `buf_area_m2 = 1482743` written before the intersect. Both output records would carry `buf_area_m2 = 1482743`. Sum that column and you get 2,965,486 m² for a total of 1,200,000 m² of actual geometry. **Nothing in the tool prevents this**; the tool did exactly what its page says — copied attribute values [S33].

The same trap in the line example: a `length_m = 2000` on R1 becomes `length_m = 2000` on *both* halves. A dashboard that sums `length_m` per ward reports 2,000 m of road in each ward — double the truth.

**A defensible allocation rule** must be chosen *before* summing anything copied onto split records. The options, from safest to most assumption-laden:

1. **Recompute from geometry.** Length and area are properties of the output shape; recalculate them. This is always correct for geometric measures and needs no assumption.
2. **Area- or length-weighted allocation** (ratio policy). For a quantity assumed to be spread evenly over the original feature (a road's maintenance budget per metre; a ward's population if — and only if — you accept uniform density), allocate in proportion to the kept fraction. ArcGIS Pro's Use Ratio Policy on layer fields does this [S33]; the assumption of uniformity must be written in the worksheet.
3. **Do not sum.** Categorical or identity attributes (road name, ward code, `BUFF_DIST`) are *labels*, not quantities; they are correctly copied and must never be added.
4. **Count distinct source features.** If the question was "how many roads touch Ward A", count distinct `road` values in the output, not records.

A rule that is *not* defensible: inventing a new total by summing copied values and reporting it. The blueprint calls this out as a required warning; treat any summed column on an intersect output as suspect until the worksheet names the rule that justifies it.

**Misconception — "intersect and clip are the same thing when the clip layer is one polygon."** Geometrically the kept shape is the same. The outputs differ in attributes (intersect adds the polygon's fields) and, when the clip/overlay layer has several polygons, in *record structure* (intersect splits by polygon; clip merges the inside parts of one input feature into one record when the software keeps them as one multipart — *verification item*: check in the installed version whether Clip with a multi-polygon clip layer returns one multipart record or several for a road that crosses two clip polygons).

**Comprehension check 11.4.** Ward C (0, 1000)–(1000, 1500) is added to the wards layer, and a new road R6 runs from (500, 800) straight north to (500, 1300). Predict the intersect output of {R6} with {A, B, C}: how many records, which ward code on each, what length each, and what `length_m` each carries if R6 had `length_m = 500` before the overlay.

---

## 11.5 Aggregate geometry with dissolve

### 11.5.1 Grouping by attribute and merging geometry

**Dissolve** groups input features that share the same values in chosen fields and merges each group's geometry into one feature. ArcGIS Pro's Dissolve "Aggregates features based on specified attributes" [S34]; QGIS's Dissolve "Takes a vector layer and combines its features into new features. One or more attributes can be specified to dissolve features belonging to the same class" [Q01]; in PostGIS the aggregate `ST_Union` "returns a geometry that is the union of a rowset of geometries" and is used with `GROUP BY` exactly like `SUM()` [P02]. Dissolve is `GROUP BY` for shapes.

Three things happen, and each needs a decision:

1. **The grouping fields** decide how many output features there are — one per distinct combination of values. With no fields, ArcGIS Pro "will dissolve all features together" [S34] into one feature.
2. **The geometry** of each group is the union of its members. Shared edges between adjacent members disappear (that is why it is called *dissolve*); members that do not touch stay as separate parts of one **multipart** feature. ArcGIS Pro's **Create multipart features** is checked by default ("Multipart features will be allowed in the output feature class"); unchecked, "Individual features will be created for each part" [S34]. QGIS: "All output geometries will be converted to multi geometries", with a **Keep disjoint features separate** option to split them [Q01].
3. **The other attributes** are *not* carried automatically. ArcGIS Pro drops fields that are neither dissolve fields nor listed in **Statistics Fields**; the statistic output is named `<statistic>_<field>` (for example `SUM_households`), and "Null values are excluded from all statistical calculations" [S34]. QGIS keeps the input fields but fills them with "the ones of the first input feature that happens to be processed" [Q01] — which is *arbitrary* for anything except the dissolve fields themselves. Never read a QGIS dissolve output's non-dissolve fields as meaningful.

**Dissolve is not append.** Appending (or merging) two datasets stacks their records into one table: two wards in, two records out, geometry untouched. Dissolving two wards that share a zone gives *one* record and *one* merged shape. Appending changes nothing about the features; dissolving changes both count and geometry. If a colleague says "I merged the wards", ask which they mean.

**Developer analogy — `GROUP BY` with an aggregate over a geometry column.** `SELECT zone, ST_Union(geom), SUM(households), COUNT(*) FROM wards GROUP BY zone` is a faithful description [P02]. The analogy stops at the geometry: `SUM` of numbers is one number, but the union of shapes can be a single polygon *or* a multipolygon, and a business rule ("a zone is one contiguous area") is not enforced by the operation.

### 11.5.2 Worked example — wards to zones, with summary statistics and a multipart outcome

**Question.** The works department manages by *zone*, not ward. Produce one feature per zone with the number of wards, the total households, and the area.

**Inputs and assumptions.** Fixture F11-2 (A, B, C, D on the training grid, metres, planar). Dissolve field: `zone`. Statistics: `households` SUM; `ward` COUNT; area recomputed from output geometry (not summed from a stored field — 11.4.3). Multipart allowed.

**Prediction.**

| `zone` | Members | Geometry | `COUNT_ward` | `SUM_households` | Area (m²) |
| --- | --- | --- | --- | --- | --- |
| Z1 | A, C | A (0–1000 × 0–1000) and C (0–1000 × 1000–1500) share the edge y = 1000, so the union is **one single-part rectangle** 0–1000 × 0–1500 | 2 | 1600 | 1,500,000 |
| Z2 | B, D | B (1000–2000 × 0–1000) and D (2500–3000 × 0–500) do not touch, so the union is **one multipart feature with two parts** | 2 | 1050 | 1,250,000 |

Hand-checked: 1200 + 400 = 1600; 900 + 150 = 1050; 1,000,000 + 500,000 = 1,500,000; 1,000,000 + 250,000 = 1,250,000.

**Inspect the multipart outcome.** Z2 is one record with two parts. If the department's rule is "a zone is a contiguous area", Z2 *violates* it — but the dissolve did not fail; it did what it was asked. Whether Z2 should be split (uncheck Create multipart features → two Z2 records, which breaks the "one row per zone" requirement) or reported as "one zone, two parts" is a *business* decision to write in the worksheet. Do not infer that every output group must be one contiguous area; do not infer the opposite either. Look.

**Check the grouping criterion against the requirement.** Dissolving by `zone` answers "per zone". If the requirement had been "per maintenance *crew*" and crews map to zones one-to-one, the same output serves; if two crews share Z1, dissolving by `zone` has thrown away the distinction and the worksheet is wrong at step 1, not at the tool. Read the requirement's *noun* and make it the dissolve field.

**A statistic that misleads.** MEAN of `households` per zone (Z1: 800; Z2: 525) is a mean *per ward*, not a household density; it depends on how the wards were drawn. Chapter 12 returns to totals, rates, and density; here, just do not report a MEAN without saying what the unit of averaging is.

**Misconception — "dissolve keeps all the attributes."** In ArcGIS Pro it keeps only the dissolve fields and the requested statistics [S34]; in QGIS it keeps the columns but with first-feature values [Q01]. A learner who dissolves wards by zone and then reads `households` from the QGIS output has read Ward A's value (or Ward C's — whichever was processed first) as the zone's value.

**Comprehension check 11.5.** You dissolve F11-2 with *no* dissolve field, statistics `households` SUM and `ward` COUNT. How many output features are there, how many parts does the geometry have, and what are the two statistics?

---
## 11.6 Transfer and summarize with spatial joins

### 11.6.1 Target, join, and the boundary policy

A **spatial join** attaches attributes from one layer to another based on a spatial relationship instead of a key. ArcGIS Pro's Spatial Join "Joins attributes from one or more inputs to another input based on the spatial relationship" [S31]. Its vocabulary, which this chapter adopts because the blueprint's source uses it:

- **Target features** — the layer whose records appear in the output (one output row per target, or per target–match pair).
- **Join features** — the layer whose attributes are transferred onto the targets.
- **Join operation** — **Join one to one**: "If multiple join features are found that have the same spatial relationship with a single target feature, the attributes from the multiple join features will be aggregated using a field map merge rule"; **Join one to many**: one output record per target–join pair [S31].
- **Keep all target features** — checked: "All target features will be maintained in the output (outer join)"; unchecked: "Only those target features that have the specified spatial relationship with the join features will be maintained in the output feature class (inner join)" [S31].
- **Match option** — the predicate. The list includes Intersect, Within a distance, Contains, Completely contains, Contains Clementini, Within, Completely within, Within Clementini, Are identical to, Boundary touches, Share a line segment with, Crossed by the outline of, Have their center in, Closest, Closest geodesic, Largest overlap [S31].
- **`Join_Count`** — an output field: "The number of join features that match each target feature" [S31]. With one-to-many, a `JOIN_FID` field is added and −1 indicates no match [S31].

QGIS splits the same idea into two algorithms: **Join attributes by location** (predicates intersect, contain, equal, touch, overlap, are within, cross; join types one-to-many, first match, or largest overlap; option "Discard records which could not be joined") and **Join attributes by location (summary)**, which adds a `JOINED_COUNT` field and statistics such as count, sum, mean, min, max [Q03]. A dedicated **Count points in polygon** algorithm writes a `NUMPOINTS` field [Q04]. In PostGIS it is `LEFT JOIN … ON ST_Covers(ward.geom, request.geom)` with `GROUP BY ward` — the `LEFT` keeping zero-count wards (Chapter 10).

**Counting requests per ward.** The target is **wards** (we want one row per ward, including zero); the join features are **requests**; the operation is **one to one** so that `Join_Count` becomes the count; **Keep all target features** must be checked so that a ward with no requests still appears. The match option encodes the **boundary policy** from Chapter 10 (Policy W-1, restated in the fixture section):

| Requirement | ArcGIS Pro match option (target = wards, join = requests) | What P5 (on the shared edge) does |
| --- | --- | --- |
| Raw boundary-inclusive membership | **Contains** — "matched if a target feature contains them" [S31] | Counted in A *and* in B |
| Strict interior | **Completely contains** — "matched if a target feature completely contains them" [S31]; documented for Select By Location as excluding any feature that intersects the boundary [X12] | Counted in neither |
| Unique assignment under W-1 | No single match option; compute raw boundary-inclusive matches, then apply the tie-break in a table step (11.6.3) | Counted once, in A, flagged |

> **Platform note — the documented boundary behaviour.** Esri's *Select By Location graphic examples* page states it directly: for **Contains**, "The selecting features can be inside as well as on the boundary of the input feature layer"; **Completely contains** selects "as long as the feature in the selecting features layer does not intersect the boundary of the input feature layer"; **Contains Clementini** is identical to Contains except when the feature is "entirely on the boundary … with no part of the contained feature properly inside", in which case it is not selected [X12]. So for a *point* on a ward edge: Contains → matched; Completely contains → not matched; Contains Clementini → not matched (a point on the boundary is entirely on it). Spatial Join uses the same relationship names [S31]. *Verification item:* the graphic-examples page documents Select By Location; the instructor must confirm with P5 that Spatial Join's match options behave the same way in the installed version (Instructor Appendix I.6) and record which option reproduced which count. In QGIS, the "contain" predicate is documented as "Returns 1 (true) if and only if no points of b lie in the exterior of a" [Q03] — the boundary is not the exterior, so a boundary point should be contained; "are within" is the same test from the other side. Verify likewise.

### 11.6.2 Worked example — counts per ward with unmatched records, zero-count areas, and multiple matches

**Question.** Count *all six* F11-1 requests per ward (ignore status for this example), for wards A, B, C (F11-2, without D).

**Inputs and assumptions.** Training grid, metres; boundary-inclusive membership (Contains); target = wards, join = requests, one to one, keep all targets.

**Prediction (hand-checked from the fixture table):**

| Ward | `Join_Count` | Matched request IDs |
| --- | --- | --- |
| A | 3 | P1, P2, P5 |
| B | 3 | P3, P4, P5 |
| C | 0 | — (zero-count area, kept because Keep all target features is checked) |
| **Sum** | **6** | but only **5 distinct** requests are matched |

Three things to inspect, in order:

1. **Unmatched records.** P6 (2200, 500) is in no ward. It appears in no ward's count. It is *not visible in this output at all* — a wards-as-target join cannot show you a request that matched nothing. To see it you need the reverse join (11.6.3) or a select-by-location with the selection inverted. Every analysis must produce the list of unmatched source records explicitly; the blueprint requires it.
2. **Zero-count areas.** Ward C has `Join_Count = 0`. If Keep all target features had been unchecked, C would be missing and a report reader would not know whether C has no requests or was never examined. Zero is information; keep it. *Verification item:* confirm that unmatched targets receive `Join_Count = 0` (not null) in the installed version — the page states the field's meaning but not the unmatched value [S31].
3. **Multiple matches.** The counts sum to 6 for 5 distinct requests because P5 was counted in both A and B. A sum larger than the number of requests is *always* explained by multiply-matched records (or by duplicate source records — Chapter 9); find the records and name them. Under Policy W-1 the *report* counts are A = 3 (P1, P2, P5†), B = 2 (P3, P4), C = 0, unassigned = 1 (P6), with † marking the BOUNDARY_TIEBREAK assignment — sum 5 + 1 unassigned = 6 = the number of source requests.

**Attribute transfer with merge rules.** If instead of a count you want *which* requests, one to one with the default field map gives you *one* request's attributes per ward (the merge rule decides which — *verification item*: the page lists merge rules Sum, Mean, Median, Mode, Minimum, Maximum, Standard Deviation, Count, Concatenate, First, Last [S31] but does not state the default; check it) — a silent loss of information. Use **Join one to many** to get one row per ward–request pair (A–P1, A–P2, A–P5, B–P3, B–P4, B–P5: six rows), or the **Concatenate** merge rule on `request_id` to get "P1,P2,P5" in one row. Either way, write in the worksheet *what unit each output row is* — a ward, or a ward–request pair (the rule of Chapter 10, module 10.3).

### 11.6.3 Reconciling source IDs: assigned, unassigned, multiply assigned

The check that catches most mistakes in this chapter costs one small table. Run the join *the other way* — target = requests, join = wards, one to many, keep all targets, match option **Within** (the mirror of Contains: "matched if a target feature is within them" [S31]) — and tabulate:

| Request | Ward rows returned | Class | Policy W-1 result |
| --- | --- | --- | --- |
| P1 | A | assigned | A |
| P2 | A | assigned | A |
| P3 | B | assigned | B |
| P4 | B | assigned | B |
| P5 | A, B | **multiply assigned** | A, `assign_rule = 'BOUNDARY_TIEBREAK'` |
| P6 | (none; `JOIN_FID = −1` [S31]) | **unassigned** | `assigned_ward = NULL`, `assign_rule = 'OUTSIDE'` |

Reconciliation: 4 assigned + 1 multiply assigned + 1 unassigned = **6 = number of source requests**; rows in the one-to-many output = 4 + 2 + 1 = 7. If those two identities do not hold, something was dropped or duplicated, and you must find it before reporting anything. The per-ward sum of 6 (11.6.2) is now *explained*: 5 distinct + 1 double-count (P5).

**Developer analogy — the reconciliation is the accounting identity of a join.** In a SQL left join, `count(*) = matched pairs + unmatched left rows`; if you cannot make the numbers balance you have mis-stated the join. The analogy is exact. Where it stops: SQL keys either match or not; spatial matches also have *tolerance* and *boundary semantics*, so the same P5 can be "matched" under one predicate and not under another. Name the predicate in the reconciliation table's heading.

**Misconception — "the per-ward counts are the report."** They are one view of it. Without the reconciliation (unmatched list, multiply-assigned list, and the policy applied), a reader cannot tell whether 3 + 3 = 6 means six requests or five. The blueprint requires that a sum larger than the number of requests be explained; make the explanation part of the deliverable, not a footnote.

**Comprehension check 11.6.** Using F11-1 and wards A, B, C with the *strict-interior* option (Completely contains), give `Join_Count` for A, B, C, the number of unassigned requests, and the reconciliation identity. Which requests changed class compared with 11.6.3, and why?

---

## 11.7 Introduce a minimal raster-analysis example

### 11.7.1 A statistic with NoData excluded — and the cost of replacing it with zero

Chapter 4 taught that **NoData** means *no observation* and that zero is a *value*. Esri's NoData page states it the same way: "NoData means that not enough information is known about a cell location to assign it a value" and "NoData and 0 are not the same—0 is a valid numerical value" [X04]; a tool can either "Always return NoData for that specified cell location" or "Ignore the NoData and compute with the available values", and "The behavior of NoData is addressed for each tool in its respective tool reference documentation" [X04]. That last sentence is the whole lesson: **the policy is chosen per tool, and you must read which one applies.**

**Worked example — the mean of Fixture R11-A.**

*Question.* What is the mean cell value of the 3 × 3 grid?
*Inputs.* R11-A as printed in the fixture section: values 0, 10, 20 / 10, NoData, 30 / 20, 30, 40; NoData marker −9999; 100 m cells at (1000, 700); values dimensionless.

*Policy 1 — exclude NoData (the correct policy for "mean of what was observed").* Eight valid cells; sum 0 + 10 + 20 + 10 + 30 + 20 + 30 + 40 = **160**; mean 160 ÷ 8 = **20.0** (hand-checked; matches the blueprint's Appendix A.3).

*Policy 2 — replace NoData with zero (wrong unless the missing cell is known to be zero).* Nine cells; sum still 160; mean 160 ÷ 9 = **17.78** (hand-checked). The mean dropped by 11 % because an *unknown* was treated as *nothing*.

*Policy 3 — the marker is not recognised.* If the file were read without its NoData header, −9999 would be a value: sum 160 − 9999 = −9839; mean −9839 ÷ 9 ≈ **−1093.2**. This is how an unrecognised marker announces itself — a wildly implausible statistic. Chapter 4 showed the same signature (−612.2 m) on the elevation grid.

**Which policy does the software apply?** ArcGIS Pro's **Calculate Statistics** tool computes raster statistics with an **Ignore Values** option that "allows you to exclude a specific value from the calculation of statistics. You may want to ignore a value if it is a NoData value or if it will skew your calculation" [X05]; a raster whose NoData is properly declared in its header is normally excluded without that option, but the page does not say so in those words — *verification item*: load `grid_a3.asc`, calculate statistics, and confirm the mean is 20.0, not 17.78. QGIS's **Raster layer statistics** algorithm "Calculates basic statistics from the values in a given band of the raster layer" (MIN, MAX, RANGE, SUM, MEAN, STD_DEV, SUM_OF_SQUARES) [Q05]; confirm the same 20.0 there. In either product, if you get 17.78 the NoData marker was lost on import; if you get about −1093 it was never declared.

**State the policy explicitly.** The worksheet line for any raster statistic must say: "NoData cells excluded (n = 8 of 9)". A reader who sees "mean 20" with no count cannot tell which policy produced it.

### 11.7.2 A conceptual threshold mask, with units — and what it is not

A **mask** is a raster of 1 where a condition holds and 0 (or NoData) where it does not. It is the raster analogue of a `WHERE` clause, evaluated per cell.

**Worked example — cells at or below 12 m on Fixture F6.**

*Question.* Which cells of `elevation_training.asc` have a height ≤ 12 m above the training datum TD-0, and what area do they cover?
*Inputs.* F6 (Chapter 4): 4 × 4 cells of 100 m; values in **metres above TD-0**; NoData −9999.0 in row 4, column 2.
*Condition.* `value <= 12` — units metres, threshold inclusive, datum TD-0 (a threshold without a datum is meaningless: 12 m above *what*?).

*Prediction (row by row, top row first; ✓ = 1, ✗ = 0, N = NoData):*

```
12.0 14.0 17.0 21.0   →  ✓ ✗ ✗ ✗
11.0 13.0 15.0 18.0   →  ✓ ✗ ✗ ✗
10.0 11.0 13.0 15.0   →  ✓ ✓ ✗ ✗
 9.0   N  12.0 13.0   →  ✓ N ✓ ✗
```

**Six** cells satisfy the condition (hand-checked); nine do not; one is NoData and *stays NoData* — the condition cannot be evaluated where there is no value. Area covered: 6 × 10,000 m² = **60,000 m² = 6 ha**. The unknown cell is neither "at or below 12 m" nor "above"; it is *unknown*, and the mask must say so (NoData), not 0.

*Software.* ArcGIS Pro's **Con** tool "Performs a conditional if/else evaluation on each of the input cells of an input raster"; with an expression such as `VALUE <= 12`, a true value of 1 and a false value of 0, and — for NoData — "If NoData does not satisfy the expression, it does not receive the value of the input false raster; it remains NoData" [X06]. Con requires the Spatial Analyst or Image Analyst extension at every licence level [X06]; so does Raster Calculator [X03]. In QGIS, the core Raster Calculator needs no extension: "Conditional expressions (=, !=, <, >=, …) return either 0 for false or 1 for true" [Q06], so the expression `"elevation_training@1" <= 12` is the mask; how the NoData cell is treated by the QGIS expression is a *verification item* (the cited page does not state it) — inspect that cell in the output.

**What the mask is not.** It is a set of cells whose stored height is at or below a number. It is **not a flood-risk map**. Flooding depends on where water comes from, how it flows, how much falls, what the ground absorbs, and what stands in the way — none of which is in a 4 × 4 elevation grid. The blueprint's boundary for this chapter excludes flood simulation, and the honest name for this output is "elevation ≤ 12 m (TD-0) mask". A colleague who labels it "flood zone" has made a claim the input cannot support; that claim is one of the 11.10 scenario questions.

### 11.7.3 Before combining rasters: alignment, cell size, extent, missing data

Raster operations combine rasters *cell by cell*. That only means something if cell (r, c) of one raster covers the same ground as cell (r, c) of the other. Four checks, all from Chapter 4's vocabulary, before any combination:

| Check | Question | R11-A versus F6 (this chapter's two grids) | What the software does if you skip it |
| --- | --- | --- | --- |
| **Extent** | Do the rasters cover the same ground? | No. R11-A covers x 1000–1300, y 700–1000 (in Ward B); F6 covers x 0–400, y 600–1000 (in Ward A). They do not overlap at all. | ArcGIS Pro tools use the environment's **Extent** (by default, often the intersection of inputs) — here empty, so the output is empty or all NoData |
| **Cell size** | Are the cells the same size? | Yes, both 100 m. | The **Cell Size** environment defaults to "Maximum of Inputs" — "Use the largest cell size of all input datasets", i.e. the coarsest [X08]; the finer raster is resampled (Chapter 4) without asking |
| **Alignment (snap)** | Do cell edges coincide? | Both have corners on multiples of 100 m, so if they *did* overlap they would align. A copy of R11-A with lower-left (1050, 700) would **not** — every cell would straddle two of the original's. | The **Snap Raster** environment makes tools "adjust the extent of output rasters so that they match the cell alignment of the specified snap raster"; "The lower left corner of the extent is snapped to a cell corner of the snap raster" [X07]. Without it, one input is resampled to the other's grid, changing values |
| **Missing data** | Where is NoData in each input, and what is the policy? | R11-A: centre cell; F6: row 4, column 2. | Per tool — typically NoData in any input gives NoData in the output for cell-by-cell operations, or is ignored where a tool offers that option [X04]; read the tool page |

The fourth row is the reason 11.7.1 came first: once two rasters are combined, the NoData cells of *both* propagate, and a statistic on the result must say how many cells survived.

> **Scope note.** Detailed raster processing — resampling choices, zonal statistics, map algebra chains, terrain derivatives — is later material. This module only establishes that rasters must be *checked for compatibility* before they are combined and that missing-data policy must be explicit. QGIS's Raster Calculator sets the output grid from a reference layer or explicit extent and cell size [Q05]; ArcGIS Pro sets it through the environment settings named above. Either way, you decide; do not let a default decide silently.

**Misconception — "the software will line the rasters up."** It will *produce* an output — by resampling one input to the other's grid, at the coarser cell size, over whatever extent the environment chose. Each of those is a decision with consequences for the values, made without you.

**Comprehension check 11.7.** A colleague computes the mean of Fixture F6 in two products and gets 13.6 in one and 12.75 in the other. Which product is wrong about the *file*, which policy did each apply, and what one line should the worksheet contain so the discrepancy could never have happened unnoticed?

---

## 11.8 Validate and document the workflow

### 11.8.1 Invariants and spot-checks

An analysis output is validated by two kinds of evidence, and the blueprint asks for both. **Invariants** are properties the output *must* have if the operations did what the worksheet says; **spot-checks** are individual records verified by hand. Neither is proof of correctness; together they catch most of what goes wrong.

| Property | Invariant to check | Example from this chapter |
| --- | --- | --- |
| **Counts** | Record counts are what the prediction said; sums reconcile (11.6.3) | 5 eligible requests; 4 near the road; per-ward sum = distinct assigned + double-counts |
| **Selected IDs** | The set of IDs, not just the count, matches the prediction | {P1, P3, P5, P6}, not merely "4" — four *wrong* IDs would give the same count |
| **Geometry type** | Output type is the predicted dimension | Clip of a line → line; intersect line/polygon → line; buffer of anything → polygon |
| **Extent** | Output extent lies inside the expected region | Clipped road extent within Ward A's extent; buffer extent = road extent grown by the distance |
| **CRS** | Output CRS is the intended analysis CRS, by *code*, on the Source tab | EPSG:32643 for every E11 output; "training grid, no CRS" for planar outputs |
| **Units** | Buffer distance field says what you typed, in the units you meant | `BUFF_DIST = 300` [S17]; area magnitudes plausible (a ward is about 10⁶ m², not 10⁻⁴ or 10¹²) |
| **Geometry validity** | Outputs pass the Chapter 9 validity check; no empty geometries where features are expected | Intersect of the two wards with default output type may be empty *by design* (11.4.2) — an *expected* empty; any other empty output is a defect |
| **Attributes** | Copied measures were recomputed or renamed (11.3.3, 11.4.3); statistic fields say the count they were computed over | `length_m` recalculated on clipped roads; "n = 8 of 9" beside the raster mean |

**Spot-checks.** Pick two or three records that exercise the edges — one boundary case (P5), one threshold case (P1 at exactly 300 m), one outside case (P6) — and verify them by hand from the coordinates, *not* by looking at the map. "P1: |200 − 500| = 300 ≤ 300 → in" is a spot-check. "P1 looks inside the buffer on screen" is not: at most map scales a point 5 m outside a polygon edge draws on the edge.

### 11.8.2 Change one thing: the sensitivity exercise

Change **one** input or parameter, predict what should change, run, and compare. This is not a proof; it is a way to discover which parts of the result are fragile.

| Change | Prediction (planar fixture, eligible requests P1, P3, P4, P5, P6) | What it teaches |
| --- | --- | --- |
| Threshold 300 → 250 m | P1 leaves the near-road set (300 > 250); P3 stays (250 ≤ 250, exactly on the new edge); set becomes {P3, P5, P6} | The set has a member on the edge at *both* thresholds — fixture data with round numbers do that, and so do real data with rounded coordinates |
| End type round → flat | P6 leaves (it is beyond the endpoint); {P1, P3, P5} | Requests near road *ends* depend on an easily overlooked parameter |
| Membership Contains → Completely contains | P5 leaves both wards; A = 1 (P1), B = 1 (P3); unassigned = P5, P6 | A boundary point's fate is a predicate choice, not a fact |
| Time window 2026-08-01 → 2026-09-01 | P1 (08-15), P4 (08-22), P6 (08-30) drop from eligibility; eligible {P3, P5}; near road {P3, P5} | The "recent" definition changes the answer more than any spatial parameter did |
| Distance method planar → geodesic (E11 only) | Distances change by about 0.1 m at most near the central meridian (0.04 % of ≤ 288 m; scale factor 0.9996, Chapter 6); no request changes class at a 200 m threshold | The method matters in principle; here its effect is far below the threshold's sensitivity — say so with the number |

Report the sensitivity table with the result. A reader who sees that P1's inclusion depends on ≤ versus < at exactly 300 m will treat the count of 4 with appropriate care; a reader who sees only "4" will not.

### 11.8.3 Record enough to rerun it

The record of an analysis is not the output layer; it is what lets someone else produce the same output layer. Minimum contents:

1. **Tool and version.** Product and release (ArcGIS Pro 3.x / QGIS 3.44 / PostGIS x.y), and the tool's name as it appears in that version.
2. **Parameters.** Every parameter, including the ones left at default — a default is still a decision (11.2.3, 11.7.3). ArcGIS Pro's geoprocessing **History** pane records "The tool input, output, and other parameter settings", custom environments, timing, success/failure, and messages; entries can be reopened with the same parameters, and the history can be written into the output dataset's metadata as a "Geoprocessing history" section [X09]. QGIS's history manager stores "The date and time of the execution … along with the parameters used", "as a command-line expression", re-executable by double-click [Q07]. Copy the relevant entries into the log; the panes are convenient but they are not the deliverable.
3. **Processing reference.** The CRS every operation ran in, by code, and the transformation used (or "none required") — Chapter 6's log lines.
4. **Source edition.** Which version of each input (file name, date, provenance note) — Chapter 7's intake form. An analysis of last month's request export is not an analysis of this month's.
5. **Output paths** and the geometry type, record count, and CRS of each output — so that a reader can confirm they opened the right thing.
6. **Known limitations.** What the output does not establish (11.2.2, 11.7.2, and whatever the fixture's provenance said).
7. **Preserved inputs.** Keep the unmodified inputs alongside the outputs. Several tools in this chapter modify their input (Near "adds fields directly to input features" [X02]; Calculate Geometry Attributes "modifies the input data" [X11]); run them on copies and say so.

**Developer analogy — a build that is reproducible from source.** Outputs are build artefacts; the log plus the preserved inputs are the source and the build script. A build nobody can rerun is a binary of unknown provenance. The analogy is exact for reproducibility. Where it stops: a software build is deterministic given the same inputs, whereas two GIS products given the same inputs can legitimately differ at the millimetre (buffer segmentation, tolerance, geodesic algorithm) — the log must record the tolerance within which "the same" is judged.

**Misconception — "the geoprocessing history is my documentation."** It records what the tool was told, not *why*, not which source edition, not what the output does not mean, and — in ArcGIS Pro — it lives in the project unless deliberately written to metadata [X09]. It is raw material for the log, not the log.

**Comprehension check 11.8.** A reviewer reruns your planar (Part 1) analysis and gets 3 eligible near-road requests instead of your 4 ({P1, P3, P5, P6}). List, in the order you would check them, four items from the log that could explain the difference, and say for each what value would make the reviewer's answer the correct one.

---
## 11.9 Guided lab: produce a service-request analysis

### 11.9.1 Objective and prerequisites

**Objective.** Answer one business question twice — first on the deterministic planar fixture, where every number can be checked on paper, then on the Earth-referenced Fixture E11, where the CRS, the units, and the measurement method have to be chosen and logged — and deliver the outputs, the worksheet, the validation evidence, and an interpretation that claims no more than the inputs support.

**The question (both parts).** *For each ward, how many requests that are OPEN and were reported on or after 2026-08-01 lie within the stated distance of the road? Report unassigned and boundary cases explicitly.* Threshold: **300 m** on the planar fixture (Part 1) and **200 m** on E11 (Part 2); both **boundary-inclusive** (≤). Boundary policy: **W-1** (fixture section).

**Prerequisites.** Modules 11.1–11.8; Chapter 6's lab (E6 files and the EPSG:32643 decision); Chapter 10's predicates.

**Execution status.** The steps below were written from the ArcGIS Pro and QGIS 3.44 documentation pages cited beside them and are **not execution-tested**. Expected values are hand-checked from the fixtures. Instructors: complete Instructor Appendix I.6 before issuing this lab.

### 11.9.2 Required software and access

- **Primary route:** ArcGIS Pro 3.x. Tools: Select Layer By Attribute, Select Layer By Location [X01], Buffer [S17], Near [X02], Clip [S32], Spatial Join [S31], XY Table To Point and Project (Chapter 6). All are listed as available at Basic, Standard, and Advanced on their pages, with Buffer's FLAT end type and LEFT/RIGHT/OUTSIDE_ONLY sides marked Advanced-only [S17] — this lab uses ROUND ends. No extension is required (the raster module 11.7 is not part of the lab). No ArcGIS Online account or credits are needed.
- **Alternative route (11.9.8):** QGIS Desktop 3.44, core algorithms only.
- The **Chapter 11 package**, built by the instructor from the text in 11.9.3: `Chapter11_Planar.gpkg` (or a file geodatabase) with an *unknown/undefined* coordinate system, exactly as `Chapter03_Town.gpkg` was built (Chapter 3, Instructor Appendix I.5), and the three E11 text files.
- A text editor for the log, and graph paper.

### 11.9.3 Input data — create it by copying the text below (synthetic)

**Part 1 — planar fixture (training grid, metres, no CRS).** Three UTF-8 CSV files; lines and polygons as WKT, points as x/y so that the point tools of both applications can be used (the Chapter 3 convention).

`wards_p11.csv`

```csv
ward,zone,households,wkt
A,Z1,1200,"POLYGON ((0 0, 1000 0, 1000 1000, 0 1000, 0 0))"
B,Z2,900,"POLYGON ((1000 0, 2000 0, 2000 1000, 1000 1000, 1000 0))"
C,Z1,400,"POLYGON ((0 1000, 1000 1000, 1000 1500, 0 1500, 0 1000))"
```

`roads_p11.csv`

```csv
road_id,name,length_m,wkt
R1,Main Road,2000,"LINESTRING (0 500, 2000 500)"
```

`requests_p11.csv`

```csv
request_id,category,status,reported_on,x,y
P1,Blocked drain,OPEN,2026-08-15,200,200
P2,Streetlight out,CLOSED,2026-07-30,800,800
P3,Pothole,OPEN,2026-09-01,1200,250
P4,Water leak,OPEN,2026-08-22,1700,900
P5,Fallen tree,OPEN,2026-09-05,1000,500
P6,Road damage,OPEN,2026-08-30,2200,500
```

**Part 2 — Fixture E11 (Earth-referenced).** `wards_e6.geojson` is Chapter 6's file, unchanged (copy it from Chapter 6, 6.8.3; EPSG:4326; positions longitude-first per RFC 7946 §3.1.1 [S18]). Two new files:

`requests_e11.csv` (EPSG:32643, metres)

```csv
request_id,category,status,reported_on,E_m,N_m
Q1,Blocked drain,OPEN,2026-08-20,500000.000,2543741.163
Q2,Streetlight out,CLOSED,2026-07-03,500000.000,2544405.362
Q3,Pothole,OPEN,2026-09-02,499487.611,2544073.271
Q4,Water leak,OPEN,2026-08-28,500512.389,2544073.271
Q5,Fallen tree,OPEN,2026-09-10,501229.733,2544073.313
```

`roads_e11.csv` (EPSG:32643, metres; WKT)

```csv
road_id,name,wkt
RE-1,Training Road,"LINESTRING (499000.000 2543900.000, 501000.000 2543900.000)"
```

`provenance_e11.txt` (copy verbatim; it is lab evidence)

```text
wards_e6.geojson  : Ward boundaries digitised for training. CRS WGS 84 (EPSG:4326).
                    GeoJSON positions, longitude first (RFC 7946). Unchanged from Chapter 6.
requests_e11.csv  : Request locations exported from the training request tracker on 2026-09-15.
                    CRS WGS 84 / UTM zone 43N (EPSG:32643). E_m, N_m in metres.
                    status = OPEN or CLOSED at export time; reported_on = local calendar date logged.
roads_e11.csv     : Road centreline digitised for training in EPSG:32643; WKT LINESTRING, metres.
All files are synthetic training data and describe no real place.
```

### 11.9.4 Ordered steps — Part 1, planar fixture (ArcGIS Pro route)

**Step 1 — Specification and worksheet before any tool (11.1).** Write the seven-item specification and the five-row worksheet from 11.1.3 (with step 3 already removed) into the log. Fill the *prediction* column of Table 11.9-A below on graph paper from the fixture coordinates. Do not open the GIS until this is done.

**Table 11.9-A — Prediction/result table (Part 1).** *Prediction values are hand-checked; the result column is yours.*

| Check | Prediction | Result | Match? |
| --- | --- | --- | --- |
| Eligible requests (`status = 'OPEN' AND reported_on >= DATE '2026-08-01'`) | P1, P3, P4, P5, P6 (5) | | |
| R1 buffer 300 m, round ends: area | ≈ 1,482,743 m² (segmented arcs slightly less; within 0.5 %) | | |
| R1 buffer: `BUFF_DIST` | 300 | | |
| Eligible requests within 300 m (≤) of R1 | P1, P3, P5, P6 (4); P4 out (400 m) | | |
| Near distances (m) for eligible requests | P1 300; P3 250; P4 400; P5 0; P6 200 | | |
| R1 clipped by Ward A: geometry, length | one line (0, 500)–(1000, 500); 1000 m; `length_m` still 2000 (stale) | | |
| Raw ward matches (Contains), near-road eligible requests, wards A/B/C | A: P1, P5 (2); B: P3, P5 (2); C: 0; sum 4 for 3 distinct | | |
| Reconciliation (requests as target, Within, one to many) | P1→A; P3→B; P5→A,B (multiply); P6→none (unassigned) | | |
| Policy W-1 report | A = 2 (P1, P5†); B = 1 (P3); C = 0; unassigned = 1 (P6); † BOUNDARY_TIEBREAK | | |

**Step 2 — Open and inspect.** *Procedure (version-specific; not execution-tested).* Open the instructor's project and confirm the three layers are present, that the map's coordinate system is *unknown* (do not set one — Chapter 3), and that the record counts are 3 wards, 1 road, 6 requests. Log them.

**Step 3 — Eligible requests (selection; 11.1.2).** Run **Select Layer By Attribute** on `requests_p11` with the expression `status = 'OPEN' And reported_on >= date '2026-08-01'` — the exact date-literal syntax depends on the data source (Chapter 10; a file geodatabase and a GeoPackage differ), so if the expression fails, consult the SQL reference for your source and log the working form. Expected selection: **5** records, IDs P1, P3, P4, P5, P6. Export the selection to `req_eligible` (right-click ▸ Data ▸ Export Features) so that later tools use a fixed input rather than a live selection; log the count of the export.

**Step 4 — Buffer the road (new geometry; 11.2).** Run **Buffer** [S17]: Input = `roads_p11`; Output = `r1_buf300`; **Distance = 300** — on the *unknown*-CRS planar package, leave the unit as *Unknown* so that the value is taken in the coordinate units (which the fixture declares to be metres) [S17]; Side Type = Full; End Type = **Round**; Dissolve Type = None; Method = Planar. *Verification item:* confirm on the installed version how the Distance parameter behaves with an unknown spatial reference (whether "300 Meters" is accepted or whether the unit must be left Unknown) and record it. Open the output table: expect one polygon, `BUFF_DIST = 300`, `ORIG_FID` pointing at R1. Calculate its area into a new field on a *copy* (Calculate Geometry Attributes modifies its input [X11]) and compare with the prediction.

**Step 5 — Requests near the road, two ways (selection; 11.2.3).**
 (a) **Select Layer By Location** [X01]: Input = `req_eligible`; Relationship = **Intersect**; Selecting Features = `r1_buf300`. Expected **4**: P1, P3, P5, P6. If P1 is missing, it is the exact-threshold case — go to (b) before concluding anything.
 (b) On a **copy** of `req_eligible` (Near adds fields to its input [X02]), run **Near**: Near Features = `roads_p11`; Method = Planar; no search radius. Read `NEAR_DIST` for each request and compare with the prediction row. Select `NEAR_DIST <= 300`: expected P1, P3, P5, P6. **This numeric test is the authoritative one for the threshold**; (a) is the map-friendly one. Log both results and any difference. Export the four as `req_near_road`.

**Step 6 — Clip the road to Ward A (new geometry; 11.3).** Select Ward A (attribute `ward = 'A'`), then run **Clip** [S32]: Input = `roads_p11`; Clip Features = the wards layer with A selected; Output = `r1_in_A`. Expect one line, endpoints (0, 500) and (1000, 500). Check `length_m`: it still reads 2000 — recalculate it on the output with Calculate Geometry Attributes (Length) into a new field `length_m_clip` [X11] and record 1000 (checked from the coordinates). Clear the selection afterwards.

**Step 7 — Count per ward (summary; 11.6).** Run **Spatial Join** [S31]: Target Features = `wards_p11`; Join Features = `req_near_road`; Output = `ward_counts_raw`; Join Operation = **Join one to one**; **Keep All Target Features = checked**; Match Option = **Contains**. Expected `Join_Count`: A 2, B 2, C 0 (sum 4 for 3 distinct requests), because Contains includes features on the boundary [X12]. *Verification item:* record the option that reproduced this; if Contains gives A 1, B 1 in the installed version, record the discrepancy with the documentation and use the option that does include the boundary (Instructor Appendix I.6).

**Step 8 — Reconcile (11.6.3).** Run Spatial Join the other way: Target = `req_near_road`; Join = `wards_p11`; **Join one to many**; Keep All Target Features checked; Match Option = **Within**; Output = `req_to_ward`. Expected 5 rows: P1–A, P3–B, P5–A, P5–B, P6–(no match, `JOIN_FID = −1` [S31]). Build the reconciliation table (assigned 2, multiply assigned 1, unassigned 1; total 4 = number of near-road eligible requests). Apply Policy W-1 in a table step (sort by `ward`, keep the first row per request, set `assign_rule = 'BOUNDARY_TIEBREAK'` for P5 and `'OUTSIDE'` for P6) and write the final report: A 2 (P1, P5†), B 1 (P3), C 0, unassigned P6.

**Step 9 — Sensitivity (11.8.2).** Rerun steps 4–5(b) with the threshold at 250 m and record the new near-road set (expected {P3, P5, P6}); rerun step 7 with **Completely contains** and record the new counts (expected A 1, B 1, C 0 with P5 unassigned). Do not rerun everything — one change at a time.

**Step 10 — Log.** For each tool run, copy the History entry [X09] into the log and add: why the parameter values were chosen, the CRS ("training grid; undefined; metres by declaration"), the output's geometry type, record count, extent, and the removed worksheet step.

### 11.9.5 Ordered steps — Part 2, Fixture E11 (ArcGIS Pro route)

**Step 11 — Intake and CRS decision (Chapters 6–7).** Read `provenance_e11.txt`. Decide the analysis CRS by Chapter 6's Table 6.6: **EPSG:32643** — the study area is inside zone 43N, the property needed is distance, planar measurement carries a documented 0.04 % scale effect at the central meridian, and two of the three inputs are already in it. Write the decision. Fill the prediction column of Table 11.9-B.

**Table 11.9-B — Prediction/result table (Part 2).** *Predictions hand-checked from Table F11-3 and Chapter 6.*

| Check | Prediction | Result | Match? |
| --- | --- | --- | --- |
| Eligible requests | Q1, Q3, Q4, Q5 (4); Q2 out (CLOSED, and 2026-07-03) | | |
| `wards_e6_utm` CRS and extent | EPSG:32643; E ≈ 498,975–501,025; N ≈ 2,543,520–2,544,627 (Chapter 6) | | |
| RE-1 length | 2,000.000 m | | |
| RE-1 buffer 200 m, round ends: area | ≈ 925,664 m² (segmented arcs slightly less) | | |
| Near distances (m), eligible requests | Q1 158.837; Q3 173.271; Q4 173.271; Q5 287.775 | | |
| Eligible within 200 m (≤) | Q1, Q3, Q4 (3); Q5 out | | |
| Raw ward matches (Contains) | A: Q1, Q3 (2); B: Q1, Q4 (2); sum 4 for 3 distinct | | |
| Reconciliation | Q1→A,B (multiply); Q3→A; Q4→B; none unassigned among the near-road set (Q5 was already out) | | |
| Policy W-1 report | A = 2 (Q3, Q1†); B = 1 (Q4); unassigned = 0; † BOUNDARY_TIEBREAK | | |
| Buffer clipped to A ∪ B: area | ≈ 820,000 m² (approximate — the projected ward edges are not exactly straight; within ± 1,000 m²) | | |

**Step 12 — Load with the right CRS.** As in Chapter 6 steps 2–4: add `wards_e6.geojson` (Source tab must read WGS 1984 / 4326); create points from `requests_e11.csv` with **XY Table To Point**, **Coordinate System = WGS 1984 UTM Zone 43N (WKID 32643)** — the default is WGS 84 geographic and would mislabel metres as degrees (Chapter 6). Load `roads_e11.csv`: *verification item* — the author did not verify a route for loading a WKT text column directly in ArcGIS Pro; the instructor should build `roads_e11` into the package by the Chapter 3 I.5 route (GDAL or QGIS export to GeoPackage, CRS EPSG:32643) or provide it as a two-vertex line for learners to create by typing coordinates in an edit session (Chapter 9). Confirm `roads_e11`'s Source tab reads WKID 32643 and its length is 2,000.000 m.

**Step 13 — Transform the wards.** Run **Project** on the wards to EPSG:32643 (`wards_e6_utm`), confirming no geographic transformation is offered (same datum), exactly as Chapter 6 step 8. Record the extent. All three layers are now stored in one projected CRS; no tool in Part 2 is run on a geographic layer.

**Step 14 — Eligible requests.** As step 3, on `requests_e11_utm`. Expected Q1, Q3, Q4, Q5. Export `req_e11_eligible`.

**Step 15 — Buffer and Near.** Buffer `roads_e11` by **200 Meters**, Round ends, Dissolve None, Method **Planar** (input is projected, so Euclidean buffers are created [S17]); log that at this location the planar 200 m is about 200.08 m on the ground (scale factor 0.9996, Chapter 6) and that the threshold is therefore planar-in-UTM by decision. Run **Near** (Planar) on a copy of `req_e11_eligible` against `roads_e11`; compare `NEAR_DIST` with the prediction row to **0.01 m**; select `NEAR_DIST <= 200` → Q1, Q3, Q4. Export `req_e11_near`.

**Step 16 — Clip the buffer to the wards (new geometry; 11.3).** Clip `re1_buf200` by `wards_e6_utm` → `re1_buf200_in_wards`. Expect a single polygon (or one record per ward part, depending on the tool's handling of a multi-polygon clip layer — *verification item*, 11.4.3) whose area is roughly 820,000 m²: the buffer's east and west round ends extend about 175 m beyond the ward edges and are removed. Record the actual area from Calculate Geometry Attributes on a copy [X11]; do not hand-derive it more precisely than ± 1,000 m² — the ward edges are projected meridians and parallels, not exact grid lines.

**Step 17 — Count, reconcile, policy.** As steps 7–8 with `wards_e6_utm` as target and `req_e11_near` as join. Expected raw: A 2 (Q1, Q3), B 2 (Q1, Q4); reverse join: Q1→A, B; Q3→A; Q4→B; W-1 report A 2 (Q3, Q1†), B 1 (Q4), unassigned 0. Q1 is *exactly* on the central meridian (easting 500,000.000, Chapter 6), which is also the shared ward edge — its double match is the boundary case by construction. *If Q1 matches only one ward*, the projected ward edge at that northing is not exactly at easting 500,000 in your software (it should be: the edge is the central meridian, which projects to the false easting); investigate with the Source tab and tolerance before accepting.

**Step 18 — Sensitivity and log.** Change the threshold to **170 m**: expected near-road set {Q1} only (Q3 and Q4, at 173.271 m, drop out); at **175 m** all three remain. Record both, and note that two requests sit within 5 m of a plausible threshold — a fact the report must state. Complete the log as in step 10, adding the CRS/transformation lines from Chapter 6.

### 11.9.6 Expected results and validation checks

| Check | Expected | If it fails |
| --- | --- | --- |
| Eligible count (Part 1 / Part 2) | 5 / 4 | Date literal parsed wrongly (Chapter 10) — test the date condition alone; check `status` case |
| `BUFF_DIST` | 300 / 200 | Units misread — buffer created in degrees or unknown units; re-run with explicit units on a projected input |
| Buffer area | ≈ 1,482,743 m² / ≈ 925,664 m², slightly less | Wrong distance or flat ends (exactly 1,200,000 / 800,000) |
| Near distances | Match Table F11-1 / F11-3 to 0.01 m | Wrong Method (geodesic on the planar package is meaningless), wrong near features, or a request layer in the wrong CRS |
| Near-road eligible set | {P1, P3, P5, P6} / {Q1, Q3, Q4} | Exact-threshold case (P1) lost by the polygon test — use the Near number; P6 lost — flat ends |
| Clipped road | 1 line, 1,000 m; `length_m` stale (2000) | Selection not applied (whole road returned, 2,000 m) — you selected instead of clipping |
| Raw per-ward counts | A 2, B 2, C 0 / A 2, B 2 | Boundary point (P5 / Q1) not matched — predicate is strict-interior; C missing — Keep All Target Features unchecked |
| Reconciliation identity | assigned + multiply + unassigned = near-road count | A request dropped or duplicated; find it by ID before doing anything else |
| Output CRS (Part 2) | WKID 32643 on every output's Source tab | A tool ran on the GeoJSON or on the mislabelled points; rebuild from step 12 |
| Originals | Byte-identical to 11.9.3 | A tool that modifies its input was run on an original (Near, Calculate Geometry) — restore from the text |

### 11.9.7 Troubleshooting

- **The date condition returns nothing.** The literal syntax is source-specific (Chapter 10); try the form the SQL reference gives for your workspace, and confirm `reported_on` was imported as a date, not text. A text field compares as text — `'2026-08-15' >= '2026-08-01'` happens to work for ISO dates but is a coincidence, not a method; log which happened.
- **Buffer on the planar package errors on units.** Enter the distance with the unit left as Unknown (coordinate units), or, if your version requires a defined spatial reference for linear units, record that as an installed-version constraint and use the Near number for the threshold test. *Verification item.*
- **P1 is not selected by Intersect with the buffer but `NEAR_DIST` = 300.** The expected exact-threshold behaviour. Report the Near result as authoritative and document the polygon test's tolerance sensitivity (11.2.3).
- **P5 or Q1 counted in one ward only.** Your match option is strict-interior, or the point is not exactly on the edge in the built package (check the stored coordinates: 1000, 500 / 500000.000). Switch to the boundary-inclusive option confirmed in I.6.
- **Ward C absent from `ward_counts_raw`.** Keep All Target Features was unchecked (inner join) [S31]. Re-run.
- **Per-ward sum ≠ distinct count and you cannot see why.** Run the reverse one-to-many join (step 8) and sort by request ID; the multiply-matched IDs appear twice.
- **Part 2 points appear near the equator.** The Coordinate System parameter was left at its default in XY Table To Point; delete and re-run (Chapter 6).
- **Q5's Near distance is not 287.775.** Its nearest road point is the *endpoint* (501,000, 2,543,900); if your value is 173.313 the road was extended or is longer than the fixture — check `roads_e11`'s vertices.

### 11.9.8 Required learner deliverables

1. **Output layers/tables:** `req_eligible`, `r1_buf300`, `req_near_road`, `r1_in_A`, `ward_counts_raw`, `req_to_ward` (Part 1) and their E11 equivalents (Part 2), plus the **untouched originals**.
2. **Analysis worksheet:** the seven-item specification; the five-row worksheet with the removed step and its reason; Tables 11.9-A and 11.9-B with the result columns filled and every mismatch explained.
3. **Validation evidence:** the invariants table of 11.8.1 filled in for each output; the hand spot-checks for P1, P5, P6 (Part 1) and Q1, Q5 (Part 2); the sensitivity runs of steps 9 and 18.
4. **Log:** tool and version, parameters (including defaults), CRS/transformation lines, source editions, output paths with geometry type/count/extent, tools run on copies, known limitations.
5. **Interpretation (≤ 150 words):** the Policy W-1 counts per ward, the unassigned and boundary cases, and *what the result does not say* — no travel times, no risk, no statement about roads other than R1/RE-1, and a sentence on the exact-threshold sensitivity.

### 11.9.9 QGIS alternative (equivalent foundational exercise; not execution-tested)

Written from the QGIS 3.44 pages cited; the same expected values apply. The important differences are in *which algorithm carries which attribute* and in how the date condition is written.

1. **Load.** Add the planar CSVs with **Add Delimited Text Layer** — WKT files with geometry "Well known text (WKT)", `requests_p11.csv` with point coordinates x/y, no Earth CRS (Chapter 3, I.5 Route B and its verification items). For E11, add `wards_e6.geojson`, the requests CSV as points with **Geometry CRS = EPSG:32643**, and `roads_e11.csv` as WKT with CRS EPSG:32643; run **Reproject layer** on the wards to EPSG:32643 (Chapter 6, 6.8.8).
2. **Eligible requests.** Use **Select features by expression** with `"status" = 'OPEN' AND "reported_on" >= '2026-08-01'` — if `reported_on` was read as text, the comparison is textual (log it), otherwise use `to_date('2026-08-01')`; *verification item*: which type the delimited-text loader assigned. Export the selection (**Export ▸ Save Selected Features As…**). Expected 5 / 4.
3. **Buffer.** Processing ▸ **Buffer** [Q01]: Distance 300 (planar package; units are the layer's coordinate units — for the unknown-CRS layer, that is the fixture's metres by declaration; for the E11 road in EPSG:32643, metres — *verification item* as in 11.2.1), Segments 5 or more, End cap style **Round**, Dissolve result unchecked. Compare area with the prediction via **Add geometry attributes** [Q01]; the area will be slightly under the hand value because of the segment count — raise Segments and watch it approach 1,482,743.
4. **Near-road set.** Two ways: **Select by location** (Predicate *intersect*, against the buffer) and, for the authoritative numeric test, **Join attributes by nearest** or **Distance to nearest hub (points)** [Q04] — *verification item*: which algorithm reports the perpendicular distance to a *line* (hub distance uses feature centres [Q04] and is therefore *not* the right tool for a line; confirm that Join attributes by nearest measures to the nearest point on the line, or compute distances with the field calculator expression `distance($geometry, geometry(get_feature('roads_p11','road_id','R1')))`). Filter `<= 300`. Expected {P1, P3, P5, P6} / {Q1, Q3, Q4}.
5. **Clip.** Processing ▸ **Clip** [Q02], Input `roads_p11`, Overlay Ward A (selected features only). The page states that attributes are unchanged while length "will be modified by the overlay operation" [Q02] — `length_m` stays 2000; add `$length` in a new field to get 1000.
6. **Count per ward.** Processing ▸ **Join attributes by location (summary)** [Q03]: Join to features in = wards; By comparing to = near-road requests; predicate **contains** ("Returns 1 (true) if and only if no points of b lie in the exterior of a" [Q03] — expected to include the boundary point; *verification item* with P5); statistics count on `request_id`. Expected `JOINED_COUNT` A 2, B 2, C 0; zero-count wards are kept because the summary algorithm keeps all input features (*verify*). Alternatively **Count points in polygon** [Q04] into `NUMPOINTS` — check that a boundary point is counted (*verify*).
7. **Reconcile.** Processing ▸ **Join attributes by location** [Q03]: Join to = near-road requests; By comparing to = wards; predicate *are within*; Join type **Create separate feature for each matching feature (one-to-many)**; leave "Discard records which could not be joined" unchecked so P6 appears with null ward. Expected 5 rows (Part 1). Apply W-1 in the attribute table or with a field-calculator expression.
8. **Log.** Copy the entries from the Processing history manager [Q07] and record the QGIS and PROJ versions, the project CRS, and the Measurements ellipsoid setting (Chapter 6, 6.8.8 step 5), which affects the measure tool but not the planar processing algorithms — *verify* that the processing algorithms used here measure in the layer CRS.

**Not claimed:** that QGIS and ArcGIS Pro produce identical buffer areas (they differ by segmentation), that the two products treat a boundary point identically under "contains" (both are expected to include it; I.6 records what was observed), or that the date comparison behaves identically (it depends on how each loader typed the column).

---
## 11.10 Independent check and progression gate

Answers are not in this section. Submit everything to the instructor, who marks against Instructor Appendix I.2–I.3. State every assumption you make; an answer that is correct under a stated assumption scores, an answer that silently picks one does not.

### 11.10.1 Concept questions (six)

**Q1 (11.1; LO1).** A manager asks: "Which streetlights are more than 50 m from any road?" Classify the *output kind* (selection, new geometry, transferred attributes, summary), name the *minimum* set of operations from this chapter (or Chapter 10) that answers it, and name one operation a beginner might add that would have no consumer.

**Q2 (11.2; LO2).** A layer of roads in EPSG:4326 is buffered in ArcGIS Pro with Distance = "250 Meters" and Method = Planar, and the same layer is buffered in PostGIS with `ST_Buffer(geom, 250)` where `geom` is a `geometry` column. Using the cited documentation, state what each product produces and which of the two results, if either, is a 250 m proximity area. Then state the one operation that makes the answer the same in both.

**Q3 (11.3; LO3).** A polygon layer of parks has a field `area_ha` filled in last year. You clip it to Ward A; one park is cut in half. Give the value of `area_ha` on the output record, say whether it is correct, and state the two acceptable ways to deliver the output.

**Q4 (11.4; LO4).** Intersect a *line* layer of pipes with a *polygon* layer of pressure zones. (a) What geometry type is the default output, and why? (b) A pipe of length 900 m crosses three zones; how many output records does it produce, and what does the copied field `length_m = 900` mean on each? (c) Name a defensible rule for reporting pipe length per zone.

**Q5 (11.5; LO5).** Fixture F11-2 is dissolved by `zone` in QGIS. A colleague reads `households = 1200` on the Z1 output row and reports "Zone Z1 has 1,200 households." Using the QGIS documentation's statement about attribute values in dissolve outputs, explain what went wrong, give the correct figure, and say how to obtain it in each product.

**Q6 (11.6; LO6).** A spatial join of wards (target) and requests (join), one to one, Keep All Target Features unchecked, match option Contains, gives counts A 4, B 3 for a request layer of 6 records. List *every* explanation consistent with these numbers (there is more than one), and state the single additional output that would let you decide between them.

### 11.10.2 Scenario questions (two)

**S1 (11.1, 11.2, 11.8; LO1, LO2, LO8).** The road crew supervisor receives your Part 1 report and says: "So P1, P3, P5 and P6 are the ones we can reach quickly from Main Road — I'll tell the council these four are within a five-minute response." Write the four-sentence reply you would send. It must (i) say what the 300 m result *does* establish, (ii) say precisely why "reach quickly" and "five-minute response" are not supported, (iii) point out the one request whose inclusion depends on ≤ versus < at exactly 300 m, and (iv) name the input that would be needed to answer the supervisor's real question.

**S2 (11.7, 11.8; LO7, LO8).** A colleague loads `elevation_training.asc` (Fixture F6), builds the mask `value <= 12`, publishes it as "Flood-prone cells (6 ha)", and — separately — reports the grid's mean height as 12.75 m. Identify the two errors, state the correct figure for the second with its cell count, explain what the NoData cell should show in the mask and why, and write the one-line title the mask layer should have had.

### 11.10.3 Independent practical task (unfamiliar inputs) — "Canal District" (synthetic)

**Inputs (planar training grid, metres, no CRS; all synthetic).**

`sectors_c11.csv`

```csv
sector,wkt
S1,"POLYGON ((0 0, 1200 0, 1200 800, 0 800, 0 0))"
S2,"POLYGON ((1200 0, 2400 0, 2400 800, 1200 800, 1200 0))"
```

`canal_c11.csv`

```csv
canal_id,wkt
K1,"LINESTRING (600 0, 600 800)"
```

`drain_requests_c11.csv`

```csv
request_id,status,reported_on,x,y
D1,OPEN,2026-09-03,500,100
D2,OPEN,2026-09-04,760,300
D3,CLOSED,2026-09-01,600,700
D4,OPEN,2026-09-06,1300,400
D5,OPEN,2026-09-02,450,800
D6,OPEN,2026-09-05,700,850
D7,OPEN,2026-08-25,1200,450
```

**Requirement.** The drainage engineer asks two things. (1) *For each sector, how many drainage requests that are OPEN and reported on or after 2026-09-01 lie within 150 m (≤) of canal K1? Report boundary and unassigned cases under Policy W-1.* (2) *What area of each sector lies within 150 m of K1, and what fraction of the sector is that?*

**Do, in this order.**

1. Write the seven-item specification and a worksheet for *both* questions. Identify the output kind of each. Choose the subset of {buffer, clip, intersect, dissolve, spatial join, Near/select-by-location} you need for each and **justify the sequence**; name at least one operation you considered and rejected, with the reason.
2. Predict, on paper, every intermediate result (eligible IDs, distances, near-canal IDs, sector membership, counts, areas) before running anything.
3. Run it (either route) and fill a prediction/result table.
4. Run one sensitivity change of your choosing and report its effect.
5. **The misleading alternative.** A colleague proposes to present the 150 m corridor as the "canal flood-risk zone" and to rank the sectors by their raw request count as "risk ranking". Explain, in no more than 120 words, why neither is supported by the inputs, and what each output *can* honestly be called.
6. Deliver: outputs, worksheet, prediction/result table, log, sensitivity note, and the 120-word explanation.

### 11.10.4 Oral explanation (one)

Bring your Part 1 outputs. The instructor will change **one** parameter (threshold, end type, match option, or time window) without telling you which and show you the new per-ward counts. Explain, in under three minutes, which parameter was changed and how you know — using the fixture coordinates, not the screen. Then explain why the per-ward sum in the raw join can exceed the number of requests.

### 11.10.5 Submission requirements and scoring

**Submit:** written answers to Q1–Q6 and S1–S2; the practical package of 11.10.3 (outputs, worksheet, tables, log, notes); the lab deliverables of 11.9.8; attend the oral.

**Scoring (100 points; suggested weights adapted from blueprint Appendix B.3).**

| Component | Points | Criteria |
| --- | --- | --- |
| Concept questions Q1–Q6 | 24 (4 each) | Correct under stated assumptions; cites the documented behaviour where the question asks for it; distinguishes general principle from product behaviour |
| Scenario S1–S2 | 16 (8 each) | Identifies what the result establishes and what it does not; corrects the figure with its count; names the missing input |
| Practical 11.10.3 — reasoning and sequence | 14 | Specification complete; output kinds right; sequence justified; a rejected operation named with reason |
| Practical — correctness and edge cases | 16 | Eligible set, near-canal set, boundary and unassigned cases, counts, areas and fractions all correct; reconciliation identity holds |
| Practical — validation and documentation | 12 | Prediction/result table; invariants; sensitivity change; log sufficient to rerun |
| Practical — the misleading alternative | 8 | Both claims refused with the right reasons; honest names given |
| Oral | 10 | Parameter identified from the coordinates; sum-versus-distinct explained |

**Pass threshold:** 80 points, *and* no critical misconception. For this chapter the critical misconceptions are: applying a metre threshold to degree coordinates; presenting proximity as travel time or a mask as risk; summing attributes copied onto split records; dropping unmatched or zero-count records from a report; and reporting a per-ward sum without reconciling it to the number of distinct requests. Any of these requires remediation (Instructor Appendix I.4) and a fresh exercise (I.5) before progression, regardless of the score.

**Progression.** Another trainee must be able to reproduce your Part 1 counts from your log and inputs *without* your outputs. If their result differs, you progress when you can explain the discrepancy by parameter, predicate, tolerance, or fixture — not when the screenshots match.

---

## 11.11 Media and source brief

This module records what the presentation, audio, and any interactive material must contain; the Media Appendix (M.1–M.5) carries the detailed specifications.

1. **Animate before/after geometries for each operation** on the planar fixture, with input IDs and output attributes visible: R1 → its 300 m buffer (with `BUFF_DIST` appearing in a table beside it); R1 → its clip by Ward A (with `length_m` staying 2000 and turning red); R1 + wards → two intersect records (with the ward code arriving on each and `length_m` copied to both); F11-2 wards → two zone features (Z2 visibly two-part); requests → wards join with P5 counted twice and P6 falling off the table. Every frame uses the coordinates printed in this chapter so that a viewer can check the geometry; no frame shows "real" data.
2. **Narration** states, for each operation, the *question*, the *operation*, the *expected change*, and the *validation step*, in that order, with a pause before the expected change so listeners predict it. A 2D animation is the primary medium; the only 3D element proposed (M.5) is a vertical bar view of the R11-A grid to make NoData visibly "absent", and it is optional.
3. **Operation sources:** buffer [S17], clip [S32], intersect [S33], dissolve [S34], spatial join [S31]; the QGIS equivalents [Q01]–[Q04] and PostGIS equivalents [P01]–[P03] for the transfer notes. Chapter 12 takes the checked result of this chapter and teaches how to communicate it — classification, normalisation, and honest uncertainty — without concealing the limitations recorded here.

---

## Glossary

| Term | Meaning in this chapter |
| --- | --- |
| **Analysis specification** | The written decision, study area, time window, eligible records, units/CRS/method, output kind, and acceptance checks, prepared before any tool is chosen (11.1). |
| **Analysis worksheet** | One row per intermediate output, each with the operation, inputs, output, and *consumer*; rows without a consumer are removed (11.1.3). |
| **Output kind** | Whether an answer is a selection, a new geometry, transferred attributes, or a summary; determines the operation (11.1.2). |
| **Buffer** | The polygon of all points within a given distance of a feature; parameters: distance and units, end type, method (planar/geodesic), dissolve type (11.2). |
| **End type / end cap** | How a line buffer is closed at the line's endpoints: round (semicircle) or flat (11.2.1). |
| **Dissolve type (buffer)** | Whether individual buffers are kept (None), merged into one (All), or merged by attribute (List) (11.2.1). |
| **Planar / geodesic method** | Whether a buffer or distance is computed on the flat projected plane or on the ellipsoid; in ArcGIS Pro, the Planar method creates geodesic buffers for linear distances on geographic inputs (11.2.1). |
| **Clip** | Keeps the parts of input features that lie inside the clip features; geometry cut, input attributes copied unchanged, no attributes from the clip layer (11.3). |
| **Stale measurement** | A stored length/area field that no longer describes the feature's geometry after an operation changed it (11.3.3). |
| **Intersect (overlay)** | Constructs the common parts of two (or more) inputs as new features carrying attributes from all inputs; splits records at overlay boundaries (11.4). |
| **Predicate versus overlay** | *Intersects?* returns true/false about existing records; *intersection* returns new geometry (11.4.1). |
| **Output dimension** | Point 0, line 1, polygon 2; an overlay's default output is the lowest dimension among its inputs (11.4.2). |
| **Allocation rule** | The stated rule for what to do with a quantity copied onto split records: recompute from geometry, weight by kept fraction, or do not sum (11.4.3). |
| **Ratio policy** | An ArcGIS Pro layer-field option that scales a copied attribute by the kept fraction of the geometry in Clip/Intersect (11.3.3). |
| **Dissolve** | Groups features by attribute values and unions each group's geometry; statistics can be computed per group; other attributes are dropped (ArcGIS Pro) or arbitrary (QGIS) (11.5). |
| **Multipart output** | A dissolved group whose members do not touch becomes one feature with several parts (11.5.2). |
| **Append / merge** | Stacking records of several datasets into one without changing geometry — not a dissolve (11.5.1). |
| **Spatial join** | Transfers attributes from join features to target features by a spatial relationship; one-to-one aggregates multiple matches, one-to-many produces a row per match (11.6). |
| **Target / join features** | The layer whose records appear in the output / the layer whose attributes are transferred (11.6.1). |
| **Keep all target features** | Outer-join behaviour: unmatched targets are kept (with count 0); unchecked, they are dropped (11.6.2). |
| **`Join_Count`** | ArcGIS Pro's count of join features matching each target (11.6.1). |
| **Match option** | The predicate used by a spatial join (Contains, Completely contains, Within, Intersect, Closest, …) (11.6.1). |
| **Boundary policy (W-1)** | Chapter 10's 10.7.2 policy: raw membership is boundary-inclusive; for unique assignment a request touching several wards goes to the alphabetically lower ward code with `assign_rule = 'BOUNDARY_TIEBREAK'`; unmatched requests keep `assigned_ward = NULL` with `assign_rule = 'OUTSIDE'` and are reported, not dropped (fixture section). |
| **Reconciliation** | The table showing each source ID as assigned, multiply assigned, or unassigned, whose counts must sum to the number of source records (11.6.3). |
| **NoData policy** | The stated choice of whether missing cells are excluded from a statistic, replaced, or propagated (11.7.1). |
| **Mask** | A raster of 1/0 (and NoData) recording where a per-cell condition holds (11.7.2). |
| **Alignment / snap raster** | Whether two rasters' cell edges coincide; an environment setting that forces an output onto a reference grid (11.7.3). |
| **Invariant** | A property an output must have if the operations were done as stated — counts, IDs, geometry type, extent, CRS, units, validity (11.8.1). |
| **Sensitivity exercise** | Changing one input or parameter, predicting the change, and comparing (11.8.2). |
| **Geoprocessing / processing history** | The product's record of tool runs and parameters; raw material for the log, not the log (11.8.3). |

## Recap

Write the specification first: the decision, the study area, the time window, the eligible records, the units and method, the output *kind*, and the checks. The kind — selection, new geometry, transferred attributes, or summary — chooses the operation; a worksheet row with no consumer is removed. A **buffer** is the polygon within a distance; its distance, units, end type, method, and dissolve type all change the answer, and it says nothing about travel. **Clip** cuts geometry and copies attributes unchanged, so stored measurements go stale. **Intersect** builds the common parts with attributes from every input and splits records; quantities copied onto the pieces must not be summed without an allocation rule. **Dissolve** is `GROUP BY` for shapes, drops or scrambles the other attributes, and may produce multipart features. A **spatial join** counts or transfers by predicate; keep the zero-count areas, list the unmatched, and reconcile assigned + multiply assigned + unassigned to the number of source records — a sum larger than the request count is always explained by double matches. Raster statistics need an explicit NoData policy (8 of 9 cells, mean 20 — not 17.78), a mask needs units and a datum and is not a risk map, and rasters must be checked for extent, cell size, alignment, and missing data before they are combined. Validate with invariants and hand spot-checks, change one thing and predict the effect, and record enough — tool, version, parameters, CRS, transformation, source edition, outputs, limitations, preserved inputs — for someone else to rerun the work.

## Cross-references to later chapters

- **Chapter 12 — Cartography and interpreting maps correctly.** Takes the per-ward counts of 11.6 and asks how to show them: raw counts versus rates and density (the Z1/Z2 MEAN trap of 11.5.2 returns as normalisation), classification, and how to show the unmatched and boundary cases and the exact-threshold sensitivity on the map itself rather than in a footnote.
- **Phase 2 (later material).** Network-based travel time and service areas — the honest answer to the supervisor in S1; zonal statistics, resampling choices, and map-algebra chains that build on 11.7.3's compatibility checks; suitability and hotspot methods excluded by this chapter's boundary; automating the 11.9 workflow in ModelBuilder, the QGIS model designer, or a PostGIS SQL script — the analysis worksheet of 11.1.3 is the design for that script.

## References

All pages were opened and read on **19 September 2026**. Esri "latest" pages are mutable; earlier chapters recorded the ArcGIS Pro release shown on the check date as 3.7 — record the installed version when running the lab. QGIS links use the pinned 3.44 edition.

| ID | Publisher — page title | Link | Used for |
| --- | --- | --- | --- |
| S17 | Esri — Buffer (Analysis) — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/analysis/buffer.html | Distance units default; Planar/Geodesic method sentences; Dissolve Type None/All/List; `BUFF_DIST`/`ORIG_FID`; End Type and Side Type licence notes; Z/M not transferred |
| S18 | RFC Editor — RFC 7946: The GeoJSON Format | https://www.rfc-editor.org/rfc/rfc7946 | Position order (§3.1.1) for `wards_e6.geojson` |
| S31 | Esri — Spatial Join (Analysis) — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/analysis/spatial-join.html | Target/join roles; Join one to one / one to many; Keep all target features; `Join_Count`, `JOIN_FID` = −1; match option list and the Contains / Completely contains / Contains Clementini / Within / Closest sentences; merge rules |
| S32 | Esri — Clip (Analysis) — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/analysis/clip.html | Summary; clip-feature geometry rules; attributes retained; ratio policy; Pairwise Clip; licensing |
| S33 | Esri — Intersect (Analysis) — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/analysis/intersect.html | Summary; simple-feature inputs; lowest-dimension default; Output Type values; attributes copied; Attributes To Join; two-input limit at Basic/Standard; Pairwise Intersect |
| S34 | Esri — Dissolve (Data Management) — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/dissolve.html | Summary; no-field dissolve; Create multipart features; statistics naming and null exclusion; text-field statistics; Unsplit lines; licensing |
| X01 | Esri — Select Layer By Location (Data Management) — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/select-layer-by-location.html | Relationship definitions (Intersect, Contains, Completely contains, Contains Clementini, Within, …); licensing |
| X02 | Esri — Near (Analysis) — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/analysis/near.html | Fields added to the input; `NEAR_FID`/`NEAR_DIST` = −1 when none; Planar/Geodesic; random tie-break |
| X03 | Esri — Raster Calculator (Spatial Analyst) — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/spatial-analyst/raster-calculator.html | Extension requirement at every licence level |
| X04 | Esri — NoData and how it affects analysis — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/help/analysis/spatial-analyst/performing-analysis/nodata-and-how-it-affects-analysis.html | NoData definition; NoData ≠ 0; per-tool policy |
| X05 | Esri — Calculate Statistics (Data Management) — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/calculate-statistics.html | Ignore Values option; skip factors; licensing |
| X06 | Esri — Con (Spatial Analyst) — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/spatial-analyst/con-.html | Conditional evaluation; NoData remains NoData; expression form; extension requirement |
| X07 | Esri — Snap Raster (Environment setting) — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/environment-settings/snap-raster.html | Alignment of output extent to a snap raster |
| X08 | Esri — Cell Size (Environment setting) — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/environment-settings/cell-size.html | Maximum of Inputs default (coarsest) |
| X09 | Esri — Geoprocessing history — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/help/analysis/geoprocessing/basics/geoprocessing-history.html | What history records; rerun from history; writing history to dataset metadata |
| X10 | Esri — Summarize Within (Analysis) — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/analysis/summarize-within.html | Alternative summary tool; Keep all input polygons; areal proportioning of polygon summaries |
| X11 | Esri — Calculate Geometry Attributes (Data Management) — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/calculate-geometry-attributes.html | "This tool modifies the input data"; Length/Area and geodesic properties; unsupported for geographic/Web Mercator planar measures |
| X12 | Esri — Select By Location graphic examples — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/select-by-location-graphical-examples.html | Boundary behaviour of Contains ("inside as well as on the boundary"), Completely contains, Contains Clementini, Within, Completely within, Within Clementini |
| Q01 | QGIS 3.44 — Vector geometry (Processing algorithms: Buffer, Dissolve, Add geometry attributes) | https://docs.qgis.org/3.44/en/docs/user_manual/processing_algs/qgis/vectorgeometry.html | Buffer parameters (Distance, Segments, End cap style, Dissolve result); Dissolve description, first-feature attribute values, multi-geometry output, Keep disjoint features separate |
| Q02 | QGIS 3.44 — Vector overlay (Processing algorithms: Clip, Intersection) | https://docs.qgis.org/3.44/en/docs/user_manual/processing_algs/qgis/vectoroverlay.html | Clip keeps parts within; attributes unchanged while area/length change; Intersection carries attributes of both layers |
| Q03 | QGIS 3.44 — Vector general (Join attributes by location; Join attributes by location (summary)) | https://docs.qgis.org/3.44/en/docs/user_manual/processing_algs/qgis/vectorgeneral.html | Predicate list and definitions; join types; Discard records option; `JOINED_COUNT`; summary statistics |
| Q04 | QGIS 3.44 — Vector analysis (Count points in polygon; Distance to nearest hub) | https://docs.qgis.org/3.44/en/docs/user_manual/processing_algs/qgis/vectoranalysis.html | `NUMPOINTS`; weight/class fields; hub distance measured between feature centres |
| Q05 | QGIS 3.44 — Raster analysis (Raster calculator; Raster layer statistics) | https://docs.qgis.org/3.44/en/docs/user_manual/processing_algs/qgis/rasteranalysis.html | Reference layer/extent/cell size for the calculator; statistics output list |
| Q06 | QGIS 3.44 — Raster Analysis (Raster Calculator dialog) | https://docs.qgis.org/3.44/en/docs/user_manual/working_with_raster/raster_analysis.html | Conditional expressions return 0/1; masking example |
| Q07 | QGIS 3.44 — The history manager | https://docs.qgis.org/3.44/en/docs/user_manual/processing/history.html | Date, time, parameters stored as a command-line expression; re-execution by double-click |
| P01 | PostGIS — ST_Buffer | https://postgis.net/docs/ST_Buffer.html | Definition; geometry units versus geography metres; style parameters; use `ST_DWithin` for distance queries |
| P02 | PostGIS — ST_Union | https://postgis.net/docs/ST_Union.html | Aggregate union with `GROUP BY` as dissolve |
| P03 | PostGIS — ST_Intersection | https://postgis.net/docs/ST_Intersection.html | Point-set intersection; lower-dimensional results |
| — | This course — Chapter 6 (`GIS_Phase_1_Chapter_06_Projections_Transformations_and_Measurement.md`), 6.5–6.8 and Instructor Appendix I.3 | local file | Fixture E6 definition, the EPSG:32643 decision, the 0.9996 scale factor, projected ward extent, XY Table To Point and Project procedures |
| — | This course — Chapter 4, fixture section | local file | Esri ASCII raster header convention; Fixture F6 and its hand-checked statistics |
| — | This course — Chapters 3, 9, and 10 | local files | Road R2; Ward C's intended shape; the WKT-CSV package convention and its build routes; Chapter 10's 10.7.2 assignment policy and its field names |

*Not consulted, and therefore not cited:* the EPSG registry (Chapter 6's citations cover the CRS parameters used). The graphic-examples page [X12] was reached at its `tool-reference` URL after the `help/mapping/navigation` URL linked from the tool page returned "page not found".

---
# Instructor Appendix (separate from learner material)

Learners should not receive this appendix before the progression gate in 11.10. Everything here was derived by hand from the printed fixtures; nothing was executed in software. Section I.6 lists what must be run and observed before the lab is issued.

## I.1 Answers and reasoning — comprehension checks

| Check | Answer and reasoning |
| --- | --- |
| 11.1 | The new question needs **new geometry** (the part of each ward within 300 m of R1 — the buffer intersected or clipped by the wards) *and* a **summary** (area per ward, and its share of the ward). Step 3 now has a consumer — the map and the area table — so it stays. But the area must be *recomputed from the output geometry* (600,000 m² for A and for B on the planar fixture; 60 % of each ward), never copied from a buffer field (11.4.3). |
| 11.2 | The 200 m Planar buffer on the EPSG:32643 road is in **metres** (projected input → Euclidean buffer [S17]). The 0.002 buffer on the EPSG:4326 copy is in **degrees**: the layer's linear unit is the degree, and 0.002° is about 205 m east–west and about 221 m north–south at this latitude (Chapter 6: 0.010° ≈ 1,025 m and 1,107 m), so the "buffer" is not even the same width in both directions. Had they typed "200 Meters" on the EPSG:4326 copy with Method = Planar, ArcGIS Pro would have created a *geodesic* buffer because the distance was given in linear units on geographic input [S17] — correct, but ArcGIS-specific behaviour. |
| 11.3 | R2 = (500, 0)→(500, 500)→(800, 900): segment lengths 500 m and √(300² + 400²) = 500 m, total 1,000 m; entirely inside Ward A. **Clip by A:** the whole of R2, unchanged (every feature fully inside is kept whole); `length_m` (1000) still describes the geometry — no recalculation needed, though re-checking is cheap. **Clip by B:** nothing — R2 has no part in B, so the output has **no features** (an expected empty output, to be logged as such). |
| 11.4 | R6 (500, 800)→(500, 1300) crosses A (y ≤ 1000) and C (y ≥ 1000), never B. Output: **2 line records** — R6/A from (500, 800) to (500, 1000), 200 m; R6/C from (500, 1000) to (500, 1300), 300 m. Each carries `length_m = 500`, so the copied values sum to 1,000 m for 500 m of road; the recomputed lengths sum to 500 m. |
| 11.5 | **One** output feature. A, B, C are mutually contiguous (A–B share x = 1000; A–C share y = 1000) and union into one part; D is detached, so the geometry has **2 parts**. `COUNT_ward = 4`; `SUM_households = 1200 + 900 + 400 + 150 = 2,650`. |
| 11.6 | Completely contains: A = 2 (P1, P2), B = 2 (P3, P4), C = 0; unassigned = 2 (P5, P6); identity 4 assigned + 0 multiply + 2 unassigned = 6. **P5** moved from *multiply assigned* to *unassigned* because a boundary point is not in the interior of either ward under the strict predicate. |
| 11.7 | 13.6 m is the mean of the **15 valid** cells (sum 204); 12.75 m = 204 ÷ 16 treats the NoData cell as 0 — that product (or that import) lost the NoData marker, so it is wrong about the *file*. The worksheet line: "mean of 15 valid cells out of 16; NoData (−9999.0) excluded". With that line, 12.75 could only appear beside "16 cells", which is visibly inconsistent. |
| 11.8 | Check, in order: (1) **threshold inclusivity** — spec says ≤ 300; if the reviewer used < 300, P1 (exactly 300) drops → 3 (their answer is correct only if the spec said "less than"); (2) **end type** — flat ends drop P6 (200 m beyond the endpoint) → 3 (correct only if the spec said flat); (3) **near-road test method** — polygon intersect versus Near number: a tolerance artefact can drop P1 (the Near number is authoritative; 4); (4) **time window / status literal** — a window starting 2026-09-01 gives eligible {P3, P5} and near-road {P3, P5} = 2, not 3, so this one does not explain 3 but must still be checked because a mis-parsed date can drop any subset. |

## I.2 Answer key — 11.10 concept and scenario questions

**Q1.** Output kind: a **selection** of streetlights (existing records). Minimum operations: Near (streetlights → roads, all roads as near features, planar on the training grid) then an attribute condition `NEAR_DIST > 50` — or Select By Location *Within a distance* 50 m of roads and invert the selection. No new geometry is required. A beginner's consumer-less addition: buffering every road by 50 m and dissolving (a polygon nobody uses) — or clipping the streetlights to the wards when the question has no study area. Award full marks for any correct minimal pair with a sound rejected operation.

**Q2.** ArcGIS Pro: with Method = Planar and a distance in linear units on a *geographic* input, "geodesic buffers will be created" [S17] — a genuine 250 m proximity area. PostGIS `ST_Buffer(geom, 250)` on a `geometry` in EPSG:4326 uses the SRS units, degrees [P01] — a 250-*degree* buffer, meaningless (it wraps the world). Neither product is "wrong"; they have different unit rules. The operation that makes both the same: **project to a suitable projected CRS (EPSG:32643 in the fixture) and buffer in metres** — or, in PostGIS, cast to `geography`, where the distance is in metres [P01].

**Q3.** `area_ha` on the cut park is **its old value** — the whole park's area — because Clip copies attributes unchanged [S32] [Q02]. It is *not* correct for the output shape. Acceptable deliveries: (a) recompute area from the output geometry into `area_ha` (on a copy, since Calculate Geometry Attributes modifies its input [X11]) or (b) rename the copied field `area_ha_orig` and add the recomputed field, so no reader mistakes one for the other. Leaving `area_ha` as is under its original name is not acceptable.

**Q4.** (a) **Line** — the default output is the lowest dimension among the inputs, and a line (1) is lower than a polygon (2) [S33]. (b) **Three** records, one per zone the pipe crosses; `length_m = 900` on each is the *original pipe's* length, copied [S33], and no longer describes any record's geometry. (c) Recompute length from each output record's geometry and sum per zone (the three recomputed lengths sum to 900 m); or, if a non-geometric quantity must be split, weight by kept fraction with the uniformity assumption written down (the ratio-policy idea [S33]).

**Q5.** QGIS's Dissolve keeps the input fields but "The values in the output layer's fields are the ones of the first input feature that happens to be processed" [Q01] — so 1,200 is Ward A's value, not the zone's. Correct figure: Z1 = A + C = **1,600** households. To obtain it: QGIS — use a statistics-capable aggregation (for example **Join attributes by location (summary)** with the zones as targets and sum of `households` [Q03], or the field calculator's `aggregate` expression, or `Dissolve` followed by a separate sum), and never read a non-dissolve field from a plain dissolve; ArcGIS Pro — list `households` in **Statistics Fields** with SUM; the output field is `SUM_households` [S34].

**Q6.** The identity is *sum of counts = distinct matched requests + extra matches*: 7 = d + e with d ≤ 6 and e ≥ 1. Consistent explanations: (i) all six requests matched, one of them on the A/B boundary and counted in both (6 + 1); (ii) five matched and two on the boundary, one request outside every ward (5 + 2); (iii) four matched with three boundary double-counts and two outside (4 + 3) — unlikely but consistent; (iv) any of these where a "boundary" request actually sits at the A/B/C corner (1000, 1000) and is also counted in C, which is invisible because Keep All Target Features was unchecked and C is not shown unless it has a match — the A/B sum is unaffected but the meaning of "double" changes; (v) duplicate source records (two records at one location, Chapter 9) do not change the identity — they are among the six — but change what "six requests" means and must be listed separately. The single output that decides: the **reverse join** (requests as target, wards as join, one to many, keep all targets), which lists per request ID how many wards it touched (2 = boundary, 0 = outside) and exposes duplicate records at one location. Award full marks for the identity, at least explanations (i), (ii), and (v), and the reverse join or an equivalent per-request listing.

**S1.** A model reply: (i) "The result lists the OPEN requests reported since 1 August whose straight-line distance to the R1 centreline is at most 300 m: P1, P3, P5, P6." (ii) "It does not establish reachability or response time — the buffer is a straight-line distance, and we have no road network, no travel speeds, and no information on which side of any barrier a request sits; 'five-minute response' is not supported by any input." (iii) "P1 is exactly 300 m away; it is included because the rule is ≤ 300, and it would be excluded under < 300." (iv) "To answer 'reach quickly' we would need a routable road network with travel times and a network/service-area analysis, which is later material."

**S2.** Error 1: the mask is *elevation ≤ 12 m above TD-0*, not flood-proneness — nothing in a 16-cell height grid supports a flood claim (no water source, flow, rainfall, drainage). Error 2: 12.75 m treats the NoData cell as 0; the correct mean is **13.6 m over 15 valid cells** (sum 204 ÷ 15). The NoData cell must remain **NoData** in the mask — a condition cannot be evaluated where there is no value, and 0 would assert "above 12 m" — as Con's documentation states for NoData [X06]. Title: "Cells with elevation ≤ 12 m above training datum TD-0 (6 of 15 valid cells; 1 cell no data) — synthetic".

## I.3 Expected results — 11.9 lab and 11.10.3 practical (hand-checked; software reproduction required)

**11.9 Part 1 (planar).** All values in Table 11.9-A are hand-derived and exact for the rectangle parts; the only approximate value is the round-ended buffer area (1,482,743.34 m² by formula; the software's segmented arcs give slightly less — record the observed value and the segment/densification setting). Additional checks: the round-ended buffer's extent is x −300…2300, y 200…800; `req_to_ward` has 5 rows; the W-1 table has P5 with `assign_rule = 'BOUNDARY_TIEBREAK'` and P6 with `'OUTSIDE'`. Sensitivity: 250 m → {P3, P5, P6}; Completely contains → A 1 (P1), B 1 (P3), C 0, unassigned {P5, P6}.

**11.9 Part 2 (E11).** Distances (planar, EPSG:32643): Q1 158.837; Q2 505.362; Q3 173.271; Q4 173.271; Q5 287.775 m (nearest point for Q5 is the endpoint (501,000, 2,543,900)). Buffer 200 m round: 925,663.71 m² by formula. Clipped to A ∪ B: ≈ 819,950 m² if the ward edges were exactly at eastings 498,975 and 501,025 (rectangle 800,000 m² plus two 25 m-wide circular strips of 9,974 m² each); the projected edges are slightly curved and not exactly at those eastings, so accept **± 1,000 m²** and record the observed value. Raw counts A 2 (Q1, Q3), B 2 (Q1, Q4); W-1: A 2 (Q3, Q1†), B 1 (Q4), unassigned 0. Sensitivity: 170 m → {Q1}; 175 m → {Q1, Q3, Q4}. Geodesic (ground) distances exceed the planar UTM ones by about 0.04 % here (scale factor 0.9996): at most about 0.12 m for Q5 — no class changes.

**11.10.3 Canal District (planar; hand-checked).**

| Item | Expected |
| --- | --- |
| Eligible (OPEN, ≥ 2026-09-01) | D1, D2, D4, D5, D6 (5). D3 out (CLOSED); D7 out (2026-08-25) |
| Distances to K1 (finite, x = 600, y 0…800) | D1 100; D2 160; D3 0; D4 700; D5 150; D6 √(100² + 50²) = 111.80 (nearest point is the endpoint (600, 800)); D7 600 |
| Eligible within 150 m (≤) | D1, D5, D6 (3); D2 out at 160; D5 exactly on the threshold |
| Sector membership (boundary-inclusive) | D1 → S1 (interior); D5 → S1 only (on S1's north edge, y = 800 — touches one sector, so under W-1 its `assign_rule` is `INTERIOR`-by-containment rather than `BOUNDARY_TIEBREAK`; the log should still note it is a boundary point of the study area); D6 → none (y = 850 is outside both) |
| Question 1 report | S1 = 2 (D1, D5), S2 = 0 (zero-count, kept), unassigned = 1 (D6); identity 2 + 0 + 1 = 3 |
| K1 buffer 150 m, round | rectangle 450…750 × 0…800 = 240,000 m² plus a full circle π·150² = 70,685.83 m² → 310,685.83 m² (segmented arcs slightly less); extent x 450…750, y −150…950 |
| Question 2 | S1 ∩ buffer = 240,000 m² (both round ends lie outside the sectors) = **25.0 %** of S1's 960,000 m²; S2 ∩ buffer = 0 m² = 0 % (buffer's x-max 750 < 1200). Sequence: buffer → intersect with sectors (or clip per sector) → recompute area → divide by sector area recomputed from geometry |
| Sequence justification (Q1) | filter → Near (or buffer + select) → spatial join (sectors as target, keep all) → reverse join for reconciliation; **rejected**: intersect buffer with sectors for Q1 (no consumer), dissolve (single canal, nothing to group) |
| Sensitivity (examples) | 160 m → adds D2; flat ends → drops D6; strict interior → D5 becomes unassigned (S1 = 1) |
| Misleading alternative | "Flood-risk zone": the corridor is a straight-line distance from a centreline; no hydrology, levels, or capacity in the inputs. "Risk ranking by raw count": 2 vs 0 counts three eligible requests over one week in sectors of equal area with no exposure, severity, or population denominator; a count is a count. Honest names: "eligible drainage requests within 150 m of canal K1 (≤), 1–6 Sep 2026" and "sector area within 150 m of K1" |

## I.4 Common mistakes and remediation

| Mistake | Symptom | Remediation |
| --- | --- | --- |
| Tool-first thinking | Worksheet begins "1. Buffer" with no question | Return to 11.1.1; make the learner classify the output kind before naming any tool |
| Buffer in degrees | Enormous or hair-thin polygon; `BUFF_DIST` 300 on a WGS 84 layer | Chapter 6 reprojection first; show the `BUFF_DIST` field and the layer's Source tab side by side |
| Proximity presented as accessibility | "reachable", "response zone" in the interpretation | S1 remediation; require the four-sentence reply; critical misconception |
| Selection reported as clip | "R1 in Ward A is 2,000 m" | 11.3.2; have the learner draw the two outputs |
| Stale measurements delivered | `length_m = 2000` on the 1,000 m clipped road | 11.3.3; recompute on a copy or rename |
| Summing copied attributes | 4,000 m of road across two wards | 11.4.3; require an allocation rule in the worksheet; critical misconception |
| Reading QGIS dissolve fields | "Z1 has 1,200 households" | Q5; show the documentation sentence; use statistics |
| Inner join hides zeros | Ward C missing | Keep All Target Features; 11.6.2; critical misconception (dropped records) |
| Unreconciled sums | "6 requests near the road" when there are 5 | 11.6.3; reverse join; critical misconception |
| NoData as zero | Mean 17.78 or 12.75 | 11.7.1; add the "n of N" line to every raster statistic |
| Mask called risk | "Flood zone" | S2; critical misconception |
| Combining unaligned rasters | Output values differ from both inputs | 11.7.3; show the 50 m-offset copy; Snap Raster |
| History treated as documentation | Log is a paste of parameters, no reasons, no source edition | 11.8.3; require the seven items |

## I.5 Fresh exercise for retesting (different values, same concepts)

Shift the Canal District fixture by (+400, +200) — sectors S3 = (400, 200)–(1600, 1000) and S4 = (1600, 200)–(2800, 1000) — rename the requests E1–E7, and move the canal onto the **shared sector boundary**: K2 from (1600, 200) to (1600, 1000). Set the threshold to 120 m. Place one request exactly on x = 1600 inside the threshold (for example E5 at (1600, 600): distance 0, touching both sectors) so that the BOUNDARY_TIEBREAK rule of Policy W-1 is exercised; place one beyond the canal's north end (for example E6 at (1660, 1080): nearest point is the endpoint (1600, 1000), distance √(60² + 80²) = 100 m, outside both sectors); keep one CLOSED and one outside the date window. Recompute every expected value by hand before use — distances, the on-threshold case, the endpoint case, the buffer area (rectangle 800 × 240 = 192,000 m² plus a circle of radius 120 m = 45,239 m²), the per-sector clipped areas (each sector gets a 120 m × 800 m strip = 96,000 m² = 10 % of 960,000 m²) — and keep the key with this appendix. Keep the "misleading alternative" item but change its wording (for example "the corridor is the maintenance-priority zone").

## I.6 Building and verifying the lab in software — not execution-tested

Nothing below has been run by the author. Record the application versions and the observed values in I.8.

1. **Build the packages.** Planar: the three CSVs of 11.9.3 → `Chapter11_Planar.gpkg` (or a file geodatabase) with an *undefined* coordinate system, by the Chapter 3 I.5 routes (GDAL, QGIS delimited-text export, or ArcGIS Pro XY Table To Point with the Coordinate System cleared). E11: reuse Chapter 6's `wards_e6.geojson`; build `roads_e11` from the WKT into the same package with CRS EPSG:32643; leave `requests_e11.csv` as a CSV for the learner's XY Table To Point step. Confirm record counts (3/1/6; 2/1/5), geometry types, and that `roads_e11`'s stored vertices are exactly the fixture values.
2. **Buffer on an undefined CRS.** Confirm how ArcGIS Pro's Buffer accepts the distance on the planar package (unit Unknown versus "300 Meters") and what `BUFF_DIST` shows; confirm QGIS's Buffer on the same layer. Record the observed buffer areas and the segment settings.
3. **Boundary semantics.** With P5 and Q1, confirm that Spatial Join's Contains gives A 2 / B 2 (boundary-inclusive, as the Select By Location graphic-examples page documents [X12]) and that Completely contains and Contains Clementini give A 1 / B 1; and, in QGIS, "contains" in Join attributes by location (summary) [Q03] and Count points in polygon [Q04]. Note that the Spatial Join page's one-line description of Contains Clementini is phrased relative to *Completely contains* [S31] while the Select Layer By Location and graphic-examples pages phrase it relative to *Contains* [X01] [X12] — for a point the three readings coincide; record what each *tool* actually does. Also confirm the `Join_Count` value for an unmatched target (expected 0).
4. **Exact-threshold behaviour.** Record whether Select By Location Intersect with the 300 m buffer picks up P1 (y = 200 on the straight edge) and whether QGIS's Select by location does; record `NEAR_DIST` for P1 (expected exactly 300).
5. **Intersect of the two wards.** Confirm that Intersect(A, B) with default Output Type returns no features and that Output Type = LINE returns the shared edge (1,000 m) [S33]. Confirm Clip with a multi-polygon clip layer: one multipart record or one per part (11.4.3 verification item).
6. **Date literals.** Record the working `reported_on` condition for the built workspace type (file geodatabase / GeoPackage / QGIS delimited text) and whether the loader typed the column as date or text.
7. **Roads_e11 loading in ArcGIS Pro.** Confirm the route by which learners obtain the line (pre-built in the package, or typed in an edit session) and adjust step 12.
8. **Raster module (optional demo).** Load `grid_a3.asc` and `elevation_training.asc`; confirm Calculate Statistics / Raster layer statistics give mean 20.0 and 13.6; build the ≤ 12 mask with Con [X06] (requires an extension — confirm entitlement) and with the QGIS raster calculator [Q06]; record what the NoData cell shows in each output.
9. **Run both routes end to end** and enter observed values beside every prediction in Tables 11.9-A/B and the Canal District table; explain any difference by parameter, predicate, tolerance, or fixture before issuing the lab.

## I.7 Blueprint corrections, source notes, and verification items

- **No factual error was found in the blueprint's Chapter 11 text or its Appendix A.2/A.3 values.** All hand-checks agree with Appendix A: 300 m boundary-inclusive → P1, P2, P3, P5, P6; boundary-inclusive membership → six matches, five distinct; A.3 mean 20 versus 17.78.
- **Chapter 10 dependency.** Chapter 10 was completed by another author on the drafting date; this chapter's Policy W-1 is Chapter 10's 10.7.2 example policy with its field names (`assigned_ward`, `assign_rule` ∈ {INTERIOR, BOUNDARY_TIEBREAK, OUTSIDE}). Chapter 10's Fixture F10-1 status vocabulary (Open / Reopened / In progress / closed) differs from F11-1's OPEN / CLOSED, which is a deliberate simplification stated in the fixture section. If either chapter's policy wording changes, update the other and I.8.
- **Source availability.** The Select Layer By Location *tool* page links to a graphic-examples page whose `help/mapping/navigation/…` URL returned "page not found" on the check date; the same content is published at `tool-reference/data-management/select-by-location-graphical-examples.html` [X12], which was read and is cited for boundary behaviour.
- **Esri page wording to watch.** The Spatial Join page describes Contains Clementini relative to *Completely contains* [S31]; the Select Layer By Location and graphic-examples pages describe it relative to *Contains* [X01] [X12]. For point join features the three variants' outcomes coincide (a point on the boundary is matched only by Contains), so the chapter's answers do not depend on the wording; instructors should still record the observed result rather than quote either sentence as settled.
- **Software versions.** Esri "latest" pages carry no visible release number in the fetched text; earlier chapters recorded 3.7 on the same date. QGIS 3.44 is the pinned edition (Chapter 6 used 3.40 pages, which now show an end-of-life banner — future revisions of Chapter 6 should move to 3.44).
- **Verification items (collected).** Buffer distance units on undefined-CRS inputs (ArcGIS Pro and QGIS); QGIS Buffer unit on geographic layers; boundary-point behaviour of Contains / Completely contains / Contains Clementini and of QGIS "contains" and Count points in polygon; `Join_Count` for unmatched targets; default merge rule of Join one to one; Intersect(A, B) default and LINE outputs; Clip with multi-polygon clip layers; exact-threshold selection of P1; QGIS raster calculator's treatment of NoData in a conditional expression; which QGIS algorithm gives perpendicular point-to-line distance; date-column typing by each loader; `roads_e11` loading route in ArcGIS Pro; Calculate Statistics' exclusion of declared NoData without the Ignore Values option.

## I.8 Instructor change log

| Date | Change | Affected sections | Rechecked |
| --- | --- | --- | --- |
| 2026-09-19 | Initial draft from blueprint revision 1.0; sources read on this date; no software execution | all | — |
| (fill in) | Observed values from I.6 entered; verification items resolved | 11.2.1, 11.6.1, 11.9, I.3 | Tables 11.9-A/B, Canal District key, M.5 validation cases |
| 2026-09-19 | Policy W-1 aligned with the completed Chapter 10 document (10.7.2 field names); boundary behaviour re-cited to the graphic-examples page [X12] | fixture section, 11.6, 11.9, I.6, I.7 | I.2 Q6, S1 |

---

# Media Appendix

## M.1 Slide outline (mapped to module IDs)

| # | Module | Slide title | Content | Question before the answer |
| --- | --- | --- | --- | --- |
| 1 | — | Chapter 11 — from questions to outputs | Five operations + one raster calculation; the discipline: specify, predict, run, check, record | "Which of these makes something new?" |
| 2 | 11.1 | Write the specification first | The seven items; running example | "What is the *output kind* of 'how many per ward'?" |
| 3 | 11.1.2 | Four kinds of answer | Selection / new geometry / transferred attributes / summary table | "Store within 1 km of a competitor — which kind?" |
| 4 | 11.1.3 | The worksheet, and the row that goes | Six-row draft; step 3 struck through with "no consumer" | "Who reads the corridor polygon?" |
| 5 | 11.2.1 | Buffer: four parameters | Distance/units; end type; method; dissolve type — one visual each | "Round or flat: which changes P6?" |
| 6 | 11.2.2 | R1 buffered by 300 m | Figure M.3-1; area 1,482,743 m²; "not a response zone" | "Where does the polygon poke outside the wards?" |
| 7 | 11.2.3 | Change the threshold | Table of 250/300/400 with the on-edge request highlighted each time | "Which request sits on the edge at 300?" |
| 8 | 11.3 | Clip: cut geometry, copy attributes | R1 → 1,000 m; `length_m` still 2000 | "Select, or clip? Two questions, two answers" |
| 9 | 11.4.1 | Predicate asks, overlay builds | "intersects? → yes" beside "intersection → this line" | "What dimension comes out of line × polygon?" |
| 10 | 11.4.2 | Splitting: two records from one road | Figure M.3-2 | "What does `length_m = 2000` mean on each half?" |
| 11 | 11.4.3 | The copied-attribute trap | Sum of copied field vs recomputed; allocation rules ranked | "Which rule needs no assumption?" |
| 12 | 11.5 | Dissolve = GROUP BY for shapes | Figure M.3-3; Z1 one part, Z2 two parts; statistics naming | "How many features? How many parts?" |
| 13 | 11.5.2 | Dissolve is not append; QGIS first-feature values | The 1,200 vs 1,600 trap | "Which product drops the field, which scrambles it?" |
| 14 | 11.6.1 | Spatial join vocabulary | Target/join; one-to-one/one-to-many; keep all; match option | "Which option counts P5 twice?" |
| 15 | 11.6.2 | Counts per ward — and the three inspections | A 3, B 3, C 0; unmatched P6; sum 6 for 5 | "Why does the sum exceed the request count?" |
| 16 | 11.6.3 | Reconciliation | The requests-as-target table; 4 + 1 + 1 = 6 | "What if it does not balance?" |
| 17 | 11.7.1 | NoData: 20 versus 17.78 | The 3 × 3 grid; three policies | "What does −1093 tell you?" |
| 18 | 11.7.2 | A mask with units — and what it is not | F6 grid with ✓/✗/N; "≤ 12 m above TD-0"; not flood | "What should the NoData cell show?" |
| 19 | 11.7.3 | Before combining rasters | Extent, cell size, alignment, missing data; the 50 m-offset copy | "What does the software do if you skip the check?" |
| 20 | 11.8.1 | Invariants and spot-checks | The invariants table; "looks right is not a check" | "Which spot-checks exercise the edges?" |
| 21 | 11.8.2 | Change one thing | The sensitivity table | "Which change moved the answer most?" |
| 22 | 11.8.3 | Record enough to rerun | The seven items; history pane is raw material | "What does the history *not* record?" |
| 23 | 11.9 | Lab overview | Part 1 planar; Part 2 E11; deliverables | — |
| 24 | 11.10 | Gate and critical misconceptions | The five critical misconceptions | — |

## M.2 Audio-lesson outline

Pronounce: "buffer", "clip", "intersect" (the operation) and "intersects" (the question — stress the *s*), "dissolve", "spatial join", "NoData" as two words, "EPSG thirty-two six four three". Introduce units explicitly the first time each appears ("three hundred metres, straight-line, on the flat training grid").

1. **Opening (11.1).** State the running question and the seven-item specification aloud. Pause: "Before I go on — is the answer a list, a shape, extra columns, or a count?" Then the four kinds. Describe the worksheet as a table you can hear: "Row three: intersect the buffer with the wards. Consumed by — nobody. Strike it out."
2. **Buffer (11.2).** Describe the figure: "Picture a horizontal road two kilometres long. Now a band three hundred metres above and below it, and at each end a half-circle bulging three hundred metres past the road's tip." Give the area in two parts (rectangle, circle). Pause before each threshold row: "At two hundred and fifty metres — who is on the edge?" State plainly what the band does not mean.
3. **Clip (11.3).** "Same road, and a square ward covering only its western half. Selecting keeps the whole road — two thousand metres. Clipping keeps the western half — one thousand. The attribute table still says two thousand." Pause: "Which is stale?"
4. **Intersect (11.4).** Contrast the two words with a beat between them. Describe the split: "one road in, two records out, each carrying the ward code — and each carrying the old length." Pause before the allocation rules.
5. **Dissolve (11.5).** "Four wards, two zones. Zone one is one rectangle. Zone two is two pieces five hundred metres apart — one feature, two parts." Then the QGIS first-feature sentence, read slowly.
6. **Spatial join (11.6).** Read the per-ward counts, then: "Three plus three is six. There are five requests. Pause — where is the sixth?" Then the reconciliation table row by row.
7. **Raster (11.7).** Read the 3 × 3 grid row by row, naming the missing cell. "Eight cells, one hundred and sixty, mean twenty. Nine cells, mean seventeen point seven eight." Then the mask: read each F6 row as "yes, no, no, no". Say "NoData stays NoData" twice.
8. **Validation and record (11.8).** Read the invariant list as a checklist with a pause after each. Read the seven record items. Close: "The history pane is not your log."
9. **Lab and gate (11.9–11.10).** Detailed clicks stay in the handout; narrate only the sequence and the three checks that matter most: Near distance beats the polygon test at exactly the threshold; keep all target features; reconcile.

## M.3 Diagram specifications

All diagrams are **schematic drawings of the synthetic fixture**, drawn to scale on the training grid (1 unit = 1 m), labelled "synthetic training grid — no CRS". No generated artwork is presented as measured data.

- **M.3-1 — Buffer of R1 (11.2.2).** Wards A and B as squares; R1 as a bold line at y = 500; the 300 m buffer as a light band from y = 200 to 800 with semicircular ends centred at (0, 500) and (2000, 500), radius 300; P1–P6 as labelled dots (P1 and P2 drawn *on* the band's edge, P6 inside the east semicircle, P4 outside). Callouts: "rectangle 1,200,000 m²", "circle 282,743 m²", "extent x −300…2300". A second panel with flat ends, P6 now outside.
- **M.3-2 — Intersect of R1 with the wards (11.4.2).** Left: inputs (R1; A and B). Right: two output lines drawn with a small gap at x = 1000, each with an attribute card: `road R1 | ward A | length_m 2000 (copied) | recomputed 1000` and the B equivalent. Below: the polygon case — buffer ∩ wards as two 1000 × 600 rectangles with the semicircle ends shown dashed and labelled "dropped: no ward".
- **M.3-3 — Dissolve wards to zones (11.5.2).** Four wards with `zone` labels; arrow; two output features: Z1 as one 1000 × 1500 rectangle (shared edge at y = 1000 erased), Z2 as B plus the detached D with a bracket "one feature, two parts". Attribute cards: `zone | COUNT_ward | SUM_households`.
- **M.3-4 — Spatial join and reconciliation (11.6).** Wards A, B, C with P1–P6; arrows from each request to the ward(s) it touches; P5 with two arrows; P6 with none and a label "unmatched — invisible in the wards-as-target output". Beside it, the reconciliation table.
- **M.3-5 — NoData policies (11.7.1).** The 3 × 3 grid three times: centre cell shown as a hole (mean 20, "8 of 9"), as a 0 (mean 17.78), as −9999 (mean ≈ −1093, drawn off-scale). Colour legend must show NoData as *absence*, not as a colour in the ramp.
- **M.3-6 — Mask on F6 (11.7.2).** The 4 × 4 grid with ✓/✗/N; area callout "6 cells = 60,000 m²"; title text "elevation ≤ 12 m above TD-0 — NOT a flood map".
- **M.3-7 — Alignment (11.7.3).** R11-A's grid and a copy offset by 50 m, overlaid, showing every cell straddling two; a snap-raster arrow pulling the copy's corner to (1000, 700).

## M.4 Table for slides — the five operations at a glance (from 11.1.2–11.6)

| Operation | Output kind | Geometry out | Attributes out | Records | The check that catches most errors |
| --- | --- | --- | --- | --- | --- |
| Buffer | new geometry | polygon (always) | input's + `BUFF_DIST`/`ORIG_FID` (ArcGIS Pro, no dissolve) | one per input (or per group / one) | `BUFF_DIST` and area magnitude |
| Clip | new geometry | same type as input | input's only, unchanged | one per input feature with an inside part | extent inside the clip layer; stale measures |
| Intersect | new geometry + transferred attributes | lowest dimension of inputs | all inputs' | one per overlapping pair — splits | sum of recomputed measures vs copied |
| Dissolve | summary (+ new geometry) | union per group; multipart possible | dissolve fields + statistics (ArcGIS Pro); first-feature values (QGIS) | one per group | parts per feature; statistic field names |
| Spatial join | transferred attributes / summary | target's geometry | target's + join's (or count) | one per target (1:1) or per pair (1:M) | reconciliation identity; zero-count targets present |

## M.5 Interactive and 3D material

**M.5-1 — Interactive 2D operation explorer (recommended; the principal media item).**

- *Learning objective:* predict and then see the effect of each parameter of buffer, clip, intersect, dissolve, and spatial join on the planar fixture, with the attribute table changing beside the map.
- *Objects:* Wards A, B, C (F11-2 without D, plus a toggle to add D); Road R1 (and a toggle for Chapter 3's R2); requests P1–P6 with `status`/`reported_on`; an attribute table panel.
- *Coordinates/units:* the training grid, metres, no CRS; a fixed on-screen scale bar labelled "schematic, planar".
- *Controls:* operation selector; buffer distance slider 0–500 m in 10 m steps with ≤/< toggle; end type round/flat; dissolve none/all; clip-by ward selector; intersect inputs; dissolve field (`zone`) and statistics; spatial join direction, one-to-one/one-to-many, keep-all, match option (boundary-inclusive / strict interior); time-window date picker; a "predict first" mode that hides the result until the learner types a count or ID list.
- *Expected behaviour and validation cases (all hand-checked in this chapter):* buffer 300 m round → area 1,482,743 m² (±0.5 % by segmentation, displayed with its tolerance), flat → 1,200,000; requests inside at 250/300/400 as in 11.2.3; clip R1 by A → 1,000 m with `length_m` shown red as "stale"; intersect R1 × wards → 2 records; buffer × wards → 600,000 + 600,000; dissolve by `zone` (with D) → Z1 one part 1,500,000 m², Z2 two parts 1,250,000 m², SUM 1600/1050; spatial join wards←requests contains → A 3, B 3, C 0 and the reconciliation panel 4 + 1 + 1 = 6; strict interior → A 2, B 2, unassigned P5, P6.
- *Labels:* "synthetic training grid — no CRS"; every count shown with its "distinct" companion; a persistent footer "proximity ≠ travel time".
- *Engine:* the demonstration must state which geometry engine computes the results (or that it uses the chapter's exact hand rules for this fixture), because buffer area and on-edge membership depend on it.

**M.5-2 — Optional 3D view of NoData (11.7.1).** The 3 × 3 grid R11-A as nine columns whose heights are the cell values (0 shown as a flat tile, *not* absent) and the NoData cell shown as a missing column with a dashed outline; a toggle "treat NoData as 0" fills it with a flat tile and the displayed mean changes from 20.0 to 17.78. Labels: "thematic heights — dimensionless values, vertical scale 1 unit = 5 m for display only — synthetic". Validation: the two means. A 2D alternative (Figure M.3-5) conveys the same lesson and is the required fallback; the 3D view is justified only because "missing" versus "zero" is clearer as an absent column than as a colour.

**No other 3D content is proposed.** Buffer, clip, intersect, dissolve, and spatial join are planar operations; a 3D scene would add nothing and would risk implying that the outputs are surfaces.

*End of Chapter 11 document.*
