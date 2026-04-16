You are the workflow orchestrator for Care OS. You run the full development pipeline for a feature — from planning through to push.

When invoked, ask the user what feature or task they want to build, then execute the pipeline below in order. You ARE the pipeline — don't call other slash commands, just follow each stage's rules directly.

## The Care OS Development Pipeline

```
┌─────────┐    ┌──────────┐    ┌─────────┐    ┌────────┐    ┌─────────┐    ┌──────────┐    ┌────────┐    ┌───────────┐    ┌──────┐
│  PLAN   │───▶│ SCAFFOLD │───▶│  BUILD  │───▶│  TEST  │───▶│  DEBUG  │───▶│  REVIEW  │───▶│ AUDIT  │───▶│ PROD-READY│───▶│ PUSH │
└─────────┘    └──────────┘    └─────────┘    └────────┘    └─────────┘    └──────────┘    └────────┘    └───────────┘    └──────┘
     │              │               │              │              │              │              │              │              │
  Plan doc     Boilerplate     Working code    Tests pass    Runtime clean   Issues fixed    Clean scan   Ship-quality    On GitHub
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
**Goal**: Verify the feature works, catches regressions, and is secure against all attack vectors.

### 4a. Unit & Endpoint Tests
Write PHPUnit feature tests covering:
- Happy path (valid request → expected response with correct data)
- Authentication (no login → redirect)
- Authorization (wrong role → 403)
- Validation (bad input → 422 with error messages)

### 4b. Multi-Step Flow Tests
Don't just test endpoints in isolation. Test the actual user journey:
- Create a record → verify it appears in the list endpoint response
- Update the record → verify the list reflects the change
- Delete/soft-delete → verify it no longer appears in the list
- If there's a workflow (e.g., acknowledge, change status), test the full sequence

### 4c. IDOR & Multi-Tenancy Tests (Cross-Home AND Cross-Entity)
This is where previous features had vulnerabilities caught late. Test **every** attack surface:

**Cross-home record access** (basic IDOR):
- Create a record for home A → try to read/update/delete it as home B → expect 404

**Cross-home foreign key injection** (the gap that was missed):
- For every parameter that references another entity (staff_user_id, logbook_id, service_user_id, etc.):
  - Send a valid ID that exists but belongs to a **different home**
  - Expect: rejection (404 or validation error), NOT silent success
- List all foreign key parameters in the feature and test each one explicitly

**Cross-home via source record** (the createFromLogBook gap):
- If a record can be created FROM another record, verify the source record's home_id is checked

### 4d. Security Payload Tests
- XSS: `<script>alert(1)</script>` in every text field → stored raw, rendered escaped
- SQLi: `' OR 1=1 --` in text/search fields → 0 results or validation error, no DB error
- CSRF: POST without `_token` → 419 response
- Oversized input: exceed `max:N` → 422
- Wrong types: string in integer field, future date in past-only field → 422

### 4e. Run & Report
1. Run: `php -d error_reporting=0 artisan test --filter=[Feature]`
2. Fix any failures
3. **Report test results with explicit count per category (4a-4d)**

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

## Stage 6: REVIEW — Adversarial Security Testing
**Goal**: Try to break every endpoint. Think like an attacker, not a checklist auditor.

**CRITICAL RULE**: Do NOT just read code and mark PASS/FAIL. You must **actually exploit** each attack vector using curl against the running dev server. Pattern-matching ("does the code use forHome()? yes → PASS") is how vulnerabilities slipped through in Features 3 and 4. The only acceptable evidence for PASS is a failed attack.

### Step 1: Start the server and authenticate
```bash
php artisan serve &
# Get a valid session cookie by logging in as komal
curl -c cookies.txt -X POST http://127.0.0.1:8000/login -d "user_name=komal&password=123456&home=Aries&_token=..."
```

### Step 2: Attack every endpoint (do ALL of these, not a subset)

**IDOR — Cross-home record access:**
For every endpoint that takes a record ID:
- Find a valid record ID belonging to a **different** home_id
- `curl` the endpoint with that ID using your authenticated session
- **PASS only if**: response is 404 or empty, NOT the other home's data

**IDOR — Cross-home foreign key injection:**
For every parameter that references another entity (staff_user_id, service_user_id, logbook_id, etc.):
- Find a valid ID of that entity type from a **different** home
- Submit a create/update request using that foreign ID
- **PASS only if**: request is rejected, record is NOT created with the cross-home reference

