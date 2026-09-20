# MyArlion

MyArlion is an **internal ERP (Enterprise Resource Planning) web application** built with **Laravel 10**, designed to support and streamline internal business operations.

This repository is intended for **internal company use only**.

---

## 📌 Project Overview

- **Project Name:** MyArlion
- **Application Type:** ERP System
- **Scope:** Internal Company Application
- **Architecture:** Monolith
- **Framework:** Laravel 10
- **Interface:** Web Application (Blade)
- **Target Users:** Internal staff & management
- **Target Developers:** Full-stack developers

---

## 🧰 Tech Stack

### Backend
- PHP **8.1**
- Laravel **10**
- MySQL

### Frontend
- Blade Templates
- Node.js **12**
- Laravel Mix / Vite (depending on project setup)

---

## 📦 Main Packages

| Package | Description |
|------|-----------|
| `spatie/laravel-permission` | Role & permission management |
| `spatie/laravel-activitylog` | User & system activity logging |
| `yajra/laravel-datatables` | Server-side DataTables integration |

---

## 🔐 Authentication & Authorization

- Uses Laravel authentication
- Authorization handled via **Spatie Laravel Permission**
- Supports:
  - Roles
  - Permissions
  - Role–Permission mapping
- Access control enforced at:
  - Route middleware
  - Controllers
  - Blade views

---

## 📁 Project Structure Notes

The project mostly follows the default Laravel structure with the following custom directory:

```

storage/
└── app/
└── templates/

````

### `storage/app/templates`
This directory stores **Excel templates** used for exporting data.

> ⚠️ Do not remove or rename this directory as it is required for export features.

---

## ⚙️ Local Environment Setup

### 1. Clone Repository
```bash
git clone <repository-url>
cd myarlion
````

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Configuration

Create `.env` file:

```bash
cp .env.example .env
```

Example `.env` configuration:

```env
APP_NAME=MyArlion
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=myarlion_db
DB_USERNAME=root
DB_PASSWORD=
```

Generate application key:

```bash
php artisan key:generate
```

---

## 🔐 Additional Environment Configuration

### Dashboard Aggregation

The application provides a **secured internal API** used to retrieve **full dashboard data** for **Visi Arlion**.

Add the following variables to `.env`:

```env
DASHBOARD_AGG_TOKEN=token_CEO
DASHBOARD_AGG_CACHE_ENABLED=true
DASHBOARD_AGG_CACHE_TTL=600
```

### Variable Explanation

| Variable                      | Description                                       |
| ----------------------------- | ------------------------------------------------- |
| `DASHBOARD_AGG_TOKEN`         | Shared secret token for dashboard aggregation API |
| `DASHBOARD_AGG_CACHE_ENABLED` | Enable or disable API response caching            |
| `DASHBOARD_AGG_CACHE_TTL`     | Cache lifetime (seconds)                          |

> ⚠️ **Important:**
>
> * Replace `token_CEO` with your own secure token
> * Store the token as a **secret variable**
> * Do **not** hardcode the token in source code

---

## 🔒 Dashboard Aggregation API

### Endpoint

```
POST {url}/api/v1/dashboard/full
```

* Replace `{url}` with the **Visi Arlion application URL**
* This endpoint returns **all dashboard data** used in Visi Arlion

### Authentication

The consuming service must send a request header containing the same token configured in `DASHBOARD_AGG_TOKEN`.

**Required Header:**

```
X-Dashboard-Token: <your_secret_token>
```

If the token is missing or invalid, the API will return:

```
401 Unauthorized
```

---

## 🧪 API Testing Examples

### cURL

```bash
curl -X POST http://127.0.0.1:8000/api/v1/dashboard/full \
  -H "Accept: application/json" \
  -H "X-Dashboard-Token: secrettoken"
```

### Axios (JavaScript)

```js
axios.post('/api/v1/dashboard/full', {
  year: 2025,
  month: '2025-10',
  modules: 'profit,target_achievement'
}, {
  headers: {
    'X-Dashboard-Token': 'your_secret_token',
    'Accept': 'application/json'
  }
}).then(res => {
  console.log(res.data.modules.profit);
});
```

---

## 📊 API Response

* Returns **all aggregated dashboard data**
* Data modules depend on request payload
* Caching is applied when enabled

---

## 🗄️ Database Setup (Current Practice)

### ⚠️ Important Note

Due to **PHP version limitations on the VPS**, database migrations are **not executed directly on the server**.

This repository now uses a **schema baseline** generated from a production-compatible database snapshot:

* Baseline schema file: `database/schema/mysql-schema.sql`
* Historical migrations before the baseline are intentionally **not kept** in `database/migrations`
* `database/migrations` is reserved only for **new migrations after the baseline**
* `database/migrations_drafts` remains excluded from normal `migrate` execution

### Current Workflow

1. Database terbaru dari live production di-export
2. Pada environment baru, database tersebut di-import
3. `.env` configuration is adjusted
4. Jalankan `php artisan migrate` untuk menerapkan **migration baru setelah baseline**

Important:

* Jangan menjalankan bootstrap database dengan asumsi full migrate dari nol
* Jangan mengembalikan migration historis yang sudah terwakili di `database/schema/mysql-schema.sql` ke `database/migrations`
* Jika membuat migration baru, file tersebut menjadi bagian dari jalur normal `php artisan migrate`

---

## 🚀 Future Development Recommendation

To improve maintainability and deployment consistency, it is strongly recommended to:

* Upgrade server PHP version to meet Laravel 10 requirements
* Enable Composer and CLI access on the server
* Keep using standard Laravel migration workflow for **new changes after import**:

  ```bash
  php artisan migrate
  php artisan db:seed
  ```

Benefits:

* Eliminates manual SQL handling
* Improves database version control
* Safer and repeatable deployments

---

## 📬 API Documentation

* API documentation is maintained using **Postman**
* Collections are shared internally
* Intended for internal integrations only

---

## 🧑‍💻 Intended Audience

This documentation is written for:

* Full-stack developers
* Backend developers
* Internal technical teams onboarding to the project

---

## 📄 License

This project is **proprietary** and intended for **internal company use only**.
Unauthorized distribution or public usage is prohibited.
