You are the workflow orchestrator for Care OS Phase 2. You run the full development pipeline for Phase 2 features — from planning through to push.

Phase 2 builds the **client/family portal, reporting engine, and workflow automation** on top of the Phase 1 foundation. Unlike Phase 1 (which wired existing backends), Phase 2 creates new modules from scratch — but always using CareRoster (Base44/React) as the design reference.

When invoked, ask the user which Phase 2 feature they want to build, then execute the pipeline below in order. You ARE the pipeline — don't call other slash commands, just follow each stage's rules directly.

## Phase 2 Feature List

| # | Feature | Est | Category |
|---|---------|-----|----------|
| 1 | Client portal — family-facing login & dashboard | 8h | Portal |
| 2 | Client portal — schedule view for family | 4h | Portal |
| 3 | Client portal — messaging with care team | 4h | Portal |
| 4 | Client portal — feedback & satisfaction forms | 4h | Portal |
| 5 | Custom report builder UI | 8h | Reporting |
| 6 | Scheduled reports (daily/weekly/monthly email) | 6h | Reporting |
| 7 | Workflow automation engine (trigger → action) | 10h | Workflows |
| 8 | Pre-built workflows (incident → notify manager) | 4h | Workflows |

## Feature Build Order

Features MUST be built in this order — later features depend on earlier infrastructure:

1. **Portal login & dashboard** (creates portal middleware, layout, auth flow, `client_portal_accesses` table — everything else in the portal depends on this)
2. **Portal schedule view** (reuses portal layout and middleware from Feature 1)
3. **Portal messaging** (reuses portal infrastructure, creates `client_portal_messages` table)
4. **Portal feedback** (reuses portal infrastructure, creates `client_feedback` table)
5. **Report builder** (admin-side, independent of portal — creates `saved_reports` table, `ReportBuilderService`)
6. **Scheduled reports** (depends on report builder — creates `scheduled_reports` table, Laravel scheduler, queue jobs)
7. **Workflow engine** (independent — creates `automated_workflows` + `workflow_execution_logs` tables, `WorkflowEngine` service)
8. **Pre-built workflows** (depends on workflow engine — seeds 8 default workflows, wires to existing Phase 1 events)

## CareRoster (Base44) Feature Classification

Each feature falls into one of three categories based on what CareRoster actually built vs. faked:

| Category | Features | What we do |
|----------|----------|------------|
| **Port** (real backend in CareRoster) | Portal dashboard, schedule, messaging, booking requests | Study CareRoster schema + UI → rebuild in Laravel |
| **Build for real** (UI-only/fake in CareRoster) | Workflow engine (localStorage + hardcoded stats), scheduled report execution (no job runs them) | Study UI for design reference → build actual backend from scratch |
| **Finish** (half-real in CareRoster) | Custom report builder (queries real data but saves to localStorage), client feedback (needs verification) | Port the real parts, build the missing parts |

**CRITICAL**: Always classify the feature FIRST in PLAN. For "Port" features, the CareRoster entity schema is the migration spec. For "Build for real" features, you're designing the backend yourself — CareRoster only gives you UI inspiration.

## Base44 Entity Schemas (Reference for Migrations)

These schemas were extracted from CareRoster. Use them as the starting point for Laravel migrations — adapt types appropriately (Base44 `string` → `varchar(255)`, Base44 `array of objects` → `JSON`, Base44 `enum` → `varchar` with validation, etc.).

### ClientPortalAccess (17 fields)
| Field | Type | Notes |
|-------|------|-------|
| id | auto-increment int | Laravel standard |
| client_id | int | FK → service_user.id |
| client_type | enum | residential, domiciliary, supported_living, day_centre |
| user_email | string (email) | Auth link — matched against logged-in user's email |
| full_name | string | Display name of the portal user |
| relationship | enum | self, parent, child, spouse, sibling, guardian, advocate, social_worker, other |
| access_level | enum | view_only, view_and_message, full_access |
| can_view_schedule | boolean | Default: true |
| can_view_care_notes | boolean | Default: true |
| can_send_messages | boolean | Default: true |
| can_request_bookings | boolean | Default: false |
| phone | string nullable | Optional contact number |
| is_primary_contact | boolean | Default: false |
| is_active | boolean | Default: true |
| activation_date | date nullable | When access was granted |
| last_login | datetime nullable | Last portal login |
| notes | text nullable | Free text |

