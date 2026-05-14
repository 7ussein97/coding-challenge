@extends('layouts.app')
@section('title', 'Manage Questions')
@section('page-title', 'Questions')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">All Questions</h5>
        <p class="text-muted small mb-0">{{ $questions->total() }} question(s) total</p>
    </div>
    <a href="{{ route('admin.questions.create') }}" class="btn btn-warning text-dark">
        <i class="fas fa-plus me-1"></i>Add Question
    </a>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th style="width:60px">Order</th>
                    <th>Title / Description</th>
                    <th style="width:80px">Image</th>
                    <th style="width:100px">Submissions</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questions as $q)
                <tr>
                    <td>
                        <span class="badge bg-secondary fs-6 rounded-circle" style="width:36px;height:36px;line-height:24px;display:inline-flex;align-items:center;justify-content:center;">
                            {{ $q->order }}
                        </span>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $q->display_title }}</div>
                        <div class="text-muted small" style="max-width:400px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ Str::limit($q->description, 80) }}
                        </div>
                    </td>
                    <td>
                        @if($q->image)
                            <img src="{{ $q->image_url }}" alt="img" class="rounded" style="width:48px;height:36px;object-fit:cover;">
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark border">{{ $q->submissions()->count() }}</span>
                    </td>
                    <td class="text-end">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('admin.questions.edit', $q) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.questions.destroy', $q) }}" method="POST"
                                  onsubmit="return confirm('Delete this question? All related submissions will be deleted.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-5">
                    <i class="fas fa-question-circle fa-2x mb-2 d-block opacity-25"></i>No questions yet. Add one!
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($questions->hasPages())
    <div class="px-4 py-3 border-top">{{ $questions->links() }}</div>
    @endif
</div>
@endsection
