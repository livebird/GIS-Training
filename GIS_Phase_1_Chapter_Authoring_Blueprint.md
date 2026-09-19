# GIS and ArcGIS Fundamentals — Phase 1 Chapter Authoring Blueprint

**Audience:** Software developers at LiveBird Technologies who are new to GIS.  
**Primary destination:** ArcGIS configuration, custom application development, ArcGIS Online operation, and later ArcGIS Enterprise delivery.  
**Transferable destination:** Mapbox, OpenLayers, QGIS, PostGIS, GeoServer, and other geospatial technologies.  
**Scope:** The 12 chapters of Phase 1 only. This is an instructional specification for producing separate chapter documents, not the completed textbook or a vendor certification syllabus.  
**Revision:** 1.0 — 18 September 2026.  
**Evidence:** Official vendor/project documentation and a published format specification. Sources are linked near relevant modules and catalogued in Appendix D. Curriculum order, exercises, and assessment policies are instructional recommendations, not claims of Esri endorsement.

## A. Instructions for the chapter author or AI agent

### A.1 Preserve the instructional sequence

1. Use the chapter sequence below and retain the module identifiers, such as **5.4**. Expand individual modules with subordinate identifiers, such as **5.4.1**, without renumbering the master outline.
2. Teach each chapter in module order. Explain a concept before using it in an exercise. Introduce unfamiliar terminology at first use and include it in the chapter glossary.
3. The numbered points under each module are ordered authoring requirements. In the generated chapter, their full identifiers are chapter.module.point. Do not treat them as optional keywords.
4. Begin every chapter with its prerequisites, observable outcomes, required materials, and a brief recap of the preceding chapter. End with the lab, independent assessment, and a transition to the next chapter.
5. Use the recurring municipal asset/service-request scenario in Appendix A. Provide an additional scenario when necessary to show that a concept generalizes beyond municipal work.
6. Keep Chapters 1–2 introductory. They may preview a layer, service, or CRS without expecting technical mastery. Chapters 3–12 then develop the concepts in dependency order.
7. Use basic map navigation and table inspection from the beginning. Provide a small tool-orientation appendix where needed; do not turn Phase 1 into a long software-interface course.
8. Teach one primary laboratory workflow per chapter. Prefer ArcGIS Pro when the training environment is licensed; provide a QGIS route for foundational tasks when access is unavailable. Verify the alternate workflow rather than assuming identical tools or outputs.

### A.2 Required depth and chapter components

1. For every conceptual module, provide a plain-language explanation, a developer-oriented analogy with its limits, one concrete example, one misconception, and a short comprehension check.
2. Explain why an operation is appropriate before describing the buttons or code. Include units, assumptions, input requirements, and how to judge whether an output is reasonable.
3. Build diagrams from the actual concept or verified dataset. Use labeled schematics for conceptual illustrations; do not present generated artwork as measured GIS evidence.
4. Supply a reproducible guided lab and a separate unfamiliar exercise. Every lab must specify inputs, coordinate systems where relevant, steps, expected invariants, deliverables, and troubleshooting checks.
5. Include a glossary, short recap, and cross-references to later chapters. Provide instructor answers separately from the learner-facing assessment.
6. Add a media brief: slide outline, narration outline, visual descriptions, and any justified interactive/3D scene specification. Do not create decorative 3D content where a table or 2D diagram teaches better.
7. Use depth appropriate to experienced programmers who are GIS beginners. No calculus, advanced geodesy, remote-sensing science, spatial statistics, or Enterprise installation is required in this phase.

### A.3 Evidence and accuracy rules

1. Follow the linked sources for factual and product-specific statements. Read the relevant section, not just its title. Cite the exact supporting page next to the claim in the expanded document.
2. The reference pages were retrieved for this blueprint on the revision date. QGIS links use a pinned documentation edition for stable concepts; Esri `latest` links can change. Record the actual installed product version when writing practical instructions.
3. Verify current UI labels, tool parameters, licenses, privileges, credit use, and platform support before writing a hands-on ArcGIS workflow. Do not invent access, subscription costs, entitlements, or free-license availability.
4. If a source is unavailable or does not establish a proposed claim, identify what needs verification. Do not invent a citation, numerical accuracy guarantee, benchmark, or executed test result.
5. Distinguish a general GIS concept, a format rule, a specific engine's behavior, and a teaching simplification. In particular, coordinate order and spatial-predicate semantics must be tied to the relevant API/format.
6. Label synthetic coordinates, observations, boundaries, and calculations as training examples. Do not imply that simulated municipal records describe Ahmedabad or any real authority.
7. Verify computed answers against the supplied fixture. Explain tolerances and rounding. Do not report invented counts or measurements for data that have not been created and checked.
8. Separate durable conceptual content from version-specific procedure boxes. This allows later updates without rewriting the entire chapter.
9. Provide original explanations and original diagrams; paraphrase sources selectively. Do not copy vendor chapters, screenshots, or exercises wholesale.

## B. Chapter sequence and dependency map

| Chapter | Title | Essential prerequisite | Required capability at completion |
| --- | --- | --- | --- |
| 1 | GIS fundamentals and spatial thinking | General software literacy | Convert a business problem into a spatial question. |
| 2 | Understanding the ArcGIS ecosystem | Chapter 1 | Explain the roles of desktop GIS, hosted platforms, servers, and client libraries. |
| 3 | Geographic data: features, geometry, attributes, and layers | Chapters 1–2 | Choose and inspect suitable vector representations. |
| 4 | Raster data and geographic surfaces | Chapter 3 | Interpret raster cells, values, resolution, and missing data. |
| 5 | Coordinates and coordinate reference systems | Chapters 3–4 | Interpret coordinates with their CRS, units, and order. |
| 6 | Projections, transformations, and accurate measurement | Chapter 5 | Select a defensible transformation and measurement workflow. |
| 7 | GIS data formats, sources, and metadata | Chapters 3–6 | Inspect a dataset's suitability and exchange limitations. |
| 8 | GIS data modeling and relationships | Chapters 3 and 7 | Design a coherent asset, inspection, and request schema. |
| 9 | Data capture, editing, and quality | Chapters 5–8 | Detect and resolve data-quality problems with an audit trail. |
| 10 | Attribute queries and spatial relationships | Chapters 8–9 | Predict query results, including joins and boundary cases. |
| 11 | Spatial analysis fundamentals | Chapters 6 and 9–10 | Build and validate a small reproducible analysis. |
| 12 | Cartography and interpreting maps correctly | Chapters 3–11 | Communicate the result without concealing limitations. |

## Chapter 1 — GIS fundamentals and spatial thinking

**Entry requirement:** No GIS experience.  
**Outcomes:** Explain GIS beyond map display; frame an answerable spatial question; identify required data and limitations.  
**Boundary:** Do not teach projection mathematics, software installation, or spatial SQL here.

### 1.1 Start with a decision, not a map

1. Introduce the recurring question: “Which unresolved service requests should an inspection team investigate first?” Separate operational policy from the evidence GIS can provide.
2. Ask what “near,” “urgent,” “unresolved,” and “service area” mean. Require the author to show why vague requirements produce ambiguous GIS results.
3. Compare a location-free request table with the same records on a simple map. Ask what each representation makes easier to see; avoid claiming that a map is always the best display.

### 1.2 Explain what a GIS includes

1. Introduce geographic data, software, hardware, and the people/processes that maintain and use them. Explain capture, storage, analysis, and communication. [S01]
2. Distinguish GIS, a static map image, a navigation application, and a table containing addresses. Explain how a table can become one input to a GIS.
3. Show a complete question-to-decision workflow with a validation step before reporting. Treat this as the course's working method, not a product architecture.

### 1.3 Introduce spatial thinking

1. Organize questions around location, distribution, proximity, containment, connectivity, and change through time. Use one short question for each.
2. Explain absolute location versus relative descriptions such as “beside the school.” Introduce spatial extent as the area being considered.
3. Contrast “nearest by straight-line distance” with “quickest to reach by road” without teaching routing algorithms.

### 1.4 Identify data and its meaning

1. Preview geometry, descriptive attributes, and time using one service request. Reserve formal vector structures for Chapter 3.
2. Distinguish an observed event from an asset and an administrative boundary. Ask which records need updating and which represent historical observations.
3. Identify who collected the data, when, for what purpose, and what was omitted. These questions precede formal metadata in Chapter 7.

### 1.5 Introduce layers and elementary map interaction

1. Demonstrate turning roads, requests, and wards on/off, panning, zooming, and selecting a record. Use a prepared project so setup does not obstruct the first lesson. [S01]
2. Ask trainees to describe the difference between hiding information and deleting data.
3. Require a brief observation log: what the map suggests, what the table confirms, and what remains uncertain.

### 1.6 Recognize limits of a spatial conclusion

1. Use a synthetic example where a concentration of reports could reflect population or reporting behavior. Require alternative explanations before proposing a cause.
2. Introduce fitness for purpose: a dataset sufficient for a city overview may be insufficient for locating a pipe during excavation.
3. Explain that a visually convincing output can still be based on incomplete, stale, or incorrectly interpreted data.

### 1.7 Guided lab: write a spatial problem brief

1. Give trainees a short service-request scenario with explicit decision-maker, date range, and study area. Ask them to formulate three answerable spatial questions.
2. For each question, list inputs, selection rules, output, and a validation method. Use plain language; no tool names are required.
3. Deliverable: a one-page problem brief plus an annotated sketch. The instructor checks whether the proposed data actually answer the question.

