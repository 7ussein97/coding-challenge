<div wire:poll.4000ms>
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-bold mb-0">Submission History</h5>
        <p class="text-muted small mb-0">
            {{ $team->name }} — {{ $submissions->count() }} submissions
            <span class="ms-2 badge rounded-pill" style="background:#e0f2fe;color:#0369a1;font-size:.7rem;">Live</span>
        </p>
    </div>
    <a href="{{ route('team.questions.index') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-list-ol me-1"></i>Back to Problems
    </a>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Question</th>
                    <th>Status</th>
                    <th>Judge Comment</th>
                    <th>Submitted</th>
                    <th>Code</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $sub)
                <tr>
                    <td class="text-muted small">{{ $sub->id }}</td>
                    <td class="fw-semibold">{{ $sub->question->display_title }}</td>
                    <td>
                        <span class="status-badge badge-{{ $sub->status }}">{{ ucfirst($sub->status) }}</span>
                    </td>
                    <td>
                        @if($sub->judge_comment)
                            <div class="text-muted small" style="max-width:200px;">
                                <i class="fas fa-comment me-1"></i>{{ Str::limit($sub->judge_comment, 60) }}
                            </div>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $sub->created_at->format('M d, Y H:i') }}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-secondary"
                                data-bs-toggle="modal" data-bs-target="#code-modal-{{ $sub->id }}">
                            <i class="fas fa-eye me-1"></i>View
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-5">
                    <i class="fas fa-paper-plane fa-2x mb-2 d-block opacity-25"></i>No submissions yet.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Code Modals --}}
@foreach($submissions as $sub)
@php
    $attemptNum  = $attemptNumberMap[$sub->id] ?? 1;
    $teamSlug    = str_replace('-', '_', \Illuminate\Support\Str::slug($team->name));
    $qSlug       = 'Q' . $sub->question->order;
    $pyFilename  = "{$teamSlug}_{$qSlug}_attempt{$attemptNum}.py";
@endphp
<div class="modal fade" id="code-modal-{{ $sub->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-code me-2"></i>
                    {{ $sub->question->display_title }} — #{{ $sub->id }}
                    <span class="status-badge badge-{{ $sub->status }} ms-2">{{ ucfirst($sub->status) }}</span>
                </h6>
                <div class="d-flex gap-2 align-items-center">
                    <button class="btn btn-sm btn-outline-info"
                            onclick="showRunInstructions('{{ $pyFilename }}')">
                        <i class="fas fa-terminal me-1"></i>Run
                    </button>
                    <a href="{{ route('team.submissions.download', $sub) }}"
                       class="btn btn-sm btn-outline-success">
                        <i class="fas fa-download me-1"></i>.py
                    </a>
                    <button class="btn btn-sm btn-secondary"
                            onclick="copyToClipboard(document.getElementById('code-{{ $sub->id }}').innerText)">
                        <i class="far fa-copy me-1"></i>Copy
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body p-0">
                <div class="code-block rounded-0">
                    <pre id="code-{{ $sub->id }}" style="margin:0;white-space:pre-wrap;word-break:break-all;">{{ $sub->code }}</pre>
                </div>
            </div>
            @if($sub->judge_comment)
            <div class="modal-footer bg-light">
                <div class="w-100">
                    <strong class="small">Judge Comment:</strong>
                    <p class="mb-0 small text-muted">{{ $sub->judge_comment }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endforeach

{{-- Shared Run Instructions Modal --}}
<div class="modal fade" id="run-instructions-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-terminal me-2 text-info"></i>How to Run
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Download the file then run one of these commands:</p>
                <div class="mb-3">
                    <div class="text-muted small fw-semibold mb-1">File name:</div>
                    <code id="run-filename" class="d-block p-2 rounded" style="background:#1e293b;color:#e2e8f0;font-size:.85rem;"></code>
                </div>
                <div class="mb-2">
                    <div class="text-muted small fw-semibold mb-1">Run command:</div>
                    <code id="run-cmd" class="d-block p-2 rounded" style="background:#1e293b;color:#86efac;font-size:.9rem;"></code>
                </div>
                <div>
                    <div class="text-muted small fw-semibold mb-1">Or with python3:</div>
                    <code id="run-cmd3" class="d-block p-2 rounded" style="background:#1e293b;color:#86efac;font-size:.9rem;"></code>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showRunInstructions(filename) {
    document.getElementById('run-filename').textContent = filename;
    document.getElementById('run-cmd').textContent  = 'python '  + filename;
    document.getElementById('run-cmd3').textContent = 'python3 ' + filename;
    new bootstrap.Modal(document.getElementById('run-instructions-modal')).show();
}
</script>
@endpush
</div>
