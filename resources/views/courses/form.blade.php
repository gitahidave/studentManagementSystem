<form method="POST"
      action="{{ $course ? route('courses.update', $course) : route('courses.store') }}">
    @csrf
    @if ($course)
        @method('PUT')
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">Please correct the errors below.</div>
    @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label for="name" class="form-label">Course name</label>
            <input id="name" name="name" value="{{ old('name', $course?->name) }}"
                   required class="form-control @error('name') is-invalid @enderror">
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
        <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
