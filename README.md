# Savoria Tech Test

Internal Application Access Portal

![Laravel 13](https://img.shields.io/badge/Laravel-13-red)
![Vue 3](https://img.shields.io/badge/Vue-3-42b883)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15-blue)
![TypeScript](https://img.shields.io/badge/TypeScript-5-blue)

Aplikasi portal internal untuk mengelola akses ke berbagai aplikasi perusahaan. Akses ditentukan berdasarkan department, role, dan/atau user secara langsung.

## Features

**Authentication**

- Login dengan Sanctum Bearer Token
- Logout
- Current authenticated user

**User Portal**

- Application dashboard berdasarkan akses
- Buka aplikasi di tab baru
- Aplikasi dikelompokkan per kategori
- Manajemen profil

**Admin Panel**

- CRUD Applications
- CRUD Categories
- CRUD Departments
- CRUD Roles
- CRUD Users
- Access Rule Management
- User Access Report

**Access Engine**
Hak akses aplikasi bisa diberikan melalui:

- Department
- Role
- Direct user assignment

## Tech Stack

- Backend: Laravel 13 (API), PHP 8.4+, PostgreSQL, Sanctum
- Frontend: Vue 3 + TypeScript, Vite, Tailwind CSS v4, PrimeVue
- Package manager: pnpm (frontend)

## Arsitektur

| Layer  | Komponen                     | Peran                      | Komunikasi            |
| ------ | ---------------------------- | -------------------------- | --------------------- |
| Client | Vue 3 SPA                    | UI dan interaksi user      | HTTP JSON ke API      |
| API    | Laravel 13                   | Auth, business logic, CRUD | Query ke PostgreSQL   |
| Auth   | Sanctum                      | Token-based auth           | Dipakai oleh API      |
| Data   | PostgreSQL                   | Penyimpanan data           | View untuk akses user |
| Data   | view_user_application_access | Aggregasi akses aplikasi   | Dibaca oleh API       |

**Alur singkat**

- SPA melakukan login ke API, menerima token Sanctum.
- SPA menyimpan token di localStorage, semua request berikutnya menggunakan Authorization: Bearer.
- Akses aplikasi dihitung dari 3 jalur: department, role, dan user langsung.

## Relasi Database (ERD)

**Tabel utama**

| Tabel        | Kolom kunci                                                         | Catatan                                    |
| ------------ | ------------------------------------------------------------------- | ------------------------------------------ |
| users        | id (PK), email (unique), department_id (FK), role_id (FK), is_admin | Soft delete, relasi ke department dan role |
| departments  | id (PK), name                                                       | Soft delete                                |
| roles        | id (PK), name                                                       | Soft delete                                |
| categories   | id (PK), name (unique)                                              | Soft delete                                |
| applications | id (PK), category_id (FK), name, url                                | Soft delete                                |

**Pivot / relasi M:N**

| Pivot                  | Kolom                         | Relasi                       |
| ---------------------- | ----------------------------- | ---------------------------- |
| application_department | application_id, department_id | applications <-> departments |
| application_role       | application_id, role_id       | applications <-> roles       |
| application_user       | application_id, user_id       | applications <-> users       |

**Ringkas relasi**

| Relasi                       | Tipe                         |
| ---------------------------- | ---------------------------- |
| users -> departments         | N:1                          |
| users -> roles               | N:1                          |
| applications -> categories   | N:1                          |
| applications <-> departments | M:N (application_department) |
| applications <-> roles       | M:N (application_role)       |
| applications <-> users       | M:N (application_user)       |

**View penting**

- view_user_application_access: hasil gabungan akses berdasarkan department, role, dan user langsung.

## Implementation Coverage

| Requirement       | Status |
| ----------------- | ------ |
| User Dashboard    | Yes    |
| CRUD Application  | Yes    |
| Access Management | Yes    |
| Database View     | Yes    |
| Authentication    | Yes    |

## Access Resolution Logic

Akses dihitung menggunakan UNION dari tiga sumber:

1. Department access
2. Role access
3. Direct user access

Duplikasi aplikasi dihapus secara otomatis, dan hasil akhir disajikan melalui view_user_application_access.

## API Response Format

Success example:

```json
{
  "success": true,
  "message": "Login success",
  "data": {}
}
```

Validation error example:

```json
{
  "success": false,
  "message": "Validation failed",
  "data": {
    "email": ["Email is required"]
  }
}
```

## Authorization

| Role  | Permission                 |
| ----- | -------------------------- |
| Admin | Full system access         |
| User  | Dashboard dan profile only |

Admin endpoints diproteksi dengan Sanctum authentication dan is_admin middleware / authorization layer.

## Contoh User (Seeder)

Password untuk semua user: password

- Admin: admin@test.com (is_admin: true)
- User One: user1@test.com (HR, Staff)
- User Two: user2@test.com (IT, Manager)
- User Three: user3@test.com (Finance, Supervisor)

## Data Contoh (Seeder)

- Departments: HR, IT, Finance
- Roles: Manager, Staff, Supervisor
- Categories: Enterprise, HR, Finance, Support, Productivity, Logistics, Sales, Analytics, IT
- Applications: ERP System, HRIS, Payroll, Support Desk, Project Management, Document Management, Inventory Management, CRM, Business Intelligence, Asset Management

## Seeder Access Example

| Rule Type  | Target         | Application  |
| ---------- | -------------- | ------------ |
| Department | HR             | HRIS         |
| Role       | Manager        | ERP System   |
| User       | user1@test.com | Support Desk |

## API Endpoint

Base URL: http://localhost:8000/api

**Auth**

- POST /login
- POST /logout
- GET /user

**Profile**

- GET /profile
- PUT /profile

**Dashboard**

- GET /dashboard

**Admin Only**

- apiResource /applications
- apiResource /categories
- apiResource /departments
- apiResource /roles
- apiResource /users
- GET /access-rules/{type}/{id}
- PUT /access-rules/{type}/{id}
- GET /reports/user-access

## Requirements

**Backend**

- PHP 8.4+
- Composer
- PostgreSQL 15+

**Frontend**

- Node.js 22+
- pnpm 10+

## Setup Backend

```bash
cd backend
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Default DB config di [.env.example](backend/.env.example): PostgreSQL savoria_tech_test_db.

## Setup Frontend

```bash
cd frontend
pnpm install
pnpm dev
```

Default API base URL di [.env.example](frontend/.env.example): http://localhost:8000/api.

## Login Cepat

1. Jalankan backend dan frontend.
2. Buka http://localhost:5173.
3. Login dengan admin@test.com / password.

## Folder Structure

```
backend/
├── app/
├── config/
├── database/
├── routes/
└── ...

frontend/
├── src/
│   ├── components/
│   ├── pages/
│   ├── router/
│   ├── services/
│   └── stores/
└── ...
```

## Design Decisions

- Laravel API dan Vue SPA dipisah untuk skalabilitas dan deployment terpisah.
- Sanctum Bearer Token dipakai untuk autentikasi stateless.
- PostgreSQL view dipakai untuk agregasi akses terpusat.
- Pivot M:N dipakai untuk fleksibilitas pemberian akses.
- Soft delete pada master table untuk menjaga histori data.

## Catatan

- Semua response API mengikuti format: { success, message, data }.
- Auth memakai Laravel Sanctum dengan token berbasis Bearer.
