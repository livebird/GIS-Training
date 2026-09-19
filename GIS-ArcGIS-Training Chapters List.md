**You’re building an ArcGIS delivery team, starting with developers who have no GIS background.** That requires a curriculum covering GIS reasoning, data preparation, ArcGIS configuration, custom development, and platform operations.

I would revise the earlier chapter list for this goal. It was a general GIS foundation; it did not give enough weight to ArcGIS administration and Enterprise deployment.

Two decisions should guide the training:

- **Everyone learns the shared foundations and ArcGIS configuration.** Development and Enterprise administration then become specializations, with some overlap.
- **Each chapter needs practical work from the beginning.** Documents, presentations, and audio support learning; they cannot establish whether someone can deliver a correct GIS solution.

Below is the chapter order I recommend. Each numbered chapter can become a separate document.

**Phase 1 — Understand GIS and the ArcGIS ecosystem**

Introduce the ecosystem early so developers understand where the foundational concepts will be used.

| Ch. | Chapter                                                         | What it should cover                                                                                                                                                    |
| --- | --------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| 1   | **GIS fundamentals and spatial thinking**                       | What GIS solves; location, distribution, proximity, and spatial relationships. Turning a business question into a geographic question.                                  |
| 2   | **Understanding the ArcGIS ecosystem**                          | The roles of ArcGIS Pro, ArcGIS Online, ArcGIS Enterprise, configurable applications, APIs, and SDKs. A simple demonstration of data becoming a map and an application. |
| 3   | **Geographic data: features, geometry, attributes, and layers** | How real-world objects become GIS records. Points, lines, polygons, multipart geometry, attributes, datasets, and layers.                                               |
| 4   | **Raster data and geographic surfaces**                         | Pixels, resolution, bands, NoData, imagery, and elevation. Choosing between vector and raster representations.                                                          |
| 5   | **Coordinates and coordinate reference systems**                | Latitude/longitude, X/Y, units, coordinate order, datums, geographic/projected systems, and EPSG identifiers.                                                           |
| 6   | **Projections, transformations, and accurate measurement**      | Projection distortion, assigning versus transforming a CRS, combining different CRSs, and choosing suitable distance/area calculations.                                 |
| 7   | **GIS data formats, sources, and metadata**                     | GeoJSON, GeoPackage, Shapefile, GeoTIFF, CSV, and geodatabases. Checking source quality, currency, accuracy, and usage rights.                                          |
| 8   | **GIS data modeling and relationships**                         | Fields, IDs, domains, nulls, relationships, attachments, and time fields. Designing data for reliable collection, querying, and editing.                                |
| 9   | **Data capture, editing, and quality**                          | Digitizing, snapping, GPS/GNSS, geocoding, georeferencing, geometry validity, and topology rules.                                                                       |
| 10  | **Attribute queries and spatial relationships**                 | Filters, joins, intersects, contains, within, touches, and nearest. Boundary cases and the difference between table joins and spatial joins.                            |
| 11  | **Spatial analysis fundamentals**                               | Buffer, clip, intersect, dissolve, spatial join, and aggregation. Selecting an operation, checking assumptions, and validating results.                                 |
| 12  | **Cartography and interpreting maps correctly**                 | Symbology, labels, classification, scale, visibility, legends, and accessibility. Distinguishing precision, accuracy, and resolution.                                   |

**Phase checkpoint:** Given unfamiliar data, the trainee can explain its structure and CRS, identify quality problems, perform a basic spatial analysis, and justify the result.

Use small practical exercises throughout this phase. Do not wait until Chapter 12 to open a GIS application.

**Phase 2 — Prepare and publish GIS content with ArcGIS Pro**

