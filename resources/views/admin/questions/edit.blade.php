@extends('layouts.app')
@section('title', 'Edit Question')
@section('page-title', 'Edit Question')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card stat-card">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h6 class="fw-bold mb-0"><i class="fas fa-edit me-2 text-primary"></i>Edit Question #{{ $question->order }}</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.questions.update', $question) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Title <span class="text-muted fw-normal">(optional)</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $question->title) }}" placeholder="e.g. Two Sum">
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Display Order</label>
                            <input type="number" name="order" class="form-control @error('order') is-invalid @enderror"
                                   value="{{ old('order', $question->order) }}" min="1" required>
                            @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description / Problem Statement</label>
                            <textarea name="description" rows="10"
                                      class="form-control font-monospace @error('description') is-invalid @enderror"
                                      required>{{ old('description', $question->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Question Image</label>
                            @if($question->image)
                                <div class="mb-2">
                                    <img src="{{ $question->image_url }}" class="rounded border" style="max-height:150px;">
                                    <div class="text-muted small mt-1">Current image. Upload a new one to replace it.</div>
                                </div>
                            @endif
                            <input type="file" name="image" accept="image/*"
                                   class="form-control @error('image') is-invalid @enderror"
                                   onchange="previewImage(this)">
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div id="img-preview" class="mt-2"></div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Save Changes
                        </button>
                        <a href="{{ route('admin.questions.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    const preview = document.getElementById('img-preview');
    preview.innerHTML = '';
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.innerHTML = `<img src="${e.target.result}" class="rounded border" style="max-height:200px;max-width:100%;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
