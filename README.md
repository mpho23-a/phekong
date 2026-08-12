# Phekong Stock Management System

A Laravel-based stock management system built for a business with a sales rep and two admins, each with distinct roles and permissions. Product stock levels can only be changed through a request-and-approval workflow, ensuring the person who physically manages the warehouse has final say over stock counts.

## Overview

The system solves a simple business problem: multiple people touch product data, but only one person (the warehouse-side admin) actually knows the real stock count. So instead of letting anyone edit quantity directly, updates go through an approval flow.

## Roles & Permissions

| Role | View Products | Edit Product Details | Request Stock Update | Approve/Reject Stock Requests | Log Daily Sales | View Sales Report | Manage Users |
|---|---|---|---|---|---|---|---|
| **Sales Rep** | ✅ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ |
| **Stock Admin** | ✅ | ✅ (name, description, price, threshold) | ✅ | ❌ | ❌ | ✅ | ❌ |
| **Approval Admin** | ✅ | ❌ | ❌ | ✅ | ❌ | ❌ | ✅ |

Roles and permissions are managed with [spatie/laravel-permission](https://spatie.be/docs/laravel-permission).

- **Sales Rep** — logs in and sees a read-only product list: name, description, price, and quantity, with a visual flag when stock is low. Can log a daily sales sheet (multiple products in one submission), which deducts sold quantities from stock immediately.
- **Stock Admin** — manages product details (not quantity directly), can submit a request to change stock quantity (e.g. after a delivery or stocktake), and reviews the sales report to track what's moving.
- **Approval Admin** — the person who actually knows what's in the warehouse. Reviews pending stock requests and approves or rejects them. Also manages user accounts (adding/removing sales reps and admins), since public registration is disabled.

Route-level middleware backs up every permission — even if a user manually types a restricted URL, they're blocked with a 403.

## Core Workflow

**Stock updates (approval-gated):**
1. Stock Admin submits a stock update request for a product (new quantity + optional reason).
2. Approval Admin receives a notification (in-app + email) and reviews the request on the **Pending Stock Requests** page.
3. Approval Admin approves or rejects it.
4. On approval, the product's actual quantity updates immediately.
5. If the new quantity falls at or below the product's low-stock threshold, both admins get a low-stock notification (in-app + email).

**Daily sales (direct, no approval needed):**
1. Sales Rep opens the sales sheet, enters quantity sold per product for the day, and submits.
2. Each submitted line item deducts directly from that product's stock quantity.
3. If a sale drops a product's quantity to or below its low-stock threshold, the same low-stock notification fires automatically.
4. Stock Admin can view and filter the sales report (by date range and/or product) to track performance and decide when to request a restock.

## Tech Stack

- **Framework:** Laravel
- **Auth scaffolding:** Laravel Breeze (Blade stack)
- **Roles & permissions:** spatie/laravel-permission
- **Frontend:** Blade + Tailwind CSS
- **Notifications:** Laravel Notifications (`database` + `mail` channels)
- **Timezone:** Africa/Johannesburg (SAST)
- **Database:** SQLite (local development and Render deployment)
- **Deployment:** Render, via Docker (bundles PHP, Composer, and Node in one container)

## Database Structure

**`products`**
- `name`, `description`, `price`, `quantity`, `low_stock_threshold`

**`stock_update_requests`**
- `product_id`, `current_quantity`, `requested_quantity`, `reason`
- `status` (`pending` / `approved` / `rejected`)
- `requested_by`, `approved_by`, `decided_at`

**`sales`**
- `product_id`, `sales_rep_id`, `quantity_sold`, `sale_date`, `notes`
- Submitting a sale deducts directly from the linked product's `quantity`.

**`users`** (extended with roles via spatie/laravel-permission)
- Standard Breeze fields, plus a role assignment (`sales_rep`, `stock_admin`, or `approval_admin`)

**`notifications`** (Laravel's default notifications table, for in-app alerts)

## Requirements

Before starting, make sure you have the following installed:

- **PHP** 8.2 or higher
- **Composer** (PHP dependency manager)
- **Node.js** and **npm**
- **Git**

No separate database server is required — this project uses **SQLite**, which ships with PHP and stores the entire database as a single file inside the project.

### Windows users

Install [Composer for Windows](https://getcomposer.org/download/) and [Node.js](https://nodejs.org/) (LTS version). PHP is often bundled with tools like [Laragon](https://laragon.org/) or [XAMPP](https://www.apachefriends.org/) if you don't already have it. Use **Git Bash**, **PowerShell**, or **CMD** — all commands in this guide work the same in any of them.

### macOS users

The easiest setup is via [Homebrew](https://brew.sh/):

```bash
brew install php composer node
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

The project includes an `.env.example` file with all the required variable names but no real values — you need your **own** `.env` file with your own mail credentials. Don't reuse anyone else's `.env` values.

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

Set the database connection in `.env` to SQLite:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

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

> Use a [Google App Password](https://myaccount.google.com/apppasswords) (requires 2-Step Verification enabled), not your regular Gmail password. Paste it without spaces. `MAIL_USERNAME` and `MAIL_FROM_ADDRESS` should be the same Gmail account.

### 4. Create the SQLite database file

Laravel needs this file to already exist — it won't create it automatically.

**macOS / Linux / Git Bash (Windows):**
```bash
touch database/database.sqlite
```

**Windows (Command Prompt):**
```cmd
type nul > database\database.sqlite
```

**Windows (PowerShell):**
```powershell
New-Item database\database.sqlite
```

### 5. Run migrations and seed data

```bash
php artisan migrate
php artisan db:seed
```

This seeds:
- 3 roles: `sales_rep`, `stock_admin`, `approval_admin`
- 3 test users (see below)
- 11 sample herbal products, several pre-set to trigger the low-stock flag

## Optional: Viewing the Database with a GUI

SQLite requires no separate server, but if you'd like to browse the data visually instead of using `php artisan tinker`, any of these free tools can open the `.sqlite` file directly:

- **[DB Browser for SQLite](https://sqlitebrowser.org/)** — free, cross-platform, built specifically for SQLite
- **[TablePlus](https://tableplus.com/)** — free tier, Windows/macOS, supports SQLite alongside other databases
- **VS Code extension:** search "SQLite Viewer" or "SQLite" in the Extensions panel

Just open `database/database.sqlite` from the project folder in any of these — no connection setup required.

## Optional: Switching to MySQL Instead of SQLite

This project defaults to SQLite for simplicity — no separate database server to install or manage. If you'd prefer to use MySQL instead (e.g. you already have it installed, or want to manage the database with a tool like phpMyAdmin, TablePlus, or MySQL Workbench), here's how to switch:

### 1. Install MySQL, if you don't already have it

**Windows:** Use [Laragon](https://laragon.org/) or [XAMPP](https://www.apachefriends.org/) — both bundle MySQL alongside PHP.

**macOS (Homebrew):**
```bash
brew install mysql
brew services start mysql
```

### 2. Create the database
Before migrating, the database itself must exist — Laravel only creates the tables inside it, not the database.

**Using the MySQL CLI:**
```bash
mysql -u root -p
```
Then inside the MySQL prompt:
```sql
CREATE DATABASE phekong;
exit;
```

**Or using a GUI tool** like TablePlus, phpMyAdmin (bundled with Laragon/XAMPP), or MySQL Workbench — just create a new database named `phekong` (or match whatever you set in `DB_DATABASE` below).

### 3. Update `.env`
Replace the SQLite connection settings with:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=phekong
DB_USERNAME=root
DB_PASSWORD=
```

> **Windows (Laragon/XAMPP):** default username is usually `root` with an empty password, unless you set one during install.
> **macOS (Homebrew):** default is also usually `root` with no password unless configured otherwise.

### 4. Clear cached config and re-migrate

```bash
php artisan config:clear
php artisan migrate:fresh --seed
```

From here, everything else in this guide works identically — Laravel doesn't care which database driver is active, only your `.env` changes. You can now connect to the `phekong` database directly from any MySQL GUI tool using the same host/username/password from step 3.

> **Note:** If you switch to MySQL locally, remember the Render deployment (see Deployment section below) is configured for SQLite via the Dockerfile. Switching your local setup to MySQL doesn't affect what's deployed — those are independent environments, each with their own `.env`/environment variables.

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

> Public registration is disabled — new accounts are provisioned either via the seeder, `php artisan tinker`, or by an Approval Admin using the **Manage Users** page once logged in.

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
Check `MAIL_MAILER` in `.env`. Use `MAIL_MAILER=log` to dump emails to `storage/logs/laravel.log` for testing without real delivery, or confirm your Gmail App Password is correct and pasted without spaces, and that `MAIL_USERNAME` matches `MAIL_FROM_ADDRESS`.

**`bash: composer: command not found` on Render**
Render's default (non-Docker) environment doesn't have PHP/Composer installed. Deploy using the included `Dockerfile` instead, which bundles PHP, Composer, and Node in one container.

**CSS/JS not loading on the deployed site, or a "mixed content" warning in browser dev tools**
Render terminates HTTPS before your app, so Laravel may generate asset links as `http://` even on a `https://` site, and browsers block the mismatch. Fixed via `URL::forceScheme('https')` in `AppServiceProvider` (already included) — confirm `APP_ENV=production` is set on Render for this fix to trigger.

**Duplicate `.env` keys (e.g. `CACHE_STORE` or `DB_CONNECTION` appearing twice)**
Only the last occurrence of a duplicated key takes effect, which can cause confusing behavior. Search your `.env` for repeated keys and remove the unused line.

## Deployment

Deployed to [Render](https://render.com) using Docker, so the container bundles PHP, Composer, and Node together — no need to rely on Render's runtime auto-detection.

### Dockerfile
The project includes a `Dockerfile` at the root that:
1. Installs PHP, Composer, and Node
2. Runs `composer install` and `npm run build`
3. Ensures the SQLite database file exists inside the container
4. On container start, runs `php artisan migrate:fresh --seed --force` (rebuilds and reseeds the database) before starting the server

> **Note:** Because the database is rebuilt fresh on every deploy/restart, this is ideal for a demo environment but means data doesn't persist long-term between deploys. For a production system with real ongoing data, swap SQLite for a persistent hosted database (e.g. Postgres) and remove `migrate:fresh` in favour of a plain `migrate --force`.

### Render environment variables
Set these in Render's dashboard (Settings → Environment), not in a committed `.env` file:

```
APP_KEY=<your app key>
APP_ENV=production
APP_DEBUG=false
APP_URL=https://<your-render-app>.onrender.com
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
CACHE_STORE=file
SESSION_DRIVER=file
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=youremail@gmail.com
MAIL_PASSWORD=your16charapppassword
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=youremail@gmail.com
MAIL_FROM_NAME="Phekong Stock System"
```

### Render service setup
- **Runtime:** Docker (should auto-detect the `Dockerfile`; if not, set it explicitly under Settings)
- **Build/Start commands:** not needed — the `Dockerfile` handles both via its `CMD` instruction
- Render's free tier has an ephemeral filesystem and spins down after inactivity, so a cold start on the first request after idle can take 30-50 seconds

### Known deployment gotcha: mixed content on assets
If CSS/JS assets fail to load with a browser "mixed content" warning, it's because Render terminates HTTPS at a reverse proxy, so Laravel may generate `http://` asset URLs internally even though the site is served over `https://`. Fixed via `AppServiceProvider`:

```php
// app/Providers/AppServiceProvider.php
use Illuminate\Support\Facades\URL;

public function boot(): void
{
    if ($this->app->environment('production')) {
        URL::forceScheme('https');
    }
}
```

## Project Structure Highlights

```
app/
  Http/Controllers/
    ProductController.php             # Product CRUD (stock_admin only)
    StockUpdateRequestController.php  # Request + approval workflow
    SaleController.php                # Daily sales sheet + sales report
    UserController.php                # User management (approval_admin only)
  Models/
    Product.php
    StockUpdateRequest.php
    Sale.php
  Notifications/
    StockRequestSubmitted.php
    LowStockAlert.php
  Providers/
    AppServiceProvider.php            # Forces HTTPS asset URLs in production
database/
  database.sqlite                     # SQLite database file
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
  sales/
    create.blade.php                  # Sales rep's daily sales sheet
    index.blade.php                   # Stock admin's sales report
  users/
    index.blade.php                   # Approval admin's user list
    create.blade.php                  # Approval admin's add-user form
routes/
  web.php
Dockerfile                            # Render deployment container
```

## Author

Mpho Mohlabane