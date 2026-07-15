# YouDoNote (Android)

A minimalist task management and progress-tracking mobile app for students and young professionals — built with **Kotlin** and **Jetpack Compose**, backed by a cloud-hosted REST + SOAP service architecture.

YouDoNote helps you create tasks and milestones, track progress, and get lightweight productivity coaching through two analytics features:
- **Workload Check** — rates a task's complexity (Low / Medium / High) before you commit to it
- **Productivity Outlook** — logs your daily focus level and tells you how manageable your remaining tasks are

> Backend repo: [youdonote-api](#) — PHP REST API + SOAP analytics service, deployed on Railway.

---

## Screenshots

| Home | Tasks | Calendar | Progress |
|------|-------|----------|----------|
| _add screenshot_ | _add screenshot_ | _add screenshot_ | _add screenshot_ |

---

## Architecture

YouDoNote follows a three-tier architecture:

```
User → YouDoNote App (Kotlin, Compose) ⇄ PHP Backend (Railway) ⇄ MySQL Database (Railway)
                                          via REST API
                                          via SOAP API
```

- **Presentation layer** — this Android app
- **Application layer** — a PHP REST API (CRUD for tasks/milestones) and a PHP SOAP service (productivity analytics)
- **Data layer** — a shared cloud-hosted MySQL database on Railway

The SOAP service is **stateless with respect to the database** — it does not query MySQL directly. The app retrieves task data via the REST API first, then passes that data to the SOAP service for computation.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Frontend | Kotlin, Jetpack Compose (Android Studio) |
| REST networking | Retrofit |
| SOAP networking | Custom `SoapClient.kt` |
| Backend (separate repo) | PHP (REST + SOAP), MySQL |
| Hosting | Railway (Railpack build) |
| Testing tools | Postman, SoapUI |

---

## Features

- Create, edit, and delete tasks with priority, category tag, estimated time, mental effort, and due date
- Create and track milestones with due dates and progress percentages
- Filter tasks by All / Today / Upcoming / Milestones
- Home dashboard: weekly completion summary, active milestones, completion streak
- Weekly calendar (Schedule Overview) with inline task editing
- Daily focus-level logging → Productivity Outlook via SOAP
- Workload Check (task complexity scoring) via SOAP
- Tag management (create, edit, remove, set default)
- Nickname and settings management

---

## Backend Endpoints Used

**REST API** (`tasks.php`, `milestones.php`)
- `GET / POST / PUT / DELETE` — task CRUD
- `GET / POST / PUT / DELETE` — milestone CRUD

**SOAP Service** (`service.php`)
- `calculateTaskComplexity` — powers Workload Check
- `getProductivityForecast` — powers Productivity Outlook
- `generateSmartSummary` — backend-verified, not yet wired to a UI screen
- `suggestOptimizedPriority` — backend-verified, not yet wired to a UI screen

---

## Getting Started

### Prerequisites
- Android Studio (latest stable)
- Min SDK: _add your min SDK version_
- An active internet connection (the app requires the cloud REST/SOAP backend — no offline/local-only mode)

### Setup
1. Clone this repo
2. Open in Android Studio and let Gradle sync
3. Confirm `RetrofitClient.kt` points to the deployed Railway REST URL
4. Confirm `SoapClient.kt` points to the deployed Railway SOAP URL
5. Run on an emulator or physical device

---

## Known Limitations

- Android only — no iOS or web client
- Single-user only — no authentication or multi-user isolation yet (see Roadmap)
- Requires internet connection at all times

## Roadmap

- [ ] User authentication (API keys or token-based login)
- [ ] Wire up `generateSmartSummary` and `suggestOptimizedPriority` to the UI
- [ ] iOS / web client
- [ ] Push notifications for upcoming/overdue tasks

---

## Author

Pauline Xavier M. Estira — College of Computer and Information Science, AY 2025–2026
