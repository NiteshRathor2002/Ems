# Employee Management System (Core PHP + OOP + AJAX)

## Stack
- Core PHP (OOP, Namespaces)
- MySQL
- AJAX (Fetch API)
- HTML/CSS

## Setup
1. Create DB/tables:
   - Import `database/schema.sql` in MySQL.
2. Configure DB credentials in `config/config.php`.
3. Generate composer autoload (optional but recommended):
   - `composer dump-autoload`
4. Start Apache and open:
   - `http://localhost/Ems/public`

## Default Admin
- Email: `admin@ems.local`
- Password: `Admin@123`

## Features Implemented
- Employee signup with dynamic qualifications + experiences.
- Secure login/logout.
- Profile page with AJAX update (email non-editable).
- Secure image upload (`jpg/png/webp`, <= 2MB).
- Admin login + employee list.
- CSRF protection, prepared statements, password hashing.
