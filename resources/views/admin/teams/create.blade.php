@extends('layouts.app')
@section('title', 'Create Team')
@section('page-title', 'Create Team')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card stat-card">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h6 class="fw-bold mb-0"><i class="fas fa-users me-2 text-primary"></i>New Team Account</h6>
                <p class="text-muted small mb-0 mt-1">Creates a shared login account for the team.</p>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.teams.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Team Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="e.g. Team Alpha" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Login Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" placeholder="team@example.com" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                               placeholder="Min 6 characters" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check me-1"></i>Create Team
                        </button>
                        <a href="{{ route('admin.teams.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
