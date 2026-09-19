# GIS and ArcGIS Fundamentals — Phase 1

# Chapter 6 — Projections, transformations, and accurate measurement

## Document metadata

| Item | Value |
| --- | --- |
| Chapter ID | Phase 1, Chapter 6 |
| Title | Projections, transformations, and accurate measurement |
| Source specification | `GIS_Phase_1_Chapter_Authoring_Blueprint.md`, revision 1.0 (18 September 2026) |
| Document revision | 1.0 — 19 September 2026 |
| Reference-check date | 19 September 2026 (every cited page was opened and read on this date; see the reference list for exact URLs) |
| Audience | Software developers at LiveBird Technologies who have completed Chapters 1–5 |
| Software referenced | ArcGIS Pro (documentation labelled "Released version: ArcGIS Pro 3.7" on the check date); QGIS Desktop 3.40 (long-term-release documentation edition); the QGIS *Gentle Introduction to GIS* 3.44 edition named in the blueprint; ArcGIS Maps SDK for JavaScript 5.1 and PostGIS (current online manual) for cross-platform notes only |
| Execution status | **No procedure in this chapter has been executed in ArcGIS Pro or QGIS by the author.** Interface steps were written from the official pages cited beside them and are marked *not execution-tested*. Every number in the chapter was produced by hand or by standard-library arithmetic applied to the published formulas cited beside it — **not** by GIS software. Instructors must reproduce the answer tables in the installed software before issuing the lab; the Instructor Appendix says exactly what to compare. |
| Data status | Every coordinate, ward, request, dataset name, and provenance note in this chapter is **synthetic training material**. The Earth-referenced fixture uses coordinates chosen for their arithmetic convenience (they sit on the central meridian of one UTM zone). The wards and requests are invented and describe no real municipality, boundary, or authority. |

### How to use this document

Read the modules in order; each one assumes the ones before it. Boxes labelled **Procedure (version-specific)** contain interface steps that will age; everything outside those boxes is durable concept. Boxes labelled **Platform note** separate ArcGIS or QGIS behaviour from general GIS principles, and boxes labelled **Verification item** tell an instructor what must be checked in the installed environment before a step is treated as fact. Comprehension checks have no answers in the learner text; answers are in the Instructor Appendix, which learners should not open before the progression gate in 6.9.

---

## Prerequisites

- **Chapter 5 completed (mandatory).** You can read a coordinate pair together with its coordinate reference system (CRS), units, and axis order; you can tell a geographic CRS (angles: degrees) from a projected CRS (a flat plane: metres or feet); you know EPSG:4326 and EPSG:3857 as identifiers whose full definitions must be looked up; you know that UTM is a family of zones, not one CRS; you know that GeoJSON stores longitude first; and you refuse to guess a CRS from the numbers alone.
- **Chapters 3–4.** Points, lines, polygons, vertices, attributes, layers, and rasters as grids of cells.
- **Chapters 1–2.** The municipal scenario, the policy/evidence distinction, and the roles of ArcGIS Pro, ArcGIS Online, and QGIS.
- Ordinary developer experience: metadata versus data, a function that changes a value versus one that changes a label, units in a data type, and reading a specification.
- **No calculus and no geodesy** are needed. Two trigonometric facts are used and explained where they appear: the cosine of an angle, and the idea that a scale factor is a ratio "map length ÷ ground length".

## Learning outcomes

By the end of this chapter you should be able to:

| ID | Outcome | Where it is taught | Where it is checked |
| --- | --- | --- | --- |
| LO1 | Explain that every map projection distorts something (area, shape/angles, distance, or direction), that "preserves" means "under particular conditions", and choose which property matters for a city measurement task versus a world map. | 6.1 | 6.9 concept Q1 |
| LO2 | Distinguish **assigning** a CRS (changing metadata only) from **transforming** data (computing new coordinates), name the operation for each in ArcGIS Pro, QGIS, and PostGIS, and predict what happens when the wrong one is used. | 6.2 | 6.9 concept Q2, scenario Q1, practical, oral |
| LO3 | Explain why changing between two geographic references (datums) needs a transformation as well as a projection, and read a transformation record for its area, accuracy, method, and required files. | 6.3 | 6.9 concept Q3 |
| LO4 | Explain on-the-fly display reprojection and fill in a worksheet separating input CRS, display CRS, processing/output CRS, transformation, and units for a given tool. | 6.4 | 6.9 concept Q4, scenario Q2 |
| LO5 | Distinguish planar from geodesic measurement, explain why degree differences are not metres, and state correctly what Web Mercator's metre units do and do not guarantee. | 6.5 | 6.9 concept Q5 |
| LO6 | Select a measurement method from the requirement (study area, property, accuracy, data quality, algorithm support, units) and keep straight-line, network, 2D, and 3D distances apart. | 6.6 | 6.9 concept Q6 |
| LO7 | Reproduce a comparison of projected, geodesic, and deliberately unsuitable planar measurements on verified points, and explain the differences by method and assumption rather than calling one "ground truth". | 6.7 | 6.9 practical |
| LO8 | Combine two correctly referenced layers into an analysis-ready copy, measure one distance and one area with units, log the transformation and method, and spot-check the result. | 6.8 | 6.8 lab; 6.9 practical |

## Required materials

- This document and a calculator (a phone calculator with `cos` and `ln` is enough for every hand check; no programming is required).
- The **Chapter 6 fixture E6** printed in this document (module "The recurring scenario and the Chapter 6 fixtures") and the two small input files in 6.8.3, which you create by copying text from this document.
- **For the guided lab (6.8):** ArcGIS Pro (any licence level; the Project, Define Projection, XY Table To Point, and Calculate Geometry Attributes tools are available at the Basic level according to the pages cited in 6.8) **or** QGIS Desktop 3.40. If neither is available, complete the worksheet parts of the lab on paper; the Instructor Appendix supplies the expected values.
- **Not required:** an ArcGIS Online account, credits, internet map services, or any downloaded transformation grid file. The lab is designed so that the two fixtures share the same geographic reference (WGS 84) and therefore need **no datum transformation**; 6.3 explains one from documentation instead.

## Recap of the preceding chapter

Chapter 5 ended the habit of treating "two numbers" as a location. A coordinate is meaningful only with its **coordinate reference system (CRS)**, its **units**, and the **axis order** of the format or API that carries it. A **geographic CRS** gives angles — latitude and longitude in degrees — on a chosen model of the Earth (an ellipsoid fixed by a **datum**). A **projected CRS** contains a geographic CRS *plus* a map projection, and gives flat-plane coordinates in linear units, usually metres [S14]. You met **EPSG:4326** (WGS 84, geographic) and **EPSG:3857** (Web Mercator, projected), learned that **UTM** is a family of 60 six-degree zones per hemisphere, and learned that RFC 7946 GeoJSON writes **longitude before latitude** regardless of the "latitude/longitude" phrase in prose [S18]. Chapter 5 also gave you a checklist for inspecting a dataset's CRS and the map's display CRS, and a firm rule: an unknown CRS is an investigation, never a guess.

Chapter 5 deliberately stopped before *doing* anything to coordinates. This chapter does the doing: it explains what a projection costs, separates two operations that beginners confuse (relabelling a CRS versus transforming coordinates), shows what a datum transformation is and how to read its evidence, separates "the layers line up on screen" from "the data are ready for analysis", and then teaches how to measure a distance and an area so that the number means what you claim it means.

## The recurring scenario and the Chapter 6 fixtures

The fictional municipality continues: **assets**, **roads**, **wards**, **requests**, and **inspections**, with the same five nouns. Two different kinds of fixture appear in this chapter, and the blueprint requires that they never be silently mixed:

1. **The planar training grid** from Chapters 1–5 (Wards A and B as 1 km squares, Road R1, requests P1–P6, metre units, *no Earth location and no EPSG code*). It is used once, in 6.1, to show what "no distortion" looks like. Nothing in it can be projected or transformed, because it is not on the Earth.
2. **Fixture E6 — an Earth-referenced fixture (synthetic).** New for this chapter. It is small enough to hand-check and is defined below. Its location was chosen for arithmetic convenience: the shared ward boundary lies exactly on longitude 75° E, which is the **central meridian of UTM zone 43N** (EPSG:32643) [E02]. That choice makes several results exact and explainable. It does not describe any real place.

**Table F6-1 — Fixture E6 wards (synthetic). Defined in WGS 84 (EPSG:4326), decimal degrees.**

| Ward | Longitude range (° E) | Latitude range (° N) | Shape in degrees |
| --- | --- | --- | --- |
| A | 74.990 to 75.000 | 23.000 to 23.010 | A 0.010° × 0.010° rectangle |
| B | 75.000 to 75.010 | 23.000 to 23.010 | A 0.010° × 0.010° rectangle |

Wards A and B share the edge at longitude 75.000° E between latitudes 23.000° and 23.010° N. Each ward is "square" *in degrees*. On the ground it is not square: at this latitude 0.010° of latitude is about **1,107 m** and 0.010° of longitude is about **1,025 m** (hand-check in 6.5.2). This fact is used repeatedly.

**Table F6-2 — Fixture E6 requests (synthetic). Defined in WGS 84 and *delivered* in WGS 84 / UTM zone 43N (EPSG:32643), metres.**

| Request | Defined position (lat ° N, lon ° E) | Delivered easting E (m) | Delivered northing N (m) | Where it lies |
| --- | --- | --- | --- | --- |
| Q1 | 23.002, 75.000 | 500000.000 | 2543741.163 | On the shared A/B boundary |
| Q2 | 23.008, 75.000 | 500000.000 | 2544405.362 | On the shared A/B boundary |
| Q3 | 23.005, 74.995 | 499487.611 | 2544073.271 | Strictly inside Ward A |
| Q4 | 23.005, 75.005 | 500512.389 | 2544073.271 | Strictly inside Ward B |
| Q5 | 23.005, 75.012 | 501229.733 | 2544073.313 | Outside both wards (east of B) |

**How the delivered values were produced, and how much to trust them.** Each request was *defined* as a latitude/longitude pair. The easting/northing values were calculated from those pairs with the Transverse Mercator formulas published in the IOGP/EPSG Guidance Note 7-2, section 3.2.3.1 ("JHS formulas"), using the UTM zone 43N parameters from the EPSG registry (central meridian 75° E, scale factor 0.9996, false easting 500,000 m, false northing 0 m) and the WGS 84 ellipsoid (semi-major axis 6,378,137 m, inverse flattening 298.257223563) [E01] [E02] [E04]. The calculation was done with standard-library arithmetic, not with GIS software, and is rounded to 0.001 m. Any correct GIS implementation should reproduce these values to within a few millimetres; the Guidance Note states that within ±4° of the central meridian the two formula families it publishes differ by at most 3 mm [E01, p. 51]. If your software differs by more than **0.01 m**, something other than rounding is wrong (see 6.8.6).

Notice two things you can check without any formula. First, Q1 and Q2 lie *exactly* on the central meridian, so their easting is *exactly* the false easting, 500,000 m — that is what "false easting" means [E01, Table 4]. Second, Q3 and Q4 are placed symmetrically 0.005° either side of the central meridian at the same latitude, so their eastings are symmetric about 500,000 (499,487.611 and 500,512.389; the difference from 500,000 is 512.389 m in both cases) and their northings are identical. A mistake in the fixture would very likely break one of these symmetries.

**Provenance notes (synthetic) supplied with the fixture:**

- `wards_e6.geojson` — "Ward boundaries digitised for training. CRS: WGS 84 (EPSG:4326). Coordinates written as GeoJSON positions, longitude first, per RFC 7946." (The file text is in 6.8.3.)
- `requests_e6.csv` — "Request locations exported from the training request tracker. CRS: WGS 84 / UTM zone 43N (EPSG:32643). Columns `E_m`, `N_m` are easting and northing in metres." (The file text is in 6.8.3.)

---

## 6.1 Explain why projection choice matters

### 6.1.1 What a projection does, and what it must break

A **map projection** is a set of mathematical rules that turns positions on a curved model of the Earth (the ellipsoid of a geographic CRS, Chapter 5) into positions on a flat plane. ArcGIS documentation puts the whole problem in one sentence: "there is no perfect way to transpose a curved surface to a flat surface without some distortion, various map projections exist to satisfy different compromises" [S14]. The QGIS introduction says the same thing from the other side: it "is usually impossible to preserve all characteristics at the same time in a map projection" [S13].

Four properties of a shape on the Earth can be spoiled by flattening:

| Property | What it means on the ground | What distortion looks like on the map |
| --- | --- | --- |
| **Area** | How much surface a ward covers (m², km²) | A ward drawn too large or too small compared with another |
| **Shape / angles** (conformality) | The angle at which two roads meet; the outline of a small object | Right angles that are no longer right angles; a round tank drawn as an ellipse |
| **Distance** | The length of a road segment | A 1 km segment drawn as if it were 1.09 km |
| **Direction** (bearing) | The compass direction from a depot to a request | A "due north" line drawn tilted |

A projection can hold *some* of these fixed, but only under particular conditions — at one point, along one line, or within one zone — and never all of them everywhere. That last clause is the whole reason this chapter exists.

### 6.1.2 "Preserves" means "under particular conditions"

The QGIS introduction names the three classical families [S13]:

- **Conformal** projections keep angles and therefore local shape. Mercator and Lambert Conformal Conic are examples. The price is area: "these distort areas significantly" over large regions [S13]. Esri's description of Mercator gives the well-known illustration: "although Greenland is only one-eighth the size of South America, Greenland appears to be larger than South America in the Mercator projection" [X05].
- **Equal-area** projections keep the ratio of areas correct everywhere. The price is shape and angle over large regions [S13].
- **Equidistant** projections keep distances correct — but only from one centre point or along particular lines, not between every pair of points [S13].

There are also **compromise** projections (Robinson, Winkel Tripel) that accept modest distortion of everything for a good-looking world map [S13].

The word to watch is **"preserves"**. A conformal projection preserves angles *at infinitesimal scale* [X05] — that is, for very small shapes — which is why a city's street grid keeps its right angles in Web Mercator while a continent's outline still looks wrong. An equidistant projection preserves distance *from its centre*, not between two arbitrary requests. Read every "preserves" claim with the question: *preserved where, and at what scale?*

**UTM as an example of "particular conditions".** UTM is Transverse Mercator applied in 6°-wide zones with a central-meridian scale factor of 0.9996 [E01, Table 4]. On the central meridian the map is 0.04 % *smaller* than the ground; moving away from the meridian the scale grows through 1.0 and past it, so the zone's scale error stays small (a fraction of a percent) *inside the zone*. The Guidance Note warns that away from the central meridian "the increasing distortions in distance, area and angle which are inherent in the Transverse Mercator projection method well away from the longitude of origin cannot be avoided", making the method "quite unsuitable in these outer areas for many purposes, for example engineering and large scale mapping" [E01, p. 51]. So UTM is excellent *inside its zone* and wrong *outside it* — a single CRS is not "good" or "bad", it is good *somewhere*.

### 6.1.3 A city measurement task and a world map need different properties

Ask the two questions the blueprint requires: *which property matters, and over what extent?*

**Task 1 — city scale.** The municipality wants the straight-line distance from each request to the nearest road, and the area of each ward, for wards that together span about 2 km east–west (Fixture E6). The properties that matter are **distance and area**, and the extent is tiny: the whole study area sits within a few kilometres of one UTM central meridian. In UTM zone 43N the scale factor at Q1–Q4 is 0.9996 to about six decimal places (6.7 shows the numbers). A 1 km distance is short by 0.4 m. For the municipality's purpose — deciding which crew goes where — 0.4 m in 1 km is irrelevant, and the answer is "a local projected CRS whose area of use covers the study area, plus planar measurement".

**Task 2 — a world map of request counts by country for a company newsletter.** Now the extent is the whole globe and the property is **area** (a reader compares country sizes visually). Web Mercator would show Canada and Russia enormously enlarged relative to India [X05]; an equal-area projection is the honest choice, and nobody will measure a distance on the newsletter map, so distance distortion is acceptable.

**The general rule.** Esri's guidance is exactly this: "The extent, location, and property (area, distance, or shape) you want to preserve must inform your choice of map projection for your projected coordinate system" [S14]. Write the three words *extent, location, property* on the decision worksheet in 6.6 — they are its first three rows.