### 1.8 Independent check and progression gate

1. Present an unfamiliar school-access or delivery-location scenario. Ask the trainee to distinguish questions about display, analysis, and operational decisions.
2. Require one ambiguous term to be clarified and one unsupported conclusion to be rejected.
3. Progress when the trainee can explain the intended decision, required evidence, and an important limitation without relying on a software demonstration.

### 1.9 Media and source brief

1. Slides: problem, table/map comparison, question types, workflow, limitations. Audio: narrate each example without referring to an unseen cursor or color alone.
2. Visual: progressively reveal the same data as records and layers. A 3D scene is unnecessary here.
3. Read [S01] for introductory GIS concepts. The problem brief and assessment above are original instructional designs. Transition to Chapter 2 by asking where each workflow step can happen.

## Chapter 2 — Understanding the ArcGIS ecosystem

**Entry requirement:** Chapter 1.  
**Outcomes:** Explain product roles; trace data to an application; distinguish configuration, development, and operation.  
**Boundary:** Orientation only—no installation, license purchasing, federation, or coding tutorial.

### 2.1 Start with generic GIS responsibilities

1. Introduce desktop authoring, data storage, service delivery, content organization, and user applications as separate responsibilities.
2. Trace one prepared asset dataset through authoring, publishing, map configuration, and use. Call this an illustrative workflow; not every solution requires every component.
3. Distinguish the computer running an application from the system storing authoritative data.

### 2.2 Position ArcGIS Pro

1. Explain its desktop authoring, editing, visualization, and analysis role. Preview projects, maps, tables, and data connections using a short guided demonstration. [S02]
2. Emphasize the difference between a saved project and the datasets it references. Later chapters will inspect storage formats.
3. Ask learners which tasks require authoring a dataset versus changing how it appears.

### 2.3 Position ArcGIS Online

1. Explain hosted mapping, sharing, content management, and organization administration at a conceptual level. [S03]
2. Preview item, web layer, web map, group, member, and application. Do not imply all items contain independent copies of their underlying data.
3. Establish that seeing a public map does not imply permission to edit or republish its data. Defer detailed access configuration.

### 2.4 Position ArcGIS Enterprise

1. Explain that organizations control an Enterprise deployment and its infrastructure responsibilities. It is not simply “ArcGIS Online installed locally.” [S04]
2. Introduce Portal, Server, and managed data-storage roles only far enough to understand content, processing, and storage. Defer the release-specific base-deployment component list.
3. Contrast application use with operating infrastructure: security, backups, monitoring, and upgrades belong to the later Enterprise track.

### 2.5 Position configurable apps, APIs, and SDKs

1. Introduce configurable application builders, dashboards, and field workflows through examples. Explain configuration as arranging supported capabilities for a requirement. [S03]
2. Introduce custom mapping applications, service APIs, and Python automation. Distinguish the ArcGIS Maps SDK for JavaScript, ArcGIS API for Python, and ArcPy at the role level. [S05]
3. Use a decision exercise: configure an available workflow, extend it, or build a custom application. Require a reason based on the requirement rather than developer preference.

### 2.6 Establish transferable roles across platforms

1. Compare roles, not brand equivalence: OpenLayers is a web mapping library; Mapbox offers mapping/location products and services. Neither label alone specifies a complete replacement architecture. [S06] [S07]
2. Position QGIS as desktop GIS, PostGIS as spatial capability for PostgreSQL, and GeoServer as geospatial server software. Avoid implying that each is a one-to-one replacement for an entire ArcGIS product. [S01] [S08] [S09]
3. Have learners label “shared GIS knowledge” versus “platform-specific implementation” for five tasks. Reserve protocols and production architecture for later phases.

### 2.7 Guided lab: trace a prepared solution

1. Supply a screenshot set or supervised demonstration of a dataset, map, and application, with a clearly documented source-to-output relationship.
2. Ask trainees to identify where geometry, styling, access decisions, and application behavior are managed. Accept “not enough information” where the evidence is missing.
3. Deliverable: an annotated architecture sketch and a role comparison table. No paid publishing action is required in this orientation lab.

### 2.8 Independent check and progression gate

1. Present separate requests to edit source geometry, change map colors, customize user interaction, and restore a failed server. Ask which responsibility each involves.
2. Require an explanation of why web mapping code alone does not provide the complete data-management and operations stack.
3. Progress when the trainee can describe the data-to-user path and the limits of their current understanding.

### 2.9 Media and source brief

1. Create one compact role diagram, followed by a product-to-role table. Keep vendor capability and licensing claims dated.
2. Narration should introduce each acronym and product once. Avoid turning the audio into a product-name catalogue. No 3D scene is needed.
3. Core readings: [S02]–[S05]. Cross-platform orientation: [S01], [S06]–[S09]. Move to Chapter 3 to inspect the actual information flowing through these systems.

## Chapter 3 — Geographic data: features, geometry, attributes, and layers

**Entry requirement:** Chapters 1–2.  
**Outcomes:** Choose suitable geometry; inspect geometry/attribute relationships; distinguish source data from layer presentation.  
**Boundary:** Coordinates are introduced as ordered values; CRS interpretation follows in Chapter 5.

### 3.1 Separate the real-world object from its representation

1. Use the same school represented as a point for a city overview and a footprint polygon for a campus plan. Ask what each representation can answer. [S10]
2. Distinguish an asset from an incident at that asset and from an inspection record about it.
3. Define the feature as the represented entity with geometry and descriptive properties. Avoid saying only visible physical objects can be features; administrative and analytical areas also matter.

### 3.2 Teach basic vector geometry in order

1. Explain point, line/polyline, and polygon using asset locations, road centerlines, and wards. Introduce vertices and segments visually. [S10]
2. Distinguish a drawn line's symbol width from the ground width of a road. Explain when an area representation is necessary.
3. Introduce coordinate dimensions: X/Y, optional Z, and measured values where a platform supports them. Do not imply every format has the same dimensional rules.

### 3.3 Introduce geometry structure and exceptions

1. Show single-part versus multipart features and polygons with holes using original diagrams.
2. Distinguish a polygon ring from a closed line that has not been modeled as an area. Reserve validation rules for Chapter 9.
3. Explain that serialization details differ between formats. If using GeoJSON examples, follow its geometry/ring rules and defer full coordinate-order discussion to Chapter 5. [S18]

### 3.4 Connect attributes to geometry

1. Introduce record, field, value, feature identifier, and attribute table. Select a feature and locate its corresponding attributes. [S23]
2. Show one value used for styling and another for filtering. Explain why a display label should not automatically be the business identifier.
3. Preview nonspatial related records such as inspections. Do not flatten all historical observations into one asset row; formal relationships follow in Chapter 8.

### 3.5 Distinguish datasets, layers, and maps

1. Explain a dataset as the stored collection and a layer as its use in a map with presentation and query settings. State that exact terminology varies by platform.
2. Demonstrate two layers referencing the same source but displaying different categories. Distinguish a filtered view from an exported copy.
3. Introduce basemap versus operational data and explain why layer drawing order can hide information.

### 3.6 Inspect extent, selection, and identity

1. Introduce bounding extent as a useful summary of spatial coverage, not the exact occupied shape.
2. Ask trainees to record feature count, geometry type, field names, and empty/missing geometry cases. Defer repair until Chapter 9.
3. Demonstrate a presentation-only change and a geometry edit on a disposable copy. Require trainees to predict which underlying records change.

### 3.7 Guided lab: model and inspect a small town

1. Supply original schematic data containing roads, assets, wards, a multipart area, and an area with a hole. State its artificial coordinate frame and lack of real-world accuracy.
2. Ask trainees to justify geometry choices, inspect attributes, create two presentations of one source, and identify a deliberately unsuitable representation.
3. Deliverable: a geometry-choice table and annotated record/map pairs. Verify that presentation changes do not alter the source feature count.

### 3.8 Independent check and progression gate

1. Give a new use case involving a river, delivery stop, and restricted area. Require geometry choices with explicit scale/purpose assumptions.
2. Test multipart versus multiple records, a polygon hole, and layer visibility versus deletion.
3. Progress when the trainee can explain which information is represented, omitted, and merely styled.

### 3.9 Media and source brief

1. Show a real-world sketch becoming geometry, then attach attribute cards and layer styles. Use original drawings.
2. Optional 3D teaching scene: raise one vertex along a labeled Z axis, then show why storing a Z value alone does not define a building solid. Label this a schematic.
3. Sources: vector representation [S10], attributes [S23], format-specific structures [S18]. Chapter 4 introduces a different representation: grids of values.

## Chapter 4 — Raster data and geographic surfaces

**Entry requirement:** Chapter 3.  
**Outcomes:** Interpret raster values and bands; distinguish display from measurement; explain resolution and NoData.  
**Boundary:** No image classification, photogrammetry, or advanced raster analysis.

### 4.1 Introduce the raster model

1. Explain rows, columns, cells, and cell values using a small numeric grid before showing imagery. [S11]
2. Contrast a discrete object representation with a sampled surface. Include both categorical land-cover and continuous elevation examples.
3. Explain that the suitability of vector or raster depends on the question; do not teach an absolute “vector for objects, raster for everything else” rule.

### 4.2 Interpret cell values and bands

