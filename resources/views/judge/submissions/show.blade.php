@extends('layouts.app')
@section('title', 'Review Submission #' . $submission->id)
@section('page-title', 'Review Submission #' . $submission->id)

@push('styles')
<style>
    .verdict-btn { width: 140px; padding: 12px; font-weight: 600; border-radius: 10px; }
    .verdict-btn.accept { background: #d1fae5; color: #065f46; border: 2px solid #10b981; }
    .verdict-btn.accept:hover, .verdict-btn.accept.selected { background: #10b981; color: #fff; border-color: #10b981; }
    .verdict-btn.reject { background: #fee2e2; color: #991b1b; border: 2px solid #ef4444; }
    .verdict-btn.reject:hover, .verdict-btn.reject.selected { background: #ef4444; color: #fff; border-color: #ef4444; }
</style>
@endpush

@section('content')
<div class="row g-4">
    {{-- Left: Submission Info + Code --}}
    <div class="col-lg-8">
        {{-- Meta --}}
        <div class="card stat-card mb-4">
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-sm-4">
                        <div class="text-muted small">Team</div>
                        <div class="fw-bold">{{ $submission->team->name }}</div>
                    </div>
                    <div class="col-sm-4">
                        <div class="text-muted small">Question</div>
                        <div class="fw-bold">{{ $submission->question->display_title }}</div>
                    </div>
                    <div class="col-sm-4">
                        <div class="text-muted small">Submitted</div>
                        <div class="fw-bold">{{ $submission->created_at->format('M d, Y H:i') }}</div>
                    </div>
                    <div class="col-sm-4">
                        <div class="text-muted small">Status</div>
                        <span class="status-badge badge-{{ $submission->status }}">{{ ucfirst($submission->status) }}</span>
                    </div>
                    @if($submission->judge)
                    <div class="col-sm-4">
                        <div class="text-muted small">Reviewed by</div>
                        <div class="fw-bold">{{ $submission->judge->name }}</div>
                    </div>
                    @endif
                    @if($submission->judge_comment)
                    <div class="col-12">
                        <div class="text-muted small">Judge Comment</div>
                        <div class="alert alert-light border mb-0 py-2 mt-1">{{ $submission->judge_comment }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Question Panel --}}
        <div class="card stat-card mb-4">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h6 class="fw-bold mb-0"><i class="fas fa-question-circle me-2 text-warning"></i>
                    Problem: {{ $submission->question->display_title }}
                </h6>
            </div>
            <div class="card-body p-4">
                @if($submission->question->image)
                    <img src="{{ $submission->question->image_url }}" class="img-fluid rounded mb-3" style="max-height:300px;">
                @endif
                <pre class="mb-0" style="white-space:pre-wrap;font-size:.9rem;">{{ $submission->question->description }}</pre>
            </div>
        </div>

        {{-- Code --}}
        <div class="card stat-card">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0"><i class="fas fa-code me-2 text-primary"></i>Submitted Code</h6>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-info"
                            data-bs-toggle="modal" data-bs-target="#run-modal">
                        <i class="fas fa-terminal me-1"></i>How to Run
                    </button>
                    <a href="{{ route('judge.submissions.download', $submission) }}"
                       class="btn btn-sm btn-outline-success">
                        <i class="fas fa-download me-1"></i>Download .py
                    </a>
                    <button class="btn btn-sm btn-secondary"
                            onclick="copyToClipboard(document.getElementById('code-content').innerText)">
                        <i class="far fa-copy me-1"></i>Copy
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="code-block rounded-0" style="border-radius:0 0 12px 12px !important;">
                    <pre id="code-content" style="margin:0;white-space:pre-wrap;word-break:break-all;">{{ $submission->code }}</pre>
                </div>
            </div>
        </div>

        {{-- Run Instructions Modal --}}
        <div class="modal fade" id="run-modal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-bottom">
                        <h6 class="modal-title fw-bold">
                            <i class="fas fa-terminal me-2 text-info"></i>How to Run
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-3">
                            Download the file first, then run one of these commands in your terminal:
                        </p>
                        <div class="mb-3">
                            <div class="text-muted small fw-semibold mb-1">File name:</div>
                            <code class="d-block p-2 rounded" style="background:#1e293b;color:#e2e8f0;font-size:.85rem;">{{ $filename }}</code>
                        </div>
                        <div class="mb-2">
                            <div class="text-muted small fw-semibold mb-1">Run command:</div>
                            <code class="d-block p-2 rounded" style="background:#1e293b;color:#86efac;font-size:.9rem;">python {{ $filename }}</code>
                        </div>
                        <div class="mb-0">
                            <div class="text-muted small fw-semibold mb-1">Or if python3 is required:</div>
                            <code class="d-block p-2 rounded" style="background:#1e293b;color:#86efac;font-size:.9rem;">python3 {{ $filename }}</code>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('judge.submissions.download', $submission) }}"
                           class="btn btn-success btn-sm">
                            <i class="fas fa-download me-1"></i>Download Now
                        </a>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right: Action Panel --}}
    <div class="col-lg-4">

        @if($submission->status !== 'pending')
        {{-- Already judged — view only --}}
        <div class="card stat-card mb-4 border-0"
             style="background:{{ $submission->status === 'accepted' ? '#d1fae5' : '#fee2e2' }};">
            <div class="card-body p-4 text-center">
                <i class="fas {{ $submission->status === 'accepted' ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} fa-3x mb-3 d-block"></i>
                <h6 class="fw-bold mb-1">
                    {{ $submission->status === 'accepted' ? 'Accepted' : 'Rejected' }}
                </h6>
                <p class="text-muted small mb-0">This submission has already been reviewed. View only.</p>
            </div>
        </div>

        @elseif($submission->isLockedBy(auth()->id()))
        {{-- Pending + locked by me — show verdict form --}}
        <div class="card stat-card mb-4">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h6 class="fw-bold mb-0"><i class="fas fa-gavel me-2" style="color:#8b5cf6"></i>Submit Verdict</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('judge.submissions.review', $submission) }}" method="POST" id="verdict-form">
                    @csrf

                    <div class="d-flex gap-2 mb-4">
                        <button type="button" class="verdict-btn accept"
                                onclick="selectVerdict('accepted', this)">
                            <i class="fas fa-check d-block fs-4 mb-1"></i>Accept
                        </button>
                        <button type="button" class="verdict-btn reject"
                                onclick="selectVerdict('rejected', this)">
                            <i class="fas fa-times d-block fs-4 mb-1"></i>Reject
                        </button>
                    </div>
                    <input type="hidden" name="verdict" id="verdict-input">

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            Judge Comment <span class="text-muted fw-normal">(optional)</span>
                        </label>
                        <textarea name="judge_comment" rows="4" class="form-control"
                                  placeholder="Explain the rejection reason or provide feedback...">{{ old('judge_comment') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-semibold" id="submit-btn" disabled>
                        <i class="fas fa-gavel me-1"></i>Submit Verdict
                    </button>
                </form>
            </div>
        </div>

        @else
        {{-- Pending but not locked by me --}}
        <div class="card stat-card mb-4">
            <div class="card-body p-4 text-center text-muted">
                <i class="fas fa-lock fa-2x mb-2 d-block opacity-50"></i>
                This submission is not locked by you.
            </div>
        </div>
        @endif

        {{-- Back button --}}
        @if($submission->status === 'pending' && $submission->isLockedBy(auth()->id()))
        {{-- Back via lock-release form so lock is properly freed --}}
        <form action="{{ route('judge.submissions.unlock', $submission) }}" method="POST" id="back-form">
            @csrf
            <button type="submit" class="btn btn-outline-secondary w-100">
                <i class="fas fa-arrow-left me-1"></i>Back to List (releases lock)
            </button>
        </form>
        @else
        <a href="{{ route('judge.submissions.index') }}" class="btn btn-outline-secondary w-100">
            <i class="fas fa-arrow-left me-1"></i>Back to List
        </a>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function selectVerdict(verdict, btn) {
    document.querySelectorAll('.verdict-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    document.getElementById('verdict-input').value = verdict;
    document.getElementById('submit-btn').disabled = false;
    document.getElementById('submit-btn').innerHTML =
        `<i class="fas fa-gavel me-1"></i>Submit as ${verdict.charAt(0).toUpperCase()+verdict.slice(1)}`;
    document.getElementById('submit-btn').className =
        'btn w-100 fw-semibold ' + (verdict === 'accepted' ? 'btn-success' : 'btn-danger');
}

@if($submission->status === 'pending' && $submission->isLockedBy(auth()->id()))
// Auto-release lock when the judge closes/navigates away without submitting a verdict
let submittingVerdict = false;
document.getElementById('verdict-form')?.addEventListener('submit', () => { submittingVerdict = true; });

window.addEventListener('beforeunload', function () {
    if (submittingVerdict) return;
    const token = document.querySelector('meta[name="csrf-token"]').content;
    navigator.sendBeacon(
        '{{ route("judge.submissions.unlock", $submission) }}',
        new Blob(['_token=' + encodeURIComponent(token)], { type: 'application/x-www-form-urlencoded' })
    );
});
@endif
</script>
@endpush
