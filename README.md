<div align="center">

# 🌲 PacificTree: Enterprise GIS & Forestry Operations Platform

### A Full-Stack Spatial Mapping, Inventory & Commercial Management Platform

Spatial Mapping • Tree Inventory • Commercial Sales • Client Portal • Financial Analytics • Document Exports

---

![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![Leaflet](https://img.shields.io/badge/Leaflet-1.9-199900?style=for-the-badge&logo=leaflet&logoColor=white)
![Chart.js](https://img.shields.io/badge/Chart.js-Analytics-FF6384?style=for-the-badge)
![DomPDF](https://img.shields.io/badge/DomPDF-PDF%20Export-E53935?style=for-the-badge)
![PhpSpreadsheet](https://img.shields.io/badge/PhpSpreadsheet-Excel-217346?style=for-the-badge)
![Git](https://img.shields.io/badge/Git-Version%20Control-F05032?style=for-the-badge&logo=git&logoColor=white)

![Status](https://img.shields.io/badge/Status-Completed-success?style=for-the-badge)
![Version](https://img.shields.io/badge/Version-1.0-blue?style=for-the-badge)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

</div>

---

# Overview

**PacificTree** is a full-stack enterprise web application designed for commercial forestry operations, spatial mapping, and digital timber asset tracking. 

The platform bridges the gap between field operations and commercial client management. It empowers staff administrators with real-time GIS mapping, tree block inventory tracking, and sales operations, while offering clients a dedicated portal to view acquisitions, monitor tree growth metrics, and track their investments.

---

## Key Features

### 📍 Interactive GIS & Spatial Mapping
This project is a direct full-stack web evolution of my initial **Tree Pacific Database System**. 

While the original system laid the foundational backend and database architecture, **PacificTree** expands the concept into a comprehensive, production-grade enterprise application by introducing:

* **Real-time Map Coordinates:** Interactive map visualization powered by Leaflet.js and OpenStreetMap.
* **GPS Pin Positioning:** Custom tree location markers displaying species, health status, pricing, and exact GPS coordinates (latitude/longitude).
* **Reverse Geocoding:** Instant location identification and address mapping using the Nominatim API.

### 🌳 Tree Block Inventory Management
* **Comprehensive CRUD Operations:** Track species names, grades, heights, diameters, health metrics, and block prices.
* **Block Assignment System:** Group individual trees into commercial blocks for structured inventory allocation.

### 💼 Commercial Operations & Client Portal
* **Role-Based Access Control (RBAC):** Distinct dashboards and access permissions tailored for Staff Administrators and Commercial Clients.
* **Sales & Block Allocation:** Seamless workflow for assigning tree blocks to client companies.
* **Client Portfolio Dashboard:** Dedicated client view to monitor order histories, acquired tree blocks, and tree growth metrics.

### 📊 Reports & Financial Analytics
* **Visual Dashboards:** Real-time revenue distribution charts, inventory status gauges, and portfolio valuations built with Chart.js.
* **Automated Document Exports:** Downloadable PDF reports (via DomPDF) and Excel/CSV spreadsheets (via PhpSpreadsheet) for offline auditing.

---

# Tech Stack & Dependencies

## Backend
- **PHP** (OOP Architecture)
- **MySQL / MariaDB**

## Frontend
- **CSS3**
- **JavaScript** (ES6+)
- **FontAwesome Icons**

## Spatial Mapping & Visualization
- **Leaflet.js** (Interactive Maps)
- **OpenStreetMap API & Nominatim** (Geocoding)
- **Chart.js** (Data & Financial Analytics)

## PHP Libraries (via Composer)
- **`dompdf/dompdf`** — Automated PDF document generation
- **`phpoffice/phpspreadsheet`** — Excel (`.xlsx`) and CSV spreadsheet exports

---

# Project Structure

```text
PacificTree/
├── database/
│   └── tree.sql
├── src/
│   ├── config/
│   │   └── db.php
│   ├── controllers/
│   ├── models/
│   └── views/
│       ├── client/
│       ├── staff/
│       └── public/
├── vendor/               # Excluded via .gitignore (managed via Composer)
├── composer.json
├── composer.lock
└── README.md

```

---

# Future Improvements

* [ ] Real-time satellite layer integration for drone/aerial forestry inspections
* [ ] Automated email notifications for order completions and status updates
* [ ] REST API endpoints for field staff mobile applications
* [ ] Multi-currency support for international timber clients
* [ ] Docker containerization for production deployment

---

# License

This project is released under the **MIT License**.

Feel free to learn from, fork, and adapt this project for your own builds.

---

⭐ If you found this project interesting, consider giving it a star!

Made with ❤️ by [mdhamka](https://www.google.com/search?q=https://github.com/mdhamka)