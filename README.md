# Employee Management System (Core PHP + MVC + Bootstrap)

## Stack
- Core PHP (OOP, Namespaces)
- MySQL (PDO)
- Bootstrap 5
- Minimal JS (Fetch API)

## Setup
1. Create DB/tables:
   - Import `database/schema.sql` in MySQL.
2. Configure DB credentials + base path in `config/config.php`.
3. Generate composer autoload (optional):
   - `composer dump-autoload`
4. Start Apache and open:
   - `http://localhost/Ems/public`

## Default Admin
- Email: `admin@ems.local`
- Password: `Admin@123`

## Key Routes
- `GET /admin/login`
- `GET /admin/dashboard`
- `GET /admin/employees`
- `GET /admin/employees/create`
- `GET /admin/employees/show/{id}`
- `GET /admin/employees/edit/{id}`

## Features Implemented
- Secure auth (sessions, `password_hash`, `password_verify`).
- CSRF protection.
- Admin dashboard with stats + recent employees.
- Employee CRUD (create/edit/view/delete).
- Secure profile picture upload (jpg/png/webp, <= 2MB) stored in `storage/uploads/profiles` and served via `/files/profile/{file}`.
- Prepared statements + XSS-safe output in views.