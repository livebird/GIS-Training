# GIS and ArcGIS Fundamentals — Phase 1

# Chapter 10 — Attribute queries and spatial relationships

## Document metadata

| Item | Value |
| --- | --- |
| Chapter ID | Phase 1, Chapter 10 |
| Title | Attribute queries and spatial relationships |
| Source specification | `GIS_Phase_1_Chapter_Authoring_Blueprint.md`, revision 1.0 (18 September 2026) |
| Document revision | 1.0 — 19 September 2026 |
| Reference-check date | 19 September 2026 (every cited page was opened and read on this date; the reference list records the exact URLs and the version each page identified) |
| Audience | Software developers at LiveBird Technologies who have completed Chapters 1–9 and have no other GIS background |
| Software referenced | ArcGIS Pro ("latest" documentation, whose page metadata identified the release as **3.7** on the check date); ArcGIS Online and the ArcGIS REST API (continuously updated; read on the check date); QGIS Desktop 3.44 User Guide; PostGIS documentation (the manual identified itself as **3.6.5dev** on the check date) and PostgreSQL "current" documentation for the transferable database examples |
| Execution status | **No procedure in this chapter has been executed in ArcGIS Pro, ArcGIS Online, QGIS, or PostGIS by the author.** Every expected result in this chapter is a *hand-checked mathematical prediction* on a synthetic planar fixture. The software steps in module 10.8 and Instructor Appendix I.6 were written from the official pages cited beside them and are marked *not execution-tested*; each names the remaining verification. |
| Data status | Every coordinate, record, name, identifier, cost, ward code, date, and team in this chapter is **synthetic training material**. Nothing describes a real municipality or organisation. The "clerk-entered" ward codes and the duplicated register row were written deliberately to teach join behaviour. |

### How to use this document

Read the modules in order; each one assumes the ones before it. Boxes labelled **Procedure (version-specific)** contain software steps that will age; everything outside those boxes is durable concept. Boxes labelled **Platform note** separate ArcGIS-, QGIS-, or database-specific behaviour from general GIS principles, and boxes labelled **Verification item** tell an instructor what must be checked in the installed environment before a step is treated as fact. Comprehension checks have no answers in the learner text; answers are in the Instructor Appendix, which learners should not open before the progression gate in 10.9.

One habit runs through the whole chapter: **predict before you run**. Every query in this chapter is first written as *input → condition → expected IDs* on a worksheet (introduced in 10.1.3), then executed, then compared. On a six-row fixture this feels slow. On a six-million-row production table it is the only way to know whether a count of 41,206 is right.

---

## Prerequisites

- **Chapter 8 completed.** You can read a data dictionary; you know the difference between unknown (null), zero, and empty text; you know that a business identifier (`P5`, `DR-0042`) is different from a display label and from a storage-generated row ID; and you can explain one-to-many relationships (one asset, many inspections) using primary and foreign keys.
- **Chapter 9 completed.** You know that a dataset can carry defects (duplicate IDs, invalid categories, orphaned related records, unintended gaps), that a defect log records what was found and what was decided, and that this chapter queries *checked* data — a query cannot repair a bad input, it can only reveal it.
- **Chapters 5–6 (coordinate awareness).** You know that every coordinate needs a stated CRS, unit, and axis order; that a metre threshold cannot be applied directly to degree coordinates; and that planar and geodesic measurements differ. This chapter reuses those facts in 10.6; it does not teach projections again.
- **Chapter 3 (geometry vocabulary).** Point, line, polygon, ring, hole, multipart feature, bounding box (extent). Module 10.4 builds on the depot-with-a-courtyard example from Chapter 3.
- Ordinary developer experience with SQL `WHERE` clauses and joins. The chapter starts from that knowledge and adds the spatial part.

## Learning outcomes

By the end of this chapter you should be able to:

| ID | Outcome | Where it is taught | Where it is checked |
| --- | --- | --- | --- |
| LO1 | Tell a filter, a selection, an exported subset, and a modification of source values apart, and predict for each which record count changes and whether the stored data changes. | 10.1 | 10.9 concept Q1 |
| LO2 | Write attribute conditions with equality, ranges, `AND`/`OR`/`NOT`, parentheses, text matching, and null checks; predict which rows a null excludes from *both* a condition and its negation; and verify quoting and date syntax for the actual data source. | 10.2 | 10.9 concept Q2; practical |
| LO3 | Predict the row count of a key-based join with unmatched keys and duplicate keys, and state which unit (requests, assets, inspections, or matched pairs) a count actually measures. | 10.3 | 10.9 concept Q3; scenario Q1; practical |
| LO4 | Name and sketch the spatial relationships intersects, disjoint, contains/within, touches, overlaps, and nearest; distinguish the *predicate* "intersects?" from the *operation* that builds an intersection geometry; and explain why bounding-box overlap is only a candidate test. | 10.4 | 10.9 concept Q4 |
| LO5 | Predict the result for a point strictly inside a polygon, on its boundary, in a hole, and outside, for named predicates in PostGIS (`ST_Contains`, `ST_Covers`, `ST_Intersects`, `ST_Touches`) and for the documented ArcGIS Pro relationship options. | 10.5 | 10.9 concept Q5; practical; oral |
| LO6 | Distinguish "nearest" from "within a distance"; state the search limit, tie rule, unit, boundary inclusiveness, measurement method (planar/geodesic), and 2D/3D behaviour of a distance query. | 10.6 | 10.9 concept Q6; scenario Q2; practical |
| LO7 | Decompose a business question into separately testable attribute and spatial conditions; write a ward-assignment policy covering boundary, overlap, unmatched, and multiple-match cases; and predict the row count and join count of a spatial join for one-to-one and one-to-many settings. | 10.7 | 10.9 scenario Q1–Q2; practical |
| LO8 | Predict, execute, and reconcile strict-interior, boundary-inclusive, and distance queries on the planar fixture, and document a shared-boundary policy that differs from the raw geometric result. | 10.8 | 10.8 lab; 10.9 practical |

## Required materials

- This document and a text editor or spreadsheet for the prediction worksheet.
- The **Chapter 10 fixture** printed below (Tables F10-1 and F10-2, plus the Chapter 1 wards and road, the Chapter 1 assets, and the Chapter 8 inspection history). The lab inputs are given as small CSV files whose full contents are printed in 10.8.2; no download is needed.
- **For the lab, one of:** ArcGIS Pro (Basic licence is enough for every tool named in this chapter — Select Layer By Attribute, Select Layer By Location, Add Join, Spatial Join, and Near are all listed as available at Basic on their tool pages [S31] [X01] [X03] [X04] [X08]); **or** QGIS Desktop (3.44 documentation is cited; the long-term release available to you may differ — record the version you use); **or** PostgreSQL with the PostGIS extension, which is the route whose predicate semantics are most fully documented [S28] [S29] [S30].
- **Not required:** an ArcGIS Online account, publishing, or credits. Where ArcGIS Online and the REST API are mentioned, it is to show how the same concepts appear there for developers; nothing in this chapter asks you to publish.

## Recap of the preceding chapter

Chapter 9 was about *trusting* data before using it. It separated five quality questions — positional accuracy, attribute correctness, completeness, logical consistency, and currency — and asked whether a source is good enough for the decision at hand. It compared ways of capturing data (digitising, coordinate import, GNSS, geocoding, georeferencing a scan) and insisted that each brings its own uncertainty. It taught careful editing on a copy, with snapping used deliberately; it separated an *invalid* geometry (a self-intersecting ring) from a *valid* geometry that breaks a *business rule* (two wards that overlap); and it required a defect log — symptom, affected records, likely cause, evidence, proposed correction, reviewer decision — for every problem found. Its lab inspected a deliberately damaged copy of the training data and produced a defect log, a corrected copy, and a list of unresolved issues.

Two lessons from Chapter 9 matter directly here. First, the data this chapter queries is the *checked* data: the duplicate IDs and orphaned inspections have been logged and decided, so a surprising query result now points at a semantic question (what does "inside" mean?) rather than at a data defect. Second, Chapter 9's warning that "proximity alone is not evidence" returns in 10.6: a query can tell you two records are 11 m apart; it cannot tell you they describe the same thing.

*Instructor note:* this recap follows the blueprint's Chapter 9 description; align it with the Chapter 9 document's actual fixture names once that chapter is issued.

## The recurring scenario and the Chapter 10 fixture

The fictional municipality continues: **assets** (streetlights, drains, trees), **road** centrelines, **wards**, **service requests** from the public, and **inspections** recorded by **teams**. Everything in this chapter sits on the flat, metre-based training grid of Chapters 1 and 3 — coordinates are written **(x, y)**, x to the right and y upward, **no Earth location and no coordinate-system identifier**. Distances on this grid are ordinary Euclidean arithmetic. When the chapter needs Earth-referenced coordinates (10.6.2), it says so and switches to Chapter 6's Fixture E6.

**Reused unchanged.** Ward A is the square (0, 0)–(1000, 0)–(1000, 1000)–(0, 1000); Ward B is (1000, 0)–(2000, 0)–(2000, 1000)–(1000, 1000); they share the edge x = 1000 for 0 ≤ y ≤ 1000 (Chapter 1, Table F1). Road R1, "Main Road", is the **finite** segment from (0, 500) to (2000, 500) (Table F2). The three assets are SL-0113 at (205, 195), DR-0042 at (995, 510), and TR-0301 at (2190, 520) (Table F4). The inspection history is Chapter 8's Table F8-2 — INS-0001 and INS-0002 on DR-0042, INS-0003 and INS-0004 on SL-0113, none on TR-0301 — repeated in 10.3.2 where it is used.

**New for Chapter 10 — Table F10-1, service requests (synthetic; Chapter 10 variant).** This is Chapter 1's Table F3 with two added columns. `ward_code` is the ward *as typed by the call-centre clerk* when the call came in — it is an ordinary text attribute, entered by a person, and it is deliberately imperfect: P5 was left blank because the caller said "right on the ward line", and P6 carries the code `C`, which no current ward has. `est_cost_inr` is the estimated repair cost in Indian rupees: `NULL` means *not yet estimated*; `0` means *estimated at zero* (P4 was closed as "no fault found"). Dates are `YYYY-MM-DD`; the `closed` column is `NULL` for requests that are not closed.

| request_id | (x, y) | category | priority | status | reported | closed | channel | ward_code | est_cost_inr |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| P1 | (200, 200) | Streetlight out | Medium | Open | 2026-09-02 | NULL | Mobile app | A | 1500 |
| P2 | (800, 800) | Pothole | High | In progress | 2026-08-28 | NULL | Phone | A | 12000 |
| P3 | (1200, 250) | Water leak | High | Open | 2026-09-10 | NULL | Mobile app | B | NULL |
| P4 | (1700, 900) | Pothole | Low | Resolved | 2026-08-15 | 2026-08-20 | Web form | B | 0 |
| P5 | (1000, 500) | Blocked drain | Medium | Reopened | 2026-08-30 | NULL | Phone | NULL | 2500 |
| P6 | (2200, 500) | Fallen tree | High | Closed – duplicate | 2026-09-11 | 2026-09-12 | Phone | C | NULL |

**New for Chapter 10 — Table F10-2, ward register export (synthetic).** The municipality's ward register keeps *history*: when Ward A's boundary was revised in 2024 (the mismatch Chapter 7 discussed in 7.6.3), a new row was added and the old one kept. An export of the register therefore contains **two rows with `ward_code` = A**. This is exactly the kind of table someone joins to requests "to get the ward name" without noticing the duplicate key. The `crew_team` values use Chapter 8's team IDs.

| ward_code | ward_name | crew_team | valid_from | is_current |
| --- | --- | --- | --- | --- |
| A | West ward | T-N | 2019-04-01 | No |
| A | West ward | T-N | 2024-01-01 | Yes |
| B | East ward | T-S | 2019-04-01 | Yes |

**Hand-checked geometric facts** (derived in Chapter 1's Instructor Appendix and re-verified for this chapter in I.3): distances from each request to Road R1 are P1 = 300 m, P2 = 300 m, P3 = 250 m, P4 = 400 m, P5 = 0 m, P6 = 200 m (to the road's end point at (2000, 500), because R1 stops there). P1 and P2 lie strictly inside Ward A; P3 and P4 strictly inside Ward B; P5 lies exactly on the shared boundary *and* on the road; P6 lies outside both wards.

**Diagram D0 (described; specification in the Media Appendix).** The training grid from x = −100 to 2,300 and y = −100 to 1,100 with axes in metres: two outlined squares labelled A and B sharing the edge x = 1000; a thick horizontal line from (0, 500) to (2000, 500) with a visible end cap; six labelled points P1–P6; three asset symbols SL-0113, DR-0042, TR-0301. P5 sits where the road crosses the shared edge. Caption: "Synthetic training grid; metres; no real-world location."

---

## 10.1 Distinguish filter, selection, join, and transformation

### 10.1.1 Four actions that look alike on screen and are not

When you "run a query" in a GIS, one of four different things happens. They look similar — fewer rows appear, some features are highlighted — but they differ in **what changes** and **for how long**, and confusing them causes real damage (an edit meant for six rows applied to six thousand; a report built on a view someone else filtered).

| Action | What you see | What changes in the stored dataset | How long it lasts | Typical GIS name |
| --- | --- | --- | --- | --- |
| **Filter a view** | Only rows/features that meet a condition are drawn and listed | Nothing | Until the filter is removed; saved with the map, not the data | ArcGIS Pro *definition query*; ArcGIS Online *filter*; QGIS *filter* on a layer |
| **Select records** | All rows are still there; the matching ones are highlighted | Nothing | Until cleared; other tools may act on "selected features only" | *Select by attributes*, *Select by location* |
| **Produce an exported subset** | A **new** dataset containing only the matching rows | Nothing in the *source*; a new dataset now exists | Permanent — a copy that will drift from the source | *Export features*, *Extract by …*, `CREATE TABLE … AS SELECT` |
| **Modify source values** | Values in the table change | **The stored data changes** | Permanent unless you restore a backup | *Calculate field*, `UPDATE … SET …`, editing |

A **filter** limits what a *layer* retrieves; in ArcGIS Pro a definition query "affects not only drawing, but also which features appear in the layer's attribute table and can be selected, labeled, identified, and processed by geoprocessing tools" [X05]. That last clause is the dangerous one: a tool run on a filtered layer silently operates on the filtered rows only. ArcGIS Online filters likewise "present a focused view of a feature layer" by "limiting the visibility of features" [X12] — a presentation setting saved in the web map, not a change to the hosted data.

A **selection** is a *set membership*: each record is either selected or not. It does not hide anything. Its power is that many tools honour it — ArcGIS Pro's Calculate Field, "when used with a selected set of features … will only update the selected records" [X06]. Its danger is the same sentence read the other way: if you *forgot* a selection was active, your calculation updated a subset; if you *thought* one was active and it was not, you updated everything.

