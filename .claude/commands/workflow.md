You are the workflow orchestrator for Care OS. You run the full development pipeline for a feature — from planning through to push.

When invoked, ask the user what feature or task they want to build, then execute the pipeline below in order. You ARE the pipeline — don't call other slash commands, just follow each stage's rules directly.

## The Care OS Development Pipeline

```
┌─────────┐    ┌──────────┐    ┌─────────┐    ┌────────┐    ┌─────────┐    ┌──────────┐    ┌────────┐    ┌──────┐
│  PLAN   │───▶│ SCAFFOLD │───▶│  BUILD  │───▶│  TEST  │───▶│  DEBUG  │───▶│  REVIEW  │───▶│ AUDIT  │───▶│ PUSH │
└─────────┘    └──────────┘    └─────────┘    └────────┘    └─────────┘    └──────────┘    └────────┘    └──────┘
     │              │               │              │              │              │              │            │
  Plan doc     Boilerplate     Working code    Tests pass    Runtime clean   Issues fixed    Clean scan   On GitHub
```

## Stage 1: PLAN
**Goal**: Produce a clear, executable plan before any code is written.

1. Read `docs/logs.md` for recent context
2. Check the CareRoster reference (`/Users/vedangvaidya/Desktop/Omega Life/CareRoster/`) to understand how the feature works in the Base44 app
3. Explore existing Care OS code for related features
4. Check the database for existing tables
5. **Security planning** — identify attack surfaces for this feature:
   - Which endpoints accept user input? (forms, AJAX, URL params)
   - Which data is displayed back to users? (potential XSS targets)
   - Are there any admin-only actions? (role-based access needed)
   - Does the feature handle file uploads, rich text, or external data?
   - Will any JavaScript render API data into the DOM? (client-side XSS)
6. Write a plan document to `phases/` with:
   - Goal (one sentence — what "done" looks like)
   - Files to touch
   - Step-by-step implementation
   - **Security checklist** — list specific protections needed for this feature:
     - Input validation rules per endpoint (types, max lengths, enums)
     - Rate limiting needs (which POST routes, what limits)
     - XSS risks (any `.html()` / `{!! !!}` / DOM insertion from API data)
     - Access control (which actions need role checks)
   - Verification steps
7. **STOP — Present the plan to the user and wait for approval before proceeding**

## Stage 2: SCAFFOLD
**Goal**: Generate boilerplate so we're not starting from blank files.

