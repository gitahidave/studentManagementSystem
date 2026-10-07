# Student Management System Dashboard

A Laravel dashboard assignment built with Blade, Bootstrap 5, Bootstrap Icons, and Laravel Breeze authentication.

## Requirements

- PHP 8.2 or later and Composer
- Node.js and npm
- A database supported by Laravel (the example environment uses SQLite)

## Setup

```sh
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm install
npm run build
php artisan serve
```

Open the URL shown by `php artisan serve`, register a user, and sign in to reach `/dashboard`. Set `DB_CONNECTION` and the related database values in `.env` if using a database other than SQLite.

For frontend development, run `npm run dev` in a second terminal while the Laravel server is running.

## Dashboard and authentication

- Laravel Breeze provides registration, login, email verification, profile management, and logout.
- The dashboard and its sidebar destinations require authentication.
- Dashboard summary values are passed to the view by `DashboardController`.
- The shared Blade layout includes the navbar, responsive collapsible sidebar, notification badge, and authenticated user menu.
- Students, courses, fees, payments, reports, and settings currently have authenticated placeholder pages so their navigation links work.

Run the automated checks with:

```sh
php artisan test
npm run build
```