| Ch. | Chapter                                          | What it should cover                                                                                                                                      |
| --- | ------------------------------------------------ | --------------------------------------------------------------------------------------------------------------------------------------------------------- |
| 13  | **ArcGIS Pro working fundamentals**              | Projects, maps, catalogs, connections, selections, tables, editing, and geoprocessing. Establishing an organized project workflow.                        |
| 14  | **Geodatabase design and editing workflows**     | Create feature classes, domains, relationships, and attachments. Apply the data-modeling concepts learned earlier.                                        |
| 15  | **Data preparation and repeatable analysis**     | Import, clean, transform, join, and analyze data. Understand processing environments and introduce ModelBuilder for repeatable workflows.                 |
| 16  | **Web GIS architecture and publishing concepts** | Client, portal, server, and storage roles; items, service URLs, feature services, map/image services, and tiles. Prepare and publish suitable web layers. |
| 17  | **3D GIS and web scenes**                        | Elevation, Z values, height references, terrain, extrusion, scenes, and scene layers. Understand when 3D adds useful information.                         |

**Phase checkpoint:** Prepare a clean dataset, produce a defensible map, publish a layer, and explain the relationship between the source data, service, portal item, and map.

**Phase 3 — Configure and operate ArcGIS Online**

| Ch. | Chapter                                                | What it should cover                                                                                                                                             |
| --- | ------------------------------------------------------ | ---------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| 18  | **ArcGIS Online organization and access fundamentals** | Members, user types, roles, groups, ownership, sharing, and licensing concepts. Establish appropriate access before publishing applications.                     |
| 19  | **Hosted layers and data management**                  | Hosted feature layers, views, editing permissions, attachments, editor tracking, updating data, and schema-change implications.                                  |
| 20  | **Web maps and Map Viewer configuration**              | Styles, labels, filters, pop-ups, forms, visibility, and map configuration. Introduce Arcade expressions in appropriate contexts.                                |
| 21  | **Configurable applications and dashboards**           | Build applications with Instant Apps, Dashboards, and Experience Builder. Decide when configuration satisfies the requirement and when development is justified. |
| 22  | **Field data collection and offline workflows**        | Survey123 and Field Maps concepts, form design, validation, offline areas, synchronization, and field-to-office workflows.                                       |
| 23  | **ArcGIS Online administration and governance**        | Organization settings, credit monitoring, content inventory, ownership transfer, sharing audits, dependencies, and content lifecycle procedures.                 |

