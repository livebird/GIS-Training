# GIS and ArcGIS Fundamentals — Phase 1

# Chapter 5 — Coordinates and coordinate reference systems

## Document metadata

| Item | Value |
| --- | --- |
| Chapter ID | Phase 1, Chapter 5 |
| Title | Coordinates and coordinate reference systems |
| Source specification | `GIS_Phase_1_Chapter_Authoring_Blueprint.md`, revision 1.0 (18 September 2026) |
| Document revision | 1.0 — 19 September 2026 |
| Reference-check date | 19 September 2026 (every cited page was opened and read on this date; see the reference list for exact URLs) |
| Audience | Software developers at LiveBird Technologies who have completed Chapters 1–4 |
| Software referenced | ArcGIS Pro (documentation pages labelled "ArcGIS Pro 3.7" on the check date); ArcGIS Maps SDK for JavaScript (documentation labelled 5.1); ArcGIS REST API geometry documentation; QGIS Desktop 3.40 (LTR documentation edition) and the QGIS *Gentle Introduction to GIS* 3.44 edition named in the blueprint; EPSG Geodetic Parameter Dataset v13.103 (registry read online); PostGIS documentation; Leaflet 1.9.4 (as a coordinate-order example only) |
| Execution status | **No procedure in this chapter has been executed in ArcGIS Pro or QGIS by the author.** Interface steps were written from the official pages cited beside them and are marked *not execution-tested*. All arithmetic (degree conversions, plausibility ranges, fixture distances) was hand-checked and is shown step by step so that you can check it too. |
| Data status | Every coordinate, file name, record, and date in this chapter is **synthetic training material**. The planar training grid has no Earth location. The Earth-referenced example set (E5) uses round-number coordinates that fall in western India only so that plausibility checks have something to compare with; it does not describe any real municipality, asset, or survey. |

### How to use this document

Read the modules in order; each one assumes the ones before it. Boxes labelled **Procedure (version-specific)** contain interface steps that will age; everything outside those boxes is durable concept. Boxes labelled **Platform note** separate one product's behaviour from a general GIS principle or a format rule. Boxes labelled **Verification item** tell an instructor what must be checked in the installed environment before a step is treated as fact. Comprehension checks have no answers in the learner text; answers are in the Instructor Appendix, which learners should not open before the progression gate in 5.9.

---

## Prerequisites

- **Chapters 1–2 completed.** You can frame a spatial question, and you know the roles of desktop GIS, hosted platforms, servers, and client libraries.
- **Chapter 3 completed.** You know that a feature has geometry (point, line, polygon) plus attributes, that a vertex is a coordinate pair such as (1200, 250), that a Z value is an optional third number, and that a dataset is different from a layer that displays it.
- **Chapter 4 completed.** You know that a raster is a grid of cells with a cell size and an origin, and that an elevation value needs units and a vertical reference (this chapter supplies that reference).
- Ordinary developer experience: you know what a schema, a data type, a unit, a serialisation format (JSON, CSV), and an API contract are. These are used as analogies and are not taught here.
- No ArcGIS account, licence, or installation is needed. Module 5.7 and the optional Part B of the lab include software steps for ArcGIS Pro **or** QGIS; nothing in the assessment depends on having run them.

## Learning outcomes

By the end of this chapter you should be able to:

| ID | Outcome | Where it is taught | Where it is checked |
| --- | --- | --- | --- |
| LO1 | Explain why two numbers alone do not identify a place, and write a complete coordinate description (values, CRS, units, axis order of the format or API, precision). | 5.1 | 5.9 concept Q1; oral |
| LO2 | Read and write latitude and longitude in decimal degrees and in degrees–minutes–seconds, with correct signs for each hemisphere, and convert between the two forms. | 5.2 | 5.9 concept Q2; 5.8 lab (DMS step) |
| LO3 | Explain, in order, what the Earth's shape, an ellipsoid, and a datum/reference frame each contribute, and why a datum name is not a complete CRS. | 5.3 | 5.9 concept Q3 |
| LO4 | Distinguish a geographic CRS (angular units) from a projected CRS (linear units), read the full definition of EPSG:4326, EPSG:3857, and a UTM zone, and explain why a metre unit does not prove a CRS is fit for measurement. | 5.4 | 5.9 concept Q4; scenario S1 |
| LO5 | State the coordinate order required by RFC 7946 GeoJSON, contrast it with the formal EPSG:4326 axis order and with other APIs, and detect a swapped pair that passes a range check. | 5.5 | 5.9 concept Q5; practical |
| LO6 | Explain why "height = 30" is ambiguous, distinguish ellipsoidal from gravity-related heights and height-above-ground from elevation, and state that a stored Z is not automatically used by an operation. | 5.6 | 5.9 concept Q6; scenario S2 |
| LO7 | Find a dataset's CRS and a map's display CRS in ArcGIS Pro or QGIS, explain why they can differ, and apply the coordinate-metadata checklist. | 5.7 | 5.8 lab; 5.9 practical |
| LO8 | Classify a coordinate set as geographic, projected, swapped, or unresolvable from the numbers alone, citing evidence, and refuse to guess an unknown CRS. | 5.8 | 5.8 lab; 5.9 practical; oral |

## Required materials

- This document and a calculator (a phone calculator is enough).
- The **Chapter 5 case files** in module 5.8, printed in this document as tables; no download is needed.
- **Optional for 5.7 and lab Part B:** ArcGIS Pro **or** QGIS Desktop. If neither is available, read the described procedure; the paper part of the lab and the whole assessment can be completed without software.
- **Not required:** an ArcGIS Online account, any transformation or reprojection (that is Chapter 6), or any real-world survey data.

## Recap of the preceding chapters

Chapter 3 taught that a **feature** is a represented thing with **geometry** and **attributes**, that geometry is built from **vertices**, and that a vertex is written as ordered values such as (x, y) with an optional Z. It deliberately did *not* say what those values mean on the Earth. Chapter 4 taught the **raster** model: rows, columns, cells, a cell size, and an origin; it showed that a cell value such as an elevation needs a **unit** and a **vertical reference**, and deferred that reference to this chapter.

Every coordinate you met so far lived on the flat **training grid**: metres, x to the right, y upward, origin at (0, 0), and — importantly — **no Earth location and no coordinate-system identifier**. That was a deliberate simplification. This chapter removes it. It answers the question Chapters 3 and 4 left open: *what do the numbers refer to?*

## The recurring scenario and the Chapter 5 fixtures

The fictional municipality continues: **assets**, **roads**, **wards**, **requests**, and **inspections** keep the meanings fixed in Chapter 1. This chapter uses **two separate fixtures** and never mixes them. This matters: mixing a planar training grid with Earth coordinates is exactly the kind of mistake this chapter teaches you to catch.

**Fixture 1 — the planar training grid (unchanged from Chapter 1; synthetic).** Ward A is the square (0, 0)–(1000, 1000); Ward B is (1000, 0)–(2000, 1000); Road R1 runs from (0, 500) to (2000, 500); requests P1–P6 are at (200, 200), (800, 800), (1200, 250), (1700, 900), (1000, 500), (2200, 500). Units are metres on a flat plane. **It has no CRS.** Chapter 1's Tables F1–F4 hold the attributes.

**Fixture 2 — Earth-referenced example set E5 (new for this chapter; synthetic).** To talk about latitude, longitude, datums, and projected coordinates you need numbers that *do* refer to the Earth. Set E5 has three small groups:

**Table E5-G — geographic points (WGS 84, EPSG:4326, decimal degrees).**

| ID | Latitude (°) | Longitude (°) | Note |
| --- | --- | --- | --- |
| G1 | 23.0250 | 72.6000 | Round-number point in western India |
| G2 | 23.0375 | 72.5875 | About 1.9 km north-west of G1 |
| G3 | 23.0125 | 72.6125 | About 1.9 km south-east of G1 |

**Table E5-U — projected points (WGS 84 / UTM zone 43N, EPSG:32643, metres).**

| ID | Easting (m) | Northing (m) | Note |
| --- | --- | --- | --- |
| U1 | 254318.4 | 2547906.2 | Independent synthetic survey export |
| U2 | 254512.9 | 2548110.7 | |
| U3 | 254101.0 | 2547650.5 | |

**Table E5-H — one height record (synthetic).** Streetlight SL-0113: `height = 30`. No unit and no reference are stored. Module 5.6 uses this record.

Two warnings about E5. First, **U1–U3 are not asserted to be G1–G3 converted**; they are a separate set whose only purpose is to be plausible UTM numbers for the same general region. Converting between the two is Chapter 6 work and has not been done here. Second, the "about 1.9 km" notes are approximations from the rule of thumb taught in 5.2 (one degree of latitude is about 111 km, one degree of longitude at this latitude about 102 km): G2 is 0.0125° north (≈ 1.39 km) and 0.0125° west (≈ 1.28 km) of G1, and √(1.39² + 1.28²) ≈ 1.9 km. They are not measured distances.

---

## 5.1 Start with the ambiguity of a coordinate pair

### 5.1.1 Two numbers are not a place

Here are two numbers a colleague pasted into a chat message:

> `23.025, 72.6`

Where is this? You may feel you can guess: the numbers look like "latitude, longitude" for somewhere in India. Now look at three more pairs, each taken from a real style of GIS data:

| Pair as written | What it could be | What you would need to know first |
| --- | --- | --- |
| `23.025, 72.6` | Latitude then longitude in degrees (a point in western India); **or** longitude then latitude (a point in the sea north of Norway, see 5.5.3) | Which number is which; the reference system; the units |
| `1200, 250` | Request P3 on the training grid in metres; **or** a point in feet in some local engineering grid; **or** nothing on the Earth at all | Whether the grid is tied to the Earth; the units; the origin |
| `254318.4, 2547906.2` | Easting and northing in metres in a UTM zone; **or** an entirely different projected system with a similar range | The named coordinate reference system, including which zone and hemisphere |

The lesson is simple. **A coordinate is a pair (or triple) of numbers *plus* an agreement about what the numbers mean.** The agreement is called a **coordinate reference system (CRS)** — a documented definition of the origin, the axes, the units, and the model of the Earth (if any) that the numbers refer to. Without the agreement, the numbers are just numbers. This is why a dataset that "has coordinates" can still be unusable: the coordinates are present, but the agreement is missing.

**New terms.** *Coordinate*: an ordered set of numbers giving a position in a reference system. *Coordinate reference system (CRS)*: the definition that gives those numbers meaning; older ArcGIS documentation says "coordinate system" or "spatial reference" for the same idea (Esri, *Properties of a spatial reference* — "A spatial reference describes where features are located in the real world"). *Axis*: one direction of measurement (x or y, easting or northing, longitude or latitude). *Origin*: the position where every coordinate value is zero.

### 5.1.2 Axes, origin, direction, and units — on a grid you already know

The training grid is a **Cartesian** system: two straight axes at right angles, an origin, and a unit. The QGIS introduction describes exactly this: "A two-dimensional coordinate reference system is commonly defined by two axes. At right angles to each other, they form a so called XY-plane" [S13]. Diagram D1 (specified in the Media Appendix) shows the grid with these four labelled facts:

1. **Origin** — (0, 0), the south-west corner of Ward A.
2. **Axes** — x increases to the right; y increases upward.
3. **Direction** — "right" and "up" are just the directions we chose when drawing the grid; nothing says right is east.
4. **Unit** — one grid unit is one metre. That is a statement in the fixture's documentation, not something the numbers reveal.

Now compare the training grid with **Earth-referenced** data, which is what most real GIS data is:

| Property | Training grid (Chapter 1 fixture) | Earth-referenced dataset |
| --- | --- | --- |
| Origin | (0, 0) at an arbitrary corner | Defined by the CRS — for example the equator and prime meridian (5.2), or a zone's central meridian and the equator with offsets (5.4) |
| Axis directions | Chosen by us | Defined by the CRS: east and north for most projected systems; north (latitude) and east (longitude) for geographic systems |
| Unit | Metres, by declaration | Degrees, metres, feet, or others — defined by the CRS |
| Tie to the Earth | **None.** The grid is graph paper. | Yes: the CRS includes a model of the Earth (5.3) |
| Identifier | None | Usually an authority code such as `EPSG:4326` (5.4) |

**Platform note — what software does with a grid that has no CRS.** ArcGIS Pro calls this an *unknown* coordinate system. When a map is set to unknown, "values are unitless", no on-the-fly projection happens, "editing is not permitted", and the measure tool "measures unitless, planar quantities only" (Esri, *Use an unknown coordinate system*). QGIS has a similar project option, *No CRS (or unknown/non-Earth projection)*, which "will disable ALL projection handling within the QGIS project, causing all layers and map coordinates to be treated as simple 2D Cartesian coordinates, with no relation to positions on the Earth's surface" (QGIS 3.40, *Working with Projections*). Both behaviours are correct for the training grid. They are a disaster for real data that merely *lost* its CRS metadata — a case the lab in 5.8 gives you.

### 5.1.3 A complete coordinate description

From now on, whenever this course writes a coordinate for anything other than the training grid, it uses a **coordinate card** with five lines. Notice that "X/Y" alone never appears on it.

| Line | Content | Example (point G1) |
| --- | --- | --- |
| 1. Values | The numbers, each labelled | latitude 23.0250, longitude 72.6000 |
| 2. CRS | Name **and** authority code | WGS 84, EPSG:4326 |
| 3. Units | From the CRS definition | degrees |
| 4. Order in this container | The rule of the file, API, or database the values sit in — not the rule of the CRS | Written here as *latitude, longitude* (prose). In a GeoJSON file it would be `[72.6000, 23.0250]` (see 5.5) |
| 5. Precision | How many digits are meaningful | 4 decimals ≈ 11 m (5.2.2) |

Line 4 is the one developers most often skip, and the one that causes the most silent errors. The CRS says what the axes *mean*; the container says in which *position* each axis is written. Those are two different contracts, and 5.5 shows that they can disagree.

**Analogy — a timestamp without a time zone.** `09:30` is not a moment in time until you know the time zone and the date format (`03/04` is March 4 or 3 April depending on the locale). A coordinate pair without a CRS is like `09:30` without a zone; a pair without an axis-order rule is like `03/04` without a locale. **Where the analogy stops:** converting a time between zones is exact and lossless. Converting a coordinate between two datums (5.3) is *not* exact; it depends on a chosen transformation with a documented accuracy. So "just convert it" is a safe answer for time zones and an unsafe answer for coordinates. Chapter 6 deals with that.

**Common misconception.** "Every GIS uses x = longitude and y = latitude, so I never need to write the order down." Many engines *do* store geographic data that way (5.5 lists verified examples), but the formal definition of EPSG:4326 lists its axes as latitude, longitude; some client libraries take (latitude, longitude); and some CSV exports put a `lat` column first. A developer who assumes one universal rule will eventually write a swapped pair that passes every range check.

**Comprehension check 5.1.** A colleague sends you a CSV with columns `id, x, y` and the message "these are in WGS 84". List the questions you still have to ask before you can load the file correctly, and say which of them the phrase "WGS 84" does not answer.

---

## 5.2 Explain latitude and longitude

### 5.2.1 The globe, the equator, the prime meridian, and angles

A **geographic coordinate system (GCS)** locates points on a rounded model of the Earth using **angles**, not distances. Esri's ArcGIS Pro documentation puts it this way: geographic coordinate systems are "based on a three-dimensional ellipsoidal or spherical surface, and locations are defined using angular measurements, usually in decimal degrees, measuring degrees of longitude (x-coordinates) and degrees of latitude (y-coordinates)" [S14].

Diagram D2 (Media Appendix) is a labelled globe. Read it with these definitions:

- **Equator** — the circle midway between the poles. It is the line of **zero latitude** (Esri, *What are geographic coordinate systems?*).
- **Prime meridian** — the line of **zero longitude**. For most geographic coordinate systems it runs "from the North pole to the South pole through Greenwich, England" [S13].
- **Latitude** — the angle north or south of the equator. "In the northern hemisphere, degrees of latitude are measured from zero at the equator to ninety at the north pole" [S13]; the southern hemisphere runs from 0 to 90 the other way. Lines of equal latitude are **parallels**; together with the meridians below they form the **graticule**, the network of reference lines drawn on a globe or map.
- **Longitude** — the angle east or west of the prime meridian, "from zero to 180 degrees East or West of the prime meridian" [S13]. Lines of equal longitude are **meridians**; they "converge at the poles" [S13].
- **Hemispheres** — north/south of the equator; east/west of the prime meridian. A point is in exactly one north–south hemisphere and one east–west hemisphere (the equator and the prime meridian themselves are the boundary cases, exactly as P5 sat on the shared ward boundary in Chapter 1).
- **Angular unit** — the degree. A GCS "is defined by a datum, an angular unit of measure (usually degrees), and a prime meridian" (Esri, *Properties of a spatial reference*).

**Why angles and not metres.** On a sphere there is no flat sheet to lay a ruler on. An angle from the centre of the Earth is the same kind of measurement everywhere on the surface, so it makes a clean global system. The price is that a degree is *not* a fixed ground distance in every direction — the next section explains.

### 5.2.2 Decimal degrees, degrees–minutes–seconds, and one checked conversion

Angles are written in two common ways.

- **Decimal degrees (DD):** one signed decimal number per axis, such as `23.0250`.
- **Degrees, minutes, seconds (DMS):** "Degrees are divided into minutes (′) and seconds (″). There are sixty minutes in a degree, and sixty seconds in a minute (3600 seconds in a degree)" [S13]. Example: `23° 01′ 30″ N`.

**Worked conversion (checked by hand).** Convert point G1 from DMS to DD and back.

*Given:* latitude `23° 01′ 30″ N`, longitude `72° 36′ 00″ E`.

*DMS → DD.* DD = degrees + minutes ÷ 60 + seconds ÷ 3600.

1. Latitude: 23 + 1 ÷ 60 + 30 ÷ 3600 = 23 + 0.016667 + 0.008333 = **23.0250**. Sign: N → positive.
2. Longitude: 72 + 36 ÷ 60 + 0 ÷ 3600 = 72 + 0.6 + 0 = **72.6000**. Sign: E → positive.

*DD → DMS (reverse check).*

1. Latitude 23.0250: whole degrees = 23; remainder 0.0250 × 60 = 1.5 minutes → 1 minute, remainder 0.5 × 60 = 30 seconds → `23° 01′ 30″`. ✔
2. Longitude 72.6000: whole degrees = 72; remainder 0.6 × 60 = 36.0 minutes → 36 minutes, 0 seconds → `72° 36′ 00″`. ✔

Both directions agree, so the conversion is verified. Notice that only four decimal places were used; more would suggest a precision the example does not have.

**How much is a decimal place worth?** The QGIS introduction gives a fixed figure for the equator: "one second of latitude or longitude = 30.87624 meters" [S13], and states that a degree of latitude spans 60 nautical miles [S13], which is about 111 km (60 × 1.852 km = 111.12 km). RFC 7946 makes the same point for GeoJSON: "6 decimal places … amounts to about 10 centimeters" [S18, §11.2]. From these, a hand-derived rule of thumb for **latitude** (an approximation — the true figure varies by a fraction of a percent with latitude because the Earth is not a perfect sphere, see 5.3):