### ClientPortalMessage (19 fields)
| Field | Type | Notes |
|-------|------|-------|
| id | auto-increment int | |
| client_id | int | FK → service_user.id |
| sender_type | enum | family, staff, system |
| sender_id | int | Portal access ID or staff ID |
| sender_name | string | Denormalised name |
| recipient_type | enum | family, staff, all_staff |
| recipient_id | int nullable | Specific person ID |
| subject | string | Message subject |
| message_content | text | Full message body |
| priority | enum | low, normal, high |
| category | enum | general, schedule, medication, care_plan, feedback, concern, request |
| is_read | boolean | Default: false |
| read_at | datetime nullable | |
| read_by | int nullable | |
| replied_to_message_id | int nullable | FK → self (threading) |
| has_reply | boolean | Default: false |
| status | enum | sent, delivered, read, archived |
| attachments | JSON nullable | [{file_name, file_url, file_type}] |
| home_id | int | Multi-tenancy — add this (not in Base44 but required for Care OS) |

### SessionBookingRequest (20 fields)
| Field | Type | Notes |
|-------|------|-------|
| id | auto-increment int | |
| client_id | int | FK → service_user.id |
| client_type | enum | day_centre, supported_living, other |
| request_type | enum | book_session, cancel_session, reschedule_session, change_transport |
| session_id | int nullable | FK → session (for cancel/reschedule) |
| activity_id | int nullable | FK → activity (for booking) |
| requested_date | date | Target date |
| requested_time | string nullable | Preferred time (HH:MM) |
| reason | text nullable | Reason text |
| additional_notes | text nullable | Free text |
| requested_by_name | string | Denormalised |
| requested_by_email | string | Denormalised |
| requested_by_relationship | string | Denormalised |
| status | enum | pending, approved, declined, completed |
| priority | enum | normal, urgent |
| reviewed_by_staff_id | int nullable | FK → user.id |
| reviewed_date | datetime nullable | |
| response_notes | text nullable | Staff response |
| transport_required | boolean | Default: false |
| transport_type | enum nullable | centre_transport, family_transport, taxi, self_transport |
| home_id | int | Multi-tenancy |

### ScheduledReport (16 fields)
| Field | Type | Notes |
|-------|------|-------|
| id | auto-increment int | |
| report_name | string | Display name |
| report_type | enum | client_progress, staff_performance, compliance, payroll_summary, incident_trends, training_compliance, audit_summary, occupancy, medication_compliance, custom |
| parameters | JSON | Arbitrary filters/date ranges |
| schedule_frequency | enum | daily, weekly, fortnightly, monthly, quarterly, annually, one_time |
| schedule_day | int nullable | 0–6 (day of week) or 1–31 (day of month) |
| schedule_time | string | HH:MM |
| next_run_date | datetime nullable | |
| last_run_date | datetime nullable | |
| last_run_status | enum nullable | success, failed, pending |
| recipients | JSON | Array of email addresses |
| output_format | enum | pdf, csv, excel, email_summary |
| is_active | boolean | Default: true |
| include_charts | boolean | Default: true |
| notes | text nullable | Free text |
| home_id | int | Multi-tenancy |
| created_by | int | FK → user.id |

### ClientFeedback (23 fields)
| Field | Type | Notes |
|-------|------|-------|
| id | auto-increment int | |
| client_id | int nullable | FK → service_user.id (optional) |
| submitted_by | string | Name of submitter |
| submitted_by_relationship | string nullable | Relationship label |
| feedback_type | enum | compliment, complaint, suggestion, concern, general |
| category | enum | staff_performance, care_quality, communication, punctuality, professionalism, facilities, safety, other |
| rating | int nullable | 1–5 star rating |
| subject | string | Brief title |
| comments | text | Full text |
| related_staff_id | int nullable | FK → user.id |
| related_visit_id | int nullable | FK → visit |
| status | enum | new, acknowledged, in_progress, resolved, closed |
| priority | enum | low, medium, high, urgent |
| assigned_to_staff_id | int nullable | FK → user.id |
| acknowledged_by_staff_id | int nullable | FK → user.id |
| acknowledged_date | datetime nullable | |
| response | text nullable | Staff response text |
| response_date | datetime nullable | |
| action_taken | text nullable | Free text |
| is_anonymous | boolean | Default: false |
| contact_email | string nullable | For follow-up |
| contact_phone | string nullable | For follow-up |
| wants_callback | boolean | Default: false |
| home_id | int | Multi-tenancy |

## Portal Auth Model (from CareRoster)

The portal uses the **same user table** with email-matching, NOT a separate auth guard.

**Flow:**
1. A family member is invited via the standard user invite system (same `user` table)
2. An admin creates a `ClientPortalAccess` record with `user_email` = the family member's email and `client_id` = the linked resident
3. On login, a middleware checks: `ClientPortalAccess::where('user_email', auth()->user()->email)->where('is_active', true)->first()`
4. If found → user is a portal user → redirect to `/portal`, show portal layout/navigation
5. If not found → normal staff/admin flow → redirect to `/roster`

