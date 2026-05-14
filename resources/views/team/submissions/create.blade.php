@extends('layouts.app')
@section('title', 'Submit Solution')
@section('page-title', 'Submit Solution')

@section('content')
<div class="row g-4">
    {{-- Left: Question + Submission Form --}}
    <div class="col-lg-8">
        {{-- Question Panel --}}
        <div class="card stat-card mb-4">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                     style="width:36px;height:36px;background:#3b82f6;flex-shrink:0;">
                    {{ $question->order }}
                </div>
                <h6 class="fw-bold mb-0">{{ $question->display_title }}</h6>
            </div>
            <div class="card-body p-4">
                @if($question->image)
                    <img src="{{ $question->image_url }}" class="img-fluid rounded mb-3" style="max-height:300px;width:100%;object-fit:contain;">
                @endif
                <pre style="white-space:pre-wrap;font-size:.9rem;font-family:inherit;">{{ $question->description }}</pre>
            </div>
        </div>

        {{-- Submission Form --}}
        <div class="card stat-card">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h6 class="fw-bold mb-0"><i class="fas fa-code me-2 text-primary"></i>Your Solution</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('team.questions.submit.store', $question) }}" method="POST" id="submit-form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Source Code</label>
                        <textarea name="code" id="code-editor" rows="18"
                                  class="form-control font-monospace @error('code') is-invalid @enderror"
                                  placeholder="Paste or type your solution here..."
                                  style="font-size:.88rem;line-height:1.6;resize:vertical;"
                                  required>{{ old('code') }}</textarea>
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary fw-semibold">
                            <i class="fas fa-paper-plane me-1"></i>Submit Solution
                        </button>
                        <a href="{{ route('team.questions.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Right: Previous Submissions --}}
    <div class="col-lg-4">
        <div class="card stat-card">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h6 class="fw-bold mb-0"><i class="fas fa-history me-2 text-muted"></i>Your Attempts</h6>
            </div>
            @if($previousSubmissions->count() > 0)
            <div class="list-group list-group-flush">
                @foreach($previousSubmissions as $sub)
                <div class="list-group-item px-4 py-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="status-badge badge-{{ $sub->status }}">{{ ucfirst($sub->status) }}</span>
                        <span class="text-muted small">{{ $sub->created_at->format('M d H:i') }}</span>
                    </div>
                    @if($sub->judge_comment)
                        <div class="mt-1 p-2 rounded bg-light small text-muted">
                            <i class="fas fa-comment me-1"></i>{{ $sub->judge_comment }}
                        </div>
                    @endif
                    <button class="btn btn-link btn-sm p-0 mt-1 text-muted"
                            onclick="loadCode(this)"
                            data-code="{{ $sub->code }}">
                        <i class="fas fa-undo me-1"></i>Load this code
                    </button>
                </div>
                @endforeach
            </div>
            @else
            <div class="card-body text-center text-muted py-4">
                <i class="fas fa-paper-plane fa-2x mb-2 d-block opacity-25"></i>
                No previous attempts.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function loadCode(btn) {
    if (confirm('Load this previous submission into the editor?')) {
        document.getElementById('code-editor').value = btn.dataset.code;
        document.getElementById('code-editor').focus();
    }
}
</script>
@endpush
