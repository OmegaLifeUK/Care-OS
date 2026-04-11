# Care OS — Security Vulnerability Checklist

> **Purpose:** Every feature built with `/workflow` MUST pass every item on this checklist during the REVIEW and AUDIT stages. This is the master list of vulnerabilities to check — no exceptions.

---

## How to use this checklist

During **REVIEW** (Stage 6), go through every item and mark PASS/FAIL. Any BLOCKER or HIGH failure **must be fixed before proceeding**. During **AUDIT** (Stage 7), run the automated grep patterns listed at the bottom.

---

## 1. Data Isolation (Multi-Tenancy) — BLOCKER

- [ ] **Every** DB query filters by `home_id`
- [ ] `home_id` is extracted from authenticated user, **never** from request input
- [ ] Admin users with comma-separated `home_id` are parsed with `explode(',', $homeIds)[0]`
- [ ] Service layer scopes all queries with `->forHome($homeId)`
- [ ] API controllers parse `home_id` the same way as web controllers

**Common mistake:** Using raw `Auth::user()->home_id` without `explode()` — breaks multi-home admins.

---

## 2. IDOR Prevention (Resource Ownership) — BLOCKER

- [ ] Every GET endpoint verifies the record's `home_id` matches the user's home
- [ ] Every POST/PUT/DELETE endpoint verifies ownership **before** acting
- [ ] Ownership checks happen in the **controller**, not just the service layer
- [ ] Tests exist for cross-home access attempts on every endpoint
- [ ] Error responses don't reveal whether a resource exists at another home (use 404, not 403)

**Common mistake:** Relying solely on the service layer for IDOR checks — controllers should validate first.

---

## 3. SQL Injection — BLOCKER

- [ ] All queries use Eloquent ORM or query builder with parameter binding
- [ ] Zero use of `DB::raw()` with user input
- [ ] Zero string concatenation in WHERE clauses
- [ ] `DB::statement()` only used for DDL (migrations), never with user data

---

## 4. XSS — Server-Side (Blade) — BLOCKER

- [ ] All user data rendered with `{{ }}` (auto-escaped)
- [ ] Zero use of `{!! !!}` for user-supplied data
- [ ] Blade `@json()` used instead of manual JSON encoding when passing data to JS
- [ ] No inline `onclick`/`onload` handlers with user data

---

## 5. XSS — Client-Side (JavaScript) — BLOCKER

- [ ] `esc()` helper function defined and used for ALL dynamic data before `.html()` / `.innerHTML`
- [ ] `.textContent` preferred over `.innerHTML` where possible
- [ ] No raw user data in jQuery `.html()`, `.append()`, or `.prepend()` without `esc()`
- [ ] Dynamic values used in HTML attributes are escaped
- [ ] CSS class names derived from user input are sanitized (e.g., `replace(/[^a-z_]/g, '')`)

**The `esc()` helper:**
```javascript
function esc(str) {
    if (!str) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}
```

---

## 6. CSRF Protection — HIGH

- [ ] `@csrf` on every `<form>`
- [ ] `$.ajaxSetup({ headers: {'X-CSRF-TOKEN': csrfToken} })` before any AJAX calls
- [ ] CSRF token sourced from `{{ csrf_token() }}` in Blade
- [ ] No GET routes that modify data

---

## 7. Input Validation — HIGH

- [ ] Every POST route calls `$request->validate()` with:
  - `required` / `nullable` on every field
  - Type checks: `integer`, `string`, `date`, `boolean`
  - Length limits: `max:N` on all string fields
  - Enum validation: `in:value1,value2,...` for fixed-choice fields
  - `exists:table,column` for foreign key references
- [ ] Client-side validation mirrors server-side rules (but never replaces them)
- [ ] Error responses don't leak internal details (table names, column names)

---

## 8. Mass Assignment Protection — HIGH

- [ ] Models use `$fillable` whitelist (never `$guarded = []`)
- [ ] `$fillable` contains only the fields that should be user-settable
- [ ] `home_id`, `created_by`, `updated_by` are set in the service layer, not from request data
- [ ] No `Model::create($request->all())` — always pass specific fields

---

## 9. Rate Limiting — HIGH

- [ ] All POST (create) routes: `->middleware('throttle:30,1')`
- [ ] All DELETE routes: `->middleware('throttle:20,1')`
- [ ] All PUT/PATCH (update) routes: `->middleware('throttle:30,1')`
- [ ] Auth endpoints (login, password reset): stricter limits

