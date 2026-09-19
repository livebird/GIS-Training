# GIS and ArcGIS Fundamentals — Phase 1

# Chapter 12 — Cartography and interpreting maps correctly

## Document metadata

| Item | Value |
| --- | --- |
| Chapter ID | Phase 1, Chapter 12 (final chapter of Phase 1) |
| Title | Cartography and interpreting maps correctly |
| Source specification | `GIS_Phase_1_Chapter_Authoring_Blueprint.md`, revision 1.0 (18 September 2026) |
| Document revision | 1.0 — 19 September 2026 |
| Reference-check date | 19 September 2026 (every cited page was opened and read on this date; see the reference list for exact URLs) |
| Audience | Software developers at LiveBird Technologies who have completed Chapters 1–11 and have no other GIS background |
| Software referenced | ArcGIS Pro ("latest" documentation; earlier chapters recorded the release shown on the check date as **3.7** — record the installed version when the lab is run); ArcGIS Online Map Viewer (documentation as published on the check date); QGIS Desktop **3.44** User Guide and Gentle Introduction (the pinned edition used by the blueprint) |
| Execution status | **No procedure in this chapter has been executed in ArcGIS Pro, ArcGIS Online, or QGIS by the author.** Every expected number (scale arithmetic, class breaks, rates, counts, distances) was derived by hand from the printed fixtures and is labelled *hand-checked*. Every software procedure is written from the official page cited beside it and is marked **not execution-tested**; the instructor must build the lab maps once in the installed versions (Instructor Appendix I.6) before issuing the lab. |
| Data status | Every ward, count, household figure, road length, request, reporter reference, note, coordinate, and date in this chapter is **synthetic training material**. Nothing describes a real municipality, a real resident, or a real risk. The Earth-referenced exit fixture reuses Chapter 6's arbitrary location. |

### How to use this document

Read the modules in order; each assumes the ones before it. Boxes labelled **Procedure (version-specific)** contain software steps that will age; everything outside those boxes is durable concept. Boxes labelled **Platform note** separate ArcGIS-, QGIS-, or web-library-specific behaviour from general cartographic principle, and boxes labelled **Verification item** tell an instructor what must be checked in the installed environment before a step is treated as fact. Comprehension checks have no answers in the learner text; the answers are in the Instructor Appendix, which learners should not open before the progression gate in 12.9.

Chapters 3–11 taught you to understand, repair, question, and analyse geographic data. This chapter is about the last step, which is also the step most people see first: **putting the checked result in front of a reader so that the reader makes the right decision — and does not make a wrong one because of how the map was drawn.** A map is a user interface for a spatial result. Like any interface, it can be honest or misleading with exactly the same data behind it. The skill of this chapter is choosing encodings — symbols, classes, denominators, legends, notes — that show what the data supports and *visibly* show what it does not. It is deliberately introductory: cartographic judgment, not graphic-design certification and not advanced thematic mapping.

---

## Prerequisites

- **Chapter 3.** Features, geometry types, attributes, and the difference between a dataset, a layer, and a map. Symbology is a *layer* property; two layers on the same dataset can look completely different.
- **Chapter 4.** Cell size, resolution, and the sentence "cell size is not positional accuracy" (4.3.1). Module 12.2 puts that sentence into a recap table next to map scale and coordinate precision.
- **Chapter 5.** Coordinate precision (four decimal places of a degree ≈ 11 m, 5.2.2) and the coordinate card.
- **Chapter 9.** Positional accuracy belongs to a capture method, not to a file; the georeferencing check point with a 12 m error (9.3.2); quality logs; keeping unresolved issues explicit (9.7.3).
- **Chapter 10.** Attribute conditions, `IS NULL` versus equality to zero, and the shared-boundary Policy W-1 (10.7.2), used here whenever a count per ward appears.
- **Chapter 11.** Counts per ward from a spatial join with zero-count areas preserved and unmatched records reconciled (11.6); the recorded limitations of an analysis (11.8.3). This chapter *communicates* those results; it never re-derives them.
- Ordinary developer experience: reading a UI specification, thinking about the reader of an interface, and the idea that the same data can be rendered many ways.

## Learning outcomes

By the end of this chapter you should be able to:

| ID | Outcome | Where it is taught | Where it is checked |
| --- | --- | --- | --- |
| LO1 | State a map's purpose as the decision its reader must make; describe the medium, viewing size, interaction, and required detail; and remove layers and detail that do not serve the decision. | 12.1 | 12.9 concept Q1; lab; critique |
| LO2 | Read and check a representative fraction; use large-scale/small-scale correctly; separate map scale, raster cell size, coordinate precision, and positional accuracy; and explain why zooming in or adding decimals adds no evidence. | 12.2 | 12.9 concept Q2; scenario S1 |
| LO3 | Match the symbol type to the variable (categories → unique symbols; ordered magnitudes → graduated colour or size); build a visual hierarchy; keep essential distinctions readable without colour alone, and run a grayscale/colour-vision check. | 12.3 | 12.9 concept Q3; lab |
| LO4 | Classify one numeric table by equal interval, quantile, and natural breaks by hand; explain each method's grouping objective; show how breaks change the apparent pattern; disclose intervals and units; and reason about recalculated classes when comparing maps. | 12.4 | 12.9 concept Q4; scenario S2; lab |
| LO5 | Distinguish totals from rates and densities; compute a rate with a stated denominator; choose and defend a denominator for the actual question; and refuse to present a rate as proof of risk. | 12.5 | 12.9 concept Q5; scenario S2; lab; Phase 1 practical |
| LO6 | Supply title, legend, units, source/date, method notes, and (when useful) scale bar or orientation aid; show zero and missing separately; and state aggregation, boundary policy, and limitations on the map itself. | 12.6 | 12.9 concept Q6; lab; critique |
| LO7 | Recognise that hiding a field or turning off a layer is presentation, not access control; classify requirements as presentation or protection; and review an output for unnecessary personal details. | 12.7 | 12.9 scenario S1; oral |
| LO8 | Redesign a misleading map into an operational map and a management map from one checked dataset; critique an unfamiliar map; and complete the integrated Phase 1 practical with an oral explanation. | 12.8, 12.9 | 12.8 lab; 12.9.1–12.9.2 |

## Required materials

- This document, a text editor, a spreadsheet or plain table for the classification and rate arithmetic, and graph paper (or any drawing tool) for the sketches in 12.3–12.6.
- The **Chapter 12 fixtures**, printed in full in the fixture section and in 12.8.3 as CSV text with WKT geometry (planar) and in 12.9.2 (Earth-referenced). The instructor builds the packages by the routes used in Chapter 3 (Instructor Appendix I.5 of that chapter) and Chapter 6 (6.8.3) and issues both the built package and the text. Every expected value can be checked from the text alone.
- **Primary route:** ArcGIS Pro 3.x — the Symbology pane (unique values, graduated colors with normalization) [X01] [S37], the Color Vision Deficiency Simulator (View tab, Accessibility group) [X03], a layout with legend and scale bar [X07] [X06] [X08]. No extension is needed for anything in this chapter; the cited pages list no licence-level restriction for these functions (**Verification item:** confirm in the installed release). **Optional web route:** ArcGIS Online Map Viewer styles (Types, Counts and Amounts) [A01] [A02] and pop-up configuration [A03]; an organisational account is needed for this route only. **Alternative route:** QGIS Desktop 3.44 — categorized and graduated renderers [Q02], Preview mode (View menu) [Q01], and the print layout.

## Recap of the preceding chapter

Chapter 11 made new things from data: the polygon within 300 m of Road R1, the part of a road inside a ward, the pieces where two layers overlap, one polygon per zone, and — the result this chapter reuses most — a **count per ward** from a spatial join under Policy W-1, with zero-count wards kept and unmatched requests listed. It insisted that a per-ward sum larger than the number of requests must be explained by double matches, that a NoData cell is not a zero, that a threshold mask is not a risk model, and that every output is documented with tool, version, parameters, CRS, units, and limitations. Its closing line was that Chapter 12 would teach how to *communicate* the checked result — "classification, normalisation, and honest uncertainty — without concealing the limitations recorded here." That is this chapter.

One habit from Chapter 11 carries over unchanged: **predict, then look.** Before you style a map, write down what the reader should conclude. After you style it, ask whether a reader would conclude that — and *only* that.

## The recurring scenario and the Chapter 12 fixtures

The fictional municipality continues: **assets**, **roads**, **wards**, **requests**, **inspections**. Two kinds of fixture appear and, as always, are never mixed in one map:

1. **The planar training grid** — flat, metre-based, x to the right, y upward, **no Earth location and no coordinate-system identifier**. This chapter adds a larger district to it (Fixture F12-1, sixteen wards) because classification needs more than four values to be interesting, and a small request sample (Fixture F12-2) for the operational map. Used in 12.2–12.8.
2. **Fixture E12** — Earth-referenced, for the integrated Phase 1 practical in 12.9.2. Wards from Chapter 6's E6 (EPSG:4326), a new road and new requests in EPSG:32643, with documented defects. Defined in 12.9.2, not here, so that learners meet it fresh.

### Fixture F12-1 — District G-12: sixteen wards with a per-ward summary (synthetic, planar)

**Geometry.** A 4 × 4 grid of 1 km squares on the training grid, x from 0 to 4,000 m, y from 0 to 4,000 m. Wards are numbered row by row from the *top* (largest y) left: W01 is the square (0, 3000)–(1000, 4000), W04 is (3000, 3000)–(4000, 4000), W05 starts the next row down at (0, 2000)–(1000, 3000), and W16 is the bottom-right square (3000, 0)–(4000, 1000). Every ward has planar area exactly 1,000,000 m² (1 km²) — deliberately equal, so that in this chapter *area* never explains a difference in counts; population and road length do.

**Attributes.** The summary below is the kind of table Chapter 11 produced: open service requests per ward as of the synthetic date 2026-09-15, counted under Policy W-1 (a request on a shared edge goes to the ward with the lower code), plus two candidate denominators. Ward W16's count is **NULL** because — in the story — its request export had not been received by the count date; that is *missing*, not zero. Ward W03's count is **0**: surveyed, no open requests. Keeping those two cases distinct is the point of 12.6.2.

**Table F12-1 (synthetic; hand-checked totals).**

| `ward` | Square (x range, y range) m | `open_requests` | `households` | `road_km` | Requests per 1,000 households | Requests per km of road |
| --- | --- | --- | --- | --- | --- | --- |
| W01 | 0–1000, 3000–4000 | 3 | 1,500 | 5 | 2.0 | 0.60 |
| W02 | 1000–2000, 3000–4000 | 2 | 1,000 | 4 | 2.0 | 0.50 |
| W03 | 2000–3000, 3000–4000 | 0 | 500 | 2 | 0.0 | 0.00 |
| W04 | 3000–4000, 3000–4000 | 4 | 800 | 4 | 5.0 | 1.00 |
| W05 | 0–1000, 2000–3000 | 5 | 2,000 | 6 | 2.5 | 0.83 |
| W06 | 1000–2000, 2000–3000 | 6 | 1,500 | 3 | 4.0 | 2.00 |
| W07 | 2000–3000, 2000–3000 | 7 | 3,500 | 7 | 2.0 | 1.00 |
| W08 | 3000–4000, 2000–3000 | 13 | 2,600 | 5 | 5.0 | 2.60 |
| W09 | 0–1000, 1000–2000 | 14 | 7,000 | 10 | 2.0 | 1.40 |
| W10 | 1000–2000, 1000–2000 | 16 | 4,000 | 8 | 4.0 | 2.00 |
| W11 | 2000–3000, 1000–2000 | 17 | 1,700 | 4 | 10.0 | 4.25 |
| W12 | 3000–4000, 1000–2000 | 31 | 2,000 | 20 | 15.5 | 1.55 |
| W13 | 0–1000, 0–1000 | 35 | 7,000 | 12 | 5.0 | 2.92 |
| W14 | 1000–2000, 0–1000 | 40 | 10,000 | 15 | 4.0 | 2.67 |
| W15 | 2000–3000, 0–1000 | 45 | 9,000 | 10 | 5.0 | 4.50 |
| W16 | 3000–4000, 0–1000 | NULL (export not received) | 1,000 | 3 | NULL | NULL |
| **Total** | 16 km² | **238** over 15 wards; W16 unknown | **55,100** | **118** | — | — |

Hand-checks: 3 + 2 + 0 + 4 + 5 + 6 + 7 + 13 + 14 + 16 + 17 + 31 + 35 + 40 + 45 = 238; households sum to 55,100; road kilometres to 118. Rates are `open_requests ÷ households × 1000` and `open_requests ÷ road_km`, rounded to one or two decimals as shown; the two-decimal per-km figures for W13 and W14 are 2.9167 and 2.6667 before rounding. Three different "worst ward" answers hide in this one table — by count (W15), by households (W12), by road length (W15, then W11) — and 12.5 is built on that.

**Provenance line supplied with the table (synthetic; data, not instructions):** "Open requests counted from the request tracker on 2026-09-15 by spatial join to the 2024 ward layer under Policy W-1. W16 export pending. Households: 2024 synthetic register. Road length: sum of centreline lengths clipped to each ward (Chapter 11 clip), rounded to whole km."

### Fixture F12-2 — Request sample for wards W06 and W07, with Station Road (synthetic, planar)

The operational map in the lab needs point features. F12-2 is the *complete* list of requests in wards W06 and W07 at the count date — 13 open, matching the 6 + 7 in Table F12-1, plus 3 closed ones that a management count excludes but a crew map may show.

**Road.** `Station Road`, a finite segment from (1000, 2500) to (3000, 2500) — 2,000 m, running through the middle of W06 and W07.

**Table F12-2 (synthetic).**

| `request_id` | (x, y) m | `category` | `priority` (1 = highest) | `status` | `reported_on` | `reporter_ref` | `reporter_note` | Ward (Policy W-1) |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| G01 | (1100, 2100) | Pothole | 2 | OPEN | 2026-09-02 | R-2201 | — | W06 |
| G02 | (1200, 2700) | Streetlight out | 3 | OPEN | 2026-09-04 | R-2202 | "Second lamp from the corner" | W06 |
| G03 | (1210, 2695) | Streetlight out | 3 | OPEN | 2026-09-04 | R-2202 | "Same street, next lamp" | W06 |
| G04 | (1500, 2480) | Blocked drain | 1 | OPEN | 2026-09-10 | R-2203 | "Water on the road after rain" | W06 |
| G05 | (1850, 2950) | Pothole | 2 | OPEN | 2026-08-28 | R-2204 | — | W06 |
| G06 | (2000, 2400) | Fallen tree | 1 | OPEN | 2026-09-12 | R-2205 | — | on the W06/W07 edge → W06 (tie-break) |
| G07 | (2100, 2900) | Pothole | 3 | OPEN | 2026-08-30 | R-2206 | — | W07 |
| G08 | (2300, 2550) | Streetlight out | 3 | OPEN | 2026-09-01 | R-2207 | "Reporter says her father, 82, walks here at night — house no. 14" | W07 |
| G09 | (2500, 2200) | Blocked drain | 1 | OPEN | 2026-09-11 | R-2208 | — | W07 |
| G10 | (2600, 2500) | Pothole | 2 | OPEN | 2026-09-06 | R-2209 | — | W07 (on Station Road) |
| G11 | (2750, 2750) | Water leak | 1 | OPEN | 2026-09-13 | R-2210 | — | W07 |
| G12 | (2900, 2100) | Streetlight out | 3 | OPEN | 2026-08-26 | R-2211 | — | W07 |
| G13 | (2950, 2950) | Pothole | 2 | OPEN | 2026-09-08 | R-2212 | — | W07 |
| G14 | (1400, 2200) | Pothole | 2 | CLOSED | 2026-08-10 | R-2213 | — | W06 |
| G15 | (2200, 2300) | Water leak | 1 | CLOSED | 2026-08-14 | R-2214 | — | W07 |
| G16 | (2850, 2600) | Blocked drain | 1 | CLOSED | 2026-08-20 | R-2215 | — | W07 |

Points to notice (all hand-checked): G02 and G03 are √(10² + 5²) = 11.18 m apart — two lamps on one street, reported by the same person; at any ordinary map scale their symbols will overlap (12.3.2). G06 lies exactly on x = 2000, the shared W06/W07 edge, and is assigned to W06 by Policy W-1; W06's open count of 6 in Table F12-1 *includes* it and W07's 7 *excludes* it — the table and the points agree only because the policy is stated. G10 sits exactly on Station Road. `reporter_ref` is an opaque reference (Chapter 9); `reporter_note` is free text and one note (G08) contains personal detail that has no place on a published map (12.7.3). `priority` is an *ordered* code, not a category — that matters in 12.3.1.

**Figure 12.0 — The Chapter 12 planar fixtures (schematic; described).** Draw a 4 × 4 grid of squares, 1 km each, and write the ward codes W01–W04 across the top row, W05–W08 across the second, W09–W12 the third, W13–W16 the bottom. Write each ward's `open_requests` in its square, with "0" in W03 and "?" in W16. Then, inside W06 and W07 (second row, second and third squares), draw Station Road as a horizontal line at y = 2500 from x = 1000 to x = 3000 and plot G01–G16 with the closed ones hollow. Label the drawing "training grid, metres, no CRS, y upward — not north".

---

## 12.1 Start with audience and map purpose

### 12.1.1 Two maps, two decisions: an operations map and a management map

A map is made *for* someone who has to decide something. Before any symbol is chosen, write the reader and the decision in one sentence each. The recurring scenario gives two readers who look at the same requests and need opposite things.

**The operations map.** Reader: a road crew supervisor in wards W06–W07 on Monday morning. Decision: *which open requests do we visit today, in what order, and where exactly are they?* The supervisor needs every open request as an individual point at its true position, its category and priority readable at a glance, the road network to plan a route, and nothing about last month's totals. A closed request is noise unless the crew must avoid re-visiting it. The unit of interest is **the individual request** and the map must locate it.

**The management map.** Reader: the ward services manager preparing the monthly review. Decision: *which wards need more crew capacity next quarter, and is that fair to say from the numbers?* The manager does not need any individual point; a hundred dots would hide the pattern. The manager needs a per-ward figure that supports a *comparison between wards*, which means a number that has been made comparable (12.5), classes that are disclosed (12.4), and a clear statement of what the figure does not prove. The unit of interest is **the ward**.

**Table 12.1-A — The same data, two purposes (synthetic scenario).**

| Design question | Operations map (crew) | Management map (review) |
| --- | --- | --- |
| The decision | Where do we go today, in what order? | Where should capacity go next quarter? |
| Unit shown | Individual request points (F12-2) | Wards with one value each (F12-1) |
| Essential attributes | `category`, `priority`, `status = OPEN`, `request_id` for the job sheet | `open_requests` **and a denominator**; the count date; W16 = not received |
| Supporting context | Roads (routing), ward outlines (crew boundaries) | Ward outlines and codes; nothing else |
| Wrong things to add | Household totals, monthly trends, closed requests from last year | Every point; asset inventory; the basemap's shop names |
| A conclusion the map must *not* invite | "The pothole at G10 is worse than the drain at G04" (priority says otherwise) | "W15 is the worst-run ward" (a count is not performance — 12.5.3) |

**Developer analogy — a dashboard versus a work queue.** A work queue (a ticket list sorted by priority with locations) and a KPI dashboard (tickets per team per month) are built from the same table and are both "correct"; putting a monthly KPI tile on the technician's phone screen, or the raw ticket list on the executive dashboard, makes each unusable. Maps are the same. **Where the analogy stops:** a dashboard tile that shows a wrong aggregate is usually caught by someone checking the number; a map that *looks* convincing is often believed without the number being checked at all. Maps carry more unearned authority than tables, so the design discipline has to be stricter, not looser.

**Misconception:** "One good map can serve everyone — just put everything on it and let readers pick." *Consequence:* the crew cannot find the point they need under the ward shading, the manager reads dot density as workload, and both readers draw a conclusion the map's author never checked. A map with two purposes has none.

**Comprehension check 12.1.** A councillor asks for "a map of the streetlight situation in W11". Write (a) two *different* decisions this could be for, (b) the unit of interest for each, and (c) one layer that belongs on one map and not on the other.

### 12.1.2 Medium, viewing size, interaction, and required detail

The reader's *device* decides as much as the reader's purpose. Write these four down with the purpose:

1. **Medium.** Printed page, projector slide, desktop web map, phone screen, or a PDF that will be printed on an office printer in black and white. The last case is common and is why 12.3.3 asks for a grayscale check.
2. **Viewing size.** An A4 sheet is 210 mm × 297 mm; a phone is roughly 70 mm wide; a projected slide is read from metres away. The same 6-point label is fine on paper, invisible on a projector, and unreadable on a phone. Text and symbol sizes must be chosen for the size at which the map is *read*, not the size at which it is *designed*.
3. **Interaction.** A printed map cannot be zoomed, filtered, or clicked, so everything the reader needs must be visible at once and the legend must be complete. An interactive web map can hide detail behind zoom levels and pop-ups — which means the *default* view must still be honest, because many readers never zoom or click. ArcGIS Pro's layout page states the design consequence for print directly: "What you see on the layout is what you will get when you print or export the map to the same page size" [X08] — the layout *is* the reader's view.
4. **Required detail.** The operations map needs every open request at its position; the management map needs sixteen numbers. Detail beyond what the decision needs is not "extra information"; it is competition for the reader's attention.

**A concrete size check (hand-checked).** District G-12 is 4 km wide. On an A4 page with a 160 mm-wide map frame, 4,000 m ÷ 0.160 m = 25,000, so the natural print scale is **1:25,000** — 1 mm on paper is 25 m on the ground. At that scale a 10 m-wide road is 0.4 mm wide on paper, thinner than a printed line, so the road *must* be drawn as a symbolic line, not to scale. The W06–W07 operations map is 2 km wide; on a 70 mm phone screen that is about 1:28,600 when the whole area is shown, and the crew will zoom in to about 1:2,000 (70 mm ≈ 140 m) to see one street. The phone map therefore needs symbols that stay readable across a range of scales, and labels that appear only when there is room (12.2.3 on scale ranges). The printed map needs one scale and one set of sizes.

> **Platform note — on-screen scale is nominal.** A printed 1:25,000 is exact: the paper does not change size. An on-screen "1:25,000" depends on an assumed physical pixel size that the software cannot know for every monitor and phone; treat the number shown in a map view's scale box as nominal and the scale bar drawn in the same pixels as self-consistent. ArcGIS Pro's scale-bar page adds a further caution that applies to any map: "Map scales are often averages and can vary based on coordinate system, latitude, direction, and map extent" [X06]. Chapter 6 explained why.