1. Distinguish stored values from display colors. A blue cell may represent a category, elevation interval, or missing-data style.
2. Explain one-band and multiband data through an elevation grid and an imagery example. Mention spectral bands only to the depth needed to interpret metadata. [S11]
3. Ask what unit or category dictionary is needed to interpret a pixel value. Never infer elevation units from appearance.

### 4.3 Explain resolution, extent, and alignment

1. Introduce cell size and spatial resolution. Make clear that a grid's cell size is not a guarantee of positional accuracy. [S11]
2. Use a synthetic metre-based grid: ten columns of ten-metre cells span one hundred metres in that grid. Keep this arithmetic separate from real-world geodetic accuracy.
3. Preview grid alignment: two rasters with the same cell size may have different origins and coverage. Detailed processing settings belong to later training.

### 4.4 Treat NoData explicitly

1. Distinguish zero, NoData, masked areas, and transparent display. Require examples where zero is a legitimate measurement.
2. Ask how a statistic changes if missing values are incorrectly treated as zero. State the operation's chosen missing-data policy.
3. Explain that missing coverage and an observed value of “no reported incidents” are different statements.

### 4.5 Introduce resampling and its consequences

1. Explain why changing a raster grid requires choosing how output cell values are obtained. Introduce nearest neighbor, bilinear interpolation, and cubic convolution conceptually. [S12]
2. Use category codes to show why averaging can create meaningless categories. Explain why nearest neighbor is a typical categorical-data choice, not a universal best method.
3. Emphasize that a smaller output cell size does not recover previously unmeasured detail. Show a deliberately enlarged grid as an illustration, not improved source accuracy.

### 4.6 Connect elevation rasters to 3D

1. Introduce an elevation surface and explain a simple height-field representation. Distinguish terrain from a surface that includes above-ground objects, while requiring the dataset's own definition. [S11]
2. Explain why heights need units and a vertical reference; defer datum detail to Chapter 5.
3. Distinguish a shaded rendering from the underlying numeric elevations. A visually realistic surface is not automatically a validated terrain model.

### 4.7 Guided lab: inspect two rasters

1. Provide a small categorical raster and a small elevation raster with documented NoData. Ask trainees to identify dimensions, cell size, values, bands, extent, and missing-data treatment.
2. Change styling without changing values, then compare two resampling choices on copies. Record the numerical effect, not just appearance.
3. Deliverable: an inspection table and a short explanation of which resampling result is appropriate for each dataset. Instructor verifies against the supplied original cells.

### 4.8 Independent check and progression gate

1. Present an image with no georeferencing information and ask whether it can be placed reliably on a map without additional evidence.
2. Test zero versus NoData, resolution versus accuracy, and resampling categories versus a continuous surface.
3. Progress when the learner can explain what each displayed color means and what the raster cannot establish.

### 4.9 Media and source brief

1. Slides should move from numeric grid to styled raster to surface. Audio must describe cell arrangement and values rather than relying on colors.
2. Optional 3D scene: elevate grid vertices from a documented synthetic surface; show a labeled vertical-exaggeration control and a flat grid comparison.
3. Read [S11] for raster foundations and [S12] for resampling behavior. Chapter 5 supplies the coordinate reference needed to position these data.

## Chapter 5 — Coordinates and coordinate reference systems

**Entry requirement:** Chapters 3–4.  
**Outcomes:** Interpret coordinates with reference, units, and axis order; distinguish geographic, projected, and vertical references.  
**Boundary:** Explain the reference framework here; perform transformations and measurement choices in Chapter 6.

### 5.1 Start with the ambiguity of a coordinate pair

1. Show two numbers without labels and ask what information is missing. Establish that coordinates alone are insufficient to identify a place reliably.
2. Introduce axes, origin, direction, and units using a schematic Cartesian grid. Distinguish an artificial training grid from Earth-referenced data.
3. Require a coordinate description to include the CRS and the format/API's axis-order convention, not just “X/Y.”

### 5.2 Explain latitude and longitude

1. Introduce equator, prime meridian, latitude, longitude, hemispheres, and angular units with a globe diagram. [S13]
2. Show decimal degrees and degrees/minutes/seconds; include one checked conversion and explicit signs. Avoid excessive coordinate precision in the example.
3. Use north/south and east/west separately: latitude is positive north, longitude positive east in the usual signed convention. Have learners verify both independently.

### 5.3 Build the Earth-reference vocabulary

1. Introduce Earth shape, ellipsoid, and datum/reference frame in that order. Explain what each contributes without advanced geodesy. [S13] [S14]
2. Explain why a datum name is not itself a complete description of every possible projected CRS based on it.
3. Mention that high-accuracy/time-dependent reference-frame work needs additional expertise. Do not teach centimetre-level transformations from this introductory chapter.

### 5.4 Distinguish geographic and projected CRS

1. Compare angular coordinates with planar coordinates in linear units. Explain that a projected CRS incorporates a geographic reference and a projection. [S14]
2. Introduce EPSG:4326 and EPSG:3857 as commonly encountered identifiers, with full CRS names checked in the selected application's CRS catalogue. Introduce UTM as a family with zones and hemispheres, not one universal CRS.
3. Require trainees to inspect the full definition, units, and area of use rather than memorize numeric identifiers alone.

### 5.5 Resolve coordinate-order confusion

1. Teach that “latitude/longitude” in prose is not a universal serialization order. Coordinate order must follow the actual interface contract.
2. For RFC 7946 GeoJSON, explicitly teach longitude then latitude in decimal degrees; distinguish this from the formal axis order associated with EPSG:4326 and from other APIs. [S18] (sections 3.1.1 and 4)
3. Use a deliberately swapped synthetic pair whose numbers both fall within allowable latitude/longitude ranges. Show why range checks alone can miss the error.

### 5.6 Introduce vertical reference and dimensional limits

1. Explain height/depth units and vertical reference; distinguish ellipsoidal and gravity-related heights at an introductory level. [S14]
2. Ask why “height = 30” is ambiguous without units and reference. Distinguish height above ground from absolute elevation.
3. Make clear that a stored Z coordinate does not guarantee that a later spatial operation uses Z. Verify each operation's dimensional behavior when it is introduced.

### 5.7 Inspect data CRS, map CRS, and coordinate metadata

1. Demonstrate where the chosen application reports a dataset's reference and the map's display reference. State that these can differ. [S13]
2. Give a checklist: known/unknown CRS, units, coordinate order, extent plausibility, datum, and source metadata.
3. Treat unexpected magnitude or location as clues, not enough evidence to assign a CRS by guesswork. Unknown reference should trigger investigation.

### 5.8 Guided lab: coordinate detective

1. Supply four small documented cases: valid geographic coordinates, valid projected coordinates, swapped coordinate order, and missing CRS metadata with a separate provenance note.
2. Ask trainees to classify each, identify evidence, and explain which case cannot be resolved from numbers alone. No transformation is required yet.
3. Deliverable: a diagnosis table with coordinate order, CRS, units, plausible extent, and confidence/remaining questions. Keep the instructor's provenance note separate until review.

### 5.9 Independent check and progression gate

1. Ask learners to explain geographic versus projected CRS and why a metre unit does not by itself prove measurement suitability.
2. Require a correct GeoJSON point interpretation and a refusal to silently guess an unknown CRS.
3. Progress only when coordinate order, angular/linear units, and dataset/display reference are distinguished correctly.

### 5.10 Media and source brief

1. Use a labeled globe, graticule, flat coordinate grid, and coordinate metadata card. A 3D globe is useful for axes and angles, not for implying that flattening can avoid distortion.
2. Narration must state coordinate order aloud. Avoid using “first coordinate” without naming what it means in the current format.
3. Primary readings: [S13], [S14], and [S18]. Chapter 6 applies these references to actual transformations and measurements.

## Chapter 6 — Projections, transformations, and accurate measurement

**Entry requirement:** Chapter 5; this is a mandatory dependency.  
**Outcomes:** Distinguish CRS assignment from transformation; choose an appropriate measurement method; validate mixed-CRS workflows.  
**Boundary:** No custom projection implementation or survey-grade transformation claims.

### 6.1 Explain why projection choice matters

1. Introduce distortion of area, shape/angles, distance, and direction. Explain that a projection preserves selected properties under particular conditions, not every property everywhere. [S13]
2. Compare a city-scale measurement task with a world map. Ask which property and geographic extent matter in each.
3. Use original diagrams with explicit labels stating what is schematic. Avoid presenting a stylized animation as a quantitatively accurate distortion model.

### 6.2 Separate assignment from transformation

1. Explain that ArcGIS Define Projection changes coordinate-system information, not stored geometry coordinates. Use it only when the correct existing reference is established. [S15]
2. Explain that Project produces coordinates in the destination reference and may require an appropriate geographic transformation. [S16]
3. Demonstrate two copies of a dataset: properly transforming one versus incorrectly relabeling the other. Make the incorrect path a controlled diagnostic example, never a repair recommendation.

### 6.3 Explain datum transformations and evidence

1. Describe why changing between geographic references may require more than a projection conversion. [S16]
2. Instruct trainees to inspect the transformation's applicability, geographic area, required resources, and documented accuracy. Do not hard-code a universal transformation choice.
3. Require the later author to verify installed transformation resources and the selected software version before writing exact lab steps.

### 6.4 Distinguish display alignment from analytical preparation

