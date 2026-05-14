<div wire:poll.3000ms>
{{-- Stats --}}
<div class="row g-3 mb-4">
    @php
    $cards = [
        ['label'=>'Pending Review', 'value'=>$stats['pending'],   'icon'=>'fa-clock',        'bg'=>'#f59e0b','light'=>'#fffbeb'],
        ['label'=>'Accepted',       'value'=>$stats['accepted'],  'icon'=>'fa-check-circle', 'bg'=>'#10b981','light'=>'#ecfdf5'],
        ['label'=>'Rejected',       'value'=>$stats['rejected'],  'icon'=>'fa-times-circle', 'bg'=>'#ef4444','light'=>'#fef2f2'],
        ['label'=>'My Locked',      'value'=>$stats['my_locked'], 'icon'=>'fa-lock',         'bg'=>'#8b5cf6','light'=>'#f5f3ff'],
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

{{-- Pending Submissions Queue --}}
<div class="table-card">
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
        <h6 class="fw-bold mb-0">
            <i class="fas fa-inbox me-2 text-warning"></i>
            Pending Review Queue
            @if($stats['pending'] > 0)
                <span class="badge bg-warning text-dark ms-2">{{ $stats['pending'] }}</span>
            @endif
            <span class="ms-2 badge rounded-pill" style="background:#e0f2fe;color:#0369a1;font-size:.7rem;">Live</span>
        </h6>
        <a href="{{ route('judge.submissions.index') }}" class="btn btn-sm btn-outline-primary">
            View All <i class="fas fa-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Team</th>
                    <th>Question</th>
                    <th>Submitted</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingSubmissions as $sub)
                <tr>
                    <td class="text-muted small">{{ $sub->id }}</td>
                    <td class="fw-semibold">{{ $sub->team->name }}</td>
                    <td>{{ $sub->question->display_title }}</td>
                    <td class="text-muted small">{{ $sub->created_at->diffForHumans() }}</td>
                    <td>
                        <a href="{{ route('judge.submissions.show', $sub) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-gavel me-1"></i>Review
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-5">
                    <i class="fas fa-check-circle fa-2x mb-2 d-block text-success opacity-50"></i>
                    All caught up! No pending submissions.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