**Misconception:** "Design on the big monitor, then export — the map will look the same." *Consequence:* labels that were comfortable at 27 inches collapse into grey smudges on a phone; a legend with twelve classes becomes unreadable on a projector. Design *at* the reading size, or check at it before publishing.

**Comprehension check 12.1.2.** The management map will be shown for thirty seconds on a projector in a large room, then attached to the minutes as a PDF. List two design decisions that differ between those two uses of the *same* map, and one that must be identical.

### 12.1.3 What can be removed without weakening the decision?

Every layer, label, colour, and number on the map should survive the question: *if this were removed, would the reader's decision get worse?* If not, remove it. Work through the operations map for W06–W07 with the layers the municipality actually has:

| Candidate layer | Keep for the crew map? | Reason |
| --- | --- | --- |
| Open requests (13 points) | **Yes** — the subject | The decision is about them |
| Closed requests (3 points) | Usually **no**; if kept, subdued and hollow | They are not jobs; showing them risks a wasted visit; some crews want them to avoid duplicates — ask |
| Station Road and other roads | **Yes**, subdued | Routing context |
| Ward outlines W06/W07 | **Yes**, thin grey | Crew boundary; the G06 edge case must be visible |
| Ward `open_requests` shading | **No** | A ward total tells the crew nothing about where to go and competes with the points |
| All assets (streetlights, drains, trees) | **No** by default | Hundreds of symbols; the request already names the asset. Show *only* the asset referenced by a request if the crew needs it |
| Inspections table | **No** | Not spatial for this decision |
| Basemap with every shop name | Replace with a light, low-contrast basemap or none | Names compete with request labels |
| Reporter notes as labels | **No** | Personal details (12.7.3) and clutter |

Removal is not loss of rigour. The full dataset remains in the package and in the analysis log (Chapter 11, 11.8.3); the map shows the *decision-relevant subset* and says so in its notes (12.6). The blueprint's instruction is exact: avoid filling the map with every available layer.

**Misconception:** "Removing layers hides information from the reader." *Consequence:* a defensive author keeps everything, the reader cannot find the decision-relevant part, and the *effective* information delivered is lower than on the sparse map. Hiding data is a *governance* question (12.7) and is decided by access rules, not by what is drawn.

**Comprehension check 12.1.3.** For the *management* map of F12-1, decide keep/remove for: ward outlines; ward codes as labels; Station Road; the 16 request points of F12-2; a label showing each ward's `households`. Give one reason each.

## 12.2 Explain scale, detail, resolution, and accuracy

### 12.2.1 Representative fraction; large scale versus small scale

**Definition — map scale.** The ratio between a distance on the map and the corresponding distance on the ground, in the same units. Written as a **representative fraction (RF)** such as **1:25,000**: one unit on the map is 25,000 of the same units on the ground; the QGIS introduction puts it as "any distance on the map is 1/25,000th of the real distance" and calls 25,000 the scale denominator [S35]. ArcGIS Pro defines it the same way — "Scale is a ratio between measurements on a map view and measurements in the real world" — and accepts it typed as `1:1,000` or as a verbal scale such as `1 cm = 1.5 km` [X04].

Three ways of writing the same scale, all legitimate [S35]:

| Form | Example | When it is most useful |
| --- | --- | --- |
| Representative fraction | 1:25,000 | Unit-free; survives translation; useless once the map is resized |
| Verbal (word) scale | "1 cm on the map is 250 m on the ground" | Readers who do not want to divide |
| Graphic scale bar | a bar labelled 0 — 500 m — 1 km | The only form that stays correct when the map is enlarged or shrunk, because the bar shrinks with it |

**Checked ratios (hand-checked).** The blueprint asks that the terminology be taught with ratios you can verify:

- At **1:1,000**, 1 cm on the map = 1,000 cm = **10 m** on the ground. A 20 cm × 20 cm map area covers 200 m × 200 m = 0.04 km².
- At **1:100,000**, 1 cm = 100,000 cm = **1 km**. The same 20 cm × 20 cm covers 20 km × 20 km = 400 km².
- At **1:25,000** (our A4 print of G-12), 1 cm = 250 m; 16 cm = 4 km, exactly the district's width. The QGIS page's own check is the same arithmetic: "100 mm x 25,000 = 2,500,000 mm", i.e. 2.5 km [S35].

So for a comparable physical map area, **1:1,000 shows more local detail than 1:100,000** — ten thousand times less ground per page, therefore room for individual lamps, drains, and building outlines instead of whole neighbourhoods.

**Large scale versus small scale.** This vocabulary trips up everyone, because it runs against everyday language. The scale is the *fraction*: 1/1,000 is a *larger number* than 1/100,000. So:

- **Large-scale map** = large fraction = **small denominator** = small ground area = **much detail** (1:1,000; a street plan).
- **Small-scale map** = small fraction = **large denominator** = large ground area = little detail (1:1,000,000; a country map).

The QGIS introduction states the consequence with an exclamation mark: "a small scale map covers a large area, and a large scale map covers a small area!" and lists 1:1,000,000 as small scale and 1:50,000 as large scale [S35]. A memory aid that works for developers: *scale is a zoom factor written as a fraction; "large scale" means zoomed in.*

> **Instructor note — a wording to watch in [S35].** The same QGIS page says "the lower the map scale, the more detailed the feature information in the map will be." Read with the page's figures, "lower" means the lower *denominator* (1:25,000 is "lower" than 1:50,000), which is the *larger* scale. The sentence is easy to misread as its opposite. This chapter uses only the unambiguous forms — *larger scale / smaller denominator / more detail* — and the checked ratios above. This is a wording ambiguity, not a factual error in the blueprint.

**Developer analogy — scale is a zoom level with the numbers inverted.** Web maps count zoom levels upward as you zoom *in* (zoom 18 is a street, zoom 3 is a continent). Cartographic scale does the same job but as a fraction, so the number you see gets *smaller* as you zoom in (1:1,000 at the street, 1:50,000,000 at the continent). **Where the analogy stops:** a web zoom level is a fixed tiling scheme; a scale denominator is a continuous ratio that also depends on the projection and the latitude (Chapter 6) — two web maps at "zoom 12" have the same tiles, while two maps "at 1:25,000" in different projections can differ in true ground scale across the page.

**Misconception:** "A 'large-scale study' in the project brief means a large area." *Consequence:* the team orders 1:250,000 mapping for a "large-scale" street-lighting audit and receives a map where a whole ward is a few millimetres. In GIS writing, say **large area** or **small denominator** if there is any doubt, and always write the ratio.

**Comprehension check 12.2.1.** A printed map of G-12 is exactly 8 cm wide for the 4 km district. (a) What is its RF? (b) Is it larger-scale or smaller-scale than the 1:25,000 A4 version? (c) How wide, in millimetres, would a 12 m-wide road be if drawn to scale on it?

### 12.2.2 Scale, cell size, coordinate precision, and positional accuracy are four different things

Four numbers that all "sound like accuracy" appear in every GIS project, and Chapters 4, 5, and 9 introduced them one at a time. The recap table puts them together, with the fixture values used in this course.

**Table 12.2-B — Four quantities that are not each other (recap; fixture values are synthetic).**

| Quantity | What it describes | Belongs to | Course example | Changing it changes… | Does it tell you how close a drawn position is to the truth? |
| --- | --- | --- | --- | --- | --- |
| **Map scale** (12.2.1) | The ratio between map distance and ground distance in one *rendering* | The map or layout, not the data | 1:25,000 for the A4 print of G-12 | how much ground fits on the page and how much detail can be *drawn* | **No.** Any dataset can be drawn at any scale |
| **Raster cell size / resolution** (Chapter 4, 4.3.1) | The ground size of one cell; the smallest thing a raster can represent | The raster dataset | 100 m cells in `elevation_training.asc` (Fixture F6) | what can exist in the grid and how much storage it costs | **No.** "Cell size is not positional accuracy" (4.3.1); a 1 m grid can be misplaced by 50 m |
| **Coordinate precision** (Chapter 5, 5.2.2) | How finely a coordinate value is *written* | The number in the record | 4 decimal places of a degree ≈ 11 m; RFC 7946 notes that six decimals is about 10 cm and warns against more precision than necessary (Chapter 5, [S18 §11.2]) | how many digits are stored, nothing else | **No.** Extra digits are free; extra truth is not |
| **Positional accuracy** (Chapter 9, 9.2–9.3) | How close the recorded position is to the true position | The *capture method* in its environment, then the dataset via provenance | The georeferencing check point with a 12 m error (9.3.2); consumer GNSS versus surveyed entrance (9.2.3) | what the data can be trusted for | **Yes — and it is the only one that does** |

The four are independent. A map at 1:1,000 (large scale, lots of detail) can display a point recorded to eight decimals (high precision) captured from a 100 m raster (coarse resolution) by an address match (accuracy unknown). Each number is honest about its own quantity and silent about the others.

**Worked example.** *Question:* a manager, looking at the crew map zoomed to 1:500, asks "the drain at G04 is drawn 20 m from the road — is that right, or is the point wrong?" *Inputs:* G04 at (1500, 2480); Station Road at y = 2500; the request's provenance (Chapter 9 style): "location from the reporter's phone, app-reported accuracy ± 15 m". *Reasoning:* the drawn 20 m is planar arithmetic on the stored coordinates (2500 − 2480 = 20 m) and is *exact for the stored numbers*. Whether the drain is really 20 m from the road depends on the ± 15 m capture accuracy, not on the map scale (1:500 changes only how big 20 m looks) and not on the coordinate's decimals. *Answer:* "The stored point is 20 m south of the road centreline; the capture accuracy is about ± 15 m, so the drain may be anywhere from about 5 m to 35 m from the centreline, and the map cannot tell you which." *What this does not establish:* whether the reporter stood at the drain or on the far pavement — only a site visit or a surveyed asset position would.

**Developer analogy — font size, `DECIMAL(12,6)`, and measurement error.** Rendering a number in 48-point type (scale) does not change it; storing it as `DECIMAL(12,6)` (precision) does not make the sensor better; the sensor's error (accuracy) is a property of the sensor. Cell size is like a sampling interval: a temperature logged every 10 minutes cannot tell you about a 30-second spike. **Where the analogy stops:** in software the four quantities are usually recorded in four obvious places (CSS, schema, sensor spec, sampling config); in GIS they are often *not recorded at all*, and a map shows none of them — which is why 12.6 asks you to write them on it.

**Misconception:** "The layer draws crisply at 1:500, so it must be accurate to a metre." *Consequence:* a crew is sent to dig at a drawn position with ± 15 m capture accuracy; a report claims sub-metre accuracy from a dataset whose provenance says "address-matched". Crispness is a property of the renderer.

**Comprehension check 12.2.2.** For each statement, name which of the four quantities it is about and whether it changes the trustworthiness of a drawn position: (a) "we re-exported the file with nine decimals"; (b) "the DEM was resampled from 30 m to 10 m cells"; (c) "the print was enlarged from A4 to A3"; (d) "the points were re-captured with a survey-grade receiver".

### 12.2.3 Zooming in and adding decimals do not improve evidence; generalisation and visibility must suit the use

**Zooming in adds nothing.** An interactive map lets the reader zoom to 1:100, where a request point with ± 15 m capture accuracy is drawn as a crisp dot on a particular doorstep. The renderer has *invented* that doorstep: the evidence behind the dot is the same ± 15 m blob it was at 1:25,000. Zooming increases the *scale* (12.2.1); it cannot increase *positional accuracy*, which was fixed at capture (Table 12.2-B). Adding decimal places does the same thing numerically: `1500.000000` is not a better observation than `1500`.

**Generalisation.** Because a map at 1:25,000 cannot show a 10 m road at its true width, or two lamps 11 m apart as separate dots, the cartographer *simplifies*: roads become lines of a fixed width, close points become one symbol or a cluster, small bends are smoothed, small polygons are dropped. This is **generalisation** — a deliberate reduction of detail to fit the scale and the purpose. The QGIS vector-data page made the underlying point in Chapter 3: the same real object is a point at one scale and a polygon at another [S10]. Generalisation is not error; *undisclosed* generalisation is.

**Visibility by scale (scale ranges).** Interactive maps handle the phone-versus-street problem by showing a layer only within a scale range. ArcGIS Pro's page states the purpose: "By setting a visible scale range for a layer, you automatically limit its visibility to suitable scales only", with the examples that one would not draw building footprints on a map of Europe nor generalised climate zones on a neighbourhood map [X05]. The setting lives on the layer (Feature Layer tab, Visibility Range group, or Layer Properties ▸ General) [X05]. For the crew map: request labels visible only when zoomed in past roughly 1:5,000; ward outlines always; the asset layer (if used at all) only past 1:2,000. The *default* view must still be complete and honest: a reader who never zooms must not miss a whole category.

> **Platform note.** Scale-dependent visibility exists in every platform on the transferable list (QGIS layer "scale dependent visibility", OpenLayers and Mapbox layer min/max zoom, GeoServer style rules with scale denominators), but the *numbers* are not interchangeable: a web zoom level is a tile scheme, an ArcGIS Pro scale range is a denominator, and a GeoServer rule uses a scale denominator computed for the map's projection. Record the setting per platform when you port a map; do not assume "zoom 14" and "1:35,000" mean the same thing everywhere.

**Worked example — the cluster on the crew map.** *Question:* on the whole-area view (about 1:28,600 on a phone), G02 and G03 (11.18 m apart) are drawn with 12-point symbols. *Reasoning:* 12 pt = 12 × 0.3528 mm = 4.23 mm on screen; at 1:28,600 that is about 121 m on the ground, eleven times the distance between the two lamps. *Outcome:* one blob; the crew sees one job where there are two. *Design responses:* (a) at the whole-area scale show a count label "2" or a cluster symbol and state it in the legend; (b) let the two symbols separate when zoomed past about 1:2,500 (4.23 mm ≈ 10.6 m at 1:2,500 — still just overlapping; at 1:2,000, 8.5 m, they separate); (c) always list the job IDs in the job sheet so that the map is not the only source. *What this does not establish:* that two symbols at 1:2,000 are two lamps 11 m apart in reality — it establishes that the *stored* positions are 11.18 m apart.

**Misconception:** "More decimals and a bigger zoom make the map more precise, and precision is what the manager asked for." *Consequence:* the report shows lamp positions to the millimetre from a phone capture; a reviewer who knows the capture method dismisses the whole report. Precision beyond accuracy is a red flag to an informed reader, not a feature.

**Comprehension check 12.2.3.** The councillor zooms the web map to 1:200 and says "the fallen tree G06 is clearly on the W07 side of the line". G06 is stored at exactly x = 2000 and captured with ± 15 m accuracy. Write the two-sentence reply.

## 12.3 Match symbols to the data

### 12.3.1 Categories are not magnitudes: unique symbols, graduated colour, graduated size

Every attribute you might show belongs to one of a few *measurement types*, and the symbol must respect the type — otherwise the map asserts an order or a quantity that the data does not have.

| Measurement type | Meaning | F12 examples | Honest visual encoding | Dishonest encoding |
| --- | --- | --- | --- | --- |
| **Nominal (category)** | Values are names; no order, no arithmetic | `category` (Pothole, Streetlight out, …); `status`; `ward` code | **Unique symbols**: distinct hues or shapes, one per value | A light-to-dark ramp — it implies Pothole < Drain |
| **Ordinal (ordered)** | Values have an order but no meaningful distance | `priority` 1, 2, 3 | Ordered shades or sizes *with the order stated in the legend* | Unrelated hues — the order disappears |
| **Quantitative (magnitude)** | Numbers with meaningful differences | `open_requests`, `households`, rates | **Graduated colour** (classes of a sequential ramp) or **graduated/proportional size** | One hue per value — 16 unrelated colours for 16 counts |

The QGIS attribute-data page draws exactly this line: unique-value symbology is for when "the attributes of features are not numeric, but instead strings are used" (its example is road types), while graduated symbols are "most useful when you want to show clear differences between features with attribute values in different value ranges", and continuous colour builds shades between a start and an end colour for a numeric attribute [S23]. ArcGIS Pro's symbology overview lists the same families: **Unique values** apply "a different symbol to each category of features"; **Graduated colors** "show quantitative differences in feature values with a range of colors"; **Graduated symbols** show them "with varying symbol sizes"; **Proportional** symbology represents values "as a series of unclassed, proportionally sized symbols"; **Unclassed colors** use a ramp "not broken into discrete classes" [X01]. Its unique-values page describes the intended data as "qualitative categories" with examples such as planning zones and soil classifications [X02]. ArcGIS Online Map Viewer names the same choice as **Types (unique symbols)** for a category attribute and **Counts and Amounts (color)** or **(size)** for a numeric attribute [A01].

**Colour or size for a magnitude?** Both are honest; they suit different geometry. For *polygons* (wards), colour fill is the natural choice and produces the choropleth of 12.5. For *points* (requests), size is often better than colour because a small dot's colour is hard to read; a graduated-size dot also does not fill an area that the value does not describe. For *lines* (roads by traffic), width is the size channel.

**Worked example — three fields, three encodings.** *Question:* style the crew map's 13 open requests so that a supervisor sees *what* each job is and *how urgent* it is. *Inputs:* F12-2 fields `category` (nominal, 5 values) and `priority` (ordinal, 1–3). *Reasoning:* `category` gets unique **shapes** (a circle for Pothole, a square for Streetlight out, a triangle for Blocked drain, a diamond for Fallen tree, a star for Water leak — shapes survive grayscale, 12.3.3); `priority` gets **size** with three steps and the legend text "1 = highest priority" so that the order is stated, not assumed. Colour is left free for `status` if closed requests are shown (hollow versus filled). *Outcome:* G04 (Blocked drain, priority 1) is a large triangle; G07 (Pothole, priority 3) is a small circle. *Check:* every legend entry can be read without colour; the priority order is written. *What this does not establish:* that priority 1 jobs are "three times" priority 3 — ordinal data has no ratio, and the legend must not say "3× more urgent".

**Developer analogy — types in a schema.** You would not sort an enum by its string, average a status code, or compare two UUIDs with `<`. Nominal, ordinal, and quantitative are the *types* of a map variable, and a ramp is an operation defined only on ordered types. **Where the analogy stops:** a compiler refuses `enum < enum`; every GIS will happily draw a category field with a graduated ramp and never warn you. The type check is yours.

**Misconception:** "Use a red-to-green ramp for status — red is bad, green is done." *Consequence:* status is nominal (Open, In progress, Reopened, Closed – duplicate); a ramp forces an order that the values do not have (is "Reopened" more or less than "In progress"?), and red/green is the pair most often indistinguishable to colour-deficient readers (12.3.3). Use distinct hues *and* shapes or labels.

**Comprehension check 12.3.1.** For each field say nominal, ordinal, or quantitative, and name an honest encoding: (a) `households`; (b) `ward` code; (c) a `condition` score 1–5 from Chapter 8; (d) `reported_on` date; (e) `asset_type_gu` (Gujarati asset-type name, Chapter 7).

### 12.3.2 Visual hierarchy: foreground, context, subdued basemap — and the oversized symbol

A reader's eye goes to whatever has the most contrast, size, and saturation. **Visual hierarchy** is arranging the map so that the eye lands, in order, on (1) the operational data the decision is about, (2) the supporting context that helps interpret it, and (3) the basemap, which should be felt rather than read.

| Level | Content on the crew map | Treatment |
| --- | --- | --- |
| Foreground | Open requests (F12-2) | Saturated fills, largest symbols, labelled with `request_id` when there is room |
| Context | Station Road; W06/W07 outlines and codes | Mid-grey line; thin grey outline; small grey ward labels |
| Basemap | Streets, building blocks | Light grey, low contrast, few or no names; or none at all for the printed job sheet |

ArcGIS Pro describes the basemap's role as "a reference map on which you overlay data from layers", and notes that in a custom basemap "Reference layers draw on top of operational layers, while background layers draw below operational layers" [X10] — that is, the platform already distinguishes *background* (below your data), *operational* (your data), and *reference* (labels drawn above). The basemap gives context; it is not the message.

**The oversized symbol.** Symbols have a physical size on the page; positions have a ground precision. When the symbol's ground footprint at the map's scale is much larger than the accuracy of the position, the symbol *conceals the location it is meant to show*. Hand-checked on the A4 print at 1:25,000:

| Symbol size | Size on paper | Ground footprint at 1:25,000 | Effect on F12-2 |
| --- | --- | --- | --- |
| 6 pt | 2.12 mm | **53 m** | G02/G03 (11 m apart) merge; G10 "on the road" covers the road and 26 m either side |
| 12 pt | 4.23 mm | **106 m** | G04 (20 m from the road) and G10 (on it) look the same; the G06 edge case straddles both wards |
| 24 pt | 8.47 mm | **212 m** | One symbol covers a fifth of a ward's width |

(1 pt = 1/72 inch = 0.3528 mm; footprint = paper size × 25,000.) The lesson is not "use tiny symbols" — a 2 pt dot is invisible from a metre away — but that at small scales a point symbol is a *label of a location*, not a depiction of it, and any question of "which side of the line" must be answered from the coordinates (Chapter 10), not from the drawing. State the symbol's footprint in the notes when it matters; on the management map, use no point symbols at all.

**Developer analogy — z-index and contrast in a UI.** Primary actions get the saturated button; secondary ones get an outline; the page background is neutral. A map's foreground/context/basemap is the same three-tier system. **Where the analogy stops:** in a UI the *position* of a button carries no meaning; on a map the position *is* the data, so an oversized "button" destroys information a UI would not lose.

**Misconception:** "Bigger symbols are clearer." *Consequence:* the crew map at the whole-area scale shows six overlapping blobs for thirteen jobs; the supervisor counts six. Size must be chosen for the *reading scale* and checked against the data's spacing (the closest pair in F12-2 is 11.18 m).

**Comprehension check 12.3.2.** On the phone at 1:2,000, a 12 pt symbol has what ground footprint? Would G02 and G03 separate? (Show the arithmetic.)

### 12.3.3 Readable labels, distinguishable symbols, and more than colour alone — the grayscale check

Three requirements that the blueprint makes mandatory, and how to check each:

