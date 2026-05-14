@extends('layouts.app')
@section('title', 'Manage Judges')
@section('page-title', 'Judges')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">All Judges</h5>
        <p class="text-muted small mb-0">{{ $judges->total() }} judge(s) registered</p>
    </div>
    <a href="{{ route('admin.judges.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Add Judge
    </a>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Created</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($judges as $judge)
                <tr>
                    <td class="text-muted small">{{ $judge->id }}</td>
                    <td class="fw-semibold">{{ $judge->name }}</td>
                    <td class="text-muted small">{{ $judge->email }}</td>
                    <td class="text-muted small">{{ $judge->created_at->format('M d, Y') }}</td>
                    <td class="text-end">
                        <form action="{{ route('admin.judges.destroy', $judge) }}" method="POST"
                              onsubmit="return confirm('Remove judge {{ addslashes($judge->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-5">
                    <i class="fas fa-user-tie fa-2x mb-2 d-block opacity-25"></i>No judges yet.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($judges->hasPages())
    <div class="px-4 py-3 border-top">{{ $judges->links() }}</div>
    @endif
</div>
@endsection