1. Explain on-the-fly display reprojection and its purpose. A map displaying layers together does not prove the underlying stored coordinates have changed. [S13]
2. Separate input CRS, display CRS, processing/output CRS, transformation, and units in a worksheet.
3. Require every measurement lab to inspect the actual tool's rules and environment settings. Avoid claiming that all tools silently use the map CRS or all require physically reprojected inputs.

### 6.5 Compare planar and geodesic measurement

1. Introduce planar distance on a projected plane and geodesic distance on a reference Earth surface. Keep terminology tied to the chosen implementation. [S17]
2. Explain why raw degree differences are not metre distances, and why appropriate geodesic calculations can work with geographic coordinates.
3. Teach that Web Mercator coordinate units are metres but planar distances/areas are distorted. Avoid the opposite overstatement that any operation involving EPSG:3857 is inherently wrong; method and location matter.

### 6.6 Select a method from the requirement

1. Give a decision worksheet: study area, measured property, required accuracy, data quality, supported algorithm, and output units.
2. For a local projected workflow, require a suitable CRS and checked area of use; for a wide-area workflow, consider supported geodesic methods. Never prescribe one UTM zone for all of India.
3. Separate straight-line proximity from network travel distance and 2D ground distance from 3D slope distance. Mention that these answer different questions.

### 6.7 Plan a reproducible comparison experiment

1. Provide verified input points in a known CRS. Compare an appropriate projected measurement, a documented geodesic measurement, and a deliberately unsuitable planar method.
2. Require the later author to calculate actual results and record software, method, ellipsoid/reference, units, and rounding before inserting an answer table. No numerical comparison is claimed in this blueprint.
3. Explain differences in terms of method and assumptions. Do not call a result “ground truth” merely because another tool produced it.

### 6.8 Guided lab: combine two correctly referenced layers

1. Supply two small datasets covering the same study area in different known CRSs, plus provenance. Inspect both before transforming anything.
2. Produce an analysis-ready copy, select a documented measurement method, and calculate one distance and one area with units.
3. Deliverable: transformation/measurement log, output datasets, and evidence of plausibility. Preserve original data and require an independent spot-check.

### 6.9 Independent check and progression gate

1. Give an unknown-CRS case, a wrongly assigned-CRS case, and a known-CRS conversion case. Require different responses to each.
2. Ask why two correctly displayed layers do not automatically justify a 500-metre analysis and why latitude/longitude data can still support a correct geodesic calculation.
3. Do not pass a trainee who confuses assignment with transformation or degree units with metre units, even if the final map appears aligned.

### 6.10 Media and source brief

1. Build a side-by-side metadata-only change versus coordinate-change animation. Label before/after values and the operation being performed.
2. Optional interactive globe/map comparison: show distortion changes by location using a verified mathematical implementation. Specify the formula/library and validation fixtures in the eventual media brief.
3. Read [S13], [S15], [S16], and [S17]. Transition to Chapter 7 by recording these decisions as part of dataset provenance.

## Chapter 7 — GIS data formats, sources, and metadata

**Entry requirement:** Chapters 3–6.  
**Outcomes:** Choose a suitable exchange/storage format; inspect source suitability; detect loss during conversion.  
**Boundary:** No database administration or service deployment.

### 7.1 Separate model, encoding, container, and service

1. Revisit vector/raster as data models, then distinguish a file encoding, a container holding datasets, a spatial database, and a network service.
2. Explain that a filename extension does not establish accuracy, completeness, freshness, or suitability.
3. Ask learners what they expect to receive from a file download versus an access-controlled service URL. Reserve protocol details for a later phase.

### 7.2 Introduce simple exchange formats

1. Explain coordinate CSVs: explicit fields, delimiter/encoding, coordinate order, CRS supplied separately, and safe handling of leading-zero identifiers.
2. Introduce GeoJSON Geometry, Feature, and FeatureCollection. Cover RFC 7946's coordinate-reference convention; do not imply arbitrary projected coordinates are compliant merely because a `crs` property is added. [S18]
3. Ask trainees to inspect a tiny example manually before loading it. Defer programming a parser.

### 7.3 Explain Shapefile compatibility and limitations

1. Explain that a shapefile comprises companion files; identify `.shp`, `.shx`, `.dbf`, and the CRS role of `.prj`. [S20]
2. Teach practical attribute limitations: short field names, null handling, date/time limitations, and encoding interoperability. Show why a successful export can still lose meaning.
3. Position it as a format learners will encounter, not the default for every new solution. Ask them to justify an export based on the receiver's needs.

### 7.4 Introduce richer containers and raster exchange

1. Introduce GeoPackage as a SQLite-based geospatial container, with capabilities defined by the standard and supported implementation. Avoid promising that every GIS application supports every extension. [S19]
2. Introduce GeoTIFF as a common georeferenced raster exchange choice; require inspection of CRS, bands, cell size, units, and NoData instead of assuming every TIFF is georeferenced. [S11]
3. Explain that conversion across containers may preserve geometry while losing relationships, rules, or styling. The lab must verify what survives.

### 7.5 Introduce ArcGIS geodatabases

1. Explain a geodatabase as storage plus an information model supporting datasets and behavior beyond a simple geometry table. [S21]
2. Distinguish a feature class, nonspatial table, and a grouping of related datasets at an introductory level. Defer creation procedures to Phase 2.
3. Clarify that a PostgreSQL/PostGIS database is not automatically an Esri enterprise geodatabase, and that ArcGIS Data Store is a separate product concept. [S09] [S21] [S04]

### 7.6 Evaluate sources and metadata

1. Require a dataset intake form with publisher, download/service URL, retrieval date, edition/date of observation, geographic coverage, CRS, units, accuracy statement, license/usage conditions, and known omissions.
2. Separate measured, digitized, geocoded, derived, and simulated data. Require a processing history for derived outputs.
3. Teach source suitability through a mismatch scenario: an old city boundary used to summarize current requests. Do not assume “official” means current or fit for every purpose.

### 7.7 Design conversion checks

1. Before conversion, record schema, counts, geometry types, CRS, extent, nulls, Unicode text, IDs, and date/time examples.
2. After conversion, compare those properties and a few known records. Document intentional changes separately from accidental loss.
3. Preserve the original and give outputs meaningful names. Explain why a green “completed” message is not a semantic validation.

### 7.8 Guided lab: select and test an exchange format

1. Provide a dataset with a long field name, a null numeric value, a leading-zero code, a timestamp, and Gujarati/English text. All records are synthetic.
2. Ask learners to choose a recipient-appropriate format, perform a checked conversion, and document any loss or adaptation.
3. Deliverable: intake form, format decision, and before/after comparison. The instructor checks actual outputs rather than assuming a predetermined failure in every implementation.

### 7.9 Independent check and progression gate

1. Present a directory containing only a `.shp` file, an unlabeled coordinate CSV, and a complete documented container. Ask what evidence/files are missing in each case.
2. Require the learner to explain why file format, source authority, access permission, and data quality are different concerns.
3. Progress when the learner can select a defensible format and identify what must be checked after exchange.

### 7.10 Media and source brief

1. Use a format comparison table and an illustrated package inspection. Narrate the purpose of file components rather than reading every extension repeatedly.
2. A 3D scene is unnecessary; a before/after schema and record comparison is more useful.
3. Sources: [S18] for GeoJSON; [S19] for GeoPackage; [S20] for Shapefile; [S21] for geodatabases; [S11] for raster context. Chapter 8 now designs the information inside those containers.

## Chapter 8 — GIS data modeling and relationships

**Entry requirement:** Chapters 3 and 7; Chapters 5–6 provide the spatial-reference context.  
**Outcomes:** Design a small spatial schema, stable identifiers, valid attribute rules, and relationships.  
**Boundary:** No enterprise versioning, advanced attribute rules, or production migration implementation.

### 8.1 Start from entities, events, and questions

1. Identify assets, inspections, service requests, and wards as distinct concepts. Ask which need geometry and which can refer to another entity.
2. Define record granularity: one physical asset, one inspection visit, or one reported incident. Prevent accidental mixing of these grains.
3. Link every required field to a business question or workflow. Avoid collecting fields simply because they might become useful.

### 8.2 Define geometry and spatial reference deliberately

1. Specify geometry type, multipart policy, horizontal CRS, and any needed Z/unit/reference for each spatial entity.
2. Define what a point represents: centroid, entrance, equipment location, or reported incident position. Do not treat these as interchangeable.
3. Document geometry limitations and whether a location is measured or approximate. Reuse Chapters 3 and 5 rather than repeating them in full.

### 8.3 Design attribute types and meaning

1. Build a data dictionary with field name, description, type, units, null policy, example, and source. Use concise programmer-friendly comparisons. [S23]
2. Distinguish unknown, not applicable, zero, and empty text. Provide a synthetic asset example where these lead to different operational decisions.
3. Store business codes as text when arithmetic is inappropriate or leading zeros matter. Specify timestamps and time-zone conventions for the chosen storage system rather than assuming uniform behavior.

### 8.4 Design identifiers

1. Separate stable business identity from display labels and storage-generated row identifiers.
2. Introduce ArcGIS ObjectID and GlobalID as platform-specific concepts requiring verified workflow documentation before implementation. Do not promise ObjectIDs remain stable through copying/republication or that a UUID alone configures synchronization.
3. Require an explicit identity policy for imports, duplicates, and related records. Use a schema exercise here; detailed service behavior is deferred.

