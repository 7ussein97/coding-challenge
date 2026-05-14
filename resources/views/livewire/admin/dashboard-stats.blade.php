<div wire:poll.5000ms>
{{-- Stats Row --}}
<div class="row g-3 mb-4">
    @php
    $cards = [
        ['label'=>'Teams',       'value'=>$stats['teams'],       'icon'=>'fa-users',          'bg'=>'#3b82f6','light'=>'#eff6ff'],
        ['label'=>'Judges',      'value'=>$stats['judges'],      'icon'=>'fa-user-tie',       'bg'=>'#8b5cf6','light'=>'#f5f3ff'],
        ['label'=>'Questions',   'value'=>$stats['questions'],   'icon'=>'fa-question-circle','bg'=>'#f59e0b','light'=>'#fffbeb'],
        ['label'=>'Submissions', 'value'=>$stats['submissions'], 'icon'=>'fa-paper-plane',    'bg'=>'#10b981','light'=>'#ecfdf5'],
    ];
    @endphp
    @foreach($cards as $card)
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="icon-wrap" style="background:{{ $card['light'] }};color:{{ $card['bg'] }}">
                    <i class="fas {{ $card['icon'] }}"></i>
                </div>
                <div>
                    <div class="text-muted small">{{ $card['label'] }}</div>
                    <div class="fw-bold fs-4 lh-1">{{ $card['value'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Submission Status Row --}}
<div class="row g-3 mb-4">
    <div class="col-4">
        <div class="card stat-card border-0 text-center py-3">
            <div class="fs-3 fw-bold text-warning">{{ $stats['pending'] }}</div>
            <div class="text-muted small">Pending</div>
        </div>
    </div>
    <div class="col-4">
        <div class="card stat-card border-0 text-center py-3">
            <div class="fs-3 fw-bold text-success">{{ $stats['accepted'] }}</div>
            <div class="text-muted small">Accepted</div>
        </div>
    </div>
    <div class="col-4">
        <div class="card stat-card border-0 text-center py-3">
            <div class="fs-3 fw-bold text-danger">{{ $stats['rejected'] }}</div>
            <div class="text-muted small">Rejected</div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card stat-card">
            <div class="card-body p-3">
                <h6 class="fw-bold mb-3"><i class="fas fa-bolt me-2 text-warning"></i>Quick Actions</h6>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.teams.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>New Team
                    </a>
                    <a href="{{ route('admin.judges.create') }}" class="btn btn-sm" style="background:#8b5cf6;color:#fff;border:none;">
                        <i class="fas fa-plus me-1"></i>New Judge
                    </a>
                    <a href="{{ route('admin.questions.create') }}" class="btn btn-warning btn-sm text-dark">
                        <i class="fas fa-plus me-1"></i>New Question
                    </a>
                    <a href="{{ route('leaderboard.index') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-trophy me-1"></i>Leaderboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Submissions --}}
<div class="table-card">
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
        <h6 class="fw-bold mb-0">
            <i class="fas fa-clock me-2 text-muted"></i>Recent Submissions
            <span class="ms-2 badge rounded-pill" style="background:#e0f2fe;color:#0369a1;font-size:.7rem;">Live</span>
        </h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Team</th>
                    <th>Question</th>
                    <th>Status</th>
                    <th>Submitted</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentSubmissions as $sub)
                <tr>
                    <td class="text-muted small">{{ $sub->id }}</td>
                    <td class="fw-semibold">{{ $sub->team->name }}</td>
                    <td>{{ $sub->question->display_title }}</td>
                    <td>
                        <span class="status-badge badge-{{ $sub->status }}">
                            {{ ucfirst($sub->status) }}
                        </span>
                    </td>
                    <td class="text-muted small">{{ $sub->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No submissions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