**For Laravel:** Reuse the `user` table. Add a `client_portal_accesses` table. Create a `CheckPortalAccess` middleware. Permission flags (`can_view_schedule`, `can_send_messages`, etc.) live on the `client_portal_accesses` record.

## Portal Data Scoping Rules (GDPR)

Strictly scoped to the linked client — **no cross-resident visibility**.

| Data Type | What Family Can See |
|-----------|-------------------|
| Schedule | Only sessions/shifts where the client_id matches their linked client |
| Messages | Only messages where client_id matches their linked client |
| Booking Requests | Only requests where client_id matches their linked client |
| Feedback | They submit feedback tagged to their client_id; they don't browse others' feedback |
| Staff info | **NOT exposed** — only staff first names shown in messages (no phone, email, address) |
| Other residents | **NO access** — all queries filtered by client_id |

## Messaging Data Flow

| Direction | Who Sends | Who Receives | How |
|-----------|-----------|-------------|-----|
| Family → Care team | Family member (sender_type: 'family') | recipient_type: 'all_staff' | Any staff viewing the messaging center can see and reply |
| Staff → Family | Staff member (sender_type: 'staff') | recipient_type: 'family', recipient_id = portal access ID | Visible to that family member in their portal |
| Threading | Either | Either | Replies set `replied_to_message_id`; original gets `has_reply: true` |
| Staff initiation | Yes | Staff can compose and send to a specific family or all families of a client | Shown in the family's Messages inbox |

**Known gaps (from CareRoster):** No push notifications, messages to `all_staff` are shared inbox (not individually routed), no per-recipient read receipts.

## Workflow Trigger Definitions (8 Pre-built Templates)

CareRoster's workflow engine was **UI-only with fake stats**. We build the real backend. The 8 default workflows:

| Workflow | Trigger Type | Trigger Condition | Action Type | Action Target | Category | Default On |
|----------|-------------|-------------------|-------------|--------------|----------|-----------|
| Shift Reminder | scheduled | 24h before shift | send_email | assigned carer | scheduling | Yes |
| Unfilled Shift Alert | condition | shift unfilled 48h before | send_notification | managers | scheduling | Yes |
| Leave Approval Reminder | scheduled | 48h after leave request | send_notification | managers | hr | Yes |
| Training Expiry Warning | scheduled | 30 days before expiry | send_email | staff member | training | Yes |
| Incident Follow-up | scheduled | 7 days after incident | send_notification | managers | compliance | Yes |
| Missed Medication Alert | event | medication marked missed (MAR code != A) | send_notification | managers | clinical | Yes |
| Client Birthday Reminder | scheduled | 3 days before birthday | send_notification | staff | engagement | No |
| Daily Summary Email | scheduled | daily at 6pm | send_email | managers | reporting | No |

**Trigger types in Laravel:**
- `scheduled` → Laravel scheduler runs a daily artisan command that scans for matching conditions
- `event` → fired from existing service methods (e.g., `MARSheetService::administer()` when code != 'A')
- `condition` → scheduled scan with query conditions (similar to scheduled but with DB checks)

**Template variables:** `{{carer_name}}`, `{{shift_date}}`, `{{shift_time}}`, `{{client_name}}`, `{{incident_type}}`, `{{medication_name}}`

## Report Builder Query Structure

CareRoster runs queries **client-side in JavaScript** (fetches all records, filters in memory). We build **server-side SQL**.

**Queryable entities (map to Care OS models):**
| Entity | Care OS Model/Table | Available Fields |
|--------|-------------------|-----------------|
| Shift | `scheduled_shifts` | date, start_time, end_time, duration_hours, status, shift_type, carer_id, client_id |
| Carer | `user` (where role = staff) | full_name, email, phone, status, employment_type, hourly_rate |
| Client | `service_user` | full_name, status, funding_type, mobility, care_needs |
| Incident | `staff_report_incidents` | incident_type, severity, status, incident_date |
| Training | `staff_training` / training assignments | status, completion_date, score |
| Medication | `mar_administrations` | medication_name, status, administration_time |

**Filter operators:** equals, contains, greater_than, less_than
**Aggregation:** count, sum, average, min, max (SQL-level — `COUNT(*)`, `SUM(column)`, etc.)
**Export formats:** Table view, CSV download, PDF generation

---

## The Care OS Phase 2 Development Pipeline

