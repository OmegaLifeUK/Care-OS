# Feature 10 — Care Roster Wire-Up

**Phase:** 1 (addendum)
**Estimated effort:** 10h
**Branch:** `komal`
**File under audit:** `resources/views/frontEnd/roster/client/client_details.blade.php` (~9,000 lines)

## Purpose

`client_details.blade.php` was ported from Base44/React HTML mockups and only partially wired. Of ~95 interactive elements, the April 2026 audit found ~35 wired and ~60 unwired. The unwired buttons look clickable but do nothing.

Many of the unwired buttons will be fixed naturally as other Phase 1 features ship (Medication → MAR buttons, Safeguarding → referral button, etc.). **Feature 10 tracks only the remaining orphans** — buttons in tabs that no other phase is going to touch.

---

## 1. Buttons already fixed (Apr 16, 2026 session)

### 1.1 Risk Assessments tab — real risk cards with body map button

**File:** `resources/views/frontEnd/roster/client/client_details.blade.php:3254`

**Problem:** `#clientRiskAssessmentsTab` (line 3245) contained 6 hardcoded `<div class="planCard">` mockups with placeholder text ("general", "Substance misuse…", "Dental health…"). None of them had the `.realRiskBodyMapBtn` wired to open the body map in edit mode. The eye and trash icons on the cards had no handlers. Data-bound version of the loop existed elsewhere in the file (`.onboardContent` block, line 603) but was hidden.

**Fix:** Replaced the 6 static `planCard` blocks with a `@forelse($risks ?? [] as $risk)` loop that:
- Iterates the `$risks` collection already passed by `ClientController::client_details()` at line 83
- Resolves status code `1/2/3` → label `historic/live/no risk` via an inline `$statusMap`
- Renders `{{ $risk->description }}` from the joined `risk` table
- Formats `created_at` and (optional) `review_date` via `date('M j, Y', …)`
- **Adds the `<button class="realRiskBodyMapBtn" data-su-risk-id="{{ $risk->id }}">`** alongside the existing eye/trash icons — this is the button that opens the body map in editable mode
- Falls back to an `@empty` "No risk assessments recorded for this client yet." card when `$risks` is empty

**Result:** Navigating to any client's Risk Assessments tab now shows their real risk records from `su_risk`, and the body-silhouette icon on each card opens the body map modal scoped to that specific risk (so injuries can be added).

**Wired by:** the pre-existing handler at `client_details.blade.php:8851`:
```javascript
$(document).on('click', '.realRiskBodyMapBtn', function() {
    var riskId = $(this).data('su-risk-id');
    $('input[name=bm_aggregated_su_id]').val('');
    $('input[name=su_rsk_id]').val(riskId);
    $('#bodyMapModal').modal('show');
});
```

---

### 1.2 Body map modal — default gender fallback

**File:** `resources/views/frontEnd/serviceUserManagement/elements/risk_change/body_map_popup.blade.php:72`

**Problem:** The body map modal contains both male and female SVG figures in the same DOM. A CSS rule hides one based on the class `gender-M` or `gender-F` on `#organswrapper`. The class was only added when `$patient->gender` was `'M'` or `'F'` — for any service user with an empty/unset gender (e.g. Alex Sheffield, id 180), **both figures rendered side-by-side**, making the modal look broken.

**Fix:** Changed the Blade expression so `$bmGender` defaults to `'M'` when the DB value is empty, and always emits `gender-{{ $bmGender }}` on the wrapper:

```php
@php $bmGender = (isset($patient) && in_array($patient->gender ?? null, ['M','F'], true)) ? $patient->gender : 'M'; @endphp
<div id="organswrapper" class="gender-{{ $bmGender }}">
```

**Result:** Every service user renders exactly one figure, even when gender is missing in the DB. Male is the safe default until the Add/Edit Client form is fixed to enforce gender selection (tracked separately under the Add Client workstream).

**Follow-up:** The proper long-term fix is the Add Client form enforcing `gender ∈ {M,F}` as a required field — not tracked in Feature 10 because it lives in `service_user_form.blade.php` / `ServiceUserController`, not the Care Roster views.

---

## 2. Buttons already wired before this session (no action needed)

The audit confirmed these are working. Listed here so future work doesn't duplicate effort.

### Risk Assessments tab
- `.realRiskBodyMapBtn` — opens body map in editable mode (handler `client_details.blade.php:8851`)
- `.riskAssessmentDeatils` — opens risk detail pane (handler line ~8603)
- `#riskAssesmentBackBtn` — back from detail pane (handler line ~8607)

### Alerts tab
- `.addalertClientDetailsBtn` — toggle add-alert form (handler line ~7723)
- `.saveClientAlert` — form submit (handler in `client_alert.js`)

### AI Insights tab
- `.aiInsightsBtn` (3 variants) — switch between Proactive / Handover / Care Plan Review (handler line ~8570)

### Care Plan tab
- `.viewPlanBtn` — open plan detail view (handler line ~8595)
- `#planBackBtn` — back from plan detail (handler line ~8599)

### Medication tab
- `#marSheetBtn` / `#medicationLogsBtn` — inner tab switcher (handler line ~7700)
- `.marSheetDetails` — open MAR detail view (handler line ~8615)
- `#logMedicationBtn` — toggle medication log form (handler line ~8611)
- `#medicationBackBtn` — back from detail view (handler line ~8619)

### PEEP tab
- `.peepDetailsBtn` — open PEEP detail view (handler line ~8623)
- `#peepBackBtn` — back from detail view (handler line ~8627)

### Behavior Chart tab
- `.behaviorChartDetailsBtn` — open chart detail view (handler line ~8631)
- `#behaviorBackBtn` — back from detail view (handler line ~8635)

### Mental Capacity tab
- `.mentalCapAsessmentDetailsBtn` — open assessment detail view (handler line ~8639)
- `#mentalCapAsessmentBackBtn` — back from detail view (handler line ~8643)

### DoLS tab
- `.addDolsRecordBtn` — toggle add form (handler line ~8647)
- `#closeDolsformBtn` — close form (handler line ~8673)
- `#saveClientDols` — form submit to server (handler in `client_dols.js`)

### DNACPR tab
- `.addDnaCprBtn` — toggle add form (handler line ~8677)
- `.closeDnaCprBtn` — close form (handler line ~8681)

### Consent / Onboarding (consent section)
- `.addConsentBtn` — toggle add form (handler line ~8685)
- `.closeConsentRecordBtn` — close form (handler line ~8689)
- Consent form submit — wired to server endpoint

### Emergency Contacts tab
- `.editBtn` — edit contact inline (handler line ~7745)
- `#addContactBtn` — add new contact (handler line ~7761)
- `.deleteIcon` — delete contact (handler line ~7771)
- `.cancelBtn` — cancel edit (`showEmergency()` at line ~7750)

### Body Map (header button, client page)
- `.openBodyMapProfile` — opens modal in aggregated read-only mode (handler line ~8841)

### Body Map modal (popup)
- Click on `path[id*="frt"]`/`path[id*="bck"]` — add/view injury (handler `body_map_popup.blade.php:992`)
- `#popupSaveInjuryBtn` — save new injury (handler line ~1098)
- `#popupRemoveInjuryBtn` — remove injury (handler line ~1142)

---

## 3. Buttons covered by other Phase 1 features (NOT in Feature 10 scope)

These are currently unwired, but building the corresponding Phase 1 feature will fix them. Do not duplicate work here.

| Tab | Unwired buttons | Gets fixed by |
|---|---|---|
| Medication | "Add MAR Sheet", `.danger` trash on MAR cards, Medication Log form save, delete on log cards | **Phase 1 Feature 6 — MAR Sheets (8h)** |
| Safeguarding | "Add Referral" | **Phase 1 Feature 9 — Safeguarding (6h)** |
| AI Insights | Copy / Export / New Analysis | **Phase 3 — AI features** |
| Progress Report | "AI Generate" | **Phase 3 — AI features** |
| Documents | "Generate Care Plan" | **Phase 3 — AI features** |

---

## 4. Feature 10 scope — orphaned buttons (what this feature actually builds)

Every button listed below belongs to a tab that **no other phase plan will touch**. If Feature 10 doesn't fix them, they stay broken indefinitely.

### 4.1 Care Tasks tab (lines 1413–1626)

**Status:** Data loads dynamically via `getCareTask()` but card action buttons are stubs.

- [ ] **"AI Generate from Care Needs"** — defer to Phase 3 or disable with tooltip. Not a Feature 10 target.
- [ ] **Edit** button on each task card — wire to existing task edit endpoint (controller: `StaffController` or similar, verify)
- [ ] **Delete** button on each task card — wire to DELETE endpoint with confirm modal + CSRF + rate limit

**Approach:** Handlers go inline at the bottom of the file in the existing script block (match pattern used by `.realRiskBodyMapBtn`). Use `data-task-id` on the buttons and delegate via `$(document).on('click', ...)`.

### 4.2 Care Plan tab (lines 2596–3244)

**Status:** Entirely static mockup cards. No $ variable; hardcoded plan data.

- [ ] **Convert static cards to `@forelse($carePlans)` loop** — requires adding `$carePlans` query in `ClientController::client_details()` joining whatever care plan table exists (investigate: `care_plans`? `client_care_plans`?)
- [ ] **`.danger` trash** on each card — wire to DELETE endpoint
- [ ] **"Standard View" / "CQC Print Format"** toggle — view-switch handler on the detail pane
- [ ] **"Print"** — `window.print()` with a print-only CSS scope
- [ ] **"Export PDF"** — wire to a PDF generation endpoint (investigate existing PDF services in the app before building new)
- [ ] **"Edit Plan"** — wire to edit form / modal

**Approach:** This is the largest orphan. Investigate whether care plan CRUD exists server-side before building client-side. If backend is missing, scope drops to "display + print" and full CRUD is deferred.

### 4.3 Risk Assessments tab (residual, after 1.1)

**Status:** Cards are now dynamic (fixed in session), but these remain unwired:

- [ ] **`.addAssessmentBtn` "Add Assessment"** — wire to open a new risk assessment form/modal. Verify there's an existing `RiskController::store` endpoint; if yes, wire this button to it. If no, this becomes a new small CRUD task.
- [ ] **`.riskAssessmentDeatils` eye icon** — note: audit says wired at line ~8603, but the handler only toggles a hardcoded detail pane (the one at line 3384 full of static "General Risk Assessment" text). Needs rewiring to load real assessment data from `$risks[].dynamic_form_id` → `dynamic_forms` table.
- [ ] **`.danger` trash on risk cards** — wire to DELETE endpoint with confirm

**Approach:** Check `RiskController` for existing store/update/delete methods. The `dynamic_form_id` on `su_risk` suggests the real form lives in a dynamic_forms table — the static detail pane needs to be replaced with a data-bound render of that form.

### 4.4 PEEP tab (lines 3781–3959)

**Status:** Detail view/back wired. Cards and Add/Delete are static mockups.

- [ ] **Convert static PEEP cards to `@forelse($peeps)` loop** — add `$peeps` to controller, join to whatever PEEP backend exists (if any)
- [ ] **`.addAssessmentBtn` "Add PEEP"** — wire to create form
- [ ] **`.danger` trash** — wire to DELETE
- [ ] **Investigate backend first** — if no PEEP table exists, this drops to "empty state + add button disabled with 'coming soon' tooltip"

### 4.5 Behavior Chart tab (lines 3980–4092)

**Status:** Same shape as PEEP.

- [ ] **Investigate backend** — does a behavior_charts table/controller exist?
- [ ] **Convert static cards to loop** — if backend exists
- [ ] **"Add Chart"** button — wire to create form
- [ ] **`.danger` trash** — wire to DELETE

### 4.6 Mental Capacity tab (lines 4094–4196)

**Status:** Same shape as PEEP and Behavior.

- [ ] **Investigate backend** — does a mental_capacity_assessments table/controller exist?
- [ ] **Convert static cards to loop** — if backend exists
- [ ] **"Add Assessment"** button — wire
- [ ] **`.danger` trash** — wire to DELETE

### 4.7 Onboarding tab (lines 323–1412)

**Status:** Most "progress card" mockups are decorative-looking but wired to hardcoded text; consent sub-section is fully wired.

- [ ] **Audit each progress card** — identify which cards represent real data (e.g. "profile completion", "alerts", "training") and which are pure mockup
- [ ] **Wire progress cards to real indicators** — e.g. "alerts" card should show `$alerts_count`, "training" should show completed/total
- [ ] **Decide on scope** — if onboarding has no backend concept beyond what's already rendered elsewhere, this tab becomes read-only display of aggregated data

### 4.8 Progress Report tab (lines 4947+)

**Status:** Most buttons are stubs. "New Record" modal trigger works.

- [ ] **"Export"** button — wire to CSV or PDF export endpoint
- [ ] **"AI Generate"** — defer to Phase 3
- [ ] **Chart/metrics data** — currently hardcoded; wire to aggregated data from existing tables (care tasks completed, incidents reported, medication given, etc.)

### 4.9 Documents tab (lines 4746–4946)

**Status:** Upload form show/hide works. Save flow needs verification.

- [ ] **Verify `#uploadDocumentForm` submit** — currently uses inline JS (`toggleDocForm()`, `closeDocForm()`); confirm the actual POST to the server works, or wire it
- [ ] **"Generate Care Plan"** — defer to Phase 3 (AI)

### 4.10 Global patterns across all tabs

- [ ] **Every `.danger` trash button** — currently unwired project-wide in this file. Create a reusable `.cardDelete` handler pattern with `data-entity-type` and `data-id`, routing to the correct DELETE endpoint per type.
- [ ] **Every "Add X" button without a handler** — list: `.addAssessmentBtn` (risk, PEEP, mental cap, behavior), "Add Chart", "Add Referral", etc. Decide per-tab whether to wire to existing form or scaffold new.
- [ ] **Confirm modal** — add a shared confirmation dialog for all destructive actions before wiring any delete button.

---

## 5. Implementation approach

### Order of operations

1. **Backend investigation first** (1h) — grep for controllers / tables behind each orphan tab. Identify which tabs have backend support vs. which are UI-only mockups. **Do not build new backends in Feature 10.**
2. **Shared utilities** (1h) — confirm modal component, `.cardDelete` handler pattern, CSRF setup already present (it is: `$.ajaxSetup`).
3. **Dynamic data conversion** (3h) — for each orphan tab with a working backend, replace static cards with `@forelse` loops. Follow the Risk Assessments pattern from section 1.1.
4. **Button wiring** (3h) — add click handlers for each Add / Edit / Delete button, routing to existing controller endpoints. All handlers go inline at the bottom of `client_details.blade.php`, matching the existing style.
5. **Disable / tooltip unsupported buttons** (1h) — for buttons whose backend doesn't exist (investigation step reveals this), add `disabled` + `title="Coming in Phase X"` rather than leaving them silently broken.
6. **Manual E2E test each tab** (1h) — click every button, confirm it does something (opens form, saves, deletes, or shows tooltip).

### Code conventions (match existing)

- **Handlers:** `$(document).on('click', '.className', function() { ... })` at the bottom of the file's inline `<script>` block (not the top — delegation survives re-renders, and new code goes near `client_details.blade.php:~8850` where the body map handlers live).
- **AJAX:** jQuery `$.ajax`, `X-CSRF-TOKEN` header already set globally.
- **Routes:** add to `routes/web.php` under the existing `/roster/client/*` or `/client/*` prefix, with `->middleware('auth')->middleware('throttle:30,1')` for writes and `throttle:20,1` for deletes (per CLAUDE.md rule 6).
- **Validation:** `$request->validate([...])` in controller methods. Controllers are thin — real logic goes in `app/Services/`.
- **Multi-tenancy:** Every new query filters by `home_id` with `where('home_id', $home_id)`. IDOR prevention required on every endpoint (CLAUDE.md rule 10).
- **Blade escaping:** `{{ }}` only, never `{!! !!}` for dynamic content.
- **JS escaping:** use `esc()` helper before `.html()` on any API-returned strings.

### Security checklist (enforced by `/workflow`)

Every new endpoint in Feature 10 must pass:
- [ ] Input validation (`$request->validate()`)
- [ ] Eloquent ORM only (no `DB::raw` with user input)
- [ ] CSRF token on form / AJAX header
- [ ] Rate limit middleware (`throttle:30,1` writes, `throttle:20,1` deletes)
- [ ] `$fillable` whitelist on any affected model
- [ ] Route constraint `->where('id', '[0-9]+')`
- [ ] Server-side role check (not just UI hiding)
- [ ] `home_id` match check on every record access
- [ ] XSS prevention server-side (`{{ }}`) and client-side (`esc()`)

---

## 6. Out of scope (explicit exclusions)

- **Medication tab buttons** — Feature 6 owns these
- **Safeguarding referral** — Feature 9 owns this
- **AI Insights / Progress Report AI Generate / Documents "Generate Care Plan"** — Phase 3 (AI) owns these
- **Add/Edit Client form itself** — tracked under the separate Add Client workstream; Feature 10 is client-details display/actions, not the create/edit form for service users themselves
- **New backend tables or controllers** — Feature 10 wires existing backends only. Anything that needs a new model/migration gets broken out into its own mini-feature.

---

## 7. Definition of done

- [ ] Every button in Feature 10 scope (section 4) is either wired to a real endpoint or explicitly disabled with a "coming soon" tooltip
- [ ] No silent click → do-nothing behavior anywhere in `client_details.blade.php`
- [ ] Manual E2E walkthrough: open Alex Sheffield and Katie's client details pages, click through every tab, confirm no dead buttons
- [ ] All new endpoints pass the security checklist in section 5
- [ ] `docs/logs.md` entry for each button wired, with teaching notes
- [ ] Session saved to `sessions/sessionN.md`

---

## References

- Audit run: Apr 16, 2026 — results in conversation history
- Fixed files this session: `client_details.blade.php`, `body_map_popup.blade.php`
- Related phase 1 features: Feature 3 (Body Maps, done), Feature 6 (MAR Sheets, pending), Feature 9 (Safeguarding, pending)
- CLAUDE.md section "Security Rules" — mandatory for every button wired
