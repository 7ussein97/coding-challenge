# Database Documentation

## Overview

The database uses **MySQL** with UTF-8 MB4 encoding. All tables use Laravel's Eloquent ORM conventions (snake_case, auto-incrementing `id`, `created_at`/`updated_at` timestamps).

---

## Tables

### `users`

Central authentication table shared by all roles.

| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT PK | Auto-increment |
| `name` | VARCHAR(255) | Display name |
| `email` | VARCHAR(255) UNIQUE | Login email |
| `password` | VARCHAR(255) | Bcrypt hash |
| `role` | ENUM('admin','judge','team') | User role |
| `remember_token` | VARCHAR(100) NULL | Session token |
| `email_verified_at` | TIMESTAMP NULL | — |
| `created_at` / `updated_at` | TIMESTAMP | — |

---

### `teams`

Maps a team name to its shared user account.

| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT PK | Auto-increment |
| `name` | VARCHAR(255) | Team display name |
| `user_id` | BIGINT FK → `users.id` | The team's login account (CASCADE DELETE) |
| `created_at` / `updated_at` | TIMESTAMP | — |

> Deleting the associated `users` row also deletes the `teams` row (ON DELETE CASCADE).

---

### `questions`

Problem statement storage including optional image.

| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT PK | Auto-increment |
| `title` | VARCHAR(255) NULL | Human-readable title (e.g. "Two Sum") |
| `description` | TEXT | Full problem statement (plain text or Markdown) |
| `image` | VARCHAR(255) NULL | Relative path inside `storage/app/public/` (e.g. `questions/abc.png`) |
| `order` | UNSIGNED INT | Display/sort order (1-based, admin-configurable) |
| `created_at` / `updated_at` | TIMESTAMP | — |

---

### `submissions`

Code submission from a team for a specific question.

| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT PK | Auto-increment |
| `team_id` | BIGINT FK → `teams.id` | The submitting team (CASCADE DELETE) |
| `question_id` | BIGINT FK → `questions.id` | The target question (CASCADE DELETE) |
| `code` | LONGTEXT | The submitted source code |
| `status` | ENUM('pending','accepted','rejected') | Default `pending` |
| `judge_comment` | TEXT NULL | Feedback from judge |
| `locked_by` | BIGINT NULL FK → `users.id` | Judge who has this submission open (NULL ON DELETE) |
| `locked_at` | TIMESTAMP NULL | When the lock was acquired |
| `created_at` / `updated_at` | TIMESTAMP | — |

**Locking rules:**
- `locked_by = NULL` → available for any judge
- `locked_by = judge_id` → locked by that judge (expires after 30 minutes from `locked_at`)
- Another judge trying to open returns a 403 message

---

### `events`

Optional competition timing metadata (informational only — does not block submissions).

| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT PK | Auto-increment |
| `name` | VARCHAR(255) | Event name (e.g. "UTAS CP 2025") |
| `start_time` | TIMESTAMP NULL | Contest start |
| `end_time` | TIMESTAMP NULL | Contest end |
| `created_at` / `updated_at` | TIMESTAMP | — |

---

## Relationships

```
users (1) ──────── (1) teams
  │
  └── (1) ──────── (N) submissions [via locked_by, nullable]

teams (1) ──────── (N) submissions
questions (1) ──── (N) submissions
```

**Eloquent model relationships:**

| Model | Relationship | Returns |
|-------|-------------|---------|
| `User` | `hasOne(Team)` | The team linked to this user account |
| `Team` | `belongsTo(User)` | The user account for this team |
| `Team` | `hasMany(Submission)` | All submissions by this team |
| `Question` | `hasMany(Submission)` | All submissions for this question |
| `Submission` | `belongsTo(Team)` | The submitting team |
| `Submission` | `belongsTo(Question)` | The question being answered |
| `Submission` | `belongsTo(User, 'locked_by')` | The judge currently reviewing |

---

## ERD Description

```
┌─────────────┐       ┌──────────────┐
│    users    │ 1───1 │    teams     │
│─────────────│       │──────────────│
│ id (PK)     │       │ id (PK)      │
│ name        │       │ name         │
│ email       │       │ user_id (FK) │
│ password    │       └──────┬───────┘
│ role        │              │ 1
└──────┬──────┘              │
       │ 1 (locked_by)       │ N
       │                     ▼
       │              ┌──────────────────┐
       └──────────────┤   submissions    │
                  N   │──────────────────│
                      │ id (PK)          │
                      │ team_id (FK)  ───┘
                      │ question_id (FK)─┐
                      │ code             │
                      │ status           │
                      │ judge_comment    │
                      │ locked_by (FK)   │
                      │ locked_at        │
                      └──────────────────┘
                                         │ N
                      ┌──────────────────┤
                      │   questions      │
                      │──────────────────│
                      │ id (PK)          │
                      │ title            │
                      │ description      │
                      │ image            │
                      │ order            │
                      └──────────────────┘
```

---

## Key Queries

### Leaderboard (solved count per team)
```sql
SELECT
    t.id,
    t.name,
    COUNT(DISTINCT s.question_id) AS solved_count,
    MAX(s.created_at)             AS last_solve_time
FROM teams t
LEFT JOIN submissions s
    ON s.team_id = t.id AND s.status = 'accepted'
GROUP BY t.id, t.name
ORDER BY solved_count DESC, last_solve_time ASC;
```

### First solver per question
```sql
SELECT question_id, team_id, MIN(created_at) AS solved_at
FROM submissions
WHERE status = 'accepted'
GROUP BY question_id;
```
