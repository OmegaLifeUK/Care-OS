# Session 11 — Body Maps Integration into New Care Roster UI

**Date:** 2026-04-16
**Branch:** komal
**Working directory:** `/Users/vedangvaidya/Desktop/Omega Life/Care OS`
**Focus:** Finish body map risk integration, surface body map on profile, discover the wrong-UI problem, then re-wire everything into the new Care Roster `client_details` page.

---

## Opening context

Started by Vedang asking whether to begin a new feature or "attach body maps to risk assessment UI first." I read the pending diff and session 10 notes to understand what was left from the body map work (staff_id filter, hover verification, profile integration). Gave a two-sentence recommendation to finish the risk popup integration before starting Feature 4.

---

## 1. Reading the state of Phase 1

Vedang asked me to read the sessions and logs to understand what's left. I read:

- `docs/logs.md` (Logs 1–41) — full history of environment setup, Phase 0 cleanup, Phase 1 Features 1–3 (Incident Management, Staff Training, Body Maps), the security hardening rounds, the CLAUDE.md and /workflow updates, and the Session 10 body-map gender/colour work.
- `sessions/session10.md` — the 5-round colour-persistence debug marathon + gender filter work, with the open items at the end (hover fix unverified, `staff_id` filter still in `RiskController@view`, standalone `body_map.blade.php` not gender-filtered, pre-existing broken migration blocking bulk `artisan migrate`, phase1.md stale at 1/9 while really 3/9, session 10 changes all uncommitted).

Gave Vedang a punch-list:

- Phase 1: 3/9 done (Incident Management, Staff Training, Body Maps)
- Pending: Handover Notes, DoLS, MAR Sheets, SOS Alerts, Notifications, Safeguarding
- Body Maps loose ends: hover fix unverified, `staff_id` filter, standalone body_map.blade.php not gender-filtered, pre-existing broken migration, phase1.md stale, session 10 uncommitted

---

## 2. Vedang: "start the server again"

Started `php -d error_reporting=0 artisan serve` in the background. Verified with curl — 302 redirect to login, as expected.

---

## 3. "Where should we attach body maps in the UI?"

Recommended two spots:

1. **Risk assessment popup** — already the primary home (risk.blade.php → body_map_popup), 90% wired.
2. **Dedicated Body Map section on the service user profile page** — profile.blade.php already includes body_map_popup.blade.php via a modal, just needs a trigger.

Vedang said "perform your recommendation."

---

## 4. Executing the recommendation — Round 1 (old UI)

### Task 1 — Lift `staff_id` filter in `RiskController@view`

Root cause: at `RiskController.php:283`, the `BodyMap::select(...)->where('staff_id', $staff_id)...` clause meant carers only saw injuries they themselves had recorded, not ones another staff member had added. This directly blocked "body map attached to risk UI" from being usable across a team.

**Fix:** removed `->where('staff_id', $staff_id)`, moved the `home_id` authorisation check to run BEFORE the query, then re-ran the query with defence-in-depth `->where('home_id', $home_id)` scoping. Result: every staff member now sees every injury recorded on a risk assessment, multi-tenancy still enforced.

### Task 2 — Verify hover/muscle3x fix

Read `body_map_popup.blade.php:899-907` and confirmed:

- `paintInjuryPath` still has `.off('mouseenter mouseleave mouseover mouseout mousedown mouseup')` to strip muscle3x's hover rebinding.
- `window.paintInjuryPath = paintInjuryPath` and `window.clearInjuryPath = clearInjuryPath` still exposed for `risk.blade.php` to call from outside the IIFE.

All good.

### Task 3 — Profile page read-only aggregated body map

Plan: add a new JSON endpoint that returns all active injuries for a service user across all risks, extend the popup to handle an "aggregated/read-only" mode, and add a trigger button on `profile.blade.php` next to the Calendar icon.

Changes made:

**BodyMapController** — added `listForServiceUser(int $serviceUserId)`:
- Gets home_id from auth.
- Verifies the service user belongs to this home (404 if not).
- Calls `BodyMapService::listForServiceUser($homeId, $serviceUserId)` (which already existed — returned all active injuries for SU).
- Returns JSON `{success: true, data: [...]}`.

