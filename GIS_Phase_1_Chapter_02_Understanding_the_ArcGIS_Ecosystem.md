# GIS and ArcGIS Fundamentals — Phase 1

# Chapter 2 — Understanding the ArcGIS ecosystem

## Document metadata

| Item | Value |
| --- | --- |
| Chapter ID | Phase 1, Chapter 2 |
| Title | Understanding the ArcGIS ecosystem |
| Source specification | `GIS_Phase_1_Chapter_Authoring_Blueprint.md`, revision 1.0 (18 September 2026) |
| Document revision | 1.0 — 19 September 2026 |
| Reference-check date | 19 September 2026 (every cited page was opened and read on this date; see the reference list for exact URLs) |
| Audience | Software developers at LiveBird Technologies with no GIS background beyond Chapter 1 |
| Software referenced | ArcGIS Pro (documentation labelled "Released version: ArcGIS Pro 3.7" on the check date); ArcGIS Online (continuously updated web service; documentation read on the check date); ArcGIS Enterprise ("latest" documentation, with the base-deployment component list verified against the 11.4 documentation page); ArcGIS Maps SDK for JavaScript (documentation labelled version 5.1, June 2026); ArcGIS API for Python (documentation labelled 2.4.3, March 2026); QGIS Desktop 3.40 (LTR documentation edition) and the QGIS *Gentle Introduction to GIS* 3.44 edition named in the blueprint |
| Execution status | **No procedure in this chapter has been executed in ArcGIS Pro, ArcGIS Online, ArcGIS Enterprise, or QGIS by the author.** Interface steps were written from the official pages cited beside them and are marked *not execution-tested*. The "prepared solution" traced in the lab is a documented, fictional deployment described in text; instructors who build it must follow the Instructor Appendix and record what they actually observe. |
| Data status | Every coordinate, record, user name, item name, URL fragment, and date in this chapter is **synthetic training material**. Nothing describes a real municipality, organisation, ArcGIS Online account, or server. |

### How to use this document

Read the modules in order; each one assumes the ones before it. Boxes labelled **Procedure (version-specific)** contain interface steps that will age; everything outside those boxes is durable concept. Boxes labelled **Platform note** separate ArcGIS-specific behaviour from general GIS principles, and boxes labelled **Verification item** tell an instructor what must be checked in the installed environment before a step is treated as fact. Comprehension checks have no answers in the learner text; answers are in the Instructor Appendix, which learners should not open before the progression gate in 2.8.

---

## Prerequisites

- **Chapter 1 completed.** You can frame a spatial question, distinguish policy from evidence, separate events (requests) from assets and boundaries (wards), and you have navigated a prepared map (layer visibility, pan, zoom, select) at least once — or followed the described walk-through.
- Ordinary developer experience: you know what a client, a server, a database, a file path, an API, and a user account with permissions are. These are used as analogies and are not taught here.
- No ArcGIS account, licence, or installation is needed to complete this chapter. Module 2.2 includes an optional short demonstration for readers who have ArcGIS Pro or QGIS available.

## Learning outcomes

By the end of this chapter you should be able to:

| ID | Outcome | Where it is taught | Where it is checked |
| --- | --- | --- | --- |
| LO1 | Name the five generic GIS responsibilities (desktop authoring, data storage, service delivery, content organisation, user applications) and explain why the computer running an application is not the system holding authoritative data. | 2.1 | 2.8 concept Q1; practical |
| LO2 | State the role of ArcGIS Pro and explain the difference between a saved project and the datasets it references. | 2.2 | 2.8 concept Q2 |
| LO3 | Describe ArcGIS Online items (web layer, web map, group, member, application) and explain why seeing a public map does not grant permission to edit or republish its data. | 2.3 | 2.8 concept Q3; scenario Q1 |
| LO4 | Explain what an organisation takes on when it operates ArcGIS Enterprise, and contrast using an application with operating infrastructure. | 2.4 | 2.8 concept Q4; practical |
| LO5 | Distinguish configuring an app builder, extending it, and building a custom application with the ArcGIS Maps SDK for JavaScript, the ArcGIS API for Python, or ArcPy, and choose between them from the requirement. | 2.5 | 2.8 concept Q5; scenario Q2 |
| LO6 | Map the same responsibilities onto OpenLayers, Mapbox, QGIS, PostGIS, and GeoServer without claiming one-to-one product equivalence, and label a task as shared GIS knowledge or platform-specific implementation. | 2.6 | 2.8 concept Q6; oral |
| LO7 | Trace a prepared solution from source dataset to application, identify where geometry, styling, access, and behaviour are managed, and say "not enough information" when the evidence does not show it. | 2.7 | 2.7 lab; 2.8 practical |

## Required materials

- This document and a way to draw a sketch (paper or any drawing tool).
- The **Chapter 2 evidence pack** in module 2.7 (printed in this document; no download is needed).
- **Optional for module 2.2:** ArcGIS Pro **or** QGIS Desktop with the instructor-prepared Chapter 1 project. If neither is available, read the described demonstration; nothing later in the chapter depends on having run it.
- **Not required:** an ArcGIS Online account, an ArcGIS Enterprise deployment, or any publishing action. This is an orientation chapter and no step in it consumes credits or requires a licence.

## Recap of the preceding chapter

Chapter 1 established the working method of the course: start from a **decision** ("which unresolved service requests should an inspection team investigate first?"), separate the organisation's **policy** from the **evidence** a GIS can supply, and turn vague words such as "near" into testable rules. A GIS was defined as data, software, hardware, and the people and processes around them, doing four things: **capture, storage, analysis, and communication** [S01]. You learned that a request is an **event**, a streetlight is an **asset**, a ward is an **administrative boundary**, and that these need different update rules. You navigated a prepared map and learned that **hiding a layer is not deleting data**. Finally, you saw that a convincing map can still rest on stale or incomplete data.

Chapter 1 closed by asking *where* each step of the question-to-decision workflow can happen. This chapter answers that question: it names the responsibilities a complete GIS solution has to cover, then shows which ArcGIS product — and which non-Esri tool — takes each one.

## The recurring scenario and the Chapter 2 fixture

The fictional municipality from Chapter 1 continues: it maintains **assets** (streetlights, drains, trees), keeps **road** centrelines, divides its territory into **wards**, receives **service requests** from the public, and records **inspections**. The Chapter 1 fixture is reused unchanged: two ward squares (A and B, each 1 km²), one road (R1, from (0, 500) to (2000, 500)), six requests (P1–P6), and three assets (SL-0113, DR-0042, TR-0301). All coordinates are on the flat, metre-based training grid with **no Earth location and no coordinate-system identifier**. If you need the tables, they are Tables F1–F4 in the Chapter 1 document.

