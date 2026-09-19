# GIS and ArcGIS Fundamentals — Phase 1

# Chapter 4 — Raster data and geographic surfaces

## Document metadata

| Item | Value |
| --- | --- |
| Chapter ID | Phase 1, Chapter 4 |
| Title | Raster data and geographic surfaces |
| Source specification | `GIS_Phase_1_Chapter_Authoring_Blueprint.md`, revision 1.0 (18 September 2026) |
| Document revision | 1.0 — 19 September 2026 |
| Reference-check date | 19 September 2026 (every cited page was opened and read on this date; see the reference list for exact URLs) |
| Audience | Software developers at LiveBird Technologies with no GIS background beyond Chapters 1–3 |
| Software referenced | ArcGIS Pro (documentation labelled version 3.7 on the check date; tool pages read at the `latest` URL); QGIS Desktop 3.44 (the Long Term Release offered on the QGIS download page on the check date; the same edition as the *Gentle Introduction to GIS* named in the blueprint); GDAL (the `stable` documentation edition, because QGIS reads and writes rasters through GDAL) |
| Execution status | **No procedure in this chapter has been executed in ArcGIS Pro or QGIS by the author.** Interface steps were written from the official pages cited beside them and are marked *not execution-tested*. Every number in the worked examples and the lab's expected results was checked by hand from the fixture printed in this document; the Instructor Appendix lists exactly which observations still need to be confirmed in software. |
| Data status | Every grid, value, coordinate, file name, and category code in this chapter is **synthetic training material**. Nothing describes a real municipality, a real land-cover survey, or a real terrain. The training grid has no Earth location and no coordinate-system identifier. |

### How to use this document

Read the modules in order; each one assumes the ones before it. Boxes labelled **Procedure (version-specific)** contain interface steps that will age; everything outside those boxes is durable concept. Boxes labelled **Platform note** separate ArcGIS- or QGIS-specific behaviour from general GIS principles, and boxes labelled **Verification item** tell an instructor what must be checked in the installed environment before a step is treated as fact. Comprehension checks have no answers in the learner text; answers are in the Instructor Appendix, which learners should not open before the progression gate in 4.8.

---

## Prerequisites

- **Chapter 3 completed.** You can describe a feature as geometry plus attributes; you know point, line, and polygon geometry; you can tell a dataset (the stored collection) from a layer (its use in a map with styling and query settings); and you know that changing how a layer looks does not change the stored records.
- **Chapters 1–2.** You can frame a spatial question, separate policy from evidence, and name the responsibilities (authoring, storage, service delivery, content organisation, applications) a complete GIS solution covers.
- Ordinary developer experience: you know what a two-dimensional array, an image file, a `NULL` value in a database, and a file header are. These are used as analogies and are not taught here.
- **Not required:** coordinate reference systems (Chapter 5), projections (Chapter 6), file-format details beyond what this chapter introduces (Chapter 7), or any raster analysis beyond inspection and resampling.

## Learning outcomes

By the end of this chapter you should be able to:

| ID | Outcome | Where it is taught | Where it is checked |
| --- | --- | --- | --- |
| LO1 | Describe a raster as rows and columns of cells with values, locate a cell from its row/column and from a coordinate, and explain when a sampled surface is a better representation than discrete objects — and when it is not. | 4.1 | 4.8 concept Q1; practical |
| LO2 | Distinguish a stored cell value from its display colour, explain one-band and multiband rasters, and state what dictionary or unit is needed before a value can be interpreted. | 4.2 | 4.8 concept Q2; scenario Q1 |
| LO3 | Compute a raster's extent from its origin, cell size, and dimensions; explain why cell size is not positional accuracy; and recognise two grids that do not align. | 4.3 | 4.8 concept Q3; practical |
| LO4 | Distinguish zero, NoData, a mask, and transparent display; compute a statistic under an explicit missing-data policy; and separate "no coverage" from "nothing observed". | 4.4 | 4.8 concept Q4; scenario Q2; practical |
| LO5 | Explain why resampling requires a method, choose nearest neighbour for categories and an interpolating method for continuous surfaces with reasons, and explain why a smaller output cell does not add detail. | 4.5 | 4.8 concept Q5; practical |
| LO6 | Describe an elevation raster as a height field, distinguish a terrain surface from a surface that includes objects, state why heights need units and a vertical reference, and distinguish a shaded rendering from the numbers beneath it. | 4.6 | 4.8 concept Q6; oral |
| LO7 | Inspect two rasters (dimensions, cell size, values, bands, extent, NoData), change styling without changing values, resample on copies, and record the numerical effect. | 4.7 | 4.7 lab; 4.8 practical |

## Required materials

- This document, a calculator or spreadsheet, and squared paper (or any drawing tool).
- The two **Chapter 4 fixture files** printed in full in the fixture section below. They are plain text; you create them by copying the listings into a text editor and saving with the stated names. No download is needed.
- **For the guided lab (4.7):** ArcGIS Pro (any licence level — every tool used is available at Basic, as noted beside each step) **or** QGIS Desktop 3.44. If neither is available, the inspection and statistics parts of the lab can be completed by hand from the listings; the resampling comparison then remains a paper prediction, and the instructor must run it once to confirm the software behaviour.
- **Not required:** an ArcGIS Online account, the Spatial Analyst or Image Analyst extension, or any publishing action. Nothing in this chapter consumes credits.

## Recap of the preceding chapter

Chapter 3 introduced the **vector** model: a **feature** is a represented entity with **geometry** (a point, line, or polygon built from vertices) and **attributes** (fields and values in a record). The same school can be a point on a city overview and a polygon on a campus plan; which is right depends on the question. You learned that a **dataset** is the stored collection, a **layer** is the dataset's use in a map with styling and filters, and that two layers can show one dataset differently without copying it. You inspected extents, counts, and field names, and you saw that a styling change alters no record while a geometry edit does.

*Instructor note.* The Chapter 3 learner document had not been issued when this chapter was written; the recap above follows the blueprint's Chapter 3 description. If Chapter 3 introduces terminology that differs from this recap, align the recap to the issued Chapter 3 text.

Chapter 3 closed by asking how a GIS represents something that has no edges — rainfall, temperature, the height of the ground. That is the subject of this chapter: **grids of values**.

## The recurring scenario and the Chapter 4 fixture

The fictional municipality continues: **assets** (streetlights, drains, trees), **road** centrelines, **wards**, **service requests**, and **inspections**. The Chapter 1 fixture is reused unchanged: Ward A is the square (0, 0)–(1000, 1000); Ward B is (1000, 0)–(2000, 1000); Road R1 runs from (0, 500) to (2000, 500); the six requests P1–P6 and the three assets SL-0113 (205, 195), DR-0042 (995, 510), and TR-0301 (2190, 520) keep their Chapter 1 coordinates. Everything is on the flat, metre-based **training grid**: x increases to the right, y increases upward, and there is **no Earth location and no coordinate-system identifier**. If you need the vector tables, they are Tables F1–F4 in the Chapter 1 document.

**New for Chapter 4 — two small rasters (synthetic).** The blueprint's Appendix A calls for "a small raster" in the training package and, for this chapter, a small categorical raster and a small elevation raster with documented NoData. Both are defined below as plain-text files in the **Esri ASCII raster format**, chosen because it is human-readable, can be typed by hand, and is read by both ArcGIS Pro and QGIS (through GDAL) [X09] [X27]. The format's header names the number of columns and rows, the coordinate of the **lower-left corner of the lower-left cell**, the cell size, and the value that stands for NoData; the cell values follow in row-major order [X07]. The first data row is the **top** (northernmost) row of the grid — rasters list their values "from the upper left pixel along each row to the lower right pixel" [X01] — and the Instructor Appendix asks the instructor to confirm this on first load (I.6).

**Fixture F5 — Land-cover raster `landcover_training.asc` (synthetic; categorical).** Ten columns by ten rows of 100 m cells covering Ward A exactly (x from 0 to 1000, y from 0 to 1000). Each cell holds a **category code**; codes are integers and mean nothing without the dictionary in Table F5b. The value `-1` marks cells that were **not classified** (in the story, hidden by cloud in the source image). Row 1 of the listing is the top row (y from 900 to 1000).

```
NCOLS 10
NROWS 10
XLLCORNER 0
YLLCORNER 0
CELLSIZE 100
NODATA_VALUE -1
2 2 2 2 2 2 2 0 0 0
2 2 2 2 2 2 2 0 0 0
2 2 2 3 3 2 2 2 0 2
2 2 3 3 3 3 2 2 2 2
1 1 1 1 1 1 1 1 1 1
1 1 1 1 1 1 1 1 1 1
2 2 1 1 2 2 2 3 3 2
2 2 1 1 2 2 2 3 3 2
-1 -1 2 2 2 2 2 2 2 2
-1 2 2 2 2 2 2 2 2 2
```

**Table F5b — Category dictionary for `landcover_training.asc` (synthetic).**

| Code | Class | Meaning in the story | Cells | Area (1 cell = 100 m × 100 m = 10,000 m² = 1 ha) |
| --- | --- | --- | --- | --- |
| 0 | Water | Pond in the north-east of Ward A | 7 | 7 ha |
| 1 | Built-up | Buildings and paving, mostly along Road R1 | 24 | 24 ha |
| 2 | Vegetation | Grass, trees, gardens | 56 | 56 ha |
| 3 | Bare ground | Unvegetated soil | 10 | 10 ha |
| −1 (NoData) | Not classified | Cloud in the source image; nothing is known about these cells | 3 | 3 ha of **no coverage** (not 3 ha of anything) |
| | | **Total** | **100** | **100 ha = 1 km² = Ward A's area** |

**Fixture F6 — Elevation raster `elevation_training.asc` (synthetic; continuous).** Four columns by four rows of 100 m cells covering the north-west corner of Ward A (x from 0 to 400, y from 600 to 1000). Each cell holds a **height in metres above the training datum "TD-0"**, an invented zero level that exists only in this exercise (Chapter 5 introduces real vertical references). Values are written with a decimal point so that the software stores them as floating-point numbers. The value `-9999.0` marks one cell where no height was measured. Row 1 of the listing is the top row (y from 900 to 1000).

```
NCOLS 4
NROWS 4
XLLCORNER 0
YLLCORNER 600
CELLSIZE 100
NODATA_VALUE -9999.0
12.0 14.0 17.0 21.0
11.0 13.0 15.0 18.0
10.0 11.0 13.0 15.0
9.0 -9999.0 12.0 13.0
```

**Hand-checked facts about F6** (derivations in Instructor Appendix I.3): 16 cells, of which 15 are valid; the valid values sum to 204; the mean of the valid cells is **13.6 m**; the minimum is 9.0 m and the maximum 21.0 m. If the missing cell were wrongly counted as 0 m, the mean would be 204 ÷ 16 = **12.75 m**. If the file's NoData marker were not recognised and −9999 were treated as a height, the mean would be (204 − 9999) ÷ 16 ≈ **−612.2 m**, which is how that mistake announces itself.

**Fixture F7 — The blueprint's raster arithmetic grid (synthetic).** The blueprint's Appendix A.3 defines a three-by-three grid used in module 4.4 for the missing-data policy example. It has no stated spacing or position; it is a table of numbers, not a file.

| Row | Column 1 | Column 2 | Column 3 |
| --- | --- | --- | --- |
| 1 | 0 | 10 | 20 |
| 2 | 10 | NoData | 30 |
| 3 | 20 | 30 | 40 |

**Figure 4.0 — The Chapter 4 fixture in the training grid (schematic; described).** Draw Ward A as a 1,000 m square. Divide it into a 10 × 10 grid of 100 m cells and shade the cells according to Table F5b; the pond occupies the top-right corner, a two-cell-high band of built-up cells runs left to right across the middle (y from 400 to 600, straddling Road R1 at y = 500), and three unclassified cells sit in the bottom-left corner. In the top-left 400 m × 400 m, overlay the 4 × 4 elevation grid with its values written in the cells; its one NoData cell is in the bottom row, second column. Mark P1 (200, 200), P2 (800, 800), SL-0113 (205, 195), and DR-0042 (995, 510). Caption: "Synthetic training grid, metres; no real-world location."

---

## 4.1 Introduce the raster model

### 4.1.1 Rows, columns, cells, and values — a numeric grid before any picture

A **raster** is a grid: a matrix of **cells** (also called **pixels**) arranged in **rows** and **columns**, where every cell holds a **value** that describes the area the cell covers [S11] [X01]. That is the whole model. There are no vertices, no rings, and no per-feature attribute records; there is one number per cell per band (bands come in 4.2), and the grid's position in the world is given once, for the whole grid, in a header.

Look at Fixture F7 before you look at any image:

| | Column 1 | Column 2 | Column 3 |
| --- | --- | --- | --- |
| **Row 1** | 0 | 10 | 20 |
| **Row 2** | 10 | NoData | 30 |
| **Row 3** | 20 | 30 | 40 |

Three things are true of every raster and are already visible here:

1. **A cell is addressed by (row, column), not by an identifier.** "Row 2, column 3" is the cell holding 30. There is no `OBJECTID`; the position *is* the identity. Row and column indices in software usually start at 0 [X01]; in this document's tables they are written 1-based and labelled, because that is how a person reads a printed grid.
2. **Every cell has a value, or is explicitly marked as having none.** The middle cell is not blank; it is **NoData**, a stated absence (module 4.4).
3. **The grid has a direction.** Rows are listed from the top down, so row 1 is the northern edge once the grid is placed on a map [X01]. This matters as soon as you convert a coordinate to a row number (4.3.2).

**Definition — raster dataset.** A stored grid of one or more bands of cells, together with the header information that says how many rows and columns it has, how big a cell is, and where the grid sits [X01]. In ArcGIS documentation the same idea is called an *image* or *raster dataset*; in QGIS documentation a *raster layer*. The word *raster* alone means the data model.

**Why the numbers come first.** Software will immediately paint a raster with colours, and the colours look like a finished map. They are not; they are a rendering of the numbers, chosen by a default rule that knows nothing about what the numbers mean (4.2.1). A learner who starts from the picture reads the picture; a learner who starts from the grid asks "what is the value in row 2, column 3, and what does 30 mean?" — the habit this chapter exists to build.

**Developer analogy (and where it stops).** A raster band is a two-dimensional array, `values[row][column]`, and a raster file is that array plus a small header — much like an image buffer in memory. The analogy holds for indexing, for "same shape means same number of rows and columns", and for the cost of storage (a 10,000 × 10,000 grid is a hundred million cells, whatever they contain [S11]). It stops in two places: an image buffer has no idea where it is in the world, whereas a raster's header makes each cell a place with an area; and an image buffer's values are colours, whereas a raster's values are *measurements or codes* that only become colours at display time.

**Common misconception.** "A raster is just an image." Consequence: the learner treats a PNG screenshot of a map as if it were data, tries to "get the elevation" from a map tile, or assumes a cell's colour is stored in the file. A satellite photograph is one kind of raster; a rainfall grid, a fire-risk grid, and an elevation grid are others [S11]. What they share is the grid; what differs is the meaning of the value.

### 4.1.2 Discrete objects versus sampled surfaces

Chapter 3's vector model represents **discrete objects**: a streetlight has a location, a road has a centreline, a ward has a boundary, and each one is a record with attributes that apply to the whole feature. That is exactly the weakness the raster model addresses. The *Gentle Introduction* puts it plainly: attribute values "apply to the whole feature, so vectors aren't very good at representing features that are not homogeneous … all over" [S11]. Ground height, rainfall, temperature, the colour of the ground seen from above — these vary continuously; there is no edge at which "height 13 m" stops and "height 14 m" starts.

A raster represents such a phenomenon by **sampling** it: the area is cut into equal cells and each cell receives one value that stands for the conditions in that cell [S11]. Two kinds of value occur, and the fixture holds one of each:

| Kind | What the value is | Fixture example | Arithmetic that makes sense | Arithmetic that does not |
| --- | --- | --- | --- | --- |
| **Continuous** (a measured quantity) | A number on a scale with units: height in metres, rainfall in millimetres, temperature in degrees | F6: 13.0 means 13.0 m above TD-0 | Mean, difference, "higher than", interpolation between neighbours | Nothing is forbidden, but every result needs its unit |
| **Categorical** (also *thematic* or *discrete*) | A code that stands for a class: 0 = Water, 1 = Built-up | F5: 2 means Vegetation | Count, area per class, "is the same class as", most common class | Mean, sum, "halfway between 0 and 2" — the code 1 that results is *Built-up*, which is not "half water, half vegetation" |

Esri's raster introduction draws the same line between *continuous* data (imagery, elevation, temperature) and *thematic* data (land use, soils) and adds a third category, pictures such as scanned maps [X01]. The QGIS text describes continuous surfaces and also shows a computed raster (average minimum temperature) beside a true-colour image [S11].

**Worked example — the same ground, two representations.** Question: the municipality wants to know how much of Ward A is built-up. Inputs: the Chapter 3 vector asset points and road centreline; Fixture F5. Reasoning: the vector data represents *objects* — three asset points and one road line — and none of them has an area of paving attached. Fixture F5 answers the question directly: 24 cells of code 1, each 1 ha, so **24 ha of the 100 ha ward is classified built-up** (Table F5b). Expected outcome: 24 ha, with two caveats stated alongside it. What the answer does not establish: it says nothing about *which* buildings exist (the raster has no objects), it is only as good as the classification that produced the codes (nothing in the file says how the codes were assigned), and 3 ha of the ward is unclassified, so the true built-up area could be anywhere from 24 ha to 27 ha.

**Developer analogy (and where it stops).** A vector dataset is a table of records; a raster is a sampled signal, like audio samples or a sensor time series but in two dimensions. Sampling has the same trade-off as in signal processing: a coarser sample rate loses detail that cannot be recovered later (4.5.3). The analogy stops at the values: a categorical raster is not a signal at all — it is a grid of enum members — and the signal-processing intuition (average, smooth, interpolate) is exactly wrong for it (4.5.2).

**Common misconception.** "Raster is for images, vector is for data." Consequence: the learner treats an elevation grid as decoration and does not think to compute with it, or tries to model a land-cover *classification* as thousands of polygons when a grid is the natural form. Both models hold data; they hold different kinds.

### 4.1.3 Which model fits depends on the question, not on the thing

The tempting rule — "vector for objects, raster for everything else" — is a shortcut that fails in both directions, and the blueprint is right to forbid teaching it as absolute. Esri's introduction lists points and rainfall as things that "can be stored as either a raster or a feature (vector) data type" and lists reasons to prefer each [X01]. The QGIS text explains both raster-to-vector and vector-to-raster conversion and notes that converting vectors to a raster loses their attribute data [S11]. The decision is made by the *question*:

