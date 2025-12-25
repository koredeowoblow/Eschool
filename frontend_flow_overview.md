# Frontend Flow & Architecture Overview

## 1. Core Architecture Pattern
The application follows a **Hybrid SPA (Single Page Application-like)** approach using Laravel Blade for structure and Vanilla JS + Axios for dynamic data.

### **The "View-First" Flow**
1.  **Route**: User visits logic-less route (e.g., `/students`).
2.  **Controller**: `ViewController` returns the Blade skeleton (`layout.app` + `index.blade.php`).
3.  **Initialization**:
    *   Browser loads `premium-app.js`.
    *   `DOMContentLoaded` triggers `reload{Entity}()` (e.g., `reloadStudents()`).
4.  **Data Fetch**:
    *   `App.renderTable()` calls API (e.g., `/api/v1/students`).
    *   Returns JSON data.
5.  **Rendering**:
    *   JavaScript generates HTML rows dynamically and injects them into the `<tbody>`.
    *   **No page reloads** for searching, pagination, or CRUD actions.

---

## 2. The Dashboard Flow (`/dashboard`)
**Objective**: Role-aware, high-performance insight hub.

1.  **Entry**:
    *   Route: `/dashboard` loads `dashboard.blade.php`.
    *   Contains empty containers: `#dashboard-stats-root`, `#dashboard-charts-root`, `#dashboard-activity-root`.
2.  **Data Loading**:
    *   `premium-app.js` -> `loadDashboard()` runs immediately.
    *   Calls `/api/v1/dashboard/stats`.
    *   **Backend**: `DashboardService` detects user role (Super Admin, School Admin, Teacher, Student) and returns specific datasets.
3.  **Dynamic Rendering**:
    *   **Stats Cards**: JS builds styled cards (`createStatCard`) with icons/values and injects them with staggered animation (`animate-in`).
    *   **Charts**: Uses `Chart.js` to render canvas elements for "School Growth", "Revenue Trends", etc.
    *   **Activity**: "Upcoming Deadlines" lists are rendered for students/teachers.

---

## 3. Standard View Patterns (Modules)
All management views (Students, Classes, Finance, etc.) follow a strict pattern:

### **A. Browse / List View**
*   **Header**: Title + "Create New" Button (protected by `@hasrole`).
*   **Search/Filter**: Inputs trigger `reload{Entity}()` on `input` or `change` events.
*   **Table**:
    *   Columns correspond to API response fields.
    *   Rows rendered via `App.renderTable`.
    *   **Generic**: Uses default row layout (Name, Status, Actions).
    *   **Custom**: Uses callback function (e.g., Students view with Avatars).

### **B. Create Action (Modal)**
*   **Trigger**: Button clicks opens Bootstrap Modal (`#create{Entity}Modal`).
*   **Initialization**: `Modal.show` event triggers `App.loadOptions` to fetch dropdown data (Grades, Sections, Terms) via API.
*   **Submission**:
    *   Form `onsubmit` calls `App.submitForm(event, reloadCallback, type, modalId)`.
    *   Prevents default submission -> Sends POST via Axios -> Handles Validation Errors -> Toasts Success -> Refreshes Table -> Closes Modal.

### **C. Edit Action (Modal)**
*   **Trigger**: Click "Edit" icon on table row. calls `edit{Entity}(data)`.
*   **Population**:
    *   `App.populateForm(form, data)` maps JSON keys to input names.
    *   Form Action updated to `/api/v1/{entity}/{id}`.
    *   **Smart Dropdowns**: `App.loadOptions` is called with the *selected value* to ensure the correct option is active.

---

## 4. Key Global Features

### **Session Locking (The "Status Check")**
*   **Logic**: `premium-app.js` checks `AppConfig.active_session.status`.
*   **Frontend Effect**:
    *   If `closed` or `locked`:
        *   Red Banner appears at top: "SESSION LOCKED".
        *   Create/Edit buttons are disabled via CSS class `.requires-session-lock`.
        *   Forms are blocked from submission.

### **Sidebar Navigation**
*   **Config**: Defined in `public/js/sidebar/*.js` (e.g., `admin.js`, `teacher.js`).
*   **Rendering**: `SidebarManager.js` builds the DOM based on user roles.
*   **Hierarchy**:
    *   Academic (Sessions, Terms, Classes...)
    *   Students
    *   Assessment
    *   Finance

---

## 5. Summary of Views to Audit
1.  **Classes**: `classes/index.blade.php` - Uses generic rendering.
2.  **Students**: `students/index.blade.php` - Uses custom avatar rendering + Guardian lookup logic.
3.  **Finance**: `payments/index.blade.php` (etc) - Table based.
4.  **Results/Assessments**: Uses complex filtering for grading scales.

**Ready for your instructions on what to change.**
