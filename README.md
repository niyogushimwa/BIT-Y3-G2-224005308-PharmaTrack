# PharmaTrack — Online Pharmacy Stock Alert System

**Student:** NIYOGUSHIMWA REMY · **ID:** 224005308  
**Stack:** PHP 8, MySQL/MariaDB, HTML, CSS and vanilla JavaScript

PharmaTrack is a responsive pharmacy inventory application for tracking medicines, suppliers, expiry dates, low-stock thresholds and stock alerts. It uses PHP server-rendered pages, PDO prepared statements, form validation, session authentication, CSRF tokens and small authenticated JSON APIs.

## Features

- Sign in, pharmacist self-registration and administrator-managed accounts with hashed passwords.
- Full create, read, update and delete workflows for medicines, suppliers, alerts and users.
- Automatic low-stock and expired-medicine alerts, alert resolution and history.
- Searchable/filterable medicine catalog and live JSON-backed medicine search.
- Dashboard with inventory summaries, expiry watch, category breakdown and recent alerts.
- Responsive interface, browser-side form constraints, server-side validation and confirmation prompts.
- Administrator-only user management; pharmacists can manage stock but cannot manage accounts.

## Requirements

- XAMPP (Apache and MySQL) or another PHP 8+ and MySQL/MariaDB environment.
- PHP extension `pdo_mysql` enabled.
- A modern browser with JavaScript enabled for the live search and periodic dashboard refresh.

## Run locally with XAMPP

1. Place the project folder under `C:\xampp\htdocs`. For this checkout, the URL is `http://localhost/Pharmacy-Stock-Alert-System2/pharmacy-stock-alert/public/`; if you rename or move the folder, adjust the URL to match.
2. Start Apache and MySQL in the XAMPP control panel.
3. Open `http://localhost/phpmyadmin`, select **Import**, and import `database/schema.sql`. The schema creates the `pharmacy_stock_alert` database and its four tables without dropping existing data.
4. Import `database/seed.sql` once to add sample records and demo accounts. The seed contains sample dates for expired, expiring and low-stock medicines.
5. If your local MySQL credentials are not the XAMPP defaults (`root` with an empty password), set `DB_HOST`, `DB_NAME`, `DB_USER` and `DB_PASS` in the Apache/PHP environment before starting the application.
6. Visit `http://localhost/pharmacy-stock-alert/public/`.

The application defaults to `127.0.0.1`, database `pharmacy_stock_alert`, user `root` and an empty password. Change these via environment variables for another installation; never put production credentials in source control.

### Demo accounts

| Role | Username | Password |
|---|---|---|
| Administrator | `admin` | `admin123` |
| Pharmacist | `remy` | `remy123` |
| Pharmacist | `claire` | `claire123` |

Change demo passwords before using the application outside a local demonstration. New public registrations always receive the pharmacist role.

## Database design

The schema has four normalized entities:

- `users`: unique username and email; password hash, role and creation timestamp.
- `suppliers`: supplier contact details.
- `medicines`: product, category, stock, reorder threshold, expiry and nullable supplier foreign key.
- `stock_alerts`: alert type, medicine foreign key, trigger time, resolution state and optional resolution time.

Each table has a primary key; `medicines.supplier_id` references `suppliers.supplier_id`, and `stock_alerts.medicine_id` references `medicines.medicine_id`. Supplier and medicine details are stored in their respective parent tables rather than copied into alerts. Supplier deletion sets a medicine's supplier to `NULL`; medicine deletion cascades to its alert history. This keeps the design in third normal form.

## Routes and API

Pages use the front controller at `public/index.php?r=...`. Main routes include `dashboard`, `medicines`, `suppliers`, `alerts` and `users`. Forms submit through separate POST-only save/delete/resolve routes.

- `GET /public/api/medicines.php?q=para` — authenticated JSON medicine search.
- `GET /public/api/stats.php` — authenticated dashboard statistics and alert synchronization.

Both APIs require a valid signed-in session and return JSON errors with appropriate HTTP status codes.

## Security notes

- Database writes and user search use PDO prepared statements.
- User passwords are stored using PHP `password_hash()` and checked with `password_verify()`.
- All state-changing application forms use session-backed CSRF tokens; deletion and logout are POST-only.
- HTML output is escaped with the shared `e()` helper. Live search renders API values using `textContent`, not HTML injection.
- Server-side validators enforce field lengths, allowed roles, numeric stock values and valid email/date formats.
- PHP session cookies are HTTP-only and `SameSite=Lax`; strict mode and session-ID rotation are enabled.
- User account administration is restricted to administrators, with protections against deleting or demoting the last/current administrator.

## Manual demo and test checklist

1. Sign in as `admin` and confirm the dashboard shows stock, expiry and active alert summaries.
2. Search the medicines page; verify the live lookup returns JSON-backed results as you type.
3. Add, edit and delete a medicine. Confirm low-stock and expired alerts synchronize on the dashboard or alerts page.
4. Add, edit and delete a supplier; confirm medicines linked to a removed supplier remain in the catalog.
5. Resolve and delete an alert; confirm the status filters show active, resolved and all history.
6. As an administrator, add/edit/delete another user. Confirm deleting the signed-in user and deleting/demoting the last admin are blocked.
7. Try invalid dates, negative stock, malformed email and mismatched passwords; confirm server-side errors are shown.
8. Sign out; verify protected pages and APIs redirect/return unauthorized, and that form submissions without a valid CSRF token are rejected.
9. Resize the browser to mobile width and confirm navigation, tables and forms remain usable.

Run the lightweight, database-independent smoke tests from the project directory with:

```powershell
php tests\run.php
```

PHP syntax can also be checked with:

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
```

Database-backed manual checks require the schema and sample data to be imported first.
