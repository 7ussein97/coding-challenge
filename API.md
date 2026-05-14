# Route / API Reference

This application uses server-rendered Blade views (no REST API). All routes return HTML unless noted. CSRF protection is enforced on all POST/PUT/DELETE routes.

---

## Authentication

All protected routes require an active session. Unauthenticated requests are redirected to `/login`.

| Method | URL | Description |
|--------|-----|-------------|
| GET | `/login` | Show login form |
| POST | `/login` | Authenticate user |
| POST | `/logout` | Invalidate session |

### Login Request (POST `/login`)
```
Content-Type: application/x-www-form-urlencoded

email=admin@admin.com
password=password
remember=1          (optional)
_token=<csrf>
```

### Login Response
- **Success:** Redirect to role-specific dashboard (HTTP 302)
- **Failure:** Back to login with `errors.email` message

---

## Public Routes

| Method | URL | Description | Response |
|--------|-----|-------------|----------|
| GET | `/leaderboard` | Leaderboard HTML page | HTML |
| GET | `/leaderboard/data` | Leaderboard JSON (for AJAX) | JSON |

### GET `/leaderboard/data` — Response
```json
{
    "leaderboard": [
        {
            "id": 1,
            "name": "Team Alpha",
            "solved_count": 3,
            "last_solve_time": "2025-01-01 10:30:00",
            "last_solved_question": "Problem #2",
            "total_attempts": 5
        }
    ],
    "timestamp": "10:35:00"
}
```

---

## Admin Routes

All routes require: `auth` + `role:admin` middleware. Prefix: `/admin`

| Method | URL | Controller | Description |
|--------|-----|-----------|-------------|
| GET | `/admin/dashboard` | `Admin\DashboardController@index` | Stats overview |
| GET | `/admin/teams` | `Admin\TeamController@index` | List all teams |
| GET | `/admin/teams/create` | `Admin\TeamController@create` | Create team form |
| POST | `/admin/teams` | `Admin\TeamController@store` | Create team + user |
| DELETE | `/admin/teams/{team}` | `Admin\TeamController@destroy` | Delete team + user |
| GET | `/admin/judges` | `Admin\JudgeController@index` | List all judges |
| GET | `/admin/judges/create` | `Admin\JudgeController@create` | Create judge form |
| POST | `/admin/judges` | `Admin\JudgeController@store` | Create judge |
| DELETE | `/admin/judges/{judge}` | `Admin\JudgeController@destroy` | Delete judge |
| GET | `/admin/questions` | `Admin\QuestionController@index` | List questions |
| GET | `/admin/questions/create` | `Admin\QuestionController@create` | Create question form |
| POST | `/admin/questions` | `Admin\QuestionController@store` | Create question |
| GET | `/admin/questions/{id}/edit` | `Admin\QuestionController@edit` | Edit question form |
| PUT | `/admin/questions/{id}` | `Admin\QuestionController@update` | Update question |
| DELETE | `/admin/questions/{id}` | `Admin\QuestionController@destroy` | Delete question |

### POST `/admin/teams` — Request Body
```
name=Team Bravo
email=bravo@team.com
password=secret123
password_confirmation=secret123
_token=<csrf>
```

### POST `/admin/questions` — Request (multipart/form-data)
```
title=Two Sum          (optional)
description=<text>
order=1
image=<file>           (optional, image/*, max 4MB)
_token=<csrf>
```

---

## Judge Routes

All routes require: `auth` + `role:judge` middleware. Prefix: `/judge`

| Method | URL | Controller | Description |
|--------|-----|-----------|-------------|
| GET | `/judge/dashboard` | `Judge\DashboardController@index` | Stats + pending queue |
| GET | `/judge/submissions` | `Judge\SubmissionController@index` | All submissions (filterable) |
| GET | `/judge/submissions/{id}` | `Judge\SubmissionController@show` | Open + lock submission |
| POST | `/judge/submissions/{id}/unlock` | `Judge\SubmissionController@unlock` | Release lock |
| POST | `/judge/submissions/{id}/review` | `Judge\SubmissionController@review` | Submit verdict |

### GET `/judge/submissions` — Query Parameters
| Param | Values | Description |
|-------|--------|-------------|
| `status` | `pending`, `accepted`, `rejected` | Filter by status |
| `question_id` | integer | Filter by question |
| `team_id` | integer | Filter by team |

### POST `/judge/submissions/{id}/review` — Request Body
```
verdict=accepted        (or: rejected)
judge_comment=<text>    (optional, max 2000 chars)
_token=<csrf>
```

**Locking behaviour on GET `/judge/submissions/{id}`:**
- If `locked_by = null` → locks submission (`locked_by = judge_id`, `locked_at = now()`)
- If `locked_by = current judge` → refreshes (already locked by you)
- If `locked_by = other judge` → HTTP redirect with error message

---

## Team Routes

All routes require: `auth` + `role:team` middleware. Prefix: `/team`

| Method | URL | Controller | Description |
|--------|-----|-----------|-------------|
| GET | `/team/dashboard` | `Team\DashboardController@index` | Stats + recent submissions |
| GET | `/team/questions` | `Team\SubmissionController@questions` | Problem list with status |
| GET | `/team/questions/{id}/submit` | `Team\SubmissionController@create` | Submission form |
| POST | `/team/questions/{id}/submit` | `Team\SubmissionController@store` | Submit code |
| GET | `/team/submissions` | `Team\SubmissionController@history` | Submission history |

### POST `/team/questions/{id}/submit` — Request Body
```
code=<source code text>
_token=<csrf>
```

### Response
- **Success:** Redirect to `/team/questions` with success flash message
- **Validation error:** Back to form with errors

---

## HTTP Status Codes Used

| Code | Meaning |
|------|---------|
| 200 | OK — page rendered successfully |
| 302 | Redirect (after form submit, or unauthenticated access) |
| 403 | Forbidden (wrong role, or accessing locked submission) |
| 404 | Not found (model not found via route model binding) |
| 419 | CSRF token mismatch |
| 422 | Validation failed |
| 500 | Server error (check `storage/logs/laravel.log`) |
