@extends('layouts.app')

@section('title', 'Register student')

@section('content')
    <div class="container-fluid p-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 fw-bold mb-1">Register student</h1>
                <p class="text-secondary mb-0">Add a student and select their course.</p>
            </div>
            <a class="btn btn-outline-secondary" href="{{ route('students.index') }}">Back to students</a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        Please correct the errors below.
                    </div>
                @endif

                <form method="POST" action="{{ route('students.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First name</label>
                            <input id="first_name" name="first_name" value="{{ old('first_name') }}" required
                                   class="form-control @error('first_name') is-invalid @enderror">
                            @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="second_name" class="form-label">Second name</label>
                            <input id="second_name" name="second_name" value="{{ old('second_name') }}" required
                                   class="form-control @error('second_name') is-invalid @enderror">
                            @error('second_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="course_id" class="form-label">Course</label>
                            <select id="course_id" name="course_id" required
                                    class="form-select @error('course_id') is-invalid @enderror">
                                <option value="">Select a course</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>
                                        {{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Register student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