### 8.5 Model relationships and cardinality

1. Teach one-to-one, one-to-many, and many-to-many through original asset/inspection/team examples. Use primary/foreign-key reasoning already familiar to developers.
2. Model one asset with multiple inspection records. Avoid repeating asset geometry and mutable asset descriptions unnecessarily in every observation.
3. Distinguish conceptual relationships from joins and from an ArcGIS relationship class implementation. Explain that storage format and tooling determine which rules are enforced. [S21]

### 8.6 Introduce controlled values and validation rules

1. Explain coded-value and range domains using status categories and allowable measurements. Distinguish stored code from displayed description. [S22]
2. Discuss defaults versus valid values: a default is not proof an observation was made. Identify where null remains meaningful.
3. Have trainees define which rules belong in the schema, application, or review procedure. Do not imply every format enforces the same rules.

### 8.7 Plan history, attachments, and maintenance

1. Define how an asset's current state differs from inspection history. Include event time versus record-entry time as distinct concepts.
2. Specify how photographs/documents relate to the relevant record and how orphaned relationships would be detected. Avoid storing many filenames in one comma-separated field.
3. Introduce ownership of schema changes and a migration impact checklist. Implementation and access management belong to later phases.

### 8.8 Guided lab: design an inspection schema

1. Supply a deliberately poor single-table design with repeated inspection fields, mixed units, unstable names used as IDs, and ambiguous null values.
2. Ask trainees to produce a corrected entity diagram, data dictionary, relationship definitions, and ten synthetic example records across the tables.
3. Deliverable: design plus three answerable business questions. Check that repeated inspections are retained without overwriting history and that related IDs resolve correctly.

### 8.9 Independent check and progression gate

1. Present a new asset type and a requirement to store multiple visits and photos. Ask the trainee to extend the model without destructive duplication.
2. Test the difference between a display label, stable ID, controlled category, default, and missing observation.
3. Progress when the schema can represent the scenario coherently and the trainee can explain which integrity rules still need enforcement.

### 8.10 Media and source brief

1. Use an entity-relationship diagram, field-definition cards, and a before/after record example. Audio should tell the lifecycle of one asset through multiple inspections.
2. A 3D scene adds little here; use a relationship animation only if it makes record ownership clearer.
3. Read [S23] for attributes, [S21] for the geodatabase information model, and [S22] for domains. The proposed schema and identity-policy exercise are instructional designs, not a mandated Esri schema.

## Chapter 9 — Data capture, editing, and quality

**Entry requirement:** Chapters 5–8.  
**Outcomes:** Choose a capture method, diagnose quality problems, apply appropriate topology rules, and document corrections.  
**Boundary:** Basic capture/quality concepts; not survey training or a full field-app configuration course.

### 9.1 Define quality for the intended use

1. Establish positional accuracy, attribute correctness, completeness, logical consistency, and currency as separate review questions.
2. Ask whether the source is adequate for the stated decision. Use contrasting map-overview and asset-maintenance scenarios without inventing numerical accuracy thresholds.
3. Require an intake checklist before editing: source, date, reference, schema, original copy, and intended output.

### 9.2 Compare capture methods

1. Introduce manual digitizing, coordinate import, GNSS observation, address geocoding, and georeferencing of scanned material as different workflows. [S24] [S26]
2. Distinguish GPS as one satellite-navigation system within GNSS. Do not assign a fixed accuracy to all phones or receivers; actual performance requires device/environment evidence.
3. Ask what uncertainty and provenance should accompany each method. An address match, roof location, and surveyed entrance point are not equivalent observations.

### 9.3 Explain georeferencing without confusing it with projection

1. Explain linking image locations to known reference locations through control points and a transformation model. [S26]
2. Discuss control-point placement and independent checking. A small fitting residual alone does not establish real-world accuracy across the image.
3. Contrast georeferencing an unlocated scan with reprojecting correctly referenced GIS data. Defer specialized image rectification methods.

### 9.4 Introduce careful editing and snapping

1. Explain creating, moving, splitting, and reshaping features on a copy. Make learners predict attribute and relationship implications before editing.
2. Introduce vertex/edge snapping and tolerance through a deliberate small gap. Excessive snapping can move valid observations or connect unrelated objects. [S25]
3. Require visual and attribute review after an edit, not just saving successfully.

### 9.5 Separate geometry validity from topology rules

1. Contrast an invalid self-intersecting polygon with two individually valid polygons that violate a business rule by overlapping.
2. Teach domain-specific rules: nonoverlapping wards, connected pipe segments where connectivity is intended, and assets that should lie within a service area. [S25]
3. Explain exceptions: a road cul-de-sac can be valid; two service territories may intentionally overlap; a bridge crossing need not be a connected road junction.

### 9.6 Diagnose attribute and relationship problems

1. Check duplicate business IDs, invalid categories, missing required observations, unit inconsistencies, and orphaned related records.
2. Distinguish two reports at one location from an accidental duplicate of one report. Proximity alone is not sufficient evidence for deduplication.
3. Require a defect log: symptom, affected records, likely cause, evidence, proposed correction, and reviewer decision.

### 9.7 Apply corrections with traceability

1. Preserve the original and record before/after values or geometry evidence. Prefer identifying the source cause over repeatedly correcting downstream exports.
2. Explain that automated geometry repair can alter a shape; inspection is still necessary. The expanded lab must document the chosen tool's actual behavior.
3. Retain unresolved issues explicitly. Do not fabricate missing observations to make a dataset pass validation.

### 9.8 Guided lab: inspect a deliberately damaged dataset

1. Prepare a separate damaged copy of the training data with a documented duplicate ID, invalid category, orphaned inspection, self-intersecting polygon, and unintended boundary gap.
2. Have trainees classify defects, choose which can be safely corrected, and explain which need source-owner clarification. Include one intentional overlap or legitimate dead end as a false alarm.
3. Deliverable: defect log, corrected copy, and unresolved-issues list. The instructor checks against a private defect manifest and verifies that valid features were not “repaired” unnecessarily.

### 9.9 Independent check and progression gate

1. Give a new set of mixed geometry, topology, and business-rule issues. Require evidence-based decisions rather than running every repair tool.
2. Ask learners to distinguish georeferencing, geocoding, CRS assignment, and reprojection.
3. Progress when the trainee can defend each correction and preserve uncertainty where evidence is missing.

### 9.10 Media and source brief

1. Create before/after diagrams for snapping, invalid geometry, and topology violations. Show the difference between a defect and a valid exception.
2. Optional 3D scene: a bridge over a road with a top-down view beside it, demonstrating that apparent 2D crossing does not establish network connectivity.
3. Read [S24] for capture, [S25] for topology/snapping, and [S26] for georeferencing. Chapter 10 queries the now-understood and checked data.

## Chapter 10 — Attribute queries and spatial relationships

**Entry requirement:** Chapters 8–9 and coordinate awareness from Chapters 5–6.  
**Outcomes:** Construct attribute/spatial queries, predict boundary behavior, and avoid join-driven miscounts.  
**Boundary:** No spatial-index internals or advanced SQL optimization.

### 10.1 Distinguish filter, selection, join, and transformation

1. Explain filtering a view, selecting records, producing an exported subset, and modifying source values as different actions.
2. Ask trainees to predict record counts and source changes before executing a query.
3. Keep a visible “input → condition → expected IDs” worksheet throughout the chapter.

### 10.2 Build attribute conditions

1. Introduce equality, ranges, AND/OR/NOT, parentheses, text matching, and null checks using the training schema. [S27]
2. Distinguish `IS NULL` from ordinary equality and unknown from zero. Use a small truth-table-style example without requiring a full database theory lesson.
3. Verify quoting, date literals, and supported SQL syntax for the actual data source. Do not present an expression as universally portable across ArcGIS, QGIS, and PostgreSQL.

### 10.3 Join records by identity

1. Explain key-based joins using ward codes or asset IDs; show unmatched keys and duplicate keys.
2. Compare one asset with several inspections and a flat joined result. Explain how joined row counts can exceed asset counts.
3. Require a decision about the unit being counted: requests, assets, inspections, or matched pairs. Do not blindly sum duplicated rows.

### 10.4 Introduce spatial predicates visually

1. Teach intersects, disjoint, contains/within, touches, overlaps, and nearest using small diagrams before naming tools.
2. Distinguish the predicate “intersects?” from the operation that constructs an intersection geometry. The latter belongs in Chapter 11.
3. Explain that bounding-box overlap can identify candidates but does not generally prove exact geometric intersection. Use a concave shape to illustrate.

### 10.5 Teach boundary semantics explicitly

1. Use a point strictly inside a polygon, on its boundary, in a hole, and outside. Require predicted outcomes before software execution.
2. For PostGIS geometry predicates, explain the documented difference between `ST_Contains` and `ST_Covers`, including the point-on-boundary case. This is a named implementation example, not a universal claim about every tool labeled “contains.” [S28] [S29]
3. Explain that `ST_Intersects` tests shared points and that boundary contact can qualify. Verify the chosen ArcGIS/QGIS tool's documented options separately. [S30]

### 10.6 Handle distance and dimension carefully