| Decimal places | Smallest step (°) | About this far on the ground (latitude) |
| --- | --- | --- |
| 0 | 1 | ≈ 111 km |
| 1 | 0.1 | ≈ 11 km |
| 2 | 0.01 | ≈ 1.1 km |
| 3 | 0.001 | ≈ 111 m |
| 4 | 0.0001 | ≈ 11 m |
| 5 | 0.00001 | ≈ 1.1 m |
| 6 | 0.000001 | ≈ 0.11 m (the RFC's "about 10 centimeters") |

For **longitude** the ground distance per degree shrinks toward the poles: "At the equator, and only at the equator, the distance represented by one line of longitude is equal to the distance represented by one degree of latitude. As you move towards the poles, the distance between lines of longitude becomes progressively less" [S13]. At the latitude of E5 (about 23°) a degree of longitude is roughly 102 km rather than 111 km (111.32 km × cos 23° ≈ 111.32 × 0.9205 ≈ 102.5 km; an approximation on a sphere). This is the first concrete reason why **degrees are not metres**: the same degree difference is a different distance depending on where you are and which axis you are on.

### 5.2.3 Signs and hemispheres — check north/south and east/west separately

In the usual signed convention, **latitude is positive north** of the equator and negative south; **longitude is positive east** of the prime meridian and negative west. Esri's documentation states it the same way: positive values indicate north of the equator and east of the prime meridian; negative values indicate south and west [S14]. The QGIS introduction notes that western longitudes "receive negative assignment in digital applications" [S13].

Treat the two signs as **two independent checks**, because a mistake in one does not show up in the other:

| DMS with hemisphere letters | Signed decimal degrees | Check 1: N/S | Check 2: E/W |
| --- | --- | --- | --- |
| 23° 01′ 30″ N, 72° 36′ 00″ E (G1) | +23.0250, +72.6000 | N → + ✔ | E → + ✔ |
| 12° 30′ 00″ S, 45° 15′ 00″ W (synthetic) | −12.5000, −45.2500 | S → − ✔ | W → − ✔ |
| 33° 52′ 12″ S, 151° 12′ 36″ E (synthetic) | −33.8700, +151.2100 | S → − ✔ | E → + ✔ |

Hand check of the second row: 30 ÷ 60 = 0.5 → 12.5; 15 ÷ 60 = 0.25 → 45.25. Third row: 52 ÷ 60 = 0.8667 and 12 ÷ 3600 = 0.0033 → 33.87; 12 ÷ 60 = 0.2 and 36 ÷ 3600 = 0.01 → 151.21. ✔

"Usual" is deliberate. Some data sources write hemisphere letters instead of signs (`72.6 E`), some GPS receivers write `W` longitudes as positive numbers with a separate flag, and some old survey tables count longitude west-positive. **The CRS definition and the source's own documentation decide** — the numbers do not. When in doubt, a coordinate card (5.1.3) forces the question.

**Common misconception.** "If the number is between −90 and 90 it is a latitude; if it is between −180 and 180 it is a longitude." Every latitude is also inside the longitude range, and every longitude between −90 and 90 is also inside the latitude range. So a range check can confirm that a value *could* be a latitude; it cannot prove that it *is* one. Module 5.5.3 builds a whole exercise on this.

**Comprehension check 5.2.** Convert `18° 57′ 36″ N, 72° 49′ 48″ E` to signed decimal degrees, showing the arithmetic, then convert your answer back to DMS to prove it. State how many decimal places you kept and roughly what ground distance that corresponds to.

---

## 5.3 Build the Earth-reference vocabulary

Latitude and longitude only make sense against a *model* of the Earth. This module builds that model in three layers — shape, ellipsoid, datum — and shows what each layer adds. You do not need geodesy (the science of measuring the Earth) beyond this; the goal is to read a CRS definition and know what its parts mean.

### 5.3.1 Earth shape → ellipsoid → datum/reference frame

**Layer 1 — the Earth's shape.** The Earth is not a perfect sphere. It is slightly flattened at the poles and bulges at the equator, and its real surface (mountains, seas) is irregular. Esri's documentation notes that "although the earth is best represented by a spheroid, it is sometimes treated as a sphere to make mathematical calculations easier" (Esri, *Spheroids and spheres*). A sphere is good enough for a world map; it is not good enough for locating a drain cover.

**Layer 2 — the ellipsoid (Esri calls it a spheroid).** A smooth mathematical surface that approximates the Earth's shape: a sphere squashed along one axis. It is described by two numbers — "the semimajor axis, or equatorial radius" and "the semiminor axis, or polar radius" — and a **flattening**, "the difference in length between the two axes expressed as a fraction" (Esri, *Spheroids and spheres*). The values for the most common ellipsoid, WGS 1984, are documented as *a* = 6 378 137.0 m, *b* = 6 356 752.31424 m, 1/*f* = 298.257223563 (same page). The difference between the two radii is about 21.4 km — small compared with the size of the Earth, but far too large to ignore for mapping.

**Layer 3 — the datum (also called a geodetic datum or reference frame).** An ellipsoid by itself floats in space; something has to say *where* it sits relative to the real Earth and how it is oriented. That is the datum: "A datum provides a frame of reference for measuring locations on the surface of the earth. It defines the origin and orientation of latitude and longitude lines" (Esri, *Datums*). Two kinds matter here: "An earth-centered, or geocentric, datum uses the earth's center of mass as the origin", whereas "A local datum aligns its spheroid to closely fit the earth's surface in a particular area" (same page). WGS 1984 is geocentric; the EPSG registry describes the datum of EPSG:4326 as the "World Geodetic System 1984 ensemble" (EPSG, *WGS 84*, code 4326).

**What each layer contributes — the summary table.**

| Layer | What it answers | If you change it… |
| --- | --- | --- |
| Shape (sphere vs. ellipsoid) | How round is the model? | Large-scale positions shift by up to kilometres |
| Ellipsoid (a, b, f) | Exactly how squashed is the model? | Positions shift by metres to hundreds of metres |
| Datum / reference frame | Where is the model anchored and how is it oriented? | **The same physical point gets different latitude/longitude numbers** |

**Worked example — the same point, different datums.** Esri's *Datums* page lists one control point in Redlands, California, in three datums (values quoted from that page, DMS, latitude then longitude):

| Datum | Latitude | Longitude |
| --- | --- | --- |
| NAD 1927 (local) | 34 01 43.72995 | −117 12 54.61539 |
| NAD 1983 (geocentric) | 34 01 43.77884 | −117 12 57.75961 |
| WGS 1984 (geocentric) | 34 01 43.778837 | −117 12 57.75961 |

*Reasoning.* The point did not move; the frame did. Between NAD 1927 and NAD 1983 the longitude differs by 57.75961″ − 54.61539″ = 3.14422″. Using the 5.2.2 rule of thumb (about 30.9 m per second at the equator, scaled by cos 34° ≈ 0.83 for longitude) that is roughly 3.14 × 30.9 × 0.83 ≈ **80 m** on the ground; the latitude difference of 0.04889″ is roughly 1.5 m. Between NAD 1983 and WGS 1984 the values differ only in the sixth decimal of a second — Esri notes the two "are identical for most applications" (same page). *What this establishes:* the datum is part of the coordinate's meaning; tens of metres of "error" can be nothing more than an unrecorded datum. *What it does not establish:* how to convert between datums — that is a **transformation**, Chapter 6.

**Analogy — a database schema version.** A row `(id=17, amount=250)` means one thing under schema v1 (amount in rupees) and another under v2 (amount in paise). The numbers are the same; the interpretation changed. A datum is the "schema version" of a latitude/longitude pair. **Where the analogy stops:** a schema migration is usually exact. A datum change is a physical re-measurement of the Earth, and converting between datums is approximate, with a documented accuracy.

### 5.3.2 A datum name is not a complete CRS

Developers often say "the data is in WGS 84" as if that settled everything. It does not. WGS 84 is a *datum* (more precisely, a datum ensemble), and many different coordinate reference systems are built on it. All of the following share the same base but produce completely different numbers for the same point:

| CRS (EPSG name) | Code | Type | Units | Axes (per EPSG) | Verified source |
| --- | --- | --- | --- | --- | --- |
| WGS 84 | 4326 | Geographic 2D | degree | latitude, longitude | EPSG registry, code 4326 |
| WGS 84 | 4979 | Geographic 3D | degree, degree, metre | latitude, longitude, ellipsoidal height | EPSG registry, code 4979 |
| WGS 84 / Pseudo-Mercator | 3857 | Projected | metre | easting, northing | EPSG registry, code 3857 |
| WGS 84 / UTM zone 43N | 32643 | Projected | metre | easting, northing | EPSG registry, code 32643 |

Point G1 is `latitude 23.0250, longitude 72.6000` in EPSG:4326. In EPSG:32643 the *same* point is an easting and a northing in the hundreds of thousands and millions of metres; in EPSG:3857 it is two different numbers in the millions of metres. All three are "WGS 84". So when someone says "WGS 84", ask: *which CRS on WGS 84?* Usually they mean EPSG:4326 in degrees, but "usually" is not a specification.

**Common misconception.** "WGS 84 and EPSG:4326 are synonyms." EPSG:4326 is *one* CRS whose datum is WGS 84. Saying "WGS 84" when you mean EPSG:4326 works only until you meet EPSG:3857 or a UTM zone, both of which are also on WGS 84.

### 5.3.3 Where this chapter stops: high-accuracy and time-dependent frames

Two facts are enough at this level, and both are boundaries rather than lessons.

1. The EPSG registry describes the WGS 84 datum as an **ensemble** (EPSG, code 4326 and 4979). That word signals that "WGS 84" has had several precise realisations over time, which differ from one another at the level of decimetres to a metre. For a city's asset register this does not matter; for survey control it does.
2. The Earth's surface moves (continents drift by centimetres a year), so some modern reference frames are **time-dependent**: a coordinate is only complete with an *epoch* (a date). The blueprint deliberately excludes this. If a project requires centimetre-level positions, "additional expertise" is the honest answer, and this chapter will not pretend otherwise.

**Comprehension check 5.3.** A vendor delivers two files that both claim "datum: WGS 84". One holds numbers like `23.0250, 72.6000`; the other holds numbers like `254318.4, 2547906.2`. Explain, using the three layers and Table 5.3.2, why both claims can be true and what each file is still missing.

---

## 5.4 Distinguish geographic and projected CRS

### 5.4.1 Angular coordinates versus planar coordinates in linear units

There are two families of horizontal CRS, and every dataset you meet belongs to one of them.

- A **geographic CRS** stores **angles** (latitude, longitude) on the ellipsoid. Units are degrees.
- A **projected CRS** stores **planar** coordinates (easting, northing, or x, y) on a flat surface, in **linear** units such as metres or feet. To get there, a mathematical **map projection** converts the angles to a plane. Esri's definition: a projected coordinate system "consists of a linear unit of measure (usually meters or feet), a map projection, the specific parameters used by the map projection, and a geographic coordinate system" (Esri, *Properties of a spatial reference*). The ArcGIS Pro help repeats the key point: "Projected coordinate systems are composed of a geographic coordinate system and a map projection" (Esri, *Work with coordinate systems*).

So a projected CRS **contains** a geographic CRS. Diagram D3 (Media Appendix) shows this as nested boxes:

```
Projected CRS  (e.g. WGS 84 / UTM zone 43N, EPSG:32643; unit: metre)
└── Map projection + parameters  (Transverse Mercator; central meridian 75°E;
│                                 scale 0.9996; false easting 500 000 m; false northing 0)
└── Geographic CRS  (WGS 84, EPSG:4326; unit: degree)
    └── Datum  (World Geodetic System 1984 ensemble)
        └── Ellipsoid  (WGS 84: a = 6 378 137 m, 1/f = 298.257223563)
```

(The projection parameters are those recorded by the EPSG registry for conversion 16043 "UTM zone 43N": latitude of natural origin 0, longitude of natural origin 75, scale factor 0.9996, false easting 500 000 m, false northing 0.)

**Why both families exist.** Geographic coordinates are global and unambiguous (given the datum), but you cannot do simple ruler arithmetic on them — 5.2.2 showed that a degree is a different distance at different latitudes and on different axes. Projected coordinates let you subtract two eastings and get a distance in metres — *within the area and accuracy the projection was designed for*. Flattening the Earth always distorts something; the QGIS introduction states that "every map shows distortions of angular conformity, distance and area" [S13]. Which property is distorted, and by how much, is Chapter 6. This chapter's job is to recognise the family, the units, and the identifier.

**Reading the numbers as a first clue (not proof).**

| You see | It is probably | Because |
| --- | --- | --- |
| Values within −90…90 and −180…180 with decimals | Geographic (degrees) | Only angles fit that range — but see 5.5.3 for the trap |
| Values in the hundreds of thousands and millions | Projected (metres or feet) | Typical of eastings/northings with false offsets |
| Small values such as 0–2000 with a stated local origin | A local/engineering grid or a training grid | Real projected systems rarely put a city near (0, 0) |

These are **clues**. Module 5.7.3 explains why a clue never licenses you to assign a CRS.

### 5.4.2 EPSG:4326, EPSG:3857, and the UTM family

**What "EPSG" means.** The EPSG Geodetic Parameter Dataset is a public registry of CRS definitions, each with a numeric code. ArcGIS calls the code a **well-known ID (WKID)**; the ArcGIS REST API documents that "Each coordinate system is defined by both a well-known ID (WKID) and a definition string" (Esri, *Using spatial references*). The code is a *key*; the definition is the *record*. Memorising keys without reading records is the mistake 5.4.3 corrects.

**EPSG:4326 — WGS 84 (geographic 2D).** Registry record (v13.103): name "WGS 84"; type Geographic 2D; datum "World Geodetic System 1984 ensemble"; coordinate system "Ellipsoidal 2D CS. Axes: latitude, longitude. Orientations: north, east. UoM: degree"; extent "World (by country)"; scope "Horizontal component of 3D system" (EPSG, code 4326). This is the CRS of GPS-style latitude/longitude and the CRS that RFC 7946 GeoJSON assumes (5.5.2). Esri's developer documentation calls 4326 "the most common spatial reference for storing a referencing data across the entire world" (Esri, *Spatial references*).

**EPSG:3857 — WGS 84 / Pseudo-Mercator (projected).** Registry record: name "WGS 84 / Pseudo-Mercator"; aliases "WGS 84 / Popular Visualisation Pseudo-Mercator" and "Web Mercator"; type Projected; "Cartesian 2D CS. Axes: easting, northing (X,Y). Orientations: east, north. UoM: m."; extent "World - 85°S to 85°N"; scope "Web mapping and visualisation"; remarks "Not a recognised geodetic system. Uses spherical development of ellipsoidal coordinates. Relative to WGS 84 / World Mercator (CRS code 3395) gives errors of 0.7 percent in scale and differences in northing of up to 43km in the map (21km on the ground)" (EPSG, code 3857). Esri's page on the Mercator projection adds that the geodetic coordinates "are projected as if they were defined on a sphere", that it "is the de facto standard for web maps and online services", and that it has "enormous area and distance distortions away from the equator" (Esri, *Mercator*). In ArcGIS the same CRS appears as **WGS 1984 Web Mercator (auxiliary sphere)**, and the REST API notes it "was originally assigned WKID 102100 but was later changed to 3857" — which is why you will see both numbers (Esri, *Geometry objects*).

Read the record and the unit lesson is already there: **the unit of EPSG:3857 is the metre, and the registry itself says its scale is wrong by 0.7 % even against a proper Mercator — and Mercator itself stretches distances increasingly away from the equator.** A unit of metres tells you what one coordinate step is *called*, not that one step is one metre on the ground. (For the curious: the Mercator scale factor is 1 ÷ cos(latitude), so at about 23° latitude a planar "metre" in EPSG:3857 is about 1.086 ground metres — an 8–9 % stretch. This is a standard property of the projection, stated here only to make the point concrete; Chapter 6 measures it properly.)

**UTM — a family, not one CRS.** The Universal Transverse Mercator system divides the Earth into "60 equal zones that are all 6 degrees wide in longitude … numbered 1 to 60, starting at the antimeridian (zone 1 at 180 degrees West longitude) and progressing East" [S13]. Each zone comes in a **northern** and a **southern** version; the QGIS example explains that "The S after the zone means that the UTM zones are located south of the equator" and that southern zones add "a false northing value of 10,000,000 m" [S13]. Within a zone, coordinates are an **easting** measured from the zone's central meridian plus a false easting of 500 000 m, and a **northing** measured from the equator [S13].

The zone for a longitude can be worked out by hand: zone = floor((longitude + 180) ÷ 6) + 1. For G1 (72.6°E): (72.6 + 180) ÷ 6 = 42.1 → floor 42 → zone **43**. The registry confirms that "WGS 84 / UTM zone 43N" (EPSG:32643) covers "World - N hemisphere - 72°E to 78°E - by country", with axes easting, northing in metres (EPSG, code 32643). India's territory spans roughly 68°E to 97°E, so it falls across **zones 42 to 47** — the blueprint's instruction "never prescribe one UTM zone for all of India" is a direct consequence. A UTM coordinate without its zone and hemisphere is incomplete; `(254318.4, 2547906.2)` could be in any of 120 zone variants.

**How the three appear in software (labels to verify in your installed version).**

| CRS | EPSG registry name | Typical ArcGIS Pro label (verify) | Typical QGIS label (uses EPSG names) |
| --- | --- | --- | --- |
| 4326 | WGS 84 | WGS 1984 (WKID 4326) | EPSG:4326 - WGS 84 |
| 3857 | WGS 84 / Pseudo-Mercator | WGS 1984 Web Mercator (auxiliary sphere) (WKID 3857; older 102100) | EPSG:3857 - WGS 84 / Pseudo-Mercator |
| 32643 | WGS 84 / UTM zone 43N | WGS 1984 UTM Zone 43N (WKID 32643) | EPSG:32643 - WGS 84 / UTM zone 43N |

**Verification item.** The ArcGIS Pro labels above follow Esri's naming pattern seen in its documentation (for example the analyzer message "Your web layer will use the WGS 1984 Web Mercator (Auxiliary Sphere) coordinate system"), but the exact strings in the *Coordinate Systems* list must be confirmed in the installed release before they are printed on a slide. The QGIS pattern `EPSG:code - name` was not execution-checked either.

### 5.4.3 Inspect the full definition, units, and area of use — do not memorise numbers

A CRS record has more fields than a code, and each one answers a question you will need:

| Field in the record | Question it answers | EPSG:32643 example |
| --- | --- | --- |
| Name | What is it called? | WGS 84 / UTM zone 43N |
| Authority and code | What is the unique key, and who issued it? | EPSG, 32643 |
| Type | Geographic or projected? | Projected |
| Base geographic CRS / datum | Which model of the Earth? | WGS 84 (ensemble) |
| Unit of measure | What is one coordinate step called? | metre |
| Axes and orientation | What do the two numbers mean and which way do they grow? | easting (east), northing (north) |
| Projection method and parameters | How was the plane made? | Transverse Mercator; central meridian 75°E; false easting 500 000 m; scale 0.9996 |
| **Area of use / extent** | Where is it valid? | N hemisphere, 72°E–78°E |
| Scope | What was it designed for? | "Navigation and medium accuracy spatial referencing" |

**Worked example — reading a record.** Set E5-U says "EPSG:32643". Checking the record: type projected, unit metre, axes easting/northing, area of use 72°E–78°E north of the equator. Are U1–U3 plausible? Easting 254 318 m is 500 000 − 254 318 = 245 682 m *west* of the central meridian (75°E). At about 23° latitude a degree of longitude is roughly 102.5 km (5.2.2), so 245.7 km ≈ 2.4° → about 72.6°E, which is inside the zone's 72–78°E band. Northing 2 547 906 m ÷ (about 111 km per degree) ≈ 23°N, which is in the northern hemisphere as the "N" requires. *What this establishes:* the numbers are consistent with the claimed CRS. *What it does not establish:* that the claim is true — a file in zone 44N with a wrong label would show the same "plausible" ranges. The rough figures also ignore the scale factor and the curvature of meridians on the projection, so they are plausibility checks, not conversions.

**Where to read the record.** In ArcGIS Pro, the *Coordinate Systems* tab of the map properties lets you right-click any entry and choose **Details** "to see the coordinate system parameters", and "The valid area of use for each coordinate system is specified in the list of details, and also visually as a blue rectangle on the map at the bottom of the dialog box" (Esri, *Work with coordinate systems*). In QGIS, the CRS selector shows a preview map of the "approximate area of use" and the read-only PROJ text of the definition (QGIS 3.40, *Working with Projections*). The EPSG registry at epsg.org shows the same record fields directly. Procedures are in 5.7.1.

**Analogy — a dependency's version number.** `lib@4326` is a key; the package's README tells you the API, the units, and the supported platforms. Nobody would ship code against a library after reading only its version number. **Where the analogy stops:** a package with the wrong version usually fails loudly. A dataset with the wrong CRS usually draws *somewhere*, silently.

**Common misconception.** "If the CRS unit is metres, the dataset is ready for measurement." EPSG:3857 is the standing counter-example: metres as a unit, distorted as a plane. Fitness for measurement depends on the projection, the location, and the area of use — Chapter 6.

**Comprehension check 5.4.** For each of EPSG:4326, EPSG:3857, and EPSG:32643, state: the family (geographic/projected), the unit, the axis meanings, and the area of use. Then say which of the three you would expect a web basemap to use and which you would *not* use for a dataset in southern India, giving the field of the record that tells you so.

---

## 5.5 Resolve coordinate-order confusion

### 5.5.1 "Latitude/longitude" in prose is not a serialisation rule

People *say* "lat, long". Textbooks list "latitude, longitude". The EPSG record for 4326 lists its axes as "latitude, longitude" (5.4.2). And yet most GIS engines store geographic geometry as **x = longitude, y = latitude**, because x is the horizontal (east–west) axis of a screen and a plane. Neither side is "wrong": one is a *conceptual* axis order, the other is a *storage* convention. The rule for a developer is therefore short:

> **Coordinate order follows the actual interface contract** — the specification of the file format, the API, the library class, or the database function you are using. Not the CRS record, not the prose, not your memory of the last project.

Table 5.5.1 lists contracts that were checked in the official documentation on the reference-check date. Each row is a *different* contract; none of them implies the others.

**Table 5.5.1 — Verified coordinate-order contracts (read each source before relying on it).**

| Interface | Order for geographic data | Documented wording | Source |
| --- | --- | --- | --- |
| RFC 7946 GeoJSON position | `[longitude, latitude(, altitude)]` | "The first two elements are longitude and latitude, or easting and northing, precisely in that order" | [S18] §3.1.1 |
| ArcGIS REST API (Esri JSON) point | `{"x": longitude, "y": latitude}`; in `paths`/`rings` arrays, x at index 0, y at index 1 | Example `{"x": -118.15, "y": 33.80, "z": 10.0}` | Esri, *Geometry objects* |
| ArcGIS Maps SDK for JavaScript `Point` | `x` = "The x-coordinate (easting)", `y` = "The y-coordinate (northing)"; separate `longitude`/`latitude` properties | "In any geographic spatial reference, the longitude will equal the x coordinate" | Esri, *Point* (SDK 5.1) |
| PostGIS `ST_Point(x, y, srid)` | x = longitude, y = latitude | "For geodetic coordinates, X is longitude and Y is latitude"; example `ST_Point(-71.104, 42.315, 4326)` | PostGIS, *ST_Point* |
| Leaflet `L.latLng(lat, lng)` | **latitude first** | "Represents a geographical point with a certain latitude and longitude"; example `L.latLng(50.5, 30.5)` | Leaflet 1.9.4 reference |
| ArcGIS Pro *XY Table To Point* tool | X Field = "the x-coordinates (longitude)", Y Field = "the y-coordinates (latitude)" | Tool parameter table | Esri, *XY Table To Point* |
| CSV / spreadsheet | **Whatever the column names say** — there is no rule | — | Your source's documentation |
| EPSG:4326 formal definition | latitude, longitude | "Axes: latitude, longitude. Orientations: north, east." | EPSG, code 4326 |

Notice the contrast between the first and the fifth rows: the same developer may write GeoJSON (`[lng, lat]`) in the morning and a Leaflet marker (`(lat, lng)`) in the afternoon. That is normal. What is not acceptable is *not knowing which contract is in force*.

**Platform note — "x/y" is not universal either.** In a projected CRS, x is usually easting and y northing, and the EPSG records for 3857 and 32643 agree (5.4.2). But a few national projected systems define their axes as northing, easting; that is exactly why the axis field of the record (5.4.3) exists and why the coordinate card (5.1.3) has separate lines for CRS and container order.

### 5.5.2 RFC 7946 GeoJSON: longitude, then latitude, in decimal degrees on WGS 84

Because GeoJSON is the format developers meet first, this section states its rules exactly, from the RFC.

1. **Position order.** "A position is an array of numbers. There MUST be two or more elements. The first two elements are longitude and latitude, or easting and northing, precisely in that order and using decimal numbers. Altitude or elevation MAY be included as an optional third element" [S18, §3.1.1].
2. **CRS is fixed — there is no `crs` member.** "The coordinate reference system for all GeoJSON coordinates is a geographic coordinate reference system, using the World Geodetic System 1984 (WGS 84) datum, with longitude and latitude units of decimal degrees" [S18, §4]. The RFC says this "is equivalent to the coordinate reference system identified by the Open Geospatial Consortium (OGC) URN urn:ogc:def:crs:OGC::CRS84" [S18, §4], and explains that the older `crs` member from the 2008 draft "has been removed from this version of the specification because the use of different coordinate reference systems … has proven to have interoperability issues" [S18, §4].
3. **Third element.** "An OPTIONAL third-position element SHALL be the height in meters above or below the WGS 84 reference ellipsoid" [S18, §4]. Module 5.6 explains why "above the ellipsoid" is a strong statement.
4. **Precision.** Six decimal places is "about 10 centimeters" and implementations "should consider the cost of using a greater precision than necessary" [S18, §11.2].

**Worked example — point G1 as GeoJSON.**

*Question:* write G1 (latitude 23.0250, longitude 72.6000, EPSG:4326) as an RFC 7946 Point.

*Reasoning:* rule 1 puts longitude first; rule 2 means no CRS member is written because WGS 84 degrees is the only allowed system; G1 is already in that system, so no conversion is needed.

```json
{ "type": "Point", "coordinates": [72.6000, 23.0250] }
```

*Check:* the first element (72.6) is the value labelled longitude on the coordinate card; the second (23.025) is the latitude. Both are within range. *What this does not establish:* if the source data had been in EPSG:32643 metres, writing the numbers into a GeoJSON file would be *wrong* even with the order right, because rule 2 fixes the CRS — the values would first have to be converted (Chapter 6).

**The name clash to remember.** GeoJSON's CRS is *CRS84* — longitude, latitude on WGS 84. The formal EPSG:4326 record is latitude, longitude on WGS 84. Same datum, same units, opposite axis order. Many tools label GeoJSON output as "EPSG:4326" because the datum and units match, and that is usually harmless — *unless* a consumer honours the formal EPSG axis order and swaps the values. If you see a library option about "axis order" or "authority axis order", this is the problem it exists to handle. Check the library's documentation; do not assume.

**Analogy — a JSON schema with positional arrays.** A tuple `[a, b]` in an API contract has meaning only because the schema says index 0 is `a`. GeoJSON's schema says index 0 is longitude. **Where the analogy stops:** a schema validator would reject a wrongly typed value; GeoJSON validators *cannot* reject `[23.025, 72.6]`, because both numbers are valid decimals inside the allowed ranges. The error is semantic, not syntactic — which is the whole point of the next section.

### 5.5.3 The swapped pair that passes every range check

**Setup (synthetic).** A script exports G1 from a CSV whose columns are `lat, lon`. The developer builds the position array in column order — a natural mistake — and writes:

```json
{ "type": "Point", "coordinates": [23.0250, 72.6000] }
```

**Apply the RFC.** Index 0 is longitude, so this point is at **longitude 23.025°E, latitude 72.6°N** — in the sea off the northern coast of Norway, thousands of kilometres from western India.

**Why a range check cannot catch it.** Latitude 72.6 is within −90…90. Longitude 23.025 is within −180…180. A validator that checks ranges — including the loose −400…400 check that Esri's *XY Table To Point* tool applies to geographic input (Esri, *XY Table To Point*) — accepts the file. The swap is invisible to syntax and to range validation.

**What does catch it.**

| Check | Result for the swapped G1 | Why it works |
| --- | --- | --- |
| Expected-extent check: "all points must fall in a box around western India, e.g. latitude 22–24, longitude 72–73" | **Fails** (latitude 72.6 is far outside 22–24) | Uses knowledge of where the data *should* be — this is the plausibility line of the coordinate card |
| Compare with source columns | **Fails** (`lat` column value appears at index 0) | Uses the two contracts explicitly |
| Draw it on a basemap | **Fails** (point in the sea) | Visual check; slow and manual, but decisive |
| Range check only | Passes | Both values are legal |

**When a range check *does* help.** A pair such as `[151.21, −33.87]` swapped to `[−33.87, 151.21]` fails the latitude range (151.21 > 90) and would be caught. Only pairs where **both** absolute values are ≤ 90 slip through — which includes all of India, most of Europe, and most of the Americas. Do not rely on luck.

**Common misconception.** "I use a library, so ordering is handled." The library applies *its* contract to *your* input. If you feed it `(lat, lon)` where it expects `(x, y)`, it will happily place your points in the sea. Libraries convert between CRSs on request; none of them can know which of your two columns is which.

**Comprehension check 5.5.** A Leaflet call `L.latLng(72.6, 23.025)` and a GeoJSON position `[72.6, 23.025]` contain the same two numbers in the same order. State where each one is on the Earth, and explain which contract you used for each answer.

---

## 5.6 Introduce vertical reference and dimensional limits

### 5.6.1 Height and depth need a unit and a vertical reference

Horizontal position was two numbers plus a CRS. Height is one number plus a **vertical coordinate system** — and the same ambiguity applies. Esri's definition: a vertical coordinate system "defines the origin for height or depth values" and "includes a unit of measure. This is always a linear unit, usually feet or meters"; it also "includes a direction. This specifies whether values are positive up, representing heights above a surface, or positive down, indicating that values are depths below a surface" (Esri, *Vertical coordinate systems*).

The surface that height is measured from — the **vertical datum** — comes in two kinds (Esri, *Coordinate systems, map projections, and transformations* [S14]):

- **Ellipsoidal height** — measured from the ellipsoid of 5.3.1, a smooth mathematical surface. This is what a GNSS/GPS receiver natively produces, and it is what GeoJSON's optional third element means ("height in meters above or below the WGS 84 reference ellipsoid" [S18, §4]). Registry example: EPSG:4979 "WGS 84" Geographic 3D, axes "latitude, longitude, ellipsoidal height", units "degree, degree, metre" (EPSG, code 4979).
- **Gravity-related height** — measured from a surface that follows gravity and approximates mean sea level, called the **geoid**. This is what "elevation above sea level" on a survey plan or a topographic map means. Registry example: EPSG:3855 "EGM2008 height", a vertical CRS whose datum is the "EGM2008 geoid", axis "height (H). Orientation: up. UoM: m", described as a "Zero-height surface approximating mean sea level" (EPSG, code 3855).

Diagram D4 (Media Appendix) shows the three surfaces stacked — ground, geoid, ellipsoid — with the two heights for one streetlight base. **The two heights of the same point are different numbers**, because the geoid and the ellipsoid are different surfaces. How different depends on where you are; you need a geoid model to find out, and that is beyond this chapter. What you must carry forward is only this: *a height value is meaningless until you know which surface it is measured from and in what unit*.

**Worked example — record E5-H.** The asset register stores `SL-0113, height = 30`. Ask the coordinate-card questions:

| Question | Possible answers for "30" | Consequence if you guess wrong |
| --- | --- | --- |
| Unit? | 30 m; 30 ft (≈ 9.1 m) | A factor of about 3.3 |
| Reference surface? | Above the ellipsoid; above the geoid (sea level) | A difference that depends on the geoid separation at the site — unknown until modelled |
| Height *of what*, from *where*? | Top of the pole above the ground; ground elevation of the base; top of the pole above sea level | Completely different quantities: a pole height (about 9 m for a typical street pole) versus a ground elevation (tens of metres in coastal western India, hundreds elsewhere) |
| Direction? | Positive up (height); positive down (depth) | Sign flip — critical for drains and boreholes |

*Answer:* the record is **undefined** as stored. It cannot be repaired by inspecting the number. It needs a field for the unit, a field (or metadata) for the reference, and a schema rule that says what "height" means for a streetlight. Chapter 8 (data modelling) is where that rule is written; this chapter's job is to refuse to interpret `30` without it.

### 5.6.2 Height above ground versus absolute elevation

Two heights that are often confused:

- **Height above ground (relative):** how far a thing is above the local ground surface. A pole is 9 m tall whether the street is at sea level or on a plateau.
- **Elevation (absolute):** the height of a point above a vertical datum. The *base* of the same pole might be at 55 m above the EGM2008 geoid.

They add: elevation of the pole top = elevation of the base + pole height. But they do not *substitute* for each other, and a column called `height` could hold either. Chapter 4 introduced the same distinction for rasters — terrain (ground) versus a surface that includes above-ground objects — and required the dataset's own definition; the same requirement applies to every Z value in a feature dataset.

**Analogy — absolute versus relative file paths.** `./logs` is a location relative to where you are; `/var/app/logs` is absolute. Both are "a path". A height above ground is a relative path; an elevation is an absolute one; and a value with no indication of which is a bug waiting to happen. **Where the analogy stops:** an OS resolves a relative path deterministically from the working directory. There is no "working directory" for a height; the reference has to be recorded explicitly.

### 5.6.3 A stored Z does not mean an operation uses Z

A dataset can carry Z values (Chapter 3 introduced X/Y/Z) and still be treated as flat by the next tool. Three verified illustrations:

- The ArcGIS Maps SDK for JavaScript `Point` has a `hasZ` property that "Indicates if the geometry has z-values (elevation)"; when false, z-values are excluded from the geometry (Esri, *Point*, SDK 5.1). Presence of a `z` number is not the same as the geometry being three-dimensional.
- GeoJSON *allows* a third element but does not require consumers to use it; in fact "In the absence of elevation values, applications sensitive to height or depth SHOULD interpret positions as being at local ground or sea level" [S18, §3.1.1] — a reminder that many applications are not height-sensitive at all.
- In ArcGIS Pro's *XY Table To Point*, Z is a separate optional parameter; only when a Z Field is specified does the output carry a vertical coordinate system (Esri, *XY Table To Point*).

The rule for this course: **each operation's dimensional behaviour is verified when that operation is introduced.** Chapter 6 states whether each measurement it uses is 2D or 3D. Nothing in this chapter entitles you to assume that a distance "includes the slope" because the data has Z.

**Common misconception.** "The survey has Z, so our distances are true 3D ground distances." They are whatever the tool computes — usually planar 2D unless documented otherwise.

**Comprehension check 5.6.** A drainage inspection record says `invert = 2.4`. Write the questions you must answer before the value can be used to compare two drains, and state which of them could be answered by an EPSG vertical CRS code and which only by the municipality's own schema documentation.

---

## 5.7 Inspect data CRS, map CRS, and coordinate metadata

### 5.7.1 Where the application reports a dataset's reference and the map's display reference

Two CRSs are in play whenever you look at a map:

- the **data CRS** (dataset/layer CRS): the reference the stored coordinates are actually in; and
- the **map CRS** (project/display CRS): the reference the map view draws everything in.

They can differ. The application converts each layer *for display* — "on the fly" — so that layers line up. The QGIS introduction describes this: "all layers that you then load, no matter what coordinate reference system they have, will be automatically displayed in the projection you defined" [S13]. ArcGIS Pro does the same: when layers are added, "they are automatically displayed using the current coordinate system of the map or scene. If the map or scene's geographic coordinate system is different than the geographic coordinate system of the layer, the data is projected in real time using a transformation" (Esri, *Work with coordinate systems*). Both products also warn that this is a display convenience: ArcGIS Pro says real-time projection "is not advisable if you are editing data or performing analysis" (same page). **A map showing two layers aligned proves nothing about the stored coordinates of either** — Chapter 6 builds on this.

Where does the map CRS come from? In ArcGIS Pro, "Empty maps and scenes derive their coordinate systems from the first layer added to them", and "In a new, empty map or local scene, the default horizontal coordinate system is WGS84 Web Mercator" (Esri, *Work with coordinate systems*). In QGIS, the option *CRS for projects* offers "Use CRS from first layer added" or "Use a default CRS" (QGIS 3.40, *Options*). Practical consequence: **if your map started with a web basemap, its map CRS is very likely EPSG:3857, whatever your data's CRS is.** Diagram D5 (Media Appendix) shows a map with a basemap in 3857, a layer in 4326, and a layer in 32643, all drawn together, with each layer's own CRS on a card.

**Procedure (version-specific) — ArcGIS Pro, documentation labelled 3.7; not execution-tested.**

*Data (layer) CRS:*
1. In the **Contents** pane, right-click the layer and click **Properties**, or double-click the layer name (Esri, *Set layer properties*).
2. Open the **Source** tab. "You can also view the layer's extent, spatial reference, domain, resolution, and tolerance information from this tab" (same page). Expand **Spatial Reference** and read the name, WKID/authority, unit, and datum. Record them on a coordinate card.

*Map CRS:*
3. In the **Contents** pane, right-click the map and click **Properties**; click the **Coordinate Systems** tab. "The Current Map Coordinate Systems heading shows the current horizontal and vertical coordinate systems of the map or scene, respectively. There may be no vertical coordinate system defined. Click the name of the coordinate system … (in blue text) to see how they are defined" (Esri, *Work with coordinate systems*).
4. Under **Available Coordinate Systems**, expand the **Layers** folder: "Expand a coordinate system heading to see the layers that reference it" (same page). This is the fastest way to see every distinct data CRS in the map at once.
5. Right-click any coordinate system and click **Details** to read its parameters and its area of use (same page).

*Pointer coordinates:* the coordinate display at the bottom of the map view shows "the real-world coordinate values corresponding with the pointer's location", and its **display units** can be changed from the arrow beside the coordinates or from **Map Properties › General › Display Units** (Esri, *Map units, display units, and location units*). Changing display units changes only what is printed; **map units** "are read-only, and you can only change them by changing the coordinate system of the map" (same page).

**Procedure (version-specific) — QGIS 3.40; not execution-tested.**

*Data (layer) CRS:*
1. Right-click the layer › **Properties** › **Source** tab › read **Assigned Coordinate Reference System (CRS)**.
2. Note the warning printed in the manual: "changing the CRS in this setting does not alter the underlying data source in any way, rather it just changes how QGIS interprets the raw coordinates from the layer in the current QGIS project" (QGIS 3.40, *Working with Projections*). This box is a *label*, not a conversion — the same distinction Chapter 6 makes for ArcGIS *Define Projection*.

*Project (map) CRS:*
3. **Project › Properties › CRS**, or click the CRS button at the right end of the status bar, which displays the "current project CRS" and, when clicked, "opens the Project Properties dialog" (QGIS 3.40, *QGIS GUI*).
4. In the CRS selector, use the **Filter** box (by code or name), and read the preview map of the "approximate area of use" and the PROJ text (QGIS 3.40, *Working with Projections*).

*Pointer coordinates:* the status-bar coordinate box shows "the current position of the mouse, following it while moving across the map view" (QGIS 3.40, *QGIS GUI*); the units follow the project settings.

**Verification item.** Before teaching these steps live, confirm in the installed release: the exact tab and heading labels; that the **Layers** folder appears in the *Coordinate Systems* list; the wording of the QGIS *Source* tab; and what a layer with no CRS shows in each product (ArcGIS Pro: unknown; QGIS: the "unknown CRS" icon and coordinates "treated as purely numerical, non-earth values" per the manual).

### 5.7.2 The coordinate-metadata checklist

Use this checklist on **every** new dataset before anything else is done with it. It is the practical form of the coordinate card.

| # | Check | How | Pass looks like | Fail or "unknown" means |
| --- | --- | --- | --- | --- |
| 1 | **CRS known?** | Layer/dataset properties; embedded metadata (`.prj`, database SRID, GeoPackage `gpkg_spatial_ref_sys`); the supplier's documentation | A named CRS with an authority code | Stop. Do not assign one. Investigate provenance (5.7.3) |
| 2 | **Units** | Read the CRS record's unit of measure | degree, metre, foot… stated | You cannot judge magnitudes or distances |
| 3 | **Coordinate order** | Read the *container's* contract (Table 5.5.1) and, for tables, the column names | Order documented | Risk of a silent swap (5.5.3) |
| 4 | **Extent plausibility** | Compare the data's bounding extent (Chapter 3.6) with where the data *should* be, in the CRS's units | Inside the expected box **and** inside the CRS's area of use | Swap, wrong CRS, wrong zone, or wrong units |
| 5 | **Datum** | Read the CRS record's base GCS / datum | Named datum matches the supplier's claim | Possible offsets of metres to hundreds of metres (5.3.1) |
| 6 | **Vertical reference** (if Z present) | Vertical CRS in the record, or the schema's own documentation | Unit, surface, and direction stated | Z is undefined (5.6) |
| 7 | **Source metadata** | Who captured it, when, with what, and in which CRS they *say* | Provenance note exists and agrees with 1–6 | Any disagreement is a defect to log, not a detail to smooth over |

Checks 1–3 are read from definitions; check 4 is arithmetic; checks 5–7 are reading and comparing documents. None of them requires a transformation.

### 5.7.3 Unexpected magnitude or location is a clue, not a licence to guess

Sooner or later you will open a dataset that draws in the wrong place, or one whose CRS is simply missing. The wrong reaction is to try coordinate systems until the points land somewhere sensible and then save that choice. Why it is wrong:

1. **Several CRSs can look "sensible".** The neighbouring UTM zone, a Web Mercator misread, or a local grid can all put points near a city. Landing somewhere plausible is weak evidence.
2. **A guessed CRS becomes metadata.** Once saved, the guess is indistinguishable from a verified fact for every later user. Chapter 6 will show that assigning a CRS (ArcGIS *Define Projection*, or the QGIS *Assigned CRS* box) changes only the label, never the numbers — so a wrong label permanently misdescribes correct numbers.
3. **The evidence that resolves it is usually not in the numbers.** It is in the capture device settings, the export script, the supplier's readme, or a colleague's memory.

So the working rule is: **unknown or implausible reference → investigate provenance → document the finding → only then assign, if the correct reference has been established.** The lab's Case D exists to make you practise stopping.

**Clue table (magnitudes are approximate; use them to *ask*, not to *decide*).**

| You observe | Candidate explanations | Next question |
| --- | --- | --- |
| Values in −90…90 / −180…180 | Geographic degrees; **or** a swap | Which column is which? Does the extent match the expected area? |
| Eastings roughly 166 000–834 000 m, northings 0–10 000 000 m | UTM (some zone, some hemisphere); other transverse-Mercator grids | Which zone? Which hemisphere? Does the supplier say? (The easting band is derived from a 6°-wide zone: ±3° × ≈111 km at the equator around the 500 000 m false easting.) |
| Values up to about ±20 037 508 m in both axes | Web Mercator (EPSG:3857) — the limit is half the circumference of the 6 378 137 m sphere, π × 6 378 137 ≈ 20 037 508 m | Was this exported from a web map? |
| Values 0–a few thousand with no metadata | Local/engineering grid, training grid, or scaled units | Is there a documented origin and unit? |
| Layer appears at (0, 0) in the ocean off West Africa | Nulls or zeros written as coordinates (note that *XY Table To Point* treats 0 as a valid coordinate — Esri, *XY Table To Point*) | Are there missing values in the source? |
| Layer is the right shape but offset by tens of metres | Datum mismatch (5.3.1) | Which datum does each dataset actually use? |

**Analogy — a stack trace.** A stack trace tells you where to look; it does not tell you the fix. Magnitude and location anomalies are the stack trace of a CRS problem. **Where the analogy stops:** a stack trace is generated by the failing code, so it is trustworthy evidence. A "sensible-looking" map after a guess is generated by *your guess*, so it is not.

**Comprehension check 5.7.** A layer with no CRS metadata draws far from the basemap. A colleague sets its CRS to EPSG:32643 "because then it lands on the city" and saves. List what is now true, what is now unknown, and what should have happened instead.

---

## 5.8 Guided lab: coordinate detective

### 5.8.1 Objective, prerequisites, and what this lab is (and is not)

**Objective.** Given four small, documented coordinate cases, classify each as *valid geographic*, *valid projected*, *swapped order*, or *unresolvable from the numbers alone*; cite the evidence for each classification; and produce a diagnosis table that another developer could check.

**Prerequisites.** Modules 5.1–5.7. You need the DMS/DD arithmetic of 5.2, the CRS records of 5.4, the contract table of 5.5, and the checklist of 5.7.2.

**What this lab is not.** No transformation, reprojection, or CRS assignment is performed. You *diagnose*; you do not *repair*. Repair is Chapter 6, and only after diagnosis.

**Required software/access.** Part A needs paper or a text editor and a calculator. Part B (optional) needs ArcGIS Pro **or** QGIS Desktop; it is not execution-tested by the author and is marked accordingly.

### 5.8.2 Input data — four case files (synthetic; reproduce exactly as printed)

Each case is a small file plus the *public* provenance line that came with it. Create the files by typing the content shown; the instructor holds a separate private provenance note for Case D and must not release it until review.

**Case A — `caseA_requests.csv`.** Public provenance: "Exported from the field app; the app's settings screen says 'WGS 84'."

```
id,lat,lon,category
A1,23.0250,72.6000,Streetlight out
A2,23.0375,72.5875,Pothole
A3,23.0125,72.6125,Water leak
```

**Case B — `caseB_survey.csv`.** Public provenance: "Total-station export from a licensed surveyor; header sheet says 'WGS 84 / UTM zone 43N (EPSG:32643), metres'."

```
pt,easting,northing
B1,254318.4,2547906.2
B2,254512.9,2548110.7
B3,254101.0,2547650.5
```

**Case C — `caseC_export.geojson`.** Public provenance: "Generated from Case A by a script last week; the developer says 'I copied the columns in order.'"

```json
{ "type": "FeatureCollection", "features": [
  { "type": "Feature", "properties": { "id": "A1" },
    "geometry": { "type": "Point", "coordinates": [23.0250, 72.6000] } },
  { "type": "Feature", "properties": { "id": "A2" },
    "geometry": { "type": "Point", "coordinates": [23.0375, 72.5875] } },
  { "type": "Feature", "properties": { "id": "A3" },
    "geometry": { "type": "Point", "coordinates": [23.0125, 72.6125] } }
] }
```

**Case D — `caseD_assets.csv`.** Public provenance: "Found on a shared drive in a folder named `Assets_export`; the folder also contains `Assets_export.dbf` and `Assets_export.shx` but **no `.prj` file**. Nobody remembers who made it."

```
asset_id,x,y,type
SL-0113,205.0,195.0,Streetlight
DR-0042,995.0,510.0,Drain
TR-0301,2190.0,520.0,Tree
```

**Expected area for all four cases**, as stated by the municipality's GIS lead: "Everything we hold should be inside our service area, which for Earth-referenced data means roughly latitude 22.9–23.1°N, longitude 72.5–72.7°E." (Synthetic; used only for plausibility checks.)

### 5.8.3 Ordered steps — Part A (paper diagnosis; deterministic)

1. **Build a blank diagnosis table** with one row per case and these columns: *Coordinate order (as written in the container)*, *CRS (name and code, or "unknown")*, *Units*, *Plausible extent (yes / no / cannot judge)*, *Classification*, *Evidence*, *Confidence and remaining questions*.
2. **Case A.** Apply checklist items 1–4. Identify which column is which from the header. Check every value against the expected area. Convert one point to DMS and back (5.2.2) to demonstrate the arithmetic. Decide whether "the app says WGS 84" plus the column names is enough to name a CRS code, and say why.
3. **Case B.** Read the EPSG:32643 record fields from 5.4.3. Check each easting against the UTM easting band and each northing for hemisphere consistency. Do the two rough plausibility estimates from 5.4.3 (how far west of 75°E; roughly what latitude) for one point and state what they do and do not prove.
4. **Case C.** Apply the RFC 7946 rule from 5.5.2 to the first position. State the latitude and longitude the file *actually* asserts. Compare with the expected area and with Case A's columns. Classify.
5. **Case D.** Apply checklist item 1. Try each of these hypotheses and record why each is or is not excluded *by the numbers alone*: (a) EPSG:4326 degrees; (b) a UTM zone in metres; (c) EPSG:3857; (d) a local planar grid in metres; (e) a local planar grid in feet. Then answer: can the file's CRS be determined from the numbers? Write down exactly what evidence would resolve it and who you would ask.
6. **Fill the confidence column** for every case using three levels: *established* (evidence from a definition or contract), *plausible* (consistent with magnitudes and expected area but not proven), *unknown* (cannot be resolved without provenance). Each row must name at least one *remaining question* even when the classification is confident.
7. **Write the one-paragraph summary** answering: which case cannot be resolved from numbers alone, and what the difference is between "consistent with" and "established".

### 5.8.4 Ordered steps — Part B (optional software inspection; not execution-tested)

Part B loads **Case A only** (the case whose CRS you have documented) and inspects data CRS versus map CRS. It does not transform anything.

**ArcGIS Pro route (documentation labelled 3.7).**

1. Open a project with a new map that already has a web basemap. Before adding data, open **Map Properties › Coordinate Systems** and record the map CRS under *Current Map Coordinate Systems* (expected, from the documentation: WGS 1984 Web Mercator (auxiliary sphere), WKID 3857, because the basemap was the first layer).
2. Add `caseA_requests.csv` to the project and run the **XY Table To Point** tool (available at all licence levels — Esri, *XY Table To Point*). Set **X Field** = `lon`, **Y Field** = `lat`, leave **Z Field** empty, and set **Coordinate System** to the geographic WGS 1984 system (WKID 4326) — the tool's documented default. Note that the tool labels X as "longitude" and Y as "latitude" in its own parameter descriptions; you are honouring that contract, and the column names are your evidence.
3. Open the output layer's **Properties › Source › Spatial Reference** and record the data CRS on a coordinate card.
4. Return to **Map Properties › Coordinate Systems**, expand the **Layers** folder, and confirm that the map lists the new layer under a different coordinate system heading from the basemap.
5. Hover the pointer over one point and read the coordinate display at the bottom of the view; then change the display units from the arrow beside the coordinates and read again. Record both readings and explain, in one line, why neither reading changed the stored data.

**QGIS route (3.40).**

1. Start a new project; note the project CRS shown on the status-bar button (it depends on the *CRS for projects* option and on whether a basemap layer was added first).
2. **Layer › Add Layer › Add Delimited Text Layer**; under *Geometry Definition* choose **Point coordinates**, **X field** = `lon`, **Y field** = `lat`, and set **Geometry CRS** to EPSG:4326 (QGIS 3.40, *Opening Data*).
3. Right-click the layer › **Properties › Source** and read *Assigned Coordinate Reference System (CRS)*.
4. Open **Project › Properties › CRS** and compare with step 3.
5. Read the status-bar coordinate box while hovering over one point; record it and the project CRS together.

**Deliberately excluded.** Do **not** load Case C or Case D in Part B. Case C would draw in the wrong place and tempt you to "fix" it by editing coordinates; Case D would prompt a CRS-assignment dialog, which is precisely the guess this chapter forbids. Both are repaired, with evidence, in Chapter 6.

### 5.8.5 Expected results and validation checks (learner-facing)

The instructor's full answer table is in Instructor Appendix I.3. Before submitting, confirm that your work passes these checks:

- Your table has exactly four classifications, and exactly one of them is "unresolvable from the numbers alone".
- Case A and Case C contain the same six numbers; your table explains why the two files nonetheless describe different places, citing the specific RFC 7946 sentence (5.5.2) as evidence.
- Every projected case names a **zone and hemisphere**, not just "UTM".
- Every "established" confidence is backed by a definition or a contract; every "plausible" is backed by a magnitude or extent check; nothing is marked established because "it looks right on a map".
- Your DMS conversion reverses correctly (5.2.2 method).
- For Case D, each of the five hypotheses has a one-line reason; at least one hypothesis is excluded by range, and at least two remain possible.
- Part B (if done): the recorded map CRS and data CRS are different, and the data CRS on the *Source* tab is what you set in the tool — not what the map shows.

### 5.8.6 Troubleshooting

| Symptom | Likely cause | What to do |
| --- | --- | --- |
| "Case B northing ÷ 111 km gives 22.95°, not 23.0° — is it wrong?" | The rule of thumb is approximate; the scale factor and meridian arc length are ignored | Treat as *plausible*; a 0.1° difference is inside the method's error. Do not "correct" the data |
| "Case A: the app said WGS 84, so I wrote EPSG:4326 as established" | Datum name ≠ CRS (5.3.2) | The values are in degrees within range and the columns are labelled, so EPSG:4326 is a strong inference; mark it *plausible/established by contract only if* the app's documentation names the code. Record the gap |
| "Case C passes my validator" | Range check only (5.5.3) | Add an expected-extent check; the file fails it |
| "Case D looks like the training grid, so it has no CRS" | Guessing from resemblance | It *may* be the training grid; the numbers cannot prove it. Mark unknown; name the evidence that would decide |
| Part B: *XY Table To Point* output appears near (0, 0) | X and Y fields swapped, or nulls | Check field mapping; remember the tool treats 0 as valid |
| Part B: layer appears but the *Layers* folder shows only one CRS | The layer was created in the map CRS, or the tool's coordinate system was left at a different value | Re-check the tool's Coordinate System parameter and the *Source* tab |
| Part B (QGIS): a CRS prompt appears when adding Case A | *Prompt for CRS* option active and Geometry CRS not set | Set Geometry CRS in the delimited-text dialog; do not accept a default you did not choose |

### 5.8.7 Required learner deliverables

1. The **diagnosis table** (four rows, seven columns) as a Markdown or spreadsheet file.
2. The **hand arithmetic** for one DMS/DD conversion and for the two Case B plausibility estimates, showing each step.
3. The **Case D hypothesis list** with one-line reasons and the evidence request.
4. The **summary paragraph** (5.8.3 step 7).
5. **Part B only:** two coordinate cards (map CRS and data CRS), the two pointer readings with their display units, and the one-line explanation.

Scoring: reasoning 30 %, correctness 30 %, verification 25 %, documentation 15 %. A table with correct labels and no evidence column scores at most 45 %.

---

## 5.9 Independent check and progression gate

Answer without opening the Instructor Appendix. Questions state their assumptions; where a question says "per RFC 7946" or "per the EPSG record", use that contract and no other.

### 5.9.1 Concept questions (six)

**Q1.** [5.1; LO1] A message reads: "Point is at 254318.4, 2547906.2." List the *minimum* additional items that would make this a complete coordinate description, and name the one that the *file format* rather than the CRS supplies.

**Q2.** [5.2; LO2] Convert `8° 31′ 12″ S, 115° 13′ 30″ E` to signed decimal degrees. Show the arithmetic, state the sign rule you applied to each axis, and verify by converting back.

**Q3.** [5.3; LO3] Which one of the following is the best statement, and why do the others fail?
(a) "WGS 84" identifies a unique coordinate reference system.
(b) A datum fixes where the ellipsoid sits relative to the Earth; a CRS built on it still needs axes, units, and (if projected) a projection.
(c) Changing the datum of a dataset changes the physical positions of its features.
(d) An ellipsoid and a datum are two names for the same thing.

**Q4.** [5.4; LO4] Under the EPSG registry records read in this chapter, which one of these statements is correct?
(a) EPSG:3857 has degree units.
(b) EPSG:32643's area of use is the whole of India.
(c) EPSG:3857's unit is the metre, but its own record states scale errors relative to a proper Mercator, so the unit does not establish measurement suitability.
(d) EPSG:4326's axes are easting, northing.

**Q5.** [5.5; LO5] A file is supposed to hold points in Sri Lanka (roughly 6–10°N, 79–82°E). It contains `{"type":"Point","coordinates":[6.93, 79.86]}`. Per RFC 7946, what place does this position describe (give latitude and longitude with hemispheres)? Would a validator that checks only that latitude is within ±90 and longitude within ±180 accept it? Name one additional check that would expose the problem, and explain why the same problem in a file of points from Bali (roughly 8–9°S, 114–116°E) *would* be caught by the range check alone.

**Q6.** [5.6, 5.7; LO6, LO7] A layer's *Source* tab in ArcGIS Pro says its spatial reference is WGS 1984 (WKID 4326); the map's *Coordinate Systems* tab says WGS 1984 Web Mercator (auxiliary sphere). The layer also has a `z` attribute with values around 55. State (i) whether the layer's stored coordinates are degrees or metres, (ii) why the map can still draw it aligned with a Web Mercator basemap, and (iii) two questions that must be answered before `z = 55` can be compared with another dataset's elevations.

### 5.9.2 Scenario questions (two)

**S1.** [5.4, 5.5, 5.7; LO4, LO5, LO7] A mobile inspection app posts JSON like `{"lat": 23.0312, "lng": 72.5934, "alt": 61.2}` to your API. Your backend must store the point in PostGIS with `ST_Point(…, 4326)`, and a reporting job must emit RFC 7946 GeoJSON. Write the argument order for the `ST_Point` call and the GeoJSON position array, cite the contract you used for each, and state one check you would add so that a future developer cannot swap the values without a test failing. Then explain why you should *not* write `alt` into the GeoJSON third element until one specific fact about it is known.

**S2.** [5.3, 5.6; LO3, LO6] A contractor delivers a drain survey as a CSV with `easting, northing` in "UTM 43N, WGS 84" and a `height` column. Your existing drain layer, which the previous contractor delivered "in WGS 84", is offset from the new one by about 3 m horizontally, and the heights differ by a roughly constant amount for the same manholes. Give two *different* categories of explanation for the horizontal offset and two for the vertical difference, and for each category name the evidence (a record field, a document, or a check from 5.7.2) that would confirm or rule it out. Do not propose a transformation.

### 5.9.3 Independent practical task (unfamiliar inputs)

Three new synthetic files arrive with a note "all from our Delhi pilot" and the expected area "roughly 28.4–28.9°N, 76.8–77.4°E". No answer from the guided lab applies.

**X1 — `pilot_points.geojson`**
```json
{ "type": "Feature", "properties": { "id": "X1" },
  "geometry": { "type": "Point", "coordinates": [28.6, 77.2] } }
```

**X2 — `pilot_survey.csv`** (header sheet says "WGS 84 / UTM zone 43N, metres; height from the phone")
```
id,northing,easting,height
X2,3168500.0,715200.0,216
```

**X3 — `pilot_legacy.csv`** (no metadata; found with no companion files)
```
id,x,y
X3,1234.5,987.6
```

Deliver: (1) a diagnosis table in the 5.8 format for X1–X3; (2) for X1, the latitude/longitude the file asserts per RFC 7946 and whether it is inside the expected area; (3) for X2, the two rough plausibility estimates of 5.4.3 with arithmetic, an explicit statement about the *column order* and why it does not contradict the EPSG axis definition, and the questions that `height = 216` leaves open; (4) for X3, the hypotheses you can exclude by range and the ones you cannot, and the evidence you would request; (5) a statement of what you did **not** do (no transformation, no CRS assignment) and why.

### 5.9.4 Oral explanation (one)

In no more than three minutes, explain to a colleague who has just said "the file is in WGS 84, so just load it" why that sentence is not enough. Your explanation must mention: the difference between a datum and a CRS; at least two CRSs on WGS 84 with different units; the container's axis-order contract; and what you would do if the CRS turned out to be unknown.

### 5.9.5 Submission requirements and scoring

**Submit:** written answers to Q1–Q6 and S1–S2; the practical deliverables (1)–(5) with arithmetic shown; the oral explanation delivered live or as a recording.

**Scoring (100 points).**

| Component | Points | Criteria |
| --- | --- | --- |
| Concept Q1–Q6 | 30 (5 each) | Correct answer *and* correct reason; MCQ items score 0 without the reason |
| Scenario S1–S2 | 20 (10 each) | Correct contracts cited; evidence named for each explanation; no unrequested transformation proposed |
| Practical | 40 | Reasoning 12, correctness 12, verification 10, documentation 6. Verification requires the arithmetic and the extent check to be shown, not asserted |
| Oral | 10 | All four required elements present; no term used without a plain-language meaning |

**Progression gate.** Advance to Chapter 6 only when: coordinate order, angular versus linear units, and dataset versus display reference are each handled correctly at least once in the practical; X1 is identified as swapped with the RFC cited; and X3 is *not* assigned a CRS. Any of the following is a **critical misconception** requiring remediation and a fresh exercise regardless of the total score (blueprint Appendix B.3): treating degrees as metres; silently guessing coordinate order or CRS; claiming a Web Mercator metre is a ground metre; treating "WGS 84" as a complete CRS specification.

---

## 5.10 Media and source brief

This learner-facing summary states what the slide deck, audio lesson, and visuals for this chapter must do; the full specifications are in the Media Appendix (M.1–M.5).

1. **Visuals.** Four core visuals: a **labelled globe** (equator, prime meridian, one parallel, one meridian, the two angles for point G1); a **graticule** (the grid of parallels and meridians drawn on the globe and then on a flat map, to show that the same lines become straight or curved depending on the projection); the **flat coordinate grid** (the training grid with origin, axes, units); and a **coordinate metadata card** (the five-line card of 5.1.3 filled in for G1, U1, and the swapped Case C). A 3D globe is used only for axes and angles. It must *not* be used to suggest that flattening the globe can be done without distortion — the Media Appendix specifies the labelling that prevents this reading.
2. **Narration.** Every time the audio reads a coordinate pair, it states the order aloud in words: "longitude seventy-two point six, then latitude twenty-three point zero two five" for GeoJSON; "latitude … then longitude …" for prose. The phrase "first coordinate" is never used without naming what it means in the current format.
3. **Readings.** Primary: [S13] (QGIS CRS introduction), [S14] (Esri coordinate systems overview), [S18] (RFC 7946, §3.1.1, §4, §11.2). Supporting: the EPSG registry records for 4326, 3857, 32643, 4979, 3855; Esri *Work with coordinate systems*; QGIS 3.40 *Working with Projections*.
4. **Transition.** Chapter 6 takes the references you can now read and *uses* them: it separates assigning a CRS from transforming coordinates, chooses transformations with evidence, and measures distances and areas with a documented method. Every Chapter 6 lab begins with the checklist of 5.7.2.

---

## Glossary

| Term | Meaning in this course |
| --- | --- |
| Area of use (extent) | The region for which a CRS definition is valid, recorded in its registry entry. |
| Axis | One direction of measurement in a CRS (latitude, longitude, easting, northing, x, y, height). |
| Axis order | The sequence in which a *container* (file, API, function) writes coordinate values. It is a contract of the container, distinct from the CRS's formal axis list. |
| Coordinate | An ordered set of numbers giving a position in a reference system. |
| Coordinate card | This course's five-line description: values, CRS, units, container order, precision. |
| Coordinate reference system (CRS) | The definition that gives coordinate numbers meaning: origin, axes, units, and Earth model. Esri documentation also says "coordinate system" or "spatial reference". |
| CRS84 | The OGC identifier for longitude–latitude on WGS 84; the CRS of RFC 7946 GeoJSON. |
| Data CRS (layer CRS) | The CRS the stored coordinates of a dataset are actually in. |
| Datum (geodetic datum, reference frame) | The definition of where an ellipsoid sits relative to the Earth and how it is oriented; the origin and orientation of latitude/longitude. |
| Decimal degrees (DD) | Angle written as one signed decimal number, e.g. 23.0250. |
| Degrees–minutes–seconds (DMS) | Angle written as degrees, minutes (1/60°), and seconds (1/3600°), with a hemisphere letter or sign. |
| Easting, northing | The x and y coordinates of most projected CRSs, in linear units, measured east and north from an origin (often with false offsets added). |
| Ellipsoid (spheroid) | A smooth mathematical model of the Earth's shape, defined by a semimajor axis, a semiminor axis, and a flattening. |
| Ellipsoidal height | Height measured from the ellipsoid; what GNSS produces natively and what GeoJSON's third element means. |
| Ensemble (datum ensemble) | A group of closely related realisations of a datum treated as one for low-accuracy purposes (e.g. "World Geodetic System 1984 ensemble"). |
| EPSG code | A numeric key in the EPSG Geodetic Parameter Dataset identifying a CRS or related object; ArcGIS calls it a WKID. |
| Equator | The circle midway between the poles; the line of zero latitude. |
| Geographic CRS | A CRS that stores angles (latitude, longitude) on an ellipsoid; units are degrees. |
| Geoid | A surface following gravity that approximates mean sea level; the reference for gravity-related heights. |
| Graticule | The network of parallels and meridians. |
| Gravity-related height | Height measured from the geoid; "height above sea level". |
| Hemisphere | Half of the Earth: north/south of the equator; east/west of the prime meridian. |
| Latitude | Angle north (+) or south (−) of the equator; −90 to +90 in the signed convention. |
| Longitude | Angle east (+) or west (−) of the prime meridian; −180 to +180 in the signed convention. |
| Map CRS (project/display CRS) | The CRS in which a map view draws all layers; layers in other CRSs are converted for display only. |
| Map projection | The mathematical rule that converts angles on the ellipsoid to planar coordinates. |
| Meridian | A line of equal longitude, running pole to pole. |
| On-the-fly projection | Converting layers to the map CRS for display only, without changing stored data. |
| Parallel | A line of equal latitude. |
| Prime meridian | The line of zero longitude; for most CRSs, through Greenwich. |
| Projected CRS | A CRS that stores planar coordinates in linear units; it contains a geographic CRS plus a projection and its parameters. |
| Provenance | Documentation of who created data, when, with what, and in which CRS. |
| Unknown CRS | The state of a dataset or map with no coordinate system defined; software then treats values as unitless numbers. |
| UTM | Universal Transverse Mercator: a family of 60 six-degree zones, each with a northern and a southern variant, using easting/northing in metres. |
| Vertical CRS / vertical datum | The definition of the surface, unit, and direction from which heights or depths are measured. |
| Web Mercator | EPSG:3857, "WGS 84 / Pseudo-Mercator": a sphere-based projected CRS in metres used by web maps; distances are distorted. |
| WGS 84 | World Geodetic System 1984: a datum (ensemble) and ellipsoid; the base of EPSG:4326, 3857, 4979, and the UTM "WGS 84 /" zones. |
| WKID | Well-known ID: Esri's term for the numeric CRS code (usually the EPSG code). |

## Recap

- Two numbers are not a place. A coordinate needs a **CRS**, **units**, the **container's axis order**, and a stated **precision** (5.1).
- **Latitude** is the angle from the equator, positive north; **longitude** is the angle from the prime meridian, positive east. DD and DMS convert exactly: DD = d + m/60 + s/3600. A degree of latitude is roughly 111 km; a degree of longitude shrinks toward the poles (5.2).
- The Earth's shape is modelled by an **ellipsoid**; a **datum** anchors it. The same point has different numbers in different datums. "WGS 84" names a datum, not a complete CRS (5.3).
- A **geographic CRS** stores degrees; a **projected CRS** stores linear units and *contains* a geographic CRS plus a projection. Read the whole record — type, unit, axes, datum, area of use — for EPSG:4326, EPSG:3857, and a UTM zone. A metre unit does not prove measurement suitability (5.4).
- **Coordinate order follows the interface contract.** RFC 7946 GeoJSON is longitude, latitude on WGS 84 (CRS84); the formal EPSG:4326 axis order is latitude, longitude; Leaflet takes latitude first; Esri JSON, the JS SDK, PostGIS, and the *XY Table To Point* tool use x = longitude. A swapped pair inside ±90 passes range checks (5.5).
- **Height** needs a unit, a reference surface (ellipsoid or geoid), a direction, and a definition of what is being measured. A stored Z is not automatically used (5.6).
- **Data CRS and map CRS can differ**; alignment on screen proves nothing about stored coordinates. Use the seven-item checklist. Unexpected magnitudes are clues; never assign a CRS by guesswork (5.7).

## Cross-references to later chapters

- **Chapter 6** — assigning versus transforming (Define Projection versus Project), datum transformations and their evidence, planar versus geodesic measurement, and why Web Mercator metres are not ground metres. Every Chapter 6 lab starts from the 5.7.2 checklist.
- **Chapter 7** — formats and metadata: where CRS metadata lives in shapefiles (`.prj`), GeoPackage, GeoJSON (fixed), and databases (SRID); what is lost when it is missing.
- **Chapter 8** — data modelling: the schema fields (unit, vertical reference, meaning of "height") that record E5-H was missing.
- **Chapter 9** — data capture and quality: how CRS defects such as swapped pairs and missing `.prj` files are logged and repaired with an audit trail.
- **Chapter 11** — analysis: choosing the processing CRS for buffers and overlays.

## References

All pages were opened and read on **19 September 2026**. Blueprint IDs are kept where the source is in the blueprint's register; other sources are listed by publisher.

| ID | Publisher — page title | URL | Used for |
| --- | --- | --- | --- |
| S13 | QGIS Project — *Coordinate Reference Systems* (A Gentle Introduction to GIS, 3.44 documentation) | https://docs.qgis.org/3.44/en/docs/gentle_gis_introduction/coordinate_reference_systems.html | Projections and distortion; latitude/longitude, DMS, hemispheres; XY-plane; UTM zones, false easting/northing; on-the-fly projection |
| S14 | Esri — *Coordinate systems, map projections, and transformations* (ArcGIS Pro documentation, "latest") | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/properties/coordinate-systems-and-projections.html | GCS/PCS/VCS definitions; sign conventions; gravity-based vs ellipsoidal vertical systems; on-the-fly reprojection |
| S18 | RFC Editor — *RFC 7946: The GeoJSON Format* | https://www.rfc-editor.org/rfc/rfc7946.html | §3.1.1 position order and altitude; §4 CRS (WGS 84, CRS84, removal of `crs`); §11.2 precision |
| E1 | Esri — *Work with coordinate systems* (ArcGIS Pro documentation, page labelled ArcGIS Pro 3.7) | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/properties/specify-a-coordinate-system.html | Map Properties › Coordinate Systems; Current Map Coordinate Systems; Layers folder; Details and area of use; default map CRS; real-time projection caution |
| E2 | Esri — *Use an unknown coordinate system* (ArcGIS Pro documentation) | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/properties/use-an-unknown-coordinate-system.html | Behaviour of unknown coordinate systems (unitless values, no editing, planar unitless measure) |
| E3 | Esri — *Properties of a spatial reference* (ArcGIS Pro documentation) | https://doc.esri.com/en/arcgis-pro/latest/help/data/geodatabases/overview/the-properties-of-a-spatial-reference.html | Definitions of spatial reference, GCS, PCS, VCS, unknown coordinate system |
| E4 | Esri — *Vertical coordinate systems* (ArcGIS Pro documentation) | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/properties/vertical-coordinate-systems.html | Vertical CRS: origin, linear unit, direction; ellipsoidal vs gravity-based |
| E5 | Esri — *Set layer properties* (ArcGIS Pro documentation) | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/layer-properties/set-layer-properties.html | Opening Layer Properties; Source tab shows spatial reference |
| E6 | Esri — *Map units, display units, and location units* (ArcGIS Pro documentation) | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/navigation/map-units-location-units-and-display-units.html | Coordinate display at the bottom of the view; changing display units; map units read-only |
| E7 | Esri — *XY Table To Point* (ArcGIS Pro tool reference) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/xy-table-to-point.html | Parameters (X Field = longitude, Y Field = latitude, Coordinate System default), Z handling, 0 treated as valid, −400…400 range, licence levels |
| E8 | Esri — *Mercator* (ArcGIS Pro documentation, projections) | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/properties/mercator.html | Web Mercator uses a sphere; de facto web standard; distortion; ~85° latitude limit |
| E9 | Esri — *What are geographic coordinate systems?* (ArcMap documentation) | https://desktop.arcgis.com/en/arcmap/latest/map/projections/about-geographic-coordinate-systems.htm | Equator, prime meridian, parallels, meridians, ranges |
| E10 | Esri — *Datums* (ArcMap documentation) | https://desktop.arcgis.com/en/arcmap/latest/map/projections/datums.htm | Datum definition; geocentric vs local; Redlands control-point values in three datums |
| E11 | Esri — *Spheroids and spheres* (ArcMap documentation) | https://desktop.arcgis.com/en/arcmap/latest/map/projections/spheroids-and-spheres.htm | Sphere vs spheroid; semimajor/semiminor axes; flattening; WGS 1984 parameters |
| E12 | Esri — *Geometry objects* (ArcGIS REST APIs reference) | https://developers.arcgis.com/rest/services-reference/enterprise/geometry-objects/ | Esri JSON point `{x, y, z}`; wkid/latestWkid; 102100 → 3857 |
| E13 | Esri — *Using spatial references* (ArcGIS REST APIs reference) | https://developers.arcgis.com/rest/services-reference/enterprise/using-spatial-references/ | WKID and WKT definition |
| E14 | Esri — *Spatial references* (Esri Developer documentation) | https://developers.arcgis.com/documentation/spatial-references/ | 4326 as the most common storage reference; 3857 default in web libraries and its distortion |
| E15 | Esri — *Point* class (ArcGIS Maps SDK for JavaScript 5.1 API reference) | https://developers.arcgis.com/javascript/latest/references/core/geometry/Point/ | x/y (easting/northing), latitude/longitude, z, hasZ, default spatial reference |
| Q1 | QGIS Project — *Working with Projections* (QGIS Desktop 3.40 User Guide) | https://docs.qgis.org/3.40/en/docs/user_manual/working_with_projections/working_with_projections.html | Layer CRS (Source tab, "does not alter the underlying data source"); project CRS; No CRS option; unknown-CRS layers; CRS selector contents |
| Q2 | QGIS Project — *Options* (QGIS Desktop 3.40 User Guide, CRS and Transforms section) | https://docs.qgis.org/3.40/en/docs/user_manual/introduction/qgis_configuration.html | CRS for projects ("Use CRS from first layer added" / "Use a default CRS"); CRS for layers options |
| Q3 | QGIS Project — *QGIS GUI* (QGIS Desktop 3.40 User Guide, Status Bar) | https://docs.qgis.org/3.40/en/docs/user_manual/introduction/qgis_gui.html | Status-bar coordinate box; project CRS button |
| Q4 | QGIS Project — *Opening Data* (QGIS Desktop 3.40 User Guide, delimited text) | https://docs.qgis.org/3.40/en/docs/user_manual/managing_data_source/opening_data.html | Add Delimited Text Layer; Point coordinates; X/Y field; Geometry CRS |
| P1 | IOGP — EPSG Geodetic Parameter Dataset v13.103, *WGS 84* (code 4326) | https://epsg.org/crs_4326/WGS-84.html | Name, type, datum ensemble, axes latitude/longitude, degree, extent |
| P2 | IOGP — EPSG Dataset, *WGS 84 / Pseudo-Mercator* (code 3857) | https://epsg.org/crs_3857/WGS-84-Pseudo-Mercator.html | Name, aliases, axes, metre, extent 85°S–85°N, scope, remarks on scale error |
| P3 | IOGP — EPSG Dataset, *WGS 84 / UTM zone 43N* (code 32643) | https://epsg.org/crs_32643/WGS-84-UTM-zone-43N.html | Name, axes, metre, extent 72°E–78°E N hemisphere, scope |
| P4 | IOGP — EPSG Dataset, *UTM zone 43N* conversion (code 16043) | https://epsg.org/conversion_16043/UTM-zone-43N.html | Transverse Mercator parameters: origin 0/75, scale 0.9996, false easting 500000 m, false northing 0 |
| P5 | IOGP — EPSG Dataset, *WGS 84* Geographic 3D (code 4979) | https://epsg.org/crs_4979/WGS-84.html | Axes latitude, longitude, ellipsoidal height; units |
| P6 | IOGP — EPSG Dataset, *EGM2008 height* (code 3855) | https://epsg.org/crs_3855/EGM2008-height.html | Vertical CRS; EGM2008 geoid datum; height up, metre; "approximating mean sea level" |
| G1 | PostGIS Project — *ST_Point* (PostGIS reference) | https://postgis.net/docs/ST_Point.html | "For geodetic coordinates, X is longitude and Y is latitude"; example with SRID 4326 |
| L1 | Leaflet — *API reference* (Leaflet 1.9.4), LatLng section | https://leafletjs.com/reference.html | `L.latLng(lat, lng)` latitude-first contract |

**Sources not used.** The blueprint's S02 (pro.arcgis.com) and other product URLs were not needed for this chapter. No non-official summaries were used for any factual claim.

---

# Instructor Appendix (separate from learner material)

Do not distribute this appendix to learners before the progression gate.

## I.1 Answers and reasoning — comprehension checks and in-module exercises

**Check 5.1.** Remaining questions: Which column is x and which is y — and does x mean longitude here (container contract)? Which CRS on WGS 84 — geographic degrees (EPSG:4326) or a projected system on WGS 84 (Web Mercator, a UTM zone)? What are the units (degrees or metres)? What precision is meaningful? Is there a Z, and if so what reference? "WGS 84" answers only the datum; it answers none of the others (5.3.2).

**Check 5.2.** 18° 57′ 36″ N → 18 + 57/60 + 36/3600 = 18 + 0.95 + 0.01 = **18.9600** (N → +). 72° 49′ 48″ E → 72 + 49/60 + 48/3600 = 72 + 0.816667 + 0.013333 = **72.8300** (E → +). Reverse: 0.96 × 60 = 57.6 → 57′, 0.6 × 60 = 36″ ✔; 0.83 × 60 = 49.8 → 49′, 0.8 × 60 = 48″ ✔. Four decimals kept ≈ 11 m (5.2.2 table). (Values are synthetic round numbers in western India.)

**Check 5.3.** Both files can be on the WGS 84 datum: the first is angles on the ellipsoid (a geographic CRS, EPSG:4326 if the axes/units are as usual); the second is planar metres produced by *some* projection of those angles (a projected CRS whose base is WGS 84 — e.g. a UTM zone or Web Mercator). Layer 3 (datum) is shared; the second file additionally needs the projection, parameters, unit, and area of use; both files still need axis order, an authority code, and provenance.

**Check 5.4.** 4326: geographic, degree, latitude/longitude (formal), world. 3857: projected, metre, easting/northing, 85°S–85°N, scope web mapping. 32643: projected, metre, easting/northing, N hemisphere 72°E–78°E. A web basemap: 3857. Not for a dataset in southern India (roughly 8–13°N, 76–80°E): 32643, because the *area of use / extent* field stops at 78°E and the dataset extends to 80°E — it crosses into zone 44N (78–84°E), so no single zone covers it and the choice needs Chapter 6's method selection. (4326 is usable for storage anywhere but is not a measurement CRS.)

**Check 5.5.** `L.latLng(72.6, 23.025)`: Leaflet's contract is latitude first → latitude 72.6°N, longitude 23.025°E → sea north of Norway. GeoJSON `[72.6, 23.025]`: RFC 7946 puts longitude first → longitude 72.6°E, latitude 23.025°N → western India (point G1). Same numbers, opposite places, because the contracts differ.

**Check 5.6.** Questions: unit (m/ft)? direction (positive down for an invert *depth*, or positive up for an invert *elevation*)? reference surface (ground at the manhole rim? geoid/sea level? ellipsoid?) — i.e., is it relative or absolute? measured at which point of the drain (invert = inside bottom of the pipe; which end)? A vertical CRS code (e.g. EPSG:3855) can answer the surface, unit, and direction *only if* the value is an absolute elevation; whether "invert" is depth below rim or absolute elevation, and which end, can only come from the schema documentation.

**Check 5.7.** Now true: the file carries a CRS label EPSG:32643 and draws near the city. Now unknown: whether the stored numbers were ever UTM 43N (the numbers did not change — only the label); which of the plausible alternatives (zone 42N/44N, a local grid, a Web Mercator misread with different magnitudes) was actually the source; whether any later user can tell the label was a guess. Should have happened: record the anomaly; run the 5.7.2 checklist; investigate provenance (device, export script, supplier); if a CRS cannot be established, keep it unknown and log it; only assign once the reference is established with evidence (Chapter 6).

## I.2 Answer key — 5.9 concept and scenario questions

**Q1.** Minimum: which number is which axis (easting/northing or x/y) *in this container*; the CRS name and authority code (which implies units, datum, and — if projected — the projection, zone, hemisphere); the precision. The item supplied by the file format rather than the CRS is the **axis order of the container** (5.1.3 line 4). Accept "units" as CRS-supplied; reject answers that list only "the EPSG code" without the container order.

**Q2.** Latitude: 8 + 31/60 + 12/3600 = 8 + 0.516667 + 0.003333 = 8.52; S → **−8.5200**. Longitude: 115 + 13/60 + 30/3600 = 115 + 0.216667 + 0.008333 = 115.225; E → **+115.2250**. Reverse: 0.52 × 60 = 31.2 → 31′, 0.2 × 60 = 12″ ✔; 0.225 × 60 = 13.5 → 13′, 0.5 × 60 = 30″ ✔. (Synthetic round numbers near Bali.)

**Q3.** **(b).** (a) fails: WGS 84 is a datum (ensemble) shared by 4326, 3857, 4979, and the UTM zones (5.3.2). (c) fails: features do not move; their coordinate *values* change (5.3.1 Redlands example). (d) fails: the ellipsoid is the shape; the datum positions and orients it (5.3.1 layers 2 and 3).

**Q4.** **(c).** (a) fails: EPSG:3857 UoM is m (P2). (b) fails: 32643's extent is 72°E–78°E N hemisphere (P3); India spans zones 42–47. (d) fails: EPSG:4326 axes are latitude, longitude (P1).

**Q5.** Per RFC 7946 §3.1.1, index 0 is longitude: the file asserts **longitude 6.93°E, latitude 79.86°N** — in the Arctic Ocean near Svalbard, not Sri Lanka. A range-only validator **accepts** it (79.86 ≤ 90 and 6.93 ≤ 180). An **expected-extent check** (latitude 6–10, longitude 79–82) exposes it; comparing the position with the source columns (`lat` value at index 0) also works. For Bali, a swapped position would read `[−8.52, 115.225]` → latitude 115.225, which violates ±90, so the range check catches it: swaps slip through only when **both** absolute values are ≤ 90 (5.5.3). Full marks require the RFC to be cited as the contract and the "both ≤ 90" condition to be stated.

**Q6.** (i) Degrees — the layer's own spatial reference is WGS 1984 (4326), a geographic CRS (P1, E3). (ii) On-the-fly projection: the map converts each layer to the map CRS for display (E1; S13); this does not change stored coordinates. (iii) Any two of: unit of z; reference surface (ellipsoidal vs gravity-related; which vertical CRS); direction (up/down); what is measured (ground elevation vs height above ground vs top of object); whether the other dataset uses the same answers.

**S1.** `ST_Point(72.5934, 23.0312, 4326)` — x = longitude, y = latitude per the PostGIS ST_Point documentation (G1). GeoJSON position `[72.5934, 23.0312]` — longitude first per RFC 7946 §3.1.1 (S18). Test: a unit test with a fixture point whose latitude and longitude have clearly different magnitudes and an **expected-extent assertion** (e.g. all stored points must have y within 22–24 and x within 72–74 for this deployment), or a property-based test that round-trips `lat`/`lng` through the API and asserts `ST_Y = lat`, `ST_X = lng`. Do not write `alt` into the third element until it is known whether the phone reports **ellipsoidal height** — the RFC defines the third element as height above or below the WGS 84 ellipsoid (S18 §4); a gravity-related ("sea level") altitude would be silently mislabelled. Accept also "unit unknown" as the blocking fact if the learner argues it.

**S2.** Horizontal offset (~3 m), two categories: (1) **datum/reference-frame difference** — "WGS 84" is an ensemble; different realisations or a supplier who actually used a different datum can produce metre-level offsets (5.3.1, 5.3.3); evidence: the datum / base-CRS field of each file's record, the supplier's control-point report, checklist item 5. (2) **Capture accuracy or a different reference point** — consumer GNSS versus total station, or one contractor measured the rim and the other the cover centre; evidence: provenance/capture-method documents, checklist item 7. (Also acceptable: a different projection/zone label applied to the same numbers — evidence: checklist items 1 and 4.) Vertical difference (roughly constant), two categories: (1) **different vertical reference** — ellipsoidal height versus gravity-related height gives a nearly constant offset over a small area (5.6.1); evidence: vertical CRS field or the supplier's statement of the reference (e.g. EGM2008 height vs WGS 84 ellipsoidal height), checklist item 6. (2) **different quantity or unit** — invert depth vs rim elevation, or feet vs metres (a ratio, not a constant offset — learners should note a *constant* offset argues against a unit error); evidence: schema documentation, checklist item 6. Any proposed transformation is out of scope and should be marked down.

**Practical task (5.9.3) — expected results.** *X1:* GeoJSON `[28.6, 77.2]` → per RFC 7946 longitude 28.6°E, latitude 77.2°N (Barents Sea, east of Svalbard); outside the expected area (28.4–28.9°N, 76.8–77.4°E); both values in range, so the swap passes a range check → **swapped order**, established by the RFC contract; the intended point is almost certainly latitude 28.6°N, longitude 77.2°E. *X2:* columns are `northing, easting` — northing first; this is a container (CSV header) order and does not contradict EPSG:32643's axis definition (easting, northing), which describes the CRS, not the file (5.5.1). Easting 715 200 → 215 200 m east of 75°E; a degree of longitude at 28.6°N ≈ 111.32 × cos 28.6° ≈ 97.7 km → ≈ 2.2° → ≈ 77.2°E ✔; northing 3 168 500 ÷ 111 000 ≈ 28.5°N ✔; both inside the zone's 72–78°E N extent and the expected area → **valid projected (plausible)**. `height = 216`: unit unknown; reference unknown — "from the phone" suggests an ellipsoidal GNSS height but the phone may report a geoid-corrected value; direction and meaning (ground elevation vs something else) unstated → undefined until documented. *X3:* 1234.5 > 180 excludes degrees; magnitudes exclude a normal UTM easting/northing and are implausible for Web Mercator near Delhi; local grid in metres or feet both possible → **unresolvable from the numbers alone**; request the creating application, source project, or a readme from whoever placed the file. *(5):* the learner must state that no transformation and no CRS assignment were performed, because diagnosis precedes repair and X3's CRS is not established.

## I.3 Expected results — 5.8 lab

**Private provenance note for Case D (release only at review).** "`Assets_export` was written in 2026 from the Chapter 1 training-grid project (Table F4 assets SL-0113, DR-0042, TR-0301). The coordinates are metres on the flat training grid with origin (0, 0) at the south-west corner of Ward A; there is **no Earth reference and no EPSG code**, so no `.prj` was ever created. The file is not usable as Earth-referenced data under any CRS."

**Answer table (Part A).**

| Case | Container order | CRS | Units | Plausible extent | Classification | Key evidence | Confidence / remaining questions |
| --- | --- | --- | --- | --- | --- | --- | --- |
| A | Columns `lat`, `lon` (latitude first, by header name) | WGS 84 geographic — EPSG:4326 inferred | degree | Yes: all latitudes 23.0125–23.0375, longitudes 72.5875–72.6125, inside 22.9–23.1 / 72.5–72.7 | **Valid geographic** | Header names; values in range; extent matches; app says "WGS 84" | *Plausible → established for order and units by contract (header); CRS code inferred* — remaining: does the app documentation name EPSG:4326 (vs 4979 with a hidden altitude)? precision claimed? |
| B | Columns `easting`, `northing` (x first) | WGS 84 / UTM zone 43N — EPSG:32643 (as stated) | metre | Yes: eastings 254 101–254 513 within 166 000–834 000; northings 2 547 650–2 548 111 positive; ≈ 72.6°E and ≈ 23°N by rough estimate; inside zone extent 72–78°E N | **Valid projected** | Header sheet names the CRS with code; magnitudes and rough back-estimates consistent; area of use consistent | *Plausible* (consistent with claim); not independently proven — remaining: was the surveyor's control on the same datum? which realisation of WGS 84? |
| C | GeoJSON position: index 0 = longitude per RFC 7946 | Fixed by the format: WGS 84 degrees (CRS84) | degree | **No**: asserts latitude 72.6°N, longitude 23.025°E (sea north of Norway) | **Swapped order** | RFC 7946 §3.1.1; values equal Case A's `lat, lon` in that order; expected-extent check fails while range check passes | *Established* — remaining: were any consumers already fed this file? |
| D | Columns `x`, `y` (no contract about meaning) | **Unknown** | Unknown | Cannot judge without a CRS | **Unresolvable from the numbers alone** | No `.prj`; no metadata; values 195–2190 exclude degrees (2190 > 180 and > 90) but fit many local grids in metres or feet; resemblance to Table F4 is suggestive, not proof | *Unknown* — evidence needed: the creator/export script, the source project, a readme; who: the shared-drive owner, the GIS lead |

**Hand arithmetic expected.** DMS for A1 as in 5.2.2 (or any point, reversed). Case B: 500 000 − 254 318.4 = 245 681.6 m ≈ 245.7 km; 245.7 ÷ 102.5 ≈ 2.40° west of 75°E → ≈ 72.6°E; 2 547 906.2 ÷ 111 000 ≈ 22.95° → "roughly 23°N". Accept 22.9–23.1 as the rough range; the point of the exercise is the reasoning and the stated limits (scale factor and meridian arc ignored; not a conversion).

**Case D hypotheses.** (a) EPSG:4326 degrees — **excluded** (2190 > 180; also 195/510/520 > 90 for y). (b) UTM metres — **excluded as a normal UTM coordinate** (eastings far below 166 000; northings 195–520 m would be within half a kilometre of the equator, contradicting any claim about the service area) but a learner may note the numbers cannot *prove* what grid they belong to. (c) EPSG:3857 — magnitudes are possible in principle (near 0, 0 = Gulf of Guinea) but contradict the expected area; **implausible**, not impossible from numbers alone. (d) Local planar grid in metres — **possible**. (e) Local planar grid in feet — **possible** (the numbers alone cannot distinguish metres from feet). Conclusion: not determinable from numbers; matches the private note once released.

**Part B expected (not execution-tested).** Map CRS recorded before adding data: expected WGS 1984 Web Mercator (auxiliary sphere), WKID 3857 (E1 documents that new maps default to Web Mercator and derive from the first layer — the basemap). Data CRS on the *Source* tab: WGS 1984, WKID 4326 (the tool's Coordinate System parameter, E7). *Layers* folder shows two headings. Pointer readings differ only in format/units and neither alters stored data (E6). **Instructor must run this once and replace "expected" with observed values, labels, and screenshots before releasing Part B as a graded item.**

## I.4 Common mistakes and remediation

| Mistake | Where it shows | Remediation |
| --- | --- | --- |
| Writing "WGS 84" in the CRS column as if complete | Cases A, B; Q3 | Re-teach 5.3.2 with the four-row table; require an authority code or "unknown" |
| Marking Case C as "valid geographic" because both values are in range | Lab; Q5 | Re-run 5.5.3 with the learner reading the RFC sentence aloud; require an expected-extent check in their own words |
| Assigning EPSG:32643 to Case D "because it looks like the survey" | Lab; Check 5.7 | Show that the numbers are three orders of magnitude off UTM; re-teach 5.7.3; the learner must write the evidence request |
| Treating "degrees ≈ 111 km" as exact | Case B arithmetic | Point to the stated approximation; require the limits to be written next to every estimate |
| Confusing display units with map CRS with data CRS | Part B; Q6 | Three coordinate cards side by side (data, map, display); E6's "map units are read-only" |
| Height treated as comparable across datasets by default | Check 5.6; S2 | Rebuild the 5.6.1 four-question table for the learner's own example |
| Proposing a transformation in S2 | S2 | Remind that diagnosis precedes repair; the transformation chapter needs the evidence first |

## I.5 Fresh exercise for retesting (different values, same concepts)

- **Conversion:** `19° 04′ 30″ N, 72° 52′ 48″ E` → 19.0750, 72.8800 (0.075 × 60 = 4.5 → 4′ 30″; 0.88 × 60 = 52.8 → 52′ 48″).
- **Swapped GeoJSON that passes range checks:** `[19.075, 72.88]` written as `[19.075, 72.88]` where the source columns were `lat, lon` → asserts longitude 19.075°E, latitude 72.88°N (Arctic Ocean north of Scandinavia); both values in range.
- **UTM plausibility:** easting 487 500, northing 2 110 000 in EPSG:32643 → 12 500 m west of 75°E ≈ 0.12° → ≈ 74.9°E; 2 110 000 ÷ 111 000 ≈ 19.0°N; inside 72–78°E N. Ask which UTM zone 72.88°E belongs to (zone 43) and whether 74.9°E is in the same zone (yes).
- **Unknown-CRS file:** `x, y = 3120.4, 1875.9` with no metadata → excluded: degrees; unresolvable between local metres/feet; request provenance.
- **Height:** `depth = 1.8` for a drain → unit, direction (positive down implied by the word but not guaranteed), reference (rim? ground? geoid?), which point.

## I.6 Building the Part B demonstration — not execution-tested

1. Create the three CSV files exactly as printed in 5.8.2 (UTF-8, comma-separated, header row).
2. In ArcGIS Pro (documentation label 3.7 on the check date), record the installed version, the default map CRS observed in a new map with the organisation's default basemap, the exact labels on the *Coordinate Systems* tab, and the *Source* tab wording. Run *XY Table To Point* with the parameters in 5.8.4 and capture the *Source* tab, the *Layers* folder, and two pointer readings.
3. In QGIS 3.40, record the *CRS for projects* option in effect, the status-bar CRS before and after adding Case A, the *Assigned CRS* text, and the delimited-text dialog labels.
4. Replace every "expected" in I.3 with the observed value. Keep screenshots as evidence; note that a screenshot alone does not prove the stored CRS — pair it with the *Source*/*Assigned CRS* text.
5. Decide whether the later planar-fixture package should use a documented local/engineering CRS instead of "unknown": E2 documents that ArcGIS Pro does not permit editing in a map with an unknown coordinate system, which affects Chapter 9 labs. Record the decision in the fixture readme (blueprint Appendix A.2 allows either approach).

## I.7 Blueprint corrections, source notes, and verification items

**Blueprint corrections.** None required for Chapter 5. All numbered points were implemented as written. Two source notes are recorded so that instructors do not over-read the references:

1. [S13] states that the distance between lines of latitude "is the same (60 nautical miles)" and gives 30.87624 m per second at the equator. Both are teaching simplifications on a sphere; on the WGS 84 ellipsoid a degree of latitude varies by roughly one percent between the equator and the poles. The chapter uses "about 111 km" and labels every derived distance as approximate.
2. [S14] (the Esri Pro overview page) does not itself carry a version label; the *Work with coordinate systems* page (E1) is labelled "ArcGIS Pro 3.7" and was used for all interface wording. E9–E11 are ArcMap documentation, used only for durable definitions (equator, datum, spheroid parameters), not for interface steps.

**Verification items before live delivery.**

- Exact ArcGIS Pro list labels for WKID 4326, 3857, and 32643 (5.4.2 table).
- QGIS CRS display pattern `EPSG:code - name` and the *Source* tab heading text (5.7.1).
- Whether the installed ArcGIS Pro release still defaults a new map to Web Mercator when the organisation's default basemap differs (E1 describes behaviour at 3.7).
- The −400…400 geographic range note for *XY Table To Point* (E7) — confirm in the installed tool help, since it affects the 5.5.3 claim that the tool does not catch swaps.
- If the RFC 7946 text is re-fetched, confirm §11.2's "about 10 centimeters" wording (used in the 5.2.2 table).

## I.8 Instructor change log

| Date | Change | Affected items |
| --- | --- | --- |
| 2026-09-19 | Initial release, revision 1.0. Sources read on this date; software procedures not execution-tested. | All |

---

# Media Appendix

## M.1 Slide outline (mapped to module IDs)

| Slides | Module | Title | Content | Question before the answer |
| --- | --- | --- | --- | --- |
| 1–2 | Front matter | Coordinates and CRS | Outcomes LO1–LO8; the two fixtures kept apart | — |
| 3–5 | 5.1 | Two numbers are not a place | The three-pair table; Diagram D1 (training grid); the coordinate card | "Where is 23.025, 72.6?" (reveal both answers) |
| 6–9 | 5.2 | Latitude and longitude | Diagram D2 (globe); DD ↔ DMS worked conversion; decimal-places table; sign table | "Convert 72° 36′ 00″ E before I do" |
| 10–13 | 5.3 | Shape, ellipsoid, datum | Layer table; WGS 84 ellipsoid values; Redlands three-datum example; the "WGS 84 is not a CRS" table | "Did the point move?" |
| 14–18 | 5.4 | Geographic vs projected | Diagram D3 (nested boxes); EPSG record fields; 4326/3857/32643 side by side; UTM zone map (schematic) | "What is the unit of 3857 — and what does that prove?" |
| 19–23 | 5.5 | Coordinate order | Table 5.5.1; RFC rules; G1 as GeoJSON; the swapped pair; the check table | "Does `[23.025, 72.6]` pass a range check?" |
| 24–27 | 5.6 | Vertical reference | Diagram D4 (three surfaces); E5-H four-question table; relative vs absolute; "stored Z ≠ used Z" | "What does `height = 30` mean?" |
| 28–31 | 5.7 | Data CRS vs map CRS | Diagram D5; procedure summaries (Pro, QGIS); checklist; clue table | "The layers line up — what do we know?" |
| 32–34 | 5.8 | Coordinate detective | Four case cards; diagnosis-table template; validation checks | "Which case cannot be solved?" |
| 35 | 5.9 | Gate | Critical misconceptions; what Chapter 6 needs from you | — |

Speaker notes for each slide should name the source page for any quoted definition (see References) and must state, for every coordinate spoken, its order and CRS.

## M.2 Audio-lesson outline

1. **Opening (5.1).** Read "twenty-three point zero two five, seventy-two point six" and pause. Say why it is not yet a place. Describe D1 verbally: "a flat grid; the origin at the bottom-left corner of Ward A; x grows to the right, y grows upward; each step is one metre; this grid is not on the Earth."
2. **Globe (5.2).** Describe D2: "the equator is the circle around the middle; the prime meridian runs top to bottom through Greenwich; latitude is the angle up from the equator; longitude is the angle around from the prime meridian." Do the DMS conversion aloud, digit by digit, and let the listener predict the result before the reverse check. Say "north is positive, east is positive — check each separately."
3. **Earth model (5.3).** Three layers in order: shape, ellipsoid, datum. Read the Redlands numbers slowly and ask the listener to say which digits change. State "roughly eighty metres" and that this is an approximation.
4. **Two families (5.4).** Describe D3 as nested boxes from the inside out: ellipsoid, datum, geographic CRS, projection, projected CRS. Read the three EPSG records as short cards: name, type, unit, axes, area of use. Say explicitly: "the unit of Web Mercator is the metre; the registry says its scale is wrong by nought point seven percent even against a proper Mercator."
5. **Order (5.5).** For every array read aloud, say "longitude first, then latitude — that is the GeoJSON rule" or "latitude first — that is the Leaflet rule." Never say "the first number" alone. Walk through the swapped pair and where it lands.
6. **Height (5.6).** Describe D4: "three surfaces stacked: the ground on top, then the geoid, which follows gravity, then the ellipsoid, which is smooth; the two heights of the streetlight base are measured to different surfaces, so they are different numbers."
7. **Data vs map CRS (5.7).** Describe D5 without colour references: "three layers; each has its own card; the map draws all of them in Web Mercator; none of the stored numbers changed."
8. **Lab briefing (5.8).** Read the four provenance lines; tell the listener to stop at Case D and explain why stopping is the right answer.
9. **Close.** Recap bullets; transition to Chapter 6.

Pronunciation and units: "E-P-S-G", "U-T-M", "W-G-S eighty-four", "Geo-JSON"; say "degrees", "metres", and "seconds of arc" explicitly; say "approximately" before every rule-of-thumb distance.

## M.3 Diagram specifications

All diagrams are original schematics. Labels use text, not colour alone. Each carries the caption "Schematic; synthetic training data" where applicable.

**D1 — The training grid as a Cartesian system.** Canvas showing x from −100 to 2300 and y from −100 to 1100 (metres). Origin marked "(0, 0) — origin"; arrows labelled "x → (metres)" and "y ↑ (metres)"; Wards A and B outlined; Road R1 as a segment with visible ends; P1–P6 as labelled points. A callout: "No Earth location. No EPSG code. Units by declaration." Frame 2 overlays a second callout: "Right is *not* east. Up is *not* north. Those words belong to a CRS."

**D2 — Labelled globe with graticule.** A sphere with the equator (labelled "equator — 0° latitude"), the prime meridian ("prime meridian — 0° longitude, through Greenwich"), two additional parallels and two meridians labelled with their values, the poles, and point G1 with two angle arcs from the centre: "latitude 23.025° N (angle from the equator)" and "longitude 72.6° E (angle from the prime meridian)". Hemisphere labels N/S/E/W on the appropriate halves. Second panel: the same graticule drawn on a flat rectangle (a plate carrée-style sketch) with the note "on a flat sheet the same lines become a grid; the flattening distorts — see Chapter 6."

**D3 — Nested CRS definition.** Five nested boxes, outermost first: "Projected CRS: WGS 84 / UTM zone 43N (EPSG:32643) — unit metre — axes easting, northing"; "Map projection: Transverse Mercator — central meridian 75°E, scale 0.9996, false easting 500 000 m, false northing 0"; "Geographic CRS: WGS 84 (EPSG:4326) — unit degree — axes latitude, longitude"; "Datum: World Geodetic System 1984 ensemble"; "Ellipsoid: WGS 84 — a = 6 378 137 m, 1/f = 298.257223563". Side panel: the same point G1 written three times — as 4326 degrees; as "easting/northing in metres (values not shown — conversion is Chapter 6)"; as "Web Mercator x/y in metres (values not shown)".

**D4 — Three vertical surfaces.** A cross-section: irregular ground line on top; a gently undulating "geoid (≈ mean sea level)" line; a smooth "ellipsoid" line. One vertical arrow from the ellipsoid to the streetlight base labelled "ellipsoidal height h (EPSG:4979 third axis; GeoJSON third element)"; one from the geoid labelled "gravity-related height H (e.g. EPSG:3855)"; a short arrow from the ground to the top of the pole labelled "height above ground (pole height)". Callout: "h and H are different numbers for the same point; the difference depends on location." Label: "Vertical separations exaggerated for clarity; not to scale."

**D5 — One map, three CRSs.** A map frame labelled "Map CRS: WGS 1984 Web Mercator (auxiliary sphere), EPSG:3857" containing three layer swatches, each with a card: "Basemap — data CRS 3857"; "Requests (Case A) — data CRS 4326, degrees"; "Survey (Case B) — data CRS 32643, metres". An arrow from each card to the frame labelled "converted for display only". Footer: "Alignment on screen ≠ identical stored coordinates."

**D6 — The swapped pair (two-panel).** Left: a schematic world outline with G1 correctly placed in western India, labelled "`[72.6, 23.025]` → lon 72.6°E, lat 23.025°N". Right: the same outline with the point in the sea north of Norway, labelled "`[23.025, 72.6]` → lon 23.025°E, lat 72.6°N — passes the range check". Caption: "Same numbers, different contract reading."

## M.4 Coordinate metadata card for slides (Table M1)

| Field | G1 (Case A) | U1 (Case B) | Case C first position |
| --- | --- | --- | --- |
| Values | latitude 23.0250, longitude 72.6000 | easting 254 318.4, northing 2 547 906.2 | `[23.0250, 72.6000]` |
| CRS | WGS 84, EPSG:4326 | WGS 84 / UTM zone 43N, EPSG:32643 | fixed by GeoJSON: WGS 84 degrees (CRS84) |
| Units | degree | metre | degree |
| Container order | CSV columns `lat, lon` | CSV columns `easting, northing` | RFC 7946: longitude, latitude → **asserts lat 72.6°N, lon 23.025°E** |
| Precision | 4 dp ≈ 11 m | 0.1 m (as written) | 4 dp |
| Plausible? | Yes | Yes (rough checks) | **No** |

## M.5 Interactive and 3D material

**Proposed interactive: "Globe to grid" (3D globe, browser-based; optional).**

- **Learning objective.** Show that latitude and longitude are angles from the Earth's centre, that the same point is described by different numbers in different CRSs, and that axis order is a property of the container, not the point.
- **Objects.** A sphere with graticule lines every 10°; the equator and prime meridian emphasised and labelled; a draggable point marker; two angle arcs from the centre that update with the marker; a side panel showing three cards for the marker — EPSG:4326 (latitude, longitude, degrees), GeoJSON position array (longitude, latitude), and Leaflet-style `(lat, lng)`. **The projected cards (UTM, Web Mercator) are omitted** because computing them requires projection formulas that belong to Chapter 6 and have not been verified in this document.
- **Labels and units.** Every value shows its unit ("°"); the array card shows "index 0 = longitude"; the hemisphere letters update with the sign; a footer states "Sphere shown for angles only; the real reference surface is an ellipsoid (a ≠ b)."
- **Controls.** Drag the marker; toggle "show angle arcs"; toggle "swap array order" which visibly moves the marker to the swapped location on the globe and turns the extent check red; a "reset to G1" button.
- **Expected behaviour.** With the marker at G1, the cards read latitude 23.025, longitude 72.6; `[72.6, 23.025]`; `(23.025, 72.6)`. Pressing "swap array order" moves the marker to latitude 72.6°N, longitude 23.025°E (sea north of Norway) and the extent check "inside 22–24°N, 72–73°E" changes from pass to fail while the range check stays green.
- **Validation cases.** (1) G1 as above. (2) Marker at the equator/prime-meridian intersection shows 0, 0 in every card and lies in the Gulf of Guinea. (3) Marker at −12.5, −45.25 shows "S" and "W" and negative signs. (4) Marker at −8.52, 115.225 and "swap" → the range check turns red because 115.225 > 90 (contrast with G1's swap, which stays green).
- **Labelling of simplification.** The globe is a sphere; text states "schematic sphere, not the WGS 84 ellipsoid". No vertical exaggeration is used because there is no elevation in the scene.
- **2D/text alternative.** Diagrams D2 and D6 plus Table M1 convey every point above; the interactive is not required for any assessment item.

**No other 3D content is proposed.** The vertical-reference concept (D4) is taught better by a labelled cross-section than by a 3D scene, and any rendering of the geoid–ellipsoid separation would need a geoid model that this chapter does not verify.