```
┌─────────┐    ┌──────────┐    ┌─────────┐    ┌────────┐    ┌─────────┐    ┌──────────┐    ┌────────┐    ┌───────────┐    ┌──────┐
│  PLAN   │───▶│ SCAFFOLD │───▶│  BUILD  │───▶│  TEST  │───▶│  DEBUG  │───▶│  REVIEW  │───▶│ AUDIT  │───▶│ PROD-READY│───▶│ PUSH │
└─────────┘    └──────────┘    └─────────┘    └────────┘    └─────────┘    └──────────┘    └────────┘    └───────────┘    └──────┘
     │              │               │              │              │              │              │              │              │
  Plan doc     Migrations &    Working code    Tests pass    Query perf &   Attacks on     Clean scan   Three user      On GitHub
               models/svc                                   queue verify   both roles     + GDPR       journeys
```

## Stage 1: PLAN
**Goal**: Produce a clear, executable plan before any code is written.

1. Read `docs/logs.md` for recent context
2. **Classify the feature** — check CareRoster (Base44):
   - Is the backend **real** (port the schema), **half-real** (port real parts, build missing), or **UI-only** (design backend from scratch)?
   - Read the Base44 entity schema from the reference section above
   - Check CareRoster source (`/Users/vedangvaidya/Desktop/Omega Life/CareRoster/`) for UI reference
3. **Check Care OS for existing overlap** — search for tables, models, controllers, services that already partially exist
4. **Design the migration** — translate Base44 schema → MySQL columns:
   - Base44 `string` → `varchar(255)` or `text` depending on length
   - Base44 `array of objects` → `JSON`
   - Base44 `enum` → `varchar(50)` with `$request->validate(['field' => 'in:val1,val2,...'])`
   - Always add `home_id` (multi-tenancy) and `created_by` even if Base44 schema doesn't have them
   - Always add `is_deleted` (project convention) — NOT Laravel's `SoftDeletes`
   - Always add `created_at`, `updated_at`
5. **Feature-specific planning:**
   - **Portal features**: Plan auth flow — which middleware, which routes behind portal guard, what data is exposed vs hidden (GDPR). Check the Portal Auth Model section above.
   - **Workflow features**: Plan event hookpoints — which existing service methods need to fire events, loop prevention strategy (`max_executions_per_hour`)
   - **Report features**: Plan queryable entities, identify which tables need indexes for aggregation performance, plan export service
6. **Security planning** — same as Phase 1 PLUS:
   - Portal data isolation (scope by `client_id` from `ClientPortalAccess`, not just `home_id`)
   - Portal ↔ admin boundary (portal users must not access admin routes)
   - Workflow loop prevention (max executions per hour)
   - Report query injection prevention (dynamic filters must use parameterised queries)
   - Email header injection prevention (sanitise recipient addresses)
7. Write plan to `phases/` with: goal, files to create, step-by-step, security checklist
8. **STOP — Present the plan to the user and wait for approval before proceeding**

## Stage 2: SCAFFOLD
**Goal**: Generate all boilerplate — Phase 2 scaffolding is heavier than Phase 1 because we're creating new modules from scratch.

1. Create migration(s) from the planned schema
2. Apply migration via tinker `DB::statement()` (artisan migrate has known issues with older migrations)
3. Create model with `$fillable`, `$casts`, relationships, scopes:
   - `scopeForHome($homeId)` — multi-tenancy filter
   - `scopeForClient($clientId)` — portal data isolation (for portal-scoped models)
   - `scopeActive()` — `where('is_deleted', 0)`
4. Create service class with method stubs
5. Create controller with method stubs
6. Add routes with middleware, `throttle`, and `->where()` constraints
7. **Portal features (Feature 1 creates, later features reuse):**
   - Create `CheckPortalAccess` middleware — checks if logged-in user has active `ClientPortalAccess` record
   - Create portal Blade layout (`layouts.portal.master` or similar) — simpler nav with just: Home, Schedule, Messages, Feedback
   - Create portal route group: `Route::prefix('portal')->middleware(['auth', 'portal.access'])->group(...)`
8. **Workflow features:**
   - Create event classes (e.g., `IncidentCreated`, `MedicationMissed`)
   - Create listener stubs
   - Register in `EventServiceProvider`
9. **Report features:**
   - Create export service stubs (CSV, PDF)
   - Create console command stub for scheduled report execution
10. Whitelist new routes in `app/Http/Middleware/checkUserAuth.php`
11. **Brief the user on what was scaffolded**

## Stage 3: BUILD
**Goal**: Implement the feature following the plan.

1. Work through the plan steps in order
2. Read every file before modifying it
3. Follow existing Care OS patterns (check similar Phase 1 features)
4. **All Phase 1 security rules still apply:**
   - Every query filters by `home_id` (multi-tenancy)
   - Every form has `@csrf`
   - Every route has auth middleware
   - Use `{{ }}` not `{!! !!}` for user data
   - Use `{{ url('...') }}` for all URLs
   - Use `$request->validate()` for all input with types, max lengths, enums
   - Rate limiting: `throttle:30,1` on create/update, `throttle:20,1` on delete
   - Models use `$fillable` whitelist, never `$guarded = []`
   - Route constraints: `->where('id', '[0-9]+')` on all parameterised routes
   - XSS client-side: `esc()` helper for all API data before `.html()` in JavaScript