**routes/web.php** — registered `GET /service/body-map/service-user/{service_user_id}/list` with an integer `where()` constraint. Placed BEFORE the wildcard `{risk_id}` route so it isn't shadowed.

**body_map_popup.blade.php** — three changes:

1. Added a new hidden input `<input type="hidden" name="bm_aggregated_su_id">` to the title block.
2. Rewrote `shown.bs.modal` handler to branch: if `bm_aggregated_su_id` is set, fetch from the new SU endpoint; else if `su_rsk_id` is set, fetch from the risk endpoint; else return. Toggles `.bm-readonly` class on the modal in aggregated mode.
3. Added `hidden.bs.modal` handler to reset the aggregated flag and remove the read-only class when the modal closes (so next open picks the right mode).
4. Extended the click handler to short-circuit in read-only mode: only show the info modal for active body parts, hide the remove button, skip the add-injury flow entirely.
5. The normal (risk) path now explicitly `.show()`s the remove button in case a prior read-only open hid it.

**profile.blade.php** — added a `fa-male` icon anchor `openBodyMapProfile` right next to the Calendar link in the `.profile-bigico` row, with `data-service-user-id="{{ $service_user_id }}"`. Added a small script block at the top of the trailing `<script>` that handles `.openBodyMapProfile` clicks — sets `bm_aggregated_su_id`, clears `su_rsk_id`, opens `#bodyMapModal`.

**Lint + smoke test:**

```
php -l app/Http/Controllers/frontEnd/ServiceUserManagement/RiskController.php  ✓
php -l app/Http/Controllers/frontEnd/ServiceUserManagement/BodyMapController.php  ✓
php -l routes/web.php  ✓
curl /service/body-map/service-user/27/list → 302 (login redirect, as expected)
```

Told Vedang the integration was complete, listed the two URLs to test.

---

## 5. "Nothing is clickable" — the wrong-UI discovery

Vedang shared `Screenshot 2026-04-15 at 10.08.48 PM.png` showing `/roster/client-details/27` — a screen with a Risk Assessments tab containing 6 cards, each with eye and trash icons that didn't react. Reply: this isn't the screen I modified, this is the **new half-built Care Roster mockup** at `/roster/client-details/{id}`. The eye button's only binding (line 8786 of `client_details.blade.php`) toggles between two hardcoded mock sections — it never calls the backend.

Showed Vedang the two real URLs to test instead: `/service/user-profile/{id}` (new body map icon) and `/service/risks/{id}` (staff_id fix).

### "tell me the ui path then, dont give me urls"

Tried to give a navigation path for the old UI (Residential Care → Clients → profile page). Vedang followed it and sent a second screenshot — `client-details/180` for Alex Sheffield — still on the new Care Roster page with no Body Map icon and no clickable risks.

**Root cause:** the Care Roster sidebar's "Clients" link goes to `/roster/client` which opens `/roster/client-details/{id}` — the new half-built UI. The old `/service-user-management` → `/service/user-profile/{id}` flow (where I added the Body Map icon) has **no link from the Care Roster sidebar**. The user was permanently stuck in the new UI and couldn't reach my integration through menu navigation.

Verified with greps:
- `resources/views/frontEnd/roster/common/roster_header.blade.php` has no `service-user-management` or `service/user-profile` link.
- `resources/views/frontEnd/dashboard.blade.php:25` is where the old UI is linked from, but that's the old dashboard (reached at `/`), not the Care Roster one.

Told Vedang the truth: my integration targeted the wrong screen. Offered three options: test the old UI, move the work to the new UI, or do both.

---

## 6. "Move the work to the new UI" — Round 2 (new Care Roster UI)

### Task 4 — Wire `ClientController@client_details` to real data

The old controller was minimal: no home_id scoping, no risks loaded, no patient passed. I rewrote it to:

