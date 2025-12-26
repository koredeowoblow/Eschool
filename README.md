# Eschool - Advanced Education Management System

Eschool is a high-performance, multi-tenant Education Management System (EMS) architected for scalability, security, and administrative efficiency. It provides a centralized platform for managing multiple educational institutions with complete data isolation and granular role-based control.

---

## 📖 Table of Contents
- [🚀 Key Features](#-key-features)
- [🏗 Architecture & Design Patterns](#-architecture--design-patterns)
- [👥 User Role & Access Model](#-user-role--access-model)
- [📦 Core Modules](#-core-modules)
- [📂 Project Structure](#-project-structure)
- [⚙️ Setup & Installation](#-setup--installation)
- [📝 Developer Guidelines](#-developer-guidelines)
- [🔐 Security & Compliance](#-security--compliance)

---

## 🚀 Key Features

### 🏢 Enterprise Multi-Tenancy
- **Strict Isolation**: Model-level scoping using `school_id` ensures no data "leakage" between institutions.
- **Dynamic Resource Limits**: Enforce student, teacher, and staff limits based on school subscription plans.
- **Custom Branding**: Configuration hooks for school-specific settings and identity.

### � Academic Excellence
- **3-Step Result Workflow**: `Draft` -> `Submitted` -> `Reviewed/Published`. Results only reach parents/students after administrative sign-off.
- **Automated Grading**: Dynamic grade calculation based on school-defined grading scales.
- **Attendance & Engagement**: Real-time digital registers and interactive assignment tracking.

### � Financial Integrity
- **Intelligent Billing**: Automated fee generation based on student class/category.
- **Payment Verification**: Secured payment gateway integration with manual override for cash/bank transfers.
- **System-Wide Auditing**: Every modification to financial or academic data is timestamped and attributed.

---

## 🏗 Architecture & Design Patterns

### 1. Service-Repository Pattern
We strictly separate data access from business logic:
- **Repositories**: Standardize database queries and enforce tenancy scoping.
- **Services**: Coordinate business workflows, handle external events, and manage transactions.
- **Controllers**: Thin wrappers that handle request validation and response delivery.

### 2. Tenancy Scoping (Automated)
Most repositories extend `BaseRepository`, which implements a global query scope using the authenticated user's `school_id`.
> [!IMPORTANT]
> Always use Repository methods instead of direct Eloquent queries in Services or Controllers to ensure tenancy is never bypassed.

### 3. API Response Standardization
All endpoints must use the `ResponseHelper` to maintain consistency:
- **Success**: `{"success": true, "data": {...}, "message": "..."}`
- **Error**: `{"success": false, "errors": {...}, "message": "..."}`

---

## 👥 User Role & Access Model

The system uses [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission) with the **Teams** feature enabled.

| Role | Responsibility | Scope Access |
| :--- | :--- | :--- |
| **Super Admin** | Platform maintenance, school onboarding, global plan management. | **Global** (No Scoping) |
| **School Admin** | Daily operations, staff management, financial approval, settings. | **Tenant** (`school_id`) |
| **Teacher** | Classroom management, attendance, mark entry, lesson notes. | **Resource-Scoped** |
| **Student** | Viewing materials, taking assignments, tracking progress. | **Owner-Scoped** |
| **Guardian** | Monitoring ward performance, chatting with teachers, paying fees. | **Relationship-Scoped** |

---

## 📦 Core Modules

### Result System (`Model\Result.php`)
The result system moves through defined states:
1. **Draft**: Initial entry by Teacher.
2. **Submitted**: Sent for review (locked for Teacher).
3. **Reviewed**: Verified by Exam Officer/Admin.
4. **Published**: Visible to Student/Guardian.

### Finance System (`Services\Finance\FinanceService.php`)
Handles complex fee logic:
- Multi-student discounts (linked via Guardian).
- Part-payment support with outstanding balance tracking.
- Automated invoice aging (Overdue notifications).

---

## 📂 Project Structure

```text
app/
├── Helpers/        # AuditLogger, ResponseHelper, and generic global helpers.
├── Http/
│   ├── Controllers/ # Thin controllers, delegated to Services.
│   ├── Requests/    # Validation logic (FormRequests).
│   └── Middleware/  # Tenancy verification, Session checking.
├── Models/         # UUID models (Traits: HasUuids, HasRoles, HasTenancy).
├── Repositories/   # Scoped data access logic.
└── Services/       # The "Brain" of the application (Business Logic).
database/
├── migrations/     # Ordered migrations with UUID support.
└── seeders/        # Environment-aware seeding (Global Roles & Admin).
```

---

## ⚙️ Setup & Installation

### Requirements
- PHP 8.2+
- MySQL 8.0+ / PostgreSQL 15+
- Node.js 18+

### Steps
1. **Clone**: `git clone <repo>`
2. **PHP Dependencies**: `composer install`
3. **JS Dependencies**: `npm install && npm run build`
4. **Environment**: `cp .env.example .env && php artisan key:generate`
5. **Database**: `php artisan migrate:fresh --seed`
6. **Serve**: `php artisan serve`

---

## 📝 Developer Guidelines

### 1. Naming Conventions
- **Controllers**: `[Domain]Controller.php` (e.g., `StudentController`)
- **Services**: `[Domain]Service.php` (e.g., `FinanceService`)
- **Repositories**: `[Domain]Repository.php`
- **Database**: Use UUIDs for all `id` columns.

### 2. Transaction Safety
Always use `DB::transaction()` in Services when modifying multiple tables (e.g., creating a Student + User + Enrollment).

### 3. Auditing
Include `AuditLogger::log(...)` for all state-changing actions. This is critical for the Accountability log visible to Super Admins.

---

## 🔐 Security & Compliance
- **Authentication**: Laravel Sanctum with 24-hour token expiration.
- **Authorization**: Mandatory `middleware(['role:name'])` or `can('permission')` on all routes.
- **Data Protection**: Sensitive data (passwords, specific student PII) is never exposed in API responses without explicit intent.
- **Rate Limiting**: Configured per endpoint based on sensitivity (Login/Payment).

---

## 📄 Documentation Links
- [Internal Codebase Flow](file:///c:/Users/pc/.gemini/antigravity/brain/032af03b-9877-4d62-aa71-d779b1cac7f9/codebase_flow.md)
- [Verification Guide](file:///c:/Users/pc/.gemini/antigravity/brain/032af03b-9877-4d62-aa71-d779b1cac7f9/walkthrough.md)