5. **Portal-specific rules (NEW for Phase 2):**
   - Every portal query scopes by `client_id` from the authenticated user's `ClientPortalAccess` record — NOT by `home_id` alone
   - Portal views must **never** expose staff personal details (phone, address, email) — only first name
   - Portal Blade layout must not include admin navigation links
   - Portal middleware must reject users without an active `ClientPortalAccess`
   - Portal users must get 403 on all admin/staff routes
6. **Workflow-specific rules (NEW for Phase 2):**
   - Every workflow execution must be logged to `workflow_execution_logs` with: workflow_id, triggered_at, trigger_data, action_result, status
   - Loop prevention: check execution count in last hour before firing — skip if `>= max_executions_per_hour`
   - Template variable substitution must use `str_replace()` with a whitelist of allowed variables — **never** `eval()` or dynamic code execution
   - Workflows must only fire when `is_active = true`
7. **Report-specific rules (NEW for Phase 2):**
   - All filtering must use Eloquent `where()` clauses with parameterised bindings — **never** interpolate filter values into raw SQL
   - Aggregation must happen at SQL level (`DB::raw('COUNT(*)')` inside `->selectRaw()`) not PHP (`$collection->count()`)
   - Large result sets must be paginated or limited (max 10,000 rows for export)
   - Report queries must be scoped by `home_id` — admins see their home's data only
8. **Post-build checklist (Phase 2 version — ALL mandatory):**
   - [ ] **UI on correct Blade file** — trace route → controller → `return view(...)` to confirm
   - [ ] **UI entry point visible** — button/link not commented out, not `display:none`, not behind broken `@if`
   - [ ] **Route whitelisted in checkUserAuth** — every new route added to `$allowed_path` array
   - [ ] **Test data seeded** — portal test user exists, test data for Aries (home_id 8)
   - [ ] **Portal features: data isolation verified** — query scoped by `client_id`, not leaking other residents
   - [ ] **Portal features: GDPR** — no staff personal details exposed (phone/email/address)
   - [ ] **Workflow features: execution logged** — every trigger → action logged with status
   - [ ] **Workflow features: loop guard** — `max_executions_per_hour` enforced
   - [ ] **Report features: SQL aggregation** — not PHP collection methods
   - [ ] **Report features: parameterised queries** — no raw SQL with user input
   - [ ] **AJAX error callbacks** — show specific messages, not generic "Error"
   - [ ] **Icons use Font Awesome 4.7** — only `fa fa-*` icons
9. Log actions in `docs/logs.md` with teaching notes
10. **Show the user what was built**

## Stage 4: TEST
**Goal**: Verify the feature works, catches regressions, and is secure.

### 4a. Endpoint Tests (same as Phase 1)
- Happy path (valid request → expected response with correct data)
- Authentication (no login → redirect)
- Authorization (wrong role → 403)
- Validation (bad input → 422 with error messages)

### 4b. Multi-Role Tests (NEW for Phase 2)
Test every endpoint as three user types:
- **Admin** → should access admin routes, should be able to manage portal users
- **Staff** (non-admin) → should access staff routes, should not manage portal access
- **Portal user** → should access portal routes only, should get 403 on admin/staff routes
- For each endpoint: test with all three roles and verify correct access/denial

### 4c. Cross-Client Isolation Tests (NEW — extends Phase 1's cross-home IDOR)
- Create portal user A linked to client X
- Create portal user B linked to client Y
- Portal user A queries client Y's messages → expect 404 or empty
- Portal user A queries client Y's schedule → expect 404 or empty
- Portal user A queries client Y's feedback → expect 404 or empty
- Portal user A sends message with `client_id` = client Y → expect rejection
- **PLUS** still test cross-home IDOR on admin-side endpoints (same as Phase 1)

### 4d. Workflow Trigger Tests (NEW)
- Enable workflow → fire matching event → assert notification/email created
- Disable workflow → fire same event → assert nothing happened
- Fire event N times rapidly → assert loop prevention kicks in after `max_executions_per_hour`
- Invalid trigger config → assert graceful failure (logged error, not crash)
- Workflow for wrong event type → assert it doesn't fire

### 4e. Report Accuracy Tests (NEW)
- Seed exactly N known records → run report → assert count = N
- Seed records with known numeric values → run SUM aggregation → assert exact sum
- Apply filter (e.g., severity = 'critical') → assert only matching records returned
- Date range filter → assert records outside range excluded
- Empty result set → assert graceful empty response, not error