- Read `home_id` from `Auth::user()->home_id` (comma-separated) via `explode`.
- Fetch the `ServiceUser` where `id = client_id AND home_id = $home_id AND is_deleted = 0`. If missing, `abort(404)`. This adds the multi-tenancy check that was entirely absent from this screen.
- Assign `$data['patient'] = $patient` (the body_map_popup's gender filter reads `$patient->gender`).
- Assign `$data['service_user_id'] = $client_id` (the popup script reads this for aggregated mode).
- Query `su_risk` joined to `risk` scoped by `service_user_id` + `home_id` + `risk.is_deleted = 0`, ordered by `created_at` desc, as `$data['risks']`.

**Security note:** this controller was previously letting anyone with the URL open any client across homes. That's now closed.

### Task 5 — Replace mocked risk cards with `@foreach`

Replaced the 6 hardcoded `planCard` blocks (lines ~602-727 of `client_details.blade.php`) with a single `@forelse($risks ?? [] as $risk)` loop. Each card renders:

- The real `$risk->description` as the title.
- A status badge using `$statusMap['1' => historic/yellow, '2' => live/radShowbtn, '3' => no risk/greenTag]`.
- `Assessed:` date formatted from `$risk->created_at`.
- An eye button replaced with a new `.realRiskBodyMapBtn` carrying `data-su-risk-id="{{ $risk->id }}"` (removed the mocked trash button that did nothing).

`@empty` block shows "No risk assessments recorded for this client yet."

### Task 6 — Body Map button in header + popup include + JS handlers

**Header button** — added a `<button class="btn borderBtn openBodyMapProfile" data-service-user-id="{{ $service_user_id }}">` with a `bx-body` icon between "Edit Client" and "Import Documents" in the `.header-actions` row.

**Popup include** — added `@include('frontEnd.serviceUserManagement.elements.risk_change.body_map_popup')` just before `@endsection` at the bottom of `client_details.blade.php`. The popup now pulls in the 50+ KB body map SVGs, CSS, and all the JS we built over sessions 7–10.

**Click handlers** — appended two handlers to the existing trailing script block:

```js
$(document).on('click', '.openBodyMapProfile', function() {
    var suId = $(this).data('service-user-id');
    $('input[name=bm_aggregated_su_id]').val(suId);
    $('input[name=su_rsk_id]').val('');
    $('#bodyMapModal').modal('show');
});

$(document).on('click', '.realRiskBodyMapBtn', function() {
    var riskId = $(this).data('su-risk-id');
    $('input[name=bm_aggregated_su_id]').val('');
    $('input[name=su_rsk_id]').val(riskId);
    $('#bodyMapModal').modal('show');
});
```

The first opens the modal in aggregated read-only mode (every injury across every risk). The second opens it in risk mode against a specific `su_risk_id` for add/remove.

**Lint + smoke test:**

```
php -l app/Http/Controllers/frontEnd/Roster/Client/ClientController.php  ✓
php artisan view:clear  ✓
curl /roster/client-details/27 → 302
```

Laravel log had one `OnboardingConfigurationController does not exist` error — pre-existing from when I tried `artisan route:list` earlier, unrelated to our work.

---

## Files changed this session

| File | Change |
|---|---|
| `app/Http/Controllers/frontEnd/ServiceUserManagement/RiskController.php` | Removed `staff_id` filter from `view()` body map query; moved home_id auth check above the query; added explicit `home_id` scoping on the BodyMap SELECT |
| `app/Http/Controllers/frontEnd/ServiceUserManagement/BodyMapController.php` | New method `listForServiceUser(int $serviceUserId)` — returns all active injuries for a SU across risks, home_id scoped, 404 on wrong home |
| `routes/web.php` | New route `GET /service/body-map/service-user/{service_user_id}/list` → `BodyMapController@listForServiceUser` |
| `resources/views/frontEnd/serviceUserManagement/elements/risk_change/body_map_popup.blade.php` | New `bm_aggregated_su_id` hidden input; `shown.bs.modal` handler branches on risk vs aggregated mode and toggles `.bm-readonly`; `hidden.bs.modal` resets flags; click handler short-circuits in read-only mode (no add, remove button hidden, info modal still shows) |
| `resources/views/frontEnd/serviceUserManagement/profile.blade.php` | New `fa-male` Body Map icon next to Calendar in the `.profile-bigico` row; click handler for `.openBodyMapProfile` → aggregated mode |
| `app/Http/Controllers/frontEnd/Roster/Client/ClientController.php` | `client_details()` now home_id scoped (404 on wrong home), loads `$patient` (ServiceUser), `$service_user_id`, and `$risks` (su_risk join risk, ordered desc) |
| `resources/views/frontEnd/roster/client/client_details.blade.php` | New **Body Map** button in header with `bx-body` icon; 6 hardcoded planCard blocks replaced with `@forelse` over `$risks`; each real card has a `.realRiskBodyMapBtn` with `data-su-risk-id`; popup `@include` added before `@endsection`; two click handlers appended (aggregated + per-risk) |

No new migrations, no schema changes.

---

## Commands run (highlights)

```bash
# Server
php -d error_reporting=0 artisan serve        # started in background, bfzi1re0i
curl -s http://127.0.0.1:8000                 # 302, app up
curl -s http://127.0.0.1:8000/service/body-map/service-user/27/list  # 302
curl -s http://127.0.0.1:8000/roster/client-details/27               # 302

# Lint + cache
php -l app/Http/Controllers/frontEnd/ServiceUserManagement/RiskController.php
php -l app/Http/Controllers/frontEnd/ServiceUserManagement/BodyMapController.php
php -l routes/web.php
php -l app/Http/Controllers/frontEnd/Roster/Client/ClientController.php
php artisan route:clear
php artisan view:clear
```

Pre-existing failure: `php artisan route:list --path=body-map` throws `Class "OnboardingConfigurationController" does not exist` (stale route binding, unrelated to this work).

---

## Session Status at End

### ✅ Done

**Round 1 (old UI — still valid as a fallback):**
- `RiskController@view` no longer filters body map injuries by `staff_id` — carers now see every injury on a risk assessment.
- Hover fix (`paintInjuryPath .off(...)`) and `window.*` exposure verified in `body_map_popup.blade.php`.
- New endpoint `GET /service/body-map/service-user/{id}/list` returns all active injuries for a SU across risks, home_id scoped.
- `body_map_popup.blade.php` supports aggregated read-only mode via `bm_aggregated_su_id` hidden input.
- `profile.blade.php` has a new Body Map icon next to Calendar that opens the modal in aggregated mode.

**Round 2 (new Care Roster UI — now the primary path):**
- `ClientController@client_details` is home_id scoped (was completely open before).
- `client_details.blade.php` Risk Assessments tab renders real `su_risk` rows via `@forelse`, not the 6 hardcoded mocked cards.
- Header has a new **Body Map** button that opens the popup in aggregated mode.
- Each real risk card has a body-map icon button that opens the popup in risk mode for add/remove.
- `body_map_popup.blade.php` is now `@included` from the Care Roster `client_details` page.

### ◼ Needs Vedang to verify in browser

- Hard-refresh `/roster/client-details/180` (Alex Sheffield).
- Header Body Map button → modal opens in read-only mode → shows all active injuries across risks.
- Each risk row's body-map icon → modal opens in risk mode → add/remove works against that su_risk_id.
- Gender filter still applied on the new page (uses `$patient->gender`, which is passed through).

### Deferred

- Standalone `body_map.blade.php` (PNG-background variant) still unreachable and not gender-filtered.
- Pre-existing broken migration `2025_11_20_111238_add_is_completed_to_staff_task_allocation_table` still blocks bulk `artisan migrate`.
- `phases/phase1.md` pipeline table still says 1/9 done while reality is 3/9.
- Pre-existing stale route `OnboardingConfigurationController` still breaks `artisan route:list`.
- Session 10 + Session 11 changes are all still uncommitted on `komal`.
- `client_details.blade.php` still has massive amounts of hardcoded mock content (Care Plan, Medication, PEEP, Repositioning, Behavior, Mental Capacity, DoLS, DNACPR, Safeguarding tabs) — only the Risk Assessments tab is wired to real data.

### Next

1. Vedang verifies the Care Roster `client_details` integration works end-to-end (Body Map button, risk card icons, gender filter, aggregated vs risk mode).
2. Commit Session 10 + Session 11 work as one or two logical commits.
3. Update `phases/phase1.md` to reflect 3/9 done.
4. Decide next feature: Handover Notes is next in recommended build order.