| Question | Better fit | Why |
| --- | --- | --- |
| "Which streetlight is out?" | Vector | You need the individual object and its record |
| "How much of Ward A is vegetation?" | Raster (F5) | The answer is an area of a class, not a set of objects |
| "How steep is the ground at DR-0042?" | Raster (an elevation grid) | Steepness is a property of a continuous surface |
| "Which ward is P3 in?" | Vector | Containment is a relationship between objects with boundaries |
| "Where are the wettest 10 % of cells after rain?" | Raster | A threshold on a sampled field |
| "How many drains are in each ward?" | Vector, or vector counted per raster cell if a density surface is wanted | Both are legitimate; the *output* form decides |

Three signals that the raster model is the wrong choice for a question, whatever the data looks like:

1. **You need the identity of a thing.** A raster cell of code 1 does not know which building it is part of.
2. **You need exact edges.** A raster's edges are cell edges. Esri's introduction lists "loss of geometric precision" from fitting data to a regular cell boundary among the reasons to prefer vector storage [X01]. The pond in F5 is seven square cells; the real pond is not square.
3. **The object is smaller than a cell.** SL-0113 at (205, 195) falls in a cell classified *Vegetation*. The streetlight has not vanished; a 100 m cell is simply dominated by what covers most of it. If the pixel size is larger than the object of interest, "that object may not exist in the raster dataset" [X01].

And three signals that the vector model is the wrong choice:

1. **The phenomenon has no edges.** Height, rainfall, noise level.
2. **You would need to draw thousands of tiny polygons to capture variation** — the grassland example in the *Gentle Introduction* [S11].
3. **The input is a sensor image.** Imagery "is only available as a raster" [X01].

**Comprehension check 4.1.** Fixture F5 says cell row 5, column 10 holds code 1 and Table F4 says asset DR-0042 sits at (995, 510). A colleague says "so the raster confirms there is a drain there". Explain (a) what F5 actually says about that cell, (b) why the raster cannot confirm or deny the drain, and (c) one question about Ward A that F5 answers better than the vector asset table does.

---

## 4.2 Interpret cell values and bands

### 4.2.1 Stored values are not display colours

Open Fixture F5 in any GIS and it will appear as coloured squares. Which colours is decided by a default rule — ArcGIS Pro, for example, picks a renderer from the number of bands, the pixel type, whether statistics exist, and how many unique values there are, assigning *random* colours when a single-band dataset has 25 or fewer unique values [X11]; QGIS picks *Singleband gray* for a single-band file that has no palette [X21]. Neither rule knows that 0 means water. The colour is a property of the **layer** (Chapter 3.5); the value is a property of the **dataset**. Change the colour and nothing in the file changes; that is the raster version of Chapter 3's "presentation-only change".

The consequence is that **a colour on screen is not evidence of anything until you know the rule that produced it**. A blue cell may be:

| The blue cell could be … | Because … | How to find out |
| --- | --- | --- |
| a **category** (code 0 = Water) | a unique-values renderer assigned blue to code 0 | read the legend *and* the dictionary (Table F5b) |
| an **interval of a continuous value** (heights from 9 to 12 m) | a classified or stretched renderer mapped the low end of the range to blue | read the legend's class breaks and the layer's unit |
| **NoData drawn in a colour** | the renderer was told to paint NoData blue rather than transparent [X02] [X21] | check the NoData display setting |
| **nothing at all** — a transparent cell showing a blue layer underneath | the cell is NoData or was made transparent, and the layer beneath happens to be blue | turn the underlying layer off |

Both platforms let you display NoData as a colour or as no colour [X02] [X21], so the last two rows are not hypothetical.

**Definition — renderer (symbology type).** The rule a layer uses to turn cell values into colours. ArcGIS Pro offers, among others, *Stretch* (values along a colour ramp), *Classify* (a colour per group of values), *Unique Values* (a colour per value, "appropriate for qualitative data such as land cover"), *Discrete*, *Colormap*, and *RGB* for multiband data [X10]. QGIS offers *Singleband gray*, *Singleband pseudocolor* (continuous palette, "e.g. an elevation map"), *Paletted/Unique values* (a colour per value), *Multiband color*, *Hillshade*, and *Contours* [X21]. The names differ; the concepts are the same: **one-colour-per-value** renderers suit categories, **ramp** renderers suit continuous values.

**Worked example — the same cell, three appearances.** Question: what does the cell at row 1, column 8 of F5 "look like"? Input: F5, whose value there is 0. Reasoning: under a unique-values renderer with the Table F5b legend it is blue (Water). Under a default grey stretch from the minimum value to the maximum (0 to 3) it is black, because 0 is the minimum [X10]. If someone then sets the layer's NoData value to 0 by mistake, it becomes transparent — and the pond disappears from the map while the file still says 0. Expected outcome: three different appearances, one stored value. What this does not establish: nothing about the appearance tells you which is "right"; only the dictionary does.

**Developer analogy (and where it stops).** The value-to-colour step is a view-model or a CSS class: `.water { fill: blue }` is styling, not data, and two stylesheets can present one record differently. The analogy stops at *defaults*: a web page with no stylesheet renders as plain text that is still legible, whereas a raster with a default renderer renders as colours that *look* meaningful and are not.

**Common misconception.** "The legend shows me what the values are." Consequence: the learner reports "the north-east is blue, so it is water" from a stretched elevation layer, or reads a random-colour default legend as if it were a classification. A legend shows the *mapping* the layer applies; the value and its dictionary are separate facts.

### 4.2.2 One band and many bands: an elevation grid and an image

So far each cell has held one value. A **band** is one complete matrix of cell values; a raster with several bands holds several spatially coincident matrices covering the same cells [X04]. A **single-band** raster has one value per cell; a **multiband** raster has one value per cell *per band*.

**One band: elevation.** Fixture F6 is single-band: each cell holds one height. Esri gives a digital elevation model as the standard example of a single-band raster, "each pixel … contains only one value representing surface elevation" [X04]. A single-band raster is displayed either as grey levels or through a colour ramp or colour map [X04].

**Several bands: imagery.** A colour photograph is stored as three bands — the red, green, and blue components — that the screen combines [S11]. Satellite and aerial sensors record more: bands for parts of the spectrum the eye cannot see, such as near-infrared, which "can be useful in identifying water bodies" [S11]. Esri's band page notes that Landsat-9 imagery has 11 bands, and that a natural-colour orthoimage has three [X04]. The number of bands is the image's **spectral resolution** [S11]. Esri's pixel page gives the vocabulary you will meet in metadata: a single band covering a wide part of the visible spectrum is *panchromatic*; three or more bands make an image *multispectral*; a hundred or more make it *hyperspectral* [X05].

**A tiny synthetic three-band example.** Suppose a 2 × 2 image with bands Red, Green, Blue (values 0–255, synthetic):

| Cell | Red | Green | Blue | Combined on screen |
| --- | --- | --- | --- | --- |
| Row 1, col 1 | 200 | 60 | 40 | reddish-brown |
| Row 1, col 2 | 34 | 120 | 200 | blue |
| Row 2, col 1 | 40 | 160 | 50 | green |
| Row 2, col 2 | 250 | 250 | 250 | near-white |

Each cell has *three* values, and "the value of the cell" is not a meaningful phrase until you say which band. Displaying the image means choosing which band feeds each of the screen's red, green, and blue channels; both platforms let you choose any three bands for the composite [X04] [X21], and feeding the near-infrared band to the red channel produces the "colour infrared" display in which healthy vegetation appears red [X10].

**How deep to go with spectral bands.** For this course, only as far as reading metadata: when a dataset says "4 bands: Blue, Green, Red, NIR", you should know that NIR is near-infrared, that band order is a property of the file and not a convention, and that a band is a full grid with the same rows, columns, cell size, and extent as the others [X04]. Interpreting reflectance, indices, or classifications is remote-sensing science and is outside Phase 1 by the blueprint's boundary.

**Developer analogy (and where it stops).** Bands are channels in an image buffer (RGBA) or columns in a wide table keyed by (row, column). The analogy holds for storage and indexing. It stops at *meaning*: RGBA channels have fixed roles, whereas raster bands are just numbered matrices whose meaning ("band 4 is NIR") lives in metadata that may be absent.

**Common misconception.** "A raster has *a* value per cell." Consequence: code that reads `band 1` of a three-band image and reports it as "the" value; or a report that says "the pixel value is 34" for a cell that is (34, 120, 200). Always state the band.

### 4.2.3 What you need before a value means anything

A value is a number. To make it a *fact about the ground* you need, in every case, an answer to two questions: **what kind of quantity is this** and **in which unit or dictionary**?

| Raster | Value seen | Needed to interpret it | Where it should be found | If missing |
| --- | --- | --- | --- | --- |
| F5 | 2 | The category dictionary (Table F5b) | The dataset's documentation, a raster attribute table, or a colour map | The value is an integer with no meaning; do not guess that 2 "looks like" vegetation |
| F6 | 13.0 | The unit (metres) and the vertical zero (TD-0) | Dataset documentation; the vertical coordinate system if one is defined (4.6.2) | 13.0 could be metres or feet; a surface that "looks" like a hill has the same shape in either |
| A three-band image | (34, 120, 200) | Which band is which, and the value range | Band metadata; the sensor's documentation | The composite can still be displayed, but nothing can be measured |
| A rainfall grid | 0 | Whether 0 means "measured, no rain" or "not measured" | The NoData definition (4.4) | The statistic will be wrong (4.4.2) |

The blueprint's instruction is exact: **never infer elevation units from appearance.** A rendered surface in metres and the same surface in feet look identical; only the numbers differ by a factor of about 3.28. The same applies to categories: the fact that Esri's default renderer paints code 0 a random colour [X11] tells you nothing about the class.

Esri's raster introduction states the possible meanings of a value plainly — "a spectral value, category, magnitude, or height" — and adds that integers "are best used to represent categorical (discrete) data and floating-point values are well suited to represent continuous surfaces" [X01]. That gives a useful *hint*: a floating-point raster is probably continuous; an integer raster might be either (integer elevation grids are common). A hint is not a dictionary.

**Platform note — the pixel type is visible, the meaning is not.** ArcGIS Pro's raster properties list the pixel type (signed/unsigned, integer/floating point), the pixel depth, the NoData value, and whether a colour map is present [X06]; QGIS's *Information* tab lists the data type, band statistics, and NoData values [X21]. Both tell you *how* the numbers are stored. Neither can tell you what the numbers *mean*; that comes from documentation, which Chapter 7 treats formally as metadata.

**Worked example.** Question: a colleague sends `elevation_training.asc` with no covering note and asks for "the highest point". Inputs: F6. Reasoning: the file's header gives dimensions, origin, cell size, and NoData value; it gives no unit and no vertical reference. The maximum valid value is 21.0 at row 1, column 4. Expected outcome: "the highest cell value is 21.0, in the north-east cell (x 300–400, y 900–1000); the unit and zero level are not stated in the file — in this course's fixture they are metres above TD-0, but that must come from the fixture documentation, not from the file." What this does not establish: whether 21.0 is the highest *point* — it is the highest of sixteen cell values, each standing for a 100 m × 100 m area; anything inside a cell is unmeasured.

**Comprehension check 4.2.** A single-band raster displays with a red-to-green ramp and the legend reads "9 … 21". List three different things the value 21 could be, and state the single piece of information that would settle it.

---

## 4.3 Explain resolution, extent, and alignment

### 4.3.1 Cell size, spatial resolution, and why neither is accuracy

**Definition — cell size (pixel size).** The width and height of one cell in the raster's coordinate units — here 100 m, because the fixture's header says `CELLSIZE 100` and the training grid is in metres. Cells are usually square, but the two dimensions can differ, which is why ArcGIS Pro reports "Cell size (x, y)" [X06].

**Definition — spatial resolution.** The level of ground detail a raster can represent, determined by its cell size: a smaller cell means finer detail [S11] [X03]. "Images with a pixel size covering a small area are called 'high resolution'" [S11]. Esri's page states the convention directly: if a pixel covers 5 m × 5 m, "the resolution is 5 meters" [X03].

So cell size and spatial resolution are two ways of saying one thing. Three consequences follow, and the blueprint asks that the third be made unmistakable.

**First: resolution decides what can exist in the raster.** An object smaller than a cell is not represented; "if the pixel size is larger than the object of interest, that object may not exist in the raster dataset" [X01]. In F5 the streetlight SL-0113 is inside a *Vegetation* cell; a 100 m classification cannot see a streetlight, and no styling will make it appear.

**Second: resolution has a cost.** Halving the cell size quadruples the number of cells for the same area; Esri gives the figure "as much as four times the storage space" [X01], and the QGIS text's South Africa example (a million cells at 1 km) makes the same point [S11]. Higher resolution is not free and is not always wanted: cloud maps for a national weather report are deliberately coarse [S11].