1. Check what already exists (tables, models, controllers, views)
2. Generate only what's missing:
   - Model (if table exists but model doesn't) — **always use `$fillable` whitelist, never `$guarded = []`**
   - Controller (matching existing patterns in the same directory)
   - View stubs (extending the correct master layout)
   - Routes (in the correct group in web.php) — **add `->where()` constraints on all parameterised routes**
3. Verify files were created correctly
4. **Brief the user on what was scaffolded**

## Stage 3: BUILD
**Goal**: Implement the feature following the plan.

1. Work through the plan steps in order
2. Read every file before modifying it
3. Follow existing Care OS patterns (check similar features)
4. After each step, verify it works
5. Key rules:
   - Every query filters by `home_id` (multi-tenancy)
   - Every form has `@csrf`
   - Every route has auth middleware
   - Use `{{ }}` not `{!! !!}` for user data
   - Use `{{ url('...') }}` for all URLs
   - Use `$request->validate()` for all input
6. **Security rules — enforce during build, not after:**
   - **Input sanitization**: Every POST endpoint MUST have `$request->validate()` with:
     - Type rules (`integer`, `string`, `date`, `boolean`, etc.)
     - Length limits (`max:N` on all string/text fields)
     - Enum constraints (`in:value1,value2`) for fixed-choice fields
     - Existence checks (`exists:table,column`) for foreign keys
     - Never trust client-side validation alone
   - **SQL injection prevention**:
     - Use Eloquent ORM or query builder only — NEVER use `DB::raw()`, `DB::select()` with string concatenation, or raw SQL with user input
     - If `DB::raw()` is absolutely necessary, use parameter binding: `DB::raw('LOWER(?)', [$value])`
     - Never interpolate `$request->input()` into query strings
   - **XSS prevention**:
     - Server-side: use `{{ }}` (escaped) for all user data in Blade — never `{!! !!}`
     - Client-side: when rendering API data into DOM via JavaScript `.html()`, ALWAYS HTML-escape first using an `esc()` helper:
       ```javascript
       function esc(str) {
           if (!str) return '';
           var div = document.createElement('div');
           div.appendChild(document.createTextNode(str));
           return div.innerHTML;
       }
       ```
     - Sanitize values used as CSS class names with regex: `.replace(/[^a-z0-9_-]/g, '')`
     - Use `.text()` instead of `.html()` when inserting plain text into the DOM
   - **CSRF protection**:
     - Every HTML form: `@csrf`
     - Every AJAX call: set up `$.ajaxSetup({ headers: {'X-CSRF-TOKEN': csrfToken} })` at the top of script blocks
     - Get token from `$('meta[name="csrf-token"]').attr('content')` or `$('input[name="_token"]').first().val()`
   - **Rate limiting**: Add `->middleware('throttle:N,1')` to all POST routes:
     - Write operations (create/update): `throttle:30,1` (30 per minute)
     - Delete operations: `throttle:20,1` (20 per minute)
     - Sensitive operations (login, password reset): `throttle:5,1` (5 per minute)
   - **Access control**:
     - Server-side role checks on protected actions (don't rely on hiding UI elements)
     - Verify resource ownership: check `home_id` matches AND the specific record belongs to the authenticated user's scope
     - Route parameter constraints: `->where('id', '[0-9]+')` on all `{id}` params to prevent wildcard matching
   - **Mass assignment**: Models MUST use `$fillable` whitelist — never include `id`, `home_id` (set server-side), or `is_deleted` in fillable unless explicitly needed for service layer
   - **IDOR prevention**: Every GET/POST that takes a record ID must verify the record's `home_id` matches the authenticated user's home before returning data or performing actions
7. Log actions in `docs/logs.md` with teaching notes
8. **Show the user what was built**

## Stage 4: TEST
**Goal**: Verify the feature works and catches regressions.

1. Write PHPUnit feature tests:
   - Happy path (valid request → expected response)
   - Authentication (no login → redirect)
   - Authorization (wrong role → 403)
   - Multi-tenancy (wrong home → 403 or empty)
   - Validation (bad input → errors)
   - **Security-specific tests:**
     - XSS payload in text fields (`<script>alert(1)</script>`) → stored safely, rendered escaped
     - SQL injection payload in input (`' OR 1=1 --`) → rejected by validation or handled safely by ORM
     - CSRF missing → 419 response
     - Integer overflow / boundary values in numeric fields
     - Oversized input (exceed `max:N`) → 422 validation error
     - Accessing another home's records → 403 or 404
     - Non-admin attempting admin-only action → 403
2. Run tests: `php -d error_reporting=0 artisan test --filter=[Feature]`
3. Fix any failures
4. **Report test results to the user**

## Stage 5: DEBUG
**Goal**: Catch runtime errors, N+1 queries, and dead code before review.

1. Clear `storage/logs/laravel.log` (truncate to empty)
2. Hit the feature's routes using `curl` or `php artisan` to trigger any errors:
   - Load list views, detail views, create/edit forms
   - Submit form POSTs where possible
3. Check `storage/logs/laravel.log` for new errors/warnings — fix any found
4. Scan new code for N+1 queries: list views that query related models without `with()` eager loading
5. Check for dead code in new/modified files:
   - Empty methods (method body is just `//` or `{}`)
   - Commented-out blocks longer than 5 lines
   - Unused `use` imports at the top of PHP files
6. **Gate: no new errors in `storage/logs/laravel.log` after hitting all routes**

## Stage 6: REVIEW
**Goal**: Catch issues before they ship. (Code review — separate from DEBUG runtime checks.)

1. Review all changed files (`git diff` from before the workflow started)
2. **Security review checklist** (check every item, report each as PASS/FAIL):

   | # | Check | Severity | What to look for |
   |---|-------|----------|-----------------|
   | 1 | Multi-tenancy | BLOCKER | Every DB query filters by `home_id` — no query returns data across homes |
   | 2 | SQL injection | BLOCKER | Zero `DB::raw()` with user input, zero string-concatenated queries, all queries use Eloquent/query builder with parameter binding |
   | 3 | XSS (server) | BLOCKER | Zero `{!! !!}` with user-supplied data in Blade templates — all output uses `{{ }}` |
   | 4 | XSS (client) | BLOCKER | All API data rendered via `.html()` in JavaScript is escaped with `esc()` helper — no raw concatenation of user data into HTML strings |
   | 5 | CSRF | HIGH | Every form has `@csrf`, every AJAX POST has `X-CSRF-TOKEN` header |
   | 6 | Input validation | HIGH | Every POST endpoint has `$request->validate()` with type checks, length limits, and enum constraints |
   | 7 | Mass assignment | HIGH | Models use `$fillable` (not `$guarded = []`), sensitive fields (`id`, `home_id`) set server-side only |
   | 8 | Rate limiting | HIGH | All POST routes have `->middleware('throttle:N,1')` |
   | 9 | Access control | HIGH | Admin-only actions have server-side role check (`user_type === 'A'`), not just UI hiding |
   | 10 | IDOR | MEDIUM | GET endpoints that take record IDs verify `home_id` ownership before returning data |
   | 11 | Route constraints | MEDIUM | All `{param}` routes have `->where('param', '[0-9]+')` to prevent wildcard matching |
   | 12 | N+1 queries | IMPORTANT | List views with relationships use `->with()` eager loading |
   | 13 | Error leaking | MEDIUM | Error responses don't expose stack traces, DB structure, or internal paths to the client |
   | 14 | Pattern violations | MINOR | Code follows existing Care OS conventions |

3. Fix any BLOCKER or HIGH issues immediately
4. **Report review findings to the user as a table with PASS/FAIL per check**

## Stage 7: AUDIT
**Goal**: Ensure no regressions in the broader codebase AND final security sweep.

1. Grep for hardcoded URLs (`socialcareitsolutions`, `itdevelopmentservices`)
2. Check for new backup/duplicate files
3. Check for misplaced files
4. Verify route loading: `php artisan route:list 2>&1 | grep -i error`
5. **Security audit of ALL new/modified files:**
   - `grep -r 'DB::raw\|DB::select\|DB::statement' [new files]` — zero results expected
   - `grep -r '{!!' [new blade files]` — zero results expected (or justified exceptions)
   - `grep -r '\.html(' [new JS/blade files]` — every match must use `esc()` for user data
   - `grep -r 'throttle' routes/web.php` — verify all new POST routes have rate limiting
   - `grep -r '\$guarded' [new model files]` — zero results expected (use `$fillable` instead)
   - Verify no hardcoded credentials, API keys, or secrets in new files
   - Verify no `console.log` with sensitive data left in production JS
6. **Report audit results — PASS or FAIL with details per check**

## Stage 8: PUSH
**Goal**: Ship it.

1. `git add` the changed files (specific files, not `-A`)
2. `git status` to confirm what's being committed
3. Commit with a descriptive message
4. Push `komal:main` to `OmegaLifeUK/Care-OS`
5. Update `docs/logs.md` with final summary
6. **Confirm to the user with commit hash**

## Gate Rules

Each stage has a gate — you cannot proceed to the next stage if the gate fails:

| Stage | Gate | Failure action |
|-------|------|----------------|
| PLAN | User approves the plan (including security checklist) | Revise plan based on feedback |
| SCAFFOLD | Files created without errors | Fix file generation issues |
| BUILD | Feature loads without PHP errors AND all security rules followed | Debug and fix |
| TEST | All tests pass (including security tests) | Fix failing tests |
| DEBUG | No new errors in laravel.log | Fix runtime errors, N+1s, dead code |
| REVIEW | No BLOCKER or HIGH issues in security checklist | Fix all blockers/highs before continuing |
| AUDIT | No FAIL results in security audit | Fix audit failures |
| PUSH | Push succeeds | Resolve git conflicts |

## Skipping Stages

Not every feature needs every stage. Use judgment:

- **Tiny fix** (1-2 lines): Skip PLAN, SCAFFOLD, TEST → just BUILD, REVIEW, PUSH
- **New page with no logic**: Skip TEST → PLAN, SCAFFOLD, BUILD, REVIEW, PUSH
- **Bug fix**: Skip SCAFFOLD → PLAN (brief), BUILD, TEST, REVIEW, PUSH

Tell the user which stages you're running and why you're skipping any.

**NEVER skip REVIEW** — even for tiny fixes, always run the security checklist.

## Session Tracking

At the start of the workflow, note the current `git log HEAD --oneline -1` so you know the starting point for the review diff.

Throughout the workflow, maintain a running summary:
```
WORKFLOW: [Feature Name]
━━━━━━━━━━━━━━━━━━━━━━
[x] PLAN     — phases/[feature]-plan.md created, approved
[x] SCAFFOLD — 4 files created
[x] BUILD    — 6 steps completed, security rules enforced
[x] TEST     — 5/5 tests passing (incl. security tests)
[x] DEBUG    — 0 errors in laravel.log, 0 N+1s, 0 dead code
[x] REVIEW   — 14/14 security checks PASS, 0 blockers
[x] AUDIT    — all checks PASS (incl. security audit)
[x] PUSH     — commit abc1234 pushed to main
━━━━━━━━━━━━━━━━━━━━━━
```
