# GIS and ArcGIS Fundamentals — Phase 1

# Chapter 7 — GIS data formats, sources, and metadata

## Document metadata

| Item | Value |
| --- | --- |
| Chapter ID | Phase 1, Chapter 7 |
| Title | GIS data formats, sources, and metadata |
| Source specification | `GIS_Phase_1_Chapter_Authoring_Blueprint.md`, revision 1.0 (18 September 2026) |
| Document revision | 1.0 — 19 September 2026 |
| Reference-check date | 19 September 2026 (every cited page was opened and read on this date; see the reference list for exact URLs) |
| Audience | Software developers at LiveBird Technologies who have completed Chapters 1–6 |
| Software referenced | ArcGIS Pro (documentation pages carry the version tag 3.7 on the check date); ArcGIS Online (continuously updated; documentation read on the check date); ArcGIS Enterprise ("latest" documentation; a link inside it resolved to the 12.1 edition); QGIS Desktop 3.44 user manual (the 3.40 manual now shows an "end of life" banner, so 3.44 pages are cited); GDAL/OGR "stable" driver documentation (the library QGIS uses to read and write files); OGC GeoPackage Encoding Standard 1.4.0; OGC GeoTIFF Standard 1.1; IETF RFC 7946 |
| Execution status | **No procedure in this chapter has been executed in ArcGIS Pro, ArcGIS Online, or QGIS by the author.** Interface steps are written from the official pages cited beside them and are marked *not execution-tested*. Every expected conversion result in the lab is stated as a *prediction derived from the cited format rules*; the lab requires learners and instructors to record what their software actually produced. |
| Data status | Every coordinate, record, name, note, timestamp, and date in this chapter is **synthetic training material**. The Gujarati words are ordinary dictionary words used only to test text handling. Nothing describes a real municipality, asset, or authority. |

### How to use this document

Read the modules in order. Boxes labelled **Format rule** quote what a published specification requires; boxes labelled **Platform note** describe what one product does with that rule; boxes labelled **Procedure (version-specific)** contain interface steps that will age; boxes labelled **Verification item** tell an instructor what must be checked in the installed environment before a statement is treated as fact. Comprehension checks have no answers in the learner text; answers are in the Instructor Appendix, which learners should not open before the progression gate in 7.9.

---

## Prerequisites

- **Chapter 3.** You can name point, line, and polygon geometry; explain vertices, rings, holes, and multipart features; and tell a dataset apart from a layer that presents it.
- **Chapter 4.** You can read a raster as rows, columns, and cell values; explain cell size, extent, bands, and NoData; and you know that a display colour is not a stored value.
- **Chapter 5.** You can interpret a coordinate only together with its coordinate reference system (CRS), units, and the coordinate order of the format or API in use; you know that GeoJSON writes longitude before latitude; and you refuse to guess an unknown CRS from the numbers alone.
- **Chapter 6.** You can distinguish *assigning* a CRS (changing metadata) from *transforming* coordinates (changing stored values), and you know why an aligned display does not prove aligned data.
- Ordinary developer experience: files and folders, character encodings (ASCII, UTF-8), JSON, CSV, SQLite or another relational database, primary keys, and null values. These are used as analogies and are not taught here.
- **No ArcGIS licence or account is required** to complete the reading and the assessment. The guided lab (7.8) has an ArcGIS Pro route and a QGIS route; it can also be completed as a paper exercise if neither is available (see 7.8.8).

## Learning outcomes

By the end of this chapter you should be able to:

| ID | Outcome | Where it is taught | Where it is checked |
| --- | --- | --- | --- |
| LO1 | Separate a **data model** (vector, raster), an **encoding** (how it is written down), a **container** (a file or database that holds several datasets), and a **service** (data reached over a network), and explain why a filename extension proves none of accuracy, completeness, freshness, or suitability. | 7.1 | 7.9 concept Q1; oral |
| LO2 | Read a coordinate CSV and a small GeoJSON document by eye, state their coordinate order and CRS, and identify the fields that will break (leading zeros, dates, encoding). | 7.2 | 7.9 concept Q2, Q3; practical |
| LO3 | List the companion files of a shapefile, name the role of `.prj`, and predict which attribute properties a shapefile export will lose. | 7.3 | 7.9 concept Q1, Q4; scenario S1 |
| LO4 | Describe GeoPackage as an SQLite-based standard container with optional extensions, describe GeoTIFF as a TIFF file carrying georeferencing tags, and list what must be inspected in each before use. | 7.4 | 7.9 concept Q1, Q5; practical |
| LO5 | Explain what an ArcGIS geodatabase adds beyond a geometry table; distinguish a feature class, a nonspatial table, and a feature dataset; and state why a PostGIS database is not automatically an enterprise geodatabase and why ArcGIS Data Store is a different thing again. | 7.5 | 7.9 concept Q6 |
| LO6 | Complete a dataset intake form, classify a dataset as measured, digitized, geocoded, derived, or simulated, and detect a source-suitability mismatch such as an old boundary applied to current requests. | 7.6 | 7.9 scenario S2; practical |
| LO7 | Design and run a before/after conversion check that separates intentional adaptation from accidental loss, and explain why a tool's "completed" message is not a semantic validation. | 7.7, 7.8 | 7.8 lab; 7.9 practical |

## Required materials

- This document and a text editor that can show file encoding (for example VS Code, Notepad++, or any editor that displays "UTF-8" in its status bar).
- The **Chapter 7 fixture** printed in this document (Table F7 and Example E7-1). No download is needed; the lab tells you how to type the CSV.
- **For the lab (7.8), one of:** ArcGIS Pro (any licence level; the lab uses only core tools) **or** QGIS Desktop 3.44. A spreadsheet program is useful but not required. If neither GIS product is available, the lab has a paper route (7.8.8).
- **Not required:** an ArcGIS Online account, an ArcGIS Enterprise deployment, a database server, or any publishing action. Nothing in this chapter consumes credits.

## Recap of the preceding chapters

Chapters 3–6 built the *content* of geographic data. Chapter 3 showed that a feature is an entity with geometry and attributes, that geometry comes as points, lines, and polygons (with rings, holes, and multipart shapes), and that a *layer* is one presentation of a stored *dataset*. Chapter 4 added the raster model: a grid of cells whose values only mean something with a unit or a category dictionary, whose cell size is not accuracy, and whose NoData must be handled deliberately. Chapter 5 established that a coordinate pair is meaningless without its CRS, units, and the coordinate order of the format at hand — GeoJSON writes longitude first — and that an unknown CRS must trigger investigation, never a guess. Chapter 6 separated *defining* a CRS (metadata only) from *projecting* (new coordinate values), showed that a map can display two layers aligned without changing what is stored, and closed by asking you to keep your 6.8 transformation-and-measurement log as provenance. In that lab you already handled two exchange files without naming them as such: `wards_e6.geojson` (a standard GeoJSON file in WGS 84) and `requests_e6.csv` (a coordinate CSV whose CRS, EPSG:32643, was supplied only in its readme). This chapter explains what those files could and could not carry.

This chapter is about the *packaging* of that content. The same six assets can be written as a CSV, a GeoJSON file, a shapefile, a GeoPackage table, a file-geodatabase feature class, or a row set behind a web service. Each package keeps some of the content and drops or bends the rest. Your job as a developer is to know what each package can hold, to read a package's own evidence about where its data came from, and to check — not assume — what survived a conversion.

## The recurring scenario and the Chapter 7 fixture

The fictional municipality continues: **assets** (streetlights, drains, trees, benches), **road** centrelines, **wards**, public **service requests**, and **inspections**. The Chapter 1 fixture is unchanged: Wards A and B (1 km² squares sharing the edge x = 1000), Road R1 from (0, 500) to (2000, 500), and requests P1–P6 (Chapter 1, Tables F1–F3). All of it sits on the flat, metre-based **training grid** with **no Earth location and no EPSG code** (blueprint Appendix A.2).

**New for Chapter 7 — Table F7, the exchange variant of the assets table (synthetic).** Chapter 1's three assets are kept at their coordinates and three more are added, with the fields the blueprint requires for the lab: a long field name, a null numeric value, a leading-zero code, a timestamp, and Gujarati/English text. The Chapter 1 `Condition` text field is replaced here by a numeric `condition_score`; both variants are valid in their own chapter.

**Table F7 — Assets, Chapter 7 exchange variant (synthetic). Coordinates are training-grid metres, (x, y).**

| asset_id | legacy_code | asset_type_en | asset_type_gu | installed_year | condition_score | last_inspection_at | inspector_note | x | y |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| SL-0113 | 0113 | Streetlight | સ્ટ્રીટલાઇટ | 2018 | 3.5 | 2026-03-14T10:42:00+05:30 | Lamp flickers at dusk | 205 | 195 |
| DR-0042 | 0042 | Drain | ગટર | 2011 | 1.5 | 2025-11-02T15:05:00+05:30 | Silt build-up — ગટર ભરાયેલી છે | 995 | 510 |
| TR-0301 | 0301 | Tree | વૃક્ષ | 2005 | *(null)* | *(null)* | *(empty)* | 2190 | 520 |
| BN-0007 | 0007 | Bench | બાંકડો | 2022 | 5.0 | 2026-06-01T09:00:00+05:30 | Repainted | 1500 | 300 |
| DR-0110 | 0110 | Drain | ગટર | 2015 | 2.0 | 2026-01-20T11:30:00+05:30 | Grate missing | 400 | 700 |
| SL-0055 | 0055 | Streetlight | સ્ટ્રીટલાઇટ | 2020 | 4.0 | 2026-08-30T02:10:00+05:30 | Night check: lamp OK | 1800 | 950 |

**Schema dictionary for Table F7 (part of the fixture; a real dataset would ship this as its readme).**

| Field | Type intended by the publisher | Meaning and rules |
| --- | --- | --- |
| `asset_id` | text, 7 characters | Business identifier: two-letter type prefix, hyphen, four digits. Unique. |
| `legacy_code` | **text**, exactly 4 characters | Code from the old inventory register. **Leading zeros are significant**: `0113` and `113` are different codes. |
| `asset_type_en` | text | Asset type in English. |
| `asset_type_gu` | text (Unicode) | Asset type in Gujarati script. |
| `installed_year` | integer | Year of installation, four digits. |
| `condition_score` | decimal number, 0.0–5.0 | Condition from the latest inspection; **null means "not yet assessed"**, not zero. |
| `last_inspection_at` | timestamp with UTC offset (ISO 8601) | Local time of the latest inspection, written with its `+05:30` offset. Null means never inspected. |
| `inspector_note` | text (Unicode), may be empty | Free text; may mix scripts. An empty value means "no note", which is different from null in the two fields above. |
| `x`, `y` | decimal numbers, metres | Position on the training grid. **CRS: none — local training grid; not Earth-referenced.** |

**Hand-checked facts about Table F7** (derivations in Instructor Appendix I.3): 6 records; 10 fields; one null in `condition_score`, one null in `last_inspection_at`, one empty `inspector_note`; extent x from 205 to 2190 and y from 195 to 950; strict-interior ward membership SL-0113, DR-0042, DR-0110 in Ward A; BN-0007 and SL-0055 in Ward B; TR-0301 outside both. Mean `condition_score` over the five assessed assets is 16.0 / 5 = **3.2**; if the null were wrongly treated as 0 the mean would be 16.0 / 6 ≈ 2.67. Seven of the ten field names are longer than ten characters.

**Example E7-1 — a separate, Earth-referenced synthetic point (GeoJSON teaching only).** GeoJSON cannot legitimately hold the training grid (7.2.2 explains why), so the GeoJSON examples use one made-up streetlight, `SL-9001`, at **longitude 70.000000°E, latitude 20.000000°N (WGS 84)**. That position is in open sea, chosen on purpose so that it cannot be mistaken for a real asset. It is used only to show syntax and coordinate order; it is *not* part of Table F7 and must never be mixed with training-grid coordinates. Chapter 6's `wards_e6.geojson` (Fixture E6) is a second, fuller example of a compliant file that you have already opened; refer back to it when reading 7.2.2.

---

## 7.1 Separate model, encoding, container, and service

### 7.1.1 Four layers that are usually confused

When a colleague says "send me the assets", four different questions are hiding in the sentence, and answering the wrong one causes most exchange failures.

| Layer | Question it answers | Examples from this course | What it does *not* decide |
| --- | --- | --- | --- |
| **Data model** | How is the world represented? | Vector (features with point/line/polygon geometry and attributes — Chapter 3); raster (a grid of cell values — Chapter 4) | How the bytes are written, or where they live |
| **Encoding** | How is one dataset written down? | A comma-separated text file; a JSON text following the GeoJSON rules; the binary record layout of a shapefile's `.shp`; the TIFF tag structure of a GeoTIFF | Whether more than one dataset can travel together, or who may read it |
| **Container** | What holds one or many datasets together, with shared bookkeeping? | A folder of shapefile companion files (weak container); a GeoPackage file (SQLite database holding many tables); a file geodatabase folder; an enterprise geodatabase inside PostgreSQL or another DBMS | Whether the data is reachable over a network |
| **Service** | How do other machines get the data on request, with rules applied? | A hosted feature layer in ArcGIS Online (Chapter 2); a feature service on ArcGIS Server; a WFS endpoint on GeoServer | What the underlying storage is — the client often cannot tell |

Read the table top to bottom as "what → how → where together → how delivered". The layers are largely independent:

- The same **model** (vector points) can use many **encodings** (CSV, GeoJSON, `.shp`).
- The same **encoding** can sit in different **containers** (a GeoJSON file alone in a folder, or a GeoJSON text stored in a database column).
- The same **container** can be exposed by different **services** or by none (a file geodatabase on a shared drive is a container with no service; publish it and it gains one — Chapter 2, module 2.1).

**Why this matters to a developer.** Bugs get filed at the wrong layer. "GeoJSON lost my Gujarati text" is almost never an encoding-rule problem (JSON text is Unicode) — it is usually a container or tool problem (the file was written in a non-UTF-8 code page, or a downstream shapefile export happened). "The service returns wrong dates" may be a service setting (time zone assumed at publishing — see 7.2.1) rather than a model or encoding fault. Name the layer before you debug.

**Developer analogy — and where it stops.** Model, encoding, container, and service correspond roughly to *domain model → serialization format → database/file system → API*. A Java object graph (model) can be serialized as JSON or Protocol Buffers (encoding), stored in a file or in Postgres (container), and served over HTTP (service). The analogy stops in one place: in GIS the *encoding* often carries the CRS and geometry rules, so choosing an encoding is also choosing which geometry and coordinate facts *can* be expressed. Choosing JSON over XML for an invoice does not change what an invoice is; choosing GeoJSON over GeoPackage changes which coordinate reference systems your data is allowed to have (7.2.2).

### 7.1.2 A filename extension establishes almost nothing

An extension is a hint about the *encoding*. It does not establish:

| Property | Why the extension cannot prove it | What does |
| --- | --- | --- |
| **Accuracy** | A `.gpkg` created from a hand-drawn sketch is as inaccurate as the sketch | The accuracy statement and capture method in the metadata (7.6) |
| **Completeness** | A file can be a filtered extract, a partial download, or a truncated write | A record count and coverage statement from the publisher, compared with your own count (7.7) |
| **Freshness** | A 2019 boundary saved yesterday has yesterday's file date and 2019's content | The edition date or date of observation, not the file-system timestamp |
| **Suitability** | The right format for a web demo is the wrong one for a legal boundary archive | The recipient's requirement (7.3.3, 7.8) |
| **Even the encoding itself** | A file renamed `.csv` may be tab-separated; a `.json` may or may not be GeoJSON; a `.tif` may have no georeferencing tags (7.4.2) | Opening it and inspecting the content |

A useful habit: treat an extension as a *claim* made by whoever named the file. Claims are checked, not trusted.

**Misconception.** "It's in a geodatabase, so it must be clean." A geodatabase (7.5) can *enforce* rules such as domains and relationships, but only if someone defined them and the data was loaded through them. An empty rule set enforces nothing. The container's capability is not the same as the data's quality.

### 7.1.3 File download versus access-controlled service URL — what should you expect to receive?

Before Phase 2 teaches protocols, fix your expectations with a comparison. Suppose the Chapter 2 solution's `Requests_Training` layer is offered to you two ways.

| You receive… | A ZIP containing `requests.gpkg` | A URL to a feature layer that requires signing in |
| --- | --- | --- |
| What you hold | A **copy**, frozen at download time | A **capability**: the right to ask a server for records, subject to its rules |
| Freshness | Goes stale immediately; you must re-download | Each request returns the current state (or a cached one — the service decides) |
| Completeness | Whatever the publisher put in the file | Whatever your account is allowed to see; fields and rows may be hidden by a view (Chapter 2, 2.3.2) |
| Performance | Local, fast, offline | Network-dependent; large pulls may be paged or limited by the server |
| Provenance evidence | Readme, metadata inside the container, checksums | Item page, service metadata, sharing level, owner |
| What can silently change | Nothing — but nothing updates either | Schema, symbology defaults, permissions, and data can all change without notice |
| Your obligations | Respect the licence; record retrieval date | Respect the licence *and* the access terms; do not republish beyond your permission |

Neither is "better". A file is right when you need a reproducible snapshot (an analysis you must be able to rerun with the same inputs). A service is right when you need current values and are content to be governed by the provider. In Phase 1 you will mostly handle files; the questions to ask of a service — who owns it, what am I allowed to see, what may change — are the same questions the intake form in 7.6 asks of a file.

**Comprehension check 7.1.** A colleague emails: "Attached `wards.shp` — it's the official ward layer." Name, for each of the four layers (model, encoding, container, service), what the attachment tells you and what it leaves unknown. Then state one thing "official" does not establish.

---

## 7.2 Introduce simple exchange formats

### 7.2.1 Coordinate CSVs: the simplest exchange, and the one with the most silent failures

A **coordinate CSV** is a plain-text table where two columns hold coordinates. It is the format most developers reach for first, because every language and spreadsheet can read it. Its weakness is that *nothing about geography is inside the file*: no CRS, no coordinate order, no field types. Everything must be supplied beside it.

**What must be explicit** — for every coordinate CSV, the publisher must state, and the recipient must verify:

| Item | Why it cannot be inferred safely | Table F7's answer |
| --- | --- | --- |
| **Which fields are coordinates** | Column names vary (`x`, `lon`, `Longitude`, `easting`, `POINT_X`…) and some tools guess from names | `x`, `y` |
| **Coordinate order and meaning** | "First column" means nothing until you know which axis it is (Chapter 5, 5.5) | `x` is the horizontal grid axis, `y` the vertical grid axis |
| **CRS and units** | A CSV has no place to write it; the numbers alone cannot prove it (Chapter 5, 5.7) | Local training grid, metres, **no Earth reference** — stated in the readme |
| **Delimiter** | "CSV" is used for comma-, semicolon-, and tab-separated files; a decimal comma (`3,5`) can collide with the field separator | Comma-separated; decimal point |
| **Character encoding** | Bytes outside ASCII are only meaningful once you know the encoding | UTF-8 (write a note; some tools want a byte-order mark, others reject it) |
| **Field types** | Text has no types; each importer *guesses* from the values it sees | Given in the schema dictionary above |
| **Null representation** | Empty, `NULL`, `NA`, `-9999`, and `0` are all used | Empty field = null for numbers and timestamps; empty text = "no note" |

**Format rule versus platform behaviour.** There is no single CSV standard that GIS tools follow for these items; each importer has its own rules, and the same file can load differently in three products:

- **ArcGIS Pro** reads delimited files through its *text file workspace*, which "determines the field properties and values"; a geoprocessing tool "cannot change the field properties or values of a delimited file", and a `schema.ini` file (the Microsoft ODBC text-driver convention) is the way to override how a field is typed [X15] [X16]. Esri's own example of a delimited file has an `ID` column with the values `001`, `002`, `003` [X15] — exactly the leading-zero case. Pro documents that "if attribute values are bracketed with special characters such as double quotes, the fields are interpreted as text fields" and that a `schema.ini` line such as `Col14=PLOTS Text` forces a text type [X16]. Microsoft's `schema.ini` reference defines the syntax (`Coln=ColumnName type [Width #]`, types including `Text`, `Long`, `Double`, `DateTime`), the `MaxScanRows` setting that controls how many rows are scanned to guess types, and the `CharacterSet` entry [X17].
- **ArcGIS Online** relies on field names and value formatting to decide types: "Since no data types are enforced in the file, ArcGIS Online relies on the field names and specific formatting in the fields to interpret the data type"; values in an unrecognised date format "will be created as string data types …, or, if only numerals are present, as an integer"; coordinate fields are recognised by name from a fixed list (for example `Latitude, Longitude`, `Lat, Long`, `Y, X`); fields "can be separated with a comma, semicolon, or tab"; files with non-English characters "must be encoded as Unicode or UTF-8, not ASCII"; and date/time values "are assumed to contain Coordinated Universal Time (UTC)" unless you specify a time zone when publishing [X18]. When you add a CSV to Map Viewer "you can define field types" [X18].
- **QGIS** (Data Source Manager ▸ Delimited Text) lets you choose the delimiter, and with *Detect field types* on it scans "the whole file to make sure that all values can actually be converted without errors, the fall-back field type is text"; it also honours an optional sidecar `.csvt` file; the geometry is defined by choosing the X and Y fields and a *Geometry CRS* [X19].
- **GDAL/OGR** (the library behind QGIS and many others) "returns all attribute columns as string data types if no field type information file (with .csvt extension) is available", treats "all CSV files … as UTF-8 encoded", and only considers empty strings as null if `EMPTY_STRING_AS_NULL=YES` [X23].

**You have already met this format.** Chapter 6's `requests_e6.csv` carried `E_m` and `N_m` columns whose meaning (UTM zone 43N eastings and northings in metres, EPSG:32643) lived only in the readme, and step 4 of that lab had to *tell* XY Table To Point the CRS because the tool's default is WGS 84 [X14]. Table F7's `x`, `y` are the same situation with an even weaker reference — a local grid.

**The leading-zero trap, worked.** `legacy_code` holds `0113`. Any importer that decides "this column is all digits, so it is an integer" stores 113 and the zero is gone; a later join against the old register (which has `0113`) matches nothing. The defence is to *declare* the type rather than let it be guessed: quote the values (`"0113"`) and, in ArcGIS Pro, add `Col2=legacy_code Text` to `schema.ini`; in QGIS, change the detected type in the sample table to Text before adding the layer, or ship a `.csvt`; in ArcGIS Online, set the field type when adding the file. Then *verify* by opening the attribute table and reading the value, not the type label.

**Verification item (instructor).** Whether quoting `"0113"` alone is enough to make QGIS's type detection choose Text is not stated in the cited QGIS page; check it in QGIS 3.44 and record the result in the change log (I.8).

**The timestamp trap, worked.** `last_inspection_at` for SL-0055 is `2026-08-30T02:10:00+05:30`. Three things can happen on import: the offset is kept (a *timestamp offset* type — supported in ArcGIS Pro 3.2 and later geodatabases [X31]); the value is converted to UTC and the offset dropped (`2026-08-29T20:40:00Z` — note the **date changed** because 02:10 local is 20:40 the previous day in UTC); or the value is read as text. All three are defensible if documented; only the *undocumented* one is a defect.

**Worked example: Table F7 as a CSV.** The first three lines of `assets_ch7.csv` (UTF-8, comma-delimited, header row) are:

```
asset_id,legacy_code,asset_type_en,asset_type_gu,installed_year,condition_score,last_inspection_at,inspector_note,x,y
SL-0113,"0113",Streetlight,સ્ટ્રીટલાઇટ,2018,3.5,2026-03-14T10:42:00+05:30,Lamp flickers at dusk,205,195
DR-0042,"0042",Drain,ગટર,2011,1.5,2025-11-02T15:05:00+05:30,"Silt build-up — ગટર ભરાયેલી છે",995,510
```

