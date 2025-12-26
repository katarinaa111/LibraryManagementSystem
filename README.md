# Library Management System

Single-page Library Management System with a PHP (Flight) REST API + MySQL/MariaDB backend and a static HTML/JS frontend.

## Live (Production)

- Frontend: https://library-katarina-soja-frontend-h7qul.ondigitalocean.app/
- Backend API (used by non-localhost frontend): https://library-db-katarina-soja-backend-kug26.ondigitalocean.app/

## Repository Structure

- `backend/` PHP REST API (FlightPHP), Swagger/OpenAPI docs, auth middleware
- `frontend/` Static SPA (jQuery + Axios), views, controllers, utilities
- `docs/library_db.sql` Database schema (and initial structure)

## Backend (API)

### Requirements

- PHP 8.x
- Composer
- MySQL/MariaDB
- Apache (recommended with XAMPP) with `mod_rewrite` enabled

### Local Setup (XAMPP)

1. Place the repository under XAMPP `htdocs` (this repo already lives at):
   - `.../htdocs/KatarinaSoja/LibraryManagementSystem`
2. Create/import the database:
   - Import `docs/library_db.sql` into your MySQL/MariaDB server
   - Default database name in code: `library_db`
3. Install backend dependencies:
   - `cd backend`
   - `composer install`
4. Configure database connection and JWT secret:
   - Update defaults in `backend/config.php`, or set environment variables:
     - `DB_HOST` (default `127.0.0.1`)
     - `DB_PORT` (default `3306`)
     - `DB_NAME` (default `library_db`)
     - `DB_USER` (default `root`)
     - `DB_PASSWORD` (default empty)
     - `JWT_SECRET` (default `your_key_string`)

Local base URL (matches `frontend/utils/constants.js:2`):

- `http://localhost/KatarinaSoja/LibraryManagementSystem/backend`

### Authentication

- `POST /auth/register` and `POST /auth/login` are public (see `backend/index.php:32`)
- All other routes require an `Authentication` header containing the JWT
- JWTs are issued for 10 hours (`backend/services/AuthService.php:58`)

### Swagger / OpenAPI Docs

- Swagger UI (local): `http://localhost/KatarinaSoja/LibraryManagementSystem/backend/public/v1/docs/`
- OpenAPI JSON (local): `http://localhost/KatarinaSoja/LibraryManagementSystem/backend/public/v1/docs/swagger.php`

### Example Requests (cURL)

Set a local base URL:

```bash
BASE="http://localhost/KatarinaSoja/LibraryManagementSystem/backend"
```

Register a new user (role defaults to `member` on register):

```bash
curl -sS "$BASE/auth/register" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "demo",
    "firstName": "Demo",
    "lastName": "User",
    "email": "demo@example.com",
    "password": "demo_password"
  }'
```

Login and capture the token:

```bash
TOKEN="$(curl -sS "$BASE/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"demo@example.com","password":"demo_password"}' \
  | php -r '$j=json_decode(stream_get_contents(STDIN), true); echo $j["data"]["token"] ?? "";')"
echo "$TOKEN"
```

Call a protected endpoint:

```bash
curl -sS "$BASE/books" -H "Authentication: $TOKEN"
```

## Frontend (SPA)

### Requirements

- Any static web server (XAMPP Apache is simplest locally)

### Local Setup (XAMPP)

The frontend is static and does not require a build step. Serve the `frontend/` directory over HTTP.

- App: `http://localhost/KatarinaSoja/LibraryManagementSystem/frontend/`
- Login page: `http://localhost/KatarinaSoja/LibraryManagementSystem/frontend/auth/login.html`

### API Base URL Configuration

The frontend automatically switches API base URL based on hostname (`frontend/utils/constants.js:2`):

- When running on `localhost`, it uses:
  - `http://localhost/KatarinaSoja/LibraryManagementSystem/backend`
- Otherwise, it uses production:
  - `https://library-db-katarina-soja-backend-kug26.ondigitalocean.app/`

To point the frontend at a different API, update `Constants.PROJECT_BASE_URL` in `frontend/utils/constants.js`.

### Auth in the Frontend

- After login, the JWT is stored in `localStorage` under `user_token`
- Requests include the token in the `Authentication` header (`frontend/utils/rest-client.js:6`)
