@extends('layouts.app')

@section('title', 'Register student')

@section('content')
    <div class="container-fluid p-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 fw-bold mb-1">Register student</h1>
                <p class="text-secondary mb-0">Add a student and their initial course enrollment.</p>
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
                            <label for="firstname" class="form-label">First name</label>
                            <input id="firstname" name="firstname" value="{{ old('firstname') }}" required
                                   class="form-control @error('firstname') is-invalid @enderror">
                            @error('firstname') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="secondname" class="form-label">Second name</label>
                            <input id="secondname" name="secondname" value="{{ old('secondname') }}" required
                                   class="form-control @error('secondname') is-invalid @enderror">
                            @error('secondname') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email address</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                                   class="form-control @error('email') is-invalid @enderror">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="phoneno" class="form-label">Phone number</label>
                            <input id="phoneno" name="phoneno" value="{{ old('phoneno') }}" required
                                   class="form-control @error('phoneno') is-invalid @enderror">
                            @error('phoneno') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="course" class="form-label">Course</label>
                            <input id="course" name="course" value="{{ old('course') }}" required
                                   class="form-control @error('course') is-invalid @enderror">
                            @error('course') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="semester" class="form-label">Semester</label>
                            <input id="semester" name="semester" value="{{ old('semester') }}" required
                                   class="form-control @error('semester') is-invalid @enderror">
                            @error('semester') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="academic_year" class="form-label">Academic year</label>
                            <input id="academic_year" name="academic_year" value="{{ old('academic_year') }}"
                                   placeholder="e.g. 2026/2027" required
                                   class="form-control @error('academic_year') is-invalid @enderror">
                            @error('academic_year') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
