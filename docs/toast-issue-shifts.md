# Toast Issue: "Failed to load shifts"

**Page:** `/roster/schedule-shift` (Shift Schedule)
**Symptom:** Browser alert (toast) saying "Failed to load shifts" on every page load
**Status:** FIXED
**Date:** 2026-04-09

---

## Step-by-Step Investigation

### Step 1 — Searched for the error message in the codebase

Grepped for `"Failed to load shifts"` across the project. Found it in two places:

- `public/frontEnd/staff/js/schedule-shift.js` line 428 — FullCalendar's `events.failure` callback fires `alert('Failed to load shifts')`
- `resources/views/frontEnd/roster/schedule/schedule_shift.blade.php` line 3301 — day view AJAX error handler

### Step 2 — Checked if the routes exist

The JS calls `GET /roster/carer/shifts` and `GET /roster/carer/shift-resources`. Phase 0 audit said these routes were missing — but checking `routes/web.php` lines 173-174, they DO exist:

```
Route::get('/carer/shift-resources', [CarerController::class, 'shift_resources']);
Route::get('/carer/shifts', [CarerController::class, 'allShifts']);
```

### Step 3 — Checked if the controller methods exist

Found both methods in `app/Http/Controllers/frontEnd/Roster/Staff/CarerController.php`:

- `shift_resources()` at line 210
- `allShifts()` at line 252

So routes and controllers are fine. The issue must be a runtime error.

### Step 4 — Read the controller code

`allShifts()` does this:

```php
$shifts = ScheduledShift::with(['staff', 'documents', 'assessments', 'recurrence'])
    ->where('home_id', $homeId)->get();
```

Then maps each shift to a JSON array, accessing `$shift->staff->name`.

### Step 5 — Tested the query in tinker

Ran the same query in `php artisan tinker`. Got 13 shifts for home_id=1, but also got **6 deprecation warnings**:

```
DEPRECATED: Using null as an array offset is deprecated, use an empty string instead
in vendor/laravel/framework/src/Illuminate/Database/Eloquent/Relations/BelongsTo.php line 187
```

### Step 6 — Found the root cause

The `scheduled_shifts` table has many rows where `staff_id = NULL` (unassigned shifts). When Laravel eager-loads the `staff` BelongsTo relationship, it tries to use `null` as an array key internally. PHP 8.5 throws a deprecation warning for this.

Even though `public/index.php` has `error_reporting(E_ALL & ~E_DEPRECATED)`, the warnings were still leaking into the HTTP response body — getting output BEFORE the JSON.

So the browser receives:

```
<warning>DEPRECATED</warning> Using null as...
<warning>DEPRECATED</warning> Using null as...
[{"id":"1","title":"Morning",...}]
```

FullCalendar tries to parse this as JSON, fails, and fires the `failure` callback → `alert('Failed to load shifts')`.

### Step 7 — Why the shifts still appeared on screen

There are TWO shift-loading mechanisms on the page:

1. **FullCalendar** (schedule-shift.js) — loads `/roster/carer/shifts` for the calendar timeline. This one FAILED (caused the toast).
2. **Day view** (inline AJAX in the blade file) — loads `/roster/carer/shifts/day`. This one may have succeeded if there were no null staff_ids on that particular day.

The 3 shifts visible in the screenshot were from the day view, not from FullCalendar.

### Step 8 — Applied the fix

Changed `allShifts()`, `dayShifts()`, and `weekShifts()` in `CarerController.php` to NOT eager-load nullable BelongsTo relationships (`staff`, `client`).

Instead, load staff and client names separately using a manual lookup:

```php
// Before (broken on PHP 8.5):
$shifts = ScheduledShift::with(['staff', 'documents', 'assessments', 'recurrence'])->get();
// ... later ...
'staff_name' => $shift->staff ? $shift->staff->name : null,

// After (fixed):
$shifts = ScheduledShift::with(['documents', 'assessments', 'recurrence'])->get();
$staffIds = $shifts->pluck('staff_id')->filter()->unique()->values()->toArray();
$staffMap = !empty($staffIds) ? User::whereIn('id', $staffIds)->pluck('name', 'id') : collect();
// ... later ...
'staff_name' => $shift->staff_id ? ($staffMap[$shift->staff_id] ?? null) : null,
```

Same pattern applied to `client` relationship in `dayShifts()` and `weekShifts()`.

### Step 9 — Methods fixed

- `allShifts()` — removed `staff` from eager-load, added manual `$staffMap`
- `dayShifts()` — removed `staff` and `client` from eager-load, added manual `$staffMap` and `$clientMap`
- `weekShifts()` — removed `staff` and `client` from eager-load, added manual `$staffMap` and `$clientMap`
- `ninetyDaysShifts()` — no fix needed (doesn't eager-load staff/client)

---

## File Modified

- `app/Http/Controllers/frontEnd/Roster/Staff/CarerController.php`

## Root Cause

PHP 8.5 deprecated using `null` as an array offset. Laravel's `BelongsTo` relation internally uses the foreign key as an array index. When `staff_id` or `service_user_id` is `null`, this triggers a deprecation warning that gets output before the JSON response, corrupting it.

## Teaching Notes

- **Output pollution:** Any text PHP outputs before your `return response()->json(...)` corrupts the JSON. Warnings, notices, `echo` statements, even a stray newline before `<?php` — all break JSON responses.
- **BelongsTo with nullable foreign keys:** On PHP 8.5, eager-loading a BelongsTo when the FK is null triggers a deprecation. Workaround: load the related data manually with `whereIn()` + `pluck()`, or don't eager-load nullable relationships.
- **Two data sources on one page:** When a page has multiple AJAX calls loading similar data, one can fail while the other succeeds — making it look like "it works but shows an error." Always check which specific request is failing using the browser's Network tab.
