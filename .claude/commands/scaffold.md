Generate production-ready Laravel boilerplate for a Care OS feature. Pass a feature type and name.

**You generate starting points, not final code. Always review and customize the output.**

## Usage

```
/scaffold crud [resource]        → Full CRUD (model, controller, views, routes)
/scaffold page [name]            → New page (controller, view, route, sidebar link)
/scaffold report [name]          → Report page with filters, DataTable, PDF export
/scaffold modal [name]           → Add/Edit modal with AJAX form submission
/scaffold api [resource]         → JSON API endpoints for a resource
/scaffold migration [table]      → Migration + Model for a new table
/scaffold notification [name]    → Email + in-app notification
/scaffold upload [context]       → File upload with validation (images, documents)
/scaffold dashboard-widget [name] → Dashboard card/widget component
```

## Step 1: Check What Exists

Before generating anything:
1. Check if the table already exists in the database: `SHOW TABLES LIKE '%[resource]%'`
2. Check if a model already exists: search `app/Models/` and `app/`
3. Check if routes already exist: search `routes/web.php`
4. Check if views already exist: search `resources/views/`
5. Check existing similar features to match patterns

**If the table/model already exists, use it — don't create duplicates.**

## Step 2: Generate Files

### `/scaffold crud [resource]` generates:

**Model** (if table exists but model doesn't):
```
app/Models/[Resource].php
- $table, $fillable, $guarded
- Relationships (belongsTo, hasMany)
- Soft deletes if table has deleted_at
- home_id scope for multi-tenancy
```

**Controller**:
```
app/Http/Controllers/frontEnd/[Section]/[Resource]Controller.php
- index()     → list view with DataTable
- create()    → show create form (modal or page)
- store()     → validate + save + redirect
- show($id)   → detail view
- edit($id)   → show edit form
- update($id) → validate + update + redirect
- destroy($id) → soft delete + redirect
- All methods filter by home_id
```

**Views**:
```
resources/views/frontEnd/[section]/[resource]/
- index.blade.php        → DataTable listing
- elements/add.blade.php → Add modal
- elements/edit.blade.php → Edit modal
```

**Routes** (added to `routes/web.php`):
```php
// [Resource] Management
Route::get('/[section]/[resource]', '[Resource]Controller@index');
Route::post('/[section]/[resource]/store', '[Resource]Controller@store');
Route::get('/[section]/[resource]/edit/{id}', '[Resource]Controller@edit');
Route::post('/[section]/[resource]/update/{id}', '[Resource]Controller@update');
Route::delete('/[section]/[resource]/delete/{id}', '[Resource]Controller@destroy');
```

### `/scaffold page [name]` generates:

**Controller**:
```
app/Http/Controllers/frontEnd/[Section]/[Name]Controller.php
- index() → returns view
```

**View**:
```
resources/views/frontEnd/[section]/[name].blade.php
- Extends frontEnd.layouts.master
- Panel with header
- Content section
```

**Route** in `routes/web.php`

### `/scaffold report [name]` generates:

**Controller**:
```
app/Http/Controllers/frontEnd/[Section]/[Name]ReportController.php
- index()        → report page with filters
- getData()      → AJAX endpoint returning filtered JSON
- exportPdf()    → PDF generation using DomPDF
```

**Views**:
```
resources/views/frontEnd/[section]/reports/[name].blade.php
- Date range filter
- Home filter (if super admin)
- DataTable with server-side data
- Export button
```

### `/scaffold modal [name]` generates:

**View partial**:
```
resources/views/frontEnd/[section]/elements/[name]_modal.blade.php
- Bootstrap modal with form
- CSRF token
- Form fields
- AJAX submission JS
- Success/error handling
```

### `/scaffold api [resource]` generates:

**Controller**:
```
app/Http/Controllers/Api/[Resource]Controller.php
- index()   → paginated JSON list
- show($id) → single resource JSON
- store()   → create, return JSON
- update()  → update, return JSON
- destroy() → delete, return JSON
- All responses: { success: bool, data: ..., message: string }
```

**Routes** in `routes/api.php`

### `/scaffold migration [table]` generates:

**Migration**:
```
database/migrations/[timestamp]_create_[table]_table.php
- id, standard columns, timestamps, soft_deletes
- home_id foreign key (multi-tenancy)
- Indexes on commonly queried columns
```

**Model**:
```
app/Models/[Resource].php
- Matching $fillable
- home_id relationship
```

### `/scaffold notification [name]` generates:

**Email template**:
```
resources/views/emails/[name].blade.php
- Follows existing email template pattern (header, body, footer)
- Dynamic URL using url('/') not hardcoded
```

**Notification logic in controller** (added to relevant controller method)

### `/scaffold upload [context]` generates:

**Upload handling**:
```
- Validation: file type (MIME + extension), max size
- Storage: public/images/[context]/ or storage/app/[context]/
- Filename sanitization
- Thumbnail generation (if image)
- Delete old file on update
```

## Step 3: Wire Everything Up

After generating files:
1. Add routes to the correct route group in `web.php` (match middleware + prefix)
2. Add sidebar link if it's a new page (check `resources/views/frontEnd/common/sidebar.blade.php` or equivalent)
3. Register any new middleware if needed

## Step 4: Post-Scaffold Checklist

Output this after scaffolding:

```
SCAFFOLD COMPLETE: [feature]
─────────────────────────────────
Files created:
  [list of files]

Files modified:
  [list of modified files]

Required actions:
  [ ] Run migration (if created): php artisan migrate
  [ ] Test the page loads: visit [URL]
  [ ] Test form submission works
  [ ] Verify home_id filtering is correct
  [ ] Review generated code — customize for your domain logic
  [ ] Remove any unused generated methods/routes
  [ ] Add to sidebar navigation if needed
─────────────────────────────────
```

## Care OS Rules

- **Always filter by `home_id`** — every query must scope to the current user's home
- **Match existing patterns** — read a similar existing feature before generating. Use the same CSS classes, modal structure, DataTable config, form layout
- **Use `{{ url('...') }}`** for all URLs — never hardcode paths
- **Use `@csrf`** in every form
- **Use `$request->validate()`** for all input
- **Extend `frontEnd.layouts.master`** for frontend views, `backEnd.layouts.master` for admin views
- **Log the scaffold in `docs/logs.md`**
- Scaffold generates starting points — the `/build` command or manual work finishes the job
