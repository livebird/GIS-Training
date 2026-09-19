# GIS and ArcGIS Fundamentals — Phase 1

# Chapter 9 — Data capture, editing, and quality

## Document metadata

| Item | Value |
| --- | --- |
| Chapter ID | Phase 1, Chapter 9 |
| Title | Data capture, editing, and quality |
| Source specification | `GIS_Phase_1_Chapter_Authoring_Blueprint.md`, revision 1.0 (18 September 2026) |
| Document revision | 1.0 — 19 September 2026 |
| Reference-check date | 19 September 2026 (every cited page was opened and read on this date; see the reference list for exact URLs) |
| Audience | Software developers at LiveBird Technologies who have completed Chapters 1–8 and have no other GIS background |
| Software referenced | ArcGIS Pro ("latest" documentation, whose page metadata identifies the release as **3.7** on the check date); ArcGIS Field Maps ("latest" documentation, one page, for GNSS metadata); QGIS Desktop **3.44** User Guide (the long-term-release edition on the check date) and the QGIS *Gentle Introduction to GIS* 3.44 edition named in the blueprint; PostGIS documentation for the transferable database examples; GPS.gov for the GNSS vocabulary |
| Execution status | **No procedure in this chapter has been executed in ArcGIS Pro, QGIS, or a database by the author.** All expected values were derived by hand from the printed fixture and are labelled *hand-checked*. Every software procedure is written from the official page cited beside it and is marked **not execution-tested**; an instructor must run the lab once in the installed versions (Instructor Appendix I.6) before issuing it. |
| Data status | Every coordinate, record, identifier, date, time, name, and defect in this chapter is **synthetic training material**. The "damaged" package was damaged deliberately for teaching. Nothing describes a real municipality, a real survey, a real scan, or a real GNSS receiver's performance. |

### How to use this document

Read the modules in order; each one assumes the ones before it. Boxes labelled **Procedure (version-specific)** contain software steps that will age; everything outside those boxes is durable concept. Boxes labelled **Platform note** separate ArcGIS-, QGIS-, or database-specific behaviour from general GIS principles, and boxes labelled **Verification item** tell an instructor what must be checked in the installed environment before a step is treated as fact. Comprehension checks have no answers in the learner text; the answers are in the Instructor Appendix, which learners should not open before the progression gate in 9.9.

This chapter is about *imperfect* data. Chapter 8 designed the tables the municipality *should* have; this chapter is what happens when real records arrive in them — captured by different methods, edited by different people, and containing mistakes. The skill being taught is not "run the repair tool". It is: *look, classify, decide with evidence, correct on a copy, and write down what you did and what you could not resolve.*

---

## Prerequisites

- **Chapter 5 completed.** You can read a coordinate with its coordinate reference system (CRS), units, and order, and you know that a dataset's CRS is metadata that can be missing or wrong.
- **Chapter 6 completed.** You can tell *assigning* a CRS (Define Projection: metadata only) from *reprojecting* (Project: coordinates recomputed) [S15] [S16]. This chapter adds two more operations that beginners confuse with those — georeferencing and geocoding — and asks you to keep all four apart.
- **Chapter 7 completed.** You can fill in a dataset intake form, classify a dataset as measured, digitised, geocoded, derived, or simulated, and take a before/after snapshot around a conversion. Chapter 9 reuses that habit around *edits* instead of conversions.
- **Chapter 8 completed.** You know the Chapter 8 schema (Asset, Inspection, Team, Request, Ward), the domains `AssetType` {SL, DR, TR}, `ConditionScore` 1–5, `PoleHeightM` 3–15 and `LocationMethod` {SURVEY, GNSS, DIGITISED, APPROX}, the identity policy, and the orphan query. Chapter 9's defects are violations of exactly those rules.
- **Chapter 3 (geometry vocabulary).** Vertex, segment, ring, exterior/interior ring, multipart, closed line versus polygon. Chapter 3 deliberately deferred *validity* to this chapter.
- Ordinary developer experience: reading a small SQL query, keeping a change log, and working on a copy rather than on production.

## Learning outcomes

By the end of this chapter you should be able to:

| ID | Outcome | Where it is taught | Where it is checked |
| --- | --- | --- | --- |
| LO1 | Review a dataset against five separate quality questions — positional accuracy, attribute correctness, completeness, logical consistency, currency — judge whether it is adequate for a *stated* decision, and complete an intake checklist before editing. | 9.1 | 9.9 concept Q1; lab step 1 |
| LO2 | Compare manual digitising, coordinate import, GNSS observation, address geocoding, and georeferencing of scans; state what uncertainty and provenance each method must carry; explain that GPS is one GNSS among several and that accuracy is a property of a device in an environment, not of a method. | 9.2 | 9.9 concept Q2; scenario S1 |
| LO3 | Explain georeferencing as fitting a transformation through control points; compute a simple image-to-map transformation by hand; explain why a small fitting residual does not prove accuracy and why an independent check point is needed; distinguish georeferencing from CRS assignment and reprojection. | 9.3 | 9.9 concept Q3; oral |
| LO4 | Create, move, split, and reshape features on a copy; predict the attribute and relationship consequences of an edit before making it; use vertex/edge snapping with a stated tolerance and explain when snapping damages data; review an edit visually and in the table. | 9.4 | 9.9 concept Q4; lab steps 4–5 |
| LO5 | Separate *invalid geometry* (a shape that cannot be interpreted) from a *topology-rule violation* (valid shapes that break a business rule); state the validity rules for polygons; choose domain-specific rules for wards, roads, and assets; recognise legitimate exceptions (cul-de-sac, intentional overlap, bridge crossing). | 9.5 | 9.9 concept Q5; scenario S2; lab |
| LO6 | Detect duplicate business identifiers, invalid categories, missing required observations, unit inconsistencies, and orphaned related records with queries; distinguish two reports at one location from an accidental duplicate using evidence other than proximity; write a defect log. | 9.6 | 9.9 concept Q6; lab |
| LO7 | Apply corrections with traceability: preserve the original, record before/after values, trace symptoms to their source cause, inspect what an automated repair actually did, and keep unresolved issues explicit instead of fabricating values. | 9.7 | 9.9 scenario S2; practical; lab |
| LO8 | Given a deliberately damaged dataset, classify each defect, correct those that the evidence supports, leave the rest documented, and avoid "repairing" valid features. | 9.8 | 9.8 lab; 9.9 practical |

## Required materials

- This document and a text editor or spreadsheet for the defect log and the conversion log.
- The **Chapter 9 damaged package** (`Chapter09_Damaged/`), printed in full in module 9.8 as CSV text with WKT geometry, exactly as the Chapter 3 Town package was. The instructor builds the GeoPackage or file geodatabase from it by the Chapter 3 routes (Chapter 3, Instructor Appendix I.5) and issues both the built package and the CSV text. If no built package is available, every check in the lab can still be done from the CSV text and graph paper — the geometry is small enough to draw.
- **Primary route:** ArcGIS Pro 3.x (any licence level for the tools used; geodatabase *topology* requires Standard or Advanced [X03] and is optional). **Alternative route:** QGIS Desktop 3.44 with the core Topology Checker plugin enabled [X19]. **No ArcGIS Online account, credits, GNSS receiver, scanner, or locator is needed**; the georeferencing and geocoding material is taught with hand-checked arithmetic and documentation, not by running those tools.
- Graph paper or a drawing tool for the sketches in 9.3 and 9.5.

## Recap of the preceding chapter

Chapter 8 turned the municipality's records into a designed schema. Assets, inspections, requests, wards, and teams became separate tables with a stated grain; every field was justified by a business question; geometry got a specification (type, multipart policy, CRS and units, what the point stands for, how the location was obtained — the `location_method` field); business identifiers were separated from display labels and from storage row identifiers; relationships were modelled with keys and cardinality; coded-value and range domains gave `asset_type`, `condition_code`, and `pole_height_m` their allowed values; and history was kept as one inspection row per visit with event time separate from entry time. The lab converted a deliberately poor flat table into that schema and finished with the **orphan query** — a left join that lists child rows whose parent does not exist — returning zero rows.

This chapter loads *imperfect* data into that design. The domains tell you which categories are invalid; the identity policy tells you what a duplicate identifier means; the orphan query is run again and, this time, returns a row.

## The recurring scenario and the Chapter 9 fixture

The fictional municipality continues. All coordinates are on the flat, metre-based **training grid** of Chapters 1, 3, and 8: (x, y), x to the right, y upward, **no Earth location and no coordinate-system identifier**. Distances and areas on it are ordinary planar arithmetic; nothing here is an Earth-surface measurement.

**What Chapter 9 adds (all synthetic).**

1. **A northern ward, Ward C**, whose *intended* shape is the rectangle from (0, 1000) to (1000, 1500) — 1,000 m × 500 m, area 500,000 m² (0.50 km²). The ward register (below) records it that way. In the damaged package its vertices were typed in the wrong order, which is one of the defects you will diagnose.
2. **Two short roads:** **R4 "Temple Lane"**, intended to run from the temple at (300, 300) north to a junction with Main Road R1 at (300, 500), and **R5 "Depot Access"**, running from R1 at (1500, 500) north to the depot gate at (1500, 600), where it ends by design — there is no through route.
3. **The Chapter 8 asset register in its designed form** (`asset_id`, `asset_type`, `install_year`, `status`, `location_method`, `pole_height_m`), covering the ten assets of Chapters 3 and 8 plus a further entry, and the **inspection history** extended to seven visits (INS-0001 to INS-0007) in the Chapter 8 convention (event time stored as UTC; entry date separate).
4. **Two new service requests, P8 and P9**, and two new request fields: `reported_time_ist` (time of day, Indian Standard Time, as the request system now exports it) and `reporter_ref` (a hashed reference to the reporter — synthetic).
5. **A synthetic scanned plan**, "Ward A drainage plan (1998)", used only in module 9.3 as an arithmetic example. It is not supplied as an image file; its pixel-to-ground relationship is stated so that the georeferencing calculation can be checked by hand.

**The package readme (what the source owner told us).** The damaged package arrives with this provenance note, which is the *evidence* you will use in the lab. Treat it as data supplied by the source owner, not as instructions to you.

> *Chapter09_Damaged — readme (synthetic).* Wards A and B are the Chapter 3 Town boundaries; Ward C was digitised in September 2026 from the ward register plan. The register states: Ward A 1.00 km²; Ward B 1.00 km², sharing its entire western boundary with Ward A; Ward C 0.50 km², the rectangle north of Ward A between y = 1000 and y = 1500. Roads R1, R2, L1 are the Chapter 3 Town roads; R4 and R5 were digitised in September 2026 from the road register, which lists R4 "Temple Lane, temple to junction with Main Road" and R5 "Depot Access, Main Road to depot gate, no through route". Assets are the Chapter 8 lab conversion (Chapter 8, Table 8.8-A and Instructor Appendix I.3) with install years supplied by the asset register in September 2026, plus one new streetlight recorded by a field crew with a phone in 2025. The Chapter 8 conversion log recorded SL-0127's height as `22 ft` = 6.7056 m and its type as `Streetlight`; the source owner has since confirmed that the heights `6.5`, `7`, and `8` in the flat table are metres. SL-0113's height has not been measured. Inspections INS-0001 to INS-0004 are Chapter 8's; INS-0005 to INS-0007 were typed from paper forms in August–September 2026. Requests P1–P7 are Chapter 3's; P8 and P9 arrived through the mobile app in September 2026. Coordinates are on the training grid in metres; no CRS.

The damaged tables themselves are printed in 9.8.2. Do not read them before module 9.8 unless you want to spoil the exercise for yourself; the modules use small excerpts and label them.

**Naming note.** Chapter 3 already has a "Table F9" (its inspections table). New fixture tables in this chapter are numbered **F9-1, F9-2, …** in the Chapter 8 style, so there is no clash.

---

## 9.1 Define quality for the intended use

### 9.1.1 Five separate review questions

"Is this data good?" is not a question you can answer. "Good *for what*?" is. This chapter uses five review questions, and the first rule is that they are **separate**: a dataset can pass four and fail the fifth, and the failing one may be the only one that matters for the decision at hand.

