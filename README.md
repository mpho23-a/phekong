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

## Requirements

Before starting, make sure you have the following installed:

- **PHP** 8.2 or higher
- **Composer** (PHP dependency manager)
- **Node.js** and **npm**
- **MySQL** (or use Postgres if deploying to Render — see Deployment section)
- **Git**

### Windows users

The easiest way to get PHP, Composer, and MySQL running together is via [Laragon](https://laragon.org/) or [XAMPP](https://www.apachefriends.org/). Laragon is recommended — it bundles PHP, MySQL, and Composer, and gives you a one-click "Start All" for your local server and database.

- Install [Composer for Windows](https://getcomposer.org/download/) if not bundled with your local dev tool.
- Install [Node.js](https://nodejs.org/) (LTS version).
- Use **Git Bash**, **PowerShell**, or **CMD** to run the commands in this guide — they all work the same way.
- If using Laragon, place the project inside Laragon's `www` folder, or run everything through Laragon's terminal to make sure PHP/MySQL paths are set correctly.

### macOS users

The easiest setup is via [Homebrew](https://brew.sh/):

```bash
brew install php composer node mysql
brew services start mysql
```

Then continue with the standard steps below using Terminal (or iTerm).

## Setup & Installation

### 1. Clone and install dependencies

```bash
git clone <repo-url>
cd phekong
composer install
npm install
```

### 2. Environment setup

The project includes an `.env.example` file with all the required variable names but no real values — you need your **own** `.env` file with your own local database and mail credentials. Don't reuse anyone else's `.env` values (especially mail credentials).

Create your `.env` file by copying the example:

**macOS / Linux / Git Bash (Windows):**
```bash
cp .env.example .env
```

**Windows (Command Prompt):**
```cmd
copy .env.example .env
```

**Windows (PowerShell):**
```powershell
Copy-Item .env.example .env
```

Then generate the app encryption key:

```bash
php artisan key:generate
```

Open the new `.env` file in your code editor and fill in your own values for the database and mail sections below — don't just copy someone else's filled-in `.env`, since database credentials, mail passwords, and app keys should be unique per environment.

Configure your database in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=phekong
DB_USERNAME=root
DB_PASSWORD=
```

> **Windows (Laragon/XAMPP) users:** default MySQL username is usually `root` with an empty password, unless you set one during install.
> **macOS (Homebrew) users:** default is also usually `root` with no password unless configured otherwise.

Set the app timezone to South Africa — this is set in the config file, not `.env`:

```php
// config/app.php
'timezone' => 'Africa/Johannesburg',
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

### 4. Create the database

Before migrating, make sure the database itself exists — Laravel doesn't create it for you, only the tables inside it.

**Using the MySQL CLI (Windows Git Bash, macOS Terminal, or Linux):**
```bash
mysql -u root -p
```
Then inside the MySQL prompt:
```sql
CREATE DATABASE phekong;
exit;
```

**Or using a GUI tool** like TablePlus, phpMyAdmin (bundled with Laragon/XAMPP), or Sequel Ace (macOS) — just create a new database named `phekong` (or match whatever you set in `DB_DATABASE`).

### 5. Run migrations and seed data

```bash
php artisan migrate
php artisan db:seed
```

This seeds:
- 3 roles: `sales_rep`, `stock_admin`, `approval_admin`
- 3 test users (see below)
- 11 sample herbal products, several pre-set to trigger the low-stock flag

### 6. Confirm role middleware is registered

The role-based route protection depends on an alias registered in `bootstrap/app.php`. This should already be committed in the repo, but if you're setting the project up from scratch or roles aren't restricting access correctly, confirm this block exists:

```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
    ]);
})
```

### 7. Confirm public registration is disabled

Since accounts are provisioned via the seeder (not self-registration), the register routes should be commented out in `routes/auth.php`. This should already be in the repo — just confirm if you hit unexpected behavior:

```php
// routes/auth.php
Route::middleware('guest')->group(function () {
    // Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    // Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    // ...forgot password routes stay active
});
```

### 8. Build frontend assets (production)

```bash
npm run build
```

### 9. Run the app

For active development (runs the PHP server, queue listener, log viewer, and Vite asset watcher together):

```bash
composer run dev
```

Or, if you just need the server without the extras:

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

## Troubleshooting

**`SQLSTATE[42S02]: Base table or view not found: 'cache'`**
Run `php artisan cache:table && php artisan migrate`, or set `CACHE_STORE=file` in `.env` and run `php artisan config:clear`.

**`Class "App\Notifications\StockUpdateRequest" not found` (or similar "Class X not found" pointing at the wrong namespace)**
A model class is missing its `use` import at the top of the file. Add e.g. `use App\Models\StockUpdateRequest;` or `use App\Models\Product;` above the class declaration.

**`The GET method is not supported for route ... Supported methods: POST`**
Something is linking to a POST-only route with a plain `<a href="">` instead of submitting a `<form method="POST">`. Check the relevant Blade view for a stray anchor tag.

**Duplicate stock request rows after one submission**
Caused by double-clicking submit or refreshing after a POST. Disable the submit button on click:
```blade
<form ... onsubmit="this.querySelector('button[type=submit]').disabled = true;">
```

**Styling looks broken or unstyled buttons after editing Blade files**
Tailwind only compiles classes present at build time. Run `npm run build` (or keep `composer run dev` running) and hard-refresh the browser (Ctrl+Shift+R / Cmd+Shift+R).

**Emails not arriving**
Check `MAIL_MAILER` in `.env`. Use `MAIL_MAILER=log` to dump emails to `storage/logs/laravel.log` for testing without real delivery, or confirm your Gmail App Password is correct and pasted without spaces.

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