1. Distinguish nearest from “within a distance.” Define search limit, ties, units, and whether boundaries are inclusive.
2. Use Chapter 6 to choose planar/geodesic distance; do not implement a metre threshold directly on degree coordinates without an appropriate method.
3. Ask whether the operation uses 2D or 3D geometry. Return to the bridge example when showing why Z storage does not establish 3D query behavior.

### 10.7 Combine spatial and attribute logic

1. Formulate “open requests inside the study area and within the specified distance of a road” as separate, testable conditions.
2. For assignment to wards, define boundary and overlap policies, unmatched handling, and whether multiple matches are allowed.
3. Explain how a spatial join can enrich or aggregate records, with target/join roles and one-to-one versus one-to-many outcomes verified against the tool. [S31]

### 10.8 Guided lab: query predictable geometry

1. Use Appendix A's small planar fixture. Predict IDs for strict interior membership, boundary-inclusive membership, and the road-distance threshold.
2. Execute the chosen documented equivalents and compare results. Explain any mismatch in predicate semantics, CRS, tolerance, or fixture construction.
3. Deliverable: prediction/result table and a written shared-boundary policy. A policy-driven single assignment is different from the raw geometric match result.

### 10.9 Independent check and progression gate

1. Add a point in a polygon hole, a duplicate join key, and an equal-distance tie in a separate exercise. Require explicit handling for all three.
2. Test whether learners can distinguish a correct geometry result from an unsuitable business-assignment rule.
3. Progress when the trainee can explain selected IDs and counts, including zero, multiple-match, and boundary cases.

### 10.10 Media and source brief

1. Create an interactive 2D polygon with a movable point and a table of documented predicate results. Name the engine or define the exact conceptual rules used.
2. Audio should narrate each point's relation to the interior, edge, hole, or exterior. No 3D scene is needed for the principal lesson.
3. Read [S27] for source-dependent SQL, [S28]–[S30] for explicit predicate behavior, and [S31] for ArcGIS spatial joins. Chapter 11 builds derived outputs from these relationships.

## Chapter 11 — Spatial analysis fundamentals

**Entry requirement:** Chapters 6 and 9–10.  
**Outcomes:** Select basic analysis operations, sequence them, validate outputs, and state limitations.  
**Boundary:** No routing solver, suitability model, hotspot statistics, machine learning, or flood simulation.

### 11.1 Specify the analysis before choosing tools

1. Write the decision, study area, time window, eligible records, units, desired output, and acceptance checks.
2. Ask whether the answer requires a selection, a new geometry, transferred attributes, or a summary. These lead to different operations.
3. Require an analysis worksheet listing each intermediate output and why it exists. The author should show one unnecessary operation being removed.

### 11.2 Create proximity areas with buffers

1. Explain buffer distance, input geometry, method, and separate versus dissolved output. [S17]
2. Use a road-proximity example and explicitly state that the buffer does not represent driving accessibility or service response time.
3. Require learners to predict effects of a changed threshold and overlapping buffers. Reuse Chapter 6 rather than assuming default measurement settings.

### 11.3 Extract a study area with clip

1. Explain clipping input features to the chosen study boundary and inspect retained geometry/attributes. [S32]
2. Show a road crossing the boundary; distinguish selecting the full road from creating only its portion inside the area.
3. Ask whether stored length/area attributes need recalculation after geometry changes. Do not assume a copied attribute automatically describes the new shape.

### 11.4 Construct overlap with intersect

1. Explain geometric overlay that produces common portions and associated attributes. Contrast this with the Boolean predicate in Chapter 10. [S33]
2. Predict output dimension and record splitting for a simple line/polygon and polygon/polygon example.
3. Include a warning about summing attributes copied onto split records. Require a defensible allocation rule rather than inventing new totals.

### 11.5 Aggregate geometry with dissolve

1. Explain grouping by selected attributes and combining geometry; distinguish dissolve from simply appending datasets. [S34]
2. Specify desired summary statistics and inspect multipart outcomes. Do not infer every output group must be one contiguous area.
3. Use a synthetic ward-to-zone example and check that the grouping criterion matches the business requirement.

### 11.6 Transfer and summarize with spatial joins

1. Assign request counts to wards using the boundary policy established in Chapter 10. [S31]
2. Inspect unmatched records, zero-count areas, and multiple matches. Preserve zero-count areas when they belong in the report.
3. Reconcile source request IDs with assigned, unassigned, and multiply assigned outcomes. A sum larger than the number of requests must be explained.

### 11.7 Introduce a minimal raster-analysis example

1. Use Appendix A's grid to compute a statistic with NoData excluded, then show the effect of replacing it with zero. State the policy explicitly.
2. Explain a conceptual threshold mask using cell values and units. Do not describe a simple elevation threshold as a flood-risk model.
3. Require alignment, cell-size, extent, and missing-data checks before combining raster inputs. Detailed raster processing remains later material.

### 11.8 Validate and document the workflow

1. Check counts, selected IDs, geometry type, extent, CRS, units, and geometry validity. Use expected invariants and manual spot-checks.
2. Change one input or threshold and ask what should change. This is a sensitivity exercise, not a guarantee of correctness.
3. Record tool/version, parameters, processing reference, source edition, transformation, output paths, and known limitations. Preserve the inputs needed to rerun the work.

### 11.9 Guided lab: produce a service-request analysis

1. First use the deterministic fixture, then a separate Earth-referenced training dataset whose CRS and provenance have been checked. Do not attach an invented EPSG code to the schematic fixture.
2. Filter eligible requests, identify proximity to roads, summarize by ward, and report unmatched/boundary cases. Select the threshold and method explicitly.
3. Deliverable: output layers/table, analysis worksheet, validation evidence, and a short interpretation limited to what the input supports.

### 11.10 Independent check and progression gate

1. Supply a new requirement that can be answered by a subset of buffer, clip, intersect, dissolve, and spatial join. Require justification for the selected sequence.
2. Include a misleading alternative such as treating proximity as travel time or raw counts as risk. Ask the trainee to explain why it is unsupported.
3. Progress when another trainee can reproduce the result and the author can explain discrepancies rather than merely show matching screenshots.

### 11.11 Media and source brief

1. Animate small before/after geometries for each operation, with input IDs and output attributes visible. Use a reproducible original fixture.
2. Narration should state the question, operation, expected change, and validation step. A 2D operation animation is generally more useful than 3D here.
3. Operation sources: buffer [S17], clip [S32], intersect [S33], dissolve [S34], spatial join [S31]. Chapter 12 communicates the checked result.

## Chapter 12 — Cartography and interpreting maps correctly

**Entry requirement:** Chapters 3–11.  
**Outcomes:** Choose suitable map encodings, communicate scale and uncertainty, and avoid misleading comparisons.  
**Boundary:** Introductory cartographic judgment; not professional graphic-design certification or advanced thematic mapping.

### 12.1 Start with audience and map purpose

1. Compare an operations map for locating requests with a management map for comparing wards. State the decision each reader must make.
2. Define medium, viewing size, interaction, and required detail. A phone map and a printed page need different presentation decisions.
3. Ask what information can be removed without weakening the decision. Avoid filling the map with every available layer.

### 12.2 Explain scale, detail, resolution, and accuracy

1. Teach representative fraction and large-scale versus small-scale terminology using checked ratios: 1:1,000 shows more local detail than 1:100,000 for a comparable physical map area. [S35]
2. Distinguish map scale, raster cell size, coordinate precision, and positional accuracy using a recap table.
3. Explain that zooming in or adding decimal places does not improve original evidence. Generalization and visibility choices must suit the intended use.

### 12.3 Match symbols to the data

1. Distinguish categories from ordered magnitudes. Choose unique categories, graduated colors, or size variation according to the meaning of the variable. [S23]
2. Explain visual hierarchy: foreground operational data, supporting context, and subdued basemap. Show how oversized symbols can obscure exact locations.
3. Require readable labels, distinguishable symbols, and more than color alone for essential distinctions. Evaluate a grayscale view as a simple supplementary check.

### 12.4 Teach classification as an analytical choice

1. Introduce equal interval, quantile, and natural breaks using the same small numeric table. Explain their different grouping objectives. [S36]
2. Show how changed breaks alter apparent patterns without changing source values. Require the final map to disclose intervals and units.
3. For comparisons over time or between areas, discuss the consequences of recalculating classes independently. Do not declare one method universally best.

### 12.5 Distinguish totals, rates, and density

1. Explain a choropleth and why a suitable denominator often matters when comparing areas. Distinguish workload totals from rates or densities. [S37]
2. Use original arithmetic: Ward A has 100 requests and 10,000 residents; Ward B has 60 requests and 3,000 residents. A has more requests, while B has more requests per 1,000 residents: 20 versus 10. Label all values synthetic.
3. Ask whether population is the right denominator for the actual question; asset counts, road length, or reporting exposure may be more relevant. Do not imply the resulting rate proves underlying risk.

### 12.6 Provide map context and honest uncertainty

1. Include a meaningful title, legend, relevant units, source/date, and essential method notes. Use a scale bar or orientation aid when it helps the reader; do not add decorations mechanically. [S35]
2. Distinguish zero values from missing data visually and in the legend. Preserve unresolved or unassigned records in the accompanying explanation.
3. State major limitations, aggregation choices, and boundary policy. Explain that an area summary does not describe every individual location inside that area.

### 12.7 Distinguish presentation controls from data protection