1. **Readable labels.** Labels must be legible at the reading size (12.1.2) and must not cover the symbol they name or each other. On the crew map only the `request_id` (and perhaps the category abbreviation) is labelled; on the management map, only the ward code. If a label cannot be placed without overlap, drop it or shrink the scale range in which it appears — never let the renderer place two labels on top of each other and call it done. (Chapter 3 introduced labels as a *layer* rendering of an attribute; nothing in the data changes when labels change.)
2. **Distinguishable symbols.** Every legend entry must be tellable from every other *on the map*, not just in the legend at 200 % zoom. Two similar blues for "Blocked drain" and "Water leak" fail this at any size. Use shape as well as colour for categories, and never more than about six or seven categories in one symbol set without grouping — ArcGIS Pro's unique-values page notes that "when there are a lot of symbol classes in a layer, it may help to group values together" [X02].
3. **More than colour alone for essential distinctions.** Some readers cannot tell red from green; every reader loses colour on a black-and-white office printer and on a faxed or photocopied page. Each *essential* distinction — open versus closed, priority 1 versus 3, "no data" versus zero — must survive as a shape, a fill pattern, a size, a label, or a legend statement.

**The grayscale check (simple supplementary check).** Render the map without colour and ask whether the decision can still be made. Both desktop products provide this directly:

> **Procedure (version-specific; not execution-tested) — ArcGIS Pro.** With the map or layout view active, on the **View** tab in the **Accessibility** group, open the **Color Vision Simulator** drop-down and choose one of **Protanopia**, **Deuteranopia**, **Tritanopia**, or **Achromatopsia** [X03]. Achromatopsia is the grayscale view: the page describes it as leaving "only monochromatic gray hues functional" [X03]. The simulation applies to maps, scenes, layouts, presentations, and reports, and is *not* included when exporting to a file or sharing to a web map [X03] — it is a checking view, not a style. Exit with the button at the upper right of the view or by toggling the ribbon button [X03].
>
> **Procedure (version-specific; not execution-tested) — QGIS 3.44.** **View ▸ Preview Mode** offers Normal, Simulate Monochrome, Simulate Achromatopsia Color Blindness (Grayscale), Simulate Protanopia (No Red), Simulate Deuteranopia (No Green), and Simulate Tritanopia (No Blue) [Q01]. Use Grayscale for the check and one of the three colour-deficiency modes for the red/green test.
>
> **Platform note.** Web libraries have no built-in simulator; check a web map by exporting an image and converting it to grayscale, or with browser developer tools. The check is a *design* step, so do it in the authoring tool before publishing.

**Worked example — the check applied.** *Question:* does the crew map survive grayscale? *Inputs:* the 12.3.1 design — shapes for category, size for priority, filled/hollow for status. *Reasoning:* in grayscale, shapes and sizes are unchanged; filled versus hollow is unchanged; the only colour-only distinction was the *hue* of each category, which was redundant with shape. *Outcome:* passes. Now the alternative design "five colours, all circles, same size": in grayscale five circles become three or four similar greys; Pothole and Water leak are indistinguishable. *Fails.* *What the check does not establish:* that the colours are pleasant, or that the label placement is right — it tests one thing.

**Developer analogy — accessibility is a test, not a feature.** You would not ship a form whose only error indication is a red border; you add an icon and a message. The grayscale render is the map's equivalent of the keyboard-only test. **Where the analogy stops:** a UI has an accessibility standard to test against; cartography has conventions and simulators but no single pass/fail rule, so the criterion is the one stated above — *can the reader still make the decision?*

**Misconception:** "The legend explains the colours, so colour alone is fine." *Consequence:* a reader who cannot distinguish the two reds in the legend cannot distinguish them on the map either; the legend does not help. The test is on the map, in grayscale, by someone who has not seen the colour version.

**Comprehension check 12.3.3.** The management map uses a five-class yellow-to-dark-red ramp for `open_requests` and a solid mid-grey for W16 ("no data"). Which class of the ramp is likely to be confused with W16 in grayscale, and what change fixes it?

## 12.4 Teach classification as an analytical choice

### 12.4.1 Equal interval, quantile, and natural breaks on one small table

A choropleth (12.5) puts each ward into a **class** and gives every ward in a class the same colour. The **classification method** decides where the class boundaries — the **breaks** — fall. Three methods are standard; each has a *different objective*, and the choice is an analytical decision, not a styling one. Esri's classification page is the reference for the three [S36]; QGIS offers the same three among its graduated modes as "Equal Interval", "Equal Count (Quantile)", and "Natural Breaks (Jenks)" [Q02]; ArcGIS Online offers them through "Classify data" in the Counts and Amounts styles [A02].

**The table.** The fifteen numeric `open_requests` values of F12-1, sorted (W16 is NULL and is *outside* the classification — 12.6.2):

`0, 2, 3, 4, 5, 6, 7, 13, 14, 16, 17, 31, 35, 40, 45` (W03, W02, W01, W04, W05, W06, W07, W08, W09, W10, W11, W12, W13, W14, W15)

Three classes each time. All arithmetic hand-checked.

**Equal interval** — *objective: classes of equal value width.* Esri: it "divides the range of attribute values into equal-sized subranges" and is best for "familiar data ranges, such as percentages and temperature" [S36]. Range 0 to 45; width 45 ÷ 3 = 15; breaks at 15 and 30.

| Class | Interval | Wards | Count of wards |
| --- | --- | --- | --- |
| 1 | 0 – 15 | W03 (0), W02 (2), W01 (3), W04 (4), W05 (5), W06 (6), W07 (7), W08 (13), W09 (14) | **9** |
| 2 | > 15 – 30 | W10 (16), W11 (17) | **2** |
| 3 | > 30 – 45 | W12 (31), W13 (35), W14 (40), W15 (45) | **4** |

No value falls exactly on 15 or 30, so the boundary convention (upper bound inclusive or not) does not matter here — when it does, state it (12.4.2).

**Quantile** — *objective: the same number of features in each class.* Esri: "Each class contains an equal number of features" [S36]. Fifteen values, three classes, five per class — a clean split, which is why fifteen was chosen.

| Class | Values | Wards | Count of wards |
| --- | --- | --- | --- |
| 1 | 0 – 5 | W03, W02, W01, W04, W05 | **5** |
| 2 | 6 – 16 | W06 (6), W07 (7), W08 (13), W09 (14), W10 (16) | **5** |
| 3 | 17 – 45 | W11 (17), W12 (31), W13 (35), W14 (40), W15 (45) | **5** |

Esri's own warning appears at once: "Similar features can be placed in adjacent classes, or features with widely different values can be put in the same class" [S36]. W10 (16) and W11 (17) differ by one request and are in different classes; W11 (17) and W15 (45) differ by 28 and share a class.

**Natural breaks (Jenks)** — *objective: groups that are internally similar and mutually different.* Esri: "Classes are based on natural groupings inherent in the data … class breaks are identified that best group similar values and that maximize the differences between classes" [S36]. The method searches for the partition with the smallest total within-class squared deviation from each class mean. For fifteen values in three contiguous classes there are 91 possible partitions; the data have three visible clusters, and the cluster partition has the smallest within-class sum of squares of every alternative checked by hand (Instructor Appendix I.3 shows the arithmetic):

| Class | Values | Wards | Count of wards | Class mean | Within-class sum of squares |
| --- | --- | --- | --- | --- | --- |
| 1 | 0 – 7 | W03, W02, W01, W04, W05, W06, W07 | **7** | 3.857 | 34.86 |
| 2 | 13 – 17 | W08, W09, W10, W11 | **4** | 15.0 | 10.00 |
| 3 | 31 – 45 | W12, W13, W14, W15 | **4** | 37.75 | 110.75 |
| | | | | Total | **155.6** |

The next-best partitions tried — moving 7 up, moving 13 down, or moving 31 down — give totals of 195, 223, and 300; the clusters are real in this table.

> **Verification item.** Software implementations of Jenks differ in the labels they print (some show the actual data values at the breaks, some the midpoints between values) and occasionally in the partition for near-ties. Run each product on F12-1 and record the observed breaks in I.8; the *membership* above is the expected result.

**Three methods, three maps, one table (hand-checked).**

| Ward | Value | Equal interval class | Quantile class | Natural breaks class |
| --- | --- | --- | --- | --- |
| W03 | 0 | 1 | 1 | 1 |
| W02 | 2 | 1 | 1 | 1 |
| W01 | 3 | 1 | 1 | 1 |
| W04 | 4 | 1 | 1 | 1 |
| W05 | 5 | 1 | 1 | 1 |
| W06 | 6 | 1 | **2** | 1 |
| W07 | 7 | 1 | **2** | 1 |
| W08 | 13 | 1 | **2** | **2** |
| W09 | 14 | 1 | **2** | **2** |
| W10 | 16 | **2** | **2** | **2** |
| W11 | 17 | **2** | **3** | **2** |
| W12 | 31 | **3** | **3** | **3** |
| W13 | 35 | 3 | 3 | 3 |
| W14 | 40 | 3 | 3 | 3 |
| W15 | 45 | 3 | 3 | 3 |
| W16 | NULL | — not classified — | — | — |

Read the rows for W06–W11: six wards change class depending on the method, with *no change to any value*.

**Developer analogy — bucketing in a histogram or a `CASE` expression.** Equal interval is `FLOOR(value / 15)`; quantile is `NTILE(3) OVER (ORDER BY value)`; natural breaks is a one-dimensional clustering (k-means-like on sorted data). Every developer knows that the histogram's shape depends on the bin rule. **Where the analogy stops:** a histogram is usually accompanied by its bin edges; a choropleth often is not, and readers then compare *colours* across maps whose bins differ. 12.4.2 makes the disclosure mandatory.

**Misconception:** "Natural breaks is the smart one, so use it by default." *Consequence:* the breaks are tuned to *this* table; a second map of next month's values gets different breaks, and the two maps' colours are incomparable (12.4.3). Esri's page says of natural breaks that it is "not suitable for comparing multiple maps built from different underlying information" [S36]. No method is universally best; the blueprint forbids declaring one so.

**Comprehension check 12.4.1.** Reclassify the same fifteen values into **two** classes by each method. For quantile, note the problem that fifteen values create and state how you resolved it.

### 12.4.2 Changed breaks change the apparent pattern; the map must disclose intervals and units

Draw the three maps of 12.4.1 on the G-12 grid with a three-step light-to-dark ramp (Figure M.3-2 specifies it):

- **Equal interval:** nine pale wards, two mid, four dark in the bottom row and W12. Message a reader takes away: "the south is the problem; the rest is fine."
- **Quantile:** five pale, five mid, five dark — by construction one third of the wards are "dark", including W11 (17), which is now in the same class as W15 (45). Message: "a third of the district is bad."
- **Natural breaks:** seven pale, four mid, four dark. Message: "three tiers; the south and W12 are a group of their own."

Same 238 requests; three stories. None is a lie — each method did what it promises — but a reader who sees only one map, with a legend that says "Low / Medium / High", cannot know which story they are being told. This is why the final map **must disclose the class intervals and the units**:

- The legend shows the *numeric* interval for each class — `0 – 7`, `13 – 17`, `31 – 45` — never only "Low / Medium / High". If the data have gaps between classes (as natural breaks produce here), show the actual value ranges so that the reader sees that no ward has 8–12 requests; ArcGIS Pro offers this with "Use feature values in labels", which shows "only values that are present in the field as boundaries in the symbol class labels" [S37].
- The legend states the **unit and the definition**: "open requests per ward, count at 2026-09-15, Policy W-1". A number without a unit is not a value.
- The map's method note names the **classification method and class count** — "natural breaks, 3 classes" — so that a second author can reproduce it or a reader can discount it.
- Where a value falls *exactly* on a break, the boundary convention must be stated ("classes include their upper bound" or the reverse). In this table no value does; in the rate table of 12.5 several tie, which is why the lab uses manual breaks with stated bounds (12.8.4).

> **Platform note.** The phrase *disclose the breaks* is a general cartographic rule. How the legend text is produced is product-specific: ArcGIS Pro formats class labels in the Symbology pane ("Format labels" for rounding and alignment) [S37]; ArcGIS Online writes the class ranges in the legend when "Classify data" is on [A02]; QGIS has a legend-format setting on the graduated renderer [Q02]. In a web library the legend is *yours to write* — OpenLayers and Mapbox draw features, not legends — so the disclosure is code you must add.

**Worked example — the same ward, three colours.** *Question:* W11 has 17 open requests. What colour is it? *Answer:* under equal interval, class 2 (mid); under quantile, class 3 (dark); under natural breaks, class 2 (mid). A councillor for W11 shown the quantile map will ask why their ward is "red"; shown the natural-breaks map they will not ask. *What decides which is right:* the question. If the question is "which third of wards should get the first crews", quantile answers it exactly (and honestly, if the legend says "top five wards by count"). If the question is "which wards are unusually high", natural breaks. If the question is "how many wards exceed 30", equal interval or a *manual* break at 30 (Esri's page lists manual interval for exactly this: "set class ranges appropriate for the data" [S36]).

**Misconception:** "The software's default classification is neutral." *Consequence:* the author never looks at the breaks; the reader assumes the pattern is in the data. A default is a choice someone else made for average data; F12-1 is not average data (it has a cluster and a gap).

**Comprehension check 12.4.2.** Write the legend for the natural-breaks map of `open_requests` — three entries plus the W16 entry — with intervals, units, and definition, in the form it should appear on the printed page.

### 12.4.3 Comparing over time or between areas: what recalculated classes do

A single map is read alone; a *pair* of maps is read by comparing colours across them. That comparison is valid only if the same colour means the same interval on both maps. Consider next month's table (synthetic, invented for this example only): extra crews were sent to W12 and W15, so W12 falls from 31 to **12** and W15 from 45 to **20**; W13 edges down from 35 to 33 and W14 from 40 to 38; the other eleven wards are unchanged. Sorted, the new values are `0, 2, 3, 4, 5, 6, 7, 12, 13, 14, 16, 17, 20, 33, 38` (hand-checked).

- **Fixed classes** — the natural-breaks intervals of 12.4.1 kept as manual breaks `0–7 / 8–17 / 18–45` and stated on both maps: W12 (12) moves from dark to mid; W15 (20) stays dark; W13 and W14 stay dark; nothing else moves. The pair shows exactly what happened: one ward improved out of the top class, one improved but is still there.
- **Quantile recalculated independently** (what you get by simply applying the same method to the new layer): five per class again, and the top five by value are now W10 (16), W11 (17), W15 (20), W13 (33), W14 (38). **W10 and W11 turn dark although their values did not change**, because two wards above them fell past them; W12 leaves the dark class. A reader concludes "W10 and W11 got worse" — false — and may miss that W12 improved most.
- **Natural breaks recalculated:** the new clusters are roughly `0–7`, `12–20`, `33–38`; W12 and W15 join W08–W11 in the middle class and the dark class shrinks to two. Different breaks, different colours, a third story — and the legend, if it says only "Low/Medium/High", tells the reader none of this.

The rule: **for a comparison over time or between areas, fix the classes once, state them, and use them on every map in the set.** Esri's note that natural breaks is unsuitable "for comparing multiple maps built from different underlying information" [S36] is the specific case; the general principle is that any *data-derived* breaks (quantile, natural breaks, standard deviation) re-derive themselves when the data change. Equal interval on a fixed *range* and manual classes are the comparison-safe choices; equal interval on the *data's* range is not, because the range moves too.

The same applies *between areas*: a district map and a city map classified separately by quantile both show "one third dark" and mean different numbers. If two maps are to be compared, their legends must be identical and say so ("classes fixed across all maps in this report").

**Developer analogy — a chart with autoscaled axes.** Two line charts with independently autoscaled y-axes both fill the frame; the reader sees two similar curves and misses that one is ten times the other. Fixing the axis range across charts is the same discipline as fixing class breaks. **Where the analogy stops:** an autoscaled axis usually *prints* its range, so a careful reader can recover the truth; an auto-classified choropleth with a "Low/High" legend gives the reader nothing to recover it from.

**Misconception:** "Recomputing the classes each month keeps the map up to date." *Consequence:* a monthly report in which the map never changes while the numbers fall by a third; or the reverse — a map that changes dramatically because one outlier moved a natural break. The map is up to date; the *comparison* is broken.

**Comprehension check 12.4.3.** The manager wants a before/after pair for W01–W16 using natural breaks "because it fits the data best". Write the three-sentence recommendation: what to fix, what to state in the legend, and the one situation in which recalculating would be acceptable.

## 12.5 Distinguish totals, rates, and density

### 12.5.1 The choropleth and why the denominator matters

**Definition — choropleth map.** A map in which each area (ward, district, country) is filled with a colour that stands for a class of a numeric value belonging to that area. ArcGIS Pro's graduated-colors page notes that such maps are "usually called choropleth maps" [S37]. It is the standard management map: one value per ward, classes from 12.4, a sequential ramp.

The choropleth invites a comparison *between areas* — "W15 is darker than W05, so W15 is worse." That comparison is only fair if the value is comparable between the areas. A **total** (a count of requests) is usually not comparable, because areas differ in size, population, road length, asset stock, and reporting habits; a bigger ward has more of everything. To compare, you divide the total by a **denominator** that measures the exposure — the "how much stuff is there to go wrong" — and get a **rate** (per 1,000 households) or a **density** (per km², per km of road). The graduated-colors page provides the mechanics: "To normalize the data, choose a field from the Normalization menu", with alternatives to divide by the percentage of total or to use a logarithm [S37]; ArcGIS Online's Counts and Amounts styles offer the same through **Divide by**, which the page describes as turning counts into rates such as per-capita or per-square-kilometre values [A02].

Three words to keep apart:

| Term | Formula | Answers the question | F12-1 example (synthetic) |
| --- | --- | --- | --- |
| **Total (count)** | Σ requests | How much work is there here? | W15: 45 open requests |
| **Rate** | total ÷ exposure count × constant | How much per unit of the thing that generates requests? | W15: 45 ÷ 9,000 × 1,000 = 5.0 per 1,000 households |
| **Density** | total ÷ area or length | How concentrated is it in space? | W15: 45 ÷ 10 km = 4.5 per km of road; 45 per km² (all wards are 1 km², so density by area equals the count here — by design) |

A total is not wrong; it is the right variable for a *workload* question ("how many crews does W15 need?"). It is the wrong variable for a *comparison* question ("is W15 unusually bad?"). The blueprint's phrase is exact: distinguish **workload totals** from rates or densities.

**Developer analogy — error counts versus error rates.** A service that logs 1,000 errors a day is not worse than one logging 100 if the first handles 100× the traffic; every SRE normalises by requests before comparing. A ward's `open_requests` is the error count; `households` or `road_km` is the traffic. **Where the analogy stops:** in monitoring, the denominator (requests served) is unambiguous and measured by the same system. In GIS the candidate denominators are *different datasets* with their own dates, accuracy, and coverage (12.5.3), and choosing between them is a judgment the map must disclose.

**Misconception:** "The count map shows where the problems are." *Consequence:* the manager sends the extra crew to W15 (45 requests among 9,000 households) and not to W12 (31 among 2,000), although W12's residents are three times as likely to have an open request. Whether that is the *right* decision depends on the question — but the count map hides the choice.

**Comprehension check 12.5.1.** Give one management question for which `open_requests` (the total) is the right map variable and one for which it is the wrong one. For the second, name the denominator.

### 12.5.2 The blueprint's arithmetic, and F12-1's

The blueprint supplies an example that fits on one line; all values are synthetic:

> Ward A has **100** requests and **10,000** residents. Ward B has **60** requests and **3,000** residents.

- A has more requests: 100 > 60. On a count map, A is darker.
- Per 1,000 residents: A = 100 ÷ 10,000 × 1,000 = **10**; B = 60 ÷ 3,000 × 1,000 = **20**. B has *twice* the rate. On a rate map, B is darker.

Both statements are true at once. A reader shown only the count map "knows" A is worse; shown only the rate map, B. The map's author must know *which question* is being asked and must write the variable and its denominator on the map.

**F12-1 carries the same lesson with sixteen wards (hand-checked from Table F12-1).**

| Ranking by… | 1st | 2nd | 3rd | Comment |
| --- | --- | --- | --- | --- |
| `open_requests` (count) | W15 (45) | W14 (40) | W13 (35) | The bottom row: the three most *populous* wards |
| per 1,000 households | W12 (15.5) | W11 (10.0) | four wards tied at 5.0 (W04, W08, W13, W15) | W12 and W11 have small populations; W14 (40 requests, 10,000 households) drops to 4.0 |
| per km of road | W15 (4.5) | W11 (4.25) | W13 (2.92) | W12 (20 km of road) drops to 1.55 — its many requests are spread along a long network |

Three defensible "worst wards" — W15, W12, W15/W11 — from one table. The choice of denominator *is* the analysis; the map merely shows its result.

**Worked example — writing the rate onto the map.** *Question:* produce the management map variable "open requests per 1,000 households". *Inputs:* Table F12-1; `households` from the synthetic 2024 register. *Assumptions:* the register's household counts are for the same ward boundaries as the request counts (both say 2024); W16's rate is NULL because its numerator is missing, not because its households are unknown. *Operation:* `open_requests / households * 1000`, computed as a new field or via the product's normalization option (Procedure box in 12.8.4). *Answer:* the seventh column of Table F12-1; W12 = 15.5, W11 = 10.0, W03 = 0.0, W16 = NULL. *Check:* a spot-check by hand for two wards (W12: 31 ÷ 2,000 × 1,000 = 15.5 ✓; W14: 40 ÷ 10,000 × 1,000 = 4.0 ✓), and the invariant that no ward with `open_requests = 0` has a non-zero rate. *What it does not establish:* that W12's residents *experience* more problems — the rate is per household, and W12 might have many non-residential premises (a market, a depot) that generate requests. That is 12.5.3.

**Misconception:** "Divide by area — that is what 'density' means and it is always right." *Consequence:* on F12-1 every ward is 1 km², so density by area *is* the count, and the map changes nothing; on a real city, dividing by area rewards sparsely populated large wards with a pale colour whether or not their residents are well served. Area is one candidate denominator among several; 12.5.3 chooses.

**Comprehension check 12.5.2.** A colleague computes "requests per household" for W12 as 31 ÷ 2,000 = 0.0155 and labels the legend "0.0155". Is the number right? Is the legend right? What would you change?

### 12.5.3 Is population the right denominator? Asset counts, road length, and reporting exposure

Population is the habitual denominator because it is usually available. It is the *right* one only when the thing being counted is generated by people in proportion to their number. Ask, for the actual question: *what generates the requests?*

| Request category | Plausible generator | Better denominator than households | Available in the training package? |
| --- | --- | --- | --- |
| Streetlight out | Number of streetlights | count of streetlight assets per ward | Yes — the assets layer (Chapter 3) |
| Pothole | Road length, traffic | `road_km` (F12-1) | Yes |
| Blocked drain | Number of drains, rainfall | count of drain assets | Yes (drains); rainfall no |
| Fallen tree | Number of trees, storm exposure | count of tree assets | Yes (trees) |
| All categories together | mixture | no single honest denominator — show categories separately, or state the compromise | — |

And a denominator that is often forgotten: **reporting exposure**. Requests are *reports*, and reports depend on who reports. A ward with an active residents' association and a well-advertised app produces more requests per fault than a ward where people do not report. A high rate can mean more faults *or* more reporting. Nothing in the request table separates the two; only an *independent* observation (an inspection survey of a sample of assets, Chapter 8's inspections) can. Therefore:

- A rate map may be titled "open requests per 1,000 households" and *never* "service quality", "risk", or "problem areas". The rate proves how many reports there are per household; it does not prove underlying risk. The blueprint's instruction is categorical: do not imply the resulting rate proves underlying risk.
- The map's notes must say which denominator was used, its source and date, and *why* — "households (2024 register) chosen because the question was about residents' experience; road length would suit the pothole subset better."
- If the decision is about **crew capacity**, the honest variable may be the *total* after all (a crew visits requests, not rates), perhaps alongside a rate map that answers the fairness question.

**Worked example — choosing for the actual question.** *Question:* "Which wards should get the first round of the new streetlight-repair contract?" *Reasoning:* the generator of streetlight requests is streetlights; households is a proxy at best; the honest denominator is the number of streetlight assets per ward, and the numerator should be *streetlight* requests only, not all categories. F12-1 does not carry a per-category count or an asset count, so the honest answer is "this table cannot rank wards for that contract; we need the per-category count and the asset register" — not a map built from the wrong variable with a confident title. *What it does establish:* the shape of the next analysis (Chapter 11's worksheet).

**Developer analogy — choosing the right metric before optimising.** Optimising "requests per second" when the real goal is "p99 latency" produces a fast system that is slow for the users who matter. The denominator is the metric definition; get it right before drawing anything. **Where the analogy stops:** engineering metrics are usually chosen once, by the team that owns the system; a map's denominator is contested by every reader with a different question, so the map must *show* its choice rather than assume agreement.

**Misconception:** "A high rate means high risk, so colour it red." *Consequence:* a map titled "flood risk by ward" built from blocked-drain *reports* per household — which measures reporting behaviour as much as drainage — is read by a councillor as an engineering assessment. Chapter 11 refused to call a mask a flood model (11.7.2); Chapter 12 refuses to call a rate a risk.

**Comprehension check 12.5.3.** For the *fallen tree* category, propose the denominator, say where it would come from, name one reason the resulting rate could still be misleading, and write the map title you would allow.

## 12.6 Provide map context and honest uncertainty

### 12.6.1 Title, legend, units, source/date, method notes — and scale bar or orientation only when they help

The QGIS introduction lists the common elements: "the title, map body, legend, north arrow, scale bar, acknowledgement, and map border", and describes the purpose of each — the title is "usually the first thing a reader will look at", the legend is "a dictionary that allows you to understand the meaning of what the map shows", and the acknowledgement carries "important information" such as data quality and "how, by whom and when a map was created" [S35]. ArcGIS Pro's layout page lists the same objects as layout elements: map frames, scale bars, north arrows, titles, descriptive text, legends, grids, and graticules [X08]; legends "tell the map reader the meaning of the symbols used to represent features on the map", each legend item pairing "a patch showing an example of the map symbols and explanatory text", and a legend can be static or dynamic — "updating to show only layers visible in the current map frame extent" [X07]. A scale bar "is a line or bar divided into parts … labeled with its ground length" and, in a layout, "If the map scale for that map frame changes, the scale bar updates to remain correct" [X06].

That is the inventory. The blueprint's instruction is to use each element *because it helps the reader*, not mechanically. Here is what each must contain on the two Chapter 12 maps, and when it may be left out:

| Element | Management map (F12-1, print) | Operations map (F12-2, phone) | When to omit |
| --- | --- | --- | --- |
| **Title** | The variable, the unit, the area, the date: "Open service requests per 1,000 households, District G-12 wards, 2026-09-15 (synthetic training data)" | Short: "Open requests — W06/W07 — crew sheet 2026-09-15" | Never |
| **Legend** | Every class with its numeric interval and unit; the "no data" entry; the zero case (12.6.2) | Category shapes; priority sizes with "1 = highest"; open/closed fill | Never; a dynamic legend is fine on the web if the default view's legend is complete |
| **Units** | In the title and every legend label | On the priority legend ("1 = highest") | Never |
| **Source and date** | "Requests: training tracker export 2026-09-15. Households: 2024 synthetic register. Boundaries: 2024 ward layer." | Same, shorter, in the app's item description | Never |
| **Method notes** | Classification method and count; Policy W-1; W16 not received; denominator choice and reason | Symbol footprint note if it matters; "positions ± 15 m (phone capture)" | Never — but on a phone they live in the layer's description, not on the map face |
| **Scale bar** | Yes — a bar labelled 0, 500 m, 1 km lets a reader judge ward size | Yes on the web map (self-consistent on screen, 12.1.2) | When the medium resizes freely (a slide that will be cropped) — then a verbal scale or nothing |
| **Orientation aid (north arrow)** | **No** for the planar training grid: the grid asserts no Earth orientation (its y axis is "up", not north); write "schematic grid — not oriented to north" instead | Same | Omit whenever north is not meaningful or is obvious; add when the map is rotated or the reader will navigate by it |
| **Border / neatline** | Optional; helps on a page with other text | Not applicable | — |

> **Platform note — a scale bar needs known units.** A scale bar can only be labelled in ground units if the map's coordinate system has units. The training grid has *no* coordinate system; how ArcGIS Pro and QGIS behave when a scale bar is added to a map whose layers have an unknown coordinate system is a **Verification item** (I.6). If the product cannot label the bar, the lab substitutes a drawn 1 km reference line with the text "1,000 m on the training grid" — which is an honest scale bar.

**Worked example — the notes block.** *Question:* write the method notes for the management map. *Answer (synthetic):* "Variable: open service requests per 1,000 households. Numerator: requests with status OPEN at 2026-09-15, assigned to wards by spatial join under Policy W-1 (a request on a shared edge is counted in the ward with the lower code). Denominator: households, 2024 synthetic register. Classes: manual, 0–2.5 / >2.5–5.0 / >5.0, chosen because several wards tie at 2.0, 4.0 and 5.0. W16: request export not received — shown as *no data*, not zero. W03: zero open requests. Positions of requests are reporter-supplied (± 15 m); ward assignment of edge cases follows the policy, not the drawing. This rate measures reports per household; it is not a measure of risk or of service quality." *Check:* every number a reader could question has its definition; every limitation of 12.5.3 and 12.6.3 is present. *What the notes do not do:* they do not make a bad variable good — they make the reader able to judge it.

**Developer analogy — a README and a schema for the map.** Title = the one-line description; legend = the type definitions; source/date = the dependency versions; method notes = the changelog and known issues. A map without them is an unlabelled binary. **Where the analogy stops:** a README is read before use; a map's notes are read *after* the reader has already formed an impression from the colours. So the notes must be *on the map*, near the legend, not in a separate document — and the most important caveats ("no data", "not risk") belong in the legend and title where they cannot be skipped.

**Misconception:** "A north arrow and a scale bar make it a proper map." *Consequence:* a north arrow on a schematic grid asserts an orientation the data do not have; a scale bar on a projection with strong scale variation across the page (Chapter 6's Web Mercator lesson) is wrong at most points on the page. Add an element when it helps the reader; ArcGIS Pro's own scale-bar page notes that map scales "can vary based on coordinate system, latitude, direction, and map extent" [X06].

**Comprehension check 12.6.1.** The crew map is exported to a PDF that the supervisor prints on a black-and-white printer. List the five elements from the table that *must* be present on the printed sheet and the one that may be dropped, with reasons.

### 12.6.2 Zero is not missing: show them differently, on the map and in the legend

Table F12-1 contains W03 = 0 (surveyed; no open requests) and W16 = NULL (export not received). Chapter 10 kept `IS NULL` apart from `= 0` in queries; Chapter 11 kept NoData apart from 0 in rasters; the map must keep them apart *visually*:

- **Zero** is a value. It belongs in the classification (it is the minimum of the range, so it sits in the lowest class) and takes that class's colour. The legend's lowest entry must show that 0 is included — "0 – 7" — so that a reader sees a pale ward as "few or none", not as "unknown".
- **Missing** is the absence of a value. It must be *excluded* from the classification (it has no value to classify, and including it as 0 would put a false zero into the range — the rate map would show W16 as a "good" ward) and drawn with a symbol that is *not on the ramp*: a hatch pattern, a neutral grey with a distinct texture, or an outline with no fill, plus a legend entry that says what is missing and why: "No data — request export not received at 2026-09-15."

| | Zero (W03) | Missing (W16) |
| --- | --- | --- |
| Is it a value? | Yes, 0 | No |
| In the classification? | Yes (lowest class) | No |
| Symbol | Lowest class colour | Hatch or neutral texture, off the ramp; survives grayscale |
| Legend text | "0 – 7 open requests" | "No data — export not received" |
| In the total? | Yes (adds 0) | No — and the total must say "238 over 15 wards; W16 unknown" |
| In a rate? | 0.0 per 1,000 | NULL — never 0.0 |

> **Platform note — how each product exposes the missing case.** ArcGIS Pro's graduated-colors page notes that values "can end up out of range in the classification scheme … when they contain null values", that "You can show values that are out of range in the table and assign a symbol to them to show them on the map", and that **Show excluded values** adds an `<excluded>` symbol class [S37] — that is where W16's hatch goes. ArcGIS Online's Counts and Amounts styles have a **Show features with no value** toggle "to draw locations with missing data on the map, and optionally specify a style and label to represent those values" [A02]. QGIS's categorized renderer has an "all other values" class for unmatched categories [Q02]; how its *graduated* renderer draws a NULL value is not stated in the section read and is a **Verification item** (I.6) — a NULL must never be silently undrawn, because an undrawn ward looks like a hole in the district, not like missing data. In a web library, the missing case is a style rule you write yourself.

**Preserve unresolved and unassigned records in the accompanying explanation.** The per-ward count came from a join that had unmatched requests (Chapter 11's P6 case) and edge cases (G06). None of those is visible in a choropleth. The map's explanation must carry them: "1 request outside all wards, not counted; 1 request on the W06/W07 edge, counted in W06 under Policy W-1." The reconciliation identity of 11.6.3 — assigned + multiply assigned + unassigned = total — is the *source-value table* the lab requires (12.8.3): the map is trustworthy only if that table can be produced alongside it.

**Worked example — the two pale wards.** *Question:* on the count map, both W03 and W16 would be pale if W16's NULL had been replaced by 0 on import. What does the reader conclude, and what is the cost? *Answer:* the reader concludes both wards are fine; W16's requests — which nobody has counted — are invisible; the district total is silently understated; and the rate map awards W16 the best rate (0.0 per 1,000) for a ward with no data. *Check:* the total on the map (238) must equal the sum of the fifteen shown values and the legend must contain a "no data" entry with exactly one ward in it; a "16 wards, total 238" caption is the tell-tale error. *What this does not establish:* that W16 has many requests — it may have few; the point is that the map must not pretend to know.

**Misconception:** "Fill NULL with 0 so the classification works." *Consequence:* the false zero enters the range, the rate map shows the ward as best-served, the total is wrong, and — because it is the same colour as W03 — nobody can tell from the map that anything is missing. Chapter 4's NoData rule applies unchanged: missing is a policy decision to be stated, never a value to be invented.

**Comprehension check 12.6.2.** Next month W16's export arrives with 9 open requests and, separately, W03's export is found to have been *empty because of a filter error* (its true count is unknown). Write the legend entries and the total caption for the new map.

### 12.6.3 State limitations, aggregation choices, and the boundary policy; an area summary is not every location inside it

A choropleth says one thing about a whole ward. It does not say that thing about any street inside the ward. W12's 15.5 per 1,000 households is a *ward average*; inside W12 there may be one market street with twenty open requests and residential streets with none. Reading an area value as if it applied to each point inside the area is the oldest error in thematic mapping; the map must guard against it in three places:

1. **The aggregation choice is stated.** "Values are per ward (16 units of 1 km²)". A reader then knows the resolution of the *claim*, exactly as Chapter 4's cell size gave the resolution of a raster. A different aggregation — per 500 m grid cell, per street — would give a different map from the same requests; the choice of unit is part of the method, and the notes must name it.
2. **The boundary policy is stated.** Every count per area needed a rule for records on the edge (Policy W-1) and for records outside every area (unassigned). The notes say which rule, and how many records it affected: "1 edge case (G06 → W06); 0 unassigned in W06/W07." A policy that affected 40 % of the records would deserve a different design (points, not areas).
3. **The limitations are stated, on the map.** Capture accuracy of positions; the date and the meaning of "open"; the denominator's date and source; the categories mixed together; the fact that the rate measures reports, not faults (12.5.3); the fact that the map does not describe any individual location. These are the limitations Chapter 11 recorded in the analysis log (11.8.3); the map is where they become public.

**Worked example — the sentence that overreaches.** *Claim in a draft report:* "Residents of W12 are three times as likely to have a problem as residents of W14." *Inputs:* rates 15.5 (W12) and 4.0 (W14) per 1,000 households — a ratio of 3.9, not 3, and the ratio is of *open reports per household*, a ward-level average. *Corrected sentence:* "At 2026-09-15, W12 had 15.5 open requests per 1,000 households and W14 had 4.0 — about four times the rate. The figures are ward averages of reports and do not describe individual streets, nor separate reporting behaviour from underlying faults." *What the correction does:* keeps the finding, fixes the number, removes the causal and individual-level claims. *What it does not do:* it does not make the comparison *unimportant* — it may be the most useful line in the report, and it is now defensible.

**Developer analogy — an average per service is not a per-request latency.** "Service A's mean latency is 200 ms" says nothing about the request that took 4 s. Every developer knows to look at the distribution. A ward value is a mean over a spatial unit; the distribution inside the unit is invisible on a choropleth. **Where the analogy stops:** a latency histogram is one click away; the within-ward distribution of requests is a *different map* (the points), which is why the lab makes both maps from one dataset.

**Misconception:** "The dark ward is where the problems are — send the crew there." *Consequence:* the crew is sent to a ward, not to a place; on arrival they need the points map anyway. The management map answers "which ward"; the operations map answers "where"; neither answers the other's question.

**Comprehension check 12.6.3.** The rate map's darkest class contains W12 and W11. A councillor says "so every street in W11 has problems". Write the two sentences you add to the map notes that pre-empt this reading, and name the other map that answers the councillor's implied question.

## 12.7 Distinguish presentation controls from data protection

### 12.7.1 Hiding a field or a layer is a presentation choice, not access control

Every mapping product lets you hide things: turn a layer off, remove a field from a pop-up, filter the features drawn, blank out a label. These controls change *what the reader sees in this map*. They do not change *what the reader can obtain from the data*, because the data live in the layer or service, and the map is one client of it.

Read the platform's own words. ArcGIS Pro's pop-up page says of field formatting in a pop-up that "Changes made here are exclusive to the pop-up display and do not impact the format of the field in the table" [X09], and its Fields element lets you turn off "Only use visible fields and Arcade expressions" to customise the list [X09] — a *display* list. ArcGIS Online's Map Viewer likewise lets you "Rearrange and remove fields, and click Select fields to add fields to the list", and remove pop-ups altogether with the **Enable pop-ups** toggle [A03] — again, display. Meanwhile the ArcGIS REST API's query operation on a feature service layer accepts an `outFields` parameter described as "The list of fields to be included in the returned result set", and with the wildcard `*` "the query results include all the field values" [R01]. A field removed from a pop-up is one HTTP request away for anyone who can reach the service. The same is true of a layer turned off in a web map (the layer item is still shared as it was) and of a filter set in a map (the unfiltered service is what is shared).

In plain terms:

| Control | What it changes | What it does *not* change |
| --- | --- | --- |
| Layer visibility off | This map's drawing | Who can open the layer item or query the service |
| Field removed from pop-up | This map's pop-up | The field in the layer, its presence in the attribute table, its return from a query |
| Map filter / definition query in the map | Which features this map draws | Which features the service returns to another client |
| Label off | This map's drawing | Nothing about the data |
| Symbology | This map's drawing | Nothing about the data |

**Developer analogy — `display: none` versus authorisation.** Hiding a salary column with CSS does not stop the API from returning it; every developer has seen the "hidden" field in the network tab. A pop-up configuration is CSS. Access control is the authorisation layer on the API — a different system with different owners. **Where the analogy stops:** in a web app the two layers are usually built by the same team and reviewed together; in a GIS platform the map author is often not the layer owner and may not even see the sharing settings, so the gap between "hidden" and "protected" is easy to miss and must be checked explicitly.

**Misconception:** "I removed the reporter's phone number from the pop-up, so the public map is safe to share." *Consequence:* the field is still in the hosted layer; anyone with the layer's URL queries it with `outFields=*`; the personal data are exposed, and the author believed they had protected them.

**Comprehension check 12.7.1.** For each action, say whether it is presentation or protection: (a) unchecking a field in the Map Viewer pop-up; (b) sharing the web map with "Everyone (public)" while the layer remains shared with the organisation only; (c) creating a hosted feature layer view with the field excluded; (d) setting a scale range so that the layer draws only past 1:5,000. (You are expected to reason from 12.7.1–12.7.2, not to know the administration details.)

### 12.7.2 Recognise when a requirement belongs to the permissions and administration chapters

This chapter does not teach security configuration; the later chapters on ArcGIS Online administration and ArcGIS Enterprise do. What you must be able to do *now* is read a requirement and say which kind it is.

| The requirement says… | Kind | Where it is met |
| --- | --- | --- |
| "The crew should not be distracted by closed requests" | Presentation | This chapter: filter or symbology in the crew map |
| "Field staff should see priority at a glance" | Presentation | This chapter: symbol size |
| "The public must not be able to obtain reporter names" | **Protection** | Later chapters: a hosted feature layer view that excludes the field, and sharing settings on the view, not on the map. ArcGIS Online's documentation describes a view as a separate item that references the source data, in which you can "exclude fields from the view if the view users do not need to access them" and which can be shared independently of the editable source [A04] |
| "Only the drainage team may edit drain requests" | **Protection** | Later chapters: editing settings and sharing on the layer or a view [A04] |
| "The councillor's version must show only W11" | Depends: if convenience, a map filter (presentation); if the councillor *must not* see other wards, a view with a definition (protection) | Ask which, then route accordingly |
| "Managers need the rate; residents need the points" | Presentation (two maps) | This chapter |
| "Personal notes must never leave the internal system" | **Protection** | Later chapters — and 12.7.3 now: do not put them on any map |

The test question is: *if a determined reader ignored the map and went to the layer, would the requirement still be met?* If the answer must be yes, it is a protection requirement and it belongs to the administration chapters. Your job in this chapter is to flag it, not to solve it with symbology.

> **Verification item for later chapters (recorded here so it is not lost).** Confirm in the installed ArcGIS Online organisation that a field excluded from a hosted feature layer view is absent from the view's REST layer definition and from its query results with `outFields=*`. The view page read for this chapter says the field is excluded from the view [A04]; the REST-level behaviour was not checked in this chapter.

**Misconception:** "The map is shared only with the team, so the data are protected." *Consequence:* sharing a map and sharing a layer are separate settings; a map shared narrowly can reference a layer shared widely (and the reverse breaks the map for its readers). This is a later-chapter topic; the Phase 1 skill is to notice that the sentence "the map is shared with…" says nothing about the layer.

**Comprehension check 12.7.2.** A stakeholder writes: "Publish the request map to the public site, but residents must not see other residents' notes or the reporter reference, and only our crews may update status." Split this into presentation and protection requirements and say, for each protection requirement, what you would tell the stakeholder about *when* it can be met.

### 12.7.3 Review the output for unnecessary personal details (synthetic data only)

Whatever the access rules, a map should not *carry* personal details it does not need. Run the review on the synthetic F12-2 before the crew map leaves the workstation:

| Field | Needed by the crew? | Needed by the manager? | Action on the maps |
| --- | --- | --- | --- |
| `request_id` | Yes (job sheet) | No | Label on the crew map; absent from the management map |
| `category`, `priority`, `status` | Yes | Only as counts | Symbolised on the crew map |
| `reported_on` | Useful (age of job) | Only as the window | Pop-up on the crew map |
| `reporter_ref` | **No** — the crew fixes the asset, not the reporter; if contact is ever needed, it goes through the office system | No | Not on any map, not in any pop-up, not in the exported package |
| `reporter_note` | Sometimes operationally useful ("second lamp from the corner"), **but** G08's note names a relative, an age, and a house number | No | Never as a label; if shown in a pop-up, only after the office has redacted personal detail; the G08 note is removed from the map copy and stays in the internal record |
| exact coordinates to the millimetre | No — ± 15 m capture accuracy (12.2.3) | No | Round in any exported table to a precision that matches the accuracy |

The principle is *data minimisation for the map*: a map is a publication, and publication is not the place to discover that a free-text field contained a name. Two practical habits:

1. **Never label or export free-text fields by default.** Free text is where personal details hide (Chapter 9's `note` field carried duplicate-detection evidence for the same reason).
2. **Build the map from a purpose-specific copy or view** whose field list is the minimum the purpose needs. That copy is a presentation artefact; the *protection* of the source is the later chapters' job (12.7.2) — but a map that never had the field cannot leak it.

The blueprint is explicit that this review is done on the **synthetic** scenario and that no real resident information is to be introduced for a map-design exercise. Everything in F12-2 — references, notes, ages, house numbers — is invented for this purpose.

**Misconception:** "It is internal, so the notes can stay on the map." *Consequence:* the "internal" PDF is forwarded, printed, left in a van. A map is copied more easily than a database row; the minimisation habit costs nothing and removes a class of incident.

**Comprehension check 12.7.3.** The supervisor asks for `reporter_note` in the crew map's pop-up "because the location hints help". Write the three-line reply: what you will include, what you will not, and what process makes the useful part available.

## 12.8 Guided lab: redesign a misleading map

### 12.8.1 Objective and prerequisites

**Objective.** Starting from one checked dataset (F12-1 and F12-2) and a deliberately poor map built from it ("Map B-12"), produce (a) an **operational map** for the W06–W07 crew and (b) a **management map** of the district, each with its legend, notes, and a source-value table, and document the design decisions that differ between them. The instructor then checks that nothing in your styling conceals a discrepancy in the underlying numbers.

**Prerequisites.** Modules 12.1–12.7 read; Chapter 10's join by identity (10.3) and Chapter 11's count-per-ward (11.6) understood — you will *use* a summary table, not recompute it. You should be able to build a layer from CSV/WKT text (Chapter 3 lab) or receive the built package from the instructor.

**What this lab is and is not.** It is a cartographic-judgment exercise on a small, fully known dataset, and every expected value is hand-checked from the tables in this chapter. It is **not execution-tested** in software by the author: the ArcGIS Pro and QGIS steps below are written from the cited documentation, and the instructor must run them once (Instructor Appendix I.6) and record the observed behaviour before issuing the lab.

### 12.8.2 Required software and access

- **Primary:** ArcGIS Pro 3.x with a Basic licence or higher; no extension; no ArcGIS Online account required. Symbology pane [X01] [S37] [X02], Color Vision Simulator [X03], layout with legend and scale bar [X07] [X06] [X08].
- **Alternative:** QGIS Desktop 3.44 — categorized and graduated renderers [Q02], Preview mode [Q01], print layout (12.8.9).
- **Optional web route:** ArcGIS Online Map Viewer with an organisational account — Types and Counts and Amounts styles with Divide by and Show features with no value [A01] [A02], pop-up configuration [A03] (12.8.10).
- A spreadsheet or text editor for the source-value table and the rationale.

### 12.8.3 Input data — create it by copying the text below (synthetic)

Five UTF-8 CSV files with a header row. Polygons and lines carry a `wkt` column; points carry `x`, `y`. Coordinates are the planar training grid in metres, **no CRS**; the instructor converts them to a GeoPackage `Chapter12_G12.gpkg` (or a file geodatabase) by the Chapter 3 routes and leaves the coordinate system *undefined*.

`wards_g12.csv`

```csv
ward,wkt
W01,"POLYGON ((0 3000, 1000 3000, 1000 4000, 0 4000, 0 3000))"
W02,"POLYGON ((1000 3000, 2000 3000, 2000 4000, 1000 4000, 1000 3000))"
W03,"POLYGON ((2000 3000, 3000 3000, 3000 4000, 2000 4000, 2000 3000))"
W04,"POLYGON ((3000 3000, 4000 3000, 4000 4000, 3000 4000, 3000 3000))"
W05,"POLYGON ((0 2000, 1000 2000, 1000 3000, 0 3000, 0 2000))"
W06,"POLYGON ((1000 2000, 2000 2000, 2000 3000, 1000 3000, 1000 2000))"
W07,"POLYGON ((2000 2000, 3000 2000, 3000 3000, 2000 3000, 2000 2000))"
W08,"POLYGON ((3000 2000, 4000 2000, 4000 3000, 3000 3000, 3000 2000))"
W09,"POLYGON ((0 1000, 1000 1000, 1000 2000, 0 2000, 0 1000))"
W10,"POLYGON ((1000 1000, 2000 1000, 2000 2000, 1000 2000, 1000 1000))"
W11,"POLYGON ((2000 1000, 3000 1000, 3000 2000, 2000 2000, 2000 1000))"
W12,"POLYGON ((3000 1000, 4000 1000, 4000 2000, 3000 2000, 3000 1000))"
W13,"POLYGON ((0 0, 1000 0, 1000 1000, 0 1000, 0 0))"
W14,"POLYGON ((1000 0, 2000 0, 2000 1000, 1000 1000, 1000 0))"
W15,"POLYGON ((2000 0, 3000 0, 3000 1000, 2000 1000, 2000 0))"
W16,"POLYGON ((3000 0, 4000 0, 4000 1000, 3000 1000, 3000 0))"
```

`ward_summary_g12.csv` (the checked analysis result; an empty cell is NULL)

```csv
ward,open_requests,households,road_km,count_date,note
W01,3,1500,5,2026-09-15,
W02,2,1000,4,2026-09-15,
W03,0,500,2,2026-09-15,surveyed; no open requests
W04,4,800,4,2026-09-15,
W05,5,2000,6,2026-09-15,
W06,6,1500,3,2026-09-15,includes G06 (edge; Policy W-1)
W07,7,3500,7,2026-09-15,
W08,13,2600,5,2026-09-15,
W09,14,7000,10,2026-09-15,
W10,16,4000,8,2026-09-15,
W11,17,1700,4,2026-09-15,
W12,31,2000,20,2026-09-15,
W13,35,7000,12,2026-09-15,
W14,40,10000,15,2026-09-15,
W15,45,9000,10,2026-09-15,
W16,,1000,3,2026-09-15,export not received
```

`station_road_g12.csv`

```csv
road_id,name,wkt
SR-1,Station Road,"LINESTRING (1000 2500, 3000 2500)"
```

`requests_g12.csv`

```csv
request_id,x,y,category,priority,status,reported_on,reporter_ref,reporter_note
G01,1100,2100,Pothole,2,OPEN,2026-09-02,R-2201,
G02,1200,2700,Streetlight out,3,OPEN,2026-09-04,R-2202,Second lamp from the corner
G03,1210,2695,Streetlight out,3,OPEN,2026-09-04,R-2202,"Same street, next lamp"
G04,1500,2480,Blocked drain,1,OPEN,2026-09-10,R-2203,Water on the road after rain
G05,1850,2950,Pothole,2,OPEN,2026-08-28,R-2204,
G06,2000,2400,Fallen tree,1,OPEN,2026-09-12,R-2205,
G07,2100,2900,Pothole,3,OPEN,2026-08-30,R-2206,
G08,2300,2550,Streetlight out,3,OPEN,2026-09-01,R-2207,"Reporter says her father, 82, walks here at night - house no. 14"
G09,2500,2200,Blocked drain,1,OPEN,2026-09-11,R-2208,
G10,2600,2500,Pothole,2,OPEN,2026-09-06,R-2209,
G11,2750,2750,Water leak,1,OPEN,2026-09-13,R-2210,
G12,2900,2100,Streetlight out,3,OPEN,2026-08-26,R-2211,
G13,2950,2950,Pothole,2,OPEN,2026-09-08,R-2212,
G14,1400,2200,Pothole,2,CLOSED,2026-08-10,R-2213,
G15,2200,2300,Water leak,1,CLOSED,2026-08-14,R-2214,
G16,2850,2600,Blocked drain,1,CLOSED,2026-08-20,R-2215,
```

`readme_g12.txt` (provenance; data, not instructions)

```
District G-12 training package. Synthetic. Planar training grid, metres, no CRS, y axis is "up" (not north).
wards_g12: 16 wards, 1 km squares, 2024 boundaries.
ward_summary_g12: open requests per ward at 2026-09-15 counted by spatial join under Policy W-1
  (edge -> lower ward code); households from the 2024 synthetic register; road_km = centreline
  length clipped to ward, rounded to whole km. W16 open_requests is NULL: export not received.
requests_g12: complete request list for W06 and W07 at 2026-09-15; positions reporter-supplied,
  nominal accuracy +/- 15 m; reporter_ref is an opaque reference; reporter_note is free text.
station_road_g12: one centreline.
```

**Map B-12 — the deliberately poor map (supplied as an image by the instructor, built from this specification).** You do not need to reproduce it; you need to *diagnose* it. Its specification, fault by fault:

| # | What Map B-12 does | Fault (module) |
| --- | --- | --- |
| 1 | Title: "Ward Request Risk Map". No date, no source, no method note | Claims risk from a count of reports (12.5.3); no context (12.6.1) |
| 2 | Choropleth of `open_requests` with **W16's NULL replaced by 0**, so 16 values are classified | Missing shown as zero (12.6.2) |
| 3 | Quantile, 4 classes of 4 wards: {W16, W03, W02, W01} / {W04–W07} / {W08–W11} / {W12–W15} | Method undisclosed; W16 "Low" (12.4, 12.6.2) |
| 4 | Legend text: "Low / Moderate / High / Severe" — no numbers, no units | Intervals and units not disclosed (12.4.2) |
| 5 | Green (Low) to red (Severe) ramp; W03 and W16 the same green | Colour-only; red/green; zero = missing (12.3.3, 12.6.2) |
| 6 | Raw count as the comparison variable across wards of very different population and road length | Inappropriate comparison variable (12.5) |
| 7 | Every layer on: all 16 request points at 18 pt, all assets, inspections, the full basemap with names, `households` labelled on every ward, `reporter_note` as point labels | Clutter, no hierarchy, oversized symbols, personal details (12.1.3, 12.3.2, 12.7.3) |
| 8 | A north arrow on the schematic grid; no scale bar | Mechanical decoration; missing the useful element (12.6.1) |
| 9 | Caption: "16 wards, 238 open requests" | Total implies W16 counted (12.6.2) |

As an ASCII sketch of its ward colouring (L = Low, M = Moderate, H = High, S = Severe; rows from the top):

```
L  L  L  M      (W01 3, W02 2, W03 0, W04 4)
M  M  M  H      (W05 5, W06 6, W07 7, W08 13)
H  H  H  S      (W09 14, W10 16, W11 17, W12 31)
S  S  S  L      (W13 35, W14 40, W15 45, W16 NULL shown as 0)
```

Notice that the bottom-right "L" is the ward nobody has counted.

### 12.8.4 Ordered steps — ArcGIS Pro route (not execution-tested)

**Step 0 — Diagnose Map B-12 (no software).** Write the nine faults in your own words, each with the module that names it and the reader decision it damages. This is your baseline; the two maps you build must fix every one.

**Part A — the operational map (crew, W06–W07, phone or A4 job sheet).**

1. **Open** the package. Add `wards_g12`, `station_road_g12`, and `requests_g12` to a new map. The coordinate system is undefined by design; do not assign one (Chapter 6, 6.2). Record the map's reported coordinate system and units (**Verification item:** what the installed release reports for an undefined system).
2. **Filter to the decision.** On `requests_g12`, set a definition query `status = 'OPEN'` (Chapter 10; the syntax box in 10.2). Expected: **13** features drawn (G01–G13). Then decide, and write down, whether the closed three are shown hollow or omitted — either is defensible; the notes must say which.
3. **Symbolise categories with shapes.** In the **Symbology** pane choose **Unique values** on `category` [X01] [X02]. Assign a *distinct shape* to each of the five categories (circle, square, triangle, diamond, star) as well as a hue; remove or leave empty the `<all other values>` class (the page notes that unchecked, "only features participating in symbol classes are drawn" [X02]) — with a complete five-value list it should be empty. Expected: 5 classes; counts by category among the open set: Pothole 5 (G01, G05, G07, G10, G13), Streetlight out 4 (G02, G03, G08, G12), Blocked drain 2 (G04, G09), Fallen tree 1 (G06), Water leak 1 (G11) — 13 in total (hand-checked).
4. **Encode priority by size.** Use the symbology's option to vary symbol *size* by the `priority` field so that priority 1 is the largest (**Verification item:** the exact control in the installed release for varying unique-values symbology by an attribute, and whether a three-step size list can be typed; the graduated-colors page documents the equivalent "Vary … by transparency, rotation, or size" for that symbology [S37]). If the control is not available, use three manual size classes and document it. Expected: G04, G06, G09, G11 largest (priority 1); G01, G05, G10, G13 middle (2); G02, G03, G07, G08, G12 smallest (3).
5. **Labels.** Label `requests_g12` with `request_id` only. Set the label class to draw only when zoomed in past a stated scale (e.g., 1:5,000) so that the whole-area view is not cluttered; on the printed job sheet, labels are on. Do not label `reporter_note` (12.7.3).
6. **Context and hierarchy.** Style `station_road_g12` as a mid-grey line; `wards_g12` as a thin grey outline with no fill; label wards with `ward` in small grey text. No basemap (there is no Earth position to place one under). Check that no ward fill competes with the points.
7. **Scale range for detail.** On the request layer, set a **Visibility Range** so that the layer always draws, and on its label class a range that hides labels beyond 1:5,000 [X05]. Record both numbers in your rationale.
8. **The close pair.** Zoom to G02/G03 at 1:2,000 and at 1:10,000 and record whether the two symbols separate at each (hand prediction from 12.3.2: at 12 pt they separate at 1:2,000 and merge at 1:10,000). Decide how the whole-area view handles the pair (count label, cluster, or accept the merge with a note) and write it down.
9. **Grayscale check.** On the **View** tab, **Accessibility** group, choose **Color Vision Simulator ▸ Achromatopsia**, then one of the red/green modes [X03]. Confirm that category (shape), priority (size), and status (fill) are all still readable. Exit the simulator [X03].
10. **Layout.** Insert a layout at A4; add a map frame; from the **Insert** tab, **Map Surrounds** group, add a **Legend** [X07] and try a **Scale Bar** [X06] (**Verification item:** behaviour on an undefined coordinate system — if it cannot be labelled, draw a 1,000 m line as a graphic and label it "1,000 m on the training grid"). Add the title "Open requests — W06/W07 — crew sheet, 2026-09-15 (synthetic)", the source line, the accuracy note ("positions ± 15 m"), and the edge-case note ("G06 on the W06/W07 edge — assigned to W06 under Policy W-1"). No north arrow; write "schematic grid — not oriented to north". Export to PDF.

**Part B — the management map (district, A4 print).**

11. **Join the summary.** Join `ward_summary_g12` to `wards_g12` on `ward` (Chapter 10, 10.3 — one-to-one, 16 rows to 16 rows). Expected after the join: 16 features, `open_requests` populated for 15, NULL for W16. Verify with a select `open_requests IS NULL` → 1 (W16) and `open_requests = 0` → 1 (W03).
12. **Three classifications of the count, for the comparison table.** Symbology ▸ **Graduated Colors** on `open_requests`, 3 classes [S37]. Set the method to **Equal Interval**, record the breaks and the class of every ward; repeat for **Quantile** and **Natural Breaks (Jenks)** [S36]. Compare with the table in 12.4.1; every membership should match (**Verification item:** the exact break labels printed by the installed release, I.6). Screenshot each legend *with its numeric labels* for the rationale.
13. **The rate.** Still in Graduated Colors, set **Normalization** to `households` [S37]. The values are now per household (0.0155 for W12 …); to show *per 1,000* either compute a field `rate_per_1000 = open_requests / households * 1000` and symbolise that, or keep the normalised value and write the legend labels as per-1,000 figures. Use **Manual** classes with the breaks **2.5** and **5.0** (upper bound inclusive): classes ≤ 2.5, > 2.5 to ≤ 5.0, > 5.0. Expected membership (hand-checked, Table F12-1): class 1 = W03, W01, W02, W07, W09, W05 (**6**); class 2 = W06, W10, W14, W04, W08, W13, W15 (**7**); class 3 = W11, W12 (**2**); W16 excluded. State the reason for manual classes in the notes (ties at 2.0, 4.0, and 5.0).
14. **Show the missing ward as missing.** On the Symbology pane's **More** menu, check **Show excluded values** to add an `<excluded>` class [S37] (or use **Show values out of range**, also on that page); give W16 a hatch or neutral texture that is not on the ramp; label the legend entry "No data — export not received". Expected: exactly one feature in that class.
15. **Legend labels.** Edit the class labels so that they read as intervals with units: "0 – 2.5", "> 2.5 – 5.0", "> 5.0 open requests per 1,000 households" (the page's **Format labels** and label editing [S37]). Never leave "Low/High".
16. **Hierarchy.** Ward outlines thin; ward codes as small labels; *no* request points, no road, no household labels. Sequential single-hue ramp (light to dark), not red/green.
17. **Grayscale check** as in step 9; the hatch for W16 must still differ from the palest class.
18. **Layout.** A4; title "Open service requests per 1,000 households — District G-12 wards — 2026-09-15 (synthetic training data)"; legend with the three intervals and the no-data entry; a text block with the full method note of 12.6.1's worked example; the caption "238 open requests over 15 wards; W16 not received"; the 1 km reference (scale bar or drawn line); no north arrow. Export to PDF.
19. **The source-value table.** Export, or type, a table with one row per ward: `ward, open_requests, households, road_km, rate_per_1000, class_count_EI, class_count_Q, class_count_NB, class_rate_manual, note`. This is the deliverable the instructor uses to check that the maps did not alter or hide any value.
20. **Rationale.** One page: for each of the nine faults of Map B-12, what you changed on which map and why; the design decisions that *differ* between the two maps (Table 12.1-A is the frame); the decisions you considered and rejected; and every verification item you hit.

### 12.8.5 Expected results and validation checks (hand-checked; not observed in software)

| Check | Expected | If it differs, first suspect |
| --- | --- | --- |
| Open requests drawn on the crew map | 13 (G01–G13) | Definition query text or case (`'OPEN'`) |
| Category counts among open | Pothole 5, Streetlight out 4, Blocked drain 2, Fallen tree 1, Water leak 1 | Typo in the CSV; a value in `<all other values>` |
| Largest symbols | G04, G06, G09, G11 | Size mapping reversed (1 must be largest) |
| G02/G03 at 12 pt | merged at 1:10,000; separate at 1:2,000 | Symbol size not in points; on-screen scale nominal (12.1.2) |
| Joined wards | 16; NULL count 1 (W16); zero count 1 (W03) | Join key case/whitespace; NULL read as 0 on import — *stop and fix the import* |
| Equal interval membership | 9 / 2 / 4 (12.4.1) | Range not 0–45 (a NULL treated as 0 does not change the range here; a NULL treated as −1 or 9999 would) |
| Quantile membership | 5 / 5 / 5 | Product's tie rule; a 16th value present |
| Natural breaks membership | 7 / 4 / 4 | Implementation's partition for near-ties (record it) |
| Rate manual classes | 6 / 7 / 2, W16 excluded | Boundary convention (2.5 and 5.0 must be in the *lower* class); normalisation not per 1,000 |
| Legend | numeric intervals with units; a no-data entry with one ward | Labels left at defaults |
| Caption | "238 over 15 wards; W16 not received" | NULL replaced by 0 (would read "16 wards") |
| Grayscale | shapes/sizes/fills readable; hatch ≠ palest class | Colour-only distinctions |
| Source-value table | every value equals Table F12-1 | A value edited to "fix" the map — never do this |

The **invariant** the instructor checks first: the sum of `open_requests` over the source-value table is 238, W16 is NULL in the table and "no data" on the map, and W03 is 0 in the table and in the lowest class on the map. A map that looks better than Map B-12 but fails that invariant fails the lab.

### 12.8.6 Troubleshooting

| Symptom | Likely cause | What to do |
| --- | --- | --- |
| W16 draws in the palest class | NULL became 0 on import, or the excluded class is off | Check the attribute table for W16; re-import with an explicit NULL; enable Show excluded values [S37] |
| Ward W03 disappears | Zero excluded with the NULL, or a definition query `open_requests > 0` | Zero is a value; remove the exclusion |
| Scale bar shows no units or refuses | Undefined coordinate system | Verification item; use the drawn 1 km reference line |
| Natural breaks gives 6 / 5 / 4 or similar | Implementation detail or a 16th value | Confirm 15 values are classified; record observed breaks in I.8; the membership in 12.4.1 is the mathematical expectation |
| Quantile gives unequal classes | Ties or a 16th value | Confirm W16 is excluded; record |
| Two symbols for G02/G03 never separate | Symbol size in map units rather than points, or reference scale set | Check the symbol unit; clear the map's reference scale |
| Labels overlap everywhere | No scale range on the label class | Set the label class visibility range [X05] |
| Colour Vision Simulator not on the ribbon | View not active, or an older release | Activate the map or layout view [X03]; record the release |
| Join yields 0 matches | Key mismatch (`W01` vs `W1`, trailing spaces) | Inspect both key fields (Chapter 10, 10.3) |

### 12.8.7 Required learner deliverables

1. The Map B-12 diagnosis (step 0).
2. The two exported PDFs (crew map; management map) and, for the management map, the three screenshots of the count legends by method.
3. The source-value table (step 19), unchanged in values from Table F12-1, with the class columns filled in.
4. The one-page rationale (step 20), including the list of design choices that differ between the two maps and every verification item encountered.
5. The grayscale-check note: which mode you used and what, if anything, you changed as a result.

### 12.8.8 What the instructor verifies

That the source-value table matches Table F12-1 exactly; that the class memberships match 12.4.1 and step 13 (or that any difference is explained by a recorded implementation detail, not by an edited value); that W16 is visibly "no data" and W03 visibly zero; that the caption says 15 wards; that no personal detail from `reporter_note` appears on any map; that the crew map's symbol sizes are justified for the reading scale; and that the rationale explains the differences between the two maps in terms of *reader decisions*, not taste. Styling that conceals a discrepancy — a NULL turned into 0, a value edited, a ward dropped — fails the lab regardless of how good the map looks.

### 12.8.9 QGIS alternative (equivalent foundational exercise; not execution-tested)

The exercise is the same; the controls differ, and two behaviours must be verified rather than assumed.

- **Load** the CSVs with **Add Delimited Text Layer** (WKT geometry for wards and the road; X/Y for requests), leaving the geometry CRS unset if the installed release allows it (Chapter 3's verification item). Join `ward_summary_g12` to `wards_g12` with the layer's **Joins** property on `ward`.
- **Crew map.** Layer Properties ▸ Symbology ▸ **Categorized** on `category` [Q02]; edit each category's marker to a distinct shape. Priority by size: use a data-defined size on the marker, or three rule-based classes. Filter with the layer's **Query Builder** (`"status" = 'OPEN'`). Labels on `request_id` with scale-dependent visibility.
- **Management map.** Symbology ▸ **Graduated** on `open_requests`, Mode **Equal Interval**, **Equal Count (Quantile)**, **Natural Breaks (Jenks)** in turn, 3 classes [Q02]; record memberships. For the rate, add a virtual or calculated field `"open_requests" / "households" * 1000` and classify it with Mode set to a manual arrangement (edit the class values to 2.5 and 5.0). **Verification item:** how the graduated renderer draws W16 (NULL) — the manual section read does not state it [Q02]; the ward must not vanish. If it is undrawn, add a rule-based layer copy that draws `"open_requests" IS NULL` with a hatch, above the graduated layer.
- **Grayscale check.** **View ▸ Preview Mode ▸ Simulate Achromatopsia Color Blindness (Grayscale)** and one of the Protanopia/Deuteranopia modes [Q01].
- **Layout.** Print Layout with a map item, legend, scale bar (**Verification item:** behaviour without a layer CRS), title and text items; export to PDF.
- Deliverables and validation checks are identical to 12.8.5–12.8.7. Do not claim the class labels or the NULL handling match ArcGIS Pro without checking; record what each product shows.

### 12.8.10 Optional web route — ArcGIS Online Map Viewer (not execution-tested)

Only for learners with an organisational account, and only for the styling part (the planar fixture cannot be published as an Earth-referenced layer — Chapter 7 — so this route uses the Earth-referenced Fixture E12 of 12.9.2, or a copy of G-12 that the instructor has placed in a documented projected CRS purely for display, labelled as such). In Map Viewer: **Styles ▸ Types (unique symbols)** on `category` for the crew map; **Counts and Amounts (color)** on `open_requests` with **Divide by** set to `households`, **Classify data** on with a chosen method and 3 classes, and **Show features with no value** on with a distinct style and the label "No data" for the management map [A01] [A02]. Configure the pop-up's field list to exclude `reporter_ref` and `reporter_note` [A03] — and write in your rationale, citing 12.7.1, why that exclusion is presentation and not protection.

## 12.9 Independent check and Phase 1 exit assessment

Answers are not in this section. Submit everything to the instructor, who marks against Instructor Appendix I.2–I.4. State every assumption; an answer that is correct under a stated assumption scores, an answer that silently picks one does not.

### 12.9.0 Concept questions (six) and scenario questions (two)

**Q1 (12.1; LO1).** A single web map is proposed "for crews and managers": ward choropleth of `open_requests` underneath all open request points. Name the decision each reader must make, state one way the combined map damages each decision, and specify the *minimum* content of each of the two maps that should replace it.

**Q2 (12.2; LO2).** A printed map shows District G-12 (4 km wide) at 10 cm width. (a) Give its representative fraction. (b) Is it larger- or smaller-scale than 1:25,000, and does it show more or less detail? (c) A request on it was captured with ± 15 m accuracy and stored with six decimal places; the reader zooms an on-screen copy to 1:250. Which of the four quantities of Table 12.2-B changed, and did the position become more trustworthy?

**Q3 (12.3; LO3).** A layer of assets has `asset_type` (Streetlight, Drain, Tree) and `condition` (integer 1–5 from Chapter 8's scale, 5 = best). A colleague symbolises `asset_type` with a light-to-dark blue ramp and `condition` with five unrelated hues. Say what is wrong with each choice, propose the encoding for each, and state the grayscale test each must pass.

**Q4 (12.4; LO4).** Using the fifteen F12-1 counts, a fourth method is proposed: **defined interval** of width 10. List the classes and their members, then state one question for which this classification is better than natural breaks and one for which it is worse. (Esri's page describes the defined-interval method [S36]; reason from its definition.)

**Q5 (12.5; LO5).** Ward P has 24 open pothole requests, 6,000 households, and 12 km of road; Ward Q has 18, 2,000 households, and 3 km of road (synthetic). Compute requests per 1,000 households and per km of road for each, say which ward is "worse" under each, and state which denominator you would use for a road-resurfacing decision and why. Then write the title the map is allowed to have.

**Q6 (12.6; LO6).** A management map's legend reads: "0–5 / 5–10 / 10–20 / 20+" with a grey class labelled "0". List every defect in that legend (there are at least three), and rewrite it for the F12-1 counts with natural breaks so that a value of exactly 5 or 10 would be unambiguous.

**S1 (12.2, 12.7; LO2, LO7).** The councillor for W07 receives the crew map as a PDF and replies: "Your map shows G06 on our side of the line, so it is W07's job, not W06's — and I see you've hidden the reporter details in the pop-up, so I assume the public version is safe to post." Write the reply in no more than five sentences. It must (i) explain why the drawing cannot decide G06's ward, and what does; (ii) explain what hiding a pop-up field does and does not do; (iii) name the kind of requirement "safe to post" is and where it is met.

**S2 (12.4, 12.5; LO4, LO5).** The monthly report shows two quantile choropleths of `open_requests` (August and September) with identical legends "Low / Medium / High". Between the months, W12 fell from 31 to 12 and W15 from 45 to 20; nothing else changed. Predict what the September map shows for W10 and W11 and why; explain why the pair misleads; specify the fix (what to fix, what the legend must say); and state what the report should have shown instead of raw counts if the question was "where are residents most affected".

### 12.9.1 Independent practical task — critique an unfamiliar map ("Map Z-12", synthetic)

You receive a map you have never seen, from a different fictional town, as an image plus its legend text. Its content (everything below is synthetic):

| Element | As it appears on Map Z-12 |
| --- | --- |
| Title | "Streetlight Risk — Harbour Town, 2026" |
| Body | Six zones (Z1–Z6) as a choropleth; a basemap with street names; a north arrow; no scale bar |
| Variable (from the legend) | "Streetlight faults" — a count of *all* streetlight requests, open and closed, per zone |
| Classes (legend) | "0–4 · 4–9 · 9–20 · 20–60 · 60+" — five classes for six zones; natural breaks (stated in a footnote) |
| Colours | white → yellow → orange → red → dark red; Z4 is drawn in a hatched grey with no legend entry |
| Zone facts printed in a side table | Z1: 3 faults, 400 lamps, data 2025; Z2: 8, 900, 2025; Z3: 19, 2,100, 2025; Z4: (blank), 700, —; Z5: 57, 3,800, 2026; Z6: 62, 1,200, 2026 |
| Source line | none; footer says "Prepared for the Council" |
| Notes | none |

**Do.** Write a critique of no more than 500 words that answers, with reference to the side table:

1. **What is being measured?** State exactly what the mapped number is and is not (open versus all; faults versus reports).
2. **Which comparisons are supported?** Compute faults per 1,000 lamps for Z1, Z2, Z3, Z5, Z6 and say whether the ranking by count survives. Say whether Z1–Z3 (2025) may be compared with Z5–Z6 (2026) at all.
3. **What is missing?** List every missing or defective context element (title claim, units, Z4's legend entry, date, source, scale, class-boundary ambiguity, method note).
4. **Which conclusions overreach?** The cover note says "Z6 is the most dangerous zone and Z4 is fine." Address both halves.
5. **What would you ask for** before redrawing it (two items), and write the title the redrawn map is allowed to carry.

Show your arithmetic; round rates to one decimal.

### 12.9.2 Integrated Phase 1 practical — Fixture E12 (Earth-referenced, synthetic, documented defects)

This is the blueprint's Appendix B.2 exit practical. It uses every chapter. Work alone; the oral (12.9.4) follows it.

**The package (synthetic).**

`wards_e6.geojson` — unchanged from Chapter 6 (6.8.3): Wards A and B, EPSG:4326, positions longitude first (RFC 7946); A spans 74.990–75.000° E, B 75.000–75.010° E, both 23.000–23.010° N.

`ward_register_e12.csv`

```csv
ward,ward_name,edition,current,households
A,West ward,2019,N,1090
A,West ward,2024,Y,1150
B,East ward,2024,Y,880
```

`roads_e12.csv` (readme: "digitised for training in EPSG:32643; WKT in metres")

```csv
road_id,name,wkt
RE-2,Depot Road,"LINESTRING (499200 2544300, 500800 2544300)"
```

`requests_e12.csv` (readme: "exported from the training tracker 2026-09-15; CRS WGS 84 / UTM zone 43N (EPSG:32643); E_m, N_m in metres; status OPEN or CLOSED at export; reported_on local date; reported_time_ist local time")

```csv
request_id,category,status,reported_on,reported_time_ist,reporter_ref,E_m,N_m
X1,Pothole,OPEN,2026-08-12,09:14,R-1001,499500.000,2544200.000
X2,Streetlight out,OPEN,2026-08-25,18:40,R-1002,500300.000,2544450.000
X3,Blocked drain,OPEN,2026-09-03,07:55,R-1003,500000.000,2544400.000
X4,Pothole,OPEN,2026-08-19,11:20,R-1004,499100.000,2544300.000
X5,Water leak,CLOSED,2026-08-02,14:05,R-1005,500600.000,2544000.000
X6,Pothole,OPEN,2026-08-12,09:17,R-1001,499500.000,2544200.000
X7,Fallen tree,OPEN,2026-09-08,16:30,R-1007,74.995,23.005
X8,Streetlight out,OPEN,2026-08-30,20:10,R-1008,2544180.000,499700.000
X9,Pothole,OPEN,2026-09-01,10:00,R-1009,501200.000,2544300.000
X10,Blocked drain,,,,R-1010,500200.000,2544350.000
```

**The business question.** *"For each ward, how many requests that were OPEN at export and reported on or after 2026-08-01 lie within 150 m (boundary-inclusive, ≤) of Depot Road RE-2? Express the answer also per 1,000 households, and show it on a map the ward services manager can read without you in the room. Then rerun with 125 m and explain what changed."*

**Required work (each item maps to the chapter that taught it).**

1. **Intake review (Chapters 5, 7, 9).** Before any operation, read every file and its readme. Record the CRS, units, and axis order of each; list every record you suspect and why. There are defects; find them from the *values* (ranges, symmetry, duplicates, blanks), not from this text.
2. **Geometry and schema explanation (Chapters 3, 8).** State the geometry type and role of each layer; state the key relationships (requests → wards by location; wards → register by `ward` and `current`).
3. **CRS and unit justification (Chapters 5, 6).** Choose the CRS in which the distance test is performed, say why, and say what happens to the 150 m threshold if the test were run in EPSG:4326.
4. **Quality log (Chapter 9).** For each defect: evidence, decision (correct / exclude / flag), the corrected value if any, and the source of the correction. Do not delete records; do not invent observations. One record's status is blank — decide what a blank status *means* and report the record separately rather than assuming.
5. **Query (Chapter 10).** Write the eligibility condition and predict its IDs before running it.
6. **Basic analysis (Chapter 11).** Compute each request's distance to RE-2 (predict on paper first — the road is axis-aligned, so the distances are readable from the coordinates), the near set at 150 m, ward membership with Policy W-1, the per-ward count, the reconciliation table (assigned + multiply assigned + unassigned = eligible), and the rate per 1,000 households using the *current* register row.
7. **Changed threshold (Chapter 11, 11.8.2).** Rerun at 125 m; state which IDs change and why; keep the zero-count ward on the map.
8. **Final map and table (this chapter).** One management map (rate per ward, disclosed classes — with two wards, state the values directly rather than classifying; the legend must still show zero versus no-data handling), with title, legend, units, source/date, method note (threshold, inclusivity, policy, CRS, defects handled), and limitations. One operational table listing the qualifying request IDs with distances.
9. **Transfer (Chapters 2, 10, 11).** In one paragraph: how the same workflow would be done in PostGIS or QGIS, and which three implementation details you would recheck there (name them precisely — e.g., the ≤ behaviour of the distance function, the boundary behaviour of the containment predicate, the axis order of the GeoJSON loader).
10. **Limitations.** What the count does and does not establish (proximity ≠ travel time; reports ≠ faults; rate ≠ risk; the ± accuracy of capture; the duplicate policy).

**Submit.** Data outputs (corrected request layer with a `qc_flag` field; near set; per-ward table), the method log (tool, version, parameters, CRS, transformation if any), validation evidence (prediction/result table with every distance and membership), the map (PDF or image), the transfer paragraph, and the limitations.

### 12.9.3 What passing Phase 1 establishes — and what it does not

Passing this chapter's assessment and the integrated practical establishes that you can take a spatial question from intake to a defensible map: read data with its CRS and units, detect and log defects, query and analyse with stated predicates and thresholds, and communicate the result without concealing its limits. It establishes **readiness for supervised platform training** — Phase 2, where these judgments are implemented systematically in ArcGIS Pro and then in ArcGIS Online.

It does **not** establish readiness to administer a production ArcGIS Enterprise deployment, to set sharing and security for real data (12.7.2 deferred exactly those decisions), to design a production schema for a real authority, or to make accuracy claims about real datasets. The blueprint is explicit on this point, and so is this chapter.

### 12.9.4 Oral explanation (one)

Bring your 12.9.2 outputs. The instructor will (a) point at one request on your map and ask you to state, from the coordinates and not from the drawing, its ward and its distance to RE-2 and why it did or did not qualify; (b) ask what would have happened to your count if you had *not* found one specific defect they choose; and (c) ask you to say, in under a minute, why hiding `reporter_ref` from the map does not protect it and what would. Three minutes total.

### 12.9.5 Submission requirements and scoring

**Submit:** written answers to Q1–Q6 and S1–S2; the critique of 12.9.1; the full 12.9.2 package; the 12.8 lab deliverables (12.8.7); attend the oral.

**Scoring (100 points; weights adapted from blueprint Appendix B.3 for the exit chapter).**

| Component | Points | Criteria |
| --- | --- | --- |
| Concept questions Q1–Q6 | 18 (3 each) | Correct under stated assumptions; scale arithmetic right; encodings match measurement type; classes and rates computed correctly |
| Scenario S1–S2 | 12 (6 each) | Drawing versus coordinates; presentation versus protection; the recalculated-quantile effect predicted and the fix specified |
| Critique 12.9.1 | 15 | What is measured; rates computed and ranking re-assessed; the 2025/2026 mix refused; missing elements listed; both overreaching claims addressed; honest title |
| Integrated practical 12.9.2 — interpretation and data/model understanding | 7 | Intake complete; every layer's role and relationship stated |
| — CRS, units, measurement | 9 | Test CRS chosen and justified; degree-threshold error explained; X7's unit mix and X8's swap detected and corrected with evidence |
| — Query/analysis correctness and quality handling | 11 | Eligible set, distances, near set, edge case, duplicate, blank status, per-ward counts, rates, and reconciliation all correct; 125 m rerun explained |
| — Reproducibility and validation | 5 | Log sufficient to rerun; prediction/result table complete |
| — Communication and map | 8 | Map elements complete; zero versus no-data; limitations honest; transfer paragraph names rechecks precisely |
| Guided lab 12.8 | 5 | Source-value invariant holds; both maps fix all nine faults; rationale in terms of decisions |
| Oral | 10 | Ward and distance from coordinates; defect consequence stated; presentation-versus-protection explained |

**Pass threshold:** 80 points, *and* no critical misconception. The blueprint's list (Appendix B.3) applies in full — treating CRS assignment as reprojection; treating degrees as metres; silently guessing coordinate order or CRS; concealing duplicate or unmatched counts; claiming more accuracy than the evidence supports — and this chapter adds: replacing missing with zero on a map; presenting a count as a rate-based comparison or a rate as risk; recalculating classes across a comparison without saying so; treating a hidden field or a switched-off layer as protection; reading a drawn position at high zoom as evidence of which side of a boundary a point lies on. Any of these requires remediation (Instructor Appendix I.4) and a fresh exercise (I.5) before Phase 2, regardless of score.

**Progression.** Another trainee must be able to reproduce your 12.9.2 per-ward counts from your log and the package *without* your outputs, and you must be able to explain any discrepancy by defect decision, predicate, threshold inclusivity, or CRS — not by pointing at a screenshot.

---

## 12.10 Media and source brief

This module records what the presentation, audio, and any interactive material must contain; the Media Appendix (M.1–M.5) carries the detailed specifications.

1. **Side-by-side maps with identical data and one changed choice.** Three pairs on District G-12, each with the source-value table visible beside the maps so that the viewer can see that no number changed: (a) `open_requests` classified by equal interval / quantile / natural breaks (the 12.4.1 table as three panels); (b) count versus rate per 1,000 households (12.5.2 — W15 dark on the left, W12 dark on the right); (c) W16 as "no data" (hatched) versus W16 as a false zero (palest class), with the caption changing from "15 wards + 1 not received" to the wrong "16 wards". Every panel uses the values printed in this chapter; nothing is generated from unseen data.
2. **An optional 3D lesson** (M.5-2) compares extruded thematic columns — each ward raised in proportion to its count or rate — with the 2D choropleth, to show occlusion (a tall W15 column hides W11 behind it from some viewpoints) and perspective distortion (nearer columns look taller). Every frame carries the label "column height = open requests per 1,000 households (thematic encoding), not building height"; the columns are labelled with their values so that the viewer can check the encoding against the table. A 2D fallback (the choropleth plus a bar chart) is provided for every point the 3D scene makes.
3. **Sources:** map elements and scale [S35]; symbols and attributes [S23]; classification [S36]; normalisation and the excluded/out-of-range classes [S37]; the additional pages cited in this chapter for symbology families [X01] [X02], colour-vision simulation [X03] [Q01], scale and scale ranges [X04] [X05], layout elements [X06] [X07] [X08], pop-ups and views [X09] [A03] [A04] [R01], and the Map Viewer styles [A01] [A02]. **Transition to Phase 2:** the judgments of this chapter — purpose, scale, encoding, classes, denominators, context, and the presentation/protection line — are implemented systematically in ArcGIS Pro (symbology, labelling, layouts, map series) and then published through ArcGIS Online, where the protection requirements deferred in 12.7 are finally configured.

---

## Glossary

| Term | Meaning in this chapter |
| --- | --- |
| **Operations map** | A map whose reader must locate and act on individual features (the crew map); the unit shown is the feature (12.1.1). |
| **Management map** | A map whose reader compares areas; the unit shown is the area with one value each (12.1.1). |
| **Medium / viewing size / interaction** | Where, how large, and how interactively the map is read; they decide sizes, scale ranges, and what must be visible by default (12.1.2). |
| **Map scale / representative fraction (RF)** | Ratio of map distance to ground distance, e.g. 1:25,000; the denominator is the scale denominator (12.2.1). |
| **Large-scale / small-scale** | Large fraction (small denominator, small area, much detail) / small fraction (large denominator, large area, little detail) (12.2.1). |
| **Verbal scale / graphic scale (scale bar)** | Scale as a sentence / as a labelled bar that stays correct when the map is resized (12.2.1, 12.6.1). |
| **Cell size / resolution** | Ground size of one raster cell; not positional accuracy (12.2.2; Chapter 4). |
| **Coordinate precision** | How finely a coordinate is written; not accuracy (12.2.2; Chapter 5). |
| **Positional accuracy** | Closeness of a recorded position to the truth; a property of the capture method (12.2.2; Chapter 9). |
| **Generalisation** | Deliberate simplification of geometry and detail to suit scale and purpose (12.2.3). |
| **Scale range / visibility range** | The scales between which a layer or label class draws (12.2.3). |
| **Nominal / ordinal / quantitative** | Categories without order / ordered values without meaningful distance / numbers with meaningful differences (12.3.1). |
| **Unique values (unique symbols)** | One symbol per category value (12.3.1). |
| **Graduated colours / graduated symbols / proportional symbols** | Classes of a colour ramp / classes of symbol size / unclassed size in proportion to value, for quantitative data (12.3.1). |
| **Visual hierarchy** | Foreground operational data, supporting context, subdued basemap (12.3.2). |
| **Symbol footprint** | The ground area a symbol covers at a given scale (paper size × scale denominator) (12.3.2). |
| **Grayscale / colour-vision check** | Rendering the map without colour, or with a simulated colour-vision deficiency, to test that essential distinctions survive (12.3.3). |
| **Classification / class breaks** | Dividing a numeric range into classes; the boundaries between them (12.4). |
| **Equal interval** | Classes of equal value width (12.4.1). |
| **Quantile** | Classes with equal numbers of features (12.4.1). |
| **Natural breaks (Jenks)** | Classes minimising within-class variance and maximising between-class difference (12.4.1). |
| **Manual / defined interval** | Author-specified breaks / classes of a specified width (12.4.2; Q4). |
| **Fixed classes** | Breaks held constant across a set of maps that will be compared (12.4.3). |
| **Choropleth** | Areas filled by class of a value belonging to each area (12.5.1). |
| **Total / rate / density** | Count / count per unit of an exposure count / count per unit of area or length (12.5.1). |
| **Denominator (normalisation)** | The exposure quantity by which a total is divided to make areas comparable (12.5). |
| **Reporting exposure** | The tendency of an area's population to report; a hidden component of any request rate (12.5.3). |
| **Map elements** | Title, legend, scale, orientation aid, source/date (acknowledgement), method notes, border (12.6.1). |
| **Zero versus missing** | A value of 0 (classified) versus the absence of a value (excluded, drawn off the ramp, named in the legend) (12.6.2). |
| **Aggregation unit** | The area over which a value is summarised; a value describes the unit, not the places inside it (12.6.3). |
| **Presentation control** | Layer visibility, pop-up fields, filters, labels, symbology — changes to what a map shows (12.7.1). |
| **Data protection / access control** | Sharing, views, editing rights — changes to what a client can obtain; the later chapters' subject (12.7.2). |
| **Hosted feature layer view** | An ArcGIS Online item referencing a source layer with its own field, feature, and editing restrictions and its own sharing (12.7.2). |
| **Data minimisation (for a map)** | Building the map from only the fields its purpose needs (12.7.3). |
| **Source-value table** | The table of every value behind a map, delivered with it so that styling cannot hide a discrepancy (12.8). |

## Recap

Start with the reader's decision and the medium; an operations map locates features for a crew, a management map compares areas for a manager, and one map cannot do both. Remove whatever does not serve the decision. Scale is a fraction — 1:1,000 is *large* scale and shows more detail than 1:100,000 — and it is one of four different numbers: scale, cell size, coordinate precision, and positional accuracy, of which only accuracy says how close a drawn position is to the truth; zooming and decimals add none of it. Match the symbol to the variable's type: unique symbols for categories, graduated colour or size for magnitudes, with the order stated for ordinal codes; build a hierarchy; check the map in grayscale. Classification is analysis: equal interval, quantile, and natural breaks put the same fifteen wards into different classes with no change to any value, so the map must print its intervals and units, and a comparison over time or between areas must fix its classes. A count is workload; a rate needs a denominator that matches the question — households, assets, road length — and no rate proves risk. Title, legend, units, source/date, and method notes always; scale bar and orientation only when they help; zero classified and missing excluded, drawn off the ramp, and named; unmatched and edge cases carried in the notes; an area value never describes each place inside it. Hiding a field or a layer is presentation; protection is sharing and views, and belongs to the later chapters — but a map should never carry personal details it does not need. And every map ships with its source-value table, so that no styling can conceal a discrepancy.

## Cross-references to later material

- **Phase 2 — ArcGIS Pro.** Implementing this chapter's judgments as product skills: symbology and label classes, layouts and map series, style files, and the classification and normalisation controls used in 12.8; reference scales and scale-based symbol sizing (the on-screen/print problem of 12.1.2).
- **Phase 2 — ArcGIS Online administration.** The protection requirements deferred by 12.7.2: item sharing levels, hosted feature layer views with excluded fields and definitions, editing privileges, and the verification item recorded in 12.7.2 (an excluded field must be absent from the view's REST output).
- **Later — ArcGIS Enterprise.** Serving the same maps and views on infrastructure the organisation operates; nothing in Phase 1 establishes readiness for that (12.9.3).
- **Later — web development.** OpenLayers, Mapbox, and the ArcGIS Maps SDK draw features from style rules and leave the legend, the scale range semantics, and the no-data rule to you (12.2.3, 12.4.2, 12.6.2 platform notes); the disclosures this chapter requires become code.

## References

All pages were opened and read on **19 September 2026**. Esri "latest" pages are mutable; earlier chapters recorded the ArcGIS Pro release shown on the check date as 3.7 — record the installed version when running the lab. QGIS links use the pinned 3.44 edition. Two `pro.arcgis.com` URLs redirected to `doc.esri.com` on the check date; the final URLs are listed.

| ID | Publisher — page title | Link | Used for |
| --- | --- | --- | --- |
| S23 | QGIS 3.44 — A Gentle Introduction to GIS: Vector Attribute Data | https://docs.qgis.org/3.44/en/docs/gentle_gis_introduction/vector_attribute_data.html | Single symbol; graduated symbols for value ranges; continuous colour; unique values for string attributes; planning symbology |
| S35 | QGIS 3.44 — A Gentle Introduction to GIS: Map Production | https://docs.qgis.org/3.44/en/docs/gentle_gis_introduction/map_production.html | Common map elements; title, legend, north arrow, scale, acknowledgement; RF 1:25,000 and the 100 mm × 25,000 check; small scale = large area; the "lower the map scale" wording (instructor note in 12.2.1) |
| S36 | Esri — Data classification methods — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/layer-properties/data-classification-methods.html | Manual, defined, equal interval, quantile (and its warning), natural breaks (Jenks) and its unsuitability for comparing maps, geometric interval, standard deviation |
| S37 | Esri — Graduated colors — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/layer-properties/graduated-colors.html | Choropleth term; Normalization by a field, percentage of total, log; out-of-range and null values; Show excluded values; Use feature values in labels; Format labels; vary by size/transparency |
| S18 | RFC Editor — RFC 7946: The GeoJSON Format | https://www.rfc-editor.org/rfc/rfc7946 | Position order (§3.1.1) and precision (§11.2), as cited via Chapter 5 |
| S10 | QGIS 3.44 — A Gentle Introduction to GIS: Vector Data | https://docs.qgis.org/3.44/en/docs/gentle_gis_introduction/vector_data.html | Scale-dependent representation (point versus polygon), via Chapter 3 |
| X01 | Esri — Symbolize feature layers — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/layer-properties/symbolize-feature-layers.html | Symbology families and their one-line definitions (single symbol, unique values, graduated colors/symbols, proportional, unclassed, dot density, charts, heat map) |
| X02 | Esri — Unique values — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/layer-properties/unique-value.html | Qualitative categories; `<all other values>` class; grouping many classes |
| X03 | Esri — Use the Color Vision Deficiency Simulator tool — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/get-started/color-vision-deficiency-simulator.html | View tab, Accessibility group; Protanopia/Deuteranopia/Tritanopia/Achromatopsia; not included in exports or sharing; exiting |
| X04 | Esri — Map scales and scale properties — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/navigation/map-scales-and-scale-properties.html | Definition of scale; accepted scale formats; scale list location |
| X05 | Esri — Display layers at certain scales — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/layer-properties/display-layers-at-certain-scales.html | Visible scale range purpose and examples; Feature Layer tab, Visibility Range group; Layer Properties General tab |
| X06 | Esri — Scale bars — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/help/layouts/scale-bars.html | Definition; updates with map frame scale; scale varies with coordinate system, latitude, direction, extent; Insert ▸ Map Surrounds |
| X07 | Esri — Add a legend — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/help/layouts/add-a-legend.html | Legend purpose; patch and text; static versus dynamic; Insert ▸ Map Surrounds |
| X08 | Esri — Layouts in ArcGIS Pro — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/help/layouts/layouts-in-arcgis-pro.html | Page layout definition; elements; "what you see … is what you will get" |
| X09 | Esri — Configure pop-ups — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/navigation/configure-pop-ups.html | Fields element; visible-fields option; changes exclusive to the pop-up display |
| X10 | Esri — Basemaps — ArcGIS Pro documentation | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/map-authoring/author-a-basemap.html | Basemap as reference; background versus reference layers relative to operational layers |
| A01 | Esri — Apply styles — ArcGIS Online Help | https://doc.arcgis.com/en/arcgis-online/create-maps/apply-styles-mv.htm | Map Viewer style names: Location, Types (unique symbols), Counts and Amounts (color/size), Heat map, and others |
| A02 | Esri — Style numbers — ArcGIS Online Help | https://doc.arcgis.com/en/arcgis-online/create-maps/style-numbers-mv.htm | Divide by; Classify data and number of classes; Show features with no value |
| A03 | Esri — Configure pop-ups — ArcGIS Online Help | https://doc.arcgis.com/en/arcgis-online/create-maps/configure-pop-ups-mv.htm | Fields list (rearrange, remove, select); Enable pop-ups toggle; number formatting |
| A04 | Esri — Create hosted feature layer views — ArcGIS Online Help | https://doc.arcgis.com/en/arcgis-online/manage-data/create-hosted-views.htm | View as a separate item referencing the source; excluding fields; sharing and editing separately from the source |
| R01 | Esri — Query (Feature Service/Layer) — ArcGIS REST APIs | https://developers.arcgis.com/rest/services-reference/enterprise/query-feature-service-layer/ | `outFields` description; `*` returns all field values |
| Q01 | QGIS 3.44 — User Guide: QGIS GUI (View menu, Preview Mode) | https://docs.qgis.org/3.44/en/docs/user_manual/introduction/qgis_gui.html | Preview modes: Normal, Monochrome, Achromatopsia (Grayscale), Protanopia, Deuteranopia, Tritanopia |
| Q02 | QGIS 3.44 — User Guide: The Vector Properties Dialog (Symbology) | https://docs.qgis.org/3.44/en/docs/user_manual/working_with_vector/vector_properties.html | Categorized renderer and "all other values"; graduated modes (Equal Count (Quantile), Equal Interval, Fixed Interval, Logarithmic, Natural Breaks (Jenks), Pretty Breaks, Standard Deviation) |
| — | This course — Chapter 4 (4.3.1), Chapter 5 (5.2.2), Chapter 9 (9.2–9.3), Chapter 10 (10.3, 10.7.2), Chapter 11 (11.6, 11.8.3), Chapter 6 (Fixture E6, I.3) | local files | Recap table values; Policy W-1; E6 ward corners in EPSG:32643; the count-per-ward workflow |

*Not consulted, and therefore not cited:* pages on ArcGIS Pro labelling, map series, style files, or Web Mercator scale variation (Chapter 6 covers the last). The `pro.arcgis.com` "legends.html" URL guessed from the layout section returned "page not found"; the "Add a legend" page [X07] was read instead.

---

# Instructor Appendix (separate from learner material)

Learners should not receive this appendix before the progression gate in 12.9. Everything here was derived by hand from the printed fixtures; nothing was executed in software. Section I.6 lists what must be run and observed before the lab is issued.

## I.1 Answers and reasoning — comprehension checks

| Check | Answer and reasoning |
| --- | --- |
| 12.1 | (a) e.g. "which lamps in W11 do we repair this week?" (operations) versus "does W11 need more lighting budget than other wards?" (management). (b) Individual streetlight requests/assets versus the ward. (c) The streetlight asset layer belongs on the operations map only; a household or lamp-count denominator belongs on the management map only. Accept any pair that keeps the unit of interest distinct. |
| 12.1.2 | Differ: text and symbol sizes (projector needs far larger), number of classes (fewer for thirty seconds), amount of note text (minimal on the slide, full in the PDF). Identical: the values, the class intervals, the legend's meaning, and the no-data treatment — the *content* must not change between renderings of the same map. |
| 12.1.3 | Outlines keep (the unit); ward codes keep (identification); Station Road remove (no role in a ward comparison); the 16 points remove (they are the other map); household labels remove from the map face — the denominator belongs in the notes and the source-value table, not as sixteen numbers competing with the colours. |
| 12.2.1 | (a) 4,000 m ÷ 0.08 m = 50,000 → 1:50,000. (b) Smaller scale than 1:25,000 (larger denominator); less detail. (c) 12 m ÷ 50,000 = 0.24 mm — not drawable to scale; a symbolic line. |
| 12.2.2 | (a) Coordinate precision; no. (b) Cell size/resolution; no — resampling to 10 m invents no new observations (Chapter 4, 4.5). (c) Map scale; no. (d) Positional accuracy; **yes** — the only one. |
| 12.2.3 | "G06 is stored at exactly x = 2000, which is the shared edge, so the coordinates — not the drawing at any zoom — decide its ward, and Policy W-1 assigns it to W06. With ± 15 m capture accuracy the tree may be on either side in reality; the map cannot tell you which, and neither can zooming." |
| 12.3.1 | (a) quantitative → graduated colour (polygons) or size; (b) nominal → unique symbols; (c) ordinal → ordered shades or sizes with "5 = best" in the legend; (d) date — ordered; encode as age classes (graduated) with the classes stated; never as unrelated hues; (e) nominal → unique symbols (the language of the label does not change the type). |
| 12.3.2 | 12 pt = 4.23 mm; × 2,000 = 8.47 m ground footprint; 8.47 m < 11.18 m, so G02 and G03 just separate (a gap of about 2.7 m at ground scale, 1.4 mm on screen). At 1:2,500 (10.6 m) they touch. |
| 12.3.3 | Whichever ramp class has the same lightness as the grey — typically the lightest or second-lightest class of a yellow-to-red ramp, since a mid-grey and a pale yellow both render as light grey. Fix: give W16 a *texture* (hatch) that no ramp class has, and label it; a solid grey of any lightness collides with some class. |
| 12.4.1 | Two classes. **Equal interval:** width 22.5 → 0–22.5 = {0,2,3,4,5,6,7,13,14,16,17} (11 wards), 22.5–45 = {31,35,40,45} (4). **Quantile:** 15 ÷ 2 = 7.5 — not an integer; a rule must be stated (e.g. 8/7 by taking the lower class as the first eight: {0…13} / {14…45}, or 7/8); mark down answers that give a split without saying how the odd value was placed. **Natural breaks:** {0–17} / {31–45}, within-class sums 360.9 + 110.75 = 471.7, versus {0–7}/{13–45} = 34.86 + 1,155.8 = 1,190.7 and {0–14}/{16–45} = 180 + 713.3 = 893.3 — the gap between 17 and 31 is the natural break. |
| 12.4.2 | "Open service requests per ward — count at 2026-09-15, Policy W-1 (edge → lower ward code). Natural breaks, 3 classes. ▮ 0 – 7 · ▮ 13 – 17 · ▮ 31 – 45 · ▨ No data — export not received (W16). No ward has 8–12 or 18–30 open requests." Accept any wording that carries: the variable, the unit/definition, the date, the method and count, the numeric intervals with the gaps visible, and the separate no-data entry. |
| 12.4.3 | "Classify the first (before) map by natural breaks once, then fix those breaks as manual classes for the after map. Print the intervals on both legends with the words 'classes fixed across both maps'. Recalculating is acceptable only for a map that will be read alone and is labelled as classified on its own values — never inside a comparison." |
| 12.5.1 | Total right for "how many crew-days of work are in each ward?" (workload). Total wrong for "which ward's residents are worst served?" — denominator households (or assets for a single category). |
| 12.5.2 | The number is right (0.0155 open requests per household). The legend is unreadable: multiply by 1,000 and label "15.5 per 1,000 households"; apply the same constant to every class; state the constant in the legend title. |
| 12.5.3 | Denominator: number of recorded trees per ward, from the assets layer (Chapter 3). Still misleading because: reporting exposure differs by ward; storm exposure differs by month; tree size/age is not in the count; a tree recorded in the assets layer may be gone. Allowed title: "Open fallen-tree requests per 100 recorded trees, by ward, 2026-09-15 (synthetic)" — not "tree risk". |
| 12.6.1 | Must be present: title (with date), legend readable in black and white (shapes/sizes/fills), source line, method/accuracy note (± 15 m; Policy W-1 for G06), the 1 km reference. May be dropped: an orientation aid — the grid is not oriented to north; if a north arrow were present it would assert a falsehood. |
| 12.6.2 | W16 now has a value (9) and is classified (class 13–17 under the fixed classes of 12.4.3; class 8–17 if those were used). W03's count is *unknown*: it leaves the classification and takes the no-data symbol with the legend entry "No data — export filter error; count unknown". Caption: "247 open requests over 15 wards; W03 unknown" (238 + 9 = 247; W03's former 0 is no longer a value). |
| 12.6.3 | "Values are ward averages of open reports per 1,000 households at 2026-09-15; they do not describe individual streets or properties. Where requests are located within a ward is shown on the operational map, not on this one." The other map: the operational (points) map. |
| 12.7.1 | (a) presentation; (b) an access setting — protection-type, and in this case inconsistent (public readers of the map cannot load an organisation-only layer); (c) protection; (d) presentation. |
| 12.7.2 | Presentation: publish a request map; show status. Protection: residents must not obtain notes or reporter references (a view that excludes the fields, shared publicly, with the source shared to the organisation only); only crews may update status (editing settings on the source or an editable view shared to the crew group). When: not by symbology in this chapter — by the administrator using the settings of the later chapters, *before* the map is shared; until then the map is not published. |
| 12.7.3 | "I will include a short, office-written `location_hint` (e.g. 'second lamp from the corner') that has been checked for personal detail. I will not include `reporter_note` itself or `reporter_ref` in any pop-up, label, or export. The office adds the hint when triaging the request, so the useful part reaches the crew without the free text leaving the internal system." |

## I.2 Answer key — 12.9.0 concept and scenario questions

**Q1.** Crew decides where to go and in what order; manager decides where capacity goes. The combined map damages the crew's decision because the ward fill competes with (and at small scales hides) the points, and the ward value tells the crew nothing about location; it damages the manager's because the dots read as density and the count is not a comparable variable. Minimum crew map: open points with category (shape) and priority (size), IDs, road, ward outlines, accuracy note. Minimum management map: wards with one disclosed-class rate (or a count labelled as workload), no-data entry, method note, source/date, source-value table.

**Q2.** (a) 4,000 m ÷ 0.10 m = 40,000 → 1:40,000. (b) Smaller scale than 1:25,000; less detail. (c) Only *map scale* changed (the on-screen 1:250 rendering); precision (six decimals) and accuracy (± 15 m) are unchanged; the position is exactly as trustworthy as before — ± 15 m.

**Q3.** `asset_type` is nominal; a ramp asserts an order Streetlight < Drain < Tree that does not exist — use unique symbols with distinct shapes. `condition` is ordinal; unrelated hues destroy the order — use one hue in five ordered shades (or five sizes) with "5 = best" in the legend. Grayscale test: asset types must remain distinguishable by shape; condition must remain readable as an ordered sequence of greys (which a single-hue ramp gives and five hues do not).

**Q4.** Defined interval, width 10, starting at 0: 0–10 = {0,2,3,4,5,6,7} (W03, W02, W01, W04, W05, W06, W07; 7 wards); 10–20 = {13,14,16,17} (W08–W11; 4); 20–30 = empty; 30–40 = {31,35,40} (W12, W13, W14; 3 — 40 exactly on the break, so the convention must be stated; if the upper bound is exclusive, 40 moves up); 40–50 = {45} (W15; plus W14 under the exclusive convention). Better than natural breaks for "how many wards exceed 30?" and for month-to-month comparison (the classes are fixed by definition); worse for showing the data's own clusters compactly (an empty class and a split of the 31–45 cluster). **Verification item:** whether the installed release displays an empty 20–30 class; Esri's page states the number of classes is determined automatically from the interval [S36].

**Q5.** P: 24 ÷ 6,000 × 1,000 = 4.0 per 1,000 households; 24 ÷ 12 = 2.0 per km. Q: 18 ÷ 2,000 × 1,000 = 9.0 per 1,000; 18 ÷ 3 = 6.0 per km. Q is "worse" under both denominators (and P is "worse" by count). For resurfacing, use per km of road: the generator of potholes is road, not households. Title: "Open pothole requests per km of road, by ward, [date] (synthetic)" — not "road condition" or "risk".

**Q6.** Defects: (1) overlapping bounds — 5 and 10 each belong to two classes; (2) no units or definition; (3) the grey "0" class treats zero as if it were not a value and looks like a no-data symbol, while any genuinely missing ward has no entry; (4) no date/method. Rewrite: "Open requests per ward, count at 2026-09-15, Policy W-1; natural breaks, 3 classes: 0 – 7 · 13 – 17 · 31 – 45 · No data — export not received (W16)." With feature-value labels the gaps make 5 and 10 unambiguous (5 is in 0–7; 10 does not occur); if continuous labels are required, write "> 7 – 17" and "> 17 – 45".

**S1.** Model reply: "The map cannot decide G06's ward: at any zoom the symbol is a drawing, and G06's stored coordinates put it exactly on the shared edge (x = 2000) with ± 15 m capture accuracy, so the assignment comes from the stated Policy W-1, which counts an edge request in the lower-coded ward, W06. Hiding a field in the pop-up changes only what this map displays; the field stays in the layer and is returned by any query of the service, so it protects nothing. 'Safe to post' is a data-protection requirement, not a map-design one; it is met by the layer's sharing settings and a view that excludes those fields, which the administration chapters cover, and until that is done the public version is not safe." (Four or five sentences; must contain the three elements.)

**S2.** September quantile top five by value: 16 (W10), 17 (W11), 20 (W15), 33 (W13), 38 (W14) → W10 and W11 turn "High" although unchanged; W12 (12) drops to "Medium". The pair misleads because each map's classes are recomputed from its own values while the legends are identical words; a reader concludes W10/W11 worsened and may not see that W12 improved most. Fix: fix the classes (e.g. 0–7 / 8–17 / 18–45 from August's natural breaks) as manual classes on both maps; legend states the numeric intervals and "classes fixed across both months". For "where residents are most affected", show a rate per 1,000 households (with the same fixed-class discipline) and say it measures reports, not risk.

## I.3 Expected results — 12.8 lab, 12.9.1 critique, and 12.9.2 practical (hand-checked; software reproduction required)

**Natural-breaks arithmetic for 12.4.1 (three classes).** Partition {0–7}/{13–17}/{31–45}: class means 3.857, 15.0, 37.75; within-class sums 34.86, 10.00, 110.75; total **155.6**. Alternatives: {0–6}/{7–17}/{31–45} = 23.33 + 61.2 + 110.75 = 195.3; {0–7,13}/{14–17}/{31–45} = 108 + 4.67 + 110.75 = 223.4; {0–7}/{13–17,31}/{35–45} = 34.86 + 214.8 + 50 = 299.7; {0–5}/{6–17}/{31–45} = 14.8 + 106.8 + 110.75 = 232.4. The three-cluster partition is the minimum of every alternative tried; an exhaustive check of all 91 partitions was not performed by hand and is unnecessary given the size of the gaps. For the September values of 12.4.3: {0–7}/{12–20}/{33–38} = 34.86 + 43.3 + 12.5 = 90.7 versus {0–7}/{12–17}/{20–38} = 34.86 + 17.2 + 172.7 = 224.8.

**12.8 lab.** As 12.8.5: 13 open; category counts 5/4/2/1/1; priority-1 set {G04, G06, G09, G11}; 16 joined wards with one NULL and one zero; count classes EI 9/2/4, Q 5/5/5, NB 7/4/4; rate manual classes 6/7/2 with W16 excluded; caption 238 over 15 wards; G02/G03 merge at 1:10,000 (42 m footprint) and separate at 1:2,000 (8.5 m). The source-value table must equal Table F12-1.

**12.9.1 critique — expected content.** Rates per 1,000 lamps: Z1 3 ÷ 400 × 1,000 = **7.5**; Z2 8 ÷ 900 × 1,000 = **8.9**; Z3 19 ÷ 2,100 × 1,000 = **9.0**; Z5 57 ÷ 3,800 × 1,000 = **15.0**; Z6 62 ÷ 1,200 × 1,000 = **51.7**. The ranking by count (Z6 > Z5 > Z3 > Z2 > Z1) happens to survive normalisation, but the *magnitudes* change: Z6 is 3.4 × Z5 per lamp, not 1.09 ×; Z2 and Z3 are nearly identical per lamp although Z3 has more than twice the count. Z1–Z3 (2025) and Z5–Z6 (2026) are different years and must not be compared on one map without saying so — the year must be a fixed window. The variable is *all* streetlight requests (open and closed, i.e. reports over an unspecified period), not "faults" and not "risk". Missing: date window, source, scale bar, units in the legend, a legend entry for Z4's hatch, the boundary convention (4, 9, 20, 60 each appear in two classes), and a method note. Overreach: "Z6 is the most dangerous" — the map shows reports per zone, not danger; per lamp Z6 is high, but reporting exposure and the 2026-only window are unexplained. "Z4 is fine" — Z4 has *no data*; the hatch means unknown, not zero. Ask for: the request status and date window per zone, and the lamp count's date; plus the source of the counts. Allowed title: "Streetlight requests per 1,000 lamps by zone, [window], Harbour Town (synthetic)".

**12.9.2 practical — expected results (hand-checked).**

*Defects to be found:* X6 duplicates X1 (same coordinates, same reporter R-1001, same day, three minutes later — Chapter 9's double-submit pattern; flag, count once); X7 has degrees in the metre columns (74.995, 23.005 — values far below any UTM easting/northing; its true position is Chapter 6's Q3, E 499,487.611, N 2,544,073.271, inside Ward A); X8 has E and N swapped (2,544,180 / 499,700 — an easting of 2.5 million is impossible in a UTM zone; corrected to E 499,700, N 2,544,180); X10 has blank status, date, and time (status unknown — report separately, do not assume OPEN); the register has a stale 2019 row for A (use `current = Y`: A 1,150; B 880). X5 is CLOSED (excluded by the condition, not a defect). X9 lies east of Ward B (E 501,200 > the projected east edge ≈ 501,024.8) — outside both wards, not a defect.

*Distances to RE-2 (planar, EPSG:32643; RE-2 runs from E 499,200 to 500,800 at N 2,544,300):*

| ID | Corrected (E, N) | Nearest point on RE-2 | Distance (m) | Ward (Policy W-1) |
| --- | --- | --- | --- | --- |
| X1 | (499500, 2544200) | perpendicular | 100 | A |
| X2 | (500300, 2544450) | perpendicular | **150** (exactly the threshold) | B |
| X3 | (500000, 2544400) | perpendicular | 100 | on the A/B edge (E = 500,000 is the 75° meridian) → **A** by tie-break |
| X4 | (499100, 2544300) | west endpoint (499200, 2544300) | 100 | A (E 499,100 is inside A's projected west edge ≈ 498,975.2) |
| X5 | (500600, 2544000) | perpendicular | 300 | B — CLOSED, excluded |
| X6 | = X1 | — | 100 | duplicate of X1 — flagged, not counted |
| X7 | (499487.611, 2544073.271) | perpendicular | 226.729 | A — not near |
| X8 | (499700, 2544180) | perpendicular | 120 | A |
| X9 | (501200, 2544300) | east endpoint (500800, 2544300) | 400 | outside both wards — not near |
| X10 | (500200, 2544350) | perpendicular | 50 | B — status unknown; reported separately |

*Eligible (OPEN, reported ≥ 2026-08-01, after de-duplication):* X1, X2, X3, X4, X7, X8, X9 — 7 records (X6 excluded as duplicate; X5 CLOSED; X10 status unknown). *Near at 150 m (≤):* X1, X2, X3, X4, X8 — 5. *Per ward under W-1:* **A = 4** (X1, X4, X8 interior; X3 boundary tie-break), **B = 1** (X2). *Raw boundary-inclusive matches:* A 4, B 2 — six matches, five distinct requests (X3 in both). *Reconciliation over the near set:* 4 interior-assigned + 1 boundary-assigned + 0 unassigned = 5 ✓ (over the eligible set: X7 and X9 are eligible but not near; X9 is additionally outside both wards). *Rates:* A 4 ÷ 1,150 × 1,000 = **3.48** per 1,000 households; B 1 ÷ 880 × 1,000 = **1.14**. *X10:* "1 request with unknown status within 50 m of RE-2 in Ward B — not counted; status to be confirmed." *At 125 m:* X2 (150) drops; X8 (120) stays; **A = 4, B = 0** — B must remain on the map as a zero-count ward with rate 0.0, not as no-data. *Common wrong answers and their causes:* A = 5 (X6 not de-duplicated); A = 3 (X8 not corrected — its raw position is thousands of kilometres away and falls outside everything); B = 2 at 150 m (X10 assumed OPEN); B = 0 at 150 m (threshold applied as < 150); X3 in both wards' counts (raw matches reported as the policy count); a rate of 3.67 for A (the stale 2019 register row with 1,090 households used instead of the current row: 4 ÷ 1,090 × 1,000 = 3.67). *Tolerance note:* if the trainee reprojects the requests to EPSG:4326 instead of the wards to EPSG:32643, X3's longitude may come back as 74.999999… or 75.000000…; the assignment must then be made by the stated policy with an explicit tolerance, not by whichever side floating point lands on — Chapter 10's lesson.

## I.4 Common mistakes and remediation

| Mistake | Symptom | Remediation |
| --- | --- | --- |
| One map for both readers | Choropleth under points | Redo Table 12.1-A for the case; require the two-map deliverable |
| "Large scale" used for large area | Scale terminology reversed in the rationale | Repeat the checked ratios (12.2.1) with the trainee's own page size; 1:1,000 versus 1:100,000 on a 20 cm square |
| Zoom or decimals read as accuracy | "The point is clearly on the W07 side" | Table 12.2-B; the G06 exercise; the ± 15 m provenance |
| Ramp on a category; hues on an order | `status` on a red-green ramp | The measurement-type table of 12.3.1; grayscale test |
| Default classification, undisclosed | "Low/High" legend | Hand-classify the fifteen values by all three methods (12.4.1); write the legend of 12.4.2 |
| Recalculated classes in a comparison | Identical-looking monthly maps | The 12.4.3 September example; fixed manual classes |
| Count presented as comparison; rate presented as risk | "Risk map" title | Blueprint arithmetic (A 10 vs B 20 per 1,000); the denominator table of 12.5.3; rewrite the title |
| NULL replaced by 0 | "16 wards, 238" caption; W16 palest | 12.6.2 table; Show excluded values; the caption invariant |
| Pop-up hiding treated as protection | "Safe to post" | 12.7.1's REST `outFields` point; classify the requirement (12.7.2) |
| Personal detail on the map | `reporter_note` labelled | 12.7.3 review table; rebuild from a minimal field list |
| Values edited to "fix" the map | Source-value table ≠ F12-1 | Fail the lab; explain that the map communicates the analysis, it does not replace it |

## I.5 Fresh exercise for retesting (different values, same concepts)

Give a 3 × 3 grid of wards V1–V9 (1 km squares, planar, no CRS) with open requests `4, 9, 1, 22, 6, NULL, 8, 30, 5` (V6 = NULL, "not received") and households `2000, 3000, 500, 2000, 6000, 1000, 800, 10000, 2500`. Ask for: the three classifications of the eight numeric values into two classes (state the odd-count rule for quantile); the rate per 1,000 households (V4 = 11.0, V7 = 10.0 lead; V8 = 3.0 despite the largest count); the legend with a no-data entry; the caption ("85 over 8 wards; V6 not received" — 4 + 9 + 1 + 22 + 6 + 8 + 30 + 5 = 85); and a one-paragraph note on why V8's count does not make it the "worst" ward. Natural breaks (two classes) for `1, 4, 5, 6, 8, 9, 22, 30`: {1–9} / {22–30} — class means 5.5 and 26; within-class sums 41.5 (values 1, 4, 5, 6, 8, 9: 20.25 + 2.25 + 0.25 + 0.25 + 6.25 + 12.25) and 32 (16 + 16); total 73.5, versus {1–6}/{8–30} = 14 + 338.75 = 352.75. Equal interval two classes: width 14.5 → {1–9} / {22–30} as well. Quantile 8 ÷ 2 = 4: {1,4,5,6} / {8,9,22,30} — 8 and 9 move up.

## I.6 Building and verifying the lab in software — not execution-tested

Nothing below has been run by the author. Record the application versions and the observed values in I.8.

1. **Build the packages.** `Chapter12_G12.gpkg` (or a file geodatabase) from the five CSVs of 12.8.3 with an *undefined* coordinate system, by the Chapter 3 I.5 routes; confirm 16 wards, 1 road, 16 requests; confirm that `open_requests` for W16 is NULL (not 0, not empty string) after the build — this is the single most important check. Fixture E12: reuse `wards_e6.geojson`; leave the three CSVs as CSVs so that the trainee performs the intake.
2. **Render Map B-12** from its specification (12.8.3) and export it as an image for the learners; keep the project so that the faults are reproducible.
3. **Undefined coordinate system behaviour.** Record what ArcGIS Pro reports as the map's coordinate system and units; what the Scale Bar element does on the layout; what the scale box shows; and the same in QGIS's print layout. Decide whether the drawn 1 km line is needed.
4. **Symbology controls.** Confirm the control for varying unique-values symbol size by `priority` in the installed release (step 4), and the three-step size list; confirm that the `<all other values>` class is empty with five categories.
5. **Classification.** Run Graduated Colors on `open_requests` with 3 classes by Equal Interval, Quantile, and Natural Breaks; record the printed breaks and the class of every ward; compare with 12.4.1 (expected 9/2/4, 5/5/5, 7/4/4). Then Normalization = `households`, manual breaks; record how the normalised values display and whether per-1,000 labels require a computed field. Confirm the effect of Show excluded values / Show values out of range on W16 [S37]. Repeat in QGIS [Q02], recording how the graduated renderer draws W16 (NULL).
6. **Colour-vision simulation.** Confirm the View ▸ Accessibility ▸ Color Vision Simulator options and that exports do not include the simulation [X03]; confirm QGIS Preview Mode entries [Q01].
7. **Scale ranges and labels.** Confirm the label-class visibility range control [X05] and that a 12 pt symbol behaves as predicted at 1:2,000 and 1:10,000 for G02/G03 (record the on-screen result and the exported PDF result separately).
8. **Optional web route.** If used, place a documented projected copy of G-12 (state the CRS and that it is for display only) or use E12; confirm Types, Counts and Amounts with Divide by and Show features with no value [A01] [A02]; confirm pop-up field removal [A03]; do **not** present the copy as a real location.
9. **E12 in software.** Build the road from WKT in EPSG:32643; load the requests as XY (Chapter 6, 6.8) — note that the loader will fail or misplace X7 and X8 until corrected, which is intended; project the wards to EPSG:32643; confirm the distances of I.3 with Near or the QGIS equivalent, the boundary behaviour for X3, and the per-ward counts under the Contains-type predicate; record everything in I.8.
10. **Run both routes end to end** and enter observed values beside every prediction before issuing the lab.

## I.7 Blueprint corrections, source notes, and verification items

- **No factual error was found in the blueprint's Chapter 12 text.** Its checked ratio (1:1,000 more local detail than 1:100,000) and its arithmetic (A 100 ÷ 10,000 → 10 per 1,000; B 60 ÷ 3,000 → 20 per 1,000) are correct.
- **Source wording to watch [S35].** The QGIS Map Production page's sentence "the lower the map scale, the more detailed the feature information in the map will be" reads naturally as "lower denominator = more detail", which is correct, but can be misread as its opposite. The chapter's 12.2.1 instructor note explains; the learner text uses only the unambiguous forms.
- **Source availability.** A guessed `…/help/layouts/legends.html` URL returned "page not found"; "Add a legend" [X07] was read instead. Two `pro.arcgis.com` pages redirected to `doc.esri.com`; the final URLs are cited.
- **Normalization wording [S37].** The page says "To normalize the data, choose a field from the Normalization menu" and also offers percentage of total and log; the chapter relies on the field option. A summariser could read the page as offering only percentage/log — instructors should read the sentence themselves.
- **Hosted view field exclusion [A04].** The view page states that fields can be excluded from a view; the REST-level behaviour (absence from `outFields=*` on the view) was **not** verified in this chapter and is recorded as a verification item for the administration chapters (12.7.2).
- **QGIS graduated renderer and NULL [Q02].** The manual section read does not state how a NULL value is drawn; verification item (I.6 step 5).
- **Jenks implementations.** The chapter's natural-breaks memberships are the minimum-variance partition checked by hand against the neighbouring partitions; software may print different break labels. Record observed breaks.
- **Boundary convention for class breaks.** The chapter avoids data values exactly on equal-interval breaks (none of the fifteen values equals 15 or 30) and uses manual breaks with a stated convention for the rate map; Q4 deliberately puts 40 on a defined-interval break so that the trainee must state a convention. Which convention each product uses is a verification item.
- **Undefined coordinate system.** Scale-bar and unit behaviour on the planar fixture in both products is unverified (I.6 step 3).
- **Verification items (collected).** Undefined-CRS scale bar/units in Pro and QGIS; unique-values size-by-attribute control; observed class breaks for all three methods in both products; QGIS NULL rendering in the graduated renderer; Show excluded values behaviour; label visibility range control; G02/G03 separation on screen versus PDF; Map Viewer Divide by and no-value style; hosted view REST field exclusion (deferred); E12 loader behaviour for X7/X8; Near distances and X3 boundary behaviour in E12.

## I.8 Instructor change log

| Date | Change | Affected sections | Rechecked |
| --- | --- | --- | --- |
| 2026-09-19 | Initial draft from blueprint revision 1.0; sources read on this date; no software execution | all | — |
| (fill in) | Observed values from I.6 entered; verification items resolved | 12.3.3, 12.4.1, 12.6.1, 12.8, I.3 | 12.8.5 table; E12 key; M.5 validation cases |

---

# Media Appendix

## M.1 Slide outline (mapped to module IDs)

| # | Module | Slide title | Content | Question before the answer |
| --- | --- | --- | --- | --- |
| 1 | — | Chapter 12 — the map is the interface | One dataset, two honest maps, one dishonest one (Map B-12 thumbnail) | "What decision does this map's reader have to make?" |
| 2 | 12.1.1 | Two readers, two decisions | Table 12.1-A | "Which map does the crew supervisor need?" |
| 3 | 12.1.2 | Medium and size | A4 at 1:25,000; phone at 1:28,600 → 1:2,000; the 12 pt symbol at each | "How wide is 12 pt on the ground at 1:25,000?" |
| 4 | 12.1.3 | What can go? | The keep/remove table for the crew map | "Does the ward shading help the crew?" |
| 5 | 12.2.1 | Scale is a fraction | 1:1,000 versus 1:100,000 on the same 20 cm square; the three forms | "Which is large scale?" |
| 6 | 12.2.2 | Four numbers that are not each other | Table 12.2-B | "Which one says how close to the truth?" |
| 7 | 12.2.3 | Zoom adds nothing | G06 at 1:200 with a ± 15 m circle drawn to scale (30 m wide = 15 cm on screen at 1:200) | "Which side of the line is the tree?" |
| 8 | 12.3.1 | Type decides the encoding | Nominal/ordinal/quantitative table; the F12-2 design | "Ramp or shapes for `category`?" |
| 9 | 12.3.2 | Hierarchy and the oversized symbol | Footprint table 6/12/24 pt | "At what scale do G02 and G03 separate?" |
| 10 | 12.3.3 | The grayscale check | The same crew map in colour and in achromatopsia simulation | "What did we lose?" |
| 11 | 12.4.1 | One table, three methods | The 15 values; three class tables (Figure M.3-2) | "Which class is W11 in?" |
| 12 | 12.4.2 | Disclose the breaks | The bad legend versus the good legend | "What does 'Medium' mean?" |
| 13 | 12.4.3 | Comparing months | August/September quantile pair (W10/W11 turn dark) versus fixed classes | "Which wards actually changed?" |
| 14 | 12.5.1–2 | Totals and rates | The blueprint's A/B arithmetic; the F12-1 three rankings | "Which ward is worst?" — three answers |
| 15 | 12.5.3 | Which denominator? | The category/generator table; reporting exposure | "What generates fallen-tree requests?" |
| 16 | 12.6.1 | Context that earns its place | The elements table; the notes block | "Does this grid have a north?" |
| 17 | 12.6.2 | Zero is not missing | W03 versus W16 (Figure M.3-3); the caption | "How many wards in the total?" |
| 18 | 12.6.3 | An area value is not every place | W12's 15.5 versus its streets; the overreaching sentence corrected | "Does every street in W11 have problems?" |
| 19 | 12.7.1 | `display: none` is not authorisation | Pop-up field off versus `outFields=*` | "Is the field gone?" |
| 20 | 12.7.2–3 | Presentation or protection? | The requirement-classification table; the G08 note | "Where is this requirement met?" |
| 21 | 12.8 | Lab: redesign Map B-12 | The nine faults; the two deliverable maps; the source-value invariant | — |
| 22 | 12.9 | Exit assessment and what it establishes | Critique, E12 practical, oral; readiness statement | — |

## M.2 Audio-lesson outline

Pronounce: "choropleth" (KLOR-o-pleth), "quantile", "Jenks", "EPSG thirty-two six four three". Introduce every unit the first time ("open requests per one thousand households"). Describe each figure in words before drawing a conclusion from it; pause before each answer so listeners can predict.

1. **Opening (12.1).** "Same data, two readers." Read Table 12.1-A row by row as a dialogue: "The crew asks *where*; the manager asks *which ward*." Pause: "Which map needs the points?"
2. **Scale (12.2).** Say the fraction out loud: "one to one thousand — one centimetre is ten metres. One to one hundred thousand — one centimetre is one kilometre." Then: "Large scale is the *large fraction* — zoomed in." Read Table 12.2-B as four short definitions and finish with "only accuracy tells you how close to the truth." Describe G06 at 1:200: "the symbol is a drawing; the tree could be fifteen metres either way."
3. **Symbols (12.3).** "Categories get shapes. Order gets shades. Amounts get a ramp or a size." Describe the crew map as it would appear in grayscale: "shapes still differ; sizes still differ; hollow still differs from filled — it passes."
4. **Classification (12.4).** Read the fifteen values slowly. "Equal interval: nine, two, four. Quantile: five, five, five. Natural breaks: seven, four, four." Pause: "W11 has seventeen — say its class under each." Then the September story: "W10 and W11 turned dark and nothing changed in them."
5. **Rates (12.5).** Read the blueprint's arithmetic: "A, one hundred over ten thousand — ten per thousand. B, sixty over three thousand — twenty per thousand." Then the three F12-1 rankings, one denominator at a time, ending "three worst wards from one table — the denominator is the analysis."
6. **Context (12.6).** Read the notes block of 12.6.1 in full — it is the model. Say "zero is a value; missing is not" twice. Describe Figure M.3-3: "two pale wards on the wrong map; one pale and one hatched on the right one."
7. **Presentation versus protection (12.7).** "Hiding the field is CSS. The query still returns it." Read the requirement table and ask listeners to classify each row before the answer.
8. **Lab and exit (12.8–12.9).** Narrate only the nine faults and the invariant ("238 over fifteen wards, W16 not received"); detailed clicks stay in the handout. Close with the readiness statement of 12.9.3.

## M.3 Diagram specifications

All diagrams are **schematic drawings of the synthetic fixtures**, drawn to scale on the training grid, labelled "synthetic training grid — no CRS — not oriented to north". No generated artwork is presented as measured data.

- **M.3-1 — Two maps from one dataset (12.1).** Left: the crew map of W06–W07 — Station Road, ward outlines, 13 open requests as shapes sized by priority, closed ones hollow, `request_id` labels, a 1 km reference line, the ± 15 m note. Right: the management map of G-12 — sixteen squares, rate classes with the three intervals, W16 hatched, the caption. Between them the source-value table (Table F12-1) with an arrow to each map.
- **M.3-2 — One table, three classifications (12.4.1).** Three 4 × 4 grids side by side, each ward labelled with its value; three-step grey ramp; under each grid its legend with numeric intervals and the class counts (9/2/4; 5/5/5; 7/4/4). W16 hatched in all three. Below, a fourth pair for 12.4.3: August and September quantile grids with W10/W11 highlighted.
- **M.3-3 — Zero is not missing (12.6.2).** Two G-12 grids: "wrong" (W16 as false zero — same pale as W03; caption "16 wards, 238") and "right" (W16 hatched, legend entry "No data — export not received"; caption "238 over 15 wards; W16 not received").
- **M.3-4 — Symbol footprint (12.3.2).** A 200 m × 200 m patch of W06 around G02/G03 at three scales (1:25,000, 1:10,000, 1:2,000) with a 12 pt symbol drawn at its true ground footprint (106 m, 42 m, 8.5 m) and the two points 11.18 m apart; the pair merges, merges, separates.
- **M.3-5 — Four numbers (12.2.2).** A single request point with four annotations: "drawn at 1:25,000" (scale), "raster cell 100 m" (a grey square behind it), "stored 1500.000000" (precision), "captured ± 15 m" (a 30 m circle) — only the circle is labelled "this is the evidence".
- **M.3-6 — Count versus rate (12.5.2).** Two G-12 grids with a three-step ramp: left `open_requests` by natural breaks (W15/W14/W13/W12 dark), right rate per 1,000 households by the manual classes (W12/W11 dark); the table of three rankings beneath.
- **M.3-7 — Presentation versus protection (12.7.1).** A layered diagram: map (pop-up with `reporter_ref` unchecked) → layer/service (all fields) → REST query `outFields=*` returning them; beside it, a view item with the field excluded and its own sharing badge, labelled "later chapters".

## M.4 Table for slides — the chapter's checks at a glance

| Before publishing a map, ask… | Module | The test |
| --- | --- | --- |
| Whose decision is this map for, on what device? | 12.1 | One sentence each; remove what does not serve it |
| What is the scale, and what does it not tell me? | 12.2 | RF checked; accuracy stated separately |
| Does the encoding match the variable's type? | 12.3 | Categories → shapes; order → shades; amounts → ramp/size; grayscale render |
| Are the classes disclosed and, if compared, fixed? | 12.4 | Numeric intervals and units in the legend; method named |
| Is the variable comparable across areas? | 12.5 | Denominator named and justified; no "risk" |
| Title, legend, units, source/date, method, zero ≠ missing, limitations? | 12.6 | Notes block present; caption counts only the wards with data |
| Is anything "hidden" that must be *protected*? | 12.7 | Classify the requirement; route protection to administration |
| Can the source-value table be produced beside the map? | 12.8 | Every value matches the analysis |

## M.5 Interactive and 3D material

**M.5-1 — Interactive 2D "same data, different map" explorer (recommended; the principal media item).**

- *Learning objective:* see, with the source-value table always visible, how classification method, class count, denominator, and the missing-data policy change a choropleth of District G-12 without changing any value.
- *Objects:* the 16 wards of F12-1 with all attributes; a legend panel; a source-value table panel; a caption line.
- *Coordinates/units:* the training grid, metres, no CRS; "schematic — not oriented to north" fixed on screen.
- *Controls:* variable selector (`open_requests`, per 1,000 households, per km of road); method selector (equal interval, quantile, natural breaks, manual with editable breaks); class count 2–5; a "W16 = NULL / W16 = 0" toggle that also updates the caption; a "fixed classes across months" toggle with the September values of 12.4.3; a grayscale toggle; a "predict first" mode that hides the map until the learner types the class of a chosen ward.
- *Expected behaviour and validation cases (all hand-checked in this chapter):* counts, 3 classes → EI 9/2/4, Q 5/5/5, NB 7/4/4 with W11 in class 2/3/2; rate per 1,000, manual 2.5/5.0 → 6/7/2; per km → W15 4.5 and W11 4.25 in the top class; W16 = 0 toggle → caption changes to the wrong "16 wards" and W16 joins the palest class (shown with a red warning); September + recalculated quantile → W10/W11 dark; September + fixed → only W12 moves; grayscale → the hatch remains distinct.
- *Labels:* every legend entry numeric with units; a persistent footer "reports per household ≠ risk".

**M.5-2 — Optional 3D lesson: extruded thematic columns versus the 2D map (12.10).**

- *Learning objective:* recognise occlusion and perspective distortion in extruded thematic 3D, and read thematic height as an encoding, not as a physical height.
- *Objects:* the 16 ward squares extruded to a height proportional to the selected variable (count or rate); the same 2D choropleth beside the scene; a bar chart of the values as the 2D fallback.
- *Coordinates/units:* the training grid in metres horizontally; **vertical axis = data value × a stated factor** (e.g. 20 m per open request; 100 m per request per 1,000 households) — labelled on screen as "thematic height, not building height; vertical exaggeration by design".
- *Controls:* orbit and tilt; variable selector; a "top-down" button that flattens the view to the 2D map; a toggle to show value labels on column tops.
- *Expected behaviour and validation cases:* from a low south-west viewpoint the W15 column (45) hides part of W11 (17) and W07; the top-down view removes the occlusion and equals the 2D map; the W12 column becomes the tallest when the variable switches to rate per 1,000 households (15.5) and W15 shrinks to 5.0; column labels must equal Table F12-1; W16 is drawn as a flat hatched square with no column and a "no data" label — never as a zero-height column that reads as "zero".
- *Labels:* "synthetic; heights are data values; W16 has no value"; the 2D map and the bar chart are always visible so that the 3D scene never carries information on its own.
