Uyayi — Infant Essentials Management System

Overview
- A Laravel-based web platform (Laravel + MySQL + XAMPP) for managing and selling infant essentials (diapers, toiletries, baby care supplies).
- Admins manage users, products, inventory, orders, payments, and expenses. Customers browse, order, and track purchases.

This workspace contains scaffolding: DB schema, example models, controllers, routes, and setup instructions to bootstrap the Laravel project.

Quick setup (recommended)
1. Ensure XAMPP is running (Apache + MySQL).
2. Install Composer and PHP 8+.
3. From `c:\xampp\htdocs` run:

```bash
composer create-project --prefer-dist laravel/laravel uyayi
cd uyayi
``` 

4. Copy the files from this scaffold (README, database/schema.sql, app/ files, routes/web.php, .env.example) into the Laravel project's corresponding paths.
5. Create a database `uyayi_db` in phpMyAdmin or via CLI, then import `database/schema.sql`.
6. Update `.env` DB settings (use `.env.example` as reference).
7. Run `php artisan key:generate` and `php artisan migrate` (if you convert SQL into migrations) or use raw SQL import.
8. Seed demo data (optional): create seeders or import sample inserts.

What's included
- [database/schema.sql](database/schema.sql): 3NF MySQL schema (users, roles, products, inventory, restocks, orders, order_items, payments, expenses).
- Example `app/Models` and `app/Http/Controllers` files demonstrating logic for admin and customer functional requirements.
- `routes/web.php` with example routes.

Next steps I can do for you
- Convert `database/schema.sql` into Laravel migrations and seeders.
- Implement Blade views (frontend) for catalog, cart, admin dashboard.
- Wire authentication using Laravel Breeze or Jetstream.

Tell me which next step you'd like me to do.