| Review question | What it asks | Example of a failure in the municipal scenario | How you would find out |
| --- | --- | --- | --- |
| **Positional accuracy** | How far is each stored location from where the thing really is? | A drain stored 6 m from the grating because the point was placed from a phone in a narrow street | Compare a sample against a better-known position (survey, a later high-accuracy observation); read the capture method and its reported uncertainty |
| **Attribute correctness** | Are the stored values true? | `asset_type` = "Lamp" for a streetlight; `pole_height_m` = 22 for a 6.7 m pole | Compare against the source form, a photo, or a site visit; check against domains and ranges |
| **Completeness** | Is everything there that should be — and nothing that should not? | A visit with no condition score; an inspection row for an asset that does not exist; a request with no location (Chapter 3's P7) | Count against an independent expectation; look for nulls in required fields; run the orphan query |
| **Logical consistency** | Do the records obey the rules they are supposed to obey — geometry rules, topology rules, schema rules? | A ward polygon that crosses itself; two wards with a sliver gap between them; a lane that stops 3 m short of the road it joins; two rows with the same `asset_id` | Validity checks, topology rules, uniqueness and domain queries |
| **Currency** | Is the data recent enough for the decision? | A 2019 ward layer applied to 2026 requests (Chapter 7, 7.6.3); an inspection from 2024 used as "current condition" | Compare the data's "as of" date with the decision's date; check `visited_at` against the decision window |

**Why keep them separate.** Each question has a different *cure*. Poor positional accuracy is fixed by re-observing, not by editing attributes. An invalid category is fixed at the source form, not by moving a point. A stale layer is fixed by obtaining a newer one, not by "repairing geometry". If you lump them into one "quality score", you lose the information about what to do.

**Developer analogy — and where it stops.** Think of the five questions as five *test suites* on a data release: an accuracy suite, a schema-validation suite, a completeness suite, an integrity suite, and a freshness check. A build that passes four suites and fails one is still not shippable, and the failing suite tells you which team to call. The analogy stops at *positional accuracy*: there is no unit test that can tell you how far a stored point is from a real grating. That answer only comes from a second, independent observation of the world, which costs money and time — so positional accuracy is usually *estimated from a sample* and *described*, not "tested to pass".

**Comprehension check 9.1.1.** Inspection INS-0004 (Chapter 8) describes a visit on 14 September 2025 but was entered on 16 March 2026 from a paper form found late. Which of the five questions does the six-month delay bear on, and which does it *not* bear on? Give one sentence for each of the five.

### 9.1.2 Adequate for the stated decision: two contrasting uses

The blueprint asks a specific question of every source: *is it adequate for the stated decision?* — and forbids inventing numerical accuracy thresholds. So the honest answer is always a comparison between what the decision needs and what the source's provenance says it delivers, not a number pulled from the air.

**Use 1 — a ward overview map for the council.** Decision: "which ward has the most open requests?" Needs: correct ward membership for each request, current status values, and a ward layer that matches the current ward boundaries. Does *not* need: metre-level positions — a request 5 m from its true place is still in the same ward, unless it sits near a boundary (P5, on the shared edge, is the case to watch). A geocoded request position (9.2) is fine here. Currency and attribute correctness dominate.

**Use 2 — locating drain DR-0042 for excavation.** Decision: "where exactly do we dig to reach the drain's connection?" Needs: a position whose stated uncertainty is small compared with the trench width, a statement of *what the point represents* (grating centre? chamber? connection?), and a capture method with evidence (survey or a high-accuracy GNSS observation with its metadata). A point digitised from a 1:5,000 plan, or placed from a phone with an unstated accuracy, is **not adequate**, however correct its attributes are. Positional accuracy and geometry meaning dominate.

The *same* asset row can be adequate for use 1 and inadequate for use 2. That is what "fitness for purpose" (Chapter 1, 1.6.2) means in practice. The dataset does not change; the requirement does.

**Misconception.** *"The data was collected by professionals, so it is accurate."* Consequence: the excavation team digs 6 m from the drain. The remedy is to read the provenance — *how* was it collected, *with what*, *when*, *for what purpose* — and to ask for the uncertainty that accompanied the observation (9.2.3). "Collected by professionals" is not a capture method.

**Comprehension check 9.1.2.** A contractor supplies tree positions "accurate to 3 m". Name two pieces of evidence you would ask for before accepting that statement, and one decision for which 3 m would be adequate and one for which it would not.

### 9.1.3 The intake checklist: before you edit anything

Chapter 7 gave you a dataset intake form for a *conversion*. Editing needs the same discipline, because an edit is the one operation in this course that *changes the source of truth*. Complete this checklist before the first edit and attach it to the defect log (9.6.3).

**Table 9.1a — Chapter 9 intake checklist (complete before editing).**

| # | Item | What to record | Why it matters here |
| --- | --- | --- | --- |
| 1 | **Source** | Who supplied the data, from what system, and what the readme says about how each layer came to exist (measured / digitised / geocoded / derived / simulated — Chapter 7, 7.6.2) | Decides what evidence a correction can rest on, and who the "source owner" is when you cannot decide |
| 2 | **Date** | The "as of" date of each layer and of the readme | Currency (9.1.1); tells you whether a "defect" might be a legitimate later change |
| 3 | **Reference** | The CRS, units, and coordinate order as *recorded* (Chapter 5 checklist) — or, for the training grid, the explicit statement "planar, metres, no CRS" | Every tolerance you set in 9.4–9.5 is in these units; snapping "10 pixels" means a different ground distance at every zoom |
| 4 | **Schema** | The data dictionary and domains you expect (Chapter 8), so that "invalid category" has a definition | Without the domain, "Lamp" is just a word |
| 5 | **Original copy** | The path and checksum (or at least the file size and modification time) of the untouched original, stored read-only | Traceability (9.7.1); every before/after comparison is made against it |
| 6 | **Intended output** | What the corrected copy is *for* and who will consume it; which checks it must pass before release | Decides which defects block release and which can stay documented and unresolved |

**Worked example — filling item 5 for the lab package.** Before touching `Chapter09_Damaged/`, copy the whole folder to `Chapter09_Work/`, mark the original folder read-only, and write into the log: "Original: `Chapter09_Damaged/` (nine CSV files, received 2026-09-19 from the instructor; not modified). Working copy: `Chapter09_Work/`. All edits are made in the working copy only." If the package was issued as a GeoPackage or geodatabase, copy that file too; never edit the issued one. Chapter 7 (7.7.3) said the same thing about conversions: *preserve the original, name outputs meaningfully*.

**Misconception.** *"I can always undo."* Undo exists inside an editing session; once edits are **saved** they are written to the data source — ArcGIS Pro's **Save** "saves all edits you made since the last time you saved your changes", and **Discard** "rolls back all edits you made since the last time you clicked Save" [X09]. QGIS's **Save Layer Edits** commits and **Rollback** discards unsaved changes [X16]. After a save, the only "undo" is the original copy you kept in item 5.

**Comprehension check 9.1.3.** The readme says Ward C was digitised in September 2026 from the ward register plan and that its area is 0.50 km². Which checklist items does that one sentence fill, and what does it *not* tell you that you would still need before correcting Ward C's geometry?

## 9.2 Compare capture methods

### 9.2.1 Five different workflows that all produce "a point on the map"

A location in a GIS table looks the same whatever produced it: a pair of numbers. The methods that produce those numbers are very different, and the difference is the *uncertainty* and the *provenance* the numbers should carry. Five methods cover almost everything the municipality does:

| Method | What the operator actually does | What the numbers mean | What can go wrong | Provenance it must carry |
| --- | --- | --- | --- | --- |
| **Manual (heads-up) digitising** | Clicks vertices on the screen over a backdrop — an aerial image, a scanned plan, an existing layer. QGIS: "You can then use this layer as a reference map, or even trace the features off the raster layer into your vector layer" [S24] | "Where the operator judged the thing to be *on the backdrop*" — inherits the backdrop's accuracy and the operator's care | Backdrop poorly georeferenced ("the newly captured data will be inaccurate!" [S24]); wrong zoom level ("it is important that you are zoomed in to an appropriate scale" [S24]); snapping to the wrong thing (9.4.2) | Backdrop name, its date and georeferencing quality, the scale/zoom used, the operator, snapping settings |
| **Coordinate import** | Loads a table that already contains coordinates (Chapter 7's CSV; Chapter 8's flat table) | Whatever the *upstream* system meant — you did not observe anything | CRS, unit, or order wrong (Chapter 5); decimal precision truncated; columns swapped | The upstream system, its CRS/units/order declaration, the export date, and — crucially — the *upstream* capture method |
| **GNSS observation** | Stands at the thing with a receiver (a phone's built-in receiver or an external one) and records the position | The receiver's estimate of the antenna's position at that moment, with the receiver's own accuracy estimate | Poor sky view (narrow streets, trees), multipath, no correction service, wrong datum handling between receiver and map [X15]; the operator not standing *at* the thing | Receiver, fix type, reported horizontal accuracy, number of satellites, dilution-of-precision values, correction service, time, and antenna height — the metadata Field Maps can record [X15] |
| **Address geocoding** | Feeds text ("12 Temple Lane") to a *locator* that matches it against reference data and returns a position | "Where the reference data says that address is" — which may be a rooftop point, an *interpolated* position along a street segment, a postcode centroid, or a town centre [X11] | Ambiguous or misspelled addresses; reference data older than the street; a match at a coarser level than you assumed | Locator and reference-data date, match status (M/T/U), match score, address type (`PointAddress`, `StreetAddress`, `Postal`, …) [X11] |
| **Georeferencing a scan** | Places an image that has no coordinates by identifying control points on it and fitting a transformation (9.3) | "Where the image *pixels* land after the fit" — features later digitised from it inherit the fit's error *and* the scan's distortions | Too few or badly placed control points; a distorted original; trusting the residual (9.3.2) | Control-point list with residuals, transformation type, source document and its date, the independent check |

The table has one message: **every method needs a provenance record, and the records differ.** Chapter 8's `location_method` domain {SURVEY, GNSS, DIGITISED, APPROX} was the first step. Looking at the table, one value is missing: a geocoded position is neither digitised nor a GNSS observation. Adding `GEOCODED` to the domain is a *schema change* with an owner and an impact checklist (Chapter 8, 8.7.3) — not something an editor does quietly in the middle of a correction. Module 9.7 returns to this.

**Developer analogy — and where it stops.** Provenance is like a commit's metadata: author, timestamp, the tool that produced the change, and a message saying why. A repository without it still "works", but nobody can tell a deliberate change from an accident. The analogy stops at *uncertainty*: a commit is exact — the diff is the diff — whereas a GNSS observation or a digitised vertex is an *estimate*, and the metadata must include how wrong it might be, not just who made it.

**Comprehension check 9.2.1.** Request P9 in the Chapter 9 package was submitted from the mobile app with coordinates (1000, 500). Name two capture methods that could have produced that pair, and say what single provenance field would tell them apart.

### 9.2.2 GPS is one GNSS; accuracy belongs to a device in an environment

People say "GPS" for any satellite positioning. Be precise, because the field-app settings and the metadata use the general term. **GNSS** — "Global navigation satellite system (GNSS) is a general term describing any satellite constellation that provides positioning, navigation, and timing (PNT) services on a global or regional basis" [X14]. **GPS** is the United States system; "While GPS is the most prevalent GNSS, other nations are fielding, or have fielded, their own systems" — the page lists BeiDou (China), Galileo (European Union), GLONASS (Russian Federation), NavIC/IRNSS (India), and QZSS (Japan) [X14]. A modern phone or receiver typically uses several of these together. For an Indian municipality, a receiver that also tracks NavIC is unremarkable; what matters for your data is not which constellations, but what the receiver *reported* about its own solution.

**Do not assign a fixed accuracy to "phones" or "receivers".** The blueprint forbids it, and the vendor documentation does not do it either: the Field Maps page says the app "can make use of the GPS built into your device or you can add an external GPS receiver to obtain high-accuracy data" and lets a map author "set the required accuracy of GPS positions and whether the positions must meet a 95 percent confidence level" — it states no accuracy number for any phone [X15]. Accuracy is a property of *this receiver, in this sky view, with this correction service, at this moment*. That is why the observation must carry its own metadata: the Field Maps page lists the GNSS metadata fields that can be stored with a feature — receiver name, latitude, longitude, altitude, horizontal accuracy, vertical accuracy, fix type, fix time, number of satellites, PDOP/HDOP/VDOP, correction age, station ID, and more — "for validating the quality of the GNSS positions" [X15]. If your asset table has a `location_method` = GNSS row with none of that, you know the *method* but not the *quality*.

**One more trap: the receiver's datum versus the map's.** A receiver reports geographic coordinates on *its* reference; if that differs from the map's, the position "must be transformed to match the map's coordinate system" and a location profile with a datum transformation is needed [X15]. This is Chapter 6's transformation question arriving through the field app. Metre-level offsets between "the same" position from two devices are a symptom to investigate (Chapter 5, S2), not noise to average away.

**Misconception.** *"Our phones are accurate to 3 m, so all our GNSS points are within 3 m."* Consequence: a drain captured under a tree canopy with a 15 m reported accuracy is treated as a 3 m point, and the excavation misses. The reported accuracy is per observation; the design must store it and the review must read it.

**Comprehension check 9.2.2.** Two rows in an asset table both say `location_method` = GNSS. One has `horizontal_accuracy_m` = 0.4 and `fix_type` indicating a corrected fix; the other has `horizontal_accuracy_m` = 12 and no correction. Which of the five quality questions (9.1.1) distinguishes them, and what decision from 9.1.2 would treat them differently?

### 9.2.3 An address match, a roof location, and a surveyed entrance are three different observations

The same building can be "located" three ways, and the three points should never be merged into one layer without a field that says which is which.

**Worked example — "12 Temple Lane" three ways (synthetic; training grid).**

| Observation | How obtained | Coordinates | What the point stands for | Provenance to store |
| --- | --- | --- | --- | --- |
| A — **address match** | The text "12 Temple Lane" geocoded; the locator returned a `StreetAddress` match, i.e. "the house number is interpolated from a range of numbers" along the street segment [X11], with a side offset | (296, 402) | A point *near the road* on the correct side, at a position computed from the house-number range — not the building | Locator, reference data date, `Status` = M, `Score`, `Addr_type` = StreetAddress [X11] |
| B — **roof location** | An operator digitised the roof centre from an aerial image | (312, 405) | The centre of the roof as seen in the image, with the image's georeferencing error | Image name and date; operator; zoom; snapping off |
| C — **surveyed entrance** | A crew observed the entrance gate with a corrected GNSS receiver | (306.2, 398.7) | The gate — the place a crew must reach | Receiver, fix type, horizontal accuracy 0.3 m, time, antenna height |

Nothing is "wrong" in this table: A is exactly what an address match is, B is exactly what a roof point is, C is exactly what a gate survey is. The *error* would be to put all three in one `Buildings` layer as if they were the same fact, or to use A for excavation, or to "correct" A to C because C is more accurate — A and C describe different things. Chapter 8's rule (8.2.2 — "define what a point represents") is the schema-level defence; `location_method` plus the method-specific metadata is the record-level one.

**Platform note — geocoding results are explicit about their level.** ArcGIS geocoding output includes `Status` (M matched, U unmatched, T tied), `Score` (0–100), and `Addr_type`, whose values distinguish `PointAddress` ("based on points that represent house and building locations"), `StreetAddress` (house number interpolated), `StreetName`, `Postal`, `Locality`, and others [X11]; for point-address matches the tool can return either an *address location* ("a rooftop location, parcel centroid, or front door") or a *routing location* ("close to the side of the street") [X17]. These fields are the provenance. Keep them in the output ("Minimal" output fields include Status, Score, Match_type, Match_addr, and Addr_type [X17]); do not export "Location Only" for anything you will later need to trust. Geocoding with the ArcGIS World Geocoding Service "may consume credits" [X17] — a cost question for the organisation, outside this chapter. Other platforms' geocoders return analogous but differently named fields; check each one's documentation rather than assuming the same vocabulary.

**Misconception.** *"A matched address is a located building."* Consequence: a request geocoded to a postcode centroid (`Addr_type` = Postal) is treated as a point on a street and assigned to the wrong ward. The remedy is to read `Addr_type` and `Score` before using the point for anything finer than the match level.

**Comprehension check 9.2.3.** A `Requests` layer has 1,200 rows; 900 have `Addr_type` = StreetAddress, 250 = Postal, 50 = Locality. For which subset is "assign each request to a ward" defensible on the training grid's 1 km wards, and what would you do with the rest?

## 9.3 Explain georeferencing without confusing it with projection

### 9.3.1 Control points and a transformation model

A scanned plan is a grid of pixels. Its coordinates are *column* and *row* — numbers that count pixels from the top-left corner and mean nothing on the ground. "Scanned maps and historical data usually do not contain spatial reference information" [S26]. **Georeferencing** is the process of giving such an image a location: "When you georeference your raster data, you define its location using map coordinates and assign the coordinate system of the map frame" [S26]. It has two ingredients:

1. **Control points** — "locations that can be accurately identified on the raster dataset and in real-world coordinates" [S26]: a surveyed benchmark drawn on the plan, a road junction visible both on the scan and in a trusted layer, a grid cross printed on the sheet. Each control point is a *pair*: (column, row) on the image ↔ (x, y) on the ground.
2. **A transformation model** — a formula that converts *any* image (column, row) into ground (x, y), fitted so that the control points land as close as possible to their ground positions. The simplest useful model is the **first-order (affine)** transformation, which can "shift, scale, and rotate a raster dataset" and turns squares into "parallelograms of arbitrary scaling and angle orientation" [S26]; it needs at least 3 control points [S26]. Higher-order polynomials bend the image (6 and 10 points minimum for second and third order [S26]) and are for curved or warped originals; QGIS warns that with them "straight lines may become curved, and there may be significant distortion introduced at the edges or far from any GCPs" [X18].

**Worked example 9.3-A — a three-point affine fit by hand (synthetic).** The scanned "Ward A drainage plan (1998)" is 600 × 600 pixels. Image coordinates are (c, r): column c increases to the right, row r increases *downward* from the top-left corner — the usual image convention, and the opposite of the training grid's upward y. Three grid crosses printed on the sheet are identifiable, and their ground positions are known from the training grid:

| Control point | Image (c, r) | Ground (x, y), metres |
| --- | --- | --- |
| G1 | (150, 50) | (200, 1000) |
| G2 | (550, 50) | (1000, 1000) |
| G3 | (150, 450) | (200, 200) |

*Fit the model.* An affine transformation is x = a + b·c + d·r and y = e + f·c + g·r. With exactly three points it can be solved directly:

- G1 → G2 changes c by +400 and r by 0, and x by +800, y by 0. So each column step is 2 m in x and 0 m in y: b = 2, f = 0.
- G1 → G3 changes r by +400 and c by 0, and y by −800, x by 0. So each row step is −2 m in y and 0 m in x: g = −2, d = 0.
- Substitute G1 to get the offsets: 200 = a + 2·150 → a = −100; 1000 = e − 2·50 → e = 1100.

Result: **x = 2c − 100, y = 1100 − 2r.** The scan is 2 m per pixel, unrotated, with y flipped — exactly what a world file for this image would say. *Check:* G2: x = 2·550 − 100 = 1000 ✓, y = 1100 − 100 = 1000 ✓. G3: x = 200 ✓, y = 1100 − 900 = 200 ✓.

*Residuals.* Every control point lands exactly on its ground position, so each **residual** — "the difference between where the from point ended up as opposed to the actual location that was specified" [S26] — is 0, and the **RMS error** (root mean square of the residuals [S26]) is 0.00 m. This is not an achievement; three points *always* fit an affine exactly, because six equations determine six unknowns. Module 9.3.2 is about why that zero proves nothing.

**Developer analogy — and where it stops.** Fitting an affine through control points is linear regression: parameters are chosen to minimise the squared residuals, and with as many points as parameters the fit is exact and the residuals are meaningless. The analogy is accurate — the georeferencing tools genuinely do least squares when "more than the minimum GCPs are specified" [X18]. It stops at *what is being fitted*: a regression fits a trend through noisy data, but here the *image itself* may be systematically distorted (stretched paper, a curled scan, a folded sheet) in a way no low-order model can follow, so a good fit at the control points can coexist with large errors between them.

**Comprehension check 9.3.1.** Using the fitted model, what ground position does image pixel (400, 250) map to? A drain digitised there — what does its stored position inherit from the scan?

### 9.3.2 Placement, independent checking, and why a small residual is not accuracy

The vendor page says it plainly: "Although the RMS error is a good assessment of the transformation's accuracy, don't confuse a low RMS error with an accurate registration" — "the transformation may still contain significant errors due to a poorly entered control point" [S26], and "Adding more links will not necessarily yield a better registration" [S26]. Three practices follow.

**1. Spread the control points.** "Typically, having at least one link near each corner of the raster dataset and a few throughout the interior produces the best results", and "you should spread the links over the entire raster dataset rather than concentrating them in one area" [S26]. In Worked example 9.3-A, G1, G2, and G3 occupy three corners; the fourth corner (bottom-right) has no control — and that is where the trouble is.

**2. Keep an independent check point.** A **check point** is a location whose ground position you know but which you *withhold* from the fit. Its residual is honest because the fit never saw it.

**Worked example 9.3-B — the check point that reveals the stretch (synthetic).** A surveyed benchmark, K1, is at ground (1000, 200) — the bottom-right corner region. On an undistorted 2 m/pixel scan it would appear at image (550, 450). On *this* scan, because the lower part of the sheet was stretched as it went through the scanner, the benchmark's mark appears at image **(550, 456)**. Apply the fitted model: x = 2·550 − 100 = 1000; y = 1100 − 2·456 = **188**. The model places K1 at (1000, 188): **12 m south of its true position**, while the RMS error of the fit reads 0.00 m. Anything digitised from the lower part of this scan is about 12 m out, and nothing in the control-point table would have told you.

**3. Do not "improve" the fit by adding the check point.** Suppose you add K1 as a fourth control point and refit (least squares, since four points over-determine an affine). The arithmetic is in Instructor Appendix I.1; the outcome is that the x-residuals stay 0 and the y-residuals become approximately +3.0, −3.0, −3.0, +3.0 m at G1, G2, G3, K1 respectively, so the **RMS error becomes about 3.0 m**. The 12 m distortion has not gone away — it has been *spread* over four points as ±3 m residuals, and the three previously exact corners are now each displaced by about 3 m. The report looks better; the map is worse in three corners and still wrong in the fourth. A higher-order polynomial could drive all four residuals to zero — and would then have *no* independent check left at all, with the extrapolation risk the QGIS page describes [X18].

**What to do instead.** Report the check-point discrepancy (12 m at K1) as the evidence of accuracy, state that the fit used three corners, and either (a) obtain more control in the distorted region and use a model that can follow it, with *new* check points held out, or (b) accept the scan for coarse use only and record "positional accuracy: about 12 m in the south-east; not for excavation" in the provenance. What you must not do is quote "RMS 0.00 m" as the accuracy.

**Platform notes.**
- *ArcGIS Pro.* The **Georeference** tab (select the raster layer, **Imagery** tab ▸ **Georeference**) has a **Prepare** group (Set SRS, Fit To Display, Move/Scale/Rotate), an **Adjust** group with **Add Control Points** and the transformation drop-down, a **Review** group whose **Control Point Table** shows the residual per point and lets you delete a point, and a **Save** group (Save writes auxiliary files; Save as New writes a new raster; Export Control Points writes the points to a text file) [X10] [S26]. The first-order transformation needs 3 points, second-order 6, third-order 10, projective 4, and spline/adjust more [S26]. **Verification item:** the exact button labels in the installed 3.x release.
- *QGIS 3.44.* **Layer ▸ Georeferencer**; add GCPs with **Add GCP Point**, typing map coordinates or clicking **From map canvas** on a referenced layer; the points are stored in a `[filename].points` text file (mapX, mapY, pixelX, pixelY) [X18]. Transformation types: **Linear** (world file only; translation and uniform scale; ≥ 2 GCPs), **Helmert** (adds rotation; ≥ 2), **Polynomial 1** (affine, "also a uniform shear"; ≥ 3), **Polynomial 2/3** (≥ 6/≥ 10), **Projective** (≥ 4), **Thin Plate Spline** (≥ 10, "rubber sheets" and "will precisely match all specified GCPs") [X18]. The optional PDF report lists "all GCPs and their RMS errors" [X18]. For Linear and Helmert only a world file is written; for the others an output raster is created and resampled (nearest neighbour keeps the original cell values — Chapter 4's rule for categorical rasters) [X18].
- *Transferable principle.* Every georeferencing tool — GDAL's, QGIS's, ArcGIS's — reports residuals *at the control points*. None can report the error *between* them. The check point is your responsibility on every platform.

**Misconception.** *"Ten control points and an RMS under one pixel means the map is accurate to one pixel."* Consequence: a plan whose east half is stretched is used to place a pipe, with a 12 m error nobody measured. Remedy: withhold check points; report *their* discrepancies; spread control across the sheet.

**Comprehension check 9.3.2.** After the three-point fit, a colleague proposes adding six more control points, all along the top edge of the scan, "to bring the RMS down". Explain in two sentences why that neither tests nor fixes the south-east error.

### 9.3.3 Georeferencing is not reprojection (and not CRS assignment)

Learners who have just met Chapter 6 tend to file georeferencing with projections. Keep the operations apart by asking *what exists before the operation*.

| Operation | Before | After | Coordinates changed? | Metadata changed? | Tool (ArcGIS Pro) |
| --- | --- | --- | --- | --- | --- |
| **Georeferencing** (9.3) | An image whose pixels have *no* ground coordinates | The image has a stated pixel-to-ground transformation and a CRS, derived from control points | Yes — ground coordinates are *created* for every pixel (or a new resampled raster is written) [S26] [X18] | Yes — the CRS is assigned as part of the process ("assign the coordinate system of the map frame" [S26]) | Georeference tab [X10] |
| **CRS assignment** (Chapter 6, 6.2) | A dataset whose coordinates exist but whose CRS metadata is missing or wrong | Same coordinates, corrected label | **No** — "only updates the existing coordinate system information; it does not modify any geometry" [S15] | Yes | Define Projection [S15] |
| **Reprojection** (Chapter 6, 6.2) | Correctly referenced coordinates in CRS 1 | Equivalent coordinates in CRS 2 | **Yes** — recomputed by formula (and a datum transformation where needed) | Yes (new CRS) | Project [S16] |
| **Geocoding** (9.2.3) | Text (an address), no coordinates | A point, plus match status/score/type | Yes — coordinates are *created* from reference data | The output inherits the locator's CRS | Geocode Addresses [X17] |

Two of the four *create* coordinates where none existed (georeferencing, geocoding); one *recomputes* them (reprojection); one *relabels* them (assignment). The dangerous confusions are: using Define Projection on an image that has no coordinates (it will not become located — there is nothing to relabel); and "reprojecting" a scan by georeferencing it to a different CRS's layer without recording that the control points were the source of the fit. **Specialised image rectification** — correcting for terrain relief and sensor geometry in aerial and satellite imagery (orthorectification) — is deferred; it is not the same operation as fitting a polynomial through control points, and it is outside Phase 1.

**Comprehension check 9.3.3.** A colleague has a GeoJSON of drains (Chapter 5: therefore WGS 84 longitude/latitude) and a scanned 1998 plan. They say "I'll define the projection of the scan as WGS 84 so they line up." Which row of the table applies to each input, and why will the sentence not work?

## 9.4 Introduce careful editing and snapping

### 9.4.1 Create, move, split, reshape — on a copy, after predicting the consequences

**Editing** is changing stored geometry or attributes. The four geometry edits you need are:

| Edit | What it does | Typical reason | What else changes |
| --- | --- | --- | --- |
| **Create** | Adds a feature: click a point, or click vertices for a line/polygon and finish [S24] | A new asset, a new road, a request located after the fact (Chapter 3's P7) | A new row appears; identifiers must follow the identity policy (Chapter 8); required attributes must be entered — "the GIS Application will ask you to enter the attribute data" [S24] |
| **Move** | Translates a whole feature | A point was placed on the wrong side of the road | Any derived value (distance to road, ward membership) is now stale; any *other* feature that was snapped to it does not move with it unless the software's topological editing does that (9.4.2) |
| **Split** | Cuts one feature into two at a line or point | A road is to be maintained in two sections; a ward is divided | One row becomes two: which keeps the identifier? which attributes are copied, which are recomputed (length!), and what happens to related records that referenced the original? QGIS: "The original feature is then assigned the biggest geometry resulting from the splitting, and new features are created for the remaining parts" [X16] |
| **Reshape** | Redraws part of a boundary or line | A digitised ward edge follows the wrong road | The area/length changes; neighbouring features sharing the edge may now overlap or leave a gap (9.5) unless edited topologically; QGIS requires the reshape line to "cross the polygon's boundary at least twice" [X16] |

**The rule: predict, then edit.** Before every edit, write two sentences — one for attributes, one for relationships — saying what *else* will be true afterwards. Then edit **on the working copy** (9.1.3), then check the prediction (9.4.3).

**Worked example 9.4-A — predicting the consequences of fixing R4 (synthetic).** In the damaged package Temple Lane R4 runs from (300, 300) to (300, 497): it stops 3 m short of Main Road R1 (y = 500). The road register says it joins Main Road. The planned edit: move R4's north endpoint from (300, 497) to (300, 500).

- *Attributes.* R4's length changes from 197 m to 200 m (hand-checked: 500 − 300). If a `length_m` field is stored, it is now wrong until recomputed; if length is derived at read time, nothing to do. `width_m` = 5 is unaffected.
- *Relationships.* No inspection or request references a road in this schema, so no foreign key is touched. But *topologically*, R4 will now touch R1 at (300, 500), which is in the middle of R1's single segment (R1 has only two vertices). Whether the software adds a vertex to R1 at the junction depends on the tool and its settings (QGIS: the Segment snap mode "creates new vertices if topological editing is enabled" [X16]); on a road *network* that difference decides whether the two roads are connected. Chapter 9 does not build a network, but the prediction must mention it.
- *Identity.* R4 keeps its identifier; nothing is split.

Prediction written; now the edit is a two-click operation, and the review in 9.4.3 checks that (300, 500) is what got stored.

**Platform notes.**
- *ArcGIS Pro.* "There are no buttons to start or stop an edit session. An edit session automatically starts when you modify existing data or create new data" [X08]. **Create Features** opens a pane of editing templates and construction tools; the **Modify Features** pane "contains the full collection of editing tools that modify features" (move, reshape, split, vertex editing, and more); the **Attributes** pane edits attributes and related records [X08]. **Save** and **Discard** are on the **Edit** tab, **Manage Edits** group [X09]. **Verification item:** the exact tool names inside the Modify Features pane in the installed release (Move, Reshape, Split, Edit Vertices) were not re-read for this chapter; confirm before issuing the lab.
- *QGIS 3.44.* **Toggle Editing** on the layer; **Add Point/Line/Polygon Feature**; the **Vertex Tool** for moving individual vertices; **Move Feature(s)**, **Reshape Features**, **Split Features** on the Advanced Digitizing toolbar; **Save Layer Edits** commits, **Rollback** discards [X16].
- *Transferable.* PostGIS has no "edit session": an `UPDATE` that sets `geom = ST_SetPoint(...)` is committed when the transaction commits. The "work on a copy" rule becomes "work in a transaction or on a copied table, and keep the original".

**Misconception.** *"Moving a point is harmless — it's one click."* Consequence: the point was the asset that three inspections and one request refer to; the *rows* still link (the key did not change) but the derived "which ward" answer and every distance computed earlier are now stale, and nobody knows because the edit was not logged. Remedy: predict, log, recompute derived values, review.

**Comprehension check 9.4.1.** You are asked to split Main Road R1 at (500, 500) where Station Road R2 joins it, so that each half can be maintained separately. Write the two prediction sentences (attributes; relationships/identity) and name one question you must ask the schema owner before splitting.

### 9.4.2 Snapping and tolerance: a deliberate 3 m gap

**Snapping** is "a configurable drawing aid that controls the accuracy of the pointer when you hover near a vertex, an endpoint, or other geometric element" [X06]. When you click within a **snapping tolerance** of an existing vertex or edge, the software stores the *existing* coordinate instead of where you clicked. It is how you make two roads meet at *exactly* the same point rather than 3 m apart.

**Snap agents / modes.** ArcGIS Pro lists agents such as **Point** ("Snap to the nearest point or LAS point feature"), **Endpoint**, **Vertex** ("Snap to the nearest vertex of a polyline or polygon feature"), **Edge** ("Snap to the nearest edge of a polyline or polygon segment"), **Intersection**, **Midpoint**, and **Tangent** [X07]. QGIS lists **Vertex**, **Segment**, **Area**, **Centroid**, **Middle of Segments**, **Line Endpoints**, plus **snapping on intersection** [X16]. Turn on only the agents you need for the edit in hand.

**Tolerance and its units.** ArcGIS Pro's XY snapping tolerance is "the 2D XY planar distance between the pointer and a geometric element within which an enabled snap agent snaps the pointer"; the default is **10 pixels**, and it can be set in pixels or map units [X06] [X07]. QGIS's tolerance is likewise set in map units or pixels, with "10 to 12 pixels is normally a good value, but it depends on the DPI of your screen" [X16]; the introductory text adds the warning that matters: "If you specify a value that is too big, the GIS may snap to a wrong vertex, especially if you are dealing with a large number of vertices close together" [S25]. **A pixel tolerance is a different ground distance at every zoom.** At a view where 1 pixel is 1 m, 10 pixels is 10 m; zoom in until 1 pixel is 0.1 m and the same setting is 1 m.

**Worked example 9.4-B — closing the R4 gap (synthetic).** R4's endpoint is at (300, 497); R1 passes through (300, 500). Gap: 3 m.

1. With the **Edge** (ArcGIS) / **Segment** (QGIS) agent on, tolerance 10 pixels, and the view scaled so that 1 pixel ≈ 1 m (tolerance ≈ 10 m ≥ 3 m), drag R4's endpoint toward R1: the pointer snaps to R1's edge, and the stored endpoint becomes (300, 500) — the point on R1 nearest the pointer.
2. Zoomed in so that 1 pixel ≈ 0.1 m (tolerance ≈ 1 m < 3 m), the same drag would *not* snap unless you carry the pointer the whole 3 m; you would see the "snap tip" only when close. Either zoom out, set the tolerance in map units, or type the coordinate.
3. *Typing beats dragging when the coordinate is known.* Since the register gives the junction, entering (300, 500) directly (QGIS Vertex Editor panel [X16]; ArcGIS Pro's vertex editing with typed coordinates — **verification item**: the exact route in the installed release) is more reproducible than any drag.

**Where snapping does harm.** The same mechanism that closes a wanted gap can close an unwanted one:

- *Moving valid observations.* Request P9 at (1000, 500) and drain DR-0042 at (995, 510) are √(5² + 10²) = √125 ≈ 11.2 m apart (hand-checked). With the **Point** agent on and a 15-pixel tolerance at 1 m per pixel, digitising a request near (1000, 500) would snap it onto the drain: a citizen's reported position silently replaced by a surveyed asset position. Two *different facts* (Chapter 8, 8.2.2) now share one coordinate, and 9.6.2's "same location" test is poisoned by the editing tool. Turn Point/Vertex snapping **off** when capturing independent observations.
- *Connecting unrelated objects.* With **Edge** snapping on, a bypass line digitised near Main Road snaps onto it and now "touches" a road it flies over on a bridge (9.5.3). Two features that should be independent are now coincident, and a later connectivity rule will treat them as a junction.
- *Exact boundary coordinates as a symptom.* A request stored at *exactly* (1000, 500) — a coordinate that lies precisely on the ward boundary — is a hint that Edge snapping to the ward layer may have been active when it was captured. Check the capture log before trusting a boundary-exact coordinate as an observation.

**Platform notes.** ArcGIS Pro: snapping on/off from the **Edit** tab, **Snapping** group, or the status bar; **hold the Spacebar to temporarily turn off snapping** while creating, modifying, or measuring [X07]; settings are in **Editor Settings ▸ Snapping** [X06]. QGIS: **Project ▸ Snapping Options…** and **Settings ▸ Options… ▸ Digitizing** [X16]. QGIS's **Topological editing** option makes a shared boundary move in *both* neighbours when you move one ("QGIS will also move them in the geometries of the neighboring features" [X16]); **Avoid Overlap on Active Layer** "cut[s] the overlapping part(s)" of a newly digitised polygon so it fits its neighbours [X16] [S25]. ArcGIS Pro achieves the shared-edge behaviour through a *topology* (map or geodatabase) rather than a checkbox — see 9.5.2.

**Misconception.** *"Snapping is a safety feature, so leave it on."* Consequence: independent observations are pulled onto nearby assets, and a bridge becomes a junction. Remedy: choose agents and tolerance *per edit*, in map units when the ground distance matters, and record the settings in the log.

**Comprehension check 9.4.2.** Streetlights SL-0113 (205, 195) and SL-0114 (260, 190) are 55.2 m apart (hand-checked: √(55² + 5²) = √3050 ≈ 55.2). At what zoom (metres per pixel) would a 10-pixel Point-snapping tolerance let a click intended for SL-0114 land on SL-0113, and what does that tell you about setting the tolerance in pixels for point capture?

### 9.4.3 Review after the edit: the save message proves nothing

"Saved successfully" means the storage accepted the write. It does not mean the edit did what you intended. Three reviews, every time:

1. **Visual review.** Zoom to the edited feature at a scale where the change is visible. For R4: the endpoint now sits *on* R1's line, with no visible gap and no overshoot beyond it. For a moved point: it is where the evidence says, not where the last snap put it.
2. **Attribute review.** Open the row. For R4: the stored endpoint coordinate is (300, 500) — read it from the vertex list or the geometry, do not infer it from the picture; the length is 200 m if stored. For a split: two rows, identifiers as intended, `width_m` on both, no related record pointing at a now-missing key.
3. **Invariant review.** Re-run the check that motivated the edit. If the edit closed a gap, re-run the gap check; if it fixed a domain violation, re-run the domain query (9.6). If the edit was meant to leave a *count* unchanged (moving a point), confirm the count.

Then write the before/after record (9.7.1): feature, field or geometry, value before, value after, evidence, who, when.

**Comprehension check 9.4.3.** After snapping R4 to R1 you notice R1 now has three vertices instead of two. Was that a mistake? What must the log say about it?


## 9.5 Separate geometry validity from topology rules

### 9.5.1 An invalid polygon versus two valid polygons that break a rule

Two things that both get called "geometry errors" are different in kind, and the difference decides who may fix them and how.

**Geometry validity** is a property of *one* shape on its own: can the shape be interpreted unambiguously as an area (or a line)? The rules come from the OGC Simple Features standard, which PostGIS restates: a polygon is valid if "the polygon boundary rings (the exterior shell ring and interior hole rings) are simple (do not cross or self-touch)", "boundary rings do not cross", "boundary rings may touch at points but only as a tangent (i.e. not in a line)", "interior rings are contained in the exterior ring", and "the polygon interior is simply connected" [X20]. A multipolygon is valid if its parts are valid, "do not overlap", and "touch only at points" [X20]. For lines the only rule is at least two distinct points — "non-simple (self-intersecting) lines are valid" [X20]. Points have no validity rules [X20].

**A topology rule** is a statement about how *several* shapes relate: wards do not overlap, wards leave no gaps, roads connect where they should, assets lie inside a service area. In ArcGIS vocabulary, "Topology is the arrangement of how point, line, and polygon features share geometry" [X23], and "Geodatabase topology rules allow you to define relationships between features in the same feature class or subtype or between two feature classes or subtypes" [X04]. Every shape involved can be perfectly valid; the *set* is wrong.

**Worked example 9.5-A — Ward C, invalid (synthetic).** In the damaged package Ward C's ring is (0, 1000) → (1000, 1000) → (0, 1500) → (1000, 1500) → back to (0, 1000). Walk it on graph paper: the second segment goes from the bottom-right corner to the top-*left*, and the fourth from the top-right back to the bottom-left. Those two segments cross. Where? The second segment is x = 1000 − 1000t, y = 1000 + 500t; the fourth is x = 1000 − 1000s, y = 1500 − 500s. Equal x gives t = s; equal y gives 1000 + 500t = 1500 − 500t, so t = 0.5 — the crossing is at **(500, 1250)** (hand-checked). The ring is *not simple*; the polygon is **invalid**. Its "area" is not even well defined: the shoelace formula on the vertices as written gives (0·1000 − 1000·1000) + (1000·1500 − 0·1000) + (0·1500 − 1000·1500) + (1000·1000 − 0·1500) = −1,000,000 + 1,500,000 − 1,500,000 + 1,000,000 = **0**, because the two lobes of the bow-tie have opposite orientation and cancel. Software will report 0 m², or a negative number, or the area of one lobe, depending on the engine — all of them meaningless. The *intended* shape (readme: the rectangle from (0, 1000) to (1000, 1500)) has area 500,000 m²; the bow-tie's two visible triangles cover 125,000 m² each — 250,000 m² in total (hand-checked: each triangle has base 1,000 m and height 250 m). Hold on to those three numbers — 0, 250,000, 500,000 — for 9.7.2.

**Worked example 9.5-B — two valid wards that violate a rule (synthetic).** Suppose Ward B had been digitised as (990, 0), (2000, 0), (2000, 1000), (990, 1000). It is a perfectly valid rectangle. Ward A is a valid square. But they **overlap** in the strip 990 ≤ x ≤ 1000 — 10 m × 1,000 m = 10,000 m² that is "in" both wards. Every shape passes the validity check; the *pair* fails the business rule "wards must not overlap". Conversely the damaged package's Ward B — (1004, 0), (2000, 0), (2000, 1000), (1000, 1000) — is a valid quadrilateral of area 998,000 m² (hand-checked by shoelace: ½ × 1,996,000) that leaves a **sliver gap** of 2,000 m² (a triangle with base 4 m and height 1,000 m) between itself and Ward A; the QGIS introduction calls these "slivers" — they "occur when the vertices of two polygons do not match up on their borders" [S25]. Valid shapes, broken rule.

| | Invalid geometry (Ward C) | Topology-rule violation (Ward A/B gap or overlap) |
| --- | --- | --- |
| How many features are involved | One | Two or more |
| Who defines the rule | The geometry standard (OGC/SQL-MM) or the engine [X20] [X01] | The organisation's business rule, expressed as a topology rule [X04] [S25] |
| Can the feature be used in analysis as is? | No — area, containment, overlay are undefined or engine-dependent | Yes, each feature individually; the *set* gives double-counted or missing area |
| Detection | Check Geometry [X01] / Check validity [X22] / `ST_IsValid` [X20] | Topology rules [X04] [X05] / Topology Checker [X19] / spatial predicates in SQL |
| Typical cause | Vertex order, an unclosed ring, a stray click that crossed a line | Two features digitised separately without snapping or topological editing |
| Automated fix exists? | Yes — and it may change the shape (9.7.2) | Sometimes ("Remove Overlap", "Merge", "Create Feature" [X04]) — and it always chooses *which* feature yields, which is a business decision |

**Platform note — three validity vocabularies for one idea.** ArcGIS Pro's **Check Geometry** reports problems including "Self intersections" ("A polygon must not intersect itself"), "Unclosed rings" ("The last segment in a ring must have its to point incident on the from point of the first segment"), "Incorrect ring ordering", "Null geometry", "Duplicate vertex", "Short segment", and "Empty parts"; it offers two **Validation Method** options — **Esri** ("ensures that geometry is topologically correct using the Esri Simplify method"; the only option for enterprise geodatabases) and **OGC** ("ensures that geometry complies with the Open Geospatial Consortium (OGC) specification"); "Geometry that is validated or repaired using the OGC option will be valid for the Esri option" [X01]. Note the ring-ordering item: Esri's convention is outer rings clockwise, inner rings counter-clockwise [X01] — the *opposite* of GeoJSON's (Chapter 3, 3.3.3) — so a ring that is "incorrectly ordered" for the Esri method can be a perfectly ordered GeoJSON ring; this is an engine rule, not a geometry defect. QGIS's **Check validity** algorithm (`native:checkvalidity`) offers **QGIS** and **GEOS** methods (default GEOS), outputs valid/invalid/error layers with an `_errors` field, and documents that the two methods word the same failure differently — GEOS "Ring self-intersection" versus QGIS "Ring self-intersection", GEOS "Self-intersection" where QGIS reports the segment pair [X22]. PostGIS's `ST_IsValid` and `ST_IsValidDetail` follow the OGC definition and give a reason and a location: the manual's example returns `Self-intersection` at a point [X20]. The *concept* is one; the *report* is three, and a defect log must say which engine and method produced it.

**Misconception.** *"The layer draws fine, so the geometry is fine."* Consequence: the bow-tie Ward C draws as two triangles and looks like a ward; its area is 0 in one tool and 250,000 m² in another; a "requests per ward" join silently drops or double-counts. Remedy: run a validity check on every polygon layer at intake, and treat "draws fine" as no evidence at all.

**Comprehension check 9.5.1.** The depot DP-01 (Chapter 3) has a hole (1450–1550, 650–750). A colleague "simplifies" it to a single ring that runs around the outside, cuts in along y = 700 to the hole, around the hole, and back out along the same cut. Is the result valid under the rules quoted from [X20]? Which rule decides?

### 9.5.2 Domain-specific rules: wards, roads, assets

Which rules apply is a *business* decision; the software only offers a vocabulary. Three families cover the municipal scenario. The rule names below are ArcGIS geodatabase topology rules [X04] [X05]; the QGIS Topology Checker plugin offers a similar list — "Must not have gaps", "Must not overlap", "Must not have invalid geometries", "Must not have dangles", "Must not have pseudos", "Must not have duplicates", "Must be covered by", "Must be inside" [X19]; and any of them can be written as a spatial query in PostGIS.

| Domain | Rule (ArcGIS name) | What it requires [X04] [X05] | Municipal meaning | Fixture case |
| --- | --- | --- | --- | --- |
| **Wards** (nonoverlapping, exhaustive) | *Must Not Overlap* | "polygons must not overlap within a feature class or subtype" | Every point of the territory belongs to at most one ward | Worked example 9.5-B's 10 m strip |
| | *Must Not Have Gaps* | "polygons must not have a void between them within a feature class or subtype" | Every point of the territory belongs to at least one ward | The 2,000 m² sliver between A and the damaged B |
| **Roads** (connected where intended) | *Must Not Have Dangles* | "The end of a line must touch any part of one other line or any part of itself" — with the page's own exception: "you can set exceptions to this rule for road segments that end at cul-de-sacs or terminate with dead-ends" | Lanes that are supposed to join the network do join it | R4's 3 m undershoot (defect); R5's dead end at the depot gate (exception — 9.5.3) |
| | *Must Not Self-Intersect* / *Must Not Intersect* | "Lines must not cross or overlap themselves" / "must not cross or overlap any part of another line" | No accidental crossings inside a carriageway layer | A bypass flyover crossing R1 is a legitimate exception (9.5.3) |
| **Assets** (inside a service area) | *Must Be Properly Inside* (point in polygon) or the polygon rule *Contains Point* ("Each polygon … must contain within its boundaries at least one point of the second feature class") | Assets lie within the area the municipality maintains; each ward has at least one asset | TR-0301 at (2190, 520) lies outside every ward — defect or exception? (9.8) |

**Which of these are "rules" and which are "questions".** "Wards must not overlap" is a hard rule: an overlap is always wrong. "Assets must lie inside a ward" is really a *question*: an asset outside every ward means either the asset is misplaced, or the ward layer is incomplete, or the municipality maintains something beyond its wards. The defect log (9.6.3) must be able to say "flagged; needs owner" rather than "error".

**Platform notes.**
- *ArcGIS Pro — geodatabase topology.* "A topology is built on a set of feature classes that are held within a common feature dataset" [X03] (Chapter 7 introduced feature datasets). Validation uses a **cluster tolerance**: "All vertices that are within the cluster tolerance may move slightly in the validation process"; "The default cluster tolerance is 0.001 meters in real-world units. It is 10 times the distance of the x,y resolution" [X03]; features with higher **rank** "will move less and exert more gravitational pull on lower-ranked coordinates" [X03]. Rule violations are stored as **errors**, and "Certain errors may be acceptable, in which case the error features can be marked as exceptions" [X03]. **Licence:** "Available with Standard or Advanced license" [X03] — a Basic licence cannot create or edit a geodatabase topology, which is why the lab's primary route uses Check Geometry plus manual review and treats geodatabase topology as an optional extension. Fixes offered per rule include *Remove Overlap*, *Merge*, and *Create Feature* [X04]; each of them decides which feature changes.
- *QGIS 3.44 — Topology Checker.* A core plugin (enable it under **Plugins ▸ Manage and Install Plugins**; it then appears under **Vector**); configure rules per layer; **Validate All** or **Validate Extent**; "Errors will show up in the table of results containing type of error, layer and feature ID", with the option to show them on the canvas [X19]. It *reports*; you fix with the editing tools.
- *PostGIS.* A gap or overlap is a query: `SELECT a.ward, b.ward FROM wards a JOIN wards b ON a.ward < b.ward AND ST_Overlaps(a.geom, b.geom)` finds overlaps; a dangle check needs the endpoints of each line tested against the other lines. Chapter 10 teaches the predicates; the point here is that the *rule* is expressed by the organisation, in whichever tool.

**Misconception.** *"Turn on all the rules and fix everything the tool reports."* Consequence: the depot access road is "fixed" by extending it into the depot, the flyover is "fixed" by splitting the main road under it, and a genuinely overlapping pair of contractor territories is "fixed" by deleting the overlap. Remedy: choose rules from the business meaning; expect exceptions; read 9.5.3 before pressing any fix.

**Comprehension check 9.5.2.** For the `Requests` layer, would you apply *Must Be Properly Inside* (wards)? What would P6 at (2200, 500) and P7 (no geometry) do to the result, and what does that tell you about the rule's usefulness for citizen reports?

### 9.5.3 Exceptions: a cul-de-sac, an intentional overlap, a bridge

A topology error is a *report*, not a verdict. Three fixture cases show why.

**The cul-de-sac.** R5 "Depot Access" runs from R1 at (1500, 500) to the depot gate at (1500, 600) and stops — the road register says "no through route". Under *Must Not Have Dangles* its north end is a dangle. It is a **valid exception**, and the vendor page says so in as many words: dead-end road segments are the standard exception to the rule [X05]. In a geodatabase topology you *mark the error as an exception* [X03]; in QGIS or a spreadsheet log you record "R5 north end: dangle; expected; register says dead end". What you do **not** do is extend R5 until it touches something. Compare R4: its dangle is 3 m from a road it is documented to join. Same rule, same symptom, opposite decisions — and the difference is *evidence* (the register), not the geometry.

**The intentional overlap.** Wards must not overlap. But a *contractor service territory* layer, where two contractors both cover the central market during a transition month, overlaps on purpose. If someone applies the ward rule to the territory layer, every overlap is an "error". The rule belongs to the ward layer's meaning, not to polygons in general. When you design the rule set (9.5.2), write next to each rule *why* it holds for *this* layer.

**The bridge.** A bypass flyover crosses Main Road R1 at (1800, 500) without a junction: vehicles on the flyover cannot turn onto R1. In two dimensions the lines *intersect*; under *Must Not Intersect* that crossing is an error. In reality they are at different heights and the crossing is not a connection. A road layer that must support connectivity needs either a `level` attribute (flyover = 1, ground = 0) or Z values (Chapter 3, 3.2.3), and the rule must be *Must Not Intersect* applied only to features at the same level — or the crossing is simply marked as an exception. The optional 3D scene in the Media Appendix (M.5) exists for exactly this point: **apparent crossing in plan view does not establish connectivity**, and neither does apparent separation establish its absence.

**The general test.** For each reported error ask: *is there evidence that the real world is as the geometry says?* If yes (register says dead end; contract says overlap; the flyover is a bridge), the report is an exception and the evidence goes in the log. If no evidence either way, the report stays *open* (9.7.3). Only if the evidence says the geometry is wrong do you correct it.

**Misconception.** *"An exception is a defect we chose to ignore."* Consequence: exceptions are not documented, so the next reviewer "fixes" them. Remedy: an exception is a *documented* decision with evidence, stored with the data (marked in the topology, or in the log that travels with the package).

**Comprehension check 9.5.3.** Station Road R2 touches R1 at (500, 500) — a T-junction — and the bus loop L1 is a closed line touching nothing. Under *Must Not Have Dangles* and *Must Not Have Pseudo Nodes* ("The end of a line cannot touch the end of only one other line" [X05]), what does each feature report, and which reports are exceptions?

## 9.6 Diagnose attribute and relationship problems

### 9.6.1 Five checks you can write as queries

Attribute and relationship defects hide in plain sight because every row *looks* normal. They are found by asking the table questions it cannot dodge. Each check below is written in ordinary SQL against the Chapter 8 schema; the same logic runs as a spreadsheet filter, an ArcGIS **Select By Attributes** expression, or a QGIS expression. Run all five at intake, before any editing, and again after.

**Table 9.6a — the five attribute/relationship checks.**

| # | Check | Query (SQL) | What a hit means | Fixture hits (9.8) |
| --- | --- | --- | --- | --- |
| 1 | **Duplicate business identifiers** | `SELECT asset_id, COUNT(*) FROM asset GROUP BY asset_id HAVING COUNT(*) > 1` | The identity policy (Chapter 8, 8.4.3) was violated: either one thing was entered twice, or two things share a number. The query cannot tell which | `SL-0114` appears twice |
| 2 | **Invalid categories** | `SELECT * FROM asset WHERE asset_type NOT IN ('SL','DR','TR')` — and the same for every coded field | A value outside the domain: a typo, an unmapped legacy code, or a genuinely new category the schema lacks | `asset_type` = `Lamp` |
| 3 | **Missing required observations** | `SELECT * FROM inspection WHERE condition_code IS NULL` | A visit happened but the observation was not recorded — *not* the same as "condition is fine" (Chapter 8, 8.6.2) | INS-0005 |
| 4 | **Unit / range inconsistencies** | `SELECT * FROM asset WHERE pole_height_m < 3 OR pole_height_m > 15` | A value outside the range domain; most often a unit slipped in (feet stored as metres) or a decimal point moved | `pole_height_m` = 22 |
| 5 | **Orphaned related records** (Chapter 8, 8.7.2) | `SELECT i.* FROM inspection i LEFT JOIN asset a ON a.asset_id = i.asset_id WHERE a.asset_id IS NULL` | A child row whose parent does not exist: a typo in the key, a deleted parent, or a parent that was never loaded | INS-0006 → `DR-0044` |

**Worked example 9.6-A — reading check 4's hit (synthetic).** `pole_height_m` = 22 for SL-0127. The range domain says 3–15 m, so 22 is impossible for a streetlight pole in this register. Is it a typo for 2.2? For 12? Or 22 *feet*? The Chapter 8 conversion log (readme) recorded the source value as `22 ft` = 6.7056 m. That is *evidence*: the correct value is 6.7056 m, which the Chapter 8 lab rounded to **6.71 m** with the rounding stated. Without that log entry the right action would be to null the value with the reason "out of range; unit unknown" and ask the owner — not to divide by 3.2808 on a hunch.

**Worked example 9.6-B — reading check 5's hit (synthetic).** INS-0006 refers to `DR-0044`; no such asset exists. DR-0043 exists, was visited by the same team 50 minutes later the same morning, and got condition 3 while INS-0006 says 4. The temptation is obvious: "DR-0044 is a typo for DR-0043." Resist it. If INS-0006 *were* DR-0043, the team would have scored the same drain 4 and then 3 within an hour — a contradiction that needs explaining, not a confirmation. The only evidence that can settle it is the paper form. Until then INS-0006 stays an orphan, *retained*, listed in the unresolved-issues list, and excluded from any "current condition" calculation with a note saying so. Deleting it would destroy the observation; re-keying it would fabricate one.

**Platform note — duplicate detection by geometry.** ArcGIS Pro's **Find Identical** tool "Reports any records in a feature class or table that have identical values in a list of fields"; when the Shape field is included, geometries are compared within an **XY Tolerance**, and the output's `FEAT_SEQ` gives identical records the same sequence number [X12]. It is a good way to find *exactly* coincident features; it says nothing about whether two coincident rows are one fact or two (9.6.2). QGIS has equivalent processing algorithms (for example *Delete duplicate geometries*), which — like any deletion — must run on the working copy only after 9.6.2's reasoning.

**Developer analogy — and where it stops.** These are the constraint checks a database would enforce — `UNIQUE`, `CHECK (x IN (...))`, `NOT NULL`, `CHECK (x BETWEEN ...)`, `FOREIGN KEY` (Chapter 8, 8.6.3) — run *after the fact* on data that arrived without them. The analogy holds. It stops at *interpretation*: a database rejects the row; a data reviewer must decide whether the row, the rule, or the reference is wrong. "Lamp" fails the domain, but the domain may be what is incomplete.

**Comprehension check 9.6.1.** Write the check-2 query for the request `status` field against Chapter 3's values (`Open`, `In progress`, `Resolved`, `Reopened`, `Closed - duplicate`), and say what you would do if it returned a row with `status` = `Closed` (no qualifier).

### 9.6.2 Two reports at one place, or one report twice? Proximity is not evidence

Service requests are *incidents* (Chapter 8, 8.1.2): five people reporting one broken light are five legitimate rows. So "two requests at the same coordinates" is *not* a duplicate by itself. Deduplication needs evidence about the *reports*, not about their distance.

**Worked example 9.6-C — P3, P8, P5, P9 (synthetic; from the Chapter 9 package).**

| Pair | Coordinates | Category | Reporter (`reporter_ref`) | Date and time (IST) | Channel | Note text | Decision |
| --- | --- | --- | --- | --- | --- | --- | --- |
| **P3 / P8** | Both (1200, 250) | Both "Water leak" | Both R-3391 | 2026-09-10 09:13 and 09:14 | Both Mobile app | Identical: "Water on road near shop" | **One report, submitted twice** (a double tap, 60 seconds apart). Evidence: same reporter, same text, same minute, same channel. Action: mark P8 as a duplicate *of P3* — status `Closed - duplicate`, note "Duplicate of P3" — and **keep the row** (Chapter 3's P6 shows the convention). Never delete it: the double submission is itself a fact about the app |
| **P5 / P9** | Both (1000, 500) | "Blocked drain" / "Streetlight out" | R-2790 / R-0077 | 2026-08-30 07:55 / 2026-09-13 19:45 | Phone / Mobile app | Different | **Two reports.** Same place, different problems, different people, two weeks apart. Coincident coordinates here are a *capture* artefact worth noting (both exactly on the ward boundary — 9.4.2) but not a reason to merge. Action: none, beyond logging that the coincidence was examined |

The evidence hierarchy for "same report?" is: same reporter identity → same problem description → same time window → same channel → *then* location. Location comes last because every method in 9.2 can place two different reports at one coordinate (a geocoder returns the same street point for every report of "near the temple"; snapping pulls nearby clicks together) and can place one report at two coordinates (a phone's position drifts between the first and second submission).

**Misconception.** *"Requests within 5 m of each other are duplicates — merge them."* Consequence: P5 (drain) and P9 (light) are merged; the light is never fixed; the reporter of P9 is told their request was "already logged". Remedy: dedupe on report evidence; treat proximity as a prompt to *look*, never as the decision.

**Comprehension check 9.6.2.** Two requests: same reporter, same category "Pothole", same coordinates, submitted 11 days apart, the second with the note "still not fixed". Duplicate or not? What single field in Chapter 8's request design would express the right relationship between them?

### 9.6.3 The defect log

Every finding — geometric, attribute, relational, and every false alarm you examined — goes into one log with six mandatory columns. The log is the deliverable that lets a second person check your work and lets the source owner answer your questions; it is also what Chapter 11's "validate and document" and Chapter 12's "honest uncertainty" will build on.

**Table 9.6b — defect log template.**

| Column | Content | Example (SL-0127 height) |
| --- | --- | --- |
| **ID** | A sequential defect number | D-06 |
| **Symptom** | What the check reported, naming the check and the engine/query | `pole_height_m` = 22 fails range 3–15 (check 4, SQL on `asset`) |
| **Affected records** | Business identifiers, never row numbers | SL-0127 |
| **Likely cause** | Your hypothesis, labelled as such | Feet entered as metres at the Chapter 8 conversion (readme: source value `22 ft`) |
| **Evidence** | What supports the cause and the correction | Chapter 8 conversion log entry; 22 × 0.3048 = 6.7056 |
| **Proposed correction** | Exactly what will change, before → after, or "none — exception", or "none — needs owner" | `pole_height_m` 22 → 6.71 (rounded to 0.01 m; 6.7056 exact) |
| **Reviewer decision** | Accepted / rejected / deferred, by whom, when | Accepted, instructor, 2026-09-19 |

Two habits make the log useful. First, **false alarms get rows too** — "R5 north end reported as dangle; register says dead end; no correction; exception" — because the next reviewer will otherwise re-investigate them. Second, **the proposed correction is written before it is made**, so that the before/after record in 9.7.1 can be checked against the intention.

**Comprehension check 9.6.3.** Write the log row for INS-0006 (the orphan) with all seven columns, leaving the reviewer decision as "deferred".

## 9.7 Apply corrections with traceability

### 9.7.1 Preserve the original; record before and after; fix the cause, not the export

Three rules, in order of importance.

**Rule 1 — the original survives, untouched.** Item 5 of the intake checklist (9.1.3). Every correction is made on the working copy; the original is read-only and named so that nobody mistakes it for the corrected version (`Chapter09_Damaged/` stays; `Chapter09_Work/` is edited; `Chapter09_Corrected_v1/` is what you release). Chapter 7 (7.7.3) gave the same rule for conversions; it is stronger here because an edit *cannot* be reversed from the data itself once saved [X09].

**Rule 2 — every change has a before/after record.** For an attribute: feature ID, field, old value, new value, evidence, editor, timestamp. For geometry: feature ID, what moved (vertex index or "north endpoint"), old coordinate, new coordinate, evidence, editor, timestamp — and, if the shape was redrawn, a copy of the old geometry (WKT is fine) in the log. Editor tracking (Chapter 8) records *who* and *when* but not *what* [Chapter 8, X12/X25]; the log carries the *what* and the *why*.

**Table 9.7a — before/after record for the R4 correction (synthetic).**

| Feature | Element | Before | After | Evidence | Editor / when |
| --- | --- | --- | --- | --- | --- |
| R4 | North endpoint (vertex 2 of 2) | (300, 497) | (300, 500) | Road register: "Temple Lane, temple to junction with Main Road"; R1 passes through (300, 500); gap 3 m | learner / 2026-09-19 |
| R4 | `length_m` (if stored) | 197 | 200 | Recomputed from the geometry | same |

**Rule 3 — find the source cause; do not keep correcting exports.** If SL-0127's height is wrong in the working copy because it is wrong in the asset register that feeds it, correcting the copy fixes this release and the next export will be wrong again. The log's *likely cause* column exists so that the correction can be pushed upstream: to the register, to the form design (a unit field), to the conversion script (a units check), to the domain (add `GEOCODED`). Downstream corrections are acceptable as a stopgap; they must be flagged as such ("corrected in copy; source not yet corrected; owner notified") so that they are not silently repeated forever.

**Developer analogy — and where it stops.** Rules 1 and 2 are version control: the original is the previous commit, the before/after record is the diff, the evidence is the commit message. Rule 3 is "fix the bug, not the symptom". The analogy stops at *the diff*: geometry engines do not produce readable diffs of shapes, so you must write the old coordinates down yourself; and a data "commit" may later turn out to have been wrong in the light of evidence you did not have, so the log must keep the evidence, not just the change.

**Comprehension check 9.7.1.** You corrected Ward B's stray vertex from (1004, 0) to (1000, 0). What are the three things the log must contain for that edit beyond the two coordinates, and which of them would the software's editor tracking have captured on its own?

### 9.7.2 Automated repair changes shapes; you still have to look

Every platform has a "make it valid" operation. They are legitimate and useful — and they *change the geometry*, by design, in ways you must inspect.

| Platform | Operation | What it does | What it may change |
| --- | --- | --- | --- |
| ArcGIS Pro | **Repair Geometry** (Data Management) — "This tool modifies the input data" [X02] | Null geometry: "The record will be deleted from the feature class" (default; can be unchecked); short segments deleted; "The areas of overlap in a polygon will be dissolved" for self-intersections; unclosed rings "closed by connecting the end points of the rings"; empty parts and duplicate vertices deleted; ring ordering and segment orientation corrected; envelopes updated [X02] | Rows can disappear (null geometry); shapes can lose parts, gain segments, or have overlapping lobes dissolved; a Basic licence cannot run it on enterprise geodatabase inputs [X02] |
| QGIS 3.44 | **Fix geometries** (`native:fixgeometries`) — "Attempts to create a valid representation of a given invalid geometry without losing any of the input vertices. Already valid geometries are returned without further intervention. Always outputs multi-geometry layer" [X22] | Two methods: **Linework** ("combines all rings into a set of noded lines and then extracts valid polygons from that linework") and **Structure** ("first makes all rings valid and then merges shells and subtracts holes from shells… Assumes that holes and shells are correctly categorized"; needs GEOS ≥ 3.10) [X22] | Output geometry type becomes *multi* for the whole layer; a self-intersecting polygon becomes a multipolygon of its lobes; M values are dropped [X22] |
| PostGIS | `ST_MakeValid(geom)` — "attempts to create a valid representation of a given invalid geometry without losing any of the input vertices" [X21] | Same two methods, `linework` (default) and `structure`; `keepcollapsed` controls whether degenerate parts are kept [X21] | A polygon can come back as a MultiPolygon or a GeometryCollection; dimensionality can drop [X21] |

**Worked example 9.7-A — what "repairing" Ward C actually produces (synthetic; reasoning from the documented method, not an executed result).** Ward C's bow-tie (9.5.1) has two lobes: the triangle (0, 1000)–(1000, 1000)–(500, 1250) and the triangle (0, 1500)–(1000, 1500)–(500, 1250). A linework-style repair — QGIS's Linework method, PostGIS's default — nodes the ring at the crossing (500, 1250) and extracts the valid polygons the noded lines enclose: the expected result is a **multipolygon of two triangles, total area 250,000 m²**, sharing the single point (500, 1250) — which is permitted for a multipolygon ("elements touch only at points" [X20]). Every input vertex is kept, as the documentation promises; the shape is *valid*; and it is **the wrong ward**. The register says Ward C is the 500,000 m² rectangle. The repair did exactly what it says; it could not know that the vertex order, not the vertices, was the mistake.

The Esri method's description for self-intersections — "The areas of overlap in a polygon will be dissolved" [X02] — is written for overlapping lobes; how it treats a bow-tie whose lobes do not overlap is **not stated on the page read**, and this chapter does not assert it. **Verification item for the instructor:** run Repair Geometry (both validation methods) on a *copy* of the damaged `wards_d` layer, record the resulting Ward C geometry type, part count, and area in I.8, and issue the observed behaviour with the lab. Until then, the lab treats the automated repair of Ward C as an *experiment on a copy* whose result is recorded, not as the correction.

**The correct correction** for Ward C is a *manual* one: re-order the vertices to (0, 1000), (1000, 1000), (1000, 1500), (0, 1500), closing at (0, 1000), because the register's rectangle is the evidence. Area 500,000 m² (hand-checked by shoelace: (0·1000 − 1000·1000) + (1000·1500 − 1000·1000) + (1000·1500 − 0·1500) + (0·1000 − 0·1500) = −1,000,000 + 500,000 + 1,500,000 + 0 = 1,000,000; half is 500,000). Log the old ring in WKT, the new ring, the evidence, and the fact that the automated repair would have given a different answer.

**When automated repair *is* right.** A duplicate consecutive vertex, an unclosed ring whose end points are a centimetre apart, a bad envelope — cases where the repaired shape is what everyone would draw by hand. Even then: run it on the copy, compare counts and areas before and after (Chapter 7's before/after snapshot), and log which tool, which method, and what changed.

**Misconception.** *"Repair Geometry reported success, so the layer is fixed."* Consequence: Ward C becomes two triangles; "requests per ward" halves Ward C's expected area; the dashboard is wrong and valid. Remedy: after any automated repair, list the features it touched (both tools report them [X01] [X22]) and inspect each against the evidence.

**Comprehension check 9.7.2.** The damaged package contains no null geometries — but Chapter 3's request P7 has one on purpose. If P7 were in a layer run through Repair Geometry with the default settings, what would happen to it [X02], and what does that tell you about the "Delete Features with Null Geometry" default?

### 9.7.3 Keep unresolved issues explicit; never fabricate an observation

A corrected dataset is not one with zero open issues. It is one where every open issue is *listed*, with what would resolve it. Three fixture cases stay open at the end of the lab, and each is a small lesson.

- **INS-0005 has no condition score.** The visit happened (the team was there; "pole leaning" was written); the score was not recorded. The value stays **null**. Filling in `3` "because most visits are 3" or `2` "because it is leaning" would be fabrication: a number in `condition_code` claims that an inspector *judged* the pole. The unresolved list says: "INS-0005: condition not scored; remarks say pole leaning; needs re-inspection or the inspector's recollection; excluded from condition statistics until resolved."
- **INS-0006 refers to DR-0044, which does not exist.** Stays an orphan, retained (9.6.1). Unresolved list: "needs the paper form."
- **Two rows are called SL-0114.** One at (260, 190), digitised, 2024; one at (1250, 420), GNSS, 2025, about 1,016 m apart (hand-checked: √(990² + 230²) = √(980,100 + 52,900) = √1,033,000 ≈ 1,016.4). They cannot be one light. Which one is *the* SL-0114 and what the other should be called is the source owner's decision under the identity policy. Renumbering one yourself would break any external reference (a work order, a photo file name) that uses the number.

**Why this matters beyond neatness.** A dataset that "passes validation" because nulls were filled and orphans re-keyed is *worse* than the damaged one: its defects are now invisible. Chapter 12 will ask you to show uncertainty on a map; you cannot show what the data no longer admits.

**Misconception.** *"Validation must pass before release, so I have to resolve everything."* Consequence: fabricated values. Remedy: release with an unresolved-issues list, and let the consumer decide what the open items mean for their use (9.1.2).

**Comprehension check 9.7.3.** TR-0301 lies outside every ward. Write its unresolved-issues entry: the symptom, the two competing explanations, and the evidence that would decide between them.


## 9.8 Guided lab: inspect a deliberately damaged dataset

### 9.8.1 Objective, prerequisites, and what this lab is (and is not)

**Objective.** Take the damaged Chapter 9 package, complete the intake checklist, run the validity, topology, attribute, and relationship checks, classify every finding as *defect — correct with evidence*, *defect — needs the source owner*, or *false alarm — exception*, apply only the supported corrections on a working copy with a before/after record, and deliver a defect log, a corrected copy, and an unresolved-issues list.

**Prerequisites.** Modules 9.1–9.7; the Chapter 8 schema and domains; Chapter 3's geometry vocabulary; the ability to build or open the package (below).

**Required software/access.** *Primary route:* ArcGIS Pro 3.x, any licence level (Check Geometry and Repair Geometry are available at Basic for file-based inputs [X01] [X02]; geodatabase topology, which needs Standard or Advanced [X03], is an optional step). *Alternative route:* QGIS Desktop 3.44 with the Topology Checker core plugin [X19]. *Paper route:* graph paper and a spreadsheet — every check in this lab can be done by hand, and the expected values were derived that way. No ArcGIS Online account, credits, or downloads are required.

**Execution status.** The software steps were written from the documentation pages cited beside them and are **not execution-tested**. The expected results in 9.8.4 are hand-checked arithmetic on the printed fixture. An instructor must run the lab once (Instructor Appendix I.6) and record observed tool behaviour — especially what Repair Geometry does to Ward C — in I.8 before issuing the lab.

**What this lab is.** A diagnosis-and-decision exercise with a private answer manifest (I.3). Marks are for *reasoning with evidence*, not for the number of tools run.

**What it is not.** It is not a topology-configuration course (a geodatabase topology is optional), not a georeferencing exercise (9.3 is taught by arithmetic), and not an exercise in making every check pass — three issues are *meant* to stay open.

### 9.8.2 Input data — the Chapter 9 damaged package (synthetic; reproduce exactly)

**Coordinate frame.** Planar training grid, metres, (x, y), y upward, **no coordinate reference system**. Not Earth-referenced.

**Files.** Seven UTF-8 CSV files with a header row, geometry as a `wkt` column for lines and polygons and as `x`, `y` columns for points, exactly as in the Chapter 3 Town package. The instructor converts them to `Chapter09_Damaged.gpkg` (or a file geodatabase) by the routes in Chapter 3, Instructor Appendix I.5, and issues the built package together with these texts. The readme in the fixture section above travels with the package. **Every defect below is deliberate**; do not "tidy" the files while typing them.

`wards_d.csv`
```text
ward,name,wkt
A,Ward A,"POLYGON((0 0,1000 0,1000 1000,0 1000,0 0))"
B,Ward B,"POLYGON((1004 0,2000 0,2000 1000,1000 1000,1004 0))"
C,Ward C,"POLYGON((0 1000,1000 1000,0 1500,1000 1500,0 1000))"
```

`roads_d.csv`
```text
road_id,name,width_m,wkt
R1,Main Road,12,"LINESTRING(0 500,2000 500)"
R2,Station Road,7,"LINESTRING(500 0,500 500,800 900)"
L1,Bus turning loop,6,"LINESTRING(1300 300,1350 300,1350 350,1300 350,1300 300)"
R4,Temple Lane,5,"LINESTRING(300 300,300 497)"
R5,Depot Access,6,"LINESTRING(1500 500,1500 600)"
```

`assets_d.csv` (blank `pole_height_m` is blank in the file; drains and trees have no pole)
```text
asset_id,asset_type,x,y,install_year,status,location_method,pole_height_m
SL-0113,SL,205,195,2018,ACTIVE,SURVEY,
DR-0042,DR,995,510,2011,ACTIVE,SURVEY,
TR-0301,TR,2190,520,2005,ACTIVE,APPROX,
TR-0302,TR,1500,700,2019,ACTIVE,SURVEY,
SL-0114,SL,260,190,2024,ACTIVE,DIGITISED,6.5
SL-0127,Lamp,600,480,2020,ACTIVE,DIGITISED,22
DR-0043,DR,1450,250,2016,ACTIVE,SURVEY,
SL-0250,SL,1800,700,2012,REMOVED,SURVEY,8
TR-0303,TR,400,900,2021,ACTIVE,APPROX,
SL-0114,SL,1250,420,2025,ACTIVE,GNSS,7
```

`teams.csv` (non-spatial)
```text
team_id,team_name,active
T-N,North crew,true
T-S,South crew,true
```

`inspections_d.csv` (non-spatial; `visited_at_utc` is the event time in UTC as designed in Chapter 8; IST = UTC + 05:30)
```text
inspection_id,asset_id,visited_at_utc,recorded_on,team_id,condition_code,defects_found,remarks
INS-0001,DR-0042,2024-10-15T04:20Z,2024-10-15,T-S,3,0,
INS-0002,DR-0042,2025-11-02T09:35Z,2025-11-02,T-S,2,2,Grating cracked; silt
INS-0003,SL-0113,2026-03-14T05:12Z,2026-03-14,T-N,3,1,Lamp flickers at dusk
INS-0004,SL-0113,2025-09-14T05:45Z,2026-03-16,T-N,3,0,
INS-0005,SL-0250,2026-08-01T04:30Z,2026-08-01,T-S,,0,Pole leaning; condition not scored
INS-0006,DR-0044,2026-09-05T03:50Z,2026-09-05,T-S,4,0,
INS-0007,DR-0043,2026-09-05T04:40Z,2026-09-05,T-S,3,1,Cover chipped
```

`requests_d.csv` (P7 has blank coordinates on purpose; `Closed - duplicate` uses a plain hyphen; times are IST as exported by the request system)
```text
request_id,x,y,category,priority,status,reported_on,reported_time_ist,reporter_ref,closed_on,channel,note
P1,200,200,Streetlight out,Medium,Open,2026-09-02,20:15,R-1180,,Mobile app,
P2,800,800,Pothole,High,In progress,2026-08-28,08:40,R-2231,,Phone,Crew assigned 2026-09-01
P3,1200,250,Water leak,High,Open,2026-09-10,09:13,R-3391,,Mobile app,Water on road near shop
P4,1700,900,Pothole,Low,Resolved,2026-08-15,17:02,R-0450,2026-08-20,Web form,
P5,1000,500,Blocked drain,Medium,Reopened,2026-08-30,07:55,R-2790,,Phone,Closed 2026-09-05; reopened 2026-09-12
P6,2200,500,Fallen tree,High,Closed - duplicate,2026-09-11,06:30,R-1902,2026-09-12,Phone,Duplicate of a report not in this package
P7,,,Pothole,Medium,Open,2026-09-14,11:20,R-3010,,Phone,Caller could not give a location; to be located
P8,1200,250,Water leak,High,Open,2026-09-10,09:14,R-3391,,Mobile app,Water on road near shop
P9,1000,500,Streetlight out,Low,Open,2026-09-13,19:45,R-0077,,Mobile app,
```

`depot.csv` — unchanged from Chapter 3 (`POLYGON((1400 600,1600 600,1600 800,1400 800,1400 600),(1450 650,1550 650,1550 750,1450 750,1450 650))`); included so that R5's end point can be checked against the depot gate.

**Domains in force (from Chapter 8):** `AssetType` {SL, DR, TR}; `AssetStatus` {ACTIVE, REMOVED, MERGED}; `LocationMethod` {SURVEY, GNSS, DIGITISED, APPROX}; `ConditionScore` integer 1–5; `PoleHeightM` 3–15 (streetlights only); request `status` ∈ {Open, In progress, Resolved, Reopened, Closed - duplicate}.

**Correspondence to earlier fixtures (for checking).** Wards A/B, roads R1/R2/L1, requests P1–P7, and assets SL-0113, DR-0042, TR-0301, TR-0302 are Chapter 3's; SL-0114, SL-0127, DR-0043, SL-0250, TR-0303 and inspections INS-0001–INS-0004 are Chapter 8's; Ward C, R4, R5, INS-0005–INS-0007, P8, P9, and the second SL-0114 row are new here.

### 9.8.3 Ordered steps

Keep a **lab log** from the first minute. Every step writes to it. Times are suggestions.

1. **Intake (15 min).** Copy the package to `Chapter09_Work/`; make the original read-only. Complete Table 9.1a (all six items) from the readme and the file listing. Record counts: wards 3, roads 5, assets 10 rows, teams 2, inspections 7, requests 9 (8 with geometry). Do *not* edit anything yet.
2. **Geometry validity (20 min).** Run a validity check on `wards_d`, `roads_d`, and `depot` and record every reported problem with the engine and method used.
   > **Procedure (version-specific; not execution-tested).** *ArcGIS Pro:* **Check Geometry** (Data Management) on each layer; read the output table's `FEATURE_ID` and `PROBLEM` columns; run it once with **Validation Method** = Esri and once with OGC and note any difference [X01]. *QGIS:* **Vector ▸ Geometry Tools ▸ Check Validity** (`native:checkvalidity`), method GEOS, and read the `_errors` field of the *Invalid output* and the `message` field of the *Error output* points [X22]. *Paper:* walk each ring on graph paper and look for crossings; apply the rules in 9.5.1.
   Expected: exactly one invalid feature (9.8.4). Log the crossing point.
3. **Topology and business rules (30 min).** Apply, and log the results of: wards *must not overlap*, wards *must not have gaps*, roads *must not have dangles*, assets *inside a ward*. For each hit, decide *defect* or *exception* using the readme and 9.5.3, and write the evidence.
   > **Procedure (version-specific; not execution-tested).** *ArcGIS Pro (any licence):* zoom to the A/B boundary at y = 0 and at y = 500 and measure the gap; select each road and read its end-point coordinates; **Select By Location**-style checks are Chapter 10's topic — for this lab, read coordinates and compare by hand. *ArcGIS Pro (Standard/Advanced, optional):* create a feature dataset, import the layers, create a geodatabase topology with the four rules, validate, and read the errors in the Error Inspector; mark R5's dangle as an exception [X03] [X04] [X05]. *QGIS:* enable **Topology Checker**; add rules `wards_d: must not overlap`, `wards_d: must not have gaps`, `roads_d: must not have dangles`, `assets_d: must be inside wards_d`; **Validate All**; read the results table [X19]. **Verification item:** whether Topology Checker reports the outer boundary of the whole ward set as a "gap"; if it does, that report is an exception (the territory has an outside) and must be logged as such.
4. **Attribute and relationship checks (30 min).** Run the five checks of Table 9.6a on `assets_d`, `inspections_d`, and `requests_d` — by SQL, spreadsheet filter, ArcGIS **Select By Attributes**, or QGIS expression — and the P3/P8 and P5/P9 comparison of 9.6.2. Log every hit *and* every examined false alarm.
5. **Classify (20 min).** Give every log row one of three classes: **C — correct with evidence** (name the evidence), **O — needs the source owner** (name the question), **X — exception / false alarm** (name the evidence). Show the classification to a colleague or the instructor before step 6 if possible.
6. **Correct, on the working copy only (40 min).** For each class-C row: write the proposed correction (before → after) in the log *first*, then make the edit, then perform the three reviews of 9.4.3, then complete the before/after record (Table 9.7a). Use snapping for R4 with the agent and tolerance recorded; re-order Ward C's vertices manually (typed coordinates are preferred). **Do not run Repair Geometry / Fix geometries as the correction for Ward C.** Do not touch class-O or class-X rows.
   > **Procedure (version-specific; not execution-tested).** *ArcGIS Pro:* Modify Features pane — vertex editing for Ward B's stray vertex and Ward C's ring, endpoint move with **Edge** snapping for R4 [X07] [X08]; **Attributes** pane for SL-0127 and P8; **Save** when each edit has been reviewed [X09]. *QGIS:* Toggle Editing; **Vertex Tool** with the Vertex Editor panel for typed coordinates; **Segment** snapping for R4; attribute table for SL-0127 and P8; **Save Layer Edits** [X16].
7. **Experiment on a *second* copy (15 min, optional but recommended).** Copy `wards_d` again, run the automated repair (Repair Geometry [X02] / Fix geometries [X22]) on the *copy*, and record what happened to Ward C: geometry type, part count, area. Compare with your manual correction and with 9.7.2. Delete the experimental copy afterwards or label it clearly.
8. **Validate (15 min).** Re-run steps 2–4 on the corrected copy. Confirm the invariants in 9.8.4. Confirm that the class-O and class-X rows are unchanged.
9. **Package (10 min).** Save the corrected copy as `Chapter09_Corrected_v1/` with the log, the before/after records, the unresolved-issues list, and a one-paragraph readme stating what was corrected, what was not, and which tool and version you used.

### 9.8.4 Expected results and validation checks (hand-checked; not observed in software)

| Check | Expected result | How to verify |
| --- | --- | --- |
| Validity — invalid features | **Exactly 1:** Ward C, self-intersection at **(500, 1250)**. Wards A and B, all roads (including the closed line L1, which is valid — a self-touching *line* is valid [X20]), and the depot are valid | Step 2 report; 9.5.1 arithmetic |
| Ward B area as delivered | **998,000 m²**; gap sliver **2,000 m²** (triangle base 4 m at y = 0, height 1,000 m); at y = 500 the B edge is at x = **1002**, so P5 at (1000, 500) is **not** on Ward B's boundary in the damaged data | Shoelace on (1004,0),(2000,0),(2000,1000),(1000,1000); linear interpolation |
| Ward B after correction | Vertex (1004, 0) → **(1000, 0)**; area **1,000,000 m²**; no gap; P5 back on the shared boundary | Re-run step 3 |
| Ward C after correction | Ring **(0 1000, 1000 1000, 1000 1500, 0 1500, 0 1000)**; valid; area **500,000 m²**; total ward area A + B + C = **2,500,000 m²** | Shoelace (9.7.2); validity check |
| Ward C if *automatically* repaired on the experimental copy (step 7) | Expected from the documented linework method: a multipolygon of two triangles, **250,000 m²** in total; the Esri method's result is **not asserted** — record what you observe | Compare with 9.7.2; report to the instructor |
| Dangles | **R4** north end (300, 497): dangle, **defect** (register: joins Main Road) — corrected to **(300, 500)**, length 197 → **200 m**. **R5** north end (1500, 600): dangle, **exception** (register: dead end at the depot gate; the point lies on the depot's south edge, y = 600, 1400 ≤ x ≤ 1600). L1: no dangles (closed). R2's south end (500, 0): dangle at the edge of the package — log as *outside the package's evidence*; do not extend it | Read end-point coordinates; readme |
| Assets inside a ward | **TR-0301** at (2190, 520) is outside all three wards — **needs the owner** (ward coverage or asset location). All other assets are strictly inside A or B (no asset lies exactly on a boundary, and none is in Ward C) | Compare each (x, y) with the ward rectangles |
| Check 1 — duplicate IDs | **SL-0114 × 2**, about 1,016 m apart — **needs the owner** | GROUP BY |
| Check 2 — invalid categories | **SL-0127 `asset_type` = Lamp** — **correct to SL** (evidence: Chapter 8 source row "Streetlight"; the SL- prefix assigned under the identity policy). No invalid `status`, `location_method`, or request `status` values | NOT IN domain |
| Check 3 — missing required observation | **INS-0005 `condition_code` null** — **stays null**; unresolved | IS NULL |
| Check 4 — range | **SL-0127 `pole_height_m` = 22** — **correct to 6.71** (22 ft × 0.3048 = 6.7056 m; rounding stated). SL-0113's blank is *not* a defect (readme: not measured) | Range query |
| Check 5 — orphans | **INS-0006 → DR-0044** — **needs the owner**; retained. All seven `team_id` values resolve; the six other inspections resolve | LEFT JOIN |
| Requests | **P8 is a duplicate of P3** (same reporter R-3391, same text, 09:13/09:14, same channel): status → `Closed - duplicate`, note "Duplicate of P3", **row kept**. **P5/P9 are two reports** — no change. P7 without geometry is Chapter 3's known case — no change | 9.6.2 |
| Counts after correction | Wards 3; roads 5; assets **10 rows** (the duplicate pair is unresolved, so no row is removed or renumbered); inspections **7**; requests **9** — **nothing has been deleted** | Count each table |
| Log | At least **9 defect rows** (Ward C; Ward B gap; R4; SL-0114 pair; SL-0127 type; SL-0127 height; INS-0005; INS-0006; P8) and at least **3 exception/false-alarm rows** (R5; P5/P9; the outer boundary if reported), plus the R2 edge note and the TR-0301 owner question; unresolved list has **4 entries** (SL-0114 pair; INS-0006; INS-0005; TR-0301) | I.3 manifest |

**Tolerance statement.** All coordinates in this fixture are integers and all expected values are exact planar arithmetic; the only rounded value is 6.7056 → 6.71 m (stated). Software may display areas with decimals or a sign; a reported area of −500,000 for a clockwise ring is the same shape. If a tool reports Ward C's damaged area as anything other than 0, record the number — it is engine-dependent and meaningless (9.5.1).

### 9.8.5 Troubleshooting

| Symptom | Likely cause | Fix |
| --- | --- | --- |
| Validity check reports Ward A or B as invalid for "ring orientation" | Esri validation method's clockwise-exterior convention [X01]; the CSV rings are counter-clockwise | Not a defect in this fixture (Chapter 3, 3.3.3); log it as an engine convention; re-run with the OGC method to confirm |
| Validity check reports nothing for Ward C | Wrong layer, or the WKT was "tidied" while typing | Re-read `wards_d.csv`; Ward C's second vertex must be (1000, 1000) and its third (0, 1500) |
| Topology Checker reports a gap around the *outside* of the wards | The rule sees the territory's outer boundary as a void — **verification item** | Exception; log it; do not create a polygon |
| Your corrected Ward C has area 250,000 m² | You ran the automated repair instead of re-ordering vertices | Restore from the original; re-read 9.7.2; re-order manually |
| R4 did not snap | Tolerance in pixels too small at your zoom (9.4.2) | Zoom out, set tolerance in map units, or type (300, 500) |
| R4 snapped to (300, 500) but R1 now has an extra vertex | Topological/segment snapping added a node to R1 | Not wrong; log it (9.4.3) and note the connectivity consequence |
| You changed INS-0006 to DR-0043 | Proximity-of-ID reasoning | Restore; re-read 9.6.1 worked example B; move to the unresolved list |
| You deleted P8 | "Duplicate" treated as "delete" | Restore; set status and note; Chapter 3's P6 convention |
| You gave INS-0005 a condition of 3 | Fabrication | Restore null; 9.7.3 |
| You extended R5 to the depot centroid or split R1 under it | "Fix every error" | Restore; 9.5.3 |
| The point layer was built with WGS 84 assigned | Tool default (Chapter 3, I.5 Route C) | Rebuild with no CRS; this is Chapter 6's assignment error and must not be "fixed" by Define Projection later |
| Asset count is 9 after correction | You merged or deleted one SL-0114 row | Restore; the pair is class O |

### 9.8.6 Required learner deliverables

Submit one folder containing:

1. **Intake checklist** (Table 9.1a, six items) and the initial counts.
2. **Defect log** (Table 9.6b columns) with every finding classified C / O / X and evidence named for each.
3. **Before/after records** (Table 9.7a) for every class-C correction, including the old Ward C ring in WKT and the snapping settings used for R4.
4. **Corrected copy** `Chapter09_Corrected_v1/` (GeoPackage/geodatabase *and* re-exported CSV text) with a readme naming the software and version, and stating that class-O and class-X rows are unchanged.
5. **Unresolved-issues list** — four entries, each with the question for the owner and the evidence that would resolve it.
6. **Validation record** — steps 2–4 re-run on the corrected copy, with the invariants of 9.8.4 ticked, and the step-7 experiment result if performed.

**Scoring** follows 9.9.5. The two non-negotiable checks are: no valid feature was "repaired" (R5, P5/P9, L1, R2 untouched; Ward C corrected to the rectangle, not the triangles), and no value was fabricated (INS-0005 null; INS-0006 retained as an orphan; both SL-0114 rows present).

### 9.8.7 QGIS alternative and paper route

The QGIS steps are embedded in the procedure boxes above; they are an equivalent foundational exercise (validity check, topology rules, vertex editing with snapping, attribute queries) and are **not execution-tested**. Known differences to expect: the validity report vocabulary differs from ArcGIS Pro's (9.5.1); Topology Checker may report the territory's outer boundary as a gap (verification item); Fix geometries always outputs a multi-geometry layer [X22]. The paper route needs no software: draw the wards and roads on graph paper at 1 cm = 100 m, find the crossing and the gap by construction, run the five checks in a spreadsheet, and write the same deliverables with the "corrected copy" as corrected CSV text.

## 9.9 Independent check and progression gate

Complete this assessment without the Instructor Appendix. Where a question says "state your assumptions", an answer without them is incomplete even if the conclusion is right. Every question names the module(s) and outcome(s) it tests. All data are synthetic and on the planar training grid (metres, no CRS) unless stated.

### 9.9.1 Concept questions (six)

**Q1.** [9.1; LO1] A ward layer was digitised in 2019 from a 1:50,000 plan; every polygon is valid, there are no gaps or overlaps, and every ward has its name and code. Two wards were merged by the council in 2024. For each of the five review questions of 9.1.1, say whether this layer passes, fails, or cannot be judged from the information given — and name the *one* decision in 9.1.2 for which the failing question is decisive.

**Q2.** [9.2; LO2] Choose the single best answer and justify it in two sentences. A request table has `location_method` = GNSS for every row, captured with crews' own phones, and no other GNSS metadata. Which statement is correct?
(a) The points are accurate to a few metres because phones are.
(b) The points are less accurate than geocoded points because geocoding uses reference data.
(c) The method is known but the quality of each observation is not; the reported accuracy, fix type, and correction status should have been stored per row.
(d) Because GPS is a GNSS, the points are on the WGS 84 datum and need no transformation.

**Q3.** [9.3; LO3] A scan is georeferenced with four control points, one in each corner, first-order transformation, RMS error 0.6 m. (a) State what the 0.6 m does and does not tell you, in one sentence each. (b) Describe the one additional measurement that would let you make an accuracy statement about the *centre* of the scan. (c) Using the fitted model of Worked example 9.3-A (x = 2c − 100, y = 1100 − 2r), give the ground position of image pixel (300, 300) and the image pixel of ground point (600, 600).

**Q4.** [9.4; LO4] You must move drain DR-0042 from (995, 510) to (997, 512) because a survey found it there. Before the edit, write the two prediction sentences (attributes; relationships). Then state which snap agents you would turn *off* for this edit and why, and which of the three post-edit reviews would catch a snap onto request P5 at (1000, 500).

**Q5.** [9.5; LO5] Classify each of the following as *invalid geometry*, *topology-rule violation*, or *valid exception*, naming the rule or validity clause that applies: (a) a ward ring whose last vertex is not the first; (b) two contractor territories that overlap by contract; (c) a road that stops 0.5 m short of the road it is documented to join; (d) a footpath that crosses itself; (e) a polygon whose hole lies outside its outer ring; (f) a flyover crossing a road with no junction.

**Q6.** [9.6; LO6] Write, in SQL or plain words, the query for each of the five checks in Table 9.6a against a `stall` table (`stall_id`, `stall_type` ∈ {FOOD, GOODS, SERVICE}, `area_m2` range 4–40, `licence_no`) and a `check` table (`check_id`, `stall_id`, `result`). Then explain why a hit on check 1 cannot by itself tell you whether to delete a row.

### 9.9.2 Scenario questions (two)

**S1.** [9.2, 9.3, Chapter 6; LO2, LO3] Four things arrive on the same day: (i) a GeoJSON of streetlights; (ii) a CSV of drains with `easting`, `northing` columns and no CRS stated; (iii) a scanned 1998 drainage plan as a PNG with no world file; (iv) a spreadsheet of 300 request addresses with no coordinates. For each, name the operation that turns it into a usable layer — *georeferencing*, *geocoding*, *CRS assignment*, *reprojection*, or *none needed* — say what evidence that operation requires, and say what provenance the resulting layer must carry. State your assumptions.

**S2.** [9.5, 9.7; LO5, LO7] A contractor delivers a ward layer in which the validity check reports two self-intersecting polygons and the topology check reports six gaps and one overlap. The contractor's covering note says: "We ran the repair tool and the fix-gaps tool, so all errors are resolved; the attached copy is clean." (a) List three questions you would ask before accepting the "clean" copy, each tied to a module of this chapter. (b) Explain, using the Ward C numbers (0 / 250,000 / 500,000 m²), how a repaired polygon can be valid and wrong. (c) State what the delivery should have contained instead of the sentence quoted.

### 9.9.3 Independent practical task (unfamiliar inputs)

[9.1–9.8; LO1–LO8] The municipality's **market precinct** (synthetic; planar grid, metres, no CRS) is delivered as five CSV files with the readme below. Perform the full Chapter 9 workflow — intake, validity, topology and business rules, the five attribute/relationship checks, classification, evidence-based correction on a copy, before/after records, unresolved list — and deliver the same six items as 9.8.6. Do not use any tool you cannot name in the log.

> *Readme (synthetic).* Zones are exclusive: the zone register gives Z1 "Market zone" as x 100–500, y 100–400; Z2 "Parking zone" as x 500–800, y 100–400; Z4 "Loading bay" as the 100 m × 100 m square with south-west corner (600, 450), with no courtyard. Contractor area K1 covers the whole precinct by contract and is delivered as a separate layer. Paths: W1 is the main path; W3 ends at the north gate; W4 joins W1. Stalls are licensed only within the Market zone; licence numbers are unique per stall. Stall areas are in square metres. Checks CK-01 to CK-03 were typed from paper on 2026-09-03.

`zones.csv`
```text
zone_id,name,wkt
Z1,Market zone,"POLYGON((100 100,500 100,500 400,100 400,100 100))"
Z2,Parking zone,"POLYGON((480 100,800 100,800 400,480 400,480 100))"
Z4,Loading bay,"POLYGON((600 450,700 450,700 550,600 550,600 450),(750 460,780 460,780 490,750 490,750 460))"
```

`contractor_areas.csv`
```text
area_id,name,wkt
K1,Contractor K,"POLYGON((100 100,800 100,800 400,100 400,100 100))"
```

`paths.csv`
```text
path_id,name,wkt
W1,Main path,"LINESTRING(100 50,800 50)"
W3,Gate access,"LINESTRING(300 50,300 20)"
W4,East link,"LINESTRING(600 60,600 300)"
W5,Zigzag,"LINESTRING(200 200,300 300,200 300,300 200)"
```

`stalls.csv`
```text
stall_id,stall_type,x,y,area_m2,licence_no,status
ST-01,FOOD,150,150,9,L-1001,ACTIVE
ST-02,GOODS,200,150,12,L-1002,ACTIVE
ST-03,GOOD,250,150,12,L-1003,ACTIVE
ST-04,FOOD,300,150,900,L-1004,ACTIVE
ST-05,SERVICE,350,150,6,L-1002,ACTIVE
ST-06,FOOD,620,150,8,L-1006,ACTIVE
ST-07,FOOD,150,150,9,L-1001,ACTIVE
```

`checks.csv` (non-spatial)
```text
check_id,stall_id,checked_on,result
CK-01,ST-01,2026-09-01,PASS
CK-02,ST-09,2026-09-01,FAIL
CK-03,ST-02,2026-09-02,
```

Domains: `stall_type` {FOOD, GOODS, SERVICE}; `area_m2` 4–40; `status` {ACTIVE, DUPLICATE, REMOVED}; `result` {PASS, FAIL}.

Constraints: nothing is deleted; every correction cites the readme or a table; every exception is logged; the unresolved list names the owner's question for each open item.

### 9.9.4 Oral explanation (one)

[9.3, 9.7; LO3, LO7] In under three minutes, using Worked examples 9.3-A and 9.3-B, explain to a project manager why a georeferenced plan with "RMS error 0.00 m" placed a benchmark 12 m from its surveyed position, what a check point is, why adding the check point to the fit made the report look better and the map worse, and what sentence about accuracy you would put in the plan's provenance instead.

### 9.9.5 Submission requirements and scoring

Submit the 9.8 deliverables, written answers to Q1–Q6 and S1–S2, the practical task's six items, and be ready for the oral question. Suggested weighting (an internal recommendation, following the blueprint's Appendix B):

| Component | Weight | Evidence required |
| --- | --- | --- |
| Concept questions Q1–Q6 | 25% | Correct answer with stated assumptions; one defensible best answer for Q2; correct arithmetic in Q3(c) |
| Scenario questions S1–S2 | 20% | Four operations correctly assigned with evidence and provenance; S2 questions tied to modules; the 0 / 250,000 / 500,000 explanation correct |
| Independent practical task | 35% | All defects found and correctly classified; corrections cite evidence; no valid feature altered; nothing deleted or fabricated; before/after records complete; unresolved list with owner questions |
| Guided lab (9.8) | 15% | Invariants of 9.8.4 met; log complete including exceptions |
| Oral explanation | 5% | Residual versus check point distinguished; the "spread" effect explained; an honest provenance sentence |

**Progression rule.** Pass at 80% overall **and** none of the following critical misconceptions present anywhere in the submission; any one of them requires remediation and a fresh exercise (I.5) before Chapter 10:

- Treating a low RMS error or a zero residual as evidence of accuracy away from the control points.
- Assigning a fixed accuracy to "phones", "GPS", or a capture method rather than to a documented observation.
- Confusing georeferencing, geocoding, CRS assignment, and reprojection (in particular, applying Define Projection to an unlocated image or a CRS-less point file to "fix" it).
- Running an automated repair or a topology fix as the correction without inspecting and logging what it changed.
- Deleting or re-keying an orphan, filling a missing observation, merging co-located reports on proximity alone, or "fixing" a documented exception.
- Editing the original instead of a copy, or saving edits without a before/after record.

---

## 9.10 Media and source brief

The full media brief — slide outline, audio-lesson outline, diagram specifications, and the one justified 3D storyboard — is in the Media Appendix so that it can be updated independently of the teaching text. This module records the three requirements the blueprint places on it.

1. **Before/after diagrams** for three things: **snapping** (R4's 3 m gap closed onto R1 — and, as the cautionary twin, P9 pulled onto DR-0042), **invalid geometry** (Ward C as typed, as manually corrected, and as an automated repair would produce — with the areas 0, 500,000, and 250,000 m² printed on the panels), and **topology violations** (the Ward A/B sliver gap and the 9.5-B overlap strip). Each diagram pairs a *defect* with a *valid exception* that looks the same in plan view: R4's dangle beside R5's; the overlap strip beside a contractor-territory overlap.
2. **Optional 3D scene** — the blueprint's bridge: a flyover crossing Main Road R1 at (1800, 500), shown as a 3D scene beside its own top-down view, so that the viewer sees the two lines *cross* in plan and *not meet* in elevation. Specified in M.5 with schematic heights, labels, controls, and validation cases; a 2D/text alternative is provided.
3. **Sources.** Capture and digitising were read in the QGIS introduction [S24]; topology errors, rules, snapping distance, and search radius in [S25]; georeferencing, control points, transformations, and residuals in the Esri overview [S26]. The intake checklist, the five review questions, the defect log, the C/O/X classification, the damaged package and its manifest, and all fixture arithmetic are **instructional designs written for this course**. The additional pages [X01]–[X23] were read to support specific tool and platform claims and are listed in the reference list.

**Transition to Chapter 10.** You can now say what a dataset is fit for, how each location was obtained, whether its shapes are valid and its relationships intact, and what is still open. Chapter 10 queries that data: attribute conditions, joins by identity, and spatial predicates — including the boundary case you have now met twice, P5 on the shared edge, whose answer changed when Ward B's vertex slipped 4 m. Checked data makes those answers mean something.

---

## Glossary

Terms are listed in the order they first appear. Platform-specific terms are marked **[ArcGIS]**, **[QGIS]**, or **[SQL/PostGIS]**; unmarked terms are general.

| Term | Meaning in this chapter |
| --- | --- |
| **Positional accuracy** | How far stored locations are from true locations; estimated by comparison with an independent, better observation. |
| **Attribute correctness** | Whether stored values are true. |
| **Completeness** | Whether everything that should be present is, and nothing that should not. |
| **Logical consistency** | Whether records obey their geometry, topology, and schema rules. |
| **Currency** | Whether the data's "as of" date suits the decision's date. |
| **Fitness for purpose** | Adequacy of a dataset for a *stated* decision (Chapter 1); judged by provenance, not by a universal threshold. |
| **Intake checklist** | Table 9.1a: source, date, reference, schema, original copy, intended output — completed before editing. |
| **Provenance** | The record of how a location or value came to exist: method, device, source document, operator, date, settings, reported uncertainty. |
| **Heads-up digitising** | Tracing features on screen over a backdrop image or layer [S24]. |
| **Coordinate import** | Loading a table that already holds coordinates; provenance is inherited from upstream. |
| **GNSS / GPS** | Global navigation satellite system — any positioning constellation; GPS is the most prevalent one among several (BeiDou, Galileo, GLONASS, NavIC, QZSS) [X14]. |
| **GNSS metadata** | Per-observation quality fields — receiver, fix type, horizontal/vertical accuracy, satellites, DOP values, correction age, and more [X15]. |
| **Location profile** **[ArcGIS]** | Field Maps setting that carries the datum transformation between the receiver's reference and the map's [X15]. |
| **Geocoding / locator** | Matching address text to reference data to produce a point; a locator is "a portable file used to perform geocoding" holding "a snapshot of the reference data" [X13]. |
| **Status / Score / Addr_type** **[ArcGIS]** | Geocoding provenance fields: M/T/U, 0–100, and the match level (PointAddress, StreetAddress, Postal, Locality, …) [X11]. |
| **Georeferencing** | Fitting a transformation through control points so an image's pixels acquire map coordinates and a CRS [S26]. |
| **Control point (GCP)** | A location identifiable both on the image and in ground coordinates [S26]. |
| **Affine (first-order) transformation** | x = a + b·c + d·r, y = e + f·c + g·r; shift, scale, rotate, shear; needs ≥ 3 control points [S26] [X18]. |
| **Residual / RMS error** | Per-point difference between fitted and specified position; the root-mean-square of the residuals [S26]. Not accuracy away from the control points. |
| **Check point** | A known location withheld from the fit to test it independently (course term). |
| **World file / auxiliary file** | External text/XML holding a raster's georeferencing without rewriting pixels [S26] [X18]. |
| **Edit session** **[ArcGIS]** | Starts automatically when you modify or create data; ends with Save or Discard [X08] [X09]. |
| **Split / Reshape / Move** | Geometry edits with attribute and relationship consequences (9.4.1) [X16]. |
| **Snapping / snap agent (snap mode)** | Drawing aid that stores an existing coordinate when the pointer is within tolerance; agents choose vertex, edge, endpoint, etc. [X06] [X07] [X16]. |
| **Snapping tolerance** | The pointer-to-element distance within which snapping acts; default 10 pixels in ArcGIS Pro; pixels or map units [X06] [X16]. |
| **Search radius** **[QGIS]** | Distance used to find the vertex you click to move [S25]. |
| **Topological editing / Avoid Overlap** **[QGIS]** | Moving shared vertices in neighbours together; clipping a new polygon to its neighbours [X16] [S25]. |
| **Geometry validity** | Single-shape rules from OGC Simple Features: simple rings, no crossings, tangent-only touching, holes inside the shell, connected interior [X20]. |
| **Simple** | A line that does not pass through the same point twice except at its ends; polygons' rings must be simple [X20]. |
| **Self-intersection** | A ring or line crossing itself; makes a polygon invalid, but not a line [X20]. |
| **Validation method (Esri / OGC)** **[ArcGIS]** | Check/Repair Geometry option; Esri uses the Simplify method and clockwise outer rings; OGC follows the OGC specification [X01] [X02]. |
| **Topology** | "the arrangement of how point, line, and polygon features share geometry" [X23]; "the spatial relationships between connecting or adjacent vector features" [S25]. |
| **Topology rule** | A stated permissible relationship between features (Must Not Overlap, Must Not Have Gaps, Must Not Have Dangles, …) [X04] [X05] [X19]. |
| **Dangle / undershoot / overshoot** | A line end touching nothing; a line stopping short of, or running past, the line it should meet [S25] [X05]. |
| **Sliver / gap** | Thin void or overlap where polygon boundaries do not match [S25]. |
| **Error / exception** **[ArcGIS]** | A stored rule violation; an error marked as acceptable with evidence [X03]. |
| **Cluster tolerance / rank** **[ArcGIS]** | Distance within which vertices are moved together during validation (default 0.001 m); rank decides which features move less [X03]. |
| **Geodatabase topology** **[ArcGIS]** | Rules stored on feature classes in a feature dataset; requires Standard or Advanced licence [X03]. |
| **Topology Checker** **[QGIS]** | Core plugin that validates rules per layer and lists errors [X19]. |
| **Orphan** | A child row whose key matches no parent (Chapter 8). |
| **Defect log** | Table 9.6b: ID, symptom, affected records, likely cause, evidence, proposed correction, reviewer decision. |
| **C / O / X classification** | Correct with evidence / needs the source owner / exception or false alarm (course convention). |
| **Before/after record** | Table 9.7a: feature, element, old, new, evidence, editor, time. |
| **Automated repair** | Repair Geometry [X02], Fix geometries [X22], `ST_MakeValid` [X21]: produce valid geometry, possibly a different shape or type. |
| **Linework / Structure** **[QGIS, PostGIS]** | The two repair methods: node all rings and extract polygons; or fix rings then union shells and subtract holes [X21] [X22]. |
| **Unresolved-issues list** | Explicit list of open items with the question and evidence that would resolve each (9.7.3). |
| **Find Identical** **[ArcGIS]** | Tool reporting records identical in listed fields, optionally geometry within a tolerance [X12]. |

## Recap

1. Quality is five separate questions — positional accuracy, attribute correctness, completeness, logical consistency, currency — answered against a *stated* decision, and an intake checklist is completed before any edit.
2. Digitising, coordinate import, GNSS observation, geocoding, and georeferencing produce points that look alike and mean different things; each needs its own provenance; GPS is one GNSS; accuracy belongs to a documented observation, not to a method or a device class.
3. Georeferencing fits a transformation through control points; three points fit an affine exactly, so a zero residual proves nothing; spread the control, hold out a check point, and report *its* discrepancy. Georeferencing creates coordinates; assignment relabels; reprojection recomputes; geocoding creates from text.
4. Edit on a copy after predicting the attribute and relationship consequences; snapping closes intended gaps and, with the wrong agent or tolerance, moves observations and joins unrelated features; review visually, in the table, and against the motivating check.
5. Invalid geometry is one shape that cannot be interpreted; a topology violation is valid shapes breaking a business rule; rules come from the meaning of the layer; cul-de-sacs, contractual overlaps, and bridges are documented exceptions.
6. Five queries find duplicate IDs, invalid categories, missing observations, unit errors, and orphans; deduplication rests on report evidence, never on proximity alone; every finding and every false alarm goes in the defect log.
7. Preserve the original; record before and after; fix the cause upstream; inspect what automated repair did (Ward C: 0 → 250,000 m² by repair, 500,000 m² by evidence); keep unresolved issues explicit and never fabricate.

## Cross-references to later chapters

- Attribute queries, joins by identity, and spatial predicates with explicit boundary semantics — run on the *corrected* Chapter 9 package, including P5 on the shared edge: **Chapter 10**.
- Buffers, clips, intersects, and spatial joins whose results inherit every unresolved issue in this chapter's list: **Chapter 11** (11.8 "validate and document" continues the defect log).
- Showing positional uncertainty, unresolved items, and "not assessed" honestly on a map: **Chapter 12**.
- Configuring geodatabase topology, attribute rules, GNSS-enabled field apps with location profiles, locators and geocoding services, and image rectification: later phases.

---

## References

All pages were opened and read by the author on **19 September 2026**. IDs [S15], [S16], [S24], [S25], [S26] are the blueprint's reference register entries used by this chapter; [X01]–[X23] are additional official pages consulted to support specific claims. Esri "latest" pages (whose page metadata identified ArcGIS Pro **3.7** on the check date), ArcGIS Field Maps pages, and GPS.gov change without notice; re-check before reuse. The blueprint's `pro.arcgis.com` addresses redirect to `doc.esri.com`; the destinations are recorded. The QGIS User Guide pages are the **3.44** edition; the blueprint's *Gentle Introduction* pages are also 3.44.

| ID | Publisher | Page title | URL | Accessed |
| --- | --- | --- | --- | --- |
| S15 | Esri | ArcGIS Pro — Define Projection (Data Management) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/define-projection.html | 2026-09-19 |
| S16 | Esri | ArcGIS Pro — Project (Data Management) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/project.html | 2026-09-19 (read for Chapter 6; cited here for the reprojection row only) |
| S24 | QGIS Project | A Gentle Introduction to GIS — Data Capture (3.44) | https://docs.qgis.org/3.44/en/docs/gentle_gis_introduction/data_capture.html | 2026-09-19 |
| S25 | QGIS Project | A Gentle Introduction to GIS — Topology (3.44) | https://docs.qgis.org/3.44/en/docs/gentle_gis_introduction/topology.html | 2026-09-19 |
| S26 | Esri | ArcGIS Pro — Overview of georeferencing | https://doc.esri.com/en/arcgis-pro/latest/help/data/imagery/overview-of-georeferencing.html | 2026-09-19 |
| X01 | Esri | ArcGIS Pro — Check Geometry (Data Management) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/check-geometry.html | 2026-09-19 |
| X02 | Esri | ArcGIS Pro — Repair Geometry (Data Management) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/repair-geometry.html | 2026-09-19 |
| X03 | Esri | ArcGIS Pro — Topology in ArcGIS (geodatabase topology: feature datasets, cluster tolerance, ranks, errors and exceptions, licence) | https://doc.esri.com/en/arcgis-pro/latest/help/data/topologies/topology-in-arcgis.html | 2026-09-19 |
| X04 | Esri | ArcGIS Pro — Geodatabase topology rules and fixes for polygon features | https://doc.esri.com/en/arcgis-pro/latest/help/editing/geodatabase-topology-rules-for-polygon-features.html | 2026-09-19 |
| X05 | Esri | ArcGIS Pro — Geodatabase topology rules and fixes for polyline features | https://doc.esri.com/en/arcgis-pro/latest/help/editing/geodatabase-topology-rules-for-polyline-features.html | 2026-09-19 |
| X06 | Esri | ArcGIS Pro — Configure snapping | https://doc.esri.com/en/arcgis-pro/latest/help/editing/change-snapping-settings.html | 2026-09-19 |
| X07 | Esri | ArcGIS Pro — Use snapping | https://doc.esri.com/en/arcgis-pro/latest/help/editing/enable-snapping.html | 2026-09-19 |
| X08 | Esri | ArcGIS Pro — A quick tour of editing | https://doc.esri.com/en/arcgis-pro/latest/help/editing/a-quick-tour-of-editing.html | 2026-09-19 |
| X09 | Esri | ArcGIS Pro — Save or discard edits | https://doc.esri.com/en/arcgis-pro/latest/help/editing/save-or-discard-edits.html | 2026-09-19 |
| X10 | Esri | ArcGIS Pro — Georeference a raster to a referenced layer | https://doc.esri.com/en/arcgis-pro/latest/help/data/imagery/georeferencing-a-raster-to-a-referenced-layer.html | 2026-09-19 |
| X11 | Esri | ArcGIS Pro — What's included in the geocoded results | https://doc.esri.com/en/arcgis-pro/latest/help/data/geocoding/what-is-included-in-the-geocoded-results-.html | 2026-09-19 |
| X12 | Esri | ArcGIS Pro — Find Identical (Data Management) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/find-identical.html | 2026-09-19 |
| X13 | Esri | ArcGIS Pro — Introduction to locators | https://doc.esri.com/en/arcgis-pro/latest/help/data/geocoding/about-locators.html | 2026-09-19 |
| X14 | U.S. Government (GPS.gov) | Other Global Navigation Satellite Systems (GNSS) | https://www.gps.gov/other-global-navigation-satellite-systems-gnss | 2026-09-19 |
| X15 | Esri | ArcGIS Field Maps — Prepare for high-accuracy data collection | https://doc.arcgis.com/en/field-maps/latest/prepare-maps/high-accuracy-data-collection.htm | 2026-09-19 |
| X16 | QGIS Project | QGIS Desktop 3.44 User Guide — Editing (snapping tolerance and search radius; snapping and digitizing options; topological editing; digitizing an existing layer; advanced digitizing; saving edited layers) | https://docs.qgis.org/3.44/en/docs/user_manual/working_with_vector/editing_geometry_attributes.html | 2026-09-19 |
| X17 | Esri | ArcGIS Pro — Geocode Addresses (Geocoding Tools) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/geocoding/geocode-addresses.html | 2026-09-19 |
| X18 | QGIS Project | QGIS Desktop 3.44 User Guide — Georeferencer (the `working_with_raster/georeferencer.html` address redirects here) | https://docs.qgis.org/3.44/en/docs/user_manual/managing_data_source/georeferencer.html | 2026-09-19 |
| X19 | QGIS Project | QGIS Desktop 3.44 User Guide — Topology Checker Plugin | https://docs.qgis.org/3.44/en/docs/user_manual/plugins/core_plugins/plugins_topology_checker.html | 2026-09-19 |
| X20 | PostGIS Project | PostGIS Documentation — Chapter 4 Data Management, §4.4 Geometry Validation | https://postgis.net/docs/using_postgis_dbmanagement.html#OGC_Validity | 2026-09-19 |
| X21 | PostGIS Project | PostGIS Documentation — ST_MakeValid | https://postgis.net/docs/ST_MakeValid.html | 2026-09-19 |
| X22 | QGIS Project | QGIS Desktop 3.44 User Guide — Processing algorithms: Vector geometry (Check validity; Fix geometries; error-message table) | https://docs.qgis.org/3.44/en/docs/user_manual/processing_algs/qgis/vectorgeometry.html | 2026-09-19 |
| X23 | Esri | ArcGIS Pro — An overview of topology in ArcGIS | https://doc.esri.com/en/arcgis-pro/latest/help/data/topologies/an-overview-of-topology-in-arcgis.html | 2026-09-19 |

---

# Instructor Appendix (separate from learner material)

Learners should not read this appendix before completing the 9.9 gate.

## I.1 Answers and reasoning — comprehension checks and in-module arithmetic

**9.1.1.** *Currency:* yes — until 16 March 2026 the "latest visit" for SL-0113 was wrong for anyone asking in the intervening six months. *Completeness:* yes — for six months the inspection table was missing a visit that had happened. *Logical consistency:* no — an entry date after the event date is consistent and expected (Chapter 8, 8.7.1); the ID order is irrelevant. *Positional accuracy:* no — nothing about location. *Attribute correctness:* no — the recorded values are what the inspector observed; delay does not make them wrong.

**9.1.2.** Evidence: (1) the capture method and device with its per-observation metadata (reported horizontal accuracy, fix type, correction) or, for digitised points, the backdrop and its georeferencing quality; (2) an independent check — a sample compared against surveyed positions, with the discrepancies listed. Adequate: a ward-level tree count or canopy map. Not adequate: choosing which of two trees 2 m apart is to be felled, or planning a trench near roots.

**9.1.3.** Fills item 1 (source: ward register plan; digitised in-house), item 2 (September 2026), and part of item 4 (an expected area attribute). Does not fill item 3 (the reference is stated elsewhere in the readme, not in that sentence), item 5, item 6, or the digitising settings/operator. Also needed before correcting: the register's *shape* — the readme's "rectangle between y = 1000 and y = 1500" supplies it; the area alone would not distinguish the rectangle from other 0.5 km² shapes.

**9.2.1.** Candidates: the phone's GNSS position (`GNSS`); a map click in the app (`DIGITISED`, possibly snapped to the ward boundary — the exact boundary coordinate is the hint); a geocode of an address the reporter typed (needs a `GEOCODED` value the domain lacks). The `location_method` field — with the method-specific metadata (GNSS accuracy fields, or `Addr_type`) — tells them apart.

**9.2.2.** Positional accuracy. Use 2 (locating a drain for excavation): 0.4 m corrected may be adequate; 12 m uncorrected is not. Use 1 (ward overview) treats them alike unless a point is near a boundary.

**9.2.3.** The 900 `StreetAddress` rows: defensible for 1 km wards, with rows within the interpolation's plausible error of a boundary flagged. The 250 `Postal` rows: not defensible — a postcode centroid can lie in a different ward from the address. The 50 `Locality`: not defensible. Action: assign only the 900, report 300 as "not assignable at this match level", and ask for better address detail or reporter-stated ward.

**9.3.1.** x = 2·400 − 100 = 700; y = 1100 − 2·250 = 600 → (700, 600). A drain digitised there inherits the scan's positional error (about 12 m in the south-east, unknown elsewhere), its date (1998), and the operator's click precision; its `location_method` should be `DIGITISED` with the scan named.

**9.3.2.** Six points along the top edge test only the top edge; the fit will be even better there and the RMS will fall, while the south-east — where the stretch is — has no control and no check. The residual can only fall where control is added; accuracy elsewhere is unmeasured until a check point is placed there.

**Least-squares arithmetic for Worked example 9.3-B (four-point fit of y).** Points (c, r, y): (150, 50, 1000), (550, 50, 1000), (150, 450, 200), (550, 456, 200). Means c̄ = 350, r̄ = 251.5, ȳ = 600. Deviations dc = −200, +200, −200, +200; dr = −201.5, −201.5, +198.5, +204.5; dy = +400, +400, −400, −400. Sums: Scc = 160,000; Srr = 40,602.25 + 40,602.25 + 39,402.25 + 41,820.25 = 162,427; Scr = 40,300 − 40,300 − 39,700 + 40,900 = 1,200; Scy = 0; Sry = −80,600 − 80,600 − 79,400 − 81,800 = −322,400. Normal equations: 160,000·b + 1,200·d = 0 and 1,200·b + 162,427·d = −322,400 → b = −0.0075·d → 162,418·d = −322,400 → d = −1.98500, b = 0.014888, a = ȳ − b·c̄ − d·r̄ = 600 − 5.211 + 499.228 = 1,094.017. Fitted y: 997.00, 1,002.96, 203.00, 197.04; residuals (observed − fitted): +3.00, −2.96, −3.00, +2.96; x-residuals 0 (x = 2c − 100 fits all four exactly). RMS = √((9.00 + 8.73 + 9.00 + 8.73)/4) = √8.87 ≈ **2.98 m ≈ 3.0 m**. Hand-computed; rounding to three decimals in intermediate steps.

**9.3.3.** GeoJSON: *none needed* — RFC 7946 fixes WGS 84 (Chapter 5); at most a *reprojection* to a working CRS. Scan: *georeferencing* — there are no coordinates to relabel. "Define the projection of the scan as WGS 84" will not work because Define Projection only rewrites metadata for coordinates that exist [S15]; the scan's pixels still have no ground position.

**9.4.1.** Attributes: two rows result, lengths 500 m and 1,500 m (hand-checked), `width_m` = 12 copied to both, `name` copied. Relationships/identity: only one row can keep `R1`; the other needs a new identifier under the identity policy, and any external reference to "R1" (Chapter 1's distance results, a maintenance contract) now refers to two features. Question for the owner: what the identifier rule for split segments is (and whether the register treats them as one road).

**9.4.2.** 10 pixels ≥ 55.2 m when 1 pixel ≥ 5.52 m — a view about 5.5 km wide on a 1,000-pixel-wide map. Point capture should therefore be done zoomed in, or with the tolerance in map units, and with Point/Vertex snapping off when placing independent observations.

**9.4.3.** Not a mistake if a junction was intended: a vertex on R1 at (300, 500) is collinear, so R1's length stays 2,000 m and its shape is unchanged. The log must record that R1's vertex count changed from 2 to 3, at which coordinate, which setting caused it (segment/edge snapping with topological editing, or a topology), and that this is what makes R4 and R1 *connected* in a network sense.

**9.5.1.** Invalid. The ring runs along the cut line twice, so it "self-touch[es]" along a line — the clause "boundary rings may touch at points but only as a tangent (i.e. not in a line)" and the explicit note that "polygon holes must be represented as interior rings, rather than by the exterior ring self-touching (a so-called 'inverted hole')" [X20] both decide it.

**9.5.2.** P6 at (2200, 500) would be reported as outside every ward; P7 has no geometry and either cannot be evaluated or is reported as an error, depending on the tool. Citizen reports legitimately fall outside the wards and legitimately lack geometry, so *Must Be Properly Inside* is useful only as a "look here" list, not as an enforced rule, for this layer.

**9.5.3.** R2: its south end (500, 0) and north end (800, 900) touch nothing → two dangle reports; the south end is at the package edge (outside the evidence), the north end has no register statement → both stay open, not corrected. R2's junction with R1 at (500, 500) is an end touching the *middle* of R1, not "the end of only one other line", so no pseudo node. L1: closed, its end touches itself → no dangle under the rule's wording [X05]; how a tool treats a closed loop under the pseudo-node rule is a verification item. Exceptions: none can be *declared* without evidence; R2's ends are "open", not "exceptions".

**9.6.1.** `SELECT * FROM request WHERE status NOT IN ('Open','In progress','Resolved','Reopened','Closed - duplicate')`. A `Closed` row: invalid category; do not map it to `Resolved` or `Closed - duplicate` by guess — log as O and ask the owner what `Closed` meant in the exporting system.

**9.6.2.** Not a duplicate: it is a *follow-up* on the same incident by the same reporter. The right relationship is a reference field linking the second request to the first (Chapter 8's `DUP`-style reference generalised to a `related_request_id` with a relation type), or a *Reopened* status change on the original with the second report attached as evidence.

**9.6.3.** D-07 | Orphan: `inspection.asset_id` = DR-0044 matches no asset (check 5, LEFT JOIN on `asset`) | INS-0006 | Likely a mis-typed asset number on the paper form (DR-0043 was visited by the same team 50 minutes later) — *hypothesis only* | No evidence in the package; the paper form is needed; DR-0043's own visit (INS-0007) scores 3, INS-0006 scores 4, so re-keying would create a contradiction | None — retain; exclude from condition statistics; ask the owner for the form | Deferred.

**9.7.1.** Beyond the coordinates: the evidence (register: A and B share the whole western boundary; Ward A's east edge is x = 1000), the editor and timestamp, and the defect-log ID and review outcome (plus the old geometry in WKT if the ring was re-drawn). Editor tracking captures only who and when (Chapter 8, X12/X25).

**9.7.2.** With the default *Delete Features with Null Geometry* checked, P7's record "will be deleted from the feature class" [X02] — a legitimate, known "to be located" request destroyed. The default is unsafe for any table where null geometry is meaningful; uncheck it, or run the tool only on layers where a null shape is a defect.

**9.7.3.** *Symptom:* TR-0301 at (2190, 520) lies outside Wards A, B, and C (rule: assets inside a ward). *Explanation 1:* the ward layer is incomplete east of x = 2000. *Explanation 2:* the tree is outside the municipality's area or its position is wrong (`location_method` = APPROX). *Deciding evidence:* the ward register's statement of the territory's eastern extent; a field check or better observation of the tree. Owner: the ward register owner and the asset register owner.

## I.2 Answer key — 9.9 concept and scenario questions

**Q1.** Logical consistency: passes (valid, no gaps/overlaps). Attribute correctness: cannot be judged (names/codes may be fine; no evidence). Positional accuracy: cannot be judged from the source scale alone — a 1:50,000 source suggests coarse boundaries, but no number should be asserted without a check. Completeness: fails in the sense that the 2024 merger makes one boundary spurious (or, equivalently, currency fails and the layer is complete *for 2019*); accept either framing if argued. Currency: fails — two wards were merged in 2024. Decisive decision: "which ward has the most open requests" (use 1) — requests will be split across a ward that no longer exists.

**Q2.** (c). (a) asserts a fixed accuracy for a device class; (b) compares methods as if accuracy were a property of the method; (d) confuses "a GNSS" with "a datum" and ignores the receiver-to-map transformation [X15].

**Q3.** (a) It tells you the root-mean-square of the four residuals at the control points after least-squares fitting [S26]; it does not tell you the error anywhere else, nor whether one bad point has been spread across the others. (b) A surveyed point near the centre, withheld from the fit, whose predicted-versus-known discrepancy is reported. (c) (300, 300) → x = 500, y = 500 → (500, 500) (the R1/R2 junction). Ground (600, 600) → c = (600 + 100)/2 = 350, r = (1100 − 600)/2 = 250 → pixel (350, 250).

**Q4.** Attributes: DR-0042's coordinates change from (995, 510) to (997, 512); `location_method` should become SURVEY (if not already) and the survey's provenance recorded; any derived ward/distance values are stale (still strictly in Ward A: 997 < 1000). Relationships: INS-0001, INS-0002, and any request linked by `asset_id` are unaffected because the key does not change. Turn off Point, Vertex, and Edge agents (or all snapping — hold Spacebar in ArcGIS Pro [X07]) and type the coordinate: the target is a surveyed position, not an existing feature. The attribute review (reading the stored coordinate) catches a snap onto P5 at (1000, 500); the visual review would too if zoomed in enough, but the coordinate is the proof.

**Q5.** (a) invalid — unclosed ring [X01] / ring not closed under OGC. (b) valid exception — *Must Not Overlap* does not apply to a layer whose meaning permits overlap. (c) topology-rule violation — dangle/undershoot under *Must Not Have Dangles* [X05]; the register says it joins. (d) valid geometry — self-intersecting lines are valid [X20]; a rule violation under *Must Not Self-Intersect* [X05] that needs evidence (it may be a digitising slip or a genuine zigzag) — "open" is the right classification. (e) invalid — "interior rings are contained in the exterior ring" [X20]. (f) valid exception — *Must Not Intersect* [X05]; the crossing is a bridge (9.5.3).

**Q6.** 1: `SELECT stall_id, COUNT(*) FROM stall GROUP BY stall_id HAVING COUNT(*) > 1` (and the same on `licence_no`). 2: `WHERE stall_type NOT IN ('FOOD','GOODS','SERVICE')`. 3: `SELECT * FROM "check" WHERE result IS NULL`. 4: `WHERE area_m2 < 4 OR area_m2 > 40`. 5: `SELECT c.* FROM "check" c LEFT JOIN stall s ON s.stall_id = c.stall_id WHERE s.stall_id IS NULL`. A check-1 hit says two rows share a key; it cannot say whether they are one stall entered twice (keep one, mark the other DUPLICATE) or two stalls wrongly numbered (renumber one under the owner's policy) — the decision needs the other fields and the owner.

**S1.** (i) GeoJSON → *none needed*: RFC 7946 fixes WGS 84 and longitude/latitude order (Chapter 5); evidence is the file's conformance; provenance: source, date, and the upstream capture method (the file does not say how the lights were located). Possibly *reprojection* for planar work. (ii) CSV with easting/northing and no CRS → *CRS assignment*, but only after the CRS is *established* from evidence (supplier statement, plausibility checks — Chapter 5, 5.7.3; Chapter 6, 6.2.2); never guessed; then *reprojection* if needed. Provenance: the evidence for the CRS and the upstream method. (iii) PNG with no world file → *georeferencing* with spread control points, a held-out check point, the transformation type, and the residual/check report; provenance: the 1998 source, the control points, the check discrepancy, "not for excavation" if warranted. (iv) addresses → *geocoding* with a named locator; provenance: locator and reference-data date, `Status`, `Score`, `Addr_type` per row [X11]; Postal/Locality-level matches flagged. Assumptions to state: the GeoJSON is RFC-conformant; the CSV's supplier can be reached; the scan has identifiable control features.

**S2.** (a) Which tool, method, and version were run, and on which copy — is the original preserved? (9.7.1, 9.7.2). Which features did the repair and the gap fix touch, with before/after geometry and areas — which polygon *yielded* in each gap and overlap fix, and on what evidence? (9.7.2, 9.5.2). Were any of the seven topology reports legitimate exceptions, and how were they distinguished? (9.5.3). Also acceptable: what did the two self-intersections look like — vertex-order errors or genuine overlaps? (b) Ward C's bow-tie has a meaningless signed area of 0; an automated linework repair yields two valid triangles totalling 250,000 m²; the register's rectangle is 500,000 m². The repaired polygon is valid by every check and covers half the real ward: validity is a property of the shape, correctness is a property of the shape *against evidence*. (c) The original, the corrected copy, a defect log with C/O/X classification and evidence, before/after records for every change, the tool settings, the exceptions marked, and an unresolved-issues list.

## I.3 Private defect manifest and expected results — 9.8 lab

**Manifest (what was altered in `Chapter09_Damaged/` and why).**

| ID | Item | Class | What was done to the fixture | Expected learner decision |
| --- | --- | --- | --- | --- |
| D1 | Ward C self-intersection | C | Vertices 3 and 4 of the intended rectangle swapped, producing a bow-tie crossing at (500, 1250); signed area 0 | Re-order manually to (0 1000, 1000 1000, 1000 1500, 0 1500); area 500,000 m²; evidence: register rectangle. Automated repair (250,000 m²) rejected as the correction |
| D2 | Ward B sliver gap | C | First vertex (1000, 0) moved to (1004, 0); area 998,000 m²; 2,000 m² gap; P5 no longer on B's boundary | Move vertex back to (1000, 0); evidence: register "entire western boundary shared"; Ward A's east edge x = 1000 |
| D3 | R4 undershoot | C | North end (300, 500) moved to (300, 497); length 197 m | Snap/type to (300, 500); length 200 m; evidence: road register |
| D4 | Duplicate `asset_id` SL-0114 | O | A second row SL-0114 added at (1250, 420), GNSS, 2025 | Retain both; ask the owner which is SL-0114 and what the other is; no renumbering |
| D5 | Invalid `asset_type` | C | SL-0127's type changed from SL to `Lamp` | Correct to SL; evidence: Chapter 8 source row "Streetlight" and the SL- prefix rule |
| D6 | Unit error | C | SL-0127's `pole_height_m` set to 22 (the source's 22 ft) | Correct to 6.71 (6.7056 exact; rounding stated); evidence: Chapter 8 conversion log |
| D7 | Orphan inspection | O | INS-0006 keyed to non-existent DR-0044 (DR-0043 visited 50 min later, different score) | Retain; exclude from statistics; ask for the paper form; do not re-key |
| D8 | Missing observation | O | INS-0005 `condition_code` blank with remark "condition not scored" | Leave null; unresolved; do not fabricate |
| D9 | Duplicate request | C | P8 added as a copy of P3 (same reporter R-3391, text, channel, one minute later) | Status `Closed - duplicate`, note "Duplicate of P3"; row kept |
| F1 | R5 dead end | X | Dead-end road added at the depot gate, documented in the register | No change; exception logged |
| F2 | P9 at P5's location | X | Second, different report at (1000, 500) | No change; two reports; coincidence logged |
| F3 | TR-0301 outside wards | O | Unchanged from Chapter 1; now visible under the "inside a ward" rule | Unresolved; owner question about ward coverage / asset position |
| — | SL-0113 height blank | not a defect | Readme: not measured | No change; not in the unresolved list (or listed as "known missing", either is acceptable) |
| — | R2 ends; outer boundary "gap" | outside evidence / tool artefact | Not altered | Logged as open / exception; no change |

**Expected results** are those in 9.8.4; all values hand-checked (I.1 and 9.5.1/9.7.2 arithmetic). Counts after correction: 3 / 5 / 10 / 2 / 7 / 9. Ward areas after correction: 1,000,000 / 1,000,000 / 500,000. R4 length 200 m. Nothing deleted.

**Marking notes.** A learner who ran Repair Geometry as the correction and delivered a 250,000 m² Ward C fails the "no valid feature repaired unnecessarily / no wrong repair" check even if everything else is right. A learner who corrected INS-0006 to DR-0043 has the critical misconception "re-keying an orphan". A learner who *proposed* GOODS-style guesses but logged them as O with the question is doing it right.

## I.4 Expected results — 9.9.3 practical task (market precinct)

| Finding | Class | Expected handling (hand-checked values) |
| --- | --- | --- |
| Z4 invalid — interior ring (750–780, 460–490) lies outside the exterior ring | C for the geometry, O for the meaning | Remove the interior ring (register: "no courtyard"); Z4 becomes the 10,000 m² square. The 900 m² square that was typed as a hole is *something* — log as O ("was this a kiosk polygon?") |
| Z1/Z2 overlap: strip x 480–500, y 100–400 = 6,000 m² | C | Move Z2's west edge to x = 500 (register); Z2 area 96,000 → 90,000 m²; Z1 unchanged at 120,000 m² |
| K1 overlaps every zone | X | Separate layer, contractual coverage; no change |
| W3 dangle at (300, 20) | X | Register: ends at the north gate |
| W4 south end (600, 60), 10 m from W1 (y = 50) | C | Move to (600, 50); length 240 → 250 m; register: "joins W1" |
| W4 north end (600, 300) | open | No statement; log; no change |
| W1 ends (100, 50) and (800, 50) | open / precinct edge | Log; no change |
| W5 self-crossing line | valid geometry; rule question | Valid under OGC [X20]; flagged under *Must Not Self-Intersect*; no evidence → O |
| ST-03 `stall_type` = GOOD | O (model answer) | Not a domain value; GOODS is the obvious candidate but the package holds no per-stall type evidence; propose GOODS, ask the owner. A learner who corrects it must state the assumption; deleting or leaving it unlogged is wrong |
| ST-04 `area_m2` = 900 | O | Out of range; 9.00 with a slipped point is a hypothesis; no evidence |
| ST-05 `licence_no` = L-1002 (also ST-02) | O | Licence numbers unique per stall (readme): which stall holds L-1002? |
| ST-06 at (620, 150) inside Z2 | O | "Licensed only within the Market zone": misplaced or unlicensed — owner |
| ST-07 identical to ST-01 (licence L-1001, same coordinates, type, area) | C | One stall entered twice: ST-07 `status` → DUPLICATE (note "of ST-01"); row kept; evidence: unique-licence rule plus identical fields |
| CK-02 → ST-09 | O | Orphan; retain; ask |
| CK-03 `result` null | O | Missing observation; leave null |
| Counts | — | Zones 3, contractor areas 1, paths 4, stalls 7, checks 3 — unchanged |

## I.5 Fresh exercise for retesting (different values, same concepts)

Shift every Chapter 9 fixture coordinate by (+250, +150) and rename identifiers. Replace the defects as follows, recomputing every expected value by hand before use: make Ward C's error an *unclosed ring* instead of a bow-tie (Check Geometry "Unclosed rings"); put the sliver *overlap* (not gap) between A and B by moving B's vertex to (996 + 250, 0 + 150); give the undershoot to R5 and make R4 the documented dead end; make the duplicate identifier a pair of *identical* rows (one light entered twice, 0 m apart) so that the decision flips to "mark one DUPLICATE"; make the orphan a `team_id` (T-E) instead of an asset; make the unit error a `pole_height_m` of 0.65 (centimetres/metres slip); and make the request pair a *follow-up* (same reporter, 11 days apart, "still not fixed") rather than a double tap. Keep the retest key with this appendix.

## I.6 Building and running the lab — routes and verification items (not execution-tested)

Nothing in this section has been run by the author. Record the application version and every observation in I.8.

**Build.** Convert the seven CSV files of 9.8.2 (plus `depot.csv` from Chapter 3) to `Chapter09_Damaged.gpkg` or a file geodatabase by Chapter 3, Instructor Appendix I.5, Routes A–C; the notes there about the `wkt` column, the P7 empty geometry, and the tool default that assigns WGS 84 apply unchanged. *Verify:* layer counts 3 / 5 / 10 / 2 / 7 / 9 (+ depot 1); Ward C loads as one feature (some loaders reject invalid WKT — if Ward C is dropped, load `wards_d` as a table and construct the polygon in the application, and note it); `assets_d` keeps both SL-0114 rows (a loader that enforces a unique key on `asset_id` will refuse — do not add such a constraint); `inspections_d` keeps INS-0005's null and INS-0006's DR-0044.

**Route A — ArcGIS Pro 3.x (primary).** (1) Check Geometry on `wards_d`, `roads_d`, `depot`, Esri and OGC methods [X01]; record the Ward C `PROBLEM` text and any ring-orientation reports for A/B. (2) *Verification item:* Repair Geometry on a *copy* of `wards_d`, both methods [X02]; record Ward C's resulting geometry type, part count, and area — the chapter deliberately does not assert the Esri method's result for a bow-tie. (3) Optional (Standard/Advanced): feature dataset, geodatabase topology with *Must Not Overlap*, *Must Not Have Gaps*, *Must Not Have Dangles*, and *Must Be Properly Inside* (assets/wards); validate; record whether the outer boundary is reported under *Must Not Have Gaps* and confirm marking R5 as an exception [X03] [X04] [X05]. (4) Editing: confirm the Modify Features tool labels (vertex editing, move) and the route for typing a vertex coordinate; confirm Edge snapping closes the R4 gap at the tolerance and zoom you record [X06] [X07] [X08]. (5) Select By Attributes for checks 1–4; check 5 by a join or by hand. (6) Record any label that differs from the chapter text.

**Route B — QGIS 3.44.** (1) *Vector ▸ Geometry Tools ▸ Check Validity*, method GEOS, then QGIS; record the `_errors` text for Ward C [X22]. (2) Enable Topology Checker; rules as in 9.8.3 step 3; *Validate All*; record whether the territory's outer boundary is reported as a gap and how L1 and R2's ends are reported [X19]. (3) *Verification item:* Fix geometries (both methods) on a copy of `wards_d`; record Ward C's output (expected: multipolygon of two triangles, 250,000 m² — confirm) [X22]. (4) Vertex Tool with the Vertex Editor panel for typed coordinates; Segment snapping for R4; record tolerance and units [X16]. (5) Attribute-table filters or expressions for checks 1–4; a join or spreadsheet for check 5.

**Route C — paper/spreadsheet.** No verification needed beyond the arithmetic in I.1, 9.5.1, and 9.7.2, which the instructor should re-derive once.

**Licence and version notes.** Check Geometry and Repair Geometry: Basic is "Limited"; a Standard or Advanced licence is required when the input is in an enterprise database or geodatabase [X01] [X02] — irrelevant for the file-based lab. Geodatabase topology: Standard or Advanced [X03]. QGIS Fix geometries "Structure" method needs GEOS ≥ 3.10 (Help ▸ About) [X22].

## I.7 Blueprint corrections, source notes, and verification items

- **No factual error was found in the Chapter 9 specification.** The blueprint's fixture facts reused here (P5 on the shared boundary; P6 outside; Chapter 3 and 8 coordinates) were re-derived by hand and agree.
- **Source coverage — [S26] is cited by the blueprint for capture methods (9.2.1) as well as georeferencing.** The Esri georeferencing overview covers control points, transformations, residuals, and saving; it does not describe digitising, GNSS, or geocoding. Module 9.2 is therefore supported by [S24] (digitising), [X14]/[X15] (GNSS), and [X11]/[X13]/[X17] (geocoding); [S26] is cited only for the georeferencing row. This is a coverage note, not a correction.
- **Source coverage — [S25] on snapping.** [S25] defines snapping distance and search radius and warns about a too-large snapping distance; the snap-agent vocabulary and the pixel/map-unit setting come from [X06]/[X07] (ArcGIS Pro) and [X16] (QGIS).
- **Deliberately unasserted:** what ArcGIS Pro's Esri validation method produces for a bow-tie polygon (9.7.2); whether QGIS Topology Checker reports the outer boundary as a gap; how either tool treats a closed line under the pseudo-node rule; the exact Modify Features tool labels and typed-coordinate route in the installed release. Each is a verification item in I.6.
- **QGIS edition.** The 3.44 User Guide was used because 3.44 was the long-term release on the check date and the older 3.40 pages showed an end-of-life banner; the blueprint's *Gentle Introduction* links are already 3.44.
- **GPS.gov.** The blueprint's register has no GNSS source; [X14] was added as an official primary source for the GNSS/GPS distinction. The page listed five non-GPS systems on the check date.
- **Field Maps page [X15]** is cited only for what the app *can* record and configure (metadata fields, required accuracy, location profiles); no accuracy figure was taken from it because it states none.

## I.8 Instructor change log

| Date | Change | Affected sections | Rechecked |
| --- | --- | --- | --- |
| 2026-09-19 | Initial revision 1.0 written from the blueprint; all references opened and read; no software execution | All | — |
| *(to be completed)* | Record installed ArcGIS Pro / QGIS versions; observed Repair Geometry / Fix geometries result for Ward C; Topology Checker outer-boundary behaviour; tool labels | 9.7.2, 9.8.3, 9.8.4, I.6 | 9.8.5, M.3 D2 |

---

# Media Appendix

## M.1 Slide outline (mapped to module IDs)

| # | Module | Slide title | Content / visual | Question before the reveal |
| --- | --- | --- | --- | --- |
| 1 | — | Imperfect data | Chapter 8's schema with red marks where Chapter 9's defects sit | "Which of these would a database constraint have caught?" |
| 2 | 9.1.1 | Five questions, not one score | Table 9.1.1 as five cards | "Can a dataset fail one and pass four?" |
| 3 | 9.1.2 | Overview map vs excavation | Two uses, one asset row, two verdicts | "Which use does DR-0042's row satisfy?" |
| 4 | 9.1.3 | Before you edit | Table 9.1a checklist | "Which item is the only 'undo' after Save?" |
| 5 | 9.2.1 | Five ways to make a point | The 9.2.1 table, one row per click | "Which rows produce numbers you did not observe?" |
| 6 | 9.2.2 | GPS ⊂ GNSS | Six constellations; a receiver's metadata card | "Where does accuracy live?" |
| 7 | 9.2.3 | Three points, one address | D4 (address / roof / gate) | "Which one is wrong?" (none) |
| 8 | 9.3.1 | Three points fit exactly | D5 panel 1: scan with G1–G3; formula x = 2c − 100, y = 1100 − 2r | "What is the RMS?" (0) |
| 9 | 9.3.2 | The check point | D5 panels 2–3: K1 at 12 m; refit spreads to ±3 m | "Did the map get better?" |
| 10 | 9.3.3 | Four operations | The 9.3.3 table | "Which two create coordinates?" |
| 11 | 9.4.1 | Predict, then edit | R4 prediction sentences | "What else changes?" |
| 12 | 9.4.2 | Snapping: 3 m closed, 11 m stolen | D1 before/after pair | "Which agent caused the second panel?" |
| 13 | 9.4.3 | Saved ≠ correct | Three reviews | "Which review reads a coordinate?" |
| 14 | 9.5.1 | Invalid vs rule-breaking | D2 (Ward C) beside D3 (gap/overlap) | "How many features are involved in each?" |
| 15 | 9.5.2 | Rules come from meaning | Rule table: wards / roads / assets | "Is 'assets inside a ward' a rule or a question?" |
| 16 | 9.5.3 | Exceptions | R4 vs R5; territories; the bridge (M.5 still) | "Same symptom — what differs?" |
| 17 | 9.6.1 | Five queries | Table 9.6a | "Which query cannot tell you what to do?" |
| 18 | 9.6.2 | Same place, same report? | P3/P8 vs P5/P9 evidence cards | "Which pair is one report?" |
| 19 | 9.6.3 | The defect log | Table 9.6b with the INS-0006 row | "Why do false alarms get rows?" |
| 20 | 9.7.1 | Original, before/after, cause | Table 9.7a | "What does editor tracking *not* record?" |
| 21 | 9.7.2 | Valid and wrong | D2 panel 3 with 0 / 500,000 / 250,000 | "Which number is the ward?" |
| 22 | 9.7.3 | Leave it open | The four unresolved items | "What would resolving each one require?" |
| 23 | 9.8 | The lab | Package contents; C/O/X | "What must you *not* do to Ward C?" |
| 24 | 9.9 | Gate | Critical misconceptions list | — |

Speaker notes should keep the click-level procedure out of the slides; it lives in the lab handout (9.8.3).

## M.2 Audio-lesson outline

Pronounce: "GNSS" as letters; "RMS" as letters; "OGC" as letters; "CRS" as letters. Introduce every unit at first use ("three metres", "ten pixels — which is a different ground distance at every zoom"). Pause for prediction where marked.

1. **Open (9.1).** "Chapter 8 built the tables. Today the data arrives — and some of it is wrong." State the five questions as five separate voices. Describe the two uses: "the council wants a count per ward; the crew wants to dig". *Pause:* "Same row — which use does it satisfy?"
2. **Capture (9.2).** Walk the five methods as five people making the same point: the tracer, the importer, the crew with a receiver, the address matcher, the scan operator. Say the GNSS sentence plainly: "GPS is one system; there are others; what matters is what the receiver reported about itself." Describe the address example verbally: "a point near the road computed from house numbers; a roof centre; a gate someone stood at — three facts, not three attempts at one fact."
3. **Georeferencing (9.3).** Describe the scan as "six hundred by six hundred pixels, rows counted downward". Give the three crosses and the result — "two metres per pixel, no rotation". *Pause:* "What is the residual?" Then the benchmark: "on the paper it sits six rows too low… the model puts it twelve metres south. The report said zero." Then the refit: "add it, and the report says three metres — and three corners moved." Close with the four-operation contrast in one sentence each.
4. **Editing (9.4).** Predict R4 aloud: "the lane grows three metres; nothing refers to it; but it now touches Main Road — is that a junction?" Describe snapping as "the software storing the neighbour's coordinate instead of your click". Give the eleven-metre theft of P9 by the drain. *Pause:* "Which agent, and what tolerance, and at what zoom?"
5. **Validity and topology (9.5).** Describe Ward C by walking it: "bottom-left, bottom-right, top-*left*, top-right — the second and fourth legs cross in the middle, at five hundred, twelve-fifty." Give the three numbers: zero, two hundred and fifty thousand, five hundred thousand. Then the gap: "four metres at the bottom, nothing at the top — a two-thousand-square-metre triangle nobody owns." Then the three exceptions, each with its evidence.
6. **Diagnosis (9.6).** Read the five queries as questions in English. Tell the INS-0006 story: "DR-0044 doesn't exist; DR-0043 was visited fifty minutes later and scored differently. *Pause:* fix it?" Then P3/P8 and P5/P9.
7. **Correction (9.7).** The three rules; the Ward C repair story; the four open items. End: "A dataset with an honest open list is better than one that passes."
8. **Lab and gate (9.8–9.9).** Describe the package, the C/O/X classes, and the two non-negotiables.

## M.3 Diagram specifications

All geometry is **schematic** and drawn from the fixture coordinates on the training grid; no diagram is a measured map.

- **D1 — Snapping, before/after (9.4.2).** Two pairs. *Pair 1:* R1 at y = 500 and R4 ending at (300, 497), zoomed so the 3 m gap is visible, labelled "3 m"; after: endpoint on R1, label "(300, 500)". *Pair 2:* DR-0042 at (995, 510) and a cursor at (1000, 500) with a 15-pixel snap circle drawn to scale at 1 m/pixel; after: P9 stored at (995, 510) with a warning label "observation moved 11.2 m". Legend: agent name and tolerance for each pair.
- **D2 — Ward C, three panels (9.5.1, 9.7.2).** Panel 1 "as typed": the bow-tie with vertex numbers 1–4 and the crossing at (500, 1250), area label "0 (meaningless)". Panel 2 "corrected by evidence": the rectangle, area "500,000 m²", register excerpt beside it. Panel 3 "automated repair (documented method)": two triangles touching at (500, 1250), area "250,000 m²", label "valid — and wrong". Instructor: replace panel 3's caption with the observed result after I.6.
- **D3 — Gap and overlap (9.5.1, 9.5.2).** Left: Ward A and the damaged Ward B with the sliver triangle hatched, "2,000 m²", P5 marked with "2 m outside B at y = 500". Right: Ward A and a Ward B starting at x = 990, overlap strip hatched, "10,000 m²". Under each: "valid shapes; rule broken".
- **D4 — Three observations of one address (9.2.3).** A street segment, a building outline, points A (296, 402), B (312, 405), C (306.2, 398.7) with their provenance cards. Caption: "three facts".
- **D5 — Georeferencing with a check point (9.3).** Panel 1: the 600 × 600 scan with G1–G3 at their pixel positions and the ground grid overlaid; formula printed. Panel 2: K1's true position (1000, 200) and the model's (1000, 188), arrow "12 m". Panel 3: residual bars ±3 m at all four points after the refit, RMS "≈ 3.0 m", caption "report improved, map worsened".
- **D6 — Four operations (9.3.3).** A 2 × 2 grid: *creates coordinates* (georeferencing, geocoding) vs *has coordinates* (assignment, reprojection); *changes numbers* vs *changes labels only*.
- **D7 — Defect vs exception (9.5.3).** Three rows: R4 vs R5 (identical dangle symbols, different register lines); ward overlap vs contractor overlap; bridge in plan vs bridge in section (still from M.5).

## M.4 Table for slides — the C / O / X decision (from 9.5.3, 9.6, 9.7)

| Question | Yes → | No → |
| --- | --- | --- |
| Is there evidence that the geometry/value is *right* as delivered? | **X** — exception; log the evidence; change nothing | next |
| Is there evidence of what the *correct* value or shape is? | **C** — log proposal, edit the copy, review, record before/after | next |
| — | | **O** — retain as is; write the owner's question and the evidence that would resolve it |

## M.5 Interactive / 3D storyboard — the bridge that does not connect (9.5.3)

- **Learning objective.** The viewer can state that two lines crossing in plan view do not necessarily connect, and that a connectivity rule needs a level attribute, Z values, or a documented exception.
- **Scene objects (schematic; synthetic; training grid, metres).** Ground plane; Main Road R1 as a ribbon from (0, 500) to (2000, 500) at height 0; a flyover as a ribbon from (1800, 300) to (1800, 700) rising to a **schematic** height of 6 m over R1 (label: "height illustrative — not a design"); Station Road R2 meeting R1 at (500, 500) at height 0 as the contrasting true junction; depot access R5 ending at (1500, 600).
- **Labels.** Each road's ID and `level` value (R1 = 0, R2 = 0, R5 = 0, flyover = 1); at (1800, 500): "plan-view crossing — no junction"; at (500, 500): "junction — shared vertex".
- **Units.** Metres on all axes; the vertical axis carries a visible "schematic height, 1:1, no exaggeration" tag — or, if the scene exaggerates for legibility, the exaggeration factor printed on screen.
- **Controls.** Orbit/tilt between top-down (tilt 90°) and oblique (tilt 30°); a toggle "show as network" that colours connected segments after choosing one of three rules: *2D intersect*, *same level only*, *exception marked at (1800, 500)*; a toggle showing the *Must Not Intersect* error marker at the crossing.
- **Expected behaviour.** In top-down view the flyover and R1 appear to cross like R2 and R1; in oblique view the gap is visible. With the *2D intersect* rule, the network colours the flyover as connected to R1 (wrong); with *same level only* or with the exception marked, it does not (right); R2 is connected under all three rules; R5 is a dead end under all three.
- **Validation cases.** (1) Crossing location reads (1800, 500) in both views. (2) Under *2D intersect*, the path R2 → R1 → flyover is coloured as reachable; under the other two rules it is not. (3) R5's end is reported as a dangle in all modes and is labelled "exception (register)". (4) Switching tilt never changes any coordinate or rule result — only what is visible.
- **2D/text alternative.** D7's third row: the plan view beside a *section* drawing along x = 1800, and the sentence "apparent crossing in plan does not establish connectivity; a level field or Z, or a documented exception, does". No other 3D content is proposed for this chapter: snapping, validity, and the defect log are taught better by the 2D diagrams above.
