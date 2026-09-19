# GIS and ArcGIS Fundamentals — Phase 1

# Chapter 1 — GIS fundamentals and spatial thinking

## Document metadata

| Item | Value |
| --- | --- |
| Chapter ID | Phase 1, Chapter 1 |
| Title | GIS fundamentals and spatial thinking |
| Source specification | `GIS_Phase_1_Chapter_Authoring_Blueprint.md`, revision 1.0 (18 September 2026) |
| Document revision | 1.0 — 18 September 2026 |
| Reference-check date | 18 September 2026 (all cited pages were opened and read on this date) |
| Audience | Software developers at LiveBird Technologies with no GIS background |
| Software referenced | ArcGIS Pro (documentation labelled "Released version: ArcGIS Pro 3.7" on the check date); QGIS Desktop 3.40 (LTR documentation edition); the QGIS *Gentle Introduction to GIS* 3.44 edition named in the blueprint |
| Execution status | **No procedure in this chapter has been executed in ArcGIS Pro or QGIS by the author.** Interface steps were written from the official pages cited beside them and are marked *not execution-tested*. All fixture geometry answers were checked by hand arithmetic (shown in the Instructor Appendix), not by running software. |
| Data status | Every coordinate, record, count, and date in this chapter is **synthetic training material**. Nothing describes a real municipality, resident, or asset. |

### How to use this document

Read the modules in order; each one assumes the ones before it. Boxes labelled **Procedure (version-specific)** contain interface steps that will age; everything outside those boxes is durable concept. Comprehension checks have no answers in the learner text; answers are in the Instructor Appendix at the end, which learners should not open before the progression gate in 1.8.

---

## Prerequisites

- General software literacy: you can install an application, open a file, and read a spreadsheet.
- Basic programming and relational-database familiarity (tables, rows, columns, primary keys, `WHERE` filters). These are used as analogies, not taught.
- **No GIS experience is expected.** This is the entry chapter of the curriculum.

## Learning outcomes

By the end of this chapter you should be able to:

| ID | Outcome | Where it is taught | Where it is checked |
| --- | --- | --- | --- |
| LO1 | Explain what a GIS is and does beyond drawing a map, naming its components and the four functions of capture, storage, analysis, and communication. | 1.2 | 1.8 concept Q1–Q2 |
| LO2 | Take a business decision and frame one or more answerable spatial questions, with every vague term ("near", "unresolved", "urgent", "service area") given a testable definition. | 1.1, 1.3 | 1.7 lab; 1.8 scenario Q1, practical |
| LO3 | Classify a question as being about location, distribution, proximity, containment, connectivity, or change through time, and distinguish absolute from relative location and straight-line from travel distance. | 1.3 | 1.8 concept Q3–Q4 |
| LO4 | For a given question, list the data required (geometry, attributes, time), state who produced it and what it omits, and separate events, assets, and boundaries. | 1.4 | 1.7 lab; 1.8 scenario Q2 |
| LO5 | Navigate a prepared map (layer visibility, pan, zoom, select) and explain why hiding a layer is not deleting data. | 1.5 | 1.5 observation log; 1.8 concept Q5 |
| LO6 | Identify at least one important limitation of a spatial conclusion and propose an alternative explanation before asserting a cause. | 1.6 | 1.8 concept Q6, practical, oral |

## Required materials

- This document and a way to draw a sketch (paper or any drawing tool).
- **Optional for module 1.5:** access to ArcGIS Pro **or** QGIS Desktop with the instructor-prepared Chapter 1 project (see Instructor Appendix, section I.5, for how it is built). If neither is available, module 1.5 can be completed with the described walk-through and the printed fixture; the chapter's lab (1.7) needs no software at all.

## Recap of the preceding chapter

There is none; this is Chapter 1. What *is* assumed is ordinary developer experience: you know that a database row can hold several typed columns, that a view over a table does not change the table, and that a function has inputs, rules, and outputs. This chapter deliberately reuses those ideas.

## The recurring scenario (used throughout Phase 1)

A fictional municipality maintains **assets** (streetlights, drains, trees), keeps **road** centrelines, divides its territory into **wards**, and receives **service requests** from the public. Inspection teams visit requests and record **inspections**. All five nouns — `asset`, `inspection`, `request`, `road`, `ward` — are used with exactly these meanings in every chapter.

**Chapter 1 fixture (synthetic).** The blueprint's planar fixture (Appendix A.2) is a flat, two-dimensional training grid measured in **metres**, with coordinates written as **(x, y)**, x increasing to the right and y increasing upward. It has **no Earth location and no coordinate-system identifier**; treat it like graph paper. The ward squares, one road, and six requests are fixed by the blueprint. For this chapter only, descriptive fields (status, priority, dates, reporting channel) and three assets have been added so that the *meaning* of records can be discussed. Later chapters may add other fields; they will say so explicitly.

**Table F1 — Wards (synthetic).**

| Ward | Corner coordinates (x, y), metres | Area |
| --- | --- | --- |
| A | (0, 0), (1000, 0), (1000, 1000), (0, 1000), back to (0, 0) | 1,000,000 m² = 1 km² |
| B | (1000, 0), (2000, 0), (2000, 1000), (1000, 1000), back to (1000, 0) | 1,000,000 m² = 1 km² |

Wards A and B share the vertical edge x = 1000 between y = 0 and y = 1000.

**Table F2 — Road (synthetic).** Road R1, "Main Road", is the finite straight segment from (0, 500) to (2000, 500). It is 2,000 m long and does **not** extend beyond x = 2000.

**Table F3 — Service requests (synthetic; Chapter 1 variant).** Times are local; the date format is `YYYY-MM-DD`.

| ID | (x, y) | Category | Priority | Status | Reported | Closed | Channel | Note |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| P1 | (200, 200) | Streetlight out | Medium | Open | 2026-09-02 | — | Mobile app | |
| P2 | (800, 800) | Pothole | High | In progress | 2026-08-28 | — | Phone | Crew assigned 2026-09-01 |
| P3 | (1200, 250) | Water leak | High | Open | 2026-09-10 | — | Mobile app | |
| P4 | (1700, 900) | Pothole | Low | Resolved | 2026-08-15 | 2026-08-20 | Web form | |
| P5 | (1000, 500) | Blocked drain | Medium | Reopened | 2026-08-30 | — | Phone | Closed 2026-09-05; reopened 2026-09-12 after a second call |
| P6 | (2200, 500) | Fallen tree | High | Closed – duplicate | 2026-09-11 | 2026-09-12 | Phone | Marked as duplicate of a report not in this package |

**Table F4 — Assets (synthetic; Chapter 1 only).**

| Asset ID | Type | (x, y) | Installed | Last inspected | Condition |
| --- | --- | --- | --- | --- | --- |
| SL-0113 | Streetlight | (205, 195) | 2018 | 2026-03-14 | Fair |
| DR-0042 | Drain | (995, 510) | 2011 | 2025-11-02 | Poor |
| TR-0301 | Tree | (2190, 520) | 2005 | — | Unknown |