---

## 10. Authentication & Authorization — HIGH

- [ ] Protected routes use `checkUserAuth` middleware
- [ ] Admin-only actions check `Auth::user()->user_type === 'A'` **server-side**
- [ ] Role checks happen in the controller, not just in Blade templates
- [ ] Unauthenticated requests redirect properly (test with no session)

---

## 11. Route Constraints — MEDIUM

- [ ] All parameterised routes have `->where('param', '[0-9]+')` for integer IDs
- [ ] No open redirects (redirect targets are hardcoded or validated)
- [ ] URLs in Blade use `{{ url('/path') }}`, never hardcoded domains

---

## 12. Audit Logging — MEDIUM

- [ ] `Log::info()` on every create operation (who created what, for which home)
- [ ] `Log::info()` on every delete/soft-delete (who deleted what, full record snapshot)
- [ ] `Log::info()` on every update (who updated what)
- [ ] Log entries include: `action`, `record_id`, `home_id`, `user_id`, `timestamp`
- [ ] No sensitive data in logs (passwords, tokens, PII beyond IDs)

---

## 13. Database Integrity — MEDIUM

- [ ] Foreign key constraints where practical (home_id, created_by, updated_by)
- [ ] Composite indexes on frequently queried column combinations
- [ ] `is_deleted` flag used (not Laravel SoftDeletes) for backwards compatibility
- [ ] Migrations have proper `down()` methods for rollback

---

## 14. Error Handling — MEDIUM

- [ ] No stack traces or internal paths exposed to client
- [ ] JSON error responses use generic messages ("Not found", "Not authorised")
- [ ] 404 for missing/unauthorized resources (don't reveal existence with 403)
- [ ] `console.error()` in JS for debugging only — no sensitive data

---

## 15. Code Conventions — MINOR

- [ ] New models in `app/Models/`, alias in `app/` if needed
- [ ] Business logic in `app/Services/`, not in controllers
- [ ] Controllers call service methods only
- [ ] `user_type` column (not `type`) for role checks
- [ ] No `dd()`, `dump()`, `console.log()` left in production code
- [ ] No commented-out code blocks longer than 5 lines

---

## Automated Grep Patterns (AUDIT stage)

Run these during Stage 7 to catch missed vulnerabilities:

```bash
# Raw SQL with user input
grep -rn "DB::raw\|->whereRaw\|->selectRaw" app/Http/Controllers/ app/Services/

# Unescaped Blade output
grep -rn "{!!" resources/views/

# Unsafe JS DOM insertion without esc()
grep -rn "\.html(\|\.innerHTML\|\.append(\|\.prepend(" resources/views/ | grep -v "esc("

# Missing rate limits on POST routes
grep -n "Route::post" routes/web.php | grep -v "throttle"

# Overly permissive models
grep -rn "\$guarded\s*=\s*\[\]" app/Models/ app/

# Hardcoded URLs
grep -rn "http://127\|http://localhost\|http://care" resources/views/

# Debug statements
grep -rn "dd(\|dump(\|console\.log(" app/ resources/views/ --include="*.php" --include="*.blade.php"

# Missing home_id filter in controllers
grep -rn "function.*Request" app/Http/Controllers/ | head -20
# Then manually verify each uses home_id scoping
```

---

## Vulnerability History

| Date | Feature | Vulnerability | Severity | Fix |
|------|---------|--------------|----------|-----|
| 2026-04-11 | Body Maps | API controller raw home_id (no explode) | CRITICAL | Added getHomeId() helper |
| 2026-04-11 | Body Maps | API removeInjury() no IDOR check | CRITICAL | Added ownership verification |
| 2026-04-11 | Body Maps | Web removeInjury() IDOR in service only | HIGH | Added controller-level check |
| 2026-04-11 | Body Maps | Web updateInjury() IDOR in service only | HIGH | Added controller-level check |
| 2026-04-11 | Body Maps | No cross-home IDOR tests | HIGH | Added 3 IDOR test cases |
| 2026-04-11 | Body Maps | No client-side validation | HIGH | Added JS validation |
| 2026-04-11 | Body Maps | No audit logging | MEDIUM | Added Log::info() in service |
| 2026-04-11 | Body Maps | No FK constraints | MEDIUM | Added migration with FKs + indexes |