An **exported subset** is a new file or table. Nothing happens to the source, but the copy is now a separate dataset that will go stale (Chapter 7's provenance lesson). ArcGIS Pro's Export Features "converts a feature class or feature layer to a new feature class" [X07].

A **modification** is the only action that changes stored values. ArcGIS Pro's Calculate Field page carries the explicit caution "This tool modifies the input data" [X06]. In SQL the corresponding statement is `UPDATE`. Chapter 9 already told you to do this on a copy with a log; this chapter's point is that the *same condition* you wrote for a harmless filter becomes destructive the moment it drives an update.

**Developer analogy.** A filter is a `WHERE` clause on a *view*; a selection is a boolean flag held in the client's session state; an export is `SELECT … INTO new_table`; a modification is `UPDATE`. The analogy holds well. Where it stops: in a GIS the *same dialog* often offers all four (a query builder with buttons "Apply", "Select", "Export", "Calculate"), and a selection frequently persists invisibly across tools in a way session state in your own code would not.

**Misconception:** "It's just a query; it can't hurt anything." *Consequence:* the query expression is copied from a filter into a field calculation with an active selection nobody noticed; 5,994 rows are overwritten. The condition was right. The *action* was wrong.

### 10.1.2 Predict counts and source changes before you execute

Before running anything, write down two predictions:

1. **How many records will the result contain**, and which IDs? On the fixture this is a hand count; on production data it is an order-of-magnitude estimate plus a spot check of two or three records you know.
2. **Will the stored data change?** For a filter, selection, or export the answer must be *no*. If your plan says *yes*, stop and confirm you have a copy and a log (Chapter 9, 9.7).

**Worked example 10.1-A.** *Question:* "Show me the open requests." *Input:* Table F10-1. *Condition:* `status = 'Open'` (the Chapter 1 definition of "unresolved" was wider — `Open` or `Reopened` — and 10.2 returns to that). *Predicted IDs:* P1, P3. *Predicted count:* 2. *Source change:* none (this is a filter). After running the filter you see two rows. Had you seen three, the first suspect is not the software but your condition: did the data owner mean `Reopened` counts as open? Had you seen two rows *and the table now has only two rows when the filter is removed*, you did not run a filter — you ran a delete or an export over the source.

**Comprehension check 10.1.2.** A colleague runs "Select by attributes" with `priority = 'High'` on the requests layer, then runs Calculate Field to set `assigned_team = 'T-N'`, then clears the selection. Predict: (a) how many rows now carry `T-N`, (b) whether the stored data changed, and (c) what the colleague should have written down before step two.

### 10.1.3 The worksheet: input → condition → expected IDs

Keep this worksheet visible for the rest of the chapter and for the lab. Each row is one query. The *expected IDs* column is filled **before** execution; the last two columns after.

| # | Input (table/layer, row count) | Condition (exactly as written) | Action (filter / select / export / modify) | Expected IDs (before running) | Result IDs (after running) | Match? Explanation if not |
| --- | --- | --- | --- | --- | --- | --- |
| W1 | F10-1 requests, 6 | `status = 'Open'` | select | P1, P3 | | |
| W2 | | | | | | |

Two rules. First, write the condition **exactly** as you will type it, quotes and all — 10.2.3 shows why the quoting itself is source-dependent. Second, when the result does not match, the explanation must name one of four causes: *my prediction was wrong* (arithmetic or misread fixture), *the condition means something different in this engine* (semantics — 10.5), *the geometry or CRS is not what I assumed* (10.6), or *the fixture was built differently from its definition* (10.8.6). "The software is weird" is not an explanation.

---

## 10.2 Build attribute conditions

### 10.2.1 The building blocks, on the training schema

An **attribute condition** is a true/false test on a record's columns. GIS software wraps it in a query builder, but underneath it is a SQL `WHERE` clause — ArcGIS Pro's query builder, definition queries, and selection tools all use "SQL syntax" [S27], and QGIS's expression engine offers the same operators in its own dialect [X25]. The blocks are the ones you already know; the table applies each to Table F10-1 with a hand-counted result.

| Block | Example on F10-1 | Expected IDs | Count | Note |
| --- | --- | --- | --- | --- |
| Equality | `status = 'Open'` | P1, P3 | 2 | Text is compared as typed; see 10.2.3 on case |
| Set membership | `status IN ('Open', 'Reopened')` | P1, P3, P5 | 3 | Same as `status = 'Open' OR status = 'Reopened'` [S27] |
| Range | `est_cost_inr BETWEEN 1000 AND 3000` | P1, P5 | 2 | `BETWEEN` includes both ends [S27] [X21]; P4 (0) and the nulls are out |
| Comparison | `est_cost_inr < 5000` | P1, P4, P5 | 3 | **P4 qualifies: zero is a value.** P3, P6 are unknown, not small |
| Date comparison | `reported >= <1 September 2026>` | P1, P3, P6 | 3 | How the date is written is source-dependent — 10.2.3 |
| `AND` | `priority = 'High' AND status = 'Open'` | P3 | 1 | Both must hold |
| `OR` | `priority = 'High' OR status = 'Open'` | P1, P2, P3, P6 | 4 | Either may hold |
| `NOT` | `NOT status = 'Open'` | P2, P4, P5, P6 | 4 | Complement *within the non-null rows* — 10.2.2 |
| Parentheses | `priority = 'High' AND (status = 'Open' OR status = 'Reopened')` | P3 | 1 | Compare the next row |
| Missing parentheses | `priority = 'High' AND status = 'Open' OR status = 'Reopened'` | P3, P5 | 2 | `AND` binds before `OR` under "standard operator precedence rules" [S27], so this reads `(High AND Open) OR Reopened` |
| Text pattern | `category LIKE '%tree%'` | **P1, P6** | 2 | `%` matches any run of characters [S27]. "S**tree**tlight out" contains `tree`. Pattern matching is literal, not semantic |
| Null check | `closed IS NULL` | P1, P2, P3, P5 | 4 | The only correct way to find missing values — 10.2.2 |

Read the `LIKE` row twice. A developer who "knows" that `%tree%` finds tree requests has just pulled in a streetlight. Pattern tests are cheap to write and cheap to get wrong; on real data, check a sample of the matches, not just the count.

**Worked example 10.2-A — turning a sentence into a condition.** *Question:* the policy owner asks for "high-priority requests that are still unresolved". *Inputs:* Table F10-1. *Assumptions to state:* "unresolved" = status `Open` or `Reopened` (Chapter 1's definition; `In progress` is deliberately excluded because a crew is already assigned — if the owner disagrees, the condition changes, not the data). *Condition:* `priority = 'High' AND status IN ('Open', 'Reopened')`. *Reasoning:* High → P2, P3, P6; unresolved → P1, P3, P5; both → P3. *Answer:* P3, count 1. *Check:* P2 is High but In progress (excluded by status); P5 is Reopened but Medium (excluded by priority); P6 is High but closed as a duplicate. *What it does not establish:* that P3 is the *only* urgent job — P5 is a reopened blocked drain; priority labels were typed by clerks (Chapter 8's "controlled value is not an observation").

**Comprehension check 10.2.1.** Without parentheses, what does `status = 'Open' OR status = 'Reopened' AND priority = 'High'` return on F10-1? Write the expected IDs. Then add parentheses so that the priority test applies to *both* statuses, and write that version's expected IDs.

### 10.2.2 `IS NULL` is not equality, and unknown is not zero

Chapter 8 taught that a null means *unknown or not recorded*, which is a different fact from zero and from empty text. Queries make this concrete, because a comparison with an unknown value is itself unknown — and an unknown never passes a `WHERE` test.

Take `est_cost_inr` in Table F10-1: 1500, 12000, NULL, 0, 2500, NULL. Here is the truth-table-style view the blueprint asks for, with the rows of the fixture as the cases:

| request | est_cost_inr | `est_cost_inr >= 5000` | `NOT (est_cost_inr >= 5000)` | `est_cost_inr < 5000` | `est_cost_inr IS NULL` |
| --- | --- | --- | --- | --- | --- |
| P1 | 1500 | false | **true** | **true** | false |
| P2 | 12000 | **true** | false | false | false |
| P3 | NULL | *unknown* | *unknown* | *unknown* | **true** |
| P4 | 0 | false | **true** | **true** | false |
| P5 | 2500 | false | **true** | **true** | false |
| P6 | NULL | *unknown* | *unknown* | *unknown* | **true** |
| **rows returned** | | **1** (P2) | **3** (P1, P4, P5) | **3** (P1, P4, P5) | **2** (P3, P6) |

Three things to notice.

1. **A condition and its negation do not add up to the table.** `>= 5000` returns 1 row, `NOT (>= 5000)` returns 3, and the table has 6. The two nulls are in *neither* result. A developer who computes "expensive = 1, so cheap = 6 − 1 = 5" has silently counted two unknowns as cheap.
2. **Zero passes `< 5000`.** P4's cost is *known* to be zero, so it is a small cost. Whether "zero-cost" belongs in a "cheap repairs" report is a business question; the query is doing exactly what was asked.
3. **`= NULL` is not the test.** In PostgreSQL, "ordinary comparison operators yield null (signifying 'unknown'), not true or false, when either input is null. For example, `7 = NULL` yields null, as does `7 <> NULL`" [X21]. QGIS documents the same behaviour for its expression engine: `5 = NULL → NULL` and `NULL = NULL → NULL` [X25]. ArcGIS states the rule as syntax: "The NULL keyword is always preceded by IS or IS NOT" [S27]. The test is `est_cost_inr IS NULL` (or `IS NOT NULL`).

The `<>` operator carries the same trap in the other direction. ArcGIS's SQL reference notes that `<>` "will exclude fields with null values" [S27]: `est_cost_inr <> 0` returns P1, P2, P5 — it excludes P4 (which *is* zero, correctly) *and* P3 and P6 (which are unknown, silently). If the question was "requests whose cost is not known to be zero", the condition is `est_cost_inr <> 0 OR est_cost_inr IS NULL` (five rows).

**Platform note — PostgreSQL's null-safe comparison.** PostgreSQL offers `IS DISTINCT FROM` / `IS NOT DISTINCT FROM`, which "act as though null were a normal data value" — `NULL IS NOT DISTINCT FROM NULL` is true [X21]. This is PostgreSQL syntax; do not expect it in an ArcGIS query builder or a shapefile filter.

**Developer analogy.** Null behaves like a three-valued logic (`true`, `false`, `unknown`) where `WHERE` keeps only `true`. If you have used a language whose `null == null` is `true`, unlearn it here: in SQL it is unknown. The analogy stops at *storage*: a shapefile cannot store a null number at all (Chapter 7, [S20]), so the same query on a shapefile export may see a `0` where the geodatabase held a null — and the truth table above collapses.

**Misconception:** "`status <> 'Resolved'` gives me everything that is not resolved." *Consequence:* any request whose `status` is null (a record entered before the status field existed, or an import that lost the value) disappears from the "not resolved" list — the exact records most likely to need attention.

**Comprehension check 10.2.2.** On Table F10-1, predict the IDs and counts for (a) `closed IS NULL AND est_cost_inr IS NOT NULL`, (b) `NOT (closed IS NULL)`, and (c) `est_cost_inr = NULL` as typed. For (c), say what an engine that accepts the syntax would return and why.

### 10.2.3 Quoting, dates, and syntax are decided by the data source

The condition `status = 'Open'` looks portable. Its *pieces* are not, and the blueprint is explicit that no expression should be presented as "universally portable across ArcGIS, QGIS, and PostgreSQL" (10.2.3). What varies:

**Which SQL dialect is applied.** ArcGIS Pro states that "the SQL syntax you use in an expression differs depending on the data source": file-based data (file geodatabases, shapefiles, CSV/text tables, and feature services that use standardized queries) use "the ArcGIS SQL dialect that supports a subset of SQL capabilities"; mobile geodatabases, SQLite, GeoPackage, and Excel use the "SQLite SQL dialect"; and enterprise databases (Oracle, SQL Server, PostgreSQL, SAP HANA, IBM Db2) use "the SQL syntax of the underlying RDBMS", to which ArcGIS passes the expression [S27]. So the *same layer* moved from a file geodatabase to a GeoPackage to a PostgreSQL database can change which date literal is legal.

**How field names are delimited.** Select Layer By Attribute notes that "file geodatabases and shapefiles use double quotes, and enterprise geodatabases don't use field delimiters" [X04] — `"status" = 'Open'` versus `status = 'Open'`. ArcPy provides `AddFieldDelimiters` so that scripts do not hard-code one style [X04].

**Text quoting and case.** String values take single quotes: `STATE_NAME = 'California'`; an apostrophe inside the value is doubled: `'Alfie''s Trough'` [S27]. "Strings are case sensitive in expressions, except when run on geodatabases in Microsoft SQL Server" [S27] — so `status = 'open'` finds nothing in a file geodatabase and finds P1 and P3 in a SQL Server geodatabase. To make a search case-insensitive deliberately, wrap the field: `UPPER(status) = 'OPEN'` [S27]. QGIS separates the two intents into two operators: `LIKE` is case-sensitive and `ILIKE` is case-insensitive ('A' LIKE 'a' → FALSE; 'A' ILIKE 'a' → TRUE) [X25].

**Date literals.** ArcGIS Pro documents different forms per source: in a file geodatabase a date-time field is compared as `Datefield = timestamp 'yyyy-mm-dd hh:mm:ss'`, a date-only field as `DateOnlyField = date '2003-01-08'`, a time-only field as `time '14:35:00'`, and a timestamp-with-offset field as `timestamp '2003-01-08 14:35:00 -08:00'`; mobile geodatabases use SQLite forms such as `JULIANDAY('yyyy-mm-dd')`; shapefiles and other file-based sources use `date 'yyyy-mm-dd'` with no time part [S27]. On a hosted feature layer queried through the REST API, the documented standardized form is `field = TIMESTAMP '2015-02-09 13:00:00'`, the clause is issued in the layer's date-field time zone, and "the query operation always returns date values in UTC" [X11] — Chapter 8's time-zone convention (event time stored in UTC, IST = UTC+05:30) is the reason that sentence matters.

**Wildcards.** ArcGIS's `LIKE` uses `%` (any number of characters, including none) and `_` (exactly one) [S27]; QGIS's `LIKE`/`ILIKE` use the same two and `\` to escape them [X25]. Older Esri file formats used `*` and `?`; the current SQL reference documents only `%` and `_`, so treat `*` as unverified for any source you have not tested.

**Procedure (version-specific) — where to type the condition.** In **ArcGIS Pro 3.7**, a definition query is built on the layer's *Data* tab → *Definition Query* group → *Build Definition Query*, in "designer mode (using clauses), editor mode (using SQL code), or a query expression file (.exp)"; the value menus "are specific to the underlying source data" [X05]. A selection is made with *Select Layer By Attribute*, whose *Selection Type* offers New selection, Add to, Remove from, Select subset from, Switch, and Clear the current selection [X04]. In **QGIS 3.44**, *Select by expression* and the layer *Filter* dialog use the expression engine documented in [X25]. In **PostgreSQL/PostGIS**, the condition is the `WHERE` clause of an ordinary `SELECT`. In the **ArcGIS REST API**, it is the `where` parameter of the layer's `query` operation, which accepts "SQL-92 WHERE clause syntax on the fields in the layer … for most data sources", with restrictions on some sources [X11].

**Verification item.** Before issuing any expression in a lab handout, run it once against the *actual* installed source and record the source type and the exact literal that worked. A date literal copied from this page into a GeoPackage layer may fail, correctly, because the dialect differs.

**Comprehension check 10.2.3.** The condition `"reported" >= date '2026-09-01'` works on a shapefile. A colleague copies it, unchanged, to a PostgreSQL enterprise geodatabase layer and to a GeoPackage layer. For each, say what you must check before trusting either a result or an error, and cite the reason in one sentence.

---
## 10.3 Join records by identity

### 10.3.1 Key-based joins, unmatched keys, and duplicate keys

A **join by identity** (an *attribute join*) pairs a row in one table with rows in another table whose key value is *equal*. No geometry is involved; this is the `JOIN … ON a.key = b.key` you already know. GIS software adds one twist: the join is usually made *onto a layer*, so the geometry stays with the left-hand table and the right-hand table's columns are appended to it. ArcGIS Pro's Add Join describes exactly that — "the records in the Join Table parameter value will be matched to the records in the Input Table parameter value. A match is made when the input field and join field values are equal" [X03] — and QGIS's layer join dialog does the same through *Properties ► Joins* [X24].

The two situations that produce wrong counts are **unmatched keys** and **duplicate keys**. Table F10-1 (requests) joined to Table F10-2 (ward register) on `ward_code` shows both at once.

**Worked example 10.3-A — requests joined to the ward register.** *Question:* "attach each request's ward name and crew team." *Inputs:* F10-1 (6 rows; key `ward_code`), F10-2 (3 rows; key `ward_code`, with A appearing twice). *Reasoning, request by request:*

| request | ward_code | Matching register rows | Rows in the joined result |
| --- | --- | --- | --- |
| P1 | A | 2 (2019 row and 2024 row) | **2** |
| P2 | A | 2 | **2** |
| P3 | B | 1 | 1 |
| P4 | B | 1 | 1 |
| P5 | NULL | **0** — a null key never equals anything (10.2.2) | 1 with empty ward columns if unmatched rows are kept; 0 if not |
| P6 | C | **0** — no register row has code C | 1 with empty ward columns if kept; 0 if not |

*Answer:* keeping all requests, the joined result has **8 rows** for **6 requests**; keeping only matches, **6 rows** for **4 requests**. *Check:* 2 + 2 + 1 + 1 = 6 matched rows, plus P5 and P6 = 8; four distinct request IDs appear among the matched rows. *What it does not establish:* that P6 is "in no ward" — geometrically it is outside both (Diagram D0), but the *attribute* join cannot know that; it only knows that `C` is not a key in the register. And it does not establish that P5 is unassignable — the clerk left the code blank; module 10.7 assigns it by geometry and policy.

**Platform note — what each tool does with duplicates.** ArcGIS Pro's Add Join defaults to a one-to-many join operation "when it is supported by the input and join table data source" (same file, mobile, or enterprise geodatabase, or memory workspace) and then warns that the attribute table "has duplicate object IDs"; it offers *Join one to first*, which "will use the first match in the join table, which may result in different matches each time you run the join" [X03]. The default *Keep all input records* is checked: an unmatched input record "is given null values for all the fields being appended"; unchecked, "that record is removed from the resultant output" and its feature is not shown on the map [X03]. QGIS's layer join is documented as strictly one-to-one: "If the join field contains duplicate matching values, only the first fetched feature is picked", and "all the features in the target layer are returned, regardless they have a match" [X24]. QGIS's Processing algorithm *Join attributes by field value* gives the choice explicitly — `0 — Create separate feature for each matching feature (one-to-many)` or `1 — Take attributes of the first matching feature only (one-to-one)`, with the one-to-one option as default and a *Discard records which could not be joined* switch [X23]. In SQL you choose with `LEFT JOIN` versus `INNER JOIN`, and duplicates simply multiply rows.

The consequence: **the same join gives 8 rows in one tool, 6 in a second, and 6 rows with an arbitrary choice of register row in a third** — and all three are behaving as documented. "First match" is the most dangerous, because it hides the duplicate and can pick the 2019 row for P1 and the 2024 row for P2.

**Developer analogy.** A GIS attribute join is a `LEFT JOIN` (keep all) or `INNER JOIN` (matches only) whose result is displayed *as if* it were the left table. Where the analogy stops: a database `JOIN` never pretends to have a primary key on the result, whereas a joined layer still shows an ObjectID column — now duplicated — which downstream tools may not tolerate; Esri recommends exporting the joined layer to a new feature class before further processing for that reason [X03].

**Misconception:** "The register has three wards, so joining it cannot change my request count." *Consequence:* a "requests per crew" report built on the joined table counts P1 and P2 twice for crew T-N. The fix is not in the join tool; it is to join to the *current* register rows only (`is_current = 'Yes'`), which is a Chapter 8 identity policy applied before the join.

### 10.3.2 One asset, several inspections: the flat result

The relationship you modelled in Chapter 8 — one asset, many inspection visits — is the clearest case where a joined row count *should* exceed the entity count. Chapter 8's Table F8-2 is repeated here (times in IST as written in the field notebook; condition 1–5):

| inspection_id | asset_id | visit (IST) | team | condition | defects |
| --- | --- | --- | --- | --- | --- |
| INS-0001 | DR-0042 | 2024-10-15 09:50 | T-S | 3 | 0 |
| INS-0002 | DR-0042 | 2025-11-02 15:05 | T-S | 2 | 2 |
| INS-0003 | SL-0113 | 2026-03-14 10:42 | T-N | 3 | 1 |
| INS-0004 | SL-0113 | 2025-09-14 11:15 | T-N | 3 | 0 |

**Worked example 10.3-B — assets joined to inspections.** *Question:* "list assets with their inspection results." *Inputs:* Table F4 (3 assets; key `asset_id`), F8-2 (4 inspections; foreign key `asset_id`). *Reasoning:* DR-0042 matches two inspections, SL-0113 two, TR-0301 none. *Answer:* keeping all assets, **5 rows** (2 + 2 + 1 unmatched); matches only, **4 rows**. The asset's geometry is repeated on each of its rows. *Check:* the number of rows with a non-empty `inspection_id` equals the inspection count (4). *What it does not establish:* a "current condition" for each asset — the flat result has two condition values for DR-0042 (3, then 2) and two for SL-0113 (3 and 3). Which one is "current" depends on **event time**, not row order and not ID order: SL-0113's latest visit is INS-0003 (2026-03-14), although INS-0004 has the higher number because it was typed in late (Chapter 8, 8.7.1).

This is why Chapter 8 kept inspections in their own table: the flat join is a *report*, produced on demand and discarded, never the storage.

**Comprehension check 10.3.2.** Using the flat asset–inspection result with all assets kept: (a) how many rows have `condition = 3`? (b) What does the average of the `condition` column across all rows measure — and why is it not "the average condition of the municipality's assets"? (c) Write the one-line rule that picks each asset's *current* condition.

### 10.3.3 Decide what you are counting before you sum anything

Every count from a joined table measures **one** of these units, and you must say which:

| Unit | Question it answers | On the asset–inspection join (all kept) | On the request–register join (all kept) |
| --- | --- | --- | --- |
| Left-hand entities | How many assets / requests? | 3 | 6 |
| Right-hand entities | How many inspections / register rows? | 4 | 3 |
| Matched pairs (joined rows with a match) | How many asset–inspection pairings? | 4 | 6 |
| Distinct left entities with ≥ 1 match | How many assets inspected / requests with a ward name? | 2 | 4 |
| Left entities with no match | Never inspected / no ward name resolved | 1 (TR-0301) | 2 (P5, P6) |

Sums inherit the same ambiguity. "Total defects found" = 0 + 2 + 1 + 0 = **3**, summed over *inspections* — correct. "Total estimated cost by crew" summed over the request–register join gives T-N: P1 1500 × 2 + P2 12000 × 2 = **27,000**, when the true figure for the two requests is 13,500 — the duplicate key doubled every rupee. **Do not sum a column that came from the left table over rows multiplied by the right table.** Either sum before joining, or count distinct IDs, or fix the duplicate key first.

**Worked example 10.3-C — the honest version of "cost per crew".** *Question:* "estimated cost of open work per crew, from the clerk's ward codes." *Reasoning:* (1) restrict the register to `is_current = 'Yes'` — now `ward_code` is unique (A → T-N, B → T-S); (2) join requests to it; (3) the unmatched P5 and P6 stay in an explicit "unassigned" group. *Answer:* T-N: P1 1500 + P2 12000 = **13,500**; T-S: P3 NULL + P4 0 = **0 with one cost unknown**; unassigned: P5 2500, P6 NULL. *Check:* 13,500 + 0 + 2,500 = 16,000 = the sum of all known costs in F10-1 (1500 + 12000 + 0 + 2500). *What it does not establish:* anything about the *true* wards — the codes were typed by clerks; 10.7 compares them with the geometric answer and finds two disagreements.

**Misconception:** "Aggregate on the joined table; the GROUP BY will sort it out." *Consequence:* the grouping is right and every number in it is inflated by the duplicate factor. Group-by does not de-duplicate; `COUNT(DISTINCT request_id)` does, and only for counts, not sums.

**Comprehension check 10.3.3.** A dashboard shows "Inspections per team: T-S 2, T-N 2, total 4" and, beside it, "Assets per team: T-S 2, T-N 2, total 4". The municipality has three assets. Explain which number is a unit error and what was actually counted.

---

## 10.4 Introduce spatial predicates visually

### 10.4.1 Six relationships, drawn before they are named

A **spatial predicate** is a true/false test on the *geometries* of two features: does this point lie in that polygon, does this road cross that ward, is this drain near that request. It is the spatial counterpart of the attribute condition from 10.2, and it appears in every platform under slightly different names. Learn the *shapes* first; the names come after.

Every geometry has three parts (the vocabulary of the OGC Simple Features model, which PostGIS, GEOS/QGIS, and Esri's "Clementini" options all build on [X20] [X02]): its **interior** (the inside of a polygon; the line itself minus its ends; a point itself), its **boundary** (a polygon's rings; a line's two end points; *a point has no boundary*), and its **exterior** (everything else). A predicate is a statement about which of these parts of A meet which parts of B. Esri phrases the boundary rule the same way: "the boundary polygon [is] the line separating inside and outside, the boundary of a line is defined as its end points, and the boundary of a point is always empty" [X01].

**Diagram D1 (described; specification in the Media Appendix).** Six small panels on the training grid, each showing Ward A (a square) and one other feature:

| Panel | Relationship | Sketch (on the fixture) | Plain-language test | Documented definition |
| --- | --- | --- | --- | --- |
| 1 | **Intersects** | Road R1 passes through Ward A | Do they share *any* point? | PostGIS: "Geometries intersect if they have any point in common" [S30]; QGIS: "share any portion of space – overlap or touch" [X22] |
| 2 | **Disjoint** | P6 at (2200, 500) and Ward A | Do they share *no* point? | PostGIS: "Geometries are disjoint if they have no point in common"; `ST_Disjoint(A,B) = NOT ST_Intersects(A,B)` [X15] |
| 3 | **Contains / Within** | Ward A and P1 at (200, 200) | Is every point of B inside A, with some of B in A's *interior*? | PostGIS `ST_Contains`: "all points of B lie inside (i.e. in the interior or boundary of) A … and the interiors of A and B have at least one point in common"; `ST_Within(A,B) = ST_Contains(B,A)` [S28] [X13] |
| 4 | **Touches** | Ward A and Ward B along x = 1000 | Do they meet only at boundaries, with no interior in common? | PostGIS: "A and B have at least one point in common, and the common points lie in at least one boundary"; "For Point/Point inputs the relationship is always FALSE, since points do not have a boundary" [X14] |
| 5 | **Overlaps** | Ward A and a hypothetical "old Ward A" square shifted 300 m east | Same dimension, interiors meet, neither covers the other? | PostGIS: "same dimension, their interiors intersect in that dimension, and each has at least one point inside the other" [X16] |
| 6 | **Nearest** | P2 at (800, 800) and the three assets | Which candidate has the smallest distance? — not a yes/no on one pair but a *ranking* over many | Esri Near: "The distance between any two features is calculated as the shortest separation between them" [X09] |

Two of these deserve a second look. *Touches* is what wards A and B do to each other: they share an edge, and nothing else. It is also what P5 does to *each* ward: P5 is on the boundary of A and on the boundary of B, in the interior of neither. *Overlaps* is deliberately picky: a point can never overlap a polygon (different dimensions), and a polygon fully inside another does not overlap it (one covers the other) — PostGIS's example notes that "A Point on a LineString is contained, but since it has lower dimension it does not overlap" [X16]. If your instinct says "P1 overlaps Ward A", the word you want is *within*.

**Worked example 10.4-A — reading the fixture through the six tests.** *Question:* for each pair below, which predicates are true? *Inputs:* Diagram D0. *Reasoning and answer:*

| Pair | Intersects | Disjoint | A contains B | Touches | Overlaps |
| --- | --- | --- | --- | --- | --- |
| Ward A, P1 | true | false | **true** | false | false (dimensions differ) |
| Ward A, P6 | false | **true** | false | false | false |
| Ward A, Ward B | true | false | false | **true** | false (no interior in common) |
| Ward A, Road R1 | true | false | false (R1 extends beyond A) | false | false (dimensions differ) |
| Road R1, P5 | true | false | true (P5 is on R1's interior, not an end point) | false | false |

*Check:* in every row exactly one of *intersects* / *disjoint* is true — they are complements [X15]. *What it does not establish:* how a particular *tool* labels these — 10.5 shows that Esri's "Within" and PostGIS's `ST_Within` disagree on P5.

**Developer analogy.** Predicates are pure boolean functions `f(geomA, geomB) → bool`, with no side effects, and most are symmetric (`intersects`, `disjoint`, `touches`, `overlaps`) while `contains`/`within` are each other's mirror. The analogy holds. Where it stops: the arguments are *sets of points*, and set semantics — interior versus boundary — decide the edge cases in ways an `equals()` on two objects never would.

**Misconception:** "'Contains' and 'intersects' are the same for points — a point is either in the polygon or not." *Consequence:* the boundary case is forgotten, and P5, which *intersects* both wards but is *contained* by neither under the strict definition, either disappears from every ward report or appears in two.

**Comprehension check 10.4.1.** Using Chapter 3's park PK-01 (one multipart feature with one part inside Ward A and one inside Ward B): which of the six predicates are true for the pair (Ward A, PK-01)? Which for (Ward B, PK-01)? State the one fact about multipart features that decides the *contains* answer.

### 10.4.2 "Intersects?" is a question; "intersection" is a construction

The word *intersect* is used for two different things, and Chapter 11 depends on you keeping them apart:

| | Spatial predicate **intersects** | Overlay operation **intersection** |
| --- | --- | --- |
| Input | Two geometries | Two geometries (or two layers) |
| Output | `true` / `false` | A **new geometry** — the shared part |
| Changes the data? | No | Creates new features |
| On the fixture | "Does R1 intersect Ward A?" → true | "What part of R1 lies in Ward A?" → the segment from (0, 500) to (1000, 500), 1,000 m long |
| Where it lives | `ST_Intersects` [S30]; Select By Location *Intersect* [X01]; REST `spatialRel=esriSpatialRelIntersects` [X11] | `ST_Intersection`; the *Intersect* and *Clip* geoprocessing tools — **Chapter 11** |

This chapter uses only the left-hand column. When 10.7 asks "which requests are inside the wards", it wants a list of IDs, not a new layer of clipped shapes. The moment a task needs *the shape of the overlap* — the length of road inside each ward, the area of a park in Ward B — it has left the predicate world and become an overlay, and that is Chapter 11's subject.

**Comprehension check 10.4.2.** A colleague says "I ran an intersect of requests and wards and got seven rows". Ask two questions that determine whether they ran a predicate-based selection or an overlay tool, and say what "seven" would mean in each case.

### 10.4.3 Bounding boxes find candidates; they do not prove intersection

Every feature has a **bounding box** (its *extent*, Chapter 3): the smallest axis-aligned rectangle containing it. Boxes are cheap to compare, so engines test boxes *first* to discard pairs that cannot possibly intersect. PostGIS says of `ST_Intersects`, `ST_Contains`, and their relatives that each "automatically includes a bounding box comparison that makes use of any spatial indexes that are available on the geometries" [S30] [S28]; the exact test runs only on the pairs whose boxes overlap. The REST API even exposes the box-only test as its own relationship, `esriSpatialRelEnvelopeIntersects`, beside the exact `esriSpatialRelIntersects` [X11].

The candidate step is *necessary but not sufficient*, and a **concave** shape shows why.

**Worked example 10.4-B — the L-shaped yard (schematic, synthetic; metres).** A depot yard Y is L-shaped: vertices (0, 0), (300, 0), (300, 100), (100, 100), (100, 300), (0, 300), closing to (0, 0). Its bounding box is 0 ≤ x ≤ 300, 0 ≤ y ≤ 300. Consider a point K at (200, 200).

- *Box test:* 0 ≤ 200 ≤ 300 and 0 ≤ 200 ≤ 300 → **candidate**.
- *Exact test:* the yard occupies the strip 0 ≤ y ≤ 100 for all x in 0–300, and the strip 0 ≤ x ≤ 100 for y in 100–300. K has y = 200 > 100, so it is not in the first strip; K has x = 200 > 100, so it is not in the second. **K is outside Y.**

The box said "maybe"; the geometry said "no". The concave notch is exactly the region where a box-only answer is wrong. On the fixture's *convex* squares the box and the shape coincide, which is why boundary cases (10.5), not box cases, are the interesting ones there.

**Diagram D2 (described).** The L-shape hatched, its bounding box dashed, point K drawn in the un-hatched notch inside the dashed box. Caption: "Box overlap = candidate. Geometry test = answer. Schematic; synthetic grid; metres."

**Platform note — what `EnvelopeIntersects` and `IndexIntersects` are for.** They are *performance* tools for a developer who will apply the exact test afterwards (for example, to fetch a page of candidates from a feature service before refining them client-side). Never report their result as a spatial answer.

**Misconception:** "Its extent overlaps the ward, so it's in the ward." *Consequence:* Chapter 3's park PK-01 has an extent spanning x = 100 to 1500 — a box that covers a kilometre of ground that is not park. Any feature in the gap between its two parts "intersects the park's extent" and is not in the park at all.

**Comprehension check 10.4.3.** Give the coordinates of one point that is inside Y's bounding box *and* inside Y, and one that is inside the box but outside Y other than K. Then say which of the two the box test alone can distinguish.

---

## 10.5 Teach boundary semantics explicitly

### 10.5.1 Four positions, predicted before any software runs

Take a polygon with a hole — Chapter 3's depot DP-01: outer ring (1400, 600)–(1600, 600)–(1600, 800)–(1400, 800), inner ring (courtyard) (1450, 650)–(1550, 650)–(1550, 750)–(1450, 750). Now four points:

| Point | Coordinates | Position | Interior of DP-01? | Boundary of DP-01? | Exterior of DP-01? |
| --- | --- | --- | --- | --- | --- |
| H1 | (1420, 620) | Strictly inside the depot land | **yes** | no | no |
| H2 | (1400, 700) | On the outer ring | no | **yes** | no |
| H3 | (1500, 700) | In the courtyard hole (this is tree TR-0302 from Chapter 3) | no | no | **yes** |
| H4 | (1700, 700) | Outside the outer ring | no | no | **yes** |

The hole row is the one people get wrong. A hole is *not part of the polygon*: the courtyard is exterior, exactly as the street outside is. A point on the hole's ring — say (1450, 700) — is on the polygon's *boundary*, because the inner ring is part of the boundary too.

Now the predictions the blueprint requires, using the PostGIS definitions (the named example; 10.5.3 checks ArcGIS and QGIS separately):

| Point | `ST_Contains(DP-01, pt)` — every point of pt in DP-01 *and* interiors share a point [S28] | `ST_Covers(DP-01, pt)` — every point of pt in DP-01 (interior or boundary) [S29] | `ST_Intersects(DP-01, pt)` — any point in common [S30] | `ST_Touches(DP-01, pt)` — common points only on a boundary [X14] |
| --- | --- | --- | --- | --- |
| H1 inside | **true** | **true** | **true** | false |
| H2 on outer ring | **false** | **true** | **true** | **true** |
| H3 in hole | false | false | false | false |
| H4 outside | false | false | false | false |

Only H2 separates the predicates, and it separates them cleanly: *contains* says no, *covers* says yes, *intersects* says yes, *touches* says yes. Write these four rows on your worksheet *before* opening any software; the lab (10.8) then replays the same logic on P5.

**Comprehension check 10.5.1.** Add a fifth point H5 at (1450, 700), on the courtyard ring. Fill in its row for the four predicates, and say in one sentence why its answers equal H2's.

### 10.5.2 PostGIS as the named example: `ST_Contains` versus `ST_Covers`

PostGIS documents the boundary rule in words that are worth reading exactly, because they are the clearest statement of the OGC "contains" definition you will find in any product manual, and because the PostGIS authors themselves recommend the alternative.

`ST_Contains(A, B)` "Returns TRUE if geometry A contains geometry B. A contains B if and only if all points of B lie inside (i.e. in the interior or boundary of) A (or equivalently, no points of B lie in the exterior of A), and the interiors of A and B have at least one point in common." Then the subtlety: "Because the interiors must have a common point, a subtlety of the definition is that polygons and lines do not contain lines and points lying fully in their boundary." [S28]

`ST_Covers(A, B)` "Returns true if every point in Geometry/Geography B lies inside (i.e. intersects the interior or boundary of) Geometry/Geography A. Equivalently, tests that no point of B lies outside (in the exterior of) A." And the recommendation: "Generally this function should be used instead of ST_Contains, since it has a simpler definition which does not have the quirk that 'geometries do not contain their boundary'." [S29]

So for P5 at (1000, 500) on the shared A/B edge: `ST_Contains(WardA, P5)` is **false** and `ST_Contains(WardB, P5)` is **false** — P5 has no interior point in common with either ward's interior, because a point's only point is its own, and that point is on the boundary. `ST_Covers(WardA, P5)` and `ST_Covers(WardB, P5)` are both **true**. `ST_Within(P5, WardA)` is the converse of `ST_Contains` — "ST_Within(A,B) = ST_Contains(B,A)" [X13] — so it is false as well; `ST_CoveredBy(P5, WardA)` is true [S29].

The PostGIS manual's own example makes the same point with a circle and its ring: `ST_Covers(bigc, ST_ExteriorRing(bigc))` is `t` while `ST_Contains(bigc, ST_ExteriorRing(bigc))` is `f` [S29] — a polygon *covers* its own boundary but does not *contain* it.

**Which one do you want?** Neither is "right". They answer different questions:

| Business question | Predicate | P5's fate |
| --- | --- | --- |
| "Requests that are unambiguously inside one ward" (strict interior) | `ST_Contains(ward, request)` | In **neither** ward — must be handled by policy |
| "Requests that a ward can be responsible for, boundary included" (inclusive) | `ST_Covers(ward, request)` | In **both** wards — must be handled by policy |
| "Requests that touch the ward without being inside" | `ST_Touches(ward, request)` | Touches both — the boundary set, isolated |

Whichever you choose, P5 needs a *policy* (10.7.2); the predicate only tells you which pile it lands in.

**Platform note — three PostGIS cautions that transfer.** (1) "Do not use this function with invalid geometries. You will get unexpected results" [S28] [S29] — Chapter 9's validity checks come first. (2) `ST_Intersects` on the *geography* type "has a distance tolerance of about 0.00001 meters and uses the sphere rather than spheroid calculation" [S30] — a boundary point can be treated as intersecting when it is a hundredth of a millimetre off. (3) The geometry predicates test 2D relationships; Z is ignored (10.6.3).

**Developer analogy.** `ST_Contains` is a *closed-interval-with-open-boundary* test — like `a < x < b` for the boundary combined with `a ≤ x ≤ b` for the body; `ST_Covers` is plain `a ≤ x ≤ b`. Where the analogy stops: in one dimension "the boundary" is two numbers; in two dimensions it is every ring, including holes, so "on the boundary" is a whole family of positions.

**Misconception:** "Every tool that says *contains* means the same thing." *Consequence:* a SQL report using `ST_Contains` drops P5; the same report rebuilt in ArcGIS Pro with the *Contains* option keeps it (10.5.3); the two teams argue about "data quality" when the data is identical and both tools are correct.

### 10.5.3 `ST_Intersects` and the ArcGIS/QGIS options, verified separately

`ST_Intersects` is the most permissive of the family: "Returns true if two geometries intersect. Geometries intersect if they have any point in common", and "Spatial intersection is implied by all the other spatial relationship tests, except ST_Disjoint" [S30]. Boundary contact qualifies — H2 on the outer ring intersects DP-01, P5 intersects both wards and the road. If you want "in or on", `ST_Intersects(polygon, point)` and `ST_Covers(polygon, point)` give the same answer for a point; they differ for lines and polygons that are partly outside (intersects: yes; covers: no).

The blueprint requires that the ArcGIS and QGIS options be verified *separately* rather than assumed to match PostGIS. Here is what each product's documentation says, read on the check date, for the case that matters — a point on a polygon boundary.

**ArcGIS Pro 3.7 — Select Layer By Location and Spatial Join.** Both tools share one set of relationship options and one explanatory page [X02]. The definitions:

- *Intersect* — "The features in the input layer will be selected if they intersect a selecting feature" [X01].
- *Within* — "Selects features in the input feature layer within or contained by features in the selecting features layer" [X02].
- *Completely within* — "The result is identical to the Within option except when the feature in the input feature layer intersects the boundary of the feature in the selecting features layer; then it is not selected" [X02].
- *Within Clementini* — "The result is identical to the Within option except when the entirety of the feature in the input feature layer is on the boundary of the feature in the selecting features layer" [X02]; the tool page adds that "Clementini defines … the boundary of a point is always empty" [X01].
- *Contains* — "The selecting features can be inside as well as on the boundary of the input feature layer"; *Completely contains* — matched "as long as the feature in the selecting features layer does not intersect the boundary"; *Contains Clementini* — identical to Contains except when the selecting feature "is entirely on the boundary" [X02].
- *Boundary touches* — "selected if they have a boundary that touches a selecting feature" [X01].

The page's worked table "Select point using polygon" lists which of its labelled cases each option selects: *Intersect* A, C; *Within* A, C; *Completely within* A; *Within Clementini* A; *Boundary touches* C [X02]. Read together with the definitions, the case that only *Boundary touches* isolates (C) is the boundary point, and it is selected by *Intersect* and *Within* but not by *Completely within* or *Within Clementini*. **So Esri's plain *Within* is boundary-inclusive — it corresponds to PostGIS `ST_CoveredBy`, not `ST_Within` — and the strict, PostGIS-`ST_Within`-like behaviour is *Completely within* (or *Within Clementini* for a point).** The same word means different things in the two products; the documentation of each is internally consistent.

The prediction for P5 in ArcGIS Pro (requests as input, wards as selecting features):

| Option | P1, P2 | P3, P4 | P5 | P6 |
| --- | --- | --- | --- | --- |
| Intersect | A | B | **A and B** | none |
| Within | A | B | **A and B** | none |
| Completely within | A | B | **neither** | none |
| Within Clementini | A | B | **neither** | none |
| Boundary touches | none | none | **A and B** | none |

Two further Esri facts change results without changing semantics. Select Layer By Location "evaluates a spatial relationship in the coordinate system of the Input Features parameter value" and warns that "Features that intersect in one coordinate system may not intersect in another" [X01]. And the tool applies the feature class's **x,y tolerance** on the client — the page notes that the DBMS route runs "without applying an x,y tolerance … This may result in slightly different selections" [X01]. On the fixture the coordinates are exact integers, so tolerance does not bite; on real data, a point 0.5 mm outside a ward may be *inside* under the tolerance.

**QGIS 3.44 — Select by location / Join attributes by location.** The predicate list is `intersect`, `contain`, `disjoint`, `equal`, `touch`, `overlap`, `are within`, `cross`; choosing several combines them with OR [X22] [X23]. The documented definitions: *Contain* "Returns 1 (true) if and only if no points of b lie in the exterior of a, and at least one point of the interior of b lies in the interior of a … This is the opposite of are within"; *Touch* "the geometries have at least one point in common, but their interiors do not intersect"; *Intersect* "share any portion of space – overlap or touch" [X22]. The *contain* definition is word-for-word the OGC/PostGIS `ST_Contains` rule, so a boundary point is *not* contained and therefore not *within*; it *touches* and *intersects*. QGIS therefore lines up with PostGIS, not with Esri's plain *Within*, for the boundary point. There is no `covers` option in the QGIS dialog; "in or on" is expressed as *intersect* (for points) or as *are within* OR *touch*.

**Verification item (both products).** The statements above are read from documentation, not from executed tests. Before the lab is issued: run the P5 case in the installed ArcGIS Pro build with *Within*, *Completely within*, *Within Clementini*, and *Boundary touches*, and in the installed QGIS with *are within*, *touch*, and *intersect*; record the selected IDs beside each option in the lab's Table 10.8-C. A discrepancy is a finding to document, not to hide.

**The transferable rule.** Never trust the *word*; trust the *documented definition* and one *executed boundary case*. Every platform this course targets — PostGIS, QGIS/GEOS, ArcGIS Pro, the REST API's `spatialRel` values [X11] — has a predicate called something like "within" or "contains", and at least two of them disagree on a point on a line.

**Misconception:** "Because P5 is 'on the line', the software will put it in one ward or the other at random, and that's fine." *Consequence:* under strict predicates it lands in *neither* and vanishes from every ward count; under inclusive predicates it lands in *both* and is counted twice. Neither outcome is random, and neither is an assignment. Assignment is a policy decision (10.7.2), recorded in writing.

**Comprehension check 10.5.3.** Chapter 3's tree TR-0302 stands in the depot courtyard hole at (1500, 700). Predict, for the pair (depot DP-01 as selecting/`A`, tree as input/`B`): PostGIS `ST_Covers`, ArcGIS Pro *Within*, ArcGIS Pro *Intersect*, QGIS *are within*. Then explain what an engine that "tested only the outer ring" would wrongly return.

---
## 10.6 Handle distance and dimension carefully

### 10.6.1 "Nearest" and "within a distance" are different questions

Both use distance; they are not interchangeable.

| | **Within a distance** | **Nearest** |
| --- | --- | --- |
| Type of answer | Yes/no *per pair* — a predicate like those in 10.4 | A *ranking* — for each input, which candidate is closest |
| Number of matches per input | 0, 1, or many | Exactly 1 — or 0 if a search limit is set and nothing is inside it — or *ambiguous* on a tie |
| Needs a threshold? | Yes: the distance and its unit | Optional: a search limit (beyond which "nothing is near") |
| Needs a tie rule? | No | **Yes** |
| Typical use | "All requests within 300 m of a road" | "Which drain does this blocked-drain report probably refer to?" |

Each has four things you must write down before running it:

1. **The unit.** Metres on the training grid. On real data it is the *layer's CRS unit* unless the tool says otherwise — PostGIS: "For geometry: The distance is specified in units defined by the spatial reference system of the geometries" [X17]; Esri's Near writes `NEAR_DIST` "in the linear unit of the input feature's coordinate system, or meters when the Method parameter is set to Geodesic" [X08]; the REST API's `distance` parameter takes a `units` value whose *default differs by product* — "esriSRUnit_Foot when querying feature services in ArcGIS Enterprise, and esriSRUnit_Meter when querying feature services in ArcGIS Online" [X11]. A developer who omits `units` gets feet on one deployment and metres on the other.
2. **Whether the threshold is inclusive.** The fixture was built so that P1 and P2 sit at *exactly* 300 m from R1. "Within 300 m" then means either {P1, P2, P3, P5, P6} (≤ 300) or {P3, P5, P6} (< 300). The blueprint's answer uses the boundary-inclusive reading (Appendix A.2.5). **The documentation you have read does not settle this for every tool**: PostGIS's `ST_DWithin` is described as "Returns true if the geometries are within a given distance" [X17] without stating ≤ or <; Esri's *Within a distance* says "within the specified distance (using Euclidean distance)" [X01]. Treat the exact-threshold case as a **verification item** for the installed engine (the lab's Table 10.8-C has a column for it) and, in any production query, avoid depending on it: choose 300.5 m or 299.5 m if the business rule allows, and say so.
3. **The search limit** for a nearest query. Esri's Near sets `NEAR_FID` and `NEAR_DIST` to −1 "if no feature is found within the search radius" [X08]; Spatial Join's *Closest* option accepts a *Search Radius* and writes −1 into the distance field when nothing is within it [S31]; QGIS's *Join attributes by nearest* has a *Maximum distance* parameter: "only features which are closer than this distance will be matched" [X23].
4. **The tie rule.** Esri is explicit and honest: "When more than one near feature has the same shortest distance from an input feature, one of them is randomly chosen as the nearest feature" [X08]; Spatial Join says the same for *Closest* — "one of the join features will be randomly selected as the matching feature (the join feature's object ID does not influence this random selection)" [S31]. QGIS's nearest-join documentation does not state a tie rule at all [X23]. A nearest result with an undocumented or random tie rule is **not reproducible**, which violates Chapter 9's traceability requirement; the fix is either a documented tie-break (lowest ID, earliest date) applied *after* fetching all equal-distance candidates, or reporting the tie.

**How distance to a line is measured.** Esri's rule, which matches ordinary geometry: "The shortest distance from a point to a line segment is the perpendicular to the line segment. If a perpendicular cannot be drawn within the end vertices of the line segment, the distance to the closest end vertex is the shortest distance" [X09]. That is why P6 at (2200, 500) is **200 m** from R1, not 0 m: the perpendicular from P6 would land at (2200, 500), which is *beyond* R1's end at (2000, 500), so the distance is measured to the end vertex. An engine that treated R1 as an infinite line would report 0 — and a report "requests on Main Road" would gain a request that is 200 m past where the road ends. Distance to a polygon is measured to its boundary, and is zero for anything inside it: "when a feature is inside a polygon, the distance between the feature and the surrounding polygon is zero" [X09].

**Worked example 10.6-A — nearest asset to each request (planar, metres, hand-computed).** *Question:* for each request, which of the three assets is nearest, and how far? *Inputs:* F10-1 points, F4 assets. *Reasoning:* Euclidean distance √(Δx² + Δy²) to each asset; keep the smallest.

| Request | To SL-0113 (205, 195) | To DR-0042 (995, 510) | To TR-0301 (2190, 520) | Nearest | Distance (m) |
| --- | --- | --- | --- | --- | --- |
| P1 (200, 200) | √(5² + 5²) = √50 | far | far | SL-0113 | **7.07** |
| P2 (800, 800) | √(595² + 605²) = √720,050 ≈ 848.56 | √(195² + 290²) = √122,125 ≈ 349.46 | far | DR-0042 | **349.46** |
| P3 (1200, 250) | far | √(205² + 260²) = √109,625 ≈ 331.10 | √(990² + 270²) ≈ 1,026.2 | DR-0042 | **331.10** |
| P4 (1700, 900) | far | √(705² + 390²) = √649,125 ≈ 805.68 | √(490² + 380²) = √384,500 ≈ 620.08 | TR-0301 | **620.08** |
| P5 (1000, 500) | far | √(5² + 10²) = √125 | far | DR-0042 | **11.18** |
| P6 (2200, 500) | far | far | √(10² + 20²) = √500 | TR-0301 | **22.36** |

*Answer:* as tabulated; no ties. *Check:* squares of the reported distances reproduce the sums (349.46² ≈ 122,122; 620.08² ≈ 384,499). *What it does not establish:* that P2 (a pothole) "refers to" drain DR-0042 349 m away, or that P1 refers to SL-0113. Nearest is a *geometric* fact; "this report is about that asset" is a *business* link that Chapter 8 modelled as a nullable `asset_id` set by a person. Chapter 9's rule stands: proximity is evidence, not identity.

**Worked example 10.6-B — a tie you already own.** *Question:* "assign each request to its nearest ward." *Reasoning:* P1–P4 are inside a ward, so their distance to that ward is 0 and to the other ward > 0 — unambiguous. P6 is 200 m from Ward B's east edge (x = 2000) and 1,200 m from Ward A — B. P5 is *on* both boundaries: distance 0 to A and 0 to B — a **tie**. *Answer:* A: P1, P2; B: P3, P4, P6; P5: tie. Under Esri's documented rule the tool would pick A or B "randomly" [X08]; a second run may differ. *What it does not establish:* a ward for P5; and note that "nearest ward" has quietly assigned P6, which is *outside* every ward, to B — a nearest query has no notion of "none of the above" unless you give it a search limit.

**Misconception:** "Nearest is just 'within a distance' with the distance set very small." *Consequence:* the distance is set to 10 m to "find the drain a request is about"; P5 finds DR-0042 (11.18 m) — no, it does not, 11.18 > 10; P1 finds SL-0113 (7.07 m); everything else finds nothing. The threshold was arbitrary, and the result is a mixture of two questions.

**Comprehension check 10.6.1.** A new request P9 is reported at (600, 500) — on the road, midway between nothing in particular. Compute its distance to each asset, name the nearest, and then say what a *within 400 m* query returns for P9 with an inclusive and with a strict threshold.

### 10.6.2 Metres are not degrees: use Chapter 6 before you set a threshold

Everything in 10.6.1 was on the planar grid, where a metre is a metre in every direction. The moment the coordinates are latitude and longitude, a distance threshold in "layer units" is a threshold in **degrees**, and the blueprint's rule applies: do not implement a metre threshold directly on degree coordinates without an appropriate method (10.6.2).

Chapter 6's Fixture E6 gives the numbers. Its wards are 0.010° × 0.010° in WGS 84 (EPSG:4326), and at that latitude (about 23° N) 0.010° of *latitude* is about 1,107 m while 0.010° of *longitude* is about 1,025 m (Chapter 6, 6.5.2). So:

- 300 m north–south ≈ 0.00271°, but 300 m east–west ≈ 0.00293°. A "0.003-degree" search distance reaches about **332 m** north–south and about **307 m** east–west — an ellipse on the ground, not a circle, and both figures are wrong for a 300 m rule. Farther from the equator the east–west figure shrinks further (Chapter 6).
- Request Q1 (23.002° N, 75.000° E) to Q3 (23.005° N, 74.995° E): the "distance in degrees" is √(0.003² + 0.005²) ≈ 0.00583°. Multiplying that by the equatorial metre-per-degree figure (≈ 111.32 km/°) gives ≈ 649 m. Planar arithmetic on the *delivered* UTM 43N coordinates (Q1 E 500000.000, N 2543741.163; Q3 E 499487.611, N 2544073.271) gives √(512.389² + 332.108²) ≈ **610.6 m** on the projection plane (about 610.9 m on the ground after the 0.9996 scale factor, Chapter 6). The degree-based figure is off by more than 6 % *for a distance under a kilometre*, and the error is direction-dependent.

The correct options are the ones Chapter 6 taught:

| Option | How the query is written | When it is appropriate |
| --- | --- | --- |
| **Project first, then planar** | Reproject (transform, not assign — Chapter 6) both layers into a suitable projected CRS in metres, then use the ordinary planar predicate: `ST_DWithin(geom_32643, road_32643, 300)`; ArcGIS *Within a distance*; QGIS *Select within distance* | Study area inside the CRS's area of use and distortion acceptable for the rule — true for Fixture E6 in EPSG:32643 |
| **Geodesic distance on the ellipsoid** | PostGIS `geography` type: `ST_DWithin(geog, geog, 300)` with "units are in meters and distance measurement defaults to use_spheroid = true" [X17]; ArcGIS *Within a distance geodesic*, which uses "a geodesic formula that takes into account the curvature of the spheroid" [X01]; Esri Near with *Method* = Geodesic [X08] | Large extents, data kept in a geographic CRS, or rules stated as ground distance |
| **Threshold in degrees** | `ST_DWithin(geom_4326, road_4326, 0.0027)` | **Not appropriate** for a metre rule; only for a rule genuinely stated in degrees |

**Platform note — PostGIS `geography` and `ST_DWithin`.** PostGIS documents `ST_DWithin` as the preferred radius test because it "includes a bounding box comparison that makes use of any indexes", whereas `ST_Distance(a, b) <= x` evaluates the distance for every row [X17] [X20]. On the geography type the intersection test uses "a distance tolerance of about 0.00001 meters" and a *sphere*, not the spheroid [S30]; the distance itself uses the spheroid by default and can be switched to the faster sphere with `use_spheroid = false` [X17] [X18]. These are the "sphere versus ellipsoid" differences Chapter 6 measured on Q1–Q2 (664.46 m geodesic versus 667.17 m spherical).

**Verification item.** Any lab that applies a metre threshold to Fixture E6 must state which of the three options it uses and record the engine, CRS, and method. The chapter's main lab avoids the problem by staying on the planar grid; Instructor Appendix I.5 sketches an optional E6 extension.

**Misconception:** "The layer is in WGS 84, so I'll just set the distance to 300 and pick 'metres' from the unit list." *Consequence:* whether the tool silently converts, warns, or applies 300 *degrees* (a threshold larger than the planet) depends on the product; Esri's 3D page notes that on geographic data "the conversion from decimal degrees to linear units is not consistent across large geographic extents" and recommends running in a projected CRS [X10]. Do not find out on production data.

**Comprehension check 10.6.2.** Fixture E6's Q4 (23.005° N, 75.005° E) is 0.005° east of the shared boundary at 75.000° E. Estimate its ground distance to the boundary in metres using Chapter 6's figures, and explain why a "within 0.005°" query centred on the boundary would find Q4 but a "within 500 m" geodesic query would not.

### 10.6.3 2D or 3D? Z storage does not make a query three-dimensional

Chapter 9's media brief used a bridge crossing a road: from above, two lines cross; in reality one passes over the other and they never meet. The same picture decides how distance and intersection queries treat height.

**The default is 2D.** The OGC-style predicates compare geometries in the x,y plane. PostGIS makes the contrast explicit with a worked example: for a point at Z = 2 and a line whose Z runs from 1 to 3 through the same x,y, `ST_3DIntersects(pt, line)` is **false** while `ST_Intersects(pt, line)` is **true** [X19]; `ST_DWithin` operates in 2D and the manual says "Use ST_3DDWithin for 3D geometries" [X17]. ArcGIS Pro's Select Layer By Location offers *Intersect* and *Within a distance* (2D) alongside the separate options *Intersect 3D* and *Within a distance 3D*, which evaluate "in three-dimensional space (x, y, and z)" [X01]; its 3D page shows rooms stacked in a building being selected by a 2D *Within a distance* "in the x- and y-coordinates only", so floors above and below the point are included, whereas *Within a distance 3D* selects only the rooms within a true 3D distance [X10].

**What follows.** Storing Z on the bridge and the road (Chapter 8's geometry specification may well ask for it) changes *nothing* about `ST_Intersects` or *Intersect*: the bridge still "intersects" the road in 2D, because the 2D predicate never reads Z. To get "they do not meet", you must (a) have Z on *both* features, (b) have Z in a stated vertical reference and unit (Chapter 5, 5.6), and (c) call the 3D predicate — and then accept that a 3D predicate on a road drawn at ground level and a bridge drawn at deck level returns *false* even where the bridge's *pier* stands on the road, because nobody drew the pier.

On the planar training grid nothing has Z, so every query in this chapter is 2D by construction, and the fixture's results are unaffected. The point of this section is to stop you from *inferring* 3D behaviour from the presence of a Z column.

| Question | 2D predicate answers | 3D predicate answers | Which one the business wants |
| --- | --- | --- | --- |
| "Does the bridge cross the road?" (map overlay, drawing order) | yes | no | 2D |
| "Can a vehicle turn from the bridge onto the road here?" | (yes — wrongly) | no (and correctly no connectivity) | Neither alone — a network model, Chapter 1's out-of-scope note |
| "Is this lamp within 5 m of the overhead line?" | yes if x,y within 5 m | only if the 3D separation is ≤ 5 m | 3D, *if* Z is real and referenced |

**Verification item.** Whether the geometry predicates of a given engine honour Z is a per-engine fact. PostGIS and ArcGIS Pro are documented above; for QGIS's *Select by location* the 3.44 documentation read for this chapter does not state a Z policy, so treat it as 2D until tested.

**Misconception:** "Our data has Z values, so 'within 5 m' is a 3D check." *Consequence:* a lamp 4 m away horizontally and 12 m below an overhead line is reported as "within 5 m"; the true 3D separation is √(4² + 12²) ≈ 12.6 m.

**Comprehension check 10.6.3.** A drain DR-0042 is at ground level; a footbridge deck passes directly above it at 6 m. Predict what `ST_Intersects(bridge_line, drain_point)` and `ST_3DIntersects(bridge_line, drain_point)` return if both geometries carry Z, and what both return if the drain's Z is missing (stored as 0 by an import that could not hold a null — Chapter 7). Which of the three answers is a data defect rather than a query result?

---

## 10.7 Combine spatial and attribute logic

### 10.7.1 One sentence, three testable conditions

The blueprint's benchmark question is: *"open requests inside the study area and within the specified distance of a road."* Written as one condition it cannot be checked. Written as three, each can be predicted, executed, and reconciled on the worksheet.

**Worked example 10.7-A.** *Inputs:* Table F10-1 (6 requests), Wards A and B, Road R1. *Assumptions, stated once:* "open" = status `Open` or `Reopened` (Chapter 1's definition); "study area" = the union of the two wards, **boundary-inclusive** (a request on the ward line is the municipality's business); "within" = **≤ 300 m**, boundary-inclusive; distances are planar metres on the training grid; R1 is finite.

| # | Condition | Kind | Predicate / expression | Expected IDs | Count |
| --- | --- | --- | --- | --- | --- |
| C1 | Open | attribute | `status IN ('Open', 'Reopened')` | P1, P3, P5 | 3 |
| C2 | Inside the study area (inclusive) | spatial | request *intersects* (or is *covered by*) Ward A or Ward B | P1, P2, P3, P4, P5 | 5 |
| C3 | Within 300 m of R1 (inclusive) | spatial | distance(request, R1) ≤ 300 | P1, P2, P3, P5, P6 | 5 |
| **C1 ∧ C2 ∧ C3** | | | | **P1, P3, P5** | **3** |

*Reasoning:* intersect the three ID sets. P2 fails C1 (In progress); P4 fails C1 and C3; P6 fails C1 and C2 — it *passes* C3 (200 m to the road's end), which is why the blueprint warns not to "silently add" the study-area condition: without C2, and with a looser status rule, P6 would appear. *Answer:* P1, P3, P5 — the same set Chapter 1's hand exercise produced. *Check:* each of the three IDs satisfies all three rows individually (P5: Reopened; on the boundary, inclusive; 0 m). *What it does not establish:* the result under a *strict* study-area rule — then P5 fails C2 and the answer is P1, P3 — nor under a strict distance rule — then P1 fails C3 and the answer is P3, P5. Three defensible readings, three different lists; the *assumptions line* is what makes the result reproducible.

**Sensitivity table** (write this for any combined query; it is what a reviewer will ask for):

| Change one assumption | Result | Why |
| --- | --- | --- |
| Study area strict (interior only) | P1, P3 | P5 on the boundary is excluded by C2 |
| Distance strict (< 300) | P3, P5 | P1 at exactly 300 m is excluded by C3 |
| "Open" includes In progress | P1, P2, P3, P5 | P2 passes C1, C2 (inside A), C3 (300 m, inclusive) |
| Drop C2 entirely | P1, P3, P5 | P6 passes C3 but fails C1 — the study-area rule was *not* what excluded P6 here; status was. Change status and C2 at once and P6 appears |

**Order of evaluation is yours to choose; the result is not.** In SQL the three conditions are one `WHERE` with `AND`; in ArcGIS Pro you would typically run Select Layer By Attribute (C1), then Select Layer By Location with *Selection Type* = "Select subset from the current selection" for C2 and again for C3 [X01] [X04]; in QGIS, *Select by expression* then *Select by location* / *Select within distance* with "selecting within current selection" [X22]. The counts after each step should be 3 → 3 → 3 if you start with C1, or 5 → 5 → 3 if you start with C2 — write the intermediate counts down; they are your audit trail.

**Developer analogy.** This is short-circuit-free boolean composition over sets: `C1 ∩ C2 ∩ C3`. The analogy holds exactly. Where it stops: GIS tools hold the intermediate result as a *selection* (10.1), which is invisible session state — a colleague opening the project later sees three highlighted points and no record of which conditions produced them unless you wrote them down.

**Comprehension check 10.7.1.** Rewrite the benchmark question for "requests reported in September 2026 that are *not* within 300 m of the road", as separately testable conditions with expected IDs, and state which single assumption most changes the answer.

### 10.7.2 Assigning requests to wards: the policy, not the predicate

A geometric predicate returns *matches*. A **ward assignment** returns *one ward per request* (or an explicit "none"), and it is a business rule that must cover four cases before anyone runs a tool:

| Case | On the fixture | Policy question | Example policy (synthetic; one defensible choice) |
| --- | --- | --- | --- |
| **Interior** | P1–P4 | none — the predicate answer is the assignment | Assign to the containing ward |
| **Boundary** | P5 on x = 1000 | Which ward, or both, or neither? | Assign to the ward with the alphabetically lower code (**A**); record `assign_rule = 'BOUNDARY_TIEBREAK'` |
| **Overlap** (two wards claim the same ground) | none on the fixture — Chapter 9 said wards must not overlap; Chapter 7's 2019/2024 boundary story is how one arises | If overlaps exist, is that a defect or a legitimate shared area? | Treat as a **data defect** (Chapter 9 log); until fixed, assign by the *current* register year and flag |
| **Unmatched** | P6, outside both wards | Drop, keep with `ward = NULL`, or assign to the nearest ward? | Keep with `assigned_ward = NULL`, `assign_rule = 'OUTSIDE'`; **do not** silently assign to nearest — that hides a service-area question |
| **Multiple matches allowed?** | Only if the report is "matches", not "assignment" | Is the deliverable a unique assignment or a match list? | For crew dispatch: unique. For "who might be affected by this boundary?": list all |

Apply the policy to the fixture:

| Request | Geometric matches (inclusive) | Assigned ward | Rule applied | Clerk's `ward_code` (F10-1) | Agree? |
| --- | --- | --- | --- | --- | --- |
| P1 | A | A | interior | A | yes |
| P2 | A | A | interior | A | yes |
| P3 | B | B | interior | B | yes |
| P4 | B | B | interior | B | yes |
| P5 | A, B | **A** | BOUNDARY_TIEBREAK | NULL | **no** — clerk left it blank |
| P6 | none | **NULL** | OUTSIDE | C | **no** — clerk typed a code that is not a ward |

The last two columns are the reason to keep *both* the clerk's code and the geometric assignment: they disagree on exactly the records that need a human decision, and the disagreement is itself a data-quality finding for Chapter 9's log.

**The blueprint's key sentence** (10.8.3): a policy-driven single assignment is *different from* the raw geometric match result. Six matches (P1, P2, P3, P4, P5×2) became five assignments plus one explicit "outside". Report the raw count (6 ward–request matches, 5 distinct requests matched, 1 unmatched) *and* the policy result (A: 3, B: 2, outside: 1) — never one dressed up as the other.

**Misconception:** "The spatial join assigned the wards, so the wards are assigned." *Consequence:* the one-to-one join silently chose *some* register row for P5 (10.7.3), the analyst cannot say which rule was applied, and the next run may choose differently.

**Comprehension check 10.7.2.** Change one policy: boundary requests go to the ward whose *crew team* has fewer open requests. Using F10-1 and the current register (A → T-N, B → T-S), decide P5's ward, show the counts you used, and name the weakness of this rule.

### 10.7.3 Spatial joins: enrich or aggregate, and know your target

A **spatial join** transfers attributes from one layer (the *join* features) to another (the *target* features) when a spatial relationship holds, instead of when a key matches. ArcGIS Pro's tool summary: "Joins attributes from one or more inputs to another input based on the spatial relationship" [S31]. Its parameters are the ones this chapter has been building toward, and its documentation is the source the blueprint names, so they are quoted exactly.

- **Target Features / Join Features.** The target keeps its geometry and gains columns; the join layer supplies them. The same page that explains Select By Location's relationships applies to Spatial Join: "The Select Layer By Location tool has a Selecting Features parameter; the Spatial Join tool's equivalent parameter is Join Features" and "Relationship … Match Option" [X02].
- **Join Operation.** *Join one to one*: "If multiple join features are found that have the same spatial relationship with a single target feature, the attributes from the multiple join features will be aggregated using a field map merge rule … If one polygon has an attribute value of 3 and the other has a value of 7, and a Sum merge rule is specified, the aggregated value in the output feature class will be 10. This is the default." *Join one to many*: "the output feature class will contain multiple copies (records) of the target feature" [S31].
- **Match Option.** The relationship list of 10.5.3, plus *Closest* (with the random tie rule), *Within a distance* (with a *Search Radius*), *Have their center in*, and *Largest overlap* [S31].
- **Join_Count** — "The number of join features that match each target feature" — and **TARGET_FID** are added automatically; with *Join one to many*, **JOIN_FID** identifies which join feature produced each row and "A value of -1 for the JOIN_FID field means no feature meets the specified spatial relationship" [S31].
- **Keep All Target Features.** Checked (default): "All target features will be maintained in the output (outer join)"; unchecked: "Only those target features that have the specified spatial relationship with the join features will be maintained in the output feature class (inner join)" [S31].

The one usage note that turns this module's arithmetic into a documented fact: "If a join feature has a spatial relationship with multiple target features, it will be counted as many times as it is matched with the target feature. For example, if a point is in three polygons, the point will be counted three times, once for each polygon" [S31].

**Worked example 10.7-B — enrich requests with their ward (target = requests, join = wards, Match Option = Intersect, inclusive).** *Predicted output:*

| Setting | Rows | Join_Count / JOIN_FID per request | Notes |
| --- | --- | --- | --- |
| One to one, keep all | **6** | P1 1, P2 1, P3 1, P4 1, **P5 2**, **P6 0** | P5's ward attributes are *merged* by the field-map rule (default rule is *First* for text — a verification item); P6's ward columns are null |
| One to one, matches only | **5** | as above minus P6 | |
| One to many, keep all | **7** | P5 appears **twice** (once with A, once with B); P6 once with JOIN_FID −1 | Now every match is visible |
| One to many, matches only | **6** | P5 twice; P6 absent | Equals the raw match count of Appendix A.2.7 |

*Check:* the one-to-one row count equals the target count (6) whenever *keep all* is on — a spatial join can never *lose* a target under that setting, and it can never *gain* one under one-to-one. The one-to-many count 6 (matches only) = the sum of Join_Count over targets = 1 + 1 + 1 + 1 + 2 + 0.

**Worked example 10.7-C — aggregate requests per ward (target = wards, join = requests, Match Option = Intersect, one to one).** *Predicted output:* 2 rows (one per ward). `Join_Count`: **A = 3** (P1, P2, P5), **B = 3** (P3, P4, P5). Sum of merge rule on `est_cost_inr`: A = 1500 + 12000 + 2500 = **16,000**; B = NULL + 0 + 2500 = **2,500** (Esri: "Null values in join fields are ignored for statistic calculation" [S31]). *Check:* Join_Count total = 6 = the raw match count; distinct requests matched = 5; P5's 2,500 appears in *both* ward sums, so the two ward totals add to 18,500 although only 16,000 of cost exists — the duplicate-key lesson of 10.3.3 in spatial form. *What it does not establish:* a *policy* result. Under the 10.7.2 policy A has 3 requests (P1, P2, P5) and B has 2 (P3, P4); the raw aggregation says 3 and 3.

**Platform note — QGIS.** *Join attributes by location* has the same three shapes: `0 — Create separate feature for each matching feature (one-to-many)`, `1 — Take attributes of the first matching feature only (one-to-one)`, `2 — Take attributes of the feature with largest overlap only (one-to-one)`, plus *Discard records which could not be joined* and an output of non-matching features [X23]. The "first matching feature" option is the QGIS counterpart of the field-map *First* rule, and it is exactly as arbitrary for P5. *Join attributes by location (summary)* is the aggregation form [X23].

**Platform note — SQL.** The whole of 10.7 is one statement in PostGIS, which is why developers should learn to *write* the query and then recognise it in a dialog:

```sql
-- Enrich (one-to-many, keep all): one row per request per covering ward
SELECT r.request_id, w.ward_code
FROM requests r
LEFT JOIN wards w ON ST_Covers(w.geom, r.geom);
-- Aggregate: raw match count per ward (P5 counted in both)
SELECT w.ward_code, COUNT(r.request_id) AS n_requests
FROM wards w
LEFT JOIN requests r ON ST_Covers(w.geom, r.geom)
GROUP BY w.ward_code;
```

Swap `ST_Covers` for `ST_Contains` and P5 drops out of both wards; that single word is the study-area policy.

**Verification item.** The Spatial Join defaults quoted here (one-to-one, *keep all* checked, *Intersect*, merge rule for text fields) were read from the ArcGIS Pro 3.7 tool page; confirm the field-map default in the installed build before telling learners what P5's merged `ward_name` will be.

**Misconception:** "Join_Count is the number of requests in the ward." *Consequence:* the ward totals sum to 6 and the requests table has 6 rows, so the analyst is satisfied — but one request is in both wards and one is in none. The two errors cancelled. Always check the *distinct* count as well as the sum.

**Comprehension check 10.7.3.** Predict the Spatial Join output (rows, Join_Count) for target = **assets** (F4), join = requests, Match Option = *Within a distance*, Search Radius 25 m, one to one, keep all. Use the distances in Worked example 10.6-A. Then say which asset–request pair your result suggests is "the same incident" and why that suggestion is not evidence.

---
## 10.8 Guided lab: query predictable geometry

### 10.8.1 Objective, prerequisites, and what this lab is (and is not)

**Objective.** Predict, on paper, the IDs returned by three families of query on the planar fixture — strict-interior ward membership, boundary-inclusive ward membership, and a 300 m road-distance threshold — then execute the *documented equivalents* in one GIS engine, reconcile every difference, and write a shared-boundary policy that turns the raw matches into a single assignment per request.

**Prerequisites.** Modules 10.1–10.7 read; the worksheet from 10.1.3 open; Chapter 9's habit of working on a copy.

**What this lab is not.** It is not a software tutorial. The predictions (Table 10.8-A) are the deliverable that carries the marks; the execution exists to *test* the predictions and the documentation. **No route below has been executed by the author**; each is written from the cited official pages and marked *not execution-tested*, with its remaining verification listed in 10.8.7. If your installed version differs from the documented one, record the difference — that is a valid lab result.

### 10.8.2 Required software and input data (synthetic; reproduce exactly)

**Software — one of:** PostgreSQL with PostGIS (Route A; the route whose predicate definitions are most completely documented [S28] [S29] [S30] [X17]); QGIS Desktop (Route B; 3.44 documentation cited [X22] [X23]); ArcGIS Pro (Route C; 3.7 documentation cited [X01] [X02] [S31]; Basic licence suffices for every tool used).

**Input data.** Four small CSV files. Create them exactly as printed (a text editor is enough). Coordinates are on the planar training grid in metres with **no CRS**; the WKT column uses the OGC well-known-text syntax you met in Chapter 3. Do **not** save any of this as GeoJSON — GeoJSON's coordinates are degrees by definition (Chapter 7, [S18]).

`wards_ch10.csv`
```
ward_code,wkt
A,"POLYGON((0 0,1000 0,1000 1000,0 1000,0 0))"
B,"POLYGON((1000 0,2000 0,2000 1000,1000 1000,1000 0))"
```

`roads_ch10.csv`
```
road_id,name,wkt
R1,Main Road,"LINESTRING(0 500,2000 500)"
```

`requests_ch10.csv` (Table F10-1; empty cell = null)
```
request_id,x,y,category,priority,status,reported,closed,channel,ward_code,est_cost_inr
P1,200,200,Streetlight out,Medium,Open,2026-09-02,,Mobile app,A,1500
P2,800,800,Pothole,High,In progress,2026-08-28,,Phone,A,12000
P3,1200,250,Water leak,High,Open,2026-09-10,,Mobile app,B,
P4,1700,900,Pothole,Low,Resolved,2026-08-15,2026-08-20,Web form,B,0
P5,1000,500,Blocked drain,Medium,Reopened,2026-08-30,,Phone,,2500
P6,2200,500,Fallen tree,High,Closed – duplicate,2026-09-11,2026-09-12,Phone,C,
```

`ward_register_ch10.csv` (Table F10-2)
```
ward_code,ward_name,crew_team,valid_from,is_current
A,West ward,T-N,2019-04-01,No
A,West ward,T-N,2024-01-01,Yes
B,East ward,T-S,2019-04-01,Yes
```

**Fixture invariants** (check these after loading, before any query): 2 wards, 1 road, 6 requests, 3 register rows; each ward's area = 1,000,000 m²; R1's length = 2,000 m; the two ward polygons share the edge x = 1000 and do not overlap; P5's coordinates are exactly (1000, 500). If any invariant fails, the fixture was built differently from its definition and every later mismatch is suspect (10.8.6).

### 10.8.3 Step 1 — predict (before touching software)

Fill Table 10.8-A on your worksheet. Column headings name the *concept*; Table 10.8-B (next step) maps each concept to a documented tool option per route. Do not look at the expected values in Instructor Appendix I.3 until Step 5.

**Table 10.8-A — prediction table (learner fills the last column).**

| Q# | Concept | Definition to apply | Predicted IDs |
| --- | --- | --- | --- |
| A1 | Strict interior, Ward A | request in A's interior (not on its rings) | |
| A2 | Strict interior, Ward B | | |
| A3 | Boundary-inclusive, Ward A | request in A's interior *or* on A's boundary | |
| A4 | Boundary-inclusive, Ward B | | |
| A5 | Boundary only (touches), either ward | request on a ward boundary and in no ward's interior | |
| A6 | Within 300 m of R1, inclusive (≤ 300) | planar distance to the finite segment | |
| A7 | Within 300 m of R1, strict (< 300) | | |
| A8 | Combined: status ∈ {Open, Reopened} ∧ A3-or-A4 ∧ A6 | 10.7.1 | |
| A9 | Attribute join requests → register on `ward_code`, keep all: **row count** and rows per request | 10.3.1 | |
| A10 | Spatial join target = wards, join = requests, inclusive, one-to-one: **Join_Count per ward** | 10.7.3 | |

Also predict two counts you will use as invariants: the number of *distinct* requests matched to at least one ward under A3 ∪ A4, and the total number of ward–request matches under A3 + A4.

### 10.8.4 Step 2 — build the fixture in your engine

**Procedure (version-specific) — Route A, PostGIS (not execution-tested).** SRID 0 is PostGIS's "unknown" spatial reference, which makes it a pure planar environment — exactly what Appendix A.2 asks for. Distances are in coordinate units, i.e. metres by our declaration [X17].

```sql
CREATE TABLE wards (ward_code text PRIMARY KEY, geom geometry(Polygon, 0));
INSERT INTO wards VALUES
  ('A', ST_GeomFromText('POLYGON((0 0,1000 0,1000 1000,0 1000,0 0))', 0)),
  ('B', ST_GeomFromText('POLYGON((1000 0,2000 0,2000 1000,1000 1000,1000 0))', 0));

CREATE TABLE roads (road_id text PRIMARY KEY, name text, geom geometry(LineString, 0));
INSERT INTO roads VALUES ('R1', 'Main Road', ST_GeomFromText('LINESTRING(0 500,2000 500)', 0));

CREATE TABLE requests (
  request_id text PRIMARY KEY, category text, priority text, status text,
  reported date, closed date, channel text, ward_code text, est_cost_inr integer,
  geom geometry(Point, 0));
INSERT INTO requests VALUES
  ('P1','Streetlight out','Medium','Open','2026-09-02',NULL,'Mobile app','A',1500,  ST_SetSRID(ST_MakePoint(200,200),0)),
  ('P2','Pothole','High','In progress','2026-08-28',NULL,'Phone','A',12000,       ST_SetSRID(ST_MakePoint(800,800),0)),
  ('P3','Water leak','High','Open','2026-09-10',NULL,'Mobile app','B',NULL,       ST_SetSRID(ST_MakePoint(1200,250),0)),
  ('P4','Pothole','Low','Resolved','2026-08-15','2026-08-20','Web form','B',0,    ST_SetSRID(ST_MakePoint(1700,900),0)),
  ('P5','Blocked drain','Medium','Reopened','2026-08-30',NULL,'Phone',NULL,2500,  ST_SetSRID(ST_MakePoint(1000,500),0)),
  ('P6','Fallen tree','High','Closed – duplicate','2026-09-11','2026-09-12','Phone','C',NULL, ST_SetSRID(ST_MakePoint(2200,500),0));

CREATE TABLE ward_register (ward_code text, ward_name text, crew_team text, valid_from date, is_current text);
INSERT INTO ward_register VALUES
  ('A','West ward','T-N','2019-04-01','No'),
  ('A','West ward','T-N','2024-01-01','Yes'),
  ('B','East ward','T-S','2019-04-01','Yes');

-- invariants
SELECT ward_code, ST_Area(geom) FROM wards;          -- 1000000 each
SELECT ST_Length(geom) FROM roads;                    -- 2000
SELECT ST_Touches(a.geom, b.geom), ST_Overlaps(a.geom, b.geom)
  FROM wards a, wards b WHERE a.ward_code='A' AND b.ward_code='B';  -- t, f
SELECT ST_IsValid(geom) FROM wards;                   -- t, t  (Chapter 9; [S28] warns about invalid input)
```

**Procedure (version-specific) — Route B, QGIS 3.44 (not execution-tested).** Add each CSV with *Layer ► Add Layer ► Add Delimited Text Layer*: for `wards_ch10.csv` and `roads_ch10.csv` choose the well-known-text geometry definition on the `wkt` field; for `requests_ch10.csv` choose point coordinates with X = `x`, Y = `y`; add `ward_register_ch10.csv` with no geometry. **Verification item:** the exact control labels of the delimited-text dialog and the way to leave the geometry CRS *unset* (a non-Earth, planar layer) were not re-read for this chapter; confirm them in the installed build, and confirm that the project CRS does not silently reproject the layers. Check the invariants with the *Measure* tool or the field calculator (`$area`, `$length`).

**Procedure (version-specific) — Route C, ArcGIS Pro 3.7 (not execution-tested).** Requests: use Chapter 3's Route C — *XY Table To Point* with X Field `x`, Y Field `y`, and the *Coordinate System* parameter **cleared** so that the output has an unknown spatial reference (Chapter 3 recorded that the tool's default is WGS 84, which would label metres as degrees). Wards and road: either open the GeoPackage built in Route B, or create the two polygons and the line from the WKT with the ArcPy `FromWKT` insert-cursor approach recorded in Chapter 3's Route C, or digitise them by typing absolute coordinates (Chapter 9). Register: add the CSV as a stand-alone table. **Verification item:** whether Select Layer By Location's *Search Distance* accepts a value with the unit *Unknown* for an unknown-CRS layer (the tool takes a *Linear Unit* [X01]) must be confirmed; if it does not, the instructor's fallback is to assign the fixture a documented local engineering CRS in metres and to record that choice, because assigning a real geographic CRS would make the metre values wrong (Chapter 5).

### 10.8.5 Step 3 — execute the documented equivalents

Use Table 10.8-B to pick, per route, the option whose *documented definition* matches each concept. The right-hand columns quote the definitions' source so that you can check the mapping yourself; do not run an option because its name "sounds right".

**Table 10.8-B — concept → documented tool option.**

| Concept | Route A — PostGIS | Route B — QGIS *Select by location* / *Select within distance* | Route C — ArcGIS Pro *Select Layer By Location* |
| --- | --- | --- | --- |
| Strict interior (A1, A2) | `ST_Contains(ward, req)` — interiors must share a point; boundary points excluded [S28] | *are within* — the opposite of *contain*, which requires "at least one point of the interior of b … in the interior of a" [X22] | *Completely within* — "not selected" if the input "intersects the boundary" [X02]; *Within Clementini* gives the same for a point [X02] |
| Boundary-inclusive (A3, A4) | `ST_Covers(ward, req)` — "no point of B lies outside" [S29]; for a point, `ST_Intersects` gives the same [S30] | *intersect* (for points) — "share any portion of space – overlap or touch" [X22]; or *are within* **plus** *touch* (the dialog ORs multiple predicates [X22]) | *Within* — "within or contained by" [X02]; *Intersect* gives the same for points [X02] |
| Boundary only (A5) | `ST_Touches(ward, req)` [X14] | *touch* [X22] | *Boundary touches* [X01] |
| Within 300 m (A6/A7) | `ST_DWithin(req, road, 300)` [X17]; check the exact-threshold behaviour with `ST_Distance` [X18] | *Select within distance*, `DISTANCE` = 300 [X22] | *Within a distance*, *Search Distance* = 300 (unit per 10.8.4) [X01] |
| Attribute join (A9) | `LEFT JOIN ward_register g ON g.ward_code = r.ward_code` | *Join attributes by field value*, `METHOD` 0 (one-to-many) and then 1 (one-to-one) [X23] | *Add Join*, default operation (one-to-many if supported) and then *Join one to first* [X03] |
| Spatial join count (A10) | `SELECT w.ward_code, COUNT(r.request_id) … LEFT JOIN requests r ON ST_Covers(w.geom, r.geom) GROUP BY 1` | *Join attributes by location (summary)* or *Count points in polygon* — record which, and its boundary behaviour [X23] | *Spatial Join*, target = wards, join = requests, *Intersect*, one-to-one, keep all; read `Join_Count` [S31] |

Route A queries, ready to run (the other routes are dialogs; record the options you set):

```sql
-- A1/A2 strict
SELECT w.ward_code, r.request_id FROM wards w JOIN requests r ON ST_Contains(w.geom, r.geom) ORDER BY 1, 2;
-- A3/A4 inclusive
SELECT w.ward_code, r.request_id FROM wards w JOIN requests r ON ST_Covers(w.geom, r.geom) ORDER BY 1, 2;
-- A5 boundary only
SELECT w.ward_code, r.request_id FROM wards w JOIN requests r ON ST_Touches(w.geom, r.geom) ORDER BY 1, 2;
-- A6 within 300 m, and the exact distances for the A7 check
SELECT r.request_id, ST_Distance(r.geom, d.geom) AS dist_m,
       ST_DWithin(r.geom, d.geom, 300) AS within_300
  FROM requests r CROSS JOIN roads d ORDER BY 1;
-- A8 combined
SELECT r.request_id FROM requests r
 WHERE r.status IN ('Open', 'Reopened')
   AND EXISTS (SELECT 1 FROM wards w WHERE ST_Covers(w.geom, r.geom))
   AND EXISTS (SELECT 1 FROM roads d WHERE ST_DWithin(r.geom, d.geom, 300))
 ORDER BY 1;
-- A9 attribute join, keep all
SELECT r.request_id, g.ward_name, g.valid_from
  FROM requests r LEFT JOIN ward_register g ON g.ward_code = r.ward_code ORDER BY 1, 3;
-- A10 spatial join count per ward (inclusive)
SELECT w.ward_code, COUNT(r.request_id) AS join_count
  FROM wards w LEFT JOIN requests r ON ST_Covers(w.geom, r.geom) GROUP BY 1 ORDER BY 1;
```

Record every result in **Table 10.8-C** (one row per Q#, one column per option you ran, plus a "matches prediction?" column). For A6/A7, record P1 and P2 separately: whether the tool's "within 300" included points at *exactly* 300 m is a fact about the engine that 10.6.1 said the documentation does not settle.

### 10.8.6 Step 4 — reconcile every mismatch

For each row of Table 10.8-C where the result differs from the prediction, write one of the four causes from 10.1.3 and the evidence:

| Cause | How it shows on this fixture | What to do |
| --- | --- | --- |
| **Predicate semantics** | P5 appears under a "within" option you expected to be strict (Esri *Within* is inclusive [X02]); or P5 is missing from an option you expected to be inclusive (QGIS *are within*, PostGIS `ST_Within` [X22] [X13]) | Not an error. Correct the *mapping* in Table 10.8-B and keep the result; this is the lab's central finding |
| **CRS / units** | Distances come out ≈ 0.0027 or ≈ 33,000 instead of 300 — the layer was assigned a geographic CRS (Chapter 5/6) or the search distance was read in a different unit | Rebuild the layer with no CRS (or the documented local CRS) and rerun; log the mistake |
| **Tolerance** | ArcGIS Pro applies the feature class's x,y tolerance client-side [X01]; on integer coordinates it should not change anything, but a fixture rebuilt with rounded coordinates might put P5 a millimetre inside one ward | Inspect P5's stored coordinates to full precision; compare with the DBMS route note in [X01] |
| **Fixture construction** | An invariant from 10.8.2 fails: ward area ≠ 1,000,000, road length ≠ 2,000, P5 ≠ (1000, 500), or the wards overlap | Fix the fixture, re-check invariants, rerun everything |

A result that matches the prediction *for the wrong reason* also counts as a mismatch: if your A3 prediction listed P5 in Ward A and the tool listed it too, but the option you ran was *Completely within*, look again — one of the two is wrong.

### 10.8.7 Expected results, validation checks, and troubleshooting

The expected values for Table 10.8-A are printed in Instructor Appendix I.3, with their derivation; open them only after Step 4. The checks below can be applied without them.

**Validation checks.**

1. **Complements.** For each ward, the strict set (A1/A2) plus the touches set (A5, restricted to that ward) must equal the inclusive set (A3/A4). Any point in the inclusive set but in neither of the other two is unexplained.
2. **Distinct versus total.** Under the inclusive rule the *total* ward–request matches exceed the number of *distinct* requests matched by exactly the number of boundary requests (here 1).
3. **Strict versus inclusive distance.** A7 ⊆ A6, and A6 − A7 is exactly the set of requests at 300.000 m (P1, P2). If A6 − A7 is empty, your engine treats the threshold as strict *or* your distances are not exactly 300 — compute `ST_Distance` / the Near distance to full precision to tell which.
4. **Combined query.** A8 must be a subset of the status set, of A3 ∪ A4, and of A6, and the counts after each step of a sequential selection must be non-increasing.
5. **Join row counts.** A9's keep-all row count must equal 6 + (number of extra register rows matched) − 0; the number of requests with *no* ward name must be 2 under a correct null and unmatched treatment.
6. **Spatial join.** The `Join_Count` total across wards must equal the A3 + A4 total; the distinct requests must equal 5.

**Troubleshooting.**

| Symptom | Likely cause | Action |
| --- | --- | --- |
| Every request is "within 300" of the road, including P4 | Threshold interpreted in a larger unit, or a geographic CRS was assigned so 300 is degrees | Check the layer's CRS (should be none) and the unit shown beside the distance |
| P6 shows distance 0 to R1 | The road was built as something other than a finite segment (e.g. an extra vertex, or a polygon) | Inspect R1's WKT; length must be 2,000 |
| P5 in *neither* ward under every option | The chosen options were all strict; or P5's coordinate was stored as (1000.0001, 500) | Run an inclusive option; inspect coordinates |
| P5 in *both* wards under every option | All chosen options were inclusive | Expected for inclusive; now run a strict option to see the other side |
| Attribute join returns 6 rows and P1 has the 2019 ward row | One-to-one / one-to-first join picked the first match [X03] [X24] | Rerun one-to-many, or filter the register to `is_current = 'Yes'` first (10.3.3) |
| Attribute join drops P5 and P6 | *Keep all* / *Discard non-matching* set to drop unmatched rows | Expected under that setting; record both counts |
| QGIS *are within* excludes P5; ArcGIS *Within* includes it | Documented difference in definition [X22] [X02] | This is the finding; write it up |
| Tool warns about unknown coordinate system | Expected for this planar fixture | Decline any offer to assign a CRS (Chapter 5); record the message |

### 10.8.8 Step 5 — write the shared-boundary policy, and required deliverables

Write a **shared-boundary policy** of no more than half a page that a colleague could apply without you. It must state: the predicate used for membership (strict or inclusive, by documented name in your engine); the rule for a request on a shared boundary; the rule for a request outside all wards; the rule for a request in a hole, should a ward ever have one; whether the deliverable is a unique assignment or a match list; and how the policy result is labelled so that it is never confused with the raw geometric result. Apply it to the fixture and show the assignment table (10.7.2 is the model; you may choose a different tie-break, but you must state it).

**Deliverables.**

1. Table 10.8-A with your predictions, dated *before* Step 3.
2. Table 10.8-C with executed results, the engine and version, the exact options/SQL used, and the reconciliation notes from Step 4 (including the exact-threshold observation for P1/P2).
3. The invariant checks from 10.8.2 with their values.
4. The shared-boundary policy and the resulting assignment table, alongside the raw match counts (total matches, distinct matched, unmatched).
5. A three-line note on any documentation you found ambiguous, and what you tested to resolve it.

A screenshot alone is not a deliverable (Appendix B.1.4): it shows a result without showing the condition or the option that produced it.

---

## 10.9 Independent check and progression gate

Complete this without the Instructor Appendix. Every question states its assumptions; where it does not say otherwise, coordinates are planar metres on the training grid, distances are Euclidean, and "inclusive" means the boundary case counts.

### 10.9.1 Concept questions (six)

**Q1.** [10.1; LO1] A colleague puts a definition query `status = 'Open'` on the requests layer in ArcGIS Pro, then runs Spatial Join with that layer as the target. Which requests does the tool process, and did the stored requests table change? Choose one: (a) all six; nothing changed — (b) P1 and P3; nothing changed — (c) P1 and P3; the other four were deleted — (d) all six; the definition query was written into the table. Justify with one sentence and a citation.

**Q2.** [10.2; LO2] On Table F10-1, give the IDs returned by `NOT (est_cost_inr > 2000)`, the IDs returned by `est_cost_inr > 2000`, and the IDs returned by *neither*. Then rewrite the first condition so that it also returns the requests whose cost is unknown.

**Q3.** [10.3; LO3] Table F10-1 is joined to Table F10-2 on `ward_code` in QGIS's layer *Joins* tab (documented as one-to-one, "only the first fetched feature is picked", all target features kept). How many rows does the result have, which requests may carry the *wrong* `valid_from`, and why can the answer to "which" differ between two runs?

**Q4.** [10.4; LO4] For the L-shaped yard Y of 10.4.3 and the point (250, 250): state the bounding-box test result, the exact `ST_Contains(Y, point)` result, and whether `ST_Intersects(Y, point)` can differ from `ST_Covers(Y, point)` for *any* point. One sentence each.

**Q5.** [10.5; LO5] Complete the table for request P5 at (1000, 500) and Ward B, giving true/false or selected/not selected and the documented reason: PostGIS `ST_Contains(B, P5)`; PostGIS `ST_Covers(B, P5)`; ArcGIS Pro Select By Location *Within* (input = requests, selecting = Ward B); ArcGIS Pro *Completely within*; QGIS *are within*; QGIS *touch*.

**Q6.** [10.6; LO6] A developer calls a hosted feature layer's `query` operation with `geometry=<P5>`, `spatialRel=esriSpatialRelIntersects`, `distance=300`, and **no** `units` parameter, once on ArcGIS Online and once on an ArcGIS Enterprise deployment. Explain why the two calls can return different feature sets although the data are identical, and name the second parameter that must be checked when the layer's coordinates are in degrees.

### 10.9.2 Scenario questions (two)

**S1.** [10.3, 10.7; LO3, LO7] A dashboard reports "Requests per crew — T-N: 6, T-S: 3, unassigned: 0; total 9". It was built by (i) a Spatial Join of requests (target) to wards (join) with *Intersect*, *Join one to many*, keep all; then (ii) an attribute join of the result to the ward register export on `ward_code` with a one-to-many operation, keeping only matches; then (iii) a count of rows per `crew_team`. Using Tables F10-1 and F10-2, reproduce the 6 and the 3, explain each inflation step, and specify a corrected procedure whose totals you state. Say which unit each of the three counts (6, 3, and your corrected numbers) actually measures.

**S2.** [10.6, 10.7; LO6, LO7] The drainage team asks: "For every blocked-drain request, find the drain it refers to — the nearest drain within 15 m — so we can auto-fill `asset_id`." On the fixture there is one blocked-drain request (P5) and one drain (DR-0042). (a) Give the distance and say whether the rule fills `asset_id`. (b) The team now wants the same rule on Fixture E6, whose coordinates are in EPSG:4326; write the two-step method you would use and the one you would refuse, with reasons. (c) State the three things the rule must specify beyond "nearest within 15 m" before it is reproducible, and the one thing a filled `asset_id` from this rule does *not* establish (Chapter 9).

### 10.9.3 Independent practical task (unfamiliar inputs)

**Fixture Z-10 (synthetic; planar metres; no CRS; new for this task — do not reuse Chapter 10 numbers).** A depot has two yards and two underground pipes; six inspection points were logged with a yard code typed by the inspector.

```
zones_z10.csv
zone_code,wkt
Z1,"POLYGON((0 0,600 0,600 600,0 600,0 0),(200 200,400 200,400 400,200 400,200 200))"
Z2,"POLYGON((600 0,1200 0,1200 600,600 600,600 0))"

pipes_z10.csv
pipe_id,wkt
L1,"LINESTRING(0 100,1200 100)"
L2,"LINESTRING(0 500,1200 500)"

points_z10.csv
point_id,x,y,zone_code
K1,100,100,Z1
K2,300,260,Z1
K3,600,300,Z2
K4,900,150,Z2
K5,1300,250,Z3
K6,200,350,

zone_register_z10.csv
zone_code,zone_name,valid_from,is_current
Z1,North yard,2023-01-01,No
Z1,North yard,2025-01-01,Yes
Z2,South yard,2023-01-01,Yes
```

Z1 has a **hole** (the inner ring). Z1 and Z2 share the edge x = 600.

**Required.**

1. **Predict on paper**, for each point: its position relative to Z1 and Z2 (interior / boundary / hole / exterior), the strict-interior zone (if any), the inclusive zone(s), and whether it *touches* a zone. Handle the point in the hole and the point on the hole's ring explicitly.
2. **Predict** the set of points within 150 m (inclusive) of *any* pipe, the set within 150 m (strict), and for each point its nearest pipe and distance to two decimals. Identify the equal-distance tie and state a documented tie rule you would apply and why "random" is unacceptable.
3. **Predict** the row count of the attribute join points → zone register on `zone_code` with all points kept, the rows per point, the number of distinct points matched, and the number unmatched (with the two different reasons).
4. **Predict** the `Join_Count` per zone for a spatial join target = zones, join = points, inclusive, one-to-one, and the total versus distinct counts.
5. **Execute** items 1–4 in one engine (any route from 10.8), record engine/version/options, and reconcile every difference using the four causes of 10.1.3.
6. **Write** the zone-assignment policy (boundary, hole, outside, unmatched register key, duplicate register key) and apply it, producing one assignment or explicit "none" per point, labelled separately from the raw matches.

Submit the prediction tables (dated before execution), the executed results, the reconciliation, and the policy with its assignment table.

### 10.9.4 Oral explanation (one)

In no more than three minutes, using request P5, explain to a colleague who has never used a GIS why the same request is "in neither ward" in one tool and "in both wards" in another, why *neither* tool is wrong, and what the one sentence in the team's written policy must say so that the crew-dispatch report shows P5 exactly once.

### 10.9.5 Submission requirements and scoring

**Submit:** written answers to Q1–Q6 and S1–S2 with the module cited beside each; the practical task's prediction tables, executed results, reconciliation, and policy; and the oral explanation delivered live or recorded.

**Scoring** (aligned with Appendix B.3; pass threshold 80 %, with remediation required for any critical misconception regardless of total):

| Component | Weight | What earns the marks |
| --- | --- | --- |
| Concept questions (6) | 30 % | Correct IDs/counts *with* the reason; a right answer with no reasoning scores half |
| Scenario questions (2) | 20 % | Each inflation or error step identified and the corrected procedure's counts stated and unit-labelled |
| Practical task | 40 % | Predictions dated before execution (10 %); correct predictions for hole, boundary, tie, duplicate key, and unmatched key (15 %); execution recorded with engine, version, and exact options (5 %); every mismatch reconciled to one of the four causes (5 %); policy complete and applied, labelled separately from raw matches (5 %) |
| Oral explanation | 10 % | Distinguishes predicate semantics from data quality; names the policy sentence; does not claim either tool is wrong |

**Critical misconceptions for this chapter** (any one triggers remediation and a fresh exercise, Instructor Appendix I.5): treating `= NULL` or `<>` as a test for missing values; summing a left-table column over rows multiplied by a right-table join; applying a metre threshold to degree coordinates; presenting a spatial join's raw match count as an assignment; and assuming a tool's *within* is strict (or inclusive) without citing its documentation.

**Progression.** Proceed to Chapter 11 when you can explain every selected ID and count in your practical — including the zero (K2 in the hole under every membership option), the multiple match (K3), the tie (K3's pipes), and the join inflation (K1, K2) — without reference to the answer key.

---

## 10.10 Media and source brief

The full specifications are in the Media Appendix (M.1–M.5). In summary:

1. **Interactive 2D demonstration (M.5).** One polygon with a hole on the training grid, one movable point, and a live table of predicate results computed by *stated* rules — the OGC/PostGIS definitions quoted in 10.5 — with a second column set showing the ArcGIS Pro option names from [X02] mapped to the same rules. The engine is named; nothing is inferred from a screenshot. No 3D scene is needed for the principal lesson; Z is mentioned only in the bridge panel of M.3.
2. **Audio.** The narration walks one point through the four positions — interior, edge, hole, exterior — and says aloud what each predicate returns and why, pausing for prediction before each answer (M.2).
3. **Sources.** The chapter reads [S27] for source-dependent SQL, [S28]–[S30] for explicit predicate behaviour, and [S31] for ArcGIS spatial joins, plus the additional official pages [X01]–[X25] listed in the References. Chapter 11 builds derived outputs — buffers, clips, intersections, dissolves, and spatial joins that *transfer* attributes — from the relationships this chapter taught you to test.

---

## Glossary

| Term | Meaning in this chapter | First used |
| --- | --- | --- |
| Attribute condition | A true/false test on a record's columns (a SQL `WHERE` clause) | 10.2 |
| Attribute join (join by identity) | Pairing rows whose key values are equal; geometry stays with the left table | 10.3 |
| Boundary (of a geometry) | A polygon's rings (outer and inner); a line's two end points; a point has none | 10.4 |
| Bounding box (extent, envelope) | The smallest axis-aligned rectangle containing a geometry; used to find candidates | 10.4.3 |
| Candidate test | A cheap test (box overlap) that can say "maybe" or "no", never "yes" | 10.4.3 |
| Clementini options (ArcGIS) | *Within Clementini* / *Contains Clementini*: the OGC interior/boundary rules, excluding features lying entirely on the boundary | 10.5.3 |
| Contains / within | Every point of B is in A and the interiors share a point; a boundary-only point is *not* contained (OGC, PostGIS, QGIS); Esri's plain *Within*/*Contains* are inclusive | 10.4, 10.5 |
| Covers / covered by | Every point of B is in A, boundary included (PostGIS `ST_Covers`) | 10.5.2 |
| DE-9IM | The interior/boundary/exterior intersection model behind the named predicates; introduced by name only | 10.4.1 |
| Definition query (ArcGIS Pro) | A filter on a layer that limits which features it retrieves, draws, lists, and processes | 10.1 |
| Disjoint | No point in common; the complement of intersects | 10.4 |
| Exterior | Everything outside a geometry's interior and boundary; a polygon's holes are exterior | 10.4, 10.5 |
| Filter | Limits what a view shows without changing the data | 10.1 |
| Geodesic distance | Distance on the ellipsoid, in metres; contrast planar | 10.6.2 |
| Hole (interior ring) | A ring inside a polygon that removes area; its ring is part of the boundary | 10.5 |
| Interior | The inside of a polygon; a line minus its ends; a point itself | 10.4 |
| Intersects (predicate) | Any point in common; boundary contact counts | 10.4, 10.5.3 |
| Intersection (operation) | Constructs the shared geometry — Chapter 11 | 10.4.2 |
| Join_Count / TARGET_FID / JOIN_FID | Fields ArcGIS Spatial Join adds: matches per target, target ID, and (one-to-many) join ID | 10.7.3 |
| Match option / relationship | The predicate chosen in ArcGIS Spatial Join / Select By Location | 10.5.3 |
| Nearest | The candidate with the smallest distance; needs a search limit and a tie rule | 10.6.1 |
| Null (unknown) | A missing value; comparisons with it are unknown; tested with `IS NULL` | 10.2.2 |
| One-to-one / one-to-many (join) | Whether multiple matches are merged into one output row or produce several | 10.3, 10.7.3 |
| Overlaps | Same dimension, interiors meet, neither covers the other | 10.4 |
| Policy (assignment) | The written business rule that turns raw matches into one assignment per record | 10.7.2 |
| Selection | A session-held set of highlighted records that tools may act on | 10.1 |
| Spatial join | Transfers attributes between layers when a spatial relationship holds | 10.7.3 |
| Spatial predicate | A true/false test on two geometries | 10.4 |
| Strict / inclusive membership | Whether a point on the boundary counts as "in" | 10.5 |
| Target / join features | The layer that keeps its geometry and gains columns / the layer that supplies them | 10.7.3 |
| Tie rule | What a nearest query does when two candidates are equally close | 10.6.1 |
| Touches | Common points exist and lie only on boundaries | 10.4 |
| Within a distance | A predicate: distance ≤ (or <) a threshold in stated units | 10.6.1 |
| x,y tolerance (ArcGIS) | A small distance within which coordinates are treated as coincident during client-side operations | 10.5.3 |

## Recap

- A **filter**, a **selection**, an **export**, and a **modification** look alike and differ in what they change; predict counts and source changes before running anything, on a visible *input → condition → expected IDs* worksheet (10.1).
- Attribute conditions are SQL, but quoting, delimiters, case sensitivity, and date literals are decided by the **data source**; `IS NULL` is the only test for missing values, and a null is excluded from a condition *and* from its negation — zero is a value, unknown is not (10.2).
- A key join with **duplicate keys** multiplies rows and inflates sums; **unmatched keys** vanish or become nulls depending on a setting; always say whether you are counting entities, matched pairs, or distinct matches (10.3).
- Spatial predicates are set statements about **interior, boundary, and exterior**; *intersects?* is a question, *intersection* is a construction; a **bounding box** overlap is a candidate, not an answer (10.4).
- On a **boundary point**, `ST_Contains`/`ST_Within` (PostGIS, QGIS) say no and `ST_Covers`/`ST_Intersects` say yes; ArcGIS Pro's plain *Within* is inclusive and *Completely within* is strict; a hole is exterior. Trust definitions and one executed case, not names (10.5).
- **Nearest** needs a search limit and a tie rule (Esri's is random); **within a distance** needs a unit and a boundary decision; metre thresholds on degree coordinates are wrong; a Z column does not make a query 3D (10.6).
- Decompose combined questions into testable conditions with stated assumptions; write a **ward-assignment policy** for boundary, overlap, unmatched, and multiple-match cases; a spatial join's `Join_Count` counts *matches*, and a point in two polygons is counted twice (10.7).
- On the fixture: strict A = {P1, P2}, B = {P3, P4}; inclusive adds P5 to both; within 300 m inclusive = {P1, P2, P3, P5, P6}; open ∧ inside ∧ near = {P1, P3, P5}; six matches, five distinct requests, one policy decision (10.8).

## Cross-references to later chapters

- **Chapter 11 — Spatial analysis fundamentals.** Buffers make the "within 300 m" region *visible* as a polygon; clip and intersect *construct* the geometry that 10.4.2 said this chapter only tests; dissolve aggregates; spatial joins *transfer and summarise* attributes with the target/join and one-to-many rules verified here [S31]. Every Chapter 11 output should be checked against a Chapter 10 predicate count.
- **Chapter 12 — Cartography.** A ward map coloured by "requests per ward" must state whether the count is raw matches (P5 twice) or policy assignments (P5 once) — the difference between 3/3 and 3/2 on this fixture is a map-reading question.
- **Later phases.** ArcGIS Online filters and the REST `query` operation's `where`, `spatialRel`, `distance`, and `units` parameters [X11] [X12] are the configuration and development surface for everything in this chapter; ArcGIS Enterprise's DBMS-side spatial evaluation [X01] and PostGIS index behaviour [X20] are performance topics deferred, as the blueprint's boundary requires, to later work.

## References

All pages were opened and read by the author on **19 September 2026**. IDs [S27]–[S31] are the blueprint's reference register entries used by this chapter; [X01]–[X25] are additional official pages consulted to support specific claims. Esri "latest" pages (whose page metadata identified ArcGIS Pro **3.7** on the check date), ArcGIS Online help, and the REST API reference change without notice; the PostGIS manual identified itself as **3.6.5dev**; QGIS pages are the 3.44 edition. The blueprint's `pro.arcgis.com` addresses redirect to `doc.esri.com`; the destinations are recorded.

| ID | Publisher | Page title | URL | Accessed |
| --- | --- | --- | --- | --- |
| S27 | Esri | ArcGIS Pro — SQL reference for query expressions used in ArcGIS | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/navigation/sql-reference-for-elements-used-in-query-expressions.html | 2026-09-19 |
| S28 | PostGIS Project | PostGIS 3.6.5dev Manual — ST_Contains | https://postgis.net/docs/ST_Contains.html | 2026-09-19 |
| S29 | PostGIS Project | PostGIS 3.6.5dev Manual — ST_Covers | https://postgis.net/docs/ST_Covers.html | 2026-09-19 |
| S30 | PostGIS Project | PostGIS 3.6.5dev Manual — ST_Intersects | https://postgis.net/docs/ST_Intersects.html | 2026-09-19 |
| S31 | Esri | ArcGIS Pro — Spatial Join (Analysis Tools) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/analysis/spatial-join.html | 2026-09-19 |
| X01 | Esri | ArcGIS Pro — Select Layer By Location (Data Management Tools) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/select-layer-by-location.html | 2026-09-19 |
| X02 | Esri | ArcGIS Pro — Select By Location graphic examples | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/select-by-location-graphical-examples.html | 2026-09-19 |
| X03 | Esri | ArcGIS Pro — Add Join (Data Management Tools) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/add-join.html | 2026-09-19 |
| X04 | Esri | ArcGIS Pro — Select Layer By Attribute (Data Management Tools) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/select-layer-by-attribute.html | 2026-09-19 |
| X05 | Esri | ArcGIS Pro — Filter features with definition queries | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/layer-properties/definition-query.html | 2026-09-19 |
| X06 | Esri | ArcGIS Pro — Calculate Field (Data Management Tools) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/calculate-field.html | 2026-09-19 |
| X07 | Esri | ArcGIS Pro — Export Features (Conversion Tools) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/conversion/export-features.html | 2026-09-19 |
| X08 | Esri | ArcGIS Pro — Near (Analysis Tools) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/analysis/near.html | 2026-09-19 |
| X09 | Esri | ArcGIS Pro — How proximity tools calculate distance | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/analysis/how-near-analysis-works.html | 2026-09-19 |
| X10 | Esri | ArcGIS Pro — Select By Location: 3D relationships | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/select-by-location-3d-relationships.html | 2026-09-19 |
| X11 | Esri | ArcGIS REST APIs — Query (Feature Service/Layer) | https://developers.arcgis.com/rest/services-reference/enterprise/query-feature-service-layer/ | 2026-09-19 |
| X12 | Esri | ArcGIS Online — Apply filters (Map Viewer) | https://doc.arcgis.com/en/arcgis-online/create-maps/apply-filters-mv.htm | 2026-09-19 |
| X13 | PostGIS Project | PostGIS 3.6.5dev Manual — ST_Within | https://postgis.net/docs/ST_Within.html | 2026-09-19 |
| X14 | PostGIS Project | PostGIS 3.6.5dev Manual — ST_Touches | https://postgis.net/docs/ST_Touches.html | 2026-09-19 |
| X15 | PostGIS Project | PostGIS 3.6.5dev Manual — ST_Disjoint | https://postgis.net/docs/ST_Disjoint.html | 2026-09-19 |
| X16 | PostGIS Project | PostGIS 3.6.5dev Manual — ST_Overlaps | https://postgis.net/docs/ST_Overlaps.html | 2026-09-19 |
| X17 | PostGIS Project | PostGIS 3.6.5dev Manual — ST_DWithin | https://postgis.net/docs/ST_DWithin.html | 2026-09-19 |
| X18 | PostGIS Project | PostGIS 3.6.5dev Manual — ST_Distance | https://postgis.net/docs/ST_Distance.html | 2026-09-19 |
| X19 | PostGIS Project | PostGIS 3.6.5dev Manual — ST_3DIntersects | https://postgis.net/docs/ST_3DIntersects.html | 2026-09-19 |
| X20 | PostGIS Project | PostGIS 3.6.5dev Manual — Spatial Relationships (DE-9IM, named predicates, index use, ST_DWithin) | https://postgis.net/docs/using_postgis_query.html | 2026-09-19 |
| X21 | PostgreSQL Global Development Group | PostgreSQL Documentation ("current") — 9.2 Comparison Functions and Operators | https://www.postgresql.org/docs/current/functions-comparison.html | 2026-09-19 |
| X22 | QGIS Project | QGIS Desktop 3.44 User Guide — Processing: Vector selection (Select by location; Select within distance; Extract by location) | https://docs.qgis.org/3.44/en/docs/user_manual/processing_algs/qgis/vectorselection.html | 2026-09-19 |
| X23 | QGIS Project | QGIS Desktop 3.44 User Guide — Processing: Vector general (Join attributes by field value; Join attributes by location; Join attributes by nearest) | https://docs.qgis.org/3.44/en/docs/user_manual/processing_algs/qgis/vectorgeneral.html | 2026-09-19 |
| X24 | QGIS Project | QGIS Desktop 3.44 User Guide — Connecting and Editing Data Across Layers (joins) | https://docs.qgis.org/3.44/en/docs/user_manual/working_with_vector/joins_relations.html | 2026-09-19 |
| X25 | QGIS Project | QGIS Desktop 3.44 User Guide — Functions List (operators: =, <>, IS, LIKE, ILIKE, BETWEEN, IN) | https://docs.qgis.org/3.44/en/docs/user_manual/expressions/functions_list.html | 2026-09-19 |

Blueprint references cited by earlier chapters and reused here without re-reading: [S18] RFC 7946 (GeoJSON coordinates are degrees — Chapter 7), [S20] shapefile output considerations (no null numbers — Chapter 7). Chapter 6's Fixture E6 values (hand-checked there from the IOGP Guidance Note 7-2 and the EPSG registry) are reused in 10.6.2 as given.

---
# Instructor Appendix (separate from learner material)

Learners should not read this appendix before completing the 10.9 gate. Every value below is a hand-checked prediction on the synthetic fixture; nothing has been executed in GIS software (see I.6 for what remains to be verified in the installed environment).

## I.1 Answers and reasoning — comprehension checks

**10.1.2.** (a) Three rows carry `T-N`: P2, P3, P6 (the High-priority set), because Calculate Field "will only update the selected records" [X06]. (b) Yes — Calculate Field "modifies the input data" [X06]; the selection was a filter on *which* rows changed, not on *whether* they changed. (c) The predicted IDs and count (P2, P3, P6; 3), a confirmation that the selection was still active, and a note that a copy/log existed (Chapter 9).

**10.2.1.** Without parentheses: `AND` binds first, so the condition reads `Open OR (Reopened AND High)`; `Reopened AND High` matches nothing (P5 is Medium), so the result is the Open set: **P1, P3**. With parentheses `(status = 'Open' OR status = 'Reopened') AND priority = 'High'`: the unresolved set {P1, P3, P5} intersected with High {P2, P3, P6} = **P3**.

**10.2.2.** (a) `closed IS NULL` → P1, P2, P3, P5; of those, `est_cost_inr IS NOT NULL` → **P1, P2, P5** (3). (b) `NOT (closed IS NULL)` = `closed IS NOT NULL` → **P4, P6** (2); `IS NULL` yields true/false, never unknown, so its negation is a clean complement [X21]. (c) In PostgreSQL and QGIS `est_cost_inr = NULL` evaluates to unknown for *every* row (`5 = NULL → NULL`, `NULL = NULL → NULL` [X25]; "7 = NULL yields null" [X21]), so **zero rows** are returned; ArcGIS requires `IS` before `NULL` [S27], so the expression is rejected or fails validation rather than returning rows.

**10.2.3.** For the PostgreSQL layer: ArcGIS "will pass the SQL expression to the RDBMS" [S27], so the literal is judged by PostgreSQL — `date '2026-09-01'` is a PostgreSQL literal, but the double-quoted `"reported"` is a case-sensitive identifier there, so it matches only a column stored in lowercase; also "enterprise geodatabases don't use field delimiters" in ArcGIS's own dialect [X04]. For the GeoPackage layer: the SQLite dialect applies [S27]; `date '…'` is documented for shapefiles/file-based sources, not for SQLite, so consult the mobile-geodatabase/SQLite forms and test with a known-answer query (a date you know matches exactly one row). In both cases: identify the source type first, then the dialect, then run a known-answer test before trusting a count *or* an error.

**10.3.2.** (a) Three rows: INS-0001, INS-0003, INS-0004 (all condition 3). (b) The mean over the four inspection rows (TR-0301's null row is ignored) is (3 + 2 + 3 + 3)/4 = **2.75**; it is a mean of *inspection observations*, weighted by how often each asset was visited, and it says nothing about TR-0301, which was never inspected. (c) "For each asset, take the inspection with the latest *visit* time" — event time, not entry time and not the highest ID: DR-0042 → INS-0002 (condition 2); SL-0113 → INS-0003 (2026-03-14; condition 3), not INS-0004.

**10.3.3.** "Inspections per team: 2 + 2 = 4" is correct (four inspection rows). "Assets per team: 2 + 2 = 4" is the unit error: it counted *inspection rows* (or asset–inspection pairs) grouped by team, not assets. Distinct assets with a team: DR-0042 (T-S) and SL-0113 (T-N) — one each — plus TR-0301 with no team; total 3.

**10.4.1.** (Ward A, PK-01): intersects **true**, disjoint false, A contains PK-01 **false** (one part lies in B), touches false (interiors overlap in the western part), overlaps **true** (same dimension, interiors meet, neither covers the other). (Ward B, PK-01): identical. The deciding fact: *contains* applies to the **whole** multipart feature; a feature with any part outside the polygon is not contained.

**10.4.2.** Ask (1) "Did the output contain *new shapes* (features clipped to the overlap, with new areas/lengths), or the original request rows highlighted or appended to?" and (2) "Which tool or function — a Select By Location / `ST_Intersects` test, or the Intersect/Clip geoprocessing tool / `ST_Intersection`?" Seven rows from a predicate-based *join* would be a one-to-many spatial join keeping all targets (P5 twice, P6 with no match); seven from an overlay of six points with two wards is not expected (an overlay would yield six output points — P5 split into one per ward — and drop P6), so "seven" itself points at a join, not an overlay.

**10.4.3.** Inside box and inside Y: any point with y ≤ 100 (e.g. (150, 50)) or x ≤ 100 (e.g. (50, 250)). Inside box but outside Y: any point with x > 100 and y > 100, e.g. (250, 250). The box test alone cannot distinguish them — both are candidates.

**10.5.1.** H5 (1450, 700) lies on the inner ring: `ST_Contains` **false**, `ST_Covers` **true**, `ST_Intersects` **true**, `ST_Touches` **true** — identical to H2, because the inner ring is part of the polygon's boundary exactly as the outer ring is.

**10.5.3.** TR-0302 in the courtyard hole is in DP-01's *exterior*: `ST_Covers(DP-01, tree)` **false**; ArcGIS *Within* **not selected**; ArcGIS *Intersect* **not selected**; QGIS *are within* **false**. An engine testing only the outer ring would report the tree as inside/covered — wrongly, because a hole is not part of the polygon.

**10.6.1.** P9 (600, 500): to SL-0113 √(395² + 305²) = √249,050 ≈ 499.05 m; to DR-0042 √(395² + 10²) = √156,125 ≈ 395.13 m; to TR-0301 √(1590² + 20²) ≈ 1,590.1 m. Nearest: **DR-0042, 395.13 m**. Within 400 m: DR-0042 only, under both inclusive and strict readings (395.13 is neither at nor beyond the threshold).

**10.6.2.** Chapter 6: 0.010° of longitude ≈ 1,025 m at this latitude, so 0.005° ≈ **512.5 m** (the delivered UTM eastings differ by 512.389 m, consistent). A "within 0.005°" query centred on the boundary reaches Q4 exactly at its threshold (inclusive reading); a "within 500 m" geodesic query does not, because 512 m > 500 m — the degree threshold is *longer* on the ground than 500 m in the east–west direction at this latitude.

**10.6.3.** With Z on both: `ST_Intersects` **true** (2D: same x,y), `ST_3DIntersects` **false** (6 m apart vertically). With the drain's Z stored as 0 by an import: `ST_Intersects` still true; `ST_3DIntersects` false (0 versus 6) — numerically the same answer, but the 0 is a fabricated value, not an observation; the data defect is the third case (a null replaced by zero, Chapter 7/8), and any 3D result built on it is not evidence.

**10.7.1.** C1 `reported >= 2026-09-01` → P1, P3, P6. C2 NOT (distance ≤ 300) → only P4 (400 m). C1 ∧ C2 = **∅** (P4 was reported in August). The assumption that most changes the answer is the threshold's inclusiveness: with a strict "within" (< 300), P1 at exactly 300 m is *not* within, so NOT-within = {P1, P4} and the answer becomes **{P1}**.

**10.7.2.** Open (Open/Reopened) requests per crew from the current register: T-N (A): P1 → 1; T-S (B): P3 → 1. The counts tie, so the rule cannot decide P5. Weakness: the rule depends on a quantity that changes hour by hour (and on the boundary request's own effect on the count), so the same request can be assigned differently on two days — not reproducible, and it can tie.

**10.7.3.** Target assets, join requests within 25 m, one-to-one, keep all: **3 rows**; Join_Count: SL-0113 = 1 (P1, 7.07 m), DR-0042 = 1 (P5, 11.18 m), TR-0301 = 1 (P6, 22.36 m); all other pairs are > 25 m. The result *suggests* P5 ("Blocked drain") is about DR-0042; it is not evidence — proximity is a geometric fact, and Chapter 8's `asset_id` set by a clerk (or an inspection record) is the only thing that establishes the link.

## I.2 Answer key — 10.9 concept and scenario questions

**Q1 — (b).** A definition query limits "which features … can be … processed by geoprocessing tools" [X05], so Spatial Join processes P1 and P3 only; nothing in the stored table changes — a definition query is a layer property. (a) ignores the filter; (c) confuses a filter with a delete; (d) confuses a layer property with a data change.

**Q2.** `NOT (est_cost_inr > 2000)` → **P1, P4** (1500, 0). `est_cost_inr > 2000` → **P2, P5** (12000, 2500). Neither → **P3, P6** (unknown). Rewrite: `NOT (est_cost_inr > 2000) OR est_cost_inr IS NULL` → P1, P3, P4, P6. Credit requires the explicit statement that P4's zero is a *known* small value.

**Q3.** **6 rows** — the QGIS layer join returns "all the features in the target layer … regardless they have a match" and, for duplicate join keys, "only the first fetched feature is picked" [X24]. P1 and P2 (code A) may carry either the 2019 or the 2024 `valid_from`; P5 (null) and P6 (C) carry nulls. "First fetched" is not defined by the documentation in terms of any column, so it can depend on storage order, indexing, or caching and may differ between runs or after an edit — which is why the register should be filtered to `is_current = 'Yes'` before joining.

**Q4.** Bounding box 0–300 × 0–300 contains (250, 250) → *candidate*. `ST_Contains(Y, point)` → **false**: x > 100 and y > 100 places the point in the notch. For a *point*, `ST_Intersects` and `ST_Covers` can never differ: both are true exactly when the point lies in the polygon's interior or on its boundary [S29] [S30] (they differ only for geometries partly outside).

**Q5.** `ST_Contains(B, P5)` **false** — P5's only point lies on B's boundary, and the interiors must share a point [S28]. `ST_Covers(B, P5)` **true** — no point of P5 is in B's exterior [S29]. ArcGIS *Within* **selected** — "within or contained by", inclusive per the point/polygon table [X02]. ArcGIS *Completely within* **not selected** — the input "intersects the boundary" [X02]. QGIS *are within* **false** — the opposite of *contain*, which needs an interior point of b in the interior of a [X22]. QGIS *touch* **true** — common point on a boundary, no interior contact [X22].

**Q6.** With `units` omitted, the default is `esriSRUnit_Foot` on ArcGIS Enterprise and `esriSRUnit_Meter` on ArcGIS Online [X11], so the Enterprise call buffers P5 by 300 ft (≈ 91.4 m) and returns {P5} only, while the Online call buffers by 300 m and returns {P1, P2, P3, P5, P6} (subject to the exact-threshold behaviour for P1/P2). The second parameter is `inSR`: "If the inSR is not specified, the geometry is assumed to be in the spatial reference of the layer" [X11] — a P5 supplied as grid metres to a degree-based layer would be placed near (0°, 0°). Full credit also notes that the layer must report `supportsQueryWithDistance` for `distance` to apply [X11].

**S1.** Step (i): one-to-many spatial join, keep all → 7 rows: P1-A, P2-A, P3-B, P4-B, P5-A, P5-B, P6-(none). Step (ii): attribute join to the register (A twice, B once), matches only → P1 ×2, P2 ×2, P3 ×1, P4 ×1, P5-A ×2, P5-B ×1; P6 dropped (null key). Step (iii): T-N (A rows) = 2 + 2 + 2 = **6**; T-S (B rows) = 1 + 1 + 1 = **3**; total 9. Inflations: the duplicate register key doubles every A row (10.3.1); the inclusive one-to-many spatial join counts P5 in both wards (10.7.3); the matches-only attribute join makes P6 vanish instead of reporting it (10.3.1). Unit of 6 and 3: *request × ward-match × register-row combinations*. Corrected procedure: filter register to `is_current = 'Yes'`; spatial join one-to-one with the written policy (P5 → A by tie-break; P6 → outside); then count: **T-N 3 (P1, P2, P5), T-S 2 (P3, P4), unassigned 1 (P6); total 6 = number of requests**, the unit being *requests*. Award marks for any explicitly stated alternative policy that still totals 6.

**S2.** (a) P5 to DR-0042 = √(5² + 10²) = √125 ≈ **11.18 m** ≤ 15 → the rule fills `asset_id = 'DR-0042'`. (b) Use: transform (not assign) both layers to a suitable projected CRS in metres — EPSG:32643 for Fixture E6 — then a planar `ST_DWithin`/Near/Within-a-distance with 15 m; or the PostGIS geography type / Esri geodesic options, which take metres directly [X17] [X01] [X08]. Refuse: applying 15 in the layer's units (15 degrees), or converting 15 m to one degree figure, because degree length differs by direction and latitude (10.6.2). (c) Must specify: inclusive/strict at 15.000 m; the distance method and CRS (planar in which CRS, or geodesic); the candidate set (assets of type Drain, currently active) and the tie rule if two drains are equally near. Does not establish: that P5 *is about* DR-0042 — proximity is not identity (Chapter 9); record the fill as `match_method = 'NEAREST_15M'` so a person can override it.

## I.3 Expected results — 10.8 lab (hand-checked predictions)

**Table 10.8-A, expected values.**

| Q# | Expected | Derivation |
| --- | --- | --- |
| A1 | P1, P2 | Interior of A: 0 < x < 1000 and 0 < y < 1000; P5 has x = 1000 (boundary) |
| A2 | P3, P4 | Interior of B: 1000 < x < 2000; P5 excluded; P6 x = 2200 outside |
| A3 | P1, P2, P5 | Add boundary points: P5 on x = 1000, 0 ≤ y ≤ 1000 |
| A4 | P3, P4, P5 | Same edge belongs to B |
| A5 | P5 (touches both A and B) | P5 in neither interior; on both boundaries |
| A6 | P1, P2, P3, P5, P6 | Distances 300, 300, 250, 400, 0, 200; ≤ 300 keeps all but P4 |
| A7 | P3, P5, P6 | < 300 drops the two at exactly 300 |
| A8 | P1, P3, P5 | Status {P1, P3, P5} ∩ (A3 ∪ A4 = P1–P5) ∩ A6 |
| A9 | 8 rows keep-all (P1 ×2, P2 ×2, P3, P4, P5 null, P6 null); 6 matches-only; 4 distinct matched; 2 unmatched (null key; missing key) | Register has A twice |
| A10 | A = 3, B = 3; total 6; distinct 5 | P5 counted in both |
| Invariants | distinct matched under A3 ∪ A4 = 5; total matches = 6 | Appendix A.2.7 |

**Distances to R1** (the finite segment y = 500, 0 ≤ x ≤ 2000): for P1–P5 the perpendicular foot (x, 500) lies within the segment, so distance = |y − 500|: 300, 300, 250, 400, 0. For P6 the foot would be at x = 2200 > 2000, so distance = distance to the end vertex (2000, 500) = 200 [X09].

**Expected reconciliation findings by route** (documentation-based; confirm in I.6):

| Route | A1/A2 option | A3/A4 option | Expected difference to explain |
| --- | --- | --- | --- |
| PostGIS | `ST_Contains` | `ST_Covers` | None if mapped correctly; a learner who used `ST_Within` for A3 will miss P5 — semantics |
| QGIS | *are within* | *intersect* | A learner who used *are within* for A3 will miss P5 — semantics; *are within* + *touch* (OR) reproduces A3 |
| ArcGIS Pro | *Completely within* | *Within* or *Intersect* | A learner who used *Within* for A1 will *include* P5 — semantics (Esri's *Within* is inclusive [X02]) |

**Exact-threshold observation (A6 vs A7).** Whether P1 and P2 are returned by "within 300" is the installed engine's fact. Record it; do not mark a learner down for either outcome, only for failing to test it.

## I.4 Common mistakes and remediation

| Mistake | Symptom | Remediation |
| --- | --- | --- |
| `= NULL` or `<> 0` used to find/exclude missing values | Unknown-cost requests vanish from both "cheap" and "expensive" | Re-teach 10.2.2 with the truth table; require the "neither" row to be reported in every such query |
| Precedence ignored | `High AND Open OR Reopened` returns P3, P5 | Require parentheses in every mixed `AND`/`OR` expression |
| Summing over a multiplied join | Cost per crew 27,000 instead of 13,500 | 10.3.3 worked example C: aggregate before joining or fix the key |
| "Within" assumed strict (or inclusive) | P5 appears/disappears unexpectedly | Make the learner quote the documented definition of the option they used [X02] [X22] [S28] [S29] |
| Hole treated as interior | Tree TR-0302 "in the depot" | 10.5.1 table; the hole row |
| Metre threshold on degrees | All requests "within 300" or none | Chapter 6 + 10.6.2; rebuild with a projected CRS or geodesic method |
| Nearest without tie rule or search limit | Different assignment on rerun; P6 assigned to a ward it is outside | 10.6.1 items 3–4; require a written tie-break |
| Raw match count reported as assignment | Ward counts 3/3 with total 6 for five distinct requests | 10.7.2 policy; report raw and policy results side by side |
| Screenshot as evidence | No record of the option or condition used | Reject; require Table 10.8-C with options/SQL |

## I.5 Fresh exercise for retesting (different values, same concepts)

Planar grid, metres, no CRS, synthetic. Wards C = (0, 0)–(800, 0)–(800, 800)–(0, 800); D = (800, 0)–(1600, 0)–(1600, 800)–(800, 800). Road S1 = finite segment (0, 300)–(1600, 300). Requests: R1 (100, 100); R2 (800, 300); R3 (1200, 650); R4 (400, 600); R5 (1700, 300). Register export: C twice (2021 and 2025 rows), D once; request codes R1 C, R2 null, R3 D, R4 C, R5 'E'.

Expected: strict C = {R1, R4}, D = {R3}; inclusive adds R2 to both; R2 touches both; R5 outside. Distances to S1: R1 200, R2 0, R3 350, R4 **300 (exact)**, R5 100 (to the end vertex (1600, 300)); ≤ 300 → {R1, R2, R4, R5}; < 300 → {R1, R2, R5}. Attribute join keep-all: R1 ×2, R4 ×2, R3 ×1, R2 null, R5 null → 7 rows, 3 distinct matched. Spatial join count: C = 3 (R1, R2, R4), D = 2 (R2, R3), total 5, distinct 4.

Optional Earth-referenced extension (for learners who need 10.6.2 reinforced): repeat A6 on Fixture E6 with a 700 m threshold from the shared boundary line (75.000° E, 23.000°–23.010° N) using (i) EPSG:32643 planar and (ii) the geography/geodesic method; expected: Q1, Q2 (0 m), Q3, Q4 (≈ 512 m) in; Q5 (≈ 1,230 m) out — using Chapter 6's delivered eastings (|E − 500,000|); record any difference between (i) and (ii) (expected < 1 m at this scale, Chapter 6).

## I.6 Verification items before the lab is issued (not execution-tested)

1. **ArcGIS Pro, unknown CRS and Search Distance unit.** Confirm that Select Layer By Location accepts a *Search Distance* on layers with an unknown spatial reference (unit *Unknown*) and returns planar results; if not, document the fallback (a stated local engineering CRS in metres) and note that it is a teaching arrangement, not a Chapter 5 recommendation.
2. **ArcGIS Pro, P5 under each option.** Run *Intersect*, *Within*, *Completely within*, *Within Clementini*, and *Boundary touches* with requests as input and each ward as selecting features; record IDs. Expected per [X02]: inclusive for the first two, exclusive for the next two, P5 only for the last.
3. **ArcGIS Pro, exact threshold.** *Within a distance* 300 with P1/P2 at exactly 300 m; record inclusion. Also run Near [X08] and confirm `NEAR_DIST` = 300.000 for P1/P2 and 200.000 for P6.
4. **ArcGIS Pro, Spatial Join defaults.** Confirm one-to-one/keep-all/Intersect defaults and the default text merge rule; confirm `Join_Count` A = 3, B = 3 for target = wards and the 7-row one-to-many output for target = requests [S31].
5. **ArcGIS Pro, Add Join.** With the register as a CSV (no ObjectID), confirm that only a one-to-first join is available [X03] and record which A row P1 receives; then load the register into the same file geodatabase and confirm the one-to-many result (8 rows keep-all).
6. **QGIS, planar layers.** Confirm the delimited-text dialog labels, how to load a WKT layer with no CRS, and that *Select within distance* uses layer units; run *are within*, *touch*, *intersect* for P5; run *Join attributes by field value* with both `METHOD` values; run *Join attributes by nearest* with two equidistant candidates to observe the (undocumented) tie behaviour [X22] [X23].
7. **PostGIS.** Execute the Route A script; confirm `ST_Distance` returns exactly 300 for P1/P2 and record `ST_DWithin(…, 300)` for them; confirm `ST_Covers`/`ST_Contains`/`ST_Touches` for P5 match 10.5.2.
8. **REST API (optional demonstration).** If a hosted copy of the requests layer exists, issue the Q6 query with and without `units` and record the counts; do not publish for this purpose alone (credits, Chapter 2).

## I.7 Blueprint corrections, source notes, and verification items

- **No factual error was found in the blueprint's Chapter 10 text or Appendix A.2.** Its fixture answers (strict A = P1/P2, B = P3/P4; inclusive adds P5 to both; ≤ 300 m → P1, P2, P3, P5, P6; six matches, five distinct) were re-derived here and agree.
- **Source note — Esri "Within" is inclusive.** The blueprint (10.5.2) rightly says the PostGIS contains/covers distinction "is a named implementation example, not a universal claim about every tool labeled 'contains'". Reading [X02] shows the concrete case: Esri's *Within*/*Contains* correspond to PostGIS covers/covered-by, and *Completely within*/*Within Clementini* to PostGIS within. The chapter states this with the documentation's wording rather than as a tested result (I.6 item 2).
- **Source note — exact threshold.** Neither [X17] nor [X01] states whether "within a distance" is ≤ or <; the chapter treats it as a per-engine verification item and designs the lab (P1/P2 at exactly 300 m) to expose it.
- **Source note — QGIS nearest-join ties.** [X23] documents no tie rule; the chapter says so rather than inferring one.
- **Source note — REST default units.** [X11] documents different defaults for ArcGIS Online (metres) and ArcGIS Enterprise (feet); the chapter uses this as a developer example. Recheck on each release.
- **Chapter 9 recap** was written from the blueprint's Chapter 9 description because the Chapter 9 document had not been issued when this chapter was drafted; align fixture names when it is.
- **Reused earlier-chapter values.** Chapter 1's distances and Chapter 6's E6 figures are reused as stated there and re-checked arithmetically here (I.3); the E6 UTM coordinates were not recomputed.

## I.8 Instructor change log

| Date | Change | Affected sections |
| --- | --- | --- |
| 2026-09-19 | Initial release, revision 1.0. References checked against ArcGIS Pro 3.7 "latest" pages, PostGIS 3.6.5dev manual, QGIS 3.44 user guide, PostgreSQL "current" documentation, ArcGIS REST API reference, ArcGIS Online help. | All |

---

# Media Appendix

## M.1 Slide outline (mapped to module IDs)

| Slide group | Module | Learning objective for the sequence | Visual | Question before the answer |
| --- | --- | --- | --- | --- |
| 1 | Front matter | State the chapter's one habit: predict, execute, reconcile | Diagram D0 (fixture) with the blank worksheet beside it | "How many requests are 'in' Ward A?" — leave unanswered until slide 8 |
| 2 | 10.1 | Separate filter, selection, export, modification | The four-row table of 10.1.1 as four icons: a lens, a highlighter, a copy, a pen | "Which of the four changed the stored table?" |
| 3 | 10.2.1 | Read and write conditions on F10-1 | Table F10-1 with rows highlighted per condition, animated one condition at a time | "What does `%tree%` match?" |
| 4 | 10.2.2 | Null is neither true nor false | The six-row truth table with the two unknown rows greyed out in *both* columns | "1 + 3 = 4; the table has 6. Where are the other two?" |
| 5 | 10.2.3 | Syntax is source-dependent | Three columns (file geodatabase / SQLite-GeoPackage / PostgreSQL) with the same date literal written three ways | "Which of these runs on your source?" |
| 6 | 10.3 | Joins multiply and drop rows | Diagram D4: requests on the left, register on the right, lines for each match; P1 and P2 with two lines, P5 and P6 with none | "How many rows? How many requests?" |
| 7 | 10.4.1–10.4.2 | Six relationships; predicate versus operation | Diagram D1 six panels; then a split slide "intersects? → true" vs "intersection → a 1,000 m segment" | "Which panel is P5 and Ward A?" |
| 8 | 10.4.3 | Boxes find candidates | Diagram D2 (L-shape) | "Is K inside?" |
| 9 | 10.5.1–10.5.2 | Interior, boundary, hole, exterior | Diagram D3: DP-01 with H1–H4 and the four-predicate table filled progressively | "Fill in H2's row before I show it" |
| 10 | 10.5.3 | Same word, different rule | Table M.4 | "Which column would drop P5?" |
| 11 | 10.6.1 | Nearest ≠ within a distance | Worked example 10.6-A as a distance matrix; P6's 200 m to the end vertex drawn | "Why is P6 not 0 m from the road?" |
| 12 | 10.6.2 | Metres are not degrees | The 0.003° "circle" drawn as an ellipse over Fixture E6 with 332 m and 307 m labelled | "Which side is longer?" |
| 13 | 10.6.3 | Z storage ≠ 3D query | Diagram D5 (bridge) | "Does the bridge intersect the road?" |
| 14 | 10.7.1 | Three testable conditions | The C1/C2/C3 table and the sensitivity table | "Change one assumption; what changes?" |
| 15 | 10.7.2 | Policy, not predicate | The assignment table with the clerk's codes beside it | "Which two rows disagree, and why is that good?" |
| 16 | 10.7.3 | Spatial join counts matches | Worked examples 10.7-B/C side by side; Join_Count total 6, distinct 5 | "Where did the sixth match come from?" |
| 17 | 10.8 | Lab briefing | Table 10.8-B | — |
| 18 | 10.9 | Gate | Critical misconceptions list | — |

Speaker notes for every slide: name the module ID, state the assumption in force (planar metres; inclusive unless stated), and read the citation for any quoted definition aloud.

## M.2 Audio-lesson outline

Pronounce consistently: "S-Q-L", "P-five", "PostGIS" (post-jiss), "DE-9-I-M" is *not* used in narration (say "the interior-boundary-exterior model"). Introduce units the first time a number is spoken ("three hundred *metres*").

1. **Opening (10.1).** Describe Diagram D0 in words: "Picture graph paper. Two squares side by side, each a kilometre across, sharing one vertical line. A road runs straight across both, halfway up, and stops at the right-hand edge of the second square. Six dots: two inside the left square, two inside the right, one exactly where the road crosses the shared line, and one past the end of the road." Then the four actions, each with the question "did the table change?"
2. **Conditions (10.2).** Read three conditions and pause four seconds before each answer. For the null table, say: "one row is expensive, three are cheap, and two are neither — say 'neither' out loud whenever you report a count."
3. **Joins (10.3).** Narrate P1's two register rows: "P1 says ward A; the register has ward A twice — once for 2019, once for 2024 — so P1 appears twice. P5 says nothing, so it matches nothing. Eight rows for six requests."
4. **Predicates (10.4).** For each of the six panels: name the shapes, say the plain-language test, and give the answer for the fixture pair. Distinguish "intersects, question mark" from "intersection, a new shape".
5. **Boundary (10.5) — the principal segment.** Walk one point around the depot: "Start inside: contains yes, covers yes, intersects yes, touches no. Step onto the outer wall: contains *no* — the definitions say a polygon does not contain its own boundary — covers yes, intersects yes, touches yes. Step into the courtyard: everything no — a hole is outside. Step over the outer wall to the street: everything no." Pause before each position. Then: "In ArcGIS Pro the option called *Within* would say yes on the wall; the option called *Completely within* would say no. Neither is wrong. Read the definition."
6. **Distance (10.6).** Read the nearest-asset distances for P5 and P6; explain the end-vertex rule for P6; state the tie rule sentence from Esri verbatim; then the degrees warning with the 332/307 m ellipse.
7. **Combination and policy (10.7).** Read the three conditions, the three IDs, and the sensitivity table. End with the policy sentence for P5.
8. **Lab hand-off.** Keep click instructions out of the audio; say "predict first, date your prediction, then open the handout."

## M.3 Diagram specifications

All diagrams: schematic, synthetic training grid, axes labelled "x (metres)" and "y (metres)", captioned "Synthetic training grid; no real-world location". Labels are ID text; do not rely on colour alone.

- **D0 — Fixture map.** Canvas 2,400 × 1,200 units for x −100…2,300, y −100…1,100. Ward A and B outlined; shared edge emphasised; R1 as a thick line with a square end cap at (2000, 500); P1–P6 as circles with labels; assets as small triangles labelled SL-0113 (205, 195), DR-0042 (995, 510), TR-0301 (2190, 520). Inset table: distances to R1.
- **D1 — Six relationships.** Six panels, each 600 × 600 units, Ward A drawn in all: (1) R1 crossing A; (2) A and P6 far apart; (3) A with P1 inside; (4) A and B sharing the edge, the edge highlighted; (5) A and a dashed square shifted 300 m east with the overlap hatched; (6) P2 with three dashed lines to the assets, the shortest solid and labelled "349.46 m". Under each panel: the predicate name and "true for this pair".
- **D2 — L-shape and box.** Y hatched; bounding box dashed; K at (200, 200) in the notch; a second point (50, 50) inside; legend "box: candidate; geometry: answer".
- **D3 — Four positions.** DP-01 with outer and inner rings; H1 (1420, 620), H2 (1400, 700) on the outer ring, H3 (1500, 700) in the hole, H4 (1700, 700) outside, H5 (1450, 700) on the inner ring; the 5 × 4 predicate table beside it, cells filled true/false; the hole shaded the same as the exterior to make the point visually.
- **D4 — Join inflation.** Left column: six request cards with `ward_code`; right column: three register cards; connecting lines: P1→A(2019), P1→A(2024), P2→A(2019), P2→A(2024), P3→B, P4→B; P5 and P6 with a struck-through line ending in "no match (null)" and "no match (C)". Counter at the bottom: "8 rows, 6 matches, 4 requests matched, 2 unmatched".
- **D5 — Bridge, 2D versus 3D.** Two panels: top-down view with two crossing lines and "intersects (2D): true"; oblique view with the bridge line 6 m above and "3D intersects: false". Label "Z is schematic; no vertical reference asserted".

## M.4 Table for slides — the same word in four engines (from 10.5.3)

| Concept (point on polygon boundary, e.g. P5 and Ward A) | PostGIS geometry | QGIS Select by location | ArcGIS Pro Select By Location / Spatial Join | ArcGIS REST `spatialRel` |
| --- | --- | --- | --- | --- |
| Any contact | `ST_Intersects` **true** [S30] | *intersect* **true** [X22] | *Intersect* **selected** [X02] | `esriSpatialRelIntersects` (definition not restated on the query page; treat as intersects [X11]) |
| Strict interior | `ST_Contains(A, P5)` / `ST_Within(P5, A)` **false** [S28] [X13] | *contain* / *are within* **false** [X22] | *Completely within*, *Within Clementini* **not selected** [X02] | `esriSpatialRelWithin` / `Contains` — **verify**; the page lists values without boundary semantics [X11] |
| Inclusive | `ST_Covers(A, P5)` **true** [S29] | (no covers option) use *intersect* | *Within*, *Contains* **selected** [X02] | — |
| Boundary only | `ST_Touches` **true** [X14] | *touch* **true** [X22] | *Boundary touches* **selected** [X01] | `esriSpatialRelTouches` — verify [X11] |

## M.5 Interactive demonstration — "Move the point" (2D; no 3D needed)

- **Learning objective.** The learner can predict, for any position of one point relative to a polygon with a hole, the value of each named predicate under stated rules, and can see that two products label the same rule with different words.
- **Objects.** The training grid (metres, schematic). Polygon DP-01 as in D3 (outer ring (1400, 600)–(1600, 800); inner ring (1450, 650)–(1550, 750)). One draggable point, default at (1420, 620). Snap targets at H1–H5 and at the courtyard centre so that the exact boundary positions can be reached.
- **Labels and units.** Point coordinates shown live as "(x, y) m"; a position badge: *interior*, *outer boundary*, *inner boundary (hole ring)*, *hole (exterior)*, *exterior*.
- **Rules engine (named).** The predicate values are computed from the **OGC Simple Features definitions as documented by PostGIS** [S28] [S29] [S30] [X14] [X15]: *contains* = no point of B outside A and interiors share a point; *covers* = no point of B outside A; *intersects* = any common point; *touches* = common points only on boundaries; *disjoint* = no common point. Implemented as explicit point-in-ring arithmetic on the schematic geometry — not by calling any GIS library — so that the displayed rules are exactly the stated ones. A visible note: "Results follow the quoted PostGIS definitions; ArcGIS Pro column shows the option names documented in [X02] mapped to the same rules; verify in your installed software."
- **Table.** Live columns: PostGIS `ST_Contains`, `ST_Covers`, `ST_Intersects`, `ST_Touches`, `ST_Disjoint`; ArcGIS Pro *Completely within*, *Within*, *Intersect*, *Boundary touches*; QGIS *are within*, *intersect*, *touch*.
- **Controls.** Drag; keyboard nudge by 1 m; "snap to H1…H5"; toggle "show hole as exterior shading"; toggle "show bounding box" (the box-candidate lesson from 10.4.3 — with the point in the hole, the box says candidate and every predicate says no).
- **Expected behaviour and validation cases.** H1 → T, T, T, F, F; H2 → F, T, T, T, F; H3 → F, F, F, F, T; H4 → F, F, F, F, T; H5 → F, T, T, T, F. ArcGIS columns at H2: not selected, selected, selected, selected. QGIS columns at H2: false, true, true. These five cases are the acceptance test for the build.
- **2D/static alternative.** Diagram D3 with its table is the printed equivalent; the audio segment 5 in M.2 is the spoken equivalent.
- **Explicitly not built.** No 3D scene: the bridge lesson (10.6.3) is a two-panel static diagram (D5) because the concept is "the 2D predicate never reads Z", which a table teaches better than a scene.