1. Explain that hiding a field in a pop-up or turning a layer off is a presentation choice, not proof of access restriction.
2. Do not teach full security configuration here. Ask learners to identify when a requirement belongs to the later permissions/administration chapters.
3. Review the output for unnecessary personal details using the synthetic training scenario; do not introduce real resident information for a map-design exercise.

### 12.8 Guided lab: redesign a misleading map

1. Supply an original deliberately poor map with unclear units, ambiguous colors, missing-data ambiguity, clutter, and an inappropriate comparison variable.
2. Ask trainees to produce an operational map and a management map from the same checked dataset, documenting their different design choices.
3. Deliverable: maps, source/value table, and a short rationale. Instructor verifies that styling changes do not conceal discrepancies in the underlying analysis.

### 12.9 Independent check and Phase 1 exit assessment

1. Ask trainees to critique an unfamiliar map: what is being measured, which comparisons are supported, what is missing, and which conclusions overreach.
2. Complete the integrated Phase 1 assessment in Appendix B using all prior chapters. Require a short oral explanation as well as files.
3. Passing Phase 1 establishes readiness for supervised platform training. It does not establish readiness to administer production ArcGIS Enterprise.

### 12.10 Media and source brief

1. Create side-by-side maps with identical data and changed classification, symbol choice, or denominator. Keep a visible source-value table to expose the difference.
2. For an optional 3D lesson, compare extruded thematic columns with a 2D map and discuss occlusion/perspective. Label thematic height as a data encoding, not physical building height.
3. Sources: map elements and scale [S35], symbols/attributes [S23], classification [S36], and normalization [S37]. Transition to Phase 2: implementing these judgments systematically in ArcGIS Pro.

## Appendix A — Shared training scenario and reproducible fixtures

### A.1 Scenario and data package to prepare later

1. Use a fictional municipality maintaining assets and responding to service requests. Keep terminology consistent: `asset`, `inspection`, `request`, `road`, and `ward`.
2. Prepare layers/tables for assets, roads, ward boundaries, requests, inspections, and a small raster. Create a schema dictionary and provenance/readme for the package.
3. Maintain separate clean, deliberately damaged, and instructor-answer copies. Include a private defect manifest describing exactly what was altered and why.
4. Use two categories of exercises: schematic planar fixtures for deterministic reasoning, and separately documented Earth-referenced examples for CRS/transformation work. Never silently mix them.
5. All fixture values below are original synthetic teaching material. No real municipal data, legal boundaries, travel times, accuracy claims, or operational risk conclusions are represented.
6. This blueprint specifies fixtures; it does not include generated GIS dataset files or claim that a lab has been run in ArcGIS/QGIS. The later chapter author must build and verify those files before issuing the lab.

### A.2 Planar vector fixture with exact conceptual answers

1. Use a two-dimensional local Cartesian plane with metre units. It has **no asserted Earth location and no EPSG code**. Use a documented engineering/local coordinate setup or a pure planar geometry environment; do not publish it as geographic GeoJSON.
2. Define Ward A as the square with corners `(0,0), (1000,0), (1000,1000), (0,1000)`, closing the ring. Define Ward B as `(1000,0), (2000,0), (2000,1000), (1000,1000)`, closing the ring.
3. Define Road R1 as the finite line segment from `(0,500)` to `(2000,500)`. It is not an infinite line.
4. Use these request points. All six are eligible for the basic spatial exercise; additional status/date filters must be introduced explicitly in later fixture variants.

| Request | Coordinate (x, y) | Strict interior location | Distance to finite Road R1 |
| --- | --- | --- | --- |
| P1 | (200, 200) | Ward A | 300 m |
| P2 | (800, 800) | Ward A | 300 m |
| P3 | (1200, 250) | Ward B | 250 m |
| P4 | (1700, 900) | Ward B | 400 m |
| P5 | (1000, 500) | Shared A/B boundary | 0 m |
| P6 | (2200, 500) | Outside both wards | 200 m to the road endpoint |

5. With a boundary-inclusive distance threshold of **300 metres**, qualifying IDs are P1, P2, P3, P5, and P6. If the requirement also demands membership in the union of the wards, P6 is excluded. Do not silently add that extra condition.
6. With strict-interior point membership, A contains P1/P2 and B contains P3/P4. P5 is on the shared boundary; P6 is outside.
7. With boundary-inclusive membership, A covers P1/P2/P5 and B covers P3/P4/P5. There are six ward–request matches but only five distinct matched requests. A unique-assignment report needs an explicit policy for P5.
8. Each square has planar area 1,000,000 square metres, or 1 square kilometre. These are idealized grid measurements, not Earth-surface areas.
9. The geometry answers above are hand-checkable. When implementing a lab, record the engine, predicate, tolerance, and coordinate setup and confirm it reproduces the intended mathematical conditions.

### A.3 Raster arithmetic fixture

1. Define this three-by-three synthetic value grid; NoData means missing, and zero is a legitimate observation:

| Row | Column 1 | Column 2 | Column 3 |
| --- | --- | --- | --- |
| 1 | 0 | 10 | 20 |
| 2 | 10 | NoData | 30 |
| 3 | 20 | 30 | 40 |

2. Excluding NoData gives eight valid cells, sum 160, and mean 20. Incorrectly replacing NoData with zero gives nine cells and mean 160/9, approximately 17.78.
3. A later author may give cells a local grid spacing for visualization, but must state the spacing, units, origin, and row direction. Do not infer a geographic position from the table.
4. This exercise teaches missing-data policy. It does not establish the behavior of every raster statistics function; inspect the chosen function's settings.

## Appendix B — Assessment design and progression policy

### B.1 Per-chapter assessment package

1. Recommended package: six concept questions, two scenario questions, one practical task, and one brief oral explanation. These counts are a proposed teaching policy, not an external standard.
2. Map each question to a module identifier and outcome. Multiple-choice items should have one defensible best answer under explicitly stated conditions.
3. Explain why incorrect answers fail. Include common misconceptions as plausible distractors without making questions ambiguous.
4. Score practical work for reasoning, correctness, verification, and documentation. A screenshot alone is insufficient proof of correct source data or processing.
5. Use fresh geometry/values for retesting so trainees cannot pass by recalling fixture answers. Keep difficulty and required concepts comparable.

### B.2 Integrated Phase 1 practical

1. Give trainees a new small, documented Earth-referenced dataset and a business question. Include a few known defects and enough source information to diagnose them.
2. Require an intake review, geometry/schema explanation, CRS/unit justification, quality log, query, basic analysis, and final map/table.
3. Require explicit handling of boundary cases, unmatched records, duplicated joins, and missing values wherever relevant to the fixture.
4. Ask the trainee to rerun one result after a changed threshold and explain what changed and why.
5. Ask how the same concepts would carry to another GIS platform and which implementation details would need rechecking.
6. Submit data outputs, method log, validation evidence, map, and limitations. Keep an instructor answer set derived from the exact released input package.

### B.3 Suggested scoring and critical misconceptions

| Area | Suggested weight | Evidence |
| --- | --- | --- |
| Problem interpretation and data/model understanding | 20% | Clear question, suitable inputs and entity/field meaning. |
| CRS, units, and measurement reasoning | 25% | Correct reference interpretation and justified method. |
| Query/analysis correctness and quality handling | 30% | Correct outcomes, edge cases, and defect decisions. |
| Reproducibility and validation | 15% | Another person can follow and check the work. |
| Communication and map interpretation | 10% | Readable output and defensible conclusions. |

1. A suggested pass threshold is 80%, with remediation required for critical misconceptions regardless of aggregate score. This is an internal recommendation, not a certification benchmark.
2. Critical misconceptions include treating CRS assignment as reprojection; treating degrees as metres; silently guessing coordinate order/CRS; concealing duplicate or unmatched counts; and claiming more accuracy than the evidence supports.
3. Require correction and a new exercise before progressing after a critical misconception. Course attendance and completed videos are not substitutes for demonstrated understanding.

## Appendix C — Instructions for generating later chapter documents and media

### C.1 Reusable chapter-generation instruction

> Create the learner document for Chapter [NUMBER] using this blueprint. Preserve every numbered module in order and implement the ordered authoring points within each module. Assume the learner is an experienced programmer with no GIS knowledge beyond the listed prerequisites. Explain concepts before procedures, include original examples and diagrams, and maintain the shared training terminology. Read the chapter's official references and cite the precise supporting sections. Verify any current product behavior against the selected installed version. Clearly distinguish general principles, format rules, platform behavior, synthetic examples, and unverified items. Create a guided lab with reproducible inputs and checked expected results, plus an independent assessment and separate instructor answer guide. Do not invent data, executed results, entitlements, accuracy claims, or references. Include slide, narration, and justified interactive/3D media briefs. Keep later-phase subjects within the scope boundaries stated for this chapter.

### C.2 Chapter document production checklist

1. Declare chapter ID, title, prerequisites, outcomes, document revision, actual software versions used in labs, and reference-check date.
2. Preserve all master module IDs. Add deeper IDs where needed rather than replacing the sequence with a generic article structure.
3. Build/check the exercise inputs and instructor answers before describing a lab as executable. If this is not possible, label the procedure as unverified and identify the missing environment.
4. Verify calculations, unit conversions, coordinate order, links, diagrams, and any quoted tool behavior. Check an original source rather than citing an AI-generated summary.
5. Review each exercise for prerequisites that have not yet been taught. Provide a short orientation or move the exercise rather than silently assuming later knowledge.
6. Maintain an instructor change log when a software change affects a lab. Recheck dependent tests and media after revising the source chapter.

