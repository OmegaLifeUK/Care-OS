Show the current status of the Care OS project.

Instructions:
1. Read `docs/logs.md` — find the latest "Status:" section to see what's done
2. Read `phases/` — list all phase docs and their completion state
3. Check `git log --oneline -5` for recent commits
4. Check `git status` for any uncommitted changes
5. Check if the dev server is running (`curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:8000` — 000 means not running)
6. Check if MySQL is running (`mysql -u root -e "SELECT 1" 2>&1`)

Report format:
```
## Care OS Status

**Branch**: [current branch]
**Last commit**: [hash + message]
**Uncommitted changes**: [yes/no + count]
**Dev server**: [running/stopped]
**MySQL**: [running/stopped]

### Current Phase
[Phase N — name, what's done, what's next]

### Recent Activity
[Last 3 log entries from docs/logs.md]
```
