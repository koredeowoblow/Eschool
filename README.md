# 🏫 Eschool - Advanced Education Management System

Eschool is a high-performance, multi-tenant Education Management System (EMS) designed for seamless school administration. It features strict data isolation, a robust subscription model, and real-time communication capabilities.

---

## 📖 Table of Contents
- [🚀 Key Features](#-key-features)
- [🏗 Architecture & Design](#-architecture--design)
- [👥 Role-Based Access (RBAC)](#-role-based-access-rbac)
- [📦 Core Business Modules](#-core-business-modules)
- [📂 Project Topology](#-project-topology)
- [⚙️ Setup & Installation](#-setup--installation)
- [📝 Developer Handbook](#-developer-handbook)
- [🔐 Security & Compliance](#-security--compliance)

---

## 🚀 Key Features

### 🏢 Enterprise Multi-Tenancy
- **Database-Level Isolation**: Every query is automatically scoped by `school_id` via the Repository layer.
- **Resource Guardrails**: Real-time enforcement of student and staff limits based on the school's active subscription plan.
- **Dynamic Configuration**: Each school maintains its own grading scales, sessions, and academic calendars.

### 📚 Academic Engine
- **Hierarchical Approvals**: A fail-safe workflow for student results (`Draft` → `Submitted` → `Reviewed` → `Published`).
- **Real-time Registers**: Digital attendance tracking with automated notifications to guardians.
- **Instructional Tools**: Integrated lesson note distribution and assignment management.

### 💰 Financial Integrity
- **Flex-Billing**: Automated invoice generation for fees with support for discounts and installments.
- **Audit Ledger**: A permanent, unchangeable record of all financial transactions and administrative changes.
                    
---

## 🏗 Architecture & Design

### 1. Service-Repository Pattern
- **Repositories**: Standardize data access. They handle all Eloquent queries and ensure strict multi-tenancy.
- **Services**: Contain the core "business rules." If a student is created, the Service handles the User creation, Role assignment, and Enrollment simultaneously.
- **Controllers**: Logic-less entry points. They validate the request and deliver the response.

### 2. Multi-Tenancy (Spatie Teams)
We use `spatie/laravel-permission` with the `teams` feature. 
- **Global Roles**: Shared templates (e.g., `Teacher`, `Student`) with `school_id = null`.
- **Custom Roles**: School-specific roles (e.g., `Principal`) scoped to a specific `school_id`.

---

## 🛠 Tech Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Storage**: MySQL 8.0+ / PostgreSQL 15+
- **Real-time**: Laravel Reverb (WebSockets)
- **Security**: Laravel Sanctum & Spatie Permissions
- **Frontend**: Blade Components, Vanilla JS, CSS3, SweetAlert2
- **Tools**: Composer, Git, Artisan

---

## 👥 Role-Based Access (RBAC)

| Role | Responsibility | Data Boundary |
| :--- | :--- | :--- |
| **Super Admin** | Platform maintenance, school onboarding, global plans. | **Global** |
| **School Admin** | Daily operations, staff oversight, billing, local settings. | **Tenant** |
| **Teacher** | Class management, attendance, mark entry, lesson planning. | **Resource-Scoped** |
| **Student** | Learning portal access, assignments, result tracking. | **Personal** |
| **Guardian** | Ward monitoring, teacher communication, fee payments. | **Relation-Scoped** |

---

## 📦 Core Business Modules

### 📝 Result Management
Managed in `App\Services\Academic\ResultService`. 
- Results are **Locked** once submitted by a teacher.
- **Validation**: Marks cannot exceed the assessment's maximum score.
- **Publication**: Results are only visible to the public after the `Published` state is reached.

### 💸 Fee System
Managed in `App\Services\Finance\FinanceService`.
- **Invoicing**: Triggered at the start of a term or upon student enrollment.
- **Payments**: Supports partial payments. The `invoices` table tracks `amount_paid` and `balance_due`.

---

## 📂 Project Topology

```text
app/
├── Helpers/        # Standardized Audit & Response helpers.
├── Http/
│   ├── Controllers/ # Delivery layer (Web/API).
│   └── Requests/    # Complex validation rules.
├── Repositories/   # Tenant-scoped data access.
└── Services/       # Business logic orchestration.
database/
├── migrations/     # Multi-tenant schema definitions.
└── seeders/        # Global baseline and system roles.
public/
└── js/             # Pre-built premium dashboard assets.
```

---

## ⚙️ Setup & Installation

### Steps
1. **Clone**: `git clone <repository-url>`
2. **Install**: `composer install`
3. **Configure**: `cp .env.example .env && php artisan key:generate`
4. **Initialize**: `php artisan migrate:fresh --seed`

### 🏃 Running the Application
To run the full suite (Server + Real-time Messaging):

```bash
# Terminal 1: Web Server
php artisan serve

# Terminal 2: Real-time Messaging (WebSockets)
php artisan reverb:start
```

---

## 📝 Developer Handbook

### 1. Tenancy Rule #1
**Never** use `Model::all()` or `Model::find()`. Always use the corresponding Repository method to ensure the query is scoped to the current school.

### 2. Standardized Responses
Always use the `ResponseHelper`.
- `return ResponseHelper::success($data, 'Successfully updated');`
- `return ResponseHelper::error('Unauthorized access', 403);`

### 3. State Management
For entities like `Results` or `Invoices`, use the defined constants in the Model (e.g., `Result::STATUS_PUBLISHED`) instead of hardcoded strings.

---

## 🔐 Security & Compliance
- **JWT/Sanctum**: All API routes are protected by token authentication.
- **Audit Logs**: Every state change is recorded with the `user_id` and `timestamp`.
- **Rate Limiting**: Critical endpoints (Login, Payment) are throttled to prevent brute-force attacks.