**CSRF:**
For every POST endpoint:
- `curl` the endpoint WITHOUT the `_token` / `X-CSRF-TOKEN` header
- **PASS only if**: response is 419

**XSS — Server-side:**
For every text input field:
- Submit `<script>alert('xss')</script>` as the value
- Fetch the record back via the list/detail endpoint
- **PASS only if**: the response contains `&lt;script&gt;` (escaped), NOT raw `<script>`

**XSS — Client-side:**
For every `.html()` / `.innerHTML` / `.append()` call in the feature's JavaScript:
- Trace the data source: where does the HTML string come from?
- If it renders API response data, verify the API response pre-escapes with `e()`, OR the JS uses `esc()` before insertion
- **PASS only if**: you can prove no path exists where raw user text reaches `.html()` unescaped

**SQL Injection:**
For every text/search input:
- Submit `' OR 1=1 --` and `'; DROP TABLE users; --`
- **PASS only if**: response is empty results or validation error, NOT a database error or all records

**Mass Assignment:**
- Submit a POST with extra fields: `home_id=999`, `is_deleted=1`, `id=1`
- **PASS only if**: the created/updated record does NOT have the injected values

**Rate Limiting:**
- For each POST route in `routes/web.php`, verify `throttle` middleware exists
- **PASS only if**: every new POST route has throttle

