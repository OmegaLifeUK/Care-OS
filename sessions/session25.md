# Session 25 — Phase 2 Feature 7 Prompt: Workflow Automation Engine

**Date:** 2026-04-27
**Branch:** `komal`
**Working Directory:** `/Users/vedangvaidya/Desktop/Omega Life/Care OS`
**Goal:** Write the pre-built prompt for Phase 2 Feature 7 — Workflow Automation Engine (trigger → action)

---

## Conversation

### User Request

User asked to create a prompt for the next Phase 2 feature — Workflow Automation Engine (trigger → action) — in the exact same format as previous Phase 2 prompts, including workflow commands and necessary reference documents.

### Research & Context Gathering

Claude gathered extensive context before writing the prompt:

1. **Listed existing Phase 2 prompts** — Found 6 existing prompts (feature1 through feature6) in `phases/` directory.

2. **Read Feature 6 prompt as format reference** — `phases/phase2-feature6-scheduled-reports-prompt.md` (654 lines). Noted the exact structure: workflow checklist, context files, feature classification, what-exists table, UI mockup, database schema, step-by-step implementation, security checklist, design decisions, test verification.

3. **Read Phase 2 feature list** — From `.claude/commands/careos-workflow-phase2.md`:
   - Feature 7: Workflow automation engine (trigger → action) — 10h — Workflows category
   - Feature 8: Pre-built workflows (incident → notify manager) — 4h — depends on Feature 7

4. **Read CareRoster reference files:**
   - `src/components/workflow/AutomatedWorkflowEngine.jsx` (263 lines) — 5 utility functions: syncClientFromCarePlan, generateShiftsFromCarePlan, syncCarerStatusFromLeave, geocodeAndUpdateAddress, assignMandatoryTraining. All client-side, no backend execution.
   - `src/components/workflows/AutomatedWorkflows.jsx` (349 lines) — UI with 8 hardcoded WORKFLOW_TEMPLATES (shift_reminder, unfilled_shift_alert, leave_approval_reminder, training_expiry_warning, incident_follow_up, medication_missed_alert, client_birthday_reminder, daily_summary_email). Toggle via localStorage, fake stats ("Executed Today: 47"), "Configure" expand with trigger/recipients/template fields. No actual backend.
   - `src/pages/WorkflowsPage.jsx` (22 lines) — Thin wrapper around AutomatedWorkflows.
   - `src/components/compliance/AutoWorkflowTrigger.jsx` — Audit-triggered workflow for creating action plans.
   - `src/components/formbuilder/WorkflowActionEditor.jsx` — Action types: create_task, send_notification, send_email, route_form.

5. **Checked CareRoster exports** — `OnboardingWorkflow.md` (3 records of onboarding step tracking, all failed — not relevant for our engine).

6. **Checked existing Care OS infrastructure:**
   - `app/Models/Workflow_notification.php` — References `workflow_notifications` table which DOES NOT EXIST. Legacy/unused.
   - `notification` table — 7,480 rows, columns: id, home_id(varchar), user_id, service_user_id, event_id, notification_event_type_id, event_action, message, is_sticky, status, created_at, updated_at.
   - `notification_event_type` table — 24 types (id 1-24), e.g., id=10 "Incident Report", id=24 "SOS_ALERT".
   - `NotificationService` — list(), markRead(), markAllRead(), unreadCount(). Uses FIND_IN_SET for home_id.
   - `Console/Kernel.php` — Already has `reports:dispatch` hourly.
   - `Console/Commands/` — Has DispatchScheduledReports.php.
   - `Mail/` — Has ScheduledReportMail.php.
   - `roster_header.blade.php` — No workflow/automation link. Dead `#!` links for Reports, Compliance Hub, Task Center, etc. "Reporting Engine" wired at line 535.
   - No existing workflow views, routes, or controllers in Care OS.

7. **Checked data quirks from logs.md:**
   - `staff_training` has NO `home_id` — must JOIN `training` table
   - `scheduled_shifts.home_id` is VARCHAR not INT
   - `scheduled_shifts` uses `deleted_at` not `is_deleted`
   - `mar_administrations` links to `mar_sheets` via `mar_sheet_id`

### Prompt Written

Created `phases/phase2-feature7-workflow-engine-prompt.md` — comprehensive prompt (~650 lines) following the exact Feature 6 format:

**Feature Classification:** BUILD FOR REAL — CareRoster's workflow system is entirely client-side (localStorage toggles, hardcoded templates, fake stats). We build a real backend engine.

**Architecture designed:**
- **Two new tables:** `automated_workflows` (workflow definitions with JSON trigger/action configs) + `workflow_execution_logs` (execution history with denormalized fields)
- **Three trigger types:** `scheduled` (time-based, identical to Feature 6), `condition` (data threshold checks like "incidents > 5 in last 7 days"), `event` (state checks like "unfilled shifts >= 3")
- **Two action types:** `send_notification` (INSERT into existing `notification` table with new event_type_id=25) + `send_email` (via Laravel Mail)
- **Artisan command:** `workflows:evaluate` runs every 15 minutes via Kernel.php scheduler
- **Cooldown mechanism:** Prevents condition/event triggers from re-firing within configurable window (default 24h)
- **5 queryable entities:** incidents, training, shifts, medication, feedback — mapped to real Care OS tables with documented quirks

**Safety guards designed:**
- Max 20 workflows per home
- Max 50 executions per hour per home
- Max 5 email recipients per workflow
- Error isolation (one failing workflow doesn't break others)
- Cooldown per workflow for condition/event triggers

**Files to create (10):**
1. `app/Models/AutomatedWorkflow.php`
2. `app/Models/WorkflowExecutionLog.php`
3. `app/Services/WorkflowEngineService.php`
4. `app/Http/Controllers/frontEnd/Roster/WorkflowController.php`
5. `app/Console/Commands/EvaluateWorkflows.php`
6. `app/Mail/WorkflowNotificationMail.php`
7. `resources/views/frontEnd/roster/workflow/index.blade.php`
8. `resources/views/emails/workflow_notification.blade.php`
9. `public/js/roster/workflows.js`
10. `tests/Feature/WorkflowEngineTest.php`

**Files to modify (4):**
1. `routes/web.php` — 7 new routes
2. `app/Http/Middleware/checkUserAuth.php` — whitelist 7 endpoints
3. `app/Console/Kernel.php` — register `workflows:evaluate` every 15 minutes
4. `resources/views/frontEnd/roster/common/roster_header.blade.php` — add sidebar link

**18 tests planned** covering CRUD, home isolation, IDOR, trigger evaluation, cooldown, action execution, limits, and auth.

---

## Session Status at End

**What was done:**
- Created `phases/phase2-feature7-workflow-engine-prompt.md` — comprehensive pre-built prompt for the Workflow Automation Engine feature

**What's next:**
- Paste the prompt into a new session and run `/careos-workflow-phase2` to build Feature 7
- After Feature 7, Feature 8 (Pre-built Workflows) seeds 8 default workflow templates using the engine
- After Feature 8, Phase 2 is complete (8/8 features)

**No code changes made** — this was a prompt-writing session only.