**Hand-checked geometric facts** (derivation in Instructor Appendix I.4): straight-line distances from each request to Road R1 are P1 = 300 m, P2 = 300 m, P3 = 250 m, P4 = 400 m, P5 = 0 m, P6 = 200 m (to the road's end point). P1 and P2 lie strictly inside Ward A; P3 and P4 strictly inside Ward B; P5 sits exactly on the shared boundary and on the road; P6 lies outside both wards.

---

## 1.1 Start with a decision, not a map

### 1.1.1 The recurring question, and what a GIS can and cannot decide

The question that runs through Phase 1 is:

> **"Which unresolved service requests should an inspection team investigate first?"**

Read it carefully. It is a *decision*, and a decision has two parts that are easy to confuse:

1. **Operational policy** — the rules the organisation chooses. "High-priority requests go first." "Requests older than ten days are escalated." "A team only works inside its own ward." "A crew can visit at most eight sites per shift." These rules are decided by people with authority; no software can derive them from data.
2. **Evidence** — facts about the world that the rules need in order to be applied. *Where* is each request? *How far* is it from the road the crew will use? *Which ward* is it in? *How old* is it? *How many* are in each area? A GIS exists to produce, store, and check exactly this kind of evidence.

A developer will recognise the split: policy is the business logic; evidence is the data the logic reads. The practical consequence is that **a GIS answers the question you actually asked, so the question has to be precise before the software is opened.** If the policy says "investigate requests near Main Road first", the GIS can list the requests within a stated distance of Main Road, but it cannot tell you what "near" should mean. That is the subject of 1.1.2.

**Table 1.1 — Separating policy from evidence in the recurring question.**

| Part of the decision | Example from the scenario | Who supplies it | Can a GIS produce it? |
| --- | --- | --- | --- |
| Which requests are eligible at all | Only those with an unresolved status | Policy owner | No — but the GIS can *apply* the definition once given |
| Order of priority | High before Medium before Low; older before newer | Policy owner | No |
| Where each request is | P3 is at (1200, 250) | Data (from the reporting channel) | Yes — stores and displays |
| Distance from the road the crew drives | P3 is 250 m from R1 | Derived by the GIS from geometry | Yes — computes |
| Which ward contains each request | P3 is in Ward B | Derived by the GIS from geometry | Yes — computes, given a boundary rule |
| How many unresolved requests each ward has | Ward A: 2 (P1, P2) | Derived by the GIS | Yes — counts, given the definitions |
| Whether a crew *should* cross a ward boundary | — | Policy owner | No |

**Developer analogy.** A GIS is to spatial evidence what a reporting database is to financial evidence: it can tell you the balance, the trend, and the outliers; it cannot tell you whether to approve the loan. The analogy breaks in one place: in a GIS the *definitions* of the evidence (what counts as "inside", "near", "the same place") are themselves geometric choices that must be made explicitly, as you will see next. A financial `SUM` has one meaning; "distance to a road" does not.

### 1.1.2 Why vague words produce ambiguous results

Take the four words in the recurring question and its usual policy phrasing — **near**, **urgent**, **unresolved**, **service area** — and try to apply each to the fixture. Every one of them changes the answer depending on a definition nobody has written down.

**"Near" Main Road.** The only measurable version of "near" in a GIS is a distance and a rule for comparing it. Using the hand-checked distances above:

| Definition of "near" | Qualifying requests | Count |
| --- | --- | --- |
| Distance ≤ 300 m (boundary-inclusive) | P1, P2, P3, P5, P6 | 5 |
| Distance < 300 m (strict) | P3, P5, P6 | 3 |
| Distance ≤ 250 m | P3, P5, P6 | 3 |
| Distance ≤ 200 m | P5, P6 | 2 |

Two requests, P1 and P2, sit at *exactly* 300 m. Whether "within 300 m" includes them is not a GIS question; it is a definition the policy owner must supply. Notice also that P6 qualifies under every threshold although it lies outside both wards, because the road's end point at (2000, 500) is only 200 m away. If the policy silently assumed "near the road *and* inside our wards", the GIS result is wrong — not because the software erred, but because a condition was never stated. (The blueprint fixes this exact fixture behaviour: with a boundary-inclusive 300 m threshold the qualifying IDs are P1, P2, P3, P5, and P6; adding a ward-membership condition removes P6.)

**"Unresolved."** Table F3 has five distinct status values. Which are "unresolved"?

| Status | Clearly unresolved? | Why it is arguable |
| --- | --- | --- |
| Open | Yes | — |
| In progress | Probably | Somebody is already working on it; should it still be "investigated first"? |
| Reopened | Yes | But it has a history; is it more urgent than a fresh Open request? |
| Resolved | No | — |
| Closed – duplicate | Ambiguous | The *record* is closed, but the underlying problem (P6's fallen tree) may be open under another ID that is not in this package |

Depending on the answer, the unresolved set is {P1, P3, P5} (Open + Reopened only), {P1, P2, P3, P5} (add In progress), or {P1, P2, P3, P5, P6} (count duplicates too). The GIS will happily filter on any of these; it cannot choose between them.

**"Urgent."** The fixture has a `Priority` field, a `Category` field, and dates. "Urgent" could mean Priority = High ({P2, P3, P6}); or a category such as *Water leak* regardless of priority; or *age* — the oldest unresolved request is P2 (reported 2026-08-28). Three defensible readings, three different lists.

**"Service area."** Is the service area the union of Wards A and B, or the wards plus a margin? P6 at (2200, 500) is 200 m east of Ward B's edge. Under a strict reading it is outside the service area; under "within 250 m of our wards" it is inside.

The lesson is general and applies to every GIS platform: **before any spatial operation is chosen, each vague term must be turned into (a) a measurable quantity, (b) a comparison rule including whether the boundary value counts, and (c) a stated unit.** Chapters 10 and 11 return to the mechanics of these rules; for now, your job is to notice when a term is undefined and to write the definition down.

**Misconception.** *"The GIS will use a sensible default for 'near'."* Tools do have defaults (a default distance unit, a default boundary rule), but a default is not a decision. A result produced under an unexamined default is unexplainable to the decision-maker and cannot be reproduced by a colleague using a different tool.

### 1.1.3 The same records as a table and as a map

Below are the six requests as a plain table (location-free) and then as a described map. Before reading the comparison, ask yourself which questions each one makes easy.

**Table 1.2 — The request table with the coordinates removed.**

| ID | Category | Priority | Status | Reported | Channel |
| --- | --- | --- | --- | --- | --- |
| P1 | Streetlight out | Medium | Open | 2026-09-02 | Mobile app |
| P2 | Pothole | High | In progress | 2026-08-28 | Phone |
| P3 | Water leak | High | Open | 2026-09-10 | Mobile app |
| P4 | Pothole | Low | Resolved | 2026-08-15 | Web form |
| P5 | Blocked drain | Medium | Reopened | 2026-08-30 | Phone |
| P6 | Fallen tree | High | Closed – duplicate | 2026-09-11 | Phone |

**Described map (Diagram D1, specified in the Media Appendix).** Two equal squares side by side, labelled Ward A (left) and Ward B (right), sharing a vertical edge. A horizontal line — Main Road R1 — runs across both squares at half height and stops at the right edge of Ward B. Six labelled dots: P1 low-left inside A; P2 high-right inside A; P3 low inside B, a little right of the shared edge; P4 high-right inside B; P5 exactly where the shared edge crosses the road; P6 to the right of both squares, in line with the road, in empty space.

**Table 1.3 — What each representation makes easier.**

| Question | Easier in the table | Easier on the map | Comment |
| --- | --- | --- | --- |
| How many requests have Priority = High? | ✔ | | A sort or a filter answers this instantly; on the map you would have to read each label. |
| Which request is oldest? | ✔ | | Dates are text; the map does not show them unless specially styled. |
| Which requests are close to Main Road? | | ✔ | Visible at a glance; the table has no notion of "close". |
| Is any request outside our wards? | | ✔ | P6 stands out immediately; in the table it is invisible. |
| Is any request *on* a ward boundary? | | ✔ (with care) | P5 looks "on the line" but a drawn symbol cannot prove exact coincidence; the coordinates can. |
| Which requests are in Ward B? | | ✔ | But only because the wards are drawn; the table has no ward column for P5 and P6. |
| Exactly how far is P3 from the road? | | | Neither: the map shows *roughly*, the table shows nothing. A computed value is needed — that is the "analysis" part of a GIS (1.2). |

Two points matter here. First, **the map is not always the better display**: for counting, sorting, and reading exact values, a table wins, and many GIS tasks end with a table. Second, the map reveals *relationships between records* (nearness, containment, outliers) that the table stores only implicitly in the coordinate columns. A GIS is the thing that lets you switch between the two views of the same records and, crucially, *compute* the relationships the map only suggests.

**Comprehension check 1.1.** The policy owner says: "Send the team to the urgent requests near the road in our service area." Write down every term in that sentence that needs a definition before the six-record fixture can be filtered, and give one concrete definition for each. Then state which requests your definitions select.

---

## 1.2 Explain what a GIS includes

### 1.2.1 Components and functions

The QGIS *Gentle Introduction* defines a GIS ("Geographical Information System") as consisting of **digital data**, **computer hardware**, and **computer software**, and immediately adds that "GIS is more than just software, it refers to all aspects of managing and using digital geographical data" [S01, §2.1–2.2]. Esri's GIS Dictionary describes a GIS as "an integrated collection of computer software and data used to view and manage information about geographic places, analyze spatial relationships, and model spatial processes" [X01]. Most teaching frameworks, including this curriculum, add a fourth component — **people and processes**: the staff who collect, check, maintain, and interpret the data, and the procedures they follow. That fourth component is a teaching framing rather than a claim from [S01]; it is added because, in practice, most GIS failures are procedural (stale data, undocumented definitions) rather than technical.

**Table 1.4 — Components of a GIS, with the scenario mapped onto them.**

| Component | What it is | In the municipal scenario | Developer counterpart |
| --- | --- | --- | --- |
| Geographic data | Records that carry a location plus descriptive values [S01, §2.5] | Requests, assets, roads, wards, inspections | Tables with a location-typed column |
| Software | Programs that let you view, edit, analyse, and output that data; "a software program that forms part of the GIS is called a GIS Application" [S01, §2.1] | Desktop GIS, web maps, field apps, the code you will write | Applications, libraries, services |
| Hardware | Machines that store, process, and display [S01, §2.1] | Laptops, servers, phones used in the field | Infrastructure |
| People and processes | Who captures, validates, maintains, and interprets; and the rules they follow | Call-centre staff, inspectors, data stewards, the policy owner | Operations, data governance |

The functions a GIS performs are usually summarised as four verbs. [S01] states them concretely: with a GIS application "you can open digital maps on your computer, create new spatial information to add to a map, create printed maps customised to your needs and perform spatial analysis" [S01, §2.1].

1. **Capture** — get location and description into the system (typing coordinates, tapping a phone, tracing from imagery, importing a file).
2. **Store** — keep it in files or a database so that geometry and attributes stay linked and can be retrieved.
3. **Analyse** — compute new facts from stored ones: distances, containment, counts by area, overlaps.
4. **Communicate** — present results as maps, tables, charts, or answers inside another application.

**Developer analogy.** A GIS is a *data platform*, not an application: ingestion (capture), storage, a query/compute layer (analysis), and presentation (communication), with an operations team around it. Where the analogy stops: in a general data platform the compute layer works on values that already have a fixed meaning; in a GIS the compute layer must also know *how space is measured* (units, reference frame, boundary rules — Chapters 5, 6, and 10). Two identical-looking GIS datasets can give different distances because they were measured differently, which has no counterpart in ordinary tabular data.

### 1.2.2 GIS versus a map image, a navigation app, and an address table

Things that look like GIS often are not, and the difference matters when a client says "we already have GIS".

**Table 1.5 — Four things that show locations.**

| Artefact | Can you ask it a new question? | Can you change what is shown? | Does it link descriptive values to places? | Can it compute relationships (distance, containment)? | Verdict |
| --- | --- | --- | --- | --- | --- |
| Static map image (PNG/PDF of the wards) | No | No | Only as printed text | No | Output *of* a GIS, not a GIS |
| Navigation app (turn-by-turn) | Only the questions it was built for (route, ETA) | Limited | Internally, yes; but you cannot add your own | Yes, but only its own | A GIS-*powered* product with a fixed purpose |
| Table of addresses (spreadsheet) | Yes, for attributes; no, for space | Yes | Yes — but the "place" is text, not a position | No | An *input* to a GIS once locations are made computable |
| GIS | Yes | Yes | Yes | Yes | — |

The address table is the interesting case because it is where most developers start. [S01] uses exactly this progression: a health worker's table has longitude and latitude columns holding "geographical data" and disease and date columns holding "non-geographical data"; loaded into a GIS application, the rows become a layer and "we can quickly understand a lot more about the patterns of illness" — for instance that "the mumps patients all live close to each other" [S01, §2.1, §2.5].

For a table to become GIS input, three things must be true:

1. **Each row has a location that can be turned into a position.** Coordinates are the direct case (the fixture's x, y). A street address is *not* yet a position; converting it is a separate, error-prone step called geocoding (Chapter 9).
2. **The positions are interpretable.** Somebody must know what the numbers mean — the units and reference frame. The fixture avoids this by declaring itself a flat metre grid; real data cannot (Chapter 5).
3. **The descriptive columns keep their meaning after import.** Types, null values, and identifiers must survive; that is a format question (Chapter 7).

When those hold, the table becomes a **layer**: the same rows, now drawable and spatially queryable. Nothing about the rows themselves changed; what changed is that the software can now treat one or two columns as *space*.

**Misconception.** *"A PDF map of the wards is our GIS data."* It is a picture. It cannot be filtered, its positions cannot be measured reliably, and it carries none of the attribute values that produced it. If the PDF is all that exists, the wards will have to be captured again (Chapter 9).

### 1.2.3 The question-to-decision workflow

This is the working method for the whole course. It is *not* a product architecture — every step can happen in different software, or on paper — and Chapter 2 will ask where each step can run.

**Diagram D2 (described; specification in Media Appendix).** Eight boxes in a loop, left to right, with an arrow returning from the last to the first:

1. **Decision** — what somebody will do differently.
2. **Question** — an answerable spatial question with every term defined.
3. **Data inventory** — which datasets are needed, who owns them, when they were last updated, what they omit.
4. **Selection rules** — the filters and spatial conditions written out, including units and boundary treatment.
5. **Operation** — the computation (a filter, a distance test, a count by area).
6. **Validation** — checks that the output is *plausible* and *reproducible* before anyone acts on it.
7. **Communication** — a map, a table, or a number, with the definitions and limitations attached.
8. **Decision** (again) — and often a refined question.

Step 6 is the one beginners skip and the one this course insists on. Validation does not mean "the tool ran without error". It means questions such as: Is the count within the range I expected? Did I spot-check one record by hand? Does the output cover the whole study area, or did a filter silently drop rows? Would a colleague get the same answer from the same definitions?

**Worked example 1.2 — one pass through the loop with the fixture.**

- **Decision:** the inspection team lead will assign tomorrow's first visits.
- **Question:** "Which requests with status Open or Reopened, reported on or before 2026-09-15, lie within 300 m (boundary-inclusive) of Road R1 and inside Ward A or Ward B (boundary-inclusive)?"
- **Data inventory:** Table F3 (requests, 6 rows, reported 2026-08-15 to 2026-09-11, from three channels), Table F2 (road R1), Table F1 (wards). Known omission: P6 is marked a duplicate of a record not in this package.
- **Selection rules:** status ∈ {Open, Reopened}; reported ≤ 2026-09-15; distance to R1 ≤ 300 m; inside A ∪ B, with the shared boundary counted as inside.
- **Operation:** apply the status/date filter (→ P1, P3, P5), then the distance test (P1 = 300 ✔, P3 = 250 ✔, P5 = 0 ✔), then the ward test (P1 in A ✔, P3 in B ✔, P5 on the shared edge — inside by the stated rule ✔).
- **Answer:** P1, P3, P5.
- **Validation:** count is 3 of 6, plausible. Spot check: P2 was excluded by status (In progress), not by geometry — it is also 300 m from the road, so if the policy owner changes the status definition it will enter the set; note this. P6 was excluded twice (status and ward); its distance alone would have qualified it. Reproducible: another person applying these exact rules to Table F3 must get {P1, P3, P5}.
- **Communication:** a three-row table with the definitions printed above it, plus a one-line note on P2 and P6.
- **What the answer does not establish:** it does not say P3 is more urgent than P1 (that is policy), nor that the crew can *reach* any of them quickly (1.3.3), nor that the six records are all the requests that exist (1.4.3).

**Comprehension check 1.2.** Name the four functions of a GIS and, for each, give one concrete action from the municipal scenario. Then explain in one sentence why a spreadsheet of addresses is not yet a GIS dataset.

---

## 1.3 Introduce spatial thinking

### 1.3.1 Six kinds of spatial question

Spatial questions fall into a small number of shapes. Recognising the shape tells you what data you need and what kind of operation will answer it. Each shape below has one short scenario question and the evidence it needs.

**Table 1.6 — Question types.**

| Type | Asks | Scenario example | Evidence needed | Fixture answer (hand-checked) |
| --- | --- | --- | --- | --- |
| **Location** | Where is X? | Where is request P3? | The record's position | (1200, 250), in Ward B |
| **Distribution** | How is X spread across the area? | How are unresolved requests spread between the wards? | All positions plus the areas | Under "Open or Reopened": A has P1; B has P3; P5 on the shared edge — a distribution question immediately raises the boundary policy |
| **Proximity** | What is near Y? / How far is X from Y? | Which requests are within 300 m of Main Road? | Positions of both, a distance rule, units | P1, P2, P3, P5, P6 (≤ 300 m) |
| **Containment** | Is X inside Y? | Is P6 inside the service area? | Position and boundary, plus a rule for "on the line" | No, under either boundary rule (it is 200 m outside Ward B) |
| **Connectivity** | Can you get from X to Y, and how? | Can a crew at the west end of Main Road reach P4 by road? | A connected network, not just shapes | Not answerable from the fixture: there is only one road segment and no side streets are modelled |
| **Change through time** | How has X changed? | Which requests were reopened after being closed? | Time fields and history, not just current state | P5 (closed 2026-09-05, reopened 2026-09-12) |

Notice two things. Connectivity needs a *different kind of data* (a network with links and junctions) from the others; a map of lines that merely cross is not a network. And change through time needs *history*: a table that stores only the current status cannot answer the P5 question — the `Note` column is doing that job in the fixture, which is a hint that a proper design (Chapter 8) would store inspection or status events as separate records.

**Developer analogy.** Question types are like query patterns: point lookup (location), `GROUP BY area` (distribution), a distance predicate (proximity), a membership predicate (containment), graph traversal (connectivity), and an event log (time). The analogy holds well; the difference is that the spatial predicates carry geometric edge cases (1.1.2) that ordinary equality does not.

### 1.3.2 Absolute location, relative location, and extent

- **Absolute location** is a position given directly in a coordinate system: DR-0042 is at (995, 510) on the training grid. It can be computed with, but only if the system is known — a topic Chapter 5 takes up.
- **Relative location** describes a place by its relationship to something else: "the drain beside the ward boundary on Main Road", "the streetlight next to the P1 pothole". Humans use relative descriptions constantly; they are how most service requests arrive ("outside the school gate"). A GIS cannot compute with them until they are converted to an absolute position, and the conversion always involves an assumption (which school? which gate? how far "outside"?). Record the assumption.
- **Spatial extent** is the area being considered. It is a decision, not a property of the data. In this chapter the extent could be "Wards A and B" (the union of the two squares) or "the rectangle from (0, 0) to (2200, 1000)" (which would include P6). Every count, every "how many per ward", depends on it. State the extent in the question, and check that the data actually cover it: if requests from outside the wards can exist (P6 proves they can), the extent and the data coverage are not the same thing.

**Worked example 1.3.** A caller reports "a blocked drain on Main Road near the Ward A/B border". Which fixture records could this be? The description is relative. Candidate matches: request P5 (already recorded at (1000, 500), Blocked drain) and asset DR-0042 at (995, 510), about 11 m from P5 (hand check: horizontal difference 5 m, vertical 10 m; √(5² + 10²) = √125 ≈ 11.2 m). The GIS can show that a request already exists at that location; it cannot decide whether this call is the *same* blockage or a new one. That is a process decision (1.4.2), and it is exactly how P5 came to be "reopened".

### 1.3.3 Nearest by straight line versus quickest by road

"Nearest" is the most misused word in spatial work. Straight-line distance (what the fixture distances are) is a *geometric* fact. Travel distance or travel time depends on a network — roads, one-way rules, speed, obstacles — and can differ from the straight line by a large factor.

**Diagram D3 (described; schematic, not computed by any routing engine).** Inside Ward A, a crew depot sits at (800, 200). Request P2 is at (800, 800), directly north. Straight-line distance: 600 m (same x; 800 − 200). Now add a schematic obstacle for illustration only: a canal running east–west at y = 650 from x = 0 to x = 900, crossable only by a bridge at x = 950. The crew must drive east to the bridge, north over it, and back west: (800, 200) → (950, 200) is 150 m; (950, 200) → (950, 800) is 600 m; (950, 800) → (800, 800) is 150 m; total 900 m — half as far again as the straight line, before any speed limits or turns are considered. Meanwhile P1 at (200, 200) is 600 m from the depot in a straight line too (800 − 200 along y = 200) and, with no obstacle drawn between them, its road distance in this schematic is also about 600 m. Two requests "equally near" by straight line; one is much quicker to reach.

The chapter does **not** teach how routing is computed. What you must take from it:

1. A straight-line distance is correct as a *geometric* answer and is often a good first filter ("candidates within 300 m").
2. It is not a travel time, and a map that shades "within 300 m of the road" must not be labelled "reachable in five minutes".
3. Answering the travel question needs network data and a routing method, which is a later topic; until then, say "straight-line" out loud whenever you mean it.

**Misconception.** *"The request closest on the map is the one to send the crew to."* Only if closeness was the policy and only if the crew can travel in a straight line. Both need to be checked.

**Comprehension check 1.3.** Classify each of the following as location, distribution, proximity, containment, connectivity, or change: (a) "Has the number of potholes in Ward B risen since August?" (b) "Is streetlight SL-0113 inside Ward A?" (c) "Which drain is nearest to P5?" (d) "Are there more requests in the north or the south of the study area?" Then say which one cannot be answered from the current-state fixture tables alone and why.

---

## 1.4 Identify data and its meaning

### 1.4.1 One request, three kinds of information

Take request P5 and look at what the record actually carries.

**Table 1.7 — Anatomy of request P5.**

| Kind of information | Fields | Values | What it lets you do |
| --- | --- | --- | --- |
| **Geometry** (where) | (x, y) | (1000, 500) | Draw it; measure distance; test containment |
| **Descriptive attributes** (what) | Category, Priority, Status, Channel, Note | Blocked drain; Medium; Reopened; Phone; "Closed 2026-09-05; reopened 2026-09-12 after a second call" | Filter; label; group; decide |
| **Time** (when) | Reported, Closed | 2026-08-30; — | Age; ordering; change over time |

Three observations, kept deliberately informal because Chapter 3 gives the formal structure:

- Geometry here is a single position, so P5 is drawn as a **point**. Roads and wards would need more than one coordinate; that is where lines and polygons come in (Chapter 3). For now, "geometry" simply means "the part of the record that is space".
- The descriptive attributes are ordinary typed columns. Their *meanings* are not self-evident: "Reopened" only means something because a process defines it. When you inherit a dataset, the first job is to find the definition of every code.
- Time is more than one field. P5 has a *reported* time, a *closed* time (now empty), and, buried in a free-text note, a history of two more events. Free text is where meaning goes to hide; a GIS cannot filter on "reopened after second call" reliably.

**Developer analogy.** A request record is like an event object with a typed location property. The analogy is good; its limit is that the location property has *its own* validity rules (is it on the grid? is it the right units? does "on the boundary" count?) that a normal property does not.

### 1.4.2 Event, asset, boundary: which records change and which are history

The fixture holds three fundamentally different kinds of record, and treating them alike causes real damage.

**Table 1.8 — Three record kinds.**

| Kind | Example | What it represents | Does it change? | What "updating" should mean |
| --- | --- | --- | --- | --- |
| **Observed event** | Request P5 | Something that *happened* at a time and place, as reported | The *observation* should not change; its handling status changes | Add status events; never overwrite the original report |
| **Asset** | Drain DR-0042 | A thing that *exists* and has a current state | Yes — condition, last inspection, replacement | Update current-state fields; keep the history of inspections separately |
| **Administrative boundary** | Ward A | An area defined by an authority | Rarely, and only by that authority | Replace with the authority's new edition; keep the old one with its validity dates |

Ask of every record: *is this a thing, a happening, or a rule?* P5 is a happening. DR-0042 is a thing. Ward A is a rule about space. The inspection team "updates" P5's status but must not move P5's reported position because a later visit found the real blockage 20 m away — that later finding is a *new* observation (an inspection), and Chapter 8 will give it its own table. Likewise, the reporter's coordinates for P6 are an observation; the tree TR-0301 at (2190, 520) is the asset. Observation and asset are 22 m apart (hand check: horizontal 10, vertical 20; √(10² + 20²) = √500 ≈ 22.4 m), which is normal — reporters are not surveyors.

**Misconception.** *"When the crew fixes the drain, we update the request's coordinates and status to Resolved and we're done."* Overwriting the observation destroys the evidence that the report and the asset were 11 m apart, that the request was reopened, and when. Six months later nobody can answer "how often are drain reports mislocated?" or "how many requests were reopened?"

### 1.4.3 Who collected it, when, why, and what is missing

Before formal metadata in Chapter 7, four plain questions must be asked of any dataset. Applied to Table F3:

| Question | Answer for the request table | Consequence for the recurring question |
| --- | --- | --- |
| **Who collected it?** | Members of the public via mobile app, phone, and web form; positions typed or tapped by the reporter or transcribed by call-centre staff | Positions are approximate and the precision differs by channel |
| **When?** | Reports dated 2026-08-15 to 2026-09-11; the package was assembled for training on 2026-09-18 | A request reported after 2026-09-11 is not here; "unresolved today" cannot be answered from this table with certainty |
| **For what purpose?** | To dispatch work, not to measure asset condition or map the city | The table has no field saying whether the reported problem was *confirmed* |
| **What was omitted?** | Requests the reporter never made (no app, no phone credit, night-time, gave up); duplicates that were merged (P6's twin is absent); requests outside the two wards unless someone happened to file one | A "concentration" of requests reflects who reports as much as what is broken — this is the subject of 1.6 |

These questions are platform-independent and will be re-asked, more formally, in Chapter 7. The habit to form now: **write the answers next to the data before you use it**, because a week later you will not remember, and the person who receives your map will never have known.

**Comprehension check 1.4.** For asset SL-0113 and request P1 (about 7 m apart — hand check: horizontal 5, vertical 5; √50 ≈ 7.1 m), explain (a) why the two records must stay separate rather than being merged into one "streetlight problem" record, and (b) which of the two should have its `Condition`-type fields updated after the crew visits.

---

## 1.5 Introduce layers and elementary map interaction

### 1.5.1 Layers, and a first walk around a prepared map

A **layer** is a set of records of one kind drawn together on the map. [S01] puts it plainly: map layers "are stored as files on a disk or as records in a database", each normally representing "something in the real world — a roads layer for example"; when several are added, "the layers are overlaid on top of each other"; and the map legend (the layers list) "provides a way to re-order, hide, show and group layers" [S01, §2.3]. In the Chapter 1 project there are three: **Wards**, **Roads**, **Requests** (and, optionally, **Assets**).

You do not need to understand storage formats yet. What you need is the mental model that a layer is a *view* of stored records: the software reads the records, draws them according to a style, and lists the layer in a panel where you can turn it on or off and change its order. The ArcGIS Pro documentation states the principle exactly: "Layers in a map reference the source dataset, but do not control the data" [X06].

**The demonstration.** The instructor opens the prepared project (build notes in Instructor Appendix I.5). Work through the following in either application; the two version-specific procedure boxes below give the interface steps. Each numbered action has a *what to notice*.

1. **Look at the layer list.** Three entries, each with a check box. *Notice:* the order in the list is the drawing order — the top layer draws on top [S01, §2.3].
2. **Turn Wards off, then on.** *Notice:* the requests and road do not move; only the squares vanish and reappear. Nothing was deleted (1.5.2).
3. **Turn Roads off.** *Notice:* P5 now looks like an ordinary dot on the ward edge; the fact that it is *on the road* has disappeared from view. Hidden information changes what a map seems to say.
4. **Zoom in on P5.** *Notice:* at high zoom the dot still sits on the ward edge; the symbol has a size in screen pixels, not in metres, so "on the line" is a claim about the coordinates, not the picture.
5. **Pan east until P6 is in view.** *Notice:* P6 is beyond both wards and the end of the road.
6. **Select P5 by clicking it.** *Notice:* the selection count appears, and the record is highlighted.
7. **Open the Requests attribute table and show only selected records.** *Notice:* the row for P5 shows exactly the values in Table F3; the map and the table are two views of one record.
8. **Clear the selection.**

> **Procedure (version-specific) — ArcGIS Pro. Not execution-tested; written from the documentation pages cited, checked 18 September 2026 against the "Released version: ArcGIS Pro 3.7" documentation [S02].**
>
> - *Open the project:* double-click the `.aprx` file the instructor supplied. A project "is a body of related work that may include maps, scenes, layouts, and connections to resources" [S02]. The **Contents** pane lists the map's layers; the **Catalog** pane lists project items [S02].
> - *Add a layer if it is missing:* with the map active, "On the **Map** tab, in the **Layer** group, click the **Add Data** drop-down menu" and browse to the dataset, or drag it from the Catalog pane onto the map [X06]. GeoPackage feature tables can be added this way [X07].
> - *Turn a layer on or off:* use the layer's check box in the Contents pane (the navigation tutorial uses the phrasing "In the **Contents** pane, turn on the … layer") [X02].
> - *Navigate:* "On the ribbon, click the **Map** tab. In the **Navigate** group, confirm that the **Explore** tool is selected." Then "drag the map to pan" and "use the mouse wheel to zoom in and out"; the Insert key zooms to the full extent; right-click a layer in Contents and click **Zoom To Layer** [X02].
> - *Select a feature:* the selection tools are "on the **Map** tab, in the **Selection** group"; "the number of features selected is displayed at the bottom of the view"; clear with the **Clear** button in the same group, or by clicking an empty part of the map [X03].
> - *Open the attribute table:* "Right-click a layer in the **Contents** pane and click **Attribute Table**", or select the layer and press Ctrl+T; the table opens below the map by default [X04]. Use the **Show Selected Records** and **Show All Records** buttons at the bottom of the table view to switch views [X05].
>
> *Remaining verification before teaching:* open the prepared project in the installed ArcGIS Pro release, confirm each label above matches, and confirm that the GeoPackage tables load with an "unknown" coordinate system without prompting a fix (see I.5).

> **Procedure (version-specific) — QGIS Desktop 3.40 (LTR). Not execution-tested; written from the QGIS 3.40 user manual pages cited.**
>
> - *Open the project:* double-click the `.qgz` file, or use **Project ► Open**. The **Layers** panel lists layers with check boxes.
> - *Add a layer if it is missing:* in the **Browser** panel find the GeoPackage, then "use the context menu, double-click its name, or drag-and-drop it into the map canvas"; or open the Data Source Manager (Ctrl+L). When a file contains several layers, a "Select Items to Add" dialog lets you choose [X11].
> - *Navigate:* with the **Pan Map** tool, "hold down the left mouse button and drag the map canvas"; or "hold down the Space key and move the mouse". "Roll the mouse wheel to zoom in or zoom out." **Zoom Full** goes "to the extent of all the layers in the project"; **Zoom To Layer(s)** to "the extent of all the selected layers in the Layers panel" [X08].
> - *Select a feature:* use **Select Features by area or single click** (Edit ► Select, or the Selection toolbar) and click the feature; to clear, use **Deselect Features from All Layers** (Ctrl+Alt+A) [X09, §8.4.1]. To read a feature's values without selecting it, use **Identify Features** (View ► Identify Features, or Ctrl+Shift+I / Cmd+Shift+I on macOS); the Identify Results panel lists the layer and the identified feature's fields [X09, §8.4.2].
> - *Open the attribute table:* right-click the layer in the Layers panel and choose **Open Attribute Table**, or press F6; Shift+F6 opens it filtered to selected features. Selecting a row selects the feature on the map and vice versa; the filter control at the bottom-left offers **Show Selected Features** [X10, §12.2.2–12.2.3].
>
> *Remaining verification before teaching:* open the prepared project in the installed QGIS release, confirm the labels, and confirm the GeoPackage layers load without a CRS prompt being answered incorrectly (see I.5).

**If no software is available**, work through the eight actions on Diagram D1 and Table F3 with the instructor describing each state; the observation log in 1.5.3 can still be completed. Chapter 2 introduces the tools properly; nothing later in this chapter depends on having clicked anything.

### 1.5.2 Hiding information is not deleting data

Unchecking a layer changes what you *see*. It does not change what is *stored*. There are three distinct levels, and confusing them is a classic beginner error in every GIS.

**Table 1.9 — Three levels of "making something go away".**

| Action | What changes | Is the data still on disk / in the database? | Can a colleague opening the same dataset still see it? | Reversible? |
| --- | --- | --- | --- | --- |
| Turn a layer off (uncheck) | Only this map's display | Yes | Yes | Instantly — check it again |
| Remove the layer from the map | This map no longer references the dataset ("Layers in a map reference the source dataset, but do not control the data" [X06]) | Yes | Yes | Add it back |
| Delete the dataset | The records are gone | **No** | **No** | Only from a backup |

There is a fourth, subtler level that matters for the recurring question: a **filter** (Chapter 10) can hide *some* records of a layer — for instance, only Open requests are drawn. A filtered map can look complete and be missing half the data. Any map handed to a decision-maker must state which records were excluded and why.

**Developer analogy.** Turning a layer off is like collapsing a panel in a UI; removing a layer is like dropping a *view*; deleting the dataset is like dropping the *table*. The analogy is exact for the first two. It weakens for the third because GIS datasets are often single files on a shared drive with no transaction log, so "drop table" can be far less recoverable than in a database.

**Misconception.** *"I unchecked the duplicates, so the count on the map is right now."* The count of records has not changed; only the drawing has. Any tool that counts the layer's records will still count the hidden ones unless a filter or selection is applied.

### 1.5.3 The observation log

Every time you look at a map in this course you will keep a three-column log. It forces the discipline that the map *suggests*, the table *confirms*, and some things remain *uncertain*.

**Table 1.10 — Observation log template, filled for two records of the Chapter 1 map.**

| Record | What the map suggests | What the table confirms | What remains uncertain |
| --- | --- | --- | --- |
| P5 | Sits on the Ward A/B edge and on Main Road | (x, y) = (1000, 500): x = 1000 is the shared edge; y = 500 is the road — exact coincidence, not just visual | Whether the *real* blockage is at the drain DR-0042, 11 m away in Ward A; whether a "reopened" request should be treated as new or old for priority |
| P6 | Isolated, east of both wards, in line with the road | (2200, 500): 200 m beyond the road's end and Ward B's edge; status Closed – duplicate | Where the surviving twin record is and whether *it* is unresolved; whether the tree TR-0301 (22 m away) is the asset concerned |

The log is a deliverable for module 1.5: complete it for all six requests. There is no single right answer for the third column, but an empty third column is always wrong.

**Comprehension check 1.5.** A colleague turns off the Wards layer, zooms to Ward B, takes a screenshot, and reports "there are two requests in Ward B". List everything about that statement that cannot be verified from the screenshot and what you would look at instead.

---

## 1.6 Recognize limits of a spatial conclusion

### 1.6.1 A concentration of reports is not yet a concentration of problems

Suppose a management map shows many more requests in Ward A than in Ward B, and someone concludes "Ward A's assets are in worse condition". Consider this synthetic summary for the two wards over the same period.

**Table 1.11 — Synthetic ward summary (illustrative counts, not the six-record fixture).**

| Ward | Requests received | Residents | Requests per 1,000 residents | Requests filed via mobile app | Streetlights maintained |
| --- | --- | --- | --- | --- | --- |
| A | 40 | 20,000 | 40 ÷ 20 = **2.0** | 36 of 40 (90%) | 400 |
| B | 15 | 5,000 | 15 ÷ 5 = **3.0** | 3 of 15 (20%) | 100 |

Ward A has nearly three times as many reports, but per resident Ward B reports *more*. Ward A also has four times the streetlights (0.10 requests per streetlight in A, 0.15 in B). And 90% of A's reports came through the app against 20% of B's — a hint that A's residents find reporting easier, not that A has more faults.

Before proposing a cause, list the alternatives:

1. **More people** live or pass through the area (exposure).
2. **More reporting** happens there (app adoption, a local campaign, a vocal resident) — the health-worker example in [S01] records only patients who *came to be treated*; the same is true of anyone who *chose to report* [S01, §2.1].
3. **More assets** exist there to fail.
4. **Duplicates** were not merged in one ward but were in the other.
5. **Different time windows** or a boundary change moved records between wards.
6. **The condition really is worse.**

A GIS can help test several of these (per-resident and per-asset rates, duplicate detection, time filters), but it can only test them if the extra data exist and the question is asked. The habit: **"compared with what?"** must be answered before "why?" is attempted. Chapter 12 returns to rates and denominators for map design; the point here is purely logical.

### 1.6.2 Fitness for purpose

A dataset is not "accurate" or "inaccurate" in the abstract; it is adequate or inadequate *for a stated use*.

| Use | What it needs from the data | Is the Chapter 1 fixture adequate? |
| --- | --- | --- |
| A city-overview map of where requests cluster | Positions roughly right relative to wards; complete-enough coverage | Conceptually yes — but the fixture is a schematic grid with no real-world position at all, so it is adequate only for *teaching* |
| Assigning each request to a ward for reporting | Positions correct relative to the boundary, plus a boundary rule for edge cases like P5 | Yes for five records; P5 needs a policy, and the boundary itself must be the current edition |
| Directing a crew to a fallen tree | Position within tens of metres | P6 vs TR-0301 differ by 22 m — probably fine for a tree |
| Locating a buried pipe before excavation | Position within a distance the excavator can safely work to, with a documented survey source | **No.** A citizen's tap on a phone map is not evidence of where a pipe lies; using it that way risks striking the pipe. This chapter states no numeric accuracy requirement, because that depends on the utility's own standard |

The blueprint's formulation is the one to remember: a dataset sufficient for a city overview may be insufficient for locating a pipe during excavation. Neither the map's appearance nor the number of decimal places in a coordinate tells you which case you are in; only the data's provenance (1.4.3) does.

### 1.6.3 Convincing, and still wrong

A polished map with a legend and crisp symbols carries authority it may not deserve. Three ways it can mislead while looking excellent:

| Failure | Scenario example | Looks like | How to catch it |
| --- | --- | --- | --- |
| **Incomplete data** | The map was made from the app channel only; phone and web reports (P2, P4, P5, P6) are absent | A tidy map with two dots | Compare the record count on the map with the count in the source system |
| **Stale data** | The ward boundaries are last year's; the shared edge has since moved 100 m east | Every request cleanly inside one ward | Check the boundary's edition date against the requests' dates |
| **Misinterpreted values** | "Closed – duplicate" was counted as "resolved", so P6's problem is reported as fixed | Fewer unresolved requests | Read the status definitions; ask what each code means to the people who set it |

None of these are software faults, and no amount of cartographic care fixes them. The defence is the validation step of 1.2.3 and the four questions of 1.4.3, applied *before* the map is trusted.

**Misconception.** *"The map came from the official GIS, so the picture is right."* The GIS drew exactly what it was given. "Official" describes who published it, not whether it is current, complete, or suited to your question.

**Comprehension check 1.6.** Using Table 1.11, write two sentences: one that a manager might wrongly conclude from raw counts, and one carefully worded conclusion that the table *does* support. Then name one further dataset you would want before saying anything about asset condition.

---

## 1.7 Guided lab: write a spatial problem brief

### 1.7.1 Objective and prerequisites

**Objective.** Convert a short operational scenario into three answerable spatial questions, each with its inputs, selection rules, output, and validation method, and present them as a one-page problem brief with an annotated sketch.

**Prerequisites.** Modules 1.1–1.6. No software is required and no tool names are needed; plain language is preferred.

**Required materials.** This document (Tables F1–F4), paper or a drawing tool, about 60–90 minutes.

### 1.7.2 The scenario (synthetic)

> **Decision-maker:** the Inspection Team Lead for Wards A and B.
> **Date of the brief:** 18 September 2026.
> **Date range of interest:** requests **reported from 1 August 2026 to 15 September 2026 inclusive**.
> **Study area:** Wards A and B; a request on the shared boundary counts as inside both wards; a request outside both wards is out of scope unless a question says otherwise.
> **Situation:** one crew is available tomorrow. It starts at the west end of Main Road, at (0, 500), and can work anywhere along the road corridor. The Team Lead wants to know which requests to visit first and whether any request needs attention from someone other than the crew (for example a duplicate to be reconciled, or a request outside the area to be passed on).
> **Data available:** Tables F1 (wards), F2 (road), F3 (requests), F4 (assets). Distances are straight-line, in metres, on the training grid.

### 1.7.3 Steps

1. **Read the scenario and underline every vague term.** Expect at least: "first", "attention", "road corridor", "unresolved" (implied), "needs".
2. **Write the decision in one sentence** beginning "Tomorrow the crew will…". If you cannot, the questions will drift.
3. **Draft three spatial questions**, each of a different type from Table 1.6 (for example one proximity, one containment, one change-through-time). Each must be answerable from Tables F1–F4 *after* you define its terms.
4. **For each question complete the four-part card** below. Write the rules so that a colleague who has never met you could apply them to Table F3 and get the same IDs.

   | Card field | What to write |
   | --- | --- |
   | Inputs | Which tables and which fields; the date range; the study area |
   | Selection rules | Every filter, with the exact status values, the distance value, the unit, and whether the boundary value counts |
   | Output | The form of the answer: a list of IDs, a count per ward, a yes/no per record |
   | Validation | How you would check it by hand, and one thing the answer does *not* establish |

5. **Apply your own rules to Table F3 by hand** and write the resulting IDs on each card. This is the step that exposes an unanswerable question.
6. **Draw the annotated sketch:** the two squares, the road, the six requests, and on it mark (a) the study area boundary, (b) the distance threshold you chose as a band along the road, (c) any record whose treatment depends on a boundary rule, (d) any record you excluded, with the reason written beside it.
7. **Assemble the one-page brief:** decision sentence, three cards, sketch, and a closing "limitations" line of at most three sentences.
8. **Self-check** against the validation list in 1.7.4 before submitting.

### 1.7.4 Expected results and validation checks

There is no single correct set of questions. The following invariants must hold for *any* correct brief, and the instructor will check them (full guidance in Instructor Appendix I.2):

- Every vague term in the scenario has been given a testable definition somewhere on the page.
- Every card's rules, applied to Table F3, produce a definite result; the learner has written that result and it is arithmetically right for the stated rules (for example, a rule "≤ 300 m of R1 and Open or Reopened" must yield P1, P3, P5 — see I.4).
- P5 (on the shared boundary and on the road) and P6 (outside both wards; closed as duplicate) are each mentioned explicitly with a stated treatment. A brief that never notices them fails the validation check.
- At least one question uses a time condition tied to the date range, and the learner notes that the package contains nothing reported after 2026-09-11.
- No question requires data the tables do not contain (travel time, population, the duplicate twin of P6) *unless* the card says so and marks the question as currently unanswerable.
- The sketch matches the cards: the band drawn along the road corresponds to the distance value on the proximity card.

### 1.7.5 Troubleshooting

| Symptom | Likely cause | Fix |
| --- | --- | --- |
| Two people applying your rules get different IDs | A term is still vague (usually the boundary value or the status list) | Rewrite the rule with "≤" or "<" and an explicit status set |
| Your proximity question selects P6 but you "meant" to exclude it | The study-area condition was assumed, not written | Add the containment condition to the card, or accept P6 and say why |
| You cannot decide which ward P5 belongs to | That is a policy, not a data fact | Write the policy on the card (both wards, or a tie-break rule) and note that the raw geometry gives two matches |
| A question needs travel time | The fixture has no network | Mark the question "not answerable with current data" and state what would be needed; do not substitute straight-line distance silently |
| The sketch shows the road continuing past x = 2000 | Misreading Table F2 | R1 is finite; P6's distance is measured to the end point |

### 1.7.6 Deliverables

1. The one-page problem brief (decision sentence, three cards with hand-derived results, limitations line).
2. The annotated sketch.
3. The observation log from 1.5.3 for all six requests (if module 1.5 was completed with software, note which application and version; if without, say so).

---

## 1.8 Independent check and progression gate

This module is the learner-facing assessment. **Do not look at the Instructor Appendix until your submission is marked.** Item mapping to modules and outcomes is shown in brackets.

### 1.8.1 Concept questions (answer each in no more than five sentences; Q4 and Q5 are multiple choice with one best answer)

**Q1.** [1.2.1; LO1] Name the four functions a GIS performs and give one municipal-scenario action for each. Then explain why "we have a PDF map of the wards" does not mean the municipality has GIS data for wards.

**Q2.** [1.2.2; LO1] A spreadsheet has columns `RequestID, Address, Category, ReportedDate`. State the three conditions that must hold before it can become a GIS layer, and say which of them the `Address` column fails as it stands.

**Q3.** [1.3.1; LO3] Classify each question by type (location, distribution, proximity, containment, connectivity, change): (a) "Which requests lie within 200 m of Road R1?" (b) "Can the crew drive from (0, 500) to P4 without leaving the modelled roads?" (c) "How many unresolved requests are in each ward?" (d) "How many requests were reopened in September?" For (b), say whether the fixture can answer it and why.

**Q4.** [1.3.3; LO3] Two requests are each 600 m from a depot in a straight line. Which statement is best supported? (A) They are equally quick to reach. (B) The one nearer to a road is quicker to reach. (C) Nothing about reaching time can be concluded without network data. (D) The GIS default distance is travel time, so both are 600 m of travel.

**Q5.** [1.5.2; LO5] After unchecking the Requests layer and saving the project, which is true? (A) The request records are deleted. (B) The request records are unchanged; only this map's display changed. (C) The dataset is removed from disk but recoverable. (D) Other maps using the same dataset will no longer show requests.

**Q6.** [1.6.1; LO6] Ward A logs 40 requests and Ward B logs 15 in the same month. List three alternative explanations, other than "Ward A's assets are in worse condition", and name the additional data each would need.

### 1.8.2 Scenario questions

**S1.** [1.1.2, 1.2.3; LO2] The policy owner writes: "Prioritise unresolved high-priority requests near Main Road inside our wards." Rewrite this as a fully defined question for the Chapter 1 fixture: state the status set, the priority value, the distance value with unit and boundary treatment, and the ward-membership rule including the shared boundary. Apply it by hand to Table F3 and give the resulting IDs. Then state one thing your result does not establish.

**S2.** [1.4.2, 1.4.3; LO4] The crew visits P5, finds that the real blockage is at drain DR-0042, clears it, and asks you to "update the map". Say exactly which records should change, which should not, and what new record (if any) should be created. Then list two things about the request data's origin that the Team Lead should know before using a map of "reopened requests" to judge crew performance.

### 1.8.3 Independent practical task (unfamiliar inputs)

**Scenario (synthetic): school access.** A district education officer has a table of 14 primary schools with coordinates on a flat local grid in metres, a table of 300 pupil home locations for one intake year (each with a school ID assigned by the enrolment office), and a single polygon for the district boundary. The officer asks three things:

1. "Can we have a map showing where pupils live relative to their schools?"
2. "Which pupils live more than 2 km from their assigned school?"
3. "Should we move the catchment boundary between School 4 and School 7?"

**Required.** Produce a short written response (no software) that:

- (a) Labels each of the three requests as primarily a **display** question, an **analysis** question, or an **operational decision**, with one sentence of justification each. [1.8.1 of the blueprint; LO2]
- (b) Clarifies **one ambiguous term** in request 2 by giving it a testable definition, and states the boundary treatment. [LO2]
- (c) Identifies **one unsupported conclusion** the officer might draw from the map in request 1 and explains why it is unsupported. [LO6]
- (d) For request 2 lists the inputs, selection rules, output, and a validation method, as in the 1.7 card. [LO4]
- (e) States one important limitation of the pupil home data that the officer should know before any of the three requests is answered. [LO4, LO6]

No pupil, school, or district in this task is real, and no coordinates are supplied: the task is about framing, not computing.

### 1.8.4 Oral explanation (three minutes, no notes, no software)

Explain to the instructor, for the recurring question "Which unresolved service requests should an inspection team investigate first?": (1) the decision the Team Lead is making, (2) the evidence a GIS can supply and the definitions that evidence depends on, and (3) one important limitation of the answer. You may draw on paper. [1.8.3 of the blueprint; LO2, LO6]

### 1.8.5 Submission requirements and scoring

**Submit:** the 1.7 deliverables; written answers to Q1–Q6 and S1–S2; the practical response (a)–(e); and be available for the oral check.

**Scoring (chapter total 100).**

| Component | Weight | What earns the marks |
| --- | --- | --- |
| Concept questions Q1–Q6 | 30 (5 each) | Correct, specific, uses the chapter's distinctions; multiple-choice items are all-or-nothing |
| Scenario questions S1–S2 | 20 (10 each) | Every vague term defined; hand result arithmetically right for the stated rules; record-change reasoning correct |
| Guided lab (1.7) | 20 | All six validation invariants in 1.7.4 hold; sketch matches cards |
| Independent practical | 20 | Correct display/analysis/decision labels; genuine clarification; a *real* unsupported conclusion rejected with a reason; card complete |
| Oral explanation | 10 | Decision, evidence-with-definitions, and limitation stated clearly without relying on any tool |

**Pass threshold:** 80 overall, **and** no critical misconception. For this chapter the critical misconceptions are: treating a straight-line distance as travel time; claiming a hidden layer deletes data; asserting a cause from a concentration of reports without an alternative; and leaving P5 or P6 unaddressed in S1 or the lab. A critical misconception requires remediation and a fresh exercise (with different geometry) before Chapter 2, regardless of the total.

**Progression rule (from the blueprint, 1.8.3):** you progress when you can explain the intended decision, the required evidence, and an important limitation *without a software demonstration*. The oral check tests exactly that.

---

## 1.9 Media and source brief

This module records what later presentation, audio, and visual material must contain; the full specifications are in the Media Appendix so that they can be updated without touching the teaching text.

1. **Slides** follow the blueprint's five beats — problem, table/map comparison, question types, workflow, limitations — mapped to modules 1.1, 1.1.3, 1.3, 1.2.3, and 1.6 (Media Appendix M.1).
2. **Audio** narrates every example by naming records and coordinates ("P5 at x one thousand, y five hundred") rather than pointing ("this one here") or relying on colour ("the red dots"). Guidance per segment is in M.2.
3. **Visuals** progressively reveal the same six records as a table, then as dots, then with the road and wards layered on — the *records → layers* reveal (Diagram D1 sequence, M.3). **No 3D scene is proposed for this chapter**; there is no height information and a table or 2D diagram teaches every concept here better. One optional 2D interactive is specified (M.4) because it demonstrates the "near" threshold problem of 1.1.2 in a way a static slide cannot.
4. **Source reading.** Read [S01] in full (it is short) for the introductory concepts of 1.2; the problem brief and assessment in this chapter are original instructional designs and are not drawn from any vendor course.
5. **Transition to Chapter 2.** Take the eight-step workflow of 1.2.3 and ask, for each step: *where could this happen?* On an inspector's phone, on an analyst's desktop, in a database, on a server, in a browser? Chapter 2 introduces the ArcGIS products and their open-source counterparts by exactly that question — roles first, product names second.

---

## Glossary

Terms are defined as used in this chapter; later chapters refine several of them.

| Term | Definition |
| --- | --- |
| **Absolute location** | A position expressed directly in a coordinate system, e.g. (995, 510) on the training grid. |
| **Asset** | A physical thing the municipality maintains (streetlight, drain, tree) with a current state. |
| **Attribute** | A descriptive, non-spatial value stored with a record (category, status, date). |
| **Attribute table** | The tabular view of a layer's records, one row per feature. |
| **Boundary-inclusive / strict** | Whether a value exactly on a limit (a point on a polygon edge; a distance exactly equal to the threshold) counts as inside. Must be stated for every rule. |
| **Capture, store, analyse, communicate** | The four functions of a GIS. |
| **Containment** | The question of whether one geometry lies inside another. |
| **Dataset** | The stored collection of records (a file or database table) that a layer draws. |
| **Extent (spatial extent)** | The area under consideration for a question; a decision, not a property of the data. |
| **Fitness for purpose** | Whether a dataset is adequate for a specific stated use, rather than "accurate" in the abstract. |
| **Geometry** | The part of a record that represents space; in this chapter, a point position. |
| **GIS** | Geographic(al) Information System: data, software, hardware, and the people and processes that use them to capture, store, analyse, and communicate location-linked information [S01; X01]. |
| **Inspection** | A record of a visit to an asset or request; formally modelled in Chapter 8. |
| **Layer** | A set of records of one kind drawn together on a map from a referenced dataset; can be reordered, hidden, and shown [S01, §2.3; X06]. |
| **Observed event** | Something that happened at a time and place as reported (a request). Its observation should not be overwritten. |
| **Policy versus evidence** | Rules chosen by the organisation versus facts a GIS can produce. |
| **Proximity** | The question of how near things are; requires a distance, a unit, and a comparison rule. |
| **Provenance** | Who produced a dataset, when, why, and what it omits. |
| **Relative location** | A place described by its relation to something else ("beside the school"). |
| **Request (service request)** | A report from the public about a problem, with a reported position, attributes, and times. |
| **Selection** | Marking records for attention in both map and table without changing them. |
| **Straight-line distance** | Geometric distance between two positions; not travel distance or time. |
| **Validation** | Checking that an output is plausible and reproducible before acting on it. |
| **Ward** | An administrative area defined by an authority; here, two synthetic 1 km squares. |

## Recap

- A GIS answers precisely the question it is asked; the decision and its policy come from people, the evidence from data, and the definitions of "near", "inside", "unresolved" must be written before any operation is chosen (1.1).
- A GIS is data + software + hardware + people/processes performing capture, storage, analysis, and communication; a map image, a navigation app, and an address table are respectively an output, a product, and an input — not a GIS (1.2).
- Spatial questions come in six shapes; absolute and relative location differ; straight-line distance is not travel time (1.3).
- A record carries geometry, attributes, and time; events, assets, and boundaries are different kinds of record with different update rules; always ask who, when, why, and what is missing (1.4).
- Layers are views of stored records; hiding is not deleting; keep an observation log of suggests/confirms/uncertain (1.5).
- A concentration of reports needs alternative explanations before a cause; adequacy is relative to purpose; a convincing map can be incomplete, stale, or misread (1.6).

**Next:** Chapter 2 asks *where* each step of the workflow can run and introduces ArcGIS Pro, ArcGIS Online, ArcGIS Enterprise, application builders, SDKs, and their open-source counterparts by role.

---

## References

All pages were opened and read on **18 September 2026**. Blueprint identifiers [Sxx] are retained; additional sources consulted for this chapter are numbered [Xxx].

| ID | Publisher — page title | Link | Used for |
| --- | --- | --- | --- |
| S01 | QGIS Project — *A Gentle Introduction to GIS*, "2. Introducing GIS" (3.44 documentation) | https://docs.qgis.org/3.44/en/docs/gentle_gis_introduction/introducing_gis.html | Definition and components of GIS (§2.1–2.2); GIS application functions; layers, map view, legend (§2.3); geographic vs non-geographic data and the health-worker table (§2.1, §2.5) |
| S02 | Esri — *Introduction to ArcGIS Pro* (ArcGIS Pro documentation; page labelled "Released version: ArcGIS Pro 3.7"). **Note:** the blueprint URL `https://pro.arcgis.com/en/pro-app/latest/get-started/get-started.htm` returned HTTP 301 to the link shown | https://doc.esri.com/en/arcgis-pro/latest/get-started/get-started.html | Project, views, Contents and Catalog panes, ribbon |
| X01 | Esri — *GIS Dictionary: GIS* | https://support.esri.com/en-us/gis-dictionary/gis | Definition of GIS |
| X02 | Esri — *Navigate maps and scenes* (ArcGIS Pro documentation; tutorial notes it was written with ArcGIS Pro 3.2) | https://doc.esri.com/en/arcgis-pro/latest/get-started/navigate-your-data.html | Explore tool location, pan, zoom, Zoom To Layer, full extent, turning a layer on in Contents |
| X03 | Esri — *Select features interactively* (ArcGIS Pro documentation) | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/navigation/select-features-interactively.html | Selection group on Map tab; selection count; Clear |
| X04 | Esri — *Open tabular data* (ArcGIS Pro documentation) | https://doc.esri.com/en/arcgis-pro/latest/help/data/tables/open-tabular-data.html | Opening an attribute table; Ctrl+T; table docks below the view |
| X05 | Esri — *View all or only the selected records* (ArcGIS Pro documentation) | https://doc.esri.com/en/arcgis-pro/latest/help/data/tables/view-all-or-only-the-selected-records.html | Show Selected Records / Show All Records buttons |
| X06 | Esri — *Add layers to a map or scene* (ArcGIS Pro documentation) | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/layer-properties/add-layers-to-a-map.html | Add Data button location; "Layers in a map reference the source dataset, but do not control the data" |
| X07 | Esri — *Work with SQLite databases and GeoPackage files in ArcGIS Pro* | https://doc.esri.com/en/arcgis-pro/latest/help/data/databases/work-with-sqlite-databases-in-arcgis-pro.html | Adding GeoPackage data via Add Data or Catalog pane |
| X08 | QGIS Project — *QGIS Desktop 3.40 User Guide*, "7.1.1 Exploring the map view" | https://docs.qgis.org/3.40/en/docs/user_manual/map_views/map_view.html | Pan, mouse-wheel zoom, Zoom Full, Zoom To Layer(s) |
| X09 | QGIS Project — *QGIS Desktop 3.40 User Guide*, "8.4.1 Selecting features" and "8.4.2 Identifying Features" | https://docs.qgis.org/3.40/en/docs/user_manual/introduction/general_tools.html | Select Features tool, deselect shortcut, Identify Features |
| X10 | QGIS Project — *QGIS Desktop 3.40 User Guide*, "12.2 Working with the Attribute Table" | https://docs.qgis.org/3.40/en/docs/user_manual/working_with_vector/attribute_table.html | Open Attribute Table, F6, Shift+F6, row↔feature selection, Show Selected Features |
| X11 | QGIS Project — *QGIS Desktop 3.40 User Guide*, "11.1 Opening Data" | https://docs.qgis.org/3.40/en/docs/user_manual/managing_data_source/opening_data.html | Browser panel, Data Source Manager (Ctrl+L), multi-layer selection dialog |
| X12 | Esri — *XY Table To Point (Data Management Tools)* (ArcGIS Pro documentation) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/xy-table-to-point.html | Parameters; default coordinate system is WGS84 (instructor build note) |
| X13 | GDAL — *Comma Separated Value (.csv)* vector driver | https://gdal.org/en/stable/drivers/vector/csv.html | `WKT` column recognised as geometry; X/Y possible names; type detection (instructor build note) |
| X14 | GDAL — *GPKG — GeoPackage vector* driver | https://gdal.org/en/stable/drivers/vector/gpkg.html | Handling of layers without a CRS (srs_id 99999 from GDAL 3.9; srs_id 0 for 3.8 and earlier) |
| X15 | OGC — *GeoPackage Encoding Standard 1.3.1*, Requirement 11 | https://www.geopackage.org/spec131/index.html | Mandatory `gpkg_spatial_ref_sys` records: −1 undefined Cartesian, 0 undefined geographic, 4326 WGS 84 |

---

# Instructor Appendix (not for learners before marking)

## I.1 Answers to comprehension checks

**1.1.** Terms: *urgent* (e.g. Priority = High), *near* (e.g. ≤ 300 m straight-line to R1, inclusive), *service area* (e.g. inside A ∪ B with the shared edge inside), and the implied *unresolved* (e.g. Open, In progress, Reopened). With those four: High → {P2, P3, P6}; ≤ 300 m → keeps all three (300, 250, 200); inside A ∪ B → drops P6; unresolved → P2 (In progress) and P3 (Open) stay. Result {P2, P3}. Accept any internally consistent definitions; the mark is for completeness and a correct hand result.

**1.2.** Capture (an inspector taps the phone to log a request), store (requests kept in a table with geometry and status), analyse (distance from each request to R1), communicate (a list of tomorrow's visits with the rules stated). Spreadsheet: addresses are text, not computable positions, and no reference frame is stated.

**1.3.** (a) change; (b) containment; (c) proximity; (d) distribution. (a) cannot be answered from current-state tables alone: it needs the counts at two times or a history; the fixture has only the current status and a free-text note.

**1.4.** (a) One is an observation (someone reported a fault on 2026-09-02), the other is a thing with a maintained state; merging loses the report history and the fact that the reported position and the asset are 7 m apart, and prevents a second report on the same light being linked properly. (b) SL-0113's condition/last-inspected fields; P1's status changes but its reported position and date must not.

**1.5.** Not verifiable: that the two dots are requests and not assets; that they are *inside* Ward B rather than on its edge (the boundary layer is off); that there are only two (a filter or an off-screen record could hide more); that "requests" means unresolved ones. Look at the Requests attribute table with Wards on, check the count, and check each record's coordinates against the boundary.

**1.6.** Wrong: "Ward A has more problems with its assets than Ward B." Supported: "Ward A received 40 requests and Ward B 15 in the period; per resident, Ward B's rate (3.0 per 1,000) is higher than Ward A's (2.0 per 1,000), and the two wards' reporting channels differ sharply." Further data: inspection outcomes (confirmed faults), or asset counts by type and age.

## I.2 Guided lab (1.7): marking guidance and expected results

There is no single answer key. Apply the six invariants in 1.7.4. Typical strong cards:

- **Proximity:** "Requests with Status in {Open, Reopened}, Reported between 2026-08-01 and 2026-09-15 inclusive, straight-line distance to R1 ≤ 300 m (inclusive), inside A ∪ B with the shared edge inside." Result {P1, P3, P5}. Validation: recompute one distance by hand (P3: |250 − 500| = 250). Does not establish travel time.
- **Containment / distribution:** "Count of requests with Status ≠ Resolved per ward, boundary-inclusive." A: P1, P2, P5 → 3; B: P3, P5 → 2; P6 unassigned (outside). Learner must note that P5 is counted twice — 5 ward–request matches but only 4 distinct matched requests (P1, P2, P3, P5) — or state a tie-break. (With no status filter, all six requests give the blueprint A.2.7 figures: 6 matches, 5 distinct requests.)
- **Change through time:** "Requests whose status changed after being closed within the date range." Result {P5}; learner must note that the evidence is a free-text note and recommend a proper history (Chapter 8 preview).

**Common mistakes and remediation.**

| Mistake | Remediation |
| --- | --- |
| Threshold written without "≤/<" | Have the learner apply the rule to P1 and P2 and watch the ambiguity appear |
| P6 silently excluded or included | Return the brief; require an explicit containment rule |
| "Nearest to the depot" question answered as straight-line without saying so | Re-read 1.3.3; require the word "straight-line" on the card |
| Question needing population or travel time answered with fixture data | Accept if marked "not answerable with current data"; otherwise return |
| Sketch band width does not match the card's distance | Require correction; the sketch is evidence the learner understands the rule |

## I.3 Assessment answer key (1.8)

**Q1.** Capture / store / analyse / communicate with any correct scenario actions (see I.1, 1.2). A PDF is an output: no records, no queryable attributes, positions not reliably measurable; wards would have to be recaptured.

**Q2.** (1) Each row has a location convertible to a position; (2) the positions are interpretable (units/reference known); (3) descriptive columns keep their meaning on import. `Address` fails (1): it is text, and converting it (geocoding) is a separate error-prone step.

**Q3.** (a) proximity; (b) connectivity — not answerable, the fixture has one road segment and no network; (c) distribution (with containment as the mechanism); (d) change through time.

**Q4.** **C.** A: unsupported without network data. B: plausible-sounding but road proximity is not reachability. D: false; distances in this chapter are straight-line and no tool "defaults" to travel time.

**Q5.** **B.** A and C confuse display with storage. D is false because layers reference the dataset and do not control it [X06].

**Q6.** Any three of: more residents/exposure (population data); more reporting via an easier channel (channel counts, app adoption); more assets (asset inventory); unmerged duplicates (deduplication review); different time windows or boundary changes (edition dates). Each must name its data.

**S1.** Model answer: Status ∈ {Open, In progress, Reopened}; Priority = High; distance to R1 ≤ 300 m inclusive (straight-line, metres); inside Ward A or B, shared edge counts as inside. Hand result: High → {P2, P3, P6}; status → P2 (In progress), P3 (Open) stay, P6 (Closed – duplicate) drops; distance → P2 = 300 ✔, P3 = 250 ✔; ward → both inside. **{P2, P3}.** If the learner excludes "In progress" the result is {P3}; accept if stated. Does not establish: order between P2 and P3 (policy), travel time, or whether P6's twin is unresolved.

**S2.** Change: P5's status (to Resolved, with a closed date) and DR-0042's condition and last-inspected fields. Do not change: P5's reported (x, y), reported date, or reopening history. Create: an inspection record linking P5 and DR-0042 with the visit date and finding (Chapter 8 will formalise). Two provenance facts: reports come from the public via three channels with differing precision; "reopened" depends on a second call being made, so it measures reporting behaviour as well as crew performance.

**Practical (school access).** (a) 1 = display; 2 = analysis (a distance test with a threshold); 3 = operational decision (policy — the GIS can supply evidence such as distances and counts, not the decision). (b) "More than 2 km": straight-line or travel? inclusive or strict at exactly 2,000 m? in what units on the grid? — any testable version accepted. (c) Examples: "pupils near School 4 attend School 4" (assignment is by the enrolment office, not location), or "the cluster north of School 7 shows over-demand" (could be housing density). (d) Inputs: pupil table (home x, y, assigned school ID), school table (x, y, ID); rule: straight-line distance from home to assigned school > 2,000 m (strict) on the grid; output: list of pupil IDs with distance; validation: hand-check two pupils, confirm count ≤ 300, confirm every pupil has a matching school ID (unmatched IDs must be reported). (e) Any of: one intake year only; home locations' source and precision unknown; whether addresses were geocoded; missing pupils.

**Oral.** Pass if the learner states (1) the assignment decision, (2) evidence — position, distance to road, ward membership, status/age — with at least two definitions the evidence depends on, and (3) a limitation such as straight-line ≠ travel, incompleteness of reports, or boundary-case policy, all without a tool.

## I.4 Hand verification of fixture arithmetic

Road R1 lies on y = 500 for 0 ≤ x ≤ 2000. For a point with 0 ≤ x ≤ 2000, straight-line distance to R1 is |y − 500|; for x > 2000 it is the distance to the end point (2000, 500).

| Request | (x, y) | Computation | Distance to R1 | Ward test |
| --- | --- | --- | --- | --- |
| P1 | (200, 200) | \|200 − 500\| | 300 m | 0 < 200 < 1000 and 0 < 200 < 1000 → strictly inside A |
| P2 | (800, 800) | \|800 − 500\| | 300 m | strictly inside A |
| P3 | (1200, 250) | \|250 − 500\| | 250 m | 1000 < 1200 < 2000 → strictly inside B |
| P4 | (1700, 900) | \|900 − 500\| | 400 m | strictly inside B |
| P5 | (1000, 500) | \|500 − 500\| | 0 m | x = 1000 is the shared edge (0 ≤ y ≤ 1000) → on the boundary of both |
| P6 | (2200, 500) | √((2200 − 2000)² + 0²) | 200 m | x > 2000 → outside both |

Threshold sets: ≤ 300 → {P1, P2, P3, P5, P6}; < 300 → {P3, P5, P6}; ≤ 250 → {P3, P5, P6}; ≤ 200 → {P5, P6}. Areas: 1000 × 1000 = 1,000,000 m² = 1 km² each. Asset-to-request offsets: SL-0113↔P1 √(5² + 5²) ≈ 7.1 m; DR-0042↔P5 √(5² + 10²) ≈ 11.2 m; TR-0301↔P6 √(10² + 20²) ≈ 22.4 m. Synthetic ward rates (Table 1.11): 40/20,000 × 1,000 = 2.0; 15/5,000 × 1,000 = 3.0; per streetlight 40/400 = 0.10, 15/100 = 0.15. Diagram D3 path: 150 + 600 + 150 = 900 m. All figures agree with blueprint Appendix A.2 items 5–8.

These are hand calculations on an idealised plane. They have **not** been reproduced in ArcGIS Pro or QGIS; when the prepared project is built (I.5), confirm the software reports the same values with the fixture's planar setup before showing any computed number to learners.

## I.5 Building the prepared Chapter 1 project (not execution-tested)

Module 1.5 needs a project with three (optionally four) layers. Nothing in this section has been run by the author; each step names what to verify.

**Input files (create as UTF-8 CSV).** Use exactly the values in Tables F1–F4. Encode the ward polygons and the road as a `WKT` column — for example `POLYGON((0 0,1000 0,1000 1000,0 1000,0 0))` and `LINESTRING(0 500,2000 500)` — and give requests and assets `x` and `y` columns.

**Route A — GDAL `ogr2ogr` to a GeoPackage.** The GDAL CSV driver treats "a field named 'WKT'" as geometry and can read point layers from `x`/`y` columns via the `X_POSSIBLE_NAMES`/`Y_POSSIBLE_NAMES` open options; without a `.csvt` file all columns are read as strings unless `AUTODETECT_TYPE=YES` is set [X13]. Do **not** pass `-a_srs`; the fixture has no CRS. How the undefined CRS is recorded depends on the GDAL version: from GDAL 3.9 a layer without a CRS is stored against a custom `srs_id` 99999 named "Undefined SRS"; GDAL 3.8 and earlier use `srs_id` 0 [X14]. Note that in the GeoPackage standard `srs_id` 0 is defined as the *undefined geographic* entry and −1 as *undefined Cartesian* [X15]; a purist may prefer to set the layer's `srs_id` to −1 after creation so that the package itself says "Cartesian, undefined". *Verify:* open the `.gpkg` in the target application, confirm three/four layers, the record counts (2, 1, 6, 3), and that the application reports an unknown/undefined coordinate system without a warning that a learner would be tempted to "fix".

**Route B — ArcGIS Pro, XY Table To Point.** The tool "creates a point feature class based on x-, y-, and z-coordinates from a table" with parameters Input Table, Output Feature Class, X Field, Y Field, Z Field (optional), Coordinate System (optional) [X12]. **Caution:** its *default* coordinate system is WGS84 geographic [X12]. Leaving the default would label the fixture's metre values as degrees (and values above 90 are not valid latitudes). Clear the Coordinate System parameter so the output is "Unknown", or build the wards and road first from WKT by another route and match. *Verify:* the layer properties report an unknown coordinate system and the six points plot at the expected grid positions relative to one another.

**Route C — QGIS, Add Delimited Text Layer.** The delimited-text import can read WKT and x/y columns; it asks for a geometry CRS. The author has not verified how QGIS 3.40 behaves when no CRS is chosen for this fixture; before teaching, test whether the layer can be left with an invalid/undefined CRS or whether a placeholder must be used, and document the choice so learners are not misled in Chapter 5.

**Project assembly (both applications).** Order the layers Requests (top), Roads, Wards (bottom); give Requests a label on `request_id`; save as `Chapter01_Town.aprx` / `Chapter01_Town.qgz` with the data in a `Chapter01_Data` folder beside it using relative paths. Record the exact application version in this appendix's change log when done.

**Change log.** 2026-09-18 — project not yet built; procedures documented from official pages only.

## I.6 Notes on the blueprint and source coverage

- No factual error was found in the Chapter 1 specification. The blueprint's fixture answers (Appendix A.2 items 5–8) were re-derived by hand and agree.
- Blueprint 1.2.1 cites [S01] for "geographic data, software, hardware, and the people/processes". [S01] defines three components (data, hardware, software) and states that GIS "refers to all aspects of managing and using digital geographical data"; it does not name people or processes as a component. The chapter therefore presents the fourth component as a teaching framing supported by the Esri definition [X01] and standard practice, not as a claim from [S01]. This is a coverage note, not a correction.
- Blueprint URL for [S02] redirects (HTTP 301) to `doc.esri.com`; the reference list records the destination. Update the blueprint register at the next revision.
- ArcGIS Pro navigation steps were taken from a tutorial written with ArcGIS Pro 3.2 [X02] published under the 3.7 documentation set; re-verify labels in the installed release.

---

# Media Appendix

## M.1 Slide outline (mapped to module IDs)

| # | Slide group | Module | Visual | Speaker note / question before reveal |
| --- | --- | --- | --- | --- |
| 1 | The decision | 1.1.1 | The recurring question in large type; a two-column split "Policy / Evidence" | Ask: "Which of these can software decide?" before revealing the split |
| 2 | Four vague words | 1.1.2 | The "near" threshold table from 1.1.2 (thresholds vs qualifying IDs) | Reveal ≤ 300 first, then < 300; ask learners to predict before showing the second row |
| 3 | Table vs map | 1.1.3 | Diagram D1 sequence: table → dots → dots + road → dots + road + wards | Ask which questions each stage makes easy; show Table 1.3 last |
| 4 | What a GIS includes | 1.2.1–1.2.2 | Table 1.4 components; Table 1.5 four artefacts | One click per row |
| 5 | The workflow | 1.2.3 | Diagram D2 loop; Worked example 1.2 overlaid step by step | Pause at Validation: "what would you check?" |
| 6 | Six question types | 1.3.1 | Table 1.6 with fixture answers hidden, then revealed | Learners classify four questions first |
| 7 | Absolute, relative, extent | 1.3.2 | Diagram D1 with two extents drawn (A ∪ B vs rectangle to 2200) | "Is P6 in the study area?" |
| 8 | Straight line vs road | 1.3.3 | Diagram D3 | Reveal the 600 m line, then the 900 m path |
| 9 | One record, three kinds | 1.4.1–1.4.2 | Table 1.7 anatomy of P5; Table 1.8 event/asset/boundary | "Which of these should the crew update?" |
| 10 | Who, when, why, missing | 1.4.3 | Four-question card filled for Table F3 | |
| 11 | Layers and interaction | 1.5.1–1.5.2 | Layer list mock-up with check boxes; Table 1.9 three levels | Live demonstration replaces this slide when software is available |
| 12 | Observation log | 1.5.3 | Table 1.10 | |
| 13 | Limits | 1.6 | Table 1.11 rates; the three "convincing and wrong" failures | Ask for alternative explanations before showing the list |
| 14 | Lab brief | 1.7 | The four-part card | |
| 15 | Transition | 1.9 | Workflow loop with "where could this run?" over each box | Bridge to Chapter 2 |

## M.2 Audio-lesson outline

Narration must be understandable with eyes closed. Rules for this chapter's audio: name every record by ID and coordinates the first time it is mentioned; say "x" and "y" and "metres" aloud; never say "this one", "here", "the red dot", or "as you can see"; say "straight-line" every time a distance is quoted.

| Segment | Duration (approx.) | Content | Visual described verbally |
| --- | --- | --- | --- |
| A1 | 3 min | The decision and the policy/evidence split | None needed |
| A2 | 5 min | The four vague words, walked through with the six requests | Describe the grid: "two squares each one thousand metres on a side, side by side; a road along the middle at y five hundred, ending at x two thousand; P six is two hundred metres beyond that end" |
| A3 | 4 min | Table then map: what each makes easy | Describe positions of the six points relative to the road and the shared edge |
| A4 | 5 min | Components, four functions, and the four look-alikes | None needed |
| A5 | 5 min | The workflow loop, narrated as Worked example 1.2 | "Eight steps in a loop; the sixth, validation, is the one we return to" |
| A6 | 5 min | Six question types with one question each | None needed |
| A7 | 4 min | Absolute, relative, extent; straight line vs road | Describe D3: depot at x eight hundred, y two hundred; P two directly north six hundred metres; a canal at y six hundred fifty crossed only at x nine hundred fifty; path of nine hundred metres |
| A8 | 5 min | P5 anatomy; event/asset/boundary; the four provenance questions | Read P5's fields aloud |
| A9 | 4 min | Layers; the three levels of "going away"; the observation log | "Imagine three check boxes in a list…" |
| A10 | 5 min | Limits: the ward table read aloud with the rates computed aloud | Read Table 1.11 row by row |
| A11 | 2 min | Lab instructions and transition | None needed |

## M.3 Diagram specifications

All diagrams are **schematic** drawings of the synthetic fixture. None depicts a real place; none is a measured GIS output.

**D1 — The fixture map (progressive reveal, four frames).** Canvas 2,400 × 1,200 grid units representing x from −100 to 2,300 and y from −100 to 1,100 metres; axes labelled "x (metres)" and "y (metres)" with ticks every 500. Frame 1: Table F3 only. Frame 2: six labelled points at their (x, y). Frame 3: add Road R1 as a thick line from (0, 500) to (2000, 500) with a visible end cap at x = 2000. Frame 4: add Ward A and Ward B as outlined squares with the shared edge slightly emphasised. Labels use ID text, not colour alone; a shape difference (circle vs square marker) may distinguish Open/Reopened from other statuses if a status view is required. Caption: "Synthetic training grid; no real-world location."

**D2 — Question-to-decision loop.** Eight boxes in a ring, numbered 1–8 as in 1.2.3, arrows clockwise, the Validation box visually heavier, with a return arrow from 8 to 1 labelled "refined question". No product names anywhere on the diagram.

**D3 — Straight line versus path.** Inside a square labelled Ward A: depot marker at (800, 200); P2 at (800, 800); a dashed vertical line between them labelled "straight-line 600 m"; a hatched band at y = 650 from x = 0 to x = 900 labelled "schematic canal (illustration only)"; a gap at x = 950 labelled "bridge"; a solid three-segment path (800, 200)→(950, 200)→(950, 800)→(800, 800) labelled "150 + 600 + 150 = 900 m (assumed path, not routed)". P1 at (200, 200) with a dashed line to the depot labelled "600 m straight-line, no obstacle drawn".

**D4 — Record anatomy card.** A card for P5 with three shaded regions labelled Geometry, Attributes, Time, and the field/value pairs from Table 1.7.

**D5 — Three levels of removal.** Three rows: a check box being unchecked; a layer entry being dragged out of a list; a file icon in a bin. Beside each, a database-cylinder icon labelled "stored records" that is unchanged, unchanged, and crossed out respectively.

**D6 — Ward comparison.** Two bars for raw counts (40, 15) beside two bars for rate per 1,000 residents (2.0, 3.0), each with its axis labelled and the word "synthetic" in the title.

## M.4 Optional interactive (2D) — "Where does 'near' end?"

- **Learning objective.** Show that the qualifying set for "near the road" depends on the threshold value and on whether the boundary value counts (1.1.2), and that P6 qualifies while lying outside both wards.
- **Objects.** The D1 frame-4 map; a slider for distance threshold from 0 to 500 m in 10 m steps; a toggle "≤ (inclusive) / < (strict)"; a check box "also require inside Ward A ∪ B (shared edge inside)"; a results panel listing qualifying IDs and a count.
- **Labels and units.** Slider labelled "Straight-line distance to Road R1 (metres)". A translucent band of the current width drawn either side of R1 with square end caps at (0, 500) and (2000, 500) — the band must **not** extend the road's length; around the end points draw a semicircle of the threshold radius so that P6 enters the set at 200 m.
- **Expected behaviour (validation cases, hand-derived in I.4).** Inclusive: 200 → {P5, P6}; 250 → {P3, P5, P6}; 300 → {P1, P2, P3, P5, P6}; 400 → all six. Strict: 300 → {P3, P5, P6}; 250 → {P5, P6}. With the ward check box on: 300 inclusive → {P1, P2, P3, P5}. P5 must qualify at every threshold including 0 m (inclusive) and be excluded at 0 m (strict).
- **Implementation rule.** Distance to a *finite segment*: for 0 ≤ x ≤ 2000 use |y − 500|; otherwise the Euclidean distance to the nearer end point. State this rule in a visible "how this is computed" note. No engine or library is prescribed; whatever is used must reproduce the validation cases above before the interactive is released.
- **Accessibility.** Results are listed as text, not only drawn; the band is distinguishable from the road by pattern, not only colour.

## M.5 3D content

None proposed. The chapter contains no height information and no concept for which a 3D scene would teach better than the 2D diagrams and tables above; the blueprint's 1.9.2 states the same.

---

*End of Chapter 1 document.*
