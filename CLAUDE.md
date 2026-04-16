# Care OS — Project Context

## What is this?

Care OS is a Laravel-based care home management system for **Omega Life UK**. We're rebuilding features from a Base44/React app (CareRoster) into this existing Laravel codebase. The CareRoster app is reference/spec only — everything gets built in Laravel Blade.

## Tech Stack

- **PHP 8.5**, **Laravel** (Blade views, Eloquent ORM)
- **MySQL 9.6** — Database: `scits_v2-35313139b6a7`
- **jQuery** for frontend JS (no React/Vue in Care OS)
- **Bootstrap 3** for UI components

## Local Setup

```bash
# Start the server
php artisan serve
# → http://127.0.0.1:8000

# Login: komal / 123456, house: Aries (Komal Gautam, Admin ID 194)
```

**.env essentials:**
- `DB_HOST=127.0.0.1`, `DB_USERNAME=root`, `DB_PASSWORD=` (empty)
- `APP_URL=http://127.0.0.1:8000`

**Fixes already applied (don't redo):**
- Symlink `public/public → public/` for asset paths
- `error_reporting(E_ALL & ~E_DEPRECATED)` in `public/index.php`
- `Pdo\Mysql::ATTR_SSL_CA` in `config/database.php` (PHP 8.5 fix)

## Git Conventions

- **Local branch:** `komal`
- **Push:** `git push origin komal:main` to `OmegaLifeUK/Care-OS`
- **Commits:** descriptive message + `Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>`
- **Never** use `git add -A` — add specific files only

## Development Process

Use `/careos-workflow` for every feature. It runs the full pipeline:

```
PLAN → SCAFFOLD → BUILD → TEST → DEBUG → REVIEW → AUDIT → PROD-READY → PUSH
```

- Read `docs/logs.md` at the start of every session for prior context
- Log every action to `docs/logs.md` with teaching notes
- Save session history to `sessions/sessionN.md` at the end of each session
- Phase details are in `phases/phase1.md`

## Key Codebase Patterns

**Multi-tenancy:** Every DB query MUST filter by `home_id`. Admin users have comma-separated `home_id` (e.g., `"8,18,1,9,11,12"`). Use `explode(',', $homeIds)[0]` to get the first home.

**Soft deletes:** Use `is_deleted` flag (not Laravel's `SoftDeletes`) for backwards compatibility with existing data.

**Model location:** New models go in `app/Models/`. If an alias exists at `app/ModelName.php`, keep it as `class ModelName extends \App\Models\ModelName {}`.

**Service layer:** Business logic goes in `app/Services/`, not in controllers. Controllers call service methods.

**Auth middleware:** `checkUserAuth` compares `csrf_token()` with `session_token` from the user record. In tests, use `->withoutMiddleware()` for non-auth tests.

**User roles:** `user_type === 'A'` for admin. The column is `user_type`, NOT `type`.

**URLs in Blade:** Always use `{{ url('/path') }}`, never hardcoded domains.

**Blade escaping:** Always `{{ }}`, never `{!! !!}` for user data.

## Security Rules (enforced by /workflow)

These are non-negotiable on every feature:

1. **Input validation** — `$request->validate()` on every POST with types, max lengths, enums
2. **SQL injection** — Eloquent ORM only, never `DB::raw()` with user input
3. **XSS (server)** — `{{ }}` only in Blade, never `{!! !!}` for user data
4. **XSS (client)** — `esc()` helper for all API data before `.html()` in JavaScript:
   ```javascript
   function esc(str) {
       if (!str) return '';
       var div = document.createElement('div');
       div.appendChild(document.createTextNode(str));
       return div.innerHTML;
   }
   ```
5. **CSRF** — `@csrf` on forms, `$.ajaxSetup({ headers: {'X-CSRF-TOKEN': csrfToken} })` on AJAX
6. **Rate limiting** — `->middleware('throttle:30,1')` on create/update routes, `->middleware('throttle:20,1')` on delete
7. **Mass assignment** — Models use `$fillable` whitelist, never `$guarded = []`
8. **Route constraints** — `->where('id', '[0-9]+')` on all parameterised routes
9. **Access control** — Server-side role checks, not just UI hiding
10. **IDOR prevention** — Every endpoint verifies record's `home_id` matches authenticated user

## Project Documentation

| File | Purpose |
|------|---------|
| `docs/logs.md` | Action log with teaching notes — read at session start |
| `sessions/sessionN.md` | Full conversation history per session |
| `phases/phase1.md` | Current phase details, feature list, progress tracker |

## Current Progress

Phase 1: Patch & Polish (4/10 features done)
- Feature 1: Incident Management — DONE
- Feature 2: Staff Training — DONE
- Feature 3: Body Maps — DONE
- Feature 4: Handover Notes — DONE
- Features 5-10: Pending

## Running Tests

```bash
# Run a specific test
php -d error_reporting=0 artisan test --filter=BodyMapTest

# Run all tests
php -d error_reporting=0 artisan test
```
