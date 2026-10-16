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
- Student registration is available to authenticated users at `/students`. Each student has a first name, second name, and course.
- Authenticated users can create, list, edit, and delete courses at `/courses`. Courses with students cannot be deleted.
- The required application tables are `courses` (`name`), `students` (`first_name`, `second_name`, `course_id`), and `fees` (`student_id`, `amount`, `fee_type`, `status`).
- Before migrating an existing database, make a backup. The schema update removes legacy student email/phone fields, course metadata, and enrollment/semester/academic-year tables after preserving each student's course assignment.
- Courses, fees, payments, reports, and settings currently have authenticated placeholder pages so their navigation links work.

Run the automated checks with:

```sh
php artisan test
npm run build
```