**Worked example 6.1 — the planar grid and Fixture E6 side by side.** On the Chapters 1–5 training grid, Ward A is a 1,000 m × 1,000 m square with area exactly 1,000,000 m² (Table F1 of Chapter 1). Nothing was projected; the grid *is* a plane, so there is no distortion and nothing to choose. Fixture E6's Ward A, by contrast, was *defined in degrees*: 0.010° × 0.010°. On the ground that is about 1,025 m east–west by about 1,107 m north–south (6.5.2), so its true surface area is about 1,135,300 m² (6.7 gives 1,135,334.6 m², with the method). Drawn in Web Mercator the same ward has a planar area of about 1,346,300 m² — 18.6 % too large — while in UTM 43N it is 1,134,426.5 m², 0.08 % too small. Same ward, three areas; only one of them was measured on the Earth's surface, and the other two are wrong by amounts that depend on the projection and the latitude. This is the concrete meaning of "projection choice matters".

### 6.1.4 Diagrams (schematic — not quantitatively accurate distortion models)

**Diagram D6-1 (described; schematic).** Three panels, each showing the same set of four equal circles placed at the equator, at 23° N, at 45° N, and at 70° N. *Panel 1 — "on the ellipsoid (reference)":* four equal circles. *Panel 2 — "conformal (Mercator family)":* the circles stay circular but grow with latitude; the 70° N circle is visibly much bigger. Label: "shape kept, area not". *Panel 3 — "equal-area":* the circles keep the same area but become flattened ellipses toward the pole. Label: "area kept, shape not". A caption in a box reads: "Schematic. Circle sizes are illustrative and are not computed from any projection formula." The point is the *kind* of change, not the amount.

**Diagram D6-2 (described; schematic).** A single UTM zone drawn as a tall strip 6° wide. The central meridian is drawn as a solid line labelled "scale factor 0.9996 (map slightly smaller than ground)"; two dashed lines either side are labelled "scale factor 1.0 (true scale)"; the zone edges are labelled "scale factor slightly above 1.0 (map slightly larger than ground)". A red band outside the zone is labelled "distortion keeps growing — use the neighbouring zone". A caption: "Schematic. Line positions are not to scale; the numbers 0.9996 and 1.0 are the only quantitative facts on this figure [E01, Table 4]."

The blueprint forbids passing off a stylised animation as a distortion model, and these captions are how this chapter obeys it: any figure that is not computed from a formula says so.

**Developer analogy.** A projection is a *lossy encoder* from a curved surface to a flat one, and each projection is a different codec tuned to keep a different feature of the signal. The analogy is good for the idea that "lossless" is impossible and that you pick the codec for the use. It stops being accurate in one important way: a lossy image codec degrades everywhere roughly uniformly, whereas a projection's error is *position-dependent* — near-zero in one place and large elsewhere — and that dependence, not the average, is what decides whether a measurement is trustworthy.

**Misconception.** *"Metres in, metres out — if the CRS is in metres the distances are right."* A projected CRS in metres can still be badly distorted at your location (Web Mercator at 23° N stretches north–south lengths by about 9 %, 6.5.3). Units tell you what to *write after the number*; the projection and your position decide whether the number is *close to the ground value*.

**Comprehension check 6.1.** A colleague proposes one projected CRS "for everything the company does in South Asia and the Gulf" so that "all our distances are comparable". (a) Which of the four properties would that projection need to preserve for distance work, and can any projection do that for every pair of points across such an extent? (b) Name the two questions (from 6.1.3) that should be asked before any such choice.

---

## 6.2 Separate assignment from transformation

### 6.2.1 Two operations with confusingly similar names

Every GIS has two different operations that both mention "projection", and they do opposite things to your data.

| | **Assign a CRS** (write a label) | **Transform / project** (compute new coordinates) |
| --- | --- | --- |
| What changes | Only the *metadata* that says which CRS the stored numbers belong to | The *stored coordinate values themselves*; the output is in the destination CRS |
| The numbers in the table | Unchanged | Different |
| When correct | The dataset's CRS metadata is missing or wrong **and** you have established, from evidence, which CRS the numbers really are in | You know the current CRS and want the data in another one |
| ArcGIS Pro tool | **Define Projection** [S15] | **Project** (features) [S16]; **Project Raster** (rasters) [X03] |
| QGIS | **Assign projection** algorithm (new layer, same geometry, new CRS label); **Define Shapefile projection** (writes a `.prj` for a Shapefile); the layer CRS setting (**Layer ▸ Set CRS of Layer(s)** or Layer Properties ▸ Source), which changes only how the project interprets the layer [Q02] [Q01] | **Reproject layer** algorithm [Q02] |
| PostGIS | `ST_SetSRID` [X08] | `ST_Transform` [X09] |

The documentation for each is unambiguous. Define Projection "Overwrites the coordinate system information (map projection and datum) stored with a dataset" and "does not modify any geometry"; for actual reprojection, "use the Project tool instead" [S15]. Project "Projects spatial data from one coordinate system to another" and writes the result to a new output dataset [S16]. QGIS's *Assign projection* "Assigns a new projection to a vector layer" without reprojecting geometries, while *Reproject layer* "Reprojects a vector layer in a different CRS" [Q02]; QGIS also warns, of the layer CRS setting, that "changing the CRS in this setting does not alter the underlying data source in any way, rather it just changes how QGIS interprets the raw coordinates" [Q01]. PostGIS's `ST_SetSRID` "does not transform the geometry coordinates in any way - it simply sets the meta data defining the spatial reference system the geometry is assumed to be in", and points you to `ST_Transform` "if you want to transform the geometry into a new projection" [X08] [X09].

This is a **general GIS principle** implemented identically, under different names, in every serious platform. The names are platform-specific; the distinction is not.

**Developer analogy.** Assignment is like changing the declared *encoding* of a text file from `latin-1` to `utf-8` without touching the bytes: if the bytes really were UTF-8 all along, you have fixed a wrong label; if they were not, you have just made every non-ASCII character garbage — and the file will still open. Transformation is like actually transcoding the bytes. The analogy holds well, including the nasty property that a wrong label does not throw an error. It breaks down in one way: a wrongly labelled text file is usually *obviously* wrong on screen, whereas a wrongly labelled dataset can look plausibly right (6.3.2 shows a case that is off by about 110 m and looks fine at city scale).

### 6.2.2 When assignment is the right operation — and the evidence it needs

Define Projection exists for one situation: the dataset's CRS information is "unknown or incorrect" [S15]. The tool even runs, with a warning, if the dataset already has a known CRS [S15] — which means the software will happily let you overwrite a correct label with a wrong one. The safety is entirely in your process:

