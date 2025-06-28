# LCN Management System

A modern PHP web application for managing LCN/channel mappings, IRD inventory, SID/channel/LCN CRUD, and IRD Challan uploads for a cable/broadcast operator.

## Features
- Secure login (bcrypt, session-based)
- City-based filtering for all data
- Channel, LCN, SID, IRD, and mapping CRUD
- Activity log for all business actions
- IRD Challan PDF upload and listing
- Modern UI (Tailwind CSS)
- All actions routed through a front controller (`index.php`) using FastRoute
- All business logic in `src/Controllers/`, all views in `views/`
- Static assets in `public/` or `uploads/`

## Routing
- All requests go through `index.php` (front controller)
- Routing is handled by FastRoute (see `index.php`)
- Legacy files are being phased out; all new features use controller-based routing

## Initial Setup
1. Clone the repo and run `composer install`
2. Copy `.env.example` to `.env` and set DB credentials
3. Ensure `uploads/` and `uploads/challans/` are writable
4. Import the SQL schema (see `/include/meghbela_lcn_db_kol.sql`)
5. Start the server (`php -S localhost:8000 server.php` or use Apache/Nginx)
6. Login at `/login`

## Main Entry Points
- `index.php` (all web requests)
- `server.php` (for PHP built-in server, static file routing)

## Directory Structure
- `src/Controllers/` — All controllers
- `views/` — All views/partials
- `public/` — Public assets (css/js/images)
- `uploads/` — User uploads (PDFs, etc.)
- `config/` — App and DB config
- `vendor/` — Composer dependencies
