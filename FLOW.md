# System Flow Documentation

---

## 1. Submission Lifecycle

```
Team submits code
      │
      ▼
submissions record created
  status = "pending"
  locked_by = NULL
      │
      ▼
Judge opens submission list (/judge/submissions)
      │
      ▼
Judge clicks "Review" on a pending submission
      │
      ├─ locked_by = NULL?
      │       │
      │       ▼ YES
      │   Lock acquired:
      │   locked_by = judge.id
      │   locked_at = now()
      │   → Show submission page
      │
      ├─ locked_by = this judge?
      │       │
      │       ▼ YES
      │   Already locked by me
      │   → Show submission page
      │
      └─ locked_by = other judge?
              │
              ▼ YES
          Access denied
          → Redirect to list with error message

On submission page:
      │
      ├─ "Release Lock" button
      │       └─ Sets locked_by = NULL, locked_at = NULL
      │          → Redirect to submissions list
      │
      └─ Verdict form (requires lock)
              │
              ├─ Select "Accept" or "Reject"
              ├─ Optional: add judge comment
              └─ Click "Submit Verdict"
                      │
                      ▼
              submission updated:
                status = "accepted" | "rejected"
                judge_comment = <text>
                locked_by = NULL      ← lock released
                locked_at = NULL
              → Redirect to submissions list
```

---

## 2. Locking Mechanism

### Purpose
Prevent two judges from reviewing the same submission simultaneously, avoiding conflicting verdicts.

### Implementation
The `submissions` table has two locking columns:
- `locked_by` — Foreign key to `users.id` (nullable)
- `locked_at` — Timestamp of when the lock was acquired (nullable)

### Lock States

| `locked_by` | `locked_at` within 30 min | State |
|-------------|--------------------------|-------|
| NULL | — | **Available** |
| judge_id | YES | **Locked** by that judge |
| judge_id | NO (expired) | **Expired** — treated as Available |
| judge_id | YES | **Locked** — another judge cannot access |

### Lock Expiry
- Locks expire automatically after **30 minutes** (defined in `Submission::LOCK_TIMEOUT`)
- Expiry is checked in `Submission::isLocked()` before every access
- Expired locks do **not** need to be manually cleared — the next judge to open the submission will take the lock

### Lock Acquisition (pseudocode)
```php
// When judge opens GET /judge/submissions/{id}
if not $submission->isLocked():
    $submission->lock(auth()->id())   // sets locked_by + locked_at
elif $submission->isLockedBy(auth()->id()):
    // Already mine — just show page
else:
    // Locked by another judge
    redirect back with error
```

### Manual Release
A judge can release their lock at any time via the **"Release Lock"** button on the submission review page. This sets `locked_by = null` and `locked_at = null`.

---

## 3. Leaderboard Logic

### Scoring Model
The leaderboard uses ICPC-style scoring:
1. **Primary:** Number of distinct problems solved (accepted, higher = better)
2. **Tiebreak:** Time of last accepted solve (earlier = better)

### Key Rules
- Only the **first acceptance per question per team** counts toward the solve count
- Multiple accepted submissions for the same problem only count as **one solve**
- Rejected submissions do **not** penalise the team's time score
- All teams are shown even if they have 0 solves

### Algorithm (PHP pseudocode)
```
for each team:
    accepted = SELECT question_id, MIN(created_at)
               FROM submissions
               WHERE team_id = team.id AND status = 'accepted'
               GROUP BY question_id

    solved_count   = COUNT(accepted rows)
    last_solve_time = MAX(first_solve_time across accepted rows)
    last_question  = question title of the most recent accepted submission

sort by:
    1. solved_count DESC
    2. last_solve_time ASC (null / 0-solve teams go to bottom)
```

### Auto-Refresh
The leaderboard view polls `GET /leaderboard/data` every **5 seconds** using JavaScript `setInterval`. The response is JSON; the table DOM is updated in-place without a full page reload.

---

## 4. Authentication Flow

```
User visits any URL
      │
      ├─ Not logged in?
      │       └─ RoleMiddleware redirects to /login
      │
      └─ Logged in?
              │
              ├─ Wrong role for route?
              │       └─ HTTP 403 Forbidden
              │
              └─ Correct role?
                      └─ Request proceeds normally

POST /login:
    1. Validate email + password
    2. Auth::attempt()
    3. On success → session regenerate → redirect to role dashboard
    4. On failure → back with error message

POST /logout:
    1. Auth::logout()
    2. Session invalidate + token regenerate
    3. Redirect to /login
```

---

## 5. Question Image Flow

```
Admin uploads image during question create/edit
      │
      ├─ File stored: storage/app/public/questions/<uuid>.ext
      │
      └─ DB stores relative path: "questions/<uuid>.ext"
             │
             └─ Displayed via: Storage::url('questions/<uuid>.ext')
                              = /storage/questions/<uuid>.ext
                (requires php artisan storage:link)
```

---

## 6. Event Timing

The `events` table stores competition start/end timestamps but does **not** gate any functionality. It exists for display purposes only:
- The system does **not** block submissions before `start_time`
- The system does **not** block submissions after `end_time`
- Timestamps are stored for reference and could be surfaced in the UI in the future