### Step 3: Checklist verification (after attacks)
Also verify these by code inspection (these can't be curl-tested):
| # | Check | Severity |
|---|-------|----------|
| 1 | Data isolation — every query filters by home_id, explode() used | BLOCKER |
| 2 | Auth middleware on all routes | HIGH |
| 3 | $fillable whitelist, no $guarded = [] | HIGH |
| 4 | Route constraints ->where('id', '[0-9]+') on all params | MEDIUM |
| 5 | Audit logging — Log::info() on create/update/delete | MEDIUM |
| 6 | DB integrity — indexes, proper down() in migrations | MEDIUM |
| 7 | Error handling — no stack traces/internal paths leaked | MEDIUM |
| 8 | Code conventions — service layer, no dd()/console.log() | MINOR |

### Step 4: Report & Fix
1. Report every attack attempted and result (PASS with evidence / FAIL with exploit details)
2. Fix ALL BLOCKER and HIGH failures immediately
3. **Re-run the failed attacks after fixing** to confirm the fix works
4. Update `docs/security-checklist.md` vulnerability history with any new findings
5. **Report final results to user**

## Stage 7: AUDIT
**Goal**: Ensure no regressions in the broader codebase AND final security sweep.

1. Grep for hardcoded URLs (`socialcareitsolutions`, `itdevelopmentservices`)
2. Check for new backup/duplicate files
3. Check for misplaced files
4. Verify route loading: `php artisan route:list 2>&1 | grep -i error`
5. **Run the automated grep patterns from `docs/security-checklist.md`** on all new/modified files:
   - `grep -rn 'DB::raw\|->whereRaw\|->selectRaw' [new files]` — zero results expected
   - `grep -rn '{!!' [new blade files]` — zero results expected (or justified exceptions)
   - `grep -rn '\.html(\|\.innerHTML' [new JS/blade files]` — every match must use `esc()` for user data
   - `grep -n 'Route::post' routes/web.php | grep -v throttle` — zero new POST routes without rate limiting
   - `grep -rn '\$guarded\s*=\s*\[\]' [new model files]` — zero results expected (use `$fillable` instead)
   - `grep -rn 'dd(\|dump(\|console\.log(' [new files]` — zero debug statements
   - `grep -rn 'http://127\|http://localhost\|http://care' [new blade files]` — zero hardcoded URLs
   - Verify no hardcoded credentials, API keys, or secrets in new files
6. **Verify all 15 checklist items from REVIEW are still PASS** (no regressions from last-minute fixes)
7. **Report audit results — PASS or FAIL with details per check**

## Stage 8: PROD-READY — Verified, Not Self-Graded
**Goal**: Verify the feature is ship-quality through actual testing, not code reading.

**CRITICAL RULE**: Every check below must be verified by actually hitting the endpoint or reading the rendered response. "I read the code and it looks right" is NOT a PASS. The Body Maps color bug passed code review — it took a browser to find it. The Handover CSRF gap passed code review — it took a curl to find it.

### 8a. Error & Edge Case Handling — VERIFY VIA CURL
For each endpoint in the feature:

**Empty state:**
- `curl` the list endpoint for a home/filter combination that returns zero records
- **PASS only if**: response contains a "no records" message, NOT a blank page or JS error

**Validation errors:**
- `curl` a POST with missing required fields
- **PASS only if**: response is 422 with readable error messages, NOT a 500 or raw exception

**Boundary values:**
- Submit a 10,000-character string in a text field
- Submit `& < > " '` in every text field
- **PASS only if**: no 500 errors, no layout-breaking HTML in the response

**AJAX error handling (code inspection — can't curl this easily):**
- Read the JS: does every `$.ajax` have an `error:` callback?
- Does the error callback show a user-visible message (alert/toast/div)?
- **PASS only if**: no AJAX call silently swallows errors

### 8b. Performance — VERIFY VIA CODE
- [ ] **N+1 queries** — list views with related data use `->with()` eager loading
- [ ] **Database indexes** — columns in WHERE/ORDER BY have indexes
- [ ] **Payload size** — `->select(...)` used, not `SELECT *` for large tables
- [ ] **No duplicate queries** — same data not fetched twice in one request

### 8c. UI/UX Quality — VERIFY BY TRACING THE INCLUDE CHAIN
**Where is this view rendered?**
- Trace the Blade `@include` chain: which parent page includes this partial?
- Does that parent page have jQuery loaded? Does it have the `.loader` element? Does it have the CSS?
- **PASS only if**: you can name the exact parent page and confirm its dependencies

**Form/modal behavior (code inspection):**
- After successful AJAX submit, does the JS clear form fields and close the modal?
- Do destructive actions (delete) have a `confirm()` prompt?
- Do all links use `{{ url('/path') }}`?

### 8d. Graceful Degradation — VERIFY VIA CURL
**Null related data:**
- What happens if a referenced staff member has been deleted? If a related record is null?
- `curl` or read the controller: is there a null check / optional chaining before accessing related fields?
- **PASS only if**: page renders with "Unknown" or "N/A", NOT a PHP error

**Session timeout:**
- `curl` a POST endpoint with an expired/invalid session cookie
- **PASS only if**: response is a redirect to login, NOT a raw 419 error page

### 8e. MANUAL TEST CHECKLIST — PRINTED FOR USER
**This is mandatory.** Before PUSH, generate a step-by-step manual test checklist specific to this feature. Format:

```
╔══════════════════════════════════════════════════════════════╗
║  MANUAL TEST CHECKLIST — [Feature Name]                      ║
║  Test these in the browser before I push.                    ║
╠══════════════════════════════════════════════════════════════╣
║                                                              ║
║  Login: komal / 123456, house: Aries                         ║
║  URL: http://127.0.0.1:8000                                  ║
║                                                              ║
║  □ Step 1: Navigate to [exact page path]                     ║
║  □ Step 2: [exact action — click what, fill what]            ║
║  □ Step 3: [what you should see]                             ║
║  ...                                                         ║
║  □ Edge: [test with zero records / empty state]              ║
║  □ Edge: [test with special characters in input]             ║
║                                                              ║
║  Reply "tested" or report bugs.                              ║
╚══════════════════════════════════════════════════════════════╝
```

Include:
- The golden path (create → view → edit → delete)
- The empty state (what shows when there are no records)
- Search/filter if applicable
- Any multi-modal flow (modal A → modal B → modal C)
- At least one edge case (special characters, long text)

### Gate
- 8a-8d: Report PASS/FAIL with evidence (curl output or specific code line)
- 8e: Checklist printed, **user confirms "tested" before PUSH**
- Any FAIL in 8a-8d must be fixed. If user reports bugs from 8e, fix before PUSH.

## Stage 9: PUSH
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
| TEST | All tests pass: endpoint, flow, IDOR (cross-home + cross-entity), security payloads | Fix failing tests |
| DEBUG | No new errors in laravel.log | Fix runtime errors, N+1s, dead code |
| REVIEW | Every attack attempted via curl PASSES — no exploitable endpoint | Fix vulnerability, re-attack to confirm |
| AUDIT | No FAIL results in security audit | Fix audit failures |
| PROD-READY | 8a-8d PASS with evidence + 8e manual checklist printed + **user confirms "tested"** | Fix before push |
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
[x] REVIEW   — N attacks attempted via curl, all failed (0 exploitable), M code checks PASS
[x] AUDIT    — all grep patterns clean, no regressions
[x] PROD-READY — 8a-8d PASS with curl evidence, manual checklist printed, user confirmed "tested"
[x] PUSH     — commit abc1234 pushed to main
━━━━━━━━━━━━━━━━━━━━━━
```
