<div wire:poll.3000ms>
{{-- Filters --}}
<div class="card stat-card mb-4">
    <div class="card-body p-3">
        <div class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small fw-semibold mb-1">Status</label>
                <select wire:model.live="statusFilter" class="form-select form-select-sm" style="min-width:130px">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="accepted">Accepted</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label small fw-semibold mb-1">Question</label>
                <select wire:model.live="questionFilter" class="form-select form-select-sm" style="min-width:160px">
                    <option value="">All Questions</option>
                    @foreach($questions as $q)
                        <option value="{{ $q->id }}">{{ $q->display_title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button wire:click="clearFilters" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-times me-1"></i>Clear
                </button>
            </div>
            <div class="col-auto ms-auto">
                <span class="badge rounded-pill" style="background:#e0f2fe;color:#0369a1;font-size:.75rem;">
                    <i class="fas fa-circle me-1" style="font-size:.5rem;"></i>Live
                </span>
            </div>
        </div>
    </div>
</div>

<div class="table-card">
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
        <h6 class="fw-bold mb-0">
            Submissions
            <span class="text-muted fw-normal small ms-1">({{ $submissions->count() }} shown)</span>
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
                    <th>Lock</th>
                    <th>Submitted</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $sub)
                <tr>
                    <td class="text-muted small">{{ $sub->id }}</td>
                    <td class="fw-semibold">{{ $sub->team->name }}</td>
                    <td>{{ $sub->question->display_title }}</td>
                    <td>
                        <span class="status-badge badge-{{ $sub->status }}">{{ ucfirst($sub->status) }}</span>
                    </td>
                    <td>
                        @if($sub->isLocked())
                            @if($sub->isLockedBy(auth()->id()))
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-lock me-1"></i>You
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="fas fa-lock me-1"></i>{{ $sub->judge?->name ?? 'Other' }}
                                </span>
                            @endif
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $sub->created_at->diffForHumans() }}</td>
                    <td>
                        @if($sub->status === 'pending' || $sub->isLockedBy(auth()->id()))
                            @if($sub->isLocked() && !$sub->isLockedBy(auth()->id()))
                                <button class="btn btn-sm btn-outline-secondary" disabled title="Locked by another judge">
                                    <i class="fas fa-lock"></i>
                                </button>
                            @else
                                <a href="{{ route('judge.submissions.show', $sub) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-gavel me-1"></i>Review
                                </a>
                            @endif
                        @else
                            <a href="{{ route('judge.submissions.show', $sub) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-5">
                    <i class="fas fa-inbox fa-2x mb-2 d-block opacity-25"></i>No submissions found.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