### 4f. Scheduled Job Tests (NEW)
- Create scheduled report with `next_run_date` in the past → dispatch console command → assert report generated
- Assert email queued (check via Mail::fake() or log driver)
- Assert `last_run_date` updated, `last_run_status` = 'success'
- Assert `next_run_date` recalculated to next occurrence based on frequency
- Inactive scheduled report → assert skipped

### 4g. Security Payload Tests (same as Phase 1)
- XSS: `<script>alert(1)</script>` in every text field → stored raw, rendered escaped
- SQLi: `' OR 1=1 --` in text/filter fields → 0 results or validation error, no DB error
- CSRF: POST without `_token` → 419
- Oversized input: exceed `max:N` → 422
- Wrong types: string in integer field → 422

### 4h. Run & Report
1. Run: `php -d error_reporting=0 artisan test --filter=[Feature]`
2. Fix any failures
3. **Report test results with explicit count per category (4a–4g)**
4. Run ALL Phase 1 tests to verify no regressions: `php -d error_reporting=0 artisan test`

## Stage 5: DEBUG
**Goal**: Catch runtime errors, performance issues, and background job failures.

1. Clear `storage/logs/laravel.log` (truncate to empty)
2. Hit all routes via curl — **as both admin AND portal user** (two separate cookie jars):
   - Portal routes with portal user cookie
   - Admin routes with admin cookie
3. Check `storage/logs/laravel.log` for new errors/warnings — fix any found
4. **Query profiling (NEW):**
   - For report builder queries: run with `->toSql()` and `EXPLAIN`
   - Flag any full table scans on tables >1000 rows
   - Add indexes if needed (create migration)
5. **Queue job verification (NEW):**
   - Manually dispatch a scheduled report job via tinker
   - Verify it processes without error
   - Verify email appears in `storage/logs/laravel.log` (dev uses `log` mail driver)
6. **Workflow execution trace (NEW):**
   - Trigger a test event (e.g., create an incident)
   - Check `laravel.log` for the full chain: event fired → listener matched → action executed → result logged
   - If using workflow engine: verify `workflow_execution_logs` entry created
7. **Multi-session test (NEW):**
   - Login as admin in one curl session, portal user in another
   - Verify no session bleed — admin session can't see portal data scope, portal session can't access admin routes
8. Check for N+1 queries: list views that query related models without `with()` eager loading
9. Check for dead code: empty methods, commented-out blocks >5 lines, unused `use` imports
10. **Gate: no new errors in `storage/logs/laravel.log` after hitting all routes**

## Stage 6: REVIEW — Adversarial Security Testing
**Goal**: Try to break every endpoint. Think like an attacker, not a checklist auditor.

**CRITICAL RULE**: Do NOT just read code and mark PASS/FAIL. You must **actually exploit** each attack vector using curl against the running dev server.

### Step 1: Start the server and authenticate as BOTH user types
```bash
php artisan serve &

# ---- ADMIN SESSION ----
curl -s -c /tmp/admin.txt http://127.0.0.1:8000/login > /tmp/lp.html
TOKEN=$(cat /tmp/lp.html | sed -n 's/.*name="_token"[^>]*value="\([^"]*\)".*/\1/p' | head -1)
curl -s -c /tmp/admin.txt -b /tmp/admin.txt -L -X POST http://127.0.0.1:8000/login \
  -d "_token=$TOKEN&username=komal&password=123456&home=8"
PAGE=$(curl -s -b /tmp/admin.txt "http://127.0.0.1:8000/roster/client-details/27")
ADMIN_CSRF=$(echo "$PAGE" | sed -n 's/.*<meta name="csrf-token" content="\([^"]*\)".*/\1/p' | head -1)

# ---- PORTAL USER SESSION ----
# (use the test portal user credentials seeded during build)
curl -s -c /tmp/portal.txt http://127.0.0.1:8000/login > /tmp/lp2.html
TOKEN2=$(cat /tmp/lp2.html | sed -n 's/.*name="_token"[^>]*value="\([^"]*\)".*/\1/p' | head -1)
curl -s -c /tmp/portal.txt -b /tmp/portal.txt -L -X POST http://127.0.0.1:8000/login \
  -d "_token=$TOKEN2&username=[portal_user]&password=[portal_pass]&home=8"
PORTAL_PAGE=$(curl -s -b /tmp/portal.txt "http://127.0.0.1:8000/portal")
PORTAL_CSRF=$(echo "$PORTAL_PAGE" | sed -n 's/.*<meta name="csrf-token" content="\([^"]*\)".*/\1/p' | head -1)
```

