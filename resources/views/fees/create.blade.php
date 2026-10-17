@extends('layouts.app')

@section('title', 'Add fee')

@section('content')
    <div class="container-fluid p-0">
        <h1 class="h2 fw-bold mb-3">Add fee</h1>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                @if ($students->isEmpty())
                    <div class="alert alert-info mb-0" role="status">
                        Add a student before recording a fee.
                        <a href="{{ route('students.create') }}" class="alert-link">Register a student</a>
                    </div>
                @else
                    <form method="POST" action="{{ route('fees.store') }}">
                        @csrf
                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">Please correct the errors below.</div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="student_id" class="form-label">Student</label>
                                <select id="student_id" name="student_id" required
                                        class="form-select @error('student_id') is-invalid @enderror">
                                    <option value="">Choose a student</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}" @selected(old('student_id') == $student->id)>
                                            {{ $student->first_name }} {{ $student->second_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="fee_type" class="form-label">Fee type</label>
                                <input id="fee_type" name="fee_type" value="{{ old('fee_type') }}" required
                                       maxlength="255"
                                       class="form-control @error('fee_type') is-invalid @enderror">
                                @error('fee_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="amount" class="form-label">Amount</label>
                                <input id="amount" name="amount" type="number" min="0.01" step="0.01"
                                       value="{{ old('amount') }}" required
                                       class="form-control @error('amount') is-invalid @enderror">
                                @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" name="status" required
                                        class="form-select @error('status') is-invalid @enderror">
                                    @foreach (['Pending', 'Paid', 'Partial'] as $status)
                                        <option value="{{ $status }}" @selected(old('status', 'Pending') === $status)>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Save fee</button>
                            <a href="{{ route('fees.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection