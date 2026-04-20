---
name: Reusable project setup prompts
description: Template prompts for setting up logs.md and /workflow command on any new Omega project — copy and customize per project
type: reference
originSessionId: 995b31df-2b05-47f1-9062-09b0e4af618b
---

## Prompt 1: Setting Up logs.md

Copy this into your first Claude Code session on a new project. Replace the `[bracketed]` values.

---

**PROMPT:**

```
Create a `docs/logs.md` file for this project. This will be the persistent memory across all our sessions — every action you take gets logged here with teaching notes so I can learn.

Rules for logs.md:
1. ALWAYS read `docs/logs.md` at the start of every session to pick up context
2. Every action you take gets a numbered log entry with:
   - What you did
   - Why you did it
   - Commands run and their output (summarized)
   - Teaching notes explaining the concepts for someone learning [FRAMEWORK]
3. Teaching notes should explain WHY, not just WHAT — e.g. don't just say "added CSRF token", explain what CSRF is and why it matters
4. Group logs by session date
5. At the end of each session, write a summary log entry

Use this format:

---
# [PROJECT NAME] — Session Logs

> **Purpose:** This file logs every action taken by Claude Code across sessions. Each entry includes what was done, why, and teaching notes. New sessions should read this file first to pick up where we left off.

---

## Session: [DATE]

### Log 1 — [Title]
**Action:** [What was done]

**Details:**
[Commands, findings, changes]

**Teaching notes:**
- [Concept explanation for learning]
---

Start by logging the initial environment check (language version, framework version, database, dependencies).
```

---

## Prompt 2: Setting Up /workflow Command

Copy this to create `.claude/commands/workflow.md`. Replace `[bracketed]` values. Remove sections that don't apply to your stack.

---

**PROMPT:**

```
Create a custom slash command at `.claude/commands/workflow.md` that runs a full development pipeline for building features. This is the /workflow command.

The pipeline has 8 stages with gates between them. Here's the template — customize it for our project:

PROJECT: [PROJECT NAME]
FRAMEWORK: [e.g. Laravel, Next.js, Django, Rails]
LANGUAGE: [e.g. PHP, TypeScript, Python, Ruby]
MULTI-TENANCY FIELD: [e.g. home_id, org_id, tenant_id — or "none"]
MAIN BRANCH: [e.g. main, master, develop]
PUSH STRATEGY: [e.g. "push branch:main to remote", "create PR"]
REFERENCE APP: [path to any reference/legacy app to check, or "none"]
HARDCODED STRINGS TO GREP: [e.g. old domain names, localhost URLs]
TEST COMMAND: [e.g. "php artisan test", "npm test", "pytest"]
LOG FILE: [e.g. "storage/logs/laravel.log", "logs/app.log"]

Pipeline stages:

1. PLAN — Read docs/logs.md, explore codebase, check reference app, check database, write plan doc to phases/, STOP for user approval
2. SCAFFOLD — Check what exists, generate only what's missing (models, controllers, views, routes, migrations), brief user
3. BUILD — Implement per plan. Key rules: [MULTI-TENANCY], CSRF on forms, auth on routes, escape user output, validate all input, use service layer for business logic. Log to docs/logs.md with teaching notes.
4. TEST — Write [TEST FRAMEWORK] tests: happy path, auth, authorization, multi-tenancy, validation. Run and fix until all pass.
5. DEBUG — Clear log file, hit routes via curl/test runner, check for errors. Scan for N+1 queries, dead code, empty methods, unused imports. Gate: zero new errors.
6. REVIEW — git diff from workflow start. Check: missing [MULTI-TENANCY], XSS, SQL injection, missing CSRF, N+1s. Fix all BLOCKER/HIGH.
7. AUDIT — Grep for hardcoded URLs, check for backup/duplicate files, verify routes load without errors.
8. PUSH — git add specific files (never -A), commit with descriptive message, push, update docs/logs.md with final summary.

Each stage has a gate — cannot proceed if it fails. Include a gate table.

Include skip rules:
- Tiny fix (1-2 lines): BUILD → REVIEW → PUSH
- New page, no logic: PLAN → SCAFFOLD → BUILD → REVIEW → PUSH
- Bug fix: PLAN (brief) → BUILD → TEST → REVIEW → PUSH

Include session tracking template that shows progress through stages.

The command should be self-contained — when invoked with /workflow, it asks what feature to build, then runs the full pipeline.
```

---

## How to Use These

1. Start a new project session in Claude Code
2. Paste Prompt 1 to set up logs.md
3. Paste Prompt 2 (with your values filled in) to set up /workflow
4. From then on, every feature: paste the phase requirements → run `/workflow`
5. At session end, run `/save-session` to save the full conversation

## Stack-Specific Customizations

**Laravel:** multi-tenancy via home_id/org_id, `{{ }}` for XSS, `@csrf` on forms, `$request->validate()`, PHPUnit, `storage/logs/laravel.log`

**Next.js:** middleware for auth, server components vs client, `next/headers` for cookies, Vitest/Jest, `.next/` build output

**Django:** multi-tenancy via tenant FK, `{% csrf_token %}`, `|escape` filter, `forms.py` validation, pytest-django, `debug.log`

**Rails:** multi-tenancy via `belongs_to :org`, `authenticity_token`, `sanitize()`, strong params, RSpec, `log/development.log`

---

## Prompt 3: Start Next Feature (Care OS — Phase 1)

Copy-paste this at the start of a new session to pick up Feature 4: Handover Notes.
After Feature 4 is done, update the feature number/name for the next one.

---

**PROMPT:**

```
You are continuing development on Care OS, a Laravel-based care home management
system for Omega Life UK. You're starting Feature 4: Handover Notes (4h est).

Features 1-3 (Incident Management, Staff Training, Body Maps) are DONE.

Step 1: Read these files (in this order)

1. `CLAUDE.md` — project conventions, tech stack, security rules, multi-tenancy
   patterns, git conventions, dev process. Re-read "Security Rules" (10 items)
   and "Key Codebase Patterns" sections carefully.

2. `docs/logs.md` — read the MOST RECENT 10-15 entries (bottom of file) to
   understand what was just done and why.

3. `sessions/session12.md` — last session history. Covers: Risk Assessments tab
   wire-up, body map gender fallback, checkUserAuth middleware digit-stripping
   bug fix, full audit of client_details.blade.php (~95 buttons, ~60 unwired),
   Feature 10 documentation.

4. `phases/phase1.md` — Phase 1 pipeline table + detailed spec for Feature 4
   (Handover Notes) starting at the "Feature 3: Handover Notes" section. Read
   the "What Exists" and "What's Missing" subsections — they tell you exactly
   what files exist and what to build.

5. `docs/feature10-careroster-wireup.md` — audit of every button in
   client_details.blade.php. Reference this if Feature 4 touches the Care
   Roster client details page.

6. `docs/security-checklist.md` — 15-item security gate enforced by /workflow.

Step 2: Check current state

Run these commands:
- `git status` — any uncommitted changes?
- `git log --oneline -5` — last commit?
- `php artisan serve` — start dev server if not running (http://127.0.0.1:8000)

Step 3: Run /careos-workflow

Execute the full pipeline for Feature 4: Handover Notes:
PLAN → SCAFFOLD → BUILD → TEST → DEBUG → REVIEW → AUDIT → PROD-READY → PUSH

Feature 4 spec from phase1.md:
- DB table: `handover_log_book`
- Controller: `app/Http/Controllers/frontEnd/HandoverController.php` (index + edit)
- Views: `handover_logbook.blade.php`, `handover_to_staff.blade.php`
- Routes: POST/GET /handover/daily/log, /handover/daily/log/edit, /handover/service/log
- Missing: Model, Service layer, verify views render, staff-to-staff handover flow

Critical reminders

- checkUserAuth.php line 125**: strips ALL digits from URLs before permission
  checking. Any new AJAX GET with a number in the URL will silently fail with
  "unauthorize" unless the digit-stripped form is whitelisted in $allowed_path.
  This burned us in session 12 — always check when adding routes.

- client_details.blade.php**: ~9000 lines, mostly static mockups. If Handover
  Notes has a tab here, wire real buttons while building. Cross-reference
  docs/feature10-careroster-wireup.md.

- Multi-tenancy**: every DB query MUST filter by home_id. Admin users have
  comma-separated home_id (e.g. "8,104,18,12"). Use explode(',', $homeIds)[0].

- is_deleted flag**: use this, NOT Laravel SoftDeletes trait.

- user_type column**: it's `user_type`, NOT `type`. Admin = 'A'.

- home_id on service_user**: verify the client belongs to the user's home
  before any data access (IDOR prevention).

Logging

- Log EVERY action to `docs/logs.md` with teaching notes
- If conversation gets long, proactively save to sessions/session13.md BEFORE
  autocompact loses context
- At session end, run /save-session
- Update pipeline status in phases/phase1.md when Feature 4 is complete

Before writing code, output a status report (under 150 words):
- What exists for Feature 4 already
- What needs to be built
- Any open questions for me
```

---

**After Feature 4 is done**, update this prompt for the next feature by changing:

- Feature number/name (4 → 5, Handover Notes → DoLS)
- "Features 1-3 are DONE" → "Features 1-4 are DONE"
- The spec section with the next feature's details from phase1.md
- Session number references (session12 → session13, session13 → session14)
- The remaining build order
