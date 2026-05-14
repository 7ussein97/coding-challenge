# Roles & Permissions

The platform has three roles, each with a distinct set of permissions and a dedicated dashboard.

---

## Role: Admin

Admins manage the entire platform. There is typically one admin account created during setup.

### Access
- URL prefix: `/admin/...`
- Middleware: `auth` + `role:admin`

### Permissions

| Action | Allowed |
|--------|---------|
| Create team accounts | ✅ |
| Delete team accounts | ✅ |
| Create judge accounts | ✅ |
| Delete judge accounts | ✅ |
| Create questions | ✅ |
| Edit questions | ✅ |
| Delete questions | ✅ |
| Upload question images | ✅ |
| Set question display order | ✅ |
| View all submissions | ✅ (read-only on dashboard) |
| Review / judge submissions | ❌ |
| Submit code | ❌ |
| View leaderboard | ✅ |

### Dashboard Features
- Platform statistics (teams, judges, questions, submission counts by status)
- Quick-action buttons (create team/judge/question)
- Recent submissions overview table

### Notes
- When an admin creates a **Team**, a linked `users` record is automatically created with `role = 'team'`. The team name and the user's name are kept in sync.
- Admin is the only role that can manage the competition structure (questions, participants).

---

## Role: Judge

Judges review submitted code and render verdicts. Multiple judges can work simultaneously, protected by the submission locking mechanism.

### Access
- URL prefix: `/judge/...`
- Middleware: `auth` + `role:judge`

### Permissions

| Action | Allowed |
|--------|---------|
| View all submissions | ✅ |
| Filter submissions by status/question | ✅ |
| Open (lock) a submission | ✅ |
| Copy submitted code | ✅ |
| Accept a submission | ✅ |
| Reject a submission with comment | ✅ |
| Release their own lock | ✅ |
| Open a submission locked by another judge | ❌ |
| Create/edit/delete questions | ❌ |
| View leaderboard | ✅ |

### Dashboard Features
- Count of pending, accepted, rejected submissions
- Count of submissions currently locked by this judge
- Pending review queue with direct "Review" links

### Submission Locking Rules
1. Judge navigates to a submission
2. If `locked_by = null` → judge takes the lock
3. If `locked_by = this judge` → judge already holds the lock, page loads normally
4. If `locked_by = other judge` → access denied, redirect with informational message
5. Lock expires automatically after **30 minutes** from `locked_at`
6. Judge can manually release their lock using the "Release Lock" button
7. After submitting a verdict (accept/reject), the lock is automatically released

---

## Role: Team

Teams are competition participants. Each team has **one shared login account** that all team members use together.

### Access
- URL prefix: `/team/...`
- Middleware: `auth` + `role:team`

### Permissions

| Action | Allowed |
|--------|---------|
| View all questions / problem set | ✅ |
| View question images | ✅ |
| Submit code for any question | ✅ |
| Resubmit (even after rejection) | ✅ |
| View own submission history | ✅ |
| View judge feedback/comments | ✅ |
| Copy own submitted code | ✅ |
| View other teams' submissions | ❌ |
| Submit on behalf of other teams | ❌ |
| View leaderboard | ✅ |

### Dashboard Features
- Problem solve progress (N solved / M total)
- Submission statistics (accepted, pending, rejected)
- Recent submission list with status badges

### Submission Flow
1. Team selects a problem from the problem list
2. Team types/pastes code into the textarea
3. Click "Submit Solution" → submission created with `status = pending`
4. Team can view their submission history at any time
5. When a judge reviews the submission, the status updates to `accepted` or `rejected`
6. If rejected, the judge may leave a comment; the team can then resubmit

### Notes
- Teams can submit multiple times for the same question (only accepted submissions count toward the leaderboard)
- Previously submitted code can be loaded into the editor via the "Load this code" button in the submission history panel

---

## Leaderboard (All Roles)

The `/leaderboard` page is **public** — no login required. It shows:
- Team name
- Number of distinct accepted questions
- Last solved question title
- Time of last accepted solve
- Total submission attempts
- First-solver badges per problem

Ranking is ICPC-style:
1. Most solved questions first (descending)
2. Tiebreak: earlier last-solve time wins (ascending)

---

## Role Comparison Summary

| Feature | Admin | Judge | Team |
|---------|:-----:|:-----:|:----:|
| Manage teams | ✅ | ❌ | ❌ |
| Manage judges | ✅ | ❌ | ❌ |
| Manage questions | ✅ | ❌ | ❌ |
| Review submissions | ❌ | ✅ | ❌ |
| Submit code | ❌ | ❌ | ✅ |
| View own submissions | ❌ | ❌ | ✅ |
| View all submissions | ✅ | ✅ | ❌ |
| View leaderboard | ✅ | ✅ | ✅ |