1. **Establish the CRS from evidence, not from the numbers.** Provenance (who exported it, from what system, with what settings), a `.prj`/metadata file that was separated from the data, a documented export specification, or a confirmed statement from the data owner. Chapter 5's checklist applies: numbers of the right magnitude are a *clue* that narrows the candidates; they are not proof.
2. **Test the candidate before committing.** Add the data to a map that already contains a trusted layer of the same area, assign the candidate CRS (in QGIS, the layer CRS setting is a harmless place to try this, because it changes nothing on disk [Q01]; in ArcGIS Pro, Project's **Input Coordinate System** parameter "allows you to specify the data's coordinate system without having to modify the input data" [S16]), and check alignment against known features.
3. **Only then** write the label with Define Projection / Define Shapefile projection / `ST_SetSRID`, and record in the provenance log *what evidence justified the assignment*.

**Platform note — where Define Projection is refused.** ArcGIS Pro's page lists inputs on which the tool is blocked, including feature classes in enterprise geodatabases that already contain records, feature classes inside a feature dataset, and feature datasets themselves; the page suggests Append or export/re-import workflows for enterprise data instead [S15]. Read the restrictions before promising a colleague that "we'll just define it".

### 6.2.3 A controlled diagnostic: two copies of the same dataset

This example exists so that you can *recognise* a wrongly relabelled dataset when you meet one. It is not a repair technique; the "wrong" copy is deliberately wrong.

Take request Q1 from Fixture E6. Its delivered coordinates are E = 500,000.000 m, N = 2,543,741.163 m in EPSG:32643. Make two copies:

| | **Copy T — transformed correctly** | **Copy L — relabelled wrongly** |
| --- | --- | --- |
| Operation | Project (features) from EPSG:32643 to EPSG:4326 [S16] | Define Projection: overwrite the label with EPSG:4326 [S15] |
| Stored numbers afterwards | longitude 75.000, latitude 23.002 (WGS 84 degrees) — new numbers computed from the old ones | 500000.000 and 2543741.163 — **unchanged** |
| What the label now claims | "these are degrees" — true | "these are degrees" — false |
| What happens when drawn | Q1 appears on the A/B boundary, where it belongs | A "longitude" of 500,000° and a "latitude" of 2,543,741° are outside any valid range (longitude −180…180, latitude −90…90); the point cannot be placed sensibly on the Earth. Behaviour varies by software: the feature may be dropped, drawn at a clamped position, or cause an error. |

Copy L is the *loud* failure: the numbers are impossible as degrees, so the mistake shows immediately.

Now the *quiet* failure. Take Copy T's numbers (75.000, 23.002) and wrongly relabel *them* as EPSG:32643. The software now reads "easting 75 m, northing 23.002 m". Both are valid UTM values — they are just nowhere near the study area. Easting 75 m is 499,925 m *west* of the central meridian (500,000 − 75), and northing 23 m is 23 m north of the equator. Hand-check the location: at the equator one degree of longitude is about 111 km, so 499.9 km west of 75° E is roughly 70.5° E, and the point lands near (0° N, 70.5° E) — open ocean, about 2,600 km from the study area (both figures hand-checked to the nearest 0.1° and 100 km). No error is raised. The layer "works". It is simply on the wrong continent.

And the *quietest* failure, previewed here and explained in 6.3.2: data genuinely in the older Indian datum Kalianpur 1975 relabelled as WGS 84 land **about 110 m** from their true positions — close enough to look right on a city map, far enough to put a request in the wrong ward.

**Table 6.2 — How to recognise each failure.**

| Symptom | Likely cause | First check |
| --- | --- | --- |
| Values impossible for the labelled CRS (e.g., "latitude" of 2.5 million) | Projected numbers labelled as geographic | Compare number magnitudes with the CRS's expected ranges (Chapter 5) |
| Layer draws thousands of kilometres away, often near (0, 0) of the CRS | Geographic numbers labelled as projected, or wrong zone/hemisphere | Look at the extent in Layer Properties ▸ Source [X10]; is it near the false origin? |
| Layer draws in the right city but shifted by tens to hundreds of metres | Wrong datum (geographic reference) in the label, or a missing/wrong transformation | Overlay a trusted reference layer; compare positions of the same known feature |
| Layer draws in the right city but slightly rotated or scaled relative to a reference | Wrong projection parameters (zone, scale factor, false easting) | Inspect the full CRS definition, not just its name |

**Misconception.** *"The layer lines up on the map now, so Define Projection fixed it."* It aligned because the label you chose happened to be *right*. If the label were wrong by one datum, the layer would still "line up" to within a screen pixel at city scale (6.3.2) and every measurement from it would be biased. Alignment on screen is a *necessary* check, not a *sufficient* one — 6.4 develops this.

**Comprehension check 6.2.** A CSV of asset locations arrives with columns `X`, `Y` and values such as `X = 500512.4, Y = 2544073.3`, and no CRS statement. The sender says "it came out of the same system as the requests". (a) Which of the two operations — assignment or transformation — is the *only* one that could ever be appropriate first, and why? (b) Name two pieces of evidence you would want before performing it. (c) What, exactly, would go wrong if you ran Project on it instead, and why can the software not stop you?

---

## 6.3 Explain datum transformations and evidence

### 6.3.1 Why a projection change is sometimes not enough

Recall from Chapter 5 that a projected CRS is "composed of a geographic coordinate system and a map projection together" [S14]. When you move data between two projected CRSs, there are therefore two possible kinds of change:

- **Same geographic CRS underneath, different projection** (for example, WGS 84 / UTM zone 43N → WGS 84 geographic, or → WGS 84 / UTM zone 44N). Only the projection maths changes. ArcGIS calls this a *projection* and the Project tool needs no transformation: "When no geographic or datum transformation is required, no drop-down list will appear on the parameter, and it is left blank" [S16]. The Project page gives a worked illustration — converting `GCS_North_American_1983` to `NAD_1983_UTM_Zone_12N` needs no transformation because both share the NAD 1983 datum, whereas converting to `WGS_1984_UTM_Zone_12N` does, because the datums differ [S16].
- **Different geographic CRS underneath** (a different datum: for example, Kalianpur 1975 → WGS 84). Now the two systems model the Earth with different ellipsoids anchored at different points, and the *same physical place has different latitude/longitude values in each*. Converting requires a **geographic (datum) transformation** in addition to any projection maths: "Transformations convert data between different geographic coordinate systems or between different vertical coordinate systems" [S14], and "When a transformation is required, a drop-down list will be generated based on the input and output datums, and a default transformation will be applied" [S16].

**Terminology.** In EPSG and ISO usage, a *conversion* is exact maths within one datum (a projection is a conversion), whereas a *transformation* changes datum and is *empirically determined* — it has an accuracy, an area of validity, and often several competing versions. Esri's interface uses "geographic transformation" for the second; QGIS uses "datum transformation" or "coordinate operation". This chapter uses **datum transformation** for the concept and quotes each product's label when describing its screen.

**Developer analogy.** A projection change is like converting a timestamp between time zones when both are defined relative to the same UTC — a pure function with an exact answer. A datum transformation is like converting between two *clocks that were set independently* and drift relative to each other: you need a measured offset, valid for a period, with a stated uncertainty, and there may be several published offsets of different quality. The analogy stops being accurate in that datum offsets vary *by location* (a 3-parameter shift approximates a whole region with one vector; grid-based methods vary the shift point by point), which is why every transformation record carries an area of use.

### 6.3.2 Reading a transformation record: applicability, area, resources, accuracy

Never accept a transformation because it was the default. Read its record. Here is one, retrieved from the EPSG registry for this chapter [E06]:

| Field | Value (EPSG registry, dataset v13.103) | What it tells you |
| --- | --- | --- |
| Name / code | **Kalianpur 1975 to WGS 84 (1)**, EPSG:1156 | The "(1)" means other variants may exist |
| Source → target | Kalianpur 1975 (EPSG:4146, geographic 2D, ellipsoid Everest 1830 (1975 Definition)) → WGS 84 | Direction; the method is marked reversible |
| Method | Geocentric translations (geog2D domain) | A 3-parameter shift: ΔX = 295 m, ΔY = 736 m, ΔZ = 257 m applied to Earth-centred X, Y, Z [E01, §4.2.4] |
| Area of use | "Asia - India mainland and Nepal" | Do **not** use it for Sri Lanka, Bangladesh, or offshore areas; they have their own records |
| Accuracy | **22 m** | Positions after transformation may be wrong by this order of magnitude |
| Remarks | "Derived at 7 stations. Accuracy 12m, 10m and 15m in X, Y and Z axes. Care! DMA ellipsoid is inconsistent with EPSG ellipsoid - transformation parameter values may not be appropriate. Also source CRS may not apply to Nepal." | The registry itself warns you |
| Scope | "Military survey." | The purpose it was derived for |

What happens if this datum difference is *ignored*? Take Q3's position and suppose a legacy survey file stores it as latitude 23.005° N, longitude 74.995° E **in Kalianpur 1975**, and a colleague relabels the file "WGS 84" because "it's lat/long anyway". Applying EPSG:1156 with the Guidance Note's geographic↔geocentric formulas [E01, §4.1.1, §4.2.4] to that pair (ellipsoidal height taken as 0) gives a WGS 84 position of about 23.00555° N, 74.99408° E — about **112 m** from where the relabelled file puts it (roughly 61 m north and 94 m west; computed with standard-library arithmetic, then the ground distance from the geodesic formula in 6.5). At the scale of a city map that is barely a pixel; at the scale of a ward boundary it is decisive, and the 22 m accuracy of the transformation means even the corrected position is only known to a few tens of metres. Two lessons: the datum matters even though "it's all degrees", and the transformation record's accuracy is part of the answer, not a footnote.

**Verification item (instructor).** The 112 m figure is a hand calculation from published parameters, with height 0. ArcGIS Pro and QGIS may offer a *different* transformation as their default for this pair, possibly a grid-based or later record; the resulting shift will differ. Before quoting any shift to learners, run the actual transformation in the installed software and record its name, code, and the figure obtained (Instructor Appendix I.6).

**How the products choose and what they need.** ArcGIS Pro lists candidate transformations "based on data extents and transformation accuracy. By default, the first transformation in the list is applied" [X01]; equation-based methods (Molodensky, geocentric translation, coordinate frame, position vector and others) are self-contained, whereas file-based methods (HARN, NADCON, NADCON5, GEOCON, NTv2) need grid files, some of which "are not installed with ArcGIS Pro" and must be downloaded [X01]. Transformations are bidirectional: "if you're converting data from WGS84 to NAD 1927, you can choose the NAD_1927_to_WGS_1984_3 transformation, and the tool will apply it correctly" [S16]. QGIS "will attempt to use the most accurate transformation available", warns when a more accurate one needs a missing grid, greys out transformations whose grid files are not installed, and can be set to "Ask for datum transformation if several are available" [Q01]. PostGIS's `ST_Transform` picks a conversion automatically, can fail when grid-shift files are absent, and offers `ST_TransformPipeline` for a specific method [X09].

### 6.3.3 Rules for this course, and what the instructor must verify

1. **Do not hard-code a universal transformation choice.** The right record depends on the source datum, the target datum, the *area*, and the accuracy you need. Read the record every time and log its name/code.
2. **Inspect four things before accepting one:** applicability (source/target pair and direction), geographic area of use, required resources (grid files installed?), and documented accuracy. If any of the four is missing, the choice is not yet justified.
3. **Recognise the boundary of this course.** Centimetre-level, time-dependent reference-frame work (the WGS 84 ensemble itself is a *set* of realisations, and the registry gives the ensemble an accuracy of 2 m [E05]) needs a geodesist. In Phase 1 you must be able to *read* a record and *log* a choice; you are not expected to *derive* one.

**Verification item (instructor).** Before writing exact lab steps that involve a datum change, record: the installed ArcGIS Pro version; whether the ArcGIS Coordinate Systems Data package (grid files) is installed; the transformation names the Project tool lists for the pair used; and the QGIS version, PROJ version, and whether the equivalent grids are present. The guided lab in 6.8 avoids all of this by keeping both fixtures on WGS 84.

**Misconception.** *"Latitude/longitude is latitude/longitude; the datum is a detail."* Two datums give two different coordinate pairs for one place. At the fixture's location the difference between Kalianpur 1975 and WGS 84 is on the order of 100 m; elsewhere in the world such differences range from a few metres to several hundred. A pair of degrees without a datum is as incomplete as a projected pair without a zone.

**Comprehension check 6.3.** A dataset in a national datum needs to be combined with WGS 84 request points. Two transformation records are offered: one covers "country – onshore" with accuracy 1 m and needs a grid file that is not installed; the other covers "region – all" with accuracy 20 m and needs nothing. (a) Which four things must be inspected for each? (b) What would you do before running anything? (c) Why is "the software picked the second one by default" not an adequate justification in a method log?

---

## 6.4 Distinguish display alignment from analytical preparation

### 6.4.1 On-the-fly reprojection: what it does and what it proves

Both ArcGIS Pro and QGIS will draw layers of different CRSs together on one map by reprojecting each one *for display* as it is drawn. This is **on-the-fly (display) reprojection**. The QGIS introduction: "you can define a certain projection when you start the GIS and all layers that you then load, no matter what coordinate reference system they have, will be automatically displayed in the projection you defined" [S13]. ArcGIS Pro: "ArcGIS Pro reprojects data on the fly so any data you add to a map adopts the coordinate system definition of the first layer added" [S14]. QGIS: "QGIS transparently reprojects all layers contained within your project into the project's CRS" [Q01].

Three consequences matter.

1. **The stored coordinates do not change.** Display reprojection is a rendering step. Your file in EPSG:32643 still contains eastings and northings after you add it to an EPSG:4326 map. A map showing Fixture E6's wards (EPSG:4326) and requests (EPSG:32643) neatly aligned proves that *both labels are consistent with each other*, not that either dataset has been converted.
2. **It depends on correct labels.** "As long as the first layer added has its coordinate system correctly defined, all other data with correct coordinate system information reprojects on the fly to the coordinate system of the map" [S14]. A wrong label (6.2.3) or a missing/inappropriate transformation (6.3) produces a misaligned or subtly shifted layer — and a *subtly* shifted layer looks aligned.
3. **It is for looking, not for analysis or editing.** Esri is direct: this approach "should not be used for analysis or editing, because it can lead to inaccuracies from misaligned data among layers. Data is also slower to draw when it is projected on the fly" [S14]; "Projecting data in real time can take longer to draw and is not advisable if you are editing data or performing analysis" [X02]. The recommended preparation is to "first project it into a consistent coordinate system shared by all your layers. This creates a new version of your data" [S14].

**Where to see the map's CRS and the layers' CRSs (ArcGIS Pro).** *Procedure (version-specific; ArcGIS Pro 3.7 documentation; not execution-tested).* Right-click the map in the **Contents** pane ▸ **Properties** ▸ **Coordinate Systems** tab. "Current Map Coordinate Systems" lists the horizontal and vertical CRS of the map; expanding the **Layers** folder lists each coordinate system and the layers that reference it [X02]. The map's transformation is on the **Transformation** tab of the same dialog [X01]. A layer's own CRS is under **Layer Properties ▸ Source**, which shows "the layer's extent, spatial reference, domain, resolution, and tolerance information" [X10]. In QGIS, the project CRS is shown at the lower right of the status bar and set under **Project ▸ Properties ▸ CRS**; the layer CRS is under the layer's **Properties ▸ Source** [Q01].

**Developer analogy.** Display reprojection is a *view* over heterogeneous tables — a virtual join that presents rows from different schemas in one result set. It is convenient and read-only, and computing aggregates over the view is slower and depends entirely on the mapping being right. When you need to compute seriously, you *materialise* a clean table. The analogy holds well; the difference is that a bad database view usually returns visibly wrong rows, while a bad display reprojection returns a picture that looks fine.

### 6.4.2 The worksheet: five things that are not the same thing

Every measurement or analysis task in this course must fill this worksheet *before* a tool is run. Its rows are deliberately separate because learners collapse them into "the CRS".

**Table 6.4 — CRS worksheet (blank template, followed by the Fixture E6 lab example).**

| Row | Question | Fixture E6 lab (6.8) answer |
| --- | --- | --- |
| 1. Input CRS(s) | What CRS is each input *stored* in, according to its metadata and provenance? | `wards_e6.geojson`: EPSG:4326 (WGS 84, degrees, lon/lat order in the file). `requests_e6.csv`: EPSG:32643 (WGS 84 / UTM 43N, metres). |
| 2. Display CRS | What CRS is the map drawing in? (Often the first layer's.) | Whatever the first layer set; irrelevant to the stored data; note it anyway. |
| 3. Processing / output CRS | What CRS will the tool *compute in* and write *out*? | EPSG:32643 for the analysis-ready copy (small area inside one zone; metre units; planar measurement acceptable — 6.6). |
| 4. Transformation | Does any step change datum? If so, which record (name/code), area, accuracy? | None required: both inputs are on WGS 84. Log "no datum transformation required — same geographic CRS (WGS 84)". |
| 5. Units | Units of the inputs, of the computation, and of the reported result | Inputs: degrees and metres. Computation: metres. Reported: metres and square metres (rounded as stated in the log). |

Row 2 and row 3 are the ones beginners conflate. The display CRS is what you *see*; the processing CRS is what the numbers are *computed in*. They coincide only if you make them coincide.

### 6.4.3 Inspect the actual tool's rules — do not generalise

The blueprint's warning is precise: do not claim that all tools silently use the map CRS, or that all tools need physically reprojected inputs. Each tool has its own rule, and you must read it. Three verified examples from ArcGIS Pro:

- **Geoprocessing in general.** When a tool takes several inputs with different coordinate systems, "the spatial reference of the first input dataset will be used" for processing unless the **Output Coordinate System** environment is set [X11]. When that environment is set, "the input is projected to the output coordinate system during tool operation" and "Processing (calculation of geometric relationships and modification of geometries) occurs in the same coordinate system as the output geodataset" [X12]. And: "A projection will not occur if either the input or output coordinate system is unknown" [X12] — an unknown CRS silently disables the protection. The same appendix warns that the default XY tolerance for an unknown CRS is 0.001 units, which for degrees "could be as large as 110 meters" [X11].
- **Buffer.** Its rule depends on the *input's* CRS and the *units you type*: "If the input features have a projected coordinate system, Euclidean buffers will be created"; "If the input features have a geographic coordinate system and you specify a Buffer Distance value in linear units (meters, feet, and so forth, as opposed to angular units such as degrees), geodesic buffers will be created" [S17]. You can also force the behaviour with the **Output Coordinate System** environment or by choosing **Geodesic (shape preserving)** explicitly [S17]. So Buffer does *not* simply use the map CRS, and does *not* require you to reproject first — but the result *depends on what you typed and what the input's CRS was*.
- **Calculate Geometry Attributes.** "The coordinate system of the input features is used by default" and "Length and area calculations will be in the units of the input features' coordinate system unless different units are selected" — but, critically, "Length and area calculations are not supported when the input features have a geographic coordinate system or a projected coordinate system based on Web Mercator", for which you should "Choose the geodesic length or geodesic area property for best results" [X04]. (Instructors: this tool "modifies the input data" [X04] — work on a copy.)

And one from QGIS: measurement tools and geometry expressions default to *ellipsoidal* calculation using the ellipsoid set under **Project ▸ Properties ▸ General ▸ Measurements**; setting that ellipsoid to **None / Planimetric** makes them planar in the project CRS [Q03] [Q04]. The processing algorithm *Add geometry attributes* has a **Calculate using** parameter with the options "Layer CRS", "Project CRS", and "Ellipsoidal" [Q05]. So in QGIS the *project setting*, not the layer, can decide the method — a different rule from ArcGIS Pro, and one you must look up rather than assume.

**Misconception.** *"I set the map to UTM, so everything I compute is in UTM."* In ArcGIS Pro, geoprocessing uses the first input's CRS (or the environment), not the map's, unless you choose "Current Map" in the environment [X11] [X12]. In QGIS, the ellipsoid setting can make a measurement *ellipsoidal* even though the project is in UTM [Q03]. The map is a view (6.4.1); the tool's page is the contract.

**Comprehension check 6.4.** A learner adds `wards_e6.geojson` (EPSG:4326) and `requests_e6.csv`-as-points (EPSG:32643) to an empty ArcGIS Pro map, in that order, sees them aligned, and runs a tool with both as inputs and no environment set. (a) What is the map's display CRS, and why? (b) In which CRS will the tool process, according to [X11]? (c) Which rows of Table 6.4 did the learner skip, and what should have been written in them?

---

## 6.5 Compare planar and geodesic measurement

### 6.5.1 Two ways to measure, tied to the implementation

A **planar** (Euclidean) distance is computed on a flat plane with Pythagoras: for two points with projected coordinates (x₁, y₁) and (x₂, y₂), √((x₂−x₁)² + (y₂−y₁)²). It is exactly right *on the plane*; how close it is to the ground distance depends on the projection's distortion at that place (6.1). Esri's Buffer page: planar buffers "measure distance in a two-dimensional Cartesian plane" and "are appropriate when analyzing distances around features in a projected coordinate system in a relatively small area (such as one UTM zone)" [S17].

A **geodesic** distance is the length of the shortest path between two points *on the surface of the reference ellipsoid* — the curved model of the Earth that the geographic CRS defines. PROJ's definition: "The shortest path between two points on the ellipsoid at (φ₁,λ₁) and (φ₂,λ₂) is called the geodesic" [X06]. ArcGIS Pro's Measure tool describes its Geodesic mode as "The shortest line between two points on the earth's surface on a spheroid (ellipsoid)" [X07]. Esri's Buffer page says geodesic buffers "account for the shape of the earth" and are suited to inputs that "cover multiple UTM zones, large regions, or the entire globe" or whose projection "distorts distances to preserve other properties such as area" [S17]. (That page describes the curved surface as "the geoid"; the calculation is on the ellipsoid, as the Measure page and PROJ state — an instructor note in I.7 records this wording difference.)

Keep the vocabulary attached to the implementation, because each product exposes the choice differently:

| Implementation | Where the choice is made | Verified wording |
| --- | --- | --- |
| ArcGIS Pro **Buffer** | **Method**: Planar (default) or Geodesic (shape preserving); planar input CRS ⇒ Euclidean; geographic input + linear units ⇒ geodesic [S17] | See 6.4.3 |
| ArcGIS Pro **Measure** tools | Modes Planar (default; "only available when measuring in a projected coordinate system"), Geodesic, Loxodromic, Great Elliptic [X07] | Depends on the map CRS |
| ArcGIS Pro **Calculate Geometry Attributes** | Properties "Length" / "Area" (planar) versus "Length (geodesic)" / "Area (geodesic)"; planar not supported for geographic or Web Mercator inputs [X04] | Writes into fields |
| QGIS measure tools / expressions | Ellipsoidal by default using the project ellipsoid; "None / Planimetric" gives cartesian values [Q03] [Q04]; Measure Line/Area dialogs offer Cartesian or Ellipsoidal [Q03] | Project setting |
| PostGIS | `geometry` type: "minimum 2D Cartesian (planar) distance … in projected units (spatial ref units)"; `geography` type: "minimum geodesic distance … in meters, compute on the spheroid determined by the SRID", or a faster sphere with `use_spheroid=false` [X13] | Type decides |
| ArcGIS Maps SDK for JavaScript 5.1 `geometryEngine` | `planarLength`/`planarArea` "uses projected coordinates and does not take into account the curvature of the earth"; `geodesicLength`/`geodesicArea`/`geodesicBuffer` "only works with WGS84 (wkid: 4326) and Web Mercator spatial references"; for those two it "is best practice to calculate areas using geodesicArea()" [X14] | Method name decides |

The PostGIS row shows a third option worth naming: a **spherical** calculation (a sphere instead of an ellipsoid). It is faster and simpler and differs from the ellipsoidal geodesic by up to about 0.5 % — PostGIS's own example gives 123.80 m on the spheroid versus 123.48 m on the sphere [X13]. In 6.7, Fixture E6's Q1–Q2 comes out 664.46 m geodesic versus 667.17 m on a sphere of mean radius. When a library says "geodesic" or "great-circle", check whether it means ellipsoid or sphere.

**Developer analogy.** Planar versus geodesic is like integer arithmetic on a fixed-point type versus floating point: the first is cheap and exact *within its model*, the second models the real thing more faithfully at a cost. The analogy fails on one point: with fixed-point you can compute the error bound from the type alone, whereas the planar error depends on *where on the Earth* you are.

### 6.5.2 Degrees are not metres — but geographic coordinates still measure correctly, geodesically

Take Q1 (23.002° N, 75.000° E) and Q2 (23.008° N, 75.000° E). The difference in latitude is 0.006°. That is *not* a distance; it is an angle. To make it a distance you need to know how long a degree is *here*, and that is not a constant:

- **One degree of latitude** at 23° N is about **110.7 km** along the meridian (integrating the ellipsoid's meridian radius of curvature between 22.5° and 23.5° gives 110,744 m; a hand approximation of 111 km is fine for magnitude checks). So 0.006° ≈ 664 m. (6.7: the exact geodesic value is 664.46 m.)
- **One degree of longitude** shrinks with latitude by a factor of cos(latitude): the parallels get shorter toward the poles. At 23.005° N, cos(23.005°) = 0.9205, and one degree of longitude is about 102.5 km (ellipsoid: 102,519 m). So Fixture E6's 0.010° ward width is about 1,025 m east–west while its 0.010° height is about 1,107 m north–south — the "square in degrees" is a rectangle on the ground.

Here is the trap in numbers. Someone computes a "distance" from Q3 (74.995° E) to Q4 (75.005° E) with Pythagoras on the degree values: √(0.010² + 0²) = 0.010 "units". Then, because the requirement is in metres, they multiply by 111,000 "because a degree is 111 km": 1,110 m. The true ground distance is 1,025 m; the error is 8.3 %, and it grows with latitude (the same mistake at 60° N is off by a factor of two). Worse, if they simply typed `0.010` into a tool expecting metres, they asked for a *one-centimetre* buffer. Esri's appendix on tolerances makes the same point from the other direction: 0.001 of a degree "could be as large as 110 meters" [X11].

**The correct statement** is two-sided, and the blueprint asks for both halves:

1. A raw difference in degrees is not a metre distance, and Pythagoras on degrees is wrong in a way that varies by latitude and direction.
2. **Geographic coordinates are nevertheless a perfectly good input for measurement** — provided you use a *geodesic* method, which is designed for exactly those inputs. The Measure tool's geodetic modes work "in a geographic coordinate system" [X07]; Buffer creates geodesic buffers from geographic input with linear units [S17]; PostGIS `geography` takes lon/lat and returns metres [X13]; `geometryEngine.geodesicLength` accepts WGS 84 geometries [X14]. You do *not* have to project lat/lon data to measure it correctly. You have to *choose the right method*.

### 6.5.3 Web Mercator: metres, but distorted — and not "always wrong"

EPSG:3857 (WGS 84 / Pseudo-Mercator, alias "Web Mercator") is a projected CRS whose axes are "easting, northing (X,Y) … UoM: m" [E03]. Its units really are metres. Its scope in the registry is "Web mapping and visualisation", and its remarks say it is "Not a recognised geodetic system. Uses spherical development of ellipsoidal coordinates. Relative to WGS 84 / World Mercator (CRS code 3395) gives errors of 0.7 percent in scale and differences in northing of up to 43km in the map (21km on the ground)" [E03]. Esri describes the same construction: "the geodetic coordinates defined on the WGS 84 datum are projected as if they were defined on a sphere, using a sphere-based version of the Mercator projection" [X05].

The formulas are short enough to show (Guidance Note 7-2, §3.2.1.2, method 1024; λ and φ in radians, natural logarithm) [E01]:

> E = FE + a·(λ − λ₀)      N = FN + a·ln[tan(π/4 + φ/2)]

with a = 6,378,137 m and FE = FN = λ₀ = 0. Because *a* multiplies the longitude directly, one degree of longitude is the *same* 111.32 km of easting at every latitude — which is exactly wrong on the ground, where it shrinks by cos(latitude). The Guidance Note gives the scale factor in the east–west direction as k = a/(ν cos φ) and in the north–south direction as h = a/(ρ cos φ), notes that "h and k are not equal, which demonstrates the non-conformallity of the Pseudo-Mercator method", and states plainly that "this method is not conformal: scale factor varies as a function of azimuth, which creates angular distortion" [E01, p. 44]. At the fixture's latitude (23.005° N) these come out as **k = 1.0858** east–west and **h = 1.0920** north–south (hand-checked from the formulas; the ellipsoid radii ρ and ν at this latitude are the only inputs). In plain words: a planar metre on a Web Mercator map is about 8.6 % *longer* than a ground metre going east, and about 9.2 % longer going north, at this location; areas are inflated by roughly k × h ≈ 1.186, which is why Fixture E6's Ward A measured 18.6 % too large in 6.1.3.

Now the balance the blueprint requires. **None of this makes "any operation involving EPSG:3857" wrong.** Correct uses include: drawing tiled basemaps (its purpose); locating features (a Web Mercator coordinate identifies a place exactly, because the projection is a reversible function); *geodesic* measurement on Web Mercator geometries (the SDK's `geodesicLength` explicitly supports that spatial reference [X14] — "method and location matter", as the blueprint says); and even planar measurement *when the accepted error is documented and small enough*, which is rarely true at 23° N (9 %) and never true for comparing areas across latitudes. What *is* wrong is planar measurement in Web Mercator **presented as ground distance or area** — and ArcGIS Pro's Calculate Geometry Attributes refuses to do it for that reason [X04].

**Misconception.** *"Web Mercator distances are wrong by the same factor everywhere, so I can just correct them."* The factor depends on latitude (cos φ) *and* on direction (h ≠ k) *and* on the ellipsoid radii at that latitude. A single correction factor is itself an approximation that only works over a small area — at which point a proper local projection or a geodesic method is simpler and honest.

**Comprehension check 6.5.** For each statement say *true*, *false*, or *depends on the method*, and give one sentence of reasoning: (a) "A layer stored in EPSG:4326 cannot give a correct distance in metres." (b) "A layer stored in EPSG:3857 has metre units, so a planar distance from it is a ground distance." (c) "Two points 0.010° apart in longitude are the same ground distance apart at 23° N as at 60° N." (d) "A spherical great-circle distance and an ellipsoidal geodesic distance are the same thing."

---

## 6.6 Select a method from the requirement

### 6.6.1 The decision worksheet

Fill this in before measuring anything. Each row is a question whose answer changes the method; the last column shows Fixture E6's lab answers.

**Table 6.6 — Measurement-method worksheet.**

| Row | Question | Why it matters | Fixture E6 lab (6.8) |
| --- | --- | --- | --- |
| 1. Study area | Where, and how large? Inside one projected CRS's area of use, or spanning zones/countries/the globe? | Decides whether a local projected CRS is *available* | About 2 km × 1 km around 23° N, 75° E; entirely within UTM zone 43N's area of use ("N hemisphere - 72°E to 78°E") [E02] |
| 2. Measured property | Distance, area, direction, or shape? | Different projections preserve different properties (6.1) | One distance and one area |
| 3. Required accuracy | What error is acceptable for the *decision* (Chapter 1: policy, not data)? | Decides whether 0.04 % (UTM at the meridian) or 9 % (Web Mercator here) is tolerable | "Within 1 m per km is more than enough for crew dispatch" (stated assumption) |
| 4. Data quality | How accurate are the positions themselves? | No measurement method can beat the input's positional error; a 22 m datum transformation (6.3.2) dwarfs a 0.4 m projection error | Synthetic and exact; in real work, record the source's stated accuracy |
| 5. Supported algorithm | Which methods does the chosen tool actually implement for this input CRS? (6.4.3) | Buffer/Measure/Calculate Geometry/QGIS ellipsoid settings differ | ArcGIS Pro: planar on EPSG:32643 input; or geodesic via the geodesic property. QGIS: ellipsoidal by default |
| 6. Output units | What unit must the answer be reported in, and to what rounding? | Prevents "0.010" being read as metres | Metres to 0.01 m; square metres to 1 m²; rounding stated in the log |

### 6.6.2 Local projected workflow versus wide-area geodesic workflow

**Local projected workflow.** When the study area fits inside the area of use of a suitable projected CRS — a UTM zone, or a national/state grid — project the analysis copy into it and measure planarly. Requirements: (i) the CRS's **area of use covers the whole study area** — check it in the catalogue, not from memory; (ii) the CRS's distortion at the location is within the required accuracy (row 3); (iii) the units are what you will report (row 6). Fixture E6 satisfies all three with EPSG:32643.

**Never prescribe one UTM zone for all of India.** India's mainland spans roughly 68° E to 97° E of longitude (a rounded figure for orientation, not a boundary claim). Six-degree UTM zones covering that range run from zone 42 (66°–72° E) through zone 47 (96°–102° E) — six zones, each with its own EPSG code (zone 43N is EPSG:32643 [E02]). Data from one zone forced into another zone's CRS is *not* invalid — it is merely measured in a place where Transverse Mercator's distortion has grown well past the small-error band (6.1.2). Choose the zone from the data's location; if the data straddle a zone boundary, choose a geodesic method or a national grid whose area of use spans the data, and record why.

**Wide-area workflow.** When the study area spans zones, countries, or the globe, no single conformal or equal-area projection keeps distances right everywhere. Use a **supported geodesic method** on geographic (or Web Mercator, where supported) inputs — Buffer's Geodesic option, Calculate Geometry's geodesic properties, QGIS's ellipsoidal measurement, PostGIS `geography`, or the SDK's `geodesic*` functions (6.5.1). Record which ellipsoid the method used; the products cited here use the ellipsoid of the input's geographic CRS, but PostGIS can be switched to a sphere and some libraries default to one (6.5.1) — that is why row 5 exists.

### 6.6.3 Four "distances" that answer four different questions

The blueprint asks you to keep these apart because reports routinely swap them:

| Distance | Question answered | What it needs | Fixture example |
| --- | --- | --- | --- |
| **Straight-line (planar or geodesic)** | "How far apart are these two places, as the crow flies?" | Positions and a measurement method (this chapter) | Q1–Q2: 664.46 m geodesic (6.7) |
| **Network travel distance / time** | "How far will a crew drive?" | A connected road network and a routing method — *not covered in Phase 1* (Chapter 1, 1.3.3, already made this distinction) | Not answerable from Fixture E6, which has no road network |
| **2D ground distance** | "How long is this path measured on the map surface?" | Horizontal positions only | All of this chapter's numbers are 2D |
| **3D slope distance** | "How long is the pipe/cable that must be laid along a slope?" | Heights (with their vertical reference — Chapter 5, 5.6) | A 100.00 m horizontal run with a 10.00 m rise is √(100² + 10²) = 100.50 m along the slope (hand-checked) |

A stored Z value does not make a tool compute in 3D (Chapter 5, 5.6.3): Calculate Geometry has a distinct "Length (3D)" property [X04], and the Measure tool reports 3D distance separately from 2D map distance in a scene [X07]. Verify the operation's dimensional behaviour before quoting a 3D figure.

**Developer analogy.** Table 6.6 is a *requirements review* before choosing a data type: you would not pick `float32` or `decimal(18,4)` for a currency field without knowing the range, the precision the business needs, and which database functions support the type. Rows 1–3 are the range and precision; row 5 is the function support. The analogy stops where GIS differs: a numeric type's error is the same for every value, whereas a projection's error depends on *where the value is on the Earth* — which is why row 1 (study area) comes first.

**Misconception.** *"Geodesic is always the more accurate choice, so I'll always use it."* Geodesic is more *faithful to the ellipsoid*, but it is not the only requirement: some tools do not support it for a given input (row 5), it is slower for very large datasets [S17], and in a small area a local projection is equally accurate for the decision at hand while giving planar coordinates that every downstream tool understands. The requirement decides, not a rule of thumb — in either direction.

**Comprehension check 6.6.** A logistics team wants the straight-line distance between every depot and every request across three Indian states, reported in kilometres to 0.1 km, from request data stored in EPSG:4326. Fill in the six rows of Table 6.6 for them, and say which of the four distances in 6.6.3 they are *actually* asking for if their real question is "which depot can reach the request fastest".

---

## 6.7 Plan a reproducible comparison experiment

### 6.7.1 Inputs, methods, and the answer table

**Inputs (verified, known CRS).** The Fixture E6 requests Q1–Q4 of Table F6-2, defined in WGS 84 and delivered in EPSG:32643. Two pairs are compared: Q1–Q2 (north–south along the central meridian) and Q3–Q4 (east–west, straddling the central meridian). Both pairs were chosen so that the differences between methods have *exact, explainable* causes.

**Methods compared.**

| Label | Method | Reference surface / CRS | Formula source | Units | Rounding |
| --- | --- | --- | --- | --- | --- |
| **G** — geodesic (appropriate for any extent) | Shortest path on the WGS 84 ellipsoid | WGS 84 ellipsoid: a = 6,378,137 m, 1/f = 298.257223563 [E04] | Vincenty's inverse formulas (as implemented in the author's standard-library calculation); cross-checked two ways — see below | m | 0.01 m |
| **U** — UTM 43N planar (appropriate for this small area) | Pythagoras on EPSG:32643 eastings/northings | Transverse Mercator, k₀ = 0.9996, λ₀ = 75° E [E02] | Guidance Note 7-2 §3.2.3.1 JHS formulas [E01] | m | 0.01 m |
| **W** — Web Mercator planar (deliberately unsuitable) | Pythagoras on EPSG:3857 X/Y | Sphere of radius a = 6,378,137 m [E03] | Guidance Note 7-2 §3.2.1.2 [E01] | m | 0.01 m |
| **S** — spherical great-circle (for comparison) | Haversine formula on a sphere | Sphere of mean radius 6,371,008.8 m (a common "mean Earth radius"; stated so the number can be reproduced) | Standard spherical trigonometry | m | 0.01 m |
| **D** — raw degree difference (not a distance) | Pythagoras on degrees | None | — | "degrees" | — |

**Cross-checks performed on G.** For Q1–Q2, which lie on one meridian, the geodesic is the meridian arc between the two latitudes. It was computed three independent ways: (1) Vincenty's inverse formulas; (2) the JHS meridian-arc series from Guidance Note 7-2 (the northing on the central meridian divided by k₀); (3) numerical integration of the ellipsoid's meridian radius of curvature. All three agree to **664.4645 m** to four decimal places. For Q3–Q4, which lie on one parallel, Vincenty (1,025.1876 m) agrees with the parallel-arc formula ν·cos φ·Δλ (1,025.1876 m) to four decimals; at this length the geodesic and the parallel differ by far less than a millimetre. These agreements are why the G column is trusted to the stated rounding.

**Table 6.7 — Results (standard-library arithmetic from the cited formulas; not produced by GIS software).**

| Pair | G geodesic (m) | U UTM 43N planar (m) | W Web Mercator planar (m) | S sphere (m) | D degrees |
| --- | --- | --- | --- | --- | --- |
| Q1–Q2 (N–S, on central meridian) | **664.46** | 664.20 | 725.63 | 667.17 | 0.006 |
| Q3–Q4 (E–W, across central meridian) | **1,025.19** | 1,024.78 | 1,113.19 | 1,023.52 | 0.010 |

**Areas of Ward A** (defined by the four corners of Table F6-1; the U and W polygons use the four projected corners joined by straight edges):

| Ward A area | Ellipsoidal (m²) | UTM 43N planar (m²) | Web Mercator planar (m²) | "Square degrees" |
| --- | --- | --- | --- | --- |
| | **1,135,334.6** | 1,134,426.5 | 1,346,270.8 | 0.0001 |

(The ellipsoidal area is the exact surface area of the lat/lon rectangle, from Δλ × ∫ρν cos φ dφ integrated numerically; densifying the UTM polygon's edges with 200 vertices per side changed the planar area by less than 0.01 m², so straight edges are adequate at this size — this is the "Preserve Shape" question the Project tool raises for larger features [S16].)

### 6.7.2 Explaining every difference by method and assumption

This is the part that matters. Every gap in Table 6.7 has a cause you can name.

- **U versus G: a ratio of exactly 0.9996.** 664.20 / 664.46 = 0.99960; 1,024.78 / 1,025.19 = 0.99960. That is the UTM central-meridian scale factor [E02] [E01, Table 4]. Both pairs sit on or within 0.5 km of the central meridian, where the scale factor is 0.9996 to six decimal places (the growth away from the meridian is proportional to the square of the distance from it, and 0.5 km is negligible against the Earth's radius). The area ratio is 1,134,426.5 / 1,135,334.6 = 0.99920 = 0.9996² — a scale factor applied to two dimensions. **U is not "wrong"; it is smaller than the ground by a known, tiny, documented amount.**
- **W versus G: 1.0920 north–south, 1.0858 east–west.** 725.63 / 664.46 = 1.0920 and 1,113.19 / 1,025.19 = 1.0858. These are exactly the Guidance Note's h = a/(ρ cos φ) and k = a/(ν cos φ) at 23.005° N (6.5.3) [E01, p. 44]. The two are *different*, which is the non-conformality of Pseudo-Mercator made visible: it does not even stretch a small square into a larger square, but into a slightly taller rectangle. The area ratio 1,346,270.8 / 1,135,334.6 = 1.1858 ≈ h × k. **W is wrong by about 9 % for lengths and 19 % for areas at this latitude, and the wrongness is a property of the projection at this place, not a bug.**
- **S versus G: +0.4 % north–south, −0.2 % east–west.** A sphere of mean radius is a slightly different shape from the WGS 84 ellipsoid: at 23° N the ellipsoid's meridian curvature radius is smaller than the mean radius (so the true N–S arc is shorter than the spherical one) while its east–west radius is larger. Which way the error goes depends on latitude and direction. **S is a different model, not a measurement error — and if a library reports S while calling it "geodesic", that is a documentation problem to log** (PostGIS names the choice explicitly [X13]).
- **D versus anything: not comparable.** 0.006 and 0.010 are angles. Multiplying them by "111 km" gives 666 m and 1,110 m: the first is close by luck (latitude degrees are nearly constant), the second is 8 % off (longitude degrees shrink with cos φ). **D is not a distance and must never appear in a results table with a unit.**

### 6.7.3 What the table does not establish

- **No column is "ground truth".** G is the most faithful *to the WGS 84 ellipsoid*, which is itself a model. The ellipsoid is not the ground: real terrain has slope (6.6.3) and the ellipsoid's surface is not sea level (Chapter 5, 5.6). If a colleague's tool produces 664.46 m, that agreement confirms both implement the same model; it does not confirm that a tape measure would read 664.46 m.
- **These numbers were not produced by ArcGIS Pro or QGIS.** They are the *expected* values for an instructor to reproduce. Instructor Appendix I.3 lists what to run and what tolerance to accept (0.01 m for lengths, 1 m² for areas). Until that reproduction is recorded, the lab's "expected results" are formula results, and the document says so.
- **The experiment is local.** The tidy 0.9996 and h/k explanations hold because the pairs are within 0.5 km of a central meridian at one latitude. Move the pairs to the zone edge or to 60° N and the ratios change; that is the point of 6.1.

**Misconception.** *"If two tools give different numbers, one of them has a bug."* In Table 6.7 every column differs, and none is a bug: each is the correct answer to a different question (plane of which projection; surface of which model). A discrepancy between tools is a prompt to compare *methods, reference surfaces, and units* — the log fields of 6.7.1 — before suspecting the software.

**Comprehension check 6.7.** Without recomputing anything, predict for a pair of points at 45° N on the central meridian of their UTM zone: (a) the U/G ratio; (b) whether the W/G ratio would be larger or smaller than 1.092, and why (cos 45° = 0.707); (c) whether the D column would become *more* or *less* misleading for an east–west pair.

---

## 6.8 Guided lab: combine two correctly referenced layers

### 6.8.1 Objective and prerequisites

**Objective.** Starting from two small datasets that cover the same study area in *different known CRSs*, inspect both, produce an analysis-ready copy of one of them in the other's CRS (or in a chosen analysis CRS) by *transformation* — not relabelling — then measure one distance and one area with stated units and method, log every decision, and spot-check the result independently.

**Prerequisites.** Modules 6.1–6.7; Chapter 5's CRS inspection checklist; ability to create a text file.

**Execution status.** The steps below were written from the ArcGIS Pro 3.7 and QGIS 3.40 documentation pages cited beside them and are **not execution-tested**. Expected values are formula results (6.7). Instructors: complete Instructor Appendix I.3 before issuing this lab.

### 6.8.2 Required software and access

- **Primary route:** ArcGIS Pro 3.x, any licence level. The tools used — XY Table To Point [X15], Project [S16], Define Projection [S15] (used *only* in the diagnostic step), and Calculate Geometry Attributes [X04] — are listed as available at Basic, Standard, and Advanced on their pages. No ArcGIS Online sign-in, credits, or downloads are needed.
- **Alternative route (6.8.8):** QGIS Desktop 3.40.
- A text editor.

### 6.8.3 Input data — create it by copying the text below (synthetic)

**File 1 — `wards_e6.geojson`** (WGS 84, EPSG:4326; GeoJSON positions are longitude then latitude [S18, §3.1.1]; RFC 7946 §4 specifies WGS 84 as the coordinate reference system for GeoJSON, so no CRS member is written). Rings are closed and run counter-clockwise for the exterior, as RFC 7946 §3.1.6 requires.

```json
{
  "type": "FeatureCollection",
  "features": [
    {
      "type": "Feature",
      "properties": { "ward": "A" },
      "geometry": {
        "type": "Polygon",
        "coordinates": [[
          [74.990, 23.000], [75.000, 23.000], [75.000, 23.010], [74.990, 23.010], [74.990, 23.000]
        ]]
      }
    },
    {
      "type": "Feature",
      "properties": { "ward": "B" },
      "geometry": {
        "type": "Polygon",
        "coordinates": [[
          [75.000, 23.000], [75.010, 23.000], [75.010, 23.010], [75.000, 23.010], [75.000, 23.000]
        ]]
      }
    }
  ]
}
```

**File 2 — `requests_e6.csv`** (WGS 84 / UTM zone 43N, EPSG:32643; metres).

```csv
request_id,category,E_m,N_m
Q1,Blocked drain,500000.000,2543741.163
Q2,Streetlight out,500000.000,2544405.362
Q3,Pothole,499487.611,2544073.271
Q4,Water leak,500512.389,2544073.271
Q5,Fallen tree,501229.733,2544073.313
```

**File 3 — `provenance_e6.txt`** (copy verbatim; it is part of the lab evidence).

```text
wards_e6.geojson  : Ward boundaries digitised for training. CRS WGS 84 (EPSG:4326).
                    GeoJSON positions, longitude first (RFC 7946).
requests_e6.csv   : Request locations exported from the training request tracker.
                    CRS WGS 84 / UTM zone 43N (EPSG:32643). E_m, N_m in metres.
Both files are synthetic training data and describe no real place.
```

### 6.8.4 Ordered steps (ArcGIS Pro route)

**Part A — Inspect before transforming anything (worksheet rows 1, 2, 5).**

1. Open the three files in a text editor. In `wards_e6.geojson`, confirm that each position is written `[longitude, latitude]` and that the numbers are consistent with that order (first value about 75, second about 23). In `requests_e6.csv`, confirm the magnitudes are consistent with UTM eastings near 500,000 and northings in the millions. Write your observations in the log (deliverable 1) *before* opening any GIS. This is Chapter 5's checklist in action: magnitude is a clue that agrees with the provenance, not a substitute for it.
2. *Procedure (version-specific; not execution-tested).* Start ArcGIS Pro, create a project with a new map, and add `wards_e6.geojson` (Map ▸ Add Data; GeoJSON is added as a layer — if your version requires converting it with the **JSON To Features** tool first, do so and log that step). Open **Layer Properties ▸ Source** and record the spatial reference shown [X10]. Expected: GCS WGS 1984 (or the equivalent WKID 4326 label used by your version — record the *exact* text).
3. Open **Map Properties ▸ Coordinate Systems** and record the map's current coordinate system [X02]. Expected: the map adopted the first layer's CRS [S14]. Write it in worksheet row 2.
4. Run **XY Table To Point** [X15]: Input Table = `requests_e6.csv`; X Field = `E_m`; Y Field = `N_m`; **Coordinate System = WGS 1984 UTM Zone 43N (WKID 32643)** — this parameter is essential. The tool's default when nothing is chosen is WGS 84 geographic [X15], which would be the "quiet wrong label" of 6.2.3. Output: `requests_e6_utm`. Record the tool parameters in the log.
5. Confirm on the map that the five request points fall on or around the two wards (Q1, Q2 on the shared boundary; Q3 in A; Q4 in B; Q5 east of B). They are drawn aligned because the map is reprojecting the UTM points on the fly to the map's CRS (6.4.1). Write in the log: "aligned on screen; stored coordinates unchanged; not yet analysis-ready".

**Part B — Diagnostic: the wrong operation, on a throw-away copy (6.2.3).**

6. Copy `requests_e6_utm` to `requests_e6_DIAG` (right-click ▸ Data ▸ Export Features, or the Copy Features tool). On the *copy only*, run **Define Projection** with Coordinate System = WGS 1984 (WKID 4326) [S15]. Observe: the attribute values and shape coordinates are unchanged; the layer disappears from the study area (its "degrees" are 500,000 and 2.5 million). Record what you observe, then delete `requests_e6_DIAG`. This step exists to make the failure recognisable; it is never a repair.

**Part C — Produce the analysis-ready copy (worksheet rows 3, 4).**

7. Decide the analysis CRS using Table 6.6 (the worked answers in 6.4.2 and 6.6.1 apply): **EPSG:32643**, because the study area is inside the zone, the property is distance/area, and planar measurement in this CRS has a documented 0.04 % scale effect. Write the decision and its reasons in the log.
8. Run **Project** [S16]: Input = `wards_e6` layer; Output = `wards_e6_utm`; Output Coordinate System = WGS 1984 UTM Zone 43N (WKID 32643). Observe the **Geographic Transformation** parameter: it should show *no* drop-down list, because input and output share the WGS 84 geographic CRS [S16]. Record: "no datum transformation required (same GCS)". Leave Preserve Shape unchecked and note it; for polygons this small, straight projected edges are adequate (6.7.1).
9. Open **Layer Properties ▸ Source** for `wards_e6_utm` and record its spatial reference and extent. Expected extent (from Table F6-1 corners projected with the fixture formulas): eastings from about 498,975 m to about 501,025 m; northings from about 2,543,520 m to about 2,544,627 m. Both layers are now stored in the same projected CRS.

**Part D — Measure (worksheet row 5; method from Table 6.6).**

10. Add a double field `dist_Q1Q2_m` to a *copy* of the request layer, or simply use the **Measure Distance** tool set to **Planar** mode with the map's coordinate system set to EPSG:32643 [X07], and measure Q1 → Q2. Because the map CRS is projected, Planar is available and is the intended method here. **Expected: 664.20 m** (U in Table 6.7), rounding to 0.01 m; the tool's snapping and click precision may make an interactive measurement differ at the decimetre level — for a precise figure, compute it from the coordinates in the attribute table: 2,544,405.362 − 2,543,741.163 = 664.199 m (same easting, so the distance is the northing difference).
11. On a **copy** of `wards_e6_utm` (the tool modifies its input [X04]), run **Calculate Geometry Attributes**: property **Area**, Area Unit **Square meters**; leave the Coordinate System parameter blank so the input's EPSG:32643 is used [X04]. **Expected for Ward A: 1,134,426 m²** (± 1 m²). Then run it again with property **Area (geodesic)** into a second field. **Expected: 1,135,335 m²** (± 1 m²; the tool's shape-preserving algorithm should agree with the ellipsoidal surface area of 6.7 to within rounding — instructors must confirm, I.3).
12. Record in the log: method (planar in EPSG:32643 / geodesic), units, the two area values, their ratio (expected 0.9992 = 0.9996²), and the reason the two differ (6.7.2).

**Part E — Spot-check independently.**

13. Without the GIS, hand-check one length and one area from the coordinates themselves: Q1–Q2 from the northings (step 10); Ward A's approximate ground area from 6.5.2's degree lengths (1,025 m × 1,107 m ≈ 1,134,700 m², which sits within 0.1 % of both computed areas — close enough to catch a units mistake, not close enough to distinguish planar from geodesic). Record both.
14. Ask a colleague (or the instructor) to open your output layer, read its Source tab, and confirm the CRS and extent without reference to your log. Their independent reading is deliverable 5.

### 6.8.5 Expected results and validation checks

| Check | Expected | If it fails |
| --- | --- | --- |
| Request points' Source tab after step 4 | WGS 1984 UTM Zone 43N (WKID 32643) | Coordinate System parameter was left blank (defaults to WGS 84 [X15]); re-run step 4 |
| Q1 and Q2 easting | Exactly 500,000.000 | Wrong zone or wrong false easting in the chosen CRS; inspect the full CRS definition |
| Project's Geographic Transformation parameter (step 8) | Empty; no list | If a list appears, the input or output GCS is not WGS 84; re-check the layers' Source tabs |
| `wards_e6_utm` extent | E ≈ 498,975–501,025; N ≈ 2,543,520–2,544,627 | Off by thousands of km → wrong operation (assignment instead of Project) or wrong zone |
| Q1–Q2 planar distance | 664.20 m ± 0.01 m (from coordinates) | 0.006 → measured in degrees; 725 m → Web Mercator map/frame; 664.46 → geodesic mode (fine, but log it as such) |
| Ward A planar area | 1,134,426 m² ± 1 m² | ≈ 1,346,000 → Web Mercator; 0.0001 → degrees; ≈ 1,000,000 → you used the Chapter 1 grid ward by mistake |
| Ward A geodesic area | 1,135,335 m² ± 1 m² (instructor to confirm, I.3) | Large deviation → tool used a sphere or a different ellipsoid; record the tool's stated method |
| Planar ÷ geodesic area | 0.9992 | Anything else → one of the two values is not what the log claims |
| Original files | Unchanged (byte-identical) | Tools were run on originals; restore from the text in 6.8.3 |

### 6.8.6 Troubleshooting

- **GeoJSON will not add directly.** Some ArcGIS Pro versions add `.geojson` as a layer; others need **JSON To Features**. Either way, confirm the resulting layer's Source tab shows WGS 84; log the route taken. *Verification item.*
- **Points appear near the equator far to the west.** The CSV was read as degrees (Coordinate System left blank). Delete the output and re-run step 4 with WKID 32643.
- **Points appear but the wards are shifted by tens of metres.** Check the map's **Transformation** tab [X01]: a transformation was applied where none should be, or a layer's label is not WGS 84. In this fixture there must be *no* transformation.
- **Values differ from the expected ones by a few millimetres.** Different Transverse Mercator implementations differ at that level [E01, p. 51]; accept up to 0.01 m. Larger differences mean a different CRS definition, zone, or method — inspect before accepting.
- **Calculate Geometry refuses planar area.** The input layer is geographic or Web Mercator [X04]; you ran it on the wrong copy. Use `wards_e6_utm`, or choose the geodesic property.
- **Measure tool shows only Geodesic.** The map CRS is geographic; Planar is "only available when measuring in a projected coordinate system" [X07]. Either change the map CRS to EPSG:32643 for this measurement or compute from the coordinates (step 10).

### 6.8.7 Required learner deliverables

1. **Transformation/measurement log** (text): worksheet Table 6.4 filled in; worksheet Table 6.6 filled in; for each tool run — tool name, version of ArcGIS Pro/QGIS, parameters, input and output CRS (name and code), transformation used ("none required — same GCS"), units, rounding; the diagnostic observation from Part B; the reasons for choosing EPSG:32643.
2. **Output datasets:** `requests_e6_utm` and `wards_e6_utm` (or the QGIS equivalents) *plus the untouched originals*.
3. **Results:** Q1–Q2 distance with method and units; Ward A planar and geodesic areas with method and units; their ratio and its explanation.
4. **Evidence of plausibility:** the hand-checks from step 13.
5. **Independent spot-check:** a colleague's or instructor's one-paragraph confirmation of the output layer's CRS and extent, read from the Source tab, without seeing your log first.

### 6.8.8 QGIS alternative (equivalent foundational exercise; not execution-tested)

QGIS covers the same concepts with different tools and one important difference in *how measurement is chosen*. Steps written from the QGIS 3.40 documentation cited; the same expected values apply.

1. **Inspect.** Open the files in a text editor as in step 1. In QGIS, add `wards_e6.geojson` via **Layer ▸ Data Source Manager ▸ Vector** [Q06]; check its CRS under **Layer Properties ▸ Source** [Q01]. Note the project CRS in the status bar; by default a new QGIS project uses EPSG:4326 unless configured to take the first layer's CRS [Q01] — record which applies on your installation. *Verification item.*
2. **Load the CSV as points** with **Data Source Manager ▸ Delimited Text** [Q06]: Geometry Definition = Point coordinates; X field = `E_m`; Y field = `N_m`; **Geometry CRS = EPSG:32643**. Confirm alignment on screen and write the same "aligned, not analysis-ready" note.
3. **Diagnostic.** On a duplicate of the points layer only, run the processing algorithm **Assign projection** with Assigned CRS = EPSG:4326 [Q02], observe the layer leave the study area, and delete it. (Do not use *Define Shapefile projection*, which writes to disk [Q02].)
4. **Transform.** Run **Reproject layer** on the wards with Target CRS = EPSG:32643 [Q02]; check the **Coordinate Operation** (advanced) field shows no datum operation, or that the operation listed is a pure projection, and log it. Save as `wards_e6_utm`.
5. **Measure — read the Info panel.** Open **Project ▸ Properties ▸ General ▸ Measurements** and record the **Ellipsoid** setting [Q04]. With the default (an ellipsoid, e.g. WGS 84), the Measure Line tool gives an **ellipsoidal** distance even on a UTM layer: expected Q1–Q2 = **664.46 m** (G). Switch the dialog to **Cartesian**, or set the ellipsoid to **None / Planimetric** with the project CRS at EPSG:32643, to obtain the planar value **664.20 m** [Q03] [Q04]. The measure dialog's Info section "explains how calculations are made according to the CRS settings available" [Q03] — copy its text into the log for each measurement. This is the QGIS-specific lesson: *the project's ellipsoid setting, not the layer, selects the method*.
6. **Area.** Run **Add geometry attributes** on `wards_e6_utm` twice: **Calculate using = Layer CRS** (expected Ward A ≈ 1,134,426 m²) and **Calculate using = Ellipsoidal** (expected ≈ 1,135,335 m², using the project ellipsoid) [Q05]. Log both, their ratio, and the explanation.
7. Deliverables as in 6.8.7, with QGIS and PROJ versions recorded.

**Not claimed:** that QGIS and ArcGIS Pro produce bit-identical numbers; that the QGIS ellipsoidal area algorithm matches Esri's "shape-preserving" one beyond the 1 m² tolerance. Instructor Appendix I.3 records what was actually observed once the instructor runs both.

---

## 6.9 Independent check and progression gate

Complete this without the Instructor Appendix. Where a question states assumptions, answer under those assumptions only.

### 6.9.1 Concept questions (six)

**Q1 (6.1; LO1).** Select the single best answer. A projected CRS is described as "conformal". Which statement is *necessarily* true?
(a) Areas measured in it are correct everywhere.
(b) Angles are preserved at infinitesimal scale, while areas are generally distorted.
(c) Distances between any two points are correct.
(d) It is suitable for a world map comparing country sizes.

**Q2 (6.2; LO2).** A Shapefile arrives without its `.prj` file. Provenance says it was exported "from the district survey office in their standard grid", which you have confirmed from the office's documentation is EPSG:32643. You need it in EPSG:4326. List, in order, the operations you would perform (name each generically *and* by its ArcGIS Pro tool), and state for each whether the stored coordinate values change.

**Q3 (6.3; LO3).** Give the four things that must be inspected in a datum-transformation record before it is accepted, and explain in one sentence each why "the software's default" and "a reversible method" are not among them.

**Q4 (6.4; LO4).** Two layers, one in EPSG:4326 and one in EPSG:32643, draw exactly on top of each other in a map. Which of the following does that alignment *prove*? Select all that apply and justify each rejection in one line.
(a) Both layers have been converted to the same CRS.
(b) Both layers' CRS labels are consistent with each other at screen resolution.
(c) The layers are ready for a 500-metre proximity analysis.
(d) Neither layer's coordinate values were changed by adding it to the map.

**Q5 (6.5; LO5).** A request layer is stored in EPSG:4326. A developer computes, for two requests, √((Δlon)² + (Δlat)²) = 0.010 and reports "the requests are 0.010 units apart, i.e., about 1.1 km". State (a) what is wrong, (b) why the error would be larger at 50° N than at 23° N for an east–west pair, and (c) two correct ways to obtain a metre distance *without* changing the stored data.

**Q6 (6.6; LO6).** A team must report, to the nearest metre, the length of a proposed drainage line 1.2 km long inside one ward, in EPSG:32643. A second team must report the straight-line distance from a national control centre to each of 3,000 depots across the whole of India, to the nearest kilometre. For each team, choose *planar in a local projected CRS* or *geodesic*, and give the two rows of Table 6.6 that most drive your choice.

### 6.9.2 Scenario questions (two)

**Scenario 1 (6.2–6.3; LO2, LO3).** Three files arrive for the same city.
- *File K:* lat/lon pairs, header "coordinates: WGS84", provenance "from the 2004 survey adjustment (Kalianpur 1975)".
- *File L:* E/N pairs near (500,000, 2,544,000), no CRS statement, provenance "exported from the request tracker; the tracker stores UTM 43N".
- *File M:* lat/lon pairs, header "EPSG:4326", provenance "captured last month by a phone app in WGS 84".

For each file say which of these three situations it is — **unknown CRS**, **wrongly assigned CRS**, or **known CRS needing conversion** — and give the *different* correct response to each (what you do, what you refuse to do, what you write in the log). Two files may fall in the same category only if you can defend it.

**Scenario 2 (6.4–6.6; LO4, LO5).** A manager sees the wards and requests aligned in a web map (Web Mercator basemap) and asks the developer to "just buffer the road by 500 m in the web map and count requests inside — it's all lined up". Write the developer's reply in no more than eight sentences: what the alignment does and does not establish, what the 500 m would mean if computed planarly in Web Mercator at 23° N (give the approximate size of the error from 6.5.3), and two acceptable ways to get a correct 500 m buffer. Then explain, separately, why latitude/longitude request data would *still* support a correct geodesic answer.

### 6.9.3 Independent practical task (unfamiliar inputs)

This task uses a **new synthetic fixture** in a different UTM zone. Do not reuse Fixture E6 numbers.

**Inputs (create by copying).**

`requests_z.geojson` — WGS 84 (EPSG:4326), GeoJSON positions (longitude first):

```json
{ "type": "FeatureCollection", "features": [
 { "type": "Feature", "properties": { "id": "R1" }, "geometry": { "type": "Point", "coordinates": [81.000, 26.500] } },
 { "type": "Feature", "properties": { "id": "R2" }, "geometry": { "type": "Point", "coordinates": [81.000, 26.505] } },
 { "type": "Feature", "properties": { "id": "R3" }, "geometry": { "type": "Point", "coordinates": [80.996, 26.5025] } },
 { "type": "Feature", "properties": { "id": "R4" }, "geometry": { "type": "Point", "coordinates": [81.004, 26.5025] } }
] }
```

`zone_z.csv` — the corners of one service zone polygon, in **WGS 84 / UTM zone 44N (EPSG:32644)**, metres, listed in ring order:

```csv
corner,E_m,N_m
c1,499501.749,2931057.610
c2,500498.251,2931057.610
c3,500498.230,2931611.367
c4,499501.770,2931611.367
```

`legacy_z.csv` — header reads "CRS: EPSG:4326"; provenance note reads "old export, format unknown":

```csv
id,x,y
L1,500000.000,2931057.600
L2,499601.408,2931334.485
```

**Required work.**

1. Inspect all three files and classify each as *known CRS*, *unknown CRS*, or *wrongly labelled*, with evidence. State what you will and will not do with `legacy_z.csv`.
2. Choose an analysis CRS with Table 6.6 (justify the zone).
3. Produce an analysis-ready copy of `requests_z` in that CRS by transformation, and build the zone polygon from `zone_z.csv`. Report the projected coordinates of R1–R4 to 0.001 m.
4. Measure: the R1–R2 distance and the R3–R4 distance (planar in the analysis CRS, *and* geodesic), and the zone polygon's area (planar and geodesic). Report units, method, and rounding.
5. Explain each planar/geodesic ratio you observe by method (6.7.2).
6. Hand-check one distance from the coordinates alone.
7. Submit the log, outputs, originals, and the hand-check.

### 6.9.4 Oral explanation (one)

In no more than three minutes, using Fixture E6's request Q1 as the example, explain to a non-GIS colleague the difference between *assigning* a CRS and *transforming* data, what happens to the stored numbers in each case, and why a map that "looks right" is not proof that the right one was done.

### 6.9.5 Submission requirements and scoring

**Submit:** written answers to 6.9.1–6.9.2; the practical's log, outputs, originals, and hand-check; the oral explanation delivered live or recorded.

**Scoring (100 points; suggested pass 80 with no critical misconception, per blueprint Appendix B.3).**

| Component | Points | What earns them |
| --- | --- | --- |
| Concept questions | 30 (5 each) | Correct, with reasoning tied to the stated assumptions |
| Scenario questions | 20 (10 each) | Three *different* responses in Scenario 1; error magnitude and two correct routes in Scenario 2 |
| Practical | 35 | Classification with evidence (7); justified CRS (5); correct transformation and coordinates within 0.01 m (8); measurements within tolerance with units and method (8); ratio explanations (4); hand-check (3) |
| Oral | 10 | Assignment/transformation distinguished correctly; numbers behave as described; "looks right ≠ is right" stated |
| Documentation | 5 | Log complete enough for another person to reproduce |

**Critical misconceptions — automatic remediation regardless of total** (blueprint B.3): treating CRS assignment as reprojection (or running Define Projection to "fix" a misplaced layer); treating degrees as metres; guessing a CRS for `legacy_z.csv` and proceeding; claiming that on-screen alignment justifies analysis; reporting a Web Mercator planar figure as ground distance/area without stating the error.

**Progression rule (6.9.3 of the blueprint).** Do not progress a learner who confuses assignment with transformation or degrees with metres, *even if the final map is aligned*.

---

## 6.10 Media and source brief

The full media specifications are in the Media Appendix. In summary:

1. **Side-by-side animation, "metadata change versus coordinate change."** Two panels show request Q1's record card (CRS label + stored E/N values) before and after each operation. Left panel — Define Projection: the label changes from EPSG:32643 to EPSG:4326; the numbers 500000.000 / 2543741.163 stay; the point on the mini-map leaves the study area. Right panel — Project: the label changes *and* the numbers become 75.000 / 23.002; the point stays on the ward boundary. Both panels label the operation being performed, the before/after values, and whether the file on disk changed. (M.3, Diagram D6-3.)
2. **Optional interactive distortion comparison.** A map/globe pair where the learner drags a 1 km × 1 km ground square across latitudes and sees its Web Mercator planar size and its UTM size with the h, k, and 0.9996 factors displayed numerically. The specification (M.5) names the formulas (Guidance Note 7-2 §3.2.1.2 and §3.2.3.1 [E01]), the ellipsoid [E04], and the validation fixtures (Table 6.7's rows, which the interactive must reproduce to 0.01 m before it is used in teaching).
3. **Primary readings:** [S13], [S15], [S16], [S17], with [S14] for vocabulary and [E01]–[E06] for the registry facts and formulas.

**Transition to Chapter 7.** Every decision this chapter asked you to log — input CRS, transformation record and its accuracy, analysis CRS and why, measurement method, units, rounding, software version — is *provenance*. Chapter 7 (formats, sources, and metadata) shows where that provenance lives in a dataset's metadata and exchange format, why a Shapefile carries its CRS in a separate `.prj` file that can go missing (as in Q2 of 6.9.1), and what a GeoPackage or geodatabase records instead. Keep your 6.8 log; it becomes a Chapter 7 metadata example.

---

## Glossary

| Term | Meaning in this course |
| --- | --- |
| **Area of use** | The geographic region for which a CRS or transformation is defined and considered valid, as recorded in the CRS registry (e.g., "N hemisphere - 72°E to 78°E" for UTM 43N [E02]). |
| **Assign a CRS (define projection)** | Write or overwrite the metadata stating which CRS a dataset's stored coordinates belong to, without changing the coordinates [S15] [Q02] [X08]. |
| **Central meridian** | The longitude of natural origin of a Transverse Mercator zone; 75° E for UTM zone 43N [E02]. Scale factor 0.9996 along it. |
| **Conformal** | A projection property: angles (and hence local shape) are preserved at infinitesimal scale [X05] [S13]. |
| **Datum transformation (geographic transformation)** | A change of geographic reference (datum) with an area of use and stated accuracy, needed when source and target geographic CRSs differ [S14] [S16]. |
| **Distortion** | The unavoidable change of area, shape/angle, distance, or direction introduced by flattening the Earth [S13] [S14]. |
| **Ellipsoidal (measurement)** | QGIS's term for measurement on the project's ellipsoid, as opposed to cartesian/planimetric [Q03] [Q04]. |
| **Equal-area** | A projection property: areas are preserved in proportion [S13]. |
| **Equidistant** | A projection property: distances are preserved from a centre or along particular lines only [S13]. |
| **False easting / false northing** | Constants added to projected coordinates so they stay positive; 500,000 m and 0 m for northern UTM zones [E01, Table 4] [E02]. |
| **Geodesic** | The shortest path between two points on the surface of the reference ellipsoid; also the measurement method that computes its length [X06] [X07]. |
| **On-the-fly (display) reprojection** | Reprojecting layers for drawing only, so that layers in different CRSs align on screen; stored coordinates are unchanged [S13] [S14] [Q01]. |
| **Planar (Euclidean) measurement** | Distance or area computed on the flat plane of a projected CRS with 2D Cartesian mathematics [S17] [X04]. |
| **Project / reproject / transform (data)** | Compute new coordinate values in a destination CRS and write them to an output dataset [S16] [Q02] [X09]. |
| **Projection (map projection)** | The mathematics that converts angular geodetic coordinates to planar coordinates [S14]. |
| **Scale factor** | The ratio of a length on the projected plane to the corresponding length on the ellipsoid, at a point and in a direction; 0.9996 on the UTM central meridian; h and k for Pseudo-Mercator [E01]. |
| **Spherical (great-circle) distance** | Distance computed on a sphere rather than an ellipsoid; differs from the geodesic by up to about 0.5 % [X13]. |
| **Web Mercator (Pseudo-Mercator, EPSG:3857)** | A projected CRS in metres that applies spherical Mercator formulas to WGS 84 coordinates; scoped to "Web mapping and visualisation"; not conformal; heavily distorted for planar measurement away from the equator [E03] [E01] [X05]. |

## Recap

- Every projection distorts something; "preserves" always means *some property, somewhere, under conditions*. Choose from **extent, location, property**.
- **Assigning** a CRS changes a label; **transforming** changes the numbers. Define Projection / Assign projection / `ST_SetSRID` versus Project / Reproject layer / `ST_Transform`. The wrong choice does not raise an error; it produces a layer that is somewhere else — loudly, quietly, or very quietly.
- A change of datum needs a **transformation record**, read for applicability, area, resources, and accuracy. Never hard-code one.
- Layers aligned on screen are **displayed** together, not **prepared** together. Fill the five-row worksheet, then read the actual tool's rule.
- **Planar** is exact on the plane and right on the ground only where the projection is; **geodesic** is right on the ellipsoid anywhere. Degrees are not metres; geographic coordinates still measure correctly with a geodesic method; Web Mercator's metres are real units but distorted by h and k.
- Fixture E6 at 23° N on the UTM 43N central meridian: geodesic 664.46 m; UTM planar 0.9996 of that; Web Mercator planar 1.092 of that; sphere +0.4 %; degrees not comparable.

## Cross-references to later chapters

- **Chapter 7** records the CRS, transformation, and method decisions as metadata and explains the `.prj` companion file and format-specific CRS handling.
- **Chapter 8** asks you to *define* the geometry and spatial reference of a new schema deliberately, using Table 6.6's reasoning at design time.
- **Chapter 9** (georeferencing, editing, quality) relies on the assignment/transformation distinction when diagnosing misplaced data, and on the tolerance warning of [X11].
- **Chapter 10** (spatial relationships) and **Chapter 11** (buffers, clips, joins) assume analysis-ready, consistently referenced inputs and a documented distance method; Buffer's planar/geodesic rule [S17] returns there in full.
- **Chapter 12** (cartography) revisits projection choice for *communication* rather than measurement.

## References

All pages were opened and read by the author on **19 September 2026**. [S13]–[S18] are blueprint register entries; [E01]–[E06] are registry and formula sources; [X01]–[X15] are additional official product pages; [Q01]–[Q06] are QGIS 3.40 user-guide pages. Esri "latest" pages change without notice; re-check before reuse. The EPSG registry pages report "EPSG Dataset v13.103" on the access date.

| ID | Publisher | Page title | URL | Accessed |
| --- | --- | --- | --- | --- |
| S13 | QGIS Project | A Gentle Introduction to GIS — Coordinate Reference Systems (3.44) | https://docs.qgis.org/3.44/en/docs/gentle_gis_introduction/coordinate_reference_systems.html | 2026-09-19 |
| S14 | Esri | ArcGIS Pro — Coordinate systems, map projections, and transformations | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/properties/coordinate-systems-and-projections.html | 2026-09-19 |
| S15 | Esri | ArcGIS Pro — Define Projection (Data Management) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/define-projection.html | 2026-09-19 |
| S16 | Esri | ArcGIS Pro — Project (Data Management) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/project.html | 2026-09-19 |
| S17 | Esri | ArcGIS Pro — Buffer (Analysis) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/analysis/buffer.html | 2026-09-19 |
| S18 | RFC Editor | RFC 7946: The GeoJSON Format (§3.1.1 positions, §3.1.6 polygon, §4 CRS) — read for Chapter 5; cited here for file conventions | https://www.rfc-editor.org/rfc/rfc7946 | 2026-09-19 |
| E01 | IOGP (EPSG) | Geomatics Guidance Note 7, part 2 — Coordinate Conversions and Transformations including Formulas, revised September 2019 (IOGP Publication 373-7-2); sections read: 3.2.1.2 Popular Visualisation Pseudo-Mercator (pp. 44–45), 3.2.3.1 Transverse Mercator incl. Table 4 and JHS formulas (pp. 49–54), 4.1.1 Geographic/geocentric conversions (pp. 95–96), 4.2.4 geocentric translations (p. 109) | https://www.iogp.org/wp-content/uploads/2019/09/373-07-02.pdf | 2026-09-19 |
| E02 | IOGP EPSG Geodetic Parameter Registry | WGS 84 / UTM zone 43N (EPSG:32643) and conversion UTM zone 43N (EPSG:16043) | https://epsg.org/crs_32643/WGS-84-UTM-zone-43N.html and https://epsg.org/conversion_16043/UTM-zone-43N.html | 2026-09-19 |
| E03 | IOGP EPSG Geodetic Parameter Registry | WGS 84 / Pseudo-Mercator (EPSG:3857) | https://epsg.org/crs_3857/WGS-84-Pseudo-Mercator.html | 2026-09-19 |
| E04 | IOGP EPSG Geodetic Parameter Registry | Ellipsoid WGS 84 (EPSG:7030); Geographic 2D CRS WGS 84 (EPSG:4326) | https://epsg.org/ellipsoid_7030/WGS-84.html and https://epsg.org/crs_4326/WGS-84.html | 2026-09-19 |
| E05 | IOGP EPSG Geodetic Parameter Registry | Datum ensemble World Geodetic System 1984 ensemble (EPSG:6326), accuracy 2 m | https://epsg.org/datum_6326/World-Geodetic-System-1984-ensemble.html | 2026-09-19 |
| E06 | IOGP EPSG Geodetic Parameter Registry | Transformation Kalianpur 1975 to WGS 84 (1) (EPSG:1156); CRS Kalianpur 1975 (EPSG:4146); datum Kalianpur 1975 (EPSG:6146); ellipsoid Everest 1830 (1975 Definition) (EPSG:7045) | https://epsg.org/transformation_1156/Kalianpur-1975-to-WGS-84-1.html ; https://epsg.org/crs_4146/Kalianpur-1975.html ; https://epsg.org/datum_6146/Kalianpur-1975.html ; https://epsg.org/ellipsoid_7045/Everest-1830-1975-Definition.html | 2026-09-19 |
| X01 | Esri | ArcGIS Pro — Geographic datum transformations | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/properties/geographic-coordinate-system-transformation.html | 2026-09-19 |
| X02 | Esri | ArcGIS Pro — Work with coordinate systems (Map Properties ▸ Coordinate Systems) | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/properties/specify-a-coordinate-system.html | 2026-09-19 |
| X03 | Esri | ArcGIS Pro — Project Raster (Data Management) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/project-raster.html | 2026-09-19 |
| X04 | Esri | ArcGIS Pro — Calculate Geometry Attributes (Data Management) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/calculate-geometry-attributes.html | 2026-09-19 |
| X05 | Esri | ArcGIS Pro — Mercator (projection description, incl. Mercator auxiliary sphere / Web Mercator) | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/properties/mercator.html | 2026-09-19 |
| X06 | PROJ (OSGeo) | PROJ documentation — Geodesic calculations (cites Karney, C. F. F., "Algorithms for geodesics", Journal of Geodesy 87(1):43–55, 2013, doi:10.1007/s00190-012-0578-z; the paper itself was not read for this chapter) | https://proj.org/en/stable/geodesic.html | 2026-09-19 |
| X07 | Esri | ArcGIS Pro — Measure (Measure Distance / Area / Features; Planar, Geodesic, Loxodromic, Great Elliptic modes) | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/navigation/measure.html | 2026-09-19 |
| X08 | PostGIS Project | PostGIS reference — ST_SetSRID | https://postgis.net/docs/ST_SetSRID.html | 2026-09-19 |
| X09 | PostGIS Project | PostGIS reference — ST_Transform | https://postgis.net/docs/ST_Transform.html | 2026-09-19 |
| X10 | Esri | ArcGIS Pro — Set layer properties (Source tab: extent, spatial reference) | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/layer-properties/set-layer-properties.html | 2026-09-19 |
| X11 | Esri | ArcGIS Pro — Spatial reference and geoprocessing (first-input rule; unknown-CRS tolerance warning) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/appendices/spatial-reference-and-geoprocessing.html | 2026-09-19 |
| X12 | Esri | ArcGIS Pro — Output Coordinate System (environment setting) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/environment-settings/output-coordinate-system.html | 2026-09-19 |
| X13 | PostGIS Project | PostGIS reference — ST_Distance (geometry planar vs geography spheroid/sphere) | https://postgis.net/docs/ST_Distance.html | 2026-09-19 |
| X14 | Esri | ArcGIS Maps SDK for JavaScript 5.1 — geometryEngine (planarLength/planarArea, geodesicLength/geodesicArea/geodesicBuffer) | https://developers.arcgis.com/javascript/latest/references/core/geometry/geometryEngine/ | 2026-09-19 |
| X15 | Esri | ArcGIS Pro — XY Table To Point (Data Management) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/xy-table-to-point.html | 2026-09-19 |
| X16 | Esri | ArcGIS Pro — Get started (page labelled "Released version: ArcGIS Pro 3.7" on the access date) | https://doc.esri.com/en/arcgis-pro/latest/get-started/get-started.html | 2026-09-19 |
| X17 | PROJ (OSGeo) | PROJ documentation — Web Mercator / Pseudo Mercator (formulas; "non conformant if used with ellipsoid") and Transverse Mercator (algorithm accuracy) | https://proj.org/en/stable/operations/projections/webmerc.html and https://proj.org/en/stable/operations/projections/tmerc.html | 2026-09-19 |
| Q01 | QGIS Project | QGIS Desktop 3.40 User Guide — Working with Projections (layer/project CRS, on-the-fly, datum transformations, unknown CRS) | https://docs.qgis.org/3.40/en/docs/user_manual/working_with_projections/working_with_projections.html | 2026-09-19 |
| Q02 | QGIS Project | QGIS Desktop 3.40 User Guide — Processing: Vector general (Assign projection, Define Shapefile projection, Reproject layer) | https://docs.qgis.org/3.40/en/docs/user_manual/processing_algs/qgis/vectorgeneral.html | 2026-09-19 |
| Q03 | QGIS Project | QGIS Desktop 3.40 User Guide — Map View: Measuring (ellipsoidal default, Cartesian/Ellipsoidal, Info section) | https://docs.qgis.org/3.40/en/docs/user_manual/map_views/map_view.html | 2026-09-19 |
| Q04 | QGIS Project | QGIS Desktop 3.40 User Guide — QGIS configuration: Project Properties ▸ General ▸ Measurements (Ellipsoid, None/Planimetric, units) and CRS tab | https://docs.qgis.org/3.40/en/docs/user_manual/introduction/qgis_configuration.html | 2026-09-19 |
| Q05 | QGIS Project | QGIS Desktop 3.40 User Guide — Processing: Vector geometry (Add geometry attributes: "Calculate using" Layer CRS / Project CRS / Ellipsoidal; Buffer) | https://docs.qgis.org/3.40/en/docs/user_manual/processing_algs/qgis/vectorgeometry.html | 2026-09-19 |
| Q06 | QGIS Project | QGIS Desktop 3.40 User Guide — Opening Data (Data Source Manager; Delimited Text: X/Y field, Geometry CRS) | https://docs.qgis.org/3.40/en/docs/user_manual/managing_data_source/opening_data.html | 2026-09-19 |

---

# Instructor Appendix (separate from learner material)

Learners should not read this appendix before completing the 6.9 gate.

## I.1 Answers and reasoning — comprehension checks

**6.1.** (a) It would need to preserve *distance between arbitrary pairs of points*; no projection does this over such an extent — equidistant projections preserve distance only from a centre or along particular lines [S13]. (b) "Which property matters?" and "over what extent/location?" — Esri's "extent, location, and property" [S14]. A good answer adds that a *geodesic* method removes the need for one shared projection.

**6.2.** (a) Assignment — the file has *no* CRS; transformation requires a known input CRS (Project's Input Coordinate System parameter exists precisely because the input may be unknown [S16]). (b) Any two of: the exporting system's documented CRS; a separated `.prj`/metadata file; alignment of the candidate against a trusted layer of the same area; confirmation from the data owner. (c) Project with an unknown input CRS cannot compute anything correct — "A projection will not occur if either the input or output coordinate system is unknown" [X12] — and if the learner *guessed* an input CRS in the parameter, the software would compute a perfectly valid transformation of the wrong assumption; it cannot know the assumption is wrong.

**6.3.** (a) Applicability (source/target pair and direction), area of use, required resources (grid files), documented accuracy (6.3.2). (b) Install the grid for the 1 m record if the accuracy requirement needs it; otherwise choose the 20 m record *and record that 20 m is the accepted uncertainty*; verify the data lie inside each record's area of use. (c) Defaults are chosen by extent and list order [X01], not by the learner's accuracy requirement; the log must state the reason, not the mechanism.

**6.4.** (a) EPSG:4326 (GCS WGS 1984) — the map adopted the first layer's CRS [S14] [X02]. (b) In the first input's CRS [X11] — which one that is depends on the order given to the tool, so the learner cannot say without checking; the correct answer notes this uncertainty. (c) Rows 3 (processing CRS: should be EPSG:32643, chosen), 4 (transformation: none required, same GCS), and 5 (units: metres, rounding).

**6.5.** (a) False — geodesic methods accept geographic input and return metres [X07] [S17] [X13] [X14]. (b) False — units are metres but planar lengths are scaled by h/k (≈ 9 % at 23° N) [E01] [E03]. (c) False — one degree of longitude shrinks by cos φ; at 60° N it is about half the 23° N value. (d) False — different reference surfaces; differences up to about 0.5 % [X13].

**6.6.** Rows: study area = three states (multiple UTM zones, so no single local CRS is safe); property = distance; accuracy = 0.1 km; data quality = as documented by the source; supported algorithm = a geodesic method on EPSG:4326 input (Calculate Geometry "Length (geodesic)", PostGIS geography, SDK geodesicLength, QGIS ellipsoidal); units = kilometres to 0.1. Their *real* question ("fastest") is a **network travel** question, which straight-line distance of any kind does not answer (6.6.3).

**6.7.** (a) Still 0.9996 — points on the central meridian have that scale regardless of latitude. (b) Larger — h = a/(ρ cos φ) grows as cos φ shrinks; at 45° N it is roughly 1/0.707 ≈ 1.41 times the ellipsoid-radius factor, i.e. about 1.42 (an exact figure is not required). (c) More misleading — the degree-to-metre factor for longitude falls further from 111 km (to about 79 km at 45° N), so a 111-km multiplier is further wrong.

## I.2 Answer key — 6.9 concept and scenario questions

**Q1.** (b). (a) fails because conformal projections distort area [S13]; (c) fails because no projection preserves all distances; (d) fails because area comparison needs equal-area [S13] [X05].

**Q2.** (1) *Assign* the established CRS — Define Projection to EPSG:32643 [S15] (or QGIS Define Shapefile projection [Q02]); stored values unchanged. (2) *Transform* — Project to EPSG:4326 [S16]; new output with changed values; no datum transformation since both are WGS 84. Full marks require the order (assign first), the "unchanged/changed" statements, and the note that the assignment rests on the office's documentation, not on the numbers.

**Q3.** Applicability, area of use, required resources, accuracy (6.3.2). "Default" is a list-ordering mechanism [X01], not evidence; "reversible" is a property of almost every method [S16] [E06] and says nothing about whether it fits this area or accuracy.

**Q4.** (b) and (d) are proven. (a) is false — display reprojection changes nothing stored [S13] [S14] [Q01]. (c) is false — Esri explicitly says on-the-fly display "should not be used for analysis or editing" [S14]; a 500 m rule also needs a documented method and units (6.4.2, 6.6).

**Q5.** (a) Degrees were treated as a unit of length and then converted with a single factor; longitude degrees shrink with cos φ, so the 1.1 km figure is about 8 % too large at 23° N for an east–west pair (6.5.2). (b) At 50° N, cos 50° ≈ 0.643 versus cos 23° ≈ 0.921, so the same 0.010° of longitude is only about 716 m against 1,025 m — the "111 km" error grows from 8 % to 55 %. (c) Any two of: geodesic length in Calculate Geometry Attributes [X04]; Measure tool in Geodesic mode [X07]; PostGIS `geography` distance [X13]; SDK `geodesicLength` [X14]; QGIS ellipsoidal measurement [Q03]. (Projecting a *copy* is also correct but changes stored data in the copy; accept if labelled as such.)

**Q6.** Team 1: planar in EPSG:32643; driving rows are study area (inside one zone) and required accuracy (1 m in 1.2 km ≫ the 0.4 m/km scale effect). Team 2: geodesic; driving rows are study area (all of India spans six UTM zones, 6.6.2) and supported algorithm (geodesic on EPSG:4326 input). Accept "planar in an India-wide national grid" for Team 2 *only* if the learner says its area of use and distortion must be checked against 1 km accuracy.

**Scenario 1.** *File K — wrongly assigned CRS.* The header says WGS84 but provenance says Kalianpur 1975; do not accept the header; establish the datum with the provider; if confirmed Kalianpur 1975, assign EPSG:4146, then transform with a documented record (e.g., EPSG:1156, accuracy 22 m, India mainland [E06]) and log the record and its accuracy; expect a shift of order 100 m (6.3.2). *File L — known CRS, needing (at most) conversion.* Provenance establishes EPSG:32643; the magnitudes agree; assign EPSG:32643 (this is not a guess — it is documented) and convert as needed; log the evidence. *File M — known CRS, needs no action beyond verification;* it may need conversion to the analysis CRS. A learner who calls L "unknown" must explain why the tracker's documented CRS is insufficient evidence; accept if they demand written confirmation. A learner who calls K "known" fails the item.

**Scenario 2.** Must contain: alignment shows consistent labels, not converted or analysis-ready data (6.4.1); a planar 500 m in Web Mercator at 23° N is about 8.6 %–9.2 % short on the ground (a "500 m" map buffer covers roughly 458–460 m of ground, since map length = ground × h or k; accept "about 9 %") (6.5.3); acceptable routes: Buffer with Method = Geodesic, or Buffer on a copy projected to EPSG:32643 with planar method, or the Output Coordinate System environment set to EPSG:32643 [S17] [X12]. Separately: lat/lon data support a geodesic buffer directly — Buffer creates geodesic buffers for geographic input with linear units [S17].

## I.3 Expected results — 6.8 lab and 6.9 practical (formula values; software reproduction required)

**Status.** All values below were computed from the cited formulas with standard-library arithmetic and cross-checked as described in 6.7.1. **They have not been reproduced in ArcGIS Pro or QGIS by the author.** Before issuing the lab, the instructor must run the steps in the installed software, record the values obtained, and enter them in the change log (I.8). Tolerances: 0.01 m for coordinates and lengths; 1 m² for areas. If Esri's "shape-preserving" geodesic area or QGIS's ellipsoidal area differ from the ellipsoidal value by more than 1 m², record the tool's stated method and adjust the learner tolerance with a note.

**6.8 lab (Fixture E6).**

| Item | Expected | Cross-check |
| --- | --- | --- |
| Ward A corners in EPSG:32643 (E, N) | (498975.185, 2543519.799); (500000.000, 2543519.764); (500000.000, 2544626.761); (498975.260, 2544626.796) | Symmetry with Ward B (501024.815 / 501024.740 mirror the A corners about 500,000) |
| Ward B corners | (500000.000, 2543519.764); (501024.815, 2543519.799); (501024.740, 2544626.796); (500000.000, 2544626.761) | As above |
| Q1–Q2 planar (EPSG:32643) | 664.20 m | Northing difference: 2544405.362 − 2543741.163 = 664.199 |
| Q1–Q2 geodesic | 664.46 m | Three independent methods agree (6.7.1) |
| Q3–Q4 planar / geodesic | 1,024.78 m / 1,025.19 m | Parallel-arc formula agrees |
| Q1–Q3 planar / geodesic (extra spot-check) | 610.60 m / 610.85 m | Ratio 0.9996 |
| Q4–Q5 geodesic | 717.63 m | — |
| Q2 to Ward A's north edge (lat 23.010) along the meridian | 221.49 m | Sanity value for containment questions |
| Q5 east of Ward B's east edge (lon 75.010) along the parallel | 205.04 m | Q5 outside both wards |
| Ward A planar area (EPSG:32643) | 1,134,426 m² | = 0.9992 × ellipsoidal |
| Ward A ellipsoidal (geodesic) area | 1,135,335 m² | Ward B identical by symmetry |
| Ward A Web Mercator planar area | 1,346,271 m² | Diagnostic only |
| Web Mercator X/Y of Q1 | (8348961.809, 2632260.506) | For the optional interactive (M.5) |

**6.9 practical (fresh fixture, UTM zone 44N, central meridian 81° E, EPSG:32644).**

| Item | Expected |
| --- | --- |
| Classification | `requests_z.geojson`: known (EPSG:4326, RFC 7946 order). `zone_z.csv`: known (EPSG:32644, documented). `legacy_z.csv`: **wrongly labelled** — values like 500000/2931057 cannot be degrees; do not guess; note that they *resemble* UTM 44N eastings/northings (L1 coincides with R1 in that reading) and ask the provider to confirm before any assignment. A learner who assigns EPSG:32644 to `legacy_z.csv` and proceeds without confirmation triggers the critical-misconception rule; a learner who states the hypothesis and *stops* is correct. |
| Analysis CRS | EPSG:32644 (data at 81° E lie on that zone's central meridian; zone 44 covers 78°–84° E) |
| R1–R4 in EPSG:32644 | R1 (500000.000, 2931057.600); R2 (500000.000, 2931611.358); R3 (499601.408, 2931334.485); R4 (500398.592, 2931334.485) |
| R1–R2 planar / geodesic / Web Mercator planar | 553.76 m / 553.98 m / 621.96 m |
| R3–R4 planar / geodesic / Web Mercator planar | 797.18 m / 797.50 m / 890.56 m |
| Zone polygon (corners c1–c4 = lon 80.995–81.005, lat 26.500–26.505 in WGS 84) area: planar / ellipsoidal / Web Mercator | 551,808 m² / 552,250 m² / 692,358 m² |
| Ratios to explain | Planar/geodesic = 0.9996 (lengths) and 0.9992 (area); Web Mercator/geodesic ≈ 1.123 N–S (h at 26.5° N) and ≈ 1.117 E–W (k); area ≈ 1.254 |
| Hand-check | R1–R2 from northings: 2931611.358 − 2931057.600 = 553.758 m |

## I.4 Common mistakes and remediation

| Mistake | How it shows | Remediation |
| --- | --- | --- |
| Running Define Projection to "move" a misplaced layer | Layer lands somewhere else or stays put; log says "fixed projection" | Re-teach 6.2.1's table with the two-copy diagnostic (6.2.3); require the learner to state, for any operation, whether the numbers change |
| Leaving XY Table To Point's Coordinate System blank | Points near (0°, 0°) or in the ocean [X15] | Add "every import states its CRS" to the learner's checklist; re-run |
| Reporting 0.006 or 0.010 as a distance | A "distance" with no unit or with "units" | Hand-check with 6.5.2's degree lengths; require units on every number |
| Measuring in a Web Mercator map frame and reporting ground metres | 725.6 m instead of 664 m | Show h/k (6.5.3); require the measurement method and CRS in the log |
| Treating the geodesic column as "truth" and the UTM column as "wrong" | Log calls U "error" | 6.7.3: both are models; U's 0.9996 is documented and acceptable for the requirement |
| Accepting a datum transformation without reading its record | Log says "default" | I.1 (6.3) and 6.3.2's record table; require the four fields |
| Using the sphere (haversine) and calling it geodesic | 667.17 m instead of 664.46 m | 6.5.1's PostGIS example; require the reference surface in the log |

## I.5 Fresh exercise for retesting (different values, same concepts)

Place a fixture on the central meridian of UTM zone 45N (87° E; EPSG:32645 — verify in the registry) at about 21.5° N. Define two points 0.008° apart in latitude on the meridian and two points 0.012° apart in longitude at the same latitude; define one ward as a 0.010° × 0.008° rectangle. Expected properties (do not release numbers until computed and reproduced in software, as in I.3): eastings of on-meridian points exactly 500,000; planar/geodesic ratio 0.9996; Web Mercator ratios h and k at 21.5° N (k ≈ 1/cos 21.5° ≈ 1.075 before the ellipsoid-radius correction); area ratio 0.9992. Difficulty and concepts match 6.9.3.

## I.6 Building and verifying the lab in software — not execution-tested

1. Record ArcGIS Pro version (documentation read at 3.7 [X16]), licence level, and whether ArcGIS Coordinate Systems Data is installed. Record QGIS and PROJ versions.
2. Create the three files from 6.8.3 and run Parts A–E exactly as written; note every UI label that differs from the text (e.g., how GeoJSON is added; the exact CRS name strings shown) and update the Procedure boxes.
3. Compare every value with I.3; enter results and deviations in I.8.
4. For 6.3.2: run Project on a Kalianpur 1975 (EPSG:4146) copy of Q3 to WGS 84 and record which transformations ArcGIS Pro lists, which is default, and the resulting shift; repeat in QGIS (Reproject layer, Coordinate Operation field). Replace or annotate the 112 m figure with what was observed, keeping the hand-calculated value labelled as such.
5. For 6.8.8: capture the QGIS measure dialog's Info text for both Cartesian and Ellipsoidal modes and insert it as an example.

## I.7 Blueprint corrections, source notes, and verification items

- **No factual error was found in the blueprint's Chapter 6 text.** Its references [S13]–[S17] were read in full and support the statements they are attached to.
- **Source wording note (Buffer).** The Buffer page describes geodesic distances as "calculated between two points on a curved surface (the geoid)" [S17]. The calculation is performed on the ellipsoid of the input's geographic CRS, as the Measure page ("on a spheroid (ellipsoid)") [X07] and PROJ [X06] state; the chapter uses "ellipsoid" and flags the page's wording. No learner-facing claim depends on the difference.
- **Karney (2013)** is cited *via* the PROJ page [X06]; the paper was not read. The chapter's geodesic values were produced with Vincenty's formulas and cross-checked by two independent methods (6.7.1), and the accuracy claim quoted ("less than 15 nanometers" for the PROJ implementation) is PROJ's, not a claim about the chapter's arithmetic.
- **India's longitude span (6.6.2)** is given as "roughly 68° E to 97° E" for orientation only; it is not a boundary statement and was not taken from a cited source. The zone arithmetic (zone n spans 6n − 186 to 6n − 180 degrees east) is from the UTM definition [S13] [E01, Table 4].
- **epsg.org** blocked the automated fetcher; its pages were read in the built-in browser. The dataset version displayed was v13.103.
- **Verification items open until I.6 is completed:** every Procedure box; the geodesic-area tolerance; the 6.3.2 shift figure in software; the QGIS default project-CRS behaviour on the training installation; whether the installed ArcGIS Pro adds `.geojson` directly.

## I.8 Instructor change log

| Date | Change | Affected sections | By |
| --- | --- | --- | --- |
| 2026-09-19 | Initial revision 1.0 from blueprint 1.0; all values are formula results awaiting software reproduction (I.3, I.6) | All | Author |

---

# Media Appendix

## M.1 Slide outline (mapped to module IDs)

| # | Module | Slide title | Visual | Question before the answer (speaker notes) |
| --- | --- | --- | --- | --- |
| 1 | Front | Chapter 6 — Projections, transformations, measurement | Chapter outcomes table | "What did Chapter 5 forbid you to do with an unknown CRS?" |
| 2 | 6.1.1 | Flattening breaks something | Diagram D6-1 (three panels, labelled schematic) | "Which of area, shape, distance, direction matters for crew dispatch?" |
| 3 | 6.1.2 | "Preserves" has fine print | Table of conformal / equal-area / equidistant / compromise with the condition column | "Where does an equidistant projection keep distances?" |
| 4 | 6.1.3 | City task versus world map | Two-column comparison; Ward A's three areas (1,135,335 / 1,134,426 / 1,346,271 m²) | "Which area is measured on the Earth?" |
| 5 | 6.1.2 | One UTM zone | Diagram D6-2 | "What is 0.9996?" |
| 6 | 6.2.1 | Two operations, opposite effects | The assign/transform table with ArcGIS, QGIS, PostGIS rows | "Which one changes the numbers?" |
| 7 | 6.2.3 | Copy T versus Copy L | Diagram D6-3 stills (before/after cards) | "Where does Copy L draw?" |
| 8 | 6.2.3 | Loud, quiet, quietest | Table 6.2 symptoms | "Which failure is 110 m?" |
| 9 | 6.3.1 | Same datum or different datum | Two flow diagrams: projection-only vs projection + transformation | "Does UTM 43N → WGS 84 need a transformation?" |
| 10 | 6.3.2 | Read the record | EPSG:1156 record as a card with the four inspection fields highlighted | "What is 22 m?" |
| 11 | 6.4.1 | Aligned on screen ≠ prepared | Screenshot placeholder of Map Properties ▸ Coordinate Systems with the Layers folder | "Did adding the layer change the file?" |
| 12 | 6.4.2 | The five-row worksheet | Table 6.4 blank, then filled | "Which two rows do people merge?" |
| 13 | 6.4.3 | Read the tool's rule | Three cards: geoprocessing first-input rule; Buffer's rule; Calculate Geometry's refusal | "Does Buffer need reprojected input?" |
| 14 | 6.5.1 | Planar, geodesic, spherical | Implementation table | "What does PostGIS geography return?" |
| 15 | 6.5.2 | Degrees are angles | 1° latitude ≈ 110.7 km; 1° longitude × cos φ; the 0.010° "distance" | "Why is 1,110 m wrong?" |
| 16 | 6.5.3 | Web Mercator: metres, distorted | Formulas; h = 1.092, k = 1.086 at 23° N; "not always wrong" list | "Name one correct use of EPSG:3857." |
| 17 | 6.6.1–6.6.2 | The decision worksheet | Table 6.6; India's six UTM zones | "Which zone for 91° E?" |
| 18 | 6.6.3 | Four distances | Table of straight-line / network / 2D / 3D | "Which one answers 'fastest'?" |
| 19 | 6.7 | The experiment | Table 6.7 with the ratios annotated | "Explain 0.9996 and 1.092 without recomputing." |
| 20 | 6.8 | The lab | Five parts, deliverables | "What must never change during the lab?" (the originals) |
| 21 | 6.9–6.10 | Gate and transition | Critical misconceptions; "your log is provenance" | — |

## M.2 Audio-lesson outline

Narration rules: say "assign" and "transform" as distinct words and never "reproject" for both; read every coordinate as "latitude twenty-three point zero zero two north, longitude seventy-five east" or as "easting five hundred thousand metres, northing two million five hundred forty-three thousand seven hundred forty-one point one six three metres" — always naming the unit and the order; pronounce EPSG as letters; pause for prediction before each answer.

1. **Recap (2 min).** A pair of numbers needs a CRS, units, and axis order. Today we act on coordinates.
2. **6.1 (6 min).** Describe D6-1 verbally: "Imagine four equal coins laid on a globe at the equator, at twenty-three degrees, at forty-five, at seventy. Flatten with a conformal projection: the coins stay round but the northern ones swell. Flatten with an equal-area projection: they keep their size but squash." Then the three Ward A areas. Pause: "Which is measured on the Earth?"
3. **6.2 (7 min).** Read Copy T and Copy L as two record cards: "Label: EPSG thirty-two six forty-three. Easting five hundred thousand. Northing two million five hundred forty-three thousand seven hundred forty-one." Describe what each operation changes. Pause: "Where does Copy L draw?" Then the three volumes of failure — loud, quiet, quietest.
4. **6.3 (6 min).** Same datum versus different datum. Read the EPSG:1156 record aloud field by field, ending with "Care!". State "about one hundred and twelve metres" and "accuracy twenty-two metres". Pause: "Would you see that on a city map?"
5. **6.4 (5 min).** Alignment is a view. Walk the five rows of the worksheet with the E6 answers. Read Buffer's rule verbatim.
6. **6.5 (7 min).** "One degree of latitude here is about one hundred and ten point seven kilometres; one degree of longitude is that times the cosine of the latitude — about one hundred and two point five kilometres." Then the 0.010 "distance" trap. Web Mercator: "metres, yes; ground metres, no: nine per cent long here." Pause: "Is every EPSG three-eight-five-seven operation wrong?"
7. **6.6 (4 min).** Six worksheet rows; six UTM zones for India; four kinds of distance.
8. **6.7 (5 min).** Read Table 6.7 row by row with the ratios, then the reasons. "Nothing here is ground truth."
9. **6.8 (3 min).** What the lab asks; detailed clicks are in the handout, not in the audio.
10. **6.9 (2 min).** The critical misconceptions, and the transition to Chapter 7.

## M.3 Diagram specifications

**D6-1 — Distortion families (schematic).** Three panels, four equal circles each at latitudes 0°, 23°, 45°, 70°; panel labels "reference (ellipsoid)", "conformal: shape kept, area not", "equal-area: area kept, shape not"; mandatory caption "Schematic; circle sizes not computed from any projection". Colours: one neutral fill; no basemap.

**D6-2 — One UTM zone (schematic).** Tall strip; central meridian solid, labelled "scale 0.9996"; two dashed lines labelled "scale 1.0"; edges labelled "scale slightly > 1"; outside band shaded, labelled "use the neighbouring zone"; caption citing [E01, Table 4] and stating that positions are not to scale.

**D6-3 — Metadata change versus coordinate change (animation, 2 × before/after).** Two panels side by side. Each shows: a record card (fields `CRS label`, `E or lon`, `N or lat`), a mini-map with Wards A and B (from Table F6-1, projected consistently in the panel's map CRS, EPSG:32643), and the point Q1. *Left panel, operation caption "Define Projection (assign) → EPSG:4326":* the label field flips; the number fields stay 500000.000 / 2543741.163; a "file on disk changed? YES (metadata only)" badge; the point leaves the map (an arrow off-panel labelled "outside valid range"). *Right panel, operation caption "Project (transform) → EPSG:4326":* the label flips and the number fields become 75.000 / 23.002 with a "new output dataset" badge; the point stays on the A/B boundary. Both panels display the before and after values in text, not only graphically. Validation: the after-values must equal Table F6-2 and Copy T in 6.2.3.

**D6-4 — Table 6.7 as a bar chart (optional).** Five bars for Q1–Q2 (G, U, W, S) plus a separate "D: 0.006°, not a distance" note outside the axis; bar labels carry units; caption states the numbers are formula results.

## M.4 Product-to-behaviour table for slides (claims dated 19 September 2026)

| Behaviour | ArcGIS Pro 3.7 docs | QGIS 3.40 docs | PostGIS docs | JS SDK 5.1 docs |
| --- | --- | --- | --- | --- |
| Assign CRS (metadata only) | Define Projection [S15] | Assign projection; Define Shapefile projection; layer CRS setting [Q02] [Q01] | `ST_SetSRID` [X08] | — |
| Transform coordinates | Project [S16]; Project Raster [X03] | Reproject layer [Q02] | `ST_Transform` [X09] | (projection service / `projection` module — not verified for this chapter) |
| Display reprojection | Map adopts first layer's CRS; on-the-fly [S14] | Project CRS; on-the-fly [Q01] | n/a | n/a |
| Planar vs geodesic choice | Tool parameter / property / Measure mode [S17] [X04] [X07] | Project ellipsoid setting; dialog Cartesian/Ellipsoidal; "Calculate using" [Q03] [Q04] [Q05] | `geometry` vs `geography` type [X13] | `planar*` vs `geodesic*` functions [X14] |
| Web Mercator planar measurement | Refused by Calculate Geometry [X04] | Allowed if ellipsoid = None (learner must know it is distorted) [Q04] | Allowed on `geometry` (units of SRID) [X13] | Allowed but discouraged [X14] |

## M.5 Interactive and 3D material

**Proposed interactive: "Drag a square across latitudes" (2D map pair; no 3D needed).**

- **Learning objective.** Show that a fixed 1 km × 1 km ground square has a different *planar* size in Web Mercator and in a UTM zone depending on latitude and distance from the central meridian, and that the difference is a computable property of the projection, not noise.
- **Objects.** Left pane: a globe-style or equirectangular locator (schematic). Right pane: two numeric panels ("Web Mercator planar width/height, m"; "UTM zone N planar width/height, m") and a small drawn square whose *drawn* proportions follow the computed h and k.
- **Labels and units.** Latitude (°), longitude (°), zone number, central meridian (°), h, k, k₀ = 0.9996, all lengths in metres to 0.01 m, areas in m² to 1 m².
- **Controls.** Drag the square's centre; a slider for latitude; a read-out of the UTM zone (computed from longitude) and distance from its central meridian.
- **Expected behaviour.** At the fixture centre (23.005° N, 75.000° E) the panel must show Web Mercator height factor 1.0920, width factor 1.0858, and UTM factor 0.9996 (both directions) — i.e., Table 6.7's values; at the equator k = 1 exactly and h = a/ρ ≈ 1.007 (φ = 0); moving toward 60° N the Web Mercator factors approach 2.
- **Formulas and library.** Web Mercator h and k from Guidance Note 7-2 §3.2.1.2 [E01]; UTM from §3.2.3.1 JHS formulas [E01] or any PROJ-based library (PROJ's `tmerc` default algorithm is "the most precise one" [X17]); ellipsoid WGS 84 [E04].
- **Validation fixtures.** Q1–Q2 and Q3–Q4 of Table 6.7 (0.01 m tolerance) and Ward A's three areas (1 m² tolerance); the build must reproduce these before the interactive is used in a class.
- **Labelling of simulation.** The locator globe is schematic; the square is *illustrative geometry*; no basemap imagery, so that visual realism cannot imply measurement accuracy.
- **2D/text alternative.** Table 6.7 and slide 19 carry the same content; the interactive is optional.

**3D content: none proposed.** The chapter's concepts are about flat-plane versus surface measurement; a 3D globe with exaggerated distortion would risk exactly the "stylised animation as distortion model" error the blueprint forbids. A globe is useful for axes (Chapter 5) but adds nothing measurable here.
