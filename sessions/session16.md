# Session 16 — Feature 7: SOS Alerts (Full Build) + Feature 8 Prompt

**Date:** 2026-04-20
**Branch:** `komal`
**Working Directory:** `/Users/vedangvaidya/Desktop/Omega Life/Care OS`
**Commit:** `6fb53ad1` pushed to main

---

## Session Overview

Built Feature 7 (SOS Alerts) end-to-end using `/careos-workflow`, discovered and fixed a wrong-Blade-file bug, updated the workflow to prevent recurrence, then wrote the Feature 8 (Notifications) prompt with all lessons learned baked in.

---

## Conversation Flow

### 1. User provided Feature 7 pre-built plan
User pasted the full SOS Alerts plan from `phases/feature7-sos-alerts-prompt.md` with detailed analysis of what exists (API only, no web UI) and what needs building.

### 2. PLAN stage — Verified current state
**Actions taken:**
- Read `docs/logs.md` for prior context
- Ran `DESCRIBE sos_alerts` — confirmed 6 columns: id, staff_id, location, deleted_at, created_at, updated_at
- Read `app/Models/staffManagement/sosAlert.php` — confirmed bare model (no $fillable, no relationships)
- Read `StaffManagementController@sos_alert` — confirmed API creates alerts + notifies managers (event_type 24), leaks `$e->getMessage()`
- Read `sticky_notification.blade.php` — confirmed handles types 4,11,14,15,16,17,18,21 but NOT 24
- Read `dashboard.blade.php` — checked layout structure (col-9 main + col-3 sidebar)
- Read `checkUserAuth.php` — checked `$allowed_path` array at line 128
- Checked route structure for roster group in web.php

**Presented plan for approval.** User approved.

### 3. BUILD stage — Created all files

**Step 1: Migration** — `2026_04_20_160000_add_columns_to_sos_alerts.php`
- Added: home_id, message, status, acknowledged_by, acknowledged_at, resolved_by, resolved_at, is_deleted
- Added indexes on home_id and status
- Ran migration successfully

**Step 2: Fixed Model** — `app/Models/staffManagement/sosAlert.php`
- Added $fillable (staff_id, home_id, location, message, status, acknowledged_by/at, resolved_by/at)
- Added casts (status→integer, acknowledged_at→datetime, resolved_at→datetime)
- Added scopes: active(), forHome()
- Added relationships: staff(), acknowledgedByUser(), resolvedByUser()

**Step 3: Created Service** — `app/Services/Staff/SosAlertService.php`
- trigger(): creates alert + notifies all managers via Notification model
- list(): paginated list with eager loading (with() for staff, acknowledgedByUser, resolvedByUser)
- acknowledge(): sets status=2, verified home_id match
- resolve(): sets status=3, appends resolution notes to message
- All methods filter by home_id, include Log::info() audit logging

**Step 4: Created Controller** — `app/Http/Controllers/frontEnd/Roster/SosAlertController.php`
- trigger(), list(), acknowledge(), resolve()
- Input validation on all POST endpoints
- Role checks: acknowledge/resolve require user_type M or A
- home_id set server-side from Auth::user()
- Generic error messages (no exception leaking)

**Step 5: Routes + Middleware**
- Added 4 POST routes in roster group with rate limiting:
  - trigger: throttle:5,1 (sensitive)
  - list: throttle:30,1
  - acknowledge: throttle:20,1
  - resolve: throttle:20,1
- Added SosAlertController import to web.php
- Whitelisted 4 routes in checkUserAuth.php $allowed_path

**Step 6: Dashboard UI** — Added to `dashboard.blade.php` (THIS WAS THE WRONG FILE — fixed later)
- Red SOS trigger button
- SOS Alert History section with container div
- SOS Trigger modal and Resolve modal
- JS include and baseUrl/user_type hidden fields

**Step 7: Sticky Notification** — `sticky_notification.blade.php`
- Added event_type_id 24 case
- Looks up staff name from sos_alerts → user table
- Sets su_name to staff name (SOS is staff-triggered, not client-related)

