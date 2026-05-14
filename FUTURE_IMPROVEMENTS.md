# Future Improvements

---

## 1. Automated Code Execution (Sandbox)

Currently judges manually review submitted code. Automatic execution would:

### What it involves
- Execute submitted code in an isolated sandbox (Docker container, `nsjail`, `firejail`, or cloud service)
- Evaluate against predefined test cases (input/output pairs stored in the DB)
- Auto-verdict: `accepted` if all test cases pass within time/memory limits

### Implementation Options
| Option | Pros | Cons |
|--------|------|------|
| Docker per submission | Full isolation, language-agnostic | Slow startup, requires Docker daemon |
| Judge0 (open-source API) | Battle-tested, multi-language | External dependency or self-hosted infra |
| Piston API | Simple REST API, 50+ languages | Rate limits on free tier |
| AWS Lambda / Cloud | Serverless, scales to 0 | Cost per execution, latency |

### Database Changes Needed
```sql
-- Test cases table
CREATE TABLE test_cases (
    id BIGINT PK,
    question_id BIGINT FK,
    input TEXT,
    expected_output TEXT,
    time_limit_ms INT DEFAULT 2000,
    memory_limit_mb INT DEFAULT 256,
    is_sample BOOLEAN DEFAULT false
);
```

---

## 2. Real-Time Updates (WebSockets)

### Current Limitation
- Leaderboard refreshes via 5-second polling (HTTP GET)
- Judges see stale data until manual refresh
- Teams don't get notified when their submission is judged

### Upgrade Path

**Laravel Reverb** (built-in Laravel WebSocket server):
```bash
php artisan install:broadcasting
php artisan reverb:start
```

**Events to broadcast:**
- `SubmissionJudged` → team's browser updates their submission status live
- `LeaderboardUpdated` → all clients update the table instantly
- `NewSubmission` → judge dashboard badge increments

**Frontend:**
```javascript
// Using Laravel Echo
Echo.channel('leaderboard')
    .listen('LeaderboardUpdated', (e) => {
        updateLeaderboardTable(e.data);
    });

Echo.private(`team.${teamId}`)
    .listen('SubmissionJudged', (e) => {
        updateSubmissionStatus(e.submission);
    });
```

---

## 3. Penalty Time Scoring (Full ICPC Mode)

### Current Scoring
- Sorted by: solved count DESC, last solve time ASC
- Rejected submissions have no time penalty

### ICPC Scoring
- Each wrong attempt before acceptance adds a **20-minute penalty**
- Total time = Σ(solve_time_from_start + 20min × wrong_attempts) for each solved problem
- Unsolved problems contribute no penalty

### Implementation
```php
// In LeaderboardController::buildLeaderboard()
foreach ($accepted_questions as $question_id) {
    $first_accepted_time = ...;       // minutes from contest start
    $wrong_before_accept = ...;       // count of rejected before first accept
    $penalty += $first_accepted_time + (20 * $wrong_before_accept);
}
```

Requires the `events` table `start_time` to calculate elapsed time from contest start.

---

## 4. Scaling & Performance

### Database
- Add indexes on `submissions(status)`, `submissions(team_id, question_id)`, `submissions(locked_by)`
- Use **read replicas** for the leaderboard query (heaviest read)
- Cache leaderboard data in Redis with 3-second TTL

### Application
- Use **Laravel Octane** (Swoole/RoadRunner) for ~10× throughput vs `artisan serve`
- Enable **query caching** for static data (questions list)
- Use **Laravel Horizon** with Redis queues for background judging jobs

### File Storage
- Move from local disk to **Amazon S3** or **MinIO** for question images
- Change in `.env`: `FILESYSTEM_DISK=s3`

---

## 5. Admin Panel Enhancements

- **Bulk import teams** from CSV file
- **Event management UI** (create/edit events with start/end time)
- **Submission export** (download all submissions as ZIP for archival)
- **Live judging statistics** (charts with Chart.js or ApexCharts)
- **Announcement system** (admin broadcasts a message to all teams)

---

## 6. Security Hardening

- **Rate limiting** on the submission endpoint (prevent spam)
  ```php
  Route::post(...)->middleware('throttle:10,1'); // 10 per minute
  ```
- **Code size limit** (reject submissions > 100KB to prevent DoS)
- **IP logging** for audit trails
- **Two-factor authentication** for admin accounts (Laravel Fortify)
- **Content Security Policy** headers

---

## 7. Team UX Improvements

- **Syntax highlighting** in the code submission textarea (CodeMirror or Monaco Editor)
- **Language selector** (for display purposes and eventual auto-execution)
- **Live submission status** via WebSocket (instead of manual page refresh)
- **Problem difficulty tags** (Easy / Medium / Hard)
- **Problem PDF download** for complex problems with complex formatting

---

## 8. Multi-Contest Support

Currently the platform supports a single ongoing competition. Future:
- `contests` table with start/end time and list of questions
- Multiple concurrent contests
- Per-contest leaderboards
- Archive view of past contests

---

## 9. Judge Management Improvements

- **Judge assignment** — assign specific questions to specific judges
- **Load balancing** — distribute unreviewed submissions evenly
- **Judge performance metrics** — average review time, accuracy stats
- **Conflict of interest** flag — judge cannot review submissions from their team

---

## 10. CI/CD and DevOps

- **GitHub Actions** pipeline: lint → test → deploy
- **Docker Compose** setup for reproducible environments:
  ```yaml
  services:
    app:    php:8.2-fpm
    nginx:  nginx:alpine
    mysql:  mysql:8.0
    redis:  redis:alpine
  ```
- **Laravel Sail** for local Docker development: `./vendor/bin/sail up`