Read it by eye before loading it: 10 header names; the second note is quoted as a precaution, because free text may one day contain a comma (quoting is harmless, and in ArcGIS Pro it also makes the field's text type explicit [X16]); the coordinate columns are last; nothing in the file says what `x` and `y` mean — that is in the readme, which must travel with the file. The row for TR-0301 will read `TR-0301,"0301",Tree,વૃક્ષ,2005,,,,2190,520` — three consecutive empty fields, whose meaning (null, null, empty note) only the schema dictionary can settle.

### 7.2.2 GeoJSON: Geometry, Feature, FeatureCollection — and one non-negotiable CRS

**GeoJSON** is a JSON text format for geographic data, standardised as **RFC 7946** [S18]. Developers like it because it is readable, web-native, and supported by every browser mapping library. It has three object kinds you must recognise.

| Object | What it holds | Required members (RFC 7946 §3) |
| --- | --- | --- |
| **Geometry** | A shape: `Point`, `MultiPoint`, `LineString`, `MultiLineString`, `Polygon`, `MultiPolygon`, or `GeometryCollection` | `"type"` and `"coordinates"` (a `GeometryCollection` has `"geometries"` instead) |
| **Feature** | One entity: a geometry plus its properties | `"type": "Feature"`, `"geometry"` (a Geometry object or `null`), `"properties"` (an object or `null`); `"id"` is optional [S18, §3.2] |
| **FeatureCollection** | A list of features | `"type": "FeatureCollection"` and `"features"`, a JSON array of Feature objects [S18, §3.3] |

**Format rule — positions and coordinate order.** "A position is an array of numbers. There MUST be two or more elements. The first two elements are longitude and latitude, or easting and northing, precisely in that order and using decimal numbers. Altitude or elevation MAY be included as an optional third element" [S18, §3.1.1]. The optional third element "SHALL be the height in meters above or below the WGS 84 reference ellipsoid" [S18, §3.1.1] — a different vertical reference from a "height above ground", as Chapter 5 warned.

**Format rule — the CRS.** "The coordinate reference system for all GeoJSON coordinates is a geographic coordinate reference system, using the World Geodetic System 1984 (WGS 84) datum, with longitude and latitude units of decimal degrees" [S18, §4]. The earlier community specification (2008) allowed a `"crs"` member naming another CRS; RFC 7946 states that this "has been removed from this version of the specification because the use of different coordinate reference systems … has proven to have interoperability issues" [S18, §4]. The RFC keeps one escape hatch: "where all involved parties have a prior arrangement, alternative coordinate reference systems can be used without risk of data being misinterpreted" [S18, §4].

**What this means in practice — the blueprint's warning, expanded.** Adding a `"crs": {...}` member to a file of projected metre coordinates does **not** make the file compliant GeoJSON. A reader that follows RFC 7946 is entitled to treat the numbers as degrees. Two consequences:

1. Table F7 (training-grid metres, no Earth location) **cannot be published as standard GeoJSON at all**. If two parties agree in writing to exchange the grid in GeoJSON syntax, that is a "prior arrangement" under §4 — legitimate between them, but the file must be labelled as non-standard and must never be handed to a third party who has not seen the arrangement. The lab in 7.8 makes you write that label.
2. Earth-referenced projected data (for example a UTM feature class from Chapter 6) must be **transformed** to WGS 84 longitude/latitude before it is written as standard GeoJSON. That is a coordinate change (Chapter 6, *Project*), not a relabel (*Define Projection*).

**Platform note — ArcGIS Pro.** The *Features To JSON* tool has an *Output to GeoJSON* option and a *Project to WGS84* option; Esri's page states: "If this parameter is not used, the output .geojson file will contain a crs tag that can be used in some applications to define the coordinate system or coordinate reference system. This tag is not fully supported under the GeoJSON specification" [X24]. In other words, the tool will happily write the non-compliant file for you unless you tick the box; the default (unchecked) writes the `crs` tag [X24]. The reverse tool, *JSON To Features*, requires you to pick one geometry type when the input is `.geojson`, because "GeoJSON supports multiple feature types within the same file, and a feature class must be composed of features of the same feature type"; it also notes that the specifications "do not include a standard way to store datetime values" and that text fields get a very large length because GeoJSON has no length property [X25].

**Platform note — QGIS/GDAL.** The GDAL GeoJSON driver, which QGIS uses, writes the 2008-style file by default (`RFC7946=NO`); with `RFC7946=YES` it reprojects to WGS 84 if needed, writes polygon rings following the right-hand rule, and rounds coordinates to 7 decimal places [X22]. So "export to GeoJSON" in QGIS is *not* automatically RFC 7946 output; the option must be set in the layer options of the export dialog [X20] [X22].

**Format rule — polygons.** "A linear ring is a closed LineString with four or more positions. The first and last positions are equivalent, and they MUST contain identical values"; "A linear ring MUST follow the right-hand rule with respect to the area it bounds, i.e., exterior rings are counterclockwise, and holes are clockwise"; and for polygons with several rings "the first MUST be the exterior ring, and any others MUST be interior rings" [S18, §3.1.6]. This is the GeoJSON-specific version of the ring structure from Chapter 3; other formats use other conventions (the shapefile convention is the opposite orientation — GDAL's shapefile driver "assumes that … the vertices of outer rings should be oriented clockwise on the X/Y plane, and those of inner rings counterclockwise" [X02]). Never assume one format's ring rule holds in another.

**Two further rules developers hit early.** The set of types is closed: "Implementations MUST NOT extend the fixed set of GeoJSON types" [S18, §7]; but extra members ("foreign members") "MAY be used in a GeoJSON document" [S18, §6.1] — so a `"name"` at collection level is allowed, a new geometry type is not. On precision, the RFC notes that "6 decimal places … amounts to about 10 centimeters" for degree coordinates [S18, §11.2]; publishing 15 decimals adds bytes, not accuracy. The registered media type is `application/geo+json` [S18, §12].

### 7.2.3 Inspect a tiny example by hand before loading it

The blueprint asks you to read GeoJSON manually first, and to defer writing a parser. Here is Example E7-1 as a FeatureCollection with one point feature and one small square polygon (both synthetic, in open sea):

```json
{
  "type": "FeatureCollection",
  "features": [
    {
      "type": "Feature",
      "id": "SL-9001",
      "geometry": { "type": "Point", "coordinates": [70.000000, 20.000000] },
      "properties": {
        "asset_id": "SL-9001",
        "legacy_code": "9001",
        "asset_type_gu": "સ્ટ્રીટલાઇટ",
        "condition_score": null,
        "last_inspection_at": "2026-03-14T10:42:00+05:30"
      }
    },
    {
      "type": "Feature",
      "geometry": {
        "type": "Polygon",
        "coordinates": [
          [ [70.000, 20.000], [70.010, 20.000], [70.010, 20.010], [70.000, 20.010], [70.000, 20.000] ]
        ]
      },
      "properties": { "name": "training square (synthetic)" }
    }
  ]
}
```

Work through it with these questions, in order:

1. **Where is the point?** `[70.000000, 20.000000]` — first element longitude 70°E, second latitude 20°N, WGS 84 [S18, §3.1.1, §4]. Not "x then y in whatever CRS I like". If you saw `[20.0, 70.0]` the file would still parse — 20°E, 70°N is near the coast of northern Norway — which is why Chapter 5 said range checks alone cannot catch a swap.
2. **Is the polygon ring valid?** Five positions, first equals last ✔; it runs east along the south edge, north along the east edge, west along the north edge, south back to the start — that is counterclockwise on a map with east to the right and north up, so it satisfies the right-hand rule for an exterior ring ✔ [S18, §3.1.6].
3. **What is the CRS?** Nothing is written, and nothing needs to be: WGS 84 degrees is the only standard answer [S18, §4]. If a `"crs"` member had been present you would treat the file as suspect and ask the publisher what they meant.
4. **What types do the properties have?** `legacy_code` is a JSON *string* `"9001"` — the zero-preserving choice; `condition_score` is JSON `null` (GeoJSON has real nulls, unlike a shapefile — 7.3); `last_inspection_at` is a string, because GeoJSON has no date type [X25] — the receiving tool decides how to parse it.
5. **How many features, and of what geometry types?** Two, of different types. A tool that needs one geometry type per dataset (ArcGIS *JSON To Features* [X25]; any feature class [X05]) will make you choose.

That five-question pass takes a minute and finds most GeoJSON problems before any code runs. Parsing GeoJSON programmatically is left to Phase 2; understanding it is the skill that transfers to every platform.

**Developer analogy — and where it stops.** GeoJSON is to geography what JSON Schema-less REST payloads are to business objects: readable, ubiquitous, and untyped beyond JSON's own types. The analogy stops at the CRS: an ordinary JSON payload carries no equivalent of "these numbers are WGS 84 degrees, longitude first, and nothing else is allowed", so developers underestimate how much the GeoJSON rule constrains them.

**Misconception.** "GeoJSON is just JSON, so I can put any coordinates in it." You can put anything in a JSON file; it stops being standard GeoJSON the moment the coordinates are not WGS 84 longitude/latitude [S18, §4]. Expect other software to draw your projected metres as degrees — at the wrong place or nowhere.

**Comprehension check 7.2.** A partner sends `sites.geojson` whose first position is `[2381.5, 6120.0]` and whose top level contains `"crs": {"type": "name", "properties": {"name": "EPSG:32643"}}`. (a) Is this a standard RFC 7946 document? (b) What would a compliant reader assume the numbers mean? (c) Name two acceptable ways to proceed and one unacceptable one.

---

## 7.3 Explain Shapefile compatibility and limitations

### 7.3.1 A shapefile is a set of companion files, not one file

The **shapefile** is Esri's 1990s vector format. It is still the most widely *accepted* exchange format in GIS, which is why you must know it, and it is also the format most likely to lose meaning silently, which is why you should rarely choose it for new work. Esri's own documentation is blunt: shapefiles "are problematic for attributes … they cannot store null values, they round up numbers, they have poor support for Unicode character strings, they do not allow field names longer than 10 characters, and they cannot store time in a date field", and "unless your data will have very simple attributes and does not require geodatabase capabilities, do not use shapefiles" [S20].

A "shapefile" is really a **group of files sharing one base name in one folder**. Esri's page lists them with their roles [S20]:

| File | Role | Required? (per [S20]) |
| --- | --- | --- |
| `.shp` | "The main file that stores the feature geometry. No attributes are stored in this file—only geometry." | Yes |
| `.shx` | "A companion file to the .shp file that stores the position of individual feature IDs in the .shp file." (an index into `.shp`) | Yes |
| `.dbf` | "The dBASE table that stores the attribute information of features." | Yes |
| `.prj` | "The file that stores the coordinate system information." | **No** — and this is the trap (7.3.2) |
| `.sbn`, `.sbx` | Spatial index files | No |
| `.atx` | dBASE attribute index | No |
| `.ixs`, `.mxs` | Geocoding index | No |
| `.xml` | ArcGIS metadata about the shapefile | No |
| `.cpg` | Code page (character set) of the `.dbf` text. Not listed on the Pro page; described in Esri's legacy ArcMap documentation as "an optional file that can be used to specify the codepage for identifying the characterset to be used" [X03], and read by GDAL: "an attempt is made to read the code page setting in the .cpg file, or as a fallback in the LDID/codepage setting from the .dbf file, and use it to translate string fields to UTF-8 on read, and back when writing" [X02] | No |

**Figure 7.1 — Illustrated package inspection (schematic).** What you should see in a folder, and what each piece contributes:

```
 assets_ch7.shp   ──► geometry only: 6 point records, no attributes
 assets_ch7.shx   ──► byte offsets of each record inside .shp (index)
 assets_ch7.dbf   ──► attribute table: 10-char field names, dBASE types, no true nulls
 assets_ch7.prj   ──► CRS definition text ── MISSING? then CRS = unknown, not "none"
 assets_ch7.cpg   ──► "UTF-8" (or a code page) ── MISSING? then text encoding is a guess
 assets_ch7.sbn/.sbx, .shp.xml ──► optional index and metadata; harmless if absent
```

Narrate it as *geometry file, its index, its attribute table, its CRS note, its encoding note* — five roles — rather than reciting extensions.

**Consequences for handling.** Copy, rename, zip, and email all the companion files together; renaming `roads.shp` to `streets.shp` while leaving `roads.dbf` behind produces a dataset with geometry and no attributes (if it opens at all). Esri states the files are visible in File Explorer but shown as one item in ArcGIS Pro [S20], so the Catalog view hides exactly the structure you need to check. Look in the folder.

### 7.3.2 Why `.prj` matters, and why "not required" is dangerous

The `.prj` holds the CRS definition as text. Because it is optional, a shapefile without it opens fine — with an **unknown** coordinate system. Chapter 5 taught what to do: investigate, do not guess. Chapter 6 taught the remedy once the correct CRS is *established* from evidence: *Define Projection* "only updates the existing coordinate system information; it does not modify any geometry" [S15]. Writing a `.prj` is exactly that operation. Writing the *wrong* `.prj` is the relabelling error of Chapter 6 — the data still opens, still draws, and is now confidently wrong. (Chapter 6's gate question about a shapefile delivered without its `.prj` is exactly this case.)

**Format rule versus platform behaviour.** The `.prj` content is Esri-flavoured well-known text; different software may write slightly different text for the same CRS, and GDAL reads a `.prj` "to associate a projection with features" when present [X02]. Do not diff `.prj` files to decide whether two datasets share a CRS; read the definitions.

### 7.3.3 Practical attribute limitations: a successful export can still lose meaning

Esri's shapefile page documents each limitation. Below, each is paired with the F7 field it will bite. These are format rules for the `.dbf`/dBASE structure, so they apply in every product; the *way* a product copes (rename, substitute, warn) is platform behaviour.

| Limitation (format rule, [S20]) | F7 field affected | What happens (prediction from the rule; record the actual result in the lab) |
| --- | --- | --- |
| "Field names cannot be longer than 10 characters." | `legacy_code` (11), `asset_type_en`, `asset_type_gu` (13), `installed_year`, `inspector_note` (14), `condition_score` (15), `last_inspection_at` (18) | Names are shortened. `asset_type_en` and `asset_type_gu` both start with the same ten characters, `asset_type`, so the writer must invent distinct names; GDAL's rule is that duplicates "will be truncated to 8 characters and appended with a serial number from 1 to 99" [X02]; ArcGIS Pro has its own renaming behaviour (not stated on [S20]; observe it). Any downstream code or join keyed on the original names breaks. |
| "Null values are not supported in shapefiles." | `condition_score` (TR-0301), `last_inspection_at` (TR-0301) | Esri's substitution table: for most tools a numeric null becomes **0**; a text null becomes a single space; a date null is "Stored as zero, but displays \<null\>" [S20]. After export, "ArcGIS cannot determine whether a field value represents a null value or a legitimate value" [S20]. TR-0301's *not assessed* becomes a *score of 0* — the worst possible condition — and the mean in the fixture drops from 3.2 to about 2.67. GDAL's handling of nulls on write is not stated on its page; observe it. |
| "Date fields only support date; they do not support time." | `last_inspection_at` | The time is dropped. Depending on whether the writer first converts to UTC, SL-0055's `2026-08-30T02:10+05:30` may be stored as **2026-08-30** or as **2026-08-29**. Either way the offset is gone. |
| Text width 254 characters; record length 4,000 bytes; 255 fields [S20] | `inspector_note` if a long note appears | Truncation. Not triggered by F7 but must be checked on real data. |
| "By default, in ArcGIS Pro, dBASE files support the ANSI character set … Esri includes Unicode support for dBASE files … However, this additional support may not be available in non-Esri applications." [S20] | `asset_type_gu`, `inspector_note` | Gujarati text survives only if the writer uses a Unicode code page **and** the reader honours the `.cpg`/LDID. GDAL's default encoding option is `LDID/87` [X02], and QGIS exposes an *Encoding* choice in its export dialog [X20]; choosing UTF-8 there is the safe path. Expect `?`, boxes, or garbage in the failure case. |
| Loss of "Subtypes, Attribute domains, Geometric networks, Topologies, Annotation" [S20] | (none in F7; matters for geodatabase sources — 7.5) | Rules vanish; the *codes* stay (for example `2` instead of `Drain`) unless the export is told to transfer descriptions [X28]. |
| `shape_length` / `shape_area` fields are not maintained after edits [S20] | (lines/polygons only) | Stale measurements after any edit. |
| "There is a 2 GB size limit for any shapefile component file" [S20]; GDAL: "it is not recommended to use a file size over 2GB for both .SHP and .DBF files" [X02] | (not triggered) | Large exports must split or use another container. |
| One geometry type per shapefile [X02]; "Parametrically defined curves" are densified to vertices [S20] | (points only in F7) | Curves and mixed geometry cannot round-trip. |

**Worked example: predicting the loss before exporting.** Take the F7 row for TR-0301: `TR-0301, "0301", Tree, વૃક્ષ, 2005, null, null, "", 2190, 520`. A shapefile export is expected, from the rules above, to produce a record whose fields are named something like `asset_id, legacy_cod, asset_type, asset_ty_1 (or similar), installed_, condition_, last_inspe, inspector_` — the exact names depend on the writer — with `condition_` = 0 rather than null, `last_inspe` = a zero-date shown as null, and `વૃક્ષ` intact only if the encoding was Unicode. The tool will report success. Every one of these changes is invisible unless you compare before and after (7.7).

**Why a green message is not a validation.** The exporter checks that it could *write bytes in the format's structure*. It does not know that `0` meant "not assessed", that `legacy_cod` used to be joined by a longer name, or that a reader without Unicode support will mangle the note. Format validity and meaning preservation are different tests, and only you can run the second.

### 7.3.4 Position: a format you will encounter, not the default for new solutions

You will receive shapefiles from contractors, government portals, and older systems for years to come, and some recipients will accept nothing else. Treat the shapefile as an **export target chosen for a named recipient**, never as a working or archival store. The question to answer before exporting is the recipient's, not yours:

| Recipient says… | Is a shapefile justified? | What to do |
| --- | --- | --- |
| "Our old desktop tool only reads `.shp`" | Yes, as a delivered copy | Export from the master; document every rename and substitution (7.7); send all companion files plus a readme |
| "We just need to see the points on a web map" | Usually no | GeoJSON (if Earth-referenced WGS 84) or a service (Chapter 2) |
| "We need the full schema with nulls, timestamps, and both languages" | No | GeoPackage or a geodatabase (7.4, 7.5) |
| "Send whatever, we'll figure it out" | Not an answer | Ask what they will do with it; choose from the answer |

**Developer analogy — and where it stops.** A shapefile is like a legacy fixed-width flat-file interface with an 8.3-style naming rule: everyone can read it, and every modern data type has to be squashed to fit. The analogy stops at the multi-file structure — no flat file breaks when one of its five companion files is left behind — and at nulls: most flat-file conventions at least allow an explicit null marker.

**Misconception.** "The export succeeded, so the data is the same." Success means the container accepted the bytes. Nulls became zeros, names were shortened, times were dropped, and text may have been re-encoded — all without an error.

**Comprehension check 7.3.** A `.dbf` shows `condition_` = 0 for TR-0301. Can you tell, from the shapefile alone, whether the asset was assessed as 0 or never assessed? What single piece of external evidence would settle it?

---

## 7.4 Introduce richer containers and raster exchange

### 7.4.1 GeoPackage: an SQLite database with standard tables — capability defined by the standard and the implementation

**GeoPackage** is an OGC standard that describes how to store geospatial data inside an SQLite database file. The project site defines it as "an open, standards-based, platform-independent, portable, self-describing, compact format for transferring geospatial information", storing "vector features, tile matrix sets of imagery and raster maps at various scales, attributes (non-spatial data), extensions" in a single file; the current adopted version is 1.4.0 [S19]. The distinction the site draws is the one to remember: "a GeoPackage is the SQLite container and the GeoPackage Encoding Standard governs the rules and requirements of content stored in a GeoPackage container" [S19] — container and rules, separately named.

**Format rules you can check yourself** (from the 1.4.0 standard [X01]; requirement numbers as printed):

| Rule | What it says | Why it matters to you |
| --- | --- | --- |
| Req 1 | "A GeoPackage SHALL be a SQLite … database file using version 3 of the SQLite file format." The first 16 bytes are the string `SQLite format 3`. | Any SQLite tool can open it; you can inspect it with `sqlite3` or a DB browser before any GIS software. |
| Req 2 | The SQLite header's `application_id` is `0x47504B47` ("GPKG"). | This — not the extension — is how a reader knows it is a GeoPackage. |
| Req 3 | "A GeoPackage SHALL have the file extension name '.gpkg'." | ArcGIS Pro requires the `.gpkg` extension to open one [X26]. |
| Req 10, 11 | A `gpkg_spatial_ref_sys` table is mandatory and "SHALL contain at a minimum" three records: `srs_id` 4326 for WGS 84, **−1 "for undefined Cartesian coordinate reference systems"**, and 0 "for undefined geographic coordinate reference systems". | The standard has an explicit, honest slot for data like Table F7 (a Cartesian grid with no Earth reference): `srs_id = -1`. Compare GeoJSON, which has no such slot. |
| Req 13 | A `gpkg_contents` table lists every user dataset with its type (`features`, `tiles`, `attributes`), extent, and `srs_id`. | One query tells you what the file contains — the "self-describing" claim. |
| Req 5 and Table 1 | Column types are restricted to a fixed list. `TEXT` is "encoded in either UTF-8 or UTF-16"; `DATE` is ISO 8601 `YYYY-MM-DD`; `DATETIME` is "ISO-8601 date/time string in the form YYYY-MM-DDTHH:MM:SS.SSSZ with T separator character and Z suffix for coordinated universal time (UTC)". | Gujarati text is safe; long field names are safe (SQLite has no 10-character rule); nulls are ordinary SQL nulls. But a **timestamp with an offset must become UTC** — `2026-08-30T02:10:00+05:30` becomes `2026-08-29T20:40:00.000Z` — an *intentional adaptation* you must document. |
| Req 29–31 | Feature tables have an integer primary key, "only one geometry column", and are registered in `gpkg_geometry_columns`. | One geometry per table, like a feature class. |
| Req 119 | Attribute (non-spatial) tables are allowed, registered with `data_type` `attributes`. | Inspections without geometry can travel in the same file. |
| Req 58–64 | Extensions are declared in a `gpkg_extensions` table; "the absence of a gpkg_extensions table or the absence of rows … SHALL indicate that the file is a GeoPackage (as opposed to an Extended GeoPackage)"; each row carries a `scope` of `read-write` or `write-only`. | A reader can "fail fast" by checking one table. Extensions are how spatial indexes (`gpkg_rtree_index`), non-linear geometry, metadata, and related tables are added. |

**The blueprint's caution, made concrete.** The standard is "designed for extension", and OGC has adopted extensions such as the Related Tables Extension [S19]. But an extension is only useful if *both* the writer and the reader implement it; the standard warns that custom extensions "do introduce interoperability risks" [X01, §2.3]. So: a GeoPackage written by product A with a related-tables extension may open in product B with the tables present but the *relationship* invisible. "Supports GeoPackage" means "supports the core"; support for each extension must be checked per product.

**Platform notes.**
- *ArcGIS Pro* opens a GeoPackage through a folder connection or by adding it to the project; "SQLite databases and OGC GeoPackage files support a single-user connection"; fields can be added but not renamed or deleted from the fields view, and the *Create SQLite Database* tool creates a GeoPackage at a chosen standard version (1.0 through 1.4) [X26] [X27]. Data is then written into it with the ordinary export tools (the lab uses *Export Features* [X28] with the output placed inside the `.gpkg`; the cited pages describe the tool and the container separately, so the instructor confirms this route in the installed version — I.6).
- *QGIS* creates GeoPackage layers directly (*New GeoPackage Layer*) and exports to it via *Export ▸ Save Features As…*, where a *Persist layer metadata* option stores metadata "in the newly created layer, if the output is of GeoPackage format" [X20]. QGIS's *Save to Default Location* for metadata "always saves the metadata in the internal metadata tables of the GeoPackage" [X21].

**Developer analogy — and where it stops.** A GeoPackage is an SQLite file with a schema convention, much like a mobile app's local database. The analogy is nearly exact, which is the point — you can use your SQL skills to inspect it. It stops at the geometry column: the BLOB there follows a GeoPackage binary encoding that plain SQL cannot interpret without a spatial extension, and the `gpkg_*` bookkeeping tables must stay consistent with the user tables, so hand-editing with SQL can produce a file that is valid SQLite and invalid GeoPackage.

### 7.4.2 GeoTIFF: a TIFF that carries its georeferencing — but check every TIFF

**GeoTIFF** is the common exchange format for georeferenced rasters (elevation grids, imagery — Chapter 4). OGC's GeoTIFF Standard 1.1 (document 19-008r4 [X34]) "defines the Geographic Tagged Image File Format (GeoTIFF) by specifying the content and structure of a group of industry-standard tag sets for the management of georeferenced or geocoded raster imagery" using TIFF [X12]. The key structural fact: "A GeoTIFF file is a TIFF 6.0 file and inherits the file structure as described in the corresponding portion of the TIFF specification" [X12]; the geo tags are independent of the image data, so an ordinary image viewer opens a GeoTIFF as a picture and simply ignores the geography.

The georeferencing lives in reserved TIFF tags: `ModelTiepointTag` (which raster cell corresponds to which model coordinate), `ModelPixelScaleTag` (cell size in model units), or `ModelTransformationTag` (a full affine matrix for rotated grids), plus `GeoKeyDirectoryTag` and its companions holding the CRS description — typically an EPSG code in `ProjectedCRSGeoKey` or `GeodeticCRSGeoKey` — and `GTRasterTypeGeoKey` stating whether a cell's coordinate refers to its area or its centre point (`PixelIsArea` / `PixelIsPoint`) [X12]. QGIS's introduction describes the same idea in words: georeferencing "consists of a coordinate for the top left pixel in the image, the size of each pixel in the X direction, the size of each pixel in the Y direction, and the amount (if any) by which the image is rotated", sometimes "provided in a small text file accompanying the raster" [S11, §6.3].

**Why "every TIFF" must be inspected.** The `.tif` extension proves only that the file is a TIFF. Without the geo tags it is a picture with rows and columns and no place in the world (Chapter 4's independent check). Software may then look for sidecar files: GDAL states that "if no georeferencing information is available in the TIFF file itself, GDAL will also check for, and use an ESRI world file with the extension .tfw, .tifw/.tiffw or .wld" [X13] — so a raster can appear georeferenced in one folder and lose it when the `.tif` is copied without its `.tfw`. The same companion-file discipline as the shapefile applies.

**Inspection checklist for any raster you receive** (each item is a Chapter 4 concept; the format only tells you where to look):

| Check | Where it comes from in a GeoTIFF | What goes wrong if you skip it |
| --- | --- | --- |
| **CRS** | GeoKeys (EPSG code or user-defined definition) [X12]; if absent, a sidecar `.prj`/`.aux.xml` or nothing | Cells drawn in the wrong place, or the "unknown CRS" guessing error |
| **Bands** | TIFF samples per pixel; band count and order are not self-explanatory | Treating band 1 of a 3-band image as elevation |
| **Cell size** | `ModelPixelScaleTag` or the transformation matrix, in the CRS's units [X12] | Confusing 10 *degrees* with 10 *metres* |
| **Units of the values** | Not in the standard tags; must come from metadata or the publisher | Reading elevation in feet as metres |
| **NoData** | **Not part of the OGC standard.** GDAL "stores band nodata value in the non standard TIFFTAG_GDAL_NODATA ASCII tag (code 42113)" and "all bands must use the same nodata value" [X13]; other writers use sidecar metadata | NoData counted as a real value — the Appendix A.3 mean error (20 → 17.78) |
| **Data type** | TIFF sample format (GDAL lists Byte, UInt16, Int16, UInt32, Int32, Float32, Float64 and complex types) [X13] | Integer truncation of a continuous surface |
| **Cell reference** | `GTRasterTypeGeoKey`: `PixelIsArea` or `PixelIsPoint` [X12] | Half-cell positional offset when combining rasters |

GeoTIFF is the *common* raster exchange choice, not the only one: GeoPackage can hold tile pyramids [S19], and ArcGIS Pro has an *Add Raster To GeoPackage* tool [X26]. Cloud-optimised GeoTIFF (COG) is a layout convention for the same format that GDAL exposes as a separate driver [X13]; it is mentioned here only so the name is familiar.

### 7.4.3 Conversion between containers can keep geometry while losing everything around it

Geometry is the part of a dataset that converters handle best, because every format has a slot for it. What sits *around* geometry has no slot in many formats. Table 7.4 summarises, format by format, what has a place to live. "Yes" means the format has a defined place for it; it does **not** mean every tool fills that place. The lab exists to check the tool.

**Table 7.4 — Format comparison: does the format have a defined place for each property?** (Rules from [S18], [S20], [X01], [X02], [X05], [S21]; the geodatabase columns summarise 7.5.)

| Property | Coordinate CSV | GeoJSON (RFC 7946) | Shapefile | GeoPackage | File / enterprise geodatabase |
| --- | --- | --- | --- | --- | --- |
| Geometry types | Points only (as columns); WKT column possible in some tools | Point, line, polygon, multi-*, collection | One type per file; no curves | Point, line, polygon, multi-*; curves via extension | All, including curves and multipatch |
| CRS stored inside | No (must be documented beside) | Fixed: WGS 84 lon/lat only | Optional `.prj` | Yes, `gpkg_spatial_ref_sys`; undefined Cartesian allowed (`-1`) | Yes; "Unknown" allowed |
| Field name length | Unlimited (importer may restrict) | Unlimited (JSON keys) | 10 characters | SQLite (practically unlimited) | 128 characters in a file geodatabase (ArcGIS Pro) [X30]; DBMS-dependent for enterprise |
| Null values | Convention only (empty, `NULL`, …) | Yes (`null`) | **No** — substituted | Yes (SQL NULL) | Yes |
| Date **and time** | Text (convention) | Text (no date type) | Date only | `DATETIME` as UTC (`Z`) | Date, date-only, time-only, timestamp offset (Pro 3.2+) [X31] |
| Unicode text | Yes if encoding is declared/UTF-8 | Yes (JSON is Unicode) | Code-page dependent; ANSI default in Pro | Yes (UTF-8/UTF-16) | Yes |
| Several datasets in one package | No (one table per file) | One collection per file (mixed geometry allowed) | No (one per base name) | Yes (tables in one file) | Yes |
| Non-spatial tables | Yes (it *is* one) | No (features must have `geometry`, possibly `null`) | `.dbf` alone | Yes (`attributes`) | Yes (tables) |
| Relationships between tables | No | No | No | Only via extension (Related Tables) | Yes (relationship classes) |
| Attribute rules, domains, subtypes | No | No | No (lost on export) | No in core; schema extension exists | Yes |
| Styling / symbology | No | No (foreign members at best) | No (`.lyrx`/`.qml`/`.sld` sidecars are separate) | Not in core; some products keep their own styles in additional, product-specific tables | Not in the dataset; in layer files / maps |
| Metadata inside the package | No (readme beside) | No (foreign members) | `.shp.xml` sidecar | Yes, via metadata extension tables | Yes (item metadata) |
| Multi-user editing | No | No | No | No ("single-user connection" in Pro [X26]) | Enterprise geodatabase: yes |

Three patterns to expect when converting:

1. **Geometry survives; typing degrades.** CSV or GeoJSON → anything: field types are re-guessed; dates become text or vice versa.
2. **Geometry and typing survive; structure is lost.** Geodatabase → GeoPackage: relationship classes, domains, subtypes, and attribute rules have no core slot. The tables arrive; the rules that kept them consistent do not, and nothing tells the recipient that a `ward_code` of `7` used to be validated against a list.
3. **Everything except styling survives.** Any dataset → any dataset: symbology, labels, and pop-up definitions live in maps, layer files, or product-specific sidecars (Chapter 3, 3.5). A container that "stores styles" stores *its own product's* styles.

**The lab must verify what survives.** Table 7.4 tells you what *can* be kept. Only a before/after comparison (7.7) tells you what *was* kept by your tool, your version, and your options.

**Misconception.** "GeoPackage is the open version of a file geodatabase." Both are single-file/single-folder containers holding many tables, and both keep nulls, long names, and Unicode. But the geodatabase's *information model* — relationship classes, domains, subtypes, topologies, versioning in the enterprise case (7.5) — is mostly outside GeoPackage's core standard. Converting one to the other keeps tables and loses model.

**Comprehension check 7.4.** You receive `survey.tif` (no sidecar files) and `survey.gpkg`. For each file, list the first three things you would inspect and the single SQL query or tag that would answer the first of them.

---

## 7.5 Introduce ArcGIS geodatabases

### 7.5.1 Storage plus an information model

Esri defines the **geodatabase** at two levels. At the storage level it is "a collection of geographic datasets of various types held in a common file system folder, or a multiuser relational database management system such as IBM Db2, Microsoft SQL Server, Oracle, PostgreSQL, or SAP HANA" [S21]. At the model level, "geodatabases have a comprehensive information model for representing and managing geographic information. This information model is implemented as a series of tables containing feature classes and attributes. In addition, advanced GIS data objects add real-world behavior, rules for managing spatial integrity, and tools for working with spatial relationships of the core features and attributes" [S21]. It is "the native data structure for ArcGIS and is the primary data format used for editing and data management" [S21].

Hold the two levels apart, because they answer different questions:

| Level | Question | Example answers |
| --- | --- | --- |
| **Physical store** | Where are the bytes? | A `.gdb` folder; a `.geodatabase` SQLite file; tables in PostgreSQL |
| **Information model** | What does ArcGIS know about those bytes beyond "table with a geometry column"? | Which tables are feature classes; which fields have domains; which tables are related; which datasets share a spatial reference; who edited what and when |

The information model is what a plain "geometry table" lacks. A PostGIS table with a `geom` column and a `condition_score` column is a perfectly good spatial table. It becomes part of a geodatabase only when the *geodatabase system tables* that describe the model exist in the database and the table is registered with them (7.5.3).

**Three types** [X04]:

| Type | Physical form | Editors | Notes from Esri's comparison |
| --- | --- | --- | --- |
| **File geodatabase** | "stored as multiple files in a folder with a .gdb extension. Each dataset is contained in a single file." Datasets grow to 1 TB by default. | "Single editor and can support multiple readers" | Freely available to all ArcGIS Pro users; supports the full information model; no versioning |
| **Mobile geodatabase** | "stored in an SQLite database that is entirely contained in a single file and has a .geodatabase extension"; 2 TB limit | Single editor | Supports domains, subtypes, relationship classes, attachments; SQL access without an ArcGIS licence because SQLite needs none; raster datasets not supported [X05] |
| **Enterprise geodatabase** | "stored in relational databases" (Oracle, SQL Server, Db2, PostgreSQL, SAP HANA); "virtually unlimited in size and number of users" | "Multiple editors and can support multiple readers" | Versioning supported; security "managed through the DBMS" |

**Developer analogy — and where it stops.** A file geodatabase is like an application's private on-disk data directory with its own schema registry: fast, portable, single-writer. An enterprise geodatabase is like adding an ORM's metadata tables (migrations, model registry) to a shared production database: the database is still Postgres, but the application now recognises its own objects in it. The analogy stops at *behaviour*: an ORM registry describes tables; the geodatabase model also enforces spatial behaviour (topology rules, network connectivity, attribute rules) that the DBMS itself knows nothing about.

### 7.5.2 Feature class, nonspatial table, and feature dataset — at an introductory level

Three dataset kinds cover most of what you will meet in Phase 1 [X05] [X06]:

| Kind | Esri's definition | In the municipal scenario | Plain-developer reading |
| --- | --- | --- | --- |
| **Feature class** | "a collection of geographic features with the same geometry type (such as point, line, or polygon), the same attributes, and the same spatial reference" [X05]; "homogeneous collections of common features" [X32] | `Assets` (points), `Roads` (lines), `Wards` (polygons) | A table with exactly one geometry column, one geometry type, and one CRS for every row |
| **Table** (nonspatial) | "the basic storage object in the database. Tables are composed of columns and rows … Tables that contain spatial attributes are called feature classes." [X05] | `Inspections` — one row per visit, keyed to an asset by `asset_id` (Chapter 3, Table F9) | An ordinary relational table; it can be related to a feature class (Chapter 8) |
| **Feature dataset** | "a collection of related feature classes that share a common coordinate system", used "to facilitate creation of controller datasets" such as a topology or utility network [X06] | A `Municipal` feature dataset holding `Wards` and `Roads` so a topology can later enforce "wards do not overlap" | A named group whose members are forced to share a CRS; a prerequisite for cross-class rules |

Two consequences you can reason about now:

- A shapefile or a single GeoPackage feature table maps naturally onto a *feature class*; a `.dbf` alone or a GeoPackage attributes table maps onto a *table*. Nothing outside a geodatabase maps onto a *feature dataset*, which is one reason "export the whole geodatabase to GeoPackage" flattens structure (7.4.3).
- Because a feature dataset fixes the CRS for its members, Esri warns not to change the coordinate system of an existing feature dataset with *Define Projection* [S15] — the assignment-versus-transformation lesson from Chapter 6 applied to a container.

**Transferability note.** The file geodatabase is an Esri format, but it is not locked to Esri software: GDAL's OpenFileGDB driver gives QGIS and other GDAL-based tools read access to file geodatabases created by ArcGIS 10 and later, and "write and update capabilities are supported since GDAL >= 3.6", including field domains and relationships [X33]. As with GeoPackage extensions, which parts of the information model round-trip through a non-Esri writer must be checked, not assumed.

Creating geodatabases, feature classes, and datasets is a Phase 2 procedure. In Phase 1 you inspect them: open the Catalog pane, expand a `.gdb`, and note which items are feature classes (geometry icon), tables (grid icon), and feature datasets (folder-like group), then read each item's fields and spatial reference in its properties. That inspection is the ArcGIS form of the 7.7 "before" snapshot.

### 7.5.3 PostGIS is not automatically an enterprise geodatabase; ArcGIS Data Store is something else again

Three things are confused because all three can involve PostgreSQL. Keep them apart with the questions *who defines the model?* and *who may access the bytes?*

**PostGIS.** "PostGIS extends the capabilities of the PostgreSQL relational database by adding support for storing, indexing, and querying geospatial data" [S09]. It is a spatial extension to a general-purpose database — spatial types, indexes, and functions. It is open source and vendor-neutral; QGIS, GeoServer, and custom code use it directly.

**Enterprise geodatabase in PostgreSQL.** Esri's page states that enterprise geodatabases "are collections of objects—such as tables, views, and stored procedures—in a relational database management system", that PostgreSQL "is one such database management system in which you can store a geodatabase", and — the decisive sentence — "If the database contains geodatabase system tables, it is considered a geodatabase in ArcGIS" [X07]. Those system tables are created by the *Enable Enterprise Geodatabase* tool, which "creates geodatabase system tables, stored procedures, functions, and types in an existing database", requires an ArcGIS Server authorization (keycodes) file, and must be run from ArcGIS Pro (Desktop Standard or Advanced) or ArcGIS Server [X09]. Before that tool runs, "you must enable a spatial type in the database": ArcGIS supports three spatial types in PostgreSQL — Esri's ST_Geometry, PostGIS geometry, and PostGIS geography — and "PostGIS is a third-party, open source installation" [X08]. Esri also notes that database tables not registered with the geodatabase can be viewed and published from ArcGIS Pro but cannot be edited through a database connection [X07].

Put together:

| Statement | True? | Why |
| --- | --- | --- |
| "We have PostGIS, so we have an enterprise geodatabase." | **No** | PostGIS supplies a spatial type. The geodatabase exists only once the geodatabase system tables have been created (with the licensed tool) [X07] [X09]. |
| "An enterprise geodatabase in PostgreSQL always uses PostGIS." | **No** | It may use Esri's ST_Geometry instead; PostGIS geometry/geography is one of three options, and the only option in the cloud database services Esri lists [X08] [X09]. |
| "A PostGIS table can be shown in ArcGIS Pro without a geodatabase." | Yes | Through a database connection, read-only for editing purposes [X07]. |
| "Tables in the same database that are not registered are outside the geodatabase model." | Yes | They coexist "alongside geodatabase data" but are not part of the model [X07]. |

**ArcGIS Data Store.** This is a different product concept. Esri defines it as "an application that provides the tooling and functional capabilities required to create and maintain the system storage types and the ArcGIS information model used by the hosting server in a base ArcGIS Enterprise deployment", creating a relational store, an object store, a spatiotemporal big data store, and a graph store; and it states that "access to the content of each data store listed above is provided exclusively through web layers" [X10]. Enterprise's storage overview draws the line as *system storage* — "managed by ArcGIS, which means you interact with that data only using the web service or layer" — versus *user storage*, "such as a database, folder, or cloud store" that you bring and can "connect to … with third-party tools" [X11]. ArcGIS Enterprise's introduction summarises the role: "ArcGIS Data Store provides data storage for the hosting server used with your deployment" [S04].

So, for a developer:

| | PostGIS database | Enterprise geodatabase (in PostgreSQL) | ArcGIS Data Store (relational store) |
| --- | --- | --- | --- |
| Who defines the schema | You, with SQL | You, through ArcGIS tools; ArcGIS adds its system tables | ArcGIS, when a hosted layer is published |
| How you reach the bytes | Any SQL client | SQL client (read) or ArcGIS (read/write with model rules) | **Only** through web layers; direct database access is not a supported path [X10] |
| Where it fits (Chapter 2 roles) | R2 data storage, vendor-neutral | R2 data storage, ArcGIS-aware ("user storage" in Enterprise terms [X11]) | R2 for hosted layers only ("system storage" [X11]) |
| Phase 1 boundary | Concept only | Concept only — no administration here | Concept only — no deployment here |

**Misconception.** "Data Store is where our enterprise geodatabase lives." No: the relational data store holds the data behind *hosted* feature layers, is managed by ArcGIS, and is reached only through web layers [X10] [X11]. An enterprise geodatabase is *your* database with ArcGIS system tables added, reachable by SQL and registered with the server as user storage [X07] [X11]. A deployment often has both, and they are administered differently.

**Comprehension check 7.5.** A partner says: "Our PostgreSQL has PostGIS and your `Assets` table is in it, so just connect ArcGIS Pro and edit." List what would have to be true for editing through a database connection to work, and name the tool and licence requirement that establish it.

---

## 7.6 Evaluate sources and metadata

### 7.6.1 The dataset intake form

Every dataset that enters a project should be admitted through the same short form, filled in *before* the data is used. It is the place where the answers that formats cannot hold (7.1.2, 7.2.1) are written down, and it is what a colleague reads six months later when the source URL is dead.

**Table 7.6 — Dataset intake form (this course's template; the fields are the blueprint's required minimum).**

| # | Field | What to write | Filled in for Table F7 |
| --- | --- | --- | --- |
| 1 | **Publisher** | The organisation or person who issued the data, and the contact | LiveBird Technologies training team (synthetic) |
| 2 | **Download / service URL** | Exact URL or path; for a service, the item and service URLs | Printed in this document (no URL) |
| 3 | **Retrieval date** | When *you* obtained it (file date is not retrieval date) | 19 September 2026 |
| 4 | **Edition / date of observation** | When the content was current; for observations, when they were made | Fixture revision 1.0; `last_inspection_at` per record |
| 5 | **Geographic coverage** | Named area and the extent as numbers | Training grid; x 205–2190, y 195–950 |
| 6 | **CRS** | Full name and identifier, or "unknown — under investigation", or "none — local grid" | None — local training grid, not Earth-referenced |
| 7 | **Units** | Coordinate units and the units of every measured field | Metres (coordinates); `condition_score` dimensionless 0–5 |
| 8 | **Accuracy statement** | The publisher's statement, quoted; "none supplied" if absent | None — synthetic; no positional accuracy claimed |
| 9 | **Licence / usage conditions** | Quoted or linked; what you may and may not do | Training use only |
| 10 | **Known omissions** | What the publisher says is missing or excluded | TR-0301 never inspected; no assets recorded in the far east of Ward B |
| 11 | **Data class** (7.6.2) | Measured / digitized / geocoded / derived / simulated | Simulated |
| 12 | **Processing history** | For derived data: inputs, operations, parameters, software, date | Not applicable (primary synthetic fixture) |
| 13 | **Encoding, delimiter, field types** | For text formats | UTF-8; comma; per schema dictionary |
| 14 | **Checks performed on receipt** | Counts, extent, nulls, spot checks (7.7) | See lab deliverable |

Fields 1–10 are the blueprint's required list; 11–14 are added because the modules that follow need them. If a field cannot be filled, write "unknown" and the question you sent to the publisher. An empty cell is the one unacceptable value.

**Where formats and products help.** ArcGIS Pro keeps metadata with the item ("in the geodatabase for geodatabase items … on the file system for file-based items") and by default shows an *item description* that "fits on one page" and "is visible on the item details page when published to ArcGIS Online or an ArcGIS Enterprise portal"; other metadata styles expose more; and only metadata "in the ArcGIS format" can be edited there, with import paths for ISO and FGDC records [X29]. QGIS's layer *Metadata* tab covers identification, categories, keywords, access (licences, rights, constraints), extent, contacts, links, and history, with a validation summary, and can save to a `.qmd` file beside the data or into a GeoPackage's internal metadata tables [X21]. The GeoPackage standard itself provides metadata tables and a metadata reference table linking metadata records to tables and rows [X01, §2.4, F.8]. Use whichever the container supports — and *also* keep the intake form as plain text in the package, because the readme survives every conversion.

### 7.6.2 Measured, digitized, geocoded, derived, simulated — and the processing history

The **data class** tells a reader how the geometry and values came to exist, which bounds what they can be trusted for.

| Class | How it came to exist | Typical accuracy evidence | Municipal example |
| --- | --- | --- | --- |
| **Measured** | Observed with an instrument at the place (survey, GNSS receiver, sensor) | Instrument specification, survey report | Streetlight positions captured with a GNSS receiver during an inventory |
| **Digitized** | Traced by a person from a map, image, or plan | Source scale and image resolution; the operator's tolerance | Ward boundaries traced from a scanned notification map |
| **Geocoded** | A position computed from text (an address or place name) by a matching process | Match score, locator version, reference data date | Requests placed from the address a caller gave |
| **Derived** | Computed from other datasets by an operation | The processing history: inputs, operation, parameters | "Requests within 300 m of R1" — Chapter 1's list |
| **Simulated** | Invented for a purpose (training, testing, modelling) | None claimed; the purpose statement is the only evidence | Every fixture in this course |

Why the classes matter: a *geocoded* request point that appears to sit on a footpath does not mean the caller stood there — it means the address matched a point along a street reference; a *digitized* boundary is only as precise as the source map's line width at its scale; a *derived* result inherits the weakest of its inputs *and* the choices of its operation (planar or geodesic distance, boundary-inclusive or strict — Chapters 6 and 10). Mixing classes in one layer without a field that records the class is a common, invisible defect.

**Processing history for derived outputs (required).** A derived dataset must carry, in its metadata or readme: the input datasets with their own intake references; the operation and every parameter (for example "distance ≤ 300 m, boundary inclusive, planar on the training grid"); the software and version; the date; and the person. The test is reproducibility: another person following the history from the same inputs must get the same output. Chapter 1's answer `{P1, P3, P5}` was reproducible only because the rules were written down, and Chapter 6's 6.8 log — input CRSs, transformation record, analysis CRS, measurement method, units, rounding, software version — is a complete processing history for a derived measurement. Keep that log with the outputs it describes; it is the Chapter 7 metadata example Chapter 6 promised.

### 7.6.3 Source suitability: the old-boundary mismatch, worked

"Official" describes *who issued* a dataset. It says nothing about *when* it was true or *what* it is fit for. The blueprint's scenario makes the failure visible with numbers.

**Setup (synthetic).** A colleague finds `wards_official.shp` on a shared drive and uses it to count current requests per ward. Its `.shp.xml` metadata, once opened, says the boundaries are the **2019 delimitation**: Ward A was then the rectangle with corners (0, 0), (1300, 0), (1300, 1000), (0, 1000), and Ward B was (1300, 0)–(2000, 1000). In **2024** the boundary moved to x = 1000 (the Chapter 1 fixture). The requests are from August–September 2026 (Chapter 1, Table F3).

**Counting with the wrong boundary (strict-interior membership, hand-checked).**

| Request | (x, y) | 2019 boundary (x = 1300) | Current boundary (x = 1000) |
| --- | --- | --- | --- |
| P1 | (200, 200) | A | A |
| P2 | (800, 800) | A | A |
| P3 | (1200, 250) | **A** (1200 < 1300) | **B** (1200 > 1000) |
| P4 | (1700, 900) | B | B |
| P5 | (1000, 500) | **A** (1000 < 1300, strictly inside) | **Boundary** (x = 1000 exactly; policy needed) |
| P6 | (2200, 500) | outside | outside |

Totals: with the 2019 file, Ward A = 4 (P1, P2, P3, P5), Ward B = 1 (P4). With the current file, Ward A = 2, Ward B = 2, and P5 needs the explicit boundary policy Chapter 1 discussed. Both counts are arithmetically correct *for their inputs*; only one answers "how many current requests fall in each current ward". The report built on the old file over-counts Ward A by two and gives P5 to A without anyone deciding so.

**How the intake form would have caught it.** Field 4 (edition/date of observation: 2019) against the question's date (2026); field 5 (coverage) showing an x-range that does not match the current wards; field 10 (known omissions: "superseded by 2024 delimitation" if the publisher was careful). Field 1 ("official") is the field that *cannot* catch it. A dataset is suitable for a question when its *content date, coverage, class, accuracy, and licence* all satisfy the question's needs; authority is not on the list.

**Generalising beyond wards.** The same mismatch appears as: a road network from before a bypass opened used for routing; a 2015 land-cover raster used to explain 2026 flooding; a geocoded request table whose locator used an older street reference. The question to ask of any source is "as of when, and for what purpose, is this true?"

**Comprehension check 7.6.** Classify each as measured, digitized, geocoded, derived, or simulated, and name the one piece of evidence you would demand for each: (a) drain positions traced from a 1:5,000 plan; (b) request points placed from callers' addresses; (c) Table F7; (d) a "priority zone" polygon produced by buffering R1 by 300 m.

---

## 7.7 Design conversion checks

### 7.7.1 Before: snapshot the properties that can change

A conversion check is a small experiment: record the source's properties, convert, record the output's properties, compare. The "before" snapshot must be taken from the *source as your tool reads it*, not from the readme, because the importer's interpretation (7.2.1) is already the first conversion.

**Table 7.7a — "Before" snapshot for Table F7 (source: `assets_ch7.csv` as documented).**

| Property | How to read it | Value for F7 |
| --- | --- | --- |
| Schema | Field list with declared/detected type and length | 10 fields — see schema dictionary; expect a detected type for `legacy_code` and `last_inspection_at` to be recorded here as *observed* |
| Record count | Table row count | 6 |
| Geometry type(s) | From the importer, or "none — coordinates in columns" | Point (after XY import); one type |
| CRS | Data source properties | None — local training grid (documented); the importer may show "Unknown" or the local reference you assign |
| Extent | Min/max of x and y | x 205–2190; y 195–950 |
| Null counts per field | Count of nulls/empties per field | `condition_score` 1; `last_inspection_at` 1; `inspector_note` 1 empty; others 0 |
| Unicode text samples | Copy two values verbatim | `સ્ટ્રીટલાઇટ` (SL-0113), `Silt build-up — ગટર ભરાયેલી છે` (DR-0042) |
| Identifier samples | Copy the leading-zero code verbatim | `legacy_code` = `0113` for SL-0113, `0007` for BN-0007 |
| Date/time samples | Copy verbatim with offset | `2026-08-30T02:10:00+05:30` (SL-0055), `2025-11-02T15:05:00+05:30` (DR-0042) |
| Numeric samples and a summary | Copy one value; compute one statistic by hand | `condition_score` 3.5 (SL-0113); mean of non-null = 3.2 |
| Known records | Three records to re-find afterwards, by business key | SL-0113, TR-0301 (the null case), SL-0055 (the date-shift case) |

The choice of "known records" is deliberate: pick the row that is ordinary, the row with nulls, and the row whose timestamp crosses midnight in UTC. If the ordinary row survives and the other two do not, you learn exactly which rule bit.

### 7.7.2 After: compare, and separate intentional change from accidental loss

Take the same snapshot from the output and lay the two side by side. Every difference goes into one of two columns:

| Column | Definition | F7 examples (predicted by format rules; to be observed) |
| --- | --- | --- |
| **Intentional adaptation** | A change you decided on, because the target format or the recipient requires it, and which you can describe and reverse or explain | GeoPackage stores `last_inspection_at` as UTC with `Z` (offset removed, instant preserved) [X01]; shapefile field names shortened to ten characters with a documented mapping [S20]; a text `legacy_code` deliberately kept as text |
| **Accidental loss** | A change you did not decide on and cannot justify to the recipient | Null `condition_score` became 0 [S20]; `legacy_code` became 113 because a type was guessed; Gujarati became `????`; the date of SL-0055's inspection moved a day without anyone noting the UTC conversion; a record dropped because its geometry was empty |

The same physical change can sit in either column: "time dropped from `last_inspection_at`" is an *adaptation* if the recipient only needs dates and the readme says so, and a *loss* if nobody noticed. The difference is documentation and consent.

**Figure 7.2 — The comparison flow (schematic).**

```
   SOURCE  ──(import as tool reads it)──►  BEFORE snapshot  ─┐
                                                              │  compare property by property
   OUTPUT  ──(open the written file)────►  AFTER snapshot   ─┘
                                                              ▼
                       ┌──────────────────────────┬──────────────────────────┐
                       │ Intentional adaptation   │ Accidental loss          │
                       │ (documented, justified)  │ (fix, re-export, or      │
                       │                          │  declare and warn)       │
                       └──────────────────────────┴──────────────────────────┘
```

**Spot checks on known records.** For each of the three known records, open the output and read every field. Do not rely on counts alone: six rows in and six rows out says nothing about whether TR-0301's null became zero.

### 7.7.3 Preserve the original, name outputs meaningfully, and distrust the green message

Three working rules:

1. **Never convert in place.** The original is the only evidence of what the data looked like before your tool touched it. Keep it read-only, alongside its intake form.
2. **Name outputs for their content and purpose, not their step.** `assets_ch7_shp_for_contractorX_2026-09-19` tells a reader what it is, who it was for, and when; `export1` and `final_final` do not. Include the target format's constraints in the readme that travels with the output (for example the field-name mapping).
3. **A "completed" message is not a semantic validation.** The tool verified that it could write a structurally valid file. It did not verify that `0` still means "not assessed", that `legacy_cod` is the field a downstream join expects, or that a reader with a different code page will see Gujarati. Only the before/after comparison verifies meaning, and only a person can decide which column a change belongs in.

**Developer analogy — and where it stops.** This is a data-migration test: snapshot, migrate, diff, classify diffs. The analogy is good. It stops at *silent substitution*: in most application migrations a lost null raises an error or a failed constraint, whereas a shapefile writer replaces it with a legal-looking value and reports success [S20]. GIS conversions must be tested for what they *changed*, not only for what they *rejected*.

**Comprehension check 7.7.** After exporting F7 to a shapefile, your comparison shows six records, ten fields, and no error. Name three specific properties that could still have changed, and the single snapshot line that detects each.

---

## 7.8 Guided lab: select and test an exchange format

### 7.8.1 Objective, prerequisites, and what this lab is (and is not)

**Objective.** Admit Table F7 through an intake form, choose an exchange format for a named recipient, perform the conversion, and produce a before/after comparison that separates intentional adaptation from accidental loss. A second, deliberately lossy export (shapefile) is made as a control so that you see loss happen on your own machine.

**Prerequisites.** Modules 7.1–7.7 read; Chapter 5's CRS checklist and Chapter 6's assignment-versus-transformation distinction understood; ability to create a UTF-8 text file.

**What this lab is not.** It is not execution-tested by the author (see Document metadata). The *expected results* in 7.8.6 are predictions from the cited format rules. Where two products may behave differently, the text says so, and your deliverable must report what *your* software did — that is the point of the exercise, and it is what the instructor will mark.

### 7.8.2 Required software and access

- **Primary route:** ArcGIS Pro 3.x (documentation checked at 3.7). No extension, no ArcGIS Online sign-in, no credits. The project's default file geodatabase is used as working storage.
- **Alternative route (7.8.10):** QGIS Desktop 3.44 (which uses GDAL/OGR for file formats).
- **Both routes:** a text editor that can save UTF-8 and show the encoding; optionally the `sqlite3` command-line tool or any SQLite browser for inspecting the GeoPackage.
- **No route available:** the paper route in 7.8.8.

### 7.8.3 Input data — create it exactly as printed (synthetic)

Create a folder `ch7_lab` and inside it a subfolder `00_original` that you will treat as read-only after this step.

**File 1 — `00_original/assets_ch7.csv`.** Save as **UTF-8** with a comma delimiter. Copy the text exactly; the leading-zero codes are quoted on purpose.

```
asset_id,legacy_code,asset_type_en,asset_type_gu,installed_year,condition_score,last_inspection_at,inspector_note,x,y
SL-0113,"0113",Streetlight,સ્ટ્રીટલાઇટ,2018,3.5,2026-03-14T10:42:00+05:30,Lamp flickers at dusk,205,195
DR-0042,"0042",Drain,ગટર,2011,1.5,2025-11-02T15:05:00+05:30,"Silt build-up — ગટર ભરાયેલી છે",995,510
TR-0301,"0301",Tree,વૃક્ષ,2005,,,,2190,520
BN-0007,"0007",Bench,બાંકડો,2022,5.0,2026-06-01T09:00:00+05:30,Repainted,1500,300
DR-0110,"0110",Drain,ગટર,2015,2.0,2026-01-20T11:30:00+05:30,Grate missing,400,700
SL-0055,"0055",Streetlight,સ્ટ્રીટલાઇટ,2020,4.0,2026-08-30T02:10:00+05:30,Night check: lamp OK,1800,950
```

**File 2 — `00_original/README_assets_ch7.md`.** Copy the schema dictionary and the filled intake form (Table 7.6) from this chapter into it, and add the line: *"Coordinates are on a local training grid in metres with no Earth reference. Do not assign an Earth CRS. Any reference assigned for software convenience must be recorded here as 'assigned for this exercise only'."*

**File 3 (ArcGIS Pro route) — `00_original/schema.ini`.** ArcGIS Pro honours this file to fix field types [X16] [X17]. Create it in the same folder as the CSV:

```
[assets_ch7.csv]
Format=CSVDelimited
ColNameHeader=True
CharacterSet=ANSI
Col1=asset_id Text
Col2=legacy_code Text
Col3=asset_type_en Text
Col4=asset_type_gu Text
Col5=installed_year Long
Col6=condition_score Double
Col7=last_inspection_at Text
Col8=inspector_note Text
Col9=x Double
Col10=y Double
```

`last_inspection_at` is declared `Text` on purpose: the ISO string with offset is then preserved verbatim on import, and its conversion to a real date/time type becomes a *visible, chosen* step rather than a guess. **Verification item (instructor):** Microsoft's `schema.ini` reference lists `CharacterSet` values `ANSI` and `OEM` [X17]; whether ArcGIS Pro reads a UTF-8 CSV correctly with this file present, and whether a byte-order mark helps or hurts, must be checked in the installed version and recorded (I.8). If Gujarati appears wrong, remove the `CharacterSet` line, retry, and record both outcomes.

**File 3 (QGIS route) — `00_original/assets_ch7.csvt`** (optional; QGIS honours it [X19], and GDAL defines it [X23]). One line:

```
"String","String","String","String","Integer","Real","String","String","Real","Real"
```

### 7.8.4 Choose a recipient

Your instructor assigns one recipient, or you choose one and justify it. Each has different needs; that is what decides the format.

| Recipient | Their statement | Their real requirement (ask!) |
| --- | --- | --- |
| **R-A — contractor** | "Our old desktop tool only reads shapefiles." | Points, IDs, and types; they will not use timestamps; they read English only |
| **R-B — web developer** | "Give me GeoJSON for the prototype." | Draw six points on a *schematic* grid in a browser, no basemap; needs the Gujarati labels |
| **R-C — internal GIS team** | "One file for the field tablet with everything intact." | Full fidelity: nulls, timestamps with their instant, both scripts, long names; single-user edits |

Before converting, write your **format decision** in the deliverable: the recipient, the format chosen, the alternatives rejected, and — using Table 7.4 — the properties of F7 you *expect* the format to adapt or lose.

### 7.8.5 Ordered steps — ArcGIS Pro route (not execution-tested; interface labels from the cited pages)

**Part 1 — Intake (no software).**
1. Fill the intake form (Table 7.6) for `assets_ch7.csv` in your deliverable. Fill every field; write "unknown" where you must, never leave a blank.
2. Read the CSV in your text editor. Confirm: UTF-8 in the status bar; ten header names; six data rows; the TR-0301 row has three consecutive empty fields.

**Part 2 — Load the source as ArcGIS Pro reads it.**
3. Create a new ArcGIS Pro project `ch7_lab.aprx` in `ch7_lab`. Its default file geodatabase is created with it.
4. On the **Map** tab, click **Add Data** and add `00_original/assets_ch7.csv`. It appears as a standalone table in the Contents pane.
5. Open the table and its fields view. Record, in your "before" snapshot (Table 7.7a layout), the **detected type** of every field and the displayed value of `legacy_code` for SL-0113 and `last_inspection_at` for SL-0055. Esri states that geoprocessing tools read exactly what the table view shows [X15], so this is your true "before".
   - If `legacy_code` shows `113` (not `0113`), the `schema.ini` was not honoured: check it is in the same folder and named exactly `schema.ini`, remove and re-add the table, and record what fixed it.
6. Open the **Geoprocessing** pane (Analysis ▸ Tools) and run **XY Table To Point** [X14]: *Input Table* = the CSV table; *Output Feature Class* = `assets_ch7_src` in the project geodatabase; *X Field* = `x`; *Y Field* = `y`; *Coordinate System* = **the local/engineering reference your instructor designates for the training grid** (recorded in the intake form as "assigned for this exercise only; not an Earth location"). Do **not** accept the tool's default, which Esri documents as WGS 84 [X14] — that would relabel metre grid values as degrees, Chapter 6's relabelling error.
   - **Verification item (instructor):** confirm in the installed version whether the *Coordinate System* parameter can be left empty to produce an unknown-CRS feature class, or whether a custom local projected coordinate system must be defined first; document the chosen reference in the instructor pack (I.6).
7. Add `assets_ch7_src` to the map, open its attribute table, and complete the "before" snapshot: count = 6; geometry type = Point; extent (Layer Properties ▸ Source) = x 205–2190, y 195–950; nulls per field; the three known records copied verbatim. This feature class is the *working source* for every export below; `00_original` stays untouched.

**Part 3 — Convert for your recipient.**
8. **R-C (GeoPackage):** run **Create SQLite Database** with *Spatial Type* = **GeoPackage 1.4** (or the latest version the tool offers) to create `ch7_lab/10_outputs/assets_ch7_R-C.gpkg` [X27]. Then run **Export Features** with *Input Features* = `assets_ch7_src` and the *Output Feature Class* placed inside the `.gpkg` with the name `assets_ch7` [X28]. Leave the field map unchanged so that every field is carried across. (**Verification item:** the instructor confirms in the installed version that *Export Features* accepts a `.gpkg` output location; if another tool is required, the instructor pack names it.)
9. **R-B (GeoJSON):** run **Features To JSON** with *Input Features* = `assets_ch7_src`, *Output JSON* = `10_outputs/assets_ch7_R-B_gridJSON_nonstandard.geojson`, **Output to GeoJSON** checked, and **Project to WGS84 unchecked** (there is no Earth reference to project from) [X24]. Open the file in your editor: expect a `crs` member that Esri says "is not fully supported under the GeoJSON specification" [X24]. Your readme for R-B must state that this file is GeoJSON *syntax* under a **prior arrangement** (RFC 7946 §4 [S18]), that its coordinates are grid metres, and that it must not be passed to any other consumer.
10. **R-A (shapefile) — everyone does this one as the control:** run **Export Features** with *Output Feature Class* = `10_outputs/assets_ch7_R-A_shp/assets_ch7.shp` (a folder path with a `.shp` name produces a shapefile [S20]).

**Part 4 — "After" snapshot and comparison.**
11. Add each output to the map. For each, open the attribute table and the fields view and fill an "after" column beside your "before" snapshot: field names and types, count, geometry type, CRS as reported, extent, nulls per field, the two Unicode samples, the identifier sample, the date/time samples, and the three known records.
12. For the GeoPackage, additionally open it outside ArcGIS if you can (`sqlite3 assets_ch7_R-C.gpkg` then `SELECT table_name, data_type, srs_id FROM gpkg_contents;` and `SELECT srs_id, organization, organization_coordsys_id FROM gpkg_spatial_ref_sys;`) and record what `srs_id` your export used [X01, Req 11, 13].
13. In the folder `assets_ch7_R-A_shp`, list the companion files present. Note whether a `.prj` and a `.cpg` were written and what the `.cpg` contains.
14. Classify every difference as **intentional adaptation** or **accidental loss** (7.7.2). For each accidental loss, either fix it (re-export with a different option and repeat steps 11–14) or declare it in the recipient readme with a warning.

**Part 5 — Package.**
15. Write `10_outputs/README_<recipient>.md`: recipient, format, the field-name mapping if any names changed, every adaptation, every declared loss, the CRS statement, the retrieval/processing date, and your name. Confirm `00_original` is unchanged (compare file sizes and open the CSV once more).

### 7.8.6 Expected results and validation checks

These are **predictions from the cited format rules**, not observed results. Your comparison table must show the observed value next to each prediction. Disagreement with a prediction is not a failure; an *unexplained* disagreement is.

| Property | Expected in GeoPackage (R-C) | Expected in GeoJSON-syntax file (R-B) | Expected in shapefile (R-A control) | Rule |
| --- | --- | --- | --- | --- |
| Record count | 6 | 6 | 6 | — |
| Field names | Unchanged | Unchanged | Shortened to ≤ 10 characters; the two `asset_type_*` names must be disambiguated by the writer — record the exact names you got | [S20] [X02] |
| `legacy_code` for SL-0113 | `0113` (text) | `"0113"` (string) | `0113` if the source field was text; `113` if a numeric type slipped in at step 5 | [X16] |
| `condition_score` for TR-0301 | NULL | `null` | **0** (or another substitute) — a *loss* | [S20] |
| `last_inspection_at` for SL-0055 | If stored as text: unchanged. If converted to a date/time type before export: expect UTC `2026-08-29T20:40:00.000Z` — an *adaptation*, note the date change | String as stored | If text: unchanged (text field). If a date type: date only, time lost; which date depends on whether UTC conversion happened first | [X01, Table 1] [S20] |
| `asset_type_gu` for SL-0113 | `સ્ટ્રીટલાઇટ` | `સ્ટ્રીટલાઇટ` | Intact only with a Unicode code page written and read; otherwise garbled — record the `.cpg` content | [X01] [S20] [X02] |
| `inspector_note` for TR-0301 | Empty string or NULL — record which; either is acceptable if the readme says so | `""` or `null` | A single space if the source was null [S20] | [S20] |
| CRS reported | The reference assigned in step 6, registered in `gpkg_spatial_ref_sys`; the standard's `-1` slot is the honest value for an undefined Cartesian system if the writer uses it | A `crs` member — non-standard; labelled | A `.prj` describing the assigned reference, or no `.prj` (unknown) — record which | [X01, Req 11] [X24] [S20] |
| Extent | x 205–2190, y 195–950 | same numbers | same numbers | — |
| Mean of `condition_score` (non-null) | 3.2 | 3.2 | If nulls became 0 and you average all six: ≈ 2.67 — the visible symptom of the null loss | fixture |

**Validation checks (must all pass before you package):**
- `00_original/assets_ch7.csv` is byte-identical to what you typed (open it; size unchanged).
- Every "after" cell has an observed value; none says "probably".
- Every difference is in exactly one of the two columns, with a one-line justification.
- The recipient readme names every field whose name changed, with old → new.
- The R-B readme contains the words "prior arrangement" and "not standard RFC 7946".

### 7.8.7 Troubleshooting

| Symptom | Likely cause | What to do |
| --- | --- | --- |
| Gujarati shows as `?` or boxes in the ArcGIS Pro table | The text workspace read the file in a non-UTF-8 code page; the `CharacterSet` line, or the absence/presence of a BOM | Try without the `CharacterSet` line; try saving the CSV with a UTF-8 BOM; record which combination worked (this is the instructor's verification item) |
| `legacy_code` shows `113` | Field typed as a number by the importer | Confirm `schema.ini` name and location; confirm the values are quoted; re-add the table |
| `condition_score` shows text type | The empty value or the decimal point confused detection | `schema.ini` `Col6=condition_score Double`; check the decimal symbol of your system locale |
| XY Table To Point rejects or warns about coordinates | Coordinate System left at WGS 84 and values outside ±400 are treated as invalid [X14] | Set the designated local reference (step 6); never "fix" the numbers |
| Points draw somewhere on Earth, over a basemap | WGS 84 was accepted at step 6 — the grid metres were relabelled as degrees | Delete the output; rerun step 6 with the designated local reference; record the mistake in your log as a Chapter 6 relabelling error |
| Export Features into the `.gpkg` fails or produces `.sqlite` | The database was created with *Spatial Type* ST_Geometry/SpatiaLite instead of GeoPackage [X27] | Recreate with *GeoPackage 1.4*; the output extension must be `.gpkg` [X26] |
| Shapefile export warns about field names | Expected: names longer than 10 characters | Record the mapping the tool chose; do not rename source fields to "avoid" the warning |
| GeoJSON output has no `crs` and coordinates look like degrees | *Project to WGS84* was checked; a transformation from the assigned local reference was attempted | Uncheck it; the grid cannot be placed on Earth |

### 7.8.8 Paper route (no software available)

Complete Parts 1, the format decision (7.8.4), and the *predicted* "after" column of 7.8.6 for your recipient and for the shapefile control, citing the rule for each prediction. Then write the recipient readme as if the predictions were observed, marking each line "predicted, not observed". This route earns full marks for reasoning and documentation but cannot earn the verification marks (7.8.9); complete the software steps when a machine is available.

### 7.8.9 Required learner deliverables

1. **Intake form** for `assets_ch7.csv` (Table 7.6 layout, all 14 fields).
2. **Format decision** (recipient, format, rejected alternatives, expected adaptations/losses from Table 7.4).
3. **Before/after comparison** (Table 7.7a layout with an "after" column per output, predictions from 7.8.6 alongside observed values, and the adaptation/loss classification with justifications).
4. **Recipient readme** for your chosen recipient, plus the shapefile control readme.
5. **Software log**: product and version, tool names used, the coordinate reference assigned at step 6 and why, and any deviation from the printed steps.
6. Statement that `00_original` is unchanged.

A screenshot of a map is not a deliverable; a screenshot of an attribute table may accompany item 3 but does not replace the typed values.

### 7.8.10 QGIS alternative (equivalent foundational exercise; not execution-tested; labels from the QGIS 3.44 manual and GDAL driver pages)

The concept is identical; the tools and the products' defaults differ, which is why observed results must be recorded rather than copied.

1. **Project setting.** Project ▸ Properties ▸ CRS: choose **No CRS (or unknown/non-Earth projection)**. The manual states this makes "all layers and map coordinates to be treated as simple 2D Cartesian coordinates, with no relation to positions on the Earth's surface" [X35] — the honest setting for the training grid. Record it in the intake form.
2. **Load the CSV.** Open the Data Source Manager, **Delimited Text** tab; file `assets_ch7.csv`; *File format* = CSV; *First record has field names* on; *Detect field types* on; check the *Sample data* preview and **change `legacy_code` to Text if it was detected as a whole number**; *Geometry definition* = Point coordinates, *X field* = `x`, *Y field* = `y`; *Geometry CRS*: follow the instructor's designation for the training grid (see the verification item below); click **Add** [X19]. If you shipped the `.csvt`, note whether the detected types match it.
   - **Verification item (instructor):** what the *Geometry CRS* widget offers for a non-Earth layer in QGIS 3.44 (the CRS selector's unknown/non-Earth choice, or a designated custom CRS), and what the layer's *Source* tab then reports.
3. **"Before" snapshot.** Layer Properties ▸ *Fields* (types), *Information* (count, extent, CRS, encoding); open the attribute table and copy the three known records.
4. **Convert.** Right-click the layer ▸ **Export ▸ Save Features As…** [X20]:
   - **R-C:** *Format* = GeoPackage; *File name* = `assets_ch7_R-C.gpkg`; *Layer name* = `assets_ch7`; *Encoding* = UTF-8; tick **Persist layer metadata** (stored "in the newly created layer, if the output is of GeoPackage format" [X20]).
   - **R-B:** *Format* = GeoJSON; in *Layer Options* look for `RFC7946` — leaving it at its default `NO` writes the 2008-style file [X22]; since the data has no Earth reference, do **not** set it to YES (that would attempt reprojection to WGS 84 [X22]); label the output non-standard exactly as in the Pro route.
   - **R-A (control):** *Format* = ESRI Shapefile; *Encoding* = UTF-8 (GDAL writes the `.cpg` accordingly [X02]); observe the field-name warning/renaming; then repeat once with the default encoding and compare the Gujarati fields — record both.
5. **"After" snapshot and comparison** exactly as in the Pro route, including the `sqlite3` queries on the GeoPackage and the companion-file listing for the shapefile.
6. **Expectation differences to watch (QGIS/GDAL versus ArcGIS Pro):** GDAL's duplicate-name rule ("truncated to 8 characters and appended with a serial number" [X02]) may give different shapefile names from ArcGIS Pro; GDAL's null handling on shapefile write is not stated on its page — observe it; the GeoPackage `DATETIME` conversion applies only if the field was typed date/time before export [X01]. None of these differences is a defect; all must be recorded.

---

## 7.9 Independent check and progression gate

Complete this assessment without the Instructor Appendix. State assumptions where a question leaves a choice open; a defensible assumption, stated, is never penalised.

### 7.9.1 Concept questions (six)

**Q1.** [7.1, 7.3, 7.4; LO1] A shared directory contains exactly three things: (a) `wards.shp` — one file, nothing else with that base name; (b) `sites.csv`, whose header is `c1,c2,name` and whose first row is `70.01,20.02,Depot`, with no readme; (c) `assets.gpkg` together with `README_assets.md` containing a completed intake form. For each item, state what evidence or files are **missing** before it can be used, what can be learned from what is present, and — for (c) — what the four layers (model, encoding, container, service) tell you and which three properties even the complete package does not prove.

**Q2.** [7.2.1; LO2] A CSV has the header `Y,X,ward_no,note` and a first row `20.0000,70.0000,007,ठीक है`. (a) Which column is longitude, and what evidence in the file supports your answer? (b) Name two ways `ward_no` can be damaged on import and one way to prevent it in a product of your choice, citing where the product documents the mechanism. (c) What must be true of the file for the note to survive, and what does ArcGIS Online say about it?

**Q3.** [7.2.2; LO2] Explain, in four sentences at most, why a `"crs"` member naming a projected CRS does not make a GeoJSON file compliant with RFC 7946, and describe the one situation in which RFC 7946 permits non-WGS 84 coordinates.

**Q4.** [7.3; LO3] A delivery contains `assets.shp`, `assets.shx`, and `assets.dbf`. (a) Will it open? (b) What is the CRS? (c) After you receive the missing companion file, explain the difference between *adding* it and *writing one yourself*, using the Chapter 6 terms. (d) The `.dbf` shows `cond_score` = 0 for two assets; state what you can and cannot conclude.

**Q5.** [7.4; LO4] Give one property of a dataset that a GeoPackage preserves and a shapefile cannot, one that a file geodatabase preserves and GeoPackage's core standard does not, and one that no file format preserves at all. For the GeoPackage, name the standard's mechanism for declaring that a file uses something beyond the core, and say what a reader should do when it finds one it does not implement.

**Q6.** [7.5; LO5] Three claims about a PostgreSQL server: (i) "It has PostGIS, so it is an enterprise geodatabase." (ii) "Its enterprise geodatabase must be using PostGIS." (iii) "Our hosted feature layers' data is in that enterprise geodatabase because they are both ArcGIS." Mark each true or false, give the sentence from the documentation that decides it, and for (iii) name the product that actually holds hosted-layer data and the only way its content is reached.

### 7.9.2 Scenario questions (two)

**S1.** [7.3, 7.7; LO3, LO7] A contractor will only accept shapefiles for a tree-inventory export. The source is a geodatabase feature class with 14 fields including `species_scientific_name` (text, Unicode), `last_pruned_at` (timestamp offset), `canopy_diameter_m` (double, nulls allowed), and `heritage_status` (coded-value domain). Write the "expected adaptations" and "expected losses" sections of the readme you would send, one line per field affected, citing the rule behind each line. Then state the two before/after checks that would detect the most damaging loss, and name it.

**S2.** [7.6; LO6] A manager's report shows request counts per ward that differ from yours by exactly two. You discover the manager used a ward layer whose metadata says "2019 delimitation", while yours is the 2024 file. Using the fixture in 7.6.3, (a) show which requests moved and why; (b) explain which intake-form fields would have exposed the problem and which field ("official") could not; (c) write the one-sentence rule you would add to the team's data-intake practice to prevent a repeat.

### 7.9.3 Independent practical task (unfamiliar inputs)

**Background (synthetic).** A survey contractor delivers a ZIP named `drop_2026-09-17.zip` for a *new* district the municipality is taking over. The contents:

- `inspections_sept.txt` — tab-separated; header `insp_id	asset_ref	visited	score	remark	Y	X`; rows such as `I-00021	0042	17/09/2026 4:15 PM	4	ठीक है — कोई कार्रवाई नहीं	20.0007	70.0012` (Hindi text; a 12-hour local time with no offset; `asset_ref` with a leading zero; coordinates in two columns named `Y` and `X`, in decimal degrees).
- `district_boundary.shp`, `district_boundary.dbf` — **no `.shx`, no `.prj`**.
- `imagery.tif` — a TIFF with no sidecar files and no explanation.
- An email saying: "Everything is in WGS 84; the boundary is the official one."

**Required work.**
1. Complete an intake form (Table 7.6) for each of the three items, writing "unknown" and the question you would send the contractor wherever the delivery is silent.
2. For `inspections_sept.txt`, list every field that can be damaged on import and the declared type you would force for each; state the coordinate order you would set and the evidence you would demand before accepting "WGS 84"; state how you would handle `visited` (assumption about time zone, and what the `+05:30` alternative would have changed).
3. For `district_boundary.*`, state whether it can be opened, what the missing files each contribute, and what you would and would not do about the missing `.prj` (Chapter 6 terms).
4. For `imagery.tif`, list the seven inspection items of 7.4.2 with, for each, where you would look and what "not present" would mean.
5. Choose an exchange format to forward the inspections to the internal GIS team (R-C) and to a web developer (R-B), justify each from Table 7.4, and write the before/after checklist you would run — three known records by ID, and the properties to snapshot.
6. If software is available, create the inspections file exactly as described (five rows of your own invention following the same patterns, labelled synthetic), perform the two conversions, and submit the comparison with observed values. If not, submit predictions labelled as such (paper route).

**Submission:** intake forms (3), the field-handling table, the boundary and raster notes, the format decisions with checklists, and — if executed — the comparison table and readmes, plus a software log.

### 7.9.4 Oral explanation (one)

In no more than three minutes, using the shapefile companion files or the GeoPackage bookkeeping tables as your only prop, explain to a new colleague why **file format, source authority, access permission, and data quality are four different concerns**, giving one municipal example where each one alone would have misled the team. The examiner will ask one follow-up question at the point where your explanation was thinnest.

### 7.9.5 Submission requirements and scoring

Submit one folder: the 7.8 lab deliverables (7.8.9), written answers to Q1–Q6, S1–S2, the 7.9.3 practical materials, and a note of the oral date. Typed values, not screenshots, are the evidence.

| Component | Weight | What earns the marks |
| --- | --- | --- |
| Concept questions Q1–Q6 | 25% | Correct distinctions with the deciding rule named; citations to the page that establishes a product behaviour |
| Scenario questions S1–S2 | 15% | Predictions tied to rules; arithmetic on the fixture correct; adaptation and loss kept apart |
| Guided lab (7.8) | 25% | Intake form complete; before/after with observed values; every difference classified and justified; original untouched; readmes name every changed field |
| Independent practical (7.9.3) | 25% | Unknowns declared rather than guessed; coordinate order and CRS evidence demanded; format choices argued from the recipient; checklist would actually detect the losses |
| Oral | 10% | Four concerns kept distinct with one example each; a wrong follow-up answer that is then corrected on reflection still passes |

**Progression gate.** Suggested pass threshold 80% overall (blueprint Appendix B.3). Regardless of total, a learner does **not** progress who: silently guesses a CRS or coordinate order; accepts a tool's "completed" message as proof of preserved meaning; cannot select a defensible format for a stated recipient; or cannot list what must be checked after an exchange. These are critical misconceptions and require remediation and a fresh exercise (Instructor Appendix I.5).

---

## 7.10 Media and source brief

The full media brief — slide outline, audio-lesson outline, and diagram specifications — is in the Media Appendix so it can be updated independently of the teaching text. This module records the three requirements the blueprint places on it.

1. **A format comparison table and an illustrated package inspection.** The table is Table 7.4; the package inspection is Figure 7.1 (shapefile companions) with a parallel panel for a GeoPackage's bookkeeping tables (Media Appendix, D2). Narration explains the *purpose* of each component — geometry, index, attributes, CRS note, encoding note — and never recites extensions one after another.
2. **No 3D scene.** Nothing in this chapter is spatial in a way a 3D scene would clarify. The most useful visual is the **before/after schema and record comparison** (Media Appendix, D3), which shows Table F7's three known records passing through a shapefile export with each change colour-coded as adaptation or loss.
3. **Sources.** GeoJSON: [S18]; GeoPackage: [S19] and the 1.4.0 standard text [X01]; shapefile: [S20] (with [X02] for the GDAL implementation); geodatabases: [S21] and [X04]–[X09]; raster context: [S11] with [X12]–[X13]; the CSV, JSON, GeoPackage, and metadata product pages [X14]–[X31], [X35].

**Transition to Chapter 8.** You can now tell what a container *can* hold and check what it *did* hold. Chapter 8 designs what *should* go inside: entities, identifiers, attribute types, controlled values, and the relationships between assets, inspections, and requests — the information model that Table F7's flat row only hints at.

---

## Glossary

Terms are listed in the order they first appear. Platform-specific terms are marked **[ArcGIS]**, **[QGIS/GDAL]**, or **[Standard]** (defined by a published specification); unmarked terms are general.

| Term | Meaning in this chapter |
| --- | --- |
| **Data model** | The way the world is represented: vector (features) or raster (grid of cells). |
| **Encoding** | How one dataset is written down as bytes or text (CSV, GeoJSON, `.shp` records, TIFF tags). |
| **Container** | A file, folder, or database that holds one or more datasets with shared bookkeeping (GeoPackage, file geodatabase, enterprise geodatabase). |
| **Service** | A network endpoint through which other machines request data or map images under the provider's rules (Chapter 2). |
| **Coordinate CSV** | A delimited text table with coordinate columns; carries no CRS, order, or types inside the file. |
| **`schema.ini`** **[ArcGIS]** | A Microsoft ODBC text-driver file that ArcGIS Pro honours to declare delimited-file field types and format [X16] [X17]. |
| **`.csvt`** **[QGIS/GDAL]** | A one-line sidecar declaring CSV column types, read by GDAL and QGIS [X19] [X23]. |
| **GeoJSON** **[Standard]** | JSON format for geographic data defined by RFC 7946; positions are longitude, latitude (WGS 84 decimal degrees) [S18]. |
| **Geometry / Feature / FeatureCollection** **[Standard]** | The three GeoJSON object kinds: a shape; a shape with properties; a list of features [S18, §3]. |
| **Linear ring** **[Standard]** | A closed LineString of four or more positions bounding a polygon area; exterior rings counterclockwise, holes clockwise in GeoJSON [S18, §3.1.6]. |
| **Prior arrangement** **[Standard]** | RFC 7946's condition under which parties may exchange non-WGS 84 coordinates in GeoJSON syntax [S18, §4]. |
| **Foreign member** **[Standard]** | A JSON member not defined by RFC 7946, permitted in a GeoJSON document [S18, §6.1]. |
| **Shapefile** | Esri's multi-file vector format: `.shp` geometry, `.shx` index, `.dbf` attributes, optional `.prj` (CRS), `.cpg` (code page), and index/metadata files [S20] [X02] [X03]. |
| **dBASE (`.dbf`)** | The 1980s table format holding shapefile attributes; source of the 10-character names, no-null, date-only, and code-page limits [S20]. |
| **Code page / `.cpg`** | The character encoding declared for a `.dbf`'s text; read from `.cpg` or the dBASE header (LDID) [X02] [X03]. |
| **Null substitution** | A shapefile writer's replacement of null with 0, a space, or a zero date [S20]. |
| **GeoPackage** **[Standard]** | An OGC standard container: an SQLite 3 database with `gpkg_*` bookkeeping tables holding features, tiles, and attributes [S19] [X01]. |
| **`gpkg_contents`, `gpkg_spatial_ref_sys`, `gpkg_extensions`** **[Standard]** | The GeoPackage tables listing datasets, coordinate reference systems (including `-1` undefined Cartesian), and extensions in use [X01]. |
| **Extended GeoPackage** **[Standard]** | A GeoPackage using one or more registered extensions [X01]. |
| **GeoTIFF** **[Standard]** | A TIFF 6.0 file carrying georeferencing and CRS tags (ModelTiepoint, ModelPixelScale, ModelTransformation, GeoKeyDirectory) [X12]. |
| **World file** | A small text sidecar (`.tfw`, `.wld`) giving raster placement when the image itself has none [X13] [S11]. |
| **NoData tag** | A non-standard TIFF tag GDAL uses to store a band's NoData value [X13]. |
| **Geodatabase** **[ArcGIS]** | Esri's physical store plus information model for geographic datasets [S21]. |
| **File / mobile / enterprise geodatabase** **[ArcGIS]** | Geodatabases stored, respectively, in a `.gdb` folder, a `.geodatabase` SQLite file, and a relational DBMS [X04]. |
| **Feature class** **[ArcGIS]** | A collection of features with the same geometry type, attributes, and spatial reference [X05]. |
| **Table** **[ArcGIS]** | A nonspatial dataset of rows and columns [X05]. |
| **Feature dataset** **[ArcGIS]** | A group of related feature classes sharing a coordinate system, used for controller datasets such as topology [X06]. |
| **Geodatabase system tables** **[ArcGIS]** | Tables created by *Enable Enterprise Geodatabase* whose presence makes a database a geodatabase in ArcGIS [X07] [X09]. |
| **ST_Geometry** **[ArcGIS]** | Esri's spatial type for databases; one of three spatial types ArcGIS supports in PostgreSQL [X08]. |
| **PostGIS** | Open-source spatial extension to PostgreSQL [S09]. |
| **ArcGIS Data Store** **[ArcGIS]** | The Enterprise application creating system storage (relational, object, spatiotemporal, graph stores) reached only through web layers [X10]. |
| **System storage / user storage** **[ArcGIS]** | Enterprise's distinction between ArcGIS-managed stores and user-managed databases, folders, and cloud stores [X11]. |
| **Intake form** | This course's 14-field record admitting a dataset into a project (Table 7.6). |
| **Measured / digitized / geocoded / derived / simulated** | The five data classes describing how geometry and values came to exist (7.6.2). |
| **Processing history** | The reproducible record of inputs, operations, parameters, software, date, and person for a derived dataset. |
| **Before/after snapshot** | The property list recorded before and after a conversion (Table 7.7a). |
| **Intentional adaptation / accidental loss** | The two classes into which every conversion difference must be sorted (7.7.2). |
| **Item description / metadata style** **[ArcGIS]** | ArcGIS Pro's default one-page metadata view and the selectable fuller styles [X29]. |
| **`.qmd`** **[QGIS/GDAL]** | QGIS's sidecar metadata file for formats without internal metadata storage [X21]. |

## Recap

1. Model, encoding, container, and service are four separate layers; name the layer before you debug an exchange problem. An extension is a claim, not evidence, of accuracy, completeness, freshness, or suitability.
2. A coordinate CSV carries nothing geographic inside it: fields, order, CRS, units, delimiter, encoding, types, and null convention must be declared beside it and verified in the importer, which otherwise guesses. Leading zeros and timestamps are the first casualties.
3. GeoJSON is readable and web-native but has one CRS — WGS 84 longitude/latitude — and a `crs` member does not change that; non-WGS 84 exchange needs a documented prior arrangement. Read a tiny file by eye before loading it.
4. A shapefile is five roles across companion files; `.prj` is optional and its absence means *unknown*, not *none*. The `.dbf` shortens names, substitutes nulls, drops times, and is code-page dependent; the export still says "completed".
5. GeoPackage is SQLite with standard bookkeeping tables and optional extensions — check the core, then check each extension per product; it has an honest slot for undefined Cartesian data and stores date-times as UTC. GeoTIFF is a TIFF with georeferencing tags; every raster still needs its CRS, bands, cell size, value units, NoData, data type, and cell reference inspected.
6. A geodatabase is storage plus an information model: feature classes, tables, feature datasets, and behaviour. PostGIS gives a database spatial types; only the geodatabase system tables make it an enterprise geodatabase; ArcGIS Data Store is ArcGIS-managed storage reached only through web layers.
7. Admit every dataset through the intake form; classify it as measured, digitized, geocoded, derived, or simulated; demand a processing history for derived data; and test suitability by content date, coverage, class, accuracy, and licence — not by who published it.
8. Snapshot before, convert, snapshot after, compare known records, and sort every difference into intentional adaptation or accidental loss. Keep the original; name outputs for content and recipient; treat the green message as structural, never semantic.

## Cross-references to later chapters

- Designing the fields, identifiers, domains, and relationships that a container should hold, and the related `Inspections` table previewed in 7.5.2: **Chapter 8**.
- Detecting and repairing geometry, attribute, and encoding defects found by intake checks, with an audit trail: **Chapter 9**.
- Boundary-inclusive versus strict membership (the P5 policy in 7.6.3) and joins keyed on identifiers such as `legacy_code`: **Chapter 10**.
- Buffers and other derived datasets that require the processing history of 7.6.2: **Chapter 11**.
- Symbology and layer files — the "styling" row of Table 7.4 that no data format carries: **Chapter 12**.
- Service protocols, publishing from geodatabases, Data Store administration, and enterprise geodatabase creation: Phase 2 and the later Enterprise track.

---

## References

All pages were opened and read by the author on **19 September 2026**. IDs [S04], [S09], [S11], [S15], [S18]–[S21] are the blueprint's reference register entries used by this chapter; [X01]–[X35] are additional official pages consulted to support specific claims. Esri "latest" pages carry the version tag 3.7 (ArcGIS Pro) on the check date and change without notice; GDAL "stable" pages track the current release; re-check before reuse.

| ID | Publisher | Page title | URL | Accessed |
| --- | --- | --- | --- | --- |
| S04 | Esri | ArcGIS Enterprise — Introduction to ArcGIS Enterprise on Windows and Linux | https://doc.esri.com/en/arcgis-enterprise/latest/introduction/what-is-arcgis-enterprise-.html | 2026-09-19 |
| S09 | PostGIS Project | PostGIS — home page | https://postgis.net/ | 2026-09-19 |
| S11 | QGIS Project | A Gentle Introduction to GIS — Raster Data (3.44), §6.3 Georeferencing, §6.5–6.6 | https://docs.qgis.org/3.44/en/docs/gentle_gis_introduction/raster_data.html | 2026-09-19 |
| S15 | Esri | ArcGIS Pro — Define Projection (Data Management) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/define-projection.html | 2026-09-19 |
| S18 | RFC Editor / IETF | RFC 7946 — The GeoJSON Format (§3.1.1, 3.1.6, 3.2, 3.3, 4, 6.1, 7, 11.2, 12) | https://www.rfc-editor.org/rfc/rfc7946 | 2026-09-19 |
| S19 | OGC | GeoPackage — official standard site (home page; news of GeoPackage 1.4.0 and the Related Tables Extension) | https://www.geopackage.org/ | 2026-09-19 |
| S20 | Esri | ArcGIS Pro — Geoprocessing considerations for shapefile output | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/appendices/geoprocessing-considerations-for-shapefile-output.html | 2026-09-19 |
| S21 | Esri | ArcGIS Pro — What is a geodatabase? | https://doc.esri.com/en/arcgis-pro/latest/help/data/geodatabases/overview/what-is-a-geodatabase-.html | 2026-09-19 |
| X01 | OGC | OGC GeoPackage Encoding Standard, version 1.4.0 (Requirements 1–3, 5, 10, 11, 13, 29–31, 58–64, 68, 69, 119; Table 1; §2.3–2.4; Annex F) | https://www.geopackage.org/spec140/ | 2026-09-19 |
| X02 | GDAL/OGR Project | GDAL — ESRI Shapefile / DBF vector driver | https://gdal.org/en/stable/drivers/vector/shapefile.html | 2026-09-19 |
| X03 | Esri (legacy ArcMap documentation; ArcMap is a retired product line — used only for the `.cpg` description) | ArcMap — Shapefile file extensions | https://desktop.arcgis.com/en/arcmap/latest/manage-data/shapefiles/shapefile-file-extensions.htm | 2026-09-19 |
| X04 | Esri | ArcGIS Pro — Types of geodatabases | https://doc.esri.com/en/arcgis-pro/latest/help/data/geodatabases/overview/types-of-geodatabases.html | 2026-09-19 |
| X05 | Esri | ArcGIS Pro — Geodatabase dataset types | https://doc.esri.com/en/arcgis-pro/latest/help/data/geodatabases/overview/geodatabase-dataset-types.html | 2026-09-19 |
| X06 | Esri | ArcGIS Pro — Create and manage feature datasets in ArcGIS Pro | https://doc.esri.com/en/arcgis-pro/latest/help/data/feature-datasets/feature-datasets-in-arcgis-pro.html | 2026-09-19 |
| X07 | Esri | ArcGIS Pro — Introduction to enterprise geodatabases in PostgreSQL | https://doc.esri.com/en/arcgis-pro/latest/help/data/geodatabases/manage-postgresql/overview-geodatabases-postgresql.html | 2026-09-19 |
| X08 | Esri | ArcGIS Pro — Spatial types in databases | https://doc.esri.com/en/arcgis-pro/latest/help/data/databases/overview-database-spatial-types.html | 2026-09-19 |
| X09 | Esri | ArcGIS Pro — Enable Enterprise Geodatabase (Data Management) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/enable-enterprise-geodatabase.html | 2026-09-19 |
| X10 | Esri | ArcGIS Enterprise — Introduction to ArcGIS Data Store (the older `enterprise.arcgis.com/en/data-store/latest/...` URL redirects here) | https://doc.esri.com/en/arcgis-enterprise/latest/plan/what-is-arcgis-data-store.html | 2026-09-19 |
| X11 | Esri | ArcGIS Enterprise — Introduction to data storage (system storage versus user storage) | https://doc.esri.com/en/arcgis-enterprise/latest/plan/introduction-to-data-storage.html | 2026-09-19 |
| X12 | OGC | OGC GeoTIFF Standard, version 1.1 (OGC 19-008r4): scope, TIFF 6.0 inheritance, georeferencing tags, GeoKeys, GTRasterTypeGeoKey | https://docs.ogc.org/is/19-008r4/19-008r4.html | 2026-09-19 |
| X13 | GDAL/OGR Project | GDAL — GTiff (GeoTIFF File Format) raster driver | https://gdal.org/en/stable/drivers/raster/gtiff.html | 2026-09-19 |
| X14 | Esri | ArcGIS Pro — XY Table To Point (Data Management) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/xy-table-to-point.html | 2026-09-19 |
| X15 | Esri | ArcGIS Pro — Geoprocessing considerations for delimited files | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/appendices/geoprocessing-considerations-for-delimited-files.html | 2026-09-19 |
| X16 | Esri | ArcGIS Pro — Use an ASCII or text file | https://doc.esri.com/en/arcgis-pro/latest/help/data/tables/add-an-ascii-or-text-file-table.html | 2026-09-19 |
| X17 | Microsoft | Schema.ini File (Text File Driver) — ODBC documentation | https://learn.microsoft.com/en-us/sql/odbc/microsoft/schema-ini-file-text-file-driver | 2026-09-19 |
| X18 | Esri | ArcGIS Online — Comma-separated values files (.csv) | https://doc.arcgis.com/en/arcgis-online/reference/csv-gpx.htm | 2026-09-19 |
| X19 | QGIS Project | QGIS Desktop 3.44 User Guide — Opening Data, §11.1.3.3 Importing a delimited text file | https://docs.qgis.org/3.44/en/docs/user_manual/managing_data_source/opening_data.html | 2026-09-19 |
| X20 | QGIS Project | QGIS Desktop 3.44 User Guide — Creating Layers, §11.2.2 Creating new layers from an existing layer (Save Features As) | https://docs.qgis.org/3.44/en/docs/user_manual/managing_data_source/create_layers.html | 2026-09-19 |
| X21 | QGIS Project | QGIS Desktop 3.44 User Guide — General Tools, §8.6.1 Metadata | https://docs.qgis.org/3.44/en/docs/user_manual/introduction/general_tools.html | 2026-09-19 |
| X22 | GDAL/OGR Project | GDAL — GeoJSON vector driver (RFC7946 and coordinate-precision options; RFC 7946 write support) | https://gdal.org/en/stable/drivers/vector/geojson.html | 2026-09-19 |
| X23 | GDAL/OGR Project | GDAL — Comma Separated Value (.csv) vector driver | https://gdal.org/en/stable/drivers/vector/csv.html | 2026-09-19 |
| X24 | Esri | ArcGIS Pro — Features To JSON (Conversion) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/conversion/features-to-json.html | 2026-09-19 |
| X25 | Esri | ArcGIS Pro — JSON To Features (Conversion) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/conversion/json-to-features.html | 2026-09-19 |
| X26 | Esri | ArcGIS Pro — Work with SQLite databases and GeoPackage files in ArcGIS Pro; and An overview of the To GeoPackage toolset | https://doc.esri.com/en/arcgis-pro/latest/help/data/databases/work-with-sqlite-databases-in-arcgis-pro.html ; https://doc.esri.com/en/arcgis-pro/latest/tool-reference/conversion/an-overview-of-the-to-geopackage-toolset.html | 2026-09-19 |
| X27 | Esri | ArcGIS Pro — Create SQLite Database (Data Management) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/create-sqlite-database.html | 2026-09-19 |
| X28 | Esri | ArcGIS Pro — Export Features (Conversion) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/conversion/export-features.html | 2026-09-19 |
| X29 | Esri | ArcGIS Pro — View metadata | https://doc.esri.com/en/arcgis-pro/latest/help/metadata/view-and-edit-metadata.html | 2026-09-19 |
| X30 | Esri | ArcGIS Pro — Define fields in tables (field name length limits) | https://doc.esri.com/en/arcgis-pro/latest/help/data/geodatabases/overview/defining-fields-in-tables.html | 2026-09-19 |
| X31 | Esri | ArcGIS Pro — ArcGIS field data types (date only, time only, timestamp offset) | https://doc.esri.com/en/arcgis-pro/latest/help/data/geodatabases/overview/arcgis-field-data-types.html | 2026-09-19 |
| X32 | Esri | ArcGIS Pro — Feature class basics | https://doc.esri.com/en/arcgis-pro/latest/help/data/geodatabases/overview/feature-class-basics.html | 2026-09-19 |
| X33 | GDAL/OGR Project | GDAL — ESRI File Geodatabase (OpenFileGDB) vector driver (write support since GDAL 3.6) | https://gdal.org/en/stable/drivers/vector/openfilegdb.html | 2026-09-19 |
| X34 | OGC | OGC GeoTIFF Standard — landing page (version 1.1, document 19-008r4) | https://www.ogc.org/standards/geotiff/ | 2026-09-19 |
| X35 | QGIS Project | QGIS Desktop 3.44 User Guide — Working with Projections (unknown CRS handling; "No CRS (or unknown/non-Earth projection)") | https://docs.qgis.org/3.44/en/docs/user_manual/working_with_projections/working_with_projections.html | 2026-09-19 |

---

# Instructor Appendix (separate from learner material)

Learners should not read this appendix before completing the 7.9 gate.

## I.1 Answers and reasoning — comprehension checks

**7.1.** *Model:* vector (a shapefile holds one geometry type; "ward" implies polygons — but confirm by opening). *Encoding:* the shapefile record layouts (`.shp`/`.dbf`). *Container:* a weak one — a set of companion files in a folder; the email tells you nothing about whether `.shx`, `.dbf`, `.prj`, `.cpg` were attached. *Service:* none; this is a frozen copy. "Official" does not establish the edition date (7.6.3), the accuracy, the licence to reuse, or that this is the *current* delimitation; it only names the issuer.

**7.2.** (a) No. RFC 7946 removed the `crs` member and fixes WGS 84 longitude/latitude [S18, §4]; values in the thousands are not degrees. (b) A compliant reader would treat `2381.5` as longitude and `6120.0` as latitude — both out of range — and would either reject the file or place features nonsensically. (c) Acceptable: ask the publisher to *transform* to WGS 84 and re-export as standard GeoJSON (Chapter 6, *Project*); or accept the file under a written prior arrangement that names EPSG:32643, keep it out of any general-purpose pipeline, and convert it yourself with a documented transformation. Unacceptable: delete the `crs` member (or relabel) and load the numbers as degrees — the Chapter 6 relabelling error.

**7.3.** No. Esri states that after conversion "ArcGIS cannot determine whether a field value represents a null value or a legitimate value" [S20]; the substitute for a numeric null under most tools is 0. The settling evidence is the *original* (pre-export) dataset or its intake record showing the null, or the inspection log showing no assessment for that asset. This is why 7.7.3 says never convert in place.

**7.4.** `survey.tif`: (1) does it carry georeferencing tags at all — read the tags; the deciding one is `GeoKeyDirectoryTag` (34735) plus a tiepoint/scale or transformation tag [X12]; (2) CRS from the GeoKeys (EPSG code or user-defined); (3) NoData — the non-standard GDAL tag 42113 or publisher metadata [X13]. `survey.gpkg`: (1) what it contains — `SELECT table_name, data_type, srs_id FROM gpkg_contents;` [X01, Req 13]; (2) which CRS definitions — `gpkg_spatial_ref_sys`; (3) whether extensions are in use — `gpkg_extensions` present and non-empty [X01, Req 59].

**7.5.** For editing through a database connection in ArcGIS Pro, the database must contain geodatabase system tables — "If the database contains geodatabase system tables, it is considered a geodatabase in ArcGIS" — and the table must be registered with the geodatabase; unregistered "database data cannot" be edited that way and needs an editable web feature layer instead [X07]. The system tables are created by *Enable Enterprise Geodatabase*, which requires an ArcGIS Server keycodes (authorization) file and ArcGIS Pro Desktop Standard/Advanced or ArcGIS Server on the connecting machine, with a spatial type (PostGIS or ST_Geometry) enabled first [X09] [X08]. PostGIS alone satisfies only the spatial-type part.

**7.6.** (a) Digitized — evidence: the source plan's scale and date, and the tracing tolerance. (b) Geocoded — evidence: match scores, locator and reference data version/date. (c) Simulated — evidence: the purpose statement; no accuracy claim exists. (d) Derived — evidence: the processing history (input R1 and its intake reference, buffer distance 300 m with units, planar method on the training grid, software/version, date, person).

**7.7.** Any three of: nulls substituted (snapshot line: null counts per field, and the TR-0301 known record); field names shortened (line: schema/field list); time dropped from timestamps and possibly the date shifted (line: date/time samples for SL-0055); Gujarati re-encoded (line: Unicode text samples); leading zeros lost if a type was guessed upstream (line: identifier samples). Count and field-count lines detect none of these — which is the lesson.

## I.2 Answer key — 7.9 concept and scenario questions

**Q1 (LO1).** (a) `wards.shp` alone: missing the required `.shx` and `.dbf` [S20] — so no attributes and, in most software, no opening at all (GDAL can rebuild a missing `.shx` only with a special option [X02]); missing `.prj` → CRS unknown; missing `.cpg` → encoding unknown; missing any metadata/readme. Present: geometry records only, from which the geometry type and extent could be read if the file opens — nothing else. (b) `sites.csv`: missing everything geographic — which columns are coordinates, their order (70.01 then 20.02 could be lon/lat or lat/lon; both are in range, so the numbers cannot decide — Chapter 5), the CRS, units, encoding, and field types; no readme, no publisher, no date. Present: a plausible pair of decimal numbers and a name; the only honest action is to ask the publisher. (c) `assets.gpkg` + readme: nothing structurally missing; check that the file is a real GeoPackage (`application_id` GPKG, `gpkg_contents`, `gpkg_spatial_ref_sys`) and whether `gpkg_extensions` lists anything the reader must support [X01]. Four layers: model — vector and/or raster and attribute tables (a GeoPackage can hold features, tiles, and attributes, so the extension does not fix the model); encoding — GeoPackage binary geometry in SQLite tables; container — an SQLite 3 database with `gpkg_*` bookkeeping tables; service — none (a frozen copy). Not proved even by the complete package: accuracy (intake field 8, publisher's statement), freshness/edition (field 4, not the file timestamp), suitability for the question at hand (recipient/question), and licence (field 9) — any three. Full marks require the learner to say for (b) that the coordinate order cannot be decided from the numbers.

**Q2 (LO2).** (a) `X` (second column) is longitude, `Y` first is latitude; the *only* evidence is the column names (the `Y, X` pair is a form ArcGIS Online recognises by name [X18]) and the publisher's statement — the values 20 and 70 are both within both ranges, so a range check cannot decide (Chapter 5). (b) `ward_no` = `007` can be typed as an integer (→ 7) by ArcGIS Pro's text workspace, by QGIS type detection ("all values can actually be converted"), or by ArcGIS Online ("if only numerals are present, as an integer") [X16] [X19] [X18]; it can also be damaged by a spreadsheet re-save. Prevention: `schema.ini` `Col3=ward_no Text` in ArcGIS Pro [X16]; changing the detected type to Text or a `.csvt` in QGIS [X19]; defining the field type when adding to Map Viewer in ArcGIS Online [X18]. (c) The file must be UTF-8 (or Unicode) with that encoding honoured by the importer; ArcGIS Online states files with non-English characters "must be encoded as Unicode or UTF-8, not ASCII" [X18].

**Q3 (LO2).** RFC 7946 fixes the CRS of all GeoJSON coordinates to WGS 84 longitude/latitude in decimal degrees and has removed the earlier `crs` member because alternative CRSs "proven to have interoperability issues" [S18, §4]. A `crs` member is therefore at best a foreign member that a compliant reader may ignore, and the numbers will be read as degrees. Projected data must be transformed to WGS 84 before export to be compliant. The single permitted exception is a *prior arrangement* among all involved parties [S18, §4] — which does not make the file standard for anyone outside the arrangement.

**Q4 (LO3).** (a) Yes — `.shp`, `.shx`, `.dbf` are the three required files [S20]. (b) Unknown: `.prj` is optional, so no CRS is defined; "unknown" is not "none". (c) Adding the publisher's `.prj` restores documented metadata (no geometry change); writing one yourself is *Define Projection* — legitimate only once the correct CRS is established from evidence, and it "does not modify any geometry" [S15]; writing a guessed one is the relabelling error. (d) You cannot tell whether 0 is a measured score or a substituted null [S20]; you can conclude only that the value is 0 *in this file*; the original or inspection records settle it.

**Q5 (LO4).** GeoPackage keeps, shapefile cannot: any of — null values, field names longer than 10 characters, date *and* time (as UTC), Unicode text without code-page dependence [X01] [S20]. File geodatabase keeps, GeoPackage core does not: relationship classes, domains/subtypes, attribute rules, feature datasets, versioning (enterprise) [S21] [X04] [X05]. No data format keeps: symbology/labels/pop-up configuration (Table 7.4). Mechanism: the `gpkg_extensions` table, whose absence or emptiness means a plain GeoPackage [X01, Req 59]; a reader that finds an extension it does not implement should "fail fast" with an error or, for `write-only` extensions, may safely read [X01, §2.3.2].

**Q6 (LO5).** (i) False — "If the database contains geodatabase system tables, it is considered a geodatabase in ArcGIS" [X07]; PostGIS supplies a spatial type only. (ii) False — three spatial types are supported in PostgreSQL: ST_Geometry, PostGIS geometry, PostGIS geography [X08]; the geodatabase uses ST_Geometry if that library is installed before enabling, PostGIS if PostGIS is enabled [X09]. (iii) False — hosted feature layer data is in ArcGIS Data Store's relational store (system storage), and "access to the content of each data store … is provided exclusively through web layers" [X10] [X11]; an enterprise geodatabase is user storage published by reference [X11].

**S1 (LO3, LO7) — model answer (key points).** *Adaptations:* `species_scientific_name` → name shortened to ≤ 10 characters, mapping documented [S20]; `last_pruned_at` → date only, time and offset dropped, with the UTC-before-truncation question answered explicitly [S20]; `heritage_status` → codes only unless the export transfers domain descriptions [X28] — either way the domain itself is lost [S20]. *Losses (must be declared):* `canopy_diameter_m` nulls → 0 (or another substitute) [S20]; Unicode species names may be garbled without a Unicode code page honoured by the contractor's tool [S20]; any field beyond 255 or names colliding after truncation. *Most damaging loss:* null → 0 in `canopy_diameter_m` (a zero-canopy tree is a false measurement). *Two checks that detect it:* the per-field null count before/after, and the known-record spot check on a tree whose canopy was null. Full marks require citing the rule for each line and separating the two sections.

**S2 (LO6) — model answer.** (a) Under the 2019 boundary (x = 1300), P3 (1200, 250) and P5 (1000, 500) fall in Ward A; under the current boundary (x = 1000), P3 is in Ward B and P5 is on the boundary. The manager's Ward A count is 4 versus your 2 (plus a P5 policy). (b) Fields 4 (edition/date of observation), 5 (coverage: an x-range to 1300 for Ward A), and 10 (known omissions/superseded) expose it; field 1 (publisher/"official") cannot. (c) Something like: "No boundary or reference layer is used for a report until its intake form's edition date has been checked against the report's date and recorded in the processing history."

## I.3 Expected results — 7.8 lab, and fixture derivations

**Fixture derivations (hand-checked).**
- Field-name lengths: `asset_id` 8; `legacy_code` 11; `asset_type_en` 13; `asset_type_gu` 13; `installed_year` 14; `condition_score` 15; `last_inspection_at` 18; `inspector_note` 14; `x` 1; `y` 1 → seven names exceed 10.
- `condition_score` values 3.5, 1.5, null, 5.0, 2.0, 4.0 → sum 16.0 over 5 valid → mean 3.2; with null as 0 over 6 → 16.0 / 6 = 2.666… ≈ 2.67.
- Extent: x min 205 (SL-0113), max 2190 (TR-0301); y min 195 (SL-0113), max 950 (SL-0055).
- Ward membership (strict interior, Chapter 1 squares): SL-0113 (205,195) A; DR-0042 (995,510) A (995 < 1000); DR-0110 (400,700) A; BN-0007 (1500,300) B; SL-0055 (1800,950) B; TR-0301 (2190,520) outside (x > 2000).
- UTC conversions (subtract 5 h 30 min): SL-0113 10:42 → 05:12Z same date; DR-0042 15:05 → 09:35Z; BN-0007 09:00 → 03:30Z; DR-0110 11:30 → 06:00Z; **SL-0055 02:10 on 2026-08-30 → 20:40Z on 2026-08-29** (date shifts).
- 7.6.3 boundary counts: 2019 file A = {P1, P2, P3, P5} = 4, B = {P4} = 1; current A = {P1, P2} = 2, B = {P3, P4} = 2, P5 boundary, P6 outside.

**Lab expectations.** The predictions in 7.8.6 are the marking baseline; the instructor must **run the lab once in the installed ArcGIS Pro and QGIS versions before issuing it** and replace each "expected" cell with the observed value, keeping the prediction beside it (I.6, I.8). Marks depend on the learner's observed values being complete and correctly classified, not on matching the predictions. Specific things to look for:

- The GeoPackage keeps all ten names, the null, and the Gujarati text; whether `last_inspection_at` became a UTC `DATETIME` or stayed text depends on the learner's step-5 typing — either is acceptable if documented.
- The GeoJSON-syntax file from *Features To JSON* contains a `crs` member [X24]; the learner's readme must label it non-standard under a prior arrangement.
- The shapefile shows renamed fields (record the exact names for both products), `condition_` (or equivalent) = 0 for TR-0301 under Esri's rule for export tools [S20], date-only values, and Gujarati intact or garbled depending on the code page.
- `00_original` unchanged.

## I.4 Common mistakes and remediation

| Mistake | Where it shows | Why it happens | Remediation |
| --- | --- | --- | --- |
| Accepting WGS 84 at XY Table To Point (points appear on Earth) | Step 6; "after" CRS line | Tool default [X14]; learner equates "coordinate system parameter" with "any value" | Re-teach Chapter 6 assignment vs transformation; rerun with the designated local reference; require the mistake in the log |
| Leaving intake fields blank | Deliverable 1 | Treating the form as paperwork | Return the form; "unknown + question sent" is the minimum |
| Counting rows and fields and declaring "no loss" | Deliverable 3 | Confusing structural success with semantic preservation | Point to the three known records; require the null and date lines |
| Renaming source fields to avoid the shapefile warning | Step 10 | Wanting a clean export | Explain that the mapping must be documented, not hidden; the source must not be edited for a recipient's limitation |
| Classifying the UTC conversion as a loss, or the null → 0 as an adaptation | Deliverable 3 | Not asking "did I decide this, and can I justify it?" | Re-teach 7.7.2 with the two-column test |
| Calling the R-B file "GeoJSON" without qualification | Deliverable 4 | Extension-as-proof thinking (7.1.2) | Require "prior arrangement" and "not standard RFC 7946" wording |
| Saying PostGIS = enterprise geodatabase | Q6 | Both involve PostgreSQL | Re-read [X07] sentence on system tables; [X09] on the enabling tool |

## I.5 Fresh exercise for retesting (different values, same concepts)

Use a new synthetic table `drains_ch7b` with six rows and fields `drain_id`, `register_no` (text, values `0090`, `0007`, `0123`, `0450`, `0001`, `0088`), `material_en`, `material_gu` (for example `કોંક્રિટ`, `પથ્થર`), `depth_m` (one null), `cleaned_at` (ISO with `+05:30`; include `2026-10-02T01:15:00+05:30`, which is `2026-10-01T19:45:00Z` — a date shift), `crew_note` (one Hindi, one empty), `x`, `y` on the training grid with one point exactly at x = 1000. Ask for the same deliverables; expected losses are the same classes with new values. For the concept retest, swap the Q2 header to `lon,lat` with values `70.0012,20.0007` (order now explicit in names) and ask what changed in the evidence.

## I.6 Preparing the lab environment — not execution-tested

1. **Designate the training-grid reference.** Decide, and document in the instructor pack, the coordinate reference learners select at step 6 (Pro) and step 2 (QGIS). Options: leave the feature class with an unknown coordinate system if the installed tool permits an empty *Coordinate System* parameter; or define a custom local projected coordinate system in metres and name it (for example `TrainingGrid_Local_m`) with an explicit note that it is not an Earth location. Record which option was used and what the analyzer/tool messages said. Chapter 2's build (I.6 there) faced the same decision; reuse its choice for consistency.
2. **Build and check the original files** exactly as printed in 7.8.3, save the CSV as UTF-8, and confirm in the installed ArcGIS Pro that the table view shows `0113` and Gujarati correctly with the printed `schema.ini`; if not, find the working `CharacterSet`/BOM combination and update 7.8.3 and the change log.
3. **Run the three exports** in ArcGIS Pro and in QGIS; capture the observed field names after shapefile export in each product, the `.cpg` contents, the null substitute, the date handling, the `srs_id` written into the GeoPackage, and the presence of the `crs` member in the GeoJSON file. Enter these as the observed column of 7.8.6 for marking.
4. **Verification items to close before issue:** the `schema.ini` `CharacterSet` behaviour with UTF-8 (7.8.3); whether XY Table To Point accepts an empty *Coordinate System* (7.8.5 step 6); QGIS's treatment of quoted `"0113"` under type detection (7.2.1); the *Geometry CRS* choices for a non-Earth layer in QGIS 3.44 (7.8.10); the exact shapefile field-renaming behaviour of ArcGIS Pro 3.7 (7.3.3); GDAL's null handling on shapefile write (7.3.3, 7.8.10); ArcGIS Pro's export of a timestamp-offset field into GeoPackage `DATETIME` (7.8.6); whether *Export Features* writes directly into a `.gpkg` or another tool is needed (7.4.1, 7.8.5 step 8).

## I.7 Blueprint corrections, source notes, and verification items

- **No factual error was found in the Chapter 7 blueprint text.** Two source-coverage gaps were filled with additional primary sources rather than left unsupported: [S11] (QGIS raster introduction) describes georeferencing generically but does not describe GeoTIFF, so the OGC GeoTIFF 1.1 standard [X12] and the GDAL GTiff driver page [X13] were added; [S04] mentions ArcGIS Data Store in one sentence, so the Data Store and data-storage pages [X10] [X11] were added.
- **QGIS documentation edition.** The blueprint pins the *Gentle Introduction* to 3.44. The 3.40 *User Guide* pages now carry an "end of life" banner, so the 3.44 User Guide is cited for procedures; the relevant sections were checked to be present in 3.44.
- **Esri URL changes.** The old `enterprise.arcgis.com/en/data-store/latest/...` Data Store URL now redirects (two hops) to `doc.esri.com/en/arcgis-enterprise/latest/plan/what-is-arcgis-data-store.html`; a related link inside that page resolved to the 12.1 edition. Esri's shapefile page does not list `.cpg`; the description was taken from the legacy ArcMap page [X03] and the GDAL driver [X02], and this is stated in the text.
- **File geodatabase field-name limit.** Older summaries state 64 characters; the current ArcGIS Pro 3.7 page states 128 characters for file geodatabase and memory workspaces [X30]. The current figure is used.
- **Null substitution values.** Esri's table distinguishes tools that require NULL/infinity/NaN output (substitute the IEEE extreme negative) from "all other geoprocessing tools" (substitute 0) [S20]; the chapter uses "0 (or another substitute)" for exports and asks learners to observe.
- **Unverified behaviours flagged in the text:** every interface step in 7.8.5 and 7.8.10; the items listed in I.6 point 4.

## I.8 Instructor change log

| Date | Change | Affected sections | Rechecked |
| --- | --- | --- | --- |
| 2026-09-19 | Initial draft from blueprint revision 1.0; all references opened and read | All | — |
| *(to be filled)* | First execution of the lab in the installed versions; observed values entered | 7.8.3, 7.8.6, I.3, I.6 | 7.9.3 practical, M.3 D3 |

---

# Media Appendix

## M.1 Slide outline (mapped to module IDs)

| Slide group | Module | Objective of the sequence | Visual | Question before the answer (speaker notes) |
| --- | --- | --- | --- | --- |
| 1–2 | Front matter | Recap Chapters 3–6 in one picture: content (features, rasters, CRS) versus packaging | Four stacked boxes: model / encoding / container / service, with Table F7 travelling through them | "When someone says 'send me the assets', how many decisions are hidden in that sentence?" |
| 3–5 | 7.1 | Separate the four layers; extension ≠ evidence; file vs service | D1 (four-layer stack); the extension-claims table; the download-vs-URL comparison | "What does `.gpkg` prove?" — answer: the encoding claim only |
| 6–9 | 7.2 | CSV silent failures; GeoJSON objects; lon/lat and WGS 84 rule; read by eye | The CSV header and TR-0301 row with three empty fields highlighted; the E7-1 JSON with the five questions as callouts; a map showing [70, 20] and [20, 70] | "Which of these two points is in the sea off India, and which is near Norway?" |
| 10–12 | 7.3 | Companion files and roles; `.prj` optional = unknown; the loss table | D2 (package inspection, shapefile panel); Table 7.3.3 rows animated one at a time with the F7 field they bite | "The export said 'completed'. Name one thing that changed without an error." |
| 13–15 | 7.4 | GeoPackage core vs extensions; GeoTIFF tags and the inspection checklist; Table 7.4 | D2 (GeoPackage panel); a TIFF header schematic with the six geo tags; Table 7.4 with a highlighted column per format | "Which format has an honest place for 'this grid is not on Earth'?" |
| 16–18 | 7.5 | Store + model; feature class / table / feature dataset; PostGIS vs enterprise geodatabase vs Data Store | Two-level diagram (physical store beneath information model); the three-column comparison | "Our Postgres has PostGIS — do we have a geodatabase?" |
| 19–21 | 7.6 | Intake form; five data classes; the 2019 boundary mismatch | Table 7.6 as a form; D4 (old vs new boundary with P3 and P5 moving) | "Which intake field would have caught the wrong boundary — and which never could?" |
| 22–24 | 7.7 | Before/after snapshot; adaptation vs loss; three working rules | D3 (record comparison); the two-column sorting diagram | "Is 'time dropped' an adaptation or a loss?" — answer: depends on consent and documentation |
| 25–27 | 7.8 | Lab briefing: recipients, routes, deliverables | The recipient table; the folder layout `00_original` / `10_outputs` | "Which recipient would you refuse a shapefile?" |
| 28 | 7.9 | Gate: what must be distinguished to progress | Four concerns (format, authority, permission, quality) as four separate cards | — |

## M.2 Audio-lesson outline

The audio version must carry every essential visual in words. Pronounce and introduce once: GIS, CRS ("coordinate reference system"), CSV ("comma-separated values"), JSON, GeoJSON, RFC ("R-F-C, an internet standards document"), OGC ("the Open Geospatial Consortium"), GeoPackage, SQLite ("S-Q-Lite"), TIFF and GeoTIFF, dBASE ("dee-base"), UTF-8, UTC, EPSG ("E-P-S-G, the code registry"), PostGIS, DBMS. Say "longitude then latitude" aloud every time GeoJSON coordinates are read; never say "the first number" without naming the axis.

1. **Opening (7.1).** State the four layers as a spoken list with one example each; then say slowly: "The extension is a claim. Claims are checked." Describe the download-versus-URL table as two columns you are reading across: "freshness — the file goes stale at once; the service answers with today's state."
2. **CSV (7.2.1).** Read the F7 header aloud. Read the TR-0301 row and count the empty fields aloud — "empty, empty, empty" — then ask the listener which of the three means null and which means 'no note'. Pause. Explain that the file cannot answer and the readme must. Read `0113` as "zero one one three, four characters" and describe what an importer does when it decides the column is a number.
3. **GeoJSON (7.2.2–7.2.3).** Describe the E7-1 file as nested boxes: "a collection, containing two features; the first feature has a point whose coordinates are longitude seventy, latitude twenty, in that order." State the WGS 84 rule as a sentence and the prior-arrangement exception as a sentence. Describe the swapped pair and say where each lands.
4. **Shapefile (7.3).** Name the five roles — geometry, index, attributes, CRS note, encoding note — and which are required. Then narrate one record passing through the export: "the null becomes zero; the name becomes ten letters; the time is gone; the Gujarati survives only if the code page is honoured." Pause and ask: "Did the tool report an error?" Answer: no.
5. **GeoPackage and GeoTIFF (7.4).** Describe a GeoPackage as "an ordinary SQLite database with a few tables whose names start with g-p-k-g underscore"; name the three mandatory spatial-reference records including minus one. For GeoTIFF, read the checklist as seven questions and say for each where the answer lives.
6. **Geodatabase (7.5).** Give the two-level definition; then the three-way comparison as a spoken table, one row at a time: who defines the schema; how you reach the bytes.
7. **Sources (7.6).** Read the intake form fields as a checklist. Tell the boundary story with the numbers: "P3 at twelve hundred: inside A under the old line at thirteen hundred; inside B under the new line at one thousand." Ask which field would have caught it. Pause.
8. **Checks (7.7).** Read the snapshot lines. State the two-column test as a question the listener asks of every difference: "Did I decide this, and can I justify it to the recipient?"
9. **Close.** Recap the eight points in one sentence each; announce the lab and the four concerns of the oral.

## M.3 Diagram specifications

All diagrams are original schematics built from the fixture; none is presented as measured GIS output. Label synthetic data as such on every diagram.

**D1 — Four-layer stack (7.1).** Four horizontal bands labelled *Data model*, *Encoding*, *Container*, *Service*, top to bottom. In each band, two or three chips naming instances (vector / raster; CSV, GeoJSON, `.shp`, GeoTIFF; folder-of-files, GeoPackage, file geodatabase, enterprise geodatabase; hosted feature layer, feature service, WFS). A single vertical thread labelled "Table F7" passes through one chip per band with the caption "same content, four independent choices". A side note reads "extension = claim about the *encoding* band only".

**D2 — Illustrated package inspection (7.3, 7.4).** Two panels. *Left:* a folder listing of `assets_ch7.*` with each file connected by a line to a role card: geometry / index / attributes / CRS note / encoding note; `.prj` and `.cpg` cards carry a red "if missing → unknown" tag. *Right:* a database-browser view of `assets_ch7_R-C.gpkg` listing `gpkg_contents`, `gpkg_spatial_ref_sys` (showing rows 4326, −1, 0 and the assigned local reference), `gpkg_geometry_columns`, `gpkg_extensions` (empty), and the user table `assets_ch7`, each with a one-line role. Caption: "Inspect the package, not the icon."

**D3 — Before/after record comparison (7.7; the blueprint's preferred visual).** Three rows (SL-0113, TR-0301, SL-0055) shown as "before" cards on the left with all ten fields, and "after shapefile export" cards on the right. Changed cells are outlined: field names shortened (amber, "adaptation, documented"); `condition_score` null → 0 (red, "loss"); `last_inspection_at` time dropped and, for SL-0055, the possible date shift (amber if documented, red if not — show both variants with a switch); Gujarati intact (green) or `????` (red) depending on a "code page: UTF-8 / ANSI" toggle. Unchanged cells grey. A legend defines the three colours. Values shown are the fixture's; the after-values are labelled "predicted from format rules; replace with observed values after the instructor's run".

**D4 — Old versus current boundary (7.6.3).** The Chapter 1 canvas (x −100 to 2300, y −100 to 1100, metres). Ward A/B current edge at x = 1000 drawn solid; the 2019 edge at x = 1300 drawn dashed and labelled "2019 delimitation (superseded)". Requests P1–P6 plotted with IDs. P3 and P5 highlighted with arrows "A → B" and "A → boundary" and a small table of the two counts. Caption: "Synthetic training grid; no real boundary."

**D5 — Two-level geodatabase (7.5).** A lower band "physical store" with three tiles (`.gdb` folder of files; `.geodatabase` SQLite file; tables in a DBMS) and an upper band "information model" with tiles (feature classes, tables, feature datasets, relationship classes, domains, behaviour rules). Arrows from the upper band into each lower tile. A separate box to the right, "ArcGIS Data Store — system storage, reached only through web layers", deliberately *not* connected to the upper band, with a dotted line to "hosted feature layers".

## M.4 Format comparison for slides (Table M1; claims dated 19 September 2026)

A condensed version of Table 7.4 for a single slide; keep the citations in the notes.

| | CSV | GeoJSON | Shapefile | GeoPackage | Geodatabase |
| --- | --- | --- | --- | --- | --- |
| CRS inside | no | fixed WGS 84 | optional `.prj` | yes (incl. undefined `−1`) | yes |
| Nulls | convention | yes | **no** | yes | yes |
| Date + time | text | text | date only | UTC `Z` | yes, incl. offset |
| Unicode | if declared | yes | code page | yes | yes |
| Long names | yes | yes | 10 chars | yes | 128 (file gdb) |
| Many datasets | no | one collection | no | yes | yes |
| Relationships/rules | no | no | no | extension only | yes |
| Styling | no | no | no | product-specific | no (maps/layer files) |

## M.5 Interactive and 3D material

**No 3D scene** is proposed; the blueprint's judgement that a before/after comparison teaches better is followed.

**One optional interactive demonstration is justified: "One record through five formats" (web widget, 2D, text-based).**

- **Learning objective.** Let the learner *predict* what each format does to a record, then reveal the rule-based outcome and the citation, reinforcing 7.3.3, 7.4.3, and 7.7.2.
- **Objects.** A single editable record with the ten F7 fields (defaults: SL-0055's values); five target-format columns (CSV, GeoJSON, Shapefile, GeoPackage, File geodatabase); per-cell outcome chips ("kept", "adapted", "lost", "product-dependent") each linking to the rule text and reference ID.
- **Labels and units.** Field names as in F7; coordinates labelled "training-grid metres (no Earth location)"; timestamps labelled with offset; a banner "Simulated outcomes derived from published format rules — not an executed conversion".
- **Controls.** Toggles: *shapefile code page* (UTF-8 / ANSI); *timestamp typed before export* (text / date-time); *null policy* (substitute 0 / substitute extreme negative); *recipient* (R-A / R-B / R-C) which highlights the recommended column. A "predict first" mode hides outcomes until the learner clicks a chip choice per cell.
- **Expected behaviour.** Changing the code page toggles the Gujarati cell between "kept" and "lost" in the shapefile column only; typing the timestamp as date-time makes the GeoPackage cell show `2026-08-29T20:40:00.000Z` with an "adapted — date changed" note; the GeoJSON column always shows the CRS cell as "non-standard: prior arrangement required" for training-grid data; the file-geodatabase column shows field names kept up to 128 characters.
- **Validation method.** The widget's outcome table is generated from a fixed rule set whose every entry cites a reference ID in this chapter; the instructor validates the widget against the observed values from the lab run (I.6) and marks any cell where the installed product differed as "product-dependent" with the observed value. The five UTC conversions in I.3 are the numeric test fixtures.
- **2D/static and text alternative.** Table 7.4 plus D3, and the M.2 narration of one record through the export, cover the same content without the widget.