**Third — and most important: cell size is not positional accuracy.** A cell size of 1 m says that the grid is *divided* into 1 m squares. It does not say that the values are *placed* within 1 m of where they belong, nor that the value in a cell was measured with 1 m precision. Accuracy depends on how the data was captured and georeferenced (Chapter 9 introduces georeferencing; Esri's overview notes that scanned maps "usually do not contain spatial reference information" and must be aligned using control points, and that a georeferenced dataset "is only as accurate as the data to which it is aligned" [X18]). Esri's pixel-size page makes the complementary point for analysis: results "are only as accurate as the least accurate dataset", so pairing a 30 m classification with a 10 m elevation model "may be unnecessary" [X03].

**Developer analogy (and where it stops).** Cell size is like the number of decimal places in a printed number: `3.14159265` has more digits than `3.1`, but if the measurement behind it was made with a school ruler, the extra digits are noise, not information. Precision (how finely the value is expressed) and accuracy (how close it is to the truth) are different, and a finer grid is finer precision only. The analogy stops in one respect: with numbers, extra digits are harmless clutter; with rasters, a finer grid costs storage and processing and can mislead a reader into trusting the position of every edge.

**Common misconception.** "This raster is 1 m resolution, so I can locate the pipe to 1 m." Consequence: an excavation team digs in the wrong place because a 1 m grid derived from a 10 m survey was taken at face value. This is Chapter 1's fitness-for-purpose lesson in raster form. The remedy is to ask *how the grid was made*, which is a metadata question (Chapter 7).

### 4.3.2 Metre-grid arithmetic: dimensions, extent, and finding a cell

The header of an Esri ASCII raster carries four numbers that, together, fix every cell's position: the number of columns and rows, the cell size, and the coordinate of the lower-left corner of the lower-left cell [X07]. Esri lists the same four "geographic properties" for any raster — a coordinate system, a reference corner coordinate, a pixel size, and the count of rows and columns — and notes that with them "the location of any specific pixel" can be found [X01]. The QGIS text describes the same information (a corner coordinate, the pixel size in X and Y, and any rotation), stored in a header or a small accompanying text file [S11]. *This chapter's fixtures have no coordinate system; the arithmetic below is pure planar arithmetic on the training grid and says nothing about the Earth.*

**The blueprint's arithmetic, first.** Ten columns of ten-metre cells span one hundred metres: 10 × 10 m = 100 m. That is all "extent" is: **count × cell size**, added to the origin. It is idealised grid arithmetic and is exact; it carries no statement about how accurately the grid sits on the ground (4.3.1).

**Extent of F5.** Origin (lower-left corner) = (0, 0); 10 columns × 100 m = 1,000 m wide; 10 rows × 100 m = 1,000 m high. Extent: x from 0 to 1,000, y from 0 to 1,000 — Ward A exactly. Both platforms report an extent as the left, right, top, and bottom coordinates of the covering rectangle [X06] [X21]; for F5 that is left 0, right 1000, bottom 0, top 1000.

**Extent of F6.** Origin (0, 600); 4 × 100 m each way. Extent: x from 0 to 400, y from 600 to 1,000. Total area 16 ha; valid area 15 ha.

**From a coordinate to a cell.** Given a point (x, y) inside the extent, with origin (x₀, y₀), cell size *c*, and *R* rows:

1. Column index from the left, 0-based: `col = floor((x − x₀) / c)`.
2. Row index from the **bottom**, 0-based: `rowFromBottom = floor((y − y₀) / c)`.
3. Row index from the **top**, 0-based: `row = R − 1 − rowFromBottom`. (Grids are listed top-down [X01]; world y increases upward. Esri's world-file page explains the same flip: "row values in the image increase from the origin downward, while y-coordinate values in the map increase from the origin upward" [X13].)

Add 1 to each index to get the 1-based row and column used in this document's tables.

**Worked example — which class is DR-0042 in?** Inputs: DR-0042 at (995, 510); F5 with origin (0, 0), *c* = 100, *R* = 10. Reasoning: `col = floor(995 / 100) = 9` (0-based) → column 10. `rowFromBottom = floor(510 / 100) = 5` → `row = 10 − 1 − 5 = 4` (0-based) → row 5. Row 5, column 10 of the listing is **1 = Built-up**. Check: row 5 covers y from 500 to 600 and column 10 covers x from 900 to 1000; (995, 510) is inside both. Expected outcome: the drain sits in a built-up cell, which is plausible for a drain beside Road R1. What this does not establish: anything about the drain itself (4.1.3).

**Worked example — SL-0113.** (205, 195): `col = floor(205/100) = 2` → column 3; `rowFromBottom = floor(195/100) = 1` → `row = 10 − 1 − 1 = 8` → row 9. Row 9, column 3 = **2 = Vegetation**.

**Boundary case — P1 sits exactly on a cell corner.** P1 is at (200, 200). Both coordinates divide exactly by the cell size (200 ÷ 100 = 2 in x and in y), so P1 lies on the shared corner of four cells: (row 8, col 2) = 2, (row 8, col 3) = 1, (row 9, col 2) = NoData, and (row 9, col 3) = 2. The formula above gives column 3, row 8 → code 1, *Built-up* — but that is a consequence of choosing `floor`, that is, of deciding that a cell's left and bottom edges belong to it and its right and top edges do not. A different engine may decide differently. This is the raster counterpart of Chapter 1's boundary rule for P5 on the ward edge: **a point on a cell edge has no value until you state the tie-breaking rule**, and the rule is implementation-specific. The lab asks you to observe what your software does (4.7) rather than assume. P2 at (800, 800) is the same situation, with neighbouring codes 0, 0, 2, and 0.

**Developer analogy (and where it stops).** Converting a coordinate to a row and column is integer division with an origin offset, the same as mapping a screen coordinate to a tile index. It stops at the *y flip* (screen y and image row both grow downward, but map y grows upward, so one of the two must be reversed) and at the edge rule, which most tile code ignores because tiles are seldom queried at exact boundaries.

**Common misconception.** "Row 1 is at the bottom because y starts at 0 there." Consequence: every lookup is mirrored top-to-bottom, so DR-0042 is reported as Vegetation instead of Built-up. The listing's first row is the top row; the header's `YLLCORNER` is the bottom edge. Both are true at once.

### 4.3.3 Alignment preview: same cell size, different grids

Two rasters with the same cell size are not necessarily on the same grid. A grid is fixed by its origin as well as its cell size; if one raster's origin is offset from another's by anything other than a whole number of cells, their cell edges do not coincide, and there is no cell in one that corresponds exactly to a cell in the other.

**Figure 4.3 — Two 100 m grids (schematic).** Draw F6's four columns with edges at x = 0, 100, 200, 300, 400. Beneath it draw a second, hypothetical grid "Rain_2026 (synthetic)" with the same 100 m cells but an origin at x = 50: edges at 50, 150, 250, 350, 450. Every Rain cell overlaps two F6 cells by half. Caption: "Same cell size, different origin: no cell-to-cell correspondence."

Three cases, from harmless to serious:

| Case | Example | Consequence |
| --- | --- | --- |
| Same cell size, origins differ by a whole number of cells, extents overlap | F5 (origin (0, 0)) and F6 (origin (0, 600)); 600 is 6 × 100 | The grids **align**: F6's cells coincide exactly with F5's rows 1–4, columns 1–4. Cell-by-cell comparison is meaningful. |
| Same cell size, origin offset by a fraction of a cell | F6 and the hypothetical Rain grid | The grids **do not align**. Comparing "this cell with that cell" requires resampling one of them (4.5), which changes values. |
| Different cell sizes | F5 (100 m) and a 30 m classification | Neither aligns with the other; any combination requires a choice of common cell size *and* origin, and the finer grid does not make the coarser one more detailed (4.5.3). |

Both platforms expose the settings that control this when rasters are processed together — ArcGIS Pro's geoprocessing environments for Resample include *Cell Size*, *Extent*, and *Snap Raster* [S12]; QGIS's GDAL *Warp* exposes a target resolution and extent [X22] — but **configuring them is later training**. What you need now is the ability to *notice*: two rasters whose extents or origins differ by odd amounts (an origin of 0 and an origin of 50; a cell size of 100 and one of 30) will not line up, and any cell-to-cell comparison between them involves a hidden resampling step.

**Common misconception.** "Both rasters are 100 m, so I can subtract one from the other." Consequence: a difference grid that compares each cell with a half-shifted neighbour; the result looks plausible and is wrong everywhere. Check origins and extents (Table 4.3 in the lab records both) before believing any cell-by-cell result.

**Comprehension check 4.3.** A raster header reads `NCOLS 40, NROWS 25, XLLCORNER 1000, YLLCORNER 0, CELLSIZE 25`. (a) State its extent. (b) Give the 1-based row and column of request P4 at (1700, 900), or explain why it has none. (c) Does this raster align with F5? Say why.

---

## 4.4 Treat NoData explicitly

### 4.4.1 Zero, NoData, masked, and transparent are four different things

The most consequential distinction in this chapter is between **a value of zero** and **no value**. They look alike in a listing, they can look alike on a map, and they are opposites in meaning.

| Term | What it states | Stored as | Fixture example | Counts toward statistics? |
| --- | --- | --- | --- | --- |
| **Zero** | "Measured; the quantity is 0" or "the category coded 0" | The number 0 | F7 row 1, column 1: 0; F5 code 0 = Water | **Yes** — it is a measurement |
| **NoData** | "There is no value here"; the absence of data [X01] [X02] | Either a reserved value that is not used for real data (−9999 is common [X02]; F6 uses −9999.0, F5 uses −1) or a separate mask that is part of the dataset [X02] | F6 row 4, column 2; F5's three unclassified cells | **No** — under any sane policy; see 4.4.2 for what happens when it does |
| **Masked** | "Ignore these cells for this operation" — an instruction applied at processing time, often from another dataset or a boundary | Not in the raster's values at all; an environment setting or a mask function [X02] | "Compute statistics for Ward A only": cells outside Ward A are masked | Excluded from the operation, but still present in the source |
| **Transparent** | "Do not paint these cells" — a display setting | Layer symbology only [X02] [X21] | NoData drawn as no colour; or the value 0 made transparent by mistake | Irrelevant to statistics; the values are untouched |

Two of the four are properties of the *data* (zero and NoData); one is a property of a *process* (mask); one is a property of a *layer* (transparency). Confusing any two of them produces a specific error:

- **Zero treated as NoData**: the pond (code 0) disappears from F5's map and from its area totals; a rainfall grid loses every dry cell, so "average rainfall over the wet cells" is reported as "average rainfall".
- **NoData treated as zero**: the missing height in F6 becomes 0 m and drags the mean down (4.4.2); three unclassified hectares in F5 become "3 ha of Water" if 0 is the water code — which is why F5 uses −1 and not 0 as its NoData marker.
- **Transparent mistaken for NoData**: a cell that has been styled invisible is assumed to hold no data, and a real value is overlooked.
- **NoData mistaken for "outside the study area"**: 4.4.3.

**Where zero is legitimate — the blueprint asks for examples.** Fixture F7's top-left cell is 0 and is an observation. In F6, a height of 0.0 m would mean "exactly at the TD-0 level" and would be a perfectly good measurement. In a rainfall grid, 0 mm is the most common valid value. In F5, 0 is the *code* for Water and has no numeric meaning at all. In a count-of-requests grid (4.4.3), 0 means "we looked and found none". A raster that uses 0 for NoData is therefore only safe when 0 can never be a real value — and Esri's NoData page carries exactly this caution: 0 "may be used as a default value for cells that don't have a valid value, but 0 may also be used to define valid values", in which case a mask is needed instead [X02].

**Platform note — how NoData is stored and set.** ArcGIS Pro stores NoData either as a mask that is part of the dataset or as a reserved pixel value; when a raster with NoData is loaded into a geodatabase a bit mask is generated, and adding NoData to a file-based raster that already uses its full value range promotes it to a larger pixel type [X02]. The NoData value can be inspected and edited per band in the raster's *Properties* (General tab, *Raster Information*, *NoData Value*), and the page warns not to choose a value that is also valid in the data [X02]. QGIS reports the source's NoData value in the layer's *Transparency* properties and lets you add an *Additional no data value* for display, and choose to *Display no data as* a colour instead of the default transparent rendering [X21]. **In both, setting a NoData value in layer properties is a decision about which stored number is to be read as "absent"; it does not delete or overwrite anything.**

**Developer analogy (and where it stops).** NoData is SQL `NULL`. `AVG(height)` ignores `NULL`; `AVG(COALESCE(height, 0))` does not, and the two answers differ. A mask is a `WHERE` clause. Transparency is a CSS `visibility: hidden` on a cell that is still in the DOM. The analogy holds well — this is the single most useful thing a developer already knows about NoData. It stops at *storage*: a database stores `NULL` as a distinct state, whereas a raster file often stores NoData as an ordinary number (−9999) that only the header declares to be special; strip the header and the absence becomes a very negative height.

**Common misconception.** "The blank cells are zero." Consequence: the mean of F6 is reported as 12.75 m instead of 13.6 m (4.4.2), and 3 ha of unclassified ground in F5 is reported as water. The fix is procedural, not conceptual: always read the NoData value from the properties before computing anything, and always state the policy you applied.

### 4.4.2 How a statistic changes when missing values are treated as zero — and stating the policy

Take the blueprint's grid (Fixture F7) and compute its mean twice.

**Policy A — exclude NoData (the usual intention).** Valid cells: 0, 10, 20, 10, 30, 20, 30, 40 — eight of them. Sum = 0 + 10 + 20 + 10 + 30 + 20 + 30 + 40 = **160**. Mean = 160 ÷ 8 = **20**.

**Policy B — NoData treated as 0 (the usual mistake).** Nine cells, sum still 160 (the added value is 0). Mean = 160 ÷ 9 = **17.78** (rounded to two decimals; exactly 17.777…).

The difference is 2.22 in a grid whose values run from 0 to 40 — an 11 % understatement of the mean from *one* missing cell out of nine. Notice which direction the error went: treating absence as zero always pulls the mean toward zero, and pulls it harder the more cells are missing. In F6 the same mistake gives 12.75 m against the correct 13.6 m (fixture facts above); in F5 it would create 3 ha of a class that was never observed.

Other statistics move too, and not all in the same direction:

| Statistic of F7 | Policy A (exclude NoData) | Policy B (NoData = 0) | What changed |
| --- | --- | --- | --- |
| Count of cells | 8 | 9 | The denominator |
| Sum | 160 | 160 | Nothing — which is why "the sum is the same" proves nothing |
| Mean | 20 | 17.78 | Pulled toward zero |
| Minimum | 0 | 0 | Nothing here, because a real 0 exists; in F6 the minimum would drop from 9.0 to 0.0 |
| Maximum | 40 | 40 | Nothing |

**The rule that follows.** Every operation that touches a raster with NoData has a **missing-data policy**, whether or not anyone chose it. Esri's NoData page names the three possibilities: the operation returns NoData for the location "no matter what"; NoData is ignored and a value is computed from whatever is available; or a value "must be estimated, and NoData cannot be returned" [X02]. Which one applies is a property of the specific tool and its settings — the blueprint is explicit that the F7 exercise "does not establish the behavior of every raster statistics function; inspect the chosen function's settings." In ArcGIS Pro's *Calculate Statistics* tool, for example, an *Ignore Values* parameter excludes stated values from the calculation, and the tool's page names NoData as a reason to use it [X12]; QGIS's *Raster layer statistics* algorithm reports count-free minimum, maximum, sum, mean, and standard deviation, and its documentation does not state its NoData handling [X24], so it must be checked (Verification item V3 in the Instructor Appendix). **When you report a statistic, report the policy with it**: "mean height 13.6 m over the 15 valid cells; 1 cell (6 % of the extent) has no data."

**Worked example — a percentage, not a mean.** Question: what fraction of Ward A is vegetation according to F5? Inputs: Table F5b. Reasoning under Policy A: 56 vegetation cells out of 97 classified cells = 57.7 %. Under a "denominator is the whole ward" policy: 56 of 100 cells = 56.0 %. Under Policy B, where NoData becomes code 0: the three cells become Water, the water count rises to 10, and vegetation is still 56 of 100 = 56.0 % — the same number as the honest whole-ward figure, but now the water figure is wrong. Expected outcome: state one of the first two, with its denominator; "56 %" alone is ambiguous. What this does not establish: what is *under* the cloud; the three cells could be anything.

**Developer analogy (and where it stops).** This is `AVG` versus `AVG(COALESCE(…, 0))` again, plus the difference between dividing by `COUNT(*)` and `COUNT(column)`. The analogy is exact for the arithmetic. It stops at *discovery*: in SQL you can see the `NULL` in the row; in a raster you must first learn which number means NoData, and a tool may silently apply either policy.

**Common misconception.** "Sum is unaffected, so the missing cell does not matter." Consequence: a report built on sums (total area, total volume) looks consistent while every mean, proportion, and density derived from it is wrong. A statistic's denominator is where NoData bites.

### 4.4.3 Missing coverage is not "nothing observed"

A NoData cell says *we do not know*. A cell holding 0 in a count grid says *we looked and found none*. The two are different statements, and a map that renders both as blank makes them indistinguishable.

**Worked example — a request-count grid (synthetic).** Suppose the municipality counts service requests per 100 m cell over Ward A for August 2026 and stores the result as a raster on F5's grid, but the counting script only covers cells where the classification exists (it uses F5 as a mask). The three unclassified cells in the bottom-left receive NoData; every other cell receives a count, and most counts are 0. Question: "How many requests were in the bottom-left cell (row 10, column 1)?" Reasoning: the cell is NoData, so the answer is **"not counted"**, not "zero". If P1 at (200, 200) had been reported in that corner instead, it would have been silently lost. Expected outcome: the report distinguishes "0 requests (counted)" from "not counted (no coverage)", and states the coverage: 97 of 100 cells. What this does not establish: whether requests occurred in the uncounted cells.

This is the raster form of a distinction Chapter 1 drew for tables — a request that was never recorded is not a request that did not happen — and it becomes a data-quality rule in Chapter 9. Three habits enforce it:

1. **Render NoData visibly** during inspection (a hatched or contrasting colour), and switch to transparent only for presentation. Both platforms allow either [X02] [X21].
2. **Report coverage** with every raster statistic: how many cells, how much area, has no data.
3. **Never fill NoData with zero "to make the tool run"** unless the phenomenon genuinely has a zero there and you document that decision. Zero is a claim.

**Comprehension check 4.4.** Fixture F6 has 16 cells and one NoData cell. A tool reports "mean = 12.75". (a) What policy did the tool apply, and how do you know? (b) What would the tool have reported if the NoData marker had not been recognised at all? (c) Write the one-sentence statistic you would put in a report, including the policy and the coverage.

---

## 4.5 Introduce resampling and its consequences

### 4.5.1 Why changing a grid means choosing how new values are made

**Definition — resampling.** Producing a new raster on a different grid — a different cell size, a different origin, or (later, Chapter 6) a different coordinate system — from an existing raster. Because the new cells do not coincide with the old ones, **each new cell's value has to be derived from the old cells by a rule**, and that rule is the **resampling method**. ArcGIS Pro's *Resample* tool describes itself as changing "the spatial resolution of a raster dataset" while setting "rules for aggregating or interpolating values across the new pixel sizes" [S12].

The need for a rule is not a software quirk; it is arithmetic. Take F6's top-left cell (12.0 m, covering x 0–100, y 900–1000) and ask for 50 m cells instead. Four new cells now sit where one old cell was. What value does each get? There is no fact in the data that answers this — the surface was never measured inside the old cell — so a method must decide.

**Three methods, conceptually.** The blueprint names three; ArcGIS Pro's tool offers four. Descriptions below follow the tool page [S12]:

| Method | How the new cell's value is chosen | Creates new values? | Suited to | Not suited to |
| --- | --- | --- | --- | --- |
| **Nearest neighbour** ("Nearest") | Takes the value of the single input cell whose centre is nearest the output cell's centre. "It will not change the values of the cells"; the maximum spatial error is one-half the cell size [S12] | **No** | Categorical (discrete) data such as land cover [S12]; also any case where original values must be preserved | Smooth appearance of continuous surfaces (produces a blocky result) |
| **Bilinear interpolation** | "A weighted distance average of the four nearest input cell centers"; "useful for continuous data and will cause some smoothing" [S12] | **Yes** — values between the inputs | Continuous surfaces (elevation, temperature) | Categorical data: "should not be used with categorical data, since the cell values may be altered" [S12] |
| **Cubic convolution** ("Cubic") | Fits "a smooth curve through the 16 nearest input cell centers"; smoother than bilinear, but "may result in the output raster containing values outside the range of the input" [S12] | **Yes** — possibly outside the input range | Continuous surfaces where smoothness matters | Categorical data (same reason); any case where values must stay within the measured range |
| **Majority** (ArcGIS Pro; QGIS's GDAL tools offer *Mode*, a related idea) | The most common value among the input cells nearest the output cell centre (a 4 × 4 window in the Esri tool) [S12] | **No** | Categorical data; "tends to give a smoother result than Nearest" [S12] | Continuous data |

QGIS's GDAL-based tools expose a longer list — nearest neighbour, bilinear, cubic, cubic B-spline, Lanczos, average, mode, maximum, minimum, median, and quartiles [X22] — because GDAL implements more kernels [X28]. The three concepts above are enough to reason about any of them: *does the method copy an existing value, or compute a new one, and if it computes, can it leave the input range?*

**Two very different places where resampling happens.** Both platforms also resample *at display time*, every time you zoom, to fit the raster's cells to screen pixels. QGIS's symbology tab has a *Resampling* section whose effect is explicitly "when you zoom in and out of an image", where nearest neighbour "can get a pixelated structure" and bilinear or cubic blur the edges [X21]; ArcGIS Pro sets a default display resampling from the raster's *Source Type* — bilinear for *Elevation*, nearest neighbour for *Thematic* [X06] [X11]. **Display resampling changes only what you see; the dataset is untouched.** The *Resample* tool and GDAL's *Warp*/*Translate* create a **new dataset** whose values are permanently derived by the chosen method. This chapter's lab does the second, on copies, so the effect can be measured.

**Developer analogy (and where it stops).** Resampling is image scaling: nearest neighbour is the "pixelated" scale-up of a sprite, bilinear and cubic are the "smooth" scale-ups a browser applies to a photograph. The analogy is good for the *look* of each method. It stops at the *meaning* of the values: a browser only needs the result to look right, whereas a resampled elevation grid will be *measured* — and a resampled land-cover grid contains codes, for which "smooth" is meaningless (4.5.2).

**Common misconception.** "Bilinear is the better method because the result looks smoother." Consequence: a land-cover raster is resampled with bilinear and acquires codes that were never on the ground (4.5.2), or an elevation grid is resampled with cubic and gains a peak higher than any measured height. "Better" depends on what the values are.

### 4.5.2 Averaging category codes creates meaningless categories

Resample Fixture F5 to 50 m cells with bilinear interpolation, and consider an output cell whose centre lies a quarter of the way from a Water cell (code 0) toward a Vegetation cell (code 2), with Water above and Vegetation below as well. Bilinear takes a distance-weighted average of the four nearest input centres [S12]; with weights of three-quarters on the two Water neighbours and one-quarter on the two Vegetation neighbours, the value is 0.75 × 0 + 0.25 × 2 = **0.5**. Move the output cell a little and the weights reverse: 0.25 × 0 + 0.75 × 2 = **1.5**. Neither number is in Table F5b. If the output is stored as an integer, 0.5 and 1.5 are rounded or truncated to **0, 1, or 2** depending on the engine — and **1 is Built-up**.

Nothing built has appeared on the ground. The tool has done exactly what it was asked — computed an average — and the average of two codes is either a number with no meaning or a third code that means something unrelated. The second case is worse, because 1 is a *valid* code and nothing flags it: the output legend shows a thin strip of Built-up around every pond in the ward, and a later area calculation counts it. With codes 0 and 3 (Water and Bare ground) the same cell would receive 0.75 or 2.25, which round to *Built-up* or *Vegetation*. This is why Esri's tool page states that bilinear and cubic "should not be used with categorical data, since the cell values may be altered" [S12]. The lab (4.7) has you produce and record one such cell.

**Why nearest neighbour is the usual choice for categories — and why that is not a universal law.** Nearest neighbour never invents a value; every output cell holds a code that some input cell held [S12]. That is the property categorical data needs. But the blueprint's caution is correct: nearest neighbour is *typical*, not *best in every case*. When *reducing* resolution (say 100 m to 500 m), nearest neighbour keeps whichever single cell happens to be nearest the new centre and discards the other 24, so a class that covers most of the new cell can vanish while a one-cell class survives by luck; *majority* (or GDAL's *mode*) picks the most common class instead and is often the better choice for aggregation [S12]. The rule is not "nearest for categories"; it is **"never compute arithmetic on codes, and choose among the non-arithmetic methods by what the aggregation should mean."**

**Developer analogy (and where it stops).** Category codes are members of an `enum`. `(RED + BLUE) / 2` is a type error in any language that takes enums seriously; a raster stores enums as integers, so the type checker is you. The analogy stops at *aggregation*: an enum has no "most common member of a group" operation built in, whereas majority resampling is exactly that, and it is meaningful.

**Common misconception.** "The software would not let me choose a method that is wrong for my data." Consequence: bilinear is applied to a land-cover grid because it was the default for the previous dataset. ArcGIS Pro's tool offers all four methods for any raster [S12]; QGIS's GDAL tools offer all of GDAL's [X22]. The *Source Type* property in ArcGIS Pro influences the *default display* resampling [X11], not what a processing tool will accept.

### 4.5.3 A smaller output cell does not recover unmeasured detail

Resampling F6 from 100 m to 50 m cells produces a 8 × 8 grid — four times as many cells. It does not produce four times as much information. Esri's pixel-size page is direct: "resampling an image to have a smaller pixel size does not produce greater detail" [X03]. The original grid measured one value per 100 m cell; nothing inside those cells was ever observed, and no method can observe it now. What the methods do is *fill* the new cells:

| Method | The 8 × 8 output looks like … | What it adds |
| --- | --- | --- |
| Nearest neighbour | The 4 × 4 grid with each cell copied into a 2 × 2 block — the same picture, drawn with more squares | Nothing. Every statistic over the valid area is unchanged (the lab checks this: the mean stays 13.6 m) |
| Bilinear | A smoother surface, with values sloping gently from cell to cell | Plausible-looking *interpolated* values that are guesses under the assumption that the surface varies linearly between cell centres |
| Cubic | Smoother still | Guesses under a different assumption; possibly values outside the measured range [S12] |

**Figure 4.5 — Enlarging a grid (schematic, an illustration only, not evidence of improved accuracy).** Left: F6's top-left 2 × 2 block (12, 14 / 11, 13) drawn as four large squares. Middle: the same block as sixteen small squares after nearest-neighbour resampling — each large square has become four identical small ones. Right: the sixteen small squares after bilinear resampling, with values graded between 11 and 14. Caption: "Three drawings of the same four measurements. The right-hand grid is not more accurate; it is more interpolated."

**When a smaller cell is nonetheless right.** Resampling to a finer grid is legitimate when the purpose is *alignment* — bringing a coarse raster onto the grid of a finer one so that they can be combined cell by cell (4.3.3) — provided the report says which layer was the coarse one. Esri's pixel-size page describes the mirror-image practice of keeping a copy at the finest, most accurate cell size while resampling to match the coarsest for analysis [X03]. What is never legitimate is describing the finer output as "1 m data" when its source was 10 m.

**Developer analogy (and where it stops).** Upscaling a 100 × 100 photograph to 400 × 400 does not add pixels' worth of information; it adds interpolated pixels. Everyone who has enlarged a small image knows this, and the intuition transfers exactly. The analogy stops at *plausibility*: an upscaled photograph looks blurry and warns the eye, whereas a bilinearly upscaled elevation grid looks *better* — smoother, more natural — than the honest blocky original, and warns nobody.

**Comprehension check 4.5.** You are given a 30 m land-cover raster and asked for a 10 m version "so it matches the building footprints". (a) Which method would you choose and why? (b) Name one thing the 10 m output will show that the 30 m input did not, and one thing it will not. (c) Write the sentence you would put in the output's documentation.

---

## 4.6 Connect elevation rasters to 3D

### 4.6.1 A height field: one height per cell, and what that can and cannot represent

Fixture F6 is an **elevation surface**: a raster whose cell values are heights. Rasters are "well suited for representing data that changes continuously across a landscape (surface)" [X01], and an elevation model is the commonest example — QGIS's text defines a digital elevation model as "a kind of raster where each pixel contains the height above sea level" [S11], and Esri's as "a raster representation of a continuous surface, usually referencing the surface of the earth" [X14].

The representation is a **height field**: for every (row, column) — that is, for every (x, y) cell — there is exactly **one** height. Draw F6 as a surface by placing each cell's value on a vertical axis above the cell, and you have a stepped landscape rising from 9 m in the south-west to 21 m in the north-east.

That "exactly one" is the model's limit. A height field cannot hold two heights at one (x, y), so it cannot represent an overhang, a bridge with a road beneath it, a tunnel, or the inside of a building; it represents *the* surface, whichever surface it was built to represent. Which brings the essential question:

**Which surface?** Esri's documentation distinguishes two products with the same structure and different content [X15]:

| Term | What the heights are | Contains buildings and trees? | Typical use |
| --- | --- | --- | --- |
| **Digital terrain model (DTM)** — "the digital elevation of the earth, not including the elevation of any objects on it", also called *bare-earth* elevation [X15] | Ground height | No | Drainage, slope, flood modelling, orthorectification |
| **Digital surface model (DSM)** — "the digital elevation of the earth, including the elevation of objects on it such as trees and buildings" [X15] | Height of whatever is first seen from above | Yes | Line-of-sight, building height, canopy studies |
| **Digital elevation model (DEM)** | Used generically for either, and often for a bare-earth model [X14] | Depends on the dataset | — |

The names are not standardised across publishers, which is why the blueprint requires *the dataset's own definition*. A file called `dem.tif` might be either. The only reliable answer to "does this surface include the roof of the depot?" is the dataset's documentation (Chapter 7). Esri's DEMs page adds a practical consequence of the difference: under dense forest a DTM cannot be derived at all, because the ground is not visible, and a DSM of the canopy is the appropriate product [X15].

**Worked example — a drain and two surfaces (synthetic).** Question: "How far above the training datum is the ground at DR-0042?" Inputs: DR-0042 at (995, 510); F6. Reasoning: F6's extent is x 0–400, y 600–1000; the drain is outside it, so **F6 gives no answer** — a raster answers only inside its extent (4.3.2). Suppose instead a second synthetic surface covered the whole ward at 100 m and reported 14.0 m at the drain's cell. Whether 14.0 is the ground or the roof of the building over the drain depends on whether that surface is a DTM or a DSM; the number alone cannot tell you. Expected outcome: "no value from F6; from the second surface, 14.0 m if it is a terrain model, otherwise the top of whatever stands there." What this does not establish: the height of the drain's invert, which is below ground and outside any height field's vocabulary.

**Developer analogy (and where it stops).** A height field is the heightmap of a game engine's terrain: a 2D array of heights, cheap to store and render, and famously unable to hold caves or overhangs, which need separate mesh objects. The analogy is exact for the structure and for the limit. It stops at *provenance*: a game heightmap is designed, whereas a GIS elevation surface is measured (or interpolated from measurements), so the question "terrain or surface, and measured how?" has no equivalent in the game case.

**Common misconception.** "The elevation raster is the ground." Consequence: a flood model run on a DSM treats every building as a hill, or a visibility analysis on a DTM sees through every wall. Read the dataset's definition first.

### 4.6.2 Heights need a unit and a vertical reference

A height is a distance *above something*. Fixture F6's documentation says "metres above the training datum TD-0" — two facts, both required, neither of which is in the file.

**The unit.** A vertical coordinate system "includes a unit of measure. This is always a linear unit, usually feet or meters" [X17]. F6's values are metres. A surface in feet would look identical on screen and be 3.28 times too steep if read as metres. When a height raster is combined with horizontal coordinates in a different unit — metres of height on a grid whose cell size is in degrees, or feet of height on a metre grid — the mismatch has to be corrected before any slope, shading, or 3D display is meaningful; ArcGIS Pro's hillshade function exposes this as a *z-factor* whose first purpose is to "convert the elevation units … to the horizontal coordinate units of the dataset" [X16].

**The zero level (vertical reference).** A vertical coordinate system "defines the origin for height or depth values" and "includes a direction" — positive up for heights above a surface, positive down for depths below one [X17]. Esri's page illustrates the point with mean sea level as a height-based zero and mean low water as a depth-based one: the same physical point has different numbers in the two systems, and a point below the zero level of a height system has a *negative* height [X17]. Two elevation rasters with different zero levels cannot be compared cell by cell any more than two temperature series in Celsius and Fahrenheit can.

This is as far as the chapter goes. What the zero level physically *is* — an ellipsoid, a geoid, a tide gauge — and how one converts between them is Chapter 5's subject (vertical datums) and beyond. For now the requirement is procedural: **a height value is incomplete without its unit and its stated zero, and a raster file frequently carries neither.** ArcGIS Pro's raster properties show a spatial reference section that lists the coordinate system's parameters when one is defined and can show "undefined" [X06]; a vertical coordinate system is set on a map or scene and may be absent [X17]. QGIS lists the CRS on the layer's *Information* tab [X21]. In the fixture, all of these read "unknown", and the unit and zero come from this document.

**Worked example — combining two surveys (synthetic).** Question: the municipality has F6 (metres above TD-0) and a contractor's survey of the same corner reporting the north-east cell as 121.0 "above datum". Is the ground 100 m higher in the contractor's data? Reasoning: F6 says 21.0 m above TD-0. The contractor's figure is 100.0 higher, which is exactly the kind of round offset a different zero level produces — or a different unit would not produce (21 m is 68.9 ft, not 121). Expected outcome: "not comparable until the contractor's datum is known; the difference is consistent with a datum offset of 100 m, but that is a hypothesis to check with the contractor, not a finding." What this does not establish: which zero is 'correct' — both can be, for their own purposes.

**Common misconception.** "Elevation is elevation; the number is the number." Consequence: two surfaces are differenced to find "ground change" and the result is a uniform 100 m step that is entirely an artefact of two zero levels. Unit and reference first, arithmetic second.

### 4.6.3 A shaded rendering is a picture of the numbers, not the numbers

The most persuasive raster display is the **hillshade**: a grey-scale rendering in which each cell is lit as if by a sun at a stated direction and height, so that slopes facing the light are bright and slopes facing away are dark. ArcGIS Pro's hillshade function produces "a grayscale 3D representation of the terrain surface" from an elevation layer, using an azimuth (default 315°, from the north-west) and altitude (default 45°) of the light source [X16]; QGIS offers *Hillshade* as one of its raster renderers [X21].

Three properties of a hillshade must be kept in view:

1. **It is derived, not measured.** Every hillshade value is computed from the heights of a cell and its neighbours; the hillshade holds no elevation. Esri's page says exactly this: hillshading "is a qualitative method for visualizing topography and does not give absolute elevation values" [X16].
2. **It is a display choice.** Change the azimuth and the same surface looks different; a ridge can look like a valley if the light is moved to the opposite side. A z-factor greater than 1 adds "vertical exaggeration for visual effect" [X16] and makes a gentle slope look dramatic.
3. **It hides nothing and reveals nothing about validity.** A hillshade of a surface with a 100 m datum error, a unit mismatch, or an interpolation artefact looks just as convincing as one of a validated terrain. Realism on screen is evidence of rendering, not of measurement. The blueprint's phrasing is the rule: **a visually realistic surface is not automatically a validated terrain model.**

The same applies to 3D scenes that drape imagery over an elevation surface: the scene *looks* like the ground, and what it shows is a rendering of one raster's values, with whatever errors they contain, under whatever exaggeration the viewer applied. The Media Appendix (M.5) specifies a small interactive scene built from F6 precisely so that the numbers and the picture can be compared side by side, with the exaggeration control visible.

**Worked example — the flat corner.** Question: a hillshade of F6 shows a dark, flat-looking patch in the bottom row, second column. Is the ground flat there? Reasoning: that cell is NoData. How a hillshade treats NoData is a property of the function — Esri's page notes that output cells "along the edge of a raster or beside NoData pixels" may be populated with NoData or interpolated depending on a setting [X16] — so the patch is an *absence* of measurement, drawn in some colour. Expected outcome: "no information about that cell; the dark patch is the renderer's handling of NoData." What this does not establish: whether the ground there is flat, steep, or a pond.

**Developer analogy (and where it stops).** A hillshade is a lighting pass over a height map — the same operation a renderer performs on a normal map. The analogy is exact. It stops at what the audience does with the result: nobody measures a game's terrain from its screenshot, whereas people routinely read a shaded relief map as though it were a survey.

**Common misconception.** "The 3D view proves the elevation data is good." Consequence: a datum offset, a unit error, or a bilinear artefact ships because the scene looked right. The check is numerical (spot-check cell values against an independent measurement, inspect the NoData pattern, read the metadata), never visual alone.

**Comprehension check 4.6.** A colleague sends a beautifully shaded 3D view of a new elevation raster of Ward B and writes: "Ground truth confirmed — you can see the buildings." Identify (a) which kind of surface the sentence implies, (b) two things the view cannot establish about the raster, and (c) the first two properties you would read from the dataset before using it.

---

## 4.7 Guided lab: inspect two rasters

### 4.7.1 Objective, prerequisites, and what this lab is (and is not)

**Objective.** Load the two Chapter 4 fixture rasters, record their dimensions, cell size, values, bands, extent, and missing-data treatment in an inspection table; change their styling without changing any value and prove it; resample copies of each with two methods; and record the *numerical* effect of each method so that you can say which result is appropriate for which dataset.

**Prerequisites.** Modules 4.1–4.6; the ability to add a layer to a map and open its properties (Chapters 1–3).

**What this lab is.** A measurement exercise. Every expected number below was derived by hand from Fixtures F5 and F6 (Instructor Appendix I.3); your job is to make the software report them and, where the software's behaviour is implementation-specific (edges, NoData neighbours, integer rounding), to *record what it does* rather than assume.

**What this lab is not.** It is not execution-tested by the author. The procedure boxes were written from the cited documentation for ArcGIS Pro 3.7 and QGIS 3.44 and are labelled accordingly. If a control is not where the box says, use the page cited beside the step, and tell the instructor so that the box can be corrected (Instructor Appendix I.8).

### 4.7.2 Required software and access

**Primary route (A): ArcGIS Pro.** Any licence level. The tools used — *Copy Raster*, *Resample*, and *Calculate Statistics* — are all listed as available at Basic, Standard, and Advanced on their tool pages [X08] [S12] [X12], and layer symbology needs no extension. No ArcGIS Online sign-in is needed beyond whatever your installation requires to start.

**Alternative route (B): QGIS Desktop 3.44** (4.7.8). Free; no account.

**No software.** Steps 1–3 and the statistics of step 7 can be done by hand from the listings; steps 8–10 become predictions (write what you expect) that the instructor confirms.

### 4.7.3 Input data — create the two fixture files

The inputs are the two listings in the fixture section. Create them exactly as follows; the header is read by name, so spelling matters, and the number of values must equal rows × columns or the file is rejected [X07].

1. Create a folder for the lab, for example `C:\GIS_Training\Ch04\` (Windows) or `~/GIS_Training/Ch04/` (macOS/Linux). Avoid spaces in the path.
2. In a plain-text editor (not a word processor), create `landcover_training.asc` and paste the F5 listing: six header lines followed by ten lines of ten integers separated by single spaces. Save as plain text.
3. Create `elevation_training.asc` and paste the F6 listing: six header lines followed by four lines of four values, each with a decimal point.
4. Check each file: F5 has exactly 100 values after the header (10 × 10); F6 has 16 (4 × 4). A quick check is to count the lines — F5 has 16 lines, F6 has 10.
5. Do **not** create a `.prj` file. The fixtures have no coordinate system, and the lab depends on that being visible in the properties.

**What you should expect from a file with no coordinate system.** Both applications will tell you, in some way, that the coordinate system is unknown, and both will draw the raster using its raw coordinates (0–1000 m). Where that appears relative to the rest of the world is meaningless. **Do not add a basemap**; a basemap has an Earth coordinate system, and the raster would be drawn at raw-coordinate positions in that system, which is the kind of silent guess Chapter 5 teaches you to refuse. QGIS makes this explicit with a project setting, *No CRS (or unknown/non-Earth projection)*, under which "layers are drawn based on their raw coordinates" [X26].

### 4.7.4 Ordered steps — Route A, ArcGIS Pro

**Procedure (version-specific; written from ArcGIS Pro 3.7 documentation; not execution-tested).**

**Step 1 — Prepare an empty map.** Start ArcGIS Pro, create a new project from the *Map* template, and in the *Contents* pane remove any basemap layer (right-click the basemap layer → *Remove*). Save the project in the lab folder.

**Step 2 — Add the land-cover raster.** On the *Map* tab, use *Add Data* to browse to `landcover_training.asc` and add it. ArcGIS Pro reads the ASCII Grid format directly (it is listed as read-only, single file, `.asc` [X09]). Accept any warning that the coordinate system is unknown. The raster appears as a 10 × 10 block of coloured squares. *Do not change anything yet.*

**Step 3 — Inspect the dataset's properties and fill Table 4.7a (row F5).** In the *Contents* pane right-click the layer → *Properties* → *Source*. The raster properties list the number of columns and rows, the number of bands, the cell size (x, y), the format, the pixel type and depth, the NoData value, the extent (top, bottom, left, right), the spatial reference, and, if statistics exist, the minimum, maximum, and mean per band [X06]. Record every item Table 4.7a asks for. Expected: 10 columns, 10 rows, 1 band, cell size 100 × 100, integer pixel type, NoData value −1, extent left 0 / right 1000 / bottom 0 / top 1000, spatial reference *undefined* or *unknown*.

**Table 4.7a — Inspection table (learner fills in; one row per raster, plus one per resampled copy from step 8).**

| Raster | Columns × rows | Bands | Cell size (x, y) and unit | Pixel type | NoData value | Extent (left, right, bottom, top) | Coordinate system | Min / max / mean (band 1) | NoData cells (count) | Source of the value dictionary or unit |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| `landcover_training.asc` | | | | | | | | | | |
| `elevation_training.asc` | | | | | | | | | | |
| `elevation_50m_nearest.tif` | | | | | | | | | | |
| `elevation_50m_bilinear.tif` | | | | | | | | | | |
| `landcover_50m_nearest.tif` | | | | | | | | | | |
| `landcover_50m_bilinear.tif` | | | | | | | | | | |

**Step 4 — Read individual cell values.** With the *Explore* tool (the navigation tool on the *Map* tab), click a cell; ArcGIS Pro opens a pop-up for raster layers [X19] showing the pixel value for the visible band(s) — the *Get Cell Value* tool page describes the same action: "click on a pixel. Values for each visible band will be returned" [X20]. Use the coordinate readout at the bottom of the map view to aim. Read and record, in Table 4.7c, the value at each of these positions:

| Position (x, y) | Why this position | Expected value (from the listing) |
| --- | --- | --- |
| (950, 950) | Centre of row 1, column 10 — the pond | 0 (Water) |
| (995, 510) | Asset DR-0042 | 1 (Built-up) |
| (205, 195) | Asset SL-0113 | 2 (Vegetation) |
| (50, 50) | Centre of row 10, column 1 | NoData (the pop-up may show "NoData", blank, or nothing; record exactly what it shows) |
| (200, 200) | Request P1 — **on a cell corner** | Any of 2, 1, NoData, 2 depending on the engine's edge rule (4.3.2); record what you get and which cell it implies |

**Step 5 — Change styling without changing values, and prove it.** Open the *Symbology* pane for the land-cover layer and choose *Unique Values* [X10]. Assign a colour and a label from Table F5b to each code (0 Water, 1 Built-up, 2 Vegetation, 3 Bare ground). Set NoData to display as a strong, visible colour — all renderers allow NoData to be shown as a colour or no colour [X02]; note where the control is in your version. Then:

- Re-open *Properties* → *Source* and confirm that every entry in Table 4.7a is unchanged.
- Re-read the five positions of step 4 and confirm the values are unchanged.
- Write one sentence in your log stating what changed (the layer's renderer) and what did not (the dataset).

**Step 6 — Add and inspect the elevation raster.** Add `elevation_training.asc`, record its properties in Table 4.7a. Expected: 4 × 4, 1 band, cell size 100, floating-point pixel type, NoData −9999, extent left 0 / right 400 / bottom 600 / top 1000, spatial reference undefined. If the pixel type is reported as integer, the decimal values were not honoured; see Troubleshooting. Style it with *Stretch* using a *Minimum Maximum* stretch type [X10] so that 9.0 and 21.0 sit at the ends of the ramp, and set NoData to a visible colour. Read the values at (50, 950) — expected 12.0 — and (150, 650) — expected NoData — and add them to Table 4.7c.

**Step 7 — Statistics under an explicit policy.** In *Properties* → *Source*, read the statistics section (minimum, maximum, mean per band) [X06]. If no statistics are shown, run *Calculate Statistics* (Data Management Tools) on the dataset with default skip factors [X12] and read them again. Record min, max, and mean for both rasters in Table 4.7a. Expected for the elevation raster if NoData is excluded: min 9.0, max 21.0, **mean 13.6**. If the mean is **12.75**, NoData was counted as zero; if it is near **−612**, the NoData marker was not recognised (Troubleshooting). Write the statistic as a sentence with its policy and coverage (4.4.2).

**Step 8 — Resample copies with two methods.** Open *Analysis* → *Tools*, find *Resample* (Data Management Tools) [S12], and run it four times, saving each output as a TIFF in the lab folder:

| Run | Input | Output Raster Dataset | Output Cell Size | Resampling Technique |
| --- | --- | --- | --- | --- |
| 1 | `elevation_training.asc` | `elevation_50m_nearest.tif` | 50 (x) 50 (y) | *Nearest* |
| 2 | `elevation_training.asc` | `elevation_50m_bilinear.tif` | 50 50 | *Bilinear* |
| 3 | `landcover_training.asc` | `landcover_50m_nearest.tif` | 50 50 | *Nearest* |
| 4 | `landcover_training.asc` | `landcover_50m_bilinear.tif` | 50 50 | *Bilinear* |

The tool's page states that the extent does not change, that the lower-left corner of the output is at the same coordinate as the input's, and that the number of columns and rows is (extent ÷ cell size) [S12] — so expect 8 × 8 for the elevation outputs and 20 × 20 for the land-cover outputs. The originals are untouched; you are working on copies, as the blueprint requires.

**Step 9 — Inspect the copies and read the same positions.** Add the four outputs to the map (if they were not added automatically), record their properties in Table 4.7a, and read the positions in Table 4.7c. The expected values marked *hand-checked* follow from the method descriptions on the tool page [S12]; those marked *record* depend on implementation details the documentation does not fix.

**Table 4.7c — Cell readings (learner fills the last two columns).**

| Raster | Position (x, y) | Output cell (1-based row, col) | Expected | Basis | Observed | Matches? |
| --- | --- | --- | --- | --- | --- | --- |
| `elevation_50m_nearest.tif` | (75, 925) | row 2, col 2 | 12.0 | hand-checked: nearest input centre is (50, 950) | | |
| `elevation_50m_nearest.tif` | (175, 625) | row 8, col 4 | NoData | hand-checked: nearest input centre is (150, 650), the NoData cell | | |
| `elevation_50m_bilinear.tif` | (75, 925) | row 2, col 2 | 12.25 | hand-checked: 0.5625 × 12 + 0.1875 × 14 + 0.1875 × 11 + 0.0625 × 13 | | |
| `elevation_50m_bilinear.tif` | (125, 925) | row 2, col 3 | 13.25 | hand-checked: 0.1875 × 12 + 0.5625 × 14 + 0.0625 × 11 + 0.1875 × 13 | | |
| `elevation_50m_bilinear.tif` | (75, 875) | row 3, col 2 | 11.75 | hand-checked: 0.1875 × 12 + 0.0625 × 14 + 0.5625 × 11 + 0.1875 × 13 | | |
| `elevation_50m_bilinear.tif` | (125, 875) | row 3, col 3 | 12.75 | hand-checked: 0.0625 × 12 + 0.1875 × 14 + 0.1875 × 11 + 0.5625 × 13 | | |
| `elevation_50m_bilinear.tif` | (25, 975) | row 1, col 1 | **record** | edge cell: fewer than four input centres surround it; behaviour not fixed by the documentation | | |
| `elevation_50m_bilinear.tif` | (175, 675) | row 7, col 4 | **record** | one of its four input neighbours is NoData; the NoData page lists three possible treatments [X02] | | |
| `landcover_50m_nearest.tif` | (725, 875) | row 3, col 15 | 0 (Water) | hand-checked: nearest input centre is (750, 850) = row 2, col 8 | | |
| `landcover_50m_bilinear.tif` | (725, 875) | row 3, col 15 | **record** — mathematically 0.5 from neighbours 2, 0, 2, 0 (weights 0.0625, 0.1875, 0.1875, 0.5625); if the output is integer it will be 0 or 1 | either way, a number that is not a code or a code (1 = Built-up) that was never observed there (4.5.2) | | |
| `landcover_50m_nearest.tif` | (995, 510) | row 10, col 20 | 1 (Built-up) | hand-checked: unchanged from the source | | |

**Step 10 — Compare statistics and counts.** Read min/max/mean for the four outputs (step 7's method). Expected:

- `elevation_50m_nearest.tif`: min 9.0, max 21.0, mean **13.6** — unchanged, because every output value is a copy of an input value and each input is copied exactly four times (60 valid cells, sum 816). Four NoData cells.
- `elevation_50m_bilinear.tif`: min and max **within** 9.0–21.0 (bilinear cannot leave the input range [S12]); mean **near but not necessarily equal to** 13.6; NoData count **record** (4, more, or fewer, depending on the NoData treatment).
- `landcover_50m_nearest.tif`: exactly the values 0, 1, 2, 3 and NoData; if your version offers a unique-values count or a raster attribute table, expect 28, 96, 224, 40, and 12 cells respectively (each source count × 4).
- `landcover_50m_bilinear.tif`: values that are not in {0, 1, 2, 3}, or values in that set at positions where the source had a different code. Record the minimum and maximum and at least one offending cell.

**Step 11 — Write the explanation.** For each dataset, one paragraph: which resampling result is appropriate and why, citing the numbers you recorded (not the appearance). Then one sentence on what the 50 m outputs do *not* contain (4.5.3).

### 4.7.5 Expected results and validation checks

A correct submission satisfies all of the following; detailed expected values are in Instructor Appendix I.3.

- **Table 4.7a is complete for six rasters**, and the two source rasters show: 10 × 10 and 4 × 4; one band each; cell size 100; NoData −1 and −9999; extents (0, 1000, 0, 1000) and (0, 400, 600, 1000); coordinate system undefined/unknown. The four outputs show cell size 50, **the same extents as their sources**, and 8 × 8 or 20 × 20 cells.
- **Every property in Table 4.7a is identical before and after step 5.** A styling change that altered any entry is a failed check (and a sign that something other than symbology was done).
- **The elevation mean is 13.6 with the policy stated**, and the learner's log names the alternative figures (12.75; ≈ −612) and what each would indicate.
- **The hand-checked cells in Table 4.7c match**: 12.0 and NoData for nearest; 12.25, 13.25, 11.75, 12.75 for bilinear (small floating-point differences in the last displayed digit are acceptable; a difference of 0.1 or more is not); 0 and 1 for land-cover nearest.
- **The four "record" cells have an observed value written down**, whatever it is. An empty cell there is a failed check; the point of those rows is to observe implementation behaviour.
- **The nearest-neighbour elevation mean equals the source mean** (13.6) and the NoData count is 4; the nearest land-cover counts are exactly four times the source counts.
- **The explanation names nearest neighbour (or majority) for the land cover and an interpolating method for the elevation**, and gives the *numerical* reason for each: no new codes; smooth values within range.
- **The explanation states that the 50 m outputs contain no information that the 100 m sources did not.**

### 4.7.6 Troubleshooting

| Symptom | Likely cause | What to do |
| --- | --- | --- |
| The `.asc` file is not offered in the browse dialog | The extension is not `.asc`, or the file filter hides it | Rename to `.asc`; the Copy Raster page notes that by default only `.asc` ASCII files are recognised by the browse dialog and recommends renaming [X08] |
| An error says the number of values does not match | A missing or extra value, or a stray character | Count values per line; the file must contain exactly rows × columns values [X07] |
| The elevation raster's pixel type is integer and values show as 12, 14, … | The decimal points were lost, or the reader chose an integer type | Check that every value in the file has a decimal point and that `NODATA_VALUE` is `-9999.0`; if needed, run *Copy Raster* to a TIFF with a floating-point *Pixel Type* [X08] and use that copy |
| The mean is about −612 | The NoData marker was not recognised and −9999 was treated as a height | Open *Properties* → *Source* and check the NoData value; set it to −9999 per band if it is missing (the NoData page describes the *NoData Value* editor [X02]); recompute statistics |
| The mean is 12.75 | NoData was treated as 0 — either by the tool or because a 0 was typed instead of −9999.0 in the file | Check the file, then check the tool's settings; the *Ignore Values* parameter of Calculate Statistics excludes a stated value [X12] |
| The pond does not display at all | The NoData value was set to 0, or a transparency setting hides 0 | Check the NoData value (must be −1) and the symbology's NoData/transparency settings [X02] |
| The raster appears somewhere odd relative to a basemap | A basemap was left in the map; the raster's raw coordinates were drawn in the basemap's coordinate system | Remove the basemap (step 1); the position is meaningless until Chapter 5 |
| The top row of the listing appears at the bottom of the map | Either the file was written bottom-up or the display is mirrored | Read the pond's position: the pond (code 0) must be at the *top* right (y 900–1000). If it is at the bottom, the file's row order is reversed — recheck the listing (Instructor Appendix I.6, verification item V1) |
| The pop-up shows three values, or a value for "Band_1" plus others | The layer is being read as multiband, or the pop-up lists renderer outputs | Confirm *Bands = 1* in properties; read the value labelled as the pixel value for band 1 |
| Resample output is 9 × 9 or 21 × 21 instead of 8 × 8 or 20 × 20 | A cell size other than exactly 50 was entered, or the extent environment was changed | Re-run with cell size 50 and default environments; the page gives columns = (xmax − xmin) ÷ cell size [S12] |
| Bilinear elevation shows NoData around the missing cell in a larger patch than expected, or shows values there | Implementation-specific NoData treatment [X02] | This is a *record* row, not an error; write down the observed pattern |

### 4.7.7 Required learner deliverables

1. **Table 4.7a** (inspection table) for all six rasters, with the "source of the value dictionary or unit" column filled in honestly ("this document, Table F5b"; "this document, fixture F6 note"; "none in the file").
2. **Table 4.7c** (cell readings) with every *observed* column filled, including the four *record* rows.
3. **Statistics log**: for each of the six rasters, the min/max/mean as read, and, for the two elevation sources and copies, the one-sentence statistic with its policy and coverage.
4. **Before/after evidence for step 5**: the two property readings (before and after styling) and the sentence stating what changed.
5. **The explanation** from step 11: one paragraph per dataset on the appropriate resampling result, citing recorded numbers, and the closing sentence on what the resampled outputs do not contain.
6. **A discrepancy list**: every place where a control, label, or behaviour differed from the procedure box, with the version of the software (from *Help* → *About*).

### 4.7.8 QGIS alternative — Route B (equivalent foundational exercise)

The lab transfers to QGIS with the same inputs, the same tables, and the same expected numbers for the hand-checked rows. The differences are in where the controls are and in how resampling is invoked; they are stated below rather than assumed identical. **Written from the QGIS 3.44 user manual and GDAL documentation; not execution-tested.**

**Procedure (version-specific; QGIS 3.44).**

1. **Project with no CRS.** Create a new project. Open *Project* → *Properties…* → *CRS* and choose *No CRS (or unknown/non-Earth projection)*, under which "layers are drawn based on their raw coordinates" [X26]. Also check *Settings* → *Options* → *CRS and Transforms*: the option for "when a layer without a CRS is loaded" should be *Leave as unknown CRS (take no action)* or *Prompt for CRS* (choose "no CRS" if prompted), not *Use project CRS* or *Use default layer CRS* [X26]. Do not add a basemap.
2. **Add the rasters.** *Layer* → *Add Layer* → *Add Raster Layer* (Ctrl+Shift+R), *File* source type, browse to each `.asc` [X25]. QGIS reads the format through GDAL's AAIGrid driver, which preserves the file's NODATA value and detects a floating-point type when the values or the NoData value are written with a decimal point [X27] — which is why F6's values carry `.0`.
3. **Inspect.** Right-click the layer → *Properties* → *Information*. The tab lists extent, width and height, data type, GDAL driver, band statistics, pixel size, number of columns and rows, NoData values, and the CRS [X21]. Fill Table 4.7a. Expected values are the same as in Route A; the CRS will read as unknown/invalid.
4. **Read cells.** Select the layer, use *Identify Features* (Ctrl+Shift+I), click a cell; the *Identify Results* panel shows the band(s) and value(s), and under *Derived* the clicked x, y and the column and row [X21]. Fill Table 4.7c for the source rasters; the coordinate readout at the bottom of the window helps you aim.
5. **Style without changing values.** *Properties* → *Symbology*. For the land cover choose *Paletted/Unique values* and set a colour and label per code [X21]; for the elevation choose *Singleband pseudocolor* (the renderer for "a continuous palette … e.g. an elevation map") [X21]. Under *Transparency*, note the reported *No data value* and use *Display no data as* a visible colour [X21]. Re-open *Information* and re-identify the cells to prove nothing changed. Note that the *Resampling* section of the Symbology tab controls *display-time* resampling only ("when you zoom in and out") [X21]; leave it at nearest neighbour so that what you see is the data.
6. **Statistics.** *Processing* → *Toolbox* → *Raster analysis* → *Raster layer statistics*, band 1 [X24]. The algorithm reports minimum, maximum, range, sum, mean, and standard deviation. **Its documentation does not state whether NoData cells are excluded** [X24]; read the mean and use the fixture facts to determine which policy it applied (13.6 = excluded; 12.75 = counted as 0; ≈ −612 = NoData not recognised). Record the policy you observed; this is verification item V3.
7. **Resample copies.** QGIS has no tool named *Resample*; use GDAL's *Translate (convert format)* (*Processing* → *Toolbox* → *GDAL* → *Raster conversion*), which accepts *Additional command-line parameters* [X23]. In that field type `-tr 50 50 -r nearest` for the nearest-neighbour copies and `-tr 50 50 -r bilinear` for the bilinear copies; `-tr` sets the target resolution in georeferenced units and `-r` selects the resampling algorithm [X28]. Save each output as a `.tif` with the names in step 8 of Route A. If your rasters *had* a CRS, *Warp (reproject)* offers the same choice through its *Resampling method to use* and *Output file resolution* parameters [X22]; it is not used here because the fixture has no CRS and Warp's target CRS defaults to EPSG:4326 [X22].
8. **Inspect, read, and compare** exactly as in Route A steps 9–11. Expect the same hand-checked numbers. GDAL's bilinear kernel [X28] and Esri's "weighted distance average of the four nearest input cell centers" [S12] are both bilinear interpolation and give the same interior values for a 2× resample; edge and NoData-neighbour behaviour is implementation-specific in both and must be *recorded*, not assumed equal. The *Raster layer unique values report* algorithm gives per-value counts for the categorical outputs [X24].

**What is the same.** The concept, the inputs, the tables, the hand-checked expectations, the deliverables, and the validation checks.

**What is different, and must be stated by the learner.** (a) Resampling is reached through a format-conversion tool with a command-line option rather than a dedicated tool, and the *display* resampling control in Symbology is a separate thing that must not be confused with it. (b) The statistics algorithm's NoData policy is undocumented and must be observed. (c) Method names differ (*Nearest* / *Nearest Neighbour*; *Bilinear* / `bilinear`), and QGIS offers more kernels than ArcGIS Pro's four [X22] [S12]. (d) Unknown-CRS handling is a project and options setting in QGIS [X26] rather than a warning on load.

---

## 4.8 Independent check and progression gate

This assessment is completed without the Instructor Appendix and without re-reading the lab's expected values. Answer every item under the stated assumptions; if you believe an item is under-specified, say what is missing rather than guessing. All grids in this section are **new synthetic data**, not the fixtures. Item-to-module mapping is shown so that you know what each item tests.

### 4.8.1 Concept questions (six)

**Q1.** [4.1; LO1] A single-band raster covers Ward A with 10 × 10 cells of 100 m; each cell holds a land-cover code. Which **one** of the following questions can it answer *directly*, and why can it not answer the other three?
(a) How many streetlights are in the ward? (b) How much of the ward is water? (c) Which streetlight is nearest request P3? (d) How long is Road R1?

**Q2.** [4.2; LO2] A raster layer shows one cell in dark red. List, in the order you would check them, the things you must know before you can state what the dark red *means*, and say for each where you would look.

**Q3.** [4.3; LO3] A raster header reads `NCOLS 8, NROWS 6, XLLCORNER 1200, YLLCORNER 300, CELLSIZE 50` (training grid, metres, no coordinate system). (a) State the extent as left, right, bottom, top. (b) Give the 1-based row and column of the cell containing the point (1330, 460), showing the arithmetic and stating which edge rule you used. (c) Can a feature 5 m wide be represented in this raster? (d) Does the cell size tell you how accurately the raster is positioned? Explain in one sentence.

**Q4.** [4.4; LO4] A one-row raster holds the values `4, 0, NoData, 6, 2` (a count of requests per cell). (a) Give the mean if NoData is excluded and the mean if NoData is treated as 0. (b) State, in one sentence each, what the cell holding 0 asserts and what the NoData cell asserts. (c) Which of the two means would you report, and what must accompany it?

**Q5.** [4.5; LO5] A soil-type raster (integer codes 1–6) at 30 m must be brought onto a 10 m grid so that it can be compared cell by cell with a 10 m raster. Under these conditions, which **one** resampling method is the defensible choice: (a) bilinear, because it gives a smoother result; (b) cubic convolution, because it is the smoothest; (c) nearest neighbour, because it creates no new values; (d) average, because the 10 m cells should represent the surrounding area? Explain why each of the other three fails, and state one thing the 10 m output will *not* contain.

**Q6.** [4.6; LO6] You receive an elevation raster of Ward B with values from 40 to 75 and a convincing hillshade. (a) Name the two facts about the values you need before you can compare it with another elevation raster. (b) Does the hillshade tell you whether the surface is a terrain model or a surface model? Why or why not? (c) Name one kind of real-world structure that a one-value-per-cell height field cannot represent.

### 4.8.2 Scenario questions (two)

**S1.** [4.2, 4.4; LO2, LO4] The municipality receives `flood_extent_B.tif`: single band, integer, values 0 and 1, NoData value 255, covering Ward B at 20 m, with a one-line note "1 = flooded". A dashboard built on it reports "62 % of Ward B flooded". Write the checks you would make before accepting that figure, in order, and then write the *two* honest versions of the percentage statement that could be correct depending on what you find (state the denominator in each). Finally, say what the raster cannot tell you even if every check passes.

**S2.** [4.3, 4.8; LO3] A contractor sends a scanned drainage plan of Ward B as a PNG image, 3,000 × 2,000 pixels, with no world file and no georeferencing in the file header, and writes: "We resampled the scan to 0.1 m pixels, so the plan is accurate to 0.1 m and can go straight onto the map." (a) Can the image be placed reliably on the map as delivered? Say what is missing and what evidence would be needed. (b) Is the accuracy claim valid? Explain the difference between the two things the contractor has conflated. (c) After the image *is* correctly placed, what one property would you still need to state before anyone measures from it?

### 4.8.3 Independent practical task (unfamiliar inputs)

**Inputs (synthetic; new for this task).** A flood-depth raster over part of Ward B, delivered as the following Esri ASCII file `flood_depth_check.asc`. Values are **metres of standing water** measured on 2026-09-15 (synthetic); `-99` marks cells that were not surveyed. The training grid applies: metres, no coordinate system; row 1 is the top row.

```
NCOLS 5
NROWS 4
XLLCORNER 1500
YLLCORNER 100
CELLSIZE 25
NODATA_VALUE -99
0.0 0.0 0.2 0.4 0.5
0.0 0.1 0.3 0.6 -99
0.0 0.0 0.2 -99 -99
0.0 0.0 0.0 0.1 0.3
```

Also given: a new inspection point **Q1 at (1560, 130)** and the Chapter 1 request **P3 at (1200, 250)**.

**Tasks.** Show your working for every number.

1. From the header alone, state the number of cells, the cell area in m², and the extent (left, right, bottom, top).
2. Count the valid cells, the NoData cells, and the cells whose value is exactly 0. State in one sentence what a 0 cell asserts and what a NoData cell asserts.
3. Compute the mean depth over valid cells, and the mean if NoData were treated as 0. State which you would report and write the reporting sentence with policy and coverage.
4. Give the depth at Q1, showing the row/column arithmetic, and give the depth at P3 or explain why there is none.
5. Predict the result of resampling this raster to 12.5 m cells with nearest neighbour: dimensions, the value at Q1, the number of NoData cells, and the mean over valid cells. Then state which of nearest neighbour and bilinear is appropriate for *this* dataset, with the numerical reason.
6. State one thing the raster cannot establish about flooding in Ward B, and one thing it cannot establish about the cell containing Q1.
7. **If software is available (ArcGIS Pro or QGIS):** create the file, load it, and confirm items 1–4 from the layer's properties and cell readings; record any difference from your hand results and explain it. If software is not available, say so; items 1–6 stand on their own.

### 4.8.4 Oral explanation (one)

In no more than two minutes, explain to a non-GIS manager why the mean of the chapter's elevation fixture is 13.6 m and not 12.75 m, and why the smoother-looking 50 m bilinear copy is not "better data" than the 100 m original. You may draw one grid. You will be asked one follow-up question.

### 4.8.5 Submission requirements and scoring

**Submit:** written answers to Q1–Q6 and S1–S2; the practical's working, tables, and (if run) software readings with the software version; the oral explanation is given live or recorded.

**Scoring (suggested; 100 points).**

| Component | Points | What earns them |
| --- | --- | --- |
| Concept questions Q1–Q6 | 30 (5 each) | The single defensible answer under the stated conditions, with the reason; for Q1 and Q5, why the other options fail |
| Scenario questions S1–S2 | 20 (10 each) | Checks in a sensible order; both honest statements with denominators; the georeferencing/accuracy distinction made explicitly |
| Practical task | 40 | Reasoning and assumptions stated (10); numerical correctness of items 1–5 (15); verification — the cross-checks between header arithmetic, counts, and means, and the software comparison if run (10); documentation — policy, coverage, and limitations written as they would appear in a report (5) |
| Oral explanation | 10 | The two ideas conveyed without jargon, with the numbers, and the follow-up answered |

**Progression.** A suggested pass threshold is 80 points. Regardless of total, the following are **critical misconceptions** and require correction and a fresh exercise before Chapter 5: treating NoData as zero (or zero as NoData) in a statistic; stating a raster's positional accuracy from its cell size; applying an averaging method to category codes; inferring a unit or a class from a colour; and asserting that an image with no georeferencing information is correctly placed. A screenshot is not evidence of a correct value; the value must be read and written down.

---

## 4.9 Media and source brief

The full media specification is in the Media Appendix (M.1–M.5). In summary:

1. **Slides** move from a numeric grid to a styled raster to a surface, in the order of the modules: F7's nine numbers first, then F5 with two different renderers, then F6 as numbers, as a ramp, and as a hillshade. Every slide that shows a colour also shows the number or the legend rule that produced it.
2. **Audio** describes cell arrangement and values — "row one, column three holds twenty" — and never relies on colour; the narration outline in M.2 gives the wording for each figure.
3. **An optional 3D scene** (M.5) elevates the vertices of F6's documented synthetic surface, with a labelled vertical-exaggeration control and a flat-grid comparison, so that the numbers, the height field, and the rendering are seen as three views of one small dataset. It is labelled schematic throughout.
4. **Sources.** Raster foundations were read in the QGIS *Gentle Introduction* [S11] and the ArcGIS Pro raster introduction [X01]; resampling behaviour in the ArcGIS Pro *Resample* tool page [S12]. The remaining references support specific claims where cited.

**Transition to Chapter 5.** Every raster in this chapter was placed by four numbers in a header — an origin, a cell size, and a row and column count — on a training grid with no location on Earth. Chapter 5 supplies what the header lacked: the **coordinate reference system** that turns "x = 995, y = 510" into a place, with its units, its axis order, and its horizontal and vertical references. Until then, the question "where is this raster?" has the honest answer the fixture gave: *nowhere in particular*.

---

## Glossary

| Term | Meaning in this course |
| --- | --- |
| **Alignment** (of grids) | Two rasters align when their cell edges coincide — same cell size and origins that differ by a whole number of cells. Aligned grids allow cell-to-cell comparison; unaligned grids require resampling first (4.3.3). |
| **Band** | One complete matrix of cell values. A multiband raster holds several bands covering the same cells [X04] (4.2.2). |
| **Bilinear interpolation** | A resampling method that sets each output cell to a distance-weighted average of the four nearest input cell centres; suited to continuous data, not categories [S12] (4.5.1). |
| **Categorical (thematic, discrete) raster** | A raster whose values are codes for classes (land cover, soil type). Only counting, area, and "same class" operations are meaningful; arithmetic on codes is not (4.1.2, 4.5.2). |
| **Cell (pixel)** | One element of the grid, covering a square (or rectangular) area and holding one value per band [S11] [X01] (4.1.1). |
| **Cell size (pixel size)** | The width and height of a cell in the raster's coordinate units; the raster's spatial resolution [X03] (4.3.1). |
| **Continuous raster** | A raster whose values are measured quantities on a scale with units (height, rainfall, temperature); averaging and interpolation are meaningful (4.1.2). |
| **Cubic convolution** | A resampling method fitting a smooth curve through the 16 nearest input centres; smoothest result, may produce values outside the input range [S12] (4.5.1). |
| **DEM / DTM / DSM** | Digital elevation model (generic); digital terrain model (bare earth, no objects); digital surface model (includes buildings and trees) [X14] [X15]. Which one a dataset is must come from its own definition (4.6.1). |
| **Display resampling** | The resampling a viewer applies when drawing a raster at a zoom level; changes only the picture, never the data [X21] [X11] (4.5.1). |
| **Esri ASCII raster (`.asc`)** | A plain-text raster format: a six-line header (columns, rows, lower-left corner, cell size, NoData value) followed by values in row order [X07]; used for the fixtures because it can be typed by hand. |
| **Extent** | The rectangle a raster covers: left, right, bottom, top, computed as origin plus count × cell size [X06] (4.3.2). |
| **Georeferencing** | The information (or the process of establishing it) that places a raster's rows and columns at world coordinates: a corner coordinate, cell size, rotation, and a coordinate system [S11] [X13] [X18]. Absent from the fixtures by design. |
| **Height field** | A surface representation with exactly one height per (x, y) cell; cannot represent overhangs or multiple levels (4.6.1). |
| **Hillshade** | A grey-scale rendering of an elevation surface lit from a chosen direction; derived, qualitative, and not a source of elevation values [X16] (4.6.3). |
| **Majority (mode) resampling** | A resampling method that assigns the most common input value in a window; suited to categorical data when aggregating [S12] (4.5.2). |
| **Mask** | A processing-time instruction to ignore certain cells (often defined by another dataset); not stored in the raster's values [X02] (4.4.1). |
| **Missing-data policy** | The rule an operation applies to NoData cells: return NoData, ignore them, or estimate a value [X02]. Must be stated with any statistic (4.4.2). |
| **Nearest neighbour** | A resampling method that copies the value of the nearest input cell centre; creates no new values; the usual choice for categories [S12] (4.5.1). |
| **NoData** | A cell state meaning "no value here"; stored as a reserved value (e.g. −9999) or a mask [X02]. Not zero (4.4.1). |
| **Origin** | The reference corner coordinate of a raster; in `.asc` files the lower-left corner of the lower-left cell [X07] (4.3.2). |
| **Pixel type / pixel depth** | How cell values are stored: signed or unsigned, integer or floating point, and how many bits [X06]. A hint about meaning, not a dictionary (4.2.3). |
| **Raster** | The grid data model: rows and columns of cells with values, positioned by a header [S11] [X01] (4.1.1). |
| **Renderer (symbology type)** | The rule that maps cell values to colours for display — unique values, classified, stretched, colour map, RGB composite, hillshade [X10] [X21] (4.2.1). |
| **Resampling** | Producing a new raster on a different grid from an existing one; requires a method that decides each new value [S12] (4.5). |
| **Row / column** | The two indices that address a cell; rows are listed from the top, so row 1 is the northern edge once the raster is placed [X01] (4.1.1, 4.3.2). |
| **Spatial resolution** | See cell size. Not the same as positional accuracy (4.3.1). |
| **Spectral resolution** | The number of bands in an image and the wavelength ranges they cover [S11] [X03] (4.2.2). |
| **Stretch** | A renderer that maps a range of values onto a colour ramp; the range chosen (minimum–maximum, percent clip, standard deviations) changes the picture, not the data [X10] (4.2.1). |
| **Vertical reference (vertical coordinate system)** | The zero level and direction (up or down) for heights, with a linear unit [X17]. Detail in Chapter 5 (4.6.2). |
| **World file** | A small text file holding a raster's six-parameter image-to-world transformation for formats that do not store georeferencing in their header [X13] (4.3.2, S2). |
| **Z-factor** | A multiplier applied to heights, to convert their unit to the horizontal unit or to exaggerate relief for display [X16] (4.6.2, 4.6.3). |

## Recap

A raster is a grid of cells with values, placed by a header that gives an origin, a cell size, and a row and column count. Values are measurements (continuous) or codes (categorical), and what a value *means* comes from a unit or a dictionary that the file rarely contains; the colour on screen is a layer's rendering rule, not a stored fact. Cell size is resolution, and resolution is neither positional accuracy nor a guarantee that small objects exist in the data. Zero is a measurement; NoData is a stated absence; a mask is a processing instruction; transparency is a display choice — and the mean of Fixture F6 is 13.6 m under the policy that excludes NoData, 12.75 m under the policy that does not, so the policy must be reported with the number. Changing a raster's grid requires a resampling method: nearest neighbour copies values and suits categories; bilinear and cubic compute new values and suit continuous surfaces; averaging category codes produces meaningless or misleading classes; and a finer output grid contains no information the source did not. An elevation raster is a height field — one height per cell — whose values need a unit and a zero level, whose definition (terrain or surface) must come from the dataset, and whose shaded rendering is a picture of the numbers, not evidence that they are right.

## Cross-references to later chapters

| Later chapter | What it builds on from here |
| --- | --- |
| 5 — Coordinates and CRS | The header's origin and cell size become meaningful only with a coordinate reference system; vertical references for heights (4.6.2). |
| 6 — Projections, transformations, measurement | Reprojecting a raster is resampling (4.5) with a change of coordinate system; z-factors and unit mismatches. |
| 7 — Formats, sources, metadata | Where the unit, the dictionary, the NoData definition, and the DTM/DSM definition are supposed to be recorded (4.2.3, 4.6.1); GeoTIFF and other raster formats [X09]. |
| 9 — Data capture, editing, quality | Georeferencing a scanned image with control points [X18] (S2); NoData as a quality issue (4.4.3). |
| 10 — Queries and spatial relationships | Point-in-cell lookup and its edge rule (4.3.2) alongside point-in-polygon and its boundary rule. |
| 11 — Spatial analysis | Raster overlay, zonal statistics, and surface analysis all inherit the alignment (4.3.3), missing-data (4.4), and resampling (4.5) rules established here. |
| 12 — Cartography | Choosing renderers, class breaks, and hillshade parameters honestly (4.2.1, 4.6.3). |

## References

All pages were opened and read by the author on **19 September 2026**. IDs [S11] and [S12] are the blueprint's reference register entries used by this chapter; [X01]–[X29] are additional official pages consulted to support specific claims. Esri `latest` pages and GDAL `stable` pages change without notice; QGIS pages are pinned to the 3.44 edition. Re-check before reuse.

| ID | Publisher | Page title | URL | Accessed |
| --- | --- | --- | --- | --- |
| S11 | QGIS Project | A Gentle Introduction to GIS — Raster Data (3.44) | https://docs.qgis.org/3.44/en/docs/gentle_gis_introduction/raster_data.html | 2026-09-19 |
| S12 | Esri | ArcGIS Pro — Resample (Data Management Tools) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/resample.html | 2026-09-19 |
| X01 | Esri | ArcGIS Pro — Introduction to image and raster data | https://doc.esri.com/en/arcgis-pro/latest/help/data/imagery/introduction-to-raster-data.html | 2026-09-19 |
| X02 | Esri | ArcGIS Pro — NoData in raster datasets | https://doc.esri.com/en/arcgis-pro/latest/help/data/imagery/nodata-in-raster-datasets.html | 2026-09-19 |
| X03 | Esri | ArcGIS Pro — Pixel size of image and raster data | https://doc.esri.com/en/arcgis-pro/latest/help/data/imagery/pixel-size-of-image-and-raster-data-pro-.html | 2026-09-19 |
| X04 | Esri | ArcGIS Pro — Raster bands | https://doc.esri.com/en/arcgis-pro/latest/help/data/imagery/raster-bands-pro-.html | 2026-09-19 |
| X05 | Esri | ArcGIS Pro — What is a pixel | https://doc.esri.com/en/arcgis-pro/latest/help/data/imagery/what-s-in-a-pixel.html | 2026-09-19 |
| X06 | Esri | ArcGIS Pro — Raster dataset properties | https://doc.esri.com/en/arcgis-pro/latest/help/data/imagery/raster-dataset-properties.html | 2026-09-19 |
| X07 | Esri | ArcGIS Pro — ASCII To Raster (Conversion Tools) (marked as a deprecated tool; used here for the format description) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/conversion/ascii-to-raster.html | 2026-09-19 |
| X08 | Esri | ArcGIS Pro — Copy Raster (Data Management Tools) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/copy-raster.html | 2026-09-19 |
| X09 | Esri | ArcGIS Pro — Raster file formats | https://doc.esri.com/en/arcgis-pro/latest/help/data/imagery/supported-raster-dataset-file-formats.html | 2026-09-19 |
| X10 | Esri | ArcGIS Pro — Change the symbology of imagery | https://doc.esri.com/en/arcgis-pro/latest/help/data/imagery/symbology-pane.html | 2026-09-19 |
| X11 | Esri | ArcGIS Pro — Raster rendering behavior | https://doc.esri.com/en/arcgis-pro/latest/help/data/imagery/raster-rendering-behavior.html | 2026-09-19 |
| X12 | Esri | ArcGIS Pro — Calculate Statistics (Data Management Tools) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/calculate-statistics.html | 2026-09-19 |
| X13 | Esri | ArcGIS Pro — World files for raster datasets | https://doc.esri.com/en/arcgis-pro/latest/help/data/imagery/world-files-for-raster-datasets.html | 2026-09-19 |
| X14 | Esri | ArcGIS Pro — Exploring digital elevation models (Spatial Analyst) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/spatial-analyst/exploring-digital-elevation-models.html | 2026-09-19 |
| X15 | Esri | ArcGIS Pro — Create elevation data using the Ortho mapping DEMs wizard (DTM/DSM definitions) | https://doc.esri.com/en/arcgis-pro/latest/help/data/imagery/generate-elevation-data-using-the-dems-wizard.html | 2026-09-19 |
| X16 | Esri | ArcGIS Pro — Hillshade function | https://doc.esri.com/en/arcgis-pro/latest/help/analysis/raster-functions/hillshade-function.html | 2026-09-19 |
| X17 | Esri | ArcGIS Pro — Vertical coordinate systems | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/properties/vertical-coordinate-systems.html | 2026-09-19 |
| X18 | Esri | ArcGIS Pro — Overview of georeferencing | https://doc.esri.com/en/arcgis-pro/latest/help/data/imagery/overview-of-georeferencing.html | 2026-09-19 |
| X19 | Esri | ArcGIS Pro — Pop-ups | https://doc.esri.com/en/arcgis-pro/latest/help/mapping/navigation/pop-ups.html | 2026-09-19 |
| X20 | Esri | ArcGIS Pro — Get Cell Value (Data Management Tools) | https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/get-cell-value.html | 2026-09-19 |
| X21 | QGIS Project | QGIS Desktop 3.44 User Guide — Raster Properties Dialog | https://docs.qgis.org/3.44/en/docs/user_manual/working_with_raster/raster_properties.html | 2026-09-19 |
| X22 | QGIS Project | QGIS Desktop 3.44 User Guide — GDAL Raster projections: Warp (reproject) | https://docs.qgis.org/3.44/en/docs/user_manual/processing_algs/gdal/rasterprojections.html | 2026-09-19 |
| X23 | QGIS Project | QGIS Desktop 3.44 User Guide — GDAL Raster conversion: Translate (convert format) | https://docs.qgis.org/3.44/en/docs/user_manual/processing_algs/gdal/rasterconversion.html | 2026-09-19 |
| X24 | QGIS Project | QGIS Desktop 3.44 User Guide — Raster analysis: Raster layer statistics; Raster layer unique values report | https://docs.qgis.org/3.44/en/docs/user_manual/processing_algs/qgis/rasteranalysis.html | 2026-09-19 |
| X25 | QGIS Project | QGIS Desktop 3.44 User Guide — Opening Data (Loading a layer from a file) | https://docs.qgis.org/3.44/en/docs/user_manual/managing_data_source/opening_data.html | 2026-09-19 |
| X26 | QGIS Project | QGIS Desktop 3.44 User Guide — QGIS Configuration (Options: CRS handling for layers without a CRS; Project Properties: CRS "No CRS") | https://docs.qgis.org/3.44/en/docs/user_manual/introduction/qgis_configuration.html | 2026-09-19 |
| X27 | GDAL / OSGeo | GDAL documentation — AAIGrid: Arc/Info ASCII Grid raster driver | https://gdal.org/en/stable/drivers/raster/aaigrid.html | 2026-09-19 |
| X28 | GDAL / OSGeo | GDAL documentation — gdal_translate program (`-tr`, `-r` options) | https://gdal.org/en/stable/programs/gdal_translate.html | 2026-09-19 |
| X29 | QGIS Project | QGIS — Download page (release labels: 3.44 LTR, 4.2 latest) | https://qgis.org/download/ | 2026-09-19 |

---

# Instructor Appendix (separate from learner material)

Learners should not read this appendix before completing the 4.8 gate.

## I.1 Answers and reasoning — comprehension checks

**4.1.** (a) F5 says only that the 1 ha cell spanning x 900–1000, y 500–600 was classified *Built-up* — the dominant cover of that area under whatever classification produced the file. (b) A raster holds no objects; a drain is far smaller than a 100 m cell, and the code 1 is consistent with a drain being present, absent, or anywhere in the cell. The raster neither confirms nor denies it; only the asset table (Chapter 3) asserts the drain. (c) Any area-by-class question — "how much of Ward A is built-up / water / vegetation?" — which the three-row asset table cannot answer at all.

**4.2.** Three of many: a height of 21 m; a height of 21 ft; a category code 21 (a land-use class) wrongly displayed with a ramp; a count of 21 requests; a temperature of 21 °C. The settling information is the dataset's documented meaning and unit (or dictionary) for that band — metadata, not anything visible in the layer.

**4.3.** (a) Left 1000, right 1000 + 40 × 25 = 2000, bottom 0, top 25 × 25 = 625. (b) P4 is at y = 900, above the top edge of 625, so it lies outside the raster and has no cell. (c) Not in the sense that matters: the cell sizes differ (25 m versus 100 m) and the extents do not overlap — they abut at x = 1000. The grid *lines* are compatible (the origin (1000, 0) is a multiple of 100 in both directions, so every F5 edge would coincide with an edge of the 25 m grid if the extents overlapped, with 4 × 4 fine cells per F5 cell); a strong answer notes this nesting and still says that no cell-to-cell correspondence exists.

**4.4.** (a) NoData treated as 0: the tell is that 12.75 × 16 = 204, the sum of the valid cells, so the denominator was 16, not 15. (b) (204 − 9999) ÷ 16 = −612.1875 ≈ −612.2. (c) "Mean height 13.6 m above TD-0 over the 15 valid cells (15 ha); 1 cell (1 ha, 6 % of the extent) has no data and was excluded."

**4.5.** (a) Nearest neighbour — the output must contain only codes that exist in the input; majority is for aggregation (coarser output), not refinement. (b) It will show nine identical 10 m cells per 30 m source cell, so that the grid coincides with the footprint layer's grid; it will not show any land-cover boundary finer than 30 m. (c) "Land cover at 10 m cell size, produced by nearest-neighbour resampling from the 30 m source [name, date]; the effective resolution and positional accuracy remain those of the source; no boundary in this dataset is more precise than 30 m."

**4.6.** (a) A DSM — "you can see the buildings" means the surface includes objects. (b) Cannot establish: the unit and zero level; the positional and height accuracy; whether parts of the surface are interpolated or NoData; that the surface is validated at all. (c) The unit and vertical reference, and the dataset's own definition (terrain or surface) — followed by cell size and NoData value.

## I.2 Answer key — 4.8 concept and scenario questions

**Q1.** (b). (a) requires counting objects; a raster has no objects and a streetlight is smaller than a cell. (c) requires identities and distances between objects. (d) requires a line feature; the raster holds no lines (a learner who says the road's *cells* could be counted should note that this measures built-up cells, not the road's length).

**Q2.** In order: (1) the layer's renderer and legend rule — which value range or code maps to dark red (symbology pane / legend); (2) the cell's stored value (identify / pop-up); (3) which band, if there is more than one (layer properties); (4) whether the cell could be NoData drawn in colour, or a transparent cell showing a layer beneath (symbology NoData and transparency settings; layer order); (5) the unit or dictionary for the value (dataset documentation / metadata). Award full marks for (1), (2), (5) in a sensible order with (3) and (4) mentioned.

**Q3.** (a) Left 1200, right 1200 + 8 × 50 = 1600, bottom 300, top 300 + 6 × 50 = 600. (b) Column: floor((1330 − 1200) ÷ 50) = floor(2.6) = 2 (0-based) → **column 3**; row from bottom: floor((460 − 300) ÷ 50) = floor(3.2) = 3 → row from top = 6 − 1 − 3 = 2 (0-based) → **row 3**. Edge rule: `floor`, i.e. a cell owns its left and bottom edges; (1330, 460) is interior so the rule does not change the answer here, but it must be stated. (c) No — a 5 m feature is a tenth of a cell; it may influence the cell's value but is not represented as an object. (d) No; cell size is grid spacing, and positional accuracy depends on how the data was captured and georeferenced.

**Q4.** (a) Excluding NoData: (4 + 0 + 6 + 2) ÷ 4 = **3.0**; NoData as 0: 12 ÷ 5 = **2.4**. (b) The 0 cell asserts "this cell was counted and contained no requests"; the NoData cell asserts "this cell was not counted; nothing is known." (c) 3.0, accompanied by the policy and coverage: "mean 3.0 requests per cell over the 4 counted cells; 1 cell was not counted."

**Q5.** (c). (a) and (b) compute averages of codes, producing non-codes or altered codes (4.5.2); (d) is also arithmetic on codes. The 10 m output will not contain any boundary or class finer than the 30 m source; every 3 × 3 block of output cells is a copy of one input cell.

**Q6.** (a) The unit (metres or feet) and the vertical reference — the zero level and whether values are positive up. (b) No; a hillshade is a derived rendering of whatever heights are present, and a DTM and a DSM both shade convincingly; the definition must come from the dataset's documentation. (c) Any of: an overhang, a bridge with a road beneath, a tunnel, a building interior, a multi-storey structure.

**S1.** Checks, in order: (1) read the NoData value (255) and count NoData cells and their area; (2) establish the dictionary — the note defines 1 only; determine whether 0 means "observed, not flooded" or was used as a filler (if 0 could mean "unknown", the raster's NoData handling is broken); (3) determine the dashboard's denominator — all cells in the raster's rectangular extent, cells inside the ward polygon, or valid (non-NoData) cells inside the ward — and whether the raster's extent exceeds or falls short of the ward; (4) confirm cell size and count (20 m cells over 1 km² is 2,500 cells) so that the percentage's arithmetic can be reproduced; (5) confirm the date/time the extent represents. Two honest statements: "62 % of the *classified* cells inside Ward B are flooded; N cells (x % of the ward) have no data" and "62 % of Ward B's *area* is classified flooded, with the unclassified x % counted as not flooded" — a learner must show that these are different claims and that the second requires a stated decision about NoData. What the raster cannot tell you: depth, duration, cause, the accuracy of the classification, and anything inside a 20 m cell.

**S2.** (a) No. The image has no georeferencing: no world file and no header transformation, so software can only use the row and column numbers as coordinates (an identity transformation [X13]). Needed: either georeferencing from the source (a world file or header carrying corner coordinate, pixel size, and rotation, plus a coordinate system) or a georeferencing process using control points that link identifiable locations on the scan to known coordinates in target data [X18]. (b) Not valid. Resampling to 0.1 m pixels changes the *cell size*; it cannot add positional accuracy [X03], and the accuracy of a georeferenced scan is limited by the scan's original detail, the control points, and the target data it is aligned to — "your georeferenced data is only as accurate as the data to which it is aligned" [X18]. The contractor has conflated resolution with accuracy (4.3.1). (c) The coordinate system and its units in which it was placed (Chapter 5) — with the estimated positional accuracy from the georeferencing residuals recorded alongside; accept either as the "one property" if the other is mentioned.

## I.3 Expected results — fixture derivations and the 4.7 lab

**F5 counts by row (top to bottom).** Water (0): rows 1–3 → 3 + 3 + 1 = 7. Built-up (1): rows 5–6 → 20; rows 7–8 → 2 + 2 = 4; total 24. Bare ground (3): row 3 → 2; row 4 → 4; rows 7–8 → 2 + 2 = 4; total 10. NoData (−1): rows 9–10 → 2 + 1 = 3. Vegetation (2): by row 7, 7, 7, 6, 0, 0, 6, 6, 8, 9 = 56. Check: 7 + 24 + 10 + 3 + 56 = 100.

**F6 sums by row.** 12 + 14 + 17 + 21 = 64; 11 + 13 + 15 + 18 = 57; 10 + 11 + 13 + 15 = 49; 9 + 12 + 13 = 34; total 204 over 15 valid cells; mean 13.6; min 9.0; max 21.0. Alternatives: 204 ÷ 16 = 12.75; (204 − 9999) ÷ 16 = −612.1875.

**Cell lookups (Table 4.7c basis).** Input cell centres: columns at x = 50, 150, 250, 350 (F6) or 50, 150, …, 950 (F5); rows at y = 950, 850, 750, 650 (F6) or 950, 850, …, 50 (F5). Output centres for 50 m cells: x = 25, 75, 125, …; y = 975, 925, 875, …. Nearest neighbour copies the input cell whose centre is nearest the output centre; every output centre is 25 m from one input centre and 75 m from the next in each axis, so each input cell is copied into a 2 × 2 block exactly. Bilinear weights at a 2× resample are always 0.75/0.25 in each axis, giving products 0.5625, 0.1875, 0.1875, 0.0625.

**Expected `elevation_50m_nearest.tif` (8 × 8; hand-checked; ND = NoData).**

```
12 12 14 14 17 17 21 21
12 12 14 14 17 17 21 21
11 11 13 13 15 15 18 18
11 11 13 13 15 15 18 18
10 10 11 11 13 13 15 15
10 10 11 11 13 13 15 15
 9  9 ND ND 12 12 13 13
 9  9 ND ND 12 12 13 13
```

Sum 816 over 60 valid cells; mean 13.6; 4 NoData cells.

**Expected interior values of `elevation_50m_bilinear.tif` (hand-checked arithmetic from the tool page's definition; "edge" = fewer than four surrounding input centres, "ND-adj" = one neighbour is NoData; both are implementation-specific and must be recorded, not predicted).**

| Row \ Col | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| 1 | edge | edge | edge | edge | edge | edge | edge | edge |
| 2 | edge | 12.25 | 13.25 | 14.4375 | 15.8125 | 17.4375 | 19.3125 | edge |
| 3 | edge | 11.75 | 12.75 | 13.8125 | 14.9375 | 16.3125 | 17.9375 | edge |
| 4 | edge | 11.1875 | 12.0625 | 13.0 | 14.0 | 15.1875 | 16.5625 | edge |
| 5 | edge | 10.5625 | 11.1875 | 12.0 | 13.0 | 14.0625 | 15.1875 | edge |
| 6 | edge | ND-adj | ND-adj | ND-adj | ND-adj | 13.1875 | 14.0625 | edge |
| 7 | edge | ND-adj | ND-adj | ND-adj | ND-adj | 12.5625 | 13.1875 | edge |
| 8 | edge | edge | edge | edge | edge | edge | edge | edge |

The 28 hand-checkable interior values lie between 10.5625 and 19.3125 — inside the input range 9–21 — and sum to 391.0. The whole-raster mean depends on the 36 edge and NoData-adjacent cells and is therefore *not* predicted; a submission that reports any value between roughly 12 and 15 with the NoData count recorded is acceptable, and the instructor should record the actual value on first run (I.6, V4).

**Expected `landcover_50m_nearest.tif` (20 × 20).** Each F5 cell becomes a 2 × 2 block; counts: Water 28, Built-up 96, Vegetation 224, Bare ground 40, NoData 12; areas unchanged (each 50 m cell is 0.25 ha).

**Expected `landcover_50m_bilinear.tif`.** Interior cells whose four neighbours share a code keep it; cells between different codes receive weighted averages such as 0.5, 1.5, 0.75, 2.25, 2.5 (or their integer roundings). At (725, 875) the arithmetic gives 0.5 from neighbours 2, 0, 2, 0. The learner's record must show at least one value outside {0, 1, 2, 3} or one code at a position where the source had a different code.

**Table 4.7a expected rows.**

| Raster | Cols × rows | Bands | Cell size | Pixel type | NoData | Extent (L, R, B, T) | CRS | Min / max / mean |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| landcover_training.asc | 10 × 10 | 1 | 100, 100 | integer | −1 | 0, 1000, 0, 1000 | undefined | 0 / 3 / (mean of codes is meaningless; if shown, 1.71 over 97 cells) |
| elevation_training.asc | 4 × 4 | 1 | 100, 100 | floating point | −9999 | 0, 400, 600, 1000 | undefined | 9.0 / 21.0 / 13.6 |
| elevation_50m_nearest.tif | 8 × 8 | 1 | 50, 50 | floating point | −9999 (or as written by the tool) | 0, 400, 600, 1000 | undefined | 9.0 / 21.0 / 13.6 |
| elevation_50m_bilinear.tif | 8 × 8 | 1 | 50, 50 | floating point | as written | 0, 400, 600, 1000 | undefined | within 9.0–21.0 / record |
| landcover_50m_nearest.tif | 20 × 20 | 1 | 50, 50 | integer | −1 (or as written) | 0, 1000, 0, 1000 | undefined | 0 / 3 |
| landcover_50m_bilinear.tif | 20 × 20 | 1 | 50, 50 | record | as written | 0, 1000, 0, 1000 | undefined | record |

(The mean of F5's codes, 166 ÷ 97 ≈ 1.71, is included only so that an instructor can recognise it if a tool displays it; learners should state that it is meaningless.)

## I.4 Expected results — 4.8.3 practical (`flood_depth_check.asc`)

1. 5 × 4 = **20 cells**; cell area 25 × 25 = **625 m²**; extent left **1500**, right 1500 + 125 = **1625**, bottom **100**, top 100 + 100 = **200**.
2. NoData cells: **3** (row 2 col 5; row 3 cols 4–5). Valid: **17**. Cells exactly 0: row 1 → 2, row 2 → 1, row 3 → 2, row 4 → 3 = **8**. A 0 asserts "surveyed; no standing water"; NoData asserts "not surveyed; depth unknown."
3. Row sums 1.1 + 1.0 + 0.2 + 0.4 = **2.7**. Mean over valid cells 2.7 ÷ 17 = **0.1588 ≈ 0.159 m**. NoData as 0: 2.7 ÷ 20 = **0.135 m**. (If −99 were treated as data: (2.7 − 297) ÷ 20 = −14.715 m.) Report 0.159 m: "mean standing-water depth 0.16 m over the 17 surveyed cells (10,625 m²); 3 cells (1,875 m², 15 % of the extent) were not surveyed."
4. Q1 (1560, 130): column floor(60 ÷ 25) = 2 → **column 3**; row from bottom floor(30 ÷ 25) = 1 → row from top 4 − 1 − 1 = 2 → **row 3**; value **0.2 m**. P3 (1200, 250) is outside the extent (x < 1500, y > 200): **no value**.
5. Nearest to 12.5 m: **10 × 8 = 80 cells**; value at Q1 **0.2** (each input copied into a 2 × 2 block); NoData **12**; mean over 68 valid cells 10.8 ÷ 68 = **0.159 m**, unchanged. Appropriate method: depth is a continuous quantity, so an interpolating method (bilinear) is *admissible* — the average of 0.2 and 0.4 m is a meaningful 0.3 m — and nearest neighbour is also defensible when measured values must be preserved; either answer earns credit **if** the numerical reason is given and the learner notes that bilinear near the three NoData cells and at edges is implementation-specific. Full credit requires rejecting the idea that the 12.5 m output is more detailed.
6. Cannot establish: flooding outside the extent or in the 3 unsurveyed cells; the depth at Q1's exact point — 0.2 m is the value for a 625 m² cell.
7. Software confirmation: the same numbers; differences to explain are the NoData policy of the statistics tool (0.135 versus 0.159) and any edge-rule effect if a learner clicks exactly on a cell boundary.

**Retest variant (fresh values, same concepts; per blueprint B.1.5).** Use a 4 × 5 grid, cell 20 m, origin (300, 1200), NoData −1, with two NoData cells and three zeros; ask the same seven items with a point inside and a point outside the extent. The instructor computes the answers by the same method before issuing it.

## I.5 Common mistakes and remediation

| Mistake | Where it shows | Remediation |
| --- | --- | --- |
| Reading the listing bottom-up | DR-0042 reported as Vegetation; pond at the bottom of the map | Re-derive the row formula with the pond as the check: it must be at the *top* right |
| NoData counted as zero | Mean 12.75; "3 ha of water" | Redo 4.4.2 with F7 and F6; require the policy sentence with every statistic thereafter |
| NoData marker not recognised | Mean ≈ −612 | Inspect the NoData value in properties; the number is the diagnosis |
| Colour read as meaning | "The north-east is blue so it is water" from a stretched elevation layer | Show the same cell under two renderers (4.2.1 worked example); require the value and the dictionary |
| Cell size read as accuracy | "1 m resolution so accurate to 1 m" | S2 and 4.3.1; the decimal-places analogy |
| Bilinear chosen for land cover "because it looks better" | Non-code values or spurious Built-up strips | The (725, 875) cell; require the learner to explain what 0.5 means in Table F5b |
| Finer grid described as more detailed | "the 50 m version shows more" | Figure 4.5; the nearest-neighbour mean invariance (13.6 both times) |
| Display resampling confused with data resampling (QGIS) | Symbology *Resampling* set to bilinear and reported as "resampled" | Show that *Information* is unchanged; run Translate with `-r` |
| Point on a cell corner assumed to have one value | P1 reported as one code without comment | 4.3.2 boundary case; require the edge rule to be stated |
| Basemap left in the map | Raster drawn "in the ocean" | Step 1; preview Chapter 5's lesson that raw coordinates without a CRS place nothing |

## I.6 Building and verifying the lab on first run — not execution-tested

The author did not run this lab. Before issuing it, an instructor should create the two `.asc` files exactly as listed, run Route A (and Route B if QGIS is offered), and confirm the following **verification items**, recording the software version from *Help* → *About* and the date:

- **V1 — Row order.** On loading `landcover_training.asc`, the pond (code 0) must appear at the top-right (y 900–1000). If it appears at the bottom, the reader treats the first data row as the bottom row, and the fixture listings must be reversed for that software (this would contradict the ASCII format description read for this chapter [X07] and should be reported).
- **V2 — Pixel type of F6.** Confirm that the elevation raster loads as floating point and that (50, 950) reads 12.0, not 12. If integer, use the Copy Raster route in Troubleshooting and update the procedure box.
- **V3 — Statistics NoData policy.** Record the mean each application reports for F6 (13.6 expected). For QGIS's *Raster layer statistics* the documentation is silent [X24]; for ArcGIS Pro's properties/Calculate Statistics, confirm whether NoData is excluded by default and whether *Ignore Values* is needed [X12].
- **V4 — Bilinear edge and NoData-adjacent behaviour.** Record the actual values at (25, 975) and (175, 675) and the whole-raster mean and NoData count of `elevation_50m_bilinear.tif` in each application, and add them to I.3 as "observed on [version, date]".
- **V5 — Categorical bilinear output type.** Record whether `landcover_50m_bilinear.tif` is integer or floating point and the value at (725, 875) (0.5 expected mathematically; 0 or 1 if integer).
- **V6 — Edge rule.** Record what each application reports for P1 (200, 200) and P2 (800, 800) and which cell that implies.
- **V7 — Unknown coordinate system handling.** Record the exact warning or prompt each application shows on load, and confirm that with no basemap (Route A) or *No CRS* (Route B) the raster draws at its raw coordinates.
- **V8 — Interface labels.** Confirm the labels used in the procedure boxes (*Add Data*, *Properties* → *Source*, *Symbology* → *Unique Values* / *Stretch*, *Resample*'s parameter names; QGIS *Information*, *Paletted/Unique values*, *Singleband pseudocolor*, *Translate (convert format)* → *Additional command-line parameters*) and correct any that differ.

Until V1–V8 are recorded, the lab's software steps remain *not execution-tested*; the hand-checked expectations in I.3 stand regardless.

## I.7 Blueprint corrections, source notes, and verification items

- **No factual error was found in the blueprint's Chapter 4 specification or Appendix A.3.** The A.3 arithmetic (eight valid cells, sum 160, mean 20; 160 ÷ 9 ≈ 17.78) and the 4.3.2 arithmetic (ten 10 m columns span 100 m) were re-checked and are correct.
- **Deprecated tool.** The blueprint does not name a conversion tool, but instructors following older material may reach for *ASCII To Raster*; its page is marked as a deprecated tool in the ArcGIS Pro 3.7 documentation, which directs users to *Copy Raster* [X07] [X08]. This chapter reads `.asc` files directly (the format is listed as read-only supported [X09]) and names Copy Raster only for a floating-point conversion if needed.
- **Source simplifications to flag when narrating [S11].** The *Gentle Introduction* says georeferencing "is often provided in a small text file accompanying the raster"; many formats (GeoTIFF among them) store it in the file header instead, and Esri's world-file page describes both [X13]. It also says raster layers "do not usually have any attribute data"; categorical rasters can carry a raster attribute table in both platforms (QGIS's properties dialog has a Raster Attribute Table section [X21]; Esri's introduction mentions linking cells to an attribute table [X01]). Neither statement is wrong for beginners; both should be qualified if asked.
- **Version note.** The QGIS download page on the check date offered 3.44 as the Long Term Release and 4.2 as the latest release [X29]. This chapter's QGIS procedures cite the 3.44 user guide; Chapter 2 cited 3.40. If the training environment runs QGIS 4.x, re-verify labels before issuing Route B; the documentation for 4.2 was not consulted.
- **Cited claims that are general principle versus platform behaviour.** General: the grid model, resolution versus accuracy, NoData versus zero, the need for a resampling method, and the height-field limit. Format rule: the `.asc` header semantics [X07] [X27]. Platform behaviour: default renderers [X11] [X21], NoData storage and promotion [X02], the four Resample methods and their descriptions [S12], GDAL's kernel list [X22] [X28], and every interface label. Teaching simplification: the training datum TD-0, the category dictionary, and the "floor" edge rule presented in 4.3.2.

## I.8 Instructor change log

| Date | Change | Affected items |
| --- | --- | --- |
| 2026-09-19 | Chapter 4 document revision 1.0 issued; no software verification performed; V1–V8 open | 4.7, I.3, I.6 |

---

# Media Appendix

## M.1 Slide outline (mapped to module IDs)

| Slide group | Module | Slides | Learning objective of the sequence | Question to pose before revealing |
| --- | --- | --- | --- | --- |
| A. A grid of numbers | Recap / 4.1 | 1. Chapter 3's vector town with the question "what about the ground itself?"; 2. Fixture F7 as nine numbers (no colour); 3. rows, columns, cells, NoData labelled; 4. discrete objects versus a sampled surface (Figure of the school grounds as points/polygons, then as a grid); 5. the "which model fits" table | LO1 | "What is in row 2, column 3 — and what does it mean?" (reveal: 30, and nothing yet) |
| B. Values and colours | 4.2 | 6. F5 rendered with random default colours; 7. the same F5 with Table F5b colours; 8. the same cell under three renderers; 9. one band (F6) beside a three-band synthetic image with its per-band values; 10. "what you need before a value means anything" table | LO2 | "Which of these two maps is right?" (reveal: neither is data) |
| C. Resolution and extent | 4.3 | 11. ten 10 m cells = 100 m; 12. F5's header → extent; 13. coordinate-to-cell arithmetic with DR-0042; 14. P1 on a corner; 15. the decimal-places analogy (resolution ≠ accuracy); 16. Figure 4.3 misaligned grids | LO3 | "Which cell is (995, 510) in?" before the arithmetic |
| D. NoData | 4.4 | 17. zero / NoData / mask / transparent table; 18. F7 mean two ways (20 versus 17.78); 19. F6 mean three ways (13.6 / 12.75 / −612); 20. request-count grid: "not counted" versus 0 | LO4 | "What is the mean of these nine cells?" (reveal both answers) |
| E. Resampling | 4.5 | 21. why a new grid needs a rule; 22. three methods table; 23. display resampling versus data resampling; 24. the (725, 875) land-cover cell: 0.5 → "Built-up?"; 25. Figure 4.5 enlarging a grid | LO5 | "What value should the four new cells get?" (reveal: there is no fact; there is a method) |
| F. Surfaces | 4.6 | 26. F6 as a stepped height field; 27. DTM versus DSM table; 28. unit and zero level (the contractor's 121.0); 29. F6 as numbers, as a ramp, as a hillshade — side by side; 30. "realistic ≠ validated" | LO6 | "Is the ground 100 m higher in the contractor's data?" |
| G. Lab briefing | 4.7 | 31. the two files; 32. Table 4.7a/4.7c; 33. the "record" rows and why they exist | LO7 | — |
| H. Gate | 4.8 | 34. assessment structure and the five critical misconceptions | — | — |

Speaker notes for each slide should be drawn from the corresponding module text; do not add capability claims that are not in the chapter.

## M.2 Audio-lesson outline

**Rules for the narration.** Describe every grid by naming rows, columns, and values — "row one, column three holds twenty" — and never by colour ("the blue cells" is forbidden; say "the cells holding code zero, which the legend paints blue"). Introduce each term once, in the order below. Pause for prediction before each computed answer. Detailed click instructions stay in the lab handout.

| Segment | Duration guide | Content | First mentions (script) |
| --- | --- | --- | --- |
| 1. From objects to grids | 3 min | Chapter 3 recap; "some things have no edges"; the nine numbers of F7 read aloud row by row, including "row two, column two: no data" | "raster — a grid of cells with values"; "cell, also called pixel"; "NoData — a stated absence" |
| 2. What a value can be | 4 min | Continuous versus categorical, using F6's 13.0 metres and F5's code 2; the table of what arithmetic makes sense; the built-up-area worked example with its caveats | "continuous"; "categorical, also called thematic"; "category dictionary" |
| 3. Colours are not values | 4 min | The same cell described under three renderers; the four things a blue cell could be; "never infer units from appearance" | "renderer — the rule that turns values into colours"; "band — one full grid of values"; "multiband" |
| 4. Cell size, extent, and finding a cell | 5 min | "Ten columns of ten-metre cells span one hundred metres"; F5's header read as four numbers; DR-0042's arithmetic spoken step by step with a pause before "row five, column ten"; P1 on a corner; resolution is not accuracy | "cell size, also called spatial resolution"; "extent — left, right, bottom, top"; "origin — the lower-left corner in this file format"; "alignment" |
| 5. Zero versus NoData | 5 min | The four-way table; F7's mean with a pause: "eight cells or nine?"; F6's three means and what each diagnoses; "not counted is not zero" | "mask"; "missing-data policy" |
| 6. Resampling | 5 min | Why the four new cells have no fact; nearest neighbour, bilinear, cubic in one sentence each; the land-cover cell that becomes 0.5; "a finer grid is not more data" | "resampling"; "nearest neighbour"; "bilinear interpolation"; "cubic convolution"; "majority" |
| 7. Surfaces | 4 min | F6 as a height field described by walking its rows; terrain versus surface; unit and zero level; "a hillshade is a picture of the numbers" | "height field"; "digital terrain model, D-T-M"; "digital surface model, D-S-M"; "vertical reference"; "hillshade" |
| 8. Lab and gate briefing | 2 min | The two files, the tables, the four "record" rows, the five critical misconceptions | — |

Figures must be described in words: for Figure 4.3, "two rows of squares; the lower row is shifted half a square to the right, so every lower square straddles two upper squares"; for Figure 4.5, "four large squares; then the same four as sixteen small squares with the same numbers; then sixteen small squares whose numbers grade smoothly from eleven to fourteen."

## M.3 Diagram specifications

**D1 — Numeric grid (F7).** A 3 × 3 table drawn as squares, each with its number centred; the centre square shows the text "NoData" on a hatched fill. Row and column labels outside the grid. No colour fill on any numbered cell. Caption: "A raster is numbers first."

**D2 — Same data, two renderers (F5).** Two copies of the 10 × 10 grid side by side. Left: arbitrary distinct colours per code with a legend reading "0, 1, 2, 3, NoData" only. Right: the Table F5b colours with class names. Between them, one cell (row 5, column 10) connected by a line to a callout "value 1 in both". Caption: "The file did not change."

**D3 — Coordinate to cell (F5).** The 10 × 10 grid with x and y axes in metres (ticks every 100), DR-0042 plotted at (995, 510), a bracket along the x axis from 900 to 1000 labelled "column 10", a bracket along the y axis from 500 to 600 labelled "row 5 (counted from the top)". A second inset shows P1 at (200, 200) sitting on the shared corner of four cells with their four codes written in. Caption: "Rows count down; y counts up; corners need a rule."

**D4 — Misaligned grids (Figure 4.3).** As described in 4.3.3: F6's column edges at 0, 100, 200, 300, 400 above a second row of squares with edges at 50, 150, 250, 350, 450. Caption: "Same cell size, different origin."

**D5 — Two means (F7).** The 3 × 3 grid twice: left with the centre cell hatched and the sum "160 ÷ 8 = 20" beneath; right with the centre cell showing "0" in red and "160 ÷ 9 = 17.78" beneath. Caption: "One cell, two policies, two answers."

**D6 — Enlarging a grid (Figure 4.5).** Three panels as described in 4.5.3, with values written in every small square (nearest: 12 12 14 14 / 12 12 14 14 / 11 11 13 13 / 11 11 13 13; bilinear interior: 12.25 13.25 / 11.75 12.75 with the edge cells marked "?"). Caption: "More squares, same measurements."

**D7 — Height field and the two surfaces (F6).** Left: F6 as a stepped block diagram with a labelled z axis in metres above TD-0 and the NoData cell shown as a gap. Right: a cross-section through one row showing a bare-ground line (DTM) and a line over a building and a tree (DSM), labelled. Caption: "One height per cell; which surface is stated by the dataset, not by the picture."

All diagrams are schematic. None represents measured geography, and every value shown is from the synthetic fixtures.

## M.4 Table of expected values for slides (from I.3)

| Slide use | Values |
| --- | --- |
| F7 mean | 20 (exclude NoData); 17.78 (NoData as 0) |
| F6 mean | 13.6 (exclude); 12.75 (as 0); −612.2 (marker not recognised) |
| F5 counts | Water 7, Built-up 24, Vegetation 56, Bare 10, NoData 3 |
| Lookups | DR-0042 → row 5, col 10 → 1; SL-0113 → row 9, col 3 → 2; P1 → corner of codes 2, 1, NoData, 2 |
| Bilinear 2× interior | 12.25, 13.25, 11.75, 12.75 from inputs 12, 14, 11, 13 |
| Land-cover bilinear at (725, 875) | 0.5 from neighbours 2, 0, 2, 0 |

## M.5 Interactive and 3D material

**Proposed scene: "Sixteen cells, one surface" (optional; justified because the numbers-versus-picture distinction of 4.6.3 is hard to convey on a flat slide).**

- **Learning objective.** LO6: the learner sees that the height field, the rendered surface, and the underlying numbers are three views of one small dataset, and that vertical exaggeration and lighting change the picture without changing a single value.
- **Objects.** (1) A flat 4 × 4 grid on the ground plane at F6's extent (x 0–400, y 600–1000), each cell labelled with its value in metres; the NoData cell drawn as an open hole with the label "NoData". (2) The same grid elevated: each cell raised to its value (metres above TD-0) as a flat-topped block; the NoData cell absent. (3) An optional smoothed surface through the 15 valid cell centres, drawn as a translucent mesh, labelled "interpolated — not measured".
- **Labels and units.** Axes labelled "x (metres, training grid)", "y (metres, training grid)", "height (metres above TD-0, synthetic)". A persistent banner: "Synthetic data. No Earth location. Vertical exaggeration shown at ×[value]."
- **Controls.** (a) *Vertical exaggeration* slider from ×1 to ×10 with the current factor displayed beside the z axis at all times. (b) *Light azimuth* dial (0–360°) and *altitude* slider (0–90°), defaulting to 315° and 45° to match the hillshade function's defaults [X16]. (c) *Show numbers* toggle that prints each cell's value on its top face. (d) *Flat / elevated* toggle to switch between objects (1) and (2). (e) *Show interpolated mesh* toggle for object (3).
- **Expected behaviour.** Changing exaggeration scales block heights and the mesh but never changes any printed value. Changing the light changes shading only; at some azimuths the north-east rise looks like a slope down. With *Show numbers* on, the block at row 1, column 4 reads 21.0 at every exaggeration. The NoData hole never receives a height, a shade, or a mesh vertex.
- **Validation.** (1) With exaggeration ×1, the block heights, measured against the z axis, equal the F6 values (spot-check 12.0, 21.0, 9.0). (2) Toggling the light through 360° leaves all printed values unchanged. (3) The scene reports "15 valid cells, 1 NoData" and a mean of 13.6 in a fixed text box that does not respond to any control. (4) The mesh, if shown, lies between 9.0 and 21.0 everywhere it exists and is absent over the NoData cell.
- **Labelling of simulation.** Every view carries "schematic; thematic heights from a synthetic 4 × 4 grid; vertical exaggeration ×N" in the frame.
- **2D/static and text alternative.** Diagram D7 and the F6 listing convey the same content; the audio segment 7 script describes the scene row by row.

No other interactive or 3D material is proposed. The remaining concepts (NoData policy, resampling, alignment) are taught better by the tables and 2D diagrams above.
