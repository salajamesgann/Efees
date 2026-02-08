# Efees - Student Fee Management System

## Overview

Efees is a web application developed using Laravel for managing student fees in an educational institution. It supports multiple user roles including students, staff, and administrators, providing role-based access to features like fee tracking, user management, and notifications.

## Installation and Setup

1. Clone the repository: `git clone <repo-url>`
2. Install dependencies: `composer install`
3. Copy environment file: `cp .env.example .env`
4. Generate application key: `php artisan key:generate`
5. Configure database in `.env` file (set DB_CONNECTION, DB_HOST, etc.)
6. Run migrations and seeders: `php artisan migrate --seed`
7. Install frontend dependencies: `npm install`
8. Build assets: `npm run dev` or `npm run build`
9. Start the server: `php artisan serve`
10. Visit `http://localhost:8000` in your browser.

Note: Ensure you have PHP, Composer, Node.js, and a database server (e.g., MySQL) installed.

## Key Features

- **User Authentication**: Login, signup (for students), logout.
- **Role-Based Dashboards**: Separate dashboards for students, staff, and admins.
- **Student Management**: Admins can create, edit, and delete student records.
- **Fee Management**: Track fee records, send reminders, approve payments.
- **Profile Management**: Students can update their personal information.
- **Notifications**: Real-time notifications for fee reminders and approvals.

## User Roles

- **Student**: View personal dashboard with upcoming fees, transactions, notifications; update profile.
- **Staff**: View student list, search students, send fee reminders, approve payments.
- **Admin**: Manage student records (CRUD), access admin dashboard.

## Database Structure

### Main Tables

- **users**: Stores user credentials and role information (user_id, email, password, role_id, roleable_type, roleable_id).
- **roles**: Defines user roles (role_id, role_name, description).
- **students**: Student details (student_id, first_name, middle_initial, last_name, contact_number, sex, year, section, address, profile_picture_url).
- **admins**: Admin details (admin_id, first_name, MI, last_name, contact_number, department, position).
- **staff**: Staff details (staff_id, first_name, MI, last_name, contact_number, department, position, salary).
- **fee_records**: Fee tracking (record_id, fee_id, balance, status, timestamps; foreign key to student_id).

Additional tables: sessions, cache, cache_locks, notifications, payment_transactions (if implemented).

## Routes and Endpoints

### Authentication
- GET /login - Login page
- POST /authenticate - Process login
- GET /signup - Signup page
- POST /register - Process registration
- POST /logout - Logout

### Dashboards
- GET /user_dashboard - Student dashboard
- GET /admin_dashboard - Admin dashboard
- GET /staff_dashboard - Staff dashboard

### Student Profile
- GET /student/profile - View profile
- POST /student/profile - Update profile

### Staff Actions
- POST /staff/remind/{student} - Send reminder
- POST /staff/approve/{student} - Approve payments

### Admin Student Management
- GET /admin/students - List students
- POST /admin/students - Create student
- GET /admin/students/{student}/edit - Edit form
- PUT /admin/students/{student} - Update student
- DELETE /admin/students/{student} - Delete student

## Usage Guide

### For Students
1. Signup or login.
2. Access dashboard to view fees and notifications.
3. Update profile via /student/profile.

### For Staff
1. Login as staff.
2. Use staff dashboard to search students, send reminders, approve fees.

### For Admins
1. Login as admin.
2. Manage students via /admin/students.

## Additional Notes
- The system uses Laravel's Eloquent ORM for database interactions.
- Frontend uses Blade templates and Vite for asset management.
- For development, use `npm run dev` for hot reloading.

For more details, refer to the source code or contact the developer.

## Maintenance Notes

- Fix: Add Charge create page failed due to missing `layouts.admin` view. The page `resources/views/admin/fees/charges/create.blade.php` was updated to a standalone layout to ensure it renders reliably.
- Validation and feedback: The create page now shows validation errors and success/error flash messages. It also displays a notice if the local `additional_charges` table is missing, indicating Supabase fallback storage.
- Tests: Added feature tests for the Add Charge flow in `tests/Feature/AdditionalChargeCreateTest.php` covering page load, Supabase fallback storage path, and validation failures. Run tests with `php artisan test` or `vendor/bin/phpunit`.
