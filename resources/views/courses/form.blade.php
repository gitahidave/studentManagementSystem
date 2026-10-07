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
            <label for="course_code" class="form-label">Course code</label>
            <input id="course_code" name="course_code" value="{{ old('course_code', $course?->course_code) }}"
                   required class="form-control @error('course_code') is-invalid @enderror">
            @error('course_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
            <label for="course_name" class="form-label">Course name</label>
            <input id="course_name" name="course_name" value="{{ old('course_name', $course?->course_name) }}"
                   required class="form-control @error('course_name') is-invalid @enderror">
            @error('course_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
            <label for="duration" class="form-label">Duration</label>
            <input id="duration" name="duration" value="{{ old('duration', $course?->duration) }}"
                   placeholder="e.g. 2 years" class="form-control @error('duration') is-invalid @enderror">
            @error('duration') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" required class="form-select @error('status') is-invalid @enderror">
                <option value="active" @selected(old('status', $course?->status ?? 'active') === 'active')>Active</option>
                <option value="inactive" @selected(old('status', $course?->status ?? 'active') === 'inactive')>Inactive</option>
            </select>
            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
        <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
