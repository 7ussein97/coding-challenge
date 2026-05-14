<div wire:poll.4000ms>
{{-- Welcome Banner --}}
<div class="card border-0 mb-4" style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;border-radius:14px;">
    <div class="card-body p-4 d-flex align-items-center justify-content-between">
        <div>
            <h5 class="fw-bold mb-1">Welcome, {{ $team->name }}!</h5>
            <p class="mb-0 opacity-75">
                You've solved <strong>{{ $stats['solved'] }}</strong> out of
                <strong>{{ $stats['total_questions'] }}</strong> problems.
            </p>
        </div>
        <div class="text-end">
            <div style="font-size:3rem;opacity:.3;"><i class="fas fa-code"></i></div>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    @php
    $cards = [
        ['label'=>'Problems Solved',  'value'=>$stats['solved'],            'icon'=>'fa-check-double', 'bg'=>'#10b981','light'=>'#ecfdf5'],
        ['label'=>'Pending',          'value'=>$stats['pending'],           'icon'=>'fa-clock',        'bg'=>'#f59e0b','light'=>'#fffbeb'],
        ['label'=>'Rejected',         'value'=>$stats['rejected'],          'icon'=>'fa-times-circle', 'bg'=>'#ef4444','light'=>'#fef2f2'],
        ['label'=>'Total Submissions','value'=>$stats['total_submissions'], 'icon'=>'fa-paper-plane',  'bg'=>'#3b82f6','light'=>'#eff6ff'],
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

{{-- Quick Actions --}}
<div class="row g-3 mb-4">
    <div class="col-6">
        <a href="{{ route('team.questions.index') }}" class="card stat-card text-decoration-none h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="icon-wrap" style="background:#eff6ff;color:#3b82f6;width:52px;height:52px;font-size:1.5rem;">
                    <i class="fas fa-list-ol"></i>
                </div>
                <div>
                    <div class="fw-bold">View Problems</div>
                    <div class="text-muted small">Submit your solutions</div>
                </div>
                <i class="fas fa-arrow-right ms-auto text-muted"></i>
            </div>
        </a>
    </div>
    <div class="col-6">
        <a href="{{ route('team.submissions.history') }}" class="card stat-card text-decoration-none h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="icon-wrap" style="background:#f5f3ff;color:#8b5cf6;width:52px;height:52px;font-size:1.5rem;">
                    <i class="fas fa-history"></i>
                </div>
                <div>
                    <div class="fw-bold">My Submissions</div>
                    <div class="text-muted small">View all your attempts</div>
                </div>
                <i class="fas fa-arrow-right ms-auto text-muted"></i>
            </div>
        </a>
    </div>
</div>

{{-- Recent Submissions --}}
@if($recentSubmissions->count() > 0)
<div class="table-card">
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
        <h6 class="fw-bold mb-0">
            <i class="fas fa-clock me-2 text-muted"></i>Recent Submissions
            <span class="ms-2 badge rounded-pill" style="background:#e0f2fe;color:#0369a1;font-size:.7rem;">Live</span>
        </h6>
        <a href="{{ route('team.submissions.history') }}" class="btn btn-sm btn-outline-secondary">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr><th>Question</th><th>Status</th><th>Submitted</th></tr>
            </thead>
            <tbody>
                @foreach($recentSubmissions as $sub)
                <tr>
                    <td class="fw-semibold">{{ $sub->question->display_title }}</td>
                    <td><span class="status-badge badge-{{ $sub->status }}">{{ ucfirst($sub->status) }}</span></td>
                    <td class="text-muted small">{{ $sub->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
</div>