**Step 8: JavaScript** — `public/js/roster/sos_alerts.js`
- esc() helper for XSS prevention
- triggerSosAlert() — confirm modal → POST → reload list
- loadSosAlerts() — AJAX list → render color-coded cards (red/amber/green)
- acknowledgeSosAlert() — confirm → POST → reload
- resolveSosAlert() — modal for notes → POST → reload
- Error callbacks on all AJAX calls with specific messages

**Step 9: Test data** — Seeded 3 records for Aries (home_id 8):
- ID 1: Active, "Test SOS alert - staff member needs help"
- ID 2: Acknowledged, "Fire alarm triggered in kitchen area"
- ID 3: Resolved, "Medical emergency resolved"

**Step 10: Fixed API controller** — `StaffManagementController.php`
- Changed `$e->getMessage()` to generic "Something went wrong. Please try again."

### 4. TEST stage — 13/13 passing

**Tests written** (`tests/Feature/SosAlertTest.php`):
- 4a (Happy path): trigger, list, acknowledge, resolve — 4 tests
- 4b (Flow): full trigger→ack→resolve sequence — 1 test
- 4c (IDOR + Access): cross-home list/ack/resolve blocked, staff can't ack/resolve — 5 tests
- 4d (Security): XSS stored safely, oversized message rejected, non-integer ID rejected — 3 tests

Initial run: 3 failures (validation tests returned 302 instead of 422 — needed `postJson` instead of `post`). Fixed and re-ran: 13/13 passing.

### 5. DEBUG stage — Clean
- Cleared laravel.log
- Hit all 5 endpoints (dashboard 200, list 200, trigger 200, acknowledge 200, resolve 200)
- 0 errors in laravel.log
- No N+1 queries (service uses with() eager loading)

### 6. REVIEW stage — 12 attacks, all PASS

| Attack | Result |
|--------|--------|
| IDOR cross-home acknowledge | PASS — "Alert not found" |
| IDOR cross-home resolve | PASS — "Alert not found" |
| IDOR cross-home list | PASS — test confirms no cross-home IDs |
| CSRF without token | PASS — HTTP 419 |
| XSS in message | PASS — stored raw, `{{ }}` and esc() escape on output |
| Mass assignment (home_id=999) | PASS — stored as home_id=8 (server-side) |
| SQL injection | PASS — rate limited (429), Eloquent only |
| Rate limiting | PASS — all 4 routes have throttle middleware |
| Access control (staff ack/resolve) | PASS — 403 for staff |
| Error leaking | PASS — generic message |
| UI reachability | PASS — button renders on dashboard |
| Client-side XSS (.html()) | PASS — all user data through esc() |

### 7. AUDIT stage — Clean
All grep patterns passed: no hardcoded URLs, no {!!}, no $guarded=[], no debug statements, no hardcoded localhost (pre-existing ones in commented section only), routes load without errors.

### 8. PROD-READY — Presented checklist

Presented manual test checklist pointing to `/roster/dashboard`.

### 9. BUG: Wrong Blade file!

**User sent screenshot** showing `/roster` page (index.blade.php) — no SOS button visible.

**Root cause:** Built the SOS UI on `dashboard.blade.php` (rendered at `/roster/dashboard`) but the user's actual landing page is `index.blade.php` (rendered at `/roster`). The sidebar "Dashboard" link goes to `/roster`, not `/roster/dashboard`.

**Fix:** Added all SOS elements (button, history section, modals, JS include) to `index.blade.php`. Verified with curl that elements render at `/roster`.

### 10. Workflow + Memory update (preventing recurrence)

User requested workflow and memory updates so this class of bug never happens again.

**Workflow changes (`careos-workflow.md` — both .claude/commands/ and docs/ copies):**
1. **PLAN stage (step 5)** — Added "CRITICAL — Verify which Blade file the target URL actually renders" with route→controller→view() tracing requirement
2. **BUILD stage (post-build checklist)** — Added new mandatory check #1: "UI on the CORRECT Blade file" with curl-grep verification
3. **REVIEW stage (Step 3)** — Added "Is the UI on the correct page?" as BLOCKER before existing reachability check
4. **PROD-READY stage (8c)** — Added "MANDATORY FIRST CHECK" requiring curl-grep of target URL

