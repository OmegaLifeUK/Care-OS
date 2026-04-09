Run a health check audit on the Care OS codebase.

Instructions — scan for these issues and report findings:

### 1. Hardcoded URLs
- Grep for `socialcareitsolutions` in all PHP, Blade, and JS files (exclude docs/, sessions/, phases/, error_log)
- Grep for `itdevelopmentservices.com` in all PHP, Blade, and JS files
- Report count and file list

### 2. Backup/Duplicate Files
- Find files matching `*_backup*`, `*_bkup*`, `*-old*`, `*Backup*`, `*.blade(*).*` in app/ and resources/
- Report count and file list

### 3. Misplaced Files
- Find `.blade.php` files in `app/Http/Controllers/`
- Find non-blade `.php` files (not ending in .blade.php) in `resources/views/`
- Report count and file list

### 4. Route Issues
- Run `php -d error_reporting=0 artisan route:list 2>&1` and check for errors
- Report any controller class not found errors

### 5. Missing Assets
- Check that `public/public` symlink exists (needed for local dev)
- Check that `.env` has `DB_HOST=127.0.0.1` (local dev, not production)

### Report Format
```
## Care OS Audit Report — [date]

### Hardcoded URLs: [PASS/FAIL] ([count] found)
### Backup Files: [PASS/FAIL] ([count] found)
### Misplaced Files: [PASS/FAIL] ([count] found)
### Route Issues: [PASS/FAIL]
### Asset/Config Check: [PASS/FAIL]

[Details for any FAIL items]
```

Log audit results in `docs/logs.md`.
