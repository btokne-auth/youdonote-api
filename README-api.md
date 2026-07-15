# youdonote-api

The cloud backend for **YouDoNote** — a PHP REST API + SOAP analytics service, sharing one MySQL database, deployed on [Railway](https://railway.com/).

> Android client repo: [youdonote-android](#) _← // to be updated once the Android repo is created_

---

## Overview

This backend replaces YouDoNote's original local-SQLite, single-device design. It has two components deployed together on the same Railway project:

- **REST API** — handles all task and milestone CRUD operations against MySQL
- **SOAP service** — handles productivity analytics (task complexity, forecasting, summaries, priority suggestions)

The SOAP service is **stateless with respect to the database** — it never queries MySQL directly. The Android app fetches task data via REST first, then sends that data to the SOAP service to be computed on.

```
User → YouDoNote App (Kotlin, Compose) ⇄ PHP Backend (Railway) ⇄ MySQL Database (Railway)
                                          via REST API
                                          via SOAP API
```

---

## Tech Stack

| Layer | Technology |
|---|---|
| REST | PHP (`tasks.php`, `milestones.php`) |
| SOAP | PHP `ext-soap` (`service.php`) |
| Database | MySQL, hosted on Railway |
| Hosting / Build | Railway (Railpack) |
| Dependency management | Composer |
| Testing | Postman, SoapUI |

---

## REST API Reference

### Tasks — `tasks.php`
| Method | Description |
|---|---|
| `GET` | List tasks / get task by `id` query param |
| `POST` | Create a task (title, description, milestone_id, priority, category, estimated_time, mental_effort, due_date) |
| `PUT` | Update a task (id is read from the JSON body, not the URL) |
| `DELETE` | Delete a task by `id` query param |

### Milestones — `milestones.php`
| Method | Description |
|---|---|
| `GET` | List milestones / get milestone by `id` |
| `POST` | Create a milestone (name, due_date) |
| `PUT` | Update a milestone |
| `DELETE` | Delete a milestone by `id` |

---

## SOAP API Reference

### `service.php`

| Operation | Purpose | UI Surface |
|---|---|---|
| `calculateTaskComplexity` | Returns Low/Medium/High complexity from priority, estimated time, mental effort | Workload Check (New Task screen) |
| `getProductivityForecast` | Returns a productivity forecast from focus level + task load | Productivity Outlook (Focus Level widget) |
| `generateSmartSummary` | Returns a generated summary from completed/pending task counts | Verified at backend only — not yet wired to UI |
| `suggestOptimizedPriority` | Returns a suggested priority based on energy level and task load | Verified at backend only — not yet wired to UI |

All operations were verified via SoapUI and Postman with `200 OK` responses.

---

## Deployment

Deployed on **Railway** using **Railpack** (no Docker).

### Required Composer extensions
```json
{
  "require": {
    "php": "^8.0",
    "ext-mysqli": "*",
    "ext-pdo_mysql": "*",
    "ext-soap": "*"
  }
}
```

> Note: `ext-soap` is not enabled by default in Railpack builds. Without it explicitly declared in `composer.json`, deployment fails with `Class "SoapServer" not found`.

### Steps
1. Provision a MySQL database on Railway
2. Deploy `tasks.php` and `milestones.php`; verify each endpoint in Postman
3. Point the Android app's Retrofit client at the deployed REST URL
4. Deploy `service.php`; ensure `ext-soap`, `ext-mysqli`, `ext-pdo_mysql` are declared in `composer.json`
5. Point the Android app's `SoapClient.kt` at the deployed SOAP URL; verify via SoapUI
6. Test end-to-end on a real device / emulator

---

## Environment Variables

Database credentials and connection details are read from environment variables (not hardcoded) — set these in the Railway project settings:

- `DB_HOST`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`

---

## Security Notes

- All endpoints are served over HTTPS via Railway
- The database is only reachable through the REST/SOAP layers, never exposed directly
- **No authentication is implemented yet** — any client with the API URL can read/write data. This is a known risk; token-based auth is planned before any public release.

---

## Roadmap

- [ ] Add authentication (API keys or token-based login)
- [ ] Per-user data isolation
- [ ] Evaluate a paid Railway tier to reduce downtime risk during demos

---

## Author

Pauline Xavier M. Estira — College of Computer and Information Science, AY 2025–2026