### Step 2: Attack every endpoint — ALL Phase 1 attacks PLUS:

| # | Attack | Method | Pass if |
|---|--------|--------|---------|
| 1 | CSRF — POST without token | All POST endpoints | 419 |
| 2 | IDOR — cross-home record access | Admin endpoints with other home's record ID | 404 or empty |
| 3 | XSS — `<script>` in text fields | All text inputs | Stored raw, rendered escaped |
| 4 | SQLi — `' OR 1=1 --` | Text/filter inputs | Validation error or empty, not DB error |
| 5 | Mass assignment — `home_id=999, is_deleted=1` | POST to create/update | Fields unchanged |
| 6 | Rate limiting — all POST routes | Check route definition | All have `throttle` |
| 7 | **Portal → admin boundary** | Portal cookie hits admin-only route (e.g., `/roster/client-details/27`) | 403 or redirect |
| 8 | **Admin → portal impersonation** | Admin without `ClientPortalAccess` hits `/portal` routes | 403 or redirect |
| 9 | **Cross-client via portal** | Portal user A hits portal endpoints with client B's `client_id` | 404 or empty |
| 10 | **Cross-client foreign key** | Portal user sends message with wrong `client_id` | Rejected |
| 11 | **Workflow direct invocation** | POST directly to workflow action endpoint (bypass trigger) | 403 or 404 |
| 12 | **Report filter injection** | Filter value = `' OR 1=1 --` in report builder | Validation error, not all records |
| 13 | **Email header injection** | Recipient = `test@evil.com\r\nBCC:attacker@evil.com` in scheduled report | Sanitised or rejected |
| 14 | **Scheduled report tampering** | Mass-assign `next_run_date` to past | Field not in fillable or ignored |
| 15 | XSS in portal messages | `<script>alert(1)</script>` in message body | Escaped in both portal and admin views |
| 16 | XSS in feedback | Script in comments/subject | Escaped in admin view |
| 17 | Auth middleware on all routes | Hit every new route without login | 302 redirect |

### Step 3: UI Reachability Check
Verify BOTH portal and admin UI entry points:
- **Portal**: Is the portal layout rendering? Can the user navigate Home → Schedule → Messages → Feedback?
- **Admin**: Are the Report Builder / Workflow Engine / Portal Management sections reachable from the admin sidebar or relevant page?
- `curl` the URL and grep for a unique element from your feature
- **BLOCKER if**: your element is not in the response — you built on the wrong page

### Step 4: Code Inspection Checklist
| # | Check | Severity |
|---|-------|----------|
| 1 | Data isolation — portal queries filter by `client_id`, admin queries filter by `home_id` | BLOCKER |
| 2 | Auth middleware on all routes | HIGH |
| 3 | Portal middleware on all `/portal/*` routes | HIGH |
| 4 | `$fillable` whitelist, no `$guarded = []` | HIGH |
| 5 | Route constraints `->where('id', '[0-9]+')` on all params | MEDIUM |
| 6 | Audit logging — `Log::info()` on create/update/delete | MEDIUM |
| 7 | DB integrity — indexes, proper `down()` in migrations | MEDIUM |
| 8 | Error handling — no stack traces/internal paths leaked | MEDIUM |
| 9 | Workflow loop guard — `max_executions_per_hour` checked before every execution | HIGH |
| 10 | Report queries use parameterised bindings, not string interpolation | BLOCKER |

### Step 5: Report & Fix
1. Report every attack attempted and result (PASS with evidence / FAIL with exploit details)
2. Fix ALL BLOCKER and HIGH failures immediately
3. **Re-run the failed attacks after fixing** to confirm the fix works
4. **Report final results to user**

## Stage 7: AUDIT
**Goal**: Ensure no regressions and final security sweep.

Run ALL Phase 1 grep patterns on new/modified files:
1. `grep -rn 'DB::raw\|->whereRaw\|->selectRaw' [new files]` — zero results expected (or justified exceptions for report aggregation with parameterised values only)
2. `grep -rn '{!!' [new blade files]` — zero results expected
3. `grep -rn '\.html(\|\.innerHTML' [new JS/blade files]` — every match must use `esc()` for user data
4. `grep -n 'Route::post' routes/web.php | grep -v throttle` — zero new POST routes without rate limiting
5. `grep -rn '\$guarded\s*=\s*\[\]' [new model files]` — zero results expected
6. `grep -rn 'dd(\|dump(\|console\.log(' [new files]` — zero debug statements
7. `grep -rn 'http://127\|http://localhost\|http://care' [new blade files]` — zero hardcoded URLs
8. Check for new backup/duplicate files (`.bak`, `.old`, `Copy of`)
9. Verify route loading: `php artisan route:list 2>&1 | grep -i error`

