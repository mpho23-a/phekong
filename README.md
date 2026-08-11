# Phekong Stock Management System

A Laravel-based stock management system built for a business with a sales rep and two admins, each with distinct roles and permissions. Product stock levels can only be changed through a request-and-approval workflow, ensuring the person who physically manages the warehouse has final say over stock counts.

## Overview

The system solves a simple business problem: multiple people touch product data, but only one person (the warehouse-side admin) actually knows the real stock count. So instead of letting anyone edit quantity directly, updates go through an approval flow.

## Roles & Permissions

| Role | Can View Products | Can Edit Product Details | Can Request Stock Update | Can Approve/Reject Stock Requests |
|---|---|---|---|---|
| **Sales Rep** | ✅ | ❌ | ❌ | ❌ |
| **Stock Admin** | ✅ | ✅ (name, description, price, threshold) | ✅ | ❌ |
| **Approval Admin** | ✅ | ❌ | ❌ | ✅ |

Roles and permissions are managed with [spatie/laravel-permission](https://spatie.be/docs/laravel-permission).

- **Sales Rep** — logs in and sees a read-only product list: name, description, price, and quantity, with a visual flag when stock is low.
- **Stock Admin** — manages product details (not quantity directly) and can submit a request to change stock quantity — e.g. after a delivery or stocktake.
- **Approval Admin** — the person who actually knows what's in the warehouse. Reviews pending stock requests and approves or rejects them. Product quantity only changes once a request is approved.

Route-level middleware backs up every permission — even if a user manually types a restricted URL, they're blocked with a 403.

## Core Workflow

1. Stock Admin submits a stock update request for a product (new quantity + optional reason).
2. Approval Admin receives a notification (in-app + email) and reviews the request on the **Pending Stock Requests** page.
3. Approval Admin approves or rejects it.
4. On approval, the product's actual quantity updates immediately.
5. If the new quantity falls at or below the product's low-stock threshold, both admins get a low-stock notification (in-app + email).

## Tech Stack

- **Framework:** Laravel
- **Auth scaffolding:** Laravel Breeze (Blade stack)
- **Roles & permissions:** spatie/laravel-permission
- **Frontend:** Blade + Tailwind CSS
- **Notifications:** Laravel Notifications (`database` + `mail` channels)
- **Timezone:** Africa/Johannesburg (SAST)

## Database Structure

**`products`**
- `name`, `description`, `price`, `quantity`, `low_stock_threshold`

**`stock_update_requests`**
- `product_id`, `current_quantity`, `requested_quantity`, `reason`
- `status` (`pending` / `approved` / `rejected`)
- `requested_by`, `approved_by`, `decided_at`

**`notifications`** (Laravel's default notifications table, for in-app alerts)

## Setup & Installation

### 1. Clone and install dependencies

```bash
git clone <repo-url>
cd phekong
composer install
npm install
```

### 2. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

Configure your database in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=phekong
DB_USERNAME=root
DB_PASSWORD=
```

Set the app timezone to South Africa:

```env
APP_TIMEZONE=Africa/Johannesburg
```

### 3. Mail configuration (Gmail SMTP)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=youremail@gmail.com
MAIL_PASSWORD=your16charapppassword
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=youremail@gmail.com
MAIL_FROM_NAME="Phekong Stock System"
```

> Use a [Google App Password](https://myaccount.google.com/apppasswords) (requires 2-Step Verification enabled), not your regular Gmail password. Paste it without spaces.

### 4. Run migrations and seed data

```bash
php artisan migrate
php artisan db:seed
```

This seeds:
- 3 roles: `sales_rep`, `stock_admin`, `approval_admin`
- 3 test users (see below)
- 11 sample herbal products, several pre-set to trigger the low-stock flag

### 5. Build frontend assets

```bash
npm run build
```

For active development with live reload:

```bash
npm run dev
```

### 6. Serve the app

```bash
php artisan serve
```

Visit `http://localhost:8000` — you'll be redirected to login.

## Test Accounts

| Role | Email | Password |
|---|---|---|
| Sales Rep | sales@test.com | password |
| Stock Admin | stock@test.com | password |
| Approval Admin | approve@test.com | password |

> Public registration is disabled — accounts are provisioned via the seeder or `php artisan tinker`, keeping access controlled.

## Notifications

Two events trigger notifications, delivered via both **database** (in-app) and **mail** (email) channels:

1. **Stock Request Submitted** — sent to all Approval Admins when a Stock Admin submits a request.
2. **Low Stock Alert** — sent to all Approval Admins when a product's quantity drops to or below its threshold (fires automatically via a model event on `Product`).

## Deployment

Deployable to Render (or similar free-tier platforms). Since Render's free tier no longer offers MySQL, switch `DB_CONNECTION` to `pgsql` for deployment — no code changes required, Laravel supports Postgres natively.

## Project Structure Highlights

```
app/
  Http/Controllers/
    ProductController.php          # Product CRUD (stock_admin only)
    StockUpdateRequestController.php  # Request + approval workflow
  Models/
    Product.php
    StockUpdateRequest.php
  Notifications/
    StockRequestSubmitted.php
    LowStockAlert.php
database/
  migrations/
  seeders/
    RoleSeeder.php
    ProductSeeder.php
resources/views/
  products/
    index.blade.php
    create.blade.php
    edit.blade.php
  stock-requests/
    index.blade.php
routes/
  web.php
```

## Author

Mpho Mohlabane