**New for Chapter 2 — a prepared solution, described in text (synthetic).** To trace data through an ecosystem you need an actual deployment to look at. This chapter describes a fictional one, called **Solution S-2, "Request Tracker (Training)"**. It is documented in module 2.7 as an *evidence pack*: a file listing, a project description, item cards, and application behaviour notes, exactly as an instructor would capture them from a real deployment. Every name in it (`gis.author`, `Requests_Training`, `\\gis-files\training\`) is invented. Instructors with an ArcGIS Online organisation can build the same solution and substitute real screenshots; the Instructor Appendix explains how, and what remains unverified.

---

## 2.1 Start with generic GIS responsibilities

### 2.1.1 Five responsibilities that every complete GIS solution must cover

Chapter 1 described a GIS as a *system* rather than a program [S01]. This module makes that concrete by splitting the system into five **responsibilities**. They are not products; they are jobs that have to be done by *something* whenever geographic data goes from a person who creates it to a person who uses it.

| # | Responsibility | The question it answers | Typical evidence that it exists |
| --- | --- | --- | --- |
| R1 | **Desktop authoring** | Where do skilled people create, edit, check, symbolise, and analyse data with full control? | A desktop application with a project open, an attribute table, and editing tools |
| R2 | **Data storage** | Where is the authoritative copy of each dataset kept, and what keeps it consistent? | A file, a file-based database, a relational database, or a managed cloud store |
| R3 | **Service delivery** | How do other machines get the data or map images over a network, on request, with rules applied? | A web service endpoint (a URL that returns features, tiles, or images) |
| R4 | **Content organisation** | How are maps, layers, and apps catalogued, found, shared with the right people, and administered? | A portal or catalogue with items, owners, groups, sharing levels, and user roles |
| R5 | **User applications** | How does an inspector, a manager, or the public actually use the result? | A web app, a mobile app, a dashboard, or a desktop map |

**Why five and not one.** A developer new to GIS often expects one program to do everything, because a map viewer *looks* like the whole system. In practice each responsibility has different owners, different failure modes, and different rules. Editing a ward boundary (R1) is a data-quality job. Deciding who may see the request layer (R4) is a governance job. Keeping the service that feeds a dashboard online at 3 a.m. (R3, and its infrastructure) is an operations job. Keeping them separate lets you ask precise questions later: "the app is slow — is it the app, the service, or the storage?"

**Developer analogy (and where it stops).** The five responsibilities resemble a familiar software stack:

| GIS responsibility | Software analogy | Where the analogy stops |
| --- | --- | --- |
| Desktop authoring | The IDE and the developer's workstation | A GIS author also *creates the data*, not just the code that processes it; there is no compiler that rejects a wrong ward boundary |
| Data storage | The database | Spatial data has geometry, coordinate references, and topology rules the database may or may not enforce (Chapters 5–9) |
| Service delivery | The API server | Map services often return *rendered images or tiles*, not only records; caching and drawing rules are part of the contract |
| Content organisation | A package registry plus identity and access management | Items reference *each other* (a map references layers), so sharing one item does not automatically make the others visible |
| User applications | The front-end clients | Many GIS apps are *configured*, not coded (module 2.5), so "the app" may contain no source code at all |

**General principle versus platform behaviour.** The five responsibilities are a *general GIS principle*: they apply to an Esri stack, to QGIS with PostGIS and GeoServer, and to a Mapbox-based product (module 2.6). Which product covers which responsibility — and whether one product covers several — is *platform-specific*.

### 2.1.2 Tracing one prepared asset dataset through the responsibilities

The clearest way to see the responsibilities is to follow one dataset through them. Take the **Assets** dataset from the fixture: three points (SL-0113, DR-0042, TR-0301) with type, installation year, last-inspection date, and condition.

**Figure 2.1 — Illustrative workflow for one asset dataset (schematic; not every solution needs every step).**

```
 [R1 Desktop authoring]        [R2 Data storage]          [R3 Service delivery]
 Author opens the Assets   →   Authoritative copy kept    →   A web service exposes
 dataset, checks the three     in a database or file          the Assets as a layer
 records, fixes a typo in      the author's software          that other machines
 "Condition", sets symbols     can read and write             can query over HTTP
            │                                                         │
            ▼                                                         ▼
 [R4 Content organisation]                                  [R5 User applications]
 The published layer becomes an item in a portal:    →      A web map that references
 it has an owner, a description, a sharing level,           the layer is placed in a
 and belongs to a group "Inspection Team"                   dashboard and a field app;
                                                            an inspector opens it on a phone
```

Read the figure left to right and then down:

1. **Authoring (R1).** A person opens the Assets dataset in a desktop GIS, inspects the attribute table, corrects a value, and decides how the points should be drawn (for example, colour by *Condition*). Nothing outside their workstation has changed yet.
2. **Storage (R2).** The dataset the author edited lives somewhere: a file on a shared drive, a file-based database, or a relational database. That location — not the author's screen — is where the correction now exists.
3. **Publishing to a service (R3).** The author *publishes* the dataset. Publishing creates a **web service**: a network endpoint from which other software can request the features. Depending on the platform, publishing either **copies** the data to a managed store or **references** the data where it already is [X02]. This distinction matters enormously and is the subject of 2.1.3.
4. **Registration as content (R4).** The service is represented as an **item** in a catalogue (an ArcGIS *portal* in Esri terminology). The item has an owner, metadata, a sharing level, and group memberships. A **web map** item is then configured that references the layer and stores its styling and pop-up settings.
5. **Use (R5).** A dashboard and a field application are configured on top of the web map. An inspector opens the field app on a phone; a manager opens the dashboard in a browser. Neither of them opens the original file.

**This is an illustrative workflow, not a rule.** The blueprint is explicit that not every solution requires every component, and it is right. Three counter-examples:

- An analyst who answers a one-off question in a desktop GIS and emails a PDF map uses R1 and R2 only.
- A public open-data portal that lets anyone download a file uses R2 and R4, and possibly R3, but no application of its own.
- A static website with an embedded map image uses R5 and nothing else at run time; the other responsibilities were exercised once, when the image was produced.

The value of the five-part model is not that you must build all five; it is that when something is *missing* you can name it.

### 2.1.3 The computer running an application is not the system storing authoritative data

**Definition — authoritative data.** The *authoritative* copy of a dataset is the one the organisation has agreed is the source of truth: the copy that edits are made to, that other copies are derived from, and that is consulted when two copies disagree. "Authoritative" is a *decision*, not a technical property; a file does not know it is authoritative.

Consider the inspector's phone in Figure 2.1. The phone runs a field application and shows the Assets layer. Three things are true at once:

1. The phone holds a **copy** of some of the data — at minimum, whatever it has drawn on screen; if the map was taken offline, a local copy of the features.
2. The phone is **not** where the authoritative data lives. Turning the phone off does not delete an asset. Dropping the phone in a drain does not lose the municipality's records.
3. The phone's copy can be **stale**. If a colleague corrected SL-0113's condition ten minutes ago and the phone has not refreshed or synchronised, the inspector is looking at the old value.

The same is true of a developer's laptop with a desktop GIS open, a browser tab showing a dashboard, or a script that has loaded features into memory. **The application's machine renders, caches, and edits a view of the data; the storage system holds the data.** Exactly *which* system is authoritative depends on the deployment — and, as 2.1.2 showed, publishing by copying can create a *second* copy that the organisation must then declare authoritative or not. Solution S-2 in the lab shows this decision being made explicitly.

**Worked example.** Question: a manager says "the request map on my laptop shows P2 as *In progress*, but the crew says they closed it this morning — which is right?" Inputs: the manager's browser (an application), the web map (content), the hosted request layer (a service backed by storage), the crew's field app (an application). Reasoning: neither the laptop nor the phone is authoritative; both are clients of the same service. The question becomes (a) has the crew's edit reached the service (was it submitted, or is it waiting in an offline queue?), and (b) has the manager's browser refreshed since then? Expected outcome: check the service's record for P2 directly (for example, from the layer's own item page or table), not either device's screen. What this does not establish: which crew member closed it or whether closing it was correct — those are policy and audit questions.

**Common misconception.** "I have the map, so I have the data." Consequences: developers copy a project file to a colleague and are surprised that the layers show as broken (module 2.2), or assume that because a public map is visible they can download and republish its data (module 2.3), or test against a cached copy and ship a bug that only appears with live data.

**Comprehension check 2.1.** A colleague shows you a screenshot of a dashboard and says "the Assets data is in the dashboard". Name the responsibility the dashboard actually covers, name at least two responsibilities that must exist *elsewhere* for the dashboard to work, and say what you would need to see to find out where the authoritative Assets copy is.

---

## 2.2 Position ArcGIS Pro

### 2.2.1 What ArcGIS Pro is for

**Platform note — this module is ArcGIS-specific.** The *responsibility* (R1, desktop authoring) is general; the product is Esri's.

Esri's documentation describes ArcGIS Pro as "a full-featured professional desktop GIS application" for exploring, visualising, and analysing data, creating 2D maps and 3D scenes, and sharing work to ArcGIS Online or ArcGIS Enterprise [S02]. In the language of 2.1, ArcGIS Pro is the **desktop authoring** product: it is where a skilled person creates and edits datasets, designs how they look, runs analysis tools, and prepares content for publishing. It runs on a workstation, and on first launch you sign in with the credentials of an ArcGIS Online or ArcGIS Enterprise organisation [S02]; it is a licensed commercial product, and the exact licence type available to you is an organisational matter to confirm with your administrator, not something this chapter can state.

The vocabulary you will meet immediately:

| Term | Meaning in ArcGIS Pro | Source |
| --- | --- | --- |
| **Project** | "A body of related work that may include maps, scenes, layouts, and connections to resources such as system folders and databases"; stored as a file with the `.aprx` extension, by default in its own folder alongside a file geodatabase and a toolbox | [S02] |
| **Map** | A 2D view in which layers are drawn together; a **scene** is the 3D equivalent | [S02] |
| **Layout** | A page design (for printing or export) that contains one or more maps plus titles, legends, and scale bars | [S02] |
| **Table** | A tabular view of a dataset's records — the attribute table you used in Chapter 1 | [S02] |
| **Contents pane** | The panel that lists what is in the active view (for a map: its layers, in drawing order) | [S02] |
| **Catalog pane** | The panel that lists the project's items and connections — folders, databases, toolboxes, servers, and the signed-in portal | [S02] |
| **Connection** | A stored pointer from the project to a folder, database, toolbox, server, or portal that holds data or tools | [X01] |

**Developer analogy (and where it stops).** An ArcGIS Pro project is like an IDE *workspace* or *solution file*: it records which things you were working on, how the windows were arranged, and where to find the sources. The analogy is close — including the fact that the workspace does not contain the source code, only paths to it. It stops being accurate in one important way: an IDE workspace points at *code you wrote*, whereas a GIS project points at *data*, which may be edited by other people and tools while your project is closed. A project can therefore "go stale" in a way a workspace rarely does.

**A short guided demonstration (optional).** The purpose is orientation only: to *see* that a project, a map, a table, and a data connection are different things. The steps below are written from the official get-started and project pages [S02] [X01] and are **not execution-tested** by the author.

> **Procedure (version-specific) — ArcGIS Pro 3.7 documentation edition; not execution-tested.**
>
> 1. Start ArcGIS Pro and open the instructor-prepared Chapter 1 project (a `.aprx` file). If prompted, sign in with your organisation's credentials [S02].
> 2. Look at the **Contents** pane. It lists the layers in the open map — Requests, Assets, Roads, Wards — in drawing order [S02]. This is *presentation*: what the map shows and in what order.
> 3. Right-click the Requests layer and open its attribute table (the command is labelled **Attribute Table** in the layer's context menu in current documentation; confirm the label in your installed version). This is a *table view* of the same dataset [S02].
> 4. Open the **Catalog** pane. Expand **Databases** and **Folders**. You are now looking at *connections*: pointers to where the datasets are stored [S02] [X01]. Note that the Requests dataset appears here as an item inside a database or folder, not as "a layer".
> 5. Open the Requests layer's properties and find the **Source** section. It shows the *path* to the dataset the layer draws. Write that path down; you will use it in 2.2.2.
> 6. Close the project **without saving**. Nothing you did changed the data.
>
> **Verification item for the instructor:** confirm the pane names, the context-menu label for the attribute table, and the location of the layer's Source information in the installed ArcGIS Pro build, and note the build number in the change log (Instructor Appendix I.8).

If you do not have ArcGIS Pro, read the steps anyway and then look at the QGIS box below, which shows the same four ideas in a different product.

> **QGIS alternative (orientation only; not execution-tested).** In QGIS Desktop, the equivalent of the project is a `.qgz` (or `.qgs`) project file, which stores the layers added, their styling, the map's coordinate reference, and layouts — but, like the ArcGIS Pro project, it stores *references* to data sources rather than the data itself [X21]. The **Layers** panel plays the role of the Contents pane (it lists loaded layers and lets you reorder, hide, and show them [S01]); the **Browser** panel plays the role of the Catalog pane; the attribute table is opened from a layer's context menu; and a layer's source path is shown in its **Properties** under **Source** / **Information**. QGIS's own documentation warns that when a data source is moved or renamed the project opens with unavailable layers and asks you to repair the paths [X21] — the same "broken link" behaviour discussed next. The two products are not identical, and the panel names differ; what is *shared* is the idea that a project references data.

### 2.2.2 A saved project is not the datasets it references

This is the single most important fact in this module, and it is stated directly by Esri: a project "includes connections to folders, databases, toolboxes, and servers that contain data and scripts", the project is "a single file with the extension `.aprx`", and map files store "the path to each layer's data source, but not the data" — so a colleague who receives a project or map file "must have access to the data" independently [X01].

**Table 2.2 — What lives inside the project file and what lives outside it.**

| Inside the `.aprx` (presentation and organisation) | Outside the `.aprx` (data and tools) |
| --- | --- |
| Which maps, scenes, layouts, and tables the project has | The datasets themselves (feature classes, tables, rasters) |
| Which layers each map contains, in what order | The file geodatabase, folder, database, or server that stores them |
| Each layer's symbols, labels, pop-up settings, visibility, and definition filters | Edits made to the data by anyone, including you |
| The *path or connection* to each layer's source | The toolboxes' tool code (the project stores a connection to the toolbox) |
| Layout page designs | The signed-in portal's content (the project stores a connection) |

**Worked example.** Question: an author saves `RequestTracker.aprx`, in which the Requests layer is drawn in red for *Open* requests and its source is `\\gis-files\training\Municipal_Training.gdb\Requests`. She emails the `.aprx` file to a colleague on another network. What does the colleague see? Inputs: the project file (about presentation), the source path (a network location the colleague cannot reach). Reasoning: the project opens — it is a self-contained file — but each layer's source path fails to resolve, so the layers are listed but cannot draw; ArcGIS Pro marks such layers as broken. Expected outcome: a map with layer names and symbols defined, but no features. Check: open the layer's Source properties and compare the path with what is reachable. What this does not establish: whether the *data* is intact — it almost certainly is, sitting untouched on the original share. The failure is a missing *connection*, not lost data.

Now reverse it. The author keeps the project open, and a colleague on the same network edits the Requests dataset directly in the geodatabase. The author's project file is unchanged, yet when the map redraws it shows the colleague's edit, because the map *reads through* its connection to the storage. The project did not "have" the old data; it never had any.

**Consequences for a developer.**

- A project file in version control captures *presentation and structure*, not data. Data needs its own backup and ownership (Chapter 7 and later).
- "Send me the project" is rarely enough; the useful request is "send me the project *and* tell me where its data is, or package both together" — ArcGIS Pro has packaging options for exactly this reason, which you will meet when you need them.
- When a layer shows as broken, the first diagnostic is the source path, not the data.

**Common misconception.** "Saving the project saves my edits." Consequence: an author edits features, saves the project, and — because in ArcGIS Pro edits to data are saved separately from the project — may lose or fail to commit data edits, or, conversely, may believe that *not* saving the project will undo data edits that have already been saved to storage. The blueprint reserves storage formats and editing workflows for Chapters 7 and 9; for now, the durable rule is **project state and data state are saved separately, and you must know which one you changed.**

> **Verification item.** The exact prompts ArcGIS Pro shows when closing a project with unsaved data edits, and the default for saving edits, are version-specific and were not execution-tested. Instructors should demonstrate the behaviour live on a disposable copy and record what the installed version does.

### 2.2.3 Authoring a dataset versus changing how it appears

The last idea in this module is a habit you will use in every later chapter: before doing anything in a desktop GIS, ask **"am I about to change the data, or change how the data is shown?"**

| Task | Changes the dataset (authoring) | Changes only the presentation | Why it matters |
| --- | --- | --- | --- |
| Move request P3 100 m north because the reporter gave a wrong location | ✔ | | Every map, service, and app that reads this dataset will show the new location; there is an audit question (who moved it, on what evidence?) |
| Draw *Open* requests in red and *Resolved* in grey | | ✔ | Stored in the map/layer; the dataset's Status values are untouched |
| Change the field name `Cat` to `Category` | ✔ | | It is a schema change; anything that referenced `Cat` (a pop-up, a query, a script) may break |
| Show the layer only when zoomed in past a certain scale | | ✔ | Visibility rule; the features still exist at every scale |
| Delete the *Closed – duplicate* request P6 | ✔ | | Destructive; a filter that *hides* closed duplicates would be presentation |
| Filter the map to show only *High* priority | | ✔ | Chapter 1's "hiding is not deleting" rule |
| Add a new field `AssignedCrew` and fill it in | ✔ | | Schema plus data; other consumers see it |
| Add a label showing the request ID | | ✔ | Labels are drawn from the data but stored as a layer setting |

**Exercise (do it now; answers in the Instructor Appendix).** Classify each of the following as *authoring the dataset* or *changing how it appears*, and state one consequence for other users of the same dataset: (a) correcting the installation year of streetlight SL-0113 from 2018 to 2016; (b) turning off the Wards layer; (c) changing the map's symbol for drains from a square to a circle; (d) splitting Road R1 into two segments at x = 1000; (e) setting a definition filter so the layer shows only requests reported after 2026-09-01.

**Comprehension check 2.2.** In one sentence each: what does a `.aprx` file contain, what does it not contain, and what is the first thing to check when a layer in a project shows as broken?

---

## 2.3 Position ArcGIS Online

### 2.3.1 What ArcGIS Online is for

**Platform note — this module is ArcGIS-specific.** The responsibilities involved (R3 service delivery, R4 content organisation, and part of R5 applications) are general; the product is Esri's.

Esri describes ArcGIS Online as "a cloud-based mapping and analysis solution" in which data and maps are "stored in a secure and private infrastructure" [S03]. Four capabilities matter for this chapter:

1. **Hosted mapping.** You can "publish data as web layers on ArcGIS Online", and those layers are "hosted in the Esri cloud and scale dynamically" [S03]. In the 2.1 model, ArcGIS Online provides both the *storage* (R2) and the *service delivery* (R3) for hosted layers — Esri operates the servers and storage; you do not see them.
2. **Sharing.** Content can be shared "with anyone, anywhere or keep them private", and you can create groups, which can be private or public [S03].
3. **Content management.** Web maps, 3D scenes, web apps, and notebooks are created and stored as *items* with metadata and ownership [S03] — responsibility R4.
4. **Organisation administration.** Administrators "configure security, manage members" and set terms of use [S03].

Two practical facts to know from the start, because developers ask about them immediately:

- **Who runs it.** Esri operates the infrastructure. You do not install, patch, back up, or scale ArcGIS Online; you administer an *organisation* within it. This is the key contrast with ArcGIS Enterprise (module 2.4).
- **Credits.** ArcGIS Online meters certain activities: "Credits are the currency used across ArcGIS and are consumed for specific transactions and types of storage, such as storing features, performing analytics, and using premium content" [X10]. The same page states that "most of what you do in ArcGIS Online does not require credits" and gives using basemaps, exporting data, and single address searches as examples [X10]. This chapter's lab performs no credit-consuming action. Never estimate credit costs from memory; look up the current table for the operation in question.

**Developer analogy (and where it stops).** ArcGIS Online is closest to a managed cloud platform: you get storage, an API layer, identity, and a catalogue without running servers. The analogy stops where GIS-specific *content relationships* begin: unlike a bucket of files, ArcGIS Online items reference one another (a map references layers; an app references a map), and the platform's sharing model operates on those items individually.

### 2.3.2 The things you will see: items, web layers, web maps, groups, members, and applications

Everything in ArcGIS Online is an **item**: a catalogued object with an owner, a title, a description, tags, a sharing level, and a type. Esri's own dashboard documentation puts it plainly — dashboards are "items within the ArcGIS geoinformation model", like web maps and web layers [X19]. The item types you must recognise in this chapter are:

| Item type | What it is | What it stores | What it references | Source |
| --- | --- | --- | --- | --- |
| **Web layer** (e.g., hosted feature layer, tile layer, map image layer, imagery layer, scene layer) | A logical collection of geographic data exposed as a service | For a *hosted* layer, the data itself, in ArcGIS Online | For a *referenced* layer (an Enterprise scenario), a service whose data "still resides in the map service's data source location and is not copied to ArcGIS Online" | [X03] |
| **Hosted feature layer view** | A separate item that is "a view of the data in the source layers", so "edits made to the data in the source appear in the view" | Its own settings (sharing, editing, excluded fields); *no* independent copy of the data | The source hosted feature layer | [X07] |
| **Web map** | "An interactive display of geographic information" — a basemap, operational layers, styling, pop-ups, extent | The map's configuration; it references layers "hosted and shared through ArcGIS Online" rather than storing data | Web layers and a basemap | [X04] |
| **Web app** (Instant App, Experience Builder app, dashboard, Field Maps configuration) | An application configured on top of a map or layers | The app's configuration | A web map and/or layers | [X18] [X17] [X19] [X20] |
| **Group** | "A collection of items usually related to a specific area of interest", whose owner decides "who can find the group, who can join, and who can contribute content" | Membership and settings | The items shared with it | [X08] |
| **Member** | A signed-in person in the organisation, with a **user type** and a **role** that together determine privileges (default roles include Viewer, Data Editor, User, Publisher, Facilitator, and Administrator) | Profile and privileges | Owns items; belongs to groups | [X09] |

**Figure 2.3 — How items reference one another in a typical ArcGIS Online solution (schematic).**

```
   Member "gis.author" (owner)                      Group "Inspection Team – Training"
            │ owns                                          ▲ shared with
            ▼                                               │
   ┌──────────────────────┐   references   ┌───────────────┴─────────┐   references   ┌────────────────────┐
   │ Hosted feature layer │ ◄───────────── │ Web map                 │ ◄───────────── │ Dashboard (app)    │
   │ "Requests_Training"  │                │ "Request Overview"      │                │ "Request Monitoring"│
   │ (DATA lives here)    │                │ (styling, pop-ups,      │                │ (indicators, lists, │
   └──────────┬───────────┘                │  layer list — NO data)  │                │  filters — NO data) │
              │ source of                  └─────────────────────────┘                └────────────────────┘
              ▼
   ┌──────────────────────┐   references   ┌─────────────────────────┐   references   ┌────────────────────┐
   │ Hosted feature layer │ ◄───────────── │ Web map                 │ ◄───────────── │ Instant App        │
   │ VIEW "…_public"      │                │ "Request Overview       │                │ "Request Locator"  │
   │ (read-only; fields   │                │  (Public)"              │                │ shared: Everyone   │
   │  hidden; NO copy)    │                └─────────────────────────┘                └────────────────────┘
   └──────────────────────┘
```

The arrows are the point. **Only one box in Figure 2.3 holds data.** The view reads the same data with different settings [X07]. The web maps hold styling and pop-up configuration and point at layers [X04]. The apps hold their own configuration and point at maps. This is why the blueprint warns against implying "all items contain independent copies of their underlying data": in a healthy deployment most items contain *no* data at all.

Two consequences follow directly from the documentation:

- **Deleting is asymmetric.** "If you delete a hosted layer, the data stored in ArcGIS Online is deleted" [X03]. Deleting a web map that references it deletes only the map's configuration; the layer and its data remain. Deleting the layer, however, breaks every map and app that referenced it.
- **Styling can be in more than one place.** A web map stores how *its* layers look. Two web maps can reference the same hosted layer and draw it completely differently, exactly as two ArcGIS Pro maps can reference one dataset (2.2.3). When you are asked "where is the red symbol for *Open* requests defined?", the answer is usually a map, not the data — and if the evidence does not show which map, the correct answer is "not enough information" (module 2.7).

**Worked example.** Question: the municipality wants the public to see request locations but not the reporter's phone channel or free-text notes, while the inspection team can see and edit everything. Inputs: one hosted feature layer `Requests_Training` (all fields), a team group, the public. Reasoning: publishing a *second copy* of the data for the public would create two datasets that drift apart — exactly the authoritative-copy problem of 2.1.3. Esri's documented pattern is instead to create a **hosted feature layer view**, exclude the sensitive fields from the view, keep the view read-only, and share the *view* publicly while the source layer stays shared with the group; the documentation describes precisely this — allowing organisation members to edit the layer "but share a read-only feature layer view with the public", and excluding fields "if the view users do not need to access them" [X07]. Expected outcome: one authoritative dataset; two items with different sharing and field visibility. Check: edit a note on the source layer and confirm the public view shows the same request location with no note field. What this does not establish: that the public app is fast or that the notes are free of sensitive content in other fields — those are separate reviews.

### 2.3.3 Seeing a public map does not mean you may edit or republish its data

Three separate things are easy to conflate: *visibility*, *edit permission*, and *usage rights*.

1. **Visibility is set per item by its sharing level.** ArcGIS Online items are either unshared (owner only, plus administrators), shared with the organisation, shared with specific groups, or shared with everyone, in which case "anyone who has access to ArcGIS Online can find and use your item" [X05]. A map you can open in a browser without signing in is shared with everyone.

2. **Editing is a separate setting on the layer, and it is off by default.** For hosted feature layers, editing is disabled unless the owner or administrator enables it; when enabled, the owner chooses which operations are allowed (add, delete, update geometry and attributes, or attributes only) [X06]. Public editing needs a further explicit step: attempting to enable editing on a layer shared with everyone is prevented unless public data collection is deliberately approved [X06]. So a publicly *visible* layer is, by default, not publicly *editable*. The documentation also recommends sharing a read-only view publicly while keeping the editable layer restricted to a group [X06] [X07].

3. **Usage rights are a licensing and terms-of-use question, not a technical one.** Being able to *see* or even *download* a public layer says nothing about whether you may republish it, combine it with your own data, or use it commercially. That depends on the item's stated terms of use and licence, which the owner writes and the organisation's terms govern [S03]. Chapter 7 makes licence and usage conditions part of the dataset intake form; for now the rule is: **visible ≠ editable ≠ reusable.**

**Developer analogy (and where it stops).** A public GitHub repository is readable by anyone, writable only by collaborators, and reusable only under its licence — three independent facts. The analogy is exact on independence. It stops at granularity: in ArcGIS Online, edit permission can be limited to *attributes only*, and a *view* can expose a subset of fields, which has no clean Git equivalent.

**Common misconception.** "It's on a public map, so it's open data." Consequence: a developer scrapes a public layer into their own service, republishes it, and violates the owner's terms — or, worse, treats an out-of-date public copy as authoritative for a decision.

**Deferred.** How to configure sharing levels, groups, and editing settings step by step, and how administrators manage roles, are later-phase administration topics. In this chapter you only need to *read* them off an item card.

**Comprehension check 2.3.** A colleague finds a public web map of ward boundaries published by another organisation and proposes to "just add it to our web map and let inspectors edit the boundaries". Identify three separate questions that must be answered before that is possible, and name the item type in which each answer would be found.

---
## 2.4 Position ArcGIS Enterprise

### 2.4.1 An organisation controls its Enterprise deployment — and its consequences

**Platform note — this module is ArcGIS-specific.** The responsibilities are the same five as before; what changes is *who operates the machines*.

Esri describes ArcGIS Enterprise as "the foundational software system for GIS" that organisations deploy on infrastructure they control: public cloud, private cloud, or on-premises physical or virtual machines, in small single-machine or large multi-machine configurations, connected to the internet or disconnected from it [S04]. The same page makes the organisation responsible for the choice of infrastructure, its scale, and its strategy for uptime and data-loss prevention [S04].

The blueprint's warning is exact: **ArcGIS Enterprise is not simply "ArcGIS Online installed locally."** It is tempting to think so, because the two share a great deal at the *content* level — the same idea of items, groups, members, and web maps, and Esri notes that web maps "adhere to the same web map specification" across ArcGIS so a map made in ArcGIS Pro can be edited in ArcGIS Online or a portal [X04], and that Enterprise "connects with ArcGIS Online" to share content [S04]. The difference is *everything below the content level*:

| Concern | ArcGIS Online | ArcGIS Enterprise |
| --- | --- | --- |
| Who provides the machines, storage, and network | Esri [S03] | Your organisation, on infrastructure it chooses [S04] |
| Who installs, upgrades, patches, backs up, and monitors | Esri | Your organisation |
| Can it run without internet access | No — it is a cloud service | Yes — "connected or disconnected" deployments are supported [S04] |
| Where hosted data physically lives | Esri's cloud [S03] | Your deployment's data store [S04] |
| Can it serve data from your own databases without copying | Only by referencing services from an ArcGIS Server [X03] | Yes — that is a core purpose (see 2.4.2) |
| How capacity grows | Esri scales the service [S03] | You add machines and configure roles [S04] |
| Metering | Credits for specific transactions and storage [X10] | Licensing per deployment (out of scope here; confirm with the organisation) |

The consequence for a developer is a change of *job description*. Building an app against ArcGIS Online means writing to an API and administering content. Building the same app against ArcGIS Enterprise means that someone in your organisation also owns servers, certificates, backups, upgrades, and outages — and if that someone is you, those tasks now sit beside your feature work.

**Developer analogy (and where it stops).** ArcGIS Online is to ArcGIS Enterprise roughly as a managed database service is to running the same database engine on your own servers: similar interface, very different operational responsibility. The analogy stops in two places. First, Enterprise is a *system of several cooperating components* (2.4.2), not a single engine. Second, the two are designed to interoperate and coexist — many organisations run both and share content between them [S04] — rather than being alternative editions of one thing.

### 2.4.2 The roles inside an Enterprise deployment: content, processing, and storage

You do not need the installation view of ArcGIS Enterprise in this phase; you need to know which *responsibility* each part carries so that you can read an architecture diagram. Esri's introduction lists four components in the base deployment and gives each a role [S04]:

| Component (Esri name) | Role in the 2.1 model | What it does, in plain language | Source |
| --- | --- | --- | --- |
| **Portal for ArcGIS** | R4 content organisation (and sign-in) | The catalogue and sharing layer: items, groups, members, roles, and the web interface people sign in to. It is the Enterprise counterpart of the ArcGIS Online organisation's content side. | [S04] |
| **ArcGIS Server** | R3 service delivery, plus processing | Powers mapping and analysis; runs the services that clients call. It can take specialised server roles, and one instance is configured as the **hosting server** for the portal. | [S04] [X11] |
| **ArcGIS Data Store** | R2 managed storage for hosted content | The data storage that backs the hosting server's hosted layers. It is Esri-managed *within your deployment*; you do not design its tables. | [S04] [X11] |
| **ArcGIS Web Adaptor** | Integration, not a GIS responsibility of its own | Connects the portal and server to your existing web server and security setup. | [S04] |

Two further points prevent the most common confusions:

- **Enterprise can serve data you already have.** Unlike a hosted layer, an ArcGIS Server service can *reference* data registered in your own database or file location; publishing from ArcGIS Pro offers exactly that choice — copy all data to the server or portal so it becomes "managed by ArcGIS", or reference registered data that stays where it is [X02]. This is how an organisation keeps its authoritative asset database where its other systems already read it while still delivering it as a web service. Whether a given service copies or references data is therefore a question to *ask*, never to assume (module 2.7).
- **"ArcGIS Data Store" is a product, not a synonym for "our database."** It is the managed storage behind hosted layers in an Enterprise deployment [S04]. Your organisation's PostgreSQL, SQL Server, or Oracle database is a different thing, registered with ArcGIS Server as a data source. Chapter 7 returns to this distinction when it introduces geodatabases.

> **Verification item — release-specific component list deferred.** The blueprint deliberately defers the exact base-deployment list because it changes between releases. For the record, the 11.4 documentation states that the base deployment consists of Portal for ArcGIS "configured as a portal", ArcGIS Server "licensed as ArcGIS GIS Server and configured as the hosting server", ArcGIS Data Store "configured as a relational data store and an object store", and two instances of ArcGIS Web Adaptor, and that these "can be installed across one or more machines, any of which can be physical, virtual, or cloud machines" [X11]. Before any hands-on Enterprise training, the author must reread the base-deployment page for the *installed* release and update this box; the "latest" URL of that page was not reachable on the check date (see Instructor Appendix I.7).

### 2.4.3 Using an application versus operating infrastructure

The final distinction in this module is between two kinds of work that are often confused because the same person is asked to do both:

| Using and configuring the platform (this phase) | Operating the platform (later Enterprise track) |
| --- | --- |
| Open a web map, make a dashboard, share a layer with a group | Install and upgrade Portal for ArcGIS, ArcGIS Server, and ArcGIS Data Store |
| Publish a layer from ArcGIS Pro to the portal | Configure certificates, web-server integration, and single sign-on |
| Set a layer's editing options | Back up the portal content and the data store, and test restoring them |
| Add a member to a group | Monitor service health, logs, disk space, and licences |
| Build an app with the JavaScript SDK against a service | Restore a failed server; add machines to scale a service |

Esri's own framing of Enterprise assigns the right-hand column to the organisation: choosing the infrastructure, its scale, and the uptime and data-loss strategy [S04]. None of it is taught in Phase 1. What you must be able to do now is *recognise* it — when a request arrives such as "the request service is down, can you fix it?", you should know that this is an infrastructure operation, that it needs access and skills different from map configuration, and that in ArcGIS Online the equivalent request would go to Esri, not to you.

**Worked example.** Question: the municipality's dashboard has stopped updating. Four possible causes are proposed: (a) the crew has not submitted edits; (b) the web map was changed to reference a different layer; (c) the hosting server's machine is out of disk space; (d) the dashboard's refresh interval was set to "never". Classify each. Reasoning: (a) is application use by field staff; (b) is content configuration; (c) is infrastructure operation; (d) is application configuration. Expected outcome: only (c) requires an Enterprise operator (and only exists if the deployment *is* Enterprise; in ArcGIS Online, (c) is Esri's problem). Check: each hypothesis is tested where its responsibility lives — the layer's own table for (a), the map's layer list for (b), the server's administration interface for (c), the dashboard settings for (d). What this does not establish: the *order* to check them in — a sensible operator starts with the cheapest and most likely, not the most dramatic.

**Common misconception.** "Enterprise means more features." Consequence: a team chooses Enterprise to "unlock" something, then discovers that the practical difference is a set of operational obligations they did not plan for. Feature differences do exist and change by release; the *responsibility* difference is permanent.

**Comprehension check 2.4.** In two sentences, explain to a project manager why "we'll just install ArcGIS Online on our own server" is not a meaningful plan, and name two responsibilities the organisation would take on if it deployed ArcGIS Enterprise instead.

---

## 2.5 Position configurable apps, APIs, and SDKs

### 2.5.1 Configurable application builders: arranging supported capabilities

**Definition — configuration.** *Configuring* an application means selecting and arranging capabilities that the product already supports — choosing a template, switching widgets on, pointing them at a map or layer, setting labels and filters — without writing application code. The product's authors decided what is possible; you decide what is used.

ArcGIS offers several configurable builders. The four below are the ones the municipal scenario needs; each is quoted from its own documentation so that you can see the role Esri assigns to it.

| Builder | Role (Esri's description) | Municipal example | Source |
| --- | --- | --- | --- |
| **ArcGIS Instant Apps** | "Share your maps as apps and provide your audience with an intuitive and focused experience"; apps come from "a gallery of app templates" with an express configuration option | A public *Request Locator*: a focused viewer of the public request view with search and a legend, built from a template in minutes | [X18] |
| **ArcGIS Experience Builder** | "Create unique web experiences using flexible layouts, content, and widgets that interact with 2D and 3D data" | An internal *Inspection Console* combining a map, a table of open requests, a chart by ward, and a filter — laid out with widgets | [X17] |
| **ArcGIS Dashboards** | "A presentation of geographic information and data that allows you to monitor events, make decisions, inform others, and see trends", using maps, lists, charts, gauges, indicators, and tables | A *Request Monitoring* dashboard showing the count of open requests, a list sorted by age, and a map | [X19] |
| **ArcGIS Field Maps** | "A mobile solution that allows mobile workers to explore maps, collect data, complete tasks, and share their location from the field"; maps and forms are prepared in the *Field Maps Designer* web app and used in the *Field Maps* mobile app, online or offline | A crew opens the request map on a phone, updates a request's status through a form, and adds an inspection record to a related table | [X20] |

Notice that every builder in the table works *on top of* a web map or layers (module 2.3). The builder stores an application item; the data stays in the layer. Configuring an app therefore never changes the data model, and switching from one builder to another usually leaves the map and layers untouched.

> **Platform note — a retired builder.** Esri's developer documentation lists *ArcGIS Web AppBuilder* as retired [S05]. You will still find it in older tutorials and existing deployments; do not choose it for new work, and treat any material that assumes it as dated.

**Developer analogy (and where it stops).** Configuring a builder is like assembling a page in a CMS: you pick blocks, set their options, and publish. The analogy stops at the boundary of the supported capability. A CMS usually has a plugin escape hatch you write yourself; some ArcGIS builders have one (Experience Builder can be extended with custom widgets [S05]), others do not. Knowing where that boundary lies is the entire skill of 2.5.3.

**Common misconception.** "Configurable means limited, so real developers build custom." Consequence: teams write and maintain code for a dashboard that a configured product already does, then own every bug and every upgrade themselves. The blueprint's rule is to justify the choice from the *requirement*, not from developer preference.

### 2.5.2 Custom development: the JavaScript SDK, the Python API, and ArcPy at the role level

When a requirement falls outside what a builder supports, ArcGIS exposes its capabilities through APIs and SDKs. Esri's developer documentation lists many [S05]; the blueprint asks you to distinguish three at the *role* level, and to know that all of them sit on the same underlying web services.

**The foundation: services and REST APIs.** Everything a client does with ArcGIS Online or Enterprise — querying features, editing them, fetching tiles, listing items — is a request to a web service. Esri's developer site lists these as service APIs: for example, a *feature service* lets you "add, update, delete, and query feature data", and the *portal service* lets you "securely store, access, and manage content items, users, and groups" [S05]. The SDKs below are convenient, supported ways of calling these services; they do not add capabilities the services lack.

| Product | Role | Runs where | Typical municipal use | What it is *not* | Source |
| --- | --- | --- | --- | --- | --- |
| **ArcGIS Maps SDK for JavaScript** | Build interactive 2D and 3D web mapping applications in the browser; described as "a developer product for building mapping and spatial analysis applications for the web", with ready-to-use web components such as `<arcgis-map>` | The user's browser | A custom public request-submission page with your own UI, workflow, and branding, reading and writing the request feature service | Not a data store, not a server, not an administration tool | [X12] |
| **ArcGIS API for Python** (`arcgis` package) | Script and automate the *platform*: "GIS organization administration", "content management", and "spatial analysis and data science", against ArcGIS Online and ArcGIS Enterprise; commonly used from Jupyter notebooks | Any machine with Python that can reach the portal over the network | A nightly script that lists items shared with everyone, checks that the public view still excludes the *Note* field, and reports layers with editing enabled | Not a browser UI toolkit; not tied to ArcGIS Pro | [X13] |
| **ArcPy** | "A Python site package that provides a useful and productive way to perform geographic data analysis, data conversion, data management, and map automation" — the scripting face of ArcGIS Pro's geoprocessing tools and map documents | A machine with ArcGIS Pro (ArcPy "must be run from inside a conda environment, which ArcGIS Pro uses to manage the installation of Python") or an ArcGIS Server, which also includes ArcPy for running geoprocessing tools and publishing | Automating the desktop authoring step: a script that validates the Assets dataset, computes a field, and exports a map layout every week | Not a web SDK; cannot run without an ArcGIS Pro or ArcGIS Server installation | [X14] [X15] [X16] |

A compact way to remember the three roles:

- **JavaScript SDK** — *the user's* side: what people see and click in a browser.
- **Python API** — *the platform's* side: items, users, groups, services, and analysis over the network.
- **ArcPy** — *the desktop/server tool* side: geoprocessing and map automation where ArcGIS Pro or ArcGIS Server is installed.

Two more categories exist and are worth one sentence each, so that you recognise them in a design: the **ArcGIS Maps SDKs for native apps** (Kotlin, Swift, .NET, Flutter, Qt, Java, plus game-engine SDKs) build installed mobile and desktop apps rather than browser apps [S05]; and Esri's documentation lists open-source browser libraries — Leaflet, MapLibre GL JS, OpenLayers, and CesiumJS — as libraries that "can be used to build web apps with ArcGIS services" [S05], which matters for module 2.6.

**Developer analogy (and where it stops).** JavaScript SDK ≈ a front-end framework with a map component; Python API ≈ a cloud provider's admin SDK; ArcPy ≈ a build-tool's scripting layer that only works where the tool is installed. The analogy holds for *where the code runs and what it talks to*. It stops at the fact that all three ultimately read and write the *same* items and services, so a change made through one is visible through the others immediately — there is no separate "API copy" of the data.

**Worked example.** Question: the municipality wants (a) a public web page where residents draw the location of a pothole and submit it, (b) a weekly check that no public item exposes reporter notes, and (c) a weekly regenerated PDF map of open requests per ward using a fixed layout. Which tool, and why? Reasoning: (a) is browser UI over a feature service → JavaScript SDK (or, if a template satisfies the requirement, an Instant App or Experience Builder app — check 2.5.3 first). (b) is an inventory of portal items and their settings → Python API. (c) is map layout export from a project → ArcPy, running where ArcGIS Pro is installed. Expected outcome: three different tools for three different responsibilities, not one "best" language. What this does not establish: that these are the *only* correct answers — for instance, (c) could also be done by scheduling a server-side task; the point is the *reasoning*, which the instructor should assess.

**Common misconception.** "ArcPy is the ArcGIS Python API." Consequence: a developer installs the `arcgis` package on a server, tries to call geoprocessing tools that only ArcPy provides, and fails; or expects ArcPy to work in a plain Python environment with no ArcGIS Pro. The two are different packages with different roles and different installation requirements [X13] [X15].

### 2.5.3 Decision exercise: configure, extend, or build?

The blueprint requires a decision *justified by the requirement*. Use the worksheet below on each request; the columns are ordered so that cheaper options are considered first.

**Table 2.5 — Decision worksheet.**

| Question | If yes → | If no → |
| --- | --- | --- |
| 1. Can a builder's *supported* capability meet the stated requirement (not a close approximation)? | **Configure.** Record the builder, the template or widgets, and the acceptance test. | Go to 2. |
| 2. Is the gap small and confined, and does the builder document an extension mechanism for it? | **Extend.** Record what the extension does, who maintains it, and how it survives platform upgrades. | Go to 3. |
| 3. Is the gap the *core* of the requirement (a workflow, UI, or integration the builders do not model)? | **Build** with the appropriate SDK. Record the services used, the ownership of the code, and the operational plan. | Return to 1; the requirement may be unclear. Rewrite it. |

Three checks apply to every row: **ownership** (who fixes it in two years?), **upgrade risk** (what happens when the platform changes?), and **evidence** (has someone confirmed, on the current release, that the capability exists — a search snippet does not count?).

**Three mini-cases (work them before reading the reasoning).**

*Case A.* "Managers want to see the number of open requests per ward, updated as edits arrive, on a screen in the control room." Reasoning: a dashboard's documented purpose is to "monitor events" using indicators, charts, and maps [X19]; the requirement names no custom interaction. → **Configure** (Dashboards). A custom build here would be developer preference, not requirement.

*Case B.* "Field crews must update request status on a phone, sometimes without network coverage, and attach an inspection record." Reasoning: Field Maps' documentation covers exactly this — maps and forms prepared in Field Maps Designer, working "online, offline", adding records "to a related table (for inspection workflows)" [X20]. → **Configure** (Field Maps). Note what is *not* established by reading the overview: whether the specific form logic the crews want is supported. That is a verification item, and it is the kind of thing that turns a "configure" into an "extend" or "build" after a proper check.

*Case C.* "Residents submit requests from a page embedded in the municipality's existing citizen portal, using the portal's own login, with a three-step wizard and a duplicate-check against nearby open requests before submission." Reasoning: the embedding, the external login, and the custom wizard with a pre-submission spatial check are the *core* of the requirement, not a gap at the edge. → **Build** with the JavaScript SDK against the request feature service. An instructor should accept "extend Experience Builder with a custom widget" only if the learner shows evidence that the embedding and login requirements can be met that way.

**Comprehension check 2.5.** A colleague proposes to write a custom React application because "Instant Apps look too simple". Using Table 2.5, write the two questions you would ask them first, and state what evidence would settle each.

---

## 2.6 Establish transferable roles across platforms

### 2.6.1 Compare roles, not brand equivalence: OpenLayers and Mapbox

Everything from 2.2 to 2.5 was one vendor's arrangement of the five responsibilities. The responsibilities themselves are portable; the arrangement is not. This module builds the habit of asking "which responsibility does this product cover?" before asking "which Esri product does it replace?" — because the honest answer to the second question is usually "part of one, and none of the others."

**OpenLayers.** The project describes itself as "a high-performance, feature-packed library for all your mapping needs" that "makes it easy to put a dynamic map in any web page" and "can display map tiles, vector data and markers loaded from any source", including tiled layers from several providers, OGC mapping services, and vector formats such as GeoJSON, TopoJSON, KML, and GML; it is free, open source, and released under the 2-clause BSD licence [S06]. In the 2.1 model this is **R5, the browser application layer** — the same role as the ArcGIS Maps SDK for JavaScript. It says nothing about where the tiles or vector data come from (R2, R3), who may see them (R4), or who authored them (R1). Esri's own documentation lists OpenLayers among libraries that can consume ArcGIS services [S05], which makes the point concretely: OpenLayers can be the front end of an Esri stack, an open-source stack, or a static file.

**Mapbox.** Mapbox's site presents product families rather than a single library: *Maps* (Mapbox GL JS for the web, mobile Maps SDKs, Studio for map design, static maps, a tiling service), *Navigation*, *Search* (geocoding, address autofill), *Data* (traffic, movement, boundaries), and a self-hosted option [S07]. Several responsibilities are therefore on offer — a browser library (R5), hosted tile and data services (R3, and R2 for the datasets Mapbox provides), and a design tool for map styles (a slice of R1 for *cartography*, not for editing your own asset data). This chapter takes no position on Mapbox pricing, licensing, or terms; the blueprint forbids inferring them, and they change.

**Developer analogy (and where it stops).** "We'll use OpenLayers" is like "we'll use React": it names the front-end library and says nothing about the database, the API layer, authentication, or hosting. "We'll use Mapbox" is more like "we'll use Firebase": a vendor's bundle of client SDKs and hosted services, chosen together. The analogy is accurate about *scope*. It stops at data ownership: in a GIS the dataset is usually the organisation's long-lived asset, so the question "who holds the authoritative copy, and can we take it with us?" weighs more heavily than it does for a typical web app's session data.

**Why "neither label alone specifies a complete replacement architecture."** Suppose a colleague says "we don't need ArcGIS, we'll use OpenLayers." Ask where the municipality's request data will be stored and edited (R1, R2), how it will be served with access control to the crews but not the public (R3, R4), how crews will edit it offline on phones (R5, but a *different* application from the browser one), and who will operate whichever servers that implies. OpenLayers answers none of these; it was never meant to. Saying "Mapbox" answers more of them — but with Mapbox's own hosted services, which is a different operational and contractual choice, not a like-for-like swap. The sentence "we'll use X" is complete only when X, or a named set of products around X, covers all five responsibilities the requirement needs.

### 2.6.2 QGIS, PostGIS, and GeoServer: what each covers and what it does not

| Product | Self-description | Responsibility covered | What it does **not** cover on its own | Source |
| --- | --- | --- | --- | --- |
| **QGIS** | A GIS application — "a software program that forms part of the GIS", with a map view, a legend of layers, and attribute tables; "completely free" | **R1 desktop authoring** (create, edit, style, analyse) | Hosted storage, web services, a content portal, member management, mobile apps (separate projects exist for some of these; they are not QGIS Desktop) | [S01] |
| **PostGIS** | "Extends the capabilities of the PostgreSQL relational database by adding support for storing, indexing, and querying geospatial data" — points, lines, polygons, multi-geometries, spatial indexes, and spatial functions for distance, area, and intersection | **R2 data storage** with spatial capability inside PostgreSQL | Serving maps over the web, a catalogue, user-facing apps, desktop editing tools. It is also *not* automatically an Esri enterprise geodatabase — Chapter 7 explains the difference | [S09] |
| **GeoServer** | "A Java-based server that allows users to view and edit geospatial data", implementing OGC standards including WMS, WFS, WCS, and WMTS | **R3 service delivery** using open standards | A content portal with groups and roles as ArcGIS Online/Portal provide, desktop authoring, hosted managed storage (it *serves* data held elsewhere, for example in PostGIS) | [S08] |

**Figure 2.6 — Two arrangements of the same five responsibilities (schematic; products shown are examples, not a prescribed stack).**

```
 Responsibility        Esri arrangement (this chapter)            One open-source arrangement
 ─────────────────     ─────────────────────────────────          ───────────────────────────────────
 R1 Authoring          ArcGIS Pro                                 QGIS Desktop
 R2 Storage            File/enterprise geodatabase; hosted        PostgreSQL + PostGIS; files
                       layers (ArcGIS Online / Data Store)
 R3 Service delivery   ArcGIS Online hosted services;             GeoServer (WMS/WFS/WMTS…)
                       ArcGIS Server
 R4 Content org.       ArcGIS Online organisation;                No single equivalent in the three
                       Portal for ArcGIS                          products above — needs a catalogue/
                                                                  portal product and identity solution
 R5 Applications       Instant Apps, Experience Builder,          OpenLayers / MapLibre / Leaflet apps
                       Dashboards, Field Maps, JS SDK apps        written and maintained by the team
```

Read the right-hand column carefully. **R4 is the gap.** QGIS, PostGIS, and GeoServer together give you authoring, storage, and services, but none of them is a portal with items, groups, sharing levels, and member roles. Teams that go this route add a catalogue product, an identity provider, and a good deal of glue, or accept that "sharing" means file paths and server URLs handed around by hand. That is a legitimate choice for some organisations; it is not a free one, and it is exactly the kind of omission the five-responsibility model exists to expose.

**Common misconception.** "QGIS is free ArcGIS Pro, GeoServer is free ArcGIS Server, PostGIS is a free geodatabase." Consequence: an architecture is proposed with three products and one missing responsibility; or a data-model feature that a geodatabase enforces (Chapter 8) is assumed to exist in PostGIS by default and silently does not. Compare *roles*, then check *each* capability the requirement needs.

### 2.6.3 Exercise: shared GIS knowledge or platform-specific implementation?

For each task below, decide whether the *knowledge needed to do it well* is **shared GIS knowledge** (it transfers unchanged to any platform) or **platform-specific implementation** (you would have to relearn it on another product). Most tasks contain both; label the *dominant* part and name the *other* part in one phrase. The first row is worked for you; answers to the rest are in the Instructor Appendix.

| # | Task | Dominant | The other part |
| --- | --- | --- | --- |
| 0 (worked) | Decide that a ward should be stored as a polygon rather than a point | **Shared** — the choice depends on the question the data must answer (Chapters 1 and 3), not on the software | Platform-specific: the geometry type name and storage format in the chosen system |
| 1 | Publish the Requests dataset as a hosted feature layer and set its sharing level to a group | | |
| 2 | Decide that the authoritative copy of request data will be the service, and the original file will be archived | | |
| 3 | Write a browser page that draws the request points from a service and opens a pop-up on click | | |
| 4 | Explain why a resident's phone showing "In progress" and a manager's laptop showing "Closed" can both be non-authoritative | | |
| 5 | Choose between configuring Dashboards and building a custom monitoring page | | |

**Reserved for later phases.** The protocols behind the services (feature service REST calls, WMS/WFS request syntax, tile schemes) and production architecture (high availability, federation, security) are deliberately not covered here; naming them is enough for now.

**Comprehension check 2.6.** In one sentence each: which responsibility does OpenLayers cover, which does PostGIS cover, and which responsibility is *not* covered by QGIS + PostGIS + GeoServer without a further product?

---

## 2.7 Guided lab: trace a prepared solution

### 2.7.1 Objective, prerequisites, and what this lab is (and is not)

**Objective.** Given documented evidence about a deployed solution — a dataset, a project, portal items, and applications — identify where its **geometry**, **styling**, **access decisions**, and **application behaviour** are managed, produce an annotated architecture sketch, and produce a role comparison table. Where the evidence does not say, write **"not enough information"** and state what evidence would settle it.

**Prerequisites.** Modules 2.1–2.6 of this chapter; Chapter 1's fixture.

**Required software and access.** **None.** The lab is performed on the printed evidence pack below. No ArcGIS account is needed and **no publishing action is performed**; nothing in this lab consumes credits.

**What the evidence pack is.** The blueprint asks for "a screenshot set or supervised demonstration" with a documented source-to-output relationship. The author cannot capture screenshots from a deployment that has not been built, so the pack is written as *transcribed evidence*: the text an instructor would read off each screen, labelled by the screen it came from. Instructors who have an ArcGIS Online organisation should build Solution S-2 (Instructor Appendix I.6) and replace each evidence card with a real screenshot; the learner tasks do not change. **Everything below is synthetic.**

### 2.7.2 Evidence pack — Solution S-2, "Request Tracker (Training)"

**Evidence E1 — Directory listing of the source data package (as captured from a file browser).**

```
\\gis-files\training\
├── README_Municipal_Training.txt      (provenance note: "Synthetic training fixture. Planar grid,
│                                       metres. No Earth location. Build copy uses a documented
│                                       local coordinate reference assigned by the instructor — see
│                                       Instructor Appendix I.6. Owner: GIS training lead.")
└── Municipal_Training.gdb\            (file geodatabase)
      Wards      polygon   2 records   fields: WardID, WardName
      Roads      line      1 record    fields: RoadID, RoadName
      Requests   point     6 records   fields: RequestID, Category, Priority, Status,
                                               Reported, Closed, Channel, Note
      Assets     point     3 records   fields: AssetID, Type, Installed, LastInspected, Condition
```

**Evidence E2 — ArcGIS Pro project (as read from the Contents and Catalog panes and layer properties).**

```
Project file:  C:\Projects\RequestTracker\RequestTracker.aprx   (last saved 2026-09-15 by gis.author)

Map "Request Overview" — Contents pane, top to bottom:
   Requests   symbol: unique values on Status (Open = red, In progress = orange,
                       Reopened = purple, Resolved/Closed = grey)   labels: RequestID
   Assets     symbol: single symbol, small green circle
   Roads      symbol: dark grey line, 2 pt
   Wards      symbol: no fill, black outline
   (no basemap — the fixture has no Earth location)

Layer sources (Properties > Source):
   Requests → \\gis-files\training\Municipal_Training.gdb\Requests
   Assets   → \\gis-files\training\Municipal_Training.gdb\Assets
   Roads    → \\gis-files\training\Municipal_Training.gdb\Roads
   Wards    → \\gis-files\training\Municipal_Training.gdb\Wards

Catalog pane > Portals: signed in to the training ArcGIS Online organisation as gis.author
```

**Evidence E3 — ArcGIS Online item cards (as read from each item's details page). Sharing and settings as displayed on 2026-09-16.**

| Item title | Type | Owner | Sharing level | Notes shown on the item page |
| --- | --- | --- | --- | --- |
| `Requests_Training` | Hosted feature layer | gis.author | Group: *Inspection Team – Training* | Published from ArcGIS Pro on 2026-09-15 with **copy all data**. Editing: **enabled** — add, update (geometry and attributes), delete. Description: "Authoritative request records from 2026-09-15. The file geodatabase copy is archived and must not be edited." |
| `Requests_Training_public` | Hosted feature layer **view** | gis.author | Everyone (public) | View of `Requests_Training`. Editing: **disabled**. Fields excluded from the view: `Channel`, `Note`. |
| `Municipal_Base_Training` | Hosted feature layer (contains three layers: Wards, Roads, Assets) | gis.author | Group: *Inspection Team – Training* | Published from ArcGIS Pro on 2026-09-15 with copy all data. Editing: *(the editing settings panel was not captured)*. |
| `Request Overview (Training)` | Web map | gis.author | Group: *Inspection Team – Training* | Layers: Requests_Training (styled by Status; pop-up shows RequestID, Category, Status, Reported), Municipal_Base_Training (Wards, Roads, Assets). Basemap: none. |
| `Request Overview (Public)` | Web map | gis.author | Everyone (public) | Layers: Requests_Training_public (single symbol; pop-up shows RequestID, Category, Status). Basemap: none. |
| `Request Monitoring (Training)` | Dashboard | gis.author | Organization | Built on *Request Overview (Training)*. Elements: indicator "Open requests", list sorted by Reported, map. |
| `Request Locator (Training)` | Instant App | gis.author | Everyone (public) | Built on *Request Overview (Public)*. Tools enabled: search, legend, zoom. |
| *(Field Maps)* — *Request Overview (Training)* | Web map configured in Field Maps Designer | gis.author | (inherits the web map's sharing: group) | Form on Requests_Training: Status (choice list), Note (text). Offline: enabled. *(Whether the form also writes to a related inspection table was not captured.)* |

**Evidence E4 — Members and group (as read from the organisation's Members and Groups pages).**

| Member | User type | Role | Groups |
| --- | --- | --- | --- |
| gis.author | Creator | Publisher | Inspection Team – Training (owner) |
| crew01 | Mobile Worker | Data Editor | Inspection Team – Training |
| mgr.ops | Viewer | Viewer | Inspection Team – Training |
| intern01 | Viewer | Viewer | *(none)* |
| org.admin | Creator | Administrator | *(not captured)* |

**Evidence E5 — Application behaviour (as observed by the instructor on 2026-09-16 and transcribed).**

1. crew01 opens the Field Maps app on a phone, sees the *Request Overview (Training)* map, taps P2, changes Status from *In progress* to *Resolved* in the form, and submits. Thirty seconds later the dashboard's "Open requests" indicator, which counts Status in (*Open*, *In progress*, *Reopened*), drops by one.
2. An anonymous visitor opens *Request Locator (Training)* in a browser without signing in, sees six request points, clicks P2, and the pop-up shows Status *Resolved*. No *Note* or *Channel* appears in the pop-up and neither field is present in the layer's field list.
3. mgr.ops signs in and opens the dashboard; it loads and shows the indicator, list, and map. intern01 signs in and opens a link to *Request Overview (Training)*; the page reports that the item is not accessible. *(What intern01 sees when opening the dashboard link was not captured.)*
4. gis.author opens `RequestTracker.aprx` in ArcGIS Pro. The Requests layer there still shows P2 as *In progress*.

### 2.7.3 Ordered steps

1. **Read the whole pack once** before writing anything. Mark every place that says "not captured".
2. **Locate the authoritative request data.** Using E1, E3 (the `Requests_Training` description), and E5 item 4, write one sentence stating which copy of the request records is authoritative *according to the organisation*, and one sentence stating what evidence supports it.
3. **Explain E5 item 4.** Why does the ArcGIS Pro project still show P2 as *In progress*? Which module of this chapter predicted it?
4. **Fill in Table 2.7 (responsibility trace).** For each row, name the item or file where the thing is managed, cite the evidence card, and give your confidence (*shown*, *inferred*, or *not enough information*).

   **Table 2.7 — Where is it managed? (learner fills in)**

   | Thing to locate | Managed in (item / file) | Evidence | Confidence |
   | --- | --- | --- | --- |
   | The geometry of the six request points that the public app draws | | | |
   | The geometry of the request points that gis.author sees in ArcGIS Pro | | | |
   | The red colour of *Open* requests in the team's web map | | | |
   | The single symbol used in the public map | | | |
   | The decision that the public can see request locations | | | |
   | The decision that the public cannot see `Note` and `Channel` | | | |
   | The decision that crew01 may change a request's status | | | |
   | The decision that intern01 cannot open the team web map | | | |
   | Whether intern01 can use the dashboard | | | |
   | Whether crew01 may edit ward boundaries | | | |
   | The "Open requests" count logic | | | |
   | The choice list crew01 sees for Status on the phone | | | |
   | Whether the phone form also writes an inspection record | | | |
   | Which machines host `Requests_Training` and who patches them | | | |

5. **Draw the annotated architecture sketch.** Start from Figure 2.3's shape. Show: the archived file geodatabase, the ArcGIS Pro project (with a dashed arrow to the *archived* file, not to the hosted layer), the two hosted feature layer items and the view, the two web maps, the three apps, the group, and the four members. Annotate each arrow with *references* or *copy made on 2026-09-15*. Put a **?** on anything not established by the evidence.
6. **Build the role comparison table.** For each of the five responsibilities R1–R5, list (a) the S-2 component that covers it, (b) one non-Esri product from module 2.6 that covers the same responsibility, and (c) one thing that would have to be re-checked if S-2 were rebuilt on that product.
7. **Write the "not enough information" list.** For every row of Table 2.7 marked *not enough information*, state the single screen or setting an instructor would need to capture to resolve it.
8. **Self-check** against 2.7.4 before submitting.

### 2.7.4 Expected results and validation checks

The following invariants must hold in a correct submission. Detailed expected values for Table 2.7 are in the Instructor Appendix; the learner checks only these.

- **Exactly one item is named as holding the authoritative request data**, and it is a hosted item, not the `.aprx` and not the file geodatabase (E3 description; E5 item 4).
- **The sketch shows two copies of the request records** (archived file geodatabase and hosted layer) and **one** view that is *not* a copy (E3, [X07]).
- **The sketch shows no data inside any web map or app** (module 2.3.2, [X04]).
- **At least three rows of Table 2.7 are marked *not enough information***. The pack was written with deliberate gaps; a submission that resolves every row has guessed.
- **The public app's field visibility is attributed to the view**, not to the web map or the Instant App (E3; [X07]).
- **The dashboard count logic is attributed to the dashboard configuration**, not to the data (E3, E5 item 1; [X19]).
- **The role comparison table has five rows and names a re-check item in every row.** A blank column (c) means the learner has assumed one-to-one equivalence, which 2.6 rules out.

### 2.7.5 Troubleshooting

| Symptom | Likely cause | What to do |
| --- | --- | --- |
| "I can't tell whether the public map or the view hides the fields." | Conflating presentation (pop-up field list) with data exposure (fields excluded from the view) | Re-read E5 item 2: the fields are absent from the *layer's field list*, which is a view property [X07]; the pop-up configuration merely chooses among fields that exist |
| "The ArcGIS Pro project must be wrong because it shows old data." | Expecting a project to hold data (2.2.2) | The project references the archived file; it is *correct* and *stale* at the same time. Note it as a governance risk: an author could edit the wrong copy |
| "I marked everything as *shown*." | Reading confidence off the pack's tone rather than its content | Check each "not captured" note in E3/E4; each one should produce at least one *not enough information* |
| "I put OpenLayers as the replacement for ArcGIS Online." | Comparing brands, not roles (2.6.1) | OpenLayers covers R5 only; split ArcGIS Online's roles (R2, R3, R4, part of R5) into separate rows |
| "The dashboard should count P6 as open." | Misreading the count rule | E5 item 1 states the rule: Status in (Open, In progress, Reopened). P6 is *Closed – duplicate* and is excluded by the rule, whatever one thinks of the policy |

### 2.7.6 Required learner deliverables

1. **Annotated architecture sketch** (one page): components, arrows labelled *references* / *copy*, question marks on unknowns.
2. **Completed Table 2.7** with evidence citations and confidence.
3. **Role comparison table** (five rows, three columns).
4. **"Not enough information" list** with, for each entry, the screen or setting that would resolve it.
5. Two sentences answering step 2 and one paragraph answering step 3.

### 2.7.7 QGIS / open-source alternative to the lab (equivalent foundational exercise)

The lab needs no software, so it needs no alternative to *run*. What the blueprint asks for is a check that the *foundational exercise* transfers. It does, with one substantive difference that the learner must handle.

**Alternative evidence pack (described, synthetic; not execution-tested).** Replace E1–E3 as follows: the source data is a GeoPackage file `municipal_training.gpkg` on the same share; the project is `request_tracker.qgz`, whose layers reference the GeoPackage [X21]; the request table has been loaded into a PostgreSQL database with PostGIS [S09]; GeoServer publishes it as a WFS layer and the base layers as WMS [S08]; a browser page built with OpenLayers draws the WFS layer and opens a pop-up [S06]; there is no portal — access to GeoServer's layers is controlled in GeoServer's own security settings, and the "public" page is simply a URL on the municipal web server.

**What is the same.** Steps 2–7 apply unchanged: locate the authoritative copy (the PostGIS table, per the readme), explain why the `.qgz` project still shows old data if it references the GeoPackage, fill Table 2.7, draw the sketch, and list the gaps.

**What is different, and must be stated by the learner.** There is no R4 item catalogue: there are no "items", no "sharing level", no "group", and no "view" item. The row "the decision that the public cannot see `Note` and `Channel`" therefore has *no* single answer analogous to a hosted view — it might be a database view in PostGIS, a GeoServer layer configuration, or the OpenLayers page simply not displaying the fields (which would *not* hide them from the service). The correct learner response is to say which of these the evidence shows, and if none, "not enough information" — and to note that "the page doesn't show the field" is presentation, not access control. This difference is the lesson of 2.6.2 made concrete. An instructor running this variant must build and verify the actual stack before claiming any specific behaviour; nothing in this box has been executed.

---
## 2.8 Independent check and progression gate

This assessment is completed without the Instructor Appendix. Answer every item under the stated assumptions; if you believe an item is under-specified, say what is missing rather than guessing. Item-to-module mapping is shown so that you know what each item is testing.

### 2.8.1 Concept questions (six)

**Q1 (2.1; LO1).** A field inspector's tablet has the request map open and, because the site has no signal, is working from an offline copy taken this morning. Which of the following statements is correct under the definitions in this chapter? Choose one.
(a) The tablet now holds the authoritative request data until it reconnects.
(b) The tablet holds a copy; the authoritative data is wherever the organisation has designated it, and the tablet's copy may be stale.
(c) The tablet's copy is authoritative for the requests it contains and the server is authoritative for the rest.
(d) Authoritative data is a technical property of hosted layers, so the question does not apply to an offline copy.

**Q2 (2.2; LO2).** An `.aprx` file is copied to a machine that cannot reach the file share named in the layers' source paths. Choose the best description of what opens.
(a) Nothing; the project cannot open without its data.
(b) The project opens with its maps, layer list, and symbols, but the layers cannot draw because their data sources are unreachable.
(c) The project opens and draws the data from an embedded copy stored in the `.aprx`.
(d) The project opens and automatically downloads the data from ArcGIS Online.

**Q3 (2.3; LO3).** In an ArcGIS Online organisation, a web map `M` references hosted feature layer `L`, and a hosted feature layer view `V` is created from `L`. State, for each action, what happens to the *data*: (i) `M` is deleted; (ii) `V` is deleted; (iii) `L` is deleted. One phrase each.

**Q4 (2.4; LO4).** Which one of the following is a *difference in responsibility* between ArcGIS Online and ArcGIS Enterprise, rather than a difference in features?
(a) Enterprise has a portal with groups and items; ArcGIS Online does not.
(b) With Enterprise the organisation provides, upgrades, backs up, and monitors the infrastructure; with ArcGIS Online Esri does.
(c) ArcGIS Online supports web maps; Enterprise supports only map services.
(d) Enterprise cannot share content with ArcGIS Online.

**Q5 (2.5; LO5).** Match each task to the *single most appropriate* tool from {ArcGIS Maps SDK for JavaScript, ArcGIS API for Python, ArcPy}, and give a one-phrase reason: (i) list every item in the organisation shared with everyone; (ii) run a geoprocessing tool on a dataset in a file geodatabase from a scheduled script on a machine with ArcGIS Pro installed; (iii) add a custom drawing tool to a browser page that residents use.

**Q6 (2.6; LO6).** "We will replace ArcGIS Online with OpenLayers." Name the responsibility OpenLayers covers, then name two responsibilities of ArcGIS Online that this sentence leaves uncovered.

### 2.8.2 Scenario questions (two)

**S1 (2.3, 2.1; LO3).** A colleague sends you a link to a public web map from a neighbouring organisation showing pipe locations, and asks you to "add these to our field map so the crews can correct the pipe positions when they are wrong". Assume the link opens without signing in. Write a short reply (four to six sentences) that (a) separates what the link proves from what it does not, (b) names the two settings on the *other* organisation's layer that would have to be checked, (c) names the non-technical question that must be answered, and (d) says which copy of the pipe data would be authoritative if the crews' corrections were accepted.

**S2 (2.5, 2.6, 2.1; LO5, LO6).** A product manager writes: "We don't need any of this platform stuff. One developer can write the whole request system in a week with a mapping library and a database." Assume the requirement is: crews edit requests offline on phones; managers see live counts; the public sees locations but not notes; and the service must be restored within four hours if a server fails. Using the five responsibilities, explain in one paragraph why "a mapping library and a database" does not describe the complete stack, and list the responsibilities the plan leaves unassigned.

### 2.8.3 Independent practical task (unfamiliar inputs)

**Solution S-X, "School Transport Planner (Training)" — synthetic evidence, deliberately different from the lab.**

- *Data:* an enterprise geodatabase in a PostgreSQL database `schools_db` on server `db01` holds feature classes `Schools` (points), `BusRoutes` (lines), and `Catchments` (polygons). The readme names `schools_db` as authoritative.
- *Publishing:* ArcGIS Pro was used to publish all three as a map image layer and a feature layer to an **ArcGIS Enterprise** portal `portal.training.example` by **referencing registered data** on the federated ArcGIS Server `gis01`.
- *Content:* web map *Transport Overview* (portal, shared with group *Transport Planners*) styles `BusRoutes` by operator and `Catchments` with a yellow fill. A second web map *Transport Public* (shared with everyone) shows `Schools` only.
- *Apps:* an Experience Builder app *Route Planner* (group) with a custom widget that computes walking distance from a chosen address to the nearest school; an Instant App *Find My School* (public).
- *Operations:* on Monday, `gis01` failed; the portal stayed up but every layer in *Transport Overview* showed an error until `gis01` was restored by the operations team on Tuesday.

**Four requests arrive.** For each, state (1) which responsibility (R1–R5, or "infrastructure operation") it involves, (2) which component or item is changed, (3) whether the *data* changes, and (4) what evidence in the description supports your answer or whether there is not enough information.

1. "Move the boundary of catchment C-14 two streets north — it was digitised wrongly."
2. "Change the catchment fill from yellow to light blue in the planners' map only."
3. "When a planner clicks a route, the app should also show the number of pupils on it — that needs a different calculation from the current widget."
4. "The layers were down all Monday; make sure this cannot happen again."

Then answer in one paragraph: **why does the Experience Builder app's custom-widget code, on its own, not provide the data-management and operations stack that S-X needs?** Refer to what happened on Monday.

### 2.8.4 Oral explanation (one)

In no more than two minutes, without notes, describe the path of a single request record in Solution S-2 (module 2.7) from the archived file geodatabase to the public *Request Locator* app, naming each component it passes through or is referenced by, and stating which component holds the authoritative copy. Finish by naming **one thing you do not yet know** about how the solution works and what you would need to see to find out.

### 2.8.5 Submission requirements and scoring

**Submit:** written answers to Q1–Q6, S1–S2, the practical task (a table for the four requests plus the paragraph), and attend a short oral session. Lab deliverables from 2.7 are submitted separately and are a prerequisite for the gate.

**Scoring (100 points).**

| Component | Points | What earns the points |
| --- | --- | --- |
| Concept questions Q1–Q6 | 30 (5 each) | Correct answer *and* the stated reason (where asked) is consistent with the chapter's definitions |
| Scenario S1 | 12 | All four parts (a)–(d) addressed; visibility, editing, and usage rights kept separate; authoritative copy named |
| Scenario S2 | 13 | Responsibilities named correctly; the paragraph reasons from the requirement, not from preference |
| Practical task — four requests | 24 (6 each) | Responsibility, component, data-change flag, and evidence/insufficiency all correct; "not enough information" used where the description is silent |
| Practical task — paragraph | 11 | Explains the Monday failure in terms of infrastructure operation and data referencing, and distinguishes app code from the rest of the stack |
| Oral explanation | 10 | Complete path in order; authoritative copy correct; one genuine unknown stated with the evidence that would resolve it |

**Progression rule.** Pass at 80 points or more **and** no critical misconception. For this chapter the critical misconceptions are: (1) treating a project, map, app, or device as holding the authoritative data; (2) treating public visibility as edit or reuse permission; (3) presenting a single library or product as a complete replacement for all five responsibilities. Any of these triggers remediation and a fresh exercise (Instructor Appendix I.5) regardless of the aggregate score. A learner who cannot name a limit of their own understanding in the oral item has not met the blueprint's gate condition and should retake the oral item.

---

## 2.9 Media and source brief

The full media brief — slide outline, audio-lesson outline, and diagram specifications — is in the Media Appendix at the end of this document so that it can be updated independently of the teaching text. This module records the three requirements the blueprint places on it.

1. **One compact role diagram, then a product-to-role table.** The role diagram is Figure 2.1 (five responsibilities) and its product overlay is Figure 2.6; the product-to-role table is Table M2 in the Media Appendix. Every vendor capability statement in the table carries the check date **19 September 2026** and the documentation edition it was read from, because Esri's "latest" pages change without notice and Mapbox's product list is marketing content that is revised freely.
2. **Narration introduces each acronym and product once.** The audio outline (Media Appendix, section M.2) lists the first-mention script for every product and acronym — GIS, CRS (mentioned only as a forward reference), SDK, API, REST, OGC, WMS/WFS — and thereafter uses the short form. The audio is not a product catalogue: any product not needed to explain a responsibility is left out of the narration and appears only in the table.
3. **No 3D scene.** Nothing in this chapter is spatial in a way that a 3D scene would clarify; the concepts are architectural. A 2D role diagram and a static table teach better, as the blueprint anticipates. No interactive demonstration is proposed either; the lab's evidence pack is the interactive element.

**Core readings** for this chapter are the ArcGIS Pro, ArcGIS Online, ArcGIS Enterprise, and developer overview pages [S02]–[S05]. **Cross-platform orientation** readings are the QGIS introduction, OpenLayers, Mapbox, GeoServer, and PostGIS overviews [S01], [S06]–[S09]. The additional pages [X01]–[X21] were read to support specific claims and are listed in the reference list.

**Transition to Chapter 3.** You now know *where* data lives, *who* serves it, *who* organises it, and *who* uses it. You have not yet looked closely at *what* flows through those systems: the shapes, the records, and the difference between a stored dataset and its appearance in a map. Chapter 3 opens the datasets and inspects features, geometry, attributes, and layers.

---

## Glossary

Terms are listed in the order they first appear. Platform-specific terms are marked **[ArcGIS]**, **[QGIS]**, or **[Open source]**; unmarked terms are general.

| Term | Meaning in this chapter |
| --- | --- |
| **Responsibility (R1–R5)** | One of five jobs a complete GIS solution must cover: desktop authoring, data storage, service delivery, content organisation, user applications. A teaching model from this course, not a vendor term. |
| **Authoritative data** | The copy of a dataset the organisation has designated as the source of truth. A decision, not a technical property. |
| **Web service** | A network endpoint that returns geographic data, map images, or tiles on request. |
| **Publish** | Create a web service (and, in ArcGIS, an item) from a dataset. May copy the data to a managed store or reference it in place [X02]. |
| **Hosted layer** **[ArcGIS]** | A web layer whose data is stored and served by ArcGIS Online or an Enterprise data store; deleting it deletes the data [X03]. |
| **Referenced layer** **[ArcGIS]** | A web layer backed by a service whose data stays in its original registered location [X03] [X02]. |
| **Project (`.aprx`)** **[ArcGIS]** | An ArcGIS Pro file holding maps, scenes, layouts, and *connections* to data; it stores paths, not data [S02] [X01]. |
| **Project (`.qgz`/`.qgs`)** **[QGIS]** | The QGIS equivalent; also stores references, not data [X21]. |
| **Contents pane / Catalog pane** **[ArcGIS]** | ArcGIS Pro panels listing, respectively, the active view's layers and the project's items and connections [S02]. |
| **Layers panel / Browser panel** **[QGIS]** | The corresponding QGIS panels [S01] [X21]. |
| **Item** **[ArcGIS]** | A catalogued object in ArcGIS Online or a portal, with owner, type, metadata, and sharing level. |
| **Web layer** **[ArcGIS]** | An item exposing geographic data as a service — feature, tile, map image, imagery, scene, and other types [X03]. |
| **Hosted feature layer view** **[ArcGIS]** | A separate item that reads the same data as a source hosted feature layer with its own sharing, editing, and field settings; not a copy [X07]. |
| **Web map** **[ArcGIS]** | An item holding a basemap, layer references, styling, pop-ups, and extent; it references layers rather than storing data [X04]. |
| **Group** **[ArcGIS]** | A collection of items shared with a set of members; the owner controls membership and contribution [X08]. |
| **Member, user type, role** **[ArcGIS]** | A signed-in person; the user type and role together determine privileges [X09]. |
| **Sharing level** **[ArcGIS]** | Owner-only, organisation, group(s), or everyone [X05]. |
| **Credits** **[ArcGIS]** | ArcGIS Online's metering currency for specific transactions and storage; most everyday use does not consume them [X10]. |
| **Portal for ArcGIS, ArcGIS Server, ArcGIS Data Store, ArcGIS Web Adaptor** **[ArcGIS]** | Components of an ArcGIS Enterprise base deployment covering content organisation, service delivery and processing, managed storage, and web-server integration respectively [S04] [X11]. |
| **Hosting server** **[ArcGIS]** | The ArcGIS Server configured to host the portal's hosted layers [X11]. |
| **Configuration** | Arranging capabilities a product already supports, without writing application code. |
| **Instant Apps, Experience Builder, Dashboards, Field Maps** **[ArcGIS]** | Configurable builders for focused apps, composed web experiences, monitoring displays, and mobile field work respectively [X18] [X17] [X19] [X20]. |
| **ArcGIS Maps SDK for JavaScript** **[ArcGIS]** | Esri's browser SDK for 2D/3D web mapping applications [X12]. |
| **ArcGIS API for Python** **[ArcGIS]** | The `arcgis` Python package for portal administration, content management, and analysis over the network [X13]. |
| **ArcPy** **[ArcGIS]** | The Python site package for geoprocessing and map automation, available where ArcGIS Pro or ArcGIS Server is installed [X14] [X15] [X16]. |
| **REST API / service API** | The HTTP interfaces through which all clients read and write ArcGIS services and items [S05]. |
| **OpenLayers** **[Open source]** | A free, BSD-licensed JavaScript library for dynamic maps in web pages; a browser application library [S06]. |
| **Mapbox** | A vendor of mapping, navigation, search, and data products and SDKs [S07]. |
| **QGIS** **[Open source]** | A free desktop GIS application [S01]. |
| **PostGIS** **[Open source]** | An extension adding spatial storage, indexing, and functions to PostgreSQL [S09]. |
| **GeoServer** **[Open source]** | A Java-based server that serves geospatial data through OGC standards such as WMS, WFS, WCS, and WMTS [S08]. |
| **OGC** | The Open Geospatial Consortium, which publishes the open standards GeoServer implements [S08]. Named only; standards are taught later. |
| **Evidence pack** | This course's term for a documented set of screens, listings, and observations from a deployed solution, used in the lab. |

## Recap

1. A complete GIS solution covers five responsibilities — desktop authoring, data storage, service delivery, content organisation, and user applications — and the machine running an application is never, by itself, the system holding authoritative data.
2. ArcGIS Pro is the desktop authoring product. A project references datasets through connections; it does not contain them. Authoring the data and changing its appearance are different acts with different consequences.
3. ArcGIS Online is Esri-operated hosting, sharing, content management, and administration. Its items reference one another; usually only hosted layers hold data. Visibility, edit permission, and usage rights are three independent settings.
4. ArcGIS Enterprise is the organisation-operated system of components — Portal, Server, Data Store, Web Adaptor — with the same content concepts but very different operational responsibilities. It is not ArcGIS Online installed locally.
5. Configurable builders arrange supported capabilities; the JavaScript SDK, the Python API, and ArcPy cover the browser, the platform, and the desktop/server tool respectively, all over the same services. Choose configure, extend, or build from the requirement.
6. OpenLayers, Mapbox, QGIS, PostGIS, and GeoServer each cover part of the picture. Compare roles, notice the gaps — especially content organisation — and label which of your knowledge is shared GIS understanding and which is platform-specific implementation.

## Cross-references to later chapters

- Storage formats, file and enterprise geodatabases, and why PostGIS is not automatically an Esri geodatabase: **Chapter 7**.
- Coordinate references, which the S-2 build notes had to assign before publishing: **Chapters 5–6**.
- Data models, identifiers, and the relationship class behind the "related inspection table" in Field Maps: **Chapter 8**.
- Editing workflows and what "save edits" means in each product: **Chapter 9**.
- Symbology and presentation choices previewed in 2.2.3 and E2: **Chapter 12**.
- Service protocols, federation, security, backups, and upgrades: the later Enterprise track, outside Phase 1.

---

## References

All pages were opened and read by the author on **19 September 2026**. IDs [S01]–[S09] are the blueprint's reference register entries used by this chapter; [X01]–[X21] are additional official pages consulted to support specific claims. Esri "latest" pages and vendor home pages change without notice; re-check before reuse.

| ID | Publisher | Page title | URL | Accessed |
| --- | --- | --- | --- | --- |
| S01 | QGIS Project | A Gentle Introduction to GIS — Introducing GIS (3.44) | https://docs.qgis.org/3.44/en/docs/gentle_gis_introduction/introducing_gis.html | 2026-09-19 |
| S02 | Esri | ArcGIS Pro — Get started (Introducing ArcGIS Pro); the blueprint URL `pro.arcgis.com/...get-started.htm` redirected permanently to this page | https://doc.esri.com/en/arcgis-pro/latest/get-started/get-started.html | 2026-09-19 |
| S03 | Esri | ArcGIS Online — What is ArcGIS Online? | https://doc.arcgis.com/en/arcgis-online/get-started/what-is-agol.htm | 2026-09-19 |
| S04 | Esri | ArcGIS Enterprise — What is ArcGIS Enterprise? | https://doc.esri.com/en/arcgis-enterprise/latest/introduction/what-is-arcgis-enterprise-.html | 2026-09-19 |
| S05 | Esri | ArcGIS Developers — Documentation home | https://developers.arcgis.com/documentation/ | 2026-09-19 |
| S06 | OpenLayers | OpenLayers — home page and overview | https://openlayers.org/ | 2026-09-19 |
| S07 | Mapbox | Mapbox — home page (product overview) | https://www.mapbox.com/ | 2026-09-19 |
| S08 | GeoServer / OSGeo | GeoServer — About | https://geoserver.org/about/ | 2026-09-19 |
| S09 | PostGIS Project | PostGIS — home page | https://postgis.net/ | 2026-09-19 |
| X01 | Esri | ArcGIS Pro — What is a project? | https://doc.esri.com/en/arcgis-pro/latest/help/projects/what-is-a-project.html | 2026-09-19 |
| X02 | Esri | ArcGIS Pro — Introduction to sharing web layers | https://doc.esri.com/en/arcgis-pro/latest/help/sharing/overview/introduction-to-sharing-web-layers.html | 2026-09-19 |
| X03 | Esri | ArcGIS Online — Layers | https://doc.arcgis.com/en/arcgis-online/reference/layers.htm | 2026-09-19 |
| X04 | Esri | ArcGIS Online — What is a web map? | https://doc.arcgis.com/en/arcgis-online/reference/what-is-web-map.htm | 2026-09-19 |
| X05 | Esri | ArcGIS Online — Share items | https://doc.arcgis.com/en/arcgis-online/share-maps/share-items.htm | 2026-09-19 |
| X06 | Esri | ArcGIS Online — Manage hosted feature layer editing | https://doc.arcgis.com/en/arcgis-online/manage-data/manage-editing-hfl.htm | 2026-09-19 |
| X07 | Esri | ArcGIS Online — Create hosted feature layer views | https://doc.arcgis.com/en/arcgis-online/manage-data/create-hosted-views.htm | 2026-09-19 |
| X08 | Esri | ArcGIS Online — Groups | https://doc.arcgis.com/en/arcgis-online/share-maps/groups.htm | 2026-09-19 |
| X09 | Esri | ArcGIS Online — Member roles | https://doc.arcgis.com/en/arcgis-online/administer/member-roles.htm | 2026-09-19 |
| X10 | Esri | ArcGIS Online — Understand credits | https://doc.arcgis.com/en/arcgis-online/administer/credits.htm | 2026-09-19 |
| X11 | Esri | ArcGIS Enterprise 11.4 — Base ArcGIS Enterprise deployment (the "latest" edition of this page was not reachable on the check date) | https://enterprise.arcgis.com/en/get-started/11.4/windows/base-arcgis-enterprise-deployment.htm | 2026-09-19 |
| X12 | Esri | ArcGIS Maps SDK for JavaScript — Overview (version 5.1) | https://developers.arcgis.com/javascript/latest/ | 2026-09-19 |
| X13 | Esri | ArcGIS API for Python — Overview (version 2.4.3) | https://developers.arcgis.com/python/latest/ | 2026-09-19 |
| X14 | Esri | ArcGIS Pro — What is ArcPy? | https://doc.esri.com/en/arcgis-pro/latest/arcpy/get-started/what-is-arcpy-.html | 2026-09-19 |
| X15 | Esri | ArcGIS Pro — Python in ArcGIS Pro (installation and conda) | https://doc.esri.com/en/arcgis-pro/latest/arcpy/get-started/installing-python-for-arcgis-pro.html | 2026-09-19 |
| X16 | Esri | ArcGIS Server 11.0 — ArcGIS Server and ArcPy | https://enterprise.arcgis.com/en/server/11.0/develop/windows/scripting-service-publishing-with-arcpy.htm | 2026-09-19 |
| X17 | Esri | ArcGIS Experience Builder — What is ArcGIS Experience Builder? | https://doc.arcgis.com/en/experience-builder/latest/get-started/what-is-arcgis-experience-builder.htm | 2026-09-19 |
| X18 | Esri | ArcGIS Instant Apps — Introduction to ArcGIS Instant Apps | https://doc.arcgis.com/en/instant-apps/latest/get-started/about-instant-apps.htm | 2026-09-19 |
| X19 | Esri | ArcGIS Dashboards — What is a dashboard? | https://doc.arcgis.com/en/dashboards/latest/get-started/what-is-a-dashboard.htm | 2026-09-19 |
| X20 | Esri | ArcGIS Field Maps — Get started with ArcGIS Field Maps | https://doc.arcgis.com/en/field-maps/get-started/get-started.htm | 2026-09-19 |
| X21 | QGIS Project | QGIS Desktop 3.40 User Guide — Project files | https://docs.qgis.org/3.40/en/docs/user_manual/introduction/project_files.html | 2026-09-19 |

---

# Instructor Appendix (separate from learner material)

Learners should not read this appendix before completing the 2.8 gate.

## I.1 Answers and reasoning — comprehension checks and in-module exercises

**Check 2.1.** The dashboard covers R5 (user application); it holds configuration, not data. At least R2 (storage, where the authoritative Assets copy lives) and R3 (the service the dashboard reads) must exist elsewhere; R4 (the item catalogue that the dashboard and its map are registered in) is also required in an ArcGIS deployment. To locate the authoritative copy you need the *layer item's* details (hosted or referenced; if referenced, the registered data source) and the organisation's statement of which copy is authoritative — a screenshot of the dashboard establishes neither.

**Exercise 2.2.3.** (a) Authoring — every consumer sees the corrected year; there should be an audit trail. (b) Presentation — the wards still exist; anyone else's map is unaffected. (c) Presentation — stored in this map; other maps keep their symbols. (d) Authoring — the road is now two records; any join, ID reference, or length calculation elsewhere changes. (e) Presentation — a definition filter hides records in this layer only; the data is untouched (Chapter 1's hide-versus-delete rule).

**Check 2.2.** Contains: maps, scenes, layouts, layer lists, symbols and other presentation settings, and *connections* (paths) to data [X01]. Does not contain: the datasets themselves, nor edits to them. First check: the layer's Source path and whether it is reachable.

**Check 2.3.** (1) Is the layer *editable* by anyone other than its owner — read from the layer item's editing settings; editing is off by default and public editing needs explicit approval [X06]. (2) Are we *permitted* to edit or republish it — a terms-of-use and licence question, read from the item's description and terms [S03]. (3) Which copy would be *authoritative* after inspectors edit — an organisational decision, because editing another organisation's layer in place (if even allowed) makes *their* layer authoritative, whereas copying it makes ours a fork. (Learners may also mention that the item is a *web map* and that the editable thing is the *layer* it references — accept this as a fourth point.)

**Check 2.4.** ArcGIS Online is a service Esri operates; there is nothing to install. The organisation-operated product is ArcGIS Enterprise, and deploying it means the organisation takes on, for example, installation and upgrades, backups, certificates and security, and monitoring and restoring servers [S04].

**Check 2.5.** Question 1: "Which stated requirement does an Instant App template fail to meet?" — evidence: the requirement list matched line by line against the template's documented tools on the current release [X18]. Question 2: "Who will own and upgrade the custom application in two years, and has that cost been accepted?" — evidence: a named owner and an operational plan. "Looks too simple" is developer preference and does not answer either.

**Exercise 2.6.3.** (1) Platform-specific (the publish dialog, hosted-layer concept, and sharing UI are Esri's); shared: the *decision* about who should see the data. (2) Shared (an authoritative-copy decision applies to any platform); platform-specific: how the archive is enforced in the chosen storage. (3) Platform-specific (the SDK or library API); shared: the concept that the page is a client of a service and holds no data. (4) Shared entirely; the platform-specific part is only *how* to check the service's current value. (5) Shared (the configure/extend/build reasoning); platform-specific: what Dashboards actually supports on the current release.

**Check 2.6.** OpenLayers: R5, browser application. PostGIS: R2, storage with spatial capability. Not covered by QGIS + PostGIS + GeoServer: R4, content organisation (items, sharing, groups, member roles).

## I.2 Answer key — 2.8 concept and scenario questions

**Q1: (b).** (a) confuses a copy with a designation; (c) invents a split that no organisation would make; (d) misstates "authoritative" as a technical property.

**Q2: (b)** [X01]. (a) — the `.aprx` is self-contained and opens; (c) — the project stores paths, not data; (d) — there is no such automatic behaviour, and the sources are file paths, not portal items.

**Q3.** (i) Nothing happens to the data; only the map's configuration is removed [X04]. (ii) Nothing happens to the data; the view holds no copy [X07]. (iii) The data is deleted [X03], and every map and view that referenced `L` is broken.

**Q4: (b)** [S04] [S03]. (a) is false — ArcGIS Online has groups and items; (c) is false; (d) is false — Esri states that Enterprise connects with ArcGIS Online to share content [S04].

**Q5.** (i) ArcGIS API for Python — portal content inventory over the network [X13]. (ii) ArcPy — geoprocessing tools where ArcGIS Pro is installed [X14] [X15]. (iii) ArcGIS Maps SDK for JavaScript — browser UI [X12]. Accept ArcGIS Server plus ArcPy for (ii) if the learner says the script runs on a server machine [X16].

**Q6.** OpenLayers covers R5 (browser application) [S06]. Uncovered: any two of R2 (hosted storage), R3 (service delivery), R4 (content organisation — items, sharing, groups, members). Reject answers that name OpenLayers as covering storage or serving.

**S1 model answer (key points).** The link proves the *web map* is shared with everyone and that its layers are at least visible to the public; it proves nothing about editing or permission to reuse (2.3.3). The two layer settings to check are the sharing level of the *layer* (as distinct from the map) and its *editing* settings (off by default; public editing requires explicit approval) [X05] [X06]. The non-technical question is whether the owning organisation's terms of use permit our use, and whether it *wants* our corrections. If corrections were accepted in place, *their* layer would be authoritative for pipe positions; if we copied the data, our copy would become a fork that diverges from theirs — which is the 2.1.3 problem.

**S2 model answer (key points).** "A mapping library and a database" names R5 (partly — a browser page, not an offline phone app) and R2. Unassigned: R1 (who authors and corrects data with proper tools), R3 (a service with access rules feeding phones, managers, and the public), R4 (who may see notes versus locations; groups and roles), the offline mobile application (a second R5), and the *operation* of whatever servers are implied by the four-hour restore requirement. The "one week" estimate is a claim about R5 and R2 only. Full marks require the answer to reason from the listed requirement (offline, live counts, field hiding, restore time), not from a general preference for platforms.

**Practical task S-X — expected results.**

| Request | Responsibility | Component or item changed | Data changes? | Evidence / sufficiency |
| --- | --- | --- | --- | --- |
| 1. Move catchment C-14's boundary | R1 desktop authoring (an edit to source geometry) | The `Catchments` feature class in `schools_db` — *not* the portal item, because the layers **reference** registered data | **Yes** | Shown: the readme names `schools_db` authoritative and publishing referenced registered data. Learners who say "edit the hosted layer" have missed that nothing is hosted here. A note that the edit tool and any edit permissions on the database are not described is welcome |
| 2. Change catchment fill to light blue in the planners' map only | R4/R5 content configuration (presentation) | Web map *Transport Overview* | **No** | Shown: styling lives in the web map [X04]; *Transport Public* does not show catchments at all, so "only" is automatically satisfied — a learner should notice this |
| 3. Show pupil count on route click, needing a different calculation | R5, custom development (extend or build) | The Experience Builder app's custom widget, and possibly a new service if the calculation needs data not in `BusRoutes` | **Not by itself** — unless a pupil count must be *stored*, which the request does not say | **Not enough information** on where pupil numbers come from; the learner should state that a data source for pupils is not in the description and must be identified before choosing extend versus build |
| 4. Layers were down all Monday; prevent recurrence | Infrastructure operation (the later Enterprise track) | The ArcGIS Server `gis01` and its availability arrangements; not any item | **No** | Shown: the portal stayed up and only the referenced layers failed, so the failure was the federated server, not the portal or the data; a durable fix (redundancy, monitoring, restore procedures) is operations work, not configuration [S04] |

*Paragraph — key points.* The custom widget is R5 code running in a browser: it draws and computes over what a service returns. On Monday the widget's code was unchanged and correct, yet every layer failed, because the *service* (R3, on `gis01`) was down; the data (R2, in `schools_db`) was untouched and the catalogue (R4, the portal) stayed up. Nothing in the widget's repository describes who owns the geodatabase, how the server is restored, or who may edit catchments. Full marks require the learner to name at least R2, R3, and infrastructure operation as things the app code does not provide, and to use the Monday event as evidence.

**Oral explanation — expected path.** Archived file geodatabase `Municipal_Training.gdb\Requests` (source at publish time) → published with copy-all-data on 2026-09-15 to hosted feature layer `Requests_Training` (**authoritative**) → hosted feature layer view `Requests_Training_public` (same data, `Note` and `Channel` excluded, read-only, shared with everyone) → web map *Request Overview (Public)* (single symbol, pop-up) → Instant App *Request Locator (Training)*. Acceptable "one thing I do not know" answers include any of the three deliberate unknowns in the pack or a genuine new one (for example, how often the dashboard refreshes, or whether the view is affected by deleting the source). Reject an answer that names the `.aprx`, the file geodatabase, or the phone as authoritative, and an answer that claims to know everything.

## I.3 Expected results — 2.7 lab (Table 2.7 and deliverables)

**Step 2.** Authoritative copy: hosted feature layer `Requests_Training` (E3 description: "Authoritative request records from 2026-09-15. The file geodatabase copy is archived"). Evidence: the item description and readme, plus E5 item 4 showing the file copy is stale. Note for the instructor: this is an *organisational designation*; the learner should say so.

**Step 3.** The project references the *file geodatabase* (E2 source paths), which was archived when the hosted copy was published with "copy all data" (E3). Edits made through the field app go to the hosted layer, not the file. The project is correct about what it references and stale relative to the authoritative copy. Module 2.2.2 (project references data) and 2.1.3 (copies and authority) predicted it.

**Table 2.7 — expected entries.**

| Thing to locate | Managed in | Evidence | Confidence |
| --- | --- | --- | --- |
| Geometry the public app draws | Hosted feature layer `Requests_Training` (read through the view `Requests_Training_public`) | E3; [X07] | Shown |
| Geometry gis.author sees in ArcGIS Pro | Archived file geodatabase `Municipal_Training.gdb\Requests` | E2 sources; E5 item 4 | Shown |
| Red colour of *Open* in the team map | Web map *Request Overview (Training)* | E3 | Shown |
| Single symbol in the public map | Web map *Request Overview (Public)* | E3 | Shown |
| Public can see request locations | Sharing level *Everyone* on the view (and on the public map and Instant App) | E3; [X05] | Shown |
| Public cannot see `Note`/`Channel` | Fields excluded on the view item | E3; E5 item 2; [X07] | Shown |
| crew01 may change status | Editing enabled on `Requests_Training` + crew01's Data Editor role + group membership | E3, E4; [X06] [X09] | Shown (accept "inferred" if the learner notes that the exact privilege behind the role was not captured) |
| intern01 cannot open the team web map | Web map shared with the group; intern01 not in the group | E3, E4, E5 item 3 | Shown |
| Whether intern01 can use the dashboard | — | Dashboard is shared with the organisation, but it references a group-shared map and layers; E5 says this was not captured | **Not enough information** — resolve by having intern01 open the dashboard in the live organisation and recording the result. *Instructor note: do not tell learners the outcome from memory; ArcGIS Online's behaviour when an app is more widely shared than the items it references must be verified in the organisation used for training.* |
| Whether crew01 may edit ward boundaries | — | `Municipal_Base_Training` editing settings not captured | **Not enough information** — resolve by capturing that layer's editing settings |
| "Open requests" count logic | Dashboard *Request Monitoring (Training)* indicator configuration | E3, E5 item 1; [X19] | Shown |
| Status choice list on the phone | Field Maps form configured in Field Maps Designer on the web map | E3 (Field Maps row); [X20] | Shown (accept a note that whether the list comes from the form or from a domain on the layer is a Chapter 8 question) |
| Whether the form writes an inspection record | — | E3 "not captured" | **Not enough information** — resolve by capturing the form's related-table configuration |
| Which machines host the layer and who patches them | Esri-operated ArcGIS Online infrastructure | S03; module 2.3.1 | Inferred from the platform — accept "shown" only if the learner cites the Catalog pane portal sign-in (E2) as evidence that this is an ArcGIS Online organisation |

**Sketch checks.** Two request copies (file, hosted); one view marked "no copy"; dashed arrow from the project to the *file*; no data inside maps or apps; question marks on the three unknown rows.

**Role comparison table — acceptable answers.** R1: ArcGIS Pro / QGIS — re-check editing tool behaviour and project-file handling. R2: hosted layer in ArcGIS Online / PostGIS — re-check that the field-exclusion requirement can be met (a database view, not a hosted view). R3: ArcGIS Online hosted service / GeoServer — re-check offline synchronisation support for phones, which the open-source stack does not provide by itself. R4: ArcGIS Online organisation / *no direct equivalent* — re-check how groups, sharing levels, and roles would be provided. R5: Instant App, Dashboards, Field Maps / OpenLayers page — re-check that an offline mobile app exists at all.

## I.4 Common mistakes and remediation

| Mistake | Signal | Remediation |
| --- | --- | --- |
| Treating the `.aprx` or the phone as the data (critical misconception 1) | Step 2 names the project or device; sketch puts data inside a map or app | Re-teach 2.1.3 and 2.2.2 with Table 2.2; give the fresh exercise in I.5 |
| Visible = editable = reusable (critical misconception 2) | S1 answer skips editing settings or terms of use | Re-teach 2.3.3 with the three-independent-facts framing; require a rewritten S1 |
| One product = whole stack (critical misconception 3) | Q6 or S2 names OpenLayers/QGIS as a replacement for ArcGIS Online | Re-teach 2.6 with Figure 2.6 and the R4 gap; require the learner to fill the right-hand column themselves |
| Resolving every Table 2.7 row | No *not enough information* entries | Point to each "not captured" note; explain that guessing is scored as an error |
| Attributing field hiding to the pop-up | Row "public cannot see Note/Channel" → web map | Re-read E5 item 2; distinguish the layer's field list (view) from pop-up field selection (map) |
| Confusing ArcPy with the Python API | Q5 answers swapped | Re-teach 2.5.2 table; emphasise where each runs and what it talks to |

## I.5 Fresh exercise for retesting (different values, same concepts)

Provide a new evidence pack for a fictional *Street Tree Inventory* solution: a tree dataset in an enterprise geodatabase, published to an **Enterprise** portal by *referencing* registered data; one web map for arborists (group) and one public map; a Dashboards item shared with the organisation; a Field Maps configuration; a member list with one person outside the group. Include two "not captured" notes (the referenced data source's server name; the public map's layer sharing). Ask for the same five deliverables. Key differences the learner must catch: with a referenced layer, the authoritative copy is the geodatabase, *not* the portal item, and deleting the portal item does not delete the data — the reverse of S-2. Keep difficulty comparable by keeping the number of items and unknowns the same.

## I.6 Building Solution S-2 for a live demonstration — not execution-tested

If you have an ArcGIS Online organisation with a Publisher-role account, you can build S-2 so that the evidence pack becomes real screenshots. The steps are written from the cited documentation and **have not been executed by the author**; record what you actually observe and update the pack accordingly.

1. **Prepare the fixture data.** Create a file geodatabase containing `Wards`, `Roads`, `Requests`, and `Assets` with the Chapter 1 fixture values. **Verification item:** hosted layers require a defined spatial reference, and the planar fixture deliberately has none. Assign a documented local/engineering coordinate reference to *this build copy only*, record it in the readme, and never present it as an Earth location; check the analyzer messages ArcGIS Pro produces for your chosen reference during sharing, and note them in the change log. (Coordinate references are taught in Chapter 5; learners in Chapter 2 do not need to interpret this choice.)
2. **Author the project.** In ArcGIS Pro 3.7, create `RequestTracker.aprx`, add the four datasets to a map, style Requests by Status as in E2, and record the layer source paths.
3. **Publish.** Share Requests as a hosted feature layer with *copy all data* [X02], and the other three as a second hosted feature layer. Confirm the privileges the account needs [X02] before starting; if publishing fails, capture the message rather than working around it.
4. **Create the view.** From `Requests_Training`, create a hosted feature layer view, exclude `Channel` and `Note`, leave editing disabled, share with everyone [X07] [X05]. **Verification item:** sharing a view publicly may prompt about the source layer or feature storage; record the prompt.
5. **Enable editing** on `Requests_Training` (add, update, delete) and keep it shared with the group only [X06].
6. **Configure the web maps, dashboard, Instant App, and Field Maps form** as in E3, using the documentation for each product [X04] [X19] [X18] [X20]. Record the exact tools and settings chosen.
7. **Create the members and group** as in E4 with the user types and roles shown [X09] [X08]; adjust if your organisation's licensing does not offer those user types, and update E4 to match.
8. **Reproduce E5** and capture screenshots. In particular, test what intern01 sees when opening the dashboard link, and *then* decide whether to leave that row as a deliberate unknown for learners or to capture it.
9. **Credits.** Storing features in hosted layers consumes credits [X10]; the amounts for this tiny fixture are small but not zero. Check the current credit table before building and note the observed consumption.

## I.7 Blueprint corrections, source notes, and verification items

- **No factual error was found in the Chapter 2 blueprint text.** Its product positioning statements agree with the official pages read.
- **S02 URL.** The blueprint's link `https://pro.arcgis.com/en/pro-app/latest/get-started/get-started.htm` now redirects permanently (HTTP 301) to `https://doc.esri.com/en/arcgis-pro/latest/get-started/get-started.html`. Content is unchanged in substance; the reference list records the destination.
- **Enterprise base-deployment page.** The "latest" edition at `enterprise.arcgis.com/en/get-started/latest/windows/base-arcgis-enterprise-deployment.htm` could not be fetched on the check date (connection reset); the 11.4 edition was read instead [X11]. Before any hands-on Enterprise session, reread the page for the installed release.
- **Retired product.** Esri's developer documentation lists ArcGIS Web AppBuilder as retired [S05]; the chapter says so and teaches Experience Builder and Instant Apps instead.
- **Field Maps and Instant Apps documentation URLs.** The versioned "what-is" URLs guessed from Esri's usual pattern returned 404; the pages actually used are [X20] and [X18].
- **GeoServer licence.** The About page states GeoServer is free software built on OGC standards [S08]; the chapter deliberately does not state a specific software licence name because the page's licence statement seen on the check date referred to the site content. Verify on the project's licence page if a licence claim is needed.
- **Unverified behaviours flagged in the text:** the ArcGIS Pro attribute-table context-menu label and Source property location (2.2.1); the ArcGIS Pro prompts for unsaved data edits (2.2.2); what a member sees when an app is shared more widely than the items it references (I.3); hosted-layer publishing with a locally assigned coordinate reference (I.6); every step of I.6.

## I.8 Instructor change log

| Date | Change | Affected sections |
| --- | --- | --- |
| 2026-09-19 | Initial release, revision 1.0. No execution of software procedures. | All |

---

# Media Appendix

## M.1 Slide outline (mapped to module IDs)

| Slide group | Module | Slides | Learning objective of the sequence | Question to pose before revealing |
| --- | --- | --- | --- | --- |
| A. Where can each step happen? | Recap / 2.1 | 1. Chapter 1's workflow with blank "where?" boxes; 2. the five responsibilities (Figure 2.1 without products); 3. the asset dataset traced through them; 4. "the phone is not the database" | LO1 | "Which of these five must exist for a dashboard to show a number?" |
| B. ArcGIS Pro | 2.2 | 5. Role and vocabulary table; 6. Table 2.2 — inside/outside the `.aprx`; 7. the emailed-project example; 8. authoring versus appearance table | LO2 | "You email the `.aprx`. What does your colleague see?" |
| C. ArcGIS Online | 2.3 | 9. Four capabilities; 10. item types table; 11. Figure 2.3 with "only one box holds data" highlighted; 12. visible ≠ editable ≠ reusable | LO3 | "Which box holds data?" (reveal: one) |
| D. ArcGIS Enterprise | 2.4 | 13. Online-versus-Enterprise responsibility table; 14. four components and roles; 15. use-versus-operate table | LO4 | "Which of these four causes needs a server operator?" (the dashboard example) |
| E. Apps, APIs, SDKs | 2.5 | 16. Builders table with municipal examples; 17. JS SDK / Python API / ArcPy roles; 18. Table 2.5 decision worksheet; 19. three mini-cases | LO5 | "Configure, extend, or build?" per case, before the reasoning |
| F. Transferable roles | 2.6 | 20. OpenLayers and Mapbox positioned; 21. QGIS/PostGIS/GeoServer table; 22. Figure 2.6 with the R4 gap highlighted; 23. shared-versus-platform-specific exercise | LO6 | "Which responsibility is missing on the right?" |
| G. Lab briefing | 2.7 | 24. What an evidence pack is; 25. the five deliverables; 26. "not enough information" is a valid answer | LO7 | — |
| H. Gate | 2.8 | 27. Assessment structure and critical misconceptions | — | — |

Speaker notes for each slide should be drawn from the corresponding module text; do not add capability claims that are not in the chapter.

## M.2 Audio-lesson outline

**Rules for the narration.** Introduce each product and acronym exactly once, in the order below, then use the short form. Describe every diagram in words: name the boxes from left to right and top to bottom, and say what each arrow means. Never say "this box" or "the red one". Pause for prediction before each answer.

| Segment | Duration guide | Content | First mentions (script) |
| --- | --- | --- | --- |
| 1. Recap and the question "where?" | 3 min | Chapter 1's workflow; the five responsibilities named and defined one at a time | "GIS — geographic information system" (repeat from Chapter 1 once) |
| 2. Tracing one dataset | 5 min | Walk through Figure 2.1: "On the left, a person at a desktop edits the assets dataset. Below that, the authoritative copy sits in storage. To the right, a web service exposes it. Beneath, a catalogue item and a group. Finally, on the right, a phone and a browser." Then the three counter-examples | "web service — an address on the network that returns data or map images on request" |
| 3. The phone is not the database | 3 min | The stale-status worked example; pause: "which screen is right?" | "authoritative — the copy the organisation has designated as the source of truth" |
| 4. ArcGIS Pro | 5 min | Role; project, map, layout, table, Contents pane, Catalog pane; Table 2.2 read aloud as two lists; the emailed-project example | "ArcGIS Pro — Esri's desktop GIS application"; "a-p-r-x, the project file" |
| 5. ArcGIS Online | 7 min | Four capabilities; item types; Figure 2.3 described: "Top left, a hosted feature layer — this is the only box that holds data. To its right, a web map that references it. Further right, a dashboard that references the map…"; visible ≠ editable ≠ reusable | "ArcGIS Online — Esri's cloud-based mapping and analysis service"; "item"; "web map"; "hosted feature layer"; "view"; "group"; "credits" |
| 6. ArcGIS Enterprise | 5 min | Responsibility table; the four components by role; use versus operate; the four-cause dashboard example with a pause before each classification | "ArcGIS Enterprise — the version the organisation deploys and operates"; "Portal for ArcGIS"; "ArcGIS Server"; "ArcGIS Data Store"; "Web Adaptor" |
| 7. Configure, extend, build | 7 min | Builders with municipal examples; JS SDK / Python API / ArcPy roles; the worksheet; three cases with pauses | "SDK — software development kit"; "API — application programming interface"; "REST — the style of web interface these services use"; "Instant Apps"; "Experience Builder"; "Dashboards"; "Field Maps"; "ArcGIS Maps SDK for JavaScript"; "ArcGIS API for Python"; "ArcPy" |
| 8. Other platforms | 5 min | OpenLayers, Mapbox; QGIS, PostGIS, GeoServer; Figure 2.6 read column by column; "the missing row is content organisation" | "OpenLayers"; "Mapbox"; "QGIS"; "PostGIS — an extension to PostgreSQL"; "GeoServer"; "OGC — the Open Geospatial Consortium"; "WMS and WFS — two of its service standards, named only" |
| 9. Lab and gate briefing | 3 min | What to do with the evidence pack; the five deliverables; "not enough information" | — |

Products not needed to explain a responsibility (native SDKs, Leaflet, MapLibre, CesiumJS, Survey123, and the long list on Esri's developer page) are **not** narrated; they appear only in Table M2.

## M.3 Diagram specifications

**D1 — Five responsibilities (Figure 2.1).** Five labelled boxes in a two-row arrangement: R1 Desktop authoring, R2 Data storage, R3 Service delivery on the top row; R4 Content organisation, R5 User applications on the bottom row. Arrows: R1→R2 "reads and writes", R2→R3 "served from", R3→R4 "registered as an item", R4→R5 "referenced by", plus a dashed arrow R5→R3 "requests data at run time". Labels only; no product names; caption "schematic — not every solution uses every box".

**D2 — Item references (Figure 2.3).** Boxes exactly as in the figure; the hosted feature layer box is the only one with a filled background and the caption "DATA". The view box is outlined with a dashed border and captioned "no copy". All other boxes are white with the caption "configuration only". Arrows are labelled "references" and point *from* the referencing item *to* the referenced item. Member and group appear as small tags at the top ("owner", "shared with").

**D3 — Online versus Enterprise (Table in 2.4.1 as a graphic).** Two columns of identical content boxes (items, groups, members, web maps) above two *different* infrastructure bands: left band labelled "operated by Esri", right band labelled "operated by your organisation: install, upgrade, back up, monitor, restore". The point of the graphic is that the top halves look the same and the bottom halves do not.

**D4 — Two arrangements (Figure 2.6).** Three columns: responsibility, Esri arrangement, one open-source arrangement. The R4 cell in the open-source column is drawn as an empty dashed box with the caption "no direct equivalent among these three products". Caption: "products are examples, not a prescribed stack; capability statements checked 19 September 2026".

**D5 — Configure / extend / build ladder (Table 2.5).** Three steps ascending left to right, each with its question; a return arrow from the third step to the first labelled "requirement unclear — rewrite it".

**D6 — S-2 architecture (lab answer sketch, instructor version).** The learner's expected sketch from I.3, drawn cleanly: archived file geodatabase (grey, "archived 2026-09-15"), `.aprx` with a dashed arrow to the file, two hosted layers (filled), one view (dashed), two web maps, three apps, group tag, four member tags, and three question-mark badges on the unknowns.

All diagrams are schematic. None represents measured geography, and none uses the fixture's coordinates.

## M.4 Product-to-role table for slides (Table M2; claims dated 19 September 2026)

| Product | Responsibility | Documentation edition read | Source |
| --- | --- | --- | --- |
| ArcGIS Pro | R1 | "Released version: ArcGIS Pro 3.7" | [S02] |
| ArcGIS Online — hosted layers | R2, R3 | Live service documentation | [S03] [X03] |
| ArcGIS Online — organisation, items, groups | R4 | Live service documentation | [S03] [X05] [X08] [X09] |
| Portal for ArcGIS | R4 | Enterprise "latest" introduction; base deployment 11.4 | [S04] [X11] |
| ArcGIS Server | R3 (+ processing) | Same | [S04] [X11] |
| ArcGIS Data Store | R2 (managed, hosted content) | Same | [S04] [X11] |
| Instant Apps, Experience Builder, Dashboards, Field Maps | R5 (configured) | Product "latest" pages | [X18] [X17] [X19] [X20] |
| ArcGIS Maps SDK for JavaScript | R5 (built) | 5.1 (June 2026) | [X12] |
| ArcGIS API for Python | Automation of R4 and services | 2.4.3 (March 2026) | [X13] |
| ArcPy | Automation of R1 (and server geoprocessing) | ArcGIS Pro 3.7; ArcGIS Server 11.0 page | [X14] [X15] [X16] |
| OpenLayers | R5 (built) | Project home page | [S06] |
| Mapbox | R5 (SDKs); R3/R2 for its hosted services and data | Vendor home page | [S07] |
| QGIS Desktop | R1 | Gentle Introduction 3.44; User Guide 3.40 | [S01] [X21] |
| PostGIS | R2 | Project home page | [S09] |
| GeoServer | R3 | Project About page | [S08] |

## M.5 Interactive and 3D material

None proposed. The chapter's concepts are architectural, and the blueprint's guidance that a table or 2D diagram teaches better applies throughout. Should a later revision want an interactive element, the only candidate is a clickable version of D2 in which selecting a box reveals "what it stores / what it references / what happens if deleted", validated against the item-type table in 2.3.2 and the deletion statements in [X03], [X04], and [X07].