**CLAUDE.md** — Added "Roster page mapping" section:
- `/roster` → `index.blade.php` (main dashboard users see)
- `/roster/dashboard` → `dashboard.blade.php` (old/unused)

**Memory updates:**
- Updated `feedback_ui_entry_point.md` — added Feature 7 incident alongside Feature 4
- Created `feedback_verify_blade_file.md` — new memory for route→controller→view tracing rule
- Updated `MEMORY.md` index

### 11. User confirmed "tested"

User tested in browser: SOS button visible, trigger works ("Test" and "Fire breakout" created), acknowledge/resolve work.

**Bug found:** Test record with 2000 "A" characters overflowing card.
**Fix:** Added `word-break: break-word` CSS to cards + truncated messages at 200 chars in JS. Cleaned up test junk data (kept only 3 seed records).

### 12. PUSH — Committed and pushed

**Commit:** `6fb53ad1` — "Feature 7 SOS Alerts: web UI, sticky notifications, acknowledge/resolve, 13 tests"
**Push:** `git push origin komal:main` succeeded (5db04c0c..6fb53ad1)
**Files changed:** 19 files, 1,810 insertions, 6 deletions

### 13. Feature 8 prompt written

User asked for Feature 8 prompt with all past mistakes prevented.

**Explored notifications codebase:**
- `notification` table: 14 columns, 881 records for home 8
- `notification_event_type` table: 24 event types
- `app/Notification.php`: 1,294 lines, static methods for rendering
- Sticky notification system in footer (Gritter popups)
- Bell icon in sidebar at line 525 — points to `#!` (not wired)
- API endpoints exist: /notifications/list, /notifications/count

**Created `phases/feature8-notifications-prompt.md`** with:
- Full plan for Notification Centre page at `/roster/notifications`
- Bell icon with unread count badge on all roster pages
- Mark read / mark all read functionality
- **"Lessons Learned" section** covering all 8 mistakes from Features 1-7 with specific prevention steps
- Post-Build Verification Checklist (7 mandatory curl/grep checks)
- Security checklist noting VARCHAR home_id requires FIND_IN_SET

---

## Files Created This Session
- `database/migrations/2026_04_20_160000_add_columns_to_sos_alerts.php`
- `app/Services/Staff/SosAlertService.php`
- `app/Http/Controllers/frontEnd/Roster/SosAlertController.php`
- `public/js/roster/sos_alerts.js`
- `tests/Feature/SosAlertTest.php`
- `phases/feature7-sos-alerts-prompt.md` (pre-existing, committed)
- `phases/feature8-notifications-prompt.md`

## Files Modified This Session
- `app/Models/staffManagement/sosAlert.php` — added $fillable, casts, scopes, relationships
- `app/Http/Controllers/Api/Staff/StaffManagementController.php` — fixed error leaking
- `app/Http/Middleware/checkUserAuth.php` — whitelisted SOS routes
- `routes/web.php` — added SOS routes with rate limiting
- `resources/views/frontEnd/common/sticky_notification.blade.php` — added event_type 24
- `resources/views/frontEnd/roster/dashboard.blade.php` — added SOS UI (also on index)
- `resources/views/frontEnd/roster/index.blade.php` — added SOS UI (correct page)
- `.claude/commands/careos-workflow.md` — added Blade file verification at 4 stages
- `docs/careos-workflow.md` — synced with above
- `CLAUDE.md` — added roster page mapping
- `docs/logs.md` — logged Feature 7 build

---

## Session Status at End

### Done
- Feature 7 (SOS Alerts) — COMPLETE, pushed as `6fb53ad1`
- Phase 1 progress: 7/10 features done (1-7)
- Workflow updated with Blade file verification at 4 checkpoints
- Feature 8 prompt ready at `phases/feature8-notifications-prompt.md`

### What's Next
- Feature 8: Notification Centre — prompt ready, run `/careos-workflow`
- Feature 9: Safeguarding — pending
- Feature 10: Care Roster Wire-Up — pending
- Update `phases/phase1.md` progress tracker (Feature 7 now done)
