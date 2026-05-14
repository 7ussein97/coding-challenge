@extends('layouts.app')
@section('title', 'Manage Teams')
@section('page-title', 'Teams')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">All Teams</h5>
        <p class="text-muted small mb-0">{{ $teams->total() }} team(s) registered</p>
    </div>
    <a href="{{ route('admin.teams.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Add Team
    </a>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Team Name</th>
                    <th>Login Email</th>
                    <th>Created</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teams as $team)
                <tr>
                    <td class="text-muted small">{{ $team->id }}</td>
                    <td>
                        <div class="fw-semibold">{{ $team->name }}</div>
                    </td>
                    <td class="text-muted small">{{ $team->user->email }}</td>
                    <td class="text-muted small">{{ $team->created_at->format('M d, Y') }}</td>
                    <td class="text-end">
                        <form action="{{ route('admin.teams.destroy', $team) }}" method="POST"
                              onsubmit="return confirm('Delete team {{ addslashes($team->name) }}? This will also delete their submissions.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-5">
                    <i class="fas fa-users fa-2x mb-2 d-block opacity-25"></i>No teams yet.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($teams->hasPages())
    <div class="px-4 py-3 border-top">{{ $teams->links() }}</div>
    @endif
</div>
@endsection
