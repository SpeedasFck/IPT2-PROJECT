# Healthcare Essentials Shop

A Laravel 12 application for a small healthcare essentials retailer. It includes a responsive product catalogue, customer accounts, guest Cash-on-Delivery checkout, and protected shop administration.

## Requirements

- PHP 8.2 or later with PDO SQLite or PDO MySQL, OpenSSL, Mbstring, Tokenizer, XML, and Fileinfo
- Composer
- Node.js and npm
- MySQL 8+ for deployment, or SQLite for local development

## Local setup

From this directory, install dependencies and prepare the environment:

```powershell
composer install
npm install
Copy-Item .env.example .env
php artisan key:generate
```

The example environment uses SQLite. Ensure `database/database.sqlite` exists, then create the schema and sample catalog:

```powershell
New-Item -ItemType File -Path database/database.sqlite -Force
php artisan migrate --seed
```

To use MySQL instead, set `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env` before migrating. Keep credentials and `APP_KEY` out of source control.

Build the local Bootstrap 5 and storefront assets:

```powershell
npm run build
```

During frontend development, use `npm run dev` in a separate terminal while the Laravel server is running.

Set `SHOP_DELIVERY_FEE` in `.env` to the delivery fee approved by the business. The default is `0`; checkout labels this as unconfigured and does not pretend the estimate includes delivery.

Run the application:

```powershell
php artisan serve
```

Open `http://127.0.0.1:8000`.

## Implemented

- Responsive Bootstrap 5 storefront homepage with product categories, available catalog items, and an about section.
- Product listing with validated name search, category filtering, price range, sorting, and pagination.
- Product detail pages and related items.
- Category and product Eloquent models, decimal prices, inventory counts, active flags, and foreign-key constraints.
- Sample categories and products for layout and development only. Images are illustrative, and descriptions direct shoppers to manufacturer-provided instructions.
- Philippine peso display and Asia/Manila application timezone defaults.
- Secure registration, login/logout, rate limiting, customer profile/address editing, and password changes.
- Session cart with server-side stock validation and update/remove/clear operations.
- Guest and registered-customer Cash-on-Delivery checkout; prices and totals are recalculated server-side, inventory is reserved transactionally, and order-item names/prices are snapshotted.
- Customer order history and unguessable order confirmation URLs.
- Admin-only dashboard, product and category management, customer directory, order search/status updates, low-stock tracking, and database-backed sales reports.
- Admin order transitions with inventory restored on cancellation, only once.
- Validated product image uploads to Laravel's public storage disk. Run `php artisan storage:link` before serving uploaded images.
- Rate-limited contact form and editable draft terms, privacy, and returns pages.
- Focused feature coverage for catalog, auth, checkout, inventory, admin permissions, profile, and contact workflows.

## Create an administrator

Create an admin account through the interactive Artisan command. It prompts for the password without printing or storing it in source:

```powershell
php artisan app:create-admin owner@example.com
```

Run `php artisan storage:link` once to serve product image uploads.

## Run tests

```powershell
php artisan test
```

## Remaining before launch

An online payment gateway, password-reset email delivery, product image gallery, admin contact-message inbox, and automated customer order-confirmation email are not implemented. Raw payment card information is never collected. Confirm actual business identity, product details and imagery, delivery-fee rules, policies, and mail delivery settings before launch. Do not add medicines or regulated devices without the applicable Philippine licensing and dispensing requirements.