ArcGIS Online includes mapping, hosted data, applications, sharing, and organization administration; these should be taught as connected workflows. [Official ArcGIS Online overview](https://doc.arcgis.com/en/arcgis-online/get-started/what-is-agol.htm)

**Phase checkpoint:** Deliver a configured solution with separate editor and viewer access, a collection workflow, a dashboard, and an operating guide.

**Phase 4 — Build custom ArcGIS applications and automation**

Your team already knows software development, so these chapters should concentrate on the GIS-specific concepts and failure modes.

| Ch. | Chapter                                                         | What it should cover                                                                                                                                  |
| --- | --------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------- |
| 24  | **ArcGIS REST APIs and authentication**                         | Portal and service endpoints, queries, pagination, spatial filters, edit operations, OAuth, API keys where supported, and authorization.              |
| 25  | **Custom web applications with ArcGIS Maps SDK for JavaScript** | Maps/views, layers, renderers, events, selection, pop-ups, editing, and integration with the team’s frontend stack.                                   |
| 26  | **Extending configured applications**                           | Experience Builder custom widgets and application integration. Evaluate extension versus a standalone custom application.                             |
| 27  | **Python automation: ArcGIS API for Python and ArcPy**          | Distinguish portal/service automation from desktop geoprocessing. Automate data preparation, publishing, inventory, and reporting.                    |
| 28  | **Backend integration and spatial databases**                   | Connect business systems to GIS services; introduce PostGIS, spatial indexes, data synchronization, stable IDs, and reliable update handling.         |
| 29  | **Application quality, performance, and delivery**              | Test spatial correctness and permissions; handle large datasets, failed requests, edit conflicts, environment configuration, logging, and deployment. |

These development paths align with Esri’s documented SDKs, app-builder extensions, and scripting APIs. [Official ArcGIS developer documentation](https://developers.arcgis.com/documentation/)

**Phase checkpoint:** Build an authenticated application that reads and edits GIS data, integrates with a backend, and handles failures correctly.

**Phase 5 — Deploy, administer, and operate ArcGIS Enterprise**

This is a separate engineering competency. Being able to build an ArcGIS application does not establish readiness to administer a production Enterprise deployment.

| Ch. | Chapter                                               | What it should cover                                                                                                                                    |
| --- | ----------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------- |
| 30  | **ArcGIS Enterprise architecture**                    | Portal, ArcGIS Server, hosting server, ArcGIS Data Store, web access components, and federation. Trace requests and distinguish Enterprise from Online. |
| 31  | **Infrastructure and deployment planning**            | Supported deployment options, capacity, operating systems, DNS, TLS certificates, networking, storage, service accounts, licensing, and compatibility.  |
| 32  | **Deploying a training environment**                  | Install and configure a supported base deployment; configure web access, federation, and hosting; validate component health and connectivity.           |
| 33  | **Enterprise publishing and data architecture**       | Hosted versus referenced data, registered data sources, enterprise geodatabases, database permissions, service capabilities, and versioning concepts.   |
| 34  | **Enterprise identity, security, and administration** | Identity-provider integration, roles, groups, service access, credential management, administrative boundaries, and auditing.                           |
| 35  | **Monitoring, troubleshooting, and performance**      | Logs, service health, resource usage, failed publishing, slow queries, storage growth, and incident diagnosis.                                          |
| 36  | **Backup, recovery, upgrades, and availability**      | Supported backup procedures, restore drills, recovery objectives, upgrade planning, dependencies, rollback planning, and high-availability concepts.    |

Deployment instructions must be tied to a **specific ArcGIS Enterprise release and deployment model**. Even the required components and supported arrangements need version-aware treatment. [Official base deployment documentation](https://doc.esri.com/en/arcgis-enterprise/latest/plan/base-arcgis-enterprise-deployment.html)

**Phase checkpoint:** Deploy a training environment, publish a secured service, diagnose an introduced failure, and demonstrate recovery from backup. Installation alone is not a pass.

**Chapter 37 — Integrated delivery and handover project**

Combine the phases into one realistic solution: data preparation, configured application, custom functionality, access control, deployment, monitoring, and operational documentation.

A suitable recurring training project is **municipal asset inspections and service requests**:

- Assets, roads, and ward boundaries teach geometry and spatial analysis.
- Inspections teach relationships, attachments, and field collection.
- Dashboards teach configuration.
- A custom portal teaches development and integration.
- Deployment and recovery exercises teach operations.

Keep the business scenario consistent so trainees can concentrate on the new GIS concept in each chapter.

**How each chapter document should be structured**

Treat each document as the authoritative teaching source from which the other materials are derived.

| Component                                      | Purpose                                                                              |
| ---------------------------------------------- | ------------------------------------------------------------------------------------ |
| Prerequisites and measurable learning outcomes | State what learners must know and what they must demonstrate afterward.              |
| Concepts and terminology                       | Explain the topic in language suitable for developers new to GIS.                    |
| Worked example and diagrams                    | Show the reasoning, inputs, steps, and expected results.                             |
| Guided lab                                     | Provide dataset, environment requirements, instructions, and verification steps.     |
| Independent exercise                           | Check whether learners can apply the concept without copying instructions.           |
| Common mistakes and troubleshooting            | Explain incorrect assumptions, symptoms, and how to investigate them.                |
| Assessment and separate instructor answers     | Combine concept questions, scenario questions, and practical scoring criteria.       |
| Media production notes                         | Specify slide sequence, narration needs, animations, and suitable 3D demonstrations. |
| References and version information             | Record official sources, applicable software versions, and review date.              |

Keep the conceptual explanation separate from version-specific click instructions. That will make the material substantially easier to maintain.

For **audio lessons**, the narration must explain diagrams verbally and avoid relying on phrases such as “click here” or “as shown above.” For tests, use practical tasks alongside questions; recall alone is too weak a measure of delivery readiness.

For **3D teaching material**, useful examples include a globe becoming a projected map, elevation surfaces, buildings with different height references, and underground utilities. The chapter document should provide a storyboard, labels, interactions, and expected learning outcome. **A document by itself is not enough to generate an accurate 3D lesson without those specifications.**

I recommend using this as the revised master chapter sequence. Train everyone through Phase 3, then let application developers and Enterprise administrators deepen their respective tracks before collaborating on Chapter 37.
