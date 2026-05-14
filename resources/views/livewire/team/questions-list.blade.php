<div wire:poll.4000ms>
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-bold mb-0">Problem Set</h5>
        <p class="text-muted small mb-0">
            {{ $questions->count() }} problems available
            <span class="ms-2 badge rounded-pill" style="background:#e0f2fe;color:#0369a1;font-size:.7rem;">Live</span>
        </p>
    </div>
    <a href="{{ route('leaderboard.index') }}" class="btn btn-outline-warning btn-sm">
        <i class="fas fa-trophy me-1"></i>Leaderboard
    </a>
</div>

<div class="row g-3">
    @forelse($questions as $q)
    @php $status = $statusMap[$q->id] ?? null; $attempts = $attemptMap[$q->id] ?? 0; $comment = $commentMap[$q->id] ?? null; @endphp
    <div class="col-md-6 col-lg-4">
        <div class="card stat-card h-100 {{ $status === 'accepted' ? 'border border-success' : '' }}"
             style="{{ $status === 'accepted' ? 'border-color:#10b981 !important;' : '' }}">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                         style="width:40px;height:40px;background:
                            @if($status === 'accepted') #10b981
                            @elseif($status === 'rejected') #ef4444
                            @elseif($status === 'pending') #f59e0b
                            @else #64748b @endif;font-size:1.1rem;">
                        {{ $q->order }}
                    </div>
                    @if($status)
                        <span class="status-badge badge-{{ $status }}">{{ ucfirst($status) }}</span>
                    @else
                        <span class="status-badge" style="background:#f1f5f9;color:#64748b;">Not Attempted</span>
                    @endif
                </div>

                <h6 class="fw-bold mb-2">{{ $q->display_title }}</h6>
                <p class="text-muted small mb-3" style="display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">
                    {{ $q->description }}
                </p>

                @if($comment && $status !== 'accepted')
                <div class="d-flex align-items-start gap-2 mb-3 p-2 rounded"
                     style="background:{{ $status === 'rejected' ? '#fef2f2' : '#fffbeb' }};border-left:3px solid {{ $status === 'rejected' ? '#ef4444' : '#f59e0b' }};">
                    <i class="fas fa-comment-alt mt-1 flex-shrink-0" style="color:{{ $status === 'rejected' ? '#ef4444' : '#f59e0b' }};font-size:.75rem;"></i>
                    <span style="font-size:.78rem;color:#374151;line-height:1.4;">{{ $comment }}</span>
                </div>
                @endif

                @if($q->image)
                <img src="{{ $q->image_url }}" class="img-fluid rounded mb-3" style="max-height:100px;width:100%;object-fit:cover;">
                @endif

                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted small">
                        <i class="fas fa-paper-plane me-1"></i>{{ $attempts }} attempt{{ $attempts !== 1 ? 's' : '' }}
                    </span>
                    @if($status === 'accepted')
                        <span class="btn btn-sm btn-success disabled">
                            <i class="fas fa-check me-1"></i>Solved
                        </span>
                    @else
                        <a href="{{ route('team.questions.submit', $q) }}" class="btn btn-sm btn-primary">
                            @if($attempts > 0)
                                <i class="fas fa-redo me-1"></i>Resubmit
                            @else
                                <i class="fas fa-paper-plane me-1"></i>Submit
                            @endif
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card stat-card text-center py-5 text-muted">
            <i class="fas fa-question-circle fa-3x mb-3 opacity-25 d-block"></i>
            No problems available yet. Check back soon!
        </div>
    </div>
    @endforelse
</div>
</div>