### C.3 Presentation, audio, and 3D adaptation

1. Presentations: map each slide group to a module ID; use one learning objective per visual sequence; include speaker notes and a short question before showing the answer.
2. Audio: explain diagrams verbally, pronounce acronyms consistently, introduce units explicitly, and provide pauses for prediction. Keep detailed click instructions in the lab handout.
3. Tests: derive questions from stated outcomes, not incidental wording. Retain instructor rationale and expected output separately from the learner version.
4. Interactive/3D material: specify the concept, scene objects, coordinates/units, user controls, expected behavior, labels, and validation cases. Use verified calculations for mathematical demonstrations.
5. Label vertical exaggeration, thematic extrusion, illustrative geometry, and simulated data visibly. Do not allow visual realism to imply measurement accuracy.
6. Provide a 2D/static and text alternative for every essential 3D concept. Reusable onboarding should not depend on a particular graphics device.

## Appendix D — Authoritative reference register

All links below are official publisher/project sources or the RFC Editor. They were retrieved during preparation on 18 September 2026. Retrieval confirms access to reference material, not that the proposed hands-on labs were executed. Source IDs link directly to the relevant page. Read the named sections before expanding the associated material; recheck mutable product documentation when authoring a lab.

| ID | Publisher and page | Use and reading focus |
| --- | --- | --- |
| S01 | [QGIS — Introducing GIS][S01] | Introductory GIS, data, layers, and application roles. Use for concepts, not historical assertions or current product comparisons. |
| S02 | [Esri — Introducing ArcGIS Pro][S02] | Desktop application role, projects, maps, views, and basic interface orientation. |
| S03 | [Esri — Introduction to ArcGIS Online][S03] | Hosted mapping, content, sharing, applications, and organizational administration. |
| S04 | [Esri — Introduction to ArcGIS Enterprise][S04] | Enterprise purpose, deployment responsibility, and component roles; details vary by release/model. |
| S05 | [Esri — Developer documentation][S05] | SDK/API/automation roles; follow the relevant product guide before writing code or capability claims. |
| S06 | [OpenLayers — Project overview][S06] | Open-source browser mapping-library role and supported data/display categories. |
| S07 | [Mapbox — Product overview][S07] | Mapping, search, navigation, and data product categories; do not infer pricing or license rights. |
| S08 | [GeoServer — About][S08] | Server role and geospatial interoperability context. |
| S09 | [PostGIS — Project overview][S09] | Spatial database capability associated with PostgreSQL. |
| S10 | [QGIS — Vector Data][S10] | Point/line/polygon representation, vertices, scale-dependent modeling, and attributes. |
| S11 | [QGIS — Raster Data][S11] | Grid model, imagery, bands, georeferencing context, and resolution. |
| S12 | [Esri — Resample][S12] | Resampling-method descriptions and categorical/continuous data considerations. |
| S13 | [QGIS — Coordinate Reference Systems][S13] | Geographic/projected systems, distortion, UTM, and on-the-fly display projection. |
| S14 | [Esri — Coordinate systems, map projections, and transformations][S14] | Horizontal/vertical references, units, and transformation vocabulary. |
| S15 | [Esri — Define Projection][S15] | Metadata assignment versus coordinate modification; read usage and restrictions. |
| S16 | [Esri — Project][S16] | Output coordinates, geographic transformations, and tool-specific environment/usage notes. |
| S17 | [Esri — Buffer][S17] | Planar/geodesic buffering, units, dissolve behavior, and parameter-dependent results. |
| S18 | [RFC Editor — RFC 7946: The GeoJSON Format][S18] | Sections 3.1.1, 3.1.6, 3.2, 3.3, and 4: positions, polygon rings, features, collections, and CRS convention. |
| S19 | [OGC GeoPackage — Official standard site][S19] | Container scope, standard documents, and extension/implementation considerations. |
| S20 | [Esri — Geoprocessing considerations for shapefile output][S20] | Companion files and geometry/attribute conversion limitations. |
| S21 | [Esri — Introduction to the geodatabase][S21] | Storage and information-model distinctions. |
| S22 | [Esri — Introduction to attribute domains][S22] | Coded-value/range domains and integrity concepts. |
| S23 | [QGIS — Vector Attribute Data][S23] | Field/record meaning and attribute-driven symbology. |
| S24 | [QGIS — Data Capture][S24] | Planning capture, creating/editing vector data, and digitizing context. |
| S25 | [QGIS — Topology][S25] | Topology errors/rules, snapping, and tolerance concepts. |
| S26 | [Esri — Overview of georeferencing][S26] | Control points, transformation context, residuals, and image referencing. |
| S27 | [Esri — SQL reference for query expressions used in ArcGIS][S27] | Operators, null handling, and source-dependent SQL behavior. |
| S28 | [PostGIS — ST_Contains][S28] | Interior/boundary distinction for the documented geometry predicate. |
| S29 | [PostGIS — ST_Covers][S29] | Boundary-inclusive coverage behavior; not a claim of universal function availability. |
| S30 | [PostGIS — ST_Intersects][S30] | Shared-point predicate and implementation-specific notes. |
| S31 | [Esri — Spatial Join][S31] | Target/join roles, match options, aggregation, and one-to-many output. |
| S32 | [Esri — Clip][S32] | Extracted geometry, retained attributes, and output behavior. |
| S33 | [Esri — Intersect][S33] | Overlay geometry, output dimensions, and split attributes. |
| S34 | [Esri — Dissolve][S34] | Grouping, summary statistics, and multipart output. |
| S35 | [QGIS — Map Production][S35] | Map elements, scale, legends, and contextual information. |
| S36 | [Esri — Data classification methods][S36] | Equal interval, quantile, natural breaks, and other classification approaches. |
| S37 | [Esri — Graduated colors][S37] | Quantitative polygon styling and normalization. |

[S01]: https://docs.qgis.org/3.44/en/docs/gentle_gis_introduction/introducing_gis.html
[S02]: https://pro.arcgis.com/en/pro-app/latest/get-started/get-started.htm
[S03]: https://doc.arcgis.com/en/arcgis-online/get-started/what-is-agol.htm
[S04]: https://doc.esri.com/en/arcgis-enterprise/latest/introduction/what-is-arcgis-enterprise-.html
[S05]: https://developers.arcgis.com/documentation/
[S06]: https://openlayers.org/
[S07]: https://www.mapbox.com/
[S08]: https://geoserver.org/about/
[S09]: https://postgis.net/
[S10]: https://docs.qgis.org/3.44/en/docs/gentle_gis_introduction/vector_data.html
[S11]: https://docs.qgis.org/3.44/en/docs/gentle_gis_introduction/raster_data.html
[S12]: https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/resample.html
[S13]: https://docs.qgis.org/3.44/en/docs/gentle_gis_introduction/coordinate_reference_systems.html
[S14]: https://doc.esri.com/en/arcgis-pro/latest/help/mapping/properties/coordinate-systems-and-projections.html
[S15]: https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/define-projection.html
[S16]: https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/project.html
[S17]: https://doc.esri.com/en/arcgis-pro/latest/tool-reference/analysis/buffer.html
[S18]: https://www.rfc-editor.org/rfc/rfc7946
[S19]: https://www.geopackage.org/
[S20]: https://doc.esri.com/en/arcgis-pro/latest/tool-reference/appendices/geoprocessing-considerations-for-shapefile-output.html
[S21]: https://doc.esri.com/en/arcgis-pro/latest/help/data/geodatabases/overview/what-is-a-geodatabase-.html
[S22]: https://doc.esri.com/en/arcgis-pro/latest/help/data/geodatabases/overview/an-overview-of-attribute-domains.html
[S23]: https://docs.qgis.org/3.44/en/docs/gentle_gis_introduction/vector_attribute_data.html
[S24]: https://docs.qgis.org/3.44/en/docs/gentle_gis_introduction/data_capture.html
[S25]: https://docs.qgis.org/3.44/en/docs/gentle_gis_introduction/topology.html
[S26]: https://doc.esri.com/en/arcgis-pro/latest/help/data/imagery/overview-of-georeferencing.html
[S27]: https://doc.esri.com/en/arcgis-pro/latest/help/mapping/navigation/sql-reference-for-elements-used-in-query-expressions.html
[S28]: https://postgis.net/docs/ST_Contains.html
[S29]: https://postgis.net/docs/ST_Covers.html
[S30]: https://postgis.net/docs/ST_Intersects.html
[S31]: https://doc.esri.com/en/arcgis-pro/latest/tool-reference/analysis/spatial-join.html
[S32]: https://doc.esri.com/en/arcgis-pro/latest/tool-reference/analysis/clip.html
[S33]: https://doc.esri.com/en/arcgis-pro/latest/tool-reference/analysis/intersect.html
[S34]: https://doc.esri.com/en/arcgis-pro/latest/tool-reference/data-management/dissolve.html
[S35]: https://docs.qgis.org/3.44/en/docs/gentle_gis_introduction/map_production.html
[S36]: https://doc.esri.com/en/arcgis-pro/latest/help/mapping/layer-properties/data-classification-methods.html
[S37]: https://doc.esri.com/en/arcgis-pro/latest/help/mapping/layer-properties/graduated-colors.html