**Phase 2-specific audit checks (NEW):**
10. **GDPR check**: `grep -rn 'phone\|email\|address' [portal blade views]` — staff personal details must NOT be displayed to portal users. Only `name` is acceptable.
11. **Queue config check**: Verify scheduled command is registered in `app/Console/Kernel.php` (for scheduled reports)
12. **Email template check**: Grep email blade views for raw URLs — no user-supplied data in URLs without signed tokens
13. **Workflow loop check**: Verify every `AutomatedWorkflow` record has `max_executions_per_hour` with a sane default (e.g., 10)
14. **Portal middleware check**: Verify all `/portal/*` routes go through the `CheckPortalAccess` middleware
15. **Phase 1 regression**: Run `php -d error_reporting=0 artisan test` — all existing tests must still pass

## Stage 8: PROD-READY — Verified, Not Self-Graded
**Goal**: Verify the feature is ship-quality through actual testing, not code reading.

**CRITICAL RULE**: Every check below must be verified by actually hitting the endpoint or reading the rendered response.

### 8a. Error & Edge Cases (hit with curl)
- Portal user with no linked client (deleted `ClientPortalAccess` record) → graceful error or redirect, not 500
- Report with zero results → empty state message, not crash
- Workflow with invalid action config → logged error, not 500
- Scheduled report with no recipients → skipped with log entry, not crash
- Portal user whose linked client was soft-deleted → graceful handling
- Message to/from deleted portal user → no crash

### 8b. Performance
- Report queries use indexes (verify with EXPLAIN output)
- Portal list views use eager loading (`with()`)
- No N+1 on message threads (messages + sender details)
- Workflow evaluation doesn't run expensive queries on every request (only on matching events)

### 8c. UI/UX Quality
- Portal layout renders correctly (NOT admin layout)
- All portal navigation links work (Home, Schedule, Messages, Feedback)
- Admin report builder filters actually filter the results
- Workflow toggle actually enables/disables (check DB after toggle)
- Portal shows correct resident name and details

### 8d. Three User Journeys (NEW — Phase 2 requires three, not one)

**Journey 1 — Portal User:**
Family login → portal dashboard (resident name, stat cards) → click Schedule (see shifts) → click Messages (send a message) → click Feedback (submit feedback) → logout

**Journey 2 — Report Builder (Admin):**
Admin login → navigate to Report Builder → select entity (e.g., Incidents) → add filter (severity = critical) → run report → verify results → export CSV → save report config → create schedule (weekly, Monday, 9am) → verify schedule appears in list

**Journey 3 — Workflow (Admin):**
Admin login → navigate to Workflows → see pre-built workflows → enable "Incident → notify manager" → go create a new incident → return to Workflows → see execution log entry → verify notification was created

### 8e. Manual Test Checklist
Print a numbered checklist for the user covering all testable paths for the feature just built. Include:
- Portal user actions (if portal feature)
- Admin actions (if admin feature)
- Cross-role verification (portal user can't do admin things, admin can manage portal)
- Edge cases (empty states, deleted data, expired access)

**Gate: user confirms "tested" before PUSH**

## Stage 9: PUSH
**Goal**: Commit and push to GitHub.

1. `git status` — review all changed/new files
2. `git diff` — review all changes
3. `git log --oneline -3` — check recent commit style
4. Stage specific files (`git add` — **never** `git add -A`)
5. Commit with descriptive message:
```bash
git commit -m "$(cat <<'EOF'
Feature N description: what was built, key details

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
EOF
)"
```
6. Push: `git push origin komal:main`
7. Update `docs/logs.md` with commit hash
8. Confirm to user with commit hash and push status

---

## Key Differences from Phase 1 Workflow

| Aspect | Phase 1 | Phase 2 |
|--------|---------|---------|
| Starting point | Existing tables/models/controllers | CareRoster schemas → new from scratch |
| Feature classification | All "wire existing" | Port / Build for real / Finish |
| Auth model | Single user type (admin/staff) | Two auth boundaries (admin/staff + portal family) |
| Data isolation | `home_id` only | `home_id` (admin) + `client_id` (portal) |
| Background jobs | None | Scheduled reports (queue), workflow triggers (events/scheduler) |
| Performance concerns | N+1 queries | N+1 + report query performance + aggregation efficiency |
| Testing | Single-role endpoint tests | Multi-role + cross-client isolation + trigger tests + accuracy tests |
| Security review | Curl attacks as one user | Attacks as BOTH admin and portal user |
| PROD-READY | One user journey | Three user journeys (portal, report, workflow) |
| GDPR | Not applicable | Portal data scoping, staff detail suppression |
